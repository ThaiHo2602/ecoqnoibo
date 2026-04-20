<?php

namespace App\Services;

use RuntimeException;

class Mailer
{
    public static function isEnabled(): bool
    {
        return (bool) config('mail.enabled', false);
    }

    public static function isConfigured(): bool
    {
        if (! self::isEnabled()) {
            return false;
        }

        $required = [
            'mail.host',
            'mail.port',
            'mail.username',
            'mail.password',
            'mail.from_email',
        ];

        foreach ($required as $key) {
            $value = config($key);
            if ($value === null || $value === '') {
                return false;
            }
        }

        return true;
    }

    public static function sendAssignmentNotification(array $recipient, array $lead, array $assigner): array
    {
        $recipientEmail = trim((string) ($recipient['email'] ?? ''));

        if ($recipientEmail === '') {
            self::log('Bo qua gui mail: nhan vien chua co email. User ID: ' . ($recipient['id'] ?? 'unknown'));

            return [
                'sent' => false,
                'reason' => 'missing_recipient_email',
                'message' => 'Nhân viên chưa có email.',
            ];
        }

        if (! filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            self::log('Bo qua gui mail: email nhan vien khong hop le - ' . $recipientEmail);

            return [
                'sent' => false,
                'reason' => 'invalid_recipient_email',
                'message' => 'Email nhân viên không hợp lệ.',
            ];
        }

        if (! self::isConfigured()) {
            self::log('Bo qua gui mail: cau hinh SMTP chua day du hoac chua bat.');

            return [
                'sent' => false,
                'reason' => 'mail_not_configured',
                'message' => 'SMTP chưa được cấu hình hoàn chỉnh.',
            ];
        }

        $subject = 'Bạn vừa được phân một khách hàng mới';
        $appointmentAt = trim((string) ($lead['appointment_at'] ?? ''));
        $appointmentLabel = $appointmentAt !== '' ? date('d/m/Y H:i', strtotime($appointmentAt)) : 'Chưa cập nhật';
        $note = trim((string) ($lead['note'] ?? ''));

        $html = self::buildAssignmentHtml($recipient, $lead, $assigner, $appointmentLabel, $note);
        $text = self::buildAssignmentText($recipient, $lead, $assigner, $appointmentLabel, $note);

        self::send([
            'to_email' => $recipientEmail,
            'to_name' => (string) ($recipient['full_name'] ?? ''),
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        ]);

        self::log('Gui mail phan khach thanh cong toi ' . $recipientEmail . ' cho lead #' . ($lead['id'] ?? 'unknown'));

        return [
            'sent' => true,
            'reason' => 'sent',
            'message' => 'Đã gửi email thông báo cho nhân viên.',
        ];
    }

    public static function send(array $payload): void
    {
        $host = (string) config('mail.host');
        $port = (int) config('mail.port');
        $username = (string) config('mail.username');
        $password = (string) config('mail.password');
        $fromEmail = (string) config('mail.from_email');
        $fromName = (string) config('mail.from_name', 'Eco-Q House');
        $encryption = strtolower((string) config('mail.encryption', 'tls'));
        $timeout = (int) config('mail.timeout', 15);

        $transportHost = $encryption === 'ssl' ? 'ssl://' . $host : $host;
        $socket = @stream_socket_client(
            $transportHost . ':' . $port,
            $errorNumber,
            $errorMessage,
            $timeout,
            STREAM_CLIENT_CONNECT
        );

        if (! $socket) {
            throw new RuntimeException('Khong the ket noi SMTP: ' . $errorMessage . ' (' . $errorNumber . ')');
        }

        stream_set_timeout($socket, $timeout);

        try {
            self::expect($socket, [220]);
            self::command($socket, 'EHLO localhost', [250]);

            if ($encryption === 'tls') {
                self::command($socket, 'STARTTLS', [220]);

                $cryptoEnabled = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                if ($cryptoEnabled !== true) {
                    throw new RuntimeException('Khong the bat ma hoa TLS cho ket noi SMTP.');
                }

                self::command($socket, 'EHLO localhost', [250]);
            }

            self::command($socket, 'AUTH LOGIN', [334]);
            self::command($socket, base64_encode($username), [334]);
            self::command($socket, base64_encode($password), [235]);

            self::command($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
            self::command($socket, 'RCPT TO:<' . (string) $payload['to_email'] . '>', [250, 251]);
            self::command($socket, 'DATA', [354]);

            $message = self::buildMimeMessage([
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'to_email' => (string) $payload['to_email'],
                'to_name' => (string) ($payload['to_name'] ?? ''),
                'subject' => (string) $payload['subject'],
                'html' => (string) ($payload['html'] ?? ''),
                'text' => (string) ($payload['text'] ?? ''),
            ]);

            fwrite($socket, $message . "\r\n.\r\n");
            self::expect($socket, [250]);
            self::command($socket, 'QUIT', [221]);
        } finally {
            fclose($socket);
        }
    }

    private static function buildMimeMessage(array $payload): string
    {
        $boundary = 'ecoq_' . bin2hex(random_bytes(12));
        $headers = [
            'MIME-Version: 1.0',
            'Date: ' . date(DATE_RFC2822),
            'From: ' . self::formatAddress($payload['from_email'], $payload['from_name']),
            'To: ' . self::formatAddress($payload['to_email'], $payload['to_name']),
            'Subject: ' . self::encodeHeader($payload['subject']),
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];

        $textBody = quoted_printable_encode(str_replace("\n", "\r\n", $payload['text']));
        $htmlBody = quoted_printable_encode(str_replace("\n", "\r\n", $payload['html']));

        $parts = [
            '--' . $boundary,
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: quoted-printable',
            '',
            $textBody,
            '--' . $boundary,
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: quoted-printable',
            '',
            $htmlBody,
            '--' . $boundary . '--',
        ];

        return implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $parts);
    }

    private static function command($socket, string $command, array $expectedCodes): void
    {
        fwrite($socket, $command . "\r\n");
        self::expect($socket, $expectedCodes);
    }

    private static function expect($socket, array $expectedCodes): void
    {
        $response = '';

        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;

            if (strlen($line) < 4 || $line[3] !== '-') {
                break;
            }
        }

        $code = (int) substr($response, 0, 3);
        if (! in_array($code, $expectedCodes, true)) {
            throw new RuntimeException('SMTP response khong hop le: ' . trim($response));
        }
    }

    private static function formatAddress(string $email, string $name = ''): string
    {
        $email = trim($email);
        $name = trim($name);

        if ($name === '') {
            return '<' . $email . '>';
        }

        return self::encodeHeader($name) . ' <' . $email . '>';
    }

    private static function encodeHeader(string $value): string
    {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private static function buildAssignmentHtml(array $recipient, array $lead, array $assigner, string $appointmentLabel, string $note): string
    {
        $customerName = htmlspecialchars((string) ($lead['customer_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars((string) ($lead['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
        $recipientName = htmlspecialchars((string) ($recipient['full_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $assignerName = htmlspecialchars((string) ($assigner['full_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $safeNote = $note !== '' ? nl2br(htmlspecialchars($note, ENT_QUOTES, 'UTF-8')) : 'Không có ghi chú thêm.';
        $loginUrl = htmlspecialchars((string) url('/customers'), ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo phân khách hàng</title>
</head>
<body style="margin:0;padding:24px;background:#f5f8ff;font-family:Arial,sans-serif;color:#14284a;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:20px;padding:32px;border:1px solid #e7eefc;">
        <div style="font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#b8861f;margin-bottom:12px;">Eco-Q House</div>
        <h1 style="margin:0 0 16px;font-size:28px;line-height:1.3;color:#102b5a;">Bạn vừa được phân một khách hàng mới</h1>
        <p style="margin:0 0 20px;line-height:1.7;">Xin chào {$recipientName}, giám đốc vừa phân cho bạn một khách hàng mới để theo dõi trong hệ thống.</p>

        <div style="background:#f7faff;border:1px solid #e3ebfb;border-radius:16px;padding:18px 20px;margin-bottom:20px;">
            <p style="margin:0 0 10px;"><strong>Khách hàng:</strong> {$customerName}</p>
            <p style="margin:0 0 10px;"><strong>Số điện thoại:</strong> {$phone}</p>
            <p style="margin:0 0 10px;"><strong>Lịch hẹn:</strong> {$appointmentLabel}</p>
            <p style="margin:0 0 10px;"><strong>Người phân:</strong> {$assignerName}</p>
            <p style="margin:0;"><strong>Ghi chú:</strong><br>{$safeNote}</p>
        </div>

        <a href="{$loginUrl}" style="display:inline-block;padding:12px 20px;border-radius:999px;background:#173a7a;color:#ffffff;text-decoration:none;font-weight:700;">Mở danh sách khách hàng</a>

        <p style="margin:24px 0 0;color:#6d7a92;line-height:1.7;">Nếu bạn chưa thấy dữ liệu trong hệ thống, hãy đăng nhập lại và kiểm tra mục Khách hàng.</p>
    </div>
</body>
</html>
HTML;
    }

    private static function buildAssignmentText(array $recipient, array $lead, array $assigner, string $appointmentLabel, string $note): string
    {
        return implode("\n", [
            'Xin chao ' . ($recipient['full_name'] ?? 'ban') . ',',
            '',
            'Ban vua duoc phan mot khach hang moi trong he thong Eco-Q House.',
            'Khach hang: ' . ($lead['customer_name'] ?? ''),
            'So dien thoai: ' . ($lead['phone'] ?? ''),
            'Lich hen: ' . $appointmentLabel,
            'Nguoi phan: ' . ($assigner['full_name'] ?? ''),
            'Ghi chu: ' . ($note !== '' ? $note : 'Khong co ghi chu them.'),
            '',
            'Xem chi tiet tai: ' . url('/customers'),
        ]);
    }

    private static function log(string $message): void
    {
        $file = (string) config('mail.log_file', base_path('storage/logs/mail.log'));
        $directory = dirname($file);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($file, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
    }
}

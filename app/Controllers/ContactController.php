<?php

namespace App\Controllers;

use App\Core\Session;
use App\Services\Mailer;
use Throwable;

class ContactController
{
    public function send(): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $topic = trim((string) ($_POST['topic'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));

        if ($name === '' || $phone === '' || $topic === '' || $message === '') {
            Session::flash('contact_error', 'Vui lòng nhập đầy đủ họ tên, số điện thoại, nhu cầu và nội dung.');
            redirect('/lien-he');
        }

        if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('contact_error', 'Email không hợp lệ. Vui lòng kiểm tra lại.');
            redirect('/lien-he');
        }

        try {
            $result = Mailer::sendLandingContact([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'topic' => $topic,
                'message' => $message,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'submitted_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $exception) {
            $result = [
                'sent' => false,
                'message' => 'Chưa thể gửi email do SMTP đang lỗi. Vui lòng kiểm tra cấu hình email.',
            ];
        }

        if (! $result['sent']) {
            Session::flash('contact_error', $result['message']);
            redirect('/lien-he');
        }

        Session::flash('contact_success', 'Cảm ơn bạn đã gửi thông tin. Eco-Q House sẽ liên hệ lại sớm nhất.');
        redirect('/lien-he');
    }
}

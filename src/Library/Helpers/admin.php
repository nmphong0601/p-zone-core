<?php

/**
 * Send email reset password
 */
if (!function_exists('pz_admin_sendmail_reset_notification') && !in_array('pz_admin_sendmail_reset_notification', config('helper_except', []))) {
    function pz_admin_sendmail_reset_notification(string $token, string $emailReset)
    {
        $checkContent = (new \PZone\Core\Front\Models\ShopEmailTemplate)->where('group', 'forgot_password')->where('status', 1)->first();
        if ($checkContent) {
            $content = $checkContent->text;
            $dataFind = [
                '/\{\{\$title\}\}/',
                '/\{\{\$reason_sendmail\}\}/',
                '/\{\{\$note_sendmail\}\}/',
                '/\{\{\$note_access_link\}\}/',
                '/\{\{\$reset_link\}\}/',
                '/\{\{\$reset_button\}\}/',
            ];
            $url = pz_route('admin.password_reset', ['token' => $token]);
            $dataReplace = [
                pz_language_render('email.forgot_password.title'),
                pz_language_render('email.forgot_password.reason_sendmail'),
                pz_language_render('email.forgot_password.note_sendmail', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]),
                pz_language_render('email.forgot_password.note_access_link', ['reset_button' => pz_language_render('email.forgot_password.reset_button'), 'url' => $url]),
                $url,
                pz_language_render('email.forgot_password.reset_button'),
            ];
            $content = preg_replace($dataFind, $dataReplace, $content);
            $dataView = [
                'content' => $content,
            ];

            $config = [
                'to' => $emailReset,
                'subject' => pz_language_render('email.forgot_password.reset_button'),
            ];

            pz_send_mail('templates.' . pz_store('template') . '.mail.forgot_password', $dataView, $config, $dataAtt = []);
        }
    }
}

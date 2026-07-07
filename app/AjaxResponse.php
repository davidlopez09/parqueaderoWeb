<?php

if (!function_exists('ajax_respond')) {
    function ajax_respond(bool $success, string $message, string $redirectUrl = ''): void
    {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                ['S_1' => $success ? '1' : '0', 'S_2' => $message]
            ]);
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($success) {
            $_SESSION['sweet_alert'] = [
                'title'   => 'Éxito',
                'message' => $message,
                'icon'    => 'success',
            ];
        } else {
            $_SESSION['sweet_alert'] = [
                'title'   => 'Error',
                'message' => $message,
                'icon'    => 'error',
            ];
        }

        if ($redirectUrl) {
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
}

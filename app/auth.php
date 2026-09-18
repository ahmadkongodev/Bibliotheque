<?php
require_once __DIR__ . '/helpers.php';

function require_login(): void
{
    startSession();

    if (empty($_SESSION['user_id'])) {
        setFlash('error', 'Connectez-vous pour accéder à cette page.');
        redirect('login.php');
    }
}

function csrf_token(): string
{
    startSession();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_check(?string $token): bool
{
    startSession();

    return !empty($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function is_logged_in(): bool
{
    startSession();
    return !empty($_SESSION['user_id']);
}
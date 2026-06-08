<?php

/** Dedicated administrator credentials (not stored in customer table). */
const AUTH_ADMIN_EMAIL = 'eatryadmin@gmail.com';
const AUTH_ADMIN_PASSWORD = 'eatryadmin123';

function auth_is_admin_credentials(string $email, string $password): bool
{
    return strcasecmp(trim($email), AUTH_ADMIN_EMAIL) === 0
        && hash_equals(AUTH_ADMIN_PASSWORD, $password);
}

function auth_is_admin_email(string $email): bool
{
    return strcasecmp(trim($email), AUTH_ADMIN_EMAIL) === 0;
}

function auth_is_admin(): bool
{
    return !empty($_SESSION['login'])
        && $_SESSION['login'] === true
        && (($_SESSION['role'] ?? '') === 'admin');
}

function auth_is_customer(): bool
{
    return !empty($_SESSION['login'])
        && $_SESSION['login'] === true
        && (($_SESSION['role'] ?? '') === 'customer');
}

function auth_set_admin_session(): void
{
    $_SESSION['id'] = 0;
    $_SESSION['username'] = 'admin';
    $_SESSION['name'] = 'Administrator';
    $_SESSION['email'] = AUTH_ADMIN_EMAIL;
    $_SESSION['role'] = 'admin';
    $_SESSION['login'] = true;
    unset($_SESSION['phone']);
}

function auth_set_customer_session(array $row): void
{
    $_SESSION['id'] = (int) $row['cust_id'];
    $_SESSION['username'] = (string) $row['cust_username'];
    $_SESSION['name'] = (string) ($row['cust_name'] ?? '');
    $_SESSION['phone'] = $row['cust_mobile'] ?? null;
    $_SESSION['email'] = (string) $row['cust_email'];
    $_SESSION['role'] = 'customer';
    $_SESSION['login'] = true;
}

function auth_login_redirect_url(string $error = ''): string
{
    $query = $error !== '' ? '?error=' . urlencode($error) : '';
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));

    if (strpos($script, '/assets/php/') !== false) {
        return '../../login.php' . $query;
    }

    return 'login.php' . $query;
}

function auth_frontend_redirect_url(string $query = ''): string
{
    $suffix = $query !== '' ? '?' . ltrim($query, '?') : '';
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));

    if (strpos($script, '/assets/php/') !== false) {
        return '../../index.php' . $suffix;
    }

    return 'index.php' . $suffix;
}

function admin_require_auth(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (auth_is_admin()) {
        return;
    }

    if (auth_is_customer()) {
        $_SESSION['access_denied_notice'] = 'You do not have permission to access the Admin Panel.';
        header('Location: ' . auth_frontend_redirect_url('access_denied=1'));
        exit;
    }

    header('Location: ' . auth_login_redirect_url('denied'));
    exit;
}

function auth_clear_session(): void
{
    unset(
        $_SESSION['login'],
        $_SESSION['role'],
        $_SESSION['id'],
        $_SESSION['username'],
        $_SESSION['name'],
        $_SESSION['email'],
        $_SESSION['phone'],
        $_SESSION['table'],
        $_SESSION['guest'],
        $_SESSION['booking_date'],
        $_SESSION['booking_time'],
        $_SESSION['booking_status']
    );
}

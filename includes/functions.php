<?php

define('USERS_FILE', __DIR__ . '/../data/users.json');

function read_users(): array
{
    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, json_encode([]));
    }

    $json = file_get_contents(USERS_FILE);
    $data = json_decode($json, true);

    return is_array($data) ? $data : [];
}

function save_users(array $users): bool
{
    $fp = fopen(USERS_FILE, 'c+');
    if (!$fp) {
        return false;
    }

    $sukses = false;
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fflush($fp);
        flock($fp, LOCK_UN);
        $sukses = true;
    }
    fclose($fp);

    return $sukses;
}

function sanitize_text(string $input): string
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitize_email(string $input): ?string
{
    $email = filter_var(trim($input), FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
}

function find_user_by_email(string $email, array $users): ?array
{
    foreach ($users as $index => $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            return ['index' => $index, 'data' => $user];
        }
    }
    return null;
}

function find_user_by_id(string $id, array $users): ?array
{
    foreach ($users as $index => $user) {
        if ($user['id'] === $id) {
            return ['index' => $index, 'data' => $user];
        }
    }
    return null;
}

function generate_id(): string
{
    return bin2hex(random_bytes(8));
}

function generate_token(): string
{
    return bin2hex(random_bytes(32));
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']); 
        return $flash;
    }
    return null;
}

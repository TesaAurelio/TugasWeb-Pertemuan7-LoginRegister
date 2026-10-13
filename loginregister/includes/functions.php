<?php
/**
 * includes/functions.php
 * Tugas Rutin 7 — Sistem Login/Register dengan PHP Native
 *
 * Kumpulan fungsi inti: baca/tulis file JSON, sanitasi & validasi
 * input, pencarian user, dan dukungan fitur "Remember Me".
 */

define('USERS_FILE', __DIR__ . '/../data/users.json');

/**
 * Membaca seluruh data user dari file JSON.
 * Kalau file belum ada, dibuat otomatis berisi array kosong.
 */
function read_users(): array
{
    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, json_encode([]));
    }

    $json = file_get_contents(USERS_FILE);
    $data = json_decode($json, true);

    // Kalau isi file rusak/tidak valid, kembalikan array kosong
    // supaya program tidak crash (defensive programming).
    return is_array($data) ? $data : [];
}

/**
 * Menulis ulang seluruh data user ke file JSON.
 * Memakai flock() (file lock) supaya aman kalau ada dua proses
 * yang menulis ke file bersamaan (praktik baik, bukan sekadar teori).
 */
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

/**
 * Sanitasi input teks biasa (nama, dll).
 * trim -> hilangkan spasi di awal/akhir
 * strip_tags -> hilangkan tag HTML/JS yang mungkin disisipkan
 * htmlspecialchars -> ubah karakter spesial jadi aman ditampilkan ulang
 */
function sanitize_text(string $input): string
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitasi & validasi khusus email.
 * Mengembalikan email yang sudah bersih, atau null kalau formatnya tidak valid.
 */
function sanitize_email(string $input): ?string
{
    $email = filter_var(trim($input), FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
}

/**
 * Mencari satu user berdasarkan email. Mengembalikan array user
 * (beserta index-nya) atau null kalau tidak ditemukan.
 */
function find_user_by_email(string $email, array $users): ?array
{
    foreach ($users as $index => $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            return ['index' => $index, 'data' => $user];
        }
    }
    return null;
}

/**
 * Mencari satu user berdasarkan ID.
 */
function find_user_by_id(string $id, array $users): ?array
{
    foreach ($users as $index => $user) {
        if ($user['id'] === $id) {
            return ['index' => $index, 'data' => $user];
        }
    }
    return null;
}

/**
 * Membuat ID unik sederhana untuk user baru.
 */
function generate_id(): string
{
    return bin2hex(random_bytes(8));
}

/**
 * Membuat token acak untuk fitur Remember Me.
 */
function generate_token(): string
{
    return bin2hex(random_bytes(32));
}

/**
 * Helper flash message: menyimpan pesan sementara di session,
 * untuk ditampilkan sekali saja di halaman berikutnya lalu dihapus.
 */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']); // tampil sekali saja
        return $flash;
    }
    return null;
}

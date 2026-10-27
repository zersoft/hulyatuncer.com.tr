<?php
/**
 * Hülya Tuncer – Güvenli Yönetici Kimlik Doğrulama Modülü
 * Sıfır üçüncü taraf bağımlılık, yerel oturum güvenliği.
 */

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

define('ADMIN_CONFIG_FILE', __DIR__ . '/../data/admin_config.json');

/**
 * Yönetici oturumunun aktif olup olmadığını denetler
 */
function isAdminLoggedIn(): bool {
    return isset($_SESSION['ht_admin_logged']) && $_SESSION['ht_admin_logged'] === true;
}

/**
 * Giriş yapılmamışsa giriş sayfasına yönlendirir veya API için 401 döner
 */
function requireAdminLogin(bool $isApi = false): void {
    if (!isAdminLoggedIn()) {
        if ($isApi) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Yetkisiz erişim. Lütfen giriş yapın.']);
            exit;
        } else {
            header('Location: /admin/');
            exit;
        }
    }
}

/**
 * CSRF belirteci üretir veya mevcut olanı döner
 */
function getCsrfToken(): string {
    if (empty($_SESSION['ht_csrf_token'])) {
        $_SESSION['ht_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['ht_csrf_token'];
}

/**
 * CSRF belirtecini doğrular
 */
function verifyCsrfToken(?string $token): bool {
    if (empty($token) || empty($_SESSION['ht_csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['ht_csrf_token'], $token);
}

/**
 * Giriş denemesi
 */
function attemptAdminLogin(string $username, string $password): array {
    if (!file_exists(ADMIN_CONFIG_FILE)) {
        return ['success' => false, 'error' => 'Yapılandırma dosyası bulunamadı.'];
    }

    $configJson = file_get_contents(ADMIN_CONFIG_FILE);
    $config = json_decode($configJson, true);
    if (!$config || empty($config['passwordHash'])) {
        return ['success' => false, 'error' => 'Yönetici hesabı yapılandırılmamış.'];
    }

    $validUsernames = ['admin', 'hulya.tuncer', strtolower($config['username'] ?? '')];
    if (!in_array(strtolower(trim($username)), $validUsernames, true)) {
        return ['success' => false, 'error' => 'Geçersiz kullanıcı adı veya şifre.'];
    }

    if (!password_verify($password, $config['passwordHash'])) {
        return ['success' => false, 'error' => 'Geçersiz kullanıcı adı veya şifre.'];
    }

    // Oturum başlat
    session_regenerate_id(true);
    $_SESSION['ht_admin_logged'] = true;
    $_SESSION['ht_admin_user'] = $config['username'] ?? 'admin';
    $_SESSION['ht_admin_name'] = $config['displayName'] ?? 'Hülya Tuncer';
    $_SESSION['ht_login_time'] = time();
    $_SESSION['ht_csrf_token'] = bin2hex(random_bytes(32));

    // Son giriş zamanını kaydet
    $config['lastLogin'] = date('c');
    file_put_contents(ADMIN_CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return ['success' => true];
}

/**
 * Çıkış işlemi
 */
function adminLogout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

/**
 * Şifre güncelleme
 */
function updateAdminPassword(string $currentPassword, string $newPassword): array {
    if (!file_exists(ADMIN_CONFIG_FILE)) {
        return ['success' => false, 'error' => 'Yapılandırma dosyası bulunamadı.'];
    }

    if (strlen($newPassword) < 8) {
        return ['success' => false, 'error' => 'Yeni şifre en az 8 karakter olmalıdır.'];
    }

    $config = json_decode(file_get_contents(ADMIN_CONFIG_FILE), true);
    if (!password_verify($currentPassword, $config['passwordHash'])) {
        return ['success' => false, 'error' => 'Mevcut şifreniz hatalı.'];
    }

    $config['passwordHash'] = password_hash($newPassword, PASSWORD_BCRYPT);
    $config['updatedAt'] = date('c');
    file_put_contents(ADMIN_CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return ['success' => true, 'message' => 'Şifreniz başarıyla güncellendi.'];
}

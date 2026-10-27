<?php
/**
 * Hülya Tuncer – Yönetici Paneli API Uç Noktası
 * Tüm işlemler oturum ve yetki kontrolüne tabidir.
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

require_once __DIR__ . '/auth.php';
requireAdminLogin(true);

$vakaFile = __DIR__ . '/../data/vakalar.json';
$talepFile = __DIR__ . '/../data/talepler.json';

function getJsonData(string $file): array {
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function saveJsonData(string $file, array $data): bool {
    $fp = fopen($file, 'c+');
    if (!$fp) return false;
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }
    fclose($fp);
    return false;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'get_vakalar':
        echo json_encode(['success' => true, 'vakalar' => getJsonData($vakaFile)]);
        break;

    case 'save_vaka':
        $title = trim($input['title'] ?? '');
        if (empty($title)) {
            echo json_encode(['success' => false, 'error' => 'Vaka veya mahkeme konusu başlığı zorunludur.']);
            exit;
        }

        $vakalar = getJsonData($vakaFile);
        $id = trim($input['id'] ?? '');
        $isNew = empty($id);

        if ($isNew) {
            $id = 'VK-' . date('Ym') . '-' . sprintf('%03d', count($vakalar) + 1);
            $newVaka = [
                'id' => $id,
                'title' => $title,
                'category' => trim($input['category'] ?? 'Mahkeme & Velayet'),
                'courtType' => trim($input['courtType'] ?? ''),
                'summary' => trim($input['summary'] ?? ''),
                'clinicalObservation' => trim($input['clinicalObservation'] ?? ''),
                'recommendations' => trim($input['recommendations'] ?? ''),
                'confidentiality' => trim($input['confidentiality'] ?? 'Yüksek Gizlilik'),
                'status' => trim($input['status'] ?? 'Devam Ediyor'),
                'createdAt' => date('c'),
                'updatedAt' => date('c')
            ];
            array_unshift($vakalar, $newVaka);
        } else {
            $found = false;
            foreach ($vakalar as &$v) {
                if ($v['id'] === $id) {
                    $v['title'] = $title;
                    $v['category'] = trim($input['category'] ?? $v['category']);
                    $v['courtType'] = trim($input['courtType'] ?? $v['courtType']);
                    $v['summary'] = trim($input['summary'] ?? $v['summary']);
                    $v['clinicalObservation'] = trim($input['clinicalObservation'] ?? $v['clinicalObservation']);
                    $v['recommendations'] = trim($input['recommendations'] ?? $v['recommendations']);
                    $v['confidentiality'] = trim($input['confidentiality'] ?? $v['confidentiality']);
                    $v['status'] = trim($input['status'] ?? $v['status']);
                    $v['updatedAt'] = date('c');
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo json_encode(['success' => false, 'error' => 'Kayıt bulunamadı.']);
                exit;
            }
        }

        if (saveJsonData($vakaFile, $vakalar)) {
            echo json_encode(['success' => true, 'message' => 'Vaka notu başarıyla kaydedildi.', 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Dosya kaydetme hatası.']);
        }
        break;

    case 'delete_vaka':
        $id = trim($input['id'] ?? '');
        $vakalar = getJsonData($vakaFile);
        $vakalar = array_filter($vakalar, fn($v) => $v['id'] !== $id);
        saveJsonData($vakaFile, $vakalar);
        echo json_encode(['success' => true, 'message' => 'Vaka kaydı silindi.']);
        break;

    case 'get_talepler':
        echo json_encode(['success' => true, 'talepler' => getJsonData($talepFile)]);
        break;

    case 'update_talep':
        $refId = trim($input['refId'] ?? '');
        $talepler = getJsonData($talepFile);
        $found = false;
        foreach ($talepler as &$t) {
            if ($t['refId'] === $refId) {
                if (isset($input['status'])) $t['status'] = trim($input['status']);
                if (isset($input['adminNote'])) $t['adminNote'] = trim($input['adminNote']);
                $t['updatedAt'] = date('c');
                $found = true;
                break;
            }
        }
        if ($found && saveJsonData($talepFile, $talepler)) {
            echo json_encode(['success' => true, 'message' => 'Talep durumu güncellendi.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Talep bulunamadı veya kaydedilemedi.']);
        }
        break;

    case 'delete_talep':
        $refId = trim($input['refId'] ?? '');
        $talepler = getJsonData($talepFile);
        $talepler = array_filter($talepler, fn($t) => $t['refId'] !== $refId);
        saveJsonData($talepFile, $talepler);
        echo json_encode(['success' => true, 'message' => 'Talep silindi.']);
        break;

    case 'change_password':
        $current = $input['currentPassword'] ?? '';
        $new = $input['newPassword'] ?? '';
        $res = updateAdminPassword($current, $new);
        echo json_encode($res);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Bilinmeyen işlem.']);
        break;
}

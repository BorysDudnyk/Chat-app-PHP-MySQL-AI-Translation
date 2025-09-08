<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function write_log($message) {
    static $logDirCreated = false;
    $logDir = __DIR__ . '/logs';

    if (!$logDirCreated) {
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logDirCreated = true;
    }

    $logFile = $logDir . '/translate.log';
    $date = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[" . $date . "] " . $message . "\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    $target = isset($_POST['target']) ? $_POST['target'] : 'uk';

    write_log("Translation request: text='$text', target='$target'");

    if ($text === '') {
        write_log("Error: empty text");
        echo 'Текст порожній';
        exit;
    }

    $sourceLang = preg_match('/[А-Яа-яЁёІіЇїЄєҐґ]/u', $text) ? 'uk' : 'en';

    if ($target !== 'uk' && $target !== 'en') {
        $target = 'uk';
    }

    if ($sourceLang === $target) {
        $target = ($sourceLang === 'uk') ? 'en' : 'uk';
    }

    $langPair = $sourceLang . '|' . $target;

    $url = 'https://api.mymemory.translated.net/get?q=' . urlencode($text) . '&langpair=' . $langPair;

    write_log("Request URL: $url");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        write_log("cURL error: $error");
        curl_close($ch);
        echo 'Помилка перекладу (cURL)';
        exit;
    }

    curl_close($ch);

    write_log("Response: $response");

    $data = json_decode($response, true);

    if (isset($data['responseData']['translatedText'])) {
        $translated = $data['responseData']['translatedText'];
        write_log("Translated: $translated");
        echo htmlspecialchars($translated, ENT_QUOTES);
    } else {
        write_log("Translation not found in response");
        echo 'Переклад не знайдено';
    }
} else {
    write_log("Error: method not POST");
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Метод не дозволений";
}
?>

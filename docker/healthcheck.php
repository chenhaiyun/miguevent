<?php
/**
 * Docker 健康检查脚本
 * 用于检查咪咕解析服务是否正常运行
 */

// 设置超时时间
set_time_limit(10);

// 检查基本PHP功能
if (!function_exists('curl_init')) {
    http_response_code(503);
    echo "ERROR: cURL extension not available";
    exit(1);
}

// 检查基本文件是否存在
$required_files = [
    '/var/www/html/migu.php',
    '/var/www/html/miguevent_jpid.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        http_response_code(503);
        echo "ERROR: Required file not found: $file";
        exit(1);
    }
}

// 检查目录权限
$required_dirs = [
    '/var/www/html/logs',
    '/var/www/html/cache'
];

foreach ($required_dirs as $dir) {
    if (!is_dir($dir)) {
        http_response_code(503);
        echo "ERROR: Required directory not found: $dir";
        exit(1);
    }
    if (!is_writable($dir)) {
        http_response_code(503);
        echo "ERROR: Directory not writable: $dir";
        exit(1);
    }
}

// 测试基本HTTP响应（不需要用户凭证的测试）
$test_url = "http://localhost/migu.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $test_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 检查HTTP响应
if ($http_code === false || $http_code == 0) {
    http_response_code(503);
    echo "ERROR: Unable to connect to service";
    exit(1);
}

// 200或者302都表示服务正常（302可能是由于没有用户凭证导致的重定向）
if ($http_code != 200 && $http_code != 302) {
    http_response_code(503);
    echo "ERROR: Service returned HTTP $http_code";
    exit(1);
}

// 所有检查通过
http_response_code(200);
echo "OK: Service is healthy";
echo "\nTimestamp: " . date('Y-m-d H:i:s');
echo "\nPHP Version: " . PHP_VERSION;
echo "\ncURL Version: " . curl_version()['version'];
exit(0);
?>

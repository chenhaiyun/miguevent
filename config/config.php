<?php
/**
 * 配置文件读取器
 * 支持从 .env 文件读取配置，如果不存在则使用默认值
 */

function loadEnvConfig($envFile = __DIR__ . '/user.env') {
    $config = [
        'MIGU_USER_ID' => '你的userId',
        'MIGU_USER_TOKEN' => '你的userToken',
        'CACHE_EXPIRE_TIME' => 3600,
        'RATE_LIMIT_MAX' => 30,
        'LOG_LEVEL' => 1
    ];
    
    // 检查环境配置文件是否存在
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // 跳过注释行
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // 解析 KEY=VALUE 格式
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // 如果值有引号，去掉引号
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                $config[$key] = $value;
            }
        }
    }
    
    return $config;
}

// 加载配置
$envConfig = loadEnvConfig();
?>

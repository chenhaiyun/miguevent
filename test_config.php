<?php
/**
 * 配置测试页面
 * 用于验证配置文件是否正确加载
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== 咪咕配置测试页面 ===\n\n";

// 加载配置文件
require_once __DIR__ . '/config/config.php';

echo "1. 配置文件加载状态：\n";
if (isset($envConfig)) {
    echo "   ✅ 配置文件加载成功\n";
} else {
    echo "   ❌ 配置文件加载失败\n";
    exit;
}

echo "\n2. 配置文件路径：\n";
$configFile = __DIR__ . '/config/user.env';
echo "   文件路径: $configFile\n";
echo "   文件存在: " . (file_exists($configFile) ? "✅ 是" : "❌ 否") . "\n";

echo "\n3. 配置值：\n";
foreach ($envConfig as $key => $value) {
    // 隐藏敏感信息
    if (strpos($key, 'TOKEN') !== false) {
        $displayValue = strlen($value) > 10 ? substr($value, 0, 6) . '...' . substr($value, -4) : $value;
    } else {
        $displayValue = $value;
    }
    echo "   $key = $displayValue\n";
}

// 测试优先级
echo "\n4. 优先级测试（URL参数 > 环境配置 > 默认值）：\n";

// 模拟用户信息获取
$defaultUserId    = $envConfig['MIGU_USER_ID'] ?? "默认userId";
$defaultUserToken = $envConfig['MIGU_USER_TOKEN'] ?? "默认userToken";

$userId    = $_GET['userId'] ?? $defaultUserId;
$userToken = $_GET['userToken'] ?? $defaultUserToken;

echo "   当前使用的 userId: ";
if (isset($_GET['userId'])) {
    echo "来自URL参数\n";
} elseif ($envConfig['MIGU_USER_ID'] !== '你的userId') {
    echo "来自配置文件\n";
} else {
    echo "使用默认值\n";
}

echo "   当前使用的 userToken: ";
if (isset($_GET['userToken'])) {
    echo "来自URL参数\n";
} elseif ($envConfig['MIGU_USER_TOKEN'] !== '你的userToken') {
    echo "来自配置文件\n";
} else {
    echo "使用默认值\n";
}

echo "\n5. 其他配置：\n";
echo "   缓存过期时间: " . ($envConfig['CACHE_EXPIRE_TIME'] ?? '3600') . " 秒\n";
echo "   访问频率限制: " . ($envConfig['RATE_LIMIT_MAX'] ?? '30') . " 次/10分钟\n";
echo "   日志级别: " . ($envConfig['LOG_LEVEL'] ?? '1') . "\n";

echo "\n=== 测试完成 ===\n";

// 如果提供了测试参数，显示实际使用的值
if (isset($_GET['test'])) {
    echo "\n🧪 实际使用的值（用于调试）：\n";
    echo "userId: $userId\n";
    $tokenDisplay = strlen($userToken) > 10 ? substr($userToken, 0, 6) . '...' . substr($userToken, -4) : $userToken;
    echo "userToken: $tokenDisplay\n";
}
?>

<?php
header('Content-Type: application/json');

// 数据存储文件
$dataFile = 'saving_data.json';

// 在这里修改密码
define('PASSWORD', '123456');// 初始密码123456
// 处理不同请求
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        getData();
        break;
    case 'save':
        saveData();
        break;
    default:
        echo json_encode(['success' => false, 'message' => '无效的操作']);
}

// 获取数据
function getData() {
    global $dataFile;
    
    if (!file_exists($dataFile)) {
        // 初始化数据文件
        $initialData = [
            'targetAmount' => 0,
            'savedAmount' => 0,
            'lastUpdated' => date('Y-m-d H:i:s')
        ];
        file_put_contents($dataFile, json_encode($initialData));
    }
    
    $data = json_decode(file_get_contents($dataFile), true);
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
}

// 保存数据
function saveData() {
    global $dataFile;
    
    // 只接受POST请求
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => '只接受POST请求']);
        return;
    }
    
    // 获取输入数据
    $input = json_decode(file_get_contents('php://input'), true);
    
    // 验证密码
    if (!isset($input['password']) || $input['password'] !== PASSWORD) {
        echo json_encode(['success' => false, 'message' => '密码错误']);
        return;
    }
    
    // 验证金额
    $targetAmount = filter_var($input['targetAmount'] ?? 0, FILTER_VALIDATE_FLOAT);
    $savedAmount = filter_var($input['savedAmount'] ?? 0, FILTER_VALIDATE_FLOAT);
    
    if ($targetAmount === false || $savedAmount === false || $targetAmount < 0 || $savedAmount < 0) {
        echo json_encode(['success' => false, 'message' => '金额无效']);
        return;
    }
    
    // 准备保存的数据
    $data = [
        'targetAmount' => $targetAmount,
        'savedAmount' => $savedAmount,
        'lastUpdated' => date('Y-m-d H:i:s')
    ];
    
    // 保存到文件
    if (file_put_contents($dataFile, json_encode($data)) === false) {
        echo json_encode(['success' => false, 'message' => '保存数据失败']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'message' => '数据保存成功',
        'data' => $data
    ]);
}
?>
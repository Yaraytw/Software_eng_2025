<?php
// index.php - 後端 API 總入口 (Router)

// 1. 設定標頭，允許前端 AJAX 呼叫 (解決 CORS 問題)
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");

// 2. 引入所有子系統 (根據你的檔案列表)
include_once 'DbConnect.php';
include_once 'LoginSystem.php';
include_once 'SignUpSystem.php';
include_once 'BookingSystem.php';
include_once 'InquirySystem.php';
include_once 'RefundSystem.php';
include_once 'MemberSystem.php';
include_once 'TopUpSystem.php';
// include_once 'BrowseSystem.php'; // 如果有用到 BrowseSystem

// 3. 接收前端傳來的資料
$method = $_SERVER['REQUEST_METHOD'];
$action = '';
$inputData = [];

if ($method === 'POST') {
    // 讀取 JSON 格式的 POST 資料
    $json = file_get_contents("php://input");
    $inputData = json_decode($json, true);
    $action = $inputData['action'] ?? '';
} else {
    // 讀取 GET 參數
    $action = $_GET['action'] ?? '';
    $inputData = $_GET;
}

// 4. 根據 action 分派任務 (Routing)
$response = ["status" => false, "message" => "未知的請求"];

try {
    switch ($action) {
        // --- 會員系統 (Login & SignUp) ---
        case 'login':
            $sys = new LoginSystem();
            $response = $sys->MainLogin($inputData['account'], $inputData['pwd']);
            break;
            
        case 'logout':
            $sys = new LoginSystem();
            $response = $sys->Logout();
            break;

        case 'register':
            $sys = new SignUpSystem();
            $response = $sys->MainSign($inputData);
            break;

        // --- 訂票系統 (Booking) ---
        case 'get_movies':
            $sys = new BookingSystem();
            $data = $sys->SearchMovie(); // B2
            $response = ["status" => true, "data" => $data]; // 轉成 API 格式
            break;

        // 注意：dashboard.html 需要 get_dates, get_showings API
        // 這裡示範補上，對應 BookingSystem 的 SearchDate/SearchShowing
        case 'get_dates':
            $sys = new BookingSystem();
            $data = $sys->SearchDate($inputData['movieId']);
            $response = ["status" => true, "data" => $data];
            break;

        case 'get_showings':
            $sys = new BookingSystem();
            $data = $sys->SearchShowing($inputData['movieId'], $inputData['date']);
            $response = ["status" => true, "data" => $data];
            break;
            
        case 'get_seats':
            $sys = new BookingSystem();
            $data = $sys->SearchSeat($inputData['showingId']);
            $response = ["status" => true, "data" => $data];
            break;

        case 'booking': // 建立訂單
            // 需要確認 Session 登入狀態 (這裡簡化略過)
            $sys = new BookingSystem();
            // 這裡需要從 inputData 提取參數
            // $memberId, $showingId, $seats, $totalPrice, ...
            // 為了測試方便，你可以先寫死 memberId 或從 inputData 傳入
            $response = $sys->CreateBooking(
                $inputData['userId'], // dashboard.html 模擬的 userId
                $inputData['showingId'],
                $inputData['seats'],
                $inputData['totalPrice'],
                'card', // 預設付款方式
                '無餐點',
                1 // 預設票種
            );
            break;

        // --- 查詢與退票 (Inquiry & Refund) ---
        case 'my_tickets':
            $sys = new InquiryTicketSystem();
            $response = $sys->MainInquiry($inputData['userId']);
            break;

        case 'refund':
            $sys = new RefundSystem();
            $response = $sys->MainRefund($inputData['orderNumber'], $inputData['userId']);
            break;

        // --- 會員資料 (Member) ---
        case 'update_profile':
            $sys = new MemberChangeSystem();
            $response = $sys->MainMemberChange($inputData['userId'], $inputData);
            break;
            
        default:
            $response = ["status" => false, "message" => "Action '$action' not found"];
    }
} catch (Exception $e) {
    $response = ["status" => false, "message" => "伺服器錯誤: " . $e->getMessage()];
}

// 5. 回傳 JSON 結果
echo json_encode($response);
?>
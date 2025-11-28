<?php
// index.php - 後端 API 總入口 (Router)

// 1. 設定標頭，允許前端 AJAX 呼叫 (解決 CORS 問題)
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Origin: *"); // 建議加上這行以允許跨域測試

// 2. 引入所有子系統
include_once 'DbConnect.php';
include_once 'LoginSystem.php';
include_once 'SignUpSystem.php';
include_once 'BookingSystem.php';
include_once 'InquirySystem.php';
include_once 'RefundSystem.php';
include_once 'MemberSystem.php';
include_once 'TopUpSystem.php';
include_once 'AdminSystem.php'; // ★ 補上這行：後台管理系統

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

        // ★ 補上：忘記密碼流程 (對應 forgot_password.html)
        case 'get_hint':
            $sys = new LoginSystem();
            $response = $sys->ReturnHint($inputData['email']);
            break;

        case 'reset_password':
            $sys = new LoginSystem();
            // 注意參數順序需對應 ForgetPwdFlow($email, $hintId, $ans, $newPwd)
            $result = $sys->ForgetPwdFlow(
                $inputData['email'], 
                $inputData['hintId'], 
                $inputData['ans'], 
                $inputData['newPwd']
            );
            if ($result) {
                $response = ["status" => true, "message" => $result];
            } else {
                $response = ["status" => false, "message" => "驗證失敗或系統錯誤"];
            }
            break;

        // --- 訂票系統 (Booking) ---
        case 'get_movies':
            $sys = new BookingSystem();
            $data = $sys->SearchMovie(); // B2
            $response = ["status" => true, "data" => $data]; 
            break;

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

        // ★ 補上：前端 dashboard.html 需要的票種與餐點 API
        case 'get_ticket_types':
            $sys = new BookingSystem();
            $data = $sys->GetTicketTypes();
            $response = $data; // 前端預期直接回傳陣列
            break;

        case 'get_meals':
            $sys = new BookingSystem();
            $data = $sys->GetMeals();
            $response = $data; // 前端預期直接回傳陣列
            break;

        case 'booking': // 建立訂單
            $sys = new BookingSystem();
            // 這裡需要從 inputData 提取參數
            $response = $sys->CreateBooking(
                $inputData['userId'], 
                $inputData['showingId'],
                $inputData['seats'],
                $inputData['totalPrice'],
                $inputData['paymentMethod'], // 前端傳來的 'card' 或 'bank'
                $inputData['meals'],
                $inputData['ticketTypeId']
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

        // --- ★ 補上：後台管理 (Admin) ---
        case 'admin_add_movie':
            $sys = new AdminSystem();
            $response = $sys->AddMovie($inputData['name'], $inputData['grade']);
            break;

        case 'admin_add_showing':
            $sys = new AdminSystem();
            $response = $sys->AddShowing($inputData['movieId'], $inputData['date'], $inputData['time']);
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
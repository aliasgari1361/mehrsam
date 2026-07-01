<?php
/**
 * کنترلر پنل کاربری
 */
function karbar_route($action, $params) {
    // اگر کاربر لاگین نکرده باشد
    if (!isLoggedIn()) {
        if ($action === 'vorod') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';

                require_once __DIR__ . '/../../dade/bank.php';
                $bank = new Bank();
                $conn = $bank->getConnection();

                // فقط کاربران عادی (role='user') می‌توانند وارد شوند
                $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ? AND role = 'user'");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['role'] = $user['role']; // 'user'
                        redirect('karbar/namayeh');
                        exit;
                    } else {
                        $error = "نام کاربری یا رمز عبور اشتباه است.";
                    }
                } else {
                    $error = "نام کاربری یا رمز عبور اشتباه است.";
                }
                $stmt->close();
                $conn->close();
            }

            include __DIR__ . '/vokarbar.php';
            return;
        }

        redirect('karbar/vorod');
    }

    // کاربر عادی لاگین کرده است
    switch ($action) {
        case 'namayeh':
        default:
            echo "پنل کاربری - خوش آمدید";
            break;
    }
}
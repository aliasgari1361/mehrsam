<?php
/**
 * کنترلر پنل مدیریت
 * لاگین، داشبورد، تنظیمات، مدیریت محتوا با ویرایشگر کامل
 */

/**
 * بررسی محیط گیت روی هاست (exec فعال است؟ git نصب است؟)
 */
function mod_git_environment() {
    if (!function_exists('exec')) {
        return ['error' => 'روی این هاست تابع exec غیرفعال است (disable_functions). گیت را نمی‌توان اجرا کرد. از بخش «آپدیت و نگهداری» (آپلود ZIP) استفاده کنید.'];
    }
    $out = [];
    exec('git --version 2>&1', $out, $code);
    if ($code !== 0 || empty($out)) {
        return ['error' => 'git روی این هاست نصب نیست یا در مسیر (PATH) نیست. از بخش «آپدیت و نگهداری» (آپلود ZIP) استفاده کنید.'];
    }
    if (!is_dir(MASIR_RISH . '.git')) {
        return ['error' => 'این پروژه روی هاست به‌صورت مخزن git نیست (پوشه .git وجود ندارد). از بخش «آپدیت و نگهداری» (آپلود ZIP) استفاده کنید.'];
    }
    return ['ok' => true, 'version' => trim($out[0])];
}

function mod_route($action, $params) {
    if ($action === 'captcha') {
        require_once __DIR__ . '/../captcha.php';
        display_captcha_image();
        return;
    }

    // Auto-login via remember-me cookie
    if (!isLoggedIn() && isset($_COOKIE['rid']) && isset($_COOKIE['rtok'])) {
        require_once __DIR__ . '/../../dade/bank.php';
        $bank = new Bank();
        $conn = $bank->getConnection();
        $stmt = $conn->prepare("SELECT id, role FROM users WHERE id = ? AND remember_token = ? AND role = 'admin'");
        $stmt->bind_param("is", $_COOKIE['rid'], $_COOKIE['rtok']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
        }
        $stmt->close();
        $conn->close();
    }

    if (!isLoggedIn() || !isAdmin()) {
        if ($action === 'lomod') {
            $error = '';
            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                $captcha_input = $_POST['captcha'] ?? '';

                require_once __DIR__ . '/../../dade/bank.php';
                $bank = new Bank();
                $conn = $bank->getConnection();
                $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

                // ----- بررسی محدودیت تلاش -----
                $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM login_attempts WHERE ip_address = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 2 MINUTE)");
                $stmt->bind_param("s", $ip);
                $stmt->execute();
                $attempt_count = (int)$stmt->get_result()->fetch_assoc()['cnt'];
                $stmt->close();

                $too_many = false;
                if ($attempt_count >= 8) {
                    $too_many = true;
                    // محاسبه زمان باقی‌مانده
                    $stmt = $conn->prepare("SELECT MAX(attempted_at) AS latest FROM login_attempts WHERE ip_address = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 2 MINUTE)");
                    $stmt->bind_param("s", $ip);
                    $stmt->execute();
                    $latest = $stmt->get_result()->fetch_assoc()['latest'];
                    $stmt->close();
                    $remaining = 120 - (time() - strtotime($latest));
                    if ($remaining < 0) $remaining = 0;
                    $error = "به دلیل تلاش‌های ناموفق زیاد، به مدت ۲ دقیقه قفل شده‌اید. " . ceil($remaining) . " ثانیه دیگر تلاش کنید.";
                }

                // Verify CAPTCHA
                require_once __DIR__ . '/../captcha.php';
                if (!$too_many && !verify_captcha($captcha_input)) {
                    $error = "کد امنیتی اشتباه است.";
                } elseif (!$too_many) {
                    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ? AND role = 'admin'");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows === 1) {
                        $user = $result->fetch_assoc();
                        if (password_verify($password, $user['password'])) {
                            // ----- ورود موفق: ثبت کوکی ذخیره پسوورد -----
                            if (!empty($_POST['remember_me'])) {
                                $token = bin2hex(random_bytes(32));
                                $stmt2 = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                                $stmt2->bind_param("si", $token, $user['id']);
                                $stmt2->execute();
                                $stmt2->close();
                                setcookie('rid', (string)$user['id'], time() + 86400 * 30, '/', '', false, true);
                                setcookie('rtok', $token, time() + 86400 * 30, '/', '', false, true);
                            } else {
                                // حذف کوکی در صورت عدم تیک
                                $stmt2 = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
                                $stmt2->bind_param("i", $user['id']);
                                $stmt2->execute();
                                $stmt2->close();
                                setcookie('rid', '', time() - 3600, '/');
                                setcookie('rtok', '', time() - 3600, '/');
                            }

                            // پاک کردن تلاش‌های قبلی
                            $stmt2 = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                            $stmt2->bind_param("s", $ip);
                            $stmt2->execute();
                            $stmt2->close();

                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['role'] = $user['role'];
                            $stmt->close();
                            $conn->close();
                            send_login_alert($username, $user['id']);
                            redirect('mod/dashmod');
                            exit;
                        }
                        $error = "نام کاربری یا رمز عبور اشتباه است.";
                    } else {
                        $error = "نام کاربری یا رمز عبور اشتباه است.";
                    }
                    $stmt->close();
                }

                // ----- ثبت تلاش ناموفق -----
                $stmt = $conn->prepare("INSERT INTO login_attempts (ip_address) VALUES (?)");
                $stmt->bind_param("s", $ip);
                $stmt->execute();
                $stmt->close();

                // ----- بررسی ارسال ایمیل در قفل دوم -----
                if ($attempt_count === 7) { // هشتمین تلاش = قفل
                    $stmt = $conn->prepare("SELECT COUNT(DISTINCT FLOOR(UNIX_TIMESTAMP(attempted_at) / 120)) AS periods FROM login_attempts WHERE ip_address = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
                    $stmt->bind_param("s", $ip);
                    $stmt->execute();
                    $periods = (int)$stmt->get_result()->fetch_assoc()['periods'];
                    $stmt->close();
                    if ($periods >= 2) {
                        // ارسال ایمیل به مدیر
                        $to = SITE_EMAIL;
                        $subject = "هشدار امنیتی: تلاش‌های مکرر ورود به پنل مدیریت";
                        $message = "سلام مدیر,\n\n";
                        $message .= "تعداد زیادی تلاش ناموفق برای ورود به پنل مدیریت از IP " . $ip . " ثبت شده است.\n";
                        $message .= "این دومین دوره قفل شدن است.\n\n";
                        $message .= "زمان: " . date('Y-m-d H:i:s') . "\n";
                        $message .= "آی‌پی: " . $ip . "\n\n";
                        $message .= "لطفاً اقدامات امنیتی لازم را انجام دهید.\n";
                        $headers = "From: " . SITE_NAME . " <noreply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n";
                        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                        @mail($to, $subject, $message, $headers);
                    }
                }

                $conn->close();
            }

            include __DIR__ . '/../../ghaleb/ghmod/lomod.php';
            return;
        }
        // آپلود عمومی فایل‌ها برای کاربران لاگین‌شده (نه فقط ادمین) مجاز است
        if (!($action === 'upload' && isLoggedIn())) {
            redirect('mod/lomod');
        }
    }

    // نگاشت روت‌های میانبر به تب‌های تنظیمات سایت
    $tab_routes = [
        'gateways' => 'store/settings',
    ];
    if (isset($tab_routes[$action])) {
        redirect('mod/' . $tab_routes[$action]);
        return;
    }

    switch ($action) {

        case 'dashmod':
    $onvan_safhe = 'داشبورد مدیریت';
    $meta_sharh = 'پنل مدیریت سایت';
    require_once __DIR__ . '/../tanzimat.php';
    require_once __DIR__ . '/../../dade/bank.php';
    $bank = new Bank();
    $conn = $bank->getConnection();

    $posts_count    = $conn->query("SELECT COUNT(*) AS cnt FROM posts")->fetch_assoc()['cnt'] ?? 0;
    $pages_count    = $conn->query("SELECT COUNT(*) AS cnt FROM posts WHERE type='safhe'")->fetch_assoc()['cnt'] ?? 0;
    $articles_count = $conn->query("SELECT COUNT(*) AS cnt FROM posts WHERE type='maghaleh'")->fetch_assoc()['cnt'] ?? 0;
    $users_count    = $conn->query("SELECT COUNT(*) AS cnt FROM users")->fetch_assoc()['cnt'] ?? 0;
    $services_count = $conn->query("SELECT COUNT(*) AS cnt FROM posts WHERE type='khadamat' AND status='publish'")->fetch_assoc()['cnt'] ?? 0;

    $orders_count     = $conn->query("SELECT COUNT(*) AS cnt FROM sefaresh")->fetch_assoc()['cnt'] ?? 0;
    $orders_pending   = $conn->query("SELECT COUNT(*) AS cnt FROM sefaresh WHERE vaziat='pending'")->fetch_assoc()['cnt'] ?? 0;
    $orders_processing = $conn->query("SELECT COUNT(*) AS cnt FROM sefaresh WHERE vaziat='processing'")->fetch_assoc()['cnt'] ?? 0;
    $orders_revenue   = $conn->query("SELECT COALESCE(SUM(majmoo_gheymat), 0) AS total FROM sefaresh WHERE pardakht_vaziat='paid'")->fetch_assoc()['total'] ?? 0;

    $messages_count   = $conn->query("SELECT COUNT(*) AS cnt FROM payam_tamas")->fetch_assoc()['cnt'] ?? 0;

    $chat_active = 0;
    $chat_unread = 0;
    if ($conn->query("SHOW TABLES LIKE 'chat_sessions'")->num_rows > 0) {
        $chat_active = $conn->query("SELECT COUNT(*) AS cnt FROM chat_sessions WHERE status IN ('waiting','active')")->fetch_assoc()['cnt'] ?? 0;
        $chat_unread = $conn->query("SELECT COUNT(*) AS cnt FROM chat_messages cm JOIN chat_sessions cs ON cm.session_id = cs.id WHERE cs.status IN ('waiting','active') AND cm.sender_type='user' AND cm.created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)")->fetch_assoc()['cnt'] ?? 0;
    }

    $products_count = $conn->query("SELECT COUNT(*) AS cnt FROM mahsulat WHERE vaziat=1")->fetch_assoc()['cnt'] ?? 0;
    $categories_count = $conn->query("SELECT COUNT(*) AS cnt FROM mahsul_dasteh")->fetch_assoc()['cnt'] ?? 0;

    $recent_orders = $conn->query("SELECT id, onvan_girande, majmoo_gheymat, vaziat, created_at FROM sefaresh ORDER BY id DESC LIMIT 5");
    $recent_messages = $conn->query("SELECT id, nam, telefon, mozoo, created_at FROM payam_tamas ORDER BY id DESC LIMIT 5");
    $recent_posts = $conn->query("SELECT id, title, type, status, created_at FROM posts ORDER BY id DESC LIMIT 5");

    $recent_chats = [];
    if ($conn->query("SHOW TABLES LIKE 'chat_sessions'")->num_rows > 0) {
        $chat_result = $conn->query("SELECT cs.id, cs.user_name, cs.user_phone, cs.status, cs.last_activity, (SELECT message FROM chat_messages WHERE session_id = cs.id ORDER BY id DESC LIMIT 1) AS last_msg FROM chat_sessions cs WHERE cs.status IN ('waiting','active') ORDER BY cs.last_activity DESC LIMIT 5");
        if ($chat_result) while ($row = $chat_result->fetch_assoc()) $recent_chats[] = $row;
    }

    $conn->close();
    ?>

    <?php include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php'; ?>
    <style>
    @import url('<?= BASE_URL ?>ghaleb/manabe/fonts/fonts.css');
    .dash { font-family:'Vazirmatn','Tahoma',sans-serif; direction:rtl; }
    .dash h3 { font-size:1.4rem; margin-bottom:4px; color:#1a1a1a; }
    .dash .sub { color:#666; font-size:14px; margin-bottom:24px; }
    .dash-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(155px,1fr)); gap:14px; margin-bottom:32px; }
    .dash-card { position:relative; background:#fff; border-radius:12px; padding:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #eef0f4; display:flex; align-items:center; gap:12px; transition:all 0.2s; overflow:hidden; }
    .dash-card::before { content:''; position:absolute; inset:0 auto 0 0; width:4px; background:var(--c,#FF6F00); opacity:0.85; }
    .dash-card:hover { box-shadow:0 6px 20px rgba(0,0,0,0.10); transform:translateY(-3px); }
    .dash-card .icon { width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;flex-shrink:0; background:var(--c,#FF6F00); }
    .dash-card .info { flex:1; min-width:0; }
    .dash-card .num { font-size:1.9rem; font-weight:800; line-height:1.15; color:#1a1a1a; }
    .dash-card .lbl { font-size:13px; color:#666; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; font-weight:500; }
    .dash-card .badge { display:inline-block; font-size:10px; padding:2px 8px; border-radius:10px; font-weight:600; }
    .dash-row { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:32px; }
    .dash-panel { background:#fff; border-radius:14px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #eef0f4; overflow:hidden; }
    .dash-panel h4 { font-size:16px; padding:16px 20px; margin:0; border-bottom:1px solid #eef0f4; display:flex; align-items:center; gap:8px; font-weight:700; }
    .dash-panel h4 .view-all { margin-right:auto; margin-left:0; font-size:13px; font-weight:400; color:var(--rang-asli,#FF6F00); padding-right:12px; }
    .dash-panel .empty { padding:32px; text-align:center; color:#aaa; font-size:15px; }
    .dash-panel table { width:100%; border-collapse:collapse; }
    .dash-panel th { text-align:right; padding:12px 16px; font-size:14px; color:#888; font-weight:600; border-bottom:1px solid #eef0f4; }
    .dash-panel td { padding:12px 16px; font-size:16px; border-bottom:1px solid #f5f6f8; }
    .dash-panel tr:last-child td { border-bottom:none; }
    .dash-panel td a { color:var(--rang-asli,#FF6F00); text-decoration:none; font-weight:500; }
    .dash-panel td a:hover { text-decoration:underline; }
    .status-b { display:inline-block; padding:3px 12px; border-radius:10px; font-size:13px; font-weight:600; }
    .status-b.pending { background:#fff3e0; color:#e65100; }
    .status-b.processing { background:#e3f2fd; color:#1565c0; }
    .status-b.publish { background:#e8f5e9; color:#2e7d32; }
    .status-b.draft { background:#f5f5f5; color:#757575; }
    .dash-actions { display:flex; flex-wrap:wrap; gap:8px; }
    .dash-actions a { display:inline-flex;align-items:center;gap:6px; padding:10px 18px; border-radius:8px; font-size:14px; font-weight:600; text-decoration:none; transition:all 0.2s; background:#f5f6f8; color:#333; }
    .dash-actions a:hover { background:var(--rang-asli,#FF6F00); color:#fff; transform:translateY(-1px); }
    @media (max-width:768px) { .dash-row { grid-template-columns:1fr; } }
    </style>

    <div class="dash">
        <div class="dash-grid">
        <div class="dash-card" style="--c:#FF6F00;"><div class="icon"><i class="fa-solid fa-file-lines"></i></div><div class="info"><div class="num"><?= $posts_count ?></div><div class="lbl">کل مطالب</div></div></div>
        <div class="dash-card" style="--c:#E65100;"><div class="icon"><i class="fa-solid fa-copy"></i></div><div class="info"><div class="num"><?= $pages_count ?></div><div class="lbl">برگه‌ها</div></div></div>
        <div class="dash-card" style="--c:#BF360C;"><div class="icon"><i class="fa-solid fa-newspaper"></i></div><div class="info"><div class="num"><?= $articles_count ?></div><div class="lbl">مقالات</div></div></div>
        <div class="dash-card" style="--c:#6C5CE7;"><div class="icon"><i class="fa-solid fa-headset"></i></div><div class="info"><div class="num"><?= $services_count ?></div><div class="lbl">خدمات فعال</div></div></div>
        <div class="dash-card" style="--c:#00B894;"><div class="icon"><i class="fa-solid fa-cube"></i></div><div class="info"><div class="num"><?= $products_count ?></div><div class="lbl">محصولات</div></div></div>
        <div class="dash-card" style="--c:#0984E3;"><div class="icon"><i class="fa-solid fa-tags"></i></div><div class="info"><div class="num"><?= $categories_count ?></div><div class="lbl">دسته‌بندی‌ها</div></div></div>
        <div class="dash-card" style="--c:#E17055;"><div class="icon"><i class="fa-solid fa-shopping-cart"></i></div><div class="info"><div class="num"><?= $orders_count ?> <span class="badge" style="background:#fff3e0;color:#e65100;"><?= $orders_pending ?> در انتظار</span></div><div class="lbl"><?= $orders_processing ?> در حال پردازش</div></div></div>
        <div class="dash-card" style="--c:#FDCB6E;"><div class="icon"><i class="fa-solid fa-coins"></i></div><div class="info"><div class="num"><?= number_format($orders_revenue) ?></div><div class="lbl">تومان فروش</div></div></div>
        <a href="<?= BASE_URL ?>mod/messages" class="dash-card" style="--c:#00B894;text-decoration:none;color:inherit;"><div class="icon"><i class="fa-solid fa-comment-dots"></i></div><div class="info"><div class="num"><?= $messages_count ?></div><div class="lbl">پیام‌های تماس</div></div></a>
        <div class="dash-card" style="--c:#6C5CE7;"><div class="icon"><i class="fa-solid fa-comments"></i></div><div class="info"><div class="num"><?= $chat_active ?> <span class="badge" style="background:#ffe0e0;color:#c62828;"><?= $chat_unread ?> جدید</span></div><div class="lbl">چت فعال</div></div></div>
        <div class="dash-card" style="--c:#0984E3;"><div class="icon"><i class="fa-solid fa-users"></i></div><div class="info"><div class="num"><?= $users_count ?></div><div class="lbl">کاربران</div></div></div>
    </div>

    <div class="dash-row">
        <div class="dash-panel">
            <h4><i class="fa-solid fa-truck" style="color:#e65100;"></i> آخرین سفارشات</h4>
            <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
            <table><thead><tr><th>مشتری</th><th>مبلغ</th><th>وضعیت</th></tr></thead><tbody>
            <?php while ($o = $recent_orders->fetch_assoc()): ?>
            <tr><td><a href="<?= BASE_URL ?>mod/sefaresh/<?= $o['id'] ?>"><?= htmlspecialchars($o['onvan_girande']) ?></a></td><td><?= number_format($o['majmoo_gheymat']) ?></td><td><span class="status-b <?= $o['vaziat'] ?>"><?= $o['vaziat'] === 'pending' ? 'در انتظار' : ($o['vaziat'] === 'processing' ? 'در حال پردازش' : $o['vaziat']) ?></span></td></tr>
            <?php endwhile; ?>
            </tbody></table>
            <?php else: ?><div class="empty">هیچ سفارشی ثبت نشده</div><?php endif; ?>
        </div>
        <div class="dash-panel">
            <h4><a href="<?= BASE_URL ?>mod/messages" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px;width:100%;"><i class="fa-solid fa-envelope" style="color:#00B894;"></i> آخرین پیام‌های تماس <span class="view-all" style="margin-right:auto;margin-left:0;padding-right:12px;">مشاهده همه ←</span></a></h4>
            <?php if ($recent_messages && $recent_messages->num_rows > 0): ?>
            <table><thead><tr><th>نام</th><th>موضوع</th><th>تاریخ</th></tr></thead><tbody>
            <?php while ($m = $recent_messages->fetch_assoc()): ?>
            <tr><td><?= htmlspecialchars($m['nam']) ?></td><td><?= htmlspecialchars($m['mozoo'] ?? '—') ?></td><td style="font-size:11px;color:#888;"><?= $m['created_at'] ?><br><span style="color:var(--rang-asli,#FF6F00);"><?= to_jalali($m['created_at'], 'Y/m/d H:i') ?></span></td></tr>
            <?php endwhile; ?>
            </tbody></table>
            <?php else: ?><div class="empty">هیچ پیامی دریافت نشده</div><?php endif; ?>
        </div>
    </div>

    <div class="dash-row">
        <div class="dash-panel">
            <h4><i class="fa-solid fa-comment-dots" style="color:#6C5CE7;"></i> چت‌های فعال <span id="chat-dash-count" style="font-size:11px;color:#888;font-weight:400;"></span></h4>
            <?php if (!empty($recent_chats)): ?>
            <table id="chat-dash-table"><thead><tr><th>نام</th><th>آخرین پیام</th><th>عملیات</th></tr></thead><tbody>
            <?php foreach ($recent_chats as $c): ?>
            <tr id="chat-dash-row-<?= $c['id'] ?>">
                <td><?= htmlspecialchars($c['user_name']) ?></td>
                <td style="font-size:12px;color:#888;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars(mb_substr($c['last_msg'] ?? '', 0, 60)) ?></td>
                <td><a href="<?= BASE_URL ?>mod/chat_view/<?= $c['id'] ?>">مشاهده</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody></table>
            <?php else: ?><div class="empty" id="chat-dash-empty">چت فعالی وجود ندارد</div><?php endif; ?>
        </div>
        <div class="dash-panel">
            <h4><i class="fa-solid fa-clock-rotate-left" style="color:#0984E3;"></i> آخرین مطالب</h4>
            <?php if ($recent_posts && $recent_posts->num_rows > 0): ?>
            <table><thead><tr><th>عنوان</th><th>نوع</th><th>وضعیت</th></tr></thead><tbody>
            <?php while ($p = $recent_posts->fetch_assoc()): ?>
            <tr><td><a href="<?= BASE_URL ?>mod/edit_content/<?= $p['id'] ?>"><?= htmlspecialchars(mb_substr($p['title'], 0, 50)) ?></a></td><td style="font-size:12px;"><?= $p['type'] === 'maghaleh' ? 'مقاله' : ($p['type'] === 'safhe' ? 'برگه' : 'وبلاگ') ?></td><td><span class="status-b <?= $p['status'] ?>"><?= $p['status'] === 'publish' ? 'منتشر' : 'پیش‌نویس' ?></span></td></tr>
            <?php endwhile; ?>
            </tbody></table>
            <?php else: ?><div class="empty">مطلبی وجود ندارد</div><?php endif; ?>
        </div>
    </div>

    <script>
    function chatDashPoll() {
        fetch('<?= BASE_URL ?>mod/chat')
            .then(r => r.text())
            .then(html => {
                var match = html.match(/چت‌های فعال/i);
                if (match) {
                    location.reload();
                }
            });
    }
    setInterval(chatDashPoll, 10000);
    </script>

    <div class="dash-actions">
        <a href="<?php echo BASE_URL; ?>mod/content"><i class="fa-solid fa-plus"></i> مطلب جدید</a>
        <a href="<?php echo BASE_URL; ?>mod/pages"><i class="fa-solid fa-file"></i> برگه جدید</a>
        <a href="<?php echo BASE_URL; ?>mod/services"><i class="fa-solid fa-headset"></i> خدمات</a>
        <a href="<?php echo BASE_URL; ?>mod/chat"><i class="fa-solid fa-comment-dots"></i> چت</a>
        <a href="<?php echo BASE_URL; ?>mod/store/products"><i class="fa-solid fa-store"></i> فروشگاه</a>
        <a href="<?php echo BASE_URL; ?>mod/theme"><i class="fa-solid fa-palette"></i> قالب</a>
        <a href="<?php echo BASE_URL; ?>mod/builder/pages"><i class="fa-solid fa-layer-group"></i> صفحه‌ساز</a>
        <a href="<?php echo BASE_URL; ?>mod/settings"><i class="fa-solid fa-gear"></i> تنظیمات سایت</a>
    </div>
    </div>
<?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
    break;

        case 'content':
            require_once __DIR__ . '/../tanzimat.php';
            require_once __DIR__ . '/../../dade/bank.php';
            $bank = new Bank();
            $conn = $bank->getConnection();
            $result = $conn->query("SELECT id, title, slug, type, status, created_at FROM posts WHERE type IN ('blog','maghaleh') ORDER BY created_at DESC");
            $posts_list = [];
            if ($result) while ($row = $result->fetch_assoc()) $posts_list[] = $row;
            $conn->close();

            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            ?>
            <h3>مدیریت محتوا</h3>
            <p><a href="<?php echo BASE_URL; ?>mod/edit_content">+ مطلب جدید</a></p>
            <table border="1" cellpadding="5" cellspacing="0" width="100%">
                <tr><th>عنوان</th><th>نوع</th><th>وضعیت</th><th>تاریخ</th><th>عملیات</th></tr>
                <?php foreach ($posts_list as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['title']); ?></td>
                    <td><?php echo $p['type'] === 'maghaleh' ? 'مقاله' : 'وبلاگ'; ?></td>
                    <td><?php echo $p['status'] === 'publish' ? 'منتشر' : 'پیش‌نویس'; ?></td>
                    <td><?php echo $p['created_at']; ?><br><span style="color:var(--rang-asli,#FF6F00);"><?php echo to_jalali($p['created_at'], 'Y/m/d H:i'); ?></span></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>mod/edit_content/<?php echo $p['id']; ?>">ویرایش</a> |
                        <a href="<?php echo BASE_URL; ?>mod/builder/edit_post/<?php echo $p['type']; ?>/<?php echo $p['id']; ?>">صفحه‌ساز</a> |
                        <a href="<?php echo BASE_URL; ?>mod/delete_content/<?php echo $p['id']; ?>" onclick="return confirm('مطمئنی؟')">حذف</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'pages':
            require_once __DIR__ . '/../../dade/bank.php';
            $bank = new Bank();
            $conn = $bank->getConnection();
            $result = $conn->query("SELECT id, title, slug, type, status, page_section, template, created_at FROM posts WHERE type='page' OR type='safhe' ORDER BY display_order ASC, id ASC");
            $pages = [];
            if ($result) while ($row = $result->fetch_assoc()) $pages[] = $row;
            $conn->close();

            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            ?>
            <h3>مدیریت صفحات</h3>
            <p><a href="<?php echo BASE_URL; ?>mod/edit_page">+ صفحه جدید</a></p>
            <table border="1" cellpadding="5" cellspacing="0" width="100%">
                <tr><th>عنوان</th><th>slug</th><th>قالب</th><th>وضعیت</th><th>عملیات</th></tr>
                <?php foreach ($pages as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['title']); ?></td>
                    <td><?php echo htmlspecialchars($p['slug']); ?></td>
                    <td><?php echo htmlspecialchars($p['template'] ?? 'default'); ?></td>
                    <td><?php echo $p['status']; ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>mod/edit_page/<?php echo $p['id']; ?>">ویرایش</a> |
                        <a href="<?php echo BASE_URL; ?>mod/builder/edit_post/<?php echo $p['type']; ?>/<?php echo $p['id']; ?>">صفحه‌ساز</a> |
                        <a href="<?php echo BASE_URL; ?>mod/delete_page/<?php echo $p['id']; ?>" onclick="return confirm('مطمئنی؟')">حذف</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'edit_page':
            require_once __DIR__ . '/../../dade/bank.php';
            $bank = new Bank();
            $conn = $bank->getConnection();
            $id = $params[0] ?? null;
            $is_edit = false;
            $post = ['title' => '', 'slug' => '', 'content' => '', 'type' => 'page', 'template' => 'default', 'status' => 'publish'];

            if ($id) {
                $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    $post = $result->fetch_assoc();
                    $is_edit = true;
                } else {
                    echo "صفحه پیدا نشد.";
                    $conn->close();
                    break;
                }
                $stmt->close();
            }

            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                $title   = $_POST['title'] ?? '';
                $slug    = $_POST['slug'] ?? '';
                $content = $_POST['content'] ?? '';
                $template = $_POST['template'] ?? 'default';
                $status  = $_POST['status'] ?? 'publish';
                if (empty($slug)) $slug = trim(preg_replace('/[^a-zA-Z0-9\-]/', '-', $title), '-');

                if ($is_edit) {
                    $stmt = $conn->prepare("UPDATE posts SET title=?, slug=?, content=?, template=?, status=? WHERE id=?");
                    $stmt->bind_param("sssssi", $title, $slug, $content, $template, $status, $id);
                    $stmt->execute();
                    $stmt->close();
                    $new_id = $id;
                } else {
                    $stmt = $conn->prepare("INSERT INTO posts (title, slug, content, type, template, status) VALUES (?, ?, ?, 'page', ?, ?)");
                    $stmt->bind_param("sssss", $title, $slug, $content, $template, $status);
                    $stmt->execute();
                    $new_id = $conn->insert_id;
                    $stmt->close();
                }
                // ساخت خودکار پوشه فایل‌های این صفحه
                require_once MASIR_RISH . 'mohtava/file/file-functions.php';
                file_create_content_folder('page', $slug ?: $new_id);
                $conn->close();
                redirect('mod/pages');
                exit;
            }
            $conn->close();

            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            ?>
            <h3><?php echo $is_edit ? 'ویرایش صفحه' : 'صفحه جدید'; ?></h3>
            <form method="post">
                <label>عنوان: <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required></label><br><br>
                <label>slug: <input type="text" name="slug" value="<?php echo htmlspecialchars($post['slug']); ?>"></label><br><br>
                <label>قالب: <select name="template">
                    <option value="home" <?php echo $post['template']=='home' ? 'selected' : ''; ?>>خانه</option>
                    <option value="services" <?php echo $post['template']=='services' ? 'selected' : ''; ?>>خدمات</option>
                    <option value="blog" <?php echo $post['template']=='blog' ? 'selected' : ''; ?>>بلاگ</option>
                    <option value="contact" <?php echo $post['template']=='contact' ? 'selected' : ''; ?>>تماس</option>
                    <option value="default" <?php echo $post['template']=='default' ? 'selected' : ''; ?>>پیش‌فرض</option>
                </select></label><br><br>
                <label>وضعیت: <select name="status">
                    <option value="publish" <?php echo $post['status']=='publish' ? 'selected' : ''; ?>>منتشر</option>
                    <option value="draft" <?php echo $post['status']=='draft' ? 'selected' : ''; ?>>پیش‌نویس</option>
                </select></label><br><br>
                <label>محتوا:</label><br>
                <?php
                $edr_value = htmlspecialchars($post['content']);
                $edr_name = 'content';
                $edr_id = 'pageContent';
                include __DIR__ . '/../editor/editor.php';
                ?>
                <br>
                <button type="submit">ذخیره</button>
            </form>
            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'delete_page':
            $id = $params[0] ?? null;
            if ($id) {
                require_once __DIR__ . '/../../dade/bank.php';
                $bank = new Bank();
                $conn = $bank->getConnection();
                $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                $conn->close();
            }
            redirect('mod/pages');
            break;

        case 'services':
            require_once __DIR__ . '/../../dade/bank.php';
            $bank = new Bank();
            $conn = $bank->getConnection();
            $result = $conn->query("SELECT id, title, slug, subtitle, kholaseh, display_order, status, created_at FROM posts WHERE type='khadamat' ORDER BY display_order ASC, id DESC");
            $services_list = [];
            if ($result) while ($row = $result->fetch_assoc()) $services_list[] = $row;
            $conn->close();

            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            ?>
            <h3>مدیریت خدمات</h3>
            <p><a href="<?php echo BASE_URL; ?>mod/add_service">+ خدمت جدید</a></p>
            <table border="1" cellpadding="5" cellspacing="0" width="100%">
                <tr><th>عنوان</th><th>زیرعنوان</th><th>ترتیب</th><th>وضعیت</th><th>عملیات</th></tr>
                <?php foreach ($services_list as $s): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['title']); ?></td>
                    <td><?php echo htmlspecialchars($s['subtitle'] ?? ''); ?></td>
                    <td><?php echo $s['display_order']; ?></td>
                    <td><?php echo $s['status'] === 'publish' ? 'منتشر' : 'پیش‌نویس'; ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>mod/edit_service/<?php echo $s['id']; ?>">ویرایش</a> |
                        <a href="<?php echo BASE_URL; ?>mod/builder/edit_post/khadamat/<?php echo $s['id']; ?>">صفحه‌ساز</a> |
                        <a href="<?php echo BASE_URL; ?>mod/delete_service/<?php echo $s['id']; ?>" onclick="return confirm('مطمئنی؟')">حذف</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'add_service':
        case 'edit_service':
            require_once __DIR__ . '/../../dade/bank.php';
            $bank = new Bank();
            $conn = $bank->getConnection();
            $id = $params[0] ?? null;
            $is_edit = ($action === 'edit_service' && $id);
            $service = ['title' => '', 'slug' => '', 'subtitle' => '', 'kholaseh' => '', 'tasvir' => '', 'content' => '', 'display_order' => 0, 'status' => 'publish'];

            if ($is_edit) {
                $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND type = 'khadamat'");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    $service = $result->fetch_assoc();
                } else {
                    echo "خدمت پیدا نشد.";
                    $conn->close();
                    break;
                }
                $stmt->close();
            }

            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                $title   = $_POST['title'] ?? '';
                $slug    = $_POST['slug'] ?? '';
                $subtitle = $_POST['subtitle'] ?? '';
                $kholaseh = $_POST['kholaseh'] ?? '';
                $tasvir  = $_POST['tasvir'] ?? '';
                $display_order = (int)($_POST['display_order'] ?? 0);
                $status  = $_POST['status'] ?? 'publish';
                if (empty($slug)) $slug = trim(preg_replace('/[^a-zA-Z0-9\-]/', '-', $title), '-');

                if ($is_edit) {
                    $stmt = $conn->prepare("UPDATE posts SET title=?, slug=?, subtitle=?, kholaseh=?, tasvir=?, display_order=?, status=? WHERE id=? AND type='khadamat'");
                    $stmt->bind_param("ssssssii", $title, $slug, $subtitle, $kholaseh, $tasvir, $display_order, $status, $id);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $stmt = $conn->prepare("INSERT INTO posts (title, slug, subtitle, kholaseh, tasvir, display_order, type, status) VALUES (?, ?, ?, ?, ?, ?, 'khadamat', ?)");
                    $stmt->bind_param("sssssis", $title, $slug, $subtitle, $kholaseh, $tasvir, $display_order, $status);
                    $stmt->execute();
                    $id = $conn->insert_id;
                    $stmt->close();
                }

                require_once MASIR_RISH . 'mohtava/file/file-functions.php';
                file_create_content_folder('khadamat', $slug ?: $id);
                $conn->close();
                redirect('mod/services');
                exit;
            }
            $conn->close();

            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            ?>
            <h3><?php echo $is_edit ? 'ویرایش خدمت' : 'افزودن خدمت جدید'; ?></h3>
            <?php if ($is_edit): ?>
            <div style="margin:0 0 20px;padding:16px 20px;background:#fff3e0;border:1px solid #ffd9a0;border-radius:12px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
                <div>
                    <strong style="color:#b25e00;">محتوای جزئیات خدمت با صفحه‌ساز ویرایش می‌شود</strong>
                    <div style="font-size:13px;color:#8a6d3b;margin-top:4px;">از دکمه زیر برای ویرایش محتوای صفحه‌ساز استفاده کنید.</div>
                </div>
                <a href="<?php echo BASE_URL; ?>mod/builder/edit_post/khadamat/<?php echo $id; ?>" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;background:var(--rang-asli,#FF6F00);color:#fff;font-weight:700;text-decoration:none;white-space:nowrap;"><i class="fa-solid fa-layer-group"></i> ویرایش با صفحه‌ساز</a>
            </div>
            <?php endif; ?>
            <form method="post">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin:20px 0;">
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">عنوان خدمت</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($service['title']); ?>" required style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">نامک (slug)</label>
                        <input type="text" name="slug" value="<?php echo htmlspecialchars($service['slug']); ?>" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">زیرعنوان</label>
                        <input type="text" name="subtitle" value="<?php echo htmlspecialchars($service['subtitle'] ?? ''); ?>" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;" placeholder="مثلاً: پشتیبانی تخصصی">
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">ترتیب نمایش</label>
                        <input type="number" name="display_order" value="<?php echo (int)($service['display_order'] ?? 0); ?>" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">
                    </div>
                    <div style="grid-column:span 2;">
                        <label style="display:block;font-weight:600;margin-bottom:4px;">خلاصه خدمت (نمایش در لیست)</label>
                        <textarea name="kholaseh" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;min-height:60px;"><?php echo htmlspecialchars($service['kholaseh'] ?? ''); ?></textarea>
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">آیکون (کد SVG یا آیکون فا)</label>
                        <input type="text" name="tasvir" value="<?php echo htmlspecialchars($service['tasvir'] ?? ''); ?>" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;" placeholder="<i class='fa-solid fa-wifi'></i>">
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">وضعیت</label>
                        <select name="status" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">
                            <option value="publish" <?php echo ($service['status'] ?? '')=='publish' ? 'selected' : ''; ?>>منتشر</option>
                            <option value="draft" <?php echo ($service['status'] ?? '')=='draft' ? 'selected' : ''; ?>>پیش‌نویس</option>
                        </select>
                    </div>
                </div>
                <button type="submit" style="padding:12px 32px;background:var(--rang-asli);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:15px;">ذخیره خدمت</button>
            </form>
            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'delete_service':
            $id = $params[0] ?? null;
            if ($id) {
                require_once __DIR__ . '/../../dade/bank.php';
                $bank = new Bank();
                $conn = $bank->getConnection();
                $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND type = 'khadamat'");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                $conn->close();
            }
            redirect('mod/services');
            break;

        case 'panel_settings':
            require_once __DIR__ . '/../tanzimat.php';
            $admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true) ?: ['bg_color' => '#f0f2f5', 'font' => 'Tahoma', 'favicon' => ''];
            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                $new_settings = [
                    'bg_color' => $_POST['bg_color'] ?? '#f0f2f5',
                    'font'     => $_POST['font'] ?? 'Tahoma',
                    'favicon'  => $_POST['favicon'] ?? '',
                    'font_size'       => (int)($_POST['font_size'] ?? 14),
                    'menu_font_size'  => (int)($_POST['menu_font_size'] ?? 13),
                    'table_font_size' => (int)($_POST['table_font_size'] ?? 14),
                    'title_font_size' => (int)($_POST['title_font_size'] ?? 18),
                ];
                save_admin_settings($new_settings);
                $admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true) ?: $new_settings;
                $message = "تنظیمات پنل ذخیره شد.";
            }
            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            ?>
            <h3>تنظیمات پنل مدیریت</h3>
            <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
            <form method="post">
                <label>رنگ پس‌زمینه:</label>
                <input type="color" name="bg_color" value="<?php echo htmlspecialchars($admin_settings['bg_color']); ?>"><br><br>

                <label>فونت:</label>
                <select name="font">
                    <option value="Tahoma" <?php if($admin_settings['font']=='Tahoma') echo 'selected'; ?>>Tahoma</option>
                    <option value="Arial" <?php if($admin_settings['font']=='Arial') echo 'selected'; ?>>Arial</option>
                    <option value="Vazir" <?php if($admin_settings['font']=='Vazir') echo 'selected'; ?>>وزیر</option>
                </select><br><br>

                <fieldset style="border:1px solid #dde1e6;border-radius:8px;padding:16px;margin-bottom:16px;">
                    <legend style="padding:0 8px;font-weight:700;">اندازه فونت بخش‌های پنل (پیکسل)</legend>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div><label>متن عمومی:</label><br><input type="number" name="font_size" value="<?php echo (int)($admin_settings['font_size'] ?? 14); ?>" min="11" max="22" style="width:90px;"></div>
                        <div><label>منوی بالا:</label><br><input type="number" name="menu_font_size" value="<?php echo (int)($admin_settings['menu_font_size'] ?? 13); ?>" min="10" max="20" style="width:90px;"></div>
                        <div><label>جداول:</label><br><input type="number" name="table_font_size" value="<?php echo (int)($admin_settings['table_font_size'] ?? 14); ?>" min="11" max="22" style="width:90px;"></div>
                        <div><label>عنوان صفحات:</label><br><input type="number" name="title_font_size" value="<?php echo (int)($admin_settings['title_font_size'] ?? 18); ?>" min="14" max="30" style="width:90px;"></div>
                    </div>
                </fieldset>

                <button type="submit">ذخیره</button>
            </form>
            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'settings':
            require_once __DIR__ . '/../tanzimat.php';
            global $site_settings;
            $current = $site_settings;

            // مدیریت آپلود فایل
            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                // آپلود لوگو
                if (!empty($_FILES['logo']['name'])) {
                    $logo_result = upload_site_image('logo', 'logo/');
                    if (is_array($logo_result) && isset($logo_result['error'])) {
                        $message = "<p style='color:red;'>خطای لوگو: {$logo_result['error']}</p>";
                    } elseif ($logo_result) {
                        $_POST['general']['logo'] = $logo_result;
                    }
                }
                // آپلود فاوآیکون
                if (!empty($_FILES['favicon']['name'])) {
                    $fav_result = upload_site_image('favicon', 'favicon/');
                    if (is_array($fav_result) && isset($fav_result['error'])) {
                        $message = "<p style='color:red;'>خطای فاوآیکون: {$fav_result['error']}</p>";
                    } elseif ($fav_result) {
                        $_POST['general']['favicon'] = $fav_result;
                    }
                }

                // ذخیره تنظیمات
                $new_settings = [];
                foreach (['general', 'social', 'theme', 'files', 'support', 'notif'] as $section) {
                    if (!empty($_POST[$section]) && is_array($_POST[$section])) {
                        $new_settings[$section] = $_POST[$section];
                    }
                }
                if (!empty($new_settings)) {
                    save_site_settings($new_settings);
                    $current = $site_settings;
                    $message = "<p style='color:green;'>تنظیمات با موفقیت ذخیره شد.</p>";
                }
            }

            $active_tab = $_GET['tab'] ?? 'general';
            $tabs = [
                'general'      => 'عمومی',
                'social'       => 'شبکه‌ها',
                'notifications'=> 'اعلان‌ها',
                'messages'     => 'پشتیبانی',
                'files'        => 'فایل‌ها',
            ];
            $standalone = in_array($active_tab, ['theme', 'git'], true);

            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            ?>
            <style>
                .settings-tabs { display:flex; gap:4px; margin-bottom:20px; border-bottom:2px solid var(--rang-border); padding-bottom:4px; }
                .settings-tabs a { padding:10px 20px; border-radius:8px 8px 0 0; text-decoration:none; font-weight:600; color:var(--rang-gray); background:#f8f9fa; border:1px solid var(--rang-border); border-bottom:none; transition:all .2s; }
                .settings-tabs a:hover { background:#eef; color:var(--rang-asli); }
                .settings-tabs a.active { background:#fff; color:var(--rang-asli); border-color:var(--rang-asli); border-bottom:2px solid #fff; margin-bottom:-2px; }
                .settings-panel { background:#fff; border:1px solid var(--rang-border); border-radius:12px; padding:24px; }
                .form-group { margin-bottom:20px; }
                .form-group label { display:block; margin-bottom:6px; font-weight:600; color:var(--rang-matn); }
                .form-group input[type=text], .form-group input[type=email], .form-group input[type=tel], .form-group input[type=url], .form-group input[type=number], .form-group select, .form-group textarea { width:100%; padding:12px; border:1.5px solid #cdd3da; background:#f6f7f9; border-radius:8px; font-family:inherit; font-size:1rem; box-sizing:border-box; color:#1a1a1a; }
                .form-group input[type=color] { background:#fff; }
                .form-group textarea { min-height:100px; resize:vertical; direction:rtl; }
                .form-group input[type=color] { width:60px; height:40px; padding:0; border:none; border-radius:8px; cursor:pointer; }
                .form-group .color-preview { display:inline-block; width:30px; height:30px; border-radius:6px; border:2px solid var(--rang-border); vertical-align:middle; margin-right:10px; }
                .form-group .checkbox-label { display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:500; }
                .form-group .checkbox-label input { width:20px; height:20px; accent-color:var(--rang-asli); }
                .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
                .form-row.single { grid-template-columns:1fr; }
                .gateway-card { border:1px solid var(--rang-border); border-radius:12px; padding:20px; margin-bottom:16px; background:#fafafa; }
                .gateway-card h4 { margin:0 0 16px; display:flex; align-items:center; gap:10px; }
                .gateway-card .toggle-switch { position:relative; width:56px; height:28px; }
                .gateway-card .toggle-switch input { opacity:0; width:0; height:0; }
                .gateway-card .toggle-slider { position:absolute; inset:0; background:#ccc; border-radius:28px; transition:.3s; cursor:pointer; }
                .gateway-card .toggle-slider:before { content:''; position:absolute; width:22px; height:22px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.3s; box-shadow:0 2px 4px rgba(0,0,0,.2); }
                .gateway-card .toggle-switch input:checked + .toggle-slider { background:var(--rang-asli); }
                .gateway-card .toggle-switch input:checked + .toggle-slider:before { transform:translateX(28px); }
                .btn { padding:12px 24px; border:none; border-radius:8px; font-family:inherit; font-size:1rem; font-weight:700; cursor:pointer; transition:.2s; }
                .btn-primary { background:var(--rang-asli); color:#fff; }
                .btn-primary:hover { background:var(--rang-tira); }
                .section-title { font-size:1.1rem; margin-bottom:8px; color:var(--rang-matn); }
                .help-text { font-size:.85rem; color:var(--rang-gray); margin-top:4px; display:block; }
            </style>

            <h3 style="margin-bottom:8px;">تنظیمات سایت</h3>
            <p style="color:var(--rang-gray); margin-bottom:24px;">مدیریت تنظیمات عمومی و شبکه‌های اجتماعی</p>
            <?php if (isset($message)) echo $message; ?>

            <?php if (!$standalone): ?>
            <div class="settings-tabs">
                <?php foreach ($tabs as $key => $label): ?>
                    <a href="?tab=<?= $key ?>" class="<?= $active_tab === $key ? 'active' : '' ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" action="<?= BASE_URL ?>mod/settings">
                <input type="hidden" name="tab" value="<?= $active_tab ?>">

                <?php if ($active_tab === 'general'): ?>
                <div class="settings-panel">
                    <h4 class="section-title"><i class="fa-solid fa-gear"></i> تنظیمات عمومی</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label>عنوان سایت</label>
                            <input type="text" name="general[site_title]" value="<?= htmlspecialchars($current['general']['site_title'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>شعار / توضیح کوتاه</label>
                            <input type="text" name="general[site_slogan]" value="<?= htmlspecialchars($current['general']['site_slogan'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>ایمیل</label>
                            <input type="email" name="general[site_email]" value="<?= htmlspecialchars($current['general']['site_email'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>تلفن (نمایش)</label>
                            <input type="tel" name="general[site_tel]" value="<?= htmlspecialchars($current['general']['site_tel'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>تلفن (انگلیسی برای لینک)</label>
                            <input type="tel" name="general[site_tel_en]" value="<?= htmlspecialchars($current['general']['site_tel_en'] ?? '') ?>" dir="ltr">
                        </div>
                        <div class="form-group">
                            <label>ساعت کاری</label>
                            <input type="text" name="general[site_hours]" value="<?= htmlspecialchars($current['general']['site_hours'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>آدرس کامل</label>
                        <textarea name="general[site_adres]"><?= htmlspecialchars($current['general']['site_adres'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>لوگو سایت</label>
                            <input type="file" name="logo" accept="image/*">
                            <?php if (!empty($current['general']['logo'])): ?>
                                <div style="margin-top:8px;">
                                    <img src="<?= $current['general']['logo'] ?>" alt="Logo" style="max-height:60px; border-radius:8px; border:1px solid var(--rang-border);">
                                    <br><small>فایل جدید انتخاب کنید تا جایگزین شود</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>فاوآیکون</label>
                            <input type="file" name="favicon" accept="image/*">
                            <?php if (!empty($current['general']['favicon'])): ?>
                                <div style="margin-top:8px;">
                                    <img src="<?= $current['general']['favicon'] ?>" alt="Favicon" style="width:32px; height:32px; border-radius:4px;">
                                    <br><small>فایل جدید انتخاب کنید تا جایگزین شود</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>کد Embed نقشه (Google Maps / OpenStreetMap)</label>
                        <textarea name="general[map_embed_url]" style="min-height:120px; font-family:monospace; direction:ltr;"><?= htmlspecialchars($current['general']['map_embed_url'] ?? '') ?></textarea>
                        <span class="help-text">مثال: https://maps.google.com/maps?q=35.7257,51.3814&z=15&output=embed</span>
                    </div>
                </div>

                <?php elseif ($active_tab === 'social'): ?>
                <div class="settings-panel">
                    <h4 class="section-title"><i class="fa-brands fa-telegram"></i> شبکه‌های اجتماعی</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fa-brands fa-telegram" style="color:#0088cc;"></i> تلگرام</label>
                            <input type="url" name="social[telegram]" value="<?= htmlspecialchars($current['social']['telegram'] ?? '') ?>" placeholder="https://t.me/...">
                        </div>
                        <div class="form-group">
                            <label><i class="fa-brands fa-whatsapp" style="color:#25d366;"></i> واتس‌اپ</label>
                            <input type="url" name="social[whatsapp]" value="<?= htmlspecialchars($current['social']['whatsapp'] ?? '') ?>" placeholder="https://wa.me/...">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fa-brands fa-bale" style="color:#0088cc;"></i> بله</label>
                            <input type="url" name="social[bale]" value="<?= htmlspecialchars($current['social']['bale'] ?? '') ?>" placeholder="https://ble.ir/...">
                        </div>
                        <div class="form-group">
                            <label><i class="fa-brands fa-instagram" style="color:#e4405f;"></i> اینستاگرام</label>
                            <input type="url" name="social[instagram]" value="<?= htmlspecialchars($current['social']['instagram'] ?? '') ?>" placeholder="https://instagram.com/...">
                        </div>
                    </div>
                </div>
                <?php elseif ($active_tab === 'notifications'): ?>
                <?php
                $notif = $current['notif'] ?? [];
                $push_count = 0;
                try {
                    require_once MASIR_RISH . 'afzuneh/elpayaagh/Notifier.php';
                    $__kp = new kanal_push();
                    $push_count = $__kp->count_subscriptions();
                } catch (\Throwable $e) { $push_count = 0; }
                ?>
                <div class="settings-panel" style="margin-bottom:20px;">
                    <h4 class="section-title"><i class="fa-solid fa-bell" style="color:var(--rang-asli)"></i> اعلان گوشی من (Web Push)</h4>
                    <p class="help-text" style="margin-bottom:12px;">هر پیام چت / تماس / سفارش جدید، نوتیف مستقیم روی گوشی تو. با گوشی اندروید (کروم) وارد لینک شو و دکمه را بزن:</p>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                        <a href="<?= BASE_URL ?>mod/push" target="_blank" class="btn btn-primary" style="text-decoration:none;"><i class="fa-solid fa-mobile-screen-button"></i> صفحه فعالسازی اعلان</a>
                        <span style="background:#f5f6f8;padding:8px 14px;border-radius:8px;font-size:13px;"><b><?= $push_count ?></b> دستگاه ثبت شده</span>
                    </div>

                    <hr style="border:none;border-top:1px dashed var(--rang-border);margin:18px 0;">

                    <h4 class="section-title"><i class="fa-brands fa-bale" style="color:#0088cc"></i> ربات بله (اعلان جایگزین)</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>توکن ربات بله</label>
                            <input type="text" name="notif[bale_token]" value="<?= htmlspecialchars($notif['bale_token'] ?? '') ?>" placeholder="توکن را از BotFather بله بگیر" dir="ltr">
                        </div>
                        <div class="form-group">
                            <label>آیدی عددی من در بله</label>
                            <input type="text" name="notif[bale_id]" value="<?= htmlspecialchars($notif['bale_id'] ?? '') ?>" placeholder="مثلا 123456789" dir="ltr">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>توکن ربات تلگرام (اختیاری)</label>
                            <input type="text" name="notif[telegram_token]" value="<?= htmlspecialchars($notif['telegram_token'] ?? '') ?>" dir="ltr">
                        </div>
                        <div class="form-group">
                            <label>آیدی عددی من در تلگرام (اختیاری)</label>
                            <input type="text" name="notif[telegram_id]" value="<?= htmlspecialchars($notif['telegram_id'] ?? '') ?>" dir="ltr">
                        </div>
                    </div>
                </div>

                <?php elseif ($active_tab === 'messages'): ?>
                <?php
                $sup = $current['support'] ?? [];
                $supc = function ($ch, $k, $def = '') use ($sup) { return htmlspecialchars($sup['channels'][$ch][$k] ?? $def); };
                $supon = function ($ch) use ($sup) { return !empty($sup['channels'][$ch]['on']); };
                $supcol = function ($ch, $def) use ($sup) { return htmlspecialchars($sup['channels'][$ch]['color'] ?? $def); };
                ?>
                <div class="settings-panel" style="margin-bottom:20px;">
                    <h4 class="section-title"><i class="fa-solid fa-headset" style="color:var(--rang-asli)"></i> دکمه پشتیبانی سایت (برای مشتریها)</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="checkbox-label"><input type="checkbox" name="support[enabled]" value="1" <?= !isset($sup['enabled']) || !empty($sup['enabled']) ? 'checked' : '' ?>> نمایش دکمه پشتیبانی شناور در سایت</label>
                        </div>
                        <div class="form-group">
                            <label>متن پیام خوشآمد چت</label>
                            <input type="text" name="support[welcome]" value="<?= htmlspecialchars($sup['welcome'] ?? 'سلام، چطور میتونم کمک کنم؟') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>رنگ اصلی ویجت</label>
                            <input type="color" name="support[main_color]" value="<?= htmlspecialchars($sup['main_color'] ?? '#FF6F00') ?>">
                        </div>
                        <div class="form-group">
                            <label>اندازه فونت ویجت (px)</label>
                            <select name="support[font_size]">
                                <?php foreach (['13','14','15','16','17'] as $fs): ?>
                                <option value="<?= $fs ?>" <?= ($sup['font_size'] ?? '14') === $fs ? 'selected' : '' ?>><?= $fs ?>px</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <h4 style="margin:18px 0 12px;">کانالهای تماس</h4>
                    <?php
                    $channels_ui = [
                        'telegram' => ['تلگرام', '#0088cc', 'fa-brands fa-telegram', 'آیدی بدون @ — مثلا mehrsam'],
                        'eitaa'    => ['ایتا', '#E94560', 'fa-solid fa-comments', 'آیدی ایتا — مثلا mehrsam'],
                        'rubika'   => ['روبیکا', '#5F4B8B', 'fa-solid fa-comment-dots', 'آیدی روبیکا'],
                        'whatsapp' => ['واتس‌اپ', '#25d366', 'fa-brands fa-whatsapp', 'شماره با کد کشور — 98912...'],
                        'email'    => ['ایمیل', '#EA4335', 'fa-solid fa-envelope', 'ایمیل پشتیبانی'],
                        'sms'      => ['پیامک', '#16a085', 'fa-solid fa-comment-sms', 'شماره دریافت پیامک'],
                        'tel'      => ['تماس تلفنی', '#2D3436', 'fa-solid fa-phone', 'شماره تماس'],
                    ];
                    foreach ($channels_ui as $ch => $info): ?>
                    <div style="border:1px solid var(--rang-border);border-radius:10px;padding:14px;margin-bottom:10px;background:#fafbfc;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
                            <label class="checkbox-label"><input type="checkbox" name="support[channels][<?= $ch ?>][on]" value="1" <?= $supon($ch) ? 'checked' : '' ?>> <i class="<?= $info[2] ?>" style="color:<?= $info[1] ?>"></i> <b><?= $info[0] ?></b></label>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-size:12px;"><?= $ch === 'email' ? 'ایمیل' : (($ch === 'whatsapp' || $ch === 'sms' || $ch === 'tel') ? 'شماره' : 'آیدی / لینک') ?></label>
                                <input type="text" name="support[channels][<?= $ch ?>][v]" value="<?= $supc($ch, 'v') ?>" placeholder="<?= $info[3] ?>" dir="ltr">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-size:12px;">رنگ دکمه</label>
                                <input type="color" name="support[channels][<?= $ch ?>][color]" value="<?= $supcol($ch, $info[1]) ?>">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <p class="help-text">راهنما: تلگرام/ایتا/روبیکا = فقط آیدی (بدون @). واتساپ = شماره با 98. پیامک و تماس = شماره معمولی.</p>
                </div>

                <?php elseif ($active_tab === 'theme'): ?>
                <div class="settings-panel">
                    <h4 class="section-title"><i class="fa-solid fa-palette"></i> تنظیمات قالب</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label>قالب فعال</label>
                            <select name="theme[active]">
                                <option value="mehrsam" <?= ($current['theme']['active'] ?? 'mehrsam') === 'mehrsam' ? 'selected' : '' ?>>مهرسام (پیش‌فرض)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>فونت</label>
                            <select name="theme[font_family]">
                                <option value="Vazirmatn" <?= ($current['theme']['font_family'] ?? 'Vazirmatn') === 'Vazirmatn' ? 'selected' : '' ?>>وزیر متن (پیش‌فرض)</option>
                                <option value="Tahoma" <?= ($current['theme']['font_family'] ?? '') === 'Tahoma' ? 'selected' : '' ?>>تاهما</option>
                                <option value="IRANSans" <?= ($current['theme']['font_family'] ?? '') === 'IRANSans' ? 'selected' : '' ?>>ایران‌سنس</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>اندازه فونت متن (پیکسل)</label>
                            <input type="number" name="theme[body_font_size]" value="<?= (int)($current['theme']['body_font_size'] ?? 15) ?>" min="12" max="22" step="1" style="width:120px;"> <span>px</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>رنگ متن (Body Text Color)</label>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <input type="color" name="theme[body_text_color]" value="<?= htmlspecialchars($current['theme']['body_text_color'] ?? '#1a1a1a') ?>" id="bodyTextColor">
                            <span class="color-preview" id="bodyTextPreview" style="background:<?= htmlspecialchars($current['theme']['body_text_color'] ?? '#1a1a1a') ?>"></span>
                        </div>
                    </div>


                    <div class="form-row">
                        <div class="form-group">
                            <label>رنگ اصلی (Primary)</label>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <input type="color" name="theme[primary_color]" value="<?= htmlspecialchars($current['theme']['primary_color'] ?? '#FF6F00') ?>" id="primColor">
                                <span class="color-preview" id="primPreview" style="background:<?= htmlspecialchars($current['theme']['primary_color'] ?? '#FF6F00') ?>"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>رنگ هاور / تیره (Primary Hover)</label>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <input type="color" name="theme[primary_hover]" value="<?= htmlspecialchars($current['theme']['primary_hover'] ?? '#E65100') ?>" id="primHoverColor">
                                <span class="color-preview" id="primHoverPreview" style="background:<?= htmlspecialchars($current['theme']['primary_hover'] ?? '#E65100') ?>"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>رنگ ثانویه (Secondary)</label>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <input type="color" name="theme[secondary_color]" value="<?= htmlspecialchars($current['theme']['secondary_color'] ?? '#00B894') ?>" id="secColor">
                            <span class="color-preview" id="secPreview" style="background:<?= htmlspecialchars($current['theme']['secondary_color'] ?? '#00B894') ?>"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>CSS سفارشی</label>
                        <textarea name="theme[custom_css]" rows="8" placeholder="/* CSS اضافی اینجا */" style="font-family:monospace; direction:ltr;"><?= htmlspecialchars($current['theme']['custom_css'] ?? '') ?></textarea>
                        <span class="help-text">این کد مستقیماً در <head> قالبInject می‌شود</span>
                    </div>

                    <script>
                        document.getElementById('primColor').addEventListener('input', e => document.getElementById('primPreview').style.background = e.target.value);
                        document.getElementById('primHoverColor').addEventListener('input', e => document.getElementById('primHoverPreview').style.background = e.target.value);
                        document.getElementById('secColor').addEventListener('input', e => document.getElementById('secPreview').style.background = e.target.value);
                        document.getElementById('bodyTextColor').addEventListener('input', e => document.getElementById('bodyTextPreview').style.background = e.target.value);
                    </script>
                </div>

                <?php elseif ($active_tab === 'files'): ?>
                <div class="settings-panel">
                    <h4 class="section-title"><i class="fa-solid fa-folder-open"></i> تنظیمات فایل‌ها</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label>حداکثر حجم آپلود کاربر (مگابایت)</label>
                            <input type="number" name="files[max_upload_size]" value="<?= (int)((int)($current['files']['max_upload_size'] ?? 5242880) / 1048576) ?>" min="1" max="100" step="1" style="width:140px;"> <span>MB</span>
                        </div>
                        <div class="form-group">
                            <label>آپلود عمومی کاربر</label>
                            <select name="files[user_upload_enabled]">
                                <option value="0" <?= empty($current['files']['user_upload_enabled']) ? 'selected' : '' ?>>غیرفعال</option>
                                <option value="1" <?= !empty($current['files']['user_upload_enabled']) ? 'selected' : '' ?>>فعال</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>پسوندهای مجاز (با ویرگول جدا کنید)</label>
                        <input type="text" name="files[allowed_extensions]" value="<?= htmlspecialchars($current['files']['allowed_extensions'] ?? 'pdf,zip,rar,doc,docx,xls,xlsx,txt,jpg,jpeg,png,gif,webp') ?>" dir="ltr">
                        <span class="help-text">مثال: pdf,zip,rar,doc,docx,xls,xlsx,txt,jpg,jpeg,png,gif,webp</span>
                    </div>

                    <p class="help-text">مدیر (ادمین) محدودیت حجم ندارد؛ این محدودیت فقط برای کاربران عادی اعمال می‌شود.</p>
                </div>

                <?php elseif ($active_tab === 'git'): ?>
                <div class="settings-panel" id="gitPanel">
                    <h4 class="section-title"><i class="fa-brands fa-github"></i> به‌روزرسانی از گیت</h4>
                    <p class="help-text">وضعیت مخزن گیت و به‌روزرسانی پروژه از مخزن اصلی (GitHub).</p>
                    <div id="gitStatus" style="background:#f8f9fa;border-radius:8px;padding:20px;margin:20px 0;direction:ltr;text-align:left;font-family:monospace;font-size:13px;line-height:1.8;">
                        <div style="margin-bottom:8px;"><strong>در حال دریافت اطلاعات...</strong></div>
                    </div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <button onclick="gitPull()" style="padding:12px 28px;background:var(--rang-asli,#FF6F00);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;">
                            <i class="fa-solid fa-cloud-arrow-down"></i> git pull (به‌روزرسانی)
                        </button>
                        <button onclick="gitRefresh()" style="padding:12px 28px;background:#f5f6f8;border:1px solid #dde1e6;border-radius:8px;font-weight:700;cursor:pointer;">
                            <i class="fa-solid fa-rotate"></i> بررسی مجدد
                        </button>
                    </div>
                    <div id="gitOutput" style="display:none;margin-top:20px;background:#1e1e2e;color:#cdd6f4;border-radius:8px;padding:20px;direction:ltr;text-align:left;font-family:monospace;font-size:13px;line-height:1.6;white-space:pre-wrap;"></div>
                </div>
                <script>
                function gitRefresh() {
                    document.getElementById('gitStatus').innerHTML = '<div style="margin-bottom:8px;"><strong>در حال دریافت اطلاعات...</strong></div>';
                    fetch('<?= BASE_URL ?>mod/git_status')
                        .then(r => r.json())
                        .then(data => {
                            if (data.error) {
                                document.getElementById('gitStatus').innerHTML = '<div style="color:#c62828;font-weight:700;">❌ ' + data.error + '</div>';
                                return;
                            }
                            var html = '<div style="margin-bottom:8px;"><strong>شاخه (Branch):</strong> ' + data.branch + '</div>';
                            html += '<div style="margin-bottom:8px;"><strong>آخرین کامیت:</strong> ' + data.last_commit + '</div>';
                            if (data.changes) {
                                html += '<div style="margin-top:12px;padding-top:12px;border-top:1px solid #dde1e6;"><strong>تغییرات محلی:</strong><br>' + data.changes.replace(/\n/g, '<br>') + '</div>';
                            } else {
                                html += '<div style="margin-top:12px;padding-top:12px;border-top:1px solid #dde1e6;color:#2e7d32;">✅ مخزن بدون تغییرات محلی است</div>';
                            }
                            document.getElementById('gitStatus').innerHTML = html;
                        })
                        .catch(function() {
                            document.getElementById('gitStatus').innerHTML = '<div style="color:#c62828;">❌ خطا در ارتباط با سرور</div>';
                        });
                }

                function gitPull() {
                    var btn = event.target;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> در حال به‌روزرسانی...';
                    var output = document.getElementById('gitOutput');
                    output.style.display = 'block';
                    output.innerHTML = 'در حال اجرای git pull...';
                    fetch('<?= BASE_URL ?>mod/git_pull')
                        .then(r => r.json())
                        .then(data => {
                            output.innerHTML = data.output;
                            if (data.success) {
                                output.innerHTML = '<span style="color:#a6e3a1;">✅ به‌روزرسانی موفق</span>\n\n' + data.output;
                            } else {
                                output.innerHTML = '<span style="color:#f38ba8;">❌ خطا</span>\n\n' + data.output;
                            }
                            gitRefresh();
                        })
                        .catch(function() {
                            output.innerHTML = '<span style="color:#f38ba8;">❌ خطا در ارتباط با سرور</span>';
                        })
                        .finally(function() {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-down"></i> git pull (به‌روزرسانی)';
                        });
                }

                gitRefresh();
                </script>
                <?php endif; ?>

                <?php if ($active_tab !== 'git'): ?>
                <div style="margin-top:24px;">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> ذخیره تغییرات</button>
                </div>
                <?php endif; ?>
            </form>

            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'edit_content':
            require_once __DIR__ . '/../../dade/bank.php';
            require_once MASIR_RISH . 'mohtava/tarnegar/tarnegar-model.php';
            $bank = new Bank();
            $conn = $bank->getConnection();
            $id = $params[0] ?? null;
            $is_edit = false;
            $post = ['title' => '', 'slug' => '', 'content' => '', 'kholaseh' => '', 'tasvir' => '', 'type' => 'blog', 'status' => 'draft'];
            $selected_cats = [];

            if ($id) {
                $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    $post = $result->fetch_assoc();
                    $is_edit = true;
                    $selected_cats = array_column(tarnegar_get_post_categories($id), 'id');
                } else {
                    echo "مطلب پیدا نشد.";
                    $conn->close();
                    break;
                }
                $stmt->close();
            }

            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                $title   = $_POST['title'] ?? '';
                $slug    = $_POST['slug'] ?? '';
                $kholaseh = $_POST['kholaseh'] ?? '';
                $tasvir  = $_POST['tasvir'] ?? '';
                $type    = $_POST['type'] ?? 'blog';
                $status  = $_POST['status'] ?? 'draft';
                if (empty($slug)) $slug = trim(preg_replace('/[^a-zA-Z0-9\-]/', '-', $title), '-');

                if ($is_edit) {
                    $stmt = $conn->prepare("UPDATE posts SET title=?, slug=?, kholaseh=?, tasvir=?, type=?, status=? WHERE id=?");
                    $stmt->bind_param("ssssssi", $title, $slug, $kholaseh, $tasvir, $type, $status, $id);
                    $stmt->execute();
                    $stmt->close();
                    $new_id = $id;
                } else {
                    $stmt = $conn->prepare("INSERT INTO posts (title, slug, kholaseh, tasvir, type, status) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $title, $slug, $kholaseh, $tasvir, $type, $status);
                    $stmt->execute();
                    $new_id = $conn->insert_id;
                    $stmt->close();
                }

                // دسته‌بندی
                $cat_ids = $_POST['categories'] ?? [];
                $conn->query("DELETE FROM post_categories WHERE post_id = $new_id");
                if (!empty($cat_ids) && is_array($cat_ids)) {
                    $stmt2 = $conn->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)");
                    foreach ($cat_ids as $cid) {
                        $cid = (int)$cid;
                        if ($cid > 0) { $stmt2->bind_param("ii", $new_id, $cid); $stmt2->execute(); }
                    }
                    $stmt2->close();
                }

                // ساخت خودکار پوشه فایل‌های این محتوا
                require_once MASIR_RISH . 'mohtava/file/file-functions.php';
                file_create_content_folder($type, $slug ?: $new_id);
                $conn->close();
                redirect('mod/content');
                exit;
            }

            $all_cats = $conn->query("SELECT id, title FROM categories ORDER BY title")->fetch_all(MYSQLI_ASSOC);
            $conn->close();

            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            ?>
            <h3><?php echo $is_edit ? 'ویرایش مطلب' : 'ایجاد مطلب جدید'; ?></h3>

            <?php if ($is_edit): ?>
            <div style="margin:0 0 20px;padding:16px 20px;background:#fff3e0;border:1px solid #ffd9a0;border-radius:12px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
                <div>
                    <strong style="color:#b25e00;">محتوا با صفحه‌ساز ویرایش می‌شود</strong>
                    <div style="font-size:13px;color:#8a6d3b;margin-top:4px;">ویرایشگر HTML قدیمی غیرفعال شد. برای ساخت و چیدمان بلاک‌ها از صفحه‌ساز استفاده کنید.</div>
                </div>
                <a href="<?= BASE_URL ?>mod/builder/edit_post/<?= htmlspecialchars($post['type']) ?>/<?= $id ?>" class="dakmeh dakmeh-asli" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;background:var(--rang-asli,#FF6F00);color:#fff;font-weight:700;text-decoration:none;white-space:nowrap;"><i class="fa-solid fa-layer-group"></i> ویرایش با صفحه‌ساز</a>
            </div>
            <?php else: ?>
            <div style="margin:0 0 20px;padding:16px 20px;background:#eef6ff;border:1px solid #cfe3ff;border-radius:12px;font-size:13px;color:#3d5a80;">
                پس از ذخیره، از دکمه «ویرایش با صفحه‌ساز» در لیست محتواها برای افزودن بلاک‌ها استفاده کنید.
            </div>
            <?php endif; ?>
            <form method="post" id="contentForm">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin:20px 0;">
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">عنوان</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">نامک (slug)</label>
                        <input type="text" name="slug" value="<?php echo htmlspecialchars($post['slug']); ?>" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">خلاصه (برای نمایش در لیست)</label>
                        <textarea name="kholaseh" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;min-height:60px;"><?php echo htmlspecialchars($post['kholaseh'] ?? ''); ?></textarea>
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">تصویر شاخص (کد SVG یا URL)</label>
                        <input type="text" name="tasvir" value="<?php echo htmlspecialchars($post['tasvir'] ?? ''); ?>" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;" placeholder="SVG code or image URL">
                        <button type="button" onclick="document.getElementById('tasvirUpload').click()" style="margin-top:6px;padding:6px 14px;background:#f5f6f8;border:1px solid #dde1e6;border-radius:6px;cursor:pointer;font-size:13px;">آپلود تصویر</button>
                        <input type="file" id="tasvirUpload" accept="image/*" style="display:none" onchange="previewTasvir(this)">
                        <div id="tasvirPreview" style="margin-top:8px;"></div>
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">دسته‌بندی</label>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            <?php foreach ($all_cats as $c): ?>
                                <label style="display:flex;align-items:center;gap:4px;font-size:14px;cursor:pointer;">
                                    <input type="checkbox" name="categories[]" value="<?= $c['id'] ?>" <?= in_array($c['id'], $selected_cats) ? 'checked' : '' ?>>
                                    <?= htmlspecialchars($c['title']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">نوع</label>
                        <select name="type" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">
                            <option value="blog" <?php echo $post['type']=='blog' ? 'selected' : ''; ?>>وبلاگ</option>
                            <option value="maghaleh" <?php echo $post['type']=='maghaleh' ? 'selected' : ''; ?>>مقاله</option>
                            <option value="safhe" <?php echo $post['type']=='safhe' ? 'selected' : ''; ?>>برگه</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:4px;">وضعیت</label>
                        <select name="status" style="width:100%;padding:10px;border:1.5px solid #dde1e6;border-radius:8px;">
                            <option value="draft" <?php echo $post['status']=='draft' ? 'selected' : ''; ?>>پیش‌نویس</option>
                            <option value="publish" <?php echo $post['status']=='publish' ? 'selected' : ''; ?>>منتشر</option>
                        </select>
                    </div>
                </div>
                <button type="submit" style="padding:12px 32px;background:var(--rang-asli);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:15px;">ذخیره مطلب</button>
            </form>
            <script>
            function previewTasvir(input) {
                var preview = document.getElementById('tasvirPreview');
                var file = input.files && input.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" style="max-width:100px;max-height:60px;border-radius:6px;">';
                    document.querySelector('[name="tasvir"]').value = e.target.result;
                };
                reader.readAsDataURL(file);
            }
            </script>
            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'delete_content':
            $id = $params[0] ?? null;
            if ($id) {
                require_once __DIR__ . '/../../dade/bank.php';
                $bank = new Bank();
                $conn = $bank->getConnection();
                $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                $conn->close();
            }
            redirect('mod/content');
            break;

        case 'chat':
            require_once __DIR__ . '/../../mohtava/gheychat/chat-model.php';
            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            include __DIR__ . '/../../ghaleb/ghmod/gheychat/admin-chat.php';
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'messages':
            require_once __DIR__ . '/../tanzimat.php';
            require_once __DIR__ . '/../../dade/bank.php';
            $bank = new Bank();
            $conn = $bank->getConnection();
            if (($params[0] ?? '') === 'view' && !empty($params[1])) {
                $id = (int)$params[1];
                $stmt = $conn->prepare("UPDATE payam_tamas SET khande_shode = 1 WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                $stmt = $conn->prepare("SELECT * FROM payam_tamas WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $msg = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                $conn->close();
                include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
                ?>
                <h3>مشاهده پیام تماس</h3>
                <p><a href="<?= BASE_URL ?>mod/messages" style="color:var(--rang-asli,#FF6F00);">&larr; بازگشت به لیست</a></p>
                <?php if ($msg): ?>
                <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:24px;max-width:800px;">
                    <p><strong>نام:</strong> <?= htmlspecialchars($msg['nam']) ?></p>
                    <p><strong>ایمیل:</strong> <?= htmlspecialchars($msg['email'] ?? '') ?></p>
                    <p><strong>تلفن:</strong> <?= htmlspecialchars($msg['telefon'] ?? '') ?></p>
                    <p><strong>موضوع:</strong> <?= htmlspecialchars($msg['mozoo'] ?? '') ?></p>
                    <p><strong>تاریخ:</strong> <?= $msg['created_at'] ?> <span style="color:var(--rang-asli,#FF6F00);"><?= to_jalali($msg['created_at'], 'Y/m/d H:i') ?></span></p>
                    <hr style="margin:16px 0;border:none;border-top:1px solid #eef0f4;">
                    <p style="white-space:pre-wrap;line-height:1.8;"><?= htmlspecialchars($msg['payam']) ?></p>
                </div>
                <?php else: ?>
                <p>پیام یافت نشد.</p>
                <?php endif; ?>
                <?php
                include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
                break;
            }
            $result = $conn->query("SELECT id, nam, email, telefon, mozoo, khande_shode, created_at FROM payam_tamas ORDER BY id DESC");
            $messages_list = [];
            if ($result) while ($row = $result->fetch_assoc()) $messages_list[] = $row;
            $conn->close();
            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            ?>
            <h3>پیام‌های تماس با ما</h3>
            <p style="color:#888;margin-bottom:16px;">پیام‌های ارسالی از فرم تماس با ما</p>
            <table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse:collapse;">
                <tr style="background:#f8f9fa;"><th>نام</th><th>ایمیل</th><th>تلفن</th><th>موضوع</th><th>وضعیت</th><th>تاریخ</th><th>عملیات</th></tr>
                <?php if (empty($messages_list)): ?>
                <tr><td colspan="7" style="text-align:center;padding:32px;color:#888;">پیامی دریافت نشده است</td></tr>
                <?php else: foreach ($messages_list as $m): ?>
                <tr style="<?= $m['khande_shode'] ? '' : 'background:#fff8f0;font-weight:600;' ?>">
                    <td><?= htmlspecialchars($m['nam']) ?></td>
                    <td><?= htmlspecialchars($m['email'] ?? '') ?></td>
                    <td dir="ltr"><?= htmlspecialchars($m['telefon'] ?? '') ?></td>
                    <td><?= htmlspecialchars($m['mozoo'] ?? '') ?></td>
                    <td><?= $m['khande_shode'] ? '<span style="color:#2e7d32;">خوانده شده</span>' : '<span style="color:#e65100;">جدید</span>' ?></td>
                    <td style="font-size:12px;color:#888;"><?= $m['created_at'] ?><br><span style="color:var(--rang-asli,#FF6F00);"><?= to_jalali($m['created_at'], 'Y/m/d H:i') ?></span></td>
                    <td><a href="<?= BASE_URL ?>mod/messages/view/<?= $m['id'] ?>">مشاهده</a></td>
                </tr>
                <?php endforeach; endif; ?>
            </table>
            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'chat_view':
            require_once __DIR__ . '/../../mohtava/gheychat/chat-model.php';
            $chat_session = chat_get_session($params[0] ?? 0);
            $chat_messages = $chat_session ? chat_get_all_messages($chat_session['id']) : [];
            include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
            include __DIR__ . '/../../ghaleb/ghmod/gheychat/admin-chat-view.php';
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'chat_poll_admin':
            require_once __DIR__ . '/../../mohtava/gheychat/chat-model.php';
            header('Content-Type: application/json');
            $session_id = (int)($params[0] ?? 0);
            $since_id = (int)($_GET['since'] ?? 0);
            $messages = $session_id ? chat_get_messages($session_id, $since_id) : [];
            echo json_encode(['messages' => $messages]);
            break;

        case 'chat_reply':
            require_once __DIR__ . '/../../mohtava/gheychat/chat-model.php';
            $session_id = $params[0] ?? 0;
            $message = $_POST['message'] ?? '';
            if ($session_id && $message) {
                chat_send_message($session_id, 'admin', $message);
            }
            redirect('mod/chat_view/' . $session_id);
            break;

        case 'chat_close':
            require_once __DIR__ . '/../../mohtava/gheychat/chat-model.php';
            $session_id = $params[0] ?? 0;
            if ($session_id) chat_close_session($session_id);
            redirect('mod/chat');
            break;

        case 'chat_delete':
            require_once __DIR__ . '/../../mohtava/gheychat/chat-model.php';
            $session_id = $params[0] ?? 0;
            if ($session_id) chat_delete_session($session_id);
            redirect('mod/chat');
            break;

        case 'menu_editor':
            require_once MASIR_RISH . 'mohtava/menu/menu-editor.php';
            $menu_type = $params[0] ?? 'site';
            if ($menu_type === 'admin') {
                menu_editor_admin();
            } else {
                menu_editor_site();
            }
            break;

        case 'logout':
            if (isset($_COOKIE['rid'])) setcookie('rid', '', time() - 3600, '/');
            if (isset($_COOKIE['rtok'])) setcookie('rtok', '', time() - 3600, '/');
            session_destroy();
            redirect('mod/lomod');
            break;

        case 'store':
            require_once MASIR_RISH . 'mohtava/forushgah/admin-store.php';
            $store_action = $params[0] ?? '';
            $store_params = array_slice($params, 1);
            admin_store_route($store_action, $store_params);
            break;

        case 'push':
            require_once MASIR_RISH . 'afzuneh/elpayaagh/push-admin.php';
            push_route($params[0] ?? '', $params);
            break;

        case 'theme':
            require_once MASIR_RISH . 'mohtava/ghaleb/admin-theme.php';
            $theme_action = $params[0] ?? '';
            $theme_params = array_slice($params, 1);
            admin_theme_route($theme_action, $theme_params);
            break;

        case 'builder':
            require_once MASIR_RISH . 'mohtava/sakhtar/builder.php';
            $builder_action = $params[0] ?? '';
            $builder_params = array_slice($params, 1);
            if ($builder_action === 'save') {
                builder_save_blocks();
            } elseif ($builder_action === 'clear_cache') {
                builder_clear_cache($builder_params[0] ?? 0);
            } elseif ($builder_action === 'render_blocks') {
                builder_render_blocks_api();
            } elseif ($builder_action === 'upload_image') {
                builder_upload_image();
            } elseif ($builder_action === 'list_images') {
                builder_list_images();
            } elseif ($builder_action === 'save_settings') {
                builder_save_settings();
            } else {
                builder_route($builder_action, $builder_params);
            }
            break;

        case 'files':
            require_once MASIR_RISH . 'mohtava/file/file-manager.php';
            $files_action = $params[0] ?? '';
            $files_params = array_slice($params, 1);
            admin_files_route($files_action, $files_params);
            break;

        case 'upload':
            require_once MASIR_RISH . 'mohtava/file/file-manager.php';
            public_upload_route();
            break;


        case 'backup':
            require_once MASIR_RISH . 'mohtava/poshtyban/backup-admin.php';
            $backup_action = $params[0] ?? '';
            admin_backup_route($backup_action);
            break;

        case 'update':
            require_once MASIR_RISH . 'mohtava/poshtyban/update-admin.php';
            $update_action = $params[0] ?? '';
            admin_update_route($update_action);
            break;


        case 'git_pull':
            header('Content-Type: application/json');
            $git_check = mod_git_environment();
            if (!empty($git_check['error'])) {
                echo json_encode(['success' => false, 'output' => $git_check['error']]);
                exit;
            }
            $output = [];
            $return_var = 0;
            $git_dir = MASIR_RISH;
            chdir($git_dir);
            exec('git pull origin main 2>&1', $output, $return_var);
            if ($return_var !== 0) {
                exec('git pull origin master 2>&1', $output, $return_var);
            }
            $out_text = implode("\n", $output);
            if ($return_var !== 0 && stripos($out_text, 'could not read') !== false) {
                $out_text .= "\n\nنکته: گیت به اکانت GitHub شما دسترسی ندارد (SSH key / توکن).";
            }
            echo json_encode([
                'success' => $return_var === 0,
                'output' => $out_text,
            ]);
            exit;

        case 'git_status':
            header('Content-Type: application/json');
            $git_check = mod_git_environment();
            if (!empty($git_check['error'])) {
                echo json_encode(['branch' => '—', 'last_commit' => '—', 'changes' => '', 'error' => $git_check['error']]);
                exit;
            }
            $output = [];
            $git_dir = MASIR_RISH;
            chdir($git_dir);
            exec('git status --short 2>&1', $output);
            $changes = implode("\n", $output);
            exec('git log -1 --oneline 2>&1', $log_output);
            $last_commit = $log_output[0] ?? 'N/A';
            exec('git rev-parse --abbrev-ref HEAD 2>&1', $branch_output);
            $branch = $branch_output[0] ?? 'N/A';
            echo json_encode([
                'branch' => $branch,
                'last_commit' => $last_commit,
                'changes' => $changes,
                'error' => '',
            ]);
            exit;

        default:
            redirect('mod/dashmod');
            break;
    }
}
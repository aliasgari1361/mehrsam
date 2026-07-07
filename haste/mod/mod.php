<?php
/**
 * کنترلر پنل مدیریت
 * لاگین، داشبورد، تنظیمات، مدیریت محتوا با ویرایشگر کامل
 */

function mod_route($action, $params) {
    // CAPTCHA route - must be at the top before any output (no auth required)
    if ($action === 'captcha') {
        require_once __DIR__ . '/../captcha.php';
        display_captcha_image();
        return;
    }

    if (!isLoggedIn() || !isAdmin()) {
        if ($action === 'lomod') {
            $error = '';
            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                $captcha_input = $_POST['captcha'] ?? '';

                // Verify CAPTCHA
                require_once __DIR__ . '/../captcha.php';
                if (!verify_captcha($captcha_input)) {
                    $error = "کد امنیتی اشتباه است.";
                } else {
                    require_once __DIR__ . '/../../dade/bank.php';
                    $bank = new Bank();
                    $conn = $bank->getConnection();

                    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ? AND role = 'admin'");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows === 1) {
                        $user = $result->fetch_assoc();
                        if (password_verify($password, $user['password'])) {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['role'] = $user['role'];
                            redirect('mod/dashmod');
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

            }

            include __DIR__ . '/../../ghaleb/ghmod/lomod.php';
            return;
        }
        redirect('mod/lomod');
    }

    // نگاشت روت‌های میانبر به تب‌های تنظیمات سایت
    $tab_routes = [
        'theme'    => 'theme',
        'store'    => 'store',
        'gateways' => 'gateways',
    ];
    if (isset($tab_routes[$action])) {
        $_GET['tab'] = $tab_routes[$action];
        $action = 'settings';
    }

    switch ($action) {

        case 'dashmod':
    $onvan_safhe = 'داشبورد مدیریت';
    $meta_sharh = 'پنل مدیریت سایت';
    require_once __DIR__ . '/../../dade/bank.php';
    $bank = new Bank();
    $conn = $bank->getConnection();

    $posts_count    = $conn->query("SELECT COUNT(*) AS cnt FROM posts")->fetch_assoc()['cnt'] ?? 0;
    $pages_count    = $conn->query("SELECT COUNT(*) AS cnt FROM posts WHERE type='safhe'")->fetch_assoc()['cnt'] ?? 0;
    $articles_count = $conn->query("SELECT COUNT(*) AS cnt FROM posts WHERE type='maghaleh'")->fetch_assoc()['cnt'] ?? 0;
    $users_count    = $conn->query("SELECT COUNT(*) AS cnt FROM users")->fetch_assoc()['cnt'] ?? 0;
    $services_count = $conn->query("SELECT COUNT(*) AS cnt FROM khadamat WHERE vaziat=1")->fetch_assoc()['cnt'] ?? 0;

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

    <?php include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php'; ?>
    <style>
    .dash { font-family:system-ui,sans-serif; direction:rtl; }
    .dash h3 { font-size:1.4rem; margin-bottom:4px; color:#1a1a1a; }
    .dash .sub { color:#666; font-size:13px; margin-bottom:24px; }
    .dash-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:16px; margin-bottom:32px; }
    .dash-card { background:#fff; border-radius:12px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #eef0f4; display:flex; align-items:center; gap:16px; transition:all 0.2s; }
    .dash-card:hover { box-shadow:0 4px 16px rgba(0,0,0,0.08); transform:translateY(-2px); }
    .dash-card .icon { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;flex-shrink:0; }
    .dash-card .info { flex:1; min-width:0; }
    .dash-card .num { font-size:1.5rem; font-weight:700; line-height:1.2; }
    .dash-card .lbl { font-size:12px; color:#888; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .dash-card .badge { display:inline-block; font-size:10px; padding:2px 8px; border-radius:10px; font-weight:600; }
    .dash-row { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:32px; }
    .dash-panel { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #eef0f4; overflow:hidden; }
    .dash-panel h4 { font-size:14px; padding:16px 20px; margin:0; border-bottom:1px solid #eef0f4; display:flex; align-items:center; gap:8px; }
    .dash-panel .empty { padding:32px; text-align:center; color:#aaa; font-size:13px; }
    .dash-panel table { width:100%; border-collapse:collapse; }
    .dash-panel th { text-align:right; padding:10px 16px; font-size:12px; color:#888; font-weight:600; border-bottom:1px solid #eef0f4; }
    .dash-panel td { padding:10px 16px; font-size:13px; border-bottom:1px solid #f5f6f8; }
    .dash-panel tr:last-child td { border-bottom:none; }
    .dash-panel td a { color:var(--rang-asli,#FF6F00); text-decoration:none; }
    .dash-panel td a:hover { text-decoration:underline; }
    .status-b { display:inline-block; padding:2px 10px; border-radius:10px; font-size:11px; font-weight:600; }
    .status-b.pending { background:#fff3e0; color:#e65100; }
    .status-b.processing { background:#e3f2fd; color:#1565c0; }
    .status-b.publish { background:#e8f5e9; color:#2e7d32; }
    .status-b.draft { background:#f5f5f5; color:#757575; }
    .dash-actions { display:flex; flex-wrap:wrap; gap:8px; }
    .dash-actions a { display:inline-flex;align-items:center;gap:6px; padding:10px 18px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; transition:all 0.2s; background:#f5f6f8; color:#333; }
    .dash-actions a:hover { background:var(--rang-asli,#FF6F00); color:#fff; transform:translateY(-1px); }
    @media (max-width:768px) { .dash-row { grid-template-columns:1fr; } }
    </style>

    <div class="dash">
    <h3>داشبورد مدیریت</h3>
    <p class="sub">خلاصه وضعیت سایت — <?= date('Y/m/d') ?></p>

    <div class="dash-grid">
        <div class="dash-card"><div class="icon" style="background:#FF6F00;"><i class="fa-solid fa-file-lines"></i></div><div class="info"><div class="num"><?= $posts_count ?></div><div class="lbl">کل مطالب</div></div></div>
        <div class="dash-card"><div class="icon" style="background:#E65100;"><i class="fa-solid fa-copy"></i></div><div class="info"><div class="num"><?= $pages_count ?></div><div class="lbl">برگه‌ها</div></div></div>
        <div class="dash-card"><div class="icon" style="background:#BF360C;"><i class="fa-solid fa-newspaper"></i></div><div class="info"><div class="num"><?= $articles_count ?></div><div class="lbl">مقالات</div></div></div>
        <div class="dash-card"><div class="icon" style="background:#6C5CE7;"><i class="fa-solid fa-headset"></i></div><div class="info"><div class="num"><?= $services_count ?></div><div class="lbl">خدمات فعال</div></div></div>
        <div class="dash-card"><div class="icon" style="background:#00B894;"><i class="fa-solid fa-cube"></i></div><div class="info"><div class="num"><?= $products_count ?></div><div class="lbl">محصولات</div></div></div>
        <div class="dash-card"><div class="icon" style="background:#0984E3;"><i class="fa-solid fa-tags"></i></div><div class="info"><div class="num"><?= $categories_count ?></div><div class="lbl">دسته‌بندی‌ها</div></div></div>
        <div class="dash-card"><div class="icon" style="background:#E17055;"><i class="fa-solid fa-shopping-cart"></i></div><div class="info"><div class="num"><?= $orders_count ?> <span class="badge" style="background:#fff3e0;color:#e65100;"><?= $orders_pending ?> در انتظار</span></div><div class="lbl"><?= $orders_processing ?> در حال پردازش</div></div></div>
        <div class="dash-card"><div class="icon" style="background:#FDCB6E;color:#333;"><i class="fa-solid fa-coin"></i></div><div class="info"><div class="num"><?= number_format($orders_revenue) ?></div><div class="lbl">تومان فروش</div></div></div>
        <div class="dash-card"><div class="icon" style="background:#00B894;"><i class="fa-solid fa-comment-dots"></i></div><div class="info"><div class="num"><?= $messages_count ?></div><div class="lbl">پیام‌های تماس</div></div></div>
        <div class="dash-card"><div class="icon" style="background:#6C5CE7;"><i class="fa-solid fa-comments"></i></div><div class="info"><div class="num"><?= $chat_active ?> <span class="badge" style="background:#ffe0e0;color:#c62828;"><?= $chat_unread ?> جدید</span></div><div class="lbl">چت فعال</div></div></div>
        <div class="dash-card"><div class="icon" style="background:#0984E3;"><i class="fa-solid fa-users"></i></div><div class="info"><div class="num"><?= $users_count ?></div><div class="lbl">کاربران</div></div></div>
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
            <h4><i class="fa-solid fa-envelope" style="color:#00B894;"></i> آخرین پیام‌های تماس</h4>
            <?php if ($recent_messages && $recent_messages->num_rows > 0): ?>
            <table><thead><tr><th>نام</th><th>موضوع</th><th>تاریخ</th></tr></thead><tbody>
            <?php while ($m = $recent_messages->fetch_assoc()): ?>
            <tr><td><?= htmlspecialchars($m['nam']) ?></td><td><?= htmlspecialchars($m['mozoo'] ?? '—') ?></td><td style="font-size:11px;color:#888;"><?= $m['created_at'] ?></td></tr>
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
        <a href="<?= BASE_URL ?>mod/content"><i class="fa-solid fa-plus"></i> مطلب جدید</a>
        <a href="<?= BASE_URL ?>mod/pages"><i class="fa-solid fa-file"></i> برگه جدید</a>
        <a href="<?= BASE_URL ?>mod/services"><i class="fa-solid fa-headset"></i> خدمات</a>
        <a href="<?= BASE_URL ?>mod/chat"><i class="fa-solid fa-comment-dots"></i> چت</a>
        <a href="<?= BASE_URL ?>mod/theme"><i class="fa-solid fa-palette"></i> مدیریت قالب</a>
        <a href="<?= BASE_URL ?>mod/store"><i class="fa-solid fa-store"></i> مدیریت فروشگاه</a>
        <a href="<?= BASE_URL ?>mod/gateways"><i class="fa-solid fa-credit-card"></i> درگاه‌ها</a>
        <a href="<?= BASE_URL ?>mod/settings"><i class="fa-solid fa-gear"></i> تنظیمات سایت</a>
    </div>
    </div>
<?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
    break;

        case 'content':
            require_once __DIR__ . '/../../dade/bank.php';
            $bank = new Bank();
            $conn = $bank->getConnection();
            $result = $conn->query("SELECT id, title, slug, type, status, created_at FROM posts WHERE type IN ('blog','maghaleh') ORDER BY created_at DESC");
            $posts_list = [];
            if ($result) while ($row = $result->fetch_assoc()) $posts_list[] = $row;
            $conn->close();

            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
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
                    <td><?php echo $p['created_at']; ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>mod/edit_content/<?php echo $p['id']; ?>">ویرایش</a> |
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

            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
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
                } else {
                    $stmt = $conn->prepare("INSERT INTO posts (title, slug, content, type, template, status) VALUES (?, ?, ?, 'page', ?, ?)");
                    $stmt->bind_param("sssss", $title, $slug, $content, $template, $status);
                }
                $stmt->execute();
                $stmt->close();
                $conn->close();
                redirect('mod/pages');
                exit;
            }
            $conn->close();

            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
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
            require_once __DIR__ . '/../mod/services.php';
            admin_services_route($params[0] ?? 'list', $params);
            break;

        case 'panel_settings':
            require_once __DIR__ . '/../settings.php';
            $admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true) ?: ['bg_color' => '#f0f2f5', 'font' => 'Tahoma', 'favicon' => ''];
            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                $new_settings = [
                    'bg_color' => $_POST['bg_color'] ?? '#f0f2f5',
                    'font'     => $_POST['font'] ?? 'Tahoma',
                    'favicon'  => $_POST['favicon'] ?? ''
                ];
                save_admin_settings($new_settings);
                $admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true) ?: $new_settings;
                $message = "تنظیمات پنل ذخیره شد.";
            }
            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
            ?>
            <h3>تنظیمات پنل مدیریت</h3>
            <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
            <form method="post">
                <label>رنگ پس‌زمینه:</label>
                <input type="color" name="bg_color" value="<?php echo $admin_settings['bg_color']; ?>"><br><br>

                <label>فونت:</label>
                <select name="font">
                    <option value="Tahoma" <?php if($admin_settings['font']=='Tahoma') echo 'selected'; ?>>Tahoma</option>
                    <option value="Arial" <?php if($admin_settings['font']=='Arial') echo 'selected'; ?>>Arial</option>
                    <option value="Vazir" <?php if($admin_settings['font']=='Vazir') echo 'selected'; ?>>وزیر</option>
                </select><br><br>

                <button type="submit">ذخیره</button>
            </form>
            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'settings':
            require_once __DIR__ . '/../site_settings.php';
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
                foreach (['general', 'social', 'theme', 'store', 'gateways'] as $section) {
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
                'general'   => 'عمومی',
                'social'    => 'شبکه‌ها',
                'theme'     => 'قالب',
                'store'     => 'فروشگاه',
                'gateways'  => 'درگاه‌ها',
            ];

            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
            ?>
            <style>
                .settings-tabs { display:flex; gap:4px; margin-bottom:20px; border-bottom:2px solid var(--rang-border); padding-bottom:4px; }
                .settings-tabs a { padding:10px 20px; border-radius:8px 8px 0 0; text-decoration:none; font-weight:600; color:var(--rang-gray); background:#f8f9fa; border:1px solid var(--rang-border); border-bottom:none; transition:all .2s; }
                .settings-tabs a:hover { background:#eef; color:var(--rang-asli); }
                .settings-tabs a.active { background:#fff; color:var(--rang-asli); border-color:var(--rang-asli); border-bottom:2px solid #fff; margin-bottom:-2px; }
                .settings-panel { background:#fff; border:1px solid var(--rang-border); border-radius:12px; padding:24px; }
                .form-group { margin-bottom:20px; }
                .form-group label { display:block; margin-bottom:6px; font-weight:600; color:var(--rang-matn); }
                .form-group input[type=text], .form-group input[type=email], .form-group input[type=tel], .form-group input[type=url], .form-group input[type=number], .form-group select, .form-group textarea { width:100%; padding:12px; border:1.5px solid var(--rang-border); border-radius:8px; font-family:inherit; font-size:1rem; box-sizing:border-box; }
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
            <p style="color:var(--rang-gray); margin-bottom:24px;">مدیریت تنظیمات عمومی، ظاهر، فروشگاه و درگاه‌های پرداخت</p>
            <?php if (isset($message)) echo $message; ?>

            <div class="settings-tabs">
                <?php foreach ($tabs as $key => $label): ?>
                    <a href="?tab=<?= $key ?>" class="<?= $active_tab === $key ? 'active' : '' ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </div>

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
                    </script>
                </div>

                <?php elseif ($active_tab === 'store' || $active_tab === 'gateways'): ?>

                <?php if ($active_tab === 'store'): ?>
                <div class="settings-panel">
                    <h4 class="section-title"><i class="fa-solid fa-store"></i> تنظیمات فروشگاه</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label>واحد پول (نمایش)</label>
                            <input type="text" name="store[currency]" value="<?= htmlspecialchars($current['store']['currency'] ?? 'تومان') ?>">
                        </div>
                        <div class="form-group">
                            <label>نماد ارز</label>
                            <input type="text" name="store[currency_symbol]" value="<?= htmlspecialchars($current['store']['currency_symbol'] ?? 'تومان') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>آستانه ارسال رایگان (ریال/تومان)</label>
                            <input type="number" name="store[free_shipping_threshold]" value="<?= (int)($current['store']['free_shipping_threshold'] ?? 0) ?>" min="0" step="10000">
                        </div>
                        <div class="form-group">
                            <label>هزینه ارسال پیش‌فرض</label>
                            <input type="number" name="store[default_shipping_cost]" value="<?= (int)($current['store']['default_shipping_cost'] ?? 0) ?>" min="0" step="1000">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="store[stock_management]" value="1" <?= !empty($current['store']['stock_management']) ? 'checked' : '' ?>> مدیریت موجودی (کم کردن از موجودی بعد از پرداخت)
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="store[auto_confirm_orders]" value="1" <?= !empty($current['store']['auto_confirm_orders']) ? 'checked' : '' ?>> تایید خودکار سفارش‌ها بعد از پرداخت
                        </label>
                    </div>
                </div>
                <?php endif; ?>

                <div class="settings-panel" style="margin-top:24px;">
                    <h4 class="section-title"><i class="fa-solid fa-credit-card"></i> درگاه‌های پرداخت</h4>
                    <p class="help-text">برای هر درگاه، فعال‌سازی و مشخصات را وارد کنید. درگاه‌های غیرفعال در چک‌اوت نمایش داده نمی‌شوند.</p>

                    <?php
                    $gateways = $current['gateways'] ?? [
                        'zarinpal' => ['enabled'=>false,'title'=>'زرین‌پال','merchant'=>'','sandbox'=>true],
                        'idpay'    => ['enabled'=>false,'title'=>'آی‌دی‌پی','api_key'=>'','sandbox'=>true],
                        'zibal'    => ['enabled'=>false,'title'=>'زیبال','merchant'=>'','sandbox'=>true],
                    ];
                    foreach ($gateways as $key => $gw): ?>
                    <div class="gateway-card">
                        <h4>
                            <label class="toggle-switch">
                                <input type="checkbox" name="gateways[<?= $key ?>][enabled]" value="1" <?= !empty($gw['enabled']) ? 'checked' : '' ?>> 
                                <span class="toggle-slider"></span>
                            </label>
                            <?= htmlspecialchars($gw['title'] ?? $key) ?>
                        </h4>

                        <?php if ($key === 'zarinpal'): ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label>مرچنت کد (Merchant ID)</label>
                                <input type="text" name="gateways[<?= $key ?>][merchant]" value="<?= htmlspecialchars($gw['merchant'] ?? '') ?>" dir="ltr" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                            </div>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="gateways[<?= $key ?>][sandbox]" value="1" <?= !empty($gw['sandbox']) ? 'checked' : '' ?>> حالت سندباکس (تست)
                                </label>
                            </div>
                        </div>
                        <?php elseif ($key === 'idpay'): ?>
                        <div class="form-group">
                            <label>API Key</label>
                            <input type="text" name="gateways[<?= $key ?>][api_key]" value="<?= htmlspecialchars($gw['api_key'] ?? '') ?>" dir="ltr" placeholder="YOUR_API_KEY">
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="gateways[<?= $key ?>][sandbox]" value="1" <?= !empty($gw['sandbox']) ? 'checked' : '' ?>> حالت سندباکس
                            </label>
                        </div>
                        <?php elseif ($key === 'zibal'): ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label>مرچنت کد</label>
                                <input type="text" name="gateways[<?= $key ?>][merchant]" value="<?= htmlspecialchars($gw['merchant'] ?? '') ?>" dir="ltr">
                            </div>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="gateways[<?= $key ?>][sandbox]" value="1" <?= !empty($gw['sandbox']) ? 'checked' : '' ?>> حالت سندباکس
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div style="margin-top:24px;">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> ذخیره تغییرات</button>
                </div>
            </form>

            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'edit_content':
            require_once __DIR__ . '/../../dade/bank.php';
            $bank = new Bank();
            $conn = $bank->getConnection();
            $id = $params[0] ?? null;
            $is_edit = false;
            $post = ['title' => '', 'slug' => '', 'content' => '', 'type' => 'blog', 'status' => 'draft'];

            if ($id) {
                $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    $post = $result->fetch_assoc();
                    $is_edit = true;
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
                $content = $_POST['content'] ?? '';
                $type    = $_POST['type'] ?? 'blog';
                $status  = $_POST['status'] ?? 'draft';
                if (empty($slug)) $slug = trim(preg_replace('/[^a-zA-Z0-9\-]/', '-', $title), '-');

                if ($is_edit) {
                    $stmt = $conn->prepare("UPDATE posts SET title=?, slug=?, content=?, type=?, status=? WHERE id=?");
                    $stmt->bind_param("sssssi", $title, $slug, $content, $type, $status, $id);
                } else {
                    $stmt = $conn->prepare("INSERT INTO posts (title, slug, content, type, status) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $title, $slug, $content, $type, $status);
                }
                $stmt->execute();
                $stmt->close();
                $conn->close();
                redirect('mod/content');
                exit;
            }
            $conn->close();

            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
            ?>
            <h3><?php echo $is_edit ? 'ویرایش مطلب' : 'ایجاد مطلب جدید'; ?></h3>

            <?php
            $edr_value = htmlspecialchars($post['content']);
            $edr_name = 'content';
            $edr_id = 'contentArea';
            include __DIR__ . '/../editor/editor.php';
            ?>
            <form method="post" id="contentForm">
                <label>عنوان: <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required></label><br><br>
                <label>نامک (slug): <input type="text" name="slug" value="<?php echo htmlspecialchars($post['slug']); ?>"></label><br><br>
                <label>نوع: <select name="type">
                    <option value="blog" <?php echo $post['type']=='blog' ? 'selected' : ''; ?>>وبلاگ</option>
                    <option value="safhe" <?php echo $post['type']=='safhe' ? 'selected' : ''; ?>>برگه</option>
                    <option value="maghaleh" <?php echo $post['type']=='maghaleh' ? 'selected' : ''; ?>>مقاله</option>
                </select></label><br><br>
                <label>وضعیت: <select name="status">
                    <option value="draft" <?php echo $post['status']=='draft' ? 'selected' : ''; ?>>پیش‌نویس</option>
                    <option value="publish" <?php echo $post['status']=='publish' ? 'selected' : ''; ?>>منتشر</option>
                </select></label><br><br>
                <button type="submit">ذخیره</button>
            </form>
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
            require_once __DIR__ . '/../../mohtava/chat/chat-model.php';
            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
            include __DIR__ . '/../../ghaleb/ghmod/chat/admin-chat.php';
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'chat_view':
            require_once __DIR__ . '/../../mohtava/chat/chat-model.php';
            $chat_session = chat_get_session($params[0] ?? 0);
            $chat_messages = $chat_session ? chat_get_all_messages($chat_session['id']) : [];
            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
            include __DIR__ . '/../../ghaleb/ghmod/chat/admin-chat-view.php';
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'chat_poll_admin':
            require_once __DIR__ . '/../../mohtava/chat/chat-model.php';
            header('Content-Type: application/json');
            $session_id = (int)($params[0] ?? 0);
            $since_id = (int)($_GET['since'] ?? 0);
            $messages = $session_id ? chat_get_messages($session_id, $since_id) : [];
            echo json_encode(['messages' => $messages]);
            break;

        case 'chat_reply':
            require_once __DIR__ . '/../../mohtava/chat/chat-model.php';
            $session_id = $params[0] ?? 0;
            $message = $_POST['message'] ?? '';
            if ($session_id && $message) {
                chat_send_message($session_id, 'admin', $message);
            }
            redirect('mod/chat_view/' . $session_id);
            break;

        case 'chat_close':
            require_once __DIR__ . '/../../mohtava/chat/chat-model.php';
            $session_id = $params[0] ?? 0;
            if ($session_id) chat_close_session($session_id);
            redirect('mod/chat');
            break;

        case 'chat_delete':
            require_once __DIR__ . '/../../mohtava/chat/chat-model.php';
            $session_id = $params[0] ?? 0;
            if ($session_id) chat_delete_session($session_id);
            redirect('mod/chat');
            break;

        case 'logout':
            session_destroy();
            redirect('mod/lomod');
            break;

        default:
            redirect('mod/dashmod');
            break;
    }
}
<?php
/**
 * کنترلر پنل مدیریت
 * لاگین، داشبورد، تنظیمات، مدیریت محتوا با ویرایشگر کامل
 */

function mod_route($action, $params) {
    require_once __DIR__ . '/../settings.php';

    if (!isLoggedIn() || !isAdmin()) {
        if ($action === 'lomod') {
            $error = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';

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

            include __DIR__ . '/../../ghaleb/ghmod/lomod.php';
            return;
        }
        redirect('mod/lomod');
    }

    switch ($action) {

        case 'dashmod':
    // دریافت آمار از دیتابیس برای ویجت‌ها
    require_once __DIR__ . '/../../dade/bank.php';
    $bank = new Bank();
    $conn = $bank->getConnection();
    // تعداد کل مطالب
    $posts_count = $conn->query("SELECT COUNT(*) AS cnt FROM posts")->fetch_assoc()['cnt'] ?? 0;
    // تعداد کاربران
    $users_count = $conn->query("SELECT COUNT(*) AS cnt FROM users")->fetch_assoc()['cnt'] ?? 0;
    // تعداد برگه‌ها
    $pages_count = $conn->query("SELECT COUNT(*) AS cnt FROM posts WHERE type='safhe'")->fetch_assoc()['cnt'] ?? 0;
    // تعداد مقالات
    $articles_count = $conn->query("SELECT COUNT(*) AS cnt FROM posts WHERE type='maghaleh'")->fetch_assoc()['cnt'] ?? 0;
    $conn->close();

    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>داشبورد مدیریت</h3>
    <p>خوش آمدید! خلاصه‌ای از وضعیت سایت:</p>
    <style>
        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            flex: 1;
            min-width: 150px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card .number {
            font-size: 32px;
            font-weight: bold;
            color: #343a40;
        }
        .card .label {
            color: #6c757d;
            margin-top: 5px;
        }
        .quick-links {
            margin-top: 30px;
        }
        .quick-links a {
            display: inline-block;
            margin: 5px 10px;
            padding: 8px 15px;
            background: #343a40;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
        }
        .quick-links a:hover { background: #495057; }
    </style>

    <div class="dashboard-cards">
        <div class="card">
            <div class="number"><?php echo $posts_count; ?></div>
            <div class="label">کل مطالب</div>
        </div>
        <div class="card">
            <div class="number"><?php echo $pages_count; ?></div>
            <div class="label">برگه‌ها</div>
        </div>
        <div class="card">
            <div class="number"><?php echo $articles_count; ?></div>
            <div class="label">مقالات</div>
        </div>
        <div class="card">
            <div class="number"><?php echo $users_count; ?></div>
            <div class="label">کاربران</div>
        </div>
    </div>

    <div class="quick-links">
        <a href="<?php echo BASE_URL; ?>mod/content">مدیریت محتوا</a>
        <a href="<?php echo BASE_URL; ?>mod/users">مدیریت کاربران</a>
        <a href="<?php echo BASE_URL; ?>mod/settings">تنظیمات پنل</a>
        <a href="<?php echo BASE_URL; ?>mod/site_settings">تنظیمات سایت</a>
    </div>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
    break;

        case 'settings':
    $admin_settings = get_admin_settings();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_settings = [
            'bg_color' => $_POST['bg_color'] ?? '#f0f2f5',
            'font'     => $_POST['font'] ?? 'Tahoma',
            'favicon'  => $_POST['favicon'] ?? ''
        ];
        save_admin_settings($new_settings);
        $admin_settings = get_admin_settings();
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

        case 'site_settings':
            require_once __DIR__ . '/../site_settings.php';
            global $site_settings;
            $current = $site_settings;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $new_settings = [
                    'site_title' => $_POST['site_title'] ?? 'سایت من',
                    'favicon'    => $_POST['favicon'] ?? 'ghaleb/manabe/favicon.png'
                ];
                save_site_settings($new_settings);
                global $site_settings;
                $current = $site_settings;
                $message = "تنظیمات عمومی ذخیره شد.";
            }

            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
            ?>
            <h3>تنظیمات عمومی سایت</h3>
            <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
            <form method="post">
                <label>عنوان سایت: <input type="text" name="site_title" value="<?php echo htmlspecialchars($current['site_title'] ?? ''); ?>"></label><br><br>
                <label>مسیر Favicon (نسبی): <input type="text" name="favicon" value="<?php echo htmlspecialchars($current['favicon'] ?? ''); ?>" size="50"></label>
                <small>مثال: ghaleb/manabe/favicon.png</small><br><br>
                <button type="submit">ذخیره</button>
            </form>
            <?php
            include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
            break;

        case 'content':
            require_once __DIR__ . '/../../dade/bank.php';
            $bank = new Bank();
            $conn = $bank->getConnection();
            $result = $conn->query("SELECT id, title, slug, type, status, created_at FROM posts ORDER BY created_at DESC");
            $posts = [];
            if ($result) while ($row = $result->fetch_assoc()) $posts[] = $row;
            $conn->close();

            include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
            ?>
            <h3>مدیریت محتوا</h3>
            <p><a href="<?php echo BASE_URL; ?>mod/edit_content">+ ایجاد مطلب جدید</a></p>
            <table border="1" cellpadding="5" cellspacing="0" width="100%">
                <tr><th>عنوان</th><th>نوع</th><th>وضعیت</th><th>تاریخ</th><th>عملیات</th></tr>
                <?php foreach ($posts as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['title']); ?></td>
                    <td><?php echo $p['type']; ?></td>
                    <td><?php echo $p['status']; ?></td>
                    <td><?php echo $p['created_at']; ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>mod/edit_content/<?php echo $p['id']; ?>">ویرایش</a> |
                        <a href="<?php echo BASE_URL; ?>mod/delete_content/<?php echo $p['id']; ?>" onclick="return confirm('مطمئنی؟')">حذف</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($posts)) echo "<tr><td colspan='5'>هیچ مطلبی یافت نشد.</td></tr>"; ?>
            </table>
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

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

            <!-- نوار ابزار ویرایشگر پیشرفته -->
            <style>
                .editor-toolbar { margin-bottom: 10px; }
                .editor-toolbar button { margin-right: 5px; padding: 5px 10px; }
                textarea#contentArea { width: 100%; height: 350px; font-family: monospace; direction: rtl; }
            </style>
            <div class="editor-toolbar">
                <button type="button" onclick="formatText('bold')"><b>B</b></button>
                <button type="button" onclick="formatText('italic')"><i>I</i></button>
                <button type="button" onclick="formatText('underline')"><u>U</u></button>
                |
                <button type="button" onclick="alignText('right')">راست‌چین</button>
                <button type="button" onclick="alignText('center')">وسط‌چین</button>
                <button type="button" onclick="alignText('left')">چپ‌چین</button>
                <button type="button" onclick="alignText('justify')">تراز کامل</button>
                |
                <button type="button" onclick="setDirection('rtl')">راست به چپ</button>
                <button type="button" onclick="setDirection('ltr')">چپ به راست</button>
                |
                <button type="button" onclick="insertLink()">لینک</button>
            </div>

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
                <label>محتوا:</label><br>
                <textarea name="content" id="contentArea"><?php echo htmlspecialchars($post['content']); ?></textarea><br>
                <button type="submit">ذخیره</button>
            </form>

            <script>
                function formatText(cmd) {
                    var ta = document.getElementById('contentArea');
                    var start = ta.selectionStart, end = ta.selectionEnd;
                    var selectedText = ta.value.substring(start, end);
                    var newText = '';
                    switch(cmd) {
                        case 'bold': newText = '<b>' + selectedText + '</b>'; break;
                        case 'italic': newText = '<i>' + selectedText + '</i>'; break;
                        case 'underline': newText = '<u>' + selectedText + '</u>'; break;
                    }
                    ta.setRangeText(newText, start, end, 'select');
                    ta.focus();
                }
                function alignText(align) {
                    var ta = document.getElementById('contentArea');
                    var start = ta.selectionStart, end = ta.selectionEnd;
                    var selectedText = ta.value.substring(start, end) || ' ';
                    var newText = '<div style="text-align: ' + align + ';">' + selectedText + '</div>';
                    ta.setRangeText(newText, start, end, 'select');
                    ta.focus();
                }
                function setDirection(dir) {
                    var ta = document.getElementById('contentArea');
                    var start = ta.selectionStart, end = ta.selectionEnd;
                    var selectedText = ta.value.substring(start, end) || ' ';
                    var newText = '<span dir="' + dir + '">' + selectedText + '</span>';
                    ta.setRangeText(newText, start, end, 'select');
                    ta.focus();
                }
                function insertLink() {
                    var url = prompt('آدرس لینک:');
                    if(url) {
                        var ta = document.getElementById('contentArea');
                        var start = ta.selectionStart, end = ta.selectionEnd;
                        var selectedText = ta.value.substring(start, end) || url;
                        var newText = '<a href="' + url + '">' + selectedText + '</a>';
                        ta.setRangeText(newText, start, end, 'select');
                        ta.focus();
                    }
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

        case 'logout':
            session_destroy();
            redirect('mod/lomod');
            break;

        default:
            redirect('mod/dashmod');
            break;
    }
}
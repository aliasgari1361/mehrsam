<?php
/**
 * کنترلر مدیریت خدمات
 */
require_once __DIR__ . '/../../dade/bank.php';
require_once __DIR__ . '/../settings.php';

function admin_services_route($action, $params) {
    global $admin_settings;
    $admin_settings = json_decode(file_get_contents(ADMIN_SETTINGS_FILE), true) ?: ['bg_color' => '#f0f2f5', 'font' => 'Tahoma'];
    
    $bank = new Bank();
    $conn = $bank->getConnection();

    if ($action === 'edit' || $action === '') {
        $id = $params[0] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $onvan = $_POST['onvan'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $icon = $_POST['icon'] ?? 'fa-tools';
            $sharh = $_POST['sharh_kootah'] ?? '';
            $virayesh = $_POST['virayesh'] ?? '';
            $rang = $_POST['rang'] ?? '#FF6F00';
            $meta_sharh = $_POST['meta_sharh'] ?? '';
            
            if (empty($slug)) {
                $slug = trim(preg_replace('/[^a-zA-Z0-9\-]/', '-', $onvan), '-');
            }
            
            if ($id) {
                $stmt = $conn->prepare("UPDATE khadamat SET onvan=?, slug=?, icon=?, sharh_kootah=?, virayesh=?, rang=?, meta_sharh=? WHERE id=?");
                $stmt->bind_param("sssssssi", $onvan, $slug, $icon, $sharh, $virayesh, $rang, $meta_sharh, $id);
            } else {
                $stmt = $conn->prepare("INSERT INTO khadamat (onvan, slug, icon, sharh_kootah, virayesh, rang, meta_sharh) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $onvan, $slug, $icon, $sharh, $virayesh, $rang, $meta_sharh);
            }
            $stmt->execute();
            $stmt->close();
            $conn->close();
            redirect('mod/services');
            exit;
        }
        
        $service = ['onvan' => '', 'slug' => '', 'icon' => 'fa-tools', 'sharh_kootah' => '', 'virayesh' => '', 'rang' => '#FF6F00', 'meta_sharh' => ''];
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM khadamat WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $service = $result->fetch_assoc() ?: $service;
            $stmt->close();
        }
        
        include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
        ?>
        <h3><?php echo $id ? 'ویرایش خدمت' : 'خدمت جدید'; ?></h3>
        <form method="post">
            <label>عنوان: <input type="text" name="onvan" value="<?php echo htmlspecialchars($service['onvan']); ?>" required></label><br><br>
            <label>slug: <input type="text" name="slug" value="<?php echo htmlspecialchars($service['slug']); ?>"></label><br><br>
            <label>آیکون: <input type="text" name="icon" value="<?php echo htmlspecialchars($service['icon']); ?>"></label><br><br>
            <label>توضیح: <textarea name="sharh_kootah"><?php echo htmlspecialchars($service['sharh_kootah']); ?></textarea></label><br><br>
            <label>محتوا: <textarea name="virayesh" rows="5"><?php echo htmlspecialchars($service['virayesh']); ?></textarea></label><br><br>
            <label>رنگ: <input type="color" name="rang" value="<?php echo htmlspecialchars($service['rang']); ?>"></label><br><br>
            <button type="submit">ذخیره</button>
        </form>
        <?php
        include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
        return;
    } elseif ($action === 'delete') {
        $id = $params[0] ?? null;
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM khadamat WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
        $conn->close();
        redirect('mod/services');
        return;
    }

    // لیست خدمات
    $result = $conn->query("SELECT id, onvan, slug, icon, vaziat, tartib FROM khadamat ORDER BY tartib ASC, onvan ASC");
    $services = [];
    if ($result) while ($row = $result->fetch_assoc()) $services[] = $row;
    $conn->close();

    include __DIR__ . '/../../ghaleb/ghmod/sarsafhe.php';
    ?>
    <h3>مدیریت خدمات</h3>
    <p><a href="<?php echo BASE_URL; ?>mod/services">+ خدمت جدید</a></p>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr><th>عنوان</th><th>slug</th><th>وضعیت</th><th>عملیات</th></tr>
        <?php foreach ($services as $s): ?>
        <tr>
            <td><?php echo htmlspecialchars($s['onvan']); ?></td>
            <td><?php echo htmlspecialchars($s['slug']); ?></td>
            <td><?php echo $s['vaziat'] ? 'فعال' : 'غیرفعال'; ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>mod/services/edit/<?php echo $s['id']; ?>">ویرایش</a> |
                <a href="<?php echo BASE_URL; ?>mod/services/delete/<?php echo $s['id']; ?>" onclick="return confirm('مطمئنی؟')">حذف</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}
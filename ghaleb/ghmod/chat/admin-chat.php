<?php
$sessions = chat_get_active_sessions();
$closed = chat_get_closed_sessions();
?>
<h3>مدیریت چت</h3>

<?php if (!empty($sessions)): ?>
<h4 style="margin-top:20px;">چت‌های فعال</h4>
<table border="1" cellpadding="8" cellspacing="0" width="100%" style="margin-top:10px;">
    <tr><th>نام</th><th>تلفن</th><th>وضعیت</th><th>آخرین فعالیت</th><th>عملیات</th></tr>
    <?php foreach ($sessions as $s): ?>
    <tr>
        <td><?= htmlspecialchars($s['user_name']) ?></td>
        <td dir="ltr"><?= htmlspecialchars($s['user_phone']) ?></td>
        <td><?= $s['status'] === 'waiting' ? 'منتظر' : 'فعال' ?></td>
        <td><?= $s['last_activity'] ?></td>
        <td><a href="<?= BASE_URL ?>mod/chat_view/<?= $s['id'] ?>">مشاهده</a> | <a href="<?= BASE_URL ?>mod/chat_delete/<?= $s['id'] ?>" onclick="return confirm('حذف شود؟')" style="color:#c62828;">حذف</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p style="color:#888;">هیچ چت فعالی وجود ندارد.</p>
<?php endif; ?>

<?php if (!empty($closed)): ?>
<h4 style="margin-top:30px;">تاریخچه چت‌ها</h4>
<table border="1" cellpadding="8" cellspacing="0" width="100%" style="margin-top:10px;">
    <tr><th>نام</th><th>تلفن</th><th>تاریخ</th><th>عملیات</th></tr>
    <?php foreach ($closed as $s): ?>
    <tr>
        <td><?= htmlspecialchars($s['user_name']) ?></td>
        <td dir="ltr"><?= htmlspecialchars($s['user_phone']) ?></td>
        <td><?= $s['created_at'] ?><br><span style="color:#FF6F00;"><?= function_exists('to_jalali') ? to_jalali($s['created_at'], 'Y/m/d H:i') : '' ?></span></td>
        <td><a href="<?= BASE_URL ?>mod/chat_view/<?= $s['id'] ?>">مشاهده</a> | <a href="<?= BASE_URL ?>mod/chat_delete/<?= $s['id'] ?>" onclick="return confirm('حذف شود؟')" style="color:#c62828;">حذف</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<?php include MASIR_GHALEB . 'sarfaraz.php'; ?>

<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1>سبد خرید</h1>
        <p>محصولات انتخاب‌شده شما</p>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <a href="<?= BASE_URL ?>/forushgah">فروشگاه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span>سبد خرید</span>
        </div>
    </div>
</div>

<section class="bakhsh">
    <div class="mohtava-container">

        <?php if (!empty($items)): ?>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
                <thead>
                    <tr style="background:var(--rang-sabz);">
                        <th style="padding:16px; text-align:right; border-bottom:2px solid var(--rang-border);">محصول</th>
                        <th style="padding:16px; text-align:center; border-bottom:2px solid var(--rang-border);">قیمت واحد</th>
                        <th style="padding:16px; text-align:center; border-bottom:2px solid var(--rang-border);">تعداد</th>
                        <th style="padding:16px; text-align:center; border-bottom:2px solid var(--rang-border);">مجموع</th>
                        <th style="padding:16px; text-align:center; border-bottom:2px solid var(--rang-border);"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr data-id="<?= $item['id'] ?>" style="border-bottom:1px solid var(--rang-border);">
                        <td style="padding:16px; display:flex; align-items:center; gap:12px;">
                            <div style="width:70px; height:70px; background:var(--rang-sabz); border-radius:8px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:24px; color:#ccc;">
                                <i class="fa-solid fa-box"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;"><?= htmlspecialchars($item['onvan']) ?></div>
                                <a href="<?= BASE_URL ?>/forushgah/<?= htmlspecialchars($item['slug']) ?>" style="font-size:13px; color:var(--rang-asli);">مشاهده محصول</a>
                            </div>
                        </td>
                        <td style="padding:16px; text-align:center; color:var(--rang-asli); font-weight:600;">
                            <?= number_format($item['gheymat_akhar']) ?> تومان
                        </td>
                        <td style="padding:16px; text-align:center;">
                            <div style="display:flex; align-items:center; justify-content:center; gap:8px;">
                                <button class="qty-btn" data-action="decrease" style="width:32px; height:32px; border:1px solid var(--rang-border); background:#fff; border-radius:6px; cursor:pointer; font-size:16px;">−</button>
                                <input type="number" class="qty-input" value="<?= $item['tedad'] ?>" min="1" max="99" style="width:60px; text-align:center; border:1px solid var(--rang-border); border-radius:6px; padding:4px; font-family:inherit;">
                                <button class="qty-btn" data-action="increase" style="width:32px; height:32px; border:1px solid var(--rang-border); background:#fff; border-radius:6px; cursor:pointer; font-size:16px;">+</button>
                            </div>
                        </td>
                        <td style="padding:16px; text-align:center; font-weight:600; color:var(--rang-matn);" class="item-total">
                            <?= number_format($item['majmoo']) ?> تومان
                        </td>
                        <td style="padding:16px; text-align:center;">
                            <button class="remove-btn" style="background:none; border:none; color:#c62828; cursor:pointer; font-size:18px; width:36px; height:36px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:background 0.2s;" onmouseover="this.style.background='#fdecea'" onmouseout="this.style.background='transparent'" title="حذف">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; padding-top:24px; border-top:2px solid var(--rang-border);">
            <a href="<?= BASE_URL ?>/forushgah" class="dakmeh dakmeh-khali">
                <i class="fa-solid fa-arrow-left"></i>
                ادامه خرید
            </a>

            <div style="text-align:left;">
                <div style="font-size:14px; color:#888; margin-bottom:4px;">مجموع سبد خرید</div>
                <div style="font-size:1.8rem; font-weight:700; color:var(--rang-asli);" id="cart-total">
                    <?= number_format($total) ?> تومان
                </div>
            </div>

            <a href="<?= BASE_URL ?>/forushgah/checkout" class="dakmeh dakmeh-asli" style="font-size:16px; padding:14px 32px;">
                <i class="fa-solid fa-arrow-left"></i>
                تسویه حساب
            </a>
        </div>

        <?php else: ?>
        <div style="text-align:center; padding:80px 0; color:#888;">
            <i class="fa-solid fa-cart-shopping" style="font-size:64px; margin-bottom:24px; color:#ddd; display:block;"></i>
            <h3 style="font-size:1.3rem; margin-bottom:8px;">سبد خرید شما خالی است</h3>
            <p style="margin-bottom:24px;">محصولی به سبد اضافه نکرده‌اید</p>
            <a href="<?= BASE_URL ?>/forushgah" class="dakmeh dakmeh-asli">
                <i class="fa-solid fa-store"></i>
                خرید محصول
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // افزایش تعداد
    document.querySelectorAll('.qty-btn[data-action="increase"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const input = row.querySelector('.qty-input');
            const id = row.dataset.id;
            const newVal = parseInt(input.value) + 1;
            updateQty(id, newVal, input);
        });
    });

    // کاهش تعداد
    document.querySelectorAll('.qty-btn[data-action="decrease"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const input = row.querySelector('.qty-input');
            const id = row.dataset.id;
            const newVal = Math.max(1, parseInt(input.value) - 1);
            updateQty(id, newVal, input);
        });
    });

    // تغییر مستقیم در input
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const row = this.closest('tr');
            const id = row.dataset.id;
            const val = Math.max(1, parseInt(this.value) || 1);
            this.value = val;
            updateQty(id, val, this);
        });
    });

    // حذف آیتم
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('این محصول از سبد حذف شود؟')) return;
            const row = this.closest('tr');
            const id = row.dataset.id;
            removeItem(id, row);
        });
    });

    function updateQty(itemId, qty, inputEl) {
        inputEl.disabled = true;
        fetch('<?= BASE_URL ?>/forushgah/sabad/update/' + itemId, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'tedad=' + qty
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cart-total').textContent = numberFormat(data.total) + ' تومان';
                updateItemTotal(inputEl.closest('tr'), qty);
                updateCartBadge(data.count);
            } else {
                alert(data.message || 'خطا در آپدیت');
                inputEl.value = parseInt(inputEl.value) - (qty > parseInt(inputEl.value) ? 1 : -1); // revert
            }
        })
        .catch(() => alert('خطای شبکه'))
        .finally(() => inputEl.disabled = false);
    }

    function removeItem(itemId, row) {
        fetch('<?= BASE_URL ?>/forushgah/sabad/remove/' + itemId, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                row.remove();
                document.getElementById('cart-total').textContent = numberFormat(data.total) + ' تومان';
                updateCartBadge(data.count);
                if (data.count === 0) location.reload(); // سبد خالی شد
            } else {
                alert(data.message || 'خطا در حذف');
            }
        });
    }

    function updateItemTotal(row, qty) {
        const priceText = row.querySelector('td:nth-child(2)').textContent.trim();
        const price = parseInt(priceText.replace(/,/g, ''));
        const total = price * qty;
        row.querySelector('.item-total').textContent = numberFormat(total) + ' تومان';
    }

    function updateCartBadge(count) {
        const badges = document.querySelectorAll('.cart-badge');
        badges.forEach(b => b.textContent = count);
    }

    function numberFormat(n) {
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
});
</script>

<?php include MASIR_GHALEB . 'panevis.php'; ?>
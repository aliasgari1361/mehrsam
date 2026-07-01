<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<!-- سرصفحه -->
<div class="sarsafhe-safhe">
    <div class="mohtava-container">
        <h1>تماس با ما</h1>
        <p>آماده پاسخگویی به سوالات و درخواست‌های شما هستیم</p>
        <div class="masir-nabz">
            <a href="<?= BASE_URL ?>/">خانه</a>
            <span><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></span>
            <span>تماس با ما</span>
        </div>
    </div>
</div>

<!-- محتوا -->
<section class="bakhsh">
    <div class="mohtava-container">
        <div style="display:grid; grid-template-columns:1fr 1.4fr; gap:48px; align-items:start;">

            <!-- ستون چپ: اطلاعات تماس -->
            <div>
                <h2 style="font-size:1.4rem; margin-bottom:8px;">راه‌های ارتباطی</h2>
                <p style="color:#888; margin-bottom:32px; font-size:14px;">
                    از طریق روش‌های زیر می‌توانید با ما در ارتباط باشید
                </p>

                <!-- آیتم‌های تماس -->
                <?php
                $etela = [
                    ['fa-location-dot', '#FF6F00', 'آدرس',       SITE_ADRES],
                    ['fa-phone',        '#E65100', 'تلفن',        SITE_TEL],
                    ['fa-envelope',     '#BF360C', 'ایمیل',       SITE_EMAIL],
                    ['fa-clock',        '#FF6F00', 'ساعت کاری',   'شنبه تا پنج‌شنبه: ۹ تا ۲۰'],
                ];
                foreach ($etela as $e): ?>
                <div style="display:flex; gap:16px; align-items:flex-start; margin-bottom:24px; padding:20px; background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border:1px solid #f0f0f0;">
                    <div style="width:46px; height:46px; background:<?= $e[1] ?>; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fa-solid <?= $e[0] ?>" style="color:#fff; font-size:18px;"></i>
                    </div>
                    <div>
                        <div style="font-size:12px; color:#aaa; margin-bottom:4px;"><?= $e[2] ?></div>
                        <div style="font-size:15px; font-weight:600; color:#1a1a1a;"><?= $e[3] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- شبکه‌های اجتماعی -->
                <div style="margin-top:8px;">
                    <div style="font-size:13px; color:#aaa; margin-bottom:12px;">شبکه‌های اجتماعی</div>
                    <div style="display:flex; gap:10px;">
                        <?php
                        $shabake = [
                            ['fa-instagram', '#E1306C', '#'],
                            ['fa-telegram',  '#0088cc', '#'],
                            ['fa-whatsapp',  '#25D366', '#'],
                        ];
                        foreach ($shabake as $s): ?>
                        <a href="<?= $s[2] ?>" style="width:42px; height:42px; background:<?= $s[1] ?>; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:16px; transition:transform 0.2s;"
                           onmouseover="this.style.transform='translateY(-3px)'"
                           onmouseout="this.style.transform='translateY(0)'">
                            <i class="fa-brands <?= $s[0] ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ستون راست: فرم -->
            <div style="background:#fff; border-radius:16px; padding:36px; box-shadow:0 4px 24px rgba(0,0,0,0.08); border:1px solid #f0f0f0;">

                <h2 style="font-size:1.3rem; margin-bottom:6px;">ارسال پیام</h2>
                <p style="color:#888; font-size:14px; margin-bottom:28px;">فرم زیر را تکمیل کنید</p>

                <!-- پیام موفق -->
                <?php if (!empty($payam)): ?>
                <div style="background:#e8f5e9; border:1px solid #a5d6a7; border-radius:10px; padding:16px; margin-bottom:24px; display:flex; align-items:center; gap:12px; color:#2e7d32;">
                    <i class="fa-solid fa-circle-check" style="font-size:20px;"></i>
                    <span><?= htmlspecialchars($payam) ?></span>
                </div>
                <?php endif; ?>

                <!-- خطاها -->
                <?php if (!empty($khata)): ?>
                <div style="background:#fdecea; border:1px solid #ef9a9a; border-radius:10px; padding:16px; margin-bottom:24px; color:#c62828;">
                    <?php foreach ($khata as $kh): ?>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span><?= htmlspecialchars($kh) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- فرم -->
                <form method="POST" action="<?= BASE_URL ?>/tamas">

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                        <!-- نام -->
                        <div>
                            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#444;">
                                نام <span style="color:#e53935;">*</span>
                            </label>
                            <input type="text" name="nam" placeholder="نام شما"
                                   value="<?= htmlspecialchars($_POST['nam'] ?? '') ?>"
                                   style="width:100%; padding:11px 14px; border:1.5px solid #e0e0e0; border-radius:8px; font-family:inherit; font-size:14px; outline:none; transition:border 0.2s;"
                                   onfocus="this.style.borderColor='#FF6F00'"
                                   onblur="this.style.borderColor='#e0e0e0'">
                        </div>
                        <!-- تلفن -->
                        <div>
                            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#444;">
                                شماره تماس
                            </label>
                            <input type="text" name="telefon" placeholder="۰۹۱۲-۰۰۰-۰۰۰۰"
                                   value="<?= htmlspecialchars($_POST['telefon'] ?? '') ?>"
                                   style="width:100%; padding:11px 14px; border:1.5px solid #e0e0e0; border-radius:8px; font-family:inherit; font-size:14px; outline:none; transition:border 0.2s;"
                                   onfocus="this.style.borderColor='#FF6F00'"
                                   onblur="this.style.borderColor='#e0e0e0'">
                        </div>
                    </div>

                    <!-- ایمیل -->
                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#444;">ایمیل</label>
                        <input type="email" name="email" placeholder="example@email.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               style="width:100%; padding:11px 14px; border:1.5px solid #e0e0e0; border-radius:8px; font-family:inherit; font-size:14px; outline:none; transition:border 0.2s;"
                               onfocus="this.style.borderColor='#FF6F00'"
                               onblur="this.style.borderColor='#e0e0e0'">
                    </div>

                    <!-- موضوع -->
                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#444;">موضوع</label>
                        <select name="mozoo"
                                style="width:100%; padding:11px 14px; border:1.5px solid #e0e0e0; border-radius:8px; font-family:inherit; font-size:14px; outline:none; background:#fff; cursor:pointer; transition:border 0.2s;"
                                onfocus="this.style.borderColor='#FF6F00'"
                                onblur="this.style.borderColor='#e0e0e0'">
                            <option value="">موضوع را انتخاب کنید</option>
                            <option value="پشتیبانی از راه دور"   <?= ($_POST['mozoo']??'')==='پشتیبانی از راه دور'   ?'selected':'' ?>>پشتیبانی از راه دور</option>
                            <option value="پشتیبانی حضوری"        <?= ($_POST['mozoo']??'')==='پشتیبانی حضوری'        ?'selected':'' ?>>پشتیبانی حضوری</option>
                            <option value="طراحی سایت"            <?= ($_POST['mozoo']??'')==='طراحی سایت'            ?'selected':'' ?>>طراحی سایت</option>
                            <option value="دوربین مدار بسته"      <?= ($_POST['mozoo']??'')==='دوربین مدار بسته'      ?'selected':'' ?>>دوربین مدار بسته</option>
                            <option value="سایر"                  <?= ($_POST['mozoo']??'')==='سایر'                  ?'selected':'' ?>>سایر</option>
                        </select>
                    </div>

                    <!-- پیام -->
                    <div style="margin-bottom:24px;">
                        <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#444;">
                            پیام <span style="color:#e53935;">*</span>
                        </label>
                        <textarea name="payam" rows="5" placeholder="پیام خود را بنویسید..."
                                  style="width:100%; padding:11px 14px; border:1.5px solid #e0e0e0; border-radius:8px; font-family:inherit; font-size:14px; outline:none; resize:vertical; transition:border 0.2s;"
                                  onfocus="this.style.borderColor='#FF6F00'"
                                  onblur="this.style.borderColor='#e0e0e0'"><?= htmlspecialchars($_POST['payam'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="dakmeh dakmeh-asli" style="width:100%; justify-content:center; font-size:15px; padding:14px;">
                        <i class="fa-solid fa-paper-plane"></i>
                        ارسال پیام
                    </button>

                </form>
            </div>

        </div>

        <!-- نقشه -->
        <div style="margin-top:48px; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.1); border:1px solid #e0e0e0;">
            <div style="background:var(--rang-roshan); padding:16px 24px; display:flex; align-items:center; gap:10px; border-bottom:1px solid #ffe0b2;">
                <i class="fa-solid fa-map-location-dot" style="color:var(--rang-asli); font-size:18px;"></i>
                <span style="font-weight:700; font-size:15px;">موقعیت ما روی نقشه</span>
                <span style="font-size:13px; color:#888; margin-right:8px;">ملارد – مارلیک – پاساژ ارغوان شمالی</span>
            </div>
            <iframe
                src="https://www.openstreetmap.org/export/embed.html?bbox=50.9461%2C35.6497%2C51.0061%2C35.6897&layer=mapnik&marker=35.6697%2C50.9761"
                style="width:100%; height:380px; border:none; display:block;"
                loading="lazy"
                title="موقعیت مهراد سام در ملارد">
            </iframe>
        </div>

    </div>
</section>

<style>
    @media (max-width:768px) {
        .mohtava-container > div[style*="grid-template-columns:1fr 1.4fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<?php include MASIR_GHALEB . 'panevis.php'; ?>

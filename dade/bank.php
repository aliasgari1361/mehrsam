<?php
/**
 * کلاس بانک اطلاعاتی
 * اتصال به MySQL با MySQLi
 */
class Bank {
    // مشخصات اتصال را از ثابت‌های تعریف‌شده در tanzimat.php می‌گیرد
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    public $conn; // شیء اتصال

    public function __construct() {
        // ایجاد اتصال
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        // بررسی خطای اتصال
        if ($this->conn->connect_error) {
            die("خطا در اتصال به پایگاه‌داده: " . $this->conn->connect_error);
        }

        // تنظیم کاراکتر برای پشتیبانی از فارسی
        $this->conn->set_charset("utf8mb4");
    }

    // تابع کمکی برای دریافت اتصال
    public function getConnection() {
        return $this->conn;
    }
}
?>
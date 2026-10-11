<?php
declare(strict_types=1);
const APP_NAME = 'AutoCare';
// Set this to the complete public URL of your installation (no trailing slash).
const APP_URL = '';
const DB_HOST = '127.0.0.1';
const DB_NAME = 'smart_vehicle_db';
const DB_USER = 'root';
const DB_PASS = '';
// Optional SMTP settings for password reset emails. InfinityFree requires external SMTP.
const SMTP_HOST = '';
const SMTP_PORT = 587;
const SMTP_USERNAME = '';
const SMTP_PASSWORD = '';
const SMTP_ENCRYPTION = 'tls';
const MAIL_FROM = '';
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);
session_start();
try {
 $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
} catch (PDOException $e) { http_response_code(500); exit('Database connection failed. Import database.sql and update config.php.'); }
function h(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!user()) { header('Location: index.php?page=login'); exit; } }
function require_role(string ...$roles): void { require_login(); if (!in_array(user()['role'], $roles, true)) { http_response_code(403); exit('Access denied.'); } }
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Session expired. Refresh and try again.'); } }
function money($n): string { return 'RM '.number_format((float)$n,2); }
function send_reset_email(string $to, string $subject, string $body): bool {
 try {
  $autoload=__DIR__.'/vendor/autoload.php';
  if (SMTP_HOST!=='' && is_file($autoload)) {
   require_once $autoload;
   $mail=new \PHPMailer\PHPMailer\PHPMailer(true);
   $mail->isSMTP(); $mail->Host=SMTP_HOST; $mail->Port=SMTP_PORT; $mail->SMTPAuth=true;
   $mail->Username=SMTP_USERNAME; $mail->Password=SMTP_PASSWORD; $mail->SMTPSecure=SMTP_ENCRYPTION;
   $mail->CharSet='UTF-8'; $mail->setFrom(MAIL_FROM,APP_NAME); $mail->addAddress($to);
   $mail->Subject=$subject; $mail->Body=$body; $mail->isHTML(false); $mail->send(); return true;
  }
  $headers="MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
  if (MAIL_FROM!=='') $headers.='From: '.APP_NAME.' <'.MAIL_FROM.">\r\n";
  return @mail($to,$subject,$body,$headers);
 } catch (Throwable $e) { error_log('AutoCare password reset email delivery failed: '.$e->getMessage()); return false; }
}

<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

/**
 * NotificationService — SMS (MSG91) and email (PHPMailer) notifications.
 *
 * Requirements: 9.1–9.5
 */
class NotificationService
{
    private string $smsApiKey;
    private string $smsSenderId;
    private string $smsDomain;

    public function __construct()
    {
        $this->smsApiKey   = $_ENV['SMS_API_KEY']    ?? getenv('SMS_API_KEY')    ?: '';
        $this->smsSenderId = $_ENV['SMS_SENDER_ID']  ?? getenv('SMS_SENDER_ID')  ?: 'SEMREG';
        $this->smsDomain   = $_ENV['SMS_DOMAIN']     ?? getenv('SMS_DOMAIN')     ?: 'api.msg91.com';
    }

    // -------------------------------------------------------------------------
    // OTP delivery
    // -------------------------------------------------------------------------

    /**
     * Send a 6-digit OTP to the student's mobile number via SMS.
     *
     * Requirement 9.5
     */
    public function sendOtp(string $mobile, string $otp): bool
    {
        // In development mode, store OTP in session for display instead of sending SMS
        if (defined('APP_ENV') && APP_ENV === 'development') {
            $_SESSION['dev_otp'] = $otp;
            $_SESSION['dev_otp_mobile'] = $mobile;
            error_log("DEV MODE: OTP for {$mobile} is: {$otp}");
            return true;
        }

        $message = "Your Semester Online OTP is: {$otp}. Valid for 5 minutes. Do not share.";
        return $this->sendSms($mobile, $message);
    }

    // -------------------------------------------------------------------------
    // Status update notifications
    // -------------------------------------------------------------------------

    /**
     * Notify a student of a registration or payment status change.
     *
     * Requirements: 9.2, 9.3, 9.4
     */
    public function sendStatusUpdate(int $studentId, string $status, string $message): bool
    {
        $mobile = $this->getMobileByStudentId($studentId);
        $email  = $this->getEmailByStudentId($studentId);

        $smsSent   = $mobile ? $this->sendSms($mobile, $message) : false;
        $emailSent = $email  ? $this->sendEmail($email, 'Semester Registration Update', $message) : false;

        return $smsSent || $emailSent;
    }

    // -------------------------------------------------------------------------
    // Welcome SMS
    // -------------------------------------------------------------------------

    /**
     * Send a welcome SMS after successful account creation.
     *
     * Requirement 9.1
     */
    public function sendWelcomeSms(string $mobile): bool
    {
        $message = 'Welcome to Semester Online! Your account has been created successfully.';
        return $this->sendSms($mobile, $message);
    }

    // -------------------------------------------------------------------------
    // Private — SMS via MSG91
    // -------------------------------------------------------------------------

    private function sendSms(string $mobile, string $message): bool
    {
        if (empty($this->smsApiKey)) {
            error_log('NotificationService: SMS_API_KEY not configured');
            return false;
        }

        // MSG91 Send SMS API
        $url = 'https://' . $this->smsDomain . '/api/v5/flow/';

        $payload = json_encode([
            'template_id' => '',   // set in .env for DLT-registered templates
            'short_url'   => '0',
            'mobiles'     => '91' . ltrim($mobile, '0'),
            'message'     => $message,
            'sender'      => $this->smsSenderId,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'authkey: ' . $this->smsApiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            error_log('NotificationService: SMS delivery failed for ' . $mobile);
            return false;
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // Private — Email via PHPMailer
    // -------------------------------------------------------------------------

    private function sendEmail(string $toEmail, string $subject, string $body): bool
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST']     ?? getenv('SMTP_HOST')     ?: 'localhost';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER']     ?? getenv('SMTP_USER')     ?: '';
            $mail->Password   = $_ENV['SMTP_PASS']     ?? getenv('SMTP_PASS')     ?: '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) ($_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT') ?: 587);

            $mail->setFrom(
                $_ENV['MAIL_FROM_ADDRESS'] ?? getenv('MAIL_FROM_ADDRESS') ?: 'noreply@gbu.ac.in',
                $_ENV['MAIL_FROM_NAME']    ?? getenv('MAIL_FROM_NAME')    ?: 'Semester Online'
            );
            $mail->addAddress($toEmail);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (MailerException $e) {
            error_log('NotificationService: Email delivery failed: ' . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------------------------
    // Private — DB lookups
    // -------------------------------------------------------------------------

    private function getMobileByStudentId(int $studentId): ?string
    {
        try {
            $db   = getAppDb();
            $stmt = $db->prepare('SELECT mobile FROM students WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $studentId]);
            $row = $stmt->fetch();
            return $row ? $row['mobile'] : null;
        } catch (\PDOException) {
            return null;
        }
    }

    private function getEmailByStudentId(int $studentId): ?string
    {
        try {
            $db   = getAppDb();
            $stmt = $db->prepare('SELECT email FROM students WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $studentId]);
            $row = $stmt->fetch();
            return ($row && !empty($row['email'])) ? $row['email'] : null;
        } catch (\PDOException) {
            return null;
        }
    }
}

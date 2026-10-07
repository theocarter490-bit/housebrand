<?php

namespace App\Services;

use App\Mail\CredentialErrorMail;
use App\Models\EmailSetting;
use App\Models\User;
use Exception;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{
    private ?EmailSetting $emailSetting = null;
    private ?EmailSetting $adminSetting = null;
    private array $config = [];
    private string $recipientEmail = '';
    private int $delay = 0;

    private $senderId = null;


    public function loadSettings()
    {
        try {
            $sender = User::find($this->senderId);

            if ($sender) {

                $targetUserId = $sender->supervisor_id ?? $sender->id;

                $this->emailSetting = EmailSetting::where('user_id', $targetUserId)
                    ->where('is_active', 1)
                    ->first();
            }

            // Fallback to admin settings if no client settings found
            if (!$this->emailSetting) {
                $this->emailSetting = EmailSetting::where('user_id', 1)->first();
            }

            // Load admin settings for fallback
            if ($this->emailSetting && $this->emailSetting->user_id !== 1) {
                $this->adminSetting = EmailSetting::where('user_id', 1)->first();
            }

            if (!$this->emailSetting) {
                throw new Exception('No email settings found in database');
            }

            $this->config = $this->buildConfig($this->emailSetting);
        } catch (Exception $e) {
            Log::error('Failed to load email settings: ' . $e->getMessage());
            throw $e;
        }

        return $this;
    }

    public function sender($senderId)
    {
        $this->senderId = $senderId;
        return $this;
    }

    /**
     * Set recipient email
     */
    public function to(string $email): self
    {
        $this->recipientEmail = $email;
        return $this;
    }

    /**
     * Set delay for queued emails (in seconds)
     */
    public function delay(int $seconds): self
    {
        $this->delay = $seconds;
        return $this;
    }

    /**
     * Send mail immediately
     */
    public function send(Mailable $mailData): bool
    {
        try {
            $this->loadSettings();
            $this->applyConfig($this->config);
            Mail::to($this->recipientEmail)->send($mailData);

            $this->reset();
            return true;
        } catch (Exception $e) {
            $this->reset();
            return $this->fallbackToAdmin($this->recipientEmail, $e);
        }
    }

    /**
     * Queue the email
     */
    public function queue($mailData): bool
    {
        try {
            $this->loadSettings();
            $this->applyConfig($this->config);

            if ($this->delay > 0) {
                Mail::to($this->recipientEmail)->later(now()->addSeconds($this->delay), $mailData);
            } else {
                Mail::to($this->recipientEmail)->queue($mailData);
            }

            Log::info("Email queued successfully for {$this->recipientEmail}");
            $this->reset();
            return true;
        } catch (Exception $e) {
            Log::error("Queue mail sending failed for {$this->recipientEmail}: " . $e->getMessage());
            $this->reset();
            return false;
        }
    }

    /**
     * Build SMTP configuration from email settings
     */
    private function buildConfig(EmailSetting $settings): array
    {
        return [
            'transport' => $settings->mail_driver ?? 'smtp',
            'host' => $settings->mail_host,
            'port' => $settings->mail_port,
            'encryption' => $settings->mail_encryption,
            'username' => $settings->mail_username,
            'password' => $settings->mail_password,
            'from' => [
                'address' => $settings->from_email,
                'name' => $settings->from_name,
            ],
        ];
    }

    /**
     * Apply configuration to Laravel Mail
     */
    private function applyConfig(array $config): void
    {
        Config::set('mail.mailers.dynamic_smtp', array_merge(config('mail.mailers.smtp'), $config));
        Config::set('mail.default', 'dynamic_smtp');
        Mail::forgetMailers();
    }

    /**
     * Fallback to admin SMTP if primary fails
     */
    private function fallbackToAdmin(string $recipientEmail, Exception $primaryError): bool
    {
        if (!$this->adminSetting || $this->emailSetting->user_id === 1) {
            return false;
        }

        try {
            $adminConfig = $this->buildAdminConfig();
            $this->applyConfig($adminConfig);

            $notificationData = [
                'host' => $this->emailSetting->mail_host,
                'port' => $this->emailSetting->mail_port,
                'name' => $this->emailSetting->from_name,
                'error' => $primaryError->getMessage(),
            ];

            Mail::to($this->emailSetting->from_email)
                ->send(new CredentialErrorMail($notificationData));
            return true;
        } catch (Exception $fallbackError) {
            return false;
        }
    }

    /**
     * Build admin SMTP configuration
     */
    private function buildAdminConfig(): array
    {
        return [
            'transport' => $this->adminSetting->mail_driver ?? 'smtp',
            'host' => $this->adminSetting->mail_host,
            'port' => $this->adminSetting->mail_port,
            'encryption' => $this->adminSetting->mail_encryption,
            'username' => $this->adminSetting->mail_username,
            'password' => $this->adminSetting->mail_password,
            'from' => [
                'address' => $this->adminSetting->from_email,
                'name' => 'System Alert',
            ],
        ];
    }

    /**
     * Reset state after sending
     */
    private function reset(): void
    {
        $this->recipientEmail = '';
        $this->delay = 0;
    }

    /**
     * Get current email setting
     */
    public function getSetting(): ?EmailSetting
    {
        return $this->emailSetting;
    }

    /**
     * Get current configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}

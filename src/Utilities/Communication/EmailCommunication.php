<?php

namespace MyDramLibrary\Utilities\Communication;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailCommunication implements Communication
{
    private array $recipients = array();
    private ?string $sender = null;
    private ?string $content = null;
    private ?string $subject = null;

    public function addRecipient(string $recipient)
    {
        if ($this->isValidEmail($recipient)) {
            if (!in_array($recipient, $this->recipients)) {
                $this->recipients[] = $recipient;
            }
        } else {
            throw new Exception('Invalid email address');
        }
    }

    public function setSender(string $sender)
    {
        if ($this->isValidEmail($sender)) {
            if ($this->sender) {
                throw new Exception('Sender already set');
            } else {
                $this->sender = $sender;
            }
        } else {
            throw new Exception('Invalid email address');
        }
    }

    public function setSubject(string $subject)
    {
        if ($this->subject) {
            throw new Exception('Subject already set');
        } else {
            $this->subject = $subject;
        }
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }

    public function send(): void
    {
        if (!$this->hasRecipients()) {
            throw new Exception('Recipient(s) not set');
        }
        if (!$this->hasSender()) {
            throw new Exception('Sender not set');
        }
        if (!$this->hasContent()) {
            throw new Exception('Content not set');
        }
        if (!$this->hasSubject()) {
            throw new Exception('Subject not set');
        }
        $mail = new PHPMailer(true);
        try {
            //$mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER; //Enable verbose debug output
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->Port = 25;

            $mail->setFrom($this->getSender());
            $recipients = $this->getRecipients();
            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient);
            }

            $mail->isHTML();
            $mail->Subject = $this->getSubject();
            $mail->Body    = $this->getContent();

            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            throw new Exception('Error sending email');
        }
    }

    public function getRecipients(): array
    {
        if ($this->hasRecipients()) {
            return $this->recipients;
        } else {
            throw new Exception('Recipient not set');
        }
    }

    public function getSender(): string
    {
        if ($this->hasSender()) {
            return $this->sender;
        } else {
            throw new Exception('Sender not set');
        }
    }

    public function getSubject(): string
    {
        if ($this->hasSubject()) {
            return $this->subject;
        } else {
            throw new Exception('Subject not set');
        }
    }

    public function getContent(): string
    {
        if ($this->hasContent()) {
            return $this->content;
        } else {
            throw new Exception('Content not set');
        }
    }

    private function isValidEmail(string $email): bool
    {
        return (filter_var($email, FILTER_VALIDATE_EMAIL)) ? true : false;
    }

    private function hasRecipients(): bool
    {
        return (bool) count($this->recipients);
    }

    private function hasSender(): bool
    {
        return $this->sender ? true : false;
    }

    private function hasSubject(): bool
    {
        return $this->subject ? true : false;
    }

    private function hasContent(): bool
    {
        return $this->content ? true : false;
    }
}

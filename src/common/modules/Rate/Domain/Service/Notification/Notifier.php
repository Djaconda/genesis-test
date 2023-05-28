<?php

namespace Rate\Domain\Service\Notification;

use Exception;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;
use yii\web\ServerErrorHttpException;

/**
 * Represents service for sending current Rate notifications
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
final readonly class Notifier {
    private MessageInterface $message;

    public function __construct(private MailerInterface $mailer) {
    }

    public function notify(iterable $emails, float $rate): void {
        $this->prepateMessage($rate);

        foreach ($emails as $email) {
            $this->sent($email);
        }
    }

    private function sent(string $email): void {
        $this->message->setTo($email);

        try {
            if (!$this->message->send()) {
                throw new ServerErrorHttpException('Email wasn\'t sent');
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    private function prepateMessage(float $rate) {
        $this->message = $this->mailer->compose()
                                      ->setSubject('Current rate')
                                      ->setHtmlBody('<b>' . $rate . '</b>');
    }
}

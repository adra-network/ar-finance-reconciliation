<?php

namespace Account\TransactionAlertSystem\Notifications;

use Illuminate\Bus\Queueable;
use Account\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TransactionsLate80Days extends Notification
{
    use Queueable;

    /** @var Collection */
    public $transactions;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Collection $transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)->line('List of late transactions (80 days):');

        /** @var Transaction $transaction */
        foreach ($this->transactions as $transaction) {
            $line = sprintf(
                '%s - %s - %s (%s)',
                $transaction->transaction_date,
                $transaction->code,
                $transaction->reference,
                ($transaction->credit_amount > 0 ? '-$'.number_format($transaction->credit_amount, 2) : '$'.number_format($transaction->debit_amount, 2))
            );

            $message->line($line);
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

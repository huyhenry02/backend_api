<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Mailable $mailable,
        public array    $email = [],
        public array    $cc = [],
        public array    $bcc = [],
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mail = Mail::to($this->email);
        if (isset($this->cc)) {
            $mail->cc($this->cc);
        }
        if (isset($this->bcc)) {
            $mail->bcc($this->bcc);
        }
        $mail->send($this->mailable);
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Mail\Mailer;


class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $to;
    protected $subject;
    protected $from;
    protected $data;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $subject, $data)
    {
      $this->to = $to;
      $this->subject = $subject;
      $this->from = 'system@slm.com';
      $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
     public function handle(Mailer $mailer)
     {
         $mailer->send('emails.league-invitation', ['data'=> $this->data], function ($message) {
             $message->to($this->to)->subject($this->subject);

         });
			
     }
}

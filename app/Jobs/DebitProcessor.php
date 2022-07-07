<?php

namespace App\Jobs;

use App\Notifications\SendBilletNotification;
use Illuminate\Bus\Queueable;
use App\Services\BilletService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DebitProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $record;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $record)
    {
        $this->record = $record;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BilletService $billetService)
    {
        //generate billet
        $billet = $billetService->generate($this->record);

        //send mail
        $sent = (new SendBilletNotification($billet))->toMail($this->record['email']);
    }
}

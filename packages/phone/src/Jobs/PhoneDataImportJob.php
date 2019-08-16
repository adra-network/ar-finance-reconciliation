<?php

namespace Phone\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Phone\Services\PhoneDataImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PhoneDataImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var User
     */
    protected $user;

    /**
     * PhoneDataImportJob constructor.
     * @param string $filename
     */
    public function __construct(User $user, string $filename)
    {
        $this->user = $user;
        $this->filename = $filename;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $phoneDataImportService = new PhoneDataImportService();
        $phoneDataImportService->importPhoneDataFromFile($this->filename);
    }
}

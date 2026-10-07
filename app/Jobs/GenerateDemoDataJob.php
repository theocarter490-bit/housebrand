<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\DemoDataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDemoDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Laravel will automatically inject the DemoDataService here
     */
    public function handle(DemoDataService $demoService): void
    {
        $demoService->generateInitialDataForDesigner($this->user);
    }
}

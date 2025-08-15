<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class CleanExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired personal access tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredTokens = PersonalAccessToken::where('expires_at', '<', now())->count();
        
        PersonalAccessToken::where('expires_at', '<', now())->delete();
        
        $this->info("Cleaned {$expiredTokens} expired tokens.");
        
        return 0;
    }
}

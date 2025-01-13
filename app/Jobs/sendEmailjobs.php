<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\NewRestaurantNotification;
use App\Models\User;

class sendEmailjobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
      protected $restaurant;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($restaurant)
    {
        $this->restaurant=$restaurant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $users = User::where('role', 'customer')->get();
        foreach ($users as $user) {
            $retryCount = 0;
            $maxRetries = 5;
            $delay = 1; // start with a 1 second delay

            while ($retryCount < $maxRetries) {
                try {
                    $user->notify(new NewRestaurantNotification($user, $this->restaurant));
                    break; // If successful, break out of the retry loop
                } catch (\Exception $e) {
                    $retryCount++;
                    \Log::error('Failed to send notification', ['user' => $user->id, 'error' => $e->getMessage()]);

                    // Wait for the delay before retrying
                    sleep($delay);
                    $delay *= 2; // Exponentially increase the delay
                }
            }

        }
    }
}

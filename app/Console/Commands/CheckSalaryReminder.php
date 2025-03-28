<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Job;
class CheckSalaryReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:salary-reminder';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User payday time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        // Get users who should receive salary today
    $users = Job::whereDay('created_at', now()->day)->get();

    foreach ($users as $user) {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'salary_reminder',
            'message' => 'It\'s time to get your salary!'
        ]);

        // Here you might also want to trigger a push notification
        // via Firebase Cloud Messaging or your preferred service
    }

    $this->info('Salary reminders processed.');
    }
}

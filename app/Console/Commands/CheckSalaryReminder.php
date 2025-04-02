<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\SalaryTimeNotification;
use Illuminate\Support\Facades\Notification;


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
    protected $description = 'Send salary reminder notifications to users whose payday is today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // جلب المستخدمين الذين يجب أن يستلموا الراتب اليوم
        $users = User::whereDay('created_at', now()->day)->get();

        foreach ($users as $user) {
            // إرسال إشعار داخل التطبيق
            $user->notify(new SalaryTimeNotification());
            // يمكنك هنا إضافة إشعار Firebase Cloud Messaging (FCM) إذا كنت تستخدمه
        }

        $this->info('Salary reminder sent successfully.');
    }
}

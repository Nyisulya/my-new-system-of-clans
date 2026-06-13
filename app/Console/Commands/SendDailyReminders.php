<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Marriage;
use App\Models\User;
use App\Notifications\UpcomingBirthdayNotification;
use App\Notifications\UpcomingAnniversaryNotification;
use App\Notifications\DeathAnniversaryNotification;
use Illuminate\Console\Command;

class SendDailyReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send daily reminders for upcoming birthdays, anniversaries, and death anniversaries';

    public function handle()
    {
        $this->info('Sending daily reminders...');

        // Get all users who want notifications
        $users = User::where('email_notifications', true)->get();

        foreach ($users as $user) {
            $this->sendBirthdayReminders($user);
            $this->sendAnniversaryReminders($user);
            $this->sendDeathAnniversaryReminders($user);
        }

        $this->info('Reminders sent successfully!');
    }

    protected function sendBirthdayReminders(User $user)
    {
        if (!$user->birthday_reminders) return;

        // Find birthdays in the next 7 days
        $members = Member::whereNotNull('date_of_birth')
            ->where('status', 'alive')
            ->get()
            ->filter(function ($member) {
                $birthday = $member->date_of_birth->setYear(now()->year);
                return now()->diffInDays($birthday, false) >= 0 && now()->diffInDays($birthday, false) <= 7;
            });

        foreach ($members as $member) {
            $user->notify(new UpcomingBirthdayNotification($member));
        }
    }

    protected function sendAnniversaryReminders(User $user)
    {
        if (!$user->anniversary_reminders) return;

        // Find anniversaries in the next 7 days
        $marriages = Marriage::whereNotNull('marriage_date')
            ->where('status', 'active')
            ->with(['husband', 'wife'])
            ->get()
            ->filter(function ($marriage) {
                $anniversary = $marriage->marriage_date->setYear(now()->year);
                return now()->diffInDays($anniversary, false) >= 0 && now()->diffInDays($anniversary, false) <= 7;
            });

        foreach ($marriages as $marriage) {
            $user->notify(new UpcomingAnniversaryNotification($marriage));
        }
    }

    protected function sendDeathAnniversaryReminders(User $user)
    {
        if (!$user->death_anniversary_reminders) return;

        // Find death anniversaries in the next 7 days
        $members = Member::whereNotNull('date_of_death')
            ->where('status', 'deceased')
            ->get()
            ->filter(function ($member) {
                $deathDate = $member->date_of_death->setYear(now()->year);
                return now()->diffInDays($deathDate, false) >= 0 && now()->diffInDays($deathDate, false) <= 7;
            });

        foreach ($members as $member) {
            $user->notify(new DeathAnniversaryNotification($member));
        }
    }
}

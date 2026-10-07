<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class EventNotifySchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:event-notify-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send event reminders 30 minutes before the start time';

    public function handle()
    {

        date_default_timezone_set(env('APP_TIMEZONE'));
        Log::info('Event notify schedule started at ' . now());
        Log::info('Event notify schedule started at ' . now()->addMinutes(30));
        $events = Event::with('user')
            ->where('active_status', 1)
            ->where('email_notify', 1)
            ->whereNull('email_notify_at')
            ->whereBetween('start_date', [now(), now()->addMinutes(30)])
            ->where('start_date', '>', now())
            ->get();

        Log::info('Fetched ' . $events->count() . ' events for notification.');
        try {
            foreach ($events as $event) {
                if (!empty($event->guest_user_ids)) {
                    $eventAudienceIds = json_decode($event->guest_user_ids);
                    log::info('event audience ids: ',$eventAudienceIds);

                    if (is_array($eventAudienceIds) && count($eventAudienceIds) > 0) {
                        $eventAudience = User::whereIn('id', $eventAudienceIds)->get();
                        if ($eventAudience->isNotEmpty()) {
                            log::info('Current audience size: ' . $eventAudience->count());
                            Notification::send($eventAudience, new EventReminderNotification($event));
                        }
                    }
                }
                // sending to event host
                $event->user->notify(new EventReminderNotification($event));
                $event->update(['email_notify_at' => now()]);
            }

        }catch (\Exception $e){
            log::error($e->getMessage());
        }

    }
}

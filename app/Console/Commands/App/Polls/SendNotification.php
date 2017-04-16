<?php

namespace App\Console\Commands\App\Polls;

use App\Model\App\Device;
use App\Model\Customers\Customer;
use App\Model\Helpers\Notification;
use App\Model\Poll;
use DB;
use Illuminate\Console\Command;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polls:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications about upcoming poll deadlines when people haven\'t voted yet';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get customers with an active license and where the app is in use
        $customers = Customer::with(['license' => function ($query) {
            $query->where('start_date', '<=', date('Y-m-d H:i:s'))
                ->where('end_date', '>=', date('Y-m-d H:i:s'))
                ->where('active', 1);
        }])->where('app', 1)->get();

        // Loop through the customers to execute the task
        foreach($customers as $customer) {
            $customer->connect_to_database();

            // Get agenda items of which the user should be remembered
            $polls = Poll::where('deadline', '>=', date('Y-m-d H:i:s'))
                ->where('notification', '!=', 0)
                ->get();

            // Loop through the tasks to send a notification when required
            foreach($polls as $poll) {
                $notification_date = date('d', strtotime($poll->deadline . ' - ' . $poll->notification_days . ' days'));
                $current_date      = date('d', time());

                // If the current day is equal to the notification date, send it
                if($current_date == $notification_date) {
                    // Get everyone that hasn't voted yet
                    $users = DB::table('polls_unvoted')->where('poll_id', $poll->id)->pluck('user_id')->toArray();

                    // If no users need a notification, stop here
                    if(count($users) == 0) {
                        return;
                    }

                    // Get the tokens for the notification
                    $tokens = Device::whereRaw('user_id IN (' . implode(', ', $users) . ')')->get(['token'])->toArray();

                    $notification = new Notification($poll->question, 'Vergeet niet te stemmen, je hebt nog ' . $poll->notification_days . ' ' . ($poll->notification_days > 1 ? 'dagen' : 'dag!'));

                    $notification->add_data(['page' => 'polls']);

                    $notification->send($tokens);

                    $this->comment('Sending notification for poll: ' . $poll->question);
                }
            }
        }
    }
}

<?php

namespace App\Console\Commands\App\Agenda;

use App\Http\Controllers\Mails\Mails;
use App\Model\App\Agenda;
use App\Model\App\Device;
use App\Model\Customers\Customer;
use App\Model\Helpers\Notification;
use App\Model\Tasks\Task;
use App\Model\Users\User;
use Illuminate\Console\Command;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agenda:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications about upcoming agenda items';

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
            $agenda_items = Agenda::where('start', '>=', date('Y-m-d H:i:s'))
                ->where('notification', '!=', 0)
                ->get();

            // Loop through the tasks to send a notification when required
            foreach($agenda_items as $item) {
                $notification_date = date('d', strtotime($item->start . ' - ' . $item->notification_days . ' days'));
                $current_date      = date('d', time());

                // If the current day is equal to the notification date, send it
                if($current_date == $notification_date) {
                    $tokens = Device::all(['token'])->toArray();

                    $notification = new Notification($item->title, 'Op ' . date('j F Y', strtotime($item->start)) . ' staat de activiteit ' . $item->title . ' gepland. Het begint om ' . date('H:i', strtotime($item->start)) . '.');

                    $notification->add_data(['page' => 'agenda']);

                    $notification->send($tokens);

                    $this->comment('Sending notification for agenda item: ' . $item->title);
                }
            }
        }
    }
}

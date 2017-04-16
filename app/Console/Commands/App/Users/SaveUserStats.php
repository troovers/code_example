<?php

namespace App\Console\Commands\App\Users;

use App\Model\Customers\Customer;
use App\Model\Users\User;
use DB;
use Illuminate\Console\Command;

class SaveUserStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:users:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save the stats of the app users';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get customers with an active license
        $customers = Customer::get_active_customers();

        // Loop through the customers to execute the task
        foreach ($customers as $customer) {
            $customer->connect_to_database();

            $this->comment('Inserting app user stats for ' . $customer->organization);

            $app_youth_members = User::leftJoin('members', 'members.id', '=', 'users.member_id')
                ->whereRaw('date_of_birth > ADDDATE(CURRENT_DATE(), INTERVAL -18 YEAR)')
                ->where('app_user', 1)->count();

            $app_senior_members = User::leftJoin('members', 'members.id', '=', 'users.member_id')
                ->whereRaw('date_of_birth <= ADDDATE(CURRENT_DATE(), INTERVAL -18 YEAR)')
                ->where('app_user', 1)->count();

            DB::table('app_users_stats')->insert([
                'date'           => date('Y-m-d'),
                'youth_members'  => $app_youth_members,
                'senior_members' => $app_senior_members
            ]);
        }
    }
}

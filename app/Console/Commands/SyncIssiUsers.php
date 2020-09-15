<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Domains\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

class SyncIssiUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync our users with the Issi SQL Server database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Getting users from Issi SQL Server...');
        try {
	        // Setup connection
	        $connection = DB::connection('sqlsrv');
	        $attendees  = $connection->select('SELECT * FROM vwTiaBuyInfo');
	        $exhibitors = $connection->select('SELECT * FROM vwTiaExhInfo');

	        $users = collect(array_merge($attendees, $exhibitors));

	        $bar = $this->output->createProgressBar(count($users));

	        $users->map(function ($user) use ($bar) {
		        User::updateOrCreate(
			        ['login_number' => $user->PerNum],
			        $this->userTransformer($user)
		        );
		        $bar->advance();
	        });

	        $bar->finish();
        } catch(\Exception $e) {
        	$this->error($e->getMessage());
        }

    }

    public function userTransformer($user)
    {
        return [
            'name' => $user->PerFirstName . ' ' . $user->PerLastName,
	        'password' => Hash::make($user->PerPwd),
            'first_name' => isset($user->PerFirstName) ? $user->PerFirstName : '',
            'last_name' => isset($user->PerLastName) ? $user->PerLastName : '',
            'title' => isset($user->PerTitle) ? $user->PerTitle : '',
            'login_number' => isset($user->PerNum) ? $user->PerNum : '',
            'login_password' => isset($user->PerPwd) ? $user->PerPwd : '',
            'email' => isset($user->PerNum) ? $user->PerNum . '@tianguisturistico.com' : '',
	        'email_adddress' => isset($user->PerEmail) ? $user->PerEmail : '',
            'register_type' => isset($user->RegType) ? $user->RegType : '',
            'company_name' => isset($user->CmpName) ? $user->CmpName : '',
            'company_city' => isset($user->CmpCity) ? $user->CmpCity : '',
            'company_state' => isset($user->CmpState) ? $user->CmpState : '',
            'company_country' => isset($user->CmpCountry) ? $user->CmpCountry : '',
            'company_url' => isset($user->CmpURL) ? $user->CmpURL : '',
            'company_organization' => isset($user->CmpProfOrg) ? $user->CmpProfOrg : '',
            'company_products' => isset($user->CmpProfProd) ? $user->CmpProfProd : '',
            'company_areas' => isset($user->CmpProfArea) ? $user->CmpProfArea : '',
            'booth' => isset($user->IsBoothOccupant) && 'Yes' === $user->IsBoothOccupant ? true : false,
            'booth_type' => isset($user->BoothType) ? $user->BoothType : '',
        ];
    }
}

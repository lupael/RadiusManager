<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RadiusCleanUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'radius:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans 90 days or older radius logs from the Database.';

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
     * @return mixed
     */
    public function handle()
    {
        $result = DB::table('radpostauth')
            ->where('created_at', '<', Carbon::today()->subDays(90)->format('Y-m-d'))->delete();
        $this->comment("{$result} records deleted from radpostauth.");
    }
}

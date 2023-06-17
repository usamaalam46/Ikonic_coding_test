<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Connection;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users= User::all();
        for($i=0;$i<29;$i++) {
            for($j=30;$j<59;$j++) {
                Connection::create([
                    "sender_id" =>$users[$i]->id,
                    "status"=>1,
                    "receiver_id"=>$users[$j]->id
                ]);
            }
        }
    }
}

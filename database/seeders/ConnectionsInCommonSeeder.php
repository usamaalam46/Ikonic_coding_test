<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Connection;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConnectionsInCommonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users= User::all();
        for($i=0;$i<30;$i++) {
            for($j=60;$j<90;$j++) {
                if($i!=$j) {
                    Connection::create([
                        "sender_id" =>$users[$i]->id,
                        "status"=>2,
                        "receiver_id"=>$users[$j]->id
                    ]);
                }
            }
        }
        for($i=60;$i<70;$i++) {
            for($j=70;$j<90;$j++) {
                if($i!=$j) {
                    Connection::create([
                        "sender_id" =>$users[$i]->id,
                        "status"=>2,
                        "receiver_id"=>$users[$j]->id
                    ]);
                }
            }
        }
    }
}

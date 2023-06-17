<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Connection;
use App\Models\User;
use Auth;

class ConnectionController extends Controller
{
    //
    public function index(Request $request)
    {
        $type= $request->query('type') ? $request->query('type') : 'suggestions';
        $user = auth()->user();
        $userId = $request->query('user_id') ? $request->query('user_id') : 0;
        if($type=='sent') {
            $data = Connection::where('sender_id', $user->id)->with('receiver')->where('status', 1)->Paginate(10);
            return $data;
        }
        if($type=='received') {
            $data = Connection::where('receiver_id', $user->id)->with('sender')->where('status', 1)->Paginate(10);
            return $data;
        }
        if($type == 'connections') {
            $connections = Connection::where(function ($q) use ($user) {
                $q->where('receiver_id', $user->id)
                ->orWhere('sender_id', $user->id);
            })->with(['sender','receiver'])->where('status', 2)->Paginate(10);
            $connectedUsers = Connection::where(function ($q) use ($user) {
                $q->where('receiver_id', $user->id)
                ->orWhere('sender_id', $user->id);
            })->with(['sender','receiver'])->where('status', 2)->get();
            $connectedUserIds= [];
            foreach($connectedUsers as $connectedUser) {
                if($connectedUser->receiver_id != $user->id) {
                    array_push($connectedUserIds, $connectedUser->receiver_id);
                }
                if($connectedUser->sender_id != $user->id) {
                    array_push($connectedUserIds, $connectedUser->sender_id);
                }
            }
            foreach($connections as $connection) {
                $connected_ids=[];
                $userConnections=[];
                if($connection->receiver_id != $user->id) {
                    $userConnections=Connection::where('sender_id', '!=', $user->id)->where('receiver_id', '!=', $user->id)->where(function ($q) use ($connection) {
                        $q->where('receiver_id', $connection->receiver_id)
                        ->orWhere('sender_id', $connection->receiver_id);
                    })->where('status', 2)->get();
                }
                if($connection->sender_id != $user->id) {
                    $userConnections=Connection::where('sender_id', '!=', $user->id)->where('receiver_id', '!=', $user->id)->where(function ($q) use ($connection) {
                        $q->where('receiver_id', $connection->sender_id)
                        ->orWhere('sender_id', $connection->sender_id);
                    })->where('status', 2)->get();
                }
                foreach($userConnections as $userConnection) {
                    if($userConnection->receiver_id != $user->id && ($userConnection->receiver_id != $connection->receiver_id && $userConnection->receiver_id != $connection->sender_id)) {
                        array_push($connected_ids, $userConnection->receiver_id);
                    }
                    if($userConnection->sender_id != $user->id && ($userConnection->sender_id != $connection->sender_id && $userConnection->sender_id != $connection->receiver_id)) {
                        array_push($connected_ids, $userConnection->sender_id);
                    }
                }

                $commonConnectionIds=array_intersect($connectedUserIds, $connected_ids);
                $connection->commonConnections = User::whereIn('id', $commonConnectionIds)->where('id', '!=', $user->id)->Paginate(10);
            }

            return response()->json($connections);
        }
        if($type =='suggestions') {
            $allConnections = Connection::where('receiver_id', $user->id)->orWhere('sender_id', $user->id)->get();
            $connectedUserIds=[];
            foreach($allConnections as $connection) {
                if($connection->receiver_id != $user->id) {
                    array_push($connectedUserIds, $connection->receiver_id);
                }
                if($connection->sender_id != $user->id) {
                    array_push($connectedUserIds, $connection->sender_id);
                }
            }
            $data = User::whereNotIn('id', $connectedUserIds)->where('id', '!=', $user->id)->Paginate(10);
            return $data;

        }
        if($type == 'common-connections') {
            $connectedUsers = Connection::where(function ($q) use ($user) {
                $q->where('receiver_id', $user->id)
                ->orWhere('sender_id', $user->id);
            })->where('status', 2)->get();
            $connectedUserIds= [];
            foreach($connectedUsers as $connectedUser) {
                if($connectedUser->receiver_id != $user->id) {
                    array_push($connectedUserIds, $connectedUser->receiver_id);
                }
                if($connectedUser->sender_id != $user->id) {
                    array_push($connectedUserIds, $connectedUser->sender_id);
                }
            }
            $connected_ids=[];
            $userConnections=Connection::where('sender_id', '!=', $user->id)->where('receiver_id', '!=', $user->id)->where(function ($q) use ($userId) {
                $q->where('receiver_id', $userId)
                ->orWhere('sender_id', $userId);
            })->where('status', 2)->get();
            foreach($userConnections as $userConnection) {
                if($userConnection->receiver_id != $user->id && $userConnection->receiver_id != $userId) {
                    array_push($connected_ids, $userConnection->receiver_id);
                }
                if($userConnection->sender_id != $user->id && $userConnection->sender_id != $userId) {
                    array_push($connected_ids, $userConnection->sender_id);
                }
            }

            $commonConnectionIds=array_intersect($connectedUserIds, $connected_ids);
            $data = User::whereIn('id', $commonConnectionIds)->where('id', '!=', $user->id)->Paginate(10);
            return $data;
        }

    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $receiverId = $request->input('id');
        $createRequest= Connection::create([
            'sender_id'=>$user->id,
            'receiver_id'=>$receiverId,
            'status'=>1
        ]);
        return redirect()->route('home');
    }
    public function update(Request $request, $id)
    {
        Connection::where('id', $id)->update(['status'=>2]);
        return "updated";
    }
    public function destroy($id)
    {
        Connection::where('id', $id)->delete();
        return "deleted";
    }
}

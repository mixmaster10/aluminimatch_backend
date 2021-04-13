<?php

namespace App\Traits;

use App\Models\Friend;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait FriendTrait
{
    public function approve($uid1, $uid2) {
        DB::table('friend_requests')->where([
            'uid' => $uid1,
            'fid' => $uid2
        ])->orWhere([
            'fid' => $uid1,
            'uid' => $uid2
        ])->delete();
        Friend::firstOrCreate(['uid' => $uid1, 'fid' => $uid2], ['shared' => 1]);
        Friend::firstOrCreate(['fid' => $uid1, 'uid' => $uid2], ['shared' => 1]);
        return true;
    }

    public function ignore($uid1, $uid2) {
        DB::table('friend_requests')->where([
            'uid' => $uid2,
            'fid' => $uid1
        ])->delete();
        return true;
    }

    public function invite($uid1, $uid2, $msg) {
        $is_invite = DB::table('friend_requests')->where([
            'uid' => $uid1,
            'fid' => $uid2,
        ])->exists();
        if ($is_invite) {
            DB::table('friend_requests')->where(['uid' => $uid1, 'fid' => $uid2,])->update(['msg' => $msg, 'updated_at' => Carbon::now()]);
        } else {
            DB::table('friend_requests')->insert(['uid' => $uid1, 'fid' => $uid2, 'msg' => $msg, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        }
        return true;
    }

    public function block($uid1,$uid2,$msg,$block=true) {
        $is_blocked = DB::table('blocked_users')->where([
            'uid' => $uid1,
            'fid' => $uid2
        ])->exists();
        if ($block) {
            if ($is_blocked) {
                DB::table('blocked_users')->where(
                    ['uid' => $uid1, 'fid' => $uid2]
                )->update(['msg' => $msg,'updated_at'=>Carbon::now()]);
            } else {
                DB::table('blocked_users')->insert(['uid' => $uid1,'fid' => $uid2, 'msg' => $msg, 'created_at' => Carbon::now(),'updated_at' => Carbon::now()]);
            }
        } else {
            DB::table('blocked_users')->where(
                ['uid' => $uid1, 'fid' => $uid2]
            )->delete();
        } 
        return true;
    }
}
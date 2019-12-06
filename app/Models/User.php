<?php

namespace App\Models;

/**
 * User Model
 */

use App\Services\Config;
use App\Utils\Hash;
use App\Utils\Tools;

class User extends Model

{
    protected $table = "user";

    public $isLogin;

    public $isAdmin;

    protected $casts = [
        "t" => 'int',
        "u" => 'int',
        "d" => 'int',
        "port" => 'int',
        "switch" => 'int',
        "transfer_enable" => 'float',
        "transfer_plan" => 'float',
        "enable" => 'int',
        "freeze" => 'int',
        'is_admin' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['pass', 'last_get_gift_time', 'last_rest_pass_time', 'last_freeze_time', 'reg_ip', 'is_email_verify', 'user_name', 'ref_by', 'is_admin'];

    public function getGravatarAttribute()
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "https://secure.gravatar.com/avatar/$hash";
    }

    public function isAdmin()
    {
        return $this->attributes['is_admin'];
    }

    public function lastSsTime()
    {
        if ($this->attributes['t'] == 0) {
            return "从未使用喵";
        }
        return Tools::toDateTime($this->attributes['t']);
    }

    public function lastCheckInTime()
    {
        if ($this->attributes['last_check_in_time'] == 0) {
            return "从未签到";
        }
        return Tools::toDateTime($this->attributes['last_check_in_time']);
    }

    public function lastGetGiftTime()
    {
        if ($this->attributes['last_get_gift_time'] == 0) {
            return "从未充值";
        }
        return Tools::toDateTime($this->attributes['last_get_gift_time']);
    }

    public function lastRestPassTime()
    {
        if ($this->attributes['last_rest_pass_time'] == 0) {
            return "从未重置";
        }
        return Tools::toDateTime($this->attributes['last_rest_pass_time']);
    }

    public function expireTime()
    {
        if ($this->attributes['expire_time'] == 0) {
            return "从未充值";
        }
        return Tools::toDateTime($this->attributes['expire_time']);
    }

    public function lastFreezeTime()
    {
        if ($this->attributes['last_freeze_time'] == 0) {
            return "从未冻结";
        }
        return Tools::toDateTime($this->attributes['last_freeze_time']);
    }

    public function frozenTime()
    {
        if ($this->attributes['freeze'] == 0) {
            return "未冻结";
        }
        return Tools::secondsToTime(time() - $this->attributes['last_freeze_time']);
    }

    public function regDate()
    {
        return $this->attributes['reg_date'];
    }

    public function updatePassword($pwd)
    {
        $this->pass = Hash::passwordHash($pwd);
        $this->save();
    }

    public function updateSsPwd($pwd)
    {
        $this->passwd = $pwd;
        $this->save();
    }

    public function updateMethod($method)
    {
        $this->method = $method;
        $this->save();
    }

    public function updateProtocol($protocol)
    {
        $this->protocol = $protocol;
        $this->save();
    }

    public function updateProtocolParam($protocol_param)
    {
        $this->protocol_param = $protocol_param;
        $this->save();
    }

    public function updateObfs($obfs)
    {
        $this->obfs = $obfs;
        $this->save();
    }

    public function updateObfsParam($obfs_param)
    {
        $this->obfs_param = $obfs_param;
        $this->save();
    }

    public function updateV2rayUUID()
    {
        $this->v2ray_uuid = Tools::genUUID();
        $this->save();
    }

    public function updateV2rayAlterID($alter_id)
    {
        $this->v2ray_alter_id = $alter_id;
        $this->save();
    }

    public function addInviteCode()
    {
        $uid = $this->attributes['id'];
        $code = new InviteCode();
        $code->code = Tools::genRandomChar(32);
        $code->user = $uid;
        $code->save();
    }

    public function addManyInviteCodes($num)
    {
        for ($i = 0; $i < $num; $i++) {
            $this->addInviteCode();
        }
    }
    
    public function extendPayment($month_num)
    {
        $nowtime = time();
        if(!$this->attributes['freeze']) {
            $this->attributes['enable'] = true;
            $this->attributes['expire_time'] = strtotime("+".$month_num." month",
                $nowtime > $this->attributes['expire_time'] ? $nowtime : $this->attributes['expire_time']);
        }
        else {
            $this->attributes['expire_time'] = strtotime("+".$month_num." month",
                $this->attributes['expire_time']);
        }
        //邀请激励机制
        if($this->attributes['last_get_gift_time'] == 0 && $this->attributes['ref_by'] != 0)
        {
            //Bonus for current user
            $this->attributes['expire_time'] = strtotime("+15 day", $this->attributes['expire_time']);
            //Bonus for ref user
            $ref_user = User::find($this->attributes['ref_by']);
            if(!empty($ref_user))
            {
                $ref_user->expire_time = strtotime("+15 day",
                    $nowtime > $ref_user->expire_time ? $nowtime : $ref_user->expire_time);
                $ref_user->save();
            }
        }
        $this->attributes['last_get_gift_time'] = $nowtime;
        //记log
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    public function trafficUsagePercent()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        $transferEnable = $this->attributes['transfer_enable'];
        if ($transferEnable == 0) {
            return 0;
        }
        $percent = $total / $transferEnable;
        $percent = round($percent, 2);
        $percent = $percent * 100;
        return $percent;
    }

    public function enableTraffic()
    {
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowAutoShow($transfer_enable);
    }

    public function enableTrafficInGB()
    {
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowToGB($transfer_enable);
    }

    public function trafficPlan()
    {
        $transfer_plan = $this->attributes['transfer_plan'];
        return Tools::flowAutoShow($transfer_plan);
    }

    public function trafficPlanInGB()
    {
        $transfer_plan = $this->attributes['transfer_plan'];
        return Tools::flowToGB($transfer_plan);
    }

    public function usedTraffic()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        return Tools::flowAutoShow($total);
    }

    public function unusedTraffic()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowAutoShow($transfer_enable - $total);
    }

    public function isAbleToCheckin()
    {
        $last = $this->attributes['last_check_in_time'];
        $hour = Config::get('checkinTime');
        if ($last + $hour * 3600 < time()) {
            return true;
        }
        return false;
    }

    /*
     * @param traffic 单位 GB
     */
    public function extendTraffic($traffic)
    {
        $this->transfer_enable += Tools::toGB($traffic);
        if($this->save())
            return true;
        else
            return false;
    }

    public function inviteCodes()
    {
        $uid = $this->attributes['id'];
        return InviteCode::where('user_id', $uid)->get();
    }

    public function refByUsers()
    {
        $uid = $this->attributes['id'];
        return User::where('ref_by', $uid)->get();
    }

}

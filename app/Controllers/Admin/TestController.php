<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\User;
use App\Services\Mail;
use App\Utils\Tools;

class TestController extends AdminController
{
    public function sendMail($request, $response, $args)
    {
        return $this->view()->display('admin/test/sendmail.tpl');
    }

    public function sendMailPost($request, $response, $args)
    {
        $to = $request->getParam('email');
        try {
            Mail::send($to, "Test", 'test.tpl', [
                'time' => Tools::toDateTime(time())
            ], [
                BASE_PATH . '/LICENSE'
            ]);
            $res = [
                "ret" => 1,
                "msg" => "ok"
            ];
        } catch (\Exception $e) {
            $res = [
                "ret" => 0,
                "msg" => $e->getMessage()
            ];
        }
        return $this->echoJson($response, $res);
    }

    public function _51feeupgrade()
    {
        $users = User::where('expire_time', '>', 1493568000)->get();
        $ret = [];
        foreach ($users as $user) {
            $inv_num = 0;
            $refBys = $user->refByUsers();
            foreach ($refBys as $refBy) {
                if ($refBy->lastGetGiftTime() != "从未充值")
                    $inv_num++;
            }
            $inv_b_num = 0;
            if($user->ref_by != 0)
                $inv_b_num++;
            // 充值时长结算
            $one_day = 24 * 60 * 60;
            if($user->last_get_gift_time < 1493088060) {
                $pre_fee_left = $user->expire_time - 1493568000;
            }
            else {
                $pre_fee_left = ($user->expire_time - 1493568000) * 2 + 1493568000 - $user->last_get_gift_time;
            }

            if (($inv_num + $inv_b_num) * 15 * $one_day > $pre_fee_left) {
                if($inv_num * 15 * $one_day > $pre_fee_left) {
                    $pro['inv'] = $pre_fee_left;
                    $pro['sum'] = $pro['inv'];
                }
                else {
                    $pro['inv'] = $inv_num * 15 * $one_day;
                    $pro['inv_b'] = $pre_fee_left - $pro['inv'];
                    $pro['sum'] = $pro['inv'] + $pro['inv_b'];
                }
            }
            else {
                $pro['inv'] = $inv_num * 15 * $one_day;
                $pro['inv_b'] = 15 * $one_day;
                $pro['prepay'] = ($pre_fee_left - $pro['inv'] - $pro['inv_b']) / 2;
                $pro['sum'] = $pro['inv'] + $pro['inv_b'] + $pro['prepay'];
            }
            foreach ($pro as $key => $value)
            {
                $pro_v[$key] = $this->secondsToWords($value);
            }
            $ret[$user->id] = $pro_v;
            $user->expire_time = 1493568000 + $pro['sum'];
            $user->save();

            //发送邮件
            $text = "<p>我们已经完成了计费策略的更新，以下是您 5 月 1 日以后的时长转换情况。</p>";
            $text .= "<ul>";
            if(isset($pro['prepay']))
                $text .= "<li>充值时长结转：" . $this->secondsToWords($pro['prepay']) . "</li>";
            if(isset($pro['inv_b']))
                $text .= "<li>被邀请奖励：" . $this->secondsToWords($pro['inv_b']) . "</li>";
            if(isset($pro['inv']))
                $text .= "<li>邀请奖励：" . $this->secondsToWords($pro['inv']) . "</li>";
            $text .= "</ul>";
            $text .= "<p>合计：" . $this->secondsToWords($pro['sum']) . "，到期时间为 " . date('Y-m-d H:i:s', 1493568000 + $pro['sum']) . "。</p>";
            $text .= "<p>以上有疑问请直接回复此邮件。</p>";

            try {
                Mail::send($user->email, "2645Network 计费结转账单", 'news/notice.tpl', [
                    'username' => $user->user_name,
                    'time' => Tools::toDateTime(time()),
                    'text' => $text
                ], [
                    BASE_PATH . '/LICENSE'
                ]);
                array_push($ret[$user->id], [
                    "email" => $user->email,
                    "ret" => 1,
                    "msg" => "ok"
                ]);
            } catch (\Exception $e) {
                array_push($ret[$user->id], [
                    "email" => $user->email,
                    "ret" => 0,
                    "msg" => $e->getMessage()
                ]);
            }
        }
        return json_encode($ret);
    }
    function secondsToWords($seconds) {
        /*** return value ***/
        $ret = "";

        $months = intval(intval($seconds) / (3600 * 24 * 30));
        if($months > 0)
        {
            $ret .= "$months 月 ";
        }

        $days = bcmod(intval(intval($seconds) / (3600 * 24)), 30);
        if($months > 0 || $days > 0)
        {
            $ret .= "$days 天 ";
        }

        /*** get the hours ***/
        $hours = bcmod(intval(intval($seconds) / 3600), 24);
        if($months > 0 || $days > 0 || $hours > 0)
        {
            $ret .= "$hours 小时 ";
        }
        /*** get the minutes ***/
        $minutes = bcmod((intval($seconds) / 60),60);
        if($months > 0 || $days > 0 || $hours > 0 || $minutes > 0)
        {
            $ret .= "$minutes 分 ";
        }

        /*** get the seconds ***/
        $seconds = bcmod(intval($seconds),60);
        $ret .= "$seconds 秒";

        return $ret;
    }
}
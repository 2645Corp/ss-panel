<?php

namespace App\Controllers;

use App\Models\InviteCode;
use App\Models\Node, App\Models\User;
use App\Services\Factory;
use App\Services\Config;
use App\Services\Mail;
use App\Utils\Tools, App\Utils\Hash, App\Utils\Helper;

/**
 *  ApiController
 */
class ApiController extends BaseController
{

    public function index()
    {

    }

    public function token($request, $response, $args)
    {
        $accessToken = $id = $args['token'];
        $storage = Factory::createTokenStorage();
        $token = $storage->get($accessToken);
        if ($token == null) {
            $res['ret'] = 0;
            $res['msg'] = "token is null";
            return $this->echoJson($response, $res);
        }
        $res['ret'] = 1;
        $res['msg'] = "ok";
        $res['data'] = $token;
        return $this->echoJson($response, $res);
    }

    public function newToken($request, $response, $args)
    {
        // $data = $request->post('sdf');
        $email = $request->getParam('email');
        $email = strtolower($email);
        $passwd = $request->getParam('passwd');

        // Handle Login
        $user = User::where('email', '=', $email)->first();

        if ($user == null) {
            $res['ret'] = 0;
            $res['msg'] = "401 邮箱或者密码错误";
            return $this->echoJson($response, $res);
        }

        if (!Hash::checkPassword($user->pass, $passwd)) {
            $res['ret'] = 0;
            $res['msg'] = "402 邮箱或者密码错误";
            return $this->echoJson($response, $res);
        }
        $tokenStr = Tools::genToken();
        $storage = Factory::createTokenStorage();
        $expireTime = time() + 3600 * 24 * 7;
        if ($storage->store($tokenStr, $user, $expireTime)) {
            $res['ret'] = 1;
            $res['msg'] = "ok";
            $res['data']['token'] = $tokenStr;
            $res['data']['user_id'] = $user->id;
            return $this->echoJson($response, $res);
        }
        $res['ret'] = 0;
        $res['msg'] = "system error";
        return $this->echoJson($response, $res);
    }

    public function node($request, $response, $args)
    {
        $nodes = Node::where('type', 1)->orderBy('sort')->get();
        $res['ret'] = 1;
        $res['msg'] = "ok";
        $res['data'] = $nodes;
        return $this->echoJson($response, $res);
    }

    public function userInfo($request, $response, $args)
    {
        $id = $args['id'];
        $accessToken = Helper::getTokenFromReq($request);
        $storage = Factory::createTokenStorage();
        $token = $storage->get($accessToken);
        if ($id != $token->userId) {
            $res['ret'] = 0;
            $res['msg'] = "access denied";
            return $this->echoJson($response, $res);
        }
        $user = User::find($token->userId);
        $user->pass = null;
        $data = $user;
        $res['ret'] = 1;
        $res['msg'] = "ok";
        $res['data'] = $data;
        return $this->echoJson($response, $res);

    }

    public function maintainPayment()
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->freeze) {
                // user is frozen, do nothing
                continue;
            } else if ($user->isAdmin() || $user->expire_time > time()) { // 是管理员，或者没欠费
                if (strtotime("+30 day", $user->last_rest_pass_time) <= time()) { // 需要重置
                    $user->last_rest_pass_time = time();
                    // Reset transfer_enable with transfer_plan.
                    $user->transfer_enable = $user->transfer_plan;
                    $user->d = 0;
                    $user->u = 0;
                }
            } else { //欠费
                $user->enable = false;
            }
            $user->save();
        }
        return json_encode(true);
    }

    public function cleanInactiveUsers()
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->isAdmin() || $user->expire_time > time() || $user->freeze) { // 没欠费或者已冻结，忽略管理员账户:P
                continue;
            } else { //欠费
                if (($user->expire_time != 0 && $user->expire_time < strtotime("-90 day", time()))
                    || ($user->expire_time == 0 && strtotime($user->regDate()) < strtotime("-90 day", time()))
                ) {   //欠费很多时
                    $user->delete();
                }
            }
        }
        return json_encode(true);
    }

    //没有保护机制 切勿频繁触发
    public function sendReminderMail($request)
    {
        if ($request->getParam('key') != Config::get('key'))
            return json_encode(['result' => false, 'msg' => 'invalid key']);
        $mail = new Mail();
        $users = User::all();
        $res = array();
        foreach ($users as $user) {
            if ($user->freeze) continue;
            if ((strtotime("-6 day", $user->expire_time) > time() && strtotime("-7 day", $user->expire_time) < time()) ||
                ($user->expire_time > time() && strtotime("-2 day", $user->expire_time) < time())
            ) { // 发送提醒邮件
                try {
                    $mail->send($user->email, "2645 Network 用户续期提醒", 'service/remind.tpl', ['username' => $user->user_name, 'expiredate' => $user->expireTime()]);
                    array_push($res, [
                        "email" => $user->email,
                        "ret" => 1,
                        "msg" => "ok"
                    ]);
                } catch (\Exception $e) {
                    array_push($res, [
                        "email" => $user->email,
                        "ret" => 0,
                        "msg" => $e->getMessage()
                    ]);
                }
            } else if ($user->expire_time < time() && strtotime("+1 day", $user->expire_time) > time()
            ) { // 发送提醒邮件
                try {
                    $mail->send($user->email, "2645 Network 用户欠费提醒", 'service/expire.tpl', ['username' => $user->user_name]);
                    array_push($res, [
                        "email" => $user->email,
                        "ret" => 1,
                        "msg" => "ok"
                    ]);
                } catch (\Exception $e) {
                    array_push($res, [
                        "email" => $user->email,
                        "ret" => 0,
                        "msg" => $e->getMessage()
                    ]);
                }
            }
        }
        return json_encode($res);
    }
}

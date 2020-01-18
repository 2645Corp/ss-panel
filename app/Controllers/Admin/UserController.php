<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Node;
use App\Models\User;
use App\Utils\Hash;
use App\Utils\Tools;

class UserController extends AdminController
{
    public function index($request, $response, $args)
    {
        $pageNum = 1;
        $search_email = '';
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        if (isset($request->getQueryParams()["email"])) {
            $search_email = $request->getQueryParams()["email"];
            $users = User::orderBy('t','desc')->where('email', 'like', '%'.$search_email.'%')
                ->paginate(15, ['*'], 'page', $pageNum);
            $users->setPath('/admin/user?email=' . urlencode($request->getQueryParams()["email"]));
        } else {
            $users = User::orderBy('t','desc')->paginate(15, ['*'], 'page', $pageNum);
            $users->setPath('/admin/user');
        }
        return $this->view()->assign('users', $users)
            ->assign('search_email', $search_email)
            ->display('admin/user/index.tpl');
    }

    public function edit($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        if ($user == null) {

        }
        $method = Node::getCustomerMethod();
        $protocol = Node::getProtocolMethod();
        $obfs = Node::getObfsMethod();
        return $this->view()->assign('user', $user)->assign('method', $method)
            ->assign('protocol', $protocol)->assign('obfs', $obfs)->display('admin/user/edit.tpl');
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);

        $user->user_name = $request->getParam('user_name');
        $user->email = $request->getParam('email');
        if ($request->getParam('pass') != '') {
            $user->pass = Hash::passwordHash($request->getParam('pass'));
        }
        if ($request->getParam('passwd') != '') {
            $user->passwd = $request->getParam('passwd');
        }
        $user->port = $request->getParam('port');
        $user->transfer_enable = Tools::toGB($request->getParam('transfer_enable'));
        $user->transfer_plan = Tools::toGB($request->getParam('transfer_plan'));
        $user->invite_num = $request->getParam('invite_num');
        $user->method = $request->getParam('method');
        $user->protocol = $request->getParam('method');
        $user->protocol_param = $request->getParam('protocol_param');
        $user->obfs = $request->getParam('obfs');
        $user->obfs_param = $request->getParam('obfs_param');
        $user->v2ray_level = $request->getParam('v2ray_level');
        $user->v2ray_alter_id = $request->getParam('v2ray_alter_id');
        $user->enable = $request->getParam('enable');
        $user->is_admin = $request->getParam('is_admin');
        $user->ref_by = $request->getParam('ref_by');
        if (!$user->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "修改失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "修改成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function updateV2rayUUID($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);

        $user->v2ray_uuid = Tools::genUUID();
        if (!$user->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "修改失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "修改成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function extendPayment($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);

        // sanity check
        if ($request->getParam('month_num') == null ||
            $request->getParam('month_num') == '' ||
            intval($request->getParam('month_num')) === 0) {
            $rs['ret'] = 0;
            $rs['msg'] = "修改失败（参数为空或参数格式错误）";
            return $response->getBody()->write(json_encode($rs));
        }

        $r = $user->extendPayment($request->getParam('month_num'));

        if (!$r) {
            $rs['ret'] = 0;
            $rs['msg'] = "修改失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "修改成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function delete($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        if (!$user->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = "删除失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "删除成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function deleteGet($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        $user->delete();
        $newResponse = $response->withStatus(302)->withHeader('Location', '/admin/user');
        return $newResponse;
    }
}

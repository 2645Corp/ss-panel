{include file='user/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            用户中心
            <small>User Center</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div id="msg-info" class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>
                        <i class="icon fa fa-info"></i>
                        你知道吗？被你介绍的朋友在第一次充值的时候可以获得15天的额外时长，你自己也可以获得15天的时长返利！
                    </h4>
                </div>
            </div>
        </div>
        <!-- START PROGRESS BARS -->
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-bullhorn"></i>

                        <h3 class="box-title">公告&FAQ</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        {$msg}
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <!-- /.col (right) -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-home"></i>

                        <h3 class="box-title">账户状态</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt>账户状态</dt>
                            <dd>{if $user->enable }正常
                                {elseif $user->freeze}已冻结
                                {else}已欠费
                                {/if}</dd>
                            <dt>上次续费时间</dt>
                            <dd>{$user->lastGetGiftTime()}</dd>
                            <dt>本月开始时间</dt>
                            <dd>{$user->lastRestPassTime()}</dd>
                            <dt>续费到期时间</dt>
                            <dd>{$user->expireTime()}</dd>
                            {if $user->freeze}
                                <dt>已冻结时间</dt>
                                <dd>{$user->frozenTime()}</dd>
                            {/if}
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <!-- /.col (left) -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-exchange"></i>

                        <h3 class="box-title">流量使用情况</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="progress progress-striped">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40"
                                         aria-valuemin="0" aria-valuemax="100"
                                         style="width: {$user->trafficUsagePercent()}%">
                                        <span class="sr-only">Transfer</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <dl class="dl-horizontal">
                            <dt>当月总流量</dt>
                            <dd>{$user->enableTraffic()}</dd>
                            <dt>月套餐流量</dt>
                            <dd>{$user->trafficPlan()}</dd>
                            <dt>已用流量</dt>
                            <dd>{$user->usedTraffic()}</dd>
                            <dt>剩余流量</dt>
                            <dd>{$user->unusedTraffic()}</dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <!-- /.col (right) -->

            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa  fa-paper-plane"></i>

                        <h3 class="box-title">连接信息</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt>端口</dt>
                            <dd>{$user->port}</dd>
                            <dt>密码</dt>
                            <dd>{$user->passwd}</dd>
                            <dt>自定义加密方式</dt>
                            <dd>{$user->method}</dd>
                            <dt>自定义协议插件</dt>
                            <dd>{$user->protocol}</dd>
                            <dt>自定义协议参数</dt>
                            <dd>{$user->protocol_param}</dd>
                            <dt>混淆插件</dt>
                            <dd>{$user->obfs}</dd>
                            <dt>混淆参数</dt>
                            <dd>{$user->obfs_param}</dd>
                            <dt>V2Ray UUID</dt>
                            <dd>{$user->v2ray_uuid}</dd>
                            <dt>V2Ray Alter ID</dt>
                            <dd>{$user->v2ray_alter_id}</dd>
                            <dt>上次使用</dt>
                            <dd>{$user->lastSsTime()}</dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col (right) -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-pencil"></i>

                        <h3 class="box-title">签到获取流量</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <p> 每{$config['checkinTime']}小时可以签到一次。</p>

                        <p>上次签到时间：<code>{$user->lastCheckInTime()}</code></p>
                        {if $user->isAbleToCheckin() }
                            <p id="checkin-btn">
                                <button id="checkin" class="btn btn-success  btn-flat">签到</button>
                            </p>
                        {else}
                            <p><a class="btn btn-success btn-flat disabled" href="#">不能签到</a></p>
                        {/if}
                        <p id="checkin-msg"></p>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
        <!-- /.row --><!-- END PROGRESS BARS -->
    </section>
    <!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
    $(document).ready(function () {
        $("#checkin").click(function () {
            $.ajax({
                type: "POST",
                url: "/user/checkin",
                dataType: "json",
                success: function (data) {
                    $("#checkin-msg").html(data.msg);
                    $("#checkin-btn").hide();
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })
    })
</script>


{include file='user/footer.tpl'}
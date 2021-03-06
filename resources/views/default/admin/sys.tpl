{include file='admin/main.tpl'}

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            系统维护
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div id="msg-error" class="alert alert-warning alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                    <p id="msg-error-p"></p>
                </div>

                <div id="msg-success" class="alert alert-success alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> 清理成功!</h4>

                    <p id="msg-success-p"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">节点状态信息清理</h3>
                    </div>
                    <div class="box-body">
                        <p>该信息包括节点在线时间和负载</p>
                    </div>
                    <div class="box-footer">
                        <button id="info" type="submit" name="action" class="btn btn-primary">确认清理</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">节点在线信息清理</h3>
                    </div>
                    <div class="box-body">
                        <p>该信息包括节点在线人数的历史信息</p>
                    </div>
                    <div class="box-footer">
                        <button id="online" type="submit" name="action" class="btn btn-primary">确认清理</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">流量历史记录清理</h3>
                    </div>
                    <div class="box-body">
                        <p>该信息包括所有用户的使用流量信息,流量结算信息,使用节点</p>
                        <span>该选项不会清零用户流量 但可能会导致一次流量结算不准确</span>
                        <span>同时节点产生流量数据也将重置</span>
                    </div>
                    <div class="box-footer">
                        <button id="traffic" type="submit" name="action" class="btn btn-primary">确认清理</button>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script>
    $(document).ready(function () {
        $("#info").click(function () {
            $.ajax({
                type: "POST",
                url: "/admin/cleannodelog",
                dataType: "json",
                success: function (data) {
                    if (data.ret) {
                        $("#msg-error").hide();
                        $("#msg-success").show();
                        $("#msg-success-p").html(data.msg);
                        window.setTimeout("history.go(0)", 2000);
                    } else {
                        $("#msg-error").show();
                        $("#msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })
    })
</script>
<script>
    $(document).ready(function () {
        $("#online").click(function () {
            $.ajax({
                type: "POST",
                url: "/admin/cleanonlinelog",
                dataType: "json",
                success: function (data) {
                    if (data.ret) {
                        $("#msg-error").hide();
                        $("#msg-success").show();
                        $("#msg-success-p").html(data.msg);
                        window.setTimeout("history.go(0)", 2000);
                    } else {
                        $("#msg-error").show();
                        $("#msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })
    })
</script>
<script>
    $(document).ready(function () {
        $("#traffic").click(function () {
            $.ajax({
                type: "POST",
                url: "/admin/cleantrafficlog",
                dataType: "json",
                success: function (data) {
                    if (data.ret) {
                        $("#msg-error").hide();
                        $("#msg-success").show();
                        $("#msg-success-p").html(data.msg);
                        window.setTimeout("history.go(0)", 2000);
                    } else {
                        $("#msg-error").show();
                        $("#msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })
    })
</script>
{include file='admin/footer.tpl'}

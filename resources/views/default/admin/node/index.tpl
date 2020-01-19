{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            节点列表
            <small>Node List</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div id="msg-success" class="alert alert-success alert-dismissable" style="display: none;">
                    <button type="button" class="close" id="ok-close" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> 成功!</h4>

                    <p id="msg-success-p"></p>
                </div>
                <div id="msg-error" class="alert alert-warning alert-dismissable" style="display: none;">
                    <button type="button" class="close" id="error-close" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                    <p id="msg-error-p"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <p><a class="btn btn-success btn-sm" href="/admin/node/create">添加</a></p>
                <div class="box">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tr>
                                <th>ID</th>
                                <th>节点</th>
                                <th>加密</th>
                                <th>描述</th>
                                <th>SS</th>
                                <th>SSR</th>
                                <th>V2Ray</th>
                                <th>排序</th>
                                <th>操作</th>
                            </tr>
                            {foreach $nodes as $node}
                                <tr>
                                    <td>#{$node->id}</td>
                                    <td> {$node->name}</td>
                                    <td>{$node->method}</td>
                                    <td>{$node->info}</td>
                                    <td>{$node->ss}</td>
                                    <td>{$node->ssr}</td>
                                    <td>{$node->v2ray}</td>
                                    <td>{$node->sort}</td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="/admin/node/{$node->id}/edit">编辑</a>
                                        <a class="btn btn-danger btn-sm delete-btn" onclick="deleteNode('{$node->id}')">删除</a>
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->


<script>
    const deleteNode = function (nodeId) {
        if (!confirm("确定要删除节点" + nodeId + "吗？")) {
            return;
        }

        $.ajax({
            type: "DELETE",
            url: "/admin/node/" + nodeId,
            success: function (data) {
                data = JSON.parse(data);
                if (data.ret) {
                    $("#msg-error").hide(100);
                    $("#msg-success").show(100);
                    $("#msg-success-p").html(data.msg);
                    window.setTimeout("location.href='/admin/node'", 2000);
                } else {
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html(data.msg);
                }
            },
            error: function (data) {
                data = JSON.parse(data);
                $("#msg-error").hide(10);
                $("#msg-error").show(100);
                $("#msg-error-p").html("发生错误：" + data.status);
            }
        });
    };

    $(document).ready(function () {
        $("html").keydown(function (event) {
            if (event.keyCode == 13) {
                login();
            }
        });
        $("#ok-close").click(function () {
            $("#msg-success").hide(100);
        });
        $("#error-close").click(function () {
            $("#msg-error").hide(100);
        });
    })
</script>

{include file='admin/footer.tpl'}

{include file='header.tpl'}
<div class="section no-pad-bot" id="index-banner">
    <div class="container">
        <br><br>
        <div class="row center">
            <h5>邀请码实时刷新</h5>
            <h5>如遇到无邀请码请找已经注册的用户获取，</h5>
            <h5>或发送邮件到<a href="mailto:wangkule@cool2645.com">wangkule@cool2645.com</a>索要。</h5>
            {$msg}
            <h5>本站是付费服务，为了服务质量不提供测试账号，请谅解。</h5>
            <h5>有关本站收费标准请戳<a href="/start">这里</a>。</h5>
        </div>
    </div>
</div>

<div class="container">
    <div class="section">
        <!--   Icon Section   -->
        <div class="row">
            <div class="row marketing">
                <h2 class="sub-header">邀请码</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>###</th>
                            <th>邀请码 (点击邀请码进入注册页面)</th>
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $codes as $code}
                            <tr>
                                <td>{$code->id}</td>
                                <td><a href="/auth/register?code={$code->code}">{$code->code}</a></td>
                                <td>可用</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>
</div>
{include file='footer.tpl'}
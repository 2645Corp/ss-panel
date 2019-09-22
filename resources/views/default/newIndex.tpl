<html>
<head>
    <title>2645Network</title>
    <style>
        @font-face {
            font-family: "Ubuntu";
            src: local("Ubuntu Bold"), local("Ubuntu-Bold"), url("https://fonts.gstatic.com/s/ubuntu/v14/4iCv6KVjbNBYlgoCxCvjsGyN.woff2") format("woff2");
            font-style: normal;
            font-weight: 700;
            unicode-range: U+0-FF, U+131, U+152-153, U+2BB-2BC, U+2C6, U+2DA, U+2DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            border: 0;
        }
        .headertop {
            height: 100%;
            position: relative;
        }
        .abs {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
        .filterdot {
            background-image: url("/img/dot.gif");
            z-index: 10;
        }
        .background-img {
            background-image: url("{$bgUrl}");
            background-size: cover;
            background-position: center;
            height: 100%;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            z-index: 20;
            position: relative;
        }
        h1 {
            font-size: 80px;
            margin: 20px 10px;
            color: white;
            font-family: "Ubuntu", sans-serif;
        }
        h2 {
            font-size: 24px;
            margin: 10px 10px;
            color: white;
            font-family: "Ubuntu", sans-serif;
        }
        .button-box {
            display: flex;
            margin: 30px 0;
        }
        .btn {
            display: block;
            line-height: 30px;
            font-size: 18px;
            font-weight: bold;
            font-family: sans-serif;
            padding: 10px 15px;
            margin: 15px;
            color: #232323;
            text-decoration: none;
            position: relative;
        }
        .btn img {
            height: 24px;
            vertical-align: middle;
        }
        .signup {
            background-color: #f9faff;
        }
        .signup .border-bottom {
            background-color: #000;
        }
        .signup:hover .border-bottom {
            width: 100%;
        }
        .login {
            background-color: #ffc745;
        }
        .login .border-bottom {
            background-color: #ffe51a;
        }
        .login:hover .border-bottom {
            width: 100%;
        }
        .border-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 0;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
<div class="headertop">
    <div class="filterdot abs"></div>
    <div class="background-img abs"></div>
    <div class="container">
        <h1>2645 Network</h1>
        <h2>稳定 · 隐私 · 易用</h2>
        <h2>—— 你不专业不快速的校园网 IPv4 隧道</h2>
        <div class="button-box">
            {if $user->isLogin}
                <a href="/user" class="btn login">
                    进入用户中心
                    <div class="border-bottom"></div>
                </a>
            {else}
                <a href="/auth/login" class="btn login">
                    <img src="/img/none.png" alt="" /> 登录去
                    <div class="border-bottom"></div>
                </a>
                <a href="/auth/register" class="btn signup">
                    <img src="/img/who.png" alt="" /> 我新来的
                    <div class="border-bottom"></div>
                </a>
            {/if}
        </div>
    </div>
</div>
</body>
</html>

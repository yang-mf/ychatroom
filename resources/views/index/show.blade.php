<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge" >
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"/>
    <title>Aliplayer Online Settings</title>
    <link rel="stylesheet" href="https://g.alicdn.com/de/prismplayer/2.8.7/skins/default/aliplayer-min.css" / >
    <script type="text/javascript" charset="utf-8" src="https://g.alicdn.com/de/prismplayer/2.8.7/aliplayer-min.js"></script>
</head>
<body>
<div>
    <?php if(empty($username)){ ?>
        <a href="{{'zhuce'}}">注册</a>
        <a href="{{'/'}}">登录</a>
    <?php }else{ ?>
        欢迎用户:<span id="username">{{$username}}</span>
    <?php } ?>
</div>
<div style="float: left;">

</div>
<div class="prism-player" id="player-con" style="float: left; width: 70%"></div>
    <div style="float: right;width: 27%; height: 500px; border: 1px solid black ;">
        <div style="width: 100%; height: 500px; border: 1px solid black ;overflow-y: auto" id ="list"></div>
        <input type="text" id = "message">
        <input type="submit" value="发送" id = "btn">
        <img src="/1.png" style="width: 20px; margin-top: 5px" id = "bq">
        <div id = "bqlist" style="width: 70%;height: auto;"></div>
    </div>


<script>
    var player = new Aliplayer({
            "id": "player-con",
            "source": "http://youke.13366737021.top/myfirstvideo/video.flv?auth_key=1583053422-0-0-bc6c19593c8d13f2b550b3f03d684f60",
            "width": "100%",
            "height": "500px",
            "autoplay": true,
            "isLive": false,
            "rePlay": false,
            "showBuffer": true,
            "snapshot": false,
            "showBarTime": 5000,
            "useFlashPrism": true,
            "skinLayout": [
                {
                    "name": "bigPlayButton",
                    "align": "blabs",
                    "x": 30,
                    "y": 80
                },
                {
                    "name": "controlBar",
                    "align": "blabs",
                    "x": 0,
                    "y": 0,
                    "children": [
                        {
                            "name": "progress",
                            "align": "tlabs",
                            "x": 0,
                            "y": 0
                        },
                        {
                            "name": "playButton",
                            "align": "tl",
                            "x": 15,
                            "y": 26
                        },
                        {
                            "name": "nextButton",
                            "align": "tl",
                            "x": 10,
                            "y": 26
                        },
                        {
                            "name": "timeDisplay",
                            "align": "tl",
                            "x": 10,
                            "y": 24
                        },
                        {
                            "name": "fullScreenButton",
                            "align": "tr",
                            "x": 10,
                            "y": 25
                        },
                        {
                            "name": "streamButton",
                            "align": "tr",
                            "x": 10,
                            "y": 23
                        },
                        {
                            "name": "volume",
                            "align": "tr",
                            "x": 10,
                            "y": 25
                        }
                    ]
                },
                {
                    "name": "fullControlBar",
                    "align": "tlabs",
                    "x": 0,
                    "y": 0,
                    "children": [
                        {
                            "name": "fullTitle",
                            "align": "tl",
                            "x": 25,
                            "y": 6
                        },
                        {
                            "name": "fullNormalScreenButton",
                            "align": "tr",
                            "x": 24,
                            "y": 13
                        },
                        {
                            "name": "fullTimeDisplay",
                            "align": "tr",
                            "x": 10,
                            "y": 12
                        },
                        {
                            "name": "fullZoom",
                            "align": "cc"
                        }
                    ]
                }
            ]
        }, function (player) {
            console.log("The player is created");
        }
    );
</script>

<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

<script>
    var username = $('#username').html()
    var ws = new WebSocket("ws://39.96.53.219:9502");
    ws.onopen = function () {
        var message = '{"type":"login","con":"'+username+'"}';
        ws.send(message);
    }
    ws.onmessage = function (res) {
        var data = JSON.parse(res.data);
        console.log(data);
        if(data.is_me == 1 && data.type == 'login'){
            var content = "<p style='text-align: center'>尊降的用户:"+data.username+"欢迎您</p>"
        }else if (data.is_me == 0 && data.type == 'login'){
            var content = "<p style='text-align: center'>系统提醒:"+data.username+"上线了</p>"
        }else if(data.is_me == 1 && data.type == 'message'){
            var content = "<div style='text-align: right' ><p>"+data.username+"</p><p style='border: solid 1px darkolivegreen; background-color: aqua;margin-right: 20px;'>"+data.message+"</p></div>";
        }else if(data.is_me == 0 && data.type == 'message'){
            var content = "<div style='text-align: left' ><p>"+data.username+"</p><p style='border: solid 1px darkolivegreen; background-color: chartreuse;margin-left: 20px;' >"+data.message+"</p></div>";
        }else if(data.is_me == 0 && data.type == 'loginout'){
            var content = "<p style='text-align: center'>系统提醒:"+data.username+"离开了直播间</p>"

        }

        var list = '在线用户列表';
        for(var i in data.online_list){
            list +="<p>"+data.online_list[i].username+"</p>";
        }
        console.log(list);

        $('.onlinelist').html(list);
        $("#list").append(content);
    }

    $(document).on('click','#btn',function () {
        var con = $("#message").val();
        var message = '{"type":"message","con":"'+con+'"}';
        ws.send(message);
    })

    $(document).on('click','#bq',function () {
        $.ajax({
            url: './bq.php',
            dataType:'json',
            success:function (res) {
                //如果返回值是纯黑色字体    字符串
                //var data =eval("("+res+")");   使用这个函数进行转换
                var img ='';
                for(var i in res){
                    img +="<img class='bqimg' src='./bq/"+res[i]+"' style='width: 75px;height: 75x;' >";
                }
                $("#bqlist").html(img);
            }
        })
    })

    $(document).on('click','.bqimg',function () {
        var src = $(this).attr('src');
        var con = "<img src='"+src+"' style='width: 75px;height: 75x;'>";
        var message = '{"type":"message","con":"'+con+'"}';
        ws.send(message);
    })
</script>

</body>
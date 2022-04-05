<!DOCTYPE html>
<!-- saved from url=(0042)file:///Users/techbin/Desktop/loginreg.htm -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>登录</title>
    <link rel="stylesheet" type="text/css" href="./login_files/dajie.64590.css">
    <link rel="stylesheet" type="text/css" href="./login_files/login.64590.css">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>
    <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>


</head>

<body> 
   <div id="loginview" class="registerBox register" >
                                <div class="loginDialogContent" style="padding:0">
                                    <div class="dialogTitle collar">快ui登录注册
                                    </div>

                                    <div class="formBox">
                                        <form id="register_form" novalidate="novalidate" method="POST" action=" ">
                                            <input type="hidden" name="regSource" value="1">
                                            <input type="hidden" name="identity" value="1">
                                            <ul>
                                                <li class="li_wrap_login">
                                                    <span class="inputTitle account"></span>

                                                    <input style="width:200px;margin-left:10px" type="text" name="email" id="email" placeholder="请输入邮箱" title="请输入邮箱" class="login_checkinput" autocomplete="off">
                                                    <em class="iconInput"></em>
                                                    <span class="errorText"></span>
                                                </li>
                                                <div id="email_autolist"></div>
                                                <li class="li_wrap_login">
                                                    <span class="inputTitle password"></span>
                                                    <input style="width:200px;margin-left:10px"  type="password" placeholder="设置密码" title="设置密码" id="pass" class="login_checkinput" autocomplete="new-password">
                                                    <em class="iconInput"></em>
                                                    <span class="errorText"></span>
                                                </li>
 
                                            </ul>
      
                                            <p class="clause li_wrap_login"><i class=""></i><span>我已阅读并同意<a href="https://kuaiui.cn/about" target="_blank">《kuaiui服务条款》</a></span>

                                                <span class="errorText" style="display: none">登录需要同意此条款</span>
                                            </p>
                                            <a id="TencentCaptcha" data-appid="2056038059" data-cbfn="callback" class="submitA">立即登录</a>
                                            <!--  置灰样式 submitA 添加class='noP' 文本显示登录中... -->



                                        </form>
                                    </div>
                                </div>

                            

                            </div>

<div id="selecttask" style="display:none">
        <a href="javascript:reloagin()">登录其他账号</a>
        <br/><br/><br/><br/><br/><br/> 
        <h3 style="font-size:19px">选择项目</h3> 
        <div id="taskview"></div> 
        <div class="formBox" style="width:100%"> 
        <a  class="submitA" style="width:100%" href="javascript:runexport()" >导出项目</a>
        </div>
</div>
   </body>

<script>
var exportfun=null;
var usertoken=""; 
// document.getElementById("panel").innerHTML=JSON.stringify(data);
function setexport(fun){
   exportfun=fun;
}
function runexport(){
    var taskid=$("#taskid").val();
    console.log(usertoken,taskid)
    exportfun(usertoken,taskid);
}
$(document).ready(function (){  
    getuserprojectlist(getQueryVariable("usertoken"));
});
function getQueryVariable(variable){
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}
function getuserprojectlist(token){
    usertoken=token;
    $.post("/api/Home/Index/getuserprojectlist",{"usertoken":token},function(result){
        if(result.status===true){
            
            document.getElementById("selecttask").style.display="block";
            document.getElementById("loginview").style.display="none";
            var data=result.data;
            var divhtml="";
            for(var i=0;i<data.length;i++){ 
                var itemid=data[i]['id'];
                var name=data[i]['name']; 
                divhtml=divhtml+`<option value="${itemid}">${name}</option>`; 
            
            }
            var div = document.createElement('div');
             var taskview = document.getElementById('taskview');
             
            div.innerHTML = `<select id="taskid" style="width:100%;height:60px" >${divhtml}</select>`;
            taskview.appendChild(div);  
        } 
    });
}
function reloagin(){
     window.location.href = window.location.href.replace(window.location.search,"");
}

window.callback = function(res){
    console.log(res)
    // res（用户主动关闭验证码）= {ret: 2, ticket: null}
    // res（验证成功） = {ret: 0, ticket: "String", randstr: "String"}
    if(res.ret === 0){
        console.log(res)
        // alert(res.ticket)   // 票据
        if($("#email").val()==""  || $("#pass").val()==""){
            alert("用户或密码填写错误");
            return;
        }
        console.log(res)
        var mail=$("#email").val();
        var pass=$("#pass").val();
        $.post("/api/Home/Index/login",{mail,pass},function(result){
            if(result.code=="0000"){  
               getuserprojectlist(result.token)
            }
        });
    }
}
</script>




</body></html>
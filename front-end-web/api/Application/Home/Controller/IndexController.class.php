<?php
namespace Home\Controller;

use Think\Controller;

header("Content-type: application/json;charset=utf-8");
class IndexController extends Controller
{
    public function _initialize(){ 
        session_start();
        setcookie('PHPSESSID', session_id(), time() + 300000000,"/");

    }
    public function getartboard(){
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        echo M('project')->where([ "filepathurl"=>$_GET['f']])->select()[0]['json'];
    }
    public function index()
    {
        echo "error";
    }
    public function sketch_upload_url(){
        echo "http://uploadsketch.kuaiui.cn/Home/index/uploadv22";
    }
    public function sketch_upload_url_v22(){
        echo "http://uploadsketch.kuaiui.cn/Home/index/uploadv22";
    }
    public function sketch_go_login(){
        $usertoken=$_GET['usertoken'];
        $taskid=$_GET['taskid'];
        header('HTTP/1.1 302 Moved Permanently');
        header("Location: https://kuaiui.cn/login/login_sketch.php?usertoken=$usertoken&taskid=$taskid");
    }
    
    public function reg()
    {
        $mail=I('mail');
        $pass=I('pass');
        $phone=I('phone');
        $userisreg=count(M('user')->where(["mail"=>$mail])->select())>0;
        if($userisreg){
            echo json_encode(["code"=>"0001","msg"=>"账号已注册"]);
            return;
        }
        if(empty($mail) || empty($pass)){
            echo json_encode(["code"=>"0001","msg"=>"用户名或密码不能为空"]);
            return;
        }
        if(strlen($pass)<6){
            echo json_encode(["code"=>"0001","msg"=>"密码长度不能少于6位"]);
            return;
        }
        $pass=md5("kuaiui".$pass);
        $token=md5("kuaiui".$mail.$pass);
        $userid=M('user')->add(['mail'=>$mail,'pass'=>$pass,'phone'=>$phone,'token'=>$token]); 
        M('project')->add(['userid'=>$userid,'name'=>'默认项目']); 
        $_SESSION["usertoken"] = $token;
        echo json_encode(["code"=>"0000","msg"=>"登录成功","token"=>$token]);
    }
    
    public function login()
    {
        $mail=I('mail');
        $pass=I('pass');
        $pass=md5("kuaiui".$pass);
        $useriscanlogin=count(M('user')->where(["mail"=>$mail,"pass"=>$pass])->select())>0;
        if($useriscanlogin){
            $token=md5("kuaiui".$mail.$pass);
            M('user')->where(['mail'=>$mail,'pass'=>$pass])->save(['token'=>$token]); 
            $_SESSION["usertoken"] = $token;
            echo json_encode(["code"=>"0000","msg"=>"登录成功","token"=>$token]);
            return;
        } 
        echo json_encode(["code"=>"0001","msg"=>"用户名密码错误"]);
    }
  
    public function getuserprojectlist(){
        $usertoken=I("usertoken");
        $userinfoarr=M('user')->where(["token"=>$usertoken])->select();
        $userisok=count($userinfoarr)>0;
        $userid=$userinfoarr[0]['id'];
        $projectarr=$userisok?M('project')->where(["userid"=>$userid])->select():null;
        echo json_encode(['status'=>$userisok,"data"=>$projectarr]);
    }
    
    public function getnewboard(){
        $taskid=I("taskid");
        $project=M('project')->where(["id"=>$taskid])->select();
        echo json_encode(['boardname'=>$project[0]['boardname']]);
    }
    
    
    public function getcdnurl(){
        echo json_encode(["data"=>"https://cdn2.kuaiui.cn"]);
    }
    
    public function getcdnurlarr(){
        echo json_encode(["data"=>["http://cdn1.kuaiui.cn","https://cdn2.kuaiui.cn"]]);
    }
    
    public function getboardfile(){  
        header('Content-type: text/javascript');
        $artboardfile = "http://uploadsketch.kuaiui.cn/Uploads".I("f").I("n").".json"; 
        $data = file_get_contents($artboardfile);
        echo "var smApp = SMApp($data);  	if(!smApp.project.codeArtboards){ 	$('#show-codes').hide(); 	}else{ 	$('#code-hidden').click(); 	}";
    }
    
    
}
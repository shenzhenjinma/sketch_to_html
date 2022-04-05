<?php
namespace User\Controller;

use Think\Controller;
header("Content-type: application/json;charset=utf-8");
class IndexController extends Controller
{
    private $usertoken = "";
    public function _initialize(){
        session_start();
        session_set_cookie_params(10 * 365 * 24 * 3600,"/");
        $usertoken =  $_SESSION['usertoken'];
        $this->usertoken = $_SESSION['usertoken'];
        $userisok=count(M('user')->where(["token"=>$usertoken])->select())>0;
        if(!$userisok){
            echo json_encode(['code'=>'0002','msg'=>'登录信息错误请重新登录'.$usertoken]);
            exit();
        }
    }
    public function index(){
        echo "";
    }
    public function test(){
        
         
    }
    public function loginout(){
        $_SESSION['usertoken']=""; 
        echo json_encode(['code'=>'0000','msg'=>'退出成功']);
    }
    public function addproject(){
        $name=I('name');
        $usertoken=$this->usertoken;
        $userid=M('user')->where(["token"=>$usertoken])->select()[0]['id'];
        $nameisok=count(M('project')->where(["userid"=>$userid,"name"=>$name])->select())>0;
        if($nameisok){
            echo json_encode(['code'=>'0001','msg'=>'名字重复，添加失败']);
            return;
        }
        $userid=M('project')->add(["userid"=>$userid,"name"=>$name]);
        echo json_encode(['code'=>'0000','msg'=>'添加成功']);
    }
    public function editproject(){
        $projectid=I('projectid');
        $name=I('name');
        $usertoken=$this->usertoken;
        $userid=M('user')->where(["token"=>$usertoken])->select()[0]['id'];
        $nameisok=count(M('project')->where(["userid"=>$userid,"id"=>$projectid])->select())>0;
        if(!$nameisok){
            echo json_encode(['code'=>'0001','msg'=>'修改失败，找不到该项目']);
            return;
        }
        $userid=M('project')->where(["userid"=>$userid,"id"=>$projectid])->save(['name'=>$name]);
        echo json_encode(['code'=>'0000','msg'=>'添加成功']);
    }
    public function getallproject(){ 
        $usertoken=$this->usertoken;
        $userid=M('user')->where(["token"=>$usertoken])->select()[0]['id'];
        if($userid==2){
            $projectarr = M('project')->where("filepathurl!='NULL'")->select(); 
        }else{
            $projectarr = M('project')->where(["userid"=>"$userid"])->select(); 
        }
        echo json_encode(['code'=>'0000','msg'=>'查询成功','data'=>$projectarr]);
    }
    
    public function delobjectid(){
        $objectid=I('objectid');
        $taskid=I('taskid');
        $usertoken=$this->usertoken; 
        $getdata=file_get_contents("http://uploadsketch.kuaiui.cn:10080/Home/Index/delobjectid?usertoken=${usertoken}&taskid=${taskid}&delobjectid=${objectid}");
        echo $getdata;
    }
          
    
    
    
    
    
    
    
    
}
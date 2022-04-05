<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->show('success','utf-8');
    }
    /** 
     * 解压文件
     * @param   string   zip压缩文件的路径
     * @param   string   解压文件的目的路径
     * @param   boolean  是否以压缩文件的名字创建目标文件夹
     * @param   boolean  是否重写已经存在的文件
     * @return  boolean  返回成功 或失败
     */
    public function unzip($src_file='',$dest_dir=false, $create_zip_name_dir=false, $overwrite=true){
        if ($zip = zip_open($src_file)){
            if ($zip){
                $splitter = ($create_zip_name_dir === true) ? "." : "/";
                if($dest_dir === false){
                    $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";
                }
                // 如果不存在 创建解压目录
                mkdir($dest_dir);
                // 对每个文件进行解压
                while ($zip_entry = zip_read($zip)){
                    // 文件不在根目录
                    $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
                    if ($pos_last_slash !== false){
                        // 创建目录
                        mkdir($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
                    }
                    // 打开包
                    if (zip_entry_open($zip,$zip_entry,"r")){
                        // 文件名保存在磁盘上
                        $file_name = $dest_dir.zip_entry_name($zip_entry);
                        // 检查文件是否需要重写
                        if ($overwrite === true || $overwrite === false && !is_file($file_name)){
                            // 读取压缩文件的内容
                            $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                            @file_put_contents($file_name, $fstream);
                            chmod($file_name, 0755); 
                        }
                        // 关闭入口
                        zip_entry_close($zip_entry);
                    }
                }
                // 关闭压缩包
                zip_close($zip);
            }
        }else{
            return false;
        }
        return true;
    }
    public function test2(){
 $src_img = "/Uploads/2020-08-19/5b89410d5c255e8c3078c5a2f7837a8a/preview/%E8%A7%86%E8%A7%89%E6%96%B9%E6%A1%88-%E4%B8%80%E9%94%AE%E6%9D%80%E6%AF%92%E5%A4%B1%E8%B4%A5%E5%8E%9F%E5%9B%A0.png"; //原图片完整路径和名称，带图片扩展名
$dst_img = "/Uploads/2020-08-19/5b89410d5c255e8c3078c5a2f7837a8a/preview/%E8%A7%86%E8%A7%89%E6%96%B9%E6%A1%88-%E4%B8%80%E9%94%AE%E6%9D%80%E6%AF%92%E5%A4%B1%E8%B4%A5%E5%8E%9F%E5%9B%A0.png_t.png"; //生成的缩略图存放的完整路径和名称
/* 生成宽300px，高200px的缩略图，不进行裁切，空白部分将会使用背景色填充 */
var_dump( $this->img2thumb($src_img, $dst_img, $width = 300, $height = 200, $cut = 0, 0));



    }
    public function test(){ 
        // $id=$_GET['id'];
        // $projectarr=M('project')->where([ "id"=>$id])->select(); 
        //      $item=$projectarr[0];
        //      $taskid=$item['id'];
        //       $rootpath = $_SERVER['DOCUMENT_ROOT']."/Uploads";
        //     //   copy($rootpath.$item['filepathurl']."lastartboard.json",$rootpath.$item['filepathurl'].$item['boardname'].".json");
              
              
              
              
        //     $artboardfile = $rootpath.$item['filepathurl'].$item['boardname'].".json";
        //      $artboardfilestr = file_get_contents($artboardfile);
        //     $artboard =json_decode($artboardfilestr);
        //     $artboard = $this->changeartboardimgpath2($artboard,$item['filepathurl']); 
             
        //     $myfile = fopen($rootpath.$item['filepathurl'].$item['boardname'].".json", "w") or die("Unable to open file!"); 
        //     fwrite($myfile,json_encode($artboard));
        //     fclose($myfile); 
        //     // unlink($rootpath.$item['filepathurl']."artboard.json");
            
      
        // $id=$id+1; 
        // echo "<script>";
        // echo "setTimeout(()=>{window.location.href='http://uploadsketch.kuaiui.cn:10080/Home/Index/test?id=".$id."'},1000)"; 
        // echo "</script>";
    }
 
    public function delobjectid(){
        $usertoken=I("usertoken");
        $taskid=I("taskid");
        $delobjectid=I("delobjectid");
        if(empty($delobjectid)){
            echo json_encode(["code"=>"-1","msg"=>"删除的id不能为空"]);
            return;
        }
        $userinfoarr=M('user')->where(["token"=>$usertoken])->select();
        $userisok=count($userinfoarr)>0;
        if(!$userisok){ 
            echo json_encode(["code"=>"-1","msg"=>"用户信息错误"]);
            return;
        }
        $userid=$userinfoarr[0]['id']; 
        $projectarr=M('project')->where(["userid"=>$userid,"id"=>$taskid])->select();
        $taskisok=count($projectarr)>0; 
        if(!$taskisok){ 
            echo json_encode(["code"=>"-1","msg"=>"找不到删除的id文件"]);
            return;
        }
        $rootpath = $_SERVER['DOCUMENT_ROOT']."/Uploads";
        $filepathurl=$projectarr[0]['filepathurl'];
        $boardname=$projectarr[0]['boardname'];
        $lastartboardpath=$rootpath.$filepathurl.$boardname.".json";
        
        if(!file_exists($lastartboardpath)){//如果没有lastartboard文件说明第一次生成 
            echo json_encode(["code"=>"-1","msg"=>"删除出错"]);
            return;
        }
    
        $newartboard = json_decode(file_get_contents($lastartboardpath)); 
        if(count($newartboard->artboards)==1){
            echo json_encode(["code"=>"-1","msg"=>"仅剩一个画板，不能删除"]);
            return;
        }
        
        $isdeled=false;
        $newartboardsarr=[];
        for ($i = 0; $i < count($newartboard->artboards); $i++) { 
            if($newartboard->artboards[$i]->objectID==$delobjectid){
                $isdeled=true; 
            }else{
                $newartboardsarr[]=$newartboard->artboards[$i];
            }
        }
        if($isdeled==false){//如果没有lastartboard文件说明第一次生成 
            echo json_encode(["code"=>"-1","msg"=>"没有需要删除的内容"]);
            return;
        }
        $newartboard->artboards=$newartboardsarr;
    
        $tempboardname=date("Ymd").md5($boardname.$delobjectid);//生成新的boardname
        $newartboardpath=$rootpath.$filepathurl.$tempboardname.".json";
         
        
        
        $myfile = fopen($newartboardpath, "w") or die("Unable to open file!"); 
        fwrite($myfile,json_encode($newartboard));
        fclose($myfile);
         
    
        M('project')->where(["userid"=>$userid,"id"=>$taskid])->save(["boardname"=>$tempboardname]);
      
        M('task')->add(["userid"=>$userid,"projectid"=>$taskid,"delobjectid"=>$delobjectid]);
        echo json_encode(["code"=>"0000","nowboardname"=>$tempboardname]);
    }
    
    public function uploadv22(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     314572800 ;// 设置附件上传大小
        $upload->exts      =     array('zip');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录  
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
          echo "error".json_encode($info);
        }else{// 上传成功 
            $rootpath = $_SERVER['DOCUMENT_ROOT']."/Uploads";
            $abspath = "/".$info['filename']['savepath'].$info['filename']['md5']."/";
            $zipfilepath =  "./Uploads/".$info['filename']['savepath'].$info['filename']['savename'];
            $savepath =  $rootpath.$abspath; 
            $nodejsfile = "/www/server/nvm/versions/node/v12.18.2/bin/node";
            $kuaiuimakevuefile = "/home/kuaiuitool/kuaiui.js";
            $artboardfile = $savepath."artboard.json";
            $this->unzip($zipfilepath,$savepath);
            unlink($zipfilepath); //删除zip文件
            $artboardfilestr = file_get_contents($artboardfile);
            $artboard =json_decode($artboardfilestr);
            $artboard = $this->changeartboardimgpath($artboard,$abspath);//改变一下json的图片路径
            $usertoken=  $artboard->usertoken; 
            $taskid=$artboard->taskid; 
            $basewidthval=$artboard->basewidthval;
            $basewidthval = is_numeric($basewidthval)?$basewidthval:750;
            unset($artboard->usertoken);
            unset($artboard->taskid);
            unset($artboard->basewidthval);
            $userinfoarr=M('user')->where(["token"=>$usertoken])->select();
            $userisok=count($userinfoarr)>0;
            if(!$userisok){
                echo "user error";
                return;
            }
            $userid=$userinfoarr[0]['id']; 
            $projectarr=M('project')->where(["userid"=>$userid,"id"=>$taskid])->select();
            $taskisok=count($projectarr)>0; 
            if(!$taskisok){
                echo "task error";
                return;
            }
            $oneimagepath=$artboard->artboards[0]->imagePath;
            $taskimg=$abspath.$oneimagepath;
            $basepath=isset($projectarr[0]['filepathurl'])?$rootpath.$projectarr[0]['filepathurl']:$savepath;
            $lastartboardpath=$basepath.$projectarr[0]['boardname'].".json";
            
            if(!file_exists($lastartboardpath)){//如果没有lastartboard文件说明第一次生成
                $json=$artboard;
            }else{ 
                $json= $this->changeartboard(json_decode(file_get_contents($lastartboardpath)),$artboard);
            }
            $tempboardname=str_replace("/","",str_replace("-","",$abspath));
            $newartboardpath=$basepath.$tempboardname.".json";
             
            
            
            $myfile = fopen($newartboardpath, "w") or die("Unable to open file!"); 
            fwrite($myfile,json_encode($json));
            fclose($myfile);
             
            if(empty($projectarr[0]['filepathurl'])){ 
                M('project')->where(["userid"=>$userid,"id"=>$taskid])->save(["filepathurl"=>$abspath,"taskimg"=>$taskimg,"boardname"=>$tempboardname]);
            }else{
                M('project')->where(["userid"=>$userid,"id"=>$taskid])->save(["taskimg"=>$taskimg,"boardname"=>$tempboardname]);
            }
            
            M('task')->add(["userid"=>$userid,"projectid"=>$taskid,"filepathurl"=>$abspath,"taskimg"=>$oneimagepath]);
            exec("$nodejsfile $kuaiuimakevuefile $basewidthval $artboardfile $savepath",$a,$b);
            //exec("/usr/bin/zip ")
            echo "success";
        }
    }
    
    
    
    private function changeartboard($oldartboard,$newartboard){
        if(is_null($oldartboard)) return $newartboard;
        for ($i = 0; $i < count($newartboard->artboards); $i++) {
            $newslug=$newartboard->artboards[$i]->slug;
            $isexist=false;
            for ($j = 0; $j < count($oldartboard->artboards); $j++) { 
                $oldslug=$oldartboard->artboards[$j]->slug;
                if($newslug==$oldslug){
                    $oldartboard->artboards[$j]=$newartboard->artboards[$i];
                    $isexist=true;
                    break;
                }
            }
            if(!$isexist){
                $oldartboard->artboards[]=$newartboard->artboards[$i];
            }
        }
        return $oldartboard; 
    }
    
    
    // 改变画布里面的图片地址
    private function changeartboardimgpath($artboard,$abspath){
        for ($i = 0; $i < count($artboard->artboards); $i++) {
            $artboard->artboards[$i]->filepath=$abspath; 
        }
        for ($l = 0; $l < count($artboard->slices); $l++) {
            $artboard->slices[$l]->filepath=$abspath;
        }
        return $artboard;
    }
    // private function changeartboardimgpath2($artboard,$abspath){
    //     for ($i = 0; $i < count($artboard->artboards); $i++) {
            
    //         $layers=$artboard->artboards[$i]->layers;
    //         for ($j = 0; $j < count($layers); $j++) {
    //             $exportable=$artboard->artboards[$i]->layers[$j]->exportable;
                 
    //             for ($k = 0; $k < count($exportable); $k++) {
    //                 $name=$artboard->artboards[$i]->layers[$j]->exportable[$k]->name;
    //                 $path=$artboard->artboards[$i]->layers[$j]->exportable[$k]->path;
    //                 if( strpos($path,$name)!=0){
    //                     $artboard->artboards[$i]->layers[$j]->exportable[$k]->path= substr($path,strpos($path,$name));
       
                        
    //                     if(strpos($artboard->artboards[$i]->imagePath,"/preview")>0){
    //                         $newpath= substr($artboard->artboards[$i]->imagePath,0,strpos($artboard->artboards[$i]->imagePath,"preview"));
    //                         $artboard->artboards[$i]->filepath=$newpath.""; 
    //                          $artboard->artboards[$i]->imagePath=str_replace($newpath,"",$artboard->artboards[$i]->imagePath);
    //                     }
                       
    //                 };
    //               // 
    //             }
    //              // code...
    //         }
             
    //     }
    // // var_dump($artboard);
    //     return $artboard;
    // }
    
    
    
    
    
        /**
      * 生成缩略图
      * @param string     源图绝对完整地址{带文件名及后缀名}
      * @param string     目标图绝对完整地址{带文件名及后缀名}
      * @param int       缩略图宽{值设为0时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
      * @param int       缩略图高{值设为0时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
      * @param int       是否裁切{宽,高必须非0}
      * @param int/float 缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
      * @return boolean
      */
    public function imgthumb(){ 
       header('content-type:image/png');  
        $imgp=I("imgp");
        $imgp=str_replace("imgthumb","",$imgp);
        $src_img=$_SERVER['DOCUMENT_ROOT'].urldecode($imgp);
        $dst_img=$_SERVER['DOCUMENT_ROOT'].urldecode($imgp."_t.png"); 
        $width = 400;
        $height = 540;
        $cut = 1;
        $proportion = 0;
        if(!is_file($src_img)){
            return false;
        } 
        if(is_file($dst_img)){ 
            echo file_get_contents($dst_img);  
            return;
        }
         
        $ot = $this->fileext($dst_img);
        $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
        $srcinfo = getimagesize($src_img);
        $src_w = $srcinfo[0];
        $src_h = $srcinfo[1];
        $type = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
        $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
        $dst_h = $height;
        $dst_w = $width;
        $x = $y = 0;
        /**
          * 缩略图不超过源图尺寸（前提是宽或高只有一个）
          */
        if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
        {
            $proportion = 1;
        }
        if($width> $src_w)
        {
            $dst_w = $width = $src_w;
        }
        if($height> $src_h)
        {
            $dst_h = $height = $src_h;
        }
        if(!$width && !$height && !$proportion)
        {
            return false;
        }
        if(!$proportion)
        {
            if($cut == 0)
            {
                if($dst_w && $dst_h)
                {
                    if($dst_w/$src_w> $dst_h/$src_h)
                    {
                        $dst_w = $src_w * ($dst_h / $src_h);
                        $x = 0 - ($dst_w - $width) / 2;
                    }
                    else
                    {
                        $dst_h = $src_h * ($dst_w / $src_w);
                        $y = 0 - ($dst_h - $height) / 2;
                    }
                }
                else if($dst_w xor $dst_h)
                {
                    if($dst_w && !$dst_h) //有宽无高
                    {
                        $propor = $dst_w / $src_w;
                        $height = $dst_h = $src_h * $propor;
                    }
                    else if(!$dst_w && $dst_h) //有高无宽
                    {
                        $propor = $dst_h / $src_h;
                        $width = $dst_w = $src_w * $propor;
                    }
                }
            }
            else
            {
                if(!$dst_h) //裁剪时无高
                {
                    $height = $dst_h = $dst_w;
                }
                if(!$dst_w) //裁剪时无宽
                {
                    $width = $dst_w = $dst_h;
                }
                $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
                $dst_w = (int)round($src_w * $propor);
                $dst_h = (int)round($src_h * $propor);
                $x = ($width - $dst_w) / 2;
                $y = ($height - $dst_h) / 2;
            }
        }
        else
        {
            $proportion = min($proportion, 1);
            $height = $dst_h = $src_h * $proportion;
            $width = $dst_w = $src_w * $proportion;
        }
        $src = $createfun($src_img);
        $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);
        if(function_exists('imagecopyresampled'))
        {
            imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
        }
        else
        {
            imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
        }
        $otfunc($dst, $dst_img);
        imagedestroy($dst);
        imagedestroy($src);
        echo file_get_contents($dst_img);  
    }
    private function fileext($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }   
    
    
    
 
    
    
    
    
    
    
}
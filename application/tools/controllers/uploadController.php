<?php
/*********************************
 * 模块：    工具模块(TOOLS)
 * 机能：    uploadController
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class tools_uploadController extends gt_controllers_baseController {
	
	public function imageAction(){
		$this->_view->display('image_upload.php');
	
	}
	
	public function uploadhandlerAction(){
		
	    $id  = $_GET['sessionId'];
        $id = trim($id);

    session_name($id);
    //session_start();
    $inputName = $_GET['userfile'];
    $fileName  = $_FILES[$inputName]['name'];
    $tempLoc   = $_FILES[$inputName]['tmp_name'];
    echo $_FILES[$inputName]['error'];
    $target_path = 'c:\\upload\\';
    $target_path = $target_path . basename($fileName);
    if(move_uploaded_file($tempLoc,$target_path))
    {
        $_SESSION['value'] = -1;
    }

	}
	
	public function getinfohandlerAction(){
	    $id = $_POST['sessionId'];
    $id = trim($id);
    session_name($id);
    //session_start();
    echo $_SESSION['value'];
    if($_SESSION['value']==-1)
    {
        session_destroy();
    }
	}
	
	public function getidhandlerAction(){
		    $id = uniqid('id');
    session_name($id);
    //session_start();
    $_SESSION['value'] = 0;
    echo $id;
	}
	
	
	
	
	
}
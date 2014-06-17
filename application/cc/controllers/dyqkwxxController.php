<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   待验区库位信息(dyqkwxx)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/06
 * 更新履历：
 *********************************/
class cc_dyqkwxxController extends cc_controllers_baseController
{
	/*
     * 待验区库位信息页面
     */
	public function indexAction()
	{
		$this->_view->assign('title', '仓储管理-待验区库位信息维护');
        $this->_view->display('dyqkwxx_01.php');
	}
	
	/*
     * 新建待验区库位信息页面
     */
	public function newAction()
	{
		$this->_view->assign("title", "仓储管理-待验区库位信息"); //标题
        $this->_view->display ("dyqkwxx_02.php");
	}
	
	/*
	 * 更新待验区库位信息
	 */
	public function updateAction()
	{
		$this->_view->assign('action', 'update');
		$this->_view->assign("title", "仓储管理-待验区库位信息修改"); //标题
		$filter['dyqkwbh']=str_pad($this->_getParam('dyqkwbh'), 6);   //待验区库位编号
		$filter['ckbh']=str_pad($this->_getParam('ckbh'), 6);   //待验区库位编号 
		$filter['dyqbh']=str_pad($this->_getParam('dyqbh'), 6);   //待验区库位编号  
		$model = new cc_models_dyqkwxx();
		$this->_view->assign("rec",$model->getDyqkwxx($filter));
        $this->_view->display ("dyqkwxx_03.php");
	}
	
    /*
     * 待验区库位信息详细信息页面
     */
    public function detailAction()
    {
    	$model = new cc_models_dyqkwxx();
        $filter['ckbh'] = $this->_getParam('ckbh'); //仓库编号
        $filter['dyqbh'] = $this->_getParam('dyqbh'); //待验区编号
        $filter['dyqkwbh'] = $this->_getParam('dyqkwbh'); //待验区库位编号
        $this->_view->assign("rec", $model->getDyqkwxx($filter));
        $this->_view->assign ( "full_page", 1 );
        $this->_view->assign('title', '仓储管理-待验区库位信息详情');
        $this->_view->display("dyqkwxx_04.php");
    }
	
	/*
     * 待验区选择页面
     */
	public function dyqlistAction()
	{
		$this->_view->assign("title", "仓储管理-待验区信息"); //标题
		$this->_view->assign("ckbh", $this->_getParam('ckbh'));
        $this->_view->display ("dyqkwxx_05.php");
	}
	
	/*
	 * 获取待验区库位信息
	 */
	public function getlistdataAction()
	{
		//取得分页排序参数
        $filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
        $filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
        $filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
        $filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式       
        //保持排序条件
        $_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
        $_SESSION["sortParams"]["direction"] = $filter ['direction'];
        
        //一般查询
        if($this->_getParam ( "isfilter", '0' )=='0'){
           //取得一般查询条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['dyqkwxx_searchParams'] = $_POST;
                unset($_SESSION['dyqkwxx_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['dyqkwxx_filterParams'] = $_POST;
                unset($_SESSION['dyqkwxx_searchParams']); //清空一般查询条件
            }
        }

        //取得检索条件
        $filter['filterParams'] = $_SESSION['dyqkwxx_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['dyqkwxx_searchParams'];  //固定查询条件
    
        $model = new cc_models_dyqkwxx();
        header ( "Content-type:text/xml" ); //返回数据格式xml     
        echo $model->getGridData ( $filter );
	}
	
	/**
     * 获取仓库状态信息
     *
     */
	public function ckstatuscheckAction()
	{
		$ckbh = $this->_getParam('ckbh');   //仓库编号获取
		$Model = new cc_models_dyqkwxx();
		echo Common_Tool::json_encode($Model->ckstatusCheck($ckbh));
	}
	
	/**
     * 获取待验区状态信息
     *
     */
	public function dyqbhcheckAction()
	{
		$dyqbh = $this->_getParam('dyqbh');   //待验区编号获取
        $Model = new cc_models_dyqkwxx();
        echo Common_Tool::json_encode($Model->dyqbhChec($dyqbh));
	}
	
	/**
     * 获取待验区库位信息
     *
     */
	public function dyqkwbhcheckAction()
	{
		$filter['actions'] = $this->_getParam('actions');
		$filter['dyqkwbh'] = str_pad($this->_getParam('DYQKWBH'), 6);   //待验区库位编号获取
		$filter['ckbh'] = str_pad($this->_getParam('CKBH'), 6);   //待验区库位编号获取
		$filter['dyqbh'] = str_pad($this->_getParam('DYQBH'), 6);   //待验区编号获取
//		if ($filter['actions'] == 'updatedata')
//		{
//			$filter['ydyqkwbh'] = str_pad($this->_getParam('YDYQKWBH'), 6);   //待验区库位编号获取
//	        $filter['yckbh'] = str_pad($this->_getParam('YCKBH'), 6);   //待验区库位编号获取
//	        $filter['ydyqbh'] = str_pad($this->_getParam('YDYQBH'), 6);   //待验区编号获取
//		}
        $Model = new cc_models_dyqkwxx();
        echo Common_Tool::json_encode($Model->dyqkwbhCheck($filter));
	}
	
	/**
     * 获取待验区信息
     *
     */
	public function getdyqlistdataAction()
	{
		$filter['ckbh'] = $this->_getParam("ckbh");   //仓库编号获取
		
		//取得分页排序参数
        $filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
        $filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
        $filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
        $filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式       
        //保持排序条件
        $_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
        $_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
        //一般查询
        if($this->_getParam ( "isfilter", '0' )=='0'){
           //取得一般查询条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['dyq_searchParams'] = $_POST;
                unset($_SESSION['dyq_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['dyq_filterParams'] = $_POST;
                unset($_SESSION['dyq_searchParams']); //清空一般查询条件
            }
        }

        //取得检索条件
        $filter['filterParams'] = $_SESSION['dyq_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['dyq_searchParams'];  //固定查询条件
        
		
		$Model = new cc_models_dyqkwxx();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $Model->getDyqListData($filter);
	}
	
	/**
     * 保存待验区库位信息
     *
     */
	public function saveAction()
	{
		$statu=$this->_getParam('actions');
		$result['status'] = '0';
		$Model = new cc_models_dyqkwxx();
		$retFlg = $Model->inputCheck();
		if(!$retFlg)
		{
			$result['status'] = '1';  //必须输入项验证错误
            echo json_encode($result);
            return true;
		}
		//保存
		if($statu == "addnew")
		{
			//保存待验区库位信息
	        $Model->save();
	        Common_Logger::logToDb("待验区库位信息登陆 待验区库位编号：".$_POST['DYQKWBH']);
	        echo json_encode($result);
		}
		//更新
		else if($statu == "updatedata")
		{
			//时间戳判断
			$filter['dyqkwbh'] = str_pad($this->_getParam('DYQKWBH'), 6);   //待验区库位编号获取
	        $filter['ckbh'] = str_pad($this->_getParam('CKBH'), 6);   //待验区库位编号获取
	        $filter['dyqbh'] = str_pad($this->_getParam('DYQBH'), 6);   //待验区编号获取
	        $filter['timestamp'] = $this->_getParam('BGRQ');
	        if($Model->getTime($filter) == false)
	        {
	        	//时间戳发生变化
	        	$result['status'] = '2';
	        	echo json_encode($result);
	        	return true;
	        }else{
	        	$result['status'] = '0';
	        }
	        $filter['dyqkwmch'] = $this->_getParam('DYQKWMCH');
	        $Model->updateData($filter);
	        Common_Logger::logToDb("待验区库位信息修改 仓库编号：" . $filter['ckbh'] . " 待验区编号：" . $filter['dyqbh'] . "待验区库位编号：" . $filter['dyqkwbh'] );
	       echo json_encode($result);
		}
		 
	}
	
	/*
	 * 待验区库位信息状态变更
	 */
	public function changestatusAction()
	{
		$result['status'] = '0';
		$model = new cc_models_dyqkwxx();
		$filter['ckbh'] = $this->_getParam('ckbh');   //获取仓库编号
		$filter['dyqbh'] = $this->_getParam('dyqbh');   //获取待验区编号
		$filter['dyqkwbh'] = $this->_getParam('dyqkwbh');   //获取待验区库位编号
		$filter['dyqkwzht'] = $this->_getParam('dyqkwzht');   //状态
		
		if($filter['dyqkwzht'] == '1')
		{
			if($model->getStatus($filter) != '1')     //不正常状态
			{
				//判断当前待验区库位所在待验区状态是否正常
				if($model->getDyqStatus($filter) == '1')
				{
					//状态正常
					$model->updateStatus($filter);
					Common_Logger::logToDb("待验区库位信息维护 启用待验区库位 待验区库位编号：" . $filter['dyqkwbh']);
				}else
				{
					//状态不正常 请先变更该待验区库位所在的待验区为正常状态。
					$result['status'] = '1';
					echo json_encode($result);
					return true;
				}
			}
			
		}
		else if($filter['dyqkwzht'] == 'X')
		{
			if($model->getStatus($filter) != 'X')
			{
				$model->updateStatus($filter);
				Common_Logger::logToDb("待验区库位信息维护 待验区库位禁用 待验区库位编号：" . $filter['dyqkwbh']);
			}
		}
		echo json_encode($result);
	}
	
	/*
	 * 获取待验区库位信息
	 */
	public function getdyqkwxxAction()
	{
		$model = new cc_models_dyqkwxx();
		$filter['ckbh'] = $this->_getParam('ckbh');   //获取仓库编号
        $filter['dyqbh'] = $this->_getParam('dyqbh');   //获取待验区编号
        $filter['dyqkwbh'] = $this->_getParam('dyqkwbh');   //获取待验区库位编号
        $flg = $this->_getParam ( 'flg', "current" );//检索方向
        $filter['filterParams'] = $_SESSION['dyqkwxx_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['dyqkwxx_searchParams'];  //固定查询条件
        $filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
        $filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
	    $rec = $model->getDyqkwxx ($filter, $flg);
        //没有找到记录
        if ($rec == FALSE) {
            echo 'false';
        } else {
            $this->_view->assign ( "rec", $rec );
            echo  $this->_view->fetchPage ("dyqkwxx_04.php");
        }
	}
}
?>
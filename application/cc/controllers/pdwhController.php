<?php

/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   盘点维护(pdwh)
 * 作成者：dltt
 * 作成日：2010/11/10
 * 更新履历：

 *********************************/
class cc_pdwhController extends cc_controllers_baseController
{

	/*
     * 盘点维护
     */
	public function indexAction()
	{
		$this->_view->assign ( 'action', 'new' ); //登录
		$this->_view->assign ( 'title', '仓储管理-盘点维护' );
		$this->_view->assign ( "stats_opts", array ('9' => '- - 请 选 择 - -', '1' => '盘点开始', '2' => '盘点结束' ) );
		$this->_view->display ( 'pdwh_01.php' );
	
	}

	/*
	 * 盘点结束弹出画面前，判断处理
	 * 
	 */
	public function checkAction()
	{
		$result = array (); //定义返回值
		$model = new cc_models_pdjs ( );
		$djbh = $this->_getParam ( "djbh", "" );
		$rec = $model->getPdjsOne ( $djbh );
		
		//该盘点信息为已结束状态，请确认
		if ($rec != false)
		{
			$result ['endstatus'] = 0;
		}
		else
		{
			$result ['endstatus'] = 9;
		}
		echo Common_Tool::json_encode ( $result );
	}

	/*
	 * 盘点维护画面数据取得
	 */
	public function getlistdataAction()
	{
		//画面数据
		//		$filter ['djbh'] = $this->_getParam("DJBH","");
		//		$filter ['pdzht'] = $this->_getParam("PDZHT","");
		// 		$filter ['pdkshshj'] = $this->_getParam("PDKSHSHJ","");
		//		$filter ['pdjshshj'] = $this->_getParam("PDJSHSHJ","");
		//		$filter ['posStart'] = $this->_getParam("posStart",0);
		//        $filter ['count'] = $this->_getParam("count" ,10);
		//		$filter ['orderby'] = $this->_getParam("orderby",5);
		//		$filter ['direction'] = $this->_getParam("direction",'DESC');
		

		//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", "1" ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", "ASC" ); //排序方式       
		//保持排序条件
		$_SESSION ["pdwh_sortParams"] ["orderby"] = $filter ['orderby'];
		$_SESSION ["pdwh_sortParams"] ["direction"] = $filter ['direction'];
		
		//一般查询
		if ($this->_getParam ( "isfilter", '0' ) == '0')
		{
			//取得一般查询条件参数并保存至session
			if ($this->_request->isPost ())
			{
				$_SESSION ['pdwh_searchParams'] = $_POST;
				unset ( $_SESSION ['pdwh_filterParams'] ); //清空精确查询条件
			}
		
		}
		else
		{ //精确查询
			//取得过滤条件参数并保存至session
			if ($this->_request->isPost ())
			{
				$_SESSION ['pdwh_filterParams'] = $_POST;
				unset ( $_SESSION ['pdwh_searchParams'] ); //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter ['filterParams'] = $_SESSION ['pdwh_filterParams']; //精确查询条件
		$filter ['searchParams'] = $_SESSION ['pdwh_searchParams']; //固定查询条件
		

		$model = new cc_models_pdwh ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml     
		echo $model->getListData ( $filter );
	}

	/*
     * 盘点详细生成画面显示
     */
	public function detailAction()
	{
		//	  $djbh = $this->_getParam("djbh"); //盘点单据号
		//        $filter ['pdkshshj'] = $this->_getParam ( "pdkshshj", '' ); //开始时间
		//        $filter ['pdjshshj'] = $this->_getParam ( "pdjshshj", '' ); //结束时间
		//        $filter ['orderby'] = $this->_getParam ( "orderby",1 ); //排序列
		//        $filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		//        $filter ['pdzht'] = $this->_getParam("PDZHT","");
		//        $filter ['djbhwh'] = $this->_getParam ( "DJBHWH", '' );
		//        $flg = $this->_getParam ( 'flg', 'current' );//检索方向
		//        
		//        $model = new cc_models_pdwh();
		//        
		//        $rec = $model->getPdwhOne($djbh,'');
		//          $model = new cc_models_pdwh();
		//          $rec  = $model->getPdwhOne($this->_getParam("djbh",''));
		//          //数据不可更改
		//	      $this->_view->assign ( 'disabled', 'disabled' );
		//	      $this->_view->assign ( 'disabledbm', 'disabled');
		//	      $this->_view->assign ( 'disableduser', 'disabled');
		//	      $this->_view->assign ( 'action', 'detail' );                                
		//        $this->_view->assign ( 'title', '基础管理-盘点详细' );
		//        
		//        //画面赋值
		//        $this->_view->assign ( 'pdkshshj', $filter ['pdkshshj'] );
		//        $this->_view->assign ( 'pdjshshj', $filter ['pdjshshj'] );
		//        $this->_view->assign ( 'zhtai', $filter ['pdzht'] );
		//        $this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) );    //列表画面排序
		//        $this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) );//列表画面排序
		//        $this->_view->assign ( "djbhwh",$filter ['djbhwh'] );
		//        $this->_view->assign ( "rec", $rec);
		//        //
		//        //账面数量条件设定
		//        switch($rec[ZHMSHLTJ]){
		//
		//            case 1 :
		//                $this->_view->assign ( 'check1', 'checked');    
		//                break;
		//            case 2 :
		//                $this->_view->assign ( 'check2', 'checked');
		//                break;
		//            case 3 :
		//                $this->_view->assign ( 'check3', 'checked');
		//                break;  
		//            default:
		//                $this->_view->assign ( 'check1', 'checked');
		//                break;  
		//        }
		//        
		//        //冻结标志设定
		//        if ($rec[DJBZH]==1){
		//            $this->_view->assign ( 'check4', 'checked');    
		//        } 
		//        
		//        //翻页用
		//        if ($flg !='current'){
		//            
		//            //上一页 下一页
		//            if ($rec == FALSE) {
		//                //当返回为空时
		//                echo 'false';
		//            }else{
		//                //上一页 下一页数据存在时
		//                $this->_view->assign ( "full_page", 0 );
		//                echo  $this->_view->fetchPage ('pdwh_02.php' ) ;    
		//            }   
		//        }else{
		//            //第一次进入详细画面时
		//            $this->_view->assign ( "full_page", 1 );
		//            $this->_view->display ( 'pdwh_02.php' );
		//        }
		

		$model = new cc_models_pdwh ( );
		$rec = $model->getPdwhOne ( $this->_getParam ( "djbh", '' ) );
		//数据不可更改
		$this->_view->assign ( 'disabled', 'disabled' );
		$this->_view->assign ( 'disabledbm', 'disabled' );
		$this->_view->assign ( 'disableduser', 'disabled' );
		$this->_view->assign ( 'action', 'detail' );
		//账面数量条件设定
		switch ($rec [ZHMSHLTJ])
		{
			
			case 1 :
				$this->_view->assign ( 'check1', 'checked' );
				break;
			case 2 :
				$this->_view->assign ( 'check2', 'checked' );
				break;
			case 3 :
				$this->_view->assign ( 'check3', 'checked' );
				break;
			default :
				$this->_view->assign ( 'check1', 'checked' );
				break;
		}
		
		//冻结标志设定
		if ($rec [DJBZH] == 1)
		{
			$this->_view->assign ( 'check4', 'checked' );
		}
		
		//画面项目赋值
		$this->_view->assign ( 'title', '仓储管理-盘点详细' );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'spsjlr_03.php' );
	}

	public function getdjxxAction()
	{
		//取得检索条件
		$djbh = $this->_getParam ( "djbh", '' ); //当前员工编号
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter ['filterParams'] = $_SESSION ['pdwh_filterParams']; //精确查询条件
		$filter ['searchParams'] = $_SESSION ['pdwh_searchParams']; //固定查询条件
		$filter ['orderby'] = $_SESSION ["pdwh_sortParams"] ["orderby"]; //排序
		$filter ['direction'] = $_SESSION ["pdwh_sortParams"] ["direction"]; //排序
		$model = new cc_models_pdwh ( );
		$rec = $model->getPdwhOne ( $djbh, $filter, $flg );
		
		$this->_view->assign ( 'disabled', 'disabled' );
		$this->_view->assign ( 'disabledbm', 'disabled' );
		$this->_view->assign ( 'disableduser', 'disabled' );
		$this->_view->assign ( 'action', 'detail' );
		switch ($rec [ZHMSHLTJ])
		{
			case 1 :
				$this->_view->assign ( 'check1', 'checked' );
				break;
			case 2 :
				$this->_view->assign ( 'check2', 'checked' );
				break;
			case 3 :
				$this->_view->assign ( 'check3', 'checked' );
				break;
			default :
				$this->_view->assign ( 'check1', 'checked' );
				break;
		}
		
		//冻结标志设定
		if ($rec [DJBZH] == 1)
		{
			$this->_view->assign ( 'check4', 'checked' );
		}
		
		//没有找到记录
		if ($rec == FALSE)
		{
			echo 'false';
		}
		else
		{
			$this->_view->assign ( "rec", $rec );
			echo $this->_view->fetchPage ( "pdwh_02.php" );
		}
	}
}
<?php
/*********************************
 * 模块：    门店模块(md)
 * 机能：    分店经营商品维护(fdjyspwh)
 * 作成者：魏峰
 * 作成日：2011/02/09
 * 更新履历：

 *********************************/
class md_fdjyspwhController extends md_controllers_baseController {
	
	/*
	 *  分店经营商品维护页面
	 */
	public function indexAction() {
		$this->_view->assign ( "riqi", date("Y-m-d"));  			//日期
		$this->_view->assign ( "title", "门店管理-分店经营商品维护" ); 	//标题
		$this->_view->display ( "fdjyspwh_01.php" );
	}	
	
	/*
	 * 得到门店商品信息列表数据
	 */
	public function getlistdataAction() {
		
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart",0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",10 ); //默认显示数量
		//$filter ['shpbh'] = $this->_getParam ( "shpbh", '' ); //商品编号
		$filter ['flbm'] = $this->_getParam ( "flbm", '' ); //分類
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){				
			//取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['searchParams'] = $_POST;
				unset($_SESSION['fdjyspwh_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['fdjyspwh_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
				unset($filter['flbm']);  //清空分類条件
			}				
		}
				
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件		
		$filter['filterParams'] = $_SESSION['fdjyspwh_filterParams'];  //精确查询条件
			
		$model = new md_models_fdjyspwh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}	
	
	/*
	 * 更改商品状态
	 */
	public function changestatusAction() {
		
		$model = new md_models_fdjyspwh ( );
		$model->updateStatus ( $_POST ['shpbh'], $_POST ['shpzht'] );
		//写入日志
		Common_Logger::logToDb ( ($_POST ['shpzht'] == '0' ? "商品锁定" : "商品解锁") . " 商品编号：" . $_POST ['shpbh'] );
	
	}	
	
    /*
     * 商品基础资料登录
     */
	public function newAction() {
		
		$model = new md_models_fdjyspwh ( );
		$zhdkqlx = $model->getZhdkqlxList (); //指定库区类型
		
		//给画面变量赋值

		$this->_view->assign ( 'action', 'new' ); //登录
		$this->_view->assign ( 'zhdkqlx', $zhdkqlx ); //指定库区类型	
		$this->_view->assign ( "riqi", date("Y-m-d"));  			//日期
		$this->_view->assign ( "title", "门店管理-门店商品基础资料登录" ); 	//标题
		$this->_view->display ( 'fdjyspwh_02.php' );
	}

    /*
     * 商品基础资料变更
     */
	public function updateAction() {
		
		$model = new md_models_fdjyspwh ( );
		$zhdkqlx = $model->getZhdkqlxList (); //指定库区类型
		$rec = $model->getshpData ( $this->_getParam ( "shpbh", '00000000' ));
		$recmd = $model->getmdshpData ( $this->_getParam ( "shpbh", '00000000'));		
		//给画面变量赋值
		$this->_view->assign ( 'action', 'update' ); //登录
		$this->_view->assign ( 'zhdkqlx', $zhdkqlx ); //指定库区类型	
		$this->_view->assign ( 'rec', $rec );
		$this->_view->assign ( 'recmd', $recmd );
		$this->_view->assign ( "riqi", date("Y-m-d"));  			//日期
		$this->_view->assign ( "title", "门店管理-门店商品基础资料变更" ); 	//标题
		$this->_view->display ( 'fdjyspwh_02.php' );
	}
	
	/*
     * 商品选择（门店）
     */
	public function mdshplistAction() {
		$this->_view->assign ( "title", "门店管理-门店商品选择" ); 	//标题		
		$this->_view->display ( "fdjyspwh_03.php" );
	}
	
	/*
	 * 商品选择弹出画面（门店）数据取得
	 */
	public function getmdshplistdataAction(){
    	$filter ['posStart'] = $this->_getParam("posStart",0);  //起始位置
        $filter ['count'] = $this->_getParam("count",10);          //每页行数
		$filter ['orderby'] = $this->_getParam("orderby",1);    //排序列
		$filter ['direction'] = $this->_getParam("direction",'ASC'); //排序方向
		
		//$filter ['searchkey'] = $this->_getParam("searchkey",'');      //检索条件
		$filter ['flbm'] = $this->_getParam("flbm",'');               //分类编码
		
				//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
			//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){				
			//取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['searchParams'] = $_POST;
				unset($_SESSION['fdjyspwh_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['fdjyspwh_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
				unset($filter['flbm']);  //清空分類条件
			}				
		}
		
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件		
		$filter['filterParams'] = $_SESSION['fdjyspwh_filterParams'];  //精确查询条件
			
		$mdshangpin_model = new md_models_fdjyspwh();
		header("Content-type:text/xml");
		echo $mdshangpin_model->getmdshpListData($filter);	
   }
   
	/*
     * 商品分类选择（门店）
     */
	public function showmdshpflAction() {
				
		$this->_view->assign ( "mdbh", '000001');  			//日期
		$this->_view->assign ( "riqi", date("Y-m-d"));  			//日期
		$this->_view->assign ( "title", "门店管理-门店商品分类选择" ); 	//标题			
		$this->_view->display ( "fdjyspwh_04.php" );
	}
	
	/*
	 * 判断商品编号是否存在
	 */
	public function checkmdshpbhAction() {
		$model = new md_models_fdjyspwh ( );
		if ($model->getMdshpbh ( $this->_getParam ( 'shpbh' )) == FALSE) {
			echo 0; //不存在
		} else {
			echo 1; //存在
		}
	}
	
	/*
	 * 保存
	 */
	public function saveAction() {
		
		$result['status'] = '0';
		$result ['SHPBH'] = $_POST ['SHPBH']; 
		//商品编号取得
		$shpbh = $_POST['SHPBH'];
		//商品分类编码取得		
		$mdshpfl = $_POST['MDSHPFL'];
		
		try {
		$model = new md_models_fdjyspwh();

		//必须输入项验证
		if(!$model->inputCheck($shpbh,$mdshpfl))
		{
			$result['status'] = '4';  //必须输入项验证错误
		}
		else
		{
			//开始一个事务
		    $model->beginTransaction ();
		    if ($_POST ['action'] == 'new') {
				//门店商品信息保存
				if ($model->insertMdshp () == false) {
					$result ['status'] = 2; //商品编号已存在
	
				} else {
					$result ['status'] = 0; //登录成功
					Common_Logger::logToDb ( "门店商品信息登录  商品编号：" . $_POST ['SHPBH'] );
				}
		    }else{
			    //更新数据
				if ($model->updateMdshp () == false) {
					$result ['status'] = 3; //时间戳已变化
				} else {
					$result ['status'] = 1; //修改成功
					Common_Logger::logToDb ( "门店商品信息修改  单位编号：" . $_POST ['SHPBH'] );
				}				
			}	
		}
		$model->commit();
		echo json_encode($result);
	}
	catch ( Exception $e )
   {
		//回滚
		$model->rollBack ();
    		throw $e;
	}
  }
  
    /*
     * 商品基础资料详情
     */
	public function detailAction() {
		
		$model = new md_models_fdjyspwh ( );
		$rec = $model->getshpData ( $this->_getParam ( "shpbh", '00000000' ));
		$recmd = $model->getmdshpData ( $this->_getParam ( "shpbh", '00000000' ));		
		//给画面变量赋值
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "flbm", $this->_getParam ( "flbm", '' ) ); //列表画面条件-分類
		$this->_view->assign ( 'rec', $rec );
		$this->_view->assign ( 'recmd', $recmd );
		$this->_view->assign ( "riqi", date("Y-m-d"));  			//日期
		$this->_view->assign ( "title", "门店管理-门店商品基础资料详情" ); 	//标题
		$this->_view->display ( 'fdjyspwh_05.php' );
	}
  
	/*
	 * 取得门店商品资料信息(上下条)
	 */
	public function getmdshpAction() {
		$shpbh = $this->_getParam ( 'shpbh', '00000000' );
	    //$filter ['shpbhkey'] = $this->_getParam ( "shpbhkey", '' ); //检索条件
	    
		$filter ['flbm'] = $this->_getParam ( "flbm", '' ); //检索条件
		$filter['filterParams'] = $_SESSION['fdjyspwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向

		$model = new md_models_fdjyspwh();
		$recmd = $model->getmdshpData ( $shpbh, $filter, $flg );
		$rec = $model->getshpData ($recmd["SHPBH"]);
		
		//没有找到记录
		if ($recmd == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( 'recmd', $recmd );
			$this->_view->assign ( 'rec', $rec );
			echo json_encode ( $this->_view->fetchPage ( "fdjyspwh_05.php" ) );
		}
	}
   
}
  
		

<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库间调拨返库(kjdbfk)
 * 作成者：dltt-姚磊
 * 作成日：2010/11/10
 * 更新履历：
 *********************************/
class cc_kjdbfkController extends cc_controllers_baseController {
	
		private $idxx_ROWNUM=0;// 行号
		private $idxx_SHPBH=1;// 商品编号
		private $idxx_SHPMCH=2;// 商品名称
		private $idxx_GUIGE=3;// 规格
		private $idxx_BZHDWBH=4;// 包装单位
		private $idxx_PIHAO=5;// 批号
		private $idxx_SHCHRQ=6;// 生产日期
		private $idxx_BZHQZH=7;// 保质期至
		private $idxx_JLGG = 8;// 计量规格
		private $idxx_BZHSHL=9;// 包装数量
		private $idxx_LSSHL=10;// 零散数量
		private $idxx_SHULIANG=11;// 数量
		private $idxx_WSHHSHL=12; //未收货数量
		private $idxx_CHANDI=13;// 产地
		private $idxx_BEIZHU=14;// 备注
		private $idxx_TONGYONGMING = 15; // 通用名
		private $idxx_DCHKW=16;// 调出库位编号
		private $idxx_DRKW = 17;//调入库位编号
		private $idxx_DCHKQ = 18;//调出库区编号
		private $idxx_DRKQ = 19;//调入库区编号
		private $idxx_BZHDW=20;// 包装单位编号_
		private $idxx_SHFSHKW=21; //是否为散货库存
	/*
     * 库间调拨返库画面显示
     */
	public function indexAction() {
		$Model = new cc_models_kjdbfk( );
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
        $this->_view->assign('bmbh', $_SESSION['auth']->bmbh);  //部门名称
		$this->_view->assign ( 'action', 'index' ); 
		$bh = $this->_getParam( "djbh" );
		$rec = $Model->getinfoData( $bh );
		$this->_view->assign ( "kprq", date("Y-m-d"));  			//开票日期
		$this->_view->assign ( "rec", $rec ); 						
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门
		$this->_view->assign ( "bmbh", $_SESSION ["auth"]->bmbh ); //部门编号
		$this->_view->assign ( "title", "仓储模块-库间调拨返库" ); //标题
		$this->_view->display ( 'kjdbfk_01.php' );
	}
	
	/*
	 * 库间调拨返库弹出页面初始化
	 */
	public function dydbckdbAction(){
		$this->_view->assign("title","仓储模块-库间调拨出库单选择");
		$this->_view->display ( "kjdbfk_02.php" );
	}
	
	/**
     * 通过商品编号取得商品相关信息
     *
     */
	public function getshangpininfoAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$Model = new cc_models_kjdbfk( );
		
	    echo Common_Tool::json_encode($Model->getShangpinInfo($filter));
	}
	
	/**
	 * 判断对象库位状态
	 *
	 */
	public function chkKwStatsAction(){
		$filter ['ckbh'] = $this->_getParam('ckbh');   //仓库编号
		$filter ['kqbh'] = $this->_getParam('kqbh'); //库区编号
		$filter ['kwbh'] = $this->_getParam('kwbh'); //库位编号
		
		$Model = new cc_models_kjdbfk( );
		
		echo Common_Tool::json_encode($Model->getKwInfo($filter));
		
	}
/**
	 * 检索调拨在库商品的数量
	 *
	 */
	public function getHaveCountAction(){
		$filter ['ckbh'] = $this->_getParam('ckbh');   //仓库编号
		$filter ['kqbh'] = $this->_getParam('kqbh'); //库区编号
		$filter ['kwbh'] = $this->_getParam('kwbh'); //库位编号
		
		$Model = new cc_models_kjdbfk( );
		
		echo Common_Tool::json_encode($Model->getKwInfo($filter));
	}
	
	/*
	 * 保存返库信息
	 */
	public function saveAction(){
		$result['status'] = '0'; 
		
		try{
				$cgkpModel = new cc_models_zjrk();
		    	$cgkpModel->beginTransaction ();  //开启一个事物
		    	$kjdbfkbh = Common_Tool::getDanhao('DBF',$_POST['KPRQ']); //库间调拨返库单编号
				$Model = new cc_models_kjdbfk( );
				$dydbchkd =$_POST['DJBH_TMP'];
				//验证时间内库区是否发生变化
				$file['BGZH'] = $_POST['BGZH'];	//更新人
				$file['BGRQ'] = $_POST['BGRQ'];  //更新日期
				if($Model->checktime($dydbchkd,$file) == false){	
					$result['status'] = '3'; 			//弹出警告信息	
					echo json_encode($result);			//错误信息3		
					return false;
				}	
				//返库验证		
				$Model->savedjxx($kjdbfkbh,$dydbchkd);			//存储库间调拨返库信息
				$Model->savekjdbfkMingxi($kjdbfkbh);	//存储库间调拨返库明细
				foreach ( $_POST ["#grid_mingxi"] as $grid  ) {
				$Model->updatadbckdxx($dydbchkd,$grid [$this->idxx_SHPBH],$grid [$this->idxx_PIHAO],$grid [$this->idxx_SHCHRQ]);	//更新库间调拨出库单明细信息的未收货数量及退货中数量		
				}	
				$Model->updateckzhtai($dydbchkd);				//出库单状态更新	
		 		$cgkpModel->commit ();
			    
				Common_Logger::logToDb ("库间调拨返库  单据编号：".$dydbchkd);
				
				$result['data'] = $kjdbfkbh; //采购单编号
				echo json_encode($result);
		}catch( Exception $e){
		//回滚
			$cgkpModel->rollBack ();
     		throw $e;
		}
	}
	
	/*
	 *  对应调拨出库单双击生成页面
	 */
	public  function getlistdataAction(){
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
                $_SESSION['searchParams'] = $_POST;
                unset($_SESSION['kjdbfkxx_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['kjdbfkxx_filterParams'] = $_POST;
                unset($_SESSION['searchParams']); //清空一般查询条件
            }
        }

        //取得检索条件
        $filter['filterParams'] = $_SESSION['kjdbfkxx_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
        
            $Model = new cc_models_kjdbfk( );
            header ( "Content-type:text/xml" ); //返回数据格式xml
            echo $Model->dydbckdb($filter);
	}
	
	/*
	 * 弹出库间调拨出库明细
	 */
	public function dbckkwxxAction(){					
		
		$djbh= $this->_getParam ( "flg" );
		$Model = new cc_models_kjdbfk( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $Model->dbckkwxx($djbh);
	}
	/*
	 * 初始化库间调拨出库明细
	 */
	public function loadmingAction(){					
		
		$djbh= $this->_getParam ( "flg" );
		$Model = new cc_models_kjdbfk( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $Model->loadming($djbh);
	}
	/*
	 * 对应调拨出库单双击明细
	 * 
	 */
	public function  getmingxilistdataAction(){
		
		$djbh= $this->_getParam ( "djbh" );
		$Model = new cc_models_kjdbfk( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $Model->getmingxilistdata($djbh);
	}
	
	/*
	 * 获取配送，备注，电话信息
	 */
	
	public function getkjdjxxAction(){
		
		$djbh= $this->_getParam ( "flg" );
		$Model = new cc_models_kjdbfk( );
		$result= $Model->getkjdjxx($djbh);
		echo json_encode($result);
		
	}
	
	/*
	 * 获取库间调拨返库明细
	 */
	public function getkjdbfkmxAction(){
		$result['status'] = '0'; 
		try{
		$djbh= $this->_getParam ( "flg" );
		$Model = new cc_models_kjdbfk( );
		$result= $Model->getkjdbfkMx($djbh);
		echo json_encode($result);						
		}catch( Exception $e){
		//回滚
			$Model->rollBack ();
     		throw $e;
		}		
	}
	
	/*
	 * 弹出商品单据信息
	 */
	public function shxxAction (){
		$this->_view->assign ( "djbh", $this->_getParam ( "flg" ) ); //单据编号	
		$this->_view->assign("title","仓储模块- 库间调拨返库明细表");
		$this->_view->display ( "kjdbfk_03.php" );
		
	}
	
	
	
	
}
<?php
/*********************************
 * 模块：    采购模块(cg)
 * 机能：    采购确认(CGQR)
 * 作成者：姚磊
 * 作成日：2011/6/9
 * 更新履历：
 *********************************/
class cg_cgqrController extends cg_controllers_baseController {
	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1; // 商品编号
	private $idx_SHPMCH = 2; // 商品名称
	private $idx_GUIGE = 3; // 商品规格
	private $idx_BZHDWM = 4; // 包装单位
	private $idx_JLGG = 5; // 计量规格
	private $idx_BZHSHL = 6; // 包装数量
	private $idx_LSSHL = 7; // 零散数量
	private $idx_SHULIANG = 8; // 数量
	private $idx_DANJIA = 9; // 单价
	private $idx_HSHJ = 10; // 含税售价
	private $idx_KOULV = 11; // 扣率
	private $idx_SHUILV = 12; // 税率
	private $idx_HSHJE = 13; // 含税金额
	private $idx_JINE = 14; //金额
	private $idx_SHUIE = 15; // 税额
	private $idx_LSHJ = 16; // 零售价
	private $idx_CHANDI = 17; // 产地
	private $idx_BEIZHU = 18; // 备注
	private $idx_TONGYONGMING = 19; // 通用名	
	private $idx_ZDSHULIANG = 20; // 最大入库数量
	private $idx_SHFSHKW = 21; // 是否散货区
	private $idx_BZHDWBH = 22; // 包装单位编号
	private $idx_XUHAO = 23; // 序号
	
	
	/*
	 * 采购开票初始页面
	 */
	public function indexAction() { 
		$cgkpModel = new cg_models_cgkp();	
		$this->_view->assign ( 'action', 'index' ); 
		$this->_view->assign ( "kprq", date("Y-m-d"));  //开票日期
		$this->_view->assign ( "kpybh", $_SESSION ["auth"]->userName );  //开票员编号，待换成名称
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门
		$this->_view->assign ( "bmbh", $_SESSION ["auth"]->bmbh ); //部门编号
		$cgkpdbh = $this->_getParam( "flg" );   //获取生成采购订单编号
		$rec = $cgkpModel->fhdata( $cgkpdbh );
		$this->_view->assign ( "title", "采购管理-采购确认" ); //标题
		$this->_view->assign ( "rec", $rec ); 
		$this->_view->display ( "cgqr_01.php" );
	}

	

	/*
	 * 采购确认保存
	 */

	public function saveAction() {
		$result['status'] = '1'; 
		$cgqrModel = new cg_models_cgqr();
		$rec = $cgqrModel->fkfs($_POST['CGDBH']);
		$cgqrModel->updatezt($_POST['CGDBH']);
		if($rec['FKFSH'] == '4'){
			
			if($cgqrModel->instercgqr($rec)){
				$fkbh = Common_Tool::getDanhao('KFK',$_POST['KPRQ']);
				$cgqrModel->instercgfkfs($fkbh); //采购付款方式
				$cgqrModel->instercgjsmx($fkbh);
				$result['status'] = '1'; 
			}else{
				$result['status'] = '2';  //保存出错
			}
			
		}else{
			$result['status'] = '1'; //无保存信息
		}
		echo  json_encode($result);
	}
 	/*
 	 * 获取单据编号
 	 */
 	public function getdjbhAction(){

 		$this->_view->display ( "cgqr_02.php" );
 	}
 	
 	/*
 	 *获取采购确认单数据 
 	 */
 	
 	public function getcgbhdataAction(){
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式	
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
 		$model = new cg_models_cgqr();
 			if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgqrlz_searchParams'] = $_POST;
				unset($_SESSION['cgqrlz_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgqrlz_filterParams'] = $_POST;
				unset($_SESSION['cgqrlz_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['cgqrlz_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cgqrlz_searchParams'];  //固定查询条件	
 		
 		
 		
 		echo $model->getdjbh($filter);
 	}
 	
 	/*
 	 * 获取采购确认明细
 	 */
 	public  function getcgqrmxAction(){
 		
 		$flg = $this->_getParam("flg");
 		$model = new cg_models_cgqr();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
 		$rec = $model->getcgGridData($flg);	
 		echo Common_Tool::createXml($rec); 		 		
 	}
 	
 	/*
 	 * 付款方式及金额
 	 */
 	public function fkfsAction(){
 		
 		$CGDBH = $this->_getParam("CGDBH"); //获取采购确认单号
 		$model = new cg_models_cgqr();
 		$result=$model->fkfs($CGDBH); //获取付款方式
// 		//if($result['fkfs'] == 4){	}				//如果付款方式是预付款 则获取预付款金额
// 		$result['fkje']=$model->fkje($CGDBH); //获取预付款金额
 		
 		
 		echo Common_Tool::json_encode($result);
 		
 	}
}
<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   盘点开始及盘点表生成(pdksjpdbsc)
 * 作成者：dltt
 * 作成日：2010/11/10
 * 更新履历：

 *********************************/
class cc_pdksjpdbscController extends cc_controllers_baseController {

	/*
     * 盘点开始及盘点表生成画面显示
     */
	public function indexAction() {
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
        $this->_view->assign('bmbh', $_SESSION['auth']->bmbh);  //部门名称
		$this->_view->assign ( 'action', 'new' );  								//登录
		$this->_view->assign ( 'title', ' 基础管理-盘点开始及盘点表生成' );
		$this->_view->display ( 'pdksjpdbsc_01.php' );
				
	}

/*
	 * 盘点单号选择弹出画面
	 * flg:0 销售员 1：采购员 2：仓库管理员
	 */
	public function listAction()
	{
		
		$this->_view->assign("title","盘点计划单号选择");
		$this->_view->assign("flg","0");
		$this->_view->display ( "pdksjpdbsc_02.php" );
	}
	
	/*
	 * 盘点单号画面数据取得
	 */
	public function getlistdataAction()
	{
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count");
		$filter ['flg'] = $this->_getParam("flg");
		$filter ['searchkey'] = $this->_getParam("searchkey",'');
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
			
		$yuangong_model = new cc_models_pdksjpdbsc();
		
		header("Content-type:text/xml");
		echo $yuangong_model->getListData($filter);
		

	}
	/*
	 * 盘点开始及盘点表生成保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		//仓库编号
		$chkckbh = $_POST['CKBH_H'];
		//库区编号
		$chkkqbh = $_POST['KQBH_H'];
		//库位编号
		$chkkwbh = $_POST['KWBH_H'];
		
		$model = new cc_models_pdksjpdbsc();

		//必须输入项
		if($chkckbh=="" ||$chkkqbh=="" || $_POST['ZHMSHLTJ']=="" ||
			$_POST['YEWUYUAN_H']== "" || $_POST['BUMEN_H']== ""){
			
			$result ['status'] = 7; //
			echo Common_Tool::json_encode ( $result );
			return false;
		}
		//2以画面项目仓库，库区，库位为条件，抽取仓库，库区，库位表。判断指定条件的仓库，库区，库位是否存在。
		
		if($model->getCkstatus( $chkckbh )==false){
			
			$result ['status'] = 2; //仓库编号已不存在
			echo Common_Tool::json_encode ( $result );
			return false;
		}

		
		if($model->getKcstatus($chkckbh,$chkkqbh) ==false){
		   
			$result ['status'] = 3; //库区编号已不存在
			echo Common_Tool::json_encode ( $result );
			return false;
		}
		
		
		if ($chkkwbh !=''){
			if($model->getKwstatus($chkckbh,$chkkqbh,$chkkwbh)==false){
				$result ['status'] = 4; //库位编号已不存在
				echo json_encode ( $result );
				return false;
			}
		}
		
		if($model->getPdstatus($chkckbh,$chkkqbh,$chkkwbh)==false){
			$result ['status'] = 5; //对象库位处于盘点状态
			echo Common_Tool::json_encode ( $result );
			return false;
		}
		
		//盘点开始 盘点单据号
		try{
			
			//开始一个事务
			$model->beginTransaction ();
		
			$pdjhsc = Common_Tool::getDanhao('PDD',date("Y-m-d"));
			//插入新数据
			$retStat = $model->insertpdjhsc($chkckbh,$chkkqbh,$chkkwbh,$pdjhsc);
			if (!$retStat) {
					$result ['status'] = 6; //在库商品为空
					echo json_encode ( $result );
					return false;
			}
			
			$result ['status'] = 0; //登录成功
			$result ['pdjhsc'] = $pdjhsc;
			Common_Logger::logToDb( "盘点开始 盘点单据号：".$pdjhsc);
			
			$model->commit();
		} catch ( Exception $e ) {
			//回滚
				$model->rollBack ();
     			throw $e;
			}
			
		
		//返回处理结果
		echo Common_Tool::json_encode ( $result );
		return true;
	}
	
  
	
}
<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库间调拨出库单查询(kjdbckdcx)
 * 作成者：dltt
 * 作成日：2010-01-26 10:23:51
 * 更新履历：

 *********************************/
class cc_kjdbckdcxController extends cc_controllers_baseController {

	/*
     * 库间调拨出库单
     */
	public function indexAction() {
		
		$this->_view->assign ( 'action', 'new' );  								//登录
		$this->_view->assign ( 'title', '仓储管理-库间调拨出库单' );
		$this->_view->display ( 'kjdbckdcx_01.php' );
				
	}

	/*
	 * 库间调拨出库单入库、返库弹出画面前，判断处理
	 * 
	 */
	public function checkAction()
	{
		$result = array (); //定义返回值
		$model = new cc_models_kjdbckdcx();
		$djbh = $this->_getParam("djbh","");
		$rec = $model->getkjdbckdcxOne($djbh);
		
		//库间调拨出库单出库状态，请确认
		if ($rec!=false) {
			$result ['endstatus'] = 9;
		}else{
			$result ['endstatus'] = 0;
		}
		echo Common_Tool::json_encode ( $result );
	}
	
	/*
	 * 库间调拨出库单画面数据取得
	 */
	public function getlistdataAction()
	{
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
        $filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
        $filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
        $filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式
        //保持排序条件
        $_SESSION["kjdbckdcx_sortParams"]["orderby"] = $filter ['orderby'];
        $_SESSION["kjdbckdcx_sortParams"]["direction"] = $filter ['direction'];
        
        
        //一般查询
        if($this->_getParam ( "isfilter", '0' )=='0'){
           //取得一般查询条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['kjdbckdcx_searchParams'] = $_POST;
                unset($_SESSION['kjdbckdcx_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['kjdbckdcx_filterParams'] = $_POST;
                unset($_SESSION['kjdbckdcx_searchParams']); //清空一般查询条件
            }
        }

        //取得检索条件
        $filter['filterParams'] = $_SESSION['kjdbckdcx_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['kjdbckdcx_searchParams'];  //固定查询条件
		
		$model = new cc_models_kjdbckdcx();
		header("Content-type:text/xml");
		echo $model->getListData($filter);

	}
	/*
     * 调拨出库详细生成画面显示
     */
	public function detailAction() {
		$model = new cc_models_kjdbckdcx();
		$this->_view->assign('title', '仓储管理-库间调拨出库明细');
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $model->getkjdbckOne ( $this->_getParam ( "djbh", '' )));
		$this->_view->display ( 'kjdbckxq_01.php' );
	
	}
	/**
	 * 调拨出库单明细列表
	 *
	 */
	public function detaillistAction() {
		$djbh = $this->_getParam("DJBH"); //调拨出库单据号
		$model = new cc_models_kjdbckdcx();
		$rec = $model->getkjdbckOne($djbh);
		$this->_view->assign ( "rec", $rec);
		header("Content-type:text/xml");
		$filter ['posStart'] = 0;
        $filter ['count'] = 10;
		$filter ['orderby'] = 1;
		$filter ['direction'] = 'ASC';
		$filter ['DCHCK'] = $rec['DCHCKBH'];
		echo $model->getDetailData($djbh,$filter);
	}
}
<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   首营商品资料(shyshp)
 * 作成者：苏迅
 * 作成日：2010/11/30
 * 更新履历： *********************************/
class jc_shyshpController extends jc_controllers_baseController {
	
	/*
     * 商品首营资料维护画面显示
     */
	public function indexAction() {
		$this->_view->assign('title', ' 基础管理-首营商品资料报表维护');
		$this->_view->display ( 'shyshp_01.php' );
	}
	
	/*
	 * 得到商品首营资料列表数据
	 */
	public function getlistdataAction() {
		
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart" ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		//保持排序条件
        $_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
        $_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
        //一般查询
        if($this->_getParam ( "isfilter", '0' )=='0'){
           //取得一般查询条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['searchParams'] = $_POST;
                unset($_SESSION['shyshp_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['shyshp_filterParams'] = $_POST;
                unset($_SESSION['searchParams']); //清空一般查询条件
            }
        }

        //取得检索条件
        $filter['filterParams'] = $_SESSION['shyshp_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
        
		$model = new jc_models_shyshp ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
     * 商品首营修改画面显示
     */
	public function updateAction() {
		
		$model = new jc_models_shyshp ( );
//		$rec = $model->getShyshp ( $this->_getParam ( "shpbh", '00000000' ), '', 'current' );
		$this->_view->assign ( 'title', '首营商品资料修改' );
//		$this->_view->assign ( 'rec', $rec );
        $this->_view->assign ( "rec", $model->getShyshp ($this->_getParam ( "shpbh", '' )));
		$this->_view->display ( 'shyshp_02.php' );
	}
	
	/*
     * 首营商品资料详情画面
     */
	public function detailAction() {
		
		$model = new jc_models_shyshp ();
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( 'title', '首营商品资料详情' );
		$this->_view->assign ( "rec", $model->getShyshp ( $this->_getParam ( "shpbh")));
		$this->_view->display ( 'shyshp_03.php' );
	}
	
	/*
	 * 取得首营商品资料信息(上下条)
	 */
	public function getshyshpAction() {
		$shpbh = $this->_getParam ( 'shpbh', '' );
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
//		$filter ['shpbh'] = $this->_getParam ( "shpbh", '' ); //检索条件
		$filter['filterParams'] = $_SESSION['shyshp_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
        $filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
        $filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		$model = new jc_models_shyshp ( );
		$rec = $model->getShyshp ( $shpbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( 'rec', $rec );
			echo json_encode ( $this->_view->fetchPage ( "shyshp_03.php" ));
		}
	}
	
	/*
	 * 保存
	 */
	public function saveAction() {
		
		$result = array (); //定义返回值
		$result ['SHPBH'] = $_POST ['SHPBH'];
		$model = new jc_models_shyshp ( );
		
		//更新商品资料(商品首营信息)
		if ($model->updateShyshp () == false) {
			$result ['status'] = 0; //时间戳已变化
		} else {
			$result ['status'] = 1; //修改成功
			Common_Logger::logToDb ( "商品首营资料信息修改  单位编号：" . $_POST ['SHPBH'] );
		}
		
		//返回处理结果
		echo json_encode ( $result );
	
	}
}

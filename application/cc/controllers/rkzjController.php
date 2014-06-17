<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购入库质检(RKZJ)
 * 作成者：ZhangZeliang
 * 作成日：2011/03/25
 * 更新履历：
 *********************************/

class cc_rkzjController extends cc_controllers_baseController {
	/*
	 * 采购入库质检页面
	 */
	public function indexAction() {
		$this->_view->assign ( "title", "仓储管理-入库质检" );
		$this->_view->assign ( "kprq", date ( "Y-m-d" ) );
		$this->_view->display ( "rkzj_01.php" );
	}
	
	/*
	 * 入库质检单选择
	 */
	public function yrkdlistAction() {
		$this->_view->assign ( "title", "仓储管理-预入库单选择" );
		$this->_view->display ( "rkzj_02.php" );
	}
	
	/*
	 * 获取预入库单据信息(采购单选择页面)
	 */
	public function getycgdlistdataAction() {
		//获取参数
		$filter ["posStart"] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); //终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' ); //单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwmchkey", '' ); //单位名称
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_rkzj ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getYcgGridData ( $filter );
	}
	
	/*
	 * 获取预入库单据信息的详细信息(采购单选择页面)
	 */
	public function getycgdmxlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['yrkdbh'] = $this->_getParam ( "yrkdbh", '' ); //采购单编号
		$model = new cc_models_rkzj ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getYcgMingxiData ( $filter );
	}
	
	/*
	 * 获取预入库单据信息的详细信息
	 */
	public function getdjinfoAction() {
		//取得列表参数
		$filter ['yrkdbh'] = $this->_getParam ( "yrkdbh", '' ); //采购单编号
		$model = new cc_models_rkzj ( );
		//header ( "Content-type:text/xml" ); //返回数据格式xml
		echo Common_Tool::json_encode ( $model->getdjinfo ( $filter ) );
	}
	
	/*
	 * 获取预入库单商品详细信息
	 */
	public function yrkdspmxinfoAction() {
		$filter ["yrkdbh"] = $this->_getParam ( "yrkdbh" ); //获取预入库单编号
		$model = new cc_models_rkzj ( );
		echo Common_Tool::json_encode ( $model->yrkdspmxinfo ( $filter ) );
	}
	
	/*
	 * 预采购入库信息保存
	 */
	public function saveAction() {
		$result ['status'] = '0';
		try {
			$model = new cc_models_rkzj ( );
			//必填项验证
			if (! $model->inputCheck ()) {
				$result ['status'] = '1'; //必须输入项验证错误
			} else if (! $model->logicCheck ()) {
				$result ['status'] = '2'; //项目合法性验证错误
			} else {
				//开始一个事务
				$model->beginTransaction ();
				//获取预入库单编号
				$yrkdbh = $_POST ['YRKDH'];
				//入库质检单保存
				$model->saveRkzj ();
				//循环读取保存明细信息
				$model->executeMingxi ();
				//更新预采购入库单的状态
				$model->updateRkzjZt ();
				//保存成功
				if ($result ['status'] == '0') {
					$model->commit ();
					Common_Logger::logToDb ( "入库质检 预入库单编号：" . $yrkdbh );
				} else {
					$model->rollBack ();
				}
			}
			echo json_encode ( $result );
		} catch ( Exception $e ) {
			//回滚
			$model->rollBack ();
			throw $e;
		}
	}
}

?>

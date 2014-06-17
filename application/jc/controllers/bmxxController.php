<?php
/******************************************************************
 ***** 模         块：       基础模块(JC)
 ***** 机         能：       商品分类(bmxx)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/11/24
 ***** 更新履历：
 ******************************************************************/

class jc_bmxxController extends jc_controllers_baseController {
	
	/*
     * 部门信息维护画面显示
     */
	public function indexAction() {
		$this->_view->display ( 'bmxx_01.php' );
	}
	
	
	/*
     * 部门信息增加根节点画面显示
     */
	public function newfnodeAction() {
		$this->_view->display ( 'bmxx_02.php' );
	}
	
	
	/*
     * 部门信息增加子节点画面显示
     */
	public function newcnodeAction() {
		$bmbh = $this->_getParam("bmbh");
		$this->_view->assign ( "bmbh", $bmbh );
		$this->_view->display ( 'bmxx_03.php' );
	}
	
	
	/*
     * 部门信息修改画面显示
     */
	public function updateAction() {	
		$model = new jc_models_bmxx();
		$bmbh = $this->_getParam("bmbh");
		$rec = $model->getBmxx($bmbh);
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'bmxx_04.php' );
	}
	
	
	/*
	 * 部门树形列表xml
	 */
	public function gettreedataAction()
	{
		$flg = $this->_getParam('flg','0');    //部门选择flg   0:仅可用   1:全部
		$model = new jc_models_bmxx();
		header("Content-type:text/xml");
		echo $model->getTreeData($flg);	
	}
	
	/*
	 * 更改部门状态
	 */
	public function changestatusAction() {
		$model = new jc_models_bmxx();
		if($model->updateStatus ( $_POST['bmbh'], $_POST['bmzht'] ) == TRUE){
			if($_POST ['bmzht'] == '0'){
				Common_Logger::logToDb( "部门信息禁用"." 部门编号：".$_POST['bmbh']);		//写入日志
			} else{
				Common_Logger::logToDb( "部门信息启用"." 部门编号：".$_POST['bmbh']);		//写入日志
			}
		}
	}
	
	/*
	 * 判断编号是否存在
	 */
	public function checkAction() {
		$model = new jc_models_bmxx();	
		if ($model->getBmxx( $this->_getParam('bmbh') ) == FALSE) {
			echo 0; //不存在
		} else {
			echo 1; //已存在
		}
	}
	
	/*
	 * 判断状态是否禁用
	 */
	public function checkstatusAction() {
		$model = new jc_models_bmxx();	
		if ($model->checkstatus( $this->_getParam('bmbh') ) == FALSE) {
			echo 0; //不存在冻结
		} else {
			echo 1; //存在冻结
		}
	}
	
	
	/*
	 * 判断是否有未禁用的下级部门
	 */
	public function lowerstatusAction() {
		$model = new jc_models_bmxx();	
		if ($model->lowerstatus( $this->_getParam('bmbh') ) == FALSE) {
			echo 0; //不存在
		} else {
			echo 1; //存在
		}
	}
	
	
	/*
	 * 判断是否有被禁用的上级部门
	 */
	public function superiorstatusAction() {
		$model = new jc_models_bmxx();
		if ($model->superiorstatus( $this->_getParam('bmbh') ) == FALSE) {
			echo 0; //不存在
		} else {
			echo 1; //存在
		}
	}
	
	
	/*
	 * 得到助记码
	 */
	public function getzhjmAction() {
		echo Common_Tool::getPy( $this->_getParam ( 'bmmch') );
	}

	
	/*
	 * 信息保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		try {
			$model = new jc_models_bmxx();
			$model -> beginTransaction ();				//开始一个事务
			$result ['bmbh'] = $_POST ['BMBH'];  //商品分类编号
			
			if ($_POST ['action'] == 'new') {    //插入新数据
				if ($model->insert() == false) {
					$result ['status'] = 2; //编号已存在					$model -> rollBack();
				} else {
					$result ['status'] = 0; //登录成功
					Common_Logger::logToDb( "部门信息登录  部门编号：".$_POST ['BMBH']);
					$model -> commit(); 
				}	
			} else {    //更新数据	
				if ($model->update() == false) {
					$result ['status'] = 3;    //修改失败
					$model -> rollBack();
				} else {
					$result ['status'] = 1;    //修改成功
					Common_Logger::logToDb( "部门信息修改  部门编号：".$_POST ['BMBH']);
					$model -> commit(); 
				}
			}	
			echo Common_Tool::json_encode( $result );     //返回处理结果
		} catch ( Exception $e ) {
			//事务回滚
			$model -> rollBack();
     		throw $e;
		}
	}

}
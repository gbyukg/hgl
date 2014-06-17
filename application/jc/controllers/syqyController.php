<?php
/******************************************************************
 ***** 模         块：       基础模块(JC)
 ***** 机         能：       首营企业审核(syqy)
 ***** 作  成  者：        于健
 ***** 作  成  日：        2010/12/07
 ***** 更新履历：

 ******************************************************************/
class jc_syqyController extends jc_controllers_baseController {
	
	/*
     * 首营企业审核维护画面显示
     */
	public function indexAction() {
		
		$this->_view->display ( 'syqy_01.php' );
	}
	
	/*
     * 首营企业信息画面显示

     */
	public function newAction() {
		$this->_view->display ( 'syqy_02.php' );
	}
	

	/*
	 * 首营企业信息保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值

		$result ['qybm'] = $_POST ['QYBM']; //企业编码	
		$model = new jc_models_syqy();

		if ($_POST ['action'] == 'new') {    //插入新数据

			if ($model->insertSyqy()== false) {
				$result ['status'] = 2; //仓库编号已存在

			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb( "首营企业信息登录  企业编码：".$_POST ['QYBM']);
			}	
		} else {    //更新数据	
			if ($model->updateSyqy()== false) {
				$result ['status'] = 3;    //时间戳已变化
			} else {
				$result ['status'] = 1;    //修改成功
				Common_Logger::logToDb( "首营企业信息修改 企业编码：".$_POST ['QYBM']);
			}
		}	
		echo json_encode ( $result );     //返回处理结果
	}
	
/*
     * 首营企业信息修改画面显示
     */
	public function updateAction() {	
		$model = new jc_models_syqy();
		//画面项目赋值

		$this->_view->assign ( 'action', 'update' );    //修改
		$this->_view->assign ( 'title', '首营企业信息修改' );
		//$this->_view->assign ( "zhuangtai_opts", array ('0' => '冻结', '1' => '可用', 'X'=>'删除') );
		$this->_view->assign ( "rec", $model->getSyqy ($this->_getParam ( "qybm", '000000' )));
		$this->_view->display ( 'syqy_03.php' );
	}
	
	/*
     * 部门信息增加子节点画面显示

     */
	public function newcnodeAction() {
		$qybm = $this->_getParam ( "qybm" );
		$this->_view->assign ( "qybm", $qybm );
		$this->_view->display ( 'syqy_03.php' );
	}
	
	/*
     * 首营企业信息详情画面
     */
	public function detailAction() {
		//首营企业信息取得
		$model = new jc_models_syqy();
		//画面项目赋值

		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) );    //列表画面排序
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) );//列表画面排序
		$this->_view->assign ( "searchkey", $this->_getParam ( "searchkey", '' ) );//列表画面条件
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $model->getSyqy($this->_getParam ( "QYMCH", '' ) ));
		$this->_view->display ( 'syqy_04.php' );
	}
	
	/*
	 * 得到首营企业列表数据
	 */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",5); //默认显示数量
		$filter ['qymch'] = $this->_getParam ( "qymch", '' ); //仓库名称
		$filter ['orderby'] = $this->_getParam ( "orderby",2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		$model = new jc_models_syqy();
		header ( "Content-type:text/xml" );       //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
}


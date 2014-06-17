<?php
/*********************************
 * 模块：   门店模块(MD)
 * 机能：   会员信息(HYXX)
 * 作成者：苏迅
 * 作成日：2011/2/11
 * 更新履历：
 *********************************/
class md_hyxxController extends md_controllers_baseController {
	
	/*
     * 会员资料画面显示
     */
	public function indexAction() {
		$this->_view->assign('title','门店管理-会员信息维护');
		$this->_view->display ( 'hyxx_01.php' );
	}
	
	/*
     * 会员资料登录
     */
	public function newAction() {
		$rec['DJRQ'] = date("Y-m-d");
		$rec['MDMCH'] = $_SESSION ['auth']->mdmch;
		//$rec['JBMD'] = $_SESSION ['auth']->mdbh;
		$rec['JINGBANRENM'] = $_SESSION ['auth']->userName;
		//$rec['JINGBANREN'] = $_SESSION ['auth']->userId;
		$this->_view->assign ( "rec", $rec);
		$this->_view->assign ( 'action', 'new' ); //登录
		$this->_view->assign ( 'title', '门店管理-会员信息维护' );
		$this->_view->assign ( "kplx_opts", array ('9' => '- - 请 选 择 - -', '0' => '积分卡', '1' => '储值卡', '2' => '打折卡' ) );
		$this->_view->assign ( "dshbb_opts", array ('9' => '- - 请 选 择 - -', '0' => '丢失补办', '1' => '丢失不补' ) );
		$this->_view->display ( 'hyxx_02.php' );
	
	}
	
	/*
     * 会员修改画面显示
     * 登录修改共用一个画面
     */
	public function updateAction() {
		$model = new md_models_hyxx ();
		
		$this->_view->assign ( 'action', 'update' ); //修改
		$this->_view->assign ( 'title', '门店管理-会员信息修改' );
		$this->_view->assign ( "kplx_opts", array ('9' => '- - 请 选 择 - -', '0' => '积分卡', '1' => '储值卡', '2' => '打折卡' ) );
		$this->_view->assign ( "dshbb_opts", array ('9' => '- - 请 选 择 - -', '0' => '丢失补办', '1' => '丢失不补' ) );
		$this->_view->assign ( "rec", $model->getHyxx ($this->_getParam ( "hybh", '' )));
		$this->_view->display ( 'hyxx_02.php' );
	}
	
	/*
     * 会员资料详情画面
     */
	public function detailAction() {
		$model = new md_models_hyxx ( );

		//画面项目赋值
        $this->_view->assign("title",'门店管理-会员信息详情');
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "kplx_opts", array ('9' => '- - 请 选 择 - -', '0' => '积分卡', '1' => '储值卡', '2' => '打折卡' ) );
		$this->_view->assign ( "dshbb_opts", array ('9' => '- - 请 选 择 - -', '0' => '丢失补办', '1' => '丢失不补' ) );
		$this->_view->assign ( "rec", $model->getHyxx ( $this->_getParam ( "hybh", '' )));
		$this->_view->display ( 'hyxx_03.php' );
	}
	
	/*
	 * 得到会员列表数据
	 */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
        $filter ['count'] = $this->_getParam ( "count", 50 );       //默认显示数量
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
                unset($_SESSION['hyxx_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['hyxx_filterParams'] = $_POST;
                unset($_SESSION['searchParams']); //清空一般查询条件
            }
        }

        //取得检索条件
        $filter['filterParams'] = $_SESSION['hyxx_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
        
		$model = new md_models_hyxx ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 取得会员资料信息
	 */
	public function gethyxxAction() {
		$hybh = $this->_getParam ('hybh', '');
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
        
		$filter['filterParams'] = $_SESSION['hyxx_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
        $filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
        $filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		$model = new md_models_hyxx ( );
		$rec = $model->getHyxx ( $hybh,$filter, $flg );
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "kplx_opts", array ('9' => '- - 请 选 择 - -', '0' => '积分卡', '1' => '储值卡', '2' => '打折卡' ) );
			$this->_view->assign ( "dshbb_opts", array ('9' => '- - 请 选 择 - -', '0' => '丢失补办', '1' => '丢失不补' ) );
			$this->_view->assign ( "rec", $rec );
			echo json_encode ( $this->_view->fetchPage ( "hyxx_03.php" ) );
		}
	}
	
	/*
	 * 保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		try {
			$model = new md_models_hyxx ();
			$model->beginTransaction ();
			if ($_POST ['action'] == 'new') {
			    //会员编号取得
			    $hybh = Common_Tool::getDanhao('HYH',$_POST['DJRQ']);
			    //会员信息信息保存
			    $model->insertHyxx ($hybh);
			    $result ['status'] = 0; //登录成功
			    $result ['HYBH'] = $hybh;
				Common_Logger::logToDb("会员信息登录 会员编号：".$hybh);		
			} else {
				//更新数据
				if ($model->updateHyxx () == false) {
					$result ['status'] = 2; //时间戳已变化
				} else {
					$result ['status'] = 1; //修改成功
					$result ['HYBH'] = $_POST ['HYBH'];
					Common_Logger::logToDb ( "会员信息修改   会员编号：" . $_POST ['HYBH'] );
				}
			}
			
			$model->commit();
			
			//返回处理结果
			echo json_encode ( $result );
		
		} catch ( Exception $e ) {
			//回滚
			$model->rollBack ();
		   	throw $e;
		}
	}
	
	/*
	 * 更改客户使用状态

	 */
	public function changestatusAction() {
		
		$model = new md_models_hyxx ( );
		$model->updateStatus ( $_POST ['hybh'], $_POST ['hyzht'] );
		//写入日志
		Common_Logger::logToDb ( ($_POST ['hyzht'] == '1' ? "会员解锁" : "会员锁定") . " 会员编号：" . $_POST ['hybh'] );
	
	}

}

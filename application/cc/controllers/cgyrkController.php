<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购预入库(CGYRK)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/24
 * 更新履历：
 *********************************/

class cc_cgyrkController extends cc_controllers_baseController
{
	/*
	 * 采购预入库画面显示
	 */
	
	private $message = array();
	
	public function indexAction()
	{
		$this->_view->assign('kprq', date('Y-m-d'));
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
		$this->_view->assign('bmbh', $_SESSION['auth']->bmbh);  //部门名称
		$this->_view->assign('title', '仓储管理-采购预入库');
		$this->_view->display('cgyrk_01.php');
	}
	
	/*
     * 采购单选择画面显示
     */
	public function cgdlistAction()
	{
		$this->_view->assign('title', '仓储管理-采购单选择');
		$this->_view->assign ( "KSRQKEY", date ( "Y-m-d",time() - (14 * 24 * 60 * 60) ) );    //开始日期
        $this->_view->assign ( "ZZRQKEY", date ( "Y-m-d" ) );   //终止日期
        $this->_view->display('cgyrk_02.php');
	}
	
	/*
	 * 采购单选择画面采购单查询
	 */
	public function getcgdlistdataAction()
	{
		//取得分页排序参数
        $filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
        $filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
        $filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
        $filter ['direction'] = $this->_getParam ( "direction","DESC"); //排序方式       
        //保持排序条件
        $_SESSION["cgdchx_sortParams"]["orderby"] = $filter ['orderby'];
        $_SESSION["cgdchx_sortParams"]["direction"] = $filter ['direction'];
        
        //一般查询
        if($this->_getParam ( "isfilter", '0' )=='0'){
           //取得一般查询条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['cgdchx_searchParams'] = $_POST;
                unset($_SESSION['cgdchx_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['cgdchx_filterParams'] = $_POST;
                unset($_SESSION['cgdchx_searchParams']); //清空一般查询条件
            }
        }

        //取得检索条件
        @$filter['filterParams'] = $_SESSION['cgdchx_filterParams'];  //精确查询条件
        @$filter['searchParams'] = $_SESSION['cgdchx_searchParams'];  //固定查询条件
    
        $model = new cc_models_cgyrk();
        header ( "Content-type:text/xml" ); //返回数据格式xml     
        echo $model->getCgdList($filter);
	}
	
	/*
	 * 获取采购单选择页面的采购单明细信息
	 */
	public function getcgdmxlistdataAction()
	{
		//取得列表参数
        $filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
        $filter ['count'] = $this->_getParam ( "count", 50 );       //默认显示数量
        $filter ['cgdbh'] = $this->_getParam ( "cgdbh", '' );   //采购单编号
        $model = new cc_models_cgyrk ( );
        header ( "Content-type:text/xml" ); //返回数据格式xml
        echo $model->getCgMingxiData ( $filter );
	}
	
	/*
	 * 根据采购单编号获取指定的采购单信息
	 */
	public function getspecificcgdAction()
	{
		$cgdbh = $this->_getParam('cgdbh', '');
		$model = new cc_models_cgyrk ();
        echo Common_Tool::json_encode($model->getSpecificCgd($cgdbh));
	}
	
	/*
	 * 根据商品编号获取商品信息
	 */
	public function getspecificshpxxAction()
	{
		$shpbh = $this->_getParam('shpbh');
		$model = new cc_models_cgyrk();
		echo Common_Tool::json_encode($model->getSpecificShpxx($shpbh));
	}
	
	/*
	 * 获取待验区信息
	 */
	public function getdyqxxAction()
	{
		$model = new cc_models_cgyrk();
		echo Common_Tool::json_encode($model->getDyqxx());
	}
	
	/*
	 * 获取采购单明细信息(cgyrk_01页面使用)
	 */
	public function getmingxiAction()
	{
		$cgdbh = $this->_getParam('cgdbh');
		$model = new cc_models_cgyrk();
		echo Common_Tool::json_encode($model->getmingxi($cgdbh));
	}
	
	/*
	 * 保存
	 */
	public function saveAction()
	{
		$result['status'] = '0';
		try
		{
			$model = new cc_models_cgyrk();
			$message = $model->turnMessage($_POST['msg']);
			if(!$model->inputCheck())
			{
				$result['status'] = '1';    //必须输入项目验证
			}else 
            {
                //开始一个事物
                $model->beginTransaction();
               
                //获取预入库单编号
                $yrkdbh = Common_Tool::getDanhao('YRK', $_POST['KPRQ']);
                
                //预入库单信息保存
                $model->saveYrkd($yrkdbh);
                
                //保存明细信息
                $model->saveMingxi($yrkdbh);
                
                //更新采购明细商品状态
                $model->updateShpZht($yrkdbh);
                
                //保存message信息
                if($_POST['rkzt'] == '1')
                {
                	if(count($message) > 0)
                	{
                		$model->saveMessage($message, $yrkdbh);
                	}else
                    {
                        $result['status'] = '2';    //状态与message不符
                    }
                }
                
               //保存成功
               if($result['status'] == '0')
               {
                   $result['yrkdbh'] = $yrkdbh;   //新生成的采购入库单编号
                   $model->commit();
                   Common_Logger::logToDb("采购预入库 预入库单编号：$yrkdbh");
               }else {
                   $model->rollBack();
               }
           }
           echo json_encode($result);
		}catch (Exception $e)
		{
			$model->rollBack();
			throw $e;
		}
	}
}
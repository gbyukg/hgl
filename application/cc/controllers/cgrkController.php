<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购入库(CGRK)
 * 作成者：ZhangZeliang
 * 作成日：2011/03/30
 * 更新履历：

 *********************************/
class cc_cgrkController extends cc_controllers_baseController
{
	/*
	 * 采购入库页面
	 */
	public function indexAction()
	{
		$this->_view->assign ("kprq", date("Y-m-d"));  		//开票日期
		$this->_view->assign ("title", "仓储管理-采购入库"); 	//标题
		$this->_view->display ("cgrk_01.php");
	}
	
	/*
	 * 采购单选择页面
	 */
	public function cgdlistAction()
	{
		$this->_view->assign("title", "仓储管理-入库质检信息选择");
		$this->_view->assign ( "KSRQKEY", date ( "Y-m-d",time() - (14 * 24 * 60 * 60) ) );    //开始日期
        $this->_view->assign ( "ZZRQKEY", date ( "Y-m-d" ) );   //终止日期
		$this->_view->display("cgrk_02.php");
	}
	
	/*
	 * 质检信息明细选择页面
	 */
	public function zjmxlistAction()
	{
		$this->_view->assign("title", "仓储管理-质检信息明细选择");
		$this->_view->assign("yrkdbh", $this->_getParam("yrkdbh"));
		$this->_view->display("cgrk_03.php");
	}
	
    /*
     * 货位选择页面(cgrk_04.php)
     */
    public function showhuoweiAction()
    {
        $this->_view->assign("shpbh" ,$this->_getParam("shpbh"));
        $this->_view->assign("pihao", $this->_getParam("pihao"));
        $this->_view->assign("zhdkqlx" ,$this->_getParam("zhdkqlx"));
        $this->_view->display("cgrk_04.php");
    }
	
	/*
	 * 采购单列表xml数据取得(cgrk_02.php页面)
	 */
	public function getcgdlistdataAction()
	{
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
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
                unset($_SESSION['rkzj_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['rkzj_filterParams'] = $_POST;
                unset($_SESSION['searchParams']); //清空一般查询条件
            }
        }
        //取得检索条件
        $filter['filterParams'] = $_SESSION['rkzj_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
		$model = new cc_models_cgrk();
		header("Content-type:text/xml");
		echo $model->getCgGridData($filter);
	}
	
	/*
	 * 采购单明细xml数据获取(cgrk_02.php页面)
	 */
	public function getxzmxAction()
	{
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter["yrkdbh"] = $this->_getParam("cgdbh");				//采购单编号
		$model = new cc_models_cgrk();
		header("Content-type:text/xml");
		echo $model->getCgdXzMx($filter);
	}
	
	/*
	 * 根据预入库单编号获取单据信息
	 */
	public function getdjinfoAction()
	{
		$yrkdbh = $this->_getParam("yrkdbh");
		$model = new cc_models_cgrk();
		echo Common_Tool::json_encode($model->getdjinfo($yrkdbh));
	}
	
	/*
	 * 根据预入库单编号获取单据明细信息(cgrk_01.php页面)
	 */
	public function getmingxiinfoAction()
	{
			$yrkdbh = $this->_getParam("yrkdbh");
			$model = new cc_models_cgrk();
			echo Common_Tool::json_encode( $model->yrkdspmxInfo($yrkdbh));
	}
	
	/*
	 * 判断数据库中是否存制定的库位中是否存在与给定批号相同的同一商品
	 */
	public function pdphhwAction()
	{
		$filter["shpbh"] = $this->_getParam("shpbh");
		$filter["pihao"] = $this->_getParam("pihao");
		$filter["ckbh"] = $this->_getParam("ckbh");
		$filter["kqbh"] = $this->_getParam("kqbh");
		$filter["kwbh"] = $this->_getParam("kwbh");
		$model = new cc_models_cgrk();
		echo Common_Tool::json_encode($model->pdPhHw($filter));
	}
	
	/*
	 * 获取质检信息明细选择表详细信息
	 */
	public function zjmxdataAction()
	{
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
        $filter ['count'] = $this->_getParam ( "count", 50 );       //默认显示数量
        $filter["yrkdbh"] = $this->_getParam("yrkdbh");              //预采购单编号
        $model = new cc_models_cgrk();
        header("Content-type:text/xml");
        echo $model->zjmxData($filter);
	}
	
	/*
     * 获取采购入库页面明细信息表中每条数据所在仓库的状态
     */
	public function kwztAction()
	{
		$filter["ckbh"] = $this->_getParam("ckbh");
		$filter["kqbh"] = $this->_getParam("kqbh");
		$filter["kwbh"] = $this->_getParam("kwbh");
		$model = new cc_models_cgrk();
		echo Common_Tool::json_encode($model->kuweiZt($filter));
	}
	
	/*
     * 获取采购入库页面明细信息表中每条数据的仓库类型
     */
	public function getkqlxAction()
	{
		$filter["ckbh"] = $this->_getParam("ckbh");
		$filter["kqbh"] = $this->_getParam("kqbh");
		
		$model = new cc_models_cgrk();
		echo Common_Tool::json_encode($model->getKqlx($filter));
	}
	
	/*
     * 更新数据
     */
	public function updatemingxiAction()
	{
	   $result["status"] = "0";
	   try
	   {
	   	   $model = new cc_models_cgrk();
	   	   if(!$model->inputCheck())
	   	   {
	   	       $result["status"] = '1';    //必须输入项目验证
	   	   }else if(!$model->logicCheck())
	   	   {
	   	   	   $result['status'] = '2';    //项目合法性验证错误
	   	   }else {
	   	   	   //开始一个事物
	   	   	   $model->beginTransaction();
	   	   	   
	   	   	   //获取入库单编号
	   	   	   $rkdbh = Common_Tool::getDanhao('RKD', $_POST['KPRQ']);
	   	   	   
	   	   	   //入库单信息保存
	   	   	   $model->saveRukudan($rkdbh);
	   	   	   
	   	   	   //入库单明细信息保存
	   	   	   $model->executeMingxi($rkdbh);

	   	   	   //更新采购单状态(H01DB012306)
	   	   	   $model->updateCgd06();
	   	   	   
	   	   	   //更新采购单状态(H01DB012429)
	   	   	   $model->updateCgd29();
	   	   	   
	   	   	   //保存成功
	   	   	   if($result['status'] == '0')
	   	   	   {
	   	   	   	   $result['data'] = $rkdbh;   //新生成的采购入库单编号
	   	   	   	   $model->commit();
	   	   	   	   Common_Logger::logToDb("新采购入库单编号：".$rkdbh);
	   	   	   }else {
	   	   	   	   $model->rollBack();
	   	   	   }
	   	   }
	   	   echo json_encode($result);
	   }catch (Exception $e)
	   {
	   	   //回滚
	   	   $model->rollBack();
	   	   throw $e;
	   }
	}
	
	/*
	 *获取仓库选择页面的仓库信息(cgrk_04.php)
	 */
	public function loadckAction()
	{
		$filter["shpbh"] = $this->_getParam("shpbh");
		$filter["pihao"] = $this->_getParam("pihao");
		$filter["zhdkqlx"] = $this->_getParam("zhdkqlx");
		$model = new cc_models_cgrk();
		header("Content-type:text/xml");
		echo $model->loadCangKu($filter);
	}
}

?>
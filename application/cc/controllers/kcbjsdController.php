<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库存位报警线设定(kcbjsd)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/18
 * 更新履历：
 *********************************/
class cc_kcbjsdController extends cc_controllers_baseController 
{
	/*
	 * 页面显示
	 */
	public function indexAction()
	{
		$this->_view->assign ( 'title', '仓储管理-库存报警设定' );
        $this->_view->display ( 'kcbjsd_01.php' ); 
	}
	
    /*
     * 报警限设定
     */
    public function alarmsetAction()
    {
    	$model = new cc_models_kcbjsd();
    	$shpbh = $this->_getParam('shpbh');
        $this->_view->assign ( 'title', '仓储管理-库存报警设定' );
        $this->_view->assign('shpbh', $shpbh);
        $this->_view->assign ( "rec", $model->getshpxx ($shpbh));
        $this->_view->display ( 'kcbjsd_02.php' );
    }
	
	/*
	 * 数据检索
	 */
	public function getlistdataAction()
	{
		//取得分页排序参数
        $filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
        $filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
        $filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
        $filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式       
        //保持排序条件
        $_SESSION["kcbjsd_sortParams"]["orderby"] = $filter ['orderby'];
        $_SESSION["kcbjsd_sortParams"]["direction"] = $filter ['direction'];
        
        //一般查询
        if($this->_getParam ( "isfilter", '0' )=='0'){
           //取得一般查询条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['kcbjsd_searchParams'] = $_POST;
                unset($_SESSION['kcbjsd_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['kcbjsd_filterParams'] = $_POST;
                unset($_SESSION['kcbjsd_searchParams']); //清空一般查询条件
            }
        }

        //取得检索条件
        $filter['filterParams'] = $_SESSION['kcbjsd_filterParams'];  //精确查询条件
        $filter['searchParams'] = $_SESSION['kcbjsd_searchParams'];  //固定查询条件
        $model = new cc_models_kcbjsd();
        header ( "Content-type:text/xml" ); //返回数据格式xml     
        echo $model->getGridData ( $filter );
	}
	
	/*
	 * 更新上线限设定
	 */
	function saveAction()
	{
		$filter['shpbh'] = $this->_getParam('SHPBH'); //商品编号
		$filter['kcshx'] = $this->_getParam('KCSHX'); //库存上线
		$filter['kcxx'] = $this->_getParam('KCXX'); //库存上线
		$model = new cc_models_kcbjsd();
		$model->updateData($filter);
	}
	
	/*
	 * 页面直接保存
	 */
	public function updatesaveAction()
	{
		$model = new cc_models_kcbjsd();
		$model->updateSave();
	}
}






?>
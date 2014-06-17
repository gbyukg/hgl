<?php
/*********************************
 * 模块：    仓储模块(cc)
 * 机能：    直接入库(ZJRK)
 * 作成者：姚磊
 * 作成日：2010/1/10
 * 更新履历：
 *********************************/
class cc_zjrkController extends cg_controllers_baseController {
	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1;// 商品编号
	private $idx_SHPMCH = 2;// 商品名称
	private $idx_GUIGE = 3;// 商品规格
	private $idx_BZHDWM = 4;// 包装单位
	private $idx_PIHAO=5;// 批号
	private $idx_HWMCH=6;// 货位
	private $idx_SHCHRQ=7;// 生产日期
	private $idx_BZHQZH=8;// 保质期至
	private $idx_JLGG = 9;// 计量规格
	private $idx_BZHSHL = 10;// 包装数量
	private $idx_LSSHL = 11;// 零散数量
	private $idx_SHULIANG = 12;// 数量
	private $idx_DANJIA = 13;// 单价
	private $idx_HSHJ = 14;// 含税价
	private $idx_KOULV = 15;// 扣率
	private $idx_SHUILV = 16;// 税率
	private $idx_HSHJE = 17;// 含税金额
	private $idx_JINE = 18; // 金额
	private $idx_SHUIE = 19;// 税额
	private $idx_LSHJ = 20; // 零售价
	private $idx_CHANDI = 21;// 产地
	private $idx_BEIZHU = 22;// 备注
	private $idx_TONGYONGMING = 23; // 通用名
	private $idx_KWSHULIANG = 24;// 最大入库数量
	private $idx_BZHDWBH = 25; // 包装单位编号
	private $idx_XUHAO = 26; // 序号
	private $idx_ZHDKQLX=27;// 指定库区类型
	private $idx_KQLXMCH=28;// 指定库区类型名称
	private $idx_SHFSHKW=29;// 是否散货区
	private $idx_CKBH=30;// 仓库编号
	private $idx_KQBH=31;// 库区编号
	private $idx_KWBH=32;// 库位编号
	
	
	/*
	 * 采购开票初始页面
	 */
	public function indexAction() { 	
		$this->_view->assign ( "kprq", date("Y-m-d"));  //开票日期
		$this->_view->assign ( "title", "仓储模块- 直接入库" ); //标题
		$this->_view->display ( "zjrk_01.php" );
	}

			
	/*
	 * 采购开票保存
	 */

	public function saveAction() {
		$result['status'] = '0'; 
		try {
			$cgkpModel = new cc_models_zjrk();
			
			//必须输入项验证
			if(!$cgkpModel->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$cgkpModel->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $cgkpModel->beginTransaction ();
			    //入库单编号取得
			    $zjbh = Common_Tool::getDanhao('CGD',$_POST['KPRQ']); //采购单据号				    
			    $dwbh = $_POST['DWBH'];
			    $syturnValue = $cgkpModel->shenpiCheck($dwbh);  //首营审批check
			    //定义一个序号，自增，用来当做数组下标
			    $index = 1;
			    if($syturnValue['status']!='0' && $syturnValue ['status']!=""){
			       $result['num'][$index] = "单位编号：【".$dwbh."】供应商首营审批没有通过。 \n";
			       $result['fenlei'][$index] = $syturnValue['status'];
			       $index ++;
			    }else{
			        $qxCheck = $cgkpModel->qxCheck($dwbh);	//Check首营期限
				if($syturnValue['status'] ==02 ){
			       $result['num'][$index] = "单位编号：【".$dwbh."】供应商首营期限已过期， 需要进行审批处理。 \n";
			       $result['fenlei'][$index] = $qxCheck['status'];
			       $index ++;
				}	
			    }
			    
			
				foreach ( $_POST ["#grid_mingxi"] as $row ) {
					if ($row [$this->idx_SHPBH] == '')
						continue;
					$spValue = $cgkpModel->spCheck($row [$this->idx_SHPBH]); 		//商品首营期审批没有通过check
					if( $spValue ['status']!='0' && $spValue ['status']!=""){
				       $result['num'][index] = "商品编号：【".$row [$this->idx_SHPBH]."】商品首营审批没有通过， 需要进行审批处理 \n";
				       $result['fenlei'][index] = $spValue ['status'];
				       $index ++;
					}
					 $spqxCheck = $cgkpModel->spqxCheck($row [$this->idx_SHPBH]);	//商品首营期限已过期check	
					if( $spValue ['status'] ==04 ){
				       $result['num'][$index] = "商品编号：【".$row [$this->idx_SHPBH]."】商品首营期限已过期， 需要进行审批处理 \n";
				       $result['fenlei'][$index] = $spqxCheck['status'];
				       $index ++;
					}	
				    $returnValue =  $cgkpModel->shifMax($row [$this->idx_SHPBH],$row [$this->idx_SHULIANG]);	//最大采购数量check
					if( $returnValue ['status']!='0' && $returnValue ['status']!=""){
				       $result['num'][$index] = "商品编号：【".$row [$this->idx_SHPBH]."】商品的采购数量超过最大采购数量， 需要进行审批处理 \n";
				       $result['fenlei'][$index] = $returnValue['status'];
				       $index ++;
					}
				    $zdjgValue = $cgkpModel->jgMax($row [$this->idx_SHPBH]);		//最大采购价格check
					if($zdjgValue['SHPBH'] == $row [$this->idx_SHPBH]){
						
				       $result['num'][$index] = "商品编号：【".$row [$this->idx_SHPBH]."】商品的采购价格超过最大采购价格， 需要进行审批处理 \n";
				       $result['fenlei'][$index] = $zdjgValue['status'];
				        $index ++;
					}
					
				    $spyxValue = $cgkpModel->spyxCheck($row [$this->idx_SHPBH]);	//商品优先供应指定供应商check
				    if($spyxValue ['status']=='30'){
				    	$shpValue = $cgkpModel->gysCheck($row [$this->idx_SHPBH]);
					if( $shpValue ['status']=='08'){
				       $result['num'][$index] = "单位编号：【".$dwbh."】的供应商不是商品编号：【".$row [$this->idx_SHPBH]."】商品的优先指定供应商， 需要进行审批处理\n";
				       $result['fenlei'][$index] = $shpValue['status'];
				        $index ++;
					}else if($shpValue ['status']=='40'){
						$spyxValue = $cgkpModel->danwCheck($row [$this->idx_SHPBH]);
						if( $spyxValue ['status']=='07'){
					   $result['num'][$index] = "单位编号：【".$dwbh."】的供应商不是商品编号：【".$row [$this->idx_SHPBH]."】商品的最优指定供应商， 需要进行审批处理\n";
				       $result['fenlei'][$index] = $spyxValue['status'];
				       $index ++;
					}}
				}
				}
			    //保存成功
			    if($result['status'] == '0'){
			  
			    	$result['dat'] = $zjbh;
			    	$result['data'];
			    }else{
				    $cgkpModel->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$cgkpModel->rollBack ();
     		throw $e;
		}
	
	}
	
	
	/*
	 * 保存开票采购数据
	 */
	function savecgAction(){
		$result['status'] = '0'; 
		try{
			$cgkpModel = new cc_models_zjrk();
		    	$cgkpModel->beginTransaction ();
			    //直接入库单编号取得
			
		    	
			    $cgkpbh = Common_Tool::getDanhao('CGD',$_POST['KPRQ']); //采购单据号		  
			    //采购表单保存
			    $cgkpModel->saveCgkpMain($cgkpbh);
			     //采购明细保存
			    $cgkpModel->saveCgkpMingxi($cgkpbh);			   			    
			 $filter ['errormeg'] = $_POST['ERRORMEG']; 
			 $filter ['error'] = $_POST['ERROR']; 
			 if($filter ['error'] == ''){
			  $zjrkbh = Common_Tool::getDanhao('RKD',$_POST['KPRQ']); //直接入库单据号	
			   $cgkpModel->errorSave($cgkpbh,$filter); //保存错误信息
			   	$cgkpModel->updateZjrkMain($zjrkbh,$cgkpbh);
			   	$cgkpModel->updateZjrkMingxi($zjrkbh);  
			   	$cgkpModel->LoginZjrkMingxi($zjrkbh);
			   	$cgkpModel->Movelvl($zjrkbh);
			   	$result['data'] = $zjrkbh; //直接入库编号
			   	$rkdbh=" 入库单编号：".$zjrkbh;
			 }
			   	
			    //审批警告信息保存
			    $cgkpModel->commit ();
			    
				Common_Logger::logToDb ("直接入库单做成  采购单据号：".$cgkpbh.$rkdbh);
				
				$result['dat'] = $cgkpbh; //采购单编号
				echo json_encode($result);
		}catch( Exception $e){
		//回滚
			$cgkpModel->rollBack ();
     		throw $e;
		}
		}

    /**
     * 通过商品编号取得商品相关信息
     *
     */
	public function getshangpininfoAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$cgkpModel = new cc_models_zjrk();
		
	    echo json_encode($cgkpModel->getShangpinInfo($filter));
	}
	
	/**
	 * 获取入库限定数量
	 */
	public function getrkxzhshlAction(){
		$filter ['shpbh'] = $this->_getParam('shpbh');   //商品编号
		$rkxzhshl = new cc_models_zjrk();
		echo Common_Tool::json_encode($rkxzhshl->getRkxzhshlInfo($filter));
	}
	 /**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new xs_models_xskp ( );					//?
		
	    echo Common_Tool::json_encode($xskpModel->getDanweiInfo($filter));
	}
	
	/*
	 * 检查账期是否超期
	 */
	public function checkxdqAction(){
		
		$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new xs_models_xskp ( );					//?
		
	    echo $xskpModel->checkXdq($filter);
	}



 	

 	
 	
 	
 	
 	
}
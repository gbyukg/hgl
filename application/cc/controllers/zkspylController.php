<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   在库商品一览(ZKSPYL)
 * 作成者：魏峰
 * 作成日：2011/01/12
 * 更新履历：
 *********************************/
class cc_zkspylController extends cc_controllers_baseController {
	
	/*
     * 在库商品一览画面显示
     */
	public function indexAction() {
		$this->_view->assign ( "title", "仓储管理-当前在库商品一览" ); 	//标题	
		$this->_view->display ( 'zkspyl_01.php' );
	}
	
    /*
	 * 得到在库商品数据
	 */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",10); //默认显示数量
		//$filter ['shpbh'] = $this->_getParam ( "shpbh", '' ); //检索条件
		//$filter ['ckbh'] = $this->_getParam ( "ckbh", '' ); //检索条件
		//$filter ['kqbh'] = $this->_getParam ( "kqbh", '' ); //检索条件
		//$filter ['kwbh'] = $this->_getParam ( "kwbh", '' ); //检索条件
    	$filter ['orderby'] = $this->_getParam ( "orderby",1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式

		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){				
			//取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['searchParams'] = $_POST;
				unset($_SESSION['zkspyl_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['zkspyl_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}				
		}
				
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件		
		$filter['filterParams'] = $_SESSION['zkspyl_filterParams'];  //精确查询条件
		
		$model = new cc_models_zkspyl( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
     * 在库商品状态修改画面显示
     */
	public function stateupdAction() {
		$zkspylModel = new cc_models_zkspyl ( );
		$this->_view->assign ( "title", "仓储管理-在库商品状态更新" ); 	//标题	
		$this->_view->assign ( "kprq", date("Y-m-d"));                             //开票日期
		$this->_view->assign ( "shpbh", $this->_getParam ( "shpbh", '' ) );        //商品编号
		$this->_view->assign ( "shpmch",$this->_getParam ( "shpmch", '' ) );      //商品名称
		$this->_view->assign ( "kuwei", $this->_getParam ( "kuwei", '' ) );        //库位名称
		$this->_view->assign ( "pihao", $this->_getParam ( "pihao", '' ) );        //批号
		$this->_view->assign ( "shchrq", $this->_getParam ( "shchrq", '' ) );      //生产日期
		$this->_view->assign ( "bzhqzh", $this->_getParam ( "bzhqzh", '' ) );      //保质期至
		$this->_view->assign ( "zkzht", $this->_getParam ( "zkzht", '' ) );   	   //在库状态
		$this->_view->assign ( "danwei", $this->_getParam ( "danwei", '' ) );      //单位
		$this->_view->assign ( "zkzhtbh", $this->_getParam ( "zkzhtbh", '' ) );    //在库状态编号
		$this->_view->assign ( "danweibh", $this->_getParam ( "danweibh", '' ) );  //单位编号	
		$this->_view->assign ( "ckbh", $this->_getParam ( "ckbh", '' ) );          //仓库编号
		$this->_view->assign ( "kqbh", $this->_getParam ( "kqbh", '' ) );          //库区编号
		$this->_view->assign ( "kwbh", $this->_getParam ( "kwbh", '' ) );          //库位编号
		$this->_view->assign ( "kwzht", $this->_getParam ( "kwzht", '' ) );        //库位状态
		$this->_view->assign ( "jlgg", $this->_getParam ( "jlgg", '' ) );          //计量规格
		$this->_view->assign ( "shfshwk", $this->_getParam ( "shfshwk", '' ) );    //是否散货库位
		$this->_view->assign ( "bmmch", $_SESSION ['auth']->bmmch);
		$this->_view->assign ( "bmbh", $_SESSION ['auth']->bmbh);
		
		$filter ["ckbh"] = $this->_getParam ( "ckbh", '' );
		$filter ["kqbh"] = $this->_getParam ( "kqbh", '' );
		$filter ["kwbh"] = $this->_getParam ( "kwbh", '' );
		$filter ["shpbh"] = $this->_getParam ( "shpbh", '' );
		$filter ["pihao"] = $this->_getParam ( "pihao", '' );
		$filter ["zkzhtbh"] = $this->_getParam ( "zkzhtbh", '' );
		$filter ["danweibh"] = $this->_getParam ( "danweibh", '' );
		$filter ["shchrq"] = $this->_getParam ( "shchrq", '' );
		$filter ["bzhqzh"] = $this->_getParam ( "bzhqzh", '' );
				
		$this->_view->assign ( "rkdjh", $zkspylModel->getRKDInfo($filter) );              //入库单号的取得
											
		$this->_view->display ( 'zkspztgx_01.php' );
	
	}
	
	/*
     * 保存商品状态变更信息(在库状态更新)
     */
	public function saveAction() {
		$result['status'] = '0'; 
		$zhtai = $this->_getParam ("zhuangtai"); //状态
		try {
		$zkspylModel = new cc_models_zkspyl ( );
		//必须输入项验证
		if(!$zkspylModel->inputCheck(1)){
			$result['status'] = '1';  //必须输入项验证错误
		}else{
			//开始一个事务
		    $zkspylModel->beginTransaction ();
		    //单据编号取得
		    $danjvbh = Common_Tool::getDanhao('ZTT',$_POST['KPRQ']);
		    //变更前在库数量检索
			$bgqshul = $zkspylModel->getBgqshul('1');
			//如果画面项目数量 = 取到的数量，更新DB:在库商品信息（H01DB012404)
			if ($_POST['SHUL'] == $bgqshul){
				$zkspylModel->updateZaiku($bgqshul,0,$danjvbh,0);	
			//如果画面项目数量 < 取到的数量，更新DB:在库商品信息（H01DB012404)
			}elseif ($_POST['SHUL'] < $bgqshul){
				$zkspylModel->updateZaiku($bgqshul,1,$danjvbh,0);
			//如果画面项目数量 > 取到的数量，,弹出警告信息，rollback	
			}else{
				$result['status'] = '3'; 
				$zkspylModel->rollBack ();//有错误发生
				echo json_encode($result);
				return;				
			}
			
		    //变更后在库数量检索 
			$bghshul = $zkspylModel->getBgqshul('2',$zhtai);
			//如果不存在，登录下记信息(insert)到DB:在库商品信息（H01DB012404)
			if ($bghshul == null){
				$zkspylModel->insertZaiku($zhtai);
			//如果存在并且数量 > 0，更新DB:在库商品信息（H01DB012404)	
			}elseif($bghshul > 0){
				$zkspylModel->updateBghZaiku($bghshul,1,$zhtai);
			//如果存在并且数量 = 0，更新DB:在库商品信息（H01DB012404)	
			}else{
				$zkspylModel->updateBghZaiku($bghshul,0,$zhtai);
			}
			
			//更新DB:商品移动履历（H01DB012405)
			$zkspylModel->insertYidongll($danjvbh,$zhtai);
						
			//登录在库商品状态更新信息
			$zkspylModel->insertZhangtaigx($danjvbh,$zhtai);
			
			$zkspylModel->commit ();
		    Common_Logger::logToDb("在库商品状态更新 单据编号：".$danjvbh);
		}
		
		echo json_encode($result);
		}catch ( Exception $e )
	    {
			//回滚
			$zkspylModel->rollBack ();
     		throw $e;
		}
	}
	
	/*
     * 在库商品批号调整画面显示
     */
	public function pihaoupdAction() {
		$zkspylModel = new cc_models_zkspyl ( );
		$this->_view->assign ( "title", "仓储管理-在库商品批号调整" ); 	//标题	
		$this->_view->assign ( "kprq", date("Y-m-d"));                             //开票日期
		$this->_view->assign ( "shpbh", $this->_getParam ( "shpbh", '' ) );        //商品编号
		$this->_view->assign ( "shpmch", $this->_getParam ( "shpmch", '' ) );      //商品名称
		$this->_view->assign ( "kuwei", $this->_getParam ( "kuwei", '' ) );        //库位名称
		$this->_view->assign ( "pihao", $this->_getParam ( "pihao", '' ) );        //批号
		$this->_view->assign ( "shchrq", $this->_getParam ( "shchrq", '' ) );      //生产日期
		$this->_view->assign ( "bzhqzh", $this->_getParam ( "bzhqzh", '' ) );      //保质期至
		$this->_view->assign ( "zkzht", $this->_getParam ( "zkzht", '' ) );   	   //在库状态
		$this->_view->assign ( "danwei", $this->_getParam ( "danwei", '' ) );      //单位
		$this->_view->assign ( "zkzhtbh", $this->_getParam ( "zkzhtbh", '' ) );    //在库状态编号
		$this->_view->assign ( "danweibh", $this->_getParam ( "danweibh", '' ) );  //单位编号	
		$this->_view->assign ( "ckbh", $this->_getParam ( "ckbh", '' ) );          //仓库编号
		$this->_view->assign ( "kqbh", $this->_getParam ( "kqbh", '' ) );          //库区编号
		$this->_view->assign ( "kwbh", $this->_getParam ( "kwbh", '' ) );          //库位编号
		$this->_view->assign ( "kwzht", $this->_getParam ( "kwzht", '' ) );        //库位状态
		$this->_view->assign ( "jlgg", $this->_getParam ( "jlgg", '' ) );          //计量规格
		$this->_view->assign ( "shfshwk", $this->_getParam ( "shfshwk", '' ) );    //是否散货库位
		$this->_view->assign ( "bmmch", $_SESSION ['auth']->bmmch);
		$this->_view->assign ( "bmbh", $_SESSION ['auth']->bmbh);
		
		$filter ["ckbh"] = $this->_getParam ( "ckbh", '' );
		$filter ["kqbh"] = $this->_getParam ( "kqbh", '' );
		$filter ["kwbh"] = $this->_getParam ( "kwbh", '' );
		$filter ["shpbh"] = $this->_getParam ( "shpbh", '' );
		$filter ["pihao"] = $this->_getParam ( "pihao", '' );
		$filter ["zkzhtbh"] = $this->_getParam ( "zkzhtbh", '' );
		$filter ["danweibh"] = $this->_getParam ( "danweibh", '' );
		$filter ["shchrq"] = $this->_getParam ( "shchrq", '' );
		$filter ["bzhqzh"] = $this->_getParam ( "bzhqzh", '' );
				
		$this->_view->assign ( "rkdjh", $zkspylModel->getRKDInfo($filter) );              //入库单号的取得
		
		
	    $this->_view->display ( 'zkspphtz_01.php' );
	}
	
    /*
     * 保存商品状态变更信息(生产批号更新)
     */
	public function savepihaoAction() {
		$result['status'] = '0'; 
		try {
			$zkspylModel = new cc_models_zkspyl ( );
			if(!$zkspylModel->inputCheck(2)){
				$result['status'] = '1';  //必须输入项验证错误
			}else{
				//开始一个事务
			    $zkspylModel->beginTransaction ();
			    //单据编号取得
			    $danjvbh = Common_Tool::getDanhao('PHT',$_POST['KPRQ']);		
			    //变更前在库数量检索
				$bgqshul = $zkspylModel->getBgqshulPihao('1');
				//如果画面项目数量 = 取到的数量，更新DB:在库商品信息（H01DB012404)
				if ($_POST['SHUL'] == $bgqshul["SHULIANG"]){
					$zkspylModel->updateZaiku($bgqshul["SHULIANG"],0,$danjvbh,1);
				//如果画面项目数量 < 取到的数量，更新DB:在库商品信息（H01DB012404)
				}elseif ($_POST['SHUL'] < $bgqshul["SHULIANG"]){
					$zkspylModel->updateZaiku($bgqshul["SHULIANG"],1,$danjvbh,1);
				//如果画面项目数量 > 取到的数量，,弹出警告信息，rollback	
				}else{
					$result['status'] = '3'; 
					$zkspylModel->rollBack ();//有错误发生
					echo json_encode($result);		
			    	return;
				}
				
				//变更后在库数量检索 
				$bghshul = $zkspylModel->getBgqshulPihao('2');			
				//如果不存在，登录下记信息(insert)到DB:在库商品信息（H01DB012404)
				if ($bghshul["SHULIANG"] == null){
					$zkspylModel->insertZaikuPihao();
				//如果存在并且数量 > 0，更新DB:在库商品信息（H01DB012404)	
				}elseif($bghshul["SHULIANG"] > 0){
					//如果存在并且取到的保质期至同画面项目调整后保质期至(不为空)不一致rollback,弹出警告窗口。
					if ($bghshul["BZHQZH"] != $_POST['TZHHBZHQZH']){
						$result['status'] = '4'; 
						$result['data'] = $bghshul["BZHQZH"];
						$zkspylModel->rollBack ();//有错误发生
						echo json_encode($result);	
						return;
					}
					$zkspylModel->updateBghZaikuPihao($bghshul["SHULIANG"],1);
				//如果存在并且数量 = 0，更新DB:在库商品信息（H01DB012404)	
				}else{
					//如果存在并且取到的保质期至同画面项目调整后保质期至(不为空)不一致rollback,弹出警告窗口。
					if ($bghshul["BZHQZH"] != $_POST['TZHHBZHQZH']){
						$result['status'] = '4'; 
						$result['data'] = $bghshul["BZHQZH"];
						$zkspylModel->rollBack ();//有错误发生
						echo json_encode($result);	
						return;
					}				
					$zkspylModel->updateBghZaikuPihao($bghshul["SHULIANG"],0);
				}

		    	//更新DB:商品移动履历（H01DB012405)
			    $zkspylModel->insertYidongllPh($danjvbh);				
				
				//登录在库商品批号修改信息
				$zkspylModel->insertPihaoxg($danjvbh);
				
				$zkspylModel->commit ();
			    Common_Logger::logToDb("在库商品批号调整 单据编号：".$danjvbh);
			
			    echo json_encode($result);
		   }  
		}catch ( Exception $e )
	    {
			//回滚
			$zkspylModel->rollBack ();
     		throw $e;
		}	
	}
}
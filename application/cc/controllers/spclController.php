<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       商品拆零(spcl)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/12/6
 ***** 更新履历：
 ******************************************************************/

class cc_spclController extends cc_controllers_baseController {
	
	/*
     * 商品拆零画面显示
     */
	public function indexAction() {
		$date = new Zend_Date();
		$this->_view->assign ( "RIQI", $date->toString("YYYY-MM-dd"));
		$this->_view->assign ( "ywyid", $_SESSION ["auth"]->userId );    //操作员编号
		$this->_view->assign ( "ywymch", $_SESSION ["auth"]->userName ); //操作员
		$this->_view->assign ( "bmbh", $_SESSION ["auth"]->bmbh );       //部门编号
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch );     //部门
		$this->_view->display ( 'spcl_01.php' );
	}
	
	/*
     * 获取拆零单位
     */
	public function getchldwAction() {
		$model = new cc_models_spcl();
		$chldw = $model->getDanwei($this->_getParam ('spbh'));
		echo Common_Tool::json_encode($chldw);
	}
	
	
	/*
     * 获取商品信息
     */
	public function getspxxAction() {
		$model = new cc_models_spcl();
		$result = $model->getspxx($this->_getParam ('spbh'));
		echo Common_Tool::json_encode($result);
	}
	
	/*
     * 获取商品在库信息
     */
	public function getzkxxAction() {
		$model = new cc_models_spcl();
		$result = $model->getzkxx($this->_getParam ('spbh'));
		echo Common_Tool::json_encode($result);
	}
	
	
	/*
     * 根据商品编号、拆零前后单位和拆零数量，获取商品拆零后的数量
     */
	public function getclhslAction() {
		$clhdw = $this->_getParam ('clhdw');
		$bzhdwbh =  $this->_getParam ('bzhdwbh');
		$shpbh = $this->_getParam ('shpbh');
		$clsl = $this->_getParam ('clsl');
		
		$model = new cc_models_spcl();
		$shuliang = $model->getclhsl($shpbh,$bzhdwbh,$clhdw,$clsl);
		echo $shuliang;
	}
	
	
	/*
	 * 判断商品编号是否存在
	 */
	public function spbhcheckAction() {
		$model = new cc_models_spcl();
		if ($model->getSpbh($this->_getParam ('spbh')) == FALSE) {
			echo 0; //不存在
		} else {
			echo 1; //存在
		}
	}
	

	/*
	 * 判断拆零后单位是否现有单位的下级单位
	 */
	public function clhdwcheckAction() {
		$model = new cc_models_spcl();
		if ($model->getclhdw($this->_getParam ('shpbh'),$this->_getParam ('bzhdwbh'),$this->_getParam ('clhdw')) == FALSE) {
			echo 0; //不合理
		} else {
			echo 1; //合理
		}
	}
	
	
	/*
	 * 判断拆零时库位数量是否大于拆零数量
	 */
	public function clslcheckAction() {
		$model = new cc_models_spcl();
		$clsl = $this->_getParam('clsl');
		$zksl = $model->getclsl($this->_getParam('shpbh'),$this->_getParam ('ckbh'),$this->_getParam ('kqbh'),$this->_getParam ('kwbh'),$this->_getParam ('pihao'),$this->_getParam ('zkzht'),$this->_getParam ('danwei'));
		if($clsl > $zksl){
			echo 0; //不合理
		} else {
			echo 1; //合理
		}
	}
	
	
	
	/*
	 * 商品拆零操作
	 */
	public function saveAction() {
		$result = array ();                 //定义返回值
		
		try {
			$model = new cc_models_spcl();
			//开始一个事务
			$model -> beginTransaction ();	
			
			//必须输入项验证
			if(!$model->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}else{
				
				//$SPCLDBH = $model->getSpclbh();     //获取商品拆零单据号
			    $SPCLDBH = Common_Tool::getDanhao('CLD',$_POST['KPRQ']);
				$result['DJBH'] = $SPCLDBH;
				
				$Zkshpxx = $model->getzkshpxx();    //获取相应在库商品信息
				
				$checksl = 0;
				foreach ( $Zkshpxx as $zkxx ) {
					$checksl = $checksl + $zkxx['SHULIANG']; 
				}
				
				if( $checksl == (int)$_POST ['KWSL'] ){
					
					$SYCLSL = $_POST ['CLSL'];          //剩余拆零数量
					$Rowid = 0;                         //相应在库商品信息行号
					$XUHAO = 1;                         //为商品移动履历设定序号
				
					while ($SYCLSL > 0){
						$CLSL = $Zkshpxx[$Rowid]['SHULIANG'];    //本条在库商品信息可以拆零的数量
						$RKDH = $Zkshpxx[$Rowid]['RKDBH'];       //本条在库商品信息的相关入库单号
						                 
						$SYCLSL = $SYCLSL - $CLSL;               //剩余拆零数量 = 原剩余拆零数量 - 拆零数量
						
						if($SYCLSL < 0){
							$SL = 0 - $SYCLSL;
							
							$model->updateZksp($SL, $RKDH, "0");      //更新对应商品在库信息的数量
							
							$CLSL = $CLSL-$SL;                          //实际拆零数量
							/**
							 * 计算拆零后的商品数量
							 * @param  string  $_POST ['SPBH']:      商品编号
							 * @param  string  $_POST ['BZHDWBH']：      商品原包装单位编号
							 * @param  string  $_POST ['CLHDWBH']：      拆零后单位编号
							 * @param  string  $CLSL - $SL：                         拆零的数量
							 * @return int
							 */	
							$CLHSL = $model->getclhsl($_POST ['SPBH'],$_POST ['BZHDWBH'],$_POST ['CLHDWBH'], $CLSL );
							
						}else{ 
							$SL = 0;
							
							$model->updateZksp($SL, $RKDH, "1");      //更新对应商品在库信息的数量，并更新其最终出库日期
							
							/**
							 * 计算拆零后的商品数量
							 * @param  string  $_POST ['SPBH']:      商品编号
							 * @param  string  $_POST ['BZHDWBH']：     商品原包装单位编号
							 * @param  string  $_POST ['CLHDWBH']：     拆零后单位编号
							 * @param  string  $CLSL：                                        拆零的数量
							 * @return int
							 */	
							$CLHSL = $model->getclhsl($_POST ['SPBH'],$_POST ['BZHDWBH'],$_POST ['CLHDWBH'],$CLSL);
						}
						
						
						/**
						 * 拆零后的商品在库信息查询-判断库位是否已有同类商品
						 * @param  string  $RKDH：     当前信息入库单号
						 * @return bool
						 */
						if($model -> SelectZkspxx($RKDH) == FALSE) {
								/**
								 * 新增在库商品信息
								 * @param  int     $SL:     拆零后数量
								 * @param  string  $RKDH：     当前信息入库单号
								 * @return bool
								 */
								$model -> insertZkspxx($CLHSL, $RKDH);
						} else {
								/**
								 * 更新在库商品信息(增加在库商品数量)
								 * @param  int     $SL:     拆零后所增加的数量
								 * @param  string  $RKDH：     当前信息入库单号
								 * @return bool
								 */
								$model -> updateZkspxx($CLHSL, $RKDH);
						}
			
						
						/**
						 * 保存商品移动履历信息
						 * @param  int     $SL:     拆零数量
						 * @param  string  $ZHYZHL: 转移种类   41:拆零入库    42:拆零出库
						 * @param  string  $RKDH：     当前信息入库单号
						 * @param  string  $SPCLDBH:商品拆零单据号
						 * @param  int     $XUHAO:  序号
						 * @return bool
						 */
						$model->insertSpydll($CLSL,"42",$RKDH,$SPCLDBH,$XUHAO); //插入商品移动履历   种类：42拆零出库
						$XUHAO = $XUHAO + 1;
						$model->insertSpydll($CLHSL,"41",$RKDH,$SPCLDBH,$XUHAO);//插入商品移动履历   种类：41拆零入库
						$XUHAO = $XUHAO + 1;
						
						$Rowid = $Rowid + 1;
					
					};
					
					
					/**
					 * 保存商品拆零单信息
					 *
					 * @return bool
					 */
					if($model->insertSpcldxx($result['DJBH']) == FALSE){
						$model -> rollBack();
						$result ['status'] = '3';        //商品拆零单保存失败
					}else{
						$result ['status'] = '0';        //商品拆零单保存成功
						//$model -> rollBack();        //事务回滚_测试恢复数据用
						$model -> commit();            //事务提交
						
						Common_Logger::logToDb( "商品拆零   拆零单号：".$result['DJBH']);   //插入日志
					}
					
				}else{
					$result['status'] = '2';  //项目合法性验证错误:选定拆零的商品数量有变动，请重新选择拆装商品
				}
				
			}
			
			if( $result['status'] != '0' ){
				$model -> rollBack();
			}
			
			echo Common_Tool::json_encode( $result );     //返回处理结果
			
		} catch ( Exception $e ) {
			//事务回滚
			$model -> rollBack();
     		throw $e;
		}
	}
	

}
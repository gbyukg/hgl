<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售开票(XSKP)
 * 作成者：周义
 * 作成日：2010/07/05
 * 更新履历：
 *********************************/
class xs_models_xskp extends Common_Model_Base {
	private $_xsdbh = null; //销售单编号
	private $idx_ROWNUM = 0; //行号
	private $idx_SHPBH = 1; //商品编号
	private $idx_SHPMCH = 2; //商品名称
	private $idx_GUIGE = 3; //规格
	private $idx_BZHDWM = 4; //包装单位
	private $idx_PIHAO = 5; //批号
	private $idx_SHCHRQ = 6; //生产日期
	private $idx_BZHQZH = 7; //保质期至
	private $idx_JLGG = 8; //计量规格
	private $idx_BZHSHL = 9; //包装数量
	private $idx_LSSHL = 10; //零散数量
	private $idx_SHULIANG = 11; //数量
	private $idx_DANJIA = 12; //单价
	private $idx_HSHJ = 13; //含税售价
	private $idx_KOULV = 14; //扣率
	private $idx_SHUILV = 15; //税率
	private $idx_HSHJE = 16; //含税金额
	private $idx_JINE = 17; //金额
	private $idx_SHUIE = 18; //税额
	private $idx_LSHJ = 19; //零售价
	private $idx_ZGSHJ = 20; //最高售价
	private $idx_SHPTM = 21; //商品条码
	private $idx_FLBM = 22; //分类编码
	private $idx_PZHWH = 23; //批准文号
	private $idx_JIXINGM = 24; //剂型
	private $idx_SHCHCHJ = 25; //生产厂家
	private $idx_CHANDI = 26; //产地
	private $idx_SHFOTC = 27; //是否otc
	private $idx_CHAE = 28; //差额
	private $idx_BZHDWBH = 29; //包装单位编号
	
	private $chkdbh;        //出库单编号
	private $chkd_xuhao=0; //出库单明细序号

	/*
	 * 取得发货区信息
	 */
	public function getFHQInfo() {
		$sql = "SELECT FHQBH,FHQMCH FROM H01DB012422 WHERE QYBH = :QYBH AND FHQZHT = '1'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$result = $this->_db->fetchPairs ( $sql, $bind );
		return $result;
	}
	
	/*
	 * 生成结算单
	 */
	public function createJsd($xshdbh,$data){
		$jsd["QYBH"] = $_SESSION ['auth']->qybh;
		$jsd["XSHDBH"] = $xshdbh; //销售单编号
		$jsd["JINE"] = str_replace(",","",$data['SUM_JINE']); //金额
		$jsd["HSHJE"] = str_replace(",","",$data['SUM_HSHJE']);//含税金额
		$jsd["YSHJE"] = $jsd["HSHJE"];//应收金额
		$jsd["SHQJE"] = "0"; //收取金额
		$jsd["JSRQ"] = new Zend_Db_Expr ("TO_DATE('1900-01-01','YYYY-MM-DD')"); //结算日期
		$jsd["JIESUANREN"] = ""; //结算人
		$jsd["JSZHT"] = "0"; //结算状态 未结
		//结算单
		$this->_db->insert("H01DB012208",$jsd);		
	}
	
	
	/*
	 * 客户资质验证（证照，资信，数量）
	 * $data:画面提交数据
	 * $xshdbh:销售单编号
	 */
	public function checkQualification($xshdbh,$data){
		$zige["status"] = "0";//验证返回值
		$xuhao = 0;
		
		//客户证照资质验证
		$zhzhCheck = $this->checkZhZh($data["DWBH"]);
		if ($zhzhCheck["status"]!="0"){
			//许可证过期
			if($zhzhCheck["data"]["XKZHYXQOK"]=="0"){
				$zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]= "许可证已过期。有效期：" .$zhzhCheck["data"]["XKZHYXQ"];
			}
		    //营业执照过期
		    if($zhzhCheck["data"]["YYZHZHYXQOK"]=="0"){
			    $zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]= "营业执照已过期。有效期：" .$zhzhCheck["YYZHZHYXQ"];
		    }
		}
		
	    //客户帐期（信贷期）验证
		if($data["FKFSH"]!="0"){
			$xdqCheck =$this->checkXdq($data);
		    if($xdqCheck["status"]!="0"){
			    $zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]="销售信贷期已超期。最长信贷期:".$xdqCheck["xdq"]."，超期天数：".$xdqCheck["xdqover"];
		    }
		}
		
		//客户信贷额验证(包括本次订单销售额)
		if($data["FKFSH"]=="1"){
			$xdeCheck =$this->checkXde($xshdbh,$data);
		    if($xdeCheck["status"]!="0"){
			    $zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]="销售信贷额已超过限额。最大信贷额:".$xdeCheck["xde"]."，累计已用信贷额：".$xdeCheck["xde_used"];
		    }
		}
		
		//判断商品出货量是否有异常
		$shuliangCheck = $this->checkShuliang($xshdbh);
		if($shuliangCheck["status"]!="0"){
			$zige["status"] = "1"; //需要审批
			foreach ($shuliangCheck["data"] as $errdata){
				$zige["data"][$xuhao++] = "超过出库限制数量。商品:".$errdata["SHPBH"].$errdata["SHPMCH"].",出库限制数量:".$errdata["CHKXZHSHL"].",开单数量：".$errdata["SHULIANG"];
			}
		}
		
		//有需要审批的项目
		if($zige["status"]== "1"){
			foreach ($zige["data"] as $xuhao=>$value){
				$shp["QYBH"]= $_SESSION ['auth']->qybh;
			    $shp["XSHDBH"]= $xshdbh;
			    $shp["XUHAO"]= $xuhao;
			    $shp["SHPYY"]= $value;
			    $shp["SHPZHT"]="0"; //审批状态 =未审批
			    $this->_db->insert("H01DB012203",$shp);
			}
			
			//修改销售订单中的审核状态为待审核
			$sql = "UPDATE H01DB012201 SET SHHZHT = '3' WHERE QYBH = :QYBH AND XSHDBH = :XSHDBH";
			$this->_db->query($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"XSHDBH"=>$xshdbh));
		}
				
		return $zige;
    }
	
	/*
	 * 生成出库单信息
	 */
	public function createCkd($xshdbh){
		$this->chkdbh = Common_Tool::getDanhao('CKD'); //出库单编号
		$ckd["QYBH"] = $_SESSION ['auth']->qybh;
		$ckd["CHKDBH"] = $this->chkdbh;
		$ckd["CKDBH"] = $xshdbh; //参考单编号
		$ckd["CHKLX"] = '1'; //销售出库 
		$ckd["CHKDZHT"] = '1'; //出库单状态（未出库确认）
        $ckd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$ckd ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$ckd['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$ckd['BGZH'] = $_SESSION ['auth']->userId; //变更者	
		$this->_db->insert ( 'H01DB012408', $ckd );
	}
		
	

	/*
	 * 在库商品出库处理
	 */
	public function doChuku($xshdbh,$data) {
		$result ['status'] = '0';
		
		//出库单信息生成
		$this->createCkd($xshdbh);
		
		//出货口发货暂存区分配
		$this->assignFhzcq($xshdbh,$data["DWBH"],$data["FAHUOQU"]);
		
		//循环所有明细行进行实际在库库存数量检验
		foreach ( $data ["#grid_mingxi"] as $row ) {
			if ($row [$this->idx_SHPBH] == '')continue;
			
			$shpbh = $row[$this->idx_SHPBH]; //商品编号
			$pihao = $row[$this->idx_PIHAO]; //批号
			$shchrq = $row[$this->idx_SHCHRQ];//生产日期
			$recs_bzh = $this->getKucun(0,$shpbh,$pihao,$shchrq); //包装库存明细
			$recs_ls = $this->getKucun(1,$shpbh,$pihao,$shchrq);//零散库存明细
			
		    $bzhshl = 0; //累计在库包装数量
		    $lsshl = 0; //累计在库零散数量
		    
			//计算库存数量
		    foreach ( $recs_bzh as $rec ) {
				$bzhshl += ( int ) $rec ['SHULIANG'];//累计在库包装数量
			}
		    foreach ( $recs_ls as $rec ) {
				$lsshl += ( int ) $rec ['SHULIANG'];//累计在库零散数量
			}
			
			//检校最新库存数量是否满足本次销售
			if(($bzhshl + $lsshl) < ( int ) $row [$this->idx_SHULIANG]){
				$result ['status'] = '1'; //库存不足
				$result ['data']['rIdx'] = ( int ) $row [$this->idx_ROWNUM]; //定位明细行index
			}
			
			//总库存可以满足本次销售（包含直接可以出库或者通过补货处理可以满足出库两种情况）
			if($result ['status']=="0"){
				//包装库位出库处理
				$shuliang = $row [$this->idx_BZHSHL]* ( int ) $row [$this->idx_JLGG];
			    $this->updateKucun("2",$shuliang,$recs_bzh, $xshdbh); //出库
			    
			    //如果零散数量不足，则先从包装库位向零散库位补货
			    if($lsshl < ( int ) $row [$this->idx_LSSHL]){
			    	//补货处理
			    	$bhshl = 1 * (int)$row[$this->idx_JLGG]; //补货数量 1件
			    	$bhdbh = Common_Tool::getDanhao("BHD");//补货单编号
                    $recs_bzh = $this->getKucun(0,$shpbh,$pihao,$shchrq);//最新包装库存
                    //补货库存更新处理
                    $this->updateKucun("6",$bhshl,$recs_bzh, $xshdbh,$bhdbh); //补货
			    	//补货完毕之后重新取得零散库存数据
			    	$recs_ls = $this->getKucun(1,$shpbh,$pihao,$shchrq);//零散库存明细
			    }
			    
			    //零散库位出库处理
			    $shuliang = $row [$this->idx_LSSHL];
			    $this->updateKucun("2",$shuliang, $recs_ls, $xshdbh );//出库
			}
		}
					
		return $result;
	}
	
	/*
	 * 发货暂存区分配
	 */
	function assignFhzcq($xshdbh,$dwbh,$fhqbh){
		$sql = "SELECT A.FHZCQBH,A.FHZCQMCH,B.FHQBH,B.CHHKBH FROM H01DB012446 A 
                LEFT JOIN H01DB012445 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.CHHKBH = B.CHHKBH 
                LEFT JOIN H01DB012422 C ON A.QYBH = C.QYBH AND B.FHQBH = C.FHQBH
                LEFT JOIN H01DB012106 D ON A.QYBH = D.QYBH AND D.KHJL = A.FHZCQLB 
                WHERE A.QYBH = :QYBH AND C.FHQBH = :FHQBH AND D.DWBH = :DWBH";
		
		//绑定查询变量
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['FHQBH'] = $fhqbh;
		$bind ['DWBH'] = $dwbh;
		//执行查询
		$rec = $this->_db->fetchRow($sql, $bind);
		
		$zancun ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
		$zancun ["XSHDBH"] = $xshdbh; //销售单编号
		$zancun ["FHQBH"] = $fhqbh; //发货区编号
		$zancun ["FHZCQBH"] = $rec==FALSE? "00000":$rec['FHZCQBH']; //发货暂存区编号
		$zancun ["ZHUANGTAI"] = "1"; //状态
		$this->_db->insert ( 'H01DB012214', $zancun );
		 
	}
	
	/*
	 * 取得最新库存明细数据
	 */
	function getKucun($flg,$shpbh,$pihao,$shchrq){
			if($flg==0){
				//取得在库包装库存数据
				$sql = "SELECT QYBH,CKBH,KQBH,KWBH,SHPBH,PIHAO,RKDBH,ZKZHT,BZHDWBH,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM') AS BZHQZH,SHULIANG".
			       " FROM H01UV012005 ".
			       " WHERE QYBH = :QYBH ".
			       "  AND SHPBH = :SHPBH ".
			       "  AND PIHAO = :PIHAO ".
			       "  AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') ".
			       "  AND SHULIANG > 0 ".
			       "  AND SHFSHKW = '0'". //包装
			       " ORDER BY ZKZHT DESC,RKDBH ASC,SHULIANG ".//在库状态 >入库单>数量
			       " FOR UPDATE OF SHULIANG WAIT 10";
			}elseif($flg==1){
				//取得在库零散库存数据
			    $sql = "SELECT QYBH,CKBH,KQBH,KWBH,SHPBH,PIHAO,RKDBH,ZKZHT,BZHDWBH,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM') AS BZHQZH,SHULIANG".
			       " FROM H01UV012005 ".
			       " WHERE QYBH = :QYBH ".
			       "  AND SHPBH = :SHPBH ".
			       "  AND PIHAO = :PIHAO ".
			       "  AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') ".
			       "  AND SHULIANG > 0 ".
			       "  AND SHFSHKW = '1'". //零散
			       " ORDER BY ZKZHT DESC,RKDBH ASC,SHFGDJ DESC ".  //在库状态 >入库单>周转架
			       " FOR UPDATE OF SHULIANG WAIT 10";
			}
			
			//绑定查询变量
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			$bind ['PIHAO'] = $pihao;
			$bind ['SHCHRQ'] = $shchrq;
			//执行查询
			$recs = $this->_db->fetchAll ( $sql, $bind ); 
			return $recs;		
	}

	/*
	 * 更新在库和移动履历信息
	 */
	public function updateKucun($flg="2",$shuliang,$kucuns,$xshdbh="",$bhdbh="") {
		if ($shuliang ==0) return;
		$idx = 0; //移动履历序号
		$bhdxuhao = 0;//补货单序号
	    foreach ( $kucuns as $kucun ) {
			$shuliang_update = 0; //在库更新数量
	
			//该条在库信息部分出库时 
			if ($shuliang <= ( int ) $kucun ['SHULIANG']) {
				$shuliang_update = ( int ) $kucun ['SHULIANG'] - $shuliang;
				$shuliang_lvli = $shuliang;  //移动履历
				$shuliang = 0;
			} else { //全部出库
				$shuliang_update = 0;
				$shuliang_lvli = ( int ) $kucun ['SHULIANG'];  //移动履历
				$shuliang = $shuliang - ( int ) $kucun ['SHULIANG'];
			}
			
			//更新在库信息H01DB012404
			$sql_zaiku = "UPDATE H01DB012404 ".
			             "SET SHULIANG = :SHULIANG " .
			             (($shuliang_update == 0) ? ",ZZHCHKRQ = SYSDATE " : "").
			             " WHERE QYBH = :QYBH ".
			             " AND CKBH = :CKBH " .
			             " AND KQBH = :KQBH ".
			             " AND KWBH = :KWBH ".
			             " AND SHPBH = :SHPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND RKDBH = :RKDBH " .
			             " AND ZKZHT = :ZKZHT " .
			             " AND BZHDWBH = :BZHDWBH ".
			             " AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')";
			unset($bind);                
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['CKBH'] = $kucun ['CKBH']; //仓库
			$bind ['KQBH'] = $kucun ['KQBH']; //库区
			$bind ['KWBH'] = $kucun ['KWBH']; //库位
			$bind ['SHPBH'] = $kucun ['SHPBH']; //商品编号
			$bind ['PIHAO'] = $kucun ['PIHAO']; //批号
			$bind ['BZHDWBH'] = $kucun ['BZHDWBH']; //包装单位
			$bind ['SHCHRQ'] = $kucun ['SHCHRQ']; //生产日期
			$bind ['RKDBH'] = $kucun ['RKDBH']; //入库单编号
			$bind ['ZKZHT'] = $kucun ['ZKZHT'];//在库状态
			$bind ['SHULIANG'] = $shuliang_update;               
			$this->_db->query ( $sql_zaiku,$bind );
			
			/*生成在库移动履历开始*/
			unset($lvli); 
			$lvli ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
			$lvli ["CKBH"] = $kucun ['CKBH']; //仓库编号
			$lvli ["KQBH"] = $kucun ['KQBH']; //库区编号
			$lvli ["KWBH"] = $kucun ['KWBH']; //库位编号
			$lvli ["SHPBH"] = $kucun ['SHPBH']; //商品编号
			$lvli ["PIHAO"] = $kucun ['PIHAO']; //批号
			$lvli ["RKDBH"] = $kucun ['RKDBH']; //入库单号
		    //移动单号
			switch ($flg){
				case "2": //出库
					$lvli ["YDDH"] = $xshdbh; //移动单号(销售单编号)
					break;
				case "6": //补货出库
					$lvli ["YDDH"] = $bhdbh; //移动单号(补货单编号)
					break;
					
			}
			
			$lvli ["XUHAO"] = $idx ++; //序号
			$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
			$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')"); //保质期至
			$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');//处理时间
			//移动数量
			switch ($flg){
				case "2": //出库
				case "6": //补货出库
					$lvli ["SHULIANG"] = $shuliang_lvli * - 1; //移动数量
				break;		
			}
			$lvli ["BZHDWBH"] = $kucun ['BZHDWBH']; //包装单位编号
			//移动种类
		    switch ($flg){
				case "2": //出库
					$lvli ["ZHYZHL"] = "21"; //转移种类  出库
					break;	
				case "6": //补货出库
					$lvli ["ZHYZHL"] = "61"; //转移种类  补货出库
					break;		
			}
			
			$lvli["BEIZHU"] = ''; //备注
			$lvli["ZKZHT"] = $kucun ['ZKZHT'];//在库状态
			$lvli["ZCHRQ"] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$lvli["ZCHZH"] = $_SESSION ['auth']->userId; //作成者
			$lvli["BGRQ"] = new Zend_Db_Expr('SYSDATE');//变更日期
			$lvli["BGZH"] = $_SESSION ['auth']->userId; //变更者
			$this->_db->insert ( 'H01DB012405', $lvli );
			/*在库移动履历生成结束*/
			
			//补货
			if($flg=="6"){
				//取得补货目的地库位
				$toolModel = new gt_models_tool();
				$kwinfo = $toolModel->autoAssignKuwei($kucun["SHPBH"],$kucun["PIHAO"],0,$shuliang_lvli);
				//判断库存表中是否存在已有信息
				$sql = "SELECT * FROM H01DB012404 WHERE QYBH=:QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH 
				        AND SHPBH = :SHPBH AND PIHAO = :PIHAO AND RKDBH = :RKDBH AND ZKZHT = :ZKZHT AND BZHDWBH = :BZHDWBH
				        AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')";
				unset($bind);    
				$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
				$bind ['CKBH'] = $kwinfo[0] ['CKBH']; //仓库
				$bind ['KQBH'] = $kwinfo[0] ['KQBH']; //库区
				$bind ['KWBH'] = $kwinfo[0] ['KWBH']; //库位
				$bind ['SHPBH'] = $kucun ['SHPBH']; //商品编号
				$bind ['PIHAO'] = $kucun ['PIHAO']; //批号
				$bind ['BZHDWBH'] = $kucun ['BZHDWBH']; //包装单位
				$bind ['SHCHRQ'] = $kucun ['SHCHRQ']; //生产日期
				$bind ['RKDBH'] = $kucun ['RKDBH']; //入库单编号
				$bind ['ZKZHT'] = $kucun ['ZKZHT'];//在库状态
				$kucunRec = $this->_db->fetchRow($sql,$bind);
				
				//如果无既存信息， 则插入一条新的在库信息
	        	if($kucunRec==FALSE){
		        	//生成在库信息H01DB012404
		        	unset($zaiku); 
					$zaiku ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
					$zaiku ["CKBH"] = $kwinfo[0]['CKBH']; //仓库编号
					$zaiku ["KQBH"] = $kwinfo[0]['KQBH']; //库区编号
					$zaiku ["KWBH"] = $kwinfo[0]['KWBH']; //库位编号
					$zaiku ["SHPBH"] = $kucun ['SHPBH']; //商品编号
					$zaiku ["PIHAO"] = $kucun ['PIHAO']; //批号
					$zaiku ["RKDBH"] = $kucun ['RKDBH']; //入库单号
                    $zaiku ["ZKZHT"] = $kucun ['ZKZHT'];//在库状态
                    $zaiku ["BZHDWBH"] = $kucun ['BZHDWBH']; //包装单位编号
                    $zaiku ["ZZHCHKRQ"] = new Zend_Db_Expr("TO_DATE('9999-12-31 23:59:59','YYYY-MM-DD HH24:MI:SS')"); //最终出库日期
                    $zaiku ["SHULIANG"] = $kwinfo[0]["SHULIANG"]; //数量
					$zaiku ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
					$zaiku ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')"); //保质期至
					$this->_db->insert ( 'H01DB012404', $zaiku );
	        	}else{
	        		//在库信息中存在既存的信息，则对库存数量进行更新处理
	        		$sql_zaiku = "UPDATE H01DB012404 ".
					             "SET SHULIANG = SHULIANG + :SHULIANG ," .
					             " ZZHCHKRQ = TO_DATE('9999-12-31','YYYY-MM-DD')".
					             " WHERE QYBH = :QYBH ".
					             " AND CKBH = :CKBH " .
					             " AND KQBH = :KQBH ".
					             " AND KWBH = :KWBH ".
					             " AND SHPBH = :SHPBH " .
					             " AND PIHAO = :PIHAO " .
					             " AND RKDBH = :RKDBH " .
					             " AND ZKZHT = :ZKZHT " .
					             " AND BZHDWBH = :BZHDWBH ".
					             " AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')"; 
					unset($bind);    
					$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
					$bind ['CKBH'] = $kwinfo ['CKBH']; //仓库
					$bind ['KQBH'] = $kwinfo ['KQBH']; //库区
					$bind ['KWBH'] = $kwinfo ['KWBH']; //库位
					$bind ['SHPBH'] = $kucun ['SHPBH']; //商品编号
					$bind ['PIHAO'] = $kucun ['PIHAO']; //批号
					$bind ['BZHDWBH'] = $kucun ['BZHDWBH']; //包装单位
					$bind ['SHCHRQ'] = $kucun ['SHCHRQ']; //生产日期
					$bind ['RKDBH'] = $kucun ['RKDBH']; //入库单编号
					$bind ['ZKZHT'] = $kucun ['ZKZHT'];//在库状态
					$bind ['SHULIANG'] = $kwinfo[0]["SHULIANG"];   //数量    
					$this->_db->query ( $sql_zaiku,$bind );
	        	}
	        	
	        	//补货入库的移动履历生成
	        	unset($lvli); 
		       	$lvli ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
				$lvli ["CKBH"] = $kwinfo[0] ['CKBH']; //仓库编号
				$lvli ["KQBH"] = $kwinfo[0] ['KQBH']; //库区编号
				$lvli ["KWBH"] = $kwinfo[0] ['KWBH']; //库位编号
				$lvli ["SHPBH"] = $kucun ['SHPBH']; //商品编号
				$lvli ["PIHAO"] = $kucun ['PIHAO']; //批号
				$lvli ["RKDBH"] = $kucun ['RKDBH']; //入库单号
				$lvli ["YDDH"] = $bhdbh; //移动单号(补货单编号)
				$lvli ["XUHAO"] = $idx ++; //序号
				$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
				$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')"); //保质期至
				$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');//处理时间
				$lvli ["SHULIANG"] = $kwinfo[0]["SHULIANG"]; //移动数量
				$lvli ["BZHDWBH"] = $kucun ['BZHDWBH']; //包装单位编号
				$lvli ["ZHYZHL"] = "62"; //转移种类  补货入库				
				$lvli["BEIZHU"] = ''; //备注
				$lvli["ZKZHT"] = $kucun ['ZKZHT'];//在库状态
				$lvli["ZCHRQ"] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				$lvli["ZCHZH"] = $_SESSION ['auth']->userId; //作成者
				$lvli["BGRQ"] = new Zend_Db_Expr('SYSDATE');//变更日期
				$lvli["BGZH"] = $_SESSION ['auth']->userId; //变更者
				$this->_db->insert ( 'H01DB012405', $lvli );
			}
			
			/*出库单 补货单生成*/
			switch ($flg){
				case "2": //出库
					/*出库单生成*/
					$chukdmx["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
					$chukdmx["CHKDBH"] = $this->chkdbh; //
					$chukdmx["XUHAO"] =  $this->chkd_xuhao++; //
					$chukdmx["SHPBH"] = $kucun ['SHPBH'];
					$chukdmx["RKDBH"] = $kucun ['RKDBH'];
					$chukdmx["CKBH"] = $kucun ['CKBH'];
					$chukdmx["KQBH"] = $kucun ['KQBH'];
					$chukdmx["KWBH"] = $kucun ['KWBH'];
					$chukdmx["PIHAO"] = $kucun ['PIHAO'];
					$chukdmx["SHCHRQ"] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
					$chukdmx["BZHQZH"] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')"); //保质期至
					//$chukdmx["BZHSHL"] = $kucun ['SHPBH'];
					//$chukdmx["LSSHL"] = $kucun ['SHPBH'];
					$chukdmx["SHULIANG"] = $shuliang_lvli; //出库数量
					$chukdmx["CHHQRZHT"] = '1'; //出货确认状态
					$chukdmx ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				    $chukdmx ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			    	$chukdmx['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
			    	$chukdmx['BGZH'] = $_SESSION ['auth']->userId; //变更者
	            	$this->_db->insert ( 'H01DB012409', $chukdmx );
	            	break;	
				case "6"://补货出库
					/*补货单生成*/
					$bhd["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
					$bhd["BHDBH"] = $bhdbh; //补货单编号
					$bhd["XUHAO"] = $bhdxuhao++;//序号
					$bhd["SHPBH"] = $kucun ['SHPBH'];//商品编号
					$bhd["PIHAO"] = $kucun ['PIHAO'];//批号
					$bhd["RKDBH"] = $kucun ['RKDBH'];//入库单编号
					$bhd["YCHCK"] = $kucun ['CKBH'];//移出仓库编号
					$bhd["YCHKQ"] = $kucun ['KQBH'];//移出库区编号
					$bhd["YCHKW"] = $kucun ['KWBH'];//移出库位编号
					$bhd["YRCK"] = $kwinfo[0] ['CKBH'];//移入仓库编号
					$bhd["YRKQ"] = $kwinfo[0] ['KQBH'];//移入库区编号
					$bhd["YRKW"] = $kwinfo[0] ['KWBH'];//移入库位编号
					$bhd["BHLX"] = "2"; //补货类型：随单自动补货
					$bhd["XSHDBH"] = $xshdbh; //销售单编号
					$bhd["BHSHL"] = $kwinfo[0]["SHULIANG"]; //补货数量
					$bhd["ZHUANGTAI"] = "1"; //补货状态：未完成
					$bhd["ZCHRQ"] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				    $bhd["ZCHZH"] = $_SESSION ['auth']->userId; //作成者
			    	$bhd["BGRQ"] = new Zend_Db_Expr('SYSDATE');//变更日期
			    	$bhd["BGZH"] = $_SESSION ['auth']->userId; //变更者
	            	$this->_db->insert ( 'H01DB012450', $bhd );
	            	break;	
				}

			//剩余数量为零则出库完毕，不再继续循环
			if ($shuliang <= 0) break;
		}
	}
	
	/*
	 * 销售订单保存
	 * $xshdbh:销售单编号 $xshddata:销售单数据 
	 */
	public function createXshd($xshdbh,$xshddata) {
		$result["status"] = "0";
		
		$xshd ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$xshd ['XSHDBH'] = $xshdbh; //销售单编号
		$xshd ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $xshddata ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$xshd ['BMBH'] = $_SESSION ['auth']->bmbh; //部门编号
		$xshd ['KPYBH'] = $_SESSION ['auth']->userId; //开票员编号
		$xshd ['YWYBH'] = $xshddata ['YWYBH']; //业务员编号
		$xshd ['DWBH'] = $xshddata ['DWBH']; //单位编号
		$xshd ['DIZHI'] = $xshddata ['DIZHI']; //地址
		$xshd ['DHHM'] = $xshddata ['DHHM']; //电话
		$xshd ['SHFZZHSH'] = isset($xshddata ['SHFZZHSH'])? $xshddata ['SHFZZHSH'] : '0'; //是否增值税
		$xshd ['KOULV'] = $xshddata ['KOULV']; //扣率
		$xshd ['XSHDZHT'] = '0'; //销售单状态(未出库)
		$xshd ['FHQBH'] = $xshddata ['FAHUOQU']; //发货区
		$xshd ['BEIZHU'] = $xshddata ['BEIZHU']; //备注
		$xshd['SHHZHT'] = "0";//审核状态   
        //$xshd['SHHR'] = '';//审核人
		//$xshd['SHHYJ'] = '';//审核意见
		//$xshd['SHHRQ'] = new Zend_Db_Expr("SYSDATE");//审核日期
		$xshd ['FKFSH'] = $xshddata ['FKFSH']; //付款方式
		$xshd ['SHFPS'] = isset($xshddata ['SHFPS'])? $xshddata ['SHFPS'] : '0'; //是否配送
		$xshd ['FPZHT'] = '0'; //发票状态  未开
		$xshd ['JINE'] = str_replace(",","",$xshddata ['SUM_JINE']); //金额
		$xshd ['SHUIE'] = str_replace(",","",$xshddata ['SUM_SHUIE']); //税额
		$xshd ['HSHJE'] = str_replace(",","",$xshddata ['SUM_HSHJE']);//含税金额
		$xshd ['SHULIANG'] = str_replace(",","",$xshddata ['SUM_SHULIANG']); //数量
		$xshd ['PSYXJ'] = '0'; //配送优先级
		$xshd ['QXBZH'] = '1';//取消标志
		$xshd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$xshd ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$xshd ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$xshd ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		//销售订单信息表
		$this->_db->insert ( "H01DB012201", $xshd );
		
		$idx = 1; //明细序号自增
        //循环所有明细行，保存销售订单明细
		foreach ( $xshddata ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == "")continue;//忽略空白行

			$xshdmx ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$xshdmx ['XSHDBH'] = $xshdbh; //销售单编号
			$xshdmx ['XUHAO'] = $idx ++; //序号
			$xshdmx ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			//$xshdmx ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
			//$xshdmx ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
			//$xshdmx ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
			$xshdmx ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			$xshdmx ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$xshdmx ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" ); //保质期至
			$xshdmx ['BZHSHL'] = $grid [$this->idx_BZHSHL]; //包装数量
			$xshdmx ['LSSHL'] =  $grid [$this->idx_LSSHL]; //零散数量
			$xshdmx ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$xshdmx ['DANJIA'] = $grid [$this->idx_DANJIA]; //单价
			$xshdmx ['HSHJ'] = $grid [$this->idx_HSHJ]; //含税价
			$xshdmx ['KOULV'] = $grid [$this->idx_KOULV]; //扣率
			$xshdmx ['JINE'] = $grid [$this->idx_JINE]; //金额
			$xshdmx ['HSHJE'] = $grid [$this->idx_HSHJE]; //含税金额
			$xshdmx ['SHUIE'] = $grid [$this->idx_SHUIE]; //税额
			//$xshdmx ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注
			$xshdmx ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$xshdmx ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$xshdmx ['ZCHRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //作成日期
			$xshdmx ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//销售订单明细表
			$this->_db->insert ( "H01DB012202", $xshdmx );	
		}
		
		return $result;
	}

	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck($data) {
		if ($data ["KPRQ"] == "" || //开票日期
            $data ["DWBH"] == "" || //单位编号
            $data ["YWYBH"] == "" || //业务员编号   
            $data ["FAHUOQU"] == "0" || //发货区
            $data ["FKFSH"] == "0" || //付款方式
            $data ["#grid_mingxi"] == "") { //明细表格
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $data ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_PIHAO] == "" || //批号
                    $grid [$this->idx_SHULIANG] == "" || //数量
                    $grid [$this->idx_SHULIANG] == "0" ||
                    $grid [$this->idx_JINE] == "0" ) //金额
                   {
					return false;
				}
			}
		}
		
		//一条明细也没有输入
		if (! $isHasMingxi) {
			return false;
		}
		
		return true;
	}
	
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck($data) {
		//单位合法性
		$dwModel = new gt_models_danwei();
		$dwfilter ['dwbh'] = $data ['DWBH'];
		$dwfilter ['flg'] = "0";
		if ($dwModel->getDanweiInfo ( $dwfilter ) == FALSE) {
			return false;
		}
		
		//商品合法性
		$shpModel = new gt_models_shangpin();
		foreach ( $data ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == "")	continue;
			$shpfilter ['dwbh'] = $data ['DWBH'];
			$shpfilter ['shpbh'] = $grid [$this->idx_SHPBH];
			if ($shpModel->getShangpinInfo ( $shpfilter ) == FALSE) {
				return false;
			}
		}
		
		return true;
	}
	
	/*
	 * 判断证照是否过期
	 */
	public function checkZhZh($dwbh){
		$zige["status"] = "0";
		//许可证有效期，营业执照有效期
		$sql = " SELECT TO_CHAR(XKZHYXQ,'YYYY-MM-DD'),(CASE WHEN XKZHYXQ < SYSDATE THEN 0 WHEN XKZHYXQ IS NULL THEN 0  ELSE 1 END) AS XKZHYXQOK,".
               " TO_CHAR(YYZHZHYXQ,'YYYY-MM-DD'),(CASE WHEN YYZHZHYXQ < SYSDATE THEN 0 WHEN YYZHZHYXQ IS NULL THEN 0  ELSE 1 END) AS YYZHZHYXQOK".
               " FROM H01DB012106  WHERE QYBH =:QYBH AND DWBH = :DWBH";
		//绑定查询变量
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $dwbh;
		$rec = $this->_db->fetchRow($sql,$bind);
		
		//许可证过期 营业执照过期
		if($rec["XKZHYXQOK"]=="0" || $rec["YYZHZHYXQOK"]=="0"){
			$zige["status"] = "1"; //需要审批
			$zige["data"]=$rec;
		}
		
		return $zige;
	}

	
	/*
	 * 判断信贷期是否超期
	 */
	public function checkXdq($data) {
		$xdqCheck["status"] = "0";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $data["DWBH"];
		
		//销售信贷期取得
		$sql = "SELECT DECODE(XSHXDQ,NULL,0,XSHXDQ) FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH";
		$xshxdq = $this->_db->fetchOne ( $sql, $bind );

		//账期销售单中尚未结账销售单的最长天数
		$sql = " SELECT DECODE(FLOOR(SYSDATE - min(A.KPRQ)),NULL,0,FLOOR(SYSDATE - min(A.KPRQ)))FROM H01DB012201 A ".
		       " JOIN H01DB012208 B ON A.QYBH = B.QYBH AND A.XSHDBH = B.XSHDBH " .
		       " WHERE A.QYBH = :QYBH AND A.DWBH = :DWBH" . 
		       " AND A.QXBZH ='1' AND A.FKFSH = '1' AND B.JSZHT <> '1' ";
   	    $days = $this->_db->fetchOne ( $sql, $bind );
   				
		//帐期已经超期
		if ($days > $xshxdq) {
			$xdqCheck["status"] = "1";
			$xdqCheck["xdq"] = $days; //信贷期天数
			$xdqCheck["xdqover"] = $days - $xshxdq; //超期天数
		}
		
		return $xdqCheck;
	}
	
    /*
	 * 判断信贷额是否超过额度
	 */
	public function checkXde($xshdbh,$data) {
		$xdeCheck["status"] = "0";
			
		//销售信贷额取得
		$sql = "SELECT DECODE(XSHXDE,NULL,0,XSHXDE) FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $data["DWBH"];
		
		$xde = $this->_db->fetchOne ( $sql, $bind );

		//账期销售单中尚未结账的合计金额
		$sql = " SELECT DECODE(SUM(B.YSHJE),NULL,0,SUM(B.YSHJE)) FROM H01DB012201 A ".
               " JOIN H01DB012208 B ON A.QYBH = B.QYBH AND A.XSHDBH = B.XSHDBH ".
		       " WHERE A.QYBH = :QYBH AND A.DWBH = :DWBH" . 
		       " AND A.QXBZH ='1' AND A.FKFSH = '1' AND B.JSZHT <> '1' ";
		//本次付款为非帐期，则排除本次金额
		if($data["FKFSH"]!="1"){
		    $sql .= " AND A.XSHDBH <> :XSHDBH";
		    $bind ['XSHDBH'] = $xshdbh;
		
		}
   	    $yshje = $this->_db->fetchOne ( $sql, $bind ); //应收金额
    	   
   	    //判断合计金额是否超过信贷额
   	    if($yshje > (float)$xde){
   	    	$xdeCheck["status"] = "1"; //超过信贷额
   	    	$xdeCheck["xde"] = (float)$xde; //最大信贷额
   	    	$xdeCheck["xde_used"] = $yshje;//已用信贷额
   	    }
   	    
		return $xdeCheck;
	}
	/*
	 * 检验商品出货量是否有异常
	 */
	public function checkShuliang($xshdbh){
		$result["status"] = "0";
		//取出本单所有超过出库限制数量的商品
		$sql = "SELECT A.SHPBH,B.SHPMCH,SUM(A.SHULIANG) AS SHULIANG,B.CHKXZHSHL FROM H01DB012202 A
                JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH
                WHERE A.QYBH = :QYBH AND A.XSHDBH = :XSHDBH
                GROUP BY A.SHPBH,B.SHPMCH,B.CHKXZHSHL
                HAVING SUM(A.SHULIANG) > B.CHKXZHSHL";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $xshdbh;
		
		$recs = $this->_db->fetchAll($sql,$bind);
		if(count($recs)>0){
			$result["status"] = "1";
			$result["data"] = $recs;
		}
		
		return $result;
	}
	/*
	 * 商品价格信息
	 */
	public function getJiageInfo($filter) {
		$result ['type'] = 'none'; //无特价无一品多价
	
		$sql = "SELECT HSHSHJTJ,XSHTJ" . 
		       " FROM H01DB012105 " .
		       " WHERE QYBH = :QYBH " . 
		       " AND SHPBH = :SHPBH " . 
		       " AND DWBH = :DWBH" . 
		       " AND QSHZHXRQ <= SYSDATE" . //起始执行日期
               " AND ZHZHZHXRQ >=SYSDATE"; //终止执行日期

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		$bind ['SHPBH'] = $filter ['shpbh'];
		
		$recs = $this->_db->fetchRow ( $sql, $bind ); //特价信息

		//特价不存在的时候，看一品多价是否存在
		if ($recs == FALSE) {
			//查看一品多价是否存在
			$sql = "SELECT COUNT(*) FROM H01DB012104 A" .
			       " INNER JOIN H01DB012106 B ON A.QYBH = B.QYBH AND A.KHDJ = B.KHDJ" .
			       " WHERE A.QYBH = :QYBH " . 
			       " AND A.SHPBH = :SHPBH " .
			       " AND B.DWBH = :DWBH";
			
			//存在一品多价
			if ($this->_db->fetchOne ( $sql, $bind ) != "0") {
				$result ['type'] = '1'; //一品多价
			}
		} else {
			$result ['type'] = '0'; //特价存在
			$result ['data'] = $recs; //特价信息
		}
		
		return $result;
	}
	/**
	 * 销售挂账单保存
	 *
	 */
	public function saveTempMain($xshgzhdbh,$data){
		$xshgzhddata ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$xshgzhddata ['XSHGZHDBH'] = $xshgzhdbh; //销售单编号
		$xshgzhddata ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $data ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$xshgzhddata ['BMBH'] = $_SESSION ['auth']->bmbh; //部门编号
		$xshgzhddata ['KPYBH'] = $_SESSION ['auth']->userId; //开票员编号
		$xshgzhddata ['YWYBH'] = $data ['YWYBH']; //业务员编号
		$xshgzhddata ['DWBH'] = $data ['DWBH']; //单位编号
		$xshgzhddata ['DIZHI'] = $data ['DIZHI']; //地址
		$xshgzhddata ['DHHM'] = $data ['DHHM']; //电话
		$xshgzhddata ['SHFZZHSH'] = isset($data ['SHFZZHSH'])? $data ['SHFZZHSH'] : '1'; //是否增值税
		$xshgzhddata ['KOULV'] = $data ['KOULV']; //扣率
		$xshgzhddata ['FHQBH'] = $data ['FAHUOQU']; //发货区
		$xshgzhddata ['FKFSH'] = $data ['FKFSH']; //付款方式
		$xshgzhddata ['SHFPS'] = isset($data ['SHFPS'])? $data ['SHFPS'] : '0'; //是否配送
		$xshgzhddata ['BEIZHU'] = $data ['BEIZHU']; //备注
		$xshgzhddata ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$xshgzhddata ['BGZH'] = $_SESSION ['auth']->userId; //变更者	
		$xshgzhddata ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$xshgzhddata ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//销售挂账单信息表
		return $this->_db->insert ( "H01DB012204", $xshgzhddata );
		
	}
	/*
	 * 销售挂账单明细保存
	 */
	public function saveTempMingxi($xshgzhdbh,$data) {
		$idx = 1; //序号自增
        //循环所有明细行，保存销售订单明细
		foreach ( $data ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')continue;
			
			$xshgzhdmxdata ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$xshgzhdmxdata ['XSHGZHDBH'] = $xshgzhdbh; //挂账单编号
			$xshgzhdmxdata ['XUHAO'] = $idx ++; //序号
			$xshgzhdmxdata ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
//			$data ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
//			$data ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
//			$data ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
			$xshgzhdmxdata ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			$xshgzhdmxdata ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$xshgzhdmxdata ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" ); //保质期至
			$xshgzhdmxdata ['BZHSHL'] = $grid [$this->idx_BZHSHL]; //包装数量
			$xshgzhdmxdata ['LSSHL'] = $grid [$this->idx_LSSHL]; //零散数量
			$xshgzhdmxdata ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$xshgzhdmxdata ['DANJIA'] = $grid [$this->idx_DANJIA]; //单价
			$xshgzhdmxdata ['HSHJ'] = $grid [$this->idx_HSHJ]; //含税价
			$xshgzhdmxdata ['KOULV'] = $grid [$this->idx_KOULV]; //扣率
			$xshgzhdmxdata ['JINE'] = $grid [$this->idx_JINE]; //金额
			$xshgzhdmxdata ['HSHJE'] = $grid [$this->idx_HSHJE]; //含税金额
			$xshgzhdmxdata ['SHUIE'] = $grid [$this->idx_SHUIE]; //税额
			$xshgzhdmxdata ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$xshgzhdmxdata ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$xshgzhdmxdata ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$xshgzhdmxdata ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//销售订单明细表
			$this->_db->insert ( "H01DB012205", $xshgzhdmxdata );	
		}
	}
	
}
	
	
	
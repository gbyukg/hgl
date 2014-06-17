<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    指定补货(ZHDBH)
 * 作成者：DLTT_LiuCong
 * 作成日：2011/06/23
 * 更新履历：
 *********************************/
class cc_models_zhdbh extends Common_Model_Base {

	/**
	 * 获取商品信息
	 */
	function getshpxx($shpbh){
		$sql ="SELECT SHPMCH,GUIGE,CHANDI,LSHJ FROM H01DB012101 WHERE SHPBH =:SHPBH AND QYBH = :QYBH";
		$bind = array( 'SHPBH' => $shpbh, 'QYBH' => $_SESSION ['auth']->qybh );
		$Spxx = $this->_db->fetchRow( $sql, $bind );
		return $Spxx;     
	}
	
	
	
	/*
	 * 库位/批号选择画面GRID列表数据取得（xml格式）
	 */
	function getListData($filter) {
		//检索SQL
		//显示排列顺序：商品名称,仓库名称,库区名称,库位名称,批号,批次数量,单位,在库状态,保质期至,生产日期,仓库编号,库区编号,库位编号,单位编号,在库状态值,入库单编号，是否散货库位
		$sql = "SELECT E.SHPMCH, D.CKMCH, C.KQMCH, B.KWMCH, A.PIHAO, A.SHULIANG , "
				."F.NEIRONG AS BZHDWM, DECODE(A.ZKZHT,'0','可销','1','催销','冻结') AS ZKZHTM,"
			    ."TO_CHAR(A.BZHQZH, 'YYYY-MM') AS BZHQZH, TO_CHAR(A.SHCHRQ, 'YYYY-MM-DD') AS SHCHRQ, "
			    ."A.CKBH, A.KQBH, A.KWBH, A.BZHDWBH, A.ZKZHT, A.RKDBH, B.SHFSHKW "
			    ."FROM H01DB012404 A "
			    ."LEFT JOIN H01DB012403 B ON A.KWBH = B.KWBH AND A.KQBH = B.KQBH AND A.CKBH = B.CKBH AND A.QYBH = B.QYBH "
			    ."LEFT JOIN H01DB012402 C ON A.KQBH = C.KQBH AND A.CKBH = C.CKBH AND A.QYBH = C.QYBH "
			    ."LEFT JOIN H01DB012401 D ON A.CKBH = D.CKBH AND A.QYBH = D.QYBH "
			    ."LEFT JOIN H01DB012101 E ON A.SHPBH = E.SHPBH AND A.QYBH = E.QYBH "
			    ."LEFT JOIN H01DB012001 F ON A.BZHDWBH = F.ZIHAOMA AND F.CHLID = 'DW' "
				."WHERE A.ZKZHT <> '2' AND A.QYBH =:QYBH AND A.SHPBH =:SHPBH AND B.SHFSHKW = '0'"
				."AND A.SHULIANG > 0";
		
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHPBH'] = $filter['shpbh'];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $sql,$bind );
		
		return Common_Tool::createXml ( $recs,false, $totalCount, $filter ["posStart"] );
	}
	
	
	/*
	 * 补货处理
	 */
	public function doSave($bhdbh) {
		$result ['status'] = '0';
		
		//取得商品的计量规格
		$sql = "SELECT JLGG FROM H01DB012101 ".
	       " WHERE QYBH = :QYBH ".             //区域编号
	       "  AND SHPBH = :SHPBH ";            //商品编号

		//绑定查询变量
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $_POST['SHPBH'];           //商品编号	

		$bhshl = 1 * (int)$this->_db->fetchOne( $sql, $bind );  //补货数量 1件
		
		
		//取得在库整件库存数据
		$sql = "SELECT QYBH,CKBH,KQBH,KWBH,SHPBH,PIHAO,RKDBH,ZKZHT,BZHDWBH,".
		   "TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM') AS BZHQZH,SHULIANG".
	       " FROM H01UV012005 ".
	       " WHERE QYBH = :QYBH ".
		   "  AND CKBH = :CKBH ".
		   "  AND KQBH = :KQBH ".
		   "  AND KWBH = :KWBH ".
	       "  AND SHPBH = :SHPBH ".
	       "  AND PIHAO = :PIHAO ".
		   "  AND RKDBH = :RKDBH ".
		   "  AND ZKZHT = :ZKZHT ".
		   "  AND BZHDWBH = :BZHDWBH ".
	       "  AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') ".
	       "  AND SHULIANG > 0 ".     //数量大于零
	       "  AND SHFSHKW = '0'".     //不是散货库位
	       " FOR UPDATE OF SHULIANG WAIT 10";

		//绑定查询变量
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $_POST['ZHJCKBH'];
		$bind ['KQBH'] = $_POST['ZHJKQBH'];
		$bind ['KWBH'] = $_POST['ZHJKWBH'];
		$bind ['SHPBH'] = $_POST['SHPBH'];
		$bind ['PIHAO'] = $_POST['PIHAO'];
		$bind ['RKDBH'] = $_POST['RKDBH'];
		$bind ['ZKZHT'] = $_POST['ZKZHT'];
		$bind ['SHCHRQ'] = $_POST['SHCHRQ'];
		$bind ['BZHDWBH'] = $_POST['BZHDWBH'];
		
		//执行查询
		$recs_bzh = $this->_db->fetchRow( $sql, $bind ); 
		
        //补货库存更新处理
        $this->updateKucun($bhshl,$recs_bzh,$bhdbh); //补货
        
		return $result;
	}


	
	/*
	 * 更新在库和移动履历信息
	 */
	public function updateKucun($shuliang,$kucun,$bhdbh="") {
		if ($shuliang ==0) return;
		
		$idx = 1;             //移动履历序号
		$bhdxuhao = 1;        //补货单序号
		$shuliang_update = 0; //在库更新数量
	
			if ($shuliang <= ( int ) $kucun ['SHULIANG']) {
				$shuliang_update = ( int ) $kucun ['SHULIANG'] - $shuliang;
				$shuliang_lvli = $shuliang;  //移动履历
				$shuliang = 0;
			} else {      
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
			$lvli ["YDDH"] = $bhdbh; //移动单号(补货单编号)
			$lvli ["XUHAO"] = $idx ++; //序号
			$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
			$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')"); //保质期至
			$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');//处理时间
			$lvli ["SHULIANG"] = $shuliang_lvli * - 1; //移动数量
			$lvli ["BZHDWBH"] = $kucun ['BZHDWBH']; //包装单位编号
			$lvli ["ZHYZHL"] = "61"; //转移种类  补货出库
			$lvli["BEIZHU"] = ''; //备注
			$lvli["ZKZHT"] = $kucun ['ZKZHT'];//在库状态
			$lvli["ZCHRQ"] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$lvli["ZCHZH"] = $_SESSION ['auth']->userId; //作成者
			$lvli["BGRQ"] = new Zend_Db_Expr('SYSDATE');//变更日期
			$lvli["BGZH"] = $_SESSION ['auth']->userId; //变更者
			
			$this->_db->insert ( 'H01DB012405', $lvli );
			/*在库移动履历生成结束*/
			


				//判断该商品是否有可用的指定固定库位(没有被其他商品或其他批次占用)
		        $sql = "SELECT A.CKBH,A.KQBH,A.KWBH,DECODE(B.SHULIANG,NULL,-9999,B.SHULIANG) AS SHULIANG ".
		               " FROM H01DB012403 A ".
		               " LEFT JOIN H01DB012404 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.KQBH = B.KQBH AND A.KWBH = B.KWBH".
		               "                        AND B.SHPBH = :SHPBH AND B.PIHAO =:PIHAO AND B.SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') ".
		               " WHERE A.QYBH = :QYBH ".
		               " AND A.SHFSHKW = '1' ".  //零散
		               " AND A.ZHDSHPBH = :SHPBH ".  //指定商品编号
		               " AND A.KWZHT = '1'".
		               " AND NOT EXISTS(SELECT NULL FROM H01DB012404 ".
		               "                WHERE QYBH = A.QYBH AND CKBH = A.CKBH ".
		               "                AND KQBH = A.KQBH AND KWBH = A.KWBH ".
		               "                AND SHULIANG > 0  ".
		               "                AND (SHPBH <> :SHPBH OR SHPBH = :SHPBH AND PIHAO <> :PIHAO AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')))".
		               " ORDER BY SHULIANG DESC";
		
		        //绑定查询变量
		        unset($bind); 
				$bind ['QYBH'] = $_SESSION ['auth']->qybh;
				$bind ['SHPBH'] = $kucun["SHPBH"];
				$bind ['PIHAO'] = $kucun["PIHAO"];
				$bind ['SHCHRQ'] = $kucun["SHCHRQ"];
				
				//固定架信息
		        $kwinfo = $this->_db->fetchRow ( $sql, $bind ); 
		        
		        
		        //如果没有可用的指定固定库位则判断是否存在可用的周转库位
		        if ($kwinfo ==FALSE){
		        	$sql = "SELECT A.CKBH,A.KQBH,A.KWBH,DECODE(B.SHULIANG,NULL,-9999,B.SHULIANG) AS SHULIANG ".
		               " FROM H01DB012403 A ".
		               " LEFT JOIN H01DB012404 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.KQBH = B.KQBH AND A.KWBH = B.KWBH ".
		               "                        AND B.SHPBH = :SHPBH AND B.PIHAO =:PIHAO AND B.SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') ".
		        	   " WHERE A.QYBH = :QYBH ".
		               " AND A.SHFSHKW = '1' ".  //零散位
		               " AND A.KWZHT = '1'". //可用
		        	   " AND A.SHFGDJ = '2'". //周转架
		               " ORDER BY SHULIANG DESC";
		        	//周转架信息  
		           $kwinfo = $this->_db->fetchRow ( $sql, $bind ); 	
		        }
				
		        
				//如果固定架与周转架均无既存信息， 则插入一条新的在库信息
	        	if($kwinfo["SHULIANG"]=="-9999"){
		        	//生成在库信息H01DB012404
		        	unset($zaiku); 
					$zaiku ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
					$zaiku ["CKBH"] = $kwinfo['CKBH']; //仓库编号
					$zaiku ["KQBH"] = $kwinfo['KQBH']; //库区编号
					$zaiku ["KWBH"] = $kwinfo['KWBH']; //库位编号
					$zaiku ["SHPBH"] = $kucun ['SHPBH']; //商品编号
					$zaiku ["PIHAO"] = $kucun ['PIHAO']; //批号
					$zaiku ["RKDBH"] = $kucun ['RKDBH']; //入库单号
                    $zaiku ["ZKZHT"] = $kucun ['ZKZHT'];//在库状态
                    $zaiku ["BZHDWBH"] = $kucun ['BZHDWBH']; //包装单位编号
                    $zaiku ["ZZHCHKRQ"] = new Zend_Db_Expr("TO_DATE('9999-12-31 23:59:59','YYYY-MM-DD HH24:MI:SS')"); //最终出库日期
                    $zaiku ["SHULIANG"] = $shuliang_lvli; //数量（补货数量=移动发生数量）
					$zaiku ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
					$zaiku ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')"); //保质期至
					
					$this->_db->insert ( 'H01DB012404', $zaiku );
	        	}else{
	        		//在库信息中存在既存的信息，则对库存数量进行更新处理
	        		$sql_zaiku = "UPDATE H01DB012404 ".
					             "SET SHULIANG = :SHULIANG ," .
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
					$bind ['SHULIANG'] = (int)$kwinfo["SHULIANG"] + $shuliang_lvli;   //数量 = 原数量 + 移动数量 
					        
					$stmt=$this->_db->query ( $sql_zaiku,$bind );
					
					//如没有完全对应项，则追加
					if($stmt->rowCount()==0){
						//生成在库信息H01DB012404
			        	unset($zaiku); 
						$zaiku ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
						$zaiku ["CKBH"] = $kwinfo['CKBH']; //仓库编号
						$zaiku ["KQBH"] = $kwinfo['KQBH']; //库区编号
						$zaiku ["KWBH"] = $kwinfo['KWBH']; //库位编号
						$zaiku ["SHPBH"] = $kucun ['SHPBH']; //商品编号
						$zaiku ["PIHAO"] = $kucun ['PIHAO']; //批号
						$zaiku ["RKDBH"] = $kucun ['RKDBH']; //入库单号
	                    $zaiku ["ZKZHT"] = $kucun ['ZKZHT'];//在库状态
	                    $zaiku ["BZHDWBH"] = $kucun ['BZHDWBH']; //包装单位编号
	                    $zaiku ["ZZHCHKRQ"] = new Zend_Db_Expr("TO_DATE('9999-12-31 23:59:59','YYYY-MM-DD HH24:MI:SS')"); //最终出库日期
	                    $zaiku ["SHULIANG"] = $shuliang_lvli; //数量（补货数量=移动发生数量）
						$zaiku ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
						$zaiku ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')"); //保质期至
						
						$this->_db->insert ( 'H01DB012404', $zaiku );
					}
	        	}
	        	
	        	
	        	//补货入库的移动履历生成
	        	unset($lvli); 
		       	$lvli ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
				$lvli ["CKBH"] = $kwinfo ['CKBH']; //仓库编号
				$lvli ["KQBH"] = $kwinfo ['KQBH']; //库区编号
				$lvli ["KWBH"] = $kwinfo ['KWBH']; //库位编号
				$lvli ["SHPBH"] = $kucun ['SHPBH']; //商品编号
				$lvli ["PIHAO"] = $kucun ['PIHAO']; //批号
				$lvli ["RKDBH"] = $kucun ['RKDBH']; //入库单号
				$lvli ["YDDH"] = $bhdbh; //移动单号(补货单编号)
				$lvli ["XUHAO"] = $idx ++; //序号
				$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
				$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')"); //保质期至
				$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');//处理时间
				$lvli ["SHULIANG"] = $shuliang_lvli; //移动数量
				$lvli ["BZHDWBH"] = $kucun ['BZHDWBH']; //包装单位编号
				$lvli ["ZHYZHL"] = "62"; //转移种类  补货入库				
				$lvli["BEIZHU"] = ''; //备注
				$lvli["ZKZHT"] = $kucun ['ZKZHT'];//在库状态
				$lvli["ZCHRQ"] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				$lvli["ZCHZH"] = $_SESSION ['auth']->userId;     //作成者
				$lvli["BGRQ"] = new Zend_Db_Expr('SYSDATE');     //变更日期
				$lvli["BGZH"] = $_SESSION ['auth']->userId;      //变更者
				
				$this->_db->insert ( 'H01DB012405', $lvli );

			
	
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
			$bhd["YRCK"] = $kwinfo ['CKBH'];//移入仓库编号
			$bhd["YRKQ"] = $kwinfo ['KQBH'];//移入库区编号
			$bhd["YRKW"] = $kwinfo ['KWBH'];//移入库位编号
			$bhd["BHLX"] = "3";             //补货类型：指定补货
			$bhd["BHSHL"] = $shuliang_lvli; //补货数量
			$bhd["ZHUANGTAI"] = "1";        //补货状态：未完成
			$bhd["ZCHRQ"] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		    $bhd["ZCHZH"] = $_SESSION ['auth']->userId;     //作成者
	    	$bhd["BGRQ"] = new Zend_Db_Expr('SYSDATE');     //变更日期
	    	$bhd["BGZH"] = $_SESSION ['auth']->userId;      //变更者
	    	
           	$this->_db->insert ( 'H01DB012450', $bhd );
	}
	

	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck($data) {
		if ($data ["SHPBH"] == "" ||    //商品编号
            $data ["ZHJCKBH"] == "" ||  //整件仓库编号
            $data ["ZHJKQBH"] == "" ||  //整件库区编号   
            $data ["ZHJKWBH"] == "" ||  //整件库位编号
            $data ["BZHDWBH"] == "" ||  //包装单位编号
            $data ["RKDBH"] == "" ||    //入库单编号
            $data ["ZKZHT"] == "" ||    //在库状态
            $data ["SHCHRQ"] == "" ||   //生产日期
            $data ["PIHAO"] == "" ) {   //批号
			return false;
		}
		
		return true;
	}
	
	
	
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck(){
		
		return true;
	}
	
}

	
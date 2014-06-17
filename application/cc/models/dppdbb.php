<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   动碰盘点报表(dppdbb)
 * 作成者：李洪波
 * 作成日：2011/01/13
 * 更新履历：
 *********************************/

class cc_models_dppdbb extends Common_Model_Base {

	private $idx_ROWNUM = 0; 	// 行号
	private $idx_SHPBH=1;		// 商品编号
	private $idx_SHPMC=2;		// 商品名称
	private $idx_SHQJC=3;		// 上期结存
	private $idx_BQJC=4;		// 本期结存
	private $idx_BQCGRK=5;		// 本期采购入库
	private $idx_BQCGTCH=6;		// 本期采购退出
	private $idx_BQXSHCK=7;		// 本期销售出库
	private $idx_BQXSHTH=8;		// 本期销售退回
	private $idx_MDDBRK=9;		// 门店调拨入库
	private $idx_MDDBTCH=10;	// 门店调拨退出
	private $idx_MDDBCK=11;		// 门店调拨出库
	private $idx_MDDBTH=12;		// 门店调拨退回
	private $idx_CKDBRK=13;		// 仓库调拨入库
	private $idx_CKDBTCH=14;	// 仓库调拨退出
	private $idx_CKDBCK=15;		// 仓库调拨出库
	private $idx_CKDBTH=16;		// 仓库调拨退回
	private $idx_BQSYZJ=17;		// 本期损溢增加
	private $idx_BQSYJSH=18;	// 本期损溢减少
	private $idx_BZHDW=19;		// 包装单位
	private $idx_SHPGG=20;		// 商品规格
	private $idx_CHANDI=21;		// 产地
	private $idx_BEIZHU=22;		// 备注
	
	/**
	 * 得到商品明细数据
	 * @param array $filter
	 * @return string xml
	 */

	public function getGridData($filter) {
		
		//检索SQL
		$sql = "SELECT DISTINCT A.SHPBH,B.SHPMCH," .
			   "C.NEIRONG,B.GUIGE,B.CHANDI,B.BZHDWBH" .
		       " FROM H01DB012404 A" . 
               " LEFT JOIN H01DB012101 B ON A.SHPBH=B.SHPBH" . 
			   " LEFT JOIN H01DB012001 C ON C.QYBH=A.QYBH AND C.ZIHAOMA=B.BZHDWBH AND C.CHLID='DW'" . 
		       " WHERE A.QYBH=:QYBH".
			   " AND A.CKBH =:CKBH".															
			   " AND A.KQBH =:KQBH";
		
		//$bind ['KSHRQ'] = $filter ['kshrq']; //开始日期
		//$bind ['JSHRQ'] = $filter ['jshrq']; //结束日期
		$bind ['CKBH'] = $filter ['ckbh']; //仓库编号
		$bind ['KQBH'] = $filter ['kqbh']; //库区编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		//$bind ['SHULIANG'] = $filter ['shuliang']; // 账面数量条件
		
		$shpbh=$this->_db->fetchAll($sql, $bind);
		
		for($i=0; $i<count($shpbh); $i++){
			//当前商品账面数量取得检索SQL
			$sqldqshpzhmshl = "SELECT BZHDWBH,SUM(SHULIANG) AS DQSHPSHL" .
						       " FROM H01DB012404" .
					           " WHERE QYBH =:QYBH" . 
				               " AND CKBH =:CKBH".															
							   " AND KQBH =:KQBH".
							   " AND SHPBH =:SHPBH".
							   " AND (ZZHCHKRQ is null OR ZZHCHKRQ >= TO_DATE(:KSHRQ,'YYYY-MM-DD HH24:MI:SS'))".
							   " GROUP BY BZHDWBH";
			
			$bind1 ['KSHRQ'] = $filter ['kshrq']." 00:00:00"; //开始日期
			$bind1 ['CKBH'] = $filter ['ckbh']; //仓库编号
			$bind1 ['KQBH'] = $filter ['kqbh']; //库区编号
			$bind1 ['SHPBH'] = $shpbh [$i]['SHPBH']; //商品编号
			$bind1 ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			
			$dqshpzhmshl=$this->_db->fetchAll($sqldqshpzhmshl, $bind1);
			
			//取得结束日期以后发生的账面数量变化信息检索SQL
			$sqljshshlbh = "SELECT BZHDWBH,SUM(SHULIANG) AS JSHSHLBH" .
						       " FROM H01DB012405" .
					           " WHERE QYBH =:QYBH" . 
				               " AND CKBH =:CKBH".															
							   " AND KQBH =:KQBH".
							   " AND SHPBH =:SHPBH".
							   " AND CHLSHJ>TO_DATE(:JSHRQ,'YYYY-MM-DD HH24:MI:SS')".
							   " GROUP BY BZHDWBH";
			
			$bind2 ['JSHRQ'] = $filter ['jshrq']." 23:59:59"; //结束日期
			$bind2 ['CKBH'] = $filter ['ckbh']; //仓库编号
			$bind2 ['KQBH'] = $filter ['kqbh']; //库区编号
			$bind2 ['SHPBH'] = $shpbh [$i]['SHPBH']; //商品编号
			$bind2 ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			
			$jshshlbh=$this->_db->fetchAll($sqljshshlbh, $bind2);
			
			//计算结束日期的24点时刻，账面数量
			for ($x=0;$x<count($dqshpzhmshl);$x++){
				
				if (count($jshshlbh)==0){
					$jshrqsl[$x]["BZHDWBH"]=$dqshpzhmshl[$x]["BZHDWBH"];
					$jshrqsl[$x]["JSHSHLBH"]=$dqshpzhmshl[$x]["DQSHPSHL"];
				}else{
					for ($y=0;$y<count($jshshlbh);$y++){
						if ($dqshpzhmshl[$x]["BZHDWBH"]==$jshshlbh[$y]["BZHDWBH"] && $dqshpzhmshl[$x]["DQSHPSHL"]!=$jshshlbh[$y]["JSHSHLBH"])
						{ 
							$jshrqsl[$x]["BZHDWBH"]=$dqshpzhmshl[$x]["BZHDWBH"];
							$jshrqsl[$x]["JSHSHLBH"]=$dqshpzhmshl[$x]["DQSHPSHL"]-$jshshlbh[$y]["JSHSHLBH"];
						}
					}
				}					
			}
			
			$countDataJG=0;
			for ($r=0;$r<count($jshrqsl);$r++){
				if ($jshrqsl[$r]["BZHDWBH"]==$shpbh[$i]['BZHDWBH']){
						$countDataJG+=$jshrqsl[$r]["JSHSHLBH"];
					}
			}
			
			//判断该商品是否符合账面数量条件。如果不符合则退出循环，到下一个商品。
			$flagZero=false;
			if ($filter ['shuliang']=="zerorup"){
				for ($z=0;$z<count($jshrqsl);$z++){
					if($jshrqsl[$z]["JSHSHLBH"]>0){
						$flagZero=true;
					}
				}
				if ($flagZero!=true){
					continue;
				}
			}
			elseif ($filter ['shuliang']=="zero"){
				for ($z=0;$z<count($jshrqsl);$z++){
						if($jshrqsl[$z]["JSHSHLBH"]>0){
							$flagZero=true;
						}
					}
				if ($flagZero==true){
						continue;
					}
			}
						
			//判断是否动销检索SQL
			$sqldongxiao = "SELECT ZHYZHL,SUM(SHULIANG) AS SHULIANG" .
						       " FROM H01DB012405" .
					           " WHERE QYBH =:QYBH" . 
				               " AND CKBH =:CKBH".															
							   " AND KQBH =:KQBH".
							   " AND SHPBH =:SHPBH".
							   " AND CHLSHJ <= TO_DATE(:JSHRQ,'YYYY-MM-DD HH24:MI:SS')".
							   " AND CHLSHJ >= TO_DATE(:KSHRQ,'YYYY-MM-DD HH24:MI:SS')".
							   " AND BZHDWBH = :BZHDWBH".
							   " GROUP BY ZHYZHL";
			
			$bind3 ['KSHRQ'] = $filter ['kshrq']." 00:00:00"; //开始日期
			$bind3 ['JSHRQ'] = $filter ['jshrq']." 23:59:59"; //结束日期
			$bind3 ['CKBH'] = $filter ['ckbh']; //仓库编号
			$bind3 ['KQBH'] = $filter ['kqbh']; //库区编号
			$bind3 ['SHPBH'] = $shpbh [$i]['SHPBH']; //商品编号
			$bind3 ['BZHDWBH'] = $shpbh [$i]['BZHDWBH']; //商品编号
			$bind3 ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			
			$dongxiao=$this->_db->fetchAll($sqldongxiao, $bind3);
			if(count($dongxiao)==0){
				continue;
			}
			
			//画面各个项目的设定。
			$recs[$i]["SHPBH"]=$shpbh[$i]["SHPBH"];//商品编号
			$recs[$i]["SHPMCH"]=$shpbh[$i]["SHPMCH"];//商品名称

			$countDX=0;
			for ($r=0;$r<count($dongxiao);$r++){
				$countDX+=$dongxiao[$r]["SHULIANG"];
			}
			
			$recs[$i]["SHQJC"]=$countDataJG-$countDX;  //上期结存
			$recs[$i]["BQJC"]=$countDataJG;  //本期结存
				
			$recs[$i]["BQCGRK"]="";
			$recs[$i]["BQCGTCH"]="";
			$recs[$i]["BQXSHCK"]="";
			$recs[$i]["BQXSHTH"]="";			
			$recs[$i]["MDDBRK"]="";
			$recs[$i]["MDDBTCH"]="";
			$recs[$i]["MDDBCK"]="";
			$recs[$i]["MDDBTH"]="";			
			$recs[$i]["CKDBRK"]="";
			$recs[$i]["CKDBTCH"]="";		
			$recs[$i]["CKDBCK"]="";		
			$recs[$i]["CKDBTH"]="";		
			$recs[$i]["BQSYZJ"]="";
			$recs[$i]["BQSYJSH"]="";
			
			for ($j=0;$j<count($dongxiao);$j++){			
				switch ($dongxiao[$j]["ZHYZHL"]) {
					case "11": 
						$recs[$i]["BQCGRK"]=$dongxiao[$j]["SHULIANG"];//本期采购入库
						break;
					case "12": 
						$recs[$i]["BQCGTCH"]=$dongxiao[$j]["SHULIANG"]*(-1);// 本期采购退出
						break;
					case "21": 
						$recs[$i]["BQXSHCK"]=$dongxiao[$j]["SHULIANG"]*(-1);// 本期销售出库
						break;
					case "22": 
						$recs[$i]["BQXSHTH"]=$dongxiao[$j]["SHULIANG"];//本期销售退回
						break;
					case "61": 
						$recs[$i]["MDDBRK"]=$dongxiao[$j]["SHULIANG"];// 门店调拨入库
						break;
					case "62": 
						$recs[$i]["MDDBTCH"]=$dongxiao[$j]["SHULIANG"]*(-1);//门店调拨退出
						break;
					case "63": 
						$recs[$i]["MDDBCK"]=$dongxiao[$j]["SHULIANG"]*(-1);//门店调拨出库
						break;
					case "64": 
						$recs[$i]["MDDBTH"]=$dongxiao[$j]["SHULIANG"];//门店调拨退回
						break;
					case "33": 
						$recs[$i]["CKDBRK"]=$recs[$i]["CKDBRK"]+$dongxiao[$j]["SHULIANG"];//仓库调拨入库
						break;
					case "37": 
						$recs[$i]["CKDBRK"]=$recs[$i]["CKDBRK"] + $dongxiao[$j]["SHULIANG"];//仓库调拨入库
						break;
					case "34": 
						$recs[$i]["CKDBTCH"]=$recs[$i]["CKDBTCH"] + $dongxiao[$j]["SHULIANG"]*(-1);//仓库调拨退出
						break;
					case "38": 
						$recs[$i]["CKDBTCH"]=$recs[$i]["CKDBTCH"] + $dongxiao[$j]["SHULIANG"]*(-1);//仓库调拨退出
						break;
					case "31": 
						$recs[$i]["CKDBCK"]=$recs[$i]["CKDBCK"] + $dongxiao[$j]["SHULIANG"]*(-1);//仓库调拨出库
						break;
					case "35": 
						$recs[$i]["CKDBCK"]=$recs[$i]["CKDBCK"] + $dongxiao[$j]["SHULIANG"]*(-1);//仓库调拨出库
						break;
					case "32": 
						$recs[$i]["CKDBTH"]=$recs[$i]["CKDBTH"] + $dongxiao[$j]["SHULIANG"];//仓库调拨退回
						break;
					case "36": 
						$recs[$i]["CKDBTH"]=$recs[$i]["CKDBTH"] + $dongxiao[$j]["SHULIANG"];//仓库调拨退回
						break;
					case "51": 
						$recs[$i]["BQSYZJ"]=$dongxiao[$j]["SHULIANG"];//本期损溢增加
						break;
					case "52": 
						$recs[$i]["BQSYJSH"]=$dongxiao[$j]["SHULIANG"]*(-1);//本期损溢减少
						break;
					case "42": 
						$beikao=$dongxiao[$j]["SHULIANG"];//备考
						break;
					default:
						break;
					}			
			}	
			$recs[$i]["NEIRONG"]=$shpbh[$i]["NEIRONG"]; //包装单位
			$recs[$i]["GUIGE"]=$shpbh[$i]["GUIGE"];//商品规格
			$recs[$i]["CHANDI"]=$shpbh[$i]["CHANDI"];//产地
			if($beikao!=""){
				$recs[$i]["BEIKAO"]="拆零数量：".$beikao;
			}
			
		}
				
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs);
	}
}

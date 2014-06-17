<?php
class gt_models_tool extends Common_Model_Base{
	/**
     * 自动分配货物库位
     *
     * @param string $shpbh  
     * @param string $pihao
     * @param int $shuliang
     * @param string $ckbh
     * @return array 
     */
	public function autoAssignKuwei($shpbh,$pihao,$bzhshuliang=0,$lsshuliang=0){
		
		//分配库位之前，先锁定库存表，待本次入库结束之后释放
		$locksql = "LOCK TABLE H01DB012404 IN EXCLUSIVE MODE";
		$this->_db->query($locksql);
		
		//取得商品长宽高计量规格及所需库区类型
		$sql = " SELECT SHPBH,NVL(DBZHCH,0) AS DBZHCH,NVL(DBZHK,0) AS DBZHK,NVL(DBZHG,0) AS DBZHG,NVL(ZHDKQLX,'000') AS ZHDKQLX,NVL(JLGG,0) AS JLGG".
		       " FROM H01DB012101 ".
		       " WHERE QYBH = :QYBH AND SHPBH = :SHPBH";

		$shpinfo = $this->_db->fetchRow($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"SHPBH"=>$shpbh));
		if($shpinfo==FALSE){throw new Zend_Exception("商品信息(".$shpbh.")不存在。");}
		if($shpinfo["DBZHCH"] == "0" || $shpinfo["DBZHCH"] == "0" || $shpinfo["DBZHCH"] == "0" ){throw new Zend_Exception("商品(".$shpbh.")：大包装长宽高数据未指定。");}
		if($shpinfo["JLGG"] == "0" ){throw new Zend_Exception("商品(".$shpbh.")：计量规格未指定。");}
		
		$bzhkuwei = array();
		$lskuwei = array();
		
		//包装库位分配
		if($bzhshuliang > 0){
			$bzhkuwei = $this->assignBzhKuwei($shpinfo,$pihao,$bzhshuliang);
		}
		//零散库位分配
		if($lsshuliang > 0){
		    $lskuwei = $this->assignLsKuwei($shpinfo,$pihao,$lsshuliang);
		}		
		
		return array_merge($bzhkuwei,$lskuwei);
   	}
   	/**
   	 * 自动分配包装库位
   	 *
   	 * @param unknown_type $shpinfo
   	 * @param unknown_type $pihao
   	 * @param unknown_type $bzhshuliang
   	 * @return unknown
   	 */
   	private function assignBzhKuwei($shpinfo,$pihao,$bzhshuliang){
   		$assigned_kuweiinfo =  Array(); //库位分配结果
		$wfpshuliang = $bzhshuliang; //未分配数量
			
		//取得同批号商品库存库位
		$sql = "SELECT A.CKBH,A.KQBH,A.KWBH,NVL(B.KWCH,0) AS KWCH,NVL(B.KWK,0) AS KWK,NVL(B.KWG,0) AS KWG,SUM(A.SHULIANG) AS SHULIANG
		        FROM H01DB012404 A 
		        JOIN H01DB012403 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.KQBH = B.KQBH AND A.KWBH = B.KWBH 
		        WHERE A.QYBH = :QYBH AND A.SHPBH = :SHPBH AND A.PIHAO = :PIHAO AND A.SHULIANG > 0 AND B.SHFSHKW = '0'
		        GROUP BY A.CKBH,A.KQBH,A.KWBH,B.KWCH,B.KWK,B.KWG
		        ORDER BY A.CKBH,A.KQBH,A.KWBH";
		$tp_kuweiinfo = $this->_db->fetchAll($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"SHPBH"=>$shpinfo["SHPBH"],"PIHAO"=>$pihao));
		
		//分配每个库位可堆放数量
        foreach ($tp_kuweiinfo as $kuwei){
         	$kdfShuliang = $this->computeStoreShuliang($shpinfo,$kuwei) - $kuwei["SHULIANG"]; //可堆放数量 = 总计可堆放数量-已堆放数量  
         	if($kdfShuliang > 0){
	         	$tmpKuwei["CKBH"] = $kuwei["CKBH"];
	         	$tmpKuwei["KQBH"] = $kuwei["KQBH"];
	         	$tmpKuwei["KWBH"] = $kuwei["KWBH"];
	         	$tmpKuwei["SHFSHKW"] = "0";
	          	
	         	//可堆放数量>未分配数量
	          	if($kdfShuliang > $wfpshuliang){
	         		$tmpKuwei["SHULIANG"] = $wfpshuliang;
	         		$wfpshuliang = 0;
	         	}else{
	         		$tmpKuwei["SHULIANG"] = $kdfShuliang;
	         		$wfpshuliang = $wfpshuliang - $tmpKuwei["SHULIANG"];//未分配数量
	         	}	
	        	array_push($assigned_kuweiinfo,$tmpKuwei);//追加至分配库位结果数组
         	}
        	
         	if($wfpshuliang == 0) break;
        }
        
        //如果仍有未分配数量，则选择同库区类型的完全空闲库位
        if($wfpshuliang > 0){
        	$sql = "SELECT A.CKBH,A.KQBH,A.KWBH,NVL(A.KWCH,0) AS KWCH,NVL(A.KWK,0) AS KWK,NVL(A.KWG,0) AS KWG FROM H01DB012403 A 
        	        JOIN H01DB012402 B ON A.QYBH = B.QYBH AND A.KQBH = B.KQBH
        	        WHERE A.QYBH = :QYBH AND A.SHFSHKW = '0' AND A.KWZHT = '1'
        	              AND B.KQLX = :KQLX AND B.KQZHT = '1'
        	              AND NOT EXISTS(SELECT 0 FROM H01DB012404 WHERE QYBH = A.QYBH AND CKBH = A.CKBH AND KQBH = A.KQBH AND KWBH = A.KWBH AND SHULIANG > 0)";
            $emptyKuwei = $this->_db->fetchAll($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"KQLX"=>$shpinfo["ZHDKQLX"]));
	        //分配每个库位可堆放数量
	        foreach ($emptyKuwei as $kuwei){
	        	$kdfShuliang = $this->computeStoreShuliang($shpinfo,$kuwei); //可堆放数量  
	        	if($kdfShuliang > 0){
		        	$tmpKuwei["CKBH"] = $kuwei["CKBH"];
		         	$tmpKuwei["KQBH"] = $kuwei["KQBH"];
		         	$tmpKuwei["KWBH"] = $kuwei["KWBH"];
		         	$tmpKuwei["SHFSHKW"] = "0";
		         	//可堆放数量>未分配数量
		          	if($kdfShuliang > $wfpshuliang){
		         		$tmpKuwei["SHULIANG"] = $wfpshuliang;
		         		$wfpshuliang = 0;
		 
		         	}else{
		         		$tmpKuwei["SHULIANG"] = $kdfShuliang;
		         		$wfpshuliang = $wfpshuliang - $tmpKuwei["SHULIANG"];//未分配数量
		         	}
		        	array_push($assigned_kuweiinfo,$tmpKuwei);//追加至分配库位结果数组			
	        	}
	        	if($wfpshuliang == 0) break;        
		    }
        }
        
        //如果仍有未分配数量，则选择同库区类型的部分空闲库位(非本商品)
        if($wfpshuliang > 0){
        	$sql = "SELECT A.CKBH,A.KQBH,A.KWBH,NVL(B.KWCH,0) AS KWCH,NVL(B.KWK,0) AS KWK,NVL(B.KWG,0) AS KWG FROM H01DB012404 A 
        	        JOIN H01DB012403 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.KQBH = B.KQBH AND A.KWBH = B.KWBH  
                    WHERE A.QYBH = :QYBH AND A.SHPBH <> :SHPBH AND B.SHFSHKW = '0'
                    GROUP BY A.CKBH,A.KQBH,A.KWBH,B.KWCH,B.KWK,B.KWG
                    HAVING SUM(A.SHULIANG) > 0";
            $bfEmptyKuwei = $this->_db->fetchAll($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"SHPBH"=>$shpinfo["SHPBH"]));
	        //分配每个库位可堆放数量
	        foreach ($bfEmptyKuwei as $kuwei){
	        	$kdfShuliang = $this->computeStoreShuliang($shpinfo,$kuwei); //可堆放数量  
	        	if($kdfShuliang > 0){
		         	$tmpKuwei["CKBH"] = $kuwei["CKBH"];
		         	$tmpKuwei["KQBH"] = $kuwei["KQBH"];
		         	$tmpKuwei["KWBH"] = $kuwei["KWBH"];
		         	$tmpKuwei["SHFSHKW"] = "0";
	          	
		         	//可堆放数量>未分配数量
		          	if($kdfShuliang > $wfpshuliang){
		         		$tmpKuwei["SHULIANG"] = $wfpshuliang;
		         		$wfpshuliang = 0;
		         	}else{
		         		$tmpKuwei["SHULIANG"] = $kdfShuliang;
		         		$wfpshuliang = $wfpshuliang - $tmpKuwei["SHULIANG"];//未分配数量
		         	}
		        	array_push($assigned_kuweiinfo,$tmpKuwei);//追加至分配库位结果数组	
	        	}
	        	if($wfpshuliang == 0) break;        
		    }
        }
        
        
        //如果仍有未分配数量，则分配至共通库位
        if($wfpshuliang > 0){   
           	$tmpKuwei["CKBH"] = "";
		   	$tmpKuwei["KQBH"] = "";
		  	$tmpKuwei["KWBH"] = ""; 
		  	$tmpKuwei["SHFSHKW"] = "0"; 
		  	$tmpKuwei["SHULIANG"] = $wfpshuliang;
		  	array_push($assigned_kuweiinfo,$tmpKuwei);//追加至分配库位结果数组	      
        }
               	
		return $assigned_kuweiinfo;
   		
   	}
   	
   	private function assignLsKuwei($shpinfo,$pihao,$lsshuliang){
   		$assigned_kuweiinfo = array();
   		//判断该商品是否有可用的指定固定库位(没有被其他商品或者批号占用)
   		$sql = "SELECT A.CKBH,A.KQBH,A.KWBH".
		       " FROM H01DB012403 A JOIN H01DB012402 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.KQBH = B.KQBH".
		       " WHERE A.QYBH = :QYBH ".
		       " AND A.SHFSHKW = '1' ".  //零散
		       " AND A.ZHDSHPBH = :SHPBH ".  //指定商品编号
		       " AND A.KWZHT = '1'".
   		       " AND B.KQLX = :KQLX".
		       " AND NOT EXISTS(SELECT NULL FROM H01DB012404 ".
		       "                WHERE QYBH = A.QYBH AND CKBH = A.CKBH ".
		       "                AND KQBH = A.KQBH AND KWBH = A.KWBH ".
		       "                AND SHULIANG > 0  ".
		       "                AND (SHPBH <> :SHPBH OR SHPBH = :SHPBH AND PIHAO <> :PIHAO))".
   		       " ORDER BY A.CKBH,A.KQBH,A.KWBH";
       	
   		$gdwInfo = $this->_db->fetchRow($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"SHPBH"=>$shpinfo["SHPBH"],"PIHAO"=>$pihao,"KQLX"=>$shpinfo["ZHDKQLX"]));
        
   		//存在可用固定货位
   		if($gdwInfo!=FALSE){
   			$assigned_kuweiinfo[0]["CKBH"] = $gdwInfo["CKBH"];
   			$assigned_kuweiinfo[0]["KQBH"] = $gdwInfo["KQBH"];
   			$assigned_kuweiinfo[0]["KWBH"] = $gdwInfo["KWBH"];
   			$assigned_kuweiinfo[0]["SHFSHKW"] = "1"; 
   			$assigned_kuweiinfo[0]["SHULIANG"] = $lsshuliang;
   		}else{
   		    $sql = "SELECT A.CKBH,A.KQBH,A.KWBH".
		       " FROM H01DB012403 A JOIN H01DB012402 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.KQBH = B.KQBH".
		       " WHERE A.QYBH = :QYBH ".
		       " AND A.SHFSHKW = '1' ".  //零散
		       " AND A.KWZHT = '1'".
   		       " AND A.SHFGDJ = '2'".
   		       " AND B.KQLX = :KQLX".
		       " AND NOT EXISTS(SELECT NULL FROM H01DB012404 ".
		       "                WHERE QYBH = A.QYBH AND CKBH = A.CKBH ".
		       "                AND KQBH = A.KQBH AND KWBH = A.KWBH ".
		       "                AND SHULIANG > 0  ".
		       "                AND (SHPBH <> :SHPBH OR SHPBH = :SHPBH AND PIHAO <> :PIHAO))".
   		       " ORDER BY A.CKBH,A.KQBH,A.KWBH";
		     //周转架信息  
		     $zhzhwInfo = $this->_db->fetchRow($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"SHPBH"=>$shpinfo["SHPBH"],"PIHAO"=>$pihao,"KQLX"=>$shpinfo["ZHDKQLX"]));
		     if ($zhzhwInfo !=FALSE){
		     	$assigned_kuweiinfo[0]["CKBH"] = $zhzhwInfo["CKBH"];
   			    $assigned_kuweiinfo[0]["KQBH"] = $zhzhwInfo["KQBH"];
   			    $assigned_kuweiinfo[0]["KWBH"] = $zhzhwInfo["KWBH"];
   			    $assigned_kuweiinfo[0]["SHFSHKW"] = "1"; 
   			    $assigned_kuweiinfo[0]["SHULIANG"] = $lsshuliang;
		     }else{
		     	$assigned_kuweiinfo[0]["CKBH"] = "";
   			    $assigned_kuweiinfo[0]["KQBH"] = "";
   			    $assigned_kuweiinfo[0]["KWBH"] = "";
   			    $assigned_kuweiinfo[0]["SHFSHKW"] = "1"; 
   			    $assigned_kuweiinfo[0]["SHULIANG"] = $lsshuliang;
		     }
   		}
   		
   		return $assigned_kuweiinfo;
   	}
	
 /**
 * 计算库位空闲空间可堆放某一商品的数量
 *
 * @param array $shpinfo
 * @param array $kuweiinfo
 * @return 可堆放数量
 */
	private function computeStoreShuliang($shpinfo,$kuweiinfo){
		$dfshuliang = 0;
		//取得该库位已放置的其他商品的情报
		$sql = "SELECT A.SHPBH,B.JLGG,NVL(B.DBZHCH,0) AS DBZHCH,NVL(B.DBZHK,0) AS DBZHK,NVL(B.DBZHG,0) AS DBZHG,SUM(A.SHULIANG) AS SHULIANG FROM H01DB012404 A
		        JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH 
		        WHERE A.QYBH = :QYBH AND A.CKBH = :CKBH AND A.KQBH = :KQBH AND A.KWBH = :KWBH AND A.SHPBH <> :SHPBH AND A.SHULIANG > 0 
		        GROUP BY A.SHPBH,B.JLGG,B.DBZHCH,B.DBZHK,B.DBZHG";
		
		//已存储其他商品的信息
		$storedShpinfo = $this->_db->fetchAll($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"CKBH"=>$kuweiinfo["CKBH"],"KQBH"=>$kuweiinfo["KQBH"],"KWBH"=>$kuweiinfo["KWBH"],"SHPBH"=>$shpinfo["SHPBH"]));

		$keyongMianji= $kuweiinfo["KWCH"] * $kuweiinfo["KWK"]; //本商品可用库位面积
		//已堆放的其他商品数量
		if(count($storedShpinfo) > 2){
			$dfshuliang = 0;
		}else{
			for($i=0;$i<count($storedShpinfo);$i++){
				$cengshu = intval($kuweiinfo["KWG"] / $storedShpinfo[$i]["DBZHG"]); //可堆放最大层数
				$xiangshu = ceil($storedShpinfo[$i]["SHULIANG"] / $storedShpinfo[$i]["JLGG"] /$cengshu); //每层需堆放的箱数
				$mianji = $storedShpinfo[$i]["DBZHCH"] * $storedShpinfo[$i]["DBZHK"] * $xiangshu; //所需面积
				$keyongMianji = $keyongMianji - $mianji;
			}
			
			$dfxiangshu = intval($keyongMianji / ($shpinfo["DBZHCH"] * $shpinfo["DBZHK"])); //本商品每层可堆放箱数
			$dfcengshu = intval($kuweiinfo["KWG"] / $shpinfo["DBZHG"]); //本商品可堆放层数
			$dfshuliang = $dfxiangshu * $dfcengshu * $shpinfo["JLGG"]; //可堆放数量
		}

		return $dfshuliang;
	}
	 
}
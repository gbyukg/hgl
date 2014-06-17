<?php
class Common_Tool{
	
	
    
    /*
	 *csv字符串反序列化成表格数组
	 */
	public static  function unSerializeToGrid($csv)
	{
		
		if($csv=="") return "";
		
		$grid= array();
		
		$rows =  split("\n",$csv);
		foreach ($rows as $row)
		{
			$cells = explode("||",$row);
			array_push($grid,$cells);
      	}
      	
      	return $grid;
		
	}
	/*
	 * 得到助记码
	 */
	public function getPy($str){
		$db = Zend_Registry::get ( "db" );
		$sql = "SELECT HGL_GETPY('".$str."') FROM DUAL";
		return $db->fetchOne($sql);
		
	}
	
	/*
	 * 单号分配
	 */
	public static function getDanhao($xmzhb='000',$riqi=""){
		
		$db = Zend_Registry::get ( "db" );
		
		$sql = "SELECT XMZHB || TO_CHAR(RIQI,'YYMMDD') || TO_CHAR(NUMID,'fm00000') AS DANHAO,NUMID ".
		       "FROM H01DB012003 ".
		       "WHERE QYBH = :QYBH ".
		       "AND XMZHB = :XMZHB ".
		       "AND TO_CHAR(RIQI,'YYYY-MM-DD') =TO_CHAR(SYSDATE,'YYYY-MM-DD')";
		       " FOR UPDATE WAIT 10";
		$bind ['QYBH']= $_SESSION ['auth']->qybh;
		$bind ['XMZHB']= $xmzhb;
		//$bind ['RIQI']= $riqi;
		
		//当日单号信息
		$rec = $db->fetchRow($sql,$bind);
		
		//不存在则添加
		if($rec == FALSE){
			$sql = "INSERT INTO H01DB012003 VALUES(:QYBH,:XMZHB,SYSDATE,:NUMID)";
			$bind ['QYBH']= $_SESSION ['auth']->qybh;
			$bind ['XMZHB']= $xmzhb;
			//$bind ['RIQI'] = $riqi;
			$bind['NUMID'] = 1;
			$db->query($sql,$bind);
						
			//添加完毕重新取得单号
			$sql = "SELECT XMZHB || TO_CHAR(RIQI,'YYMMDD') || TO_CHAR(NUMID,'fm00000') AS DANHAO,NUMID ".
		       "FROM H01DB012003 ".
		       "WHERE QYBH = :QYBH ".
		       "AND XMZHB = :XMZHB ".
		       "AND TO_CHAR(RIQI,'YYYY-MM-DD') = TO_CHAR(SYSDATE,'YYYY-MM-DD')";
		    $bind =null;
		    $bind ['QYBH']= $_SESSION ['auth']->qybh;
		    $bind ['XMZHB']= $xmzhb;
		  //  $bind ['RIQI']= $riqi;
		
		    //当日单号信息
		    $rec = $db->fetchRow($sql,$bind);
			
		}
		    //存在则更新
			$sql = "UPDATE H01DB012003 SET NUMID = :NUMID".
			       " WHERE QYBH = :QYBH AND XMZHB = :XMZHB AND TO_CHAR(RIQI,'YYYY-MM-DD') = TO_CHAR(SYSDATE,'YYYY-MM-DD')";
			$bind ['QYBH']= $_SESSION ['auth']->qybh;
			$bind ['XMZHB']= $xmzhb;
			///$bind ['RIQI'] = $riqi;
			$bind ['NUMID'] = (int)$rec['NUMID'] + 1;
			$db->query($sql,$bind);

		
		return $rec['DANHAO'];
	}	
	/*
	 * json
	 * 替换Null
	 */
	public static function json_encode($obj){
		$json = json_encode($obj);
		$table_change = array(':null'=>':""');
		return strtr($json,$table_change);  
			
	}
	
    /**
	 * 表格用xml数据格式生成
	 *
	 * @param $recs        本次检索数据
	 * @param $cols        列表项目
	 * @param $rowcnt      是否带行号
	 * @param $rowid       ROWID项目名
	 * @param $totalcount  总记录数
	 * @param $posstart    起始位置
	 */
	public static function createXml($recs,$rowcnt = true,$totalCount=0,$posStart=0,$userdata=null)
	{
		$dom = new DOMDocument ( '1.0', 'utf-8' );
		$rows = $dom->createElement ( "rows" );
		
		$rows->setAttribute ( "total_count", $totalCount );
		$rows->setAttribute ( "pos", $posStart );
		
		if(isset($userdata)){
			foreach ($userdata as $key=>$value){
				$userdata_node = $dom->createElement ( "userdata" );
				$userdata_node->setAttribute ( "name", $key );
			    $userdata_node->appendChild ( $dom->createTextNode ($value) );
			    $rows->appendChild($userdata_node);
			}		
		}

		foreach ( $recs as $rec ) {
			$row = $dom->createElement ( "row" );
			
			//是否需要行号
			if($rowcnt)
			{
			 $cell = $dom->createElement ( "cell" ); //行号
			 $cell->appendChild ( $dom->createTextNode ("&nbsp;") ); //行号
			 $row->appendChild ( $cell);
			}
			
			//各列赋值
			unset($rec['RN']);
			foreach ($rec as $col){
				$cell = $dom->createElement ( "cell" );
				$cell->appendChild ( $dom->createTextNode ( $col ) ); 
				$row->appendChild ( $cell);
			}
			
			
			$rows->appendChild ( $row );
		}
		

			
		$dom->appendChild ( $rows );
		
		//return count($recs);
		return $dom->saveXML ();
		
	}
     /**
	 * 树形控件用Xml数据生成
	 *
	 * @param array $recs  树形数组
	 * @param unknown_type $id  树形节点id项目名
	 * @param unknown_type $text 树形节点显示项目名
	 * @return unknown
	 */
	public static function createTreeXml($recs,$id,$text,$userdatakey=""){
		$dom = new DOMDocument ( '1.0', 'utf-8' );
		
		$root = $dom->createElement ( "tree" );
		$root->setAttribute ( "id", "0" );
		$dom->appendChild ( $root );
		
		$currLevel = 0; //当前级别
		$prevLevel = 0; //前一条级别
		$itemArr = array ();

		foreach ( $recs as $rec ) {
			$currLevel = count ( split ( "/", $rec ["PATH"] ) ) - 2;
			$itemArr [$currLevel] = $dom->createElement ( "item" );
			$itemArr [$currLevel]->setAttribute ( "text", $rec [$text] );
			$itemArr [$currLevel]->setAttribute ( "id", $rec [$id] );
			
			//USERDATA
			if($userdatakey!="" && $rec [$userdatakey] !=""){
				$userdata = $dom->createElement ( "userdata" );
				$userdata->setAttribute ( "name", $userdatakey );
				$userdata->appendChild ( $dom->createTextNode ($rec [$userdatakey]) ); 
				$itemArr [$currLevel]->appendChild($userdata);
			}

			
			if ($currLevel <= $prevLevel) {
				
				if ($currLevel == 0) {
					$root->appendChild ( $itemArr [$currLevel] );
				} else {
					$itemArr [$currLevel - 1]->appendChild ( $itemArr [$currLevel] );
				}
			} elseif ($currLevel > $prevLevel) {
				
				$itemArr [$prevLevel]->appendChild ( $itemArr [$currLevel] );
			
			}
			
			$prevLevel = $currLevel;
		
		}
		
		return $dom->saveXML ();
	}
	
     /**
	 * 分页数据取得Sql
	 *
	 * @param String $searchSql
	 * @param array $filter
	 * @return array
	 */
	public static function getPageSql($searchSql, $filter) {
		
		$pagedSql = array ();
		
		if (isset ( $filter ["posStart"] ))
			$posStart = $filter ['posStart'];
		else
			$posStart = 0;
		if (isset ( $filter ["count"] ))
			$count = $filter ['count'];
		else
			$count = 100;
		
		$posEnd = $posStart + $count;
		
		$sql = "SELECT * FROM (";
		$sql .= "SELECT X.*,ROWNUM RN FROM (";
		$sql .= $searchSql;
		$sql .= " ) X  WHERE ROWNUM <= $posEnd ) WHERE RN  > $posStart";
		
		$pagedSql ["sql_page"] = $sql;
		$pagedSql ["sql_count"] = "SELECT COUNT(*) FROM ($searchSql)";
		return $pagedSql;
	}
	
	
	public static function createRptXml($recs,$ToCompress=0){
		$XMLText='<xml> ';
		
		foreach ( $recs as $row ) {
			$XMLText.="<row ";
			foreach ($row as $colname=>$colvalue){
				$XMLText.= $colname."=\"".$colvalue ."\" ";
			}
			$XMLText.="/>\n";
		}
		$XMLText.="</xml>\n";
			
		if ( $ToCompress )
		{
		    //写入特有的压缩头部信息，以便报表客户端插件能识别数据
	        header("gr_zip_type: deflate");                                      //指定压缩方法
	        header("gr_zip_size: ".strval(strlen($XMLText)));                    //指定数据的原始长度
	        header("gr_zip_encode: utf-8");//指定数据的编码方式 utf-8 utf-16 ...
	    	
		    //压缩数据并输出
	        $compressed = gzdeflate($XMLText); 
		    return $compressed;
		}
		else
		{
		    return $XMLText;
		}
	}
	
	/**
	 * 生成过滤器SQL
	 *
	 * @param unknown_type $gridId
	 * @param unknown_type $filterParams
	 * @param unknown_type $bind
	 * @return unknown
	 */
	public static function createFilterSql($gridId,$filterParams,& $bind){
		$where = "";

		//过滤条件
		if($filterParams != null){         
			$filterArr = $filterParams[$gridId . '_FILTERARR'];  //选中的过滤条件
			$typeArr = $filterParams[$gridId .'_TYPEARR'];  //选中的过滤条件类型
			//循环过滤条件对象
			for($i=0;$i<count($filterArr);$i++){
				$key = $filterArr[$i]; //过滤项目id
				$type = $typeArr[$i]; //过滤项目类型
				$op = $filterParams[$gridId .'_f_o_'.$key]; //比较符
				$value = $filterParams[$gridId .'_f_v_'.$key]; //过滤项目值
				$value_1 = $filterParams[$gridId .'_f_v_'.$key.'_1'];//过滤项目值1
				
				//项目值为空时跳过该过滤条件
				if(is_string($value)){ 
					//等于，不等于,Like 
					if(($op == '0' ||  $op == '1' || $op == '2') && strlen($value)==0) continue;
					if(($op == '3') && strlen($value)==0  && strlen($value_1)==0) continue;
				}else{
				    if(count($value)==0) continue;
				}
				
				
				//判断类型组合sql
				switch($type){
					case '0': //文本
					case '3': //radio	
						if($op=='0'){//等于
							$where .=" AND " . $key . " = :dy_" . $key;  //条件
					        $bind["dy_".$key] = $value;
						}elseif($op=='1'){//不等于
							$where .=" AND " . $key . " <> :bdy_" . $key;  //条件
					        $bind["bdy_".$key] = $value;
						}elseif($op=='2'){//部分等于
							$where .=" AND lower(" . $key . ") LIKE '%' || :bfdy_". $key ." || '%'";   //条件
					        $bind["bfdy_".$key] = strtolower($value);
						}elseif($op=='3'){//范围
							if($value!=null){
				    		    $where .=" AND " . $key . " >= :from_" . $key;  //条件from
				    		    $bind["from_".$key] = $value; 	
				    	    }
				            if($value_1!=null){
				    		    $where .=" AND " . $key . " <= :to_" . $key;  //条件to
				    		    $bind["to_".$key] = $value_1; 	
				    	    }
						}
						break;
					case '1'://日期
						if($op=='0'){//等于
							$where .=" AND TO_CHAR(" . $key . ",'YYYY-MM-DD') = :dy_" . $key ;
					        $bind["dy_".$key] = $value;
						}elseif($op=='1'){//不等于
							$where .=" AND TO_CHAR(" . $key . ",'YYYY-MM-DD') <> :bdy_" . $key ;
					        $bind["bdy_".$key] = $value;
						}elseif($op=='3'){//范围
							if($value!=null){
				    		    $where .=" AND " . $key . " >= TO_DATE(:from_" . $key . ",'YYYY-MM-DD')";  //条件from
				    		    $bind["from_".$key] = $value; 	
				    	    }
				            if($value_1!=null){
				    		    $where .=" AND " . $key . " <= TO_DATE(:to_" . $key . ",'YYYY-MM-DD HH24:MI:SS')";  //条件TO
				    		    $bind["to_".$key] = $value_1 . " 23:59:59" ; 	
				    	    }
						}
						break;

					case '2'://数值
						break;
					case '4': //checkbox
					case '5': //select
						if($op=='0'){
							if(is_array($value)){
								$where .=" AND " . $key . " IN ( ";
								for($j=0;$j<count($value);$j++){
									if($j == 0){
										$where .= ":".$key.$j;
									}else{
										$where .= ",:".$key.$j;
									}
									
									$bind[$key.$j] = $value[$j];
								}
								$where .=")";
							}else{
							    $where .=" AND " . $key . " = :dy_" . $key; 
						        $bind["dy_".$key] = $value;
							}			
						}elseif($op=='1'){
							if(is_array($value)){
								$where .=" AND " . $key . " NOT IN ( ";
								for($j=0;$j<count($value);$j++){
									if($j == 0){
										$where .= ":".$key.$j;
									}else{
										$where .= ",:".$key.$j;
									}
									
									$bind[$key.$j] = $value[$j];
								}
								$where .=")";
							}else{
							    $where .=" AND " . $key . " <> :bdy_" . $key;
						        $bind["bdy_".$key] = $value;
							}
					    break; 
						}
				}
	
			}
			
		}
		
		return $where;
	}
    /**
     * Enter description here...
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @param unknown_type $shpbh
     * @param unknown_type $pihao
     * @param unknown_type $shuliang
     * @param unknown_type $ckbh
     * @return unknown
     */
	public function autoAssignKuwei($shpbh,$pihao,$shuliang,$ckbh){
		
		$assigned_kuweiinfo =  Array(); //库位分配结果
		$wfpshuliang = $shuliang; //未分配数量
		$db = Zend_Registry::get ( "db" );
		
		//取得商品长宽高及所需库区类型
		$sql = " SELECT NVL(DBZHCH,0) AS DBZHCH,NVL(DBZHK,0) AS DBZHK,NVL(DBZHG,0) AS DBZHG,NVL(ZHDKQLX,'000') AS ZHDKQLX,NVL(JLGG,0) AS JLGG".
		       " FROM H01DB012101 ".
		       " WHERE QYBH = :QYBH AND SHPBH = :SHPBH";

		$shpinfo = $db->fetchRow($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"SHPBH"=>$shpbh));
		
		//商品信息
		if($shpinfo==FALSE){
			throw new Zend_Exception("商品信息(".$shpbh.")不存在。");
		}
		//大包装长宽高
		if($shpinfo["DBZHCH"] == "0" || $shpinfo["DBZHCH"] == "0" || $shpinfo["DBZHCH"] == "0" ){
			throw new Zend_Exception("商品(".$shpbh.")：大包装长宽高数据未指定。");
		}
		
		//计量规格
		if($shpinfo["JLGG"] == "0" ){
			throw new Zend_Exception("商品(".$shpbh.")：计量规格未指定。");
		}
		
		//取得同批号商品库存库位
		$sql = "SELECT A.CKBH,A.KQBH,A.KWBH,NVL(B.KWCH,0) AS KWCH,NVL(B.KWK,0) AS KWK,NVL(B.KWG,0) AS KWG,SUM(A.SHULIANG) AS SHULIANG
		        FROM H01DB012404 A 
		        JOIN H01DB012403 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.KQBH = B.KQBH AND A.KWBH = B.KWBH 
		        WHERE A.QYBH = :QYBH AND A.SHPBH = :SHPBH AND A.PIHAO = :PIHAO AND A.SHULIANG > 0
		        GROUP BY A.CKBH,A.KQBH,A.KWBH,B.KWCH,B.KWK,B.KWG
		        ORDER BY A.CKBH,A.KQBH,A.KWBH";
		$tp_kuweiinfo = $db->fetchAll($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"SHPBH"=>$shpbh,"PIHAO"=>$pihao));
		
		//分配每个库位可堆放数量
        foreach ($tp_kuweiinfo as $kuwei){
         	$kdfShuliang = self::computeStoreShuliang($shpinfo,$kuwei) - $kuwei["SHULIANG"]; //可堆放数量 = 总计可堆放数量-已堆放数量  
         	if($kdfShuliang > 0){
	         	$tmpKuwei["CKBH"] = $kuwei["CKBH"];
	         	$tmpKuwei["KQBH"] = $kuwei["KQBH"];
	         	$tmpKuwei["KWBH"] = $kuwei["KWBH"];
	          	
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
            $emptyKuwei = $db->fetchAll($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"KQLX"=>$shpinfo["ZHDKQLX"]));
	        //分配每个库位可堆放数量
	        foreach ($emptyKuwei as $kuwei){
	        	$kdfShuliang = self::computeStoreShuliang($shpinfo,$kuwei); //可堆放数量  
	        	if($kdfShuliang > 0){
		        	$tmpKuwei["CKBH"] = $kuwei["CKBH"];
		         	$tmpKuwei["KQBH"] = $kuwei["KQBH"];
		         	$tmpKuwei["KWBH"] = $kuwei["KWBH"];
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
                    WHERE A.QYBH = :QYBH AND A.SHPBH <> :SHPBH
                    GROUP BY A.CKBH,A.KQBH,A.KWBH,B.KWCH,B.KWK,B.KWG
                    HAVING SUM(A.SHULIANG) > 0";
            $bfEmptyKuwei = $db->fetchAll($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"SHPBH"=>$shpbh));
	        //分配每个库位可堆放数量
	        foreach ($bfEmptyKuwei as $kuwei){
	        	$kdfShuliang = self::computeStoreShuliang($shpinfo,$kuwei); //可堆放数量  
	        	if($kdfShuliang > 0){
		         	$tmpKuwei["CKBH"] = $kuwei["CKBH"];
		         	$tmpKuwei["KQBH"] = $kuwei["KQBH"];
		         	$tmpKuwei["KWBH"] = $kuwei["KWBH"];
	          	
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
	function computeStoreShuliang($shpinfo,$kuweiinfo){
		$dfshuliang = 0;
		$db = Zend_Registry::get ( "db" );
		//取得该库位已放置的其他商品的情报
		$sql = "SELECT A.SHPBH,B.JLGG,NVL(B.DBZHCH,0) AS DBZHCH,NVL(B.DBZHK,0) AS DBZHK,NVL(B.DBZHG,0) AS DBZHG,SUM(A.SHULIANG) AS SHULIANG FROM H01DB012404 A
		        JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH 
		        WHERE A.QYBH = :QYBH AND A.CKBH = :CKBH AND A.KQBH = :KQBH AND A.KWBH = :KWBH AND A.SHPBH <> :SHPBH AND A.SHULIANG > 0 
		        GROUP BY A.SHPBH,B.JLGG,B.DBZHCH,B.DBZHK,B.DBZHG";
		
		//已存储其他商品的信息
		$storedShpinfo = $db->fetchAll($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"CKBH"=>$kuweiinfo["CKBH"],"KQBH"=>$kuweiinfo["KQBH"],"KWBH"=>$kuweiinfo["KWBH"],"SHPBH"=>$shpinfo["SHPBH"]));

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

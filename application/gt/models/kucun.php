<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    库存选择Model
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_models_kucun extends Common_Model_Base {
	/*
	 * 列表数据取得（xml格式）
	 */
	function getListData($filter) {
		//检索SQL
		$sql = "SELECT A.CKMCH,A.KQMCH,A.KWMCH,A.PIHAO,A.SHULIANG,A.BZHQZH,A.SHCHRQ,".
		       "A.CKBH,A.KQBH,A.KWBH,A.SHFSHKW,DECODE(A.SHFSHKW,'1','散货库位','0','包装库位','库位类型未知') AS SHFSHKWMCH ".
		       "FROM H01VIEW012002 A ".
		       "WHERE A.QYBH = :QYBH  AND A.SHPBH = :SHPBH ";
		//库位类型
		if($filter['flg']=='0'){
			$sql .= " AND A.SHFSHKW = '0'";
		}else if($filter['flg']=='1'){
			$sql .= " AND A.SHFSHKW = '1'";
		}

		//20110302仓库为条件追加
		if($filter['ckbh']!=''){
			
			$sql .= " AND A.ckbh = :ckbh ";
			$bind['CKBH'] = $filter['ckbh'];
		}
		
		//排序
		$sql .=" ORDER BY A.SHFSHKW,A.ZKZHT DESC,A.PIHAO,A.SHCHRQ,A.RKDBH,A.CKBH,A.KQBH,A.KWBH";
		
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHPBH'] = $filter['shpbh'];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		
		return Common_Tool::createXml ( $recs,false, $totalCount, $filter ["posStart"] );
	}
	/*
	 * 取得销售用库存列表数据
	 * 商品编号 名称 批号 包装数量 零散数量
	 */
	function getListDataForXs($shpbh){
		$sql = "SELECT SHPBH,SHPMCH,PIHAO,TO_CHAR(SHCHRQ,'YYYY-MM-DD'),TO_CHAR(BZHQZH,'YYYY-MM'),
		        SUM(BZHSHL) AS BZHSHL,--整件
		        SUM(LSSHL) AS LSSHL,--零散
		        SUM(BZHSHL+LSSHL) AS SHULIANG --总数
		        FROM (SELECT SHPBH,SHPMCH,PIHAO,SHCHRQ,BZHQZH,ZKZHT,RKDBH,SHFGDJ,SHULIANG AS BZHSHL,0 AS LSSHL
                      FROM H01UV012005 
                      WHERE QYBH = :QYBH AND SHPBH = :SHPBH AND SHFSHKW = '0' AND SHULIANG > 0 --包装库位
                      UNION ALL   --合并结果集
                      SELECT SHPBH,SHPMCH,PIHAO,SHCHRQ,BZHQZH,ZKZHT,RKDBH,SHFGDJ,0 AS BZHSHL,SHULIANG AS LSSHL 
                      FROM H01UV012005
                      WHERE QYBH = :QYBH AND SHPBH = :SHPBH AND SHFSHKW = '1' AND SHULIANG > 0 --零散库位
                      )
               GROUP BY SHPBH,SHPMCH,PIHAO,SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM')
               ORDER BY MAX(ZKZHT) DESC,MAX(RKDBH),PIHAO,MAX(SHFGDJ)";  //排列顺序    催销 >先入先出
		
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHPBH'] = $shpbh;
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $sql,$bind );
		return Common_Tool::createXml ( $recs,false);
	}
	
	
	
	/*
	 * 根据商品和库位类型，取得最新库存数据
	 */
	/*
	function getKucunData($filter){
		//检索SQL
		$sql = "SELECT A.* FROM H01VIEW012002 A ".
		       "WHERE A.QYBH = :QYBH".
		       " AND A.SHPBH = :SHPBH ".
		       " AND A.SHFSHKW = :SHFSHKW";
		//20110302仓库为条件追加
		if($filter['ckbh']!=''){
			
			$sql .= " AND A.CKBH = :CKBH ";
			$bind['CKBH'] = $filter['ckbh'];
		}
		
		//20110302
		
		//排序
		$sql .=" ORDER BY ZKZHT DESC,PIHAO,SHCHRQ,RKDBH,CKBH,KQBH,KWBH";
		
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHPBH'] = $filter['shpbh'];
		$bind['SHFSHKW'] = $filter['shfshkw'];
		return $this->_db->fetchAll ( $sql,$bind );
	}*/
}

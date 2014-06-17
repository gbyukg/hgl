<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    业务员选择
 * 作成者：周义
 * 作成日：2011/02/21
 * 更新履历：
 *********************************/
class gt_models_ywy extends Common_Model_Base{
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
			
		//检索SQL
		$sql = "SELECT YGBH,YGXM,SSBM,SSBMMCH,DHHM,SHJHM,DZYJ ".
		       "FROM H01UV012002 ".
		       "WHERE QYBH =:QYBH ".
		       "AND YGZHT = '1' ";
		
		if($filter['flg']=='0'){
			$sql .= "AND DWBH = :DWBH ";
			$sql .= "AND YGQF = 'C' ";
			$sql .= "AND SHFCGY = '1' ";  //采购员
			$bind ['DWBH'] = $filter['dwbh'];
		}else if($filter['flg']=='1'){
			$sql .= "AND DWBH = :DWBH ";
			$sql .= "AND YGQF = 'X' ";
			$sql .= "AND SHFXSHY = '1' ";   //销售员
			$bind ['DWBH'] = $filter['dwbh'];
		}else if($filter['flg']=='2'){
			$sql .= "AND SHFCKGLY = '1' "; //仓库管理员
		}
		$sql .=" ORDER BY YOUXIANJI"; //优先级
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		       
		//取得数据
		$recs = $this->_db->fetchAll ($sql ,$bind);
		
		return Common_Tool::createXml($recs,true);
	}

	/*
	 * 自动完成数据取得
	 */
	public function getData($filter){
       $sql = "SELECT YGBH,YGXM,SSBM,SSBMMCH,DHHM,SHJHM,DZYJ ".
		       "FROM H01UV012002 ".
		       "WHERE QYBH =:QYBH ".
 		       "AND YGZHT = '1' ";
       
		if($filter['flg']=='0'){
			$sql .= "AND DWBH = :DWBH ";
			$sql .= "AND YGQF = 'C' ";
			$sql .= "AND SHFCGY = '1' ";  //采购员
			$bind ['DWBH'] = $filter['dwbh'];
		}else if($filter['flg']=='1'){
			$sql .= "AND DWBH = :DWBH ";
			$sql .= "AND YGQF = 'X' ";
			$sql .= "AND SHFXSHY = '1' "; //销售员
			$bind ['DWBH'] = $filter['dwbh'];
		}else if($filter['flg']=='2'){
			$sql .= "AND SHFCKGLY = '1' "; //仓库管理员
		}				
		//查询条件		
		if($filter['searchkey'] !=""){
			$sql .= " AND (YGBH LIKE :SEARCHKEY || '%' OR lower(YGXM) LIKE :SEARCHKEY || '%' OR lower(ZHJM) LIKE :SEARCHKEY || '%')";
		    $bind['SEARCHKEY']= $filter['searchkey'];
		}
		
		$sql .=" ORDER BY YOUXIANJI"; //优先级
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);
	}
}
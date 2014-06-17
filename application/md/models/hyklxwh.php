<?php
/**********************************************************
 * 模     块：   门店模块(MD)
 * 机     能：   会员卡类型维护(HYKLXWH)
 * 作成者：   刘    枞
 * 作成日：   2011/02/12
 * 更新履历：
 **********************************************************/
class md_models_hyklxwh extends Common_Model_Base {
	
	/**
	 * 得到会员卡类型列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "ZHUANGTAI", "HYKFLBH","HYKFLMCH","KOULV","JFJD","JFXSH","YXQY","BEIZHU" );

		//检索SQL
		$sql = "SELECT DECODE(ZHUANGTAI,'X','禁用','1','正常','未知') AS ZHUANGTAI," . 
		       " HYKFLBH,HYKFLMCH,KOULV,JFJD,JFXSH,YXQY,BEIZHU,BGZH,BGRQ " . 
		       " FROM H01VIEW012502 " . 
		       " WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("MD_HYKLXWH",$filter['filterParams'],$bind);;
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=",HYKFLBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
	}
	
	
	/**
	 * 取得会员卡类型信息
	 * @param string $bh 编号
	 * @param array $filter  查询条件
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getxx($bh, $filter=null, $flg = 'current') {
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）
		//排序用字段名
		$fields = array("", "ZHUANGTAI", "HYKFLBH", "HYKFLMCH", "KOULV", "JFJD", "JFXSH", "YXQY", "BEIZHU");

		$sql_list = "SELECT ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",HYKFLBH) AS NEXTROWID,".
		            " LAG(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,HYKFLBH) AS PREVROWID,".
					" DECODE(ZHUANGTAI,'X','禁用','1','正常','未知') AS ZHUANGTAI," . 
					" HYKFLBH,HYKFLMCH,KOULV,JFJD,JFXSH,YXQY,BEIZHU,BGZH,BGRQ " . 
					" FROM H01VIEW012502 " . 
					" WHERE QYBH = :QYBH";

        //绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("MD_HYKLXWH",$filter['filterParams'],$bind);

		//会员卡类型信息单条查询
		$sql_single = "SELECT HYKFLBH,HYKFLMCH,KOULV,JFJD,JFXSH,YXQY,ZHUANGTAI,BEIZHU FROM H01VIEW012502 ";

		//当前记录
		if ($flg == 'current') {
			$sql_single .= "WHERE QYBH = :QYBH AND HYKFLBH = :HYKFLBH";
		} else if ($flg == 'next') {  //下一条
			$sql_single .= "WHERE ROWID = (SELECT NEXTROWID FROM (SELECT NEXTROWID,HYKFLBH FROM ( $sql_list ) WHERE HYKFLBH = :HYKFLBH))";		
		} else if ($flg == 'prev') {  //前一条
			$sql_single .= "WHERE ROWID = (SELECT PREVROWID FROM (SELECT PREVROWID,HYKFLBH FROM ( $sql_list ) WHERE HYKFLBH = :HYKFLBH))";		
		}

		$bind['HYKFLBH'] = $bh;       //会员卡类型编号
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	
	/**
	 * 会员卡类型信息登录
	 *
	 * @return bool
	 */
	function insertxx() {
		//判断编号是否存在
		if ($this->getxx( $_POST ['KPLXBH'] ) != FALSE) {
			return false;
		} else {
			$data ['QYBH'] = $_SESSION ['auth']->qybh;          //区域编号
			$data ['HYKFLBH'] = $_POST ['KPLXBH'];              //会员卡类型编号
			$data ['HYKFLMCH'] = $_POST ['KPLX'];               //会员卡类型名称
			$data ['KOULV'] = $_POST ['KOULV'];                 //扣率
			$data ['JFJD'] = $_POST ['JFJD'];                   //积分精度
			$data ['JFXSH'] = $_POST ['JFXSH'];                 //积分系数
			$data ['YXQY'] = $_POST ['YXQY'];                   //有效期（月）
			$data ['ZHUANGTAI'] = '1';                          //状态
			$data ['BEIZHU'] = $_POST ['BEIZHU'];               //备注
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" );    //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId;        //操作用户

			$this->_db->insert( "H01DB012502", $data );

			return true;
		}
	}
	
	
	/**
	 * 更新会员卡分类信息
	 *
	 * @return bool
	 */
	function updatexx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012502 WHERE QYBH = :QYBH AND HYKFLBH = :HYKFLBH FOR UPDATE WAIT 10";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'HYKFLBH' => $_POST ['HYKFLBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );

		//时间戳已经变更
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012502 SET " . 
			       " HYKFLMCH = :HYKFLMCH," . 
			       " KOULV = :KOULV," . 
			       " JFJD = :JFJD," . 
			       " JFXSH = :JFXSH," . 
			       " YXQY = :YXQY," . 
			       " BEIZHU = :BEIZHU," . 
			       " BGRQ = SYSDATE," . 
			       " BGZH = :BGZH" . 
			       " WHERE QYBH = :QYBH AND HYKFLBH =:HYKFLBH";

			$bind ['HYKFLMCH'] = $_POST ['KPLX'];           //会员卡类型名称
			$bind ['KOULV'] = $_POST ['KOULV'];             //扣率
			$bind ['JFJD'] = $_POST['JFJD'];                //积分精度
			$bind ['JFXSH'] = $_POST ['JFXSH'];             //积分系数
			$bind ['YXQY'] = $_POST ['YXQY'];               //有效期（月）
			$bind ['BEIZHU'] = $_POST ['BEIZHU'];           //备注
			$bind ['BGZH'] = $_SESSION ['auth']->userId;    //变更者
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;      //区域编号
			$bind ['HYKFLBH'] = $_POST ['KPLXBH'];          //会员卡类型编号

			$this->_db->query ( $sql, $bind );
			return true;
		}
	}
	
	
	/**
	 * 会员卡类型禁用和启用
	 *
	 * @param string $bh  编号
	 * @param string $zht 状态
	 * @return unknown
	 */
	function updateStatus($bh, $zht) {
		$sql = "UPDATE H01DB012502 " .
		       " SET ZHUANGTAI = :ZHUANGTAI" .
		       " WHERE QYBH =:QYBH AND HYKFLBH =:HYKFLBH";
		
		$bind['QYBH'] =$_SESSION ['auth']->qybh;
		$bind['HYKFLBH']= $bh;
		$bind['ZHUANGTAI'] = $zht;
		return $this->_db->query( $sql, $bind );
	}

}
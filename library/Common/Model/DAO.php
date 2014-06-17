<?php
/***
**
**具体的父类查看Zend_Db_Adapter_Abstract
**也就是/library/Zend/Db/Adapter/Abstract.php
**
**
**/
abstract class Common_Model_DAO extends Zend_Db_Table
{
	/**
	 * table name
	 *
	 * 
	 */
	protected  $_name;	
	
	/**
	 * database connection
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected  $_db;

	
	
	function __construct(){
		$this->_db = Zend_Registry::get('db');
		$this->_name = SQL_PREFIX.$this->_name;
		parent::__construct();
	}
	
		/**
		 * 得到全部记录信息**
		 * *
		 *
		 * @return unknown
		 */
	   public function getAllinfo(){		
			$strSQL = " SELECT * FROM " . $this->_name . " ORDER BY id DESC";
			return  $this->_db->fetchAll($strSQL);
		}
		
		
		/**
		 * 得到一条信息*
		 *
		 * @param unknown_type $sys_projeck_id
		 * @return unknown
		 */
	   public function getinfo($name,$value){		
			$strSQL = " SELECT * FROM " . $this->_name." WHERE 
						 UPPER($name) = :value";
			$repData = array("value"=>strtoupper($value));
			return  $this->_db->fetchRow($strSQL,$repData);
		}
		
		/**
		 * 得到记录的总条数*
		 *
		 * @return unknown
		 */
	    public function getRecordCount()
	    {
	         $strSQL = "SELECT COUNT(*) FROM " . $this->_name;

			 $where = $this->getSearchWhere();
			 
	         if ($where!="") $strSQL .= " WHERE ".$where;

	         $count = $this->_db->fetchOne($strSQL);   
	         return $count;
	    }




		/**
		 * 得到分页记录信息**
		 * *
		 *
		 * @return unknown
		 */
	   public function getPage($filter){		
			$strSQL = "SELECT * FROM ( SELECT ROWNUM ROWSEQ, X.* FROM ( SELECT * FROM " . $this->_name ;

			 $where = $this->getSearchWhere();

	         if ($where!="") $strSQL .= " WHERE ".$where;

 			 $strSQL .= " ORDER by g.".$filter['sort_by']." ".$filter['sort_order']. ") X) WHERE ROWSEQ BETWEEN ";
	         if ($filter["start"] == 0){
	            $strSQL .= '1 AND ' . $filter["page_size"];
	         }else{
	              $startv = $filter["start"]+1;
	              $endv = $filter["start"] + $filter["page_size"];
	              $strSQL .=  $startv . ' AND ' . $endv;
	        }

			return  $this->_db->fetchAll($strSQL);
		}


		/**
		 *
		 * 得到搜索的条件**
		 * *
		 * @return string
		 */
	   public function getSearchWhere(){
			$where = "";
			return $where;
	   }



		/**
		 * 下拉列表**
		 * *
		 * @param  $listvalue,$listKey (都是字段的名字)
		 * @return Array
		 */
	   public function getListArr($listvalue,$listKey="id"){		
			$backResult = array();
			$strSQL = " SELECT * FROM " . $this->_name . " ORDER BY id ASC";
			$result = $this->_db->fetchAll($strSQL);
			foreach ($result as $key=>$entity) {
				$backResult[$entity[$listKey]] = $entity[$listvalue];
			}
			return $backResult;
		}	

	    
	/**
	 * 保存过滤条件
	 * @param   array   $filter     过滤条件
	 * @param   string  $sql        查询语句
	 * @param   string  $param_str  参数字符串，由list函数的参数组成
	 */
	 public function set_filter($filter, $sql,$where, $param_str = '')
	{
	    $filterfile = basename($_SERVER['PHP_SELF'], '.php');
	    if ($param_str)
	    {
	        $filterfile .= $param_str;
	    }
	    setcookie('lib[lastfilterfile]', sprintf('%X', crc32($filterfile)), time() + 600);
	    setcookie('lib[lastfilter]',     urlencode(serialize($filter)), time() + 600);
	    setcookie('lib[lastfiltersql]',  urlencode($sql), time() + 600);
	    setcookie('lib[lastfilterwhere]',  urlencode($where), time() + 600);


		$_SESSION['LIB']['lastfilterfile'] = sprintf('%X', crc32($filterfile));
		$_SESSION['LIB']['lastfilter'] = urlencode(serialize($filter));
		$_SESSION['LIB']['lastfiltersql'] = urlencode($sql);
	    $_SESSION['LIB']['lastfilterwhere'] = urlencode($where);
	}
			
		/**
		 * 取得上次的过滤条件
		 * @param   string  $param_str  参数字符串，由list函数的参数组成
		 * @return  如果有，返回array('filter' => $filter, 'sql' => $sql)；否则返回false
		 */
		public function get_filter($param_str = '')
		{
		    $filterfile = basename($_SERVER['PHP_SELF'], '.php');
		    if ($param_str){
		        $filterfile .= $param_str;
		    }
		    
		    if (isset($_GET['uselastfilter']) && isset($_SESSION['LIB']['lastfilterfile'])){		        
				/*
		        return array(
		            'filter' => unserialize(urldecode($_COOKIE['lib']['lastfilter'])),
		            'sql'    => urldecode($_COOKIE['lib']['lastfiltersql']),
		            'where' => urldecode($_COOKIE['lib']['lastfilterwhere'])
		        );
				*/
		        return array(
		            'filter' => unserialize(urldecode($_SESSION['LIB']['lastfilter'])),
		            'sql'    => urldecode($_SESSION['LIB']['lastfiltersql']),
		            'where' => urldecode( $_SESSION['LIB']['lastfilterwhere'])
		        );
		    }else{
		        return false;
		    }
		}
}

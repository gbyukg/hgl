<?php
class Common_Pager
{
		/**
		 *  生成给pager.lbi赋值的数组
		 * @access  public
		 * @param   string      $url        分页的链接地址(必须是带有参数的地址，若不是可以伪造一个无用参数)
		 * @param   array       $param      链接参数 key为参数名，value为参数值
		 * @param   int         $record     记录总数量
		 * @param   int         $page       当前页数
		 * @param   int         $size       每页大小
		 * @return  array       $pager
		 */
		private $_size;
		
		function __construct(){
			$this->_size = 18;
		}
		
		public function get_pager($url, $param, $record_count, $page = 1)
		{
		    $size = intval($size);
		    if ($size < 1)
		    {
		        $size = $this->_size;
		    }
		    $page = intval($page);
		    if ($page < 1)
		    {
		        $page = 1;
		    }
		    $record_count = intval($record_count);
		    $page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;
		    if ($page > $page_count)
		    {
		        $page = $page_count;
		    }
		    $page_prev  = ($page > 1) ? $page - 1 : 1;
		    $page_next  = ($page < $page_count) ? $page + 1 : $page_count;
		    /* 将参数合成url字串 */
		    $param_url = '?';
		    foreach ($param AS $key => $value)
		    {
		        $param_url .= $key . '=' . $value . '&';
		    }
		    $pager['url']          = $url;
		    $pager['start']        = ($page -1) * $size;
		    $pager['page']         = $page;
		    $pager['size']         = $size;
		    $pager['record_count'] = $record_count;
		    $pager['page_count']   = $page_count;
		    $pager['page_first']   = $url . $param_url . 'page=1';
		    $pager['page_prev']    = $url . $param_url . 'page=' . $page_prev;
		    $pager['page_next']    = $url . $param_url . 'page=' . $page_next;
		    $pager['page_last']    = $url . $param_url . 'page=' . $page_count;
		    $pager['search'] = $param;
		    $pager['array']  = array();
		    for ($i = 1; $i <= $page_count; $i++)
		    {
		        $pager['array'][$i] = $i;
		    }
		    return $pager;
		}

		
			/**
			 * 分页的信息加入条件的数组
			 *
			 * @access  public
			 * @return  array
			 */
			public function page_and_size($filter)
			{
			    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
			    {
			        $filter['page_size'] = intval($_REQUEST['page_size']);
			    }
			    elseif (isset($_COOKIE['lib']['page_size']) && intval($_COOKIE['lib']['page_size']) > 0)
			    {
			        $filter['page_size'] = intval($_COOKIE['lib']['page_size']);
			    }
			    else
			    {
			        $filter['page_size'] = $this->_size;
			    }
			    /* 每页显示 */
			    $filter['page'] = (empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
			    /* page 总数 */
			    $filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;
			
			    /* 边界处理 */
			    if ($filter['page'] > $filter['page_count'])
			    {
			        $filter['page'] = $filter['page_count'];
			    }
			    $filter['start'] = ($filter['page'] - 1) * $filter['page_size'];
			    return $filter;
			}
			
			
			/**
			 * 根据过滤条件获得排序的标记
			 *
			 * @access  public
			 * @param   array   $filter
			 * @return  array
			 */
			public static  function sort_flag($filter)
			{
			    $flag['tag']    = 'sort_' . preg_replace('/^.*\./', '', $filter['sort_by']);
			    $flag['img']    = "<img src=\"".THEMESURL."/../common/images/". ($filter['sort_order'] == "DESC" ? "sort_desc.gif" : "sort_asc.gif") . "\" />";
			    return $flag;
			}
				
			/**
			 *设置多少条记录为1页
			 */
			public function setSize($size)
			{
				$this->_size = $size;
			}
}


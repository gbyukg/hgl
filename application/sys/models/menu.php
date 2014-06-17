<?php
class admin_models_menu {
	/**
	 * database connection
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_db = null;
	
	protected $opts = array (

	'target' => 'false', // 指定所有节点在新窗口中打开链接，默认为 true


	'folderLinks' => 'true', // 文件夹可链接，默认为 true


	'useSelection' => 'true', // 节点被选择时高亮显示，默认为 true


	'useCookies' => 'true', // 用Cookies保存树的当前状态，默认为 true


	'useLines' => 'true', // 创建带线的树，默认为 true


	'useIcons' => 'false', // 创建带有图标的树，默认为 true


	'useStatusText' => 'false', // 在底部的状态栏中显示节点名还是显示节点的url，默认为 false


	'closeSameLevel' => 'false', // 同级节点树只能展开一个，默认为 false


	'inOrder' => 'false' ); // 如果父级节点总是添加在子级节点之前,使用这个参数可以加速菜单显示，默认为 false
	

	public function __construct() {
		
		$this->_db = Zend_Registry::get ( 'db' );
	
	}
	
	//取得菜单数据
	public function getMenuData() {
		$strSQL = " SELECT MENUID,MENUNAME,PARENTMENUID,URL,TARGET,ICON
		            FROM ACL_MENU";
		$menuData = $this->_db->fetchAll ( $strSQL);
		return $this->createMenuScrip ( $menuData, $this->opts );
	}
	
	//生成菜单脚本文件
	function createMenuScrip(& $menus, $confOpts = null) {
		
		$output = '';
		
		$output .= "<div class=\"dtree\">\n";
		
		//$output .= "<p><a href=\"javascript: d.openAll();\">全部展开</a> | <a href=\"javascript: d.closeAll();\">全部折叠</a></p>\n";
		
		$output .= "<script type=\"text/javascript\">\n";
		
		$output .= "<!--\n\n";
		
		$output .= "d = new dTree('d');\n\n";
		
		/**

		 * 设置 dTree 配置选项值

		 */
		
		if (isset ( $confOpts ['target'] )) {
			
			$output .= "d.config.target = {$confOpts['target']};\n";
		
		} else {
			
			$output .= "d.config.target = true;\n";
		
		}
		
		if (isset ( $confOpts ['folderLinks'] )) {
			
			$output .= "d.config.folderLinks = {$confOpts['folderLinks']};\n";
		
		} else {
			
			$output .= "d.config.folderLinks = true;\n";
		
		}
		
		if (isset ( $confOpts ['useSelection'] )) {
			
			$output .= "d.config.useSelection = {$confOpts['useSelection']};\n";
		
		} else {
			
			$output .= "d.config.useSelection = true;\n";
		
		}
		
		if (isset ( $confOpts ['useCookies'] )) {
			
			$output .= "d.config.useCookies = {$confOpts['useCookies']};\n";
		
		} else {
			
			$output .= "d.config.useCookies = true;\n";
		
		}
		
		if (isset ( $confOpts ['useLines'] )) {
			
			$output .= "d.config.useLines = {$confOpts['useLines']};\n";
		
		} else {
			
			$output .= "d.config.useLines = true;\n";
		
		}
		
		if (isset ( $confOpts ['useIcons'] )) {
			
			$output .= "d.config.useIcons = {$confOpts['useIcons']};\n";
		
		} else {
			
			$output .= "d.config.useIcons = true;\n";
		
		}
		
		if (isset ( $confOpts ['useStatusText'] )) {
			
			$output .= "d.config.useStatusText = {$confOpts['useStatusText']};\n";
		
		} else {
			
			$output .= "d.config.useStatusText = false;\n";
		
		}
		
		if (isset ( $confOpts ['closeSameLevel'] )) {
			
			$output .= "d.config.closeSameLevel = {$confOpts['closeSameLevel']};\n";
		
		} else {
			
			$output .= "d.config.closeSameLevel = false;\n";
		
		}
		
		if (isset ( $confOpts ['inOrder'] )) {
			
			$output .= "d.config.inOrder = {$confOpts['inOrder']};\n";
		
		} else {
			
			$output .= "d.config.inOrder = false;\n\n";
		
		}
		
		//$output.= "d.imagefolder = {$THEMESURL}\n\n";
		
		// 添加菜单
		

		foreach ( $menus as $menu ) {
			
			if (! isset ( $menu ['MENUID'] ) || ! isset ( $menu ['PARENTMENUID'] ) || ! isset ( $menu ['MENUNAME'] )) {
				
				//js_alert('传入的菜单数据项 MENUID、PARENTMENUID 或 MENUNAME 可能不存在。');
				

				echo '传入的菜单数据项 MENUID、PARENTMENUID 或 MENUNAME 可能不存在。';
				
				return false;
			
			}
			
			if (! isset ( $menu['URL'] ) && ! isset ( $menu ['TITLE'] ) && ! isset ( $menu ['TARGET'] ) && ! isset ( $menu ['ICON'] ) && ! isset ( $menu ['iconOpen'] ) && ! isset ( $menu ['open'] )) {
				
				$output .= "d.add(\"{$menu['MENUID']}\", \"{$menu['PARENTMENUID']}\", \"{$menu['MENUNAME']}\");\n";
			
			} else {
				
				if (! isset ( $menu ['TITLE'] ) && ! isset ( $menu ['TARGET'] ) && ! isset ( $menu ['ICON'] ) && ! isset ( $menu ['iconOpen'] ) && ! isset ( $menu ['open'] )) {
					
					$output .= "d.add(\"{$menu['MENUID']}\", \"{$menu['PARENTMENUID']}\", \"{$menu['MENUNAME']}\", \"{$menu['URL']}\");\n";
				
				} else {
					
					if (! isset ( $menu ['TARGET'] ) && ! isset ( $menu ['ICON'] ) && ! isset ( $menu ['iconOpen'] ) && ! isset ( $menu ['open'] )) {
						
						if (! isset ( $menu['URL'] )) {
							
							$menu['URL'] = '';
						
						}
						
						$output .= "d.add(\"{$menu['MENUID']}\", \"{$menu['PARENTMENUID']}\", \"{$menu['MENUNAME']}\", \"{$menu['URL']}\", \"{$menu['title']}\");\n";
					
					} else {
						
						if (! isset ( $menu ['ICON'] ) && ! isset ( $menu ['iconOpen'] ) && ! isset ( $menu ['open'] )) {
							
							if (! isset ( $menu['URL'] )) {
								
								$menu['URL'] = '';
							
							}
							
							if (! isset ( $menu ['TITLE'] )) {
								
								$menu ['TITLE'] = '';
							
							}
							
							$output .= "d.add(\"{$menu['MENUID']}\", \"{$menu['PARENTMENUID']}\", \"{$menu['MENUNAME']}\", \"{$menu['URL']}\", \"{$menu['title']}\", '{$menu['TARGET']}');\n";
						
						} else {
							
							if (! isset ( $menu ['iconOpen'] ) && ! isset ( $menu ['open'] )) {
								
								if (! isset ( $menu['URL'] )) {
									
									$menu['URL'] = '';
								
								}
								
								if (! isset ( $menu ['TITLE'] )) {
									
									$menu ['TITLE'] = '';
								
								}
								
								if (! isset ( $menu ['TARGET'] )) {
									
									$menu ['target'] = '';
								
								}
								
								$output .= "d.add(\"{$menu['MENUID']}\", \"{$menu['PARENTMENUID']}\", \"{$menu['MENUNAME']}\", \"{$menu['URL']}\", \"{$menu['title']}\", '{$menu['TARGET']}', '{$menu['ICON']}');\n";
							
							} else {
								
								if (! isset ( $menu ['open'] )) {
									
									if (! isset ( $menu['URL'] )) {
										
										$menu['URL'] = '';
									
									}
									
									if (! isset ( $menu ['TITLE'] )) {
										
										$menu ['TITLE'] = '';
									
									}
									
									if (! isset ( $menu ['TARGET'] )) {
										
										$menu ['TARGET'] = '';
									
									}
									
									if (! isset ( $menu ['ICON'] )) {
										
										$menu ['ICON'] = '';
									
									}
									
									$output .= "d.add(\"{$menu['MENUID']}\", \"{$menu['PARENTMENUID']}\", \"{$menu['MENUNAME']}\", \"{$menu['URL']}\", \"{$menu['title']}\", '{$menu['TARGET']}', '{$menu['ICON']}', '{$menu['iconOpen']}');\n";
								
								} else {
									
									if (! isset ( $menu['URL'] )) {
										
										$menu['URL'] = '';
									
									}
									
									if (! isset ( $menu ['TITLE'] )) {
										
										$menu ['TITLE'] = '';
									
									}
									
									if (! isset ( $menu ['TARGET'] )) {
										
										$menu ['TARGET'] = '';
									
									}
									
									if (! isset ( $menu ['ICON'] )) {
										
										$menu ['ICON'] = '';
									
									}
									
									if (! isset ( $menu ['iconOpen'] )) {
										
										$menu ['iconOpen'] = '';
									
									}
									
									$output .= "d.add(\"{$menu['MENUID']}\", \"{$menu['PARENTMENUID']}\", \"{$menu['MENUNAME']}\", \"{$menu['URL']}\", \"{$menu['title']}\", '{$menu['TARGET']}', '{$menu['ICON']}', '{$menu['iconOpen']}', '{$menu['open']}');\n";
								
								}
							
							}
						
						}
					
					}
				
				}
			
			}
		
		}
		
		// 创建菜单树
		

		$output .= "\ndocument.write(d);\n\n";
		
		$output .= "//-->\n";
		
		$output .= "</script>\n";
		
		$output .= "</div>\n";
		
		return $output;
	
	}

}
	
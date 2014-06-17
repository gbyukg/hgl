<?php /* Smarty version 2.6.26, created on 2011-04-01 11:13:36
         compiled from sys_main_header.php */ ?>
﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<META http-equiv=Content-Type content="text/html; charset=utf-8;">
<link type="text/css" rel="stylesheet" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/header.css" />
</head>
<body >
    <div id="header">
     <ul class="navigation">
        <li id="navswitch"><a href="javascript:showMenu()">隐藏导航菜单</a></li>
        <li><a href="javascript:">快捷菜单</a></li>
    </ul>
   
    <ul class="shortmenu">
        <li><a href="javascript:">个人中心</a></li>
        <li><a href="javascript:">个人设置</a></li>
        <li><a href="javascript:">常用功能A</a></li>
        <li><a href="javascript:">常用功能B</a></li>
        <li><a href="javascript:">常用功能C</a></li>
        <li><a href="javascript:">常用功能D</a></li>
        <li><a href="javascript:">常用功能E</a></li>
        <li><a href="javascript:">常用功能F</a></li>
    </ul>
    
    <ul class="button">
        <li><img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_hd_support.gif" title="服务支持" /></li>
        <li><img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_hd_help.gif" title="帮助" /></li>
        <li><img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_hd_exit.gif" title="退出"  /></li>
    </ul>
</div>

</body>
</html>
<script>
function showMenu(){
	var obj = window.parent.document.getElementById('frameleft');
	var navswitch = document.getElementById('navswitch');

	if(obj.style.display == 'none'){
	    obj.style.display = '';
	    navswitch.innerHTML = '<a href="javascript:showMenu()">隐藏导航菜单</a>';
	 }else{
	  obj.style.display = 'none';
	  navswitch.innerHTML = '<a href="javascript:showMenu()">显示导航菜单</a>';;
	}

}

function showHeader(){
	var obj = window.parent.document.getElementById('trheader');

	if(obj.style.display == 'none'){
	    obj.style.display = '';
	    //navswitch.innerHTML = '<a href="javascript:showMenu()">隐藏导航菜单</a>';
	 }else{
	  obj.style.display = 'none';
	  //navswitch.innerHTML = '<a href="javascript:showMenu()">显示导航菜单</a>';;
	}

}

</script>
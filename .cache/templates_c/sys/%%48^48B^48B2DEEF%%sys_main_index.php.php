<?php /* Smarty version 2.6.26, created on 2011-04-01 11:13:35
         compiled from sys_main_index.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <META http-equiv=Content-Type content="text/html; charset=utf-8;">
    <link rel="STYLESHEET" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/style.css">
    <script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery-1.4.2.min.js"></script>

<!-- 
	<frameset  name="main" rows="70,*" frameborder="no" border="0" framespacing="0">
           <frame name="frmhead" src="<?php echo $this->_tpl_vars['ROOTURL']; ?>
/sys/main/header"  frameborder="no" border="0" scrolling="no" title="frmhead" noresize />
           	<frameset name="content" cols="200,*" frameborder="no" border="0" framespacing="0">
		       <frame name="frmnav" src="<?php echo $this->_tpl_vars['ROOTURL']; ?>
/sys/main/menu"  onresize="top.frames('frmnav').resizeTreebox()" frameborder="no" border="0" scrolling="no" title="frmnav" noresize />
		       <frame name="frmwork" src="<?php echo $this->_tpl_vars['ROOTURL']; ?>
/sys/main/work" frameborder="no" border="0" scrolling="no" title="frmwork" noresize />
        	</frameset>
   </frameset>
	<noframes>
	                                  您的浏览器不支持框架结构，请使用支持框架结构的浏览器来登陆本平台
		</noframes>
        -->
<script language="javascript">

//返回当前页面高度
function pageHeight() {
	if ($.browser.msie) {
		return document.compatMode == "CSS1Compat" ? document.documentElement.clientHeight
				: document.body.clientHeight;
	} else {
		return self.innerHeight;
	}
};
//返回当前页面宽度
function pageWidth() {
	if ($.browser.msie) {
		return document.compatMode == "CSS1Compat" ? document.documentElement.clientWidth
				: document.body.clientWidth;
	} else {
		return self.innerWidth;
	}
};

//隐藏菜单栏
function switchframe(){
		var obj = document.getElementById('frameleft');
		var switchbar = document.getElementById('switchbar');
		if(obj.style.display == 'none'){
		obj.style.display = '';
		switchbar.style.left = '187px';
		switchbar.style.backgroundPosition = '0';
		}else{
		obj.style.display = 'none';
		switchbar.style.left = '0px';
		switchbar.style.backgroundPosition = '-20';
		}
}

//自动调整尺寸
$(document).ready( function() {
	$("#leftmenu").height(pageHeight()-73);
    $("#work").height(pageHeight()-73);
    $(window).resize(function() {
    	$("#leftmenu").height(pageHeight()-73);
        $("#work").height(pageHeight()-73);
    });
});

</script>

<style>
#switchbar {
	width:20px;
	height:70px;
	position: absolute;
	left: 187px;
	top:200px;
	background:url(<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/frame_switch_index.gif) no-repeat 0;
	cursor: pointer;
}
</style>

</head>
<body>
<!-- 
 <div id="switchbar" onclick="switchframe()"></div>
  -->
  
<table border="0" cellPadding="0" cellSpacing="0" height="100%" width="100%">

 <tr height="70" id="trheader">
	<td colspan="2" align="middle" id="frametop" valign="top" name="frametop" >
	     <iframe id="header" name="header" frameborder="0" src="<?php echo $this->_tpl_vars['ROOTURL']; ?>
/sys/main/header" scrolling="no" style="width:100%; height:70px;z-index: 1"></iframe>
    </td>
 </tr>
 
 <tr valign="top" > 
  <td align="middle" id="frameleft" valign="top" name="frameleft" width="190">
      <iframe id="leftmenu" name="leftmenu" frameborder="0" src="<?php echo $this->_tpl_vars['ROOTURL']; ?>
/sys/main/menu" scrolling="no" style="width: 190px; z-index: 1" ></iframe>
  </td>
   <td>
     <iframe id="work" frameborder="0" name="work" src="<?php echo $this->_tpl_vars['ROOTURL']; ?>
/sys/main/work" scrolling="no" style="width: 100%; z-index: 1;" ></iframe>
    </td>
</tr>
</table>
</body>

</html>
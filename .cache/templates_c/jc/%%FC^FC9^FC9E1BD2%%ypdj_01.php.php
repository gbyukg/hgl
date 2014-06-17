<?php /* Smarty version 2.6.26, created on 2011-07-27 14:18:37
         compiled from ypdj_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title> 一品多价维护</title>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/ypdj_01.js"></script>
</head>
<body>
<div id="top">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td class="title"> 基础管理-一品多价维护</td>
        <td>
        <div id="toolbar"></div>
        </td>
    </tr>
</table>
</div>
<div id="vspace"></div>
<div id="body">
<form name="form1" id="form1" style="display:inline;margin:0px;">
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="100px">商品信息</td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="form">
    <tr>
        <th width="100px">商品编号<span style="color: red">*</span></th>
        <td width="200px">
        	<input id="SHPBH" name="SHPBH" type="text" style="width:180px" value="请双击选择商品" readonly class="readonly"/>
        </td>
        <th width="100px">商品名称</th>
        <td width="200px"><label id="SHPMCH" ></label></td>
    </tr>
    <tr>
        <th width="100px">商品规格</th>
        <td width="200px"><label id="GUIGE" ></label></td>
        <th width="100px">产地</th>
        <td width="200px"><label id="CHANDI"></label></td>
        <input id="SHUILV" name="SHUILV" type="hidden">
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="subtitle" border=0>
    <tr>
        <td width="150px">一品多价维护</td>
        <td><img id="ADDROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_add.gif" onclick="addRow();" /> 
        <img id="DELROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_del.gif" onclick="deleteRow();" /></td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
      <td>
        <div id="#grid_duojia" style="width:100%; height:260px; background-color:white; overflow:auto"></div>
      </td>
    </tr>
</table>
</form>
</div>
    <div id="loader" style="z-index:999;position:absolute;left:200px;top:100px;display:none">
          <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
    </div>
 </body>
</html>
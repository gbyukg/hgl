<?php /* Smarty version 2.6.26, created on 2011-05-23 13:07:06
         compiled from spbzqwh_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>商品保质期维护</title>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/spbzqwh_01.js"></script>
</head>
<body>
    <div id="top">
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
             <td class="title">基础管理-商品保质期维护</td>
             <td><div id="toolbar"></div></td>
           </tr>
        </table>
    </div>
    <div id="body">
    	<form name="form1" id="form1">
		<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
            <tr>
				<td width="150px">商品保质期信息 </td>
				<td>
                    <img id="addRow"	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_add.gif" onclick="addRow();" />
	     	 		<img id="deleteRow"	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_del.gif" onclick="deleteRow();" />                   
                </td>
            </tr>
        </table>    
        <table width="100%" cellpadding="0" cellspacing="1" class="grid">
            <tr><td>
            	<div id="#grid_bzqwh" style="width:100%;height:350px;background-color:white;overflow:auto"></div>
            </td></tr>
        </table>
        </form>
    </div>
</body>



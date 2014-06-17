<?php /* Smarty version 2.6.26, created on 2011-11-07 15:29:06
         compiled from chsdchk_02.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/chsdchk_02.js"></script>
</head>
<body>
<div id="top">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td class="title"><?php echo $this->_tpl_vars['title']; ?>
</td>
        <td>
        <div id="toolbar"></div>
        </td>
    </tr>
</table>
</div>
<div id="vspace"></div>
<div id="body">
<form name="form1" id="form1" style="display:inline;margin:0px;">
<input type=hidden id="action" name="action" value="<?php echo $this->_tpl_vars['action']; ?>
" />
<table width="100%" cellpadding="0" cellspacing="0" class="form" >
    <tr>
        <th width="130px">传送带出口:<span style="color: red">*</span></th>
        <td><input id="CHSDCHK" name="CHSDCHK" type="text" maxlength="1" style="width: 200px"
            class="editable"  value="<?php echo $this->_tpl_vars['rec']['CHSDCHK']; ?>
" /></td>
    </tr>
    <tr>
       <th>所属仓库:&nbsp;&nbsp;<span style="color: red">*</span></th>
       <td> 
					<input  id="CKMCH" name="CKMCH" type="text" maxlength="100" style="width: 200PX" class="editable"/>
					<input  id="CKBH" name="CKBH" type="hidden"/>
					<input  id="CKZHT" name="CKZHT" type="hidden"/>
       </td> 
    </tr>
    
</table>
</form>
</div>
</body>
</html>
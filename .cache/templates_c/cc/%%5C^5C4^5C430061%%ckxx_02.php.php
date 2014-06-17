<?php /* Smarty version 2.6.26, created on 2011-05-06 10:57:14
         compiled from ckxx_02.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/ckxx_02.js"></script>

</head>

<body>
<div id="top">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td class="title">仓储管理-<?php echo $this->_tpl_vars['title']; ?>
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
<input type=hidden id="BGRQ" name="BGRQ" value="<?php echo $this->_tpl_vars['rec']['RQ']; ?>
" />
<table width="100%" cellpadding="0" cellspacing="1" class="form">
    <tr>
        <th width="120px">仓库编号<span style="color: red">*</span></th>
        <td>
            <?php if ($this->_tpl_vars['action'] == 'update'): ?>
            <input type="text" maxlength="6" style="width: 200px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
"/>
            <input id="CKBH" name="CKBH" type=hidden maxlength="6" value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
"/>   
            <?php else: ?>
             <input id="CKBH" name="CKBH" type="text" maxlength="6" style="width: 200px"
            	class="editable" />
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th>仓库名称<span style="color: red">*</span></th>
        <td><input id="CKMCH" name="CKMCH" type="text" maxlength="50" style="width: 200px"
            class="editable"   value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
" /></td>
    </tr>
    <tr>
        <th>联系电话</th>
        <td><input id="LXDH" name="LXDH" type="text" maxlength="25" style="width: 200px" 
        	class="editable"   value="<?php echo $this->_tpl_vars['rec']['LXDH']; ?>
"/>
    </tr>
    <tr>
        <th>邮政编码</th>
        <td><input id="YZHBM" name="YZHBM" type="text" maxlength="6" style="width: 200px" 
        	class="editable"  value="<?php echo $this->_tpl_vars['rec']['YZHBM']; ?>
"/></td>
    </tr>
	<tr>
        <th>仓库地址</th>
        <td><input id="DIZHI" name="DIZHI" type="text" maxlength="100" style="width: 300px"
            class="editable"   value="<?php echo $this->_tpl_vars['rec']['DIZHI']; ?>
"/></td>
    </tr>
</table>
</form>
</div>
</body>
</html>
<?php /* Smarty version 2.6.26, created on 2011-05-11 16:18:01
         compiled from dyqkwxx_04.php */ ?>
<?php if ($this->_tpl_vars['full_page']): ?>
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
/js/dyqkwxx_04.js"></script>
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
<?php endif; ?>
<form name="form1" id="form1" style="display:inline;margin:0px;">
<table width="100%" cellpadding="0" cellspacing="1" class="form">
    <tr>
        <th width="120px">仓库</th>
        <td>
            <input type="text" maxlength="6" style="width: 200px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
"/>
            <input id="CKBH" name="CKBH" type="hidden" maxlength="6" value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
"/> 
        </td>
    </tr>
    <tr>
        <th>待验区</th>
        <td>
        <input id="DYQMCH" name="DYQMCH" type="text" maxlength="50" style="width: 200px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['DYQMCH']; ?>
" />
        <input id="DYQBH" name="DYQBH" type="hidden" maxlength="6" value="<?php echo $this->_tpl_vars['rec']['DYQBH']; ?>
"/>
        </td>
    </tr>
    <tr>
        <th>待验区库位编号</th>
        <td>
        <input type="text" maxlength="25" style="width: 200px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['DYQKWBH']; ?>
"/>
        <input id="DYQKWBH" name="DYQKWBH" type="hidden" maxlength="6" value="<?php echo $this->_tpl_vars['rec']['DYQKWBH']; ?>
"/>
        </td>
    </tr>
    <tr>
        <th>待验区名称</th>
        <td>
        <input id="DYQKWMCH" name="DYQKWMCH" type="text" maxlength="6" style="width: 200px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['DYQKWMCH']; ?>
"/>
        
        </td>
    </tr>
</table>
</form>
<?php if ($this->_tpl_vars['full_page']): ?>
</div>
<div id="tooltip"></div>
</body>
</html>
<?php endif; ?>
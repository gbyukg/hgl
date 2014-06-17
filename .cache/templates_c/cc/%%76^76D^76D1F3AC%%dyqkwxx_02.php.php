<?php /* Smarty version 2.6.26, created on 2011-05-10 13:25:34
         compiled from dyqkwxx_02.php */ ?>
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
/js/dyqkwxx_02.js"></script>

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
<form name="form1" id="form1" style="display: inline; margin: 0px;"><input
	type=hidden id="action" name="action" value="<?php echo $this->_tpl_vars['action']; ?>
" /> <input
	type=hidden id="BGRQ" name="BGRQ" value="<?php echo $this->_tpl_vars['rec']['RQ']; ?>
" />
<table width="100%" cellpadding="0" cellspacing="1" class="form">
	<tr>
		<th width="120px">仓库<span style="color: red">*</span></th>
		<td>
		<?php if ($this->_tpl_vars['action'] == 'update'): ?>
		     <input id="CKMCH" name="CKMCH" type=text style="width: 200px" readonly="readonly" class="readonly" value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
" /> 
		     <input id="CKBH" name="CKBH" type=hidden style="width: 200px" class="editable" value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
" /> 
		<?php else: ?> 
			<input id="CKMCH" name="CKMCH" type="text" style="width: 200px" readonly="readonly" class="readonly" value="--双击选择仓库--" /> 
			<input id="CKBH" name="CKBH" type="hidden" style="width: 200px" class="editable" value="" /> 
		<?php endif; ?>
		</td>
	</tr>
	<tr>
		<th>待验区<span style="color: red">*</span></th>
		<td>
		<?php if ($this->_tpl_vars['action'] == 'update'): ?> 
			<input id="DYQMCH" name="DYQMCH" type="text" style="width: 200px" class="readonly" readonly="readonly" value="<?php echo $this->_tpl_vars['rec']['DYQMCH']; ?>
" />
			<input id="DYQBH" name="DYQBH" type="hidden" style="width: 200px" class="readonly" readonly="readonly" value="<?php echo $this->_tpl_vars['rec']['DYQBH']; ?>
" />
		<?php else: ?>
			<input id="DYQMCH" name="DYQMCH" type="text" style="width: 200px" class="readonly" readonly="readonly" value="--双击选择待验区--" />
			<input id="DYQBH" name="DYQBH" type="hidden" style="width: 200px" class="readonly" readonly="readonly" value="" />
		<?php endif; ?>
		</td>
	</tr>
	<tr>
		<th>待验区库位编号<span style="color: red">*</span></th>
		<td>
		<?php if ($this->_tpl_vars['action'] == 'update'): ?> 
		  <input id="DYQKWBH" name="DYQKWBH" type="text" maxlength="6" style="width: 200px" class="editable" value="<?php echo $this->_tpl_vars['rec']['DYQKWBH']; ?>
" /> 
		<?php else: ?> 
		  <input id="DYQKWBH" name="DYQKWBH" type="text" maxlength="6" style="width: 200px" class="editable" value="" /> 
		<?php endif; ?>
		</td>
	</tr>
	<tr>
		<th>待验区库位名称<span style="color: red">*</span></th>
		<td>
		<?php if ($this->_tpl_vars['action'] == 'update'): ?> 
		  <input id="DYQKWMCH" name="DYQKWMCH" type="text" maxlength="100" style="width: 200px" class="editable" value="<?php echo $this->_tpl_vars['rec']['DYQKWMCH']; ?>
" /> 
		<?php else: ?> 
		  <input id="DYQKWMCH" name="DYQKWMCH" type="text" maxlength="100" style="width: 200px" class="editable" value="" /> 
		<?php endif; ?>
		</td>
	</tr>
</table>
</form>
</div>
</body>
</html>
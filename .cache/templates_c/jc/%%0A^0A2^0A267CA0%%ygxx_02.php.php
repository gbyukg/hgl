<?php /* Smarty version 2.6.26, created on 2011-07-04 15:30:35
         compiled from ygxx_02.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'ygxx_02.php', 60, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/ygxx_02.js"></script>
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
<input type=hidden id="BGRQ" name="BGRQ" value="<?php echo $this->_tpl_vars['rec']['BGRQ']; ?>
" />

<table width="100%" cellpadding="0" cellspacing="1" class="form">
    <tr height="0px">
      <td width="65px"></td>
      <td></td>
    </tr>
    <tr>
        <th>员工编号:<span style="color: red">*</span></th>
        <td>
            <?php if ($this->_tpl_vars['action'] == 'update'): ?>
            <input type=hidden id="YGBH" name="YGBH" value="<?php echo $this->_tpl_vars['rec']['YGBH']; ?>
" />  
            <input type="text" maxlength="8" style="width: 150px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['YGBH']; ?>
"/>
            <?php else: ?>
            <input id="YGBH" name="YGBH" type="text" maxlength="8" style="width: 150px" class="editable"/>
            <?php endif; ?>
            </td>
    </tr>
    <tr>
        <th>员工姓名:<span style="color: red">*</span></th>
        <td><input id="YGXM" name="YGXM" type="text" maxlength="20" style="width: 150px"
            class="editable" value="<?php echo $this->_tpl_vars['rec']['YGXM']; ?>
" /></td>
    </tr>
    <tr>
        <th>助&nbsp;记&nbsp;码:</th>
        <td><input id="ZHJM" name="ZHJM" type="text" maxlength="25" style="width: 150px"
            class="editable"   value="<?php echo $this->_tpl_vars['rec']['ZHJM']; ?>
"/></td>
    </tr>
    <tr>
        <th>所属部门:</th>
        <td><input id="BMMCH" name="BMMCH" type="text" style="width: 150px"  class="editable"  value="<?php echo $this->_tpl_vars['rec']['SSBMMCH']; ?>
"/>
            <input id="BMBH" name="BMBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['SSBM']; ?>
">
            </tr>
    <tr>
        <th>性&nbsp;&nbsp;别:</th>
        <td><select id="XINGBIE" name="XINGBIE" style="width: 150px" >
            <option value="9">请选择</option>
            <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['xingbie_opts'],'selected' => $this->_tpl_vars['rec']['XINGBIE']), $this);?>
 
        </select></td>
    </tr>
    <tr>
        <th>出生日期:</th>
        <td><input id="CHSHRQ" name="CHSHRQ" type="text" style="width: 150px" class="editable"  value="<?php echo $this->_tpl_vars['rec']['CHSHRQ']; ?>
"/></td>
    </tr>
    <tr>
        <th>身份证号:</th>
        <td><input id="SHFZHH" name="SHFZHH" type="text" maxlength="18" style="width: 150px"
            class="editable"  value="<?php echo $this->_tpl_vars['rec']['SHFZHH']; ?>
" /></td>
    </tr>
    <tr>
        <th>固定电话:</th>
        <td><input id="DHHM" name="DHHM" type="text" maxlength="20" style="width: 150px"
            class="editable"  value="<?php echo $this->_tpl_vars['rec']['DHHM']; ?>
"/></td>
    </tr>
    <tr>
        <th>手机号码:</th>
        <td><input id="SHJHM" name="SHJHM" type="text" maxlength="20" style="width: 150px"
            class="editable"  value="<?php echo $this->_tpl_vars['rec']['SHJHM']; ?>
"/></td>
    </tr>
    <tr>
        <th>电子邮件:</th>
        <td><input id="DZYJ" name="DZYJ" type="text" maxlength="100" style="width: 150px"
            class="editable"  value="<?php echo $this->_tpl_vars['rec']['DZYJ']; ?>
" /></td>
    </tr>
    <tr>
        <th>住&nbsp;&nbsp;址:</th>
        <td><input id="ZHZH" name="ZHZH" type="text" maxlength="200" style="width: 400px"
            class="editable"  value="<?php echo $this->_tpl_vars['rec']['ZHZH']; ?>
"/ ></td>
    </tr>
    <tr>
        <th>备&nbsp;&nbsp;注:</th>
        <td><input id="BEIZHU" name="BEIZHU" type="text" maxlength="200" style="width: 400px"
            class="editable"  value="<?php echo $this->_tpl_vars['rec']['BEIZHU']; ?>
" />
            </td>
    </tr>
</table>
</form>
</div>
</body>
</html>
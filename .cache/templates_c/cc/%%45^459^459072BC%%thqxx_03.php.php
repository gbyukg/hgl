<?php /* Smarty version 2.6.26, created on 2011-05-12 09:57:30
         compiled from thqxx_03.php */ ?>
<?php if ($this->_tpl_vars['full_page']): ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/thqxx_03.js"></script>
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
<input id="CKBH" name="CKBH" type="hidden"  value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
">
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="100px">退货区信息</td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="form" border="0">
    <tr height="0px">
      <td width="80px"></td>
      <td></td>
    </tr>
    <tr>
       <th>退货区编号:<span style="color: red">*</span></th>
       <td> 
            <input id="THQBH" name="THQBH" type="text" maxlength="6" style="width: 150px" class="readonly" readonly value="<?php echo $this->_tpl_vars['rec']['THQBH']; ?>
"/>
        
      </td>
    </tr>
    <tr>
       <th>退货区名称:<span style="color: red">*</span></th>
    　　　<td>
             <input id="THQMCH" name="THQMCH" type="text" id="THQMCH" name="THQMCH" maxlength="50" style="width: 150px" class="readonly" readonly value="<?php echo $this->_tpl_vars['rec']['THQMCH']; ?>
"/> 
        </td>
    </tr>
    <tr>
       <th>所属仓库:&nbsp;&nbsp;<span style="color: red">*</span></th>
       <td> <input  type="text" id="CKMCH" name="CKMCH" maxlength="50" style="width: 150px" class="readonly" readonly value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
"/> 
           
       </td> 
    </tr>
   	<tr>
        <th>库区类型:</th>
        <td><input id="KQLX" name="KQLX" type="text" maxlength="30" style="width: 150px"
            class="readonly" readonly  value="<?php echo $this->_tpl_vars['rec']['KQLXMCH']; ?>
"/></td>
    </tr>
</table>
</form>
<?php if ($this->_tpl_vars['full_page']): ?>
</div>
<div id="tooltip"></div>
</body>
</html>
<?php endif; ?>
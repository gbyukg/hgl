<?php /* Smarty version 2.6.26, created on 2011-05-13 17:16:51
         compiled from spsjlr_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/spsjlr_01.js"></script>
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
<table width="100%" cellpadding="0" cellspacing="1" class="form">
   				<input type="hidden" id="CKBH_H" name="CKBH_H" />
	            <input type="hidden" id="KQBH_H" name="KQBH_H" />
	            <input type="hidden" id="BUMEN_H" name="BUMEN_H"  />
	            <input type="hidden" id="YEWUYUAN_H" name="YEWUYUAN_H"  />
	            <input type="hidden" id="BGRQ" name="BGRQ"  />
	            <input type="hidden" id="BGZH" name="BGZH"  />
	            <input type="hidden" id="ZHJEHJ_H" name="ZHJEHJ_H" />
	            <input type="hidden" id="SPJEHJ_H" name="SPJEHJ_H" />
	            <input type="hidden" id="SYJEHJ_H" name="SYJEHJ_H" />
	<tr height="0">
      <td width="75px"></td>
      <td width="125px"></td>
      <td width="75px"></td>
       <td width="125px"></td>
      <td width="75px"></td>
      <td></td>
    </tr>
      <tr>
	     <th>盘点单据号:<span style="color: red">*</span>
		 </th>
	  	<td>
				<input id="DJBH" name="DJBH" size="15" type="text" maxlength="14" style="width: 115px" value="<?php echo $this->_tpl_vars['DJBH']; ?>
" class="editable"/>
		</td>
		<th>仓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;库:</th>
		<td>
				<input id="CKBH" name="CKBH" size="15" type="text" maxlength="70" style="width: 115px" readonly  class="readonly" value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
"/>
		</td>
		<th>库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;区:</th>
		<td>
				<input id="KQBH" name="KQBH" size="15" type="text" maxlength="70" style="width: 115px" readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['KQMCH']; ?>
"/>
		</td>
	  </tr>
	  <tr>
	    <th>部&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;门:<span style="color: red">*</span>
		</th>
		<td>
				<input id="JSHBM" name="JSHBM" size="15" type="text" <?php echo $this->_tpl_vars['disabledbm']; ?>
 maxlength="20" style="width: 115px" class="editable" value=""/>
		</td>
		<th>业&nbsp;&nbsp;务&nbsp;&nbsp;员:<span style="color: red">*</span>
		</th>
		<td>
				<input id="YEWUYUAN" name="YEWUYUAN" size="15" <?php echo $this->_tpl_vars['disableduser']; ?>
 type="text" maxlength="20" style="width: 115px" class="editable" value=""/>
		</td>
	    <th>冻&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;结:
		</th>
		<td >
				<input id="DJBZH" name="DJBZH" size="15" type="checkbox" <?php echo $this->_tpl_vars['check4']; ?>
 <?php echo $this->_tpl_vars['disabled']; ?>
 class="readonly" disabled/>
		</td>
	  </tr>
 </table>
<table width="100%" cellpadding="0" cellspacing="0" class="subtitle" border=0>
    <tr>
        <td width="100px">商品明细</td>
        <td></td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
        <div id="#grid_spsjlr" style="width: 100%; height:170px; background-color: white;"></div>
        </td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
	<tr height="0">
      <td width="75px"></td>
      <td width="125px"></td>
      <td width="75px"></td>
       <td width="125px"></td>
      <td width="75px"></td>
      <td></td>
    </tr>
	<tr>
      <th>账面金额合计:</th>
      <td><span id="ZHJEHJ" class="span_num" style="width:115px"></span></td>
      <th>实盘金额合计:</th>
      <td><span id="SPJEHJ" class="span_num" style="width:115px"></span></td>
      <th>损溢金额合计:</th>
      <td><span id="SYJEHJ" class="span_num" style="width:115px"></span></td>
    </tr> 
</table>
</form>
</div>
</body>
</html>
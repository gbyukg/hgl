<?php /* Smarty version 2.6.26, created on 2011-06-16 11:44:28
         compiled from knshpyd_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/knshpyd_01.js"></script>

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
	<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
		<tr>
			<td width="100px">单据信息</td>
	
		</tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="1" class="form">
	    <tr height="0">
	      <td width="65px"></td>
	      <td width="125px"></td>
	      <td width="65px"></td>
	      <td width="125px"></td>
	      <td width="65px"></td>
	      <td width="125px"></td>
	      <td width="65px"></td>
	      <td></td>
	    </tr>
		<tr>
			<th>开票日期:<span style="color: red">*</span></th>
			<td><input id="KPRQ" name="KPRQ" type="text" style="width:115px"
				class="editable" value="<?php echo $this->_tpl_vars['kprq']; ?>
"/></td>
			<th>单据编号:</th>
			<td><span id="XSHDH" class="span_normal" style="width:115px">--自动生成--</span></td>
			<th>部&nbsp;&nbsp;&nbsp;&nbsp;门:<span style="color: red">*</span></th>
			<td>
			<!--
				<input id="BMMCH" name="BMMCH" type="text"  class="editable" value="销售部"/>
				<input id="BMBH" name="BMBH" type="hidden" value="000001"/>
			-->
							<input id="BMMCH" name="BMMCH" type="text" class="editable" style="width:115px"/>
				<input id="BMBH" name="BMBH" type="hidden"/>
			</td>
			<th>业&nbsp;务&nbsp;员:<span style="color: red">*</span></th>
			<td>
			<!--
				<input id="YWYMCH" name="YWYMCH" type="text"  class="editable" value="王"/>
				<input id="YWYBH" name="YWYBH" type="hidden" value="000020"/>
			-->
				<input id="YWYMCH" name="YWYMCH" type="text" class="editable" style="width:115px"/>
				<input id="YWYBH" name="YWYBH" type="hidden"/>
			</td>
		</tr>
		<tr>
			<th>仓&nbsp;&nbsp;&nbsp;&nbsp;库:<span style="color: red">*</span></th>
			<td>
			<span id="CKMCH" class="span_normal" style="width:115px">请双击选择仓库</span><!--
			<input id="CKMCH" name="CKMCH" type="text" style="width:115px"
				class="readonly" value='请双击选择仓库' readonly='readonly'/>
				--><input id="CKBH" name="CKBH" type="hidden"/>
				<input id="CKDZ" name="CKDZ" type="hidden"/>
				</td>
			<th>调出库位:<span style="color: red">*</span></th>
			<td><span id="DCKW" class="span_normal" style="width:115px">请双击选择调出库位</span><!--
			<input id="DCKW" name="DCKW" type="text" style="width:115px"
				class="readonly" value='请双击选择调出库位' readonly='readonly'/>
				--><input id="DCKQBH" name="DCKQBH" type="hidden"/>
				<input id="DCKWBH" name="DCKWBH" type="hidden"/>
				<input id="DCSHFSHKW" name="DCSHFSHKW" type="hidden"/>
				</td>
			<th>调入库位:<span style="color: red">*</span></th>			
			<td colspan=3><span id="DRKW" class="span_normal" style="width:115px">请双击选择调入库位</span><!--
			<input id="DRKW" name=""DRKW"" type="text" style="width:115px"
				class="readonly" value='请双击选择调入库位' readonly='readonly'/>
				--><input id="DRKQBH" name="DRKQBH" type="hidden"/>
				<input id="DRKWBH" name="DRKWBH" type="hidden"/>
				<input id="DRSHFSHKW" name="DRSHFSHKW" type="hidden"/>
				<input id="DRKQLX" name="DRKQLX" type="hidden"/>
				<input id="DRKQLXM" name="DRKQLXM" type="hidden"/>
				</td>

		</tr>
		<tr>
			<th>备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
			<td colspan=7><input id="BEIZHU" name="BEIZHU" type="text" maxlength="500"
				style="width: 703px" class="editable" /></td>
		
		</tr>
	</table>

	<table width="100%" cellpadding="0" cellspacing="0" class="subtitle">
		<tr>
			<td width="150px">明细信息</td>
			<td>
			<img id="ADDROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_add.gif" onclick="addRow();" /> 
			<img id="DELROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_del.gif" onclick="deleteRow();" />
			</td>
			<td></td>
		</tr>
	</table>

	<table width="100%" cellpadding="0" cellspacing="1" class="grid">
		<tr>
			<td>
			<div id="#grid_mingxi" style="width: 100%; height: 170px; background-color: white;"></div>
				</td>
		</tr>
	</table>

<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
		<tr>
				<td width="150px">当前商品详细信息</td>
		</tr>
	</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
   <tr>
   <td width="60px"></td>
   <td width="200px"></td>
   <td width="60px"></td>
   <td width="200px"></td>
   <td width="60px"></td>
   <td width=""></td>
   </tr>
    <tr>
        <th>通&nbsp;用&nbsp;名:</th>
        <td><span id="TONGYONGMING" class="span_normal" style="width:190px"></span></td>
        <th>产&nbsp;&nbsp;&nbsp;&nbsp;地:</th>
        <td><span id="CHANDI" class="span_normal" style="width:190px"></span></td>
        <th>规&nbsp;&nbsp;&nbsp;&nbsp;格:</th>
        <td><span id="SHPGG" class="span_normal" style="width:190px"></span></td>
    </tr>
    <tr>
        <th>单&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td><span id="BZHDW" class="span_normal" style="width:190px"></span></td>
        <th>数&nbsp;&nbsp;&nbsp;&nbsp;量:</th>
        <td><span id="SHULIANG" class="span_num" style="width:190px"></span></td>
        <th>批&nbsp;&nbsp;&nbsp;&nbsp;号:</th>
        <td><span id="PIHAO" class="span_normal" style="width:190px"></span></td>
    </tr>
    <tr>
        <th>货&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td colspan=5><span id="HWMCH" class="span_normal" style="width:190px"></span></td>
    </tr>

</table>
	<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
		<tr>
			<td width="120px">合计信息</td>
		</tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="1" class="form">
		<tr>
			<th width="60px">数量合计:</th>        
			<td colspan=5>
        <span id="SHULIANG_HEJI" class="span_num" style="width:185px"></td>
		</tr>
	</table>
	</form>
</div>

</body>
</html>
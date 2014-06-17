<?php /* Smarty version 2.6.26, created on 2011-05-27 18:04:23
         compiled from kjdbfkrk_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kjdbfkrk_01.js"></script>

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
			<td>基本信息</td>
	
		</tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="1" class="form">
	<!--<input type=hidden id="BGRQ" name="BGRQ"/> 
		-->
		 <tr height="0">
	      <td width="70px"></td>
	      <td width="125px"></td>
	      <td width="70px"></td>
	      <td width="125px"></td>
	      <td width="80px"></td>
	      <td width="130px"></td>
	      <td width="70px"></td>
	      <td></td>
	    </tr>
		<tr>
			<th>开票日期:</th>
			
			<td>
			<!--<span id="DRCKDZH" class="span_normal" style="width:115px"><?php echo $this->_tpl_vars['kprq']; ?>
</span>-->
			
			<input id="KPRQ" name="KPRQ" type="text" style="width: 115px"
				class="editable" value="<?php echo $this->_tpl_vars['kprq']; ?>
"/>
			</td>
			<th>单据编号:</th>
			<td><span id="XSHDH" class="span_normal" style="width:115px">--自动生成--</span></td>
			<th>部&nbsp;&nbsp;&nbsp;&nbsp;门:<span style="color: red">*</span></th>
			<td>
				<input id="BMMCH" name="BMMCH" type="text" style="width:115px" class="editable"/>
				<input id="BMBH" name="BMBH" type="hidden"/>
			</td>
			<th>业&nbsp;务&nbsp;员:<span style="color: red">*</span></th>
			<td>
				<input id="YWYMCH" name="YWYMCH" type="text" style="width:115px" class="editable"/>
				<input id="YWYBH" name="YWYBH" type="hidden"/>
			</td>

		</tr>
		<tr>
			<th>调拨返库单:</th>
			<td><span id="DYDBFKDS" class="span_normal" style="width:115px"><?php echo $this->_tpl_vars['djbh']; ?>
</span>
			<input id="DYDBFKD" name="DYDBFKD" type="hidden" />
			<!--
			<input id="DYDBFKD" name="DYDBFKD" type="text" value="请双击选择"
				style="width:115px" readonly="readonly" class="readonly" />
			--></td>
			<th>调拨出库单:</th>
			<td><span id="DYDBCHKDS" class="span_normal" style="width:115px"></span>
			<input id="DYDBCHKD" name="DYDBCHKD" type="hidden" />
			<!--
			<input id="DYDBCHKD" name="DYDBCHKD" type="text"
				style="width:115px" readonly="readonly" class="readonly" />
			--></td>
			<th>调出仓库:</th>
			<td><span id="DCHCK" class="span_normal" style="width:115px"></span><!--
			<input id="DCHCK" name="DCHCK" type="text" style="width:115px"
				readonly="readonly" class="readonly" />
			    --><input id="DCHCKBH" name="DCHCKBH" type="hidden"/>
			<th>调入仓库:</th>
			<td><span id="DRCK" class="span_normal" style="width:115px"></span><!--
			<input id="DRCK" name="DRCK" type="text"
				style="width:115px" readonly="readonly" class="readonly" />
				--><input id="DRCKBH" name="DRCKBH" type="hidden"/>
			</td>


		</tr>
		<tr>
			<th>配&nbsp;&nbsp;&nbsp;&nbsp;送:</th>	
			<td><input id="SHFPS" name="SHFPS" type="checkbox" disabled></input><label for="SHFPS" >是否配送</label></td>
			<th>电&nbsp;&nbsp;&nbsp;&nbsp;话:</th>
			<td><span id="DHHMS" class="span_normal" style="width:115px"></span>
			<input id="DHHM" name="DHHM" type="hidden"/>
			<!--
			<input id="DHHM" name="DHHM" type="text" style="width:115px"
				readonly="readonly" class="readonly" />	--></td>
			<th>调入仓库地址:</th>
			<td><span id="DRCKDZHS" class="span_normal" style="width:336px"></span>
			<input id="DRCKDZH" name="DRCKDZH" type="hidden"/>
			<!--
			<input id="DRCKDZH" name="DRCKDZH" type="text"
				style="width: 336px" readonly="readonly" class="readonly" />-->
				</td>
			<th>备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
			<td><input id="BEIZHU" name="BEIZHU" type="text" maxlength="500"
				style="width:115px" class="editable"/></td>

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
		</tr>
	</table>

	<table width="100%" cellpadding="0" cellspacing="1" class="grid">
		<tr>
			<td>
			<div id="#grid_mingxi"
				style="width: 100%; height: 170px; background-color: white;"></div>
			</td>
		</tr>
	</table>

	<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
		<tr>
			<td>当前商品详细信息</td>
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
        <th>药品规格:</th>
        <td><span id="SHPGG" class="span_normal" style="width:190px"></span></td>
	    </tr>
	    <tr>
        <th>单&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td><span id="BZHDW" class="span_normal" style="width:190px"></span></td> 
        <th>数&nbsp;&nbsp;&nbsp;&nbsp;量:</th>
        <td><span id="SHULIANG" class="span_num" style="width:190px"></span></td>
        <th>货&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td><span id="HWMCH" class="span_normal" style="width:190px"></span></td>
	    </tr>
	    <tr>
        <th>批&nbsp;&nbsp;&nbsp;&nbsp;号:</th>
        <td colspan=5><span id="PIHAO" class="span_normal" style="width:190px"></span></td>
	    </tr>
	
	</table>
	<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
		<tr>
			<td>合计信息</td>
		</tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="1" class="form">
		<tr>

			<th width="60px">数量合计:</th>        
			<td colspan=5>
        <span id="SHULIANG_HEJIS" class="span_num" style="width:185px"></td>
        <input id="SHULIANG_HEJI" name="SHULIANG_HEJI" type="hidden" />
		</tr>
	</table>
	</form>
</div>

</body>
</html>

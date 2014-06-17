<?php /* Smarty version 2.6.26, created on 2011-04-06 09:53:46
         compiled from xsth_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">


<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<link rel="stylesheet" type="text/css"
	href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/style.css">


<link rel="stylesheet" type="text/css"
	href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/skins/dhtmlxtoolbar_dhx_skyblue.css"></link>
<script type="text/javascript"
	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript"
	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery.hotkeys-modified.js"></script>
<script type="text/javascript"
	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css"
	href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/jquery.autocomplete.css"></link>
<script type="text/javascript"
	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxcommon.js"></script>
<script type="text/javascript"
	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxtoolbar.js"></script>
<script type="text/javascript"
	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxgrid.js"></script>
<script type="text/javascript"
	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxgridcell.js"></script>
<script type="text/javascript"
	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/excells/dhtmlxgrid_excell_cntr.js"></script>
<script type="text/javascript"
	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_nxml.js "></script>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_splt.js  "></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/common.js"></script>
<script type="text/javascript"
	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/calender/WdatePicker.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/xsth_01.js"></script>


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
<form name="form1" id="form1" style="display: inline; margin: 0px;">
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	<tr>
		<td width="100px">基本信息</td>
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
		<td><input id="KPRQ" name="KPRQ" type="text" style="width: 115px"
			class="editable" value="<?php echo $this->_tpl_vars['kprq']; ?>
" /></td>
		<th>单据编号:</th>
		<td><label id="THDH" style="color: #ccc">-订单保存时自动生成-</label></td>
		<th>增值税格式:</th>
		<td><input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" disabled />
		<label for="SHFZZHSH">是否增值税</label></td>
		<th>销售单号:<span style="color: red">*</span></th>
		<td><input id="XSHDH" name="XSHDH" type="text" style="width: 115px" class="editable" /></td>
	</tr>
	<tr>
		<th>单位编号:</th>
		<td><!-- <label id="DANWEIBH"></label> --><span id="DANWEIBH" class="span_normal" style="width:115px"></span></td>
		<th>单位名称:</th>
		<td colspan="3"><span id="DANWEIMCH" class="span_normal" style="width:115px"></span></td>
		<th>电&nbsp;&nbsp;&nbsp;&nbsp;话:</th>
		<td><label id="DIANHUA"></label></td>
	</tr>

	<tr>
		<th>地&nbsp;&nbsp;&nbsp;&nbsp;址:</th>
		<td colspan="3"><span id="DIZHI" class="span_normal" style="width:115px"></span></td>
		<th>部&nbsp;&nbsp;&nbsp;&nbsp;门:<span style="color: red">*</span></th>
		<td><input id="BMMCH" name="BMMCH" type="text" style="width: 115px"
			class="editable" /> <input id="BMBH" name="BMBH" type="hidden" /></td>
		<th>业&nbsp;务&nbsp;员:<span style="color: red">*</span></th>
		<td><input id="YWYMCH" name="YWYMCH" type="text" style="width: 115PX"
			class="editable" /> <input id="YWYBH" name="YWYBH" type="hidden" /></td>
	</tr>

	<tr>

		<th>备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
		<td colspan="3"><span id="BEIZHU" class="span_normal" style="width:115px"></span></td>
		<th>付款方式:</th>
		<td><select id="FKFSH" name="FKFSH" style="width: 115px" disabled>
			<option value="">--选择付款方式--</option>
			<option value="1">账期结算</option>
			<option value="2">现金</option>
			<option value="3">货到付款</option>
		</select></td>
		<th>扣&nbsp;&nbsp;&nbsp;&nbsp;率:</th>
		<td><span id="KOULV" class="span_normal" style="width:115px; text-align:right"></span></td>
	</tr>

</table>
<table width="100%" cellpadding="0" cellspacing="0" class="subtitle"
	border=0>
	<tr>
		<td width="150px">明细信息</td>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="grid">
	<tr>
		<td>
		<div id="#grid_mingxi" style="width: 100%; height: 160px; background-color: white;"></div>
		</td>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	<tr>
		<td width="150px">当前商品详细信息</td>
		<td></td>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
	<tr height="0">
	<td width="60px"></td>
	<td width="200px"></td>
	<td width="60px"></td>
	<td width="200px"></td>
	<td width="60px"></td>
	<td width=""></td>
	</tr>
	<tr>
		<th>通用名:</th>
		<td><span id="TONGYONGMING" class="span_normal" style="width:190px"></span></td>
		<th>产地:</th>
		<td><span id="CHANDI" class="span_normal" style="width:190px"></span></td>
		<th>规格:</th>
		<td><span id="SHPGG" class="span_normal" style="width:190px"></span></td>
	</tr>
	<tr>
		<th>单位:</th>
		<td><span  class="span_normal" style="width:190px" id="BZHDW"></span></td>
		<th>数量:</th>
		<td><span  class="span_num" style="width:190px" id="SHULIANG"></span></td>
		<th>单价:</th>
		<td><span  class="span_num" style="width:190px" id="DANJIA"></span></td>
	</tr>
	</tr>
	<tr>
		<th>生产日期:</th>
		<td><span  class="span_normal" style="width:190px" id="SHCHRQ"></span></td>
		<th>批号:</th>
		<td><span  class="span_normal" style="width:190px" id="PIHAO"></span></td>
		<th>金额:</th>
		<td><span  class="span_num" style="width:190px" id="JINE"></span></td>
	</tr>
	</tr>

</table>
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	<tr>
		<td width="150px">合计信息</td>
		<td></td>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
	<tr>
		<th width="60">数量合计:</th>
		<td width="170"><span  class="span_num" style="width:165px" id="SHLHJ"></span></td>
		<th width="115">不含税金额合计:</th>
		<td width="170"><span  class="span_num" style="width:165px" id="BHSHJEHJ"></span></td>
		<th width="60">税额合计:</th>
		<td width="170"><span  class="span_num" style="width:165px" id="SHEHJ"></span></td>
	</tr>
	</tr>
	<tr>
		<th width="60">金额合计:</th>
		<td width="170"><span  class="span_num" style="width:165px" id="JEHJ"></span></td>
		<th width="115">金额合计（大写）:</th>
		<td colspan="3"><span  class="span_num" style="width:100%" id="JEHJDX"></span></td>

	</tr>
	</tr>

</table>
</form>
</div>
<div id="loading"
	style="text-align: left; Z-INDEX: 1; position: absolute; left: 200px; top: 100px; FILTER: Alpha(opacity = 80); background-color: #FFF; border: 1px dashed #999999; width: 50px; padding: 10px; display: none">
<img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle"></div>

</body>
</html>
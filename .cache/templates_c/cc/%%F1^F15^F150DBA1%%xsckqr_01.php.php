<?php /* Smarty version 2.6.26, created on 2011-06-02 17:59:51
         compiled from xsckqr_01.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'xsckqr_01.php', 116, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/skins/dhtmlxtoolbar_dhx_skyblue.css"></link>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery.hotkeys-modified.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/jquery.autocomplete.css"></link>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxcommon.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxtoolbar.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxgrid.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxgridcell.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/excells/dhtmlxgrid_excell_cntr.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_validation.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_nxml.js "></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_splt.js  "></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/common.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/calender/WdatePicker.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/xsckqr_01.js"></script>

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
        <td width="100px">基本信息</td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="form">
	<tr height="0">
      <td width="65px"></td>
      <td width="125px"></td>
      <td width="65px"></td>
      <td width="125px"></td>
      <td width="80px"></td>
      <td width="125px"></td>
      <td width="65px"></td>
      <td></td>
    </tr>
    <tr>
        <th width="100px">开票日期:<span style="color: red">*</span></th>
        <td width="200px">
        	<input id="KPRQ" name="KPRQ" type="text" style="width: 115px" class="editable" value="<?php echo $this->_tpl_vars['kprq']; ?>
" />
        </td>
        <th width="100px">单据编号:</th>
        <td width="200px">
        	<label id="XSHDBH"  style="color:#ccc; width:115px">--保存时自动生成--</label>
        </td>
        <th width="100px">销售订单:<span style="color: red">*</span></th>
        <td width="200px">
        	<input id="XSD" name="XSD" type="text" style="width: 115px" class="editable" />
        </td>
        <th width="100px">增&nbsp;值&nbsp;税:</th>
        <td>
            <input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" disabled><label for="SHFZZHSH" >是否增值税</label></input> 
        </td>
    </tr>
    <tr>
        <th width="100px">单位编号:</th>
        <td width="200px">
        	<input id="DWBH" name="DWBH" type="text" maxlength="8" style="width: 115px" readonly class="readonly" />
        <th width="100px">单位名称:</th>
        <td colspan="3">
        	<input id="DWMCH" name="DWMCH" type="text" maxlength="100" style="width: 326px" readonly class="readonly" />
        </td>
        <th width="100px">电&nbsp;&nbsp;&nbsp;&nbsp;话:</th>
        <td width="200px">
        	<input id="DHHM" name="DHHM" type="text" maxlength="25" style="width: 115px" readonly class="readonly" />
        </td>
    </tr>
    <tr>
        <th width="100px">地&nbsp;&nbsp;&nbsp;&nbsp;址:</th>
        <td colspan="3">
        	<input id="DIZHI" name="DIZHI" type="text" maxlength="100" style="width: 305px" readonly class="readonly" />
        </td>
        <th width="100px">部&nbsp;&nbsp;&nbsp;&nbsp;门:<span style="color: red">*</span></th>
        <td width="200px">
        	<input id= "BMBH" name="BMBH" type="hidden" />
        	<input id="BMMCH" name="BMMCH" type="text" style="width: 115px" class="editable" value=""/>
        </td>
        <th width="100px">业&nbsp;务&nbsp;员:<span style="color: red">*</span></th>
        <td>
        	<input id= "YWYBH" name="YWYBH" type="hidden" />
        	<input id="YWYMCH" name="YWYMCH" type="text" style="width: 115px" class="editable" value=""/>
        </td>
    </tr>
    <tr>
    	<th width="100px">付款方式:</th>
        <td>
            <select id="FKFSH" name="FKFSH" style="width: 115px" disabled>
	            	 <option value="">--选择付款方式--</option>
		       		 <option value="1">账期结算</option>
		       		 <option value="2">现金</option>
		       		 <option value="3">货到付款</option>
			</select>
        </td>
        <th width="100px">发&nbsp;货&nbsp;区:</th>
        <td width="200px">
        	<select id="FAHUOQU" name="FAHUOQU" style="width: 115px">
       			 <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['fahuoqu']), $this);?>
					
			</select>
        </td>
        <th width="100px">是否配送:</th>
        <td width="200px">
            <input id="SHFPS" name="SHFPS" type="checkbox" disabled><label for="SHFPS">配&nbsp;&nbsp;&nbsp;&nbsp;送</label></input> 
        </td>
        <th width="100px">扣&nbsp;&nbsp;&nbsp;&nbsp;率:</th>
        <td width="200px">
        	<input id="KOULV" name="KOULV" type="text" maxlength="5" style="width: 115px" readonly class="readonly" />
        </td>
    </tr>
    <tr>
        <th width="100px">备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
        <td  colspan="7">
        	<input id="BEIZHU" name="BEIZHU" type="text" maxlength="500" style="width: 718px" class="editable" />
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" class="subtitle" border=0>
    <tr>
        <td width="200px">明细信息</td> 
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
        <div id="#grid_mingxi" style="width: 100%; height:160px; background-color: white;"></div>
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
    <tr>
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
        <th>产&nbsp;&nbsp;地:</th>
        <td><span id="CHANDI" class="span_normal" style="width:190px"></span></td>
        <th>规&nbsp;&nbsp;格:</th>
        <td><span id="SHPGG" class="span_normal" style="width:190px"></span></td>
    </tr>
    <tr>
        <th>单&nbsp;&nbsp;位:</th>
        <td><span id="BZHDW" class="span_normal" style="width:190px"></span></td>
        <th>数&nbsp;&nbsp;量:</th>
        <td><span id="SHULIANG" class="span_normal" style="width:190px"></span></td>
        <th>单&nbsp;&nbsp;价:</th>
        <td><span id="DANJIA" class="span_normal" style="width:190px"></span></td>
    </tr>
    <tr>
        <th>货&nbsp;&nbsp;位:</th>
        <td><span id="HWMCH" class="span_normal" style="width:190px"></span></td>
        <th>批&nbsp;&nbsp;号:</th>
        <td><span id="PIHAO" class="span_normal" style="width:190px"></span></td>
        <th>金&nbsp;&nbsp;额:</th>
        <td><span id="JINE" class="span_normal" style="width:190px"></span></td>
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
        <th width="90px">数量合计:</th>
        <td width="120px"><span id="SHULIANGHJ" class="span_num" style="width:110px"></span></td>
        <th width="110px">金额合计:</th>
        <td width="120px"><span id="BHSHJEHJ" class="span_num" style="width:110px"></span></td>
        <th width="90px">税额合计:</th>
        <td width="120px"><span id="SHEHJ" class="span_num" style="width:110px"></span></td>
    </tr>
    <tr>
        <th width="90px">含税金额合计:</th>
        <td width="120px"><span id="JEHJ" class="span_num" style="width:110px"></span></td>
        <th width="110px">含税金额合计(大写):</th>
        <td colspan="3"><span id="JEHJDX" class="span_num" style="width:380px"></span></td>
    </tr>
</table>

</form>
</div>

<div id="loading" style="text-align: left;Z-INDEX: 1;position:absolute;left:200px;top:280px;FILTER:Alpha(opacity=80);background-color: #FFF;border: 1px dashed #999999;width:50px;padding:10px;display:none">
     <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
</div>

</body>
</html>
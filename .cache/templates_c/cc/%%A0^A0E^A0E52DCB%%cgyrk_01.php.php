<?php /* Smarty version 2.6.26, created on 2011-06-21 13:57:57
         compiled from cgyrk_01.php */ ?>
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
/js/cgyrk_01.js"></script>
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
			<th>
			     开票日期:<span style="color: red">*</span>
			</th>
			<td>
			     <input id="KPRQ" name="KPRQ" type="text" style="width: 115px" class="editable" value="<?php echo $this->_tpl_vars['kprq']; ?>
"/>
			</td>
			<th>单据编号:</th>
			<td>
			     <span id="DJBH" name="DJBH" class="span_normal" style="width:115px">--自动生成--</span>
			</td>
			<th>部&nbsp;&nbsp;&nbsp;&nbsp;门:<span style="color: red">*</span></th>
            <td>
                <span id="BMMCH" name="BMMCH" class="span_normal" ><?php echo $this->_tpl_vars['bmmch']; ?>
</span>
                <input id="BMBH" name="BMBH" type="hidden" value="<?php echo $this->_tpl_vars['bmbh']; ?>
"/>
            </td>
			<th>采购单号:</th>
			<td>
			     <span id="CGDBH" name="CGDBH" class="span_normal" style="width:115px">请双击选择</span>
			     <input id="FKFSH" name="FKFSH" type="hidden"/>
			</td>
		</tr>
		<tr>
			<th>单位编号:</th>
			<td>
			     <span id="DWBH" name="DWBH" class="span_normal" style="width:115px"></span>
			</td>
			<th>单位名称:</th>
			<td colspan="3">
			     <span id="DWMCH" name="DWMCH" class="span_normal" style="width:311px"></span>
			</td>
			</td>
			<th>电&nbsp;&nbsp;&nbsp;&nbsp;话:</th>
			<td>
			     <span id="DHHM" name="DHHM" class="span_normal" style="width:115px"></span>
			</td>
		</tr>
		<tr>
			<th>地&nbsp;&nbsp;&nbsp;&nbsp;址:</th>
			<td colspan="5">
			     <span id="DIZHI" name="DIZHI" class="span_normal" style="width:115px"></span>
			</td>
			<th>扣&nbsp;&nbsp;&nbsp;&nbsp;率:</th>
            <td>
                <span id="KOULV" name="KOULV" class="span_num" style="width:115px"></span>
            </td>
		</tr>
		<tr>
            <th>送货清单</th>
            <td>
                <input id="SHQD" name="SHQD" type="text" style="width: 115px" class="editable"/>
            </td>
            <th>发票编号</th>
            <td>
                <input id="FPBH" name="FPBH" type="text" style="width: 115px" class="editable"/>
            </td>
            <th>业&nbsp;务&nbsp;员:<span style="color: red">*</span></th>
            <td>
                <input id="YWYMCH" name="YWYMCH" type="text" style="width: 115px" class="editable"/>
                <input id="YWYBH" name="YWYBH" type="hidden"/>
            </td>
            <th>增&nbsp;值&nbsp;税:</th>
            <td>
                 <input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" disabled>是否增值税
            </input>
            </td>
		</tr>
		<tr>
		  <th>备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
                <td colspan="7"><input id="BEIZHU" name="BEIZHU" maxlength="500" type="text"  style="width: 703px" class="editable" />
          </td>
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
			<div id="#grid_mingxi" style="width: 100%; height: 170px; background-color: white;"></div>
			</td>
		</tr>
	</table>

	<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
		<tr>
			<td width="120px">当前商品详细信息</td>
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
        <th>单&nbsp;&nbsp;&nbsp;&nbsp;价:</th>
        <td><span id="DANJIA" class="span_num" style="width:190px"></span></td> 
    </tr>
    <tr>
        <th>货&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td><span id="HWMCH" class="span_normal" style="width:190px"></span></td>
        <th>批&nbsp;&nbsp;&nbsp;&nbsp;号:</th>
        <td><span id="PIHAO" class="span_normal" style="width:190px"></span></td>
        <th>金&nbsp;&nbsp;&nbsp;&nbsp;额:</th>
        <td><span id="JINE" class="span_num" style="width:190px"></span></td>
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
			<td width="170px"><span id="SHULIANG_HEJI" class="span_num" style="width:165px">
				</span></td>
			<th width="115px">含税金额合计:</th>
        	<td width="170px"><span id="HANSHUIJINE_HEJI" class="span_num" style="width:165px">
        		</span></td>			
			<th width="60px">税额合计:</th>
        	<td width=""><span id="SHUIE_HEJI" class="span_num" style="width:185px">
        		</span></td>
		</tr>
		<tr>
			<th>金额合计:</th>
        	<td><span id="JINE_HEJI" class="span_num" style="width:165px">
        		</span></td>
            <th>含税金额合计(大写):</th>
        	<td colspan="3"><span id="JINE_HEJI_CAP" class="span_num" style="width:425px">
        		</span></td>
		</tr>
	</table>
	</form>
</div>
</body>
</html>
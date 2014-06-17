<?php /* Smarty version 2.6.26, created on 2011-06-02 17:59:53
         compiled from xsybwc_01.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'xsybwc_01.php', 111, false),)), $this); ?>
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
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/skins/dhtmlxmenu_dhx_skyblue.css">

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
/codebase/ext/dhtmlxgrid_nxml.js "></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxmenu.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxmenu_ext.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/excells/dhtmlxgrid_excell_link.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_splt.js  "></script>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/common.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/calender/WdatePicker.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/xsybwc_01.js"></script>

</head>
<body>
<div id="top">
<table width="1450px" cellpadding="0" cellspacing="0">
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
<table width="1450px" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="100px">基本信息</td>
    </tr>
</table>

<table width="1450px" cellpadding="0" cellspacing="1" class="form">
	<tr>
			<td width="150px"></td>
			<td width="280px"></td>
			<td width="150px"></td>
			<td width="280px"></td>
			<td width="150px"></td>
			<td></td>
	</tr>
    <tr>
        <th>开票日期<span style="color: red">*</span></th>
        <td><input id="KPRQ" name="KPRQ" type="text" style="width:220px"  class="editable" value="<?php echo $this->_tpl_vars['kprq']; ?>
" /></td>
        <th>单据编号</th>
        <td><label id="XSHDH" style="color:#ccc">--订单保存时自动生成--</label></td>
        <th>增值税格式</th>
        <td><input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" /><label for="SHFZZHSH">是否增值税</label></td>
    </tr>
    <tr>
        <th>销售部门<span style="color: red">*</span></th>
        <td>
        <input id="XSBMMCH" name="XSBMMCH" type="text" style="width:220px"  class="editable" />
        <input id="XSBMBH" name="XSBMBH" type="hidden" />
        </td>
        <th>销售开票员</th>
        <td><label id="XSKPYXM">魏峰</label><hidden id="XSKPYBH"></td>
        <th>销售业务员<span style="color: red">*</span></th>
        <td>
        <input id="XSYWYMCH" name="XSYWYMCH" type="text" style="width:220px"  class="editable" />
        <input id="XSYWYBH" name="XSYWYBH" type="hidden" />       </td>

    </tr>
        <tr>
        <th>仓储部门<span style="color: red">*</span></th>
        <td>
        <input id="CCBMMCH" name="CCBMMCH" type="text" style="width:220px"  class="editable" />
        <input id="CCBMBH" name="CCBMBH" type="hidden" />
        </td>
        <th>仓储开票员</th>
        <td><label id="CCKPYXM">魏峰</label><hidden id="CCKPYBH"></td>
        <th>仓储业务员<span style="color: red">*</span></th>
        <td>
        <input id="CCYWYMCH" name="CCYWYMCH" type="text" style="width:220px"  class="editable" />
        <input id="CCYWYBH" name="CCYWYBH" type="hidden" /></td>

    </tr>
    <tr>
        <th>单位编号<span style="color: red">*</span></th>
        <td><input id="DWBH" name="DWBH" type="text" maxlength="8" style="width:220px" class="editable"/>
        <th>单位名称</th>
        <td colspan="3"><label id="DWMCH"></label></td>
    </tr>
    <tr>
        <th>电话</th>
        <td><input id="DHHM" name="DHHM" type="text" maxlength="25" style="width:220px" class="editable"/></td>
        <th>地址</th>
        <td colspan="3"><input id="DIZHI" name="DIZHI" type="text" maxlength="100" style="width:666px" class="editable" /></td>
    </tr>
    <tr>
        <th>发货区<span style="color: red">*</span></th>
        <td><select id="FAHUOQU" name="FAHUOQU" style="width:220px">
                      <option value="0">--选择发货区--</option>
       					 <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['fahuoqu']), $this);?>
					
			</select>
        </select></td>
        <th>扣率</th>
        <td><input id="KOULV" name="KOULV" type="text" maxlength="5" style="width:220px" class="editable_num"/></td>
        <th>付款方式<span style="color: red">*</span></th>
        <td>
            <select id="FKFSH" name="FKFSH" style="width: 55%">
       		 <option value="0">--选择付款方式--</option>
       		 <option value="1">账期结算</option>
       		 <option value="2">现金</option>
       		 <option value="3">货到付款</option>			 				
			</select>
        </select></td>
    </tr>   
    <tr>
        <th><label for="SHFPS">是否配送</label></th>
        <td><input type="checkbox" id="SHFPS" name="SHFPS"></td>
        <th>备注</th>
        <td colspan="3"><input id="BEIZHU" name="BEIZHU" type="text" maxlength="10" style="width:666px"
            class="editable" /></td>
    </tr>
</table>
<table width="1450px" cellpadding="0" cellspacing="0" class="subtitle" border=0>
    <tr>
        <td width="150px">明细信息</td>
        <td><img id="ADDROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_add.gif" onclick="addRow();" /> <img
            id="DELROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_del.gif" onclick="deleteRow();" /></td>
    </tr>
    <tr>
        <td></td>
    </tr>
</table>

<table width="1450px" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
        <div id="#grid_mingxi"
            style="width: 100%; height:160px; background-color: white; overflow: auto"></div>
        </td>
    </tr>
</table>
<table width="1450px" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="150px">当前商品详细信息</td>
        <td></td>
    </tr>
</table>
<table width="1450px" cellpadding="0" cellspacing="1" class="form">
	<tr>
			<td width="150px"></td>
			<td width="280px"></td>
			<td width="150px"></td>
			<td width="280px"></td>
			<td width="150px"></td>
			<td></td>
	</tr>
    <tr>
        <th>通用名</th>
        <td><label id="TONGYONGMING" ></label></td>
        <th>产地</th>
        <td><label id="CHANDI"></label></td>
        <th>规格</th>
        <td><label id="SHPGG"></label></td>
    </tr>
    <tr>
        <th>单位</th>
        <td><label id="BZHDW" ></label></td>
        <th>数量</th>
        <td><label id="SHULIANG"></label></td>
        <th>单价</th>
        <td><label id="DANJIA"></label></td>
    </tr>
    </tr>
    <tr>
        <th>货位</th>
        <td><label id="HWMCH"></label></td>
        <th>批号</th>
        <td><label id="PIHAO"></label></td>
        <th>金额</th>
        <td><label id="JINE"></label></td>
    </tr>
    </tr>

</table>
	<table width="1450px" cellpadding="0" cellspacing="1" class="subtitle">
		<tr>
			<td width="120px">合计信息</td>
		</tr>
	</table>
	<table width="1450px" cellpadding="0" cellspacing="1" class="form">
		<tr>
				<td width="150px"></td>
				<td width="280px"></td>
				<td width="150px"></td>
				<td width="280px"></td>
				<td width="150px"></td>
				<td></td>
		</tr>	
		<tr>
			<th>数量合计</th>
			<td><label id="SHULIANG_HEJI" style="width: 92%"></label></td>
			<th>不含税金额合计</th>
			<td><label id="HANSHUIJINE_HEJI" style="width: 92%"></label></td>
			<th>税额合计</th>
			<td><label id="SHUIE_HEJI" style="width: 92%"></label></td>
		</tr>
		<tr>
			<th>金额合计</th>
			<td><label id="JINE_HEJI" style="width: 92%"></label></td>
			<th>金额合计（大写）</th>
			<td colspan="3"><label id="JINE_HEJI_CAP" style="width: 92%"></label></td>
		</tr>
	</table>
</div>

</form>
</div>
<div id="loading" style="text-align: left;Z-INDEX: 1;position:absolute;left:200px;top:100px;FILTER:Alpha(opacity=80);background-color: #FFF;border: 1px dashed #999999;width:50px;padding:10px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
        </div>

</body>
</html>
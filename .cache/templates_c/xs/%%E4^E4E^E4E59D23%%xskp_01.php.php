<?php /* Smarty version 2.6.26, created on 2011-05-26 15:57:20
         compiled from xskp_01.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'xskp_01.php', 86, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/xskp_01.js"></script>
</head>
<body>
<div id="top">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td class="title" style="width:120px"><?php echo $this->_tpl_vars['title']; ?>
</td>
        <td>
        <div id="toolbar"></div>
        </td>
    </tr>
</table>
</div>
<div id="vspace"></div>
<div id="body">
<form name="xskp" id="xskp" style="display:inline;margin:0px;">
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="100px">基本信息</td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form" border="0">
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
        <td><input id="KPRQ" name="KPRQ" type="text" style="width: 115px"  maxlength=10 class="editable" value="<?php echo $this->_tpl_vars['kprq']; ?>
"/></td>
        <th>单据编号:</th>
        <td><span id="XSHDBH" class="span_normal" style="width:115px">--自动生成--</span></td>
        <th>开&nbsp;票&nbsp;员:</th>
        <td><span id="KPYMCH" class="span_normal" style="width:115px"><?php echo $this->_tpl_vars['kpymch']; ?>
</span></td>
        <th>部&nbsp;&nbsp;&nbsp;&nbsp;门:<span style="color: red">*</span></th>
        <td><span id="BMMCH" class="span_normal" style="width:115px"><?php echo $this->_tpl_vars['bmmch']; ?>
</span>
	    </td>
  
    </tr>
    <tr>
        <th>单位编号:<span style="color: red">*</span></th>
        <td>
            <input id="DWBH" name="DWBH" type="text" maxlength="8" style="width: 115px" class="editable" />
        </td>
        <th>单位名称:</th>
        <td colspan="3">
        <span id="DWMCH" class="span_normal" style="width:310px"></span></td>
        
        <th>电&nbsp;&nbsp;&nbsp;&nbsp;话:</th>
        <td><input id="DHHM" name="DHHM" type="text" maxlength="25" style="width: 115px" class="editable" /></td>
    </tr>
    <tr>
        <th>地&nbsp;&nbsp;&nbsp;&nbsp;址:</th>
        <td colspan="3"><input id="DIZHI" name="DIZHI" type="text" maxlength="200" style="width: 305px" class="editable" /></td>
        <th>增&nbsp;值&nbsp;税:</th>
        <td><input id="SHFZZHSH" name="SHFZZHSH" type="checkbox"  value="1"/><label for="SHFZZHSH" >增值税格式</label></td>
 
        <th>业&nbsp;务&nbsp;员:<span style="color: red">*</span></th>
        <td>
           <input id="YWYMCH" name="YWYMCH" type="text" style="width: 115px"  class="editable" />
           <input id="YWYBH" name="YWYBH" type="hidden" />
        </td>
    </tr>
    <tr>
        <th>付款方式:<span style="color: red">*</span></th>
        <td>
            <select id="FKFSH" name="FKFSH" style="width: 115px" >
       		 <option value="0">--付款方式--</option>
       		 <option value="1">1:账期付款</option>
       		 <option value="2">2:现金结算</option>
       		 <option value="3">3:货到付款</option>	 
			</select>
        </select></td>
        <th>发&nbsp;货&nbsp;区:<span style="color: red">*</span></th>
        <td><select id="FAHUOQU" name="FAHUOQU" style="width: 115px">
                      <option value="0">--发货区--</option>
       					 <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['fahuoqu']), $this);?>
					
			</select>
        </select></td>
        <th>配&nbsp;&nbsp;&nbsp;&nbsp;送:</th>
        <td><input type="checkbox" id="SHFPS" name="SHFPS" checked="checked" value='1'/><label for="SHFPS">要配送</label></td>
        <th>扣&nbsp;&nbsp;&nbsp;&nbsp;率:</th>
        <td><input id="KOULV" name="KOULV" type="text" maxlength="5" style="width: 115px" class="editable_num"/></td>
   </tr>
   <tr>
        <th>备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
        <td colspan="7"><input id="BEIZHU" name="BEIZHU" type="text" maxlength="200" style="width: 703px"
            class="editable" /></td>
   </tr>

</table>
<table width="100%" cellpadding="0" cellspacing="0" class="subtitle" >
    <tr>
        <td width="100px">明细信息</td>
        <td width="200px"><img id="ADDROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_add.gif" onclick="addRow();" /> <img
            id="DELROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_del.gif" onclick="deleteRow();" /></td>
        <td></td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
        <div id="#grid_mingxi" style="width: 100%; height:170px;background-color: white;"></div>
        </td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="150px">当前商品详细信息</td>
    </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="1" class="form">
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
        <th>单&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td><span id="BZHDWMCH" class="span_normal" style="width:190px"></span></td>  
        <th>单&nbsp;&nbsp;&nbsp;&nbsp;价:</th>
        <td><span id="DANJIA" class="span_num" style="width:190px"></span></td>      
    </tr>
    <tr>
        <th>产&nbsp;&nbsp;&nbsp;&nbsp;地:</th>
        <td><span id="CHANDI" class="span_normal" style="width:190px"></span></td>       
        <th>批&nbsp;&nbsp;&nbsp;&nbsp;号:</th>
        <td><span id="PIHAO" class="span_normal" style="width:190px"></span></td>
        <th>数&nbsp;&nbsp;&nbsp;&nbsp;量:</th>
        <td><span id="SHULIANG" class="span_num" style="width:190px"></span></td>
    </tr>
    <tr>
        <th>药品规格:</th>
        <td><span id="SHPGG" class="span_normal" style="width:190px"></span></td>
        <th>计量规格:</th>
        <td><span id="JLGG" class="span_normal" style="width:190px"></span></td>
        <th>金&nbsp;&nbsp;&nbsp;&nbsp;额:</th>
        <td><span id="JINE" class="span_num" style="width:190px"></span></td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="150px">合计信息</td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
    <tr>
        <th width="60px">数量合计:</th>
        <td width="170px">
        <span id="SUM_SHULIANG" class="span_num" style="width:165px"></td>
        <th width="115px">含税金额合计:</th>
        <td width="170px"><span id="SUM_HSHJE" class="span_num" style="width:165px"></span></td>
        <th width="60px">税额合计:</th>
        <td width=""><span id="SUM_SHUIE" class="span_num" style="width:185px"></span></td>
       
    </tr>
    <tr>
        <th>金额合计:</th>
        <td><span id="SUM_JINE" class="span_num" style="width:165px"></span></td>
        <th>含税金额合计(大写):</th>
        <td colspan="3"><span id="UPPER_SUM_HSHJE" class="span_num" style="width:425px"></span></td>
             
    </tr>
</table>
</form>
</div>
<div id="loading" style="text-align: left;Z-INDEX: 1;position:absolute;left:200px;top:100px;FILTER:Alpha(opacity=80);background-color: #FFF;border: 1px dashed #999999;width:50px;padding:10px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
</div>
</body>
</html>
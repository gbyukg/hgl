<?php /* Smarty version 2.6.26, created on 2011-05-06 13:10:37
         compiled from zjrk_01.php */ ?>
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
/js/zjrk_01.js"></script>

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
<input  type="hidden" id="ERRORMEG" name="ERRORMEG">
<input  type="hidden" id="ERROR" name="ERROR">
<table width="100%" cellpadding="0" cellspacing="1" class="form" border="0">
    <tr height="0">
      <td width="65px"></td>
      <td width="125px"></td>
      <td width="85px"></td>
      <td width="125px"></td>
      <td width="75px"></td>
      <td width="125px"></td>
      <td width="75px"></td>
      <td></td>
    </tr>
    <tr>
        <th >开票日期:<span style="color: red">*</span></th>
        <td ><input id="KPRQ" name="KPRQ" type="text" style="width: 115PX"  value = <?php echo $this->_tpl_vars['kprq']; ?>
 class="editable"  /></td>
        <th >单据编号:</th>
        <td ><label id="XSHDH" style="color:#ccc">--自动生成--</label></td>
        <th >增值税:</th>
         <td >    
        <?php if ($this->_tpl_vars['rec']['SHFZZHSH'] == '1'): ?>
              <input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" checked="checked">是否增值税
                <?php else: ?>
                <input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" >是否增值税
                <?php endif; ?></td>
     <td colspan="2"></td>
    </tr>
    <tr>
           
        
         <input type="hidden" id="SHPZHT" name="SHPZHT"><!--  审批状态  	-->
		<th >单位编号:<span style="color: red">*</span></th>
        <td ><input id="DWBH" name="DWBH" type="text" maxlength="8" style="width: 115PX" class="editable" 
             /><input id="DWBH_HIDDEN" name="DWBH_HIDDEN" type="hidden" /></td>
        <th >单位名称:</th>
        <td ><span id="DWMCH" name="DWMCH" type="text" style="width: 115PX"  class="span_normal" disabled ></span></td>
   		 <td colspan="2"></td>
   		<th >电&nbsp;&nbsp;&nbsp;&nbsp;话:</th>
        <td ><input id="DHHM" name="DHHM" type="text" maxlength="25" style="width: 115PX" class="editable"  /></td>
    </tr>
    <tr>
        
    </tr>
    <tr>
    	
    
    
        
        <th>地&nbsp;&nbsp;&nbsp;&nbsp;址:</th>
        <td colspan="3"><input id="DIZHI" name="DIZHI" type="text" maxlength="100" style="width: 331PX" class="editable"  /></td>
       
   		     <th >扣&nbsp;&nbsp;&nbsp;&nbsp;率:</th>
        <td ><input id="KOULV" name="KOULV" type="text" maxlength="6" style="width: 115PX" class="editable" />
        <th >备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
        <td ><input id="BEIZHU" name="BEIZHU" type="text" maxlength="500"  style="width: 115PX"  class="editable" /></td>
   </td>
    </tr>
	
    <tr>
    <th >仓库部门:<span style="color: red">*</span></th>
        <td ><input id="CANGKU" name="CANGKU" type="text" maxlength="6" style="width: 115PX" class="editable" />
         <input id="CANGKUBH" name="CANGKUBH" type="hidden" /></td>
        <th >仓库业务员:<span style="color: red">*</span></th>
        <td ><input id="YWYN" name="YWYN" type="text" maxlength="10" style="width: 115PX"  class="editable" />
         <input id="YWYNBH" name="YWYNBH" type="hidden" />
        </td>
          <th >采购部门:<span style="color: red">*</span></th>
        <td >
        <input id="BMMCH" name="BMMCH" type="text" style="width: 115PX"  class="editable" />
        <input id="BMBH" name="BMBH" type="hidden" />
        </td> 
             <th >采购业务员:<span style="color: red">*</span></th>
        <td>
        <input id="YWYMCH" name="YWYMCH" type="text" style="width: 115PX"  class="editable"  />
        <input id="YWYBH" name="YWYBH" type="hidden" />        </td>
    </tr>
    

</table>
<table width="100%" cellpadding="0" cellspacing="0" class="subtitle" border=0>
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

<table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
        <div id="#grid_mingxi"style="width: 100%; height:160px; background-color: white; "></div>

        </td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="150px">当前商品详细信息</td>
        <td></td>
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
        <th >通&nbsp;用&nbsp;名:</th>
        <td ><span id="TONGYONGMING" class="span_normal" style="width:190px" ></span></td>
        <th >产&nbsp;&nbsp;&nbsp;&nbsp;地:</th>
        <td ><span id="CHANDI" class="span_normal" style="width:190px"></span></td>
        <th >规&nbsp;&nbsp;&nbsp;&nbsp;格:</th>
        <td ><span id="SHPGG" class="span_normal" style="width:190px"></span></td>
    </tr>
    <tr>
        <th >单&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td ><span id="BZHDW" class="span_normal" style="width:190px"></span></td>
        <th >数&nbsp;&nbsp;&nbsp;&nbsp;量:</th>
        <td ><span id="SHULIANG" class="span_normal" style="width:190px"></span></td>
        <th >单&nbsp;&nbsp;&nbsp;&nbsp;价:</th>
        <td ><span id="DANJIA" class="span_normal" style="width:190px"></span></td>
    </tr>
    <tr>
        <th >库&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td ><span id="HWMCH" class="span_normal" style="width:190px"></span></td>
        <th >批&nbsp;&nbsp;&nbsp;&nbsp;号:</th>
        <td ><span id="PIHAO" class="span_normal" style="width:190px"></span></td>
        <th >金&nbsp;&nbsp;&nbsp;&nbsp;额:</th>
        <td ><span id="JINE" class="span_normal" style="width:190px"></span></td>
    </tr>




</table>
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="150px">单据汇总</td>
        <td></td>
    </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="1" class="form">
   <tr>
   <td width="60px"></td>
   <td width="200px"></td>
   <td width="120px"></td>
   <td width="140px"></td>
   <td width="60px"></td>
   <td width=""></td>
   </tr>
    <tr>
        <th >数量合计:</th>
        <td ><span id="SHULIANG_1" class="span_num" style="width:190px"></span></td>
        <th >含税金额合计:</th>
        <td ><span id="HANSUI_1" class="span_num" style="width:190px"></span></td>
        <th >税额合计:</th>
        <td ><span id="SUIE_1" class="span_num" style="width:190px"></span></td>
    </tr>
    <tr>
        <th>金额合计:</th>
        <td ><span id="JIN_1" class="span_num" style="width:190px"></span></td>
        <th >含税金额合计(大写):</th>
        <td ><span id="JINHEJI_1" class="span_num" style="width:190px"></span></td>
        <td colspan="2"></td>
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
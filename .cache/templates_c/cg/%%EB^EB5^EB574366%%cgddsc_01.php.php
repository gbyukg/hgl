<?php /* Smarty version 2.6.26, created on 2011-05-25 17:15:23
         compiled from cgddsc_01.php */ ?>
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
/js/cgddsc_01.js"></script>

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
      <td width="65px"></td>
      <td width="125px"></td>
      <td width="75px"></td>
      <td width="125px"></td>
      <td width="65px"></td>
      <td width="125px"></td>
    </tr>
    <tr>
        <th >开票日期:<span style="color: red">*</span></th>
        <td ><input id="KPRQ" name="KPRQ" type="text" style="width: 115px"  value = <?php echo $this->_tpl_vars['kprq']; ?>
 class="editable"  /></td>
        <th >单据编号:</th>
        <td ><label id="XSHDH" style="color:#ccc">--自动生成--</label></td>
        <th >增值税格式:</th>
         <td >    
        <?php if ($this->_tpl_vars['rec']['SHFZZHSH'] == '1'): ?>
              <input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" >是否增值税
                <?php else: ?>
                <input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" checked="checked">是否增值税
                <?php endif; ?></td>
                 <th >开&nbsp;票&nbsp;员:</th>
        <td ><span id="KPYBH" name="KPYBH"  style="width: 115px"   class="span_normal" disabled ><?php echo $this->_tpl_vars['kpybh']; ?>
</span></td>
           <td colspan="4"></td>     
    </tr>
    <tr>
   
        <th >单位编号:</th>
        <td ><input id="DWBH" name="DWBH" type="text" maxlength="8" style="width: 115px" class="editable" 
             /><input id="DWBH_HIDDEN" name="DWBH_HIDDEN" type="hidden" /></td>
        <th >单位名称:</th>
        <td  colspan="3">
       
         <span id="DWMCH" class="span_normal" style="width:310px"></span>
        </td>
    <th >电&nbsp;&nbsp;&nbsp;&nbsp;话:</th>
        <td colspan="4"><input id="DHHM" name="DHHM" type="text" maxlength="25" style="width: 115px" class="editable"  /></td>
 
    </tr>

    <tr>
    <th >地&nbsp;&nbsp;&nbsp;&nbsp;址:</th>
        <td colspan="3"><input id="DIZHI" name="DIZHI" type="text" maxlength="100" style="width: 311px" maxlength="200"class="editable"  /></td>
         <th >部&nbsp;&nbsp;&nbsp;&nbsp;门:<span style="color: red">*</span></th>
        <td >
        <input id="BMMCH" name="BMMCH" type="text" style="width: 115px"  class="editable" />
        <input id="BMBH" name="BMBH" type="hidden" />
        </td>      
        <th >业&nbsp;务&nbsp;员:</th>
        <td colspan="4">
        <input id="YWYMCH" name="YWYMCH" type="text" style="width: 115px" maxlength="8" class="editable"  />
        <input id="YWYBH" name="YWYBH" type="hidden" />        </td>
    	
        
    	<td colspan="3"></td>
    </tr>

    <tr>
        <th >预到货日期:</th>
        <td ><input id="YDHRQ" name="YDHRQ" type="text" style="width: 115px"  class="editable" /></td>
        
        <th >备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
        <td colspan="7"><input id="BEIZHU" name="BEIZHU" type="text"  style="width: 517px" maxlength="500" class="editable" /></td>
   		
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
        <div id="#grid_mingxi"
            style="width: 100%; height:300px; background-color: white;"></div>
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
        <td ><span id="TONGYONGMING" class="span_normal" style="width:190px"></span></td>
        <th >产&nbsp;&nbsp;&nbsp;&nbsp;地:</th>
        <td ><span id="CHANDI" class="span_normal" style="width:190px"></span></td>
        <th >规&nbsp;&nbsp;&nbsp;&nbsp;格:</th>
        <td ><span id="SHPGG" style="width:190px" class="span_normal"></span></td>
    </tr>
    <tr>
        <th >包装单位:</th>
        <td ><span id="BZHDW" style="width:190px" class="span_normal"></span></td>
        <th >数&nbsp;&nbsp;&nbsp;&nbsp;量:</th>
        <td ><span id="SHULIANG" class="span_num" style="width:190px"></span></td>
		<td colspan="2"></td>
    </tr>
</table>
</form>
</div>


</body>
</html>
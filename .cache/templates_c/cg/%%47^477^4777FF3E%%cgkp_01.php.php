<?php /* Smarty version 2.6.26, created on 2011-05-25 11:15:27
         compiled from cgkp_01.php */ ?>
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
/js/cgkp_01.js"></script>

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
      <td width="70px"></td>
      <td width="125px"></td>
      <td width="65px"></td>
      <td width="125px"></td>
      <td width="75px"></td>
      <td width="125px"></td>
      <td width="65px"></td>
      <td width="125px"></td>
    </tr>
    <tr><input type ="hidden" id =" tmp" name ="tmp" value="tmp">
        <th>开票日期:<span style="color: red">*</span></th>
        <td ><input id="KPRQ" name="KPRQ" type="text" style="width: 115px" value = <?php echo $this->_tpl_vars['kprq']; ?>
 class="readonly" readonly  /></td>
        <th >单据编号:</th>
        <td ><label id="XSHDH" style="color:#ccc">--自动生成--</label></td>
    		<input type ="hidden" id="HIDCGDBH" name="HIDCGDBH">
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
         <th>单位编号:<span style="color: red">*</span></th>
        <td ><input id="DWBH" name="DWBH" type="text" maxlength="8" style="width: 115px" class="editable" /></td>  
             <th >单位名称:</th>
        <td  colspan="3">
         <span id="DWMCHH" class="span_normal" style="width:310px"></span>
         <input type ="hidden" id ="DWMCH" name ="DWMCH">
        </td>    
        
       
        <th >电&nbsp;&nbsp;&nbsp;&nbsp;话:</th>
        <td colspan="2"><input id="DHHM" name="DHHM" type="text" maxlength="25" style="width: 115px" class="editable"  /></td>

    </tr>
    <tr>
        
        <th >地&nbsp;&nbsp;&nbsp;&nbsp;址:</th>
        <td colspan="3"><input id="DIZHI" name="DIZHI" type="text" maxlength="100" style="width: 311px" class="editable" /></td>
         <th >部&nbsp;&nbsp;&nbsp;&nbsp;门:<span style="color: red">*</span></th>
        <td><input id="BMMCH" name="BMMCH" type="text" style="width: 115px"  class="editable" />
         <th >业&nbsp;务&nbsp;员:<span style="color: red">*</span></th>
        <td colspan="3">
        <input id="YWYMCH" name="YWYMCH" type="text" style="width: 115px"  class="editable"  />
        <input id="YWYBH" name="YWYBH" type="hidden" /><input id="BMBH" name="BMBH" type="hidden" /></td>
   
        
        </td>
     
    </tr>

    <tr>
       <th>预到货日期:</th>
        <td ><input id="YDHRQ" name="YDHRQ" type="text" style="width: 115px"  class="editable" /></td>
          <th >扣&nbsp;&nbsp;&nbsp;&nbsp;率:</th>
        <td ><input id="KOULV" name="KOULV" type="text" maxlength="6" style="width: 115px" class="editable_num" />
   </td>
        <th >备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
        
        <td colspan="5"><input id="BEIZHU" name="BEIZHU" type="text" maxlength="500" style="width: 312px"  class="editable" /></td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="subtitle" border=0>
    <tr>
        <td width="100px">明细信息</td>
        <td><img id="ADDROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_add.gif" onclick="addRow();" /> <img
            id="DELROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_del.gif" onclick="deleteRow();" /></td>
    </tr>
    <tr>
        <td colspan="2"></td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
      <div id="#grid_mingxi" style="width: 100%; height:200px;background-color: white; "></div>
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
        <td ><span id="CHANDI"  class="span_normalt" style="width:190px"></span></td>
        <th >规&nbsp;&nbsp;&nbsp;&nbsp;格:</th>
        <td ><span id="SHPGG" class="span_normal" style="width:190px"></span></td>
    </tr>
    <tr>
        <th >单&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td ><span id="BZHDW"   class="span_normalt" style="width:190px"></span></td>
        <th >数&nbsp;&nbsp;&nbsp;&nbsp;量:</th>
        <td ><span id="SHULIANG"  class="span_num" style="width:190px"></span></td>
        <th >扣&nbsp;&nbsp;&nbsp;&nbsp;率:</th>
        <td ><span id="KOULV_1"  class="span_num" style="width:190px"></span></td>
    </tr>
    </tr>
    <tr>
        <th >单&nbsp;&nbsp;&nbsp;&nbsp;价:</th>
        <td ><span id="DANJIA"  class="span_num" style="width:190px"></span></td>
        <th >金&nbsp;&nbsp;&nbsp;&nbsp;额:</th>
        <td colspan="3"><span id="JINE"  class="span_num" style="width:190px"></span></td>
    </tr>
    </tr>

</table>
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="150px">单据汇总</td>
        <td></td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
    <tr>
        <th width="60px">数量合计:</th>
        <td width="170px"><span id="SHULIANG_1" class="span_num" style="width:165px" ></span></td>
        <th width="115px">含&nbsp;税&nbsp;金&nbsp;额&nbsp;合&nbsp;计&nbsp;:</th>
        <td width="170px"><span id="HANSUI_1" class="span_num" style="width:165px"></span></td>
        <th  width="60px">税额合计:</th>
        <td width=""><span id="SUIE_1" class="span_num" style="width:185px"></span></td>
    </tr>
    <tr>
        <th >金额合计:</th>
        <td ><span id="JIN_1"  class="span_num" style="width:165px"></span></td>
        <th >含税金额合计(大写):</th>
        <td colspan="3"><span id="JINHEJI_1" class="span_num" style="width:425px"></span></td>
    </tr>

</table>
</form>
</div>


</body>
</html>
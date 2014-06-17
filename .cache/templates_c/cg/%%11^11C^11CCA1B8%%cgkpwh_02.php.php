<?php /* Smarty version 2.6.26, created on 2011-05-30 15:41:15
         compiled from cgkpwh_02.php */ ?>
<?php if ($this->_tpl_vars['full_page']): ?>
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
/js/cgkpwh_02.js"></script>

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
			<input type="hidden" id="SERCHKSRQ" value="<?php echo $this->_tpl_vars['SERCHKSRQ']; ?>
"/>
			<input type="hidden" id="SERCHJSRQ" value="<?php echo $this->_tpl_vars['SERCHJSRQ']; ?>
"/>
			<input type="hidden" id="SERCHDWBH" value="<?php echo $this->_tpl_vars['SERCHDWBH']; ?>
"/>
			<input type="hidden" id="SERCHDWMCH" value="<?php echo $this->_tpl_vars['SERCHDWMCH']; ?>
"/>
			<input type="hidden" id="orderby" value="<?php echo $this->_tpl_vars['orderby']; ?>
"/>
			<input type="hidden" id="direction" value="<?php echo $this->_tpl_vars['direction']; ?>
"/>
<?php endif; ?>
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
      <td></td>
    </tr>
    <tr>
        <th width="10%">开票日期</th>
        
        <td ><input id="KPRQ" name="KPRQ" type="text" style="width: 115px"class="readonly" value="<?php echo $this->_tpl_vars['rec']['KPRQ']; ?>
" readonly/></td>
        <th >单据编号</th>
        <td ><input id="CGDBH" name="CGDBH" type="text" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['CGDBH']; ?>
" readonly/></td>
        <th >增值税格式</th>
         <td >    
        <?php if ($this->_tpl_vars['rec']['SHFZZHSH'] == '1'): ?>
              <input id="SHFZZHSH" name="SHFZZHSH" type="checkbox"  class="readonly"  disabled >是否增值税
                <?php else: ?>
                <input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" checked="checked" class="readonly"  disabled>是否增值税
                <?php endif; ?></td>
                   <th >开&nbsp;票&nbsp;员:</th>
        <td ><span id="KPYBH" name="KPYBH"  style="width: 115px"   class="span_normal" disabled ><?php echo $this->_tpl_vars['kpybh']; ?>
</span></td>
        <td colspan="3"></td>  
    </tr>
    <tr>  
     <th >单位编号</th>
        <td >
        <input id="DWBH" name="DWBH" type="text" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['DWBH']; ?>
" readonly/>
    	</td>
        <th >单位名称</th>
        <td colspan="3">
        <input id="DWMCH" name="DWMCH" type="text" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['DWMCH']; ?>
" readonly/>     
        </td>
        <th >电话</th>
        <td colspan="4">
        <input id="DHHM" name="DHHM" type="text" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['DHHM']; ?>
" readonly/>
        </td>
    </tr>
    <tr>
       
        <th>地址</th>
        <td colspan="3">
         <input id="DIZHI" name="DIZHI" type="text" style="width: 311px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['DIZHI']; ?>
" readonly/>
		</td>
		 <th >部门</th>
        <td ><input id="BMMCH" name="BMMCH" type="text" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['BMMCH']; ?>
" readonly/></td>      
        <th >业务员</th>
        <td colspan="4"><input id="YGXM" name="YGXM" type="text" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['YGXM']; ?>
" readonly/>
		</td>
		
    </tr>

    <tr>
       
    <th >预到货日期</th>
        <td >
        <input id="YDHRQ" name="YDHRQ" type="text" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['YDHRQ']; ?>
" readonly/>
		</td>
		 <th>扣率</th>
        <td >
         <input id="KOULV" name="KOULV" type="text" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['KOULV']; ?>
" readonly/>        
   	</td>
        <th >备注</th>
        <td colspan="6">
        <input id="BEIZHU" name="BEIZHU" type="text" style="width: 312px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['BEIZHU']; ?>
" readonly/> 
        </td>
    </tr>
</table>
</form>
<?php if ($this->_tpl_vars['full_page']): ?>
<table width="100%" cellpadding="0" cellspacing="0" class="subtitle" border=0>
    <tr>
        <td width="150px">明细信息</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
        <div id="#grid_mingxi"
            style="width: 100%; height:160px; background-color: white; "></div>
        </td>
    </tr>
</table>


</div>


</body>
</html>
<?php endif; ?>
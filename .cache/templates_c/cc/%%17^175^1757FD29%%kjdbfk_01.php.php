<?php /* Smarty version 2.6.26, created on 2011-05-27 17:53:25
         compiled from kjdbfk_01.php */ ?>
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
/js/kjdbfk_01.js"></script>

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
 <table width="100%" cellpadding="0" cellspacing="1" class="form" border="0">
    <tr height="0">
      <td width="75px"></td>
      <td width="115px"></td>
      <td width="85px"></td>
      <td width="125px"></td>
      <td width="100px"></td>
      <td width="125px"></td>
      <td width="65px"></td>
      <td width="90px"></td>
    </tr>
    <input type="hidden" id="BMBH_H" name="BMBH_H" />
    <input type="hidden" id="YWYBH_H" name="YWYBH_H" />
	<input type="hidden" id="DCHCKBH" name="DCHCKBH" />
	<input type="hidden" id="DRCKBH" name="DRCKBH" />
	<input type="hidden" id="SHLHEJ" name="SHLHEJ" />
	<input id="BGZH" type="hidden" name="BGZH">
	 <input id="BGRQ" type="hidden" name="BGRQ">
	  <input id="DJBH_TMP" type="hidden" name="DJBH_TMP">
        <tr>
		    <th >
		             开票日期:<font color="red">*</font>
		    </th>
		    <td>
		     <?php if ($this->_tpl_vars['action'] == 'index'): ?>
		        <input id="KPRQ" name="KPRQ" type="text" maxlength="18" style="width: 100px" class="editable" value="<?php echo $this->_tpl_vars['kprq']; ?>
"/>
		        <?php else: ?>
		         <input id="KPRQ" name="KPRQ" type="text" maxlength="18" style="width: 100px" class="editable" />
		        <?php endif; ?>
		    </td>
		    <th >
		             单据编号:
		    </th>
		    <td >
		    
		        <input id="DJBH" name="DJBH" type="text" maxlength="14" style="width: 100px"  disabled class="readonly" value="--自动生成--" />
		    </td>
		   	    <th>
		             部&nbsp;&nbsp;&nbsp;&nbsp;门:<font color="red">*</font>
		    </th>
		    <td>
		        <span id="BMMCH" name="BMMCH" class="span_normal" ><?php echo $this->_tpl_vars['bmmch']; ?>
</span>
                <input id="BMBH" name="BMBH" type="hidden" value="<?php echo $this->_tpl_vars['bmbh']; ?>
"/>
		    </td>
		        <th >
		             业&nbsp;务&nbsp;员:<font color="red">*</font>
		    </th>
		    <td  colspan="3">
		     <?php if ($this->_tpl_vars['action'] == 'index'): ?>
		      	<input id="YGXM" name="YGXM" type="text" maxlength="8" style="width: 90px" class="editable"  value="<?php echo $this->_tpl_vars['rec']['YGXM']; ?>
"/>
		        <input id="YWYBH" name="YWYBH" type="hidden" maxlength="8" style="width: 90px" class="editable" value="<?php echo $this->_tpl_vars['rec']['YWYBH']; ?>
"/>		    	
		        <?php else: ?> 
		       	<input id="YGXM" name="YGXM" type="text" maxlength="8" style="width: 90px" class="editable" />
		        <input id="YWYBH" name="YWYBH" type="hidden" maxlength="8" style="width: 90px" class="editable"/>
		         <?php endif; ?> 
		    </td>
			
        <tr>
        	 <th >调拨出库单:<font color="red">*</font></th>
		    <td> 
		    <?php if ($this->_tpl_vars['action'] == 'index'): ?>
		    <input id="DYDBCKDBH" name="DYDBCKDBH" type="text" maxlength="14"  style="width: 100px"  readonly class="editable" value="<?php echo $this->_tpl_vars['rec']['DJBH']; ?>
" />
		    <input id="djbh" name="djbh" type="hidden" />
		    <?php else: ?>
		      <input id="DYDBCKDBH" name="DYDBCKDBH" type="text" maxlength="14"  style="width: 100px"  readonly class="editable"  />
		    <?php endif; ?>
		    </td>
		    <th>
		             调出仓库:
		    </th>
		    <td>
		    <?php if ($this->_tpl_vars['action'] == 'index'): ?>
		       <input id="DCHCK" name="DCHCK" type="text" maxlength="6" style="width: 100px" readonly class="editable" value="<?php echo $this->_tpl_vars['rec']['DCCKMCH']; ?>
"/>
		     <?php else: ?> 
		     <input id="DCHCK" name="DCHCK" type="text" maxlength="6" style="width: 100px" readonly class="editable"  value="--双击选择仓库--" />
		    <?php endif; ?> 
		    </td>
		    <th>
		             调入仓库:
		    </th>
		    <td>
		    <?php if ($this->_tpl_vars['action'] == 'index'): ?>
		        <input id="DRCK" name="DRCK" type="text" maxlength="6" style="width: 100px" readonly class="editable"   value="<?php echo $this->_tpl_vars['rec']['DRCKMCH']; ?>
"/>
		    <?php else: ?> 
		   <input id="DRCK" name="DRCK" type="text" maxlength="6" style="width: 100px" readonly class="editable"  value="--双击选择仓库--"/>
		   <?php endif; ?> 
		    </td>
		    <th >
		             是否配送:
		    </th>
		    <td colspan="4">
		     <?php if ($this->_tpl_vars['rec']['SHFPS'] == '0'): ?>
		        <input id="SHFPS" name="SHFPS" type="checkbox"/>
		         <?php else: ?> 
		           <input id="SHFPS" name="SHFPS" type="checkbox"  checked="checked"/>
		         <?php endif; ?> 
		    </td>
        <tr>
            <th>
		             电&nbsp;&nbsp;&nbsp;&nbsp;话:
		    </th>
		    <td >
		     <?php if ($this->_tpl_vars['action'] == 'index'): ?>
		        <input id="DHHM" name="DHHM" type="text" maxlength="25" style="width: 100px" class="editable" value="<?php echo $this->_tpl_vars['rec']['DHHM']; ?>
"/>
		        <?php else: ?> 
		          <input id="DHHM" name="DHHM" type="text" maxlength="25" style="width: 100px" class="editable" />
		        <?php endif; ?> 
		    </td>
		    <th>
		             调入仓库地址:
		    </th>
		    <td>
		     <?php if ($this->_tpl_vars['action'] == 'index'): ?>
		        <input id="DRCKDZH" name="DRCKDZH" type="text" maxlength="200" style="width: 100px" class="editable" value="<?php echo $this->_tpl_vars['rec']['DRCKDZH']; ?>
"/>
		        <?php else: ?> 
		        <input id="DRCKDZH" name="DRCKDZH" type="text" maxlength="200" style="width: 100px" class="editable"  />
		        <?php endif; ?> 
		    </td>
			  <th>
		             备&nbsp;&nbsp;&nbsp;&nbsp;注:
		    </th>
		    <td colspan="5">
		    <?php if ($this->_tpl_vars['action'] == 'index'): ?>
		        <input id="BEIZHU" name="BEIZHU" type="text" maxlength="500" style="width:287px" class="editable" value="<?php echo $this->_tpl_vars['rec']['BEIZHU']; ?>
"/>
		         <?php else: ?> 
		         <input id="BEIZHU" name="BEIZHU" type="text" maxlength="500" style="width: 287px" class="editable" />
		        <?php endif; ?>
		    </td>
		    
  

    </table>
      
	<table width="100%" cellpadding="0" cellspacing="0" class="subtitle" border=0>
    <tr>
        <td width="150px">明细信息</td>
        <td width="200px"><img id="ADDROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_add.gif" onclick="addRow();" /> <img
            id="DELROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_del.gif" onclick="deleteRow();" /></td>
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
        <th width="120px">通&nbsp;用&nbsp;名:</th>
        <td width="240px"><span id="TYMCH" class="span_normal" style="width:190px"></span></td>
 
        <th width="120px">产&nbsp;&nbsp;&nbsp;&nbsp;地:</th>
        <td width="240px"><span id="CHANDI" class="span_normal"style="width:190px"></span></td>

        <th width="120px">规&nbsp;&nbsp;&nbsp;&nbsp;格:</th>
        <td><span id="GUIGE" class="span_normal" style="width:190px"></span></td>
        </tr>
        <tr>
        <th>单&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td><span id="BZHDWBH" class="span_normal" style="width:190px"></span></td>
  
        <th>数&nbsp;&nbsp;&nbsp;&nbsp;量:</th>
        <td><span id="SHULIANG" class="span_num" style="width:190px"></span></td>
		  <th>批&nbsp;&nbsp;&nbsp;&nbsp;号:</th>
        <td><span id="PIHAO"  class="span_normal" style="width:190px"></span></td>
      
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
        <th width="65px">数量合计:</th>
        <td><span id="SHLHJ" class="span_num" style="width:190px"></span></td>
        </tr>

	</table>
 	 </form> 
  </div>
  
    <div id="loader" style="z-index:999;position:absolute;left:400px;top:100px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle" />
    </div>

</body>
</html>
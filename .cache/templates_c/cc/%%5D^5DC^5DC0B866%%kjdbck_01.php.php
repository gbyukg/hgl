<?php /* Smarty version 2.6.26, created on 2011-05-30 10:17:41
         compiled from kjdbck_01.php */ ?>
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
/js/kjdbck_01.js"></script>

</head>

<body>
  <div id="top">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="title">
                          <?php echo $this->_tpl_vars['title']; ?>


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
    <input type="hidden" id="BMBH_H" name="BMBH_H"  />
    <input type="hidden" id="YWYBH_H" name="YWYBH_H" />
	<input type="hidden" id="DCHCK_H" name="DCHCK_H" />
	<input type="hidden" id="DRCK_H" name="DRCK_H"  />
		<tr>
			<td width="85px"></td>
			<td width="110px"></td>
			<td width="85px"></td>
			<td width="110px"></td>
			<td width="85px"></td>
			<td width="110px"></td>
			<td width="85px"></td>
			<td></td>
			
		</tr>
        <tr>
		    <th>
		             开票日期:<font color="red">*</font>
		    </th>
		    <td>
		        <input id="KPRQ" name="KPRQ" type="text" maxlength="18" style="width: 100px" class="editable" value = "<?php echo $this->_tpl_vars['kpdate']; ?>
"/>
		    </td>
		    <th>
		             单据编号:
		    </th>
		    <td>
		        <input id="DJBH" name="DJBH" type="text" maxlength="14" style="width: 100px"  disabled class="readonly" value="--保存时自动生成--"/>
		    </td>
	
		    <th>
		             部门:<font color="red">*</font>
		    </th>
		    <td>
		        <span id="BMMCH" name="BMMCH" class="span_normal" ><?php echo $this->_tpl_vars['bmmch']; ?>
</span>
		        <input id="BMBH" name="BMBH" type="hidden" value="<?php echo $this->_tpl_vars['bmbh']; ?>
"/>
		    </td>
		    <th>
		             业务员:<font color="red">*</font>
		    </th>
		    <td>
		        <input id="YWYBH" name="YWYBH" type="text" maxlength="8" style="width: 100px" class="editable" />
		    </td>
		    <td>
		    </td>
        <tr>
		    <th>
		             调出仓库:<font color="red">*</font>
		    </th>
		    <td>
		        <input id="DCHCK" name="DCHCK" type="text" maxlength="6" style="width: 100px" readonly class="readonly" value="--双击选择仓库--" />
		    </td>
		    <th>
		             调入仓库:<font color="red">*</font>
		    </th>
		    <td>
		        <input id="DRCK" name="DRCK" type="text" maxlength="6" style="width: 100px" readonly class="readonly" value="--双击选择仓库--" />
		    </td>
		    <th>
		             是否配送:
		    </th>
		    <td>
		        <input id="SHFPS" name="SHFPS" type="checkbox"/>
		    </td>
		    <th>
		             电话:
		    </th>
		    <td>
		        <input id="DHHM" name="DHHM" type="text" maxlength="25" style="width: 100px" class="editable"/>
		    </td>
		     <td>
		    </td>
		</tr>    
        <tr>
		    <th>
		             调入仓库地址:
		    </th>
		    <td>
		        <input id="DRCKDZH" name="DRCKDZH" type="text" maxlength="200" style="width: 100px" class="editable"/>
		    </td>
	
        
		    <th>
		             备注:
		    </th>
		    <td colspan="3">
		        <input id="BEIZHU" name="BEIZHU" type="text" maxlength="500" style="width: 300px" class="editable"/>
		    </td>
        </tr>

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
            style="width: 100%; height:150px; background-color: white; "></div>
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
        <th >通&nbsp;用&nbsp;名:</th>
        <td ><span id="TYMCH" class="span_normal" style="width:190px"></span></td>
 
        <th >产&nbsp;&nbsp;&nbsp;&nbsp;地:</th>
        <td ><span id="CHANDI" class="span_normal" style="width:190px"></span></td>

        <th >规&nbsp;&nbsp;&nbsp;&nbsp;格:</th>
        <td ><span id="GUIGE" class="span_normal" style="width:190px"></span></td>
        </tr>
        <tr>
        <th>单&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td><span id="BZHDWBH" class="span_normal" style="width:190px"></span></td>
  
        <th>数&nbsp;&nbsp;&nbsp;&nbsp;量:</th>
        <td><span id="SHULIANG" class="span_normal" style="width:190px"></span></td>
 
        <th>库&nbsp;&nbsp;&nbsp;&nbsp;位:</th>
        <td><span id="DCHKW" class="span_normal" style="width:190px"></span></td>
        </tr>
        <tr>
        <th>批&nbsp;&nbsp;&nbsp;&nbsp;号:</th>
        <td><span id="PIHAO" class="span_normal" style="width:190px"></span></td>
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
		   <td width="60px"></td>
		   <td width="200px"></td>
		   <td></td>
   		</tr>
        <tr>
        <th>数量合计:</th>
        <td><span id="SHLHJ" class="span_normal" style="width:190px"></span></td>
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
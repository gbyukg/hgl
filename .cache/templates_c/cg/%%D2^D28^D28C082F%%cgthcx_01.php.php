<?php /* Smarty version 2.6.26, created on 2011-06-08 15:39:14
         compiled from cgthcx_01.php */ ?>
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
/js/cgthcx_01.js"></script>

</head>
<body>
	 <div id="top">
         <table width="100%" cellpadding="0" cellspacing="0">
           <tr>
              <td class="title"> 采购模块-采购退货查询</td>
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
	     	 <td width="100px">查询信息</td>
	     	 <td></td>
	     </tr>
	  </table>
	   <table width="100%" cellpadding="0" cellspacing="1" class="form">
	    <tr height="0">
	      <td width="90px"></td>
	      <td width="150px"></td>
	      <td width="90px"></td>
	      <td width="150px"></td>
	      <td width="90px"></td>
	      <td width="150px"></td>
	      <td width="90px"></td>
	      <td></td>
	    </tr>
        <tr>
            <th> 开始日期：</th>
            <td>
                <input id= "KSRQKEY" name="KSRQKEY" type="text" style="width: 115px"class="editable"/>
            </td>
            <th>终止日期：</th>
            <td>
                <input id="ZZRQKEY" name="ZZRQKEY" type="text" style="width: 115px" class="editable"/>
            </td>
            <td></td>
            <td style="text-aling:left">
            	<img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
            	<img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif"/>
            </td>
            <td colspan = '4'></td>
        </tr>
        <tr>
            <th>单位编号：</th>
            <td>
                <input id="DWBHKEY"  name="DWBHKEY" type="text" style="width: 115px" class="editable"/>
            </td>
            <th>单位名称：</th>
            <td colspan="3">

           <span id="DWMCHKEY" class="span_normal" style="width:310px"></span>
            </td>
            <td colspan = '4'></td>
        </tr>
      </table>
      </form>
	  <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	     <tr>
	     	 <td width="100px">单据信息</td>
	     	 
	    </tr>
	  </table>
	  <table width="100%" cellpadding="0" cellspacing="1" class="grid">
            <tr>
	            <td>
		            <div id="grid_danju" style="width:100%;height:145px;background-color:white;"></div>
		            <div class="paggingarea"><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
	            </td>
            </tr>
	  </table>
	 
	  <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	     <tr>
	     	<td width="100px">明细信息</td>
	    </tr>
	  </table>
	  <table width="100%" cellpadding="0" cellspacing="1" class="grid">
	       <tr><td>
	            <div id="grid_mingxi" style="width:100%;height:145px;background-color:white;"></div>
	            <div class="paggingarea"><span id="pagingArea1"></span>&nbsp;<span id="infoArea1"></span></div>
	       </td></tr>
	  </table>
	 </div>
	 <div id="loader" style="z-index:999;position:absolute;left:200px;top:100px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
     </div>
</body>
</html>
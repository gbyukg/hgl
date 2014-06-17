<?php /* Smarty version 2.6.26, created on 2011-04-27 11:04:25
         compiled from kjdbckdcx_01.php */ ?>
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
/js/kjdbckdcx_01.js" ></script>

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
  	
    <table width="100%" cellpadding="0" cellspacing="1" class="form">
                
	<input type="hidden" id="DCHCK_H" name="DCHCK_H" />
	
	<input type="hidden" id="DRCK_H" name="DRCK_H" />
		<tr>
               	  <td width="100px"></td>
			      <td width="120px"></td>
			      <td width="100px"></td>
			      <td width="120px"></td>
			      <td width="100px"></td>
			      <td width="120px"></td>
			      <td width="60px"></td>
			      <td></td>	
               </tr>
        <tr>
		    <th>
		             调拨出库单据号:
		    </th>
		    <td>
		        <input id="DJBH" name="DJBH" type="text" maxlength="14" style="width: 120px" class="editable"/>
		    </td>
		    <th >
		             调出仓库:
		    </th>
		    <td>
		        <input id="DCHCK" name="DCHCK" type="text" maxlength="6" style="width: 120px" readonly class="readonly" value="--双击选择调出仓库--"/>
		    </td>
		    <th>
		             调入仓库:
		    </th>
		    <td>
		        <input id="DRCK" name="DRCK" type="text" maxlength="6" style="width: 120px" readonly class="readonly" value="--双击选择调入仓库--"/>
		    </td>
		  
		    <td class="button" width="60px">
		        <img id="BTNSEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
		        
		    </td>
		    <td width="60px">
		    	<img id="BTNRESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif" />
		    </td>		    
		</tr>
        <tr>
		    <th width="100px">
		             开票日期从:
		    </th>
		    <td width="150px">
		        <input id="KPRQ_S" name="KPRQ_S" type="text" maxlength="18" style="width: 120px" class="editable"/>
		    </td>
		    <th width="100px">
		             开票日期到:
		    </th>
		    <td >
		        <input id="KPRQ_E" name="KPRQ_E" type="text" maxlength="18" style="width: 120px" class="editable"/>
		    </td>
		    <th width="100px">
		             仅显示未完成单据:

		    </th>

		    <td >
		        <input id="CHKDZHT" name="CHKDZHT" type="checkbox"/>
		    </td>



    </table>
     </form>   

  <table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
         <div id="mygrid" style="width:100%;height:300px;background-color:white;overflow:auto"></div>
               <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
        </td>
       </tr>
       
  </table>
  </div>
   <div id="loader" style="z-index:999;position:absolute;left:200px;top:100px;display:none">
                  <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
   </div>
  </div>
</html>
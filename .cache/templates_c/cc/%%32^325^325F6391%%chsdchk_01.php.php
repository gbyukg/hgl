<?php /* Smarty version 2.6.26, created on 2011-05-23 14:06:42
         compiled from chsdchk_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/chsdchk_01.js"></script>
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
    <form name="form1" id="form1">
    <table width="100%" cellpadding="0" cellspacing="0" class="search" border="0">
      <tr height="0">
      <td width="100px"></td>
      <td width="150px"></td>
      <td width="100px"></td>
      <td width="150px"></td>
      <td width="130px"></td>
      <td></td>
      </tr>
        <tr>
         <th>仓库编号/名称:</th>
         <td width="150px"><input id="CKBH" name="CKBH" type="text" class="editable" style="width:120px"></td>
         <th >传送带出口:</th>
         <td width="150px"><input id="CHSDCHK" name="CHSDCHK" type="text" class="editable" style="width:120px"></td>
         <td class="button"width="200px">
		     	 	<img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif"/>
		     	 	<img id="RESET"	src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif"/>
	     </td>
        </tr>
    </table>	   
   </form>
   <table width="100%" cellpadding="0" cellspacing="1" class="grid">
       <tr>
           <td>
               <div id="mygrid" style="width:100%;height:368px;background-color:white;"></div>
               <div class="paggingarea"><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
           </td>
       </tr>
    </table>
  </div>
</body>  
</html>
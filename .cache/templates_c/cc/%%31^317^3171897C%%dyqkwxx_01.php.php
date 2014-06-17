<?php /* Smarty version 2.6.26, created on 2011-11-07 15:28:53
         compiled from dyqkwxx_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/dyqkwxx_01.js"></script>
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
        <tr>
         <th width="50px">仓库:</th>
         <td width="120px"><input id="CANGKU" name="CANGKU" type="text" class="editable" style="width:120px"></td>
         <th width="50px">待验区:</th>
         <td width="120px"><input id="DAIYANQU" name="DAIYANQU" type="text" class="editable" style="width:120px"></td>
         <th width="80px">待验区库位:</th>
         <td width="120px"><input id="DYQKW" name="DYQKW" type="text" class="editable" style="width:120px"></td>
         <td><img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
         <img id="BTNRESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif" /></td>
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
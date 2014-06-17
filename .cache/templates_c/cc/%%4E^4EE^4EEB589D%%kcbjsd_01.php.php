<?php /* Smarty version 2.6.26, created on 2011-09-15 11:01:40
         compiled from kcbjsd_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/excells/dhtmlxgrid_excell_link.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kcbjsd_01.js"></script>
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
         <th width="160px">商品编号/商品名称/注解码:</th>
         <td width="180px"><input id="SHANGPIN" name="SHANGPIN" type="text" class="editable" style="width:180px"></td>
         <th width="60px">生产厂家:</th>
         <td width="190px"><input id="SHCHCHJ" name="SHCHCHJ" type="text" class="editable" style="width:180px"></td>
         <td width="120px">
            <input type="checkbox" id="KCXX" name="KCXX" />库存下限未设定
         </td>
         <td width="120px">
            <input type="checkbox" id="KCSHX" name="KCSHX" />库存上限未设定
         </td>
         <td>
         <img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
         <img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif"/>
         </td>
        </tr>
    </table>  
    <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
         <tr>
            <td width="100px">明细信息</td>
         </tr>
      </table>
    <table width="100%" cellpadding="0" cellspacing="1" class="grid">
       <tr>
           <td>
               <div id="#grid_mingxi" style="width: 100%; height: 568px; background-color: white;"></div>
               <div class="paggingarea"><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
           </td>
       </tr>
    </table>
    </form>
  </div>
</body>  
</html>
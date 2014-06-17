<?php /* Smarty version 2.6.26, created on 2011-06-07 18:10:41
         compiled from fxcl_01.php */ ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->_tpl_vars['title']; ?>
</title>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_drag.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_mcol.js"></script> 
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/fxcl_01.js"></script>
</head>
<body>

  <div id="top">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
                  <td class="title" >
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
 <table width="100%" cellpadding="0" cellspacing="1" class="form" border="0">
 <tr>
 <td>
	 <form name="form1" id="form1" style="display:inline;margin:0px;margin-top:0px">
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle"  border="0" >
<tr>
<td>
	 <table width="100%" cellpadding="0" cellspacing="1" class="form" border="0">
    <tr height="0">
      <td width="70px"></td>
      <td width="155px"></td>
      <td width="75px"></td>
      <td width="75px"></td>
     </tr>
        <tr>
              <th>销售单号：</th>
              <td>
                  <input id= "XSDH" name="XSDH" type="text" style="width: 115px"class="readonly" value="<?php echo $this->_tpl_vars['xshdbh']; ?>
" readonly/>
              </td>
              <th>整件数量</th>
              <td colspan = '3'>
                  <input id="ZJSL" name="ZJSL" type="text" style="width: 115px" class="readonly" readonly value="<?php echo $this->_tpl_vars['rec']['SHULIANG']; ?>
"/>
              </td>
              <td  style="text-aling:left"></td>
              <td></td>
          </tr>
      </table>
      
      <table width="100%" cellpadding="0" cellspacing="1">
          <td width="57%">
              <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
                  <tr>
                      <td width="100px">零散商品</td>
                      <td width="100px"></td>
                      <td width="40px">剩余</td>
                      <td width="60px"><input id="SHENGYU" name="SHENGYU" type="text" style="width:50px" class="readonly" readonly="readonly"/></td>
                      <td>箱</td>
                  </tr>
              </table>
              <table width="100%" cellpadding="0" cellspacing="1" class="grid">
                  <tr>
                      <td>
                         <div id="#grid_main" style="width:100%;height:352px;background-color:white;"></div>
                      </td>
                  </tr>
              </table>
          </td>
          <td>
              <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
                  <tr>
                      <td width="100px">当前箱商品</td>
                      <td width="40px">分箱号</td>
                      <td width="80px"><input id="XIANGHAO" name="XIANGHAO" type="text" style="width:70px" class="readonly" readonly="readonly" /></td>
                      <td width="40px">填充率</td>
                      <td width="60px"><input id="SHENGYU" name="SHENGYU" type="text" style="width:50px" class="readonly" readonly="readonly" /></td>
                      <td width="20px"></td>
                      <td width="60px"><input name="FENXIANG" type="button" id="FENXIANG" value="封箱" onclick="fenxiangRow();"/></td>
                      <td></td>
                  </tr>
              </table>
              <table width="100%" cellpadding="0" cellspacing="1" class="grid">
                  <tr>
                      <td>
                         <div id="#grid_shp" style="width:100%;height:160px;background-color:white;"></div>
                      </td>
                  </tr>
              </table>
              <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
                  <tr>
                      <td width="150px">箱列表</td>
                      <td><img id="DELROW" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_del.gif" onclick="deleteRow();" /></td>
                  </tr>
              </table>
              <table width="100%" cellpadding="0" cellspacing="1" class="grid">
                  <tr>
                      <td>
                         <div id="#grid_fxmingxi" style="width:100%;height:160px;background-color:white;"></div>
                      </td>
                  </tr>
              </table>
          </td>
      </table>
      </td></tr></table>
    </form>
</div>

</body>
</html>


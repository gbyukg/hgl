<?php /* Smarty version 2.6.26, created on 2011-05-23 14:03:13
         compiled from lsfjdb_01.php */ ?>
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
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_drag.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_mcol.js"></script> 
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/lsfjdb_01.js"></script>
</head>

<body>
	 <div id="top">
         <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td class="title"><?php echo $this->_tpl_vars['title']; ?>
</td>
              <td><div id="toolbar"></div></td>
            </tr>
         </table>
	 </div>
	 <div id="vspace"></div>
<div id="body">
<form name="form1" id="form1" style="display:inline;margin:0px;">
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
              <th>当前周转箱：</th>
              <td>
                  <input id= "DQZZX" name="DQZZX" type="text" style="width: 115px" class="editable"/>
              </td>
              <th>本单剩余箱数：</th>
              <td>
                  <span id="BDSHYXSH" style="width: 115px" class="span_normal"></span>
              </td>
              <th>订单编号：</th>
              <td>
                  <span id= "DDBH" style="width: 115px" ></span>
              </td>
              <td colspan="2" style="text-aling:left"></td>
          </tr>
      </table>      
      <table width="100%" cellpadding="0" cellspacing="1">
      		<td width="25%">
              <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
                  <tr height="25px">
                      <td width="100px">已处理周转箱</td>
                  </tr>
              </table>
              <table width="100%" cellpadding="0" cellspacing="1" class="grid">
                  <tr>
                      <td>
                         <div id="#grid_zhzhx" style="width: 100%; height:200px; background-color: white;"></div>
                      </td>
                  </tr>
              </table>
              <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
                  <tr height="25px">
                      <td width="150px">封箱后箱子列表</td>
                  </tr>
              </table>
              <table width="100%" cellpadding="0" cellspacing="1" class="grid">
                  <tr>
                      <td>
                        <div id="#grid_xiang" style="width: 100%; height:200px; background-color: white;"></div>
                      </td>
                  </tr>
              </table>
          </td>
          <td>
              <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
                  <tr height="25px">
                      <td width="100px">待封箱商品</td>
                      <td width="200px"></td>
                      <td width="100px"><input name="fengxiang" id="fengxiang" type="button" value="封箱" /></td>
                      <td></td>
                  </tr>
              </table>
              <table width="100%" cellpadding="0" cellspacing="1" class="grid">
				    <tr>
				        <td>
				        <div id="#grid_shangpin" style="width: 100%; height:432px; background-color: white;"></div>
				        </td>
				    </tr>
			  </table>
          </td>
      </table>
    </form>
    </div> 	
</body>
</html>
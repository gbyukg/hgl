<?php /* Smarty version 2.6.26, created on 2011-05-26 14:48:30
         compiled from kcbjsd_02.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!--<title><?php echo $this->_tpl_vars['title']; ?>
</title>-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kcbjsd_02.js"></script>
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
        <table width="100%" cellpadding="0" cellspacing="1" class="form">
            <tr>
                <th width="20%">
                    商品编号:
                </th>
                <td width="80%">
                    <input  id="SHPBH" name="SHPBH" type="text" maxlength="20" style="width: 280PX" class="readonly" readonly="readonly" value="<?php echo $this->_tpl_vars['shpbh']; ?>
"/>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    商品名称:
                </th>
                <td width="80%">
                    <input  id="SHPMCH" name="SHPMCH" type="text" maxlength="20" style="width: 280PX" class="readonly" readonly="readonly" value="<?php echo $this->_tpl_vars['rec']['SHPMCH']; ?>
"/>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    库存上限:<span style="color: red">*</span>
                </th>
                <td width="80%" >
                    <input  id="KCSHX" name="KCSHX" type="text" maxlength="20" style="width: 280PX" class="edit" value="<?php echo $this->_tpl_vars['rec']['KCSHX']; ?>
"/>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    库存下限:<span style="color: red">*</span>
                </th>
                <td width="80%" >
                    <input  id="KCXX" name="KCXX" type="text" maxlength="20" style="width: 280PX" class="edit" value="<?php echo $this->_tpl_vars['rec']['KCXX']; ?>
"/>
                </td>
            </tr>
            <tr><td colspan='2' style="height:10px;"></td></tr>
            <tr>
            <td></td>
            <td style="padding-left:230px;">
                <img id="OK" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_ok.gif" />
            </td>
            </tr>
        </table>
         </form>  
         </div> 
</body>
</html>
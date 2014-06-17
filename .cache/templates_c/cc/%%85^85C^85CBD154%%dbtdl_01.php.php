<?php /* Smarty version 2.6.26, created on 2011-05-17 17:48:34
         compiled from dbtdl_01.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'dbtdl_01.php', 47, false),)), $this); ?>
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
/js/dbtdl_01.js"></script>


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
                    仓库:<span style="color: red">*</span>
                </th>
                <td width="80%">
                <?php if ($this->_tpl_vars['con'] == 'one'): ?>
                    <input  id="CKMCH" name="CKMCH" type="text" maxlength="20" style="width: 160PX" class="readonly" readonly="readonly" value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
"/>
                    <input  id="CKBH" name="CKBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
"/>
                <?php else: ?>
                    <input  id="CKMCH" name="CKMCH" type="text" maxlength="20" style="width: 160PX" class="readonly" readonly="readonly" value="双击请选择仓库"/>
                    <input  id="CKBH" name="CKBH" type="hidden" value=""/>
                <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    打包台:<span style="color: red">*</span>
                </th>
                <td width="80%" >
                    <select style="width: 165PX" id="DBT" name="DBT">
<!--                         <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['kqlx']), $this);?>
-->
                    </select>
                </td>
            </tr>
            <tr><td colspan='2' style="height:10px;"></td></tr>
            <tr>
            <td></td>
            <td style="padding-left:110px;">
                <img id="NEXT" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_ok.gif" />
<!--                <input type="button" id="NEXT" value="下一步" />-->
            </td>
            </tr>
        </table>
         </form>  
         </div> 
</body>
</html>
<?php /* Smarty version 2.6.26, created on 2011-08-22 10:59:31
         compiled from kuwei_03.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'kuwei_03.php', 27, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kuwei_03.js"></script>
</head>
<body>
    <div id="top">
            <table width="100%" cellpadding="0" cellspacing="0" >
               <tr>
                  <td>
                    <div id="toolbar"></div>
                  </td>
                </tr>
             </table>
    </div>
    <div id="vspace"></div>
    <div id="body">
               <table width="100%" cellpadding="0" cellspacing="1" class="form">
                                <tr>
                                    <th width="40px">
                                                                                                    仓库
                                    </th>
                                     <td width="130px">
                                     <select id="CKBH" style="width:120px">
                                     <option value=''>--全部仓库--</option>
                                     <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['cklist']), $this);?>

                                     </select>
                                     </td>
                                     <th width="60px">
                                                                                                    库区类型
                                   </th>
                                     <td width="130px">
                                      <select id="KQLX" style="width:120px">
                                      <option value=''>--全部类型--</option>
                                     <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['kqlx']), $this);?>

                                     </select>
                                     </td>
                                     <th width="40px">
                                                                                                       库区                                                           
                                    </th>
                                     <td width="130px">
                                       <select id="KQBH" style="width:120px">
                                      <option value=''>--全部库区--</option>
                                     <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['kqlist']), $this);?>

                                     </select>
                                     </td>
                                      <th width="60px">
                                                                                                    库位类型
                                   </th>
                                     <td width="130px">
                                      <select id="KWLX" style="width:120px">
                                      <?php if ($this->_tpl_vars['kwlx'] == '0'): ?>
                                          <option value='0'>整件库位</option>
                                      <?php elseif ($this->_tpl_vars['kwlx'] == '1'): ?>   
                                         <option value='1'>零散库位</option> 
                                      <?php else: ?>   
	                                      <option value=''>--全部类型--</option>
	                                      <option value='0'>整件库位</option>
	                                      <option value='1'>零散库位</option>
	                                  <?php endif; ?>
                                     </select>
                                     </td>
                                     <td><img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" id="BTNSEARCH" /></td>
                              </tr>
                </table>
            <!----------- 数据列表 ------------------>
            <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            <div id="gridbox" style="width:100%;height:245px;background-color:white;overflow:auto"></div>
            <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
             </td></tr>
            </table>
        </div>
</body>
</html>
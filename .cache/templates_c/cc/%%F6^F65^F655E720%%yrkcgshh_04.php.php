<?php /* Smarty version 2.6.26, created on 2011-09-19 17:14:10
         compiled from yrkcgshh_04.php */ ?>
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
/js/yrkcgshh_04.js"></script>
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
<form name="form1" id="form1" style="display: inline; margin: 0px;">
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="100px">新采购订单单据信息</td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
    <tr height="0">
        <td width="65px"></td>
        <td width="125px"></td>
        <td width="65px"></td>
        <td width="125px"></td>
        <td width="65px"></td>
        <td width="125px"></td>
        <td width="65px"></td>
        <td></td>
    </tr>
    <tr>
        <th>开票日期:</th>
        <td><input id="KPRQ" name="KPRQ" type="text" style="width: 115px" class="readonly" readonly="readonly" value="<?php echo $this->_tpl_vars['rec']['KPRQ']; ?>
" /></td>
        <th>单据编号:</th>
        <td>
            <input type="hidden" id="YRKDBH" name="YRKDBH" value="<?php echo $this->_tpl_vars['yrkdbh']; ?>
" />
            <input type="hidden" id="flg" name="flg" value="<?php echo $this->_tpl_vars['flg']; ?>
" />
            <input type="text" id="DJBH" name="DJBH" class="readonly" readonly="readonly" style="width: 115px"  <?php if ($this->_tpl_vars['flg'] == 'new'): ?>value="系统自动生成"<?php else: ?>value="<?php echo $this->_tpl_vars['djbh']; ?>
"<?php endif; ?> />
        </td>
        <th>增&nbsp;值&nbsp;税:</th>
        <td><input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" checked="<?php echo $this->_tpl_vars['rec']['SHFZZHSH']; ?>
" disabled></input></td>
        <th>开&nbsp;票&nbsp;员:</th>
        <td>
            <input type="text" id="KPYXM" name="KPYXM" class="readonly" readonly="readonly" style="width: 115px"  value="<?php echo $this->_tpl_vars['rec']['KPYXM']; ?>
" />
        </td>
    </tr>
    <tr>
        <th>单位编号:</th>
        <td><input type="text" id="DWBH" name="DWBH"  class="readonly" readonly="readonly" style="width: 115px" value="<?php echo $this->_tpl_vars['rec']['DWBH']; ?>
" /></td>
        <th>单位名称:</th>
        <td colspan="3">
            <span id="DWMCH" class="span_normal" style="width: 311px"><?php echo $this->_tpl_vars['rec']['DWMCH']; ?>
</span>
        </td>
        </td>
        <th>电&nbsp;&nbsp;&nbsp;&nbsp;话:</th>
        <td>
            <input type="text" id="DHHM" name="DHHM"  class="readonly" readonly="readonly" style="width: 115px" value="<?php echo $this->_tpl_vars['rec']['DWBH']; ?>
" />
        </td>
    </tr>
    <tr>
        <th>地&nbsp;&nbsp;&nbsp;&nbsp;址:</th>
        <td colspan="3"><span id="DIZHI" class="span_normal" style="width: 115px"><?php echo $this->_tpl_vars['rec']['DIZHI']; ?>
</span></td>
        <th>部&nbsp;&nbsp;&nbsp;&nbsp;门:</th>
        <td>
            <input id="BMMCH" name="BMMCH" type="text" readonly="readonly" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['BMMCH']; ?>
" />
        </td>
        <th>业&nbsp;务&nbsp;员:</th>
        <td><input id="YWYMCH" name="YWYMCH" type="text" readonly="readonly" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['YWYXM']; ?>
" /> <input id="YWYBH" name="YWYBH" type="hidden" /></td>
    </tr>
    <tr>
        <th>预到货日期:</th>
        <td><input id="YDHRQ" name="YDHRQ" readonly="readonly" type="text" style="width: 115px" class="readonly" readonly="readonly" value="<?php echo $this->_tpl_vars['rec']['YDHRQ']; ?>
" /></td>
        <th>扣&nbsp;&nbsp;&nbsp;&nbsp;率:</th>
        <td>
	        <span id="KOULV" name="KOULV" class="span_num" style="width:115px;" ><?php echo $this->_tpl_vars['rec']['KOULV']; ?>
</span> 
	        <input id="BMBH" name="BMBH" type="hidden" />
        </td>
        <th>备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
        <td colspan="3"><span id="BEIZHU" name="BEIZHU" class="span_normal" style="width: 311px;"><?php echo $this->_tpl_vars['rec']['BEIZHU']; ?>
</span></td>
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
        <div id="grid_mingxi" style="width: 100%; height: 380px; background-color: white;"></div>
        </td>
    </tr>
</table>
</form>
</div>
</body>
</html>
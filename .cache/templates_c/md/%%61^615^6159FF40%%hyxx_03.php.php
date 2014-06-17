<?php /* Smarty version 2.6.26, created on 2011-04-25 16:44:17
         compiled from hyxx_03.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'hyxx_03.php', 195, false),)), $this); ?>
<?php if ($this->_tpl_vars['full_page']): ?>
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
/js/hyxx_03.js"></script>
</head>
<body>
<div id="top">
<table width="900px" cellpadding="0" cellspacing="0">
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
<table width="900px" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="100px">会员信息</td>
    </tr>
</table>
<input type="hidden" id="orderby" value="<?php echo $this->_tpl_vars['orderby']; ?>
"/>
<input type="hidden" id="direction" value="<?php echo $this->_tpl_vars['direction']; ?>
"/>
<input type="hidden" id="hymkey" value="<?php echo $this->_tpl_vars['hymkey']; ?>
"/>
<input type="hidden" id="hykh" value="<?php echo $this->_tpl_vars['hykh']; ?>
"/>
<input type="hidden" id="lxdh" value="<?php echo $this->_tpl_vars['lxdh']; ?>
"/>
<?php endif; ?>
  <form name="form1" id="form1" style="display:inline;margin:0px;">
            <table width="900px" cellpadding="0" cellspacing="1" class="form">
	             	<tr>
						<td width="100px"></td>
						<td width="180px"></td>
						<td width="100px"></td>
						<td width="180px"></td>
						<td width="100px"></td>
						<td></td>
					</tr>
					<tr>
					    <th>
					        门店
					    </th>
					    <td>
					        <input id="JBMDMCH" name="JBMDMCH" type="text" style="width: 160px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['JBMDMCH']; ?>
"/>
					        <!--<input id="JBMD" name="JBMD" type="hidden" value="<?php echo $this->_tpl_vars['rec']['JBMD']; ?>
"/>
					    --></td>
					    <th>
					        经办人
		
					    </th>
					    <td>
					        <input id="JINGBANRENM" name="JINGBANRENM" type="text" style="width: 160px" class="readonly" readonly="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['JINGBANRENM']; ?>
"/>
					        <!--<input id="JINGBANREN" name="JINGBANREN" type="hidden" value="<?php echo $this->_tpl_vars['rec']['JINGBANREN']; ?>
"/>
					    --></td>
					    <th width="80px">
					        开户日期		
					    </th>
					    <td>
					        <input id="DJRQ" name="DJRQ" type="text" style="width: 160px" class="readonly" readonly="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['DJRQ']; ?>
"/>
					    </td>
					</tr>
				</table>
				
				<table width="900px" cellpadding="0" cellspacing="1" class="subtitle">
					<tr><td width="100px">会员信息</td>
					</tr>
				</table>
		
				<table width="900px" cellpadding="0" cellspacing="1" class="form">
					             	<tr>
						<td width="100px"></td>
						<td width="180px"></td>
						<td width="100px"></td>
						<td width="180px"></td>
						<td width="100px"></td>
						<td></td>
					</tr>
					<tr>
					    <th>
					        会员编号
					    </th>
					    <td>
		                    	<input id="HYBH" name="HYBH" type="text" style="width: 160px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['HYBH']; ?>
"/>
					    </td>
					    <th>
					        会员名<font color="red">*</font>
					    </th>
					    <td>
							<input id="HUIYUANMING" name="HUIYUANMING" type="text" maxlength="20" style="width: 160px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['HUIYUANMING']; ?>
"/>
					    </td>
					    <th>
					        性别
					    </th>
					    <td>            
				            <?php if ($this->_tpl_vars['rec']['XINGBIE'] == '0'): ?>
					            <input type="radio" name="sex" id="male" value="0" checked disabled><label for="male">男</label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp				            
					            <input type="radio" name="sex" id="female" value="1" disabled><label for="female">女</label>  
				            <?php elseif ($this->_tpl_vars['rec']['XINGBIE'] == '1'): ?>  
					            <input type="radio" name="sex" id="male" value="0" disabled><label for="male">男</label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
					            <input type="radio" name="sex" id="female" value="1" checked disabled><label for="female">女</label> 
					        <?php elseif ($this->_tpl_vars['rec']['XINGBIE'] == '9'): ?>
					            <input type="radio" name="sex" id="male" value="0" disabled><label for="male">男</label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
					            <input type="radio" name="sex" id="female" value="1" disabled><label for="female">女</label>
					         <?php else: ?>  
					          	<input type="radio" name="sex" id="male" value="0" disabled><label for="male">男</label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp				            
					            <input type="radio" name="sex" id="female" value="1" disabled><label for="female">女</label> 
					            <?php endif; ?> 
				            <input id="XINGBIE" name="XINGBIE" type="hidden"/> 
					    </td>
					</tr>
					
					<tr>
					    <th>
					        身份证号
					    </th>
					    <td>
							<input id="SHFZHH" name="SHFZHH" type="text" maxlength="18" style="width: 160px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['SHFZHH']; ?>
"/>
					    </td>
					    <th >
					        出生日期
					    </th>
					    <td>
							<input id="CHSHRQ" name="CHSHRQ" type="text" style="width: 160px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['CHSHRQ']; ?>
"/>
					    </td>
					    <th>
					        联系电话<font color="red">*</font>
					    </th>
					    <td>
							<input id="LXDH" name="LXDH" type="text" maxlength="25" style="width: 160px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['LXDH']; ?>
"/>			    
					    </td>
					</tr>
					
					<tr>
					    <th>
					        邮政编码
					    </th>
					    <td>
							<input id="YZHBM" name="YZHBM" type="text" maxlength="6" style="width: 160px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['YZHBM']; ?>
"/>
					    </td>
					    <th>
					        通讯地址
					    </th>
					    <td colspan="3">
							<input id="TXDZH" name="TXDZH" type="text" maxlength="100" style="width: 452px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['TXDZH']; ?>
"/>
					    </td>
					</tr>
					
					<tr>
					    <th>
					        Email
					    </th>
					    <td>
							<input id="EMAIL" name="EMAIL" type="text" maxlength="50" style="width: 160px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['EMAIL']; ?>
"/>
					    </td>
					    <th>
					        备注
					    </th>
					    <td colspan="3">
							<input id="BEIZHU" name="BEIZHU" type="text" maxlength="500" style="width: 452px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['BEIZHU']; ?>
"/>
					    </td>
					</tr>
				</table>
				
				<table width="900px" cellpadding="0" cellspacing="1" class="subtitle">
					<tr><td width="100px">卡片输入</td>
					</tr>
				</table>
		
				<table width="900px" cellpadding="0" cellspacing="1" class="form">
					             	<tr>
						<td width="100px"></td>
						<td width="180px"></td>
						<td width="100px"></td>
						<td width="180px"></td>
						<td width="100px"></td>
						<td></td>
					</tr>
					<tr>
					    <th>
					        会员卡号<font color="red">*</font>
					    </th>
					    <td>
							<input id="HYKH" name="HYKH" type="text"  maxlength="14" style="width: 160px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['HYKH']; ?>
"/>
					    </td>
					    <th>
					        卡片类型
					    </th>
					    <td>
							<select id="KPLX" name="KPLX" style="width: 160px" disabled class="readonly">
								<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['kplx_opts'],'selected' => $this->_tpl_vars['rec']['KPLX']), $this);?>
 
							</select>
					    </td>
					    <th>
					        丢失补办
					    </th>
					    <td>
							<select id="DSHBB" name="DSHBB" style="width: 160px" disabled class="readonly">
								<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['dshbb_opts'],'selected' => $this->_tpl_vars['rec']['DSHBB']), $this);?>
 
							</select>
					    </td>
					</tr>
					
					<tr>
					    <th>
					        失效日期
					    </th>
					    <td>
							<input id="SHXRQ" name="SHXRQ" type="text" style="width: 160px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['SHXRQ']; ?>
"/>
					    </td>
					    <th >
					        初始积分
					    </th>
					    <td>
							<input id="CSHJF" name="CSHJF" type="text" maxlength="6" style="width: 160px" class="editable_num" disabled value="<?php echo $this->_tpl_vars['rec']['CSHJF']; ?>
"/>
					    </td>
					    <th>
					        现有积分
					    </th>
					    <td>
							<input id="XYJF" name="XYJF" type="text" maxlength="6" style="width: 160px" class="editable_num" disabled value="<?php echo $this->_tpl_vars['rec']['XYJF']; ?>
"/>			    
					    </td>
					</tr>
					
					<tr>
					    <th>
					      兑换积分
					    </th>
					    <td>
							<input id="DHJF" name="DHJF" type="text" maxlength="6" style="width: 160px" class="editable_num" disabled value="<?php echo $this->_tpl_vars['rec']['DHJF']; ?>
"/>
					    </td>
					    <th>
					        累计积分
					    </th>
					    <td colspan="3">
							<input id="LJJF" name="LJJF" type="text" maxlength="6" style="width: 160px" class="editable_num" disabled value="<?php echo $this->_tpl_vars['rec']['LJJF']; ?>
"/>
					    </td>
					</tr>
					
				</table>
                </form>
<?php if ($this->_tpl_vars['full_page']): ?>
</div>

</body>
</html>
<?php endif; ?>
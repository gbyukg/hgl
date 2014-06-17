<?php /* Smarty version 2.6.26, created on 2011-08-19 10:58:07
         compiled from yrkcgshh_02.php */ ?>
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
/js/yrkcgshh_02.js"></script>
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
		<td width="100px">预入库单据信息</td>
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
		<th>预入库单号:</th>
		<td><span id="YRKDBH" name="YRKDBH" class="span_normal" style="width: 115px"><?php echo $this->_tpl_vars['yrkdbh']; ?>
</span></td>
		<th>增&nbsp;值&nbsp;税:</th>
		<td><input id="SHFZZHSH" name="SHFZZHSH" type="checkbox" checked="<?php echo $this->_tpl_vars['rec']['SHFZZHSH']; ?>
" disabled></input></td>
		<th>采购单号:</th>
		<td><span id="CGDBH" class="span_normal" style="width: 115px"><?php echo $this->_tpl_vars['rec']['CKDBH']; ?>
</span></td>
	</tr>
	<tr>
		<th>单位编号:</th>
		<td><span id="DWBH" class="span_normal" style="width: 115px"><?php echo $this->_tpl_vars['rec']['DWBH']; ?>
</span></td>
		<th>单位名称:</th>
		<td colspan="3"><span id="DWMCH" class="span_normal" style="width: 311px"><?php echo $this->_tpl_vars['rec']['DWMCH']; ?>
</span></td>
		</td>
		<th>电&nbsp;&nbsp;&nbsp;&nbsp;话:</th>
		<td><span id="DHHM" class="span_normal" style="width: 115px"><?php echo $this->_tpl_vars['rec']['DHHM']; ?>
</span></td>
	</tr>
	<tr>
		<th>地&nbsp;&nbsp;&nbsp;&nbsp;址:</th>
		<td colspan="5"><span id="DIZHI" class="span_normal" style="width: 115px"><?php echo $this->_tpl_vars['rec']['DIZHI']; ?>
</span></td>
		<th>扣&nbsp;&nbsp;&nbsp;&nbsp;率:</th>
		<td><span id="KOULV" class="span_num" style="width: 115px"><?php echo $this->_tpl_vars['rec']['KOULV']; ?>
</span></td>
	</tr>
	<tr>
		<th>送货清单号:</th>
		<td><input id="YWYMCH" name="YWYMCH" readonly="readonly" type="text" style="width: 115px" class="readonly" readonly="readonly" value="<?php echo $this->_tpl_vars['rec']['SHQDH']; ?>
" /></td>
		<th>发票编号:</th>
		<td><input id="BEIZHU" name="BEIZHU" maxlength="500" type="text" readonly="readonly" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['FPBH']; ?>
" /></td>
		<th>部&nbsp;&nbsp;&nbsp;&nbsp;门:</th>
		<td><input id="BMMCH" name="BMMCH" type="text" readonly="readonly" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['BMMCH']; ?>
" /> <input id="BMBH" name="BMBH" type="hidden" /></td>
		<th>业&nbsp;务&nbsp;员:</th>
		<td><input id="YWYMCH" name="YWYMCH" type="text" readonly="readonly" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['YWYXM']; ?>
" /> <input id="YWYBH" name="YWYBH" type="hidden" /></td>
	</tr>
	<tr>
		<th>备&nbsp;&nbsp;&nbsp;&nbsp;注:</th>
		<td colspan="5"><input id="BEIZHU" name="BEIZHU" readonly="readonly" maxlength="500" type="text" value="<?php echo $this->_tpl_vars['rec']['BEIZHU']; ?>
" style="width: 507px" class="readonly" /></td>
		<th>采&nbsp;购&nbsp;员:</th>
		<td><input id="YWYMCH" name="YWYMCH" type="text" readonly="readonly" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['DYCGYXM']; ?>
" /> <input id="YWYBH" name="YWYBH" type="hidden" /></td>
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
		<div id="grid_mingxi" style="width: 100%; height: 180px; background-color: white;"></div>
		</td>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="subtitle">
	<tr>
		<td width="55%">
		<table width="100%" cellpadding="0" cellspacing="0" class="subtitle">
			<tr>
				<td style="width: 100%; height: 20px;">采购订单与实收货对比信息</td>
			</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" class="grid">
			<tr>
				<td>
				<div id="grid_match" style="width: 100%; height: 180px; background-color: white;"></div>
				</td>
			</tr>
		</table>
		</td>
		<td width="45%">
		<table width="100%" cellpadding="0" cellspacing="0" class="subtitle">
			<tr>
				<td style="width: 100px; height: 20px;">预入库警告信息</td>
				<td><input id="B_ALARM" name="B_ALARM" type="button" style="height: 20px;" value="所有警告信息""></td>
			</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" class="grid">
			<tr>
				<td>
				<div id="grid_alarm" style="width: 99%; height: 180px; background-color: white;"></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="subtitle">
	<tr>
		<td width="120px">采购员审核意见</td>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
	<tr>
		<td>
			<ul>
				<li>
				    <table>
					    <tr>
					        <td>
					            <input type="radio" name="namez" id="SFCZCGDD" value="0" /> 
	                            <label id="l1" for="SFCZCGDD">重做采购订单</label>
					        </td>
					        <td><input id="XDDYL" name="XDDYL" type="button" style="display:none;" value="新订单预览" /></td>
					    </tr>
				    </table>
				</li>
				<li>
					<table>
						<tr>
							<td>
								<input type="radio" name="namez" id="SFCFGHQD" value="1" />
								<label for="SFCFGHQD">要求厂家重发购货清单</label>
							</td>
						</tr>
					</table>
				</li>
				<li>
					<table>
						<tr>
							<td>
							    <input type="radio" name="namez" id="ZDTH" value="2" /> <label for="ZDTH">整单退回</label>
							</td>
						</tr>
					</table>
				</li>
			</ul>
		</td>
	</tr>
</table>
</form>
</div>
</body>
</html>
<?php /* Smarty version 2.6.26, created on 2011-08-23 10:55:05
         compiled from shpzl_02.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'shpzl_02.php', 110, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/calender/WdatePicker.js"></script>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/shpzl_02.js"></script>
	
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

            <form name="form1" id="form1" style="display:inline;margin:0px;">
            <input type=hidden id="action" name="action" value="<?php echo $this->_tpl_vars['action']; ?>
" />
			<input type=hidden id="BGRQ" name="BGRQ" value="<?php echo $this->_tpl_vars['rec']['BGRQ']; ?>
" />
			<!--拆散单位flg，等于1时，保存时需要删除对应商品的拆散信息-->
			<input type=hidden id="FLG_DELCHAISAN" name="FLG_DELCHAISAN" value="0" />
	<table width="100%" cellpadding="0" cellspacing="1" class="form">
	         <tr height="0">
		         <td width="100px"></td>
		         <td width="164px"></td>
		         <td width="100px"></td>
		         <td width="164px"></td>
		         <td width="95px"></td>
		         <td></td>
	         </tr>
			<tr>
			    <th>
			       商品编号<font color="red">*</font>
			    </th>
			    <td>
			    <?php if ($this->_tpl_vars['action'] == 'update'): ?>
			    <input type=hidden id="SHPBH" name="SHPBH" value="<?php echo $this->_tpl_vars['rec']['SHPBH']; ?>
" /> 
			        <input id="SHPBH" name="SHPBH" maxlength="8" type="text" style="width:155px" value="<?php echo $this->_tpl_vars['rec']['SHPBH']; ?>
" class="readonly" disabled/>
			        <?php else: ?>
			        <input id="SHPBH" name="SHPBH" maxlength="8" type="text" style="width:155px" class="editable"/>
			        <?php endif; ?>
			    </td>
			    <th>
			       商品名称<font color="red">*</font>
			    </th>
			    <td colspan="3">
			        <input id="SHPMCH" name="SHPMCH" type="text" maxlength="100" style="width:420px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['SHPMCH']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       商品规格
			    </th>
			    <td>
			        <input id="GUIGE" name="GUIGE" type="text" maxlength="50" style="width:155px" class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['GUIGE']; ?>
"/>
			    </td>
			                 <th>
			       药品功能主治疗效
			    </th>
			    <td  colspan="3">
			        <input id="YPGNZHZHLXQK" name="YPGNZHZHLXQK" type="text" maxlength="100" style="width:420px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['YPGNZHZHLXQK']; ?>
"/>
			    </td>

			</tr>
			
			<tr>
			    <th>
			       商品条码
			    </th>
			    <td>
			        <input id="SHPTM" name="SHPTM" type="text" maxlength="20" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['SHPTM']; ?>
"/>
			        <input id="HIDDENSHPTM" name="HIDDENSHPTM" type="hidden" value="<?php echo $this->_tpl_vars['rec']['SHPTM']; ?>
"/>
			    </td>
			    
			  <th>
			       国家编码
			    </th>
			    <td>
			        <input id="GJBM" name="GJBM" type="text" maxlength="20" style="width:155px"  class="editable" value="<?php echo $this->_tpl_vars['rec']['GJBM']; ?>
"/>
			    </td>
			  <th>
			       分类编码<font color="red">*</font>
			    </th>
			    <td>
			        <input id="FLBM" name="FLBM" type="text" style="width:155px"   class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['FLBM']; ?>
"/>
			    </td>
 
			</tr>
			
			<tr>
			    <th>
			          是否药品<font color="red">*</font>
			    </th>
			    <td>
					<select id="SHFYP" name="SHFYP" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['shfyaop_opt'],'selected' => $this->_tpl_vars['rec']['SHFYP']), $this);?>

					</select>
			    </td>
			    <th>
			       生产厂家
			    </th>
			    
			    <td>
			        <input id="SHCHCHJ" name="SHCHCHJ" type="text" maxlength="100" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['SHCHCHJ']; ?>
"/>
			    </td>
			        		    <th>
			       助记码
			    </th>
			    <td>
			        <input id="ZHJM" name="ZHJM" type="text" maxlength="25" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['ZHJM']; ?>
"/>
			    </td>
			
			  
	
			</tr>
			
			<tr>
			    <th>
			       化学名
			    </th>
			    <td>
			        <input id="HUAXUEMING" name="HUAXUEMING" type="text" maxlength="100" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['HUAXUEMING']; ?>
"/>
			    </td>
			    <th>
			       常用名
			    </th>
			    <td>
			        <input id="CHYM" name="CHYM" type="text" maxlength="100" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['CHYM']; ?>
"/>
			    </td>
			    <th>
			       俗名
			    </th>
			    <td>
			        <input id="SUMING" name="SUMING" type="text" maxlength="100" style="width:155px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['SUMING']; ?>
"/>
			    </td>
			</tr>
			<tr>
			    <th>
			       剂型
			    </th>
			    <td>
					<select id="JIXING" name="JIXING" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['jixing'],'selected' => $this->_tpl_vars['rec']['JIXING']), $this);?>

					</select>
			    </td>
			    <th>
			       西文名称
			    </th>
			    <td>
			        <input id="XWMCH" name="XWMCH" type="text" maxlength="100" style="width:155px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['XWMCH']; ?>
"/>
			    </td>
			    <th>
			       通用名			    </th>
			    <td>
			        <input id="TYMCH" name="TYMCH" type="text" maxlength="100" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['TYMCH']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       类别
			    </th>
			    <td>
					<select id="LEIBIE" name="LEIBIE" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['leibie'],'selected' => $this->_tpl_vars['rec']['LEIBIE']), $this);?>

					</select>
			    </td>
			    <th>
			       用药分类
			    </th>
			    <td>
					<select id="YYFL" name="YYFL"  style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['yyfl'],'selected' => $this->_tpl_vars['rec']['YYFL']), $this);?>

					</select>
			    </td>
			    <th>
			       处方分类
			    </th>
			    <td>
					<select id="CHFFL" name="CHFFL" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['chffl'],'selected' => $this->_tpl_vars['rec']['CHFFL']), $this);?>

					</select>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       药方判断
			    </th>
			    <td>
					<select id="YFPD" name="YFPD" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['yfpd'],'selected' => $this->_tpl_vars['rec']['YFPD']), $this);?>

					</select>
			    </td>
			    <th>
			       贵重标志
			    </th>
			    <td>
					<select id="GZHBZH" name="GZHBZH" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['gzhbzh_opt'],'selected' => $this->_tpl_vars['rec']['GZHBZH']), $this);?>

					</select>
			    </td>
			    <th>
			       药物成分
			    </th>
			    <td>
					<input id="YWCHF" name="YWCHF" type="text" maxlength="200" style="width:155px"   class="editable"
					value="<?php echo $this->_tpl_vars['rec']['YWCHF']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       商品类型
			    </th>
			    <td>
					<select id="SHPLX" name="SHPLX" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['shplx'],'selected' => $this->_tpl_vars['rec']['SHPLX']), $this);?>

					</select>
			    </td>
			    <th>
			       用法与用量
			    </th>
			    <td>
			        <input id="YFYYL" name="YFYYL" maxlength="200" type="text" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['YFYYL']; ?>
"/>
			    </td>
			    <th>
			       科目号
			    </th>
			    <td>
			        <input id="KEMUHAO" name="KEMUHAO" maxlength="20" type="text" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['KEMUHAO']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			<th>
			       有批准文号 
			    </th>
			           <td>  
                    	<?php if ($this->_tpl_vars['rec']['YPZHWH'] == '1'): ?>
                        <input id="YPZHWH" name="YPZHWH" type="checkbox" checked="checked"/> 
                        <?php else: ?>
                        <input id="YPZHWH" name="YPZHWH" type="checkbox"/>
                        <?php endif; ?>
			           </td>
			    <th>
			       批准文号
			    </th>
			    <td>
			        <input id="PZHWH" name="PZHWH" maxlength="50" type="text" style="width:155px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['PZHWH']; ?>
"/>
			    </td>
			    <th>
			       批准文号有效期
			    </th>
			    <td>
			        <input id="PZHWHYXQ" name="PZHWHYXQ" type="text" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['PZHWHYXQ']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       不良反应
			    </th>
			    <td>
			        <input id="BLFY" name="BLFY" maxlength="200" type="text" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['BLFY']; ?>
"/>
			    </td>
			    <th>
			       禁忌症			    </th>
			    <td>
			        <input id="JJZH" name="JJZH" maxlength="200" type="text" style="width:155px"   class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['JJZH']; ?>
"/>
			    </td>
			    			    <th>
			       适应症
			    </th>
			    <td>
			        <input id="SHYZH" name="SHYZH" maxlength="200" type="text" style="width:155px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['SHYZH']; ?>
"/>
			    </td>
			</tr>
			
			<tr>

			    <th>
			       储存注意事项
			    </th>
			    <td>
			        <input id="CHCZHYSHX" name="CHCZHYSHX" maxlength="200" type="text" style="width:155px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['CHCZHYSHX']; ?>
"/>
			    </td>
			    <th>
			       备注
			    </th>
			    <td>
			        <input id="BEIZHU" name="BEIZHU" maxlength="500" type="text" style="width:155px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['BEIZHU']; ?>
"/>
			    </td>
			    <th>
			       注意事项
			    </th>
			    <td>
			        <input id="ZHYSHX" name="ZHYSHX" maxlength="200" type="text" style="width:155px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['ZHYSHX']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       产地
			    </th>
			    <td>
			        <input id="CHANDI" name="CHANDI" maxlength="100" type="text" style="width:155px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['CHANDI']; ?>
"/>
			    </td>
			    <th>
			       装箱规格
			    </th>
			    <td>
			        <input id="BZHGG" name="BZHGG" maxlength="100" type="text" style="width:155px"  class="editable"
			        value="<?php echo $this->_tpl_vars['rec']['BZHGG']; ?>
"/>
			    </td>
			    <th>
			       经销代销
			    </th>
			    <td>
					<select id="JXDX" name="JXDX" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['jxdx_opt'],'selected' => $this->_tpl_vars['rec']['JXDX']), $this);?>
   
					</select>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       保质期方式
			    </th>
			    <td>
			          <select id="BZHQFSH" name="BZHQFSH" style="width:155px">
                      	<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['bzhqfsh_opt'],'selected' => $this->_tpl_vars['rec']['BZHQFSH']), $this);?>
 
                      </select>
			    </td>
			    <th>
			       保质期月数
			    </th>
			    <td>
			        <input id="BZHQYSH" name="BZHQYSH" maxlength="4" type="text" style="width:155px"  class="editable_num"
			        value="<?php echo $this->_tpl_vars['rec']['BZHQYSH']; ?>
"/>
			    </td>
			    <th>
			       预警天数
			    </th>
			    <td>
					<input id="YJYSH" name="YJYSH" maxlength="4" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['YJYSH']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       正常出厂价
			    </th>
			    <td>
					<input id="ZHCHCHCHJ" name="ZHCHCHCHJ" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['ZHCHCHCHJ']; ?>
"/>
			    </td>
			    <th>
			       含税进价
			    </th>
			    <td>
			        <input id="HSHJJ" name="HSHJJ" maxlength="12" type="text" style="width:155px"  class="editable_num"
			        value="<?php echo $this->_tpl_vars['rec']['HSHJJ']; ?>
"/>
			    </td>
			    <th>
			       进价
			    </th>
			    <td>
					<input id="JINJIA" name="JINJIA" maxlength="16" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['JINJIA']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       含税售价
			    </th>
			    <td>
					<input id="HSHSHJ" name="HSHSHJ" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['HSHSHJ']; ?>
"/>
			    </td>
			    <th>
			       售价
			    </th>
			    <td>
			        <input id="SHOUJIA" name="SHOUJIA" maxlength="16" type="text" style="width:155px"  class="editable_num"
			        value="<?php echo $this->_tpl_vars['rec']['SHOUJIA']; ?>
"/>
			    </td>
			    <th>
			       零售价
			    </th>
			    <td>
					<input id="LSHJ" name="LSHJ" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['LSHJ']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       牌价
			    </th>
			    <td>
					<input id="PAIJIA" name="PAIJIA" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['PAIJIA']; ?>
"/>
			    </td>
			    <th>
			       指导成本价

			    </th>
			    <td>
			        <input id="ZHDCHBJ" name="ZHDCHBJ" maxlength="16" type="text" style="width:155px"  class="editable_num"
			        value="<?php echo $this->_tpl_vars['rec']['ZHDCHBJ']; ?>
"/>
			    </td>
			    <th>
			       最低售价

			    </th>
			    <td>
					<input id="ZDSHJ" name="ZDSHJ" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['ZDSHJ']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       最高售价

			    </th>
			    <td>
					<input id="ZGSHJ" name="ZGSHJ" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['ZGSHJ']; ?>
"/>
			    </td>
			    <th>
			       毛利率

			    </th>
			    <td>
			        <input id="MAOLILV" name="MAOLILV" maxlength="8" type="text" style="width:155px"  class="editable_num"
			        value="<?php echo $this->_tpl_vars['rec']['MAOLILV']; ?>
"/>
			    </td>
			    <th>
			       税率
			    </th>
			    <td>
					<input id="SHUILV" name="SHUILV" maxlength="6" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['SHUILV']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       扣率
			    </th>
			    <td>
					<input id="KOULV" name="KOULV" maxlength="6" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['KOULV']; ?>
"/>
			    </td>
			    <th>
			       成本计算<font color="red">*</font>
			    </th>
			    <td>
					<select id="CHBJS" name="CHBJS" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['chbjs'],'selected' => $this->_tpl_vars['rec']['CHBJS']), $this);?>

					</select>
			    </td>
			    <th>
			       是否饮片
			    </th>
			    <td>
					<select id="SHFYINPIAN" name="SHFYINPIAN" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['shfyp_opt'],'selected' => $this->_tpl_vars['rec']['SHFYINPIAN']), $this);?>

					</select>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       包装单位<font color="red">*</font>
			    </th>
			    <td>
					<select id="BZHDWBH" name="BZHDWBH" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['bzhdw'],'selected' => $this->_tpl_vars['rec']['BZHDWBH']), $this);?>

					</select>
					<input id="BZHDWMCH" name="BZHDWMCH" type="hidden"
			        value="<?php echo $this->_tpl_vars['rec']['BZHDWMCH']; ?>
"/>
			    </td>
			    <th>
			       计量规格<font color="red">*</font>
			    </th>
			    <td>
					<input id="JLGG" name="JLGG" maxlength="6" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['JLGG']; ?>
"/>
			    </td>
				<th>
				      是否OTC
				</th>
				<td>
					<select id="SHFOTC" name="SHFOTC" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['shfotc'],'selected' => $this->_tpl_vars['rec']['SHFOTC']), $this);?>

					</select>
				</td>
			</tr>
			
			<tr>
			    <th>
			       库存上限
			    </th>
			    <td>
					<input id="KCSHX" name="KCSHX" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['KCSHX']; ?>
"/>
			    </td>
			    <th>
			       库存下限
			    </th>
			    <td>
					<input id="KCXX" name="KCXX" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['KCXX']; ?>
"/>
			    </td>
			    <th>
			       合理库存
			    </th>
			    <td>
					<input id="HLKC" name="HLKC" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['HLKC']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       大包装体积
			    </th>
			    <td>
					<input id="DBZHTJ" name="DBZHTJ" maxlength="11" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['DBZHTJ']; ?>
"/>
			    </td>
			    <th>
			       大包装重量
			    </th>
			    <td>
					<input id="DBZHZHL" name="DBZHZHL" maxlength="11" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['DBZHZHL']; ?>
"/>
			    </td>
			    <th>
			       单品重量
			    </th>
			    <td>
					<input id="DPZHL" name="DPZHL" maxlength="11" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['DPZHL']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       单品长
			    </th>
			    <td>
					<input id="DPCH" name="DPCH" maxlength="11" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['DPCH']; ?>
"/>
			    </td>
			    <th>
			       单品宽
			    </th>
			    <td>
					<input id="DANPINKUAN" name="DANPINKUAN" maxlength="11" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['DANPINKUAN']; ?>
"/>
			    </td>
			    <th>
			       单品高
			    </th>
			    <td>
					<input id="DANPINGAO" name="DANPINGAO" maxlength="11" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['DANPINGAO']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       最小单位体积

			    </th>
			    <td>
					<input id="ZXDWTJ" name="ZXDWTJ" maxlength="11" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['ZXDWTJ']; ?>
"/>
			    </td>
			    <th>
			       最小单位重量

			    </th>
			    <td>
					<input id="ZXDWZHL" name="ZXDWZHL" maxlength="11" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['ZXDWZHL']; ?>
"/>
			    </td>
			    <th>
			       配送价
			    </th>
			    <td>
					<input id="PEISONGJIA" name="PEISONGJIA" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['PEISONGJIA']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			    tj单价
			    </th>
			    <td>
					<input id="TJDJ" name="TJDJ" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['TJDJ']; ?>
"/>
			    </td>
			    <th>
			          供货价			    </th>
			    <td>
					<input id="GONGHUOJIA" name="GONGHUOJIA" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['GONGHUOJIA']; ?>
"/>
			    </td>
			    <th>
			       批发价		    </th>
			    <td>
					<input id="PIFAJIA" name="PIFAJIA" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['PIFAJIA']; ?>
"/>
			    </td>
			</tr>			
			
			<tr>
			    <th>
			          重点养护品种类型
			    </th>
			    <td>
					<input id="ZHDYHPZHLX" name="ZHDYHPZHLX" maxlength="10" type="text" style="width:155px"  class="editable"
					value="<?php echo $this->_tpl_vars['rec']['ZHDYHPZHLX']; ?>
"/>
			    </td>
			    <th>
			          订货周期
			    </th>
			    <td>
					<input id="DHZHQ" name="DHZHQ" maxlength="10" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['DHZHQ']; ?>
"/>
			    </td>
			    <th>
			          协议品种
			    </th>
			    <td>
					<input id="XYPZH" name="XYPZH" maxlength="1" type="text" style="width:155px"  class="editable"
					value="<?php echo $this->_tpl_vars['rec']['XYPZH']; ?>
"/>
			    </td>
			</tr>			
			<tr>
			    <th>
			         有效期

			    </th>
			    <td>
					<input id="YOUXIAOQI" name="YOUXIAOQI" maxlength="4" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['YOUXIAOQI']; ?>
"/>
			    </td>
			    <th>
			       出库限制数量
			    </th>
			    <td>
					<input id="CHKXZHSHL" name="CHKXZHSHL" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['CHKXZHSHL']; ?>
"/>
			    </td>
			   <th>
			       入库限制数量
			    </th>
			    <td>
					<input id="RKXZHSHL" name="RKXZHSHL" maxlength="12" type="text" style="width:155px"  class="editable_num"
					value="<?php echo $this->_tpl_vars['rec']['RKXZHSHL']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       指定库区类型
			    </th>
			    <td>
					<select id="ZHDKQLX" name="ZHDKQLX" style="width:155px">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['zhdkqlx'],'selected' => $this->_tpl_vars['rec']['ZHDKQLX']), $this);?>

					</select>
			    </td>
			    <th>
			       缺省库位
			    </th>
			    <td colspan=3>
					<input id="QSKW" name="QSKW" type="text" style="width:420px"  class="readonly" value="<?php echo $this->_tpl_vars['rec']['QSKW']; ?>
" readonly/>
					<input type=hidden id=QSHCKBH name="QSHCKBH" value="<?php echo $this->_tpl_vars['rec']['QSHCKBH']; ?>
" />
					<input type=hidden id="QSHKQBH" name="QSHKQBH" value="<?php echo $this->_tpl_vars['rec']['QSHKQBH']; ?>
" />
					<input type=hidden id="QSHKWBH" name="QSHKWBH" value="<?php echo $this->_tpl_vars['rec']['QSHKWBH']; ?>
" />
			    </td>			    
			</tr>			

			<tr>
		        <th>分店商品标志</th>
		        <td>
		        <?php if ($this->_tpl_vars['rec']['FDSHPBZH'] == '1'): ?>
		        <input id="FDSHPBZH" name="FDSHPBZH" type="checkbox" checked="checked"/>	
		        <?php else: ?>
		        <input id="FDSHPBZH" name="FDSHPBZH" type="checkbox"/>	
		        <?php endif; ?>	        				
			    			    <th>
			       中标价
			    </th>
			    <td colspan=3>
					<input id="ZHBJ" name="ZHBJ" type="text" style="width:155px"  class="editable_num" value="<?php echo $this->_tpl_vars['rec']['ZHBJ']; ?>
"/>
			    </td>
			</tr>
		</table>
                </form>
        </div>

</body>
</html>


<?php /* Smarty version 2.6.26, created on 2011-06-14 15:29:59
         compiled from khzl_02.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'khzl_02.php', 30, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/khzl_02.js"></script>
	
</head>
<body>

    <div id="top">

             <table width="100%" cellpadding="0" cellspacing="0">
               <tr>
                  <td class="title">
                     基础管理-<?php echo $this->_tpl_vars['title']; ?>

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
			<select id="HIDDENSHI" name="HIDDENSHI" style="display:none;">
                   		<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['shi'],'selected' => $this->_tpl_vars['rec']['SZSHI']), $this);?>

                   	</select>   
              <table width="100%" cellpadding="0" cellspacing="1" class="form">
              	         <tr height="0">
		         <td width="100px"></td>
		         <td width="164px"></td>
		         <td width="100px"></td>
		         <td width="164px"></td>
		         <td width="100px"></td>
		         <td></td>
	         </tr>
                <tr>
                    <th>
                   		单位编号<font color="red">*</font></th>
                  	<td>
                  	<?php if ($this->_tpl_vars['action'] == 'update'): ?>
                  	<input type=hidden id="DWBH" name="DWBH" value="<?php echo $this->_tpl_vars['rec']['DWBH']; ?>
" />
                    	<input id="DWBH" name="DWBH" type="text" maxlength="8" style="width: 155px" class="readonly" disabled value="<?php echo $this->_tpl_vars['rec']['DWBH']; ?>
"/>
                    <?php else: ?>
                    <input id="DWBH" name="DWBH" type="text" maxlength="8" style="width: 155px" class="editable"/>
                    <?php endif; ?>
                    </td>
                    <th>
                                                                单位名称<font color="red">*</font></th>
                    <td>
                        <input id="DWMCH" name="DWMCH" type="text" maxlength="100" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['DWMCH']; ?>
"/> 
                    </td>
                    <th>
                       	 助记码</th>
              		<td>
						<input id="ZHJM" name="ZHJM" type="text" maxlength="25" style="width: 155px" class="editable"
						value="<?php echo $this->_tpl_vars['rec']['ZHJM']; ?>
"/>                  </td>
                 </tr>
                 <tr>
                    <th>
                    	科目号</th>
                    <td>
                      <input id="KEMUHAO" name="KEMUHAO" maxlength="20" type="text" style="width: 155px" class="editable"
                      value="<?php echo $this->_tpl_vars['rec']['KEMUHAO']; ?>
"/>                    </td>
                    <th>
                                                                是否销售</th>
                    <td>
                        <select id="SHFXSH" name="SHFXSH" style="width: 150px">
							<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['xiaoshou_opts'],'selected' => $this->_tpl_vars['rec']['SHFXSH']), $this);?>
 
                      	</select>                    
					</td>
					<th>
                       	 是否进货 </th>
                    <td>
                        <select id="SHFJH" name="SHFJH" style="width: 150px">
							<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['gonghuo_opts'],'selected' => $this->_tpl_vars['rec']['SHFJH']), $this);?>
                   		
                      	</select>                    
					</td>
                </tr>
                <tr>
                    <th>
                    	对应首营企业编号</th>
                	<td>
                      <input id="DYSHYQYBH" name="DYSHYQYBH" type="text" maxlength="8" style="width: 155px" class="editable"
                      value="<?php echo $this->_tpl_vars['rec']['DYSHYQYBH']; ?>
"/> <input id="DYSHYQYBH_HIDDEN" name="DYSHYQYBH_HIDDEN" type="hidden" />
				    </td>
                    <th>
                        客户等级</th>
                    <td>
                        <input id="KHDJ" name="KHDJ" type="text" maxlength="2" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['KHDJ']; ?>
"/> 
                  	</td>
					<th>
                        税号</th>
                    <td>
                        <input id="SHUIHAO" name="SHUIHAO" type="text" maxlength="25" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['SHUIHAO']; ?>
"/> 
                  	</td>
                </tr>
                <tr>
					<th>
                        所在省 </th>
                    <td>
                        <select id="SZSH" name="SZSH" style="width: 150px">
                        <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['sheng'],'selected' => $this->_tpl_vars['rec']['SZSH']), $this);?>

                      	</select>                    
					</td>
					<th>
                        所在市 </th>
                    <td>
                        <select id="SZSHI" name="SZSHI" style="width: 150px">
                      		<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['shi'],'selected' => $this->_tpl_vars['rec']['SZSHI']), $this);?>

                      	</select>                    
					</td>
                    <th>
                    	地址</th>
              		<td>
                        <input id="DIZHI" name="DIZHI" type="text" maxlength="200" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['DIZHI']; ?>
"/>
                    </td>
                </tr>
				<tr>
                    <th>
                        电话</th>
                    <td>
                        <input id="DHHM" name="DHHM" type="text" maxlength="20" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['DHHM']; ?>
"/> 
                    </td>
					<th>
                        银行账号</th>
                    <td>
                        <input id="YHZHH" name="YHZHH" type="text" maxlength="25" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['YHZHH']; ?>
"/> 
                    </td>
                    <th>
                    	邮编 </th>
              		<td>
                        <input id="YZHBM" name="YZHBM" type="text" maxlength="6" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['YZHBM']; ?>
"/> 
                    </td>
                </tr>
				<tr>
                    <th>
                        联系人</th>
                    <td>
                        <input id="LXRXM" name="LXRXM" type="text" maxlength="20" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['LXRXM']; ?>
"/> 
                    </td>
					<th>
                        区域分类 </th>
                    <td>
                        <input id="QYFL" name="QYFL" type="text" maxlength="20" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['QYFL']; ?>
"/>
                    </td>
                    <th>
                    	经营范围</th>
              		<td>
                        <input id="JYFW" name="JYFW" type="text" maxlength="500" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['JYFW']; ?>
"/>
                    </td>
                </tr>
				<tr>
                    <th>
                    	是否有许可证</th>
              		<td>
              		<?php if ($this->_tpl_vars['rec']['YXKZH'] == '1'): ?>
                        <input type="checkbox" id="YXKZH" name="YXKZH" 

                        checked="checked">有许可证</input>
                        <?php else: ?>
                        <input type="checkbox" id="YXKZH" name="YXKZH">有许可证</input>
                        <?php endif; ?>
                         
                    </td>
                    <th>
                        许可证号</th>
                    <td>
                        <input id="XKZHH" name="XKZHH" type="text" maxlength="25" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['XKZHH']; ?>
"/> 
                    </td>
					<th>
                        许可证有效期</th>
                    <td>
                        <input id="XKZHYXQ" name="XKZHYXQ" type="text" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['XKZHYXQ']; ?>
"/> 
                    </td>
                </tr>
				<tr>
                    <th>
                    	是否有营业执照</th>
              		<td>
              		<?php if ($this->_tpl_vars['rec']['SHFYYYZHZH'] == '1'): ?>
                        <input type="checkbox" id="SHFYYYZHZH" name="SHFYYYZHZH" checked="checked">有营业执照</input> 
                        <?php else: ?>
                        <input type="checkbox" id="SHFYYYZHZH" name="SHFYYYZHZH">有营业执照</input> 
                        <?php endif; ?>
                    </td>
                    <th>
                        营业执照号 </th>
                    <td>
                        <input id="YYZHZHH" name="YYZHZHH" type="text" maxlength="25" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['YYZHZHH']; ?>
"/> 
                    </td>
					<th>
                        营业执照有效期</th>
                    <td>
                        <input id="YYZHZHYXQ" name="YYZHZHYXQ" type="text" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['YYZHZHYXQ']; ?>
"/> 
                    </td>

                </tr>
				<tr>
                    <th>
                        供货信贷额</th>
                    <td>
                        <input id="GHXDE" name="GHXDE" type="text" maxlength="14" style="width: 155px" class="editable_num"
                        value="<?php echo $this->_tpl_vars['rec']['GHXDE']; ?>
"/> 
                    </td>
					<th>
                        供货信贷期</th>
                    <td>
                        <input id="GHXDQ" name="GHXDQ" type="text" maxlength="5" style="width: 155px" class="editable_num"
                        value="<?php echo $this->_tpl_vars['rec']['GHXDQ']; ?>
"/> 
                    </td>
                    <th>
                    	销售信贷额</th>
              		<td>
                        <input id="XSHXDE" name="XSHXDE" type="text" maxlength="14" style="width: 155px" class="editable_num"
                        value="<?php echo $this->_tpl_vars['rec']['XSHXDE']; ?>
"/> 
                    </td>
                </tr>
				<tr>
                    <th>
                        销售信贷期</th>
                    <td>
                        <input id="XSHXDQ" name="XSHXDQ" type="text" maxlength="5" style="width: 155px" class="editable_num"
                        value="<?php echo $this->_tpl_vars['rec']['XSHXDQ']; ?>
"/> 
                    </td>
					<th>
                        应收上限</th>
                    <td>
                        <input id="YSHSHX" name="YSHSHX" type="text" maxlength="14" style="width: 155px" class="editable_num"
                        value="<?php echo $this->_tpl_vars['rec']['YSHSHX']; ?>
"/> 
                    </td>
                    <th>
                    	预到货天数</th>
              		<td>
                        <input id="YDHTSH" name="YDHTSH" type="text" maxlength="5" style="width: 155px" class="editable_num"
                        value="<?php echo $this->_tpl_vars['rec']['YDHTSH']; ?>
"/> 
                    </td>
                </tr>
				<tr>
                    <th>
                        扣率</th>
                    <td>
                        <input id="KOULV" name="KOULV" type="text" maxlength="6" style="width: 155px" class="editable_num"
                        value="<?php echo $this->_tpl_vars['rec']['KOULV']; ?>
"/> 
                    </td>
					<th>
                        残损扣率</th>
                    <td>
                        <input id="CSKL" name="CSKL" type="text" maxlength="6" style="width: 155px" class="editable_num"
                        value="<?php echo $this->_tpl_vars['rec']['CSKL']; ?>
"/> 
                    </td>
					<th>
                        缺省发货区</th>
                    <td>
                        <select id="FHQBH" name="FHQBH" style="width: 150px">
                        <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['fhq'],'selected' => $this->_tpl_vars['rec']['FHQBH']), $this);?>

                      	</select>   
                    </td>
                </tr>
				<tr>
                    <th>
                        分店标识</th>
                    <td>
                    <?php if ($this->_tpl_vars['rec']['FDBSH'] == '1'): ?>
                        <input id="FDBSH" name="FDBSH" type="checkbox" checked="checked"/> 
                        <?php else: ?>
                        <input id="FDBSH" name="FDBSH" type="checkbox"/>
                        <?php endif; ?>
                    </td>
                    
                <th>
                        客户类别</th>
                    <td>
                        <input id="KHLB" name="KHLB" type="text" maxlength="20" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['KHLB']; ?>
"/> 
                    </td>
                    
                    					<th>
                        行业名称</th>
                    <td>
                        <input id="HYMCH" name="HYMCH" type="text" maxlength="100" style="width: 155px" class="editable"
                        value="<?php echo $this->_tpl_vars['rec']['HYMCH']; ?>
"/> 
                    </td>

                </tr>
                				
                				<tr>
                    <th>
                        是否执行中标价</th>
                    <td>
                    <?php if ($this->_tpl_vars['rec']['SHFZHXZHBJ'] == '1'): ?>
                        <input id="SHFZHXZHBJ" name="SHFZHXZHBJ" type="checkbox" checked="checked"/> 
                        <?php else: ?>
                        <input id="SHFZHXZHBJ" name="SHFZHXZHBJ" type="checkbox"/>
                        <?php endif; ?>
                    </td>
                    
                                        <th>
                        是否执行赠品</th>
                    <td colspan=3>
                    <?php if ($this->_tpl_vars['rec']['SHFZHXZP'] == '1'): ?>
                        <input id="SHFZHXZP" name="SHFZHXZP" type="checkbox" checked="checked"/> 
                        <?php else: ?>
                        <input id="SHFZHXZP" name="SHFZHXZP" type="checkbox"/>
                        <?php endif; ?>
                    </td>


                </tr>
                
                </table>
                </form>
        </div>

</body>
</html>


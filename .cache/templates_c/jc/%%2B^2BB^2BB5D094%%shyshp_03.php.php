<?php /* Smarty version 2.6.26, created on 2011-04-22 16:12:55
         compiled from shyshp_03.php */ ?>
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
/js/shyshp_03.js"></script>
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
           <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
             <tr><td width="100px">
                   	首营商品资料
                </td>
               
            </tr>
            </table>
<input type="hidden" id="orderby" value="<?php echo $this->_tpl_vars['orderby']; ?>
"/>
<input type="hidden" id="direction" value="<?php echo $this->_tpl_vars['direction']; ?>
"/>
<input type="hidden" id="shpbhkey" value="<?php echo $this->_tpl_vars['shpbhkey']; ?>
"/>

<?php endif; ?>
<form name="form1" id="form1" style="display:inline;margin:0px;">
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
			       商品编号<font color="red">*</font>

			    </th>
			    <td>
			        <input type="hidden" id="SHPBH" name="SHPBH" value="<?php echo $this->_tpl_vars['rec']['SHPBH']; ?>
" />
			        <input maxlength="8" type="text" style="width: 155px" value="<?php echo $this->_tpl_vars['rec']['SHPBH']; ?>
" class="readonly" readonly/>
			    </td>
			    <th>
			       商品名称<font color="red">*</font>

			    </th>
			    <td colspan="3">
			        <input id="SHPMCH" name="SHPMCH" type="text" maxlength="100" style="width: 425px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['SHPMCH']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       商品规格
			    </th>
			    <td>
			        <input id="GUIGE" name="GUIGE" type="text" maxlength="20" style="width: 155px" class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['GUIGE']; ?>
"/>
			    </td>
			    <th>
			       药品功能主治疗效
			    </th>
			    <td colspan="3">
			        <input id="YPGNZHZHLXQK" name="YPGNZHZHLXQK" type="text" maxlength="100" style="width: 425px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['YPGNZHZHLXQK']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       有许可证
			    </th>
			           <td>  
			            <?php if ($this->_tpl_vars['rec']['YXKZHSHY'] == '1'): ?>
                        <input id="YXKZHSHY" name="YXKZHSHY" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="YXKZHSHY" name="YXKZHSHY" type="checkbox" disabled/>
                        <?php endif; ?>
                        <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_pic.gif" OnClick="javascript:window.showModalDialog('图片管理.html','','help:no;status:no');"  />
			           </td>
                            
                <th>
			       许可证号
			    </th>
			    <td>
			        <input id="XKZHHSHY" name="XKZHHSHY" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['XKZHHSHY']; ?>
"/>
			    </td>
			    <th>
			       许可证有效期
			    </th>
			    <td>
			        <input id="XKZHYXQSHY" name="XKZHYXQSHY" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['XKZHYXQSHY']; ?>
"/>
			    </td>
			</tr>

			<tr>
			    <th>
			       有营业执照 
			    </th>

			           <td>  
			            <?php if ($this->_tpl_vars['rec']['YYYZHZHSHY'] == '1'): ?>
                        <input id="YYYZHZHSHY" name="YYYZHZHSHY" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="YYYZHZHSHY" name="YYYZHZHSHY" type="checkbox" disabled/>
                        <?php endif; ?>
                        <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_pic.gif" OnClick="javascript:window.showModalDialog('图片管理.html','','help:no;status:no');"  />
			           </td>
		    <th>
			       营业执照号

			    </th>
			    <td>
			        <input id="YYZHZHHSHY" name="YYZHZHHSHY" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['YYZHZHHSHY']; ?>
"/>
			    </td>
			    <th>
			       营业执照有效期

			    </th>
			    <td>
			        <input id="YYZHZHYXQSHY" name="YYZHZHYXQSHY" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['YYZHZHYXQSHY']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       有批准文号 
			    </th>
			           <td>  
                    	<?php if ($this->_tpl_vars['rec']['YPZHWHSHY'] == '1'): ?>
                        <input id="YPZHWHSHY" name="YPZHWHSHY" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="YPZHWHSHY" name="YPZHWHSHY" type="checkbox" disabled/>
                        <?php endif; ?>
			           </td>
			    <th>
			       批准文号
			    </th>
			    <td>
			        <input id="PZHWHSHY" name="PZHWHSHY" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['PZHWHSHY']; ?>
"/>
			    </td>
			    <th>
			       批准文号有效期

			    </th>
			    <td>
			        <input id="PZHWHYXQSHY" name="PZHWHYXQSHY" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['PZHWHYXQSHY']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       符合质量标准
			    </th>
			           <td>  
			            <?php if ($this->_tpl_vars['rec']['FHZHLBZH'] == '1'): ?>
                        <input id="FHZHLBZH" name="FHZHLBZH" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="FHZHLBZH" name="FHZHLBZH" type="checkbox" disabled/>
                        <?php endif; ?>
			           </td>
			    <th>
			       质量标准
			    </th>
			    <td>
			        <input id="ZHLBZH" name="ZHLBZH" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['ZHLBZH']; ?>
"/>
			    </td>
			    <th>
			       有小包装
			    </th>
			           <td>  
			            <?php if ($this->_tpl_vars['rec']['YXBZH'] == '1'): ?>
                        <input id="YXBZH" name="YXBZH" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="YXBZH" name="YXBZH" type="checkbox" disabled/>
                        <?php endif; ?>
			           </td>
			</tr>
			
			<tr>
			    <th>
			       有注册商标

			    </th>
			           <td>  
			            <?php if ($this->_tpl_vars['rec']['YZHCSHB'] == '1'): ?>
                        <input id="YZHCSHB" name="YZHCSHB" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="YZHCSHB" name="YZHCSHB" type="checkbox" disabled/>
                        <?php endif; ?>
			           </td>
			    <th>
			       注册商标
			    </th>
			    <td>
			        <input id="ZHCSHB" name="ZHCSHB" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['ZHCSHB']; ?>
"/>
			    </td>
			    <th>
			       有标签

			    </th>
			           <td>  
			            <?php if ($this->_tpl_vars['rec']['YOUBIAOQIAN'] == '1'): ?>
                        <input id="YOUBIAOQIAN" name="YOUBIAOQIAN" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="YOUBIAOQIAN" name="YOUBIAOQIAN" type="checkbox" disabled/>
                        <?php endif; ?>
			           </td>
			</tr>
			
			<tr>
			    <th>
			       有说明书
			    </th>
			           <td>  
			            <?php if ($this->_tpl_vars['rec']['YSHMSH'] == '1'): ?>
                        <input id="YSHMSH" name="YSHMSH" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="YSHMSH" name="YSHMSH" type="checkbox" disabled/>
                        <?php endif; ?>
			           </td>
			    <th>
			       有样品

			    </th>
			           <td>  
			            <?php if ($this->_tpl_vars['rec']['YOUYANGPIN'] == '1'): ?>
                        <input id="YOUYANGPIN" name="YOUYANGPIN" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="YOUYANGPIN" name="YOUYANGPIN" type="checkbox" disabled/>
                        <?php endif; ?>
			           </td>
			    <th>
			       GMP达标
			    </th>
			           <td>  
			            <?php if ($this->_tpl_vars['rec']['GMPDB'] == '1'): ?>
                        <input id="GMPDB" name="GMPDB" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="GMPDB" name="GMPDB" type="checkbox" disabled/>
                        <?php endif; ?>
			           </td>
			</tr>
			
			<tr>
			    <th>
			       供商单位
			    </th>
			    <td>
			        <input id="GSHDW" name="GSHDW" maxlength="100" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['GSHDW']; ?>
"/>
			    </td>
			    <th>
			       存储条件
			    </th>
			    <td>
			        <input id="CCHTJ" name="CCHTJ" maxlength="500" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['CCHTJ']; ?>
"/>
			    </td>
			    <th>
			       工厂负责期

			    </th>
			    <td>
			        <input id="GCHFZQ" name="GCHFZQ" maxlength="5" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['GCHFZQ']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       申请部门
			    </th>
			    <td>
			        <input id="BMMCH" name="BMMCH" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['BMMCH']; ?>
"/>
			    </td>
			    <th>
			       开始日期

			    </th>
			    <td>
			        <input id="KSHRQ" name="KSHRQ" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['KSHRQ']; ?>
"/>
			    </td>
			    <th>
			       申请原因
			    </th>
			    <td>
			        <input id="SHQYY" name="SHQYY" maxlength="500" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['SHQYY']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       采购员意见

			    </th>
			    <td>
			        <input id="CGYYJ" name="CGYYJ" maxlength="500" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['CGYYJ']; ?>
"/>
			    </td>
			    <th>
			       业务部门主管意见
			    </th>
			    <td>
			        <input id="YWBMZHGYJ" name="YWBMZHGYJ" maxlength="500" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['YWBMZHGYJ']; ?>
"/>
			    </td>
			    			    <th>
			       质量部门意见
			    </th>
			    <td>
			        <input id="ZHLBMYJ" name="ZHLBMYJ" maxlength="500" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['ZHLBMYJ']; ?>
"/>
			    </td>
			</tr>
			
			<tr>

			    <th>
			       物价部门意见
			    </th>
			    <td>
			        <input id="WJBMYJ" name="WJBMYJ" maxlength="500" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['WJBMYJ']; ?>
"/>
			    </td>
			    <th>
			       经理审批意见
			    </th>
			    <td>
			        <input id="JLSHPYJ" name="JLSHPYJ" maxlength="500" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['JLSHPYJ']; ?>
"/>
			    </td>
			    <th>
			       处理情况
			    </th>
			    <td>
			        <input id="CHLQK" name="CHLQK" maxlength="500" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['CHLQK']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       审批结果
			    </th>
			    <td>
			        <input id="SHPJG" name="SHPJG" maxlength="500" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['SHPJG']; ?>
"/>
			    </td>
			    <th>
			       审批日期
			    </th>
			    <td>
			        <input id="SHPRQ" name="SHPRQ" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['SHPRQ']; ?>
"/>
			    </td>
			    <th>
			       审批资料档案号

			    </th>
			    <td>
			        <input id="SHPZLDAH" name="SHPZLDAH" maxlength="20" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['SHPZLDAH']; ?>
"/>
			    </td>
			</tr>
			
			<tr>
			    <th>
			       证照档案号

			    </th>
			    <td>
			        <input id="ZHZHDAH" name="ZHZHDAH" maxlength="20" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['ZHZHDAH']; ?>
"/>
			    </td>
			    <th>
			       摘要
			    </th>
			    <td>
			        <input id="ZHAIYAO" name="ZHAIYAO" maxlength="500" type="text" style="width: 155px"  class="readonly" readonly
			        value="<?php echo $this->_tpl_vars['rec']['ZHAIYAO']; ?>
"/>
			    </td>
			    <th>
			       审批通过
			    </th>
			           <td>  
			            <?php if ($this->_tpl_vars['rec']['SHPTG'] == '1'): ?>
                        <input id="SHPTG" name="SHPTG" type="checkbox" checked="checked" disabled/> 
                        <?php else: ?>
                        <input id="SHPTG" name="SHPTG" type="checkbox" disabled/>
                        <?php endif; ?>
			           </td>
			</tr>
                </form>
<?php if ($this->_tpl_vars['full_page']): ?>
</div>

</body>
</html>
<?php endif; ?>


<?php
class Common_Validate {
	
	protected $_options; //验证规则
	protected $_datas;   //验证数据
	protected $_result;  //验证结果
	protected $_message = array('required'=>'必须输入项',
	                            'email'=>'请输入正确格式的电子邮件',
	                            'date'=>'请输入合法的日期',
	                            'zipcode'=>'请输入正确格式的邮政编码',
	                            'telphone'=>'请输入正确格式的电话号码',
	                            'mobilephone'=>'请输入正确格式的移动电话号码',
	                            'numberletterunderline'=>'只能输入数字，字母或者下划线',
	                            'number'=>'请输入合法的数字',
	                            'digits'=>'请输入一个整数',
	                            'maxlength'=>'请输入一个长度最多是 {0} 的字符串',
	                            'minlength'=>'请输入一个长度最少是 {0} 的字符串',
	                            'rangelength'=>'请输入一个长度介于 {0} 和 {1} 之间的字符串',
	                            'range'=>'请输入一个介于 {0} 和 {1} 之间的值',
	                            'max'=>'请输入一个最大为 {0} 的值',
	                            'min'=>'请输入一个最小为 {0} 的值',
	                            'equalTo'=>'两次输入值不相同');

	public function __construct($datas,$options) {
		$this->_datas = $datas;
		$this->_options = $options;
		$this->_result = array();
	
	}
	
	/*
	 * 对输入值进行检查
	 */
	public function check() {
			
		//循环验证规则进行检查
		foreach ( $this->_options as $option ) {
			foreach ( $option ['rules'] as $rule ) {
				$tmp = split(':',$rule);
				$function = $tmp[0];
				$param = $tmp[1];
				$this->$function ($option['element'],$option['message'],$function,$param);
			
			}
		
		}

		
		$return ="";
		foreach ($this->_result as $result)
		{
			$return = $return.'<br>'.$result;
		}
		
		return $return;
	
	}

/*
	 * 必须输入项检查
	 */
	public function required($element,$message,$rule) {
		$validator = new Zend_Validate_NotEmpty();
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.$message;
			array_push($this->_result,$error);
		}
	
	}

	/*
	 * 电子邮件格式
	 */
	public function email($element,$message,$rule) {
		
		if(empty($this->_datas[$element['id']])) return ;
		
	    $validator = new Zend_Validate_EmailAddress();
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.$message;
			array_push($this->_result,$error);
		}
	}
	
	/*
	 * 日期验证
	 */
	public function date($element,$message,$rule) {
		if(empty($this->_datas[$element['id']])) return ;
	    $validator = new Zend_Validate_Date('YYYY/MM/DD');
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.$message;
			array_push($this->_result,$error);
		}
	}
	
	
	/*
	 * 邮政编码
	 */
	public function zipcode($element,$message,$rule) {
		if(empty($this->_datas[$element['id']])) return ;
	  $validator = new Zend_Validate_Regex('/^[0-9]{6}$/');
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.$message;
			array_push($this->_result,$error);
		}
	
	}
	
	/*
	 * 固定电话
	 */
	public function telphone($element,$message,$rule) {
		if(empty($this->_datas[$element['id']])) return ;
	  $validator = new Zend_Validate_Regex('/^([+]{0,1}\d{2,3}\-)?(0\d{2,3}\-)?[1-9]\d{6,7}$/');
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.$message;
			array_push($this->_result,$error);
		}
	
	}
	
	/*
	 * 移动电话
	 */
	public function mobilephone($element,$message,$rule) {
		if(empty($this->_datas[$element['id']])) return ;
	  $validator = new Zend_Validate_Regex('/(^0?13[0,1,2,3,4,5,6,7,8,9]\d{8}$)|(^0?15[8,9,1,2,3,4,5,6,7]\d{8}$)/');
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.$message;
			array_push($this->_result,$error);
		}
	
	}
	
	/*
	 * 数字字母下划线
	 */
	public function numberletterunderline($element,$message,$rule) {
		if(empty($this->_datas[$element['id']])) return ;
	  $validator = new Zend_Validate_Regex('/^\w*$/');
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.$message;
			array_push($this->_result,$error);
		}
	
	}
	
	/*
	 * 数字
	 */
	public function number($element,$message,$rule) {
		if(empty($this->_datas[$element['id']])) return ;
	  $validator = new Zend_Validate_Regex('/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/');
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.$message;
			array_push($this->_result,$error);
		}
	
	}
	
		/*
	 * 整数
	 */
	public function digits($element,$message,$rule) {
		if(empty($this->_datas[$element['id']])) return ;
	  $validator = new Zend_Validate_Digits();
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.$message;
			array_push($this->_result,$error);
		}
	
	}
	
	/*
	 * 最大长度字符串
	 */
	public function maxlength($element,$message,$rule,$param) {
		if(empty($this->_datas[$element['id']])) return ;
	  $validator = new Zend_Validate_StringLength(0,$param);
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.   str_replace('{0}',$param,$message);
			array_push($this->_result,$error);
		}
	
	}
	
	/*
	 * 最小长度字符串
	 */
	public function minlength($element,$message,$rule,$param) {
		if(empty($this->_datas[$element['id']])) return ;
	  $validator = new Zend_Validate_StringLength($param);
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.   str_replace('{0}',$param,$message);
			array_push($this->_result,$error);
		}
	
	}
	
	/*
	 * 范围长度字符串
	 */
	public function rangelength($element,$message,$rule,$param) {
		if(empty($this->_datas[$element['id']])) return ;
        $param = split(',',$param);
		
	    $validator = new Zend_Validate_StringLength($param[0],$param[1]);
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.   str_replace('{0}',$param[0],str_replace('{1}',$param[1],$message));
			array_push($this->_result,$error);
		}
	
	}
	
	
/*
	 * 最大值
	 */
	public function max($element,$message,$rule,$param) {
		if(empty($this->_datas[$element['id']])) return ;
	  $validator = new Zend_Validate_LessThan($param);
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.   str_replace('{0}',$param,$message);
			array_push($this->_result,$error);
		}
	
	}
	
	/*
	 * 最小值
	 */
	public function min($element,$message,$rule,$param) {
		if(empty($this->_datas[$element['id']])) return ;
	  $validator = new Zend_Validate_LessThan($param);
				
		if($validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.   str_replace('{0}',$param,$message);
			array_push($this->_result,$error);
		}
	
	}
	
	/*
	 * 范围
	 */
	public function range($element,$message,$rule,$param) {
		if(empty($this->_datas[$element['id']])) return ;
        $param = split(',',$param);
		
	    $validator = new Zend_Validate_Between($param[0],$param[1]);
				
		if(!$validator->isValid($this->_datas[$element['id']]))
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.   str_replace('{0}',$param[0],str_replace('{1}',$param[1],$message));
			array_push($this->_result,$error);
		}
	
	}
	
	/*
	 * 两值相等
	 */
	public function equalTo($element,$message,$rule,$param) {
				
		if($this->_datas[$element['id']] !=$this->_datas[$param] )
		{
			//没有自定义message则用默认message
		    $message = isset($message[$rule])? $message[$rule]:$this->_message[$rule];
		    $error = $element['name'].':'.   str_replace('{0}',$param[0],str_replace('{1}',$param[1],$message));
			array_push($this->_result,$error);
		}
	
	}
	


}
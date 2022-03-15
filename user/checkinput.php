<?php

define(SAE_MYSQL_HOST_M, '127.0.0.1');
define(SAE_MYSQL_PORT, 3306);
define(SAE_MYSQL_USER, 'root');
define(SAE_MYSQL_PASS, '123456');
define(SAE_MYSQL_DB, 'dbCoderChain');

/**
	 * 过滤sql注入
	 * @param  string $value 字符串
	 * @return string        过滤完成的字符串
	 * @return boolean       是否用于insert
	 */
function check_input($value, $bInsert = false) {
		
	    if ( !get_magic_quotes_gpc() ) {
	        $value = stripslashes( $value );
	    }

	    if ( ! is_numeric( $value ) ) {
	        $value =  @mysql_escape_string( $value );
	    }

	    if (!$bInsert) {
	    	 $value = str_replace("_", "/_", $value);
			$value = str_replace("%", "/%", $value);
	    }

	    $value = htmlspecialchars($value);
      
	    return $value;
	}
?>
<?php

namespace gp_brief\components;

class Logger
{
	function write_log($log,$dump=false) {
        if ($dump) {
        	ob_start();                    // start buffer capture
		    var_dump( $log );           // dump the values
		    $contents = ob_get_contents(); // put the buffer into a variable
		    ob_end_clean();                // end capture
		    error_log( $contents );        // log contents of the result of var_dump( $object )
        } else {
        	if (is_array($log) || is_object($log)) {
	            error_log(print_r($log, true));
	        } else {
	            error_log($log);
	        }
        }     
    }
}

?>
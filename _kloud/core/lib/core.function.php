<?php

function kloud_version()
{
	return '0.1.0';
}

function transcribe($aList, $aIsTopLevel = true) 
{
   $gpcList = array();
   $isMagic = get_magic_quotes_gpc();
  
   foreach ($aList as $key => $value) {
       if (is_array($value)) {
           $decodedKey = ($isMagic && !$aIsTopLevel)?stripslashes($key):$key;
           $decodedValue = transcribe($value, false);
       } else {
           $decodedKey = stripslashes($key);
           $decodedValue = ($isMagic)?stripslashes($value):$value;
       }
       $gpcList[$decodedKey] = $decodedValue;
   }
   return $gpcList;
}

$_GET = transcribe( $_GET ); 
$_POST = transcribe( $_POST ); 
$_REQUEST = transcribe( $_REQUEST );


function v( $str )
{
	return isset( $_REQUEST[$str] ) ? $_REQUEST[$str] : false;
}

function z( $str )
{
	return strip_tags( $str );
}

function c( $str )
{
	return isset( $GLOBALS['konfig'][$str] ) ? $GLOBALS['konfig'][$str] : false;
}

function g( $str )
{
	return isset( $GLOBALS[$str] ) ? $GLOBALS[$str] : false;	
}

function t( $str )
{
	return trim($str);
}

function u( $str )
{
	return urlencode( $str );
}

function uid()
{
	return intval($_SESSION['uid']);
}

function uname()
{
	return t($_SESSION['uname']);
}


function wintval( $string )
{
	$array = str_split( $string );
	$ret = '';
	foreach( $array as $v )
	{
		if( is_numeric( $v ) ) $ret .= intval( $v );
	}
	
	return $ret;
}


if (!function_exists('apache_request_headers')) 
{ 
	function apache_request_headers()
	{ 
		foreach($_SERVER as $key=>$value)
		{ 
			if (substr($key,0,5)=="HTTP_")
			{ 
				$key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
                    $out[$key]=$value; 
			}
			else
			{ 
				$out[$key]=$value; 
			}
       } 
       
	   return $out; 
   } 
}


function is_ajax_request()
{
	$headers = apache_request_headers();
	return (isset( $headers['X-Requested-With'] ) && ( $headers['X-Requested-With'] == 'XMLHttpRequest' )) || (isset( $headers['x-requested-with'] ) && ($headers['x-requested-with'] == 'XMLHttpRequest' ));
}

function is_json_request()
{
	$headers = apache_request_headers();
	return (isset( $headers['Content-Type'] ) && ( clean_header($headers['Content-Type']) == 'application/json' ));
}



function is_mobile_request()
{
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
 
    $mobile_browser = '0';
 
    if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
        $mobile_browser++;
 
    if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))
        $mobile_browser++;
 
    if(isset($_SERVER['HTTP_X_WAP_PROFILE']))
        $mobile_browser++;
 
    if(isset($_SERVER['HTTP_PROFILE']))
        $mobile_browser++;
 
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
    $mobile_agents = array(
                        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
                        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
                        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
                        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
                        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
                        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
                        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
                        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
                        'wapr','webc','winw','winw','xda','xda-'
                        );
 
    if(in_array($mobile_ua, $mobile_agents))
        $mobile_browser++;
 
    if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
        $mobile_browser++;
 
    // Pre-final check to reset everything if the user is on Windows
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        $mobile_browser=0;
 
    // But WP7 is also Windows, with a slightly different characteristic
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
        $mobile_browser++;
 
    if($mobile_browser>0)
        return true;
    else
        return false;
}

function uses( $m )
{
	load( 'lib/' . basename($m)  );
}

function load( $file_path ) 
{
	$file = AROOT . $file_path;
	if( file_exists( $file ) )
	{
		//echo $file;
		require( $file );
	
	}
	else
	{
		//echo CROOT . $file_path;
		require( CROOT . $file_path );
	}
	
}

// ===========================================
// load db functions
// ===========================================

if( function_exists('mysqli_connect') )
	$dbfile_postfix = '.mysqli.function.php';
else
	$dbfile_postfix = '.function.php';

if( defined('SAE_APPNAME') )
	include_once( CROOT .  'lib/db.sae'. $dbfile_postfix );
else
	include_once( CROOT .  'lib/db' . $dbfile_postfix );
	

// **************************************************************
// * Plugins & hooks
// ************************************************************** 
function add_filter( $tag , $function_to_add , $priority = 10 , $accepted_args_num = 1 )
{
    return add_hook( $tag , $function_to_add , $priority , $accepted_args_num );
}

function add_action( $tag , $function_to_add , $priority = 10 , $accepted_args_num = 1 )
{
    return add_hook( $tag , $function_to_add , $priority , $accepted_args_num );
}

function add_hook( $tag , $function_to_add , $priority = 10 , $accepted_args_num = 1 )
{
    $tag = strtoupper($tag);
    $idx = build_hook_id( $tag , $function_to_add , $priority );
    $GLOBALS['KLOUD_HOOK'][$tag][$priority][$idx] = array( 'function' => $function_to_add , 'args_num' => $accepted_args_num );
}

function do_action( $tag , $value = null )
{
    return apply_hook( $tag , $value );
}

function apply_filter( $tag , $value = null )
{
    return apply_hook( $tag , $value );
}



function apply_hook( $tag , $value )
{
    $tag = strtoupper($tag);
    if( $hooks  = has_hook( $tag ) )
    {
        ksort( $hooks );
        $args = func_get_args();
        reset( $hooks );

        do
        {
            foreach( (array) current( $hooks ) as $hook )
            {
                if( !is_null($hook['function']) )
                {
                    $args[1] = $value;
                    $value = call_user_func_array( $hook['function'] , array_slice($args, 1, (int) $hook['args_num']));
                }
            }
        }while( next( $hooks ) !== false );

    }

    return $value;
}

function has_hook( $tag , $priority = null )
{
    $tag = strtoupper($tag);
    if( is_null($priority) ) return isset( $GLOBALS['KLOUD_HOOK'][$tag] )? $GLOBALS['KLOUD_HOOK'][$tag]:false;
    else return isset( $GLOBALS['KLOUD_HOOK'][$tag][$priority] )? $GLOBALS['KLOUD_HOOK'][$tag][$priority]:false;
}

function remove_hook( $tag , $priority = null )
{
    $tag = strtoupper($tag);
    if( is_null($priority) ) unset( $GLOBALS['KLOUD_HOOK'][$tag] );
    else unset( $GLOBALS['KLOUD_HOOK'][$tag][$priority] );
}
// This function is based on wordpress  
// from  https://raw.github.com/WordPress/WordPress/master/wp-includes/plugin.php
// requere php5.2+

function build_hook_id( $tag , $function ) 
{
    if ( is_string($function) )
        return $function;

    if ( is_object($function) ) 
    {
        // Closures are currently implemented as objects
        $function = array( $function, '' );
    }
    else
    {
        $function = (array) $function;
    }

    if (is_object($function[0]) ) 
    {
        // Object Class Calling
        if ( function_exists('spl_object_hash') ) 
        {
            return spl_object_hash($function[0]) . $function[1];
        }
        else
        {
            return substr( serialize($function[0]) , 0 , 15 ). $function[1];
        }

    }
    elseif( is_string($function[0]) )
    {
        // Static Calling
        return $function[0].$function[1];
    }
}



// input check
function input_check( $value , $filter_func , $info = null )
{
	/*$tf = function( $filter_func , $value )
	{
		return ;
	};*/

	if( ($ret = call_user_func( $filter_func , $value )) !== false )
	{
		return $ret;
	}
	else
	{
		// exception
		exit;
	}
}

function not_empty( $value )
{
	if( strlen( $value ) < 1 ) return false;
	else return $value ;
}	

function is_mail( $value )
{
	if( filter_var($value, FILTER_VALIDATE_EMAIL) === false ) 
		return false;
	else
		return $value;
}





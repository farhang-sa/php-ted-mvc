<?php

namespace Ted ;

// Package Version 
define( 'TVersion' , 'v1' );

// Set Ini Use Cookies
ini_set('session.cookie_secure', 0 );

// Set Default TimeZone To UTC
date_default_timezone_set( 'UTC' );

// Set internal character encoding to UTF-8
mb_internal_encoding( 'UTF-8' );

// Set HTTP Output Document's Character Encoding To UTF-8
mb_http_output( 'UTF-8' );

// Check Start Of The Execution , TExec Not Defined , Quit The Application
defined( 'TExec' ) or die( 'Access Denied' );

// Check The Directory Separator
defined( 'TPath_DS' ) or define( 'TPath_DS' , DIRECTORY_SEPARATOR );

// Check The Ted Base Constant
defined( 'TPath_Base' ) or define( 'TPath_Base' , realpath( __DIR__ ) );

// Section Below Loades Every Deirectory In TPath_Base And Sets 
// A Constant Like "TPath_BaseName" For It's Directory Address
$scn = scandir( TPath_Base );

foreach( $scn as $DirName ){
	
	$newDirAddress = TPath_Base . TPath_DS . $DirName ;
	
	$continue = false ;
	
	if ( $DirName == "." || $DirName == ".." ) $continue = true ;
	
	else if ( ! is_dir( $newDirAddress ) ) $continue = true ;
	
	if ( $continue ) continue ;
	
	$newConsName = 'TPath_Base' . ucfirst( $DirName ) ;
	
	if ( ! defined( $newConsName ) ) define( $newConsName , $newDirAddress );
	
}

// Load Ted Basic Functions
include_once( "Statics/Functions.php" );

// Define The Root Execution Directory
defined( 'TPath_Root' ) or define( 'TPath_Root' , dirname( ScriptFile() ) );

// Define The Original Execution File
defined( 'TPath_IndexPath' ) or define( 'TPath_IndexPath' , TPath_Root . TPath_DS . basename( ScriptFile() ) );

// Define The Original Execution File
defined( 'TPath_Index' ) or define( 'TPath_Index' , basename( TPath_IndexPath ) );
defined( 'TPath_IndexFile' ) or define( 'TPath_IndexFile' , TPath_Index );

// the TWeb_Schame constant : http/https
define( 'TWeb_Schame' , WebSchame() );

// the TWeb_Domain constant : ted.com
define( 'TWeb_Domain' , WebDomain() ); 

// the TWeb_HttpDomain constant : https://ted.com
define( 'TWeb_HttpDomain' , TWeb_Schame . "://" . TWeb_Domain );

// the TWeb_DomainAccess constant : http://user:pass@ted.com:8081
define( 'TWeb_DomainAccess' , WebDomainAccess() ); 

// the TWeb_Path constant : /tedpath/
define( 'TWeb_Path' , WebPath() );
define( 'TWeb_Script' , TWeb_Path );
define( 'TWeb_ScriptPath' , TWeb_Path );

// the TWeb_URL constant : http://user:pass@ted.com:8081/tedpath/
$path = TWeb_DomainAccess . TWeb_Path ;
$path = trim( $path , " \\/" );
define( 'TWeb_URL' , $path ); 
define( 'TWeb_url' , TWeb_URL ); 
define( 'TWeb_UrlRoot' , TWeb_URL ); 
define( 'TWeb_Urlroot' , TWeb_URL );
define( 'TWeb_urlRoot' , TWeb_URL );
define( 'TWeb_urlroot' , TWeb_URL );
define( 'TWeb_URLROOT' , TWeb_URL );
define( 'TWeb_WWWRoot' , TWeb_URL ); 
define( 'TWeb_WwwRoot' , TWeb_URL ); 
define( 'TWeb_wwwRoot' , TWeb_URL ); 
define( 'TWeb_wwwroot' , TWeb_URL ); 
define( 'TWeb_WWWROOT' , TWeb_URL ); 

// the TWeb_Index constant : http://user:pass@ted.com:8081/tedpath/index-file.php
define( 'TWeb_Index' , trim( TWeb_URL . "/" . TPath_Index , " \\/" ) ); 

define( 'TAccess' 			, " Access Denied To This Path" );
define( 'TDisable' 			, " Requested File Is Not Available" );
define( 'TClassDamaged' 	, " Requested Class Is Damaged" );
define( 'TClassMissing' 	, " Requested Class Is Not Available" );

// Load System Statics
Import( 'Base.Statics.Intel' );

// Load System Abstracts
Import( 'Base.Abstracts.*' );

// Initialise Ted's Static Classes
Intel::Initialise(); /// User Related Intel Worker

// Ted Information Class
class Ted {

	public static function printStyle(){
		print '<style>';
		print "table.ted {border:1px solid black;border-collapse: collapse;direction:ltr;display: block;overflow-x: auto;}\n";
		print "table.ted th {border:2px solid black;padding:5px;font-size:20px;}\n";
		print "table.ted td {border:2px solid black;padding:2.5px;padding-left:20px;text-align:left;}\n";
		print '</style>' ;
	}

	public static function phpinfo(){ self::info(); }
	public static function infophp(){ self::info(); }
	public static function info(){

		// style
		self::printStyle();
		
		// create table
		print '<table class="table table-striped ted">' ;
		print '<tr"><th colspan=2>Basic PHPINFO()</th></tr>';
		print '<tr><th>Item</th><th>Value</th></tr>';
		
		// PHPINFO Definitions
		$exten = get_loaded_extensions();
		$extel = "" ;
		for($i = 0 ; $i <= count( $exten ) - 1 ; $i++ ) {
			$extel .= $exten[$i] . ' - ' ;
			if( $i !== 0 && $i%5 === 0 )
				$extel = trim( $extel , ' -' ) . '<br />' ;
		} $extel = trim( $extel , ' -' );
		$execs = ExecFunctions();
		$execf = ! empty( $execs ) ? $execs[0] : null ;
		$vars = array( 
			'Machine' => str_ireplace( gethostname() , '' , php_uname() ) ,
			'Machine Name/IP' => gethostname() . ' / ' . Intel::GetVar( 'SERVER_ADDR' , 'UNKOWN IP' , 'SERVER' ) ,
			'Server' => Intel::GetVar( 'SERVER_SOFTWARE' , 'UNKOWN Server Software' , 'SERVER' ) ,
			'Who Am I' => get_current_user() ,
			'PHP Version' => defined( 'PHP_VERSION' ) ? PHP_VERSION : phpversion() ,
			'<b>Exec</b>' => '<b>' . join( ' - ' , $execs ) . '</b>' ,
			'Extensions' => $extel  
		);
		$ini = array( 
			'allow_url_fopen' , 'allow_url_include' , 
			'max_execution_time' , 'max_input_time' , 
			'post_max_size' , 'upload_max_filesize' , 'max_file_uploads' );
		if( $ini ) foreach ($ini as $item ) {
			$val = ini_get( $item );
			if( ! $val ) $val = '-----';
			$vars[ 'INI : ' . $item . '' ] = $val ;
		}

		$a = shell_exec( 'composer --version' );
		$cmd = array( 'git' , 'composer' , 'mysqld' , 'postgres' , 'mariadb' , 'python' , 'java' , 'node' , 'npm' );
		if( $execf ) foreach ($cmd as $tool ) {
			$exe = null ;
			$exf = null ;
			@$execf( $tool . ' --version' , $exe  , $exf );
			$exe = empty( $exe ) ? '-----' : $exe[ count( $exe ) - 1 ];
			if( stristr( $exe , '--version' ) )
				$exe = explode( '--version' , $exe )[1] ;
			$exe = trim( $exe , ' -' );
			$exe = str_ireplace( "\n", '<br />', $exe );
			$vars[ $tool ] = $exe ;
		}


		foreach( $vars as $name => $val )
			print "<tr><td>{$name}</td><td>{$val}</td></tr>" ;

		// Ted Definitions
		print '<tr"><th colspan=2>Ted Definitions</th></tr>';
		print '<tr><th>Defined</th><th>Value</th></tr>';
		$vars = array( 
			'Ted Version' 		=> TVersion ,
			'TPath_Base' 		=> TPath_Base ,
			'TPath_Root' 	  	=> TPath_Root ,
			'TPath_IndexPath' 	=> TPath_IndexPath ,
			'TPath_Index' 	  	=> TPath_Index ,
			'TPath_IndexFile' 	=> TPath_IndexFile ,
			'TWeb_Schame' 	  	=> TWeb_Schame ,
			'TWeb_Domain' 		=> TWeb_Domain ,
			'TWeb_HttpDomain' 	=> TWeb_HttpDomain ,
			'TWeb_DomainAccess' => TWeb_DomainAccess ,
			'TWeb_Path' 		=> TWeb_Path ,
			'TWeb_Script' 		=> TWeb_Script ,
			'TWeb_ScriptPath' 	=> TWeb_ScriptPath ,
			'TWeb_URL' 			=> TWeb_URL ,
			'TWeb_url' 			=> TWeb_url ,
			'TWeb_UrlRoot' 		=> TWeb_UrlRoot ,
			'TWeb_Urlroot' => TWeb_Urlroot ,
			'TWeb_urlRoot' => TWeb_urlRoot ,
			'TWeb_urlroot' => TWeb_urlroot ,
			'TWeb_URLROOT' => TWeb_URLROOT ,
			'TWeb_WWWRoot' => TWeb_WWWRoot ,
			'TWeb_WwwRoot' => TWeb_WwwRoot ,
			'TWeb_wwwRoot' => TWeb_wwwRoot ,
			'TWeb_wwwroot' => TWeb_wwwroot ,
			'TWeb_WWWROOT' => TWeb_WWWROOT
		);

		foreach( $vars as $name => $val )
			print "<tr><td>{$name}</td><td>{$val}</td></tr>" ;

		print '</table>' ;

	}

	public static function infoini(){ self::ini(); }
	public static function iniinfo(){ self::ini(); }
	public static function ini(){
		$ini = ini_get_all();
		if( empty( $ini ) )
			print "<b>ini_get_all() not active!</b>" ;

		// style
		self::printStyle();
		
		// create table
		print '<table class="ted">' ;
		print '<tr"><th colspan=4>php.ini details</th></tr>';
		print '<tr><th>variable</th><th>global value</th><th>local value</th><th>access</th></tr>';
		foreach ($ini as $k => $v )
			print '<tr><th>' . $k . '</th><th>' . $v['global_value'] . '</th>' .
				'<th>' . $v['local_value'] . '</th><th>' . $v['access'] . '</th></tr>';

	}

} ?>
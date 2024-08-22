<?php

namespace Ted ;

defined( 'TExec' ) or die( 'Access Denied' );

// Get Human-Readable File size
function HumanFileSize( $size , $unit="" ) {
    
  if( (!$unit && $size >= 1<<30) || $unit == "GB")
  
    return number_format($size/(1<<30),2)."GB";
    
  if( (!$unit && $size >= 1<<20) || $unit == "MB")
  
    return number_format($size/(1<<20),2)."MB";
    
  if( (!$unit && $size >= 1<<10) || $unit == "KB")
  
    return number_format($size/(1<<10),2)."KB";
    
  return number_format($size)." bytes";
  
}

// Get Human-Readable price
function HumanPrice( $price ){

	return number_format( $price );

}

// PHP Execute Functions that are available
function ExecFunctions(){

	$list = array( 'exec' , 'shell_exec' , 'system' , 'passthru' , 'backticks' );

	for( $i = 0 ; $i <= count( $list ) - 1 ; $i++ )

	    if( ! function_exists( $list[$i] ) )

	    	unset( $list[$i] );

	return array_values( $list );
	
}

// Handle Multipart file uploads ( just give the upload handle )
function ListMultipartUploads( $MPUF ) {

	if ( ! isset( $MPUF[ "name" ] ) ) return $MPUF ;

	if ( ! is_array( $MPUF[ "name" ] ) ) return array( $MPUF ) ;

	$len = count( $MPUF[ "name" ] ) - 1 ;

	$nMPUF = array() ;

	for ( $i = 0 ; $i <= $len ; $i++ ) { 

		$nf = array() ;

		if ( isset( $MPUF[ "name" ][$i] ) ) 	
			$nf["name"] 	= $MPUF[ "name" ][$i] ;

		if ( isset( $MPUF[ "type" ] ) && isset( $MPUF[ "type" ][$i] ) ) 	
			$nf["type"] 	= $MPUF[ "type" ][$i] ;

		if ( isset( $MPUF[ "tmp_name" ] ) && isset( $MPUF[ "tmp_name" ][$i] ) ) 
			$nf["tmp_name"] = $MPUF[ "tmp_name"][$i] ;

		if ( isset( $MPUF[ "error" ] ) && isset( $MPUF[ "error" ][$i] ) ) 
			$nf["error"] 	= $MPUF[ "error" ][$i] ;

		if ( isset( $MPUF[ "size" ] ) && isset( $MPUF[ "size" ][$i] ) ) 
			$nf["size"] 	= $MPUF[ "size" ][$i] ;

		if ( count( $nf ) < 4  ) continue ;
		if ( isset( $nf["error"] ) && $nf["error"] === 0 ) $nMPUF[$i] = $nf ;

	} return $nMPUF ;

}

// Copy All Files from $src folder to $dst folder
function Copy( $src , $dst ) { 

    $dir = opendir( $src ); 

    @mkdir( $dst ); 

    while(false !== ( $file = readdir($dir)) ) { 

        if (( $file != '.' ) && ( $file != '..' )) { 

            if ( is_dir($src . '/' . $file) ) Ted\Copy($src . '/' . $file,$dst . '/' . $file); 

            else copy($src . '/' . $file,$dst . '/' . $file); 

        } 

    } closedir($dir); 
    
} 

// Include_once file if found in $root ( Default $root is TPath_Root )
function Import( $import = null , $root = null , $ext = "php" ) {

	if ( $import ) {
		
		// Trim The Import From " \/"
		$import = trim( $import , " /\\");
		
		// Check The Root Searching Area
		$inc = ( $root !== null ) ? $root : TPath_Root ;

		// Explode The File Package Name 
		$dires = explode( "." , $import );	
	
		foreach( $dires as $k => $value ) {
			
			if ( $value == '*' ) {
						
				$scaned = scandir( $inc );
				
				foreach( $scaned as $phpFiles ) {
							
					$cFile = $inc . TPath_DS . $phpFiles;
							
					$info = pathinfo( $cFile );
							
					if ( is_file( $cFile ) && strtolower( $info['extension'] ) == "php" ) {
					
						include_once $cFile;
							
					}
						
				}
						
				return true;
					
			} else if ( stristr( $value , '*' ) && strlen( $value ) >= 2 ) {
						
				$starCounter = str_ireplace( '*' , '.*' , $value );
						
				$scaned = scandir( $inc );
				
				foreach( $scaned as $phpFiles ) {
					
					$match = array ();
							
					$exists = preg_match_all( "|{$starCounter}|" , $phpFiles , $match );
							
					if ( $exists ) {
								
						$cFile = $inc . TPath_DS . $match[0][0];
						
						if ( is_file( $cFile ) ) include_once( $cFile );
							
					}
						
				} return true;
					
			} else {	
			
				$fakeConstant = "TPath_{$value}" ;
				
				if ( defined( $fakeConstant ) ) {
					
					$inc = constant( $fakeConstant) ;
					
				} else $inc .= TPath_DS . $value;
				
			}
			
		} $ext = ( $ext ) ? $ext : "php" ;
		
		$inc .= ".{$ext}" ;
		
		$inc = realpath( $inc ) ;
	
		if ( file_exists( $inc ) ) {
					
			include_once $inc;	
			
			return true;
				
		}
			
	} return false;
	
}

// Find if classes exists ( case insensitive )
function FindClass(){

	$funcArgs = func_get_args( );

	$Class = array() ;

	foreach ( $funcArgs  as $value ) {

		$Class[] = trim( $value , " /\\");

		$Class[] = strtolower( $value );

		$Class[] = strtoupper( $value );

		$Class[] = ucfirst( $value );
		
	} $className = null ;

	foreach ( $Class as $value ) {
		
		if ( class_exists( $value ) ) {

			$className = $value ;

			break;

		}

	} return $className ;

}

// Find Directory in list folders
function FindDirectory( $root = null , $list = array() ) {

	$address = null;
			
	$list = ( is_array( $list ) ) ? $list : [ $list ] ;
			
	foreach( $list as $directoryName ) {
				
		$newDire = $root . TPath_DS . $directoryName ;

		$newDire = trim( $newDire , " \\/" );
				
		if ( is_dir( $newDire ) ) {
					
			$address = $newDire;
					
			break;
				
		} else if ( is_dir( $newDire = $root . TPath_DS . ucwords( $directoryName ) ) ) {
					
			$address = $newDire;
					
			break;
				
		} else if ( is_dir( $newDire = $root . TPath_DS . ucfirst( $directoryName ) ) ) {
					
					$address = $newDire;
					
					break;
				
		} else if ( is_dir( $newDire = $root . TPath_DS . lcfirst( $directoryName ) ) ) {
					
			$address = $newDire;
					
			break;
				
		} else if ( is_dir( $newDire = $root . TPath_DS . strtolower( $directoryName ) ) ) {
					
			$address = $newDire;
					
			break;
				
		} else if ( is_dir( $newDire = $root . TPath_DS . strtoupper( $directoryName ) ) ) {
					
			$address = $newDire;
					
			break;
				
		}
			
	} if ( $address && is_dir( $address ) ) return realpath( $address );
			
	else return false;

}

// Find File in list folders
function FindFile( $root = null , $list = array() ) {

	$address = null;

	if( ! $root ) return false ;
			
	if ( ! is_dir( $root ) ) return false ;
			
	if ( is_array( $list ) ) {
				
		foreach( $list as $FileName ) {
					
			$newFile = $root . TPath_DS . $FileName;
					
			if ( is_file( $newFile ) ) {
						
				$address = $newFile;
						
				break;
					
			} else if ( is_file( $newFile = $root . TPath_DS . strtolower( $FileName ) ) ) {
						
				$address = $newFile;
				
				break;
			
			} else if ( is_file( $newFile = $root . TPath_DS . strtoupper( $FileName ) ) ) {
						
				$address = $newFile;
						
				break;
					
			} else if ( is_file( $newFile = $root . TPath_DS . ucwords( $FileName ) ) ) {
						
				$address = $newFile;
						
				break;
					
			} else if ( is_file( $newFile = $root . TPath_DS . ucfirst( $FileName ) ) ) {
						
				$address = $newFile;
						
				break;
					
			} else if ( is_file( $newFile = $root . TPath_DS . lcfirst( $FileName ) ) ) {
						
				$address = $newFile;
						
				break;
					
			} else if ( is_file( $newFile = FindFile( $root , $FileName ) ) ) {
						
				$address = $newFile;
						
				break;
					
			}
				
		}
			
	} else {
				
		$scand = scandir( $root );
				
		foreach( $scand as $arrayMember ) {
					
			$fileAddress = $root . TPath_DS . $arrayMember;
					
			$info = pathinfo( $fileAddress );
					
			$filename = strtolower( $info['filename'] );
					
			$basename = strtolower( $info['basename'] );
					
			$subject = strtolower( $list );
					
			if ( $subject == $filename || $subject == $basename ) {
						
				$address = $fileAddress;
						
				break;
					
			}
				
		}
			
	} if ( $address && is_file( $address ) ) return realpath( $address );
			
	return false;
		
}

// Find File Absolute Path
function FindFilePath( $root = null , $RouteArray = array() , $fileType = array() ) {
	
	$mainDire = ( $root ) ? $root : TPath_Root ;
	
	$mainFile = null ;
	
	$backDire = null ;
	
	$backFile = null ;
	
	$RouteArray =  ( is_string( $RouteArray ) ) ? [ $RouteArray ] : $RouteArray;
	
	$RouteArray =  ( is_array( $RouteArray ) ) ? $RouteArray : array();
	
	$FileRoute = array_values( $RouteArray ) ;
	
	$fileType = ( is_array( $fileType ) ) ? $fileType : [ $fileType ] ;
	
	$fileType = ( empty( $fileType ) ) ? [ "php" ] : $fileType ;
	
	foreach ( $FileRoute as $newDirectionName ) {

		$newBackDire = FindDirectory( $backDire , $newDirectionName )  ;
			
		$newMainDire = FindDirectory( $mainDire , $newDirectionName )  ;

		if ( $newMainDire ) {
				
			$backDire = ( $newBackDire ) ? $newBackDire : $mainDire ;

			$mainDire = $newMainDire ;
				
		} else if ( $mainDire === $backDire && $newBackDire ){
				
			$backDire = $mainDire ;
				
			$mainDire = $newBackDire ;
				
		} $searchArray = array() ;
		
		foreach( $fileType as $ft ) $searchArray[] = "{$newDirectionName}.{$ft}" ;
		
		$newBackMedia = FindFile( $backDire , $searchArray );

		$newMainMedia = FindFile( $mainDire , $searchArray );
		
		if ( $newMainMedia ){
				
			$backFile = ( $newBackMedia ) ? $newBackMedia : $mainFile ;
				
			$mainFile = $newMainMedia ;
				
		} else if ( $newBackMedia ){
				
			$backFile = $mainFile ;

			$mainFile = $newBackMedia ;
				
		}

	} if ( $mainFile ) return $mainFile ;
	
	else if ( $backFile ) return $backFile ;
	
	return null ; 
		
}

// Get Web-Link ( Direct-Link ) of a file
function FindWebPath( $address = null , $roots = array() ){
    
	if ( ! file_exists( $address ) ) return false;
    
	$webAddress = "";
	
	$WebRoot = TWeb_HttpDomain . TWeb_Path ; 
    //$WebRoot = TWeb_URL ;

	if ( stristr( $address , TPath_Root ) ){
		
		$webAddress = str_ireplace( TPath_Root , '' , $address );
	
		$webAddress = str_ireplace( TPath_DS , '/' , $webAddress );
	
		$webAddress = trim( $webAddress , " \\/");
	
		$webAddress = $WebRoot . '/' . $webAddress;
		
	} else {
	    
	    // Find Base Web Break
		$TedUrl = explode( "//" , $WebRoot , 2 ) ;
		
		$http = $TedUrl[ 0 ] ;
		
		unset( $TedUrl[ 0 ] );
		
		$TedUrl = explode( "/" , $TedUrl[ 1 ] ) ;
		
		$http .= "//" . $TedUrl[ 0 ] ;
		
		unset( $TedUrl[ 0 ] );
		
		$dif = str_ireplace( $http , "" , $WebRoot );
		
		$dif = trim( $dif , " /" ); 
		
		$dif = explode( "/" , $dif ); // Last Path
		
		$FilePath = explode( TPath_DS , $address );
		
		$RootPath = explode( TPath_DS , TPath_Root );
		
		$Route = array();
		
		$isPathClear = false ;
		
		foreach( $RootPath as $n => $v ) 
		    if( isset( $FilePath[ $n ] ) ) if ( strtolower( $v ) == strtolower( $FilePath[ $n ] ) ){
				
			unset( $RootPath[ $n ] , $FilePath[ $n ] ) ;
			
			$Route[] = $v ;
			
			if( ! $isPathClear ) foreach( $dif as $np ) 
                if( strtolower( $np ) == strtolower( $v ) )
                    $isPathClear = true ;
				
		} // Route Now Contains Path To First Breaking Of Roots
		
		$FilePath = array_values( $FilePath ) ;
		
		foreach( $FilePath as $newPath ) 
		
		    $Route[] = $newPath ;
		
		$TedUrl = array_values( $TedUrl ) ;
		
		if( ! $isPathClear ) // Not Same Path : Just Add FilePath To Http !
		
		    $http .= "/" . implode( "/" , $FilePath ) ;
		
		else foreach( $TedUrl as $k => $v ){
			
			$break = false ;
			
			foreach( $Route as $l => $p ){
				
				if ( $v == $p ) {
					
					$np = array_slice( $Route , $l ) ;
					
					$http .= "/" . implode( "/" , $np ) ;
					
					$break = true ;
					
					break;
					
				}
				
			} if ( $break ) break ;
			
			else $http .= "/" . $v ;
			
		} $webAddress = $http ;
		
	} $webAddress = trim( $webAddress , " /\\" ) ;
	
	return $webAddress ;

}
	
// Find user intefaces that are enabled for $AppName
function FindUserInterfaces( $root = null , $AppName = null ){

	$UiFiles = ( is_dir( $root ) ) ? scandir( $root ) : array() ;

	$AppName = ( $AppName ) ? $AppName : "TED" ;

	$UserInterfaces = array() ;

	foreach ( $UiFiles as $key => $name ) {

		$newUI = $root . TPath_DS . $name ;

		if ( is_file( $newUI ) && stristr( $name , ".php" ) ) {

			$name = $name ;

		} else if ( is_dir( $newUI ) && $name[ 0 ] != "." && ! isset( $UserInterfaces[ strtoupper($name) ] ) ) {

			$new = FindDirectory( $newUI , [ "ui.php" , "{$name}.php" ] );

			$newUI = ( $new ) ? $new : $newUI ;

		} else continue ;

		$name = str_ireplace( ".php" , "" , $name );

		$name = str_ireplace( "_userinterface" , "" , $name );

		$name = str_ireplace( "_interface" , "" , $name );

		$name = str_ireplace( "_ui" , "" , $name );

		$name = str_ireplace( "{$AppName}_" , "" , $name );

		$name = str_ireplace( "__" , "_" , $name );

		$name = trim( $name , " _/\\");

		// name = html , json , xml , ...
		// newUi = file_address 
		$UserInterfaces[ ucfirst( $name ) ] = $newUI ;

	} return $UserInterfaces ;
	
}

// Find Routeings for given path ( $Args = ['folderParent' , 'child' , 'filename.ext' ] )
function FindRouteElements( $Args = array() ){

	if ( ! is_array( $Args ) || empty( $Args ) ) return [ array() , array() ] ;

	$Route = array() ;

	$State = false ;

	$Extra = array() ;

	$PrewRoute = null ;

	foreach ( $Args as $key => $value ) {

		if ( is_array( $value ) ) {

			$Counter = 0 ;

			foreach ( $value as $key2 => $value2 )	{

				$key2 = ( is_string( $key2 ) && ( ( int ) $key2 ) === 0 ) ? 
					$key2 : "_{$Counter}";

				$Extra[ $key2 ] = $value2 ;

				if ( $key2 === "_{$Counter}" ) $Counter++ ;

			} 

		} else if ( is_string( $value ) ){

			if ( array_key_exists( $PrewRoute , $Extra ) 
				&& ! isset( $Extra[ $PrewRoute ] ) )

				$Extra[ $PrewRoute ] = $value ;

			if ( ! isset( $Extra[ $value ] ) ) $Extra[ $value ] = $value ;

			$PrewRoute = $value ;

			array_push( $Route , $value ) ;

		}else if ( is_bool( $value ) ) {

			if ( array_key_exists( $PrewRoute , $Extra ) 
				&& ! isset( $Extra[ $PrewRoute ] ) )

				$Extra[ $PrewRoute ] = $value ;

			$PrewRoute = null ;

			$State = $value ;

		} unset( $Args[ $key ] ) ; 

	} array_push( $Route , $State ) ;

	return [ $Route , $Extra ] ;

}

// Get ENTER replaced by \n ( new line )
function addSlashe( $StringsArray = array() ){

	$E = "
";  
	foreach( $StringsArray as $k => $v ) {

		if ( is_array( $v ) ) foreach ( $v as $k1 => $v1 ) {

			if ( is_array( $v1 ) ) 

				$v[ $k1 ] = addSlashe( $v1 ) ;

			else if ( is_string( $v1 ) ) 

				$v[ $k1 ] = str_ireplace( $E , "\\n" , trim( addslashes( $v1 ) ) ) ;

		} else if ( is_string( $v ) ) 

			$v = str_ireplace( $E , "\\n" , trim( addslashes( $v ) ) ) ;

		$StringsArray[ $k ] = $v ;

	} return $StringsArray ;

}

// Route Delimiters
function Delemiters( $string = null ){
	
	$delemiters = [ '<==>' ,'<=>' ,'==>' ,'=>' ,'==' ,'=' ,

	'<::>' ,'<:>' ,'::>' ,':>' ,'::' ,':' , '<-->' ,'<->' ,
	
	'-->' ,'->' ,'--' ,'<<>>' , '<>' ,'>>' ,'>' ];	

	foreach( $delemiters as $delemiter ) 
	
		if ( stristr( $string , $delemiter ) ) return ( string ) $delemiter;
		
	return null;
	
}

// get json decoded to array
function json_str_to_array( $str ){

	if( ! $str )

		return null ;

	$ex = @json_decode( $str , true ) ;

	if ( ! is_array( $ex ) ){

		$ex = str_replace( "\'" , "'" , $str );

		$ex = str_replace( '\\"' , '"' , $ex);

		$ex = str_replace( '\\\\"' , '\\"' , $ex);
		
		$ex = preg_replace( '/\s+/' , ' ', $ex );

		$ex = @json_decode( $ex , true , 512 , 
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ;

	} return $ex ;

} 

// get array to json string
function json_array_to_str( $array , $pretty = false ){

    if( $pretty ) 

    	return @json_encode( $array , 
    		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) ;
    
    else return @json_encode( $array , 
    	JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ;

}

// check if this array is indexed by numbers
function isIndexedArray( $array ){

	$i = 0 ;

	while( $i <= count( $array ) - 1 ){

		if( ! array_key_exists( $i , $array ) )

			return false ;

		$i++ ;

	} return true ;

}

// check if this array is Assoc
function isAssocArray( $array ){

	return ! isIndexedArray( $array ) ;

}


// check if array is one dimensinal
function isOneDimensional( $array ){

	foreach ( $array as $value ) 
		
		if( is_array( $value ) )

			return false ;

	return true ;

}

// replace \n( new line ) with <br /> tags
function Br( $c = 1 ) {

	$r = "";
			
	$e = ( PHP_SAPI == "cli" ) ? PHP_EOL : PHP_EOL . "<br />" ;
			
	for( $i = 1 ; $i <= ( int ) $c ; $i ++ ) $r .= " " . $e ;
	
	return $r;
		
}

// check if we are in cli ( command line , terminal , ... )
function IsCli( ) { return ( PHP_SAPI == 'cli' ) ? true : false ; }

// get name of entering script file name ( start of php interpretation )
function ScriptFile(){
	
	$FileName = null ;
	
	if ( isset( $_SERVER[ "SCRIPT_FILENAME" ] ) ) {
		
		$FileName = realpath( $_SERVER[ "SCRIPT_FILENAME" ] );
		
	} else if ( IsCli() && isset( $argv[ 0 ] ) ) {
		
		$FileName = $argv[ 0 ] ;
		
	} else $FileName = "Unknown" ;
	
	return $FileName;
	
}

// get web schame ( http , https , ...)
function WebSchame(){
	
	if ( defined( 'TWeb_Schame' ) ) return TWeb_Schame ;
	
	$schame = ( isset( $_SERVER['REQUEST_SCHEME'] ) ) ? strtolower( $_SERVER['REQUEST_SCHEME'] ) : null ;
	
	$schame = ( $schame ) ? $schame : "http" ;
	
	is_string( $schame ) && $schame = trim( $schame , " /\\") ;

	$schame = IsCli() ? "cmd" : $schame ;
	
	return $schame ;
	
}

// get domain name
function WebDomain(){
	
	// just ted.ir OR www.ted.ir OR localhost OR 127.0.0.1
	
	if ( defined( 'TWeb_Domain' ) ) return TWeb_Domain ;
	
	$domain = ( isset( $_SERVER[ 'SERVER_NAME' ] ) ) ? $_SERVER[ 'SERVER_NAME' ] : null ;
		
	if ( $domain === null && isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
		
		$domain = $_SERVER[ 'HTTP_HOST' ] ;
		
		if( stristr( $domain , ":" ) ) {
			
			$domain = trim( $domain , ":" );
			
			$explo = explode( ":" , $domain , 2 );
			
			$domain = $explo[ 0 ] ;
			
		}
		
	} is_string( $domain ) && $domain = trim( $domain , " /" ) ;
	
	return $domain ;
	
}

// get full domain name
function WebDomainAccess(){
	
	//full http/https Acess
	//http://user:pass@ted.ir:8080 OR http://user:pass@www.ted.ir:8080 OR 
	//http://user:pass@localhost:8080 OR http://user:pass@127.0.0.1:8080
	
	if ( defined( 'TWeb_DomainAccess' ) ) return TWeb_DomainAccess ;
	
	$domain = ( isset( $_SERVER[ 'HTTP_HOST' ] ) ) ? $_SERVER[ 'HTTP_HOST' ]: null ;
	
	$domain = ( $domain === null ) ? WebDomain() : $domain ;
	
	if ( $domain === WebDomain() && $domain !== null )
		
		$domain .= ( isset( $_SERVER['SERVER_PORT'] ) && ( int ) $_SERVER['SERVER_PORT'] !== 80 ) ? ":" . ( int ) $_SERVER['SERVER_PORT'] : "" ;
	
	$authUser = ( isset( $_SERVER['PHP_AUTH_USER'] ) ) ? $_SERVER['PHP_AUTH_USER'] : null ;
	
	$authPass = ( isset( $_SERVER['PHP_AUTH_PW'] ) ) ? $_SERVER['PHP_AUTH_PW'] : null ;

	$authInfo = ( $authUser ) ? $authUser : null ;
	
	$authInfo && $authInfo .= ( $authPass ) ? ":" . $authPass : "" ;
		
	$domain && $domain = ( $authInfo !== null ) ? "{$authInfo}@{$domain}" : $domain ;
	
	$domain && $domain = WebSchame() . "://" . $domain ;
	
	is_string( $domain ) && $domain = trim( $domain , " /" ) ;
	
	return $domain ;
	
}

// get web url of Entering file's path
function WebPath(){

	if ( defined( 'TWeb_Path' ) ) return TWeb_Path ;
	
	$host = ( isset( $_SERVER['SCRIPT_NAME'] ) ) ? $_SERVER['SCRIPT_NAME'] : null ;

	if ( ! $host ) return __FILE__ ;

	$host = str_ireplace( TPath_DS , "/" , dirname( $host ) ) ;
	
	if ( $host == "/" ) return "/" ;

	$path = null ;

	$address = realpath( dirname( ScriptFile() ) );

	if ( stristr( $address , TPath_Root ) ){

		$path = str_ireplace( TPath_Root , '' , $address );
	
		$path = str_ireplace( TPath_DS , '/' , $path );

		$path = trim( $path , " /\\") ;

		if ( strlen( $path ) == 0 ) $path = $host ;

		else {

			$e = explode( "/" , $path );

			$sp = $host ;

			foreach ($e as $value ) {
				
				$sp = str_replace( $value , "" , $sp );

				$sp  = str_ireplace( "//" , "" , $sp );

				$sp  = trim( $sp , " /\\") ;

			} $path = $sp ;

		}
		
	} else {
			
		$FilePath = explode( TPath_DS , $address );
			
		$RootPath = explode( TPath_DS , TPath_Root );

		foreach ( $RootPath as $key => $value ) {
			
			foreach ( $FilePath as $key2 => $value2 ) 
			
				if ( $value == $value2 ) unset( $FilePath[ $key2 ] , $RootPath[ $key ] ) ;

		}

		$sp = $host ;

		foreach ( $FilePath as $key => $value) {

			$replace = "" ;
			
			if ( isset( $RootPath[ $key ] ) ){

				$replace = $RootPath[ $key ] ; 

				unset( $RootPath[ $key ] );

			} $sp = str_ireplace( $value , $replace , $sp );

			$sp  = str_ireplace( "//" , "" , $sp );

			$sp  = trim( $sp , " /\\") ;

		} $sp .= "/" . join( $RootPath , "/" ) ;
		
		$path = $sp ;

	} return "/" . trim( $path , " /\\" ) ;
	
}

// convert english numbers to persian
function PersianNumbers( $numsStr ){

	$numsStr = ( string ) $numsStr ;

	if( strlen( $numsStr ) == 0 ) return "" ;

	$PNL = array( "۰" => "0" , "۱" => "1" , 
	"۲" => "2" , "۳" => "3" , "۴" => "4" , "۵" => "5" , 
	"۶" => "6" , "۷" => "7" , "۸" => "8" , "۹" => "9" );

	foreach ($PNL as $key => $value ) 

		$numsStr = str_ireplace( $value , $key , $numsStr ) ;

	return $numsStr ;

}

// convert persian numbers to english
function RealNumbers( $numsStr = null ){

	$numsStr = ( string ) $numsStr ;

	if( strlen( $numsStr ) == 0 ) return "" ;

	$PNL = array( "۰" => "0" , "۱" => "1" , 
	"۲" => "2" , "۳" => "3" , "۴" => "4" , "۵" => "5" , 
	"۶" => "6" , "۷" => "7" , "۸" => "8" , "۹" => "9" );

	foreach ($PNL as $key => $value ) 

		$numsStr = str_ireplace( $key , $value, $numsStr ) ;

	return $numsStr ;

}

// convert arabic/non-unicode persian to real persian words
function RealPersian( $str ){

	$str = ( string ) $str ;

	if( strlen( $str ) == 0 ) return "" ;
	$str = RealNumbers( $str ) ;
	$str = str_ireplace( array( "ﺂ", "ﺂ" , "آ" ) , "آ" , $str );
	$str = str_ireplace( array( "ﺎ", "ﺎ" , "ا" ) , "ا" , $str );
	$str = str_ireplace( array( "ﺐ", "ﺒ" , "ﺑ" ) , "ب" , $str );
	$str = str_ireplace( array( "ﭗ", "ﭙ" , "ﭘ" ) , "پ" , $str );
	$str = str_ireplace( array( "ﺖ", "ﺘ" , "ﺗ" ) , "ت" , $str );
	$str = str_ireplace( array( "ﺚ", "ﺜ" , "ﺛ" ) , "ث" , $str );
	$str = str_ireplace( array( "ﺞ", "ﺠ" , "ﺟ" ) , "ج" , $str );
	$str = str_ireplace( array( "ﭻ", "ﭽ" , "ﭼ" ) , "چ" , $str );
	$str = str_ireplace( array( "ﺢ", "ﺤ" , "ﺣ" ) , "ح" , $str );
	$str = str_ireplace( array( "ﺦ", "ﺨ" , "ﺧ" ) , "خ" , $str );
	$str = str_ireplace( array( "ﺪ", "ﺪ" , "ﺩ" ) , "د" , $str );
	$str = str_ireplace( array( "ﺬ", "ﺬ" , "ﺫ" ) , "ذ" , $str );
	$str = str_ireplace( array( "ﺮ", "ﺮ" , "ﺭ" ) , "ر" , $str );
	$str = str_ireplace( array( "ﺰ", "ﺰ" , "ﺯ" ) , "ز" , $str );
	$str = str_ireplace( array( "ﮋ", "ﮋ" , "ﮊ" ) , "ژ" , $str );
	$str = str_ireplace( array( "ﺲ", "ﺴ" , "ﺳ" ) , "س" , $str );
	$str = str_ireplace( array( "ﺶ", "ﺸ" , "ﺷ" ) , "ش" , $str );
	$str = str_ireplace( array( "ﺺ", "ﺼ" , "ﺻ" ) , "ص" , $str );
	$str = str_ireplace( array( "ﺾ", "ﻀ" , "ﺿ" ) , "ض" , $str );
	$str = str_ireplace( array( "ﻂ", "ﻄ" , "ﻃ" ) , "ط" , $str );
	$str = str_ireplace( array( "ﻆ", "ﻈ" , "ﻇ" ) , "ظ" , $str );
	$str = str_ireplace( array( "ﻊ", "ﻌ" , "ﻋ" ) , "ع" , $str );
	$str = str_ireplace( array( "ﻎ", "ﻐ" , "ﻏ" ) , "غ" , $str );
	$str = str_ireplace( array( "ﻒ", "ﻔ" , "ﻓ" ) , "ف" , $str );
	$str = str_ireplace( array( "ﻖ", "ﻘ" , "ﻗ" ) , "ق" , $str );
	$str = str_ireplace( array( "ك", "ﻚ" , "ﻜ" , "ﻛ" ), "ک" , $str );
	$str = str_ireplace( array( "ﮓ", "ﮕ" , "ﮔ" ) , "گ" , $str );
	$str = str_ireplace( array( "ﻞ", "ﻠ" , "ﻟ" ) , "ل" , $str );
	$str = str_ireplace( array( "ﻢ", "ﻤ" , "ﻣ" ) , "م" , $str );
	$str = str_ireplace( array( "ﻦ", "ﻨ" , "ﻧ" ) , "ن" , $str );
	$str = str_ireplace( array( "ﻮ", "ﻮ" , "ﻭ" ) , "و" , $str );
	$str = str_ireplace( array( "ﻫ", "ﻬ" , "ﻪ" ) , "ه" , $str );
	$str = str_ireplace( array( "ی", "ﯿ" , "ﯾ" , "ﻲ" , "ﯽ" ), "ي" , $str );
	$str = str_ireplace( array( "ﺄ", "ﺄ" , "ﺃ" ) , "أ" , $str );
	$str = str_ireplace( array( "ﺆ", "ﺆ" , "ﺅ" ) , "ؤ" , $str );
	$str = str_ireplace( array( "ﺈ", "ﺈ" , "ﺇ" ) , "إ" , $str );
	$str = str_ireplace( array( "ﺊ", "ﺌ" , "ﺋ" ) , "ئ" , $str );
	$str = str_ireplace( "ﺔ" ,   "ة", $str );
	return $str ;
	
}
	
?>
<?php namespace Ted ;

defined( 'TExec' ) or die( 'Access Denied' );

use MaxTools ;

// Get Human-Readable File size
function HumanFileSize( $size , $unit = '' ) {
	return MaxTools\HumanFileSize( $size , $unit ); }

// Get Human-Readable price
function HumanPrice( $price ){
	return MaxTools\HumanPrice( $price ); }

// PHP Execute Functions that are available
function ExecFunctions(){
	return MaxTools\ExecFunctions(); }

// Copy All Files from $src folder to $dst folder
function Copy( $src , $dst ) { 
    return MaxTools\Copy( $src , $dst ); }
    
// Include_once file if found in $root ( Default $root is TPath_Root )
function Import( $import = null , $root = null , $ext = 'php' ) {
	$root = $root ? $root : TPath_Root ;
	return MaxTools\Import( $import , $root , $ext );
}

// Find if classes exists ( case insensitive )
function FindClass(){
	return call_user_func_array( 'MaxTools\FindClass' , func_get_args() ); }

// Find Directory in list folders
function FindDirectory( $root = null , $list = array() ) {
	$root = $root ? $root : TPath_Root ;
	return MaxTools\FindDirectory( $root , $list );
}

// Find File in list folders
function FindFile( $root = null , $list = array() ) {
	$root = $root ? $root : TPath_Root ;
	return MaxTools\FindFile( $root , $list );
}

// Find File Absolute Path
function FindFilePath( $root = null , $RouteArray = array() , $fileType = array() ) {
	$root = $root ? $root : TPath_Root ;
	return MaxTools\FindFilePath( $root , $RouteArray , $fileType );
}

// Get ENTER replaced by \n ( new line )
function addSlashe( $StringsArray = array() ){
	return MaxTools\addSlashe( $StringsArray ); }

// get json decoded to array
function json_str_to_array( $str ){
	return MaxTools\json_str_to_array( $str ); }

// get array to json string
function json_array_to_str( $array , $pretty = false ){
    return MaxTools\json_array_to_str( $array , $pretty ); }

// check if this array is indexed by numbers
function isIndexedArray( $array ){
	return MaxTools\isIndexedArray( $array ); }

// check if this array is Assoc
function isAssocArray( $array ){
	return MaxTools\isAssocArray( $array ); }

// check if array is one dimensinal
function isOneDimensional( $array ){
	return MaxTools\isOneDimensionalArray( $array ); }

// get NEW-LINE : [ cli ? \n( PHP_EOL ) : <br />
function br( $c = 1 ) {
	return MaxTools\br( $c ); }

// check if we are in cli ( command line , terminal , ... )
function isCli() { 
	return MaxTools\isCli(); }

// get name of entering script file name ( start of php interpretation )
function ScriptFile(){
	return MaxTools\ScriptFile(); }

// get web schame ( http , https , ...)
function WebSchame(){
	return MaxTools\WebSchame(); }

// get domain name
function WebDomain(){
	if( defined( 'TWeb_Domain' ) )
		return TWeb_Domain ;
	return MaxTools\WebDomain();
}

// get full domain name
function WebDomainAccess(){
	if ( defined( 'TWeb_DomainAccess' ) ) 
		return TWeb_DomainAccess ;
	return MaxTools\WebDomainFull();
}

// get web url of Entering file's path
function WebPath(){
	if ( defined( 'TWeb_Path' ) ) 
		return TWeb_Path ;
	return MaxTools\WebPath( TPath_Root );
}

// Get Web-Link ( Direct-Link ) of a file
function FindWebPath( $address = null ){
	return MaxTools\FindWebPath( $address , TPath_Root ); }


function ListMultipartUploads( $MPUF ){
	return MaxTools\ListMultipartUploads( $MPUF ); }

// convert english numbers to persian
function PersianNumbers( $numsStr ){
	return MaxTools\PersianNumbers( $numsStr ); }

// convert persian numbers to english
function RealNumbers( $numsStr = null ){
	return MaxTools\RealNumbers( $numsStr ); }

// convert arabic/non-unicode persian to real persian words
function RealPersian( $str ){
	return MaxTools\RealPersian( $str ); }

/****************************
**** Custom Ted Function ****
*****************************/

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

// Route Delimiters
function Delemiters( $string = null ){
	
	$delemiters = [ '<==>' ,'<=>' ,'==>' ,'=>' ,'==' ,'=' ,
		'<::>' ,'<:>' ,'::>' ,':>' ,'::' ,':' , '<-->' ,'<->' ,
		'-->' ,'->' ,'--' ,'<<>>' , '<>' ,'>>' ,'>' ];	

	foreach( $delemiters as $delemiter ) 
		if ( stristr( $string , $delemiter ) ) 
			return ( string ) $delemiter;
		
	return null;
	
}
	
?>
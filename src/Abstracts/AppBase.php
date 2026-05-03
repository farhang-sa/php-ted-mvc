<?php

namespace Ted ;

defined( 'TExec' ) or die( 'Access Denied' );

abstract class AppBase {

	public $Char_Enter = "
";

	// OverWriteable Tool PHP File Includer
	protected function Import( $search = null , $root = null , $type = null ) {
		
		$root = $root === null ? TPath_Base : $root ;
		
		return Import( $search , $root , $type );
	
	}

	public function WebLink( $FilePath ){

		if ( ! $FilePath ) return false ;

		return FindWebPath( $FilePath );

	}

	public function ReadJSONFileAsArray( $jsn = null ){

		if( strlen( $jsn ) >= 4 && substr( strtolower($jsn) , 0 , 4 ) === "http" ){

			$jsn = @file_get_contents( $jsn );

			if ( $jsn === false ) return null ;

		} else if ( @is_file( $jsn ) && @is_readable( $jsn ) ) {
			
			$jsn = @file_get_contents( $jsn );

			if ( $jsn === false ) return null ;

		} $jsn = json_str_to_array( $jsn ) ;

		return is_array( $jsn ) ? $jsn : null ;

	}

	public function WriteJSONFileFromArray( $jsn = null , $Data = null , $pretty = false ){

		if ( is_string( $Data ) ) $Data = @json_decode( $Data , true ) ;

		if ( ! is_array( $Data ) ) return false ;

		if ( empty( $Data ) ) return false ;

		$Data = json_array_to_str( $Data , ( boolean ) $pretty ) ;

		return @file_put_contents( $jsn , $Data ) ;

	}
	
	public function JSON_str_to_array( $jsnstr = null ){
	    
	    $jsnstr = json_str_to_array( $jsnstr ) ;
	    
	    return is_array( $jsnstr ) ? $jsnstr : null ;
	    
	}
	
	public function JSON_array_to_str( $jsnarray = null , $pretty = false ){
	    
	    $jsnarray = json_array_to_str( $jsnarray , $pretty ) ;
	    
	    return is_string( $jsnarray ) ? $jsnarray : null ;
	    
	}

	/***************************************
	/// The Instance Holder Part */
	
}

?>
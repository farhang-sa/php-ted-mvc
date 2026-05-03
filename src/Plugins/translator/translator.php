<?php

defined( 'TExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
class translatorPlugin extends Ted\Plugin {
	
	private static $Translations = array() ;
	private static $Language = "en" ;

	public function __get( $name ) { return $this->Find( $name ); }
	
	public function __set( $prop , $value ) { 

		$A = $this->Find( $value , $prop ) ;

		return $A ? $A : $this->Find( $prop , $value ) ;

	}

	public function Language( $lang = null ){

		if ( $lang ) self::$Language = strtolower( $lang ) ;

		return self::$Language ;

	}

	public function Find( $Search = null , $lang = null ) {

		$lang = $lang ? $lang : self::$Language ;
		
		$data = isset( self::$Translations[ $lang ] ) ? self::$Translations[ $lang ] : null ;

		if ( ! $data ) return $Search ;

		if ( isset( $data[ $Search ] ) ) return $data[ $Search ] ;

		if ( isset( $data[ ucfirst( $Search ) ] ) ) return $data[ ucfirst( $Search ) ] ;

		if ( isset( $data[ strtolower( $Search ) ] ) ) return $data[ strtolower( $Search ) ] ;

		if ( isset( $data[ strtoupper( $Search ) ] ) ) return $data[ strtoupper( $Search ) ] ;

		return $Search ;

	}

	public function LoadINIDirectory( $directory = null ){

		if ( ! is_dir( $directory ) ) return false ;

		$scn = scandir( $directory ) ;
		
		foreach ( $scn as $key => $value ) :

			$file = $directory . TPath_DS . $value ;

			if ( is_file( $file ) ) $this->LoadINIFile( $file ) ;

		endforeach ;
		
		return true ;

	}

	public function LoadINIFile( $FileAddress = null , $Language = null ) {

		if ( ! file_exists( $FileAddress ) ) return false ;

		$FileAddress = realpath( $FileAddress ) ;

		$Content = file_get_contents( $FileAddress ) ;

		$Content = trim( $Content , " /\\." ) ;

		$lang = $Language ? $Language : strtolower( pathinfo( $FileAddress )['filename'] ) ;
		
		return $this->LoadINIString( $Content , $lang , 2);

	}

	public function LoadINIString( $IniString = null , $Language = "en" ) {

		if ( ! $IniString || ! is_string( $IniString ) ) return false ;

		$Language = strtolower( $Language ) ;

		if ( ! isset( self::$Translations[ $Language ] ) ) 

			self::$Translations[ $Language ] = array() ;

		$del = stristr( $IniString , PHP_EOL ) !== false ? PHP_EOL : "\n" ;

		$IniString = explode( $del , $IniString ) ;

		foreach ( $IniString  as $value ) {

			if ( strlen( $value ) < 3 ) continue ;

			$explode = ( stristr( $value , "=" ) !== false) ? "=" : " " ;

			$explode = explode( $explode , $value , 2 ) ;

			$k = isset( $explode[ 0 ] ) ? trim( $explode[ 0 ] , " \\/" ) : null ;

			$v = isset( $explode[ 1 ] ) ? trim( $explode[ 1 ] , " \\/" ) : null ;

			if ( ! $k || ! $v ) continue ;

			self::$Translations[ $Language ][ $k ] = $v ;
			
		} return true ;

	}

}

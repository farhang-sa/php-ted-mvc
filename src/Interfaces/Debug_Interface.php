<?php

defined( 'TExec' ) or die( 'Access Denied' );

class Debug_Interface extends Ted\TedInterface{
	
	public function Respond( $directRespond = false ){ 
		
		// Print HEADER as html
		if ( ! Ted\IsCli( ) ) 
			header( 'Content-type:text/html; charset:UTF-8;' );

		print "<!DOCTYPE html>\n<html>\n<head><title>Debug Mode</title>" ;

		print "<meta name='charset' content='text/html; charset=UTF-8' /></head>\n<body>" ;
		
		parent::Respond();

		print "</body></html>" ;

		return true ;
		
	}

	public function Response( ){
	
		$ARGUMENTS = func_get_args();

		$ARGUMENTS = ( count( $ARGUMENTS ) == 1 && is_array( $ARGUMENTS[0] ) ) ? $ARGUMENTS[0] : $ARGUMENTS ;
		
		if ( empty( $ARGUMENTS ) ) $ARGUMENTS = [ "exec" => "false" ] ;

		//Response Has 3 Main Requirments
		$VIEW = null ; // The View HTML Page Name
		
		$EXECUTE = null ; // EXECUTE Of Application Work
		
		$EXTRA = array() ; // Extra Variables That Passed On
		
		$viewSearchList = [ 'view' ,'html' ,'layout' ,'page' ];
		
		foreach( $ARGUMENTS as $k => $v ){

			$doContinue = false ;
			
			if ( is_array( $v ) ){
				
				foreach( $viewSearchList as $m ){
					
					foreach ( array_keys( $v ) as $newKey ) 

						if ( stristr( $newKey , $m ) ) 

							$VIEW = $v[$newKey] ;
						
				}
				
			} if ( is_int( $k ) && ( is_bool( $v ) || is_string( $v ) ) ) {
				
				if ( is_bool( $v ) ) $EXECUTE = $v ;
				
				else if ( is_string( $v ) ) $VIEW = ( string ) $v ;
				
				unset( $ARGUMENTS[ $k ] );
				
				$doContinue = true ;
				
			} else if ( is_string( $k ) && is_string( $v ) ) {
				
				foreach( $viewSearchList as $m ){
					
					if ( strtolower( $k ) === $m ) {
						
						$VIEW = $v ;
						
						unset( $ARGUMENTS[ $k ] );

						$doContinue = true ;

						break;
						
					}
					
				}
				
				
			}

			if ( $doContinue ) continue ;
			
			$EXTRA[ $k ] = $ARGUMENTS[ $k ] ;
			
			unset( $ARGUMENTS[ $k ] );
			
		} $EXTRA = ( is_array( $EXTRA ) ) ? $EXTRA : array() ;
		
		if( is_string( $VIEW ) || is_numeric( $VIEW ) )

			( ! is_null( $VIEW ) ) && $EXTRA["view"] = $VIEW;
		
		( ! is_null( $EXECUTE ) ) && $EXTRA["exec"] = $EXECUTE;

		if ( ! isset( $EXTRA["exec"] ) ) $EXTRA["exec"] = true ;
		
		print 'Exec output array ( toJSONString ) : ';

		$Prints = $this->toJSONString( $EXTRA ) ;
		
		print  "<pre>" . $Prints . "</pre>" ;

		return $EXTRA ;
		
	}

	public function toJSONString( $array ){
		return ( string ) json_encode( $array , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ); }

}

?>
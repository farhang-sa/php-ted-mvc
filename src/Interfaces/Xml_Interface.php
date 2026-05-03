<?php

defined( 'TExec' ) or die( 'Access Denied' );

class Xml_Interface extends Ted\TedInterface {
	
	public function Respond( $directRespond = false ){ 
		
		if ( ! Ted\IsCli( ) ) header( 'Content-type:application/xml; charset=UTF-8' );
	
		parent::Respond(); 

		return true ;
		
	}

	public function Response( ){
	
		$ARGUMENTS = func_get_args();
		
		$ARGUMENTS = ( count( $ARGUMENTS ) == 1 && is_array( $ARGUMENTS[0] ) ) ? $ARGUMENTS[0] : $ARGUMENTS ;
		
		if ( empty( $ARGUMENTS ) ) $ARGUMENTS = [ "execute" => "false" ] ;
		
		//Response Has 3 Main Requirments
		$VIEW = null ; // The View HTML Page Name
		
		$EXECUTE = null ; // EXECUTE Of Application Work
		
		$EXTRA = array() ; // Extra Variables That Passed On
		
		$viewSearchList = [ 'view' ,'html' ,'layout' ,'page' ];
		
		foreach( $ARGUMENTS as $k => $v ){

			$doContinue = false ;
			
			if ( is_array( $v ) ){
				
				foreach( $viewSearchList as $m ){
					
					foreach ( array_keys( $v ) as $newKey ) {
						
						if ( stristr( $newKey , $m ) ){
							
							$VIEW = $v[$newKey] ;
							
						}
						
					}
					
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
			
		}
		
		$EXTRA = ( is_array( $EXTRA ) ) ? $EXTRA : array() ;
		
		( ! is_null( $VIEW ) ) && $EXTRA["view"] = $VIEW;
		
		( ! is_null( $EXECUTE ) ) && $EXTRA["execute"] = $EXECUTE;

		$DOM = new SimpleXMLElement("<?xml version=\"1.0\"?><response/>");

		$DOM = $this->Array2Xml( $EXTRA , $DOM );

		@ob_end_clean(); // End Cleaning Output Buffer
	
		print $DOM->asXML();
	
		return $EXTRA ;
		
	}

	public function Array2Xml( $array , $xmlObj ) {
		
		foreach( $array as $key => $value ) {
					
			if ( is_array( $value ) ) {
				
				if ( ! is_numeric( $key ) ) {

	            	$subNode = $xmlObj->addChild( $key );

					$subNode->addAttribute( "count" , count( $value ) );
	            	
	            	$this->Array2Xml( $value , $subNode ) ;

	            } else $xmlObj = $this->Array2Xml( $value , $xmlObj ) ;

			} else{

				if ( is_numeric( $key ) ) {

					$data = $xmlObj->addChild("data" , $value );

					$data->addAttribute( "index" , $key );

					$data->addAttribute( "type" , gettype( $value ) );

				} else {

					$data = $xmlObj->addChild( $key , $value );

					$data->addAttribute( "type" , gettype( $value ) );

				}

			}
				
		}
		
		return $xmlObj ;
			
	}

}

?>
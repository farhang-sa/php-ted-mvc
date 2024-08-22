<?php

defined( 'TExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
class dataPlugin extends Ted\Plugin {
	
	protected $AppStorageName = null ;
	
	public function __construct( ){
		
		$this->AppStorageName = 'DEVELOPER-' . $this->App() ;
		
	}
	
	public function __get( $name ) {

		$name = str_ireplace( "-" , "_" , $name );

		$name = str_ireplace( " " , "_" , $name );

		$dataValue = TedIntel::getVar( $name , null , $this->AppStorageName , false );
		
		if ( $dataValue === null ) return null;
		
		$this->{$name} = $dataValue;
		
		return $this->{$name};
	
	}
	
	public function __set( $prop , $value ) {
		
		return $this->{$prop} = TedIntel::setVar( $prop , $value , $this->AppStorageName , true );
		
	}
	
	public function Find( ) {

		$arguments = self::OptimizeControl( func_get_args( ) ) ;
		
		foreach( $arguments as $Name => $property ) {
			
			if ( $this->{$Name} ) continue;
			
			$Default 	= ( isset( $property[ 'default' ] ) )? $property[ 'default' ] 	: null ;
			
			$Default 	= ( isset( $property[ 'def' ] ) )	? $property[ 'def' ] 		: $Default ;
			
			$Desc 		= ( isset( $property[ 'desc' ] ) ) 	? $property[ 'desc' ] 		: null ;
			
			$Desc 		= ( isset( $property[ 'des' ] ) ) 	? $property[ 'des' ] 		: $Desc ;
			
			$Method 	= ( isset( $property[ 'method' ] ) ) ? $property[ 'method' ] 	: 'USER' ;
			
			$Method 	= ( isset( $property[ 'meth' ] ) ) 	? $property[ 'meth' ] 		: $Method ;
			
			$dataValue = TedIntel::getVar( $Name , $Default , $this->AppStorageName , true );
			
			$dataValue = $this->Evaluate( $Name , $dataValue );
			
			if ( $dataValue === false ) $lostargs[ $Name ] = $Desc;
		
		}
		
		return $this ;
	
	}

	public function Control( ) {

		$arguments = self::OptimizeControl( func_get_args( ) ) ;
		
		$lost_args = array ();
		
		foreach( $arguments as $Name => $property ) {
			
			if ( $this->{$Name} ) continue;
			
			$Default 	= ( isset( $property[ 'default' ] ) )? $property[ 'default' ] 	: null ;
			
			$Default 	= ( isset( $property[ 'def' ] ) )	? $property[ 'def' ] 		: $Default ;
			
			$Desc 		= ( isset( $property[ 'desc' ] ) ) 	? $property[ 'desc' ] 		: null ;
			
			$Desc 		= ( isset( $property[ 'des' ] ) ) 	? $property[ 'des' ] 		: $Desc ;
			
			$Method 	= ( isset( $property[ 'method' ] ) ) ? $property[ 'method' ] 	: 'USER' ;
			
			$Method 	= ( isset( $property[ 'meth' ] ) ) 	? $property[ 'meth' ] 		: $Method ;
			
			$dataValue = TedIntel::getVar( $Name , $Default , $this->AppStorageName , true );
			
			$dataValue = $this->Evaluate( $Name , $dataValue );
			
			if ( $dataValue === false ) $lost_args[ $Name ] = ( $Desc ) ? $Desc : "" ;
		
		}
		
		if ( empty( $lost_args) ) return true ;
		
		return false;
	
	}

	protected function OptimizeControl( $arguments = array( ) ){
		
		$MainControl = array() ;
		
		if ( count( $arguments ) == 1 && is_array( $arguments[ 0 ] ) ) $MainControl = $arguments[ 0 ] ;
			
		else {
				
			foreach ( $arguments as $newData ) {
		
				$newData = ( string ) trim( $newData ) ;
		
				$Name = null ;
		
				if ( stristr( $newData , "(" ) && stristr( $newData , ")" )  && $newData[ strlen( $newData ) - 1 ] == "(" ){
						
					$editExp = explode( "(" , $newData , 2 ) ;
		
					$Name = ( string ) trim( $editExp[ 0 ] );
						
					$editNew = ( string ) trim( $editExp[ 1 ] );
						
					$newData = ( string ) substr( $editNew , 0 , -1 );
						
				} if ( stristr( $newData , "," ) ) {
						
					$editExp = explode( "," , $newData ) ;
						
					$Name = null ;
						
					$Defa = null ;
						
					$Desc = null ;
						
					$Meth = null ;
						
					foreach ( $editExp as $n => $v ) {
		
						$v = ( string ) trim( $v ) ;
		
						$n = ( int ) $n ;
		
						$Deler = self::FindDelemiter( $newData );
		
						$nn = $vv = null ;
		
						if ( $Deler !== null ) {
								
							$editExp = explode( $Deler , $v , 2 ) ;
								
							$new = ( string ) trim( $editExp[ 0 ] ) ;
								
							if ( stristr( $new , "nam" ) ) $nn = 0 ;
								
							else if ( stristr( $new , "def" ) ) $nn = 1 ;
								
							else if ( stristr( $new , "des" ) ) $nn = 2 ;
								
							else if ( stristr( $new , "met" ) ) $nn = 3 ;
								
							$vv = ( string ) trim( $editExp[ 1 ] ) ;
								
						} else {
								
							$nn = $n ;
								
							$vv = $v ;
								
						}
		
						switch ( $nn ){
		
							case 0 : {
		
								$Name = $vv ;
		
								break ;
		
							} case 1 : {
		
								$Defa = $vv ;
		
								break ;
		
							} case 2 : {
		
								$Desc = $vv ;
		
								break ;
		
							} case 3 : {
		
								$Meth = $vv ;
		
								break ;
		
							}
		
						}
		
					}
						
					$MainControl[ $Name ] = array( "default" => $Defa , "desc" => $Desc , "method" => $Meth ) ;
						
				} else {
						
					if ( stristr( $newData , ":" ) && ! stristr( $newData , "=" ) && ! stristr( $newData , ">" ) ){
		
						// id : desc
							
						$editExp = explode( ":" , ( string ) trim( $newData ) , 2 ) ;
		
						$Name = ( string ) trim( $editExp[ 0 ] ) ;
		
						$Desc = ( string ) trim( $editExp[ 1 ] ) ;
		
						$MainControl[ $Name ] = array( "default" => null , "desc" => $Desc , "method" => null ) ;
		
					} else if ( ! stristr( $newData , ":" ) && stristr( $newData , "=" ) && ! stristr( $newData , ">" ) ){
		
						// id = defalut
							
						$editExp = explode( "=" , ( string ) trim( $newData ) , 2 ) ;
		
						$Name = ( string ) trim( $editExp[ 0 ] ) ;
		
						$Defa = ( string ) trim( $editExp[ 1 ] ) ;
		
						$MainControl[ $Name ] = array( "default" => $Defa , "desc" => null , "method" => null ) ;
		
					} else if ( ! stristr( $newData , ":" ) && ! stristr( $newData , "=" ) && stristr( $newData , ">" ) ){
		
						// id > method
							
						$editExp = explode( ">" , ( string ) trim( $newData ) , 2 ) ;
		
						$Name = ( string ) trim( $editExp[ 0 ] ) ;
		
						$Meth = ( string ) trim( $editExp[ 1 ] ) ;
		
						$MainControl[ $Name ] = array( "default" => null , "desc" => null , "method" => $Meth ) ;
		
					} else if ( stristr( $newData , ":" ) || stristr( $newData , "=" ) || stristr( $newData , ">" ) ){
		
						$Name = $Default = $Desc = $Method = null ;
		
						preg_match_all( '|(.*)[:^>^=]|iU' , $newData , $Name ) ;
		
						$Name = $Name [ 1 ][ 0 ] ;
		
						if ( stristr( $newData , "=" ) ) {
								
							$a = preg_match_all( '|.*=(.*)[:^>]|iU' , $newData , $Default ) ;
								
							if ( $a === 0 ) $Default = explode( "=" , $newData , 2 )[1] ;
								
							else $Default = $Default[ 1 ][ 0 ];
								
						} if ( stristr( $newData , ":" ) ) {
								
							$a = preg_match_all( '|.*:(.*)[>^=]|iU' , $newData , $Desc ) ;
								
							if ( $a === 0 ) $Desc = explode( ":" , $newData , 2 )[1] ;
								
							else $Desc = $Desc[ 1 ][ 0 ];
								
						} if ( stristr( $newData , ">" ) ) {
								
							$a = preg_match_all( '|.*>(.*)[=^:]|iU' , $newData , $Method ) ;
								
							if ( $a === 0 ) $Method = explode( ">" , $newData , 2 )[1] ;
								
							else $Method = $Method[ 1 ][ 0 ];
								
						}
		
						$MainControl[ $Name ] = array( "default" => $Default , "desc" => $Desc , "method" => $Method ) ;
		
					} else {
		
						$Name = ( string ) trim( $newData ) ;
		
						$MainControl[ $Name ] = array( "default" => null , "desc" => null , "method" => null ) ;
		
					}
						
				}
		
			}
				
		}
		
		return $MainControl ;
		
	}

	protected function Evaluate( $property , $value ) {

		if ( $value !== null ) {
			
			$this->{$property} = $value;
			
			return true;
		
		} else if ( $this->{$property} ) return true;
		
		return false;
	
	}

	public function Convert( $ConvertionArray ) {
		
		$ConvertionArray = ( is_array( $ConvertionArray ) ) ? $ConvertionArray : null ;
		
		if ( $ConvertionArray === null ) {
			
			$ConvertionArray = array( ) ;
			
			$args = func_get_args() ;
			
			$prev = null ;
			
			foreach ( $args  as $v ) {
				
			 	$deler = TED_Delemiters( $v ) ;
			 	
			 	if ( $deler ) {
			 		
			 		$deled = explode( $deler , $v , 2 ) ;
			 		
			 		$ConvertionArray[ trim( $deled[ 0 ] ) ] = trim( $deled[ 1 ] ) ;
			 		
			 	} else {
			 		
			 		$v = trim( $v ) ;
			 		
			 		if ( $prev ) {
			 			
			 			$ConvertionArray[ $prev ] = $v ;
			 			
			 			$prev = null ;
			 			
			 		} else {
			 			
			 			$prev = $v ;
			 			
			 			$ConvertionArray[ $prev ] = null ;
			 			
			 		}
			 		
			 	}
				
			} 
			
		}
		
		foreach( $ConvertionArray as $k => $v ) {
			
			if ( $this->{$k} ) {
				
				$this->{$v} = $this->{$k};
				
				unset( $this->{$k} );
			
			}
		
		}
		
		return $this;
	
	}

	public function Obtain( ) {

		return @get_object_vars( $this );
	
	}

}

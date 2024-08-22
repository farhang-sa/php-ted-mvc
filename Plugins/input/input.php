<?php

defined( 'TExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
class inputPlugin extends Ted\Plugin {
	
	protected $AppStorageName = null ;

	public function __construct( ){
		
		$this->AppStorageName = 'DEVELOPER-' . $this->App() ;
		
	}
	
	public function __get( $name ) {

		$Original = $name ;

		$dataValue = Ted\Intel::GetVar( $name , null , $this->AppStorageName , false );
		
		$dataValue = ( $dataValue ) ? $dataValue : Ted\Intel::GetVar( $name , null , 'USER' , false );

		if ( $dataValue !== null ) { $this->{$name} = $dataValue ; return $dataValue ; }

		$name = ucfirst( $Original ) ;

		$dataValue = Ted\Intel::GetVar( $name , null , $this->AppStorageName , false );
		
		$dataValue = ( $dataValue ) ? $dataValue : Ted\Intel::GetVar( $name , null , 'USER' , false );
	
		if ( $dataValue !== null ) { $this->{$name} = $dataValue ; return $dataValue ; }

		$name = strtolower( $Original ) ;

		$dataValue = Ted\Intel::GetVar( $name , null , $this->AppStorageName , false );
		
		$dataValue = ( $dataValue ) ? $dataValue : Ted\Intel::GetVar( $name , null , 'USER' , false );
		
		if ( $dataValue !== null ) { $this->{$name} = $dataValue ; return $dataValue ; }

		$name = strtoupper( $Original ) ;

		$dataValue = Ted\Intel::GetVar( $name , null , $this->AppStorageName , false );
		
		$dataValue = ( $dataValue ) ? $dataValue : Ted\Intel::GetVar( $name , null , 'USER' , false );
	
		if ( $dataValue !== null ) { $this->{$name} = $dataValue ; return $dataValue ; }

		/// For _ Values Link "Start_with_SECUENCE_seTing"
		$Needle = null ;

		$Needle = ( stristr( $Original , " " ) !== false ) ? " " : $Needle ;

		$Needle = ( stristr( $Original , "." ) !== false ) ? "." : $Needle ;

		$Needle = ( stristr( $Original , "-" ) !== false ) ? "-" : $Needle ;

		$Needle = ( stristr( $Original , "~" ) !== false ) ? "~" : $Needle ;

		$Needle = ( stristr( $Original , "_" ) !== false ) ? "_" : $Needle ;

		if ( $Needle ) {

			$NewSearch 	= array() ;

			$explode 	= explode( $Needle , $Original ) ; // 

			$NewValue 	= "" ;

			foreach ( $explode as $value ) $NewValue .= $Needle . ucfirst( $value ) ;

			$NewSearch[] = trim( $NewValue , $Needle ) ;

			$NewValue 	= "" ;

			foreach ( $explode as $value ) $NewValue .= $Needle . strtolower( $value ) ;

			$NewSearch[] = trim( $NewValue , $Needle ) ;

			$NewValue 	= "" ;

			foreach ( $explode as $value ) $NewValue .= $Needle . strtoupper( $value ) ;

			$NewSearch[] = trim( $NewValue , $Needle ) ;
		
			foreach ( $NewSearch as $name ) {
				
				$dataValue = Ted\Intel::GetVar( $name , null , $this->AppStorageName , false );
				
				$dataValue = ( $dataValue ) ? $dataValue : Ted\Intel::GetVar( $name , null , 'USER' , false );

				if ( $dataValue ){ $this->{$name} = $dataValue ; return $dataValue ; }

			}

		} return null ;

	}

	public function __set( $prop , $value ) {
		
		return $this->{$prop} = Ted\Intel::SetVar( $prop , $value , $this->AppStorageName , true );
		
	}
	
	public function __call( $name , $param = null ){
		
		$param = ( is_array( $param ) && isset( $param[ 0 ] ) ) ? $param[ 0 ] : null ;

		if ( ! $param ) return null ;
	
		$dataValue = $this->{$param} ;
		
		if ( $dataValue !== null ) return $dataValue ;
			
		else $dataValue = Ted\Intel::GetVar( $param , null , strtoupper( $name ) , false );
		
		if ( $dataValue === null ) return null;
		
		$this->{$param} = $dataValue;
		
		return $this->{$param};
		
	}

	public function doSearch(){

		return call_user_func_array( [ $this , "Search" ] , func_get_args() ) ;
		
	}

	public function SearchFor(){

		return call_user_func_array( [ $this , "Search" ] , func_get_args() ) ;

	}

	public function VariableSearch(){

		return call_user_func_array( [ $this , "Search" ] , func_get_args() ) ;
		
	}

	public function SearchForVariable(){

		return call_user_func_array( [ $this , "Search" ] , func_get_args() ) ;
	}

	public function Search(){

		// Search1 , Search2 

		// [ Search1 , Search2 ]

		$Args = func_get_args( ) ;
		
		$First = ( isset( $Args[ 0 ] ) ) ? $Args[ 0 ] : null ;
		
		$Args = ( is_array( $First ) && count( $Args  ) == 1 ) ? $First : $Args ;

		if ( empty( $Args ) ) return null ;

		else $Args = array_values( $Args ) ;

		$Name = $Args[ 0 ] ;

		foreach ( $Args as $value ){

			if ( $this->{$value} ) {

				$this->{$Name} = $this->{$value} ;

				break ;

			} 

		} return $this->{$Name} ;

	}

	public function Airport(){

		/// [ "search1" , "search2" ] , [ "value1" , "value2" ]
		/// [ "search1" => "value3" , "search2" => [ "someValue1" , "someValue2" ] ]
		$searchList = array() ; // [ "search1" => [ "value1" ] , "search2" => [ "value2" , "someAnotherValue" ] ]

		$args = func_get_args() ;

		if ( count( $args ) == 2 ) {

			$Inp1 = $args[ 0 ] ;

			$Inp2 = $args[ 1 ] ;

			if ( is_array( $Inp1 ) ) {

				foreach ( $Inp1 as $key => $value1 ) {
					
					if ( is_array( $Inp2 ) ) 

						foreach ( $Inp2 as $value2 ) $searchList[ $value1 ][] = $value2 ;

					else $searchList[ $value1 ] = [ $Inp2 ] ;

				}

			} else $searchList[ ( string ) $Inp1 ]= $Inp2 ;

		} elseif ( count( $args ) == 1 && is_array( $args[ 0 ] ) ) {

			$Inp1 = $args[ 0 ] ;

			foreach ( $Inp1 as $key => $value ) {

				if ( ! is_int( $key ) ) {

					if ( is_array( $value ) ) $searchList[ $key ] = $value ;

					else $searchList[ $key ] = [ $value ];

				}

			}

		}

		/////////////////////
		foreach ( $searchList as $search => $values ) {
			
			$Search = $this->{$search} ;

			foreach ( $values as $value ){

				$value = is_string( $value ) ? strtolower( $value ) : $value ;

				if( $Search === $value ) return $Search ;

				if ( strtolower( $Search ) === $value ) return $Search ;

			}

		} return null ;

	}

	public function Find() {

		$arguments = self::OptimizeControl( func_get_args( ) ) ;
		
		foreach( $arguments as $Name => $property ) {
			
			if ( $this->{$Name} ) continue;
			
			$Default 	= ( isset( $property[ 'default' ] ) )? $property[ 'default' ] 	: null ;
			
			$Default 	= ( isset( $property[ 'def' ] ) )	? $property[ 'def' ] 		: $Default ;
			
			$Desc 		= ( isset( $property[ 'desc' ] ) ) 	? $property[ 'desc' ] 		: null ;
			
			$Desc 		= ( isset( $property[ 'des' ] ) ) 	? $property[ 'des' ] 		: $Desc ;
			
			$Method 	= ( isset( $property[ 'method' ] ) ) ? $property[ 'method' ] 	: 'USER' ;
			
			$Method 	= ( isset( $property[ 'meth' ] ) ) 	? $property[ 'meth' ] 		: $Method ;

			$replace = ( $Default ) ? true : false ;
			
			$dataValue = Ted\Intel::GetVar( $Name , $Default , $Method , $replace  );
			
			$dataValue = $this->Evaluate( $Name , $dataValue );
			
			if ( $dataValue === false ) $lostargs[ $Name ] = $Desc;
		
		}
		
		return $this ;
	
	}

	public function Control( ) {

		$arguments = self::OptimizeControl( func_get_args( ) ) ;
		
		$lostargs = array ();
		
		foreach( $arguments as $Name => $property ) {
			
			if ( $this->{$Name} ) continue;
			
			$Default 	= ( isset( $property[ 'default' ] ) )? $property[ 'default' ] 	: null ;
			
			$Default 	= ( isset( $property[ 'def' ] ) )	? $property[ 'def' ] 		: $Default ;
			
			$Desc 		= ( isset( $property[ 'desc' ] ) ) 	? $property[ 'desc' ] 		: null ;
			
			$Desc 		= ( isset( $property[ 'des' ] ) ) 	? $property[ 'des' ] 		: $Desc ;
			
			$Method 	= ( isset( $property[ 'method' ] ) ) ? $property[ 'method' ] 	: 'USER' ;
			
			$Method 	= ( isset( $property[ 'meth' ] ) ) 	? $property[ 'meth' ] 		: $Method ;

			$Method 	=  strtoupper( $Method ) ;

			$replace = ( $Default ) ? true : false ;
			
			$dataValue = Ted\Intel::GetVar( $Name , $Default , $Method , $replace );

			$dataValue = $this->Evaluate( $Name , $dataValue );
			
			if ( $dataValue === false ) $lostargs[ $Name ] = ( $Desc ) ? $Desc : "" ;
		
		}

		if ( count( $lostargs ) >= 1 ) $this->kill( $lostargs );
		
		return $this;
	
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
			
			if ( $this->$k ) {
				
				$this->{$v} = $this->{$k};
				
				unset( $this->{$k} );
			
			}
		
		}
		
		return $this;
	
	}

	public function Obtain( ) {

		return @get_object_vars( $this );
	
	}

	public function kill( $needs = array() ) {

		$arr["CODE"] = "0";
		
		$arr["ERROR"] = " Please Fill This Fields ";
		
		$arr["NEED"] = $needs;
		
		return parent::kill( $arr );
	
	}

}

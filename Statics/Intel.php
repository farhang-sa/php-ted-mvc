<?php

namespace Ted ;

defined( 'TExec' ) or die( 'Access Denied' );

/**
* final class Intel {} ( Like Joomla's Input Class )
*
* Intel Class Works With User Related Intelligance
*
* @package Ted/Intel
* @author Farhang Saeedi
* @link farhang-saeedi.com
*/

final class Intel {
	
	private static $GLOBALS = array();
	private static $RouteTable = array();
	private static $Application = null;
	private static $Component = null;
	private static $Params = array();
	private static $Init = false;

	public static $En 		= null ;
	public static $En_cli 	= "cli";
	public static $En_net 	= "net";


	/**
	* public static function Initialise() 
	*
	* Collects All The User Entered Data 
	*
	* @param  None
	* @return boolean True On Success , boolean False On Redue
	* @package Ted/Intel
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/

	public static function Initialise( ){

		if ( self::$Init ) return true ;
		
		self::$Init = true ;
		
		self::AcceptRequests(); 
		
		self::BuildRequests();
		
		self::$Init = true ;

		// Find Environment Type
		self::$En = ( IsCLI() ) ? self::$En_cli : self::$En_net ;
		
	}

	/**
	* public static function en() , en_cli() , en_net()
	*
	*  Work With The Current Environment Type
	*
	* @param  None
	* @return string 'cli' if php exec | string 'net'
	* @package Ted/Intel 
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/

	public static function En( )	{ 	return self::$En 		; }

	public static function En_cli( ){ 	return self::$En_cli 	; }

	public static function En_net( ){ 	return self::$En_net 	; }
	/**
	* public static function CleanInput() 
	*
	* Cleans The argument From 'Script' and 'php' tags
	*
	* @param  input 
	* @return cleaned input
	* @package Ted/Intel
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/

	private static function CleanInput( $input ){

		if( is_string( $input ) ){

			$input = str_ireplace( "<script>", "[script]" , $input );
			$input = str_ireplace( "</script>", "[/script]" , $input );
			$input = str_ireplace( "<?php", "[php]" , $input );

		} else if ( is_array( $input ) ) 

			foreach ( $input as $key => $value )

				$input[$key] = self::CleanInput( $value );

		return $input ;

	}

	/**
	* private static final function AcceptRequests()
	*
	* 	Collects User's Inputs At All Methods And Ways
	*
	* @param  None
	* @return Void
	* @package Ted/Intel 
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/
		
	private static function AcceptRequests(){
		
		global $argv;
		
		$Reqs = array();
		
		if ( isset( $_POST ) && ! empty( $_POST ) ) {

			$_POST = addSlashe( $_POST );

			foreach( $_POST as $k => $v ) 

				$Reqs['POST'][$k] = self::CleanInput( $v );

		} if ( isset( $_GET ) && ! empty( $_GET ) ) {

			$_GET = addSlashe( $_GET );

			foreach( $_GET as $k => $v ) 

				$Reqs['GET'][$k] = self::CleanInput( $v );

		} if ( isset( $_FILES ) && ! empty( $_FILES ) ) {
			
			foreach( $_FILES as $k => $v ) 

				$Reqs['FILES'][$k] = $v ;
		
		} if ( isset( $_COOKIE ) && ! empty( $_COOKIE ) ) {
			
			foreach( $_COOKIE as $k => $v ) 

				$Reqs['COOKIE'][$k] = $v ;
		
		} if ( isset( $_SERVER ) && ! empty( $_SERVER ) ) {
			
			foreach( $_SERVER as $k => $v ) 

				$Reqs['SERVER'][$k] = $v ;
			
		} if ( PHP_SAPI == "cli" && isset( $argv ) ) {
			
			unset( $argv[0] );
			
			$argv = array_values( $argv );
			
			$args = array ();
			
			$argk = array ();
			
			foreach( $argv as $k => $v ) {
				
				$endPosision = strpos( $v , "=" );
				
				$endPosision = ( $endPosision == false ) ? strlen( $v ) : $endPosision;
				
				if ( isset( $v[0] ) && isset( $v[1] ) && ( $v[0] . $v[1] ) == "--" ) {
					
					$endPosision -= 2;
					
					$argk[] = substr( $v , 2 , $endPosision ) . ":";
				
				} else if ( isset( $v[0] ) && $v[0] == "-" ) {
					
					$endPosision -= 1;
					
					$args[] = substr( $v , 1 , $endPosision ) . ":";
				
				} else {
					
					$inputs = explode( ',' , $v );
					
					foreach( $inputs as $vk ) {
						
						$deler = TED_Delemiters( $vk );
						
						if ( is_string( $deler ) && $deler !== null ) {
							
							$explo = explode( $deler , $vk , 2 );
							
							$Reqs['CLI'][$explo[0]] = $explo[1];
						
						}
					
					}
				
				}
			
			} unset( $endPosision , $inputs , $deler , $explo ) ;
			
			$imploded = implode( "" , $args );
			
			$options = getopt( $imploded , $argk );
			
			foreach( $options as $k => $v ) {
				
				$Reqs['CLI'][$k] = $v;
			
			} unset( $argv , $args , $argk , $options , $imploded ) ;
		
		} else if ( PHP_SAPI == "cli-server" ){
			
			$magicUrl = ( isset( $_SERVER["REQUEST_URI"] ) ) ? $_SERVER["REQUEST_URI"] : null ;

			$magicUrl = ( !$magicUrl && isset($_SERVER["PHP_SELF"] ) ) ? $_SERVER["PHP_SELF"] : $magicUrl ;

			$magicUrl = ( !$magicUrl && isset($_SERVER["SCRIPT_NAME"] ) ) ? $_SERVER["SCRIPT_NAME"] : $magicUrl ;

			$Reqs["GET"]["url"] = $magicUrl ;

			unset( $magicUrl ) ;
			
		} else if ( isset( $_SERVER['PATH_INFO'] ) ) {
			
			$Reqs["GET"]["NewUrl"] = $_SERVER['PATH_INFO'];
			
		} self::$GLOBALS = $Reqs;
		
		if ( self::getVar( 'url' ) ) {
			
			self::setVar( "url" , str_replace( "/" . TPath_Index , "" , self::getVar( 'url' ) ) , 'GET' , true );
		
		} unset( $Reqs );
		
	}

	/**
	* public static function getVar()
	*
	* 	Returns The Requested Variable Value From Users Inputs
	*
	* @param  String Name , String Default , String Method , boolean Default ,
	* @return Mixed Variable Value
	* @package Ted/Intel
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/
	
	public static function GetVar( $name = null , $def = null , $method = null , $considerDef = false ) {

		if ( $name ) {
			
			if ( $method ) {

				$method = strtoupper( $method ) ;
				
				if ( isset( self::$GLOBALS[ $method ] ) ) {

					$retL = strtolower( $name ) ;

					$retU = strtoupper( $name ) ;

					$retF = ucfirst( $name ) ;

					$retC = ( isset( self::$GLOBALS[$method][$name] ) ) ? self::$GLOBALS[$method][$name] : null ;

					$retL = ( isset( self::$GLOBALS[$method][$retL] ) ) ? self::$GLOBALS[$method][$retL] : null ;

					$retU = ( isset( self::$GLOBALS[$method][$retU] ) ) ? self::$GLOBALS[$method][$retU] : null ;

					$retF = ( isset( self::$GLOBALS[$method][$retF] ) ) ? self::$GLOBALS[$method][$retF] : null ;
						
					$ret = $retC ? $retC : $retL ;
						
					$ret = $ret  ? $ret  : $retU ;
						
					$ret = $ret  ? $ret  : $retF ;
					
					if ( $ret !== null ) return $ret;
					
					if ( $considerDef ) return self::SetVar( $name , $def , $method , true );
						
				} else if ( $considerDef ) return self::SetVar( $name , $def , $method , true );
					
				return null;
			
			} else {
				
				foreach( self::$GLOBALS as $meth => $vars ) {

					$retL = strtolower( $name ) ;

					$retU = strtoupper( $name ) ;

					$retF = ucfirst( $name ) ;

					$retC = ( isset( self::$GLOBALS[$method][$name] ) ) ? self::$GLOBALS[$method][$name] : null ;

					$retL = ( isset( self::$GLOBALS[$method][$retL] ) ) ? self::$GLOBALS[$method][$retL] : null ;

					$retU = ( isset( self::$GLOBALS[$method][$retU] ) ) ? self::$GLOBALS[$method][$retU] : null ;

					$retF = ( isset( self::$GLOBALS[$method][$retF] ) ) ? self::$GLOBALS[$method][$retF] : null ;
						
					$ret = $retC ? $retC : $retL ;
						
					$ret = $ret  ? $ret  : $retU ;
						
					$ret = $ret  ? $ret  : $retF ;
					
					if ( $ret !== null ) return $ret;
					
				}
				
				if ( $considerDef ) return self::SetVar( $name , $def , 'USER' , true );
					
				return null;
			
			}
		
		} else {
			
			if ( $method ) {

				$method = strtoupper( $method ) ;
				
				return ( isset( self::$GLOBALS[$method] ) ) ? self::$GLOBALS[$method] : false;

			} return null;
		
		}
	
	}

	/**
	* public static function setVar()
	*
	* 	Sets The Requested Variable Value For Users Inputs
	*
	* @param  String Name , String Default , String Method , boolean Default ,
	* @return Mixed Variable's Newly Seted Value
	* @package Ted/Intel 
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/

	public static function SetVar( $name = null , 
		$def = null , $method = null , $overWrite = false ) {

		if ( $method ) {

			$method = strtoupper( $method ) ;
			
			$var = self::GetVar( $name , false , $method , false );
			
			if ( $var !== null ) {
				
				if ( $overWrite ) self::$GLOBALS[$method][$name] = $def;
				
				else $def = $var;
			
			} else self::$GLOBALS[$method][$name] = $def;
			
			return $def;
		
		} else {
			
			$var = self::GetVar( $name , false , null , false );
			
			if ( $var !== null ) {
				
				if ( $overWrite ) self::$GLOBALS['USER'][$name] = $def;
				
				else $def = $val;
			
			} else self::$GLOBALS['USER'][$name] = $def;
			
			return $def;
		
		}
	
	}

	/**
	* public static function getRoute()
	*
	* 	Returens Users Requested Route As Array
	*
	* @param  None
	* @return Array Route
	* @package Ted/Intel
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/

	public static function GetRoute() {

		foreach( self::$RouteTable as $k => $v ) 

			if ( ! $v || empty( $v ) ) 

				unset( self::$RouteTable[$k] );
		
		self::$RouteTable = array_values( self::$RouteTable );

		return self::$RouteTable;
	
	}

	/**
	* private static final function BuildRequests()
	*
	* 	Compiles The User Collected Inputs In Order To Be Usable At Applications
	*
	* @param  None
	* @return Void
	* @package Ted/Intel 
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/
	
	private static function BuildRequests( ) {
		
		self::BuildForEnv(self::GetVar(null,null,'COOKIE',false));
		
		self::BuildForEnv(self::GetVar(null,null,'FILES',false));
		
		self::BuildForEnv(self::GetVar(null,null,'GET',false));
		
		self::BuildForEnv(self::GetVar(null,null,'POST',false));
		
		self::BuildForEnv(self::GetVar(null,null,'CLI',false));
		
		self::BuildForUrl(self::GetVar('NewUrl',false,'GET',false));
		
		self::BuildForUrl(self::GetVar('url',false,'GET',false));
		
		self::$GLOBALS['USER'] = self::$Params;
		
		self::$RouteTable = self::GetRoute();

	}

	/**
	* private static final function BuildForUrl()
	*
	* 	Compiles The User's Requested Urls
	*
	* @param  None
	* @return Void
	* @package Ted/Intel
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/

	private static function BuildForUrl( $url = null ) {

		$builded = array ();
		
		if ( $url ) {
			
			$url = trim( $url , '/' );
			
			$url = trim( $url , ' ' );
			
			$url = explode( '/' , $url );
			
			if ( count( $url ) >= 1 ) {
				
				$SlashPrevName = null;
				
				$isValue = 0;
				
				foreach( $url as $k => $v ) {
					
					$ex = ( stristr( $v , ',' ) ) ? explode( ',' , $v ) : array();
					
					$ex = ( stristr( $v , '|' ) && count( $ex ) === 0 ) ? explode( '|' , $v ) : $ex;
					
					if ( count( $ex ) === 0 ) {
						
						$delemiter = Delemiters( ( string ) $v );
						
						if ( $delemiter !== null ) {
							
							$SlashPrevName = null;
							
							$isValue = 0;
							
							self::GetPropertyStatus( explode( $delemiter , $v ) );
							
						} else {
							
							$isValue = ( $isValue === 0 ) ? 1 : 0 ;
							
							$SlashPrevName = self::GetSingleStatus( $SlashPrevName , $v , $isValue );
							
							$isValue = ( $isValue === 0 ) ? 1 : 0 ;
						
						}
						
					} else {
						
						foreach( $ex as $prop => $val ) {
							
							$delemiter = Delemiters( ( string ) $val );
							
							if ( $delemiter !== null ) {
								
								$SlashPrevName = null;
								
								$isValue = 0;
								
								self::GetPropertyStatus( explode( $delemiter , $val ) );
								
							} else {
								
								foreach( $ex as $nu => $newRoutes ) {
									
									$deleEx = Delemiters( ( string ) $newRoutes );
									
									if ( $deleEx === null ) {
							
										$isValue = ( $isValue === 0 ) ? 1 : 0 ;
										
										$SlashPrevName = self::GetSingleStatus( $SlashPrevName , $newRoutes , $isValue );
										
										$isValue = ( $isValue === 0 ) ? 1 : 0 ;
									
									} else {
										
										$SlashPrevName = null;
										
										$isValue = 0;
										
										self::GetPropertyStatus( explode( $deleEx , $newRoutes ) );
									
									}
								
								} break;
							
							}
						
						}
						
					}
					
				} 
			
			}
		
		}
	
	}

	/**
	* private static final function buildForEnv()
	*
	* 	Compiles The User's Inputed Environmental Variables
	*
	* @param  None
	* @return Void
	* @package Ted/Intel
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/

	private static function buildForEnv( $Env = null ) {
	
		if ( $Env ) {
				
			if ( is_array( $Env ) ) {
	
				$Env = self::GetStatus( $Env );
	
				foreach( $Env as $k => $v ) { self::$Params[$k] = $v; }
				
			}
	
		}
	
	}

	/**
	* private static final function getStatus()
	*
	* 	Searches For Routeing Variables In User's Environmental Inputs 
	*
	* @param  Array Inuted Variables ( Like $_POST )
	* @return Array Exactly Inputed Variables Without Routing Variasbles
	* @package Ted/Intel
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/

	private static function GetStatus( $array ) {

		if ( isset( $array['application'] ) ) {
			
			self::$Application = ( $array['application'] );
			
			unset( $array['application'] );
		
		} else if ( isset( $array['appl'] ) ) {
			
			self::$Application = ( $array['appl'] );
			
			unset( $array['appl'] );
		
		} else if ( isset( $array['app'] ) ) {
			
			self::$Application = ( $array['app'] );
			
			unset( $array['app'] );
		
		} if ( isset( $array['component'] ) ) {
			
			self::$Component = ( $array['component'] );
			
			unset( $array['component'] );
		
		} else if ( isset( $array['comp'] ) ) {
			
			self::$Component = ( $array['comp'] );
			
			unset( $array['comp'] );
		
		} else if ( isset( $array['com'] ) ) {
			
			self::$Component = ( $array['com'] );
			
			unset( $array['com'] );
		
		} return $array;
	
	}

	/**
	* private static final function GetSingleStatus()
	*
	* 	Works With Url's To Find Variables
	*
	* @param  String Current Url Value , String Previews Value , 
	*							boolean Is Value For Routing , Int Routing Level 
	* @return String Variable Value
	* @package Ted/Intel
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/

	private static function GetSingleStatus( $prevSlash = null , $val2 = null , $isValue = 1 ) {

		$SlashPrevName = null ; $k2 = 3;

		if ( self::$Application === null ) $k2 = 0;
		
		else if ( self::$Component === null ) $k2 = 1;
		
		switch( $k2 ) {
			
			case 0 : {
					
				self::$RouteTable[0] = $val2 ;
				
				self::$Application = $val2 ;
				
				$SlashPrevName = self::$Application;
				
				break;
				
			} case 1 : {
				
				self::$RouteTable[1] = $val2;
				
				self::$Component = $val2 ;
				
				$SlashPrevName = self::$Component;
				
				break;
				
			} default : {
					
				if ( ( int ) $val2 === 0 ) self::$RouteTable[] = $val2 ;
				
				if ( ( int ) $val2 === 0 ) $SlashPrevName = $val2 ;
				
				else $SlashPrevName = null ;
				
				break;
				
			}
		
		}
		
		if ( ( int ) $val2 === 0 ) self::$Params[$val2] = self::SetVar( $val2 , $val2 , null , true );
		
		if ( $prevSlash !== null ) {
			
			if ( $k2 <= 2 || $isValue === 1 ) 

				self::$Params[$prevSlash] = self::SetVar( $prevSlash , $val2 , null , true );
			
			if ( $k2 > 2 && ( int ) $val2 === 0 ) 

				self::$Params[$val2] = self::SetVar( $val2 , true , null , true );
			
		} return $SlashPrevName;
	
	}

	/**
	* private static final function GetPropertyStatus()
	*
	* 	Searches For Routing Variables In Array Values Of User Inputs
	*
	* @param  Array Variable Value To Search
	* @return Void
	* @package Ted/Intel
	* @author Farhang Saeedi
	* @link farhang-saeedi.com
	*/

	private static function GetPropertyStatus( $TheExploded = array() ) {

		while( ! empty( $TheExploded ) ) {

			$property = ( isset( $TheExploded[0] ) ) ? $TheExploded[0] : "";
			
			$value = ( isset( $TheExploded[1] ) ) ? $TheExploded[1] : true ;

			array_shift( $TheExploded ) ;

			if ( ( int ) $property > 0 ) continue ;

			$oldParent = null ;
			
			if ( ( strtoupper( $property ) == 'APPLICATION' || strtoupper( $property ) == 'APP' ) && ! is_bool( $value ) ) {
				
				self::$RouteTable[0] = $value ;
				
				self::$Application = $value ;
				
			} else if ( ( strtoupper( $property ) == 'COMPONENT' || strtoupper( $property ) == 'COMP' ) && ! is_bool( $value ) ) {
				
				$oldParent = self::$Application ;
				
				self::$RouteTable[1] = $value ;
				
				self::$Component = $value ;
				
			} else self::$Params[$property] = self::SetVar( $property , $value , null , true ) ;
			
			if ( $oldParent !== null ) self::$Params[$oldParent] = self::GetVar( $property , $value , null , true );

		}
	
	}

}

?>
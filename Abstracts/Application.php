<?php

namespace Ted ;

defined( 'TExec' ) or die( 'Access Denied' );

abstract class Application extends AppBase {

	protected $AppName ;
	protected $AppRoot ;

	protected $AppError ;
	protected $AppComp ;
	protected $AppFunc ;

	protected $AppSink ;
	protected $AppState ;
	protected $AppRoute ;
	protected $AppHistory 	= array();

	protected $AppInterfaces = array();
	protected $AppCurrentUI ;
	
	protected $classObjects ;
	protected $PlgDire ;
	protected $CompDir ;

	public abstract function Initialise(); // Get Called After Instancing Application
	public abstract function Finish(); // Get Called At The End Of Application

	public function __construct( $root = null , $route = array() ){
		
		$this->AppInitingMethod( $root , $route );	

	}
	
	/// Plugin Load Handling With Magic Methods
	public function __call( $name , $params = array() ) {

		$retValue = $name;
	
		$retValue = trim( $retValue , ' ' );
	
		$retValue = str_ireplace( '::' , '.' , $retValue );
	
		$retValue = str_ireplace( '->' , '.' , $retValue );
	
		$retValue = str_ireplace( '-' , '.' , $retValue );
	
		$retValue = str_ireplace( '>' , '.' , $retValue );
	
		$retValue = trim( $retValue , '.' );
	
		$retValue = trim( $retValue , ' ' );
	
		$newObj = $this->{$retValue};
	
		if ( is_object( $newObj ) ) {
				
			$newCls = get_class( $newObj );
				
			return call_user_func_array( [ $this->{$retValue} , $newCls ] , $params );
	
		} return $this->__get( $retValue );
	
	}
	
	public function __get( $name ) { return $this->LoadPlugin( $name ); }

	/***************************************
	/// Base Application Behavior Controllers */
	protected function AppInitingMethod( $root = null , $route = array() ){
		
		if ( isset( self::$Instances[ $this->AppName() ] ) ) return true ;
		
		$this->AppRoot = ( ! is_dir( $root) ) ? TPath_Root : $root ;
		
		$defaultRoute = array();
		
		( $this->AppComp ) && array_push( $defaultRoute , $this->AppComp );
		
		( $this->AppFunc ) && array_push( $defaultRoute , $this->AppFunc );
		
		$AppStste = ( $this->AppState !== null ) ? $this->AppState : false ;
			
		$route = array_values( $route ) ;

		$first = isset( $route[ 0 ] ) ? $route[ 0 ] : null ;

		if ( $first && strtolower( $first ) === strtolower( $this->AppName ) ) {

			unset( $route[ 0 ] , $first );

			$route = array_values( $route ) ;

		} if ( ! empty( $route ) ){
			
			$last = $route[ count( $route ) - 1 ] ;
			
			if ( is_bool( $last ) ) {
				
				$AppStste = $last ;
			
				unset( $route[ count( $route ) - 1 ] );

				$route = array_values( $route ) ;
				
			}
			
		} $route = ( ! empty( $route ) ) ? $route : $defaultRoute ;

		array_unshift( $route , $this->AppName );
		
		array_push( $route , $AppStste );

		call_user_func_array( [ $this , 'AppRouterMethod' ] , $route ) ;

		if ( empty( $this->AppHistory ) ) 

			call_user_func_array( [ $this , 'AppRouterMethod' ] , $defaultRoute ) ;

		// Find UI's
		$UiFolders = [ "UI" , "UIs" , "Interfaces" , "UserInterfaces" ] ;

		$uiDirectory = FindDirectory( $this->AppRoot() , $UiFolders );
				
		$this->AppInterfaces = FindUserInterfaces( $uiDirectory , $this->AppName );

		if ( empty( $this->AppInterfaces ) ) {

			$uiDirectory = FindDirectory( TPath_Base , $UiFolders ) ;
				
			$this->AppInterfaces = FindUserInterfaces( $uiDirectory , "TED" ) ;

		}
		
		// Set Plugin Classes Plate
		$this->classObjects = array() ;

		$PlgDire = ( $this->PlgDire ) ? $this->PlgDire : "Plugins" ;

		$PlgDire = ( is_dir( $PlgDire ) ) ? $PlgDire : FindDirectory( $this->AppRoot() , $PlgDire ) ;

		$PlgDire = ( is_dir( $PlgDire ) ) ? $PlgDire : TPath_BasePlugins ;

		$this->PlgDire = $PlgDire ;
		
		self::$Instances[ strtolower( $this->AppName() ) ] = $this ;

	}

	protected function AppUiFinderMethond( $uiName = null , $applayChange = true ){

		$uiName = $this->UIFinder( $uiName ); 
		
		$ui = $this->AppInterfaces[ $uiName ] ;

		if ( is_object( $ui ) ) {
			
			if( $applayChange )

				$this->AppCurrentUI = $uiName ;

			return $this->AppInterfaces[ $uiName ] ;

		} else if ( is_file( $ui ) ) {

			include_once $ui ;

			$appName 	= $this->AppName() ;

			$classNames = array() ;
			
			$classNames[] = "{$appName}_{$uiName}_Interface" ;
			
			$classNames[] = "{$appName}{$uiName}Interface" ;
			
			$classNames[] = "{$appName}_{$uiName}_UI" ;
			
			$classNames[] = "{$appName}{$uiName}UI" ;
			
			$classNames[] = "{$appName}App_{$uiName}_Interface" ;
			
			$classNames[] = "{$appName}App{$uiName}Interface" ;
			
			$classNames[] = "{$appName}App_{$uiName}_UI" ;
			
			$classNames[] = "{$appName}App{$uiName}UI" ;

			$AppInterface = null ;

			foreach ( $classNames as $newClassName ) {

				$newClassName = FindClass( $newClassName ) ;
				
				if ( $newClassName ) { $AppInterface = $newClassName ; break ; }

			} if ( $AppInterface )

				$this->AppInterfaces[ $uiName ] = new $AppInterface( $this->AppName() ) ;
				
			else { 

				$AppInterface = "{$uiName}_Interface" ;

				Import( "Base.Interfaces.{$AppInterface}" );

				if ( class_exists( $AppInterface ) )

					$this->AppInterfaces[ $uiName ] = new $AppInterface( $this->AppName() ) ;

				else {

					unset( $this->AppInterfaces[ $uiName ] ) ;

					return $this->AppUiFinderMethond( $uiName ) ;

				}

			} if( $applayChange )

				$this->AppCurrentUI = $uiName ;
			
			return $this->AppInterfaces[ $uiName ] ;

		} else if ( is_string( $ui ) ){

			if ( class_exists( $ui ) ) {

				if( $applayChange )

					$this->AppCurrentUI = $uiName ;

				$this->AppInterfaces[ $uiName ] = new $ui( $this->AppName() ) ;

				return $this->AppInterfaces[ $uiName ] ;

			} else if ( isset( $this->AppInterfaces[ $ui ] ) )

				return $this->AppUiFinderMethond( $ui );

		} unset( $this->AppInterfaces[ $uiName ] ) ;
		
		return $this->AppUiFinderMethond( $uiName );

	}
	
	protected function AppRouterMethod() {
		
		// Prepare The Route
		$args = func_get_args( ) ;
		
		while( count( $args ) == 1 && is_array( $args[ 0 ] ) ){

			$First = ( isset( $args[ 0 ] ) ) ? $args[ 0 ] : null ;
			
			$args = ( is_array( $First ) && count( $args ) == 1 ) ? $First : $args ;

		} if ( empty( $args ) ) return false ;
		
		list( $Route , $Args ) = FindRouteElements( $args );

		$state = array_pop( $Route ) ;
		
		$state = ( is_bool( $state ) ) ? $state : $this->AppState ;
		
		$this->AppState = $state ;

		// Find The Route Component File
		return $this->AppRoute = $this->Router( array_values( $Route ) );

	}
	
	/***************************************
	/// General Application Behavior Controllers */

	public function Respond( $uiType = null ){
		
		return $this->AppInterface( $uiType )->Respond( true ) ;
		
	}

	public function Route() {
		
		return call_user_func_array( [ $this , 'AppRouterMethod' ] , 
			func_get_args() ) ;

	}

	public function Sink(){

		$this->AppSink = is_array( $this->AppSink ) ?
			$this->AppSink : array() ;
		
		$ARGS = func_get_args();

		if ( ! empty( $ARGS ) ) {

			$First = ( isset( $ARGS[ 0 ] ) ) ? $ARGS[ 0 ] : null ;
			
			$this->AppSink = ( is_array( $First ) && count( $ARGS ) == 1 ) ? $First : $ARGS ;

			$this->AppHistory = array() ;

			$this->AppRoute = null ;

		} return ( empty( $this->AppSink ) ) ? false : $this->AppSink ;

	}

	public function Execute() {

		$Args = func_get_args( ) ;

		list( $Route , $Args ) = FindRouteElements( $Args );
		
		if ( ! empty( $Route ) ) 

			call_user_func_array( [ $this , 'AppRouterMethod' ] , $Route );

		if ( $this->Sink() ) return $this->Response( $this->Sink() );
		
		if ( file_exists( $this->AppRoute ) ) {

			foreach ( $Args as $ke_24ehj32y => $value ) {

				${$ke_24ehj32y} = $value ;

				${ucfirst($ke_24ehj32y)} = $value ;

				${strtoupper($ke_24ehj32y)} = $value ;

				${strtolower($ke_24ehj32y)} = $value ;

			} return include $this->AppRoute ;

		} return $this->Response(["message" => "Incorrect Route" , "code" => "404"]);

	}

	public function SilentCall(){

		list( $Route , $Args ) = FindRouteElements( func_get_args( ) );
		
		array_pop( $Route ) ; 

		array_push( $Route , true , $Args ) ;

		return call_user_func_array( [ $this , 'Call' ] , $Route ) ;

	}

	public function Call() {

		$Olds = $this->AppHistory() ;

		array_push( $Olds , $this->AppState ) ;

		$Rets = call_user_func_array( [ $this , 'Execute' ] , func_get_args( ) ) ;
		
		call_user_func_array( [ $this , 'AppRouterMethod' ] , $Olds ) ;
		
		return $Rets ;
		
	}
	
	public function Response(){
		
		$ARGS = func_get_args();
		
		$First = ( isset( $ARGS[ 0 ] ) ) ? $ARGS[ 0 ] : null ;
		
		$ARGS = ( is_array( $First ) && count( $ARGS ) == 1 ) ? $First : $ARGS ;
		
		if ( $this->AppState ) 

			return ( ! empty( $ARGS ) ) ? $ARGS : null ;
		
		if ( $this->AppInterface() ) 

			return call_user_func_array( [ $this->AppInterface() , 'Response' ] , [ $ARGS ] ) ;

		return [ "message" => "No User Interface" , "code" => "403" ];
			
	}

	/***************************************
	/* Application Intels / Genral Intel Stuff */

	public function AppRoot()	{ return $this->AppRoot ; }

	public function AppRootWww(){ return FindWebPath( $this->AppRoot() ) ; }

	public function AppRootUrl(){ return $this->AppRootWww() ; }

	public function AppWww() { 

		return $this->AppWww = TWeb_URL . "/" . basename( TPath_Index ) . "/" . $this->AppName(); 

	}

	public function AppUrl() { return $this->AppWww(); }

	public function AppName() {

		if ( $this->AppName === null ) {
			
			$mName = trim( get_class( $this )  , " .\\/") ;

			$mName = explode( "\\" , $mName )[ 0 ] ;
			
			if ( stristr( $mName , "\\" ) === false ) {

				$AName = substr( $mName , -3 ) ;
			
				$mName = ( strtoupper( $AName ) == "APP" ) ? substr( $mName , 0 , -3 ) : $mName ;

			} $this->AppName = trim( $mName , " .\\/");
			
		} return $this->AppName ;
	
	}

	/// Special Intel Stuff
	public function AppState(){ return is_bool( $this->AppState ) ? $this->AppState : false ; }

	public function AppHistory(){ return is_array( $this->AppHistory ) ? $this->AppHistory : array() ; }

	public function AppComp() { 

		return $this->AppComp = isset( $this->AppHistory[1] ) ? $this->AppHistory[1] : $this->AppComp ; 

	}

	public function AppFunc() { 

		return $this->AppFunc = isset( $this->AppHistory[2] ) ? $this->AppHistory[2] : $this->AppFunc ; 

	}

	public function AppRoute(){ return $this->AppRoute ; }

	public function AppError() { return $this->AppError; }

	public function AppInterface( $uiName = null , $applayUiChange = true ) { 
		return $this->AppUiFinderMethond( $uiName , $applayUiChange ); }

	/***************************************
	/// OverWritable Application Behavior Controllers */

	// OverWriteable Tool Router
	protected function Router( $Route = array() ){

		$History = ( is_array( $Route ) && ! empty( $Route ) ) ? 

			array_values( $Route ) : $this->AppHistory();

		if ( strtolower( $History[ 0 ] ) !== strtolower( $this->AppName ) )

			array_unshift( $History , $this->AppName ) ;
			
		$OHistory = $this->AppHistory() ;
			
		$NHistory = array( ) ;
		
		foreach ( $History as $nu => $nRoute ) {
		
			if ( $nRoute === null ) 

				$NHistory[ $nu ] = ( isset( $OHistory[ $nu ] ) ) ? $OHistory[ $nu ] : null ;
		
			else $NHistory[ $nu ] = $nRoute ;
		
		} foreach ( $NHistory as $k => $v ) if ( is_null( $v ) ) unset( $NHistory[ $k ] ) ;
			
		$NHistory = array_values( $NHistory ) ;

		if ( count( $NHistory ) <= 1 ) 

			return $this->AppRoute ;
		
		$this->AppHistory = $NHistory ;

		$this->AppComp = $NHistory[ 1 ] ;
			
		$this->AppFunc = ( isset( $NHistory[ 2 ] ) ) ? $NHistory[ 2 ] : null ;

		array_shift( $NHistory ) ;

		$Route = null ;

		$Comp = $this->AppComp() ;
		
		$COMPDir = [ "Components" , "Comp" , "Comps" , "Com" , "Coms" ] ;
		
		if ( strlen( ( string ) $this->CompDir ) > 0 ) 

			array_unshift( $COMPDir , $this->CompDir ) ;
		    
		$COMPDir = FindDirectory( $this->AppRoot() , $COMPDir ) ;
		
		$COMPDir = $COMPDir ? $COMPDir : $this->AppRoot() ;
		
		if ( count( $NHistory ) == 0 ){

			$Route = null ;

			return false;

		} else if ( count( $NHistory ) == 1 ){
			
			$CoFile = FindFile( $COMPDir , [ "com_{$Comp}.php" , "{$Comp}.php" ] );

			$CoFile = $CoFile ? 

				$CoFile : FindFile( $this->AppRoot() , [ "com_{$Comp}.php" , "{$Comp}.php" ] );

			if ( $CoFile ) return $CoFile ;

		} $CoDire = FindDirectory( $COMPDir , [ "com_{$Comp}" , "{$Comp}" ] ) ;

		$CoDire = ( $CoDire ) ? $CoDire : $COMPDir ;

		$Route = FindFilePath( $CoDire , $NHistory , "php" );

		$Route = ( $Route ) ? 

			$Route : FindFile( $COMPDir , [ "com_{$Comp}.php" , "{$Comp}.php" ] );
		
		return $Route ;

	}

	// OverWriteable Tool UserInterface Finder
	protected function UIFinder( $uiType = null ){

		// Find Ui Type
		if ( ! $uiType ) {

			if ( ! isset( $this->AppCurrentUI ) ) {

				$uiType = Intel::GetVar( 'ui' , null , 'USER' , false ) ;

				if ( Intel::En() === Intel::En_cli() )

					$uiType = Intel::GetVar( 'ui' , "cli" , 'USER' , true ) ;

				else $uiType = Intel::GetVar( 'ui' , "site" , 'USER' , true ) ;

			} else $uiType = $this->AppCurrentUI ;
			
		} $uiType = ucfirst( trim( $uiType , " /\\?" ) ) ;

		if ( ! isset( $this->AppInterfaces[ $uiType ] ) ) {

			/////////// return cli/site ui if no ui exists

			$newUiName = ( isCLI() ) ? "Cli" : "Site" ;

			$AppInterface = "{$newUiName}_Interface" ;

			Import( "Base.Interfaces.{$AppInterface}" );

			if ( ! isset( $this->AppInterfaces[ $newUiName ] ) ) {

				$this->AppInterfaces[ $newUiName ] = new $AppInterface( $this->AppName() );

			} else $this->AppInterfaces[ $uiType ] = $newUiName ;

		} return ucfirst( $uiType ) ;

	}

	// OverWriteable Tool PHP File Includer
	protected function Import( $search = null , $type = null , $fake = null ) {
		
		return Import( $search , $this->AppRoot() , $type );
	
	}

	// OverWriteable Tool Plugin Loader
	protected function LoadPlugin( $name = null ){

		if ( ! $name ) return null ;
			
		$name = trim( $name , ' \\/.' );
	
		$storage = strtoupper( $name );
			
		if ( $this->PrivateInstancePlugin( $name , $this->PlgDire ) ) {

			return $this->classObjects[$storage] ;

		} else if ( $this->PlgDire != TPath_BasePlugins ){

			$load = $this->PrivateInstancePlugin( $name , TPath_BasePlugins ) ;

			if ( $load ) return $this->classObjects[$storage] ;

		} return null ;

	}

	private function PrivateInstancePlugin( $name , $plgRoot ){
			
		$name = trim( $name , ' \\/.' );

		$original = $name;
	
		$storage = strtoupper( $name );
	
		$name = strtolower( $name );
	
		$exp = explode( '.' , $name );
		
		if ( isset( $this->classObjects[$storage] ) ) return true ;
	
		else if ( count( $exp ) > 1 ) {
				
			$plgStore = "";
			
			foreach( $exp as $newPlugin ) {

				$plgInfo = $this->PrivateImpoertPlugin( $plgRoot , $newPlugin , $plgStore );

				if ( $plgInfo ){

					$plgRoot = $plgInfo[ 0 ] ;

					$plgStore = $plgInfo[ 1 ] ;

				} else break ;
				
			} if ( strlen( $plgStore ) >= 1 ) 

				return $this->PrivateInstancePlugin( $plgStore , $plgRoot );
				
			else return false;
	
		} else {

			if ( $this->PrivateImpoertPlugin( $plgRoot , $name , "" ) ) return true ;
					
			else if ( stristr( $name , '_' ) !== false ) 

				return $this->PrivateInstancePlugin( str_ireplace( '_' , '.' , $name ) , $plgRoot );
				
			$Classes = array();
				
			$search = $original;
				
			while( true ) {
	
				$newMatch = array();
	
				$haveMatched = preg_match( '#.*([A-Z].*)#' , $search , $newMatch );
				
				if ( $haveMatched == 0 ) break;
	
				$Classes[] = $newMatch[1];
	
				$search = str_ireplace( $newMatch[1] , '' , $search );
					
			} $Classes[] = $search;
				
			$Classes = array_reverse( $Classes );
		
			if ( count( $Classes ) > 1 ) 

				return $this->PrivateInstancePlugin( strtolower( implode( '.' , $Classes ) ) , $plgRoot );
	
		} return false ;
	
	}

	private function PrivateImpoertPlugin( $plgRoot = null , $plgName = null , $plgStore = "" ){

		$PlgFilePath = FindFilePath( $plgRoot , $plgName );

		if ( $PlgFilePath ) {

			include_once $PlgFilePath ;

			$Root = dirname( $PlgFilePath ) ;

			$newClass = FindClass( "{$plgName}Plugin" , "{$plgName}_Plugin" , "{$plgName}Plg" , "{$plgName}_Plg" , $plgName );

			if ( $newClass ) {
					
				$newObj = new $newClass( );
					
				$newObj->Name( $plgName );
					
				$newObj->Root( $Root );
					
				$newObj->App( $this->AppName() );
					
				$plgStore .= "{$plgName}";

				$plgStore = strtoupper( trim( $plgStore , ' ./\\' ) );
			
				$this->classObjects[$plgStore] = $newObj;

				return [ $Root , $plgStore ] ;

			}

		} 

	}

	// OverWriteable Tool For Object Loading
	public function LoadObject( ){

		/// $root , $name , $class , $args  

		$input = func_get_args( ) ;

		$root = null; /// Searching Directory

		$name = null; /// Object File Name

		$class = null;  /// Object Class Name

		$args = null; /// Arguments For Class Constructor

		/////////////////// Find The Args Now =)

		$First = isset( $input[ 0 ] ) ? $input[ 0 ] : null ;

		if ( is_string( $First ) ) unset( $input[ 0 ] ) ;

		$Second = isset( $input[ 1 ] ) ? $input[ 1 ] : null ;

		if ( is_string( $Second ) ) unset( $input[ 1 ] ) ;

		$Third = isset( $input[ 2 ] ) ? $input[ 2 ] : null ;

		if ( $Third ) unset( $input[ 2 ] ) ;

		/////////////////// Check The Args Now =)

		if ( is_dir( $First ) ) {

			$root = $First ;

			$Second && $name = $Second ; // Found The Object File Name

			is_string( $Third ) && $class = $Third ; // Found The Object Class Name

			$args = array_values( $input ) ;

		} else {

			$root = $this->AppRoot();

			$First && $name = $First ; // Found The Object File Name

			is_string( $Second ) && $class = $Second ; // Found The Object Class Name

			$args = array_values( $input ) ;

		}

		if ( ! $name ) return false ; // Get Out In Object File Name Not Founded Yet :|

		$name = ( strtolower( substr( $name , -4 ) ) == ".php" ) ? substr( $name , 0 , -4 ) : $name ;

		$class = ( $class ) ? $class : $name ;

		$FirstArg = ( isset( $args[ 0 ] ) ) ? $args[ 0 ] : null ;
		
		$args = ( is_array( $FirstArg ) && count( $args ) == 1 ) ? $FirstArg : $args ;

		/////////////////// Find The Class Object

		$ObjectClassFile = FindFile( $root , "{$name}.php" );

		if ( $ObjectClassFile ) {

			include_once $ObjectClassFile ;

			if ( class_exists( $class ) ) return $this->{$name} = new $class();

		} $ObjectClassFolder = FindDirectory( $root , $name );

		if ( $ObjectClassFolder ){

			$ObjectClassFile = FindFile( $root , "{$name}.php" , "index.php" , "class.php" , "object.php" );

			if ( $ObjectClassFile ) {

				include_once $ObjectClassFile ;

				if ( class_exists( $class ) ) return $this->{$name} = new $class();

			}

		} return false;

	}

	// OverWriteable Tool For Translating
	public function Translate( $Word = null ){
        
		return $this->translator->{$Word} ;

	}

	/***************************************
	/// The Instance Holder Part */

	private static $Instances = array ();

	public static function getInstance( $instance = null ){

		$LowIns = strtolower( $instance ) ;

		if ( isset( self::$Instances[ $LowIns ] ) ) return self::$Instances[ $LowIns ] ;

		return null ;

	}

	public static function killInstance( $instance = null ){

		$LowIns = strtolower( $instance ) ;

		if ( isset( self::$Instances[ $LowIns ] ) ){

			if ( method_exists( self::$Instances[ $LowIns ] , "Finish" ) )

				self::$Instances[ $LowIns ]->Finish( );

			else if ( method_exists( self::$Instances[ $LowIns ] , "Kill" ) )

				self::$Instances[ $LowIns ]->Kill( );

			unset( self::$Instances[ $LowIns ] ) ;

		}

		return true ;

	}

	public static function getAllInstances(){

		return array_keys( self::$Instances ) ;

	}
	
}

?>
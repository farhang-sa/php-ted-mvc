<?php

defined( 'TExec' ) or die( 'Access Denied' );

class Feed_Interface extends Ted\TedInterface {

	protected $App = null;

	protected $Root = null;

	protected $Www = null;
			
	protected $htmlDirList = [ 'pages' , 'views' , "layouts" , "htmls" ] ;

	protected $cssDirList = [ 'css' , 'styles' , 'stylesheets' ];

	protected $jsDirList = [ 'js' , 'scripts' , 'javascripts' ];

	protected $fileDireList = [ 'files' , 'data' ];

	protected $deviceDireList = [ 'device' , 'devices' ];

	protected $browserDireList = [ 'browser' , 'browsers' ];

	protected $pageDirectory = null;

	protected $fileDirectory = null;

	protected static $init = false;

	protected static $device = null; 	// / User's Device

	protected static $platform = null; 	// / User's platform

	protected static $browser = null; 	// / User's Browser

	public static $head = array();

	public static $Cont = "";

	public static $body = array();
	
	public function __construct( $App = null , $UIRoot = null ) {
		
		$this->App( $App ) ;
		
		$this->Root( $UIRoot );
		
		if ( ! self::$init ) $this->init( );
	
	}
	
	public function App( $App = null ) {

		if ( is_object( $App ) && $App instanceof TED_Application ){
			
			$this->App = $App;
			
			$this->Www = $App->AppWww() ;
			
			$searchList = [ "www" , "html" , "web" , "site" , "public_html" , "public_www" , "public_web" , "public_site" ] ;
			
			$this->Root( TED_FindMeDirectory( $this->App->AppRoot() , $searchList ) );
			
		}
		
		return $this->App;
	
	}

	public function Root( $Root = null ) {

		if ( $this->Root === null && is_dir( $Root ) ){
			
			$this->Root = $Root;
			
		}
		
		return $this->Root;
	
	}
	
	public function init( ) {
		
		/**
		 * ************************************
		 * @ Detect ( Device / Platform / Browser ) Types
		 */
		 
		$AGENT = TED_Interface::getVar( 'HTTP_USER_AGENT' , $_SERVER['HTTP_USER_AGENT'] , 'SERVER' , true );
		
		$d = TED_Interface::getVar( 'user_device' , null , null , false );
		
		$p = TED_Interface::getVar( 'user_platform' , null , null , false );
		
		$b = TED_Interface::getVar( 'user_browser' , null , null , false );
		
		if ( $d ) self::$device = $d;
		
		if ( $p ) self::$platform = $p;
		
		if ( $b ) self::$browser = $b;
		
		if ( $AGENT ) {
			
			if ( stristr( $AGENT , "windows" ) ) {
				
				$d = "windows";
				
				$p = "WIN";
			
			} if ( stristr( $AGENT , "linux" ) ) {
				
				$d = "linux";
				
				$p = "LIN";
			
			} if ( stristr( $AGENT , "mac" ) || stristr( $AGENT , "apple" ) ) {
				
				$d = "apple";
				
				$p = "MAC";
			
			} if ( stristr( $AGENT , "android" ) ) {
				
				$d = "anroid";
				
				$p = "LIN";
			
			} if ( stristr( $AGENT , "iwatch" ) ) {
				
				$d = "iwatch";
				
				$p = "IOS";
			
			} if ( stristr( $AGENT , "iphone" ) ) {
				
				$d = "iphone";
				
				$p = "IOS";
			
			} if ( stristr( $AGENT , "ipad" ) ) {
				
				$d = "ipad";
				
				$p = "IOS";
			
			} if ( stristr( $AGENT , "ipod" ) ) {
				
				$d = "ipod";
				
				$p = "IOS";
			
			} if ( stristr( $AGENT , "blackberry" ) ) {
				
				$d = "blackberry";
				
				$p = "BBY";
			
			} if ( $d == null ) {
				
				$d = "unknown";
			
			} if ( $p == null ) {
				
				$p = "unknown";
			
			}
			
			// // Browser Detection ...
			
			if ( stristr( $AGENT , "webkit" ) ) {
				
				$b = "webkit";
			
			} if ( stristr( $AGENT , "MSIE" ) ) {
				
				$b = "ie";
			
			} if ( stristr( $AGENT , "Firefox" ) ) {
				
				$b = "firefox";
			
			} if ( stristr( $AGENT , "Opera" ) ) {
				
				$b = "opera";
			
			} if ( stristr( $AGENT , "safari" ) ) {
				
				$b = "safari";
			
			} if ( stristr( $AGENT , "chrome" ) ) {
				
				$b = "chrome";
			
			} if ( $b == null ) {
				
				$b = "unknown";
			
			}
		
		}
		
		( self::$platform === null ) and self::$platform = $p;
		
		( self::$browser === null ) and self::$browser = $b;
		
		( self::$device === null ) and self::$device = $d;
				
		/**
		 * ************************************
		 * @ Find ( File / Pages ) Directorys
		 */
		
		$this->fileDirectory = TED_FindMeDirectory( $this->Root , $this->fileDireList );
		
		$this->pageDirectory = TED_FindMeDirectory( $this->Root , $this->htmlDirList );		
	
		/**
		 * ************************************
		 * @ Set Html Output Defaults
		 */
		
		self::$head['tag'] = array ();
		
		self::$head['met'] = array ();
		
		self::$head['str'] = array ();
		
		self::$head['jsf'] = array ();
		
		self::$head['css'] = array ();
		
		self::$body['tag'] = array ();
		
		self::$body['str'] = array ();
		
		self::$body['jsf'] = array ();
		
		self::$init = true ;
		
		return $this;
	
	}

	public function Www(){
		
		return $this->Www ;
		
	}

	public function WebURL(){
	
		return $this->Www ;
	
	}

	public function URL(){
	
		return $this->Www ;
	
	}
	
	public function tag2Head( $string = null ) {

		$string = ( string ) $string;
		
		$string = trim( $string );
		
		if ( $string[0] != "<" ) $string = "<" . $string;
		
		if ( $string[strlen( $string ) - 1] != ">" ) $string = ">";
		
		if ( strlen( $string ) >= 1 ) self::$head['tag'][] = $string;
		
		return $this;
	
	}

	public function head( $string = null ) {

		$string = ( string ) $string;
		
		$string = trim( $string );
		
		self::$head['str'][] = $string;
		
		return $this;
	
	}

	public function title( $title = null ) {

		if ( ! isset( self::$head['tag']['title'] ) ) self::$head['tag']['title'] = null;
		
		if ( $title ) self::$head['tag']['title'] .= "<title>{$title}</title>";
		
		return $this;
	
	}

	public function charset( $char = 'UTF-8' ) {

		self::$head['tag']['charset'] = "<meta charset='{$char}' />";
		
		$this->meta( 'charset' , $char );
		
		return $this;
	
	}

	public function keyword( $keys = null ) {

		$this->meta( 'keywords' , $keys );
		
		return $this;
	
	}

	public function description( $desc = null ) {

		$this->meta( 'description' , $desc );
		
		return $this;
	
	}

	public function generator( $generator ) {

		$this->meta( 'generator' , $generator );
		
		return $this;
	
	}

	public function meta( $name = null , $content = null ) {

		if ( $name && $content ) {
			
			$name = strtolower( $name );
			
			self::$head['met'][$name] = $content;
		
		}
		
		return $this;
	
	}

	public function tag2Body( $string = null ) {

		$string = ( string ) $string;
		
		$string = trim( $string );
		
		if ( strlen( $string ) >= 7 ) self::$body['tag'][] = $string ;
	
	}

	public function body( $string = null ) {

		$string = ( string ) $string;
		
		$string = trim( $string );
		
		if ( strlen( $string ) >= 1 ) self::$body['str'][] = $string ;
		
		return $this ;
	
	}

	public function js2head( $name ) {
		
		$save = basename( $name ) ;
		
		if ( isset( self::$head['jsf'][ $save ] ) ) return $this ;
		
		$path = trim( str_ireplace( $save , "" , $name ) ) ;
		
		$searchDirectoriesList = $this->jsDirList ;
		
		if ( strlen( $path ) > 0 ){
			
			$path = trim( $path , "/\\" ) ;
			
			foreach ( $searchDirectoriesList as $k => $v ) $searchDirectoriesList[ $k ] = $v . TED_DS . $path ;
			
		}
		
		$newJs = $this->media( $searchDirectoriesList , $save ) ;
		
		if ( $newJs !== null ) self::$head['jsf'][ $save ] = $newJs ;
			
		return $this ; 

	}

	public function js2body( $name ) {
		
		$save = basename( $name ) ;
		
		if ( isset( self::$body['jsf'][ $save ] ) ) return $this ;
		
		$path = trim( str_ireplace( $save , "" , $name ) ) ;
		
		$searchDirectoriesList = $this->jsDirList ;
		
		if ( strlen( $path ) > 0 ){
			
			$path = trim( $path , "/\\" ) ;
			
			foreach ( $searchDirectoriesList as $k => $v ) $searchDirectoriesList[ $k ] = $v . TED_DS . $path ;
			
		}
		
		$newJs = $this->media( $searchDirectoriesList , $save ) ;
		
		if ( $newJs !== null ) self::$body['jsf'][ $save ] = $newJs ;
			
		return $this; 
		
	}

	public function js( $name , $toHead = true ) {

		if ( $toHead ) return $this->js2head( $name );
		
		else return $this->js2body( $name );
	
	}

	public function ExternalJs( $address , $toHead = true ) {
		
		$save = basename( $address ) ;

		if ( $toHead ) {
		
		if ( isset( self::$head['jsf'][ $save ] ) ) return $this ;
			
			self::$head['jsf'][ $save ] = $address;
			
			return $this;
		
		}
		
		if ( isset( self::$body['jsf'][ $save ] ) ) return $this ;
		
		self::$body['jsf'][ $save ] = $address;
		
		return $this;
	
	}

	public function Css( $name ) {
		
		$save = basename( $name ) ;
		
		if ( isset( self::$head['css'][ $save ] ) ) return $this ;
		
		$path = trim( str_ireplace( $save , "" , $name ) ) ;
		
		$searchDirectoriesList = $this->cssDirList ;
		
		if ( strlen( $path ) > 0 ){
			
			$path = trim( $path , "/\\" ) ;
			
			foreach ( $searchDirectoriesList as $k => $v ) $searchDirectoriesList[ $k ] = $v . TED_DS . $path ;
			
		}
		
		$newCss = $this->media( $searchDirectoriesList , $save ) ;
		
		if ( $newCss !== null ) self::$head['css'][ $save ] = $newCss ;
			
		return $this; 
		
	}

	public function ExternalCss( $address ) {
		
		$save = basename( $address ) ;
		
		if ( isset( self::$head['css'][ $save ] ) ) return $this ;

		self::$head['css'][ $save ] = $address;
		
		return $this;
	
	}
	
	public function setContents( $Contents = null ){
		
		self::$Cont = $Contents ;
		
	}
	
	public function RenderHead( ) {

		$cCom = $this->App->AppComp();
		
		if ( $cCom ) {
			
			$this->Css( "{$cCom}.css" );
			
			$this->js2head( "{$cCom}.head.js" );
		
		}
		
		$returns = PHP_EOL . PHP_EOL;
		
		$tags = ( is_array( self::$head['tag'] ) ) ? self::$head['tag'] : array ();
		
		$mets = ( is_array( self::$head['met'] ) ) ? self::$head['met'] : array ();
		
		$csse = ( is_array( self::$head['css'] ) ) ? self::$head['css'] : array ();
		
		$jses = ( is_array( self::$head['jsf'] ) ) ? self::$head['jsf'] : array ();
		
		$strs = ( is_array( self::$head['str'] ) ) ? self::$head['str'] : array ();
		
		foreach( $tags as $type => $tag ) if ( $tag ) $returns .= $tag . PHP_EOL;
		
		foreach( $mets as $name => $con ) if ( $name && $con ) $returns .= "<meta name='{$name}' content='{$con}' />" . PHP_EOL;
		
		foreach( $csse as $numb => $css ) if ( $css ) $returns .= "<link rel='stylesheet' type='text/css' href='{$css}' />" . PHP_EOL;
		
		foreach( $jses as $numb => $jsf ) if ( $jsf ) $returns .= "<script src='{$jsf}'></script>" . PHP_EOL;
		
		foreach( $strs as $type => $str ) if ( $str ) $returns .= $str . PHP_EOL;
		
		return $returns;
	
	}
	
	public function RenderComponent(){
		
		return self::$Cont;
		
	}
	
	public function RenderBody( ) {

		$cCom = $this->App->AppComp();
		
		if ( $cCom ) {
			
			$this->js2body( "{$cCom}.js" );
			
			$this->js2body( "{$cCom}.body.js" );
		
		}
		
		$returns = PHP_EOL . PHP_EOL;
		
		$tags = ( is_array( self::$body['tag'] ) ) ? self::$body['tag'] : array ();
		
		$jses = ( is_array( self::$body['jsf'] ) ) ? self::$body['jsf'] : array ();
		
		$strs = ( is_array( self::$body['str'] ) ) ? self::$body['str'] : array ();
		
		foreach( $tags as $type => $tag ) if ( $tag ) $returns .= $tag . PHP_EOL;
		
		foreach( $jses as $numb => $jsf ) if ( $jsf ) $returns .= "<script src='{$jsf}'></script>" . PHP_EOL;
		
		foreach( $strs as $type => $str ) if ( $str ) $returns .= $str . PHP_EOL;
		
		return $returns;
	
	}

	public function webMedia( $direc = null , $name = null , $root = null ) {
		
		return $this->media( $direc , $name , $root );
		
	}
	
	public function media( $direc = null , $name = null , $root = null ) {

		$OriginalFile 	= null;
		
		$MainBackUp 	= null;
		
		$BrowserBackup 	= null;
		
		$DeviceBackup 	= null;
		
		$newRoot = ( is_dir( $root ) ) ? $root : $this->Root ;
		
		$newerRoot = ( $newRoot == $this->Root ) ? $this->fileDirectory : null ;
		
		if ( is_dir( $rootDire = TED_FindMeDirectory( $newRoot , $direc ) ) ) goto FindFile ;
		
		if ( is_dir( $rootDire = TED_FindMeDirectory( $newRoot , $this->deviceDireList ) ) ) goto FindFile ;
		
		if ( is_dir( $rootDire = TED_FindMeDirectory( $newRoot , $this->browserDireList ) ) ) goto FindFile ;
		
		if ( is_dir( $rootDire = TED_FindMeDirectory( $newerRoot , $direc ) ) ) goto FindFile ;
		
		if ( is_dir( $rootDire = TED_FindMeDirectory( $newerRoot , $this->deviceDireList ) ) ) goto FindFile ;
		
		if ( is_dir( $rootDire = TED_FindMeDirectory( $newerRoot , $this->browserDireList ) ) ) goto FindFile ;
		
		FindFile : {
			
			$file = $this->FindNewFile( $rootDire , $name );
			
			if ( is_file( $file ) ) {
				
				$file = self::getWebPath( $file );
				
				if ( stristr( $file , self::$browser ) && stristr( $file , self::$device ) ) $OriginalFile = $file;
				
				else if ( stristr( $file , self::$browser ) ) $BrowserBackup = $file;
				
				else if ( stristr( $file , self::$device ) ) $DeviceBackup = $file;
				
				else $MainBackUp = $file;
			
			}
			
			if ( $OriginalFile !== null ) return $OriginalFile;

		}
		
		if ( $BrowserBackup !== null ) return trim( $BrowserBackup ) ;
		
		else if ( $DeviceBackup !== null ) return trim( $DeviceBackup ) ;
		
		else if ( $MainBackUp !== null ) return trim( $MainBackUp ) ;
		
		return null ;
		
	}

	public function FindNewFile( $root , $name ) {

		return self::FindFile( $root , $name );
	
	}

	public static function FindFile( $root , $name ) {

		$original = TED_FindMeFile( $root , $name );
		
		$browser = TED_FindMeFile( $root , self::$browser . "." . $name );
		
		$device = TED_FindMeFile( $root , self::$device . "." . $name );
		
		$browInDev = TED_FindMeFile( $root , self::$browser . "." . self::$device . "." . $name );
		
		$devInBrow = TED_FindMeFile( $root , self::$device . "." . self::$browser . "." . $name );
		
		if ( is_file( $browInDev ) ) return $browInDev;
		
		else if ( is_file( $devInBrow ) ) return $devInBrow;
		
		else if ( is_dir( $newBrowser = $root . TED_DS . self::$browser ) ) {
			
			$browserFind = self::FindFile( $newBrowser , $name );
			
			if ( is_file( $browserFind ) ) return $browserFind;
		
		} if ( is_dir( $newDevice = $root . TED_DS . self::$device ) ) {
			
			$deviceFind = self::FindFile( $newDevice , $name );
			
			if ( is_file( $deviceFind ) ) return $deviceFind;
		
		} else if ( is_file( $browser ) ) return $browser;
		
		else if ( is_file( $device ) ) return $device;
		
		return $original;
	
	}
	
	public function getWebPath( $address ) {

		$appFiles = str_ireplace( TED_Root , '' , $address );
		
		$appFiles = str_ireplace( TED_DS , '/' , $appFiles );
		
		$appFiles = ( $appFiles[0] == "/" ) ? substr( $appFiles , 1 , strlen( $appFiles ) ) : $appFiles;
		
		$appFiles = TED_URL . '/' . $appFiles;
		
		return $appFiles;
	
	}

	// //////////// Extra Helpful Tags !
	
	public function image( $dire , $name , $extraPut = null ) {

		$add = self::media( $dire , $name );
		
		if ( $add ) {
			
			$p = "<img src='{$add}'";
			
			$p = ( $extraPut ) ? "{$p} {$extraPut}" : $p;
			
			$p .= " >";
			
			return $p;
		
		}
	
	}
	
	public function Response( $VIEW = null , $CODE = null , $MESSAGES = null , $DATA = null , $DUMP = null , $NEED = null , $EXTRA = null , $RESULT = null ){
	
		$HtmlRoot = $this->pageDirectory ; 
		
		$mainDire = null ;
		
		$mainFile = null ;
		
		$backDire = null ;
		
		$backFile = null ;
		
		$PHPRoute = $this->App->AppHistory( );
		
		unset( $PHPRoute[ 0 ] ) ;
		
		$PHPRoute = array_values( $PHPRoute ) ;
		
		if ( ! empty( $VIEW ) && ! is_null( $VIEW ) ) {
			
			if ( is_array( $VIEW ) ) {
				
				foreach ( $VIEW as $newView ) if ( !empty( $newView ) && is_string( $newView ) ) array_push( $PHPRoute , $newView ) ;
				
			} else if ( is_file( $VIEW ) ) {
			
				$mainFile = $VIEW ;
			
			} else {
				
				$VIEW = trim( $VIEW ) ;
				
				if ( stristr( $VIEW, '/' ) ) {
					
					$explo = explode( '/' ,  $VIEW ) ;
				
					foreach ( $explo as $newView ) if ( ! empty( $newView ) && is_string( $newView ) ) array_push( $PHPRoute , $newView ) ;
					
				} else array_push( $PHPRoute , $VIEW ) ;
				
			}
			
		}
		
		if ( $HtmlRoot ) {
			
			$mainDire = $HtmlRoot ;
			
			if ( ! is_file( $mainFile ) ) foreach ( $PHPRoute as $newView ) {
				
				$newBackDire = TED_FindMeDirectory( $backDire , $newView )  ;
					
				$newMainDire = TED_FindMeDirectory( $mainDire , $newView )  ;
				
				if ( $newMainDire ) {
					
					$backDire = ( $newBackDire ) ? $newBackDire : $mainDire ;
						
					$mainDire = $newMainDire ;
					
				} else if ( $newBackDire ){
					
					$backDire = $mainDire ;
					
					$mainDire = $newBackDire ;
					
				}

				$newBackMedia = $this->FindNewFile( $backDire , "{$newView}.html" );

				$newMainMedia = $this->FindNewFile( $mainDire , "{$newView}.html" );
				
				if ( $newMainMedia ){
					
					$backFile = ( $newBackMedia ) ? $newBackMedia : $mainFile ;
					
					$mainFile = $newMainMedia ;
					
				} else if ( $newBackMedia ){
					
					$backFile = $mainFile ;
						
					$mainFile = $newBackMedia ;
					
				}
				
			}
			
			$EXECUTE = ( $mainFile ) ? $mainFile : $backFile ;
			
			$EXECUTE = ( $EXECUTE ) ? $EXECUTE : null ;
			
			if ( $EXECUTE ){
						
				$html = $Html = $HTML = $ui = $UI = $Ui = $gui = $GUI = $Gui = $doc = $Doc = $DOC = $this ;
								
				$App = $Application = $app = $application = $this->App();
				
				$Result = $Results = $Res = $RESULT ;
						
				$view = $View = $VIEW;
						
				$data = $Data = $DATA;
						
				$dump = $Dump = $DUMP;
						
				$need = $Need = $NEED;
						
				$code = $Code = $CODE;
						
				$message = $Message = $messages = $Messages = $error = $Error = $ERROR = $MESSAGES;
						
				return include $EXECUTE;

			}
				
		}
		
		print "<h3>HTML Page Not Exists</h3>";
				
		return false;
	
	}

}

?>
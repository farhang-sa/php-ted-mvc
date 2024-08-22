<?php

defined( 'TExec' ) or die( 'Access Denied' );

class Gtk_Interface extends Ted\TedInterface {

	private $Root = null;

	private $Www = null;
			
	private $htmlDirList = [ 'pages' , 'views' , "layouts" , "htmls" ] ;

	private $cssDirList = [ 'css' , 'styles' , 'stylesheets' ];

	private $jsDirList = [ 'js' , 'scripts' , 'javascripts' ];

	private $fileDireList = [ 'files' , 'data' ];

	private $deviceDireList = [ 'device' , 'devices' ];

	private $browserDireList = [ 'browser' , 'browsers' ];

	private $pageDirectory = null;

	private $fileDirectory = null;

	private static $inited = false;

	private static $head;

	private static $body;

	public final function App( $App = null ) {

		if ( is_object( $App ) && $App instanceof TED_Application ){
			
			$this->App = $App;
			
			$this->Root( $App->AppWuiRoot() );
			
		}
		
		return $this->App;
	
	}

	public final function Root( $Root = null ) {

		if ( is_dir( $Root ) ){
			
			$this->Root = $Root;
			
			$this->Www = self::getWebPath( $Root ) ;
			
		}
		
		return $this->Root;
	
	}

	public final function wui(){
	
		return $this->Www ;
	
	}
	
	public final function Www(){
		
		return $this->Www ;
		
	}

	public final function WebURL(){
	
		return $this->Www ;
	
	}

	public final function URL(){
	
		return $this->Www ;
	
	}
	
	public final function Response( $VIEW = null , $CODE = null , $MESSAGES = null , $DATA = null , $DUMP = null , $NEED = null ){
	
		$HtmlRoot = $this->pageDirectory ; 
		
		$PHPRoute = $this->App->AppHistory( );
		
		unset( $PHPRoute[ 0 ] ) ;
		
		$PHPRoute = array_values( $PHPRoute ) ;
		
		if ( ! empty( $VIEW ) && ! is_null( $VIEW ) ) {
			
			if ( is_array( $VIEW ) ) {
				
				foreach ( $VIEW as $newView ) if ( !empty( $newView ) && is_string( $newView ) ) array_push( $PHPRoute , $newView ) ;
				
			} else {
				
				$VIEW = trim( $VIEW ) ;
				
				if ( stristr( $VIEW, '/' ) ) {
					
					$explo = explode( '/' ,  $VIEW ) ;
				
					foreach ( $explo as $newView ) if ( ! empty( $newView ) && is_string( $newView ) ) array_push( $PHPRoute , $newView ) ;
					
				} else array_push( $PHPRoute , $VIEW ) ;
				
			}
			
		}
		
		$mainDire = null ;
		
		$mainFile = null ;
		
		$backDire = null ;
		
		$backFile = null ;
		
		if ( $HtmlRoot ) {
			
			$mainDire = $HtmlRoot ;

			foreach ( $PHPRoute as $newView ) {
				
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
						
				$view = $View = $VIEW;
						
				$data = $Data = $DATA;
						
				$dump = $Dump = $DUMP;
						
				$need = $Need = $NEED;
						
				$code = $Code = $CODE;
						
				$message = $Message = $messages = $Messages = $error = $Error = $ERROR = $MESSAGES;
						
				return include $EXECUTE;

			}
				
		}
		
		print "<h3>{$VIEW}:{$MESSAGES}</h3>";
				
		return false;
	
	}
	
	private final function init( ) {

		self::$inited = true;
		
		$this->fileDirectory = TED_FindMeDirectory( $this->Root , $this->fileDireList );
		
		$this->pageDirectory = TED_FindMeDirectory( $this->Root , $this->htmlDirList );
		
		self::$head['tag'] = array ();
		
		self::$head['met'] = array ();
		
		self::$head['str'] = array ();
		
		self::$head['jsf'] = array ();
		
		self::$head['css'] = array ();
		
		self::$body['tag'] = array ();
		
		self::$body['str'] = array ();
		
		self::$body['jsf'] = array ();
		
		return $this;
	
	}

	public final function tag2Head( $string = null ) {

		$string = ( string ) $string;
		
		$string = trim( $string );
		
		if ( $string[0] != "<" ) $string = "<" . $string;
		
		if ( $string[strlen( $string ) - 1] != ">" ) $string = ">";
		
		if ( strlen( $string ) >= 1 ) self::$head['tag'][] = $string;
		
		return $this;
	
	}

	public final function head( $string = null ) {

		$string = ( string ) $string;
		
		$string = trim( $string );
		
		self::$head['str'][] = $string;
		
		return $this;
	
	}

	public final function title( $title = null ) {

		if ( ! isset( self::$head['tag']['title'] ) ) self::$head['tag']['title'] = null;
		
		if ( $title ) self::$head['tag']['title'] .= "<title>{$title}</title>";
		
		return $this;
	
	}

	public final function charset( $char = 'UTF-8' ) {

		self::$head['tag']['charset'] = "<meta charset='{$char}' />";
		
		$this->meta( 'charset' , $char );
		
		return $this;
	
	}

	public final function keyword( $keys = null ) {

		$this->meta( 'keywords' , $keys );
		
		return $this;
	
	}

	public final function description( $desc = null ) {

		$this->meta( 'description' , $desc );
		
		return $this;
	
	}

	public final function generator( $generator ) {

		$this->meta( 'generator' , $generator );
		
		return $this;
	
	}

	public final function meta( $name = null , $content = null ) {

		if ( $name && $content ) {
			
			$name = strtolower( $name );
			
			self::$head['met'][$name] = $content;
		
		}
		
		return $this;
	
	}

	public final function tag2Body( $string = null ) {

		$string = ( string ) $string;
		
		$string = trim( $string );
		
		if ( $string[0] != "<" ) $string = "<" . $string;
		
		if ( $string[strlen( $string ) - 1] != ">" ) $string = ">";
		
		if ( strlen( $string ) >= 1 ) self::$body = $string . PHP_EOL . self::$body;
	
	}

	public final function body( $string = null ) {

		$string = ( string ) $string;
		
		$string = trim( $string );
		
		self::$body = $string . PHP_EOL . self::$body;
		
		return $this ;
	
	}

	public final function js2head( $name ) {
		
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

	public final function js2body( $name ) {
		
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

	public final function js( $name , $toHead = true ) {

		if ( $toHead ) return $this->js2head( $name );
		
		else return $this->js2body( $name );
	
	}

	public final function ExternalJs( $address , $toHead = true ) {
		
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

	public final function Css( $name ) {
		
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

	public final function ExternalCss( $address ) {
		
		$save = basename( $address ) ;
		
		if ( isset( self::$head['css'][ $save ] ) ) return $this ;

		self::$head['css'][ $save ] = $address;
		
		return $this;
	
	}

	public final function ReturnHead( ) {

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

	public final function ReturnBody( ) {

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

	public final function media( $direc , $name , $root = null ) {

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
				
				if ( stristr( $file , TED_Requests::$browser ) && stristr( $file , TED_Requests::$device ) ) $OriginalFile = $file;
				
				else if ( stristr( $file , TED_Requests::$browser ) ) $BrowserBackup = $file;
				
				else if ( stristr( $file , TED_Requests::$device ) ) $DeviceBackup = $file;
				
				else $MainBackUp = $file;
			
			}
			
			if ( $OriginalFile !== null ) return $OriginalFile;

		}
		
		if ( $BrowserBackup !== null ) return trim( $BrowserBackup ) ;
		
		else if ( $DeviceBackup !== null ) return trim( $DeviceBackup ) ;
		
		else if ( $MainBackUp !== null ) return trim( $MainBackUp ) ;
		
		return null ;
		
	}

	public final function getWebPath( $address ) {

		$appFiles = str_ireplace( TED_Root , '' , $address );
		
		$appFiles = str_ireplace( TED_DS , '/' , $appFiles );
		
		$appFiles = ( $appFiles[0] == "/" ) ? substr( $appFiles , 1 , strlen( $appFiles ) ) : $appFiles;
		
		$appFiles = TED_URL . '/' . $appFiles;
		
		return $appFiles;
	
	}

	public final function FindNewFile( $root , $name ) {

		return self::FindFile( $root , $name );
	
	}

	public static final function FindFile( $root , $name ) {

		$original = TED_FindMeFile( $root , $name );
		
		$browser = TED_FindMeFile( $root , TED_Requests::$browser . "." . $name );
		
		$device = TED_FindMeFile( $root , TED_Requests::$device . "." . $name );
		
		$browInDev = TED_FindMeFile( $root , TED_Requests::$browser . "." . TED_Requests::$device . "." . $name );
		
		$devInBrow = TED_FindMeFile( $root , TED_Requests::$device . "." . TED_Requests::$browser . "." . $name );
		
		if ( is_file( $browInDev ) ) return $browInDev;
		
		else if ( is_file( $devInBrow ) ) return $devInBrow;
		
		else if ( is_dir( $newBrowser = $root . TED_DS . TED_Requests::$browser ) ) {
			
			$browserFind = self::FindFile( $newBrowser , $name );
			
			if ( is_file( $browserFind ) ) return $browserFind;
		
		} if ( is_dir( $newDevice = $root . TED_DS . TED_Requests::$device ) ) {
			
			$deviceFind = self::FindFile( $newDevice , $name );
			
			if ( is_file( $deviceFind ) ) return $deviceFind;
		
		} else if ( is_file( $browser ) ) return $browser;
		
		else if ( is_file( $device ) ) return $device;
		
		return $original;
	
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

}

?>
<?php

defined( 'TExec' ) or die( 'Access Denied' );

class Html_Interface extends Ted\TedInterface {

	protected $HtmlLoadersList = null ;

	protected $pageDirectory = null;

	protected $fileDirectory = null;

	protected static $init = false;

	protected static $Html_Attr ;

	protected static $Body_Attr ;

	protected static $Head_Attr ;

	protected static $head = array();

	protected static $body = array();

	protected static $Cont = "";

	protected static $__EXTRA__ = array();
	
	public function __construct( $App = null , $UIRoot = null ) {
		
		$this->setApp( $App , [ 'Htmls' , 'WebApp' , 'WebUi' , 'WebUI' , 'HtmlUI' , 'Web' ] ) ;
		
		$this->setRoot( $UIRoot );
		
		$this->init( );	

	}

	public function setRoot( $Root = null ){
		
		parent::setRoot( $Root );
				
		$this->fileDirectory = Ted\FindDirectory( 
			$this->Root() , [ 'files' , 'assets' , 'data' ] );

		$this->fileDirectory = ( $this->fileDirectory ) ? $this->fileDirectory : $this->Root() ;
		
		$this->pageDirectory = Ted\FindDirectory( 
			$this->Root() , [ 'pages' , 'layouts' , 'views' ] );	

		$this->pageDirectory = ( $this->pageDirectory ) ? $this->pageDirectory : $this->Root ;	
		
	}
	
	public function Respond( $directRespond = true ){ 
		
		$this->Connect( $directRespond );

		return true ;
		
	}
	
	public function ExecInitFiles( $Root = null , $doPHP = false ){

		$Root = $Root && is_dir( $Root ) ? $Root : $this->Root ;
		
		@ob_start(); // Start Output Buffrer
		@ob_clean(); // Cleaning Output Buffrer

		//find init/config file
		$CFG = null ;
		if( $doPHP ) 
			$CFG = Ted\FindFile( $Root , [ 'init.php' , 'config.php' , 'cfg.php' ] );
		else $CFG = Ted\FindFile( $Root , [ 'init.html' , 'config.html' , 'cfg.html' ] );
		
		// execute init file
		$CFG && $this->Response( $CFG , null , $this );

		@ob_end_clean(); // End Cleaning Output Buffrer
		
	}
	
	public function init() {
		
		if ( self::$init ) return true ;
	
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
	
	///* ///// Simple WWW Intel  /////// */// 

	public function Www(){ return Ted\FindWebPath($this->Root()); }
	public function WebURL(){ return $this->Www() ; }
	public function URL(){ return $this->Www() ; }
	public function AppRootWww(){ return $this->App->AppRootWww() ; }
	public function AppRootUrl(){ return $this->App->AppRootUrl() ; }
	
	///* ///// General Document Helpers /////// */// 

	public function HtmlAttr( $string = null ){ $string = ( string ) trim( $string ) ; 

		if ( strlen( $string ) >= 1 ) self::$Html_Attr = $string; return self::$Html_Attr; 

	}

	public function HeadAttr( $string = null ){ $string = ( string ) trim( $string ) ; 

		if ( strlen( $string ) >= 1 ) self::$Head_Attr = $string; return self::$Head_Attr; 

	}

	public function BodyAttr( $string = null ){ $string = ( string ) trim( $string ) ;

		if ( strlen( $string ) >= 1 ) self::$Body_Attr = $string; return self::$Body_Attr; 

	}
	
	public function tag2Head( $string = null ) {

		$string = ( string ) $string;
		
		$string = trim( $string );
		
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
		
		if ( $title ) self::$head['tag']['title'] = "<title>{$title}</title>";
		
		$title = self::$head['tag']['title'] ;
		
		if ( $title ){
			
			$title = str_ireplace( "<title>" , "" , $title );
	
			$title = str_ireplace( "</title>" , "" , $title );
			
		}
		
		return $title ;
	
	}

	public function charset( $char = 'UTF-8' ) {

		$char = strtoupper( $char ) ;

		return $this->meta( 'Charset' , "text/html; charset={$char}" );
	
	}

	public function keyword( $keys = null ) {

		$this->meta( 'keywords' , $keys );
		
		return $this;
	
	}

	public function description( $desc = null ) {

		return $this->meta( 'description' , $desc );
	
	}

	public function generator( $generator = null ) {

		return $this->meta( 'generator' , $generator );
	
	}

	public function ico( $ico ) {

		return $this->tag2Head( "<link rel=\"shortcut icon\" href=\"{$ico}\" type=\"image/x-icon\" />" ) ;

	}

	public function pTag( $tag , $classes , $styles , $extraIntel ){

		print "<{$tag} class='{$classes}' style='{$styles}' {$extraIntel}>" ;

	}

	public function pEndTag( $tag ){ print "</{$tag}>"; }
	public function pTagEnd( $tag ){ print "</{$tag}>"; }

	public function pImage( $address = '' , $classes ='' , $styles = '' ){

		print "<img src='{$address}' class='{$classes}' style='{$styles}' />" ;

	}

	public function pVideo( $address = '' , $classes ='' , $styles = '' , $controls = true ){

		print "<video src='{$address}' class='{$classes}' style='{$styles}'" ;
		print $controls ? ' controls ' : ' ' ;
		print '/>' ;

	}

	public function pAudio( $address = '' , $classes ='' , $styles = '' , $controls = true ){

		print "<audio src='{$address}' class='{$classes}' style='{$styles}'" ;
		print $controls ? ' controls ' : ' ' ;
		print '/>' ;

	}

	public function printDiv( $classes ='' , $styles = '' , $extraIntel = '' ){

		print "<div class='{$classes}' style='{$styles}' {$extraIntel}>" ;

	}
	
	public function pDiv( $classes ='' , $styles = '' , $extraIntel = '' ){

		print "<div class='{$classes}' style='{$styles}' {$extraIntel}>" ;

	}

	public function printEndDiv(){ print "</div>" ; }
	public function printDivEnd(){ print "</div>" ; }
	public function pEndDiv(){ print "</div>" ; }
	public function pDivEnd(){ print "</div>" ; }

	public function pA( $href = ''  , 
		$text = '' , $classes ='' , $styles = '' , $extra = '' ){

		print "<a href='{$href}' class='{$classes}' style='{$styles}' {$extra}>{$text}</a>" ;

	}

	public function GetDeviceIntent( $os , $scheme , $package , $activity ){
	    
	    if( ! is_null( $scheme ) )
	    
	        $scheme = strtolower( $scheme );
	    
	    $os = strtolower( $os ) ;
	    
	    if( $os === "android" )

		    return $this->GetAndroidIntent( $scheme , $package , $activity ); 
	    
	    if( $os === "windows" )

		    return $this->GetWindowsIntent( $scheme , $package , [ "activity" => $activity ] ); 
		    
		return null ;

	}

	public function GetDeviceDeepLink( $os , $scheme , $page , $details = array() ){
	    
	    if( ! is_null( $scheme ) )
	    
	        $scheme = strtolower( $scheme );
	    
	    $os = strtolower( $os ) ;
	    
	    if( $os === "android" )

		    return $this->GetAndroidDeepLink( $scheme , $page , $details ); 
	    
	    if( $os === "windows" )

		    return $this->GetWindowsDeepLink( $scheme , $page , $details ); 
		    
		return null ;

	}

	public function GetAndroidIntent( $scheme , $package , $activity ){

		/* 
		Intents Convert To DeepLink When Android Interprates Them
		intent:
		   HOST/URI-path // Optional host
		   #Intent;
		      package=[string];
		      action=[string];
		      category=[string];
		      component=[string];
		      scheme=[string];
		   end;

			IntentLink
				intent:#Intent;package=;category=;action=;end;
			Example:
        		"intent:#Intent;package={$package};action=android.intent.category.LAUNCHER;end" ;
		        "intent:#Intent;action=schemas.{$package}.{$activity};end";
		*/
		$intent = "intent:#Intent;";
		if( $scheme && ! empty( $scheme ) ) 
		    $intent .= "scheme={$scheme};";
        //else $intent .= "scheme=;";
		if( $package && ! empty( $package ) ) 
		    $intent .= "package={$package};";
		$intent .= "action=android.intent.action.VIEW;category=android.intent.category.BROWSABLE;end";
		return $intent;
		
	}

	public function GetAndroidDeepLink( $scheme , $page , $details = array() ){
		/*
			DeepLink
				scheme://host?pama_name=value&other_param_name=value
			Example:
				somescheme://page_details?detail_id=2
		*/

		$link = "{$scheme}://{$page}?";

		if( ! is_array( $details ) || empty( $details ) )

			return $link;

		foreach ($details as $key => $value) 

			$link .= "{$key}={$value}&" ;

		return trim( $link , " &" );

	}
	
	public function GetWindowsDeepLink( $scheme , $page , $details = array() ) {
		return $this->GetWindowsIntent( $scheme , $page , $details ); }

	public function GetWindowsIntent( $scheme , $page , $details = array() ){
	    
	    $link = "{$scheme}://{$page}?";

		if( ! is_array( $details ) || empty( $details ) )

			return $link;

		foreach ($details as $key => $value) 

			$link .= "{$key}={$value}&" ;

		return trim( $link , " &" );
	    
	}

	public function meta( $name = null , $content = null ) {

		if ( $name && ! isset( self::$head['met'][$name] ) ) 
			
			self::$head['met'][$name] = null;

		if ( $name && $content ) {
			
			$name = strtolower( $name );
			
			self::$head['met'][$name] = $content;
		
		} if ( $name ) return self::$head['met'][$name] ;
			
		else return null;
	
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
		
		$newJs = $this->FindJs( $name ) ;
		
		if ( $newJs !== null ) 

			self::$head['jsf'][ $save ] = $newJs ;
			
		return $this ; 

	}

	public function js2body( $name ) {
		
		$save = basename( $name ) ;
		
		$newJs = $this->FindJs( $name ) ;
		
		if ( $newJs !== null ) 

			self::$body['jsf'][ $save ] = $newJs ;
			
		return $this; 
		
	}

	public function js( $name , $toHead = true ) {

		if ( $toHead ) return $this->js2head( $name );
		
		else return $this->js2body( $name );
	
	}

	public function FindJs( $name ){

		$save = basename( $name ) ;
		
		if ( isset( self::$head['jsf'][ $save ] ) ) 

			return self::$head['jsf'][ $save ] ;
			
		else if ( isset( self::$body['jsf'][ $save ] ) ) 

			return self::$body['jsf'][ $save ] ;
		
		$path = trim( str_ireplace( $save , "" , $name ) ) ;
		
		$SDL_ = [ 'js' , 'script' , 'scripts' , 'javascript' , 'javascripts' ] ;
		
		if ( strlen( $path ) > 0 ){
			
			$path = trim( $path , "/\\" ) ;
			
			foreach ( $SDL_ as $k => $v ) 

				$SDL_[ $k ] = $v . TPath_DS . $path ;
			
		} return $this->media( $SDL_ , $save ) ;

	}
	
	public function ExternalJs( $address , $toHead = true ) {
		
		$save = basename( $address ) ;

		if ( $toHead ) {
		
		if ( isset( self::$head['jsf'][ $save ] ) ) return $this ;
			
			self::$head['jsf'][ $save ] = $address;
			
			return $this;
		
		} if ( isset( self::$body['jsf'][ $save ] ) ) return $this ;
		
		self::$body['jsf'][ $save ] = $address;
		
		return $this;
	
	}

	public function Css( $name ) {
		
		$save = basename( $name ) ;
		
		$newCss = $this->FindCss( $name );
		
		if ( $newCss !== null ) 

			self::$head['css'][ $save ] = $newCss ;
			
		return $this; 
		
	}

	public function FindCss( $name ){

		$save = basename( $name ) ;
		
		if ( isset( self::$head['css'][ $save ] ) ) 

			return self::$head['css'][ $save ] ;
		
		$path = trim( str_ireplace( $save , "" , $name ) ) ;
		
		$SDL_ = [ 'css' , 'style' , 'styles' , 'stylesheet' , 'stylesheets' ] ;
		
		if ( strlen( $path ) > 0 ){
			
			$path = trim( $path , "/\\" ) ;
			
			foreach ( $SDL_ as $k => $v ) 

				$SDL_[ $k ] = $v . TPath_DS . $path ;
			
		} return $this->media( $SDL_ , $save ) ;
		
	}

	public function ExternalCss( $address ) {
		
		$save = basename( $address ) ;
		
		if ( isset( self::$head['css'][ $save ] ) ) return $this ;

		self::$head['css'][ $save ] = $address;
		
		return $this;
	
	}

	public function webMedia( $direc = null , $name = null , $root = null ) {
		
		return $this->media( $direc , $name , $root );
		
	}
	
	public function media( $direc = null , $name = null , $root = null ) {

		$newRoot = ( $root && is_dir( $root ) ) ? $root : $this->Root() ;

		$direc = $direc == null ? "" : $direc ;
		
		if ( is_dir( $rootDire = Ted\FindDirectory( $newRoot , $direc ) ) ) goto FindFile ;
		
		$newerRoot = ( $newRoot == $this->Root() ) ? $this->fileDirectory : null ;
		
		if ( is_dir( $rootDire = Ted\FindDirectory( $newerRoot , $direc ) ) ) goto FindFile ;
		
		FindFile : {
			
			$file = Ted\FindFile( $rootDire , $name );
			
			if ( $file && is_file( $file ) ) 

				return Ted\FindWebPath( $file );

		} return null ;
		
	}

	public function PrintCss( $fName ){

		$C = $this->FindCss( $fName );

		$C = $C != null ? $C : $fName ;

		print "<link rel='stylesheet' type='text/css' href='{$C}' />" ;

	}

	public function PrintJs( $fName ){

		$J = $this->FindJs( $fName );

		$J = $J != null ? $J : $fName ;

		print "<script src='{$J}'></script>" ;

	}

	public function FindHtml( $Route = array() ){

		if ( ! is_array( $Route ) || empty( $Route ) ) return null ;

		$Route = array_values( $Route ) ;

		$EXECUTE = $BackUpFile = Ted\FindFilePath( $this->pageDirectory , $Route , [ "html" , "htm" ] ) ;
		
		while ( $EXECUTE && stristr( $EXECUTE , $Route[ 0 ] ) === false ) {

			$last = count( $Route ) - 1 ;

			if ( $last > 0 ) unset( $Route[ $last ] ) ;

			else break ;

			$Route = array_values( $Route ) ;

			$EXECUTE = Ted\FindFilePath( $this->pageDirectory , $Route , [ "html" , "htm" ] ) ;

		} if( $EXECUTE && $BackUpFile )

			$EXECUTE = ( dirname( $EXECUTE ) == dirname( $BackUpFile ) ) ? $BackUpFile : $EXECUTE ;
		
		$EXECUTE = ( $EXECUTE ) ? $EXECUTE : $BackUpFile;

		unset( $BackUpFile , $Route ) ;

		return $EXECUTE ;
		
	}
	
	/* ///////////// //////////////// */
	
	public function getHead(){ return self::$head ; }

	public function setContents( $Contents = null ){ self::$Cont = $Contents ; }

	public function getContents(){ return self::$Cont ; }
	
	public function getBody(){ return self::$body ; }
	
	public function RenderHead( ) {
		
		$returns = PHP_EOL ;
		
		$tags = ( is_array( self::$head['tag'] ) ) ? self::$head['tag'] : array ();
		
		$mets = ( is_array( self::$head['met'] ) ) ? self::$head['met'] : array ();
		
		$csse = ( is_array( self::$head['css'] ) ) ? self::$head['css'] : array ();
		
		$jses = ( is_array( self::$head['jsf'] ) ) ? self::$head['jsf'] : array ();
		
		$strs = ( is_array( self::$head['str'] ) ) ? self::$head['str'] : array ();
		
		foreach( $tags as $type => $tag ) if ( $tag ) $returns .= "\t\t" . $tag . PHP_EOL;
		
		foreach( $mets as $name => $con ) if ( $name && $con ) 
			
			$returns .= "\t\t" . "<meta name='{$name}' content='{$con}' />" . PHP_EOL;
		
		foreach( $csse as $numb => $css ) if ( $css ) $returns .= "\t\t" . 

			"<link rel='stylesheet' type='text/css' href='{$css}' />" . PHP_EOL;
		
		foreach( $jses as $numb => $jsf ) if ( $jsf ) $returns .= "\t\t" . 

			"<script src='{$jsf}'></script>" . PHP_EOL;
		
		foreach( $strs as $type => $str ) if ( $str ) $returns .= "\t\t" . $str . PHP_EOL;
		
		$returns .= PHP_EOL ;
		
		$returns = str_replace( PHP_EOL . PHP_EOL . PHP_EOL , PHP_EOL , $returns ) ;
		
		$returns = str_replace( PHP_EOL . PHP_EOL , PHP_EOL , $returns ) ;
		
		$returns = str_replace( PHP_EOL . PHP_EOL , PHP_EOL , $returns ) ;
		
		return $returns ;
	
	}
	
	public function RenderComponent(){
		
		$returns = self::$Cont ;
		
		$expl = explode( PHP_EOL , $returns ) ;

		foreach ( $expl as $key => $value ) if ( strlen( $value ) == 0 ) unset( $expl[ $key ] ) ;

		$expl = array_values( $expl ) ; $returns = "" ;
		
		foreach( $expl as $newLine ) $returns .= "\t\t" . $newLine . PHP_EOL ;
		
		$returns = substr( $returns , 0 , -2 ) ;
		
		$returns = str_replace( PHP_EOL . PHP_EOL . PHP_EOL , PHP_EOL , $returns ) ;
		
		$returns = str_replace( PHP_EOL . PHP_EOL , PHP_EOL , $returns ) ;
		
		$returns = str_replace( PHP_EOL . PHP_EOL , PHP_EOL , $returns ) ;
		
		return $returns ;
		
	}
	
	public function RenderBody( ) {

		$returns = PHP_EOL ;
		
		$tags = ( is_array( self::$body['tag'] ) ) ? self::$body['tag'] : array ();
		
		$jses = ( is_array( self::$body['jsf'] ) ) ? self::$body['jsf'] : array ();
		
		$strs = ( is_array( self::$body['str'] ) ) ? self::$body['str'] : array ();

		foreach( $tags as $type => $tag ) if ( $tag ) $returns .= $tag . PHP_EOL;
		
		foreach( $jses as $numb => $jsf ) if ( $jsf ) $returns .= 

			"\t\t" . "<script src='{$jsf}'></script>" . PHP_EOL; 
		
		foreach( $strs as $type => $str ) if ( $str ) $returns .= "\t\t" . $str . PHP_EOL;
		
		$returns .= PHP_EOL ;
		
		$returns = str_replace( PHP_EOL . PHP_EOL . PHP_EOL , PHP_EOL , $returns ) ;
		
		$returns = str_replace( PHP_EOL . PHP_EOL , PHP_EOL , $returns ) ;
		
		$returns = str_replace( PHP_EOL . PHP_EOL , PHP_EOL , $returns ) ;
		
		return $returns ;
	
	}

	/* ///////////// //////////////// */

	public final function ExecuteView( $File = null , $Args = array() ) {
			
		if ( $File ){
							
			$App = $app = $APP = $Application = $this->App();

			if ( $this instanceof Site_Interface ){
				
				$Html = $html = $HTML = $this->App()->AppInterface( "html" ) ;

				$Site = $SITE = $site = $this ;

			} else {

				$Site = $SITE = $site = $this->App()->AppInterface( "site" ) ;
				
				$Html = $html = $HTML = $this ;

			} $Args = ( count( $Args ) == 1 && isset( $Args[0] ) && is_array( $Args[0] ) ) ? $Args[0] : $Args ;

			foreach ( $Args as $key => $value ) 

				if ( is_int( $key ) || ( ( int) $key ) !== 0 ) unset( $Args[ $key ] ) ;

			foreach ( $Args as $Key_ga_s_go_dl___ga_95_ => $value_ga_s_g35_gdjz ) {

				${$Key_ga_s_go_dl___ga_95_} = $value_ga_s_g35_gdjz ;

				${ucfirst($Key_ga_s_go_dl___ga_95_)} = $value_ga_s_g35_gdjz ;

				${strtoupper($Key_ga_s_go_dl___ga_95_)} = $value_ga_s_g35_gdjz ;

				${strtolower($Key_ga_s_go_dl___ga_95_)} = $value_ga_s_g35_gdjz ;

			} self::$__EXTRA__ = $Args = ( ! empty( $Args ) ) ? $Args : self::$__EXTRA__ ;

			return include $File ;

		} $Args = ( $this->App()->Sink( ) ) ? $this->App()->Sink( ) : $Args ;

		if ( isset( $Args[ "error" ] ) ) print PHP_EOL . $Args[ "error" ] . PHP_EOL ;

		else print PHP_EOL . "404:No Html View For " . $this->getApp()->AppName() . PHP_EOL ;
		
		return $Args ;

	}

	public final function Call(){
	
		$Args = func_get_args();

		$Args = ( count( $Args ) == 1 && is_array( $Args[0] ) ) ? $Args[0] : $Args ;
		
		list( $Route , $Args ) = Ted\FindRouteElements( $Args );

		array_pop( $Route ) ;
		
		return $this->ExecuteView( $this->FindHtml( $Route ) , $Args ) ;

	}

	public function setController( $controller ){

		$this->HtmlLoadersList = $controller ;

	}

	public function Connect( $directRespond = false ){

		@ob_start(); // Start Output Buffrer
		@ob_clean(); // Start Cleaning Output Buffer

		//Find App's Html Holder File -> Just A Simple HtmlApp Container
		if( empty( $this->HtmlLoadersList ) )
			$this->setController( [ "html.html" , "index.html"] );

		// Find in root webui
		$AppHtmlIndexFile = Ted\FindFile( $this->Root() , $this->HtmlLoadersList );

		// Find in page/layout directory
		if ( ! $AppHtmlIndexFile ) 
			$AppHtmlIndexFile = Ted\FindFile( $this->pageDirectory , $this->HtmlLoadersList );
		
		// If Founded Then Load It !
		if ( $AppHtmlIndexFile ) $this->Response( $AppHtmlIndexFile );

		else { // Else Build A Simple One !
			
			print "<div class='TedHtml Output TedOutput HtmlOutput TedHtmlOutput'>" . PHP_EOL ;

				$PHPRoute = $this->App()->AppHistory() ;
				
				array_shift( $PHPRoute ) ;

				$eFile = $this->FindHtml( $PHPRoute ) ;
				
				if ( $eFile ) $this->Response( $eFile );

				else $this->App()->Execute();
				
			print PHP_EOL . "</div>" ;
		
		} $this->setContents( ob_get_contents() );
		
		@ob_end_clean(); // End Cleaning Output Buffer
		
		if ( $directRespond ) print $this->RenderComponent();
		
		return true ;
		
	}

	public function Response(){
	
		$Args = func_get_args();
		
		$Args = ( count( $Args ) == 1 && is_array( $Args[0] ) ) ? $Args[0] : $Args ;
		
		if ( empty( $Args ) ) $Args = [ "execute" => "false" ] ;
		
		$Args = ( $this->App()->Sink() ) ? $this->App()->Sink() : $Args ;

		//Response Has 3 Main Requirments
		$VIEW = null ; // The View HTML Page Name
		
		$EXECUTE = null ; // EXECUTE Of Application Work
		
		$EXTRA = array() ; // Extra Variables That Passed On
		
		$viewSearchList = [ 'view' ,'html' ,'layout' ,'page' ];
	
		foreach( $Args as $k => $v ){

			$doContinue = false ;
			
			if ( is_array( $v ) ){
				
				foreach( $viewSearchList as $m ) foreach ( array_keys( $v ) as $newKey ) 

					{ if ( stristr( $newKey , $m ) ){ $VIEW = $v[$newKey] ; } }
				
			} else if ( is_int( $k ) ) {
				
				if ( is_bool( $v ) ) $Args[ "execute" ] = $v ;
				
				elseif ( is_string( $v ) ) $VIEW = ( string ) $v ;
				
				unset( $Args[ $k ] );
				
				$doContinue = true ;
				
			} else if ( is_string( $k ) && is_string( $v ) ) {
				
				foreach( $viewSearchList as $m ){
					
					if ( strtolower( $k ) === $m ) {
						
						$VIEW = $v ;
						
						unset( $Args[ $k ] );

						$doContinue = true ;

						break;
						
					}
					
				}
				
			} if ( $doContinue ) continue ;
			
			$EXTRA[ $k ] = $Args[ $k ] ;
			
			unset( $Args[ $k ] );
			
		} unset( $viewSearchList , $Args ) ;
		
		$EXTRA = ( is_array( $EXTRA ) ) ? $EXTRA : array() ;
		
		$PHPRoute = $this->App()->AppHistory( );
		
		array_shift( $PHPRoute ) ;

		$directFile = $VIEW && is_file( $VIEW ) ? $VIEW : false ;

		if ( ! $directFile && $VIEW ) {
			
			if ( is_array( $VIEW ) ) {

				foreach ($VIEW as $k => $v) if ( empty($v) && !is_string($v) ) unset( $VIEW[ $k ] ) ;

				$PHPRoute = array_values( $VIEW ) ;

			} else {
				
				$VIEW = trim( $VIEW ) ;

				$VIEW = str_ireplace( TPath_DS , '/' , $VIEW ) ;
				
				if ( $VIEW && stristr( $VIEW , '/' ) !== false ) {
					
					$explo = explode( '/' ,  $VIEW ) ;
				
					foreach ( $explo as $newView ) 
						
						if ( ! empty( $newView ) && is_string( $newView ) ) 
							
							array_push( $PHPRoute , $newView ) ;
					
				} else array_push( $PHPRoute , $VIEW ) ;
				
			}
			
		} $EXECUTE_FILE = ( $directFile && is_file( $directFile ) ) ? $directFile : $this->FindHtml( $PHPRoute ) ;
		
		unset( $directFile , $PHPRoute ) ;
		
		return $this->ExecuteView( $EXECUTE_FILE , $EXTRA );
	
	}
	
}

?>
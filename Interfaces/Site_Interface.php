<?php

defined( 'TExec' ) or die( 'Access Denied' );

Ted\Import( "Base.Interfaces.Html_Interface" );

class Site_Interface extends Html_Interface{ 

	protected $SiteLoadersList = null ;
	
	public function __construct( $App = null , $UIRoot = null ) {
		
		// App With Html UI Directory list
		$this->setApp( $App , [ "www" , "site" , "public_www" , 
			"public_site" , "web" , "Web" , "WebUi" , "HTMLS" , 
			"htmls" , "WebApp" , "WebUI" , "WebUi" , "HtmlUI" ] ) ;
		
		$this->setRoot( $UIRoot );
		
		$this->init();
		
	}

	public function setController( $controller ){

		$this->SiteLoadersList = $controller ;

	}
	
	public function Respond( $directRespond = true ){ 

		$HtmlUI = $this->App()->AppInterface( "Html" ) ;

		//Fid Root!
		if ( $this->Root === $this->App()->AppRoot() )

			$this->Root = $HtmlUI ? $HtmlUI->Root() : $this->Root ;

		// Execute Init.php For SiteUI
		$this->ExecInitFiles( $this->Root , true );

		// Execute Init.php For HtmlUI
		$HtmlUI->ExecInitFiles( $HtmlUI->Root() );
		
		$HtmlUI->Connect( false );

		if ( ! $directRespond ) return true ;

		@ob_start(); // Start Output Buffrer
		@ob_clean(); // Cleaning Output Buffrer

		// set Controllers if not set
		if( empty( $this->SiteLoadersList ) )
			$this->setController( [ "site.php" , "index.php" ] );
	
		$SiteIndexFile = Ted\FindFile( $this->Root , $this->SiteLoadersList );
		
		if ( $SiteIndexFile ) 

			$this->Response( $SiteIndexFile );
		
		else { //
			
			// Print The Document In Simple Way
			print "<!doctype html>" . PHP_EOL ;
			
			print "<html>" . PHP_EOL ;
			
			print "\t<head>" . PHP_EOL ;
			
				print $this->RenderHead();
				
			print "\t</head>" . PHP_EOL ;
			
			print "\t<body class='TedEngineBody'>" . PHP_EOL ;
			
				print $this->RenderComponent();
				
				print $this->RenderBody();
				
			print "\t</body>" . PHP_EOL ;
			
			print "</html>";	
			
		} $Html = ob_get_contents() ;

		@ob_end_clean(); // End Cleaning Output Buffrer
		
		print trim( $Html , " /\\." . PHP_EOL ) ;

		return true ;
		
	}
	
	public function Response( $SiteFile = null  ){
		
		if( is_string( $SiteFile ) && is_file( $SiteFile ) ) {
		
			$Site = $SITE = $site = $this ;
			
			$Html = $HTML = $html = $this->App()->AppInterface( "html" ) ;
			
			$App = $app = $APP = $Application = $this->App();
			
			return include $SiteFile ;
			
		} return false;
		
	}

}
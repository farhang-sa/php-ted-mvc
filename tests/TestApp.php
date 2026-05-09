<?php define( 'TExec' , true );

include_once '../vendor/autoload.php' ;

class TestApp extends Ted\Application {

	public function Initialise(){

		echo 'Initialising App for instance mode' . Ted\br();

	}

	// works in HTML-UI
	public function Execute(){

		if( ! Ted\isCli() )
			// html view ( no routing just execute some htmls )
			return $this->ExecHtml();

		// else : cli mode baby
		$ui   = $this->AppInterface( 'cli' );
		$args = func_get_args();
		if( count( $args ) === 1 && $args[0] === 'info' )
			Ted\Ted::info();
		else $ui->print( 'we have just one command => info' );

	}
	
	public function Respond( $ui = 'html' ){

		// check ui
		$ui = Ted\isCli() ? 'cli' : 'html' ;

		// load ui!
		$ui = $this->AppInterface( $ui );

		// execute ui
		if( $ui instanceof Html_Interface ):

			$ui->Connect( false );
			$this->HtmlRespond( $ui );

		else :
			$ui->Connect( true );
		endif ;
	}

	public function ExecHtml(){
		if( $this->input->info ) {

			print '<a href="?" class="btn btn-info btn-lg p-3 m-3">Go back</a>' ;
		
			Ted\Ted::info();
		
			return ;
		
		} // else 

		print '<div><h3><a href="?info=1">See info</a></h3></div>' ;
		print Ted\br(3);

		// any code!
		print '<div class="row p-3"><div class="col-12 col-sm-2 col-md-4 col-lg-3"></div>';
		print '<div class="col-12 col-sm-10 col-md-8 col-lg-6">';
		print '<b> this is validation plugin test<br />input variable "name" please</b>' ;
		print '<form method="post"><input type="text" class="form-control" name="name"></form>';
		
		$cond = 'substr($,0,5):!farha' ;
		$name = $this->input->name ;
		$name = $this->validation->validate( $name , $cond );

		print "<b>Validation condition : $cond</b>" . Ted\br() ;
		print 'var_dump( $name ) : ' ;
		var_dump( $name );
		
		print '</div></div>' ;
	}

	public function HtmlRespond( $Html ){

		$Html->Title( 'TestApp | By Farhang Saeedi' );

		//$Html->ExternalCss( 
		//	'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' );
		//$Html->ExternalJs( 
		//	'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js' , false );

		// html head
		print "<html>\n<head>" ;
		print $Html->RenderHead();

		// html body
		print "</head>\n<body class='container p-3'>" ;
		print $Html->RenderComponent();
		print $Html->RenderBody();

		// end body
		print "</body>\n</html>" ;

	}

	public function Finish(){

		echo 'Finishing App for instance mode' . Ted\br();

	}

}

(new TestApp)->Respond();

?>
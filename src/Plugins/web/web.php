<?php

defined( 'TExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
class webPlugin extends Ted\Plugin {
    
    public function getHeaders( $link ){
    
        $head = @get_headers( $link , 1 );
        
        if( $head )
            return [ 
                "size" => $head["Content-Length"] , 
                "type" => $head["Content-Type"] 
            ];
        return false ;
       
    }
    
} ?>
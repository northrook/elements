<?php

namespace Northrook\Elements\Element;

use Northrook\Elements\Element;

class Content
{

    public static function render( string | array | Element $content, ?string $template = null ) : string {

        if ( $content instanceof Element ) {
            $content = $content->print();
        }

        if ( is_array( $content ) ) {
            $content = implode( '', $content );
        }

        return $content;
    }
// template
// pretty
//
}
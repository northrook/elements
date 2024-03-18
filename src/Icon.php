<?php

declare( strict_types = 1 );

namespace Northrook\Elements;

use Northrook\Elements\Element\Asset;
use Northrook\Support\Arr;
use Northrook\Symfony\Core\File;
use Northrook\Types\Path;

class Icon extends Asset
{

    public function __construct( protected string $get, ...$set ) {
//        if ( array_key_exists( 'get', $set ) ) {

        if ( $this->isPath( $this->get ) ) {
            $this->path = new Path( $this->get );
        }
        else {
            $this->path = new Path( dirname( __DIR__ ) . '/assets/icons' );
            [ $get, $pack ] = Arr::assignVariables( [ 'not-found', 'ui' ], $this->get );
            $this->path->add( $pack . '/' . $get . '.svg' );
        }


        $icon = $this->path->isValid ? File::getContent( $this->path ) : File::getContent( 'ui/not-found.svg' );

        dump( $this->path );
        $this->mimeType = File::getMimeType( $this->path );

        if ( $set[ 'iconStroke' ] ) {
            $icon = str_replace( 'stroke-width="1"', 'stroke-width="' . $set[ 'iconStroke' ] . '"', $icon );
        }

        $this->content[ 'svg' ] = $icon;

//
//            unset( $this->>get );
//        }

        parent::__construct( ...$set );
    }

    public static function get( string $icon, ?float $stroke = null ) : Icon {
        return new static( get : $icon, iconStroke : $stroke );
    }


}
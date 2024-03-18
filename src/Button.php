<?php

declare( strict_types = 1 );

namespace Northrook\Elements;

class Button extends Element
{
    protected array $attributes = [
        'type' => 'button',
    ];

    public function __construct( ...$set ) {

        if ( array_key_exists( 'icon', $set ) ) {
            $icon                    = $set[ 'icon' ];
            $this->content[ 'icon' ] = $icon instanceof Icon ? $icon : new Icon( get : $icon );
            unset( $set[ 'icon' ] );
        }

        parent::__construct( ...$set );
    }

    public static function close( string $label = 'Close' ) : self {
        $button = new static(
        // this generates auto style, with before/after or two spans, forming the × symbol
            class   : 'icon:close',
            content : '×',
            tooltip : $label
        );
        $button->tooltip( $label );

        return $button;
    }
}
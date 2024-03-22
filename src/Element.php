<?php

declare( strict_types = 1 );

namespace Northrook\Elements;


use JetBrains\PhpStorm\Pure;
use Northrook\Elements\Element\Attribute;
use Northrook\Elements\Element\Content;
use Northrook\Elements\Element\Html;
use Northrook\Elements\Element\Tag;
use Northrook\Elements\Element\Tooltip;
use Northrook\Support\Sort;


/**
 * @property ?string   $id
 * @property Attribute $class
 * @property Attribute $style
 * @property Tooltip   $tooltip
 *
 *
 */
class Element
{
    public const TAG = null;
    protected array     $content    = [];
    protected array     $attributes = [];
    public readonly Tag $tag;

    public function __construct( ...$set ) {
        $this->assignElementAttributes( $set );
    }

    final public function __get( string $name ) {
        if ( 'class' === $name ) {
            return new Attribute( $this->attributes, $name, $this );
        }

        if ( 'style' === $name ) {
            return new Attribute( $this->attributes, $name, $this, true );
        }

        return $this->$name;
    }

    public function tooltip( string $content ) : self {
        $this->attributes[ 'tooltip' ] = new Tooltip( $content );
        return $this;
    }

    protected function onConstrict() : void {}

    protected function onPrint() : void {}

    public function print( bool $pretty = false ) : string {

        $this->onPrint();

        $element = [
            '<' . $this->getElementAttributes() . '>',
            Content::render( $this->content ),
            $this->tag->isSelfClosing ? '</' . $this->tag . '>' : '',
        ];

        $element = implode( '', array_filter( $element ) );

        return $pretty ? Html::pretty( $element ) : $element;
    }

    public function __toString() : string {
        return $this->print();
    }

    protected function getElementAttributes( bool $implode = true ) : array | string {
        $attributes = array_filter( [ $this->tag, ... $this->getAttributes() ] );
        return $implode ? implode( ' ', $attributes ) : $attributes;
    }

    private function getAttributes( bool $raw = false ) : array {

        $attributes = [];

        foreach ( $this->attributes as $name => $value ) {

            if ( 'style' === $name ) {
                foreach ( $value as $key => $val ) {
                    $value[ $key ] = "$key: $val;";
                }
            }

            $value = match ( gettype( $value ) ) {
                'string'  => $value,
                'boolean' => $value ? 'true' : 'false',
                'array'   => implode( ' ', array_filter( $value ) ),
                'object'  => method_exists( $value, '__toString' ) ? $value->__toString() : null,
                'NULL'    => null,
                default   => (string) $value,
            };

            if ( in_array( $name, [ 'disabled', 'readonly', 'required', 'checked', 'hidden' ] ) ) {
                $value = null;
            }

            if ( $raw ) {
                $attributes[ $name ] = $value;
                continue;
            }

            $attributes[ $name ] = ( null === $value ) ? $name : "$name=\"$value\"";
        }
        return Sort::elementAttributes( $attributes );
    }

    final protected function assignElementAttributes( array $set ) : self {

        if ( array_key_exists( 'tag', $set ) ) {
            $this->tag = new Tag( $set[ 'tag' ] );
            unset( $set[ 'tag' ] );
        }
        else {
            $this->tag = new Tag( $this->autoTag() );
        }

//        dd( $set );
        foreach ( $set as $name => $value ) {
            if ( $name == 'content' || $name == 'innerHTML' ) {
                $this->content[ $name ] = $value;
            }
            else {
                $this->set( $name, $value );
            }
        }

        return $this;
    }

    public function has( string $name ) : bool {
        return array_key_exists( $name, $this->attributes );
    }

    public function set( string $name, mixed $value ) : self {
        $this->attributes[ $this->key( $name ) ] = match ( $this->key( $name ) ) {
            'id'    => Attribute::id( $value ),
            'class' => Attribute::classes( $value ),
            'style' => Attribute::styles( $value ),
            default => $value,
        };
        return $this;
    }

    public function getAttribute( string $name ) : mixed {
        return $this->attributes[ $this->key( $name ) ];
    }

    public function remove( string $name ) : self {
        unset( $this->attributes[ $this->key( $name ) ] );
        return $this;
    }

    private function key( string $key ) : string {
        return str_replace( '_', '-', strtolower( trim( $key ) ) );
    }

    /**
     * Get a tag based on the .
     *
     * @return string
     */
    #[Pure( true )]
    private function autoTag() : string {

        if ( $this::TAG ) {
            return $this::TAG;
        }

        $autoTag = substr( get_called_class(), strrpos( get_called_class(), '\\' ) + 1 );
        return Tag::isValidTag( $autoTag ) ? $autoTag : 'div';
    }


}
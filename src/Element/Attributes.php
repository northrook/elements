<?php

declare( strict_types = 1 );

namespace Northrook\Elements\Element;


use Northrook\Support\Sort;


/**
 * @property Attribute $class
 * @property Attribute $style
 * @property array     $attributes
 *
 */
class Attributes
{
    protected array $attributes = [];

    public function __construct( ...$set ) {
        $this->assignElementAttributes( $set );
    }

    final public function __get( string $name ) {

        if ( 'attributes' === $name ) {
            return $this->getAttributes( true );
        }

        if ( 'class' === $name ) {
            return new Attribute( $this->attributes, $name, $this );
        }

        if ( 'style' === $name ) {
            return new Attribute( $this->attributes, $name, $this, true );
        }

        if ( array_key_exists( $name, $this->attributes ) ) {
            return $this->attributes[ $name ];
        }

        return $this->$name;
    }

    public function __toString() : string {
        return implode( ' ', $this->getAttributes() );
    }

    protected function getAttributes( bool $raw = false ) : array {

        $attributes = [];

        foreach ( $this->attributes as $name => $value ) {

            if ( 'id' === $name && !$value ) {
                continue;
            }

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

        foreach ( $set as $name => $value ) {
            $this->set( $name, $value );
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

    public function id( ?string $string = null ) : ?string {
        return $string ? Attribute::id( $string ) : $this->getAttribute( 'id' );
    }

    public function class( ?string $string = null ) : ?string {
        return implode( ' ', $this->getAttribute( 'class' ) + [ $string ] );
    }

    public function getAttribute( string $name ) : mixed {
        return $this->attributes[ $this->key( $name ) ] ?? null;
    }

    public function remove( string $name ) : self {
        unset( $this->attributes[ $this->key( $name ) ] );
        return $this;
    }

    private function key( string $key ) : string {
        return str_replace( '_', '-', strtolower( trim( $key ) ) );
    }

}
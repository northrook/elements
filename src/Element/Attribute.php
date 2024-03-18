<?php

namespace Northrook\Elements\Element;

use Northrook\Elements\Element;
use Northrook\Logger\Log;
use ReflectionClass;

class Attribute
{
    public function __construct(
        private array            $attributes,
        private readonly string  $attribute,
        private readonly Element $element,
        private readonly bool    $keys = false,
    ) {
//        dump( "Parsing: $this->attribute" );
    }

    public function add( array | string $attribute ) : self {

        if ( 'style' === $this->attribute ) {
            $attribute = $this::styles( $attribute );
        }

        if ( is_array( $attribute ) ) {
            $this->attributes[ $this->attribute ] = array_merge( $this->attributes[ $this->attribute ], $attribute );
        }
        else {
            $this->attributes[ $this->attribute ][] = $attribute;
        }

        return $this->updateElement();
    }

    public function has( $value ) : bool {
        if ( $this->keys ) {
            return array_key_exists( $value, $this->attributes[ $this->attribute ] );
        }
        return in_array( $value, $this->attributes[ $this->attribute ] );
    }

    public function remove( $value ) : self {
        if ( $this->has( $value ) ) {
            if ( !$this->keys ) {
                $value = array_search( $value, $this->attributes[ $this->attribute ] );
            }
            unset( $this->attributes[ $this->attribute ][ $value ] );

            return $this->updateElement();
        }

        return $this;
    }

    public function get( $value ) {
        return $this->attributes[ $value ];
    }

    public function all() : array {
        return $this->attributes;
    }

    private function updateElement() : self {
        $element = new ReflectionClass( $this->element );
        $element->getProperty( 'attributes' )->setValue( $this->element, $this->attributes );
        return $this;
    }

    public static function id( ?string $string = null ) : ?string {
        return $string ? preg_replace( '/[^A-Za-z0-9_-]/', '', $string ) : null;
    }

    public static function classes( null | string | array $set = null ) : ?array {

        if ( !$set ) {
            return null;
        }

        $classes = [];

        if ( is_string( $set ) ) {
            $classes = explode( ' ', $set );
        }

        if ( is_array( $set ) && !array_is_list( $set ) ) {
            Log::Warning(
                '{key} was parsed, but {error}. Keys were {action}.',
                [
                    'key'      => print_r( $set, true ),
                    'error'    => 'passed array is associative',
                    'action'   => 'ignored',
                    'solution' => "Provide an indexed array.\nFor example: [ 'property1', 'property2' ], or use the add() method.",
                ],
            );
        }

        if ( is_array( $set ) ) {
            $classes = $set;
        }

        foreach ( $classes as $index => $class ) {

            if ( !is_string( $class ) ) {
                Log::Error(
                    '{value} was parsed, but it was type {error}. It should be a {correct}. It was {action}.',
                    [
                        'value'   => print_r( $class, true ),
                        'error'   => gettype( $class ),
                        'raw'     => $class,
                        'correct' => 'string',
                        'action'  => 'removed',
                    ],
                );
                unset( $classes[ $index ] );
                continue;
            }

            if ( ctype_digit( $class[ 0 ] ) ) {
                Log::Error(
                    '{value} was parsed, but it starts with a number. Classes cannot start with numbers. It was {action}.',
                    [
                        'value'  => $class,
                        'action' => 'removed',
                    ],
                );
                unset( $classes[ $index ] );
                continue;
            }

            $classes[ $index ] = strtolower( trim( $class ) );
        }

        return array_values( $classes );
    }

    public static function styles( null | string | array $set = null ) : ?array {

        if ( !$set ) {
            return null;
        }

        $styles = [];

        if ( is_string( $set ) ) {
            foreach ( array_filter( explode( ';', $set ) ) ?? [] as $declaration ) {
                if ( !strpos( $declaration, ':' ) ) {
                    Log::Warning(
                        'The style {key} was parsed, but {error}. The style was not rendered.',
                        [
                            'key'   => $declaration,
                            'error' => 'has no declaration separator',
                            'value' => $set,
                        ],
                    );
                    continue;
                }
                [ $property, $value ] = explode( ':', $declaration );
                $styles[ $property ] = $value;
            }
        }

        if ( is_array( $set ) && array_is_list( $set ) ) {
            Log::Error(
                'The style {key} was parsed, but {error}. No styles were set.',
                [
                    'key'      => $set,
                    'error'    => 'has no declaration separator',
                    'value'    => $set,
                    'solution' => "Provide a key-value array.\nFor example: [ 'property1' => 'value1', 'property2' => 'value2' ], or use the add() method.",
                ],
            );
            return null;
        }

        if ( is_array( $set ) ) {
            $styles = $set;
        }

        foreach ( $styles as $key => $value ) {
            if ( !is_string( $value ) || !trim( $value ) ) {
                Log::Warning(
                    'The style {key} was parsed, but {error}. The style was not rendered.',
                    [
                        'key'   => $key,
                        'error' => !is_string( $value ) ? 'was not a string' : 'was empty',
                    ],
                );
                unset( $styles[ $key ] );
                continue;
            }
            unset( $styles[ $key ] );
            $key            = strtolower( trim( $key, ": \t\n\r\0\x0B" ) );
            $value          = strtolower( trim( $value, "; \t\n\r\0\x0B" ) );
            $styles[ $key ] = $value;
        }


        return $styles;
    }
}
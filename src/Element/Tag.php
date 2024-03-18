<?php

declare( strict_types = 1 );

namespace Northrook\Elements\Element;

use JetBrains\PhpStorm\ExpectedValues;

final class Tag
{

    public const NAMES = [
        'div', 'body', 'html', 'li', 'dropdown', 'menu', 'modal', 'field', 'fieldset', 'legend', 'label', 'option',
        'select', 'input', 'textarea', 'form', 'tooltip', 'section', 'main', 'header', 'footer', 'div', 'span', 'p',
        'ul', 'a', 'img', 'button', 'i', 'strong', 'em', 'sup', 'sub', 'br', 'hr', 'h', 'h1', 'h2', 'h3', 'h4',
    ];

    private const SELF_CLOSING = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source',
        'track', 'wbr',
    ];
    private string $name;

    public function __construct(
        #[ExpectedValues( self::NAMES )]
        string       $name = 'div',
        public ?bool $isSelfClosing = null,
    ) {
        $this->set( $name );
    }

    /**
     * @param string  $name
     *
     * @return Tag
     */
    public function set(
        #[ExpectedValues( self::NAMES )]
        string $name,
    ) : Tag {
        $this->name = strtolower( $name );

        if ( $this->isSelfClosing === null ) {
            $this->isSelfClosing = !in_array( $this->name, self::SELF_CLOSING );
        }

        return $this;
    }

    public function __toString() : string {
        return $this->name;
    }

    public function is(
        #[ExpectedValues( self::NAMES )]
        string $name,
    ) : bool {
        return $this->name === $name;
    }

    public static function isValidTag( ?string $string = null ) : bool {
        return in_array( strtolower( $string ), self::NAMES );
    }

}
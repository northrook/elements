<?php

namespace Northrook\Elements\Element;

use Northrook\Elements\Element;
use Northrook\Symfony\Core\Support\Str;
use Northrook\Types\Path;

/**
 * @property string $mimeType
 */
abstract class Asset extends Element
{
    private static array $cache = [];
    protected Path       $path;
    protected string     $mimeType;

    protected function isPath( string $string ) : bool {
        $separator = Str::contains( $string, [ '/', '\\' ], true );
        return $separator ? Str::contains( $string, [ $separator[ 0 ], '.' ] ) : false;
    }

}
<?php

declare( strict_types = 1 );

namespace Northrook\Elements;

use Northrook\Elements\Element\Asset;

/**
 * Handle both single-url and srcset images
 *
 * * Wrap each image in a <picture> tag
 * * Add a <source> tag for each image
 * * Caption
 * * Alt
 * * Author/copyright
 * * Blurhash
 */
class Image extends Asset
{

}
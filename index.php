<?php

use Northrook\Elements\Button;
use Northrook\Elements\Element;
use Northrook\Elements\Icon;
use Northrook\Logger\Log;

require_once __DIR__ . '/vendor/autoload.php';

$element = new Element(
    id         : 'button',
    class      : 'button m-r:sm',
    style      : '  background-color: red;',
    selected   : true,
    aria_label : 'Button',
);


$element->tag->set( 'input' );

$button = new Button(
    type      : 'submit',
    content   : 'Submit',
    innerHTML : 'Submitted',
    icon      : Icon::get( 'code:ui' ),
);

$element->style->add( [ 'background-color' => 'blue' ] );

$element->set( 'class', 'testing!!' );
//echo (string) $button;
echo Button::close()->print();
echo Icon::get( 'h1' );
dump(
    Icon::get( 'h1' ),
//    $element->has( 'aria-label' ),
////    $element->class->has( 'element' ),
////    $element->class->add( 'testing' ),
////    $element->class->has( 'testing' ),
////    $element->class->remove( 'element' ),
////    $element->style->has( 'background-color' ),
//    $button,
//    (string) $button,
//    $element,
//    (string) $element,
//    $element->getAttributes( true ),
    Log::inventory(),
);

<?php

use Characteristic;
use Lindan\Tranformation\XmlModel\XmlModelSerializer;

class Characteristics extends XmlModelSerializer {
    public $random;


    public $children=[
        'CharacteristicList*|characteristic'=>Characteristic::class
    ];
    /**
     * List of Characteristic
     *
     * @var Characteristic[]
     */
    public $CharacteristicList;
}
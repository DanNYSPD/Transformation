<?php

use Lindan\Tranformation\XmlModel\XmlModelSerializer;

class Characteristics extends XmlModelSerializer {
    public $random="a";


    public $children=[
        #The firts part is the property name, the second one is the tagname
        'CharacteristicList*|characteristic'=>Characteristic::class
    ];
    /**
     * List of Characteristic
     *
     * @var Characteristic[]
     */
    public $CharacteristicList;
}
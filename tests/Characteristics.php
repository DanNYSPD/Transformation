<?php

use Lindan\Tranformation\XmlModel\XmlModelSerializer;

class Characteristics extends XmlModelSerializer {
    public $random="a";


    public $children=[
        #The firts part is the property name, the second one is the tagname
        'CharacteristicList*|characteristic'=>Characteristic::class,
        'race|Race'=>Race::class,
        'breed|Breed'=>Breed::class,
    ];
    /**
     * List of Characteristic
     *
     * @var Characteristic[]
     */
    public $CharacteristicList;
    /**
     *  I added this property only to test if this single element is created as child.
     *
     * @var Race
     */
    public $race;

    public $breed;
}
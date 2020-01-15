<?php

use Lindan\Tranformation\XmlModel\XmlModel;
use Lindan\Tranformation\XmlModel\XmlModelSerializer;

class Pet extends XmlModel{
    
    public $attributes=[
        'name', #in this case property name is the same for the attribute        
    ];

    public $name;

    public $children=[
        'characteristics'=>Characteristics::class
    ];
    /**
     * Undocumented variable
     *
     * @var Characteristics
     */
    public $characteristics;
}
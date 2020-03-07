<?php

use Lindan\Tranformation\XmlModel\XmlModel;

class Breed extends XmlModel{

    public $children=[
        #The firts part is the property name, the second one is the tagname
        'specie|specie'=>Specie::class,
        
    ];
    public $bahevior;
    public $specie;
}
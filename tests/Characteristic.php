<?php

use Lindan\Tranformation\XmlModel\XmlModelSerializer;

class Characteristic extends XmlModelSerializer{
  public  $name; 
  public  $description;
  public $children=[
    
    'breed|Breed'=>Breed::class,
  ];
  /**
   * Just added to represent a child
   *
   * @var Breed
   */
  public $breed;
}
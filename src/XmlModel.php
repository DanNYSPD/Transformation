<?php
namespace Lindan\Tranformation\XmlModel;

use DOMNode;
use DOMXPath;
use DOMElement;
use DOMDocument;
use RuntimeException;
/**
 * @author Daniel J Hdz <daniel.hernandez.job@gmail.com>
 * 
 */
class XmlModel {
    public $noAttributes=false;
    public $attributes=[];

    public $children=[];
    /**
     * Undocumented variable
     *
     * @var DOMElement
     */
    protected $node;
    public function __construct(?DOMElement $node=null){
        $this->node=$node;
    }
    /**
     * Undocumented variable
     *
     * @var DOMXPath
     */
    public $_xpath=null;
    public function setXpath(?DOMXPath $xpath){
        $this->_xpath=$xpath;
    }
    public static function parseFromArray(DOMElement $node,$object, array $attributes){
        foreach ($attributes as $key =>$property) {
            if(\in_array($property,['_xpath','attributes','noAttributes','children','node'])){
                continue;
            }
            //if the key is numeric use the $property as name for property and attribute
            if(\is_numeric($key)){
                $object->{$property} =$node->getAttribute($property);
            }else{
                //use the key  as attribute name
                $object->{$property} =$node->getAttribute($key);
            }           
        }
    }
    public function parseAttributes(DOMElement $node){
        if($this->noAttributes==true){
            return;
        }
        $attributes=[];
        #si esta vacio attributes tomara las propiedades de clase
        if(empty($this->attributes)||count($this->attributes)==0){
            $attributes= \array_keys(get_object_vars ($this));
        }else{
            $attributes=$this->attributes;
        }
        //note, when there is at least one element with associative key on an array, then the other elements became index based on elements
        //with numeric index. 
        // By the other hand, when we use a simple array with key=>$value expresion, $key is a numeric index, so the next code  applies for both cases
        self::parseFromArray($node,$this,$attributes);
        //\var_dump($this->_xpath);
        $this->parsedAttributes($node,$this->_xpath);
    }
    public function parseChildren(bool $deep=false){
        if(empty($this->children)||count($this->children)==0){
            return false;
        }
        if(null==$this->node){
            //return false;
        }
        foreach ($this->children as $tagNameAndProperty=>$ClassFQN) {
            //list($tagNameAndProperty,$ClassFQN)=$childDefinition;
           
            if(($pos=\strpos($tagNameAndProperty,'*'))!==false){
                $tagName;
                $propertyName;
                $posPipe=\strpos($tagNameAndProperty,'|');
                #es un array
                #obtenemos el nombre del tag (puede ser explicito despues de un| , o implicito, no coloclando nada y asumiendo que el nombre de la propiedad)
                if($posPipe>0 &&($len=\strlen($tagNameAndProperty))!=$pos){
                    $tagName=\substr($tagNameAndProperty,$posPipe+1,$len-$posPipe);
                    $propertyName=\substr($tagNameAndProperty,0,$pos);
                }else{
                    $tagName=$tagNameAndProperty;
                    $propertyName=$tagNameAndProperty;
                }

               
                  $nodeList=  $this->node->getElementsByTagName($tagName);
                   foreach ($nodeList as $node) {
                      $element=  new $ClassFQN();
                      $element->node=$node;
                      $element->parseAttributes($node);
                      if($deep){
                        $element->parseChildren($deep);
                      }
                      if(!\is_array($this->{$propertyName})){
                          #if the property isn't  initialized, initialize it as an array
                          $this->{$propertyName}=[];
                         #throw new RuntimeException("Propertie {$propertyName} is not an array in class ".get_class($this));
                      }
                      $this->{$propertyName}[]=$element;
                      $this->onEveryNode($node,$propertyName);//new function
                   }
                #return true;
            }else{
                #We are just interested in the first element(child)
                $this->{$tagNameAndProperty} = new $ClassFQN();
                $nodeList=  $this->node->getElementsByTagName($tagNameAndProperty);                
                if($nodeList->length>0){
                    $this->{$tagNameAndProperty}->node = $nodeList->item(0);
                    $this->{$tagNameAndProperty}->parseAttributes($nodeList->item(0));
                    $this->{$tagNameAndProperty}->parseChildren($deep,$tagNameAndProperty);

                    $this->onEveryNode($nodeList->item(0),$tagNameAndProperty);//new function
                }
                
            }
           
        }
        $this->parsedChildren($this->node);
    }

    public function onEveryNode(DOMElement $node,string $propertyName){
        //this can be inhereted
        
    }

    public function parsedAttributes(DOMElement $node,?DOMXPath $xpath){
        //to implemnet in subclases
    }
    public function parsedChildren(DOMElement $node){

    }


    /**
     * Verifies wether the propertie  _xpath has a valueundocumented variable
     *
     * @return  bool
     */ 
    public function hasXpath():bool
    {
        return !empty($this->_xpath);
    }
}
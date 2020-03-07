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
            if(self::isInArray($property)){
                continue;
            }
            //if the key is numeric use the $property as name for property and attribute
            $attributeName=\is_numeric($key)?$property:$key;
            if($node->hasAttribute($attributeName)){
                $object->{$property} =$node->getAttribute($attributeName);
            }      
        }
    }
    public function getAttributes(){
        $attributes=[];
        #si esta vacio attributes tomara las propiedades de clase
        if(empty($this->attributes)||count($this->attributes)==0){
            $attributes= \array_keys(get_object_vars ($this));
        }else{
            $attributes=$this->attributes;
        }
        return $attributes;
    }
    public function parseAttributes(DOMElement $node){
        if($this->noAttributes==true){
            return;
        }
       
        $attributes=$this->getAttributes();
        //note, when there is at least one element with associative key on an array, then the other elements became index based on elements
        //with numeric index. 
        // By the other hand, when we use a simple array with key=>$value expresion, $key is a numeric index, so the next code  applies for both cases
        self::parseFromArray($node,$this,$attributes);
        //\var_dump($this->_xpath);
        $this->parsedAttributes($node,$this->_xpath);
    }
    public function hasChildren():bool{
        return !empty($this->children)&&!count($this->children)==0;
    }
    /**
     * Resolves the tagName and the property when the declaration defines a list
     *
     * @param string $tagNameAndProperty
     * @param integer $pos
     * @return array
     */
    protected function resolveTagNameAndPropertyWhenItsAList(string $tagNameAndProperty,int $pos){
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
        return [$tagName,$propertyName];
    }
    public function parseChildren(bool $deep=false){
        if(!$this->hasChildren()){
            return false;
        }
        if(null==$this->node){
            //return false;
        }
        foreach ($this->children as $tagNameAndProperty=>$ClassFQN) {
            //list($tagNameAndProperty,$ClassFQN)=$childDefinition;
           
            if(($pos=\strpos($tagNameAndProperty,'*'))!==false){
                //we deal with the whole list
                 [$tagName,$propertyName]=$this->resolveTagNameAndPropertyWhenItsAList($tagNameAndProperty,$pos);
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
                $tagName;
                $propertyName;
                if(($pos=\strpos($tagNameAndProperty,'|'))!==false){
                    list($propertyName,$tagName)=explode('|',$tagNameAndProperty);
                }else{
                    $tagName=$tagNameAndProperty;
                    $propertyName=$tagNameAndProperty;
                }
                
                

                $this->{$propertyName} = new $ClassFQN();
                $nodeList=  $this->node->getElementsByTagName($tagName);                
                if($nodeList->length>0){
                    $this->{$propertyName}->node = $nodeList->item(0);
                    $this->{$propertyName}->parseAttributes($nodeList->item(0));
                    $this->{$propertyName}->parseChildren($deep,$tagName);

                    $this->onEveryNode($nodeList->item(0),$tagName);//new function
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
    public static function isInArray($property){
        return \in_array($property,['_hidden','_xpath','attributes','noAttributes','children','node']);
    }

    public function createNode(string $name){
        if(!$this->node){
            $this->node=$this->domDocument->createElement($name);
        }
        
        $attributes=$this->getAttributes();
        
        self::populateWithAttributes($node,$this,$attributes);
        //Now I will create the children nodes:

        if(!$this->hasChildren()){
            return;
        }
        //if this object has children then create it's subnodes (children nodes)
    }
    /**
     * Undocumented variable
     *
     * @var DOMDocument
     */
   protected $domDocument;
    /**
     * This function receives and object and a node , and populates the $node with the properties defined in the object
     */
    public static function populateWithAttributes(DOMElement $node,$object, array $attributes){
        foreach ($attributes as $key =>$property) {
            if(self::isInArray($property)){
                continue;
            }
            //if the key is numeric use the $property as name for property and attributes
            $attributeName=\is_numeric($key)?$property:$key;
            $node->setAttribute($attributeName, $object->{$property});                      
        }
    }
}
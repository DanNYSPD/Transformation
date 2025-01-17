<?php
namespace Lindan\Tranformation\XmlModel;

use DOMNode;
use DOMXPath;
use DOMElement;
use DOMDocument;
use RuntimeException;
use InvalidArgumentException;

/**
 * @author Daniel J Hdz <daniel.hernandez.job@gmail.com>
 * 
 */
class XmlModel {
    /**
     * This property indicates wheter the model has or not attributes(because an empty attributes array indicates that properties must be taken as attributes )
     *
     * @var boolean
     */
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
     * Resolves the tagName and the property when the declaration defines a list, that is, the property is an array 
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
    /**
     * This function resolve the tagName and property when the prperty is an object, that is when it's a single child
     *
     * @param string $tagNameAndProperty
     * @return void
     */
    protected function resolveTagNameAndPropertyWhenItsASingleChild($tagNameAndProperty){
        $tagName;
        $propertyName;
        if(($pos=\strpos($tagNameAndProperty,'|'))!==false){
            list($propertyName,$tagName)=explode('|',$tagNameAndProperty);
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
                [$tagName,$propertyName]=$this->resolveTagNameAndPropertyWhenItsASingleChild($tagNameAndProperty);
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
        return \in_array($property,['_hidden','_xpath','attributes','noAttributes','children','node',"_hideXmlAttributes"]);
    }
    public static function isInChildren($property){
        //return \in_array($property,$this)
    }

    public function createNodeAndPopulate(?string $name='',?string $ns=null){
        if(!$this->node){
            if (!$ns){
                $this->node=$this->domDocument->createElement($name);

            }else{
                $this->node=$this->domDocument->createElementNS($ns,$name);

            }
        }
        
        $attributes=$this->getAttributes();
        if($this->noAttributes!==true){
            self::populateWithAttributes($this->node,$this,$attributes);
        }
        
        //Now I will create the children nodes:

        if(!$this->hasChildren()){
            return;
        }
        //if this object has children then create it's subnodes (children nodes)
        foreach ($this->children as $tagNameAndProperty=>$ClassFQN) {
            //list($tagNameAndProperty,$ClassFQN)=$childDefinition;
           
            if(($pos=\strpos($tagNameAndProperty,'*'))!==false){
                //we deal with the whole list
                 [$tagName,$propertyName]=$this->resolveTagNameAndPropertyWhenItsAList($tagNameAndProperty,$pos);

                   //in this case the property is an array of objects
                  if(is_array($this->{$propertyName})){
                   foreach ($this->{$propertyName} as $object) {                     
                     $node= $this->domDocument->createElement($tagName);    
                     //as we create the node here, we passed it to the object
                        $object->setDomDocument($this->domDocument);
                        $object->setNode($node);                                       
                        
                        $object->createNodeAndPopulate();
                        $this->node->appendChild($node);
                   }
                }else{
                    #\var_dump($this->{$propertyName});
                }
                #return true;
            }else{
                [$tagName,$propertyName]=$this->resolveTagNameAndPropertyWhenItsASingleChild($tagNameAndProperty);
                #The rule is, only if it's different from null, it will be created.
                if($this->{$propertyName}!==null){
                    $newNode=$this->domDocument->createElement($tagName);
                    if(
                        /*
                         $this->{$propertyName} instanceof XmlModel ||
                        $this->{$propertyName} instanceof self ||
                        $this->{$property} instanceof XmlModelSerializer
                        */
                        \method_exists($this->{$propertyName},'setDomDocument')&&
                        \method_exists($this->{$propertyName},'setNode')&&
                        \method_exists($this->{$propertyName},'createNodeAndPopulate')
                        
                        ){
                        $this->{$propertyName}->setDomDocument($this->domDocument);
                        $this->{$propertyName}->setNode($newNode);
                        $this->{$propertyName}->createNodeAndPopulate();
                        $this->node->appendChild($newNode);
                    }else {
                        throw new InvalidArgumentException("The property: {$propertyName} doesn't implement the XmlModel");
                    }
                }
            }
           
        }
        return $this->node;
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
        #\var_dump($attributes);
        foreach ($attributes as $key =>$property) {
            #echo $property;
            if(self::isInArray($property)){
                continue;
            }
            //if the key is numeric use the $property as name for property and attributes
            $attributeName=\is_numeric($key)?$property:$key;
            if(!\is_scalar($object->{$property})){
                #echo "please define the properties array or a scalar vale {$property} is not scalar";
                continue;
            }
            $node->setAttribute($attributeName, $object->{$property});                      
        }
        
    }

    /**
     * Set undocumented variable
     *
     * @param  DOMElement  $node  Undocumented variable
     *
     * @return  self
     */
    public function setNode(DOMElement $node)
    {
        $this->node = $node;

        return $this;
    }

   /**
    * Set undocumented variable
    *
    * @param  DOMDocument  $domDocument  Undocumented variable
    *
    * @return  self
    */
   public function setDomDocument(DOMDocument $domDocument)
   {
      $this->domDocument = $domDocument;

      return $this;
   }
}
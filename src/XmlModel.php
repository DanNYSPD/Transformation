<?php
namespace Lindan\Tranformation\XmlModel;

use DOMElement;
use DOMDocument;
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
        foreach ($attributes as $property) {
            if(\in_array($property,['attributes','noAttributes','children','node'])){
                continue;
            }
            $this->{$property} =$node->getAttribute($property);
        }
    }
    public function parseChildren(bool $deep=false){
        if(empty($this->children)||count($this->children)==0){
            return false;
        }
        if(null==$this->node){
            return false;
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
                    $TagName=\substr($tagNameAndProperty,$posPipe+1,$len-$posPipe);
                    $propertyName=\substr($tagNameAndProperty,0,$pos);
                }else{
                    $tagName=$tagNameAndProperty;
                    $propertyName=$tagNameAndProperty;
                }

               
                  $nodeList=  $this->node->getElementsByTagName($TagName);
                   foreach ($nodeList as $node) {
                      $element=  new $ClassFQN();
                      $element->node=$node;
                      $element->parseAttributes($node);
                      if($deep){
                        $element->parseChildren($deep);
                      }
                      $this->{$propertyName}[]=$element;
                   }
                return true;
            }else{
                #solo interesa el primero elemento.
                $this->{$tagNameAndProperty} = new $ClassFQN();
                $nodeList=  $this->node->getElementsByTagName($tagNameAndProperty);                
                if($nodeList->length>0){
                    $this->{$tagNameAndProperty}->node = $nodeList->item(0);
                    $this->{$tagNameAndProperty}->parseAttributes($nodeList->item(0));
                    $this->{$tagNameAndProperty}->parseChildren($deep);
                }
                
            }
           
        }
    }

}
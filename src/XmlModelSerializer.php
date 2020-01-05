<?php
namespace Lindan\Tranformation\XmlModel;

use JsonSerializable;

class XmlModelSerializer extends XmlModel implements JsonSerializable{
    public $hideXmlAttribytes=true; 
    public function jsonSerialize() {
        $clean=[];
        foreach ($this as $propertie => $value) {
            if($this->hideXmlAttribytes===true&& !\in_array($propertie,['noAttributes','attributes','children','node','hideXmlAttribytes'])){
                $clean[$propertie]=$value;
            }
        }
        return $clean;
    }
}
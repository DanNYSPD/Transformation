<?php
namespace Lindan\Tranformation\XmlModel;

use JsonSerializable;

class XmlModelSerializer extends XmlModel implements JsonSerializable{
    public $hideXmlAttribytes=true;
    protected $_hidden=[]; 
    public function jsonSerialize() {
        $clean=[];
        $defaultHidden=['_xpath','_hidden','noAttributes','attributes','children','node','hideXmlAttribytes'];
        $hidden=array_merge($defaultHidden,$this->_hidden);
        foreach ($this as $propertie => $value) {
            if($this->hideXmlAttribytes===true&& !\in_array($propertie,$hidden)){
                $clean[$propertie]=$value;
            }
        }
        return $clean;
    }
}
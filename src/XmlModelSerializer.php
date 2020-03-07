<?php
namespace Lindan\Tranformation\XmlModel;

use JsonSerializable;

class XmlModelSerializer extends XmlModel implements JsonSerializable{
    public $_hideXmlAttributes=true;
    protected $_hidden=[]; 
    public function jsonSerialize() {
        $clean=[];
        $defaultHidden=['_xpath','_hidden','noAttributes','attributes','children','node','_hideXmlAttributes'];
        #echo \var_dump($this->_hidden);
        $hidden=array_merge($defaultHidden,$this->_hidden);
        foreach ($this as $propertie => $value) {
            if($this->_hideXmlAttributes===true&& !\in_array($propertie,$hidden)){
                $clean[$propertie]=$value;
            }
        }
        return $clean;
    }
}
<?php


namespace app\models\fields;


use app\core\ModelField;

class StatusModelField extends ModelField
{
    public array $statusList = [];

    public function __construct(string $name, string $verbose = null)
    {
        parent::__construct($name, 'string', $verbose);
        $this->setValue(0);
        return $this;
    }

//    public function convert($value)
//    {
//        $value = trim($value);
//        if (ctype_digit($value)) {
//            return intval($value);
//        } elseif (!is_null($this->default) && !$value) {
//            return $this->default;
//        } else {
//            $this->addErrorMessage("Input value is not valid integer");
//            return null;
//        }
//    }

    public function getName()
    {
        return $this->statusList[$this->getValue()] ?? $this->getValue();
    }

    public function setStatusList(array $statusList)
    {
        $this->statusList = $statusList;
        return $this;
    }

    public function fieldValidate($value)
    {
        return isset($this->statusList[$value]);
    }

}
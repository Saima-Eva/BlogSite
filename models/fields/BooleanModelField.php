<?php


namespace app\models\fields;


use app\core\ModelField;

class BooleanModelField extends ModelField
{

    /**
     * TextModelField constructor.
     * @param string $name
     * @param string|null $verbose
     */
    public function __construct(string $name, string $verbose = null)
    {
        parent::__construct($name, 'boolean', $verbose);
        return $this;
    }

    public function convert($value)
    {
        $message = "Input value is not valid boolean";
        if (gettype($value) == "boolean") {
            return $value;
        } else if (gettype($value) == "string") {
            $value = trim($value);
            if ($this->checkBool($value)) {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } else {
                $this->addErrorMessage($message);
                return null;
            }
        } else {
            $this->addErrorMessage($message);
            return null;
        }
    }

    protected function checkBool($string)
    {
        $string = strtolower($string);
        return (in_array($string, array("true", "false", "1", "0", "yes", "no"), true));
    }
}
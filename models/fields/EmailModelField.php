<?php


namespace app\models\fields;


use app\core\ModelField;

class EmailModelField extends ModelField
{

    /**
     * TextModelField constructor.
     * @param string $name
     * @param string|null $verbose
     */
    public function __construct(string $name, string $verbose = null)
    {
        parent::__construct($name, 'string', $verbose)
            ->setMin(5)
            ->setMax(255);
        return $this;

    }

    public function fieldValidate($value)
    {
        $validation = filter_var($value, FILTER_VALIDATE_EMAIL) ? true : false;
        if (!$validation)
            $this->addErrorMessage("This field should be valid email address");
        return $validation;
    }
}
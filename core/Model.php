<?php


namespace app\core;


use app\models\fields\IntegerModelField;

abstract class Model
{
    public array $errors = [];
    public bool $isFormValid = true;
    protected Database $db;
    protected Session $session;
    public IntegerModelField $id;

    public function __construct()
    {
        $this->db = Application::$app->db;
        $this->id = new IntegerModelField($name = 'id', $verbose = "ID");
        $this->session = Application::$app->session;
    }

    public function loadData(array $data)
    {
        $this->isFormValid = true;
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if (!$this->validate($key, $value)) {
                    $this->isFormValid = false;
                }
            }
        }
        $this->formValidation();
    }

    public function currentUserID()
    {
        return Application::$app->user->id->getValue();
    }


    private function validate($key, $value)
    {
        $field = $this->{$key};
        $validation = $field->validate($value);
        if (!$validation) {
            $this->addErrors($field);
        }
        return $validation;
    }

    protected function addErrors($field)
    {
        $this->errors[$field->name] = array(
            "name" => $field->verbose,
            "messages" => $field->errors
        );
    }

    protected function formValidation()
    {
        return true;
    }

    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute)
    {
        return isset($this->errors[$attribute]["messages"][0]) ? $this->errors[$attribute]["messages"][0] : "";
    }

    public static function loadAttributeValuesToArray(array $attributes)
    {
        $keyValues = array();
        foreach ($attributes as $attribute) {
            $keyValues[$attribute->name] = $attribute->getValue();
        }
        return $keyValues;
    }

    protected function setProperties($record)
    {
        foreach ($record as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key->setValue($value);
            }
        }
    }
}
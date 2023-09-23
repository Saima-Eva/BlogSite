<?php


namespace app\core;


abstract class ModelField
{
    public string $name;
    private ?string $value = null;
    private ?string $dbValue = null;
    protected ?string $default = null;
    private string $type;
    public string $verbose;
    protected ?int $min = null;
    protected ?int $max = null;
    protected bool $required = false;
    protected $uniqueMethod;
    public array $errors = [];

    /**
     * ModelField constructor.
     * @param string $name
     * @param string $type
     * @param string|null $verbose
     */
    public function __construct(string $name, string $type, string $verbose)
    {
        $this->name = $name;
        $this->type = $type;

        settype($this->value, $this->type);
        settype($this->default, $this->type);
        $this->verbose = !is_null($verbose) ? $verbose : $this->name;

        return $this;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function validate($value)
    {
        $validation = ($this->required) ? $this->validateRequired($value) : true;
        $value = $this->convert($value);
        $validation &= (!in_array($this->type, ['integer', 'float'])) ? $this->validateMinMax($value) : $this->validateNumberMinMax($value);
        $validation &= $this->fieldValidate($value);
        if ($validation)
            $this->setValue($value);
//        if ($validation)
//            $validation &= $this->validateUnique();
        return $validation;

    }

    public function getVerboseLower()
    {
        return strtolower($this->verbose) ?? "";
    }

    private function validateRequired($value)
    {
        $message = "This field is required";
        if ($this->type == 'string' && !$value)
            $is_value = false;
        elseif (in_array($this->type, ['integer', 'float']) && $value === '')
            $is_value = false;
        else
            $is_value = true;

        if (!$is_value) {
            $this->addErrorMessage($message);
        }
        return $is_value;
    }

    public function fieldValidate($value)
    {
        return true;
    }

    public function validateUnique(): bool
    {
        $isUnique = $this->getUniqueMethod() ? call_user_func($this->getUniqueMethod()) : true;
        if (!$isUnique) {
            $this->addErrorMessage("This {$this->getVerboseLower()} already exists");
        }
        return $isUnique;
    }

    protected function convert($value)
    {
        return trim($value);
    }

    protected function validateMinMax($value)
    {
        $message = "";
        $validation = true;
        if (!is_null($this->min) && !is_null($this->max)) {
            $validation = strlen($value) >= $this->min && strlen($value) <= $this->max;
            if (!$validation)
                $message = "$this->verbose value can be {$this->min} to {$this->max} characters";
        } elseif (!is_null($this->min)) {
            $validation = strlen($value) >= $this->min;
            if (!$validation)
                $message = "$this->verbose value should be at least {$this->min} characters";
        } elseif (!is_null($this->max)) {
            $validation = strlen($value) <= $this->max;
            if (!$validation)
                $message = "$this->verbose value should be maximum {$this->max} characters";
        }
        if (!$validation) {
            $this->addErrorMessage($message);
        }
        return $validation;
    }

    protected function validateNumberMinMax($value)
    {
        $message = "";
        $validation = true;

        if (!is_null($this->min) && !is_null($this->max)) {
            $validation = $value >= $this->min && $value <= $this->max;
            if (!$validation)
                $message = "ModelField value can be {$this->min} to {$this->max}";
        } elseif (!is_null($this->min)) {
            $validation = $value >= $this->min;
            if (!$validation)
                $message = "ModelField value should be at least {$this->min}";
        } elseif (!is_null($this->max)) {
            $validation = $value <= $this->max;
            if (!$validation)
                $message = "ModelField value should be maximum {$this->max}";
        }
        if (!$validation) {
            $this->addErrorMessage($message);
        }
        return $validation;
    }

    public function addErrorMessage($message)
    {
        array_push($this->errors, $message);
        Application::$app->session->setMessage("danger", $this->verbose, $message);
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
        $this->setDbValue($value);
    }

    /**
     * @param string|null $default
     */
    public function setDefault(?string $default)
    {
        $this->setValue($default);
        $this->default = $default;
        return $this;
    }

    /**
     * @param int|null $min
     */
    public function setMin(?int $min)
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param int|null $max
     */
    public function setMax(?int $max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDbValue(): ?string
    {
        return $this->dbValue;
    }

    /**
     * @param string|null $dbValue
     */
    public function setDbValue(?string $dbValue): void
    {
        $this->dbValue = $dbValue;
    }

    /**
     * @return mixed
     */
    public function getUniqueMethod()
    {
        return $this->uniqueMethod;
    }

    /**
     * @param $callable
     */
    public function setUniqueMethod($callable): ModelField
    {
        $this->uniqueMethod = $callable;
        return $this;
    }



}
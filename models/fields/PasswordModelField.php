<?php


namespace app\models\fields;


use app\core\ModelField;

class PasswordModelField extends ModelField
{
    private ?string $savedPassword = null;
    /**
     * TextModelField constructor.
     * @param string $name
     * @param string|null $verbose
     */
    public function __construct(string $name, string $verbose = null)
    {
        parent::__construct($name, 'string', $verbose)
            ->setMin(8)
            ->setMax(50);
        return $this;
    }

    public function __toString()
    {
        return "$this->verbose value is private";
    }
    public function match($confirmPassword)
    {
        $validation = false;
        if ($this->getPassword() && $confirmPassword->getDbValue()){
            $validation = $this->verify($confirmPassword->getDbValue());

            if (!$validation)
                $this->addErrorMessage("$confirmPassword->verbose didn't match");
        }
        return $validation;
    }

    public function verify(string $password)
    {
        return password_verify($this->getPassword(), $password);
    }

    public function fieldValidate($value)
    {
        return true;
    }

    /**
     * @param string|null $dbValue
     */
    public function setDbValue(?string $dbValue): void
    {
        parent::setDbValue(password_hash($dbValue, PASSWORD_DEFAULT));
    }

    private function getPassword()
    {
        return parent::getValue();
    }

    public function getValue(): ?string
    {
        return null;
    }


}
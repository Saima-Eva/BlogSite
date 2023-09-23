<?php


namespace app\models\fields;


use app\core\ModelField;

class DateTimeModelField extends ModelField
{

    /**
     * TextModelField constructor.
     * @param string $name
     * @param string|null $verbose
     */
    public function __construct(string $name, string $verbose = null)
    {
        parent::__construct($name, 'string', $verbose);
        return $this;
    }

    public function getValue(): ?string
    {
        $dateTime = date_create_from_format('Y-m-d H:i:s', parent::getValue());
        return date_format($dateTime , "F j, Y \a\\t g:i:s A");
    }

    public function getDbValue(): ?string
    {
        return parent::getValue();
    }


}
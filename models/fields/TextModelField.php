<?php


namespace app\models\fields;


use app\core\ModelField;

class TextModelField extends ModelField
{

    /**
     * TextModelField constructor.
     * @param string $name
     * @param string|null $verbose
     */
    public function __construct(string $name, string $verbose = null)
    {
        parent::__construct($name, 'string', $verbose)
            ->setMin(1)
            ->setMax(100);
        return $this;
    }

    public function htmlContent()
    {
        return html_entity_decode($this->getValue());
    }

    public function htmlText($limit=null)
    {
        $text = strip_tags($this->htmlContent());
        if (!is_null($limit)){
            $text = substr($text, 0, $limit);
        }
        return $text;
    }


}
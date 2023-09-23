<?php


namespace app\core;


/**
 * Class FormField
 * @package app\core
 */
class FormField
{
    public Model $model;
    public string $attribute;
    public string $type;
    public array $extra;
    public bool $labelVisibility = true;

    public function __construct(Model $model, string $attribute, string $type, array $extra=[], $labelVisibility=true)
    {
        $this->model = $model;
        $this->attribute = $attribute;
        $this->type = $type;
        $this->extra = $extra;
        $this->labelVisibility = $labelVisibility;
    }

    public function __toString()
    {
        if ($this->type=="textarea")
            return $this->renderTextarea();
        else
            return $this->renderInput();
    }

    public function renderInput()
    {
        $model = $this->model;
        $attribute = $this->attribute;
        $field = $model->{$attribute};
        $labelVisibility = $this->labelVisibility ? "" : 'style="display: none"';
        $extra = implode(" ", array_map(fn($e)=> "$e='{$this->extra[$e]}'" , array_keys($this->extra)));

        return sprintf('<div class="form-floating mb-3">
            
            <input type="%s" name="%s" class="form-control %s"
                   id="%s" value="%s" %s>
            <label for="%s"  %s>%s</label>
            <div class="invalid-feedback">
                %s
            </div>
        </div>',
            $this->type,
            $attribute,
            $model->hasError($attribute)? 'is-invalid' : "",
            $attribute,
            $field->getValue(),
            $extra,
            $attribute,
            $labelVisibility,
            $field->verbose,
            $model->getFirstError($this->attribute)
        ) ;
    }

    private function renderTextarea()
    {
        $model = $this->model;
        $attribute = $this->attribute;
        $field = $model->{$attribute};
        $extra = implode(" ", array_map(fn($e)=> "$e='{$this->extra[$e]}'" , array_keys($this->extra)));
        $labelVisibility = $this->labelVisibility ? "" : 'style="display: none"';

        return sprintf('<div class="mb-3">
            <label for="%s" class="form-label" %s>%s</label>
            <textarea name="%s" class="form-control %s"
                   id="%s" %s>%s</textarea>
            <div class="invalid-feedback">
                %s
            </div>
        </div>',
            $attribute,
            $labelVisibility,
            $field->verbose,
            $attribute,
            $model->hasError($attribute)? 'is-invalid' : "",
            $attribute,
            $extra,
            $field->getValue(),
            $model->getFirstError($this->attribute)
        ) ;
    }


}
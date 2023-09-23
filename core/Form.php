<?php


namespace app\core;


class Form
{
    public static function begin($action, $method)
    {
        echo sprintf('<form action="%s" method="%s" class="text-left"> ', $action, $method);
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field(Model $model, $attribute, $type, $extra=[], $labelVisibility=true)
    {
        return new FormField($model, $attribute, $type, $extra, $labelVisibility);
    }

    public function button($text="Submit", $name="", $style="primary", $type="submit", $extra_class="", $value="")
    {
        return "<button type='$type' class='btn btn-$style $extra_class' name='$name' value='$value'>$text</button>";
    }
}
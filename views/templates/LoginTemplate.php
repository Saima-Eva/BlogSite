<?php

use app\core\Form;

$form = Form::begin('', 'post');
$model = $model ?? null;

echo $form->field($model, 'email', 'email');
echo $form->field($model, 'password', 'password');
echo $form->button("Login")  ;
Form::end();
?>




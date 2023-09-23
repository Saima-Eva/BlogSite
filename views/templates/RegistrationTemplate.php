<?php

use app\core\Form;

$form = Form::begin('', 'post');
$model = $model ?? null;
?>
<div class="row">
    <div class="col"><?php echo $form->field($model, 'firstname', 'text'); ?></div>
    <div class="col"><?php echo $form->field($model, 'lastname', 'text'); ?></div>
</div>
<?php
echo $form->field($model, 'email', 'text');
echo $form->field($model, 'password', 'password');
echo $form->field($model, 'confirmPassword', 'password');
echo '<button type="submit" class="btn btn-primary">Sign Up</button>';
Form::end();
?>


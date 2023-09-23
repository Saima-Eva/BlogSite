<?php

use app\core\Application;
use app\core\Form;
?>


<div class="row ">
    <div class="col-12">
        <?php
        $form = Form::begin('', 'post');
        $model = $model ?? null;

        echo $form->field($model, 'title', 'text');
        ?>
        <div class="form-floating mb-3">
            <select class="form-control form-select mb-3" id="category" name="category" aria-label="Select Category">
                <?php
                echo "<option value=''>Select Category</option>";
                foreach (Application::$app->category->categoryArray as $k => $v) {
                    $selected = $model->category->getValue()==$k ? "selected" : "";
                    echo "<option $selected value='$k'>$v</option>";
                } ?>
            </select>
            <label for="category">Category</label>
        </div>

        <?php

        echo $form->field($model, 'content', 'textarea', ["rows"=>30], false);
        echo $form->button("Save", "save", "primary");
        echo $form->button("Publish", "publish", "success", "submit", "mx-2");
        Form::end();
        ?>
    </div>
</div>


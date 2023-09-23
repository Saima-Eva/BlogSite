<?php

use app\core\Form;
use app\models\PostModel;

$model = $model ?? null;
$fields = ["id", "category_key", "category_name"];

?>

<button class="btn btn-primary my-3" onclick='openModalAdd(this)'>Add New</button>

<table id="postModerationTable" class="table table-striped table-bordered" style="width:100%">
    <thead>
    <?php
    foreach ($fields as $field) {
        if (property_exists($model, $field)) {
            if ($field == "author") {
                echo "<th>Author</th>";
            } else {
                echo "<th >{$model->$field->verbose}</th>";
            }
        }
    }
    ?>
    <th>Action</th>
    </thead>
    <tbody>
    <?php
    foreach ($model->categoryList as $post) {
        echo "<tr>";
        foreach ($fields as $field) {
            if (property_exists($post, $field)) {
                if (in_array($field, ["status", "role"])) {
                    echo "<td>{$post->$field->getName()}</td>";
                } elseif ($field == "author") {
                    echo "<td><a href='/profile/{$post->$field->id->getValue()}'>{$post->$field->getFullName()}</a></td>";
                } else {
                    echo "<td>{$post->$field}</td>";
                }
            }
        }
        echo "<td><a class='btn btn-sm btn-outline-primary m-1' onclick='openModal(this)'>Edit</a></td>";
        echo "</tr>";
    }
    ?>
    </tbody>

</table>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo $view->getTitle();?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <?php
                    $form = new Form();
                    foreach ($fields as $field) {
                        if (property_exists($model, $field)) {
                            $extra = $field=="id" ? ['readonly'=>'readonly'] : [];
                            echo $form->field($model, $field, "text", $extra)   ;
                        }
                    } ?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger"  name="delete">Delete</button>
                    <button type="submit" class="btn btn-primary" name="save">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

</script>
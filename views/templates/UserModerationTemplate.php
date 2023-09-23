<?php

use app\core\Application;

$model = $model ?? null;
$fields = ["id", "firstname", "lastname", "status", "role", "created_at"];

?>
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
    foreach ($model->userList as $post) {
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
                    <div class="form-floating mb-3">
                        <input type="text" name="id" class="form-control " id="id" readonly="readonly">
                        <label for="id">ID</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-control form-select" id="status" name="status" aria-label="Select Status">
                            <?php foreach ($model->status->statusList as $k => $v) {
                                echo "<option value='$k'>$v</option>";
                            } ?>
                        </select>
                        <label for="status">Status</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-control form-select" id="role" name="role" aria-label="Select Status">
                            <?php foreach ($model->role->statusList as $k => $v) {
                                echo "<option value='$k'>$v</option>";
                            } ?>
                        </select>
                        <label for="role">User Role</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

</script>
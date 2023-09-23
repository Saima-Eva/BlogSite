<?php

use app\core\Application;
$session = Application::$app->session;
?>
<div aria-live="polite" aria-atomic="true" class="position-relative">
    <div class="toast-container position-absolute top-0 end-0 p-3">
        <?php
        foreach ($session->getMessageKeys() as $key){
            $message = $session->getMessage($key);

            if ($message) {
                echo <<<EOS
        <div class="toast toast-{$message['type']} fade hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-{$message['type']} text-white">
                <strong class="me-auto">{$message['title']}</strong>
                <small class="text-white">{$message['time']}</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">{$message['message']}</div>
        </div>
EOS;
            }
        }?>

    </div>
</div>



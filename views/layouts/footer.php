<?php

$authors = \app\core\Application::$app->getAuthors();

?>

<footer class="blog-footer">
    <p><b><?php echo SITE_NAME ?></b> built for Web Technology (MITE-305) Project, <a href="http://www.iit.du.ac.bd/">IIT, DU</a> by
        <?php
            $authorLinks = [];
            foreach ($authors as $author){
                $url = strlen($author['email'])?  "mailto:{$author['email']}" : $author['homepage'];
                array_push($authorLinks, "<a href='$url '>{$author['name']}</a>");
            }
            echo implode(", ", $authorLinks);
        ?>
    </p>


</footer>

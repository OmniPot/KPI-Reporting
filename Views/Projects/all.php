<?php /** @var Medieval\ViewModels\ProjectsViewModel $model */ ?>

<div>
    <?php foreach ( $model->getProjects() as $project ) {
        echo "<p>" . $project[ 'name' ] . "</p>";
    } ?>
</div>

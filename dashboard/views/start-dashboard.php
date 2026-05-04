<?php ob_start() ?>
<article>
    <div id="main" class="container">
        <h3>My Dashboard</h3>
        <div class="row">
            <?php
            if ($_SESSION["status"] == 'artist'): ?> 
            <p>Artist Panel</p>
            <?php else: ?>
             <p>User Panel</p>
            <?php endif; ?>
        </div>
    </div>
</article>

<?php $content = ob_get_clean(); ?>
<?php include "views/templates/layout.php"; ?>
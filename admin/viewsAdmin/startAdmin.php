<?php ob_start() ?>
<article>
    <div id="main" class="container">
        <h3>Admin Panel</h3>
        <div class="row">
            <p>Admin Panel</p>
        </div>
    </div>
</article>

<?php $content = ob_get_clean(); ?>
<?php include "viewsAdmin/templates/layout.php"; ?>
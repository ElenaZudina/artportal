<?php
ob_start();
?>

<div class="container" style="min-height:400px;">
    <div class="col-mid-11">
        <h2>Category Add</h2>
        <?php
        if(isset($test)) {
            if($test==true)
            {
                ?>
                <div class="alert alert-ifo">
                    <strong>Record added successfully. </strong><a href="categories">Go to category list</a>
                </div>
                <?php
            }
            else if($test==false)
            {
                ?>
                <div class="alert alert-warning">
                    <strong><?php echo !empty($errorMessage) ? htmlspecialchars($errorMessage) : 'Error adding record!'; ?> </strong><a href="categories">Go to category list</a>
                </div>
                <?php
            }
        }
        else {
            ?>
            <form method='POST' action="store" enctype="multipart/form-data">
                <table class='table table-bordered'>
                    <tr>
                        <td>Category title</td>
                        <td><input type='text' name='name' class='form-control' required></td>
                    </tr>

                     <tr>
                        <td colspan="2">
                            <button type="submit" class="btn btn-primary" name="save">
                        <span class="glyphicon glyphicon-plus"></span> Save
                        </button>
                        <a href="categories" class="btn btn-large btn-success">
                            <i class="glyphicon glyphicon-backward"></i> &nbsp;Back to list
                        </a>
                        </td>
                     </tr>
                </table>
            </form>
            <?php
        }
        ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include "views/templates/layout.php"; ?>


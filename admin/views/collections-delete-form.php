<?php
/**
 * Collection Delete Form View
 * Displays confirmation form for deleting a collection in admin panel
 */
ob_start();
?>

<?php $collection = $collection ?? []; ?>

<!-- Main content: Delete collection form -->
<div class="container" style="min-height:400px;">
    <div class="col-md-11">
        <h2>Collection Delete</h2>
        <?php
        if(isset($test)) {
            if($test==true)
            {
                ?>
                <div class="alert alert-info">
                    <strong>The entry has been deleted. </strong><a href="collections">Go to collection list</a>
                </div>
                <?php
            }
            else if($test==false)
            {
                ?>
                <div class="alert alert-warning">
                    <strong><?php echo !empty($errorMessage) ? htmlspecialchars($errorMessage) : 'Error deleting entry!'; ?> </strong><a href="collections">Go to collection list</a>
                </div>
                <?php
            }
        }
        else {
            ?>
            <form method='POST' action="result-delete-collection?id=<?php echo (int)($collection['id'] ?? 0); ?>">
                <table class='table table-bordered'>
                    <tr>
                        <td>Collection title</td>
                        <td><input type='text' name='title' class='form-control' required readonly value="<?php echo htmlspecialchars($collection['title'] ?? ''); ?>"></td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <button type="submit" class="btn btn-primary" name="save">
                                <span class="fa-solid fa-trash"></span> Delete
                            </button>
                            <a href="collections" class="btn btn-lg btn-success">
                                <i class="fa-solid fa-arrow-left"></i> &nbsp;Back to collection list
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

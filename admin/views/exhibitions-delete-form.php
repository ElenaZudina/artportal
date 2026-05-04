<?php
ob_start();
?>

<?php $exhibition = $exhibition ?? []; ?>

<div class="container" style="min-height:400px;">
    <div class="col-md-11">
        <h2>Exhibition Delete</h2>
        <?php
        if(isset($test)) {
            if($test==true)
            {
                ?>
                <div class="alert alert-info">
                    <strong>The entry has been deleted. </strong><a href="exhibitions">Go to exhibitions list</a>
                </div>
                <?php
            }
            else if($test==false)
            {
                ?>
                <div class="alert alert-warning">
                    <strong><?php echo !empty($errorMessage) ? htmlspecialchars($errorMessage) : 'Error deleting entry!'; ?> </strong><a href="exhibitions">Go to exhibitions list</a>
                </div>
                <?php
            }
        }
        else {
            ?>
            <form method='POST' action="result-delete-exhibition?id=<?php echo (int)($exhibition['id'] ?? 0); ?>">
                <table class='table table-bordered'>
                    <tr>
                        <td>Exhibition title</td>
                        <td><input type='text' name='title' class='form-control' required readonly value="<?php echo htmlspecialchars($exhibition['title'] ?? ''); ?>"></td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <button type="submit" class="btn btn-primary" name="save">
                                <span class="fa-solid fa-trash"></span> Delete
                            </button>
                            <a href="exhibitions" class="btn btn-lg btn-success">
                                <i class="fa-solid fa-arrow-left"></i> &nbsp;Back to exhibitions list
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

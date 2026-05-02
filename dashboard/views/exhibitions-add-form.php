<?php
ob_start();
?>

<?php $collections = $collections ?? []; ?>

<div class="container" style="min-height:400px;">
    <div class="col-md-11">
        <h2>Exhibition Add</h2>
        <?php
        if(isset($test)) {
            if($test==true)
            {
                ?>
                <div class="alert alert-info">
                    <strong>Record added successfully. </strong><a href="exhibitions">Go to exhibitions list</a>
                </div>
                <?php
            }
            else if($test==false)
            {
                ?>
                <div class="alert alert-warning">
                    <strong><?php echo !empty($errorMessage) ? htmlspecialchars($errorMessage) : 'Error adding record!'; ?> </strong><a href="exhibitions">Go to exhibitions list</a>
                </div>
                <?php
            }
        }
        else {
            if (empty($collections)) {
                ?>
                <div class="alert alert-warning">
                    <strong>No collections available. </strong>
                    <a href="create-collection">Create a collection first</a>
                </div>
                <?php
            }
            else {
                ?>
                <form method='POST' action="store-exhibition">
                    <table class='table table-bordered'>
                        <tr>
                            <td>Exhibition title</td>
                            <td><input type='text' name='title' class='form-control' required></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><textarea name='description' class='form-control' rows='4'></textarea></td>
                        </tr>
                        <tr>
                            <td>Collection</td>
                            <td>
                                <select name="collection_id" class="form-select" required>
                                    <option value="">Select collection</option>
                                    <?php foreach ($collections as $collection): ?>
                                        <option value="<?php echo (int)$collection['id']; ?>"><?php echo htmlspecialchars($collection['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Start Date</td>
                            <td><input type='datetime-local' name='start_date' class='form-control' required></td>
                        </tr>
                        <tr>
                            <td>End Date</td>
                            <td><input type='datetime-local' name='end_date' class='form-control' required></td>
                        </tr>

                         <tr>
                            <td colspan="2">
                                <button type="submit" class="btn btn-primary" name="save">
                            <span class="fa-solid fa-plus"></span> Save
                            </button>
                            <a href="exhibitions" class="btn btn-lg btn-success">
                                <i class="fa-solid fa-arrow-left"></i> &nbsp;Back to list
                            </a>
                            </td>
                         </tr>
                    </table>
                </form>
                <?php
            }
        }
        ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include "views/templates/layout.php"; ?>
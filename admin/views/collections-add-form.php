<?php
ob_start();
?>

<div class="container" style="min-height:400px;">
    <div class="col-md-11">
        <h2>Collection Add</h2>
        <?php
        if(isset($test)) {
            if($test==true)
            {
                ?>
                <div class="alert alert-info">
                    <strong>Record added successfully. </strong><a href="collections">Go to collections list</a>
                </div>
                <?php
            }
            else if($test==false)
            {
                ?>
                <div class="alert alert-warning">
                    <strong><?php echo !empty($errorMessage) ? htmlspecialchars($errorMessage) : 'Error adding record!'; ?> </strong><a href="collections">Go to collections list</a>
                </div>
                <?php
            }
        }
        else {
            ?>
            <form method='POST' action="store-collection" enctype="multipart/form-data">
                <table class='table table-bordered'>
                    <tr>
                        <td>Collection title</td>
                        <td><input type='text' name='title' class='form-control' required></td>
                    </tr>
                    <tr>
                        <td>Collection type</td>
                        <td>
                            <select name="type" class="form-select" id="collectionType" required>
                                <option value="">Select type</option>
                                <option value="keyword">keyword</option>
                                <option value="latest">latest</option>
                                <option value="random">random</option>
                                <option value="popular">popular</option>
                                <option value="ai">ai</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Param</td>
                        <td>
                            <input type='text' name='param' class='form-control' id="collectionParam" data-ai-placeholder="e.g. lazy autumn day">
                            <div class="form-text d-none" id="aiParamHint">For AI collections, enter short keywords separated by spaces.</div>
                        </td>
                    </tr>

                     <tr>
                        <td colspan="2">
                            <button type="submit" class="btn btn-primary" name="save">
                        <span class="fa-solid fa-plus"></span> Save
                        </button>
                        <a href="collections" class="btn btn-lg btn-success">
                            <i class="fa-solid fa-arrow-left"></i> &nbsp;Back to list
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

<script>
(function () {
    var typeSelect = document.getElementById('collectionType');
    var paramInput = document.getElementById('collectionParam');
    var hint = document.getElementById('aiParamHint');

    if (!typeSelect || !paramInput || !hint) {
        return;
    }

    var aiPlaceholder = paramInput.getAttribute('data-ai-placeholder') || '';

    function toggleHint() {
        var isAi = typeSelect.value === 'ai';
        hint.classList.toggle('d-none', !isAi);
        paramInput.setAttribute('placeholder', isAi ? aiPlaceholder : '');
    }

    typeSelect.addEventListener('change', toggleHint);
    toggleHint();
})();
</script>
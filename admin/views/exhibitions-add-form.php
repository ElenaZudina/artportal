<?php
/**
 * Exhibition Add Form View
 * Displays form for adding a new exhibition in admin panel
 */
ob_start();
?>

<?php $collections = $collections ?? []; ?>

<!-- Main content: Add exhibition form -->
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
                    <!-- Кнопка для создания коллекции через модальное окно -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCollectionModal">
                        <i class="fa-solid fa-plus"></i> Create collection
                    </button>
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
                                <div class="d-flex gap-2">
                                    <select name="collection_id" id="collectionSelect" class="form-select" required>
                                        <option value="">Select collection</option>
                                        <?php foreach ($collections as $collection): ?>
                                            <option value="<?php echo (int)$collection['id']; ?>"><?php echo htmlspecialchars($collection['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <!-- Кнопка для создания новой коллекции через модальное окно -->
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createCollectionModal">
                                        <i class="fa-solid fa-plus"></i> New
                                    </button>
                                </div>
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

<!-- ===================== МОДАЛЬНОЕ ОКНО ===================== -->
<!-- Bootstrap модальное окно для создания коллекции -->
<div class="modal fade" id="createCollectionModal" tabindex="-1" aria-labelledby="createCollectionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Заголовок модали -->
            <div class="modal-header">
                <h5 class="modal-title" id="createCollectionLabel">Create New Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Тело модали с формой -->
            <div class="modal-body">
                <!-- Контейнер для вывода ошибок -->
                <div id="collectionErrorAlert" class="alert alert-danger" style="display: none;"></div>

                <!-- Контейнер для вывода успеха -->
                <div id="collectionSuccessAlert" class="alert alert-success" style="display: none;"></div>

                <!-- Форма создания коллекции -->
                <form id="createCollectionForm" method="POST" action="store-collection">
                    <!-- Название коллекции (обязательное) -->
                    <div class="mb-3">
                        <label for="collectionTitle" class="form-label">Collection Title *</label>
                        <input type="text" class="form-control" id="collectionTitle" name="title" required>
                    </div>

                    <!-- Тип коллекции (обязательное) -->
                    <div class="mb-3">
                        <label for="collectionType" class="form-label">Collection Type *</label>
                        <select class="form-select" id="collectionType" name="type" required>
                            <option value="">Select type</option>
                            <option value="keyword">Keyword</option>
                            <option value="latest">Latest</option>
                            <option value="random">Random</option>
                            <option value="popular">Popular</option>
                        </select>
                    </div>

                    <!-- Параметр коллекции (опционально) -->
                    <div class="mb-3">
                        <label for="collectionParam" class="form-label">Parameter (optional)</label>
                        <input type="text" class="form-control" id="collectionParam" name="param" placeholder="e.g., search keyword">
                    </div>
                </form>
            </div>

            <!-- Футер модали с кнопками -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCollectionBtn">Save Collection</button>
            </div>
        </div>
    </div>
</div>

<!-- ===================== JAVASCRIPT ===================== -->
<script src="/artportal/public/js/create-collection.js"></script>

<?php $content = ob_get_clean(); ?>
<?php include "views/templates/layout.php"; ?>
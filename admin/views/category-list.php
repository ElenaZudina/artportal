<?php
ob_start();
?>

<h2>Categories List</h2>

<div class="container" style="min-height:400px;">
    <div style="margin:20px;">
        <a class="btn btn-primary" href="create-category" role="button">Add category</a>
    </div>
    <div class="col-md-11">
        <div class="table-responsive">
        <table class='table table-bordered'>
            <tr>
                <th width="10%">ID</th>
                <th width="70%">Category</th>
                <th width="20%"></th>
            </tr>
            <?php
            foreach($arr as $row) {
                echo '<tr>';
                echo '<td>'.$row['id'].'</td> ';
                echo '<td>';
                echo $row['name'];
                //echo '<br><b>Artist: </b><i>'.$row['artist_name'].'</i>';

                echo '</td>';
                echo'<td>
                <a href="edit-category?id='.$row['id'].'">Edit <i class="fa fa-pen" aria-hidden="true"></i></a>
                <a href="delete-category?id='.$row['id'].'">Delete <i class="fa fa-trash" aria-hidden="true"></i></a>
                </td>';
                echo '</tr>';
            }
            ?>
        </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php include "views/templates/layout.php"; ?>
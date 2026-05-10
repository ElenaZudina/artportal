<?php
ob_start();
?>
<h1>All paintings </h1>
<br>

<?php
ViewPaintings::PaintingsGrid($arr, false, false);

if (!empty($pagination) && ($pagination['totalPages'] ?? 0) > 1) {
	$currentPage = (int)$pagination['currentPage'];
	$totalPages = (int)$pagination['totalPages'];

	echo '<nav class="paintings-pagination mt-5" aria-label="Paintings pagination">';
	echo '<div class="pagination-controls">';

	$prevPage = $currentPage - 1;
	$nextPage = $currentPage + 1;

	// Previous button
	if ($currentPage > 1) {
		echo '<a class="pagination-btn pagination-btn--nav" href="all?page=' . $prevPage . '" aria-label="Previous page">← Prev</a>';
	} else {
		echo '<span class="pagination-btn pagination-btn--nav pagination-btn--disabled" aria-disabled="true">← Prev</span>';
	}

	// Page numbers
	echo '<div class="pagination-pages">';
	$maxVisible = 5;
	$startPage = max(1, $currentPage - 2);
	$endPage = min($totalPages, $startPage + $maxVisible - 1);
	
	if ($startPage > 1) {
		echo '<a class="pagination-page" href="all?page=1">1</a>';
		if ($startPage > 2) {
			echo '<span class="pagination-dots">...</span>';
		}
	}

	for ($page = $startPage; $page <= $endPage; $page++) {
		$activeClass = $page === $currentPage ? ' pagination-page--active' : '';
		echo '<a class="pagination-page' . $activeClass . '" href="all?page=' . $page . '" ' . ($page === $currentPage ? 'aria-current="page"' : '') . '>' . $page . '</a>';
	}

	if ($endPage < $totalPages) {
		if ($endPage < $totalPages - 1) {
			echo '<span class="pagination-dots">...</span>';
		}
		echo '<a class="pagination-page" href="all?page=' . $totalPages . '">' . $totalPages . '</a>';
	}
	echo '</div>';

	// Next button
	if ($currentPage < $totalPages) {
		echo '<a class="pagination-btn pagination-btn--nav" href="all?page=' . $nextPage . '" aria-label="Next page">Next →</a>';
	} else {
		echo '<span class="pagination-btn pagination-btn--nav pagination-btn--disabled" aria-disabled="true">Next →</span>';
	}

	echo '</div>';
	echo '</nav>';
}

$content = ob_get_clean();
include_once 'views/layout.php';

?>
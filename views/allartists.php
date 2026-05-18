<?php
/**
 * All Artists Page View
 * Shows searchable and paginated list of approved artists
 */
ob_start();
?>
<h1>Meet our artists </h1>
<br>

<?php
?>
<div class="d-flex justify-content-end mb-2">
	<form class="artist-search-form" method="get" action="artists">
		<div class="input-group artist-search-group">
			<span class="input-group-text artist-search-icon" aria-hidden="true"><i class="fa fa-search"></i></span>
			<input type="text" name="search" class="form-control artist-search-input" placeholder="Search artists by name, location or bio" value="<?php echo htmlspecialchars($searchQuery ?? '', ENT_QUOTES, 'UTF-8'); ?>">
			<button type="submit" class="btn artist-search-btn">Search</button>
			<?php if (!empty($searchQuery)): ?>
				<a href="artists" class="btn btn-outline-secondary artist-search-clear">Clear</a>
			<?php endif; ?>
		</div>
	</form>
</div>

<?php
ViewArtists::ArtistsGrid($arr, true);

if (!empty($pagination) && ($pagination['totalPages'] ?? 0) > 1) {
	$currentPage = (int)$pagination['currentPage'];
	$totalPages = (int)$pagination['totalPages'];
	$querySuffix = !empty($searchQuery) ? '&search=' . urlencode((string)$searchQuery) : '';

	echo '<nav class="paintings-pagination mt-5" aria-label="Artists pagination">';
	echo '<div class="pagination-controls">';

	$prevPage = $currentPage - 1;
	$nextPage = $currentPage + 1;

	if ($currentPage > 1) {
		echo '<a class="pagination-btn pagination-btn--nav" href="artists?page=' . $prevPage . $querySuffix . '" aria-label="Previous page">← Prev</a>';
	} else {
		echo '<span class="pagination-btn pagination-btn--nav pagination-btn--disabled" aria-disabled="true">← Prev</span>';
	}

	echo '<div class="pagination-pages">';
	$maxVisible = 5;
	$startPage = max(1, $currentPage - 2);
	$endPage = min($totalPages, $startPage + $maxVisible - 1);

	if ($startPage > 1) {
		echo '<a class="pagination-page" href="artists?page=1' . $querySuffix . '">1</a>';
		if ($startPage > 2) {
			echo '<span class="pagination-dots">...</span>';
		}
	}

	for ($page = $startPage; $page <= $endPage; $page++) {
		$activeClass = $page === $currentPage ? ' pagination-page--active' : '';
		echo '<a class="pagination-page' . $activeClass . '" href="artists?page=' . $page . $querySuffix . '" ' . ($page === $currentPage ? 'aria-current="page"' : '') . '>' . $page . '</a>';
	}

	if ($endPage < $totalPages) {
		if ($endPage < $totalPages - 1) {
			echo '<span class="pagination-dots">...</span>';
		}
		echo '<a class="pagination-page" href="artists?page=' . $totalPages . $querySuffix . '">' . $totalPages . '</a>';
	}
	echo '</div>';

	if ($currentPage < $totalPages) {
		echo '<a class="pagination-btn pagination-btn--nav" href="artists?page=' . $nextPage . $querySuffix . '" aria-label="Next page">Next →</a>';
	} else {
		echo '<span class="pagination-btn pagination-btn--nav pagination-btn--disabled" aria-disabled="true">Next →</span>';
	}

	echo '</div>';
	echo '</nav>';
}

$content = ob_get_clean();
include_once 'views/layout.php';

?>
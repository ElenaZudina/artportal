/**
 * Create Collection Modal
 * Handles AJAX submission for creating new collections in modals
 */
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // Get required DOM elements
    const createCollectionForm = document.getElementById('createCollectionForm');
    const saveCollectionBtn = document.getElementById('saveCollectionBtn');
    const collectionErrorAlert = document.getElementById('collectionErrorAlert');
    const collectionSuccessAlert = document.getElementById('collectionSuccessAlert');
    const collectionSelect = document.getElementById('collectionSelect');
    const createCollectionModal = document.getElementById('createCollectionModal');

    // Bail out if required elements don't exist
    if (!createCollectionForm || !saveCollectionBtn) {
        return;
    }

    // Handle "Save Collection" button click
    saveCollectionBtn.addEventListener('click', function(e) {
        e.preventDefault();

        // Hide old error/success messages
        if (collectionErrorAlert) {
            collectionErrorAlert.style.display = 'none';
        }
        if (collectionSuccessAlert) {
            collectionSuccessAlert.style.display = 'none';
        }

        // Validate form
        if (!createCollectionForm.checkValidity()) {
            if (collectionErrorAlert) {
                collectionErrorAlert.textContent = 'Please fill in all required fields';
                collectionErrorAlert.style.display = 'block';
            }
            return;
        }

        // Collect form data
        const formData = new FormData(createCollectionForm);

        // Send AJAX request to server
        fetch('store-collection-ajax', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Check if response is valid JSON
            if (!response.ok) {
                throw new Error('Network error: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Handle server response
            if (data.success) {
                // Collection created successfully

                // Show success message
                if (collectionSuccessAlert) {
                    collectionSuccessAlert.textContent = data.message || 'Collection created successfully!';
                    collectionSuccessAlert.style.display = 'block';
                }

                // Reset form
                createCollectionForm.reset();

                // Add new collection to select (if it exists)
                if (collectionSelect) {
                    const newOption = document.createElement('option');
                    newOption.value = data.id;
                    newOption.textContent = data.title;
                    collectionSelect.appendChild(newOption);

                    // Auto-select the new collection
                    collectionSelect.value = data.id;
                }

                // Close modal after 1.5 seconds
                setTimeout(() => {
                    if (createCollectionModal && window.bootstrap && window.bootstrap.Modal) {
                        const modal = bootstrap.Modal.getInstance(createCollectionModal);
                        if (modal) {
                            modal.hide();
                        }
                    }
                }, 1500);
            } else {
                // Error creating collection

                // Show error message
                if (collectionErrorAlert) {
                    collectionErrorAlert.textContent = data.message || 'Error creating collection';
                    collectionErrorAlert.style.display = 'block';
                }
            }
        })
        .catch(error => {
            // Handle network or JSON parsing errors
            console.error('Error:', error);
            if (collectionErrorAlert) {
                collectionErrorAlert.textContent = 'Network error. Please try again.';
                collectionErrorAlert.style.display = 'block';
            }
        });
    });

    // Fallback: if JavaScript is disabled, form works through regular POST
    // This is achieved through method="POST" and action="store-collection" attributes
});

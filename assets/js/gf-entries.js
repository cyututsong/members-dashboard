jQuery(document).ready(function($) {
    function showLoading(container) {
        container.find('.gf-entries-table-wrapper').html(
            '<div class="gf-loading"><span class="spinner"></span> Loading...</div>'
        );
    }

    function loadEntries(container, page = 1, search = '') {
        var formId = container.data('form-id');

        // Show loader
        showLoading(container);

        $.post(gfEntriesAjax.ajaxurl, {
            action: 'gf_get_entries',
            form_id: formId,
            page: page,
            search: search
        }, function(response) {
            if (response.success) {
                container.find('.gf-entries-table-wrapper').html(response.data.html);
            } else {
                container.find('.gf-entries-table-wrapper').html('<p><em>Error loading entries.</em></p>');
            }
        }).fail(function() {
            container.find('.gf-entries-table-wrapper').html('<p><em>Server error. Please try again.</em></p>');
        });
    }

    $('.gf-entries-container').each(function() {
        var container = $(this);

        // Initial load
        loadEntries(container);

        // Pagination click
        container.on('click', '.gf-page-link', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            var search = container.find('.gf-search-input').val();
            loadEntries(container, page, search);
        });

        // Search button click
        container.on('click', '.gf-search-btn', function() {
            var search = container.find('.gf-search-input').val();
            loadEntries(container, 1, search);
        });

        // Search on Enter key
        container.on('keypress', '.gf-search-input', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                var search = $(this).val();
                loadEntries(container, 1, search);
            }
        });
    });
});

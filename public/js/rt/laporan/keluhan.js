$(document).ready(function() {
    // Initialize DataTable with modern styling
    var table = $('#keluhanTable').DataTable({
        responsive: true,
        info: false,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            },
            emptyTable: '<div class="text-center p-4"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><p>Tidak ada keluhan yang ditemukan</p></div>',
            zeroRecords: '<div class="text-center p-4"><i class="fas fa-search fa-3x text-muted mb-3"></i><p>Tidak ada hasil yang ditemukan</p></div>'
        },
        columnDefs: [
            { orderable: false, targets: 5 } // Disable sorting on action column
        ],
        order: [[0, 'asc']], // Sort by first column (No) by default
        dom: '<"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        lengthMenu: [5, 10, 25, 50],
        pageLength: 10,
        drawCallback: function(settings) {
            var api = this.api();
            var pageInfo = api.page.info();
            
            // Update pagination info text
            $('#paginationInfo').html('Menampilkan ' + (pageInfo.start + 1) + ' - ' + pageInfo.end + ' dari ' + pageInfo.recordsTotal + ' keluhan');
            
            // Add animation to rows
            $('tbody tr').each(function(index) {
                $(this).css('animation-delay', (index * 0.05) + 's');
                $(this).addClass('animate-fade-in');
            });
        }
    });

    // Custom search functionality with debounce
    var searchTimeout;
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        var value = $(this).val();
        
        searchTimeout = setTimeout(function() {
            // Add loading indicator
            if (!$('.search-spinner').length && value.length > 0) {
                $('.input-group-text').html('<i class="fas fa-spinner fa-spin"></i>');
            }
            
            // Perform search
            table.search(value).draw();
            
            // Restore search icon
            setTimeout(function() {
                $('.input-group-text').html('<i class="fas fa-search"></i>');
            }, 300);
        }, 400);
    });

    // Focus animation for search input
    $('#searchInput').on('focus', function() {
        $(this).parent().parent().addClass('search-focused');
    }).on('blur', function() {
        $(this).parent().parent().removeClass('search-focused');
    });
});

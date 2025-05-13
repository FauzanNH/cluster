$(document).ready(function() {
    // Initialize DataTable with advanced features
    var table = $('#satpamTable').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRowImmediate,
                type: 'column',
                renderer: function(api, rowIdx, columns) {
                    var data = $.map(columns, function(col, i) {
                        return col.hidden ?
                            '<li class="dtr-data-row">' +
                            '<span class="dtr-title">' + col.title + '</span> ' +
                            '<span class="dtr-data">' + col.data + '</span>' +
                            '</li>' :
                            '';
                    }).join('');

                    return data ?
                        $('<ul class="dtr-details"/>').append(data) :
                        false;
                }
            }
        },
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [
            {
                extend: 'collection',
                text: '<i class="fas fa-download"></i> Export',
                className: 'btn-sm btn-outline-secondary',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="far fa-file-excel"></i> Excel',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="far fa-file-pdf"></i> PDF',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        className: 'dropdown-item'
                    }
                ]
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i>',
                className: 'btn-sm btn-outline-secondary',
                columns: [1, 2, 3, 4, 5, 6]
            }
        ],
        columnDefs: [
            { 
                responsivePriority: 1, 
                targets: [1] // Nama is most important
            },
            { 
                responsivePriority: 2, 
                targets: [2, 7] // NIK and Actions next most important
            },
            { 
                responsivePriority: 3, 
                targets: [0, 6] // No and unit gerbang
            },
            { 
                responsivePriority: 4, 
                targets: [3, 5] // Domisili and No KEP
            },
            { 
                responsivePriority: 5, 
                targets: 4 // Tanggal Lahir least important
            },
            { 
                orderable: false, 
                targets: 7 
            },
            { 
                searchable: false, 
                targets: [0, 7] 
            }
        ],
        order: [[1, 'asc']], // Sort by name by default
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        pageLength: 10,
        autoWidth: false
    });
    
    // Add the export buttons to the DataTable
    table.buttons().container().appendTo('#satpamTable_wrapper .col-md-6:eq(0)');
    
    // Add custom search box (enhanced search)
    $('#satpamTable_filter input').attr('placeholder', 'Cari data satpam...');
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Fix Bootstrap 5 compatibility issues with DataTables
    $(document).on('shown.bs.modal', function() {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    // Handle add form submission
    $('#saveSatpam').on('click', function() {
        // You can add form validation and AJAX submission here
        var isValid = validateSatpamForm();
        
        if (isValid) {
            // For now, just close the modal
            $('#addSatpamModal').modal('hide');
            
            // You would typically do an AJAX call here to save the data
            // After successful save, you can refresh the table or add a new row
        }
    });

    // Modal closing reset form
    $('#addSatpamModal').on('hidden.bs.modal', function() {
        $('#addSatpamForm')[0].reset();
        // Remove validation errors
        $('.is-invalid').removeClass('is-invalid');
    });

    // Handle edit button click
    $(document).on('click', '.btn-warning', function() {
        // Get the data from the row for editing
        // This would typically populate a modal form with data
    });

    // Handle delete button click
    $(document).on('click', '.btn-danger', function() {
        // Confirm deletion
        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            // Handle deletion via AJAX
        }
    });

    // Handle view details button click
    $(document).on('click', '.btn-info', function() {
        // Show details in a modal or redirect to details page
    });
    
    // Adjust for mobile view - improve responsive behavior
    function adjustForMobile() {
        if (window.innerWidth < 768) {
            // Make sure dropdown menu doesn't get cut off
            $('.dt-button-collection').css('max-height', (window.innerHeight * 0.7) + 'px');
            $('.dt-button-collection').css('overflow-y', 'auto');
            
            // Improve spacing in mobile view
            $('.dataTables_filter, .dataTables_length').css('margin-bottom', '15px');
            
            // Force redraw of table to ensure responsive behavior works correctly
            table.responsive.recalc();
        }
    }
    
    // Run on page load and resize
    adjustForMobile();
    $(window).on('resize', adjustForMobile);

    // Handler untuk tombol hapus dan ganti password satpam
    var usersIdToDelete = null;
    var usersIdToPassword = null;

    $(document).on('click', '.btn-danger[title="Hapus"]', function() {
        console.log('Tombol hapus diklik');
        usersIdToDelete = $(this).closest('tr').find('.btn-detail-satpam').data('users_id');
        $('#modalHapusSatpam').modal('show');
    });

    $(document).on('click', '.btn-warning[title="Ganti Password"]', function() {
        console.log('Tombol ganti password diklik');
        usersIdToPassword = $(this).closest('tr').find('.btn-detail-satpam').data('users_id');
        $('#formPasswordSatpam')[0].reset();
        $('#modalPasswordSatpam').modal('show');
    });
});

// Form validation function
function validateSatpamForm() {
    var isValid = true;
    
    // Get form inputs
    var nama = $('#nama').val();
    var nik = $('#nik').val();
    var domisili = $('#domisili').val();
    var tanggalLahir = $('#tanggal_lahir').val();
    var noKep = $('#no_kep').val();
    var seksiUnit = $('#seksi_unit').val();
    
    // Clear previous validation errors
    $('.is-invalid').removeClass('is-invalid');
    
    // Basic validation
    if (!nama) {
        isValid = false;
        // Add error class or message
        $('#nama').addClass('is-invalid');
    } else {
        $('#nama').removeClass('is-invalid');
    }
    
    if (!nik || nik.length !== 16) {
        isValid = false;
        $('#nik').addClass('is-invalid');
    } else {
        $('#nik').removeClass('is-invalid');
    }
    
    if (!domisili) {
        isValid = false;
        $('#domisili').addClass('is-invalid');
    } else {
        $('#domisili').removeClass('is-invalid');
    }
    
    if (!tanggalLahir) {
        isValid = false;
        $('#tanggal_lahir').addClass('is-invalid');
    } else {
        $('#tanggal_lahir').removeClass('is-invalid');
    }
    
    if (!noKep) {
        isValid = false;
        $('#no_kep').addClass('is-invalid');
    } else {
        $('#no_kep').removeClass('is-invalid');
    }
    
    if (!seksiUnit) {
        isValid = false;
        $('#seksi_unit').addClass('is-invalid');
    } else {
        $('#seksi_unit').removeClass('is-invalid');
    }
    
    return isValid;
}

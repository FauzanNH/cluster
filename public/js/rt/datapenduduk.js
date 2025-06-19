$(document).ready(function() {
    // Initialize DataTable with advanced features
    var table = $('#pendudukTable').DataTable({
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
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="far fa-file-pdf"></i> PDF',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        className: 'dropdown-item'
                    }
                ]
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i>',
                className: 'btn-sm btn-outline-secondary',
                columns: [1, 2, 3, 4, 5]
            }
        ],
        columnDefs: [
            { 
                responsivePriority: 1, 
                targets: [1] // Nama is most important
            },
            { 
                responsivePriority: 2, 
                targets: [2, 6] // NIK and Actions next most important
            },
            { 
                responsivePriority: 3, 
                targets: [0, 5] // No and Gender
            },
            { 
                responsivePriority: 4, 
                targets: [3] // Domisili KTP
            },
            { 
                responsivePriority: 5, 
                targets: 4 // Tanggal Lahir least important
            },
            { 
                orderable: false, 
                targets: 6 
            },
            { 
                searchable: false, 
                targets: [0, 6] 
            }
        ],
        order: [[1, 'asc']], // Sort by name by default
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        pageLength: 10,
        autoWidth: false
    });
    
    // Add the export buttons to the DataTable
    table.buttons().container().appendTo('#pendudukTable_wrapper .col-md-6:eq(0)');
    
    // Add custom search box (enhanced search)
    $('#pendudukTable_filter input').attr('placeholder', 'Cari data penduduk...');
    
    // Initialize tooltips for Bootstrap 5
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Fix Bootstrap 5 compatibility issues with DataTables
    $(document).on('shown.bs.modal', function() {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    // ===== File Input Handling =====
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Pilih file...');
    });

    // ===== Form Submission =====
    $('#savePenduduk').on('click', function() {
        // Form validation
        var isValid = validatePendudukForm();
        
        if (isValid) {
            // Close the modal
            $('#addPendudukModal').modal('hide');
            
            // Submit form handled by form action now
        }
    });

    // ===== Modal Close Handling =====
    // Add Penduduk Modal reset on close
    $('#addPendudukModal').on('hidden.bs.modal', function() {
        $('#addPendudukForm')[0].reset();
        // Reset file input label
        $('.custom-file-label').html('Pilih file...');
        // Remove validation errors
        $('.is-invalid').removeClass('is-invalid');
        // Reset NIK/KK validation
        $('#nikHelp, #nokkHelp').removeClass('text-danger').addClass('form-text text-muted');
        $('#savePenduduk').prop('disabled', false);
    });
    
    // Show modal if there are validation errors
    if ($('.alert-danger').length > 0) {
        $('#addPendudukModal').modal('show');
        
        // Highlight fields with errors
        $('.alert-danger ul li').each(function() {
            var errorText = $(this).text();
            if (errorText.toLowerCase().includes('nik')) {
                $('#nik').addClass('is-invalid');
                $('#nikHelp').text('NIK ini sudah terdaftar dalam sistem!').removeClass('text-muted').addClass('text-danger');
            }
            if (errorText.toLowerCase().includes('no kk')) {
                $('#nokk').addClass('is-invalid');
                $('#nokkHelp').text('No KK ini sudah terdaftar untuk warga lain!').removeClass('text-muted').addClass('text-danger');
            }
        });
    }
    
    // Close buttons in modals - Updated for Bootstrap 5
    $('.modal .btn-secondary').on('click', function() {
        var modalId = $(this).closest('.modal').attr('id');
        var myModal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        myModal.hide();
    });

    // ===== Detail View Handling =====
    // Handle view details button click
    $(document).on('click', '.view-details', function() {
        // Get data from button attributes
        var nama = $(this).data('nama');
        var nik = $(this).data('nik');
        var domisili = $(this).data('domisili');
        var tanggalLahir = $(this).data('tanggal-lahir');
        var gender = $(this).data('gender');
        var nokk = $(this).data('nokk');
        var status = $(this).data('status');
        var pekerjaan = $(this).data('pekerjaan');
        var agama = $(this).data('agama');
        var pendidikan = $(this).data('pendidikan');
        var linkFotoKtp = $(this).data('link-foto-ktp');
        var linkFotoKk = $(this).data('link-foto-kk');
        
        // Populate modal with data
        $('#detail-nama').text(nama);
        $('#detail-nik-value').text(nik);
        $('#detail-nokk-value').text(nokk);
        $('#detail-domisili').text(domisili);
        $('#detail-tanggal-lahir').text(tanggalLahir);
        $('#detail-gender').text(gender);
        $('#detail-status').text(status);
        $('#detail-pekerjaan').text(pekerjaan);
        $('#detail-agama').text(agama);
        $('#detail-pendidikan').text(pendidikan);
        
        // Apply gender-specific styling
        if (gender === 'Laki-laki') {
            $('#detail-gender').addClass('text-primary').removeClass('text-pink');
        } else if (gender === 'Perempuan') {
            $('#detail-gender').addClass('text-pink').removeClass('text-primary');
        }

        // Check NIK and KK length - highlight if invalid
        if (nik.length < 16) {
            $('#detail-nik-value').addClass('data-invalid');
        } else {
            $('#detail-nik-value').removeClass('data-invalid');
        }

        if (nokk.length < 16) {
            $('#detail-nokk-value').addClass('data-invalid');
        } else {
            $('#detail-nokk-value').removeClass('data-invalid');
        }
        
        // Set file links
        if (linkFotoKtp) {
            $('#link-foto-ktp').attr('href', linkFotoKtp);
            $('#link-foto-ktp').show();
        } else {
            $('#link-foto-ktp').hide();
        }
        
        if (linkFotoKk) {
            $('#link-foto-kk').attr('href', linkFotoKk);
            $('#link-foto-kk').show();
        } else {
            $('#link-foto-kk').hide();
        }
        
        // Show the modal - Bootstrap 5
        var detailModal = new bootstrap.Modal(document.getElementById('detailPendudukModal'));
        detailModal.show();
    });
    
    // ===== Edit from Detail Modal =====
    $(document).on('click', '.edit-from-detail', function() {
        // Close detail modal
        $('#detailPendudukModal').modal('hide');
        
        // Get data from detail modal to populate edit form
        var nama = $('#detail-nama').text();
        var nik = $('#detail-nik').text().replace('NIK: ', '');
        var nokk = $('#detail-nokk').text().replace('No. KK: ', '');
        var domisili = $('#detail-domisili').text();
        var tanggalLahir = $('#detail-tanggal-lahir').text();
        var gender = $('#detail-gender').text();
        var status = $('#detail-status').text();
        var pekerjaan = $('#detail-pekerjaan').text();
        var agama = $('#detail-agama').text();
        var pendidikan = $('#detail-pendidikan').text();
        
        // Convert date format if needed (from dd-mm-yyyy to yyyy-mm-dd for input date)
        var parts = tanggalLahir.split('-');
        if (parts.length === 3) {
            tanggalLahir = parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        
        // Populate edit form
        // In a real application, you would populate an edit form here
        // For demonstration, just log the data
        console.log("Edit data:", {
            nama: nama,
            nik: nik,
            nokk: nokk,
            domisili: domisili,
            tanggalLahir: tanggalLahir,
            gender: gender,
            status: status,
            pekerjaan: pekerjaan,
            agama: agama,
            pendidikan: pendidikan
        });
        
        // You could open an edit modal here or redirect to an edit page
    });

    // ===== Table Action Buttons =====
    // Handle edit button click
    $(document).on('click', '.btn-warning', function() {
        // Get the row data for editing
        var row = $(this).closest('tr');
        var data = table.row(row).data();
        
        // You would populate a modal form with this data
        // For demonstration purposes only:
        console.log("Edit row data:", data);
    });

    // ===== Table Styling and UI Improvements =====
    // Add highlight to gender column and check for invalid NIK/KK
    table.on('draw', function() {
        $('#pendudukTable tbody tr').each(function() {
            var genderCell = $(this).find('td:nth-child(7)');
            var gender = genderCell.text().trim();
            
            if (gender === 'Laki-laki') {
                genderCell.addClass('text-primary font-weight-bold');
            } else if (gender === 'Perempuan') {
                genderCell.addClass('text-pink font-weight-bold');
            }

            // Check NIK length and add class if needed
            var nikCell = $(this).find('td:nth-child(4)');
            var nik = nikCell.text().trim();
            if (nik.length < 16) {
                nikCell.addClass('data-invalid');
            }
        });
    });
    
    // ===== Mobile Responsiveness =====
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

    // Validasi real-time untuk NIK dan No KK
    $('#nik, #nokk').on('input', function() {
        validateNikAndKK();
    });

    function validateNikAndKK() {
        var nik = $('#nik').val();
        var nokk = $('#nokk').val();
        var isNikValid = nik.length === 16;
        var isNokkValid = nokk.length === 16;
        
        // Validate NIK
        if (!isNikValid && nik.length > 0) {
            $('#nik').addClass('is-invalid');
            $('#nikHelp').text('NIK harus 16 digit').removeClass('text-muted').addClass('text-danger');
        } else {
            $('#nik').removeClass('is-invalid');
            $('#nikHelp').text('Masukkan 16 digit NIK').removeClass('text-danger').addClass('text-muted');
        }
        
        // Validate No KK
        if (!isNokkValid && nokk.length > 0) {
            $('#nokk').addClass('is-invalid');
            $('#nokkHelp').text('No KK harus 16 digit').removeClass('text-muted').addClass('text-danger');
        } else {
            $('#nokk').removeClass('is-invalid');
            $('#nokkHelp').text('Masukkan 16 digit No KK').removeClass('text-danger').addClass('text-muted');
        }
        
        // Disable/enable submit button based on NIK & KK validation
        // Only if there are values entered (to allow form to be opened without disabling immediately)
        if ((nik.length > 0 && !isNikValid) || (nokk.length > 0 && !isNokkValid)) {
            $('#savePenduduk').prop('disabled', true);
        } else {
            $('#savePenduduk').prop('disabled', false);
        }
    }

    // ===== Edit Data Penduduk =====
    $(document).on('click', '.btn-edit', function() {
        // Reset error messages setiap kali tombol edit diklik
        $('#edit_nik').removeClass('is-invalid');
        $('#editNikHelp').removeClass('text-danger').addClass('text-muted').text('Masukkan 16 digit NIK');
        $('#edit_nokk').removeClass('is-invalid');
        $('#editNokkHelp').removeClass('text-danger').addClass('text-muted').text('Masukkan 16 digit No KK');
        
        var wargaId = $(this).data('warga-id');
        $.get('/rt/datapenduduk/' + wargaId + '/edit', function(data) {
            // Isi field modal edit
            $('#edit_nama').val(data.nama);
            $('#edit_nik').val(data.nik);
            $('#edit_nokk').val(data.no_kk);
            $('#edit_domisili').val(data.domisili_ktp);
            $('#edit_tanggal_lahir').val(data.tanggal_lahir);
            $('#edit_gender').val(data.gender);
            $('#edit_status').val(data.status_pernikahan);
            $('#edit_pekerjaan').val(data.pekerjaan);
            $('#edit_pendidikan').val(data.pendidikan_terakhir);
            $('#edit_agama').val(data.agama);
            // Set action form
            $('#editPendudukForm').attr('action', '/rt/datapenduduk/' + wargaId + '/update');
            // Tampilkan modal
            var editModal = new bootstrap.Modal(document.getElementById('editPendudukModal'));
            editModal.show();
            
            // Cek apakah ada error validasi setelah submit form edit
            if ($('.alert-danger').length > 0) {
                $('.alert-danger ul li').each(function() {
                    var errorText = $(this).text();
                    if (errorText.toLowerCase().includes('nik')) {
                        $('#edit_nik').addClass('is-invalid');
                        $('#editNikHelp').text('NIK ini sudah terdaftar untuk warga lain!').removeClass('text-muted').addClass('text-danger');
                    }
                    if (errorText.toLowerCase().includes('no kk')) {
                        $('#edit_nokk').addClass('is-invalid');
                        $('#editNokkHelp').text('No KK ini sudah terdaftar untuk warga lain!').removeClass('text-muted').addClass('text-danger');
                    }
                });
            }
        });
    });

    // Reset edit form on close
    $('#editPendudukModal').on('hidden.bs.modal', function() {
        $('#editPendudukForm')[0].reset();
        // Remove validation errors
        $('.is-invalid').removeClass('is-invalid');
        // Reset NIK/KK validation
        $('#editNikHelp, #editNokkHelp').removeClass('text-danger').addClass('form-text text-muted');
    });

    // ===== Delete Data Penduduk =====
    $(document).on('click', '.btn-delete', function() {
        var form = $(this).closest('form');
        if (confirm('Apakah Anda yakin ingin menghapus data penduduk ini?')) {
            form.submit();
        }
    });

    // ===== Real-time Form Validation for Required Fields =====
    function checkRequiredFields() {
        var isComplete = true;
        // Cek semua field required (kecuali foto_ktp jika belum punya KTP)
        $('#addPendudukForm [required]').each(function() {
            // Untuk file foto_ktp, pengecekan dilakukan di bawah
            if ($(this).attr('id') === 'foto_ktp') return;
            if (!$(this).val() || $(this).val() === '') {
                isComplete = false;
                return false; // break
            }
        });
        // Cek khusus untuk foto KTP
        var belumPunyaKtp = $('#belum_punya_ktp').is(':checked');
        var fotoKtp = $('#foto_ktp')[0].files[0];
        if (!belumPunyaKtp && !fotoKtp) {
            isComplete = false;
        }
        // Validasi NIK dan No KK harus 16 digit
        var nik = $('#nik').val();
        var nokk = $('#nokk').val();
        if (nik.length !== 16 || nokk.length !== 16) {
            isComplete = false;
        }
        $('#savePenduduk').prop('disabled', !isComplete);
    }

    // Trigger check on input/select change
    $('#addPendudukForm').on('input change', '[required]', function() {
        checkRequiredFields();
    });

    // Inisialisasi saat modal/form dibuka
    $('#addPendudukModal').on('shown.bs.modal', function() {
        checkRequiredFields();
    });

    // Tambahkan event listener untuk checkbox 'Belum punya KTP' agar validasi real-time berjalan
    $('#belum_punya_ktp').on('change', function() {
        checkRequiredFields();
    });
});

// ===== Form Validation =====
function validatePendudukForm() {
    var isValid = true;
    
    // Clear previous validation errors
    $('.is-invalid').removeClass('is-invalid');
    
    // Get form inputs
    var nama = $('#nama').val();
    var nik = $('#nik').val();
    var nokk = $('#nokk').val();
    var domisili = $('#domisili').val();
    var tanggalLahir = $('#tanggal_lahir').val();
    var gender = $('#gender').val();
    var status = $('#status').val();
    var pekerjaan = $('#pekerjaan').val();
    var agama = $('#agama').val();
    var pendidikan = $('#pendidikan').val();
    
    // Validate name
    if (!nama) {
        isValid = false;
        $('#nama').addClass('is-invalid');
    }
    
    // Validate NIK (16 digits)
    if (!nik || !/^\d{16}$/.test(nik)) {
        isValid = false;
        $('#nik').addClass('is-invalid');
    }
    
    // Validate No KK (16 digits)
    if (!nokk || !/^\d{16}$/.test(nokk)) {
        isValid = false;
        $('#nokk').addClass('is-invalid');
    }
    
    // Validate domisili KTP
    if (!domisili) {
        isValid = false;
        $('#domisili').addClass('is-invalid');
    }
    
    // Validate tanggal lahir
    if (!tanggalLahir) {
        isValid = false;
        $('#tanggal_lahir').addClass('is-invalid');
    }
    
    // Validate gender
    if (!gender) {
        isValid = false;
        $('#gender').addClass('is-invalid');
    }
    
    // Validate status pernikahan
    if (!status) {
        isValid = false;
        $('#status').addClass('is-invalid');
    }
    
    // Validate pekerjaan
    if (!pekerjaan) {
        isValid = false;
        $('#pekerjaan').addClass('is-invalid');
    }
    
    // Validate agama
    if (!agama) {
        isValid = false;
        $('#agama').addClass('is-invalid');
    }
    
    // Validate pendidikan
    if (!pendidikan) {
        isValid = false;
        $('#pendidikan').addClass('is-invalid');
    }
    
    // Check for file upload validation if needed
    var belumPunyaKtp = $('#belum_punya_ktp').is(':checked');
    var fotoKtp = $('#foto_ktp')[0].files[0];

    if (!belumPunyaKtp) {
        if (!fotoKtp) {
            isValid = false;
            $('#foto_ktp').addClass('is-invalid');
        } else {
            // Validasi file size dan type
            if (fotoKtp.size > 2 * 1024 * 1024) {
                isValid = false;
                $('#foto_ktp').addClass('is-invalid');
                alert('Ukuran file terlalu besar! Maksimal 2MB.');
            }
            var allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(fotoKtp.type)) {
                isValid = false;
                $('#foto_ktp').addClass('is-invalid');
                alert('Format file tidak didukung! Gunakan JPG, PNG, atau PDF.');
            }
        }
    } else {
        $('#foto_ktp').removeClass('is-invalid');
    }
    
    return isValid;
}
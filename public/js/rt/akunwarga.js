$(document).ready(function() {
    // Initialize DataTable
    var table = $('#akunWargaTable').DataTable({
        responsive: true,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                className: 'btn btn-success btn-sm mr-1',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                className: 'btn btn-danger btn-sm mr-1',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print mr-1"></i> Print',
                className: 'btn btn-primary btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });

    // Handle View Details button click
    $(document).on('click', '.view-details', function() {
        // Get data from button attributes
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var email = $(this).data('email');
        var nohp = $(this).data('nohp');
        var gender = $(this).data('gender');
        var role = $(this).data('role');
        var rtBlok = $(this).data('rt-blok');
        var createdAt = $(this).data('created-at');

        // Populate modal with data
        $('#detail-nama').text(nama);
        $('#detail-id').html('<span class="badge badge-info">ID:</span> ' + id);
        $('#detail-email').html('<span class="badge badge-info">Email:</span> ' + email);
        $('#detail-nohp').text(nohp);
        $('#detail-gender').text(gender);
        $('#detail-role').text(role);
        $('#detail-rt-blok').text(rtBlok);
        $('#detail-created-at').text(createdAt);

        // Show the modal
        $('#detailAkunWargaModal').modal('show');
    });

    // Handle Edit button click from detail modal
    $(document).on('click', '.edit-from-detail', function() {
        $('#detailAkunWargaModal').modal('hide');
        var id = $('#detail-id').text().replace('ID:', '').trim();
        // Ambil data user dari server
        $.get('/rt/akunwarga/' + id, function(user) {
            $('#edit-id').val(user.id);
            $('#edit-nama').val(user.nama);
            $('#edit-email').val(user.email);
            $('#edit-no_hp').val(user.no_hp);
            $('#edit-gender').val(user.gender);
            $('#edit-alamat').val(user.alamat);
            $('#edit-password').val('');
            // Set form action
            $('#editAkunWargaForm').attr('action', '/rt/akunwarga/' + id);
            $('#editAkunWargaModal').modal('show');
        });
    });

    // Handle Change Password button click
    $(document).on('click', '.change-password-btn', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        $('#change-password-id').val(id);
        $('#change-password-nama').val(nama);
        $('#change-password-password').val('');
        $('#change-password-password_confirmation').val('');
        $('#changePasswordForm').attr('action', '/rt/akunwarga/' + id + '/password');
        $('#changePasswordModal').modal('show');
    });

    // Konfirmasi sebelum hapus akun warga
    $(document).on('submit', '.delete-akunwarga-form', function(e) {
        if (!confirm('Yakin ingin menghapus akun warga ini?')) {
            e.preventDefault();
        }
    });
});

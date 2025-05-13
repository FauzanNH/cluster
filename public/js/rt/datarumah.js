$(document).ready(function() {
    // Initialize DataTable without export/print buttons
    var table = $('#rumahTable').DataTable({
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari data rumah...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang cocok",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Handler for view detail button (keep this)
    $(document).on('click', '.view-detail', function() {
        const id_rumah = $(this).data('id_rumah');
        const status_kepemilikan = $(this).data('status_kepemilikan');
        const alamat = $(this).data('alamat');
        $('#detail-id_rumah').text(id_rumah);
        $('#detail-status_kepemilikan').text(status_kepemilikan);
        $('#detail-alamat').text(alamat);
        $('#detailRumahModal').modal('show');
    });
}); 
// DataTables, SweetAlert, dan event handler modal detail/edit/simpan
// Setup CSRF token untuk semua AJAX request
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    // DataTables inisialisasi jika diperlukan (jika sebelumnya ada di datasatpam.js, bisa dipindahkan ke sini)
    if ($.fn.DataTable) {
        $('#satpamTable').DataTable();
    }
});

var currentUsersId = null;
$(document).on('click', '.btn-detail-satpam', function() {
    var users_id = $(this).data('users_id');
    currentUsersId = users_id;
    // Reset modal
    $('#detail-nik').text('-').show();
    $('#input-nik').val('').addClass('d-none');
    $('#detail-tanggal-lahir').text('-').show();
    $('#input-tanggal-lahir').val('').addClass('d-none');
    $('#detail-no-kep').text('-').show();
    $('#input-no-kep').val('').addClass('d-none');
    $('#detail-seksi-unit').text('-').show();
    $('#input-seksi-unit').val('').addClass('d-none');
    $('#btn-edit-satpam').show();
    $('#btn-simpan-satpam').addClass('d-none');
    // Ambil data detail via AJAX
    $.get('/rt/datasatpam/' + users_id + '/detail', function(data) {
        if (data.nik) {
            $('#detail-nik').text(data.nik);
            $('#input-nik').val(data.nik);
        } else {
            $('#detail-nik').html('<span class="text-danger">Belum di isi</span>');
            $('#input-nik').val('');
        }
        if (data.tanggal_lahir) {
            $('#detail-tanggal-lahir').text(data.tanggal_lahir);
            $('#input-tanggal-lahir').val(data.tanggal_lahir);
        } else {
            $('#detail-tanggal-lahir').html('<span class="text-danger">Belum di isi</span>');
            $('#input-tanggal-lahir').val('');
        }
        if (data.no_kep) {
            $('#detail-no-kep').text(data.no_kep);
            $('#input-no-kep').val(data.no_kep);
        } else {
            $('#detail-no-kep').html('<span class="text-danger">Belum di isi</span>');
            $('#input-no-kep').val('');
        }
        if (data.seksi_unit_gerbang) {
            $('#detail-seksi-unit').text(data.seksi_unit_gerbang);
            $('#input-seksi-unit').val(data.seksi_unit_gerbang);
        } else {
            $('#detail-seksi-unit').html('<span class="text-danger">Belum di isi</span>');
            $('#input-seksi-unit').val('');
        }
    }).fail(function() {
        $('#detail-nik').html('<span class="text-danger">Belum di isi</span>');
        $('#input-nik').val('');
        $('#detail-tanggal-lahir').html('<span class="text-danger">Belum di isi</span>');
        $('#input-tanggal-lahir').val('');
        $('#detail-no-kep').html('<span class="text-danger">Belum di isi</span>');
        $('#input-no-kep').val('');
        $('#detail-seksi-unit').html('<span class="text-danger">Belum di isi</span>');
        $('#input-seksi-unit').val('');
    });
    $('#modalDetailSatpam').modal('show');
});

$(document).on('click', '#btn-edit-satpam', function() {
    $('#detail-nik, #detail-tanggal-lahir, #detail-no-kep, #detail-seksi-unit').hide();
    $('#input-nik, #input-tanggal-lahir, #input-no-kep, #input-seksi-unit').removeClass('d-none');
    $('#btn-edit-satpam').hide();
    $('#btn-simpan-satpam').removeClass('d-none');
});

$(document).on('click', '#btn-simpan-satpam', function() {
    var formData = {
        nik: $('#input-nik').val(),
        tanggal_lahir: $('#input-tanggal-lahir').val(),
        no_kep: $('#input-no-kep').val(),
        seksi_unit_gerbang: $('#input-seksi-unit').val(),
        users_id: currentUsersId
    };
    $.ajax({
        url: '/rt/datasatpam/' + currentUsersId + '/simpan',
        method: 'POST',
        data: formData,
        success: function(res) {
            // Sembunyikan input, tampilkan value baru
            $('#detail-nik').text(formData.nik).show();
            $('#input-nik').addClass('d-none');
            $('#detail-tanggal-lahir').text(formData.tanggal_lahir).show();
            $('#input-tanggal-lahir').addClass('d-none');
            $('#detail-no-kep').text(formData.no_kep).show();
            $('#input-no-kep').addClass('d-none');
            $('#detail-seksi-unit').text(formData.seksi_unit_gerbang).show();
            $('#input-seksi-unit').addClass('d-none');
            $('#btn-edit-satpam').show();
            $('#btn-simpan-satpam').addClass('d-none');
            // Pop up berhasil
            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data satpam berhasil disimpan!',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                alert('Data satpam berhasil disimpan!');
            }
        },
        error: function(xhr) {
            alert('Gagal menyimpan data. Pastikan semua field terisi dengan benar.');
        }
    });
});

$(document).on('click', '.btn-danger[title="Hapus"]', function() {
    var usersIdToDelete = $(this).closest('tr').find('.btn-detail-satpam').data('users_id');
    window.usersIdToDelete = usersIdToDelete;
    $('#modalHapusSatpam').modal('show');
});
$(document).on('click', '.btn-warning[title="Ganti Password"]', function() {
    var usersIdToPassword = $(this).closest('tr').find('.btn-detail-satpam').data('users_id');
    window.usersIdToPassword = usersIdToPassword;
    $('#formPasswordSatpam')[0].reset();
    $('#modalPasswordSatpam').modal('show');
});

// Hapus Satpam
$(document).on('click', '#btn-confirm-hapus-satpam', function() {
    if (!window.usersIdToDelete) return;
    $.ajax({
        url: '/rt/datasatpam/' + window.usersIdToDelete + '/hapus',
        method: 'DELETE',
        success: function(res) {
            $('#modalHapusSatpam').modal('hide');
            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Akun satpam berhasil dihapus!',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => window.location.reload());
            } else {
                alert('Akun satpam berhasil dihapus!');
                window.location.reload();
            }
        },
        error: function(xhr) {
            Swal.fire('Gagal', 'Gagal menghapus akun satpam.', 'error');
        }
    });
});

// Ganti Password Satpam
$(document).on('click', '#btn-simpan-password-satpam', function() {
    var password = $('#password-baru').val();
    var password_confirmation = $('#password-konfirmasi').val();
    if (!password || password.length < 6) {
        Swal.fire('Gagal', 'Password minimal 6 karakter.', 'error');
        return;
    }
    if (password !== password_confirmation) {
        Swal.fire('Gagal', 'Konfirmasi password tidak cocok.', 'error');
        return;
    }
    $.ajax({
        url: '/rt/datasatpam/' + window.usersIdToPassword + '/password',
        method: 'PATCH',
        data: {
            password: password,
            password_confirmation: password_confirmation
        },
        success: function(res) {
            $('#modalPasswordSatpam').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Password satpam berhasil diubah!',
                timer: 1500,
                showConfirmButton: false
            });
        },
        error: function(xhr) {
            Swal.fire('Gagal', 'Gagal mengubah password satpam.', 'error');
        }
    });
});

// Tambah Data Satpam
$(document).on('click', '#btn-simpan-tambah-satpam', function() {
    var form = $('#formTambahSatpam');
    var data = {
        nama: $('#tambah-nama').val(),
        email: $('#tambah-email').val(),
        no_hp: $('#tambah-no_hp').val(),
        gender: $('#tambah-gender').val(),
        alamat: $('#tambah-alamat').val(),
        password: $('#tambah-password').val(),
        password_confirmation: $('#tambah-password_confirmation').val()
    };
    $.ajax({
        url: '/rt/datasatpam/register',
        method: 'POST',
        data: data,
        success: function(res) {
            $('#modalTambahSatpam').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Akun satpam berhasil ditambahkan!',
                timer: 1500,
                showConfirmButton: false
            }).then(() => window.location.reload());
        },
        error: function(xhr) {
            let msg = 'Gagal menambah akun satpam.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).map(e => e.join('<br>')).join('<br>');
            }
            Swal.fire('Gagal', msg, 'error');
        }
    });
});

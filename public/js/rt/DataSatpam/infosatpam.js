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
    
    // Update satpam counter
    updateSatpamCounter();
});

// Function to update the satpam counter
function updateSatpamCounter() {
    const satpamCount = $('#satpamTable tbody tr').not(':has(td[colspan])').length;
    const isMaxReached = satpamCount >= 8;
    
    $('.satpam-counter .badge')
        .removeClass('bg-info bg-danger')
        .addClass(isMaxReached ? 'bg-danger' : 'bg-info')
        .html(`<i class="fas fa-users me-1"></i> ${satpamCount}/8 Satpam`);
        
    if (isMaxReached) {
        $('.satpam-counter').append('<small class="text-danger ms-2">Batas maksimum tercapai</small>');
        $('#btn-tambah-satpam').prop('disabled', true);
    } else {
        $('.satpam-counter small').remove();
        $('#btn-tambah-satpam').prop('disabled', false);
    }
}

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

// ========== TOAST CUSTOM ========== //
function showToast(message, type = 'error') {
    let toast = $('#custom-toast');
    if (!toast.length) {
        // Jika container belum ada, buat otomatis
        toast = $('<div id="custom-toast"></div>').appendTo('body');
        toast.css({
            display: 'none',
            position: 'fixed',
            bottom: '30px',
            right: '30px',
            zIndex: 9999,
            minWidth: '250px',
            padding: '16px 24px',
            background: '#323232',
            color: '#fff',
            borderRadius: '8px',
            boxShadow: '0 2px 8px rgba(0,0,0,0.2)',
            fontSize: '16px',
            transition: 'transform 0.4s, opacity 0.4s',
            opacity: 0,
            transform: 'translateX(100%)'
        });
    }
    toast.removeClass('hide').addClass('show');
    toast.css('background', type === 'success' ? '#4caf50' : '#e74a3b');
    toast.html(message);
    toast.show().css({opacity: 1, transform: 'translateX(0)'});
    setTimeout(function() {
        toast.css({opacity: 0, transform: 'translateX(100%)'});
        setTimeout(function() {
            toast.hide();
        }, 400);
    }, 2500);
}
// ========== END TOAST CUSTOM ========== //

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
            showToast('Data satpam berhasil disimpan!', 'success');
        },
        error: function(xhr) {
            let pesan = 'Gagal menyimpan data. Pastikan semua field terisi dengan benar.';
            if (xhr.status === 422) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    // Cek isi pesan custom dari backend
                    if (xhr.responseJSON.message.toLowerCase().includes('email sudah digunakan')) {
                        pesan = 'Email sudah digunakan oleh user lain.';
                    } else if (xhr.responseJSON.message.toLowerCase().includes('no hp sudah digunakan')) {
                        pesan = 'No HP sudah digunakan oleh user lain.';
                    } else {
                        pesan = xhr.responseJSON.message;
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Tangani error default Laravel
                    if (xhr.responseJSON.errors.email && xhr.responseJSON.errors.email[0].toLowerCase().includes('already been taken')) {
                        pesan = 'Email sudah digunakan oleh user lain.';
                    } else if (xhr.responseJSON.errors.no_hp && xhr.responseJSON.errors.no_hp[0].toLowerCase().includes('already been taken')) {
                        pesan = 'No HP sudah digunakan oleh user lain.';
                    } else {
                        pesan = Object.values(xhr.responseJSON.errors)[0][0];
                    }
                }
            }
            showToast(pesan, 'error');
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
            showToast('Akun satpam berhasil dihapus!', 'success');
            // Remove the row from the table
            $(`button.btn-detail-satpam[data-users_id="${window.usersIdToDelete}"]`).closest('tr').remove();
            // Update the counter
            updateSatpamCounter();
            // If table is now empty, add the empty message row
            if ($('#satpamTable tbody tr').length === 0) {
                $('#satpamTable tbody').append('<tr><td colspan="7" class="text-center">Tidak ada data satpam.</td></tr>');
            }
            // Re-enable the add button if we're now under the limit
            if ($('#satpamTable tbody tr').not(':has(td[colspan])').length < 8) {
                $('#btn-tambah-satpam').prop('disabled', false);
                $('#modalTambahSatpam .alert-danger').remove();
                $('#formTambahSatpam').removeClass('disabled-form');
                $('#formTambahSatpam input, #formTambahSatpam select').prop('disabled', false);
                $('#btn-simpan-tambah-satpam').prop('disabled', false);
            }
        },
        error: function(xhr) {
            showToast('Gagal menghapus akun satpam.', 'error');
        }
    });
});

// Ganti Password Satpam
$(document).on('click', '#btn-simpan-password-satpam', function() {
    var password = $('#password-baru').val();
    var password_confirmation = $('#password-konfirmasi').val();
    if (!password || password.length < 6) {
        showToast('Password minimal 6 karakter.', 'error');
        return;
    }
    if (password !== password_confirmation) {
        showToast('Konfirmasi password tidak cocok.', 'error');
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
            showToast('Password satpam berhasil diubah!', 'success');
        },
        error: function(xhr) {
            showToast('Gagal mengubah password satpam.', 'error');
        }
    });
});

// Tambah Data Satpam
$(document).on('click', '#btn-simpan-tambah-satpam', function() {
    // Check if we've reached the maximum limit
    if ($('#satpamTable tbody tr').not(':has(td[colspan])').length >= 8) {
        showToast('Jumlah maksimal satpam (8 orang) telah tercapai.', 'error');
        return;
    }
    
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
    
    // Basic validation
    if (!data.nama || !data.email || !data.no_hp || !data.gender || !data.alamat || !data.password) {
        showToast('Semua field harus diisi.', 'error');
        return;
    }
    
    if (data.password.length < 6) {
        showToast('Password minimal 6 karakter.', 'error');
        return;
    }
    
    if (data.password !== data.password_confirmation) {
        showToast('Konfirmasi password tidak cocok.', 'error');
        return;
    }
    
    $.ajax({
        url: '/rt/datasatpam/register',
        method: 'POST',
        data: data,
        success: function(res) {
            $('#modalTambahSatpam').modal('hide');
            showToast('Akun satpam berhasil ditambahkan!', 'success');
            
            // Add the new row to the table
            if (res.data) {
                const newRow = `
                <tr>
                    <td class="text-center">${res.data.users_id}</td>
                    <td>${res.data.nama}</td>
                    <td>${res.data.email}</td>
                    <td>${res.data.alamat}</td>
                    <td>${res.data.no_hp}</td>
                    <td>${res.data.gender}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-info btn-sm btn-detail-satpam" data-users_id="${res.data.users_id}" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" title="Ganti Password">
                            <i class="fas fa-key"></i>
                        </button>
                    </td>
                </tr>`;
                
                // If table was empty, remove the empty message row
                if ($('#satpamTable tbody tr:has(td[colspan])').length) {
                    $('#satpamTable tbody').empty();
                }
                
                $('#satpamTable tbody').append(newRow);
                
                // Update the counter
                updateSatpamCounter();
                
                // If we've reached the limit, disable the add button
                if ($('#satpamTable tbody tr').not(':has(td[colspan])').length >= 8) {
                    $('#btn-tambah-satpam').prop('disabled', true);
                    $('#modalTambahSatpam .modal-body').prepend(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i> Jumlah maksimal satpam (8 orang) telah tercapai. Hapus data satpam yang tidak aktif terlebih dahulu.
                        </div>
                    `);
                    $('#formTambahSatpam').addClass('disabled-form');
                    $('#formTambahSatpam input, #formTambahSatpam select').prop('disabled', true);
                    $('#btn-simpan-tambah-satpam').prop('disabled', true);
                }
            } else {
                setTimeout(() => window.location.reload(), 1500);
            }
        },
        error: function(xhr) {
            let msg = 'Gagal menambah akun satpam.';
            
            if (xhr.status === 422 && xhr.responseJSON) {
                if (xhr.responseJSON.message && xhr.responseJSON.message.includes('maksimal satpam')) {
                    msg = xhr.responseJSON.message;
                    
                    // Update UI to reflect max limit reached
                    updateSatpamCounter();
                    
                    // Add warning to modal
                    if (!$('#modalTambahSatpam .alert-danger').length) {
                        $('#modalTambahSatpam .modal-body').prepend(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i> ${msg}
                            </div>
                        `);
                    }
                    
                    // Disable form
                    $('#formTambahSatpam').addClass('disabled-form');
                    $('#formTambahSatpam input, #formTambahSatpam select').prop('disabled', true);
                    $('#btn-simpan-tambah-satpam').prop('disabled', true);
                } else if (xhr.responseJSON.errors) {
                    // Handle validation errors
                    const firstError = Object.values(xhr.responseJSON.errors)[0][0];
                    msg = firstError;
                }
            } else if (xhr.status === 409 && xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            
            showToast(msg, 'error');
        }
    });
});

// Reset form when modal is closed
$('#modalTambahSatpam').on('hidden.bs.modal', function() {
    $('#formTambahSatpam')[0].reset();
});

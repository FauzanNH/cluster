$(document).ready(function() {
    // Toggle password visibility
    $('.toggle-password').on('click', function() {
        const targetId = $(this).data('target');
        const passwordInput = $('#' + targetId);
        const icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Password strength meter
    $('#new_password').on('input', function() {
        const password = $(this).val();
        let strength = 0;
        let progressClass = 'bg-danger';
        let strengthText = 'Sangat Lemah';
        
        if (password.length >= 8) strength += 1;
        if (password.match(/[a-z]/)) strength += 1;
        if (password.match(/[A-Z]/)) strength += 1;
        if (password.match(/[0-9]/)) strength += 1;
        if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
        
        switch (strength) {
            case 0:
            case 1:
                progressClass = 'bg-danger';
                strengthText = 'Sangat Lemah';
                break;
            case 2:
                progressClass = 'bg-warning';
                strengthText = 'Lemah';
                break;
            case 3:
                progressClass = 'bg-info';
                strengthText = 'Sedang';
                break;
            case 4:
                progressClass = 'bg-primary';
                strengthText = 'Kuat';
                break;
            case 5:
                progressClass = 'bg-success';
                strengthText = 'Sangat Kuat';
                break;
        }
        
        const percentage = (strength / 5) * 100;
        $('.password-strength .progress-bar')
            .removeClass('bg-danger bg-warning bg-info bg-primary bg-success')
            .addClass(progressClass)
            .css('width', percentage + '%')
            .attr('aria-valuenow', percentage);
        
        $('#password-strength-text').text(strengthText);
    });
    
    // Avatar preview
    $('#avatar-upload').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatar-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    // Simpan nilai awal profil
    let initialProfile = {
        nama: $('#nama').val(),
        email: $('#email').val(),
        no_hp: $('#no_hp').val(),
        rt_blok: $('#rt_blok').val()
    };

    // Simpan nilai awal password (kosongkan saja)
    let initialPassword = {
        current_password: '',
        new_password: '',
        new_password_confirmation: ''
    };

    // PROFILE TAB
    $('#edit-profile-btn').on('click', function() {
        $('#profile-form input').prop('disabled', false);
        $('#edit-profile-btn').addClass('d-none');
        $('#save-profile-btn').removeClass('d-none');
        $('#cancel-profile-btn').removeClass('d-none');
        // Tampilkan note blok RT jika field enable
        if (!$('#rt_blok').prop('disabled')) {
            $('.rt-blok-note').show();
        }
    });

    $('#cancel-profile-btn').on('click', function() {
        // Kembalikan nilai awal
        $('#nama').val(initialProfile.nama);
        $('#email').val(initialProfile.email);
        $('#no_hp').val(initialProfile.no_hp);
        $('#rt_blok').val(initialProfile.rt_blok);

        $('#profile-form input').prop('disabled', true);
        $('#edit-profile-btn').removeClass('d-none');
        $('#save-profile-btn').addClass('d-none');
        $('#cancel-profile-btn').addClass('d-none');
        // Sembunyikan note blok RT jika field disable
        $('.rt-blok-note').hide();
    });

    // Sembunyikan note saat load jika field disabled
    if ($('#rt_blok').prop('disabled')) {
        $('.rt-blok-note').hide();
    }

    // PASSWORD TAB
    $('#edit-password-btn').on('click', function() {
        $('#security-form input').prop('disabled', false);
        $('#edit-password-btn').addClass('d-none');
        $('#save-password-btn').removeClass('d-none');
        $('#cancel-password-btn').removeClass('d-none');
    });

    $('#cancel-password-btn').on('click', function() {
        // Kosongkan field password
        $('#current_password').val('');
        $('#new_password').val('');
        $('#new_password_confirmation').val('');
        $('#security-form input').prop('disabled', true);
        $('#edit-password-btn').removeClass('d-none');
        $('#save-password-btn').addClass('d-none');
        $('#cancel-password-btn').addClass('d-none');
        // Reset password strength bar
        $('.password-strength .progress-bar')
            .removeClass('bg-danger bg-warning bg-info bg-primary bg-success')
            .addClass('bg-danger')
            .css('width', '0%')
            .attr('aria-valuenow', 0);
        $('#password-strength-text').text('Belum diisi');
    });
});

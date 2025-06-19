<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f7fa; color: #333; margin: 0; padding: 0; }
        .email-wrapper { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: #1976d2; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .logo { max-height: 60px; margin-bottom: 10px; }
        .content { padding: 30px 25px; }
        .greeting { font-size: 18px; margin-bottom: 20px; font-weight: 500; }
        .message { font-size: 16px; line-height: 1.6; margin-bottom: 25px; }
        .otp-container { background: #f5f7fa; border-radius: 8px; padding: 20px; text-align: center; margin: 25px 0; }
        .otp-code { font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #1976d2; }
        .otp-info { font-size: 14px; color: #666; margin-top: 10px; }
        .warning { background: #fff8e1; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; font-size: 15px; }
        .footer { background: #f5f7fa; padding: 20px; text-align: center; font-size: 14px; color: #666; }
        .divider { height: 1px; background: #eee; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <h1>Bukit Asri Cluster</h1>
        </div>
        <div class="content">
            <div class="greeting">
                Yth. Bapak/Ibu {{ $nama }},
            </div>
            <div class="message">
                Anda baru saja meminta untuk melakukan reset password akun Bukit Asri Cluster.<br>
                Untuk melanjutkan proses, silakan masukkan kode OTP berikut pada aplikasi:
            </div>
            <div class="otp-container">
                <div class="otp-code">{{ $otp_code }}</div>
                <div class="otp-info">Kode OTP ini berlaku hingga {{ $expired_at }}</div>
            </div>
            <div class="warning">
                <strong>Perhatian:</strong> Jangan berikan kode OTP ini kepada siapapun termasuk staff atau admin Bukit Asri Cluster.<br>
                Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini dan pastikan keamanan akun Anda.
            </div>
            <div class="divider"></div>
            <div class="message">
                Terima kasih atas perhatiannya.<br><br>
                Hormat kami,<br>
                <strong>Manajemen Bukit Asri Cluster</strong>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Bukit Asri Cluster. Semua hak dilindungi.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html> 
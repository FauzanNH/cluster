// Fungsi untuk export ke Excel
function exportToExcel() {
    let table = document.getElementById("tabelKunjungan");
    let tanggal = document.getElementById("tanggal").value;
    
    // Format tanggal untuk nama file
    let formattedDate = new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).replace(/\//g, '-');
    
    let fileName = `Laporan_Tamu_${formattedDate}.xlsx`;
    
    // Membuat workbook baru
    let wb = XLSX.utils.table_to_book(table, {sheet: "Laporan Tamu"});
    
    // Menulis ke file dan download
    XLSX.writeFile(wb, fileName);
}

// Fungsi untuk export ke PDF
function exportToPDF() {
    let tanggal = document.getElementById("tanggal").value;
    let rtBlok = document.getElementById("rtBlok").value;
    
    // Format tanggal untuk nama file dan judul
    let formattedDate = new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
    
    let fileName = `Laporan_Tamu_RT_Blok_${rtBlok}_${tanggal.replace(/-/g, '_')}.pdf`;
    
    // Konfigurasi untuk html2pdf
    let element = document.getElementById('printArea');
    let opt = {
        margin: 1,
        filename: fileName,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'cm', format: 'a4', orientation: 'landscape' }
    };
    
    // Buat PDF dan download
    html2pdf().set(opt).from(element).save();
}

// Inisialisasi saat dokumen siap
document.addEventListener('DOMContentLoaded', function() {
    // Tambahkan event listener untuk tombol export jika ada
    const btnExcel = document.getElementById('btnExportExcel');
    if (btnExcel) {
        btnExcel.addEventListener('click', exportToExcel);
    }
    
    const btnPDF = document.getElementById('btnExportPDF');
    if (btnPDF) {
        btnPDF.addEventListener('click', exportToPDF);
    }
    
    const btnPrint = document.getElementById('btnPrint');
    if (btnPrint) {
        btnPrint.addEventListener('click', function() {
            window.print();
        });
    }
    
    // Inisialisasi flatpickr untuk input tanggal
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#tanggal", {
            dateFormat: "Y-m-d",
            maxDate: "today",
            locale: "id"
        });
    }
});

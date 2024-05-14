$(document).ready(function () {
  var oTable = $("#dataTable").dataTable({
    sScrollX: true, // Mengatur tinggi scroll vertikal tabel
  });

  $(window).on("resize", function () {
    oTable.fnAdjustColumnSizing(); // Menyesuaikan ukuran kolom saat ukuran jendela berubah
  });
});

var map = L.map("map").setView([-0.904553, 119.861121], 12);

// Tambahkan layer OpenStreetMap
L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
}).addTo(map);

// Tambahkan GeoJSON Layer
fetch("../assets/data/sulteng.geojson")
  .then((response) => response.json())
  .then((data) => {
    L.geoJSON(data).addTo(map);
  });

const info = L.control();

function onEachFeature(feature, layer) {
  layer.on({
    mouseover: showPopup,
    mouseout: hidePopup,
    click: zoomToFeature,
  });
}

function showPopup(e) {
  e.target.openPopup();
}

function hidePopup(e) {
  e.target.closePopup();
}

function zoomToFeature(e, lat, lng) {
  var bounds = L.latLng(lat, lng).toBounds(0.0001);
  map.fitBounds(bounds);
}

fetch("../assets/data/getData.php")
  .then((response) => response.json())
  .then((data) => {
    data.forEach((item) => {
      var latitude = parseFloat(item.titik_lokasi.latitude);
      var longitude = parseFloat(item.titik_lokasi.longitude);

      var skor = item.skor;
      var hasil = item.hasil;
      var tgl = item.tgl_pendataan;
      var surveyor = item.surveyor;
      var nama_desa = item.nama_desa;
      var nama_kecamatan = item.nama_kecamatan;
      var no_hp = item.no_hp;
      var nama_kepalakeluarga = item.nama_kepalakeluarga;
      var foto_sebelum = item.foto_sebelum;
      var foto_sesudah = item.foto_sesudah;

      var layak = L.icon({
        iconUrl: "../vendor/leaflet/images/layak-icon.png ",
        iconSize: [25, 41],
        iconAnchor: [13, 40],
        popupAnchor: [-3, -76],
      });

      var tidakLayak = L.icon({
        iconUrl: "../vendor/leaflet/images/tidak-layak-icon.png",
        iconSize: [25, 41],
        iconAnchor: [13, 40],
        popupAnchor: [-3, -76],
      });

      if (hasil === "LAYAK") {
        var marker = L.marker([latitude, longitude], { icon: layak })
          .addTo(map)
          .on("click", function (e) {
            zoomToFeature(e, latitude, longitude); // Panggil fungsi zoomToFeature dengan parameter yang sesuai
          })
          .on("mouseover", function (e) {
            showPopup(e); // Tampilkan pop-up saat hover pada marker
          })
          .on("mouseout", function (e) {
            hidePopup(e); // Sembunyikan pop-up saat kursor keluar dari marker
          });
      } else {
        var marker = L.marker([latitude, longitude], { icon: tidakLayak })
          .addTo(map)
          .on("click", function (e) {
            zoomToFeature(e, latitude, longitude); // Panggil fungsi zoomToFeature dengan parameter yang sesuai
          })
          .on("mouseover", function (e) {
            showPopup(e); // Tampilkan pop-up saat hover pada marker
          })
          .on("mouseout", function (e) {
            hidePopup(e); // Sembunyikan pop-up saat kursor keluar dari marker
          });
      }

      marker.bindPopup(/*html*/ `
        <div class="container ">
          <div class="row text-center">
            <div class="col-sm">
              <img src="../assets/data/file/${foto_sebelum}" class="card-img-top" alt="...">
              Before
            </div>
            <div class="col-sm">
              <img src="../assets/data/file/${foto_sesudah}" class="card-img-top" alt="...">
              After
            </div>
          </div>
        </div>
        <div class="card-body px-0 pb-0">
          <ul type="none">
            <li>Skor: ${skor}</li>
            <li>Hasil: ${hasil}</li>
            <li>Tanggal Pendataan: ${tgl}</li>
            <li>Surveyor: ${surveyor}</li>
            <li>Nama Desa: ${nama_desa}</li>
            <li>Nama Kecamatan: ${nama_kecamatan}</li>
            <li>No HP: ${no_hp}</li>
            <li>Nama Kepala Keluarga: ${nama_kepalakeluarga}</li>
          </ul>
        </div>
      `);

      var polygonGroup = L.layerGroup().addTo(map);

      // Tentukan warna polygon berdasarkan hasil
      var polygonColor = hasil === "LAYAK" ? "green" : "red";

      var polygon = L.polygon(
        [
          [latitude - 0.00004, longitude - 0.00004],
          [latitude - 0.00004, longitude + 0.00004],
          [latitude + 0.00004, longitude + 0.00004],
          [latitude + 0.00004, longitude - 0.00004],
        ],
        {
          color: polygonColor,
        }
      )
        .addTo(polygonGroup)
        .on("click", function (e) {
          zoomToFeature(e, latitude, longitude);
        })
        .on("mouseover", function (e) {
          showPopup(e);
        })
        .on("mouseout", function (e) {
          hidePopup(e);
        });
    });
  })
  .catch((error) => {
    console.error("Terjadi kesalahan:", error);
  });

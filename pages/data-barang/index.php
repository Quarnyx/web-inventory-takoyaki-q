<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Data Barang</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Data Barang</h5>
            <p class="card-subtitle">Tabel menampilkan data barang di aplikasi
            </p>
            <button id="tambah" class="btn btn-primary mb-2 mt-2">Tambah
                Barang</button>
        </div>
        <div class="card-body">
            <div id="tabel">

            </div>
        </div>
    </div>

</div>
<script>
    function loadTable() {
        $('#tabel').load('pages/data-barang/tabel.php');
    }
    $(document).ready(function () {
        loadTable();
        $('#tambah').click(function () {
            $('.modal').modal('show');
            $('.modal-title').text('Tambah Barang');
            $('.modal-body').load('pages/data-barang/form-tambah.php');
        });
    });
</script>
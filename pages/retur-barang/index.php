<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Retur Barang</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">ReturBarang</h5>
            <p class="card-subtitle">Tabel menampilkan data retur barang di aplikasi
            </p>
            <button id="tambah" class="btn btn-primary mb-2 mt-2">Tambah
                retur Barang</button>
        </div>
        <div class="card-body">
            <div id="tabel">

            </div>
        </div>
    </div>

</div>
<script>
    function loadTable() {
        $('#tabel').load('pages/retur-barang/tabel.php');
    }
    $(document).ready(function () {
        loadTable();
        $('#tambah').click(function () {
            $('.modal').modal('show');
            $('.modal-title').text('Tambah PO Barang');
            $('.modal-body').load('pages/retur-barang/form-tambah.php');
        });
    });
</script>
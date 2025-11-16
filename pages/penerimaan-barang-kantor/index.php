<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Data Penerimaan Barang</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Data Penerimaan Barang dari Supplier</h5>
            <p class="card-subtitle">Tabel menampilkan data penerimaan barang di aplikasi
            </p>
        </div>
        <div class="card-body">
            <div id="tabel">

            </div>
        </div>
    </div>

</div>
<script>
    function loadTable() {
        $('#tabel').load('pages/penerimaan-barang-kantor/tabel.php');
    }
    $(document).ready(function () {
        loadTable();
        $('#tambah').click(function () {
            $('.modal').modal('show');
            $('.modal-title').text('Tambah PO Barang');
            $('.modal-body').load('pages/penerimaan-barang-kantor/form-tambah.php');
        });
    });
</script>
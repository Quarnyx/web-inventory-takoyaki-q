<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Data Supplier</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Data Supplier</h5>
            <p class="card-subtitle">Tabel menampilkan data supplier di aplikasi
            </p>
            <button id="tambah" class="btn btn-primary mb-2 mt-2">Tambah
                Supplier</button>
        </div>
        <div class="card-body">
            <div id="tabel">

            </div>
        </div>
    </div>

</div>
<script>
    function loadTable() {
        $('#tabel').load('pages/data-supplier/tabel.php');
    }
    $(document).ready(function () {
        loadTable();
        $('#tambah').click(function () {
            $('.modal').modal('show');
            $('.modal-title').text('Tambah Supplier');
            $('.modal-body').load('pages/data-supplier/form-tambah.php');
        });
    });
</script>
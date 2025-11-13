<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Data Kategori</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Data Kategori</h5>
            <p class="card-subtitle">Tabel menampilkan data kategori di aplikasi
            </p>
            <button id="tambah" class="btn btn-primary mb-2 mt-2">Tambah
                Kategori</button>
        </div>
        <div class="card-body">
            <div id="tabel">

            </div>
        </div>
    </div>

</div>
<script>
    function loadTable() {
        $('#tabel').load('pages/data-kategori/tabel.php');
    }
    $(document).ready(function () {
        loadTable();
        $('#tambah').click(function () {
            $('.modal').modal('show');
            $('.modal-title').text('Tambah Kategori');
            $('.modal-body').load('pages/data-kategori/form-tambah.php');
        });
    });
</script>
<!DOCTYPE html>
<html>
<meta name="csrf-token" content="{{ csrf_token() }}">

<head>
    <title>Home Page</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/notif.css') }}">
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="loading-overlay justify-content-center">
                <div class="drawing">
                    <div class="loading-dot"></div>
                </div>
            </div>
            <div class="card card-home">
                <div class="card-header">
                    <div class="card-tittle m-2">
                        <h5>Welcome, <label class="name"></span> </h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="float-left">
                                <a class="btn btn-dark btn-sm" href="#modalAddItem" data-toggle="modal"> <i
                                        class="fas fa-plus"></i> Add</a>
                            </div>
                            <div class="float-right">
                                <a class="btn btn-danger btn-sm text-white" onclick="doLogout()">
                                    <i class="fas fa-power-off"></i> Logout </a>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mt-3">
                                    <thead>
                                        <tr>
                                            <th colspan="4" class="text-center">
                                                <a href="javascript:void(0)" onclick="getListItem()"
                                                    class="btn btn-sm btn-info"><i class="fa fa-refresh"></i> Get
                                                    Data</a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Stock</th>
                                            <th>Uom</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body">
                                        <tr>
                                            <td colspan="4" class="text-center">No Data</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddItem" tabindex="-1" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="form-add-item">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" class="form-control" id="item-name" required
                                placeholder="silahkan isi..">
                        </div>
                        <div class="form-group">
                            <label>Stock</label>
                            <input type="number" class="form-control" id="item-stock" required
                                placeholder="silahkan isi..">
                        </div>
                        <div class="form-group">
                            <label>UOM</label>
                            <input type="text" class="form-control" id="item-uom" required
                                placeholder="silahkan isi..">
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="javascript:void(0)" class="btn btn-primary" onclick="savePartials()">Save</a>
                </div>

            </div>
        </div>
    </div>

</body>
<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/notif.js') }}"></script>

<script>
    // getListItem();
    checkSession();
    var notyf = new Notyf({
        position: {
            x: 'right',
            y: 'top',
        }
    });

    function doLogout() {
        localStorage.clear();
        location.href = "{{ url('/') }}";
    }

    function getListItem() {
        showLoading(true);
        $.ajax({
            url: "{{ route('getListItem') }}",
            type: "GET",
            data: {},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + JSON.parse(localStorage.getItem('auth')).token
            },
            success: function(response) {
                showLoading(false);
                initItems(response.data);
                generateTable();
            },
            error: function(xhr, status, error) {
                showLoading(false);
                notyf.error('Error fetching items.');
            }
        });
    }

    function initItems(apiItems) {
        const existing = localStorage.getItem('items');
        if (!existing) {
            localStorage.setItem('items', JSON.stringify({
                data: apiItems
            }));
        }
    }

    function getLocalStorage() {
        return JSON.parse(localStorage.getItem('items')) || {
            data: []
        };
    }

    function savePartials() {
        const item_name = $('#item-name').val().trim();
        const stock = $('#item-stock').val();
        const unit = $('#item-uom').val().trim();
        // console.log(name);

        if (!item_name || !stock || !unit) {
            alert('Semua field wajib diisi');
            return;
        }

        const store = getLocalStorage();
        // console.log(name + '--' + stock + '--' + uom);
        const exists = store.data.some(item =>
            item.item_name.toLowerCase() === item_name.toLowerCase()
        );

        if (exists) {
            alert('Item sudah ada');
            return;
        }

        store.data.push({
            'id': Date.now(),
            item_name,
            stock,
            unit,
        });

        pushToLocalStorage(store);
        $('#modalAddItem').modal('hide');
        getListItem();
    }

    function pushToLocalStorage(data) {
        localStorage.setItem('items', JSON.stringify(data));
    }

    function showLoading(isLoading) {
        if (isLoading) {
            $('.loading-overlay').addClass('d-flex');
        } else {
            $('.loading-overlay').removeClass('d-flex');
        }
    }

    function checkSession() {
        const auth = JSON.parse(localStorage.getItem('auth') || '{}');
        if (!auth.token) {
            localStorage.clear();
            location.href = "{{ url('/') }}";
        } else {
            $('.name').text(auth.user);
            // getListItem();
        }
    }

    function generateTable() {
        const store = getLocalStorage();
        const data = store.data || [];

        let tableBody = '';

        if (!data.length) {
            tableBody = `<tr><td colspan="3" class="text-center">No Data</td></tr>`;
        } else {
            data.forEach(item => {
                tableBody += `
                <tr>
                    <td>${item.item_name}</td>
                    <td>${item.stock}</td>
                    <td>${item.unit}</td>
                    <td>
                        <button class="btn btn-warning btn-sm m-1" onclick="editItem(${item.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm m-1" onclick="deleteItem(${item.id})"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
            `;
            });
        }

        $('#table-body').html(tableBody);
    }

    function editItem(id) {
        const store = getLocalStorage();
        const index = store.data.findIndex(item => item.id == id);

        const item = store.data[index];

        const newName = prompt('Nama Barang:', item.item_name);
        if (newName === null) return;

        const newStock = prompt('Stock:', item.stock);
        if (newStock === null) return;

        const newUnit = prompt('UOM:', item.unit);
        if (newUnit === null) return;

        if (!newName.trim() || !newStock || !newUnit.trim()) {
            alert('Data tidak boleh kosong');
            return;
        }
        const duplicate = store.data.some(i =>
            i.item_name.toLowerCase() === newName.toLowerCase() &&
            i.id !== id
        );

        if (duplicate) {
            alert('Item dengan nama tersebut sudah ada');
            return;
        }
        store.data[index] = {
            ...item,
            item_name: newName.trim(),
            stock: newStock,
            unit: newUnit.trim()
        };

        pushToLocalStorage(store);
        generateTable();

        alert('Data berhasil diupdate');
    }


    function deleteItem(id) {
        if (!confirm('Apakah anda yakin?')) {
            return false;
        }
        const store = getLocalStorage();
        store.data = store.data.filter(item => item.id != id);
        pushToLocalStorage(store);
        generateTable();
        notyf.success('Item deleted successfully.');
    }
</script>

</html>

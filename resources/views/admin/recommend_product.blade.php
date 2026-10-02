@extends('admin.layout.master')

@section('unit.recommend_product', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="recommend_product" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        管理首頁「精選商品」卡片要顯示哪些商品。先選「商品分類」，再選該分類下的「商品」，商品的名稱／圖片會自動帶入，不用重複輸入。<br>前台顯示規則：卡片左上角的類別標籤（主機／喇叭／行車記錄器…）由商品分類自動帶出；商品圖請用白底或去背的方形圖（建議 1200×1200，商品佔 8 成），卡片底色是純白；精選只選 1 個會變成大張主打卡、2～3 個置中排列、最多顯示前 8 個（排序數字小的在前）；一個都沒選時，前台會顯示「精選商品準備中」。停用的商品不會顯示。
                    </div>
                </div>
            </div>

            {{-- create / update --}}
            <div class="row justify-content-center">
                <div class="col-12 col-sm-8">
                    <form v-on:submit.prevent="createItem()" id="createArea" style="display:none">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-plus"></i>
                                    新增
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>*商品分類</label>
                                            <select class="form-control" v-model="createData.product_type" @change="loadOptions('create')" required>
                                                <option value="" disabled>請選擇分類</option>
                                                <option v-for="t in types" :key="t.value" :value="t.value">@{{ t.label }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>*商品</label>
                                            <select class="form-control" v-model="createData.product_id" :disabled="!createData.product_type" required>
                                                <option value="" disabled>請先選擇商品</option>
                                                <option v-for="o in createOptions" :key="o.value" :value="o.value">@{{ o.label }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>排序（數字越大越前面）</label>
                                            <input type="number" class="form-control" v-model="createData.sort">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>*狀態</label>
                                            <select class="form-control" v-model="createData.status" required>
                                                <option value="1">啟用</option>
                                                <option value="0">停用</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    新增
                                </button>
                                <button type="button" @click="open('list')" class="btn float-right btn-warning">
                                    <i class="fas fa-undo-alt"></i>
                                    返回
                                </button>
                            </div>
                        </div>
                    </form>
                    <form v-on:submit.prevent="updateItem(editData.id)" id="editArea" style="display:none">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-pencil-alt"></i>
                                    編輯
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>*商品分類</label>
                                            <select class="form-control" v-model="editData.product_type" @change="loadOptions('edit')" required>
                                                <option value="" disabled>請選擇分類</option>
                                                <option v-for="t in types" :key="t.value" :value="t.value">@{{ t.label }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>*商品</label>
                                            <select class="form-control" v-model="editData.product_id" :disabled="!editData.product_type" required>
                                                <option value="" disabled>請先選擇商品</option>
                                                <option v-for="o in editOptions" :key="o.value" :value="o.value">@{{ o.label }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>排序（數字越大越前面）</label>
                                            <input type="number" class="form-control" v-model="editData.sort">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>*狀態</label>
                                            <select class="form-control" v-model="editData.status">
                                                <option value="1">啟用</option>
                                                <option value="0">停用</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    更新
                                </button>
                                <button type="button" @click="open('list')" class="btn float-right btn-warning">
                                    <i class="fas fa-undo-alt"></i>
                                    返回
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- search --}}
            <div class="modal fade" id="search_modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">搜尋</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-form-label">商品分類</label>
                                <select class="form-control" v-model="search.product_type">
                                    <option value="">全部</option>
                                    <option v-for="t in types" :key="t.value" :value="t.value">@{{ t.label }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-left btn btn-primary" @click="getItems()">送出</button>
                            <button type="button" class="float-right btn btn-default" @click="clear('search')">清空</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- list --}}
            <div class="row" id="listArea">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <button @click="open('create')" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                新增
                            </button>

                            <div class="card-tools">
                                <button type="button" class="btn btn-default" v-if="search.is_search == true" @click="clear('search')">
                                    <i class="fa-solid fa-align-justify"></i>
                                    總覽
                                </button>

                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#search_modal">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    搜尋
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>商品分類</th>
                                        <th>圖片</th>
                                        <th>商品名稱</th>
                                        <th style="width: 20%">排序<small class="text-muted d-block" style="font-weight:400">由上到下就是前台順序（最上面最先顯示，前台最多顯示前 8 個）。拖曳 ☰ 或按 ↑ ↓ 調整，改數字也行（自動儲存）</small></th>
                                        <th style="width: 10%">狀態</th>
                                        <th style="width: 15%">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, num) in items" :key="item.id" :draggable="handleDown" :class="{'sort-over': dragOverIdx === num && dragFrom > num, 'sort-over-down': dragOverIdx === num && dragFrom >= 0 && dragFrom < num}" @dragstart="dragStart(num, $event)" @dragenter.prevent="dragEnter(num)" @dragover.prevent @drop.prevent="dropRow(num)" @dragend="dragEnd">
                                        <td>@{{ typeLabel(item.product_type) }}</td>
                                        <td>
                                            <img v-if="item.product_img" :src="item.product_img" style="height:50px;">
                                        </td>
                                        <td>@{{ item.product_name }}</td>
                                        <td style="white-space:nowrap">
                                            <span class="sort-handle" title="按住拖曳" @mousedown="handleDown = true" @mouseup="handleDown = false">☰</span>
                                            <a href="javascript:void(0)" class="sort-arrow" title="往前一格" @click="moveItem(num, -1)">↑</a>
                                            <a href="javascript:void(0)" class="sort-arrow" title="往後一格" @click="moveItem(num, 1)">↓</a>
                                            <input type="number" class="form-control sort-num" v-model="item.sort" @change="sortItems">
                                        </td>
                                        <td>
                                            <label class="sw-toggle" :title="item.status == 1 ? '啟用中（點一下停用）' : '已停用（點一下啟用）'"><input type="checkbox" :checked="item.status == 1" @click.prevent="statusItem(item.id)"><span class="sw-slider"></span></label>
                                            <span class="sw-text" :class="item.status == 1 ? 'on' : 'off'">@{{ item.status == 1 ? '啟用' : '停用' }}</span>
                                        </td>
                                        <td class="method-button">
                                            <button class="btn btn-primary btn-sm" @click="open('edit', item.id)">
                                                <i class="fas fa-pencil-alt"></i>
                                                編輯
                                            </button>
                                            <button class="btn btn-danger btn-sm" @click="deleteItem(item.id)">
                                                <i class="fas fa-trash"></i>
                                                刪除
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <x-components::pagination />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        var vm = new Vue({
            mixins: [window.sortMixin || {}],
            el: '#container',
            data: {
                url: '{{ route('admin.recommend_product') }}',
                types: @json($types),
                items: {},
                createData: {},
                editData: {},
                createOptions: [],
                editOptions: [],
                search: {
                    product_type: '',
                    is_search: false
                },
                page: 1,
                pagination: {
                    start: 0,
                    total: 0,
                    current_page: 1
                }
            },
            created: function() {
                this.getItems();
                // 讓瀏覽器「上一頁」能回到列表：切換到新增/編輯畫面時有登記一筆瀏覽器紀錄（見 open()），
                // 按上一頁會觸發這裡，直接切回列表，而不是離開這一頁。
                window.addEventListener('popstate', function() {
                    vm.open('list');
                });
            },
            methods: {
                typeLabel: function(value) {
                    let found = vm.types.find(function(t) { return t.value == value; });
                    return found ? found.label : value;
                },
                clear: function(method = 'all') {
                    if (method == 'all') {
                        vm.createData = {
                            product_type: '',
                            product_id: '',
                            sort: 0,
                            status: 1,
                        };
                        vm.editData = {};
                        vm.createOptions = [];
                        vm.editOptions = [];
                    } else if (method == 'search') {
                        vm.search = {
                            product_type: '',
                            is_search: false
                        };

                        vm.getItems();
                    }
                },
                loadOptions: function(mode) {
                    let type = mode == 'create' ? vm.createData.product_type : vm.editData.product_type;
                    if (!type) return;

                    try {
                        axios.get(vm.url + '/options', {
                            params: { product_type: type }
                        }).then(function(response) {
                            if (mode == 'create') {
                                vm.createOptions = response.data.options;
                                vm.createData.product_id = '';
                            } else {
                                vm.editOptions = response.data.options;
                            }
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                open: function(active = '', id = '') {
                    vm.clear();

                    switch (active) {
                        case 'list':
                            $('#createArea').fadeOut(0);
                            $('#editArea').fadeOut(0);
                            $('#listArea').fadeIn(300);
                            break;

                        case 'create':
                            history.pushState({}, '');
                            $('#listArea').fadeOut(0);
                            $('#createArea').fadeIn(300);
                            break;

                        case 'edit':
                            history.pushState({}, '');
                            try {
                                axios.get(vm.url + '/' + id).then(function(response) {
                                    vm.editData = response.data.item;
                                    $('#listArea').fadeOut(0);
                                    $('#editArea').fadeIn(300);
                                    // 編輯時先把該分類的商品選項載入，才能讓下拉正確顯示目前選的商品
                                    axios.get(vm.url + '/options', {
                                        params: { product_type: vm.editData.product_type }
                                    }).then(function(res) {
                                        vm.editOptions = res.data.options;
                                    });
                                }).catch(function(error) {
                                    vm.showMessage('error', error.response.data.message);
                                });
                            } catch (error) {
                                vm.showMessage('error', error);
                            }
                            break;

                        default:
                            vm.showMessage('error', '系統異常。');
                            break;
                    }
                },
                getItems: function(page = 1, pageMove = true) {
                    let vm = this;
                    vm.page = page;

                    if (pageMove) {
                        $('html, body').animate({scrollTop: 0}, 'slow');
                    }

                    try {
                        axios.get(vm.url + '/all', {
                            params: {
                                page: page,
                                per_page: 500,
                                product_type: vm.search.product_type
                            }
                        }).then(function(response) {
                            let total = Math.ceil(response.data.items.total / response.data.items.per_page);
                            vm.items = response.data.items.data;
                            vm.search.is_search = response.data.is_search;
                            vm.setPagination(response.data.items.current_page, total);
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                createItem: function() {
                    try {
                        axios.post(vm.url, vm.createData).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems();
                            vm.open('list');
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                updateItem: function(id) {
                    try {
                        axios.patch(vm.url + '/' + id, vm.editData).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page, false);
                            vm.open('list');
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                deleteItem: function(id) {
                    if (confirm('確定要刪除？') !== true) return false;

                    try {
                        axios.delete(vm.url + '/' + id).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page, false);
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                sortItems: function() {
                    if (!vm.items || vm.items.length == 0) return false;

                    try {
                        axios.patch(vm.url + '/all/sort', {items: vm.items}).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page, false);
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                statusItem: function(id) {
                    try {
                        axios.patch(vm.url + '/' + id + '/status').then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page, false);
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                setPagination: function(current_page, total) {
                    vm.pagination.current_page = current_page;
                    if (current_page > 6) {
                        vm.pagination.start = current_page - 5;
                        vm.pagination.total  = (total > (current_page+ 5)) ? current_page + 5 : total;
                    } else {
                        vm.pagination.start = 1;
                        vm.pagination.total = total < 10 ? total : 10;
                    }
                },
                showMessage: function(format, message) {
                    if (format == 'success') {
                        toastr.success(message);
                    } else {
                        toastr.warning(message);
                    }
                }
            }
        });
    </script>
@endsection

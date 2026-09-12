@extends('admin.layout.master')

@section('unit.install_case', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="install_case" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        管理首頁「安裝實績（Case）」卡片，會顯示在首頁「精選商品」區塊下方。
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
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*名稱</label>
                                            <input type="text" class="form-control" v-model="createData.name" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*分類</label>
                                            <select class="form-control" v-model="createData.category" required>
                                                <option v-for="c in categories" :key="c.value" :value="c.value">@{{ c.label }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>*案例圖片 <span class="text-muted">　建議 1600 × 1000（16:10）實拍，主體置中，300KB 內。電腦版／手機版共用一張（首頁卡、列表卡、內頁都是 16:10 裁切）</span></span> <a href="{{ asset('images/admin-guide/case-1600x1000.png') }}" target="_blank" style="white-space:nowrap">（看範例圖）</a></label>
                                            <div class="input-group">
                                                <input id="create-img" class="form-control" type="text" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-create" data-input="create-img" data-preview="create-preview" class="btn btn-block btn-default">
                                                        <i class="fa fa-picture-o"></i>
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <div id="create-preview" style="margin-top:10px; margin-bottom:10px"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>安裝日期 <span class="text-muted">(前台依此排序，不填則用建立時間)</span></label>
                                            <input type="date" class="form-control" v-model="createData.installed_at">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>*汽車品牌</label>
                                            <select class="form-control" v-model="createData.car_brand_id" @change="createData.car_id = ''" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in brands" :value="row.id">
                                                    @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>*汽車車款</label>
                                            <select class="form-control" v-model="createData.car_id" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in cars" v-if="row.car_brand_id == createData.car_brand_id" :value="row.id">
                                                    @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>安裝產品 <span class="text-muted">(例：Clarion GL-1002)</span></label>
                                            <input type="text" class="form-control" v-model="createData.product">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>施工據點</label>
                                            <input type="text" class="form-control" v-model="createData.dealer">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>客戶需求</label>
                                            <textarea class="form-control" rows="3" v-model="createData.need"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>施工內容</label>
                                            <textarea class="form-control" rows="3" v-model="createData.work"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>置頂</label>
                                            <select class="form-control" v-model="createData.is_pinned">
                                                <option value="0">否</option>
                                                <option value="1">是（排在最前面）</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>顯示在首頁 <span class="text-muted">(最多 3 筆)</span></label>
                                            <select class="form-control" v-model="createData.is_home">
                                                <option value="0">否</option>
                                                <option value="1">是</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>首頁排序 <span class="text-muted">(數字小的在前)</span></label>
                                            <input type="number" class="form-control" v-model="createData.home_sort">
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
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*名稱</label>
                                            <input type="text" class="form-control" v-model="editData.name" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*分類</label>
                                            <select class="form-control" v-model="editData.category" required>
                                                <option v-for="c in categories" :key="c.value" :value="c.value">@{{ c.label }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>*案例圖片 <span class="text-muted">　建議 1600 × 1000（16:10）實拍，主體置中，300KB 內。電腦版／手機版共用一張（首頁卡、列表卡、內頁都是 16:10 裁切）</span></span> <a href="{{ asset('images/admin-guide/case-1600x1000.png') }}" target="_blank" style="white-space:nowrap">（看範例圖）</a></label>
                                            <div class="input-group">
                                                <input id="edit-img" class="form-control" type="text" :value="editData.img" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-edit" data-input="edit-img" data-preview="edit-preview" class="btn btn-block btn-default">
                                                        <i class="fa fa-picture-o"></i>
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <div style="margin-top:10px; margin-bottom:10px">
                                                <div v-if="editData.img" style="display: inline-block">
                                                    <img :src="editData.img" style="height:10rem;">
                                                    <span>=></span>
                                                </div>
                                                <div id="edit-preview" style="display: inline-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>安裝日期 <span class="text-muted">(前台依此排序，不填則用建立時間)</span></label>
                                            <input type="date" class="form-control" v-model="editData.installed_at">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>*汽車品牌</label>
                                            <select class="form-control" v-model="editData.car_brand_id" @change="editData.car_id = ''" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in brands" :value="row.id">
                                                    @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>*汽車車款</label>
                                            <select class="form-control" v-model="editData.car_id" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in cars" v-if="row.car_brand_id == editData.car_brand_id" :value="row.id">
                                                    @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>安裝產品 <span class="text-muted">(例：Clarion GL-1002)</span></label>
                                            <input type="text" class="form-control" v-model="editData.product">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>施工據點</label>
                                            <input type="text" class="form-control" v-model="editData.dealer">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>客戶需求</label>
                                            <textarea class="form-control" rows="3" v-model="editData.need"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>施工內容</label>
                                            <textarea class="form-control" rows="3" v-model="editData.work"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>置頂</label>
                                            <select class="form-control" v-model="editData.is_pinned">
                                                <option value="0">否</option>
                                                <option value="1">是（排在最前面）</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>顯示在首頁 <span class="text-muted">(最多 3 筆)</span></label>
                                            <select class="form-control" v-model="editData.is_home">
                                                <option value="0">否</option>
                                                <option value="1">是</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>首頁排序 <span class="text-muted">(數字小的在前)</span></label>
                                            <input type="number" class="form-control" v-model="editData.home_sort">
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
                                <label class="col-form-label">名稱</label>
                                <input type="text" v-model="search.name" class="form-control">
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
                                        <th>名稱</th>
                                        <th style="width: 15%">分類</th>
                                        <th>圖片</th>
                                        <th style="width: 8%">排序</th>
                                        <th style="width: 8%">置頂</th>
                                        <th style="width: 8%">首頁</th>
                                        <th style="width: 10%">狀態</th>
                                        <th style="width: 15%">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, num) in items" :key="item.id">
                                        <td>
                                            @{{ item.name }}
                                            <small v-if="item.brand || item.car" class="d-block text-muted">
                                                @{{ item.brand ? item.brand.name : '' }} @{{ item.car ? item.car.name : '' }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">@{{ categoryLabel(item.category) }}</span>
                                        </td>
                                        <td>
                                            <img v-if="item.img" :src="item.img" style="height:50px;">
                                        </td>
                                        <td>@{{ item.sort }}</td>
                                        <td>
                                            <a class="btn btn-white btn-sm" href="javascript:void(0)" @click="pinnedItem(item.id)">
                                                <i v-if="item.is_pinned == 1" class="fas fa-check-circle text-green"></i>
                                                <i v-else class="fas fa-times-circle text-red"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-white btn-sm" href="javascript:void(0)" @click="homeItem(item.id)">
                                                <i v-if="item.is_home == 1" class="fas fa-check-circle text-green"></i>
                                                <i v-else class="fas fa-times-circle text-red"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a v-if="item.status == 1" class="btn btn-white btn-sm" href="javascript:void(0)" @click="statusItem(item.id)">
                                                <i class="fas fa-check-circle text-green"></i>
                                                啟用
                                            </a>
                                            <a v-else class="btn btn-white btn-sm" href="javascript:void(0)" @click="statusItem(item.id)">
                                                <i class="fas fa-times-circle text-red"></i>
                                                停用
                                            </a>
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
            el: '#container',
            data: {
                url: '{{ route('admin.install_case') }}',
                items: {},
                createData: {},
                editData: {},
                brands: {},
                cars: {},
                categories: [
                    { value: 0, label: '多媒體安卓機' },
                    { value: 1, label: '車型專用機' },
                    { value: 2, label: '汽車音響' },
                    { value: 3, label: '行車記錄器' },
                    { value: 4, label: '影像・安全' },
                    { value: 5, label: '車用配件' },
                    { value: 6, label: '車用主機 1/2DIN' },
                    { value: 7, label: '頭枕螢幕' },
                    { value: 8, label: '可攜式' }
                ],
                search: {
                    name: '',
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
            },
            mounted: function() {
                $('#lfm-create').filemanager('file', {prefix: 'filemanager'});
                $('#lfm-edit').filemanager('file', {prefix: 'filemanager'});
            },
            methods: {
                categoryLabel: function(value) {
                    let found = vm.categories.find(function(c) { return c.value == value; });
                    return found ? found.label : '-';
                },
                clear: function(method = 'all') {
                    if (method == 'all') {
                        vm.createData = {
                            category: 0,
                            sort: 0,
                            status: 1,
                            installed_at: '',
                            car_brand_id: '',
                            car_id: '',
                            product: '',
                            dealer: '',
                            need: '',
                            work: '',
                            is_pinned: 0,
                            is_home: 0,
                            home_sort: 0,
                        };
                        vm.editData = {};
                        $('#create-img').val('');
                        $('#create-preview').empty();
                        $('#edit-img').val('');
                        $('#edit-preview').html('');
                    } else if (method == 'search') {
                        vm.search = {
                            name: '',
                            is_search: false
                        };

                        vm.getItems();
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
                            $('#listArea').fadeOut(0);
                            $('#createArea').fadeIn(300);
                            break;

                        case 'edit':
                            try {
                                axios.get(vm.url + '/' + id).then(function(response) {
                                    vm.editData = response.data.item;
                                    $('#listArea').fadeOut(0);
                                    $('#editArea').fadeIn(300);
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
                                name: vm.search.name
                            }
                        }).then(function(response) {
                            let total = Math.ceil(response.data.items.total / response.data.items.per_page);
                            vm.items = response.data.items.data;
                            vm.brands = response.data.brands;
                            vm.cars = response.data.cars;
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
                        vm.createData.img = $('#create-img').val();
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
                        vm.editData.img = $('#edit-img').val();
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
                pinnedItem: function(id) {
                    try {
                        axios.patch(vm.url + '/' + id + '/pinned').then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page, false);
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                homeItem: function(id) {
                    try {
                        axios.patch(vm.url + '/' + id + '/home').then(function(response) {
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

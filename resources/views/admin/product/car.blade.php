@extends('admin.layout.master')

@section('nav.product', 'menu-open')
@section('unit_master.product', 'active')
@section('unit.car', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="product" unit="car" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            {{-- create / update --}}
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-6">
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
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>*汽車品牌</label>
                                            <select class="form-control" v-model="createData.car_brand_id" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in brands" :value="row.id">
                                                    @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>*名稱</label>
                                            <input type="text" class="form-control" v-model="createData.name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-12 col-sm-2 col-form-label">*年份區間</label>
                                    <div class="col-12 col-sm-4">
                                        <input type="number" min="1911" max="{{ date('Y') }}" class="form-control" v-model="createData.year_start" placeholder="開始時間" required/>
                                    </div>
                                    <label class="col-12 col-sm-2 col-form-label text-center">~</label>
                                    <div class="col-12 col-sm-4">
                                        <input type="number" min="1911" max="{{ date('Y') }}" class="form-control" v-model="createData.year_end" placeholder="結束時間" required/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-4">
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
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>*汽車品牌</label>
                                            <select class="form-control" v-model="editData.car_brand_id" required>
                                                <option v-for="row in brands" :value="row.id">
                                                    @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>*名稱</label>
                                            <input type="text" class="form-control" v-model="editData.name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-12 col-sm-2 col-form-label">*年份區間</label>
                                    <div class="col-12 col-sm-4">
                                        <input type="number" min="1911" max="{{ date('Y') }}" class="form-control" v-model="editData.year_start" placeholder="開始時間" required/>
                                    </div>
                                    <label class="col-12 col-sm-2 col-form-label text-center">~</label>
                                    <div class="col-12 col-sm-4">
                                        <input type="number" min="1911" max="{{ date('Y') }}" class="form-control" v-model="editData.year_end" placeholder="結束時間" required/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-4">
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
                                <label class="col-form-label">分類</label>
                                <select class="form-control" v-model="search.car_brand_id">
                                    <option value="">請選擇</option>
                                    <option v-for="row in brands" :value="row.id">
                                        @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                    </option>
                                </select>
                            </div>
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
                                        <th>汽車品牌</th>
                                        <th>名稱</th>
                                        <th>年份區間</th>
                                        <!--<th>建立時間</th>-->
                                        <!--<th style="width: 10%">-->
                                        <!--    排序-->
                                        <!--    <a href="javascript:void(0)" @click="sortItems">-->
                                        <!--        <i class="fas fa-sync-alt text-gray"></i>-->
                                        <!--    </a>-->
                                        <!--</th>-->
                                        <th style="width: 10%">狀態</th>
                                        <th style="width: 15%">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, num) in items" :key="item.id">
                                        <td>
                                            <!--@{{ item.brand ? item.brand.name + (item.brand.status == 0 ? '(停用)' : '') : '' }}-->
                                            @{{ item.brand_name }}
                                        </td>
                                        <td>@{{ item.name }}</td>
                                        <td>@{{ item.year_start + ' ~ ' + item.year_end }}</td>
                                        <!--<td>@{{ item.created_at }}</td>-->
                                        <!--<td>-->
                                        <!--    <input type="number" class="form-control" v-model="item.sort">-->
                                        <!--</td>-->
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
                url: '{{ route('admin.car') }}',
                brands: {},
                items: {},
                createData: {},
                editData: {},
                search: {
                    name: '',
                    car_brand_id: '',
                    is_search: false
                },
                page: 1,
                pagination: {
                    start: 0,
                    total: 0,
                    current_page: 1
                },
            },
            created: function() {
                this.getItems();
            },
            methods: {
                clear: function(method = 'all') {
                    if (method == 'all') {
                        vm.createData = {
                            car_brand_id: '',
                            status: 1,
                        };
                        vm.editData = {};
                    } else if (method == 'search') {
                        vm.search = {
                            name: '',
                            car_brand_id: '',
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
                                name: vm.search.name,
                                car_brand_id: vm.search.car_brand_id,
                            }
                        }).then(function(response) {
                            let total = Math.ceil(response.data.items.total / response.data.items.per_page);
                            vm.items = response.data.items.data;
                            vm.brands = response.data.brands;
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
                    if (vm.items.length == 0) return false;

                    try {
                        axios.patch(vm.url + '/all/sort', {items: vm.items}).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page);
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

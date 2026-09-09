@extends('admin.layout.master')

@section('unit.list_banner', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="list_banner" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        管理前台各個「產品列表/總覽」頁面最上方的 Banner 背景圖。同一個頁面被 MM/Clarion 或多個分類共用時，可以各自設定不同的圖片；沒有設定圖片的頁面，前台會自動改用預設的漸層背景，不會空白。
                    </div>
                </div>
            </div>

            {{-- edit --}}
            <div class="row justify-content-center">
                <div class="col-12 col-sm-8">
                    <form v-on:submit.prevent="saveItem()">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-pencil-alt"></i>
                                    設定 Banner
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*頁面</label>
                                            <select class="form-control" v-model="form.page_key" @change="onPageChange" required>
                                                <option v-for="(page, key) in pagesConfig" :key="key" :value="key">@{{ page.name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*分類</label>
                                            <select class="form-control" v-model="form.type_key" :disabled="!hasTypes" required>
                                                <option v-for="(label, key) in typeOptions" :key="key" :value="key">@{{ label }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Banner 圖片 <span class="text-muted">(建議寬圖，例如 1600x600；不設定則前台用預設漸層背景)</span></label>
                                            <div class="input-group">
                                                <input id="edit-img" class="form-control" type="text" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-edit" data-input="edit-img" data-preview="edit-preview" class="btn btn-block btn-default">
                                                        <i class="fa fa-picture-o"></i>
                                                        選取檔案
                                                    </a>
                                                </span>
                                                <span class="input-group-btn">
                                                    <a href="javascript:void(0)" class="btn btn-block btn-outline-danger" @click="clearForm">
                                                        <i class="fa fa-times"></i>
                                                        清除
                                                    </a>
                                                </span>
                                            </div>
                                            <div id="edit-preview" style="margin-top:10px; margin-bottom:10px"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    儲存
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- list --}}
            <div class="row" id="listArea">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">目前設定狀況</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>頁面</th>
                                        <th>分類</th>
                                        <th>目前 Banner</th>
                                        <th style="width: 15%">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in items" :key="row.page_key + '-' + row.type_key">
                                        <td>@{{ row.page_name }}</td>
                                        <td>@{{ row.type_name }}</td>
                                        <td>
                                            <img v-if="row.img" :src="row.img" style="height:44px;">
                                            <span v-else class="text-muted">未設定（用預設背景）</span>
                                        </td>
                                        <td class="method-button">
                                            <button type="button" class="btn btn-primary btn-sm" @click="openEdit(row)">
                                                <i class="fas fa-pencil-alt"></i>
                                                編輯
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" v-if="row.img" @click="clearItem(row)">
                                                <i class="fas fa-trash"></i>
                                                清除
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                url: '{{ route('admin.list_banner') }}',
                pagesConfig: @json($pages),
                items: [],
                form: {
                    page_key: '',
                    type_key: 'default',
                    img: ''
                }
            },
            computed: {
                typeOptions: function() {
                    let page = this.pagesConfig[this.form.page_key];
                    return (page && page.types) ? page.types : { default: '預設' };
                },
                hasTypes: function() {
                    let page = this.pagesConfig[this.form.page_key];
                    return !!(page && page.types);
                }
            },
            created: function() {
                let keys = Object.keys(this.pagesConfig);
                if (keys.length) {
                    this.form.page_key = keys[0];
                }
                // onPageChange／getItems 內部用的是全域變數 vm（跟專案其他後台頁一致的寫法），
                // 但 vm 要等 new Vue(...) 整個建構完才會賦值；created 執行的當下 vm 還是 undefined，
                // 直接呼叫會噴錯、導致清單一直讀不到資料。用 $nextTick 延到下一輪再呼叫，此時 vm 已經有值。
                this.$nextTick(function() {
                    vm.onPageChange();
                    vm.getItems();
                });
            },
            mounted: function() {
                $('#lfm-edit').filemanager('file', {prefix: 'filemanager'});
            },
            methods: {
                onPageChange: function() {
                    let types = this.typeOptions;
                    vm.form.type_key = Object.keys(types)[0];
                },
                clearForm: function() {
                    vm.form.img = '';
                    $('#edit-img').val('');
                    $('#edit-preview').html('');
                },
                getItems: function() {
                    try {
                        axios.get(vm.url + '/all').then(function(response) {
                            vm.items = response.data.items;
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                openEdit: function(row) {
                    vm.form.page_key = row.page_key;
                    vm.form.type_key = row.type_key;
                    vm.form.img = row.img || '';
                    $('#edit-img').val(row.img || '');
                    if (row.img) {
                        $('#edit-preview').html('<img src="' + row.img + '" style="max-height:8rem;">');
                    } else {
                        $('#edit-preview').html('');
                    }
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                },
                saveItem: function() {
                    try {
                        vm.form.img = $('#edit-img').val();
                        axios.patch(vm.url + '/' + vm.form.page_key + '/' + vm.form.type_key, {
                            img: vm.form.img
                        }).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems();
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                clearItem: function(row) {
                    if (confirm('確定要清除這個 Banner？清除後前台會改用預設背景。') !== true) return false;

                    try {
                        axios.patch(vm.url + '/' + row.page_key + '/' + row.type_key, {
                            img: ''
                        }).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems();
                            if (vm.form.page_key === row.page_key && vm.form.type_key === row.type_key) {
                                vm.clearForm();
                            }
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
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

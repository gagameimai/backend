@extends('admin.layout.master')

@section('unit.list_banner', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="list_banner" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="row">
                <div class="col-12">
                    <div class="callout callout-info">
                        <h5 style="margin-bottom:.5rem"><i class="fas fa-info-circle"></i> 列表頁 Banner 尺寸說明（請先看這段）</h5>
                        <p style="margin-bottom:.35rem">
                            這裡管理的是各「產品列表／總覽」頁最上方那條 Banner。它跟首頁 Banner <b>不一樣</b>：首頁是滿版整個畫面，這裡是<b>橫長條</b>，
                            而且<b>電腦版與手機版要各上傳一張</b>——電腦版是 4:1 的長條，塞進手機會被左右各裁掉七成。
                        </p>
                        <table class="table table-sm table-bordered" style="background:#fff;margin-bottom:.5rem">
                            <thead><tr><th style="width:22%">欄位</th><th style="width:24%">建議尺寸</th><th>說明</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td>圖片（電腦版）</td>
                                    <td><b>1920 × 480</b>（4:1）</td>
                                    <td>電腦、平板橫向。<b>文字壓在左邊</b>，左邊約 2/3 會蓋一層淺色漸層，所以<b>主體放右邊 1/3</b>、左邊留空或留單純的底。</td>
                                </tr>
                                <tr>
                                    <td>圖片（手機版）</td>
                                    <td><b>1080 × 608</b>（16:9）</td>
                                    <td>螢幕寬 640px 以下自動改吃這張，改成「圖在上、文字在下」，圖<b>完整露出、不蓋白霧</b>。留空會沿用電腦版（會被裁）。</td>
                                </tr>
                            </tbody>
                        </table>
                        <details style="margin-bottom:.6rem">
                            <summary style="cursor:pointer;color:#0b5c8a;font-weight:700">看安全區範例圖（電腦版／手機版）</summary>
                            <div class="row" style="margin-top:.5rem;align-items:flex-end">
                                <div class="col-12 col-md-8"><img src="{{ asset('images/admin-guide/list-banner-desktop-1920x480.png') }}" style="width:100%;border:1px solid #ccd;border-radius:6px"></div>
                                <div class="col-8 col-md-3"><img src="{{ asset('images/admin-guide/list-banner-mobile-1080x608.png') }}" style="width:100%;border:1px solid #ccd;border-radius:6px"></div>
                            </div>
                        </details>
                        <p style="margin-bottom:0">
                            JPG 或 PNG，每張壓在 300KB 以內。同一頁被 MM／Clarion 或多個分類共用時可各自設定；沒設定的頁面前台自動用預設漸層背景，不會空白。
                        </p>
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
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>圖片（電腦版）<span class="text-muted">　建議 1920 × 480（4:1）</span></label>
                                            <div class="input-group">
                                                <input id="edit-img" class="form-control" type="text" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-edit" data-input="edit-img" data-preview="edit-preview" class="btn btn-block btn-default">
                                                        <i class="fa fa-picture-o"></i>
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <small class="form-text text-muted">主體放右邊 1/3，左邊 2/3 會蓋淺色漸層給文字。</small>
                                            <div id="edit-preview" style="margin-top:10px; margin-bottom:10px"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>圖片（手機版）<span class="text-muted">　建議 1080 × 608（16:9）</span></label>
                                            <div class="input-group">
                                                <input id="edit-img-mobile" class="form-control" type="text" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-edit-mobile" data-input="edit-img-mobile" data-preview="edit-preview-mobile" class="btn btn-block btn-default">
                                                        <i class="fa fa-picture-o"></i>
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <small class="form-text text-muted">手機圖在上、文字在下，圖完整露出。留空沿用電腦版（會被裁）。</small>
                                            <div id="edit-preview-mobile" style="margin-top:10px; margin-bottom:10px"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm" @click="clearForm">
                                            <i class="fa fa-times"></i>
                                            清除兩張圖
                                        </a>
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
                                        <th>電腦版</th>
                                        <th>手機版</th>
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
                                        <td>
                                            <img v-if="row.img_mobile" :src="row.img_mobile" style="height:44px;">
                                            <span v-else class="text-muted" style="font-size:12px">未設定<br>（沿用電腦版）</span>
                                        </td>
                                        <td class="method-button">
                                            <button type="button" class="btn btn-primary btn-sm" @click="openEdit(row)">
                                                <i class="fas fa-pencil-alt"></i>
                                                編輯
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" v-if="row.img || row.img_mobile" @click="clearItem(row)">
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
                    img: '',
                    img_mobile: ''
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
                $('#lfm-edit-mobile').filemanager('file', {prefix: 'filemanager'});
            },
            methods: {
                onPageChange: function() {
                    let types = this.typeOptions;
                    vm.form.type_key = Object.keys(types)[0];
                },
                clearForm: function() {
                    vm.form.img = '';
                    vm.form.img_mobile = '';
                    $('#edit-img').val('');
                    $('#edit-preview').html('');
                    $('#edit-img-mobile').val('');
                    $('#edit-preview-mobile').html('');
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
                    vm.form.img_mobile = row.img_mobile || '';
                    $('#edit-img-mobile').val(row.img_mobile || '');
                    if (row.img_mobile) {
                        $('#edit-preview-mobile').html('<img src="' + row.img_mobile + '" style="max-height:8rem;">');
                    } else {
                        $('#edit-preview-mobile').html('');
                    }
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                },
                saveItem: function() {
                    try {
                        vm.form.img = $('#edit-img').val();
                        vm.form.img_mobile = $('#edit-img-mobile').val();
                        axios.patch(vm.url + '/' + vm.form.page_key + '/' + vm.form.type_key, {
                            img: vm.form.img,
                            img_mobile: vm.form.img_mobile
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
                            img: '',
                            img_mobile: ''
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

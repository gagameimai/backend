@extends('admin.layout.master')

@section('unit.banner', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="banner" />

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
                            <div class="callout callout-info" style="margin:0 0 1rem 0">
                                <h5 style="margin-bottom:.5rem"><i class="fas fa-info-circle"></i> 首頁 Banner 尺寸說明（請先看這段）</h5>
                                <p style="margin-bottom:.35rem">
                                    首頁 Banner 是<b>滿版</b>顯示（撐滿整個瀏覽器畫面高度，用 object-fit: cover 裁切），
                                    所以<b>電腦版與手機版要各上傳一張</b>。
                                </p>
                                <table class="table table-sm table-bordered" style="background:#fff;margin-bottom:.5rem">
                                    <thead>
                                        <tr><th style="width:22%">欄位</th><th style="width:24%">建議尺寸</th><th>說明</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>圖片（電腦版）</td>
                                            <td><b>1920 × 1080</b>（16:9）</td>
                                            <td>桌機、平板橫向都吃這張。JPG 或 WebP，壓在 300KB 以內。</td>
                                        </tr>
                                        <tr>
                                            <td>圖片（手機版）</td>
                                            <td><b>1080 × 2160</b>（1:2 直式）</td>
                                            <td>螢幕寬度 640px 以下自動改吃這張。<b>留空的話會沿用電腦版那張</b>，但橫圖在手機會被左右各裁掉約 35%，圖上的字會被切掉。</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p style="margin-bottom:.35rem"><b>做圖時要注意：</b></p>
                                <ul style="margin-bottom:.35rem;padding-left:1.2rem">
                                    <li>重要的字與主體<b>集中在畫面中間 60% 的範圍</b>，兩邊、上下都可能被裁掉。</li>
                                    <li>底部大約 190px 會蓋上一層由下往上的暗漸層（給 SCROLL 指示與輪播圓點用），<b>不要把字放在最下面</b>。</li>
                                    <li>畫面正下方還會壓上「車型查詢」浮動卡片，也要留白。</li>
                                    <li>可以上傳多張，會自動輪播；排序欄位小的排前面。</li>
                                </ul>
                                <p style="margin-bottom:0">
                                    <b>色系（VIS）：</b>深色底 #0D1016 系、重點色用 Clarion Azzurro #007ABE。
                                    不要整塊染藍；MM 的橘 #F28729 與藍紫 #AF47D2 只用在 MM 專頁，首頁不用。
                                </p>
                            </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>*名稱</label>
                                            <input type="text" class="form-control" v-model="createData.name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>外部連結</label>
                                            <input type="url" class="form-control" v-model="createData.url">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>*圖片（電腦版）<span class="text-muted">　建議 1920 × 1080（16:9）</span></label>
                                            <div class="input-group">
                                                <input id="create-img" class="form-control" type="text" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-create" data-input="create-img" data-preview="create-preview" class="btn btn-block btn-default">
                                                        <i class="fa fa-picture-o"></i>
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <small class="form-text text-muted">桌機、平板橫向吃這張。JPG／WebP，壓在 300KB 以內。</small>
                                            <div id="create-preview" style="margin-top:10px; margin-bottom:10px"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>圖片（手機版）<span class="text-muted">　建議 1080 × 2160（直式）</span></label>
                                            <div class="input-group">
                                                <input id="create-img-mobile" class="form-control" type="text" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-create-mobile" data-input="create-img-mobile" data-preview="create-preview-mobile" class="btn btn-block btn-default">
                                                        <i class="fa fa-picture-o"></i>
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <small class="form-text text-muted">螢幕寬 640px 以下吃這張。留空會沿用電腦版，但橫圖在手機會被左右各裁約 35%。</small>
                                            <div id="create-preview-mobile" style="margin-top:10px; margin-bottom:10px"></div>
                                        </div>
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
                            <div class="callout callout-info" style="margin:0 0 1rem 0">
                                <h5 style="margin-bottom:.5rem"><i class="fas fa-info-circle"></i> 首頁 Banner 尺寸說明（請先看這段）</h5>
                                <p style="margin-bottom:.35rem">
                                    首頁 Banner 是<b>滿版</b>顯示（撐滿整個瀏覽器畫面高度，用 object-fit: cover 裁切），
                                    所以<b>電腦版與手機版要各上傳一張</b>。
                                </p>
                                <table class="table table-sm table-bordered" style="background:#fff;margin-bottom:.5rem">
                                    <thead>
                                        <tr><th style="width:22%">欄位</th><th style="width:24%">建議尺寸</th><th>說明</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>圖片（電腦版）</td>
                                            <td><b>1920 × 1080</b>（16:9）</td>
                                            <td>桌機、平板橫向都吃這張。JPG 或 WebP，壓在 300KB 以內。</td>
                                        </tr>
                                        <tr>
                                            <td>圖片（手機版）</td>
                                            <td><b>1080 × 2160</b>（1:2 直式）</td>
                                            <td>螢幕寬度 640px 以下自動改吃這張。<b>留空的話會沿用電腦版那張</b>，但橫圖在手機會被左右各裁掉約 35%，圖上的字會被切掉。</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p style="margin-bottom:.35rem"><b>做圖時要注意：</b></p>
                                <ul style="margin-bottom:.35rem;padding-left:1.2rem">
                                    <li>重要的字與主體<b>集中在畫面中間 60% 的範圍</b>，兩邊、上下都可能被裁掉。</li>
                                    <li>底部大約 190px 會蓋上一層由下往上的暗漸層（給 SCROLL 指示與輪播圓點用），<b>不要把字放在最下面</b>。</li>
                                    <li>畫面正下方還會壓上「車型查詢」浮動卡片，也要留白。</li>
                                    <li>可以上傳多張，會自動輪播；排序欄位小的排前面。</li>
                                </ul>
                                <p style="margin-bottom:0">
                                    <b>色系（VIS）：</b>深色底 #0D1016 系、重點色用 Clarion Azzurro #007ABE。
                                    不要整塊染藍；MM 的橘 #F28729 與藍紫 #AF47D2 只用在 MM 專頁，首頁不用。
                                </p>
                            </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>*名稱</label>
                                            <input type="text" class="form-control" v-model="editData.name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>外部連結</label>
                                            <input type="url" class="form-control" v-model="editData.url"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>*圖片（電腦版）<span class="text-muted">　建議 1920 × 1080（16:9）</span></label>
                                            <div class="input-group">
                                                <input id="edit-img" class="form-control" type="text" :value="editData.img" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-edit" data-input="edit-img" data-preview="edit-preview" class="btn btn-block btn-default">
                                                        <i class="fa fa-picture-o"></i>
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <small class="form-text text-muted">桌機、平板橫向吃這張。JPG／WebP，壓在 300KB 以內。</small>
                                            <div id="edit-preview" style="margin-top:10px; margin-bottom:10px">
                                                <img v-if="editData.img != null" :src="editData.img" style="height:10rem;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label>圖片（手機版）<span class="text-muted">　建議 1080 × 2160（直式）</span></label>
                                            <div class="input-group">
                                                <input id="edit-img-mobile" class="form-control" type="text" :value="editData.img_mobile" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-edit-mobile" data-input="edit-img-mobile" data-preview="edit-preview-mobile" class="btn btn-block btn-default">
                                                        <i class="fa fa-picture-o"></i>
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <small class="form-text text-muted">螢幕寬 640px 以下吃這張。留空會沿用電腦版，但橫圖在手機會被左右各裁約 35%。</small>
                                            <div id="edit-preview-mobile" style="margin-top:10px; margin-bottom:10px">
                                                <img v-if="editData.img_mobile != null" :src="editData.img_mobile" style="height:10rem;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <label>*狀態</label>
                                            <select class="form-control" v-model="editData.status" required>
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

            {{-- list --}}
            <div class="row" id="listArea">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <button @click="open('create')" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                新增
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>名稱</th>
                                        <th>外部連結</th>
                                        <th>圖片（電腦版）</th>
                                        <th>圖片（手機版）</th>
                                        <th>建立時間</th>
                                        <th style="width: 10%">
                                            排序
                                            <a href="javascript:void(0)" @click="sortItems">
                                                <i class="fas fa-sync-alt text-gray"></i>
                                            </a>
                                        </th>
                                        <th style="width: 10%">狀態</th>
                                        <th style="width: 15%">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, num) in items" :key="item.id">
                                        <td>@{{ item.name }}</td>
                                        <td>
                                            <a v-if="item.url" :href="item.url" target="_blank">連結</a>
                                        </td>
                                        <td>
                                            <img :src="item.img" style="height:50px;">
                                        </td>
                                        <td>
                                            <img v-if="item.img_mobile" :src="item.img_mobile" style="height:50px;">
                                            <span v-else class="text-muted" style="font-size:12px">未設定<br>（沿用電腦版）</span>
                                        </td>
                                        <td>@{{ item.created_at }}</td>
                                        <td>
                                            <input type="number" class="form-control" v-model="item.sort">
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
    <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
    <script type="text/javascript">
        var vm = new Vue({
            el: '#container',
            data: {
                url: '{{ route('admin.banner') }}',
                items: {},
                createData: {},
                editData: {},
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
            mounted: function() {
                $('#lfm-create').filemanager('file', {prefix: 'filemanager'});
                $('#lfm-create-mobile').filemanager('file', {prefix: 'filemanager'});
                $('#lfm-edit').filemanager('file', {prefix: 'filemanager'});
                $('#lfm-edit-mobile').filemanager('file', {prefix: 'filemanager'});
            },
            methods: {
                clear: function() {
                    vm.createData = {
                        status: 1,
                    };
                    vm.editData = {};
                    $('#create-img').val('');
                    $('#create-preview').empty();
                    $('#create-img-mobile').val('');
                    $('#create-preview-mobile').empty();
                    $('#edit-img').val('');
                    $('#edit-img-mobile').val('');
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
                                page: page
                            }
                        }).then(function(response) {
                            let total = Math.ceil(response.data.items.total / response.data.items.per_page);
                            vm.items = response.data.items.data;
                            vm.setPagination(response.data.items.current_page, total)
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
                        vm.createData.img_mobile = $('#create-img-mobile').val();
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
                        vm.editData.img_mobile = $('#edit-img-mobile').val();
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

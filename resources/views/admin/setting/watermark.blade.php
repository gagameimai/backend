@extends('admin.layout.master')

@section('nav.setting', 'menu-open')
@section('unit_master.setting', 'active')
@section('unit.watermark', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="setting" unit="watermark" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="alert alert-info">
                        <p class="mb-1"><b>說明</b></p>
                        <ul class="mb-0 pl-3">
                            <li>此處的圖片用於「安卓車框」上傳圖片時自動加上的浮水印，之後更換 LOGO 可自行在此替換。</li>
                            <li>請上傳 <b>透明背景的 PNG</b> 檔（檔案 5MB 內、寬高 20～4000 像素）。</li>
                            <li>只會影響<b>之後新上傳／重新儲存</b>的圖片，已處理過的舊圖不會改變。</li>
                            <li>每次替換會自動備份舊檔；按「還原成預設」可回到系統原本的浮水印。</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-lg-5" v-for="item in items" :key="item.key">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">@{{ item.name }}</h3>
                            <div class="card-tools">
                                <span class="badge" :class="item.is_custom ? 'badge-warning' : 'badge-secondary'">
                                    @{{ item.is_custom ? '自訂' : '預設' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3" style="background-image: linear-gradient(45deg,#ccc 25%,transparent 25%,transparent 75%,#ccc 75%),linear-gradient(45deg,#ccc 25%,#fff 25%,#fff 75%,#ccc 75%); background-size: 20px 20px; background-position: 0 0, 10px 10px; min-height: 120px; padding: 10px;">
                                <img v-if="item.url" :src="item.url" style="max-width:100%; max-height:200px;" alt="目前浮水印">
                                <span v-else class="text-muted">目前沒有圖片</span>
                            </div>
                            <p class="text-muted mb-2" style="font-size:13px;">
                                檔名：@{{ item.file }}　
                                <span v-if="item.updated_at">更新：@{{ item.updated_at }}</span>
                            </p>
                            <div class="form-group mb-0">
                                <input type="file" accept="image/png" class="form-control-file" :id="'file-' + item.key">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" :disabled="busy" v-on:click="upload(item)">
                                <i class="fas fa-upload"></i> 上傳並替換
                            </button>
                            <button type="button" class="btn btn-outline-secondary float-right" :disabled="busy || !item.is_custom" v-on:click="restore(item)">
                                <i class="fas fa-undo"></i> 還原成預設
                            </button>
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
                url: '{{ route('admin.watermark') }}',
                items: [],
                busy: false
            },
            created: function() {
                this.getItems();
            },
            methods: {
                getItems: function() {
                    let vm = this;
                    axios.get(vm.url + '/all').then(function(response) {
                        vm.items = response.data.items;
                    }).catch(function(error) {
                        vm.showMessage('error', vm.errText(error));
                    });
                },
                upload: function(item) {
                    let vm = this;
                    let input = document.getElementById('file-' + item.key);

                    if (!input || !input.files.length) {
                        vm.showMessage('error', '請先選擇 PNG 檔案');
                        return;
                    }

                    let form = new FormData();
                    form.append('file', input.files[0]);
                    vm.busy = true;

                    axios.post(vm.url + '/' + item.key, form).then(function(response) {
                        input.value = '';
                        vm.showMessage('success', response.data.message);
                        vm.getItems();
                    }).catch(function(error) {
                        vm.showMessage('error', vm.errText(error));
                    }).then(function() {
                        vm.busy = false;
                    });
                },
                restore: function(item) {
                    let vm = this;

                    if (!confirm('確定要把「' + item.name + '」還原成預設浮水印嗎？')) {
                        return;
                    }

                    vm.busy = true;
                    axios.delete(vm.url + '/' + item.key).then(function(response) {
                        vm.showMessage('success', response.data.message);
                        vm.getItems();
                    }).catch(function(error) {
                        vm.showMessage('error', vm.errText(error));
                    }).then(function() {
                        vm.busy = false;
                    });
                },
                errText: function(error) {
                    if (error.response && error.response.data) {
                        let d = error.response.data;
                        if (d.errors) {
                            return Object.values(d.errors)[0][0];
                        }
                        if (d.message) {
                            return d.message;
                        }
                    }
                    return '發生錯誤，請稍後再試';
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

@extends('admin.layout.master')

@section('unit.home_section', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="home_section" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="row">
                <div class="col-12">
                    <div class="callout callout-info">
                        <h5 style="margin-bottom:.5rem"><i class="fas fa-info-circle"></i> 首頁滿版區塊說明（請先看這段）</h5>
                        <p style="margin-bottom:.35rem">
                            這裡管理的是首頁 3 段「滿版情境區塊」的內容跟背景圖，就是目前首頁 clarion 滿版區、MM 美邁滿版區、尾端 CTA 滿版區這 3 段。
                            區域不限定一定要放哪個品牌，內容欄位是完整的編輯器，標題、說明文字、按鈕文字等版面全部由你自己排版。
                        </p>
                        <p style="margin-bottom:.35rem">
                            <b>還沒填內容之前，前台會維持目前的預設文字跟預設圖，不會空白</b>，你可以先慢慢編輯，存檔那一刻前台才會換成你排的內容。
                        </p>
                        <p style="margin-bottom:0">
                            背景圖建議跟目前一樣：電腦版 2560×1440（16:9），手機版另外放一張直式 1080×1920；手機版留空會沿用電腦版的圖（構圖可能會被裁到）。
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12" v-for="section in sections" :key="section.key">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-pencil-alt"></i>
                                @{{ section.name }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>區塊內容（CKEditor，自由排版）</label>
                                <textarea class="form-control" :id="'ckeditor-' + section.key"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>背景圖（電腦版）<span class="text-muted">　建議 2560 × 1440（16:9）</span></label>
                                        <div class="input-group">
                                            <input :id="'img-' + section.key" class="form-control" type="text" readonly>
                                            <span class="input-group-btn">
                                                <a :id="'lfm-' + section.key" :data-input="'img-' + section.key" :data-preview="'preview-' + section.key" class="btn btn-block btn-default lfm-trigger">
                                                    <i class="fa fa-picture-o"></i>
                                                    選取檔案
                                                </a>
                                            </span>
                                        </div>
                                        <div :id="'preview-' + section.key" style="margin-top:10px; margin-bottom:10px"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>背景圖（手機版）<span class="text-muted">　建議 1080 × 1920（直式）</span></label>
                                        <div class="input-group">
                                            <input :id="'img-mobile-' + section.key" class="form-control" type="text" readonly>
                                            <span class="input-group-btn">
                                                <a :id="'lfm-mobile-' + section.key" :data-input="'img-mobile-' + section.key" :data-preview="'preview-mobile-' + section.key" class="btn btn-block btn-default lfm-trigger">
                                                    <i class="fa fa-picture-o"></i>
                                                    選取檔案
                                                </a>
                                            </span>
                                        </div>
                                        <small class="form-text text-muted">留空會沿用電腦版的圖。</small>
                                        <div :id="'preview-mobile-' + section.key" style="margin-top:10px; margin-bottom:10px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" @click="saveItem(section.key)">
                                <i class="fas fa-save"></i>
                                儲存
                            </button>
                            <button type="button" class="btn btn-outline-danger" @click="clearItem(section.key)">
                                <i class="fa fa-times"></i>
                                清除這個區塊（恢復前台預設）
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        var vm = new Vue({
            el: '#container',
            data: {
                url: '{{ route('admin.home_section') }}',
                sectionsConfig: @json($sections),
                sections: [],
                items: {},
                ckeditor: {}
            },
            created: function() {
                // 這裡只用 this／self，不要用全域變數 vm ——
                // created()／mounted() 是在 new Vue(...) 建構過程中同步執行，
                // 此時外層 var vm = new Vue(...) 還沒賦值完成，直接呼叫 vm.xxx 會噴錯（跟 list_banner 同一個坑）。
                // 真的需要用到 vm 的非同步呼叫（getItems）才用 $nextTick 延到下一輪。
                var self = this;
                self.sections = Object.keys(self.sectionsConfig).map(function(key) {
                    return { key: key, name: self.sectionsConfig[key].name };
                });
                self.$nextTick(function() {
                    vm.getItems();
                });
            },
            mounted: function() {
                // 3 個區塊各自獨立的 CKEditor 實體與 LFM 上傳按鈕，一次全部初始化（用 self，理由同上）
                var self = this;
                self.sections.forEach(function(section) {
                    self.ckeditor[section.key] = CKEDITOR.replace('ckeditor-' + section.key, ckeditorConfig);
                    $('#lfm-' + section.key).filemanager('file', {prefix: 'filemanager'});
                    $('#lfm-mobile-' + section.key).filemanager('file', {prefix: 'filemanager'});
                });
            },
            methods: {
                getItems: function() {
                    try {
                        axios.get(vm.url + '/all').then(function(response) {
                            response.data.items.forEach(function(row) {
                                vm.items[row.section_key] = row;
                                if (vm.ckeditor[row.section_key]) {
                                    vm.ckeditor[row.section_key].setData(row.content == null ? '' : row.content);
                                }
                                $('#img-' + row.section_key).val(row.img || '');
                                if (row.img) {
                                    $('#preview-' + row.section_key).html('<img src="' + row.img + '" style="max-height:8rem;">');
                                }
                                $('#img-mobile-' + row.section_key).val(row.img_mobile || '');
                                if (row.img_mobile) {
                                    $('#preview-mobile-' + row.section_key).html('<img src="' + row.img_mobile + '" style="max-height:8rem;">');
                                }
                            });
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                saveItem: function(key) {
                    try {
                        var content = vm.ckeditor[key].getData();
                        var img = $('#img-' + key).val();
                        var imgMobile = $('#img-mobile-' + key).val();
                        axios.patch(vm.url + '/' + key, {
                            content: content,
                            img: img,
                            img_mobile: imgMobile
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
                clearItem: function(key) {
                    if (confirm('確定要清除這個區塊？清除後前台會恢復成預設文字跟預設圖。') !== true) return false;

                    try {
                        vm.ckeditor[key].setData('');
                        $('#img-' + key).val('');
                        $('#preview-' + key).html('');
                        $('#img-mobile-' + key).val('');
                        $('#preview-mobile-' + key).html('');
                        axios.patch(vm.url + '/' + key, {
                            content: '',
                            img: '',
                            img_mobile: ''
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

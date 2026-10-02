@extends('admin.layout.master')

@section('nav.setting', 'menu-open')
@section('unit_master.setting', 'active')
@section('unit.content_policy', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="setting" unit="content_policy" />

    <style>
        .cp-tips{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:10px;margin-bottom:16px}
        .cp-tip{border-radius:8px;padding:10px 12px;font-size:13.5px;border-left:5px solid;line-height:1.5}.cp-tip b{display:block}
        .cp-t1{background:#eafaf0;border-color:#34c759}.cp-t2{background:#fff4e0;border-color:#ff9500}.cp-t3{background:#eaf3fb;border-color:#007abe}
        .cp-card{border-radius:10px;overflow:hidden;margin-bottom:16px}
        .cp-hd{padding:11px 16px;font-weight:700;display:flex;align-items:center;gap:10px}
        .cp-num{width:26px;height:26px;border-radius:50%;background:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:14px;box-shadow:0 0 0 1px #d5dbe3}
        .cp-sub{font-size:12.5px;color:#6b7785;margin:3px 0 10px}
        .cp-langs{display:flex;gap:6px;margin-bottom:14px}
        .cp-lang{border:1px solid #cfd6df;background:#fff;border-radius:20px;padding:4px 16px;font-size:13.5px;cursor:pointer;font-weight:700}.cp-lang.on{background:#007abe;color:#fff;border-color:#007abe}
        .cp-lang i{display:inline-block;width:7px;height:7px;border-radius:50%;background:#34c759;margin-left:6px}
        .cp-pane{display:none}.cp-pane.on{display:block}
        .cp-save{position:sticky;bottom:0;background:rgba(244,246,249,.96);padding:12px 0;z-index:5;display:flex;gap:8px;align-items:center}
        .cp-save small{color:#6b7785;margin-left:auto}
    </style>

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="cp-tips">
                <div class="cp-tip cp-t1"><b>這頁是什麼</b>前台頁尾「關於」欄的「內容來源與更正聲明」：說明網站部分內容由 AI 協助製作，若有引用到他人著作，歡迎通知，我們願意下架或更正。</div>
                <div class="cp-tip cp-t2"><b>中文、英文各一份</b>前台切換「繁中／EN」會各自顯示對應語言。兩邊都要改，英文沒改的話前台英文版就還是舊的。</div>
                <div class="cp-tip cp-t3"><b>留空＝用內建文字</b>任何欄位清空存檔，前台就顯示程式內建的預設文字；想回到最初版本按「還原預設」。E-mail 與電話前台會自動帶「網站基本設定」。</div>
            </div>

            <form v-on:submit.prevent="updateItem()">
                <div class="cp-langs">
                    <button type="button" class="cp-lang" :class="{on: cur === 'zh'}" @click="switchLang('zh')">中文<i v-if="filled('zh')"></i></button>
                    <button type="button" class="cp-lang" :class="{on: cur === 'en'}" @click="switchLang('en')">English<i v-if="filled('en')"></i></button>
                </div>

                <div v-for="lang in ['zh', 'en']" :key="lang" class="cp-pane" :class="{on: cur === lang}">
                    <div class="cp-card card">
                        <div class="cp-hd" style="background:#eaf3fb"><span class="cp-num">1</span>標題帶（@{{ lang === 'zh' ? '中文' : 'English' }}）</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>頁面標題</label>
                                <input type="text" class="form-control" v-model="content[lang].title" :placeholder="defaults[lang].title">
                                <div class="cp-sub">顯示在頁面最上方的大標題，也是瀏覽器分頁名稱。留空＝「@{{ defaults[lang].title }}」。</div>
                            </div>
                            <div class="form-group">
                                <label>標題下方的說明</label>
                                <textarea class="form-control" rows="3" v-model="content[lang].intro" :placeholder="defaults[lang].intro"></textarea>
                                <div class="cp-sub">一到三句，講清楚這頁要說什麼。留空＝用內建文字。</div>
                            </div>
                        </div>
                    </div>

                    <div class="cp-card card">
                        <div class="cp-hd" style="background:#f1eafb"><span class="cp-num">2</span>內文（@{{ lang === 'zh' ? '中文' : 'English' }}）</div>
                        <div class="card-body">
                            <div class="cp-sub">用「標題 2」當段落標題（為什麼需要這份聲明、我們的承諾、如何通知我們、處理流程、其他說明），前台會自動套用版面。E-mail 與電話不用自己寫：在想放聯絡卡片的位置單獨打一行 [聯絡方式]（English 分頁打 [contact]），前台會自動換成卡片；沒打的話卡片會接在內文最後面。</div>
                            <textarea class="form-control" :id="'ckeditor-' + lang"></textarea>
                        </div>
                    </div>
                </div>

                <div class="cp-save">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 儲存（中英文一起存）</button>
                    <button type="button" class="btn btn-outline-secondary" @click="restoreDefault()">還原預設（目前語言）</button>
                    <small v-if="updatedAt">上次儲存：@{{ updatedAt }}</small>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        var vm = new Vue({
            el: '#container',
            data: {
                url: '{{ route('admin.content_policy') }}',
                cur: 'zh',
                content: { zh: { title: '', intro: '', body: '' }, en: { title: '', intro: '', body: '' } },
                defaults: { zh: { title: '', intro: '', body: '' }, en: { title: '', intro: '', body: '' } },
                updatedAt: null,
                editors: {}
            },
            created: function() {
                this.getItem();
            },
            mounted: function() {
                this.editors.zh = CKEDITOR.replace('ckeditor-zh', ckeditorConfig);
                this.editors.en = CKEDITOR.replace('ckeditor-en', ckeditorConfig);
            },
            methods: {
                getItem: function() {
                    let vm = this;
                    axios.get(vm.url + '/all').then(function(response) {
                        vm.content = response.data.item.content;
                        vm.updatedAt = response.data.item.updated_at;
                        if (response.data.defaults) vm.defaults = response.data.defaults;
                        vm.editors.zh.setData(vm.content.zh.body || '');
                        vm.editors.en.setData(vm.content.en.body || '');
                    }).catch(function(error) {
                        vm.showMessage('error', error.response ? error.response.data.message : error);
                    });
                },
                switchLang: function(lang) {
                    this.cur = lang;
                    let ed = this.editors[lang];
                    // 隱藏中建立的 CKEditor 顯示後要重新量高度
                    if (ed && ed.resize) setTimeout(function() { try { ed.resize('100%', 400); } catch (e) {} }, 50);
                },
                filled: function(lang) {
                    let c = this.content[lang];
                    return !!(c.title || c.intro || (this.editors[lang] && this.editors[lang].getData && this.editors[lang].getData().trim()));
                },
                restoreDefault: function() {
                    let lang = this.cur;
                    if (!confirm((lang === 'zh' ? '中文' : '英文') + '的標題、說明、內文都會換回程式內建的預設文字（還沒按儲存前不會真的改到）。確定嗎？')) return;
                    this.content[lang].title = this.defaults[lang].title;
                    this.content[lang].intro = this.defaults[lang].intro;
                    this.editors[lang].setData(this.defaults[lang].body || '');
                },
                updateItem: function() {
                    let vm = this;
                    let payload = {
                        zh: { title: vm.content.zh.title, intro: vm.content.zh.intro, body: vm.editors.zh.getData() },
                        en: { title: vm.content.en.title, intro: vm.content.en.intro, body: vm.editors.en.getData() }
                    };
                    axios.patch(vm.url, { content: payload }).then(function(response) {
                        vm.showMessage('success', response.data.message);
                        vm.getItem();
                    }).catch(function(error) {
                        vm.showMessage('error', error.response ? error.response.data.message : error);
                    });
                },
                showMessage: function(format, message) {
                    if (format == 'success') { toastr.success(message); } else { toastr.warning(message); }
                }
            }
        });
    </script>
@endsection

@extends('admin.layout.master')

@section('nav.setting', 'menu-open')
@section('unit_master.setting', 'active')
@section('unit.website', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="setting" unit="website" />

    <style>
        .th-color{display:flex;align-items:center;flex-wrap:wrap;gap:6px}
        .th-sw{width:30px;height:30px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 0 1px #aab4c0;cursor:pointer;padding:0}
        .th-sw.on{box-shadow:0 0 0 3px #007abe}
        .th-def{width:auto;border-radius:15px;padding:0 12px;background:#fff;font-size:12px;color:#495563}
        .th-pick{display:inline-flex;align-items:center;gap:4px;margin:0;cursor:pointer;color:#0b5c8a}
        .th-pick input{width:30px;height:30px;border:0;padding:0;background:none;cursor:pointer}
        .th-hex{color:#8a94a0;font-size:12px;margin-left:4px}
        .th-pv{border-radius:8px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.25);color:#fff;font-size:13px;text-align:center}
        .th-pv-nav{background:#fff;color:#333;padding:8px;border-bottom:1px solid #e5e8ec}
        .th-pv-mid{padding:20px 10px;color:#41506b}.th-pv-btn{display:inline-block;color:#fff;border-radius:6px;padding:3px 12px;margin-left:8px}
        .th-pv-hero{padding:46px 10px}.th-pv-foot{padding:18px 10px;color:#9aa6b5}
        .lg-box{border:2px solid #e3e8ee;border-radius:12px;padding:12px;height:100%;background:#fff}
        .lg-hd{margin-bottom:8px;font-size:15px}.lg-pv{border-radius:8px;border:1px dashed #b9c3cf;min-height:84px;display:flex;align-items:center;justify-content:center;padding:10px;margin-bottom:8px}
        .lg-pv img{max-width:100%;height:auto}.lg-empty{font-size:13px}
        .lg-spec{margin-bottom:4px}.lg-chip{display:inline-block;background:#eaf3fb;color:#0b5c8a;border-radius:20px;padding:1px 10px;font-size:12px;margin-right:4px}
        .lg-where{font-size:12.5px;color:#6b7785;margin-bottom:8px;line-height:1.5}
        .bn-upbtn{display:inline-block;background:#fff;border:2px solid #007abe;color:#007abe;border-radius:8px;padding:7px 14px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none!important;margin-right:6px}
        .bn-upbtn.has{background:#007abe;color:#fff}
    </style>

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            {{-- update --}}
            <div class="row justify-content-center">
                <div class="col-12 col-sm-8">
                    <form v-on:submit.prevent="updateItem()">
                        <div class="card card-primary">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>Copyright</label>
                                            <input type="text" class="form-control" v-model="item.content.copyright"/>
                                        </div>
                                    </div>
                                      <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>公司地址</label>
                                            <input type="text" class="form-control" v-model="item.content.address"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>聯絡電話</label>
                                            <input type="text" class="form-control" v-model="item.content.tel"/>
                                        </div>
                                    </div>
                                      <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" v-model="item.content.email"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Facebook</label>
                                            <input type="url" class="form-control" v-model="item.content.facebook"/>
                                        </div>
                                    </div>
                                      <div class="col-12">
                                        <div class="form-group">
                                            <label>Instagram</label>
                                            <input type="url" class="form-control" v-model="item.content.instagram"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Youtube</label>
                                            <input type="url" class="form-control" v-model="item.content.youtube"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    更新
                                </button>
                            </div>
                        </div>
                    <div class="card card-primary" style="margin-top:16px">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-image"></i> 全站標誌圖片（Logo、分頁小圖示）</h3></div>
                        <div class="card-body">
                            <div class="alert alert-info" style="margin-bottom:14px">
                                這裡換圖，前台的<b>瀏覽器分頁小圖示、網站左上角標誌、頁尾標誌</b>就會跟著換，不用找工程師。<br>
                                <b>沒上傳＝維持目前的樣子</b>，想還原按「還原預設」。建議用 <b>SVG</b>（放大不會糊）；PNG 請用<b>透明背景</b>、至少 2 倍大。換好存檔後，前台重新整理才會看到。
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 col-xl-4" v-for="lg in logoFields" :key="lg.key" style="margin-bottom:16px">
                                    <div class="lg-box">
                                        <div class="lg-hd"><b>@{{ lg.name }}</b></div>
                                        <div class="lg-pv" :style="{background: lg.bg}">
                                            <img v-if="item.content[lg.key]" :src="item.content[lg.key]" :style="{maxHeight: lg.maxh + 'px'}" alt="">
                                            <span v-else class="lg-empty" :style="{color: lg.bg === '#fff' ? '#9aa5b1' : '#9fb3c8'}">使用預設圖</span>
                                        </div>
                                        <div class="lg-spec">
                                            <span class="lg-chip">@{{ lg.size }}</span><span class="lg-chip">@{{ lg.fmt }}</span>
                                        </div>
                                        <div class="lg-where">@{{ lg.where }}</div>
                                        <input :id="'lg-' + lg.key" type="hidden" :value="item.content[lg.key]">
                                        <a :id="'lfm-' + lg.key" :data-input="'lg-' + lg.key" class="bn-upbtn" :class="{has: item.content[lg.key]}"><i class="fa fa-picture-o"></i> @{{ item.content[lg.key] ? '更換圖片' : '選取／上傳圖片' }}</a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" v-if="item.content[lg.key]" @click="setTheme(lg.key, '')">還原預設</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 更新</button>
                        </div>
                    </div>
                    <div class="card card-primary" style="margin-top:16px">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-palette"></i> 全站底色</h3></div>
                        <div class="card-body">
                            <div class="alert alert-info" style="margin-bottom:14px">
                                這裡改的顏色會<b>套用到整個網站</b>（所有頁面的深色底、Banner 與區塊底下的底色、頁尾），改完存檔，前台就會換。<br>
                                <b>深色底色請選深色系</b>：深色區塊上的字是白色，選太亮會看不清楚；淺灰區塊底色請選淺色。不想改就都選「預設」。<br>
                                <span class="text-muted">目前不包含：文字顏色（白字／深色字）、純白底。如果要整站改成淺色風格，需要另外請工程師調整版面。</span>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>全站深色底色</label>
                                        <div class="th-color">
                                            <button type="button" class="th-sw th-def" :class="{on: !item.content.theme_dark}" @click="setTheme('theme_dark', '')">預設</button>
                                            <button type="button" v-for="c in darkPresets" :key="c.v" class="th-sw" :class="{on: item.content.theme_dark === c.v}" :style="{background: c.v}" :title="c.n" @click="setTheme('theme_dark', c.v)"></button>
                                            <label class="th-pick"><input type="color" :value="item.content.theme_dark || '#0d1016'" @input="setTheme('theme_dark', $event.target.value)">自選</label>
                                            <span class="th-hex">@{{ item.content.theme_dark || '預設 #0D1016' }}</span>
                                        </div>
                                        <small class="text-muted">首頁各區塊底下、各產品頁橫幅、導覽等所有深色背景。預設 <b>#0D1016</b>（歌樂深色底）。</small>
                                    </div>
                                    <div class="form-group">
                                        <label>重點色（按鈕、連結、選取狀態）</label>
                                        <div class="th-color">
                                            <button type="button" class="th-sw th-def" :class="{on: !item.content.theme_accent}" @click="setTheme('theme_accent', '')">預設</button>
                                            <button type="button" v-for="c in accentPresets" :key="c.v" class="th-sw" :class="{on: item.content.theme_accent === c.v}" :style="{background: c.v}" :title="c.n" @click="setTheme('theme_accent', c.v)"></button>
                                            <label class="th-pick"><input type="color" :value="item.content.theme_accent || '#007abe'" @input="setTheme('theme_accent', $event.target.value)">自選</label>
                                            <span class="th-hex">@{{ item.content.theme_accent || '預設 #007ABE' }}</span>
                                        </div>
                                        <small class="text-muted">按鈕、連結、標籤、選中狀態用的顏色。預設 <b>#007ABE</b>（歌樂 Azzurro 藍）。滑過、淡色版會自動跟著算出來。</small>
                                    </div>
                                    <div class="form-group">
                                        <label>淺灰區塊底色</label>
                                        <div class="th-color">
                                            <button type="button" class="th-sw th-def" :class="{on: !item.content.theme_bg2}" @click="setTheme('theme_bg2', '')">預設</button>
                                            <button type="button" v-for="c in bg2Presets" :key="c.v" class="th-sw" :class="{on: item.content.theme_bg2 === c.v}" :style="{background: c.v}" :title="c.n" @click="setTheme('theme_bg2', c.v)"></button>
                                            <label class="th-pick"><input type="color" :value="item.content.theme_bg2 || '#f5f7fa'" @input="setTheme('theme_bg2', $event.target.value)">自選</label>
                                            <span class="th-hex">@{{ item.content.theme_bg2 || '預設 #F5F7FA' }}</span>
                                        </div>
                                        <small class="text-muted">各產品頁裡交錯出現的淺灰色區塊底（常見問題、說明區等）。預設 <b>#F5F7FA</b>。</small>
                                    </div>
                                    <div class="form-group">
                                        <label>頁尾底色</label>
                                        <div class="th-color">
                                            <button type="button" class="th-sw th-def" :class="{on: !item.content.theme_footer}" @click="setTheme('theme_footer', '')">預設</button>
                                            <button type="button" v-for="c in darkPresets" :key="c.v" class="th-sw" :class="{on: item.content.theme_footer === c.v}" :style="{background: c.v}" :title="c.n" @click="setTheme('theme_footer', c.v)"></button>
                                            <label class="th-pick"><input type="color" :value="item.content.theme_footer || '#0d1016'" @input="setTheme('theme_footer', $event.target.value)">自選</label>
                                            <span class="th-hex">@{{ item.content.theme_footer || '預設 #0D1016' }}</span>
                                        </div>
                                        <small class="text-muted">只改最下面的頁尾。不選的話，頁尾跟著上面的「全站深色底色」。</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label>預覽（示意）</label>
                                    <div class="th-pv">
                                        <div class="th-pv-nav">導覽列</div>
                                        <div class="th-pv-hero" :style="{background: item.content.theme_dark || '#0d1016'}">首頁大 Banner　／　各區塊底色</div>
                                        <div class="th-pv-mid" :style="{background: item.content.theme_bg2 || '#f5f7fa'}">淺灰區塊　<span class="th-pv-btn" :style="{background: item.content.theme_accent || '#007abe'}">重點色按鈕</span></div>
                                        <div class="th-pv-foot" :style="{background: item.content.theme_footer || item.content.theme_dark || '#0d1016'}">頁尾　Clarion × MM　美邁車用電子</div>
                                    </div>
                                    <div class="text-muted" style="font-size:12px;margin-top:6px">每個區塊還可以在「首頁滿版區塊管理」各自改成不同底色，沒改的就用這裡的全站深色底色。</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 更新</button>
                        </div>
                    </div>
                    </form>
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
                url: '{{ route('admin.website') }}',
                item: {
                    content: {}
                },
                logoFields: [
                    { key: 'logo_favicon', name: '① 瀏覽器分頁小圖示', bg: '#fff', maxh: 64, size: '正方形 512×512', fmt: 'PNG', where: '瀏覽器分頁標題旁的小圖。只要傳一張大圖，系統會自動縮成各種小尺寸。' },
                    { key: 'logo_cobrand_dark', name: '② 左上角標誌（歌樂 × MM・白底用）', bg: '#fff', maxh: 50, size: '橫式約 1750×300', fmt: 'SVG／透明 PNG・深色字', where: '網站白底表頭左上角，首頁、MM 專區、經銷據點等共用頁面使用。' },
                    { key: 'logo_cobrand_white', name: '③ 左上角標誌（歌樂 × MM・透明浮動用）', bg: '#0d1016', maxh: 50, size: '同②尺寸', fmt: 'SVG／透明 PNG・白色字', where: '首頁表頭一開始透明、浮在大 Banner 上時使用（捲動後會換回②）。' },
                    { key: 'logo_clarion_dark', name: '④ 歌樂專區標誌（白底用）', bg: '#fff', maxh: 50, size: '橫式約 1260×330', fmt: 'SVG／透明 PNG・深色字', where: '進入 /clarion 歌樂專區頁面時，白底表頭使用。' },
                    { key: 'logo_clarion_white', name: '⑤ 歌樂專區標誌（透明浮動用）', bg: '#0d1016', maxh: 50, size: '同④尺寸', fmt: 'SVG／透明 PNG・白色字', where: '歌樂專區頁面表頭透明時使用。' },
                    { key: 'logo_footer', name: '⑥ 頁尾標誌', bg: '#0d1016', maxh: 50, size: '橫式約 1750×300', fmt: 'SVG／透明 PNG・白色字', where: '全站最下方深色頁尾左側。' }
                ],
                accentPresets: [
                    { v: '#007ABE', n: '歌樂 Azzurro（預設）' },
                    { v: '#F28729', n: 'MM 橘' },
                    { v: '#AF47D2', n: 'MM 藍紫' },
                    { v: '#E53935', n: '紅' },
                    { v: '#00A86B', n: '綠' },
                    { v: '#1565C0', n: '深藍' }
                ],
                bg2Presets: [
                    { v: '#F5F7FA', n: '淺灰（預設）' },
                    { v: '#EEF2F7', n: '淺藍灰' },
                    { v: '#F7F3EC', n: '米色' },
                    { v: '#FFFFFF', n: '純白' },
                    { v: '#E9EDF2', n: '中淺灰' }
                ],
                darkPresets: [
                    { v: '#0D1016', n: '歌樂深色（預設）' },
                    { v: '#0B1F33', n: '深海藍' },
                    { v: '#111827', n: '石墨藍灰' },
                    { v: '#1A1A1A', n: '炭黑' },
                    { v: '#14213D', n: '午夜藍' },
                    { v: '#1B1B2F', n: '暗夜紫藍' }
                ],
            },
            created: function() {
                this.getItem();
            },
            mounted: function() {
                var self = this;
                this.logoFields.forEach(function(lg) {
                    $('#lfm-' + lg.key).filemanager('file', {prefix: 'filemanager'});
                    $('#lg-' + lg.key).on('change', function() { self.$set(self.item.content, lg.key, $(this).val()); });
                });
            },
            methods: {
                getItem: function() {
                    let vm = this;

                    try {
                        axios.get(vm.url + '/all').then(function(response) {
                            vm.item = response.data.item;
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                updateItem: function(id) {
                    try {
                        axios.patch(vm.url, {
                            content: vm.item.content
                        }).then(function(response) {
                            vm.showMessage('success', response.data.message);
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                setTheme: function(key, val) {
                    // content 原本沒有這個鍵時要用 $set 才會即時更新畫面；空字串＝用預設
                    this.$set(this.item.content, key, val);
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

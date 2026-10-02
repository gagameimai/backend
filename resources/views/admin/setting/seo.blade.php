@extends('admin.layout.master')

@section('nav.setting', 'menu-open')
@section('unit_master.setting', 'active')
@section('unit.seo', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="setting" unit="seo" />

    <style>
        .sg-tips{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:10px;margin-bottom:16px}
        .sg-tip{border-radius:8px;padding:10px 12px;font-size:13.5px;border-left:5px solid;line-height:1.5}.sg-tip b{display:block}
        .sg-t1{background:#eafaf0;border-color:#34c759}.sg-t2{background:#fff4e0;border-color:#ff9500}.sg-t3{background:#eaf3fb;border-color:#007abe}
        .sg-card{border-radius:10px;overflow:hidden;margin-bottom:16px}
        .sg-hd{padding:11px 16px;font-weight:700;display:flex;align-items:center;gap:10px}
        .sg-num{width:26px;height:26px;border-radius:50%;background:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:14px;box-shadow:0 0 0 1px #d5dbe3}
        .sg-sub{font-size:12.5px;color:#6b7785;margin:3px 0 10px}
        .sg-cnt{float:right;font-weight:400;font-size:12px;color:#6b7785}.sg-cnt.ok{color:#28a745}.sg-cnt.bad{color:#e53935}
        .sg-pages{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
        .sg-pg{border:1px solid #cfd6df;background:#fff;border-radius:20px;padding:3px 12px;font-size:13px;cursor:pointer}.sg-pg.on{background:#007abe;color:#fff;border-color:#007abe}
        .sg-pg i{display:inline-block;width:7px;height:7px;border-radius:50%;background:#34c759;margin-left:5px}
        .sg-serp{border:1px solid #e3e8ee;border-radius:10px;padding:12px 14px;background:#fff}
        .sg-serp .u{font-size:12.5px;color:#4d5156}.sg-serp .t{font-size:19px;color:#1a0dab;margin:2px 0;line-height:1.3}.sg-serp .d{font-size:13.5px;color:#4d5156;line-height:1.5}
        .sg-chip{display:inline-block;background:#eaf3fb;color:#0b5c8a;border-radius:20px;padding:1px 10px;font-size:12px;margin-right:4px}
        .sg-faq{border:1px solid #e3e8ee;border-radius:8px;padding:10px;margin-bottom:8px;background:#fff}
        .sg-trow{display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid #eef1f5}.sg-trow:last-child{border:0}.sg-trow .n{flex:1}.sg-trow small{color:#7d8794;display:block}
        .sg-og{border:1px solid #e3e8ee;border-radius:10px;overflow:hidden;max-width:420px;background:#fff}
        .sg-og .im{aspect-ratio:1.91/1;background:#0d1016 center/cover no-repeat;color:#9fb3c8;display:flex;align-items:center;justify-content:center;font-size:13px}
        .sg-og .tx{padding:8px 12px;background:#f2f3f5;font-size:13px}
        .sg-warn{background:#fff4e0;border-radius:6px;padding:6px 10px;font-size:12.5px;margin-top:6px}
        .sg-save{position:sticky;bottom:0;background:rgba(244,246,249,.96);padding:12px 0;z-index:5}
        .bn-upbtn{display:inline-block;background:#fff;border:2px solid #007abe;color:#007abe;border-radius:8px;padding:7px 14px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none!important;margin-right:6px}
        .bn-upbtn.has{background:#007abe;color:#fff}
    </style>

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="sg-tips">
                <div class="sg-tip sg-t1"><b>SEO＝讓 Google 搜得到</b>改「每頁標題與說明」，就是別人在 Google 看到的那兩行字。</div>
                <div class="sg-tip sg-t2"><b>GEO＝讓 AI 講對你</b>ChatGPT、Gemini 等會讀「公司基本資料」與「常見問答」，寫對、寫一致，AI 才不會講錯。</div>
                <div class="sg-tip sg-t3"><b>沒填＝維持現在的文字</b>每一欄留空，前台就沿用網站原本的內容；填了就改成你寫的。改完存檔，前台重新整理（或重新部署）後生效。</div>
            </div>

            <div class="sg-card card">
                <div class="sg-hd" style="background:#eaf3fb"><span class="sg-num">1</span>公司基本資料（AI 與 Google 商家最重視，全站共用一份）</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6 form-group"><label>公司中文名稱</label><input type="text" class="form-control" v-model="seo.company_zh" placeholder="美邁車用電子有限公司"></div>
                        <div class="col-12 col-md-6 form-group"><label>公司英文名稱</label><input type="text" class="form-control" v-model="seo.company_en" placeholder="MEIMAI Vehicle Electronics Co., Ltd."></div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 form-group"><label>電話（國際格式）</label><input type="text" class="form-control" v-model="seo.phone_intl" placeholder="+886-3-2170098"><div class="sg-sub">國際格式給 Google 與 AI 讀；網頁上顯示的電話在「網站基本設定」。</div></div>
                        <div class="col-4 col-md-2 form-group"><label>縣市</label><input type="text" class="form-control" v-model="seo.addr_region" placeholder="桃園市"></div>
                        <div class="col-4 col-md-2 form-group"><label>區</label><input type="text" class="form-control" v-model="seo.addr_locality" placeholder="桃園區"></div>
                        <div class="col-4 col-md-2 form-group"><label>路段門牌</label><input type="text" class="form-control" v-model="seo.addr_street" placeholder="國信街35號"></div>
                    </div>
                    <div class="sg-warn">提醒：名稱、電話、地址要和 Google 商家、Facebook 上寫的「一個字都不差」，差一個字會被當成兩間公司。留空＝使用網站原本的資料。</div>
                </div>
            </div>

            <div class="sg-card card">
                <div class="sg-hd" style="background:#f1eafb"><span class="sg-num">2</span>每頁的搜尋標題與說明（Google 搜尋結果看到的）</div>
                <div class="card-body">
                    <div class="sg-pages">
                        <button type="button" class="sg-pg" v-for="(p, i) in pages" :key="p.path" :class="{on: cur === i}" @click="cur = i">@{{ p.name }}<i v-if="has(p.path)"></i></button>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <label>標題 <span class="sg-cnt" :class="titleOk ? 'ok' : 'bad'">@{{ curTitle.length }} 字</span></label>
                            <input type="text" class="form-control" :value="curTitle" @input="setPage('title', $event.target.value)" placeholder="留空＝用這一頁目前的標題">
                            <div class="sg-sub">建議 10～30 字。多數頁面會自動在後面接上網站名稱「｜Clarion 歌樂 台灣官方授權總經銷｜美邁車用電子」，不用重寫（預覽會一起顯示）。</div>
                            <label>說明 <span class="sg-cnt" :class="descOk ? 'ok' : 'bad'">@{{ curDesc.length }} 字</span></label>
                            <textarea class="form-control" rows="4" :value="curDesc" @input="setPage('description', $event.target.value)" placeholder="留空＝用這一頁目前的說明"></textarea>
                            <div class="sg-sub">建議 60～110 字（繁中超過約 110 字 Google 會截斷）。寫「誰、賣什麼、哪裡買」。</div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="clearPage()">還原這頁（清空＝沿用目前文字）</button>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label>Google 搜尋結果預覽</label>
                            <div class="sg-serp">
                                <div class="u">clarion.meimai.com.tw › @{{ pages[cur].path === '/' ? '' : pages[cur].path.substring(1).replace(/\//g, ' › ') }}</div>
                                <div class="t">@{{ curTitle ? curTitle + suffix : '（沿用這一頁目前的標題）' }}</div>
                                <div class="d">@{{ curDesc || '（沿用這一頁目前的說明）' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sg-card card">
                <div class="sg-hd" style="background:#e9f7ee"><span class="sg-num">3</span>分享圖片（貼到 LINE、Facebook 看到的圖）</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label>預設分享圖 <span class="sg-chip">1200 × 630</span><span class="sg-chip">JPG／PNG</span><span class="sg-chip">1MB 內</span></label>
                            <div><input id="seo-og" type="hidden" :value="seo.og_image">
                                <a id="lfm-seo-og" data-input="seo-og" class="bn-upbtn" :class="{has: seo.og_image}"><i class="fa fa-picture-o"></i> @{{ seo.og_image ? '更換圖片' : '選取／上傳圖片' }}</a>
                                <button type="button" class="btn btn-sm btn-outline-secondary" v-if="seo.og_image" @click="seo.og_image = ''">還原預設</button></div>
                            <div class="sg-sub" style="margin-top:8px">文字與主體放中間，上下左右各留 10% 邊。沒上傳＝用現在的歌樂分享圖。有自己分享圖的頁面（例如商品頁）仍用自己的。</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="sg-og"><div class="im" :style="seo.og_image ? {backgroundImage: 'url(' + seo.og_image + ')'} : null">@{{ seo.og_image ? '' : '目前使用預設分享圖' }}</div><div class="tx"><small>clarion.meimai.com.tw</small><br><b>@{{ curTitle || '頁面標題' }}</b></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sg-card card">
                <div class="sg-hd" style="background:#fff0e0"><span class="sg-num">4</span>AI 常見問答（會顯示在「關於我們」頁，也給 AI 讀）</div>
                <div class="card-body">
                    <div class="sg-sub">沒新增任何一組＝使用網站內建的 3 則問答（總經銷是誰／如何確認原廠公司貨／保固怎麼算）。Google 規定問答必須是頁面上看得到的文字，所以這裡寫的會同時顯示在頁面與給 AI 的資料裡。</div>
                    <div class="sg-faq" v-for="(f, i) in seo.faq" :key="i">
                        <input type="text" class="form-control" v-model="f.q" placeholder="問題，例如：Clarion 歌樂在台灣的官方總經銷是誰？" style="margin-bottom:6px">
                        <textarea class="form-control" rows="3" v-model="f.a" placeholder="回答"></textarea>
                        <div style="text-align:right;margin-top:6px">
                            <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="i === 0" @click="moveFaq(i, -1)">↑</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="i === seo.faq.length - 1" @click="moveFaq(i, 1)">↓</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" @click="seo.faq.splice(i, 1)">刪除</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" @click="seo.faq.push({q: '', a: ''})">＋ 新增一組問答</button>
                </div>
            </div>

            <div class="sg-card card">
                <div class="sg-hd" style="background:#eef0f4"><span class="sg-num">5</span>進階（一般不用動）</div>
                <div class="card-body">
                    <div class="sg-trow"><div class="n"><b>公司結構化資料（Organization）</b><small>讓 Google／AI 認得「這間公司是誰、在哪」。建議開啟。</small></div>
                        <label class="sw-toggle"><input type="checkbox" v-model="seo.flags.org" :true-value="1" :false-value="0"><span class="sw-slider"></span></label></div>
                    <div class="sg-trow"><div class="n"><b>商品結構化資料（Product）</b><small>商品詳情頁；有價格才會輸出價格。建議開啟。</small></div>
                        <label class="sw-toggle"><input type="checkbox" v-model="seo.flags.product" :true-value="1" :false-value="0"><span class="sw-slider"></span></label></div>
                    <div class="sg-trow"><div class="n"><b>允許 AI 爬蟲讀取網站（robots.txt）</b><small>關閉＝ChatGPT、Claude、Gemini、Perplexity 等不會收錄，不建議關。需重新部署才生效。</small></div>
                        <label class="sw-toggle"><input type="checkbox" v-model="seo.flags.ai_bots" :true-value="1" :false-value="0"><span class="sw-slider"></span></label></div>
                    <div class="row" style="margin-top:12px">
                        <div class="col-12 col-md-6 form-group"><label>Google Search Console 驗證碼</label><input type="text" class="form-control" v-model="seo.gsc" placeholder="貼上 google-site-verification 的內容"></div>
                        <div class="col-12 col-md-6 form-group"><label>Bing 驗證碼</label><input type="text" class="form-control" v-model="seo.bing" placeholder="貼上 msvalidate.01 的內容"></div>
                    </div>
                </div>
            </div>

            <div class="sg-save"><button type="button" class="btn btn-success btn-lg" @click="save()" :disabled="saving">儲存設定</button></div>
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
                pages: @json($pages),
                suffix: '｜Clarion 歌樂 台灣官方授權總經銷｜美邁車用電子',
                content: {},
                cur: 0,
                saving: false,
                seo: { company_zh: '', company_en: '', phone_intl: '', addr_region: '', addr_locality: '', addr_street: '', og_image: '', gsc: '', bing: '', flags: { org: 1, product: 1, ai_bots: 1 }, pages: {}, faq: [] }
            },
            computed: {
                curPath: function () { return this.pages[this.cur].path; },
                curTitle: function () { return (this.seo.pages[this.curPath] || {}).title || ''; },
                curDesc: function () { return (this.seo.pages[this.curPath] || {}).description || ''; },
                titleOk: function () { return this.curTitle.length === 0 || (this.curTitle.length >= 10 && this.curTitle.length <= 30); },
                descOk: function () { return this.curDesc.length === 0 || (this.curDesc.length >= 60 && this.curDesc.length <= 110); }
            },
            created: function () {
                var self = this;
                axios.get(this.url + '/all').then(function (res) {
                    self.content = res.data.item.content || {};
                    var s = self.content.seo || {};
                    var f = s.flags || {};
                    self.seo = {
                        company_zh: s.company_zh || '', company_en: s.company_en || '', phone_intl: s.phone_intl || '',
                        addr_region: s.addr_region || '', addr_locality: s.addr_locality || '', addr_street: s.addr_street || '',
                        og_image: s.og_image || '', gsc: s.gsc || '', bing: s.bing || '',
                        flags: { org: f.org === 0 ? 0 : 1, product: f.product === 0 ? 0 : 1, ai_bots: f.ai_bots === 0 ? 0 : 1 },
                        pages: JSON.parse(JSON.stringify(s.pages && !Array.isArray(s.pages) ? s.pages : {})),
                        faq: JSON.parse(JSON.stringify(s.faq || []))
                    };
                }).catch(function () { toastr.error('讀取設定失敗，請重新整理'); });
            },
            mounted: function () {
                var self = this;
                $('#lfm-seo-og').filemanager('file', {prefix: 'filemanager'});
                $('#seo-og').on('change', function () { self.seo.og_image = $(this).val(); });
            },
            methods: {
                has: function (path) { var p = this.seo.pages[path]; return !!(p && (p.title || p.description)); },
                setPage: function (key, val) {
                    var cur = this.seo.pages[this.curPath] || { title: '', description: '' };
                    var n = { title: cur.title || '', description: cur.description || '' };
                    n[key] = val;
                    this.$set(this.seo.pages, this.curPath, n);
                },
                clearPage: function () { this.$delete(this.seo.pages, this.curPath); },
                moveFaq: function (i, d) {
                    var j = i + d; if (j < 0 || j >= this.seo.faq.length) return;
                    var t = this.seo.faq.splice(i, 1)[0]; this.seo.faq.splice(j, 0, t);
                },
                save: function () {
                    var self = this;
                    for (var i = 0; i < this.seo.faq.length; i++) {
                        var f = this.seo.faq[i];
                        if ((f.q || '').trim() === '' !== ((f.a || '').trim() === '')) {
                            toastr.error('第 ' + (i + 1) + ' 組問答的「問題」與「回答」要一起填（或一起刪掉）');
                            return;
                        }
                    }
                    var content = JSON.parse(JSON.stringify(this.content));
                    content.seo = JSON.parse(JSON.stringify(this.seo));
                    self.saving = true;
                    axios.patch(this.url, { content: content }).then(function () {
                        self.content = content;
                        toastr.success('已儲存');
                    }).catch(function (err) {
                        toastr.error((err.response && err.response.data && err.response.data.message) || '儲存失敗');
                    }).then(function () { self.saving = false; });
                }
            }
        });
    </script>
@endsection

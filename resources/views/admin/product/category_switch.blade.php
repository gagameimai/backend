@extends('admin.layout.master')

@section('nav.product', 'menu-open')
@section('unit_master.product', 'active')
@section('unit.product_category', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="product" unit="product_category" />

    <style>
        .cs-tips{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:10px;margin-bottom:16px}
        .cs-tip{border-radius:8px;padding:10px 12px;font-size:13.5px;border-left:5px solid;line-height:1.5}
        .cs-tip b{display:block}.cs-t1{background:#eafaf0;border-color:#34c759}.cs-t2{background:#fff4e0;border-color:#ff9500}.cs-t3{background:#eaf3fb;border-color:#007abe}.cs-t4{background:#f1eafb;border-color:#8e5cd9}
        .cs-card{border-radius:10px;overflow:hidden;margin-bottom:16px}
        .cs-hd{padding:11px 16px;font-weight:700;color:#fff;display:flex;justify-content:space-between;align-items:center}
        .cs-bd{display:flex;gap:18px;flex-wrap:wrap;padding:14px 16px}
        .cs-list{flex:1.6;min-width:320px}.cs-pv{flex:1;min-width:250px;background:#0d1016;border-radius:10px;padding:12px;color:#fff;align-self:flex-start}
        .cs-row{display:flex;align-items:center;gap:8px;padding:8px 4px;border-bottom:1px solid #eef1f5;background:#fff}
        .cs-row.drag-over{background:#eaf3fb}.cs-row.off .cs-name{opacity:.55}
        .cs-handle{cursor:grab;color:#9aa5b1;font-size:18px;padding:0 4px;user-select:none}
        .cs-arrows button{border:1px solid #d5dbe3;background:#fff;border-radius:4px;width:26px;height:22px;line-height:1;padding:0;cursor:pointer;display:block}
        .cs-arrows button+button{margin-top:2px}.cs-arrows button:disabled{opacity:.3;cursor:default}
        .cs-name{flex:1;min-width:120px}.cs-url{flex:1;min-width:120px}
        .cs-orig{font-size:12px;color:#8a94a0;display:block}
        .cs-st{width:44px;font-weight:700;font-size:13px;text-align:center}
        .cs-tag{display:inline-block;background:#f1eafb;color:#6b3fb5;border-radius:4px;padding:0 6px;font-size:12px;margin-left:4px}
        .cs-pv h4{margin:0 0 8px;font-size:13px;color:#9fb3c8}.cs-pv .g{display:grid;grid-template-columns:1fr 1fr;gap:6px}
        .cs-pv .g div{background:#1b2230;border-radius:6px;padding:7px 9px;font-size:13px}.cs-pv .none{color:#7d8794;font-size:12px;margin-top:8px}
        .cs-save{position:sticky;bottom:0;background:rgba(244,246,249,.96);padding:12px 0;z-index:5}
        .cs-btn-add{margin:10px 0 0}
    </style>

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="cs-tips">
                <div class="cs-tip cs-t1"><b>① 開關：關閉＝前台整類消失</b>前台選單、產品總覽頁的該區塊都一起不顯示，不用找工程師。</div>
                <div class="cs-tip cs-t2"><b>② 產品資料不會刪</b>只是藏起來，之後再打開，原本上架的產品都還在。關閉的類別若有人直接輸入網址，會顯示找不到頁面。</div>
                <div class="cs-tip cs-t3"><b>③ 順序：拖曳 ☰ 或按 ↑↓</b>選單與總覽頁都照這裡的順序排，越上面越前面。</div>
                <div class="cs-tip cs-t4"><b>④ 文字：直接改名稱</b>留空＝用預設名稱（灰字）。</div>
            </div>

            <div class="cs-card card" v-for="b in brands" :key="b.key">
                <div class="cs-hd" :style="{background: b.color}">
                    <span>@{{ b.name }}</span>
                    <span style="font-size:12px;font-weight:400">顯示中 <b>@{{ onCount(b.key) }}</b> / @{{ lists[b.key].length }} 類</span>
                </div>
                <div class="cs-bd">
                    <div class="cs-list">
                        <div class="cs-row" v-for="(r, i) in lists[b.key]" :key="r.key"
                             :class="{off: !r.on, 'drag-over': over.b === b.key && over.i === i}"
                             draggable="true"
                             @dragstart="dragStart(b.key, i, $event)" @dragover.prevent="over = {b: b.key, i: i}"
                             @dragleave="over = {}" @drop.prevent="drop(b.key, i)" @dragend="over = {}">
                            <span class="cs-handle" title="拖曳排序">☰</span>
                            <div class="cs-arrows">
                                <button type="button" :disabled="i === 0" @click="move(b.key, i, -1)">↑</button>
                                <button type="button" :disabled="i === lists[b.key].length - 1" @click="move(b.key, i, 1)">↓</button>
                            </div>
                            <div class="cs-name">
                                <input type="text" class="form-control form-control-sm" v-model="r.name"
                                       :placeholder="r.label" maxlength="40">
                                <span class="cs-orig">預設：@{{ r.label }}</span>
                            </div>
                                                        <span class="cs-st" :style="{color: r.on ? '#28a745' : '#9aa5b1'}">@{{ r.on ? '顯示' : '隱藏' }}</span>
                            <label class="sw-toggle" style="margin:0">
                                <input type="checkbox" v-model="r.on" :true-value="1" :false-value="0"><span class="sw-slider"></span>
                            </label>
                                                    </div>
                                            </div>
                    <div class="cs-pv">
                        <h4>前台「@{{ b.name }}」下拉選單會長這樣</h4>
                        <div class="g"><div v-for="r in shown(b.key)" :key="'p'+r.key">@{{ r.name || r.label }}</div></div>
                        <div class="none" v-if="!shown(b.key).length">全部關閉：前台這個選單的子項目會整個消失</div>
                    </div>
                </div>
            </div>

            <div class="cs-save">
                <button type="button" class="btn btn-success btn-lg" @click="save()" :disabled="saving">儲存設定</button>
                <span class="text-muted ml-2" style="font-size:13px">儲存後前台重新整理即生效。</span>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script type="text/javascript">
        var vm = new Vue({
            el: '#container',
            data: {
                url: '{{ route('admin.website') }}',
                defaults: @json($defaults),
                content: {},
                brands: [
                    { key: 'clarion', name: '歌樂 Clarion', color: '#007abe' },
                    { key: 'mm', name: '美邁 MM', color: '#e8851c' }
                ],
                lists: { clarion: [], mm: [] },
                drag: {},
                over: {},
                saving: false
            },
            created: function () {
                var self = this;
                axios.get(this.url + '/all').then(function (res) {
                    self.content = res.data.item.content || {};
                    self.build();
                }).catch(function () {
                    self.build();
                    toastr.error('讀取設定失敗，請重新整理');
                });
            },
            methods: {
                // 已存的順序優先；預設清單裡有、但還沒存過的類別補在後面
                build: function () {
                    var self = this, saved = self.content.categories || {};
                    ['clarion', 'mm'].forEach(function (b) {
                        var defs = self.defaults[b].items, map = {}, out = [];
                        defs.forEach(function (d) { map[d.key] = d; });
                        (saved[b] || []).forEach(function (s) {
                            if (map[s.key]) {
                                out.push({ key: s.key, label: map[s.key].label, name: s.name || '', on: s.on ? 1 : 0 });
                                map[s.key] = null;
                            }
                        });
                        defs.forEach(function (d) {
                            if (map[d.key]) out.push({ key: d.key, label: d.label, name: '', on: d.on });
                        });
                        self.$set(self.lists, b, out);
                    });
                },
                onCount: function (b) { return this.lists[b].filter(function (r) { return r.on; }).length; },
                shown: function (b) { return this.lists[b].filter(function (r) { return r.on; }); },
                move: function (b, i, d) {
                    var l = this.lists[b], j = i + d;
                    if (j < 0 || j >= l.length) return;
                    var t = l.splice(i, 1)[0]; l.splice(j, 0, t);
                },
                dragStart: function (b, i, e) { this.drag = { b: b, i: i }; if (e.dataTransfer) e.dataTransfer.setData('text', 'x'); },
                drop: function (b, i) {
                    if (this.drag.b !== b || this.drag.i === undefined) { this.over = {}; return; }
                    var l = this.lists[b], t = l.splice(this.drag.i, 1)[0];
                    l.splice(i, 0, t);
                    this.drag = {}; this.over = {};
                },
                save: function () {
                    var self = this, cats = {};
                    for (var bi = 0; bi < 2; bi++) {
                        var b = this.brands[bi].key;
                        for (var i = 0; i < this.lists[b].length; i++) {
                            var r = this.lists[b][i];
                        }
                        cats[b] = this.lists[b].map(function (r) {
                            var o = { key: r.key, name: (r.name || '').trim(), on: r.on ? 1 : 0 };
                            return o;
                        });
                    }
                    var content = JSON.parse(JSON.stringify(this.content));
                    content.categories = cats;
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

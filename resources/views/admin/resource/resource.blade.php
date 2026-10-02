@extends('admin.layout.master')

@section('nav.resource', 'menu-open')
@section('unit_master.resource', 'active')
@section('unit.resource', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="resource" unit="resource" />
    <style>
        .bn-top{display:flex;align-items:center;gap:12px;margin-bottom:12px}.bn-title{font-size:18px;font-weight:700}
        .bn-card{border-radius:10px;overflow:hidden}.bn-card .card-header b{font-size:15px}
        .bn-num{display:inline-flex;width:26px;height:26px;border-radius:50%;background:#fff;align-items:center;justify-content:center;margin-right:8px;font-size:14px;box-shadow:0 0 0 1px #d5dbe3}
        .bn-up{border:2px solid;border-radius:12px;background:#fff;overflow:hidden;height:100%}
        .bn-d{border-color:#007abe}.bn-m{border-color:#e8851c}
        .bn-uh{padding:10px 14px;color:#fff;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:6px}
        .bn-d .bn-uh{background:#007abe}.bn-m .bn-uh{background:#e8851c}
        .bn-chip{display:inline-block;background:rgba(255,255,255,.22);border-radius:20px;padding:2px 10px;font-size:12px;font-style:normal;margin-left:5px}
        .bn-tag{display:inline-block;background:#fff;color:#0b5c8a;border-radius:4px;padding:0 7px;font-size:12px;font-weight:400;margin-left:4px}
        .bn-tag.o{background:#fff4e6;color:#a15c00}
        .bn-ub{padding:14px;text-align:center}
        .bn-frame{position:relative;margin:0 auto 12px;background:repeating-conic-gradient(#eef2f7 0 25%,#fff 0 50%) 0 0/20px 20px;border:1px dashed #9aa7b8;border-radius:8px;overflow:hidden;display:flex;align-items:center;justify-content:center;color:#7d8794;font-size:13px}
        .bn-fd{aspect-ratio:16/9;width:100%}.bn-fm{aspect-ratio:1/2;width:46%;min-width:150px}
        .bn-frame>img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
        .bn-guide{position:absolute;inset:0;pointer-events:none;display:none}.show-guide .bn-guide{display:block}
        .bn-safe{position:absolute;left:20%;right:20%;top:12%;bottom:30%;border:2px dashed #4cd964;border-radius:4px}
        .bn-safe i{position:absolute;top:-1px;left:-1px;background:#4cd964;color:#06310f;font:700 11px/1 sans-serif;padding:3px 6px;font-style:normal}
        .bn-bot{position:absolute;left:0;right:0;bottom:0;height:22%;background:linear-gradient(to top,rgba(255,59,48,.55),rgba(255,59,48,.08));border-top:2px dashed #ff3b30}
        .bn-bot i{position:absolute;bottom:4px;left:6px;color:#fff;font:700 11px/1.2 sans-serif;font-style:normal;text-shadow:0 0 3px #000}
        .bn-fm .bn-safe{left:8%;right:8%;top:10%;bottom:32%}.bn-fm .bn-bot{height:18%}
        .bn-upbtn{display:inline-block;background:#fff;border:2px solid currentColor;border-radius:8px;padding:9px 18px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none!important}
        .bn-d .bn-upbtn{color:#007abe}.bn-m .bn-upbtn{color:#e8851c}
        .bn-d .bn-upbtn.has{background:#007abe;color:#fff}.bn-m .bn-upbtn.has{background:#e8851c;color:#fff}
        .bn-rm{margin-left:10px;color:#dc3545;font-size:13px}
        .bn-hint{font-size:12.5px;color:#6b7785;margin-top:8px}
        .bn-guidechk{display:block;font-size:13px;color:#495563;margin:2px 0 12px;cursor:pointer}
        .bn-tips{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px}
        .bn-tip{border-radius:8px;padding:10px 12px 10px 14px;font-size:13.5px;border-left:5px solid;line-height:1.5}.bn-tip b{display:block}
        .bn-tip.t1{background:#eafaf0;border-color:#34c759}.bn-tip.t2{background:#ffecea;border-color:#ff3b30}.bn-tip.t3{background:#fff4e0;border-color:#ff9500}.bn-tip.t4{background:#eaf3fb;border-color:#007abe}
        .bn-colors{display:flex;gap:12px;flex-wrap:wrap;margin-top:12px}
        .bn-cgrp{flex:1;min-width:260px;background:#f7f9fb;border-radius:8px;padding:10px 14px;font-size:13.5px}
        .bn-ctitle{font-weight:700;margin-bottom:6px}
        .bn-sw{display:inline-flex;align-items:center;margin:0 14px 4px 0}.bn-sw i{width:18px;height:18px;border-radius:4px;margin-right:6px;border:1px solid rgba(0,0,0,.2)}
        .bn-note{display:block;color:#6b7785;font-size:12.5px}
        .bn-save{position:sticky;bottom:0;background:rgba(244,246,249,.96);padding:12px 0;z-index:5}
        .bn-save .btn{margin-right:8px}
    </style>
    <style>
        .rs-cat{display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:8px;cursor:pointer;border:1px solid #e1e5ea;background:#fff;margin-bottom:6px}
        .rs-cat:hover{background:#f4f8fc}.rs-cat.on{background:#007abe;color:#fff;border-color:#007abe}
        .rs-cat .n{margin-left:auto;font-size:12px;background:rgba(0,0,0,.08);border-radius:10px;padding:0 8px}.rs-cat.on .n{background:rgba(255,255,255,.28)}
        .rs-cat.off{color:#8a94a0}.rs-cat.off.on{color:#fff}
        .rs-grp td{background:#eaf3fb;font-weight:700;color:#0b5c8a}
        .rs-no{display:inline-block;width:24px;height:24px;line-height:24px;text-align:center;border-radius:50%;background:#e9eef4;font-size:12px;margin-right:6px;color:#495563}
        .rs-ty{display:inline-block;border-radius:10px;padding:0 8px;font-size:12px}.rs-ty.v{background:#ffecea;color:#c62828}.rs-ty.f{background:#eafaf0;color:#1f6b34}.rs-ty.l{background:#eef1f5;color:#495563}
        .rs-chip{display:inline-block;padding:6px 14px;border-radius:20px;border:2px solid #cfd6df;background:#fff;margin:0 8px 8px 0;cursor:pointer;font-size:14px}
        .rs-chip.on{border-color:#007abe;background:#e6f1fb;color:#0b5c8a;font-weight:700}.rs-chip.off{color:#8a94a0}
        .rs-pv{background:#f7f9fb;border:1px solid #e1e5ea;border-radius:10px;padding:14px}
        .rs-pv .grp{font-weight:700;margin-bottom:6px}.rs-pv li{list-style:none;display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-top:1px solid #e1e5ea}
        .rs-pv ul{padding:0;margin:0}.rs-pv .dl{background:#007abe;color:#fff;border-radius:6px;padding:3px 14px;font-size:13px}.rs-pv .dl.vid{background:#c62828}
        .rs-kind{font-size:13px;margin-top:6px}
        .rs-how{background:#fff8e6;border:1px solid #f3dd9e;border-radius:8px;padding:10px 12px;margin-top:10px;font-size:13px;line-height:1.7}
    </style>

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>

            {{-- ========== 編輯／新增畫面 ========== --}}
            <div class="row" v-show="view === 'edit'">
                <div class="col-12 col-xl-10">
                    <form v-on:submit.prevent="saveItem()">
                        <div class="bn-top">
                            <button type="button" class="btn btn-secondary" @click="backToList"><i class="fas fa-arrow-left"></i> 回列表</button>
                            <span class="bn-title">@{{ form.id ? '編輯檔案' : '新增檔案' }}</span>
                        </div>

                        <div class="card bn-card">
                            <div class="card-header" style="background:#eaf3fb"><span class="bn-num">1</span><b>放到哪個分類？</b><span class="text-muted" style="margin-left:10px;font-size:13px">前台「資源下載」頁，每個分類是一個可展開的區塊</span></div>
                            <div class="card-body">
                                <span v-for="c in categorys" :key="c.id" class="rs-chip" :class="{on: form.resource_category_id == c.id, off: c.status == 0}" @click="form.resource_category_id = c.id">@{{ c.name }}<small v-if="c.status == 0">（停用）</small></span>
                                <div v-if="!categorys.length" class="text-muted">還沒有分類，請先到「資源下載管理 → 分類管理」新增。</div>
                            </div>
                        </div>

                        <div class="card bn-card">
                            <div class="card-header" style="background:#f1eafb"><span class="bn-num">2</span><b>檔案內容</b></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group">
                                            <label>名稱 <span class="bn-tag" style="background:#e6f1fb">必填</span></label>
                                            <input type="text" class="form-control" v-model="form.name" placeholder="例如：GL-1002 韌體 v2.1.0（2026-09）">
                                            <small class="text-muted">前台下載清單上顯示的文字。</small>
                                        </div>
                                        <div class="form-group">
                                            <label>狀態</label>
                                            <div style="display:flex;align-items:center;gap:8px">
                                                <label class="sw-toggle" style="margin:0"><input type="checkbox" :checked="form.status == 1" @change="form.status = $event.target.checked ? 1 : 0"><span class="sw-slider"></span></label>
                                                <b :style="{color: form.status == 1 ? '#28a745' : '#8a94a0'}">@{{ form.status == 1 ? '啟用（前台顯示）' : '停用（前台不顯示）' }}</b>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label>檔案連結（Google 雲端分享連結）<span class="bn-tag" style="background:#e6f1fb">必填</span></label>
                                        <input id="res-url" type="url" class="form-control" v-model="form.url" placeholder="https://drive.google.com/file/d/…/view?usp=sharing">
                                        <div class="rs-kind" v-if="form.url">
                                            偵測到：<b :class="kind(form.url).cls">@{{ kind(form.url).text }}</b>
                                            <a :href="form.url" target="_blank" style="margin-left:8px">點這裡測試連結 <i class="fas fa-external-link-alt"></i></a>
                                        </div>
                                        <div class="rs-how">
                                            <b>怎麼取得連結（Google 雲端）：</b>
                                            <ol style="margin:4px 0 0 18px;padding:0">
                                                <li>把檔案放進 Google 雲端硬碟。</li>
                                                <li>在檔案上按右鍵 →「共用」→ 一般存取權改成<b>「知道連結的任何人」</b>（身分選「檢視者」）。</li>
                                                <li>按「複製連結」，貼到上面這一欄。</li>
                                            </ol>
                                            <div class="text-muted" style="margin-top:4px">沒有改成「知道連結的任何人」的話，客人點下去會看到「需要權限」。YouTube 影片連結也可以，前台會顯示「觀看」。</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="rs-pv" style="margin-top:6px">
                                    <div class="text-muted" style="font-size:12px;margin-bottom:6px">前台這一行會長這樣（預覽）</div>
                                    <div class="grp">+ @{{ categoryName(form.resource_category_id) || '（尚未選分類）' }}</div>
                                    <ul><li><span>@{{ form.name || '（還沒填名稱）' }}</span><span class="dl" :class="{vid: isVideo(form.url)}">@{{ isVideo(form.url) ? '觀看' : '下載' }}</span></li></ul>
                                </div>
                            </div>
                        </div>

                        <div class="bn-save">
                            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> @{{ form.id ? '儲存修改' : '新增檔案' }}</button>
                            <button type="button" class="btn btn-secondary btn-lg" @click="backToList">取消</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========== 列表 ========== --}}
            <div v-show="view === 'list'">
                <div class="alert alert-info" style="margin-bottom:12px">
                    左邊選<b>分類</b>，右邊就是這個分類在前台「資源下載」頁<b>由上到下的順序</b>。拖曳 <b>☰</b> 或按 <b>↑ ↓</b> 就能調整，只會在同一個分類內移動，改完自動儲存。要新增檔案，先選好分類再按「＋新增檔案」。
                </div>
                <div class="row">
                    <div class="col-12 col-lg-3 mb-3">
                        <div class="card"><div class="card-header"><h3 class="card-title">分類</h3></div>
                            <div class="card-body" style="padding:10px">
                                <div class="rs-cat" :class="{on: !catId}" @click="pickCat('')">全部<span class="n">@{{ items.length }}</span></div>
                                <div v-for="c in categorys" :key="c.id" class="rs-cat" :class="{on: catId == c.id, off: c.status == 0}" @click="pickCat(c.id)">
                                    @{{ c.name }}<small v-if="c.status == 0">（停用）</small><span class="n">@{{ countOf(c.id) }}</span>
                                </div>
                                <a href="{{ route('admin.resource_category') }}" style="font-size:12px;display:block;margin-top:8px">管理分類（新增、改名、排序）→</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-9">
                        <div class="card">
                            <div class="card-header" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                                <button type="button" class="btn btn-primary" @click="openEdit(null)"><i class="fas fa-plus"></i> 新增檔案</button>
                                <b>@{{ catId ? categoryName(catId) : '全部分類' }}</b><span class="text-muted">共 @{{ shownItems.length }} 個檔案</span>
                                <input type="text" class="form-control" v-model="keyword" placeholder="搜尋名稱" style="max-width:220px;margin-left:auto">
                            </div>
                            <div class="card-body">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:18%">順序（前台由上到下）</th>
                                            <th>名稱</th>
                                            <th style="width:12%">類型</th>
                                            <th style="width:13%">狀態</th>
                                            <th style="width:16%">功能</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="(item, num) in shownItems">
                                            <tr v-if="!catId && (num === 0 || shownItems[num - 1].resource_category_id !== item.resource_category_id)" :key="'g' + item.id" class="rs-grp"><td colspan="5">@{{ categoryName(item.resource_category_id) }}</td></tr>
                                            <tr :key="item.id" :draggable="handleDown" :class="{'sort-over': dragOverIdx === num && dragFrom > num, 'sort-over-down': dragOverIdx === num && dragFrom >= 0 && dragFrom < num}" @dragstart="dragStart(num, $event)" @dragenter.prevent="dragEnter(num)" @dragover.prevent @drop.prevent="dropRow(num)" @dragend="dragEnd">
                                                <td style="white-space:nowrap">
                                                    <span class="rs-no">@{{ orderNo(item) }}</span>
                                                    <span class="sort-handle" title="按住拖曳" @mousedown="handleDown = true" @mouseup="handleDown = false">☰</span>
                                                    <a href="javascript:void(0)" class="sort-arrow" title="往前一格" @click="moveItem(num, -1)">↑</a>
                                                    <a href="javascript:void(0)" class="sort-arrow" title="往後一格" @click="moveItem(num, 1)">↓</a>
                                                </td>
                                                <td>@{{ item.name }}<div><a v-if="item.url" :href="item.url" target="_blank" class="text-muted" style="font-size:12px">開啟連結 <i class="fas fa-external-link-alt"></i></a></div></td>
                                                <td><span class="rs-ty" :class="isVideo(item.url) ? 'v' : 'f'">@{{ isVideo(item.url) ? '影片' : '檔案' }}</span></td>
                                                <td>
                                                    <label class="sw-toggle" :title="item.status == 1 ? '啟用中（點一下停用）' : '已停用（點一下啟用）'"><input type="checkbox" :checked="item.status == 1" @click.prevent="statusItem(item.id)"><span class="sw-slider"></span></label>
                                                    <span class="sw-text" :class="item.status == 1 ? 'on' : 'off'">@{{ item.status == 1 ? '啟用' : '停用' }}</span>
                                                </td>
                                                <td class="method-button">
                                                    <button type="button" class="btn btn-primary btn-sm" @click="openEdit(item)"><i class="fas fa-pencil-alt"></i> 編輯</button>
                                                    <button type="button" class="btn btn-danger btn-sm" @click="deleteItem(item.id)"><i class="fas fa-trash"></i> 刪除</button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr v-if="!shownItems.length"><td colspan="5" class="text-center text-muted" style="padding:30px">這個分類還沒有檔案。按左上角「＋新增檔案」新增第一個。</td></tr>
                                    </tbody>
                                </table>
                            </div>
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
            mixins: [window.sortMixin || {}],
            el: '#container',
            data: {
                url: '{{ route('admin.resource') }}',
                view: 'list',
                categorys: [],
                items: [],
                catId: '',
                keyword: '',
                form: { id: '', resource_category_id: '', name: '', url: '', status: 1 }
            },
            computed: {
                // 目前畫面上的檔案：依分類篩選＋名稱搜尋；已經依分類順序、分類內順序排好
                shownItems: function() {
                    var self = this, k = (this.keyword || '').trim().toLowerCase();
                    return this.items.filter(function(it) {
                        if (self.catId && it.resource_category_id != self.catId) return false;
                        return !k || String(it.name).toLowerCase().indexOf(k) >= 0;
                    });
                }
            },
            created: function() {
                var q = new URLSearchParams(location.search).get('category');
                if (q) this.catId = q;
                this.$nextTick(function() { vm.getItems(); });
                window.addEventListener('popstate', function() { vm.view = 'list'; });
            },
            methods: {
                // 依連結判斷類型，後台顯示「偵測到：…」讓人知道貼對了
                kind: function(u) {
                    u = u || '';
                    if (/youtube\.com|youtu\.be/i.test(u)) return { text: 'YouTube 影片（前台顯示「觀看」）', cls: 'text-danger' };
                    if (/drive\.google\.com|docs\.google\.com/i.test(u)) return { text: 'Google 雲端檔案（前台顯示「下載」）', cls: 'text-success' };
                    if (/^https?:\/\//i.test(u)) return { text: '一般網址（前台顯示「下載」）', cls: 'text-primary' };
                    return { text: '不是有效的網址（要以 https:// 開頭）', cls: 'text-danger' };
                },
                isVideo: function(u) { return /youtube\.com|youtu\.be/i.test(u || ''); },
                categoryName: function(id) {
                    var c = this.categorys.filter(function(x) { return x.id == id; })[0];
                    return c ? c.name : '';
                },
                countOf: function(id) { return this.items.filter(function(i) { return i.resource_category_id == id; }).length; },
                // 這個檔案在「它的分類」裡排第幾個（前台順序）
                orderNo: function(item) {
                    var n = 0;
                    for (var i = 0; i < this.items.length; i++) {
                        if (this.items[i].resource_category_id == item.resource_category_id) n++;
                        if (this.items[i].id === item.id) return n;
                    }
                    return n;
                },
                pickCat: function(id) { this.catId = id; },
                sortGroupKey: function(item) { return item.resource_category_id; },
                getItems: function() {
                    axios.get(vm.url + '/all', { params: { page: 1, per_page: 200 } }).then(function(response) {
                        vm.categorys = response.data.categorys;
                        var ord = {};
                        vm.categorys.forEach(function(c, i) { ord[c.id] = i; });
                        // 依分類順序分組；同分類內維持後端給的順序（排序數字小的在前）
                        vm.items = response.data.items.data.map(function(it, i) { return {it: it, i: i}; }).sort(function(a, b) {
                            var d = (ord[a.it.resource_category_id] || 0) - (ord[b.it.resource_category_id] || 0);
                            return d || a.i - b.i;
                        }).map(function(x) { return x.it; });
                    }).catch(function(error) {
                        vm.showMessage('error', error.response ? error.response.data.message : error);
                    });
                },
                // 調整順序時，排序數字要用「完整清單」重新分配；篩選後的 shownItems 是 items 的子集，
                // 所以 mixin 用到的 items 要是完整清單，而畫面列的 index 要換算（見下面覆寫）
                moveItem: function(num, dir) {
                    var full = this.items, cur = this.shownItems[num];
                    var fi = full.indexOf(cur), g = cur.resource_category_id;
                    var to = fi + dir;
                    while (to >= 0 && to < full.length && full[to].resource_category_id !== g) to += dir;
                    if (to < 0 || to >= full.length) return;
                    var it = full.splice(fi, 1)[0];
                    full.splice(to, 0, it);
                    this.applyOrder(g);
                },
                dragStart: function(num, e) {
                    if (!this.handleDown) { e.preventDefault(); return; }
                    this.dragFrom = num;
                    if (e.dataTransfer) { e.dataTransfer.effectAllowed = 'move'; try { e.dataTransfer.setData('text/plain', String(num)); } catch (x) {} }
                },
                dragEnter: function(num) {
                    if (this.dragFrom < 0) return;
                    this.dragOverIdx = (this.shownItems[num].resource_category_id === this.shownItems[this.dragFrom].resource_category_id) ? num : -1;
                },
                dropRow: function(num) {
                    var from = this.dragFrom;
                    this.dragEnd();
                    if (from < 0 || from === num) return;
                    var a = this.shownItems[from], b = this.shownItems[num];
                    if (a.resource_category_id !== b.resource_category_id) { toastr.warning('只能在同一個分類內調整順序'); return; }
                    var full = this.items;
                    full.splice(full.indexOf(a), 1);
                    full.splice(full.indexOf(b) + (from < num ? 1 : 0), 0, a);
                    this.applyOrder(a.resource_category_id);
                },
                sortItems: function() {
                    if (!vm.items.length) return false;
                    axios.patch(vm.url + '/all/sort', {items: vm.items}).then(function(response) {
                        vm.showMessage('success', response.data.message);
                        vm.getItems();
                    }).catch(function(error) {
                        vm.showMessage('error', error.response ? error.response.data.message : error);
                    });
                },
                openEdit: function(item) {
                    if (item) {
                        this.form = { id: item.id, resource_category_id: item.resource_category_id, name: item.name, url: item.url, status: item.status };
                    } else {
                        // 新增：帶入目前選的分類；選「全部」就先不選，要自己點一個
                        this.form = { id: '', resource_category_id: this.catId || '', name: '', url: '', status: 1 };
                    }
                    history.pushState({}, '');
                    this.view = 'edit';
                    $('html, body').scrollTop(0);
                },
                backToList: function() { this.view = 'list'; $('html, body').scrollTop(0); },
                saveItem: function() {
                    var f = this.form;
                    if (!f.resource_category_id) { this.showMessage('error', '請先選擇分類'); return; }
                    if (!String(f.name).trim()) { this.showMessage('error', '請填寫名稱'); return; }
                    if (!String(f.url).trim()) { this.showMessage('error', '請貼上檔案連結（Google 雲端分享連結）'); return; }
                    var data = { resource_category_id: f.resource_category_id, name: f.name, url: f.url, status: f.status };
                    var req = f.id ? axios.patch(vm.url + '/' + f.id, data) : axios.post(vm.url, data);
                    req.then(function(response) {
                        vm.showMessage('success', response.data.message);
                        vm.catId = f.resource_category_id;
                        vm.getItems();
                        vm.view = 'list';
                    }).catch(function(error) {
                        vm.showMessage('error', error.response ? error.response.data.message : error);
                    });
                },
                deleteItem: function(id) {
                    if (confirm('確定要刪除？') !== true) return false;
                    axios.delete(vm.url + '/' + id).then(function(response) {
                        vm.showMessage('success', response.data.message);
                        vm.getItems();
                    }).catch(function(error) {
                        vm.showMessage('error', error.response ? error.response.data.message : error);
                    });
                },
                statusItem: function(id) {
                    axios.patch(vm.url + '/' + id + '/status').then(function(response) {
                        vm.showMessage('success', response.data.message);
                        vm.getItems();
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

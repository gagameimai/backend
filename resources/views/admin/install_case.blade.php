@extends('admin.layout.master')

@section('unit.install_case', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="install_case" />

    <style>
        .wz-sec { margin-bottom:14px; }
        .wz-savebar { position:sticky; bottom:0; background:rgba(244,246,249,.95); padding:10px 0; z-index:5; }
        .wz-stepper { display:flex; background:#fff; border-radius:6px; padding:12px 8px; box-shadow:0 1px 3px rgba(0,0,0,.15); margin-bottom:14px; }
        .wz-stepper > div { flex:1; text-align:center; font-size:13px; color:#98a2ad; position:relative; cursor:pointer; }
        .wz-stepper b { display:inline-flex; width:26px; height:26px; border-radius:50%; background:#dfe4ea; color:#fff; align-items:center; justify-content:center; margin-bottom:4px; position:relative; z-index:1; }
        .wz-stepper > div.done b { background:#28a745; } .wz-stepper > div.done { color:#28a745; }
        .wz-stepper > div.cur b { background:#007bff; } .wz-stepper > div.cur { color:#007bff; font-weight:700; }
        .wz-stepper > div:not(:last-child):after { content:""; position:absolute; top:13px; left:calc(50% + 18px); right:calc(-50% + 18px); height:2px; background:#dfe4ea; }
        .wz-stepper > div.done:after { background:#28a745 !important; }
        .wz-step { display:inline-flex; width:24px; height:24px; border-radius:50%; background:#007bff; color:#fff; align-items:center; justify-content:center; font-size:13px; margin-right:8px; }
        .wz-tabs span { display:inline-block; padding:3px 12px; border-radius:14px; background:#e9ecef; font-size:12px; cursor:pointer; margin-right:6px; }
        .wz-tabs span.on { background:#007bff; color:#fff; }
        .wz-card { border:1px solid #e6ebf1; border-radius:10px; overflow:hidden; background:#fff; width:230px; display:inline-block; vertical-align:top; margin-right:10px; }
        .wz-card .ph { height:144px; background:#0d1016 center/cover no-repeat; display:flex; align-items:center; justify-content:center; color:#7d8794; font-size:12px; }
        .wz-card h4 { font-size:14px; margin:8px 10px 2px; } .wz-card small { display:block; margin:0 10px 10px; color:#93a0b0; font-size:12px; }
        .wz-dp { border:1px solid #e6ebf1; border-radius:8px; background:#fff; padding:12px; font-size:12px; }
        .wz-dp .r2 { display:flex; gap:12px; } .wz-dp .im { width:48%; aspect-ratio:16/10; background:#0d1016 center/cover no-repeat; border-radius:6px; flex:none; }
        .wz-dp h3 { font-size:16px; margin:2px 0 6px; } .wz-dp dl { display:grid; grid-template-columns:44px 1fr; gap:3px 8px; margin:0; } .wz-dp dt { color:#93a0b0; font-weight:400; } .wz-dp dd { margin:0; }
        .wz-dp .cat { color:#007abe; font-weight:700; font-size:11px; } .wz-dp h5 { font-size:13px; margin:12px 0 3px; border-left:3px solid #007abe; padding-left:6px; }
        .wz-dp p { margin:0; white-space:pre-line; }
        .wz-set { display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid #eef0f3; } .wz-set:last-child { border:0; }
        .wz-set .t { font-weight:700; } .wz-set .d { font-size:12px; color:#6c757d; }
        .wz-set .sw-toggle { margin:0; flex:none; }
    </style>

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        管理「安裝案例」。打開「顯示在首頁」的案例，會出現在<b>首頁最底部</b>的「CASE ／ 安裝實績」區塊（在兩個滿版區塊之後、最後的 CTA 之前，最多 3 筆；一筆都沒打開時這個區塊整個不顯示）。<br>
                        <b>前台排序規則：</b>「置頂」的案例排最前面（同一時間只會有一筆置頂），其餘依「安裝日期」由新到舊（沒填日期就用建立時間），不需要手動輸入排序數字。
<details style="margin-top:8px">
                            <summary style="cursor:pointer;color:#0b5c8a;font-weight:700">看「顯示在首頁」會出現在首頁哪裡（位置圖）</summary>
                            <div class="row" style="margin-top:10px">
                                <div class="col-12 col-md-4" style="max-width:260px">
                                    <div style="border-radius:8px;overflow:hidden;font-size:12px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.2)">
                                        <div style="background:#0d1016;color:#fff;padding:22px 6px">① 首頁大 Banner</div>
                                        <div style="background:#e9eef4;padding:9px 6px">② 精選商品</div>
                                        <div style="background:#1d2a38;color:#fff;padding:9px 6px">③ 滿版區塊 1</div>
                                        <div style="background:#1d2a38;color:#fff;padding:9px 6px;border-top:1px solid #fff">④ 滿版區塊 2（MM）</div>
                                        <div style="background:#fff4e0;color:#a15c00;border:3px solid #ff9500;padding:12px 6px;font-weight:700">⑤ 安裝實績（Case）← 就是這裡</div>
                                        <div style="background:#0d1016;color:#fff;padding:9px 6px">⑥ 滿版區塊 3（最底部 CTA）</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8" style="font-size:13.5px;line-height:1.7">
                                    <b>前台長這樣：</b>標題「CASE ／ 安裝實績」，右邊「看全部案例 →」，下面是 3 張案例卡片（圖＋名稱＋分類）。<br>
                                    點卡片進到該案例的詳細頁；點「看全部案例」進案例列表。<br>
                                    「顯示在首頁」打開的案例最多 3 筆；一筆都沒打開時，整個區塊不會顯示（不會出現空標題）。
                                </div>
                            </div>
                        </details>

                    </div>
                </div>
            </div>

            {{-- ========== 新增／編輯（分步驟＋即時預覽） ========== --}}
            <div v-show="view === 'wizard'">
                <div style="margin-bottom:12px">
                    <button type="button" class="btn btn-secondary" @click="open('list')"><i class="fas fa-arrow-left"></i> 回列表</button>
                    <span style="font-size:18px; font-weight:700; margin-left:10px">@{{ mode === 'create' ? '新增案例' : '編輯案例' }}</span>
                </div>

                <div class="row">
                    <div class="col-12 col-xl-7">
                        <div>
                                {{-- 1 案例圖片（先傳圖） --}}
                                <div class="card card-primary card-outline wz-sec" id="wz-sec-1"><div class="card-header"><h3 class="card-title"><span class="wz-step">1</span>案例圖片</h3></div><div class="card-body">
                                    <label>*案例圖片　<span class="text-muted font-weight-normal">建議 1600 × 1000（16:10）實拍，主體置中，300KB 內</span></label>
                                    <input id="wz-img" type="hidden">
                                    <a id="lfm-wz" data-input="wz-img" data-preview="wz-dummy" class="d-flex flex-column align-items-center justify-content-center" style="border:2px dashed #9bbbe0; border-radius:8px; background:#f5f9ff; min-height:180px; color:#3a6ea5; text-decoration:none; cursor:pointer">
                                        <img v-if="form.img" :src="form.img" style="max-height:150px; max-width:100%; border-radius:4px; margin-bottom:8px">
                                        <span class="btn btn-primary"><i class="fa fa-picture-o"></i> @{{ form.img ? '更換圖片' : '選取／上傳圖片' }}</span>
                                        <small v-if="!form.img" style="margin-top:8px">點這裡開啟檔案管理員，可上傳新圖或選擇已上傳的圖</small>
                                    </a>
                                    <div id="wz-dummy" style="display:none"></div>
                                    <a v-if="form.img" href="javascript:void(0)" class="text-danger" style="font-size:12px" @click="clearImg">移除這張</a>
                                    <div class="callout callout-warning" style="margin-top:12px">電腦版、手機版共用這一張（首頁卡、列表卡、內頁都是 16:10 裁切），所以主體請放在畫面中間，右邊預覽就是裁切後的樣子。</div>
                                </div>
                                </div>
                                {{-- 2 基本資料 --}}
                                <div class="card card-primary card-outline wz-sec" id="wz-sec-2"><div class="card-header"><h3 class="card-title"><span class="wz-step">2</span>基本資料</h3></div><div class="card-body">
                                    <div class="form-group"><label>*名稱</label><input type="text" class="form-control" v-model="form.name" placeholder="例如：Alphard 安裝 Clarion GL-1002"></div>
                                    <div class="form-group"><label>*分類</label>
                                        <select class="form-control" v-model="form.category">
                                            <option v-for="c in categories" :key="c.value" :value="c.value">@{{ c.label }}</option>
                                        </select>
                                        <small class="text-muted">前台案例卡片上會顯示這個分類。</small>
                                    </div>
                                </div>
                                </div>
                                {{-- 3 車款與產品 --}}
                                <div class="card card-primary card-outline wz-sec" id="wz-sec-3"><div class="card-header"><h3 class="card-title"><span class="wz-step">3</span>車款與產品</h3></div><div class="card-body">
                                    <div class="row">
                                        <div class="col-6"><div class="form-group"><label>*汽車品牌</label>
                                            <select class="form-control" v-model="form.car_brand_id" @change="form.car_id = ''">
                                                <option value="">請選擇</option>
                                                <option v-for="row in brands" :key="row.id" :value="row.id">@{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}</option>
                                            </select></div></div>
                                        <div class="col-6"><div class="form-group"><label>*汽車車款</label>
                                            <select class="form-control" v-model="form.car_id">
                                                <option value="">請選擇</option>
                                                <option v-for="row in carsOfBrand" :key="row.id" :value="row.id">@{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}</option>
                                            </select></div></div>
                                    </div>
                                    <div class="form-group"><label>安裝產品</label><input type="text" class="form-control" v-model="form.product" placeholder="例：Clarion GL-1002"><small class="text-muted">型號照原廠寫法。</small></div>
                                    <div class="row">
                                        <div class="col-6"><div class="form-group"><label>施工據點</label><input type="text" class="form-control" v-model="form.dealer"></div></div>
                                        <div class="col-6"><div class="form-group"><label>安裝日期</label><input type="date" class="form-control" v-model="form.installed_at"><small class="text-muted">前台依此排序；不填就用建立時間。</small></div></div>
                                    </div>
                                </div>
                                </div>
                                {{-- 4 案例說明 --}}
                                <div class="card card-primary card-outline wz-sec" id="wz-sec-4"><div class="card-header"><h3 class="card-title"><span class="wz-step">4</span>案例說明</h3></div><div class="card-body">
                                    <div class="form-group"><label>客戶需求</label><textarea class="form-control" rows="4" v-model="form.need" placeholder="客戶想解決什麼問題？"></textarea></div>
                                    <div class="form-group"><label>施工內容</label><textarea class="form-control" rows="4" v-model="form.work" placeholder="做了哪些施工？"></textarea></div>
                                    <small class="text-muted">兩欄都可以留空，沒填的前台就不顯示那一段。</small>
                                </div>
                                </div>
                                {{-- 5 顯示設定 --}}
                                <div class="card card-primary card-outline wz-sec" id="wz-sec-5"><div class="card-header"><h3 class="card-title"><span class="wz-step">5</span>顯示設定</h3></div><div class="card-body">
                                    <div class="wz-set">
                                        <div><div class="t">啟用</div><div class="d">關閉就不會顯示在前台（等於草稿）</div></div>
                                        <label class="sw-toggle"><input type="checkbox" :checked="form.status == 1" @change="form.status = $event.target.checked ? 1 : 0"><span class="sw-slider"></span></label>
                                    </div>
                                    <div class="wz-set">
                                        <div><div class="t">顯示在首頁</div>
                                            <div class="d">顯示在首頁最底部的「CASE ／ 安裝實績」區塊，最多 3 筆（目前已有 @{{ homeOthers }} 筆）<span v-if="homeFull" class="text-danger">，已滿，請先到列表關掉別筆</span></div></div>
                                        <label class="sw-toggle"><input type="checkbox" :checked="form.is_home == 1" @change="toggleHome($event)"><span class="sw-slider"></span></label>
                                    </div>
                                    <div class="wz-set">
                                        <div><div class="t">置頂</div>
                                            <div class="d">排在列表最前面。同一時間只會有一筆置頂<span v-if="otherPinned" class="text-danger">；目前置頂的是「@{{ otherPinned.name }}」，打開後它會自動取消置頂</span></div></div>
                                        <label class="sw-toggle"><input type="checkbox" :checked="form.is_pinned == 1" @change="form.is_pinned = $event.target.checked ? 1 : 0"><span class="sw-slider"></span></label>
                                    </div>
                                    <small class="text-muted d-block mt-2">其餘案例依「安裝日期」由新到舊自動排列，不用手動排序。</small>
                                </div>
                                </div>
                                <div class="wz-savebar">
                                    <button type="button" class="btn btn-success btn-lg" @click="save"><i class="fas fa-save"></i> @{{ mode === 'create' ? '儲存案例' : '儲存修改' }}</button>
                                    <button type="button" class="btn btn-secondary btn-lg" @click="open('list')">取消</button>
                                </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-5">
                        <div class="card" style="position:sticky; top:10px">
                            <div class="card-header"><h3 class="card-title">即時預覽</h3><div class="card-tools" style="font-size:12px">前台會長這樣</div></div>
                            <div class="card-body">
                                <div class="wz-tabs" style="margin-bottom:10px">
                                    <span :class="{on: tab === 'home'}" @click="tab = 'home'">首頁卡片</span>
                                    <span :class="{on: tab === 'list'}" @click="tab = 'list'">列表卡片</span>
                                    <span :class="{on: tab === 'detail'}" @click="tab = 'detail'">案例內頁</span>
                                </div>
                                <div v-show="tab !== 'detail'">
                                    <div class="wz-card">
                                        <div class="ph" :style="pvImg"><span v-if="!form.img">尚未選圖</span></div>
                                        <h4>@{{ form.name || '（案例名稱）' }}</h4>
                                        <small>@{{ categoryLabel(form.category) }}</small>
                                    </div>
                                    <div class="text-muted" style="font-size:12px; margin-top:6px">@{{ tab === 'home' ? '首頁「安裝實績」區塊（最多 3 筆）' : '「導入事例」列表頁' }}。卡片上看不到車款與日期，那些在內頁顯示。</div>
                                </div>
                                <div v-show="tab === 'detail'" class="wz-dp">
                                    <div class="r2">
                                        <div class="im" :style="pvImg"></div>
                                        <div style="flex:1">
                                            <div class="cat">@{{ categoryLabel(form.category) }}</div>
                                            <h3>@{{ form.name || '（案例名稱）' }}</h3>
                                            <dl>
                                                <template v-if="pvCar"><dt>車款</dt><dd>@{{ pvCar }}</dd></template>
                                                <template v-if="form.product"><dt>產品</dt><dd>@{{ form.product }}</dd></template>
                                                <template v-if="form.dealer"><dt>據點</dt><dd>@{{ form.dealer }}</dd></template>
                                                <template v-if="form.installed_at"><dt>日期</dt><dd>@{{ form.installed_at }}</dd></template>
                                            </dl>
                                        </div>
                                    </div>
                                    <div v-if="form.need"><h5>客戶需求</h5><p>@{{ form.need }}</p></div>
                                    <div v-if="form.work"><h5>施工內容</h5><p>@{{ form.work }}</p></div>
                                    <div v-if="!form.need && !form.work" class="text-muted" style="margin-top:10px">（第 4 步填寫後會顯示在這裡）</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- search --}}
            <div class="modal fade" id="search_modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">搜尋</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-form-label">名稱</label>
                                <input type="text" v-model="search.name" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-left btn btn-primary" @click="getItems()">送出</button>
                            <button type="button" class="float-right btn btn-default" @click="clear('search')">清空</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- list --}}
            <div class="row" v-show="view === 'list'">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <button @click="open('create')" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                新增
                            </button>

                            <div class="card-tools">
                                <button type="button" class="btn btn-default" v-if="search.is_search == true" @click="clear('search')">
                                    <i class="fa-solid fa-align-justify"></i>
                                    總覽
                                </button>

                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#search_modal">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    搜尋
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>名稱</th>
                                        <th style="width: 14%">分類</th>
                                        <th>圖片</th>
                                        <th style="width: 12%">安裝日期</th>
                                        <th style="width: 9%">置頂</th>
                                        <th style="width: 9%">首頁</th>
                                        <th style="width: 11%">狀態</th>
                                        <th style="width: 14%">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, num) in items" :key="item.id">
                                        <td>
                                            @{{ item.name }}
                                            <small v-if="item.brand || item.car" class="d-block text-muted">
                                                @{{ item.brand ? item.brand.name : '' }} @{{ item.car ? item.car.name : '' }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">@{{ categoryLabel(item.category) }}</span>
                                        </td>
                                        <td>
                                            <img v-if="item.img" :src="item.img" style="height:50px;">
                                        </td>
                                        <td>@{{ item.installed_at || '—' }}</td>
                                        <td>
                                            <label class="sw-toggle" :title="item.is_pinned == 1 ? '置頂中（點一下取消）' : '未置頂（點一下置頂，會取消目前置頂的那筆）'"><input type="checkbox" :checked="item.is_pinned == 1" @click.prevent="pinnedItem(item)"><span class="sw-slider"></span></label>
                                        </td>
                                        <td>
                                            <label class="sw-toggle" :title="item.is_home == 1 ? '顯示在首頁（點一下取消）' : '不在首頁（點一下加入，最多 3 筆）'"><input type="checkbox" :checked="item.is_home == 1" @click.prevent="homeItem(item.id)"><span class="sw-slider"></span></label>
                                        </td>
                                        <td>
                                            <label class="sw-toggle" :title="item.status == 1 ? '啟用中（點一下停用）' : '已停用（點一下啟用）'"><input type="checkbox" :checked="item.status == 1" @click.prevent="statusItem(item.id)"><span class="sw-slider"></span></label>
                                            <span class="sw-text" :class="item.status == 1 ? 'on' : 'off'">@{{ item.status == 1 ? '啟用' : '停用' }}</span>
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
    <script type="text/javascript">
        function defaultForm() {
            return {
                id: null, category: 0, name: '', img: '', status: 1, installed_at: '',
                car_brand_id: '', car_id: '', product: '', dealer: '', need: '', work: '',
                is_pinned: 0, is_home: 0, sort: 0, home_sort: 0
            };
        }

        var vm = new Vue({
            el: '#container',
            data: {
                url: '{{ route('admin.install_case') }}',
                items: [],
                view: 'list',
                mode: 'create',
                step: 1,
                tab: 'home',
                form: defaultForm(),
                brands: [],
                cars: [],
                pinned: null,
                homeIds: [],
                stepTitles: ['案例圖片', '基本資料', '車款與產品', '案例說明', '顯示設定'],
                categories: [
                    { value: 0, label: '多媒體安卓機' },
                    { value: 1, label: '車型專用機' },
                    { value: 2, label: '汽車音響' },
                    { value: 3, label: '行車記錄器' },
                    { value: 4, label: '影像・安全' },
                    { value: 5, label: '車用配件' },
                    { value: 6, label: '車用主機 1/2DIN' },
                    { value: 7, label: '頭枕螢幕' },
                    { value: 8, label: '可攜式' }
                ],
                search: {
                    name: '',
                    is_search: false
                },
                page: 1,
                pagination: {
                    start: 0,
                    total: 0,
                    current_page: 1
                }
            },
            computed: {
                carsOfBrand: function() {
                    var b = this.form.car_brand_id;
                    return this.cars.filter(function(r) { return r.car_brand_id == b; });
                },
                pvImg: function() { return this.form.img ? { backgroundImage: 'url(' + this.form.img + ')' } : {}; },
                pvCar: function() {
                    var self = this;
                    var b = this.brands.find(function(r) { return r.id == self.form.car_brand_id; });
                    var c = this.cars.find(function(r) { return r.id == self.form.car_id; });
                    return ((b ? b.name : '') + ' ' + (c ? c.name : '')).trim();
                },
                homeOthers: function() {
                    var id = this.form.id;
                    return this.homeIds.filter(function(i) { return i != id; }).length;
                },
                homeFull: function() { return this.homeOthers >= 3 && this.form.is_home != 1; },
                otherPinned: function() {
                    return (this.pinned && this.pinned.id != this.form.id) ? this.pinned : null;
                }
            },
            watch: {
                step: function(s) { this.tab = (s <= 2 || s === 5) ? 'home' : 'detail'; }
            },
            created: function() {
                this.getItems();
                // 讓瀏覽器「上一頁」能回到列表：進入新增/編輯時登記一筆瀏覽器紀錄（見 open()）
                window.addEventListener('popstate', function() {
                    vm.view = 'list';
                });
            },
            mounted: function() {
                $('#lfm-wz').filemanager('file', {prefix: 'filemanager'});
                // 檔案管理員選好圖會觸發 change，同步回 form.img
                $('#wz-img').on('change', function() { vm.form.img = $(this).val(); });
            },
            methods: {
                categoryLabel: function(value) {
                    // 注意：這裡要用 this，不能用全域 vm（第一次渲染時 vm 還沒指派完成，會噴錯）
                    let found = this.categories.find(function(c) { return c.value == value; });
                    return found ? found.label : '-';
                },
                clear: function(method = 'all') {
                    if (method == 'search') {
                        vm.search = {
                            name: '',
                            is_search: false
                        };

                        vm.getItems();
                    }
                },
                open: function(active = '', id = '') {
                    switch (active) {
                        case 'list':
                            vm.view = 'list';
                            break;

                        case 'create':
                            history.pushState({}, '');
                            vm.mode = 'create';
                            vm.form = defaultForm();
                            vm.step = 1;
                            $('#wz-img').val('');
                            vm.view = 'wizard';
                            window.scrollTo(0, 0);
                            break;

                        case 'edit':
                            history.pushState({}, '');
                            axios.get(vm.url + '/' + id).then(function(response) {
                                vm.mode = 'edit';
                                var it = response.data.item;
                                vm.form = Object.assign(defaultForm(), it, {
                                    installed_at: it.installed_at ? String(it.installed_at).substring(0, 10) : '',
                                    img: it.img || ''
                                });
                                $('#wz-img').val(vm.form.img);
                                vm.step = 1;
                                vm.view = 'wizard';
                                window.scrollTo(0, 0);
                            }).catch(function(error) {
                                vm.showMessage('error', error.response ? error.response.data.message : error);
                            });
                            break;

                        default:
                            vm.showMessage('error', '系統異常。');
                            break;
                    }
                },
                clearImg: function() { vm.form.img = ''; $('#wz-img').val(''); },
                validateStep: function(s) {
                    var f = this.form;
                    if (s === 1 && !f.img) { this.showMessage('error', '請先上傳案例圖片'); return false; }
                    if (s === 2 && !String(f.name).trim()) { this.showMessage('error', '請先填寫案例名稱'); return false; }
                    if (s === 3 && (!f.car_brand_id || !f.car_id)) { this.showMessage('error', '請選擇汽車品牌與車款'); return false; }
                    return true;
                },
                goStep: function(n) {
                    if (n > this.step && this.mode === 'create') {
                        for (var s = this.step; s < n; s++) { if (!this.validateStep(s)) return; }
                    }
                    this.step = n;
                },
                prev: function() { if (this.step > 1) this.step--; },
                next: function() { if (this.validateStep(this.step) && this.step < 5) this.step++; },
                toggleHome: function(e) {
                    if (e.target.checked && this.homeOthers >= 3) {
                        e.target.checked = false;
                        this.showMessage('error', '首頁最多只能放 3 筆，請先到列表把其中一筆關閉');
                        return;
                    }
                    this.form.is_home = e.target.checked ? 1 : 0;
                },
                save: function() {
                    for (var s = 1; s <= 3; s++) { if (!this.validateStep(s)) { var el = document.getElementById('wz-sec-' + s); if (el) el.scrollIntoView({behavior: 'smooth', block: 'center'}); return; } }
                    var data = Object.assign({}, this.form);
                    delete data.brand; delete data.car;
                    var req = this.mode === 'create' ? axios.post(vm.url, data) : axios.patch(vm.url + '/' + this.form.id, data);
                    req.then(function(response) {
                        vm.showMessage('success', response.data.message);
                        vm.getItems(vm.mode === 'create' ? 1 : vm.page, false);
                        vm.view = 'list';
                    }).catch(function(error) {
                        vm.showMessage('error', error.response ? error.response.data.message : error);
                    });
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
                                page: page,
                                name: vm.search.name
                            }
                        }).then(function(response) {
                            let total = Math.ceil(response.data.items.total / response.data.items.per_page);
                            vm.items = response.data.items.data;
                            vm.brands = response.data.brands;
                            vm.cars = response.data.cars;
                            vm.pinned = response.data.pinned || null;
                            vm.homeIds = response.data.home_ids || [];
                            vm.search.is_search = response.data.is_search;
                            vm.setPagination(response.data.items.current_page, total);
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
                pinnedItem: function(item) {
                    // 開啟置頂時，若別筆已經置頂，先讓使用者確認（後端會自動取消那一筆）
                    if (item.is_pinned != 1 && vm.pinned && vm.pinned.id != item.id) {
                        if (confirm('目前置頂的是「' + vm.pinned.name + '」，設定這一筆置頂後，它會自動取消置頂。確定嗎？') !== true) return false;
                    }
                    try {
                        axios.patch(vm.url + '/' + item.id + '/pinned').then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page, false);
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                homeItem: function(id) {
                    try {
                        axios.patch(vm.url + '/' + id + '/home').then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page, false);
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

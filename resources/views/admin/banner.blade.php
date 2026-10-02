@extends('admin.layout.master')

@section('unit.banner', 'active')

@section('content')
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
    <x-components::unit-title guard="admin" area="banner" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            {{-- create / update --}}
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <form v-on:submit.prevent="createItem()" id="createArea" style="display:none" :class="{'show-guide': showGuide}">
                        <div class="bn-top">
                            <button type="button" @click="open('list')" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> 回列表</button>
                            <span class="bn-title">新增 Banner</span>
                        </div>

                        <div class="card bn-card">
                            <div class="card-header" style="background:#eaf3fb"><span class="bn-num">1</span><b>先上傳圖片（電腦版、手機版各一張）</b></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-7 mb-3">
                                        <div class="bn-up bn-d">
                                            <div class="bn-uh"><b>電腦版圖片 <span class="bn-tag">必填</span></b>
                                                <span><i class="bn-chip">1920 × 1080</i><i class="bn-chip">橫式 16:9</i><i class="bn-chip">300KB 內</i></span></div>
                                            <div class="bn-ub">
                                                <div class="bn-frame bn-fd">
                                                    <img v-if="createData.img" :src="createData.img">
                                                    <span v-else class="bn-empty">尚未上傳<br>桌機、平板橫向顯示這張</span>
                                                    <div class="bn-guide"><div class="bn-safe"><i>重要文字、主體放這裡</i></div><div class="bn-bot"><i>底部不要放字（有漸層＋車型查詢卡）</i></div></div>
                                                </div>
                                                <input id="create-img" type="hidden" :value="createData.img">
                                                <a id="lfm-create" data-input="create-img" class="bn-upbtn" :class="{has: createData.img}"><i class="fa fa-picture-o"></i> @{{ createData.img ? '更換圖片' : '選取／上傳圖片' }}</a>
                                                <a v-if="createData.img" href="javascript:void(0)" class="bn-rm" @click="createData.img = ''">移除</a>
                                                <div class="bn-hint">JPG／WebP 皆可</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-5 mb-3">
                                        <div class="bn-up bn-m">
                                            <div class="bn-uh"><b>手機版圖片 <span class="bn-tag">可不傳</span></b>
                                                <span><i class="bn-chip">1080 × 2160</i><i class="bn-chip">直式 1:2</i><i class="bn-chip">300KB 內</i></span></div>
                                            <div class="bn-ub">
                                                <div class="bn-frame bn-fm">
                                                    <img v-if="createData.img_mobile" :src="createData.img_mobile">
                                                    <span v-else class="bn-empty">未上傳<br>會沿用電腦版</span>
                                                    <div class="bn-guide"><div class="bn-safe"><i>字放這裡</i></div><div class="bn-bot"><i>底部留空</i></div></div>
                                                </div>
                                                <input id="create-img-mobile" type="hidden" :value="createData.img_mobile">
                                                <a id="lfm-create-mobile" data-input="create-img-mobile" class="bn-upbtn" :class="{has: createData.img_mobile}"><i class="fa fa-picture-o"></i> @{{ createData.img_mobile ? '更換圖片' : '選取／上傳圖片' }}</a>
                                                <a v-if="createData.img_mobile" href="javascript:void(0)" class="bn-rm" @click="createData.img_mobile = ''">移除</a>
                                                <div class="bn-hint">不傳的話，手機會用電腦版，兩側約被裁掉 35%，字可能被切</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <label class="bn-guidechk"><input type="checkbox" v-model="showGuide"> 預覽框上顯示「安全區」提示線（綠＝文字放這裡、紅＝不要放字）</label>

                                <div class="bn-tips">
                                    <div class="bn-tip t1"><b>文字放中間</b>重要的字與主體集中在畫面中間 60%，兩邊可能被裁。</div>
                                    <div class="bn-tip t2"><b>底部留空</b>最下面約 190px 會蓋暗漸層，還有「車型查詢」卡片壓上去。</div>
                                    <div class="bn-tip t3"><b>手機版建議另做</b>橫圖直接塞手機會被裁掉，直式圖效果最好。</div>
                                    <div class="bn-tip t4"><b>多張會自動輪播</b>順序回列表用「☰ 拖曳」或 ↑↓ 調整，數字小的排前面。</div>
                                </div>

                                <div class="bn-colors">
                                    <div class="bn-cgrp"><div class="bn-ctitle">歌樂 Clarion 專用色（首頁、歌樂專頁用）</div>
                                        <span class="bn-sw"><i style="background:#0D1016"></i>#0D1016 深色底</span>
                                        <span class="bn-sw"><i style="background:#007ABE"></i>#007ABE Azzurro 重點藍</span>
                                        <span class="bn-note">不要整塊染藍，藍色只當重點。</span></div>
                                    <div class="bn-cgrp"><div class="bn-ctitle">MM 專用色（只用在 MM 專頁，首頁不用）</div>
                                        <span class="bn-sw"><i style="background:#F28729"></i>#F28729 橘</span>
                                        <span class="bn-sw"><i style="background:#AF47D2"></i>#AF47D2 藍紫</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="card bn-card">
                            <div class="card-header" style="background:#f1eafb"><span class="bn-num">2</span><b>基本設定</b></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-5 form-group"><label>名稱 <span class="bn-tag o">選填</span></label>
                                        <input type="text" class="form-control" v-model="createData.name" placeholder="例如：2026 秋季 GL-1002 主視覺">
                                        <small class="text-muted">只有後台看得到，方便你辨識；不填也可以。</small></div>
                                    <div class="col-12 col-md-5 form-group"><label>點擊後前往的連結 <span class="bn-tag o">選填</span></label>
                                        <input type="url" class="form-control" v-model="createData.url" placeholder="https://">
                                        <small class="text-muted">不填就是不能點擊。</small></div>
                                    <div class="col-12 col-md-2 form-group"><label>狀態</label>
                                        <div style="display:flex;align-items:center;gap:8px;height:38px">
                                            <label class="sw-toggle" style="margin:0"><input type="checkbox" :checked="createData.status == 1" @change="createData.status = $event.target.checked ? 1 : 0"><span class="sw-slider"></span></label>
                                            <b :style="{color: createData.status == 1 ? '#28a745' : '#8a94a0'}">@{{ createData.status == 1 ? '啟用' : '停用' }}</b></div>
                                        <small class="text-muted">關閉＝前台不顯示</small></div>
                                </div>
                            </div>
                        </div>

                        <div class="bn-save">
                            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> 儲存 Banner</button>
                            <button type="button" @click="open('list')" class="btn btn-secondary btn-lg">取消</button>
                        </div>
                    </form>
                    <form v-on:submit.prevent="updateItem(editData.id)" id="editArea" style="display:none" :class="{'show-guide': showGuide}">
                        <div class="bn-top">
                            <button type="button" @click="open('list')" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> 回列表</button>
                            <span class="bn-title">編輯 Banner</span>
                        </div>

                        <div class="card bn-card">
                            <div class="card-header" style="background:#eaf3fb"><span class="bn-num">1</span><b>先上傳圖片（電腦版、手機版各一張）</b></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-7 mb-3">
                                        <div class="bn-up bn-d">
                                            <div class="bn-uh"><b>電腦版圖片 <span class="bn-tag">必填</span></b>
                                                <span><i class="bn-chip">1920 × 1080</i><i class="bn-chip">橫式 16:9</i><i class="bn-chip">300KB 內</i></span></div>
                                            <div class="bn-ub">
                                                <div class="bn-frame bn-fd">
                                                    <img v-if="editData.img" :src="editData.img">
                                                    <span v-else class="bn-empty">尚未上傳<br>桌機、平板橫向顯示這張</span>
                                                    <div class="bn-guide"><div class="bn-safe"><i>重要文字、主體放這裡</i></div><div class="bn-bot"><i>底部不要放字（有漸層＋車型查詢卡）</i></div></div>
                                                </div>
                                                <input id="edit-img" type="hidden" :value="editData.img">
                                                <a id="lfm-edit" data-input="edit-img" class="bn-upbtn" :class="{has: editData.img}"><i class="fa fa-picture-o"></i> @{{ editData.img ? '更換圖片' : '選取／上傳圖片' }}</a>
                                                <a v-if="editData.img" href="javascript:void(0)" class="bn-rm" @click="editData.img = ''">移除</a>
                                                <div class="bn-hint">JPG／WebP 皆可</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-5 mb-3">
                                        <div class="bn-up bn-m">
                                            <div class="bn-uh"><b>手機版圖片 <span class="bn-tag">可不傳</span></b>
                                                <span><i class="bn-chip">1080 × 2160</i><i class="bn-chip">直式 1:2</i><i class="bn-chip">300KB 內</i></span></div>
                                            <div class="bn-ub">
                                                <div class="bn-frame bn-fm">
                                                    <img v-if="editData.img_mobile" :src="editData.img_mobile">
                                                    <span v-else class="bn-empty">未上傳<br>會沿用電腦版</span>
                                                    <div class="bn-guide"><div class="bn-safe"><i>字放這裡</i></div><div class="bn-bot"><i>底部留空</i></div></div>
                                                </div>
                                                <input id="edit-img-mobile" type="hidden" :value="editData.img_mobile">
                                                <a id="lfm-edit-mobile" data-input="edit-img-mobile" class="bn-upbtn" :class="{has: editData.img_mobile}"><i class="fa fa-picture-o"></i> @{{ editData.img_mobile ? '更換圖片' : '選取／上傳圖片' }}</a>
                                                <a v-if="editData.img_mobile" href="javascript:void(0)" class="bn-rm" @click="editData.img_mobile = ''">移除</a>
                                                <div class="bn-hint">不傳的話，手機會用電腦版，兩側約被裁掉 35%，字可能被切</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <label class="bn-guidechk"><input type="checkbox" v-model="showGuide"> 預覽框上顯示「安全區」提示線（綠＝文字放這裡、紅＝不要放字）</label>

                                <div class="bn-tips">
                                    <div class="bn-tip t1"><b>文字放中間</b>重要的字與主體集中在畫面中間 60%，兩邊可能被裁。</div>
                                    <div class="bn-tip t2"><b>底部留空</b>最下面約 190px 會蓋暗漸層，還有「車型查詢」卡片壓上去。</div>
                                    <div class="bn-tip t3"><b>手機版建議另做</b>橫圖直接塞手機會被裁掉，直式圖效果最好。</div>
                                    <div class="bn-tip t4"><b>多張會自動輪播</b>順序回列表用「☰ 拖曳」或 ↑↓ 調整，數字小的排前面。</div>
                                </div>

                                <div class="bn-colors">
                                    <div class="bn-cgrp"><div class="bn-ctitle">歌樂 Clarion 專用色（首頁、歌樂專頁用）</div>
                                        <span class="bn-sw"><i style="background:#0D1016"></i>#0D1016 深色底</span>
                                        <span class="bn-sw"><i style="background:#007ABE"></i>#007ABE Azzurro 重點藍</span>
                                        <span class="bn-note">不要整塊染藍，藍色只當重點。</span></div>
                                    <div class="bn-cgrp"><div class="bn-ctitle">MM 專用色（只用在 MM 專頁，首頁不用）</div>
                                        <span class="bn-sw"><i style="background:#F28729"></i>#F28729 橘</span>
                                        <span class="bn-sw"><i style="background:#AF47D2"></i>#AF47D2 藍紫</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="card bn-card">
                            <div class="card-header" style="background:#f1eafb"><span class="bn-num">2</span><b>基本設定</b></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-5 form-group"><label>名稱 <span class="bn-tag o">選填</span></label>
                                        <input type="text" class="form-control" v-model="editData.name" placeholder="例如：2026 秋季 GL-1002 主視覺">
                                        <small class="text-muted">只有後台看得到，方便你辨識；不填也可以。</small></div>
                                    <div class="col-12 col-md-5 form-group"><label>點擊後前往的連結 <span class="bn-tag o">選填</span></label>
                                        <input type="url" class="form-control" v-model="editData.url" placeholder="https://">
                                        <small class="text-muted">不填就是不能點擊。</small></div>
                                    <div class="col-12 col-md-2 form-group"><label>狀態</label>
                                        <div style="display:flex;align-items:center;gap:8px;height:38px">
                                            <label class="sw-toggle" style="margin:0"><input type="checkbox" :checked="editData.status == 1" @change="editData.status = $event.target.checked ? 1 : 0"><span class="sw-slider"></span></label>
                                            <b :style="{color: editData.status == 1 ? '#28a745' : '#8a94a0'}">@{{ editData.status == 1 ? '啟用' : '停用' }}</b></div>
                                        <small class="text-muted">關閉＝前台不顯示</small></div>
                                </div>
                            </div>
                        </div>

                        <div class="bn-save">
                            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> 儲存修改</button>
                            <button type="button" @click="open('list')" class="btn btn-secondary btn-lg">取消</button>
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
                                        <th style="width: 20%">排序<small class="text-muted d-block" style="font-weight:400">數字小的在前。拖曳 ☰ 或按 ↑ ↓ 調整，改數字也行（自動儲存）</small></th>
                                        <th style="width: 10%">狀態</th>
                                        <th style="width: 15%">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, num) in items" :key="item.id" :draggable="handleDown" :class="{'sort-over': dragOverIdx === num && dragFrom > num, 'sort-over-down': dragOverIdx === num && dragFrom >= 0 && dragFrom < num}" @dragstart="dragStart(num, $event)" @dragenter.prevent="dragEnter(num)" @dragover.prevent @drop.prevent="dropRow(num)" @dragend="dragEnd">
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
                                        <td style="white-space:nowrap">
                                            <span class="sort-handle" title="按住拖曳" @mousedown="handleDown = true" @mouseup="handleDown = false">☰</span>
                                            <a href="javascript:void(0)" class="sort-arrow" title="往前一格" @click="moveItem(num, -1)">↑</a>
                                            <a href="javascript:void(0)" class="sort-arrow" title="往後一格" @click="moveItem(num, 1)">↓</a>
                                            <input type="number" class="form-control sort-num" v-model="item.sort" @change="sortItems">
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
    <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
    <script type="text/javascript">
        var vm = new Vue({
            mixins: [window.sortMixin || {}],
            el: '#container',
            data: {
                url: '{{ route('admin.banner') }}',
                items: {},
                createData: {},
                editData: {},
                showGuide: true,
                page: 1,
                pagination: {
                    start: 0,
                    total: 0,
                    current_page: 1
                },
            },
            created: function() {
                this.getItems();
                // 讓瀏覽器「上一頁」能回到列表：切換到新增/編輯畫面時有登記一筆瀏覽器紀錄（見 open()），
                // 按上一頁會觸發這裡，直接切回列表，而不是離開這一頁。
                window.addEventListener('popstate', function() {
                    vm.open('list');
                });
            },
            mounted: function() {
                $('#lfm-create').filemanager('file', {prefix: 'filemanager'});
                $('#lfm-create-mobile').filemanager('file', {prefix: 'filemanager'});
                $('#lfm-edit').filemanager('file', {prefix: 'filemanager'});
                $('#lfm-edit-mobile').filemanager('file', {prefix: 'filemanager'});
                // 檔案管理員選好圖後會改 hidden input 並觸發 change，這裡同步回 Vue 狀態（預覽框即時更新）
                $('#create-img').on('change', function() { vm.$set(vm.createData, 'img', $(this).val()); });
                $('#create-img-mobile').on('change', function() { vm.$set(vm.createData, 'img_mobile', $(this).val()); });
                $('#edit-img').on('change', function() { vm.$set(vm.editData, 'img', $(this).val()); });
                $('#edit-img-mobile').on('change', function() { vm.$set(vm.editData, 'img_mobile', $(this).val()); });
            },
            methods: {
                clear: function() {
                    vm.createData = {
                        name: '',
                        url: '',
                        img: '',
                        img_mobile: '',
                        status: 1,
                    };
                    vm.editData = {};
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
                            history.pushState({}, '');
                            $('#listArea').fadeOut(0);
                            $('#createArea').fadeIn(300);
                            break;

                        case 'edit':
                            history.pushState({}, '');
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
                                page: page,
                                per_page: 500
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
                        if (!vm.createData.img) { vm.showMessage('error', '請先上傳電腦版圖片'); return; }
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
                        if (!vm.editData.img) { vm.showMessage('error', '請先上傳電腦版圖片'); return; }
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
                // 排序只在同一個「啟用／停用」組內調整（列表固定先排啟用、再排停用）
                sortGroupKey: function(item) { return parseInt(item.status, 10) === 1 ? 1 : 0; },
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

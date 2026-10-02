@extends('admin.layout.master')

@section('unit.list_banner', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="list_banner" />

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
 .ruler{position:relative;height:30px;margin:-6px auto 10px;font:600 11px/1 sans-serif;color:#495563}
        .ruler span{position:absolute;top:0;height:18px;border:1px solid #9aa7b8;border-top:0;text-align:center;padding-top:5px;white-space:nowrap}
        .ruler .a{background:#fff4e0;border-color:#ff9500;color:#a15c00}.ruler .b{background:#eafaf0;border-color:#34c759;color:#1f6b34}.ruler .c{background:#f0f2f5}
        .vr{font-size:12px;color:#495563;margin:0 0 10px;text-align:center}.vr b{color:#a15c00}
        .ovt{position:absolute;left:23.6%;top:50%;transform:translateY(-50%);width:29%;text-align:left;pointer-events:none;color:#0d1a2b;z-index:2}
        .ovt .k{font:700 clamp(5px,.9vw,10px)/1 sans-serif;letter-spacing:.3em;color:#e8851c}.ovt .t{font:900 clamp(11px,2.3vw,26px)/1.15 sans-serif;margin:3px 0}.ovt .d{font:300 clamp(5px,1vw,11px)/1.5 sans-serif;color:#41506b}
        .bn-n{display:inline-block;color:#fff;border-radius:50%;width:20px;height:20px;line-height:20px;text-align:center;font-size:12px;margin-right:4px;font-weight:700}
        .lb-color{display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-top:8px;font-size:13px}
        .lb-clab{color:#6b7785;margin-right:2px}
        .lb-sw{width:28px;height:28px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 0 1px #aab4c0;cursor:pointer;padding:0}
        .lb-sw.on{box-shadow:0 0 0 2px #007abe}
        .lb-def{width:auto;border-radius:14px;padding:0 10px;background:#fff;font-size:12px;color:#495563}
        .lb-pick{display:inline-flex;align-items:center;gap:4px;margin:0;cursor:pointer;color:#0b5c8a}
        .lb-pick input{width:28px;height:28px;border:0;padding:0;background:none;cursor:pointer}
        .lb-hex{color:#8a94a0;font-size:12px;margin-left:4px}
    </style>
    <style>
        .bn-fd4{aspect-ratio:4/1;width:100%}.bn-fm9{aspect-ratio:16/9;width:70%;min-width:200px}
        .bn-fog{position:absolute;left:0;top:0;bottom:0;width:66%;background:linear-gradient(to right,rgba(255,255,255,.85),rgba(255,255,255,0));pointer-events:none;display:none}
        .show-guide .bn-fog{display:block}
        .bn-fog i{position:absolute;left:8px;top:50%;transform:translateY(-50%);font:700 11px/1.3 sans-serif;color:#b45309;font-style:normal;background:#fff4e0;padding:3px 6px;border-radius:3px;border:1px dashed #ff9500}
        .bn-focus{position:absolute;left:70%;right:3%;top:10%;bottom:10%;border:2px dashed #4cd964;border-radius:4px;pointer-events:none;display:none}
        .show-guide .bn-focus{display:block}
        .bn-focus i{position:absolute;top:-1px;left:-1px;background:#4cd964;color:#06310f;font:700 11px/1 sans-serif;padding:3px 6px;font-style:normal}
        .bn-full{position:absolute;inset:4px;border:2px dashed #4cd964;border-radius:4px;pointer-events:none;display:none}
        .show-guide .bn-full{display:block}
        .bn-full i{position:absolute;top:-1px;left:-1px;background:#4cd964;color:#06310f;font:700 11px/1 sans-serif;padding:3px 6px;font-style:normal}
        .lb-st{display:inline-block;border-radius:20px;padding:1px 10px;font-size:12px}
        .lb-on{background:#e3f6e8;color:#1f8a3b}.lb-off{background:#eef1f5;color:#7a8594}
        .lb-grp td{background:#f4f6f9;font-weight:700;color:#495563}
    </style>

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            {{-- ========== 編輯畫面 ========== --}}
            <div class="row" v-show="view === 'edit'">
                <div class="col-12 col-xl-10">
                    <form v-on:submit.prevent="saveItem()" :class="{'show-guide': showGuide}">
                        <div class="bn-top">
                            <button type="button" class="btn btn-secondary" @click="backToList"><i class="fas fa-arrow-left"></i> 回列表</button>
                            <span class="bn-title">設定 Banner：@{{ form.page_name }}<span v-if="form.type_name && form.type_name !== '預設'"> ／ @{{ form.type_name }}</span></span>
                            <a v-if="form.url" :href="'https://clarion.meimai.com.tw' + form.url" target="_blank" class="btn btn-outline-primary btn-sm" style="margin-left:auto">看前台這一頁 <i class="fas fa-external-link-alt"></i></a>
                        </div>

                        <div class="card bn-card">
                            <div class="card-header" style="background:#eaf3fb"><span class="bn-num">1</span><b>上傳圖片（電腦版、手機版各一張，都可以不傳）</b></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-7 mb-3">
                                        <div class="bn-up bn-d">
                                            <div class="bn-uh"><b>電腦版圖片 <span class="bn-tag">可不傳</span></b>
                                                <span><i class="bn-chip">1920 × 480</i><i class="bn-chip">橫長條 4:1</i><i class="bn-chip">300KB 內</i></span></div>
                                            <div class="bn-ub">
                                                <div class="bn-frame bn-fd4">
                                                    <img v-if="form.img" :src="form.img">
                                                    <span v-else class="bn-empty">尚未上傳<br>會用預設漸層背景</span>
                                                    <div class="bn-fog"><i>文字壓這裡<br>左邊留空</i></div>
                                                    <div class="ovt" v-if="form.kicker || form.title || form.description"><div class="k" :style="form.kicker_color ? {color: form.kicker_color} : null">@{{ form.kicker }}</div><div class="t" :style="form.title_color ? {color: form.title_color} : null">@{{ form.title }}</div><div class="d" :style="form.desc_color ? {color: form.desc_color} : null">@{{ form.description }}</div></div>
                                                    <div class="bn-focus"><i>主體放這裡</i></div>
                                                </div>
                                                <input id="edit-img" type="hidden" :value="form.img">
                                                <div class="ruler"><span class="c" style="left:0;width:23.6%">左邊留白 450px</span><span class="a" style="left:23.6%;width:29%">文字區 約 560px 寬</span><span class="c" style="left:52.6%;width:14.1%">空 約 270px</span><span class="b" style="left:66.7%;width:33.3%">主體區：距左 1280px 起，寬 640px</span></div>
                                                <div class="vr">文字區<b>上下置中</b>，高約 220px → 上方、下方各約 130px 是空的（以 1920 × 480 的圖為準）</div>
                                                <a id="lfm-edit" data-input="edit-img" class="bn-upbtn" :class="{has: form.img}"><i class="fa fa-picture-o"></i> @{{ form.img ? '更換圖片' : '選取／上傳圖片' }}</a>
                                                <a v-if="form.img" href="javascript:void(0)" class="bn-rm" @click="form.img = ''">移除</a>
                                                <div class="bn-hint">電腦、平板橫向顯示。JPG／PNG 皆可</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-5 mb-3">
                                        <div class="bn-up bn-m">
                                            <div class="bn-uh"><b>手機版圖片 <span class="bn-tag">可不傳</span></b>
                                                <span><i class="bn-chip">1080 × 608</i><i class="bn-chip">橫式 16:9</i><i class="bn-chip">300KB 內</i></span></div>
                                            <div class="bn-ub">
                                                <div class="bn-frame bn-fm9">
                                                    <img v-if="form.img_mobile" :src="form.img_mobile">
                                                    <span v-else class="bn-empty">未上傳<br>會沿用電腦版（會被裁）</span>
                                                    <div class="bn-full"><i>完整露出，不蓋白霧</i></div>
                                                </div>
                                                <input id="edit-img-mobile" type="hidden" :value="form.img_mobile">
                                                <div class="vr" style="margin-top:-4px">整張完整露出，<b>主體離四邊至少 54px</b>（以 1080 × 608 為準），文字另外顯示在圖下方</div>
                                                <a id="lfm-edit-mobile" data-input="edit-img-mobile" class="bn-upbtn" :class="{has: form.img_mobile}"><i class="fa fa-picture-o"></i> @{{ form.img_mobile ? '更換圖片' : '選取／上傳圖片' }}</a>
                                                <a v-if="form.img_mobile" href="javascript:void(0)" class="bn-rm" @click="form.img_mobile = ''">移除</a>
                                                <div class="bn-hint">螢幕寬 640px 以下用這張：圖在上、文字在下</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <label class="bn-guidechk"><input type="checkbox" v-model="showGuide"> 預覽框上顯示提示線（橘＝文字會壓在這、綠＝主體放這裡）</label>

                                <div class="bn-tips">
                                    <div class="bn-tip t3"><b>左邊留空</b>電腦版左邊約 2/3 會蓋淺色漸層放文字，這裡放單純的底。</div>
                                    <div class="bn-tip t1"><b>主體放右邊 1/3</b>車子、產品放右邊，才不會被文字蓋住。</div>
                                    <div class="bn-tip t4"><b>手機版另做最好</b>電腦版是 4:1 長條，塞進手機會被左右裁掉約七成。</div>
                                    <div class="bn-tip t2"><b>沒傳也不會空白</b>前台自動用預設漸層背景；清除後也是。</div>
                                </div>

                                <div class="bn-colors">
                                    <div class="bn-cgrp"><div class="bn-ctitle">歌樂 Clarion 專用色</div>
                                        <span class="bn-sw"><i style="background:#0D1016"></i>#0D1016 深色底</span>
                                        <span class="bn-sw"><i style="background:#007ABE"></i>#007ABE Azzurro 重點藍</span>
                                        <span class="bn-note">不要整塊染藍，藍色只當重點。</span></div>
                                    <div class="bn-cgrp"><div class="bn-ctitle">MM 專用色（只用在 MM 專頁）</div>
                                        <span class="bn-sw"><i style="background:#F28729"></i>#F28729 橘</span>
                                        <span class="bn-sw"><i style="background:#AF47D2"></i>#AF47D2 藍紫</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="card bn-card">
                            <div class="card-header" style="background:#fff4e6"><span class="bn-num">2</span><b>左邊文字（選填）</b><span class="text-muted" style="margin-left:10px;font-size:13px">不填就用網站原本的字；上面預覽框會即時顯示文字位置</span></div>
                            <div class="card-body">
                                <div class="row" style="margin-bottom:14px">
                                    <div class="col-12 col-md-6">
                                        <div style="background:linear-gradient(100deg,#eef3f8,#cfdbe7);border-radius:8px;padding:16px 18px;border:1px solid #d5dde6">
                                            <div style="font:700 11px/1 sans-serif;letter-spacing:.3em;color:#e8851c"><span class="bn-n" style="background:#e8851c">①</span> 小標題（例：MEIMAI ｜ 多媒體安卓機）</div>
                                            <div style="font:900 24px/1.2 sans-serif;margin:8px 0;color:#0d1a2b"><span class="bn-n" style="background:#007abe">②</span> 大標題</div>
                                            <div style="font:300 13px/1.6 sans-serif;color:#41506b"><span class="bn-n" style="background:#6c757d">③</span> 說明文字（一兩句話）</div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6" style="font-size:13.5px;line-height:1.7;display:flex;align-items:center">
                                        <div>左圖就是前台這一頁<b>最上方 Banner 左邊</b>的三行字，對應下面三個欄位。<br>下面已經填好<b>目前網站上正在顯示的文字</b>，直接改就會換；清空某一格，前台那一格會退回網站原本的字。</div>
                                    </div>
                                </div>
                                <div class="form-group"><label><span class="bn-n" style="background:#e8851c">①</span> 小標題 <span class="bn-tag o">選填</span></label>
                                    <input type="text" class="form-control" v-model="form.kicker" maxlength="100" placeholder="例：MEIMAI ｜ 多媒體安卓機">
                                    <div class="lb-color">
                                        <span class="lb-clab">文字顏色</span>
                                        <button type="button" class="lb-sw lb-def" :class="{on: !form.kicker_color}" @click="form.kicker_color = ''" title="用網站原本的顏色">預設</button>
                                        <button type="button" v-for="c in colorPresets" :key="c.v" class="lb-sw" :class="{on: form.kicker_color === c.v}" :style="{background: c.v}" :title="c.n" @click="form.kicker_color = c.v"></button>
                                        <label class="lb-pick" title="自己挑顏色"><input type="color" :value="form.kicker_color || '#000000'" @input="form.kicker_color = $event.target.value">自選</label>
                                        <span class="lb-hex">@{{ form.kicker_color || '預設' }}</span>
                                    </div></div>
                                <div class="form-group"><label><span class="bn-n" style="background:#007abe">②</span> 大標題 <span class="bn-tag o">選填</span></label>
                                    <input type="text" class="form-control" v-model="form.title" maxlength="100" placeholder="例：多媒體安卓機">
                                    <div class="lb-color">
                                        <span class="lb-clab">文字顏色</span>
                                        <button type="button" class="lb-sw lb-def" :class="{on: !form.title_color}" @click="form.title_color = ''" title="用網站原本的顏色">預設</button>
                                        <button type="button" v-for="c in colorPresets" :key="c.v" class="lb-sw" :class="{on: form.title_color === c.v}" :style="{background: c.v}" :title="c.n" @click="form.title_color = c.v"></button>
                                        <label class="lb-pick" title="自己挑顏色"><input type="color" :value="form.title_color || '#000000'" @input="form.title_color = $event.target.value">自選</label>
                                        <span class="lb-hex">@{{ form.title_color || '預設' }}</span>
                                    </div></div>
                                <div class="form-group"><label><span class="bn-n" style="background:#6c757d">③</span> 說明文字 <span class="bn-tag o">選填</span></label>
                                    <textarea class="form-control" rows="3" v-model="form.description" maxlength="500" placeholder="一兩句話就好，建議 60 字內"></textarea>
                                    <div class="lb-color">
                                        <span class="lb-clab">文字顏色</span>
                                        <button type="button" class="lb-sw lb-def" :class="{on: !form.desc_color}" @click="form.desc_color = ''" title="用網站原本的顏色">預設</button>
                                        <button type="button" v-for="c in colorPresets" :key="c.v" class="lb-sw" :class="{on: form.desc_color === c.v}" :style="{background: c.v}" :title="c.n" @click="form.desc_color = c.v"></button>
                                        <label class="lb-pick" title="自己挑顏色"><input type="color" :value="form.desc_color || '#000000'" @input="form.desc_color = $event.target.value">自選</label>
                                        <span class="lb-hex">@{{ form.desc_color || '預設' }}</span>
                                    </div>
                                    <small class="text-muted">文字是網頁上的真實文字，位置、距離、換行都會自動排好，<b>不用再把字做進圖片裡</b>，也不用自己算距離。三格可以單獨填，沒填的那格沿用網站原本的字。手機版文字會自動排在圖的下方。</small></div>
                                <div class="bn-tips"><div class="bn-tip t3"><b>目前只有中文版會用你填的字</b>切到英文版時，仍顯示網站原本的英文。</div></div>
                            </div>
                        </div>

                        <div class="bn-save">
                            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> 儲存 Banner</button>
                            <button type="button" class="btn btn-secondary btn-lg" @click="backToList">取消</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========== 列表 ========== --}}
            <div class="row" v-show="view === 'list'">
                <div class="col-12">
                    <div class="alert alert-info" style="margin-bottom:12px">
                        這裡設定各「產品列表／總覽」頁最上方的橫長條 Banner。<b>按右邊「設定」</b>進去上傳圖片；<b>還沒設定的頁面，前台會自動用預設漸層背景</b>，不會空白。
                    </div>
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">各頁面 Banner 設定狀況</h3></div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>頁面</th>
                                        <th>分類</th>
                                        <th>電腦版</th>
                                        <th>手機版</th>
                                        <th style="width: 12%">狀態</th>
                                        <th style="width: 17%">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in items" :key="row.page_key + '-' + row.type_key">
                                        <td>@{{ row.page_name }}</td>
                                        <td>@{{ row.type_name }}<div v-if="row.url" style="font-size:12px"><a :href="'https://clarion.meimai.com.tw' + row.url" target="_blank" class="text-muted">前台網址 https://clarion.meimai.com.tw@{{ row.url }}</a></div></td>
                                        <td>
                                            <img v-if="row.img" :src="row.img" style="height:44px;">
                                            <span v-else class="text-muted" style="font-size:12px">未設定</span>
                                        </td>
                                        <td>
                                            <img v-if="row.img_mobile" :src="row.img_mobile" style="height:44px;">
                                            <span v-else class="text-muted" style="font-size:12px">未設定<br>（沿用電腦版）</span>
                                        </td>
                                        <td>
                                            <span class="lb-st" :class="(row.img || row.img_mobile || row.title || row.kicker || row.description) ? 'lb-on' : 'lb-off'">@{{ (row.img || row.img_mobile || row.title || row.kicker || row.description) ? '已設定' : '用預設' }}</span>
                                        </td>
                                        <td class="method-button">
                                            <button type="button" class="btn btn-primary btn-sm" @click="openEdit(row)">
                                                <i class="fas fa-pencil-alt"></i>
                                                設定
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" v-if="row.img || row.img_mobile" @click="clearItem(row)">
                                                <i class="fas fa-trash"></i>
                                                清除
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                url: '{{ route('admin.list_banner') }}',
                pagesConfig: @json($pages),
                view: 'list',
                showGuide: true,
                items: [],
                form: {
                    page_key: '',
                    type_key: 'default',
                    page_name: '',
                    type_name: '',
                    url: '',
                    img: '',
                    img_mobile: '',
                    kicker: '',
                    title: '',
                    description: '',
                    kicker_color: '',
                    title_color: '',
                    desc_color: ''
                },
                // 常用文字顏色：深色字適合淺色底圖、白字適合深色底圖；藍／橘是品牌色
                colorPresets: [
                    { v: '#0D1A2B', n: '深藍黑（網站標題原色）' },
                    { v: '#FFFFFF', n: '白色（深色底圖用）' },
                    { v: '#007ABE', n: '歌樂藍 Azzurro' },
                    { v: '#F28729', n: 'MM 橘' },
                    { v: '#AF47D2', n: 'MM 藍紫' },
                    { v: '#41506B', n: '灰藍（說明文字原色）' }
                ]
            },
            created: function() {
                // getItems 內用的是全域變數 vm，但 created 當下 vm 還沒賦值（new Vue 還沒跑完），
                // 直接呼叫會噴錯、清單永遠是空的；延到下一輪再呼叫。
                this.$nextTick(function() { vm.getItems(); });
                // 讓瀏覽器「上一頁」能回到列表（編輯畫面開啟時有登記一筆瀏覽器紀錄，見 openEdit）
                window.addEventListener('popstate', function() {
                    vm.view = 'list';
                });
            },
            mounted: function() {
                $('#lfm-edit').filemanager('file', {prefix: 'filemanager'});
                $('#lfm-edit-mobile').filemanager('file', {prefix: 'filemanager'});
                // 檔案管理員選好圖後會改 hidden input 並觸發 change，同步回 Vue 狀態（預覽框即時更新）
                $('#edit-img').on('change', function() { vm.form.img = $(this).val(); });
                $('#edit-img-mobile').on('change', function() { vm.form.img_mobile = $(this).val(); });
            },
            methods: {
                getItems: function() {
                    try {
                        axios.get(vm.url + '/all').then(function(response) {
                            vm.items = response.data.items;
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                openEdit: function(row) {
                    vm.form = {
                        page_key: row.page_key,
                        type_key: row.type_key,
                        page_name: row.page_name,
                        url: row.url || '',
                        type_name: row.type_name,
                        img: row.img || '',
                        img_mobile: row.img_mobile || '',
                        kicker: row.kicker || '',
                        title: row.title || '',
                        description: row.description || '',
                        kicker_color: row.kicker_color || '',
                        title_color: row.title_color || '',
                        desc_color: row.desc_color || ''
                    };
                    history.pushState({}, '');
                    vm.view = 'edit';
                    $('html, body').scrollTop(0);
                },
                backToList: function() {
                    vm.view = 'list';
                    $('html, body').scrollTop(0);
                },
                saveItem: function() {
                    try {
                        axios.patch(vm.url + '/' + vm.form.page_key + '/' + vm.form.type_key, {
                            img: vm.form.img,
                            img_mobile: vm.form.img_mobile,
                            kicker: vm.form.kicker,
                            title: vm.form.title,
                            description: vm.form.description,
                            kicker_color: vm.form.kicker_color,
                            title_color: vm.form.title_color,
                            desc_color: vm.form.desc_color
                        }).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems();
                            vm.view = 'list';
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                clearItem: function(row) {
                    if (confirm('確定要清除這個 Banner？清除後前台會改用預設背景。') !== true) return false;

                    try {
                        axios.patch(vm.url + '/' + row.page_key + '/' + row.type_key, {
                            img: '',
                            img_mobile: '',
                            kicker: '',
                            title: '',
                            description: '',
                            kicker_color: '',
                            title_color: '',
                            desc_color: ''
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

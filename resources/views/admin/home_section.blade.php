@extends('admin.layout.master')

@section('unit.home_section', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="home_section" />

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
        .bn-fd16{aspect-ratio:16/9;width:100%}.bn-fm916{aspect-ratio:9/16;width:34%;min-width:130px}
        .hz{position:absolute;display:none;pointer-events:none}.show-guide .hz{display:block}
        .hz-text{border:2px dashed #ff9500;background:rgba(255,149,0,.12);border-radius:4px}
        .hz-text i,.hz-sub i{position:absolute;top:-1px;left:-1px;font:700 11px/1 sans-serif;padding:3px 6px;font-style:normal}
        .hz-text i{background:#ff9500;color:#fff}.hz-sub{border:2px dashed #4cd964;border-radius:4px}.hz-sub i{background:#4cd964;color:#06310f}
        .lb-color{display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-top:6px;font-size:13px}
        .lb-clab{color:#6b7785;margin-right:2px}
        .lb-sw{width:26px;height:26px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 0 1px #aab4c0;cursor:pointer;padding:0}
        .lb-sw.on{box-shadow:0 0 0 2px #007abe}
        .lb-def{width:auto;border-radius:14px;padding:0 10px;background:#fff;font-size:12px;color:#495563}
        .lb-pick{display:inline-flex;align-items:center;gap:4px;margin:0;cursor:pointer;color:#0b5c8a}
        .lb-pick input{width:26px;height:26px;border:0;padding:0;background:none;cursor:pointer}
        .lb-hex{color:#8a94a0;font-size:12px;margin-left:4px}
        .bn-n{display:inline-block;color:#fff;border-radius:50%;width:20px;height:20px;line-height:20px;text-align:center;font-size:12px;margin-right:4px;font-weight:700}
        .lb-st{display:inline-block;border-radius:20px;padding:1px 10px;font-size:12px}.lb-on{background:#e3f6e8;color:#1f8a3b}.lb-off{background:#eef1f5;color:#7a8594}
        .pm{border-radius:10px;overflow:hidden;box-shadow:0 1px 5px rgba(0,0,0,.3);background:#fff;max-width:340px}
        .pm-b{position:relative;display:flex;align-items:center;gap:8px;padding:6px 10px;color:#fff;font-size:12px;line-height:1.35;border-bottom:1px solid rgba(255,255,255,.25);background-size:cover;background-position:center}
        .pm-b.light{color:#333;border-bottom-color:#e1e5ea}
        .pm-b.zone{cursor:pointer}.pm-b.zone:hover{outline:3px solid #ff9500;outline-offset:-3px}
        .pm-b.active{outline:3px solid #ff9500;outline-offset:-3px}
        .pm-b .pm-t{flex:1;min-width:0;text-shadow:0 1px 3px rgba(0,0,0,.6)}.pm-b.light .pm-t{text-shadow:none}
        .pm-b small{display:block;opacity:.8;font-size:11px}
        .pm-n{flex:none;width:20px;height:20px;border-radius:50%;background:#fff;color:#333;font-weight:700;text-align:center;line-height:20px;font-size:12px}
        .pm-b.zone .pm-n{background:#ff9500;color:#fff}
        .pm-go{flex:none;font-size:11px;color:#9ad1ff!important;text-decoration:underline;text-shadow:none}.pm-b.light .pm-go{color:#0b5c8a!important}
        .pm-tag{flex:none;font-size:11px;background:#ff9500;color:#fff;border-radius:10px;padding:0 7px;text-shadow:none}
        .hs-pvin .wrapin{width:100%;max-width:1080px;margin:0 auto;padding:0 26px;box-sizing:border-box}
    </style>
    <style>
        .hs-pos { display:flex; gap:5px; margin:6px 0; }
        .hs-pos i { display:block; width:30px; height:9px; background:#e1e5ea; border-radius:2px; }
        .hs-pos i.on { background:#007bff; }
        .hs-thumb { width:190px; height:107px; background:#0d1016 center/cover no-repeat; border-radius:4px; flex:none; display:flex; align-items:center; justify-content:center; color:#7d8794; font-size:12px; }
        .hs-thumb-m { width:60px; height:107px; background:#0d1016 center/cover no-repeat; border-radius:4px; flex:none; }
        .hs-step { display:inline-flex; width:24px; height:24px; border-radius:50%; background:#007bff; color:#fff; align-items:center; justify-content:center; font-size:13px; margin-right:8px; }
        .hs-up { border:2px dashed #9bbbe0; border-radius:8px; background:#f5f9ff; min-height:110px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#3a6ea5; font-size:13px; text-align:center; padding:8px; cursor:pointer; }
        .hs-up img { max-height:100px; max-width:100%; border-radius:4px; }
        .hs-seg { display:inline-flex; border:1px solid #ced4da; border-radius:4px; overflow:hidden; }
        .hs-seg span { padding:6px 14px; font-size:13px; background:#fff; cursor:pointer; }
        .hs-seg span.on { background:#007bff; color:#fff; }
        .hs-pvbox { position:relative; width:100%; padding-top:56.25%; border-radius:6px; overflow:hidden; background:#0d1016; }
        .hs-pvin { position:absolute; left:0; top:0; transform-origin:0 0; background:#0d1016 center/cover no-repeat; color:#fff; display:flex; align-items:center; font-family:"Noto Sans TC",sans-serif; }
        .hs-safe { position:absolute; inset:10% 20%; border:2px dashed rgba(255,255,255,.55); pointer-events:none; }
        /* 預覽用的文字樣式：與前台 .cms-block 相近（前台用 clamp 自適應，這裡用固定值近似） */
        .hs-pvin .cms-block { max-width:720px; width:100%; }
        .hs-pvin .cms-align-center { margin:0 auto; text-align:center; }
        .hs-pvin .cms-align-right { margin-left:auto; text-align:right; }
        .hs-pvin .cms-k { font-size:12px; letter-spacing:4px; font-weight:700; color:#4fb6ea; margin:0 0 14px; }
        .hs-pvin .cms-t { font-weight:900; line-height:1.15; color:#fff; margin:0 0 16px; text-shadow:0 2px 24px rgba(0,0,0,.45); }
        .hs-pvin .cms-d { line-height:1.8; color:rgba(255,255,255,.86); margin:0 0 26px; white-space:pre-line; }
        .hs-pvin .cms-b { display:inline-flex; align-items:center; height:48px; padding:0 28px; border-radius:10px; background:#007abe; color:#fff; font-weight:800; text-decoration:none; }
        .hs-pvin.d { width:1280px; height:720px; padding:0; }
        .hs-pvin.d .cms-t { font-size:56px; } .hs-pvin.d .cms-d { font-size:18px; }
        .hs-pvin.m { width:390px; height:780px; padding:0 26px; }
        .hs-pvin.m .cms-t { font-size:34px; } .hs-pvin.m .cms-d { font-size:15px; }
    </style>

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>

            {{-- ========== 列表 ========== --}}
            <div v-show="view === 'list'">
                <div class="alert alert-info" style="margin-bottom:12px">
                    首頁往下捲，有 <b>3 段滿版大圖＋文字</b>。按右邊「<b>設定</b>」進去，先上傳圖片、再填文字，右邊會即時預覽電腦版和手機版。<br>
                    <b>沒設定圖片的區塊，前台只顯示深色底；沒填文字的區塊，前台不顯示文字。</b>要自己排版（改字體、顏色）的人，進去選「進階模式」。
                </div>
                <div class="row">
                    <div class="col-12 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">首頁長這樣（由上往下）</h3></div>
                            <div class="card-body">
                                <div class="pm">
                            <div v-for="b in homeBlocks" :key="b.n" class="pm-b" :class="{zone: b.zone, active: b.zone && b.key === editKey, light: b.light}" :style="blockStyle(b)" @click="b.zone ? openEdit(b.key) : null">
                                <span class="pm-n">@{{ b.n }}</span>
                                <span class="pm-t"><b>@{{ b.name }}</b><small>@{{ b.sub }}</small></span>
                                <span v-if="b.zone" class="pm-tag">在這裡設定</span>
                                <a v-else :href="b.link" class="pm-go" @click.stop>@{{ b.linkText }}</a>
                            </div>
                        </div>
                                <div class="text-muted" style="font-size:12px;margin-top:8px"><span class="pm-tag">在這裡設定</span> 的區塊（3、4、6）點一下就能進去設定；其他區塊點右邊的連結，到各自的管理頁上傳圖片或改內容。</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">滿版區塊設定狀況（3 段）</h3></div>
                            <div class="card-body">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>區塊</th>
                                            <th>電腦版</th>
                                            <th>手機版</th>
                                            <th>文字</th>
                                            <th style="width: 12%">狀態</th>
                                            <th style="width: 12%">功能</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="section in sections" :key="section.key">
                                            <td><b>@{{ section.title }}</b><div class="text-muted" style="font-size:12px">首頁第 @{{ section.slot }} 段　@{{ section.where }}</div></td>
                                            <td><div class="hs-thumb" style="width:120px;height:68px" :style="thumbStyle(section.key, 'img')"><span v-if="!rowOf(section.key).img">未上傳</span></div></td>
                                            <td><div class="hs-thumb-m" style="width:38px;height:68px" :style="thumbStyle(section.key, 'img_mobile')"></div><span v-if="!rowOf(section.key).img_mobile" class="text-muted" style="font-size:12px">未上傳<br>（沿用電腦版）</span></td>
                                            <td><b v-if="summary(section.key)">@{{ summary(section.key) }}</b><span v-else class="text-muted" style="font-size:12px">還沒填文字</span></td>
                                            <td><span class="lb-st" :class="isSet(section.key) ? 'lb-on' : 'lb-off'">@{{ isSet(section.key) ? '已設定' : '未設定' }}</span></td>
                                            <td class="method-button"><button type="button" class="btn btn-primary btn-sm" @click="openEdit(section.key)"><i class="fas fa-pencil-alt"></i> 設定</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== 編輯頁 ========== --}}
            <div v-show="view === 'edit'" :class="{'show-guide': showSafe}">
                <div class="bn-top">
                    <button type="button" class="btn btn-secondary" @click="backToList"><i class="fas fa-arrow-left"></i> 回列表</button>
                    <span class="bn-title">設定：@{{ current.title }}</span>
                    <span class="text-muted" style="font-size:13px">位置：@{{ current.where }}</span>
                </div>

                <div class="row">
                    <div class="col-12 col-xl-8">
                        {{-- 步驟 1：圖片 --}}
                        <div class="card bn-card">
                            <div class="card-header" style="background:#eaf3fb"><span class="bn-num">1</span><b>上傳圖片（電腦版、手機版各一張）</b></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-7 mb-3">
                                        <div class="bn-up bn-d">
                                            <div class="bn-uh"><b>電腦版圖片 <span class="bn-tag">建議要傳</span></b>
                                                <span><i class="bn-chip">2560 × 1440</i><i class="bn-chip">橫式 16:9</i><i class="bn-chip">300KB 內</i></span></div>
                                            <div class="bn-ub">
                                                <div class="bn-frame bn-fd16">
                                                    <img v-if="imgDesk" :src="imgDesk">
                                                    <span v-else class="bn-empty">尚未上傳<br>前台只顯示深色底</span>
                                                    <div class="hz hz-text" style="left:10%;width:34%;top:32%;height:36%"><i>文字區（靠左時）</i></div>
                                                    <div class="hz hz-sub" style="left:58%;right:3%;top:10%;bottom:10%"><i>主體放這裡</i></div>
                                                </div>
                                                <div class="ruler"><span class="c" style="left:0;width:10%">邊 10%</span><span class="a" style="left:10%;width:34%">文字區 約 10%～44%</span><span class="c" style="left:44%;width:14%">空</span><span class="b" style="left:58%;width:42%">主體區：距左 58% 起（約 1480px）</span></div>
                                                <div class="vr">文字<b>上下置中</b>，高約 30%（約 430px）→ 上方、下方各約 <b>32%（約 460px）</b>是空的。文字寬度最多約 720px，螢幕越寬，文字越往中間靠（以 2560 × 1440 為準）</div>
                                                <input id="img-desk" type="hidden">
                                                <a id="lfm-desk" data-input="img-desk" data-preview="hs-dummy" class="bn-upbtn" :class="{has: imgDesk}"><i class="fa fa-picture-o"></i> @{{ imgDesk ? '更換圖片' : '選取／上傳圖片' }}</a>
                                                <a v-if="imgDesk" href="javascript:void(0)" class="bn-rm" @click="clearImg('desk')">移除</a>
                                                <div class="bn-hint">JPG／WebP 皆可</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-5 mb-3">
                                        <div class="bn-up bn-m">
                                            <div class="bn-uh"><b>手機版圖片 <span class="bn-tag">可不傳</span></b>
                                                <span><i class="bn-chip">1080 × 1920</i><i class="bn-chip">直式 9:16</i><i class="bn-chip">300KB 內</i></span></div>
                                            <div class="bn-ub">
                                                <div class="bn-frame bn-fm916">
                                                    <img v-if="imgMob" :src="imgMob">
                                                    <span v-else class="bn-empty" style="font-size:12px;text-align:center">未上傳<br>沿用電腦版</span>
                                                    <div class="hz hz-text" style="left:7%;right:7%;top:36%;height:28%"><i>文字區</i></div>
                                                    <div class="hz hz-sub" style="left:7%;right:7%;top:6%;height:24%"><i>主體</i></div>
                                                    <div class="hz hz-sub" style="left:7%;right:7%;bottom:4%;height:24%"><i>或放這</i></div>
                                                </div>
                                                <div class="vr" style="margin-top:-4px">文字<b>上下置中</b>，高約 28%（約 540px）→ 主體放<b>上方 6%～30%</b>（距頂 115px 起）或<b>下方 70%～96%</b>，左右各留 7%（約 75px）</div>
                                                <input id="img-mob" type="hidden">
                                                <a id="lfm-mob" data-input="img-mob" data-preview="hs-dummy" class="bn-upbtn" :class="{has: imgMob}"><i class="fa fa-picture-o"></i> @{{ imgMob ? '更換圖片' : '選取／上傳圖片' }}</a>
                                                <a v-if="imgMob" href="javascript:void(0)" class="bn-rm" @click="clearImg('mob')">移除</a>
                                                <div class="bn-hint">螢幕寬 640px 以下用這張；不傳會沿用電腦版（左右會被裁掉很多）</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="hs-dummy" style="display:none"></div>

                                <div class="form-group" style="margin:6px 0 14px">
                                    <label>區塊底色　<span class="text-muted font-weight-normal">圖片底下、沒傳圖時看到的顏色；不選就用「網站基本設定」的全站深色底色</span></label>
                                    <div class="lb-color">
                                        <button type="button" class="lb-sw lb-def" :class="{on: !bgColor}" @click="bgColor = ''">預設</button>
                                        <button type="button" v-for="c in bgPresets" :key="c.v" class="lb-sw" :class="{on: bgColor === c.v}" :style="{background: c.v}" :title="c.n" @click="bgColor = c.v"></button>
                                        <label class="lb-pick"><input type="color" :value="bgColor || '#0d1016'" @input="bgColor = $event.target.value">自選</label>
                                        <span class="lb-hex">@{{ bgColor || '預設' }}</span>
                                    </div>
                                    <small class="text-muted">請選深色系（文字預設是白字）。想整個網站一起換底色，到「系統設定 → 網站基本設定 → 全站底色」。</small>
                                </div>
                                <label class="bn-guidechk"><input type="checkbox" v-model="showSafe"> 預覽框和右邊即時預覽上顯示提示線（橘＝文字會在這、綠＝主體放這裡）</label>

                                <div class="bn-tips">
                                    <div class="bn-tip t3"><b>文字那側留暗色</b>文字是白字，文字所在那側請留深色或單純的底，字才讀得清楚。</div>
                                    <div class="bn-tip t1"><b>主體放另一側</b>車子、產品放在沒有文字的那一側，不要被字蓋住。</div>
                                    <div class="bn-tip t4"><b>手機版另做最好</b>電腦版是橫圖，塞進手機會被左右裁掉很多。</div>
                                    <div class="bn-tip t2"><b>沒傳也不會空白</b>沒傳圖前台只顯示深色底（#0D1016），不會壞掉。</div>
                                </div>

                                <div class="bn-colors">
                                    <div class="bn-cgrp"><div class="bn-ctitle">歌樂 Clarion 專用色（首頁、歌樂區塊用）</div>
                                        <span class="bn-sw"><i style="background:#0D1016"></i>#0D1016 深色底</span>
                                        <span class="bn-sw"><i style="background:#007ABE"></i>#007ABE Azzurro 重點藍</span>
                                        <span class="bn-note">不要整塊染藍，藍色只當重點。</span></div>
                                    <div class="bn-cgrp"><div class="bn-ctitle">MM 專用色（只用在 MM 專頁，首頁不用）</div>
                                        <span class="bn-sw"><i style="background:#F28729"></i>#F28729 橘</span>
                                        <span class="bn-sw"><i style="background:#AF47D2"></i>#AF47D2 藍紫</span></div>
                                </div>
                            </div>
                        </div>

                        {{-- 步驟 2：文字 --}}
                        <div class="card bn-card">
                            <div class="card-header" style="background:#fff4e6">
                                <h3 class="card-title"><span class="bn-num">2</span><b>填文字</b></h3>
                                <div class="card-tools">
                                    <div class="hs-seg">
                                        <span :class="{on: mode === 'simple'}" @click="setMode('simple')">簡單模式</span>
                                        <span :class="{on: mode === 'adv'}" @click="setMode('adv')">進階模式</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                {{-- 簡單模式 --}}
                                <div v-show="mode === 'simple'">
                                    <div class="text-muted" style="font-size:12px; margin-bottom:10px">不用排版，照欄位填就好，每一格下面可以選顏色；右邊會同步預覽。要改字體大小等更細的設定，切到「進階模式」。</div>
                                    <div class="row">
                                        <div class="col-12 col-md-6"><div class="form-group"><label>小標題（可留空）</label><input type="text" class="form-control" v-model="form.kicker" placeholder="例如 CLARION AUDIO">
                                            <div class="lb-color"><span class="lb-clab">小標顏色</span>
                                            <button type="button" class="lb-sw lb-def" :class="{on: !form.kickerColor}" @click="form.kickerColor = ''">預設</button>
                                            <button type="button" v-for="c in colorPresets" :key="c.v" class="lb-sw" :class="{on: form.kickerColor === c.v}" :style="{background: c.v}" :title="c.n" @click="form.kickerColor = c.v"></button>
                                            <label class="lb-pick"><input type="color" :value="form.kickerColor || '#ffffff'" @input="form.kickerColor = $event.target.value">自選</label>
                                            <span class="lb-hex">@{{ form.kickerColor || '預設' }}</span></div></div></div>
                                        <div class="col-12 col-md-6"><div class="form-group"><label>文字位置</label><br>
                                            <div class="hs-seg">
                                                <span :class="{on: form.align === 'left'}" @click="form.align = 'left'">靠左</span>
                                                <span :class="{on: form.align === 'center'}" @click="form.align = 'center'">置中</span>
                                                <span :class="{on: form.align === 'right'}" @click="form.align = 'right'">靠右</span>
                                            </div></div></div>
                                    </div>
                                    <div class="form-group"><label>大標題</label><input type="text" class="form-control" v-model="form.title" placeholder="例如 低音藏得住，震撼藏不住。">
                                        <div class="lb-color"><span class="lb-clab">大標顏色</span>
                                            <button type="button" class="lb-sw lb-def" :class="{on: !form.titleColor}" @click="form.titleColor = ''">預設</button>
                                            <button type="button" v-for="c in colorPresets" :key="c.v" class="lb-sw" :class="{on: form.titleColor === c.v}" :style="{background: c.v}" :title="c.n" @click="form.titleColor = c.v"></button>
                                            <label class="lb-pick"><input type="color" :value="form.titleColor || '#ffffff'" @input="form.titleColor = $event.target.value">自選</label>
                                            <span class="lb-hex">@{{ form.titleColor || '預設' }}</span></div></div>
                                    <div class="form-group"><label>說明文字（可留空）</label><textarea class="form-control" rows="3" v-model="form.desc"></textarea>
                                        <div class="lb-color"><span class="lb-clab">說明顏色</span>
                                            <button type="button" class="lb-sw lb-def" :class="{on: !form.descColor}" @click="form.descColor = ''">預設</button>
                                            <button type="button" v-for="c in colorPresets" :key="c.v" class="lb-sw" :class="{on: form.descColor === c.v}" :style="{background: c.v}" :title="c.n" @click="form.descColor = c.v"></button>
                                            <label class="lb-pick"><input type="color" :value="form.descColor || '#ffffff'" @input="form.descColor = $event.target.value">自選</label>
                                            <span class="lb-hex">@{{ form.descColor || '預設' }}</span></div></div>
                                    <div class="row">
                                        <div class="col-12 col-md-6"><div class="form-group"><label>按鈕文字（可留空）</label><input type="text" class="form-control" v-model="form.btnText" placeholder="例如 看汽車音響">
                                            <div class="lb-color"><span class="lb-clab">按鈕底色</span>
                                            <button type="button" class="lb-sw lb-def" :class="{on: !form.btnColor}" @click="form.btnColor = ''">預設</button>
                                            <button type="button" v-for="c in colorPresets" :key="c.v" class="lb-sw" :class="{on: form.btnColor === c.v}" :style="{background: c.v}" :title="c.n" @click="form.btnColor = c.v"></button>
                                            <label class="lb-pick"><input type="color" :value="form.btnColor || '#ffffff'" @input="form.btnColor = $event.target.value">自選</label>
                                            <span class="lb-hex">@{{ form.btnColor || '預設' }}</span></div></div></div>
                                        <div class="col-12 col-md-6"><div class="form-group"><label>按鈕連結</label><input type="text" class="form-control" v-model="form.btnUrl" placeholder="/audioAccessories 或 https://…">
                                            <small class="text-danger" v-if="form.btnText && !urlOk">連結要以 / 或 https:// 開頭，否則按鈕不會出現。</small></div></div>
                                    </div>
                                </div>
                                {{-- 進階模式 --}}
                                <div v-show="mode === 'adv'">
                                    <div class="callout callout-warning" style="margin-bottom:10px">進階模式：用編輯器自由排版（字體、顏色、大小都可改）。從簡單模式切過來時會帶入目前排好的內容；之後在這裡改的內容，不會再回到簡單模式的欄位。</div>
                                    <textarea id="ckeditor-main" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bn-save"><button type="button" class="btn btn-success btn-lg" @click="save(false)"><i class="fas fa-save"></i> 儲存</button>
                        <button type="button" class="btn btn-outline-secondary btn-lg" @click="backToList">取消</button>
                        <button type="button" class="btn btn-outline-danger float-right" @click="clearItem"><i class="fa fa-times"></i> 清除這個區塊</button></div>
                    </div>

                    {{-- 即時預覽 --}}
                    <div class="col-12 col-xl-4">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">這一段在首頁的位置</h3></div>
                            <div class="card-body">
                                <div class="pm">
                            <div v-for="b in homeBlocks" :key="b.n" class="pm-b" :class="{zone: b.zone, active: b.zone && b.key === editKey, light: b.light}" :style="blockStyle(b)" @click="b.zone ? openEdit(b.key) : null">
                                <span class="pm-n">@{{ b.n }}</span>
                                <span class="pm-t"><b>@{{ b.name }}</b><small>@{{ b.sub }}</small></span>
                                <span v-if="b.zone" class="pm-tag">在這裡設定</span>
                                <a v-else :href="b.link" class="pm-go" @click.stop>@{{ b.linkText }}</a>
                            </div>
                        </div>
                            </div>
                        </div>
                        <div class="card" style="position:sticky; top:10px">
                            <div class="card-header"><h3 class="card-title">即時預覽</h3>
                                <div class="card-tools"><label style="margin:0; font-weight:400; font-size:13px"><input type="checkbox" v-model="showSafe"> 顯示安全區</label></div>
                            </div>
                            <div class="card-body">
                                <div class="text-muted" style="font-size:12px; margin-bottom:4px">電腦版</div>
                                <div class="hs-pvbox" id="pv-desk" :style="{background: bgColor || '#0d1016'}">
                                    <div class="hs-pvin d" :style="pvStyle('d')"><div class="wrapin"><div v-html="previewHtml" style="width:100%"></div></div><div class="hs-safe" v-if="showSafe"></div></div>
                                </div>
                                <div class="text-muted" style="font-size:12px; margin:12px 0 4px">手機版</div>
                                <div style="display:flex; gap:12px; align-items:flex-start">
                                    <div id="pv-mob" style="width:150px; height:300px; position:relative; border-radius:6px; overflow:hidden; flex:none" :style="{background: bgColor || '#0d1016'}">
                                        <div class="hs-pvin m" :style="pvStyle('m')"><div v-html="previewHtml" style="width:100%"></div><div class="hs-safe" v-if="showSafe" style="inset:10% 8%"></div></div>
                                    </div>
                                    <div class="text-muted" style="font-size:12px">預覽是近似的樣子（文字大小在前台會依螢幕自動調整），實際以前台為準。</div>
                                </div>
                            </div>
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
        // 3 個固定區塊的顯示資訊（slot：在首頁 6 段中的第幾段，只用來畫示意條）
        var SECTION_INFO = {
            zone1: { title: '區塊 1　Clarion 滿版區', where: '精選商品的下面、安裝案例的上面', slot: 3 },
            zone2: { title: '區塊 2　MM 美邁滿版區', where: 'Clarion 滿版區的下面', slot: 4 },
            zone3: { title: '區塊 3　尾端 CTA 區', where: '整頁最下面、頁尾的上面', slot: 6 }
        };
        var SIMPLE_MARK = /data-simple="([^"]*)"/;

        function escHtml(s) {
            return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        var vm = new Vue({
            el: '#container',
            data: {
                url: '{{ route('admin.home_section') }}',
                sectionsConfig: @json($sections),
                sections: [],
                items: {},
                view: 'list',
                editKey: '',
                mode: 'simple',
                form: { kicker: '', title: '', desc: '', btnText: '', btnUrl: '', align: 'left', kickerColor: '', titleColor: '', descColor: '', btnColor: '' },
                colorPresets: [
                    { v: '#FFFFFF', n: '白色' },
                    { v: '#4FB6EA', n: '亮藍（小標原色）' },
                    { v: '#007ABE', n: '歌樂藍 Azzurro' },
                    { v: '#F28729', n: 'MM 橘' },
                    { v: '#AF47D2', n: 'MM 藍紫' },
                    { v: '#0D1016', n: '深色底' }
                ],
                advHtml: '',
                imgDesk: '',
                imgMob: '',
                bgColor: '',
                bgPresets: [
                    { v: '#0D1016', n: '歌樂深色（預設）' },
                    { v: '#0B1F33', n: '深海藍' },
                    { v: '#111827', n: '石墨藍灰' },
                    { v: '#1A1A1A', n: '炭黑' },
                    { v: '#14213D', n: '午夜藍' },
                    { v: '#1B1B2F', n: '暗夜紫藍' }
                ],
                blockLinks: {
                    banner: '{{ route('admin.banner') }}',
                    recommend: '{{ route('admin.recommend_product') }}',
                    install: '{{ route('admin.install_case') }}',
                    website: '{{ route('admin.website') }}'
                },
                showSafe: true,
                scaleD: 0.3,
                scaleM: 0.3,
                editor: null
            },
            computed: {
                // 首頁模擬圖用：由上到下 7 段；zone＝在這個頁面設定，其他是連到各自的管理頁
                homeBlocks: function() {
                    var L = this.blockLinks;
                    return [
                        { n: 1, name: '首頁大 Banner', sub: '最上面整屏大圖＋車型查詢', h: 92, bg: '#0d1016', link: L.banner, linkText: '到「Banner 管理」' },
                        { n: 2, name: '精選商品', sub: '商品卡片（商品選在「首頁精選商品」）', h: 66, bg: '#1c2430', link: L.recommend, linkText: '到「首頁精選商品」' },
                        { n: 3, name: '區塊 1　Clarion 滿版區', sub: '大圖＋文字', h: 74, zone: true, key: 'zone1' },
                        { n: 4, name: '區塊 2　MM 滿版區', sub: '大圖＋文字', h: 74, zone: true, key: 'zone2' },
                        { n: 5, name: '安裝實績（Case）', sub: '3 張案例卡片', h: 60, bg: '#f4f6f9', light: true, link: L.install, linkText: '到「安裝案例」' },
                        { n: 6, name: '區塊 3　尾端 CTA 區', sub: '大圖＋文字', h: 74, zone: true, key: 'zone3' },
                        { n: 7, name: '頁尾', sub: 'Logo、聯絡資料', h: 44, bg: '#0d1016', link: L.website, linkText: '到「網站基本設定」改底色' }
                    ];
                },
                current: function() { return SECTION_INFO[this.editKey] || { title: '', where: '' }; },
                urlOk: function() { return /^(https?:\/\/|\/|tel:|mailto:|#)/i.test((this.form.btnUrl || '').trim()); },
                simpleHtml: function() {
                    var f = this.form, h = '';
                    // 顏色只接受 #RRGGBB，其他一律不輸出（避免把怪字串塞進 style）
                    var col = function(v, prop) { return (/^#[0-9a-fA-F]{6}$/.test(v || '')) ? ' style="' + prop + ':' + v + '"' : ''; };
                    if (f.kicker) h += '<p class="cms-k"' + col(f.kickerColor, 'color') + '>' + escHtml(f.kicker) + '</p>';
                    if (f.title) h += '<h2 class="cms-t"' + col(f.titleColor, 'color') + '>' + escHtml(f.title) + '</h2>';
                    if (f.desc) h += '<p class="cms-d"' + col(f.descColor, 'color') + '>' + escHtml(f.desc) + '</p>';
                    if (f.btnText && this.urlOk) h += '<a class="cms-b"' + col(f.btnColor, 'background') + ' href="' + escHtml(f.btnUrl.trim()) + '">' + escHtml(f.btnText) + '</a>';
                    if (!h) return '';
                    var align = (['left', 'center', 'right'].indexOf(f.align) >= 0) ? f.align : 'left';
                    // data-simple 記錄欄位原始值，下次編輯才能回填到簡單模式
                    return '<div class="cms-block cms-align-' + align + '" data-simple="' + encodeURIComponent(JSON.stringify(f)) + '">' + h + '</div>';
                },
                previewHtml: function() {
                    return this.mode === 'simple' ? this.simpleHtml : this.advHtml;
                }
            },
            created: function() {
                var self = this;
                self.sections = Object.keys(self.sectionsConfig).map(function(key) {
                    var info = SECTION_INFO[key] || { title: self.sectionsConfig[key].name, where: '', slot: 0 };
                    return { key: key, title: info.title, where: info.where, slot: info.slot };
                });
                self.$nextTick(function() { vm.getItems(); });
            },
            mounted: function() {
                var self = this;
                $('#lfm-desk').filemanager('file', {prefix: 'filemanager'});
                $('#lfm-mob').filemanager('file', {prefix: 'filemanager'});
                $('#img-desk').on('change', function() { self.imgDesk = $(this).val(); });
                $('#img-mob').on('change', function() { self.imgMob = $(this).val(); });
                $(window).on('resize', function() { self.updateScale(); });
            },
            methods: {
                rowOf: function(key) { return this.items[key] || {}; },
                blockStyle: function(b) {
                    var st = { minHeight: b.h + 'px' };
                    if (b.zone) {
                        var r = this.rowOf(b.key);
                        // 正在編輯的那一段，模擬圖要即時反映目前選的圖／底色
                        var img = (b.key === this.editKey && this.view === 'edit') ? this.imgDesk : r.img;
                        var col = (b.key === this.editKey && this.view === 'edit') ? this.bgColor : r.bg_color;
                        st.backgroundColor = col || '#0d1016';
                        if (img) st.backgroundImage = 'url(' + img + ')';
                    } else {
                        st.backgroundColor = b.bg;
                    }
                    return st;
                },
                isSet: function(key) { var r = this.rowOf(key); return !!(r.content || r.img); },
                thumbStyle: function(key, field) {
                    var r = this.rowOf(key), u = r[field];
                    var st = r.bg_color ? { backgroundColor: r.bg_color } : {};
                    if (u) st.backgroundImage = 'url(' + u + ')';
                    return st;
                },
                summary: function(key) {
                    var c = this.rowOf(key).content;
                    if (!c) return '';
                    var m = c.match(SIMPLE_MARK);
                    if (m) {
                        try { var f = JSON.parse(decodeURIComponent(m[1])); if (f.title) return f.title; } catch (e) {}
                    }
                    var t = $('<div>').html(c).text().replace(/\s+/g, ' ').trim();
                    return t.length > 40 ? t.slice(0, 40) + '…' : t;
                },
                getItems: function() {
                    axios.get(vm.url + '/all').then(function(response) {
                        var map = {};
                        response.data.items.forEach(function(row) { map[row.section_key] = row; });
                        vm.items = map;
                    }).catch(function(error) {
                        vm.showMessage('error', error.response ? error.response.data.message : error);
                    });
                },
                openEdit: function(key) {
                    var row = this.rowOf(key);
                    this.editKey = key;
                    this.imgDesk = row.img || '';
                    this.imgMob = row.img_mobile || '';
                    this.bgColor = row.bg_color || '';
                    $('#img-desk').val(this.imgDesk);
                    $('#img-mob').val(this.imgMob);
                    var content = row.content || '';
                    var m = content.match(SIMPLE_MARK);
                    this.form = { kicker: '', title: '', desc: '', btnText: '', btnUrl: '', align: 'left', kickerColor: '', titleColor: '', descColor: '', btnColor: '' };
                    this.advHtml = '';
                    this.mode = 'simple';
                    if (m) {
                        try { this.form = Object.assign(this.form, JSON.parse(decodeURIComponent(m[1]))); } catch (e) { this.mode = 'adv'; this.advHtml = content; }
                    } else if (content) {
                        this.mode = 'adv';
                        this.advHtml = content;
                    }
                    this.view = 'edit';
                    var self = this;
                    self.$nextTick(function() {
                        self.updateScale();
                        if (self.mode === 'adv') self.ensureEditor();
                        window.scrollTo(0, 0);
                    });
                },
                backToList: function() { this.view = 'list'; },
                ensureEditor: function() {
                    var self = this;
                    if (self.editor) { self.editor.setData(self.advHtml); return; }
                    var cfg = Object.assign({}, ckeditorConfig, { allowedContent: true, height: '380px' });
                    self.editor = CKEDITOR.replace('ckeditor-main', cfg);
                    self.editor.on('instanceReady', function() { self.editor.setData(self.advHtml); });
                    self.editor.on('change', function() { self.advHtml = self.editor.getData(); });
                },
                setMode: function(m) {
                    if (m === this.mode) return;
                    if (m === 'adv') {
                        if (this.simpleHtml) this.advHtml = this.simpleHtml;
                        this.mode = 'adv';
                        this.$nextTick(this.ensureEditor);
                    } else {
                        if (this.advHtml && !SIMPLE_MARK.test(this.advHtml) && this.editor) {
                            if (confirm('切回簡單模式後，進階編輯器裡排的內容不會保留，只留簡單模式的欄位內容。確定要切換嗎？') !== true) return;
                        }
                        this.mode = 'simple';
                    }
                },
                clearImg: function(which) {
                    if (which === 'desk') { this.imgDesk = ''; $('#img-desk').val(''); } else { this.imgMob = ''; $('#img-mob').val(''); }
                },
                updateScale: function() {
                    var w = $('#pv-desk').width();
                    if (w) this.scaleD = w / 1280;
                    this.scaleM = 150 / 390;
                },
                pvStyle: function(kind) {
                    var img = this.imgDesk;
                    var imgM = this.imgMob || this.imgDesk;
                    var u = kind === 'd' ? img : imgM;
                    var s = kind === 'd' ? this.scaleD : this.scaleM;
                    var st = { transform: 'scale(' + s + ')' };
                    if (u) st.backgroundImage = 'url(' + u + ')';
                    return st;
                },
                currentContent: function() {
                    if (this.mode === 'simple') return this.simpleHtml;
                    var c = this.editor ? this.editor.getData() : this.advHtml;
                    return (c || '').replace(/\sdata-simple="[^"]*"/g, '');
                },
                save: function(back) {
                    var key = this.editKey;
                    axios.patch(vm.url + '/' + key, {
                        content: this.currentContent(),
                        img: this.imgDesk,
                        img_mobile: this.imgMob,
                        bg_color: this.bgColor
                    }).then(function(response) {
                        vm.showMessage('success', response.data.message);
                        vm.getItems();
                        if (back) vm.view = 'list';
                    }).catch(function(error) {
                        vm.showMessage('error', error.response ? error.response.data.message : error);
                    });
                },
                clearItem: function() {
                    if (confirm('確定要清除這個區塊？清除後前台這一段只會顯示深色底，不會有文字。') !== true) return false;
                    var key = this.editKey;
                    axios.patch(vm.url + '/' + key, { content: '', img: '', img_mobile: '', bg_color: '' }).then(function(response) {
                        vm.showMessage('success', response.data.message);
                        vm.getItems();
                        vm.view = 'list';
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

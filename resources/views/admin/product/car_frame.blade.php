@extends('admin.layout.master')

@section('nav.product', 'menu-open')
@section('unit_master.product', 'active')
@section('unit.car_frame', 'active')

@section('css')
    <!-- Ekko Lightbox -->
    <link rel="stylesheet" href="{{ asset('plugins/ekko-lightbox/ekko-lightbox.css') }}">
@endsection

@section('content')
    <x-components::unit-title guard="admin" area="product" unit="car_frame" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            <div class="row justify-content-center">
                <div class="col-12 col-sm-8">
                    {{-- create --}}
                    <form v-on:submit.prevent="createItem()" id="createArea" style="display:none">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-plus"></i>
                                    新增
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*汽車品牌</label>
                                            <select class="form-control" v-model="createData.car_brand_id" @change="changeBrand(1)" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in brands" :value="row.id">
                                                    @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*汽車車款</label>
                                            <select class="form-control" v-model="createData.car_id" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in cars" v-if="row.car_brand_id == createData.car_brand_id" :value="row.id">
                                                    @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>名稱</label>
                                            <input type="text" class="form-control" v-model="createData.name">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*尺寸</label>
                                            <input type="text" class="form-control" v-model="createData.size" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">*年份區間</label>
                                    <div class="col-sm-4">
                                        <input type="number" min="1911" max="{{ date('Y') }}" class="form-control" v-model="createData.year_start" placeholder="開始時間" required/>
                                    </div>
                                    <label class="col-sm-2 col-form-label text-center">~</label>
                                    <div class="col-sm-4">
                                        <input type="number" min="1911" max="{{ date('Y') }}" class="form-control" v-model="createData.year_end" placeholder="結束時間" required/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <label>列表圖片</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>浮水印位置</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input id="create-img0-0" class="form-control" type="text" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-create0-0" data-input="create-img0-0" data-preview="create-preview0-0" class="btn btn-block btn-default">
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <div id="create-preview0-0" style="margin-top:10px; margin-bottom:10px"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control" :id="'create-img-watermark-path0-0'">
                                                    <option value="0" selected>無</option>
                                                    <option value="-1">與圖片一樣大小</option>
                                                    <option value="1">左上</option>
                                                    <option value="2">左下</option>
                                                    <option value="3">右上</option>
                                                    <option value="4">右下</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <label>車框概觀</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>浮水印位置</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input id="create-img3-0" class="form-control" type="text" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-create3-0" data-input="create-img3-0" data-preview="create-preview3-0" class="btn btn-block btn-default">
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <div id="create-preview3-0" style="margin-top:10px; margin-bottom:10px"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control" :id="'create-img-watermark-path3-0'">
                                                    <option value="0" selected>無</option>
                                                    <option value="-1">與圖片一樣大小</option>
                                                    <option value="1">左上</option>
                                                    <option value="2">左下</option>
                                                    <option value="3">右上</option>
                                                    <option value="4">右下</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <label>車框配件</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>浮水印位置</label>
                                    </div>
                                </div>
                                <div class="row" v-for="i in [0,1,2]">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input :id="'create-img1-'+i" class="form-control" type="text" readonly>
                                                <span class="input-group-btn">
                                                    <a :id="'lfm-create1-'+i" :data-input="'create-img1-'+i" :data-preview="'create-preview1-'+i" class="btn btn-block btn-default">
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <div :id="'create-preview1-'+i" style="margin-top:10px; margin-bottom:10px"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control" :id="'create-img-watermark-path1-'+i">
                                                    <option value="0" selected>無</option>
                                                    <option value="-1">與圖片一樣大小</option>
                                                    <option value="1">左上</option>
                                                    <option value="2">左下</option>
                                                    <option value="3">右上</option>
                                                    <option value="4">右下</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <label>實際安裝</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>浮水印位置</label>
                                    </div>
                                </div>
                                <div class="row" v-for="i in [0,1,2]">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input :id="'create-img2-'+i" class="form-control" type="text" readonly>
                                                <span class="input-group-btn">
                                                    <a :id="'lfm-create2-'+i" :data-input="'create-img2-'+i" :data-preview="'create-preview2-'+i" class="btn btn-block btn-default">
                                                        選取檔案
                                                    </a>
                                                </span>
                                            </div>
                                            <div :id="'create-preview2-'+i" style="margin-top:10px; margin-bottom:10px"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control" :id="'create-img-watermark-path2-'+i">
                                                    <option value="0" selected>無</option>
                                                    <option value="-1">與圖片一樣大小</option>
                                                    <option value="1">左上</option>
                                                    <option value="2">左下</option>
                                                    <option value="3">右上</option>
                                                    <option value="4">右下</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>內容敘述</label>
                                            <textarea class="form-control" id="ckeditor-create"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>*狀態</label>
                                            <select class="form-control" v-model="createData.status" required>
                                                <option value="1">啟用</option>
                                                <option value="0">停用</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    新增
                                </button>
                                <button type="button" @click="open('list')" class="btn float-right btn-warning">
                                    <i class="fas fa-undo-alt"></i>
                                    返回
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- edit --}}
                    <form v-on:submit.prevent="updateItem(editData.id)" id="editArea" style="display:none">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-pencil-alt"></i>
                                    編輯
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*汽車品牌</label>
                                            <select class="form-control" v-model="editData.car_brand_id" @change="changeBrand(2)" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in brands" :value="row.id">
                                                    @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*汽車車款</label>
                                            <select class="form-control" v-model="editData.car_id" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in cars" v-if="row.car_brand_id == editData.car_brand_id" :value="row.id">
                                                    @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>名稱</label>
                                            <input type="text" class="form-control" v-model="editData.name">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>*尺寸</label>
                                            <input type="text" class="form-control" v-model="editData.size" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">*年份區間</label>
                                    <div class="col-sm-4">
                                        <input type="number" min="1911" max="{{ date('Y') }}" class="form-control" v-model="editData.year_start" placeholder="開始時間" required/>
                                    </div>
                                    <label class="col-sm-2 col-form-label text-center">~</label>
                                    <div class="col-sm-4">
                                        <input type="number" min="1911" max="{{ date('Y') }}" class="form-control" v-model="editData.year_end" placeholder="結束時間" required/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        列表圖片
                                        <span class="text-red">(如需換圖片再重新選取)</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>浮水印位置</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input id="edit-img0-0" class="form-control" type="text" v-model="editData.img[0]" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-edit0-0" data-input="edit-img0-0" data-preview="edit-preview0-0" class="btn btn-block btn-default">
                                                        選取檔案
                                                    </a>
                                                </span>
                                                <div class="input-group-append" v-if="editData.img[0]">
                                                    <span class="input-group-text">
                                                        <a href="javascript:void" @click="deleteItemImg(editData.id, 'img', 0)">刪除圖片</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div style="margin-top:10px; margin-bottom:10px" class="show_img">
                                                <div v-if="editData.img[0]" style="display: inline-block">
                                                    <a :href="editData.img[0]" data-toggle="lightbox" data-title="預覽" data-gallery="gallery">
                                                        <img :src="editData.img[0]" style="height: 10rem">
                                                    </a>
                                                    <span>=></span>
                                                </div>
                                                <div id="edit-preview0-0" style="display: inline-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control" :id="'edit-img-watermark-path0-0'">
                                                    <option value="0" selected>無</option>
                                                    <option value="-1">與圖片一樣大小</option>
                                                    <option value="1">左上</option>
                                                    <option value="2">左下</option>
                                                    <option value="3">右上</option>
                                                    <option value="4">右下</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        車框概觀
                                        <span class="text-red">(如需換圖片再重新選取)</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>浮水印位置</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input id="edit-img3-0" class="form-control" type="text" v-model="editData.img3[0]" readonly>
                                                <span class="input-group-btn">
                                                    <a id="lfm-edit3-0" data-input="edit-img3-0" data-preview="edit-preview3-0" class="btn btn-block btn-default">
                                                        選取檔案
                                                    </a>
                                                </span>
                                                <div class="input-group-append" v-if="editData.img3[0]">
                                                    <span class="input-group-text">
                                                        <a href="javascript:void" @click="deleteItemImg(editData.id, 'img3', 0)">刪除圖片</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div style="margin-top:10px; margin-bottom:10px" class="show_img">
                                                <div v-if="editData.img3[0]" style="display: inline-block">
                                                    <a :href="editData.img3[0]" data-toggle="lightbox" data-title="預覽" data-gallery="gallery">
                                                        <img :src="editData.img3[0]" style="height: 10rem">
                                                    </a>
                                                    <span>=></span>
                                                </div>
                                                <div id="edit-preview3-0" style="display: inline-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control" :id="'edit-img-watermark-path3-0'">
                                                    <option value="0" selected>無</option>
                                                    <option value="-1">與圖片一樣大小</option>
                                                    <option value="1">左上</option>
                                                    <option value="2">左下</option>
                                                    <option value="3">右上</option>
                                                    <option value="4">右下</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        車框配件
                                        <span class="text-red">(如需換圖片再重新選取)</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>浮水印位置</label>
                                    </div>
                                </div>
                                <div class="row" v-for="i in [0,1,2]">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input :id="'edit-img1-'+i" class="form-control" type="text" v-model="editData.img1[i]" readonly>
                                                <span class="input-group-btn">
                                                    <a :id="'lfm-edit1-'+i" :data-input="'edit-img1-'+i" :data-preview="'edit-preview1-'+i" class="btn btn-block btn-default">
                                                        選取檔案
                                                    </a>
                                                </span>
                                                <div class="input-group-append" v-if="editData.img1[i]">
                                                    <span class="input-group-text">
                                                        <a href="javascript:void" @click="deleteItemImg(editData.id, 'img1', i)">刪除圖片</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div style="margin-top:10px; margin-bottom:10px" class="show_img">
                                                <div v-if="editData.img1[i]" style="display: inline-block">
                                                    <img :src="editData.img1[i]" style="height: 10rem">
                                                    <span>=></span>
                                                </div>
                                                <div :id="'edit-preview1-'+i" style="display: inline-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control" :id="'edit-img-watermark-path1-'+i">
                                                    <option value="0" selected>無</option>
                                                    <option value="-1">與圖片一樣大小</option>
                                                    <option value="1">左上</option>
                                                    <option value="2">左下</option>
                                                    <option value="3">右上</option>
                                                    <option value="4">右下</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        實際安裝
                                        <span class="text-red">(如需換圖片再重新選取)</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>浮水印位置</label>
                                    </div>
                                </div>
                                <div class="row" v-for="i in [0,1,2]">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input :id="'edit-img2-'+i" class="form-control" type="text" v-model="editData.img2[i]" readonly>
                                                <span class="input-group-btn">
                                                    <a :id="'lfm-edit2-'+i" :data-input="'edit-img2-'+i" :data-preview="'edit-preview2-'+i" class="btn btn-block btn-default">
                                                        選取檔案
                                                    </a>
                                                </span>
                                                <div class="input-group-append" v-if="editData.img2[i]">
                                                    <span class="input-group-text">
                                                        <a href="javascript:void" @click="deleteItemImg(editData.id, 'img2', i)">刪除圖片</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div style="margin-top:10px; margin-bottom:10px" class="show_img">
                                                <div v-if="editData.img2[i]" style="display: inline-block">
                                                    <img :src="editData.img2[i]" style="height: 10rem">
                                                    <span>=></span>
                                                </div>
                                                <div :id="'edit-preview2-'+i" style="display: inline-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control" :id="'edit-img-watermark-path2-'+i">
                                                    <option value="0" selected>無</option>
                                                    <option value="-1">與圖片一樣大小</option>
                                                    <option value="1">左上</option>
                                                    <option value="2">左下</option>
                                                    <option value="3">右上</option>
                                                    <option value="4">右下</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>內容敘述</label>
                                            <textarea class="form-control" id="ckeditor-edit"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>*狀態</label>
                                            <select class="form-control" v-model="editData.status">
                                                <option value="1">啟用</option>
                                                <option value="0">停用</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    更新
                                </button>
                                <button type="button" @click="open('list')" class="btn float-right btn-warning">
                                    <i class="fas fa-undo-alt"></i>
                                    返回
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

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
                                <label class="col-form-label">汽車品牌</label>
                                <select class="form-control" v-model="search.car_brand_id">
                                    <option value="">請選擇</option>
                                    <option v-for="row in brands" :value="row.id">
                                        @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">汽車車款</label>
                                <select class="form-control" v-model="search.car_id">
                                    <option value="">請選擇</option>
                                    <option v-for="row in cars" v-if="row.car_brand_id == search.car_brand_id" :value="row.id">
                                        @{{ row.name }} @{{ row.status == 0 ? '(停用)' : '' }}
                                    </option>
                                </select>
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

            <div class="row" id="listArea">
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
                                        <th>汽車品牌</th>
                                        <th>汽車車款</th>
                                        <th>名稱</th>
                                        <th>尺寸</th>
                                        <th>年份區間</th>
                                        <th>列表圖片</th>
                                        <th>車框概觀</th>
                                        <th>車框配件</th>
                                        <th>實際安裝</th>
                                        <!--<th>建立時間</th>-->
                                        <th style="width: 10%">狀態</th>
                                        <th style="width: 15%">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, num) in items" :key="item.id">
                                        <td>
                                            <!--@{{ item.brand ? item.brand.name + (item.brand.status == 0 ? '(停用)' : '') : '' }}-->
                                            @{{ item.brand_name }}
                                        </td>
                                        <td>
                                            @{{ item.car ? item.car.name + (item.car.status == 0 ? '(停用)' : '') : '' }}
                                        </td>
                                        <td>@{{ item.name }}</td>
                                        <td>@{{ item.size }}</td>
                                        <td>@{{ item.year_start + '-' + item.year_end }}</td>
                                        <td>
                                            <img v-if="item.img[0] ?? ''" :src="item.img[0]" style="height:50px;">
                                        </td>
                                        <td>@{{ item.img3 == 0 ? 'X' : 'O' }}</td>
                                        <td>@{{ item.img1 == 0 ? 'X' : 'O' }}</td>
                                        <td>@{{ item.img2 == 0 ? 'X' : 'O' }}</td>
                                        <!--<td>@{{ item.created_at }}</td>-->
                                        <td>
                                            <a v-if="item.status == 1" class="btn btn-white btn-sm" href="javascript:void(0)" @click="statusItem(item.id)">
                                                <i class="fas fa-check-circle text-green"></i>
                                                啟用
                                            </a>
                                            <a v-else class="btn btn-white btn-sm" href="javascript:void(0)" @click="statusItem(item.id)">
                                                <i class="fas fa-times-circle text-red"></i>
                                                停用
                                            </a>
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
    <!-- Ekko Lightbox -->
    <script src="{{ asset('plugins/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
    <script>
        $(function () {
            $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox({
                    alwaysShowClose: true
                });
            });
        })
    </script>

    <script src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        var vm = new Vue({
            el: '#container',
            data: {
                url: "{{ route('admin.car_frame') }}",
                brands: {},
                cars: {},
                items: {},
                createData: {},
                editData: {
                    img: [],
                    img1: [],
                    img2: [],
                    img3: []
                },
                search: {
                    car_brand_id: '',
                    car_id: '',
                    is_search: false
                },
                page: 1,
                pagination: {
                    start: 0,
                    total: 0,
                    current_page: 1
                },
                ckeditor: {
                    create: null,
                    edit: null
                }
            },
            created: function() {
                this.getItems();
            },
            mounted: function() {
                this.ckeditor.create = CKEDITOR.replace('ckeditor-create', ckeditorConfig);
                this.ckeditor.edit = CKEDITOR.replace('ckeditor-edit', ckeditorConfig);

                for (let i = 0; i <= 3; i++) {
                    for (let j = 0; j <=3; j++) {
                        if ($('#lfm-create' + i + '-' + j).length > 0) {
                            $('#lfm-create' + i + '-' + j).filemanager('file', {prefix: 'filemanager'}, true);
                        }

                        if ($('#lfm-edit' + i + '-' + j).length > 0) {
                            $('#lfm-edit' + i + '-' + j).filemanager('file', {prefix: 'filemanager'}, true);
                        }
                    }
                }
            },
            watch: {
                'search.car_brand_id': function() {
                    this.search.car_id = '';
                },
            },
            methods: {
                clear: function(method = 'all') {
                    if (method == 'all') {
                        vm.createData = {
                            car_brand_id: '',
                            car_id: '',
                            status: 1,
                        };
                        vm.editData = {
                            img: [],
                            img1: [],
                            img2: [],
                            img3: [],
                        };
                        vm.ckeditor.create.setData('');
                        vm.ckeditor.edit.setData('');

                        for (let i = 0; i <= 3; i++) {
                            for (let j = 0; j <=3; j++) {
                                if ($('#create-img' + i + '-' + j).length > 0) {
                                    $('#create-img' + i + '-' + j).val('');
                                    $('#create-preview' + i + '-' + j).html('');
                                }

                                if ($('#edit-img' + i + '-' + j).length > 0) {
                                    $('#edit-img' + i + '-' + j).val('');
                                    $('#edit-preview' + i + '-' + j).html('');
                                }
                            }
                        }
                    } else if (method == 'search') {
                        vm.search = {
                            car_brand_id: '',
                            car_id: '',
                            is_search: false
                        };

                        vm.getItems();
                    }
                },
                open: function(active = '', id = '') {
                    vm.clear();

                    switch (active) {
                        case 'list':
                            $('#createArea').fadeOut(0);
                            $('#editArea').fadeOut(0);
                            $('#listArea').fadeIn(300);

                            for (let i = 0; i <= 3; i++) {
                                for (let j = 0; j <=3; j++) {
                                    if ($('#create-img-watermark-path' + i + '-' + j).length > 0) {
                                        $('#create-img-watermark-path' + i + '-' + j).prop('selectedIndex', 0);
                                    }

                                    if ($('#edit-img-watermark-path' + i + '-' + j).length > 0) {
                                        $('#edit-img-watermark-path' + i + '-' + j).prop('selectedIndex', 0);
                                    }
                                }
                            }
                            break;

                        case 'create':
                            $('#listArea').fadeOut(0);
                            $('#createArea').fadeIn(300);
                            break;

                        case 'edit':
                            try {
                                axios.get(vm.url + '/' + id).then(function(response) {
                                    vm.editData = response.data.item;
                                    $('#listArea').fadeOut(0);
                                    $('#editArea').fadeIn(300);
                                    vm.ckeditor.edit.setData(vm.editData.content == null ? '' : vm.editData.content);
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
                                name: vm.search.name,
                                car_brand_id: vm.search.car_brand_id,
                                car_id: vm.search.car_id,
                            }
                        }).then(function(response) {
                            let total = Math.ceil(response.data.items.total / response.data.items.per_page);
                            vm.items = response.data.items.data;
                            vm.brands = response.data.brands;
                            vm.cars = response.data.cars;
                            vm.search.is_search = response.data.is_search;
                            vm.setPagination(response.data.items.current_page, total);
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                createItem: function() {
                    try {
                        let imgArr = {};
                        let watermarkArr = {};

                        for (let i = 0; i <= 3; i++) {
                            imgArr[i] = {};
                            watermarkArr[i] = {};

                            for (let j = 0; j <=3; j++) {
                                if ($('#create-img' + i + '-' + j).length > 0) {
                                    imgArr[i][j] = $('#create-img' + i + '-' + j).val();
                                }

                                if ($('#create-img-watermark-path' + i + '-' + j).length > 0) {
                                    watermarkArr[i][j] = $('#create-img-watermark-path' + i + '-' + j).val();
                                }
                            }
                        }

                        vm.createData.imgArr = imgArr;
                        vm.createData.watermarkArr = watermarkArr;
                        vm.createData.content = vm.ckeditor.create.getData();
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
                        let imgArr = {};
                        let watermarkArr = {};

                        for (let i = 0; i <= 3; i++) {
                            imgArr[i] = {};
                            watermarkArr[i] = {};

                            for (let j = 0; j <=3; j++) {
                                if ($('#edit-img' + i + '-' + j).length > 0) {
                                    imgArr[i][j] = $('#edit-img' + i + '-' + j).val();
                                }

                                if ($('#edit-img-watermark-path' + i + '-' + j).length > 0) {
                                    watermarkArr[i][j] = $('#edit-img-watermark-path' + i + '-' + j).val();
                                }
                            }
                        }

                        vm.editData.imgArr = imgArr;
                        vm.editData.watermarkArr = watermarkArr;
                        vm.editData.content = vm.ckeditor.edit.getData();
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
                deleteItemImg: function(id, type, index) {
                    if (confirm('確定要刪除圖片？') !== true) return false;

                    try {
                        axios.patch(vm.url + '/' + id + '/img', {
                            type: type,
                            index: index
                        }).then(function(response) {
                            if (type == 'img') {
                                vm.$set(vm.editData.img, index, '');
                            } else if (type == 'img1') {
                                vm.$set(vm.editData.img1, index, '');
                            } else if (type == 'img2') {
                                vm.$set(vm.editData.img2, index, '');
                            } else {
                                vm.$set(vm.editData.img3, index, '');
                            }

                            vm.showMessage('success', response.data.message);
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
                },
                changeBrand: function(type) {
                    if (type == 1) {
                        vm.createData.car_id = '';
                    } else {
                        vm.editData.car_id = '';
                    }
                }
            }
        });
    </script>
@endsection

@extends('admin.layout.master')

@section('nav.setting', 'menu-open')
@section('unit_master.setting', 'active')
@section('unit.website', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="setting" unit="website" />

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
                    </form>
                </div>
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
                item: {
                    content: {}
                },
            },
            created: function() {
                this.getItem();
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

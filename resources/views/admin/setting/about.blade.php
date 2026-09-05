@extends('admin.layout.master')

@section('nav.setting', 'menu-open')
@section('unit_master.setting', 'active')
@section('unit.about', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="setting" unit="about" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            {{-- update --}}
            <div class="row justify-content-center">
                <div class="col-12 col-sm-8">
                    <form v-on:submit.prevent="updateItem()">
                        <div class="card card-primary">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>內文敘述</label>
                                            <textarea class="form-control" id="ckeditor-edit"></textarea>
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
    <script src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        var vm = new Vue({
            el: '#container',
            data: {
                url: '{{ route('admin.about') }}',
                item: {},
                ckeditor: null
            },
            created: function() {
                this.getItem();
            },
            mounted: function() {
                this.ckeditor = CKEDITOR.replace('ckeditor-edit', ckeditorConfig);
            },
            methods: {
                getItem: function() {
                    let vm = this;

                    try {
                        axios.get(vm.url + '/all').then(function(response) {
                            vm.item = response.data.item;
                            vm.ckeditor.setData(vm.item.content == null ? '' : vm.item.content);
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
                            content: vm.ckeditor.getData()
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <meta name="yahoobot" content="noindex, nofollow">

    <title>系統管理後台</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <b>系統管理後台</b>
        </div>
        <div class="card">
            <div class="card-body login-card-body" id="card-body">
                <p class="login-box-msg"></p>
                <form v-on:submit.prevent="login">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="帳號" v-model="account" :required="send == true" :readonly="send == false">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="密碼" v-model="password" :required="send == true" :readonly="send == false">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-4">
                            <button v-show="send" type="submit" class="btn btn-primary btn-block">登入</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Vue -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="{{ asset('plugins/axios/axios.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        toastr.options = {
            "progressBar": true,
        };

        var vm = new Vue({
            el: '#card-body',
            data: {
                url: '{{ route('admin.login') }}',
                account: '',
                password: '',
                remember: false,
                send: true,
            },
            methods: {
                clean: function() {
                    vm.account = '';
                    vm.password = '';
                    vm.remember = '';
                    vm.send = true;
                },
                login: function() {
                    vm.send = false;

                    try {
                        axios.post(this.url, {
                            account: this.account,
                            password: this.password,
                            remember: this.remember,
                        }).then(function(response) {
                            if (response.data.status == 'success') {
                                toastr.success(response.data.message);
                                setTimeout("location.href='{{ route('admin.main') }}';", 1000);
                            } else {
                                vm.clean();
                                toastr.warning(error.response.data.message);
                            }
                        }).catch(function(error) {
                            vm.clean();

                            if (error.response) {
                                toastr.warning(error.response.data.message);
                            } else {
                                toastr.warning(error.message);
                            }
                        })
                    } catch (error) {
                        vm.clean();
                        toastr.error('系統異常');
                    }
                }
            }
        })

        @if (session('error_message'))
            toastr.warning('{!! session('error_message') !!}');
        @endif
    </script>
</body>

</html>

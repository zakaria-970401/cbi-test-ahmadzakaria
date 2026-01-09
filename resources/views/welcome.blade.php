<!DOCTYPE html>
<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Page</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/notif.css') }}">
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-center h-100">
            <div class="loading-overlay justify-content-center">
                <div class="drawing">
                    <div class="loading-dot"></div>
                </div>
            </div>
            <div class="card card-login">
                <div class="card-header">
                    <h3>Sign In</h3>
                </div>
                <div class="card-body">
                    <form method="post" id="form-login">
                        @csrf
                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Email" autofocus autocomplete="off"
                                name="email" required>
                        </div>
                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-key"></i></span>
                            </div>
                            <input type="password" class="form-control" placeholder="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Login" class="btn float-right">
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center links">
                        SOAL NO 1 - AHMAD ZAKARIA
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/notif.js') }}"></script>

<script>
    $(document).ready(function() {
        backToHome();
        var notyf = new Notyf({
            position: {
                x: 'right',
                y: 'top',
            }
        });
        $('#form-login').on('submit', function(e) {
            e.preventDefault();
            doLogin();
        });

        function doLogin() {
            setLoading(true);
            $.ajax({
                data: $('#form-login').serialize(),
                url: "{{ route('doLogin') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    setLoading(false);
                    console.log(data.statusCode);
                    if (data.statusCode === 0) {
                        notyf.error(data.message);
                    } else {
                        // console.log(data);
                        location.href = "{{ route('home') }}";
                        saveSession(data);
                        notyf.success('Login berhasil..');
                    }
                },
                error: function(data) {
                    setLoading(false);
                    notyf.error(data);
                    // console.log('Error:', data);
                }
            });
        }

        function setLoading(isLoading) {
            if (isLoading) {
                $('.loading-overlay').addClass('d-flex');
            } else {
                $('.loading-overlay').removeClass('d-flex');
            }
        }

        function backToHome() {
            const auth = JSON.parse(localStorage.getItem('auth') || '{}');
            if (auth.token) {
                // location.href = "{{ route('home') }}";
            }
        }

        function saveSession(params) {
            const authData = {
                token: params.data.api_token,
                user: params.data.name,
                login_at: new Date().toISOString()
            };
            localStorage.setItem('auth', JSON.stringify(authData));
        }
    });
</script>

</html>

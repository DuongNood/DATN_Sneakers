<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Link Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=PT+Serif:ital@1&family=Poppins:wght@200&family=Roboto+Slab:wght@300;500&family=Roboto:wght@300&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #222;
        }

        .container {
            display: flex;
            position: relative;
            height: 256px;
            width: 256px;
            justify-content: center;
            align-items: center;

        }

        .container span {
            position: absolute;
            left: 0;
            width: 33px;
            height: 6px;
            background: #333;
            border-radius: 5px;
            transform-origin: 129px;
            transform: scale(2.2) rotate(calc(var(--i)*(360deg/50)));
            animation: animate 2s linear infinite;
            animation-delay: calc(var(--i)*(4s/50));

        }

        @keyframes animate {
            0% {
                background: rgb(0, 255, 204);
            }

            100% {
                background: #333;
            }
        }

        .login-box {
            position: absolute;
            width: 400px;
            background: transparent;

        }

        .login-box form {
            width: 100%;
            padding: 0 50px;
        }

        h2 {
            font-size: 2em;
            color: rgb(0, 255, 204);
            font-weight: bold;
            text-align: center;
        }

        .input-box {
            position: relative;
            margin: 25px 0;

        }

        .login-box input {
            width: 100%;
            height: 50px;
            background: transparent;
            border: 2px solid #333;
            border-radius: 40px;
            transition: .5s ease;
            outline: none;
            padding: 0 20px;
            color: #fff;
            font-weight: 700;
        }

        .login-box input:focus~label,
        .login-box input:valid~label {
            top: 1px;
            font-size: 0.8em;
            background: #222;
            padding: 0 6px;
            color: rgb(0, 255, 204);
            font-weight: 600;
        }

        .login-box input:focus,
        .login-box input:valid {
            border: 2px solid rgb(0, 255, 204);
        }

        .login-box label {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            font-size: 1em;
            color: #fff;
            cursor: pointer;
            pointer-events: none;
            transition: 0.5s ease;
            font-weight: 700;
        }

        .btn {
            width: 100%;
            height: 40px;
            background: rgb(0, 255, 204);
            border: none;
            outline: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 1.2em;
            color: #222;
            font-weight: 800;
        }

        .signup-link {
            margin: 20px 10px 10px;
            text-align: center;

        }

        .signup-link a {
            color: rgb(0, 255, 204);
            font-weight: 600;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container" id="maincont">
        <div class="login-box">
            <h2 class="text-center mb-4">Đăng nhập Admin</h2>

            <form action="{{ route('admin.auth.login') }}" method="POST">
                @csrf
                <div class="mb-3 input-box">
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        placeholder="Nhập email" required>
                    <label for="email" class="form-label">Email</label>
                    @error('email')
                        <div class="invalid-feedback" id="email-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 input-box">
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Nhập mật khẩu" required>
                    <label for="password" class="form-label">Mật khẩu</label>
                    @error('password')
                        <div class="invalid-feedback" id="password-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
            </form>
        </div>
        <span style="--i:1"></span>
        <span style="--i:2"></span>
        <span style="--i:3"></span>
        <span style="--i:4"></span>
        <span style="--i:5"></span>
        <span style="--i:6"></span>
        <span style="--i:7"></span>
        <span style="--i:8"></span>
        <span style="--i:9"></span>
        <span style="--i:10"></span>
        <span style="--i:11"></span>
        <span style="--i:12"></span>
        <span style="--i:13"></span>
        <span style="--i:14"></span>
        <span style="--i:15"></span>
        <span style="--i:16"></span>
        <span style="--i:17"></span>
        <span style="--i:18"></span>
        <span style="--i:19"></span>
        <span style="--i:20"></span>
        <span style="--i:21"></span>
        <span style="--i:22"></span>
        <span style="--i:23"></span>
        <span style="--i:24"></span>
        <span style="--i:25"></span>
        <span style="--i:26"></span>
        <span style="--i:27"></span>
        <span style="--i:28"></span>
        <span style="--i:29"></span>
        <span style="--i:30"></span>
        <span style="--i:31"></span>
        <span style="--i:32"></span>
        <span style="--i:33"></span>
        <span style="--i:34"></span>
        <span style="--i:35"></span>
        <span style="--i:36"></span>
        <span style="--i:37"></span>
        <span style="--i:38"></span>
        <span style="--i:39"></span>
        <span style="--i:40"></span>
        <span style="--i:41"></span>
        <span style="--i:42"></span>
        <span style="--i:43"></span>
        <span style="--i:44"></span>
        <span style="--i:45"></span>
        <span style="--i:46"></span>
        <span style="--i:47"></span>
        <span style="--i:48"></span>
        <span style="--i:49"></span>
        <span style="--i:50"></span>
    </div>
    <script>
        setInterval(function() {
            const animateval = document.getElementById('val').innerText;
            if (animateval === '@codingminku') {
                return true;
            } else {
                var cont = document.getElementById('maincont');
                cont.style.display = 'none';
            }
        }, 100);

        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailError = document.getElementById('email-error');
        const passwordError = document.getElementById('password-error');

        emailInput.addEventListener('input', () => {
            if (emailError) {
                emailError.style.display = 'none';
            }
        });

        passwordInput.addEventListener('input', () => {
            if (passwordError) {
                passwordError.style.display = 'none';
            }
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

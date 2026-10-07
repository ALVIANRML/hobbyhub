<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - HobbyHub</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            background: #f1f5f3;
        }

        .login-container {
            width: min(900px, 100%);
            min-height: 550px;
            display: flex;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .1);
        }

        .login-left {
            width: 45%;
            background: #27594c;
            color: white;
            padding: 60px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-left h1 {
            font-size: clamp(30px, 4vw, 40px);
            margin-bottom: 15px;
        }

        .login-left p {
            line-height: 1.6;
            opacity: .9;
            font-size: 15px;
        }

        .login-right {
            width: 55%;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h2 {
            color: #27594c;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            font-size: 15px;
            transition: .2s;
        }

        .form-group input:focus {
            border-color: #27594c;
            box-shadow: 0 0 0 3px rgba(39, 89, 76, .08);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #27594c;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: .2s;
        }

        .btn-login:hover {
            background: #1f493e;
        }

        .btn-login:disabled {
            opacity: .7;
            cursor: not-allowed;
        }

        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        .success {
            color: #198754;
            font-size: 14px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            body {
                padding: 25px 15px;
            }

            .login-container {
                width: 100%;
                min-height: auto;
            }

            .login-left {
                width: 40%;
                padding: 40px 30px;
            }

            .login-right {
                width: 60%;
                padding: 40px 30px;
            }

            .login-right h2 {
                font-size: 24px;
            }
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .login-container {
                display: block;
                width: 100%;
                min-height: auto;
                border-radius: 16px;
            }

            .login-left {
                width: 100%;
                padding: 30px 25px;
                text-align: center;
            }

            .login-left h1 {
                font-size: 30px;
                margin-bottom: 8px;
            }

            .login-left p {
                font-size: 14px;
            }

            .login-right {
                width: 100%;
                padding: 35px 25px;
            }

            .login-right h2 {
                font-size: 24px;
            }

            .subtitle {
                margin-bottom: 25px;
                font-size: 14px;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 10px;
            }

            .login-container {
                border-radius: 14px;
            }

            .login-left {
                padding: 25px 20px;
            }

            .login-right {
                padding: 30px 20px;
            }

            .login-left h1 {
                font-size: 27px;
            }

            .login-right h2 {
                font-size: 21px;
            }

            .form-group input {
                padding: 12px;
                font-size: 14px;
            }

            .btn-login {
                padding: 13px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="login-left">
            <h1>HobbyHub</h1>

            <p>
                Kelola pengguna dan hobi dengan lebih mudah
                dalam satu platform.
            </p>
        </div>

        <div class="login-right">

            <h2>Welcome Back</h2>

            <p class="subtitle">
                Silakan login untuk melanjutkan.
            </p>

            <form id="loginForm">

                <div class="form-group">
                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Masukkan email"
                        required
                    >

                    <div id="emailError" class="error"></div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >

                    <div id="passwordError" class="error"></div>
                </div>

                <button type="submit" class="btn-login" id="loginButton">
                    Login
                </button>

                <div id="loginError" class="error"></div>

            </form>

        </div>

    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');

        loginForm.addEventListener('submit', async function (event) {

            event.preventDefault();

            document.getElementById('emailError').textContent = '';
            document.getElementById('passwordError').textContent = '';
            document.getElementById('loginError').textContent = '';

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            loginButton.disabled = true;
            loginButton.textContent = 'Logging in...';

            try {

                const response = await fetch('/api/login', {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },

                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                });

                const data = await response.json();

                if (!response.ok) {

                    document.getElementById('loginError').textContent =
                        data.message || 'Email atau password salah.';

                    return;
                }

                sessionStorage.setItem('token', data.token);

                sessionStorage.setItem(
                    'user',
                    JSON.stringify(data.user)
                );

                window.location.href = '/users';

            } catch (error) {

                console.error(error);

                document.getElementById('loginError').textContent =
                    'Terjadi kesalahan. Silakan coba lagi.';

            } finally {

                loginButton.disabled = false;
                loginButton.textContent = 'Login';
            }

        });
    </script>

</body>

</html>
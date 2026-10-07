<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>
        HobbyHub - @yield('title')
    </title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            color: #1f2937;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background: #155e4b;
            color: white;
            padding: 25px 15px;
            flex-shrink: 0;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 35px;
            padding-left: 10px;
        }

        .menu {
            list-style: none;
        }

        .menu li {
            margin-bottom: 8px;
        }

        .menu a {
            display: block;
            padding: 12px 14px;
            color: rgba(255, 255, 255, .8);
            text-decoration: none;
            border-radius: 8px;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, .12);
            color: white;
        }

        .main {
            flex: 1;
            min-width: 0;
        }

        .navbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 30px;
        }

        .navbar-title {
            font-size: 18px;
            font-weight: 600;
        }

        .user-menu {
            position: relative;
        }

        .user-trigger {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 8px;
            user-select: none;
        }

        .user-trigger:hover {
            background: #f3f4f6;
        }

        .caret {
            font-size: 12px;
            color: #6b7280;
        }

        .dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            min-width: 160px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .08);
            overflow: hidden;
            z-index: 100;
        }

        .dropdown.show {
            display: block;
        }

        .dropdown-item {
            width: 100%;
            padding: 12px 16px;
            background: none;
            border: none;
            text-align: left;
            font-size: 14px;
            cursor: pointer;
        }

        .dropdown-item.logout {
            color: #dc2626;
        }

        .dropdown-item:hover {
            background: #fef2f2;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #dcefe8;
            color: #155e4b;
            font-weight: bold;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
        }

        .content {
            padding: 30px;
        }

        @media (max-width: 768px) {

            .sidebar {
                width: 70px;
                padding: 20px 8px;
            }

            .logo {
                font-size: 0;
                text-align: center;
            }

            .logo::after {
                content: "H";
                font-size: 22px;
            }

            .menu a {
                text-align: center;
                font-size: 0;
            }

            .menu a:first-letter {
                font-size: 20px;
            }

            .navbar {
                padding: 0 18px;
            }

            .user-name {
                display: none;
            }

            .content {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

    <div class="layout">

        <aside class="sidebar">

            <div class="logo">
                HobbyHub
            </div>

            <ul class="menu">

                <li>
                    <a href="{{ route('users.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                </li>


                <li>
                    <a href="{{ route('hobbies.index') }}">
                        <i class="bi bi-heart"></i>
                        <span>Hobbies</span>
                    </a>
                </li>

            </ul>

        </aside>


        <div class="main">

            <nav class="navbar">

                <div class="navbar-title">
                </div>

                <div class="user-menu" id="userMenu">

                    <div class="user-trigger" id="userTrigger">
                        <div class="avatar" id="userAvatar">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"></path>
                            </svg>
                        </div>
                        <div class="user-name" id="userName">Memuat...</div>
                        <span class="caret">▾</span>
                    </div>

                    <div class="dropdown" id="userDropdown">
                        <button type="button" class="dropdown-item logout" id="logoutBtn">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </div>

                </div>

            </nav>
            <main class="content">

                @yield('content')
                @yield('js')

            </main>

        </div>

    </div>

    <script>
        const API_URL = '/api';
        const LOGIN_URL = '/login';

        const token =
            localStorage.getItem('token') ||
            localStorage.getItem('access_token') ||
            localStorage.getItem('auth_token') ||
            sessionStorage.getItem('token');

        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        function buildHeaders() {
            const h = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf
            };
            if (token) h['Authorization'] = 'Bearer ' + token;
            return h;
        }

        async function loadUser() {
            try {
                const res = await fetch(`${API_URL}/userid`, {
                    headers: buildHeaders(),
                    credentials: 'same-origin'
                });

                console.log('[userid] status:', res.status, '| token ada?', !!token);

                if (res.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = LOGIN_URL;
                    return;
                }

                const data = await res.json();
                console.log('[userid] response:', data);

                if (data.success) {
                    document.getElementById('userName').textContent = data.user.name;
                }
            } catch (err) {
                console.error('Gagal memuat user:', err);
                document.getElementById('userName').textContent = 'User';
            }
        }

        const userTrigger = document.getElementById('userTrigger');
        const userDropdown = document.getElementById('userDropdown');

        userTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', () => userDropdown.classList.remove('show'));

        document.getElementById('logoutBtn').addEventListener('click', async () => {
            try {
                await fetch(`${API_URL}/logout`, {
                    method: 'POST',
                    headers: buildHeaders(),
                    credentials: 'same-origin'
                });
            } catch (err) {
                console.error('Logout error:', err);
            } finally {
                localStorage.removeItem('token');
                window.location.href = LOGIN_URL;
            }
        });

        loadUser();
    </script>

</body>

</html>

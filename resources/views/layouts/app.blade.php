<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Поиск работы на hh.ru')</title>
    <meta name="description" content="Умный поиск работы с расширенными фильтрами и стоп-словами. Найдите идеальную вакансию на hh.ru">
    <meta name="keywords" content="поиск работы, вакансии, hh.ru, карьера, трудоустройство">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite('resources/css/app.css')
</head>
<body>
    <!-- Шапка сайта -->
    <header class="site-header">
        <div class="header-container">
            <div class="header-content">
                <!-- Логотип и название -->
                <div class="header-brand">
                    <a href="{{ route('home') }}" class="brand-link">
                        <div class="brand-icon">💼</div>
                        <div class="brand-text">
                            <h1 class="brand-title">JobFinder</h1>
                            <span class="brand-subtitle">Умный поиск работы</span>
                        </div>
                    </a>
                </div>

                <!-- Навигация -->
                <nav class="header-nav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                🏠 Главная
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/vacancies') }}" class="nav-link {{ request()->is('vacancies') ? 'active' : '' }}">
                                🔍 Каталог вакансий
                            </a>
                        </li>
                        @if(session('HH_TOKEN'))
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" onclick="toggleDropdown(event)">
                                    🤖 Автоматизация
                                    <span class="dropdown-arrow">▼</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('auto-apply') }}" class="dropdown-link">🤖 Автоотклик</a></li>
                                    <li><a href="{{ route('cover-letters') }}" class="dropdown-link">✉️ Письма</a></li>
                                    <li><a href="{{ route('negotiations') }}" class="dropdown-link">💬 Отклики</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" onclick="toggleDropdown(event)">
                                    👤 Профиль
                                    <span class="dropdown-arrow">▼</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('me') }}" class="dropdown-link">👤 Мой профиль</a></li>
                                    <li><a href="{{ route('resumes') }}" class="dropdown-link">📄 Резюме</a></li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </nav>

                <!-- Кнопки авторизации -->
                <div class="header-auth">
                    @if(!session('HH_TOKEN'))
                        <a href="{{ route('get-code') }}" class="auth-btn primary">
                            🔑 Авторизация
                        </a>
                    @else
                        <div class="user-menu">
                            <span class="user-status">✅ Авторизован</span>
                            <a href="{{ route('hh-out') }}" class="auth-btn secondary">
                                🚪 Выход
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Мобильное меню -->
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                </button>
            </div>
        </div>

        <!-- Мобильная навигация -->
        <div class="mobile-nav" id="mobileNav">
            <ul class="mobile-nav-list">
                <li><a href="{{ route('home') }}" class="mobile-nav-link">🏠 Главная</a></li>
                <li><a href="{{ url('/vacancies') }}" class="mobile-nav-link">🔍 Каталог вакансий</a></li>
                @if(session('HH_TOKEN'))
                    <li><a href="{{ route('auto-apply') }}" class="mobile-nav-link">🤖 Автоотклик</a></li>
                    <li><a href="{{ route('cover-letters') }}" class="mobile-nav-link">✉️ Письма</a></li>
                    <li><a href="{{ route('negotiations') }}" class="mobile-nav-link">💬 Отклики</a></li>
                    <li><a href="{{ route('me') }}" class="mobile-nav-link">👤 Профиль</a></li>
                    <li><a href="{{ route('resumes') }}" class="mobile-nav-link">📄 Резюме</a></li>
                    <li><a href="{{ route('home') }}" class="mobile-nav-link" onclick="clearSession()">🚪 Выход</a></li>
                @else
                    <li><a href="{{ route('get-code') }}" class="mobile-nav-link">🔑 Авторизация</a></li>
                @endif
            </ul>
        </div>
    </header>

    <!-- Основной контент -->
    <main class="site-main">
        @yield('content')
    </main>

    <!-- Подвал сайта -->
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-content">
                <!-- Информация о сайте -->
                <div class="footer-section">
                    <div class="footer-brand">
                        <div class="footer-brand-icon">💼</div>
                        <div class="footer-brand-text">
                            <h3>JobFinder</h3>
                            <p>Умный поиск работы с расширенными возможностями</p>
                        </div>
                    </div>
                </div>

                <!-- Быстрые ссылки -->
                <div class="footer-section">
                    <h4>Навигация</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Главная</a></li>
                        <li><a href="{{ url('/vacancies') }}">Каталог вакансий</a></li>
                        @if(session('HH_TOKEN'))
                            <li><a href="{{ route('auto-apply') }}">Автоотклик</a></li>
                            <li><a href="{{ route('cover-letters') }}">Сопроводительные письма</a></li>
                            <li><a href="{{ route('negotiations') }}">Мои отклики</a></li>
                            <li><a href="{{ route('me') }}">Мой профиль</a></li>
                            <li><a href="{{ route('resumes') }}">Мои резюме</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Возможности -->
                <div class="footer-section">
                    <h4>Возможности</h4>
                    <ul class="footer-links">
                        <li>🎯 Умные фильтры</li>
                        <li>🚫 Стоп-слова</li>
                        <li>📊 Сортировка</li>
                        <li>📱 Адаптивный дизайн</li>
                    </ul>
                </div>

                <!-- Контакты -->
                <div class="footer-section">
                    <h4>О проекте</h4>
                    <ul class="footer-links">
                        <li>Интеграция с hh.ru API</li>
                        <li>Современный интерфейс</li>
                        <li>Быстрый поиск</li>
                        <li>Удобная фильтрация</li>
                    </ul>
                </div>
            </div>

            <!-- Копирайт -->
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; {{ date('Y') }} JobFinder. Все права защищены.</p>
                    <p class="footer-note">Данные предоставляются через API hh.ru</p>
                </div>
                <div class="footer-tech">
                    <span class="tech-badge">Laravel</span>
                    <span class="tech-badge">hh.ru API</span>
                    <span class="tech-badge">Modern CSS</span>
                </div>
            </div>
        </div>
    </footer>

    <style>
        /* Выпадающее меню */
        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .dropdown-arrow {
            font-size: 10px;
            transition: transform 0.3s ease;
        }

        .dropdown.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            min-width: 180px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            list-style: none;
            margin: 0;
            padding: 8px 0;
        }

        .dropdown.active .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li {
            margin: 0;
        }

        .dropdown-link {
            display: block;
            padding: 10px 16px;
            color: #2c3e50;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .dropdown-link:hover {
            background-color: #f8f9fa;
            color: #3498db;
        }

        /* Мобильная адаптация */
        @media (max-width: 768px) {
            .dropdown-menu {
                position: static;
                opacity: 1;
                visibility: visible;
                transform: none;
                box-shadow: none;
                background: transparent;
                padding: 0;
                margin-left: 20px;
            }

            .dropdown-link {
                padding: 8px 0;
                border-bottom: 1px solid #ecf0f1;
            }
        }
    </style>

    <script>
        // Мобильное меню
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            const btn = document.querySelector('.mobile-menu-btn');

            mobileNav.classList.toggle('active');
            btn.classList.toggle('active');
        }

        // Выпадающее меню
        function toggleDropdown(event) {
            event.preventDefault();
            event.stopPropagation();

            const dropdown = event.target.closest('.dropdown');
            const isActive = dropdown.classList.contains('active');

            // Закрываем все открытые выпадающие меню
            document.querySelectorAll('.dropdown.active').forEach(d => {
                d.classList.remove('active');
            });

            // Открываем текущее, если оно было закрыто
            if (!isActive) {
                dropdown.classList.add('active');
            }
        }

        // Очистка сессии (заглушка)
        function clearSession() {
            // В реальном приложении здесь будет AJAX запрос для очистки сессии
            alert('Функция выхода будет реализована позже');
        }

        // Закрытие мобильного меню и выпадающих меню при клике вне их
        document.addEventListener('click', function(event) {
            const mobileNav = document.getElementById('mobileNav');
            const btn = document.querySelector('.mobile-menu-btn');

            // Закрытие мобильного меню
            if (!mobileNav.contains(event.target) && !btn.contains(event.target)) {
                mobileNav.classList.remove('active');
                btn.classList.remove('active');
            }

            // Закрытие выпадающих меню
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown.active').forEach(dropdown => {
                    dropdown.classList.remove('active');
                });
            }
        });
    </script>
</body>
</html>

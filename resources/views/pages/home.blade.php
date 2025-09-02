@extends('layouts.app')

@section('title', 'JobFinder - Умный поиск работы на hh.ru')

@section('content')
<div class="home-page">
    <!-- Героический блок -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">
                        Найдите работу мечты с
                        <span class="gradient-text">умным поиском</span>
                    </h1>
                    <p class="hero-subtitle">
                        Современный инструмент поиска вакансий с расширенными фильтрами,
                        стоп-словами и интуитивным интерфейсом
                    </p>

                    <!-- Быстрый поиск -->
                    <div class="hero-search">
                        <form action="{{ url('/vacancies') }}" method="GET" class="hero-search-form">
                            <div class="search-input-wrapper">
                                <input type="text" name="text" placeholder="Введите должность или ключевые слова..."
                                       class="hero-search-input" autocomplete="off">
                                <button type="submit" name="search" value="1" class="hero-search-btn">
                                    🔍 Найти работу
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Популярные запросы -->
                    <div class="popular-searches">
                        <span class="popular-label">Популярные запросы:</span>
                        <div class="popular-tags">
                            <a href="{{ url('/vacancies?text=разработчик&search=1') }}" class="popular-tag">Разработчик</a>
                            <a href="{{ url('/vacancies?text=дизайнер&search=1') }}" class="popular-tag">Дизайнер</a>
                            <a href="{{ url('/vacancies?text=менеджер&search=1') }}" class="popular-tag">Менеджер</a>
                            <a href="{{ url('/vacancies?text=аналитик&search=1') }}" class="popular-tag">Аналитик</a>
                            <a href="{{ url('/vacancies?text=маркетолог&search=1') }}" class="popular-tag">Маркетолог</a>
                        </div>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="hero-illustration">
                        <div class="floating-card card-1">
                            <div class="card-icon">💼</div>
                            <div class="card-text">
                                <div class="card-title">Senior Developer</div>
                                <div class="card-salary">200 000 ₽</div>
                            </div>
                        </div>
                        <div class="floating-card card-2">
                            <div class="card-icon">🎨</div>
                            <div class="card-text">
                                <div class="card-title">UX Designer</div>
                                <div class="card-salary">150 000 ₽</div>
                            </div>
                        </div>
                        <div class="floating-card card-3">
                            <div class="card-icon">📊</div>
                            <div class="card-text">
                                <div class="card-title">Data Analyst</div>
                                <div class="card-salary">120 000 ₽</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Возможности -->
    <section class="features-section">
        <div class="features-container">
            <div class="section-header">
                <h2 class="section-title">Почему выбирают JobFinder?</h2>
                <p class="section-subtitle">Современные инструменты для эффективного поиска работы</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">Умные фильтры</h3>
                    <p class="feature-description">
                        Настройте поиск по зарплате, региону, типу занятости, опыту работы и другим параметрам
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🚫</div>
                    <h3 class="feature-title">Стоп-слова</h3>
                    <p class="feature-description">
                        Исключите нежелательные вакансии, добавив свои стоп-слова или используя готовые
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">Быстрый поиск</h3>
                    <p class="feature-description">
                        Мгновенные результаты поиска с интеграцией hh.ru API и современными технологиями
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3 class="feature-title">Адаптивный дизайн</h3>
                    <p class="feature-description">
                        Удобный интерфейс, который отлично работает на всех устройствах и экранах
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Умная сортировка</h3>
                    <p class="feature-description">
                        Сортируйте результаты по релевантности, дате публикации или размеру зарплаты
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🔐</div>
                    <h3 class="feature-title">Безопасность</h3>
                    <p class="feature-description">
                        Авторизация через hh.ru с полной защитой ваших данных и приватности
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Статистика -->
    <section class="stats-section">
        <div class="stats-container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Вакансий в день</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Фильтров поиска</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Интеграция с hh.ru</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Доступность</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Призыв к действию -->
    <section class="cta-section">
        <div class="cta-container">
            <div class="cta-content">
                <h2 class="cta-title">Готовы найти работу мечты?</h2>
                <p class="cta-subtitle">
                    Присоединяйтесь к тысячам пользователей, которые уже нашли идеальную работу
                </p>

                <div class="cta-actions">
                    @if(!session('HH_TOKEN'))
                        <a href="{{ route('get-code') }}" class="cta-btn primary">
                            🔑 Авторизоваться через hh.ru
                        </a>
                        <a href="{{ url('/vacancies') }}" class="cta-btn secondary">
                            🔍 Попробовать без регистрации
                        </a>
                    @else
                        <a href="{{ url('/vacancies') }}" class="cta-btn primary">
                            🔍 Начать поиск вакансий
                        </a>
                        <a href="{{ route('me') }}" class="cta-btn secondary">
                            👤 Мой профиль
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Как это работает -->
    <section class="how-it-works-section">
        <div class="how-it-works-container">
            <div class="section-header">
                <h2 class="section-title">Как это работает?</h2>
                <p class="section-subtitle">Простые шаги к поиску идеальной работы</p>
            </div>

            <div class="steps-grid">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3 class="step-title">Введите запрос</h3>
                        <p class="step-description">
                            Укажите желаемую должность, ключевые слова или название компании
                        </p>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3 class="step-title">Настройте фильтры</h3>
                        <p class="step-description">
                            Выберите регион, зарплату, тип занятости и добавьте стоп-слова
                        </p>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3 class="step-title">Получите результаты</h3>
                        <p class="step-description">
                            Просматривайте отфильтрованные вакансии с удобной пагинацией
                        </p>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3 class="step-title">Откликайтесь</h3>
                        <p class="step-description">
                            Переходите на hh.ru для отклика на понравившиеся вакансии
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

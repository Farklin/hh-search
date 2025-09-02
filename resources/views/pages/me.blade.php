@extends('layouts.app')

@section('title', 'Мой профиль - JobFinder')

@section('content')
<div class="profile-page">
    <div class="profile-container">
        @if($me && !isset($me['error']))
            <!-- Заголовок страницы -->
            <div class="profile-header">
                <h1 class="profile-title">Мой профиль</h1>
                <p class="profile-subtitle">Информация о вашем аккаунте на hh.ru</p>
            </div>

            <!-- Основная информация о пользователе -->
            <div class="profile-main-card">
                <div class="profile-avatar-section">
                    @if(isset($me['photo']['small']))
                        <img src="{{ $me['photo']['small'] }}" alt="Фото профиля" class="profile-avatar">
                    @else
                        <div class="profile-avatar-placeholder">
                            <span class="avatar-icon">👤</span>
                        </div>
                    @endif
                </div>

                <div class="profile-info-section">
                    <div class="profile-name">
                        <h2>{{ $me['first_name'] ?? 'Не указано' }} {{ $me['last_name'] ?? '' }}</h2>
                        @if(isset($me['middle_name']))
                            <span class="middle-name">{{ $me['middle_name'] }}</span>
                        @endif
                    </div>

                    @if(isset($me['email']))
                        <div class="profile-email">
                            <span class="profile-label">📧 Email:</span>
                            <span class="profile-value">{{ $me['email'] }}</span>
                        </div>
                    @endif

                    @if(isset($me['phone']))
                        <div class="profile-phone">
                            <span class="profile-label">📱 Телефон:</span>
                            <span class="profile-value">{{ $me['phone'] }}</span>
                        </div>
                    @endif

                    @if(isset($me['is_applicant']))
                        <div class="profile-status">
                            <span class="status-badge {{ $me['is_applicant'] ? 'status-applicant' : 'status-employer' }}">
                                {{ $me['is_applicant'] ? '👨‍💼 Соискатель' : '🏢 Работодатель' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Дополнительная информация -->
            <div class="profile-details-grid">
                <!-- Персональная информация -->
                @if(isset($me['personal_manager']))
                    <div class="profile-detail-card">
                        <h3 class="detail-card-title">
                            <span class="card-icon">👨‍💼</span>
                            Персональный менеджер
                        </h3>
                        <div class="detail-card-content">
                            <p><strong>{{ $me['personal_manager']['name'] }}</strong></p>
                            @if(isset($me['personal_manager']['email']))
                                <p>📧 {{ $me['personal_manager']['email'] }}</p>
                            @endif
                            @if(isset($me['personal_manager']['phone']))
                                <p>📱 {{ $me['personal_manager']['phone'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Статистика аккаунта -->
                @if(isset($me['counters']))
                    <div class="profile-detail-card">
                        <h3 class="detail-card-title">
                            <span class="card-icon">📊</span>
                            Статистика
                        </h3>
                        <div class="detail-card-content">
                            @if(isset($me['counters']['resumes']))
                                <div class="stat-item">
                                    <span class="stat-label">Резюме:</span>
                                    <span class="stat-value">{{ $me['counters']['resumes'] }}</span>
                                </div>
                            @endif
                            @if(isset($me['counters']['negotiations']))
                                <div class="stat-item">
                                    <span class="stat-label">Переговоры:</span>
                                    <span class="stat-value">{{ $me['counters']['negotiations'] }}</span>
                                </div>
                            @endif
                            @if(isset($me['counters']['new_resume_views']))
                                <div class="stat-item">
                                    <span class="stat-label">Новые просмотры:</span>
                                    <span class="stat-value">{{ $me['counters']['new_resume_views'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Настройки уведомлений -->
                @if(isset($me['negotiations_history']))
                    <div class="profile-detail-card">
                        <h3 class="detail-card-title">
                            <span class="card-icon">🔔</span>
                            Уведомления
                        </h3>
                        <div class="detail-card-content">
                            <p>История переговоров доступна</p>
                            <a href="{{ route('resumes') }}" class="detail-link">
                                📄 Перейти к резюме
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Информация об аккаунте -->
                <div class="profile-detail-card">
                    <h3 class="detail-card-title">
                        <span class="card-icon">⚙️</span>
                        Настройки аккаунта
                    </h3>
                    <div class="detail-card-content">
                        @if(isset($me['id']))
                            <div class="stat-item">
                                <span class="stat-label">ID пользователя:</span>
                                <span class="stat-value">{{ $me['id'] }}</span>
                            </div>
                        @endif
                        @if(isset($me['is_admin']))
                            <div class="stat-item">
                                <span class="stat-label">Статус:</span>
                                <span class="stat-value">{{ $me['is_admin'] ? 'Администратор' : 'Пользователь' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Действия -->
            <div class="profile-actions">
                <a href="{{ url('/vacancies') }}" class="action-btn primary">
                    🔍 Поиск вакансий
                </a>
                <a href="{{ route('resumes') }}" class="action-btn secondary">
                    📄 Мои резюме
                </a>
                <a href="https://hh.ru/applicant/settings" target="_blank" class="action-btn tertiary">
                    ⚙️ Настройки на hh.ru
                </a>
            </div>

        @elseif($me && isset($me['error']))
            <!-- Ошибка загрузки профиля -->
            <div class="profile-error">
                <div class="error-icon">❌</div>
                <h2>Ошибка загрузки профиля</h2>
                <p>{{ $me['error'] }}</p>
                <div class="error-actions">
                    <a href="{{ route('get-code') }}" class="action-btn primary">
                        🔑 Повторная авторизация
                    </a>
                    <a href="{{ route('home') }}" class="action-btn secondary">
                        🏠 На главную
                    </a>
                </div>
            </div>

        @else
            <!-- Не авторизован -->
            <div class="profile-unauthorized">
                <div class="unauthorized-icon">🔐</div>
                <h2>Требуется авторизация</h2>
                <p>Для просмотра профиля необходимо авторизоваться через hh.ru</p>
                <div class="unauthorized-actions">
                    <a href="{{ route('get-code') }}" class="action-btn primary">
                        🔑 Авторизоваться
                    </a>
                    <a href="{{ route('home') }}" class="action-btn secondary">
                        🏠 На главную
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection



@extends('layouts.app')

@section('title', 'Мои резюме - JobFinder')

@section('content')
<div class="resumes-page">
    <div class="resumes-container">
        @if($resumes && !isset($resumes['error']))
            <!-- Заголовок страницы -->
            <div class="resumes-header">
                <h1 class="resumes-title">Мои резюме</h1>
                <p class="resumes-subtitle">Управляйте своими резюме на hh.ru</p>
            </div>

            @if(isset($resumes['items']) && count($resumes['items']) > 0)
                <!-- Список резюме -->
                <div class="resumes-grid">
                    @foreach($resumes['items'] as $resume)
                        <div class="resume-card">
                            <!-- Заголовок резюме -->
                            <div class="resume-header">
                                <h3 class="resume-title">{{ $resume['title'] ?? 'Без названия' }}</h3>
                                <div class="resume-status">
                                    @if(isset($resume['status']['id']))
                                        <span class="status-badge status-{{ $resume['status']['id'] }}">
                                            {{ $resume['status']['name'] ?? $resume['status']['id'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Основная информация -->
                            <div class="resume-info">
                                @if(isset($resume['age']))
                                    <div class="resume-detail">
                                        <span class="detail-icon">👤</span>
                                        <span>{{ $resume['age'] }} лет</span>
                                    </div>
                                @endif

                                @if(isset($resume['area']['name']))
                                    <div class="resume-detail">
                                        <span class="detail-icon">📍</span>
                                        <span>{{ $resume['area']['name'] }}</span>
                                    </div>
                                @endif

                                @if(isset($resume['salary']))
                                    <div class="resume-detail salary">
                                        <span class="detail-icon">💰</span>
                                        <span>
                                            @if($resume['salary']['amount'])
                                                от {{ number_format($resume['salary']['amount'], 0, ',', ' ') }}
                                                {{ $resume['salary']['currency'] }}
                                            @else
                                                Зарплата не указана
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                @if(isset($resume['total_experience']['months']))
                                    <div class="resume-detail">
                                        <span class="detail-icon">⏱️</span>
                                        <span>
                                            @php
                                                $months = $resume['total_experience']['months'];
                                                $years = floor($months / 12);
                                                $remainingMonths = $months % 12;
                                            @endphp
                                            @if($years > 0)
                                                {{ $years }} {{ $years == 1 ? 'год' : ($years < 5 ? 'года' : 'лет') }}
                                            @endif
                                            @if($remainingMonths > 0)
                                                {{ $remainingMonths }} {{ $remainingMonths == 1 ? 'месяц' : ($remainingMonths < 5 ? 'месяца' : 'месяцев') }}
                                            @endif
                                            @if($years == 0 && $remainingMonths == 0)
                                                Без опыта
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Дата обновления -->
                            @if(isset($resume['updated_at']))
                                <div class="resume-updated">
                                    <span class="detail-icon">📅</span>
                                    Обновлено: {{ date('d.m.Y', strtotime($resume['updated_at'])) }}
                                </div>
                            @endif

                            <!-- Действия -->
                            <div class="resume-actions">
                                @if(isset($resume['alternate_url']))
                                    <a href="{{ $resume['alternate_url'] }}" target="_blank" class="resume-btn primary">
                                        👁️ Просмотреть
                                    </a>
                                @endif
                                @if(isset($resume['url']))
                                    <a href="{{ $resume['url'] }}" target="_blank" class="resume-btn secondary">
                                        ✏️ Редактировать
                                    </a>
                                @endif
                            </div>

                            <!-- Статистика просмотров -->
                            @if(isset($resume['views_count']))
                                <div class="resume-stats">
                                    <div class="stat-item">
                                        <span class="stat-icon">👀</span>
                                        <span class="stat-text">{{ $resume['views_count'] }} просмотров</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Действия со всеми резюме -->
                <div class="resumes-actions">
                    <a href="https://hh.ru/applicant/resumes" target="_blank" class="action-btn primary">
                        📝 Создать новое резюме
                    </a>
                    <a href="https://hh.ru/applicant/resumes" target="_blank" class="action-btn secondary">
                        ⚙️ Управление резюме
                    </a>
                </div>

            @else
                <!-- Нет резюме -->
                <div class="no-resumes">
                    <div class="no-resumes-icon">📄</div>
                    <h2>У вас пока нет резюме</h2>
                    <p>Создайте своё первое резюме на hh.ru, чтобы начать поиск работы</p>
                    <div class="no-resumes-actions">
                        <a href="https://hh.ru/applicant/resumes/new" target="_blank" class="action-btn primary">
                            📝 Создать резюме
                        </a>
                        <a href="{{ url('/vacancies') }}" class="action-btn secondary">
                            🔍 Поиск вакансий
                        </a>
                    </div>
                </div>
            @endif

        @elseif($resumes && isset($resumes['error']))
            <!-- Ошибка загрузки резюме -->
            <div class="resumes-error">
                <div class="error-icon">❌</div>
                <h2>Ошибка загрузки резюме</h2>
                <p>{{ $resumes['error'] }}</p>
                <div class="error-actions">
                    <a href="{{ route('get-code') }}" class="action-btn primary">
                        🔑 Повторная авторизация
                    </a>
                    <a href="{{ route('me') }}" class="action-btn secondary">
                        👤 Мой профиль
                    </a>
                </div>
            </div>

        @else
            <!-- Не авторизован -->
            <div class="resumes-unauthorized">
                <div class="unauthorized-icon">🔐</div>
                <h2>Требуется авторизация</h2>
                <p>Для просмотра резюме необходимо авторизоваться через hh.ru</p>
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




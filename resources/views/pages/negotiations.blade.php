@extends('layouts.app')

@section('title', 'Мои отклики - JobFinder')

@section('content')
<div class="negotiations-page">
    <div class="negotiations-container">
        <div class="negotiations-header">
            <h1 class="negotiations-title">Мои отклики</h1>
            <p class="negotiations-subtitle">Управляйте своими откликами на вакансии и отслеживайте их статус</p>
        </div>

        <!-- Фильтры -->
        <div class="negotiations-filters">
            <h3 class="filters-title">
                <span class="filter-icon">🔍</span>
                Фильтры
            </h3>

            <form method="GET" action="{{ route('negotiations') }}">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">Статус отклика:</label>
                        <select name="state" class="filter-select">
                            <option value="">Все статусы</option>
                            <option value="response" {{ request('state') === 'response' ? 'selected' : '' }}>Отклик</option>
                            <option value="invitation" {{ request('state') === 'invitation' ? 'selected' : '' }}>Приглашение</option>
                            <option value="discard" {{ request('state') === 'discard' ? 'selected' : '' }}>Отказ</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Сортировка:</label>
                        <select name="sort" class="filter-select">
                            <option value="created_at_desc" {{ request('sort', 'created_at_desc') === 'created_at_desc' ? 'selected' : '' }}>Новые первыми</option>
                            <option value="created_at_asc" {{ request('sort') === 'created_at_asc' ? 'selected' : '' }}>Старые первыми</option>
                            <option value="updated_at_desc" {{ request('sort') === 'updated_at_desc' ? 'selected' : '' }}>Недавно обновленные</option>
                        </select>
                    </div>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn-filter">Применить фильтры</button>
                    <a href="{{ route('negotiations') }}" class="btn-clear">Сбросить</a>
                </div>
            </form>
        </div>

        <!-- Статистика откликов -->
        @if(count($negotiations['items']) > 0)
        <div class="negotiations-stats">
            <h3 class="stats-title">Статистика откликов</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">{{ count($negotiations['items']) }}</div>
                    <div class="stat-label">Всего откликов</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ collect($negotiations['items'])->where('state.id', 'response')->count() }}</div>
                    <div class="stat-label">В ожидании</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ collect($negotiations['items'])->where('state.id', 'invitation')->count() }}</div>
                    <div class="stat-label">Приглашения</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ collect($negotiations['items'])->where('state.id', 'discard')->count() }}</div>
                    <div class="stat-label">Отказы</div>
                </div>
            </div>
        </div>
        @endif

        <div class="negotiations-grid">
            @forelse($negotiations['items'] as $negotiation)
                <div class="negotiation-card">
                    <div class="negotiation-card__header">
                        <div class="negotiation-card__title">
                            <a href="{{ $negotiation['vacancy']['alternate_url'] }}" target="_blank">
                                {{ $negotiation['vacancy']['name'] }}
                            </a>
                        </div>

                        <div class="negotiation-card__state">
                            @if($negotiation['state']['id'] == 'discard')
                                <span class="badge bg-danger">{{ $negotiation['state']['name'] }}</span>
                            @elseif($negotiation['state']['id'] == 'response')
                                <span class="badge bg-gray">{{ $negotiation['state']['name'] }}</span>
                            @elseif($negotiation['state']['id'] == 'invitation')
                                <span class="badge bg-success">{{ $negotiation['state']['name'] }}</span>
                            @else
                                <span class="badge bg-info">{{ $negotiation['state']['name'] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="negotiation-card__info">
                        <div class="negotiation-detail company">
                            <span class="detail-icon">🏢</span>
                            <strong>{{ $negotiation['vacancy']['employer']['name'] }}</strong>
                        </div>

                        @if(isset($negotiation['vacancy']['area']))
                        <div class="negotiation-detail">
                            <span class="detail-icon">📍</span>
                            {{ $negotiation['vacancy']['area']['name'] }}
                        </div>
                        @endif

                        @if(isset($negotiation['vacancy']['salary']) && $negotiation['vacancy']['salary'])
                        <div class="negotiation-detail salary">
                            <span class="detail-icon">💰</span>
                            @if($negotiation['vacancy']['salary']['from'])
                                от {{ number_format($negotiation['vacancy']['salary']['from'], 0, ',', ' ') }}
                            @endif
                            @if($negotiation['vacancy']['salary']['to'])
                                до {{ number_format($negotiation['vacancy']['salary']['to'], 0, ',', ' ') }}
                            @endif
                            {{ $negotiation['vacancy']['salary']['currency'] === 'RUR' ? '₽' : $negotiation['vacancy']['salary']['currency'] }}
                            @if(isset($negotiation['vacancy']['salary']['gross']) && !$negotiation['vacancy']['salary']['gross'])
                                <span class="salary-net">на руки</span>
                            @endif
                        </div>
                        @endif

                        @if(isset($negotiation['resume']))
                        <div class="negotiation-detail">
                            <span class="detail-icon">📄</span>
                            Резюме: {{ $negotiation['resume']['title'] }}
                        </div>
                        @endif

                        @if(isset($negotiation['counters']) && $negotiation['counters']['messages'] > 0)
                        <div class="negotiation-detail">
                            <span class="detail-icon">💬</span>
                            Сообщений: {{ $negotiation['counters']['messages'] }}
                            @if($negotiation['counters']['unread_messages'] > 0)
                                <span class="unread-badge">({{ $negotiation['counters']['unread_messages'] }} непрочитанных)</span>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="negotiation-card__created_at">
                        <span class="date-icon">📅</span>
                        Создан: {{ \Carbon\Carbon::parse($negotiation['created_at'])->format('d.m.Y H:i') }}
                        @if($negotiation['created_at'] != $negotiation['updated_at'])
                            <br>Обновлен: {{ \Carbon\Carbon::parse($negotiation['updated_at'])->format('d.m.Y H:i') }}
                        @endif
                    </div>

                    <div class="negotiation-actions">
                        <a href="{{ $negotiation['vacancy']['alternate_url'] }}" target="_blank" class="negotiation-btn secondary">
                            Просмотреть вакансию
                        </a>
                        @if(isset($negotiation['messages_url']))
                        <a href="#" class="negotiation-btn primary" onclick="alert('Функция просмотра сообщений будет добавлена позже')">
                            Сообщения
                        </a>
                        @endif
                        @if(isset($negotiation['resume']['alternate_url']))
                        <a href="{{ $negotiation['resume']['alternate_url'] }}" target="_blank" class="negotiation-btn primary">
                            Мое резюме
                        </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="no-negotiations">
                    <div class="no-negotiations-icon">📋</div>
                    <h2>У вас пока нет откликов</h2>
                    <p>Начните поиск работы, откликнувшись на интересные вакансии. Здесь будут отображаться все ваши отклики и их статусы.</p>
                    <div class="no-negotiations-actions">
                        <a href="{{ route('vacancies') }}" class="btn-primary">Найти вакансии</a>
                        <a href="{{ route('resumes') }}" class="btn-secondary">Мои резюме</a>
                    </div>
                </div>
            @endforelse
        </div>

        @if(count($negotiations['items']) > 0 && $negotiations['pages'] > 1)
        <div class="negotiations-pagination">
            @for($i = 1; $i <= $negotiations['pages']; $i++)
                <a href="{{ route('negotiations', array_merge(request()->query(), ['page' => $i])) }}"
                   class="negotiation-pagination__item {{ $i == $negotiations['page'] ? 'active' : '' }}">
                    {{ $i }}
                </a>
            @endfor
        </div>
        @endif

    </div>
</div>
@endsection

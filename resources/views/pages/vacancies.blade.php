@extends('layouts.app')

@section('title', 'Каталог вакансий - JobFinder')

@section('content')
<div class="vacancies-page">
    <div class="vacancies-container">
        <div class="vacancies-header">
            <h1 class="vacancies-title">Каталог вакансий</h1>
            <p class="vacancies-subtitle">Найдите работу своей мечты с расширенными фильтрами</p>
        </div>

    <!-- Форма поиска и фильтров -->
    <div class="search-section">
        <form method="GET" action="{{ url('/vacancies') }}" class="search-form">
            <!-- Основной поиск -->
            <div class="search-main">
                <div class="search-input-group">
                    <input type="text" name="text" placeholder="Должность, ключевые слова, компания"
                           value="{{ $currentFilters['text'] ?? '' }}" class="search-input">
                    <button type="submit" name="search" value="1" class="search-btn">Найти</button>
                </div>
            </div>

            <!-- Расширенные фильтры -->
            <div class="filters-section">
                <div class="filters-toggle">
                    <button type="button" class="filters-toggle-btn" onclick="toggleFilters()">
                        <span>Расширенные фильтры</span>
                        <span class="arrow">▼</span>
                    </button>
                </div>

                <div class="filters-content" id="filtersContent">
                    <div class="filters-grid">
                        <!-- Регион -->
                        <div class="filter-group">
                            <label>Регион:</label>
                            <select name="area" class="filter-select">
                                <option value="">Любой регион</option>
                                @if(isset($dictionaries['areas']))
                                    @foreach($dictionaries['areas'] as $area)
                                        <option value="{{ $area['id'] }}"
                                                {{ ($currentFilters['area'] ?? '') == $area['id'] ? 'selected' : '' }}>
                                            {{ $area['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Зарплата -->
                        <div class="filter-group">
                            <label>Зарплата от:</label>
                            <div class="salary-group">
                                <input type="number" name="salary_from" placeholder="0"
                                       value="{{ $currentFilters['salary_from'] ?? '' }}" class="salary-input">
                                <select name="currency" class="currency-select">
                                    @if(isset($dictionaries['dictionaries']['currency']))
                                        @foreach($dictionaries['dictionaries']['currency'] as $currency)
                                            <option value="{{ $currency['code'] }}"
                                                    {{ ($currentFilters['currency'] ?? 'RUR') == $currency['code'] ? 'selected' : '' }}>
                                                {{ $currency['name'] }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <!-- Тип занятости -->
                        <div class="filter-group">
                            <label>Тип занятости:</label>
                            <select name="employment" class="filter-select">
                                <option value="">Любой тип</option>
                                @if(isset($dictionaries['dictionaries']['employment']))
                                    @foreach($dictionaries['dictionaries']['employment'] as $employment)
                                        <option value="{{ $employment['id'] }}"
                                                {{ ($currentFilters['employment'] ?? '') == $employment['id'] ? 'selected' : '' }}>
                                            {{ $employment['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- График работы -->
                        <div class="filter-group">
                            <label>График работы:</label>
                            <select name="schedule" class="filter-select">
                                <option value="">Любой график</option>
                                @if(isset($dictionaries['dictionaries']['schedule']))
                                    @foreach($dictionaries['dictionaries']['schedule'] as $schedule)
                                        <option value="{{ $schedule['id'] }}"
                                                {{ ($currentFilters['schedule'] ?? '') == $schedule['id'] ? 'selected' : '' }}>
                                            {{ $schedule['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Опыт работы -->
                        <div class="filter-group">
                            <label>Опыт работы:</label>
                            <select name="experience" class="filter-select">
                                <option value="">Любой опыт</option>
                                @if(isset($dictionaries['dictionaries']['experience']))
                                    @foreach($dictionaries['dictionaries']['experience'] as $experience)
                                        <option value="{{ $experience['id'] }}"
                                                {{ ($currentFilters['experience'] ?? '') == $experience['id'] ? 'selected' : '' }}>
                                            {{ $experience['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Период публикации -->
                        <div class="filter-group">
                            <label>Период публикации:</label>
                            <select name="period" class="filter-select">
                                <option value="1" {{ ($currentFilters['period'] ?? '30') == '1' ? 'selected' : '' }}>За день</option>
                                <option value="3" {{ ($currentFilters['period'] ?? '30') == '3' ? 'selected' : '' }}>За 3 дня</option>
                                <option value="7" {{ ($currentFilters['period'] ?? '30') == '7' ? 'selected' : '' }}>За неделю</option>
                                <option value="30" {{ ($currentFilters['period'] ?? '30') == '30' ? 'selected' : '' }}>За месяц</option>
                            </select>
                        </div>

                        <!-- Сортировка -->
                        <div class="filter-group">
                            <label>Сортировка:</label>
                            <select name="order_by" class="filter-select">
                                <option value="relevance" {{ ($currentFilters['order_by'] ?? 'relevance') == 'relevance' ? 'selected' : '' }}>По релевантности</option>
                                <option value="publication_time" {{ ($currentFilters['order_by'] ?? 'relevance') == 'publication_time' ? 'selected' : '' }}>По дате</option>
                                <option value="salary_desc" {{ ($currentFilters['order_by'] ?? 'relevance') == 'salary_desc' ? 'selected' : '' }}>По убыванию зарплаты</option>
                                <option value="salary_asc" {{ ($currentFilters['order_by'] ?? 'relevance') == 'salary_asc' ? 'selected' : '' }}>По возрастанию зарплаты</option>
                            </select>
                        </div>

                        <!-- Количество на странице -->
                        <div class="filter-group">
                            <label>Показать на странице:</label>
                            <select name="per_page" class="filter-select">
                                <option value="10" {{ ($currentFilters['per_page'] ?? 20) == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($currentFilters['per_page'] ?? 20) == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ ($currentFilters['per_page'] ?? 20) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($currentFilters['per_page'] ?? 20) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>

                                        <!-- Стоп-слова -->
                    <div class="filter-special">
                        <label class="checkbox-label">
                            <input type="checkbox" name="use_stop_words" value="1"
                                   {{ ($currentFilters['use_stop_words'] ?? false) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            Исключить вакансии с базовыми стоп-словами (стажер, junior, удаленно и т.д.)
                        </label>
                    </div>

                    <!-- Пользовательские стоп-слова -->
                    <div class="custom-stopwords-section">
                        <label class="stopwords-label">Дополнительные стоп-слова:</label>
                        <div class="stopwords-container">
                            <div class="stopwords-tags" id="stopwordsTags">
                                @if(isset($currentFilters['custom_stop_words']) && !empty($currentFilters['custom_stop_words']))
                                    @foreach(explode(',', $currentFilters['custom_stop_words']) as $word)
                                        @if(trim($word))
                                            <span class="stopword-tag">
                                                {{ trim($word) }}
                                                <button type="button" class="remove-tag" onclick="removeStopwordTag(this)">×</button>
                                            </span>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            <input type="text" id="stopwordsInput" class="stopwords-input"
                                   placeholder="Введите слово и нажмите Enter"
                                   onkeydown="handleStopwordInput(event)">
                            <input type="hidden" name="custom_stop_words" id="customStopWordsHidden"
                                   value="{{ $currentFilters['custom_stop_words'] ?? '' }}">
                        </div>
                        <div class="stopwords-help">
                            Добавьте слова, которые должны исключаться из поиска. Нажмите Enter после каждого слова.
                        </div>
                    </div>

                    <!-- Кнопки управления -->
                    <div class="filters-actions">
                        <button type="submit" name="search" value="1" class="btn-apply">Применить фильтры</button>
                        <a href="{{ url('/vacancies') }}" class="btn-reset">Сбросить</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Результаты поиска -->
    <div class="results-section">
        @if($vacancies && !isset($vacancies['error']))
            <!-- Статистика -->
            <div class="results-stats">
                <p>Найдено вакансий: <strong>{{ $vacancies['found'] ?? 0 }}</strong></p>
                @if(isset($vacancies['pages']) && $vacancies['pages'] > 1)
                    <p>Страница {{ ($vacancies['page'] ?? 0) + 1 }} из {{ $vacancies['pages'] }}</p>
                @endif
            </div>

            <!-- Список вакансий -->
            @if(isset($vacancies['items']) && count($vacancies['items']) > 0)
                <div class="vacancies-list">
                    @foreach($vacancies['items'] as $vacancy)
                        <div class="vacancy-card">
                            <div class="vacancy-header">
                                <h3 class="vacancy-title">
                                    <a href="{{ $vacancy['alternate_url'] }}" target="_blank" rel="noopener">
                                        {{ $vacancy['name'] }}
                                    </a>
                                </h3>
                                @if($vacancy['salary'])
                                    <div class="vacancy-salary">
                                        @if($vacancy['salary']['from'])
                                            от {{ number_format($vacancy['salary']['from'], 0, ',', ' ') }}
                                        @endif
                                        @if($vacancy['salary']['to'])
                                            @if($vacancy['salary']['from']) - @endif
                                            до {{ number_format($vacancy['salary']['to'], 0, ',', ' ') }}
                                        @endif
                                        {{ $vacancy['salary']['currency'] }}
                                        @if($vacancy['salary']['gross'])
                                            <span class="salary-gross">до вычета налогов</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="vacancy-company">
                                <strong>{{ $vacancy['employer']['name'] }}</strong>
                                @if(isset($vacancy['area']))
                                    <span class="vacancy-location">• {{ $vacancy['area']['name'] }}</span>
                                @endif
                            </div>

                            @if(isset($vacancy['snippet']))
                                <div class="vacancy-snippet">
                                    @if($vacancy['snippet']['requirement'])
                                        <div class="snippet-section">
                                            <strong>Требования:</strong>
                                            <p>{!! $vacancy['snippet']['requirement'] !!}</p>
                                        </div>
                                    @endif
                                    @if($vacancy['snippet']['responsibility'])
                                        <div class="snippet-section">
                                            <strong>Обязанности:</strong>
                                            <p>{!! $vacancy['snippet']['responsibility'] !!}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="vacancy-meta">
                                <span class="vacancy-date">{{ date('d.m.Y', strtotime($vacancy['published_at'])) }}</span>
                                @if(isset($vacancy['schedule']))
                                    <span class="vacancy-schedule">• {{ $vacancy['schedule']['name'] }}</span>
                                @endif
                                @if(isset($vacancy['employment']))
                                    <span class="vacancy-employment">• {{ $vacancy['employment']['name'] }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Пагинация -->
                @if(isset($vacancies['pages']) && $vacancies['pages'] > 1)
                    <div class="pagination">
                        @php
                            $currentPage = ($currentFilters['page'] ?? 0);
                            $totalPages = $vacancies['pages'];
                            $queryParams = array_filter($currentFilters);
                            unset($queryParams['page']);
                        @endphp

                        @if($currentPage > 0)
                            <a href="{{ url('/vacancies') }}?{{ http_build_query(array_merge($queryParams, ['page' => $currentPage - 1])) }}"
                               class="pagination-btn">← Предыдущая</a>
                        @endif

                        @for($i = max(0, $currentPage - 2); $i <= min($totalPages - 1, $currentPage + 2); $i++)
                            @if($i == $currentPage)
                                <span class="pagination-current">{{ $i + 1 }}</span>
                            @else
                                <a href="{{ url('/vacancies') }}?{{ http_build_query(array_merge($queryParams, ['page' => $i])) }}"
                                   class="pagination-btn">{{ $i + 1 }}</a>
                            @endif
                        @endfor

                        @if($currentPage < $totalPages - 1)
                            <a href="{{ url('/vacancies') }}?{{ http_build_query(array_merge($queryParams, ['page' => $currentPage + 1])) }}"
                               class="pagination-btn">Следующая →</a>
                        @endif
                    </div>
                @endif
            @else
                <div class="no-results">
                    <h3>Вакансии не найдены</h3>
                    <p>Попробуйте изменить параметры поиска или расширить критерии фильтрации.</p>
                </div>
            @endif

        @elseif($vacancies && isset($vacancies['error']))
            <div class="error-message">
                <h3>Ошибка при поиске</h3>
                <p>{{ $vacancies['error'] }}</p>
            </div>

        @else
            <div class="welcome-message">
                <h3>Добро пожаловать в каталог вакансий!</h3>
                <p>Введите поисковый запрос или настройте фильтры для поиска подходящих вакансий.</p>
                <ul>
                    <li>🔍 <strong>Умный поиск</strong> - ищите по должности, ключевым словам или названию компании</li>
                    <li>🎯 <strong>Точные фильтры</strong> - настройте регион, зарплату, тип занятости и график работы</li>
                    <li>🚫 <strong>Стоп-слова</strong> - исключите нежелательные вакансии (стажировки, удаленную работу и т.д.)</li>
                    <li>📊 <strong>Гибкая сортировка</strong> - по релевантности, дате публикации или размеру зарплаты</li>
                </ul>
            </div>
        @endif
    </div>
</div>
</div>

<script>
function toggleFilters() {
    const content = document.getElementById('filtersContent');
    const arrow = document.querySelector('.filters-toggle-btn .arrow');

    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        arrow.textContent = '▲';
    } else {
        content.style.display = 'none';
        arrow.textContent = '▼';
    }
}

// Функции для работы со стоп-словами
function handleStopwordInput(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const input = event.target;
        const word = input.value.trim().toLowerCase();

        if (word && !isStopwordExists(word)) {
            addStopwordTag(word);
            input.value = '';
            updateStopwordsHidden();
        }
    }
}

function addStopwordTag(word) {
    const tagsContainer = document.getElementById('stopwordsTags');
    const tag = document.createElement('span');
    tag.className = 'stopword-tag';
    tag.innerHTML = `
        ${word}
        <button type="button" class="remove-tag" onclick="removeStopwordTag(this)">×</button>
    `;
    tagsContainer.appendChild(tag);
}

function removeStopwordTag(button) {
    button.parentElement.remove();
    updateStopwordsHidden();
}

function isStopwordExists(word) {
    const tags = document.querySelectorAll('#stopwordsTags .stopword-tag');
    for (let tag of tags) {
        if (tag.textContent.trim().replace('×', '').toLowerCase() === word) {
            return true;
        }
    }
    return false;
}

function updateStopwordsHidden() {
    const tags = document.querySelectorAll('#stopwordsTags .stopword-tag');
    const words = Array.from(tags).map(tag =>
        tag.textContent.trim().replace('×', '')
    );
    document.getElementById('customStopWordsHidden').value = words.join(',');
}

// Показать фильтры если есть активные
document.addEventListener('DOMContentLoaded', function() {
    const hasActiveFilters = {{ !empty(array_filter($currentFilters)) ? 'true' : 'false' }};
    if (hasActiveFilters) {
        const content = document.getElementById('filtersContent');
        const arrow = document.querySelector('.filters-toggle-btn .arrow');
        content.style.display = 'block';
        arrow.textContent = '▲';
    }

    // Инициализация скрытого поля стоп-слов при загрузке
    updateStopwordsHidden();
});
</script>
@endsection

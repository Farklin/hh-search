@extends('layouts.app')

@section('title', 'Автоматический отклик - JobFinder')

@section('content')
<div class="auto-apply-page">
    <div class="auto-apply-container">
        <div class="auto-apply-header">
            <h1 class="auto-apply-title">🤖 Автоматический отклик на вакансии</h1>
            <p class="auto-apply-subtitle">Настройте параметры для массового отклика с минимальным вмешательством</p>
        </div>

        <div class="auto-apply-form-container">
            <form id="autoApplyForm" class="auto-apply-form">
                @csrf

                <!-- Выбор резюме -->
                <div class="form-section">
                    <h3>📄 Выберите резюме</h3>
                    @if(empty($resumes))
                        <div class="alert alert-warning">
                            <p>❌ У вас нет резюме. <a href="{{ route('resumes') }}">Перейдите в раздел резюме</a> для создания.</p>
                        </div>
                    @else
                        <div class="resume-selection">
                            @foreach($resumes as $resume)
                                <label class="resume-option">
                                    <input type="radio" name="resume_id" value="{{ $resume['id'] }}" required>
                                    <div class="resume-card">
                                        <h4>{{ $resume['title'] }}</h4>
                                        <p>Обновлено: {{ date('d.m.Y', strtotime($resume['updated_at'])) }}</p>
                                        @if(isset($resume['status']))
                                            <span class="resume-status status-{{ $resume['status']['id'] }}">
                                                {{ $resume['status']['name'] }}
                                            </span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Параметры поиска -->
                <div class="form-section">
                    <h3>🔍 Параметры поиска вакансий</h3>

                    <div class="search-params">
                        <div class="param-group">
                            <label>Поисковый запрос *</label>
                            <input type="text" name="text" placeholder="Должность, ключевые слова" required class="form-input">
                        </div>

                        <div class="param-row">
                            <div class="param-group">
                                <label>Регион</label>
                                <select name="area" class="form-select">
                                    <option value="">Любой регион</option>
                                    @if(isset($dictionaries['areas']))
                                        @foreach($dictionaries['areas'] as $area)
                                            <option value="{{ $area['id'] }}">{{ $area['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="param-group">
                                <label>Зарплата от</label>
                                <input type="number" name="salary_from" placeholder="0" class="form-input">
                            </div>
                        </div>

                        <div class="param-row">
                            <div class="param-group">
                                <label>Тип занятости</label>
                                <select name="employment" class="form-select">
                                    <option value="">Любой тип</option>
                                    @if(isset($dictionaries['dictionaries']['employment']))
                                        @foreach($dictionaries['dictionaries']['employment'] as $employment)
                                            <option value="{{ $employment['id'] }}">{{ $employment['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="param-group">
                                <label>Опыт работы</label>
                                <select name="experience" class="form-select">
                                    <option value="">Любой опыт</option>
                                    @if(isset($dictionaries['dictionaries']['experience']))
                                        @foreach($dictionaries['dictionaries']['experience'] as $experience)
                                            <option value="{{ $experience['id'] }}">{{ $experience['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="param-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="use_stop_words" value="1" checked>
                                <span class="checkmark"></span>
                                Исключать вакансии с базовыми стоп-словами (стажер, junior, удаленно и т.д.)
                            </label>
                        </div>

                        <div class="param-group">
                            <label>Дополнительные стоп-слова (через запятую)</label>
                            <input type="text" name="custom_stop_words" placeholder="фриланс, подработка, без опыта" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Выбор сопроводительных писем -->
                <div class="form-section">
                    <h3>✉️ Сопроводительные письма</h3>
                    <div class="cover-letters-info">
                        <p>Выберите одно или несколько писем. При отклике будет случайно выбираться одно из выбранных.</p>
                        <a href="{{ route('cover-letters') }}" target="_blank" class="manage-letters-link">
                            Управление письмами
                        </a>
                    </div>

                    @if(empty($coverLetters))
                        <div class="alert alert-info">
                            <p>У вас нет сопроводительных писем. <a href="{{ route('cover-letters') }}">Создайте их</a> для более эффективного отклика.</p>
                        </div>
                    @else
                        <div class="cover-letters-selection">
                            @foreach($coverLetters as $letter)
                                <label class="letter-option">
                                    <input type="checkbox" name="cover_letters[]" value="{{ $letter['content'] }}">
                                    <div class="letter-card">
                                        <h4>{{ $letter['title'] }}</h4>
                                        <p class="letter-preview">{{ Str::limit($letter['content'], 100) }}</p>
                                        <small>Создано: {{ date('d.m.Y', strtotime($letter['created_at'])) }}</small>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Настройки автоматизации -->
                <div class="form-section">
                    <h3>⚙️ Настройки автоматизации</h3>

                    <div class="automation-params">
                        <div class="param-row">
                            <div class="param-group">
                                <label>Максимум вакансий для отклика *</label>
                                <input type="number" name="max_vacancies" value="20" min="1" max="100" required class="form-input">
                                <small>Рекомендуется не более 50 за раз</small>
                            </div>

                            <div class="param-group">
                                <label>Задержка между откликами (сек) *</label>
                                <input type="number" name="delay_seconds" value="5" min="3" max="60" required class="form-input">
                                <small>Минимум 3 секунды для избежания блокировки</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопки управления -->
                <div class="form-actions">
                    <button type="button" id="previewBtn" class="btn btn-secondary">
                        🔍 Предварительный просмотр
                    </button>
                    <button type="submit" id="startBtn" class="btn btn-primary" disabled>
                        🚀 Начать автоматический отклик
                    </button>
                </div>
            </form>
        </div>

        <!-- Превью вакансий -->
        <div id="previewSection" class="preview-section" style="display: none;">
            <h3>📋 Найденные вакансии для отклика</h3>
            <div class="preview-info">
                <p>Проверьте список вакансий перед запуском автоматического отклика. Вы можете исключить нежелательные вакансии.</p>
            </div>
            <div id="previewContent"></div>
            <div class="preview-actions">
                <button type="button" id="confirmApplyBtn" class="btn btn-primary">
                    ✅ Подтвердить и начать отклик
                </button>
                <button type="button" id="cancelPreviewBtn" class="btn btn-secondary">
                    ❌ Отменить
                </button>
            </div>
        </div>

        <!-- Результаты -->
        <div id="resultsSection" class="results-section" style="display: none;">
            <h3>📊 Результаты выполнения</h3>
            <div id="resultsContent"></div>
        </div>

        <!-- Прогресс -->
        <div id="progressSection" class="progress-section" style="display: none;">
            <h3>⏳ Выполнение автоматического отклика...</h3>
            <div class="progress-info">
                <div class="progress-bar">
                    <div id="progressFill" class="progress-fill"></div>
                </div>
                <div id="progressText" class="progress-text">Инициализация...</div>
            </div>
            <div id="progressDetails" class="progress-details"></div>
        </div>
    </div>
</div>

<style>
.auto-apply-page {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.auto-apply-header {
    text-align: center;
    margin-bottom: 30px;
}

.auto-apply-title {
    color: #2c3e50;
    margin-bottom: 10px;
}

.auto-apply-subtitle {
    color: #7f8c8d;
    font-size: 1.1em;
}

.form-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-section h3 {
    color: #34495e;
    margin-bottom: 20px;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 10px;
}

.resume-selection {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.resume-option {
    cursor: pointer;
}

.resume-card {
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
}

.resume-option input[type="radio"]:checked + .resume-card {
    border-color: #3498db;
    background-color: #ebf3fd;
}

.resume-card h4 {
    margin: 0 0 8px 0;
    color: #2c3e50;
}

.resume-status {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
}

.status-published {
    background-color: #d4edda;
    color: #155724;
}

.param-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 15px;
}

.param-group {
    margin-bottom: 15px;
}

.param-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #2c3e50;
}

.form-input, .form-select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-input:focus, .form-select:focus {
    border-color: #3498db;
    outline: none;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: normal !important;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 10px;
}

.cover-letters-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 6px;
}

.manage-letters-link {
    color: #3498db;
    text-decoration: none;
    font-weight: 600;
}

.cover-letters-selection {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.letter-option {
    cursor: pointer;
}

.letter-card {
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
}

.letter-option input[type="checkbox"]:checked + .letter-card {
    border-color: #27ae60;
    background-color: #eafaf1;
}

.letter-preview {
    color: #7f8c8d;
    margin: 8px 0;
    line-height: 1.4;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background-color: #3498db;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background-color: #2980b9;
}

.btn-secondary {
    background-color: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background-color: #7f8c8d;
}

.btn:disabled {
    background-color: #bdc3c7;
    cursor: not-allowed;
}

.results-section, .progress-section, .preview-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-top: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.preview-info {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    border-left: 4px solid #3498db;
}

.preview-list {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #ecf0f1;
    border-radius: 6px;
}

.preview-vacancy {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #ecf0f1;
    transition: background-color 0.2s ease;
}

.preview-vacancy:hover {
    background-color: #f8f9fa;
}

.preview-vacancy:last-child {
    border-bottom: none;
}

.vacancy-checkbox {
    margin-right: 12px;
    transform: scale(1.2);
}

.vacancy-info {
    flex: 1;
}

.vacancy-title {
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 4px 0;
    font-size: 14px;
}

.vacancy-details {
    font-size: 12px;
    color: #7f8c8d;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.vacancy-company {
    font-weight: 500;
}

.vacancy-salary {
    color: #27ae60;
    font-weight: 500;
}

.vacancy-external {
    margin-left: 8px;
}

.vacancy-external a {
    color: #3498db;
    text-decoration: none;
    font-size: 12px;
}

.preview-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #ecf0f1;
}

.preview-stats {
    background-color: #e8f5e8;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: 600;
    color: #2c3e50;
}

.progress-bar {
    width: 100%;
    height: 20px;
    background-color: #ecf0f1;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 15px;
}

.progress-fill {
    height: 100%;
    background-color: #3498db;
    width: 0%;
    transition: width 0.3s ease;
}

.progress-text {
    text-align: center;
    font-weight: 600;
    color: #2c3e50;
}

.progress-details {
    margin-top: 15px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 6px;
    font-family: monospace;
    font-size: 14px;
    max-height: 200px;
    overflow-y: auto;
}

.alert {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 15px;
}

.alert-warning {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
}

.alert-info {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

@media (max-width: 768px) {
    .param-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('autoApplyForm');
    const previewBtn = document.getElementById('previewBtn');
    const startBtn = document.getElementById('startBtn');
    const resultsSection = document.getElementById('resultsSection');
    const progressSection = document.getElementById('progressSection');
    const previewSection = document.getElementById('previewSection');
    const confirmApplyBtn = document.getElementById('confirmApplyBtn');
    const cancelPreviewBtn = document.getElementById('cancelPreviewBtn');

    let currentVacancies = [];

    // Включаем кнопку старта при выборе резюме
    const resumeInputs = document.querySelectorAll('input[name="resume_id"]');
    resumeInputs.forEach(input => {
        input.addEventListener('change', function() {
            startBtn.disabled = false;
            previewBtn.disabled = false;
        });
    });

    // Предварительный просмотр
    previewBtn.addEventListener('click', function() {
        showPreview();
    });

    // Подтверждение отклика
    confirmApplyBtn.addEventListener('click', function() {
        const selectedVacancies = getSelectedVacancies();
        if (selectedVacancies.length === 0) {
            alert('Выберите хотя бы одну вакансию для отклика');
            return;
        }
        executeAutoApply(selectedVacancies);
    });

    // Отмена превью
    cancelPreviewBtn.addEventListener('click', function() {
        previewSection.style.display = 'none';
        currentVacancies = [];
    });

    // Отправка формы (прямой запуск без превью)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        showPreview();
    });

    function showPreview() {
        const formData = new FormData(form);

        // Блокируем кнопки
        previewBtn.disabled = true;
        startBtn.disabled = true;

        fetch('{{ route("auto-apply.preview") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentVacancies = data.vacancies;
                displayVacanciesPreview(data.vacancies);
                previewSection.style.display = 'block';
                resultsSection.style.display = 'none';
                progressSection.style.display = 'none';
            } else {
                showError(data.message || 'Ошибка при получении превью вакансий');
            }
        })
        .catch(error => {
            showError('Ошибка при отправке запроса: ' + error.message);
        })
        .finally(() => {
            // Разблокируем кнопки
            previewBtn.disabled = false;
            startBtn.disabled = false;
        });
    }

    function displayVacanciesPreview(vacancies) {
        const html = `
            <div class="preview-stats">
                Найдено вакансий: ${vacancies.length}. Выберите те, на которые хотите откликнуться.
            </div>
            <div class="preview-list">
                ${vacancies.map(vacancy => `
                    <div class="preview-vacancy">
                        <input type="checkbox" class="vacancy-checkbox" value="${vacancy.id}" checked>
                        <div class="vacancy-info">
                            <div class="vacancy-title">${vacancy.name}</div>
                            <div class="vacancy-details">
                                <span class="vacancy-company">${vacancy.company}</span>
                                <span class="vacancy-area">${vacancy.area}</span>
                                <span class="vacancy-salary">${vacancy.salary}</span>
                                <span class="vacancy-date">${vacancy.published_at}</span>
                            </div>
                        </div>
                        <div class="vacancy-external">
                            <a href="${vacancy.url}" target="_blank">🔗 Открыть</a>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        document.getElementById('previewContent').innerHTML = html;
    }

    function getSelectedVacancies() {
        const checkboxes = document.querySelectorAll('.vacancy-checkbox:checked');
        return Array.from(checkboxes).map(cb => parseInt(cb.value));
    }

    function executeAutoApply(vacancyIds) {
        const formData = new FormData();
        formData.append('resume_id', document.querySelector('input[name="resume_id"]:checked').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Добавляем ID выбранных вакансий
        vacancyIds.forEach(id => {
            formData.append('vacancy_ids[]', id);
        });

        // Добавляем выбранные сопроводительные письма
        const selectedLetters = document.querySelectorAll('input[name="cover_letters[]"]:checked');
        selectedLetters.forEach(letter => {
            formData.append('cover_letters[]', letter.value);
        });

        // Скрываем превью и показываем прогресс
        previewSection.style.display = 'none';
        progressSection.style.display = 'block';
        resultsSection.style.display = 'none';

        updateProgress(0, 'Создание задачи автоотклика...');

        fetch('{{ route("auto-apply.execute") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateProgress(100, 'Задача создана успешно!');
                showTaskResults(data);
            } else {
                updateProgress(0, 'Ошибка при создании задачи');
                showError(data.message || 'Неизвестная ошибка');
            }
        })
        .catch(error => {
            updateProgress(0, 'Ошибка сети');
            showError('Ошибка при отправке запроса: ' + error.message);
        });
    }

    function updateProgress(percent, text) {
        document.getElementById('progressFill').style.width = percent + '%';
        document.getElementById('progressText').textContent = text;
    }

        function showTaskResults(data) {
        const html = `
            <div class="results-summary">
                <h4>✅ Задача создана успешно</h4>
                <div class="task-info">
                    <p><strong>ID задачи:</strong> ${data.task_id}</p>
                    <p><strong>Количество вакансий:</strong> ${data.total_vacancies}</p>
                    <p><strong>Статус:</strong> Задача поставлена в очередь на выполнение</p>
                    <p class="task-note">
                        💡 Задача будет выполнена в фоновом режиме.
                        Отклики будут отправляться автоматически с безопасными интервалами.
                    </p>
                </div>
            </div>
        `;

        document.getElementById('resultsContent').innerHTML = html;
        resultsSection.style.display = 'block';
        progressSection.style.display = 'none';
    }

    function showError(message) {
        const html = `
            <div class="error-result">
                <h4>❌ Ошибка</h4>
                <p>${message}</p>
            </div>
        `;

        document.getElementById('resultsContent').innerHTML = html;
        resultsSection.style.display = 'block';
        progressSection.style.display = 'none';
    }
});
</script>
@endsection

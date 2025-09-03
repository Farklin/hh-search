@extends('layouts.app')

@section('title', 'Управление сопроводительными письмами - JobFinder')

@section('content')
<div class="cover-letters-page">
    <div class="cover-letters-container">
        <div class="cover-letters-header">
            <h1 class="cover-letters-title">✉️ Управление сопроводительными письмами</h1>
            <p class="cover-letters-subtitle">Создавайте и редактируйте шаблоны для автоматического отклика</p>
        </div>

        <!-- Кнопка добавления нового письма -->
        <div class="add-letter-section">
            <button id="addLetterBtn" class="btn btn-primary">
                ➕ Добавить новое письмо
            </button>
        </div>

        <!-- Список существующих писем -->
        <div class="letters-list">
            @if(empty($coverLetters))
                <div class="no-letters">
                    <h3>📭 У вас пока нет сопроводительных писем</h3>
                    <p>Создайте первое письмо, чтобы использовать его при автоматическом отклике на вакансии.</p>
                </div>
            @else
                <div class="letters-grid">
                    @foreach($coverLetters as $letter)
                        <div class="letter-card" data-id="{{ $letter['id'] }}">
                            <div class="letter-header">
                                <h3 class="letter-title">{{ $letter['title'] }}</h3>
                                <div class="letter-actions">
                                    <button class="btn-edit" onclick="editLetter('{{ $letter['id'] }}')">
                                        ✏️ Редактировать
                                    </button>
                                    <button class="btn-delete" onclick="deleteLetter('{{ $letter['id'] }}')">
                                        🗑️ Удалить
                                    </button>
                                </div>
                            </div>
                            <div class="letter-content">
                                <p>{{ $letter['content'] }}</p>
                            </div>
                            <div class="letter-meta">
                                <small>Создано: {{ date('d.m.Y H:i', strtotime($letter['created_at'])) }}</small>
                                <small>Символов: {{ strlen($letter['content']) }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Модальное окно для добавления/редактирования письма -->
<div id="letterModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Новое сопроводительное письмо</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="letterForm" class="modal-body">
            @csrf
            <input type="hidden" id="letterId" name="id">

            <div class="form-group">
                <label for="letterTitleInput">Название письма *</label>
                <input type="text" id="letterTitleInput" name="title" required
                       placeholder="Например: Универсальное письмо" maxlength="255" class="form-input">
            </div>

            <div class="form-group">
                <label for="letterContentInput">Текст письма *</label>
                <textarea id="letterContentInput" name="content" required
                          placeholder="Введите текст сопроводительного письма..."
                          maxlength="2000" rows="8" class="form-textarea"></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span>/2000 символов
                </div>
            </div>

            <div class="letter-tips">
                <h4>💡 Советы для эффективного сопроводительного письма:</h4>
                <ul>
                    <li>Будьте краткими и по существу (150-300 символов оптимально)</li>
                    <li>Подчеркните свою заинтересованность в вакансии</li>
                    <li>Избегайте шаблонных фраз</li>
                    <li>Создайте несколько вариантов для разных типов вакансий</li>
                </ul>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    Отмена
                </button>
                <button type="submit" class="btn btn-primary">
                    💾 Сохранить
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Уведомления -->
<div id="notification" class="notification"></div>

<style>
.cover-letters-page {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.cover-letters-header {
    text-align: center;
    margin-bottom: 30px;
}

.cover-letters-title {
    color: #2c3e50;
    margin-bottom: 10px;
}

.cover-letters-subtitle {
    color: #7f8c8d;
    font-size: 1.1em;
}

.add-letter-section {
    text-align: center;
    margin-bottom: 30px;
}

.btn {
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
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

.btn-primary:hover {
    background-color: #2980b9;
}

.btn-secondary {
    background-color: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background-color: #7f8c8d;
}

.no-letters {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.no-letters h3 {
    color: #7f8c8d;
    margin-bottom: 15px;
}

.letters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}

.letter-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.letter-card:hover {
    transform: translateY(-2px);
}

.letter-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.letter-title {
    color: #2c3e50;
    margin: 0;
    flex: 1;
}

.letter-actions {
    display: flex;
    gap: 8px;
}

.btn-edit, .btn-delete {
    background: none;
    border: none;
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-edit {
    color: #3498db;
}

.btn-edit:hover {
    background-color: #ebf3fd;
}

.btn-delete {
    color: #e74c3c;
}

.btn-delete:hover {
    background-color: #fdf2f2;
}

.letter-content {
    margin-bottom: 15px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 6px;
    border-left: 4px solid #3498db;
}

.letter-content p {
    margin: 0;
    line-height: 1.5;
    color: #2c3e50;
}

.letter-meta {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #7f8c8d;
}

/* Модальное окно */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #ecf0f1;
}

.modal-header h2 {
    margin: 0;
    color: #2c3e50;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #7f8c8d;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    color: #2c3e50;
}

.modal-body {
    padding: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}

.form-input, .form-textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.3s ease;
}

.form-input:focus, .form-textarea:focus {
    border-color: #3498db;
    outline: none;
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
}

.char-counter {
    text-align: right;
    font-size: 12px;
    color: #7f8c8d;
    margin-top: 5px;
}

.letter-tips {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.letter-tips h4 {
    margin: 0 0 10px 0;
    color: #2c3e50;
}

.letter-tips ul {
    margin: 0;
    padding-left: 20px;
}

.letter-tips li {
    margin-bottom: 5px;
    font-size: 14px;
    color: #7f8c8d;
}

.modal-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid #ecf0f1;
}

/* Уведомления */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 6px;
    color: white;
    font-weight: 600;
    z-index: 1100;
    transform: translateX(400px);
    transition: transform 0.3s ease;
}

.notification.show {
    transform: translateX(0);
}

.notification.success {
    background-color: #27ae60;
}

.notification.error {
    background-color: #e74c3c;
}

@media (max-width: 768px) {
    .letters-grid {
        grid-template-columns: 1fr;
    }

    .modal-content {
        width: 95%;
        margin: 10px auto;
    }

    .letter-header {
        flex-direction: column;
        gap: 10px;
    }

    .letter-actions {
        align-self: flex-start;
    }
}
</style>

<script>
let currentEditingId = null;

document.addEventListener('DOMContentLoaded', function() {
    const addLetterBtn = document.getElementById('addLetterBtn');
    const letterForm = document.getElementById('letterForm');
    const letterContentInput = document.getElementById('letterContentInput');
    const charCount = document.getElementById('charCount');

    // Открытие модального окна для добавления письма
    addLetterBtn.addEventListener('click', function() {
        openModal();
    });

    // Отслеживание количества символов
    letterContentInput.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;

        if (count > 2000) {
            charCount.style.color = '#e74c3c';
        } else if (count > 1800) {
            charCount.style.color = '#f39c12';
        } else {
            charCount.style.color = '#7f8c8d';
        }
    });

    // Отправка формы
    letterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveLetter();
    });

    // Закрытие модального окна по клику вне его
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('letterModal');
        if (e.target === modal) {
            closeModal();
        }
    });
});

function openModal(letterId = null) {
    const modal = document.getElementById('letterModal');
    const modalTitle = document.getElementById('modalTitle');
    const letterIdInput = document.getElementById('letterId');
    const letterTitleInput = document.getElementById('letterTitleInput');
    const letterContentInput = document.getElementById('letterContentInput');
    const charCount = document.getElementById('charCount');

    if (letterId) {
        // Режим редактирования
        const letterCard = document.querySelector(`[data-id="${letterId}"]`);
        const title = letterCard.querySelector('.letter-title').textContent;
        const content = letterCard.querySelector('.letter-content p').textContent;

        modalTitle.textContent = 'Редактировать письмо';
        letterIdInput.value = letterId;
        letterTitleInput.value = title;
        letterContentInput.value = content;
        charCount.textContent = content.length;
        currentEditingId = letterId;
    } else {
        // Режим добавления
        modalTitle.textContent = 'Новое сопроводительное письмо';
        letterIdInput.value = '';
        letterTitleInput.value = '';
        letterContentInput.value = '';
        charCount.textContent = '0';
        currentEditingId = null;
    }

    modal.style.display = 'block';
    letterTitleInput.focus();
}

function closeModal() {
    const modal = document.getElementById('letterModal');
    modal.style.display = 'none';
    currentEditingId = null;
}

function editLetter(letterId) {
    openModal(letterId);
}

function deleteLetter(letterId) {
    if (!confirm('Вы уверены, что хотите удалить это сопроводительное письмо?')) {
        return;
    }

    const formData = new FormData();
    formData.append('id', letterId);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch('{{ route("cover-letters.delete") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Удаляем карточку из DOM
            const letterCard = document.querySelector(`[data-id="${letterId}"]`);
            letterCard.remove();

            showNotification('Письмо успешно удалено', 'success');

            // Проверяем, остались ли письма
            const remainingCards = document.querySelectorAll('.letter-card');
            if (remainingCards.length === 0) {
                location.reload(); // Перезагружаем страницу, чтобы показать сообщение "нет писем"
            }
        } else {
            showNotification('Ошибка при удалении письма', 'error');
        }
    })
    .catch(error => {
        showNotification('Ошибка сети: ' + error.message, 'error');
    });
}

function saveLetter() {
    const formData = new FormData(document.getElementById('letterForm'));
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch('{{ route("cover-letters.save") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            showNotification('Письмо успешно сохранено', 'success');

            // Перезагружаем страницу для обновления списка
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            let errorMessage = 'Ошибка при сохранении письма';
            if (data.errors) {
                const errors = Object.values(data.errors).flat();
                errorMessage = errors.join(', ');
            }
            showNotification(errorMessage, 'error');
        }
    })
    .catch(error => {
        showNotification('Ошибка сети: ' + error.message, 'error');
    });
}

function showNotification(message, type) {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.classList.add('show');

    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}
</script>
@endsection




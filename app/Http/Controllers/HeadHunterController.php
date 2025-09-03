<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HeadHunterService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class HeadHunterController extends Controller
{
    public function index()
    {

    }

    public function getCode()
    {
        $headHunterService = new HeadHunterService();
        $url = $headHunterService->getUrlCode();
        return redirect($url);
    }

    public function hhCode(Request $request){
        // Логируем полученные параметры для диагностики
        Log::info('HH OAuth callback received', [
            'code' => $request->code,
            'state' => $request->state,
            'all_params' => $request->all(),
            'query_string' => $request->getQueryString(),
            'url' => $request->fullUrl()
        ]);

        if (empty($request->code)) {
            Log::error('Empty code received from HeadHunter OAuth');
            return redirect()->route('home')->with('error', 'Ошибка авторизации: не получен код от HeadHunter');
        }

        session(['HH_CODE' => $request->code]);
        return redirect()->route('token');
    }


    public function vacancies(Request $request)
    {
        $headHunterService = new HeadHunterService();

        // Получаем справочники для фильтров
        $dictionaries = [];
        try {
            $dictionaries = $headHunterService->getFilterDictionaries();
        } catch (\Exception $e) {
            // Если не удалось получить справочники, создаем базовые
            $dictionaries = $this->getBasicDictionaries();
        }

        $vacancies = null;
        $filters = [];

        // Если есть параметры поиска, выполняем поиск
        if ($request->has('search') || $request->hasAny(['text', 'area', 'salary_from', 'employment', 'schedule', 'experience', 'custom_stop_words'])) {
            // Подготавливаем фильтры
            $filters = [
                'text' => $request->input('text'),
                'area' => $request->input('area'),
                'salary_from' => $request->input('salary_from'),
                'currency' => $request->input('currency', 'RUR'),
                'employment' => $request->input('employment'),
                'schedule' => $request->input('schedule'),
                'experience' => $request->input('experience'),
                'professional_role' => $request->input('professional_role'),
                'period' => $request->input('period', '30'),
                'order_by' => $request->input('order_by', 'relevance'),
                'per_page' => $request->input('per_page', 20),
                'page' => $request->input('page', 0),
                'use_stop_words' => $request->boolean('use_stop_words'),
                'custom_stop_words' => $request->input('custom_stop_words'),
            ];

            try {
                $vacancies = $headHunterService->searchVacancies($filters);
            } catch (\Exception $e) {
                $vacancies = ['error' => 'Ошибка при поиске вакансий: ' . $e->getMessage()];
            }
        }

        return view('pages.vacancies', [
            'vacancies' => $vacancies,
            'dictionaries' => $dictionaries,
            'currentFilters' => $filters,
        ]);
    }

    /**
     * Базовые справочники если API недоступно
     */
    private function getBasicDictionaries(): array
    {
        return [
            'dictionaries' => [
                'employment' => [
                    ['id' => 'full', 'name' => 'Полная занятость'],
                    ['id' => 'part', 'name' => 'Частичная занятость'],
                    ['id' => 'project', 'name' => 'Проектная работа'],
                    ['id' => 'volunteer', 'name' => 'Волонтерство'],
                    ['id' => 'probation', 'name' => 'Стажировка']
                ],
                'schedule' => [
                    ['id' => 'fullDay', 'name' => 'Полный день'],
                    ['id' => 'shift', 'name' => 'Сменный график'],
                    ['id' => 'flexible', 'name' => 'Гибкий график'],
                    ['id' => 'remote', 'name' => 'Удаленная работа'],
                    ['id' => 'flyInFlyOut', 'name' => 'Вахтовый метод']
                ],
                'experience' => [
                    ['id' => 'noExperience', 'name' => 'Нет опыта'],
                    ['id' => 'between1And3', 'name' => 'От 1 года до 3 лет'],
                    ['id' => 'between3And6', 'name' => 'От 3 до 6 лет'],
                    ['id' => 'moreThan6', 'name' => 'Более 6 лет']
                ],
                'currency' => [
                    ['code' => 'RUR', 'name' => 'Рубли'],
                    ['code' => 'USD', 'name' => 'Доллары США'],
                    ['code' => 'EUR', 'name' => 'Евро']
                ]
            ],
            'areas' => [
                ['id' => '1', 'name' => 'Москва'],
                ['id' => '2', 'name' => 'Санкт-Петербург'],
                ['id' => '113', 'name' => 'Россия']
            ]
        ];
    }

    public function me(){
        $headHunterService = new HeadHunterService();

        try {
            $me = $headHunterService->getMe();
        } catch (\Exception $e) {
            $me = ['error' => 'Ошибка при получении данных профиля: ' . $e->getMessage()];
        }

        return view('pages.me', ['me' => $me]);
    }

    public function token(){
        $headHunterService = new HeadHunterService();
        $token = $headHunterService->getToken();
        return redirect()->route('home');
    }

    public function resumes(){
        $headHunterService = new HeadHunterService();

        try {
            $resumes = $headHunterService->getAllResumes();
        } catch (\Exception $e) {
            $resumes = ['error' => 'Ошибка при получении резюме: ' . $e->getMessage()];
        }

        return view('pages.resumes', ['resumes' => $resumes]);
    }

    public function negotiation(Request $request){
        $page = $request->input('page', 1);
        $per_page = $request->input('per_page', 20);
        $state = $request->input('state');
        $sort = $request->input('sort', 'created_at_desc');

        $headHunterService = new HeadHunterService();
        $negotiation = $headHunterService->getNegotiation($page, $per_page, $state, $sort);

        return view('pages.negotiations', ['negotiations' => $negotiation]);
    }

    public function hhOut(){
        session()->forget('HH_CODE');
        session()->forget('HH_TOKEN');
        return redirect()->route('home');
    }

    /**
     * Страница автоматического отклика
     */
    public function autoApply(Request $request)
    {
        $headHunterService = new HeadHunterService();

        // Получаем резюме пользователя
        $resumes = [];
        try {
            $resumesData = $headHunterService->getAllResumes();
            $resumes = $resumesData['items'] ?? [];
        } catch (\Exception $e) {
            // Игнорируем ошибку, покажем пустой список
        }

        // Получаем справочники для фильтров
        $dictionaries = [];
        try {
            $dictionaries = $headHunterService->getFilterDictionaries();
        } catch (\Exception $e) {
            $dictionaries = $this->getBasicDictionaries();
        }

        // Получаем сохраненные сопроводительные письма
        $coverLetters = $this->getCoverLetters();

        return view('pages.auto-apply', [
            'resumes' => $resumes,
            'dictionaries' => $dictionaries,
            'coverLetters' => $coverLetters
        ]);
    }

    /**
     * Получить превью вакансий для отклика
     */
    public function previewVacancies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|min:1',
            'max_vacancies' => 'required|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $headHunterService = new HeadHunterService();

        // Подготавливаем фильтры для поиска вакансий
        $filters = [
            'text' => $request->input('text'),
            'area' => $request->input('area'),
            'salary_from' => $request->input('salary_from'),
            'currency' => $request->input('currency', 'RUR'),
            'employment' => $request->input('employment'),
            'schedule' => $request->input('schedule'),
            'experience' => $request->input('experience'),
            'professional_role' => $request->input('professional_role'),
            'period' => $request->input('period', '7'),
            'order_by' => $request->input('order_by', 'publication_time'),
            'use_stop_words' => $request->boolean('use_stop_words'),
            'custom_stop_words' => $request->input('custom_stop_words'),
        ];

        try {
            // Получаем подходящие вакансии
            $vacancies = $headHunterService->getVacanciesForAutoApply(
                $filters,
                $request->input('max_vacancies')
            );

            if (empty($vacancies)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не найдено подходящих вакансий по заданным критериям'
                ]);
            }

            // Формируем краткий список для превью
            $preview = array_map(function($vacancy) {
                return [
                    'id' => $vacancy['id'],
                    'name' => $vacancy['name'],
                    'company' => $vacancy['employer']['name'] ?? 'Не указано',
                    'area' => $vacancy['area']['name'] ?? 'Не указано',
                    'salary' => $this->formatSalary($vacancy['salary'] ?? null),
                    'url' => $vacancy['alternate_url'] ?? '',
                    'published_at' => date('d.m.Y', strtotime($vacancy['published_at']))
                ];
            }, $vacancies);

            return response()->json([
                'success' => true,
                'vacancies' => $preview,
                'total' => count($preview)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при поиске вакансий: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Выполнить автоматический отклик (создать задачу в БД)
     */
    public function executeAutoApply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resume_id' => 'required|string',
            'vacancy_ids' => 'required|array',
            'vacancy_ids.*' => 'integer',
            'cover_letters' => 'array',
            'cover_letters.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        try {
            // Создаем задачу в БД вместо прямой отправки
            $taskId = $this->createAutoApplyTask([
                'resume_id' => $request->input('resume_id'),
                'vacancy_ids' => $request->input('vacancy_ids'),
                'cover_letters' => $request->input('cover_letters', []),
                'user_session' => session()->getId()
            ]);

            return response()->json([
                'success' => true,
                'task_id' => $taskId,
                'message' => 'Задача автоматического отклика создана и будет выполнена в фоновом режиме',
                'total_vacancies' => count($request->input('vacancy_ids'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании задачи: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Получить статус задачи автоотклика
     */
    public function getTaskStatus($taskId)
    {
        $tasks = session('auto_apply_tasks', []);

        if (!isset($tasks[$taskId])) {
            return response()->json([
                'success' => false,
                'message' => 'Задача не найдена'
            ]);
        }

        return response()->json([
            'success' => true,
            'task' => $tasks[$taskId]
        ]);
    }

    /**
     * Форматирование зарплаты для отображения
     */
    private function formatSalary($salary): string
    {
        if (!$salary) {
            return 'Не указана';
        }

        $result = '';
        if (isset($salary['from']) && $salary['from']) {
            $result = 'от ' . number_format($salary['from'], 0, ',', ' ');
        }
        if (isset($salary['to']) && $salary['to']) {
            if ($result) {
                $result .= ' до ' . number_format($salary['to'], 0, ',', ' ');
            } else {
                $result = 'до ' . number_format($salary['to'], 0, ',', ' ');
            }
        }
        if ($result && isset($salary['currency'])) {
            $result .= ' ' . $salary['currency'];
        }

        return $result ?: 'Не указана';
    }

    /**
     * Создать задачу автоматического отклика в БД
     */
    private function createAutoApplyTask(array $data): int
    {
        // Пока используем сессию, позже можно заменить на БД
        $taskId = uniqid();

        $tasks = session('auto_apply_tasks', []);
        $tasks[$taskId] = [
            'id' => $taskId,
            'resume_id' => $data['resume_id'],
            'vacancy_ids' => $data['vacancy_ids'],
            'cover_letters' => $data['cover_letters'],
            'status' => 'pending',
            'created_at' => now()->toDateTimeString(),
            'total_vacancies' => count($data['vacancy_ids']),
            'processed' => 0,
            'successful' => 0,
            'failed' => 0
        ];

        session(['auto_apply_tasks' => $tasks]);

        return $taskId;
    }

    /**
     * Получить список сохраненных сопроводительных писем
     */
    private function getCoverLetters(): array
    {
        try {
            $coverLetters = \App\Models\CoverLetter::all();
            return $coverLetters->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting cover letters: ' . $e->getMessage());
            return [];
        }
    }
}

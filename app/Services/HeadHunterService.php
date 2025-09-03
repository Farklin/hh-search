<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
class HeadHunterService
{
    protected Client $client;

    public function __construct()
    {
        $config = [
            'base_uri' => 'https://api.hh.ru/',
            'timeout'  => 10.0,
        ];

        // Если есть токен, добавляем заголовок авторизации
        if(session('HH_TOKEN')){
            $config['headers'] = [
                'Authorization' => 'Bearer ' . session('HH_TOKEN')
            ];
        }

        $this->client = new Client($config);
    }


    public function getUrlCode(): string
    {
        //https://hh.ru/oauth/authorize?response_type=code&client_id={HH_CLIENT_ID}&state={state_value}&redirect_uri={REDIRECT_URI}
        return 'https://hh.ru/oauth/authorize?response_type=code&client_id=' . env('HH_CLIENT_ID') . '&state=user_id&redirect_uri=' . env('HH_REDIRECT_URI');
    }

    /**
     * Получить токен
     */
    public function getToken(): ?string
    {
        $client = new Client([
            'base_uri' => 'https://hh.ru/',
            'timeout'  => 10.0,
        ]);
        Log::info("code: " . session('HH_CODE'));
        // Создаем отдельный клиент для получения токена, так как нужен другой базовый URL
        $response = $client->post('oauth/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => env('HH_CLIENT_ID'),
                'client_secret' => env('HH_CLIENT_SECRET'),
                'redirect_uri' => env('HH_REDIRECT_URI'),
                'code' => session('HH_CODE'), // то что получили на шаге 1
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        if(isset($data['access_token'])){
            session(['HH_TOKEN' => $data['access_token']]);
        }

        return $data['access_token'] ?? null;
    }

    /**
     * Стоп-слова для фильтрации вакансий
     */
    private array $stopWords = [
        'стажер', 'стажёр', 'junior', 'trainee', 'intern', 'практикант',
        'удаленно', 'удаленная работа', 'remote', 'фриланс', 'freelance',
        'без опыта', 'начинающий', 'entry level', 'неполная занятость',
        'подработка', 'временная работа', 'проект'
    ];

    /**
     * Получить список вакансий по запросу с расширенными фильтрами
     */
    public function searchVacancies(array $filters = []): array
    {
        // Базовые параметры
        $query = [
            'per_page' => $filters['per_page'] ?? 20,
            'page' => $filters['page'] ?? 0,
        ];

        // Текст поиска
        if (!empty($filters['text'])) {
            $query['text'] = $filters['text'];
        }

        // Регион
        if (!empty($filters['area'])) {
            $query['area'] = $filters['area'];
        }

        // Зарплата
        if (!empty($filters['salary_from'])) {
            $query['salary'] = $filters['salary_from'];
        }

        // Валюта зарплаты
        if (!empty($filters['currency'])) {
            $query['currency'] = $filters['currency'];
        }

        // Тип занятости
        if (!empty($filters['employment'])) {
            $query['employment'] = $filters['employment'];
        }

        // График работы
        if (!empty($filters['schedule'])) {
            $query['schedule'] = $filters['schedule'];
        }

        // Опыт работы
        if (!empty($filters['experience'])) {
            $query['experience'] = $filters['experience'];
        }

        // Профессиональная область
        if (!empty($filters['professional_role'])) {
            $query['professional_role'] = $filters['professional_role'];
        }

        // Период публикации
        if (!empty($filters['period'])) {
            $query['period'] = $filters['period'];
        }

        // Сортировка
        if (!empty($filters['order_by'])) {
            $query['order_by'] = $filters['order_by'];
        }

        $response = $this->client->get('vacancies', ['query' => $query]);
        $data = json_decode($response->getBody()->getContents(), true);

        // Применяем фильтр стоп-слов если включен
        if (!empty($filters['use_stop_words']) && $filters['use_stop_words']) {
            // Получаем пользовательские стоп-слова
            $customStopWords = [];
            if (!empty($filters['custom_stop_words'])) {
                $customStopWords = explode(',', $filters['custom_stop_words']);
            }

            $data = $this->filterByStopWords($data, $customStopWords);
        } elseif (!empty($filters['custom_stop_words'])) {
            // Если базовые стоп-слова отключены, но есть пользовательские
            $customStopWords = explode(',', $filters['custom_stop_words']);
            $data = $this->filterByStopWords($data, $customStopWords);
        }

        return $data;
    }

        /**
     * Фильтрация вакансий по стоп-словам
     */
    private function filterByStopWords(array $data, array $customStopWords = []): array
    {
        if (!isset($data['items']) || !is_array($data['items'])) {
            return $data;
        }

        // Объединяем базовые и пользовательские стоп-слова
        $allStopWords = array_merge($this->stopWords, $customStopWords);
        $allStopWords = array_map('trim', $allStopWords);
        $allStopWords = array_filter($allStopWords); // Убираем пустые элементы

        if (empty($allStopWords)) {
            return $data;
        }

        $filteredItems = [];

        foreach ($data['items'] as $vacancy) {
            $shouldSkip = false;
            $textToCheck = strtolower($vacancy['name'] . ' ' . ($vacancy['snippet']['requirement'] ?? '') . ' ' . ($vacancy['snippet']['responsibility'] ?? ''));

            foreach ($allStopWords as $stopWord) {
                $stopWord = trim(strtolower($stopWord));
                if (!empty($stopWord) && strpos($textToCheck, $stopWord) !== false) {
                    $shouldSkip = true;
                    break;
                }
            }

            if (!$shouldSkip) {
                $filteredItems[] = $vacancy;
            }
        }

        $data['items'] = $filteredItems;
        $data['found'] = count($filteredItems);

        return $data;
    }

    /**
     * Получить справочники для фильтров
     */
    public function getFilterDictionaries(): array
    {
        $areas = $this->client->get('areas');
        $currencies = $this->client->get('dictionaries');
        $professionalRoles = $this->client->get('professional_roles');

        return [
            'areas' => json_decode($areas->getBody()->getContents(), true),
            'dictionaries' => json_decode($currencies->getBody()->getContents(), true),
            'professional_roles' => json_decode($professionalRoles->getBody()->getContents(), true),
        ];
    }


    public function getNegotiation($page, $per_page = 20, $state = null, $sort = 'created_at_desc'){
        $query = [
            'page' => $page,
            'per_page' => $per_page
        ];

        // Добавляем фильтр по статусу если указан
        if ($state) {
            $query['state'] = $state;
        }

        // Добавляем сортировку
        switch ($sort) {
            case 'created_at_asc':
                $query['order_by'] = 'created_at';
                $query['order_direction'] = 'asc';
                break;
            case 'updated_at_desc':
                $query['order_by'] = 'updated_at';
                $query['order_direction'] = 'desc';
                break;
            case 'created_at_desc':
            default:
                $query['order_by'] = 'created_at';
                $query['order_direction'] = 'desc';
                break;
        }

        $response = $this->client->get('negotiations', [
            'query' => $query
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        // Если фильтрация по статусу указана на клиенте (API может не поддерживать),
        // дополнительно фильтруем результаты
        if ($state && isset($data['items'])) {
            $data['items'] = array_filter($data['items'], function($item) use ($state) {
                return isset($item['state']['id']) && $item['state']['id'] === $state;
            });
            $data['items'] = array_values($data['items']); // Переиндексируем массив
        }

        return $data;
    }
    /**
     * Получить список областей (регионов)
     */
    public function getAreas(): array
    {
        $response = $this->client->get('areas');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getMe(): array
    {
        $response = $this->client->get('me');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getProfessionalRoles(): array
    {
        $response = $this->client->get('professional_roles');
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Получить данные по конкретной вакансии
     */
    public function getVacancy(int $id): array
    {
        $response = $this->client->get("vacancies/{$id}");
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getVacanciesByCompany(int $companyId): array
    {
        $response = $this->client->get("employers/{$companyId}/vacancies");
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getVacanciesByArea(int $areaId): array
    {
        $response = $this->client->get("areas/{$areaId}/vacancies");
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getAllResumes(): array
    {
        $response = $this->client->get("/resumes/mine");
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getResume(int $id): array
    {
        $response = $this->client->get("resumes/{$id}");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Отправить отклик на вакансию
     */
    public function applyToVacancy(int $vacancyId, string $resumeId, ?string $message = null): array
    {
        $params = [
            'vacancy_id' => $vacancyId,
            'resume_id' => $resumeId
        ];

        if ($message) {
            $params['message'] = $message;
        }

        try {
            $response = $this->client->post('negotiations', [
                'form_params' => $params
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'vacancy_id' => $vacancyId
            ];
        }
    }

    /**
     * Массовый отклик на вакансии
     */
    public function massApplyToVacancies(array $vacancyIds, string $resumeId, ?string $message = null, int $delaySeconds = 5): array
    {
        $results = [];
        $successful = 0;
        $failed = 0;

        foreach ($vacancyIds as $index => $vacancyId) {
            // Добавляем задержку между запросами (кроме первого)
            if ($index > 0) {
                sleep($delaySeconds);
            }

            $result = $this->applyToVacancy($vacancyId, $resumeId, $message);
            $results[] = $result;

            if (isset($result['error']) && $result['error']) {
                $failed++;
            } else {
                $successful++;
            }
        }

        return [
            'total' => count($vacancyIds),
            'successful' => $successful,
            'failed' => $failed,
            'results' => $results
        ];
    }

    /**
     * Получить подходящие вакансии для автоматического отклика
     */
    public function getVacanciesForAutoApply(array $filters, int $maxVacancies = 50): array
    {
        $allVacancies = [];
        $page = 0;
        $perPage = 20;

        // Получаем вакансии постранично до достижения лимита
        while (count($allVacancies) < $maxVacancies) {
            $filters['page'] = $page;
            $filters['per_page'] = $perPage;

            $response = $this->searchVacancies($filters);

            if (!isset($response['items']) || empty($response['items'])) {
                break;
            }

            $allVacancies = array_merge($allVacancies, $response['items']);
            $page++;

            // Если получили меньше чем per_page, значит это последняя страница
            if (count($response['items']) < $perPage) {
                break;
            }
        }

        // Ограничиваем количество вакансий
        if (count($allVacancies) > $maxVacancies) {
            $allVacancies = array_slice($allVacancies, 0, $maxVacancies);
        }

        return $allVacancies;
    }

    /**
     * Проверить, можно ли откликнуться на вакансию
     */
    public function canApplyToVacancy(int $vacancyId): bool
    {
        try {
            $vacancy = $this->getVacancy($vacancyId);

            // Проверяем, что вакансия активна и можно откликнуться
            return isset($vacancy['response_letter_required']) &&
                   !isset($vacancy['archived']) || !$vacancy['archived'];
        } catch (\Exception $e) {
            return false;
        }
    }

}

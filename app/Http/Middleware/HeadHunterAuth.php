<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\HeadHunterService;

class HeadHunterAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем наличие токена HeadHunter
        if (!session('HH_TOKEN')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться через HeadHunter'
                ], 401);
            }

            return redirect()->route('home')->with('error', 'Необходимо авторизоваться через HeadHunter');
        }

        // Дополнительная проверка валидности токена через API
        try {
            $headHunterService = new HeadHunterService();
            $userHhId = $headHunterService->getCurrentUserId();

            if (!$userHhId) {
                // Токен недействителен, очищаем сессию
                session()->forget(['HH_TOKEN', 'HH_CODE']);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Токен авторизации недействителен. Необходимо повторно авторизоваться'
                    ], 401);
                }

                return redirect()->route('home')->with('error', 'Токен авторизации недействителен. Необходимо повторно авторизоваться');
            }
        } catch (\Exception $e) {
            // В случае ошибки API пропускаем запрос, но логируем проблему
            \Illuminate\Support\Facades\Log::warning('HeadHunter API error in middleware: ' . $e->getMessage());
        }

        return $next($request);
    }
}

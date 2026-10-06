<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Project;
use App\Models\Service;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index(): View
    {
        $stats = [
            ['number' => '40+', 'description' => 'проектов'],
            ['number' => '5', 'description' => 'лет опыта'],
            ['number' => '5000+', 'description' => 'посетителей/месяц'],
            ['number' => '16000+', 'description' => 'подписчиков']
        ];

        $data = [
            'title' => 'Mounteam - Создаем продающие сайты',
            'heroTitle' => 'Создаем продающие сайты. Быстро. Mounteam.',
            'heroDescription' => 'Полный цикл создания веб-проектов с гарантией результата.',
            'heroSubtitle' => 'От Вас требуется минимум - остальное мы быстро сделаем с нуля',
            'heroProcess' => 'Идея → Структура → Дизайн → Наполнение → Реализация → Поддержка',
            'heroImage' => 'https://mounteam.ru/images/notebook.png',
            'stats' => $stats
        ];

        return view('index', $data);
    }

    /**
     * Get statistics data for API.
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'projects' => Project::count(),
            'years_experience' => 5,
            'monthly_visitors' => 5000,
            'subscribers' => 16000
        ];

        return response()->json($stats);
    }

    /**
     * Handle contact form submission.
     */
    public function contact(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:1000',
            'service' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Send email notification
            Mail::to(config('mail.admin_email', 'admin@mounteam.ru'))
                ->send(new ContactMail($request->all()));

            return response()->json([
                'success' => true,
                'message' => 'Ваше сообщение успешно отправлено. Мы свяжемся с вами в ближайшее время.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при отправке сообщения. Попробуйте позже.'
            ], 500);
        }
    }
}

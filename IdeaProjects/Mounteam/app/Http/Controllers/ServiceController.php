<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Service;
use App\Models\Portfolio;

class ServiceController extends Controller
{
    /**
     * Display websites service page.
     */
    public function websites(): View
    {
        $service = Service::where('slug', 'websites')->first();
        $portfolioItems = Portfolio::where('service_type', 'websites')->latest()->take(6)->get();

        return view('services.websites', compact('service', 'portfolioItems'));
    }

    /**
     * Display design service page.
     */
    public function design(): View
    {
        $service = Service::where('slug', 'design')->first();
        $portfolioItems = Portfolio::where('service_type', 'design')->latest()->take(6)->get();

        return view('services.design', compact('service', 'portfolioItems'));
    }

    /**
     * Display hosting service page.
     */
    public function hosting(): View
    {
        $service = Service::where('slug', 'hosting')->first();
        $hostingPlans = [
            [
                'name' => 'Shared Hosting',
                'price' => '299',
                'features' => ['10 GB SSD', '1 сайт', 'SSL сертификат', 'Техподдержка 24/7']
            ],
            [
                'name' => 'VPS',
                'price' => '999',
                'features' => ['50 GB SSD', 'Неограниченно сайтов', 'Root доступ', 'Backup']
            ],
            [
                'name' => 'Dedicated',
                'price' => '4999',
                'features' => ['500 GB SSD', 'Выделенный сервер', 'Полный контроль', 'DDoS защита']
            ]
        ];

        return view('services.hosting', compact('service', 'hostingPlans'));
    }

    /**
     * Display domains service page.
     */
    public function domains(): View
    {
        $service = Service::where('slug', 'domains')->first();
        $domainPrices = [
            '.ru' => '199',
            '.com' => '899',
            '.рф' => '299',
            '.org' => '799',
            '.net' => '899'
        ];

        return view('services.domains', compact('service', 'domainPrices'));
    }

    /**
     * Display promotion service page.
     */
    public function promotion(): View
    {
        $service = Service::where('slug', 'promotion')->first();
        $promotionServices = [
            'SEO оптимизация',
            'Контекстная реклама',
            'SMM продвижение',
            'Email маркетинг',
            'Аналитика и отчеты'
        ];

        return view('services.promotion', compact('service', 'promotionServices'));
    }

    /**
     * Display AI service page.
     */
    public function ai(): View
    {
        $service = Service::where('slug', 'ai')->first();
        $aiServices = [
            'Чат-боты для сайтов',
            'Автоматизация процессов',
            'Анализ данных',
            'Персонализация контента',
            'Машинное обучение'
        ];

        return view('services.ai', compact('service', 'aiServices'));
    }

    /**
     * Display education service page.
     */
    public function education(): View
    {
        $service = Service::where('slug', 'education')->first();
        $courses = [
            [
                'title' => 'Основы веб-разработки',
                'duration' => '2 месяца',
                'price' => '15000'
            ],
            [
                'title' => 'Продвинутый JavaScript',
                'duration' => '3 месяца',
                'price' => '25000'
            ],
            [
                'title' => 'UI/UX дизайн',
                'duration' => '2 месяца',
                'price' => '20000'
            ]
        ];

        return view('services.education', compact('service', 'courses'));
    }

    /**
     * Display payment service page.
     */
    public function payment(): View
    {
        $service = Service::where('slug', 'payment')->first();
        $paymentMethods = [
            'Карты РФ',
            'Криптовалюты',
            'Зарубежные карты',
            'PayPal',
            'Банковские переводы'
        ];

        return view('services.payment', compact('service', 'paymentMethods'));
    }

    /**
     * Display VPN service page.
     */
    public function vpn(): View
    {
        $service = Service::where('slug', 'vpn')->first();
        $vpnPlans = [
            [
                'name' => 'Базовый',
                'price' => '299',
                'features' => ['1 устройство', '10 серверов', 'Базовая поддержка']
            ],
            [
                'name' => 'Премиум',
                'price' => '599',
                'features' => ['5 устройств', '50+ серверов', 'Приоритетная поддержка']
            ],
            [
                'name' => 'Корпоративный',
                'price' => '1299',
                'features' => ['Неограниченно устройств', 'Все серверы', 'Персональный менеджер']
            ]
        ];

        return view('services.vpn', compact('service', 'vpnPlans'));
    }
}

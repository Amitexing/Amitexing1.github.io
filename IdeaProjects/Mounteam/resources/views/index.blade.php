<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Mounteam - Создаем продающие сайты' }}</title>
    <link rel="icon" href="{{ asset('https://mounteam.ru/images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('https://mounteam.ru/images/favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('https://mounteam.ru/images/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('https://mounteam.ru/css/styles.css') }}">
</head>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let lastScrollTop = 0;
    const header = document.querySelector('.header');

    if (!header) return;

    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Скролл вниз — спрячем
            header.style.transform = 'translateY(-100%)';
        } else {
            // Скролл вверх — покажем
            header.style.transform = 'translateY(0)';
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    });
});
</script>
<body>
    <main class="landing-page">
    @include('parts.header')


        <section class="hero-section">
            <div class="hero-content">
                <div class="hero-text">
                <h1 class="hero-title">
                       {!! __('messages.hero_title') !!}
                    </h1>

                    <p class="hero-description">
                        {{ $heroDescription ?? __('Полный цикл создания веб-проектов с гарантией результата.') }}<br>
                        {{ __('Разработка сайтов, дизайн, хостинг и регистрация доменов.') }}
                    </p>
                    <div class="hero-buttons">
                        <a href="{{ route('projects.create') ?? '#start-project' }}" class="btn btn-primary">{{ __('Начать проект') }}</a>
                        <a href="{{ route('portfolio') ?? '#portfolio' }}" class="btn btn-secondary">{{ __('Портфолио') }}</a>
                    </div>
                    <section class="stats-section">
                        @php
                            $stats = $stats ?? [
                                ['number' => '40+', 'description' => 'проектов'],
                                ['number' => '5', 'description' => 'лет опыта'],
                                ['number' => '5000+', 'description' => 'посетителей/месяц'],
                                ['number' => '16000+', 'description' => 'подписчиков']
                            ];
                        @endphp
                        @foreach($stats as $stat)
                            <div class="stat-item">
                                <span class="stat-number">{{ $stat['number'] }}</span><br>
                                <span class="stat-description">{{ __($stat['description']) }}</span>
                            </div>
                        @endforeach
                    </section>
                    <p class="hero-subtitle">
                        {{ $heroSubtitle ?? __('От Вас требуется минимум - остальное мы быстро сделаем с нуля') }}
                    </p>
                    <p class="hero-process">
                        {{ $heroProcess ?? __('Идея → Структура → Дизайн → Наполнение → Реализация → Поддержка') }}
                    </p>
                </div>
                <div class="hero-image">
                    <img
                        src="{{ $heroImage ?? 'https://mounteam.ru/images/notebook.png' }}"
                        alt="{{ __('Mounteam services illustration') }}"
                        class="hero-img"
                    />
                </div>
            </div>
        </section>

        <section class="services-section">
            <div class="services-grid">
                <div class="services-main">
                    <article href="{{ route('design') ?? '#design' }}" class="service-card service-card-main">
                        <h2 class="service-title">{{ __('Сайт любой сложности') }}</h2>
                        <p class="service-subtitle">{!! __('messages.service-subtitle') !!}</p>
                        <div class="service-buttons">
                            <a href="{{ route('services.websites') ?? '#websites' }}" class="btn btn-service-primary">{{ __('Подробнее') }}</a>
                            <a href="{{ route('portfolio') ?? '#portfolio' }}" class="btn btn-service-secondary">{{ __('Портфолио') }}</a>
                        </div>
                    </article>
                    <div class="services-row">
                        <div class="services-left">
                                <a href="{{ route('design') ?? '#design' }}" class="service-card service-card-design">
                                    <h2 class="service-title">{!! __('messages.service-title-design') !!}</h2>
                                </a>
                            <a href="{{ route('design') ?? '#design' }}" class="service-card service-card-promotion">
                                <h2 class="service-title">{{ __('Продвижение') }}</h2>
                            </a>
                        </div>
                        <div class="services-right">
                            <a href="{{ route('design') ?? '#design' }}" class="service-card service-card-domains">
                                <h2 class="service-title">{{ __('Домены') }}</h2>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="services-hosting">
                    <a href="{{ route('design') ?? '#design' }}" class="service-card service-card-hosting">
                        <h2 class="service-title-hosting">{{ __('Хостинг') }}</h2>
                        <div class="service-description">
                            {{ __('Для сайтов') }}<br>
                            {{ __('VPS') }}<br>
                            {{ __('VDS') }}<br>
                            {{ __('Dedicated') }}<br>
                            {{ __('Cloud') }}<br>
                            {{ __('AntiDDos') }}
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </main>

    @stack('scripts')
</body>
</html>

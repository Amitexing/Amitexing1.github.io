<header class="header">
    <img
        src="https://mounteam.ru/images/logo.svg"
        alt="{{ __('Mounteam Logo') }}"
        class="logo"
    />
    <nav class="navigation">
        <a href="{{ route('sites') ?? '#sites' }}" class="nav-link">{{ __('Сайты') }}</a>
        <a href="{{ route('design') ?? '#design' }}" class="nav-link">{{ __('Дизайн') }}</a>
        <a href="{{ route('hosting') ?? '#hosting' }}" class="nav-link">{{ __('Хостинг') }}</a>
        <a href="{{ route('domains') ?? '#domains' }}" class="nav-link">{{ __('Домены') }}</a>
        <a href="{{ route('promotion') ?? '#promotion' }}" class="nav-link nav-link-auto">{{ __('Продвижение') }}</a>
        <a href="{{ route('ai') ?? '#ai' }}" class="nav-link">{{ __('Нейросети') }}</a>
        <a href="{{ route('education') ?? '#education' }}" class="nav-link">{{ __('Обучение') }}</a>
        <a href="{{ route('payment') ?? '#payment' }}" class="nav-link nav-link-auto">{{ __('Оплата за рубежом') }}</a>
        <a href="{{ route('vpn') ?? '#vpn' }}" class="nav-link">{{ __('VPN') }}</a>
    </nav>
    <div class="auth-buttons">
        @guest
            <a href="{{ route('login') }}" class="btn btn-login">{{ __('Войти') }}</a>
            <a href="{{ route('register') }}" class="btn btn-signup">{{ __('Создать аккаунт') }}</a>
        @else
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-login">{{ __('Выйти') }}</button>
            </form>
            <a href="{{ route('dashboard') }}" class="btn btn-signup">{{ __('Личный кабинет') }}</a>
        @endguest
    </div>
</header>

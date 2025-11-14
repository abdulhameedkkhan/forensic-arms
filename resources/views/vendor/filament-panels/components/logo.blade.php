@php
    $brandName = filament()->getBrandName();
    $brandLogo = filament()->getBrandLogo();
    $brandLogoHeight = filament()->getBrandLogoHeight() ?? '1.5rem';
    $darkModeBrandLogo = filament()->getDarkModeBrandLogo();
    $hasDarkModeBrandLogo = filled($darkModeBrandLogo);

    $getLogoClasses = fn (bool $isDarkMode): string => \Illuminate\Support\Arr::toCssClasses([
        'fi-logo',
        'fi-logo-light' => $hasDarkModeBrandLogo && (! $isDarkMode),
        'fi-logo-dark' => $isDarkMode,
    ]);

    $logoStyles = "height: {$brandLogoHeight}";
@endphp

@capture($content, $logo, $isDarkMode = false)
    @if ($logo instanceof \Illuminate\Contracts\Support\Htmlable)
        <div
            {{
                $attributes
                    ->class([$getLogoClasses($isDarkMode)])
                    ->style([$logoStyles])
            }}
        >
            {{ $logo }}
        </div>
    @elseif (filled($logo))
        <img
            alt="{{ __('filament-panels::layout.logo.alt', ['name' => $brandName]) }}"
            src="{{ $logo }}"
            {{
                $attributes
                    ->class([$getLogoClasses($isDarkMode)])
                    ->style([$logoStyles])
            }}
        />
    @else
        <div
            {{
                $attributes->class([
                    $getLogoClasses($isDarkMode),
                    'flex items-center gap-2',
                ])
            }}
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <!-- Barrel -->
                <rect x="3" y="10" width="14" height="3" rx="1" />
                <!-- Grip/Handle -->
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 13v5a1 1 0 01-1 1h-3a1 1 0 01-1-1v-5" />
                <!-- Trigger guard -->
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12v3a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3" />
                <!-- Front sight -->
                <line x1="5" y1="8" x2="5" y2="10" stroke-width="2" />
                <!-- Rear sight -->
                <line x1="15" y1="8" x2="15" y2="10" stroke-width="2" />
                <!-- Barrel tip -->
                <circle cx="17" cy="11.5" r="1" />
            </svg>
            <span>{{ $brandName }}</span>
        </div>
    @endif
@endcapture

{{ $content($brandLogo) }}

@if ($hasDarkModeBrandLogo)
    {{ $content($darkModeBrandLogo, isDarkMode: true) }}
@endif

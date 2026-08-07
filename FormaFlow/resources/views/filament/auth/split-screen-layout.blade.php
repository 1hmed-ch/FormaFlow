@php
    use Filament\Support\Facades\FilamentView;$livewire ??= null;
    $renderHookScopes = $livewire?->getRenderHookScopes();
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="fi-split-login relative grid min-h-screen overflow-hidden bg-gray-950 lg:grid-cols-2">
        <div
            class="pointer-events-none absolute inset-0 bg-gradient-to-br from-primary-600 from-0% via-primary-800 via-40% to-gray-950 to-75%"></div>
        <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-16 h-96 w-96 rounded-full bg-white/5 blur-3xl"></div>

        <div class="relative z-10 hidden flex-col justify-between p-12 lg:flex">
            <div>
                <x-filament-panels::logo/>
            </div>

            <div class="max-w-md">
                <h2 class="text-3xl font-bold tracking-tight text-white">
                    FormaFlow
                </h2>
                <p class="mt-4 text-base leading-relaxed text-white/80">
                    Gérez vos dossiers, vos formations et vos participants depuis une seule plateforme.
                </p>
            </div>

            <p class="text-sm text-white/50">
                &copy; {{ date('Y') }} FormaFlow - Tous droits réservés.
            </p>
        </div>
        <div
            class="relative z-10 flex items-center justify-center bg-white px-6 py-12 dark:bg-gray-950 sm:px-12 lg:rounded-l-[3rem] lg:shadow-2xl">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}
</x-filament-panels::layout.base>

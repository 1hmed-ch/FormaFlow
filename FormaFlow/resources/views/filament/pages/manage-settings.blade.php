<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::section heading="Pièces jointes administratives" description="Documents requis pour le dossier de l'organisme" class="mt-6">

            <div class="pj-grid">
                @foreach ($this->piecesJointes as $key => $piece)
                    @php
                        $config = match ($piece['etat']) {
                            'Valide' => ['color' => '#22c55e', 'bg' => 'rgba(34,197,94,0.1)', 'icon' => 'heroicon-o-check-circle'],
                            'Expiré' => ['color' => '#ef4444', 'bg' => 'rgba(239,68,68,0.1)', 'icon' => 'heroicon-o-exclamation-triangle'],
                            default  => ['color' => '#9ca3af', 'bg' => 'rgba(156,163,175,0.1)', 'icon' => 'heroicon-o-document'],
                        };
                    @endphp

                    <div class="pj-card">
                        <div class="pj-card-header">
                            <div class="pj-card-title">
                                <x-filament::icon :icon="$config['icon']" style="width:20px;height:20px;color:{{ $config['color'] }};flex-shrink:0;" />
                                <span>{{ $piece['label'] }}</span>
                            </div>
                            <span class="pj-badge" style="color:{{ $config['color'] }};background:{{ $config['bg'] }};">
                                {{ $piece['etat'] }}
                            </span>
                        </div>

                        <div class="pj-filename" title="{{ $piece['nom_fichier'] }}">
                            @if ($piece['nom_fichier'])
                                <x-filament::icon icon="heroicon-o-paper-clip" style="width:14px;height:14px;flex-shrink:0;" />
                                <span class="pj-filename-text">{{ $piece['nom_fichier'] }}</span>
                            @else
                                <span style="font-style:italic;">Aucun fichier</span>
                            @endif
                        </div>

                        <div class="pj-dates">
                            <div>
                                <span class="pj-dates-label">Ajouté</span>
                                {{ $piece['date_ajout']?->format('d/m/Y') ?? '—' }}
                            </div>
                            <div>
                                <span class="pj-dates-label">Expire</span>
                                {{ $piece['date_expiration'] ? \Carbon\Carbon::parse($piece['date_expiration'])->format('d/m/Y') : '—' }}
                            </div>
                        </div>

                        <div class="pj-action">
                            {{ ($this->uploadPieceAction)(['collection' => $key])->size('sm') }}
                        </div>
                    </div>
                @endforeach
            </div>

        </x-filament::section>

        <div class="mt-6 flex justify-end">
            <x-filament::button
                type="submit"
                size="lg"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">
                    Enregistrer
                </span>
                <span wire:loading wire:target="save">
                    Enregistrement...
                </span>
            </x-filament::button>
        </div>
    </form>
<style>
    .pj-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.75rem;
    }
    .pj-card {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding: 0.85rem;
        border-radius: 0.625rem;
        border: 1px solid rgb(228 228 231);
        background: rgb(255 255 255);
        transition: box-shadow .15s ease, border-color .15s ease;
    }
    .dark .pj-card {
        border-color: rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.03);
    }
    .pj-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: rgb(212 212 216);
    }
    .dark .pj-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        border-color: rgba(255,255,255,0.18);
    }
    .pj-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.4rem;
    }
    .pj-card-title {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        font-size: 0.8rem;
        color: rgb(24 24 27);
        line-height: 1.2;
    }
    .dark .pj-card-title {
        color: rgb(243 244 246);
    }
    .pj-card-title span {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .pj-badge {
        font-size: 0.62rem;
        font-weight: 600;
        padding: 0.1rem 0.4rem;
        border-radius: 9999px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .pj-filename {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.68rem;
        color: rgb(113 113 122);
        overflow: hidden;
    }
    .dark .pj-filename {
        color: rgb(156 163 175);
    }
    .pj-filename-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .pj-dates {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.4rem;
        font-size: 0.68rem;
        color: rgb(113 113 122);
        border-top: 1px solid rgb(228 228 231);
        padding-top: 0.4rem;
    }
    .dark .pj-dates {
        color: rgb(156 163 175);
        border-top-color: rgba(255,255,255,0.06);
    }
    .pj-dates-label {
        display: block;
        font-size: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgb(161 161 170);
    }
    .dark .pj-dates-label {
        color: rgb(107 114 128);
    }
    .pj-action {
        margin-top: auto;
        padding-top: 0.2rem;
    }
    .pj-action button {
        width: 100%;
    }
</style>
</x-filament-panels::page>
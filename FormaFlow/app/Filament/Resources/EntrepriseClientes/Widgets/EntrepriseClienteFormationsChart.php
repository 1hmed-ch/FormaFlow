<?php

namespace App\Filament\Resources\EntrepriseClientes\Widgets;

use App\Enums\FormationStatus;
use App\Models\EntrepriseCliente;
use Filament\Widgets\ChartWidget;

class EntrepriseClienteFormationsChart extends ChartWidget
{
    public ?EntrepriseCliente $record = null;

    protected ?string $heading = 'Répartition des formations par statut';

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '200px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = [];

        foreach (FormationStatus::cases() as $status) {
            $counts[$status->getLabel()] = $this->record
                ->formations()
                ->where('statut', $status)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Formations',
                    'data' => array_values($counts),
                    'backgroundColor' => [
                        '#525EA7', // Planifiee - info
                        '#FED24F', // En cours - warning
                        '#45A9A9', // Terminee - success
                        '#D90000', // Annulee - danger
                    ],
                ],
            ],
            'labels' => array_keys($counts),
        ];
    }
}

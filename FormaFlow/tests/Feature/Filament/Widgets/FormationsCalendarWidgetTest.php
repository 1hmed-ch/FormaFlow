<?php

use App\Filament\Widgets\FormationsCalendarWidget;
use App\Models\Formation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('ne renvoie que les formations terminées comprises dans la période demandée', function () {
    $dansLaPeriode = Formation::factory()->terminee()->create([
        'date_debut' => '2026-03-05',
        'date_fin' => '2026-03-10',
    ]);

    $horsPeriode = Formation::factory()->terminee()->create([
        'date_debut' => '2026-06-01',
        'date_fin' => '2026-06-05',
    ]);

    $nonTerminee = Formation::factory()->create([ // statut par défaut = Planifiée
        'date_debut' => '2026-03-06',
        'date_fin' => '2026-03-08',
    ]);

    $widget = new FormationsCalendarWidget();

    $events = $widget->fetchEvents([
        'start' => '2026-03-01',
        'end' => '2026-03-31',
    ]);

    $ids = collect($events)->pluck('id');

    expect($ids)->toContain($dansLaPeriode->id)
        ->and($ids)->not->toContain($horsPeriode->id)
        ->and($ids)->not->toContain($nonTerminee->id);
});

it('place chaque événement aux bonnes dates de début et de fin', function () {
    $formation = Formation::factory()->terminee()->create([
        'intitule' => 'Formation Leadership',
        'date_debut' => '2026-04-10',
        'date_fin' => '2026-04-12',
    ]);

    $widget = new FormationsCalendarWidget();

    $events = $widget->fetchEvents([
        'start' => '2026-04-01',
        'end' => '2026-04-30',
    ]);

    $event = collect($events)->firstWhere('id', $formation->id);

    expect($event)->not->toBeNull()
        ->and($event['title'])->toBe('Formation Leadership')
        ->and(Carbon::parse($event['start'])->toDateString())->toBe('2026-04-10')
        ->and(Carbon::parse($event['end'])->toDateString())->toBe('2026-04-13');
});
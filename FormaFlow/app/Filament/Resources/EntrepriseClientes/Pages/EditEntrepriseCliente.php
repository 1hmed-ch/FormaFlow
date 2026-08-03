<?php

namespace App\Filament\Resources\EntrepriseClientes\Pages;

use App\Filament\Resources\EntrepriseClientes\EntrepriseClienteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditEntrepriseCliente extends EditRecord
{
    protected static string $resource = EntrepriseClienteResource::class;

    protected array $autresDocumentsAAttacher = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->autresDocumentsAAttacher = $data['autres_documents_repeater'] ?? [];
        unset($data['autres_documents_repeater']);

        return $data;
    }

    protected function afterSave(): void
    {
        Log::info('Contenu du repeater à sauvegarder :', $this->autresDocumentsAAttacher);

        foreach ($this->autresDocumentsAAttacher as $ligne) {
            if (empty($ligne['fichier']) || empty($ligne['intitule'])) {
                Log::warning('Ligne ignorée (fichier ou intitulé manquant) :', $ligne);
                continue;
            }

            $this->record
                ->addMediaFromDisk($ligne['fichier'], 'local')
                ->withCustomProperties(['intitule' => $ligne['intitule']])
                ->toMediaCollection('autres_documents');

            Log::info('Média attaché avec succès pour : ' . $ligne['intitule']);
        }
    }
}
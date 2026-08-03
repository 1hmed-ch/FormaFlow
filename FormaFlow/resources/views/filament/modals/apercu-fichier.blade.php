@php
    $isPdf = str_contains($mime ?? '', 'pdf');
    $isImage = str_starts_with($mime ?? '', 'image/');
@endphp

<div class="w-full">
    @if ($isPdf)
        <iframe
            src="{{ $url }}"
            style="width: 100%; height: 75vh; border: 0; border-radius: 0.5rem;"
        ></iframe>
    @elseif ($isImage)
        <div class="flex items-center justify-center rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
            <img src="{{ $url }}" alt="Aperçu du fichier" class="max-h-[75vh] max-w-full rounded-lg shadow" />
        </div>
    @else
        <div class="py-12 text-center text-gray-500">
            <p class="mb-2">Aperçu non disponible pour ce type de fichier.</p>
            <a href="{{ $url }}" target="_blank" class="text-primary-600 underline">
                Ouvrir dans un nouvel onglet
            </a>
        </div>
    @endif
</div>
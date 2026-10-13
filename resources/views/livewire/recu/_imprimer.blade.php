{{-- Choix du format + impression du reçu (PDF, 2 exemplaires). Variables : $type, $id. --}}
<div class="d-flex flex-wrap align-items-center justify-content-end">
    <select class="form-control form-control-sm mr-2 mb-1" style="width:auto;" wire:model.live="format" title="Format d'impression">
        @foreach (config('documents.recu.formats') as $cle => $f)
            <option value="{{ $cle }}">{{ $f['libelle'] }}</option>
        @endforeach
    </select>
    <a class="btn btn-default btn-sm text-nowrap" target="_blank"
        href="{{ url('recu/imprimer/' . $type . '/' . encrypt($id)) . '?format=' . $format }}">
        <i class="fa fa-print"></i> Imprimer
    </a>
</div>

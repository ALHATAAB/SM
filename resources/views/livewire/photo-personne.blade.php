<div class="text-center m-b-10">
    <div style="width:110px; height:110px; margin:0 auto 8px; border-radius:50%; overflow:hidden; background:#eef1f5; border:2px solid #dde3ea;">
        @if ($url)
            <img src="{{ $url }}" alt="Photo" style="width:100%; height:100%; object-fit:cover;">
        @else
            <div style="line-height:106px; font-size:44px; color:#9aa5b1;"><i class="ti ti-user"></i></div>
        @endif
    </div>

    @if ($peutModifier)
        <label class="btn btn-default btn-sm m-b-0" style="cursor:pointer;">
            <i class="ti ti-camera"></i> {{ $url ? 'Changer la photo' : 'Ajouter une photo' }}
            <input type="file" wire:model="photo" accept="image/png,image/jpeg" style="display:none;">
        </label>
        @if ($url)
            <button type="button" class="btn btn-outline-danger btn-sm" wire:click="supprimer"
                wire:confirm="Supprimer cette photo ?" title="Supprimer la photo">
                <i class="ti ti-trash"></i>
            </button>
        @endif
        <div wire:loading wire:target="photo" class="text-muted small m-t-5">Envoi en cours…</div>
        @error('photo') <div class="text-danger small m-t-5">{{ $message }}</div> @enderror
    @endif
</div>

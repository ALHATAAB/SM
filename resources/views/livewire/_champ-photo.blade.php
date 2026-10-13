{{-- Champ photo des formulaires de création (nécessite WithFileUploads et la propriété publique $photo). --}}
@php
    $apercu = null;
    if ($photo) {
        try { $apercu = $photo->temporaryUrl(); } catch (\Throwable $e) { $apercu = null; }
    }
@endphp
<div class="form-group">
    <label>Photo <small class="text-muted">(facultatif — JPG ou PNG, 2 Mo max)</small></label>
    <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:70px; height:70px; border-radius:50%; overflow:hidden; background:#eef1f5; border:2px solid #dde3ea; flex:none; text-align:center;">
            @if ($apercu)
                <img src="{{ $apercu }}" alt="Aperçu" style="width:100%; height:100%; object-fit:cover;">
            @else
                <span style="line-height:66px; font-size:30px; color:#9aa5b1;"><i class="ti ti-user"></i></span>
            @endif
        </div>
        <input type="file" wire:model="photo" accept="image/png,image/jpeg" class="form-control-file">
    </div>
    <div wire:loading wire:target="photo" class="text-muted small">Envoi en cours…</div>
    @error('photo') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

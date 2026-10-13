{{-- Bloc identité : informations + photo (si elle existe) à droite. --}}
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td style="vertical-align:top; padding:0; border:none;">
            @include('livewire.print._fiche_infos', ['lignes' => $lignes])
        </td>
        @if (!empty($photo) && file_exists($photo))
            <td style="width:92px; vertical-align:top; padding:0 0 0 8px; border:none; text-align:right;">
                <img src="{{ $photo }}" style="width:84px; border:1px solid #bbb;" alt="Photo">
            </td>
        @endif
    </tr>
</table>

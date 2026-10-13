{{-- En-tête officiel : pays + devise, puis logo et coordonnées de l'établissement. --}}
@if (!empty($pays))<div class="pays">{{ $pays }}</div>@endif
@if (!empty($devise))<div class="devise">{{ $devise }}</div>@endif
@include('livewire.print._entete_societe')

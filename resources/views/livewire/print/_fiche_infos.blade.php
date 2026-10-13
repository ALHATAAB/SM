{{-- Bloc « libellé / valeur » sur deux colonnes de paires. $lignes = [[libellé, valeur], ...] --}}
@if (!empty($lignes))
    <table class="infos">
        @foreach (array_chunk($lignes, 2) as $paire)
            <tr>
                <td class="lib">{{ $paire[0][0] }}</td>
                <td>{{ $paire[0][1] }}</td>
                @if (isset($paire[1]))
                    <td class="lib">{{ $paire[1][0] }}</td>
                    <td>{{ $paire[1][1] }}</td>
                @else
                    <td class="lib"></td><td></td>
                @endif
            </tr>
        @endforeach
    </table>
@else
    <div class="vide">Aucune information.</div>
@endif

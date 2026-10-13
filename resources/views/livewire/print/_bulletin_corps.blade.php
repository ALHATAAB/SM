        <table class="infos">
            @foreach (array_chunk($b['salarie'], 2) as $paire)
                <tr>
                    @foreach ($paire as $info)
                        <td><strong>{{ $info[0] }} :</strong> {{ $info[1] }}</td>
                    @endforeach
                    @if (count($paire) === 1)
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </table>

        @if ($b['type'] === 'personnel')
            <table class="net" style="margin: 0 0 10px;">
                <tr>
                    <td class="k">Salaire brut</td>
                    <td><span class="montant">{{ $fmt($b['brut']) }} F CFA</span></td>
                    <td class="k">Base de cotisation</td>
                    <td><span class="montant">{{ $fmt($b['base_cotisation']) }} F CFA</span></td>
                </tr>
                <tr>
                    <td class="k">Brut imposable</td>
                    <td><span class="montant">{{ $fmt($b['brut_imposable'] ?? 0) }} F CFA</span></td>
                    <td class="k">Brut non imposable</td>
                    <td><span class="montant">{{ $fmt($b['brut_non_imposable'] ?? 0) }} F CFA</span></td>
                </tr>
            </table>
        @endif

        @if ($b['type'] === 'enseignant')
            <table class="net" style="margin: 0 0 10px;">
                <tr>
                    <td class="k">Total brut</td>
                    <td><span class="montant">{{ $fmt($b['brut']) }} F CFA</span></td>
                </tr>
            </table>
        @endif

        <table class="lignes">
            <thead>
                <tr>
                    <th style="text-align:left;">Rubrique</th>
                    <th>Base</th>
                    <th>Taux</th>
                    <th>Nombre</th>
                    <th>Gains</th>
                    <th>Retenues</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($b['gains'] as $l)
                    <tr>
                        <td>{{ $l['libelle'] }}</td>
                        <td class="d">{{ $l['base'] !== null ? $fmt($l['base']) : '' }}</td>
                        <td class="c">{{ $dec($l['taux']) }}</td>
                        <td class="c">{{ $dec($l['nombre']) }}</td>
                        <td class="d">{{ $fmt($l['montant']) }}</td>
                        <td></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="c vide">Aucun gain</td></tr>
                @endforelse

                @foreach ($b['retenues'] as $l)
                    <tr>
                        <td>{{ $l['libelle'] }}</td>
                        <td class="d">{{ $l['base'] !== null ? $fmt($l['base']) : '' }}</td>
                        <td class="c">{{ $dec($l['taux']) }}</td>
                        <td class="c">{{ $dec($l['nombre']) }}</td>
                        <td></td>
                        <td class="d">{{ $fmt($l['montant']) }}</td>
                    </tr>
                @endforeach

                <tr class="total">
                    <td colspan="4">TOTAUX</td>
                    <td class="d">{{ $fmt($b['total_gain']) }}</td>
                    <td class="d">{{ $fmt($b['total_retenu']) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="net">
            <tr>
                <td class="k">Net à payer</td>
                <td><span class="montant">{{ $fmt($b['net']) }} F CFA</span></td>
            </tr>
            @if (!empty($b['paiement']))
            <tr>
                <td class="k">Paiement</td>
                <td>
                    <span class="{{ $b['paiement']['paye'] ? 'paye' : 'attente' }}">{{ $b['paiement']['statut'] }}</span>
                    @if (!empty($b['paiement']['date']))
                        — le {{ $b['paiement']['date'] }}
                    @endif
                    @if (!empty($b['paiement']['mode']))
                        ({{ $b['paiement']['mode'] }})
                    @endif
                </td>
            </tr>
            @endif
        </table>

        @if ($b['type'] === 'personnel' && !empty($b['patronales']))
            <table class="lignes" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th style="text-align:left;">Charges patronales <span style="font-weight:normal;font-size:9px;">(à la charge de l'employeur, non déduites du net)</span></th>
                        <th>Base</th>
                        <th>Taux</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($b['patronales'] as $l)
                        <tr>
                            <td>{{ $l['libelle'] }}</td>
                            <td class="d">{{ $l['base'] !== null ? $fmt($l['base']) : '' }}</td>
                            <td class="c">{{ $dec($l['taux']) }}</td>
                            <td class="d">{{ $fmt($l['montant']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

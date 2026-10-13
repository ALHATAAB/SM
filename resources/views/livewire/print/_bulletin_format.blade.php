{{--
    Bulletin de salaire du personnel au format de l'établissement (modèle « simulation_bulletin ») :
    en-tête, période, coût mensuel du poste, gains, salaire brut, cotisations sociales et fiscales, salaire net,
    retenues sur salaire, net à payer, identité du salarié et signatures.
    Variables : $b (bulletin calculé ou bulletin modèle), $fmt, $dec.
--}}
@php
    $fmt = $fmt ?? fn ($v) => number_format((float) $v, 0, ',', ' ');
    $dec = $dec ?? fn ($v) => $v === null ? '' : rtrim(rtrim(number_format((float) $v, 2, ',', ' '), '0'), ',');
    $det = $b['details'] ?? [];
    $ident = $b['identite'] ?? [];

    $retenuesAutres = collect($b['retenues'] ?? [])->reject(fn ($l) => in_array($l['auto'] ?? null, ['CNSS', 'ITS'], true))->values();
    $totalAutres    = (float) $retenuesAutres->sum('montant');
    $cnss           = (float) ($det['cnss'] ?? 0);
    $its            = (float) ($det['its'] ?? 0);
    $totalSocFisc   = $cnss + $its;
    $patronal       = (float) collect($b['patronales'] ?? [])->sum('montant');
    $netAPayer      = (float) $b['net'];
    $salaireNet     = $netAPayer + $totalAutres; // brut − retenues sociales et fiscales

@endphp
<style>
    .bf { font-family: sans-serif; font-size: 11px; color: #111; }
    .bf table { width: 100%; border-collapse: collapse; }
    .bf .bf-titre { text-align: center; font-size: 16px; font-weight: bold; letter-spacing: 1px; margin: 4px 0 8px; }
    .bf table.bf-box td, .bf table.bf-box th { border: 1px solid #555; padding: 3px 6px; }
    .bf table.bf-box th { background: #e9ecef; text-align: center; }
    .bf table.bf-main td { border: 1px solid #555; padding: 3px 6px; height: 15px; }
    .bf table.bf-main td.m { width: 15%; text-align: right; }
    .bf table.bf-main td.b { width: 14%; text-align: right; }
    .bf table.bf-main td.t { width: 8%; text-align: center; }
    .bf table.bf-main td.n { width: 8%; text-align: center; }
    .bf table.bf-main tr.sec td { background: #e9ecef; text-align: center; font-weight: bold; }
    .bf table.bf-main tr.tot td { background: #f4f4f4; font-weight: bold; }
    .bf table.bf-main tr.net td { font-size: 14px; font-weight: bold; padding: 7px 6px; background: #e9ecef; }
    .bf table.bf-main tr.pat td { font-style: italic; }
    .bf .bf-droite { text-align: right; font-size: 10px; line-height: 1.6; vertical-align: top; }
    .bf .bf-sign td { height: 60px; vertical-align: bottom; text-align: center; font-weight: bold; border: none; }
</style>
<div class="bf">

    {{-- En-tête : société à gauche, informations de l'employé à droite --}}
    <table>
        <tr>
            <td style="width:72%; vertical-align:top;">
                @if (empty($b['modele']))
                    @include('livewire.print._entete_societe')
                @else
                    <strong style="font-size:14px; text-transform:uppercase;">{{ \App\Services\SocieteService::info()['nom'] ?? '' }}</strong>
                @endif
            </td>
            <td class="bf-droite">
                Date d'entrée : {{ $ident['date_embauche'] ?? '' }}<br>
                Charges familiales : {{ $ident['charge'] ?? 0 }}<br>
                Ancienneté : {{ isset($ident['anciennete']) ? $ident['anciennete'].' an(s)' : '' }}
            </td>
        </tr>
    </table>

    <div class="bf-titre">BULLETIN DE SALAIRE</div>

    {{-- Période et coût mensuel du poste --}}
    <table>
        <tr>
            <td style="width:62%; vertical-align:top; padding-right:10px;">
                <table class="bf-box">
                    <tr>
                        <td style="width:45%; text-align:center;"><strong>DECOMPTE DE LA REMUNERATION</strong></td>
                        <td style="text-align:center;">PÉRIODE</td>
                        <td style="text-align:center;"><strong>{{ $b['periode'] }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2">Réglé le : {{ $b['paiement']['date'] ?? '' }}</td>
                        <td>Jours de présence : </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align:top;">
                <table class="bf-box">
                    <tr><th colspan="2">Coût mensuel poste</th></tr>
                    <tr><td>Salaire brut</td><td style="text-align:right;">{{ $fmt($b['brut']) }}</td></tr>
                    <tr><td>Charges patronales</td><td style="text-align:right;">{{ $fmt($patronal) }}</td></tr>
                    <tr><td><strong>TOTAL</strong></td><td style="text-align:right;"><strong>{{ $fmt($b['brut'] + $patronal) }}</strong></td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Tableau des rubriques : Base / Taux / Nombre / Montant gain / Montant retenue --}}
    <table class="bf-main" style="margin-top:8px;">
        <thead>
            <tr class="sec">
                <td style="text-align:left;">RUBRIQUE</td>
                <td class="b">Base</td>
                <td class="t">Taux</td>
                <td class="n">Nombre</td>
                <td class="m">Gain</td>
                <td class="m">Retenue</td>
            </tr>
        </thead>
        <tbody>
        {{-- Gains --}}
        @foreach ($b['gains'] as $l)
            <tr>
                <td>{{ mb_strtoupper($l['libelle']) }}</td>
                <td class="b">{{ $l['base'] !== null ? $fmt($l['base']) : '' }}</td>
                <td class="t">{{ $dec($l['taux']) }}</td>
                <td class="n">{{ $dec($l['nombre']) }}</td>
                <td class="m">{{ $fmt($l['montant']) }}</td>
                <td class="m"></td>
            </tr>
        @endforeach
        <tr class="tot">
            <td colspan="4" style="text-align:center;">SALAIRE BRUT</td>
            <td class="m">{{ $fmt($b['brut']) }}</td>
            <td class="m"></td>
        </tr>
        @if (($b['brut_non_imposable'] ?? 0) > 0)
            <tr>
                <td colspan="4">dont brut imposable / brut non imposable</td>
                <td class="m" colspan="2">{{ $fmt($b['brut_imposable'] ?? 0) }} / {{ $fmt($b['brut_non_imposable']) }}</td>
            </tr>
        @endif

        {{-- Cotisations sociales et fiscales --}}
        <tr class="sec"><td colspan="6">COTISATIONS SOCIALES ET FISCALES</td></tr>
        <tr>
            <td>Retenue CNSS</td>
            <td class="b">{{ ($det['base_cnss'] ?? null) !== null ? $fmt($det['base_cnss']) : $fmt($b['base_cotisation'] ?? 0) }}</td>
            <td class="t">{{ $dec($det['taux_cnss'] ?? null) }}</td>
            <td class="n"></td>
            <td class="m"></td>
            <td class="m">{{ $fmt($cnss) }}</td>
        </tr>
        @if (($det['taux_frais_pro'] ?? 0) > 0)
            <tr>
                <td>Abattement ITS pour frais professionnels <span style="font-size:9px;">({{ $fmt($det['frais_pro'] ?? 0) }})</span></td>
                <td class="b"></td>
                <td class="t">{{ $dec($det['taux_frais_pro']) }}</td>
                <td class="n"></td>
                <td class="m"></td>
                <td class="m"></td>
            </tr>
        @endif
        <tr>
            <td>Retenue ITS du mois <span style="font-size:9px;">(base retenues ITS)</span></td>
            <td class="b">{{ ($det['base_its'] ?? null) !== null ? $fmt($det['base_its']) : '' }}</td>
            <td class="t"></td>
            <td class="n"></td>
            <td class="m"></td>
            <td class="m">{{ $fmt($its) }}</td>
        </tr>
        <tr>
            <td>Taux abattement ITS pour charges de famille</td>
            <td class="b"></td>
            <td class="t">{{ $dec($det['taux_abattement'] ?? 0) }}</td>
            <td class="n"></td>
            <td class="m"></td>
            <td class="m"></td>
        </tr>
        <tr class="tot">
            <td colspan="4" style="text-align:center;">TOTAL DES RETENUES SOCIALES ET FISCALES</td>
            <td class="m"></td>
            <td class="m">{{ $fmt($totalSocFisc) }}</td>
        </tr>

        {{-- Salaire net --}}
        <tr class="tot">
            <td colspan="4" style="text-align:center;">SALAIRE NET</td>
            <td class="m">{{ $fmt($salaireNet) }}</td>
            <td class="m"></td>
        </tr>

        {{-- Retenues sur salaire (FSSP, avances, prêts...) --}}
        <tr class="sec"><td colspan="6">RETENUES SUR SALAIRE</td></tr>
        @forelse ($retenuesAutres as $l)
            <tr>
                <td>{{ mb_strtoupper($l['libelle']) }}</td>
                <td class="b">{{ $l['base'] !== null ? $fmt($l['base']) : '' }}</td>
                <td class="t">{{ $dec($l['taux']) }}</td>
                <td class="n">{{ $dec($l['nombre']) }}</td>
                <td class="m"></td>
                <td class="m">{{ $fmt($l['montant']) }}</td>
            </tr>
        @empty
            <tr><td colspan="4">&nbsp;</td><td class="m"></td><td class="m">0</td></tr>
        @endforelse
        <tr class="tot">
            <td colspan="4" style="text-align:center;">TOTAL DES RETENUES SUR SALAIRES</td>
            <td class="m"></td>
            <td class="m">{{ $fmt($totalAutres) }}</td>
        </tr>

        {{-- Totaux --}}
        <tr class="tot">
            <td colspan="4">TOTAUX</td>
            <td class="m">{{ $fmt($b['total_gain']) }}</td>
            <td class="m">{{ $fmt($b['total_retenu']) }}</td>
        </tr>
        <tr class="net">
            <td colspan="4" style="text-align:center;">NET À PAYER</td>
            <td class="m" colspan="2">{{ $fmt($netAPayer) }} F CFA</td>
        </tr>
        </tbody>
    </table>

    {{-- Charges patronales : à la charge de l'employeur, non déduites du net --}}
    @if (!empty($b['patronales']))
        <table class="bf-main" style="margin-top:8px;">
            <tr class="sec">
                <td style="text-align:left;">CHARGES PATRONALES <span style="font-weight:normal; font-size:9px;">(non déduites du net)</span></td>
                <td class="b">Base</td>
                <td class="t">Taux</td>
                <td class="m">Montant</td>
            </tr>
            @foreach ($b['patronales'] as $l)
                <tr class="pat">
                    <td>{{ $l['libelle'] }}</td>
                    <td class="b">{{ $l['base'] !== null ? $fmt($l['base']) : '' }}</td>
                    <td class="t">{{ $dec($l['taux']) }}</td>
                    <td class="m">{{ $fmt($l['montant']) }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    {{-- Identité du salarié --}}
    <table class="bf-box" style="margin-top:8px;">
        <tr><td style="width:28%;">Matricule</td><td><strong>{{ $ident['matricule'] ?? '' }}</strong></td><td style="width:28%;">Catégorie</td><td></td></tr>
        <tr><td>NOM</td><td><strong>{{ $ident['nom'] ?? '' }}</strong></td><td>Grade</td><td></td></tr>
        <tr><td>PRÉNOM</td><td><strong>{{ $ident['prenom'] ?? '' }}</strong></td><td>Échelon</td><td></td></tr>
        <tr><td>FONCTION</td><td><strong>{{ $ident['fonction'] ?? '' }}</strong></td><td>Numéro de compte</td><td></td></tr>
        <tr><td>N° CNSS</td><td>{{ $ident['numero_cnss'] ?? '' }}</td><td>BANQUE</td><td></td></tr>
    </table>

    <table class="bf-sign" style="margin-top:6px;">
        <tr>
            <td style="text-decoration:underline;">Signature employé</td>
            <td style="text-decoration:underline;">Directeur Général</td>
        </tr>
    </table>

    @if (empty($b['modele']))
        <div style="text-align:center; font-size:8.5px; color:#666; margin-top:6px;">Édité le {{ now()->format('d/m/Y') }}</div>
    @endif
</div>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        /* .header {
            background-color: #dc3545;
            color: white;
            padding: 10px;
        } */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            position: relative;
        }
        .logo {
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
        }
        .company-contact {
            font-size: 12px;
        }
        .list-group-item {
            border-bottom: 1px solid #ccc;
            padding: 5px;
        }

        .bg-success,
        .bg-danger,
        .bg-secondary {
            color: white;
            padding: 5px;
        }
    </style>
</head>
@foreach ($etatsPaies as $etatsPaie)
<body>
    <div class="header">
        <img src="{{ public_path('assets/img/logos/logo_smartschool.png') }}" height="60" alt="Logo" />
        {{-- <img src="{{ public_path('images/logo.png') }}" class="logo"> --}}
        <div class="company-name">{{ __('menu.Smart_School') }}</div>
        <div class="company-contact">Adresse : Niamey, Niger | Tel :+227 88156677</div>
    </div>

        <h2 class="header">BULLETIN DE PAIE – {{ $etatsPaies->first()->mois->mois ?? '' }} {{ $etatsPaies->first()->annee->annee ?? '' }}</h2>
        
        <p><div style="margin-top:-100px;">
            <div style="width:50%; float:left;">
                <p><strong>Matricule :</strong> {{ $etatsPaie->employer->matricule }}<br>
                    <strong>Nom prénom:</strong> {{ $etatsPaie->employer->nom }} {{ $etatsPaie->employer->prenom }}<br>
                </p>
            </div>
            <div style="width:20%; float:right;">
                    <strong>Adresse:</strong> {{ $etatsPaie->employer->adresse }}<br>
                    <strong>Poste:</strong> {{ $etatsPaie->employer->poste }}
                </p>
            </div>
        </div>
        </p>
        <br>
        <br>

        @php
        $gains = App\Models\PaieRH\BulletinCalculerModel::where('employer_id', $etatsPaie->employer_id)
            ->where('annee_id', $etatsPaie->annee_id)
            ->where('mois_id', $etatsPaie->mois_id)
            ->where('montant_gain', '!=', 0)
            ->get();
      
        $retenues = App\Models\PaieRH\BulletinCalculerModel::where('employer_id', $etatsPaie->employer_id)
            ->where('annee_id', $etatsPaie->annee_id)
            ->where('mois_id', $etatsPaie->mois_id)
            ->where('montant_retenu', '!=', 0)
            ->get();
    @endphp
    <p>
        <div class="list-group-item">
            RUBRIQUE
            <span style="float:right;">MONTANT</span>
        </div>
        <div class="bg-success">Gains</div>
        @foreach ($gains as $gain)
            <div class="list-group-item">
                {{ $gain->rubrique->rubrique }}
                <span style="float:right;">{{ number_format($gain->montant_gain, 0, ',', ' ') }}</span>
            </div>
        @endforeach
        <div style="float:right;" >Total gain : {{ number_format($etatsPaie->total_gain, 0, ',', ' ') }}</div>
<br>
        <div class="bg-danger">Retenues</div>
        @foreach ($retenues as $retenue)
            <div class="list-group-item">
                {{ $retenue->rubrique->rubrique }}
                <span style="float:right;">{{ number_format($retenue->montant_retenu, 0, ',', ' ') }}</span>
            </div>
        @endforeach

        <div  style="float:right;">Total retenue : {{ number_format($etatsPaie->total_retenu, 0, ',', ' ') }}</div>
<br>
        <div style="text-align:right; margin-top:20px;">
            <strong>Net à payer :</strong> <span
                class="text-primary">{{ number_format($etatsPaie->salaire_net, 0, ',', ' ') }} F CFA</span>
        </div>
    </p>
        

        <div style="margin-top:100px;">
            <div style="width:50%; float:left;">Employé</div>
            <div style="width:20%; float:right;">Responsable RH</div>
        </div>
    
</body>
@endforeach
</html>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            width: 58mm;
        }

        .card {
            width: 100%;
            box-sizing: border-box;
        }

        .card-header {
            border-bottom: 1px dashed #000;
            display: flex;
            align-items: center;
        }

        .card-header img {
            width: 50px;
            height: 50px;
            margin-right: 5px;
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-group-item {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            border-bottom: 1px dashed #ccc;
        }

        .footer-note {
            font-style: italic;
            text-align: center;
        }

        h6, h3, small {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    @include('livewire.print._entete_societe')
    <div class="card">
        <div class="card-header">
            <div>
                <h6>Reçu paiement scolarisation</h6>
                <small>imprimer le :{{ \Carbon\Carbon::now()->format('d/m/Y h:i') }}</small>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><strong>N° opé :</strong> <span>{{ $paiement_scolarisation->id }}</span></li>
                <li class="list-group-item"><strong>Matricule :</strong> <span>{{ $paiement_scolarisation->matricule }}</span></li>
                <li class="list-group-item"><strong>Nom :</strong> <span>{{ $paiement_scolarisation->inscription->preInscription->nom }}</span></li>
                <li class="list-group-item"><strong>Prénom :</strong> <span>{{ $paiement_scolarisation->inscription->preInscription->prenom }}</span></li>
                <li class="list-group-item"><strong>Niveau :</strong> <span>{{ $paiement_scolarisation->inscription->niveau->niveau }}</span></li>
                <li class="list-group-item"><strong>Filière :</strong> <span>{{ $paiement_scolarisation->inscription->filiere->filiere }}</span></li>
                <li class="list-group-item"><strong>Classe :</strong> <span>{{ $paiement_scolarisation->inscription->classe->classe }}</span></li>
                <li class="list-group-item"><strong>Payé :</strong> <span>{{ number_format($paiement_scolarisation->frais_payer, 0, ',', ' ') }} FCFA</span></li>
                @if ($paiement_scolarisation->remise != null)
                    <li class="list-group-item"><strong>Remise :</strong> <span>{{ number_format($paiement_scolarisation->remise, 0, ',', ' ') }} FCFA</span></li>
                @endif
                <li class="list-group-item"><strong>Mode :</strong> <span>{{ $paiement_scolarisation->mode_paiement }}</span></li>
                @if ($paiement_scolarisation->coordonnee_paiement != null)
                    <li class="list-group-item"><strong>N° {{ $paiement_scolarisation->mode_paiement }} :</strong> <span>{{ $paiement_scolarisation->coordonnee_paiement }}</span></li>
                @endif
            </ul>
            <p class="footer-note"><strong>NB :</strong> Aucun remboursement n'est possible.</p>
        </div>
    </div>
</body>

</html>

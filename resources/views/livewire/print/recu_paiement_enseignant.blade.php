



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 20px;
            margin: 0;
            padding: 20px;
        }
        .card {
            border: 1px solid #ccc;
            padding: 20px;
            width: 100%;
            max-width: 600px;
            margin: auto;
        }
        .card-header {
            border-bottom: 1px solid #ccc;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .card-header img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }
        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .list-group-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #eee;
        }
        .footer-note {
            margin-top: 20px;
            font-style: italic;
            color: #555;
        }
        h6, h3 {
            margin: 0;
        }
    </style>
</head>
<body>
    @include('livewire.print._entete_societe')
    <div class="card">
        <div class="card-header">
            <div>
                <h6>Reçu paiement enseignant</h6>
                <small class="text-muted">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</small>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><strong>Numéro opération : </strong>
                    <span>{{ $paiement_enseignant->id }}</span>
                </li>
                <li class="list-group-item"><strong>Matricule : </strong>
                    <span>{{ $paiement_enseignant->matricule }}</span>
                </li>
                <li class="list-group-item"><strong>Nom : </strong>
                    <span>{{ $paiement_enseignant->enseignant->nom }}</span>
                </li>
                <li class="list-group-item"><strong>Prénom : </strong>
                    <span>{{ $paiement_enseignant->enseignant->prenom }}</span>
                </li>
                </li>
                <li class="list-group-item"><strong>salaire payés : </strong>
                    <span>{{ number_format($paiement_enseignant->salaire_payer, 0, ',', ' ') }} FCFA</span>
                </li>
                <li class="list-group-item"><strong>Mois de paie : </strong>
                    <span>{{ $paiement_enseignant->mois->mois }} {{ $paiement_enseignant->annee->annee }} </span>
                </li>
                <li class="list-group-item"><strong>Mode paiement : </strong>
                    <span class="float-right">{{ $paiement_enseignant->mode_paiement }}</span>
                </li>
                @if($paiement_enseignant->coordonnee_paiement!= NULL)
                <li class="list-group-item">
                    <strong>Numero {{$paiement_enseignant->mode_paiement }} :</strong>
                    <span>{{$paiement_enseignant->coordonnee_paiement }} </span>
                </li>
                @endif
            </ul>
            <p class="footer-note"><strong>Souhait : </strong> Bon usage.</p>
        </div>
    </div>
</body>
</html>

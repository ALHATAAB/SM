
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu d'recu_abonnement</title>
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
                <h6>Reçu d'abonnement :</h6>
               <small>imprimer le :{{ \Carbon\Carbon::now()->format('d/m/Y h:i') }}</small>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><strong>N° opé :</strong> <span>{{ $recu_abonnement->id }}</span></li>
                <li class="list-group-item">
                    <strong>Matricule : </strong>
                    <span>{{ $recu_abonnement->matricule }}</span>
                </li>
                <li class="list-group-item"><strong>Nom </strong><span> {{ $recu_abonnement->preInscription->nom }}</span></li>
                <li class="list-group-item"><strong>Prénom </strong><span> {{ $recu_abonnement->preInscription->prenom }}</span></li>
                 {{--
                <li class="list-group-item"><strong>Date & lieu naissance</strong><span>{{ $recu_abonnement->prerecu_abonnement->date_naissance }} {{ $recu_abonnement->lieu_naissance }}</span></li>
                <li class="list-group-item"><strong>Téléphone</strong><span>{{ $recu_abonnement->prerecu_abonnement->telephone }}</span></li>
                --}}
                <li class="list-group-item">
                    <strong>Type abonnement :</strong>
                    <span>{{ $recu_abonnement->type_abonnement->type_abonnement }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Periode :</strong>
                    <span>{{ $recu_abonnement->nombre }} {{ $recu_abonnement->periode }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Date debut :</strong>
                    <span>{{ $recu_abonnement->date_debut }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Date fin :</strong>
                    <span>{{ $recu_abonnement->date_fin }}</span>
                </li>
                <li class="list-group-item"><strong>Frais abonnement : </strong>
                    <span class="float-right">{{ number_format($recu_abonnement->frais_abonnement + $recu_abonnement->remise , 0, ',', ' ') }}</span>
                </li>
                @if ($recu_abonnement->remise != null)
                    <li class="list-group-item"><strong>Remise :</strong> <span>{{ number_format($recu_abonnement->remise, 0, ',', ' ') }} FCFA</span></li>
                @endif
                <li class="list-group-item">
                    <strong>Frais payé :</strong>
                    <span>{{ number_format($recu_abonnement->frais_abonnement, 0, ',', ' ') }} FCFA</span>
                </li>
                
                <li class="list-group-item">
                    <strong>Mode paiement :</strong>
                    <span>{{$recu_abonnement->mode_paiement }} </span>
                </li>
                @if($recu_abonnement->coordonnee_paiement!= NULL)
                <li class="list-group-item">
                    <strong>Numero {{$recu_abonnement->mode_paiement }} :</strong>
                    <span>{{$recu_abonnement->coordonnee_paiement }} </span>
                </li>
                @endif
            </ul>
            
            <p class="footer-note"><strong>NB :</strong> Aucun remboursement n'est possible.</p>

           
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu d'recu_inscription</title>
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
                <h6>Reçu d'inscription :</h6>
                <small>imprimer le :{{ \Carbon\Carbon::now()->format('d/m/Y h:i') }}</small>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><strong>N° opé :</strong> <span>{{ $recu_inscription->id }}</span></li>
                
                <li class="list-group-item">
                    <strong>Matricule :</strong>
                    <span>{{ $recu_inscription->matricule }}</span>
                </li>
                {{-- 
                <li class="list-group-item"><strong>Nom</strong><span>{{ $recu_inscription->prerecu_inscription->nom }}</span></li>
                <li class="list-group-item"><strong>Prénom</strong><span>{{ $recu_inscription->prerecu_inscription->prenom }}</span></li>
                <li class="list-group-item"><strong>Date & lieu naissance</strong><span>{{ $recu_inscription->prerecu_inscription->date_naissance }} {{ $recu_inscription->lieu_naissance }}</span></li>
                <li class="list-group-item"><strong>Téléphone</strong><span>{{ $recu_inscription->prerecu_inscription->telephone }}</span></li>
                --}}
                <li class="list-group-item">
                    <strong>Niveau :</strong>
                    <span>{{ $recu_inscription->niveau->niveau }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Filière :</strong>
                    <span>{{ $recu_inscription->filiere->filiere }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Classe affecter :</strong>
                    <span>{{ $recu_inscription->classe->classe }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Frais d'inscription :</strong>
                    <span>{{ number_format($recu_inscription->frais_inscription, 0, ',', ' ') }} FCFA</span>
                </li>
                <li class="list-group-item">
                    <strong>Mode paiement :</strong>
                    <span>{{$recu_inscription->mode_paiement }} </span>
                </li>
                @if($recu_inscription->coordonnee_paiement!= NULL)
                <li class="list-group-item">
                    <strong>Numero {{$recu_inscription->mode_paiement }} :</strong>
                    <span>{{$recu_inscription->coordonnee_paiement }} </span>
                </li>
                @endif
            </ul>
            
            <p class="footer-note"><strong>NB :</strong> Aucun remboursement n'est possible.</p>

           
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Reçu d'inscription</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            margin: 5px; /* Réduction marge générale */
            padding: 5px;
            background-color: #fff;
        }

        .card {
            border: 1px solid #333;
            padding: 10px; /* Moins d’espace interne */
            width: 100%;
            max-width: 650px;
            margin: auto;
        }

        .card-header {
            border-bottom: 1px solid #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .card-header img {
            width: 70px; /* Agrandir logo */
            height: 70px;
            margin-right: 10px;
        }

        .card-header h6 {
            margin: 0;
            font-size: 18px;
        }

        .card-header small {
            display: block;
            font-size: 12px;
            color: #555;
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-group-item {
            display: flex;
            justify-content: space-between;
            padding: 3px 0; /* Réduire espace entre lignes */
            border-bottom: 1px dotted #999;
        }

        .list-group-item strong {
            color: #000;
        }

        .footer-note {
            margin-top: 8px;
            font-style: italic;
            font-size: 11px;
            color: #555;
            text-align: center;
        }
    </style>
</head>

<body>
    @include('livewire.print._entete_societe')
    <div class="card">
        <div class="card-header">
            <div>
                <h6>Reçu d'inscription</h6>
                <small>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</small>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Numéro opération :</strong>
                    <span>{{ $recu_preinscription->id }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Matricule :</strong>
                    <span>{{ $recu_preinscription->matricule }}</span>
                </li>
                <li class="list-group-item"><strong>Nom </strong>
                                    <span class=" float-right">{{ $recu_preinscription->nom }}</span>
                                </li>
                                <li class="list-group-item"><strong>Prenom </strong>
                                    <span class="float-right">{{ $recu_preinscription->prenom }}</span>
                                </li>

                                <li class="list-group-item"><strong>Date et lieu naissance: </strong>
                                    <span class=" float-right">{{ $recu_preinscription->date_naissance }}
                                        {{ $recu_preinscription->lieu_naissance }}</span>
                                </li>
                                <li class="list-group-item"><strong>Telephone : </strong>
                                    <span class="float-right">{{ $recu_preinscription->telephone }}</span>
                                </li>
            </ul>

            <p class="footer-note">
                <strong>NB :</strong> Aucun remboursement n'est possible.
            </p>
        </div>
    </div>
</body>

</html>

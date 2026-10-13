<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Étudiants</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header .logo {
            width: 80px;
            height: auto;
        }
        .header .institution {
            flex: 1;
            text-align: center;
        }
        .institution h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .institution p {
            margin: 2px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

    @include('livewire.print._entete_societe')

    <h3 style="text-align:center;">LISTE DES ÉTUDIANTS INSCRITS EN CLASSE DE : <strong> {{ $listeClasseFirst->classe->classe }}</strong></h3>

    @if (count($listeClasses) > 0)
    <table>
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Téléphone</th>
                <th>Niveau</th>
                <th>Filière</th>
                <th>Année</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listeClasses as $etudiant)
            <tr>
                <td>{{ $etudiant->matricule }}</td>
                <td>{{ $etudiant->preInscription->nom }}</td>
                <td>{{ $etudiant->preInscription->prenom }}</td>
                <td>{{ $etudiant->preInscription->telephone }}</td>
                <td>{{ $etudiant->niveau->niveau }}</td>
                <td>{{ $etudiant->filiere->filiere }}</td>
                <td>{{ $etudiant->annee->annee }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center;">Aucune donnée disponible.</p>
    @endif

</body>
</html>

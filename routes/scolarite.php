<?php

use App\Livewire\Scolarite\Abonnement;
use App\Livewire\Scolarite\AbsenceApprenant;
use App\Livewire\Scolarite\AnneeScolaire;
use App\Livewire\Scolarite\ClasseEtude;
use App\Livewire\Scolarite\CoefficientMatiere;
use App\Livewire\Scolarite\DetailAchat;
use App\Livewire\Scolarite\DetailEnseignant;
use App\Livewire\Scolarite\DetailInscrit;
use App\Livewire\Scolarite\DetailVente;
use App\Livewire\Scolarite\EmargementEnseignant;
use App\Livewire\Scolarite\Enseignant;
use App\Livewire\Scolarite\FiliereEtude;
use App\Livewire\Scolarite\FraisAbonnement;
use App\Livewire\Scolarite\FraisEtude;
use App\Livewire\Scolarite\GestionPaieEnseignant;
use App\Livewire\Scolarite\Inscription;
use App\Livewire\Scolarite\InscriptionEdite;
use App\Livewire\Scolarite\InventaireStockLivre;
use App\Livewire\Scolarite\ListeAbsenceApprenant;
use App\Livewire\Scolarite\ListeAbsenceEnseignant;
use App\Livewire\Scolarite\ListeInscrit;
use App\Livewire\Scolarite\ListePresenceEnseignant;
use App\Livewire\Scolarite\Livre;
use App\Livewire\Scolarite\LivreAchete;
use App\Livewire\Scolarite\LivreVendu;
use App\Livewire\Scolarite\MatiereEtude;
use App\Livewire\Scolarite\MouvementStockLivre;
use App\Livewire\Scolarite\NiveauEtude;
use App\Livewire\Scolarite\NoteApprenant;
use App\Livewire\Scolarite\NouvelAbonnement;
use App\Livewire\Scolarite\NouvelAchat;
use App\Livewire\Scolarite\NouvelleVente;
use App\Livewire\Scolarite\Preinscription;
use App\Livewire\Scolarite\RecherchePreinscrit;
use App\Livewire\Scolarite\ReglementFraisScolaire;
use App\Livewire\Scolarite\ReglementPaieEnseignant;
use App\Livewire\Scolarite\SalleEtude;
use App\Livewire\Scolarite\Trimestre;
use App\Livewire\Scolarite\TypeAbonnement;
use Illuminate\Support\Facades\Route;








Route::get('/annee/scolaire', AnneeScolaire::class);
Route::get('/trimestre', Trimestre::class);
Route::get('/salle/etude', SalleEtude::class);
Route::get('/classe/etude', ClasseEtude::class);
Route::get('/niveau/etude', NiveauEtude::class);
Route::get('/filiere/etude', FiliereEtude::class);
Route::get('/matiere/etude', MatiereEtude::class);
Route::get('/frais/etude', FraisEtude::class);
Route::get('/coefficient/matiere', CoefficientMatiere::class);

Route::get('/preinscription', Preinscription::class);
Route::get('/recherche/preinscrit', RecherchePreinscrit::class);
Route::get('/inscription', Inscription::class);
Route::get('/editer/inscrit/{id}', InscriptionEdite::class);
Route::get('/reglement/frais/scolaire', ReglementFraisScolaire::class);
Route::get('/liste/inscrit', ListeInscrit::class);
Route::get('/detail/inscrit/{id}', DetailInscrit::class);
Route::get('/absence/apprenant', AbsenceApprenant::class);
Route::get('/note/apprenant', NoteApprenant::class);
Route::get('/liste/absence/apprenant', ListeAbsenceApprenant::class);

Route::get('/liste/enseignant', Enseignant::class);
Route::get('/detail/enseignant/{id}', DetailEnseignant::class);
Route::get('/liste/presence/enseignant', ListePresenceEnseignant::class);
Route::get('/liste/absence/enseignant', ListeAbsenceEnseignant::class);
Route::get('/emargement/enseignant', EmargementEnseignant::class);
Route::get('/gestionnaire/paie/enseignant', GestionPaieEnseignant::class);
Route::get('reglement/paie/enseignant',ReglementPaieEnseignant::class);



Route::get('/type/abonnement', TypeAbonnement::class);
Route::get('/frais/abonnement', FraisAbonnement::class);
Route::get('/nouvel/abonnement', NouvelAbonnement::class);
Route::get('abonnement', Abonnement::class);

Route::get('livre', Livre::class);
Route::get('/livre/acheter', LivreAchete::class);
Route::get('inventaire/livre', InventaireStockLivre::class);
Route::get('mouvement/livre', MouvementStockLivre::class);
Route::get('nouvel/achat', NouvelAchat::class);
Route::get('achat/livre/detail/{id}', DetailAchat::class);


Route::get('/livre/vendu', LivreVendu::class);
Route::get('nouvel/vente', NouvelleVente::class);
Route::get('vente/livre/detail/{id}', DetailVente::class);




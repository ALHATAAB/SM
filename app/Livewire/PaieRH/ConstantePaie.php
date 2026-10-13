<?php

namespace App\Livewire\PaieRH;

use App\Models\PaieRH\ConstantePaieModel;
use App\Services\FormuleService;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Constantes de paie utilisables dans les formules des rubriques, rangées par catégorie :
 * tranche (bornes min/max), calcul (taux, coefficient, montant fixe), test (seuil de condition), autre.
 */
class ConstantePaie extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    /** true quand le composant est affiché dans « Paramètres paie » (sans mise en page de page). */
    public bool $integre = false;

    public $recherche;
    public $filtre_categorie = '';

    public $constante_id, $code, $libelle, $categorie = 'calcul', $valeur, $valeur_max, $description;

    public function render()
    {
        $constantes = ConstantePaieModel::query()
            ->when($this->recherche, fn ($q) => $q->where(fn ($w) => $w
                ->where('code', 'like', '%'.$this->recherche.'%')
                ->orWhere('libelle', 'like', '%'.$this->recherche.'%')))
            ->when($this->filtre_categorie, fn ($q) => $q->where('categorie', $this->filtre_categorie))
            ->orderBy('categorie')->orderBy('code')
            ->paginate(10);

        return view($this->integre ? 'livewire.paie-r-h.constante-paie-integre' : 'livewire.paie-r-h.constante-paie', ['constantes' => $constantes]);
    }

    public function updatingRecherche() { $this->resetPage(); }
    public function updatingFiltreCategorie() { $this->resetPage(); }

    protected function regles(?int $id = null): array
    {
        return [
            'code' => [
                'required', 'max:50', 'regex:/^[A-Za-z][A-Za-z0-9_]*$/',
                Rule::unique('constante_paie_models', 'code')->ignore($id),
                // Variables de la ligne (BASE, TAUX, NOMBRE, ANCIENNETE...) et paramètres de paie (CNSS_, ITS_, FSSP_...) : réservés
                function ($attribut, $valeur, $echec) {
                    if (app(FormuleService::class)->codeReserve((string) $valeur)) {
                        $echec('Ce code est réservé (variable de formule ou paramètre de paie automatique) : choisissez un autre code.');
                    }
                },
            ],
            'libelle'    => 'required|max:255',
            'categorie'  => 'required|in:tranche,calcul,test,autre',
            'valeur'     => 'required|numeric',
            'valeur_max' => 'nullable|numeric|gte:valeur',
            'description'=> 'nullable|max:255',
        ];
    }

    protected $messages = [
        'code.regex'   => 'Le code commence par une lettre et ne contient que des lettres, chiffres et _ (sans espace).',
        'code.unique'  => 'Ce code existe déjà.',
        'valeur_max.gte' => 'La borne maximale doit être supérieure ou égale à la valeur.',
    ];

    public function addConstante()
    {
        $this->validate($this->regles());

        ConstantePaieModel::create($this->donnees());
        $this->resetInput();

        return redirect('/parametre/paieRH')->with('success', 'Opération effectuée avec succès');
    }

    public function editConstante($id)
    {
        $c = ConstantePaieModel::findOrFail($id);
        $this->resetErrorBag();
        $this->constante_id = $c->id;
        $this->code         = $c->code;
        $this->libelle      = $c->libelle;
        $this->categorie    = $c->categorie;
        $this->valeur       = (float) $c->valeur;
        $this->valeur_max   = $c->valeur_max !== null ? (float) $c->valeur_max : null;
        $this->description  = $c->description;
    }

    public function updateConstante()
    {
        $c = ConstantePaieModel::findOrFail($this->constante_id);
        $this->validate($this->regles($c->id));

        $c->update($this->donnees());

        return redirect('/parametre/paieRH')->with('info', 'Modification effectuée avec succès');
    }

    protected function donnees(): array
    {
        return [
            'code'        => strtoupper(trim($this->code)),
            'libelle'     => trim($this->libelle),
            'categorie'   => $this->categorie,
            'valeur'      => $this->valeur,
            // la borne maximale n'a de sens que pour une tranche
            'valeur_max'  => $this->categorie === 'tranche' && $this->valeur_max !== null && $this->valeur_max !== '' ? $this->valeur_max : null,
            'description' => $this->description ?: null,
        ];
    }

    public function resetInput()
    {
        $this->reset(['constante_id', 'code', 'libelle', 'valeur', 'valeur_max', 'description']);
        $this->categorie = 'calcul';
        $this->resetErrorBag();
    }
}

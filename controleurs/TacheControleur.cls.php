<?php
class TacheControleur extends Controleur
{
    function __construct($modele, $module, $action, $params)
    {
        if(!isset($_SESSION['utilisateur'])) {
            Utilitaire::nouvelleRoute('utilisateur/index');
        }
        
        parent::__construct($modele, $module, $action, $params);
    }

    /**
     * Méthode invoquée par défaut si aucune action n'est indiquée
     */
    public function index()
    {
        // Par défaut on affiche les tâches
        $this->gabarit->affecterActionParDefaut('tout');
        $this->tout();

    }

    public function tout() {
        
    }

}

<?php
class Controleur 
{
    protected $modele;
    protected $gabarit;
    protected $params;

    function __construct($modele, $module, $action, $params)
    {
        if(class_exists($modele)) {
            $this->modele = new $modele();
        }
        $this->gabarit = new HtmlGabarit($module, $action);
        $this->gabarit->affecter('page', $module);
        $this->params = $params;
    }

    function __destruct()
    {
       $this->gabarit->genererVue(); 
    }

    // Action par défaut : donc méthode obligatoire
    public function index() 
    {

    }
}
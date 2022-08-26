<?php
class UtilisateurControleur extends Controleur
{
    function __construct($modele, $module, $action, $params)
    {
        // Si l'utilisateur est connecté on le dirige directement dans la page 'catégories'
        if(isset($_SESSION['utilisateur'])) {
            Utilitaire::nouvelleRoute('tache/tout');
        }
        
        parent::__construct($modele, $module, $action, $params);
    }

    /**
     * Méthode invoquée par défaut si aucune action n'est indiquée
     */
    public function index()
    {
        // Par défaut on affiche le formulaire de connexion  : aucune autre action 
        // n'est requise pour le moment

    }

    public function nouveau()
    {
        // On affiche le formulaire de création de compte : aucune autre action 
        // n'est requise pour le moment
        
    }

    /**
     * Vérifier la connexion d'un utilisateur
     */
    public function connexion()
    {
        $courriel = $_POST['uti_courriel'];
        $mdp = $_POST['uti_mdp'];

        $utilisateur = $this->modele->un($courriel);

        $erreur = false;
        if(!$utilisateur || !password_verify($mdp, $utilisateur->uti_mdp)) {
            $erreur = "Combinaison courriel/mot de passe erronée";
        }
        else if($utilisateur->uti_confirmation != '') {
            $erreur = "Compte non confirmé : vérifiez vos courriels";
        }

        if(!$erreur) {
            // Sauvegarder l'état de connexion
            $_SESSION['utilisateur'] = $utilisateur;
            // Rediriger vers categorie/tout
            Utilitaire::nouvelleRoute('tache/tout');
        }
        else {
            $this->gabarit->affecter('erreur', $erreur);
            $this->gabarit->affecterActionParDefaut('index');
            $this->index();
        }
    }

    /**
     * Supprimer la connexion d'un utilisateur (en détruisant la variable de session associée)
     */
    public function deconnexion()
    {
        unset($_SESSION['utilisateur']);
        Utilitaire::nouvelleRoute('utilisateur/index');
    }

    /**
     * Ajouter un utilisateur
     */
    public function ajouter()
    {
        $res = $this->modele->ajouter($_POST);
        $this->envoyerMessageConfirmationCompte($res['courriel'],$res['cc']);
        Utilitaire::nouvelleRoute('utilisateur/index');
    }

    /**
     * Code rudimentaire pour confirmer l'adresse courriel de l'utilisateur
     * (En particulier, on suppose que le CC est vraiment UNIQUE ! Sinon, il faut 
     * travailler un peu plus en comparant courriel et CC : mais il faut trouver
     * alors un moyen de transmettre l'adresse courriel dans le lien envoyé par
     * courriel sans compromettre cette adresse pour vos utilisateurs... à réfléchir ;-))
     */
    public function confirmer() {
        $cc = $this->params['cc'];
        $this->modele->confirmer($cc);
        $this->gabarit->affecterActionParDefaut('index');
        $this->index();
    }

    /**
     * Envoyer un message de confirmation
     * @param string $courriel Adresse courriel de l'utilisateur
     * @param string $cc Code de confirmation à joindre dans le lien de ce message
     */
    private function envoyerMessageConfirmationCompte($courriel, $cc) {
        $sujet = "Confirmation de votre compte Memo";
        $message = "
        <html>
            <head>
                <title>Confirmation de votre compte Memo</title>
            </head>
            <body>
                <p>Votre compte est presque prêt !</p>
                <p>
                    Avant de pouvoir l'utiliser, vous devez confirmer votre 
                    adresse courriel ; il suffit de cliquer le lien suivant :
                </p>
                <p>
                    <a href='".BASE_SERVEUR."utilisateur/confirmer/cc=".$cc."'>".BASE_SERVEUR."utilisateur/confirmer/cc=".$cc."</a>
                </p>
            </body>
        </html>
        ";
        $entetes[] = 'MIME-Version: 1.0';
        $entetes[] = 'Content-type: text/html; charset=utf-8';
        $entetes[] = 'From: Équipe Memo <admin@memo.com>';
        // Localement, par défaut vous n'avez pas de serveur SMTP installé. Il y a
        // plusieurs solutions pour simuler ou configurer un serveur de mail, mais
        // le plus simple pour illustrer cet exemple est d'installer un utilitaire
        // qui simule SMTP comme "Papercut-SMTP" : https://github.com/ChangemakerStudios/Papercut-SMTP
        mail($courriel, $sujet, $message, implode("\r\n", $entetes));
    }
}

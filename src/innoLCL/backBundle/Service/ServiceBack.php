<?php

namespace innoLCL\backBundle\Service;


class ServiceBack {
    protected $viewModerateur;
    protected $viewLecteur;
    protected $viewValidateur;
    protected $viewSelectionneur;
    
    public function __construct()
    {
    $this->viewModerateur = array("all");
    $this->viewLecteur = array("maybe","refused","validated");
    $this->viewValidateur = array("maybe","refused","validated");
    $this->viewSelectionneur = array("validated"); 
    }
    
    // Redirige les utilisateurs avec plus de 2 roles à l'authentification
    public function isUserWithTooManyRole($roles) {
        if(sizeof($roles) > 2) 
        { 
            return true;    
        }
        else 
        {
            return false;
        }
             
    }
    
    //retourne le role admin de l'user, supprime ROLE_USER
    public function getAdminRole($roles) {
        if(in_array("ROLE_MODERATEUR",$roles)) { return "ROLE_MODERATEUR";}
        if(in_array("ROLE_LECTEUR",$roles)) { return "ROLE_LECTEUR";}
        if(in_array("ROLE_VALIDATEUR",$roles)) { return "ROLE_VALIDATEUR";}
        if(in_array("ROLE_SELECTIONNEUR",$roles)) { return "ROLE_SELECTIONNEUR";}
        if(in_array("ROLE_STATS",$roles)) { return "ROLE_STATS";}
    }
    
    public function canRoleViewThisList($viewstatuts,$userAdminRole) {
        if($userAdminRole == "ROLE_MODERATEUR" && in_array($viewstatuts,$this->viewModerateur)) { return true;}
        if($userAdminRole == "ROLE_LECTEUR" && in_array($viewstatuts,$this->viewModerateur)) { return true;}
        if($userAdminRole == "ROLE_VALIDATEUR" && in_array($viewstatuts,$this->viewModerateur)) { return true;}
        if($userAdminRole == "ROLE_SELECTIONNEUR" && in_array($viewstatuts,$this->viewSelectionneur)) { return true;}
        return false;
    }
    
    public function defaultViewOfRole($role) 
    {
        if($role == "ROLE_MODERATEUR") { return "all";}
        if($role == "ROLE_LECTEUR") { return "maybe";}
        if($role == "ROLE_VALIDATEUR") { return "maybe";}
        if($role == "ROLE_SELECTIONNEUR") { return "validated";}
    }
    
    public function canUserModeratedIdeaOnly($userAdminRole) {
        if($userAdminRole == "ROLE_VALIDATEUR") { return true;}
        return false;        
    }
    
     public function canUserValidatedIdeaOnly($userAdminRole) {
        if($userAdminRole == "ROLE_SELECTIONNEUR") { return true;}
        return false;
     }
     
     public function canUserEditThisIdea($role,$ideastatut,$ideavalidated) {
         if($role == "ROLE_MODERATEUR" && $ideastatut != "notmoderated") { return false;}
         if($role == "ROLE_LECTEUR" && 1 == 0) { return false;} //TO DO a corriger ne devrait pas pouvoir accèder à des idées dont il n'y a pas de review pour la version actuelle
         if($role == "ROLE_VALIDATEUR" && 1 == 0) { return false;} //TO DO a corriger ne devrait pas pouvoir accèder à des idées dont il n'y a pas de review pour la version actuelle
         if($role == "ROLE_SELECTIONNEUR" && $ideavalidated == false) { return false;}
         return true;
     }
}

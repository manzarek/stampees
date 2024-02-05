let eMessageErreurConnexion = document.getElementById('messageErreurConnexion');
let eModaleConnexion        = document.getElementById('modaleConnexion');
let eUtilisateur            = document.getElementById('utilisateur');
let eConnecter              = document.getElementById('connecter'); 
let eDeconnecter            = document.getElementById('deconnecter'); 
let eCreerEnchere           = document.getElementById('creerEnchere');
let eVoirFavoris            = document.getElementById('voirFavoris');
let eFaireMise              = document.getElementById('faireMise');


eConnecter.onclick    = afficherFenetreModale;
frmConnexion.onsubmit = traiterFormulaireConnection;
eDeconnecter.onclick  = deconnecter;
eFaireMise.addEventListener('click',traiterMise.bind(this));

/**
 * Affichage de la fenêtre modale
 */
function afficherFenetreModale() {
  eMessageErreurConnexion.innerHTML = "&nbsp;";
  document.getElementById('modaleConnexion').showModal();
}

/**
 * Traitement du formulaire dans la fenêtre modale
 * @param {Event} event
 */
function traiterFormulaireConnection(event) {
  event.preventDefault();
  let fd = new FormData(frmConnexion);
  fetch('connecter', {method: 'post', body: fd})
  .then (reponse => reponse.json())
  .then (utilisateur => {
    if (!utilisateur) {
      eMessageErreurConnexion.innerHTML = "Courriel ou mot de passe incorrect.";
    } else {
      eUtilisateur.innerHTML = "Bienvenue, " + utilisateur.prenom + " " + utilisateur.nom;
      eConnecter  .classList.add('hidden-menu');
      eDeconnecter.classList.remove('hidden-menu');
      eCreerEnchere.classList.remove('hidden-menu');
      eVoirFavoris.classList.remove('hidden-menu');
      eModaleConnexion.close();
    }
  })
  .catch(() => {
    eMessageErreurConnexion.innerHTML = "Problème technique sur le serveur.";
  });
}

/**
 * Déconnecter
 */
function deconnecter() {
  fetch('deconnecter')
  .then (reponse => reponse.json())
  .then (codeRetour => {
    if (codeRetour) {
      eUtilisateur.innerHTML = "";
      eDeconnecter.classList.add('hidden-menu');
      eConnecter  .classList.remove('hidden-menu');
      eCreerEnchere.classList.add('hidden-menu');
      eVoirFavoris.classList.add('hidden-menu');
    }
  });
}

/**
 * Confirmation de mise
 * @param {Event} evt
 */
function faireMise(evt) {
  evt.preventDefault();
  
}
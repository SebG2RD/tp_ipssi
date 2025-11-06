// @ts-nocheck

const MESSAGES_ERREUR = {
  requis: "Ce champ est requis",
  email: "Veuillez entrer une adresse email valide",
  emailLong: "L'email est trop long",
  minLength: "Ce champ doit contenir au moins 2 caractères",
  nomLong: "Le nom est trop long",
  prenomLong: "Le prénom est trop long",
};

const inputs = document.querySelectorAll("input");
const formulaire = document.querySelector("form");

// Validation d'un champ du formulaire
function validerChamp(input) {
  const valeur = input.value.trim();

  // Vérifier si le champ est vide
  if (!valeur) {
    return { isValid: false, message: MESSAGES_ERREUR.requis };
  }

  // Validation pour l'email
  if (input.id === "email") {
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valeur)) {
      return { isValid: false, message: MESSAGES_ERREUR.email };
    }
    if (valeur.length > 255) {
      return { isValid: false, message: MESSAGES_ERREUR.emailLong };
    }
  }

  // Validation pour le nom
  if (input.id === "nom") {
    if (valeur.length < 2) {
      return { isValid: false, message: MESSAGES_ERREUR.minLength };
    }
    if (valeur.length > 100) {
      return { isValid: false, message: MESSAGES_ERREUR.nomLong };
    }
  }

  // Validation pour le prénom
  if (input.id === "prenom") {
    if (valeur.length < 2) {
      return { isValid: false, message: MESSAGES_ERREUR.minLength };
    }
    if (valeur.length > 100) {
      return { isValid: false, message: MESSAGES_ERREUR.prenomLong };
    }
  }

  // Si toutes les validations passent
  return { isValid: true, message: "" };
}

// Affichage de l'état de validation
function afficherValidation(input, isValid, message) {
  const errorSpan = document.getElementById(input.id + "-error");
  input.setCustomValidity(isValid ? "" : message);
  // Utilisation des classes Bootstrap pour la validation
  input.classList.toggle("is-valid", isValid);
  input.classList.toggle("is-invalid", !isValid);
  if (errorSpan) {
    errorSpan.textContent = isValid ? "" : message;
    // Bootstrap affiche automatiquement invalid-feedback quand is-invalid est présent
  }
}

// Validation et affichage
function validerEtAfficher(input) {
  const resultat = validerChamp(input);
  afficherValidation(input, resultat.isValid, resultat.message);
}

// Écouteurs d'événements
inputs.forEach((input) => {
  input.addEventListener("input", () => validerEtAfficher(input));
  input.addEventListener("blur", () => validerEtAfficher(input));
});

// Afficher un message avec Bootstrap
function afficherMessage(message, type = "success") {
  let msgDiv = document.getElementById("message-formulaire");
  if (!msgDiv) {
    msgDiv = document.createElement("div");
    msgDiv.id = "message-formulaire";
    formulaire.insertAdjacentElement("afterend", msgDiv);
  }
  // Utilisation des classes Bootstrap pour les alertes
  msgDiv.className = `alert alert-${
    type === "success" ? "success" : "danger"
  } alert-dismissible fade show`;
  msgDiv.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;
  msgDiv.scrollIntoView({ behavior: "smooth", block: "nearest" });
}

// Afficher les erreurs serveur
function afficherErreursServeur(erreurs) {
  Object.keys(erreurs).forEach((nomChamp) => {
    const input = document.querySelector(`[name="${nomChamp}"]`);
    if (input) {
      const errorSpan = document.getElementById(input.id + "-error");
      if (errorSpan) {
        errorSpan.textContent = erreurs[nomChamp];
        // Utilisation des classes Bootstrap
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
      }
    }
  });
}

// Réinitialiser les erreurs
function reinitialiser() {
  inputs.forEach((input) => {
    // Utilisation des classes Bootstrap
    input.classList.remove("is-valid", "is-invalid");
    const errorSpan = document.getElementById(input.id + "-error");
    if (errorSpan) {
      errorSpan.textContent = "";
    }
  });
}

// Soumission du formulaire
if (formulaire) {
  formulaire.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Validation
    let valide = true;
    inputs.forEach((input) => {
      const resultat = validerChamp(input);
      afficherValidation(input, resultat.isValid, resultat.message);
      if (!resultat.isValid) valide = false;
    });
    if (!valide) return;

    // Envoi au serveur
    try {
      document
        .getElementById("message-formulaire")
        ?.style.setProperty("display", "none");
      reinitialiser();

      const formData = new FormData(formulaire);
      const reponse = await fetch("inscription.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(formData).toString(),
      });

      if (!reponse.ok) throw new Error(`Erreur serveur: ${reponse.status}`);

      const donneesJson = await reponse.json();

      if (donneesJson.success) {
        afficherMessage(
          donneesJson.message || "Inscription réussie !",
          "success"
        );
        formulaire.reset();
        reinitialiser();
      } else {
        afficherMessage(
          donneesJson.message || "Une erreur est survenue",
          "error"
        );
        if (donneesJson.erreurs) afficherErreursServeur(donneesJson.erreurs);
      }
    } catch (erreur) {
      console.error("Erreur:", erreur);
      let msg = "Erreur de connexion. Veuillez réessayer.";
      if (erreur.message.includes("Erreur serveur"))
        msg = "Erreur serveur. Contactez l'administrateur.";
      else if (erreur.message.includes("JSON"))
        msg = "Réponse serveur invalide.";
      afficherMessage(msg, "error");
    }
  });
}

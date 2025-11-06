<?php
// Inclure le header
require_once __DIR__ . '/includes/header.php';
?>

<main>
    <div class="container-custom">
        <h1 class="text-center mb-4 text-gradient">Inscription à une Newsletter</h1>
        <form action="inscription.php" method="post" name="form_inscription">
            <!-- Champ Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="required">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div id="email-error" class="invalid-feedback"></div>
            </div>

            <!-- Champ Nom -->
            <div class="mb-3">
                <label for="nom" class="form-label">Nom <span class="required">*</span></label>
                <input type="text" class="form-control" id="nom" name="nom" required>
                <div id="nom-error" class="invalid-feedback"></div>
            </div>

            <!-- Champ Prénom -->
            <div class="mb-3">
                <label for="prenom" class="form-label">Prenom <span class="required">*</span></label>
                <input type="text" class="form-control" id="prenom" name="prenom" required>
                <div id="prenom-error" class="invalid-feedback"></div>
            </div>

            <!-- Bouton de soumission -->
            <button type="submit" class="btn btn-primary w-100 mt-3">S'inscrire</button>
        </form>
    </div>
</main>

<?php
// Inclure le footer
require_once __DIR__ . '/includes/footer.php';
?>
<?php
require_once '../config.php'; 

$message = ""; 

if (isset($_POST['inscription'])) {
    $prenom      = trim($_POST['prenom'] ?? '');
    $nom         = trim($_POST['nom'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $tel         = trim($_POST['telephone'] ?? '');
    $mdp         = $_POST['password'] ?? '';
    $mdp_confirm = $_POST['confirm_password'] ?? '';

    // --- VALIDATION SERVEUR ---
    if (empty($prenom) || empty($nom)) {
        $message = "Nom et prénom requis.";
    } elseif (strlen($prenom) < 2 || strlen($nom) < 2) {
        $message = "Nom et prénom doivent contenir au moins 2 caractères.";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s\-']+$/u", $prenom) || !preg_match("/^[a-zA-ZÀ-ÿ\s\-']+$/u", $nom)) {
        $message = "Nom et prénom ne doivent contenir que des lettres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresse email invalide.";
    } elseif (!preg_match("/^[0-9\s\+\-\(\)]{8,20}$/", $tel)) {
        $message = "Numéro de téléphone invalide.";
    } elseif (strlen($mdp) < 8) {
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!preg_match("/[A-Z]/", $mdp)) {
        $message = "Le mot de passe doit contenir au moins une majuscule.";
    } elseif (!preg_match("/[a-z]/", $mdp)) {
        $message = "Le mot de passe doit contenir au moins une minuscule.";
    } elseif (!preg_match("/[0-9]/", $mdp)) {
        $message = "Le mot de passe doit contenir au moins un chiffre.";
    } elseif (!preg_match("/[\W_]/", $mdp)) {
        $message = "Le mot de passe doit contenir au moins un caractère spécial.";
    } elseif ($mdp !== $mdp_confirm) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
        try {
            $db->beginTransaction();
            $sql1 = "INSERT INTO login (login, password) VALUES (?, ?)";
            $stmt1 = $db->prepare($sql1);
            $stmt1->execute([$email, $mdp_hash]);
            $id_login_cree = $db->lastInsertId();

            $sql2 = "INSERT INTO client (nom, prenom, email, telephone, id_login) VALUES (?, ?, ?, ?, ?)";
            $stmt2 = $db->prepare($sql2);
            $stmt2->execute([$nom, $prenom, $email, $tel, $id_login_cree]);

            $db->commit();
            $message = "Compte créé avec succès !";
        } catch (Exception $e) {
            $db->rollBack();
            $message = ($e->getCode() == 23000) ? "Erreur : cet identifiant existe déjà." : "Erreur système : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Inscription</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg">

    <?php include 'composants_client/navbar.php'; ?>

    <main class="auth-page">
        <!-- Colonne gauche : formulaire -->
        <div class="auth-form-col">
            <div class="auth-content">
                <h2>INSCRIPTION</h2>
                <div class="title-bar"></div>

                <?php if ($message != ""): ?>
                    <div class="form-msg"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form action="inscription.php" method="POST" id="formInscription" novalidate>
                    <div class="grid grid-2" style="gap:1rem;">
                        <div class="form-group">
                            <label class="form-label">Prénom</label>
                            <input type="text" 
                                   name="prenom" 
                                   id="prenom"
                                   placeholder="JEAN" 
                                   required 
                                   minlength="2"
                                   class="form-input"
                                   autocomplete="given-name">
                            <span class="form-error" id="error-prenom"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nom</label>
                            <input type="text" 
                                   name="nom" 
                                   id="nom"
                                   placeholder="DUPONT" 
                                   required 
                                   minlength="2"
                                   class="form-input"
                                   autocomplete="family-name">
                            <span class="form-error" id="error-nom"></span>
                        </div>
                    </div>

                    <div class="grid grid-2" style="gap:1rem;">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" 
                                   name="email" 
                                   id="email"
                                   placeholder="VOTRE@EMAIL.COM" 
                                   required 
                                   class="form-input"
                                   autocomplete="email">
                            <span class="form-error" id="error-email"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" 
                                   name="telephone" 
                                   id="telephone"
                                   placeholder="5000 0000" 
                                   required 
                                   class="form-input"
                                   autocomplete="tel">
                            <span class="form-error" id="error-telephone"></span>
                        </div>
                    </div>

                    <div class="grid grid-2" style="gap:1rem;">
                        <div class="form-group">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   placeholder="••••••••" 
                                   required 
                                   minlength="8"
                                   class="form-input"
                                   autocomplete="new-password">
                            <span class="form-error" id="error-password"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirmation</label>
                            <input type="password" 
                                   name="confirm_password" 
                                   id="confirm_password"
                                   placeholder="••••••••" 
                                   required 
                                   minlength="8"
                                   class="form-input"
                                   autocomplete="new-password">
                            <span class="form-error" id="error-confirm_password"></span>
                        </div>
                    </div>

                    <div class="auth-actions">
                        <button type="submit" name="inscription" class="btn btn-primary btn-block">
                            Créer un profil membre
                        </button>
                        <a href="connexion.php" class="auth-alt-link">
                            Déjà membre ? Se connecter
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Colonne droite : image -->
        <div class="auth-image-col">
            <img src="<?= ASSETS_URL ?>/hero/connexionimg.jpg" alt="Ferrari">
        </div>
    </main>

    <script>
        // =========================================================
        // VALIDATION CLIENT — Inscription
        // Mode : blur (par champ) + input (efface erreur) + submit
        // =========================================================

        // --- Règles de validation par champ ---
        const rulesInscription = {
            prenom: (v) => {
                if (!v.trim()) return "Prénom requis.";
                if (v.trim().length < 2) return "Prénom trop court (2 min).";
                if (!/^[a-zA-ZÀ-ÿ\s\-']+$/.test(v.trim())) return "Lettres uniquement.";
                return "";
            },
            nom: (v) => {
                if (!v.trim()) return "Nom requis.";
                if (v.trim().length < 2) return "Nom trop court (2 min).";
                if (!/^[a-zA-ZÀ-ÿ\s\-']+$/.test(v.trim())) return "Lettres uniquement.";
                return "";
            },
            email: (v) => {
                if (!v.trim()) return "Email requis.";
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())) return "Format email invalide.";
                return "";
            },
            telephone: (v) => {
                if (!v.trim()) return "Téléphone requis.";
                if (!/^[0-9\s\+\-\(\)]{8,20}$/.test(v.trim())) return "Numéro invalide (8-20 chiffres).";
                return "";
            },
            password: (v) => {
                if (!v) return "Mot de passe requis.";
                if (v.length < 8) return "8 caractères minimum.";
                if (!/[A-Z]/.test(v)) return "Au moins une majuscule.";
                if (!/[a-z]/.test(v)) return "Au moins une minuscule.";
                if (!/[0-9]/.test(v)) return "Au moins un chiffre.";
                if (!/[\W_]/.test(v)) return "Au moins un caractère spécial.";
                return "";
            },
            confirm_password: (v) => {
                if (!v) return "Confirmation requise.";
                if (v !== document.getElementById('password').value) return "Les mots de passe ne correspondent pas.";
                return "";
            }
        };

        // --- Fonction générique de validation ---
        function validateField(input, errorId) {
            const errorEl = document.getElementById(errorId);
            const rule = rulesInscription[input.id];
            if (!rule) return true;

            const msg = rule(input.value);
            errorEl.textContent = msg;
            input.classList.toggle('input-error', msg !== "");
            return msg === "";
        }

        const formInscription = document.getElementById('formInscription');
        const passwordInput = document.getElementById('password');

        // --- Attache : blur (validation) + input (efface erreur) ---
        formInscription.querySelectorAll('.form-input').forEach(input => {
            const errorId = 'error-' + input.id;

            // Au blur : on valide
            input.addEventListener('blur', function() {
                if (this.value !== "") validateField(this, errorId);
            });

            // Au input : efface l'erreur si ça devient valide
            input.addEventListener('input', function() {
                const errorEl = document.getElementById(errorId);
                if (errorEl.textContent !== "" && validateField(this, errorId)) {
                    errorEl.textContent = "";
                }

                // Cas spécial : si on modifie le mot de passe, on revalide la confirmation
                if (this.id === 'password' && document.getElementById('confirm_password').value !== "") {
                    validateField(document.getElementById('confirm_password'), 'error-confirm_password');
                }
            });
        });

        // --- Validation finale à la soumission ---
        formInscription.addEventListener('submit', function(e) {
            let allValid = true;

            formInscription.querySelectorAll('.form-input').forEach(input => {
                const errorId = 'error-' + input.id;
                if (!validateField(input, errorId)) {
                    allValid = false;
                }
            });

            if (!allValid) {
                e.preventDefault();
                const firstError = formInscription.querySelector('.input-error');
                if (firstError) firstError.focus();
            }
        });
    </script>
</body>
</html>
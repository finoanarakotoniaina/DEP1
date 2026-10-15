<?php
require_once '../config.php'; 

// Génération captcha
if (!isset($_SESSION['captcha_reponse'])) {
    $n1 = rand(0, 3);
    $n2 = rand(0, 3);
    $_SESSION['captcha_reponse'] = $n1 + $n2;
    $_SESSION['captcha_question'] = "$n1 + $n2";
}

$message = ""; 

if (isset($_POST['connexion'])) {
    $email_saisi   = trim($_POST['email'] ?? '');
    $mdp_saisi     = $_POST['password'] ?? '';
    $captcha_saisi = isset($_POST['captcha']) ? intval($_POST['captcha']) : -1;

    // --- VALIDATION SERVEUR ---
    if (!filter_var($email_saisi, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresse email invalide.";
    } elseif (strlen($mdp_saisi) < 1) {
        $message = "Mot de passe requis.";
    } elseif ($captcha_saisi < 0) {
        $message = "Captcha invalide (nombre positif attendu).";
    } elseif ($captcha_saisi !== $_SESSION['captcha_reponse']) {
        $message = "Captcha incorrect.";
    } else {
        try {
            $sql = "SELECT l.password, c.id_client, c.prenom, c.nom, c.email, c.telephone 
                    FROM login l
                    INNER JOIN client c ON l.id_login = c.id_login 
                    WHERE l.login = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$email_saisi]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($mdp_saisi, $user['password'])) {
                    $_SESSION['id_client'] = $user['id_client'];
                    $_SESSION['prenom']    = $user['prenom'];
                    $_SESSION['nom']       = $user['nom'];
                    $_SESSION['email']     = $user['email'];
                    $_SESSION['telephone'] = $user['telephone'];

                    $final_redirect = 'accueil.php';
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'redirect=demande.php') !== false) {
                        $final_redirect = 'demande.php';
                    }

                    header("Location: " . $final_redirect);
                    exit();
                } else {
                    $message = "Mot de passe incorrect.";
                }
            } else {
                $message = "Identifiant inconnu.";
            }
        } catch (PDOException $e) {
            $message = "Erreur de base de données : " . $e->getMessage();
        }
    }

    // Régénérer le captcha
    $n1 = rand(0, 3); $n2 = rand(0, 3);
    $_SESSION['captcha_reponse'] = $n1 + $n2;
    $_SESSION['captcha_question'] = "$n1 + $n2";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Connexion</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg">

    <?php include 'composants_client/navbar.php'; ?>

    <main class="auth-page">
        <!-- Colonne gauche : formulaire -->
        <div class="auth-form-col">
            <div class="auth-content">
                <h2>CONNEXION</h2>
                <div class="title-bar"></div>

                <?php if (isset($_GET['inscription']) && $_GET['inscription'] === 'success' && empty($message)): ?>
                    <?php $message = "Compte créé avec succès ! Vous pouvez maintenant vous connecter."; ?>
                <?php endif; ?>

                <?php if (!empty($message)): ?>
                    <p class="form-msg"><?= htmlspecialchars($message) ?></p>
                <?php endif; ?>

                <form action="connexion.php" method="POST" id="formConnexion" novalidate>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" 
                               name="email" 
                               id="email"
                               placeholder="nom@gmail.com" 
                               required 
                               class="form-input"
                               autocomplete="email">
                        <span class="form-error" id="error-email"></span>
                    </div>

                    <div class="grid grid-2" style="gap:2rem;">
                        <div class="form-group">
                            <label class="form-label">Mot de passe</label>
                            <div class="password-wrapper">
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       placeholder="••••••••" 
                                       required 
                                       class="form-input"
                                       autocomplete="current-password">
                                <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Afficher le mot de passe">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                            <span class="form-error" id="error-password"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="color:var(--crimson);">
                                Calcul : <?= $_SESSION['captcha_question'] ?>
                            </label>
                            <div class="number-input-wrapper">
                                <input type="number" 
                                       name="captcha" 
                                       id="captcha" 
                                       placeholder="0" 
                                       required 
                                       min="0" 
                                       max="999" 
                                       step="1"
                                       class="form-input">
                                <div class="number-arrows">
                                    <button type="button" class="arrow-up" onclick="document.getElementById('captcha').stepUp()">▲</button>
                                    <button type="button" class="arrow-down" onclick="document.getElementById('captcha').stepDown()">▼</button>
                                </div>
                            </div>
                            <span class="form-error" id="error-captcha"></span>
                        </div>
                    </div>

                    <div class="auth-actions">
                        <button type="submit" name="connexion" class="btn btn-primary btn-block">
                            Se connecter
                        </button>
                        <a href="inscription.php" class="auth-alt-link">
                            Créer un compte
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Colonne droite : image -->
        <div class="auth-image-col">
            <img src="<?= ASSETS_URL ?>/hero/photo_connexion.webp" alt="Ferrari">
        </div>
    </main>

    <script>
        // =========================================================
        // AFFICHER / MASQUER LE MOT DE PASSE (SVG œil)
        // =========================================================
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;

            const iconEyeOpen = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>`;

            const iconEyeClosed = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>`;

            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = iconEyeOpen;
                btn.setAttribute('aria-label', 'Masquer le mot de passe');
            } else {
                input.type = 'password';
                btn.innerHTML = iconEyeClosed;
                btn.setAttribute('aria-label', 'Afficher le mot de passe');
            }
        }

        // =========================================================
        // VALIDATION CLIENT — Connexion
        // Mode : blur (par champ) + submit (global)
        // =========================================================

        // --- Règles de validation par champ ---
        const rulesConnexion = {
            email: (v) => {
                if (!v.trim()) return "Email requis.";
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())) return "Format email invalide.";
                return "";
            },
            password: (v) => {
                if (!v) return "Mot de passe requis.";
                return "";
            },
            captcha: (v) => {
                const n = parseInt(v, 10);
                if (v === "" || isNaN(n)) return "Réponse requise.";
                if (n < 0) return "Nombre positif attendu.";
                return "";
            }
        };

        // --- Fonction générique de validation d'un champ ---
        function validateField(input, errorId) {
            const value = input.value;
            const errorEl = document.getElementById(errorId);
            const rule = rulesConnexion[input.id];
            if (!rule) return true;

            const msg = rule(value);
            errorEl.textContent = msg;
            input.classList.toggle('input-error', msg !== "");
            return msg === "";
        }

        // --- Attache : blur (validation immédiate) + input (efface l'erreur) ---
        const formConnexion = document.getElementById('formConnexion');

        formConnexion.querySelectorAll('.form-input').forEach(input => {
            const errorId = 'error-' + input.id;

            // Au blur : on valide
            input.addEventListener('blur', function() {
                if (this.value !== "") validateField(this, errorId);
            });

            // Au input : si l'erreur était affichée et que ça devient valide, on efface
            input.addEventListener('input', function() {
                const errorEl = document.getElementById(errorId);
                if (errorEl.textContent !== "" && validateField(this, errorId)) {
                    errorEl.textContent = "";
                }
            });
        });

        // --- Validation finale à la soumission ---
        formConnexion.addEventListener('submit', function(e) {
            let allValid = true;

            formConnexion.querySelectorAll('.form-input').forEach(input => {
                const errorId = 'error-' + input.id;
                if (!validateField(input, errorId)) {
                    allValid = false;
                }
            });

            if (!allValid) {
                e.preventDefault();
                // Focus sur le premier champ en erreur
                const firstError = formConnexion.querySelector('.input-error');
                if (firstError) firstError.focus();
            }
        });
    </script>
</body>
</html>
# Configuration du fichier .env

## Instructions pour créer le fichier .env

Pour utiliser les variables d'environnement, vous devez créer un fichier `.env` à la racine du projet.

### Étapes :

1. **Créez un fichier nommé `.env`** dans le dossier `TP_PHP` (même niveau que `index.php`)

2. **Copiez le contenu suivant dans ce fichier :**

```
# Configuration de la base de données
DB_HOST=localhost
DB_NAME=tp_php
DB_USER=root
DB_PASS=
DB_CHARSET=utf8
```

3. **Modifiez les valeurs selon votre configuration :**
   - `DB_HOST` : L'adresse de votre serveur de base de données (généralement `localhost`)
   - `DB_NAME` : Le nom de votre base de données
   - `DB_USER` : Votre nom d'utilisateur MySQL
   - `DB_PASS` : Votre mot de passe MySQL (laissez vide si pas de mot de passe)
   - `DB_CHARSET` : L'encodage des caractères (généralement `utf8`)

### Exemple de configuration :

Si votre base de données s'appelle `ma_base` et que votre utilisateur est `admin` avec le mot de passe `monMotDePasse123`, votre fichier `.env` ressemblera à :

```
# Configuration de la base de données
DB_HOST=localhost
DB_NAME=ma_base
DB_USER=admin
DB_PASS=monMotDePasse123
DB_CHARSET=utf8
```

### Important :

- ⚠️ **Ne partagez JAMAIS votre fichier `.env`** (il contient des informations sensibles)
- Le fichier `.env` est déjà dans le `.gitignore` pour ne pas être versionné
- Si le fichier `.env` n'existe pas, le système utilisera des valeurs par défaut

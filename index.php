<?php

require 'DBCONNECT.php';

//connexion à la base de données
$db = dbConnect();

function getAuteurs($db) {
	$query = $db->prepare('SELECT nom FROM auteur');
	$query->execute();
	if ($query->errorCode() != 0) {
		$errors = $query->errorInfo();
		throw new PDOException($errors[2], $errors[0]);
	}
	return $query->fetchAll(PDO::FETCH_ASSOC);
}
function getEditeurs($db) {
	$query = $db->prepare('SELECT nom FROM editeur');
	$query->execute();
	if ($query->errorCode() != 0) {
		$errors = $query->errorInfo();
		throw new PDOException($errors[2], $errors[0]);
	}
	return $query->fetchAll(PDO::FETCH_ASSOC);
}


$auteurs = getAuteurs($db);
$editeurs = getEditeurs($db);

$db = null;

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Recherche</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">
    <div class="flex flex-row px-3">
        <div class="w-5/12 mx-auto mt-10 p-5 bg-white rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-5">Recherche de livres</h1>
            <form action="result.php" method="GET">
                <div class="mb-4">
                    <label for="titre" class="block font-medium text-gray-700">Titre :</label>
                    <input type="text" name="titre" id="titre" value="<?php echo isset($titre) ? $titre : ''; ?>" class="w-full border-gray-400 rounded-lg shadow-md py-2 px-3 mt-1">
                </div>
                <div class="mb-4">
                    <label for="auteur" class="block font-medium text-gray-700">Auteur :</label>
                    <select name="auteur" id="auteur" class="w-full border-gray-400 rounded-lg shadow-md py-2 px-3 mt-1">
                        <option value="" <?php echo (isset($auteur) && $auteur == 'tous') ? 'selected' : ''; ?>>Tous</option>
                        <?php foreach ($auteurs as $auteur) : ?>
                            <option value="<?php echo $auteur['nom']; ?>" <?php echo (isset($auteur) && $auteur == $auteur['nom']) ? 'selected' : ''; ?>><?php echo $auteur['nom']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="editeur" class="block font-medium text-gray-700">Editeur :</label>
                    <select name="editeur" id="editeur" class="w-full border-gray-400 rounded-lg shadow-md py-2 px-3 mt-1">
                        <option value="" <?php echo (isset($editeur) && $editeur == 'tous') ? 'selected' : ''; ?>>Tous</option>
                        <?php foreach ($editeurs as $editeur) : ?>
                            <option value="<?php echo $editeur['nom']; ?>" <?php echo (isset($editeur) && $editeur == $editeur['nom']) ? 'selected' : ''; ?>><?php echo $editeur['nom']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="dispo" class="block font-medium text-gray-700">Disponibilité :</label>
                    <select name="dispo" id="dispo" class="w-full border-gray-400 rounded-lg shadow-md py-2 px-3 mt-1">
                        <option value="tous" <?php echo (isset($dispo) && $dispo == 'tous') ? 'selected' : ''; ?>>Tous</option>
                        <option value="dispo" <?php echo (isset($dispo) && $dispo == 'dispo') ? 'selected' : ''; ?>>Disponible</option>
                        <option value="non_dispo" <?php echo (isset($dispo) && $dispo == 'non_dispo') ? 'selected' : ''; ?>>Non disponible</option>
                    </select>
                </div>
                <div class="mb-4">
                    <input type="submit" value="Rechercher" class="py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md">
                </div>

            </form>
            <br>
        </div>
        <div class="w-5/12 mx-auto mt-10 p-5 bg-white rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-5">Rechercher un abonne</h1>
            <form action="resultsAbonne.php" method="GET">
                <div class="mb-4">
                    <label for="nom" class="block font-medium text-gray-700">Nom :</label>
                    <input type="text" name="nom" id="nom" value="<?php echo isset($nom) ? $nom : ''; ?>" class="w-full border-gray-400 rounded-lg shadow-md py-2 px-3 mt-1">
                </div>
                <div class="mb-4">
                    <label for="prenom" class="block font-medium text-gray-700">Prénom :</label>
                    <input type="text" name="prenom" id="prenom" value="<?php echo isset($prenom) ? $prenom : ''; ?>" class="w-full border-gray-400 rounded-lg shadow-md py-2 px-3 mt-1">
                </div>
                <div class="mb-4">
                    <label for="ville" class="block font-medium text-gray-700">Ville :</label>
                    <input type="text" name="ville" id="ville" value="<?php echo isset($ville) ? $ville : ''; ?>" class="w-full border-gray-400 rounded-lg shadow-md py-2 px-3 mt-1">
                </div>
                <div class="mb-4">
                    <!--select pour l'état de l'abonnement (abonne, expire ou tous-->
                    <label for="etat" class="block font-medium text-gray-700">Etat :</label>
                    <select name="etat" id="etat" class="w-full border-gray-400 rounded-lg shadow-md py-2 px-3 mt-1">
                        <option value="tous" <?php echo (isset($etat) && $etat == 'tous') ? 'selected' : ''; ?>>Tous</option>
                        <option value="abonne" <?php echo (isset($etat) && $etat == 'abonne') ? 'selected' : ''; ?>>Abonné</option>
                        <option value="expire" <?php echo (isset($etat) && $etat == 'expire') ? 'selected' : ''; ?>>Expiré</option>
                    </select>
                </div>
                <div class="mb-4">
                    <input type="submit" value="Rechercher" class="py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md">
                </div>
            </form>
        </div>
</body>

</html>
<?php
//page fiche abonné
//import de la fonction de connexion à la base de données
require 'DBCONNECT.php';

//connexion à la base de données
$db = dbConnect();


// Récupération des informations de l'abonné
function get_abonne_info($id, $db)
{
    $query = $db->prepare('SELECT * FROM abonne WHERE id = :id');
    $query->execute(['id' => $id]);
    return $query->fetch(PDO::FETCH_ASSOC);
}

// Récupération de la liste des livres empruntés par l'abonné par ordre chronologique, le tableau doit contenir le titre du livre, la date d'emprunt et la date de retour
function get_livres_empruntes($id, $db)
{
    $query = $db->prepare('SELECT livre.titre, emprunt.date_emprunt, emprunt.date_retour
    FROM emprunt
    INNER JOIN livre ON emprunt.id_livre = livre.id
    WHERE emprunt.id_abonne = :id
    ORDER BY  emprunt.date_emprunt
    DESC
    ');
    $query->execute(['id' => $id]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


//suggérer 5 livres qui pourrait plaire à l'abonné de la manière suivante :
// chercher le genre que l'abonné lit le plus 
// prendre les livres les plus empruntés sur 1 an pour ce genre
// exclure les livres déjà empruntés par l'abonné
// exlure les livres actuallement empruntés 
function get_suggestions($id, $db)
{
    //chercher le genre que l'abonné lit le plus
    $query = $db->prepare('SELECT genre, COUNT(genre) AS nb_genre
    FROM livre
    INNER JOIN emprunt ON emprunt.id_livre = livre.id
    WHERE emprunt.id_abonne = :id
    GROUP BY genre
    ORDER BY nb_genre DESC
    LIMIT 1
    ');
    $query->execute(['id' => $id]);
    $genre = $query->fetch(PDO::FETCH_ASSOC);

    if ($genre == false) {
        //suggérer les 5 livres les plus empruntés sur 1 an si l'abonné n'a pas encore emprunté de livre
        $query = $db->prepare('SELECT livre.titre, livre.genre, COUNT(emprunt.id_livre) AS nb_emprunt
        FROM livre
        INNER JOIN emprunt ON emprunt.id_livre = livre.id
        GROUP BY emprunt.id_livre
        ORDER BY nb_emprunt DESC
        LIMIT 5
        ');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    //prendre les livres les plus empruntés sur 1 an pour ce genre
    $query = $db->prepare('SELECT livre.titre, livre.genre, COUNT(emprunt.id_livre) AS nb_emprunt
    FROM livre
    INNER JOIN emprunt ON emprunt.id_livre = livre.id
    WHERE livre.genre = :genre
    AND emprunt.date_emprunt > DATE_SUB(NOW(), INTERVAL 1 YEAR)
    GROUP BY emprunt.id_livre
    ORDER BY nb_emprunt DESC
    LIMIT 5
    ');
    $query->execute([
        'genre' => $genre['genre']
    ]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
// Récupération de l'id de l'abonné
$id = $_GET['id'];

// Récupération des informations de l'abonné
$abonne = get_abonne_info($id, $db);

// Récupération des livres empruntés par l'abonné
$emprunts = get_livres_empruntes($id, $db);

// Récupération des suggestions de livres
$suggestions = get_suggestions($id, $db);

}

elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
    //mise à jour de la base de donnée avec les infos du formulaire 
    $query = $db->prepare('UPDATE abonne SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, adresse = :adresse, code_postal = :code_postal, ville = :ville WHERE id = :id');
    $query->execute([
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'date_naissance' => $_POST['date_naissance'],
        'adresse' => $_POST['adresse'],
        'code_postal' => $_POST['code_postal'],
        'ville' => $_POST['ville'],
        'id' => $_POST['id']
    ]);
    //redirection vers la même page
    header('Location: abonne.php?id='.$_POST['id']);
    exit();
}

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Abonné</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="bg-gray-100 min-h-screen flex items-center justify-center flex flex-row justify-around">
        <div id="fiche_container" class="w-3/6 p-8 bg-white rounded-lg shadow-lg">
            <a href="index.php" class="bg-teal-400 hover:bg-teal-600 text-white font-bold py-2 px-4 rounded mb-8">Accueil</a>
            <h1 class="text-2xl font-bold mb-4 mt-8">Fiche abonné</h1>
            <div id="fiche_content" class="flex justify-between mt-8 mb-4">
                <div id="fiche_content_left" class="w-1/3">
                    <p class="mb-2"><span class="font-bold">Nom :</span> <?php echo $abonne['nom'] ?></p>
                    <p class="mb-2"><span class="font-bold">Prénom :</span> <?php echo $abonne['prenom'] ?></p>
                    <p class="mb-2"><span class="font-bold">Date de naissance :</span> <?php echo $abonne['date_naissance'] ?></p>
                </div>
                <div id="fiche_content_center>" class="w-1/3">
                    <p class="mb-2"><span class="font-bold">Adresse :</span> <?php echo $abonne['adresse'] ?></p>
                    <p class="mb-2"><span class="font-bold">Ville :</span> <?php echo $abonne['ville'] ?></p>
                    <p class="mb-2"><span class="font-bold">Code postal :</span> <?php echo $abonne['code_postal'] ?></p>
                </div>
                <div id="fiche_content_right" class="w-1/3">
                    <p class="mb-2"><span class="font-bold">Date d'inscription :</span> <?php echo $abonne['date_inscription'] ?></p>
                    <p class="mb-2"><span class="font-bold">Date de fin d'abonnement :</span> <?php echo $abonne['date_fin_abo'] ?></p>
                </div>
            </div>
            <a href="editabonne.php?id=<?php echo $abonne['id'] ?>" class="py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md">Edit</a>
            <!-- liste des livres empruntés par l'abonné -->
            <h1 class="text-2xl font-bold mb-4 mt-8">Livres empruntés</h1>
            <div id="livres_empruntes_content">
                <tbody>
                    <?php if (empty($emprunts)) : ?>
                        <td colspan="3" class="border px-4 py-2 text-center">Aucun livre emprunté</td>
                    <?php else : ?>
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Titre</th>
                            <th class="px-4 py-2">Date d'emprunt</th>
                            <th class="px-4 py-2">Date de retour</th>
                        </tr>
                    </thead>
                            <?php foreach ($emprunts as $emprunt) : ?>
                                <tr>
                                    <td class="border px-4 py-2"><?php echo $emprunt['titre'] ?></td>
                                    <td class="border px-4 py-2"><?php echo $emprunt['date_emprunt'] ?></td>
                                    <td class="border px-4 py-2"><?php echo $emprunt['date_retour'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
            </div>
            <h1 class="text-2xl font-bold mb-4 mt-8">Suggestions</h1>
            <div id="suggestions_content">
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Titre</th>
                            <th class="px-4 py-2">Genre</th>
                            <th class="px-4 py-2">Nombre d'emprunts</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suggestions as $suggestion) : ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $suggestion['titre'] ?></td>
                                <td class="border px-4 py-2"><?php echo $suggestion['genre'] ?></td>
                                <td class="border px-4 py-2"><?php echo $suggestion['nb_emprunt'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
</body>

</html>
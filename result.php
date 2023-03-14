<?php
require 'DBCONNECT.php';

$db = dbConnect();

$titre = isset($_GET['titre']) ? $_GET['titre'] : '';
$auteur = isset($_GET['auteur']) ? $_GET['auteur'] : '';
$editeur = isset($_GET['editeur']) ? $_GET['editeur'] : '';
$dispo = isset($_GET['dispo']) ? $_GET['dispo'] : 'tous';

// définir la page actuelle si elle n'est pas défini
$page = isset($_GET['page']) ? $_GET['page'] : 1;
// calculer la limite et l'offset
$limit = 20;
$offset = ($page - 1) * $limit;


$query = $db->prepare('SELECT livre.id, livre.titre, auteur.nom AS auteur, editeur.nom AS editeur, emprunt.id_abonne as emprunteur,
    IF(emprunt.date_retour < NOW(), "Disponible", "Non disponible") AS disponibilite,
    (SELECT MAX(emprunt.date_emprunt) FROM emprunt WHERE emprunt.id_livre = livre.id) AS date_emprunt
    FROM livre
    INNER JOIN auteur ON livre.id_auteur = auteur.id
    INNER JOIN editeur ON livre.id_editeur = editeur.id
    LEFT JOIN emprunt ON livre.id = emprunt.id_livre
    WHERE livre.titre LIKE :titre
    AND auteur.nom LIKE :auteur
    AND editeur.nom LIKE :editeur
    AND ((CASE WHEN emprunt.date_retour < NOW() THEN "dispo" ELSE "non_dispo" END) = :disponibilite1 OR :disponibilite2 = "tous")
    GROUP BY livre.id, disponibilite
    LIMIT ' . $limit . '
    OFFSET ' . $offset . '
    ');



$query->execute([
    'titre' => '%' . $titre . '%',
    'auteur' => '%' . $auteur . '%',
    'editeur' => '%' . $editeur . '%',
    'disponibilite1' => $dispo,
    'disponibilite2' => $dispo,
]);

$livres = $query->fetchAll(PDO::FETCH_ASSOC);


$db = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-100 max-h-screen">
    <div class="max-w-screen-lg mx-auto p-4">
        <a href="index.php" class="py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md">Accueil</a>
        <div class="mt-4 flex justify-between">
            <?php
            // ajouter des liens de pagination pour passer à la page suivante et précédente
            if ($page > 1) {
                echo '<a href="result.php?titre=' . $titre . '&auteur=' . $auteur . '&editeur=' . $editeur . '&dispo=' . $dispo . '&page=' . ($page - 1) . '" class="py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md">Page précédente</a>';
            }
            if (count($livres) == $limit) {
                echo '<a href="result.php?titre=' . $titre . '&auteur=' . $auteur . '&editeur=' . $editeur . '&dispo=' . $dispo . '&page=' . ($page + 1) . '" class="py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md">Page suivante</a>';
            }
            ?>
        </div>
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Index</th>
                    <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Titre</th>
                    <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Auteur</th>
                    <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Editeur</th>
                    <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Disponibilité</th>
                    <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Date d'emprunt</th>
                    <!-- colonne "emprunteur" qui s'affiche seulement si le livre est emprunté -->
                    <?php if ($dispo == 'non_dispo') : ?>
                        <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Emprunteur</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre) : ?>
                    <tr class="hover:bg-gray-200">
                        <td class="px-4 py-2 border-b border-gray-300"><?php echo $livre['id']; ?></td>
                        <td class="px-4 py-2 border-b border-gray-300"><?php echo $livre['titre']; ?></td>
                        <td class="px-4 py-2 border-b border-gray-300"><?php echo $livre['auteur']; ?></td>
                        <td class="px-4 py-2 border-b border-gray-300"><?php echo $livre['editeur']; ?></td>
                        <td class="px-4 py-2 border-b border-gray-300"><?php echo $livre['disponibilite']; ?></td>
                        <!-- date d'emprunt mis en forme -->
                        <td class="px-4 py-2 border-b border-gray-300"><?php echo date('d/m/Y', strtotime($livre['date_emprunt'])); ?></td>
                        <!-- colonne "emprunteur" qui s'affiche seulement si le livre est emprunté -->
                        <?php if ($dispo == 'non_dispo') : ?>
                        <!--bouton vers la fiche de l'emprunteur -->
                            <td class="px-4 py-2 border-b border-gray-300"><a href="abonne.php?id=<?php echo $livre['emprunteur']; ?>" class="py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md">Fiche</a></td>
                            <?php endif; ?>
                    
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </div>
</body>
</html>
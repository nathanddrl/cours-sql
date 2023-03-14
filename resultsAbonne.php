<?php
require_once 'DBCONNECT.php';

function getAbonnes($nom, $prenom, $ville, $etat, $page = 1, $limit = 20) {
    $db = dbConnect();

    $offset = ($page - 1) * $limit;

    $query = $db->prepare('SELECT abonne.id, abonne.nom, abonne.prenom, abonne.ville, IF(NOW() BETWEEN date_inscription AND date_fin_abo, "abonne", "expire") AS etat
        FROM abonne
        WHERE UPPER(abonne.nom) LIKE UPPER(:nom)
        AND UPPER(abonne.prenom) LIKE UPPER(:prenom)
        AND UPPER(abonne.ville) LIKE UPPER(:ville)
        AND (IF(NOW() BETWEEN date_inscription AND date_fin_abo, "abonne", "expire") LIKE :etat OR :etat = "tous")
        LIMIT :limit
        OFFSET :offset');

    $query->bindValue(':nom', '%' . $nom . '%');
    $query->bindValue(':prenom', '%' . $prenom . '%');
    $query->bindValue(':ville', '%' . $ville . '%');
    $query->bindValue(':etat', $etat);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();

    $abonnes = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = null;
    return $abonnes;
}

$nom = isset($_GET['nom']) ? $_GET['nom'] : '';
$prenom = isset($_GET['prenom']) ? $_GET['prenom'] : '';
$ville = isset($_GET['ville']) ? $_GET['ville'] : '';
$etat = isset($_GET['etat']) ? $_GET['etat'] : '';

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$abonnes = getAbonnes($nom, $prenom, $ville, $etat, $page);

?>

<doctype html>
    <html lang="fr">

    <head>
        <meta charset="utf-8">
        <title>Abonnés</title>
        <link rel="stylesheet" href="style.css">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="bg-gray-100 h-screen">
        <div class="max-w-screen-lg mx-auto p-4">
            <a href="index.php" class="py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md">Accueil</a>
            <div class="mt-4 flex justify-between">
                <?php
                // ajouter des liens de pagination pour passer à la page suivante et précédente
                if ($page > 1) {
                    echo "<a href='resultsAbonne.php?nom=$nom&prenom=$prenom&ville=$ville&etat=$etat&page=" . ($page - 1) . "' class='py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md'>Page précédente</a>";
                }
                if (count($abonnes) == 20) {
                    echo "<a href='resultsAbonne.php?nom=$nom&prenom=$prenom&ville=$ville&etat=$etat&page=" . ($page + 1) . "' class='py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md'>Page suivante</a>";
                }

                ?>
            </div>
            <table class="w-full border-collapse">
                <thead>
                    <!-- lien vers la page de l'abonné -->
                    <tr>
                        <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Index</th>
                        <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Nom</th>
                        <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Prénom</th>
                        <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Ville</th>
                        <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Etat</th>
                        <th class="px-4 py-2 text-gray-600 font-bold uppercase border-b border-gray-300">Voir fiche</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($abonnes as $abonne) : ?>
                        <tr class="hover:bg-gray-200 text-center">
                            <td class="px-4 py-2 border-b border-gray-300"><?php echo $abonne['id']; ?></td>
                            <td class="px-4 py-2 border-b border-gray-300"><?php echo $abonne['nom']; ?></td>
                            <td class="px-4 py-2 border-b border-gray-300"><?php echo $abonne['prenom']; ?></td>
                            <td class="px-4 py-2 border-b border-gray-300"><?php echo $abonne['ville']; ?></td>
                            <td class="px-4 py-2 border-b border-gray-300"><?php echo $abonne['etat']; ?></td>
                            <td class="px-4 py-2 border-b border-gray-300 flex justify-center items-center"><a href="abonne.php?id=<?php echo $abonne['id']; ?>" class="py-2 px-4 bg-teal-400 hover:bg-teal-600 text-white rounded-lg shadow-md">Voir fiche</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </body>

    </html>
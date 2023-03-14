<?php
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

// récupération des données de l'abonné
$abonne = get_abonne_info($_GET['id'], $db);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title></title>
    <script src="https://cdn.tailwindcss.com"></script>
     
</head>

<body class="bg-gray-200">
  <div class="max-w-lg mx-auto py-8">
    <form action="abonne.php" method="post" class="bg-white p-6 rounded-lg shadow-lg">
      <input type="hidden" name="id" value="<?php echo $abonne['id'] ?>">
      <div class="mb-4">
        <label for="nom" class="block text-gray-700 font-bold mb-2">Nom</label>
        <input type="text" name="nom" value="<?php echo $abonne['nom'] ?>" class="w-full p-2 border border-gray-400 rounded focus:bg-teal-200">
      </div>
      <div class="mb-4">
        <label for="prenom" class="block text-gray-700 font-bold mb-2">Prénom</label>
        <input type="text" name="prenom" value="<?php echo $abonne['prenom'] ?>" class="w-full p-2 border border-gray-400 rounded focus:bg-teal-200">
      </div>
      <div class="mb-4">
        <label for="adresse" class="block text-gray-700 font-bold mb-2">Adresse</label>
        <input type="text" name="adresse" value="<?php echo $abonne['adresse'] ?>" class="w-full p-2 border border-gray-400 rounded focus:bg-teal-200">
      </div>
      <div class="mb-4">
        <label for="code_postal" class="block text-gray-700 font-bold mb-2">Code postal</label>
        <input type="text" name="code_postal" value="<?php echo $abonne['code_postal'] ?>" class="w-full p-2 border border-gray-400 rounded focus:bg-teal-200">
      </div>
      <div class="mb-4">
        <label for="ville" class="block text-gray-700 font-bold mb-2">Ville</label>
        <input type="text" name="ville" value="<?php echo $abonne['ville'] ?>" class="w-full p-2 border border-gray-400 rounded focus:bg-teal-200">
      </div>
      <div class="mb-4">
        <label for="date_naissance" class="block text-gray-700 font-bold mb-2">Date de naissance</label>
        <input type="date" name="date_naissance" value="<?php echo $abonne['date_naissance'] ?>" class="w-full p-2 border border-gray-400 rounded focus:bg-teal-200">
      </div>
      <div class="mb-4">
        <label for="date_inscription" class="block text-gray-700 font-bold mb-2">Date d'inscription</label>
        <input type="date" name="date_inscription" value="<?php echo $abonne['date_inscription'] ?>" class="w-full p-2 border border-gray-400 rounded focus:bg-teal-200">
      </div>
      <div class="flex justify-end">
        <a href="abonne.php?id=<?php echo $abonne['id'] ?>" class="bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700">Annuler</a>
        <input type="submit" value="Modifier" class="bg-teal-400 text-white font-bold py-2 px-4 rounded hover:bg-teal-600 ml-6">
      </div>
    </form>
  </div>
</body>


</html>
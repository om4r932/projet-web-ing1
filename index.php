<?php
require_once 'db.php';

if (isset($_GET['term'])) {
    header('Content-Type: application/json');
    echo json_encode(searchPokemonNames($pdo, $_GET['term']));
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$pokemon = ($id > 0) ? getPokemonById($pdo, $id) : null;

function cleanString($string) {
    $search  = explode(",", "à,á,â,ã,ä,ç,è,é,ê,ë,ì,í,î,ï,ñ,ò,ó,ô,õ,ö,ù,ú,û,ü,ý,ÿ,À,Á,Â,Ã,Ä,Ç,È,É,Ê,Ë,Ì,Í,Î,Ï,Ñ,Ò,Ó,Ô,Õ,Ö,Ù,Ú,Û,Ü,Ý");
    $replace = explode(",", "a,a,a,a,a,c,e,e,e,e,i,i,i,i,n,o,o,o,o,o,u,u,u,u,y,y,A,A,A,A,A,C,E,E,E,E,I,I,I,I,N,O,O,O,O,O,U,U,U,U,Y");
    return strtolower(str_replace($search, $replace, $string));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pokédex</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="search-container">
    <input type="text" id="search" placeholder="Nom du Pokémon..." oninput="autoComplete()">
    <div id="suggestions"></div>
</div>

<div class="card">
    <?php
    if ($pokemon) {
        $stats = getStatsByPokemonId($pdo, $id);
        $types = getTypesByPokemonId($pdo, $id);
        $evolutions = getEvolutionsByPokemonId($pdo, $id);
        $reversedEvolutions = getReversedEvolutionsByPokemonId($pdo, $id);

        echo "<h1>" . htmlspecialchars($pokemon['name_fr']) . " (#" . $pokemon['pokedex_id'] . ")</h1>";

        $sprites = [
            "Normal" => "regular",
            "Normal (Chromatique)" => "shiny",
            "Méga" => "mega-regular",
            "Mega (Chromatique)" => "mega-shiny",
            "Méga (version X)" => "mega_x-regular",
            "Méga (version X) (Chromatique)" => "mega_x-shiny",
            "Méga (version Y)" => "mega_y-regular",
            "Méga (version Y) (Chromatique)" => "mega_y-shiny",
            "Gigamax" => "gmax-regular"
        ];

        echo "<div class='carousel-container'>";
        $i = 0;
        foreach ($sprites as $label => $s) {
            $url = "https://raw.githubusercontent.com/Yarkis01/TyraDex/images/sprites/" . $id . "/" . $s . ".png";
            $headers = @get_headers($url);
            if ($headers && strpos($headers[0], '200')) {
                $i++;
                echo "<div class='carousel-item'><img class='sprite' src='$url' alt='$label'/><p><strong>$label</strong></p></div>";
            }
        }

        if($i > 1){
            echo "<div class='carousel-buttons'><button onclick='prevSlide()'>Précédent</button><button onclick='nextSlide()'>Suivant</button></div>";
        }
        echo "</div>";

        echo "<p>Nom Anglais: " . htmlspecialchars($pokemon['name_en']) . "</p>";
        echo "<p>Nom Japonais: " . htmlspecialchars($pokemon['name_jp']) . "</p>";
        echo "<p>Catégorie: " . htmlspecialchars($pokemon['category']) . "</p>";
        echo "<p>Type: " . implode(" ", array_map(fn($t) => "<img class='type' src='https://raw.githubusercontent.com/Yarkis01/TyraDex/images/types/" . cleanString($t['name']) . ".png' title='".$t['name']."' />", $types)) . "</p>";
        echo "<p>Taille: " . $pokemon['height'] . "m | Poids: " . $pokemon['weight'] . "kg</p>";
        
        if ($stats) {
            echo "<h2>Statistiques</h2>";
            $sList = ['hp'=>'PV', 'atk'=>'Attaque', 'def'=>'Défense', 'spe_atk'=>'Attaque Spé', 'spe_def'=>'Défense Spé', 'vit'=>'Vitesse'];
            foreach ($sList as $key => $label) {
                $val = $stats[$key];
                echo "<div>$label: $val</div>";
                echo "<div class='stats-bar'><div class='progress' style='width: " . min(100, ($val / 200) * 100) . "%'></div></div>";
            }
        }

        if ($evolutions || $reversedEvolutions) {
            echo "<h2>Famille d'évolution</h2>";
            if ($reversedEvolutions) {
                foreach ($reversedEvolutions as $evo) echo "<div>Pré-évolution: <a href='index.php?id=" . $evo['pokedex_id'] . "'>" . htmlspecialchars($evo['name_fr']) . "</a></div>";
            }
            if ($evolutions) {
                foreach ($evolutions as $evo) echo "<div>Évolution: <a href='index.php?id=" . $evo['pokedex_id'] . "'>" . htmlspecialchars($evo['name_fr']) . "</a> (".$evo['evolution_condition'].")</div>";
            }
        }
    } else {
        echo "<p>Pokémon non trouvé.</p>";
    }
    ?>
</div>

<script src="script.js"></script>
</body>
</html>

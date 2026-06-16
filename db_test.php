<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

if ($id > 0) {
    $pokemon = getPokemonById($pdo, $id);

    if ($pokemon) {
        $stats = getStatsByPokemonId($pdo, $id);
        $types = getTypesByPokemonId($pdo, $id);
        $evolutions = getEvolutionsByPokemonId($pdo, $id);
        $reversedEvolutions = getReversedEvolutionsByPokemonId($pdo, $id);

        echo "<h1>" . htmlspecialchars($pokemon['name_fr']) . " (#" . $pokemon['pokedex_id'] . ")</h1>";

        $sprites = ["regular", "shiny", "mega-regular", "mega-shiny", "mega_x-regular", "mega_x-shiny", "mega_y-regular", "mega_y-shiny", "gmax-regular", "g"];
        foreach ($sprites as $s) {
            $url = "https://raw.githubusercontent.com/Yarkis01/TyraDex/images/sprites/" . $id . "/" . $s . ".png";
            $headers = @get_headers($url);
            if ($headers && strpos($headers[0], '200')) {
                echo "<img style='width: 128px;' src='$url' alt='$s'/>";
            }
        }

        echo "<p>Nom Anglais: " . htmlspecialchars($pokemon['name_en']) . "</p>";
        echo "<p>Nom Japonais: " . htmlspecialchars($pokemon['name_jp']) . "</p>";
        echo "<p>Catégorie: " . htmlspecialchars($pokemon['category']) . "</p>";
        $type_names = array_map(fn($t) => "<img style='width: 30px;' src='https://raw.githubusercontent.com/Yarkis01/TyraDex/images/types/" . strtolower($t['name']) . ".png' />", $types);
        echo "<p>Type: " . implode(" ", $type_names) . "</p>";
        echo "<p>Taille: " . $pokemon['height'] . "m</p>";
        echo "<p>Poids: " . $pokemon['weight'] . "kg</p>";

        if ($stats) {
            echo "<h2>Statistiques</h2>";
            echo "<ul>
                    <li>PV: " . $stats['hp'] . "</li>
                    <li>Attaque: " . $stats['atk'] . "</li>
                    <li>Défense: " . $stats['def'] . "</li>
                    <li>Attaque Spéciale: " . $stats['spe_atk'] . "</li>
                    <li>Défense Spéciale: " . $stats['spe_def'] . "</li>
                    <li>Vitesse: " . $stats['vit'] . "</li>
                  </ul>";
        }

        if ($evolutions) {
            echo "<h2>Évolutions</h2>";
            echo "<ul>";
            foreach ($evolutions as $evo) {
                echo "<li><a href='pokemon_info.php?id=" . $evo['pokedex_id'] . "'>" . htmlspecialchars($evo['name_fr']) . "</a>";
                if ($evo['evolution_condition']) {
                    echo " (" . htmlspecialchars($evo['evolution_condition']) . ")";
                }
                echo "</li>";
            }
            echo "</ul>";
        }

        if ($reversedEvolutions) {
            echo "<h2>Formes précédents</h2>";
            echo "<ul>";
            foreach ($reversedEvolutions as $evo) {
                echo "<li><a href='pokemon_info.php?id=" . $evo['pokedex_id'] . "'>" . htmlspecialchars($evo['name_fr']) . "</a></li>";
            }
            echo "</ul>";
        }

    } else {
        echo "Pokémon non trouvé.";
    }
} else {
    echo "ID invalide.";
}
?>
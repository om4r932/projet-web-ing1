CREATE DATABASE IF NOT EXISTS sae CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sae;

DROP TABLE IF EXISTS pokemon_talent CASCADE;
DROP TABLE IF EXISTS pokemon_type CASCADE;
DROP TABLE IF EXISTS pokemon_stats CASCADE;
DROP TABLE IF EXISTS pokemon CASCADE;
DROP TABLE IF EXISTS type CASCADE;
DROP TABLE IF EXISTS talent CASCADE;

CREATE TABLE IF NOT EXISTS pokemon (
    pokedex_id INT PRIMARY KEY,
    name_fr VARCHAR(50) NOT NULL,
    name_en VARCHAR(50) NOT NULL,
    name_jp VARCHAR(50) NOT NULL,
    category VARCHAR(50),
    height FLOAT(10,2),
    weight FLOAT(10,2),
    malePer FLOAT(10,2),
    femalePer FLOAT(10,2),
    catch_rate INT,
    generation INT
);

CREATE TABLE IF NOT EXISTS pokemon_stats (
    pokemon_id INT PRIMARY KEY,
    hp INT,
    atk INT,
    def INT,
    spe_atk INT,
    spe_def INT,
    vit INT,
    FOREIGN KEY (pokemon_id) REFERENCES pokemon(pokedex_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS pokemon_type (
    pokemon_id INT,
    type_id INT,
    PRIMARY KEY (pokemon_id, type_id),
    FOREIGN KEY (pokemon_id) REFERENCES pokemon(pokedex_id) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES type(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS talent (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS pokemon_talent (
    pokemon_id INT NOT NULL,
    talent_id INT NOT NULL,
    is_hidden BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (pokemon_id, talent_id),
    FOREIGN KEY (pokemon_id) REFERENCES pokemon(pokedex_id) ON DELETE CASCADE,
    FOREIGN KEY (talent_id) REFERENCES talent(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pokemon_evolution (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pre_evolution_id INT NOT NULL,
    post_evolution_id INT NOT NULL,
    evolution_condition VARCHAR(255),
    FOREIGN KEY (pre_evolution_id) REFERENCES pokemon(pokedex_id) ON DELETE CASCADE,
    FOREIGN KEY (post_evolution_id) REFERENCES pokemon(pokedex_id) ON DELETE CASCADE
);
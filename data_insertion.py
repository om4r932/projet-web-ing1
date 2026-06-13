import json
import os

import mysql.connector
import requests
from dotenv import load_dotenv

load_dotenv()

db_config = {
    "host": os.getenv("DB_HOST"),
    "user": os.getenv("DB_USER"),
    "password": os.getenv("DB_PASS"),
    "database": os.getenv("DB_NAME"),
    "port": os.getenv("DB_PORT")
}

headers = {
    "User-Agent": "RobotPokemon",
    'Content-type': 'application/json'
}

def transformer_pokemon(data):
    """Transforme le JSON brut de TyraDex en structures simplifiées pour SQL."""
    male_per = data["sexe"]["male"] if data["sexe"] is not None else 0.0
    female_per = data["sexe"]["female"] if data["sexe"] is not None else 0.0

    evolutions = []
    if data.get("evolution") and data["evolution"].get("next"):
        for evo in data["evolution"]["next"]:
            evolutions.append({
                "evolves_to_id": evo["pokedex_id"],
                "condition": evo.get("condition")
            })

    return {
        "pokemon": {
            "pokedex_id": data["pokedex_id"],
            "name_fr": data["name"]["fr"],
            "name_en": data["name"]["en"],
            "name_jp": data["name"]["jp"],
            "category": data["category"],
            "height": float(str(data["height"]).replace(" m", "").replace(",", ".")),
            "weight": float(str(data["weight"]).replace(" kg", "").replace(",", ".")),
            "malePer": float(male_per),
            "femalePer": float(female_per),
            "catch_rate": data["catch_rate"],
            "generation": data["generation"]
        },
        "stats": {
            "pokemon_id": data["pokedex_id"],
            "hp": data["stats"]["hp"],
            "atk": data["stats"]["atk"],
            "def": data["stats"]["def"],
            "spe_atk": data["stats"]["spe_atk"],
            "spe_def": data["stats"]["spe_def"],
            "vit": data["stats"]["vit"]
        },
        "types": [t["name"] for t in data["types"]],
        "talents": [{"name": tl["name"], "is_hidden": tl["tc"]} for tl in data["talents"]]
    }

data_list = requests.get("https://tyradex.app/api/v1/pokemon", headers=headers).json()[1:]

try:
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()

    for item in data_list:
        p = transformer_pokemon(item)

        cursor.execute("""
            INSERT INTO pokemon (pokedex_id, name_fr, name_en, name_jp, category, height, weight, malePer, femalePer, catch_rate, generation)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE name_fr=%s, name_en=%s, name_jp=%s
        """, (p["pokemon"]["pokedex_id"], p["pokemon"]["name_fr"], p["pokemon"]["name_en"], p["pokemon"]["name_jp"], p["pokemon"]["category"], p["pokemon"]["height"], p["pokemon"]["weight"], p["pokemon"]["malePer"], p["pokemon"]["femalePer"], p["pokemon"]["catch_rate"], p["pokemon"]["generation"], p["pokemon"]["name_fr"], p["pokemon"]["name_en"], p["pokemon"]["name_jp"]))

        cursor.execute("""
            INSERT INTO pokemon_stats (pokemon_id, hp, atk, def, spe_atk, spe_def, vit)
            VALUES (%s, %s, %s, %s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE hp=%s, atk=%s, def=%s, spe_atk=%s, spe_def=%s, vit=%s
        """, (p["stats"]["pokemon_id"], p["stats"]["hp"], p["stats"]["atk"], p["stats"]["def"], p["stats"]["spe_atk"], p["stats"]["spe_def"], p["stats"]["vit"], p["stats"]["hp"], p["stats"]["atk"], p["stats"]["def"], p["stats"]["spe_atk"], p["stats"]["spe_def"], p["stats"]["vit"]))

        for t_name in p["types"]:
            cursor.execute("INSERT IGNORE INTO type (name) VALUES (%s)", (t_name,))
            cursor.execute("SELECT id FROM type WHERE name = %s", (t_name,))
            type_result = cursor.fetchone()
            if type_result:
                type_id = type_result[0]
                cursor.execute("INSERT IGNORE INTO pokemon_type (pokemon_id, type_id) VALUES (%s, %s)", (p["pokemon"]["pokedex_id"], type_id))

        for tl in p["talents"]:
            cursor.execute("INSERT IGNORE INTO talent (name) VALUES (%s)", (tl["name"],))
            cursor.execute("SELECT id FROM talent WHERE name = %s", (tl["name"],))
            talent_result = cursor.fetchone()
            if talent_result:
                talent_id = talent_result[0]
                cursor.execute("INSERT IGNORE INTO pokemon_talent (pokemon_id, talent_id, is_hidden) VALUES (%s, %s, %s)", (p["pokemon"]["pokedex_id"], talent_id, tl["is_hidden"]))

    conn.commit()
    print("Insertion réussie.")

except mysql.connector.Error as err:
    print(f"Erreur: {err}")
finally:
    if 'conn' in locals() and conn.is_connected():
        cursor.close()
        conn.close()

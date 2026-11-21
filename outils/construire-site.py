#!/usr/bin/env python3
"""Synchronise les blocs communs du site vitrine (en-tête, pied de page, styles).

Chaque bloc commun est écrit UNE fois dans gabarits/<nom>.html. Dans les pages du site
(fichiers .html de la racine), il est recopié entre deux marqueurs :

    <!-- bloc:entete -->
    ...contenu généré, ne pas modifier dans la page...
    <!-- /bloc:entete -->

Les pages restent des fichiers HTML complets : elles s'ouvrent directement dans un
navigateur, sans serveur ni compilation.

Usage (depuis la racine du projet) :
    python3 outils/construire-site.py              met toutes les pages à jour
    python3 outils/construire-site.py --verifier   signale les pages pas à jour (code de sortie 1)
"""
import re
import sys
from pathlib import Path

RACINE = Path(__file__).resolve().parent.parent
GABARITS = RACINE / "gabarits"
BLOC = re.compile(r"(<!-- bloc:(?P<nom>[a-z0-9-]+) -->\n)(?P<contenu>.*?)(<!-- /bloc:(?P=nom) -->)", re.S)


def charger_gabarits() -> dict[str, str]:
    blocs = {}
    for fichier in sorted(GABARITS.glob("*.html")):
        if fichier.name.startswith("modele-"):
            continue  # modèles de page : copiés à la main, pas recopiés dans les pages
        contenu = fichier.read_text(encoding="utf-8")
        blocs[fichier.stem] = contenu if contenu.endswith("\n") else contenu + "\n"
    return blocs


def synchroniser(page: Path, blocs: dict[str, str]) -> tuple[str, list[str]]:
    """Renvoie le nouveau contenu de la page et la liste des blocs inconnus."""
    inconnus = []

    def remplacer(m: re.Match) -> str:
        nom = m.group("nom")
        if nom not in blocs:
            inconnus.append(nom)
            return m.group(0)
        return m.group(1) + blocs[nom] + m.group(4)

    return BLOC.sub(remplacer, page.read_text(encoding="utf-8")), inconnus


def main() -> int:
    verifier = "--verifier" in sys.argv[1:]
    blocs = charger_gabarits()
    pages = sorted(RACINE.glob("*.html"))
    modifiees, erreurs = [], []

    for page in pages:
        nouveau, inconnus = synchroniser(page, blocs)
        erreurs += [f"{page.name} : bloc « {nom} » sans gabarit dans gabarits/" for nom in inconnus]
        if not BLOC.search(nouveau):
            erreurs.append(f"{page.name} : aucun bloc commun (ajouter les marqueurs, voir gabarits/modele-page.html)")
        if nouveau != page.read_text(encoding="utf-8"):
            modifiees.append(page.name)
            if not verifier:
                page.write_text(nouveau, encoding="utf-8")

    for e in erreurs:
        print("ERREUR", e)
    if verifier:
        for nom in modifiees:
            print("PAS À JOUR", nom)
        print(f"{len(pages)} pages vérifiées, {len(modifiees)} à mettre à jour.")
        return 1 if modifiees or erreurs else 0
    print(f"{len(pages)} pages, {len(modifiees)} mises à jour.")
    return 1 if erreurs else 0


if __name__ == "__main__":
    sys.exit(main())

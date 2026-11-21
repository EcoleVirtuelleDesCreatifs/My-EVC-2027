#!/usr/bin/env python3
"""Construit myevc/myevc-autonome.html : la plateforme MY EVC en UN seul fichier.

Tout est intégré (Bootstrap, icônes, polices, styles, scripts) : le fichier s'ouvre
hors ligne, s'envoie par e-mail ou WhatsApp, sans dossier à côté.

Usage (depuis la racine du projet) :
    python3 outils/construire-autonome.py
À relancer après chaque modification des fichiers de assets/app ou de myevc/index.html.
"""
import base64, re
from pathlib import Path

RACINE = Path(__file__).resolve().parent.parent
SOURCE = RACINE / "myevc" / "index.html"
SORTIE = RACINE / "myevc" / "myevc-autonome.html"
MIME = {".woff2": "font/woff2", ".woff": "font/woff", ".ttf": "font/ttf", ".svg": "image/svg+xml", ".png": "image/png"}


def integrer_urls(css: str, dossier: Path) -> str:
    """Remplace url(fichier) par une donnée base64 (polices, images)."""
    def rempl(m):
        brut = m.group(1).strip("'\"")
        if brut.startswith(("data:", "http", "#")):
            return m.group(0)
        chemin = (dossier / brut.split("?")[0].split("#")[0]).resolve()
        if not chemin.exists() or chemin.suffix == ".ttf":
            return "url()"  # les .ttf de secours ne sont pas nécessaires (woff2 suffit)
        donnees = base64.b64encode(chemin.read_bytes()).decode()
        return f"url(data:{MIME.get(chemin.suffix, 'application/octet-stream')};base64,{donnees})"
    return re.sub(r"url\(([^)]+)\)", rempl, css)


def main():
    html = SOURCE.read_text(encoding="utf-8")

    def css(m):
        chemin = (SOURCE.parent / m.group(1)).resolve()
        return f"<style>/* {chemin.relative_to(RACINE)} */\n{integrer_urls(chemin.read_text(encoding='utf-8'), chemin.parent)}\n</style>"

    def js(m):
        chemin = (SOURCE.parent / m.group(1)).resolve()
        code = chemin.read_text(encoding="utf-8").replace("</script", "<\\/script")
        return f"<script>/* {chemin.relative_to(RACINE)} */\n{code}\n</script>"

    html = re.sub(r'<link rel="stylesheet" href="([^"]+\.css)">', css, html)
    html = re.sub(r'<script src="([^"]+\.js)"></script>', js, html)
    favicon = RACINE / "assets/app/img/symbole-couleur.svg"
    html = html.replace('href="../assets/app/img/symbole-couleur.svg"',
                        f'href="data:image/svg+xml;base64,{base64.b64encode(favicon.read_bytes()).decode()}"')
    # Les liens vers le site vitrine restent relatifs : ils fonctionnent si le fichier est rangé dans myevc/.
    SORTIE.write_text(html, encoding="utf-8")
    print(f"{SORTIE.relative_to(RACINE)} : {SORTIE.stat().st_size/1024/1024:.1f} Mo")


if __name__ == "__main__":
    main()

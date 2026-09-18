#!/usr/bin/env bash
#
# Deploy git-tracked files in this repo to the live site over plain FTP.
#
# Usage:
#   ./deploy.sh                  # upload every git-tracked file
#   ./deploy.sh api.php config.php.example   # upload only the listed files
#   ./deploy.sh --dry-run        # show what would be uploaded, do nothing
#
# Credentials are read from deploy.env (gitignored, next to this script) -
# never pass them on the command line or paste them into a chat/terminal
# that gets logged. Create deploy.env yourself from deploy.env.example.
#
# config.php is intentionally NOT git-tracked (real secrets) so it is never
# uploaded by this script. Upload it by hand once, or add it to the file
# list explicitly when you run this script, e.g.:
#   ./deploy.sh config.php

set -euo pipefail

cd "$(dirname "${BASH_SOURCE[0]}")"

if [ ! -f deploy.env ]; then
    echo "deploy.env not found. Copy deploy.env.example to deploy.env and fill in your FTP details." >&2
    exit 1
fi
# shellcheck disable=SC1091
source deploy.env

: "${FTP_HOST:?FTP_HOST not set in deploy.env}"
: "${FTP_USER:?FTP_USER not set in deploy.env}"
: "${FTP_PASS:?FTP_PASS not set in deploy.env}"
: "${FTP_REMOTE_BASE:?FTP_REMOTE_BASE not set in deploy.env (e.g. /meteo/)}"

DRY_RUN=0
FILES=()
for arg in "$@"; do
    if [ "$arg" = "--dry-run" ]; then
        DRY_RUN=1
    else
        FILES+=("$arg")
    fi
done

if [ ${#FILES[@]} -eq 0 ]; then
    mapfile -t FILES < <(git ls-files)
fi

echo "Deploying ${#FILES[@]} file(s) to ftp://${FTP_HOST}${FTP_REMOTE_BASE}"
[ "$DRY_RUN" = "1" ] && echo "(dry run - no files will actually be uploaded)"

for f in "${FILES[@]}"; do
    remote="${FTP_REMOTE_BASE%/}/${f}"
    if [ "$DRY_RUN" = "1" ]; then
        echo "  would upload: $f -> $remote"
        continue
    fi
    echo "  uploading: $f"
    curl --silent --show-error --ftp-create-dirs \
        --user "${FTP_USER}:${FTP_PASS}" \
        -T "$f" \
        "ftp://${FTP_HOST}${remote}"
done

echo "Done."

#!/usr/bin/env bash
# Build the release zip: dist/buddypress-recaptcha-<version>.zip.
#
# Packages the COMMITTED tree with `git archive`, minus the paths in
# .distignore, so the zip always equals the tagged code. Built assets (min,
# RTL) are committed; this script rebuilds them first and stops if that
# changes anything, so a stale build can never ship.
set -euo pipefail

cd "$(dirname "$0")/.."
SLUG="buddypress-recaptcha"
VERSION=$(grep -m1 -E '^\s*\*?\s*Version:' recaptcha-for-buddypress.php | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')
[ -n "$VERSION" ] || { echo "Could not read Version from the plugin header"; exit 1; }
echo "Building $SLUG $VERSION"

[ -z "$(git status --porcelain)" ] || { echo "Working tree is not clean. Commit or stash first."; exit 1; }

# Rebuild minified and RTL assets; they must already be committed.
[ -d node_modules ] || npm install --silent
npx grunt build >/dev/null
[ -z "$(git status --porcelain)" ] || { echo "The asset build changed committed files. Commit them, then rebuild:"; git status --short; exit 1; }

# Static key/hook contract scan, when the skill is installed on this machine.
CONTRACT_AUDIT="$HOME/.claude/skills/wp-contract-audit/scripts/contract-audit.php"
if [ -f "$CONTRACT_AUDIT" ]; then
	php "$CONTRACT_AUDIT" . || { echo "Contract audit failed. Fix it or baseline it before release."; exit 1; }
fi

EXCLUDES=()
while IFS= read -r line; do
	[[ -z "$line" || "$line" == \#* ]] && continue
	EXCLUDES+=( ":(exclude)$line" )
done <.distignore

mkdir -p dist
ZIP="dist/${SLUG}-${VERSION}.zip"
rm -f "$ZIP"
git archive --format=zip --prefix="${SLUG}/" -o "$ZIP" HEAD -- . "${EXCLUDES[@]}"

echo "Created $ZIP ($(unzip -l "$ZIP" | tail -1 | awk '{print $2}') files)"
echo "Next: install it on a clean site and run 'wp plugin check $SLUG'."

#!/usr/bin/env bash
# Local CI: run before every push. Exits non-zero on the first class of failure.
#   Q1 PHP lint (8.1 syntax floor) . Q2 version triangulation . Q3 PHPStan
#   G  portfolio release gate (gate-check.sh), when installed on this machine
set -uo pipefail

cd "$(dirname "$0")/.."
FAIL=0
section() { printf '\n== %s ==\n' "$1"; }

section "Q1 PHP lint"
if find . -name '*.php' -not -path './vendor/*' -not -path './node_modules/*' -not -path './dist/*' -print0 \
	| xargs -0 -n1 -P4 php -l >/dev/null; then echo ok; else echo "FAIL: syntax errors"; FAIL=1; fi

section "Q2 version triangulation"
HEADER_V=$(grep -m1 -E '^\s*\*?\s*Version:' recaptcha-for-buddypress.php | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')
CONST_V=$(grep -m1 -oE "RFB_PLUGIN_VERSION', '[0-9]+\.[0-9]+\.[0-9]+" recaptcha-for-buddypress.php | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')
README_V=$(grep -m1 -E '^Stable tag:' readme.txt | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')
PKG_V=$(grep -m1 -oE '"version": "[0-9]+\.[0-9]+\.[0-9]+' package.json | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')
echo "header=$HEADER_V const=$CONST_V readme=$README_V package=$PKG_V"
if [ "$HEADER_V" = "$CONST_V" ] && [ "$HEADER_V" = "$README_V" ] && [ "$HEADER_V" = "$PKG_V" ]; then echo ok; else echo "FAIL: version mismatch"; FAIL=1; fi
grep -qE "^= $HEADER_V( |=)" readme.txt && echo "readme changelog has $HEADER_V" || { echo "FAIL: no readme.txt changelog entry for $HEADER_V"; FAIL=1; }

section "Q3 PHPStan (level 5 + baseline)"
if [ -x vendor/bin/phpstan ]; then
	vendor/bin/phpstan analyse --no-progress --memory-limit=2G -c phpstan.neon || FAIL=1
else
	echo "skip: run composer install"
fi

section "G release gate"
GATE="$HOME/.claude/skills/wp-plugin-development/scripts/gate-check.sh"
if [ -f "$GATE" ]; then bash "$GATE" "$PWD" || FAIL=1; else echo "skip: gate-check.sh not installed here"; fi

section "Result"
[ "$FAIL" -eq 0 ] && echo PASS || { echo FAIL; exit 1; }

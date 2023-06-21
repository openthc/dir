#!/bin/bash
#
# Install Helper
#
# SPDX-License-Identifier: GPL-3.0-only
#

set -o errexit
# set -o nounset

BIN_SELF=$(readlink -f "$0")
APP_ROOT=$(dirname "$BIN_SELF")

# Do Stuff
case "$1" in
# Install Stuff
install)

	npm update
	composer update --no-ansi --no-dev --no-progress --quiet --classmap-authoritative

	;;

# Update the Search Thing
update-search)

	./bin/search-update.php

	;;

# Help, the default target
*)

	echo
	echo "You must supply a make command"
	echo
	awk '/^# [A-Z\-].+/ { h=$0 }; /^[0-9a-z\-]+\)/ { printf " \033[0;49;31m%-15s\033[0m%s\n", gensub(/\)$/, "", 1, $$1), h }' "$BIN_SELF" |sort
	echo

esac

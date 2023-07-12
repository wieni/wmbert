#!/usr/bin/env bash

find "$@" -type f -print0 | xargs -0 gsed -i -r \
  -e 's/wmbert/bert/g'

#!/usr/bin/env bash

# Note: On linux you might want to use `sed` instead of `gsed`
# on Mac you might need to install `gsed`. See https://formulae.brew.sh/formula/gnu-sed

find "$@" -type f -print0 | xargs -0 gsed -i -r \
  -e 's/wmbert/bert/g' \
  -e 's/WmBertSelection/BertSelection/g' \
  -e 's/bert\\Plugin\\EntityReferenceListFormatter/bert\\Plugin\\bert\\EntityReferenceListFormatter/g' \
  -e 's/bert\\Plugin\\EntityReferenceLabelFormatter/bert\\Plugin\\bert\\EntityReferenceLabelFormatter/g'

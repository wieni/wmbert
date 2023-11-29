#!/usr/bin/env bash

find "$@" -type f -print0 | xargs -0 gsed -i -r \
  -e 's/wmbert/bert/g' \
  -e 's/WmBertSelection/BertSelection/g' \
  -e 's/bert\\Plugin\\EntityReferenceListFormatter/bert\\Plugin\\bert\\EntityReferenceListFormatter/g' \
  -e 's/bert\\Plugin\\EntityReferenceLabelFormatter/bert\\Plugin\\bert\\EntityReferenceLabelFormatter/g'

#!/bin/bash
CONF=$1
FILE=$2

USAGE="USAGE: tidy.sh tidy.conf file.html"

if [ ! $FILE ]
then
  echo File is required
  echo $USAGE
  exit 1
fi

if [ ! $CONF ]
then
  echo Config is required
  echo $USAGE
  exit 1
fi

echo "Processing $FILE" >&2
tidy -config $CONF $FILE 2>&1 \
  | perl -p -e 's/.*Warning: <img> lacks "alt" attribute\n//'\
  | perl -p -e 's/.*Warning: <link> proprietary attribute "color"\n//'\
  | perl -p -e 's/.*Warning: unescaped & or unknown entity ".*\n//' \
  | perl -p -e 's/.*adjacent hyphens within comment.*\n//'
err=$?

echo "Deleting excess newlines" >&2
cat $FILE | sed -s ':a;N;$!ba;s/\n\n\n/\n\n/g' | sed -s ':a;N;$!ba;s/\n\n\n/\n\n/g' | sed -s ':a;N;$!ba;s/\n\n\n/\n\n/g' | sed -s ':a;N;$!ba;s/\n\n\n/\n\n/g' > t
mv t $FILE
exit $err

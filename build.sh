#!/bin/bash

US=$(pwd)

echo "Removing old site"
cd public
rm -rf *
cd ..

echo "Installing site"
echo ""
hugo
if [ $? -eq 0 ]
then
  echo "Site built"
else
  echo "Failed to build site, aborting" >&2
  exit 1
fi

sleep 0.2

cd public
mv plugins ../t

echo "Cleaning HTML"
echo ""
pwd
find . -type f -name "*.html" -exec ${US}/tidy.sh ${US}/tidy.conf {} \;
find . -type f -name "*.php" -exec ${US}/tidy.sh ${US}/tidy.conf {} \;
if [ $? -eq 0 ]
then
  echo "HTML Tidy complete"
  echo ""
else
  echo "Failed to tidy HTML!!!" >&2
fi

mv ../t plugins

echo "Gzipping sitemaps"
gzip -k sitemap.xml
# gzip -k feed.xml

echo "Removing trailing whitespace"
find . -type f -name "*.html" -exec perl -pi -e 's/ +$//g' {} \;
find . -type f -name "*.php" -exec perl -pi -e 's/ +$//g' {} \;

cd $US

echo Done

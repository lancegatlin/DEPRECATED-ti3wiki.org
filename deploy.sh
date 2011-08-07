#!/bin/bash

# Change to html directory
cd html

# Wikimap Images
echo DOWNLOADING: Wikimap Images
curl -Lk https://github.com/lancegatlin/TI3WikiMap-Images/tarball/v0.\
1.\
0 | tar zx
mv lancegatlin-TI3WikiMap-Images* wikimap

# Change to wiki directory
cd w

# Get mediawiki 1.16.5
echo DOWNLOADING: Mediawiki 1.16.5...
curl -Lk http://download.wikimedia.org/mediawiki/1.16/mediawiki-1.16.5.tar.gz | tar zx
cp -R mediawiki-1.16.5/* .
rm -rf mediawiki-1.16.5
cp LocalSettings.db.pwd.template LocalSettings.db.pwd

# Get skin
cd skins
echo DOWNLOADING: TI3 Skin
curl -Lk https://github.com/lancegatlin/MediaWiki-Skin--TI3Wiki/tarball/v0.\
1.\
0 | tar zx
mv lancegatlin-MediaWiki-Skin--TI3Wiki* ti3skin
cp -R ti3skin/* .
rm -rf ti3skin
cd ..

# Get extensions
cd extensions

# Cite for MW1.16
echo DOWNLOADING MWExt1.16: Cite
curl -Lk http://upload.wikimedia.org/ext-dist/Cite-MW1.16-r62678.tar.gz | tar zx

# ParserFunctions for MW1.16
echo DOWNLOADING MWExt1.16: ParserFunctions
curl -Lk http://upload.wikimedia.org/ext-dist/ParserFunctions-MW1.16-r62695.tar.gz | tar zx

# Tree and Menu for MW1.16
echo DOWNLOADING MWExt1.16: TreeAndMenu
curl -Lk http://upload.wikimedia.org/ext-dist/TreeAndMenu-MW1.16-r66255.tar.gz | tar zx

# UserAdmin
echo DOWNLOADING MWExt1.16: UserAdmin
curl -Lk https://github.com/lancegatlin/MediaWiki-Extension--UserAdmin/tarball/v0.9.0 | tar zx
mv lancegatlin-MediaWiki-Extension--UserAdmin-361458a UserAdmin

# SafePHP
echo DOWNLOADING MWExt1.16: SafePHP
curl -Lk https://github.com/lancegatlin/MediaWiki-Extension--SafePHP/tarball/v0.1.\
0 | tar zx
mv lancegatlin-MediaWiki-Extension--SafePHP* SafePHP

# InPlaceCache
echo DOWNLOADING MWExt1.16: InPlaceCache
curl -Lk https://github.com/lancegatlin/MediaWiki-Extension--InPlaceCache/tarball/v0.1.\
0 | tar zx
mv lancegatlin-MediaWiki-Extension--InPlaceCache* InPlaceCache

# TI3WikiMap1
echo DOWNLOADING MWExt1.16: TI3WikiMap1
curl -Lk https://github.com/lancegatlin/MediaWiki-Extension--TI3WikiMap1/tarball/v0.1.\
0 | tar zx
mv lancegatlin-MediaWiki-Extension--TI3WikiMap1* TI3WikiMap1

# TI3WikiMap2
echo DOWNLOADING MWExt1.16: TI3WikiMap2
curl -Lk https://github.com/lancegatlin/MediaWiki-Extension--TI3WikiMap2/tarball/v0.\
1.\
0 | tar zx
mv lancegatlin-MediaWiki-Extension--TI3WikiMap2* TI3WikiMap2

# ReplaceOnSave
echo DOWNLOADING MWExt1.16: ReplaceOnSave
curl -Lk https://github.com/lancegatlin/MediaWiki-Extension--ReplaceOnSave/tarball/v0.\
1.\
0 | tar zx
mv lancegatlin-MediaWiki-Extension--ReplaceOnSave* ReplaceOnSave


# Secure
echo DOWNLOADING MWExt1.16: Secure
curl -Lk https://github.com/lancegatlin/MediaWiki-Extension--Secure/tarball/v0.\
1.\
2 | tar zx
mv lancegatlin-MediaWiki-Extension--Secure* Secure
cp Secure/secure.pwd.template Secure/secure.pwd

# Navigate back to wiki directory
cd ..

# File permissions for MediaWiki
echo Setting permissions...
chgrp -R www *
chmod 770 images
chmod 770 cache

# Navigate back to deploy directory
cd ../../

echo REMINDER: Create database, database user and import wiki data
echo REMINDER: Edit LocalSettings.pwd and set server database password
echo REMINDER: Edit secure.pwd.template and set server password
echo REMINDER: mw_ti3wiki-test.sql contains test wiki data
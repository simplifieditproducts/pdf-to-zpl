#!/usr/bin/env bash

NEW_VERSION=$(jq -r ".version" composer.json)
echo "Attempting to publish $NEW_VERSION of pdf-to-zpl" 
git tag $NEW_VERSION
git push origin $NEW_VERSION
open https://packagist.org/packages/faerber/pdf-to-zpl


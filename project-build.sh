#!/bin/bash
# Version 20170122
BUILD_NUMBER=$1
REGISTRY_URL=$2
REGISTRY_NAME=$3
ERROR_CODE=0

echo "- BUILD_NUMBER: $1"
echo "- REGISTRY_URL: $2"
echo "- REGISTRY_NAME: $3"

if !([ $1 -a $2 -a $3 ]); then
    echo '--- PRAMA ERROR ---'
    exit 255
fi

if [ -f IMAGEIDS ]; then
    rm IMAGEIDS
fi

chmod a+x -R **/*.sh

echo "--- INFO: Start to Build Docker Images ---"
while read line
do
    if [ ! -d $line -a ! '' == $line ]; then
        echo "--- INFO: '$line' not exist. Continue ---"
        continue
    fi
    echo "--- INFO: Building '$line' ---"
    echo -n "$REGISTRY_URL/$REGISTRY_NAME/$line:" >> IMAGEIDS
    docker build -q -t $REGISTRY_URL/$REGISTRY_NAME/$line:$BUILD_NUMBER $line/ >> IMAGEIDS
    ERROR_CODE=`expr $ERROR_CODE + $?`
done < BUILDLIST
echo "--- INFO: Building completed ---"

if [ -f tmp_shell.sh ]; then
    rm tmp_shell.sh
fi

while read line
do
    echo $line | sed "s/\(.*\):sha256:\(.*\)/docker tag \2 \1:latest/g" >> tmp_shell.sh
    echo $line | sed "s/\(.*\):sha256:\(.*\)/docker tag \2 \1:$BUILD_NUMBER/g" >> tmp_shell.sh
    echo $line | sed "s/\(.*\):sha256:\(.*\)/docker push \1:latest/g" >> tmp_shell.sh
    echo $line | sed "s/\(.*\):sha256:\(.*\)/docker push \1:$BUILD_NUMBER/g" >> tmp_shell.sh
done < IMAGEIDS

if [ -f PUSH.log ]; then
    rm PUSH.log
fi

if [ ! -f tmp_shell.sh ]; then
    echo '--- ERROR! tmp_shell.sh is missing! ---'
    exit 255
fi

echo "--- INFO: Start to push images ---"
bash tmp_shell.sh
ERROR_CODE=`expr $ERROR_CODE + $?`
exit $ERROR_CODE

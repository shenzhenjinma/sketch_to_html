#!/bin/sh
sketchDir=$1
apiUrl=$(curl https://kuaiui.cn/api/Home/Index/sketch_upload_url)
cd "$sketchDir"
if [ -f "kuaiui.zip" ]; then
  rm kuaiui.zip
fi
if [ -f "success" ]; then
  rm success
fi
zip  -r kuaiui.zip  ./*
status=$(curl -F "filename=@kuaiui.zip" ${apiUrl})
if [ -f "undel" ]; then
  echo "undel"
else
  rm -fr /tmp/kuaiui/*
fi
touch ${status}
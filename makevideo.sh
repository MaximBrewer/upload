#!/bin/bash

read w h name suffix path < <(identify -format "%w %h %t %e %d" $1)

cd $path;

mw=$((100 * $w * 1 / ($h * 1)))
dw=$(($mw - 75))
crw=0
if (($dw > 0)); then
    if (($dw > 10)); then
        crw=10
    else
        crw=$(($dw))
    fi
fi
crh=0
if (($dw < 0)); then
    if (($dw < -10)); then
        crh=10
    else
        crh=$(($dw * -1))
    fi
fi

convert $1 -crop $(($w - ($w * $crw / 100)))x$(($h - ($h * $crh / 100)))+$(($w * $crw / 100 / 2))+$(($h * $crh / 100 / 2)) "tmp.png"

convert "tmp.png" -scale 660X880 -bordercolor "rgba(0,0,0,0.4)" -border 1 "tmp.png"

read wn hn < <(identify -format "%w %h" "tmp.png")

wdn=$((720 + (660 - $wn) / 2))
hdn=$((115 + (880 - $hn) / 2))

echo $wdn
echo $hdn

convert -background None -virtual-pixel transparent -background transparent \
    \( "tmp.png" +distort Perspective '0,0 0,0  880,10 880,-10  880,630 880,680  0,660 0,660' \) \
    "perspective.png"

convert "perspective.png"  -background transparent -rotate 1 "rotate.png"

composite -geometry '+'$wdn'+'$hdn "rotate.png" /home/debian/www/storage/tmp/photo.png /home/debian/www/storage/tmp/mask.jpg "final.jpg"

jpegoptim "final.jpg" --strip-all

ffmpeg -y -hide_banner -loglevel panic -filter_complex aevalsrc=0 -loop 1 -i "final.jpg" -t 5.8 "final.mp4"
ffmpeg -y -hide_banner -loglevel panic -i "final.mp4" -i /home/debian/www/storage/tmp/sound.aac -c:a copy -c:v libx264 -map 0:v:0 -map 1:a:0 "final.ts"
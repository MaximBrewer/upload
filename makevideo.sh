#!/bin/bash

read w h name suffix path < <(identify -format "%w %h %t %e %d" $1)

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

convert $1 -crop $(($w - ($w * $crw / 100)))x$(($h - ($h * $crh / 100)))+$(($w * $crw / 100 / 2))+$(($h * $crh / 100 / 2)) $3/tmp/orders/$2/tmp.png

convert $3/tmp/orders/$2/tmp.png -scale 660X880 -bordercolor "rgba(0,0,0,0.4)" -border 1 $3/tmp/orders/$2/tmp.png

read wn hn < <(identify -format "%w %h" "$3/tmp/orders/$2/tmp.png")

wdn=$((720 + (660 - $wn) / 2))
hdn=$((115 + (880 - $hn) / 2))

echo $wdn
echo $hdn

convert -background none -virtual-pixel transparent -background transparent \
    \( $3/tmp/orders/$2/tmp.png +distort Perspective '0,0 0,0  880,10 880,-10  880,630 880,680  0,660 0,660' \) \
    $3/tmp/orders/$2/perspective.png

convert $3/tmp/orders/$2/perspective.png  -background transparent -rotate 1 $3/tmp/orders/$2/rotate.png

composite -geometry '+'$wdn'+'$hdn $3/tmp/orders/$2/rotate.png $3/tmp/photo.png $3/tmp/mask.jpg $3/tmp/orders/$2/final.jpg

jpegoptim $3/tmp/orders/$2/final.jpg --strip-all

ffmpeg -y -hide_banner -loglevel panic -loop 1 -i $3/tmp/orders/$2/final.jpg -t 5.8 -framerate 30 -profile:v main -pix_fmt:v yuv420p -c:v libx264 -s 1920x1080 -b:v 10M $3/tmp/orders/$2/final.mp4
ffmpeg -y -hide_banner -loglevel panic -i $3/tmp/orders/$2/final.mp4 -i $3/tmp/sound.aac -map 0:v:0 -map 1:a:0 -ar 44100 -c:v copy -c:a aac -b:a 164k  $3/tmp/orders/$2/final.ts


# ffmpeg -y -hide_banner -filter_complex aevalsrc=0 -loop 1 -i /home/debian/www/kinderhappynewyear.space/storage/tmp/orders/284573/final.jpg -t 5.8 -framerate 25 -profile:v main -pix_fmt:v yuv420p -c:v libx264 -s 1920x1080 -b:v 10M /home/debian/www/kinderhappynewyear.space/storage/tmp/orders/284573/final.mp4

# ffmpeg -y -hide_banner -i /home/debian/www/kinderhappynewyear.space/storage/tmp/orders/284573/final.mp4 -i /home/debian/www/kinderhappynewyear.space/storage/tmp/sound.aac  -map 0:v:0 -map 1:a:0 -ar 44100 -c:v copy -c:a aac -b:a 164k /home/debian/www/kinderhappynewyear.space/storage/tmp/orders/284573/final.ts
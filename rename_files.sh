#!/usr/bin/env bash
for f in /home/tri/tmp/ofo/fram_denzo_man_bos_pentius/denso/new_img_0108/*; do if [[ "$f" == *"_BOT"* ]]; then rm "$f"; fi; done
function flashplayer(url,image, width, height, start, swf_url)
{
	document.write('<OBJECT id=flashplayer \n');
	document.write('	height=' + height + '\n');
	document.write('	hspace=5\n');
	document.write('	width=' + width + '\n');
	document.write('	align=left \n');
	document.write('	classid=clsid:d27cdb6e-ae6d-11cf-96b8-444553540000 \n');
	document.write('	name=flashplayer\n');
	//document.write('	type="application/x-shockwave-flash"\n');
	document.write('	codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0">\n');
	document.write('	<PARAM NAME="allowScriptAccess" VALUE="sameDomain">\n');
	document.write('	<PARAM NAME="movie" VALUE="' + swf_url + 'swf/flvplayer.swf?file=' + url + '&defaultImage=' + image + '&startPlayingOnload=' + start + '">\n');
	document.write('	<PARAM NAME="loop" VALUE="false">\n');
	document.write('	<PARAM NAME="menu" VALUE="false">\n');
	document.write('	<PARAM NAME="quality" VALUE="high">\n');
	document.write('	<PARAM NAME="scale" VALUE="noscale">\n');
	document.write('	<PARAM NAME="salign" VALUE="lt">\n');
	document.write('	<PARAM NAME="bgcolor" VALUE="#000000">\n');
	document.write('	<embed src="' + swf_url + 'swf/flvplayer.swf?file=' + url + '&defaultImage=' + image + '&startPlayingOnload=' + start + '" \n');
	document.write('		id="flashplayer" \n');
	document.write('		loop="false" \n');
	document.write('		menu="false" \n');
	document.write('		quality="high" \n');
	document.write('		scale="noscale" \n');
	document.write('		salign="lt" \n');
	document.write('		width=' + width + ' \n');
	document.write('		align="left"\n');
	document.write('		hspace="5"\n');
	document.write('		height=' + height + '\n');
	document.write('		name="flashplayer" \n');
	document.write('		align="middle" \n');
	document.write('		bgcolor="#000000" \n');
	document.write('		allowScriptAccess="sameDomain" \n');
	document.write('		type="application/x-shockwave-flash"\n');
	document.write('		pluginspage="http://www.macromedia.com/go/getflashplayer" />\n');
	document.write('	</embed>\n');
	document.write('</OBJECT>\n');
}

function mediaplayer(url, width, height, start)
{
	document.write('<OBJECT id="mediaPlayer" width="' + width + '" height="' + height + '" align="left"\n');
	document.write('	classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95"\n');
	document.write('	codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701"\n');
	document.write('	standby="Loading Microsoft Windows Media Player components..." type="application/x-oleobject">\n');
	document.write('	<param name="fileName" value="' + url + '">\n');
	document.write('	<param name="animationatStart" value="true">\n');
	document.write('	<param name="transparentatStart" value="false">\n');
	document.write('	<param name="autoStart" value="' + start + '">\n');
	document.write('	<param name="showControls" value="true">\n');
	document.write('	<param name="loop" value="false">\n');
	document.write('	<EMBED type="application/x-mplayer2"\n');
	document.write('		pluginspage="http://microsoft.com/windows/mediaplayer/en/download/"\n');
	document.write('		id="mediaPlayer" name="mediaPlayer"\n');
	document.write('		bgcolor="darkblue" showcontrols="true"\n');
	document.write('		showdisplay="0" animationatStart="true"\n');
	document.write('		transparentatStart="false"\n');
	document.write('		width="' + width + '"\n');
	document.write('		height="' + height + '"\n');
	//document.write('		align="left"\n');
	document.write('		src="' + url + '"\n');
	document.write('		autostart="' + start + '"\n');
	document.write('		loop="false">\n');
	document.write('	</EMBED>\n');
	document.write('</OBJECT>\n');
}

<?php

function barcode_image( $text, $image_type = "png", $codetype = "code128" )
{
    gd_errors_checks( $image_type );
    $errors = "";
    $img = imagecreate( 100, 30 );
    $black = imagecolorallocate( $img, 0, 0, 0 );
    $white = imagecolorallocate( $img, 255, 255, 255 );
    imagefill( $img, 0, 0, $white );
    if ( $codetype == "code128" )
    {
        if ( preg_match( "/[^0-9A-Z\\-\\*\\+$\\%\\/\\.\\s\"\\!\\#\\&'\\(\\)\\,\\:\\;\\<\\=\\>\\?\\@\\[\\]\\^\\_\\`\\{\\|\\}\\~\\\\]/i", $text ) || strlen( $text ) < 1 )
        {
            $errors .= "Invalid code128";
        }
        else
        {
            $img = barcode_code128( $text );
        }
    }
    else if ( $codetype == "ean13" )
    {
        if ( preg_match( "/[^0-9]/", $text ) || strlen( $text ) != 12 )
        {
            $errors .= "Invalid ean13";
        }
        else
        {
            $img = barcode_ean13( $text );
        }
    }
    else if ( $codetype == "code39" )
    {
        if ( preg_match( "/[^0-9A-Z\\-*+$%\\/. ]/", $text ) )
        {
            $errors .= "Invalid code39";
        }
        else
        {
            $img = barcode_code39( $text );
        }
    }
    else if ( $codetype == "int25" )
    {
        if ( preg_match( "/[^0-9]/", $text ) || strlen( $text ) != 12 )
        {
            $errors .= "Invalid int25";
        }
        else
        {
            $img = barcode_int25( $text );
        }
    }
    else if ( $codetype == "upca" )
    {
        if ( preg_match( "/[^0-9]/", $text ) || strlen( $text ) != 12 )
        {
            $errors .= "Invalid upca";
        }
        else
        {
            $img = barcode_upca( $text );
        }
    }
    else if ( $codetype == "postnet" )
    {
        if ( preg_match( "/[^0-9]/", $text ) )
        {
            $errors .= "Invalid postnet";
        }
        else
        {
            $img = barcode_postnet( $text );
        }
    }
    else
    {
        $errors .= INVALID_CODE_TYPE_MSG;
    }
    if ( $errors )
    {
        barcode_message( $errors, $img );
    }
    return $img;
}

function draw_barcode( $text, $image_type = "png", $codetype = "code128" )
{
    $img = barcode_image( $text, $image_type, $codetype );
    if ( $image_type == "png" )
    {
        header( "Content-type: image/png" );
        imagepng( $img );
    }
    else if ( $image_type == "gif" )
    {
        header( "Content-type: image/gif" );
        imagegif( $img );
    }
    else if ( $image_type == "jpg" )
    {
        header( "Content-type: image/jpeg" );
        imagejpeg( $img );
    }
    imagedestroy( $img );
}

function save_barcode( $filename, $text, $image_type = "png", $codetype = "code128" )
{
    $img = barcode_image( $text, $image_type, $codetype );
    if ( $image_type == "png" )
    {
        imagepng( $img, $filename );
    }
    else if ( $image_type == "gif" )
    {
        imagegif( $img, $filename );
    }
    else if ( $image_type == "jpg" )
    {
        imagejpeg( $img, $filename );
    }
    imagedestroy( $img );
}

function barcode_message( $text, &$img )
{
    $black = imagecolorallocate( $img, 0, 0, 0 );
    $white = imagecolorallocate( $img, 255, 255, 255 );
    $image_width = imagesx( $img );
    $image_height = imagesy( $img );
    $font = 2;
    $xcenter = $image_width / 2 - strlen( $text ) * imagefontwidth( $font ) / 2;
    $ycenter = 0;
    imagefilledrectangle( $img, $xcenter, $ycenter, $xcenter + strlen( $text ) * imagefontwidth( $font ), $ycenter + imagefontheight( $font ), $white );
    imagestring( $img, $font, $xcenter, $ycenter, $text, $black );
}

function barcode_license_message( &$img )
{
    if ( function_exists( "va_license_check" ) )
    {
        list( $host_valid, $license_expired, $va_code ) = va_license_check( );
    }
    else
    {
        $host_valid = false;
        $license_expired = true;
    }
    if ( !$host_valid || $license_expired )
    {
        barcode_message( "ViArt", &$img );
    }
}

function gd_errors_checks( $image_type = "png" )
{
    $error = "";
    if ( !function_exists( "gd_info" ) )
    {
        $error = GD_LIBRARY_ERROR_MSG;
    }
    else if ( $image_type == "png" )
    {
        if ( !( imagetypes( ) & IMG_PNG ) )
        {
            $error = "PNG image format is not supported by GD.";
        }
    }
    else if ( $image_type == "gif" )
    {
        if ( !( imagetypes( ) & IMG_GIF ) )
        {
            $error = "GIF image format is not supported by GD.";
        }
    }
    else if ( $image_type == "jpg" )
    {
        if ( !( imagetypes( ) & IMG_JPG ) )
        {
            $error = "JPEG image format is not supported by GD.";
        }
    }
    else
    {
        $error = "Invalid image format. Format must be png, jpg or png";
    }
    if ( $error )
    {
        exit( $error );
    }
}

function barcode_code128( $text, $barcodeheight = 60 )
{
    $barwidth = 1;
    $font = 2;
    $code = array( "0" => "212222", "1" => "222122", "2" => "222221", "3" => "121223", "4" => "121322", "5" => "131222", "6" => "122213", "7" => "122312", "8" => "132212", "9" => "221213", "10" => "221312", "11" => "231212", "12" => "112232", "13" => "122132", "14" => "122231", "15" => "113222", "16" => "123122", "17" => "123221", "18" => "223211", "19" => "221132", "20" => "221231", "21" => "213212", "22" => "223112", "23" => "312131", "24" => "311222", "25" => "321122", "26" => "321221", "27" => "312212", "28" => "322112", "29" => "322211", "30" => "212123", "31" => "212321", "32" => "232121", "33" => "111323", "34" => "131123", "35" => "131321", "36" => "112313", "37" => "132113", "38" => "132311", "39" => "211313", "40" => "231113", "41" => "231311", "42" => "112133", "43" => "112331", "44" => "132131", "45" => "113123", "46" => "113321", "47" => "133121", "48" => "313121", "49" => "211331", "50" => "231131", "51" => "213113", "52" => "213311", "53" => "213131", "54" => "311123", "55" => "311321", "56" => "331121", "57" => "312113", "58" => "312311", "59" => "332111", "60" => "314111", "61" => "221411", "62" => "431111", "63" => "111224", "64" => "111422", "65" => "121124", "66" => "121421", "67" => "141122", "68" => "141221", "69" => "112214", "70" => "112412", "71" => "122114", "72" => "122411", "73" => "142112", "74" => "142211", "75" => "241211", "76" => "221114", "77" => "413111", "78" => "241112", "79" => "134111", "80" => "111242", "81" => "121142", "82" => "121241", "83" => "114212", "84" => "124112", "85" => "124211", "86" => "411212", "87" => "421112", "88" => "421211", "89" => "212141", "90" => "214121", "91" => "412121", "92" => "111143", "93" => "111341", "94" => "131141", "95" => "114113", "96" => "114311", "97" => "411113", "98" => "411311", "99" => "113141", "100" => "114131", "101" => "311141", "102" => "411131" );
    $startcode = "211214";
    $stopcode = "2331112";
    $checksum = 104;
    $allbars = $startcode;
    $bars = "";
    $i = 0;
    for ( ; $i < strlen( $text ); ++$i )
    {
        $char = $text[$i];
        $val = ord( $char ) - 32;
        $checksum += $val * ( $i + 1 );
        $bars = $code[ord( $char ) - 32];
        $allbars = $allbars.$bars;
    }
    $checkdigit = $checksum % 103;
    $bars = $code[$checkdigit];
    $allbars = $allbars.$bars.$stopcode;
    $barcodewidth = 20;
    $i = 0;
    for ( ; $i < strlen( $allbars ); ++$i )
    {
        $nval = $allbars[$i];
        $barcodewidth += $nval * $barwidth;
    }
    $barcodelongheight = ( integer )( imagefontheight( $font ) / 2 ) + $barcodeheight;
    $img = imagecreate( $barcodewidth, $barcodelongheight + imagefontheight( $font ) + 1 );
    $black = imagecolorallocate( $img, 0, 0, 0 );
    $white = imagecolorallocate( $img, 255, 255, 255 );
    imagefill( $img, 0, 0, $white );
    imagestring( $img, $font, $barcodewidth / 2 - strlen( $text ) / 2 * imagefontwidth( $font ), $barcodeheight + imagefontheight( $font ) / 2, $text, $black );
    $xpos = 10;
    $bar = 1;
    $i = 0;
    for ( ; $i < strlen( $allbars ); ++$i )
    {
        $nval = $allbars[$i];
        $width = $nval * $barwidth;
        if ( $bar == 1 )
        {
            imagefilledrectangle( $img, $xpos, 0, $xpos + $width - 1, $barcodelongheight, $black );
            $xpos += $width;
            $bar = 0;
        }
        else
        {
            $xpos += $width;
            $bar = 1;
        }
    }
    barcode_license_message( &$img );
    return $img;
}

function barcode_ean13( $text, $barcodeheight = 50 )
{
    if ( empty( $text ) )
    {
        exit( "Error: empty EAN string" );
    }
    if ( strlen( $text ) != 12 )
    {
        exit( "Error: EAN string must be 12 symbols" );
    }
    $even_chars = 0;
    $odd_chars = 0;
    $i = 0;
    for ( ; $i < 12; ++$i )
    {
        $onechar = substr( $text, $i, 1 );
        if ( $i % 2 == 0 )
        {
            $odd_chars += ( integer )$onechar;
        }
        else
        {
            $even_chars += ( integer )$onechar;
        }
    }
    $summ = $odd_chars + $even_chars * 3;
    $control_symbol = 10 * ceil( $summ / 10 ) - $summ;
    $text .= $control_symbol;
    $font = 2;
    $barwidth = 1;
    $number_set = array( "0" => array( "A" => array( 0, 0, 0, 1, 1, 0, 1 ), "B" => array( 0, 1, 0, 0, 1, 1, 1 ), "C" => array( 1, 1, 1, 0, 0, 1, 0 ) ), "1" => array( "A" => array( 0, 0, 1, 1, 0, 0, 1 ), "B" => array( 0, 1, 1, 0, 0, 1, 1 ), "C" => array( 1, 1, 0, 0, 1, 1, 0 ) ), "2" => array( "A" => array( 0, 0, 1, 0, 0, 1, 1 ), "B" => array( 0, 0, 1, 1, 0, 1, 1 ), "C" => array( 1, 1, 0, 1, 1, 0, 0 ) ), "3" => array( "A" => array( 0, 1, 1, 1, 1, 0, 1 ), "B" => array( 0, 1, 0, 0, 0, 0, 1 ), "C" => array( 1, 0, 0, 0, 0, 1, 0 ) ), "4" => array( "A" => array( 0, 1, 0, 0, 0, 1, 1 ), "B" => array( 0, 0, 1, 1, 1, 0, 1 ), "C" => array( 1, 0, 1, 1, 1, 0, 0 ) ), "5" => array( "A" => array( 0, 1, 1, 0, 0, 0, 1 ), "B" => array( 0, 1, 1, 1, 0, 0, 1 ), "C" => array( 1, 0, 0, 1, 1, 1, 0 ) ), "6" => array( "A" => array( 0, 1, 0, 1, 1, 1, 1 ), "B" => array( 0, 0, 0, 0, 1, 0, 1 ), "C" => array( 1, 0, 1, 0, 0, 0, 0 ) ), "7" => array( "A" => array( 0, 1, 1, 1, 0, 1, 1 ), "B" => array( 0, 0, 1, 0, 0, 0, 1 ), "C" => array( 1, 0, 0, 0, 1, 0, 0 ) ), "8" => array( "A" => array( 0, 1, 1, 0, 1, 1, 1 ), "B" => array( 0, 0, 0, 1, 0, 0, 1 ), "C" => array( 1, 0, 0, 1, 0, 0, 0 ) ), "9" => array( "A" => array( 0, 0, 0, 1, 0, 1, 1 ), "B" => array( 0, 0, 1, 0, 1, 1, 1 ), "C" => array( 1, 1, 1, 0, 1, 0, 0 ) ) );
    $number_set_left_coding = array( "0" => array( "A", "A", "A", "A", "A", "A" ), "1" => array( "A", "A", "B", "A", "B", "B" ), "2" => array( "A", "A", "B", "B", "A", "B" ), "3" => array( "A", "A", "B", "B", "B", "A" ), "4" => array( "A", "B", "A", "A", "B", "B" ), "5" => array( "A", "B", "B", "A", "A", "B" ), "6" => array( "A", "B", "B", "B", "A", "A" ), "7" => array( "A", "B", "A", "B", "A", "B" ), "8" => array( "A", "B", "A", "B", "B", "A" ), "9" => array( "A", "B", "B", "A", "B", "A" ) );
    $barcodewidth = strlen( $text ) * ( 7 * $barwidth ) + 8 + imagefontwidth( $font ) + 1;
    $barcodelongheight = ( integer )( imagefontheight( $font ) / 2 ) + $barcodeheight;
    $img = imagecreate( $barcodewidth, $barcodelongheight + imagefontheight( $font ) + 1 );
    $black = imagecolorallocate( $img, 0, 0, 0 );
    $white = imagecolorallocate( $img, 255, 255, 255 );
    imagefill( $img, 0, 0, $white );
    $key = substr( $text, 0, 1 );
    $xpos = 0;
    imagestring( $img, $font, $xpos, $barcodeheight, $key, $black );
    $xpos = imagefontwidth( $font ) + 1;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    $xpos += $barwidth;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    $set_array = $number_set_left_coding[$key];
    $idx = 1;
    for ( ; $idx < 7; ++$idx )
    {
        $value = substr( $text, $idx, 1 );
        imagestring( $img, $font, $xpos + 1, $barcodeheight, $value, $black );
        foreach ( $number_set[$value][$set_array[$idx - 1]] as $bar )
        {
            if ( $bar )
            {
                imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodeheight, $black );
            }
            $xpos += $barwidth;
        }
    }
    $xpos += $barwidth;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    $xpos += $barwidth;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    $xpos += $barwidth;
    $idx = 7;
    for ( ; $idx < 13; ++$idx )
    {
        $value = substr( $text, $idx, 1 );
        imagestring( $img, $font, $xpos + 1, $barcodeheight, $value, $black );
        foreach ( $number_set[$value]['C'] as $bar )
        {
            if ( $bar )
            {
                imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodeheight, $black );
            }
            $xpos += $barwidth;
        }
    }
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    $xpos += $barwidth;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    barcode_license_message( &$img );
    return $img;
}

function barcode_code39( $text, $barcodeheight = 50 )
{
    if ( preg_match( "/[^0-9A-Z\\-*+$%\\/. ]/", $text ) )
    {
        exit( "Invalid text for Code39" );
    }
    $barthinwidth = 1;
    $barthickwidth = 3;
    $coding_map = array( "0" => "000110100", "1" => "100100001", "2" => "001100001", "3" => "101100000", "4" => "000110001", "5" => "100110000", "6" => "001110000", "7" => "000100101", "8" => "100100100", "9" => "001100100", "A" => "100001001", "B" => "001001001", "C" => "101001000", "D" => "000011001", "E" => "100011000", "F" => "001011000", "G" => "000001101", "H" => "100001100", "I" => "001001100", "J" => "000011100", "K" => "100000011", "L" => "001000011", "M" => "101000010", "N" => "000010011", "O" => "100010010", "P" => "001010010", "Q" => "000000111", "R" => "100000110", "S" => "001000110", "T" => "000010110", "U" => "110000001", "V" => "011000001", "W" => "111000000", "X" => "010010001", "Y" => "110010000", "Z" => "011010000", "-" => "010000101", "*" => "010010100", "+" => "010001010", "\$" => "010101000", "%" => "000101010", "/" => "010100010", "." => "110000100", " " => "011000100" );
    $final_text = "*".$text."*";
    $barcode = "";
    $split_text = preg_split( "//", $final_text, -1, PREG_SPLIT_NO_EMPTY );
    foreach ( $split_text as $character )
    {
        $color = 1;
        $chars = preg_split( "//", $coding_map[$character]."0", -1, PREG_SPLIT_NO_EMPTY );
        foreach ( $chars as $bit )
        {
            $barcode .= $bit == 1 ? str_repeat( "{$color}", $barthickwidth ) : str_repeat( "{$color}", $barthinwidth );
            $color = $color == 0 ? 1 : 0;
        }
    }
    $barcode_len = strlen( $barcode );
    $img = imagecreate( $barcode_len, $barcodeheight );
    $black = imagecolorallocate( $img, 0, 0, 0 );
    $white = imagecolorallocate( $img, 255, 255, 255 );
    $font_height = imagefontheight( "gdFontSmall" );
    $font_width = imagefontwidth( "gdFontSmall" );
    imagefill( $img, 0, 0, $white );
    $xpos = 0;
    $split_text = preg_split( "//", $barcode, -1, PREG_SPLIT_NO_EMPTY );
    foreach ( $split_text as $character_code )
    {
        if ( $character_code == 0 )
        {
            imageline( $img, $xpos, 0, $xpos, $barcodeheight - $font_height - 1, $white );
        }
        else
        {
            imageline( $img, $xpos, 0, $xpos, $barcodeheight - $font_height - 1, $black );
        }
        ++$xpos;
    }
    imagestring( $img, "gdFontSmall", ( $barcode_len - $font_width * strlen( $text ) ) / 2, $barcodeheight - $font_height, $text, $black );
    barcode_license_message( &$img );
    return $img;
}

function barcode_int25( $text )
{
    $barcodeheight = 50;
    $barthinwidth = 1;
    $barthickwidth = 3;
    $coding_map = array( "0" => "00110", "1" => "10001", "2" => "01001", "3" => "11000", "4" => "00101", "5" => "10100", "6" => "01100", "7" => "00011", "8" => "10010", "9" => "01010" );
    $text = trim( $text );
    if ( !preg_match( "/[0-9]/", $text ) )
    {
        exit( "Invalid text for Int25" );
    }
    $text = strlen( $text ) % 2 ? "0".$text : $text;
    $barcodewidth = strlen( $text ) * ( 3 * $barthinwidth + 2 * $barthickwidth ) + strlen( $text ) * 2.5 + ( 7 * $barthinwidth + $barthickwidth ) + 3;
    $img = imagecreate( $barcodewidth, $barcodeheight );
    $black = imagecolorallocate( $img, 0, 0, 0 );
    $white = imagecolorallocate( $img, 255, 255, 255 );
    imagefill( $img, 0, 0, $white );
    $xpos = 0;
    $i = 0;
    for ( ; $i < 2; ++$i )
    {
        $elementwidth = $barthinwidth;
        imagefilledrectangle( $img, $xpos, 0, $xpos + $elementwidth - 1, $barcodeheight, $black );
        $xpos += $elementwidth;
        $xpos += $barthinwidth;
        ++$xpos;
    }
    $idx = 0;
    for ( ; $idx < strlen( $text ); $idx += 2 )
    {
        $oddchar = substr( $text, $idx, 1 );
        $evenchar = substr( $text, $idx + 1, 1 );
        $baridx = 0;
        for ( ; $baridx < 5; ++$baridx )
        {
            $elementwidth = substr( $coding_map[$oddchar], $baridx, 1 ) ? $barthickwidth : $barthinwidth;
            imagefilledrectangle( $img, $xpos, 0, $xpos + $elementwidth - 1, $barcodeheight, $black );
            $xpos += $elementwidth;
            $elementwidth = substr( $coding_map[$evenchar], $baridx, 1 ) ? $barthickwidth : $barthinwidth;
            $xpos += $elementwidth;
            ++$xpos;
        }
    }
    $elementwidth = $barthickwidth;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $elementwidth - 1, $barcodeheight, $black );
    $xpos += $elementwidth;
    $xpos += $barthinwidth;
    ++$xpos;
    $elementwidth = $barthinwidth;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $elementwidth - 1, $barcodeheight, $black );
    barcode_license_message( &$img );
    return $img;
}

function barcode_upcax( $text, $barcodeheight = 50 )
{
    $font = 2;
    $barwidth = 1;
    $number_set = array( "0" => array( "A" => array( 0, 0, 0, 1, 1, 0, 1 ), "B" => array( 0, 1, 0, 0, 1, 1, 1 ), "C" => array( 1, 1, 1, 0, 0, 1, 0 ) ), "1" => array( "A" => array( 0, 0, 1, 1, 0, 0, 1 ), "B" => array( 0, 1, 1, 0, 0, 1, 1 ), "C" => array( 1, 1, 0, 0, 1, 1, 0 ) ), "2" => array( "A" => array( 0, 0, 1, 0, 0, 1, 1 ), "B" => array( 0, 0, 1, 1, 0, 1, 1 ), "C" => array( 1, 1, 0, 1, 1, 0, 0 ) ), "3" => array( "A" => array( 0, 1, 1, 1, 1, 0, 1 ), "B" => array( 0, 1, 0, 0, 0, 0, 1 ), "C" => array( 1, 0, 0, 0, 0, 1, 0 ) ), "4" => array( "A" => array( 0, 1, 0, 0, 0, 1, 1 ), "B" => array( 0, 0, 1, 1, 1, 0, 1 ), "C" => array( 1, 0, 1, 1, 1, 0, 0 ) ), "5" => array( "A" => array( 0, 1, 1, 0, 0, 0, 1 ), "B" => array( 0, 1, 1, 1, 0, 0, 1 ), "C" => array( 1, 0, 0, 1, 1, 1, 0 ) ), "6" => array( "A" => array( 0, 1, 0, 1, 1, 1, 1 ), "B" => array( 0, 0, 0, 0, 1, 0, 1 ), "C" => array( 1, 0, 1, 0, 0, 0, 0 ) ), "7" => array( "A" => array( 0, 1, 1, 1, 0, 1, 1 ), "B" => array( 0, 0, 1, 0, 0, 0, 1 ), "C" => array( 1, 0, 0, 0, 1, 0, 0 ) ), "8" => array( "A" => array( 0, 1, 1, 0, 1, 1, 1 ), "B" => array( 0, 0, 0, 1, 0, 0, 1 ), "C" => array( 1, 0, 0, 1, 0, 0, 0 ) ), "9" => array( "A" => array( 0, 0, 0, 1, 0, 1, 1 ), "B" => array( 0, 0, 1, 0, 1, 1, 1 ), "C" => array( 1, 1, 1, 0, 1, 0, 0 ) ) );
    $number_set_left_coding = array( "0" => array( "A", "A", "A", "A", "A", "A" ), "1" => array( "A", "A", "B", "A", "B", "B" ), "2" => array( "A", "A", "B", "B", "A", "B" ), "3" => array( "A", "A", "B", "B", "B", "A" ), "4" => array( "A", "B", "A", "A", "B", "B" ), "5" => array( "A", "B", "B", "A", "A", "B" ), "6" => array( "A", "B", "B", "B", "A", "A" ), "7" => array( "A", "B", "A", "B", "A", "B" ), "8" => array( "A", "B", "A", "B", "B", "A" ), "9" => array( "A", "B", "B", "A", "B", "A" ) );
    $error = false;
    if ( !preg_match( "/[0-9]/", $text ) || strlen( $text ) != 12 )
    {
        $barcodewidth = 84 * $barwidth + 3 + 5 + 3 + 2 * ( imagefontwidth( $font ) + 1 );
        $error = true;
    }
    else
    {
        $barcodewidth = strlen( $text ) * ( 7 * $barwidth ) + 3 + 5 + 3 + imagefontwidth( $font ) + 1 + imagefontwidth( $font ) + 1;
    }
    $barcodelongheight = ( integer )( imagefontheight( $font ) / 2 ) + $barcodeheight;
    $img = imagecreate( $barcodewidth, $barcodelongheight + imagefontheight( $font ) + 1 );
    $black = imagecolorallocate( $img, 0, 0, 0 );
    $white = imagecolorallocate( $img, 255, 255, 255 );
    imagefill( $img, 0, 0, $white );
    if ( $error )
    {
        $imgerror = imagecreate( $barcodewidth, $barcodelongheight + imagefontheight( $font ) + 1 );
        $red = imagecolorallocate( $imgerror, 255, 0, 0 );
        $black = imagecolorallocate( $imgerror, 0, 0, 0 );
        imagefill( $imgerror, 0, 0, $red );
        imagestring( $imgerror, $font, $barcodewidth / 2 - 5 * imagefontwidth( $font ), $barcodeheight / 2, "Code Error", $black );
    }
    $key = substr( $text, 0, 1 );
    $xpos = 0;
    imagestring( $img, $font, $xpos, $barcodeheight, $key, $black );
    $xpos = imagefontwidth( $font ) + 1;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    $xpos += $barwidth;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    $set_array = $number_set_left_coding[$key];
    foreach ( $number_set['0'][$set_array[0]] as $bar )
    {
        if ( $bar )
        {
            imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
        }
        $xpos += $barwidth;
    }
    $idx = 1;
    for ( ; $idx < 6; ++$idx )
    {
        $value = substr( $text, $idx, 1 );
        imagestring( $img, $font, $xpos + 1, $barcodeheight, $value, $black );
        foreach ( $number_set[$value][$set_array[$idx]] as $bar )
        {
            if ( $bar )
            {
                imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodeheight, $black );
            }
            $xpos += $barwidth;
        }
    }
    $xpos += $barwidth;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    $xpos += $barwidth;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    $xpos += $barwidth;
    $idx = 6;
    for ( ; $idx < 11; ++$idx )
    {
        $value = substr( $text, $idx, 1 );
        imagestring( $img, $font, $xpos + 1, $barcodeheight, $value, $black );
        foreach ( $number_set[$value]['C'] as $bar )
        {
            if ( $bar )
            {
                imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodeheight, $black );
            }
            $xpos += $barwidth;
        }
    }
    $value = substr( $text, 11, 1 );
    foreach ( $number_set[$value]['C'] as $bar )
    {
        if ( $bar )
        {
            imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
        }
        $xpos += $barwidth;
    }
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    $xpos += $barwidth;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $barcodelongheight, $black );
    $xpos += $barwidth;
    imagestring( $img, $font, $xpos + 1, $barcodeheight, $value, $black );
    if ( $error )
    {
        return $imgerror;
    }
    else
    {
        barcode_license_message( &$img );
        return $img;
    }
}

function barcode_upca( $text )
{
    define( "BCD_DEFAULT_TEXT_OFFSET", 2 );
    define( "BCD_DEFAULT_TEXT_FILL_OFFSET", -10 );
    define( "BCS_ALIGN_CENTER", 4 );
    define( "BCS_IMAGE_PNG", 64 );
    define( "BCS_DRAW_TEXT", 128 );
    define( "BCS_STRETCH_TEXT", 256 );
    define( "BCD_DEFAULT_STYLE", BCS_ALIGN_CENTER | BCS_IMAGE_PNG | BCS_DRAW_TEXT | BCS_STRETCH_TEXT );
    $mChars = "0123456789";
    $mCharSetL = array( "0001101", "0011001", "0010011", "0111101", "0100011", "0110001", "0101111", "0111011", "0110111", "0001011" );
    $mCharSetR = array( "1110010", "1100110", "1101100", "1000010", "1011100", "1001110", "1010000", "1000100", "1001000", "1110100" );
    $mValue = $text;
    $xres = 1;
    $mWidth = 120;
    $mHeight = 80;
    $mFont = 2;
    $mStyle = BCD_DEFAULT_STYLE;
    $img = imagecreate( $mWidth, $mHeight );
    $black = imagecolorallocate( $img, 0, 0, 0 );
    $white = imagecolorallocate( $img, 255, 255, 255 );
    imagefill( $img, 0, 0, $white );
    $len = strlen( $mValue );
    $StartSize = $xres * 3;
    $StopSize = $xres * 3;
    $MidSize = $xres * 5;
    $CharSize = $xres * 7;
    $size = $CharSize * $len + $StartSize + $MidSize + $StopSize;
    $cPos = 0;
    $sPos = ( integer )( ( $mWidth - $size ) / 2 );
    $ySize = $mHeight - imagefontheight( $mFont );
    $DrawPos = $sPos;
    imageline( $img, $DrawPos, 0, $DrawPos, $ySize, $black );
    $DrawPos += $xres;
    $DrawPos += $xres;
    imageline( $img, $DrawPos, 0, $DrawPos, $ySize, $black );
    $DrawPos += $xres;
    $i = 0;
    for ( ; $i < 6; ++$i )
    {
        $cchar = $mValue[$i];
        $c = strpos( $mChars, $cchar );
        $cset = $mCharSetL[$c];
        $j = 0;
        for ( ; $j < strlen( $cset ); ++$j )
        {
            if ( intval( substr( $cset, $j, 1 ) ) == 1 )
            {
                imageline( $img, $DrawPos, 0, $DrawPos, $ySize, $black );
            }
            $DrawPos += $xres;
        }
    }
    $DrawPos += $xres;
    imageline( $img, $DrawPos, 0, $DrawPos, $ySize, $black );
    $DrawPos += $xres;
    $DrawPos += $xres;
    imageline( $img, $DrawPos, 0, $DrawPos, $ySize, $black );
    $DrawPos += $xres;
    $DrawPos += $xres;
    $i = 6;
    for ( ; $i < $len; ++$i )
    {
        $cchar = $mValue[$i];
        $c = strpos( $mChars, $cchar );
        $cset = $mCharSetR[$c];
        $j = 0;
        for ( ; $j < strlen( $cset ); ++$j )
        {
            if ( intval( substr( $cset, $j, 1 ) ) == 1 )
            {
                imageline( $img, $DrawPos, 0, $DrawPos, $ySize, $black );
            }
            $DrawPos += $xres;
        }
    }
    imageline( $img, $DrawPos, 0, $DrawPos, $ySize, $black );
    $DrawPos += $xres;
    $DrawPos += $xres;
    imageline( $img, $DrawPos, 0, $DrawPos, $ySize, $black );
    $DrawPos += $xres;
    if ( $mStyle & BCS_DRAW_TEXT )
    {
        $mid = $sPos + $size / 2;
        $len5 = ( strlen( $mCharSetL[$c] ) + 1 ) * $xres * 5;
        $ht = imagefontheight( $mFont );
        imagefilledrectangle( $img, $mid - $len5 - $xres * 2, $ySize + 0 + BCD_DEFAULT_TEXT_OFFSET + BCD_DEFAULT_TEXT_FILL_OFFSET, $mid - $xres * 2, $ySize + 0 + BCD_DEFAULT_TEXT_OFFSET + BCD_DEFAULT_TEXT_FILL_OFFSET + $ht, $white );
        imagefilledrectangle( $img, $mid + $xres * 2, $ySize + 0 + BCD_DEFAULT_TEXT_OFFSET + BCD_DEFAULT_TEXT_FILL_OFFSET, $mid + $len5 + $xres * 2, $ySize + 0 + BCD_DEFAULT_TEXT_OFFSET + BCD_DEFAULT_TEXT_FILL_OFFSET + $ht, $white );
        imagestring( $img, 1 < $mFont - 2 ? $mFont - 2 : 1, $sPos - $xres * 3 - imagefontwidth( 1 < $mFont ? $mFont - 1 : 1 ), $ySize + 0 + BCD_DEFAULT_TEXT_OFFSET + BCD_DEFAULT_TEXT_FILL_OFFSET, $mValue[0], $black );
        $left = $mid - $len5;
        $i = 1;
        for ( ; $i < $len / 2; ++$i )
        {
            imagestring( $img, $mFont, $left + $size / $len * ( $i - 1 ), $ySize + 0 + BCD_DEFAULT_TEXT_OFFSET + BCD_DEFAULT_TEXT_FILL_OFFSET, $mValue[$i], $black );
        }
        $left = $mid + $xres * 4;
        $i = $len / 2;
        for ( ; $i < $len - 1; ++$i )
        {
            imagestring( $img, $mFont, $left + $size / $len * ( $i - $len / 2 ), $ySize + 0 + BCD_DEFAULT_TEXT_OFFSET + BCD_DEFAULT_TEXT_FILL_OFFSET, $mValue[$i], $black );
        }
        imagestring( $img, 1 < $mFont - 2 ? $mFont - 2 : 1, $sPos + $xres * 6 + $size, $ySize + 0 + BCD_DEFAULT_TEXT_OFFSET + BCD_DEFAULT_TEXT_FILL_OFFSET, $mValue[$len - 1], $black );
    }
    barcode_license_message( &$img );
    return $img;
}

function barcode_postnet( $text )
{
    $barshortheight = 17;
    $bartallheight = 25;
    $barwidth = 2;
    $coding_map = array( "0" => "11000", "1" => "00011", "2" => "00101", "3" => "00110", "4" => "01001", "5" => "01010", "6" => "01100", "7" => "10001", "8" => "10010", "9" => "10100" );
    if ( !preg_match( "/[0-9]/", $text ) )
    {
        exit( "Invalid text for PostNet" );
    }
    $barcodewidth = strlen( $text ) * 2 * 5 * $barwidth + $barwidth * 3;
    $img = imagecreate( $barcodewidth, $bartallheight );
    $black = imagecolorallocate( $img, 0, 0, 0 );
    $white = imagecolorallocate( $img, 255, 255, 255 );
    imagefill( $img, 0, 0, $white );
    $xpos = 0;
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $bartallheight, $black );
    $xpos += 2 * $barwidth;
    $idx = 0;
    for ( ; $idx < strlen( $text ); ++$idx )
    {
        $char = substr( $text, $idx, 1 );
        $baridx = 0;
        for ( ; $baridx < 5; ++$baridx )
        {
            $elementheight = substr( $coding_map[$char], $baridx, 1 ) ? 0 : $barshortheight;
            imagefilledrectangle( $img, $xpos, $elementheight, $xpos + $barwidth - 1, $bartallheight, $black );
            $xpos += 2 * $barwidth;
        }
    }
    imagefilledrectangle( $img, $xpos, 0, $xpos + $barwidth - 1, $bartallheight, $black );
    $xpos += 2 * $barwidth;
    barcode_license_message( &$img );
    return $img;
}

?>

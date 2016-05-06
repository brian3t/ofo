<?php


function parse_multipart( $boundary, $is_sub_boundary )
{
    global $fp;
    global $fw;
    global $save_email_copy;
    global $is_boundary;
    global $header_received;
    global $message_headers;
    global $messages;
    global $msg_id;
    global $settings;
    global $attachments_mask;
    global $attachments_dir;
    while ( !feof( $fp ) )
    {
        $line = fgets( $fp, 4096 );
        if ( $save_email_copy )
        {
            fputs( $fw, $line );
        }
        if ( !$is_boundary )
        {
            if ( trim( $line ) == $boundary || trim( $line ) == "--".$boundary )
            {
                $is_boundary = true;
            }
            else
            {
                if ( !( $is_sub_boundary && trim( $line ) == "--".$boundary."--" ) )
                {
                    continue;
                    return;
                }
            }
        }
        else if ( !$header_received )
        {
            if ( trim( $line ) == "" )
            {
                $header_received = true;
                $messages[$msg_id] = $message_headers;
                $messages[$msg_id]['encoding'] = "";
                $messages[$msg_id]['attachment'] = false;
                $messages[$msg_id]['inline'] = false;
                $messages[$msg_id]['body'] = "";
                $messages[$msg_id]['content-type-value'] = "";
                $sub_boundary = "";
                if ( isset( $message_headers['content-type'] ) && preg_match( "/^([^;\n\r]*)/s", $message_headers['content-type'], $match ) )
                {
                    $messages[$msg_id]['content-type-value'] = trim( strtolower( $match[1] ) );
                    if ( preg_match( "/delsp=([^\\s]+)/si", $message_headers['content-type'], $match ) )
                    {
                        $messages[$msg_id]['content-type-delsp'] = trim( $match[1] );
                    }
                    if ( preg_match( "/format=([^\\s]+)/si", $message_headers['content-type'], $match ) )
                    {
                        $messages[$msg_id]['content-type-format'] = trim( $match[1] );
                    }
                    if ( preg_match( "/boundary=\"(.+)\"/i", $message_headers['content-type'], $match ) )
                    {
                        $sub_boundary = $match[1];
                    }
                    else if ( preg_match( "/boundary=([^\\s]+)/i", $message_headers['content-type'], $match ) )
                    {
                        $sub_boundary = rtrim( $match[1], ";" );
                    }
                }
                if ( strlen( $sub_boundary ) )
                {
                    $is_boundary = false;
                    $header_received = false;
                    $message_headers = array( );
                    parse_multipart( $sub_boundary, true );
                }
                else
                {
                    if ( isset( $message_headers['content-transfer-encoding'] ) && preg_match( "/^([^;\n\r]*)/s", $message_headers['content-transfer-encoding'], $match ) )
                    {
                        $messages[$msg_id]['encoding'] = trim( strtolower( $match[1] ) );
                    }
                    if ( isset( $message_headers['content-disposition'] ) && ( preg_match( "/filename=/i", $message_headers['content-disposition'] ) || preg_match( "/attachment/si", $message_headers['content-disposition'], $match ) ) || preg_match( "/name=\"(.+)\"/si", $messages[$msg_id]['content-type'] ) || preg_match( "/name=([^\\s]+)/si", $messages[$msg_id]['content-type'] ) )
                    {
                        $messages[$msg_id]['attachment'] = true;
                    }
                    else if ( isset( $message_headers['content-disposition'] ) && preg_match( "/inline/si", $message_headers['content-disposition'], $match ) )
                    {
                        $messages[$msg_id]['inline'] = true;
                    }
                    if ( $messages[$msg_id]['content-type-value'] && $messages[$msg_id]['content-type-value'] != "text/plain" && $messages[$msg_id]['content-type-value'] != "text/html" )
                    {
                        $messages[$msg_id]['attachment'] = true;
                    }
                    if ( $messages[$msg_id]['attachment'] )
                    {
                        $messages[$msg_id]['filename'] = "";
                        if ( isset( $message_headers['content-disposition'] ) && preg_match( "/filename=/i", $message_headers['content-disposition'] ) )
                        {
                            if ( preg_match( "/filename=\"([^\"]+)\"/si", $message_headers['content-disposition'], $match ) )
                            {
                                $messages[$msg_id]['filename'] = trim( $match[1] );
                            }
                            else
                            {
                                if ( preg_match( "/filename=([^\\s]+)/si", $message_headers['content-disposition'], $match ) )
                                {
                                    $messages[$msg_id]['filename'] = trim( $match[1] );
                                }
                            }
                        }
                        else if ( preg_match( "/name=\"(.+)\"/si", $messages[$msg_id]['content-type'], $match ) )
                        {
                            $messages[$msg_id]['filename'] = trim( $match[1] );
                        }
                        else if ( preg_match( "/name=([^\\s]+)/si", $messages[$msg_id]['content-type'], $match ) )
                        {
                            $messages[$msg_id]['filename'] = trim( $match[1] );
                        }
                        else if ( preg_match( "/^text\\/([^\\s]+)/i", $messages[$msg_id]['content-type-value'], $match ) )
                        {
                            $messages[$msg_id]['filename'] = trim( $match[1] ).".txt";
                        }
                        else if ( preg_match( "/^message\\/([^\\s]+)/i", $messages[$msg_id]['content-type-value'], $match ) )
                        {
                            $messages[$msg_id]['filename'] = trim( $match[1] ).".txt";
                        }
                        if ( strlen( $messages[$msg_id]['filename'] ) )
                        {
                            $filename_charset = "";
                            $filename = $messages[$msg_id]['filename'];
                            decode_mail_header( $filename, $filename_charset );
                            $filename = preg_replace( "/[\\:\\/\\\\]/", "_", $filename );
                            $messages[$msg_id]['filename'] = $filename;
                        }
                        $messages[$msg_id]['attachment-allowed'] = false;
                        if ( strlen( $messages[$msg_id]['filename'] ) )
                        {
                            $attachments_regexp = preg_replace( "/\\s/", "", $attachments_mask );
                            $filename_check = $messages[$msg_id]['filename'];
                            if ( !preg_match( "/\\./", $filename_check ) )
                            {
                                $filename_check .= ".";
                            }
                            $s = array( "\\", "^", "\$", ".", "[", "]", "|", "(", ")", "+", "{", "}" );
                            $r = array( "\\\\", "\\^", "\\\$", "\\.", "\\[", "\\]", "\\|", "\\(", "\\)", "\\+", "\\{", "\\}" );
                            $attachments_regexp = str_replace( $s, $r, $attachments_regexp );
                            $attachments_regexp = str_replace( array( ",", ";", "*", "?" ), array( ")|(", ")|(", ".*", "." ), $attachments_regexp );
                            $attachments_regexp = "/^((".$attachments_regexp."))$/i";
                            if ( preg_match( $attachments_regexp, $filename_check ) )
                            {
                                if ( is_dir( $attachments_dir ) )
                                {
                                    $messages[$msg_id]['attachment-allowed'] = true;
                                    $filename = $messages[$msg_id]['filename'];
                                    $new_filename = $filename;
                                    $file_index = 0;
                                    while ( file_exists( $attachments_dir.$new_filename ) )
                                    {
                                        ++$file_index;
                                        $delimiter_pos = strpos( $filename, "." );
                                        if ( $delimiter_pos )
                                        {
                                            $new_filename = substr( $filename, 0, $delimiter_pos )."_".$file_index.substr( $filename, $delimiter_pos );
                                        }
                                        else
                                        {
                                            $new_filename = $index."_".$filename;
                                        }
                                    }
                                    $filepath = $attachments_dir.$new_filename;
                                    $messages[$msg_id]['filepath'] = $filepath;
                                    $fa = fopen( $filepath, "w" );
                                }
                                else
                                {
                                    $recipients = $settings['admin_email'];
                                    $mail_subject = "auto: ViArt Shop Notification";
                                    $email_headers = array( );
                                    $email_headers['from'] = $settings['admin_email'];
                                    $email_headers['Auto-Submitted'] = "auto-generated";
                                    $email_headers['Content-Type'] = "text/plain";
                                    $message = "Directory for HelpDesk attachments cannot be found: ";
                                    $message .= "'".$attachments_dir."'".$eol;
                                    va_mail( $recipients, $mail_subject, $message, $email_headers );
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                if ( preg_match( "/^(\\w[\\w\\-]*):(.*)$/", $line, $matches ) )
                {
                    $name = strtolower( $matches[1] );
                    $value = $matches[2];
                }
                else
                {
                    $value = $line;
                }
                if ( isset( $message_headers[$name] ) )
                {
                    $message_headers[$name] .= "\n".$value;
                }
                else
                {
                    $message_headers[$name] = $value;
                }
            }
        }
        else if ( trim( $line ) == $boundary || trim( $line ) == "--".$boundary || trim( $line ) == "--".$boundary."--" )
        {
            if ( $messages[$msg_id]['attachment'] && $messages[$msg_id]['attachment-allowed'] )
            {
                fclose( $fa );
                chmod( $messages[$msg_id]['filepath'], 493 );
            }
            ++$msg_id;
            $message_headers = array( );
            $header_received = false;
            if ( trim( $line ) == "--".$boundary."--" )
            {
                $is_boundary = false;
                if ( $is_sub_boundary )
                {
                    return;
                }
            }
        }
        else
        {
            if ( $messages[$msg_id]['attachment'] )
            {
                if ( $messages[$msg_id]['attachment-allowed'] )
                {
                    if ( $messages[$msg_id]['encoding'] == "base64" )
                    {
                        $line = base64_decode( $line );
                    }
                    else if ( $messages[$msg_id]['encoding'] == "quoted-printable" )
                    {
                        $line = quoted_printable_decode( $line );
                    }
                    fputs( $fa, $line );
                }
            }
            else
            {
                if ( $messages[$msg_id]['encoding'] == "base64" )
                {
                    $line = base64_decode( $line );
                }
                else if ( $messages[$msg_id]['encoding'] == "quoted-printable" )
                {
                    $line = quoted_printable_decode( $line );
                }
                $messages[$msg_id]['body'] .= $line;
            }
        }
    }
}

function decode_mail_header( &$message, &$charset )
{
    if ( preg_match( "/=\\?([\\w\\d\\-]+)\\?(\\w)\\?(.*)\\?=/i", $message, $matches ) )
    {
        $charset = $matches[1];
        $encode_type = $matches[2];
        $message = $matches[3];
        if ( strtoupper( $encode_type ) == "Q" )
        {
            $message = quoted_printable_decode( $message );
        }
        else if ( strtoupper( $encode_type ) == "B" )
        {
            $message = base64_decode( $message );
        }
    }
    return $message;
}

if ( !isset( $save_email_copy ) )
{
    $save_email_copy = false;
}
if ( !isset( $emails_folder ) )
{
    $emails_folder = "./";
}
$eol = get_eol( );
$test = get_param( "test" );
if ( $test )
{
    $save_email_copy = false;
}
if ( $test )
{
    $fp = fopen( "../includes/test_message.txt", "r" );
}
else
{
    $fp = fopen( "php://stdin", "r" );
    if ( $save_email_copy )
    {
        srand( ( double ) * 1000000 );
        $random_value = rand( );
        $copy_filename = $emails_folder.date( "Y_m_d_H_i_s_", time( ) ).$random_value.".txt";
        $fw = fopen( $copy_filename, "a" );
    }
}
if ( $fp )
{
    $headers = array( );
    $headers['encoding'] = "";
    $headers['attachment'] = false;
    $headers['inline'] = false;
    $headers['body'] = "";
    $headers['content-type-value'] = "";
    $header_received = false;
    $mail_headers = "";
    $mail_body_text = "";
    $mail_body_html = "";
    while ( !feof( $fp ) && !$header_received )
    {
        $line = fgets( $fp, 4096 );
        if ( $save_email_copy )
        {
            fputs( $fw, $line );
        }
        $mail_headers .= $line;
        if ( preg_match( "/^\\s*$/", $line ) )
        {
            $header_received = true;
        }
        else
        {
            if ( preg_match( "/^(\\w[\\w\\-]*):(.*)$/", $line, $matches ) )
            {
                $name = strtolower( $matches[1] );
                $value = $matches[2];
            }
            else
            {
                $value = $line;
            }
            if ( isset( $headers[$name] ) )
            {
                $headers[$name] .= "\n".$value;
            }
            else
            {
                $headers[$name] = $value;
            }
        }
    }
    if ( isset( $headers['x-autoresponder'] ) || isset( $headers['x-autoreply'] ) || isset( $headers['x-autorespond'] ) || isset( $headers['auto-submitted'] ) && strtolower( $headers['auto-submitted'] ) != "no" )
    {
        if ( $save_email_copy )
        {
            fclose( $fw );
            chmod( $copy_filename, 493 );
        }
        fclose( $fp );
        return;
    }
    if ( isset( $headers['content-type'] ) && preg_match( "/^([^;\n\r]*)/s", $headers['content-type'], $match ) )
    {
        $headers['content-type-value'] = trim( strtolower( $match[1] ) );
        if ( preg_match( "/delsp=([^\\s]+)/si", $headers['content-type'], $match ) )
        {
            $headers['content-type-delsp'] = trim( $match[1] );
        }
        if ( preg_match( "/format=([^\\s]+)/si", $headers['content-type'], $match ) )
        {
            $headers['content-type-format'] = trim( $match[1] );
        }
    }
    if ( isset( $headers['content-transfer-encoding'] ) && preg_match( "/^([^;\n\r]*)/s", $headers['content-transfer-encoding'], $match ) )
    {
        $headers['encoding'] = trim( strtolower( $match[1] ) );
    }
    if ( isset( $headers['content-disposition'] ) && preg_match( "/attachment/si", $headers['content-disposition'], $match ) )
    {
        $headers['attachment'] = true;
        $headers['attachment-allowed'] = false;
    }
    else if ( isset( $headers['content-disposition'] ) && preg_match( "/inline/si", $headers['content-disposition'], $match ) )
    {
        $headers['inline'] = true;
    }
    $subject = "";
    $from = "";
    $from_user = "";
    $from_email = "";
    $to = "";
    $to_emails = array( );
    $subject = isset( $headers['subject'] ) ? $headers['subject'] : "";
    $subject = preg_replace( "/^\\s*((Re(\\[\\d+\\])?|FW(\\[\\d+\\])?):\\s*)+/i", "", $subject );
    $subject = preg_replace( "/^Support\\s*(Request|Ticket|Issue)\\s*/i", "", $subject );
    $subject = preg_replace( "/^:+\\s*/i", "", $subject );
    $subject = trim( $subject );
    if ( !strlen( $subject ) )
    {
        $subject = "No Subject";
    }
    decode_mail_header( $subject, $subject_charset );
    $from = isset( $headers['from'] ) ? $headers['from'] : "";
    if ( preg_match( "/(.*?)<(.*?)>/s", $from, $found ) )
    {
        $from_user = trim( $found[1] );
        $from_email = trim( $found[2] );
        if ( !strlen( $from_email ) )
        {
            $from_email = "<>";
        }
    }
    else
    {
        $from_email = trim( $from );
    }
    if ( !strlen( $from_user ) )
    {
        if ( preg_match( "/^([^@]+)@/", $from_email, $match ) )
        {
            $from_user = trim( $match[1] );
        }
        else
        {
            $from_user = $from_email;
        }
    }
    if ( preg_match( "/^\"(.+)\"$/", $from_user, $match ) )
    {
        $from_user = $match[1];
    }
    decode_mail_header( $from_user, $from_user_charset );
    $mail_receivers = array( );
    $cc_emails = array( );
    $department_id = "";
    $incoming_type_id = "";
    $incoming_product_id = "";
    if ( isset( $account_address ) && $account_address )
    {
        $mail_receivers[] = $account_address;
    }
    $to = isset( $headers['to'] ) ? $headers['to'] : "";
    $to_values = explode( ",", $to );
    $i = 0;
    for ( ; $i < sizeof( $to_values ); ++$i )
    {
        $to_value = $to_values[$i];
        if ( preg_match( "/<([^@]+@[^@]+(\\.[^@]+)*\\.[a-z]+)>/i", $to_value, $match ) )
        {
            $mail_receivers[] = $match[1];
        }
        else if ( preg_match( "/\\s*([^@]+@[^@]+(\\.[^@]+)*\\.[a-z]+)\\s*/i", $to_value, $match ) )
        {
            $mail_receivers[] = trim( $match[1] );
        }
    }
    $cc = isset( $headers['cc'] ) ? $headers['cc'] : "";
    $cc_values = explode( ",", $cc );
    $i = 0;
    for ( ; $i < sizeof( $cc_values ); ++$i )
    {
        $cc_value = $cc_values[$i];
        if ( preg_match( "/<([^@]+@[^@]+(\\.[^@]+)*\\.[a-z]+)>/i", $cc_value, $match ) )
        {
            $mail_receivers[] = $match[1];
        }
        else if ( preg_match( "/\\s*([^@]+@[^@]+(\\.[^@]+)*\\.[a-z]+)\\s*/i", $cc_value, $match ) )
        {
            $mail_receivers[] = trim( $match[1] );
        }
    }
    if ( 0 < sizeof( $mail_receivers ) )
    {
        $sql = " SELECT * FROM ".$table_prefix."support_departments ";
        $i = 0;
        for ( ; $i < sizeof( $mail_receivers ); ++$i )
        {
            if ( $i == 0 )
            {
                $sql .= " WHERE ";
            }
            else
            {
                $sql .= " OR ";
            }
            $sql .= " incoming_account LIKE '%".$db->tosql( $mail_receivers[$i], TEXT, false )."%'";
        }
        $db->query( $sql );
        if ( $db->next_record( ) )
        {
            $department_id = $db->Record['dep_id'];
            $attachments_dir = $db->Record['attachments_dir'];
            $attachments_mask = $db->Record['attachments_mask'];
            $incoming_type_id = $db->Record['incoming_type_id'];
            $incoming_account = $db->Record['incoming_account'];
            $incoming_emails = array( );
            $incoming_values = explode( ",", $incoming_account );
            $i = 0;
            for ( ; $i < sizeof( $incoming_values ); ++$i )
            {
                $incoming_value = $incoming_values[$i];
                if ( preg_match( "/<([^@]+@[^@]+(\\.[^@]+)*\\.[a-z]+)>/i", $incoming_value, $match ) )
                {
                    $incoming_emails[] = $match[1];
                }
                else if ( preg_match( "/\\s*([^@]+@[^@]+(\\.[^@]+)*\\.[a-z]+)\\s*/i", $incoming_value, $match ) )
                {
                    $incoming_emails[] = trim( $match[1] );
                }
            }
            foreach ( $mail_receivers as $receiver_email )
            {
                if ( !in_array( $receiver_email, $incoming_emails ) )
                {
                    $cc_emails[] = $receiver_email;
                }
            }
            if ( !strlen( $incoming_type_id ) )
            {
                $incoming_type_id = 0;
            }
            $incoming_product_id = $db->Record['incoming_product_id'];
            if ( !strlen( $incoming_product_id ) )
            {
                $incoming_product_id = 0;
            }
            if ( !$attachments_dir )
            {
                $sql = "SELECT setting_value FROM ".$table_prefix."global_settings ";
                $sql .= "WHERE setting_type='support' AND setting_name='attachments_dir'";
                $attachments_dir = get_db_value( $sql );
            }
        }
    }
    if ( strlen( $department_id ) )
    {
        $boundary = "";
        $content_type = isset( $headers['content-type'] ) ? $headers['content-type'] : "";
        if ( preg_match( "/boundary=\"(.+)\"/i", $content_type, $match ) )
        {
            $boundary = $match[1];
        }
        else if ( preg_match( "/boundary=([^\\s]+)/i", $content_type, $match ) )
        {
            $boundary = $match[1];
        }
        $message_headers = array( );
        $messages = array( );
        $msg_id = 0;
        $header_received = false;
        if ( strlen( $boundary ) )
        {
            $is_boundary = false;
            parse_multipart( $boundary, false );
        }
        else
        {
            $messages[0] = $headers;
            $messages[0]['body'] = "";
            while ( !feof( $fp ) )
            {
                $line = fgets( $fp, 4096 );
                if ( $save_email_copy )
                {
                    fputs( $fw, $line );
                }
                $messages[0]['body'] .= $line;
            }
            if ( $messages[0]['encoding'] == "base64" )
            {
                $messages[0]['body'] = base64_decode( $messages[0]['body'] );
            }
            else if ( $messages[$msg_id]['encoding'] == "quoted-printable" )
            {
                $messages[0]['body'] = quoted_printable_decode( $messages[0]['body'] );
            }
            if ( $messages[0]['content-type-value'] == "" )
            {
                if ( preg_match( "/<html>/i", $messages[0]['body'] ) )
                {
                    $messages[0]['content-type-value'] = "text/html";
                }
                else
                {
                    $messages[0]['content-type-value'] = "text/plain";
                }
            }
        }
        if ( $save_email_copy )
        {
            fclose( $fw );
            chmod( $copy_filename, 493 );
        }
        fclose( $fp );
        $body = "";
        $mail_body_text = "";
        $mail_body_html = "";
        foreach ( $messages as $msg_id => $message )
        {
            if ( !$message['attachment'] && !$message['inline'] && $message['content-type-value'] == "text/plain" )
            {
                if ( $mail_body_text )
                {
                    $mail_body_text .= "--\n";
                }
                $mail_body_text .= $message['body'];
            }
        }
        foreach ( $messages as $msg_id => $message )
        {
            if ( !$message['attachment'] && !$message['inline'] && $message['content-type-value'] == "text/html" )
            {
                if ( $mail_body_html )
                {
                    $mail_body_html .= "<br><hr><br>";
                }
                $mail_body_html .= $message['body'];
            }
        }
        foreach ( $messages as $msg_id => $message )
        {
            if ( $message['inline'] )
            {
                if ( $message['content-type-value'] == "text/html" )
                {
                    if ( $mail_body_html )
                    {
                        $mail_body_html .= "<br><hr><br>";
                    }
                    $mail_body_html .= $message['body'];
                    if ( $mail_body_text )
                    {
                        $mail_body_text .= "\n--\n";
                    }
                    if ( $mail_body_text )
                    {
                        $inline_body = $message['body'];
                        $inline_body = preg_replace( "/<br>/i", $eol, $inline_body );
                        $inline_body = preg_replace( "/&nbsp;/i", " ", $inline_body );
                        $inline_body = preg_replace( "/&gt;/i", ">", $inline_body );
                        $inline_body = preg_replace( "/&lt;/i", "<", $inline_body );
                        $inline_body = strip_tags( $inline_body );
                        $mail_body_text .= $inline_body;
                    }
                }
                else
                {
                    if ( $mail_body_html )
                    {
                        $mail_body_html .= "<br><hr><br>";
                    }
                    if ( $mail_body_html )
                    {
                        $mail_body_html .= nl2br( htmlspecialchars( $message['body'] ) );
                    }
                    if ( $mail_body_text )
                    {
                        $mail_body_text .= "\n--\n";
                    }
                    $mail_body_text .= $message['body'];
                }
            }
        }
        if ( $mail_body_text )
        {
            $body = $mail_body_text;
        }
        else
        {
            $body = $mail_body_html;
            $body = preg_replace( "/[\n\r]/", "", $body );
            $body = preg_replace( "/<br>/i", $eol, $body );
            $body = preg_replace( "/&nbsp;/i", " ", $body );
            $body = preg_replace( "/&gt;/i", ">", $body );
            $body = preg_replace( "/&lt;/i", "<", $body );
            $body = strip_tags( $body );
        }
        if ( preg_match( "/Ticket\\s+ID\\s+is\\s+(\\d+)/si", $body, $match ) )
        {
            $ticket_id = $match[1];
        }
        else if ( preg_match( "/support_id=(\\d+)/s", $body, $match ) )
        {
            $ticket_id = $match[1];
        }
        else
        {
            $ticket_id = "";
        }
        $body = preg_replace( "/\\-+Ticket\\-Body\\-End.*/is", "", $body );
        $body = trim( $body );
        if ( strlen( $ticket_id ) )
        {
            $sql = "SELECT support_id FROM ".$table_prefix."support WHERE support_id=".$db->tosql( $ticket_id, INTEGER );
            $db->query( $sql );
            if ( !$db->next_record( ) )
            {
                $ticket_id = "";
            }
        }
        $message_id = 0;
        if ( strlen( $ticket_id ) )
        {
            $sql = " SELECT status_id FROM ".$table_prefix."support_statuses WHERE is_user_reply=1 ";
            $db->query( $sql );
            if ( $db->next_record( ) )
            {
                $reply_status_id = $db->f( "status_id" );
            }
            else
            {
                $sql = " SELECT status_id FROM ".$table_prefix."support_statuses WHERE is_user_new=1 ";
                $db->query( $sql );
                if ( $db->next_record( ) )
                {
                    $reply_status_id = $db->f( "status_id" );
                }
                else
                {
                    $reply_status_id = 0;
                }
            }
            $sql = "INSERT INTO ".$table_prefix."support_messages ";
            $sql .= " (support_id,is_user_reply,support_status_id,date_added,reply_from,reply_to,subject,";
            $sql .= " message_text,mail_headers,mail_body_text,mail_body_html) VALUES (";
            $sql .= "{$ticket_id}, 1, ";
            $sql .= $db->tosql( $reply_status_id, INTEGER ).", ";
            $sql .= $db->tosql( va_time( ), DATETIME ).", ";
            $sql .= $db->tosql( $from_email, TEXT ).", ";
            $sql .= $db->tosql( $to, TEXT ).", ";
            $sql .= $db->tosql( $subject, TEXT ).", ";
            $sql .= $db->tosql( $body, TEXT ).", ";
            $sql .= $db->tosql( $mail_headers, TEXT ).", ";
            $sql .= $db->tosql( $mail_body_text, TEXT ).", ";
            $sql .= $db->tosql( $mail_body_html, TEXT ).") ";
            $db->query( $sql );
            if ( $db_type == "mysql" )
            {
                $message_id = get_db_value( " SELECT LAST_INSERT_ID() " );
            }
            else if ( $db_type == "access" )
            {
                $message_id = get_db_value( " SELECT @@IDENTITY " );
            }
            else if ( $db_type == "db2" )
            {
                $message_id = get_db_value( " SELECT PREVVAL FOR seq_".$table_prefix."support_messages FROM ".$table_prefix."support_messages " );
            }
            else
            {
                $sql = " SELECT MAX(message_id) AS lid FROM ".$table_prefix."support_messages ";
                $sql .= " WHERE support_id=".$db->tosql( $ticket_id, INTEGER );
                $message_id = get_db_value( $sql );
            }
            $sql = " UPDATE ".$table_prefix."support SET ";
            $sql .= " admin_id_assign_to=0, admin_id_assign_by=0, ";
            $sql .= " support_status_id=".$db->tosql( $reply_status_id, INTEGER ).", ";
            $sql .= " date_modified=".$db->tosql( va_time( ), DATETIME );
            $sql .= " WHERE support_id=".$ticket_id;
            $db->query( $sql );
            $new_thread = false;
        }
        else
        {
            $sql = " SELECT status_id FROM ".$table_prefix."support_statuses WHERE is_user_new=1 ";
            $db->query( $sql );
            if ( $db->next_record( ) )
            {
                $new_status_id = $db->f( "status_id" );
            }
            else
            {
                $sql = " SELECT status_id FROM ".$table_prefix."support_statuses WHERE is_user_reply=1 ";
                $db->query( $sql );
                if ( $db->next_record( ) )
                {
                    $new_status_id = $db->f( "status_id" );
                }
                else
                {
                    $new_status_id = 0;
                }
            }
            $priority_id = 0;
            $sql = " SELECT sp.priority_id, sup.priority_expiry ";
            $sql .= " FROM ".$table_prefix."support_priorities sp, ".$table_prefix."support_users_priorities sup ";
            $sql .= " WHERE sp.priority_id=sup.priority_id ";
            $sql .= " AND user_email=".$db->tosql( $from_email, TEXT );
            $db->query( $sql );
            if ( $db->next_record( ) )
            {
                $priority_id = $db->f( "priority_id" );
                $current_ts = va_timestamp( );
                $priority_expiry = $db->f( "priority_expiry", DATETIME );
                if ( is_array( $priority_expiry ) )
                {
                    $priority_expiry_ts = va_timestamp( $priority_expiry );
                    if ( $priority_expiry_ts < $current_ts )
                    {
                        $priority_id = 0;
                    }
                }
            }
            if ( !$priority_id )
            {
                $sql = " SELECT priority_id FROM ".$table_prefix."support_priorities WHERE is_default=1 ";
                $db->query( $sql );
                if ( $db->next_record( ) )
                {
                    $priority_id = $db->f( "priority_id" );
                }
            }
            $sql = " SELECT user_id FROM ".$table_prefix."users ";
            $sql .= " WHERE email=".$db->tosql( $from_email, TEXT );
            $db->query( $sql );
            if ( $db->next_record( ) )
            {
                $user_id = $db->f( "user_id" );
            }
            else
            {
                $user_id = 0;
            }
            $ip = get_ip( );
            $sql = "INSERT INTO ".$table_prefix."support (";
            if ( $db_type == "postgre" )
            {
                $ticket_id = get_db_value( " SELECT NEXTVAL('seq_".$table_prefix."support') " );
                $sql .= " support_id, ";
            }
            $sql .= " site_id, date_modified,support_product_id,support_type_id,support_status_id,support_priority_id,date_added,dep_id,";
            $sql .= " user_id, user_name,user_email,remote_address,summary,description,mail_cc,mail_headers,mail_body_text,mail_body_html) VALUES (";
            if ( $db_type == "postgre" )
            {
                $sql .= $db->tosql( $ticket_id, INTEGER ).", ";
            }
            if ( isset( $site_id ) )
            {
                $sql .= $db->tosql( $site_id, INTEGER, true, false ).", ";
            }
            else
            {
                $sql .= $db->tosql( 1, INTEGER ).", ";
            }
            $sql .= $db->tosql( va_time( ), DATETIME ).",";
            $sql .= $db->tosql( $incoming_product_id, INTEGER ).", ";
            $sql .= $db->tosql( $incoming_type_id, INTEGER ).", ";
            $sql .= $db->tosql( $new_status_id, INTEGER ).", ";
            $sql .= $db->tosql( $priority_id, INTEGER ).", ";
            $sql .= $db->tosql( va_time( ), DATETIME ).", ";
            $sql .= $department_id.", ";
            $sql .= $db->tosql( $user_id, INTEGER ).", ";
            $sql .= $db->tosql( $from_user, TEXT ).", ";
            $sql .= $db->tosql( $from_email, TEXT ).", ";
            $sql .= $db->tosql( $ip, TEXT ).", ";
            $sql .= $db->tosql( $subject, TEXT ).", ";
            $sql .= $db->tosql( $body, TEXT ).", ";
            if ( 0 < sizeof( $cc_emails ) )
            {
                $sql .= $db->tosql( implode( ", ", $cc_emails ), TEXT ).", ";
            }
            else
            {
                $sql .= "'', ";
            }
            $sql .= $db->tosql( $mail_headers, TEXT ).", ";
            $sql .= $db->tosql( $mail_body_text, TEXT ).", ";
            $sql .= $db->tosql( $mail_body_html, TEXT ).") ";
            $db->query( $sql );
            if ( $db_type == "mysql" )
            {
                $ticket_id = get_db_value( " SELECT LAST_INSERT_ID() " );
            }
            else if ( $db_type == "access" )
            {
                $ticket_id = get_db_value( " SELECT @@IDENTITY " );
            }
            else if ( $db_type == "db2" )
            {
                $ticket_id = get_db_value( " SELECT PREVVAL FOR seq_".$table_prefix."support FROM ".$table_prefix."support " );
            }
            $new_thread = true;
        }
        foreach ( $messages as $msg_id => $message )
        {
            if ( $message['attachment'] && $message['attachment-allowed'] )
            {
                $sql = " INSERT INTO ".$table_prefix."support_attachments (support_id, message_id, admin_id, attachment_status, date_added, file_name, file_path) ";
                $sql .= " VALUES (".$ticket_id.",";
                $sql .= $db->tosql( $message_id, INTEGER ).", ";
                $sql .= "0, 1, ";
                $sql .= $db->tosql( va_time( ), DATETIME ).", ";
                $sql .= $db->tosql( $message['filename'], TEXT ).", ";
                $sql .= $db->tosql( $message['filepath'], TEXT ).") ";
                $db->query( $sql );
            }
        }
    }
    else
    {
        if ( $save_email_copy )
        {
            while ( !feof( $fp ) )
            {
                $line = fgets( $fp, 4096 );
                fputs( $fw, $line );
            }
            fclose( $fw );
            chmod( $copy_filename, 493 );
        }
        fclose( $fp );
        $recipients = $settings['admin_email'];
        $mail_subject = "auto: ViArt Shop Notification";
        $email_headers = array( );
        $email_headers['from'] = $settings['admin_email'];
        $email_headers['Auto-Submitted'] = "auto-generated";
        $email_headers['Content-Type'] = "text/plain";
        $message = "Can't find the appropriate Helpdesk Department to pipe email for account: ";
        $message .= "'".join( ", ", $mail_receivers )."'".$eol;
        va_mail( $recipients, $mail_subject, $message, $email_headers );
    }
}
else
{
    $recipients = $settings['admin_email'];
    $mail_subject = "auto: ViArt Shop Notification";
    $email_headers = array( );
    $email_headers['from'] = $settings['admin_email'];
    $email_headers['Auto-Submitted'] = "auto-generated";
    $email_headers['Content-Type'] = "text/plain";
    $message = "Can't read the email message";
    va_mail( $recipients, $mail_subject, $message, $email_headers );
}
?>

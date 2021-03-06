<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  messages.php                                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	define("CHARSET", "iso-8859-1");
	// date messages
	define("YEAR_MSG", "Vuosi");
	define("YEARS_QTY_MSG", "{quantity} years");
	define("MONTH_MSG", "Kuukausi");
	define("MONTHS_QTY_MSG", "{quantity} months");
	define("DAY_MSG", "P�iv�");
	define("DAYS_MSG", "P�iv��");
	define("DAYS_QTY_MSG", "{quantity} days");
	define("HOUR_MSG", "Tunti");
	define("HOURS_QTY_MSG", "{quantity} hours");
	define("MINUTE_MSG", "Minuutti");
	define("MINUTES_QTY_MSG", "{quantity} minutes");
	define("SECOND_MSG", "Sekuntti");
	define("SECONDS_QTY_MSG", "{quantity} seconds");
	define("WEEK_MSG", "Viikko");
	define("WEEKS_QTY_MSG", "{quantity} weeks");
	define("TODAY_MSG", "T�n��n");
	define("YESTERDAY_MSG", "Eilen");
	define("LAST_7DAYS_MSG", "Viim. 7 p�iv��");
	define("THIS_MONTH_MSG", "T�m� kuukausi");
	define("LAST_MONTH_MSG", "Viime kuukausi");
	define("THIS_QUARTER_MSG", "T�m� nelj�nnes");
	define("THIS_YEAR_MSG", "T�m� vuosi");

	// months
	define("JANUARY", "Tammikuu");
	define("FEBRUARY", "Helmikuu");
	define("MARCH", "Maaliskuu");
	define("APRIL", "Huhtikuu");
	define("MAY", "Toukokuu");
	define("JUNE", "Kes�kuu");
	define("JULY", "Hein�kuu");
	define("AUGUST", "Elokuu");
	define("SEPTEMBER", "Syyskuu");
	define("OCTOBER", "Lokakuu");
	define("NOVEMBER", "Marraskuu");
	define("DECEMBER", "Joulukuu");

	define("JANUARY_SHORT", "Tam");
	define("FEBRUARY_SHORT", "Hel");
	define("MARCH_SHORT", "Maa");
	define("APRIL_SHORT", "Huh");
	define("MAY_SHORT", "Tou");
	define("JUNE_SHORT", "Kes");
	define("JULY_SHORT", "Hei");
	define("AUGUST_SHORT", "Elo");
	define("SEPTEMBER_SHORT", "Syy");
	define("OCTOBER_SHORT", "Lok");
	define("NOVEMBER_SHORT", "Mar");
	define("DECEMBER_SHORT", "Jou");

	// weekdays
	define("SUNDAY", "Sunnuntai");
	define("MONDAY", "Maanantai");
	define("TUESDAY", "Tiistai");
	define("WEDNESDAY", "Keskiviikko");
	define("THURSDAY", "Torstai");
	define("FRIDAY", "Perjantai");
	define("SATURDAY", "Lauantai");

	define("SUNDAY_SHORT", "Su");
	define("MONDAY_SHORT", "Ma");
	define("TUESDAY_SHORT", "Ti");
	define("WEDNESDAY_SHORT", "Ke");
	define("THURSDAY_SHORT", "To");
	define("FRIDAY_SHORT", "Pe");
	define("SATURDAY_SHORT", "La");

	// validation messages
	define("REQUIRED_MESSAGE", "<b>{field_name}</b> vaaditaan");
	define("UNIQUE_MESSAGE", "Kent�n <b>{field_name}</b> tieto on jo tietokannassa");
	define("VALIDATION_MESSAGE", "Kent�n varmennus ei onnistunut <b>{field_name}</b>");
	define("MATCHED_MESSAGE", "<b>{field_one}</b> ja <b>{field_two}</b> eiv�t t�sm��");
	define("INSERT_ALLOWED_ERROR", "Valitettavasti et voi lis�t� tietoja");
	define("UPDATE_ALLOWED_ERROR", "Valitettavasti et voi p�ivitt�� tietoja");
	define("DELETE_ALLOWED_ERROR", "Valitettavasti et voi poistaa tietoja");
	define("ALPHANUMERIC_ALLOWED_ERROR", "Only alpha-numeric characters, hyphen and underscore are allowed for field <b>{field_name}</b>");

	define("INCORRECT_DATE_MESSAGE", "<b>{field_name}</b> has incorrect date value. Use calendar dates");
	define("INCORRECT_MASK_MESSAGE", "<b>{field_name}</b> ei vastaa parametrej�. K�yt� seuraava '<b>{field_mask}</b>'");
	define("INCORRECT_EMAIL_MESSAGE", "S�hk�postios. Muot virheellinen kent�ss� {field_name}");
	define("INCORRECT_VALUE_MESSAGE", "Virheellinen arvo kent�ss� <b>{field_name}</b>");

	define("MIN_VALUE_MESSAGE", "Arvo kent�ss� <b>{field_name}</b> ei voi olla alle {min_value}");
	define("MAX_VALUE_MESSAGE", "Arvo kent�ss� <b>{field_name}</b> ei voi olla suurempi kuin {max_value}");
	define("MIN_LENGTH_MESSAGE", "The length in field <b>{field_name}</b> can't be less than {min_length} symbols");
	define("MAX_LENGTH_MESSAGE", "Tekstin pituus kent�ss� <b>{field_name}</b> voi olla maksimissaan{max_length} kirjainta");

	define("FILE_PERMISSION_MESSAGE", "Tiedostoon ei voi kirjoittaa<b>'{file_name}'</b>. Vaihda kirjoitusoikeudet ennen jatkamista");
	define("FOLDER_PERMISSION_MESSAGE", "Don't have writable permission to folder <b>'{folder_name}'</b>. Please change folder permission before continue");
	define("INVALID_EMAIL_MSG", "Email osoite ei kelpaa");
	define("DATABASE_ERROR_MSG", "Tietokantavirhe");
	define("BLACK_IP_MSG", "Palvelimesi ei salli t�t� toimintoa");
	define("BANNED_CONTENT_MSG", "Sy�tt�m�si tiedot sis�lt�v�t kiellettyj� merkkej�");
	define("ERRORS_MSG", "Virheit�");
	define("REGISTERED_ACCESS_MSG", "Vain rekister�ityneet k�ytt�j�t ");
	define("SELECT_FROM_LIST_MSG", "Valitse listasta");

	// titles 
	define("TOP_RATED_TITLE", "Top rankattu");
	define("TOP_VIEWED_TITLE", "Eniten katsottu");
	define("RECENTLY_VIEWED_TITLE", "Viimeksi katsottu");
	define("HOT_TITLE", "Hot!");
	define("LATEST_TITLE", "Viimeisin");
	define("CONTENT_TITLE", "Sis�lt�");
	define("RELATED_TITLE", "Liittyy");
	define("SEARCH_TITLE", "Etsi");
	define("ADVANCED_SEARCH_TITLE", "Tarkempi haku");
	define("LOGIN_TITLE", "Sis��nkirjautuminen");
	define("CATEGORIES_TITLE", "Luokat");
	define("MANUFACTURERS_TITLE", "Valmistajat");
	define("SPECIAL_OFFER_TITLE", "Erikoistarjous");
	define("NEWS_TITLE", "Uutiset");
	define("EVENTS_TITLE", "Tapahtumat");
	define("PROFILE_TITLE", "Profiili");
	define("USER_HOME_TITLE", "Kotisivu");
	define("DOWNLOAD_TITLE", "Lataa");
	define("FAQ_TITLE", "Usein kysytyt kysymykset");
	define("POLL_TITLE", "��nestys");
	define("HOME_PAGE_TITLE", "Kotisivu");
	define("CURRENCY_TITLE", "Valuutta");
	define("SUBSCRIBE_TITLE", "Tilaa");
	define("UNSUBSCRIBE_TITLE", "Poista tilaus");
	define("UPLOAD_TITLE", "Lataa palvelimelle");
	define("ADS_TITLE", "Mainokset");
	define("ADS_COMPARE_TITLE", "Mainosvertailu");
	define("ADS_SELLERS_TITLE", "Myyj�t");
	define("AD_REQUEST_TITLE", "Tee tarjous/Tee kysymys myyj�lle");
	define("LANGUAGE_TITLE", "Kielet");
	define("MERCHANTS_TITLE", "Kauppiaat");
	define("PREVIEW_TITLE", "Esikatselu");
	define("ARTICLES_TITLE", "Articles");
	define("SITE_MAP_TITLE", "Site Map");
	define("LAYOUTS_TITLE", "Layouts");

	// menu items
	define("MENU_ABOUT", "meist�");
	define("MENU_ACCOUNT", "tilisi");
	define("MENU_BASKET", "ostoskorisi");
	define("MENU_CONTACT", "ota yhteytt�");
	define("MENU_DOCUMENTATION", "dokumentit");
	define("MENU_DOWNLOADS", "lataukset");
	define("MENU_EVENTS", "tapahtumat");
	define("MENU_FAQ", "U.K.K");
	define("MENU_FORUM", "foorumi");
	define("MENU_HELP", "apua");
	define("MENU_HOME", "koti");
	define("MENU_HOW", "miten teen ostoksia");
	define("MENU_MEMBERS", "j�senet");
	define("MENU_MYPROFILE", "oma profiili");
	define("MENU_NEWS", "uutiset");
	define("MENU_PRIVACY", "yksityisyys");
	define("MENU_PRODUCTS", "tuotteet");
	define("MENU_REGISTRATION", "rekister�inti");
	define("MENU_SHIPPING", "l�hetys");
	define("MENU_SIGNIN", "kirjaudu");
	define("MENU_SIGNOUT", "kirjaudu ulos");
	define("MENU_SUPPORT", "tuki");
	define("MENU_USERHOME", "k�ytt�j� koti");
	define("MENU_ADS", "luokitellut mainokset");
	define("MENU_ADMIN", "hallinta");
	define("MENU_KNOWLEDGE", "Dokumentointi");

	// main terms
	define("NO_MSG", "Ei");
	define("YES_MSG", "Kyll�");
	define("NOT_AVAILABLE_MSG", "Ei saatavilla");
	define("MORE_MSG", "lis��..");
	define("READ_MORE_MSG", "lue lis��..");
	define("CLICK_HERE_MSG", "klikkaa t�st�");
	define("ENTER_YOUR_MSG", "Anna");
	define("CHOOSE_A_MSG", "Valitse");
	define("PLEASE_CHOOSE_MSG", "Ole hyv� ja valitse");
	define("SELECT_MSG", "Valitse");
	define("DATE_FORMAT_MSG", "use following format <b>{date_format}</b>");
	define("NEXT_PAGE_MSG", "Seuraava");
	define("PREV_PAGE_MSG", "Edellinen");
	define("FIRST_PAGE_MSG", "Ensimm�inen");
	define("LAST_PAGE_MSG", "Viimeinen");
	define("OF_PAGE_MSG", "/");
	define("TOP_CATEGORY_MSG", "Alkuun");
	define("SEARCH_IN_CURRENT_MSG", "Nykyinen kategoria");
	define("SEARCH_IN_ALL_MSG", "Kaikki kategoriat");
	define("FOUND_IN_MSG", "L�ytyi");
	define("TOTAL_VIEWS_MSG", "Yhteens� katsottu");
	define("VOTES_MSG", "��nt�");
	define("TOTAL_VOTES_MSG", "Yhteens� ��ni�");
	define("TOTAL_POINTS_MSG", "Yhteens� pisteit�");
	define("VIEW_RESULTS_MSG", "Katso tulokset");
	define("PREVIOUS_POLLS_MSG", "Edelliset ��nestykset");
	define("TOTAL_MSG", "Yhteens�");
	define("CLOSED_MSG", "Suljettu");
	define("CLOSE_WINDOW_MSG", "Sulje ikkuna");
	define("ASTERISK_MSG", "t�hti (*) - vaaditut kent�t");
	define("PROVIDE_INFO_MSG", "Sy�t� punaisilla merkittyihin kenttiin tiedot ja paina '{button_name}'");
	define("FOUND_ARTICLES_MSG", "L�ysimme <b>{found_records}</b> artikkelia haulla '<b>{search_string}</b>'");
	define("NO_ARTICLE_MSG", "Artikkelia t�ll� id:ll� ei ole");
	define("NO_ARTICLES_MSG", "Artikkelieita ei l�ytynyt");
	define("NOTES_MSG", "Huom");
	define("KEYWORDS_MSG", "Avainsanat");
	define("LINK_URL_MSG", "Linkki");
	define("DOWNLOAD_URL_MSG", "Lataa");
	define("SUBSCRIBE_FORM_MSG", "Tilataksesi uutiskirjeemme, kirjoita e-mail osoitteesi ja paina '{button_name}' nappulaa.");
	define("UNSUBSCRIBE_FORM_MSG", "Please type your email address in the box below and press '{button_name}' button.");
	define("SUBSCRIBE_LINK_MSG", "Tilaa");
	define("UNSUBSCRIBE_LINK_MSG", "Peruuta tilaus");
	define("SUBSCRIBED_MSG", "Onnittelut, olet nyt tilannut uutiskirjeemme");
	define("ALREADY_SUBSCRIBED_MSG", "Olet jo tilauslistalla. Kiitos!");
	define("UNSUBSCRIBED_MSG", "Olet peruuttanut tilauksesi. Kiitos!");
	define("UNSUBSCRIBED_ERROR_MSG", "Emme l�yt�neet tietojasi tietokannasta, todenn�k�isesti olet jo peruuttanut tilauksesi");
	define("FORGOT_PASSWORD_MSG", "Salasana unohtunut?");
	define("FORGOT_PASSWORD_DESC", "Ole hyv� ja kirjoita s�hk�postiosoite jota k�ytit rekister�ityess�si:");
	define("FORGOT_EMAIL_ERROR_MSG", "Emme l�yd� s�hk�postiosoitettasi tietokannasta");
	define("FORGOT_EMAIL_SENT_MSG", "Kirjautumistiedot on l�hetetty s�hk�postiisi");
	define("RESET_PASSWORD_REQUIRE_MSG", "Joitain vaadittuja tietoja puuttui");
	define("RESET_PASSWORD_PARAMS_MSG", "Antamasi tiedot eiv�t t�sm�� omien tietojemme kanssa");
	define("RESET_PASSWORD_EXPIRY_MSG", "Nollauskoodi on vanhentunut. Ole hyv� ja anna uusi nollauskoodi");
	define("RESET_PASSWORD_SAVED_MSG", "Uusi salasanasi on tallennettu");
	define("PRINTER_FRIENDLY_MSG", "Kirjoitinyst�v�llinen sivu");
	define("PRINT_PAGE_MSG", "Tulosta");
	define("ATTACHMENTS_MSG", "Liitteet");
	define("VIEW_DETAILS_MSG", "Katso yksityiskohdat");
	define("HTML_MSG", "HTML");
	define("PLAIN_TEXT_MSG", "Pelkk� teksti");
	define("META_DATA_MSG", "Meta Data");
	define("META_TITLE_MSG", "Sivun otsikko");
	define("META_KEYWORDS_MSG", "Meta Keywords");
	define("META_DESCRIPTION_MSG", "Meta Kuvaus");
	define("FRIENDLY_URL_MSG", "Lyhyt URL");
	define("IMAGES_MSG", "Kuvat");
	define("IMAGE_MSG", "Kuvat");
	define("IMAGE_TINY_MSG", "Tiny Image");
	define("IMAGE_TINY_ALT_MSG", "Tiny Image Alt");
	define("IMAGE_SMALL_MSG", "Pieni kuva");
	define("IMAGE_SMALL_DESC", "n�yt� listauksessa");
	define("IMAGE_SMALL_ALT_MSG", "Pieni kuva vaihtoehto");
	define("IMAGE_LARGE_MSG", "Iso Kuva");
	define("IMAGE_LARGE_DESC", "N�yt� listauksessa");
	define("IMAGE_LARGE_ALT_MSG", "Iso kuva vaihtoehto");
	define("IMAGE_SUPER_MSG", "Tosi-iso kuva");
	define("IMAGE_SUPER_DESC", "Kuva omassa ikkunassa");
	define("IMAGE_POSITION_MSG", "Image Position");
	define("UPLOAD_IMAGE_MSG", "Lataa kuva");
	define("UPLOAD_FILE_MSG", "Lataa tiedosto");
	define("SELECT_IMAGE_MSG", "Valitse kuva");
	define("SELECT_FILE_MSG", "Valitse tiedosto");
	define("SHOW_BELOW_PRODUCT_IMAGE_MSG", "show image below large product image");
	define("SHOW_IN_SEPARATE_SECTION_MSG", "show image in separate images section");
	define("IS_APPROVED_MSG", "Hyv�ksytty");
	define("NOT_APPROVED_MSG", "Not Approved");
	define("IS_ACTIVE_MSG", "Is Active");
	define("CATEGORY_MSG", "Luokka");
	define("SELECT_CATEGORY_MSG", "Valitse luokka");
	define("DESCRIPTION_MSG", "Kuvaus");
	define("SHORT_DESCRIPTION_MSG", "Lyhyt kuvaus");
	define("FULL_DESCRIPTION_MSG", "T�ysi kuvaus");
	define("HIGHLIGHTS_MSG", "Korostukset");
	define("SPECIAL_OFFER_MSG", "Erikoistarjous");
	define("ARTICLE_MSG", "Article");
	define("OTHER_MSG", "Muuta");
	define("WIDTH_MSG", "Leveys");
	define("HEIGHT_MSG", "Korkeus");
	define("LENGTH_MSG", "Pituus");
	define("WEIGHT_MSG", "Paino");
	define("QUANTITY_MSG", "Kpl");
	define("CALENDAR_MSG", "Kalenteri");
	define("FROM_DATE_MSG", "P�iv�m��r�st�");
	define("TO_DATE_MSG", "P�iv�m��r��n");
	define("TIME_PERIOD_MSG", "Aikajakso");
	define("GROUP_BY_MSG", "Ryhmittely");
	define("BIRTHDAY_MSG", "Birthday");
	define("BIRTH_DATE_MSG", "Birth Date");
	define("BIRTH_YEAR_MSG", "Birth Year");
	define("BIRTH_MONTH_MSG", "Birth Month");
	define("BIRTH_DAY_MSG", "Birth Day");
	define("STEP_NUMBER_MSG", "Step {current_step} of {total_steps}");
	define("WHERE_STATUS_IS_MSG", "Miss� tila on");
	define("ID_MSG", "ID");
	define("QTY_MSG", "Qty");
	define("TYPE_MSG", "Tyyppi");
	define("NAME_MSG", "Name");
	define("TITLE_MSG", "Otsikko");
	define("DEFAULT_MSG", "Default");
	define("OPTIONS_MSG", "Options");
	define("EDIT_MSG", "Edit");
	define("CONFIRM_DELETE_MSG", "Would you like to delete this {record_name}?");
	define("DESC_MSG", "Desc");
	define("ASC_MSG", "Asc");
	define("ACTIVE_MSG", "Aktiivinen");
	define("INACTIVE_MSG", "Inactive");
	define("EXPIRED_MSG", "Vanhentunut");
	define("EMOTICONS_MSG", "Emoticons");
	define("EMOTION_ICONS_MSG", "Emotion Icons");
	define("VIEW_MORE_EMOTICONS_MSG", "View more Emoticons");
	define("SITE_NAME_MSG", "Site Name");
	define("SITE_URL_MSG", "Sivun osoite");
	define("SORT_ORDER_MSG", "Sort Order");
	define("NEW_MSG", "New");
	define("USED_MSG", "Used");
	define("REFURBISHED_MSG", "Refurbished");
	define("ADD_NEW_MSG", "Add New");
	define("SETTINGS_MSG", "Settings");
	define("VIEW_MSG", "Katso");
	define("STATUS_MSG", "Tila");
	define("NONE_MSG", "None");
	define("PRICE_MSG", "Hinta");
	define("TEXT_MSG", "Text");
	define("WARNING_MSG", "Warning");
	define("HIDDEN_MSG", "Piilotettu");
	define("CODE_MSG", "Koodi");
	define("LANGUAGE_MSG", "Kieli");
	define("DEFAULT_VIEW_TYPE_MSG", "Default View Type");
	define("CLICK_TO_OPEN_SECTION_MSG", "Click to open section");
	define("CURRENCY_WRONG_VALUE_MSG", "Currency code has wrong value.");
	define("TRANSACTION_AMOUNT_DOESNT_MATCH_MSG", "<b>Transaction Amount</b> and <b>Order Amount</b> doesn't match.");
	define("STATUS_CANT_BE_UPDATED_MSG", "The status for order #{order_id} can't be updated. ");
	define("CANT_FIND_STATUS_MSG", "Can't find the status with ID:{status_id}");
	define("NOTIFICATION_SENT_MSG", "Notification sent");
	define("AUTO_SUBMITTED_PAYMENT_MSG", "Auto-submitted payment");
	define("FONT_METRIC_FILE_ERROR", "Could not include font metric file");
	define("PER_LINE_MSG", "per line");
	define("PER_LETTER_MSG", "per letter");
	define("PER_NON_SPACE_LETTER_MSG", "per non-space letter");
	define("LETTERS_ALLOWED_MSG", "letters allowed");
	define("LETTERS_ALLOWED_PER_LINEMSG", "letters allowed per line");
	define("RENAME_MSG", "Rename");
	define("IMAGE_FORMAT_ERROR_MSG", "Image format is not supported by the GD library");
	define("GD_LIBRARY_ERROR_MSG", "GD library not loaded");
	define("INVALID_CODE_MSG", "Invalid code: ");
	define("INVALID_CODE_TYPE_MSG", "Invalid code type");
	define("INVALID_FILE_EXTENSION_MSG", "Invalid file extension:");
	define("FOLDER_WRITE_PERMISSION_MSG", "The folder doesn't exists or you do not have the permissions");
	define("UNDEFINED_RECORD_PARAMETER_MSG", "Undefined record parameter: <b>{parameter_name}</b>");
	define("MAX_RECORDS_LIMITATION_MSG", "You are not allowed to add more than <b>{max_records}</b> {records_name} for your version");
	define("ACCESS_DENIED_MSG", "You are not allowed to access this section.");
	define("DELETE_RECORDS_BEFORE_PROCEED_MSG", "Please delete some {records_name} before proceed.");
	define("PRODUCT_MIN_LIMIT_MSG", "You can't add less than {limit_quantity} items for {product_name}.");
	define("FOLDER_DOESNT_EXIST_MSG", "The folder doesn't exist:");
	define("FILE_DOESNT_EXIST_MSG", "The file doesn't exist:");
	define("PARSE_ERROR_IN_BLOCK_MSG", "Parse error in block:");
	define("BLOCK_DOENT_EXIST_MSG", "Block doesn't exist:");
	define("NUMBER_OF_ELEMENTS_MSG", "Number of elements");
	define("MISSING_COMPONENT_MSG", "Missing component/parameter.");
	define("RELEASES_TITLE", "Julkaisut");
	define("DETAILED_MSG", "Detailed");
	define("LIST_MSG", "List");
	define("READONLY_MSG", "Readonly");
	define("CREDIT_MSG", "Credit");
	define("ONLINE_MSG", "Online");
	define("OFFLINE_MSG", "Offline");
	define("SMALL_CART_MSG", "Small Cart");
	define("NEVER_MSG", "Never");
	define("SEARCH_EXACT_WORD_OR_PHRASE", "exact wording or phrase");
	define("SEARCH_ONE_OR_MORE", "one or more of these words");
	define("SEARCH_ALL", "all these words");
	define("RELATED_ARTICLES_MSG", "Related Articles");
	define("RELATED_FORUMS_MSG", "Related Forums");

	define("RECORD_UPDATED_MSG", "The record has been successfully updated.");
	define("RECORD_ADDED_MSG", "New record has been successfully added.");
	define("RECORD_DELETED_MSG", "The record has been successfully deleted.");
	define("FAST_PRODUCT_ADDING_MSG", "Fast Product Adding");

	define("CURRENT_SUBSCRIPTION_MSG", "Current Subscription");
	define("SUBSCRIPTION_EXPIRATION_MSG", "Subscription expiration date");
	define("UPGRADE_DOWNGRADE_MSG", "Upgrade/Downgrade");
	define("SUBSCRIPTION_MONEY_BACK_MSG", "Subscription Money Back");
	define("MONEY_TO_CREDITS_BALANCE_MSG", "money will be added to your credits balance");
	define("USED_VOUCHERS_MSG", "Used Vouchers");
	define("VOUCHERS_TOTAL_MSG", "Vouchers Total");
	define("SUBSCRIPTIONS_GROUPS_MSG", "Subscriptions Groups");
	define("SUBSCRIPTIONS_GROUP_MSG", "Subscriptions Group");
	define("SUBSCRIPTIONS_MSG", "Subscriptions");
	define("SUBSCRIPTION_START_DATE_MSG", "Subscription Start Date");
	define("SUBSCRIPTION_EXPIRY_DATE_MSG", "Subscription Expiration Date");
	define("RECALCULATE_COMMISSIONS_AND_POINTS_MSG", "Automatically recalculate commissions and points for this item using price value");
	define("SUBSCRIPTION_PAGE_MSG", "Subscription page");
	define("SUBSCRIPTION_WITHOUT_REGISTRATION_MSG", "User can add subscriptions to his cart without registration");
	define("SUBSCRIPTION_REQUIRE_REGISTRATION_MSG", "User must have an account before accessing subscription page");

	define("MATCH_EXISTED_PRODUCTS_MSG", "Match Existed Products");
	define("MATCH_BY_ITEM_CODE_MSG", "by product code");
	define("MATCH_BY_MANUFACTURER_CODE_MSG", "by manufacturer code");
	define("DISPLAY_COMPONENTS_AND_OPTIONS_LIST_MSG", "Display components and options list");
	define("AS_LIST_MSG", "as list");
	define("AS_TABLE_MSG", "as table");

	define("ACCOUNT_SUBSCRIPTION_MSG", "Account subscription");
	define("ACCOUNT_SUBSCRIPTION_DESC", "To activate his account user need to pay subscription fee");
	define("SUBSCRIPTION_CANCELLATION_MSG", "Subscription cancellation");
	define("CONFIRM_CANCEL_SUBSCRIPTION_MSG", "Are you sure you want cancel this subscription?");
	define("CONFIRM_RETURN_SUBSCRIPTION_MSG", "Are you sure you want cancel this subscription and return {credits_amount} to balance?");
	define("CANCEL_SUBSCRIPTION_MSG", "Cancel Subscription");
	define("DONT_RETURN_MONEY_MSG", "Don't return the money");
	define("RETURN_MONEY_TO_CREDITS_BALANCE_MSG", "Return money for unsed period to credits balance");
	define("UPGRADE_DOWNGRADE_TYPE_MSG", "Can user upgrade/downgrade his account type");

	define("PREDEFINED_TYPES_MSG", "Predefined Types");
	define("SHIPPING_TAX_PERCENT_MSG", "Shipping Tax Percent");
	define("PACKAGES_NUMBER_MSG", "Number of Packages");
	define("PER_PACKAGE_MSG", "per package");
	define("CURRENCY_SHOW_DESC", "user can choose this currency in which prices will be shown");
	define("CURRENCY_DEFAULT_SHOW_DESC", "all prices shown by default in this currency");
	define("RECOMMENDED_MSG", "Recomended");
	define("UPDATE_STATUS_MSG", "Update status");

	define("FIRST_CONTROLS_ARE_FREE_MSG", "First {free_price_amount} controls are free");
	define("FIRST_LETTERS_ARE_FREE_MSG", "First {free_price_amount} letters are free");
	define("FIRST_NONSPACE_LETTERS_ARE_FREE_MSG", "First  {free_price_amount} non-space letters are free");

	// email & SMS messages
	define("EMAIL_NOTIFICATION_MSG", "S�hk�posti-ilmoitus");
	define("EMAIL_NOTIFICATION_ADMIN_MSG", "Hallinnan email ilmoitus");
	define("EMAIL_NOTIFICATION_USER_MSG", "K�ytt�j�n email ilmoitus");
	define("EMAIL_SEND_ADMIN_MSF", "L�het� ilmoitus webmasterille");
	define("EMAIL_SEND_USER_MSG", "L�het� ilmoitus k�ytt�j�lle");
	define("EMAIL_USER_IF_STATUS_MSG", "L�het� ilmoitus k�ytt�j�lle kun hyv�ksytyy");
	define("EMAIL_TO_MSG", "Kenelle");
	define("EMAIL_TO_USER_DESC", "As. email jos tyhj�");
	define("EMAIL_FROM_MSG", "Kenelt�");
	define("EMAIL_CC_MSG", "Kopio");
	define("EMAIL_BCC_MSG", "Piilokopio");
	define("EMAIL_REPLY_TO_MSG", "Vastaa");
	define("EMAIL_RETURN_PATH_MSG", "Vastauspolku");
	define("EMAIL_SUBJECT_MSG", "Aihe");
	define("EMAIL_MESSAGE_TYPE_MSG", "Viestin tyyppi");
	define("EMAIL_MESSAGE_MSG", "Viestin tyyppi");
	define("SMS_NOTIFICATION_MSG", "SMS ilmoitus");
	define("SMS_NOTIFICATION_ADMIN_MSG", "Webmaster SMS ilmoitus");
	define("SMS_NOTIFICATION_USER_MSG", "K�ytt�j�n SMS ilmoitus");
	define("SMS_SEND_ADMIN_MSF", "L�het� SMS webmasterille");
	define("SMS_SEND_USER_MSG", "L�het� SMS k�ytt�j�lle");
	define("SMS_USER_IF_STATUS_MSG", "L�het� SMS k�ytt�j�lle kun hyv�ksytyy");
	define("SMS_RECIPIENT_MSG", "SMS vastaanottaja");
	define("SMS_RECIPIENT_ADMIN_DESC", "Webmaster gms numero");
	define("SMS_RECIPIENT_USER_DESC", "T�m� kentt� ei voi olla tyhj�");
	define("SMS_ORIGINATOR_MSG", "SMS alkuper�inen");
	define("SMS_MESSAGE_MSG", "SMS viesti");

	// account messages
	define("LOGIN_AS_MSG", "Loggasit sis��n");
	define("LOGIN_INFO_MSG", "Sis.kirj tiedot");
	define("ACCESS_HOME_MSG", "Alkuun");
	define("REMEMBER_LOGIN_MSG", "Muista sis��nkirjautumiseni");
	define("ENTER_LOGIN_MSG", "Kirjaudu sis��n jatkaaksesi");
	define("LOGIN_PASSWORD_ERROR", "Sis��nkirjaus ei onnistunut");
	define("ACCOUNT_APPROVE_ERROR", "Valitettavasti tili� ei ole vahvistettu viel�");
	define("ACCOUNT_EXPIRED_MSG", "Your account has expired.");
	define("NEW_PROFILE_ERROR", "Et voi avata tili�");
	define("EDIT_PROFILE_ERROR", "Et voi muokata t�t� profiilia");
	define("CHANGE_DETAILS_MSG", "Muuta tietojasi");
	define("CHANGE_DETAILS_DESC", "Klikkaa jos haluat muuttaa tietojasi jotka annoit rekister�itymisen yhteydess�");
	define("CHANGE_PASSWORD_MSG", "Vaihda salasana");
	define("CHANGE_PASSWORD_DESC", "T�st� voit vaihtaa salasanasi");
	define("SIGN_UP_MSG", "Uusi tili");
	define("MY_ACCOUNT_MSG", "Oma tilini");
	define("NEW_USER_MSG", "Uusi k�ytt�j�");
	define("EXISTS_USER_MSG", "Vanhat k�ytt�j�t");
	define("EDIT_PROFILE_MSG", "Muuta profiilia");
	define("PERSONAL_DETAILS_MSG", "Henkil�koht. Tiedot");
	define("DELIVERY_DETAILS_MSG", "Toimitustiedot");
	define("SAME_DETAILS_MSG", "Jos toimitustiedot ovat samat, klikkaa t�st� <br> muutoin t�ydenn� tiedot");
	define("DELIVERY_MSG", "Toimitustiedot");
	define("SUBSCRIBE_CHECKBOX_MSG", "Merkitse t�m� jos haluat tilata uutiskirjeen");
	define("ADDITIONAL_DETAILS_MSG", "Lis�tiedot");
	define("GUEST_MSG", "Guest");

	// ads messages
	define("MY_ADS_MSG", "Omat mainokset");
	define("MY_ADS_DESC", "Jos haluat myyd� omia tavaroitasi, voit tehd� sen t��ll�");
	define("AD_GENERAL_MSG", "Lis�tietoja");
	define("ALL_ADS_MSG", "All Ads");
	define("AD_SELLER_MSG", "Myyj�");
	define("AD_START_MSG", "Alkaa");
	define("AD_RUNS_MSG", "P�iv�� voimassa");
	define("AD_QTY_MSG", "Kpl");
	define("AD_AVAILABILITY_MSG", "Saatavuus");
	define("AD_COMPARED_MSG", "Salli mainosten vertailu");
	define("AD_UPLOAD_MSG", "Lataa kuvat");
	define("AD_DESCRIPTION_MSG", "Kuvaus");
	define("AD_SHORT_DESC_MSG", "Lyhyt kuvaus");
	define("AD_FULL_DESC_MSG", "T�ysi kuvaus");
	define("AD_LOCATION_MSG", "Sijainti");
	define("AD_LOCATION_INFO_MSG", "Lis�tietoja");
	define("AD_PROPERTIES_MSG", "Mainosominaisuudet");
	define("AD_SPECIFICATION_MSG", "Mainosm��ritykset");
	define("AD_MORE_IMAGES_MSG", "Lis�� kuvia");
	define("AD_IMAGE_DESC_MSG", "Kuvaus");
	define("AD_DELETE_CONFIRM_MSG", "Haluatko poistaa t�m�n mainoksen?");
	define("AD_NOT_APPROVED_MSG", "Ei hyv�ksytty");
	define("AD_RUNNING_MSG", "Voimassa");
	define("AD_CLOSED_MSG", "Suljettu");
	define("AD_NOT_STARTED_MSG", "Ei alkanut");
	define("AD_NEW_ERROR", "Sinulla ei ole oikeutta luoda ilmoituksia");
	define("AD_EDIT_ERROR", "Sinulla ei ole oikeutta muuttaa ilmoituksia");
	define("AD_DELETE_ERROR", "Sinulla ei ole oikeutta poistaa ilmoituksia");
	define("NO_ADS_MSG", "Ei mainoksia");
	define("NO_AD_MSG", "T�ll� ID:ll� ei l�ytynyt ilmoituksia");
	define("FOUND_ADS_MSG", "L�ysimme <b>{found_records}</b> mainosta hakukriteereill� '<b>{search_string}</b>'");
	define("AD_OFFER_MESSAGE_MSG", "Tarjous");
	define("AD_OFFER_LOGIN_ERROR", "Kirjaudu sis��n jatkaaksesi");
	define("AD_REQUEST_BUTTON", "L�het� tiedustelu");
	define("AD_SENT_MSG", "Tarjouksesi on l�hetetty");
	define("ADS_SETTINGS_MSG", "Ads Settings");
	define("ADS_DAYS_MSG", "Days to run Ad");
	define("ADS_HOT_DAYS_MSG", "Days to run Hot Ad");
	define("AD_HOT_OFFER_MSG", "Hot Offer");
	define("AD_HOT_ACTIVATE_MSG", "Add this Ad to Hot Offers section");
	define("AD_HOT_START_MSG", "Hot Offer Start Date");
	define("AD_HOT_DESCRIPTION_MSG", "Hot Description");
	define("ADS_SPECIAL_DAYS_MSG", "Days to run Special Ad");
	define("AD_SPECIAL_OFFER_MSG", "Special Offer");
	define("AD_SPECIAL_ACTIVATE_MSG", "Add this Ad to Special Offers section");
	define("AD_SPECIAL_START_MSG", "Special Offer Start Date");
	define("AD_SPECIAL_DESCRIPTION_MSG", "Special Offer Description");
	define("AD_SHOW_ON_SITE_MSG", "Show on site");
	define("AD_CREDITS_BALANCE_ERROR", "Not enough credits on your balance, you need {more_credits} more to post this Ad.");
	define("AD_CREDITS_MSG", "Ad Credits");
	define("ADS_SPECIAL_OFFERS_SETTINGS_MSG", "Ads Special Offers Settings");

	define("EDIT_DAY_MSG", "Edit Day");
	define("DAYS_PRICE_MSG", "Days Price");
	define("ADS_PUBLISH_PRICE_MSG", "Price to post Ad");
	define("DAYS_NUMBER_MSG", "Number of Days");
	define("DAYS_TITLE_MSG", "Days Title");
	define("USER_ADS_LIMIT_MSG", "Number of ads can be added by user");
	define("USER_ADS_LIMIT_DESC", "leave this field blank if you do not want to limit number of ads user can post");
	define("USER_ADS_LIMIT_ERROR", "Sorry, but you are not allowed add more than {ads_limit} ads.");

	define("ADS_SHOW_TERMS_MSG", "Show Terms & Conditions");
	define("ADS_SHOW_TERMS_DESC", "To submit an ad user has to read and agree to our terms and conditions");
	define("ADS_TERMS_MSG", "Terms & Conditions");
	define("ADS_TERMS_USER_DESC", "I have read and agree to your terms and conditions");
	define("ADS_TERMS_USER_ERROR", "To submit an ad you have to read and agree to our terms and conditions");
	define("ADS_ACTIVATION_MSG", "Ads Activation");
	define("ACTIVATE_ADS_MSG", "Activate Ads");
	define("ACTIVATE_ADS_NOTE", "automatically activate all user ads if his status changed to 'Approved'");
	define("DEACTIVATE_ADS_MSG", "Deactivate Ads");
	define("DEACTIVATE_ADS_NOTE", "automatically deactivate all user ads if his status changed to 'Not Approved'");

	define("MIN_ALLOWED_ADS_PRICE_MSG", "Minimum Allowed Price");
	define("MIN_ALLOWED_ADS_PRICE_NOTE", "leave this field blank if you do not want to limit the lower price for ads");
	define("MAX_ALLOWED_ADS_PRICE_MSG", "Maximum Allowed Price");
	define("MAX_ALLOWED_ADS_PRICE_NOTE", "leave this field blank if you do not want to limit the higher price for ads");

	// search message
	define("SEARCH_FOR_MSG", "Etsi");
	define("SEARCH_IN_MSG", "Etsi mist�");
	define("SEARCH_TITLE_MSG", "Otsikko");
	define("SEARCH_CODE_MSG", "Koodi");
	define("SEARCH_SHORT_DESC_MSG", "Lyhyt kuvaus");
	define("SEARCH_FULL_DESC_MSG", "Tarkempi kuvaus");
	define("SEARCH_CATEGORY_MSG", "Etsi luokasta");
	define("SEARCH_MANUFACTURER_MSG", "Valmistaja");
	define("SEARCH_SELLER_MSG", "Myyj�");
	define("SEARCH_PRICE_MSG", "Hintahaarukka");
	define("SEARCH_WEIGHT_MSG", "Painoraja");
	define("SEARCH_RESULTS_MSG", "Hakutulokset");
	define("FULL_SITE_SEARCH_MSG", "Full Site Search");

	// compare messages
	define("COMPARE_MSG", "Vertaa");
	define("COMPARE_REMOVE_MSG", "Poista");
	define("COMPARE_REMOVE_HELP_MSG", "Klikkaa t�st� poistaaksesi t�m�n tuotteen vertailusta");
	define("COMPARE_MIN_ALLOWED_MSG", "Valitse v�hint��n 2 tuotetta");
	define("COMPARE_MAX_ALLOWED_MSG", "Max 5 tuotetta");
	define("COMPARE_PARAM_ERROR_MSG", "Vertailuparametreill� v��r� muoto");

	// Tell a friend messages
	define("TELL_FRIEND_TITLE", "Kerro Kaverille");
	define("TELL_FRIEND_SUBJECT_MSG", "Kaverisi on l�hett�nyt t�m�n linkin");
	define("TELL_FRIEND_DEFAULT_MSG", "Moro {friend_name} -Kiinnostaisiko t�ll�inen tuote {item_title} t�ll� sivustolla {item_url}");
	define("TELL_YOUR_NAME_FIELD", "Nimesi");
	define("TELL_YOUR_EMAIL_FIELD", "S�hk�postisi");
	define("TELL_FRIENDS_NAME_FIELD", "Yst�v�n nimi");
	define("TELL_FRIENDS_EMAIL_FIELD", "Yst�v�n email");
	define("TELL_COMMENT_FIELD", "Kommentit");
	define("TELL_FRIEND_PRIVACY_NOTE_MSG", "HUOM! Emme tallenna s�hk�postiosoitteita!");
	define("TELL_SENT_MSG", "Viesti l�hetetty!<br>Kiitos!");
	define("TELL_FRIEND_MESSAGE_MSG", "Thought you might be interested in seeing the {item_title} at {item_url}\\n\\n{user_name} left you a note:\\n{user_comment}");
	define("TELL_FRIEND_PARAM_MSG", "Introduce a friend' URL");
	define("TELL_FRIEND_PARAM_DESC", "adds a friend's URL parameter to a 'Tell a Friend' link if it exists for a user");
	define("FRIEND_COOKIE_EXPIRES_MSG", "Friend Cookie Expires");

	define("CONTACT_US_TITLE", "Contact Us");
	define("CONTACT_USER_NAME_FIELD", "Oma nimesi");
	define("CONTACT_USER_EMAIL_FIELD", "S�hk�postisi");
	define("CONTACT_SUMMARY_FIELD", "Yhden rivin kuvaus");
	define("CONTACT_DESCRIPTION_FIELD", "Kuvaus");
	define("CONTACT_REQUEST_SENT_MSG", "Your request was successfully sent.");

	// buttons
	define("GO_BUTTON", "Mene");
	define("CONTINUE_BUTTON", "Jatka");
	define("BACK_BUTTON", "Takaisin");
	define("NEXT_BUTTON", "Seuraava");
	define("PREV_BUTTON", "Edellinen");
	define("SIGN_IN_BUTTON", "Kirjaudu");
	define("LOGIN_BUTTON", "Kirjaudu");
	define("LOGOUT_BUTTON", "Ulos");
	define("SEARCH_BUTTON", "Etsi");
	define("RATE_IT_BUTTON", "Arvostele!");
	define("ADD_BUTTON", "Lis��");
	define("UPDATE_BUTTON", "P�ivit�");
	define("APPLY_BUTTON", "K�yt�");
	define("REGISTER_BUTTON", "Rekister�i");
	define("VOTE_BUTTON", "��nest�");
	define("CANCEL_BUTTON", "Peruuta");
	define("CLEAR_BUTTON", "Tyhjenn�");
	define("RESET_BUTTON", "Nollaa");
	define("DELETE_BUTTON", "Poista");
	define("DELETE_ALL_BUTTON", "Delete All");
	define("SUBSCRIBE_BUTTON", "Tilaa");
	define("UNSUBSCRIBE_BUTTON", "Peruuta tilaus");
	define("SUBMIT_BUTTON", "L�het�");
	define("UPLOAD_BUTTON", "Lataa");
	define("SEND_BUTTON", "L�het�");
	define("PREVIEW_BUTTON", "Esikatselu");
	define("FILTER_BUTTON", "Suodatin");
	define("DOWNLOAD_BUTTON", "Download");
	define("REMOVE_BUTTON", "Remove");
	define("EDIT_BUTTON", "Edit");

	// controls
	define("CHECKBOXLIST_MSG", "Checkboxes List");
	define("LABEL_MSG", "Label");
	define("LISTBOX_MSG", "ListBox");
	define("RADIOBUTTON_MSG", "Radio Buttons");
	define("TEXTAREA_MSG", "TextArea");
	define("TEXTBOX_MSG", "TextBox");
	define("TEXTBOXLIST_MSG", "Text Boxes List");
	define("IMAGEUPLOAD_MSG", "Image Upload");
	define("CREDIT_CARD_MSG", "Credit Card");
	define("GROUP_MSG", "Group");

	// fields
	define("LOGIN_FIELD", "K�ytt�j�nimi");
	define("PASSWORD_FIELD", "Salasana");
	define("CONFIRM_PASS_FIELD", "Vahvista salasana");
	define("NEW_PASS_FIELD", "Uusi salasana");
	define("CURRENT_PASS_FIELD", "Nykyinen salasana");
	define("FIRST_NAME_FIELD", "Etunimi");
	define("LAST_NAME_FIELD", "Sukunimi");
	define("NICKNAME_FIELD", "Lempinimi");
	define("PERSONAL_IMAGE_FIELD", "Henk.koht kuva");
	define("COMPANY_SELECT_FIELD", "Yritys");
	define("SELECT_COMPANY_MSG", "Valitse yritys");
	define("COMPANY_NAME_FIELD", "Yritysnimi");
	define("EMAIL_FIELD", "S�hk�posti");
	define("STREET_FIRST_FIELD", "Katuosoite 1");
	define("STREET_SECOND_FIELD", "Katuosoite 2");
	define("CITY_FIELD", "Kaupunki");
	define("PROVINCE_FIELD", "Maakunta");
	define("SELECT_STATE_MSG", "Select State");
	define("STATE_FIELD", "Osavaltio");
	define("ZIP_FIELD", "Postinumero");
	define("SELECT_COUNTRY_MSG", "Valitse maa");
	define("COUNTRY_FIELD", "Maakunta");
	define("PHONE_FIELD", "Puhelin");
	define("DAYTIME_PHONE_FIELD", "Puhelin p�ivisin");
	define("EVENING_PHONE_FIELD", "Puhelin iltaisin");
	define("CELL_PHONE_FIELD", "Matkapuhelin");
	define("FAX_FIELD", "Fax");
	define("VALIDATION_CODE_FIELD", "Varmennuskoodi");
	define("AFFILIATE_CODE_FIELD", "J�lleenmyyj�koodi");
	define("AFFILIATE_CODE_HELP_MSG", "K�yt� seuraavaa URL:t� {affiliate_url} linkitt��ksesi j�lleenmyyj�t");
	define("PAYPAL_ACCOUNT_FIELD", "PayPal tili");
	define("TAX_ID_FIELD", "VAT numero");
	define("MSN_ACCOUNT_FIELD", "MSN Account");
	define("ICQ_NUMBER_FIELD", "ICQ Number");
	define("USER_SITE_URL_FIELD", "User's Site URL");
	define("HIDDEN_STATUS_FIELD", "Hidden Status");
	define("HIDE_MY_ONLINE_STATUS_MSG", "Do not show my online status");
	define("SUMMARY_MSG", "Yhteenveto");

	// no records messages
	define("NO_RECORDS_MSG", "Ei tietoja");
	define("NO_EVENTS_MSG", "Ei tapahtumia");
	define("NO_QUESTIONS_MSG", "Ei kysymyksi�");
	define("NO_NEWS_MSG", "Ei uusia artikkeleita");
	define("NO_POLLS_MSG", "Ei ��nestyksi�");

	// SMS messages
	define("SMS_TITLE", "SMS");
	define("SMS_TEST_TITLE", "SMS Testi");
	define("SMS_TEST_DESC", "Anna matkapuhelinnumerosi ja paina 'SEND_BUTTON' vastaanottaaksesi testiviestin");
	define("INVALID_CELL_PHONE", "GSM numero v��rin");

	define("ARTICLE_RELATED_PRODUCTS_TITLE", "Article Related Products");
	define("CATEGORY_RELATED_PRODUCTS_TITLE", "Category Related Products");
	define("SELECT_TYPE_MSG", "Valitse tyyppi");
	define("OFFER_PRICE_MSG", "Offer Price");
	define("OFFER_MESSAGE_MSG", "Offer Message");

	define("MY_WISHLIST_MSG", "My Wishlist");
	define("MY_REMINDERS_MSG", "My Reminders");
	define("EDIT_REMINDER_MSG", "Edit Reminder");

	define("PARAMETER_WRONG_VALUE_MSG", "{param_name} parameter has wrong value.");
	define("CANNOT_OBTAIN_PARAMETER_MSG", "Can't obtain {param_name} parameter.");
	define("TRANSACTION_DECLINED_MSG", "Your transaction has been declined.");
	define("UNEXPECTED_GATEWAY_RESPONSE_MSG", "Unexpected response format from gateway.");
	define("EMPTY_GATEWAY_RESPONSE_MSG", "Empty response from gateway. Please check your settings.");
	define("CURL_INIT_ERROR_MSG", "Can't initialize cURL.");

	define("PROCESSING_TRANSACTION_ERROR_MSG", "There has occurred an error while processing the transaction.");

	define("UPS_PARAMETER_REQUIRED_MSG", "UPS module error: {param_name} is required.");
	define("CONFIRM_SUBSCRIPTION_MSG", "Add this subscription to your Shopping Cart?");
	define("ADDED_SUBSCRIPTION_MSG", "{subscription_name} was added to your Shopping Cart.");
	define("SELECT_SUBSCRIPTION_MSG", "Please select subscription to add to your Shopping Cart.");
	define("SELECT_SUBFOLDER_MSG", "Select Subfolder");
	define("CURRENT_DIR_MSG", "Current directory");
	define("NO_AVAILIABLE_CATEGORIES_MSG", "No categories available");
	define("SHOW_FOR_NON_REGISTERED_USERS_MSG", "Show for non registered users");
	define("SHOW_FOR_REGISTERED_USERS_MSG", "Show for all registered users");
	define("VIEW_ITEM_IN_THE_LIST_MSG", "View item in products list");
	define("ACCESS_DETAILS_MSG", "Access Details");
	define("ACCESS_ITEMS_DETAILS_MSG", "Access item details / buy item");
	define("OTHER_SUBSCRIPTIONS_MSG", "Other Subscriptions");
	define("USE_CATEGORY_ALL_SITES_MSG", "Use this category for all sites (untick this checkbox to select sites manually)");
	define("USE_ITEM_ALL_SITES_MSG", "Use this item for all sites (untick this checkbox to select sites manually) ");
	define("SAVE_SUBSCRIPTIONS_SETTINGS_BY_CATEGORY_MSG", "Save subscriptions settings from categories");
	define("SAVE_SITES_SETTINGS_BY_CATEGORY_MSG", "Save sites settings from categories");
	define("ACCESS_LEVELS_MSG", "Access Levels");
	define("SITES_MSG", "Sites");
	define("NON_REGISTERED_USERS_MSG", "Non registered users");
	define("REGISTERED_CUSTOMERS_MSG", "Registered Users");
	define("PREVIEW_IN_SEPARATE_SECTION_MSG", "Show in separate section");
	define("PREVIEW_BELOW_DETAILS_IMAGE_MSG", "Show below image on details page");
	define("PREVIEW_BELOW_LIST_IMAGE_MSG", "Show below image on listing page");
	define("PREVIEW_POSITION_MSG", "Position");
	define("ADMIN_NOTES_MSG", "Administrator Notes");
	define("USER_NOTES_MSG", "User Notes");
	define("CMS_PERMISSIONS_MSG", "CMS Permissions");
	define("ARTICLES_PERMISSIONS_MSG", "Articles Permissions");
	define("FOOTER_LINK_MSG", "Footer Link");
	define("FOOTER_LINKS_MSG", "Footer Links");

?>
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


	define("CHARSET", "iso-8859-2");
	//d�tum �zenetek
	define("YEAR_MSG", "�v");
	define("YEARS_QTY_MSG", "{quantity} years");
	define("MONTH_MSG", "h�nap");
	define("MONTHS_QTY_MSG", "{quantity} months");
	define("DAY_MSG", "nap");
	define("DAYS_MSG", "nap");
	define("DAYS_QTY_MSG", "{quantity} days");
	define("HOUR_MSG", "�ra");
	define("HOURS_QTY_MSG", "{quantity} hours");
	define("MINUTE_MSG", "perc");
	define("MINUTES_QTY_MSG", "{quantity} minutes");
	define("SECOND_MSG", "m�sodperc");
	define("SECONDS_QTY_MSG", "{quantity} seconds");
	define("WEEK_MSG", "h�t");
	define("WEEKS_QTY_MSG", "{quantity} weeks");
	define("TODAY_MSG", "ma");
	define("YESTERDAY_MSG", "tegnap");
	define("LAST_7DAYS_MSG", "legut�bbi 7 nap");
	define("THIS_MONTH_MSG", "ez a h�nap");
	define("LAST_MONTH_MSG", "m�lt h�nap");
	define("THIS_QUARTER_MSG", "ez a negyed�v");
	define("THIS_YEAR_MSG", "ez �v");

	//h�napok
	define("JANUARY", "janu�r");
	define("FEBRUARY", "febru�r");
	define("MARCH", "m�rcius");
	define("APRIL", "�prilis");
	define("MAY", "m�jus");
	define("JUNE", "j�nius");
	define("JULY", "j�lius");
	define("AUGUST", "augusztus");
	define("SEPTEMBER", "szeptember");
	define("OCTOBER", "okt�ber");
	define("NOVEMBER", "november");
	define("DECEMBER", "december");

	define("JANUARY_SHORT", "jan.");
	define("FEBRUARY_SHORT", "febr.");
	define("MARCH_SHORT", "m�rc.");
	define("APRIL_SHORT", "�pr.");
	define("MAY_SHORT", "m�j");
	define("JUNE_SHORT", "j�n.");
	define("JULY_SHORT", "j�l.");
	define("AUGUST_SHORT", "aug.");
	define("SEPTEMBER_SHORT", "szept.");
	define("OCTOBER_SHORT", "okt.");
	define("NOVEMBER_SHORT", "nov.");
	define("DECEMBER_SHORT", "dec.");

	//h�t napjai
	define("SUNDAY", "vas�rnap");
	define("MONDAY", "h�tf�");
	define("TUESDAY", "kedd");
	define("WEDNESDAY", "szerda");
	define("THURSDAY", "cs�t�rt�k");
	define("FRIDAY", "p�ntek");
	define("SATURDAY", "szombat");

	define("SUNDAY_SHORT", "v");
	define("MONDAY_SHORT", "h");
	define("TUESDAY_SHORT", "k");
	define("WEDNESDAY_SHORT", "sze");
	define("THURSDAY_SHORT", "cs");
	define("FRIDAY_SHORT", "p");
	define("SATURDAY_SHORT", "szo.");

	//meger�s�t� �zenetek
	define("REQUIRED_MESSAGE", "A <b>{field_name}</b> sz�ks�ges");
	define("UNIQUE_MESSAGE", "Az �rt�k az <b>{field_name}</b>mez�ben m�r l�tezik az adatb�zisban");
	define("VALIDATION_MESSAGE", "Nem siker�lt �rv�nyes�teni <b>{field_name}</b>");
	define("MATCHED_MESSAGE", "<b>{field_one}</b> �s a <b>{field_two}</b> nem egyezik");
	define("INSERT_ALLOWED_ERROR", "Sajn�lom, de nem jogosult a beilleszt�s m�velethez");
	define("UPDATE_ALLOWED_ERROR", "Sajn�lom, de nem jogosult a friss�t�s m�velethez");
	define("DELETE_ALLOWED_ERROR", "Sajn�lom, de nem jogosult a t�rl�s m�velethez");
	define("ALPHANUMERIC_ALLOWED_ERROR", "Only alpha-numeric characters, hyphen and underscore are allowed for field <b>{field_name}</b>");

	define("INCORRECT_DATE_MESSAGE", "A <b>{field_name}</b> mez�ben helytelen d�tum forma van. Haszn�lja a napt�rt.");
	define("INCORRECT_MASK_MESSAGE", "<b>{field_name}</b> nem egyezik beviteli maszkkal. A k�vetkez�k�ppen haszn�lja:'<b>{field_mask}</b>'");
	define("INCORRECT_EMAIL_MESSAGE", "�rv�nytelen email form�tum a mez�ben {field_name}");
	define("INCORRECT_VALUE_MESSAGE", "Helytelen �rt�k a mez�ben <b>{field_name}</b>");

	define("MIN_VALUE_MESSAGE", "Ebben a mez�ben <b>{field_name}</b> az �rt�k nem lehet kevesebb, mint {min_value}");
	define("MAX_VALUE_MESSAGE", "Ebben a mez�ben <b>{field_name}</b> az �rt�k nem lehet t�bb, mint {max_value}");
	define("MIN_LENGTH_MESSAGE", "A hossz�s�g itt <b>{field_name}</b>nem lehet kevesebb , mint {min_length} karakter");
	define("MAX_LENGTH_MESSAGE", "A hossz�s�g itt <b>{field_name}</b> nem lehet t�bb. mint {max_length} karakter");

	define("FILE_PERMISSION_MESSAGE", "Nem rendelkezik �r�si enged�llyel a f�jlra <b>'{file_name}'</b>. K�rem v�ltoztassa meg a f�jl �r�si enged�lyt, miel�tt folytatja.");
	define("FOLDER_PERMISSION_MESSAGE", "Nem rendelkezik �r�si enged�llyel a k�nyvt�rra <b>'{folder_name}'</b>.K�rem v�ltoztassa meg k�nyvt�r �r�si enged�lyt, miel�tt folytatja.");
	define("INVALID_EMAIL_MSG", "Az emailed �rv�nytelen.");
	define("DATABASE_ERROR_MSG", "Adatb�zis hiba t�rt�nt.");
	define("BLACK_IP_MSG", "Ez a tev�kenys�g nem megengedett a kiszolg�l�dn�l.");
	define("BANNED_CONTENT_MSG", "Sajn�lom,az adott sz�veg nem megengedett kifejez�st tartalmaz.");
	define("ERRORS_MSG", "Hib�k");
	define("REGISTERED_ACCESS_MSG", "Csak regisztr�lt felhaszn�lok f�rhetnek hozz� ehhez az opci�hoz");
	define("SELECT_FROM_LIST_MSG", "V�lassz a list�b�l");

	//c�mek 
	define("TOP_RATED_TITLE", "Legjobbnak  �rt�kelt");
	define("TOP_VIEWED_TITLE", "Legjobban n�zett");
	define("RECENTLY_VIEWED_TITLE", "Nemr�giben n�zett");
	define("HOT_TITLE", "Legfrissebb");
	define("LATEST_TITLE", "leg�jabb");
	define("CONTENT_TITLE", "Tartalom");
	define("RELATED_TITLE", "Kapcsol�d�");
	define("SEARCH_TITLE", "Keres�s");
	define("ADVANCED_SEARCH_TITLE", "B�v�tett keres�s");
	define("LOGIN_TITLE", "Felhaszn�l� bejelentkez�s");
	define("CATEGORIES_TITLE", "Kateg�ri�k");
	define("MANUFACTURERS_TITLE", "Gy�rt�k");
	define("SPECIAL_OFFER_TITLE", "K�l�nleges k�n�lat");
	define("NEWS_TITLE", "H�rek");
	define("EVENTS_TITLE", "Esem�nyek");
	define("PROFILE_TITLE", "Profil");
	define("USER_HOME_TITLE", "Kezd�lap");
	define("DOWNLOAD_TITLE", "Let�lt�s");
	define("FAQ_TITLE", "Gyakran ism�telt k�rd�sek");
	define("POLL_TITLE", "Szavaz�s");
	define("HOME_PAGE_TITLE", "Kezd�lap");
	define("CURRENCY_TITLE", "P�nznem");
	define("SUBSCRIBE_TITLE", "Feliratkoz�s");
	define("UNSUBSCRIBE_TITLE", "Leiratkoz�s");
	define("UPLOAD_TITLE", "Felt�lt�s");
	define("ADS_TITLE", "Hirdet�sek");
	define("ADS_COMPARE_TITLE", "Hirdet�s �sszehasonl�t�s");
	define("ADS_SELLERS_TITLE", "Elad�k");
	define("AD_REQUEST_TITLE", "Aj�nlatt�tel/k�rd�s a hirdet�h�z");
	define("LANGUAGE_TITLE", "Nyelvek");
	define("MERCHANTS_TITLE", "Keresked�k");
	define("PREVIEW_TITLE", "El�n�zet");
	define("ARTICLES_TITLE", "Articles");
	define("SITE_MAP_TITLE", "Site Map");
	define("LAYOUTS_TITLE", "Layouts");

	//men� t�telek
	define("MENU_ABOUT", "R�lunk");
	define("MENU_ACCOUNT", "Sz�ml�m");
	define("MENU_BASKET", "Kosaram");
	define("MENU_CONTACT", "kapcsolat");
	define("MENU_DOCUMENTATION", "Dokument�ci�");
	define("MENU_DOWNLOADS", "Let�lt�sek");
	define("MENU_EVENTS", "Esem�nyek");
	define("MENU_FAQ", "Gyik");
	define("MENU_FORUM", "F�rum");
	define("MENU_HELP", "Seg�ts�g");
	define("MENU_HOME", "Kezd�lap");
	define("MENU_HOW", "Hogyan v�s�roljunk");
	define("MENU_MEMBERS", "Tagok");
	define("MENU_MYPROFILE", "Profilom");
	define("MENU_NEWS", "H�rek");
	define("MENU_PRIVACY", "Szem�lyes");
	define("MENU_PRODUCTS", "Term�kek");
	define("MENU_REGISTRATION", "Regisztr�ci�");
	define("MENU_SHIPPING", "Kisz�ll�t�s");
	define("MENU_SIGNIN", "Bejelentkez�s");
	define("MENU_SIGNOUT", "Kijelentkez�s");
	define("MENU_SUPPORT", "T�mogat�s");
	define("MENU_USERHOME", "Felhaszn�l� kezd�lap");
	define("MENU_ADS", "Oszt�lyozott hirdet�sek");
	define("MENU_ADMIN", "Adminisztr�ci�");
	define("MENU_KNOWLEDGE", "tud�sb�zis");

	//f� kifejez�sek
	define("NO_MSG", "Nem");
	define("YES_MSG", "Igen");
	define("NOT_AVAILABLE_MSG", "Nem el�rhet�");
	define("MORE_MSG", "Tov�bb...");
	define("READ_MORE_MSG", "Tov�bb olvasni...");
	define("CLICK_HERE_MSG", "Kattints ide");
	define("ENTER_YOUR_MSG", "Ird be a te");
	define("CHOOSE_A_MSG", "V�lassz egyet");
	define("PLEASE_CHOOSE_MSG", "K�rem v�lasszon");
	define("SELECT_MSG", "Kijel�l");
	define("DATE_FORMAT_MSG", "A k�vetkez� form�tumot haszn�ld <b>{date_format}</b>");
	define("NEXT_PAGE_MSG", "K�vetkez�");
	define("PREV_PAGE_MSG", "El�z�");
	define("FIRST_PAGE_MSG", "Els�");
	define("LAST_PAGE_MSG", "Utols�");
	define("OF_PAGE_MSG", " / ");
	define("TOP_CATEGORY_MSG", "F�kateg�ria");
	define("SEARCH_IN_CURRENT_MSG", "Jelenlegi kateg�ria");
	define("SEARCH_IN_ALL_MSG", "Mindegyik kateg�ria");
	define("FOUND_IN_MSG", "Tal�lat ebben");
	define("TOTAL_VIEWS_MSG", "�sszes n�z�s");
	define("VOTES_MSG", "Szavazatok");
	define("TOTAL_VOTES_MSG", "�sszes szavazat");
	define("TOTAL_POINTS_MSG", "�sszes pont");
	define("VIEW_RESULTS_MSG", "Eredm�ny megn�z�se");
	define("PREVIOUS_POLLS_MSG", "El�z� szavaz�sok");
	define("TOTAL_MSG", "�sszes");
	define("CLOSED_MSG", "Z�rva");
	define("CLOSE_WINDOW_MSG", "Ablak bez�r�sa");
	define("ASTERISK_MSG", "Csillag (*)- k�telez� kit�lteni");
	define("PROVIDE_INFO_MSG", "K�rem,hogy gondoskodj inform�ci�r�l a piros r�szekben �s nyomjad meg a'{button_name}' gombot");
	define("FOUND_ARTICLES_MSG", "tal�ltunk  <b>{found_records}</b> cikket ami megfelel a '<b>{search_string}</b> kifejez�snek");
	define("NO_ARTICLE_MSG", "Cikk ezzel az azonos�t� sz�mmal nem el�rhet�");
	define("NO_ARTICLES_MSG", "Nem tal�ltunk cikket");
	define("NOTES_MSG", "Megjegyz�sek");
	define("KEYWORDS_MSG", "Kulcsszavak");
	define("LINK_URL_MSG", "Link");
	define("DOWNLOAD_URL_MSG", "Let�lt�s");
	define("SUBSCRIBE_FORM_MSG", "H�rlevel�nkre val� feliratkoz�shoz �rd be az email c�medet, �s nyomd meg a '{button_name}'  gombot.");
	define("UNSUBSCRIBE_FORM_MSG", "K�rem beg�pelni az email c�med mez�be �s nyomd meg a'{button_name}'  gombot");
	define("SUBSCRIBE_LINK_MSG", "Feliratkoz�s");
	define("UNSUBSCRIBE_LINK_MSG", "Leiratkoz�s");
	define("SUBSCRIBED_MSG", "Gratul�lok! Most bejegyzett tag vagy a h�rlev�l k�ld�shez.");
	define("ALREADY_SUBSCRIBED_MSG", "M�r feliratkozt�l a h�rlev�lre. K�sz�nj�k.");
	define("UNSUBSCRIBED_MSG", "Sikeresen leiratkozt�l a h�rlevel�nkr�l. K�sz�nj�k.");
	define("UNSUBSCRIBED_ERROR_MSG", "Sajn�lunk de nem tal�ljuk az email c�med az adatb�zisunkban,  val�sz�n�leg m�r leiratkozt�l a h�rlev�lb�l.");
	define("FORGOT_PASSWORD_MSG", "Elfelejtetted a jelsz�dat?");
	define("FORGOT_PASSWORD_DESC", "K�rem be�rni az e-mail c�met, amit haszn�lt�l regisztr�l�sn�l:");
	define("FORGOT_EMAIL_ERROR_MSG", "Sajn�ljuk de nem tal�lunk egy megegyez� email c�met az adatb�zisunkban.");
	define("FORGOT_EMAIL_SENT_MSG", "A r�szletes bejelentkez�si utas�t�sok elk�ldve az email c�medre.");
	define("RESET_PASSWORD_REQUIRE_MSG", "N�h�ny k�v�natos param�ter hi�nyzik.");
	define("RESET_PASSWORD_PARAMS_MSG", "Az elk�ld�tt param�terek nem stimmelnek az adatb�zisunkkal.");
	define("RESET_PASSWORD_EXPIRY_MSG", "Az �jraind�tott k�d lej�rt. K�rem k�rjen egy �j k�dot az �jra be�ll�t�s�hoz a jelsz�dnak.");
	define("RESET_PASSWORD_SAVED_MSG", "Az �j jelsz�dat sikeresen mentett�k.");
	define("PRINTER_FRIENDLY_MSG", "Nyomtat�bar�t");
	define("PRINT_PAGE_MSG", "Az oldal nyomtat�sa");
	define("ATTACHMENTS_MSG", "Mell�klet");
	define("VIEW_DETAILS_MSG", "N�zd meg a r�szleteket");
	define("HTML_MSG", "HTML");
	define("PLAIN_TEXT_MSG", "Sima sz�veg");
	define("META_DATA_MSG", "Meta adat");
	define("META_TITLE_MSG", "Oldal c�m");
	define("META_KEYWORDS_MSG", "Meta kulcsszavak");
	define("META_DESCRIPTION_MSG", "Meta le�r�s");
	define("FRIENDLY_URL_MSG", "Bar�ts�gos URL");
	define("IMAGES_MSG", "K�pek");
	define("IMAGE_MSG", "K�p");
	define("IMAGE_TINY_MSG", "Tiny Image");
	define("IMAGE_TINY_ALT_MSG", "Tiny Image Alt");
	define("IMAGE_SMALL_MSG", "Kis K�p");
	define("IMAGE_SMALL_DESC", "Megn�zni a lista oldalon");
	define("IMAGE_SMALL_ALT_MSG", "Kis k�p  v�ltozat");
	define("IMAGE_LARGE_MSG", "Nagy k�p");
	define("IMAGE_LARGE_DESC", "megn�zni a r�szletez� oldalon");
	define("IMAGE_LARGE_ALT_MSG", "Nagy k�p v�ltozat");
	define("IMAGE_SUPER_MSG", "Szuper-nagym�ret� k�p");
	define("IMAGE_SUPER_DESC", "Felbukkan� k�p �j ablakban");
	define("IMAGE_POSITION_MSG", "Image Position");
	define("UPLOAD_IMAGE_MSG", "K�p felt�lt�se");
	define("UPLOAD_FILE_MSG", "F�jl felt�lt�se");
	define("SELECT_IMAGE_MSG", "V�lassz k�pet");
	define("SELECT_FILE_MSG", "V�lassz f�jlt");
	define("SHOW_BELOW_PRODUCT_IMAGE_MSG", "show image below large product image");
	define("SHOW_IN_SEPARATE_SECTION_MSG", "show image in separate images section");
	define("IS_APPROVED_MSG", "Elfogadva");
	define("NOT_APPROVED_MSG", "Not Approved");
	define("IS_ACTIVE_MSG", "Is Active");
	define("CATEGORY_MSG", "Kateg�ria");
	define("SELECT_CATEGORY_MSG", "Kateg�ria v�laszt�s");
	define("DESCRIPTION_MSG", "Le�r�s");
	define("SHORT_DESCRIPTION_MSG", "R�vid Le�r�s");
	define("FULL_DESCRIPTION_MSG", "Teljes Le�r�s");
	define("HIGHLIGHTS_MSG", "Kiemel�sek");
	define("SPECIAL_OFFER_MSG", "K�l�nleges K�n�lat");
	define("ARTICLE_MSG", "Article");
	define("OTHER_MSG", "M�s");
	define("WIDTH_MSG", "Sz�less�g");
	define("HEIGHT_MSG", "Magass�g");
	define("LENGTH_MSG", "Hossz�s�g");
	define("WEIGHT_MSG", "S�ly");
	define("QUANTITY_MSG", "Mennyis�g");
	define("CALENDAR_MSG", "Napt�r");
	define("FROM_DATE_MSG", "D�tumt�l");
	define("TO_DATE_MSG", "D�tumig");
	define("TIME_PERIOD_MSG", "Id�szak");
	define("GROUP_BY_MSG", "Csoport");
	define("BIRTHDAY_MSG", "Birthday");
	define("BIRTH_DATE_MSG", "Birth Date");
	define("BIRTH_YEAR_MSG", "Birth Year");
	define("BIRTH_MONTH_MSG", "Birth Month");
	define("BIRTH_DAY_MSG", "Birth Day");
	define("STEP_NUMBER_MSG", "Step {current_step} of {total_steps}");
	define("WHERE_STATUS_IS_MSG", "Ahol a st�tusz");
	define("ID_MSG", "ID");
	define("QTY_MSG", "Qty");
	define("TYPE_MSG", "T�pus");
	define("NAME_MSG", "Name");
	define("TITLE_MSG", "C�m");
	define("DEFAULT_MSG", "Default");
	define("OPTIONS_MSG", "Options");
	define("EDIT_MSG", "Edit");
	define("CONFIRM_DELETE_MSG", "Would you like to delete this {record_name}?");
	define("DESC_MSG", "Desc");
	define("ASC_MSG", "Asc");
	define("ACTIVE_MSG", "Akt�v");
	define("INACTIVE_MSG", "Inactive");
	define("EXPIRED_MSG", "Lej�rt");
	define("EMOTICONS_MSG", "Emoticons");
	define("EMOTION_ICONS_MSG", "Emotion Icons");
	define("VIEW_MORE_EMOTICONS_MSG", "View more Emoticons");
	define("SITE_NAME_MSG", "Site Name");
	define("SITE_URL_MSG", "Webhely URL");
	define("SORT_ORDER_MSG", "Sort Order");
	define("NEW_MSG", "New");
	define("USED_MSG", "Used");
	define("REFURBISHED_MSG", "Refurbished");
	define("ADD_NEW_MSG", "Add New");
	define("SETTINGS_MSG", "Settings");
	define("VIEW_MSG", "N�zett");
	define("STATUS_MSG", "St�tusz");
	define("NONE_MSG", "None");
	define("PRICE_MSG", "�r");
	define("TEXT_MSG", "Text");
	define("WARNING_MSG", "Warning");
	define("HIDDEN_MSG", "Rejtett");
	define("CODE_MSG", "K�d");
	define("LANGUAGE_MSG", "Nyelv");
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
	define("RELEASES_TITLE", "Megjelentet�sek");
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

	//email & SMS �rtes�t�s
	define("EMAIL_NOTIFICATION_MSG", " E-mail �rtes�t�s");
	define("EMAIL_NOTIFICATION_ADMIN_MSG", "�gyint�z� E-mail �rtes�t�s");
	define("EMAIL_NOTIFICATION_USER_MSG", "Felhaszn�l� E-mail �rtes�t�s");
	define("EMAIL_SEND_ADMIN_MSF", "�rtes�t�s k�ld�s �gyint�z�nek ");
	define("EMAIL_SEND_USER_MSG", "�rtes�t�s k�ld�s felhaszn�l�nak");
	define("EMAIL_USER_IF_STATUS_MSG", "�rtes�t�s k�ld�s felhaszn�l�nak amikor a st�tusza elfogadott");
	define("EMAIL_TO_MSG", "C�mzett");
	define("EMAIL_TO_USER_DESC", "�gyf�l email c�m�t haszn�lja , ha �res");
	define("EMAIL_FROM_MSG", "Inn�t");
	define("EMAIL_CC_MSG", "M�solat");
	define("EMAIL_BCC_MSG", "Bcc");
	define("EMAIL_REPLY_TO_MSG", "V�lasz");
	define("EMAIL_RETURN_PATH_MSG", "Visszak�ld�s");
	define("EMAIL_SUBJECT_MSG", "T�rgy");
	define("EMAIL_MESSAGE_TYPE_MSG", "�zenet t�pus");
	define("EMAIL_MESSAGE_MSG", "�zenet");
	define("SMS_NOTIFICATION_MSG", "SMS �rtes�t�s");
	define("SMS_NOTIFICATION_ADMIN_MSG", "�gyint�z� SMS �rtes�t�s");
	define("SMS_NOTIFICATION_USER_MSG", "Felhaszn�l� SMS �rtes�t�s ");
	define("SMS_SEND_ADMIN_MSF", "SMS �rtes�t�s k�ld�s �gyint�z�nek");
	define("SMS_SEND_USER_MSG", "SMS �rtes�t�s k�ld�s Felhaszn�l�nak ");
	define("SMS_USER_IF_STATUS_MSG", "SMS �rtes�t�s k�ld�s felhaszn�l�nak amikor a st�tusa elfogadott.");
	define("SMS_RECIPIENT_MSG", "SMS -t fogad�");
	define("SMS_RECIPIENT_ADMIN_DESC", "�gyint�z� mobiltelefon sz�ma");
	define("SMS_RECIPIENT_USER_DESC", "Mobiltelefon'  mez� haszn�lt , ha �res");
	define("SMS_ORIGINATOR_MSG", "SMS l�trehoz�");
	define("SMS_MESSAGE_MSG", "SMS �zenet");

	//sz�mla �zenetek
	define("LOGIN_AS_MSG", "Bejelentkez�s mint");
	define("LOGIN_INFO_MSG", "Bejelentkez�si inform�ci�");
	define("ACCESS_HOME_MSG", "El�rni a kezd�lapod");
	define("REMEMBER_LOGIN_MSG", "Eml�kszik az azonos�t�ra �s a jelsz�ra");
	define("ENTER_LOGIN_MSG", "�rjad be az azonos�t�d �s jelszavad a folytat�shoz");
	define("LOGIN_PASSWORD_ERROR", "Jelsz� vagy azonos�t� helytelen");
	define("ACCOUNT_APPROVE_ERROR", "Sajn�lom, a regisztr�ci�d m�g nincs elfogadva.");
	define("ACCOUNT_EXPIRED_MSG", "Your account has expired.");
	define("NEW_PROFILE_ERROR", "Nem rendelkezel enged�lyekkel hogy nyiss�l sz�ml�t.");
	define("EDIT_PROFILE_ERROR", "Nem rendelkezel enged�lyekkel, hogy szerkesztd ezt a profilt.");
	define("CHANGE_DETAILS_MSG", "Adataid v�ltoztat�sa");
	define("CHANGE_DETAILS_DESC", "Kattints a linkre ha szeretn�d megv�ltoztatni a regisztr�ci�kn�l megadott inform�ci�kat.");
	define("CHANGE_PASSWORD_MSG", "jelsz� v�ltoztat�s ");
	define("CHANGE_PASSWORD_DESC", "A link arra az oldalara mutat , ahol megv�ltoztathatod a jelszavad. ");
	define("SIGN_UP_MSG", "Regisztr�lj most!");
	define("MY_ACCOUNT_MSG", "Saj�t fi�kom");
	define("NEW_USER_MSG", "�j Felhaszn�l�");
	define("EXISTS_USER_MSG", "L�tez� felhaszn�l�k");
	define("EDIT_PROFILE_MSG", "Profil szerkeszt�s");
	define("PERSONAL_DETAILS_MSG", "Szem�lyes r�szletek");
	define("DELIVERY_DETAILS_MSG", "Sz�ll�t�si r�szletek");
	define("SAME_DETAILS_MSG", "Ha a sz�ll�t�si c�m megegyezik a sz�ml�z�si c�mmel,akkor kattints ide<br>ha nem,akkor ki kell t�lteni az �res mez�ket");
	define("DELIVERY_MSG", "k�zbes�t�s");
	define("SUBSCRIBE_CHECKBOX_MSG", "Kattints ide, ha szeretn�d megkapni h�rlevel�nket.");
	define("ADDITIONAL_DETAILS_MSG", "Tov�bbi R�szletek");
	define("GUEST_MSG", "Guest");

	//apr�hirdet�sek �zenetei
	define("MY_ADS_MSG", "Hirdet�seim");
	define("MY_ADS_DESC", "Ha rendelkezel valamivel amit szeretn�l eladni, rakj fel egy hirdet�st itt. Gyorsan �s k�nnyen lehet apr�hirdet�st elhelyezni.");
	define("AD_GENERAL_MSG", "�ltal�nos apr�hirdet�s inform�ci�");
	define("ALL_ADS_MSG", "All Ads");
	define("AD_SELLER_MSG", "Elad�");
	define("AD_START_MSG", "Kezdete");
	define("AD_RUNS_MSG", "Napok sz�ma");
	define("AD_QTY_MSG", "Mennyis�g");
	define("AD_AVAILABILITY_MSG", "El�rhet�s�g");
	define("AD_COMPARED_MSG", "Lehet�v� tenni az apr�hirdet�sek �sszehasonl�t�s�t");
	define("AD_UPLOAD_MSG", "k�p felt�lt�s");
	define("AD_DESCRIPTION_MSG", "Le�r�s");
	define("AD_SHORT_DESC_MSG", "R�vid le�r�s");
	define("AD_FULL_DESC_MSG", "Teljes le�r�s");
	define("AD_LOCATION_MSG", "Elhelyezked�s");
	define("AD_LOCATION_INFO_MSG", "Extra inform�ci�");
	define("AD_PROPERTIES_MSG", "Apr�hirdet�s Tulajdons�gok");
	define("AD_SPECIFICATION_MSG", "Apr�hirdet�s R�szletez�s");
	define("AD_MORE_IMAGES_MSG", "T�bb k�p");
	define("AD_IMAGE_DESC_MSG", "K�p le�r�s");
	define("AD_DELETE_CONFIRM_MSG", "T�r�lni akarod ezt az apr�hirdet�st?");
	define("AD_NOT_APPROVED_MSG", "Nem j�v�hagyott");
	define("AD_RUNNING_MSG", "Fut�");
	define("AD_CLOSED_MSG", "Befejezett");
	define("AD_NOT_STARTED_MSG", "Nincs ind�tva");
	define("AD_NEW_ERROR", "Nem rendelkezel enged�lyekkel �j apr�hirdet�st l�trehozni.");
	define("AD_EDIT_ERROR", "Nem rendelkezel enged�lyekkel szerkeszteni azt az apr�hirdet�st.");
	define("AD_DELETE_ERROR", "Nem rendelkezel enged�lyekkel t�r�lni ezt az apr�hirdet�st.");
	define("NO_ADS_MSG", "Nincs apr�hirdet�s");
	define("NO_AD_MSG", "Nincs apr�hirdet�s ezzel az azonos�t�val ebben a kateg�ri�ban.");
	define("FOUND_ADS_MSG", "Tal�ltunk <b>{found_records}</b>apr�hirdet�st , ami megfelel a'<b>{search_string}</b>' keres�felt�telnek.");
	define("AD_OFFER_MESSAGE_MSG", "Aj�nlat");
	define("AD_OFFER_LOGIN_ERROR", "A folytat�s el�tt be kell jelentkezni.");
	define("AD_REQUEST_BUTTON", "�rdekl�d�s k�ld�se");
	define("AD_SENT_MSG", "Az aj�nlatodat sikeresen elk�ldt�k.");
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

	//keres�s �zenetek
	define("SEARCH_FOR_MSG", "Keres�s");
	define("SEARCH_IN_MSG", "Keres�s itt");
	define("SEARCH_TITLE_MSG", "C�m");
	define("SEARCH_CODE_MSG", "K�d");
	define("SEARCH_SHORT_DESC_MSG", "R�vid le�r�s");
	define("SEARCH_FULL_DESC_MSG", "R�szletes le�r�s");
	define("SEARCH_CATEGORY_MSG", "Keres�s kateg�ri�ban");
	define("SEARCH_MANUFACTURER_MSG", "Gy�rt�");
	define("SEARCH_SELLER_MSG", "Elad�");
	define("SEARCH_PRICE_MSG", "�r hat�r");
	define("SEARCH_WEIGHT_MSG", "S�ly korl�t");
	define("SEARCH_RESULTS_MSG", "Keres�si eredm�nyek");
	define("FULL_SITE_SEARCH_MSG", "Full Site Search");

	//�sszehasonl�t� �zenetek
	define("COMPARE_MSG", " �sszehasonl�t�s");
	define("COMPARE_REMOVE_MSG", "Elt�vol�t�s");
	define("COMPARE_REMOVE_HELP_MSG", "Kattint�s ide a term�k elt�vol�t�s�hoz az �sszehasonl�t�sb�l.");
	define("COMPARE_MIN_ALLOWED_MSG", "Legal�bb 2 term�ket ki kell v�lasztani");
	define("COMPARE_MAX_ALLOWED_MSG", "Nem lehet 5 term�kn�l t�bbet kiv�lasztani");
	define("COMPARE_PARAM_ERROR_MSG", "�sszehasonl�t�s param�tere  rossz �rt�ket tartalmaz");

	//Mond el egy bar�todnak �zenetek
	define("TELL_FRIEND_TITLE", "//Mond el egy bar�todnak");
	define("TELL_FRIEND_SUBJECT_MSG", "A bar�tod k�ldte ezt a linket.");
	define("TELL_FRIEND_DEFAULT_MSG", "Szia {friend_name} -  Gondoltam t�ged �rdekelhet ez dolog  {item_title} ezen a weboldalon {item_url}");
	define("TELL_YOUR_NAME_FIELD", "A neved");
	define("TELL_YOUR_EMAIL_FIELD", "Az email c�med");
	define("TELL_FRIENDS_NAME_FIELD", "Bar�tod neve");
	define("TELL_FRIENDS_EMAIL_FIELD", "Bar�tod email c�me");
	define("TELL_COMMENT_FIELD", "Megjegyz�s");
	define("TELL_FRIEND_PRIVACY_NOTE_MSG", "MAG�N�LET FIGYELMEZTET�S: Nem fogjuk menteni �s t�rolni a te �s a bar�tod email c�m�t semmilyen m�s felhaszn�l�s c�lj�ra.");
	define("TELL_SENT_MSG", "Az �zenetedet sikeresen elk�ldt�k!<br> K�sz�n�m!");
	define("TELL_FRIEND_MESSAGE_MSG", "Thought you might be interested in seeing the {item_title} at {item_url}\\n\\n{user_name} left you a note:\\n{user_comment}");
	define("TELL_FRIEND_PARAM_MSG", "Introduce a friend' URL");
	define("TELL_FRIEND_PARAM_DESC", "adds a friend's URL parameter to a 'Tell a Friend' link if it exists for a user");
	define("FRIEND_COOKIE_EXPIRES_MSG", "Friend Cookie Expires");

	define("CONTACT_US_TITLE", "Contact Us");
	define("CONTACT_USER_NAME_FIELD", "Neved");
	define("CONTACT_USER_EMAIL_FIELD", "Emailed C�med");
	define("CONTACT_SUMMARY_FIELD", "Egysoros �sszefoglal�");
	define("CONTACT_DESCRIPTION_FIELD", "Le�r�s");
	define("CONTACT_REQUEST_SENT_MSG", "Your request was successfully sent.");

	//gombok
	define("GO_BUTTON", "Mehet");
	define("CONTINUE_BUTTON", "Folytat�s");
	define("BACK_BUTTON", "Vissza");
	define("NEXT_BUTTON", "K�vetkez�");
	define("PREV_BUTTON", "El�z�");
	define("SIGN_IN_BUTTON", "Beregisztr�l�s");
	define("LOGIN_BUTTON", "Bejelentkez�s");
	define("LOGOUT_BUTTON", "Kijelentkez�s");
	define("SEARCH_BUTTON", "Keres�s");
	define("RATE_IT_BUTTON", "�rt�keld!");
	define("ADD_BUTTON", "Hozz�ad�s");
	define("UPDATE_BUTTON", "Friss�t�s");
	define("APPLY_BUTTON", "Alkalmaz");
	define("REGISTER_BUTTON", "Regisztr�l�s");
	define("VOTE_BUTTON", "Szavaz�s");
	define("CANCEL_BUTTON", "M�gse");
	define("CLEAR_BUTTON", "�r�t");
	define("RESET_BUTTON", "Reset");
	define("DELETE_BUTTON", "T�r�l");
	define("DELETE_ALL_BUTTON", "Delete All");
	define("SUBSCRIBE_BUTTON", "Feliratkoz�s");
	define("UNSUBSCRIBE_BUTTON", "Leiratoz�s");
	define("SUBMIT_BUTTON", "Tov�bb�t");
	define("UPLOAD_BUTTON", "Felt�lt�s");
	define("SEND_BUTTON", "K�ld�s");
	define("PREVIEW_BUTTON", "El�zetes");
	define("FILTER_BUTTON", "Sz�r�s");
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

	//mez�k
	define("LOGIN_FIELD", "Felhaszn�l�n�v");
	define("PASSWORD_FIELD", "Jelsz�");
	define("CONFIRM_PASS_FIELD", "Jelsz� meger�s�t�s");
	define("NEW_PASS_FIELD", "�j Jelsz�");
	define("CURRENT_PASS_FIELD", "Jelenlegi Jelsz�");
	define("FIRST_NAME_FIELD", "Keresztn�v");
	define("LAST_NAME_FIELD", "Csal�dn�v");
	define("NICKNAME_FIELD", "Nick");
	define("PERSONAL_IMAGE_FIELD", "Saj�t k�p");
	define("COMPANY_SELECT_FIELD", "V�llalat");
	define("SELECT_COMPANY_MSG", "V�llalat kiv�laszt�s");
	define("COMPANY_NAME_FIELD", "V�llalat n�v");
	define("EMAIL_FIELD", "Email");
	define("STREET_FIRST_FIELD", "Utcac�m 1");
	define("STREET_SECOND_FIELD", "Utcac�m 2");
	define("CITY_FIELD", "V�ros");
	define("PROVINCE_FIELD", "Megye");
	define("SELECT_STATE_MSG", "Select State");
	define("STATE_FIELD", "�llam");
	define("ZIP_FIELD", "Zip/Ir�ny�t�sz�m");
	define("SELECT_COUNTRY_MSG", "Orsz�g v�laszt�s");
	define("COUNTRY_FIELD", "Orsz�g");
	define("PHONE_FIELD", "Telefon");
	define("DAYTIME_PHONE_FIELD", "Nappali telefon");
	define("EVENING_PHONE_FIELD", "Esti telefon");
	define("CELL_PHONE_FIELD", "Mobiltelefon");
	define("FAX_FIELD", "Fax");
	define("VALIDATION_CODE_FIELD", "�rv�nyes�t� k�d");
	define("AFFILIATE_CODE_FIELD", "Csatlakoz� k�d");
	define("AFFILIATE_CODE_HELP_MSG", "K�rem haszn�lja a k�vetkez� URL {affiliate_url} ,hogy kapcsol�d� linket k�sz�tsen az oldalunkhoz.");
	define("PAYPAL_ACCOUNT_FIELD", "PayPal sz�mla");
	define("TAX_ID_FIELD", "Ad� sz�m");
	define("MSN_ACCOUNT_FIELD", "MSN Account");
	define("ICQ_NUMBER_FIELD", "ICQ Number");
	define("USER_SITE_URL_FIELD", "User's Site URL");
	define("HIDDEN_STATUS_FIELD", "Hidden Status");
	define("HIDE_MY_ONLINE_STATUS_MSG", "Do not show my online status");
	define("SUMMARY_MSG", "�sszefoglal�");

	//nincs rekord �zenetek
	define("NO_RECORDS_MSG", "Nem tal�ltunk rekordot");
	define("NO_EVENTS_MSG", "Nem tal�ltunk esem�nyt");
	define("NO_QUESTIONS_MSG", "Nem tal�ltunk k�rd�st");
	define("NO_NEWS_MSG", "Nem tal�ltunk h�reket");
	define("NO_POLLS_MSG", "Nem tal�ltunk szavaz�st");

	//SMS �zenetek
	define("SMS_TITLE", "SMS");
	define("SMS_TEST_TITLE", "SMS Pr�ba");
	define("SMS_TEST_DESC", "K�rem be�rni a mobiltelefonod sz�m�t �s nyomd meg a 'SEND_BUTTON'gombot ,hogy kapj egy teszt �zenetet");
	define("INVALID_CELL_PHONE", "Helytelen mobiltelefon sz�m");

	define("ARTICLE_RELATED_PRODUCTS_TITLE", "Article Related Products");
	define("CATEGORY_RELATED_PRODUCTS_TITLE", "Category Related Products");
	define("SELECT_TYPE_MSG", "Kiv�laszt�s t�pus");
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
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


	define("CHARSET", "windows-1252");
	// date messages
	define("YEAR_MSG", "�r");
	define("YEARS_QTY_MSG", "{quantity} �r");
	define("MONTH_MSG", "M�nad");
	define("MONTHS_QTY_MSG", "{quantity} m�nader");
	define("DAY_MSG", "Dag");
	define("DAYS_MSG", "Dagar");
	define("DAYS_QTY_MSG", "{quantity} dagar");
	define("HOUR_MSG", "Timme");
	define("HOURS_QTY_MSG", "{quantity} timmar");
	define("MINUTE_MSG", "Minut");
	define("MINUTES_QTY_MSG", "{quantity} minuter");
	define("SECOND_MSG", "Sekund");
	define("SECONDS_QTY_MSG", "{quantity} seconds");
	define("WEEK_MSG", "Vecka");
	define("WEEKS_QTY_MSG", "{quantity} veckor");
	define("TODAY_MSG", "Idag");
	define("YESTERDAY_MSG", "Ig�r");
	define("LAST_7DAYS_MSG", "Senaste 7 dagarna");
	define("THIS_MONTH_MSG", "Den h�r m�naden");
	define("LAST_MONTH_MSG", "F�rra m�naden");
	define("THIS_QUARTER_MSG", "Det h�r kvartalet");
	define("THIS_YEAR_MSG", "Det h�r �ret");

	// months
	define("JANUARY", "Januari");
	define("FEBRUARY", "Februari");
	define("MARCH", "March");
	define("APRIL", "April");
	define("MAY", "Maj");
	define("JUNE", "Juni");
	define("JULY", "Juli");
	define("AUGUST", "Augusti");
	define("SEPTEMBER", "September");
	define("OCTOBER", "Oktober");
	define("NOVEMBER", "November");
	define("DECEMBER", "December");

	define("JANUARY_SHORT", "Jan");
	define("FEBRUARY_SHORT", "Feb");
	define("MARCH_SHORT", "Mar");
	define("APRIL_SHORT", "Apr");
	define("MAY_SHORT", "Maj");
	define("JUNE_SHORT", "Jun");
	define("JULY_SHORT", "Jul");
	define("AUGUST_SHORT", "Aug");
	define("SEPTEMBER_SHORT", "Sep");
	define("OCTOBER_SHORT", "Okt");
	define("NOVEMBER_SHORT", "Nov");
	define("DECEMBER_SHORT", "Dec");

	// weekdays
	define("SUNDAY", "S�ndag");
	define("MONDAY", "M�ndag");
	define("TUESDAY", "Tisdag");
	define("WEDNESDAY", "Onsdag");
	define("THURSDAY", "Torsdag");
	define("FRIDAY", "Fredag");
	define("SATURDAY", "L�rdag");

	define("SUNDAY_SHORT", "S�n");
	define("MONDAY_SHORT", "M�n");
	define("TUESDAY_SHORT", "Tis");
	define("WEDNESDAY_SHORT", "Ons");
	define("THURSDAY_SHORT", "Tors");
	define("FRIDAY_SHORT", "Fre");
	define("SATURDAY_SHORT", "L�r");

	// validation messages
	define("REQUIRED_MESSAGE", "<b>{field_name}</b> �r obligatorisk");
	define("UNIQUE_MESSAGE", "V�rdet i f�ltet <b>{field_name}</b> finns redan i databasen");
	define("VALIDATION_MESSAGE", "Verifiering misslyckades med f�ltet {field_name}");
	define("MATCHED_MESSAGE", "<b>{field_one}</b> och <b>{field_two}</b> matchade inte");
	define("INSERT_ALLOWED_ERROR", "Tyv�rr, men du har inte till�telse att g�ra inl�gg");
	define("UPDATE_ALLOWED_ERROR", "Tyv�rr, men du har inte till�telse att uppdatera");
	define("DELETE_ALLOWED_ERROR", "Tyv�rr, men du har inte till�telse att radera");
	define("ALPHANUMERIC_ALLOWED_ERROR", "Bara alfa-numeriska tecken, bindestreck och understreck �r till�tna i f�ltet <b>{field_name}</b>");

	define("INCORRECT_DATE_MESSAGE", "<b>{field_name}</b> har inkorrekt datum. Anv�nd kalenderdatum");
	define("INCORRECT_MASK_MESSAGE", "<b>{field_name}</b> motsvarade inte 'mask'. Anv�nd f�ljande '<b>{field_mask}</b>'");
	define("INCORRECT_EMAIL_MESSAGE", "Felaktigt epostformat i f�ltet {field_name}");
	define("INCORRECT_VALUE_MESSAGE", "Felaktigt v�rde i f�ltet <b>{field_name}</b>");

	define("MIN_VALUE_MESSAGE", "V�rdet i f�ltet <b>{field_name}</b> kan inte vara mindre �n {min_value}");
	define("MAX_VALUE_MESSAGE", "V�rdet i f�ltet <b>{field_name}</b> kan inte vara st�rre �n {max_value}");
	define("MIN_LENGTH_MESSAGE", "Antalet tecken i f�ltet <b>{field_name}</b> m�ste vara minst {min_length}");
	define("MAX_LENGTH_MESSAGE", "Antalet tecken i f�ltet <b>{field_name}</b> f�r vara h�gst {max_length}");

	define("FILE_PERMISSION_MESSAGE", "Du har inte skrivr�ttigheter till filen <b>'{file_name}'</b>. Var v�nlig �ndra skrivr�ttigheterna innan du forts�tter");
	define("FOLDER_PERMISSION_MESSAGE", "Du har inte skrivr�ttigheter till mappen <b>'{folder_name}'</b>. Var v�nlig �ndra skrivr�ttigheterna innan du forts�tter");
	define("INVALID_EMAIL_MSG", "Din epostadress �r felaktig.");
	define("DATABASE_ERROR_MSG", "Databasfel har uppt�ckts.");
	define("BLACK_IP_MSG", "Den h�r handlingen �r inte till�ten hos din v�rd");
	define("BANNED_CONTENT_MSG", "Tyv�rr, det tillhandah�llna inneh�llet inneh�ller olagliga p�st�enden.");
	define("ERRORS_MSG", "Fel");
	define("REGISTERED_ACCESS_MSG", "Bara registrerade anv�ndare har tillg�ng till den h�r valm�jligheten.");
	define("SELECT_FROM_LIST_MSG", "V�lj fr�n listan");

	// titles 
	define("TOP_RATED_TITLE", "H�gst v�rderad");
	define("TOP_VIEWED_TITLE", "Mest visad");
	define("RECENTLY_VIEWED_TITLE", "Senast visad");
	define("HOT_TITLE", "Popul�r");
	define("LATEST_TITLE", "Senaste");
	define("CONTENT_TITLE", "Inneh�ll");
	define("RELATED_TITLE", "Relaterad");
	define("SEARCH_TITLE", "S�k");
	define("ADVANCED_SEARCH_TITLE", "Avancerad s�k");
	define("LOGIN_TITLE", "Anv�ndarinloggning");
	define("CATEGORIES_TITLE", "Kategorier");
	define("MANUFACTURERS_TITLE", "Tillverkare");
	define("SPECIAL_OFFER_TITLE", "Specialerbjudanden");
	define("NEWS_TITLE", "Nyheter");
	define("EVENTS_TITLE", "H�ndelser");
	define("PROFILE_TITLE", "Profil");
	define("USER_HOME_TITLE", "Startsida");
	define("DOWNLOAD_TITLE", "Nedladdning");
	define("FAQ_TITLE", "FAQ - Vanliga fr�gor");
	define("POLL_TITLE", "Omr�stning");
	define("HOME_PAGE_TITLE", "Startsida");
	define("CURRENCY_TITLE", "Valuta");
	define("SUBSCRIBE_TITLE", "Prenumerera");
	define("UNSUBSCRIBE_TITLE", "Avsluta prenumeration");
	define("UPLOAD_TITLE", "Ladda upp");
	define("ADS_TITLE", "Annonser");
	define("ADS_COMPARE_TITLE", "Annonsj�mf�relse");
	define("ADS_SELLERS_TITLE", "S�ljare");
	define("AD_REQUEST_TITLE", "Ge ett bud / St�ll en fr�ga till s�ljaren");
	define("LANGUAGE_TITLE", "Spr�k");
	define("MERCHANTS_TITLE", "Grossister");
	define("PREVIEW_TITLE", "F�rhandsvisa");
	define("ARTICLES_TITLE", "Artiklar");
	define("SITE_MAP_TITLE", "Sidkarta");
	define("LAYOUTS_TITLE", "Layouter");

	// menu items
	define("MENU_ABOUT", "Om oss");
	define("MENU_ACCOUNT", "Ditt konto");
	define("MENU_BASKET", "Varukorgen");
	define("MENU_CONTACT", "Kontakt");
	define("MENU_DOCUMENTATION", "Dokumentation");
	define("MENU_DOWNLOADS", "Nedladdningar");
	define("MENU_EVENTS", "H�ndelser");
	define("MENU_FAQ", "Vanliga fr�gor");
	define("MENU_FORUM", "Forum");
	define("MENU_HELP", "Hj�lp");
	define("MENU_HOME", "Startsidan");
	define("MENU_HOW", "Hur man handlar");
	define("MENU_MEMBERS", "Medlemmar");
	define("MENU_MYPROFILE", "Min profil");
	define("MENU_NEWS", "Nyheter");
	define("MENU_PRIVACY", "R�ttigheter");
	define("MENU_PRODUCTS", "Produkter");
	define("MENU_REGISTRATION", "Registrering");
	define("MENU_SHIPPING", "Frakt");
	define("MENU_SIGNIN", "Logga in");
	define("MENU_SIGNOUT", "Logga ut");
	define("MENU_SUPPORT", "Support");
	define("MENU_USERHOME", "Anv�ndarsidan");
	define("MENU_ADS", "Annonser");
	define("MENU_ADMIN", "Administration");
	define("MENU_KNOWLEDGE", "Kunskapsbas");

	// main terms
	define("NO_MSG", "Nej");
	define("YES_MSG", "Ja");
	define("NOT_AVAILABLE_MSG", "Inte tillg�nglig");
	define("MORE_MSG", "Mer..");
	define("READ_MORE_MSG", "L�s mer..");
	define("CLICK_HERE_MSG", "Klicka h�r");
	define("ENTER_YOUR_MSG", "Skriv ditt");
	define("CHOOSE_A_MSG", "V�lj ett");
	define("PLEASE_CHOOSE_MSG", "V�nligen v�lj");
	define("SELECT_MSG", "V�lj");
	define("DATE_FORMAT_MSG", "Anv�nd formatet <b>{date_format}</b>");
	define("NEXT_PAGE_MSG", "N�sta");
	define("PREV_PAGE_MSG", "F�reg�ende");
	define("FIRST_PAGE_MSG", "F�rsta");
	define("LAST_PAGE_MSG", "Sista");
	define("OF_PAGE_MSG", "av");
	define("TOP_CATEGORY_MSG", "Topp");
	define("SEARCH_IN_CURRENT_MSG", "Nuvarande kategori");
	define("SEARCH_IN_ALL_MSG", "Alla kategorier");
	define("FOUND_IN_MSG", "Hittad i");
	define("TOTAL_VIEWS_MSG", "Visad");
	define("VOTES_MSG", "R�ster");
	define("TOTAL_VOTES_MSG", "Totalt antal r�ster");
	define("TOTAL_POINTS_MSG", "Totalt antal po�ng");
	define("VIEW_RESULTS_MSG", "Visa resultat");
	define("PREVIOUS_POLLS_MSG", "Tidigare omr�stningar");
	define("TOTAL_MSG", "Totalt");
	define("CLOSED_MSG", "St�ngd");
	define("CLOSE_WINDOW_MSG", "St�ng f�nster");
	define("ASTERISK_MSG", "Asterisk (*) - obligatoriskt f�lt");
	define("PROVIDE_INFO_MSG", "Var v�nlig fyll i de markerade f�lten, klicka sedan p� '{button_name}'");
	define("FOUND_ARTICLES_MSG", "Vi har hittat <b>{found_records}</b> artiklar som matchar s�kordet '<b>{search_string}</b>'");
	define("NO_ARTICLE_MSG", "N�gon artikel med detta ID fanns inte");
	define("NO_ARTICLES_MSG", "Inga artiklar hittades");
	define("NOTES_MSG", "Noteringar");
	define("KEYWORDS_MSG", "S�kord");
	define("LINK_URL_MSG", "L�nk");
	define("DOWNLOAD_URL_MSG", "Ladda ner");
	define("SUBSCRIBE_FORM_MSG", "F�r att f� v�rt nyhetsbrev, var v�nlig skriv in din epostadress i rutan nedan. Klicka sedan p� '{button_name}'-knappen.");
	define("UNSUBSCRIBE_FORM_MSG", "Var v�nlig skriv in din epostadress i rutan nedan. Klicka sedan p� '{button_name}'-knappen.");
	define("SUBSCRIBE_LINK_MSG", "Prenumerera");
	define("UNSUBSCRIBE_LINK_MSG", "Avsluta prenumeration");
	define("SUBSCRIBED_MSG", "V�lkommen! Du kommer i forts�ttningen f� v�rt nyhetsbrev.");
	define("ALREADY_SUBSCRIBED_MSG", "Du �r redan prenumerant. Tack.");
	define("UNSUBSCRIBED_MSG", "Du har nu avslutat din prenumaration f�r nyhetsbrevet. V�lkommen �ter.");
	define("UNSUBSCRIBED_ERROR_MSG", "Tyv�rr kan vi inte hitta din epostadress i v�rt system. Du har troligtvis redan avslutat din prenumeration.");
	define("FORGOT_PASSWORD_MSG", "Gl�mt l�senordet?");
	define("FORGOT_PASSWORD_DESC", "Var v�nlig skriv in din epostadress som du anv�nt i samband med registreringen.");
	define("FORGOT_EMAIL_ERROR_MSG", "Ledsen men vi kan inte hitta din epostadress i systemet.");
	define("FORGOT_EMAIL_SENT_MSG", "Dina uppgifter har skickats till din epostadress.");
	define("RESET_PASSWORD_REQUIRE_MSG", "N�gra obligatoriska parametrar saknas");
	define("RESET_PASSWORD_PARAMS_MSG", "De angivna parametrarna motsvarar inte dem i databasen.");
	define("RESET_PASSWORD_EXPIRY_MSG", "�terst�llningskoden som du har angett har f�rfallit. V�nligen beg�r en ny �terst�llningskod f�r ditt l�senord.");
	define("RESET_PASSWORD_SAVED_MSG", "Ditt nya l�senord �r nu sparat med framg�ng.");
	define("PRINTER_FRIENDLY_MSG", "Utskriftsv�nlig");
	define("PRINT_PAGE_MSG", "Skriv ut sidan");
	define("ATTACHMENTS_MSG", "Bilagor");
	define("VIEW_DETAILS_MSG", "Visa detaljer");
	define("HTML_MSG", "HTML");
	define("PLAIN_TEXT_MSG", "Ren text");
	define("META_DATA_MSG", "Metadata");
	define("META_TITLE_MSG", "Sidtitel");
	define("META_KEYWORDS_MSG", "Meta-s�kord");
	define("META_DESCRIPTION_MSG", "Meta-beskrivning");
	define("FRIENDLY_URL_MSG", "Enkel URL");
	define("IMAGES_MSG", "Bilder");
	define("IMAGE_MSG", "Bild");
	define("IMAGE_TINY_MSG", "Minibild");
	define("IMAGE_TINY_ALT_MSG", "Minibildsbeskrivning");
	define("IMAGE_SMALL_MSG", "Liten bild");
	define("IMAGE_SMALL_DESC", "Visad p� inneh�llssidan");
	define("IMAGE_SMALL_ALT_MSG", "Liten bilds beskrivning");
	define("IMAGE_LARGE_MSG", "Stor bild");
	define("IMAGE_LARGE_DESC", "Visad p� detaljsidan");
	define("IMAGE_LARGE_ALT_MSG", "Stor bilds beskrivning");
	define("IMAGE_SUPER_MSG", "Extra stor bild");
	define("IMAGE_SUPER_DESC", "Bilden �ppnas i nytt f�nster");
	define("IMAGE_POSITION_MSG", "Bildplacering");
	define("UPLOAD_IMAGE_MSG", "Ladda upp bild");
	define("UPLOAD_FILE_MSG", "Ladda upp fil");
	define("SELECT_IMAGE_MSG", "V�lj bild");
	define("SELECT_FILE_MSG", "V�lj fil");
	define("SHOW_BELOW_PRODUCT_IMAGE_MSG", "Visa bilden under den stora produktbilden");
	define("SHOW_IN_SEPARATE_SECTION_MSG", "Visa bilden i en separat bildsektion");
	define("IS_APPROVED_MSG", "Godk�nd");
	define("NOT_APPROVED_MSG", "Inte godk�nd");
	define("IS_ACTIVE_MSG", "�r aktiv");
	define("CATEGORY_MSG", "Kategori");
	define("SELECT_CATEGORY_MSG", "V�lj kategori");
	define("DESCRIPTION_MSG", "Beskrivning");
	define("SHORT_DESCRIPTION_MSG", "Kort beskrivning");
	define("FULL_DESCRIPTION_MSG", "L�ng beskrivning");
	define("HIGHLIGHTS_MSG", "H�jdpunkter");
	define("SPECIAL_OFFER_MSG", "Specialerbjudanden");
	define("ARTICLE_MSG", "Artikel");
	define("OTHER_MSG", "�vrigt");
	define("WIDTH_MSG", "Bredd");
	define("HEIGHT_MSG", "H�jd");
	define("LENGTH_MSG", "L�ngd");
	define("WEIGHT_MSG", "Vikt");
	define("QUANTITY_MSG", "Antal");
	define("CALENDAR_MSG", "Kalender");
	define("FROM_DATE_MSG", "Fr�n och med");
	define("TO_DATE_MSG", "Till och med");
	define("TIME_PERIOD_MSG", "Tidsperiod");
	define("GROUP_BY_MSG", "Gruppera efter");
	define("BIRTHDAY_MSG", "F�delsedag");
	define("BIRTH_DATE_MSG", "F�delsedatum");
	define("BIRTH_YEAR_MSG", "F�delse�r");
	define("BIRTH_MONTH_MSG", "F�delsem�nad");
	define("BIRTH_DAY_MSG", "F�delsedag");
	define("STEP_NUMBER_MSG", "Steg {current_step} av {total_steps}");
	define("WHERE_STATUS_IS_MSG", "D�r statusen �r");
	define("ID_MSG", "ID");
	define("QTY_MSG", "Antal");
	define("TYPE_MSG", "Sort");
	define("NAME_MSG", "Namn");
	define("TITLE_MSG", "Titel");
	define("DEFAULT_MSG", "Standard");
	define("OPTIONS_MSG", "Valm�jligheter");
	define("EDIT_MSG", "Redigera");
	define("CONFIRM_DELETE_MSG", "Vill du radera {record_name}?");
	define("DESC_MSG", "Beskrivning");
	define("ASC_MSG", "Asc");
	define("ACTIVE_MSG", "Aktiv");
	define("INACTIVE_MSG", "Inte aktiv");
	define("EXPIRED_MSG", "F�rfallen");
	define("EMOTICONS_MSG", "K�nsloikoner");
	define("EMOTION_ICONS_MSG", "K�nslom�ssiga ikoner");
	define("VIEW_MORE_EMOTICONS_MSG", "Visa fler k�nsloikoner");
	define("SITE_NAME_MSG", "Webbplatsens namn");
	define("SITE_URL_MSG", "Webbplatsens URL");
	define("SORT_ORDER_MSG", "Sorteringsordning");
	define("NEW_MSG", "Ny");
	define("USED_MSG", "Anv�nd");
	define("REFURBISHED_MSG", "Renoverad");
	define("ADD_NEW_MSG", "L�gg till ny");
	define("SETTINGS_MSG", "Inst�llningar");
	define("VIEW_MSG", "Visa");
	define("STATUS_MSG", "Status");
	define("NONE_MSG", "Ingen");
	define("PRICE_MSG", "Pris");
	define("TEXT_MSG", "Text");
	define("WARNING_MSG", "Varning");
	define("HIDDEN_MSG", "G�md");
	define("CODE_MSG", "Kod");
	define("LANGUAGE_MSG", "Spr�k");
	define("DEFAULT_VIEW_TYPE_MSG", "Standardvisning");
	define("CLICK_TO_OPEN_SECTION_MSG", "Klicka f�r att �ppna sektionen");
	define("CURRENCY_WRONG_VALUE_MSG", "Valutakoden har fel v�rde.");
	define("TRANSACTION_AMOUNT_DOESNT_MATCH_MSG", "<b>Transaktionsv�rdet</b> och <b>Orderv�rdet</b> �r inte samma.");
	define("STATUS_CANT_BE_UPDATED_MSG", "Statusen f�r order  #{order_id} kan inte uppdateras");
	define("CANT_FIND_STATUS_MSG", "Kan inte hitta statusen med ID: {status_id}");
	define("NOTIFICATION_SENT_MSG", "Meddelande skickat");
	define("AUTO_SUBMITTED_PAYMENT_MSG", "Automatisk betalning");
	define("FONT_METRIC_FILE_ERROR", "Kunde inte inkludera fontinneh�llsfilen");
	define("PER_LINE_MSG", "Per linje");
	define("PER_LETTER_MSG", "Per tecken");
	define("PER_NON_SPACE_LETTER_MSG", "Per tecken undantaget mellanslag");
	define("LETTERS_ALLOWED_MSG", "Till�tna tecken");
	define("LETTERS_ALLOWED_PER_LINEMSG", "Till�tna tecken per linje");
	define("RENAME_MSG", "D�p om");
	define("IMAGE_FORMAT_ERROR_MSG", "Bildformatet �r inte till�tet i GD-biblioteket");
	define("GD_LIBRARY_ERROR_MSG", "GD-biblioteket �r inte tillg�ngligt");
	define("INVALID_CODE_MSG", "Felaktig kod:");
	define("INVALID_CODE_TYPE_MSG", "Felaktig kodsort");
	define("INVALID_FILE_EXTENSION_MSG", "Felaktig ");
	define("FOLDER_WRITE_PERMISSION_MSG", "Mappen finns inte eller s� har du inte r�ttigheterna");
	define("UNDEFINED_RECORD_PARAMETER_MSG", "Odefinierad registerparameter: <b>{parameter_name}</b>");
	define("MAX_RECORDS_LIMITATION_MSG", "Du f�r inte l�gga till fler �n <b>{max_records}</b> {records_name} i din version");
	define("ACCESS_DENIED_MSG", "Du har inte till�telse att tillg� denna sektion");
	define("DELETE_RECORDS_BEFORE_PROCEED_MSG", "Var v�nlig radera n�gra {records_name} innan du forts�tter");
	define("PRODUCT_MIN_LIMIT_MSG", "Du kan inte l�gga till mindre �n {limit_quantity} element av {product_name}.");
	define("FOLDER_DOESNT_EXIST_MSG", "Mappen finns inte:");
	define("FILE_DOESNT_EXIST_MSG", "Filen finns inte:");
	define("PARSE_ERROR_IN_BLOCK_MSG", "Tolkningsfel i blocket:");
	define("BLOCK_DOENT_EXIST_MSG", "Blocket finns inte:");
	define("NUMBER_OF_ELEMENTS_MSG", "Antal element");
	define("MISSING_COMPONENT_MSG", "Saknad komponent/parameter:");
	define("RELEASES_TITLE", "Utg�vor");
	define("DETAILED_MSG", "Detaljerad");
	define("LIST_MSG", "Lista");
	define("READONLY_MSG", "L�sbar");
	define("CREDIT_MSG", "Krediter");
	define("ONLINE_MSG", "Inloggad");
	define("OFFLINE_MSG", "Utloggad");
	define("SMALL_CART_MSG", "Liten varukorg");
	define("NEVER_MSG", "Aldrig");
	define("SEARCH_EXACT_WORD_OR_PHRASE", "Exakt ord eller fras");
	define("SEARCH_ONE_OR_MORE", "Minst ett av dessa ord");
	define("SEARCH_ALL", "Alla dessa ord");
	define("RELATED_ARTICLES_MSG", "Relaterade artiklar");
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

	define("FIRST_CONTROLS_ARE_FREE_MSG", "F�rsta {free_price_amount} kontrollerna �r gratis");
	define("FIRST_LETTERS_ARE_FREE_MSG", "F�rsta {free_price_amount} tecken �r gratis");
	define("FIRST_NONSPACE_LETTERS_ARE_FREE_MSG", "F�rsta {free_price_amount} tecken undantaget mellanslag �r gratis");

	// email & SMS messages
	define("EMAIL_NOTIFICATION_MSG", "Epostmeddelande");
	define("EMAIL_NOTIFICATION_ADMIN_MSG", "Administrationsepostmeddelande");
	define("EMAIL_NOTIFICATION_USER_MSG", "Anv�ndarepostmeddelande");
	define("EMAIL_SEND_ADMIN_MSF", "Skicka meddelande till adminstrator");
	define("EMAIL_SEND_USER_MSG", "Skicka meddelande till anv�ndare");
	define("EMAIL_USER_IF_STATUS_MSG", "Skicka meddelande till anv�ndare n�r statusen �r till�mpad");
	define("EMAIL_TO_MSG", "Till");
	define("EMAIL_TO_USER_DESC", "Om tom anv�nds kundens epost");
	define("EMAIL_FROM_MSG", "Avs�ndare");
	define("EMAIL_CC_MSG", "Kopia");
	define("EMAIL_BCC_MSG", "Hemlig kopia");
	define("EMAIL_REPLY_TO_MSG", "Svara till");
	define("EMAIL_RETURN_PATH_MSG", "�terv�ndsgr�nd");
	define("EMAIL_SUBJECT_MSG", "�mne");
	define("EMAIL_MESSAGE_TYPE_MSG", "Meddelandetyp");
	define("EMAIL_MESSAGE_MSG", "Meddelande");
	define("SMS_NOTIFICATION_MSG", "SMS-meddelande");
	define("SMS_NOTIFICATION_ADMIN_MSG", "Administrat�r SMS");
	define("SMS_NOTIFICATION_USER_MSG", "Anv�ndare SMS");
	define("SMS_SEND_ADMIN_MSF", "Skicka ett SMS till administrat�ren");
	define("SMS_SEND_USER_MSG", "Skicka ett SMS till anv�ndaren");
	define("SMS_USER_IF_STATUS_MSG", "Skicka ett SMS till anv�ndaren n�r statusen �r till�mpad");
	define("SMS_RECIPIENT_MSG", "SMS-mottagare");
	define("SMS_RECIPIENT_ADMIN_DESC", "Administratorns mobiltelefonnummer");
	define("SMS_RECIPIENT_USER_DESC", "Om tomt anv�nds mobiltelefonnummer-f�ltet");
	define("SMS_ORIGINATOR_MSG", "SMS avs�ndare");
	define("SMS_MESSAGE_MSG", "SMS meddelande");

	// account messages
	define("LOGIN_AS_MSG", "Du �r inloggad som");
	define("LOGIN_INFO_MSG", "Inloggningsinformation");
	define("ACCESS_HOME_MSG", "F�r att komma �t din sida");
	define("REMEMBER_LOGIN_MSG", "Kom ih�g mitt anv�ndarnamn och l�senord");
	define("ENTER_LOGIN_MSG", "Skriv in anv�ndarnamn och l�senord f�r att forts�tta");
	define("LOGIN_PASSWORD_ERROR", "Anv�ndarnamn eller l�senord �r fel");
	define("ACCOUNT_APPROVE_ERROR", "Tyv�rr, ditt konto har inte godk�nts �n.");
	define("ACCOUNT_EXPIRED_MSG", "Ditt konto har f�rfallit.");
	define("NEW_PROFILE_ERROR", "Du har inte till�telse att �ppna ett konto.");
	define("EDIT_PROFILE_ERROR", "Du har inte till�telse att �ndra din profil.");
	define("CHANGE_DETAILS_MSG", "�ndra dina uppgifter");
	define("CHANGE_DETAILS_DESC", "Klicka p� l�nken ovan om du vill �ndra dina kontakt- eller inloggningsuppgifter som du angav n�r du skapade kontot.");
	define("CHANGE_PASSWORD_MSG", "�ndra l�senordet");
	define("CHANGE_PASSWORD_DESC", "L�nken nedan tar dig till sidan d�r du kan �ndra ditt l�senord.");
	define("SIGN_UP_MSG", "Registrera dig nu");
	define("MY_ACCOUNT_MSG", "Mitt konto");
	define("NEW_USER_MSG", "Ny anv�ndare");
	define("EXISTS_USER_MSG", "Registrerad anv�ndare");
	define("EDIT_PROFILE_MSG", "�ndra profil");
	define("PERSONAL_DETAILS_MSG", "Personliga uppgifter");
	define("DELIVERY_DETAILS_MSG", "Leveransuppgifter");
	define("SAME_DETAILS_MSG", "Om leveransadressen �r samma som ovan, bocka f�r i rutan<br> eller fyll i uppgifterna nedan");
	define("DELIVERY_MSG", "Leverans");
	define("SUBSCRIBE_CHECKBOX_MSG", "Markera denna ruta om du vill ta emot v�rt nyhetsbrev.");
	define("ADDITIONAL_DETAILS_MSG", "Ytterligare uppgifter");
	define("GUEST_MSG", "G�st");

	// ads messages
	define("MY_ADS_MSG", "Mina annonser");
	define("MY_ADS_DESC", "Om du har saker som du vill s�lja, l�gg in en annons h�r. Det �r snabbt och enkelt.");
	define("AD_GENERAL_MSG", "Generell annonsinfo");
	define("ALL_ADS_MSG", "Alla annonser");
	define("AD_SELLER_MSG", "S�ljare");
	define("AD_START_MSG", "Startdatum");
	define("AD_RUNS_MSG", "Dagar att synas");
	define("AD_QTY_MSG", "Antal");
	define("AD_AVAILABILITY_MSG", "Tillg�nglighet");
	define("AD_COMPARED_MSG", "Till�t annonsj�mf�relse");
	define("AD_UPLOAD_MSG", "Ladda upp bild");
	define("AD_DESCRIPTION_MSG", "Beskrivning");
	define("AD_SHORT_DESC_MSG", "Kort beskrivning");
	define("AD_FULL_DESC_MSG", "L�ng beskrivning");
	define("AD_LOCATION_MSG", "Plats");
	define("AD_LOCATION_INFO_MSG", "Ytterligare information");
	define("AD_PROPERTIES_MSG", "Annons-inst�llningar");
	define("AD_SPECIFICATION_MSG", "Annons-detaljer");
	define("AD_MORE_IMAGES_MSG", "Fler bilder");
	define("AD_IMAGE_DESC_MSG", "Bildbeskrivning");
	define("AD_DELETE_CONFIRM_MSG", "Vill du ta bort denna annons?");
	define("AD_NOT_APPROVED_MSG", "Inte godk�nd");
	define("AD_RUNNING_MSG", "P�g�ende");
	define("AD_CLOSED_MSG", "Avslutad");
	define("AD_NOT_STARTED_MSG", "Inte startad");
	define("AD_NEW_ERROR", "Du har inte till�telse att skapa en annons.");
	define("AD_EDIT_ERROR", "Du har inte till�telse att �ndra denna annons.");
	define("AD_DELETE_ERROR", "Du har inte till�telse att radera denna annons.");
	define("NO_ADS_MSG", "Inga annonser kunde hittas.");
	define("NO_AD_MSG", "Ingen annons med detta ID finns tillg�nglig i denna kategorin.");
	define("FOUND_ADS_MSG", "Vi hittade <b>{found_records}</b> annonser som matchade s�ktermen '<b>{search_string}</b>'");
	define("AD_OFFER_MESSAGE_MSG", "Erbjudande");
	define("AD_OFFER_LOGIN_ERROR", "Du beh�ver logga in f�r att forts�tta");
	define("AD_REQUEST_BUTTON", "Skicka f�rfr�gan");
	define("AD_SENT_MSG", "Din f�rfr�gan har nu skickats med framg�ng.");
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
	define("SEARCH_FOR_MSG", "S�k efter");
	define("SEARCH_IN_MSG", "S�k i");
	define("SEARCH_TITLE_MSG", "�mne");
	define("SEARCH_CODE_MSG", "Kod");
	define("SEARCH_SHORT_DESC_MSG", "Kort beskrivning");
	define("SEARCH_FULL_DESC_MSG", "Detaljerad beskrivning");
	define("SEARCH_CATEGORY_MSG", "S�k i kategori");
	define("SEARCH_MANUFACTURER_MSG", "Tillverkare");
	define("SEARCH_SELLER_MSG", "S�ljare");
	define("SEARCH_PRICE_MSG", "Prisniv�");
	define("SEARCH_WEIGHT_MSG", "Viktbegr�nsning");
	define("SEARCH_RESULTS_MSG", "S�kresultat");
	define("FULL_SITE_SEARCH_MSG", "Helsidess�k");

	// compare messages
	define("COMPARE_MSG", "J�mf�r");
	define("COMPARE_REMOVE_MSG", "Ta bort");
	define("COMPARE_REMOVE_HELP_MSG", "Klicka h�r f�r att ta bort denna produkten i j�mf�relsen");
	define("COMPARE_MIN_ALLOWED_MSG", "Du m�ste v�lja minst 2 produkter");
	define("COMPARE_MAX_ALLOWED_MSG", "Du f�r inte v�lja fler �n 5 produkter");
	define("COMPARE_PARAM_ERROR_MSG", "J�mf�ringsparametern har fel v�rde");

	// Tell a friend messages
	define("TELL_FRIEND_TITLE", "Tipsa en v�n");
	define("TELL_FRIEND_SUBJECT_MSG", "Din v�n har skickat dig denna l�nk.");
	define("TELL_FRIEND_DEFAULT_MSG", "Hej {friend_name}! Jag t�nkte att du kanske �r intresserad av {item_title} p� denna webbplatsen: {item_url}");
	define("TELL_YOUR_NAME_FIELD", "Ditt namn");
	define("TELL_YOUR_EMAIL_FIELD", "Din epostadress");
	define("TELL_FRIENDS_NAME_FIELD", "V�nnens namn");
	define("TELL_FRIENDS_EMAIL_FIELD", "V�nnens epostadress");
	define("TELL_COMMENT_FIELD", "Kommentar");
	define("TELL_FRIEND_PRIVACY_NOTE_MSG", "R�ttighetsnotis: Vi sparar inte din eller din v�ns epostadress f�r n�got annat syfte.");
	define("TELL_SENT_MSG", "Ditt meddelande har skickats!<br>Tack s� mycket!");
	define("TELL_FRIEND_MESSAGE_MSG", "Du kanske �r intresserad av att se {item_title} p� {item_url}.<br>{user_name} l�mnade ett meddelande:<br>{user_comment}");
	define("TELL_FRIEND_PARAM_MSG", "Tipsa en v�n-parameter");
	define("TELL_FRIEND_PARAM_DESC", "L�gg till en Tipsnings-URL till 'Tipsa en v�n'-parametern om den �r tillg�nglig f�r anv�ndaren");
	define("FRIEND_COOKIE_EXPIRES_MSG", "Tipsa en v�n-cookie f�rfaller");

	define("CONTACT_US_TITLE", "Kontakta oss");
	define("CONTACT_USER_NAME_FIELD", "Ditt namn");
	define("CONTACT_USER_EMAIL_FIELD", "Din epostadress");
	define("CONTACT_SUMMARY_FIELD", "Enradsbeskrivning");
	define("CONTACT_DESCRIPTION_FIELD", "Beskrivning");
	define("CONTACT_REQUEST_SENT_MSG", "Din f�rfr�gan har skickats med framg�ng.");

	// buttons
	define("GO_BUTTON", "G�");
	define("CONTINUE_BUTTON", "Forts�tt");
	define("BACK_BUTTON", "Bak�t");
	define("NEXT_BUTTON", "N�sta");
	define("PREV_BUTTON", "F�reg�ende");
	define("SIGN_IN_BUTTON", "Registrera");
	define("LOGIN_BUTTON", "Logga in");
	define("LOGOUT_BUTTON", "Logga ut");
	define("SEARCH_BUTTON", "S�k");
	define("RATE_IT_BUTTON", "Betygs�tt!");
	define("ADD_BUTTON", "L�gg till");
	define("UPDATE_BUTTON", "Uppdatera");
	define("APPLY_BUTTON", "Anv�nd");
	define("REGISTER_BUTTON", "Registrera");
	define("VOTE_BUTTON", "R�sta");
	define("CANCEL_BUTTON", "Avbryt");
	define("CLEAR_BUTTON", "Rensa");
	define("RESET_BUTTON", "�terst�ll");
	define("DELETE_BUTTON", "Radera");
	define("DELETE_ALL_BUTTON", "Radera allt");
	define("SUBSCRIBE_BUTTON", "Prenumerera");
	define("UNSUBSCRIBE_BUTTON", "Avsluta prenumeration");
	define("SUBMIT_BUTTON", "Skicka");
	define("UPLOAD_BUTTON", "Ladda upp");
	define("SEND_BUTTON", "S�nd");
	define("PREVIEW_BUTTON", "F�rhandsvisa");
	define("FILTER_BUTTON", "Filter");
	define("DOWNLOAD_BUTTON", "Ladda ner");
	define("REMOVE_BUTTON", "Ta bort");
	define("EDIT_BUTTON", "Redigera");

	// controls
	define("CHECKBOXLIST_MSG", "Checkbox-lista");
	define("LABEL_MSG", "Etikett");
	define("LISTBOX_MSG", "Listbox");
	define("RADIOBUTTON_MSG", "Radioknappar");
	define("TEXTAREA_MSG", "Textarea");
	define("TEXTBOX_MSG", "Textbox");
	define("TEXTBOXLIST_MSG", "Textboxlista");
	define("IMAGEUPLOAD_MSG", "Bilduppladdning");
	define("CREDIT_CARD_MSG", "Kreditkort");
	define("GROUP_MSG", "Grupp");

	// fields
	define("LOGIN_FIELD", "Anv�ndarnamn");
	define("PASSWORD_FIELD", "L�senord");
	define("CONFIRM_PASS_FIELD", "Bekr�fta l�senord");
	define("NEW_PASS_FIELD", "Nytt l�senord");
	define("CURRENT_PASS_FIELD", "Nuvarande l�senord");
	define("FIRST_NAME_FIELD", "F�rnamn");
	define("LAST_NAME_FIELD", "Efternamn");
	define("NICKNAME_FIELD", "Anv�ndarnamn");
	define("PERSONAL_IMAGE_FIELD", "Personlig bild");
	define("COMPANY_SELECT_FIELD", "F�retag");
	define("SELECT_COMPANY_MSG", "V�lj f�retag");
	define("COMPANY_NAME_FIELD", "F�retagsnamn");
	define("EMAIL_FIELD", "Epost");
	define("STREET_FIRST_FIELD", "Gatuadress");
	define("STREET_SECOND_FIELD", "C/O-adress");
	define("CITY_FIELD", "Stad");
	define("PROVINCE_FIELD", "L�n");
	define("SELECT_STATE_MSG", "V�lj l�n");
	define("STATE_FIELD", "L�n");
	define("ZIP_FIELD", "Postnummer");
	define("SELECT_COUNTRY_MSG", "V�lj land");
	define("COUNTRY_FIELD", "Land");
	define("PHONE_FIELD", "Telefonnummer");
	define("DAYTIME_PHONE_FIELD", "Telefonnummer - dagtid");
	define("EVENING_PHONE_FIELD", "Telefonnummer - kv�llstid");
	define("CELL_PHONE_FIELD", "Mobiltelefonnummer");
	define("FAX_FIELD", "Faxnummer");
	define("VALIDATION_CODE_FIELD", "Validationskod");
	define("AFFILIATE_CODE_FIELD", "Affiliatekod");
	define("AFFILIATE_CODE_HELP_MSG", "Var v�nlig anv�nd f�ljande URL {affiliate_url} f�r att skapa en l�nk som affilierar till v�r webbplats");
	define("PAYPAL_ACCOUNT_FIELD", "PayPalkonto");
	define("TAX_ID_FIELD", "Momsnummer");
	define("MSN_ACCOUNT_FIELD", "MSN-konto");
	define("ICQ_NUMBER_FIELD", "ICQ-nummer");
	define("USER_SITE_URL_FIELD", "Anv�ndarens hemsida");
	define("HIDDEN_STATUS_FIELD", "Dold status");
	define("HIDE_MY_ONLINE_STATUS_MSG", "Visa inte min onlinestatus");
	define("SUMMARY_MSG", "Summering");

	// no records messages
	define("NO_RECORDS_MSG", "Inga objekt hittades");
	define("NO_EVENTS_MSG", "Inga h�ndelser hittades");
	define("NO_QUESTIONS_MSG", "Inga fr�gor hittades");
	define("NO_NEWS_MSG", "Inga nyhetsartiklar hittades");
	define("NO_POLLS_MSG", "Inga omr�stningar hittades");

	// SMS messages
	define("SMS_TITLE", "SMS");
	define("SMS_TEST_TITLE", "SMS-test");
	define("SMS_TEST_DESC", "Var v�nlig skriv ditt mobiltelefonnummer och klicka p� knappen 'SEND_BUTTON' f�r att ta emot ett test-sms.");
	define("INVALID_CELL_PHONE", "Felaktigt mobiltelefonnummer");

	define("ARTICLE_RELATED_PRODUCTS_TITLE", "Artikel relaterade produkter");
	define("CATEGORY_RELATED_PRODUCTS_TITLE", "Kategori relaterade produkter");
	define("SELECT_TYPE_MSG", "V�lj typ");
	define("OFFER_PRICE_MSG", "Erbjudande pris");
	define("OFFER_MESSAGE_MSG", "Erbjudande meddelande");

	define("MY_WISHLIST_MSG", "Min �nskelista");
	define("MY_REMINDERS_MSG", "Mina p�minnelser");
	define("EDIT_REMINDER_MSG", "Redigera p�minnelser");

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
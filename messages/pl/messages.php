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
	// data
	define("YEAR_MSG", "Rok");
	define("YEARS_QTY_MSG", "{quantity} years");
	define("MONTH_MSG", "Miesi�c");
	define("MONTHS_QTY_MSG", "{quantity} months");
	define("DAY_MSG", "Dzie�");
	define("DAYS_MSG", "Dni");
	define("DAYS_QTY_MSG", "{quantity} days");
	define("HOUR_MSG", "Godzina");
	define("HOURS_QTY_MSG", "{quantity} hours");
	define("MINUTE_MSG", "Minuta");
	define("MINUTES_QTY_MSG", "{quantity} minutes");
	define("SECOND_MSG", "Sekunda");
	define("SECONDS_QTY_MSG", "{quantity} seconds");
	define("WEEK_MSG", "Week");
	define("WEEKS_QTY_MSG", "{quantity} weeks");
	define("TODAY_MSG", "Today");
	define("YESTERDAY_MSG", "Yesterday");
	define("LAST_7DAYS_MSG", "Last 7 Days");
	define("THIS_MONTH_MSG", "This Month");
	define("LAST_MONTH_MSG", "Last Month");
	define("THIS_QUARTER_MSG", "This Quarter");
	define("THIS_YEAR_MSG", "This Year");

	//miesi�ce
	define("JANUARY", "Stycze�");
	define("FEBRUARY", "Luty");
	define("MARCH", "Marzec");
	define("APRIL", "Kwiecie�");
	define("MAY", "Maj");
	define("JUNE", "Czerwiec");
	define("JULY", "Lipiec");
	define("AUGUST", "Sierpie�");
	define("SEPTEMBER", "Wrzesie�");
	define("OCTOBER", "Pa�dziernik");
	define("NOVEMBER", "Listopad");
	define("DECEMBER", "Grudzie�");

	define("JANUARY_SHORT", "Sty");
	define("FEBRUARY_SHORT", "Lut");
	define("MARCH_SHORT", "Mar");
	define("APRIL_SHORT", "Kwi");
	define("MAY_SHORT", "Maj");
	define("JUNE_SHORT", "Cze");
	define("JULY_SHORT", "Lip");
	define("AUGUST_SHORT", "Sie");
	define("SEPTEMBER_SHORT", "Wrz");
	define("OCTOBER_SHORT", "Pa�");
	define("NOVEMBER_SHORT", "Lis");
	define("DECEMBER_SHORT", "Gru");

	// dni tygodnia
	define("SUNDAY", "Niedziela");
	define("MONDAY", "Poniedzia�ek");
	define("TUESDAY", "Wtorek");
	define("WEDNESDAY", "�roda");
	define("THURSDAY", "Czwartek");
	define("FRIDAY", "Pi�tek");
	define("SATURDAY", "Sobota");

	define("SUNDAY_SHORT", "Nie");
	define("MONDAY_SHORT", "Pon");
	define("TUESDAY_SHORT", "Wto");
	define("WEDNESDAY_SHORT", "�ro");
	define("THURSDAY_SHORT", "Czw");
	define("FRIDAY_SHORT", "Pi�");
	define("SATURDAY_SHORT", "Sob");

	// sprawdzanie poprawno�ci danych
	define("REQUIRED_MESSAGE", "<b>{field_name}</b>jest wymagane");
	define("UNIQUE_MESSAGE", "Warto�� w polu <b>{field_name}</b> jest ju� w bazie danych");
	define("VALIDATION_MESSAGE", "Sprawdzanie zgodno�ci danych dla pola {field_name} nie powiod�o si�");
	define("MATCHED_MESSAGE", "<b>{field_one}</b> i <b>{field_two}</b> nie pasuj� do siebie");
	define("INSERT_ALLOWED_ERROR", "Sorry, but insert operation is not allowed for you");
	define("UPDATE_ALLOWED_ERROR", "Sorry, but update operation is not allowed for you");
	define("DELETE_ALLOWED_ERROR", "Sorry, but delete operation is not allowed for you");
	define("ALPHANUMERIC_ALLOWED_ERROR", "Only alpha-numeric characters, hyphen and underscore are allowed for field <b>{field_name}</b>");

	define("INCORRECT_DATE_MESSAGE", "<b>{field_name}</b> zawiera nieprawid�ow� warto��. U�yj dat z kalendarza.");
	define("INCORRECT_MASK_MESSAGE", "<b>{field_name}</b> nie pasuje do maski wyboru. U�yj nast�puj�cej maski '<b>{field_mask}</b>'");
	define("INCORRECT_EMAIL_MESSAGE", "Niepoprawny format email w polu {field_name}");
	define("INCORRECT_VALUE_MESSAGE", "Niepoprawna warto�� w polu <b>{field_name}</b>");

	define("MIN_VALUE_MESSAGE", "Warto�� w polu <b>{field_name}</b>nie mo�e by� mniejsza ni� {min_value}");
	define("MAX_VALUE_MESSAGE", "Warto�� w polu <b>{field_name}</b> nie mo�e by� mniejsza ni� {max_value}");
	define("MIN_LENGTH_MESSAGE", "D�ugo�� pola <b>{field_name}</b> nie mo�e by� mniejsza ni� {min_length} znak�w");
	define("MAX_LENGTH_MESSAGE", "D�ugo�� pola <b>{field_name}</b> nie mo�e by� wi�ksza od {max_length} znak�w");

	define("FILE_PERMISSION_MESSAGE", "Brak prawa do zapisu dla pliku <b>'{file_name}'</b>. Przed kontynuacj� prosimy zmieni� prawa dost�pu do pliku");
	define("FOLDER_PERMISSION_MESSAGE", "Brak prawa do zapisu dla katalogu <b>'{folder_name}'</b>. Przed kontynuacj� prosimy zmieni� prawa dost�pu do katalogu");
	define("INVALID_EMAIL_MSG", "Tw�j email nie jest prawid�owy");
	define("DATABASE_ERROR_MSG", "Wyt�pi� b��d bazy danych");
	define("BLACK_IP_MSG", "This action is not permitted from your host.");
	define("BANNED_CONTENT_MSG", "Sorry, the provided content contains illegal statement.");
	define("ERRORS_MSG", "B��dy");
	define("REGISTERED_ACCESS_MSG", "Only registered users could access this option.");
	define("SELECT_FROM_LIST_MSG", "Wybierz z listy");

	// tytu�y
	define("TOP_RATED_TITLE", "Najlepiej oceniane");
	define("TOP_VIEWED_TITLE", "Najcz�ciej ogl�dane");
	define("RECENTLY_VIEWED_TITLE", "Ostatnio ogl�dane");
	define("HOT_TITLE", "Gor�ce");
	define("LATEST_TITLE", "Ostatnie");
	define("CONTENT_TITLE", "Zawarto��");
	define("RELATED_TITLE", "Powi�zany");
	define("SEARCH_TITLE", "Szukaj");
	define("ADVANCED_SEARCH_TITLE", "Szukanie zaawansowane");
	define("LOGIN_TITLE", "U�ytkownik");
	define("CATEGORIES_TITLE", "Kategorie");
	define("MANUFACTURERS_TITLE", "Wytw�rcy");
	define("SPECIAL_OFFER_TITLE", "Oferta specjalna");
	define("NEWS_TITLE", "Nowo�ci");
	define("EVENTS_TITLE", "Wydarzenia");
	define("PROFILE_TITLE", "Profil");
	define("USER_HOME_TITLE", "Strona domowa");
	define("DOWNLOAD_TITLE", "�ci�gnij pliki");
	define("FAQ_TITLE", "Najcz�ciej zadawane pytania");
	define("POLL_TITLE", "Ankieta");
	define("HOME_PAGE_TITLE", "Strona startowa");
	define("CURRENCY_TITLE", "Waluta");
	define("SUBSCRIBE_TITLE", "Zapisz si� do newsletter");
	define("UNSUBSCRIBE_TITLE", "Wypisz si� z newsletter");
	define("UPLOAD_TITLE", "Umie�� plik");
	define("ADS_TITLE", "Reklama");
	define("ADS_COMPARE_TITLE", "Por�wnanie reklam");
	define("ADS_SELLERS_TITLE", "Sprzedawcy");
	define("AD_REQUEST_TITLE", "Z�� ofert� / Zadaj pytanie sprzedawcy");
	define("LANGUAGE_TITLE", "J�zyki");
	define("MERCHANTS_TITLE", "Merchants");
	define("PREVIEW_TITLE", "Preview");
	define("ARTICLES_TITLE", "Articles");
	define("SITE_MAP_TITLE", "Site Map");
	define("LAYOUTS_TITLE", "Layouts");

	// pozycje w menu
	define("MENU_ABOUT", "o nas");
	define("MENU_ACCOUNT", "twoje konto");
	define("MENU_BASKET", "tw�j koszyk");
	define("MENU_CONTACT", "kontakt");
	define("MENU_DOCUMENTATION", "dokumentacja");
	define("MENU_DOWNLOADS", "pliki do �ci�gni�cia");
	define("MENU_EVENTS", "Wydarzenia");
	define("MENU_FAQ", "faq");
	define("MENU_FORUM", "forum");
	define("MENU_HELP", "pomoc");
	define("MENU_HOME", "start");
	define("MENU_HOW", "jak kupowa�?");
	define("MENU_MEMBERS", "cz�onkowie");
	define("MENU_MYPROFILE", "m�j profil");
	define("MENU_NEWS", "nowo�ci");
	define("MENU_PRIVACY", "ochrona prywatno�ci");
	define("MENU_PRODUCTS", "produkty");
	define("MENU_REGISTRATION", "rejestracja");
	define("MENU_SHIPPING", "dostawa");
	define("MENU_SIGNIN", "zapisz si�");
	define("MENU_SIGNOUT", "wypisz si�");
	define("MENU_SUPPORT", "wsparcie");
	define("MENU_USERHOME", "strona u�ytkownika");
	define("MENU_ADS", "klasyfikacja reklam");
	define("MENU_ADMIN", "administracja");
	define("MENU_KNOWLEDGE", "Knowledge Base");

	// g��wne wyra�enia
	define("NO_MSG", "Nie");
	define("YES_MSG", "Tak");
	define("NOT_AVAILABLE_MSG", "N/D");
	define("MORE_MSG", "wi�cej...");
	define("READ_MORE_MSG", "czytaj wi�cej...");
	define("CLICK_HERE_MSG", "kliknij tu");
	define("ENTER_YOUR_MSG", "Wprowad� Tw�j");
	define("CHOOSE_A_MSG", "Wybierz");
	define("PLEASE_CHOOSE_MSG", "Prosimy wybra�");
	define("SELECT_MSG", "Wyb�r");
	define("DATE_FORMAT_MSG", "u�yj nast�puj�cego formatu <b>{date_format}</b>");
	define("NEXT_PAGE_MSG", "Nast�pna");
	define("PREV_PAGE_MSG", "Poprzednia");
	define("FIRST_PAGE_MSG", "Pierwsza");
	define("LAST_PAGE_MSG", "Ostatnia");
	define("OF_PAGE_MSG", "z");
	define("TOP_CATEGORY_MSG", "Najwy�sza kategoria");
	define("SEARCH_IN_CURRENT_MSG", "Obecna kategoria");
	define("SEARCH_IN_ALL_MSG", "Wszystkie kategorie");
	define("FOUND_IN_MSG", "Znaleziono w");
	define("TOTAL_VIEWS_MSG", "Ogl�dany");
	define("VOTES_MSG", "G�osy");
	define("TOTAL_VOTES_MSG", "Total Votes");
	define("TOTAL_POINTS_MSG", "Total Points");
	define("VIEW_RESULTS_MSG", "Zobacz wyniki");
	define("PREVIOUS_POLLS_MSG", "Poprzednie ankiety");
	define("TOTAL_MSG", "Wszystkich");
	define("CLOSED_MSG", "Zamkni�te");
	define("CLOSE_WINDOW_MSG", "Zamknij okno");
	define("ASTERISK_MSG", "gwiazdka (*) - wymagane pole");
	define("PROVIDE_INFO_MSG", "Prosimy o wprowadzenie informacji w sekcjach zaznaczonych na czerwono. P�niej kliknij '{button_name}'");
	define("FOUND_ARTICLES_MSG", "Znale�li�my <b>{found_records}</b> artyku��w pasuj�cych do wyra�enia '<b>{search_string}</b>'");
	define("NO_ARTICLE_MSG", "Artyku� o tym identyfikatorze nie jest dost�pny");
	define("NO_ARTICLES_MSG", "Nie znaleziono �adnego artyku�u");
	define("NOTES_MSG", "Notatki");
	define("KEYWORDS_MSG", "S�owa kluczowwe");
	define("LINK_URL_MSG", "Link");
	define("DOWNLOAD_URL_MSG", "�ci�gnij pliki");
	define("SUBSCRIBE_FORM_MSG", "Aby otrzyma� nasz newsletter prosimy o wprowadzenie poni�ej Twojego adresu email i wci�ni�cie klawisza '{button_name}'.");
	define("UNSUBSCRIBE_FORM_MSG", "Prosimy wpisz poni�ej Tw�j adres email i wci�nij klawisz '{button_name}'.");
	define("SUBSCRIBE_LINK_MSG", "Zapisz si�");
	define("UNSUBSCRIBE_LINK_MSG", "Wypisz si�");
	define("SUBSCRIBED_MSG", "Gratulujemy! Zosta�e�/a� cz�onkiem naszego newsletter'a.");
	define("ALREADY_SUBSCRIBED_MSG", "Zosta�e�/a� ju� zapisany/na do listy odbiorc�w naszego newsletter'a. Dzi�kujemy.");
	define("UNSUBSCRIBED_MSG", "Wypisa�e�/a� si� z listy odiorc�w naszego newsletter'a. Dzi�kujemy i zapraszamy ponownie.");
	define("UNSUBSCRIBED_ERROR_MSG", "Przepraszamy, ale nie mo�emy znale�� w naszej bazie email'a, kt�ty poda�e�/a�. Prawdopodobnie ju� wypisa�e�/a� si� z naszego newsletter'a.");
	define("FORGOT_PASSWORD_MSG", "Zapomnia�e�/a� has�a?");
	define("FORGOT_PASSWORD_DESC", "Prosimy wprowad� adres email, kt�ry poda�e�/a� przy rejestracji:");
	define("FORGOT_EMAIL_ERROR_MSG", "Przepraszamy, ale nie mo�emy znale�� w naszej bazie email'a, kt�ty poda�e�/a�.");
	define("FORGOT_EMAIL_SENT_MSG", "Dok�adne instrukcje dotycz�ce logowania zosta�y wys�ane na Tw�j email.");
	define("RESET_PASSWORD_REQUIRE_MSG", "Niekt�re wymagane parametry nie zosta�y odnalezione.");
	define("RESET_PASSWORD_PARAMS_MSG", "Parametry, kt�re dostarczy�e�/a� nie pasuj� do jakichkolwiek parametr�w zawartych w naszej bazie danych.");
	define("RESET_PASSWORD_EXPIRY_MSG", "Kod resetuj�cy, kt�ry dostarczy�e�/a� wygas�. Wy�lij zapytanie o nowy kod, kt�ry zresetuje Twoje has�o.");
	define("RESET_PASSWORD_SAVED_MSG", "Twoje nowe has�o zosta�o zapisane.");
	define("PRINTER_FRIENDLY_MSG", "Przyjazne drukowanie");
	define("PRINT_PAGE_MSG", "Wydrukuj t� stron�");
	define("ATTACHMENTS_MSG", "Attachments");
	define("VIEW_DETAILS_MSG", "View Details");
	define("HTML_MSG", "HTML");
	define("PLAIN_TEXT_MSG", "Plain Text");
	define("META_DATA_MSG", "Meta Data");
	define("META_TITLE_MSG", "Page Title");
	define("META_KEYWORDS_MSG", "Meta Keywords");
	define("META_DESCRIPTION_MSG", "Meta Description");
	define("FRIENDLY_URL_MSG", "Friendly URL");
	define("IMAGES_MSG", "Obrazki");
	define("IMAGE_MSG", "Obrazek");
	define("IMAGE_TINY_MSG", "Tiny Image");
	define("IMAGE_TINY_ALT_MSG", "Tiny Image Alt");
	define("IMAGE_SMALL_MSG", "Ma�y obrazek");
	define("IMAGE_SMALL_DESC", "shown on list page");
	define("IMAGE_SMALL_ALT_MSG", "Small Image Alt");
	define("IMAGE_LARGE_MSG", "Du�y obrazek");
	define("IMAGE_LARGE_DESC", "shown on details page");
	define("IMAGE_LARGE_ALT_MSG", "Large Image Alt");
	define("IMAGE_SUPER_MSG", "Super-Sized Image");
	define("IMAGE_SUPER_DESC", "image popup in the new window");
	define("IMAGE_POSITION_MSG", "Image Position");
	define("UPLOAD_IMAGE_MSG", "Wgraj obraz na serwer");
	define("UPLOAD_FILE_MSG", "Upload File");
	define("SELECT_IMAGE_MSG", "Select Image");
	define("SELECT_FILE_MSG", "Select File");
	define("SHOW_BELOW_PRODUCT_IMAGE_MSG", "show image below large product image");
	define("SHOW_IN_SEPARATE_SECTION_MSG", "show image in separate images section");
	define("IS_APPROVED_MSG", "Approved");
	define("NOT_APPROVED_MSG", "Not Approved");
	define("IS_ACTIVE_MSG", "Is Active");
	define("CATEGORY_MSG", "Kategoria");
	define("SELECT_CATEGORY_MSG", "Wybierz kategorie");
	define("DESCRIPTION_MSG", "Opis");
	define("SHORT_DESCRIPTION_MSG", "Short Description");
	define("FULL_DESCRIPTION_MSG", "Full Description");
	define("HIGHLIGHTS_MSG", "Highlights");
	define("SPECIAL_OFFER_MSG", "Oferta specjalna");
	define("ARTICLE_MSG", "Article");
	define("OTHER_MSG", "Other");
	define("WIDTH_MSG", "Width");
	define("HEIGHT_MSG", "Height");
	define("LENGTH_MSG", "Length");
	define("WEIGHT_MSG", "Waga");
	define("QUANTITY_MSG", "Ilo��");
	define("CALENDAR_MSG", "Calendar");
	define("FROM_DATE_MSG", "From Date");
	define("TO_DATE_MSG", "To Date");
	define("TIME_PERIOD_MSG", "Time Period");
	define("GROUP_BY_MSG", "Group By");
	define("BIRTHDAY_MSG", "Birthday");
	define("BIRTH_DATE_MSG", "Birth Date");
	define("BIRTH_YEAR_MSG", "Birth Year");
	define("BIRTH_MONTH_MSG", "Birth Month");
	define("BIRTH_DAY_MSG", "Birth Day");
	define("STEP_NUMBER_MSG", "Step {current_step} of {total_steps}");
	define("WHERE_STATUS_IS_MSG", "Where status is");
	define("ID_MSG", "ID");
	define("QTY_MSG", "Qty");
	define("TYPE_MSG", "Typ");
	define("NAME_MSG", "Name");
	define("TITLE_MSG", "Tytu�");
	define("DEFAULT_MSG", "Default");
	define("OPTIONS_MSG", "Options");
	define("EDIT_MSG", "Edit");
	define("CONFIRM_DELETE_MSG", "Would you like to delete this {record_name}?");
	define("DESC_MSG", "Desc");
	define("ASC_MSG", "Asc");
	define("ACTIVE_MSG", "Active");
	define("INACTIVE_MSG", "Inactive");
	define("EXPIRED_MSG", "Wygas�e");
	define("EMOTICONS_MSG", "Emoticons");
	define("EMOTION_ICONS_MSG", "Emotion Icons");
	define("VIEW_MORE_EMOTICONS_MSG", "View more Emoticons");
	define("SITE_NAME_MSG", "Site Name");
	define("SITE_URL_MSG", "URL strony");
	define("SORT_ORDER_MSG", "Sort Order");
	define("NEW_MSG", "New");
	define("USED_MSG", "Used");
	define("REFURBISHED_MSG", "Refurbished");
	define("ADD_NEW_MSG", "Add New");
	define("SETTINGS_MSG", "Settings");
	define("VIEW_MSG", "Zobacz");
	define("STATUS_MSG", "Status");
	define("NONE_MSG", "None");
	define("PRICE_MSG", "Cena");
	define("TEXT_MSG", "Text");
	define("WARNING_MSG", "Warning");
	define("HIDDEN_MSG", "Hidden");
	define("CODE_MSG", "Code");
	define("LANGUAGE_MSG", "Language");
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
	define("RELEASES_TITLE", "Wydania");
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
	define("EMAIL_NOTIFICATION_MSG", "Email Notification");
	define("EMAIL_NOTIFICATION_ADMIN_MSG", "Administrator Email Notification");
	define("EMAIL_NOTIFICATION_USER_MSG", "User Email Notification");
	define("EMAIL_SEND_ADMIN_MSF", "Send notification to administrator ");
	define("EMAIL_SEND_USER_MSG", "Send notification to user");
	define("EMAIL_USER_IF_STATUS_MSG", "Send notification email to user when the status is applied");
	define("EMAIL_TO_MSG", "To");
	define("EMAIL_TO_USER_DESC", "Customer email used if is empty");
	define("EMAIL_FROM_MSG", "From");
	define("EMAIL_CC_MSG", "Cc");
	define("EMAIL_BCC_MSG", "Bcc");
	define("EMAIL_REPLY_TO_MSG", "Reply To");
	define("EMAIL_RETURN_PATH_MSG", "Return Path");
	define("EMAIL_SUBJECT_MSG", "Subject");
	define("EMAIL_MESSAGE_TYPE_MSG", "Message Type");
	define("EMAIL_MESSAGE_MSG", "Message");
	define("SMS_NOTIFICATION_MSG", "SMS Notification");
	define("SMS_NOTIFICATION_ADMIN_MSG", "Administrator SMS Notification");
	define("SMS_NOTIFICATION_USER_MSG", "User SMS Notification ");
	define("SMS_SEND_ADMIN_MSF", "Send SMS Notification to Administrator");
	define("SMS_SEND_USER_MSG", "Send SMS Notification to User ");
	define("SMS_USER_IF_STATUS_MSG", "Send SMS notification to user when the status is applied");
	define("SMS_RECIPIENT_MSG", "SMS Recipient");
	define("SMS_RECIPIENT_ADMIN_DESC", "administrator cell phone number");
	define("SMS_RECIPIENT_USER_DESC", "Cell phone' field used if is empty");
	define("SMS_ORIGINATOR_MSG", "SMS Originator");
	define("SMS_MESSAGE_MSG", "SMS Message");

	// informacje dot. konta u�ytkownika 
	define("LOGIN_AS_MSG", "Zalogowa�e� si� jako");
	define("LOGIN_INFO_MSG", "Infornacje o logowaniu");
	define("ACCESS_HOME_MSG", "Aby mie� dost�p do Twojej strony u�ytkownika");
	define("REMEMBER_LOGIN_MSG", "Pami�taj Twoj� nazw� u�ytkownika i has�o");
	define("ENTER_LOGIN_MSG", "Aby kontynuowa� wprowad� nazw� u�ytkownika i has�o");
	define("LOGIN_PASSWORD_ERROR", "Has�o lub nazwa u�ytkownika jest niepoprawna");
	define("ACCOUNT_APPROVE_ERROR", "Przepraszamy, Twoje konto nie zosta�o jeszcze zatwierdzone.");
	define("ACCOUNT_EXPIRED_MSG", "Your account has expired.");
	define("NEW_PROFILE_ERROR", "Nie masz odpoweidnich praw, aby utworzy� konto.");
	define("EDIT_PROFILE_ERROR", "Nie masz odpowiednich praw do edycji tego profilu.");
	define("CHANGE_DETAILS_MSG", "Zmie� swoje dane szczeg�lowe");
	define("CHANGE_DETAILS_DESC", "Kliknij w link wy�ej je�li chcesz zmieni� dane kontaktowe lub informacje o logowaniu kt�re poda�e� przy tworzeniu konta.");
	define("CHANGE_PASSWORD_MSG", "Zmie� has�o");
	define("CHANGE_PASSWORD_DESC", "Link ni�ej poprowadzi Ci� do strony gdzie mo�esz zmienic swoje has�o");
	define("SIGN_UP_MSG", "Zarejestruj si� teraz");
	define("MY_ACCOUNT_MSG", "Moje konto");
	define("NEW_USER_MSG", "Nowy u�ytkownik");
	define("EXISTS_USER_MSG", "Istniej�cy u�ytkownicy");
	define("EDIT_PROFILE_MSG", "Edytuj profil");
	define("PERSONAL_DETAILS_MSG", "Dane personalne");
	define("DELIVERY_DETAILS_MSG", "Szczeg�y dostawy");
	define("SAME_DETAILS_MSG", "Je�li szczeg�y dostawy s� takie same jak powy�ej zaznacz to pole<br> je�li nie, Prosimy podaj szczeg�y poni�ej");
	define("DELIVERY_MSG", "Dostawa");
	define("SUBSCRIBE_CHECKBOX_MSG", "Prosimy zaznacz to pole je�li chcesz otrzymywa� nasz newsletter.");
	define("ADDITIONAL_DETAILS_MSG", "Additional Details");
	define("GUEST_MSG", "Guest");

	// informacje dot. og�osze�
	define("MY_ADS_MSG", "Moje og�oszenia");
	define("MY_ADS_DESC", "Je�li masz przedmioty, kt�re chcia�by� sprzeda�, umie�� tutaj og�oszenie. Umieszczanie og�osze� jest �atwe i szybkie.");
	define("AD_GENERAL_MSG", "Informacje og�lne dot. og�oszenia");
	define("ALL_ADS_MSG", "All Ads");
	define("AD_SELLER_MSG", "Sprzedawca");
	define("AD_START_MSG", "Data rozpocz�cia emisji");
	define("AD_RUNS_MSG", "Dni do zako�czenia emisji");
	define("AD_QTY_MSG", "Ilo��");
	define("AD_AVAILABILITY_MSG", "Dost�pno��");
	define("AD_COMPARED_MSG", "Pozw�l na por�wnanie og�osze�");
	define("AD_UPLOAD_MSG", "Wgraj obrazek na serwer");
	define("AD_DESCRIPTION_MSG", "Opis");
	define("AD_SHORT_DESC_MSG", "Kr�tki opis");
	define("AD_FULL_DESC_MSG", "Pe�ny opis");
	define("AD_LOCATION_MSG", "Lokalizacja");
	define("AD_LOCATION_INFO_MSG", "Dodatkowe informacje");
	define("AD_PROPERTIES_MSG", "W�a�ciwo�ci og�oszenia");
	define("AD_SPECIFICATION_MSG", "Specyfikacja og�oszenia");
	define("AD_MORE_IMAGES_MSG", "Wi�cej obrazk�w");
	define("AD_IMAGE_DESC_MSG", "Opis obrazka");
	define("AD_DELETE_CONFIRM_MSG", "Czy chcesz skasowa� to og�oszenie?");
	define("AD_NOT_APPROVED_MSG", "Nie zatwierdzono");
	define("AD_RUNNING_MSG", "W dzia�aniu");
	define("AD_CLOSED_MSG", "Zamkni�te");
	define("AD_NOT_STARTED_MSG", "Nie rozpocz�to emisji");
	define("AD_NEW_ERROR", "Nie masz odpowiednich praw aby utworzy� nowe og�oszenie.");
	define("AD_EDIT_ERROR", "Nie masz odpowiednich praw aby edytowa� to og�oszenie.");
	define("AD_DELETE_ERROR", "Nie masz odpowiednich praw aby skasowa� to og�oszenie.");
	define("NO_ADS_MSG", "Nie znaleziono reklam w tej kategorii");
	define("NO_AD_MSG", "Brak dost�pnego w tej kategorii og�oszenia o tym identyfikatorze");
	define("FOUND_ADS_MSG", "Znaleziono <b>{found_records}</b> og�osze� pasuj�cych do wyra�enia: '<b>{search_string}</b>'");
	define("AD_OFFER_MESSAGE_MSG", "Oferta");
	define("AD_OFFER_LOGIN_ERROR", "Przed przyst�pieniem do dzia�ania musisz by� zalogowany.");
	define("AD_REQUEST_BUTTON", "Wy�lij zapytanie");
	define("AD_SENT_MSG", "Twoja oferta zosta�a wys�ana.");
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

	// informacje dot. wyszukiwania
	define("SEARCH_FOR_MSG", "Szukaj wyra�enia");
	define("SEARCH_IN_MSG", "Szukaj w ");
	define("SEARCH_TITLE_MSG", "Tytu�");
	define("SEARCH_CODE_MSG", "Code");
	define("SEARCH_SHORT_DESC_MSG", "Kr�tki opis");
	define("SEARCH_FULL_DESC_MSG", "Dok�adny opis");
	define("SEARCH_CATEGORY_MSG", "Szukaj w kategorii");
	define("SEARCH_MANUFACTURER_MSG", "Producent");
	define("SEARCH_SELLER_MSG", "Sprzedawca");
	define("SEARCH_PRICE_MSG", "Rozpi�to�� cen");
	define("SEARCH_WEIGHT_MSG", "Limity wagowe");
	define("SEARCH_RESULTS_MSG", "Wyniki wyszukiwania");
	define("FULL_SITE_SEARCH_MSG", "Full Site Search");

	// informacje dot. por�wnywania
	define("COMPARE_MSG", "Por�wnanie");
	define("COMPARE_REMOVE_MSG", "Usu�");
	define("COMPARE_REMOVE_HELP_MSG", "Kliknij tutaj aby usun�� produkt z tabeli por�wnawczej");
	define("COMPARE_MIN_ALLOWED_MSG", "Musisz wybra� conajmniej 2 produkty");
	define("COMPARE_MAX_ALLOWED_MSG", "Nie mo�esz wybra� wi�cej ni� 5 produkt�w");
	define("COMPARE_PARAM_ERROR_MSG", "Parametr por�wnania ma z�� warto��");

	// informacje dot. powiadamiania znajomych
	define("TELL_FRIEND_TITLE", "Powiedz znajomym");
	define("TELL_FRIEND_SUBJECT_MSG", "Ten link wys�a� Tw�j znajomy");
	define("TELL_FRIEND_DEFAULT_MSG", "Cze�� {friend_name} - Pomy�la�em, �e mo�esz by� zainteresowany/na i chcesz zobaczy� {item_title} na stronie {item_url}");
	define("TELL_YOUR_NAME_FIELD", "Twoje imi�");
	define("TELL_YOUR_EMAIL_FIELD", "Tw�j email");
	define("TELL_FRIENDS_NAME_FIELD", "Imi� znajomego");
	define("TELL_FRIENDS_EMAIL_FIELD", "Email znajomego");
	define("TELL_COMMENT_FIELD", "Komentarz");
	define("TELL_FRIEND_PRIVACY_NOTE_MSG", "NOTKA O OCHRONIE DANYCH: Nie zachowujemy i nie wykorzystujemy ponownie Twojego adresu email lub adresu email Twojego znajomego dla jakichkolwiek innych cel�w");
	define("TELL_SENT_MSG", "Twoja wiadomo�� zosta�a wys�ana pomy�lnie<br>Dzi�kujemy!");
	define("TELL_FRIEND_MESSAGE_MSG", "Thought you might be interested in seeing the {item_title} at {item_url}\\n\\n{user_name} left you a note:\\n{user_comment}");
	define("TELL_FRIEND_PARAM_MSG", "Introduce a friend' URL");
	define("TELL_FRIEND_PARAM_DESC", "adds a friend's URL parameter to a 'Tell a Friend' link if it exists for a user");
	define("FRIEND_COOKIE_EXPIRES_MSG", "Friend Cookie Expires");

	define("CONTACT_US_TITLE", "Contact Us");
	define("CONTACT_USER_NAME_FIELD", "Twoje imi�");
	define("CONTACT_USER_EMAIL_FIELD", "Tw�j email");
	define("CONTACT_SUMMARY_FIELD", "Podsumowanie on-line");
	define("CONTACT_DESCRIPTION_FIELD", "Opis");
	define("CONTACT_REQUEST_SENT_MSG", "Your request was successfully sent.");

	// przyciski
	define("GO_BUTTON", "Id�");
	define("CONTINUE_BUTTON", "Kontynuuj");
	define("BACK_BUTTON", "Powr�t");
	define("NEXT_BUTTON", "Nast�pny");
	define("PREV_BUTTON", "Poprzedni");
	define("SIGN_IN_BUTTON", "Zapisz si�");
	define("LOGIN_BUTTON", "Zaloguj si�");
	define("LOGOUT_BUTTON", "Wyloguj si�");
	define("SEARCH_BUTTON", "Szukaj");
	define("RATE_IT_BUTTON", "Oce� produkt!");
	define("ADD_BUTTON", "Dodaj");
	define("UPDATE_BUTTON", "Uaktualnij");
	define("APPLY_BUTTON", "Zastosuj");
	define("REGISTER_BUTTON", "Zarejestruj");
	define("VOTE_BUTTON", "G�osuj");
	define("CANCEL_BUTTON", "Anuluj");
	define("CLEAR_BUTTON", "Wyczy��");
	define("RESET_BUTTON", "Zresetuj");
	define("DELETE_BUTTON", "Skasuj");
	define("DELETE_ALL_BUTTON", "Delete All");
	define("SUBSCRIBE_BUTTON", "Zapisz si�");
	define("UNSUBSCRIBE_BUTTON", "Wypisz si�");
	define("SUBMIT_BUTTON", "Zatwierd�");
	define("UPLOAD_BUTTON", "Wgraj pliki");
	define("SEND_BUTTON", "Wy�lij");
	define("PREVIEW_BUTTON", "Preview");
	define("FILTER_BUTTON", "Filter");
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

	// pola
	define("LOGIN_FIELD", "Nazwa u�ytkownika");
	define("PASSWORD_FIELD", "Has�o");
	define("CONFIRM_PASS_FIELD", "Potwierd� has�o");
	define("NEW_PASS_FIELD", "Nowe has�o");
	define("CURRENT_PASS_FIELD", "Obecne has�o");
	define("FIRST_NAME_FIELD", "Imi�");
	define("LAST_NAME_FIELD", "Nazwisko");
	define("NICKNAME_FIELD", "U�ytkownik");
	define("PERSONAL_IMAGE_FIELD", "Zdj�cie");
	define("COMPANY_SELECT_FIELD", "Firma");
	define("SELECT_COMPANY_MSG", "Wybierz firm�");
	define("COMPANY_NAME_FIELD", "Nazwa firmy");
	define("EMAIL_FIELD", "Email");
	define("STREET_FIRST_FIELD", "Adres linijka 1");
	define("STREET_SECOND_FIELD", "Adres linijka 2");
	define("CITY_FIELD", "Miasto");
	define("PROVINCE_FIELD", "Wojew�dztwo");
	define("SELECT_STATE_MSG", "Select State");
	define("STATE_FIELD", "Stan");
	define("ZIP_FIELD", "Kod pocztowy");
	define("SELECT_COUNTRY_MSG", "Wybierz kraj");
	define("COUNTRY_FIELD", "Kraj");
	define("PHONE_FIELD", "Telefon");
	define("DAYTIME_PHONE_FIELD", "Telefon w godz. dziennych");
	define("EVENING_PHONE_FIELD", "Telefon w godz. wieczornych");
	define("CELL_PHONE_FIELD", "Telefon kom�rkowy");
	define("FAX_FIELD", "Fax");
	define("VALIDATION_CODE_FIELD", "Validation Code");
	define("AFFILIATE_CODE_FIELD", "Affiliate Code");
	define("AFFILIATE_CODE_HELP_MSG", "Please use the following URL {affiliate_url} to create a link affiliated with our site");
	define("PAYPAL_ACCOUNT_FIELD", "PayPal Account");
	define("TAX_ID_FIELD", "Tax Number");
	define("MSN_ACCOUNT_FIELD", "MSN Account");
	define("ICQ_NUMBER_FIELD", "ICQ Number");
	define("USER_SITE_URL_FIELD", "User's Site URL");
	define("HIDDEN_STATUS_FIELD", "Hidden Status");
	define("HIDE_MY_ONLINE_STATUS_MSG", "Do not show my online status");
	define("SUMMARY_MSG", "Podsumowanie");

	// informacje dot. braku wpis�w
	define("NO_RECORDS_MSG", "Nie znaleziono wpis�w");
	define("NO_EVENTS_MSG", "Nie znaleziono zdarzenia");
	define("NO_QUESTIONS_MSG", "Nie znaleziono pyta�");
	define("NO_NEWS_MSG", "Nie znaleziono nowych artyku��w");
	define("NO_POLLS_MSG", "Nie znaleziono ankiet");

	// SMS messages
	define("SMS_TITLE", "SMS");
	define("SMS_TEST_TITLE", "SMS Test");
	define("SMS_TEST_DESC", "Please enter your cell phone number and press button 'SEND_BUTTON' to receive test message");
	define("INVALID_CELL_PHONE", "Incorrect cell phone number");

	define("ARTICLE_RELATED_PRODUCTS_TITLE", "Article Related Products");
	define("CATEGORY_RELATED_PRODUCTS_TITLE", "Category Related Products");
	define("SELECT_TYPE_MSG", "Wybierz Typ");
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
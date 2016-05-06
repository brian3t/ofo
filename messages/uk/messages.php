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


	define("CHARSET", "windows-1251");
	// ����������� ����
	define("YEAR_MSG", "�i�");
	define("YEARS_QTY_MSG", "{quantity} ����");
	define("MONTH_MSG", "�i����");
	define("MONTHS_QTY_MSG", "{quantity} ������");
	define("DAY_MSG", "����");
	define("DAYS_MSG", "���");
	define("DAYS_QTY_MSG", "{quantity} ����");
	define("HOUR_MSG", "������");
	define("HOURS_QTY_MSG", "{quantity} �����");
	define("MINUTE_MSG", "�������");
	define("MINUTES_QTY_MSG", "{quantity} ������");
	define("SECOND_MSG", "�������");
	define("SECONDS_QTY_MSG", "{quantity} ������");
	define("WEEK_MSG", "�������");
	define("WEEKS_QTY_MSG", "{quantity} �������");
	define("TODAY_MSG", "��������");
	define("YESTERDAY_MSG", "�����");
	define("LAST_7DAYS_MSG", "������� 7 ����");
	define("THIS_MONTH_MSG", "��� �����");
	define("LAST_MONTH_MSG", "���������� �����");
	define("THIS_QUARTER_MSG", "��� �������");
	define("THIS_YEAR_MSG", "��� ��");

	//�����
	define("JANUARY", "�i����");
	define("FEBRUARY", "�����");
	define("MARCH", "��������");
	define("APRIL", "��i����");
	define("MAY", "�������");
	define("JUNE", "�������");
	define("JULY", "������");
	define("AUGUST", "�������");
	define("SEPTEMBER", "��������");
	define("OCTOBER", "�������");
	define("NOVEMBER", "��������");
	define("DECEMBER", "�������");

	define("JANUARY_SHORT", "�i�");
	define("FEBRUARY_SHORT", "���");
	define("MARCH_SHORT", "���");
	define("APRIL_SHORT", "��");
	define("MAY_SHORT", "���");
	define("JUNE_SHORT", "���");
	define("JULY_SHORT", "���");
	define("AUGUST_SHORT", "���");
	define("SEPTEMBER_SHORT", "���");
	define("OCTOBER_SHORT", "���");
	define("NOVEMBER_SHORT", "���");
	define("DECEMBER_SHORT", "���");

	// ��� �����
	define("SUNDAY", "���i��");
	define("MONDAY", "�����i���");
	define("TUESDAY", "�i������");
	define("WEDNESDAY", "������");
	define("THURSDAY", "������");
	define("FRIDAY", "�'������");
	define("SATURDAY", "������");

	define("SUNDAY_SHORT", "��");
	define("MONDAY_SHORT", "��");
	define("TUESDAY_SHORT", "��");
	define("WEDNESDAY_SHORT", "��");
	define("THURSDAY_SHORT", "��");
	define("FRIDAY_SHORT", "��");
	define("SATURDAY_SHORT", "��");

	// ���������� �����������
	define("REQUIRED_MESSAGE", "<b>{field_name}</b> ����'������ ����.");
	define("UNIQUE_MESSAGE", "�������� ���� <b>{field_name}</b> ��� ��������������� � ������i. �i����i�� ����-����� i��� ��������.");
	define("VALIDATION_MESSAGE", "�������� ���� {field_name} �� �������.");
	define("MATCHED_MESSAGE", "<b>{field_one}</b> �� <b>{field_two}</b> �� ��i��������.");
	define("INSERT_ALLOWED_ERROR", "�������, ��� �� �� ������ ���������� �������� �������");
	define("UPDATE_ALLOWED_ERROR", "�������, ��� �� �� ������ ���������� �������� �����������");
	define("DELETE_ALLOWED_ERROR", "�������, ��� �� �� ������ ���������� �������� ���������");
	define("ALPHANUMERIC_ALLOWED_ERROR", "ҳ���� ��������-������ �����, ����� � �����������, ��������� ��� ���� <b>{field_name}</b>");

	define("INCORRECT_DATE_MESSAGE", "<b>{field_name}</b> �� ������ �������� ����. �������������� ���������� ���.");
	define("INCORRECT_MASK_MESSAGE", "<b>{field_name}</b> �� �i����i�� ������� ����. �������������� ��������� ������ '<b>{field_mask}</b>'");
	define("INCORRECT_EMAIL_MESSAGE", "������ �������� ���������� ������ � ���  {field_name}.");
	define("INCORRECT_VALUE_MESSAGE", "�������� ���� <b>{field_name}</b> ������� �����������.");

	define("MIN_VALUE_MESSAGE", "�������� ���� <b>{field_name}</b> �� ���� ���� ����� �� {min_value}.");
	define("MAX_VALUE_MESSAGE", "�������� ���� <b>{field_name}</b> �� ���� ���� �i���� �� {max_value}.");
	define("MIN_LENGTH_MESSAGE", "������� ���� <b>{field_name}</b> �� ���� ���� ������ �� {min_length} ������i�.");
	define("MAX_LENGTH_MESSAGE", "������� ���� <b>{field_name}</b> �� ���� ���� �i����� �� {max_length} ������i�.");

	define("FILE_PERMISSION_MESSAGE", "���� ���� �� ����� �� ����� <b>'{file_name}'</b>. ����-����� ������ ����� ������� ����� ���, �� ����������.");
	define("FOLDER_PERMISSION_MESSAGE", "���� ���� �� ����� �� ����� <b>'{folder_name}'</b>. ����-����� ������ ����� ������� �� ����� ����� ���, �� ����������.");
	define("INVALID_EMAIL_MSG", "���� ���������� ������ ������� ������.");
	define("DATABASE_ERROR_MSG", "������� ���� ������.");
	define("BLACK_IP_MSG", "���� �� ��� ���� IP ������ �� ���������.");
	define("BANNED_CONTENT_MSG", "�����, �� ��������� �� ����, ������ ����������� ������.");
	define("ERRORS_MSG", "�������");
	define("REGISTERED_ACCESS_MSG", "ҳ���� ������������ ����������� ������ ������������� ���� ������.");
	define("SELECT_FROM_LIST_MSG", "������� �i ������");

	// ���������
	define("TOP_RATED_TITLE", "�������� �������");
	define("TOP_VIEWED_TITLE", "�������� �����������");
	define("RECENTLY_VIEWED_TITLE", "����� �����������");
	define("HOT_TITLE", "������");
	define("LATEST_TITLE", "�������");
	define("CONTENT_TITLE", "����");
	define("RELATED_TITLE", "������� �����");
	define("SEARCH_TITLE", "�����");
	define("ADVANCED_SEARCH_TITLE", "����������� �����");
	define("LOGIN_TITLE", "����� �����");
	define("CATEGORIES_TITLE", "�������i�");
	define("MANUFACTURERS_TITLE", "���������");
	define("SPECIAL_OFFER_TITLE", "�������� ��������i�");
	define("NEWS_TITLE", "������");
	define("EVENTS_TITLE", "���i�");
	define("PROFILE_TITLE", "����������i ���i");
	define("USER_HOME_TITLE", "������� ����i���");
	define("DOWNLOAD_TITLE", "������������");
	define("FAQ_TITLE", "������� ��i ����� ���������");
	define("POLL_TITLE", "����������");
	define("HOME_PAGE_TITLE", "������� ����i���");
	define("CURRENCY_TITLE", "������");
	define("SUBSCRIBE_TITLE", "ϳ���������");
	define("UNSUBSCRIBE_TITLE", "³���������");
	define("UPLOAD_TITLE", "��������");
	define("ADS_TITLE", "����������");
	define("ADS_COMPARE_TITLE", "��������� ���������");
	define("ADS_SELLERS_TITLE", "��������");
	define("AD_REQUEST_TITLE", "������� ���������� / �������� �������");
	define("LANGUAGE_TITLE", "����");
	define("MERCHANTS_TITLE", "��������");
	define("PREVIEW_TITLE", "���������� ��������");
	define("ARTICLES_TITLE", "�����");
	define("SITE_MAP_TITLE", "���� �����");
	define("LAYOUTS_TITLE", "�������");

	// ������ ����
	define("MENU_ABOUT", "��� ���");
	define("MENU_ACCOUNT", "��� �����");
	define("MENU_BASKET", "��� �����");
	define("MENU_CONTACT", "��'���� � ����");
	define("MENU_DOCUMENTATION", "������������");
	define("MENU_DOWNLOADS", "����������");
	define("MENU_EVENTS", "��䳿");
	define("MENU_FAQ", "�������");
	define("MENU_FORUM", "�����");
	define("MENU_HELP", "��������");
	define("MENU_HOME", "�������");
	define("MENU_HOW", "�� ��������");
	define("MENU_MEMBERS", "�����������");
	define("MENU_MYPROFILE", "�� �����");
	define("MENU_NEWS", "������");
	define("MENU_PRIVACY", "�����������");
	define("MENU_PRODUCTS", "��������");
	define("MENU_REGISTRATION", "���������");
	define("MENU_SHIPPING", "��������");
	define("MENU_SIGNIN", "��������������");
	define("MENU_SIGNOUT", "�����");
	define("MENU_SUPPORT", "ϳ�������");
	define("MENU_USERHOME", "��� �������");
	define("MENU_ADS", "����������");
	define("MENU_ADMIN", "��������������");
	define("MENU_KNOWLEDGE", "���� �����");

	// ������� ������
	define("NO_MSG", "�i");
	define("YES_MSG", "���");
	define("NOT_AVAILABLE_MSG", "�i�����i�");
	define("MORE_MSG", "���...");
	define("READ_MORE_MSG", "������ ���i...");
	define("CLICK_HERE_MSG", "������i�� ���");
	define("ENTER_YOUR_MSG", "����i�� ��i�");
	define("CHOOSE_A_MSG", "�����i��");
	define("PLEASE_CHOOSE_MSG", "����-����� �����i��");
	define("SELECT_MSG", "�������");
	define("DATE_FORMAT_MSG", "�������������� ��������� ������ <b>{date_format}</b>");
	define("NEXT_PAGE_MSG", "�������� ����i���");
	define("PREV_PAGE_MSG", "��������� ����i���");
	define("FIRST_PAGE_MSG", "����� ����i���");
	define("LAST_PAGE_MSG", "������� ����i���");
	define("OF_PAGE_MSG", "�");
	define("TOP_CATEGORY_MSG", "���");
	define("SEARCH_IN_CURRENT_MSG", "������� �������i�");
	define("SEARCH_IN_ALL_MSG", "��i �������i�");
	define("FOUND_IN_MSG", "�������� �");
	define("TOTAL_VIEWS_MSG", "��������i�");
	define("VOTES_MSG", "������");
	define("TOTAL_VOTES_MSG", "������");
	define("TOTAL_POINTS_MSG", "�����");
	define("VIEW_RESULTS_MSG", "����������� ����������");
	define("PREVIOUS_POLLS_MSG", "��������i ����������");
	define("TOTAL_MSG", "�������");
	define("CLOSED_MSG", "��������");
	define("CLOSE_WINDOW_MSG", "������� �i���");
	define("ASTERISK_MSG", "�i����� (*) - ���� ����'�����i ��� ����������");
	define("PROVIDE_INFO_MSG", "����-����� ������i�� i�������i� � �������� ����i��, ���i� ������i�� ������ '{button_name}'.");
	define("FOUND_ARTICLES_MSG", "�������� <b>{found_records}</b> ������, �� ���������� ������ '<b>{search_string}</b>'");
	define("NO_ARTICLE_MSG", "������ � �������� ID �� ��������");
	define("NO_ARTICLES_MSG", "����� ����� �� ��������");
	define("NOTES_MSG", "�������");
	define("KEYWORDS_MSG", "������ �����");
	define("LINK_URL_MSG", "���������");
	define("DOWNLOAD_URL_MSG", "�������");
	define("SUBSCRIBE_FORM_MSG", "��� ���������� ���� ������, ����-����� ������ ���� ���������� ������ � ����, �� ����������� ����� � ��������� ������ '{button_name}'.");
	define("UNSUBSCRIBE_FORM_MSG", "����-����� ������ ���� ��������� ������ � ����, �� ����������� ����� � ��������� ������  '{button_name}'.");
	define("SUBSCRIBE_LINK_MSG", "ϳ���������");
	define("UNSUBSCRIBE_LINK_MSG", "³���������");
	define("SUBSCRIBED_MSG", "³����! ����� �� �������� �� ���� ������.");
	define("ALREADY_SUBSCRIBED_MSG", "�� ��� �������� �� ���� ������. ������.");
	define("UNSUBSCRIBED_MSG", "�� ���� �������� �� ����� �����. ������.");
	define("UNSUBSCRIBED_ERROR_MSG", "�������, ��� �� �� ������ ������ ������� ������ ����� ������� ���� ���������� ������, ������� �� ��� ���������� �� ����� �����.");
	define("FORGOT_PASSWORD_MSG", "������ ��� ������?");
	define("FORGOT_PASSWORD_DESC", "����-����� ������ ���������� ������, ��� �� ��������������� ��� ����������:");
	define("FORGOT_EMAIL_ERROR_MSG", "�������, ��� �� �� ������ ������ ����� ���������� � ����� ������ ���� �������� ���������� ������.");
	define("FORGOT_EMAIL_SENT_MSG", "���� ����������� ����� ���� ����������� �� ���� ���������� ������.");
	define("RESET_PASSWORD_REQUIRE_MSG", "��������� ���� ��������� ���������");
	define("RESET_PASSWORD_PARAMS_MSG", "������� ��������� �� ���������� ������� � ��� �����");
	define("RESET_PASSWORD_EXPIRY_MSG", "The reset code you supplied has expired. Please request a new code to reset your password.");
	define("RESET_PASSWORD_SAVED_MSG", "��� ����� ������ ���������.");
	define("PRINTER_FRIENDLY_MSG", "����� ��� �����");
	define("PRINT_PAGE_MSG", "��������� �������");
	define("ATTACHMENTS_MSG", "�����������");
	define("VIEW_DETAILS_MSG", "����������� �����");
	define("HTML_MSG", "HTML");
	define("PLAIN_TEXT_MSG", "��������� �����");
	define("META_DATA_MSG", "���� ����");
	define("META_TITLE_MSG", "����� �������");
	define("META_KEYWORDS_MSG", "���� ������ �����");
	define("META_DESCRIPTION_MSG", "���� ����");
	define("FRIENDLY_URL_MSG", "������ ���������");
	define("IMAGES_MSG", "�������");
	define("IMAGE_MSG", "�������");
	define("IMAGE_TINY_MSG", "������");
	define("IMAGE_TINY_ALT_MSG", "�������������� ����� ������");
	define("IMAGE_SMALL_MSG", "��������� �������");
	define("IMAGE_SMALL_DESC", "���������� � ������");
	define("IMAGE_SMALL_ALT_MSG", "������� ���������� �������");
	define("IMAGE_LARGE_MSG", "������� �������");
	define("IMAGE_LARGE_DESC", "���������� � ������� ����");
	define("IMAGE_LARGE_ALT_MSG", "������� �������� �������");
	define("IMAGE_SUPER_MSG", "��������� �������");
	define("IMAGE_SUPER_DESC", "��������� ������� � ������ ����");
	define("IMAGE_POSITION_MSG", "Image Position");
	define("UPLOAD_IMAGE_MSG", "�������� �������");
	define("UPLOAD_FILE_MSG", "����������� ����");
	define("SELECT_IMAGE_MSG", "������� �������");
	define("SELECT_FILE_MSG", "������� ����");
	define("SHOW_BELOW_PRODUCT_IMAGE_MSG", "show image below large product image");
	define("SHOW_IN_SEPARATE_SECTION_MSG", "show image in separate images section");
	define("IS_APPROVED_MSG", "������������");
	define("NOT_APPROVED_MSG", "Not Approved");
	define("IS_ACTIVE_MSG", "��������");
	define("CATEGORY_MSG", "��������");
	define("SELECT_CATEGORY_MSG", "������� ��������");
	define("DESCRIPTION_MSG", "����");
	define("SHORT_DESCRIPTION_MSG", "�������� ����");
	define("FULL_DESCRIPTION_MSG", "������ ����");
	define("HIGHLIGHTS_MSG", "ϳ����������");
	define("SPECIAL_OFFER_MSG", "�������� ��������i�");
	define("ARTICLE_MSG", "Article");
	define("OTHER_MSG", "�����");
	define("WIDTH_MSG", "������");
	define("HEIGHT_MSG", "������");
	define("LENGTH_MSG", "�������");
	define("WEIGHT_MSG", "����");
	define("QUANTITY_MSG", "ʳ������");
	define("CALENDAR_MSG", "��������");
	define("FROM_DATE_MSG", "��������� ����");
	define("TO_DATE_MSG", "ʳ����� ����");
	define("TIME_PERIOD_MSG", "����� ����");
	define("GROUP_BY_MSG", "���������� ��");
	define("BIRTHDAY_MSG", "���� ����������");
	define("BIRTH_DATE_MSG", "���� ����������");
	define("BIRTH_YEAR_MSG", "г� ����������");
	define("BIRTH_MONTH_MSG", "̳���� ����������");
	define("BIRTH_DAY_MSG", "���� ����������");
	define("STEP_NUMBER_MSG", "{current_step} ���� �� {total_steps}");
	define("WHERE_STATUS_IS_MSG", "�� ������ ������");
	define("ID_MSG", "ID");
	define("QTY_MSG", "ʳ������");
	define("TYPE_MSG", "���");
	define("NAME_MSG", "�����");
	define("TITLE_MSG", "�����");
	define("DEFAULT_MSG", "�� ����������");
	define("OPTIONS_MSG", "�����");
	define("EDIT_MSG", "����������");
	define("CONFIRM_DELETE_MSG", "�������� ����� {record_name}?");
	define("DESC_MSG", "����");
	define("ASC_MSG", "�����");
	define("ACTIVE_MSG", "��������");
	define("INACTIVE_MSG", "����������");
	define("EXPIRED_MSG", "����������");
	define("EMOTICONS_MSG", "Emoticons");
	define("EMOTION_ICONS_MSG", "Emotion Icons");
	define("VIEW_MORE_EMOTICONS_MSG", "View more Emoticons");
	define("SITE_NAME_MSG", "Site Name");
	define("SITE_URL_MSG", "������ �����");
	define("SORT_ORDER_MSG", "Sort Order");
	define("NEW_MSG", "New");
	define("USED_MSG", "Used");
	define("REFURBISHED_MSG", "Refurbished");
	define("ADD_NEW_MSG", "������");
	define("SETTINGS_MSG", "������������");
	define("VIEW_MSG", "�����������");
	define("STATUS_MSG", "������");
	define("NONE_MSG", "None");
	define("PRICE_MSG", "�i��");
	define("TEXT_MSG", "Text");
	define("WARNING_MSG", "Warning");
	define("HIDDEN_MSG", "����������");
	define("CODE_MSG", "���");
	define("LANGUAGE_MSG", "����");
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
	define("RENAME_MSG", "�������������");
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
	define("RELEASES_TITLE", "��i ����i�");
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
	define("RELATED_ARTICLES_MSG", "��������� �����");
	define("RELATED_FORUMS_MSG", "��������� ������");

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

	// email �� SMS �����������
	define("EMAIL_NOTIFICATION_MSG", "Email ���������");
	define("EMAIL_NOTIFICATION_ADMIN_MSG", "Email ��������� �������������");
	define("EMAIL_NOTIFICATION_USER_MSG", "Email ��������� �����������");
	define("EMAIL_SEND_ADMIN_MSF", "³�������� ��������� �������������");
	define("EMAIL_SEND_USER_MSG", "³�������� ��������� �����������");
	define("EMAIL_USER_IF_STATUS_MSG", "³�������� ��������� ����������� ����������� ������, ���� ������ �������");
	define("EMAIL_TO_MSG", "����");
	define("EMAIL_TO_USER_DESC", "��������������� ���������� ����� �����������, ���� ��������");
	define("EMAIL_FROM_MSG", "³�");
	define("EMAIL_CC_MSG", "����");
	define("EMAIL_BCC_MSG", "��������� ����");
	define("EMAIL_REPLY_TO_MSG", "³�������");
	define("EMAIL_RETURN_PATH_MSG", "��������� ����");
	define("EMAIL_SUBJECT_MSG", "����");
	define("EMAIL_MESSAGE_TYPE_MSG", "��� �����������");
	define("EMAIL_MESSAGE_MSG", "�����������");
	define("SMS_NOTIFICATION_MSG", "SMS ���������");
	define("SMS_NOTIFICATION_ADMIN_MSG", "������������ SMS ���������");
	define("SMS_NOTIFICATION_USER_MSG", "���������� SMS ���������");
	define("SMS_SEND_ADMIN_MSF", "³�������� SMS ��������� �������������");
	define("SMS_SEND_USER_MSG", "³�������� SMS ��������� �����������");
	define("SMS_USER_IF_STATUS_MSG", "³�������� SMS ��������� ����������� ����������� ������, ���� ������ �������");
	define("SMS_RECIPIENT_MSG", "����");
	define("SMS_RECIPIENT_ADMIN_DESC", "������� �������������");
	define("SMS_RECIPIENT_USER_DESC", "������� ����������� ���� ��������");
	define("SMS_ORIGINATOR_MSG", "����� SMS");
	define("SMS_MESSAGE_MSG", "����� SMS");

	// ����������� �����������
	define("LOGIN_AS_MSG", "�� �����������i ��");
	define("LOGIN_INFO_MSG", "��������� ��� ����� � �������");
	define("ACCESS_HOME_MSG", "��� ������� �� ���� ����i���");
	define("REMEMBER_LOGIN_MSG", "�����'����� �i� ���i� �� ������");
	define("ENTER_LOGIN_MSG", "����i�� ��i� ���i� �� ������ ��� ����������");
	define("LOGIN_PASSWORD_ERROR", "������ ��� ���i� �������i ���i���.");
	define("ACCOUNT_APPROVE_ERROR", "������� ��� ��� ����� �� �� ��� ������������.");
	define("ACCOUNT_EXPIRED_MSG", "��� ������� �� ������");
	define("NEW_PROFILE_ERROR", "�� �� ���� ���� ��� �������� ������ ������.");
	define("EDIT_PROFILE_ERROR", "�� �� ���� ���� ��� ����������� ������ ������.");
	define("CHANGE_DETAILS_MSG", "��i���� ����������i ����i");
	define("CHANGE_DETAILS_DESC", "������i�� ��������� ���� ���� �� ������ ��i���� ��������� i�������i�, �� �� ����� �i� ��� �i������� ����� ������.");
	define("CHANGE_PASSWORD_MSG", "��i���� ������");
	define("CHANGE_PASSWORD_DESC", "��������� ����� ��������� ��� �� ����i��� �� �� ������� ��i���� ��� �������� ������.");
	define("SIGN_UP_MSG", "������������� �����");
	define("MY_ACCOUNT_MSG", "��� ����i���");
	define("NEW_USER_MSG", "����� ����������");
	define("EXISTS_USER_MSG", "I�����i ����������i");
	define("EDIT_PROFILE_MSG", "����������� ������");
	define("PERSONAL_DETAILS_MSG", "����������i ����i");
	define("DELIVERY_DETAILS_MSG", "����i ��� ��������");
	define("SAME_DETAILS_MSG", "���� ���i �������� ���i ���i �� ���� ��������� �������� ��� <br>i����� ����-����� ������i��� �����i �����");
	define("DELIVERY_MSG", "��������");
	define("SUBSCRIBE_CHECKBOX_MSG", "����-����� ��������� �� �����, ���� �� ������ ���������� ���� ������.");
	define("ADDITIONAL_DETAILS_MSG", "�������� �����");
	define("GUEST_MSG", "ó���");

	// ����������� �������� �����������
	define("MY_ADS_MSG", "�� ����������");
	define("MY_ADS_DESC", "���� � ��� � ����, �� �� ������ ������� �� ������ ��������� ���� ������� �� ������ ����. �� ������, ������ �� ������.");
	define("AD_GENERAL_MSG", "������� ���������� �� ��������");
	define("ALL_ADS_MSG", "All Ads");
	define("AD_SELLER_MSG", "���������");
	define("AD_START_MSG", "���� �������");
	define("AD_RUNS_MSG", "��������� �������");
	define("AD_QTY_MSG", "ʳ������");
	define("AD_AVAILABILITY_MSG", "�����������");
	define("AD_COMPARED_MSG", "��������� ��������� ����������");
	define("AD_UPLOAD_MSG", "�������� �������");
	define("AD_DESCRIPTION_MSG", "���� ��������");
	define("AD_SHORT_DESC_MSG", "�������� ����");
	define("AD_FULL_DESC_MSG", "��������� ����");
	define("AD_LOCATION_MSG", "̳��� ������������");
	define("AD_LOCATION_INFO_MSG", "��������� ����������");
	define("AD_PROPERTIES_MSG", "����������");
	define("AD_SPECIFICATION_MSG", "������������");
	define("AD_MORE_IMAGES_MSG", "������ �������");
	define("AD_IMAGE_DESC_MSG", "���� �������");
	define("AD_DELETE_CONFIRM_MSG", "�� ������ �� �������� ��� �������?");
	define("AD_NOT_APPROVED_MSG", "�� ������������");
	define("AD_RUNNING_MSG", "��������");
	define("AD_CLOSED_MSG", "��������");
	define("AD_NOT_STARTED_MSG", "�� ���������");
	define("AD_NEW_ERROR", "�� �� ���� ���� ��� ��������� ������ ����������");
	define("AD_EDIT_ERROR", "�� �� ���� ���� ��� ����������� ����������.");
	define("AD_DELETE_ERROR", "�� �� ���� ���� ��� ��������� ����������.");
	define("NO_ADS_MSG", "�� �������� ������� ���������� � ������ �������.");
	define("NO_AD_MSG", "���������� � ����� �� ����� �� ���� � ��� �������");
	define("FOUND_ADS_MSG", "�������� <b>{found_records}</b> ��������� �� ���������� ������ '<b>{search_string}</b>'.");
	define("AD_OFFER_MESSAGE_MSG", "����������");
	define("AD_OFFER_LOGIN_ERROR", "��� ������� �������������� ����� ���, �� ����������.");
	define("AD_REQUEST_BUTTON", "�������� �����");
	define("AD_SENT_MSG", "���� ���������� ���� ��������.");
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

	// ����������� ������
	define("SEARCH_FOR_MSG", "������");
	define("SEARCH_IN_MSG", "������ �");
	define("SEARCH_TITLE_MSG", "����� ��������");
	define("SEARCH_CODE_MSG", "���");
	define("SEARCH_SHORT_DESC_MSG", "������i� �����i�");
	define("SEARCH_FULL_DESC_MSG", "������� ����i");
	define("SEARCH_CATEGORY_MSG", "������ � �������i�");
	define("SEARCH_MANUFACTURER_MSG", "��������");
	define("SEARCH_SELLER_MSG", "���������");
	define("SEARCH_PRICE_MSG", "�i����� �i������");
	define("SEARCH_WEIGHT_MSG", "�������� ����");
	define("SEARCH_RESULTS_MSG", "���������� ������");
	define("FULL_SITE_SEARCH_MSG", "����� �� �����");

	// ����������� ���������
	define("COMPARE_MSG", "��������");
	define("COMPARE_REMOVE_MSG", "��������");
	define("COMPARE_REMOVE_HELP_MSG", "���������� ��� ��� ��������� �������� � ������� ��������");
	define("COMPARE_MIN_ALLOWED_MSG", "�� ���� ������� ��������� ��� ��������");
	define("COMPARE_MAX_ALLOWED_MSG", "�� �� ������ ������� ����� 5 ��������");
	define("COMPARE_PARAM_ERROR_MSG", "����������� �������� �� ������ ��������");

	// ������� ��������
	define("TELL_FRIEND_TITLE", "������ ��������");
	define("TELL_FRIEND_SUBJECT_MSG", "��� ������� ������� ��� �� ���������");
	define("TELL_FRIEND_DEFAULT_MSG", "����� {friend_name} - � ����� �� ����� ������������ �������� {item_title} �� ����� ���� {item_url}");
	define("TELL_YOUR_NAME_FIELD", "���� ��'�");
	define("TELL_YOUR_EMAIL_FIELD", "���� ���������� ������");
	define("TELL_FRIENDS_NAME_FIELD", "��'� ��������");
	define("TELL_FRIENDS_EMAIL_FIELD", "���������� ������ ��������");
	define("TELL_COMMENT_FIELD", "��������");
	define("TELL_FRIEND_PRIVACY_NOTE_MSG", "�������: �� �� ������ �������� �� ��������������� ���� ���������� ������ ��� ������ ������ �������� ��� ������ ����� �����");
	define("TELL_SENT_MSG", "���� ����������� ���� ������ ����������!<br>������!");
	define("TELL_FRIEND_MESSAGE_MSG", "Thought you might be interested in seeing the {item_title} at {item_url}\\n\\n{user_name} left you a note:\\n{user_comment}");
	define("TELL_FRIEND_PARAM_MSG", "Introduce a friend' URL");
	define("TELL_FRIEND_PARAM_DESC", "adds a friend's URL parameter to a 'Tell a Friend' link if it exists for a user");
	define("FRIEND_COOKIE_EXPIRES_MSG", "Friend Cookie Expires");

	define("CONTACT_US_TITLE", "��������� ��'����");
	define("CONTACT_USER_NAME_FIELD", "���� i�'�");
	define("CONTACT_USER_EMAIL_FIELD", "���� ������� ������");
	define("CONTACT_SUMMARY_FIELD", "���������");
	define("CONTACT_DESCRIPTION_FIELD", "����");
	define("CONTACT_REQUEST_SENT_MSG", "��� ����� ��������");

	// ������
	define("GO_BUTTON", "���i");
	define("CONTINUE_BUTTON", "���i");
	define("BACK_BUTTON", "�����");
	define("NEXT_BUTTON", "����������");
	define("PREV_BUTTON", "�����");
	define("SIGN_IN_BUTTON", "��i���");
	define("LOGIN_BUTTON", "��i���");
	define("LOGOUT_BUTTON", "�����");
	define("SEARCH_BUTTON", "�����");
	define("RATE_IT_BUTTON", "��i����");
	define("ADD_BUTTON", "������");
	define("UPDATE_BUTTON", "�������");
	define("APPLY_BUTTON", "�����������");
	define("REGISTER_BUTTON", "��������������");
	define("VOTE_BUTTON", "����������");
	define("CANCEL_BUTTON", "���������");
	define("CLEAR_BUTTON", "��������");
	define("RESET_BUTTON", "��������� ��i��");
	define("DELETE_BUTTON", "��������");
	define("DELETE_ALL_BUTTON", "Delete All");
	define("SUBSCRIBE_BUTTON", "ϳ���������");
	define("UNSUBSCRIBE_BUTTON", "³���������");
	define("SUBMIT_BUTTON", "��������");
	define("UPLOAD_BUTTON", "��������");
	define("SEND_BUTTON", "��������");
	define("PREVIEW_BUTTON", "��������");
	define("FILTER_BUTTON", "Գ����");
	define("DOWNLOAD_BUTTON", "�������");
	define("REMOVE_BUTTON", "��������");
	define("EDIT_BUTTON", "����������");

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

	// ����
	define("LOGIN_FIELD", "���i�");
	define("PASSWORD_FIELD", "������");
	define("CONFIRM_PASS_FIELD", "�i����������� ������");
	define("NEW_PASS_FIELD", "����� ������");
	define("CURRENT_PASS_FIELD", "�������� ������");
	define("FIRST_NAME_FIELD", "I�'�");
	define("LAST_NAME_FIELD", "��i�����");
	define("NICKNAME_FIELD", "�������i�");
	define("PERSONAL_IMAGE_FIELD", "��� �������");
	define("COMPANY_SELECT_FIELD", "�������");
	define("SELECT_COMPANY_MSG", "������� ��������");
	define("COMPANY_NAME_FIELD", "����� ������i�");
	define("EMAIL_FIELD", "���������� ������");
	define("STREET_FIRST_FIELD", "����� �����i 1");
	define("STREET_SECOND_FIELD", "����� �����i 2");
	define("CITY_FIELD", "̳���");
	define("PROVINCE_FIELD", "�������");
	define("SELECT_STATE_MSG", "Select State");
	define("STATE_FIELD", "����");
	define("ZIP_FIELD", "�������� i�����");
	define("SELECT_COUNTRY_MSG", "������� �����");
	define("COUNTRY_FIELD", "�����");
	define("PHONE_FIELD", "�������");
	define("DAYTIME_PHONE_FIELD", "������ �������");
	define("EVENING_PHONE_FIELD", "���i��i� �������");
	define("CELL_PHONE_FIELD", "���i����� �������");
	define("FAX_FIELD", "����");
	define("VALIDATION_CODE_FIELD", "��� ������������");
	define("AFFILIATE_CODE_FIELD", "��� �����������");
	define("AFFILIATE_CODE_HELP_MSG", "�������������� ��������� URL {affiliate_url} ��� ��������� ������������� ��������� �� ��� ����");
	define("PAYPAL_ACCOUNT_FIELD", "PayPal �������");
	define("TAX_ID_FIELD", "���������� �����");
	define("MSN_ACCOUNT_FIELD", "MSN �������");
	define("ICQ_NUMBER_FIELD", "����� ICQ");
	define("USER_SITE_URL_FIELD", "URL ����� �����������");
	define("HIDDEN_STATUS_FIELD", "���������� ������");
	define("HIDE_MY_ONLINE_STATUS_MSG", "�� ���������� �� ������ ������");
	define("SUMMARY_MSG", "���������");

	// ����������� ��� ���������� ������
	define("NO_RECORDS_MSG", "�� �������� ������� ������");
	define("NO_EVENTS_MSG", "����� ���i� �� ��������");
	define("NO_QUESTIONS_MSG", "������� ������� �� ��������");
	define("NO_NEWS_MSG", "����� ������ �� ��������");
	define("NO_POLLS_MSG", "������� ���������� �� ��������");

	// SMS �����������
	define("SMS_TITLE", "SMS");
	define("SMS_TEST_TITLE", "������� SMS");
	define("SMS_TEST_DESC", "���� �����, ������ ����� ����� ��������� � ��������� 'SEND_BUTTON', ��� �������� ������� �����������");
	define("INVALID_CELL_PHONE", "������� ����� ���������");

	define("ARTICLE_RELATED_PRODUCTS_TITLE", "�������� �� ���������� �� �����");
	define("CATEGORY_RELATED_PRODUCTS_TITLE", "�������� �� ���������� �� �������");
	define("SELECT_TYPE_MSG", "�����i�� ��� ������");
	define("OFFER_PRICE_MSG", "ֳ�� ����������");
	define("OFFER_MESSAGE_MSG", "����������� ����������");

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
<?php
/*-------------------------------------------------------
*
*	Plugin "miniMarket"
*	Author: Stepanov Mark (nikto)
*	Official site: http://altocms.ru/profile/nikto/
*	Contact e-mail: markus1024@yandex.ru
*
---------------------------------------------------------
*/

/**
 * Русский языковой файл.
 * Содержит все текстовки плагина.
 */
return array(
	/**
	 * Админка. Атрибуты
	 */
	'admin_attributes' => 'Атрибуты',
	'admin_attribut_add' => 'Добавить новый атрибут',
	'admin_attribut_adding' => 'Добавление нового атрибута',
	'admin_attribut_adding_name_example' => 'Например, <b>Монитор</b>',
	'admin_attribut_adding_name_error' => 'Имя атрибута может быть от 2 до 50 символов',
	'admin_attribut_adding_description_error' => 'Описание не должно превышать 2000 символов',
	'admin_attribut_adding_ok' => 'Новый атрибут успешно добавлен',
	'admin_attribut_adding_edit' => 'Успешно отредактировано',
	'admin_attribut_edit_title' => 'Редактирование атрибута',
	'admin_attribut_properties_added' => 'Добавленные свойства',
	'admin_attribut_properties_add' => 'Добавить свойство',
	'admin_attribut_for' => 'для',
	'admin_attribut_detele_confirm' => 'Вы действительно хотите удалить этот атрибут?',
	/**
	 * Админка. Свойства
	 */
	'admin_property_adding_name_example' => 'Например, <b>17 дюймов</b>',
	'admin_property_adding_name_error' => 'Имя свойства может быть от 2 до 50 символов',
	'admin_property_adding_description_error' => 'Описание не должно превышать 2000 символов',
	'admin_property_adding_title' => 'Добавление нового свойства',
	'admin_property_edit_title' => 'Редактирование свойства',
	'admin_property_detele_confirm' => 'Вы действительно хотите удалить это свойство?',
	/**
	 * Админка. Настройки
	 */
	'admin_settings' => 'Настройки магазина',
	'admin_settings_base' => 'Основные',
	'admin_settings_base_cart_currency' => 'Валюта корзины',
	'admin_settings_base_cart_currency_example' => 'Валюта, в которой будут отображаться товары в корзине, независимо от валюты самого товара. В этой же валюте будет происходить оплата',
	'admin_settings_base_currency_null' => 'Нет ни одной валюты',
	'admin_settings_base_default_currency' => 'Валюта по умолчанию',
	'admin_settings_base_default_currency_example' => 'Валюта, которая будет использоваться по умолчанию во всех случаях, где есть выбор валюты из списка',
	'admin_settings_base_get_default_currency_error' => 'Валюта, выбранная в разделе "По умолчанию", не существует',
	'admin_settings_base_get_cart_currency_error' => 'Валюта, выбранная в разделе "Валюта корзины", не существует',
	/**
	 * Админка. Категории атрибутов
	 */
	'admin_attributes_category_detele_confirm' => 'Вы действительно хотите удалить данную категорию атрибутов?',
	'admin_attributes_category' => 'Категории атрибутов',
	'admin_attributes_category_add' => 'Добавить новую категорию атрибутов',
	'admin_attributes_category_edit_title' => 'Редактирование категории атрибутов',
	'admin_attributes_category_adding' => 'Добавление категории атрибутов',
	'admin_attributes_category_adding_name_example' => 'Например, <b>Габариты и вес</b>',
	'admin_attributes_category_adding_name_error' => 'Имя категории атрибутов может быть от 2 до 50 символов',
	'admin_attributes_category_adding_ok' => 'Категория атрибутов успешно добавлена',
	'admin_attributes_category_adding_edit' => 'Категория атрибутов успешно отредактирована',
	/**
	 * Товар
	 */
	'product' => 'Товар',
	'product_not' => 'Тут пока нет ни одного товара',
	'product_characteristics' => 'Характеристики',
	'product_technical_characteristics' => 'Технические характеристики',
	'product_features' => 'Особенности',
	'product_buy' => 'Купить',
	'product_min' => 'товар',
	'product_product_editing' => 'Редактирование товара',
	'product_edit_title' => 'Редактирование',
	'product_toggle_images' => 'Добавить изображения',
	'product_delete_confirm' => 'Вы действительно хотите удалить товар?',
	'product_adding_weight' => 'Вес',
	'product_adding_weight_notice' => 'Необходим для расчета доставки. Указывается в граммах. Например: 2450',
	'product_adding_show' => 'Показывать в магазине',
	'product_adding_show_notice' => 'Если отметить эту галку, то товар будет отображаться в магазине.',
	'product_adding_in_stock' => 'Товар в наличии',
	'product_adding_in_stock_notice' => 'Если отметить эту галку, то данный товар можно будет купить.',
	'product_adding_title' => 'Название товара',
	'product_adding_category' => 'Категория',
	'product_adding_category_select_default' => 'Нет',
	'product_adding_attributs' => 'Атрибуты',
	'product_adding_attribut_selected' => 'Выберите нужный атрибут',
	'product_adding_attribut_select_property' => 'Выберите нужное свойство',
	'product_adding_characteristics' => 'Характеристики для краткого описания',
	'product_adding_characteristics_notice' => 'Характеристики нужно разделять запятой. Например: Экран 10", HDD 320 ГБ, Intel X3600',
	'product_adding_features' => 'Особенности товара',
	'product_adding_features_notice' => 'Особенности нужно разделять запятой. Например: Для девушки, Для линуксоида, Без ОС',
	'product_adding_submit_publish' => 'Опубликовать',
	'product_adding_brand' => 'Бренд',
	'product_adding_brand_select_default' => 'Нет',
	'product_adding_url_error' => 'URL может быть от 2 до 200 символов, только латиница, без пробелов.',
	'product_adding_brand_error' => 'Такого бренда не существует',
	'product_adding_category_error' => 'Такой категории не существует',
	'product_adding_manufacturer_code' => 'Код производителя',
	'product_adding_price' => 'Цена',
	'product_adding_currency' => 'Валюта',
	'product_adding_currency_null' => 'Необходимо создать минимум одну валюту',
	'product_adding_currency_error' => 'Необходимо выбрать валюту',
	'product_adding_currency_default' => 'по умолчанию',
	'product_adding_url_double_error' => 'Товар с таким URL уже существует',
	/**
	 * Товар. Фотосет
	 */
	'product_photoset_is_preview' => 'Отмечено как превью к товару',
	'product_photoset_photo_delete' => 'Удалить',
	'product_photoset_mark_as_preview' => 'Отметить как превью',
	'product_photoset_photo_delete_confirm' => 'Удалить изображение?',
	'product_photoset_upload_choose' => 'Загрузить изображение',
	'product_photoset_error_too_much_photos' => 'Товар может содержать не более %%MAX%% изображений',
	'product_photoset_error_bad_filesize' => 'Размер изображения должен быть не более %%MAX%% Кб',
	'product_photoset_photo_added' => 'Изображение добавлено',
	'product_photoset_photo_deleted' => 'Изображение удалено',
	'product_photoset_error_size' => 'У изображения слишком большое разрешение',
	'product_photoset_choose_image' => 'Выберите изображение для загрузки',
	'product_photoset_upload_close' => 'Закрыть',
	'product_photoset_upload_title' => 'Загрузка изображений',
	'product_photoset_upload_rules' => 'Доступна загрузка изображений в формат JPG, PNG, GIF<br />Размер изображений не должен превышать %%SIZE%% Kб<br />Максимальное число загружаемых изображений: %%COUNT%%',
	/**
	 * Админка. Категории
	 */
	'admin_categories' => 'Категории',
	'admin_category_add' => 'Добавить категорию',
	'admin_category_add_title' => 'Добавление новой категории',
	'admin_category_adding_name_example' => 'Например, <b>Ноутбуки</b>',
	'admin_category_adding_name_error' => 'Имя категории может быть от 2 до 50 символов',
	'admin_category_adding_url_example' => 'Например, <b>notebook</b>',
	'admin_category_adding_url_error' => 'Поле служебного названия (URL) может быть от 1 до 50 символов, только латиница, без пробелов. Также адрес не может совпадать с существующими адресами экшенов',
	'admin_category_adding_select_double_error' => 'Категория с таким URL уже существует у данной родительской категории',
	'admin_category_adding_parent' => 'Родительская категория',
	'admin_category_adding_select_example' => 'Выберите родительскую категорию',
	'admin_category_adding_select_default' => 'Нет',
	'admin_category_adding_description_error' => 'Описание не должно превышать 2000 символов',
	'admin_category_adding_select_error' => 'Такой родительской категории не существует',
	'admin_category_adding_select_parent_error' => 'Категория не может ссылаться сама на себя',
	'admin_category_add_ok' => 'Новая категория успешно добавлена',
	'admin_category_edit_ok' => 'Категория успешно отредактирована',
	'admin_category_edit_title' => 'Редактирование категории',
	'admin_category_detele_confirm' => 'Внимание! Кроме данной категории, так же буду удалены все вложенные в нее подкатегории. Продолжить?',
	'admin_category_attribut' => 'Категория',
	/**
	 * Админка. Бренды
	 */
	'admin_brands' => 'Бренды',
	'admin_brans_add_ok' => 'Новый бренд успешно добавлен',
	'admin_brans_edit_ok' => 'Бренд успешно отредактирован',
	'admin_brand_add' => 'Добавить бренд',
	'admin_brand_adding_name_example' => 'Например, <b>ASUS</b>',
	'admin_brand_adding_name_error' => 'Имя бренда может быть от 2 до 50 символов',
	'admin_brand_adding_url_example' => 'Например, <b>asus</b>',
	'admin_brand_adding_url_error' => 'Поле служебного названия (URL) может быть от 1 до 50 символов, только латиница, без пробелов. Также адрес не может совпадать с существующими адресами экшенов',
	'admin_brand_adding_description_error' => 'Описание не должно превышать 4000 символов',
	'admin_brand_adding_double_error' => 'Бренд с таким URL уже существует',
	'admin_brand_detele_confirm' => 'Вы действительно хотите удалить этот бренд?',
	'admin_brand_add_title' => 'Добавление нового бренда',
	'admin_brand_edit_title' => 'Редактирование бренда',
	/**
	 * Админка. Заказы
	 */
	'admin_orders' => 'Заказы',
	'admin_order_number' => 'ID',
	'admin_order_user_data' => 'Данные клиента',
	'admin_order_delivery_service' => 'Способ доставки',
	'admin_order_pay_system' => 'Способ оплаты',
	'admin_order_date' => 'Дата',
	'admin_order_time_order_init' => 'Время создания заказа',
	'admin_order_time_selected_pay_system' => 'Время выбора системы оплаты',
	'admin_order_time_payment_success' => 'Время оплаты заказа',
	'admin_order_status' => 'Статус',
	'admin_order_detele_confirm' => 'Вы действительно хотите удалить данный заказ?',
	'admin_order_remove' => 'Удалить',
	'admin_order_edit_title' => 'Редактирование заказа',
	'admin_order_client_name' => 'Имя',
	'admin_order_information' => 'Информация о заказе',
	'admin_order_client_index' => 'Индекс',
	'admin_order_client_address' => 'Адрес',
	'admin_order_client_phone' => 'Телефон',
	'admin_order_client_comment' => 'Комментарий',
	'admin_order_city' => 'Город',
	'admin_order_delivery' => 'Доставка',
	'admin_order_pay' => 'Оплата',
	'admin_order_select_delivery' => 'Выберите службу доставки',
	'admin_order_select_pay' => 'Выберите систему оплаты',
	'admin_order_delivery_time' => 'Сроки доставки (дней)',
	'admin_order_delivery_time_from' => 'от',
	'admin_order_delivery_time_to' => 'до',
	'admin_order_products' => 'Товары',
	'admin_order_purchase' => 'Покупка',
	'admin_order_count' => 'Количество',
	'admin_order_cost' => 'Стоимость',
	'admin_order_cart_sum' => 'Стоимость товаров',
	'admin_order_delivery_service_sum' => 'Стоимость доставки',
	'admin_order_status_label' => 'Статус',
	'admin_order_status_formation' => 'Формирование',
	'admin_order_status_adopted' => 'Принят, ожидается оплата',
	'admin_order_status_paid' => 'Оплачен, ожидает доставки',
	'admin_order_status_ok' => 'Выполнен',
	'admin_order_payment_sum' => 'Сумма к оплате',
	'admin_order_button_order_paid' => 'Заказ оплачен',
	'admin_order_button_order_delivered' => 'Заказ доставлен',
	/**
	 * Админка. Службы доставки
	 */
	'admin_delivery_services' => 'Службы доставки',
	'admin_delivery_service_add' => 'Добавить новую службу доставки',
	'admin_delivery_service_adding' => 'Добавление новой службы доставки',
	'admin_delivery_service_adding_name_example' => 'Например, <b>Доставка курьером</b>',
	'admin_delivery_service_adding_activation' => 'Активная',
	'admin_delivery_service_adding_location_gropus' => 'Группы местоположений',
	'admin_delivery_service_adding_delivery_time' => 'Сроки доставки (дней)',
	'admin_delivery_service_adding_from' => 'От',
	'admin_delivery_service_adding_to' => 'До',
	'admin_delivery_service_adding_weight' => 'Вес (кг)',
	'admin_delivery_service_adding_order_value' => 'Стоимость заказа',
	'admin_delivery_service_adding_processing_costs' => 'Взнос за обработку заказа',
	'admin_delivery_service_adding_cost_calculation' => 'Расчет стоимости заказа',
	'admin_delivery_service_adding_cost_calculation_error' => 'Выберите форму расчета стоимости заказа',
	'admin_delivery_service_adding_cost_calculation_all_order' => 'За весь заказ',
	'admin_delivery_service_adding_cost_calculation_each_item' => 'За каждый товар',
	'admin_delivery_service_adding_cost_calculation_weight' => 'По весу (указывается стоимость за 1 кг.)',
	'admin_delivery_service_adding_cost' => 'Стоимость',
	'admin_delivery_service_adding_cost_example' => 'Например, <b>2.35</b>',
	'admin_delivery_service_adding_location_group_error' => 'Необходимо выбрать хотя бы одну группу местоположений',
	'admin_delivery_service_adding_pay_system_error' => 'Необходимо выбрать хотя бы одну систему оплаты',
	'admin_delivery_service_adding_pay_systems' => 'Системы оплаты',
	'admin_delivery_service_adding_currency' => 'Валюта',
	'admin_delivery_service_adding_currency_null' => 'Необходимо создать минимум одну валюту',
	'admin_delivery_service_adding_currency_error' => 'Необходимо выбрать валюту',
	'admin_delivery_service_adding_currency_default' => 'по умолчанию',
	'admin_delivery_service_edit_title' => 'Редактирование службы доставки',
	'admin_delivery_service_detele_confirm' => 'Вы действительно хотите удалить данную службу доставки?',
	'admin_delivery_service_editing' => 'Редактирование службы доставки',
	'admin_delivery_service_tunable' => 'Настраиваемые',
	'admin_delivery_service_automatic' => 'Автоматические',
	/**
	 * Админка. Службы доставки. Почта России
	 */
	'admin_delivery_service_postofrussia_edit_title' => 'Редактирование службы доставки: Почта России',
	'admin_delivery_service_postofrussia_field_1_error' => 'Поле #1 должно быть от 2 до 50 символов',
	'admin_delivery_service_postofrussia_adding_activation' => 'Активировать',
	'admin_delivery_service_postofrussia_adding_location_gropus' => 'Группы местоположений',
	'admin_delivery_service_postofrussia_adding_pay_systems' => 'Системы оплаты',
	'admin_delivery_service_postofrussia_adding_field_1' => 'Поле #1',
	'admin_delivery_service_postofrussia_adding_currency_error' => 'Необходимо создать валюту RUB',
	/**
	 * Админка. Системы оплаты
	 */
	'admin_pay_systems' => 'Системы оплаты',
	'admin_pay_system_edit_title' => 'Редактирование системы оплаты',
	/**
	 * Админка. Системы оплаты. Наличными
	 */
	'admin_pay_system_cash_edit_title' => 'Редактирование системы оплаты: Наличными',
	'admin_pay_system_cash_activation' => 'Активировать',
	'admin_pay_system_cash_name_error' => 'Название системы оплаты должно быть от 2 до 50 символов',
	/**
	 * Админка. Системы оплаты. Robokassa
	 */
	'admin_pay_system_robokassa_edit_title' => 'Редактирование системы оплаты: ROBOKASSA',
	'admin_pay_system_robokassa_activation' => 'Активировать',
	'admin_pay_system_robokassa_name_error' => 'Название системы оплаты должно быть от 2 до 50 символов',
	'admin_pay_system_robokassa_login' => 'Логин',
	'admin_pay_system_robokassa_password_1' => 'Пароль #1',
	'admin_pay_system_robokassa_password_2' => 'Пароль #2',
	'admin_pay_system_robokassa_login_error' => 'Логин должен быть от 2 до 50 символов',
	'admin_pay_system_robokassa_password_1_error' => 'Пароль #1 должен быть от 2 до 50 символов',
	'admin_pay_system_robokassa_password_2_error' => 'Пароль #2 должен быть от 2 до 50 символов',
	'admin_pay_system_robokassa_test_mode' => 'Тестовый режим',
	/**
	 * Админка. Группы местоположений
	 */
	'admin_location_groups' => 'Группы местоположений',
	'admin_location_group_add' => 'Добавить новую группу местоположений',
	'admin_location_group_confirm' => 'Вы действительно хотите удалить данную группу местоположений?',
	'admin_location_group_adding' => 'Добавление новой группы местоположений',
	'admin_location_group_editing' => 'Редактирование группы местоположений',
	'admin_location_group_adding_name' => 'Название',
	'admin_location_group_adding_name_example' => 'Например, <b>Группа 1</b>',
	'admin_location_group_adding_location' => 'Местоположения, входящие в группу',
	'admin_location_group_adding_name_error' => 'Название группы местоположений должно быть от 2 до 50 символов',
	'admin_location_group_adding_sity_id_error' => 'Не выбрано ни одно местоположение',
	/**
	 * Админка. Валюты
	 */
	'admin_currency' => 'Валюта',
	'admin_currency_adding_key_example' => 'Например: <b>USD</b>',
	'admin_currency_adding_key_error' => 'Название валюты должно быть от 2 до 50 символов, только латиница и без пробелов',
	'admin_currency_adding_key_double_error' => 'Валюта с таким названием уже существует',
	'admin_currency_adding_nominal' => 'Номинал',
	'admin_currency_adding_nominal_example' => 'Например: <b>1</b>',
	'admin_currency_adding_nominal_error' => 'Номинал должен быть целым числом. Может быть от 1 до 99999',
	'admin_currency_adding_course' => 'Курс',
	'admin_currency_adding_course_example' => 'Например: <b>30.33</b>',
	'admin_currency_adding_course_error' => 'Курс может быть от 1 до 99999999',
	'admin_currency_adding_format' => 'Формат валюты при выводе',
	'admin_currency_adding_format_example' => 'Например: <b>#$</b>, где знак <b>#</b> будет заменен на цену товара',
	'admin_currency_adding_format_error' => 'Не верный формат валюты',
	'admin_currency_adding_decimal_places' => 'Количество знаков в дробной части при выводе',
	'admin_currency_adding_decimal_places_error' => 'Количество знаков в дробной части может быть от 0 до 9',
	'admin_currency_add' => 'Добавить новую валюту',
	'admin_currency_adding' => 'Добавление новой валюты',
	'admin_currency_editing' => 'Редактирование валюты',
	'admin_currency_detele_confirm' => 'Вы действительно хотите удалить данную валюту?',
	'admin_currency_course' => 'Курс',
	'admin_currency_nominal' => 'Номинал',
	'admin_currency_delete_product_error' => 'Невозможно удалить валюту, так как существуют товары, ее использующие',
	'admin_currency_delete_delivery_service_error' => 'Невозможно удалить валюту, так как существуют службы доставки, ее использующие',
	'admin_currency_delete_payment_error' => 'Невозможно удалить валюту, так как существуют счета, ее использующие',
	/**
	 * Корзина
	 */
	'cart' => 'Корзина',
	'cart_purchase' => 'Покупка',
	'cart_count' => 'Количество',
	'cart_price' => 'Стоимость',
	'cart_delete' => 'Удалить',
	'cart_summ' => 'Общая стоимость',
	'cart_no' => 'Ваша корзина пуста, так как вы пока не выбрали ни один товар',
	'cart_next' => 'Далее',
	/**
	 * Заказ. Ввод адреса
	 */
	'order_next' => 'Далее',
	'order_address_name' => 'Имя и фамилия',
	'order_address_city' => 'Страна и город',
	'order_address_index' => 'Почтовый индекс',
	'order_address_address' => 'Адрес доставки',
	'order_address_phone' => 'Телефон',
	'order_address_comment' => 'Комментарий по доставке',
	'order_address_city_error' => 'Необходимо выбрать город доставки',
	/**
	 * Заказ. Доставка
	 */
	'order_delivery' => 'Доставка',
	'order_delivery_methods' => 'Способ доставки',
	'order_delivery_table_name' => 'Название',
	'order_delivery_table_count' => 'Количество',
	'order_delivery_table_cost' => 'Стоимость',
	'order_delivery_days_count' => 'Срок в рабочих днях',
	'order_delivery_cost' => 'Стоимость',
	'order_delivery_from' => 'от',
	'order_delivery_to' => 'до',
	'order_delivery_sum' => 'Сумма к оплате',
	'order_delivery_access_error' => 'Нет ни одной доступной службы доставки',
	/**
	 * Заказ. Способы оплаты
	 */
	'order_pay_systems' => 'Способы оплаты',
	'order_pay_systems_access_error' => 'Нет ни одной доступной системы оплаты',
	/**
	 * Заказ. Оплата заказа
	 */
	'order_pay_order' => 'Оплата заказа',
	/**
	 * Заказ. Заказ принят
	 */
	'order_ok_title' => 'Заказ принят',
	'order_ok_message' => 'Ваш заказ принят. Спасибо.',
	/**
	 * Оплата. Выбор системы оплаты
	 */
	'payment_available_pay_systems_error' => 'Нет доступных систем оплаты',
	'payment_available_pay_systems' => 'Способы оплаты',
	/**
	 * Action Payment
	 */
	'action_payment_robox_error' => 'При оплаче счета что-то пошло не так',
	'action_payment_init_title' => 'Оплата счета',
	'action_payment_init_text_order' => 'Заказ #%%order_number%% успешно сформирован',
	'action_payment_init_text_payment' => 'Пожалуйста, оплатите счет #%%payment_number%%',
	'action_payment_init_title_order' => 'Оплата заказа',
	'action_payment_init_text_cash_order' => '
		Спасибо, заказ #%%order_number%% принят. В течении суток ваш заказ будет сформирован, и наш менеджер свяжется с вами.
		<br /><br />
		Оплата будет произведена через наличный расчет.
	',
	'action_payment_init_text_robokassa_order' => '
		Спасибо, заказ #%%order_number%% принят. В течении суток ваш заказ будет сформирован, и наш менеджер свяжется с вами.
		<br /><br />
		Пожалуйста, оплатите заказ через ROBOKASSA.
	',
	'action_payment_init_button_pay' => 'Оплатить',
	'action_payment_init_button_next' => 'Далее',
	'action_payment_success_order_title' => 'Заказ успешно оплачен',
	'action_payment_fail_order_title' => 'Оплата заказа завершилась неудачей',
	/**
	 * Other
	 */
	'shop' => 'Магазин',
	'catalog' => 'Каталог',
	'filter' => 'Фильтр',
	'lover' => 'Любитель',
	'pros' => 'Профи',
	'category_not' => 'Тут пока нет ни одной категории',
	'content_submit' => 'Сохранить',
	'additionally' => 'Дополнительно',
	'save' => 'Сохранить',
	'delete' => 'Удалить',
	'edit' => 'Редактировать',
	'action' => 'Действие',
	'name' => 'Название',
	'description' => 'Описание',
	'url' => 'URL',
	'yes' => 'Да',
	'no' => 'Нет',
	'order_nulled' => 'Отказаться от заказа',
);

?>
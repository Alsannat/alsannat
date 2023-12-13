<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Helper;

// Stores > Configuration > Google Product Ratings Helper
class Config
{
    const XML_PATH_SETTINGS_LOG = 'googleproductratings/settings/log';
    
    const XML_PATH_DATA_FEED_SETTINGS_PUBLISHER_NAME = 'googleproductratings/data_feed_settings/publisher_name';
    const XML_PATH_DATA_FEED_SETTINGS_COLLECTION_METHOD = 'googleproductratings/data_feed_settings/collection_method';
    const XML_PATH_DATA_FEED_SETTINGS_RATING_RANGE_MIN = 'googleproductratings/data_feed_settings/rating_range_min';
    const XML_PATH_DATA_FEED_SETTINGS_RATING_RANGE_MAX = 'googleproductratings/data_feed_settings/rating_range_max';
    const XML_PATH_DATA_FEED_SETTINGS_ANONYMOUS_REVIEWER = 'googleproductratings/data_feed_settings/anonymous_reviewer';

    const XML_PATH_OPTIONS_USE_MINIMAL_CONFIGURATION = 'googleproductratings/options/use_minimal_configuration';
    const XML_PATH_OPTIONS_PRODUCT_SETTINGS_FILTER_UNAVAILABLE = 'googleproductratings/options/product_settings/filter_unavailable';
    const XML_PATH_OPTIONS_PRODUCT_SETTINGS_PRODUCT_GTIN = 'googleproductratings/options/product_settings/product_gtin';
    const XML_PATH_OPTIONS_PRODUCT_SETTINGS_PRODUCT_MPN = 'googleproductratings/options/product_settings/product_mpn';
    const XML_PATH_OPTIONS_PRODUCT_SETTINGS_PRODUCT_SKU = 'googleproductratings/options/product_settings/product_sku';
    const XML_PATH_OPTIONS_PRODUCT_SETTINGS_PRODUCT_BRAND = 'googleproductratings/options/product_settings/product_brand';
    const XML_PATH_OPTIONS_PRODUCT_SETTINGS_PRODUCT_NAME = 'googleproductratings/options/product_settings/product_name';
    
    const XML_PATH_SCHEDULE_CRON = 'googleproductratings/schedule/cron';
    
    const XML_PATH_CRON_JOB_ENABLE_REPORTING = 'googleproductratings/cron_job/enable_reporting';
    const XML_PATH_CRON_JOB_REPORTING_SETTINGS_SENDER_EMAIL = 'googleproductratings/cron_job/reporting_settings/sender_email';
    const XML_PATH_CRON_JOB_REPORTING_SETTINGS_SENDER_NAME = 'googleproductratings/cron_job/reporting_settings/sender_name';
    const XML_PATH_CRON_JOB_REPORTING_SETTINGS_EMAILS = 'googleproductratings/cron_job/reporting_settings/emails';
    const XML_PATH_CRON_JOB_REPORTING_SETTINGS_SUBJECT = 'googleproductratings/cron_job/reporting_settings/subject';
    const MAIL_NOTIFICATION_TEMPLATE = 'wyomind_googleproductratings_cron_report';
    
    const XML_PATH_STORAGE_FILE_PATH = 'googleproductratings/storage/file_path';
    const XML_PATH_STORAGE_FILE_NAME = 'googleproductratings/storage/file_name';
    const XML_PATH_STORAGE_UPDATED_AT = 'googleproductratings/storage/updated_at';
}

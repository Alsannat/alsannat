<?php
/**
 * Created by Magenest
 * User: Pham Quang Hau
 */
namespace Magenest\GiftCard\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_giftcard'))
            ->addColumn(
                'giftcard_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )  ->addColumn(
                'giftcard_purchased_id',
                Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false],
                'ID'
            )
            ->addColumn(
                'code',
                Table::TYPE_TEXT,
                null,
                [],
                'Code'
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                [],
                'Status'
            )
            ->addColumn(
                'is_sent',
                Table::TYPE_SMALLINT,
                null,
                [],
                'Whether gift card is sent, 1 is sent'
            )
            ->addColumn(
                'date_created',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Date Created'
            )
            ->addColumn(
                'date_updated',
                Table::TYPE_TIMESTAMP,
                null,
                [],
                'Date Updated'
            )
            ->addColumn(
                'date_expired',
                Table::TYPE_DATETIME,
                null,
                [],
                'Date Expired'
            ) ->addColumn(
                'available_date',
                Table::TYPE_DATETIME,
                null,
                [],
                'Date that gift card becomes available to use'
            )
            ->addColumn(
                'website_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true,'default' => 0],
                'Website ID'
            )  ->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true,'default' => 0],
                'Store ID'
            )

            ->addColumn(
                'order_item_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true,'default' => 0],
                'Order Item  ID'
            )
            ->addColumn(
                'balance',
                Table::TYPE_DECIMAL,
                null,
                [],
                'Balance'
            )
            ->addColumn(
                'state',
                Table::TYPE_SMALLINT,
                null,
                [],
                'State'
            )
            ->addColumn(
                'giftcard_image',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Image'
            )  ->addColumn(
                'giftcard_pdf',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Image'
            )
            ->addColumn(
                'sender_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Sender Name'
            )->addColumn(
                'sender_email',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Sender Email'
            )->addColumn(
                'recipient_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Recipient Name'
            )->addColumn(
                'recipient_email',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Recipient Email'
            )->addColumn(
                'headline',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card HeadLine'
            )->addColumn(
                'message',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Message'
            )->addColumn(
                'schedule_send_time',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Schedule Send Time'
            )->addColumn(
                'template',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Template'
            )->addColumn(
                'personal_design',
                Table::TYPE_TEXT,
                null,
                [],
                'Personal design'
            )

            ->setComment('Gift Card');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_giftcard_purchased'))
            ->addColumn(
                'giftcard_purchased_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )

            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                [],
                'Status'
            )
            ->addColumn(
                'date_created',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Date Created'
            )
            ->addColumn(
                'date_updated',
                Table::TYPE_DATE,
                null,
                [],
                'Date Updated'
            )

            ->addColumn(
                'website_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true,'default' => 0],
                'Website ID'
            ) ->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true,'default' => 0],
                'Store ID'
            ) ->addColumn(
                'order_item_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true,'default' => 0],
                'Order Item  ID'
            )
            ->addColumn(
                'amount',
                Table::TYPE_DECIMAL,
                null,
                [],
                'Amount'
            )->addColumn(
                'template',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Template'
            )
            ->addColumn(
                'personal_design',
                Table::TYPE_TEXT,
                null,
                [],
                'Personal design'
            )
            ->addColumn(
                'recipient_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Recipient Email'
            )
            ->addColumn(
                'recipient_email',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Recipient Email'
            )->addColumn(
                'sender_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Sender Name'
            )->addColumn(
                'sender_email',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Sender Email'
            )->addColumn(
                'headline',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card HeadLine'
            )->addColumn(
                'message',
                Table::TYPE_TEXT,
                null,
                [],
                'Gift Card Message'
            )->addColumn(
                'schedule_send_time',
                Table::TYPE_DATE,
                null,
                [],
                'Gift Card Schedule Send Time'
            )->addColumn(
                'expired_after_x_days',
                Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true],
                'Gift Card will be expired after x days'
            )

            ->setComment('Gift Card Purchased');

        $installer->getConnection()->createTable($table);
        $table = $installer->getConnection()->newTable($installer->getTable('magenest_giftcard_template'))
            ->addColumn(
                'template_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['default' => 0],
                'Status'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                null,
                [],
                'Name'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                null,
                [],
                'Title'
            )
            ->addColumn(
                'note',
                Table::TYPE_TEXT,
                null,
                [],
                'Title'
            )
            ->addColumn(
                'design_type',
                Table::TYPE_SMALLINT,
                null,
                [],
                'Design Type'
            )
            ->addColumn(
                'style_color',
                Table::TYPE_TEXT,
                null,
                [],
                'Style Color'
            )
            ->addColumn(
                'text_color',
                Table::TYPE_TEXT,
                null,
                [],
                'Text Color'
            )
            ->addColumn(
                'extra_field',
                Table::TYPE_TEXT,
                null,
                [],
                'Extra Field'
            )
            ->addColumn(
                'background_image',
                Table::TYPE_TEXT,
                null,
                [],
                'Background Image'
            )
            ->addColumn(
                'main_image',
                Table::TYPE_TEXT,
                null,
                [],
                'Main Image'
            )
            ->addColumn(
                'avatar',
                Table::TYPE_TEXT,
                null,
                [],
                'Avatar Image'
            )
            ->addColumn(
                'logo_image',
                Table::TYPE_TEXT,
                null,
                [],
                'Logo Image'
            )
            ->addColumn(
                'additional_info',
                Table::TYPE_TEXT,
                null,
                [],
                'Additional Info'
            )
            ->setComment('Gift Card Template');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_giftcard_temp'))
            ->addColumn(
                'temp_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'data',
                Table::TYPE_TEXT,
                null,
                [],
                'Data'
            )
            ->setComment('Gift Card Temporary Template');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_giftcard_history'))
            ->addColumn(
                'history_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'giftcard_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Giftcard ID'
            )
            ->addColumn(
                'date_created',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Date Created'
            )
            ->addColumn(
                'action',
                Table::TYPE_SMALLINT,
                null,
                [],
                'Action'
            )
            ->addColumn(
                'amount',
                Table::TYPE_DECIMAL,
                null,
                [],
                'Amount'
            )->addColumn(
                'delta',
                Table::TYPE_DECIMAL,
                null,
                [],
                'Delta'
            )
            ->addColumn(
                'note',
                Table::TYPE_TEXT,
                null,
                [],
                'Note'
            )
            ->setComment('Gift Card History');
        $installer->getConnection()->createTable($table);

        //the table that hold the product and background relationship
        $table = $installer->getConnection()->newTable($installer->getTable('magenest_giftcard_background'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                null,
                [],
                'Title'
            )
            ->addColumn(
                'file',
                Table::TYPE_TEXT,
                null,
                [],
                'Path of the image'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                [],
                'Action'
            )
            ->addColumn(
                'size',
                Table::TYPE_INTEGER,
                null,
                [],
                'Size'
            ) ->addColumn(
                'name',
                Table::TYPE_TEXT,
                null,
                [],
                'Name'
            )

            ->addColumn(
                'note',
                Table::TYPE_TEXT,
                null,
                [],
                'Note'
            )
            ->setComment('Gift Card Background');
        $installer->getConnection()->createTable($table);

             //the table that hold the product and background relationship
        $table = $installer->getConnection()->newTable($installer->getTable('magenest_giftcard_art'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                null,
                [],
                'Title'
            )
            ->addColumn(
                'file',
                Table::TYPE_TEXT,
                null,
                [],
                'Path of the image'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                [],
                'Action'
            )
            ->addColumn(
                'size',
                Table::TYPE_INTEGER,
                null,
                [],
                'Size'
            ) ->addColumn(
                'name',
                Table::TYPE_TEXT,
                null,
                [],
                'Name'
            )
            ->addColumn(
                'note',
                Table::TYPE_TEXT,
                null,
                [],
                'Note'
            )
            ->setComment('Gift Card Art');

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}

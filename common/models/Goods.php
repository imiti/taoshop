<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $id
 * @property integer $cat_id
 * @property string $goods_sn
 * @property string $goods_name
 * @property string $goods_name_style
 * @property integer $click_count
 * @property integer $brand_id
 * @property integer $goods_number
 * @property string $goods_weight
 * @property string $market_price
 * @property string $shop_price
 * @property string $promote_price
 * @property integer $promote_start_date
 * @property integer $promote_end_date
 * @property integer $warn_number
 * @property string $keywords
 * @property string $goods_brief
 * @property string $goods_desc
 * @property string $goods_img
 * @property integer $is_real
 * @property string $extension_code
 * @property integer $is_on_sale
 * @property integer $is_alone_sale
 * @property integer $is_shipping
 * @property integer $is_delete
 * @property integer $is_best
 * @property integer $is_new
 * @property integer $is_host
 * @property integer $is_promote
 * @property integer $bonus_type_id
 * @property integer $last_update
 * @property integer $goods_type
 * @property string $seller_note
 * @property integer $give_integral
 * @property integer $rank_integral
 * @property integer $suppliers_id
 * @property integer $is_check
 * @property integer $sort_order
 * @property integer $create_time
 */
class Goods extends \common\component\ActiveRecordD
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'goods_sn', 'goods_name', 'click_count', 'goods_desc', 'goods_img'], 'required'],
            [['cat_id', 'click_count', 'brand_id', 'goods_number', 'promote_start_date', 'promote_end_date', 'warn_number', 'is_real', 'is_on_sale', 'is_alone_sale', 'is_shipping', 'is_delete', 'is_best', 'is_new', 'is_host', 'is_promote', 'bonus_type_id', 'last_update', 'goods_type', 'give_integral', 'rank_integral', 'suppliers_id', 'is_check', 'sort_order', 'create_time'], 'integer'],
            [['goods_weight', 'market_price', 'shop_price', 'promote_price'], 'number'],
            [['goods_desc'], 'string'],
            [['goods_sn'], 'string', 'max' => 32],
            [['goods_name'], 'string', 'max' => 127],
            [['goods_name_style'], 'string', 'max' => 64],
            [['keywords', 'goods_brief', 'goods_img', 'seller_note'], 'string', 'max' => 255],
            [['extension_code'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cat_id' => Yii::t('app', 'Cat ID'),
            'goods_sn' => Yii::t('app', 'Goods Sn'),
            'goods_name' => Yii::t('app', 'Goods Name'),
            'goods_name_style' => Yii::t('app', 'Goods Name Style'),
            'click_count' => Yii::t('app', 'Click Count'),
            'brand_id' => Yii::t('app', 'Brand ID'),
            'goods_number' => Yii::t('app', 'Goods Number'),
            'goods_weight' => Yii::t('app', 'Goods Weight'),
            'market_price' => Yii::t('app', 'Market Price'),
            'shop_price' => Yii::t('app', 'Shop Price'),
            'promote_price' => Yii::t('app', 'Promote Price'),
            'promote_start_date' => Yii::t('app', 'Promote Start Date'),
            'promote_end_date' => Yii::t('app', 'Promote End Date'),
            'warn_number' => Yii::t('app', 'Warn Number'),
            'keywords' => Yii::t('app', 'Keywords'),
            'goods_brief' => Yii::t('app', 'Goods Brief'),
            'goods_desc' => Yii::t('app', 'Goods Desc'),
            'goods_img' => Yii::t('app', 'Goods Img'),
            'is_real' => Yii::t('app', 'Is Real'),
            'extension_code' => Yii::t('app', 'Extension Code'),
            'is_on_sale' => Yii::t('app', 'Is On Sale'),
            'is_alone_sale' => Yii::t('app', 'Is Alone Sale'),
            'is_shipping' => Yii::t('app', 'Is Shipping'),
            'is_delete' => Yii::t('app', 'Is Delete'),
            'is_best' => Yii::t('app', 'Is Best'),
            'is_new' => Yii::t('app', 'Is New'),
            'is_host' => Yii::t('app', 'Is Host'),
            'is_promote' => Yii::t('app', 'Is Promote'),
            'bonus_type_id' => Yii::t('app', 'Bonus Type ID'),
            'last_update' => Yii::t('app', 'Last Update'),
            'goods_type' => Yii::t('app', 'Goods Type'),
            'seller_note' => Yii::t('app', 'Seller Note'),
            'give_integral' => Yii::t('app', 'Give Integral'),
            'rank_integral' => Yii::t('app', 'Rank Integral'),
            'suppliers_id' => Yii::t('app', 'Suppliers ID'),
            'is_check' => Yii::t('app', 'Is Check'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
    }
}

<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "member".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_ip
 * @property integer $last_login_time
 * @property string $mobile
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $password;
    public $rePassword;
    public $smsCode;
    public $code;
    public $checked;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email','mobile','rePassword','smsCode'], 'required'],
            [['username'], 'unique'],
            [['mobile'], 'match', 'pattern' => '/^(13|14|15|18|17)[0-9]{9}$/','message' => '手机号错误'],
            ['rePassword','compare','compareAttribute' => 'password'],
          ['smsCode','validateCode'],
           // ['smsCode','compare','compareValue' => Yii::$app->session->get("tel_".$this->mobile)]
            ['code','captcha','captchaAction' => 'user1/captcha']
        ];
    }
    public function validateCode($attribute, $params)
    {
      //  if (!$this->hasErrors()) {

            //把存在Session中的验证码取出来 和当前的对比
            $code=Yii::$app->session->get("tel_".$this->mobile);

            if ($this->smsCode!=$code){
                $this->addError($attribute, '验证码错误');
            }
            /*$user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }*/
       // }
    }
    public function behaviors()
    {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'attributes' =>[
                    self::EVENT_BEFORE_INSERT=>['created_at','updated_at'],
                    self::EVENT_BEFORE_UPDATE=>['updated_at']
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'last_login_ip' => 'IP地址',
            'last_login_time' => '最后登录时间',
            'mobile' => '手机',
            'checked'=>'',
            'password'=>'密码',
            'rePassword'=>'确认密码'
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key===$authKey;
    }
}

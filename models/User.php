<?php



use yii\web\IdentityInterface;


class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    const STATUS_DELETED=0;
    const STATUS_ACTIVE=1;

    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id, 'active' =>
            self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->andWhere(['token' => $token])
            ->andWhere(['>', 'expired_at', time()])
            ->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['login' => $username, 'active'
        => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {

    }
    public function validateAuthKey($authKey)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */


    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()
            ->validatePassword($password, $this->pass);
    }

    public function tokenInfo()
    {
        return [
            'token' => $this->token,
            'expiredAt' => $this->expired_at,
            'fio' => $this->lastname.' '.$this->firstname. '
'.$this->patronymic,
            'roles' => Yii::$app->authManager->
            getRolesByUser($this->user_id)
        ];
    }


    public function logout()
    {
        $this->token = null;
        $this->expired_at = null;
        return $this->save();
    }


    public function generateToken($expire)
    {
        $this->expired_at = $expire;
        $this->token = Yii::$app->security
            ->generateRandomString();
    }




    public function getGender()
    {
        return $this->hasOne(Gender::className(),['gender_id' =>
            'gender_id']);
    }


}

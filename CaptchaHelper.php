<?php

namespace riskivy\captcha;

use yii\captcha\CaptchaAction;
use yii\base\Exception;
use Yii;

class CaptchaHelper extends CaptchaAction
{
    private $code;

    /**
     * CaptchaHelper constructor.
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function generateImage(): string
    {
        $base64 = "data:image/png;base64," . base64_encode($this->renderImage($this->generateCode()));
        Yii::$app->cache->set($this->generateSessionKey($this->generateCode()), $this->generateCode(), 60);
        return $base64;
    }

    /**
     * @return string
     */
    public function generateCode(): string
    {
        if ($this->code) {
            return $this->code;
        }

        return $this->code = $this->generateVerifyCode();
    }

    /**
     * @param string $code
     * @return bool
     * @throws Exception
     */
    public function verify(string $code): bool
    {
        $verify = Yii::$app->cache->get($this->generateSessionKey($code));

        // 删除cache
        Yii::$app->cache->delete($this->generateSessionKey($code));
        
        if ($verify === $code) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    private function generateSessionKey(string $code): string
    {
        return base64_encode(Yii::$app->request->getRemoteIP() . Yii::$app->request->getUserAgent() . $code);
    }
}

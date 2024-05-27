<?php


namespace app\user\private;

use app\core\RestController;
use app\core\router\Response;
use app\exception\InvalidCodeException;
use app\notification\Factory as NotifyFactory;
use app\user\User;
use app\user\Util;
use app\user\Verify;

class ProfileController extends RestController
{
    protected ?User $user;
    protected array $validationParams = [
        'phone' => 'kz-phone-number|required'
    ];
    public function __construct(array $aParams = [])
    {
        parent::__construct($aParams);
        $this->user = User::query()->findOrFail($_SESSION['user_id'] ?? 0);
        if ($this->getParam(0) === 'code') {
            $this->validationParams = ['code' => 'required'];
        }
    }

    public function get(): Response
    {
        return $this->response->setContent([
            'username' => $this->user->username,
            'phone' => $this->user->phone,
            'status' => $this->user->status
        ])->setIsSuccess(true)->setMessage('user data');
    }

    public function post(): Response
    {
        if ($this->getParam(0) === 'code') {
            /** @var Verify $verify */
            $verify = Verify::query()
                ->where('user_id', $_SESSION['user_id'])
                ->firstOrFail();
            if ($verify->verify($this->request->post('code'))) {
                $verify->execute();
                $this->response->setContent([])->setIsSuccess(true)->setMessage('Data successfully refreshed');
            } else {
                throw new InvalidCodeException();
            }
        } else {
            /** @var Verify $verify */
            $verify = Verify::query()
                ->where('user_id', $_SESSION['user_id'])
                ->firstOrNew();
            $data = [
                'username' => $this->request->post('username') ?? $this->user->username,
                'phone' => Util::purifyPhone($this->request->post('phone') ?? $this->user->phone),
                'password' => password_hash($this->request->post('password') ?? $this->user->password, PASSWORD_BCRYPT),
            ];
            $code = random_int(100000, 999999);
            $verify->blank($this->user->id, get_class($this->user), $this->user->id, $data, $code);
            $verify->save();
            $this->user->phone = Util::purifyPhone($this->request->post('phone'));
            NotifyFactory::get('sms')->setRecipient($this->user)->notify($code);
            $this->response->setMessage('Security code sent to ' . $this->user->phone)->setIsSuccess(true);
        }
        return $this->response;
    }
}
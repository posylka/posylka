<?php


namespace app\user\private;

use app\core\enums\UserStatus;
use app\core\RestController;
use app\core\router\Response;
use app\events\Event;
use app\exception\CodeExpiredException;
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

    /**
     * @throws CodeExpiredException|InvalidCodeException
     */
    public function post(): Response
    {
        if ($this->getParam(0) === 'code') {
            /** @var Verify $verify */
            $verify = Verify::query()
                ->where('user_id', $this->user->id)
                ->firstOrFail();
            $event = $this->user->status === UserStatus::NOT_VERIFIED ? 'verify' : 'default';
            if ($verify->verify($this->request->post('code'))) {
                $verify->execute();
                Event::get($event)->trigger($this->user);
                $this->response->setContent([])->setIsSuccess(true)->setMessage('Data successfully refreshed');
            } else {
                throw new InvalidCodeException();
            }
        } else {
            /** @var Verify $verify */
            $verify = Verify::query()
                ->where('user_id', $this->user->id)
                ->firstOrNew();
            $data = [
                'username' => $this->request->post('username') ?? $this->user->username,
                'phone' => Util::purifyPhone($this->request->post('phone') ?? $this->user->phone),
                'password' => password_hash($this->request->post('password') ?? $this->user->password, PASSWORD_BCRYPT),
                'status' => UserStatus::VERIFIED,
            ];
            $code = random_int(100000, 999999);
            $verify->blank($this->user->id, get_class($this->user), $this->user->id, $data, $code);
            $verify->save();
            $this->user->phone = Util::purifyPhone($this->request->post('phone'));
            if (PROD) NotifyFactory::get('sms')->setRecipient($this->user)->notify($code);
            $this->response->setMessage('Security code sent to ' . $this->user->phone)->setIsSuccess(true);
        }
        return $this->response;
    }
}
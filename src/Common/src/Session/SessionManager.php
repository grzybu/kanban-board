<?php

declare(strict_types=1);

namespace Common\Session;

class SessionManager extends \SessionHandler
{
    protected $name;
    protected $cookie;

    public function __construct($name = 'MY_SESSION')
    {
        $this->name = $name;
        $this->cookie = [
            'lifetime' => 0,
            'path' => ini_get('session.cookie_path'),
            'domain' => ini_get('session.cookie_domain'),
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true
        ];
        $this->setup();
    }

    private function setup()
    {
        ini_set('session.use_cookies', 'true');
        ini_set('session.use_only_cookies', 'true');
        session_name($this->name);
        session_set_cookie_params(
            $this->cookie['lifetime'],
            $this->cookie['path'],
            $this->cookie['domain'],
            $this->cookie['secure'],
            $this->cookie['httponly']
        );
    }

    public function start()
    {
        if (session_id() === '') {
            return session_start();
        }
        return false;
    }

    public function forget()
    {
        if (session_id() === '') {
            return false;
        }
        $_SESSION = [];
        setcookie(
            $this->name,
            '',
            time() - 42000,
            $this->cookie['path'],
            $this->cookie['domain'],
            $this->cookie['secure'],
            $this->cookie['httponly']
        );
        return session_destroy();
    }

    public function refresh()
    {
        return session_regenerate_id(true);
    }

    public function isExpired($ttl = 30)
    {
        $last = $_SESSION['_last_activity'] ?? false;
        if ($last !== false && time() - $last > $ttl * 60) {
            return true;
        }
        $_SESSION['_last_activity'] = time();
        return false;
    }

    public function isValid()
    {
        return !$this->isExpired();
    }

    public function get($name)
    {
        $parsed = explode('.', $name);
        $result = $_SESSION;
        while ($parsed) {
            $next = array_shift($parsed);
            if (isset($result[$next])) {
                $result = $result[$next];
            } else {
                return null;
            }
        }
        return $result;
    }

    public function put($name, $value)
    {
        if ('' === session_id()) {
            $this->start();
        }

        $parsed = explode('.', $name);
        $session =& $_SESSION;
        while (count($parsed) > 1) {
            $next = array_shift($parsed);
            if (!isset($session[$next]) || !is_array($session[$next])) {
                $session[$next] = [];
            }
            $session =& $session[$next];
        }
        $session[array_shift($parsed)] = $value;
    }
}

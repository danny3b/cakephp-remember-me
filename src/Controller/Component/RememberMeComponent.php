<?php

/**
 * Remember Me plugin for CakePHP 3
 * Copyright (c) Narendra Vaghela (http://www.narendravaghela.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.md
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Narendra Vaghela (http://www.narendravaghela.com)
 * @link          https://github.com/narendravaghela/cakephp-remember-me
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace RememberMe\Controller\Component;

use Cake\Controller\Component;
use Cake\Utility\Security;

/**
 * RememberMe component
 *
 * Provides a basic functionality to store user data in Cookies of your CakePHP
 * applications for login and remember user in specific browser.
 */
class RememberMeComponent extends Component
{

    /**
     * Default configuration.
     *
     * - `cypherKey` - Random unuqie string to encrypt/decrypt data.
     *   If not set, default salt value of the application will be used.
     * - `cookieName` - Name of the cookie.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'cypherKey' => "17485937564892755682047369192734583655920926",
        'cookieName' => "rememberme",
        'period' => '14 Days'
    ];

    /**
     * Other components
     *
     * @var array
     */
    public $components = ['Cookie'];

    /**
     * Initialize config data and properties.
     *
     * @param array $config The config data.
     * @return void
     */
    public function initialize(array $config)
    {
        if (!$this->_config['cypherKey']) {
            $this->getConfig('cypherKey', Security::salt());
        }

        $this->Cookie->configKey($this->getConfig('cookieName'), [
            'key' => $this->getConfig('cypherKey'),
            'expires' => $this->getConfig('period')
        ]);
    }

    /**
     * Stores data in cookie
     *
     * @param mixes $data Data to store in cookie
     * @return bool
     */
    public function rememberData($data = null)
    {
        if (empty($data)) {
            return false;
        }

        if (is_object($data)) {
            $encryptedData = json_encode($data);
        } else {
            $encryptedData = serialize($data);
        }

        $this->Cookie->write($this->getConfig('cookieName'), $encryptedData);

        return true;
    }

    /**
     * Returns data stored in cookie
     *
     * @return mixed Stored data otherwise false
     */
    public function getRememberedData()
    {
        $cookieData = $this->Cookie->read($this->getConfig('cookieName'));
        if (!empty($cookieData)) {
            $data = json_decode($cookieData);
            if (!$data) {
                $data = unserialize($cookieData);
            }

            return $data;
        } else {
            return false;
        }
    }

    /**
     * Removes data
     *
     * @return void
     */
    public function removeRememberedData()
    {
        $this->Cookie->delete($this->getConfig('cookieName'));
    }
}

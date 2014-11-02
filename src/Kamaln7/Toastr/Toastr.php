<?php namespace Kamaln7\Toastr;

use Illuminate\Config\Repository;

class Toastr {

    /**
     * Added notifications
     *
     * @var array
     */
    protected $notifications = array();

    /**
     * Illuminate Session
     *
     * @var \Illuminate\Session\SessionManager
     */
    protected $session;

    /**
     * Toastr config
     *
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Constructor
     *
     * @param \Illuminate\Session\SessionManager $sessio
     * @param  Illuminate\Config\Repository  $config
     * @return void
     */
    public function __construct(\Illuminate\Session\SessionManager $session, Repository $config) {
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * Render the notifications' script tag
     *
     * @param bool $flashed Whether to get the 
     *
     * @return string
     */
    public function render() {
        $notifications = $this->session->get('toastr::notifications');
        if(!$notifications) $notifications = array();

        $output = '<script type="text/javascript">';
        foreach($notifications as $notification) {
           
            if(count($notification['options']) > 0) {
                // Merge user supplied options with default options
                $options = array_merge($this->config->get('toastr::options'), $notification['options']);
                // Writing options for output  
                $output .= 'toastr.options = ' . json_encode($options) . ';';
            }
            // Toastr output
            $output .= 'toastr.' . $notification['type'] . "('" . str_replace("'", "\\'", htmlentities($notification['message'])) . "'" . (isset($notification['title']) ? ", '" . str_replace("'", "\\'", htmlentities($notification['title'])) . "'" : null) . ');';
        }
        $output .= '</script>';

        return $output;
    }

    /**
     * Add a notification
     *
     * @param string $type Could be error, info, success, or warning.
     * @param string $message The notification's message
     * @param string $title The notification's title
     *
     * @return bool Returns whether the notification was successfully added or 
     * not.
     */
    public function add($type, $message, $title = null,$options = array()) {
        $allowedTypes = array('error', 'info', 'success', 'warning');
        if(!in_array($type, $allowedTypes)) return false;

        $this->notifications[] = array(
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'options' => $options
        );

        $this->session->flash('toastr::notifications', $this->notifications);
    }

    /**
     * Shortcut for adding an info notification
     *
     * @param string $message The notification's message
     * @param string $title The notification's title
     */
    public function info($message, $title = null, $options = array()) {
        $this->add('info', $message, $title, $options);
    }

    /**
     * Shortcut for adding an error notification
     *
     * @param string $message The notification's message
     * @param string $title The notification's title
     */
    public function error($message, $title = null, $options = array()) {
        $this->add('error', $message, $title, $options);
    }

    /**
     * Shortcut for adding a warning notification
     *
     * @param string $message The notification's message
     * @param string $title The notification's title
     */
    public function warning($message, $title = null, $options = array()) {
        $this->add('warning', $message, $title, $options);
    }

    /**
     * Shortcut for adding a success notification
     *
     * @param string $message The notification's message
     * @param string $title The notification's title
     */
    public function success($message, $title = null, $options = array()) {
        $this->add('success', $message, $title, $options);
    }

    /**
     * Clear all notifications
     */
    public function clear() {
        $this->notifications = array();
    }

}

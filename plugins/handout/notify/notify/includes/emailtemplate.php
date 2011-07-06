<?php
/* @version 	$Id: emailtemplate.php
 * @package 	Handout Notify
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
defined('_JEXEC') or die('Restricted access');

require_once NOTIFY_PATH.DS.'includes'.DS.'info.php';
require_once NOTIFY_PATH.DS.'includes'.DS.'site.php';
require_once NOTIFY_PATH.DS.'includes'.DS.'handoutdocument.php';
require_once NOTIFY_PATH.DS.'includes'.DS.'handoutfile.php';


class NotifyEmailTemplate
{

    /**
     * @var array
     */
    protected $_params;

    /**
     * @var string
     * @private
     */
    protected $_recipients;

    /**
     * @var string
     * @private
     */
    protected $_body;

    /**
     * @var string
     * @private
     */
    protected $_subject;

    /**
     * @var string
     * @private
     */
    protected $_template = 'email';


    /**
     * @var object
     */
    public $info;

    /**
     * @var object
     */
    public $user;

    /**
     * @var object
     */
    public $doc;

    /**
     * @var object
     */
    public $site;


	public function __construct( $params )
	{

        $this->_params = & $params;
        $cfg = NotifyConfig::getInstance();



        $this->user = JFactory::getUser();
        if( ! $this->user->id )
        {
        	$this->user->name 		= JText::_('PLG_HANDOUT_NOTIFY_GUEST');
            $this->user->username 	= JText::_('PLG_HANDOUT_NOTIFY_NOT_AVAILABLE');
            $this->user->email 		= JText::_('PLG_HANDOUT_NOTIFY_NOT_AVAILABLE');
            $this->user->usertype 	= JText::_('PLG_HANDOUT_NOTIFY_GUEST');
        }

        if( !$this->user->name ) { // in admin some fields are not filled in
        	$this->user->load( $this->user->id );
        }
        $this->user->ip = $_SERVER['REMOTE_ADDR'];

        $this->info = new NotifyInfo();
        $this->site = new NotifySite();
        $this->doc = new NotifyDocument( $this->_params );
        $this->file = new NotifyFile( $this->_params );
        $this->_recipients = & $cfg->getRecipients();

	}

    /**
     * Send email using template
     */
    public function send()
    {
        $app 		= JFactory::getApplication();
        $MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');

        // get the template
        ob_start();

        require_once NOTIFY_PATH.DS.'templates'.DS.$this->_template.'.php';
        require_once NOTIFY_PATH.DS.'templates'.DS.'footer.php';
        $this->_body = ob_get_clean();

        if ( !$this->_recipients ) { // if there are no addresses to send to, return
            return;
        }

        $mail = JFactory::getMailer();

        $mail->IsHTML(true);
        $mail->addRecipient( $this->_recipients );
		$mail->setSender( array( $MailFrom, $FromName ) );
		$mail->setSubject( $this->_subject );
		$mail->setBody( $this->_body );
    	$sent = $mail->Send();
    }

    /**
     * Set the template
     *
     * @param string $template Template name (without php extension)
     */
    public function setTemplate( $template )
    {
        $this->_template = $template;
    }

    /**
     * Set the subject
     *
     * @param string $subject
     */
    public function setSubject( $subject )
    {
        $this->_subject = $subject;
    }

}
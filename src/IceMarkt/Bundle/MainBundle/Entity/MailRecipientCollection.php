<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/07/2014
 * Time: 12:23
 */

namespace IceMarkt\Bundle\MainBundle\Entity;

class MailRecipientCollection extends \ArrayObject
{

    /**
     * add a Mail Recipient to this array
     *
     * @param MailRecipient $mailRecipient
     * @return $this
     */
    public function addMailRecipient(MailRecipient $mailRecipient)
    {
        $this[] = $mailRecipient;

        return $this;
    }

    /**
     * @param $email
     * @return Boolean
     */
    public function removeMailRecipientByEmailAddress($email)
    {
        $deleted = false;

        $tmp = $this->getArrayCopy();

        foreach ($tmp as &$recipient) {
            if ($recipient->getEmailAddress() === $email) {
                unset($recipient);
                $deleted = true;
            }
        }

        $this->exchangeArray($tmp);

        return $deleted;
    }
}

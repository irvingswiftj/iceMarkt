<?php

namespace IceMarkt\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class SpreadSheet
 * @package IceMarkt\Bundle\MainBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="spreadsheet_uploads")
 */
class SpreadSheet
{
    /**
     * Possible titles for an 'Email Address' Column
     * @var array
     */
    private $emailTitles = array('email', 'email_address', 'email address', 'e-mail');

    /**
     * Possible titles for a 'First Name' Column
     * @var array
     */
    private $firstNameTitles = array('first name', 'fname', 'firstname');

    /**
     * Possible titles for a 'Last Name' Column
     * @var array
     */
    private $lastNameTitles = array('last name', 'lname', 'lastname', 'surname');

    /**
     * Possible titles for a 'Name' Column
     * @var array
     */
    private $nameTitles = array('name', 'fullname', 'full name');

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * Spreadsheet file
     *
     * @Assert\File(
     *     maxSize = "50M",
     *     mimeTypes = {"text/csv", "text/plain"},
     *     maxSizeMessage = "The maximum allowed file size is 50MB.",
     *     mimeTypesMessage = "Only CSV files for now!"
     * )
     */
    protected $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        $this->setPath($file->getFilename());
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    /**
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads/spreadsheets';
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SpreadSheet
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return SpreadSheet
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     *
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        $this->getFile()->move($this->getUploadRootDir(), $this->getFile()->getClientOriginalName());
        $this->setPath($this->getFile()->getClientOriginalName());
    }

    /**
     * Method to calculate which in the index for the column that contains email addresses
     *
     * @param array $row
     * @return int
     */
    public function getEmailColumnIndex(array $row)
    {
        $index = -1;

        foreach ($row as $key => $value) {
            //check if value is an email or the title for the email column
            if ($this->isEmailColumn($value)) {
                $index = $key;
                break;
            }
        }

        return $index;
    }

    /**
     * Method to try and find the first name column by title name
     * @param array $row
     * @return int
     */
    public function getFirstNameColumnIndex(array $row)
    {
        $index = -1;

        foreach ($row as $key => $value) {
            if ($this->isFirstNameColumn($value)) {
                $index = $key;
                break;
            }
        }

        return $index;
    }

    /**
     * Method to try and find the last name column by title name
     * @param array $row
     * @return int
     */
    public function getLastNameColumnIndex(array $row)
    {
        $index = -1;

        foreach ($row as $key => $value) {
            if ($this->isLastNameColumn($value)) {
                $index = $key;
                break;
            }
        }

        return $index;
    }

    /**
     * Method to try and find the 'name' column by title name
     * @param array $row
     * @return int
     */
    public function getNameColumnIndex(array $row)
    {
        $index = -1;

        foreach ($row as $key => $value) {
            if ($this->isNameColumn($value)) {
                $index = $key;
                break;
            }
        }

        return $index;
    }

    /**
     * Method to work out if a value is an email address else and email column title
     * @param $value
     * @return bool
     */
    private function isEmailColumn($value)
    {
        return (filter_var($value, FILTER_VALIDATE_EMAIL) || in_array(strtolower($value), $this->emailTitles));
    }

    /**
     * Method to match the column title for first name column
     * @param $value
     * @return bool
     */
    private function isFirstNameColumn($value)
    {
        return in_array(strtolower($value), $this->firstNameTitles);
    }

    /**
     * Method to match the column title for last name column
     * @param $value
     * @return bool
     */
    private function isLastNameColumn($value)
    {
        return in_array(strtolower($value), $this->lastNameTitles);
    }

    /**
     * Method to match the column title for 'name' column
     * @param $value
     * @return bool
     */
    private function isNameColumn($value)
    {
        return in_array(strtolower($value), $this->nameTitles);
    }
}

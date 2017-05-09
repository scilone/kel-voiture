<?php

/**
 * Class ProgressBar
 */
class ProgressBar
{
    private $fileProgressBar;
    private $pathProgressBar = 'progressBar/progressBar';
    private $extensionProgressBar = '.txt';

    /**
     * ProgressBar constructor.
     */
    public function __construct($id = null)
    {
        if ($id === null) {
            $id = session_id();
        }

        $this->fileProgressBar = fopen(
            $this->pathProgressBar . $id . $this->extensionProgressBar,
            'w+'
        );
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->prepareEcrase();
        $this->setNewValue($value);
    }

    /**
     * @param $value
     */
    private function setNewValue($value)
    {
        fputs($this->fileProgressBar, $value);
    }

    /**
     * remet à 0 le fichier
     */
    public function reset()
    {
        $this->prepareEcrase();
        $this->setNewValue(0);

        fclose($this->fileProgressBar);
    }

    /**
     * pointeur au debut du fichier
     */
    private function prepareEcrase()
    {
        fseek($this->fileProgressBar, 0);
    }
}
<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Cache;

/**
 * Description of SharedMemory
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SharedMemory {

    /**
     * the system id for the shared memory block
     */
    protected $id;

    /**
     * the default permission (octal) that will be used in created memory blocks
     */
    protected $shmid;

    /**
     * permissions
     */
    protected $perms = 0644;

    /**
     * Here we install the shared memory. For this we need unique ID. This id
     * will be usually a filename/path on the system (see ftok)
     * @param string $id
     */
    public function __construct($key) {
        $this->id = ftok($key, 'B'); // B stands for Blend
        if ($this->exists($this->id)) {
            $this->shmid = shmop_open($this->id, "w", 0, 0);
        }
    }

    /**
     * Checks if the shared memory block already exists
     * @return type
     */
    public function exists() {
        $status = @shmop_open($this->id, "a", 0, 0);
        return $status !== FALSE;
    }

    /**
     * Wrotes data to the shared memoty block
     * @param mixed $data
     */
    public function write($data) {
        $size = mb_strlen($data, 'UTF-8');

        if ($this->exists($this->id)) {
            shmop_delete($this->shmid);
            shmop_close($this->shmid);
            $this->shmid = shmop_open($this->id, "c", $this->perms, $size);
            shmop_write($this->shmid, $data, 0);
        } else {
            $this->shmid = shmop_open($this->id, "c", $this->perms, $size);
            shmop_write($this->shmid, $data, 0);
        }
    }

    /**
     * Reads data from shared memoty block
     * @return type
     */
    public function read($default = null) {
        if (!is_null($this->shmid)) {
            $size = shmop_size($this->shmid);
            $data = shmop_read($this->shmid, 0, $size);
            return $data;
        } else {
            return $default;
        }
    }

    /**
     * Mark a shared memory block for deletion
     */
    public function delete() {
        if (!is_null($this->shmid)) {
            shmop_delete($this->shmid);
        }
    }

    /**
     * Gets the current shared memory block id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Closes the shared memory block and stops manipulation
     */
    public function __destruct() {
        if (!is_null($this->shmid)) {
            shmop_close($this->shmid);
        }
    }

}

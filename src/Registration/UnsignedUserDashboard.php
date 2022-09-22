<?php

namespace MyDramLibrary\Registration;

use MyDramLibrary\Utilities\Database\Database;

class UnsignedUserDashboard
{
    private Database $database;
    private int $titleCount;
    private int $userCount;
    private int $cityCount;
    private bool $loaded = false;

    public function __construct()
    {
        $this->database = Database::instance();
    }

    private function getRounded(int $number)
    {
        if ($number < 10) {
            return $number;
        }
        $numberRoundedTen = floor($number / 10) * 10;
        return ($number == $numberRoundedTen) ? $number : $this->getRounded($numberRoundedTen / 10) * 10;
    }

    private function load(): void
    {
        if (!$this->loaded) {
            $this->loadTitleCount();
            $this->loadUserCount();
            $this->loadCityCount();
            $this->loaded = true;
        }
    }

    private function loadTitleCount(): void
    {
        $sql = 'select count(id) from title';
        $this->titleCount = $this->database->run($sql)->fetchColumn();
    }

    private function loadUserCount(): void
    {
        $sql = 'select count(id) from user';
        $this->userCount = $this->database->run($sql)->fetchColumn();
    }

    private function loadCityCount(): void
    {
        $this->cityCount = 0; //na razie nie mam w bazie kolumny identyfikującej miasto
    }

    public function getTitleCount(): int
    {
        if (!$this->loaded) {
            $this->load();
        }
        return $this->titleCount;
    }

    public function getTitleCountRounded(): string
    {
        return $this->getRounded($this->getTitleCount()) . '+';
    }

    public function getUserCount(): int
    {
        if (!$this->loaded) {
            $this->load();
        }
        return $this->userCount;
    }

    public function getUserCountRounded(): string
    {
        return $this->getRounded($this->getUserCount()) . '+';
    }

    public function getCityCount(): int
    {
        if (!$this->loaded) {
            $this->load();
        }
        return $this->cityCount;
    }

    public function getCityCountRounded(): string
    {
        return $this->getRounded($this->getCityCount()) . '+';
    }
}

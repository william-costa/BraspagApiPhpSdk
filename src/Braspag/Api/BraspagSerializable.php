<?php
namespace Braspag\Api;

interface BraspagSerializable extends \JsonSerializable
{

    public function populate(\stdClass $data);
}
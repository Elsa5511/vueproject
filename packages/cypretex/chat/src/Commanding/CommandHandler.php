<?php

namespace Cypretex\Chat\Commanding;

interface CommandHandler
{
    public function handle($command);
}

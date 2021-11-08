<?php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework) {
    $framework->router()->defaultUri('%env(MERCREDI_URI)%');
};

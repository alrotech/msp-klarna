<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
        switch ($_GET['a']) {
            case 'system/settings':

                break;
        }
}

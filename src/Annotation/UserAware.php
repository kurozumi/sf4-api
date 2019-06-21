<?php


namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * 一覧をユーザーで絞り込むためのアノテーション
 *
 * Class UserAware
 * @package App\Annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
final class UserAware
{
    public $userFieldName;
}
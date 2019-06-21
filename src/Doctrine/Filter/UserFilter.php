<?php


namespace App\Doctrine\Filter;

use App\Annotation\UserAware;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class UserFilter extends SQLFilter
{
    private $reader;

    /**
     * エンティティにUserAwareアノテーションが設定され、
     * そのエンティティがUserエンティティとリレーションしている場合に
     * ユーザーで絞り込むフィルター。
     *
     * @param ClassMetaData $targetEntity
     * @param string $targetTableAlias
     *
     * @return string 制限するSQLまたは空の値
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if(null === $this->reader) {
            throw new \RuntimeException(sprintf('Annotation Readerが必要です。"%s::setAnnotationReader()"でセットして下さい。', __CLASS__));
        }

        $userAware = $this->reader->getClassAnnotation($targetEntity->getReflectionClass(), UserAware::class);
        if(!$userAware) {
            return '';
        }

        $fieldName = $userAware->userFieldName;

        try {
            $userId = $this->getParameter('id');
        } catch (\InvalidArgumentException $e) {
            return '';
        }

        if(empty($filedName) || empty($userId)) {
            return '';
        }

        return sprintf('%s.%s = %s', $targetTableAlias, $fieldName, $userId);
    }

    public function setAnnotationReader(Reader $reader): void
    {
        $this->reader = $reader;
    }
}
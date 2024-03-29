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
     * 指定したユーザーで絞り込むフィルター。
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

        // UserAwareアノテーションを取得
        $userAware = $this->reader->getClassAnnotation($targetEntity->getReflectionClass(), UserAware::class);
        if(!$userAware) {
            return '';
        }

        // UserAwareアノテーションで設定されたUserフィールド名
        $fieldName = $userAware->userFieldName;

        try {
            // セットされたuserIdを取得
            $userId = $this->getParameter('id');
        } catch (\InvalidArgumentException $e) {
            return '';
        }

        if(empty($fieldName) || empty($userId)) {
            return '';
        }

        // クエリにユーザーで絞り込む条件を追加
        return sprintf('%s.%s = %s', $targetTableAlias, $fieldName, $userId);
    }

    public function setAnnotationReader(Reader $reader): void
    {
        $this->reader = $reader;
    }
}
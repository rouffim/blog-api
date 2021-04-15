<?php


namespace App\Enums;


use BenSampo\Enum\Enum;

final class PermissionEnum extends Enum
{
    const ViewArticle = 'view_article';
    const AddArticle = 'add_article';
    const EditOwnArticle = 'edit_own_article';
    const EditAllArticle = 'edit_all_article';
    const RemoveOwnArticle = 'remove_own_article';
    const RemoveAllArticle = 'remove_all_article';
    const PinArticle = 'pin_article';
    const ChangeRole = 'change_role';
}

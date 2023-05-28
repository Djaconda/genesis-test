<?php

namespace PHPSTORM_META {

    use Agency\Domain\Model\Agency\AgencyQuery;
    use Agency\Domain\Model\Agency\AgencyRecord;
    use Client\Profile\Domain\Model\Client\ClientQuery;
    use Client\Profile\Domain\Model\Client\ClientRecord;
    use Client\Program\Domain\Model\Program\ProgramQuery;
    use Client\Program\Domain\Model\Program\ProgramRecord;
    use PHPKitchen\DI\Contracts\Container;
    use Referral\Profile\Domain\Model\Note\NoteQuery;
    use Referral\Profile\Domain\Model\Note\NoteRecord;
    use User\Profile\Domain\Model\User\UserQuery;
    use User\Profile\Domain\Model\User\UserRecord;

    override(\array_filter(0), type(0));
    override(\core\frontend\Application::getContainer(), map([
        '' => Container::class,
    ]));
    override(Container::get(0), map([
        '' => '@',
    ]));
    override(Container::create(0), map([
        '' => '@',
    ]));
}

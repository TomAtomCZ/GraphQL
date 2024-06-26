<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/6/15 11:15 PM
*/

namespace Youshido\Tests\StarWars\Schema;

use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class CharacterInterface extends AbstractInterfaceType
{
    public function build(InterfaceTypeConfig $config): void
    {
        $config
            ->addField('id', new NonNullType(new IdType()))
            ->addField('name', new NonNullType(new StringType()))
            ->addField('friends', [
                'type'    => new ListType(new CharacterInterface()),
                'resolve' => static function (array $value) {
                    return $value['friends'];
                }
            ])
            ->addField('appearsIn', new ListType(new EpisodeEnum()));
    }

    public function getDescription(): string
    {
        return 'A character in the Star Wars Trilogy';
    }

    public function getName(): string
    {
        return 'Character';
    }

    public function resolveType($object): \Youshido\Tests\StarWars\Schema\HumanType|\Youshido\Tests\StarWars\Schema\DroidType|null
    {
        $humans = StarWarsData::humans();
        $droids = StarWarsData::droids();

        $id = isset($object['id']) ? $object['id'] : $object;

        if (isset($humans[$id])) {
            return new HumanType();
        }

        if (isset($droids[$id])) {
            return new DroidType();
        }

        return null;
    }

}

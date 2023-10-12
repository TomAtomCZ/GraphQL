<?php
/**
 * Created by PhpStorm.
 * User: mounter
 * Date: 8/18/16
 * Time: 2:17 PM
 */

namespace Youshido\Tests\Performance;


use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class LoadTest extends \PHPUnit_Framework_TestCase
{

    public function testLoad10k(): bool
    {
        microtime(true);
        $postType = new ObjectType([
            'name'   => 'Post',
            'fields' => [
                'id'      => new IdType(),
                'title'   => new StringType(),
                'authors' => [
                    'type' => new ListType(new ObjectType([
                        'name'   => 'Author',
                        'fields' => [
                            'name' => new StringType()
                        ]
                    ]))
                ],
            ]
        ]);

        $data = [];
        for ($i = 1; $i <= 10000; ++$i) {
            $authors = [];
            while (count($authors) < rand(1, 4)) {
                $authors[] = [
                    'name' => 'Author ' . substr(md5(time()), 0, 4)
                ];
            }
            
            $data[] = [
                'id'      => $i,
                'title'   => 'Title of ' . $i,
                'authors' => $authors,
            ];
        }

        new Processor(new Schema([
            'query' => new ObjectType([
                'name' => 'RootQuery',
                'fields' => [
                    'posts' => [
                        'type' => new ListType($postType),
                        'resolve' => static function () use ($data) {
                            return $data;
                        }
                    ]
                ],
            ]),
        ]));
        return true;
    }

}
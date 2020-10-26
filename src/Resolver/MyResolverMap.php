<?php


namespace App\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\ArgumentInterface;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

class MyResolverMap extends ResolverMap
{
    protected function map()
    {
       return [
           'Query' => [
               self::RESOLVE_FIELD => function ($value, ArgumentInterface $args, \ArrayObject $context, ResolveInfo $info) {
                   if ('baz' === $info->fieldName) {
                       $id = (int) $args['id'];

                       return findBaz('baz', $id);
                   }

                   return null;
               },
           ]
       ];
    }
}
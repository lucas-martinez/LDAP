<?php
/**
 * This file is part of the phpDS package.
 *
 * (c) Chad Sikorra <Chad.Sikorra@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PhpDs\Ldap\Control\Sorting;

use PhpDs\Ldap\Asn1\Asn1;
use PhpDs\Ldap\Asn1\Encoder\BerEncoder;
use PhpDs\Ldap\Control\Control;
use PhpDs\Ldap\Control\Sorting\SortingControl;
use PhpDs\Ldap\Control\Sorting\SortKey;
use PhpSpec\ObjectBehavior;

class SortingControlSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new SortKey('foo'), new SortKey('bar'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SortingControl::class);
    }

    function it_should_have_the_sorting_oid()
    {
        $this->getTypeOid()->shouldBeEqualTo(Control::OID_SORTING);
    }

    function it_should_get_the_sort_keys()
    {
        $this->getSortKeys()->shouldBeLike([
           new SortKey('foo'),
           new SortKey('bar')
        ]);
    }

    function it_should_set_sort_keys()
    {
        $this->setSortKeys(new SortKey('foobar'));

        $this->getSortKeys()->shouldBeLike([new SortKey('foobar')]);
    }

    function it_should_add_sort_keys()
    {
        $key = new SortKey('foobar');
        $this->addSortKeys($key);

        $this->getSortKeys()->shouldContain($key);
    }

    function it_should_generate_correct_asn1()
    {
        $this->addSortKeys(new SortKey('foobar', 'bleh', true));

        $encoder = new BerEncoder();
        $this->toAsn1()->shouldBeLike(Asn1::sequence(
            Asn1::ldapOid(Control::OID_SORTING),
            Asn1::boolean(false),
            Asn1::octetString($encoder->encode(Asn1::sequenceOf(
                Asn1::sequence(Asn1::ldapString('foo')),
                Asn1::sequence(Asn1::ldapString('bar')),
                Asn1::sequence(
                    Asn1::ldapString('foobar'),
                    Asn1::context(0, Asn1::ldapString('bleh')),
                    Asn1::context(1, Asn1::boolean(true))
                )
            )))
        ));
    }
}
<?php

declare(strict_types=1);

use App\Services\EnrollmentInquiryPolicy;
use PHPUnit\Framework\TestCase;

final class EnrollmentInquiryPolicyTest extends TestCase
{
    public function testCategoriesMustBeApproved(): void
    {
        self::assertTrue(EnrollmentInquiryPolicy::validCategory('U7'));
        self::assertTrue(EnrollmentInquiryPolicy::validCategory('not_sure'));
        self::assertFalse(EnrollmentInquiryPolicy::validCategory('U15'));
        self::assertFalse(EnrollmentInquiryPolicy::validCategory('institutional_admin'));
    }

    public function testAdultContactFieldsAreBounded(): void
    {
        self::assertTrue(EnrollmentInquiryPolicy::validName('Ana López'));
        self::assertFalse(EnrollmentInquiryPolicy::validName('A'));
        self::assertFalse(EnrollmentInquiryPolicy::validName(str_repeat('A', 121)));
        self::assertTrue(EnrollmentInquiryPolicy::validPhone(''));
        self::assertTrue(EnrollmentInquiryPolicy::validPhone('+503 2222-2222'));
        self::assertFalse(EnrollmentInquiryPolicy::validPhone('texto no válido'));
        self::assertTrue(EnrollmentInquiryPolicy::validMessage('Deseo conocer los entrenamientos'));
        self::assertFalse(EnrollmentInquiryPolicy::validMessage(str_repeat('a', 501)));
    }
}

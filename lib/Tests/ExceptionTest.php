<?php
/**
 * Panthera Framework 2 exception test cases
 *
 * @package Panthera\exceptions\tests
 * @author Mateusz Warzyński <lxnmen@gmail.com>
 */
class ExceptionsTest extends PantheraFrameworkTestCase
{
    /**
     * Check PantheraFrameworkException from BaseExceptions module
     *
     * @expectedException \Panthera\Classes\BaseExceptions\PantheraFrameworkException
     *
     * @author Mateusz Warzyński <lxnmen@gmail.com>
     */
    public function testSetGetValue()
    {
        $this->setup();
        throw new \Panthera\Classes\BaseExceptions\PantheraFrameworkException('Test Panthera Framework exception', 'test');
    }

    /**
     * Check ValidationException from BaseExceptions module
     *
     * @expectedException \Panthera\Classes\BaseExceptions\ValidationException
     *
     * @author Mateusz Warzyński <lxnmen@gmail.com>
     */
    public function testValidationException()
    {
        $this->setup();
        throw new \Panthera\Classes\BaseExceptions\ValidationException('Simple message', 'yay, this is code');
    }
}
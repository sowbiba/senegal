<?php
/**
 * Author: Florent Coquel
 * Date: 24/09/13
 */
namespace Api\Sdk\Mediator;

class SdkMediator extends AbstractMediator
{
     /**
     * Returns the name of the given sdk
     *
     * @param ColleagueInterface $sdk
     *
     * @return string
     */
    protected function getName(ColleagueInterface $sdk)
    {
        $explodeClassName = explode("\\", get_class($sdk));
        $sdkName          = str_replace("Sdk", "", lcfirst(array_pop($explodeClassName)));

        return $sdkName;
    }

    /**
     * Return a SDK by the supported class of the sdk
     *
     * @param string $classname
     *
     * @return SdkInterface
     * @throws \RuntimeException
     */
    public function getSdkByClass($classname)
    {
        /** @var SdkInterface $sdk */
        foreach ($this->getColleagues() as $sdk) {
            if ($sdk->supports($classname)) {
                return $sdk;
            }
        }

        throw new \RuntimeException(sprintf('SDK for class "%s" not found', $classname));
    }

   public function getSdk($sdkName)
    {
        return parent::getColleague($sdkName);
    }

    public function addSdk(ColleagueInterface $sdk)
    {
        parent::addColleague($sdk);
    }

    public function setSdkList(array $sdkList)
    {
        parent::setColleagueList($sdkList);
    }
}

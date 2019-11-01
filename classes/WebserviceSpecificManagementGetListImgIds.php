<?php

class WebserviceSpecificManagementGetListImgIds implements WebserviceSpecificManagementInterface
{
    /** @var WebserviceOutputBuilder */
    protected $objOutput;
    protected $output;
    private $id;

    /** @var WebserviceRequest */
    protected $wsObject;

    public function setUrlSegment($segments)
    {
        $this->urlSegment = $segments;
        return $this;
    }

    public function getUrlSegment()
    {
        return $this->urlSegment;
    }

    public function getWsObject()
    {
        return $this->wsObject;
    }

    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    /**
     * This must be return a string with specific values as WebserviceRequest expects.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->objOutput->getObjectRender()->overrideContent($this->output);
    }

    public function setWsObject(WebserviceRequestCore $obj)
    {
        $this->wsObject = $obj;
        return $this;
    }

    /**
     * @param WebserviceOutputBuilderCore $obj
     * @return WebserviceSpecificManagementInterface
     */
    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {

        $this->objOutput = $obj;
        return $this;
    }

    public function manage()
    {

        if (!isset($this->wsObject->urlFragments['id_img'])) {
            throw new WebserviceException('params undefinid, id_image from img', array(100, 400));
        } else {
            $queryFinal = 'SELECT id_img FROM `' . _DB_PREFIX_ . 'sincro_img` WHERE id_product =' . $this->wsObject->urlFragments['id_img'] . ' AND status = 1;';
            $respuesta = Db::getInstance()->executeS($queryFinal);
            $this->output = json_encode($respuesta, true);

        }

    }
}
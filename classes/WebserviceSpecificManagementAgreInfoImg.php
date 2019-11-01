<?php

class WebserviceSpecificManagementAgreInfoImg implements WebserviceSpecificManagementInterface
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

        //  $valor = json_encode($obj, true);
        //   $final = json_decode($valor, true);
        //   $this->id = $final['urlSegment'][1];
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

        if (!isset($this->wsObject->urlFragments['id_product']) || !isset($this->wsObject->urlFragments['id_img']) || !isset($this->wsObject->urlFragments['id_img_ecommerce']) || !isset($this->wsObject->urlFragments['id_product_ecommerce'])) {
            throw new WebserviceException('params undefinid idSic and md5Img  and idEcommerce and status from img the', array(100, 400));
        } else {
            $query = 'INSERT INTO `' . _DB_PREFIX_ . 'sincro_img` (id_product_s, id_img_s, id_img_ecommerce, id_product_ecommerce, status) VALUES(' . $this->wsObject->urlFragments['id_product'] . ', ' . $this->wsObject->urlFragments['id_img'] . ', ' . $this->wsObject->urlFragments['id_img_ecommerce'] . ', ' . $this->wsObject->urlFragments['id_product_ecommerce'] . ', 1);';

            $respuesta = Db::getInstance()->execute($query);
            $this->output = $query . '  respuesta=' . json_encode($respuesta, true);

        }
    }
}
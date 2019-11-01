<?php

class WebserviceSpecificManagementGetImgIsExist implements WebserviceSpecificManagementInterface
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

        if (!isset($this->wsObject->urlFragments['id_product']) || !isset($this->wsObject->urlFragments['id_img'])) {
            throw new WebserviceException('params undefinid id_product and id_img', array(100, 400));
        } else {
            $query = 'SELECT `id_img` FROM `' . _DB_PREFIX_ . 'sincro_img` WHERE id_product = ' . $this->wsObject->urlFragments['id_product'] . ' AND id_img =' . $this->wsObject->urlFragments['id_img'] . ' AND status = 1;';

            $respuesta = Db::getInstance()->executeS($query);
            if ($respuesta != null) {
                $this->output = 1;
            } else {
                $this->output = 0;
            }
        }

    }
}
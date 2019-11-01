<?php

class WebserviceSpecificManagementEnableImg implements WebserviceSpecificManagementInterface
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

        if (!isset($this->wsObject->urlFragments['id_img'])) {
            throw new WebserviceException('params undefinid id_img from img', array(100, 400));
        } else {

            $queryVar = 'SELECT @idiimg := id_ecommerce
FROM `'._DB_PREFIX_.'sincro_img`
WHERE id_img_ecommerce = '.$this->wsObject->urlFragments['id_img'].'
  AND status = 0
LIMIT 1;
UPDATE `'._DB_PREFIX_.'sincro_img`, `ps_image`
SET `'._DB_PREFIX_.'sincro_img`.status = 1,
    `'._DB_PREFIX_.'image`.id_product  = @idiimg
WHERE `'._DB_PREFIX_.'sincro_img`.id_img_ecommerce = '.$this->wsObject->urlFragments['id_img'].'
  AND `'._DB_PREFIX_.'image`.id_image = '.$this->wsObject->urlFragments['id_img'].';';
            $respuesta = Db::getInstance()->execute($queryVar);
            $this->output = $queryVar . '  respuesta=' . json_encode($respuesta, true);

        }


    }
}
<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require 'PSWebServiceLibrary.php';
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
include_once(_PS_MODULE_DIR_ . 'sync/classes/WebserviceSpecificManagementSincro.php');
include_once(_PS_MODULE_DIR_ . 'sync/classes/WebserviceSpecificManagementAgreInfoImg.php');
include_once(_PS_MODULE_DIR_ . 'sync/classes/WebserviceSpecificManagementEnableImg.php');
include_once(_PS_MODULE_DIR_ . 'sync/classes/WebserviceSpecificManagementDisableImg.php');
include_once(_PS_MODULE_DIR_ . 'sync/classes/WebserviceSpecificManagementGetListImgIds.php');
include_once(_PS_MODULE_DIR_ . 'sync/classes/WebserviceSpecificManagementGetImgIsExist.php');

class Sync extends Module
{
    var $tempBearer;
    var $bear;
    var $url;
    var $nube;
    var $name;
    var $pass;
    var $version;
    var $sucId;
    var $dominios;
    var $banderaCat = true;
    var $banderaArt = true;
    var $banderaPack = true;
    var $banderaEditArt = true;
    var $banderaEditCat = true;
    var $banderaEditDepa = true;
    var $banderaDepa = true;
    var $banderaPackPorEditar = true;
    var $serviceKey = 'PDXSMLLRR9R51MP7G3HIZT41UMJBM91V';

    public function __construct()
    {
        $this->name = 'sync';
        $this->tab = 'front_office_features';
        $this->version = '0.0.1';
        $this->author = 'fredy';
        $this->bootstrap = TRUE;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->displayName = 'Sync';
        $this->description = 'SYNC';
        parent::__construct();
        $this->confirmUninstall = 'Deseas desinstalar este modulo ?';
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('displayProductAdditionalInfo')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('addWebserviceResources')
        ) {
            return false;
        } else {
            return true;
        }
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::deleteByName('sync'))
            return false;
        return true;
    }

    public function hookAddWebserviceResources()
    {
        return array(
            'Sincro' => array(
                'description' => 'img sincro ',
                'specific_management' => true,
            ),
            'AgreInfoImg' => array(
                'description' => 'add info from one img',
                'specific_management' => true,
            ),
            'EnableImg' => array(
                'description' => 'Enable img',
                'specific_management' => true,
            ),
            'DisableImg' => array(
                'description' => 'Disable img',
                'specific_management' => true,
            ),
            'GetListImgIds' => array(
                'description' => 'get list  img enable from ecommerce ',
                'specific_management' => true,
            ),
            'GetImgIsExist' => array(
                'description' => 'check if exist img in ecommerce ',
                'specific_management' => true,
            )
        );
    }

    public function sincronisar()
    {
        $name = Configuration::get('name-nube');
        $pass = Configuration::get('pass-nube');
        $version = Configuration::get('-version');
        $suckId = Configuration::get('sucursal-id');
        $dom = Configuration::get('PS_SHOP_DOMAIN');
        $dominio = 'http://' . $dom . '/';
        $this->dominios = $dominio;
        $this->sucId = $suckId;

        try {
            if ($this->log($name, $pass, $version, $dominio) === true) {

                while ($this->banderaDepa) {
                    $this->sincronisarDepa($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                }

                while ($this->banderaCat) {
                    $this->sincronisarCat($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                }

                while ($this->banderaArt) {
                    $this->sincronisarProducts($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                }

                while ($this->banderaPack) {
                    $this->sincronisarPack($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                }

                while ($this->banderaEditArt) {
                    $this->sincronisarProductsPorEditar($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                }

                while ($this->banderaEditDepa) {
                    $this->sincronisarDepaPorEditar($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                }

                while ($this->banderaEditCat) {
                    $this->sincronisarCatPorEditar($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                }

                while ($this->banderaPackPorEditar) {
                    $this->sincronisarPaquetePorEditar($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                }
            } else {
                Configuration::updateValue('cron-sincro', 'no logro loguearse');
            }

        } catch (Exception $e) {
            Configuration::updateValue('cron-sincro', $e);
        }
    }

    public function getContent()
    {
        if (Tools::isSubmit('boton')) {
            $name = Tools::getValue('name');
            $pass = Tools::getValue('pass');
            $version = Tools::getValue('versions');
            $suckId = Configuration::get('sucursal-id');
            $dom = Configuration::get('PS_SHOP_DOMAIN');
            $dominio = 'http://' . $dom . '/';
            $this->dominios = $dominio;
            $this->sucId = $suckId;

            try {
                Configuration::updateValue('name-nube', $name);
                Configuration::updateValue('pass-nube', $pass);
                Configuration::updateValue('-version', $version);

                if ($this->log($name, $pass, $version, $dominio) === true) {

                    while ($this->banderaDepa) {
                        $this->sincronisarDepa($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                    }

                    while ($this->banderaCat) {
                        $this->sincronisarCat($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                    }

                    while ($this->banderaArt) {
                        $this->sincronisarProducts($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                    }

                    while ($this->banderaPack) {
                        $this->sincronisarPack($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                    }

                    while ($this->banderaEditArt) {
                        $this->sincronisarProductsPorEditar($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                    }

                    while ($this->banderaEditDepa) {
                        $this->sincronisarDepaPorEditar($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                    }

                    while ($this->banderaEditCat) {
                        $this->sincronisarCatPorEditar($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                    }

                    while ($this->banderaPackPorEditar) {
                        $this->sincronisarPaquetePorEditar($name, $pass, $this->nube, $version, $this->sucId, $this->dominios, $this->tempBearer, $this->serviceKey);
                    }
                }
            } catch (Exception $e) {
                Configuration::updateValue('ecommerce-errorsf', $e);
            }
        }
        $nombre = Configuration::get('name-nube');
        $contra = Configuration::get('pass-nube');

        $this->smarty->assign('nameValue', $nombre);
        $this->smarty->assign('passValue', $contra);

        if (Configuration::get('sucursal') != null) {
            return $this->display(__FILE__, 'sincro.tpl');
        } else {
            $sucuid = Configuration::get('sucursal-id');
            $this->smarty->assign('sucu', $sucuid);
            return $this->display(__FILE__, 'configure.tpl');
        }
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        $this->context->controller->addCSS(array($this->_path . 'views/css/nubestyle.css'));
    }

    function log($name, $pass, $version, $dominioss)
    {
        $fiels2s = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ws=\"http://ws.login./\">\n   <soapenv:Header/>\n   <soapenv:Body>\n      <ws:getUrlWsdl>\n         <!--Optional:-->\n         <user>" . $name . "</user>\n       \n         <pass>" . $pass . "</pass>\n      </ws:getUrlWsdl>\n   </soapenv:Body>\n</soapenv:Envelope>";
        $responses = $this->peticionesCurl('https://sync.mx/LoginWS/getUrlWsdl', '', $fiels2s, $name, $pass, 'ey', false, false);
        $final = substr($responses, 148);
        $urlarray = explode('</return>', $final);
        $this->nube = $urlarray[0];
        $dom = new DOMDocument;
        $dom->loadHTML($responses, LIBXML_COMPACT | LIBXML_HTML_NOIMPLIED | LIBXML_NONET);
        $b_nodelist = $dom->getElementsByTagName('return');
        foreach ($b_nodelist as $b) {
            $texto = $b->textContent;
        }
        if ($texto != null) {
            try {
                $ur2 = '/login/sign';
                $filsis = 'user=' . $name . '&pass=' . $pass . '&undefined=';
                $responses = $this->peticionesCurl($this->nube, $ur2, $filsis, $name, $pass, 'ey', true, false);
                $this->bear = $responses;
                $this->tempBearer = $responses;
                $this->smarty->assign('bear', $responses);
                $activa = Configuration::get('sucursal');

            } catch (Exception $e) {
                Configuration::updateValue('log-error', $e);
            }
            return true;
        } else {
            $dom = new DOMDocument;
            $dom->loadHTML($responses, LIBXML_COMPACT | LIBXML_HTML_NOIMPLIED | LIBXML_NONET);
            $b_nodelist = $dom->getElementsByTagName('faultstring');
            foreach ($b_nodelist as $b) {
                $texto = $b->textContent;
                $this->smarty->assign('error', $texto);
                return $this->display(__FILE__, 'configure.tpl');
            }
        }
    }

    function sincronisarDepa($name, $pass, $nube, $version, $sucId, $dominios, $jwt, $serviceKey)
    {
        try {
            $ur2 = '/departamento/lista';
            $fiels2 = 'sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
            $responseDep = $this->peticionesCurl($nube, $ur2, $fiels2, $name, $pass, $jwt, false, false);
            $arrayjson = json_decode($responseDep, true);
            foreach ($arrayjson as $key) {
                if ($key['system'] == true) {
                    $id = $key['depId'];
                    $ur22 = '/ed/vin';
                    $fiels22 = 'tipo=Departamento&nubId=' . $id . '&locId=3&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $this->peticionesCurl($nube, $ur22, $fiels22, $name, $pass, $jwt, false, false);
                    continue;
                } else {
                    try {
                        $webService = new PrestaShopWebservice($dominios, $serviceKey, false);
                        $statt = $key['status'];
                        if ($key['status'] == -1) {
                            $statt = 0;
                        }
                        $depaName = $key['nombre'];
                        if ($key['nombre'] == null) {
                            $depaName = 'Desconocido';
                        }
                        $opt = array(
                            'resource' => 'categories',
                            'postXml' => "<?xml version='1.0' encoding='UTF-8'?>
<prestashop xmlns:xlink='http://www.w3.org/1999/xlink'>
<category>
	<id/>
	<id_parent><![CDATA[2]]></id_parent>
	<active><![CDATA[" . $statt . "]]></active>
	<id_shop_default><![CDATA[1]]></id_shop_default>
	<is_root_category><![CDATA[0]]></is_root_category>
	<position><![CDATA[0]]></position>
	<date_add></date_add>
	<date_upd></date_upd>
	<name><language id='1' xlink:href='" . $dominios . "/api/languages/1'><![CDATA[" . $depaName . "]]></language><language id='2'/></name>
	<link_rewrite><language id='1' xlink:href='" . $dominios . "/api/languages/1'><![CDATA[link-rewrite]]></language><language id='2'/></link_rewrite>
	<description><![CDATA[<p><span style='font-size:10pt;font-family:Arial;font-style:normal;'></span></p>]]></description>
	<meta_title></meta_title>
	<meta_description></meta_description>
	<meta_keywords></meta_keywords>
<associations>
<categories>
	<category>
	<id/>
	</category>
</categories>
<products>
	<product>
	<id/>
	</product>
</products>
</associations>
</category>
</prestashop>"
                        );
                        $xml_request = $webService->add($opt);
                    } catch (Exception $e) {
                        Configuration::updateValue('ecommerce-error1', $e);
                        $this->sincronisarDepa($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
                    }
                    $requestArray = (array)$xml_request;
                    $requestArray2 = (array)$requestArray['category'];
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'sincro_nube` (tipo, id_ecommerce, id_) VALUES("Departamento", ' . $requestArray2['id'] . ',' . $key['depId'] . ');';
                    Db::getInstance()->execute($sql);
                    $id = $key['depId'];
                    $ur232 = '/ed/vin';
                    $fiels232 = 'tipo=Departamento&nubId=' . $id . '&locId=' . $requestArray2['id'] . '&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $responsess23 = $this->peticionesCurl($nube, $ur232, $fiels232, $name, $pass, $jwt, false, false);
                }
            }
            if ($responsess23 == '') {
                $this->banderaDepa = false;
            }
        } catch (Exception $e) {
            Configuration::updateValue('ecommerce-error2', $e);
            $this->sincronisarDepa($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
        }
    }

    function sincronisarCat($name, $pass, $nube, $version, $sucId, $dominios, $jwt, $serviceKey)
    {
        try {
            $urs = '/categoria/lista';
            $fielsi = 'sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
            $responsecat = $this->peticionesCurl($nube, $urs, $fielsi, $name, $pass, $jwt, false, false);
            $arrayjson = json_decode($responsecat, true);
            foreach ($arrayjson as $key) {
                if ($key['system'] == true) {
                    $id = $key['catId'];
                    $ur22 = '/ed/vin';
                    $fiels22 = 'tipo=Categoria&nubId=' . $id . '&locId=3&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $this->peticionesCurl($nube, $ur22, $fiels22, $name, $pass, $jwt, false, false);
                    continue;
                } else {
                    try {
                        $sql = 'SELECT id_ecommerce FROM  `' . _DB_PREFIX_ . 'sincro_nube` WHERE id_ = ' . $key['departamento']['depId'] . ' AND tipo = "Departamento";';
                        $id_defauld = Db::getInstance()->executeS($sql);
                        $id_cat = $id_defauld[0]['id_ecommerce'];
                        $webService = new PrestaShopWebservice($dominios, $serviceKey, false);
                        if ($id_cat == null) {
                            $id_cat = 2;
                        }
                        $statts = $key['status'];
                        if ($key['status'] == -1) {
                            $statts = 0;
                        }
                        $opt = array(
                            'resource' => 'categories',
                            'postXml' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<prestashop xmlns:xlink=\"http://www.w3.org/1999/xlink\">
<category>
	<id/>
	<id_parent><![CDATA[" . $id_cat . "]]></id_parent>
	<active><![CDATA[" . $statts . "]]></active>
	<id_shop_default><![CDATA[1]]></id_shop_default>
	<is_root_category><![CDATA[0]]></is_root_category>
	<position><![CDATA[0]]></position>
	<date_add></date_add>
	<date_upd></date_upd>
	<name><language id='1' xlink:href='" . $dominios . "/api/languages/1'><![CDATA[" . $key['nombre'] . "]]></language><language id='2'/></name>
	<link_rewrite><language id='1' xlink:href='" . $dominios . "/api/languages/1'><![CDATA[link-rewrite]]></language><language id='2'/></link_rewrite>
	<description><![CDATA[<p><span style='font-size:10pt;font-family:Arial;font-style:normal;'></span></p>]]></description>
	<meta_title></meta_title>
	<meta_description></meta_description>
	<meta_keywords></meta_keywords>
<associations>
<categories>
	<category>
	<id/>
	</category>
</categories>
<products>
	<product>
	<id/>
	</product>
</products>
</associations>
</category>
</prestashop>"
                        );
                        $xml_request = $webService->add($opt);
                        $requestArray = (array)$xml_request;
                        $requestArray2 = (array)$requestArray['category'];
                    } catch (Exception $e) {
                        Configuration::updateValue('ecommerce-error1', $e);
                        $this->sincronisarCat($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
                    }
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'sincro_nube` (tipo, id_ecommerce, id_) VALUES("Categoria", ' . $requestArray2['id'] . ',' . $key['catId'] . ');';
                    Db::getInstance()->execute($sql);
                    $id = $key['catId'];
                    $urc2 = '/ed/vin';
                    $fielsc2 = 'tipo=Categoria&nubId=' . $id . '&locId=3&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $responsess22 = $this->peticionesCurl($nube, $urc2, $fielsc2, $name, $pass, $jwt, false, false);
                }
            }
            if ($responsess22 == '') {
                $this->banderaCat = false;
            }
        } catch (Exception $e) {
            Configuration::updateValue('ecommerce-error2', $e);
            $this->sincronisarCat($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
        }
    }

    function sincronisarProducts($name, $pass, $nube, $version, $sucId, $dominios, $jwt, $serviceKey)
    {
        try {
            $urpro = '/articulo/lista';
            $fielsi = 'sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
            $responsepro = $this->peticionesCurl($nube, $urpro, $fielsi, $name, $pass, $jwt, false, false);
            $arrayjson = json_decode($responsepro, true);
            foreach ($arrayjson as $key) {
                if ($key['articulo']['tipo'] == 0) {
                    try {
                        $webService = new PrestaShopWebservice($dominios, $serviceKey, false);
                        $sqll = 'SELECT  id_ecommerce FROM `' . _DB_PREFIX_ . 'sincro_nube` WHERE id_ = ' . $key['articulo']['categoria']['catId'] . ' AND tipo = "Categoria";';
                        $id_defauld = Db::getInstance()->executeS($sqll);
                        $id_cat = $id_defauld[0]['id_ecommerce'];
                        if ($id_cat == null) {
                            $id_cat = 1;
                        }
                        $refe = $key['articulo']['categoria']['departamento']['nombre'];
                        if ($refe == '') {
                            $refe = 'Sin Referencia';
                        }
                        $we = $key['articulo']['peso'];
                        if ($we == '') {
                            $we = 0;
                        }
                        $prices = $key['articulo']['precio1'];
                        $impuesto = $key['articulo']['impuestoList'][0]['impuesto'];
                        // precio 1 * impuesto / 100+1
                        $price = $prices * (($impuesto / 100) + 1);
                        $artStatus = $key['articulo']['status'];
                        if ($key['articulo']['status'] == -1) {
                            $artStatus = 0;
                        }
                        $names = (string)$key['articulo']['descripcion'];
                        $opt = array(
                            'resource' => 'products',
                            'postXml' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<prestashop xmlns:xlink=\"http://www.w3.org/1999/xlink\">
<product>
	<id/>
	<id_manufacturer/>
	<id_supplier/>
	<id_category_default>" . $id_cat . "</id_category_default>
	<new/>
	<cache_default_attribute/>
	<id_default_image>1</id_default_image>
	<id_default_combination/>
	<id_tax_rules_group/>
	<position_in_category/>
	<type/>
	<id_shop_default/>
	<reference>" . $refe . "</reference>
	<supplier_reference/>
	<location/>
	<width/>
	<height/>
	<depth/>
	<weight>" . $we . "</weight>
	<quantity_discount/>
	<ean13/>
	<isbn/>
	<upc/>
	<cache_is_pack/>
	<cache_has_attachments/>
	<is_virtual/>
	<state>1</state>
	<additional_delivery_times/>
	<delivery_in_stock><language id='1'/><language id='2'/></delivery_in_stock>
	<delivery_out_stock><language id='1'/><language id='2'/></delivery_out_stock>
	<on_sale/>
	<online_only/>
	<ecotax/>
	<minimal_quantity>1</minimal_quantity>
	<low_stock_threshold/>
	<low_stock_alert/>
	<price>" . $price . "</price>
	<wholesale_price/>
	<unity/>
	<unit_price_ratio/>
	<additional_shipping_cost/>
	<customizable/>
	<text_fields/>
	<uploadable_files/>
	<active>" . $artStatus . "</active>
	<redirect_type/>
	<id_type_redirected/>
	<available_for_order>1</available_for_order>
	<available_date/>
	<show_condition/>
	<condition/>
	<show_price>1</show_price>
	<indexed/>
	<visibility/>
	<advanced_stock_management/>
	<date_add></date_add>
	<date_upd></date_upd>
	<pack_stock_type>3</pack_stock_type>
	<meta_description><language id='1' xlink:href='" . $dominios . "/api/languages/1'>meta description</language><language id='2'/></meta_description>
	<meta_keywords><language id='1' xlink:href='" . $dominios . "/api/languages/1'>meta keywords1, keywords2, keywords3</language><language id='2'/></meta_keywords>
	<meta_title><language id='1' xlink:href='" . $dominios . "/api/languages/1'>meta title</language><language id='2'/></meta_title>
	<link_rewrite><language id='1' xlink:href='" . $dominios . "/api/languages/1'>link-rewrite</language><language id='2'/></link_rewrite>
	<name><language id='1' xlink:href='" . $dominios . "/api/languages/1'>" . $names . "</language><language id='2'/></name>
	<description><language id='1' xlink:href='" . $dominios . "/api/languages/1'><p><span style='font - size:10pt;font - family:Arial;font - style:normal;'> " . $key['articulo']['caracteristicas'] . " </span></p></language><language id='2'/></description>
	<description_short><language id='1' xlink:href='" . $dominios . "/api/languages/1'></language><language id='2'/><p><span style='font-size:10pt;font-family:Arial;font-style:normal;'> " . $key['articulo']['clave'] . " </span></p></description_short>
	<available_now><language id='1' xlink:href='" . $dominios . "/api/languages/1'>In stock</language><language id='2'/></available_now>
	<available_later><language id='1' xlink:href='" . $dominios . "/api/languages/1'>available_later</language><language id='2'/></available_later>
<associations>
<categories>
	<category>
	<id/>
	</category>
<category><id>2</id></category><category><id>" . $id_cat . "</id></category></categories>
<images>
	<image>
	<id/>
	</image>
</images>
<combinations>
	<combination>
	<id/>
	</combination>
</combinations>
<product_option_values>
	<product_option_value>
	<id/>
	</product_option_value>
</product_option_values>
<product_features>
	<product_feature>
	<id/>
	<id_feature_value/>
	</product_feature>
</product_features>
<tags>
	<tag>
	<id/>
	</tag>
</tags>
<stock_availables>
	<stock_available>
	<id/>
	<id_product_attribute/>
	</stock_available>
</stock_availables>
<accessories>
	<product>
	<id/>
	</product>
</accessories>
<product_bundle>
	<product>
	<id/>
	<quantity/>
	</product>
</product_bundle>
</associations>
</product>
</prestashop>"
                        );
                        $xml_request = $webService->add($opt);

                    } catch (Exception $e) {
                        Configuration::updateValue('ecommerce-error1', $e);
                        $this->sincronisarProducts($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
                    }
                    $requestArray = (array)$xml_request;
                    $requestArray2 = (array)$requestArray['product'];
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'sincro_nube` (tipo, id_ecommerce, id_) VALUES("Articulo", ' . $requestArray2['id'] . ',' . $key['articulo']['artId'] . ');';
                    Db::getInstance()->execute($sql);

                    $articulo_id = $key['articulo']['artId'];
                    $urprov = '/ed/vin';
                    $fielsiv = 'tipo=Articulo&nubId=' . $articulo_id . '&locId=' . $requestArray2['id'] . '&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $responsepro2 = $this->peticionesCurl($nube, $urprov, $fielsiv, $name, $pass, $jwt, false, false);
                    $urprostock = '/inventario/consultaInventarioTotal';

                    $fielsistock = 'artId=' . $key['articulo']['artId'] . '&user=' . $name . '&pass=' . $pass . '&version=' . $version . '&sucId=' . $sucId;
                    $responseprostock = $this->peticionesCurl($nube, $urprostock, $fielsistock, $name, $pass, $jwt, false, false);
                    $stok = (array)json_decode($responseprostock);
                    $updateStock = 'UPDATE `' . _DB_PREFIX_ . 'stock_available` SET  quantity = ' . $stok['response'] . ' WHERE id_product = ' . $requestArray2['id'] . ';';
                    Db::getInstance()->executeS($updateStock);
                }
            }
            if ($responsepro2 == '') {
                $this->banderaArt = false;
            }
        } catch (Exception $e) {
            Configuration::updateValue('ecommerce-error2', $e);
            $this->sincronisarProducts($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
        }
    }

    function sincronisarPack($name, $pass, $nube, $version, $sucId, $dominios, $jwt, $serviceKey)
    {
        try {
            $urpack = '/paquete/lista';
            $fielspack = 'sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
            $responsepack = $this->peticionesCurl($nube, $urpack, $fielspack, $name, $pass, $jwt, false, false);
            $arrayjson = json_decode($responsepack, true);
            foreach ($arrayjson as $key) {
                if ($key['articulo']['tipo'] == '2') {
                    try {
                        $webService = new PrestaShopWebservice($dominios, $serviceKey, false);
                        $sqll = 'SELECT  id_ecommerce FROM `' . _DB_PREFIX_ . 'sincro_nube` WHERE id_ = ' . $key['articulo']['categoria']['catId'] . ' AND tipo = "Categoria";';
                        $id_defauld = Db::getInstance()->executeS($sqll);
                        $id_cat = $id_defauld[0]['id_ecommerce'];
                        if ($id_cat == null) {
                            $id_cat = 1;
                        }
                        $refe = $key['articulo']['categoria']['departamento']['nombre'];
                        if ($refe == '') {
                            $refe = 'Sin nombre';
                        }
                        $we = $key['articulo']['peso'];
                        if ($we == '') {
                            $we = 0;
                        }
                        $prices = $key['articulo']['precio1'];
                        $impuesto = $key['articulo']['impuestoList'][0]['impuesto'];
                        // precio 1 * impuesto / 100+1
                        $price = $prices * (($impuesto / 100) + 1);
                        $packStatus = $key['articulo']['status'];
                        if ($key['articulo']['status'] == -1) {
                            $packStatus = 0;
                        }
                        $names = (string)$key['articulo']['descripcion'];
                        $packxmlValue = "";
                        $packxmlId = "";
                        foreach ($key['listaPaquete'] as $item) {
                            $packxmlValue = $packxmlValue . "<product_option_value><id>" . $item['articulo1']['artId'] . "</id></product_option_value>";
                        }
                        $caut = 0;
                        foreach ($key['listaPaquete'] as $assoc) {
                            $sqlls = "SELECT id_ecommerce FROM `" . _DB_PREFIX_ . "sincro_nube` WHERE id_=" . $assoc['articulo1']['artId'] . " AND tipo = 'Articulo'";
                            $id_defaulds = Db::getInstance()->executeS($sqlls);
                            $id_cat = $id_defaulds[0]['id_ecommerce'];
                            if ($caut == 0) {
                                $packxmlId = $packxmlId . "<product><id>" . $id_cat . "</id><quantity>" . $assoc['cantidad'] . "</quantity></product>";
                            } else {
                                $packxmlId = $packxmlId . "<product><id>" . $id_cat . "</id><quantity>" . $assoc['cantidad'] . "</quantity></product>";
                            }
                            $caut++;
                        }
                        $opt = array(
                            'resource' => 'products',
                            'postXml' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<prestashop xmlns:xlink=\"http://www.w3.org/1999/xlink\">
<product>
	<id/>
	<id_manufacturer/>
	<id_supplier/>
	<id_category_default>" . $id_cat . "</id_category_default>
	<new/>
	<cache_default_attribute/>
	<id_default_image>1</id_default_image>
	<id_default_combination/>
	<id_tax_rules_group/>
	<position_in_category/>
	<type/>
	<id_shop_default/>
	<reference>" . $refe . "</reference>
	<supplier_reference/>
	<location/>
	<width/>
	<height/>
	<depth/>
	<weight>" . $we . "</weight>
	<quantity_discount/>
	<ean13/>
	<isbn/>
	<upc/>
	<cache_is_pack/>
	<cache_has_attachments/>
	<is_virtual/>
	<state>1</state>
	<additional_delivery_times/>
	<delivery_in_stock><language id='1'/><language id='2'/></delivery_in_stock>
	<delivery_out_stock><language id='1'/><language id='2'/></delivery_out_stock>
	<on_sale/>
	<online_only/>
	<ecotax/>
	<minimal_quantity>1</minimal_quantity>
	<low_stock_threshold/>
	<low_stock_alert/>
	<price>" . $price . "</price>
	<wholesale_price/>
	<unity/>
	<unit_price_ratio/>
	<additional_shipping_cost/>
	<customizable/>
	<text_fields/>
	<uploadable_files/>
	<active>" . $packStatus . "</active>
	<redirect_type/>
	<id_type_redirected/>
	<available_for_order>1</available_for_order>
	<available_date/>
	<show_condition/>
	<condition/>
	<show_price>1</show_price>
	<indexed/>
	<visibility/>
	<advanced_stock_management/>
	<date_add></date_add>
	<date_upd></date_upd>
	<pack_stock_type>3</pack_stock_type>
	<meta_description><language id='1' xlink:href='" . $dominios . "/api/languages/1'>meta description</language><language id='2'/></meta_description>
	<meta_keywords><language id='1' xlink:href='" . $dominios . "/api/languages/1'>meta keywords1, keywords2, keywords3</language><language id='2'/></meta_keywords>
	<meta_title><language id='1' xlink:href='" . $dominios . "/api/languages/1'>meta title</language><language id='2'/></meta_title>
	<link_rewrite><language id='1' xlink:href='" . $dominios . "/api/languages/1'>link-rewrite</language><language id='2'/></link_rewrite>
	<name><language id='1' xlink:href='" . $dominios . "/api/languages/1'>" . $names . "</language><language id='2'/></name>
	<description><language id='1' xlink:href='" . $dominios . "/api/languages/1'><p><span style='font - size:10pt;font - family:Arial;font - style:normal;'> " . $key['articulo']['caracteristicas'] . " </span></p></language><language id='2'/></description>
	<description_short><language id='1' xlink:href='" . $dominios . "/api/languages/1'></language><language id='2'/><p><span style='font-size:10pt;font-family:Arial;font-style:normal;'> " . $key['articulo']['clave'] . " </span></p></description_short>
	<available_now><language id='1' xlink:href='" . $dominios . "/api/languages/1'>In stock</language><language id='2'/></available_now>
	<available_later><language id='1' xlink:href='" . $dominios . "/api/languages/1'>available_later</language><language id='2'/></available_later>
<associations>
<categories>
	<category>
	<id/>
	</category>
<category><id>2</id></category><category><id>" . $id_cat . "</id></category></categories>
<images>
	<image>
	<id/>
	</image>
</images>
<combinations>
	<combination>
	<id/>
	</combination>
</combinations>
<product_option_values>
   " . $packxmlValue . "
</product_option_values>
<product_features>
	<product_feature>
	<id/>
	<id_feature_value/>
	</product_feature>
</product_features>
<tags>
	<tag>
	<id/>
	</tag>
</tags>
<stock_availables>
	<stock_available>
	<id/>
	<id_product_attribute/>
	</stock_available>
</stock_availables>
<accessories>
	<product>
	<id/>
	</product>
</accessories>
<product_bundle>
	" . $packxmlId . "
</product_bundle>
</associations>
</product>
</prestashop>"
                        );
                        $xml_request = $webService->add($opt);
                    } catch (Exception $e) {
                        Configuration::updateValue('ecommerce-error1', $e);
                        $this->sincronisarPack($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
                    }
                    $requestArray = (array)$xml_request;
                    $requestArray2 = (array)$requestArray['product'];
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'sincro_nube` (tipo, id_ecommerce, id_) VALUES("Pack", ' . $requestArray2['id'] . ',' . $key['articulo']['artId'] . ');';
                    Db::getInstance()->execute($sql);

                    $bandera = true;
                    $stockAvailable = array();
                    $quantityArray = array();
                    $variableDeControl = true;
                    $arraySto = (array)$requestArray2['associations'];
                    $arraySto = (array)$arraySto['product_bundle'];
                    $arraySto = (array)$arraySto['product'];
                    foreach ($arraySto as $response) {
                        $response = (array)$response;
                        $stockSQL = 'SELECT quantity FROM `' . _DB_PREFIX_ . 'stock_available` WHERE id_product = ' . $response['id'] . ';';
                        $cantidad = Db::getInstance()->executeS($stockSQL);
                        if ($cantidad[0]['quantity'] >= $response['quantity'] AND $cantidad[0]['quantity'] - $response['quantity'] != 0 AND $cantidad[0]['quantity'] - $response['quantity'] > -1 AND !is_float($cantidad[0]['quantity'] - $response['quantity'])) {
                            array_push($stockAvailable, $cantidad[0]['quantity']);
                            array_push($quantityArray, $response['quantity']);
                        } else {
                            $variableDeControl = false;
                            continue;
                        }
                    }
                    $stockTotal = 0;
                    if ($variableDeControl == false) {
                        $updateStock = 'UPDATE `' . _DB_PREFIX_ . 'stock_available` SET  quantity = 0 WHERE id_product = ' . $requestArray2['id'] . ';';
                        Db::getInstance()->executeS($updateStock);
                    } else {
                        while ($bandera) {
                            $cccon = 0;
                            $stockTotal++;
                            foreach ($stockAvailable as $stock) {
                                if ($stock - $quantityArray[$cccon] >= 0 and $stock - $quantityArray[$cccon] != 0 and $stock - $quantityArray[$cccon] > -1 and !is_float($stock - $quantityArray[$cccon])) {
                                    $stockAvailable[$cccon] = $stockAvailable[$cccon] - $quantityArray[$cccon];
                                } else {
                                    $bandera = false;
                                    continue;
                                }
                                $cccon++;
                            }
                        }
                        $updateStock = 'UPDATE `' . _DB_PREFIX_ . 'stock_available` SET  quantity = ' . $stockTotal . ' WHERE id_product = ' . $requestArray2['id'] . ';';
                        Db::getInstance()->executeS($updateStock);
                    }
                    $articulo_id = $key['articulo']['artId'];
                    $urpackmincular = '/ed/vin';
                    $fielspackvin = 'tipo=Articulo&nubId=' . $articulo_id . '&locId=' . $requestArray2['id'] . '&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . "&version=" . $version;
                    $responsepackvin = $this->peticionesCurl($nube, $urpackmincular, $fielspackvin, $name, $pass, $jwt, false, false);
                }
            }
            if ($responsepackvin == '') {
                $this->banderaPack = false;
            }

        } catch (Exception $e) {
            Configuration::updateValue('ecommerce-error2', $e);
            $this->sincronisarPack($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
        }
    }

    function sincronisarProductsPorEditar($name, $pass, $nube, $version, $sucId, $dominios, $jwt, $serviceKey)
    {
        try {
            $urpro = '/articulo/lista';
            $fielsi = 'sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
            $responsepro = $this->peticionesCurl($nube, $urpro, $fielsi, $name, $pass, $jwt, false, false);
            $arrayjson = json_decode($responsepro, true);
            foreach ($arrayjson as $key) {
                if ($key['articulo']['tipo'] == 0) {
                    $urprostock = '/inventario/consultaInventarioTotal';
                    $fielsistock = 'artId=' . $key['articulo']['artId'] . '&user=' . $name . '&pass=' . $pass . '&version=' . $version . '&sucId=' . $sucId;
                    $responseprostock = $this->peticionesCurl($nube, $urprostock, $fielsistock, $name, $pass, $jwt, false, false);
                    $stok = (array)json_decode($responseprostock);
                    try {
                        $consultaId = 'SELECT id_ecommerce FROM `' . _DB_PREFIX_ . 'sincro_nube` WHERE id_ = ' . $key['articulo']['artId'] . ' AND tipo = "Articulo";';
                        $executes = Db::getInstance()->executeS($consultaId);
                        $idArt = $executes[0]['id_ecommerce'];
                        $webService = new PrestaShopWebservice($dominios, $serviceKey, false);
                        $sqll = 'SELECT  id_ecommerce FROM `' . _DB_PREFIX_ . 'sincro_nube` WHERE id_ = ' . $key['articulo']['categoria']['catId'] . ' AND tipo = "Categoria";';
                        $id_defauld = Db::getInstance()->executeS($sqll);
                        $id_cat = $id_defauld[0]['id_ecommerce'];
                        if ($id_cat == null) {
                            $id_cat = 1;
                        }
                        $refe = $key['articulo']['categoria']['departamento']['nombre'];
                        if ($refe == '') {
                            $refe = 'Sin nombre';
                        }
                        $we = $key['articulo']['peso'];
                        if ($we == '') {
                            $we = 0;
                        }
                        $prices = $key['articulo']['precio1'];
                        $impuesto = $key['articulo']['impuestoList'][0]['impuesto'];
                        // precio 1 * impuesto / 100+1
                        $price = $prices * (($impuesto / 100) + 1);
                        $artStatus = $key['articulo']['status'];
                        if ($key['articulo']['status'] == -1) {
                            $artStatus = 0;
                        }
                        $names = (string)$key['articulo']['descripcion'];
                        $opt = array(
                            'resource' => 'products',
                            'putXml' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<prestashop xmlns:xlink=\"http://www.w3.org/1999/xlink\">
<product>
	<id>" . $idArt . "</id>
	<id_manufacturer/>
	<id_supplier/>
	<id_category_default>" . $id_cat . "</id_category_default>
	<new/>
	<cache_default_attribute/>
	<id_default_image>1</id_default_image>
	<id_default_combination/>
	<id_tax_rules_group/>
	<position_in_category/>
	<type/>
	<id_shop_default/>
	<reference>" . $refe . "</reference>
	<supplier_reference/>
	<location/>
	<width/>
	<height/>
	<depth/>
	<weight>" . $we . "</weight>
	<quantity_discount/>
	<ean13/>
	<isbn/>
	<upc/>
	<cache_is_pack/>
	<cache_has_attachments/>
	<is_virtual/>
	<state>1</state>
	<additional_delivery_times/>
	<delivery_in_stock><language id='1'/><language id='2'/></delivery_in_stock>
	<delivery_out_stock><language id='1'/><language id='2'/></delivery_out_stock>
	<on_sale/>
	<online_only/>
	<ecotax/>
	<minimal_quantity>1</minimal_quantity>
	<low_stock_threshold/>
	<low_stock_alert/>
	<price>" . $price . "</price>
	<wholesale_price/>
	<unity/>
	<unit_price_ratio/>
	<additional_shipping_cost/>
	<customizable/>
	<text_fields/>
	<uploadable_files/>
	<active>" . $artStatus . "</active>
	<redirect_type/>
	<id_type_redirected/>
	<available_for_order>1</available_for_order>
	<available_date/>
	<show_condition/>
	<condition/>
	<show_price>1</show_price>
	<indexed/>
	<visibility/>
	<advanced_stock_management/>
	<date_add></date_add>
	<date_upd></date_upd>
	<pack_stock_type>3</pack_stock_type>
	<meta_description><language id='1' xlink:href='" . $dominios . "/api/languages/1'>meta description</language><language id='2'/></meta_description>
	<meta_keywords><language id='1' xlink:href='" . $dominios . "/api/languages/1'>meta keywords1, keywords2, keywords3</language><language id='2'/></meta_keywords>
	<meta_title><language id='1' xlink:href='" . $dominios . "/api/languages/1'>meta title</language><language id='2'/></meta_title>
	<link_rewrite><language id='1' xlink:href='" . $dominios . "/api/languages/1'>link-rewrite</language><language id='2'/></link_rewrite>
	<name><language id='1' xlink:href='" . $dominios . "/api/languages/1'>" . $names . "</language><language id='2'/></name>
	<description><language id='1' xlink:href='" . $dominios . "/api/languages/1'><p><span style='font - size:10pt;font - family:Arial;font - style:normal;'> " . $key['articulo']['caracteristicas'] . " </span></p></language><language id='2'/></description>
	<description_short><language id='1' xlink:href='" . $dominios . "/api/languages/1'></language><language id='2'/><p><span style='font-size:10pt;font-family:Arial;font-style:normal;'> " . $key['articulo']['clave'] . " </span></p></description_short>
	<available_now><language id='1' xlink:href='" . $dominios . "/api/languages/1'>In stock</language><language id='2'/></available_now>
	<available_later><language id='1' xlink:href='" . $dominios . "/api/languages/1'>available_later</language><language id='2'/></available_later>
<associations>
<categories>
	<category>
	<id/>
	</category>
<category><id>2</id></category><category><id>" . $id_cat . "</id></category></categories>
<images>
	<image>
	<id/>
	</image>
</images>
<combinations>
	<combination>
	<id/>
	</combination>
</combinations>
<product_option_values>
	<product_option_value>
	<id/>
	</product_option_value>
</product_option_values>
<product_features>
	<product_feature>
	<id/>
	<id_feature_value/>
	</product_feature>
</product_features>
<tags>
	<tag>
	<id/>
	</tag>
</tags>
<stock_availables>
	<stock_available>
	<id/>
	<id_product_attribute/>
	</stock_available>
</stock_availables>
<accessories>
	<product>
	<id/>
	</product>
</accessories>
<product_bundle>
	<product>
	<id/>
	<quantity/>
	</product>
</product_bundle>
</associations>
</product>
</prestashop>",
                            'id' => $idArt
                        );
                        $xml_request = $webService->edit($opt);
                    } catch (Exception $e) {
                        Configuration::updateValue('ecommerce-error1', $e);
                        $this->sincronisarProductsPorEditar($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
                    }
                    $requestArray = (array)$xml_request;
                    $requestArray2 = (array)$requestArray['product'];
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'sincro_nube` (tipo, id_ecommerce, id_) VALUES("Articulo", ' . $requestArray2['id'] . ',' . $key['articulo']['artId'] . ');';
                    Db::getInstance()->execute($sql);

                    $updateStock = 'UPDATE `' . _DB_PREFIX_ . 'stock_available` SET  quantity = ' . $stok['response'] . ' WHERE id_product = ' . $requestArray2['id'] . ';';
                    Db::getInstance()->execute($updateStock);
                    $articulo_id = $key['articulo']['artId'];
                    $urprovE = '/ed/ediEditado';
                    $fielsivE = 'tipo=Articulo&nubId=' . $articulo_id . '&locId=' . $requestArray2['id'] . '&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $responsepro2x = $this->peticionesCurl($nube, $urprovE, $fielsivE, $name, $pass, $jwt, false, false);
                }
            }
            if ($responsepro2x == '') {
                $this->banderaEditArt = false;
            }
        } catch (Exception $e) {
            Configuration::updateValue('ecommerce-error2', $e);
            $this->sincronisarProductsPorEditar($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
        }
    }

    function sincronisarDepaPorEditar($name, $pass, $nube, $version, $sucId, $dominios, $jwt, $serviceKey)
    {
        try {
            $ur2 = '/departamento/lista';
            $fiels2 = 'sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
            $responseDep = $this->peticionesCurl($nube, $ur2, $fiels2, $name, $pass, $jwt, false, false);
            $arrayjson = json_decode($responseDep, true);
            foreach ($arrayjson as $key) {
                if ($key['system'] == true) {
                    $id = $key['depId'];
                    $ur2vd = '/ed/ediEditado';
                    $fiels2vd = 'tipo=Departamento&nubId=' . $id . '&locId=3&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $this->peticionesCurl($nube, $ur2vd, $fiels2vd, $name, $pass, $jwt, false, false);
                    continue;
                } else {
                    try {
                        $consulta = 'SELECT id_ecommerce FROM `' . _DB_PREFIX_ . 'sincro_nube` WHERE id_ = ' . $key['depId'] . ' AND  tipo = "Departamento";';
                        $idDep = Db::getInstance()->executeS($consulta);
                        $id_depa = $idDep[0]['id_ecommerce'];
                        $webService = new PrestaShopWebservice($dominios, $serviceKey, false);
                        $sta = $key['status'];
                        if ($key['status'] == -1) {
                            $sta = 0;
                        }
                        $depEdit = $key['nombre'];
                        if ($key['nombre'] == null) {
                            $depEdit = ' Desconocido';
                        }
                        $opt = array(
                            'resource' => 'categories',
                            'putXml' => "<?xml version='1.0' encoding='UTF-8'?>
<prestashop xmlns:xlink='http://www.w3.org/1999/xlink'>
<category>
	<id>" . $id_depa . "</id>
	<id_parent><![CDATA[2]]></id_parent>
	<active><![CDATA[" . $sta . "]]></active>
	<id_shop_default><![CDATA[1]]></id_shop_default>
	<is_root_category><![CDATA[0]]></is_root_category>
	<position><![CDATA[0]]></position>
	<date_add></date_add>
	<date_upd></date_upd>
	<name><language id='1' xlink:href='" . $dominios . "/api/languages/1'><![CDATA[" . $depEdit . "]]></language><language id='2'/></name>
	<link_rewrite><language id='1' xlink:href='" . $dominios . "/api/languages/1'><![CDATA[link-rewrite]]></language><language id='2'/></link_rewrite>
	<description><![CDATA[<p><span style='font-size:10pt;font-family:Arial;font-style:normal;'></span></p>]]></description>
	<meta_title></meta_title>
	<meta_description></meta_description>
	<meta_keywords></meta_keywords>
<associations>
<categories>
	<category>
	<id/>
	</category>
</categories>
<products>
	<product>
	<id/>
	</product>
</products>
</associations>
</category>
</prestashop>",
                            'id' => $id_depa
                        );
                        $xml_request = $webService->edit($opt);
                    } catch (Exception $e) {
                        Configuration::updateValue('ecommerce-error1', $e);
                        $this->sincronisarDepaPorEditar($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
                    }
                    $requestArray = (array)$xml_request;
                    $requestArray2 = (array)$requestArray['category'];
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'sincro_nube` (tipo, id_ecommerce, id_) VALUES("Departamento", ' . $requestArray2['id'] . ',' . $key['depId'] . ');';
                    Db::getInstance()->execute($sql);
                    $ur2vd2 = '/ed/ediEditado';
                    $fiels2vd2 = 'tipo=Departamento&nubId=' . $id . '&locId=' . $requestArray2['id'] . '&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $responseDeps = $this->peticionesCurl($nube, $ur2vd2, $fiels2vd2, $name, $pass, $jwt, false, false);
                }
            }
            if ($responseDeps == '') {
                $this->banderaEditDepa = false;
            }
        } catch (Exception $e) {
            Configuration::updateValue('ecommerce-error2', $e);
            $this->sincronisarDepaPorEditar($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
        }
    }

    function sincronisarCatPorEditar($name, $pass, $nube, $version, $sucId, $dominios, $jwt, $serviceKey)
    {
        try {
            $ur2 = '/categoria/lista';
            $fiels2 = 'sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
            $responseCat = $this->peticionesCurl($nube, $ur2, $fiels2, $name, $pass, $jwt, false, false);
            $arrayjson = json_decode($responseCat, true);
            foreach ($arrayjson as $key) {
                if ($key['system'] == true) {
                    $id = $key['catId'];
                    $ur2vd = '/ed/ediEditado';
                    $fiels2vd = 'tipo=Categoria&nubId=' . $id . '&locId=3&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $this->peticionesCurl($nube, $ur2vd, $fiels2vd, $name, $pass, $jwt, false, false);
                    continue;
                } else {
                    try {
                        $consulta = 'SELECT id_ecommerce FROM `' . _DB_PREFIX_ . 'sincro_nube` WHERE id_ = ' . $key['catId'] . ' AND tipo = "Categoria";';
                        $executes = Db::getInstance()->executeS($consulta);
                        $id_cat = $executes[0]['id_ecommerce'];
                        $webService = new PrestaShopWebservice($dominios, $serviceKey, false);
                        $sql = 'SELECT id_parent FROM `' . _DB_PREFIX_ . 'category` WHERE id_category = ' . $id_cat . ';';
                        $result = Db::getInstance()->executeS($sql);
                        $id_pare = $result[0]['id_parent'];
                        $sta2 = $key['status'];
                        if ($key['status'] == -1) {
                            $sta2 = 0;
                        }
                        $catEdit = $key['nombre'];
                        if ($key['nombre'] == null) {
                            $catEdit = 'Desconocido';
                        }
                        $opt = array(
                            'resource' => 'categories',
                            'putXml' => "<?xml version='1.0' encoding='UTF-8'?>
<prestashop xmlns:xlink='http://www.w3.org/1999/xlink'>
<category>
	<id>" . $id_cat . "</id>
	<id_parent><![CDATA[" . $id_pare . "]]></id_parent>
	<active><![CDATA[" . $sta2 . "]]></active>
	<id_shop_default><![CDATA[1]]></id_shop_default>
	<is_root_category><![CDATA[0]]></is_root_category>
	<position><![CDATA[0]]></position>
	<date_add></date_add>
	<date_upd></date_upd>
	<name><language id='1' xlink:href='" . $dominios . "/api/languages/1'><![CDATA[" . $catEdit . "]]></language><language id='2'/></name>
	<link_rewrite><language id='1' xlink:href='" . $dominios . "/api/languages/1'><![CDATA[link-rewrite]]></language><language id='2'/></link_rewrite>
	<description><![CDATA[<p><span style='font-size:10pt;font-family:Arial;font-style:normal;'></span></p>]]></description>
	<meta_title></meta_title>
	<meta_description></meta_description>
	<meta_keywords></meta_keywords>
<associations>
<categories>
	<category>
	<id/>
	</category>
</categories>
<products>
	<product>
	<id/>
	</product>
</products>
</associations>
</category>
</prestashop>",
                            'id' => $id_cat
                        );
                        $xml_request = $webService->edit($opt);
                    } catch (Exception $e) {
                        Configuration::updateValue('ecommerce-error1', $e);
                        $this->sincronisarCatPorEditar($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
                    }
                    $requestArray = (array)$xml_request;
                    $requestArray2 = (array)$requestArray['category'];
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'sincro_nube` (tipo, id_ecommerce, id_) VALUES("Categoria", ' . $requestArray2['id'] . ',' . $key['catId'] . ');';
                    Db::getInstance()->execute($sql);
                    $id = $key['catId'];
                    $ur2cdp = '/ed/ediEditado';
                    $fiels2cdp = 'tipo=Categoria&nubId=' . $id . '&locId=' . $requestArray2['id'] . '&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $responseCep2 = $this->peticionesCurl($nube, $ur2cdp, $fiels2cdp, $name, $pass, $jwt, false, false);
                }
            }
            if ($responseCep2 == '') {
                $this->banderaEditCat = false;
            }
        } catch (Exception $e) {
            Configuration::updateValue('ecommerce-error2', $e);
            $this->sincronisarCatPorEditar($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
        }
    }

    function sincronisarPaquetePorEditar($name, $pass, $nube, $version, $sucId, $dominios, $jwt, $serviceKey)
    {
        try {
            $urpacked = '/paquete/lista';
            $fielspacked = 'sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
            $responsepacked = $this->peticionesCurl($nube, $urpacked, $fielspacked, $name, $pass, $jwt, false, false);
            $arrayjson = json_decode($responsepacked, true);
            foreach ($arrayjson as $key) {
                if ($key['articulo']['tipo'] == 2) {
                    try {
                        $webService = new PrestaShopWebservice($dominios, $serviceKey, false);
                        $consultaId = 'SELECT id_ecommerce FROM `' . _DB_PREFIX_ . 'sincro_nube` WHERE id_ = ' . $key['articulo']['artId'] . ' AND tipo = "Pack";';
                        $executes = Db::getInstance()->executeS($consultaId);
                        $idArt = $executes[0]['id_ecommerce'];
                        $sqll = 'SELECT  id_ecommerce FROM `' . _DB_PREFIX_ . 'sincro_nube` WHERE id_ = ' . $key['articulo']['categoria']['catId'] . ' AND tipo = "Categoria";';
                        $id_defauld = Db::getInstance()->executeS($sqll);
                        $id_cat = $id_defauld[0]['id_ecommerce'];
                        if ($id_cat == null) {
                            $id_cat = 1;
                        }
                        $refe = $key['articulo']['categoria']['departamento']['nombre'];
                        if ($refe == '') {
                            $refe = 'Sin nombre';
                        }
                        $we = $key['articulo']['peso'];
                        if ($we == '') {
                            $we = 0;
                        }
                        $prices = $key['articulo']['precio1'];
                        $impuesto = $key['articulo']['impuestoList'][0]['impuesto'];
                        // precio 1 * impuesto / 100+1
                        $price = $prices * (($impuesto / 100) + 1);
                        $packStatus = $key['articulo']['status'];
                        if ($key['articulo']['status'] == -1) {
                            $packStatus = 0;
                        }
                        $names = (string)$key['articulo']['descripcion'];
                        $packxmlValue = "";
                        $packxmlId = "";
                        foreach ($key['listaPaquete'] as $item) {
                            $packxmlValue = $packxmlValue . "<product_option_value><id>" . $item['articulo1']['artId'] . "</id></product_option_value>";
                        }
                        $caut = 0;
                        foreach ($key['listaPaquete'] as $assoc) {
                            $sqlls = "SELECT id_ecommerce FROM `" . _DB_PREFIX_ . "sincro_nube` WHERE id_=" . $assoc['articulo1']['artId'] . " AND tipo = 'Articulo'";
                            $id_defaulds = Db::getInstance()->executeS($sqlls);
                            $id_cat = $id_defaulds[0]['id_ecommerce'];
                            if ($caut == 0) {
                                $packxmlId = $packxmlId . "<product><id>" . $id_cat . "</id><quantity>" . $assoc['cantidad'] . "</quantity></product>";
                            } else {
                                $packxmlId = $packxmlId . "<product><id>" . $id_cat . "</id><quantity>" . $assoc['cantidad'] . "</quantity></product>";
                            }
                            $caut++;
                        }
                        $opt = array(
                            'resource' => 'products',
                            'putXml' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<prestashop xmlns:xlink=\"http://www.w3.org/1999/xlink\">
<product>
	<id>$idArt</id>
	<id_manufacturer/>
	<id_supplier/>
	<id_category_default>" . $id_cat . "</id_category_default>
	<new/>
	<cache_default_attribute/>
	<id_default_image>1</id_default_image>
	<id_default_combination/>
	<id_tax_rules_group/>
	<position_in_category/>
	<type/>
	<id_shop_default/>
	<reference>" . $refe . "</reference>
	<supplier_reference/>
	<location/>
	<width/>
	<height/>
	<depth/>
	<weight>" . $we . "</weight>
	<quantity_discount/>
	<ean13/>
	<isbn/>
	<upc/>
	<cache_is_pack/>
	<cache_has_attachments/>
	<is_virtual/>
	<state>1</state >
	<additional_delivery_times/>
	<delivery_in_stock><language id = '1'/><language id = '2'/></delivery_in_stock>
	<delivery_out_stock><language id = '1'/><language id = '2'/></delivery_out_stock>
	<on_sale/>
	<online_only/>
	<ecotax/>
	<minimal_quantity>1</minimal_quantity>
	<low_stock_threshold/>
	<low_stock_alert/>
	<price>" . $price . "</price>
	<wholesale_price/>
	<unity/>
	<unit_price_ratio/>
	<additional_shipping_cost/>
	<customizable/>
	<text_fields/>
	<uploadable_files/>
	<active>" . $packStatus . "</active >
	<redirect_type/>
	<id_type_redirected/>
	<available_for_order>1</available_for_order>
	<available_date/>
	<show_condition/>
	<condition/>
	<show_price>1</show_price>
	<indexed/>
	<visibility/>
	<advanced_stock_management/>
	<date_add></date_add>
	<date_upd></date_upd>
	<pack_stock_type>3</pack_stock_type>
	<meta_description><language id = '1' xlink:href = '" . $dominios . "/api/languages/1'>meta description</language><language id='2'/></meta_description>
	<meta_keywords><language id = '1' xlink:href = '" . $dominios . "/api/languages/1'>meta keywords1, keywords2, keywords3</language><language id ='2'/></meta_keywords>
	<meta_title><language id = '1' xlink:href = '" . $dominios . "/api/languages/1'>meta title </language ><language id='2'/></meta_title>
	<link_rewrite><language id = '1' xlink:href = '" . $dominios . "/api/languages/1'>link-rewrite</language><language id='2'/></link_rewrite>
	<name><language id = '1' xlink:href = '" . $dominios . "/api/languages/1'>" . $names . "</language ><language id ='2'/></name>
	<description><language id = '1' xlink:href = '" . $dominios . "/api/languages/1'><p ><span style = 'font - size:10pt;font - family:Arial;font - style:normal;' > " . $key['articulo']['caracteristicas'] . " </span ></p ></language><language id = '2' /></description>
	<description_short><language id = '1' xlink:href = '" . $dominios . "/api/languages/1'></language><language id='2'/><p ><span style = 'font-size:10pt;font-family:Arial;font-style:normal;' > " . $key['articulo']['clave'] . " </span ></p></description_short>
	<available_now><language id = '1' xlink:href = '" . $dominios . "/api/languages/1'>In stock</language><language id = '2' /></available_now>
	<available_later><language id = '1' xlink:href = '" . $dominios . "/api/languages/1'>available_later</language ><language id = '2'/></available_later>
<associations>
<categories>
	<category>
	<id/>
	</category>
<category><id>2</id></category><category><id>" . $id_cat . "</id></category></categories>
<images>
	<image>
	<id/>
	</image>
</images>
<combinations>
	<combination>
	<id/>
	</combination>
</combinations>
<product_option_values>
" . $packxmlValue . "
</product_option_values>
<product_features>
	<product_feature>
	<id/>
	<id_feature_value/>
	</product_feature>
</product_features>
<tags>
	<tag>
	<id/>
	</tag>
</tags>
<stock_availables>
	<stock_available>
	<id/>
	<id_product_attribute/>
	</stock_available>
</stock_availables>
<accessories>
	<product>
	<id/>
	</product>
</accessories>
<product_bundle>
" . $packxmlId . "
</product_bundle>
</associations>
</product>
</prestashop> ",
                            'id' => $idArt
                        );
                        $xml_request = $webService->edit($opt);
                    } catch (Exception $e) {
                        Configuration::updateValue('ecommerce-error1', $e);
                        $this->sincronisarPaquetePorEditar($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
                    }
                    $requestArray = (array)$xml_request;
                    $requestArray2 = (array)$requestArray['product'];
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'sincro_nube` (tipo, id_ecommerce, id_) VALUES("Articulo", ' . $requestArray2['id'] . ',' . $key['articulo']['artId'] . ');';
                    Db::getInstance()->execute($sql);

                    $bandera = true;
                    $stockAvailable = array();
                    $quantityArray = array();
                    $variableDeControl = true;
                    $arraySto = (array)$requestArray2['associations'];
                    $arraySto = (array)$arraySto['product_bundle'];
                    $arraySto = (array)$arraySto['product'];
                    foreach ($arraySto as $response) {
                        $response = (array)$response;
                        $stockSQL = 'SELECT quantity FROM `' . _DB_PREFIX_ . 'stock_available` WHERE id_product = ' . $response['id'] . ';';
                        $cantidad = Db::getInstance()->executeS($stockSQL);
                        if ($cantidad[0]['quantity'] >= $response['quantity'] AND $cantidad[0]['quantity'] - $response['quantity'] != 0 AND $cantidad[0]['quantity'] - $response['quantity'] > -1 AND !is_float($cantidad[0]['quantity'] - $response['quantity'])) {
                            array_push($stockAvailable, $cantidad[0]['quantity']);
                            array_push($quantityArray, $response['quantity']);
                        } else {
                            $variableDeControl = false;
                            continue;
                        }
                    }
                    $stockTotal = 0;
                    if ($variableDeControl == false) {
                        $updateStock1 = 'UPDATE `' . _DB_PREFIX_ . 'stock_available` SET  quantity = 0 WHERE id_product = ' . $requestArray2['id'] . ';';
                        Db::getInstance()->executeS($updateStock1);
                    } else {
                        while ($bandera) {
                            $cccon = 0;
                            $stockTotal++;
                            foreach ($stockAvailable as $stock) {
                                if ($stock - $quantityArray[$cccon] >= 0 and $stock - $quantityArray[$cccon] != 0 and $stock - $quantityArray[$cccon] > -1 and !is_float($stock - $quantityArray[$cccon])) {
                                    $stockAvailable[$cccon] = $stockAvailable[$cccon] - $quantityArray[$cccon];
                                } else {
                                    $bandera = false;
                                    continue;
                                }
                                $cccon++;
                            }
                        }
                        $updateStock2 = 'UPDATE `' . _DB_PREFIX_ . 'stock_available` SET  quantity = ' . $stockTotal . ' WHERE id_product = ' . $requestArray2['id'] . ';';
                        Db::getInstance()->executeS($updateStock2);
                    }
                    $articulo_id = $key['articulo']['artId'];
                    $urpackmincular = '/ed/ediEditado';
                    $fielspackvin = 'tipo=Articulo&nubId=' . $articulo_id . '&locId=' . $requestArray2['id'] . '&sucId=' . $sucId . '&user=' . $name . '&pass=' . $pass . '&version=' . $version;
                    $responsepackedit = $this->peticionesCurl($nube, $urpackmincular, $fielspackvin, $name, $pass, $jwt, false, false);
                }
            }
            if ($responsepackedit == '') {
                $this->banderaPackPorEditar = false;
            }
        } catch (Exception $e) {
            Configuration::updateValue('ecommerce-error2', $e);
            $this->sincronisarPaquetePorEditar($name, $pass, $nube, $version, $sucId, $dominios, $this->tempBearer, $this->serviceKey);
        }
    }

    function peticionesCurl($url, $peticion, $field, $name, $pass, $jwt, $responseJwt, $arrays)
    {
        try {
            if ($responseJwt === false AND $arrays === false) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url . $peticion,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $field,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization:  ' . $jwt,
                        'cache - control: no - cache'
                    ),
                ));
                $responseD = curl_exec($curl);
                $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($status == 200) {
                    return $responseD;
                }
                if ($status == 401) {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url . '/login/signIn',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HEADER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => 'user=' . $name . '&pass=' . $pass . '&undefined=',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization:  ' . $jwt,
                            'cache - control: no - cache'
                        ),
                    ));
                    $response = curl_exec($curl);
                    $headers = array();
                    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
                    foreach (explode("\r\n", $header_text) as $i => $line)
                        if ($i == 0)
                            $headers['http_code'] = $line;
                        else {
                            list ($key, $value) = explode(': ', $line);
                            $headers[$key] = $value;
                        }
                    $this->tempBearer = $headers['Authorization'];
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url . $peticion,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $field,
                        CURLOPT_HTTPHEADER => array(
                            'Authorization:  ' . $headers['Authorization'],
                            'cache - control: no - cache'
                        ),
                    ));
                    $responses = curl_exec($curl);
                    return $responses;
                }
            }
            if ($responseJwt === true AND $arrays === true) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $field,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization:  ' . $jwt,
                        'cache - control: no - cache'
                    ),
                ));
                $response = curl_exec($curl);
                $headers = array();
                $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
                foreach (explode("\r\n", $header_text) as $i => $line)
                    if ($i == 0)
                        $headers['http_code'] = $line;
                    else {
                        list ($key, $value) = explode(': ', $line);
                        $headers[$key] = $value;
                    }
                $this->tempBearer = $headers['Authorization'];
                return [$headers['Authorization'], $response];
            }
            if ($responseJwt === true AND $arrays === false) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url . $peticion,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $field,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization:  ' . $jwt,
                        'cache - control: no - cache'
                    ),
                ));
                $response = curl_exec($curl);
                $headers = array();
                $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
                foreach (explode("\r\n", $header_text) as $i => $line)
                    if ($i == 0)
                        $headers['http_code'] = $line;
                    else {
                        list ($key, $value) = explode(': ', $line);
                        $headers[$key] = $value;
                    }
                $this->tempBearer = $headers['Authorization'];
                return $headers['Authorization'];
            } else {
                return 'fallo';
            }
        } catch (Exception $e) {
            print_r($e);
        }
    }

}

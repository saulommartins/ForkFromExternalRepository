<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Imprimir Autenticações
* Data de Criação: 27/12/2005

* @author Analista: Lucas Leusin
* @author Desenvolvedor: Cleisson Barboza
* @author Desenvolvedor: Jose Eduardo Porto
*
* $Id: IAppletAutenticacao.class.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* @package framework
* @subpackage componentes

* Casos de uso: uc-02.04.15

*/
include_once ( CLA_APPLET );

class IAppletAutenticacao extends Applet
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;
    /**
        * @access Private
        * @var String
    */
    public $stTextoImpressao;
    /**
        * @access Private
        * @var String
    */
    public $stImpressora;
    /**
        * @access Private
        * @var String
    */
    public $stRedirecionamento;
    /**
        * @access Private
        * @var Integer
    */
    public $inWidth;
    /**
        * @access Private
        * @var Integer
    */
    public $inHeight;

    /**
        * @access Public
        * @param String $valor
    */
    public function setTextoImpressao($valor) { $this->stTextoImpressao       = $valor; }
    /**
        * @access Public
        * @param String $valor
    */
    public function setImpressora($valor) { $this->stImpressora       = $valor; }
    /**
        * @access Public
        * @param String $valor
    */
    public function setRedirecionamento($valor) { $this->stRedirecionamento       = $valor; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setWidth($valor) { $this->inWidth       = $valor; }
    /**
        * @access Public
        * @param Integer $valor
    */

    public function setHeight($valor) { $this->inHeight       = $valor; }
    /**
        * @access Public
        * @return String $valor
    */
    public function getTextoImpressao() { return $this->stTextoImpressao;       }
    /**
        * @access Public
        * @return String $valor
    */
    public function getImpressora() { return $this->stImpressora;       }
    /**
        * @access Public
        * @return String $valor
    */
    public function getRedirecionamento() { return $this->stRedirecionamento;       }
    /**
        * @access Public
        * @return Integer $valor
    */
    public function getWidth() { return $this->inWidth;       }
    /**
        * @access Public
        * @return Integer $valor
    */
    public function getHeight() { return $this->inHeight;       }
    /**
        * Metodo Construtor
        * @access Public
    */
    public function IAppletAutenticacao($obForm)

    {
        parent::Applet();

        $this->obForm = $obForm;

        $this->setCode    ( "Autenticacao.class"              );
        $this->setName    ( "autenticacao");
        $this->setId      ( "autenticacao");
        $this->setRotulo  ( "Impressão");
        $this->setArchive ( CAM_FW_APPLETS."Autenticacao.jar" );
        $this->setHeight  ( $this->getHeight()                );

    }

    public function montaHTML()
    {
        $stHtml = null;
        $this->setWidth   ( 280 );
        $stTexto = "new Array(";

        foreach ($this->getTextoImpressao() as $key => $valor) {
            $stTexto .= "\"". $valor ."\",";
        }

        $stTexto = substr( $stTexto, 0, strlen($stTexto) - 1 ) . ")";

        parent::montaHTML();
        $stHtml .= parent::getHtml()                                           ."                  \n";
        $stHtml .= "<script type=\"text/javascript\">                                               \n";
        $stHtml .= "var texto = ".$stTexto.";                                                      \n";
        $stHtml .= "document.applets['".$this->getName()."'].setTexto(texto);                                           \n";
        $stHtml .= "</script>                                                                      \n";
        $this->setHtml( $stHtml );

    }
}
?>

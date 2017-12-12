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
* Data de Criação: 03/10/2006

* @author Analista:
* @author Desenvolvedor: Lucas Teixeira Stephanou

* @package URBEM
* @subpackage

$Revision: 28037 $
$Name$
$Author: luiz $
$Date: 2008-02-13 15:41:28 -0200 (Qua, 13 Fev 2008) $

* Casos de uso: uc-01.00.00
                uc-03.05.14
*/

include_once ( CLA_BUSCAINNER );

class  IPopUpCGMVinculado extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */

    public $obForm;
    public $inNumCGM;
    public $stTabelaVinculo;
    public $stNomeVinculo;
    public $stCampoVinculo;
    public $stFiltro;
    public $buscaContrato;
    public $stFiltroVinculado;

    /**
        * Metodo Construtor
        * @access Public

    */

    public function IPopUpCGMVinculado($obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo               ('CGM');
        $this->setTitle                ('');
        $this->setId                   ('stNomCGM');
        $this->setNull                 (false);
        $this->setTabelaVinculo        ('sw_cgm');

        $this->obCampoCod->setName     ("inCGM");
        $this->obCampoCod->setId       ("inCGM");
        $this->obCampoCod->setSize     (6 );
        $this->obCampoCod->setMaxLength(10);
        $this->obCampoCod->setAlign    ("left");
        $this->obCampoCod->setExpReg   ('[^0-9]');

        $this->stTipo = 'vinculado';
    }
    public function setTabelaVinculo($valor) { $this->stTabelaVinculo = $valor; }
    public function getTabelaVinculo() { return $this->stTabelaVinculo; }

    public function setCampoVinculo($valor) { $this->stCampoVinculo = $valor; }
    public function getCampoVinculo() { return $this->stCampoVinculo; }

    public function setNomeVinculo($valor) { $this->stNomeVinculo = $valor; }
    public function getNomeVinculo() { return $this->stNomeVinculo; }

    public function setFiltro($valor) { $this->stFiltro = $valor; }
    public function getFiltro() { return $this->stFiltro; }

    public function setFiltroVinculado($valor) { $this->stFiltroVinculado = $valor; }
    public function getFiltroVinculado() { return $this->stFiltroVinculado; }

    public function setBuscaContrato($buscaContrato) { $this->buscaContrato = $buscaContrato;}
    public function getBuscaContrato() { return $this->buscaContrato;}

    public function setTipo($stTipo='geral')
    {
        $this->stTipo = $stTipo;
    }
    public function setNumCGM($inValor) { $this->inNumCGM = $inValor; }

    public function montaHTML()
    {
        $arAux = array();
        $arAux['FLIPopUpCGMVinculado'] = $this->getFiltro();
        $arAux['stFiltroVinculado'] = $this->getFiltroVinculado();
        Sessao::write($this->getId(), $arAux);

        $pgOcul = "'".CAM_GA_CGM_PROCESSAMENTO."OCProcurarCgm.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+document.getElementById('".$this->obCampoCod->getName()."').value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stTabelaVinculo=". $this->getTabelaVinculo(). "&stNomeVinculo=" . $this->getNomeVinculo() . "&stCampoVinculo=".$this->getCampoVinculo() . "&buscaContrato=".$this->getBuscaContrato() . "&stTipo=".$this->stTipo."'";

        if (($this->obCampoCod->getName() == 'inNumResponsavelAnterior') or ($this->obCampoCod->getName() == 'inNumResponsavelNovo')) {
            $this->obCampoCod->obEvento->setOnBlur ( "ajaxJavaScript(".$pgOcul.",'buscaPopup');" );
        } else {
            $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaPopup');" );
        }

        $this->setFuncaoBusca("abrePopUp('" . CAM_GA_CGM_POPUPS . "cgm/FLProcurarCgm.php','".$this->obForm->getName()."&stTabelaVinculo=".$this->getTabelaVinculo()."&stCampoVinculo=".$this->getCampoVinculo()."&stId=".$this->getId()."&buscaContrato=".$this->getBuscaContrato()."', '". $this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");

        parent::montaHTML();
    }
}
?>

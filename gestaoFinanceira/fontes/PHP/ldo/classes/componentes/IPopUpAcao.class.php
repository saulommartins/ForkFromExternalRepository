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
 * Componente IPopUpAcao
 * Data de Criação: 02/12/2008
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

include_once(CLA_BUSCAINNER);

class  IPopUpAcao extends ComponenteBase
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;
    public $obInnerAcao;
    public $stExercicio;

    public function getName()
    {
        return 'IPopUpAcao';
    }

    public function setExibePPA($boValor)
    {
        $this->boExibePPA = $boValor;
    }

    public function getExibePPA()
    {
        return $this->boExibePPA;
    }

    public function setExibePrograma($boValor)
    {
        $this->boExibePrograma = $boValor;
    }

    public function getExibePrograma()
    {
        return $this->boExibePrograma;
    }

    public function setExercicio($boValor)
    {
        $this->stExercicio = $boValor;
    }

    public function getExercicio()
    {
        return $this->stExercicio;
    }

    public function getRotulo()
    {
       return $this->obInnerAcao->getRotulo();
    }

    public function setNumAcao($inNumAcao)
    {
        $this->inNumAcao = $inNumAcao;
    }

    public function setNull($boNull)
    {
        $this->obInnerAcao->setNull($boNull);
    }

    /**
    * Metodo Construtor
    * @access Public
    */
    public function IPopUpAcao($obForm)
    {
        $this->obInnerAcao = new BuscaInner();

        $this->obForm = $obForm;

        $this->obInnerAcao->setRotulo('Acao');
        $this->obInnerAcao->setTitle('Informe o Acao.');
        $this->obInnerAcao->setId('stNomAcao');

        $this->obInnerAcao->obCampoCod->setId('inNumAcao');
        $this->obInnerAcao->obCampoCod->setName('inNumAcao');
        $this->obInnerAcao->obCampoCod->setSize(10);
        $this->obInnerAcao->obCampoCod->setMaxLength(9);
        $this->obInnerAcao->obCampoCod->setAlign('left');

        $this->obHdnPPA = new Hidden;
        $this->obHdnPPA->setName('inCodPPA');
        $this->obHdnPPA->setId('inCodPPA');

        $this->obHdnCodAcao = new Hidden;
        $this->obHdnCodAcao->setName('inCodAcaoPPA');
        $this->obHdnCodAcao->setId('inCodAcaoPPA');
    }

    public function geraFormulario(&$obFormulario)
    {
        if ($this->inNumAcao) {
            $obRPPAManterAcao  = new RPPAManterAcao();
            $obVPPAManterAcao  = new VPPAManterAcao($obRPPAManterAcao);
            $param['inNumAcao'] = $this->inNumAcao;

            $rsAcao = new RecordSet();
            $rsAcao = $obVPPAManterAcao->listaAcao($param);

            if (!$rsAcao->eof()) {
                $this->obInnerAcao->setValue( $rsAcao->getCampo('identificacao'));
                $this->obInnerAcao->obCampoCod->setValue($this->inNumAcao);
            }
        }

        $this->obInnerAcao->setFuncaoBusca("abrePopUp('".CAM_GF_LDO_POPUPS."acao/FLProcurarAcao.php','".$this->obForm->getName()."','".$this->obInnerAcao->obCampoCod->getName()."','".$this->obInnerAcao->getId()."','".(bool) $this->boExibePrograma."','".Sessao::getId()."','800','550');");

        $this->obInnerAcao->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_LDO_POPUPS.'acao/OCProcurarAcao.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerAcao->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerAcao->getId()."&boExibePrograma=".(bool) $this->boExibePrograma."&stNomForm=".$this->obForm->getName()."&inNumAcao='+this.value, 'listaAcao' );");

        $obFormulario->addHidden($this->obHdnCodAcao);
        $obFormulario->addComponente($this->obInnerAcao);
    }

    public function montaHTML()
    {
        $sessao = $_SESSION ['sessao'];

        $this->obInnerAcao->setFuncaoBusca("abrePopUp('" . CAM_GF_LDO_POPUPS . "acao/FLProcurarAcao.php','".$this->obForm->getName()."', '". $this->obInnerAcao->obCampoCod->getName() ."','". $this->obInnerAcao->getId() . "','','" . Sessao::getId() .	"','800','550');");

        $this->obInnerAcao->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_LDO_POPUPS.'acao/OCProcurarAcao.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerAcao->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerAcao->getId()."&stNomForm=".$this->obForm->getName()."&inNumAcao='+this.value, 'listaAcao' );");

        $this->obInnerAcao->montaHTML();
    }

    public function getHTML()
    {
        return $this->obInnerAcao->getHTML();
    }
}

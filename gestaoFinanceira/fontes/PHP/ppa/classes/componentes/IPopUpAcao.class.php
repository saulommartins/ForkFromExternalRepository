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
* Arquivo de popup de busca de Acaos
* Data de Criação: 02/12/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Jânio Eduardo Vasconcellos de Magalhães
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
    public function setExibeDiagnostico($boValor)
    {
        $this->boExibeDiagnostico = $boValor;
    }
    public function getExibeDiagnostico()
    {
        return $this->boExibeDiagnostico;
    }
    public function setExibeObjetivo($boValor)
    {
        $this->boExibeObjetivo = $boValor;
    }
    public function getExibeObjetivo()
    {
        return $this->boExibeObjetivo;
    }
    public function setExibeDiretrizes($boValor)
    {
        $this->boExibeDiretrizes = $boValor;
    }
    public function getExibeDiretrizes()
    {
        return $this->boExibeDiretrizes;
    }
    public function setExibePublico($boValor)
    {
        $this->boExibePublico = $boValor;
    }
    public function getExibePublico()
    {
        return $this->boExibePublico;
    }
    public function setExibeNatureza($boValor)
    {
        $this->boExibeNatureza = $boValor;
    }
    public function getExibeNatureza()
    {
        return $this->boExibeNatureza;
    }
    public function getRotulo()
    {
       return $this->obInnerAcao->getRotulo();
    }
    public function setCodAcao($inCodAcao)
    {
        $this->inCodAcao = $inCodAcao;
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

        $this->obInnerAcao->obCampoCod->setId('inCodAcao');
        $this->obInnerAcao->obCampoCod->setName('inCodAcao');
        $this->obInnerAcao->obCampoCod->setSize(10);
        $this->obInnerAcao->obCampoCod->setMaxLength(9);
        $this->obInnerAcao->obCampoCod->setAlign('left');

        $this->obLblPPA = new Label;
        $this->obLblPPA->setRotulo('PPA' );
        $this->obLblPPA->setName('lbCodPPA');
        $this->obLblPPA->setId('lbCodPPA');
        $this->obLblPPA->setValue("&nbsp;");

        $this->obLblPrograma = new Label;
        $this->obLblPrograma->setRotulo('Programa' );
        $this->obLblPrograma->setName('lbCodPrograma');
        $this->obLblPrograma->setId('lbCodPrograma');
        $this->obLblPrograma->setValue("&nbsp;");

        $this->obLblDiagnostico = new Label;
        $this->obLblDiagnostico->setRotulo('Diagnostico' );
        $this->obLblDiagnostico->setName('lbCodDiagnostico');
        $this->obLblDiagnostico->setId('lbCodDiagnostico');
        $this->obLblDiagnostico->setValue("&nbsp;");

        $this->obLblObjetivo = new Label;
        $this->obLblObjetivo->setRotulo('Objetivo' );
        $this->obLblObjetivo->setName('lbCodObjetivo');
        $this->obLblObjetivo->setId('lbCodObjetivo');
        $this->obLblObjetivo->setValue("&nbsp;");

        $this->obLblDiretrizes = new Label;
        $this->obLblDiretrizes->setRotulo('Diretrizes' );
        $this->obLblDiretrizes->setName('lbCodDiretrizes');
        $this->obLblDiretrizes->setId('lbCodDiretrizes');
        $this->obLblDiretrizes->setValue("&nbsp;");

        $this->obLblPublico = new Label;
        $this->obLblPublico->setRotulo('Publico' );
        $this->obLblPublico->setName('lbCodPublico');
        $this->obLblPublico->setId('lbCodPublico');
        $this->obLblPublico->setValue("&nbsp;");

        $this->obLblNatureza = new Label;
        $this->obLblNatureza->setRotulo('Natureza' );
        $this->obLblNatureza->setName('lbCodNatureza');
        $this->obLblNatureza->setId('lbCodNatureza');
        $this->obLblNatureza->setValue("&nbsp;");

        $this->obHdnPPA = new Hidden;
        $this->obHdnPPA->setName('inCodPPA');
        $this->obHdnPPA->setId('inCodPPA');
    }

    public function geraFormulario(&$obFormulario)
    {
        if ($this->inCodAcao) {
            $obRPPAManterAcao  = new RPPAManterAcao();
            $obVPPAManterAcao  = new VPPAManterAcao($obRPPAManterAcao);
            $param['inCodAcao'] = $this->inCodAcao;

            $rsAcao = new RecordSet();
            $rsAcao = $obVPPAManterAcao->listaAcao($param);

            if (!$rsAcao->eof()) {
                $this->obInnerAcao->setValue( $rsAcao->getCampo('identificacao'));
                $this->obInnerAcao->obCampoCod->setValue($this->inCodAcao);

                if ($this->boExibePrograma) {
                    $this->obLblPrograma->setValue($rsAcao->getCampo('indentificador'));
                    $this->obLblDiagnostico->setValue($rsAcao->getCampo('diagnostico'));
                    $this->obLblObjetivo->setValue($rsAcao->getCampo('objetivo'));
                    $this->obLblDiretrizes->setValue($rsAcao->getCampo('diretriz'));
                    $this->obLblPublico->setValue($rsAcao->getCampo('publico'));
                    $this->obLblNatureza->setValue($rsAcao->getCampo('continuo'));
                }
            }
        }

        $this->obInnerAcao->setFuncaoBusca("abrePopUp('".CAM_GF_PPA_POPUPS."acao/FLProcurarAcao.php','".$this->obForm->getName()."','".$this->obInnerAcao->obCampoCod->getName()."','".$this->obInnerAcao->getId()."','".(bool) $this->boExibePrograma."','".Sessao::getId()."','800','550');");

        $this->obInnerAcao->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_PPA_POPUPS.'acao/OCProcurarAcao.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerAcao->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerAcao->getId()."&boExibePrograma=".(bool) $this->boExibePrograma."&stNomForm=".$this->obForm->getName()."&inCodAcao='+this.value, 'listaAcao' );");
        $obFormulario->addComponente($this->obInnerAcao);
        if ($this->boExibePrograma) {
            $obFormulario->addComponente($this->obLblPrograma);
            $obFormulario->addComponente($this->obLblDiagnostico);
            $obFormulario->addComponente($this->obLblObjetivo);
            $obFormulario->addComponente($this->obLblDiretrizes);
            $obFormulario->addComponente($this->obLblPublico);
            $obFormulario->addComponente($this->obLblNatureza);
        }
    }

    public function montaHTML()
    {
        $sessao = $_SESSION ['sessao'];

        $this->obInnerAcao->setFuncaoBusca("abrePopUp('" . CAM_GF_PPA_POPUPS . "acao/FLProcurarAcao.php','".$this->obForm->getName()."', '". $this->obInnerAcao->obCampoCod->getName() ."','". $this->obInnerAcao->getId() . "','','" . Sessao::getId() .	"','800','550');");

        $this->obInnerAcao->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_PPA_POPUPS.'acao/OCProcurarAcao.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerAcao->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerAcao->getId()."&stNomForm=".$this->obForm->getName()."&inCodAcao='+this.value, 'listaAcao' );");

        $this->obInnerAcao->montaHTML();
    }

    public function getHTML()
    {
        return $this->obInnerAcao->getHTML();
    }
}
?>

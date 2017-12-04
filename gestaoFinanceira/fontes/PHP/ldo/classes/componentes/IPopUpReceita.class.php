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
 * Pagina de PopUp tipo do uc-02.10.04
 * Data de Criação: 17/02/209
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author <analista> Bruno Ferreira Santos <bruno.ferreira>
 * @author <desenvolvedor> Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage ldo
 * @uc uc-02.10.04
 */

include_once(CLA_BUSCAINNER);
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"                       );

class  IPopUpReceita extends ComponenteBase
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;
    public $obInnerReceita;
    public $obHdnValorReceita;
    public $obHdnNumReceita;
    public $boExibeRecurso;
    public $boExibeValorReceita;

    public function getName()
    {
        return 'IPopUpReceita';
    }

    public function setExibePPA($boValor)
    {
        $this->boExibePPA = $boValor;
    }

    public function setExibeRecurso($boValor)
    {
        $this->boExibeRecurso = $boValor;
    }

    public function setExibeValorReceita($boValor)
    {
        $this->boExibeValorReceita = $boValor;
    }

    public function getRotulo()
    {
       return $this->obInnerReceita->getRotulo();
    }

    public function setNull($boNull)
    {
        $this->obInnerAcao->setNull($boNull);
    }

    /**
    * Metodo Construtor
    * @access Public
    */
    public function IPopUpReceita($obForm)
    {
        $obROrcamentoReceita                         = new ROrcamentoReceita;
        $stMascaraRubrica = $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaMascara();
        $this->obInnerReceita = new BuscaInner();

        $this->obForm = $obForm;

        $this->obInnerReceita->setRotulo('Receita');
        $this->obInnerReceita->setTitle('Informe o Receita.');
        $this->obInnerReceita->setId('stNomReceita');

        $this->obInnerReceita->obCampoCod->setId('inNumEstruturalReceita');
        $this->obInnerReceita->obCampoCod->setName('inNumEstruturalReceita');
        $this->obInnerReceita->obCampoCod->setSize(strlen($stMascaraRubrica));
        $this->obInnerReceita->obCampoCod->setMaxLength(strlen($stMascaraRubrica));
        $this->obInnerReceita->obCampoCod->setAlign('left');
        $this->obInnerReceita->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");

        $obHdnMascClassificacao = new Hidden;
        $obHdnMascClassificacao->setName ( "stMascClassificacao" );
        $obHdnMascClassificacao->setValue( $stMascaraRubrica );

        $this->obHdnNumReceita = new Hidden();
        $this->obHdnNumReceita->setName('inNumReceita');
        $this->obHdnNumReceita->setID('inNumReceita');

    }

    public function geraFormulario(&$obFormulario)
    {
         if ($this->boExibeValorReceita) {
            $this->obHdnValorReceita = new Hidden();
            $this->obHdnValorReceita->setName('flValorReceita');
            $this->obHdnValorReceita->setID('flValorReceita');
            $this->obHdnValorReceita->setValue(0.0);
        }

        if ($this->inNumReceita) {
            $obRPPAManterReceita  = new RPPAManterReceita();
            $obVPPAManterReceita  = new VPPAManterReceita($obRPPAManterReceita);
            $param['inNumReceita'] = $this->inNumReceita;

            $rsReceita = new RecordSet();
            $rsReceita = $obVPPAManterReceita->listaReceita($param);
        }

        $this->obInnerReceita->setFuncaoBusca("abrePopUp('".CAM_GF_LDO_POPUPS."receita/FLProcurarReceita.php?boExibeValorReceita=".$this->boExibeValorReceita."&','".$this->obForm->getName()."','".$this->obInnerReceita->obCampoCod->getName()."','".$this->obInnerReceita->getId()."','".(bool) $this->boExibeRecurso."','".Sessao::getId()."','800','550');");

        $this->obInnerReceita->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_LDO_POPUPS.'receita/OCProcurarReceita.php?boExibeValorReceita='.$this->boExibeValorReceita.'&'.Sessao::getId()."&stNomCampoCod=".$this->obInnerReceita->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerReceita->getId()."&boExibeRecurso=".(bool) $this->boExibeRecurso."&stNomForm=".$this->obForm->getName()."&inNumEstruturalReceita='+this.value, 'listaReceita' );");

        $obFormulario->addComponente($this->obInnerReceita);
        $obFormulario->addHidden($this->obHdnNumReceita);

        if ($this->boExibeValorReceita) {
            $obFormulario->addHidden($this->obHdnValorReceita);
        }
    }

    public function montaHTML()
    {
        $sessao = $_SESSION['sessao'];

        $this->obInnerReceita->setFuncaoBusca("abrePopUp('" . CAM_GF_LDO_POPUPS . "receita/FLProcurarReceita.php','".$this->obForm->getName()."', '". $this->obInnerReceita->obCampoCod->getName() ."','". $this->obInnerReceita->getId() . "','".(bool) $this->boExibeRecurso."','" . Sessao::getId() . "','800','550');");

        $this->obInnerReceita->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".CAM_GF_LDO_POPUPS.'receita/OCProcurarReceita.php?'.Sessao::getId()."&stNomCampoCod=".$this->obInnerReceita->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerReceita->getId()."&stNomForm=".$this->obForm->getName()."&inNumReceita='+this.value, 'listaReceita');");

        $this->obInnerReceita->montaHTML();
    }

    public function getHTML()
    {
        return $this->obInnerReceita->getHTML();
    }
}

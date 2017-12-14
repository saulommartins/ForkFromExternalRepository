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
    * Classe interface para Filtro de Tipo de Folhas
    * Data de Criação: 24/08/2007

    * @author Analista: Diego Lemos de Souza
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class IFiltroTipoFolha extends Objeto
{
/**
   * @access Private
   * @var Object
*/
var $obSpnTipoFolha;
var $obHdnTipoFolha;
var $obTxtTipoCalculo;
var $obCmbTipoCalculo;
var $boMostraDesdobramento;
var $stDesdobramentoFolhas;
var $stValorPadrao;
var $boMostraAcumularSalCompl;

function setMostraDesdobramento($valor,$stFolhas="T")
{
    $this->boMostraDesdobramento = $valor;
    $this->stDesdobramentoFolhas = $stFolhas;
}

function getMostraDesdobramento() {return $this->boMostraDesdobramento;}

function getDesdobramentoFolhas() {return $this->stDesdobramentoFolhas;}

function setValorPadrao($stValor) {$this->stValorPadrao=$stValor;}

function getValorPadrao() {return $this->stValorPadrao;}

function setMostraAcumularSalCompl($stValor) {$this->boMostraAcumularSalCompl=$stValor;}

function getMostraAcumularSalCompl() {return $this->boMostraAcumularSalCompl;}
/**
     * Método construtor
     * @access Private
*/
function IFiltroTipoFolha()
{
    $this->obTxtTipoCalculo= new TextBox;
    $this->obTxtTipoCalculo->setRotulo                    ( "Tipo de Cálculo"                                     );
    $this->obTxtTipoCalculo->setTitle                     ( "Selecione o tipo de cálculo."                        );
    $this->obTxtTipoCalculo->setName                      ( "inCodConfiguracao"                                   );
    $this->obTxtTipoCalculo->setSize                      ( 6                                                     );
    $this->obTxtTipoCalculo->setMaxLength                 ( 3                                                     );
    $this->obTxtTipoCalculo->setNull                      ( false                                                 );
    $this->obTxtTipoCalculo->setInteiro                   ( true                                                  );
    $this->obTxtTipoCalculo->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroTipoFolha.php?".Sessao::getId()."&inCodConfiguracao='+document.frm.inCodConfiguracao.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value+'&boDesdobramento=false','gerarSpanTipoFolha' );");

    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php");
    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsConfiguracaoEvento);
    $this->obCmbTipoCalculo = new Select;
    $this->obCmbTipoCalculo->setRotulo                    ( "Tipo de Cálculo"                                     );
    $this->obCmbTipoCalculo->setTitle                     ( "Selecione o tipo de cálculo."                        );
    $this->obCmbTipoCalculo->setName                      ( "stConfiguracao"                                      );
    $this->obCmbTipoCalculo->setStyle                     ( "width: 200px"                                        );
    $this->obCmbTipoCalculo->addOption("","Selecione");
    $this->obCmbTipoCalculo->addOption("0","Complementar");
    $this->obCmbTipoCalculo->setCampoID                   ( "[cod_configuracao]"                                  );
    $this->obCmbTipoCalculo->setCampoDesc                 ( "[descricao]"                                         );
    $this->obCmbTipoCalculo->preencheCombo                ( $rsConfiguracaoEvento                                 );
    $this->obCmbTipoCalculo->setNull                      ( false                                                 );
    $this->obCmbTipoCalculo->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroTipoFolha.php?".Sessao::getId()."&inCodConfiguracao='+document.frm.inCodConfiguracao.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value+'&boDesdobramento=false','gerarSpanTipoFolha' );");

    $this->obSpnTipoFolha = new Span();
    $this->obSpnTipoFolha->setId("spnTipoFolha");

    $this->obHdnTipoFolha = new hiddenEval();
    $this->obHdnTipoFolha->setId("hdnTipoFolha");
}

function atualizarComplementar($inCodPeriodoMovimentacao)
{
    $stJs = "";

    if (trim($inCodPeriodoMovimentacao) != "") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
        $obTFolhaPagamentoComplementar = new TFolhaPagamentoComplementar;
        $stFiltro = " AND complementar.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
        $obTFolhaPagamentoComplementar->recuperaRelacionamento($rsFolhaComplementar,$stFiltro);

        $stJs .= " if (f.inCodComplementar) {							   \n";
        $stJs .= " limpaSelect(f.inCodComplementar,0); 					   \n";
        $stJs .= " jQuery('#inCodComplementar').addOption('','Selecione'); \n";

        while (!$rsFolhaComplementar->eof()) {
            $stJs .= " jQuery('#inCodComplementar').addOption('".$rsFolhaComplementar->getCampo("cod_complementar")."','".$rsFolhaComplementar->getCampo("cod_complementar")."'); \n";
            $rsFolhaComplementar->proximo();
        }

        $stJs .= " } \n";
    }

    return $stJs;
}

/**
    * Monta os combos de competencia
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    if ($this->getMostraDesdobramento()) {
    $this->obTxtTipoCalculo->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroTipoFolha.php?".Sessao::getId()."&inCodConfiguracao='+document.frm.inCodConfiguracao.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value+'&boDesdobramento=true&stDesdobramentoFolhas=".$this->getDesdobramentoFolhas()."','gerarSpanTipoFolha' );");
        $this->obCmbTipoCalculo->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroTipoFolha.php?".Sessao::getId()."&inCodConfiguracao='+document.frm.inCodConfiguracao.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value+'&boDesdobramento=true&stDesdobramentoFolhas=".$this->getDesdobramentoFolhas()."','gerarSpanTipoFolha' );");
    }
    if ($this->getMostraAcumularSalCompl()) {
        $this->obTxtTipoCalculo->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroTipoFolha.php?".Sessao::getId()."&inCodConfiguracao='+document.frm.inCodConfiguracao.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value+'&boDesdobramento=true&boMostraAcumularSalCompl=".$this->getMostraAcumularSalCompl()."','gerarSpanTipoFolha' );");
    $this->obCmbTipoCalculo->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroTipoFolha.php?".Sessao::getId()."&inCodConfiguracao='+document.frm.inCodConfiguracao.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value+'&boDesdobramento=true&boMostraAcumularSalCompl=".$this->getMostraAcumularSalCompl()."','gerarSpanTipoFolha' );");
    }
    if ( $this->getValorPadrao() ) {
        $this->obTxtTipoCalculo->setValue($this->getValorPadrao());
        $this->obCmbTipoCalculo->setValue($this->getValorPadrao());
    }

    $obFormulario->addComponenteComposto($this->obTxtTipoCalculo,$this->obCmbTipoCalculo);
    $obFormulario->addSpan($this->obSpnTipoFolha);
    $obFormulario->addHidden($this->obHdnTipoFolha,true);

    /**********************************************************************************
    *  Carrega o array de refêrencia para o componente de competência atualizar
    *  as devidas complementares de acordo com o periodo de movimentação selecionado
    * *********************************************************************************/
    $arFiltroTipoFolha = Sessao::read("arFiltroTipoFolha");
    if (!is_array($arFiltroTipoFolha)) {
        $arFiltroTipoFolha = array();
    }
    array_push($arFiltroTipoFolha,$this);
    Sessao::write("arFiltroTipoFolha", $arFiltroTipoFolha);
}

}
?>

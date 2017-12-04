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
    * Classe do componente AnoCompetencia
    * Data de Criação: 22/10/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                );

class ISelectAnoCompetencia extends Objeto
{
var $boPreencheCombo;
var $obCmbAnoCompetencia;
var $obTFolhaPagamentoPeriodoMovimentacao;
var $obRecordSet;
var $inValue;
var $stRotulo;
var $boDisabledSession;
var $stComplemento;

/**
    * @access Public
    * @param Boolean $Valor
*/
function setPreencheCombo($valor) { $this->boPreencheCombo  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setAnoCompetenciaCombo($valor) { $this->obCmbAnoCompetencia  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setTFolhaPagamentoPeriodoMovimentacao($valor) { $this->obTFolhaPagamentoPeriodoMovimentacao     = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRecordSet($valor) { $this->obRecordSet     = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setValue($valor) { $this->obCmbAnoCompetencia->setValue($valor); }

function setDisabledSession($valor)
{
    $this->boDisabledSession = $valor;
}

function setCodigoPeriodoMovimentacao($valor)
{
    $this->inCodigoPeriodoMovimentacao = $valor;
}

function setComplemento($valor)
{
    $this->stComplemento = $valor;
}

/**
    * @access Public
    * @return Boolean
*/
function getPreencheCombo() { return $this->boPreencheCombo; }
/**
    * @access Public
    * @return Objeto
*/
function getAnoCompetenciaCombo() { return $this->obCmbAnoCompetencia; }
/**
    * @access Public
    * @return Objeto
*/
function getTFolhaPagamentoPeriodoMovimentacao() { return $this->obTFolhaPagamentoPeriodoMovimentacao; }
/**
    * @access Public
    * @return Objeto
*/
function getRecordSet() { $this->obRecordSet->setPrimeiroElemento(); return $this->obRecordSet; }
/**
    * @access Public
    * @return Integer
*/
function getValue() { return $this->obCmbAnoCompetencia->getValue(); }

function getDisabledSession()
{
    return $this->boDisabledSession;
}

function getCodigoPeriodoMovimentacao()
{
    return $this->inCodigoPeriodoMovimentacao;
}

function getComplemento()
{
    return $this->stComplemento;
}

function ISelectAnoCompetencia($boPreencheCombo=true, $boSeleciona=true)
{
    $this->setPreencheCombo($boPreencheCombo);

    $this->setDisabledSession(false);
    $this->setAnoCompetenciaCombo(new Select);
    $this->obCmbAnoCompetencia->setName                    ( "inAnoCompetencia"                    );
    $this->obCmbAnoCompetencia->setRotulo                  ( "Exercício"                           );
    $this->obCmbAnoCompetencia->setTitle                   ( "Selecione o exercício."              );
    $this->obCmbAnoCompetencia->addOption                  ( "", "Selecione"                       );
    $this->obCmbAnoCompetencia->setStyle                   ( "width: 100px"                        );

    if ( $this->getPreencheCombo() ) {
        $this->obRecordSet = new RecordSet();

        $this->setTFolhaPagamentoPeriodoMovimentacao( new TFolhaPagamentoPeriodoMovimentacao() );
        $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaAnosPeriodoMovimentacao($this->obRecordSet);

        if ($this->obRecordSet->getNumLinhas() > 0) {
            while (!$this->obRecordSet->eof()) {
                if($boSeleciona)
                    if($this->obRecordSet->getCorrente() == 1)
                        $this->obCmbAnoCompetencia->setValue($this->obRecordSet->getCampo('ano'));

                $this->obCmbAnoCompetencia->addOption($this->obRecordSet->getCampo('ano'),$this->obRecordSet->getCampo('ano'));
                $this->obRecordSet->proximo();
            }
        }//
    }
}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $stParametros  = "&stNomeComponente=".$this->obCmbAnoCompetencia->getName();
    $stParametros .= "&inAnoCompetencia".$this->getComplemento()."='+document.frm.inAnoCompetencia".$this->getComplemento().".value+'";
    $stParametros .= "&stComplemento=".$this->getComplemento();

    $stOnChange    = " ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCISelectAnoCompetencia.php?".Sessao::getId()."".$stParametros."','processarAnoCompetencia' );";

    $stOnChangeCombo = $this->obCmbAnoCompetencia->obEvento->getOnChange();

    $this->obCmbAnoCompetencia->obEvento->setOnChange($stOnChange.$stOnChangeCombo);

    $obFormulario->addComponente($this->obCmbAnoCompetencia);

    $this->obRecordSet->setPrimeiroElemento();
    if ($this->obRecordSet->getNumLinhas() > 0) {
        while (!$this->obRecordSet->eof()) {
            if ($this->obRecordSet->getCampo('ano') == $this->obCmbAnoCompetencia->getValue()) {
                $this->setCodigoPeriodoMovimentacao($this->obRecordSet->getCampo('cod_periodo_movimentacao'));
            }
            $this->obRecordSet->proximo();
        }
    }//

    /*********************************************************************
    *  Carrega o array de refêrencia para o componente de lotação
    *  de acordo com o periodo de movimentação selecionado
    *  CUIDADO: Quando existir mais de um componente IFiltroCompetencia
    *           na tela só um pode estar habilitado
    * *******************************************************************/
    if (!$this->getDisabledSession()) {
        $arFiltroAnoCompetencia = array($this);
        Sessao::write("arFiltroAnoCompetencia", $arFiltroAnoCompetencia);
    }

}

}
?>

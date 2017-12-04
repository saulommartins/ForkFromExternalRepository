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
    * Classe de regra para Anexo Detalhamento
    * Data de Criação   : 27/09/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.17
*/

/*
$Log$
Revision 1.7  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                                                             );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioProgramaTrabalho.class.php"                 );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioProgramaTrabalhoBalanco.class.php"          );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                                     );

class ROrcamentoRelatorioAnexoDetalhamento extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoSomatorioProgramaTrabalho;
var $stFiltro;
var $inExercicio;
var $stDataInicial;
var $stDataFinal;
var $stSituacao;
var $stTipoRelatorio;
var $obREntidade;
var $stEntidades;

/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoSomatorioProgramaTrabalho($valor) { $this->obFOrcamentoSomatorioProgramaTrabalho  = $valor; }
function setFiltro($valor) { $this->stFiltro                               = $valor; }
function setExercicio($valor) { $this->inExercicio                            = $valor; }
function setDataInicial($valor) { $this->stDataInicial                          = $valor; }
function setDataFinal($valor) { $this->stDataFinal                            = $valor; }
function setSituacao($valor) { $this->stSituacao                             = $valor; }
function setTipoRelatorio($valor) { $this->stTipoRelatorio                        = $valor; }
function setREntidade($valor) { $this->obREntidade                            = $valor; }
function setEntidades($valor) { $this->stEntidades                            = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFOrcamentoSomatorioProgramaTrabalho() { return $this->obFOrcamentoSomatorioProgramaTrabalho; }
function getFiltro() { return $this->stFiltro                             ; }
function getExercicio() { return $this->inExercicio                          ; }
function getDataInicial() { return $this->stDataInicial                        ; }
function getDataFinal() { return $this->stDataFinal                          ; }
function getSituacao() { return $this->stSituacao                           ; }
function getTipoRelatorio() { return $this->stTipoRelatorio                      ; }
function getREntidade() { return $this->obCodEntidade                        ; }
function getEntidades() { return $this->stEntidades                          ; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexoDetalhamento()
{
    $this->obREntidade                  = new ROrcamentoEntidade;
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')         );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, $stOrder = "")
{
    if ($this->stTipoRelatorio=="orcamento") {
        $this->obFOrcamentoSomatorioProgramaTrabalho= new FOrcamentoSomatorioProgramaTrabalho;
    } else {
        $this->obFOrcamentoSomatorioProgramaTrabalho = new FOrcamentoSomatorioProgramaTrabalhoBalanco;
    }

    if ($this->stTipoRelatorio=="orcamento") {
        $this->obFOrcamentoSomatorioProgramaTrabalho->setDado ("exercicio", $this->inExercicio       );
        $this->obFOrcamentoSomatorioProgramaTrabalho->setDado ("stFiltro", $this->stFiltro           );
    } else {
        $this->obFOrcamentoSomatorioProgramaTrabalho->setDado ("exercicio", $this->inExercicio       );
        $this->obFOrcamentoSomatorioProgramaTrabalho->setDado ("stFiltro", $this->stFiltro           );
        $this->obFOrcamentoSomatorioProgramaTrabalho->setDado ("dataInicial", $this->stDataInicial   );
        $this->obFOrcamentoSomatorioProgramaTrabalho->setDado ("dataFinal", $this->stDataFinal       );
        $this->obFOrcamentoSomatorioProgramaTrabalho->setDado ("stEntidades", $this->stEntidades     );
        $this->obFOrcamentoSomatorioProgramaTrabalho->setDado ("stSituacao", $this->stSituacao       );
    }

    $obErro = $this->obFOrcamentoSomatorioProgramaTrabalho->recuperaTodos( $rsRecordSet, "", "" );
    //$this->obFOrcamentoSomatorioProgramaTrabalho->Debug();

    $inCount = 0;
    $inTotalCorrente = 0;
    $inTotalCapital  = 0;
    $inTotal         = 0;

    $boPrimeiroOrgao = $boPrimeiroUnidade = true;

    while ( !$rsRecordSet->eof() ) {

        if ( $rsRecordSet->getCampo("nivel") == 1 ) {
            if ($boPrimeiroOrgao) {
                $arOrgao = array ();
                $arOrgao["descricao"]   = "TOTAL ORGAO ....";
                $arOrgao["vl_corrente"] = $rsRecordSet->getCampo('vl_corrente');
                $arOrgao["vl_capital"]  = $rsRecordSet->getCampo('vl_capital');
                $arOrgao["vl_total"]    = $rsRecordSet->getCampo('vl_total');

                $inTotalCorrente = $inTotalCorrente + $rsRecordSet->getCampo('vl_corrente');
                $inTotalCapital = $inTotalCapital + $rsRecordSet->getCampo('vl_capital');
                $inTotal = $inTotal + $rsRecordSet->getCampo('vl_total');

                $boPrimeiroOrgao = false;
            } else {
                $arOrcamento[$inCount] = $arUnidade;
                $inCount++;
                $arOrcamento[$inCount] = $arOrgao;
                $inCount++;
                $arOrgao = array ();
                $arOrgao["descricao"]   = "TOTAL ORGAO ....";
                $arOrgao["vl_corrente"] = $rsRecordSet->getCampo('vl_corrente');
                $arOrgao["vl_capital"]  = $rsRecordSet->getCampo('vl_capital');
                $arOrgao["vl_total"]    = $rsRecordSet->getCampo('vl_total');
                $boPrimeiroUnidade = true;

                $inTotalCorrente = $inTotalCorrente + $rsRecordSet->getCampo('vl_corrente');
                $inTotalCapital = $inTotalCapital + $rsRecordSet->getCampo('vl_capital');
                $inTotal = $inTotal + $rsRecordSet->getCampo('vl_total');
            }
        }

        if ( $rsRecordSet->getCampo("nivel") == 2 ) {
            if ($boPrimeiroUnidade) {
                $arUnidade = array ();
                $arUnidade["descricao"]   = "TOTAL UNIDADE ....";
                $arUnidade["vl_corrente"] = $rsRecordSet->getCampo('vl_corrente');
                $arUnidade["vl_capital"]  = $rsRecordSet->getCampo('vl_capital');
                $arUnidade["vl_total"]    = $rsRecordSet->getCampo('vl_total');
                $boPrimeiroUnidade = false;

            } else {
                $arOrcamento[$inCount] = $arUnidade;
                $inCount++;
                $arUnidade = array ();
                $arUnidade["descricao"]   = "TOTAL UNIDADE ....";
                $arUnidade["vl_corrente"] = $rsRecordSet->getCampo('vl_corrente');
                $arUnidade["vl_capital"]  = $rsRecordSet->getCampo('vl_capital');
                $arUnidade["vl_total"]    = $rsRecordSet->getCampo('vl_total');
            }
        }

        $arOrcamento[$inCount]['alinhamento']  = 0;
        $arOrcamento[$inCount]['dotacao']      = $rsRecordSet->getCampo('dotacao');
        $arOrcamento[$inCount]['descricao']    = $rsRecordSet->getCampo('descricao');

        $arOrcamento[$inCount]['vl_corrente']  = $rsRecordSet->getCampo("vl_corrente");
        $arOrcamento[$inCount]['vl_capital']   = $rsRecordSet->getCampo("vl_capital");
        $arOrcamento[$inCount]['vl_total']     = $rsRecordSet->getCampo("vl_total");

        $inCount++;
        $rsRecordSet->proximo();
    }

    $arOrcamento[$inCount] = $arUnidade;
    $inCount++;
    $arOrcamento[$inCount] = $arOrgao;
    $inCount++;

    $arOrcamento[$inCount]['alinhamento']  = 0;
    $arOrcamento[$inCount]['descricao']    = "* TOTAL GERAL ....";
    $arOrcamento[$inCount]['vl_corrente']  = $inTotalCorrente;
    $arOrcamento[$inCount]['vl_capital']   = $inTotalCapital;
    $arOrcamento[$inCount]['vl_total']     = $inTotal;
    $inCount++;

    $arOrcamento[$inCount]["dotacao"] = "* TOTAL = CORRENTE+CAPITAL+RESERVA";

    $inCount++;
    $arOrcamento[$inCount]['descricao']   = "";

    $inCount++;
    $arOrcamento[$inCount]['descricao']   = "ENTIDADES RELACIONADAS";

    $stEntidade = substr(trim($this->getFiltro()),strpos($this->getFiltro(),"("));
    $stEntidade = substr($stEntidade,0,strlen($stEntidade)-1);

    $inEntidades = str_replace("'","",$stEntidade);
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inCount++;
        $this->obREntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREntidade->consultarNomes($rsLista);
        $arOrcamento[$inCount]['descricao']   = "- " . $rsLista->getCampo("entidade");
    }

    $rsRecordSetNovo = new RecordSet;
    $rsRecordSetNovo->preenche( $arOrcamento );
    $rsRecordSet = $rsRecordSetNovo;

    return $obErro;
}
}

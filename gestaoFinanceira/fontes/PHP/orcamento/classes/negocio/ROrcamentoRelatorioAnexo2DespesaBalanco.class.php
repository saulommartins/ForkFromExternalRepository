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
    * Classe de regra para Anexo2Despesa
    * Data de Criação: 18/05/2005

    * @author Analista: Diego Barbosa
    * @author Desenvolvedor: Cleisson da silva Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 32079 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.11
*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDespesaBalanco.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"            );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php"              );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"              );

class ROrcamentoRelatorioAnexo2DespesaBalanco
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoSomatorioDespesaBalanco;
var $stFiltro;
var $inExercicio;
var $inOrgao;
var $inUnidade;
var $stDataInicial;
var $stDataFinal;
var $stSituacao;
var $stEntidades;
var $obREntidade;

/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoSomatorioDespesaBalanco($valor) { $this->obFOrcamentoSomatorioDespesaBalanco  = $valor; }
function setFiltro($valor) { $this->stFiltro                      = $valor; }
function setExercicio($valor) { $this->inExercicio                   = $valor; }
function setOrgao($valor) { $this->inOrgao                       = $valor; }
function setUnidade($valor) { $this->inUnidade                     = $valor; }
function setDataInicial($valor) { $this->stDataInicial                 = $valor; }
function setDataFinal($valor) { $this->stDataFinal                   = $valor; }
function setSituacao($valor) { $this->stSituacao                    = $valor; }
function setEntidades($valor) { $this->stEntidades                   = $valor; }
function setREntidade($valor) { $this->obREntidade                   = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function getFOrcamentoSomatorioDespesaBalanco() { return $this->obFOrcamentoSomatorioDespesaBalanco; }
function getFiltro() { return $this->stFiltro                    ; }
function getExercicio() { return $this->inExercicio                 ; }
function getOrgao() { return $this->inOrgao                     ; }
function getUnidade() { return $this->inUnidade                   ; }
function getDataInicial() { return $this->stDataInicial               ; }
function getDataFinal() { return $this->stDataFinal                 ; }
function getSituacao() { return $this->stSituacao                  ; }
function getEntidades() { return $this->stEntidades                 ; }
function getREntidade() { return $this->obREntidade                 ; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo2DespesaBalanco()
{
    $this->setFOrcamentoSomatorioDespesaBalanco ( new FOrcamentoSomatorioDespesaBalanco   );
    $this->obREntidade                  = new ROrcamentoEntidade;
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')         );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, &$rsCabecalho, &$rsResumo , $stOrder = "")
{
    $arCabecalho = array();
    $arOrgao     = array();
    $arUnidade   = array();
    $arCorrente  = array();
    $arCapital   = array();
    $arContingencia = array();
    $arResumo    = array ();
    $obROrgao    = new ROrcamentoOrgaoOrcamentario;
    $obRUnidade  = new ROrcamentoUnidadeOrcamentaria;

    $obROrgao->setNumeroOrgao ($this->inOrgao);
    $obROrgao->setExercicio ($this->inExercicio);

    $obRUnidade->setNumeroUnidade ($this->inUnidade);
    $obRUnidade->setExercicio ($this->inExercicio);
    $obRUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao ($this->inOrgao);

    if ( ($this->inUnidade) && ($this->inOrgao) ) {  // VERIFICA SE FORAM SETADOS ORGAO E UNIDADE PARA AJUSTAR CABECALHO
        $obROrgao->listar ( $rsOrgao );
        $obRUnidade->listar ( $rsUnidade );

        $arOrgao[0]["classificacao"] = str_pad($this->inOrgao,2,"0",STR_PAD_LEFT);
        $arOrgao[0]["descricao"]     = $rsOrgao->getCampo("nom_orgao");
        $arUnidade[0]["classificacao"] = str_pad("$this->inUnidade",2,"0",STR_PAD_LEFT);
        $arUnidade[0]["descricao"]     = $rsUnidade->getCampo("nom_unidade");

        $arCabecalho = array_merge ($arOrgao, $arUnidade );
    } else
        if ($this->inOrgao) { // VERIFICA SE FOI SETADO ORGAO PARA AJUSTAR CABECALHO
            $obROrgao->listar ( $rsOrgao );

            $arOrgao[0]["classificacao"] = str_pad($this->inOrgao,2,"0",STR_PAD_LEFT);
            $arOrgao[0]["descricao"]     = $rsOrgao->getCampo("nom_orgao");

            $arCabecalho = $arOrgao;
        } else
            if ($this->inUnidade) { // VERIFICA SE FOI SETADO UNIDADE PARA AJUSTAR CABECALHO
                $obRUnidade->listar ( $rsUnidade );

                $arUnidade[0]["classificacao"] = str_pad($this->inUnidade,2,"0",STR_PAD_LEFT);
                $arUnidade[0]["descricao"]     = $rsUnidade->getCampo("nom_unidade");

                $arCabecalho = $arUnidade;
            }

    $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("stDataInicial", $this->stDataInicial);
    $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("stDataFinal", $this->stDataFinal);
    $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("stSituacao", $this->stSituacao);
    $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("stEntidades", $this->stEntidades);
    $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("exercicio", $this->inExercicio);
    $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("stFiltro", $this->stFiltro);
    $stFiltro = "WHERE valor <> 0";
    $obErro = $this->obFOrcamentoSomatorioDespesaBalanco->recuperaTodos( $rsRecordSet, $stFiltro, "" );
    //$this->obFOrcamentoSomatorioDespesaBalanco->Debug();

    $inCount = 0;
    $inCountCorrente = $inTotalCorrente = 0;
    $inCountCapital = $inTotalCapital = 0;
    $inCountContingencia = $inTotalContingencia = 0;

    $inTotal = 0;
    $stClassifCorrente = "";
    while ( !$rsRecordSet->eof() ) {

        $arOrcamento[$inCount]['nivel']         = $rsRecordSet->getCampo('nivel');
        $arOrcamento[$inCount]['classificacao'] = $rsRecordSet->getCampo('classificacao');
        $arOrcamento[$inCount]['descricao']     = $rsRecordSet->getCampo('descricao');
        $arOrcamento[$inCount]['alinhamento']   = $rsRecordSet->getCampo('alinhamento');

        if ( $stClassifCorrente != $rsRecordSet->getCampo('classificacao') ) {
            $stClassifCorrente = $rsRecordSet->getCampo('classificacao');
            $arOrcamento[$inCount]['nivel'] = $rsRecordSet->getCampo('nivel');
        }

        $stColuna = trim($rsRecordSet->getCampo("coluna"));

        $stGrupo  = trim($rsRecordSet->getCampo("classificacao_reduzida"));
        $stGrupo  = explode (".", $stGrupo);
        $stGrupo  = $stGrupo[0];

        $inValor = number_format ($rsRecordSet->getCampo('valor'),2,",",".");

        if ( trim($rsRecordSet->getCampo("nivel")) == 1 ) {
            $inTotal = $inTotal + $rsRecordSet->getCampo("valor");
        }

        if ($stColuna == 'desdobramento') {
            $arOrcamento[$inCount]['valor_d']       = $inValor;
            $arOrcamento[$inCount]['valor_e']       = '';
            $arOrcamento[$inCount]['valor_c']       = '';
        } elseif ($stColuna == 'elemento') {
            $arOrcamento[$inCount]['valor_d']       = '';
            $arOrcamento[$inCount]['valor_e']       = $inValor;
            $arOrcamento[$inCount]['valor_c']       = '';
        } else {

            if ( ($stGrupo == 3) && (trim($rsRecordSet->getCampo("nivel")) == 2) ) {
                $arCorrente[$inCountCorrente]["alinhamento"] = 2;
                $arCorrente[$inCountCorrente]["descricao"]   = $rsRecordSet->getCampo('descricao');
                $arCorrente[$inCountCorrente]["valor"]       = $inValor;
                $inTotalCorrente = $inTotalCorrente + $rsRecordSet->getCampo("valor");
                $inCountCorrente++;
            } else
                if ( ($stGrupo == 4) && (trim($rsRecordSet->getCampo("nivel")) == 2) ) {
                    $arCapital[$inCountCapital]["alinhamento"] = 2;
                    $arCapital[$inCountCapital]["descricao"]   = $rsRecordSet->getCampo('descricao');
                    $arCapital[$inCountCapital]["valor"]       = $inValor;
                    $inTotalCapital = $inTotalCapital + $rsRecordSet->getCampo("valor");
                    $inCountCapital++;
                } else
                    if ( (trim($rsRecordSet->getCampo("nivel")) == 2) && ($stGrupo != 9) ) {
                        $arContingencia[$inCountContingencia]["alinhamento"] = 2;
                        $arContingencia[$inCountContingencia]["descricao"]   = $rsRecordSet->getCampo('descricao');
                        $arContingencia[$inCountContingencia]["valor"]       = $inValor;
                        $inTotalContingencia = $inTotalContingencia + $rsRecordSet->getCampo("valor");
                        $inCountContingencia++;
                    }

            $arOrcamento[$inCount]['valor_d']       = '';
            $arOrcamento[$inCount]['valor_e']       = '';
            $arOrcamento[$inCount]['valor_c']       = $inValor;
        }

       if ( ( $stGrupo == 9) && (trim($rsRecordSet->getCampo("conta")) == (trim($rsRecordSet->getCampo("nivel"))))) {
           $arContingencia[$inCountContingencia]["alinhamento"] = 2;
           $arContingencia[$inCountContingencia]["descricao"]   = $rsRecordSet->getCampo('descricao');
           $arContingencia[$inCountContingencia]["valor"]       = $inValor;
           $inTotalContingencia = $inTotalContingencia + $rsRecordSet->getCampo("valor");
           $inCountContingencia++;
        }

        $inCount++;
        $rsRecordSet->proximo();
    }

    // Monta recordSet do RESUMO
    $inIndiceUltimo = count ( $arCorrente );
    $arCorrente[$inIndiceUltimo]["alinhamento"] = 3;
    $arCorrente[$inIndiceUltimo]["descricao"]   = "Total DESPESA CORRENTES";
    $arCorrente[$inIndiceUltimo]["valor"]       = number_format ($inTotalCorrente,2,",",".");

    $inIndiceUltimo = count ( $arCapital );
    $arCapital[$inIndiceUltimo]["alinhamento"] = 3;
    $arCapital[$inIndiceUltimo]["descricao"]   = "Total DESPESA DE CAPITAL";
    $arCapital[$inIndiceUltimo]["valor"]       = number_format ($inTotalCapital,2,",",".");

    $inIndiceUltimo = count ( $arContingencia );
    $arContingencia[$inIndiceUltimo]["alinhamento"] = 3;
    $arContingencia[$inIndiceUltimo]["descricao"]   = "Total Reserva de Contingencia";
    $arContingencia[$inIndiceUltimo]["valor"]       = number_format ($inTotalContingencia,2,",",".");

    $arResumo = array_merge ( $arCorrente, $arCapital, $arContingencia );

    $inIndiceUltimo = count ( $arResumo );
    $arResumo[$inIndiceUltimo]["alinhamento"] = 4;
    $arResumo[$inIndiceUltimo]["descricao"]   = "T o t a l   G e r a l  ...";
    $arResumo[$inIndiceUltimo]["valor"]       = number_format (($inTotalCapital + $inTotalCorrente + $inTotalContingencia),2,",",".");

    $inIndice = $inIndiceUltimo;

    $inIndice++;
    $arResumo[$inIndice]['nivel']         = 1;
    $arResumo[$inIndice]["alinhamento"]   = 1;
    $arResumo[$inIndice]["descricao"]     = "";

    $inIndice++;
    $arResumo[$inIndice]['nivel']            = 1;
    $arResumo[$inIndice]["alinhamento"]      = 1;
    $arResumo[$inIndice]["descricao"]        = "ENTIDADES RELACIONADAS";

    $inEntidades = str_replace("'","",$this->getEntidades() );
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inIndice++;
        $this->obREntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREntidade->consultarNomes($rsLista);
        $arResumo[$inIndice]['nivel']        = 1;
        $arResumo[$inIndice]["alinhamento"]  = 1;
        $arResumo[$inIndice]["descricao"]    = "- ".$rsLista->getCampo("entidade");;
    }

    $inIndice++;
    $arResumo[$inIndice]['nivel']            = 1;
    $arResumo[$inIndice]["alinhamento"]      = 1;
    $arResumo[$inIndice]["descricao"]        = "";

    //===

    $inIndiceUltimo = count ($arOrcamento);
    $arOrcamento[$inIndiceUltimo]["alinhamento"] = 5;
    $arOrcamento[$inIndiceUltimo]["descricao"]   = "Total .......:";
    $arOrcamento[$inIndiceUltimo]["valor_d"]     = number_format ($inTotal, 2, ",",".");

    $arOrcamento[$inIndiceUltimo+1]["alinhamento"] = 3;
    $arOrcamento[$inIndiceUltimo+1]["descricao"]   = "Total Geral ...:";
    $arOrcamento[$inIndiceUltimo+1]["valor_d"]     = number_format ($inTotal, 2, ",",".");

    $rsRecordSetNovo  = new RecordSet;
    $rsRecordSetNovo2 = new RecordSet;
    $rsRecordSetNovo3 = new RecordSet;

    if ( count ($arCabecalho) > 0 ) {
        $rsRecordSetNovo2->preenche ( $arCabecalho );
    }

    $rsRecordSetNovo->preenche( $arOrcamento );
    $rsRecordSetNovo3->preenche( $arResumo );

    $rsRecordSet = $rsRecordSetNovo;
    $rsCabecalho = $rsRecordSetNovo2;
    $rsResumo    = $rsRecordSetNovo3;

    return $obErro;
}
}

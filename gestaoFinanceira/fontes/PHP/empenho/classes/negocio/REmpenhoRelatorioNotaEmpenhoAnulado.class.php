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
    * Classe de Regra para emissão do relatório
    * Data de Criação   : 07/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoRelatorioNotaEmpenhoAnulado.class.php 65434 2016-05-20 18:32:34Z michel $

    * Casos de uso: uc-02.03.03
                    uc-02.03.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE_RELATORIO;
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAtributoEmpenhoValor.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";

/**
    * Classe de Regra para emissão do Plano de Contas com Banco/Recurso

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class REmpenhoRelatorioNotaEmpenhoAnulado extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stExercicioEmpenho;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodEmpenho;
/**
    * @var Boolean
    * @access Private
*/
var $boImplantado;
/**
    * @var String
    * @access Private
*/
var $stTimestamp;

var $nuSaldoDotacao;
var $boReemitir;
/**
     * @access public
     * @param string $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
     * @access public
     * @param string $valor
*/
function setExercicioEmpenho($valor) { $this->stExercicioEmpenho = $valor; }
/**
     * @access public
     * @param Integer $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade= $valor; }
/**
     * @access public
     * @param Integer $valor
*/
function setCodEmpenho($valor) { $this->inCodEmpenho = $valor; }
/**
     * @access public
     * @param Boolean $valor
*/
function setImplantado($valor) { $this->boImplantado = $valor; }
/**
     * @access public
     * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp = $valor; }

function setSaldoDotacao($valor) { $this->nuSaldoDotacao = $valor; }

function setReemitir($valor) { $this->boReemitir = $valor; }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio; }
/**
     * @access Public
     * @return String
*/
function getExercicioEmpenho() { return $this->stExercicioEmpenho; }
/**
     * @access Public
     * @return Integer
*/
function getCodEntidade() { return $this->inCodEntidade; }
/**
     * @access Public
     * @return Integer
*/
function getCodEmpenho() { return $this->inCodEmpenho;  }
/**
     * @access Public
     * @return Boolean
*/
function getImplantado() { return $this->boImplantado;  }
/**
     * @access Public
     * @return String
*/
function getTimestamp() { return $this->stTimestamp;  }

function getSaldoDotacao() { return $this->nuSaldoDotacao; }
function getReemitir() { return $this->boReemitir; }
/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioNotaEmpenhoAnulado()
{
    parent::PersistenteRelatorio();
    $this->obRCadastroDinamico  = new RCadastroDinamico;
    $this->obREmpenhoEmpenho    = new REmpenhoEmpenho;
    $this->obRCadastroDinamico->setPersistenteValores  ( new TEmpenhoAtributoEmpenhoValor );
    $this->obRCadastroDinamico->setCodCadastro(1);
    $this->obRCadastroDinamico->obRModulo->setCodModulo(10);
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSet , $stOrder = "")
{
    include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
    include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoPreEmpenho.class.php";
    include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";

    $obTEmpenhoEmpenho    = new TEmpenhoEmpenho;
    $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;
    $obROrcamentoDespesa  = new ROrcamentoDespesa;

    $arRecordSet = array();
    $stFiltro    = "";
    $stFiltro   .= " WHERE cod_entidade      = " . $this->inCodEntidade;
    $stFiltro   .= " AND   cod_empenho       = " . $this->inCodEmpenho;
    $stFiltro   .= " AND   exercicio_empenho = '" . $this->stExercicioEmpenho."'";
    $obTEmpenhoEmpenho->setDado( 'exercicio' , $this->getExercicio() );
    if ($this->boImplantado == 't') {
        $obErro = $obTEmpenhoEmpenho->recuperaRelatorioEmpenhoAnuladoImplantado( $rsRecordSet, $stFiltro, $stOrder );
    } else {
        $obErro = $obTEmpenhoEmpenho->recuperaRelatorioEmpenhoAnulado( $rsRecordSet, $stFiltro, $stOrder );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obREmpenhoEmpenho->setExercicio                           ( $this->stExercicioEmpenho );
        $this->obREmpenhoEmpenho->setCodEmpenho                          ( $this->inCodEmpenho );
        $this->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $this->inCodEntidade );
        $this->obREmpenhoEmpenho->consultar();
        $arChaveAtributo =  array( "cod_pre_empenho" => $this->obREmpenhoEmpenho->getCodPreEmpenho(),
                                   "exercicio"       => $this->stExercicioEmpenho );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
        $this->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
    }

    $this->obREmpenhoEmpenho->setTimestamp ( $this->stTimestamp );
    $this->obREmpenhoEmpenho->listarEmpenhoAnulado( $rsEmpenhoAnulado );

    $inNumItemOld = 0;
    while ( !$rsEmpenhoAnulado->eof() ) {
        if ( $inNumItemOld < $rsEmpenhoAnulado->getCampo('num_item') ) {
            $arItemVlAnulado[$rsEmpenhoAnulado->getCampo('num_item')] = $rsEmpenhoAnulado->getCampo('vl_anulado');
        } else {
            break;
        }
        $inNumItemOld = $rsEmpenhoAnulado->getCampo('num_item');

        $arTimeStamp = explode(' ',$rsEmpenhoAnulado->getCampo( "timestamp" ));
        list($ano,$mes,$dia) = explode('-',$arTimeStamp[0]);
        $stDtAnulado = $dia."/".$mes."/".$ano;

        $rsEmpenhoAnulado->proximo();
    }

    if ( !$rsRecordSet->eof() ) {

        //Linha0
        $arLinha0[0]['entidade']       = $rsRecordSet->getCampo('cod_entidade') . ' - ' . $rsRecordSet->getCampo('nom_entidade');
        $arLinha0[0]['cod_entidade']   = $rsRecordSet->getCampo('cod_entidade');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha0);
        $arRecordSet[0] = $rsNewRecord;

        //Linha1
        $arLinha1[0]['Orgao']   = $rsRecordSet->getCampo('num_nom_orgao');
        $arLinha1[0]['Unidade'] = $rsRecordSet->getCampo('num_nom_unidade');
        $arLinha1[0]['Tipo']    = $rsRecordSet->getCampo('nom_tipo_pre_empenho');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha1);
        $arRecordSet[1] = $rsNewRecord;

        //Linha2
        $arLinha2[0]['Dotacao'] = $rsRecordSet->getCampo('dotacao_reduzida'). " - " .$rsRecordSet->getCampo('dotacao_formatada')." - ".$rsRecordSet->getCampo( "nom_conta" );
        $arLinha2[0]['Recurso'] = $rsRecordSet->getCampo('nom_recurso');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha2);
        $arRecordSet[2] = $rsNewRecord;

        //Linha3
        $arLinha3[0]['Credor']  = $rsRecordSet->getCampo('numcgm').' - '.$rsRecordSet->getCampo('nom_cgm');
        $arLinha3[0]['CpfCnpj'] = $rsRecordSet->getCampo('cpf_cnpj');
        $arLinha3[0]['Cgm']     = $rsRecordSet->getCampo('numcgm');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha3);
        $arRecordSet[3] = $rsNewRecord;

        //Linha4
        $arLinha4[0]['Endereco']= $rsRecordSet->getCampo('endereco');
        $arLinha4[0]['Fone']    = $rsRecordSet->getCampo('fone');
        $arLinha4[0]['Cidade']  = $rsRecordSet->getCampo('nom_municipio');
        $arLinha4[0]['Uf']      = $rsRecordSet->getCampo('sigla_uf');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha4);
        $arRecordSet[4] = $rsNewRecord;

        //Linha5
        if ($this->boImplantado != 't') {
            if ( $rsRecordSet->getCampo('cod_autorizacao') != "" ) {
                $arLinha5[0]['Autorizacao'] = $rsRecordSet->getCampo('cod_autorizacao').' / '.$rsRecordSet->getCampo('exercicio_autorizacao');
            } else {
                $arLinha5[0]['Autorizacao'] = "Diversos";
            }
        }
        $arLinha5[0]['Emissao']     = $rsRecordSet->getCampo('dt_empenho');
        $arLinha5[0]['Vencimento']  = $rsRecordSet->getCampo('dt_vencimento');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha5);
        $arRecordSet[5] = $rsNewRecord;

        //Armazena os valores
        $nuValorOrcado = $arLinha6[0]['ValorOrcado'] = $rsRecordSet->getCampo('valor_orcado');

        $obTEmpenhoPreEmpenho->setDado( "exercicio"    , $this->getExercicio());
        $obTEmpenhoPreEmpenho->setDado( "cod_despesa"  , $rsRecordSet->getCampo('dotacao_reduzida') );
        $obTEmpenhoPreEmpenho->setDado( "dt_empenho"   , $stDtAnulado);
        $obTEmpenhoPreEmpenho->setDado( "entidade"     , $this->getCodEntidade());
        $obTEmpenhoPreEmpenho->setDado( "tipo_emissao" , "E" );
        $obErro = $obTEmpenhoPreEmpenho->recuperaSaldoAnteriorDataEmpenho( $rsSaldoAnterior, "", $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $nuSaldoAnterior = $arLinha6[0]['SaldoAnterior'] = $rsSaldoAnterior->getCampo( "saldo_anterior" );
        }

        $obROrcamentoDespesa->obTPeriodo->setTDataFinal( $stDtAnulado );
        $obROrcamentoDespesa->setExercicio( $this->getExercicio() );
        $obROrcamentoDespesa->setCodDespesa( $rsRecordSet->getCampo('dotacao_reduzida') );
        $obErro =  $obROrcamentoDespesa->consultarValorReservaDotacaoPeriodo( $nuVlReserva, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $nuSaldoAnterior += $nuVlReserva;
            $arLinha6[0]['SaldoAnterior'] += $nuVlReserva;
        }

        $stRecurso = $rsRecordSet->getCampo('nom_recurso');
    }
    $inCount=0;
    while ( !$rsRecordSet->eof() ) {
        $arLinha7[$inCount]['Item']         = $rsRecordSet->getCampo('num_item');
        $arLinha7[$inCount]['Quantidade']   = $rsRecordSet->getCampo('quantidade');
        $arLinha7[$inCount]['simbolo']      = $rsRecordSet->getCampo('simbolo');
        $arLinha7[$inCount]['ValorAnulado'] = $arItemVlAnulado[$rsRecordSet->getCampo('num_item')];
        $arLinha7[$inCount]['ValorTotal']   = $rsRecordSet->getCampo('valor_total');
        $nuValorEmpenho                    += $rsRecordSet->getCampo('valor_total');
        $nuValorEmpenhoAnulado             += $arItemVlAnulado[$rsRecordSet->getCampo('num_item')];
        $cod_item = ($rsRecordSet->getCampo('cod_item')!='') ? $rsRecordSet->getCampo('cod_item')." - " : "";
        $stNomItem = trim(strtoupper($rsRecordSet->getCampo('nom_item')." ".$rsRecordSet->getCampo('complemento')));
        $stNomItem = str_replace( chr(10), "", $stNomItem );
        $stNomItem = str_replace( chr(13), " ", $stNomItem );

        $stNomItem = wordwrap( $stNomItem, 53, chr(13) );
        $arNomItem = explode( chr(13), $cod_item.$stNomItem );
        foreach ($arNomItem as $stNomItem) {
            $arLinha7[$inCount]['Especificacao']= $stNomItem;
            $inCount++;
        }
        $rsRecordSet->proximo();
    }
    $rsRecordSet->setPrimeiroElemento();

    // Define o "Valor Anulado" ($nuValorEmpenho) a ser considerado na nota.
    if ($nuValorEmpenhoAnulado < $nuValorEmpenho) $nuValorEmpenho = $nuValorEmpenhoAnulado;

    //Linha6
    if ($this->boImplantado != 't') {
        $arLinha6[0]['ValorOrcado']  = $nuValorOrcado;
        $arLinha6[0]['SaldoAnterior']= $nuSaldoAnterior - $nuValorEmpenho;
        $arLinha6[0]['ValorEmpenho'] = $nuValorEmpenho;
        $arLinha6[0]['SaldoAtual']   = $nuSaldoAnterior;
    }

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha6);
    $rsNewRecord->addFormatacao('ValorOrcado'   ,'NUMERIC_BR');
    $rsNewRecord->addFormatacao('SaldoAnterior' ,'NUMERIC_BR');
    $rsNewRecord->addFormatacao('ValorEmpenho'  ,'NUMERIC_BR');
    $rsNewRecord->addFormatacao('SaldoAtual'    ,'NUMERIC_BR');
    $arRecordSet[6] = $rsNewRecord;

    $arHistorico[0]['Historico'] = $rsRecordSet->getCampo( "cod_historico" ).' - '.$rsRecordSet->getCampo( "nom_historico" );
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche( $arHistorico );
    $arRecordSet[10] = $rsNewRecord;

    //Descricao
    $arDescricao = array();
    $stDescricao = str_replace( chr(10) , "", $rsRecordSet->getCampo('descricao') );
    $stDescricao = wordwrap( $stDescricao , 146, chr(13) );
    $arMotivo = explode( chr(13), $stDescricao );
    foreach ($arMotivo as $stDescricao) {
        $arDesc[1] = $stDescricao;
        $arDescricao[] = $arDesc;
    }
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arDescricao);
    $arRecordSet[11] = $rsNewRecord;

    if (count($arLinha7)) {
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha7);
        $rsNewRecord->addFormatacao('Quantidade'    ,'NUMERIC_BR_4');
        $rsNewRecord->addFormatacao('ValorAnulado'  ,'NUMERIC_BR');
        $rsNewRecord->addFormatacao('ValorTotal'    ,'NUMERIC_BR');
        $arRecordSet[7] = $rsNewRecord;

        $arLinha8[0]['Recurso'] = $stRecurso;
        $arLinha8[0]['Total']   = $nuValorEmpenhoAnulado;
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha8);
        $rsNewRecord->addFormatacao('Total','NUMERIC_BR');
        $arRecordSet[8] = $rsNewRecord;
    }
    $inCount = 0;
    while ( !$rsAtributos->eof() ) {
        $arLinha9[$inCount]['Nome']     = $rsAtributos->getCampo('nom_atributo');
        if ($rsAtributos->getCampo('cod_tipo')==3) {
            $arDescricoes = explode("[][][]",$rsAtributos->getCampo('valor_padrao_desc'));
            $arValores    = explode(",",$rsAtributos->getCampo('valor_padrao'));

            foreach ($arValores as $chave => $valor) {
                if ( $valor == $rsAtributos->getCampo('valor') ) {
                    $arLinha9[$inCount]['Valor']    = $arDescricoes[ $chave ];
                }
            }
        } elseif ($rsAtributos->getCampo('cod_tipo')==4) {
            $arDescricoes   = explode("[][][]",$rsAtributos->getCampo('valor_padrao_desc'));
            $arValores      = explode(",",$rsAtributos->getCampo('valor_desc'));
            $stValor        = "";
            for ($inIndice=0; $inIndice<count($arDescricoes); $inIndice++) {
                $stValor = $arDescricoes[ $arValores[($inIndice-1)] ];
            }
            $arLinha9[$inCount]['Valor']    = $stValor;
        } else {
            $arLinha9[$inCount]['Valor']    = ($rsAtributos->getCampo('valor')?$rsAtributos->getCampo('valor'):$rsAtributos->getCampo('valor_padrao'));
        }
        $inCount++;
        $rsAtributos->proximo();
    }
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha9);
    $arRecordSet[9] = $rsNewRecord;

    $inCount = sizeof( $arRecordSet );
    $rsEmpenhoAnulado->setPrimeiroElemento();
    if ( !$rsEmpenhoAnulado->eof() ) {
        $rsRecordSet = new RecordSet;
        $stDtAnulado = "Empenho anulado em: ".$stDtAnulado;
        $arAnulado[0]['stDtAnulado'] = $stDtAnulado;
        $rsRecordSet->preenche( $arAnulado );
        $arRecordSet[$inCount] = $rsRecordSet;
        $inCount++;
        $arMotivoAnulado = array();
        $stMotivo = wordwrap( $rsEmpenhoAnulado->getCampo( "motivo" ) , 140, chr(13) );
        $arMotivo = explode( chr(13), $stMotivo );
        $i = 0;
        foreach ($arMotivo as $stLinhaMotivo) {
            $arMotivoAnulado[$i]['stMotivo'] = $stLinhaMotivo;
            $i++;
        }
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arMotivoAnulado );
        $arRecordSet[$inCount] = $rsRecordSet;
        $inCount++;
    }
}

}

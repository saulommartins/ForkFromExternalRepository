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
    * Classe de Regra do Relatório de Situação de Empenho
    * Data de Criação   : 16/11/2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 30835 $
    $Name$
    $Autor: $
    $Date: 2007-10-03 18:26:37 -0300 (Qua, 03 Out 2007) $

    * Casos de uso: uc-02.04.10
*/

/*
$Log$
Revision 1.17  2007/10/03 21:25:58  cako
Ticket#10254#

Revision 1.16  2007/09/14 21:36:38  cako
Ticket#10037#

Revision 1.15  2007/07/04 18:13:07  leandro.zis
Bug #9362#

Revision 1.14  2007/05/30 19:25:00  bruce
Bug #9116#

Revision 1.13  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"             );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Extrato Bancario
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaRelatorioExtratoBancario extends PersistenteRelatorio
{
/**
    * @var Integer
    * @access Private
*/
var $inCodPlano;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stEntidade;
/**
    * @var String
    * @access Private
*/
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
/**
    * @var String
    * @access Private
*/
var $stFiltro;

var $boImprimeSemContasSemMov = true;

/**
     * @access Public
     * @param Integer $valor
*/
function setCodPlano($valor) { $this->inCodPlano= $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setEntidade($valor) { $this->stEntidade           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataInicial($valor) { $this->stDataInicial      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro      = $valor; }

/*
    * @access Public
    * @return Integer
*/
function getCodPlano() { return $this->inCodPlano;                      }
/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                      }
/*
    * @access Public
    * @return String
*/
function getEntidade() { return $this->stEntidade;                      }
/*
    * @access Public
    * @return String
*/
function getDataInicial() { return $this->stDataInicial;                      }
/*
    * @access Public
    * @return String
*/
function getDataFinal() { return $this->stDataFinal;                      }
/*
    * @access Public
    * @return String
*/
function getFiltro() { return $this->stFiltro;                      }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaRelatorioExtratoBancario()
{
    $this->obRTesourariaBoletim            = new RTesourariaBoletim;
    $this->obRRelatorio                    = new RRelatorio;
    $this->obRTesourariaBoletim->addArrecadacao();
    $this->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GF_TES_MAPEAMENTO."FTesourariaExtratoBancario.class.php");
    $obFTesourariaExtratoBancario = new FTesourariaExtratoBancario;
    
    $codPlano = $this->getCodPlano()  ;

    $obFTesourariaExtratoBancario->setDado("inCodPlanoInicial"   ,$codPlano[0] );
    $obFTesourariaExtratoBancario->setDado("inCodPlanoFinal"     ,$codPlano[1] );

    $obFTesourariaExtratoBancario->setDado("stExercicio"          ,$this->getExercicio());
    $obFTesourariaExtratoBancario->setDado("stEntidade"           ,$this->getEntidade());
    $obFTesourariaExtratoBancario->setDado("stDataInicial"        ,$this->getDataInicial());
    $obFTesourariaExtratoBancario->setDado("stDataFinal"          ,$this->getDataFinal());

    $obErro = $obFTesourariaExtratoBancario->recuperaDadosBancarios( $rsDadosBancarios, $stFiltroExtratoBancario, $stOrder );
    $arDadosBancarios = array();

    $inCount = 0;
    $in = 0;

    $nuVlTotalArrecadacao             = 0;
    $nuVlTotalEstornoArrecadacao      = 0;
    $nuVlTotalPagamento               = 0;
    $nuVlTotalEstornoPagamento        = 0;
    $nuVlTotalArrecadacaoExtra        = 0;
    $nuVlTotalEstornoArrecadacaoExtra = 0;
    $nuVlTotalPagamentoExtra          = 0;
    $nuVlTotalEstornoPagamentoExtra   = 0;
    $nuVlTotalAplicacoes              = 0;
    $nuVlTotalResgates                = 0;
    $nuVlTotalDepositosRetiradas      = 0;

    while ( !$rsDadosBancarios->eof() ) {
        $stDadosBanco = "CONTA: ";
        $stDadosBanco .= $rsDadosBancarios->getCampo('cod_plano') ." - ". $rsDadosBancarios->getCampo("nom_conta");
        //// buscando o extrato da conta
        $obFTesourariaExtratoBancario->setDado( "stDtInicial","01/01/".$this->getExercicio());
        if (substr($this->getDataInicial(),0,5) == "01/01") {
            $dtAnterior = $this->getDataInicial();
            $obFTesourariaExtratoBancario->setDado( "boMovimentacao", "false" );
        } else {
            $dtInicial = explode("/",$this->getDataInicial());
            $dtAnterior = date("d/m/Y",mktime(0,0,0,$dtInicial[1],$dtInicial[0]-1,$dtInicial[2]));
            $obFTesourariaExtratoBancario->setDado( "boMovimentacao", "false" );
        }
        $obFTesourariaExtratoBancario->setDado( "botcems", "false" );
        if (Sessao::getExercicio() > '2012') {
            $obFTesourariaExtratoBancario->setDado( "botcems", "true" );
        }
        $obFTesourariaExtratoBancario->setDado( "stDtFinal", $dtAnterior );
        $obFTesourariaExtratoBancario->setDado( "inCodPlano", $rsDadosBancarios->getCampo( 'cod_plano' ) );
        $obErro = $obFTesourariaExtratoBancario->recuperaSaldoAnteriorAtual( $rsSaldoAnteriorAtual, $stFiltro, $stOrder );

        $saldoAnterior = $rsSaldoAnteriorAtual->getCampo("fn_saldo_conta_tesouraria");
        $obErro = $obFTesourariaExtratoBancario->recuperaTodos( $rsMovimentacoes, $stFiltro, $stOrder );

        $arMovimentacoes = array();

        $arMovimentacoes[0]["valor"]     = "";
        $arMovimentacoes[0]["data"]      = "";
        $arMovimentacoes[0]["descricao"] = "               SALDO ANTERIOR";
        $arMovimentacoes[0]["saldo"]     = number_format($saldoAnterior,2,',','.');

        $inCount = 1;

        $saldoAtual = $saldoAnterior;

        while ( !$rsMovimentacoes->eof() ) {
            $arMovimentacoes[$inCount]["valor"]     = number_format($rsMovimentacoes->getCampo("valor"),2,',','.');
            $arMovimentacoes[$inCount]["data"]      = $rsMovimentacoes->getCampo("data");
            $saldoAtual = bcadd( $saldoAtual, $rsMovimentacoes->getCampo("valor"), 2 );

            $stDescricao = str_replace(chr(10), '', $rsMovimentacoes->getCampo('descricao'));
            $stDescricao = wordwrap($stDescricao, 70, chr(13));
            $arDescricao = explode(chr(13), $stDescricao);

            $inCountAux = $inCount;
            foreach ($arDescricao as $stDescricao) {
                $arMovimentacoes[$inCount]['descricao'] = $stDescricao;
                $inCount++;
            }

            if ( $stDtOld != $rsMovimentacoes->getCampo("data") ) {
                $arMovimentacoes[$inCountAux-1]["saldo"] = number_format(bcsub($saldoAtual,$rsMovimentacoes->getCampo("valor"), 2 ),2,',','.');
            }
            $stDtOld = $rsMovimentacoes->getCampo("data");

            // Gera Totalizadores
            if ( $rsMovimentacoes->getCampo('situacao') == '2' ) {
                $nuVlTotalEstornoArrecadacao = bcadd( $nuVlTotalEstornoArrecadacao, $rsMovimentacoes->getCampo("valor"), 4 );
            } elseif ( $rsMovimentacoes->getCampo('situacao') == '4' ) {
                $nuVlTotalEstornoPagamento = bcadd( $nuVlTotalEstornoPagamento, $rsMovimentacoes->getCampo("valor"), 4 );
            } elseif ( $rsMovimentacoes->getCampo('situacao') == 'X' ) {
                switch ( $rsMovimentacoes->getCampo('cod_situacao') ) {
                        case '2' : $nuVlTotalEstornoPagamentoExtra   = bcadd( $nuVlTotalEstornoPagamentoExtra  , $rsMovimentacoes->getCampo("valor"), 4 ); break;
                        case '1' : $nuVlTotalEstornoArrecadacaoExtra = bcadd( $nuVlTotalEstornoArrecadacaoExtra, $rsMovimentacoes->getCampo("valor"), 4 ); break;
                    }
            } else {
                if ( $rsMovimentacoes->getCampo('situacao') == '1' ) {
                    $nuVlTotalArrecadacao = bcadd( $nuVlTotalArrecadacao, $rsMovimentacoes->getCampo("valor"), 4 );
                } if ( $rsMovimentacoes->getCampo('situacao') == '3' ) {
                    $nuVlTotalPagamento = bcadd( $nuVlTotalPagamento, $rsMovimentacoes->getCampo("valor"), 4 );
                } if ( strpos($rsMovimentacoes->getCampo('descricao'), 'Pagamento Extra') !== false ) {
                    $nuVlTotalPagamentoExtra     = bcadd( $nuVlTotalPagamentoExtra, $rsMovimentacoes->getCampo("valor"), 4 );
                } if ( strpos($rsMovimentacoes->getCampo('descricao'), 'Arrecadação Extra') !== false ) {
                    $nuVlTotalArrecadacaoExtra   = bcadd( $nuVlTotalArrecadacaoExtra, $rsMovimentacoes->getCampo("valor"), 4 );
                } if ( strpos($rsMovimentacoes->getCampo('descricao'), 'Aplicação') !== false ) {
                    $nuVlTotalAplicacoes         = bcadd( $nuVlTotalAplicacoes, $rsMovimentacoes->getCampo("valor"), 4 );
                } if ( strpos($rsMovimentacoes->getCampo('descricao'), 'Resgate') !== false ) {
                    $nuVlTotalResgates           = bcadd( $nuVlTotalResgates, $rsMovimentacoes->getCampo("valor"), 4 );
                } if ( strpos($rsMovimentacoes->getCampo('descricao'), 'Depósito/Retirada') !== false ) {
                    $nuVlTotalDepositosRetiradas = bcadd( $nuVlTotalDepositosRetiradas, $rsMovimentacoes->getCampo("valor"), 4 );
                }
            }

            $rsMovimentacoes->proximo();
        }

        $arMovimentacoes[$inCount]["valor"]     = "";
        $arMovimentacoes[$inCount]["data"]      = "";
        $arMovimentacoes[$inCount]["descricao"] = "               SALDO ATUAL";
        $arMovimentacoes[$inCount]["saldo"]     = number_format($saldoAtual,2,',','.');

        $boContaTemMovimentacao = ( $rsMovimentacoes->getNumLinhas() > 0 ) ? true : false;
        // Para forçar caso for informada apenas uma conta e não gerar um relatório em branco.
        if ($rsDadosBancarios->getNumLinhas() == 1) {
            $boContaTemMovimentacao = true;
            $this->boImprimeContasSemMov = true;
        }
        if ( ($this->boImprimeContasSemMov || $boContaTemMovimentacao) ) {
           $arDadosBancarios[$in]["dados_banco"] = $stDadosBanco;
           $arDadosBancarios[$in]['movimentacao'] = $arMovimentacoes;
           $in++;
        }
        $rsDadosBancarios->proximo();
    }

    $nuVlTotalLiquidoArrecadacao      = bcadd( $nuVlTotalArrecadacao, $nuVlTotalEstornoArrecadacao, 4);
    $nuVlTotalLiquidoPagamento        = bcadd( $nuVlTotalPagamento, $nuVlTotalEstornoPagamento, 4);
    $nuVlTotalLiquidoArrecadacaoExtra = bcadd( $nuVlTotalArrecadacaoExtra, $nuVlTotalEstornoArrecadacaoExtra, 4);
    $nuVlTotalLiquidoPagamentoExtra   = bcadd( $nuVlTotalPagamentoExtra, $nuVlTotalEstornoPagamentoExtra, 4);

    $inCount = 0;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Arrecadações Orçamentárias";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalArrecadacao, 2, ',', '.');
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "Arrecadação Orçamentária Líquida";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoArrecadacao, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Estorno de Arrecadações Orçamentárias";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalEstornoArrecadacao, 2, ',', '.');
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Arrecadações Extra-Orçamentárias";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalArrecadacaoExtra, 2, ',', '.');
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "Arrecadação Extra-Orçamentária Líquida";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoArrecadacaoExtra, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Estorno de Arrecadações Extra-Orçamentárias";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalEstornoArrecadacaoExtra, 2, ',', '.');
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Pagamentos Orçamentários";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalPagamento, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "Pagamento Orçamentário Líquido";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoPagamento, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Estorno de Pagamentos Orçamentários";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalEstornoPagamento, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Pagamentos Extra-Orçamentários";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalPagamentoExtra, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "Pagamento Extra-Orçamentário Líquido";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoPagamentoExtra, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Estorno de Pagamentos Extra-Orçamentários";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalEstornoPagamentoExtra, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Aplicações";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalAplicacoes, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Resgates";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalResgates, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Depósitos/Retiradas";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalDepositosRetiradas, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $rsTotalDemonstrativo = new RecordSet;
    $rsTotalDemonstrativo->preenche( $arTotalDemonstrativo );

    $rsRecordSet[0] = $arDadosBancarios;
    $rsRecordSet[1] = $rsTotalDemonstrativo;

    return $obErro;
}

}

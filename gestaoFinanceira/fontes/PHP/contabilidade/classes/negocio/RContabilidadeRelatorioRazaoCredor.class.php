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
    * Classe de relatório para Razão do Credor
    * Data de Criação   : 08/06/2005

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.02.16
*/

/*
$Log$
Revision 1.8  2007/03/26 18:41:55  luciano
#8842#

Revision 1.7  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO               );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeRelatorioRazaoCredor.class.php" );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"                         );
include_once( CAM_FW_PDF."RRelatorio.class.php"                              );

/**
    * Classe de Regra de Negócios Razão por Credor
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class RContabilidadeRelatorioRazaoCredor extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obTContabilidadeRazaoCredor;
/**
    * @var Object
    * @access Private
*/
var $inCodEntidade;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inNumOrgao;
/**
    * @var Integer
    * @access Private
*/
var $inNumUnidade;
/**
    * @var String
    * @access Private
*/
var $inCodDespesa;
/**
    * @var String
    * @access Private
*/
var $inCodEmpenho;
/**
    * @var Integer
    * @access Private
*/
var $inCodRecurso;
var $stDestinacaoRecurso;
var $inCodDetalhamento;
/**
    * @var Integer
    * @access Private
*/
var $inNumCGM;
/**
    * @var Boolean
    * @access Private
*/
var $boDemoLiquidacao;
/**
    * @var Boolean
    * @access Private
*/
var $boDemoRestos;
/**
    * @var String
    * @access Private
*/
var $stDtInicial;
/**
    * @var String
    * @access Private
*/
var $stDtFinal;

/**
     * @access Public
     * @param Integer $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio        = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setNumOrgao($valor) { $this->inNumOrgao         = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setNumUnidade($valor) { $this->inNumUnidade       = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodDespesa($valor) { $this->inCodDespesa       = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEmpenho($valor) { $this->inCodEmpenho       = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodRecurso($valor) { $this->inCodRecurso       = $valor; }
function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso = $valor; }
function setCodDetalhamento($valor) { $this->inCodDetalhamento = $valor; }

/**
     * @access Public
     * @param Integer $valor
*/
function setNumCGM($valor) { $this->inNumCGM           = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setDemoLiquidacao($valor) { $this->boDemoLiquidacao   = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setDemoRestos($valor) { $this->boDemoRestos       = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtInicial($valor) { $this->stDtInicial        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtFinal($valor) { $this->stDtFinal          = $valor; }

/**
     * @access Public
     * @return Integer
*/
function getCodEntidade() { return $this->inCodEntidade;                }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;                  }
/**
     * @access Public
     * @return Integer
*/
function getNumOrgao() { return $this->inNumOrgao;                   }
/**
     * @access Public
     * @return Integer
*/
function getNumUnidade() { return $this->inNumUnidade;                 }
/**
     * @access Public
     * @return String
*/
function getCodDespesa() { return $this->inCodDespesa;                 }
/**
     * @access Public
     * @return String
*/
function getCodEmpenho() { return $this->inCodEmpenho;                 }
/**
     * @access Public
     * @return Integer
*/
function getCodRecurso() { return $this->inCodRecurso;                 }
function getDestinacaoRecurso() { return $this->stDestinacaoRecurso;          }
function getCodDetalhamento() { return $this->inCodDetalhamento;            }
/**
     * @access Public
     * @return Integer
*/
function getNumCGM() { return $this->inNumCGM;                     }
/**
     * @access Public
     * @return Boolean
*/
function getDemoLiquidacao() { return $this->boDemoLiquidacao;             }
/**
     * @access Public
     * @return Boolean
*/
function getDemoRestos() { return $this->boDemoRestos;                 }

/**
     * @access Public
     * @return Object
*/
function RContabilidadeRelatorioRazaoCredor()
{
    $this->obTContabilidadeRazaoCredor  = new TContabilidadeRelatorioRazaoCredor;
    $this->obREmpenhoEmpenho            = new REmpenhoEmpenho;
    $this->obRRelatorio                 = new RRelatorio;
}

/**
    * Método abstrato
    * @access Public
*/

function geraRecordSet(&$rsRecordSet1, &$rsRecordSet2 , &$rsRecordSet3, &$rsRecordSet4, &$rsRecordSet5, &$rsRecordSet6, $stOrder = "")
{
$stFiltro = "";

    $this->obTContabilidadeRazaoCredor->setDado("exercicio"        , $this->stExercicio     );
    $this->obTContabilidadeRazaoCredor->setDado("cod_entidade"     , $this->inCodEntidade   );
    $this->obTContabilidadeRazaoCredor->setDado("cgm_beneficiario" , $this->inNumCGM        );
    $this->obTContabilidadeRazaoCredor->setDado("dt_inicial"       , $this->stDtInicial     );
    $this->obTContabilidadeRazaoCredor->setDado("dt_final"         , $this->stDtFinal       );

    if( !$this->boDemoRestos == 'S' )
        $this->obTContabilidadeRazaoCredor->setDado( "stExercicio" , ' AND EE.exercicio = '.$this->stExercicio );

    if( $this->inNumOrgao )
        $stFiltro .= " AND OD.num_orgao = ".$this->inNumOrgao     ." \n";
    if( $this->inNumUnidade )
        $stFiltro .= " AND OD.num_unidade = ".$this->inNumUnidade ." \n";
    if( $this->inCodRecurso )
        $stFiltro .= " AND OD.cod_recurso = ".$this->inCodRecurso ." \n";
    if( $this->stDestinacaoRecurso )
        $stFiltro .= " AND REC.masc_recurso like '".$this->stDestinacaoRecurso ."%' \n";
    if( $this->inCodDetalhamento )
        $stFiltro .= " AND REC.cod_detalhamento = ".$this->inCodDetalhamento." \n";
    if( $this->inCodDespesa )
        $stFiltro .= " AND OCD.cod_estrutural like publico.fn_mascarareduzida('".$this->inCodDespesa."')|| '%' \n";
    if( $this->inCodEmpenho )
        $stFiltro .= " AND tbl.cod_empenho = ".$this->inCodEmpenho." \n";

    $this->obTContabilidadeRazaoCredor->setDado( 'stFiltro', $stFiltro );
    $stFiltro = "";

    $this->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( $this->inNumCGM );
    $this->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->listar( $rsCGM );
    $arRecordSet1 = array();
    $arRecordSet1[0]["credor"] = "Credor: ".$this->inNumCGM.' '.$rsCGM->getCampo( "nom_cgm" );

    $obErro = $this->obTContabilidadeRazaoCredor->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $arRecordSet2 = array();
    $nuVlTotalEmpenho            = 0;
    $nuVlTotalLiquidado          = 0;
    $nuVlTotalPago               = 0;
    $nuVlTotalEmpenhoAnulado     = 0;
    $nuVlTotalLiquidadoAnulado   = 0;
    $nuVlTotalPagoAnulado        = 0;
    $nuVlTotalRPLiquidado        = 0;
    $nuVlTotalRPPago             = 0;
    $nuVlTotalRPEmpenhoAnulado   = 0;
    $nuVlTotalRPLiquidadoAnulado = 0;
    $nuVlTotalRPPagoAnulado      = 0;
    $inCount = 0;

    if ( !$obErro->ocorreu() ) {
        while ( !$rsRecordSet->eof() ) {

            if ( $inCodEmpenhoOld != $rsRecordSet->getCampo( "cod_empenho" ) or $inCodEntidadeOld != $rsRecordSet->getCampo("cod_entidade") or $stExercicioOld != $rsRecordSet->getCampo( "exercicio" ) ) {
                if ($inCount > 0) {
                    $arRecordSet2[$inCount]['data'] = "";
                    $inCount++;
                }
                $arRecordSet2[$inCount]["data"] = " Empenho ";
                $stEmpenho = $rsRecordSet->getCampo("cod_entidade").'-'.$rsRecordSet->getCampo("cod_empenho").'/'.$rsRecordSet->getCampo("exercicio_empenho");
                $arRecordSet2[$inCount]["lote"] = $stEmpenho;
                $arRecordSet2[$inCount]["historico"] = "Dotação ".$rsRecordSet->getCampo( "cod_despesa")." - ".$rsRecordSet->getCampo( "dotacao_formatada" );
                $arRecordSet2[$inCount]['historico'] .= " - ".$rsRecordSet->getCampo( "descricao" );
                $inCount++;
                $arRecordSet2[$inCount]["data"] = "";
                $inCount++;
            }

            if ( $this->boDemoLiquidacao == 'S' or ( $this->boDemoLiquidacao == 'N' and $rsRecordSet->getCampo( "tipo" ) != 'L' ) ) {
                $arRecordSet2[$inCount]["data"]        = $rsRecordSet->getCampo( "dt_lote" );
                $arRecordSet2[$inCount]["lote"]        = $rsRecordSet->getCampo( "cod_entidade" ).'.'.$rsRecordSet->getCampo( "tipo" ).'.';
                $arRecordSet2[$inCount]["lote"]       .= $rsRecordSet->getCampo( "cod_lote" ).'.'.$rsRecordSet->getCampo( "sequencia" );
                $arRecordSet2[$inCount]["historico"]   = $rsRecordSet->getCampo( "cod_historico" ).' - '.$rsRecordSet->getCampo( "nom_historico" ).' ';
                $arRecordSet2[$inCount]["historico"]  .= trim($rsRecordSet->getCampo( "complemento" ));
                $arRecordSet2[$inCount]["debito"]      = $rsRecordSet->getCampo( "cod_plano_debito" );
                $arRecordSet2[$inCount]["estrut_deb"]  = $rsRecordSet->getCampo( "cod_estrutural_debito" );
                $arRecordSet2[$inCount]["credito"]     = $rsRecordSet->getCampo( "cod_plano_credito" );
                $arRecordSet2[$inCount]["estrut_cred"] = $rsRecordSet->getCampo( "cod_estrutural_credito" );
                $arRecordSet2[$inCount]["valor"]       = $rsRecordSet->getCampo( "vl_lancamento" );
                $inCount++;
            }

            $inCodEmpenhoOld  = $rsRecordSet->getCampo( "cod_empenho"  );
            $inCodEntidadeOld = $rsRecordSet->getCampo( "cod_entidade" );
            $stExercicioOld   = $rsRecordSet->getCampo( "exercicio"    );

            // Monta totalizadores
            if ( $rsRecordSet->getCampo( "sequencia" ) == 1 ) {
                // Totaliza valores que não sejam estorno
                if ( $rsRecordSet->getCampo( "estorno" ) == 'f' ) {

                    switch ( $rsRecordSet->getCampo( "tipo" ) ) {
                        case 'E': if(!($rsRecordSet->getCampo( 'exercicio_empenho' ) < $this->stExercicio))
                                     $nuVlTotalEmpenho   = bcadd( $nuVlTotalEmpenho  , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  break;
                        case 'L': if(!($rsRecordSet->getCampo( 'exercicio_empenho' ) < $this->stExercicio))
                                      $nuVlTotalLiquidado = bcadd( $nuVlTotalLiquidado, $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  else $nuVlTotalRPLiquidado = bcadd( $nuVlTotalRPLiquidado, $rsRecordSet->getCampo( 'vl_lancamento' ), 4 );
                                  break;
                        case 'P': if(!($rsRecordSet->getCampo( 'exercicio_empenho' ) < $this->stExercicio))
                                      $nuVlTotalPago      = bcadd( $nuVlTotalPago     , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  else $nuVlTotalRPPago   = bcadd( $nuVlTotalRPPago   , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  break;
                    }

                // Totaliza valores estornados
                } else {

                    switch ( $rsRecordSet->getCampo( "tipo" ) ) {
                        case 'E': if(!($rsRecordSet->getCampo( 'exercicio_empenho' ) < $this->stExercicio))
                                      $nuVlTotalEmpenhoAnulado   = bcadd( $nuVlTotalEmpenhoAnulado  , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  else $nuVlTotalRPEmpenhoAnulado  = bcadd( $nuVlTotalRPEmpenhoAnulado , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  break;
                        case 'L': if(!($rsRecordSet->getCampo( 'exercicio_empenho' ) < $this->stExercicio))
                                      $nuVlTotalLiquidadoAnulado = bcadd( $nuVlTotalLiquidadoAnulado, $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  else $nuVlTotalRPLiquidadoAnulado = bcadd( $nuVlTotalRPLiquidadoAnulado, $rsRecordSet->getCampo( 'vl_lancamento' ), 4 );
                                  break;
                        case 'P': if(!($rsRecordSet->getCampo( 'exercicio_empenho' ) < $this->stExercicio))
                                      $nuVlTotalPagoAnulado      = bcadd( $nuVlTotalPagoAnulado     , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  else $nuVlTotalRPPagoAnulado   = bcadd( $nuVlTotalRPPagoAnulado   , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  break;
                    }
                }
            }

            $rsRecordSet->proximo();
        }

        $nuVlFinalEmpenhado = bcsub( $nuVlTotalEmpenho  , $nuVlTotalEmpenhoAnulado  , 4 );
        $nuVlFinalLiquidado = bcsub( $nuVlTotalLiquidado, $nuVlTotalLiquidadoAnulado, 4 );
        $nuVlFinalPago      = bcsub( $nuVlTotalPago     , $nuVlTotalPagoAnulado     , 4 );

        $arRecordSet4[0]["coluna1"] = "Empenhado";
        $arRecordSet4[1]["coluna1"] = "Liquidado";
        $arRecordSet4[2]["coluna1"] = "Pago";
        $arRecordSet4[3]["coluna1"] = "Saldo a Liquidar";

        $arRecordSet4[0]["coluna2"] = $nuVlTotalEmpenho;
        $arRecordSet4[1]["coluna2"] = $nuVlTotalLiquidado;
        $arRecordSet4[2]["coluna2"] = $nuVlTotalPago;
        $arRecordSet4[3]["coluna2"] = $nuSaldoALiquidar;

        $arRecordSet4[0]["coluna3"] = "Anulado";
        $arRecordSet4[1]["coluna3"] = "Estorno de Liquidação";
        $arRecordSet4[2]["coluna3"] = "Estorno de Pagamento";
        $arRecordSet4[3]["coluna3"] = "Saldo Liquidado a Pagar";

        $arRecordSet4[0]["coluna4"] = $nuVlTotalEmpenhoAnulado;
        $arRecordSet4[1]["coluna4"] = $nuVlTotalLiquidadoAnulado;
        $arRecordSet4[2]["coluna4"] = $nuVlTotalPagoAnulado;
        $arRecordSet4[3]["coluna4"] = $nuSaldoAPagar;

        $arRecordSet5[0]["coluna1"] = "Liquidado";
        $arRecordSet5[1]["coluna1"] = "Anulado";
        $arRecordSet5[2]["coluna1"] = "Pago";

        $arRecordSet5[0]["coluna2"] = $nuVlTotalRPLiquidado;
        $arRecordSet5[1]["coluna2"] = $nuVlTotalRPEmpenhoAnulado;
        $arRecordSet5[2]["coluna2"] = $nuVlTotalRPPago;

        $arRecordSet5[0]["coluna3"] = "Estorno de Liquidação";
        $arRecordSet5[1]["coluna3"] = "";
        $arRecordSet5[2]["coluna3"] = "Estorno de Pagamento";

        $arRecordSet5[0]["coluna4"] = $nuVlTotalRPLiquidadoAnulado;
        $arRecordSet5[1]["coluna4"] = "";
        $arRecordSet5[2]["coluna4"] = $nuVlTotalRPPagoAnulado;
    }

    $rsRecordSet1 = new RecordSet;
    $rsRecordSet2 = new RecordSet;
    $rsRecordSet4 = new RecordSet;
    $rsRecordSet5 = new RecordSet;
    $rsRecordSet1->preenche( $arRecordSet1 );
    $rsRecordSet2->preenche( $arRecordSet2 );
    $rsRecordSet2->addFormatacao( 'vl_lancamento', 'NUMERIC_BR' );
    $rsRecordSet4->preenche( $arRecordSet4 );
    $rsRecordSet4->addFormatacao( 'coluna2', 'NUMERIC_BR' );
    $rsRecordSet4->addFormatacao( 'coluna4', 'NUMERIC_BR' );
    $rsRecordSet4->addFormatacao( 'coluna6', 'NUMERIC_BR' );
    $rsRecordSet5->preenche( $arRecordSet5 );
    $rsRecordSet5->addFormatacao( 'coluna2', 'NUMERIC_BR' );
    $rsRecordSet5->addFormatacao( 'coluna4', 'NUMERIC_BR' );

    $nuVlTotalEmpenho   = 0;
    $nuVlTotalLiquidado = 0;
    $nuVlTotalPago      = 0;
    $nuVlTotalEmpenhoAnulado   = 0;
    $nuVlTotalLiquidadoAnulado = 0;
    $nuVlTotalPagoAnulado      = 0;

    // Recupera valores para montar totalizador do inicio do ano até a data final
    $this->obTContabilidadeRazaoCredor->setDado( 'stExercicio', 'AND EE.exercicio = '.$this->stExercicio );
    $this->obTContabilidadeRazaoCredor->setDado( 'dt_inicial', '01/01/'.$this->stExercicio );
    $obErro = $this->obTContabilidadeRazaoCredor->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );
    if ( !$obErro->ocorreu() ) {
        while ( !$rsRecordSet->eof() ) {
            // Monta totalizadores
            if ( $rsRecordSet->getCampo( "sequencia" ) == 1 ) {
                // Totaliza valores que não sejam estorno
                if ( $rsRecordSet->getCampo( "estorno" ) == 'f' ) {

                    switch ( $rsRecordSet->getCampo( "tipo" ) ) {
                        case 'E': $nuVlTotalEmpenho   = bcadd( $nuVlTotalEmpenho  , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  break;
                        case 'L': $nuVlTotalLiquidado = bcadd( $nuVlTotalLiquidado, $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  break;
                        case 'P': $nuVlTotalPago      = bcadd( $nuVlTotalPago     , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  break;
                    }

                // Totaliza valores estornados
                } else {

                    switch ( $rsRecordSet->getCampo( "tipo" ) ) {
                        case 'E': $nuVlTotalEmpenhoAnulado   = bcadd( $nuVlTotalEmpenhoAnulado  , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  break;
                        case 'L': $nuVlTotalLiquidadoAnulado = bcadd( $nuVlTotalLiquidadoAnulado, $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  break;
                        case 'P': $nuVlTotalPagoAnulado      = bcadd( $nuVlTotalPagoAnulado     , $rsRecordSet->getCampo( "vl_lancamento" ), 4 );
                                  break;
                    }
                }
            }

            $rsRecordSet->proximo();
        }

        $nuVlFinalEmpenhado = bcsub( $nuVlTotalEmpenho  , $nuVlTotalEmpenhoAnulado  , 4 );
        $nuVlFinalLiquidado = bcsub( $nuVlTotalLiquidado, $nuVlTotalLiquidadoAnulado, 4 );
        $nuVlFinalPago      = bcsub( $nuVlTotalPago     , $nuVlTotalPagoAnulado     , 4 );

        $nuSaldoALiquidar   = bcsub( $nuVlFinalEmpenhado, $nuVlFinalLiquidado       , 4 );
        $nuSaldoAPagar      = bcsub( $nuVlFinalLiquidado, $nuVlFinalPago            , 4 );
        $nuSaldoEmpenhadoAPagar = bcsub( $nuVlFinalEmpenhado, $nuVlFinalPago        , 4 );

        $arRecordSet3[0]["coluna1"] = "Empenhado";
        $arRecordSet3[1]["coluna1"] = "Liquidado";
        $arRecordSet3[2]["coluna1"] = "Pago";
        $arRecordSet3[3]["coluna1"] = "Saldo a Liquidar";

        $arRecordSet3[0]["coluna2"] = $nuVlTotalEmpenho;
        $arRecordSet3[1]["coluna2"] = $nuVlTotalLiquidado;
        $arRecordSet3[2]["coluna2"] = $nuVlTotalPago;
        $arRecordSet3[3]["coluna2"] = $nuSaldoALiquidar;

        $arRecordSet3[0]["coluna3"] = "Anulado";
        $arRecordSet3[1]["coluna3"] = "Estorno de Liquidação";
        $arRecordSet3[2]["coluna3"] = "Estorno de Pagamento";
        $arRecordSet3[3]["coluna3"] = "Saldo Liquidado a Pagar";

        $arRecordSet3[0]["coluna4"] = $nuVlTotalEmpenhoAnulado;
        $arRecordSet3[1]["coluna4"] = $nuVlTotalLiquidadoAnulado;
        $arRecordSet3[2]["coluna4"] = $nuVlTotalPagoAnulado;
        $arRecordSet3[3]["coluna4"] = $nuSaldoAPagar;

        $arRecordSet3[0]["coluna5"] = "( Empenhado - Anulado )";
        $arRecordSet3[1]["coluna5"] = "( Liquidação - Estorno de Liquidação )";
        $arRecordSet3[2]["coluna5"] = "( Pago - Estorno de Pagamento )";
        $arRecordSet3[3]["coluna5"] = "Saldo Empenhado a Pagar";

        $arRecordSet3[0]["coluna6"] = $nuVlFinalEmpenhado;
        $arRecordSet3[1]["coluna6"] = $nuVlFinalLiquidado;
        $arRecordSet3[2]["coluna6"] = $nuVlFinalPago;
        $arRecordSet3[3]["coluna6"] = $nuSaldoEmpenhadoAPagar;
    }

    $rsRecordSet3 = new RecordSet;
    $rsRecordSet3->preenche( $arRecordSet3 );
    $rsRecordSet3->addFormatacao( 'coluna2', 'NUMERIC_BR' );
    $rsRecordSet3->addFormatacao( 'coluna4', 'NUMERIC_BR' );
    $rsRecordSet3->addFormatacao( 'coluna6', 'NUMERIC_BR' );

    // Monta Entidades relacionadas
    $this->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( $this->stExercicio );
    $arEntidades = explode( ',', $this->inCodEntidade );

    $inCount = 0;
    foreach ($arEntidades as $key => $inCodEntidade) {
        $this->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
        $obErro = $this->obREmpenhoEmpenho->obROrcamentoEntidade->consultarNomes( $rsLista );

        if( $obErro->ocorreu() )
            break;

        $arRecordSet6[$inCount]['nom_entidade'] = $rsLista->getCampo("entidade");
        $inCount++;
    }

    $rsRecordSet6 = new RecordSet;
    $rsRecordSet6->preenche( $arRecordSet6 );

    return $obErro;
}

}

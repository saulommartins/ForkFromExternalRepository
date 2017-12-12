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
    * Data de Criação   : 12/12/2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2007-10-24 15:18:39 -0200 (Qua, 24 Out 2007) $

    * Casos de uso: uc-02.04.07
*/

/*
$Log$
Revision 1.31  2007/07/04 18:12:29  leandro.zis
corrigido para o PHP5

Revision 1.30  2007/02/26 21:28:06  cako
Bug #8311#

Revision 1.29  2007/02/16 17:11:04  cako
Bug #8400#

Revision 1.28  2007/02/16 12:54:24  cako
Bug #7769#

Revision 1.27  2007/02/12 15:55:42  cako
Bug #7549#

Revision 1.26  2007/02/12 12:21:52  cako
Bug #7549#

Revision 1.25  2006/11/14 12:22:29  cako
Bug #7232#

Revision 1.24  2006/07/19 16:41:29  jose.eduardo
Bug #6596#

Revision 1.23  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                                  );
include_once( CAM_GF_TES_MAPEAMENTO."FTesourariaEmitirBoletim.class.php" );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"          );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaAssinatura.class.php"       );
include_once( CAM_FW_PDF."RRelatorio.class.php"                          );

/**
    * Classe de Regra de Negócios Emitir Boletim
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RTesourariaRelatorioEmitirBoletim extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obTTesourariaEmitirBoletim;
/**
    * @var String
    * @access Private
*/
var $stTipoEmissao;
/**
    * @var Integer
    * @access Private
*/
var $inCodTerminal;
/**
    * @var String
    * @access Private
*/
var $stDtBoletim;
/**
    * @var Integer
    * @access Private
*/
var $inCodBoletim;
/**
    * @var Integer
    * @access Private
*/
var $inCgmUsuario;
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
var $stIncluirAssinatura;

/**
    * @var String
    * @access Private
*/
var $boSemMovimentacao;

/**
     * @access Public
     * @param String $valor
*/
function setTipoEmissao($valor) { $this->stTipoEmissao = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodTerminal($valor) { $this->inCodTerminal = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtBoletim($valor) { $this->stDtBoletim   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodBoletim($valor) { $this->inCodBoletim  = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCgmUsuario($valor) { $this->inCgmUsuario  = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setEntidade($valor) { $this->stEntidade  = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setIncluirAssinatura($valor) { $this->stIncluirAssinatura  = $valor; }

/**
     * @access Public
     * @param String $valor
*/
function setSemMovimentacao($valor) { $this->boSemMovimentacao = $valor;  }

/*
    * @access Public
    * @return String
*/
function getTipoEmissao() { return $this->stTipoEmissao;           }
/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;             }
/*
    * @access Public
    * @return Integer
*/
function getCodTerminal() { return $this->inCodTerminal;           }
/*
    * @access Public
    * @return String
*/
function getDtBoletim() { return $this->stDtBoletim;             }
/*
    * @access Public
    * @return String
*/
function getCodBoletim() { return $this->stCodBoletim;            }
/*
    * @access Public
    * @return String
*/
function getCgmUsuario() { return $this->stCgmUsuario;            }
/*
    * @access Public
    * @return String
*/
function getEntidade() { return $this->stEntidade;              }
/*
    * @access Public
    * @return String
*/
function getIncluirAssinatura() { return $this->stIncluirAssinatura;     }

/*
    * @access Public
    * @return Boolean
*/
function getSemMovimento() { return $this->boSemMovimentacao;  }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaRelatorioEmitirBoletim()
{
    $this->obRRelatorio               = new RRelatorio;
    $this->obFTesourariaEmitirBoletim = new FTesourariaEmitirBoletim;
    $this->obRTesourariaBoletim       = new RTesourariaBoletim;
    $this->obRTesourariaAssinatura    = new RTesourariaAssinatura;
    $this->obRTesourariaBoletim->addArrecadacao();
    $this->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
}

/**
    * Método para gerar recordsets para demonstrativo de caixa
    * @access Public
    * @param Object &$rsBoletim
    * @param Object &$rsBoletim
    * @return Object $obErro
*/
function geraRecordSetCaixa(&$arRecordSet)
{
    $obErro = $this->obFTesourariaEmitirBoletim->recuperaDemonstrativoCaixa( $rsDemonstrativoCaixa );
    //Monta array com cod_boletim e data
    $arBoletim[0]['descricao'] = "Boletim";
    $arBoletim[0]['valor']     = $rsDemonstrativoCaixa->getCampo( "cod_boletim"       );
    $arBoletim[1]['descricao'] = "Data";
    $arBoletim[1]['valor']     = $this->stDtBoletim;
    $rsBoletim = new RecordSet();
    $rsBoletim->preenche( $arBoletim );

    $arDemonstrativoCaixa = array();
    $inCount = 0;

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

    while ( !$rsDemonstrativoCaixa->eof() ) {
        // Separa contas credito
        $arDemonstrativoCaixa[$inCount]['cod_boletim'] = $rsDemonstrativoCaixa->getCampo( "cod_boletim"       );
        $arDemonstrativoCaixa[$inCount]['dt_boletim']  = $rsDemonstrativoCaixa->getCampo( "dt_boletim"        );
        $arDemonstrativoCaixa[$inCount]['hora']        = $rsDemonstrativoCaixa->getCampo( "hora"              );
        $arDemonstrativoCaixa[$inCount]['valor']       = $rsDemonstrativoCaixa->getCampo( "valor"        )*(-1);
        $arDemonstrativoCaixa[$inCount]['cod_conta']   = $rsDemonstrativoCaixa->getCampo( "conta_credito"     );
        $arDemonstrativoCaixa[$inCount]['descricao']   = $rsDemonstrativoCaixa->getCampo( "descricao"         );
        $arDemonstrativoCaixa[$inCount]['nom_cgm']     = $rsDemonstrativoCaixa->getCampo( "nom_cgm"           );
        $stNomConta = $rsDemonstrativoCaixa->getCampo( "nom_conta_credito" );
        $stNomConta = str_replace( chr(10), "", $stNomConta );
        $stNomConta = wordwrap( $stNomConta, 45, chr(13) );
        $arNomConta = array();
        $arNomConta = explode( chr(13), $stNomConta );
        foreach ($arNomConta as $stNomConta) {
            $arDemonstrativoCaixa[$inCount]['nom_conta'] = $stNomConta;
            $inCount++;
        }
        // Separa contas debito
        $arDemonstrativoCaixa[$inCount]['cod_boletim'] = $rsDemonstrativoCaixa->getCampo( "cod_boletim"       );
        $arDemonstrativoCaixa[$inCount]['dt_boletim']  = $rsDemonstrativoCaixa->getCampo( "dt_boletim"        );
        $arDemonstrativoCaixa[$inCount]['hora']        = $rsDemonstrativoCaixa->getCampo( "hora"              );
        $arDemonstrativoCaixa[$inCount]['valor']       = $rsDemonstrativoCaixa->getCampo( "valor"             );
        $arDemonstrativoCaixa[$inCount]['cod_conta']   = $rsDemonstrativoCaixa->getCampo( "conta_debito"      );
        $arDemonstrativoCaixa[$inCount]['descricao']   = $rsDemonstrativoCaixa->getCampo( "descricao"         );
        $arDemonstrativoCaixa[$inCount]['nom_cgm']     = $rsDemonstrativoCaixa->getCampo( "nom_cgm"           );
        $stNomConta = $rsDemonstrativoCaixa->getCampo( "nom_conta_debito" );
        $stNomConta = str_replace( chr(10), "", $stNomConta );
        $stNomConta = wordwrap( $stNomConta, 45, chr(13) );
        $arNomConta = array();
        $arNomConta = explode( chr(13), $stNomConta );
        foreach ($arNomConta as $stNomConta) {
            $arDemonstrativoCaixa[$inCount]['nom_conta'] = $stNomConta;
            $inCount++;
        }

        // Gera Totalizadores
        if ( $rsDemonstrativoCaixa->getCampo("estorno") == "t") {
            if ( $rsDemonstrativoCaixa->getCampo("tipo") =='A' ) {
                $nuVlTotalEstornoArrecadacao = bcadd( $nuVlTotalEstornoArrecadacao, $rsDemonstrativoCaixa->getCampo("valor"), 4 );
            } elseif ( $rsDemonstrativoCaixa->getCampo("tipo") =='T' ) {
                switch ($rsDemonstrativoCaixa->getCampo("cod_tipo")) {
                    case 1 : $nuVlTotalEstornoPagamentoExtra   = bcadd( $nuVlTotalEstornoPagamentoExtra  , $rsDemonstrativoCaixa->getCampo("valor"), 4 ); break;
                    case 2 : $nuVlTotalEstornoArrecadacaoExtra = bcadd( $nuVlTotalEstornoArrecadacaoExtra, $rsDemonstrativoCaixa->getCampo("valor"), 4 ); break;
                }
            } elseif ( $rsDemonstrativoCaixa->getCampo("tipo") =='P' ) {
                if (substr( trim($rsDemonstrativoCaixa->getCampo('descricao')), strlen(trim($rsDemonstrativoCaixa->getCampo('descricao')))-4, 999) < Sessao::getExercicio()) { // Exercicio anterior
                    $nuVlTotalEstornoPagamentoExtra = bcadd( $nuVlTotalEstornoPagamentoExtra, $rsDemonstrativoCaixa->getCampo("valor"), 4 );
                } else
                    $nuVlTotalEstornoPagamento = bcadd( $nuVlTotalEstornoPagamento, $rsDemonstrativoCaixa->getCampo("valor"), 4 );
            }
        } else {
            if ( $rsDemonstrativoCaixa->getCampo("tipo") =='A' ) {
                $nuVlTotalArrecadacao = bcadd( $nuVlTotalArrecadacao, $rsDemonstrativoCaixa->getCampo("valor"), 4 );
            } elseif ( $rsDemonstrativoCaixa->getCampo("tipo") == 'T' ) {
                switch ($rsDemonstrativoCaixa->getCampo("cod_tipo")) {
                    case 1 : $nuVlTotalPagamentoExtra     = bcadd( $nuVlTotalPagamentoExtra,     $rsDemonstrativoCaixa->getCampo("valor"), 4 ); break;
                    case 2 : $nuVlTotalArrecadacaoExtra   = bcadd( $nuVlTotalArrecadacaoExtra,   $rsDemonstrativoCaixa->getCampo("valor"), 4 ); break;
                    case 3 : $nuVlTotalAplicacoes         = bcadd( $nuVlTotalAplicacoes,         $rsDemonstrativoCaixa->getCampo("valor"), 4 ); break;
                    case 4 : $nuVlTotalResgates           = bcadd( $nuVlTotalResgates,           $rsDemonstrativoCaixa->getCampo("valor"), 4 ); break;
                    case 5 : $nuVlTotalDepositosRetiradas = bcadd( $nuVlTotalDepositosRetiradas, $rsDemonstrativoCaixa->getCampo("valor"), 4 ); break;
                }
            } elseif ( $rsDemonstrativoCaixa->getCampo("tipo") =='P' ) {
                if(substr( trim($rsDemonstrativoCaixa->getCampo('descricao')), strlen(trim($rsDemonstrativoCaixa->getCampo('descricao')))-4, 999) < Sessao::getExercicio() ) // Exercicio anterior
                    $nuVlTotalPagamentoExtra = bcadd( $nuVlTotalPagamentoExtra, $rsDemonstrativoCaixa->getCampo("valor"), 4 );
                else
                    $nuVlTotalPagamento = bcadd( $nuVlTotalPagamento, $rsDemonstrativoCaixa->getCampo("valor"), 4 );
            }
        }
        $rsDemonstrativoCaixa->proximo();
    }

    $nuVlTotalLiquidoArrecadacao      = bcsub( $nuVlTotalArrecadacao, $nuVlTotalEstornoArrecadacao, 4);
    $nuVlTotalLiquidoPagamento        = bcsub( $nuVlTotalPagamento, $nuVlTotalEstornoPagamento, 4);
    $nuVlTotalLiquidoArrecadacaoExtra = bcsub( $nuVlTotalArrecadacaoExtra, $nuVlTotalEstornoArrecadacaoExtra, 4);
    $nuVlTotalLiquidoPagamentoExtra   = bcsub( $nuVlTotalPagamentoExtra, $nuVlTotalEstornoPagamentoExtra, 4);

    $inCount = 0;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Arrecadações Orçamentárias";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalArrecadacao, 2, ',', '.');
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "Arrecadação Orçamentária Líquida";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoArrecadacao, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Estorno de Arrecadações Orçamentárias";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalEstornoArrecadacao, 2, ',', '.');
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Arrecadações Extra-Orçamentárias";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalArrecadacaoExtra, 2, ',', '.');
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "Arrecadação Extra-Orçamentária Líquida";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoArrecadacaoExtra, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Estorno de Arrecadações Extra-Orçamentárias";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalEstornoArrecadacaoExtra, 2, ',', '.');
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Pagamentos Orçamentários";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalPagamento, 2, ',', '.' );
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "Pagamento Orçamentário Líquido";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoPagamento, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Estorno de Pagamentos Orçamentários";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalEstornoPagamento, 2, ',', '.' );
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Pagamentos Extra-Orçamentários";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalPagamentoExtra, 2, ',', '.' );
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "Pagamento Extra-Orçamentário Líquido";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoPagamentoExtra, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Estorno de Pagamentos Extra-Orçamentários";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalEstornoPagamentoExtra, 2, ',', '.' );
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Aplicações";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalAplicacoes, 2, ',', '.' );
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Resgates";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalResgates, 2, ',', '.' );
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativoCaixa[$inCount]["descricao"] = "Total de Depósitos/Retiradas";
    $arTotalDemonstrativoCaixa[$inCount]["valor"] = number_format( $nuVlTotalDepositosRetiradas, 2, ',', '.' );
    $arTotalDemonstrativoCaixa[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativoCaixa[$inCount]["valor_liquido"]  = "";

    $rsDemonstrativoCaixa = new RecordSet;
    $rsDemonstrativoCaixa->preenche( $arDemonstrativoCaixa );
    $rsDemonstrativoCaixa->addFormatacao( 'valor', 'CONTABIL' );

    $rsTotalDemonstrativoCaixa = new RecordSet;
    $rsTotalDemonstrativoCaixa->preenche( $arTotalDemonstrativoCaixa );

    $arRecordSet[0] = $rsBoletim;
    $arRecordSet[1] = $rsDemonstrativoCaixa;
    $arRecordSet[2] = $rsTotalDemonstrativoCaixa;

    return $obErro;
}

/**
    * Método para gerar relatorio Emitir Boletim de Tesouraria
    * @access Private
    * @param Object &$rsContaBanco
    * @param Object &$rsMovimentacao
    * @return Object $obErro
*/
function geraRecordSetBoletim(&$arRecordSet)
{
    $this->obFTesourariaEmitirBoletim->setDado('stDtBoletim', $this->stDtBoletim );
    $this->obFTesourariaEmitirBoletim->setDado('inCodBoletim', $this->inCodBoletim );
    $this->obFTesourariaEmitirBoletim->setDado('inCodEntidade', $this->stEntidade );
    $this->obFTesourariaEmitirBoletim->setDado('stExercicio', $this->stExercicio );
    $this->obFTesourariaEmitirBoletim->setDado('boSemMovimentacao', $this->boSemMovimentacao );

    $rsMovimentoBanco = new RecordSet();
    $rsTransferencia = new RecordSet();
    $rsPagamento = new RecordSet();
    $rsArrecadacao = new RecordSet();
    $rsBoletimLiberado = new RecordSet();

    $obErro = $this->obFTesourariaEmitirBoletim->recuperaMovimentoBanco( $rsMovimentoBanco );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obFTesourariaEmitirBoletim->recuperaTransferencia( $rsTransferencia );
    }

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obFTesourariaEmitirBoletim->recuperaPagamento( $rsPagamento );
    }

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obFTesourariaEmitirBoletim->recuperaArrecadacao( $rsArrecadacao );
    }

    if ( !$obErro->ocorreu() ) {
        $this->obRTesourariaBoletim->setCodBoletim( $this->inCodBoletim );
        $this->obRTesourariaBoletim->setDataBoletim( $this->stDtBoletim  );
        $this->obRTesourariaBoletim->setExercicio( $this->stExercicio   );
        $obErro = $this->obRTesourariaBoletim->listarBoletimLiberado( $rsBoletimLiberado );
        $boLiberado = ( $rsBoletimLiberado->eof() ) ? false : true;
    }

    // Monta recorsets da receita orçamentária e extra-orçamentária
    if ( !$obErro->ocorreu() ) {
        $arSaldoContaArrecadacao  = array();
        $inCountOrcamentaria      = 0;
        $inCountExtra             = 0;
        $nuVlOrcamentariaSubTotal = '0.00';
        $nuVlExtraSubTotal        = '0.00';
        $nuVlOrcamentariaTotal    = '0.00';
        $nuVlExtraTotal           = '0.00';
        while ( !$rsArrecadacao->eof() ) {
            list( $inCodContaDeb , $stNomContaDeb  ) = explode( "-", $rsArrecadacao->getCampo( "conta_debito"  ) );
            list( $inCodContaCred, $stNomContaCred ) = explode( "-", $rsArrecadacao->getCampo( "conta_credito" ) );
            if ( $rsArrecadacao->getCampo( "tipo" ) != "T" ) {
                $arSaldoContaArrecadacao[trim($inCodContaDeb)]['vl_conta_debito']  = bcadd($arSaldoContaArrecadacao[trim($inCodContaDeb)]['vl_conta_debito'],$rsArrecadacao->getCampo("valor"),4);
                $arSaldoContaArrecadacao[trim($inCodContaCred)]['vl_conta_credito']= bcadd($arSaldoContaArrecadacao[trim($inCodContaCred)]['vl_conta_credito'],abs($rsArrecadacao->getCampo("valor")),4);
            }
            if ( substr( $rsArrecadacao->getCampo( "cod_estrutural" ), 0, 3 ) != "2.1" ) {
                if ( $stCodEstruturalOld != $rsArrecadacao->getCampo( "cod_estrutural" ) OR $stTipoOld != $rsArrecadacao->getCampo("tipo") OR $rsArrecadacao->getCampo("tipo") == "E") {
                    $arNomConta = array();
                    $stNomConta = $rsArrecadacao->getCampo( "conta_debito"  );
                    $stNomConta = str_replace( chr(10), "", $stNomConta );
                    $stNomConta = wordwrap( $stNomConta, 40, chr(13) );
                    $arNomConta = explode( chr(13), $stNomConta );
                    $inCountOrcamentariaAux = $inCountOrcamentaria;
                    foreach ($arNomConta as $stNomConta) {
                        $arReceitaOrcamentaria[$inCountOrcamentariaAux]["conta_debito" ] = $stNomConta;
                        $inCountOrcamentariaAux++;
                    }
                }
                $arReceitaOrcamentaria[$inCountOrcamentaria]["valor" ] = $rsArrecadacao->getCampo( "valor" );

                if ( $rsArrecadacao->getCampo("tipo") == "A" OR $rsArrecadacao->getCampo( "cod_estrutural" ) != $stCodEstruturalOld OR $stTipoOld != $rsArrecadacao->getCampo("tipo") ) {
                    $arNomConta = array();
                    $stNomConta = $rsArrecadacao->getCampo( "conta_credito" );
                    $stNomConta = str_replace( chr(10), "", $stNomConta );
                    $stNomConta = wordwrap( $stNomConta, 40, chr(13) );
                    $arNomConta = explode( chr(13), $stNomConta );
                    foreach ($arNomConta as $stNomConta) {
                        $arReceitaOrcamentaria[$inCountOrcamentaria]["conta_credito"] = $stNomConta;
                        $inCountOrcamentaria++;
                    }
                }
                $nuVlOrcamentariaSubTotal = bcadd( $nuVlOrcamentariaSubTotal, $rsArrecadacao->getCampo( "valor" ), 4 );
                $inCountOrcamentaria = count( $arReceitaOrcamentaria );

                $stCodEstruturalOld = $rsArrecadacao->getCampo( "cod_estrutural" );
                $stTipoOld          = $rsArrecadacao->getCampo( "tipo"           );
                $rsArrecadacao->proximo();
                if ( $rsArrecadacao->getCampo( "cod_estrutural" ) != $stCodEstruturalOld OR $stTipoOld != $rsArrecadacao->getCampo("tipo") ) {
                    $arReceitaOrcamentaria[$inCountOrcamentaria]["conta_debito"] = "Total do Banco";
                    $arReceitaOrcamentaria[$inCountOrcamentaria]["valor"] = $nuVlOrcamentariaSubTotal;
                    $inCountOrcamentaria++;
                    $nuVlOrcamentariaTotal = bcadd( $nuVlOrcamentariaTotal, $nuVlOrcamentariaSubTotal, 4 );
                    $nuVlOrcamentariaSubTotal = '0.00';
                }
            } else {
//                if ( $stCodEstruturalOld != $rsArrecadacao->getCampo( "cod_estrutural" ) OR $stTipoOld != $rsArrecadacao->getCampo("tipo") OR $rsArrecadacao->getCampo("tipo") == "E" ) {
                    $arNomConta = array();
                    $stNomConta = $rsArrecadacao->getCampo( "conta_debito"  );
                    $stNomConta = str_replace( chr(10), "", $stNomConta );
                    $stNomConta = wordwrap( $stNomConta, 40, chr(13) );
                    $arNomConta = explode( chr(13), $stNomConta );
                    $inCountExtraAux = $inCountExtra;
                    foreach ($arNomConta as $stNomConta) {
                        $arReceitaExtra[$inCountExtraAux]["conta_debito" ] = $stNomConta;
                        $inCountExtraAux++;
                    }
//                }
                $arReceitaExtra[$inCountExtra]["valor" ] = $rsArrecadacao->getCampo( "valor" );

//                if ( $rsArrecadacao->getCampo("tipo") == "A" OR $rsArrecadacao->getCampo( "cod_estrutural" ) != $stCodEstruturalOld ) {
                    $stNomConta = $rsArrecadacao->getCampo( "conta_credito" );
                    $stNomConta = str_replace( chr(10), "", $stNomConta );
                    $stNomConta = wordwrap( $stNomConta, 40, chr(13) );
                    $arNomConta = explode( chr(13), $stNomConta );
                    foreach ($arNomConta as $stNomConta) {
                        $arReceitaExtra[$inCountExtra]["conta_credito"] = $stNomConta;
                        $inCountExtra++;
                    }
//                }

                $nuVlExtraSubTotal = bcadd( $nuVlExtraSubTotal, $rsArrecadacao->getCampo( "valor" ), 4 );
                $inCountExtra = count( $arReceitaExtra );

                $stCodEstruturalOld = $rsArrecadacao->getCampo( "cod_estrutural" );
                $stTipoOld          = $rsArrecadacao->getCampo( "tipo"           );
                $rsArrecadacao->proximo();
                if ( $rsArrecadacao->getCampo( "cod_estrutural" ) != $stCodEstruturalOld OR $stTipoOld != $rsArrecadacao->getCampo("tipo") ) {
                    $arReceitaExtra[$inCountExtra]["conta_debito"] = "Total do Banco";
                    $arReceitaExtra[$inCountExtra]["valor"] = $nuVlExtraSubTotal;
                    $inCountExtra++;
                    $nuVlExtraTotal = bcadd( $nuVlExtraTotal, $nuVlExtraSubTotal, 4 );
                    $nuVlExtraSubTotal = '0.00';
                }
            }
        }
        $arReceitaOrcamentaria[$inCountOrcamentaria]["conta_debito"] = "Total Receita Orçamentária Lançada";
        $arReceitaOrcamentaria[$inCountOrcamentaria]["valor"       ] = $nuVlOrcamentariaTotal;
        $rsReceitaOrcamentaria = new RecordSet();
        $rsReceitaOrcamentaria->preenche( $arReceitaOrcamentaria );

        $arReceitaExtra[$inCountExtra]["conta_debito"] = "Total Receita Extra-Orçamentária Lançada";
        $arReceitaExtra[$inCountExtra]["valor"       ] = $nuVlExtraTotal;
        $rsReceitaExtra = new RecordSet();
        $rsReceitaExtra->preenche( $arReceitaExtra );
    }
    // Gera recordset com totalizadores para movimento de banco
    if ( !$obErro->ocorreu() ) {
        $inCount = 0;
        $nuVlMBSaldoAnteriorTotal = '0.00';
        $nuVlMBCreditoTotal       = '0.00';
        $nuVlMBDebitoTotal        = '0.00';
        $nuVlMBSaldoAtualTotal    = '0.00';
        $nuVlMBSaldoAnteriorSubTotal = '0.00';
        $nuVlMBSaldoAtualSubTotal = '0.00';
        while ( !$rsMovimentoBanco->eof() ) {
            if ( $rsMovimentoBanco->getCampo("saldo_anterior") != '0.00' OR $rsMovimentoBanco->getCampo("vl_debito") != '0.00' OR $rsMovimentoBanco->getCampo("vl_credito") != '0.00' OR $this->boSemMovimentacao == 'S') {
                if ( Sessao::getExercicio() > '2012' ) {
                    $arMovimentoBanco[$inCount]["cod_estrutural"] = sistemaLegado::doMask($rsMovimentoBanco->getCampo("cod_estrutural"));
                } else {
                    $arMovimentoBanco[$inCount]["cod_estrutural"] = $rsMovimentoBanco->getCampo( "cod_estrutural" );
                }
                $arMovimentoBanco[$inCount]["cod_plano"]      = $rsMovimentoBanco->getCampo( "cod_plano"      );
                $arMovimentoBanco[$inCount]["saldo_anterior"] = $rsMovimentoBanco->getCampo( "saldo_anterior" );

                    $arMovimentoBanco[$inCount]["vl_debito"]  = $rsMovimentoBanco->getCampo("vl_debito");
                    $arMovimentoBanco[$inCount]["vl_credito"] = abs($rsMovimentoBanco->getCampo("vl_credito"));

                $arMovimentoBanco[$inCount]["vl_credito"] = ( $arMovimentoBanco[$inCount]["vl_credito"] ) ? $arMovimentoBanco[$inCount]["vl_credito"] : '0.00';
                $arMovimentoBanco[$inCount]["saldo_atual"] = bcsub( bcadd($rsMovimentoBanco->getCampo( "saldo_anterior" ), $arMovimentoBanco[$inCount]["vl_debito"], 4 ), $arMovimentoBanco[$inCount]["vl_credito"], 4 );

                $nuVlMBDebitoSubTotal        = bcadd( $nuVlMBDebitoSubTotal       , $arMovimentoBanco[$inCount]["vl_debito"] , 4 );
                $nuVlMBCreditoSubTotal       = bcadd( $nuVlMBCreditoSubTotal      , $arMovimentoBanco[$inCount]["vl_credito"], 4 );
                $nuVlMBSaldoAnteriorSubTotal = bcadd( $nuVlMBSaldoAnteriorSubTotal, $rsMovimentoBanco->getCampo( "saldo_anterior" ), 4 );
                $nuVlMBSaldoAtualSubTotal    = bcadd( $nuVlMBSaldoAtualSubTotal   , $arMovimentoBanco[$inCount]["saldo_atual"], 4 );

                $arNomConta = array();
                $stNomConta = $rsMovimentoBanco->getCampo( "nom_conta" );
                $stNomConta = str_replace( chr(10), '', $stNomConta );
                $stNomConta = wordwrap( $stNomConta, 35, chr(13) );
                $arNomConta = explode( chr(13), $stNomConta );
                foreach ($arNomConta as $stNomConta) {
                    $arMovimentoBanco[$inCount]["nom_conta"] = $stNomConta;
                    $inCount++;
                }
                $inCount--;

                $stCodEstruturalOld = $rsMovimentoBanco->getCampo( "cod_estrutural" );
                $inCount++;
                $rsMovimentoBanco->proximo();

                if ( substr($rsMovimentoBanco->getCampo( "cod_estrutural" ),0,9 ) != substr( $stCodEstruturalOld, 0, 9 ) or $rsMovimentoBanco->eof() ) {
                    $arMovimentoBanco[$inCount]["cod_estrutural"] = "Sub-Total";
                    $arMovimentoBanco[$inCount]["saldo_anterior"] = $nuVlMBSaldoAnteriorSubTotal;
                    $arMovimentoBanco[$inCount]["vl_debito"     ] = $nuVlMBDebitoSubTotal;
                    $arMovimentoBanco[$inCount]["vl_credito"    ] = ( abs($nuVlMBCreditoSubTotal) ) ? abs($nuVlMBCreditoSubTotal) : '0.00';
                    $arMovimentoBanco[$inCount]["saldo_atual"   ] = $nuVlMBSaldoAtualSubTotal;

                    $nuVlMBSaldoAnteriorTotal = bcadd( $nuVlMBSaldoAnteriorTotal, $nuVlMBSaldoAnteriorSubTotal, 4 );
                    $nuVlMBDebitoTotal        = bcadd( $nuVlMBDebitoTotal       , $nuVlMBDebitoSubTotal       , 4 );
                    $nuVlMBCreditoTotal       = bcadd( $nuVlMBCreditoTotal      , $nuVlMBCreditoSubTotal      , 4 );
                    $nuVlMBSaldoAtualTotal    = bcadd( $nuVlMBSaldoAtualTotal   , $nuVlMBSaldoAtualSubTotal   , 4 );

                    $nuVlMBSaldoAnteriorSubTotal = '0.00';
                    $nuVlMBCreditoSubTotal       = '0.00';
                    $nuVlMBDebitoSubTotal        = '0.00';
                    $nuVlMBSaldoAtualSubTotal    = '0.00';

                    $inCount++;
                    $arMovimentoBanco[$inCount]["cod_estrutural"] = "";
                    $inCount++;
                }
            } else {
                $rsMovimentoBanco->proximo();
            }
        }
        $arMovimentoBanco[$inCount]["cod_estrutural"] = "Total Caixa / Bancos";
        $arMovimentoBanco[$inCount]["saldo_anterior"] = $nuVlMBSaldoAnteriorTotal;
        $arMovimentoBanco[$inCount]["vl_debito"     ] = $nuVlMBDebitoTotal;
        $arMovimentoBanco[$inCount]["vl_credito"    ] = ( abs($nuVlMBCreditoTotal) ) ? abs($nuVlMBCreditoTotal) : '0.00';
        $arMovimentoBanco[$inCount]["saldo_atual"   ] = $nuVlMBSaldoAtualTotal;

        $rsMovimentoBanco = new RecordSet();
        $rsMovimentoBanco->preenche( $arMovimentoBanco );
    }

    // Gera recordset das transferencias + totalizadores
    if ( !$obErro->ocorreu() ) {
        $inCount = 0;
        $nuVlTransferenciaTotal = 0.00;
        while ( !$rsTransferencia->eof() ) {
            $arTransferencia[$inCount]["conta_debito" ] = $rsTransferencia->getCampo( "conta_debito"  );
            $arTransferencia[$inCount]["conta_credito"] = $rsTransferencia->getCampo( "conta_credito" );
            $arTransferencia[$inCount]["valor"        ] = $rsTransferencia->getCampo( "vl_lancamento" );
            $nuVlTransferenciaTotal = bcadd( $nuVlTransferenciaTotal, $rsTransferencia->getCampo( "vl_lancamento" ), 4 );

            $inCount++;
            $rsTransferencia->proximo();
        }
        $arTransferencia[$inCount]["conta_debito"] = "Total Depósitos / Transferências";
        $arTransferencia[$inCount]["valor"       ] = ($nuVlTransferenciaTotal) ? $nuVlTransferenciaTotal : '0.00';

        $rsTransferencia = new RecordSet();
        $rsTransferencia->preenche( $arTransferencia );
    }

    // Gera recordsets de pagamento orçamentário e extra-orçamentários
    if ( !$obErro->ocorreu() ) {
        $inCountOrcamentaria      = 0;
        $inCountExtra             = 0;
        $nuVlOrcamentariaSubTotal = '0.00';
        $nuVlExtraSubTotal        = '0.00';
        $nuVlOrcamentariaTotal    = '0.00';
        $nuVlExtraTotal           = '0.00';
        $arDespesaOrcamentaria    = array();
        $arDespesaExtra           = array();

        while ( !$rsPagamento->eof() ) {
            if ( $rsPagamento->getCampo( "exercicio" ) == $rsPagamento->getCampo( "exercicio_empenho" ) ) {

                $arNomConta = array();
                $stNomConta = $rsPagamento->getCampo( "conta_debito"  );
                $stNomConta = str_replace( chr(10), "", $stNomConta );
                $stNomConta = wordwrap( $stNomConta, 40, chr(13) );
                $arNomConta = explode( chr(13), $stNomConta );
                $inCountOrcamentariaAux = $inCountOrcamentaria;
                foreach ($arNomConta as $stNomConta) {
                    $arDespesaOrcamentaria[$inCountOrcamentariaAux]["conta_debito" ] = $stNomConta;
                    $inCountOrcamentariaAux++;
                }

                $arDespesaOrcamentaria[$inCountOrcamentaria]["valor"] = $rsPagamento->getCampo( "vl_pago");

                $arNomConta = array();
                $stNomConta = $rsPagamento->getCampo( "conta_credito" );
                $stNomConta = str_replace( chr(10), "", $stNomConta );
                $stNomConta = wordwrap( $stNomConta, 40, chr(13) );
                $arNomConta = explode( chr(13), $stNomConta );
                foreach ($arNomConta as $stNomConta) {
                    $arDespesaOrcamentaria[$inCountOrcamentaria]["conta_credito"] = $stNomConta;
                    $inCountOrcamentaria++;
                }

                $nuVlOrcamentariaSubTotal = bcadd( $nuVlOrcamentariaSubTotal, $rsPagamento->getCampo( "vl_pago" ), 4 );
                $inCountOrcamentaria = count( $arDespesaOrcamentaria );

                $stCodEstruturalOld = $rsPagamento->getCampo( "cod_estrutural" );
                $rsPagamento->proximo();
                if ( $rsPagamento->getCampo( "cod_estrutural" ) != $stCodEstruturalOld ) {
                    $arDespesaOrcamentaria[$inCountOrcamentaria]["conta_debito"] = "Total do Banco";
                    $arDespesaOrcamentaria[$inCountOrcamentaria]["valor"] = $nuVlOrcamentariaSubTotal;
                    $inCountOrcamentaria++;
                    $nuVlOrcamentariaTotal = bcadd( $nuVlOrcamentariaTotal, $nuVlOrcamentariaSubTotal, 4 );
                    $nuVlOrcamentariaSubTotal = '0.00';
                }
            } else {

                $arNomConta = array();
                $stNomConta = $rsPagamento->getCampo( "conta_debito" );
                $stNomConta = str_replace( chr(10), "", $stNomConta );
                $stNomConta = wordwrap( $stNomConta, 40, chr(13) );
                $arNomConta = explode( chr(13), $stNomConta );
                $inCountExtraAux = $inCountExtra;
                foreach ($arNomConta as $stNomConta) {
                    $arDespesaExtra[$inCountExtraAux]["conta_debito" ] = $stNomConta;
                    $inCountExtraAux++;
                }

                $arDespesaExtra[$inCountExtra]["valor"] = $rsPagamento->getCampo( "vl_pago" );

                $arNomConta = array();
                $stNomConta = $rsPagamento->getCampo( "conta_credito"  );
                $stNomConta = str_replace( chr(10), "", $stNomConta );
                $stNomConta = wordwrap( $stNomConta, 40, chr(13) );
                $arNomConta = explode( chr(13), $stNomConta );
                foreach ($arNomConta as $stNomConta) {
                    $arDespesaExtra[$inCountExtra]["conta_credito" ] = $stNomConta;
                    $inCountExtra++;
                }

                $nuVlExtraSubTotal = bcadd( $nuVlExtraSubTotal, $rsPagamento->getCampo( "vl_pago" ), 4 );
                $inCountExtra = count( $arDespesaExtra );

                $stCodEstruturalOld = $rsPagamento->getCampo( "cod_estrutural" );
                $stExercicioEmpenhoOld = $rsPagamento->getCampo( "exercicio_empenho" );
                $rsPagamento->proximo();
                if ( $rsPagamento->getCampo( "cod_estrutural" ) != $stCodEstruturalOld OR $rsPagamento->getCampo( "exercicio_empenho" ) != $stExercicioEmpenhoOld ) {
                    $arDespesaExtra[$inCountExtra]["conta_debito"] = "Total do Banco";
                    $arDespesaExtra[$inCountExtra]["valor"] = $nuVlExtraSubTotal;
                    $inCountExtra++;
                    $nuVlExtraTotal = bcadd( $nuVlExtraTotal, $nuVlExtraSubTotal, 4 );
                    $nuVlExtraSubTotal = '0.00';
                }
            }
        }
        $arDespesaOrcamentaria[$inCountOrcamentaria]["conta_debito"] = "Total Despesa Orçamentária Lançada";
        $arDespesaOrcamentaria[$inCountOrcamentaria]["valor"       ] = $nuVlOrcamentariaTotal;
        $rsDespesaOrcamentaria = new RecordSet();
        $rsDespesaOrcamentaria->preenche( $arDespesaOrcamentaria );

        $arDespesaExtra[$inCountExtra]["conta_debito"] = "Total Despesa Extra-Orçamentária Lançada";
        $arDespesaExtra[$inCountExtra]["valor"       ] = $nuVlExtraTotal;
        $rsDespesaExtra = new RecordSet();
        $rsDespesaExtra->preenche( $arDespesaExtra );
    }

    $arAssinatura = array();
    $arAssinatura[0]['assinatura1']   = "";
    $arAssinatura[0]['assinatura2']   = "";
    $arAssinatura[0]['assinatura3']   = "";
    $arAssinatura[1]['assinatura1']   = "";
    $arAssinatura[1]['assinatura2']   = "";
    $arAssinatura[1]['assinatura3']   = "";

    if ($this->stIncluirAssinatura=='sim') {
        $inCount=1;
        $this->obRTesourariaAssinatura->setEntidades($this->getEntidade());
        $this->obRTesourariaAssinatura->setExercicio($this->getExercicio());
        $this->obRTesourariaAssinatura->setSituacao ('true');
        $this->obRTesourariaAssinatura->setTipo( "BO" );
        $obErro = $this->obRTesourariaAssinatura->listar($rsAssinat);
        if (!$obErro->ocorreu()) {
            if ($rsAssinat->getNumLinhas()==1) {
                $arAssinatura[0]['assinatura2']   = $rsAssinat->getCampo("nom_cgm");
                $arAssinatura[1]['assinatura2']   = $rsAssinat->getCampo("cargo");
            }
            if ($rsAssinat->getNumLinhas()==2) {
                $arAssinatura[0]['assinatura1']   = $rsAssinat->getCampo("nom_cgm");
                $arAssinatura[1]['assinatura1']   = $rsAssinat->getCampo("cargo");
                $rsAssinat->proximo();
                $arAssinatura[0]['assinatura3']   = $rsAssinat->getCampo("nom_cgm");
                $arAssinatura[1]['assinatura3']   = $rsAssinat->getCampo("cargo");
            }
            if ($rsAssinat->getNumLinhas()==3) {
                while (!$rsAssinat->eof()) {
                    $arAssinatura[0]['assinatura'.$inCount]   = $rsAssinat->getCampo("nom_cgm");
                    $arAssinatura[1]['assinatura'.$inCount]   = $rsAssinat->getCampo("cargo");
                    $inCount++;
                    $rsAssinat->proximo();
                }
            }
        }
    }
    $rsAssinatura = new RecordSet();
    $rsAssinatura->preenche( $arAssinatura );

    $arRecordSet[0] = $rsMovimentoBanco;
    $arRecordSet[1] = $rsTransferencia;
    $arRecordSet[2] = $rsDespesaOrcamentaria;
    $arRecordSet[3] = $rsDespesaExtra;
    $arRecordSet[4] = $rsReceitaOrcamentaria;
    $arRecordSet[5] = $rsReceitaExtra;
    $arRecordSet[6] = $rsAssinatura;

    return $obErro;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSet, $stOrder = "")
{
    if ($this->inCodTerminal) {
        $stFiltroTransferencia               .= " AND TT.cod_terminal = ".$this->inCodTerminal." ";
        $stFiltroTransferenciaBanco          .= " AND TT.cod_terminal = ".$this->inCodTerminal." ";

        $stFiltroTransferenciaEstornada      .= " AND TTE.cod_terminal = ".$this->inCodTerminal." ";
        $stFiltroTransferenciaEstornadaBanco .= " AND TTE.cod_terminal = ".$this->inCodTerminal." ";

        $stFiltroPagamento                   .= " AND TP.cod_terminal = ".$this->inCodTerminal." ";
        $stFiltroPagamentoBanco              .= " AND TP.cod_terminal = ".$this->inCodTerminal." ";

        $stFiltroPagamentoEstornado          .= " AND TPE.cod_terminal = ".$this->inCodTerminal." ";
        $stFiltroPagamentoEstornadoBanco     .= " AND TPE.cod_terminal = ".$this->inCodTerminal." ";

        $stFiltroPagamentoTmp                .= " AND tp.cod_terminal = ".$this->inCodTerminal." ";
        $stFiltroPagamentoTmpBanco           .= " AND tp.cod_terminal = ".$this->inCodTerminal." ";

    }

    if ($this->inCodBoletim) {
        $stFiltroTransferencia               .= " AND TT.cod_boletim  in ( ".$this->inCodBoletim." ) ";
        $stFiltroTransferenciaBanco          .= " AND TT.cod_boletim  in ( ".$this->inCodBoletim." ) ";

        $stFiltroTransferenciaEstornada      .= " AND TTE.cod_boletim in ( ".$this->inCodBoletim." ) ";
        $stFiltroTransferenciaEstornadaBanco .= " AND TTE.cod_boletim in ( ".$this->inCodBoletim." ) ";

        $stFiltroPagamento                   .= " AND TP.cod_boletim  in ( ".$this->inCodBoletim." ) ";
        $stFiltroPagamentoBanco              .= " AND TP.cod_boletim  in ( ".$this->inCodBoletim." ) ";

        $stFiltroPagamentoEstornado          .= " AND TPE.cod_boletim in ( ".$this->inCodBoletim." ) ";
        $stFiltroPagamentoEstornadoBanco     .= " AND TPE.cod_boletim in ( ".$this->inCodBoletim." ) ";

        $stFiltroPagamentoTmp                .= " AND tp.cod_boletim  in ( ".$this->inCodBoletim." ) ";
        $stFiltroPagamentoTmpBanco           .= " AND tp.cod_boletim  in ( ".$this->inCodBoletim." ) ";
    }

    if ($this->stDtBoletim) {
        $stFiltroTransferencia               .= " AND TB.dt_boletim = TO_DATE( ''".$this->stDtBoletim."'', ''dd/mm/yyyy'') ";
        $stFiltroTransferenciaBanco          .= " AND TB.dt_boletim = TO_DATE( '||quote_literal('".$this->stDtBoletim."')||', '||quote_literal('dd/mm/yyyy')||' ) ";

        $stFiltroTransferenciaEstornada      .= " AND TB.dt_boletim = TO_DATE( ''".$this->stDtBoletim."'', ''dd/mm/yyyy'') ";
        $stFiltroTransferenciaEstornadaBanco .= " AND TB.dt_boletim = TO_DATE( '||quote_literal('".$this->stDtBoletim."')||', '||quote_literal('dd/mm/yyyy')||' ) ";

        $stFiltroPagamento                   .= " AND TB.dt_boletim = TO_DATE( ''".$this->stDtBoletim."'', ''dd/mm/yyyy'') ";
        $stFiltroPagamentoBanco              .= " AND TB.dt_boletim = TO_DATE( '||quote_literal('".$this->stDtBoletim."')||', '||quote_literal('dd/mm/yyyy')||' ) ";

        $stFiltroPagamentoEstornado          .= " AND TO_DATE(TO_CHAR(tpe.timestamp_anulado,''dd/mm/yyyy''),''dd/mm/yyyy'') = TO_DATE(''".$this->stDtBoletim."'', ''dd/mm/yyyy'') ";
        $stFiltroPagamentoEstornadoBanco     .= " AND TO_DATE(TO_CHAR(tpe.timestamp_anulado,'||quote_literal('dd/mm/yyyy')||'),'||quote_literal('dd/mm/yyyy')||') = TO_DATE( '||quote_literal('".$this->stDtBoletim."')||', '||quote_literal('dd/mm/yyyy')||' ) ";

        $stFiltroArrecadacao                 .= " AND TB.dt_boletim = TO_DATE(''".$this->stDtBoletim."'', ''dd/mm/yyyy'' ) ";

        $stFiltroPagamentoTmp                .= " AND TO_DATE(TO_CHAR(tp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') = TO_DATE(''".$this->stDtBoletim."'',''dd/mm/yyyy'')";
        $stFiltroPagamentoTmpBanco           .= " AND TO_DATE(TO_CHAR(tp.timestamp,'||quote_literal('dd/mm/yyyy')||'),'||quote_literal('dd/mm/yyyy')||') = TO_DATE('||quote_literal('".$this->stDtBoletim."')||','||quote_literal('dd/mm/yyyy')||')";
    }

    if ($this->inCgmUsuario) {
        $stFiltroTransferencia               .= " AND TT.cgm_usuario = ".$this->inCgmUsuario." ";
        $stFiltroTransferenciaBanco          .= " AND TT.cgm_usuario = ".$this->inCgmUsuario." ";

        $stFiltroTransferenciaEstornada      .= " AND TTE.cgm_usuario = ".$this->inCgmUsuario." ";
        $stFiltroTransferenciaEstornadaBanco .= " AND TTE.cgm_usuario = ".$this->inCgmUsuario." ";

        $stFiltroPagamento                   .= " AND TP.cgm_usuario = ".$this->inCgmUsuario." ";
        $stFiltroPagamentoBanco              .= " AND TP.cgm_usuario = ".$this->inCgmUsuario." ";

        $stFiltroPagamentoEstornado          .= " AND TPE.cgm_usuario = ".$this->inCgmUsuario." ";
        $stFiltroPagamentoEstornadoBanco     .= " AND TPE.cgm_usuario = ".$this->inCgmUsuario." ";

        $stFiltroPagamentoTmp                .= " AND tp.cgm_usuario = ".$this->inCgmUsuario." ";
        $stFiltroPagamentoTmpBanco           .= " AND tp.cgm_usuario = ".$this->inCgmUsuario." ";
    }

    if ($this->stExercicio) {
        $stFiltroTransferencia               .= " AND TT.exercicio = ''".$this->stExercicio."''";
        $stFiltroTransferenciaBanco          .= " AND TT.exercicio = '||quote_literal('".$this->stExercicio."')||' ";

        $stFiltroTransferenciaEstornada      .= " AND TTE.exercicio = ''".$this->stExercicio."''";
        $stFiltroTransferenciaEstornadaBanco .= " AND TTE.exercicio = '||quote_literal('".$this->stExercicio."')||' ";

        $stFiltroPagamento                   .= " AND TP.exercicio = ''".$this->stExercicio."''";
        $stFiltroPagamentoBanco              .= " AND TP.exercicio = '||quote_literal('".$this->stExercicio."')||' ";

        $stFiltroPagamentoEstornado          .= " AND TPE.exercicio_boletim = ''".$this->stExercicio."''";
        $stFiltroPagamentoEstornadoBanco     .= " AND TPE.exercicio_boletim = '||quote_literal('".$this->stExercicio."')||' ";

        $stFiltroArrecadacao                 .= " AND TA.exercicio = ''".$this->stExercicio."'' ";

        $stFiltroPagamentoTmp                .= " AND tp.exercicio_boletim = ''".$this->stExercicio."'' ";
        $stFiltroPagamentoTmpBanco           .= " AND tp.exercicio_boletim = '||quote_literal('".$this->stExercicio."')||' ";

    }
    if ($this->stEntidade) {
        $stFiltroTransferencia               .= " AND TT.cod_entidade  in ( ".$this->stEntidade." ) ";
        $stFiltroTransferenciaBanco          .= " AND TT.cod_entidade  in ( ".$this->stEntidade." ) ";

        $stFiltroTransferenciaEstornada      .= " AND TTE.cod_entidade in ( ".$this->stEntidade." ) ";
        $stFiltroTransferenciaEstornadaBanco .= " AND TTE.cod_entidade in ( ".$this->stEntidade." ) ";

        $stFiltroPagamento                   .= " AND TP.cod_entidade  in ( ".$this->stEntidade." ) ";
        $stFiltroPagamentoBanco              .= " AND TP.cod_entidade  in ( ".$this->stEntidade." ) ";

        $stFiltroPagamentoEstornado          .= " AND TPE.cod_entidade in ( ".$this->stEntidade." ) ";
        $stFiltroPagamentoEstornadoBanco     .= " AND TPE.cod_entidade in ( ".$this->stEntidade." ) ";

        $stFiltroArrecadacao                 .= " AND TA.cod_entidade  in ( ".$this->stEntidade." ) ";

        $stFiltroPagamentoTmp                .= " AND TP.cod_entidade  in ( ".$this->stEntidade." ) ";
        $stFiltroPagamentoTmpBanco           .= " AND TP.cod_entidade  in ( ".$this->stEntidade." ) ";

    }
    $this->obFTesourariaEmitirBoletim->setDado( "stExercicio"                         , $this->getExercicio()                );
    $this->obFTesourariaEmitirBoletim->setDado( "stEntidade"                          , $this->getEntidade()                 );
    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroTransferencia"               , $stFiltroTransferencia               );
    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroTransferenciaEstornada"      , $stFiltroTransferenciaEstornada      );
    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroPagamento"                   , $stFiltroPagamento                   );
    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroPagamentoTmp"                , $stFiltroPagamentoTmp                );
    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroPagamentoEstornado"          , $stFiltroPagamentoEstornado          );
    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroArrecadacao"                 , $stFiltroArrecadacao                 );

    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroTransferenciaBanco"          , $stFiltroTransferenciaBanco          );
    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroTransferenciaEstornadaBanco" , $stFiltroTransferenciaEstornadaBanco );
    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroPagamentoBanco"              , $stFiltroPagamentoBanco              );
    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroPagamentoEstornadoBanco"     , $stFiltroPagamentoEstornadoBanco     );
    $this->obFTesourariaEmitirBoletim->setDado( "stFiltroPagamentoTmpBanco"           , $stFiltroPagamentoTmpBanco           );

    if ($this->stTipoEmissao == "caixa") {
        $obErro = $this->geraRecordSetCaixa( $arRecordSet );
    } elseif ($this->stTipoEmissao == "boletim") {
        $obErro = $this->geraRecordSetBoletim( $arRecordSet );
    }

    return $obErro;

}

}

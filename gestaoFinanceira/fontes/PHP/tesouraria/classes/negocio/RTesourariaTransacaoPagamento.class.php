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
    * Classe de Regra de Negócio para Transacao Pagamento
    * Data de Criação   : 26/01/2006

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-04-30 16:21:28 -0300 (Seg, 30 Abr 2007) $

    * Casos de uso: uc-02.04.20,uc-02.03.28
*/

/*
$Log$
Revision 1.10  2007/04/30 19:21:10  cako
implementação uc-02.03.28

Revision 1.9  2007/03/30 21:59:12  cako
Bug #7884#

Revision 1.8  2007/01/24 19:04:38  cako
Bug #7884#

Revision 1.7  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS      ."Transacao.class.php"                         );
include_once ( CAM_GF_TES_NEGOCIO      ."RTesourariaTransacao.class.php"              );
include_once ( CAM_GF_EMP_NEGOCIO      ."REmpenhoPagamentoLiquidacao.class.php"       );

/**
    * Classe de Regra de Transacao Pagamento
    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaTransacaoPagamento extends RTesourariaTransacao
{
/*
    * @var Object
    * @access Private
*/
var $obREmpenhoPagamentoLiquidacao;
/*
    * @var String
    * @access Private
*/
var $stNumDocumento;
/*
    * @var String
    * @access Private
*/
var $stDescricao;
/*
    * @var Object
    * @access Private
*/
var $roRTesourariaBordero;
var $inCodOrdem;

/*
    * @access Public
    * @param String $valor
*/
function setNumDocumento($valor) { $this->stNumDocumento          = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao             = $valor; }
function setCodOrdem($valor) { $this->inCodOrdem              = $valor; }

/*
    * @access Public
    * @return String
*/
function getNumDocumento() { return $this->stNumDocumento;          }
/*
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;             }
function getCodOrdem() { return $this->inCodOrdem;              }

function RTesourariaTransacaoPagamento(&$roRTesourariaBordero)
{
    parent::RTesourariaTransacao();
    $this->roRTesourariaBordero     = &$roRTesourariaBordero;
    $this->obREmpenhoPagamentoLiquidacao = new REmpenhoPagamentoLiquidacao;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransacaoPagamento.class.php"  );
    $obTransacao                         = new Transacao();
    $obTTesourariaTransacaoPagamento = new TTesourariaTransacaoPagamento();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obTTesourariaTransacaoPagamento->setDado('cod_bordero'        , $this->roRTesourariaBordero->getCodBordero() );
        $obTTesourariaTransacaoPagamento->setDado('cod_ordem'          , $this->getCodOrdem());
        $obTTesourariaTransacaoPagamento->setDado('cod_entidade'       , $this->roRTesourariaBordero->obROrcamentoEntidade->getCodigoEntidade() );
        $obTTesourariaTransacaoPagamento->setDado('exercicio'          , $this->roRTesourariaBordero->getExercicio() );
        $obTTesourariaTransacaoPagamento->setDado('cod_tipo'           , $this->getTipo() );
        $obTTesourariaTransacaoPagamento->setDado('cod_banco'          , $this->obRMONAgencia->obRMONBanco->getCodBanco() );
        $obTTesourariaTransacaoPagamento->setDado('cod_agencia'        , $this->obRMONAgencia->getCodAgencia() );
        $obTTesourariaTransacaoPagamento->setDado('conta_corrente'     , $this->getContaCorrente() );
        $obTTesourariaTransacaoPagamento->setDado('documento'          , $this->getNumDocumento() );
        $obTTesourariaTransacaoPagamento->setDado('descricao'          , $this->getDescricao() );

        $obErro = $obTTesourariaTransacaoPagamento->inclusao( $boTransacao );

        // Pagando direto no PR do Borderô
        //if ( !$obErro->ocorreu() ) {
        //    $obErro = $this->obREmpenhoPagamentoLiquidacao->pagarOP( $boTransacao );
        //}
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaBordero );

    return $obErro;

}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function listar(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransacaoPagamento.class.php"          );
    $obTTesourariaTransacaoPagamento          = new TTesourariaTransacaoPagamento();

    if ($this->roRTesourariaBordero->inCodBordero) {
        $stFiltro .= " tb.cod_bordero = " .$this->roRTesourariaBordero->getCodBordero(). " AND";
        $obTTesourariaTransacaoPagamento->setDado ( 'cod_bordero', $this->roRTesourariaBordero->getCodBordero() );
    }

    if ($this->roRTesourariaBordero->obROrcamentoEntidade->inCodigoEntidade) {
        $stFiltro .= " tb.cod_entidade = " .$this->roRTesourariaBordero->obROrcamentoEntidade->getCodigoEntidade(). " AND";
        $obTTesourariaTransacaoPagamento->setDado ( 'cod_entidade', $this->roRTesourariaBordero->obROrcamentoEntidade->getCodigoEntidade() );
    }

    if ($this->roRTesourariaBordero->stExercicio) {
        $stFiltro .= " tb.exercicio = '" .$this->roRTesourariaBordero->getExercicio(). "' AND";
        $obTTesourariaTransacaoPagamento->setDado ( 'exercicio', $this->roRTesourariaBordero->getExercicio() );
    }

    if ($this->roRTesourariaBordero->stCodOrdem) {
        $obTTesourariaTransacaoPagamento->setDado('stCodOrdem', $this->roRTesourariaBordero->stCodOrdem );
    }
    $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';

    $obErro = $obTTesourariaTransacaoPagamento->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function consultarDadosBancariosCGM($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransacaoPagamento.class.php"          );
    $obTTesourariaTransacaoPagamento          = new TTesourariaTransacaoPagamento();

    $obTTesourariaTransacaoPagamento->setDado('numcgm', $this->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNumCGM() );

    $obErro = $obTTesourariaTransacaoPagamento->recuperaDadosBancariosCGM( $rsRecordSet, $boTransacao );

    $this->obRMONAgencia->obRMONBanco->setNumBanco($rsRecordSet->getCampo("num_banco"));
    $this->obRMONAgencia->setNumAgencia($rsRecordSet->getCampo("num_agencia"));
    $this->setContaCorrente($rsRecordSet->getCampo("conta_corrente"));

    return $obErro;

}

}

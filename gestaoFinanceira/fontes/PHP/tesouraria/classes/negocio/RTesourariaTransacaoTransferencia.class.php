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
    * Classe de Regra de Negócio para Transacao Transferencia
    * Data de Criação   : 24/01/2006

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.5  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS      ."Transacao.class.php"                         );
include_once ( CAM_GA_CGM_NEGOCIO      ."RCGM.class.php"                              );
include_once ( CAM_GF_TES_NEGOCIO      ."RTesourariaTransacao.class.php"              );
include_once ( CAM_GF_CONT_NEGOCIO     ."RContabilidadePlanoBanco.class.php"          );

/**
    * Classe de Regra de Transacao Transferencia
    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaTransacaoTransferencia extends RTesourariaTransacao
{
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
    * @var Float
    * @access Private
*/
var $flValorTransferencia;
/*
    * @var Object
    * @access Private
*/
var $roRTesourariaBordero;
/*
    * @var Object
    * @access Private
*/
var $obRCGM;
/*
    * @var Object
    * @access Private
*/
var $obRCcontabilidadePlanoBanco;

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
/*
    * @access Public
    * @param Float  $valor
*/
function setValorTransferencia($valor) { $this->flValorTransferencia    = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setRCGM($valor) { $this->obRCGM                  = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setRContabilidadePlanobanco($valor) { $this->obRContabilidadePlanoBanco                  = $valor; }

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
/*
    * @access Public
    * @return Float
*/
function getValorTransferencia() { return $this->flValorTransferencia;    }
/*
    * @access Public
    * @return Object
*/
function getRContabilidadePlanoBanco() { return $this->obRContabilidadePlanoBanco;                  }

function RTesourariaTransacaoTransferencia(&$roRTesourariaBordero)
{
    parent::RTesourariaTransacao();
    $this->obRCGM                   = new RCGM;
    $this->obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
    $this->roRTesourariaBordero     = &$roRTesourariaBordero;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransacaoTransferencia.class.php"  );
    $obTransacao                         = new Transacao();
    $obTTesourariaTransacaoTransferencia = new TTesourariaTransacaoTransferencia();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {

        $obTTesourariaTransacaoTransferencia->setDado('cod_bordero'        , $this->roRTesourariaBordero->getCodBordero() );
        $obTTesourariaTransacaoTransferencia->setDado('cod_entidade'       , $this->roRTesourariaBordero->obROrcamentoEntidade->getCodigoEntidade() );
        $obTTesourariaTransacaoTransferencia->setDado('numcgm'             , $this->obRCGM->getNumCGM() );
        $obTTesourariaTransacaoTransferencia->setDado('exercicio'          , $this->roRTesourariaBordero->getExercicio() );
        $obTTesourariaTransacaoTransferencia->setDado('cod_tipo'           , $this->getTipo() );
        $obTTesourariaTransacaoTransferencia->setDado('documento'          , $this->getNumDocumento() );
        $obTTesourariaTransacaoTransferencia->setDado('descricao'          , $this->getDescricao() );
        $obTTesourariaTransacaoTransferencia->setDado('valor'              , $this->getValorTransferencia() );
        $obTTesourariaTransacaoTransferencia->setDado('cod_agencia'        , $this->obRMONAgencia->getCodAgencia() );
        $obTTesourariaTransacaoTransferencia->setDado('cod_banco'          , $this->obRMONAgencia->obRMONBanco->getCodBanco() );
        $obTTesourariaTransacaoTransferencia->setDado('conta_corrente'     , $this->getContaCorrente() );
        $obTTesourariaTransacaoTransferencia->setDado('cod_plano'          , $this->obRContabilidadePlanoBanco->getCodPlano() );

        $obErro = $obTTesourariaTransacaoTransferencia->inclusao( $boTransacao );
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
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransacaoTransferencia.class.php"          );
    $obTTesourariaTransacaoTransferencia          = new TTesourariaTransacaoTransferencia();

    if( $this->roRTesourariaBordero->inCodBordero )
        $stFiltro .= " tb.cod_bordero = " .$this->roRTesourariaBordero->getCodBordero(). " AND";

    if( $this->roRTesourariaBordero->obROrcamentoEntidade->inCodigoEntidade )
        $stFiltro .= " tb.cod_entidade = " .$this->roRTesourariaBordero->obROrcamentoEntidade->getCodigoEntidade(). " AND";

    if( $this->roRTesourariaBordero->stExercicio )
        $stFiltro .= " tb.exercicio = '" .$this->roRTesourariaBordero->getExercicio(). "' AND";

    $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';

    $obErro = $obTTesourariaTransacaoTransferencia->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

}

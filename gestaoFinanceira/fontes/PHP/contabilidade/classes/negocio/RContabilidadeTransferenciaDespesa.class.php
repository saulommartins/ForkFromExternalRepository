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
    * Classe de Regra de Negócio de Transferencia de Despesa
    * Data de Criação   : 24/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.04
                    uc-02.01.07
*/

/*
$Log$
Revision 1.7  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoTransferencia.class.php"      );

class RContabilidadeTransferenciaDespesa
{
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var Object
    * @access Private
*/
var $obTContabilidadeTransferenciaDespesa;
/**
    * @var Object
    * @access Private
*/
var $obRContabilidadeLancamentoTransferencia;
/**
    * @var Integer
    * @access Private
*/
var $inCodSuplementacao;
/**
    * @var Object
    * @access Private
*/
var $roROrcamentoSuplementacao;

/**
    * @access Public
    * @param Object $valor
*/
function setTContabilidadeLancamentoTransferencia($valor) { $this->obTContabilidadeLancamentoTransferencia = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodSuplementacao($valor) { $this->inCodSuplementacao = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTipoTransferencia($valor) { $this->inCodTipoTransferencia = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setEstorno($valor) { $this->boEstorno = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRContabilidadeLancamentoTransferencia() { return $this->obRContabilidadeLancamentoTransferencia; }
/**
    * @access Public
    * @return Integer
*/
function getCodSuplementacao() { return $this->inCodSuplementacao; }
/**
    * @access Public
    * @return Integer
*/
function getCodTipoTransferencia() { return $this->inCodTipoTransferencia; }
/**
    * @access Public
    * @return Boolean
*/
function getEstorno() { return $this->boEstorno; }

/**
    * Método Construtor
    * @access Private
    * @param  Object $roROrcamentoSuplementacao
*/
function RContabilidadeTransferenciaDespesa(&$roROrcamentoSuplementacao)
{
    $this->obRContabilidadeLancamentoTransferencia = new RContabilidadeLancamentoTransferencia;
    $this->obTransacao                             = new Transacao;
    $this->roROrcamentoSuplementacao = &$roROrcamentoSuplementacao;
}

/**
    * Inclui Lancamento Transferencia no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeTransferenciaDespesa.class.php"    );
    $obTContabilidadeTransferenciaDespesa    = new TContabilidadeTransferenciaDespesa;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obRContabilidadeLancamentoTransferencia->incluir( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTContabilidadeTransferenciaDespesa->setDado('cod_entidade'     , $this->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade());
            $obTContabilidadeTransferenciaDespesa->setDado('cod_tipo'         , $this->obRContabilidadeLancamentoTransferencia->getCodTipo()           );
            $obTContabilidadeTransferenciaDespesa->setDado('exercicio'        , $this->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio());
            $obTContabilidadeTransferenciaDespesa->setDado('sequencia'        , $this->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->getSequencia() );
            $obTContabilidadeTransferenciaDespesa->setDado('tipo'             , $this->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getTipo());
            $obTContabilidadeTransferenciaDespesa->setDado('cod_lote'         , $this->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote());
            $obTContabilidadeTransferenciaDespesa->setDado('cod_suplementacao', $this->roROrcamentoSuplementacao->getCodSuplementacao()                );
            $obErro = $obTContabilidadeTransferenciaDespesa->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeTransferenciaDespesa );

    return $obErro;
}

/**
    * Anular Transferencia Despesa ( inverte contas )
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeTransferenciaDespesa.class.php"    );
    $obTContabilidadeTransferenciaDespesa    = new TContabilidadeTransferenciaDespesa;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        // IMPLEMENTAR FUTURAMENTE
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeTransferenciaDespesa );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeTransferenciaDespesa.class.php"    );
    $obTContabilidadeTransferenciaDespesa    = new TContabilidadeTransferenciaDespesa;

    // Implementado apenas para recuperar entidade - caso exista necessidade de mais campos pode alterar este metodo
    $obTContabilidadeTransferenciaDespesa->setDado( 'cod_tipo'         , $this->roROrcamentoSuplementacao->getCodTipo()          );
    $obTContabilidadeTransferenciaDespesa->setDado( 'exercicio'        , $this->roROrcamentoSuplementacao->getExercicio()        );
    $obTContabilidadeTransferenciaDespesa->setDado( 'cod_suplementacao', $this->roROrcamentoSuplementacao->getCodSuplementacao() );
    $obTContabilidadeTransferenciaDespesa->setDado( 'tipo'             , 'S'                                               );
    $obErro = $obTContabilidadeTransferenciaDespesa->recuperaPorChave( $rsRecordset, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $rsRecordset->getCampo( 'cod_entidade' ) );
        $this->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( $rsRecordset->getCampo( 'exercicio' ) );
        $this->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $rsRecordset->getCampo( 'cod_lote' ) );
        $this->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( $rsRecordset->getCampo( 'tipo' ) );
        $obErro = $this->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->consultarNomes( $rsEntidade, $boTransacao );
    }

    return $obErro;
}

/**
    * Lista todos os Lancamentos de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $obTransacao = "")
{
    $stFiltro = "";
        // IMPLEMENTAR FUTURAMENTE
    return $obErro;
}

}

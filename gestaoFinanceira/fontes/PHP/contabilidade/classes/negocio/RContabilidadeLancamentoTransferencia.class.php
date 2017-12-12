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
    * Classe de Regra de Negócio Lançamento Transferencia
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
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php"              );

class RContabilidadeLancamentoTransferencia extends RContabilidadeLancamentoValor
{
/**
    * @var Object
    * @access Private
*/
var $obRContabilidadeLancamentoValor;
/**
    * @var Integer
    * @access Private
*/
var $inCodTipo;
/**
    * @var Boolean
    * @access Private
*/
var $boEstorno;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodTipo($valor) { $this->inCodTipo = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setEstorno($valor) { $this->boEstorno = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodTipo() { return $this->inCodTipo; }
/**
    * @access Public
    * @return Boolean
*/
function getEstorno() { return $this->boEstorno; }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeLancamentoTransferencia()
{
    parent::RContabilidadeLancamentoValor();
    $this->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( "S" );
    $this->boEstorno = false;
}

/**
    * Inclui Lancamento Transferencia no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoTransferencia.class.php" );
    $obTContabilidadeLancamentoTransferencia = new TContabilidadeLancamentoTransferencia;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTContabilidadeLancamentoTransferencia->setDado('cod_lote'     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()  );
        $obTContabilidadeLancamentoTransferencia->setDado('tipo'         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()     );
        $obTContabilidadeLancamentoTransferencia->setDado('sequencia'    , $this->obRContabilidadeLancamento->getSequencia()                      );
        $obTContabilidadeLancamentoTransferencia->setDado('exercicio'    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio());
        $obTContabilidadeLancamentoTransferencia->setDado('cod_entidade' , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade());
        $obTContabilidadeLancamentoTransferencia->setDado('cod_tipo'     , $this->inCodTipo                                                       );
        $obTContabilidadeLancamentoTransferencia->setDado('estorno'      , $this->boEstorno                                                       );
        $obErro = $obTContabilidadeLancamentoTransferencia->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoTransferencia );

    return $obErro;
}

/**
    * Anular Receita ( inverte contas )
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoTransferencia.class.php" );
    $obTContabilidadeLancamentoTransferencia = new TContabilidadeLancamentoTransferencia;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        // IMPLEMENTAR FUTURAMENTE
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoTransferencia );

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
//     $obErro = parent::consultar( $boTransacao );
//    if ( !$obErro->ocorreu() ) {
        // IMPLEMENTAR FUTURAMENTE
//    }
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

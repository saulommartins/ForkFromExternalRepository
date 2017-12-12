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
    * Classe de regra de negócio para arrecadacao tipo suspensao
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra

    * $Id: RARRTipoSuspensao.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.07
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRTipoSuspensao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRSuspensao.class.php" );

/**
    * Classe de regra de negócio para arrecadacao tipo-suspensao
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra
*/

class RARRTipoSuspensao
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTipoSuspensao;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var Boolean
*/
var $boEmitir;
/**
    * @access Private
    * @var Object
*/
var $obTARRTipoSuspensao;

// SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoTipoSuspensao($valor) { $this->inCodigoTipoSuspensao = $valor ; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao           = $valor ; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setEmitir($valor) { $this->boEmitir              = $valor ; }

// GETTERES
/**
    * @access Public
    * @return Integer
*/
function getCodigoTipoSuspensao() { return $this->inCodigoTipoSuspensao ; }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao           ; }
/**
    * @access Public
    * @return Boolean
*/
function getEmitir() { return $this->boEmitir              ; }

/**
     * Método construtor
     * @access Private
*/
function RARRTipoSuspensao()
{
    $this->obTARRTipoSuspensao  = new TARRTipoSuspensao;
    $this->obTransacao          = new Transacao;
}

/**
    * Inclui os dados referentes ao tipo de suspensao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirTipoSuspensao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTARRTipoSuspensao->proximoCod( $this->inCodigoTipoSuspensao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            //EXECUTA A INCLUSAO NA TABELA TIPO_SUSPENSAO
            $this->obTARRTipoSuspensao->setDado( "cod_tipo_suspensao"   , $this->inCodigoTipoSuspensao  ) ;
            $this->obTARRTipoSuspensao->setDado( "descricao"            , $this->stDescricao            ) ;
            $this->obTARRTipoSuspensao->setDado( "emitir"               , $this->boEmitir               ) ;
            $obErro = $this->obTARRTipoSuspensao->inclusao( $boTransacao );
        }
    }
   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRTipoSuspensao );

   return $obErro;
}
/**
    * alterar os dados referentes ao tipo de pagamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarTipoSuspensao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$obErro->ocorreu() ) {
            //EXECUTA A ALTERACAO NA TABELA DE TIPO PAGAMENTO
            $this->obTARRTipoSuspensao->setDado( "cod_tipo_suspensao"   , $this->inCodigoTipoSuspensao  ) ;
            $this->obTARRTipoSuspensao->setDado( "descricao"            , $this->stDescricao            ) ;
            $this->obTARRTipoSuspensao->setDado( "emitir"               , $this->boEmitir               ) ;
            $obErro = $this->obTARRTipoSuspensao->alteracao( $boTransacao );
        }
    }
   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRTipoSuspensao );

   return $obErro;
}
/**
    * Inclui os dados referentes ao tipo de suspensao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirTipoSuspensao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    $obTARRSuspensao = new TARRSuspensao;
    if ( !$obErro->ocorreu() ) {
        $obErro = $obTARRSuspensao->recuperaTodos( $rsListaSuspensao, $stFiltro, "", $boTransacao );
        if ( !$rsListaSuspensao->eof() ) {
            $obErro = new Erro;
            $obErro->setDescricao( "Tipo de suspensão está sendo utilizado pelo sistema e por isto não pode ser excluido!" );
        }

        if ( !$obErro->ocorreu() ) {
            //EXECUTA A EXCLUSAO NA TABELA ATIVIDADE
            $this->obTARRTipoSuspensao->setDado( "cod_tipo_suspensao", $this->inCodigoTipoSuspensao );
            $obErro = $this->obTARRTipoSuspensao->exclusao( $boTransacao );
        }
    }
   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRTipoSuspensao );

   return $obErro;
}

/**
    * Inclui os dados referentes ao tipo de suspensao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTipoSuspensao(&$rsRecordset, $boTransacao = "")
{
    $boFlagTransacao = false;
    // filtro
    if ($this->getCodigoTipoSuspensao() )
        $stFiltro = "\r\n\t WHERE cod_tipo_suspensao = ".$this->getCodigoTipoSuspensao();
    // ordem
    $stOrdem    = "\r\n\tORDER BY cod_tipo_suspensao";
    // faz listagem
    $obErro = $this->obTARRTipoSuspensao->recuperaTodos($rsRecordset, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

/**
    * Recupera do banco de dados os dados da Atividade selecionada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarTipoSuspensao($boTransacao = "")
{
    $obErro = new Erro;
    if ($this->inCodigoTipoSuspensao) {
        $obErro = $this->listarTipoPagamento( $rsTipoSuspensao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->inCodigoTipoSuspensao= $rsTipoSuspensao->getCampo( "cod_tipo_suspensao" );
            $this->stDescricao          = $rsTipoSuspensao->getCampo( "descricao"          );
            $this->boEmitir             = $rsTipoSuspensao->getCampo( "emitir"             );
        }
    }

    return $obErro;
}

}

?>

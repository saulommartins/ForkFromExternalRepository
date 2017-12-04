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
    * Classe de regra de negócio para arrecadacao tipo pagamento
    * Data de Criação: 05/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra

    * $Id: RARRTipoPagamento.class.php 59612 2014-09-02 12:00:51Z gelson $
    Caso de uso: uc-05.03.09
*/

/*
$Log$
Revision 1.11  2006/09/15 11:50:14  fabio
corrigidas tags de caso de uso

Revision 1.10  2006/09/15 10:48:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRTipoPagamento.class.php"       );
/**
    * Classe de regra de negócio para Localizacao
    * Data de Criação: 17/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo
    * @package URBEM
    * @subpackage Regra
*/

class RARRTipoPagamento
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTipo;
/**
    * @access Private
    * @var String
*/
var $stNomeTipo;
/**
    * @access Private
    * @var String
*/
var $stNomeResumido;
/**
    * @access Private
    * @var Boolean
*/
var $boSistema;
/**
    * @access Private
    * @var Boolean
*/
var $boPagamento;
/**
    * @access Private
    * @var Object
*/
var $obTARRTipoPagamento;

// SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoTipo($valor) { $this->inCodigoTipo = $valor    ;}
/**
    * @access Public
    * @param String $valor
*/
function setNomeTipo($valor) { $this->stNomeTipo   = $valor    ;}
/**
    * @access Public
    * @param String $valor
*/
function setNomeResumido($valor) { $this->stNomeResumido   = $valor    ;}
/**
    * @access Public
    * @param Boolean $valor
*/
function setSistema($valor) { $this->boSistema    = $valor    ;}
/**
    * @access Public
    * @param Boolean $valor
*/
function setPagamento($valor) { $this->boPagamento    = $valor    ;}

// GETTERES
/**
    * @access Public
    * @return Integer
*/
function getCodigoTipo() { return $this->inCodigoTipo  ; }
/**
    * @access Public
    * @return String
*/
function getNomeTipo() { return $this->stNomeTipo    ; }
/**
    * @access Public
    * @return String
*/
function getNomeResumido() { return $this->stNomeResumido    ; }
/**
    * @access Public
    * @return Boolean
*/
function getSistema() { return $this->boSistema     ; }
/**
    * @access Public
    * @return Boolean
*/
function getPagamento() { return $this->boPagamento     ; }

/**
     * Método construtor
     * @access Private
*/
function RARRTipoPagamento()
{
    $this->obTARRTipoPagamento  = new TARRTipoPagamento;
    $this->obTransacao          = new Transacao;
}

/**
    * Inclui os dados referentes ao tipo de pagamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirTipoPagamento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        // VERIFICA NOME
        $obErro = $this->VerificaTipoPagamento($rsTipoPagamento,$boTransacao);
        if ( !$obErro->ocorreu() && $rsTipoPagamento->getNumLinhas() > 0  ) {
            $obErro->setDescricao("Tipo de Baixa já cadastrada no sistema - ".$this->getNomeTipo()."");
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTARRTipoPagamento->proximoCod( $this->inCodigoTipo, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                //EXECUTA A INCLUSAO NA TABELA TIPO_PAGAMENTO
                $this->obTARRTipoPagamento->setDado( "cod_tipo"     , $this->inCodigoTipo         );
                $this->obTARRTipoPagamento->setDado( "nom_tipo"     , trim($this->stNomeTipo)     );
                $this->obTARRTipoPagamento->setDado( "nom_resumido" , trim($this->stNomeResumido) );
                $this->obTARRTipoPagamento->setDado( "pagamento"    , $this->getPagamento()       );
                $obErro = $this->obTARRTipoPagamento->inclusao( $boTransacao );
            }
        }
    }
   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRTipoPagamento );

   return $obErro;
}

/**
    * alterar os dados referentes ao tipo de pagamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarTipoPagamento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        // VERIFICA NOME
        $obErro = $this->VerificaTipoPagamento($rsTipoPagamento,$boTransacao);
        if ( !$obErro->ocorreu() && $rsTipoPagamento->getNumLinhas() > 0  ) {
            $obErro->setDescricao("Tipo de Baixa já cadastrada no sistema - ".$this->getNomeTipo()."");
        }
        if ( !$obErro->ocorreu() ) {
            //EXECUTA A ALTERACAO NA TABELA DE TIPO PAGAMENTO
            $this->obTARRTipoPagamento->setDado( "cod_tipo"     , $this->inCodigoTipo         );
            $this->obTARRTipoPagamento->setDado( "nom_tipo"     , trim($this->stNomeTipo)     );
            $this->obTARRTipoPagamento->setDado( "nom_resumido" , trim($this->stNomeResumido) );
            $this->obTARRTipoPagamento->setDado( "pagamento"    , $this->getPagamento()       );
            $obErro = $this->obTARRTipoPagamento->alteracao( $boTransacao );
            //$this->obTARRTipoPagamento->debug(); exit;
        }
    }

   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRTipoPagamento );

   return $obErro;
}
/**
    * Inclui os dados referentes ao tipo de pagamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirTipoPagamento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //EXECUTA A EXCLUSAO NA TABELA ATIVIDADE
        $this->obTARRTipoPagamento->setDado( "cod_tipo", $this->inCodigoTipo );
        $obErro = $this->obTARRTipoPagamento->exclusao( $boTransacao );
    }
   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRTipoPagamento );

   return $obErro;
}

/**
    * Inclui os dados referentes ao tipo de pagamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTipoPagamento(&$rsRecordset, $boTransacao = "")
{
    $boFlagTransacao = false;

    $stFiltro = "";
    if ( $this->getNomeTipo()) {
        $stFiltro = " \n\n lower(trim(nom_tipo)) = '".strtolower(trim($this->getNomeTipo()))."' AND\n";
    }
    if ( !$this->getSistema() ) {
        $stFiltro  .= "\n sistema = false AND";
    }
    if ( $this->getCodigoTipo() ) {
        $stFiltro  .= "\n cod_tipo = ". $this->getCodigoTipo() ." AND ";
    }
    if ( $this->getPagamento() ) {
        $stFiltro  .= "\n pagamento = '". $this->getPagamento() ."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = "     WHERE cod_tipo > 1 AND ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrdem    = "\n ORDER BY cod_tipo";

    $obErro = $this->obTARRTipoPagamento->recuperaTodos($rsRecordset, $stFiltro, $stOrdem, $boTransacao );
    //$this->obTARRTipoPagamento->debug();
    return $obErro;
}

/**
    * Inclui os dados referentes ao tipo de pagamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function VerificaTipoPagamento(&$rsRecordset, $boTransacao = "")
{
    $boFlagTransacao = false;

    $stFiltro = "";
    if ( $this->getNomeTipo()) {
        $stFiltro = " \n\n lower(trim(nom_tipo)) = '".strtolower(trim($this->getNomeTipo()))."' AND\n";
    }
    if ( !$this->getSistema() ) {
        $stFiltro  .= "\n sistema = false AND";
    }
    if ( $this->getCodigoTipo() ) {
        $stFiltro  .= "\n cod_tipo != ". $this->getCodigoTipo() ." AND ";
    }
    if ($stFiltro) {
        $stFiltro = "     WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrdem    = "\n ORDER BY cod_tipo";

    $obErro = $this->obTARRTipoPagamento->recuperaTodos($rsRecordset, $stFiltro, $stOrdem, $boTransacao );
    //$this->obTARRTipoPagamento->debug();
    return $obErro;
}

}

?>

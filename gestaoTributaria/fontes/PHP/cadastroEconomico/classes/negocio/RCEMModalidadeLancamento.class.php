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
    * Classe de regra de negócio para Modalidade de Lançamento
    * Data de Criação: 03/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMModalidadeLancamento.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.4  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMModalidadeLancamento.class.php" );

/**
* Classe de regra de negócio para Modalidade de Lançamento
* Data de Criação: 30/01/2005

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCEMModalidadeLancamento
{
/**
* @access Private
* @var Integer
*/
var $inCodigoModalidade;
/*
* @access Private
* @var String
*/
var $stDescricao;
/**
* @access Private
* @var Object
*/
var $obTCEMModalidadeLancamento;
/**
* @access Private
* @var Object
*/
var $obRCEMAtividade;
/**
* @access Private
* @var Object
*/
var $obRCEMInscricaoEconomica;

//SETTERS
/**
* @access Public
* @param Integer $valor
*/
function setCodigoModalidade($valor) { $this->inCodigoModalidade = $valor; }
/**
* @access Public
* @param String $valor
*/
function setDescricao($valor) { $this->stDescricao = $valor;        }

//GETTERS
/**
* @access Public
* @return Integer
*/
function getCodigoModalidade() { return $this->inCodigoModalidade; }
/**
* @access Public
* @return String
*/
function getDescricao() { return $this->stDescricao;        }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCEMModalidadeLancamento()
{
    $this->obTCEMModalidadeLancamento = new TCEMModalidadeLancamento;
    $this->obTransacao                = new Transacao;
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)
/**
    * Seta dados na tabela para inclusao, alteracao e exclusao
    * @access Private
*/
function setarDados()
{
    $this->obTCEMModalidadeLancamento->setDado( "cod_modalidade", $this->inCodigoModalidade );
    $this->obTCEMModalidadeLancamento->setDado( "nom_modalidade", $this->stDescricao        );
}

/**
* Inclui os dados setados na tabela de Modalidade de Lancamento
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function definirModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCEMModalidadeLancamento->proximoCod( $this->inCodigoModalidade, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setarDados();
            $obErro = $this->obTCEMModalidadeLancamento->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMModalidadeLancamento );

    return $obErro;
}

/**
* Altera os dados da Modalidade de Lancamento setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setarDados();
        $obErro = $this->obTCEMModalidadeLancamento->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMModalidadeLancamento );

    return $obErro;
}

/**
* Exclui os dados da Modalidade de Lancamento setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setarDados();
        $obErro = $this->obTCEMModalidadeLancamento->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMModalidadeLancamento );

    return $obErro;
}

/**
    * Lista as Modalidades de Lancamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarModalidade(&$rsLista, $boTransacao = "")
{
    $stOrder = " ORDER BY cod_modalidade ";
    $obErro = $this->obTCEMModalidadeLancamento->recuperaTodos( $rsLista, "", $stOrder, $boTransacao );

    return $obErro;
}

}

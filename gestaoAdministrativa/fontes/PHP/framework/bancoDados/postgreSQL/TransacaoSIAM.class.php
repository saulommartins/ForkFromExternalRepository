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
    * Classe responsável pela manipulação da transação com o banco de dados
    * Data de Criação   : 05/02/2004

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Diego Barbosa Victoria

    * @package Conectividade
    * @subpackage PostgreSQL

Casos de uso: uc-01.01.00

*/

/**
    * Classe responsável pela manipulação da transação com o banco de dados
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Diego Barbosa Victoria
*/
class TransacaoSIAM extends ConexaoSIAM
{
/**
    * @var Integer
    * @access Private
*/
var $inTransacao;

/**
    * @access Public
    * @param Integer $valor
*/
function setTransacao($valor) { $this->inTransacao = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getTransacao() { return $this->inTransacao; }

/**
    * Método Construtor
    * @access Private
*/
function TransacaoSIAM()
{
    parent::ConexaoSIAM();
}

/**
    * Abre uma conexão com o banco de dados, e executa o BEGIN; para início da transação
    * @access Public
    * @return Object  Objeto Erro
*/
function begin($boTransacao = "")
{
    $obErro = new Erro;
    $obErro = $this->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->abreConexao();
        if ( !$obErro->ocorreu() ) {
            $inTransacao = pg_query( $this->getConnection(), "begin;" );
            $obErro->setDescricao( pg_last_error( $this->getConnection() ) );
        } else {
            $this->fechaConexao();
        }
        if ( !$obErro->ocorreu() ) {
            $this->setTransacao( $inTransacao );
        } else {
            $obErro->setDescricao( "Erro abrindo a transação de banco!" );
        }
    }

    return $obErro;
}
/**
    * Comita a transação com o banco de dados mas mantêm a conexão aberta.
    * @access Public
    * @return Object  Objeto Erro
*/
function commit($boTransacao = "")
{
    $obErro = new Erro;
    $obErro = $this->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->abreConexao();
        if ( !$obErro->ocorreu() ) {
            $inResult = pg_query( $this->getConnection(), "commit" );
            $obErro->setDescricao( pg_last_error( $this->getConnection() ) );
        } else {
            $this->fechaConexao();
        }
    }

    return $obErro;
}
/**
    * Comita a transação com o banco de dados e fecha a conexão.
    * @access Public
    * @return Object  Objeto Erro
*/
function commitAndClose($boTransacao = "")
{
    $obErro = new Erro;
    $obErro = $this->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->abreConexao();
        if ( !$obErro->ocorreu() ) {
            $inResult = pg_query( $this->getConnection(), "commit" );
            $obErro->setDescricao( pg_last_error( $this->getConnection() ) );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->fechaConexao();
            }
        }
    }

    return $obErro;
}
/**
    * Executa um rollback e desfaz as operações realizadas dentro da transação mas mantêm a conexão aberta.
    * @access Public
    * @return Object  Objeto Erro
*/
function rollback($boTransacao = "")
{
    $obErro = new Erro;
    $obErro = $this->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->abreConexao();
        if ( !$obErro->ocorreu() ) {
            $inResult = pg_query( $this->getConnection(), "rollback" );
            $obErro->setDescricao( pg_last_error( $this->getConnection() ) );
        } else {
            $obErro = $this->fechaConexao();
        }
    }

    return $obErro;
}
/**
    * Executa um rollback e desfaz as operações realizadas dentro da transação além de fechar a conexão.
    * @access Public
    * @return Object  Objeto Erro
*/
function rollbackAndClose($boTransacao = "")
{
    $obErro = new Erro;
    $obErro = $this->buscaParametros( $boTransacao );
    $obErro = $this->abreConexao();
    if ( !$obErro->ocorreu() ) {
        $inResult = pg_query( $this->getConnection(), "rollback" );
        $obErro->setDescricao( pg_last_error( $this->getConnection() ) );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->fechaConexao( $boTransacao );
        }
    }

    return $obErro;
}
/**
    * Responsável por abrir uma Transação
    * @access Public
    * @param Boolean $boFlagTransacao
    * @param Boolean $boTransacao
    * @return Object Objeto Erro
*/
function abreTransacao(&$boFlagTransacao, &$boTransacao)
{
    $obErro = new Erro;
    if ( empty( $boTransacao ) ) {
        $obErro = $this->begin();
        if ( $obErro->ocorreu() ) {
            return $obErro;
        } else {
            $boTransacao = $this->getTransacao();
            $boFlagTransacao = true;
        }
    }

    return $obErro;
}
/**
    * Responsável por fechar uma Transação
    * @access Public
    * @param Boolean $boFlagTransacao
    * @param Boolean $boTransacao
    * @param Object  Objeto Erro
    * @param Object  Objeto Mapeamento
*/
function fechaTransacao(&$boFlagTransacao, &$boTransacao, &$obErro, $obMapeamento = "")
{
    if ($boFlagTransacao) {
        $obAuditoria = new Auditoria;
        if ( !$obErro->ocorreu() ) {
            $obAuditoria->setTransacao( 'true' );
            $obErro = $obAuditoria->incluiAuditoria( $obMapeamento, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->commitAndClose( $boTransacao );

                return 0;
            }
        }
        if ( $obErro->ocorreu() ) {
            $obErroRollBack = $this->rollbackAndClose( $boTransacao );
            if ( $obErroRollBack->ocorreu() ) {
                $obErro = $obErroRollBack;
            }
            $obAuditoria->setTransacao( 'false' );
            $obErroAuditoria = $obAuditoria->incluiAuditoria( $obMapeamento, $boTransacao );
            if ( !$obErroAuditoria->ocorreu() ) {
                $obErro->setDescricao( $obErro->getDescricao()." Erro auditado!" );
            }
        }
    }
}

}

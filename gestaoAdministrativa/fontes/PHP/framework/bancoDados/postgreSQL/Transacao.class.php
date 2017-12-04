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
 * Data de Criação: 05/02/2004
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Desenvolvedor: Diego Barbosa Victoria
 * @package bancoDados
 * @subpackage postgreSQL
 Casos de uso: uc-01.01.00
 */
/**
 * Classe responsável pela manipulação da transação com o banco de dados
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Documentor: Diego Barbosa Victoria
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once (CLA_CONEXAO);

class Transacao extends Conexao
{
    public $obResource;
    public $inTransacao;
    public $obMapeamento;

    public function setResource($valor) { $this->obResource   = $valor;  }
    public function setTransacao($valor) { $this->inTransacao  = $valor;  }
    public function setMapeamento(&$valor) { $this->obMapeamento = &$valor; }

    public function getResource() { return $this->obResource;   }
    public function getTransacao() { return $this->inTransacao;  }
    public function getMapeamento() { return $this->obMapeamento; }

    public function Transacao()
    {
        parent::Conexao();

    }
    /**
     * Abre uma conexão com o banco de dados, e executa o BEGIN; para início da transação
     * @access Public
     * @return Object Objeto Erro
     */

    public function begin()
    {
        $obErro = new Erro;
        $obErro = $this->abreConexao();

        if (!$obErro->ocorreu()) {

            $obResource = pg_query($this->getConnection() , "begin;");
            $obErro->setDescricao(pg_last_error($this->getConnection()));
        }

        if (!$obErro->ocorreu()) {

            $this->setResource($obResource);

        } else {
            $obErro->setDescricao("Erro abrindo a transação de banco!");

            if (Sessao::getTrataExcecao()) {
                $excecao = Sessao::getExcecao();
                $excecao->setDescricao($obErro->getDescricao());
                $excecao->tratarErro();
            }

        }

        return $obErro;

    }
    /**
     * Comita a transação com o banco de dados mas mantêm a conexão aberta.
     * @access Public
     * @return Object Objeto Erro
     */

    public function commit()
    {
        $obErro = new Erro;
        $obErro = $this->abreConexao();

        if (!$obErro->ocorreu()) {

            $inResult = pg_query($this->getConnection() , "commit");
            $obErro->setDescricao(pg_last_error($this->getConnection()));

        } else {
            $this->fechaConexao();

        }

        return $obErro;

    }
    /**
     * Comita a transação com o banco de dados e fecha a conexão.
     * @access Public
     * @return Object Objeto Erro
     */

    public function commitAndClose()
    {
        $obErro = new Erro;
        $obErro = $this->abreConexao();

        if (!$obErro->ocorreu()) {

            $inResult = pg_query($this->getConnection() , "commit");
            $obErro->setDescricao(pg_last_error($this->getConnection()));

            if (!$obErro->ocorreu()) {

                $obErro = $this->fechaConexao();

            }

        }

        return $obErro;

    }
    /**
     * Executa um rollback e desfaz as operações realizadas dentro da transação mas mantêm a conexão aberta.
     * @access Public
     * @return Object Objeto Erro
     */

    public function rollback()
    {
        $obErro = new Erro;
        $obErro = $this->abreConexao();

        if (!$obErro->ocorreu()) {

            $inResult = pg_query($this->getConnection() , "rollback");
            $obErro->setDescricao(pg_last_error($this->getConnection()));

        } else {
            $obErro = $this->fechaConexao();

        }

        return $obErro;

    }
    /**
     * Executa um rollback e desfaz as operações realizadas dentro da transação além de fechar a conexão.
     * @access Public
     * @return Object Objeto Erro
     */

    public function rollbackAndClose()
    {
        $obErro = new Erro;
        $obErro = $this->abreConexao();

        if (!$obErro->ocorreu()) {

            $inResult = pg_query($this->getConnection() , "rollback");
            $obErro->setDescricao(pg_last_error($this->getConnection()));

            if (!$obErro->ocorreu()) {

                $obErro = $this->fechaConexao();

            }

        }

        return $obErro;

    }
    /**
     * Responsável por abrir uma Transação
     * @access Public
     * @param  Boolean $boFlagTransacao
     * @param  Boolean $boTransacao
     * @return Object  Objeto Erro
     */

    public function abreTransacao(&$boFlagTransacao, &$boTransacao)
    {
        $obErro = new Erro;

        if (empty($boTransacao)) {

            $obErro = $this->begin();

            if ($obErro->ocorreu()) {
                return $obErro;

            } else {
                $boTransacao = $boFlagTransacao = true;

                # Auditoria deve usar o mesmo resource de conexão da Transação
                $obAuditoria = new Auditoria(true);
                $obAuditoria->setConnection($this->getConnection());
                Sessao::write('obAuditoria', $obAuditoria);
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

    public function fechaTransacao(&$boFlagTransacao, &$boTransacao, &$obErro, $obMapeamento = "")
    {
        if ($boFlagTransacao) {

            $obAuditoria = Sessao::read('obAuditoria'); //recupera da Sessão.
            if (is_null($obAuditoria)) { //caso não exista, instancia,
                $obAuditoria = new Auditoria(true);
            }

            if (!$obErro->ocorreu()) {
                $obAuditoria->setTransacao($boTransacao);
                $obErro = $obAuditoria->salvar($obMapeamento, $boTransacao);

                if (!$obErro->ocorreu()) {

                    $obErro = $this->commitAndClose();
                    Sessao::remove('obAuditoria');

                    return 0;
                }

            }

            if ($obErro->ocorreu()) {

                $obErroRollBack = $this->rollbackAndClose();

                if ($obErroRollBack->ocorreu()) {

                    $obErro = $obErroRollBack;

                }

                $obAuditoria->setTransacao('false');
                $obErroAuditoria = $obAuditoria->incluiAuditoria($obMapeamento, $boTransacao);

                if (!$obErroAuditoria->ocorreu()) {

                    $obErro->setDescricao($obErro->getDescricao() . " Erro auditado!");

                }

            }

        }
    }

    public function encerraTransacao()
    {
        $boExcecao = Sessao::getExcecao();
        $obErro = new Erro();

        $obMapeamento = $this->getMapeamento();
        $obAuditoria = Sessao::read('obAuditoria');

        if (is_null($obAuditoria)) {
            $obAuditoria = new Auditoria(true);
        }

        if (!$boExcecao->ocorreu()) {

            $obErro = $obAuditoria->salvar($obMapeamento, $obAuditoria->getTransacao());

            if (!$obErro->ocorreu()) {
                $obErro = $this->commitAndClose();
                Sessao::remove('obAuditoria');

                return 0;
            } else {
                $obErroRollBack = $this->rollbackAndClose();

                if ($obErroRollBack->ocorreu()) {
                    $obErro = $obErroRollBack;
                }
                $obAuditoria->setTransacao('false');
                $obErroAuditoria = $obAuditoria->incluiAuditoria($obMapeamento, Sessao::getTrataExcecao());

                if (!$obErroAuditoria->ocorreu()) {
                    $obErro->setDescricao($obErro->getDescricao() . " Erro auditado!");
                }
            }
        } else {
            $obErroRollBack = $this->rollbackAndClose();

            if ($obErroRollBack->ocorreu()) {
                $obErro = $obErroRollBack;
            }
            $obAuditoria->setTransacao('false');
            $obErroAuditoria = $obAuditoria->incluiAuditoria($obMapeamento, Sessao::getTrataExcecao());

            if (!$obErroAuditoria->ocorreu()) {
                $obErro->setDescricao($obErro->getDescricao() . " Erro auditado!");
            }
            $boExcecao->tratarErro();
        }
        Sessao::remove('obAuditoria');
        Sessao::setTrataExcecao(false);
    }
    /**
     * Retorna codigo OID para inserir dados binarios no banco
     */

    public function retornaOID($boTransacao = '')
    {

        if (Sessao::getTrataExcecao()) {
            $transacao = Sessao::getTransacao();
            $oid = pg_lo_create($transacao->getConnection());

        } else {
            $obErro = $this->abreTransacao($boFlagTransacao, $boTransacao);

            if (!$obErro->ocorreu()) {

                $oid = pg_lo_create($this->getConnection());

            }

        }

        return $oid;

    }

}

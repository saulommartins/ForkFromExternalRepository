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
 * Classe de Conexão
 * Data de Criação: 05/02/2004
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Documentor: Diego Barbosa Victoria
 * @package bancoDados
 * @subpackage postgreSQL
 Casos de uso: uc-01.01.00
 */
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once (CLA_OBJETO);

/**
 * Classe que faz a conexão com uma base PostgreSql e executa querys
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 */

class Conexao extends Objeto
{
    /**
     * @var String
     * @access Private
     */
    public $stHost;
    /**
     * @var String
     * @access Private
     */
    public $stDbName;
    /**
     * @var String
     * @access Private
     */
    public $stUser;
    /**
     * @var String
     * @access Private
     */
    public $stPassWord;
    /**
     * @var Integer
     * @access Private
     */
    public $inPort;
    /**
     * @var resource
     * @access Private
     */
    private static $inConnection;
    /**
     * @var Booleano
     * @access Public
     */
    public $boMostraConexao = FALSE;

    /**
     * @access Public
     * @param String $valor
     */

    public function setHost($valor)
    {
        $this->stHost = $valor;

    }
    /**
     * @access Public
     * @param String $valor
     */

    public function setDbName($valor)
    {
        $this->stDbName = $valor;

    }
    /**
     * @access Public
     * @param String $valor
     */

    public function setUser($valor)
    {
        $this->stUser = $valor;

    }
    /**
     * @access Public
     * @param String $valor
     */

    public function setPassWord($valor)
    {
        $this->stPassWord = $valor;

    }
    /**
     * @access Public
     * @param Integer $valor
     */

    public function setPort($valor)
    {
        $this->inPort = $valor;

    }
    /**
     * @access Public
     * @param Integer $valor
     */

    public function setConnection($valor)
    {
        self::$inConnection = $valor;

    }
    /**
     * @access Public
     * @return String
     */

    public function getHost()
    {
        return $this->stHost;

    }
    /**
     * @access Public
     * @return String
     */

    public function getDbName()
    {
        return $this->stDbName;

    }
    /**
     * @access Public
     * @return String
     */

    public function getUser()
    {
        return $this->stUser;

    }
    /**
     * @access Public
     * @return String
     */

    public function getPassWord()
    {
        return $this->stPassWord;

    }
    /**
     * @access Public
     * @return Integer
     */

    public function getPort()
    {
        return $this->inPort;

    }
    /**
     * @access Public
     * @return Integer
     */

    public function getConnection()
    {
        return self::$inConnection;

    }
    /**
     * Método Construtor
     * @access Private
     */

    public function Conexao()
    {
        $this->setHost(BD_HOST);
        $this->setPort(BD_PORT);
        $this->setDbName(BD_NAME);
        $this->setUser(BD_USER);
        $this->setPassWord(BD_PASS);

    }
    /**
     * Efetua abertura de conexão com o banco de dados
     * @param  Boolean $boRetornaObjeto
     * @return Object  Objeto Erro
     */

    public function abreConexao($boRetornaObjeto = false)
    {

        $obErro = new Erro;
        $options = array();

        if (is_null($this->getConnection()) || $boRetornaObjeto) {
            if ($this->getHost()) {
                $options[] = "host=".$this->getHost();

            }

            if ($this->getPort()) {
                $options[] = "port=".$this->getPort();

            }

            if ($this->getDbName()) {
                $options[] = "dbname=".$this->getDbName();

            }

            if ($this->getUser()) {
                $options[] = "user='".$this->getUser()."'";

            }

            if ($this->getPassWord()) {
                $options[] = "password='".$this->getPassWord()."'";

            }

            //sort($options);
            $stConnection = implode(" ", $options);
            $inConnection = pg_connect($stConnection);

            if (!$inConnection) {

                $obErro->setDescricao("Não foi possível efetuar a conexão!");

            } else {
                if ($boRetornaObjeto) {
                    return $inConnection;
                }

                $this->setConnection($inConnection);

            }
        }

        return $obErro;

    }
    /**
     * Fecha conexão com o banco de dados
     * @access Private
     * @return Object Objeto Erro
     */

    public function fechaConexao()
    {
        $obErro = new Erro;

        //Fecha a conexão.
        $inConnection = pg_close($this->getConnection());
        $this->setConnection(null);

        if (!$inConnection) {

            $obErro->setDescricao(pg_last_error($this->getConnection()));

        }

        return $obErro;

    }
    /**
     * Executa comando DML no banco
     * @access Private
     * @param  String  $stSql
     * @param  Boolean $boTransacao
     * @return Object  Objeto Erro
     */

    public function executaDML($stSql, $boTransacao = "")
    {
        $stSql = $this->filtroEntidadeRh($stSql);
        #if (constant('ENV_TYPE') == 'dev') {
        #    echo '<pre class="debug">';
        #    echo '<h7 class="debug">DebugSQL</h7>';
        #    echo $stSql;
        #    echo '</pre>';
        #}

        if (Sessao::getTrataExcecao()) {

            $this->_executaDML($stSql);

            return Sessao::getExcecao();

        } else {
            return $this->__executaDML($stSql, $boTransacao);

        }

    }
    
    public function mostraConexao($inConexao, $stSql)
    {
        if($this->boMostraConexao){
            sistemaLegado::mostraVar('ID CONEXÃO: '.$inConexao);
            sistemaLegado::mostraVar($stSql);
        }
    }

    public function _executaDML($stSql)
    {
        $excecao = Sessao::getExcecao();
        if (!$excecao->ocorreu()) {
            $transacao = Sessao::getTransacao();
            $this->mostraConexao($transacao->getConnection(), $stSql);
            $inResult = pg_query($transacao->getConnection() , $stSql);
            $excecao->setDescricao(pg_last_error($transacao->getConnection()));

        }

        if ($excecao->ocorreu()) {

            $excecao->tratarErro();

        }

    }

    public function __executaDML($stSql, $boTransacao)
    {
        $obErro = new Erro;
        $obErro = $this->abreConexao();

        if (!$obErro->ocorreu()) {
            $this->mostraConexao($this->getConnection(), $stSql);
            $inResult = pg_query($this->getConnection() , $stSql);
            $obErro->setDescricao(pg_last_error($this->getConnection()));

        }

        if (!$boTransacao) {

            $this->fechaConexao();

        }

        return $obErro;

    }
    /**
     * Grava bloco de dados binario no banco
     * @param Integer $inOID   codigo OID para inserir dados
     * @param String  $stDados dados a serem inseridos
     */

    public function gravaBlob($inOID, $stDados)
    {

        if (Sessao::getTrataExcecao()) {
            $transacao = Sessao::getTransacao();
            $objeto = pg_lo_open($transacao->getConnection() , $inOID, "w");

        } else {
            $objeto = pg_lo_open($this->getConnection() , $inOID, "w");

        }
        pg_lo_write($objeto, $stDados);
        pg_lo_close($objeto);

    }

    public function carregaBlob($inOID, $inTamanho, &$stDados)
    {

        if (Sessao::getTrataExcecao()) {
            $transacao = Sessao::getTransacao();
            $objeto = pg_lo_open($transacao->getConnection() , $inOID, "r");

        } else {
            $objeto = pg_lo_open($this->getConnection() , $inOID, "r");

        }
        $stDados = pg_lo_read($objeto, $inTamanho);
        pg_lo_close($objeto);

    }
    /**
     * Executa comando SQL no banco e retorna um RecordSet preenchido
     * @access Private
     * @param  Object  $rsRecordSet Objeto RecordSet preenchido
     * @param  String  $stSql
     * @param  Boolean $boTransacao
     * @return Object  Objeto Erro
     */

    public function executaSQL(&$rsRecordSet, $stSql, $boTransacao = "", $obConexao = null)
    {
        global $boPaginacao;
        global $GridPaging;
        // grid paging
        $stSql = $this->filtroEntidadeRh($stSql);
        if ($GridPaging) {
            return $this->___executaSQL($rsRecordSet, $stSql, $obConexao);

        }

        if (Sessao::getTrataExcecao()) {

            $this->_executaSQL($rsRecordSet, $stSql);

            return Sessao::getExcecao();

        } else {
            return $this->__executaSQL($rsRecordSet, $stSql, $boTransacao, $obConexao);

        }

    }

    public function _executaSQL(&$rsRecordSet, $stSql)
    {
        global $boPaginacao;
        $rsRecordSet = new RecordSet;

        $excecao = Sessao::getExcecao();
        $transacao = Sessao::getTransacao();

        if (!$excecao->ocorreu()) {
            if ($boPaginacao) {
                if (!Sessao::getNumeroLinhas()) {
                    $stSQLCount = "SELECT COUNT(*) as cont from (" . $stSql . ") as consulta";
                    $this->mostraConexao($transacao->getConnection(), $stSQLCount);
                    $inResult = pg_query($transacao->getConnection() , $stSQLCount);
                    Sessao::setNumeroLinhas(pg_fetch_result($inResult, 0, 'cont'));
                }
                global $inMaxLinhas;
                $inMaxLinhas = $inMaxLinhas ? $inMaxLinhas : 10;
                $inPg = $_GET['pg'] > 1 ? $_GET['pg']-1 : 0;
                $inOffSet = $inPg*$inMaxLinhas;
                $stSql = $stSql . ' LIMIT ' . $inMaxLinhas . ' OFFSET ' . $inOffSet;

            }
            $this->mostraConexao($transacao->getConnection(), $stSql);
            $inResult = pg_query($transacao->getConnection() , $stSql);
            $excecao->setDescricao(pg_last_error($transacao->getConnection()));

            if (!$excecao->ocorreu()) {
                if (pg_num_rows($inResult)) {
                    $rsRecordSet->preenche(pg_fetch_all($inResult));
                    /* Seta total de linhas da consulta sem paginação  */
                    $rsRecordSet->setTotalLinhas(Sessao::getNumeroLinhas());
                }
            } else {
                #if (constant('ENV_TYPE') == 'dev') {
                #    echo '<pre class="debug">';
                #    echo '<h7 class="debug">DebugSQL</h7>';
                #    echo $stSql;
                #    echo '</pre>';
                #}
            }
        }

        if ($excecao->ocorreu()) {
            $excecao->tratarErro();
        }

    }

    public function __executaSQL(&$rsRecordSet, $stSql, $boTransacao, $obConexao = null)
    {
        global $boPaginacao;
        $obErro = new Erro;
        $rsRecordSet = new RecordSet;
        $obErro = $this->abreConexao();

        if (is_null($obConexao)) {
            $obConexao = $this->getConnection();
        }

        if (!$obErro->ocorreu()) {

            if ($boPaginacao) {

                if (!Sessao::getNumeroLinhas()) {

                    $stSQLCount = "SELECT COUNT(*) as cont from (" . $stSql . ") as consulta";
                    $this->mostraConexao($obConexao, $stSQLCount);
                    $inResult = pg_query($obConexao , $stSQLCount);
                    Sessao::setNumeroLinhas(pg_fetch_result($inResult, 0, 'cont'));

                }
                global $inMaxLinhas;
                $inMaxLinhas = $inMaxLinhas ? $inMaxLinhas : 10;
                $inPg = $_GET['pg'] > 1 ? $_GET['pg']-1 : 0;
                $inOffSet = $inPg*$inMaxLinhas;
                $stSql = $stSql . ' LIMIT ' . $inMaxLinhas . ' OFFSET ' . $inOffSet;

            }
            $stSql = str_replace('\\\\', '\\', $stSql); //referente ao ticket #5234 ( onde eram colocados muitos escapes na sentença) e dava erro de SQL
            $this->mostraConexao($this->getConnection(), $stSql);
            $inResult = pg_query($this->getConnection() , $stSql);
            $obErro->setDescricao(pg_last_error($this->getConnection()));

            if (!$obErro->ocorreu()) {

                if (pg_num_rows($inResult)) {

                    $rsRecordSet->preenche(pg_fetch_all($inResult));
                    $rsRecordSet->setTotalLinhas(Sessao::getNumeroLinhas());

                }

            }

        }

        if (!$boTransacao) {
            $this->fechaConexao();

        }

        return $obErro;

    }
    /**
     * Função de Consulta Sql para Grid
     */

    public function ___executaSQL(&$rsRecordSet, $stSql, $obConexao = null)
    {
        global $GridPaging;
        $obErro = new Erro;
        $rsRecordSet = new RecordSet;
        $obErro = $this->abreConexao();

        if (is_null($obConexao)) {
            $obConexao = $this->getConnection();
        }

        if (!$obErro->ocorreu()) {

            /* conta total de registros da consulta */

            if (!Sessao::getNumeroLinhas()) {

                $stSQLCount = "SELECT COUNT(*) as cont from (" . $stSql . ") as consulta";
                $this->mostraConexao($this->getConnection(), $stSQLCount);
                $inResult = pg_query($this->getConnection() , $stSQLCount);
                Sessao::setNumeroLinhas(pg_fetch_result($inResult, 0, 'cont'));

            }
            /* retira ordenação */
            $stRegex = "/ORDER BY.*/i";
            $stSql = preg_replace($stRegex, '', $stSql);
            /* coloca ordenção determinada pelo componente */
            $stSql.= " order by " . $GridPaging['sort'] . " " . $GridPaging['dir'];
            /* adiciona limit, offset a consulta para fazer pagina  */
            $stSql = $stSql . ' LIMIT ' . $GridPaging['limit'] . ' OFFSET ' . $GridPaging['start'];
            /* executa consulta */
            $this->mostraConexao($this->getConnection(), $stSql);
            $inResult = pg_query($this->getConnection() , $stSql);
            /* seta erro, retorna null caso nao exista*/
            $obErro->setDescricao(pg_last_error($this->getConnection()));

            if (!$obErro->ocorreu()) {

                if (pg_num_rows($inResult)) {

                    /* preenche recordset */
                    $rsRecordSet->preenche(pg_fetch_all($inResult));
                    /* seta total de registros encontrados */
                    $rsRecordSet->setTotalLinhas(Sessao::getNumeroLinhas());

                }

            }
            $this->fechaConexao();

            return $obErro;

        }

    }

    public function filtroEntidadeRh($stSql)
    {
        $arEsquemasRH = Sessao::read("arSchemasRH");
        $esperadoAntesDoEsquema = '[\s,]';
        if (is_array($arEsquemasRH) and count($arEsquemasRH) >= 1) {
            $pattern = "/(".$esperadoAntesDoEsquema.join('|'.$esperadoAntesDoEsquema,$arEsquemasRH).")\./i";
            $replace = "$1".Sessao::getEntidade().".";
            $stSql = preg_replace($pattern,$replace,$stSql);
        }

        return $stSql;
    }

}
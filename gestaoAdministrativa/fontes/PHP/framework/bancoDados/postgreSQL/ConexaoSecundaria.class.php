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
 * Data de Criação: 04/06/2011
 * @author Desenvolvedor: Davi Ritter Aroldi
 * @package bancoDados
 * @subpackage postgreSQL
 *
 $Id: $
 Casos de uso: uc-01.01.00
 */
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/mapeamento/TAdministracaoUsuario.class.php';
include_once (CLA_OBJETO);

/**
 * Classe que faz a conexão secundária com uma base PostgreSql para verificação, interna, de usuários
 * @author Desenvolvedor: Davi Ritter Aroldi
 */

class ConexaoSecundaria extends Objeto
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
     * @var Integer
     * @access Private
     */
    public $stPassWordLogin;
    /**
     * @var String
     * @access Private
     */
    public $stUserLogin;
    /**
     * @var String
     * @access Private
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
        $this->inConnection = $valor;
    }
    /**
     * @access Public
     * @param String $valor
     */
    public function setPassWordLogin($valor)
    {
        $this->stPassWordLogin = sha1(trim($valor));
    }
    /**
     * @access Public
     * @param String $valor
     */
    public function setUserLogin($valor)
    {
        $this->stUserLogin = $valor;
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
        return $this->inConnection;
    }
    /**
     * @access Public
     * @return String
     */
    public function getPassWordLogin()
    {
        return $this->stPassWordLogin;
    }
    /**
     * @access Public
     * @return String
     */
    public function getUserLogin()
    {
        return $this->stUserLogin;
    }
    /**
     * Método Construtor
     * @access Private
     */
    public function ConexaoSecundaria($username = '', $password = '')
    {
        $this->setHost(BD_HOST);
        $this->setPort(BD_PORT);
        $this->setDbName(BD_NAME);

        if ($username) {
            $this->setUser($username);
        }
        if ($password) {
            $this->setPassWord($password);
        }
    }
    /**
     * Efetua abertura de conexão com o banco de dados
     * @access Private
     * @return Object Objeto Erro
     */
    public function abreConexao()
    {
        $obErro = new Erro;
        $stConnection = "";

        if ($this->getHost()) {
            $stConnection.= " host=".$this->getHost();
        }

        if ($this->getPort()) {
            $stConnection.= " port=".$this->getPort();
        }

        if ($this->getDbName()) {
            $stConnection.= " dbname=".$this->getDbName();
        }

        if ($this->getUser()) {
            $stConnection.= " user=".$this->getUser();
        }

        if ($this->getPassWord()) {
            $stConnection.= " password=".$this->getPassWord();
        }

        $inConnection = @pg_connect($stConnection);

        if (!$inConnection) {
            $obErro->setDescricao("Usuário ou senha inválido!");
        } else {
            $this->setConnection($inConnection);
        }

        return $obErro;

    }
    /**
     * Fecha conexão com o banco de dados
     * @access Private
     * @return Object Objeto Erro
     */
    public function verificaConexao()
    {
        $obErro = new Erro();
        $obTAdministracaoUsuario = new TAdministracaoUsuario();

        $stFiltro = " WHERE usuario.status = 'A'
                        AND usuario.username = '".$this->getUserLogin()."' ";
        $obErro = $obTAdministracaoUsuario->recuperaUsuario($rsUsuario, $stFiltro);

        if (!$obErro->ocorreu()) {
            if ($rsUsuario->getNumLinhas() != -1) {
                if ( trim($rsUsuario->getCampo("password")) != trim(crypt($this->getPassWordLogin(), $rsUsuario->getCampo("password"))) ) {
                    $obErro->setDescricao('Usuário não existe ou está inativo!');
                }
            } else {
                $obErro->setDescricao('Usuário não existe ou está inativo!');
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
        $inConnection = pg_close($this->getConnection());

        if (!$inConnection) {
            $obErro->setDescricao(pg_last_error($this->getConnection()));
        }

        return $obErro;

    }

}

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
 *
 * Data de Criação: 27/10/2005
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Documentor: Cassiano de Vasconcellos Ferreira
 * @package framework
 * @subpackage componentes
 */
if ( !defined('URBEM_ROOT_PATH')) {
    include '../../../../../../config.php';
}
include_once (URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/framework/URBEM/SessaoLegada.class.php');
include_once (URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php');

class Sessao extends SessaoLegada
{
    //Propriedade novas
    private $dtValidade;
    private $flVersao;
    private $stHistoricoVersao;
    private $stUsername;
    private $stPassword;
    private $inExercicio;
    private $arTituloPagina;
    private $stTituloPagina;
    private $inGestaoAtual;
    private $boTrataExcecao;
    private $obExcecao;
    private $obTransacao;
    private $boVoltarProtocolo;
    private $arRequestProtocolo;
    private $inNumeroLinhas = 0; //UTILIZADA NA PAGINAÇÃO
    private $stEntidade;
    private $boEntidade;
    private $inConexao;    

    const CACHE_LIMIT = 30;
    
    public static function setVersao($valor)
    {
        return Sessao::write('flVersao', $valor, true);

    }

    public static function setHistoricoVersao($valor)
    {
        return Sessao::write('stHistoricoVersao', $valor, true);

    }

    public static function setUsuarioSessao($valor)
    {
        return Sessao::write('flVersao', $valor, true);

    }

    public static function setUsername($valor)
    {
        return Sessao::write('stUsername', $valor, true);

    }

    public static function setPassword($valor)
    {
        return Sessao::write('stPassword', sha1(trim($valor)), true);

    }

    public static function setExercicio($valor)
    {
        return Sessao::write('exercicio', $valor, true);

    }

    public static function setTituloPagina($valor)
    {
        return Sessao::write('stTituloPagina', $valor, true);
    }

    public static function setEntidade($valor)
    {
        Sessao::write('stEntidade', $valor, true);
        Sessao::write('boEntidade', true, true);

        return true;

    }

    public static function setConexao($valor)
    {
        Sessao::write('inConexao', $valor, true);

        return true;
    }

    public static function setTrataExcecao($valor)
    {
        Sessao::write('boTrataExcecao', $valor);

        if ($valor == true) {
            include_once CLA_TRANSACAO;

            $obTransacao = new Transacao;
            $obTransacao->abreTransacao($obFlagTransacao, $boTransacao);

            Sessao::setTransacao($obTransacao);
            Sessao::setExcecao(new Excecao());
        }

    }

    public static function setExcecao($valor)
    {
        $GLOBALS['excecao'] = $valor;
    }

    public static function setTransacao($valor)
    {
        $GLOBALS['transacao'] = $valor;
    }

    public static function setVoltarProtocolo($valor)
    {
        Sessao::write('boVoltarProtocolo', $valor, true);
    }

    public static function setRequestProtocolo($valor)
    {
        Sessao::write('arRequestProtocolo', $valor, true);
    }

    public static function setNumeroLinhas($valor)
    {
        Sessao::write('inNumeroLinhas', $valor, true);
    }

    public static function setId($valor)
    {
        Sessao::write('sessao_id', $valor, true);
    }

    public static function getVersao()
    {
        return Sessao::read('flVersao');

    }

    public static function getHistoricoVersao()
    {
        return Sessao::read('stHistoricoVersao');

    }

    public static function getUsuarioSessao()
    {
        return Sessao::read('obUsuarioSessao');

    }

    public static function getUsername()
    {
        return Sessao::read('stUsername');

    }

    public static function getPassword()
    {
        return Sessao::read('stPassword');

    }

    public static function getExercicio()
    {
        return Sessao::read('exercicio');

    }

    public static function getTituloPagina()
    {
        return Sessao::read('stTituloPagina');

    }

    public static function getEntidade()
    {
        if (Sessao::read('stEntidade') == "") {
            return Sessao::read('stEntidade');

        } else {
            return "_" . Sessao::read('stEntidade');

        }

    }
    public static function getBoEntidade()
    {
        return Sessao::read('boEntidade');
    }

    public static function getConexao()
    {
        return Sessao::read('inConexao');
    }

    public static function getCodEntidade($boTransacao="")
    {
        if (Sessao::read('stEntidade')== "") {
            return SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::read('exercicio'),$boTransacao);

        } else {
            return Sessao::read('stEntidade');

        }
    }

    public static function getTrataExcecao()
    {
        return Sessao::read('boTrataExcecao');
    }

    public static function getExcecao()
    {
        return $GLOBALS['excecao'];

    }

    public static function getTransacao()
    {
        return $GLOBALS['transacao'];

    }

    public static function getVoltarProtocolo()
    {
        return Sessao::read('boVoltarProtocolo');

    }

    public static function getRequestProtocolo()
    {
        return Sessao::read('arRequestProtocolo');

    }

    public static function getNumeroLinhas()
    {
        return Sessao::read('inNumeroLinhas');

    }

    public static function getId()
    {
        return Sessao::read('sessao_id');

    }
    /**
     * Retorna Módulo Corrente no Urbem
     *
     * @return int Codigo do Modulo Atual
     */
    public static function getModulo()
    {
        return Sessao::read('modulo');

    }

    public static function verificarSistemaAtivo($boTransacao = "")
    {
        include_once (CAM_GA_ADM_MAPEAMENTO . "TAdministracaoConfiguracao.class.php");
        $obTConfiguracao = new TAdministracaoConfiguracao;
        $obTConfiguracao->setDado("cod_modulo", 2);
        $obTConfiguracao->setDado("exercicio", Sessao::getExercicio());
        $obErro = $obTConfiguracao->pegaConfiguracao($stValor, "status", $boTransacao);

        if (!$obErro->ocorreu()) {

            if ($stValor == 'I' and Sessao::read('numCgm') != 0) {

                $obErro->setDescricao("Sistema Inativo!");

            }

        }

        return $obErro;

    }

    public static function consultarDadosSessao($boTransacao = '')
    {
        include_once (CAM_GA_ADM_MAPEAMENTO . "TAdministracaoUsuario.class.php");
        $obTUsuario = new TUsuario;
        $stFiltro = " AND usuario.username = '" . Sessao::getUsername() . "'";
        $obErro = $obTUsuario->recuperaRelacionamento($rsUsuarioSessao, $stFiltro, '', $boTransacao);

        if (!$obErro->ocorreu()) {

            if (!$rsUsuarioSessao->eof()) {

                  if ( trim($rsUsuarioSessao->getCampo("password")) == trim(crypt(Sessao::getPassword(), $rsUsuarioSessao->getCampo("password"))) ) {

                      if($rsUsuarioSessao->getCampo("status") != 'I'){

                          Sessao::write('numCgm',$rsUsuarioSessao->getCampo("numcgm"), true);
                          Sessao::write('nomCgm',$rsUsuarioSessao->getCampo("nom_cgm"), true);
                          Sessao::write('nomSetor',$rsUsuarioSessao->getCampo("nom_setor"), true);
                          Sessao::write('orgao',$rsUsuarioSessao->getCampo("orgao"), true);
                          Sessao::write('codOrgao',$rsUsuarioSessao->getCampo("cod_orgao"), true);
                          Sessao::write('username',Sessao::getUsername(), true);

                      } else {
                        
                          $obErro->setDescricao('Erro: Usuário inativo!');
                        
                      }

                  } else {

                      $obErro->setDescricao('Erro: Usuário ou senha inválidos!');

                  }

            } else {

                $obErro->setDescricao('Erro: Usuário não existe!');

            }

        }

        return $obErro;

    }

    public static function montaTituloPagina($inNivel, $stTitulo)
    {
        $arTituloPagina = explode('::',Sessao::getTituloPagina());
        $stTituloPagina = "";

        if ($inNivel) {
            if (!empty($stTitulo)) {
                if ($inNivel == 1) {
                    $stTitulo = "Gestão " . $stTitulo;
                    $arTituloPagina = array_slice($arTituloPagina,1); // remove 'URBEM'

                }

                $arTituloPagina[$inNivel] = $stTitulo;
            }

            $inIndice = 0;
            ksort($arTituloPagina);

            foreach ($arTituloPagina as $stTituloPag) {

                $stTituloPagina.= $stTituloPag;

                if (($inIndice+1) == $inNivel) {

                    break;

                }

                $stTituloPagina.= " :: ";
                $inIndice++;

            }

        } else {

            $stTituloPagina = 'URBEM';

        }

        Sessao::setTituloPagina($stTituloPagina);

    }

    public static function encerraExcecao()
    {
        Sessao::getTransacao()->encerraTransacao();
        Sessao::setTrataExcecao(false);

    }

    /**
     * Abre sessao
     *
     * @return true
    */
    public static function open($nomeSessao = false)
    {
        if (!Sessao::started()) {
            
            if ($nomeSessao) {
                session_name($nomeSessao);
            }
            
            session_cache_expire(Sessao::CACHE_LIMIT);
            
            session_start();
            
            return true;
        
        }else{
            //Caso a sessao tenho expirado retornar para a tela de login
            if ( Sessao::expiredSession() ) {                
                Sessao::close();                
                echo("<script> parent.window.location.href='".URBEM_ROOT_URL."/index.php?action=sair';</script>");                
                return false;
            }else{
                return true;
            }
        }
        
    }


    /**
     * fecha sessao e destroi todos os dados
     *
     * @return boolean
     * @deprecated
     */

    public static function close()
    {

        if (Sessao::started()) {
            Sessao::clean(true);
            session_unset();

            return session_destroy();

        }

        return true;

    }
    /** Retorna true se existe uma sessao iniciada
     *
     * @return boolean
     */

    public static function started()
    {

        if (isset($_SESSION)) {
            return true;
        } else {
            return false;
        }

    }
    /** Retorna true se existe uma sessao que ja foi expirada
     *
     * @return boolean
     */
    public static function expiredSession()
    {
        if (!isset($_SESSION['CREATED'])) {
            $_SESSION['CREATED'] = time();
            return false;
        }else{
            $inTime = 60 * Sessao::CACHE_LIMIT;
            if ( (time() - $_SESSION['CREATED']) > $inTime) {
                return true;
            }
        }  
    }


    /**
     *  Read data from current session
     *
     *
     * @param string A unique key identifying your data
     * @return mixed Data associated with your key
     *
     */

    public static function read($key, $retval=null)
    {
        if (isset($_SESSION[$key])) {
            if (is_object($_SESSION[$key]) && $_SESSION[$key] instanceof Auditoria) {
                return $_SESSION[$key];
            } else {
                return unserialize($_SESSION[$key]);
            }
        } else {
            if (is_null($retval)) {
                return null;
            } else {
                return $retval;
            }
        }
    }
    /**
     * Writes data to session
     *
     * @param $key string A unique key identifying your data
     * @param $data mixed  Data associated with your key
     * @param $persist boolean  Define if data will be erased on switch to another action by menu
     *
     */

    public static function write($key, $data , $persist = false)
    {
        if (is_object($data) && $data instanceof Auditoria) {
            $_SESSION[$key] = $data;
        } else {
            $_SESSION[$key] = serialize($data);
        }

        # write metadata if persist it's true
        if ($persist) {
            $metadata = (array) Sessao::getPersistMetaData();
            if (!in_array($key,$metadata)) {
                $metadata[] = $key;
                Sessao::write('sessao_urbem_persist_metadata', $metadata, true);
            }
        }

        return $data;
    }
    /**
     * Remove $key from session
     *
     * @param string A unique key identifying your data
     * @param mixed  Data associated with your key
     *
     */

    public static function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * return session meta data
     *
     * @param $key boolean the key to check
     */
    public static function getPersistMetaData()
    {
        return Sessao::read('sessao_urbem_persist_metadata');
    }

    /**
     * Return a boolean that indicate if passed key is persist in environment
     *
     * @param $key boolean the key to check
     */
    public static function isPersist($key)
    {
        return in_array($key,(array) Sessao::getPersistMetaData());
    }

    /**
     * Clean Session data
     *
     * @param $force boolean Force clean of all data instead just user defined
     */
    public static function clean($force = false)
    {
        $session = $_SESSION;

        foreach ($session as $key => $data) {
            if ($force || !Sessao::isPersist($key) ) {
                Sessao::remove($key);
            }
        }
    }

    public static function getMensagens()
    {
        return Sessao::read('mensagens_');
    }

    public static function setMensagens($value)
    {
        Sessao::write('mensagens_',$value, true);
    }

}

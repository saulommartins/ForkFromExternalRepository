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
* Classe estensão da Conexão para conectar em outro bancos que não o do urbem
* Data de Criação: 04/03/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Diego Barbosa Victoria

* @package bancoDados
* @subpackage PostgreSQL

Casos de uso: uc-01.01.00

*/

class ConexaoSIAM extends Conexao
{
var $obTAdministracaoConfiguracao;

function ConexaoSIAM($stHost = "", $stPort = "", $stDbName = "", $stUser = "", $stPassWord = "")
{
    parent::Conexao();
    if ($stHost) {
        $this->setHost     ( $stHost );
    }
    if ($stPort) {
        $this->setPort     ( $stPort );
    }
    if ($stDbName) {
        $this->setDbName   ( $stDbName );
    }
    if ($stUser) {
        $this->setUser     ( $stUser );
    }
    if ($stPassWord) {
        $this->setPassWord ( $stPassWord );
    }
}

function buscaParametros($boTransacao = "")
{
    parent::Conexao();
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    $this->obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
    $arParametros = array( "samlink_port" => "",
                           "samlink_user" => "",
                           "samlink_password" => "",
                           "samlink_host" => "",
                           "samlink_dbname" => "");
    $this->obTAdministracaoConfiguracao->setDado( "cod_modulo" , 2 );
    $this->obTAdministracaoConfiguracao->setDado( "exercicio", Sessao::getExercicio() );
    foreach ($arParametros as $stParametro => $stValor) {
        $obErro = $this->obTAdministracaoConfiguracao->pegaConfiguracao( $arParametros[$stParametro], $stParametro, $boTransacao );
        if ( $obErro->ocorreu() ) {
            break;
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->setHost     ( $arParametros["samlink_host"]     );
        $this->setPort     ( $arParametros["samlink_port"]     );
        $this->setDbName   ( $arParametros["samlink_dbname"]   );
        $this->setUser     ( $arParametros["samlink_user"]     );
        $this->setPassWord ( $arParametros["samlink_password"] );
    }

    return $obErro;
}

}

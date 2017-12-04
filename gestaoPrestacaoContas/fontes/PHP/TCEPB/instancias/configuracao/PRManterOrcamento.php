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
    * PÃ¡gina de Processamento
    * Data de CriaÃ§Ã£o   : 25/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * Casos de uso: uc-06.03.00

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TADM."TAdministracaoConfiguracaoEntidade.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrcamento";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$obMapeamento = new TAdministracaoConfiguracaoEntidade();
Sessao::getTransacao()->setMapeamento( $obMapeamento );
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($_REQUEST['stAcao']) {
  default:
    $obMapeamento->setDado('exercicio',Sessao::getExercicio() );
    $obMapeamento->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
    $obMapeamento->setDado('cod_modulo',Sessao::read('modulo'));
    $obMapeamento->setDado('parametro','data_aprovacao_loa');
    $obMapeamento->setDado('valor',implode('-',array_reverse(explode('/',$_REQUEST['dtAprovacaoLOA']))));
    $obMapeamento->recuperaPorChave( $rsConfiguracao );
    if ( $rsConfiguracao->getNumLinhas() > 0 ) {
        $obMapeamento->alteracao();
    } else {
        $obMapeamento->inclusao();
    }
    $obMapeamento->setDado('parametro','numero_loa');
    $obMapeamento->setDado('valor', $_REQUEST['numLeiOrcamentaria']);
    $obMapeamento->recuperaPorChave( $rsConfiguracao );
    if ( $rsConfiguracao->getNumLinhas() > 0 ) {
        $obMapeamento->alteracao();
    } else {
        $obMapeamento->inclusao();
    }

    $obMapeamento->setDado('parametro','data_aprovacao_ldo');
    $obMapeamento->setDado('valor', implode('-',array_reverse(explode('/',$_REQUEST['dtAprovacaoLDO']))));
    $obMapeamento->recuperaPorChave( $rsConfiguracao );
    if ( $rsConfiguracao->getNumLinhas() > 0 ) {
        $obMapeamento->alteracao();
    } else {
        $obMapeamento->inclusao();
    }

    $obMapeamento->setDado('parametro','numero_ldo');
    $obMapeamento->setDado('valor', $_REQUEST['numLDO']);
    $obMapeamento->recuperaPorChave( $rsConfiguracao );
    if ( $rsConfiguracao->getNumLinhas() > 0 ) {
        $obMapeamento->alteracao();
    } else {
        $obMapeamento->inclusao();
    }

    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
  break;
}

Sessao::encerraExcecao();
?>

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
    * Data de CriaÃ§Ã£o   : 26/04/2008

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 22245 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-04-27 15:31:00 -0300 (Sex, 27 Abr 2007) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTPB."TTPBEmpenhoObras.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterEmpenhoObras";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$obMapeamento = new TTPBEmpenhoObras();
Sessao::getTransacao()->setMapeamento( $obMapeamento );
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($_REQUEST['stAcao']) {
  case 'excluir':

    $obMapeamento->setDado('exercicio_empenho'  ,$_REQUEST['inExercicioEmpenho']);
    $obMapeamento->setDado('cod_entidade'       ,$_REQUEST['inCodEntidadeEmpenho']);
    $obMapeamento->setDado('cod_empenho'        ,$_REQUEST['inCodEmpenho']);
    $obMapeamento->setDado('exercicio_obras'    ,$_REQUEST['inExercicioObra']);
    $obMapeamento->setDado('num_obra'           ,$_REQUEST['inCodObra']);
    $obMapeamento->exclusao();

    $stMensagem = "Empenho ".$_REQUEST['inCodEmpenho']."/".$_REQUEST['inExercicioEmpenho'];
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao",$stMensagem ,"excluir","aviso", Sessao::getId(), "../");

  break;

  case 'incluir':

    $arEmpenho = explode("/",$_REQUEST['inCodEmpenho']);

    if ($arEmpenho[0] != "") {

        $obMapeamento->setDado('exercicio_empenho'  ,($arEmpenho[1]?$arEmpenho[1]:Sessao::getExercicio()) );
        $obMapeamento->setDado('cod_entidade'       ,$_REQUEST['inCodEntidadeEmpenho']);
        $obMapeamento->setDado('cod_empenho'        ,$arEmpenho[0]);
        $obMapeamento->setDado('exercicio_obras'    ,$_REQUEST['inExercicioObra']);
        $obMapeamento->setDado('num_obra'           ,$_REQUEST['inCodObra']);
        $obMapeamento->recuperaPorChave($rsRecordSet);
        if ($rsRecordSet->eof()) {
            $obMapeamento->inclusao();
        } else {
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Empenho ".$_REQUEST['inCodEmpenho']." da Entidade ".$_REQUEST['inCodEntidadeEmpenho']." já possui obra cadastrada.","erro","erro_n", Sessao::getId(), "../");
            break;
        }
    } else {
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Empenho (".$_REQUEST['inCodEmpenho'].") da Entidade inválido","erro","erro_n", Sessao::getId(), "../");
    }

    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Empenho ".$_REQUEST['inCodEmpenho'],"incluir","incluir_n", Sessao::getId(), "../");
  break;
}

Sessao::encerraExcecao();
?>

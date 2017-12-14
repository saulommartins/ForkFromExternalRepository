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
    * Data de Criação: 04/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 28251 $
    $Name$
    $Author: luiz $
    $Date: 2008-02-27 13:43:36 -0300 (Qua, 27 Fev 2008) $

    * Casos de uso: uc-03.01.07
*/

/*
$Log$
Revision 1.1  2007/10/17 13:42:13  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioManutencao.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php");

$stPrograma = "ManterAgendarManutencao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTPatrimonioManutencao = new TPatrimonioManutencao();
Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioManutencao );

//recupera o registro do bem para verificacoes
$obTPatrimonioBem = new TPatrimonioBem();
$obTPatrimonioBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );

switch ($stAcao) {
    case 'incluir' :
        $obTPatrimonioBem->recuperaPorChave( $rsBem );

        if (  implode('',array_reverse(explode('/',$rsBem->getCampo( 'dt_aquisicao' )))) > implode('',array_reverse(explode('/',$_REQUEST['dtAgendamento']))) ) {
            $stMensagem = 'A data de agendamento deve ser posterior a data de aquisição do bem';
        }

        //verifica se nao existe um registro na base
        $obTPatrimonioManutencao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
        $obTPatrimonioManutencao->setDado( 'dt_agendamento', $_REQUEST['dtAgendamento'] );
        $obTPatrimonioManutencao->recuperaPorChave( $rsManutencao );
        if ( $rsManutencao->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe uma manutenção agendada para este bem nesta data';
        }

        if (!$stMensagem) {
            $obTPatrimonioManutencao->setDado( 'numcgm', $_REQUEST['inNumCGM'] );
            $obTPatrimonioManutencao->setDado( 'observacao', $_REQUEST['stObservacao'] );
            $obTPatrimonioManutencao->inclusao();
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&pos=".$_REQUEST['pos']."&pg=".$_REQUEST['pg'],"Bem - ".$_REQUEST['inCodBem'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;
    case 'alterar' :
        //verifica se a data de agendamento e superior a data de aquisicao
        $obTPatrimonioBem->recuperaPorChave( $rsBem );
        if ( str_replace('-','',$rsBem->getCampo( 'dt_aquisicao' )) > implode('',array_reverse(explode('/',$_REQUEST['dtAgendamento']))) ) {
            $stMensagem = 'A data de agendamento deve ser posterior a data de aquisição do bem';
        }

        //verifica se a nova data de agendamento é igual a original
        if ( implode('',array_reverse(explode('/',$_REQUEST['dtAgendamentoOriginal']))) != implode('',array_reverse(explode('/',$_REQUEST['dtAgendamento']))) ) {
            $obTPatrimonioManutencao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
            $obTPatrimonioManutencao->setDado( 'dt_agendamento', $_REQUEST['dtAgendamento'] );
            $obTPatrimonioManutencao->recuperaPorChave( $rsManutencao );
            if ( $rsManutencao->getNumLinhas() > 0 ) {
                $stMensagem = 'Já existe uma manutenção agendada para esta data';
            }
        }

        //se nao tiver erros, altera a base
        if (!$stMensagem) {
            $obTPatrimonioManutencao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
            $obTPatrimonioManutencao->setDado( 'numcgm', $_REQUEST['inNumCGM'] );
            $obTPatrimonioManutencao->setDado( 'observacao', $_REQUEST['stObservacao'] );
            if (!$rsManutencao) {
                $obTPatrimonioManutencao->setDado( 'dt_agendamento', $_REQUEST['dtAgendamento'] );
                $obTPatrimonioManutencao->alteracao();
            } else {
                //exclui o registro anterior
                $obTPatrimonioManutencao->setDado( 'dt_agendamento', $_REQUEST['dtAgendamentoOriginal'] );
                $obTPatrimonioManutencao->exclusao();
                //inclui um novo na base
                $obTPatrimonioManutencao->setDado( 'dt_agendamento', $_REQUEST['dtAgendamento'] );
                $obTPatrimonioManutencao->inclusao();

            }
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Bem - ".$_REQUEST['inCodBem'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_alterar","erro");

        }

        break;

}
Sessao::encerraExcecao();

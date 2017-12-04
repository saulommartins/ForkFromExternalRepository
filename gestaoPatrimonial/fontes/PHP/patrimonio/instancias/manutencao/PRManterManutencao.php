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

    $Revision: 26391 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-25 18:22:46 -0200 (Qui, 25 Out 2007) $

    * Casos de uso: uc-03.01.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioManutencao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioManutencaoPaga.class.php";

$stPrograma = "ManterManutencao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao");

$obTPatrimonioManutencao = new TPatrimonioManutencao();
$obTPatrimonioManutencaoPaga = new TPatrimonioManutencaoPaga();
Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioManutencao );

switch ($stAcao) {
    case 'incluir' :

        //verifica se a data de realizacao e igual ou superior a data de agendamento
        if ( implode('',array_reverse(explode('/',$_REQUEST['dtAgendamento']))) > implode('',array_reverse(explode('/',$_REQUEST['dtRealizacao']))) ) {
            $stMensagem = 'A data de realização deve ser igual ou superior a data de agendamento';
        }
        //verifica se a data de garantia e igual ou superior a data de realizacao
        if ( $_REQUEST['dtGarantia'] != '' AND implode('',array_reverse(explode('/',$_REQUEST['dtRealizacao']))) > implode('',array_reverse(explode('/',$_REQUEST['dtGarantia']))) ) {
            $stMensagem = 'A data de garantia deve ser igual ou superior a data de realização';
        }

        if (!$stMensagem) {
            //recupera os dados da table
            $obTPatrimonioManutencao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
            $obTPatrimonioManutencao->setDado( 'dt_agendamento', $_REQUEST['dtAgendamento'] );
            $obTPatrimonioManutencao->recuperaPorChave( $rsManutencao );

            //faz update na table patrimonio.manutencao
            $obTPatrimonioManutencao->setDado( 'numcgm', $rsManutencao->getCampo('numcgm') );
            $obTPatrimonioManutencao->setDado( 'observacao', $rsManutencao->getCampo('observacao') );
            $obTPatrimonioManutencao->setDado( 'dt_realizacao', $_REQUEST['dtRealizacao'] );
            if ($_REQUEST['dtGarantia']) {
                $obTPatrimonioManutencao->setDado( 'dt_garantia', $_REQUEST['dtGarantia'] );
            }
            $obTPatrimonioManutencao->alteracao();

            # Formata para inserir na base de dados.
            $vlManutencao = str_replace(',', '.', str_replace('.', '', $_REQUEST['vlManutencao']));

            //insere dados na table patrimonio.manutencao_paga
            $obTPatrimonioManutencaoPaga->setDado( 'cod_bem', $rsManutencao->getCampo('cod_bem') );
            $obTPatrimonioManutencaoPaga->setDado( 'dt_agendamento', $rsManutencao->getCampo('dt_agendamento') );
            $obTPatrimonioManutencaoPaga->setDado( 'cod_empenho', $_REQUEST['inCodEmpenho'] );
            $obTPatrimonioManutencaoPaga->setDado( 'exercicio', $_REQUEST['stExercicio'] );
            $obTPatrimonioManutencaoPaga->setDado( 'cod_entidade', $_REQUEST['inCodEntidade'] );
            $obTPatrimonioManutencaoPaga->setDado( 'valor',  $vlManutencao);
            $obTPatrimonioManutencaoPaga->inclusao();

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Bem - ".$_REQUEST['inCodBem'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");

        }

        break;
    case 'excluir' :
        $obTPatrimonioManutencaoPaga->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
        $obTPatrimonioManutencaoPaga->setDado( 'dt_agendamento', $_REQUEST['dtAgendamento'] );
        $obTPatrimonioManutencaoPaga->exclusao();

        $obTPatrimonioManutencao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
        $obTPatrimonioManutencao->setDado( 'dt_agendamento', $_REQUEST['dtAgendamento'] );
        $obTPatrimonioManutencao->recuperaPorChave( $rsManutencao );

        $obTPatrimonioManutencao->exclusao();

        $obTPatrimonioManutencao->setDado( 'numcgm', $rsManutencao->getCampo('numcgm') );
        $obTPatrimonioManutencao->setDado( 'observacao', $rsManutencao->getCampo('observacao') );
        $obTPatrimonioManutencao->inclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Bem - ".$_REQUEST['inCodBem'],"excluir","aviso", Sessao::getId(), "../");

        break;
}
Sessao::encerraExcecao();

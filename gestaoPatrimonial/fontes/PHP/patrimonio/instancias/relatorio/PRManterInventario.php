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
 * Página de Processamento
 * Data de Criação: 18/01/2009

 * @author Analista:      Gelson Wolowski
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 * @ignore

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioHistoricoBem.class.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioInventario.class.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioInventarioHistoricoBem.class.php';

# Define o nome dos arquivos PHP
$stPrograma = "ManterInventario";
$pgFilt     = "FL".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgList     = "LS".$stPrograma.".php";

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento($obTPatrimonioInventario);

switch ($stAcao) {

    case "incluir":
    case "alterar":

        $inIdInventario    = $_REQUEST['inIdInventario'];
        $stExercicio       = $_REQUEST['stExercicio'];
        $boImprimeAbertura = $_REQUEST['boImprimeAbertura'];
        $stDataInicial     = $_REQUEST['stDataInicial'];
        $stObservacao      = $_REQUEST['stObservacao'];

        if (is_numeric($inIdInventario) && !empty($stExercicio)) {
            $obTPatrimonioInventario = new TPatrimonioInventario;
            $obTPatrimonioInventario->setDado('id_inventario' , $inIdInventario);
            $obTPatrimonioInventario->setDado('exercicio'     , $stExercicio);
            $obTPatrimonioInventario->setDado('dt_inicio'     , $stDataInicial);
            $obTPatrimonioInventario->setDado('observacao'    , $stObservacao);
            $obTPatrimonioInventario->setDado('numcgm'        , Sessao::read('numCgm'));
            $obTPatrimonioInventario->alteracao();

            $pgDest = $pgFilt;

            if ($stAcao == 'incluir') {
                $pgDest = "OCGeraAberturaEncerramentoInventario.php?inIdInventario=".$inIdInventario."&stExercicio=".$stExercicio;
            }

            SistemaLegado::alertaAviso($pgDest.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], $inIdInventario."/".$stExercicio."",$_REQUEST['stAcao'],"aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso('Erro ao incluir o Inventário.','form','erro',Sessao::getId() );
        }

    break;

    case "excluir":

        $inIdInventario = $_REQUEST['inIdInventario'];
        $stExercicio    = $_REQUEST['stExercicio'];

        if (is_numeric($inIdInventario) && !empty($stExercicio)) {

            $obTPatrimonioInventarioHistoricoBem = new TPatrimonioInventarioHistoricoBem;
            $obTPatrimonioInventarioHistoricoBem->setDado('id_inventario' , $inIdInventario);
            $obTPatrimonioInventarioHistoricoBem->setDado('exercicio'     , $stExercicio);
            $obErro = $obTPatrimonioInventarioHistoricoBem->exclusao();

            $obTPatrimonioInventario = new TPatrimonioInventario;
            $obTPatrimonioInventario->setDado('id_inventario' , $inIdInventario);
            $obTPatrimonioInventario->setDado('exercicio'     , $stExercicio);
            $obErro = $obTPatrimonioInventario->exclusao();
        }

        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], $inIdInventario."/".$stExercicio."",$_REQUEST['stAcao'],"aviso", Sessao::getId(), "../");

    break;

    # Processamento do Inventário.
    case "processar":

        $inIdInventario = $_REQUEST['inIdInventario'];
        $stExercicio    = $_REQUEST['stExercicio'];

        if (is_numeric($inIdInventario) && !empty($stExercicio)) {

            # Efetiva o processamento do Inventário do Patrimônio.
            $obTPatrimonioInventario = new TPatrimonioInventario;
            $obTPatrimonioInventario->setDado('id_inventario' , $inIdInventario);
            $obTPatrimonioInventario->setDado('exercicio'     , $stExercicio);
            $obTPatrimonioInventario->recuperaInventarioProcessamento($rsRecordSet);

            # Encerra o Inventário do Patrimônio.
            $obTPatrimonioInventario->setDado('id_inventario' , $inIdInventario);
            $obTPatrimonioInventario->setDado('exercicio'     , $stExercicio);
            $obTPatrimonioInventario->setDado('dt_inicio'     , $_REQUEST['stDataInicial']);
            $obTPatrimonioInventario->setDado('dt_fim'        , date('d/m/Y'));
            $obTPatrimonioInventario->setDado('observacao'    , $_REQUEST['stObservacao']);
            $obTPatrimonioInventario->setDado('numcgm'        , Sessao::read('numCgm'));
            $obTPatrimonioInventario->setDado('processado'    , true);
            $obTPatrimonioInventario->alteracao();

            $stMsg = "Processamento do Inventário concluído com Sucesso! (".$inIdInventario."/".$stExercicio.")";

            SistemaLegado::LiberaFrames(true, true);
            SistemaLegado::alertaAviso($pgFilt.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], $stMsg."",$_REQUEST['stAcao'],"aviso", Sessao::getId(), "../");
        }

    break;
}

Sessao::encerraExcecao();

?>

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
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaInfracao.class.php' );

$stPrograma = "ManterInfracao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$dtInfracao   = explode('/', $_REQUEST['dtInfracao']);
$inDtInfracao = $dtInfracao[2].$dtInfracao[1].$dtInfracao[0];

if ($inDtInfracao > date('Ymd')) {
    SistemaLegado::exibeAviso(urlencode('A data da infração não pode ser maior que a data atual'),"n_incluir","erro");
    echo "<script>LiberaFrames(true,true);</script>";
    die;
}

switch ($stAcao) {

    case 'incluir':
        $obTFrotaInfracao = new TFrotaInfracao();
        $obTFrotaInfracao->proximoCod($id);
        $obTFrotaInfracao->setDado( 'id'  , $id );
        $obTFrotaInfracao->setDado( 'cod_veiculo'  , $_REQUEST['inCodVeiculo'] );
        $obTFrotaInfracao->setDado( 'cgm_motorista', $_REQUEST['inCodMotorista'] );
        $obTFrotaInfracao->setDado( 'auto_infracao', $_REQUEST['stAutoInfracao'] );
        $obTFrotaInfracao->setDado( 'data_infracao', $_REQUEST['dtInfracao'] );
        $obTFrotaInfracao->setDado( 'cod_infracao' , $_REQUEST['inCodInfracao'] );
        $obTFrotaInfracao->inclusao();

        sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Infração inserida com sucesso',"incluir","incluir", Sessao::getId(), "../");

        echo "<script>LiberaFrames(true,true);</script>";
    break;

    case 'alterar':
        $obTFrotaInfracao = new TFrotaInfracao();
        $obTFrotaInfracao->setDado( 'id'           , $_REQUEST['inId'] );
        $obTFrotaInfracao->setDado( 'cod_veiculo'  , $_REQUEST['inCodVeiculo'] );
        $obTFrotaInfracao->setDado( 'cgm_motorista', $_REQUEST['inCodMotorista'] );
        $obTFrotaInfracao->setDado( 'auto_infracao', $_REQUEST['stAutoInfracao'] );
        $obTFrotaInfracao->setDado( 'data_infracao', $_REQUEST['dtInfracao'] );
        $obTFrotaInfracao->setDado( 'cod_infracao' , $_REQUEST['inCodInfracao'] );
        $obTFrotaInfracao->alteracao();

        sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Infração alterada com sucesso',"alterar","alterar", Sessao::getId(), "../");

        echo "<script>LiberaFrames(true,true);</script>";
    break;

    case 'excluir':
        $obTFrotaInfracao = new TFrotaInfracao();
        $obTFrotaInfracao->setDado( 'id', $_REQUEST['id'] );
        $obTFrotaInfracao->exclusao();

        sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Infração excluida com sucesso',"excluir","excluir", Sessao::getId(), "../");

        echo "<script>LiberaFrames(true,true);</script>";
    break;
}

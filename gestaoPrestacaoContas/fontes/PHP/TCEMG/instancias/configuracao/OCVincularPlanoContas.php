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
/*
    * Página do Oculto de Vinculo do Plano de Contas ao TCE-MG
    * Data de Criação : 13/07/2016

    * @author: Michel Teixeira

    * @ignore
    * $Id: OCVincularPlanoContas.php 66067 2016-07-14 17:27:32Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_COMPONENTES.'/Table/Table.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGPlanoContas.class.php';

$stPrograma = "VincularPlanoContas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');
$stAcao = $request->get('stAcao');

switch( $stCtrl ){
    case "carregaGrupoContas":
        $stJs = "";
        $stHTML = "";

        $inCodUF       = $request->get('inCodUF');
        $inCodPlano    = $request->get('inCodPlano');
        $stExercicio   = $request->get('stExercicio');
        $inCodGrupo    = $request->get('inCodGrupo');
		$inCodEntidade = $request->get('inCodEntidade');

        if($stExercicio > Sessao::getExercicio()){
            $stJs .= "jQuery('#stExercicio').val(''); \n";
            $stJs .= "alertaAviso('O Exercício não pode ser maior que ".Sessao::getExercicio()."!','form','erro','".Sessao::getId()."'); \n";
        }elseif(!empty($inCodUF) && !empty($inCodPlano) && !empty($stExercicio) && !empty($inCodGrupo) &&!empty($inCodEntidade)){
            $obTTCEMGPlanoContas = new TTCEMGPlanoContas;
            $obTTCEMGPlanoContas->setDado('exercicio'    , $stExercicio);
			$obTTCEMGPlanoContas->setDado('cod_entidade' , $inCodEntidade);
            $obTTCEMGPlanoContas->setDado('cod_uf'       , $inCodUF);
            $obTTCEMGPlanoContas->setDado('cod_plano'    , $inCodPlano);
            $obTTCEMGPlanoContas->setDado('cod_grupo'    , $inCodGrupo);
            $obTTCEMGPlanoContas->recuperaTodos($rsEstrutural, $stFiltro, "ORDER BY tabela.cod_estrutural");

            if($rsEstrutural->getNumLinhas() > 0){
                if($inCodGrupo==4)
                    $stFiltro = "AND ( codigo_estrutural LIKE '3.5.2%' OR codigo_estrutural LIKE '4.%' )";
                else
                    $stFiltro = "AND codigo_estrutural LIKE '".$inCodGrupo.".%'";

                $obTTCEMGPlanoContas->recuperaPlanoContaEstrutura($rsPlanoEstrutura, $stFiltro);

                //cria um select com as contas do Elenco de contas do TCE
                $obCmbElenco = new Select;
                $obCmbElenco->setId        ('slPlano_[cod_conta]');
                $obCmbElenco->setName      ('slPlano_[cod_conta]');
                $obCmbElenco->setCampoId   ('[codigo_estrutural]');
                $obCmbElenco->setCampoDesc ('[codigo_estrutural] - [titulo]');
                $obCmbElenco->addOption    ('','Selecione');
                $obCmbElenco->preencheCombo($rsPlanoEstrutura);
                $obCmbElenco->setValue     ('[cod_estrutural_estrutura]');
                $obCmbElenco->setStyle     ('width:100%');

                //cria uma table para demonstrar os valores para o vinculo
                $obTable = new Table;
                $obTable->setRecordset($rsEstrutural);
                $obTable->addLineNumber(true);

                $obTable->Head->addCabecalho('Cod. Reduzido'     , 5);
                $obTable->Head->addCabecalho('Estrutural'        , 10);
                $obTable->Head->addCabecalho('Descrição'         , 30);
                $obTable->Head->addCabecalho('Elenco Contas TCE' , 40);
                $obTable->Head->addCabecalho('Vinculado'         , 5);

                $obTable->Body->addCampo('[cod_plano]'      , 'C');
                $obTable->Body->addCampo('[cod_estrutural]' , 'C');
                $obTable->Body->addCampo('[nom_conta]'      , 'E');
                $obTable->Body->addCampo($obCmbElenco       , 'E');
                $obTable->Body->addCampo('[vinculado]'      , 'C');

                $obTable->montaHTML(true);
                $stHTML = $obTable->getHtml();
            }
        }

        $stJs .= "jQuery('#spnLista').html('".$stHTML."'); \n";
        $stJs .= "LiberaFrames(true,true); \n";;

        echo $stJs;
    break;
}
?>

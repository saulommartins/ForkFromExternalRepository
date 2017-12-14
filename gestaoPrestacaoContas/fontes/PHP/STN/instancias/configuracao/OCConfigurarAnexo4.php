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
    * Arquivo oculto de Configuração do Anexo 4
    * Data de Criação   : 05/04/2013

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @package URBEM
    * @subpackage Configuração

    * Casos de uso: uc-02.08.07
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );
require_once( CAM_GPC_STN_MAPEAMENTO."TSTNAporteRecursoRPPS.class.php");
require_once( CAM_GPC_STN_MAPEAMENTO."TSTNAporteRecursoRPPSReceita.class.php");

$stPrograma = "ConfigurarAnexo4";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obROrcamentoReceita = new ROrcamentoReceita();
$obTSTNAporteRecursoRPPS = new TSTNAporteRecursoRPPS();
$obTSTNAporteRecursoRPPSReceita = new TSTNAporteRecursoRPPSReceita();

function montaLista(array $arReceita, $inCodAporte)
{

    $rsReceita = new RecordSet();
    $rsReceita->preenche( $arReceita );

    $obTable = new Table();
    $obTable->setRecordSet( $rsReceita );
    // $obTable->setSummary('Lista de Receitas');

    //$obTable->setConditional( true , "#efefef" );

    $obTable->Head->addCabecalho( 'Código Reduzido' , 10 );
    $obTable->Head->addCabecalho( 'Código Estrutural' , 20 );
    $obTable->Head->addCabecalho( 'Descrição' , 50 );

    $obTable->Body->addCampo( 'cod_receita', 'E' );
    $obTable->Body->addCampo( 'mascara_classificacao', 'E' );
    $obTable->Body->addCampo( 'descricao', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s,%s)', array( 'cod_aporte','cod_receita' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "document.getElementById('spnLista_".$inCodAporte."').innerHTML = '".$stHTML."';";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "buscaReceita":
        $stJs = "";
        foreach ($_REQUEST as $key => $value) {
            //indice 0 = nomecampo
            //indice 1 = cod_aporte
            $arCodReceita = explode('_', $key);
            if ($arCodReceita[0] == 'inCodReceita') {
                if ($value != "") {
                    $obROrcamentoReceita->setCodReceita($value);
                    $obROrcamentoReceita->setExercicio(Sessao::getExercicio());
                    $obROrcamentoReceita->listarReceita($rsReceita);
                    $stNomConta = $rsReceita->getCampo('descricao');
                    if (!$stNomConta) {
                        $stJs .= 'f.inCodReceita_'.$arCodReceita[1].'.value = "";';
                        $stJs .= 'f.inCodReceita_'.$arCodReceita[1].'.focus();';
                        $stJs .= 'd.getElementById("stNomReceita_'.$arCodReceita[1].'").innerHTML = "&nbsp;";';
                        $stJs .= "alertaAviso('@Valor inválido. (".$value.")','form','erro','".Sessao::getId()."');";
                    } else {
                        $stJs  = 'd.getElementById("stNomReceita_'.$arCodReceita[1].'").innerHTML = "'.$stNomConta.'";';
                        $stJs .= 'f.inCodReceita_'.$arCodReceita[1].'.value = "'.$value.'";';
                    }
                } else {
                    $stJs .= 'd.getElementById("stNomReceita_'.$arCodReceita[1].'").innerHTML = "&nbsp;";';
                }
            }
        }

        echo $stJs;

    break;
    case "incluiReceita":
        $stJs = "";
        foreach ($_REQUEST as $key => $value) {
            //indice 0 = nomecampo
            //indice 1 = cod_aporte
            $arCodReceita = explode('_', $key);
            if ($arCodReceita[0] == 'inCodReceita') {
                if ($value != "") {
                    $arReceitas = Sessao::read("arReceitaAporte_".$arCodReceita[1]);
                    $obROrcamentoReceita->setCodReceita($value);
                    $obROrcamentoReceita->setExercicio(Sessao::getExercicio());
                    $obROrcamentoReceita->listarReceita($rsReceita);

                    $arTMP = array();
                    if ($rsReceita->getNumLinhas() > 0) {
                        $arTMP['cod_receita'] = $rsReceita->getCampo('cod_receita');
                        $arTMP['descricao'] = $rsReceita->getCampo('descricao');
                        $arTMP['mascara_classificacao'] = $rsReceita->getCampo('mascara_classificacao');
                        $arTMP['cod_aporte'] = $arCodReceita[1];
                        if (in_array($arTMP, $arReceitas)) {
                            $stJs .= "alertaAviso('@A Receita já está na lista. (".$arTMP['cod_receita'].")','form','erro','".Sessao::getId()."');";
                        } else {
                            $arReceitas[] = $arTMP;
                            Sessao::write("arReceitaAporte_".$arCodReceita[1], $arReceitas);
                            $stJs .= montaLista($arReceitas, $arCodReceita[1]);
                        }
                    } else {
                        $stJs .= "alertaAviso('@Não há Receita com este código. (".$value.")','form','erro','".Sessao::getId()."');";
                    }

                    $stJs .= 'f.inCodReceita_'.$arCodReceita[1].'.value = "";';
                    $stJs .= 'f.inCodReceita_'.$arCodReceita[1].'.focus();';
                    $stJs .= 'd.getElementById("stNomReceita_'.$arCodReceita[1].'").innerHTML = "&nbsp;";';
                }
            }
        }
        echo $stJs;
    break;
    case "limparCampos":
        $stJs = "";
        foreach ($_REQUEST as $key => $value) {
            //indice 0 = nomecampo
            //indice 1 = cod_aporte
            $arCodReceita = explode('_', $key);
            if ($arCodReceita[0] == 'btnLimpar') {
                $stJs .= 'f.inCodReceita_'.$arCodReceita[1].'.value = "";';
                $stJs .= 'f.inCodReceita_'.$arCodReceita[1].'.focus();';
                $stJs .= 'd.getElementById("stNomReceita_'.$arCodReceita[1].'").innerHTML = "&nbsp;";';
            }
        }
        echo $stJs;
    break;
    case "excluirListaItens":
        $stJs = "";
        // $obROrcamentoReceita->setCodReceita($value);
        $obROrcamentoReceita->setExercicio(Sessao::getExercicio());
        $obROrcamentoReceita->listarReceita($rsReceita);
        $arTMP = array();
        if ($rsReceita->getNumLinhas() > 0) {
            $arReceitas = Sessao::read("arReceitaAporte_".$_GET['inCodAporte']);
            foreach ($arReceitas as $receita) {
                if ($_GET['inCodAporte'] != $receita['cod_aporte'] || $_GET['inCodReceita'] != $receita['cod_receita']) {
                    $arTMP[] = $receita;
                }
            }
            $arReceitas = $arTMP;

            Sessao::write("arReceitaAporte_".$_GET['inCodAporte'], $arReceitas);
            $stJs .= montaLista($arReceitas, $_GET['inCodAporte']);
        }
        echo $stJs;
    break;

    case "montaListas":
        $stJs = "";
        $obTSTNAporteRecursoRPPS->listarAporteRecursoRPPS($rsAportes, " WHERE exercicio = '".Sessao::getExercicio()."' ", " ORDER BY cod_aporte ");

        while (!$rsAportes->eof()) {
            $stFiltro = " WHERE receita.exercicio = '".Sessao::getExercicio()."'
                            AND cod_aporte = ".$rsAportes->getCampo('cod_aporte')."
                            AND timestamp = (SELECT MAX(timestamp)
                                               FROM stn.aporte_recurso_rpps_receita t2
                                              WHERE t2.exercicio = receita.exercicio )
                            ";
            $obTSTNAporteRecursoRPPSReceita->listarVinculoAporteReceita($rsVinculoReceitaAporte, $stFiltro);

            $arReceitas = Sessao::read("arReceitaAporte_".$rsAportes->getCampo('cod_aporte'));
            if (!$rsVinculoReceitaAporte->eof()) {
                while (!$rsVinculoReceitaAporte->eof()) {
                    $arTMP = array();
                    $arTMP['cod_receita'] = $rsVinculoReceitaAporte->getCampo('cod_receita');
                    $arTMP['descricao'] = $rsVinculoReceitaAporte->getCampo('descricao');
                    $arTMP['mascara_classificacao'] = $rsVinculoReceitaAporte->getCampo('mascara_classificacao');
                    $arTMP['cod_aporte'] = $rsVinculoReceitaAporte->getCampo('cod_aporte');
                    $arReceitas[] = $arTMP;

                    $rsVinculoReceitaAporte->proximo();
                }

                Sessao::write("arReceitaAporte_".$rsAportes->getCampo('cod_aporte'), $arReceitas);
                unset($arTMP);
                unset($arReceitas);
                $stJs .= montaLista($rsVinculoReceitaAporte->arElementos, $rsAportes->getCampo('cod_aporte'));
            }

            $rsAportes->proximo();
        }

        echo $stJs;
        break;
}

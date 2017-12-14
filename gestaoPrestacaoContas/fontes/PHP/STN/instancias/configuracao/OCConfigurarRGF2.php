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
    * Arquivo oculto de Configuração do Anexo 2 RGF
    * Data de Criação   : 28/05/2013

    * @author Desenvolvedor: Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage Configuração
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php" );
require_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );
require_once( CAM_GPC_STN_MAPEAMENTO."TSTNContasRGF2.class.php" );
require_once( CAM_GPC_STN_MAPEAMENTO."TSTNVinculoContasRGF2.class.php" );

$stPrograma = "ConfigurarRGF2";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRContabilidadePlanoConta = new RContabilidadePlanoConta();
$obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica();
$obTSTNContasRGF2 = new TSTNContasRGF2();
$obTSTNVinculoContasRGF2 = new TSTNVinculoContasRGF2();

function montaLista(array $arPlanoConta, $inCodConta)
{
    $rsPlanoConta = new RecordSet();
    $rsPlanoConta->preenche( $arPlanoConta );

    $obTable = new Table();
    $obTable->setRecordSet( $rsPlanoConta );

    $obTable->Head->addCabecalho( 'Código Reduzido' , 10 );
    $obTable->Head->addCabecalho( 'Código Estrutural' , 20 );
    $obTable->Head->addCabecalho( 'Descrição' , 50 );

    $obTable->Body->addCampo( 'cod_plano', 'E' );
    $obTable->Body->addCampo( 'cod_estrutural', 'E' );
    $obTable->Body->addCampo( 'nom_conta', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s,%s)', array( 'cod_conta','cod_plano' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "document.getElementById('spnLista_".$inCodConta."').innerHTML = '".$stHTML."';";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "buscaPlanoConta":
        $stJs = "";
        foreach ($_REQUEST as $key => $value) {
            //indice 0 = nomecampo
            //indice 1 = cod_conta
            $arCodPlanoConta = explode('_', $key);
            if ($arCodPlanoConta[0] == 'inCodPlano') {
                if ($value != "") {
                    $obRContabilidadePlanoContaAnalitica->setCodPlano($value);
                    $obRContabilidadePlanoContaAnalitica->setExercicio(Sessao::getExercicio());
                    $obRContabilidadePlanoContaAnalitica->listarPlanoConta($rsPlanoConta);
                    $stNomConta = $rsPlanoConta->getCampo('nom_conta');
                    if (!$stNomConta) {
                        $stJs .= 'f.inCodPlano_'.$arCodPlanoConta[1].'.value = "";';
                        $stJs .= 'f.inCodPlano_'.$arCodPlanoConta[1].'.focus();';
                        $stJs .= 'd.getElementById("stNomConta_'.$arCodPlanoConta[1].'").innerHTML = "&nbsp;";';
                        $stJs .= "alertaAviso('@Código Reduzido inválido para Conta. (".$value.")','form','erro','".Sessao::getId()."');";
                    } else {
                        $stJs  = 'd.getElementById("stNomConta_'.$arCodPlanoConta[1].'").innerHTML = "'.$stNomConta.'";';
                        $stJs .= 'f.inCodPlano_'.$arCodPlanoConta[1].'.value = "'.$value.'";';
                    }
                } else {
                    $stJs .= 'd.getElementById("stNomConta_'.$arCodPlanoConta[1].'").innerHTML = "&nbsp;";';
                }
            }
        }

        echo $stJs;

    break;

    case "incluiPlanoConta":
        $stJs = "";
        foreach ($_REQUEST as $key => $value) {
            //indice 0 = nomecampo
            //indice 1 = cod_conta
            $arCodPlanoConta = explode('_', $key);
            if ($arCodPlanoConta[0] == 'inCodPlano') {
                if ($value != "") {
                    $arVinculo = Sessao::read("arVinculoContas_".$arCodPlanoConta[1]);
                    $obRContabilidadePlanoContaAnalitica->setCodPlano($value);
                    $obRContabilidadePlanoContaAnalitica->setExercicio(Sessao::getExercicio());
                    $obRContabilidadePlanoContaAnalitica->listarPlanoConta($rsPlanoConta);
                    $stNomConta = $rsPlanoConta->getCampo('nom_conta');

                    $arTMP = array();
                    if ($rsPlanoConta->getNumLinhas() > 0) {
                        $arTMP['cod_plano'] = $rsPlanoConta->getCampo('cod_plano');
                        $arTMP['nom_conta'] = $rsPlanoConta->getCampo('nom_conta');
                        $arTMP['cod_estrutural'] = $rsPlanoConta->getCampo('cod_estrutural');
                        $arTMP['cod_conta'] = $arCodPlanoConta[1];
                        if (in_array($arTMP, $arVinculo)) {
                            $stJs .= "alertaAviso('@A Conta já está na lista. (".$arTMP['cod_plano'].")','form','erro','".Sessao::getId()."');";
                        } else {
                            $arVinculo[] = $arTMP;
                            Sessao::write("arVinculoContas_".$arCodPlanoConta[1], $arVinculo);
                            $stJs .= montaLista($arVinculo, $arCodPlanoConta[1]);
                        }
                    } else {
                        $stJs .= "alertaAviso('@Não há Conta com este código. (".$value.")','form','erro','".Sessao::getId()."');";
                    }

                    $stJs .= 'f.inCodPlano_'.$arCodPlanoConta[1].'.value = "";';
                    $stJs .= 'f.inCodPlano_'.$arCodPlanoConta[1].'.focus();';
                    $stJs .= 'd.getElementById("stNomConta_'.$arCodPlanoConta[1].'").innerHTML = "&nbsp;";';
                }
            }
        }
        echo $stJs;
    break;
    case "limparCampos":
        $stJs = "";
        foreach ($_REQUEST as $key => $value) {
            //indice 0 = nomecampo
            //indice 1 = cod_conta
            $arCodConta = explode('_', $key);
            if ($arCodConta[0] == 'btnLimpar') {
                $stJs .= 'f.inCodPlano_'.$arCodConta[1].'.value = "";';
                $stJs .= 'f.inCodPlano_'.$arCodConta[1].'.focus();';
                $stJs .= 'd.getElementById("stNomConta_'.$arCodConta[1].'").innerHTML = "&nbsp;";';
            }
        }
        echo $stJs;
    break;
    case "excluirListaItens":
        $stJs = "";
        $obRContabilidadePlanoConta->setExercicio(Sessao::getExercicio());
        $obRContabilidadePlanoConta->listar($rsPlanoConta);
        $arTMP = array();
        if ($rsPlanoConta->getNumLinhas() > 0) {
            $arVinculos = Sessao::read("arVinculoContas_".$_GET['inCodConta']);
            foreach ($arVinculos as $arVinculo) {
                if ($_GET['inCodConta'] != $arVinculo['cod_conta'] || $_GET['inCodPlano'] != $arVinculo['cod_plano']) {
                    $arTMP[] = $arVinculo;
                }
            }
            $arVinculo = $arTMP;

            Sessao::write("arVinculoContas_".$_GET['inCodConta'], $arVinculo);
            $stJs .= montaLista($arVinculo, $_GET['inCodConta']);
        }
        echo $stJs;
    break;

    case "montaListas":
        $stJs = "";
        $obTSTNContasRGF2->listarContasRGF2($rsContas);

        while (!$rsContas->eof()) {
            $stFiltro = " WHERE plano_analitica.exercicio = '".Sessao::getExercicio()."'
                            AND vinculo_contas_rgf_2.cod_conta = ".$rsContas->getCampo('cod_conta')."
                            AND timestamp = (SELECT MAX(timestamp)
                                               FROM stn.vinculo_contas_rgf_2 t2
                                              WHERE t2.exercicio = plano_analitica.exercicio )
                            ";
            $obTSTNVinculoContasRGF2->listarVinculoContasRGF2($rsVinculo, $stFiltro);

            $arVinculo = Sessao::read("arVinculoContas_".$rsContas->getCampo('cod_conta'));
            if (!$rsVinculo->eof()) {
                while (!$rsVinculo->eof()) {
                    $arTMP = array();
                    $arTMP['cod_conta'] = $rsVinculo->getCampo('cod_conta');
                    $arTMP['nom_conta'] = $rsVinculo->getCampo('nom_conta');
                    $arTMP['cod_estrutural'] = $rsVinculo->getCampo('cod_estrutural');
                    $arTMP['cod_plano'] = $rsVinculo->getCampo('cod_plano');
                    $arVinculo[] = $arTMP;

                    $rsVinculo->proximo();
                }

                Sessao::write("arVinculoContas_".$rsContas->getCampo('cod_conta'), $arVinculo);
                unset($arTMP);
                unset($arVinculo);
                $stJs .= montaLista($rsVinculo->arElementos, $rsContas->getCampo('cod_conta'));
            }

            $rsContas->proximo();
        }

        echo $stJs;
        break;
}

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
    * Oculto de processamento do componente ISelectRegSubCarEsp
    * Data de Criação: 19/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencherSubDivisao($boFuncao=false)
{
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php" );
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    //Limpa Subdivisão
    $stJs .= "f.inCodSubDivisao.value = '';                                                         \n";
    $stJs .= "limpaSelect(f.stSubDivisao,0);                                                        \n";
    $stJs .= "f.stSubDivisao[0] = new Option('Selecione','', 'selected');                           \n";
    if ($boFuncao) {
        //Limpa Função
        $stJs .= "f.inCodFuncao.value = '';                                                         \n";
        $stJs .= "limpaSelect(f.stFuncao,0);                                                        \n";
        $stJs .= "f.stFuncao[0] = new Option('Selecione','', 'selected');                           \n";
    } else {
        //Limpa Cargo
        $stJs .= "f.inCodCargo.value = '';                                                          \n";
        $stJs .= "limpaSelect(f.stCargo,0);                                                         \n";
        $stJs .= "f.stCargo[0] = new Option('Selecione','', 'selected');                            \n";
    }
    //Limpa Especialidade
    $stJs .= "f.inCodEspecialidade.value = '';                                                      \n";
    $stJs .= "limpaSelect(f.stEspecialidade,0);                                                     \n";
    $stJs .= "f.stEspecialidade[0] = new Option('Selecione','', 'selected');                        \n";

    if ($_REQUEST["inCodRegime"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->roPessoalRegime->setCodRegime( $_REQUEST['inCodRegime'] );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            $inCodSubDivisao  = $rsSubDivisao->getCampo( "cod_sub_divisao" );
            $stSubDivisao     = $rsSubDivisao->getCampo( "nom_sub_divisao" );
            $stJs .= "f.stSubDivisao.options[$inContador] = new Option('".$stSubDivisao."','".$inCodSubDivisao."',''); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
    }

    return $stJs;
}

function preencherCargo()
{
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php" );
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    //Limpa Cargo
    $stJs .= "f.inCodCargo.value = '';                                                              \n";
    $stJs .= "limpaSelect(f.stCargo,0);                                                             \n";
    $stJs .= "f.stCargo[0] = new Option('Selecione','', 'selected');                                \n";
    //Limpa Especialidade
    $stJs .= "f.inCodEspecialidade.value = '';                                                      \n";
    $stJs .= "limpaSelect(f.stEspecialidade,0);                                                     \n";
    $stJs .= "f.stEspecialidade[0] = new Option('Selecione','', 'selected');                        \n";
    if ($_REQUEST["inCodSubDivisao"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($_REQUEST['inCodSubDivisao']);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisao( $rsCargo );
        $inContador = 1;
        while ( !$rsCargo->eof() ) {
            $inCodCargo = $rsCargo->getCampo( "cod_cargo" );
            $stCargo    = $rsCargo->getCampo( "descricao" );
            $stJs .= "f.stCargo.options[$inContador] = new Option('".$stCargo."','".$inCodCargo."',''); \n";
            $inContador++;
            $rsCargo->proximo();
        }
    }

    return $stJs;
}

function preencherFuncao()
{
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php" );
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    //Limpa Cargo
    $stJs .= "f.inCodFuncao.value = '';                                                             \n";
    $stJs .= "limpaSelect(f.stFuncao,0);                                                            \n";
    $stJs .= "f.stFuncao[0] = new Option('Selecione','', 'selected');                               \n";
    //Limpa Especialidade
    $stJs .= "f.inCodEspecialidade.value = '';                                                      \n";
    $stJs .= "limpaSelect(f.stEspecialidade,0);                                                     \n";
    $stJs .= "f.stEspecialidade[0] = new Option('Selecione','', 'selected');                        \n";
    if ($_REQUEST["inCodSubDivisao"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($_REQUEST['inCodSubDivisao']);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisao( $rsFuncao );
        $inContador = 1;
        while ( !$rsFuncao->eof() ) {
            $inCodFuncao = $rsFuncao->getCampo( "cod_cargo" );
            $stFuncao    = $rsFuncao->getCampo( "descricao" );
            $stJs .= "f.stFuncao.options[$inContador] = new Option('".$stFuncao."','".$inCodFuncao."',''); \n";
            $inContador++;
            $rsFuncao->proximo();
        }
    }

    return $stJs;
}

function preencherEspecialidade()
{
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php" );
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    //Limpa Especialidade
    $stJs .= "f.inCodEspecialidade.value = '';                                                      \n";
    $stJs .= "limpaSelect(f.stEspecialidade,0);                                                     \n";
    $stJs .= "f.stEspecialidade[0] = new Option('Selecione','', 'selected');                        \n";
    if ($_REQUEST["inCodCargo"] or $_REQUEST['inCodFuncao']) {
        $inCodCargo = ( $_REQUEST["inCodCargo"] != "" ) ? $_REQUEST["inCodCargo"] : $_REQUEST['inCodFuncao'];
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $inCodCargo );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
        if ( $rsEspecialidade->getNumLinhas() > 0 ) {
            $stValidacao  = "stCampo = document.frm.inCodEspecialidade;                                   ";
            $stValidacao .= "if (stCampo.value == \'\') {                                                 ";
            $stValidacao .= "   erro = true; mensagem += \'@Campo Especialidade inválido!(\'+stCampo.value+\')\';";
            $stValidacao .= "}                                                                              ";
        } else {
            $stValidacao = "";
        }
        $stJs .= "f.hdnHiddenEvalRegSubCarEsp.value = '".$stValidacao."';   \n";
        $inContador = 1;
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            $stJs .= "f.stEspecialidade.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencherSubDivisao":
        $ststJs = preencherSubDivisao();
    break;
    case "preencherSubDivisaoFuncao":
        $ststJs = preencherSubDivisao(true);
    break;
    case "preencherCargo":
        $ststJs = preencherCargo();
    break;
    case "preencherFuncao":
        $ststJs = preencherFuncao();
    break;
    case "preencherEspecialidade":
        $ststJs = preencherEspecialidade();
    break;
}

if ($ststJs) {
    echo $ststJs;
}
?>

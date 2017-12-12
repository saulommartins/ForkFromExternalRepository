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
    * Oculto
    * Data de Criação: 11/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherSubDivisao($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->roPessoalRegime->setCodRegime( $_POST['inCodRegime'] );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao($rsSubDivisao,"","",$boTransacao );
    $inContador = 1;
    //Limpa combo de sub-divisão
    $stJs .= "limpaSelect(f.stSubDivisao,0);                                \n";
    $stJs .= "f.stSubDivisao[0] = new Option('Selecione','','selected');    \n";
    $stJs .= "f.inCodSubDivisao.value = '';                                 \n";
    //Limpa combo de cargo
    $stJs .= "limpaSelect(f.stCargo,0);                                     \n";
    $stJs .= "f.stCargo[0] = new Option('Selecione','','selected');         \n";
    $stJs .= "f.inCodCargo.value = '';                                      \n";
    //Limpa combo de especialidade
    $stJs .= "limpaSelect(f.stEspecialidade,0);                             \n";
    $stJs .= "f.stEspecialidade[0] = new Option('Selecione','','selected'); \n";
    $stJs .= "f.inCodEspecialidade.value = '';                              \n";
    while ( !$rsSubDivisao->eof() ) {
        $stJs .= "f.stSubDivisao.options[$inContador] = new Option('".$rsSubDivisao->getCampo('nom_sub_divisao')."','".$rsSubDivisao->getCampo('cod_sub_divisao')."',''); \n";
        $inContador++;
        $rsSubDivisao->proximo();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencherFuncao($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_POST['inCodSubDivisao'] );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisao( $rsCargo );
    //Limpa combo de cargo
    $stJs .= "limpaSelect(f.stCargo,0);                                     \n";
    $stJs .= "f.stCargo[0] = new Option('Selecione','','selected');         \n";
    $stJs .= "f.inCodCargo.value = '';                                      \n";
    //Limpa combo de especialidade
    $stJs .= "limpaSelect(f.stEspecialidade,0);                             \n";
    $stJs .= "f.stEspecialidade[0] = new Option('Selecione','','selected'); \n";
    $stJs .= "f.inCodEspecialidade.value = '';                              \n";
    $inContador = 1;
    while ( !$rsCargo->eof() ) {
        $stJs .= "f.stCargo.options[$inContador] = new Option('".$rsCargo->getCampo('descricao')."','".$rsCargo->getCampo('cod_cargo')."',''); \n";
        $inContador++;
        $rsCargo->proximo();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencherEspecialidade($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->setCodCargo( $_POST['inCodCargo'] );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->listarEspecialidadesPorCargo( $rsEspecialidade );
    $stJs .= "limpaSelect(f.stEspecialidade,0);                                \n";
    $stJs .= "f.stEspecialidade[0] = new Option('Selecione','','selected');    \n";
    $stJs .= "f.inCodEspecialidade.value = '';                                 \n";
    $inContador = 1;
    while ( !$rsEspecialidade->eof() ) {
        $stJs .= "f.stEspecialidade.options[$inContador] = new Option('".$rsEspecialidade->getCampo('descricao')."','".$rsEspecialidade->getCampo('cod_especialidade')."',''); \n";
        $inContador++;
        $rsEspecialidade->proximo();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

switch ($_POST["stCtrl"]) {
    case "preencherSubDivisao":
        $stJs.= preencherSubDivisao();
    break;
    case "preencherFuncao":
        $stJs.= preencherFuncao();
    break;
    case "preencherEspecialidade":
        $stJs.= preencherEspecialidade();
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>

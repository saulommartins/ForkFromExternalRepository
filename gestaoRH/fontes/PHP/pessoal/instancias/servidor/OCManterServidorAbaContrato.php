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
    * Página processamento ocuto Pessoal ServidorP
    * Data de Criação   : 14/12/2004
    *

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: alex $
    $Date: 2008-03-17 11:51:54 -0300 (Seg, 17 Mar 2008) $

    * Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                                 );
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"								);
include_once (CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" 															);
include_once CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php';
include_once ( CAM_GRH_PES_MAPEAMENTO.'FRecuperaQuantidadeMesesProgressaoAfastamento.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterServidor";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOculIdentificacao = "OC".$stPrograma."AbaIdentificacao.php";
$pgOculDocumentacao  = "OC".$stPrograma."AbaDocumentacao.php";
$pgOculContrato      = "OC".$stPrograma."AbaContrato.php";
$pgOculPrevidencia   = "OC".$stPrograma."AbaPrevidencia.php";
$pgOculDependentes   = "OC".$stPrograma."AbaDependentes.php";
$pgOculAtributos     = "OC".$stPrograma."AbaAtributos.php";
$pgJS                = "JS".$stPrograma.".js";

function comparaComDataNascimento($stCampo,$stRotulo)
{
    $dtComparacao = $_POST[$stCampo];
    $dtNascimento = ($_POST['stDataNascimento'] != "") ? $_POST['stDataNascimento'] : $_POST['dtDataNascimento'];
    $stJs = "";
    if ($dtNascimento == "") {
        $stMensagem = "campo Data de Nascimento da Guia Identificação inválido()!";
        $stJs .= "f.".$stCampo.".value = '';\n";
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
    } else {
        if ( $dtComparacao != "" and sistemaLegado::comparaDatas($dtNascimento,$dtComparacao) ) {
            $stMensagem = $stRotulo." (".$dtComparacao.") não pode ser anterior à Data de Nascimento(".$dtNascimento.")!";
            $stJs .= "f.".$stCampo.".value = '';\n";
            $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
        }
    }

    return $stJs;
}

function validaDataPosse()
{
    $stValida = comparaComDataNascimento("dtDataNomeacao","Data de Nomeação");
    if ($stValida != "") {
        $stJs .= $stValida;
        $stJs .= "f.dtDataPosse.value    = '';\n";
        $stJs .= "f.dtAdmissao.value    = '';\n";
        $stJs .= "f.dtVigenciaSalario.value    = '';\n";
    } else {
        if ($_POST['dtDataNomeacao'] != "" and $_POST['dtDataPosse'] == "") {
            $stJs .= "f.dtDataPosse.value = '".$_POST['dtDataNomeacao']."';     \n";
            $stJs .= "f.dtAdmissao.value = '".$_POST['dtDataNomeacao']."';     \n";
            $stJs .= "f.dtVigenciaSalario.value = '".$_POST['dtDataNomeacao']."';     \n";
        }
        if ($_POST['dtDataNomeacao'] != "" and $_POST['dtDataPosse'] != "") {
            if ( sistemaLegado::comparaDatas($_POST['dtDataNomeacao'],$_POST['dtDataPosse']) ) {
                $stMensagem = "Data da posse (".$_POST['dtDataPosse'].") não pode ser anterior à data de nomeação(".$_POST['dtDataNomeacao'].")!";
                $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
                $stJs .= "f.dtDataPosse.value='';                                           \n";
                $stJs .= "f.dtAdmissao.value= '';\n";
                $stJs .= "f.dtVigenciaSalario.value    = '';\n";
                $stJs .= "f.dtDataPosse.focus();                                            \n";
            } else {
                $stJs .= preencheProgressao($_POST['inCodPadrao']);
            }
        }
        if ($_POST['stAcao'] == "alterar") {
            $stJs .= "f.dtDataAlteracaoFuncao.value = f.dtDataPosse.value;\n";
        }
    }

    return $stJs;
}

function validaDataAdmissao()
{
    $stValida = comparaComDataNascimento("dtAdmissao","Data Admissão");
    if ($stValida != "") {
        $stJs .= $stValida;
    }

    return $stJs;
}

function preencheProgressao($inCodPadrao)
{
    $stValida = comparaComDataNascimento("dtDataProgressao","Data Início para Progressão");
    if ($stValida != "") {
        $stJs .= $stValida;
    } else {
        include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php" );
        $inCodProgressao  = "";
        $stLblProgressao  = "&nbsp;";
        //$inCodPadrao      = $_POST['inCodPadrao'];
        $stDataProgressao = $_POST['dtDataProgressao'];
        if ($inCodPadrao != "" and $stDataProgressao != "") {
            //calcula diferença de meses entre datas
            $stDataProgressao    = explode('/',$stDataProgressao);

            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

            $dtDataAtual = explode('/',$rsPeriodoMovimentacao->getCampo("dt_final"));

            if (isset($_REQUEST['inCodContrato'])) {
                $inCodContrato = $_REQUEST['inCodContrato'];
            } else {
               $inCodContrato = 0;
            }

            $rsQtdMeses = new RecordSet;
            $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento = new FPessoalRecuperaQuantidadeMesesProgressaoAfastamento;
            $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('cod_contrato', $inCodContrato);
            $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_inicial', $stDataProgressao[2]."-".$stDataProgressao[1]."-".$stDataProgressao[0]);
            $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_final', $dtDataAtual[2]."-".$dtDataAtual[1]."-".$dtDataAtual[0]);
            $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->recuperaMesesProgressaoAfastamento($rsQtdMeses);

            $arQtdMeses = $rsQtdMeses->arElementos;
            //Lista as progressões, a última progressão do rsProgressao é a progressão do padrão para esta data de início de progressão
            $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
            $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
            $obRFolhaPagamentoPadrao->addNivelPadrao();
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setQtdMeses( $arQtdMeses[0]['retorno'] );
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao );
            $rsProgressao->setUltimoElemento();
            if ( $rsProgressao->getNumLinhas() > 0 ) {
                $stLblProgressao = $rsProgressao->getCampo('cod_nivel_padrao')." - ".$rsProgressao->getCampo('descricao');
                $inCodProgressao = $rsProgressao->getCampo('cod_nivel_padrao');
            }
        }
        $stJs .= calculaSalario( $inCodPadrao, $inCodProgressao );

        $stJs .= "d.getElementById('stlblProgressao').innerHTML = '".$stLblProgressao."'; \n";
        $stJs .= "f.inCodProgressao.value = '".$inCodProgressao."'; \n";
    }

    return $stJs;
}

function preencheProgressaoAlterar()
{
    global $inCodPadrao,$stDataProgressao,$dtDataProgressao,$stHorasMensais;
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php" );
    $inCodProgressao  = "";
    $stLblProgressao  = "&nbsp;";
    $stDataProgressao = ( $stDataProgressao != "" ) ? $stDataProgressao : $dtDataProgressao;
    if ($inCodPadrao && $stDataProgressao) {
        //calcula diferença de meses entre datas -- INICIO
        $stDataProgressao    = explode('/',$stDataProgressao);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

        $dtDataAtual = explode('/',$rsPeriodoMovimentacao->getCampo("dt_final"));

        //calcula diferença de meses entre datas -- FIM
        $rsQtdMeses = new RecordSet;
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento = new FPessoalRecuperaQuantidadeMesesProgressaoAfastamento;
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('cod_contrato', $_REQUEST['inCodContrato']);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_inicial', $stDataProgressao[2]."-".$stDataProgressao[1]."-".$stDataProgressao[0]);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_final', $dtDataAtual[2]."-".$dtDataAtual[1]."-".$dtDataAtual[0]);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->recuperaMesesProgressaoAfastamento($rsQtdMeses);

        $arQtdMeses = $rsQtdMeses->arElementos;
        //Lista as progressões, a última progressão do rsProgressao é a progressão do padrão para esta data de início de progressão
        $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
        $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
        $obRFolhaPagamentoPadrao->addNivelPadrao();

        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setQtdMeses( $arQtdMeses[0]['retorno'] );
        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao );
        $rsProgressao->setUltimoElemento();
        if ( $rsProgressao->getNumLinhas() > 0 ) {
            $stLblProgressao = $rsProgressao->getCampo('cod_nivel_padrao')." - ".$rsProgressao->getCampo('descricao');
            $inCodProgressao = $rsProgressao->getCampo('cod_nivel_padrao');
        }
    }
    //$stJs .= calculaSalario( $inCodPadrao, $inCodProgressao,$stHorasMensais );
    $stJs .= "d.getElementById('stlblProgressao').innerHTML = '".$stLblProgressao."'; \n";
    $stJs .= "f.inCodProgressao.value = '".$inCodProgressao."'; \n";

    return $stJs;

}

function calculaSalario($inCodPadrao = "", $inCodProgressao = "", $inHorasMensais = "")
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php" );
    //O valor do $_REQUEST["stHorasMensais"] é setado na função preencheInformacoesSalariais
    $inHorasMensais = $inHorasMensais != ""   ? $inHorasMensais   : $_REQUEST["stHorasMensais"];
    //Para quando o calculaSalario é chamado sem ter passado pelo preencheInformacoesSalariais
    $inHorasMensais = $inHorasMensais   ? $inHorasMensais   : $_POST["stHorasMensais"];
    $nuSalario = "";

    if ($inCodPadrao != "") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadrao.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodo);

        $obTFolhaPagamentoPadrao = new TFolhaPagamentoPadrao();
        $stFiltro = " AND FPP.cod_padrao = ".$inCodPadrao;
        $stFiltro .= " AND FPP.vigencia <= to_date('".$rsPeriodo->getCampo("dt_final")."','dd/mm/yyyy')";
        $obTFolhaPagamentoPadrao->recuperaRelacionamento($rsPadrao,$stFiltro);

        $inHorasMensaisPadrao = ($rsPadrao->getCampo('horas_mensais') > 0.00) ? $rsPadrao->getCampo('horas_mensais') : 1;
        if ($inCodProgressao != "") {
            $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
            $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
            $obRFolhaPagamentoPadrao->addNivelPadrao();
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao( $inCodProgressao);
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao );
            $nuSalarioPadrao      = $rsProgressao->getCampo('valor');
            $nuSalarioHoraPadrao  = $nuSalarioPadrao / $inHorasMensaisPadrao;
            $nuSalario            = $nuSalarioHoraPadrao * $inHorasMensais;
        } else {
            $nuSalarioPadrao      = $rsPadrao->getCampo('valor');
            $nuSalarioHoraPadrao  = $nuSalarioPadrao / $inHorasMensaisPadrao;
            $nuSalario            = $nuSalarioHoraPadrao * $inHorasMensais;
        }
    }

    if ($nuSalario != '') {
        $nuSalario = number_format($nuSalario, 2, ',','.');
        $stJs .= "f.inSalario.value = '".$nuSalario."'; \n";

        return $stJs;
    }
}

function validaDataExameMedico()
{
    $stValida = comparaComDataNascimento("dtValidadeExameMedico","Validade do Exame Médico");
    if ($stValida != "") {
        $stJs .= $stValida;
    } else {
        if ( $_POST['dtValidadeExameMedico'] != "" and sistemaLegado::comparaDatas($_POST['dtDataNomeacao'],$_POST['dtValidadeExameMedico']) ) {
            $stMensagem = 'Data de Validade do Exame Médico não pode ser anterior à data de nomeação!';
            $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');";
        }
    }

    return $stJs;
}

function preencheSubDivisao()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );
    $js .= "limpaSelect(f.stSubDivisao,0); \n";
    $js .= "f.stSubDivisao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodSubDivisao.value = ''; \n";

    $js .= "f.inCodCargo.value = ''; \n";
    $js .= "limpaSelect(f.stCargo,0); f.stCargo[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";

    //Limpa componentes da função
    $js .= "limpaSelect(f.stSubDivisaoFuncao,0);\n";
    $js .= "f.stSubDivisaoFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodSubDivisaoFuncao.value = ''; \n";

    $js .= "limpaSelect(f.stFuncao,0);\n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodFuncao.value = ''; \n";

    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);\n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";

    if ($_REQUEST["inCodRegime"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->roPessoalRegime->setCodRegime( $_REQUEST['inCodRegime'] );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            $inCodSubDivisao  = $rsSubDivisao->getCampo( "cod_sub_divisao" );
            $stSubDivisao     = $rsSubDivisao->getCampo( "nom_sub_divisao" );
            $arAcao = explode("_",$_POST['stAcao']);
            if ($arAcao[0] == 'alterar') {
                if ($inCodSubDivisao == $_REQUEST["inCodSubDivisao"]) {
                    $stSelected = "selected";
                    $js .= "f.inCodSubDivisao.value = '".$_REQUEST["inCodSubDivisao"]."'; \n";

                } else {
                    $stSelected = "";
                }
            }
            $js .= "f.stSubDivisao.options[$inContador] = new Option('".$stSubDivisao."','".$inCodSubDivisao."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
        $_REQUEST["inCodRegimeFuncao"] = $_REQUEST['inCodRegime'];
        $js .= preencheSubDivisaoFuncao();
    }
    $stJs .= $js;

    return $stJs;
}

function preencheSubDivisaoAlterar()
{
    global $inCodRegime,$inCodSubDivisao;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );
    $stFiltro = " AND pr.cod_regime = ".$inCodRegime;
    if ($inCodRegime) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            if ($inCodSubDivisao == $rsSubDivisao->getCampo( "cod_sub_divisao" )) {
                $stSelected = "selected";
                $stJs .= "f.inCodSubDivisao.value = '".$inCodSubDivisao."'; \n";
            } else {
                $stSelected = "";
            }
            $stJs .= "f.stSubDivisao.options[$inContador] = new Option('".$rsSubDivisao->getCampo( "nom_sub_divisao" )."','".$rsSubDivisao->getCampo( "cod_sub_divisao" )."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
        $stJs .= "f.stSubDivisao.value = '".$inCodSubDivisao."'; \n";
    }

    $stJs .= "f.stRegime.disabled = true;";
    $stJs .= "f.inCodRegime.disabled = true;";
    $stJs .= "f.stRegime.style.color = '#333333';";
    $stJs .= "f.inCodRegime.style.color = '#333333';";

    $stJs .= "f.stSubDivisao.disabled = true;";
    $stJs .= "f.inCodSubDivisao.disabled = true;";
    $stJs .= "f.stSubDivisao.style.color = '#333333';";
    $stJs .= "f.inCodSubDivisao.style.color = '#333333';";

    return $stJs;
}

function preencheSubDivisaoFuncao()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );

    $js .= "f.inCodSubDivisaoFuncao.value = '';                                                 \n";
    $js .= "limpaSelect(f.stSubDivisaoFuncao,0);                                                \n";
    $js .= "f.stSubDivisaoFuncao[0] = new Option('Selecione','', 'selected');                   \n";
    $js .= "f.inCodFuncao.value = '';                                                           \n";
    $js .= "limpaSelect(f.stFuncao,0);                                                          \n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');                             \n";
    $js .= "f.inCodEspecialidadeFuncao.value = '';                                              \n";
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);                                             \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');                \n";

    $stFiltro = " AND pr.cod_regime = ".$_REQUEST["inCodRegimeFuncao"];
    if ($_REQUEST["inCodRegimeFuncao"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            $inCodSubDivisao  = $rsSubDivisao->getCampo( "cod_sub_divisao" );
            $stSubDivisao     = $rsSubDivisao->getCampo( "nom_sub_divisao" );
            $arAcao = explode("_",$_POST['stAcao']);
            if ($arAcao[0] == 'alterar') {
                $stSelected = "";
            }
            $js .= "f.stSubDivisaoFuncao.options[$inContador] = new Option('".$stSubDivisao."','".$inCodSubDivisao."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
    }
    $stJs .= limpaInformacoesSalariais();
    $stJs .= $js;
    if ($_POST['stAcao'] == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $stJs .= "f.dtDataAlteracaoFuncao.readOnly = false;                                 \n";
        $stJs .= "f.dtDataAlteracaoFuncao.style.color = '#000000';                          \n";
        $stJs .= "f.dtDataAlteracaoFuncao.value = '$dtCompetenciaFinal';  \n";
    }

    return $stJs;
}

function preencheSubDivisaoFuncaoAlterar()
{
    global $inCodRegimeFuncao,$inCodSubDivisaoFuncao;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );

    $js .= "f.inCodSubDivisaoFuncao.value = '';                                                 \n";
    $js .= "limpaSelect(f.stSubDivisaoFuncao,0);                                                \n";
    $js .= "f.stSubDivisaoFuncao[0] = new Option('Selecione','', 'selected');                   \n";
    $js .= "f.inCodFuncao.value = '';                                                           \n";
    $js .= "limpaSelect(f.stFuncao,0);                                                          \n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');                             \n";
    $js .= "f.inCodEspecialidadeFuncao.value = '';                                              \n";
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);                                             \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');                \n";
    $stFiltro = " AND pr.cod_regime = ".$inCodRegimeFuncao;
    if ($inCodRegimeFuncao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            $inCodSubDivisao  = $rsSubDivisao->getCampo( "cod_sub_divisao" );
            $stSubDivisao     = $rsSubDivisao->getCampo( "nom_sub_divisao" );
            if ($inCodSubDivisao == $inCodSubDivisaoFuncao) {
                $stSelected = "selected";
                $js .= "f.inCodSubDivisaoFuncao.value = '".$inCodSubDivisaoFuncao."'; \n";

            } else {
                $stSelected = "";
            }
            $js .= "f.stSubDivisaoFuncao.options[$inContador] = new Option('".$stSubDivisao."','".$inCodSubDivisao."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
        $js .= "f.stSubDivisaoFuncao.value = '".$inCodSubDivisaoFuncao."'; \n";
    }
    $stJs .= $js;

    return $stJs;
}

function preencheCargo()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );
    $js .= "f.inCodCargo.value = ''; \n";
    $js .= "limpaSelect(f.stCargo,0); f.stCargo[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";

    //Limpa componentes da função
    $js .= "limpaSelect(f.stFuncao,0);\n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodFuncao.value = ''; \n";

    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);\n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
    if ($_REQUEST["inCodSubDivisao"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($_POST['inCodSubDivisao']);

        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo);

        $arAcao = explode("_",$_POST['stAcao']);
        if ($arAcao[0] != "alterar") {
            $js .= "f.inCodCargo.value = ''; \n";
            $js .= "limpaSelect(f.stCargo,0); f.stCargo[0] = new Option('Selecione','', 'selected');\n";
            $js .= "f.stCargo[0] = new Option('Selecione','','selected');\n";
        }
        $js .= "f.inCodEspecialidadeCargo.value = '';\n";
        $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        while ( !$rsCargo->eof() ) {
            $inCodCargo = $rsCargo->getCampo( "cod_cargo" );
            $stCargo    = $rsCargo->getCampo( "descricao" );
            $arAcao = explode("_",$_POST['stAcao']);
            if ($arAcao[0] == 'alterar') {
                if ($inCodCargo == $_REQUEST["inCodCargo"]) {
                    $stSelected = "selected";
                    $js .= "f.inCodCargo.value = '".$_REQUEST["inCodCargo"]."'; \n";
                } else {
                    $stSelected = "";
                }
            }
            if( $stSelected != "selected" )
            $js .= "f.stCargo.options[$inContador] = new Option('".$stCargo."','".$inCodCargo."','".$stSelected."'); \n";
            $inContador++;
            $rsCargo->proximo();
        }
        $_REQUEST["inCodSubDivisaoFuncao"] = $_REQUEST['inCodSubDivisao'];
        $js .= preencheFuncao();
    }
    $stJs .= $js;

    return $stJs;
}

function preencheCargoAlterar()
{
    global $inCodSubDivisao,$inCodCargo;
    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );
    if ($inCodSubDivisao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($inCodSubDivisao);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($inCodCargo);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(true);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo);
        $inContador = 1;
        $boDesbloqueia = false;
        while ( !$rsCargo->eof() ) {
            if ($inCodCargo == $rsCargo->getCampo( "cod_cargo" )) {
                $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
                $boComparaDatas = SistemaLegado::comparaDatas($rsUltimaMovimentacao->getCampo('dt_final'), $rsCargo->getCampo('dt_publicacao'), true);

                if ($boComparaDatas === false || ($rsCargo->getCampo('dt_termino') !== null && $rsCargo->getCampo('dt_termino') >= $rsUltimaMovimentacao->getCampo('dt_inicial'))) {
                    $boDesbloqueia = true;
                }

                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $stJs .= "f.stCargo.options[$inContador] = new Option('".$rsCargo->getCampo( "descricao" )."','".$rsCargo->getCampo( "cod_cargo" )."','".$stSelected."'); \n";
            $inContador++;
            $rsCargo->proximo();
        }
        $stJs .= "f.inCodCargo.value = '".$inCodCargo."'; \n";
        $stJs .= "f.stCargo.value = '".$inCodCargo."'; \n";
        if ($boDesbloqueia === false) {
            $stJs .= "f.stCargo.disabled = true;";
            $stJs .= "f.inCodCargo.disabled = true;";
            $stJs .= "f.stCargo.style.color = '#333333';";
            $stJs .= "f.inCodCargo.style.color = '#333333';";
        }
    }

    return $stJs;
}

function preencheFuncao()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );
    $js .= "f.inCodFuncao.value = '';\n";
    $js .= "limpaSelect(f.stFuncao,0);\n";
    $js .= "f.stFuncao[0] = new Option('Selecione','','selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0); \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";

    if ($_REQUEST["inCodSubDivisaoFuncao"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($_REQUEST['inCodSubDivisaoFuncao']);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao);
        $js .= "f.inCodFuncao.value = '';\n";
        $js .= "limpaSelect(f.stFuncao,0);\n";
        $js .= "f.stFuncao[0] = new Option('Selecione','','selected');\n";
        $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
        $js .= "limpaSelect(f.stEspecialidadeFuncao,0); f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        while ( !$rsFuncao->eof() ) {
            $inCodFuncao = $rsFuncao->getCampo( "cod_cargo" );
            $stFuncao    = $rsFuncao->getCampo( "descricao" );
            $js .= "f.stFuncao.options[$inContador] = new Option('".$stFuncao."','".$inCodFuncao."','".$stSelected."'); \n";
            $inContador++;
            $rsFuncao->proximo();
        }
    }
    $js .= limpaInformacoesSalariais();
    $stJs .= $js;
    if ($_POST['stAcao'] == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $stJs .= "f.dtDataAlteracaoFuncao.readOnly = false;                                 \n";
        $stJs .= "f.dtDataAlteracaoFuncao.style.color = '#000000';                          \n";
        $stJs .= "f.dtDataAlteracaoFuncao.value = '$dtCompetenciaFinal';  \n";
    }

    return $stJs;
}

function preencheFuncaoAlterar()
{
    global $inCodSubDivisaoFuncao,$inCodFuncao;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );
    if ($inCodSubDivisaoFuncao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($inCodSubDivisaoFuncao);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($inCodFuncao);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(true);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao);
        $inContador = 1;
        while ( !$rsFuncao->eof() ) {
            if ($inCodFuncao == $rsFuncao->getCampo( "cod_cargo" )) {
                $stSelectedFuncao = "selected";
            } else {
                $stSelectedFuncao = "";
            }
            $stJs .= "f.stFuncao.options[$inContador] = new Option('".$rsFuncao->getCampo( "descricao" )."','".$rsFuncao->getCampo( "cod_cargo" )."','".$stSelectedFuncao."'); \n";
            $inContador++;
            $rsFuncao->proximo();
        }
        $stJs .= "f.inCodFuncao.value = '".$inCodFuncao."'; \n";
        $stJs .= "f.stFuncao.value = '".$inCodFuncao."'; \n";
    }

    return $stJs;
}

function preencheEspecialidadeFuncaoAlterar()
{
    global $inCodSubDivisaoFuncao,$inCodFuncao,$inCodEspecialidadeFuncao;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $inCodSubDivisaoFuncao );
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $inCodFuncao );
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
    $inContador = 1;
    if ( $rsEspecialidade->getNumLinhas() > 0 ) {
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            if ($inCodEspecialidade == $inCodEspecialidadeFuncao) {
                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $js .= "f.stEspecialidadeFuncao.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."','".$stSelected."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
        $js .= "f.inCodEspecialidadeFuncao.value = '".$inCodEspecialidadeFuncao."'; \n";
    } else {
        $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
    }
    $stJs .= $js;

    return $stJs;
}

function preencheEspecialidadeFuncao()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);                                             \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');                \n";
    $js .= "f.inCodEspecialidadeFuncao.value = '';                                              \n";

    if ($_REQUEST["inCodFuncao"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_REQUEST["inCodSubDivisaoFuncao"] );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $_REQUEST["inCodFuncao"] );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
        $inContador = 1;
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            $js .= "f.stEspecialidadeFuncao.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
    }
    if ($_POST['stAcao'] == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $stJs .= "f.dtDataAlteracaoFuncao.value = '$dtCompetenciaFinal';\n";
    }

    $js .= preencheInformacoesSalariais();

    if ($_POST['stAcao'] == "alterar") {
        $js .= "f.dtDataAlteracaoFuncao.readOnly = false;                                 \n";
        $js .= "f.dtDataAlteracaoFuncao.style.color = '#000000';                          \n";
        $js .= "f.dtDataAlteracaoFuncao.value = '$dtCompetenciaFinal';  \n";
    }

    return $js;
}

function preencheEspecialidade()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();

    $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";

    //Limpa componentes da função

    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);\n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";

    $js = limpaInformacoesSalariais();

    if ($_REQUEST["inCodCargo"]) {

        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_REQUEST["inCodSubDivisao"] );
        $inCodCargo = ($_REQUEST["inCodCargo"]<>'') ? $_REQUEST["inCodCargo"] : $_REQUEST["inHdnCodCargo"];
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $inCodCargo );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->consultaCargoPadrao( $rsPadrao );
        $rsPadrao->addFormatacao( "valor", NUMERIC_BR );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );

        $js .= "f.inCodPadrao.value = '".$rsPadrao->getCampo('cod_padrao')."'; \n";
        $js .= "f.stPadrao.value = '".$rsPadrao->getCampo('cod_padrao')."'; \n";
        $js .= "f.stHorasMensais.value = '".$rsPadrao->getCampo('horas_mensais')."'; \n";
        $js .= "f.stHorasSemanais.value = '".$rsPadrao->getCampo('horas_semanais')."'; \n";
        $js .= "limpaSelect(f.stEspecialidadeCargo,0); \n";
        $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
        $js .= "f.stEspecialidadeCargo[0] = new Option('Selecione','','selected');\n";
        $js .= "limpaSelect(f.stEspecialidadeFuncao,0); \n";
        $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
        $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','','selected');\n";
        $inContador = 1;
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            $js .= "f.stEspecialidadeCargo.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."'); \n";
            $js .= "f.stEspecialidadeFuncao.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
        $js .= preencheInformacoesSalariais( $_REQUEST["inCodCargo"] );
    }
    if ($_POST['stAcao'] == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $stJs .= "f.dtDataAlteracaoFuncao.value = '$dtCompetenciaFinal';\n";
    }

    return $js;
}

function preencheEspecialidadeAlterar()
{
    global $inCodSubDivisao,$inCodCargo,$inCodEspecialidadeCargo;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    if ($inCodSubDivisao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $inCodSubDivisao );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $inCodCargo );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
        $inContador = 1;
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            if ($inCodEspecialidade == $inCodEspecialidadeCargo) {
                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $js .= "f.stEspecialidadeCargo.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."','".$stSelected."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
        $js .= "f.stEspecialidadeCargo.disabled = true;";
        $js .= "f.inCodEspecialidadeCargo.disabled = true;";
        $js .= "f.stEspecialidadeCargo.style.color = '#333333';";
        $js .= "f.inCodEspecialidadeCargo.style.color = '#333333';";

    } else {
        sistemaLegado::exibeAviso("Deve ser selecionada uma subdivisão."," "," ");
    }
    $stJs .= $js;

    return $stJs;
}

function preencheInformacoesSalariais($inCodFuncao = "", $inCodEspecialidadeFuncao = "", $stDataProgressao = "")
{
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php" );
    $inCodFuncao              = $inCodFuncao              ? $inCodFuncao              : $_POST["stFuncao"];
    $inCodEspecialidadeFuncao = $inCodEspecialidadeFuncao ? $inCodEspecialidadeFuncao : $_POST["stEspecialidadeFuncao"];
    $stDataProgressao         = $stDataProgressao         ? $stDataProgressao         : $_POST["dtDataProgressao"];
    $stHorasMensais           = "";
    $stHorasSemanais          = "";
    $inCodPadrao              = "";
    $inCodProgressao          = "";
    $nuSalario                = "";

    $js = limpaInformacoesSalariais();

    //Se posssui CodFuncao pode-se buscar pelo padrão e calcular o salário.
    if ($inCodFuncao) {
        $obRPessoalCargo = new RPessoalCargo;

        // Para ver se o cargo possui especialidades
        $obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalCargo->setCodCargo( $inCodFuncao );
        $obRPessoalCargo->addEspecialidade();
        $obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidades );

        //Se Cargo da função tem especialidade
        if ( $rsEspecialidades->getNumLinhas() > 0 ) {
            //Se está setado o cod da especialidade
            if ($inCodEspecialidadeFuncao) {
                $obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $inCodEspecialidadeFuncao );
                $obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsPadraoEspecialidade );
                $rsPadraoEspecialidade->addFormatacao( "horas_mensais" , NUMERIC_BR );
                $rsPadraoEspecialidade->addFormatacao( "horas_semanais", NUMERIC_BR );
                $stHorasMensais           = $rsPadraoEspecialidade->getCampo("horas_mensais");
                $stHorasSemanais          = $rsPadraoEspecialidade->getCampo("horas_semanais");
                $inCodPadrao              = $rsPadraoEspecialidade->getCampo("cod_padrao");
            }
        } else {
            //Cargo da função não tem especialidade
            $obRPessoalCargo->consultaCargoPadrao( $rsPadraoCargo, $boTransacao );
            $rsPadraoCargo->addFormatacao( "horas_mensais" , NUMERIC_BR );
            $rsPadraoCargo->addFormatacao( "horas_semanais", NUMERIC_BR );
            $stHorasMensais           = $rsPadraoCargo->getCampo("horas_mensais");
            $stHorasSemanais          = $rsPadraoCargo->getCampo("horas_semanais");
            $inCodPadrao              = $rsPadraoCargo->getCampo("cod_padrao");
        }

        //Este if garante que se o cargo tem especialidade o código dele esteja setado ou não tenha especialidade
        if ( !( $rsEspecialidades->getNumLinhas() > 0) || ( ($rsEspecialidades->getNumLinhas() > 0) && $inCodEspecialidadeFuncao ) ) {
            //O valor aqui setado será usado na função calculaSalario,
            // quando não for passado nenhum parametro de horas mensais a ela
            $_REQUEST["stHorasMensais"] = $stHorasMensais;
            $js .= preencheProgressao($inCodPadrao);

            $js .= "f.stHorasMensais.value = '".$stHorasMensais."'; \n";
            $js .= "f.stHorasSemanais.value = '".$stHorasSemanais."'; \n";
            $js .= "f.inCodPadrao.value = '".$inCodPadrao."'; \n";
            $js .= "f.stPadrao.value = '".$inCodPadrao."'; \n";
        }
    }
    if ($_POST['stAcao'] == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $js .= "f.dtDataAlteracaoFuncao.readOnly = false;                                 \n";
        $js .= "f.dtDataAlteracaoFuncao.style.color = '#000000';                          \n";
        $js .= "f.dtDataAlteracaoFuncao.value = '$dtCompetenciaFinal';  \n";
    }

    return $js;
}

function limpaInformacoesSalariais()
{
    $js .= "f.inCodPadrao.value = '';      \n";
    $js .= "f.stPadrao.value = '';         \n";
    $js .= "f.stHorasMensais.value = '';   \n";
    $js .= "f.stHorasSemanais.value = '';  \n";
    $js .= "f.inSalario.value = '';        \n";
    $js .= "d.getElementById('stlblProgressao').innerHTML = '&nbsp;'; \n";
    $js .= "f.inCodProgressao.value = ''; \n";

    return $js;
}

function preenchePreEspecialidadeFuncao()
{
    if ($_POST['inCodFuncao'] == $_POST['inCodCargo']) {
        $stJs .= "f.inCodEspecialidadeFuncao.value = f.inCodEspecialidadeCargo.value; \n";
        $stJs .= "f.stEspecialidadeFuncao.value    = f.inCodEspecialidadeCargo.value; \n";
    }
    $stJs .= preencheInformacoesSalariais("", $_POST['inCodEspecialidadeCargo'], "");

    return $stJs;
}

function buscaLocal()
{
    GLOBAL $inCodLocal;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $stText = "inCodLocal";
    $stSpan = "stLocal";
    if ($_REQUEST[ $stText ] != "" or $inCodLocal != "") {
        $obRPessoalServidor->roUltimoContratoServidor->obROrganogramaLocal->setCodLocal( ($_REQUEST['inCodLocal']) ? $_REQUEST['inCodLocal'] : $inCodLocal );
        $obRPessoalServidor->roUltimoContratoServidor->obROrganogramaLocal->listarLocal( $rsRecordSet );
        $stNull = "&nbsp;";
        if ( $rsRecordSet->getNumLinhas() <= 0) {
            $Js .= 'f.'.$stText.'.value = "";';
            $Js .= 'f.'.$stText.'.focus();';
            $Js .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $Js .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
            $Js .= 'd.getElementById("'.$stSpan.'").innerHTML             = "'.($rsRecordSet->getCampo('descricao')?$rsRecordSet->getCampo('descricao'):$stNull ).'";';
        }
    } else {
        $Js .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }
    $stJs .= $Js;

    return $stJs;
}

function preencheAgenciaBancaria()
{
    global $inCodAgenciaFGTS,$inCodBancoFGTS;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $js .= "limpaSelect(f.stAgenciaBancoFGTS,0); \n";
    $js .= "f.inCodAgenciaFGTS.value = ''; \n";
    $js .= "f.stAgenciaBancoFGTS[0] = new Option('Selecione','','selected');\n";
    if ($_POST["inCodBancoFGTS"] or $inCodBancoFGTS) {
        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaFGTS->obRMONBanco->setNumBanco(($_REQUEST["inCodBancoFGTS"]) ? $_REQUEST["inCodBancoFGTS"]  : $inCodBancoFGTS);
        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaFGTS->listarAgencia($rsAgenciaBancaria,$boTransacao);
        $inContador = 1;
        while ( !$rsAgenciaBancaria->eof() ) {
            $inCodAgenciaBancaria  = $rsAgenciaBancaria->getCampo( "num_agencia" );
            $stAgenciaBancaria     = $rsAgenciaBancaria->getCampo( "nom_agencia" );
            if ($inCodAgenciaBancaria == $inCodAgenciaFGTS) {
                $stSelected = "selected";
                $js .= "f.inCodAgenciaFGTS.value = '$inCodAgenciaBancaria'; \n";
            } else {
                $stSelected = "";
            }
            $js .= "f.stAgenciaBancoFGTS.options[$inContador] = new Option('".$stAgenciaBancaria."','".$inCodAgenciaBancaria."','".$stSelected."'); \n";
            $inContador++;
            $rsAgenciaBancaria->proximo();
        }
    } elseif ($_POST["inCodBancoFGTS"] == '0') {
        $js .= "f.inCodAgenciaFGTS.value = '0'; \n";
        $js .= "f.stAgenciaBancoFGTS.options[0] = new Option('Não informado','0'); \n";

    }

    return $js;
}

function preencheAgenciaBancariaSalario()
{
    global $inCodAgenciaSalario,$inCodBancoSalario;
    $obRPessoalServidor = new RPessoalServidor;
    $js .= "limpaSelect(f.stAgenciaSalario,0); \n";
    $js .= "f.inCodAgenciaSalario.value = ''; \n";
    $js .= "f.stAgenciaSalario[0] = new Option('Selecione','');\n";

    if ($_POST["inCodBancoSalario"] or $inCodBancoSalario) {
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaSalario->obRMONBanco->setNumBanco( ($_REQUEST["inCodBancoSalario"]) ? $_REQUEST["inCodBancoSalario"] : $inCodBancoSalario );
        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaSalario->listarAgencia($rsAgenciaBancaria, $boTransacao);
        $inContador = 1;
        while ( !$rsAgenciaBancaria->eof() ) {
            $inCodAgenciaBancaria  = $rsAgenciaBancaria->getCampo( "num_agencia" );
            $stAgenciaBancaria     = $rsAgenciaBancaria->getCampo( "nom_agencia" );
            if ($inCodAgenciaBancaria == $inCodAgenciaSalario) {
                $stSelected = "selected";
                $js .= "f.inCodAgenciaSalario.value = '".$inCodAgenciaBancaria."'; \n";
                $inCodAgenciaBancariaSelect = $inCodAgenciaBancaria;
            } else {
                $stSelected = "";
            }
            $js .= "f.stAgenciaSalario.options[".$inContador."] = new Option('".$stAgenciaBancaria."','".$inCodAgenciaBancaria."'); \n";
            $inContador++;
            $rsAgenciaBancaria->proximo();
        }
        $js .= "f.stAgenciaSalario.value = '".$inCodAgenciaBancariaSelect."'; \n";
    } elseif ($_POST["inCodBancoSalario"] == '0') {
        $js .= "f.inCodAgenciaSalario.value = '0'; \n";
        $js .= "f.stAgenciaSalario.options[0] = new Option('Não informado','0'); \n";

    }

    return $js;
}

function habilita()
{
    global $inCodFormaPagamento;
    if ($_POST['stFormaPagamento'] == '3' or $inCodFormaPagamento == 3) {
        $stJs .= "f.inCodBancoSalario.disabled             = false;\n";
        $stJs .= "f.stBancoSalario.disabled                = false;\n";
        $stJs .= "f.inCodAgenciaSalario.disabled           = false;\n";
        $stJs .= "f.stAgenciaSalario.disabled              = false;\n";
        $stJs .= "f.inContaSalario.disabled                = false;\n";
    } else {
        $stJs .= "f.inCodBancoSalario.disabled             = true;\n";
        $stJs .= "f.stBancoSalario.disabled                = true;\n";
        $stJs .= "f.inCodAgenciaSalario.disabled           = true;\n";
        $stJs .= "f.stAgenciaSalario.disabled              = true;\n";
        $stJs .= "f.inContaSalario.disabled                = true;\n";
    }

    return $stJs;
}

function buscaSindicato()
{
    GLOBAL $inNumCGMSindicato;
    $obRPessoalServidor = new RPessoalServidor;
    $stText = "inNumCGMSindicato";
    $stSpan = "stNomSindicato";
    if ($_REQUEST[ $stText ] != "" or $inNumCGMSindicato != "") {
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoSindicato->obRCGM->setNumCGM( ($_REQUEST[ $stText ] != "") ? $_REQUEST[ $stText ] : $inNumCGMSindicato );
        $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoSindicato->consultarSindicato( $rsSindicato );
        $stNull = "&nbsp;";
        if ( $rsSindicato->getNumLinhas() <= 0 ) {
            $Js .= 'f.'.$stText.'.value = "";';
            $Js .= 'f.'.$stText.'.focus();';
            $Js .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $Js .= "alertaAviso('@Valor inválido. (".$_REQUEST[$stText].")','form','erro','".Sessao::getId()."');";
        } else {
            $Js .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsSindicato->getCampo('nom_cgm')?$rsSindicato->getCampo('nom_cgm'):$stNull ).'";';
            if (!$_POST["dtDataBase"]) {
                $Js .= 'f.dtDataBase.value = "'.$rsSindicato->getCampo('data_base').'";';
            }
        }
    } else {
        $Js .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }
    $stJs .= $Js;

    return $stJs;
}

function validaDataBase()
{
    if ($_POST['dtDataBase'] < 1 or $_POST['dtDataBase'] > 12) {
        $stMensagem = 'O valor do campo Data-base deve estar entre 1 e 12';
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');   \n";
        $stJs .= "f.dtDataBase.value='';                                        \n";
    }

    return $stJs;
}

function preencheTurnos()
{
    global $inCodGradeHorario;
    $inCodGradeHorario = (!empty($inCodGradeHorario))?$inCodGradeHorario:$_REQUEST['inCodGradeHorario'];

    $obRPessoalServidor = new RPessoalServidor;
    $rsFaixaTurno = new RecordSet();
    if ($inCodGradeHorario) {
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade( $inCodGradeHorario );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->addFaixaTurno();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->roRPessoalFaixaTurno->listarFaixaTurno( $rsFaixaTurno,$boTransacao );
    }

    $obLista = new Lista;
    $obLista->setTitulo( "Turnos" );
    $obLista->setRecordSet( $rsFaixaTurno );
    $obLista->setMostraPaginacao( false );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Dia" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Horário de Entrada" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Horário de Saída" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Horário de Entrada2" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Horário de Saída2" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_dia");
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "hora_entrada" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "hora_saida" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "hora_entrada_2" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "hora_saida_2" );
        $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnTurnos').innerHTML = '".$stHtml."';";

    return $stJs;
}

function preenchePortariaNomeacao()
{
    global $inCodNorma;

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma($inCodNorma);
    $obRPessoalServidor->roUltimoContratoServidor->obRNorma->listar($rsNorma);
    include_once(CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php");
    $obTTipoNorma = new TTipoNorma();
    $stFiltro = " WHERE cod_tipo_norma = ".$rsNorma->getCampo("cod_tipo_norma");
    $obTTipoNorma->recuperaTodos($rsTipoNorma,$stFiltro);
    $stJs .= "document.getElementById('stCodNorma').value = '".trim($rsNorma->getCampo("num_norma_exercicio"))."';\n";
    $stJs .= "document.getElementById('stNorma').innerHTML= '".trim($rsTipoNorma->getCampo("nom_tipo_norma"))." ".$rsNorma->getCampo("num_norma")."/".$rsNorma->getCampo("exercicio")." - ".trim($rsNorma->getCampo("nom_norma"))."';\n";

    return $stJs;
}

function validarVigenciaSalario()
{
    $stValida = comparaComDataNascimento("dtVigenciaSalario","Vigência do Salário");
    if ($stValida != "") {
        $stJs .= $stValida;
    } else {
        if ( sistemaLegado::comparaDatas(Sessao::read('dtVigenciaSalario'),$_POST['dtVigenciaSalario']) ) {
            $stMensagem = "A vigência deve ser posterior a ".Sessao::read('dtVigenciaSalario');
            $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
            $stJs .= "f.dtVigenciaSalario.value = '".Sessao::read('dtVigenciaSalario')."';";
        }
    }

    return $stJs;
}

function preencherSpanCedencia()
{
    $rsAdidoCedido = Sessao::read('rsAdidoCedido');

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $arCompetencia = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
    $dtCompetencia = $arCompetencia[1].$arCompetencia[2];

    $arDataInicial = explode("-",$rsAdidoCedido->getCampo("dt_inicial"));
    $dtDataInicial = $arDataInicial[1].$arDataInicial[0];

    $arDataFinal = explode("-",$rsAdidoCedido->getCampo("dt_final"));
    $dtDataFinal = $arDataFinal[1].$arDataFinal[0];

    if ($dtCompetencia >= $dtDataInicial AND $dtCompetencia <= $dtDataFinal) {

        if ( $rsAdidoCedido->getNumLinhas() > 0 ) {
            include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
            $obTCGM = new TCGM;
            $stFiltro = " WHERE numcgm = ".$rsAdidoCedido->getCampo("cgm_cedente_cessionario");
            $obTCGM->recuperaTodos($rsCGM,$stFiltro);

            $stRotuloCGM       = ( $rsAdidoCedido->getCampo("tipo_cedencia") == 'a' ) ? "CGM Órgão/Entidade Cedente" : "CGM Órgão/Entidade Cessionário";
            $stIndicativoOnus = ($rsAdidoCedido->getCampo("indicativo_cedencia") == "c") ? "Cedente" : "Cessionário";
            $stValueCGM        = $rsAdidoCedido->getCampo("cgm_cedente_cessionario")."-".$rsCGM->getCampo("nom_cgm");

               $obLblDataInicialAto = new Label();
            $obLblDataInicialAto->setRotulo("Data Inicial do Ato");
            $obLblDataInicialAto->setValue($rsAdidoCedido->getCampo("data_inicial"));

            $obLblDataFinalAto = new Label();
            $obLblDataFinalAto->setRotulo("Data Final do Ato");
            $obLblDataFinalAto->setValue($rsAdidoCedido->getCampo("data_final"));

            $obLblCgmOrgaoEntidade = new Label();
            $obLblCgmOrgaoEntidade->setRotulo($stRotuloCGM);
            $obLblCgmOrgaoEntidade->setValue($stValueCGM);

            $obLblIndicativoOnus = new Label();
            $obLblIndicativoOnus->setRotulo("Indicativo de Ônus");
            $obLblIndicativoOnus->setValue($stIndicativoOnus);

            $obFormulario = new Formulario();
            $obFormulario->addTitulo("Informações de Cedência");
            $obFormulario->addComponente($obLblDataInicialAto);
            $obFormulario->addComponente($obLblDataFinalAto);
            $obFormulario->addComponente($obLblCgmOrgaoEntidade);
            $obFormulario->addComponente($obLblIndicativoOnus);
            $obFormulario->montaInnerHTML();
            $stJs .= "d.getElementById('spnCedencia').innerHTML = '".$obFormulario->getHTML()."'; \n";
        }

        return $stJs;
    }
}

function preencherSpanAposentadoria()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoria.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTPessoalContratoServidor = new TPessoalContratoServidor();
    $obTPessoalAposentadoria = new TPessoalAposentadoria();
    $stFiltro = " AND aposentadoria.cod_contrato = ".$_GET['inCodContrato'];
    $obTPessoalAposentadoria->recuperaRelacionamento($rsAposentadoria,$stFiltro);
    $obTPessoalContratoServidor->setDado("cod_contrato",$_GET['inCodContrato']);
    $obTPessoalContratoServidor->recuperaPorChave($rsContrato);
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $arCompetencia = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
    $dtCompetencia = $arCompetencia[2]."-".$arCompetencia[1];
    $arConcessao   = explode("/",$rsAposentadoria->getCampo("data_concessao"));
    $dtConcessao   = $arConcessao[2]."-".$arConcessao[1];
    $stHtml = "";
    if ( $rsAposentadoria->getNumLinhas() == 1 and $dtConcessao <= $dtCompetencia ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacao.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalEnquadramento.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoriaEncerramento.class.php");
        $obTPessoalClassificacao = new TPessoalClassificacao();
        $obTPessoalClassificacao->setDado("cod_classificacao",$rsAposentadoria->getCampo("cod_classificacao"));
        $obTPessoalClassificacao->recuperaPorChave($rsClassificacao);
        $obTPessoalEnquadramento = new TPessoalEnquadramento();
        $obTPessoalEnquadramento->setDado("cod_enquadramento",$rsAposentadoria->getCampo("cod_enquadramento"));
        $obTPessoalEnquadramento->recuperaPorChave($rsEnquadramento);
        $obTPessoalAposentadoriaEncerramento = new TPessoalAposentadoriaEncerramento();
        $obTPessoalAposentadoriaEncerramento->setDado("cod_contrato",$rsAposentadoria->getCampo("cod_contrato"));
        $obTPessoalAposentadoriaEncerramento->setDado("timestamp",$rsAposentadoria->getCampo("timestamp"));
        $obTPessoalAposentadoriaEncerramento->recuperaPorChave($rsEncerramento);

        $obLblDataConcessao = new Label();
        $obLblDataConcessao->setRotulo("Data da Concessão do Benefício");
        $obLblDataConcessao->setValue($rsAposentadoria->getCampo("data_concessao"));
        $obLblDataConcessao->setId("stDataConcessao");

        $obLblClassificacao = new Label();
        $obLblClassificacao->setRotulo("Classificação Regra Aposentadoria");
        $obLblClassificacao->setValue($rsClassificacao->getCampo("nome_classificacao"));
        $obLblClassificacao->setId("stClassificacao");

        $obLblEnquadramento = new Label();
        $obLblEnquadramento->setRotulo("Enquadramento da Aposentadoria");
        $obLblEnquadramento->setValue($rsEnquadramento->getCampo("descricao"));
        $obLblEnquadramento->setId("stEnquadramento");

        $obLblTipo = new Label();
        $obLblTipo->setRotulo("Tipo de Reajuste");
        $obLblTipo->setValue($rsEnquadramento->getCampo("reajuste"));
        $obLblTipo->setId("stTipo");

        $obLblPercentual = new Label();
        $obLblPercentual->setRotulo("Percentual do Benefício Recebido em Folha");
        $obLblPercentual->setValue(number_format($rsAposentadoria->getCampo("percentual"),2,',','.')."%");
        $obLblPercentual->setId("stPercentual");

        $obLblDataEncerramento = new Label();
        $obLblDataEncerramento->setRotulo("Data de Encerramento");
        $obLblDataEncerramento->setValue($rsEncerramento->getCampo("dt_encerramento"));
        $obLblDataEncerramento->setId("stDataEncerramento");

        $obLblMotivo = new Label();
        $obLblMotivo->setRotulo("Motivo do Encerramento");
        $obLblMotivo->setValue($rsEncerramento->getCampo("motivo"));
        $obLblMotivo->setId("stMotivo");

        $obFormulario = new Formulario();
        $obFormulario->addTitulo( "Informações da Aposentadoria"                                    );
        $obFormulario->addComponente($obLblDataConcessao);
        $obFormulario->addComponente($obLblClassificacao);
        $obFormulario->addComponente($obLblEnquadramento);
        $obFormulario->addComponente($obLblTipo);
        $obFormulario->addComponente($obLblPercentual);
        $obFormulario->addComponente($obLblDataEncerramento);
        $obFormulario->addComponente($obLblMotivo);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnAposentadoria').innerHTML = '".$stHtml."'; \n";

    return $stJs;
}

function preencherSpanRescisao()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCasoCausa.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisao.class.php");
    $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
    $obTPessoalContratoServidorCasoCausa->setDado("cod_contrato",$_GET['inCodContrato']);
    $obTPessoalContratoServidorCasoCausa->recuperaPorChave($rsContrato);

    if ($rsContrato->getNumLinhas() > 0) {
        $obTPessoalCasoCausa = new TPessoalCasoCausa();
        $obTPessoalCasoCausa->setDado("cod_caso_causa",$rsContrato->getCampo("cod_caso_causa"));
        $obTPessoalCasoCausa->recuperaPorChave($rsCasoCausa);

        $obTPessoalCausaRescisao = new TPessoalCausaRescisao();
        $obTPessoalCausaRescisao->setDado("cod_causa_rescisao",$rsCasoCausa->getCampo("cod_causa_rescisao"));
        $obTPessoalCausaRescisao->recuperaPorChave($rsCausaRescisao);

        $obLblRescisao = new Label;
        $obLblRescisao->setName               ( "dtRescisao"           );
        $obLblRescisao->setRotulo             ( "Data Rescisão"         );
        $obLblRescisao->setValue              ( $rsContrato->getCampo("dt_rescisao")            );

        $obLblCausa = new Label();
        $obLblCausa->setRotulo("Causa");
        $obLblCausa->setValue($rsCausaRescisao->getCampo("num_causa")." - ".$rsCausaRescisao->getCampo("descricao"));

        $obFormulario = new Formulario();
        $obFormulario->addTitulo( "Informações da Rescisão"                                    );
        $obFormulario->addComponente($obLblRescisao);
        $obFormulario->addComponente($obLblCausa);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnRescisao').innerHTML = '".$stHtml."'; \n";

    return $stJs;
}

function buscaTipoAdmissao()
{
    $stHtml = "&nbsp;";
    if ($_POST['inCodTipoAdmissao']) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalTipoAdmissao.class.php");
        $obTPessoalTipoAdmissao = new TPessoalTipoAdmissao();
        $stFiltro = " WHERE cod_tipo_admissao = ".$_POST['inCodTipoAdmissao'];
        $obTPessoalTipoAdmissao->recuperaTodos($rsTipoAdmissao,$stFiltro);
        if ( $rsTipoAdmissao->getNumLinhas() < 0 ) {
            $stJs .= "f.inCodTipoAdmissao.value = ''; \n";
            $stMensagem = ' Código de tipo de admissão digitado está incorreto! ';
            $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
        }

        $stHtml = ($rsTipoAdmissao->getCampo("descricao") != "") ? $rsTipoAdmissao->getCampo("descricao") : $stHtml;
    }
    $stJs .= "d.getElementById('stTipoAdmissao').innerHTML = '".$stHtml."'; \n";

    return $stJs;
}

function buscaVinculoEmpregaticio()
{
    ;
    $stHtml = "&nbsp;";
    if ($_POST['inCodVinculoEmpregaticio']) {

        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalVinculoEmpregaticio.class.php");
        $obTPessoalVinculoEmpregaticio = new TPessoalVinculoEmpregaticio();
        $stFiltro = " WHERE cod_vinculo = ".$_POST['inCodVinculoEmpregaticio'];
        $obTPessoalVinculoEmpregaticio->recuperaTodos($rsVinculo,$stFiltro);
        $stHtml = ($rsVinculo->getCampo("descricao") != "") ? $rsVinculo->getCampo("descricao") : $stHtml;
        if ($rsVinculo->getCampo("descricao")=="") {
            $stMensagem = " Vínculo Empregatício não encontrado!";
            $stJs .= "f.inCodVinculoEmpregaticio.value = '';\n";
               $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
        }
    }
    $stJs .= "d.getElementById('stVinculoEmpregaticio').innerHTML = '".$stHtml."'; \n";

    return $stJs;
}

function buscaCategoria()
{
    $stHtml = "&nbsp;";
    if ($_POST['inCodCategoria'] != '') {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCategoria.class.php");
        $obTPessoalCategoria = new TPessoalCategoria();
        $stFiltro = " WHERE cod_categoria = ".$_POST['inCodCategoria'];
        $obTPessoalCategoria->recuperaTodos($rsCategoria,$stFiltro);

        if ( $rsCategoria->getNumLinhas() <= 0 || $_POST['inCodCategoria'] == '0') {
            $stJs .= "f.inCodCategoria.value = '';         \n";
            $stJs .= "f.inCodCategoria.focus();            \n";
            $stMensagem = " Código da categoria inválido! ";
            $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');";
        }
        $stHtml = ($rsCategoria->getCampo("descricao") != "") ? $rsCategoria->getCampo("descricao") : $stHtml;
    }
    $stJs .= "d.getElementById('stCategoria').innerHTML = '".$stHtml."'; \n";

    return $stJs;
}

function validaTipoAdmissao()
{
    $stValida = comparaComDataNascimento("dtDataNomeacao","Data de Nomeação");
    if ($stValida != "") {
        $stJs .= $stValida;
        $stJs .= "f.dtDataPosse.value    = '';\n";
   } else {
       if ($_POST['dtDataNomeacao'] != "" and $_POST['dtDataPosse'] == "") {
           $stJs .= "f.dtDataPosse.value = '".$_POST['dtDataNomeacao']."';     \n";
       }

       if ($_POST['dtDataNomeacao'] != "" and $_POST['dtDataPosse'] != "") {
           if ( sistemaLegado::comparaDatas($_POST['dtDataNomeacao'],$_POST['dtDataPosse']) ) {
               $stMensagem = "Data da posse (".$_POST['dtDataPosse'].") não pode ser anterior à data de nomeação(".$_POST['dtDataNomeacao'].")!";
               $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
               $stJs .= "f.dtDataPosse.value='';                                           \n";
               $stJs .= "f.dtDataPosse.focus();                                            \n";
           } else {
               $stJs .= preencheProgressao($_POST['inCodPadrao']);
           }
       }
       if ($_POST['stAcao'] == "alterar") {
           $stJs .= "f.dtDataAlteracaoFuncao.value = f.dtDataPosse.value;\n";
       }
   }

   return $stJs;
}
switch ($_POST["stCtrl"]) {
    case "validaDataPosse":
        $stJs .= validaDataPosse();
        break;
    case "validaDataAdmissao":
        $stJs .= validaDataAdmissao();
        break;
    case "preencheProgressao":
        $stJs .= preencheProgressao($_POST['inCodPadrao']);
        break;
    case "calculaSalario":
        $stJs .= calculaSalario($_POST['inCodPadrao'],$_POST['inCodProgressao']);
        break;
    case "validaDataExameMedico":
        $stJs .= validaDataExameMedico();
        break;
    case "preencheSubDivisao":
        $stJs .= preencheSubDivisao();
        break;
    case "preencheSubDivisaoFuncao":
        $stJs .= preencheSubDivisaoFuncao();
        break;
    case "preencheCargo":
        $stJs .= preencheCargo();
        break;
    case "preencheFuncao":
        $stJs .= preencheFuncao();
        break;
    case "preencheEspecialidade":
        $stJs .= preencheEspecialidade();
        break;
    case "preencheEspecialidadeFuncao":
        $stJs .= preencheEspecialidadeFuncao();
        break;
    case "preenchePreEspecialidadeFuncao":
        $stJs .= preenchePreEspecialidadeFuncao();
        break;
    case "buscaLocal":
        $stJs .= buscaLocal();
        break;
    case "preencheAgenciaBancaria":
        $stJs .= preencheAgenciaBancaria();
        break;
    case "preencheAgenciaBancariaSalario":
        $stJs .= preencheAgenciaBancariaSalario();
        break;
    case "habilita":
        $stJs .= habilita();
        break;
    case "buscaSindicato":
        $stJs .= buscaSindicato();
        break;
    case "validaDataBase":
        $stJs .= validaDataBase();
        break;
    case "preencheTurnos":
        $stJs .= preencheTurnos();
        break;
    case "preenchePortariaNomeacao":
        $stJs .= preenchePortariaNomeacao();
        break;
    case "preencheInformacoesSalariais":
        $stJs .= preencheInformacoesSalariais();
        break;
    case "validarVigenciaSalario":
        $stJs .= validarVigenciaSalario();
        break;
    case "validaDataAlteracaoFuncao":
        $stJs .= comparaComDataNascimento("dtDataAlteracaoFuncao","Data da Alteração da Função");
        break;
    case "validarDataFGTS":
        $stJs .= comparaComDataNascimento("dtDataFGTS","Data de Opção do FGTS");
        break;
    case "validarDataValidade":
        $stJs .= comparaComDataNascimento("dtDataValidadeConselho","Data de Validade");
        break;
    case "buscaTipoAdmissao":
        $stJs .= buscaTipoAdmissao();
        break;
    case "buscaVinculoEmpregaticio":
        $stJs .= buscaVinculoEmpregaticio();
        break;
    case "buscaCategoria":
        $stJs .= buscaCategoria();
        break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>

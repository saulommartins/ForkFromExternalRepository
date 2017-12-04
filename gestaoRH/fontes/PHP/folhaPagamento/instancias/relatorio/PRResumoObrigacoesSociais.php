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
    * Processamento
    * Data de Criação : 13/08/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * Casos de uso: uc-04.05.64
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "ResumoObrigacoesSociais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$preview = new PreviewBirt(4,27,5);
$preview->setVersaoBirt( '2.5.0' );
$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLResumoObrigacoesSociais.php");
$preview->setTitulo('Resumo Obrigações Sociais');
$preview->setNomeArquivo('resumoObrigacoesSociais');
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("stTipoFiltro", $_POST["stTipoFiltro"]);

//periodo de movimentação
$inMesFinal =( $_POST["inCodMes"]<10 ) ? "0".$_POST["inCodMes"]:$_POST["inCodMes"];
$dtCompetenciaFinal = $inMesFinal."/".$_POST["inAno"];
$stFiltro = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetenciaFinal."'";
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentaco,$stFiltro);

if (!$rsPeriodoMovimentaco->eof()) {
    $preview->addParametro("inCodPeriodoMovimentacao", $rsPeriodoMovimentaco->getCampo("cod_periodo_movimentacao"));
    $preview->addParametro("stCompetencia", $dtCompetenciaFinal);
}
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidencia.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaPrevidencia.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaRegimeRat.class.php");
$obTFolhaPagamentoPrevidencia = new TFolhaPagamentoPrevidencia();
$obTFolhaPagamentoPrevidenciaPrevidencia = new TFolhaPagamentoPrevidenciaPrevidencia();
$obTFolhaPagamentoPrevidenciaRegimeRat = new TFolhaPagamentoPrevidenciaRegimeRat();
$stFiltro = " AND previdencia_previdencia.cod_previdencia = ".$_POST["inCodPrevidencia"];
$obTFolhaPagamentoPrevidenciaPrevidencia->recuperaRelacionamento($rsPrevidenciaPrevidencia,$stFiltro);
$stFiltro = " AND previdencia_regime_rat.cod_previdencia = ".$_POST["inCodPrevidencia"];
$obTFolhaPagamentoPrevidenciaRegimeRat->recuperaRelacionamento($rsRat,$stFiltro);
$stFiltro = " WHERE cod_previdencia = ".$_POST["inCodPrevidencia"];
$obTFolhaPagamentoPrevidencia->recuperaTodos($rsPrevidencia,$stFiltro);

$preview->addParametro("stPrevidencia", $rsPrevidenciaPrevidencia->getCampo("descricao"));
$preview->addParametro("inCodPrevidencia", $rsPrevidenciaPrevidencia->getCampo("cod_previdencia"));
$preview->addParametro("inCodRegimePrevidencia", $rsPrevidencia->getCampo("cod_regime_previdencia"));
// $preview->addParametro("aliquota_rat", ($rsRat->getCampo("aliquota_rat") == "") ? 0 : str_replace('.',',',$rsRat->getCampo("aliquota_rat")) );
$preview->addParametro("aliquota_rat", ($rsRat->getCampo("aliquota_rat") == "") ? 0 : $rsRat->getCampo("aliquota_rat") );
$preview->addParametro("aliquota_fap", ($rsRat->getCampo("aliquota_fap") == "") ? 0 : $rsRat->getCampo("aliquota_fap") );
//$preview->addParametro("aliquota_patronal", str_replace('.',',',$rsPrevidenciaPrevidencia->getCampo("aliquota")));
$preview->addParametro("aliquota_patronal", $rsPrevidenciaPrevidencia->getCampo("aliquota"));

//filtrar por folha complementar
$inCodComplementar = ($_POST["inCodComplementar"]!="")?$_POST["inCodComplementar"]:0;
$inCodConfiguracao = $_POST["inCodConfiguracao"];

$preview->addParametro("inCodConfiguracao", $inCodConfiguracao);
$preview->addParametro("inCodComplementar", $inCodComplementar);
$preview->addParametro("ordenacao", $_POST["stOrdenacao"]);

$inCodAtributo = 0;
$inCodTipoAtributo = 0;
$stCodigos = "";

switch ($_POST["stTipoFiltro"]) {
    case "lotacao_grupo":
        $stCodigos = trim(implode(",",$_POST["inCodLotacaoSelecionados"]));
        break;
    case "local_grupo":
        $stCodigos = trim(implode(",",$_POST["inCodLocalSelecionados"]));
        break;
    case "sub_divisao_grupo":
        $stCodigos = trim(implode(",",$_POST["inCodSubDivisaoSelecionados"]));
        break;
    case "atributo_servidor_grupo":
    case "atributo_pensionista_grupo":
        $inCodAtributo = $_POST["inCodAtributo"];
        $inCodCadastro = $_POST["inCodCadastro"];
        $stNome = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array($_POST[$stNome."_Selecionados"])) {
            $stCodigos = implode(",",$_POST[$stNome."_Selecionados"]);
            $boAtributoMultiplo = 1;
        } else {
            $stCodigos = pg_escape_string($_POST[$stNome]);
            $boAtributoMultiplo = 0;
        }

        //Recupera o nome e o tipo do atributo
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php");
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
        $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico();

        $rsAtributoDinamico = new RecordSet();
        $obTAdministracaoAtributoDinamico->setDado("cod_modulo",   22);
        $obTAdministracaoAtributoDinamico->setDado("cod_cadastro", $inCodCadastro);
        $obTAdministracaoAtributoDinamico->setDado("cod_atributo", $inCodAtributo);
        $obTAdministracaoAtributoDinamico->recuperaPorChave($rsAtributoDinamico);

        $stNomeAtributo    = $rsAtributoDinamico->getCampo("nom_atributo");
        $inCodTipoAtributo = $rsAtributoDinamico->getCampo("cod_tipo");
        break;
}

$preview->addParametro( "stSituacao", $_POST["stSituacao"]);
$preview->addParametro( "boQuebrarPagina", ($_POST["boQuebrar"] == "true") ? "true" : "false");
$preview->addParametro( "boAgrupar", ($_POST["boAgrupar"] == "true") ? "true" : "false");
$preview->addParametro( "stCodigos", $stCodigos);
$preview->addParametro( "inCodAtributo", $inCodAtributo);
$preview->addParametro( "inCodTipoAtributo", $inCodTipoAtributo);
$preview->preview();

?>

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
    * Página de Processamento do Estagiário
    * Data de Criação: 03/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.07.01

    $Id: PRManterEstagiario.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php"                             );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagioConta.class.php"                        );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagioLocal.class.php"                        );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagioBolsa.class.php"                        );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioValeRefeicao.class.php"                        );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioValeTransporte.class.php"                        );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiario.class.php"                                    );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadoraEstagio.class.php"                 );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioAtributoEstagiarioEstagio.class.php"                     );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php"                                              );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php"                                            );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                                         );

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterEstagiario";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTEstagioEstagiarioEstagio             = new TEstagioEstagiarioEstagio();
$obTEstagioEstagiarioEstagioConta        = new TEstagioEstagiarioEstagioConta();
$obTEstagioEstagiarioEstagioLocal        = new TEstagioEstagiarioEstagioLocal();
$obTEstagioEstagiario                    = new TEstagioEstagiario();
$obTEstagioEstagiarioValeRefeicao        = new TEstagioEstagiarioValeRefeicao();
$obTEstagioEstagiarioValeTransporte      = new TEstagioEstagiarioValeTransporte();
$obTEstagioEntidadeIntermediadoraEstagio = new TEstagioEntidadeIntermediadoraEstagio();
$obTEstagioAtributoEstagiarioEstagio     = new TEstagioAtributoEstagiarioEstagio();
$obTEstagioEstagiarioEstagioBolsa        = new TEstagioEstagiarioEstagioBolsa();
$obTEstagioEstagiarioEstagio->obTEstagioEstagiario = &$obTEstagioEstagiario;
$obTEstagioEstagiarioEstagioConta->obTEstagioEstagiarioEstagio = &$obTEstagioEstagiarioEstagio;
$obTEstagioEstagiarioEstagioLocal->obTEstagioEstagiarioEstagio = &$obTEstagioEstagiarioEstagio;
$obTEstagioEstagiarioEstagioBolsa->obTEstagioEstagiarioEstagio = &$obTEstagioEstagiarioEstagio;
$obTEstagioEntidadeIntermediadoraEstagio->obTEstagioEstagiarioEstagio = &$obTEstagioEstagiarioEstagio;
$obTEstagioAtributoEstagiarioEstagio->obTEstagioEstagiarioEstagio = &$obTEstagioEstagiarioEstagio;
$obTEstagioEstagiarioValeRefeicao->obTEstagioEstagiarioEstagioBolsa = &$obTEstagioEstagiarioEstagioBolsa;
$obTEstagioEstagiarioValeTransporte->obTEstagioEstagiarioEstagioBolsa = &$obTEstagioEstagiarioEstagioBolsa;
$obRCadastroDinamico                        = new RCadastroDinamico;
$obRCadastroDinamico->setPersistenteValores ( new TEstagioAtributoEstagiarioEstagio );

switch ($stAcao) {
    case "incluir":

        Sessao::setTrataExcecao(true);

        $obTEstagioEstagiario->setDado("numcgm",$_POST['inCGM']);
        $obTEstagioEstagiario->setDado("nom_pai",$_POST['stNomePai']);
        $obTEstagioEstagiario->setDado("nom_mae",$_POST['stNomeMae']);
        $obTEstagioEstagiario->recuperaPorChave($rsPaiMae);

        if ( $rsPaiMae->getNumLinhas() > 0 ) {
            $obTEstagioEstagiario->alteracao();
        } else {
            $obTEstagioEstagiario->inclusao();
        }

        $obTEstagioEstagiarioEstagio->setDado("numero_estagio",$_POST['inCodEstagio']);
        $obTEstagioEstagiarioEstagio->setDado("cgm_instituicao_ensino",$_POST['inNumCGMInstituicao']);
        $obTEstagioEstagiarioEstagio->setDado("cod_grade",$_POST['inCodGradeHorario']);
        $obTEstagioEstagiarioEstagio->setDado("cod_curso",$_POST['inCodCurso']);
        $obTEstagioEstagiarioEstagio->setDado("vinculo_estagio",$_POST['stVinculo']);
        $obTEstagioEstagiarioEstagio->setDado("dt_inicio",$_POST['dtInicioEstagio']);
        $obTEstagioEstagiarioEstagio->setDado("dt_final",$_POST['dtFimEstagio']);
        $obTEstagioEstagiarioEstagio->setDado("dt_renovacao",$_POST['dtRenovacaoEstagio']);
        $obTEstagioEstagiarioEstagio->setDado("funcao",$_POST['stFuncao']);
        $obTEstagioEstagiarioEstagio->setDado("objetivos",$_POST['stObjetivo']);
        $obTEstagioEstagiarioEstagio->setDado("cod_orgao",$_POST["hdnUltimoOrgaoSelecionado"]);
        $obTEstagioEstagiarioEstagio->setDado("ano_semestre",$_POST['stAnoSemestre']);
        $obTEstagioEstagiarioEstagio->inclusao();

        $obTEstagioEstagiarioEstagio->recuperaPorChave($rsEstagio);

        $ValorBolsa = str_replace( ',' , '', $_POST['nuValorBolsa'] );
        $ValorBolsa = str_replace( '.' , '', $ValorBolsa );
        $ValorBolsa = number_format($ValorBolsa / 100, 2, '.', '');

        if ($ValorBolsa != "") {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
            $obTEstagioEstagiarioEstagioBolsa->recuperaNow($stTimestamp);

            $obTEstagioEstagiarioEstagioBolsa->setDado("cgm_instituicao_ensino",$rsEstagio->getCampo("cgm_instituicao_ensino"));
            $obTEstagioEstagiarioEstagioBolsa->setDado("cgm_estagiario",$rsEstagio->getCampo("cgm_estagiario"));
            $obTEstagioEstagiarioEstagioBolsa->setDado("cod_curso",$rsEstagio->getCampo("cod_curso"));
            $obTEstagioEstagiarioEstagioBolsa->setDado("cod_estagio",$rsEstagio->getCampo("cod_estagio"));
            $obTEstagioEstagiarioEstagioBolsa->setDado("vl_bolsa",$ValorBolsa);
            $obTEstagioEstagiarioEstagioBolsa->setDado("faltas",$_POST["inDiasFaltas"]);
            $obTEstagioEstagiarioEstagioBolsa->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTEstagioEstagiarioEstagioBolsa->setDado("timestamp",$stTimestamp);
            $obTEstagioEstagiarioEstagioBolsa->setDado("vale_refeicao",$_POST["boVR"]);
        $obTEstagioEstagiarioEstagioBolsa->setDado("vale_transporte",$_POST["boVT"]);
            $obTEstagioEstagiarioEstagioBolsa->inclusao();

            if ($_POST["boVR"] == "true") {
                $obTEstagioEstagiarioValeRefeicao->setDado("quantidade",$_POST["inQuantVR"]);
                $obTEstagioEstagiarioValeRefeicao->setDado("vl_vale",$_POST["nuValorVR"]);
                $obTEstagioEstagiarioValeRefeicao->setDado("vl_desconto",($_POST["nuValorDescontoVR"]!="")?$_POST["nuValorDescontoVR"]:0.00);
                $obTEstagioEstagiarioValeRefeicao->setDado("timestamp",$stTimestamp);
                $obTEstagioEstagiarioValeRefeicao->inclusao();
            }

            if ($_POST["boVT"] == "true") {
                $obTEstagioEstagiarioValeTransporte->setDado("cod_tipo",$_POST["inTipoContagem"]);
                $obTEstagioEstagiarioValeTransporte->setDado("quantidade",$_POST["inQuantVT"]);
                $obTEstagioEstagiarioValeTransporte->setDado("valor_unitario",$_POST["nuValorVT"]);
                $obTEstagioEstagiarioValeTransporte->setDado("cod_calendar",($_POST["inCodCalendario"]!="")?$_POST["inCodCalendario"]:'null');
                $obTEstagioEstagiarioValeTransporte->setDado("timestamp",$stTimestamp);
                $obTEstagioEstagiarioValeTransporte->inclusao();
            }
        }

        if ($_POST['inCodLocal'] != "") {
            $obTEstagioEstagiarioEstagioLocal->setDado("cod_local",$_POST['inCodLocal']);
            $obTEstagioEstagiarioEstagioLocal->inclusao();
        }

        if ($_POST['stNumAgencia'] != "") {
            $obTMONBanco = new TMONBanco();
            $stFiltro = " WHERE num_banco = '".$_POST['inCodBanco']."'";
            $obTMONBanco->recuperaTodos($rsBanco,$stFiltro);
            $obTMONAgencia = new TMONAgencia();
            $stFiltro  = " WHERE num_agencia = '".$_POST['stNumAgenciaTxt']."'";
            $stFiltro .= "   AND cod_banco = ".$rsBanco->getCampo("cod_banco");
            $obTMONAgencia->recuperaTodos($rsAgencia,$stFiltro);

            $obTEstagioEstagiarioEstagioConta->setDado("cod_banco",$rsBanco->getCampo("cod_banco"));
            $obTEstagioEstagiarioEstagioConta->setDado("cod_agencia",$rsAgencia->getCampo("cod_agencia"));
            $obTEstagioEstagiarioEstagioConta->setDado("num_conta",$_POST['stContaCorrente']);
            $obTEstagioEstagiarioEstagioConta->inclusao();
        }
        if ($_POST['stVinculo'] == "e") {
            $obTEstagioEntidadeIntermediadoraEstagio->setDado("cgm_entidade",$_POST['inNumCGMEntidade']);
            $obTEstagioEntidadeIntermediadoraEstagio->inclusao();
        }
        $obAtributos = new MontaAtributos;
        $obAtributos->setName      ( "Atributo_" );
        $obAtributos->recuperaVetor( $arChave    );

        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/" , $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $arChaveAtributoCandidato =  array( "cod_estagio" => $rsEstagio->getCampo("cod_estagio"),
                                                 "numcgm" => $rsEstagio->getCampo("cgm_estagiario"),
                                              "cod_curso" => $rsEstagio->getCampo("cod_curso"),
                                 "cgm_instituicao_ensino" => $rsEstagio->getCampo("cgm_instituicao_ensino"));
        $obRCadastroDinamico->setCodCadastro(1);
        $obRCadastroDinamico->obRModulo->setCodModulo(39);
        $obRCadastroDinamico->setChavePersistenteValores($arChaveAtributoCandidato);
        $obRCadastroDinamico->salvarValores();
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgForm,"Estagiário ".$_POST['inCGM']."-".$_POST['stNomCGM'],"incluir","aviso", Sessao::getId(), "../");
    break;
    case "alterar":
        Sessao::setTrataExcecao(true);

        $obTEstagioEstagiario->setDado("numcgm",$_POST['inNumCGMEstagiario']);
        $obTEstagioEstagiario->setDado("nom_pai",$_POST['stNomePai']);
        $obTEstagioEstagiario->setDado("nom_mae",$_POST['stNomeMae']);
        $obTEstagioEstagiario->alteracao();
        $obTEstagioEstagiarioEstagio->setDado("cod_estagio",$_POST['inCodEstagio']);
        $obTEstagioEstagiarioEstagio->setDado("numero_estagio",$_POST['inCodigoEstagio']);
        $obTEstagioEstagiarioEstagio->setDado("cgm_instituicao_ensino",$_POST['inNumCGMInstituicao']);
        $obTEstagioEstagiarioEstagio->setDado("cod_grade",$_POST['inCodGradeHorario']);
        $obTEstagioEstagiarioEstagio->setDado("cod_curso",$_POST['inCodCurso']);
        $obTEstagioEstagiarioEstagio->setDado("vinculo_estagio",$_POST['stVinculo']);
        $obTEstagioEstagiarioEstagio->setDado("dt_inicio",$_POST['dtInicioEstagio']);
        $obTEstagioEstagiarioEstagio->setDado("dt_final",$_POST['dtFimEstagio']);
        $obTEstagioEstagiarioEstagio->setDado("dt_renovacao",$_POST['dtRenovacaoEstagio']);
        $obTEstagioEstagiarioEstagio->setDado("funcao",$_POST['stFuncao']);
        $obTEstagioEstagiarioEstagio->setDado("objetivos",$_POST['stObjetivo']);
        $obTEstagioEstagiarioEstagio->setDado("cod_orgao",$_POST["hdnUltimoOrgaoSelecionado"]);
        $obTEstagioEstagiarioEstagio->setDado("ano_semestre",$_POST['stAnoSemestre']);
        $obTEstagioEstagiarioEstagio->alteracao();

        $ValorBolsa = str_replace( ',' , '', $_POST['nuValorBolsa'] );
        $ValorBolsa = str_replace( '.' , '', $ValorBolsa );
        $ValorBolsa = number_format($ValorBolsa / 100, 2, '.', '');

        if ($ValorBolsa != "") {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
            $obTEstagioEstagiarioEstagioBolsa->recuperaNow($stTimestamp);

            $obTEstagioEstagiarioEstagioBolsa->setDado("cgm_instituicao_ensino",$_POST['inNumCGMInstituicao']);
            $obTEstagioEstagiarioEstagioBolsa->setDado("cgm_estagiario",$_POST['inNumCGMEstagiario']);
            $obTEstagioEstagiarioEstagioBolsa->setDado("cod_curso",$_POST['inCodCurso']);
            $obTEstagioEstagiarioEstagioBolsa->setDado("cod_estagio",$_POST['inCodEstagio']);
            $obTEstagioEstagiarioEstagioBolsa->setDado("vl_bolsa",$ValorBolsa);
            $obTEstagioEstagiarioEstagioBolsa->setDado("faltas",$_POST["inDiasFaltas"]);
            $obTEstagioEstagiarioEstagioBolsa->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTEstagioEstagiarioEstagioBolsa->setDado("timestamp",$stTimestamp);
            $obTEstagioEstagiarioEstagioBolsa->setDado("vale_refeicao",$_POST["boVR"]);
        $obTEstagioEstagiarioEstagioBolsa->setDado("vale_transporte",$_POST['boVT']);
            $obTEstagioEstagiarioEstagioBolsa->inclusao();

        if ($_POST["boVR"] == "true") {
                $obTEstagioEstagiarioValeRefeicao->setDado("quantidade",$_POST["inQuantVR"]);
                $obTEstagioEstagiarioValeRefeicao->setDado("vl_vale",$_POST["nuValorVR"]);
                $obTEstagioEstagiarioValeRefeicao->setDado("vl_desconto",($_POST["nuValorDescontoVR"]!="")?$_POST["nuValorDescontoVR"]:0.00);
                $obTEstagioEstagiarioValeRefeicao->setDado("timestamp",$stTimestamp);
                $obTEstagioEstagiarioValeRefeicao->inclusao();
            } else {
        $stFiltro  = " WHERE estagiario_estagio_bolsa.vale_refeicao = true            								   \n";
        $stFiltro .= " AND   estagiario_estagio_bolsa.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao")."  \n";
        $stFiltro .= " AND   estagiario_estagio_bolsa.cgm_estagiario = ".$obTEstagioEstagiarioEstagioBolsa->getDado("cgm_estagiario")."	           \n";
        $obTEstagioEstagiarioValeRefeicao->recuperaValeRefeicaoPorPeriodoMovimentacao($rsValeRefeicaoPorPeriodo,$stFiltro);

        if ($rsValeRefeicaoPorPeriodo->getCampo("cod_periodo_movimentacao") == $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao")) {
            while (!$rsValeRefeicaoPorPeriodo->eof()) {
            $obTEstagioEstagiarioValeRefeicao->setDado("timestamp",$rsValeRefeicaoPorPeriodo->getCampo("timestamp"));
            $obTEstagioEstagiarioValeRefeicao->exclusao();
            $rsValeRefeicaoPorPeriodo->proximo();
            }
        }

        $obTEstagioEstagiarioValeRefeicao->setDado("timestamp",$stTimestamp);
            }

            if ($_POST["boVT"] == "true") {
                $obTEstagioEstagiarioValeTransporte->setDado("cod_tipo",$_POST["inTipoContagem"]);
                $obTEstagioEstagiarioValeTransporte->setDado("quantidade",$_POST["inQuantVT"]);
                $obTEstagioEstagiarioValeTransporte->setDado("valor_unitario",$_POST["nuValorVT"]);
                $obTEstagioEstagiarioValeTransporte->setDado("cod_calendar",($_POST["inCodCalendario"]!="")?$_POST["inCodCalendario"]:'null');
                $obTEstagioEstagiarioValeTransporte->setDado("timestamp",$stTimestamp);
                $obTEstagioEstagiarioValeTransporte->inclusao();
            }
        }

        if ($_POST['inCodLocal'] != "") {
            $obTEstagioEstagiarioEstagioLocal->setDado("cod_local",$_POST['inCodLocal']);
            $obTEstagioEstagiarioEstagioLocal->exclusao();
            $obTEstagioEstagiarioEstagioLocal->inclusao();
        }

        if ($_POST['stNumAgencia'] != "") {
            $obTMONBanco = new TMONBanco();
            $stFiltro = " WHERE num_banco = '".$_POST['inCodBanco']."'";
            $obTMONBanco->recuperaTodos($rsBanco,$stFiltro);
            $obTMONAgencia = new TMONAgencia();
            $stFiltro  = " WHERE num_agencia = '".$_POST['stNumAgenciaTxt']."'";
            $stFiltro .= "   AND cod_banco = ".$rsBanco->getCampo("cod_banco");
            $obTMONAgencia->recuperaTodos($rsAgencia,$stFiltro);

            $obTEstagioEstagiarioEstagioConta->setDado("cod_banco",$rsBanco->getCampo("cod_banco"));
            $obTEstagioEstagiarioEstagioConta->setDado("cod_agencia",$rsAgencia->getCampo("cod_agencia"));
            $obTEstagioEstagiarioEstagioConta->setDado("num_conta",$_POST['stContaCorrente']);
            $obTEstagioEstagiarioEstagioConta->exclusao();
            $obTEstagioEstagiarioEstagioConta->inclusao();
        }

        $obAtributos = new MontaAtributos;
        $obAtributos->setName      ( "Atributo_" );
        $obAtributos->recuperaVetor( $arChave    );

        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
        $arChaves = preg_split( "/[^a-zA-Z0-9]/" , $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obTEstagioAtributoEstagiarioEstagio->setDado("cod_modulo",39);
            $obTEstagioAtributoEstagiarioEstagio->setDado("cod_cadastro",1);
            $obTEstagioAtributoEstagiarioEstagio->setDado("cod_atributo",$inCodAtributo);
            $obTEstagioAtributoEstagiarioEstagio->setDado("valor",$value);
            $obTEstagioAtributoEstagiarioEstagio->recuperaPorChave($rsAtributos);
            if ( $rsAtributos->getNumLinhas() == 1 ) {
                $obTEstagioAtributoEstagiarioEstagio->setDado("timestamp",$rsAtributos->getCampo("timestamp"));
                $obTEstagioAtributoEstagiarioEstagio->alteracao();
            }
            if ( $rsAtributos->getNumLinhas() == -1 ) {
                $obTEstagioAtributoEstagiarioEstagio->inclusao();
            }
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Estagiário ".$_POST['inNumCGMEstagiario']."-".$_POST['stNomCGM'],"alterar","aviso", Sessao::getId(), "../");
    break;
    case "excluir":
        Sessao::setTrataExcecao(true);
        $stFiltro = " WHERE cgm_estagiario = ".$_GET['inNumCGMEstagiario'];
        $obTEstagioEstagiarioEstagio->recuperaTodos($rsEstagios,$stFiltro);

        $obTEstagioEstagiarioEstagio->setDado("cgm_estagiario",$_GET['inNumCGMEstagiario']);
        $obTEstagioEstagiarioEstagio->setDado("cod_estagio",$_GET['inCodEstagio']);
        $obTEstagioEstagiarioEstagio->setDado("cgm_instituicao_ensino",$_GET['inNumCGMInstituicao']);
        $obTEstagioEstagiarioEstagio->setDado("cod_curso",$_GET['inCodCurso']);

        $obTEstagioEstagiarioEstagioLocal->exclusao();
        $obTEstagioEstagiarioEstagioConta->exclusao();
        $obTEstagioEntidadeIntermediadoraEstagio->exclusao();
        $obTEstagioAtributoEstagiarioEstagio->exclusao();
        $obTEstagioEstagiarioValeTransporte->exclusao();
        $obTEstagioEstagiarioValeRefeicao->exclusao();
        $obTEstagioEstagiarioEstagioBolsa->exclusao();
        $obTEstagioEstagiarioEstagio->exclusao();
        if ( $rsEstagios->getNumLinhas() <= 1 ) {
            $obTEstagioEstagiario->exclusao();
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Estagiário ".$_GET['inNumCGMEstagiario']."-".$_GET['stNomCGM'],"excluir","aviso", Sessao::getId(), "../");
    break;
}

?>

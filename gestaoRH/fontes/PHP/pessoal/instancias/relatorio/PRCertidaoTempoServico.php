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
    * Arquivo de Filtro
    * Data de Criação: 09/09/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php"                                         );

$stPrograma = "CertidaoTempoServico";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

function formataData($stData)
{
    $stRetorno = "";

    list($stDia, $stMes, $stAno) = explode("/", $stData);
    $stRetorno = trim($stAno)."-".trim($stMes)."-".trim($stDia);

    return $stRetorno;
}

$stFiltro  = "   AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao);
$stFiltro .= "   AND entidade.exercicio = '".Sessao::getExercicio()."'";
$obTEntidade = new TEntidade();
$obTEntidade->recuperaInformacoesCGMEntidade($rsEntidade, $stFiltro);

// Monta os parametros para o birt
$stCodigos = "";
switch ($_POST["stTipoFiltro"]) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        $arContratos = Sessao::read("arContratos");
        foreach ($arContratos as $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        $boArray = "false";
        break;
    case "lotacao":
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        $boArray = "false";
        break;
    case "local":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        $boArray = "false";
        break;
    case "atributo_servidor":
        $inCodAtributo = $_POST["inCodAtributo"];
        $stNomeCampoAtributo = "Atributo_".$_POST["inCodAtributo"]."_".$_POST["inCodCadastro"];
        if (is_array($_POST[$stNomeCampoAtributo."_Selecionados"])) {
            $stCodigos = implode(",",$_POST[$stNomeCampoAtributo."_Selecionados"]);
            $boArray = "true";
        } else {
            $stCodigos = $_POST[$stNomeCampoAtributo];
            $boArray = "false";
        }
        break;
}

// Monta PDF no Birt
switch (trim($_REQUEST["stTipoCertidao"])) {
    case "completa":
        $preview = new PreviewBirt(4,22,9);
        $preview->setVersaoBirt('2.5.0');
        $preview->setTitulo('Certidão Tempo Serviço - Completa');
        $preview->setNomeArquivo('certidaoTempoServicoCompleta');
    break;

    case "descritiva":
        $preview = new PreviewBirt(4,22,10);
        $preview->setVersaoBirt('2.5.0');
        $preview->setTitulo('Certidão Tempo Serviço - Descritiva');
        $preview->setNomeArquivo('certidaoTempoServicoDescritiva');
    break;

    case "inss":
        $preview = new PreviewBirt(4,22,11);
        $preview->setVersaoBirt('2.5.0');
        $preview->setTitulo('Certidão Tempo Serviço - Modelo INSS');
        $preview->setNomeArquivo('certidaoTempoServicoINSS');
    break;
}

$preview->setReturnURL( CAM_GRH_PES_INSTANCIAS."relatorio/".$pgFilt);
$preview->addParametro("entidade"             , Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade"           , Sessao::getEntidade());
$preview->addParametro("stCodigos"            , $stCodigos);
$preview->addParametro("boArray"              , $boArray);
$preview->addParametro("stDescricaoEntidade"  , $rsEntidade->getCampo('nom_cgm'));
$preview->addParametro("stCNPJEntidade"       , $rsEntidade->getCampo('cnpj_formatado'));
$preview->addParametro("boTotaisTempoServico" , ($_POST["boTotaisTempoServico"]?"true":"false"));
$preview->addParametro("stTipoFiltro"         , $_POST["stTipoFiltro"]);
$preview->addParametro("inCodAtributo"        , $_POST["inCodAtributo"] == '' ? 0 : $_POST["inCodAtributo"]);
$preview->addParametro("stDescAtributo"       , $_POST["stDescCadastro"]);
$preview->addParametro("inContratoResponsavel", $_POST["inContratoResponsavel"]);
$preview->addParametro("stChaveProcesso"      , $_POST["stChaveProcesso"]);
$preview->addParametro("stOrdenacaoMatricula" , $_POST["stOrdenacaoMatricula"]);
$preview->addParametro("dtPeriodoInicial"     , formataData($_POST["stDataInicial"]));
$preview->addParametro("dtPeriodoFinal"       , formataData($_POST["stDataFinal"]));
$preview->addParametro("stNumeroCertidao"     , $_POST["stNumeroCertidao"]);
$preview->addParametro("boDadosIdentificacao" , ($_POST["boDadosIdentificacao"]?"true":"false"));
$preview->addParametro("boGradeEfetividade"   , ($_POST["boGradeEfetividade"]?"true":"false"));
$preview->addParametro("boHistoricoFuncional" , ($_POST["boHistoricoFuncional"]?"true":"false"));
$preview->addParametro("boTotaisTempoServico" , ($_POST["boTotaisTempoServico"]?"true":"false"));
$preview->addParametro("boAgrupar"            , ($_POST["boAgruparCertidoes"] == "S" ?"true":"false"));
$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
?>

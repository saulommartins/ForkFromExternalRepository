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
    * Titulo do arquivo (Ex.: "Formulario de configuração do IPERS")
    * Data de Criação   : 23/06/2008

    * @author Rafael Garbin

    * Casos de uso: uc-04.05.66

    $Id: PRConfiguracaoIpers.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoIpe.class.php"                        );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoIpePensionista.class.php"             );
include_once(  CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                                 );

$stPrograma = "ConfiguracaoIpers";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?stAcao=".$_REQUEST["stAcao"];
$pgForm     = "FM".$stPrograma.".php?stAcao=".$_REQUEST["stAcao"];
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stAcao     = trim($_REQUEST["stAcao"]);

# Verifica o fluxo do programa após o processamento
switch ($stAcao) {
    case "alterar";
    case "excluir";
        $pgProx = $pgList;
        break;
    case "incluir";
        $pgProx = $pgForm;
        break;
}

$obTFolhaPagamentoEvento                     = new TFolhaPagamentoEvento();
$obTFolhaPagamentoConfiguracaoIpe            = new TFolhaPagamentoConfiguracaoIpe();
$obTFolhaPagamentoConfiguracaoIpePensionista = new TFolhaPagamentoConfiguracaoIpePensionista();
Sessao::setTrataExcecao(true);

switch ($stAcao) {
    case "incluir":
    case "alterar":
        if ( trim($_REQUEST["inNumeroMatriculaPensionistaIPERS"]) == trim($_REQUEST["stDataIngressoPensionistaIPERS"])
            && trim($_REQUEST["inNumeroMatriculaPensionistaIPERS"]) != ""
            && trim($_REQUEST["stDataIngressoPensionistaIPERS"]) != "") {
            Sessao::getExcecao()->setDescricao("Os Campos Número da Matrícula IPE/RS e Data de Ingresso(Cadastro do Pensionista) não podem ser iguais.");
        }

        if (trim($_REQUEST["inNumeroMatriculaServidorIPERS"]) == trim($_REQUEST["stDataIngressoServidorIPERS"])) {
            Sessao::getExcecao()->setDescricao("Os Campos Número da Matrícula IPE/RS e Data de Ingresso(Cadastro do Servidor) não podem ser iguais.");
        }

        // verifica se esta alterando vigencia correta
        if ($stAcao != "alterar") {
            $rsConfiguracaoIpe = new RecordSet();
            $stFiltro = " where to_date('".$_REQUEST["dtVigencia"]."', 'dd/mm/yyyy') = vigencia ";
            $obTFolhaPagamentoConfiguracaoIpe->recuperaTodos($rsConfiguracaoIpe, $stFiltro);

            if ($rsConfiguracaoIpe->getNumLinhas() >= 0) {
                Sessao::getExcecao()->setDescricao("Vigência ".trim($_REQUEST["dtVigencia"])." já esta cadastrada.");
            }
        }

        $arAtributoMatPensionista  = explode("-", $_REQUEST["inNumeroMatriculaPensionistaIPERS"]);
        $arAtributoDataPensionista = explode("-", $_REQUEST["stDataIngressoPensionistaIPERS"]);

        $arAtributoMatServidor  = explode("-", $_REQUEST["inNumeroMatriculaServidorIPERS"]);
        $arAtributoDataServidor = explode("-", $_REQUEST["stDataIngressoServidorIPERS"]);

        # Busca o código do evento para evento Base
        $stFiltro = " where codigo = '".$_REQUEST['inCodigoEventoBaseIPERS']."'";
        $obTFolhaPagamentoEvento->recuperaTodos( $rsEventoBase, $stFiltro);

        # Busca o código do evento para evento Desconto
        $stFiltro = " where codigo = '".$_REQUEST['inCodigoEventoDescontoIPERS']."'";
        $obTFolhaPagamentoEvento->recuperaTodos( $rsEventoDesconto, $stFiltro);

        $rsConfiguracaoIpe = new RecordSet();
        $stFiltro = " WHERE configuracao_ipe.vigencia = to_date('".$_REQUEST["dtVigencia"]."', 'dd/mm/yyyy')";
        $obTFolhaPagamentoConfiguracaoIpe->recuperaRelacionamento($rsConfiguracaoIpe, $stFiltro);
        $rsConfiguracaoIpe->setUltimoElemento();

        if ($rsConfiguracaoIpe->getNumLinhas() > 0) {
            $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_configuracao",$rsConfiguracaoIpe->getCampo("cod_configuracao")+1);
            $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("cod_configuracao",$rsConfiguracaoIpe->getCampo("cod_configuracao")+1);
        } else {
            $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_configuracao", 1);
            $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("cod_configuracao", 1);
        }

        # Inserindo configuração Ipe
        $obTFolhaPagamentoConfiguracaoIpe->setDado("vigencia"               , $_REQUEST["dtVigencia"]);
        $obTFolhaPagamentoConfiguracaoIpe->setDado("contibuicao_serv"       , $_REQUEST["stPercContribServidor"]);
        $obTFolhaPagamentoConfiguracaoIpe->setDado("contribuicao_pat"       , $_REQUEST["stPercContribPatronal"]);
        $obTFolhaPagamentoConfiguracaoIpe->setDado("codigo_orgao"           , $_REQUEST["inCodOrgao"]);
        $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_evento_base"        , $rsEventoBase->getCampo("cod_evento"));
        $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_evento_automatico"  , $rsEventoDesconto->getCampo("cod_evento"));
        $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_modulo_mat"         , $arAtributoMatServidor[0]);
        $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_cadastro_mat"       , $arAtributoMatServidor[1]);
        $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_atributo_mat"       , $arAtributoMatServidor[2]);
        $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_modulo_data"        , $arAtributoDataServidor[0]);
        $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_cadastro_data"      , $arAtributoDataServidor[1]);
        $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_atributo_data"      , $arAtributoDataServidor[2]);
        $obTFolhaPagamentoConfiguracaoIpe->inclusao();

        # Inserindo configuração Ipe Pensionista
        if (trim($_REQUEST["inNumeroMatriculaPensionistaIPERS"]) != "" && trim($_REQUEST["stDataIngressoPensionistaIPERS"]) != "") {
            $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("vigencia"           , $_REQUEST["dtVigencia"]);
            $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("cod_modulo_mat"     , $arAtributoMatPensionista[0]);
            $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("cod_cadastro_mat"   , $arAtributoMatPensionista[1]);
            $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("cod_atributo_mat"   , $arAtributoMatPensionista[2]);
            $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("cod_modulo_data"    , $arAtributoDataPensionista[0]);
            $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("cod_cadastro_data"  , $arAtributoDataPensionista[1]);
            $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("cod_atributo_data"  , $arAtributoDataPensionista[2]);
            $obTFolhaPagamentoConfiguracaoIpePensionista->inclusao();
        }

        $stMensagem = "Configuração IPERS ".($stAcao=="alterar"?"alterada":"incluída")." com sucesso.";
    break;

    case "excluir":
        $stFiltro = " WHERE configuracao_ipe.vigencia = to_date('".$_REQUEST["dtVigencia"]."', 'dd/mm/yyyy')";
        $obTFolhaPagamentoConfiguracaoIpe->recuperaRelacionamento($rsConfiguracaoIpe, $stFiltro);

        $obTFolhaPagamentoConfiguracaoIpe->setDado("vigencia", $_REQUEST["dtVigencia"]);
        $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("vigencia", $_REQUEST["dtVigencia"]);

        while (!$rsConfiguracaoIpe->eof()) {
            $obTFolhaPagamentoConfiguracaoIpe->setDado("cod_configuracao", $rsConfiguracaoIpe->getCampo("cod_configuracao"));
            $obTFolhaPagamentoConfiguracaoIpePensionista->setDado("cod_configuracao", $rsConfiguracaoIpe->getCampo("cod_configuracao"));
            $obTFolhaPagamentoConfiguracaoIpePensionista->exclusao();
            $obTFolhaPagamentoConfiguracaoIpe->exclusao();
            $rsConfiguracaoIpe->proximo();
        }
        $stMensagem = "Configuração IPERS excluída";
    break;
}
Sessao::encerraExcecao();
sistemaLegado::alertaAviso($pgProx,$stMensagem,$stAcao,"aviso",Sessao::getId(),"../");

?>

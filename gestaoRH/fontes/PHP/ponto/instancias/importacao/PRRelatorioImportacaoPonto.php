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
    * Formulário
    * Data de Criação: 07/10/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.10.04

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$stPrograma = "ManterImportacaoPonto";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

switch (trim($_POST["stTipoRelatorio"])) {
    case "relatorio":
        $preview = new PreviewBirt(4,51,1);
        $preview->setVersaoBirt("2.5.0");
        $preview->setTitulo("Importação do Ponto");
        $preview->setNomeArquivo("importacaoPonto");
        $preview->addParametro("inCodImportacao" , Sessao::read("inCodImportacao"));
        break;

    case "relatorioErros":
        $preview = new PreviewBirt(4,51,2);
        $preview->setVersaoBirt("2.5.0");
        $preview->setTitulo("Erros Importação do Ponto");
        $preview->setNomeArquivo("errosImportacaoPonto");
        $preview->addParametro("inCodImportacao" , Sessao::read("inCodImportacaoErro"));
        break;
}

$preview->setReturnURL( CAM_GRH_PON_INSTANCIAS."importacao/".$pgFilt);
$preview->addParametro("entidade"        , Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade"      , Sessao::getEntidade());
$preview->addParametro("dtInicial"       , Sessao::read("stDataInicial"));
$preview->addParametro("dtFinal"         , Sessao::read("stDataFinal"));
$preview->addParametro("dtImportacao"    , date("Y-m-d"));
$preview->preview();
?>

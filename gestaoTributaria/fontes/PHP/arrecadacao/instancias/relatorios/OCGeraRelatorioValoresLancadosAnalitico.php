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
    * Página de processamento oculto e geração do relatório para Valores Lançados
    * Data de Criação   : 23/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: OCGeraRelatorioValoresLancados.php 37981 2009-02-10 15:30:53Z cercato $

    * Casos de uso: uc-05.01.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//relatorio analitico agora usa burp
$stFiltroGrupo = "";
if ($_REQUEST['inCodGrupoInicio'] || $_REQUEST['inCodGrupoTermino']) {
    if ($_REQUEST['inCodGrupoInicio'] && $_REQUEST['inCodGrupoTermino']) {
        $arTMP1 = explode( "/", $_REQUEST['inCodGrupoInicio'] );
        $arTMP2 = explode( "/", $_REQUEST['inCodGrupoTermino'] );
        $stFiltroGrupo = $_REQUEST['inCodGrupoInicio']." até ".$_REQUEST['inCodGrupoTermino'];
    } else {
        $arTMP1 = explode( "/", $_REQUEST['inCodGrupoInicio']?$_REQUEST['inCodGrupoInicio']:$_REQUEST['inCodGrupoTermino'] );
        $stFiltroGrupo = $_REQUEST['inCodGrupoInicio']?$_REQUEST['inCodGrupoInicio']:$_REQUEST['inCodGrupoTermino'];
    }
}

$stFiltroCredito = "";
if ($_REQUEST['inCodCreditoInicio'] || $_REQUEST['inCodCreditoTermino']) {
    if ($_REQUEST['inCodCreditoInicio'] && $_REQUEST['inCodCreditoTermino']) {
        $stFiltroCredito = $_REQUEST['inCodCreditoInicio']." até ".$_REQUEST['inCodCreditoTermino'];
        $arTMP1 = explode( ".", $_REQUEST['inCodCreditoInicio'] );
        $arTMP2 = explode( ".", $_REQUEST['inCodCreditoTermino'] );
    } else {
        $arTMP1 = explode( ".", $_REQUEST['inCodCreditoInicio']?$_REQUEST['inCodCreditoInicio']:$_REQUEST['inCodCreditoTermino'] );
        $stFiltroCredito = $_REQUEST['inCodCreditoInicio']?$_REQUEST['inCodCreditoInicio']:$_REQUEST['inCodCreditoTermino'];
    }
}
$stFiltroContrib = "";
if ($_REQUEST['inCodContribuinteInicial'] || $_REQUEST['inCodContribuinteFinal']) {
    if ($_REQUEST['inCodContribuinteInicial'] && $_REQUEST['inCodContribuinteFinal']) {
        $stFiltroContrib = $_REQUEST['inCodContribuinteInicial']." até ".$_REQUEST['inCodContribuinteFinal'];
    } else {
        $stFiltroContrib = $_REQUEST['inCodContribuinteInicial']?$_REQUEST['inCodContribuinteInicial']:$_REQUEST['inCodContribuinteFinal'];
    }
}

$stFiltroLograd = "";
if ($_REQUEST['inNumLogradouro']) {
    $stFiltroLograd = $_REQUEST['inNumLogradouro'];
}

$stFiltroImovel = "";
if ($_REQUEST['inNumInscricaoImobiliariaInicial'] || $_REQUEST['inNumInscricaoImobiliariaFinal']) {
    if ($_REQUEST['inNumInscricaoImobiliariaInicial'] && $_REQUEST['inNumInscricaoImobiliariaFinal']) {
        $stFiltroImovel = $_REQUEST['inNumInscricaoImobiliariaInicial']." até ".$_REQUEST['inNumInscricaoImobiliariaFinal'];
    } else {
        $stFiltroImovel = ($_REQUEST['inNumInscricaoImobiliariaInicial']?$_REQUEST['inNumInscricaoImobiliariaInicial']:$_REQUEST['inNumInscricaoImobiliariaFinal']);
    }
}

$stFiltroExercicio = "";
if ($_REQUEST['inExercicio']) {
    $stFiltroExercicio = $_REQUEST['inExercicio'];

}

$stFiltroEco = "";
if ($_REQUEST['inNumInscricaoEconomicaInicial'] || $_REQUEST['inNumInscricaoEconomicaFinal']) {
    if ($_REQUEST['inNumInscricaoEconomicaInicial'] && $_REQUEST['inNumInscricaoEconomicaFinal']) {
        $stFiltroEco = $_REQUEST['inNumInscricaoEconomicaInicial']." até ".$_REQUEST['inNumInscricaoEconomicaFinal'];
    } else {
        $stFiltroEco = $_REQUEST['inNumInscricaoEconomicaInicial']?$_REQUEST['inNumInscricaoEconomicaInicial']:$_REQUEST['inNumInscricaoEconomicaFinal'];
    }
}

$stFiltroAtividade = "";
if ($_REQUEST["inCodInicio"] || $_REQUEST["inCodTermino"]) {
    if ($_REQUEST["inCodInicio"] && $_REQUEST["inCodTermino"]) {
        $stFiltroAtividade = $_REQUEST["inCodInicio"]." até ".$_REQUEST["inCodTermino"];
    } else {
        $stFiltroAtividade = $_REQUEST["inCodInicio"]?$_REQUEST["inCodInicio"]:$_REQUEST["inCodTermino"];
    }
}

$stFiltroCondominio = "";
if ($_REQUEST['inCodCondominioInicial'] || $_REQUEST['inCodCondominioFinal']) {
    if ($_REQUEST['inCodCondominioInicial'] && $_REQUEST['inCodCondominioFinal']) {
        $stFiltroCondominio = $_REQUEST['inCodCondominioInicial']." até ".$_REQUEST['inCodCondominioFinal'];
    } else {
        $stFiltroCondominio = ($_REQUEST['inCodCondominioInicial']?$_REQUEST['inCodCondominioInicial']:$_REQUEST['inCodCondominioFinal']);
    }
}

$stFiltroValor = "";
if ($_REQUEST['nuValorInicial'] || $_REQUEST['nuValorFinal']) {
    $nuValorInicial = str_replace( '.', '', $_REQUEST['nuValorInicial'] ) ;
    $nuValorInicial = str_replace( ',', '.', $nuValorInicial );
    $nuValorFinal = str_replace( '.', '', $_REQUEST['nuValorFinal'] ) ;
    $nuValorFinal = str_replace( ',', '.', $nuValorFinal );
    if ($_REQUEST['nuValorInicial'] && $_REQUEST['nuValorFinal']) {
        $stFiltroValor = $_REQUEST['nuValorInicial']." até ".$_REQUEST['nuValorFinal'];
    } else {
        $stFiltroValor = ($_REQUEST['nuValorInicial']?$_REQUEST['nuValorInicial']:$_REQUEST['nuValorFinal']);
    }
}

$stFiltroSituacao = $_REQUEST['boSituacao'];

$preview = new PreviewBirt( 5, 25, 4 );
$preview->setVersaoBirt( '2.5.0' );
$preview->setFormato( 'pdf' );
$preview->setTitulo( 'Relatório de Arrecadacação' );
$preview->addParametro( 'stFiltroValor'     , $stFiltroValor      );
$preview->addParametro( 'stFiltroInscImob'  , $stFiltroImovel     );
$preview->addParametro( 'stFiltroInscEco'   , $stFiltroEco        );
$preview->addParametro( 'stFiltroSituacao'  , $stFiltroSituacao   );
$preview->addParametro( 'stFiltroGrupo'     , $stFiltroGrupo      );
$preview->addParametro( 'stFiltroCredito'   , $stFiltroCredito    );
$preview->addParametro( 'stFiltroExerc'     , $stFiltroExercicio               );
$preview->addParametro( 'stFiltroContrib'   , $stFiltroContrib    );
$preview->addParametro( 'stFiltroLograd'    , $stFiltroLograd     );
$preview->addParametro( 'stFiltroAtividade' , $stFiltroAtividade  );
$preview->addParametro( 'stFiltroCond'      , $stFiltroCondominio );
$preview->preview();

?>

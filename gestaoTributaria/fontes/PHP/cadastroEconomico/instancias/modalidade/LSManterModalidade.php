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
    * Lista para Modalidade de Lançamento
    * Data de Criação   : 04/01/2005

    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: LSManterModalidade.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.7  2006/09/15 14:33:18  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeLancamento.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeAtividade.class.php"  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeInscricao.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterModalidade";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stCaminho = CAM_GT_CEM_INSTANCIAS."modalidade/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

// DEFINE LISTA
$rsLista = new RecordSet;

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read( "link" );
if ($_GET["pg"] and  $_GET["pos"]) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}
Sessao::write( "link", $link );
//DEFINICAO DO FILTRO PARA CONSULTA
$stLink = "";
if ($_REQUEST["boVinculoModalidade"] == "inscricao") {
    $obRCEMModalidadeInscricao  = new RCEMModalidadeInscricao;
    if ($_REQUEST["inInscricaoEconomica"]) {
        $obRCEMModalidadeInscricao->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST["inInscricaoEconomica"] );
        $stLink .= "&inInscricaoEconomica=".$_REQUEST["inInscricaoEconomica"];
    }
    $obRCEMModalidadeInscricao->listarModalidadeAtividadeInscricao( $rsLista );

    $stLink .= "&stAcao=".$stAcao;

    //DEFINICAO DA LISTA - INSCRIÇÃO ECONOMICA
    $obLista = new Lista;
    $obLista->obPaginacao->setFiltro("&stLink=".$stLink );
    $obLista->setRecordSet( $rsLista );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Inscrição Econômica" );
    $obLista->ultimoCabecalho->setWidth( 16 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->ultimoCabecalho->setWidth( 64 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Vigência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inscricao_economica"    );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm"                );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_vigencia_modalidade" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO"           );
    $obLista->commitDado();

    // Define ACOES
    if ($stAcao == "excluir") {
        $obLista->addAcao();
        $stAcao = "excluir";
        $obLista->ultimaAcao->setAcao( $stAcao );
        $obLista->ultimaAcao->addCampo("&inInscricaoEconomica" , "inscricao_economica"    );
        $obLista->ultimaAcao->addCampo("&stDescQuestao"        , "inscricao_economica"    );
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?boVinculoModalidade=inscricao&".Sessao::getId().$stLink."&stAcao=".$stAcao );
        $obLista->commitAcao();
    } elseif ($stAcao == "baixar") {
        $obLista->addAcao();
        $stAcao = "baixar";
        $obLista->ultimaAcao->setAcao( $stAcao );
        $obLista->ultimaAcao->addCampo("&inInscricaoEconomica" , "inscricao_economica"    );
        $obLista->ultimaAcao->addCampo("&inCodigoAtividade"    , "cod_atividade"          );
        $obLista->ultimaAcao->addCampo("&inOcorrenciaAtividade", "ocorrencia_atividade"   );
        $obLista->ultimaAcao->addCampo("&inCodigoModalidade"   , "cod_modalidade"         );
        $obLista->ultimaAcao->addCampo("&stNomeModalidade"     , "nom_modalidade"         );
        $obLista->ultimaAcao->addCampo("&dtVigenciaModalidade" , "dt_vigencia_modalidade" );
        $obLista->ultimaAcao->addCampo("&dtDataBaixaModalidade", "dt_baixa_modalidade"    );
        $obLista->ultimaAcao->addCampo("&stMotivoBaixa"        , "motivo_baixa_modalidade");
        $obLista->ultimaAcao->addCampo("&inNumCGM"             , "numcgm"                 );
        $obLista->ultimaAcao->addCampo("&stNomeCGM"            , "nom_cgm"                );
        $obLista->ultimaAcao->setLink( $pgFormVinculo."?boVinculoModalidade=inscricao&".Sessao::getId().$stLink."&stAcao=".$stAcao );
        $obLista->commitAcao();
    }
} elseif ($_REQUEST["boVinculoModalidade"] == "atividade") {
    $obRCEMModalidadeAtividade  = new RCEMModalidadeAtividade;
    if ($_REQUEST["stValorComposto"]) {
        $stValorComposto=$_REQUEST["stValorComposto"];
    } else {
        $stValorComposto=$_REQUEST["stChaveAtividade"];
    }
    if ($stValorComposto) {
        $obRCEMModalidadeAtividade->obRCEMAtividade->setValorComposto($stValorComposto);
        $stLink .= "&stValorComposto=".$stValorComposto;
    }
    $obRCEMModalidadeAtividade->listarModalidadeAtividade( $rsLista );

    $stLink .= "&stAcao=".$stAcao;

    //DEFINICAO DA LISTA - ATIVIDADE
    $obLista = new Lista;
    $obLista->obPaginacao->setFiltro("&stLink=".$stLink );
    $obLista->setRecordSet( $rsLista );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Atividade");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Modalidade");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Vigência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor_composto"         );
    $obLista->ultimoDado->setAlinhamento( "CENTRO"           );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_atividade"          );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_modalidade"         );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_vigencia_modalidade" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO"           );
    $obLista->commitDado();

    // Define ACOES
    if ($stAcao == "excluir") {
        $obLista->addAcao();
        $stAcao = "excluir";
        $obLista->ultimaAcao->setAcao( $stAcao );
        $obLista->ultimaAcao->addCampo("&inCodigoAtividade"    , "cod_atividade"          );
        $obLista->ultimaAcao->addCampo("&stDescQuestao"        , "valor_composto"          );
        $obLista->ultimaAcao->addCampo("&inCodigoModalidade"   , "cod_modalidade"         );
        $obLista->ultimaAcao->addCampo("&dtVigenciaModalidade" , "dt_vigencia_modalidade" );
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?boVinculoModalidade=atividade&".Sessao::getId().$stLink."&stAcao=".$stAcao );
        $obLista->commitAcao();
    } elseif ($stAcao == "baixar") {
        $obLista->addAcao();
        $stAcao = "baixar";
        $obLista->ultimaAcao->setAcao( $stAcao );
        $obLista->ultimaAcao->addCampo("&inCodigoAtividade"    , "cod_atividade"          );
        $obLista->ultimaAcao->addCampo("&stNomeAtividade"      , "nom_atividade"          );
        $obLista->ultimaAcao->addCampo("&stValorComposto"      , "valor_composto"         );
        $obLista->ultimaAcao->addCampo("&inCodigoModalidade"   , "cod_modalidade"         );
        $obLista->ultimaAcao->addCampo("&stNomeModalidade"     , "nom_modalidade"         );
        $obLista->ultimaAcao->addCampo("&dtVigenciaModalidade" , "dt_vigencia_modalidade" );
        $obLista->ultimaAcao->addCampo("&dtDataBaixaModalidade", "dt_baixa_modalidade"    );
        $obLista->ultimaAcao->addCampo("&stMotivoBaixa"        , "motivo_baixa_modalidade");
        $obLista->ultimaAcao->setLink( $pgFormVinculo."?boVinculoModalidade=atividade&".Sessao::getId().$stLink."&stAcao=".$stAcao );
        $obLista->commitAcao();
    }
}
$obLista->show();

// DEFINE BOTOES
$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );
$botoes = array ($obBtnFiltro);

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

//DEFINE FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.02.13" );
$obFormulario->addHidden   ($obHdnAcao          );
$obFormulario->defineBarra ($botoes,'left',''   );
$obFormulario->show();
?>

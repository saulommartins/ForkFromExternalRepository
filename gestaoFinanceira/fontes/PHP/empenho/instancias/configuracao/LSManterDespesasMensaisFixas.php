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
    * Página de Lista de Despesas Mensais Fixas
    * Data de Criação   : 30/08/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 31087 $
    $Name$
    $Autor: $
    $Date: 2006-11-25 13:23:07 -0200 (Sáb, 25 Nov 2006) $

    * Casos de uso: uc-02.03.29
*/

/**

$Log$
Revision 1.4  2006/11/25 15:23:07  cleisson
Bug #7597#

Revision 1.3  2006/09/08 16:17:14  tonismar
alteração dos campos nro_identificacao e nro_contrato para num_identificacao e num_contrato

Revision 1.2  2006/09/04 10:29:22  tonismar
Manter Despesas Fixas Mensais

Revision 1.1  2006/09/01 17:35:03  tonismar
Manter Despesas Fixas Mensais

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TEMP."TEmpenhoDespesasFixas.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDespesasMensaisFixas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProx = $pgForm;

$stCaminho = CAM_GF."PHP/empenho/instancias/configuracao/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
if ($_GET["pg"] and  $_GET["pos"]) {
    Sessao::write('pg', $_GET["pg"]);
    Sessao::write('pos', $_GET["pos"]);
}
if ( is_array(Sessao::read('link')) ) {
    $_GET = Sessao::read('link');
    $_REQUEST = Sessao::read('link');
} else {
    $arLink = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
    Sessao::write('link', $arLink);
}

$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}

$obTEmpenhoDespesasFixas = new TEmpenhoDespesasFixas();
$obTEmpenhoDespesasFixas->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
$obTEmpenhoDespesasFixas->setDado('exercicio', $_REQUEST['stExercicio']);
$obTEmpenhoDespesasFixas->setDado('cod_tipo', $_REQUEST['inCodTipo']);

if ($_REQUEST['inIdentificacao']) {
    $obTEmpenhoDespesasFixas->setDado('num_identificacao', $_REQUEST['inIdentificacao']);
}
if ($_REQUEST['inContrato']) {
    $obTEmpenhoDespesasFixas->setDado('num_contrato', $_REQUEST['inContrato']);
}
if ($_REQUEST['inCodLoca']) {
    $obTEmpenhoDespesasFixas->setDado('cod_local', $_REQUEST['inCodLoca']);
}
if ($_REQUEST['inCodDotacao']) {
    $obTEmpenhoDespesasFixas->setDado('cod_despesa', $_REQUEST['inCodDotacao']);
}
if ($_REQUEST['inCodCredor']) {
    $obTEmpenhoDespesasFixas->setDado('numcgm', $_REQUEST['inCodCredor']);
}
if ($_REQUEST['stDataInicial']) {
    $obTEmpenhoDespesasFixas->setDado('dt_inicial', $_REQUEST['stDataInicial']);
}
if ($_REQUEST['stDataFinal']) {
    $obTEmpenhoDespesasFixas->setDado('dt_final', $_REQUEST['stDataFinal']);
}
if ($_REQUEST['inCodStatus'] == 1) {
    $obTEmpenhoDespesasFixas->setDado('status', 't');
} elseif ($_REQUEST['inCodStatus'] == 2) {
    $obTEmpenhoDespesasFixas->setDado('status', 'f');
}

$rsLista = new RecordSet();

$obTEmpenhoDespesasFixas->recuperaDespesasFixas( $rsLista );

$obLista = new Lista();

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade ");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nr. Identificação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Dotação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Credor" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "num_identificacao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_despesa" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodEntidade","cod_entidade");
$obLista->ultimaAcao->addCampo("&stEntidade", "nom_entidade");
$obLista->ultimaAcao->addCampo("&inCodDotacao","cod_dotacao");
$obLista->ultimaAcao->addCampo("inCodTipo", "cod_tipo");
$obLista->ultimaAcao->addCampo("stDescricaoTipo", "descricao");
$obLista->ultimaAcao->addCampo("&stExercicio"   ,"exercicio");
$obLista->ultimaAcao->addCampo("&inCodDespesaFixa", "cod_despesa_fixa");
$obLista->ultimaAcao->addCampo("&inIdentificacao", "num_identificacao");
$obLista->ultimaAcao->addCampo("&inContrato", "num_contrato");
$obLista->ultimaAcao->addCampo("&inCodLocal", "cod_local");
$obLista->ultimaAcao->addCampo("&inCodDotacao", "cod_despesa");
$obLista->ultimaAcao->addCampo("&inCodCredor", "numcgm");
$obLista->ultimaAcao->addCampo("&stCredor", "nom_cgm");
$obLista->ultimaAcao->addCampo("&stDataInicial", "dt_inicial");
$obLista->ultimaAcao->addCampo("&stDataFinal", "dt_final");
$obLista->ultimaAcao->addCampo("&stDataInclusao", "dt_inclusao");
$obLista->ultimaAcao->addCampo("&inCodStatus", "status");
$obLista->ultimaAcao->addCampo("&stDescQuestao" ,"cod_despesa_fixa");
$obLista->ultimaAcao->addCampo("&inDiaVencimento", "dia_vencimento");
$obLista->ultimaAcao->addCampo("&stHistorico", "historico");
$obLista->ultimaAcao->addCampo("&inCodStatus", "status");
$obLista->ultimaAcao->addCampo("&inCodDespesaFixa", "cod_despesa_fixa");

if ($stAcao == "excluir") {
   $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} else {
   $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

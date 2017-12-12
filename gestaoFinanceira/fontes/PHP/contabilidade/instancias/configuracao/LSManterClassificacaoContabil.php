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
    * Página de Listagem Classificação Contábil
    * Data de Criação   : 10/11/2004

    * @author Desenvolvedor: Gelson Wolowski Gonçalves

    * @ignore

    $Revision: 30739 $
    $Name$
    $Autor: $
    $Date: 2007-08-15 11:15:55 -0300 (Qua, 15 Ago 2007) $

    * Casos de uso: uc-02.02.01
*/

/*
$Log$
Revision 1.5  2007/08/15 14:15:13  hboaventura
Bug#9914#

Revision 1.4  2006/07/05 20:50:46  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO."RContabilidadeClassificacaoContabil.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterClassificacaoContabil";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProx = $pgForm;

$stCaminho = CAM_GF_CONT_INSTANCIAS."configuracao/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$arFiltro = Sessao::read('filtro');
if ($_REQUEST['inCodClassificacao'] or $_REQUEST['stNomClassificacao']) {
    foreach ($_REQUEST as $key => $value) {
        $arFiltro[$key] = $value;
    }
    Sessao::write('filtro', $arFiltro);
} else {
    if ($arFiltro) {
        foreach ($arFiltro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando', true);
}

$obRegra = new RContabilidadeClassificacaoContabil;
$stFiltro = "";
$stLink   = "";

$stLink .= '&inCodClassificacao='.$_REQUEST['inCodClassificacao'];
$stLink .= "&stAcao=".$stAcao;

$rsLista = new RecordSet;
$obRegra->setCodClassificacao( $_REQUEST['inCodClassificacao'] );
$obRegra->setNomClassificacao( $_REQUEST['stNomClassificacao'] );
$obRegra->setExercicio   ( Sessao::getExercicio()          );

$obRegra->listar( $rsLista );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setAjuda             ('UC-02.02.01');
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_classificacao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_classificacao" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodClassificacao","cod_classificacao");
$obLista->ultimaAcao->addCampo("&stNomClassificacao","nom_classificacao");
$obLista->ultimaAcao->addCampo("&stExercicio"   ,"exercicio");
$obLista->ultimaAcao->addCampo("&stDescQuestao" ,"[cod_classificacao] - [nom_classificacao]");

if ($stAcao == "excluir") {
   $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} else {
   $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

?>

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
    * Página de Formulário Almoxarifado
    * Data de Criação   : 28/10/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    $Id: LSManterClassificacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoClassificacao.class.php");

$stPrograma = "ManterClassificacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_ALM_INSTANCIAS."catalogo/";

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

if ( isset($_REQUEST['inCodCatalogo'] )) {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write('link' , $link);
} else {
    $arrayFiltro = Sessao::read('link');
    if ($arrayFiltro) {
        foreach ($arrayFiltro as $key => $value) {
              $_REQUEST[$key] = $value;
        }
    }
   Sessao::write('paginando',true);
}

$stAcao = $request->get('stAcao');

$obRegra = new RAlmoxarifadoCatalogoClassificacao;

$obRegra->obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodCatalogo']);
$rsCodClassificacao = new RecordSet;
$obRegra->setEstrutural( $_REQUEST['stChaveClassificacao'] );
$obRegra->recuperaCodigoClassificacao( $rsCodClassificacao );

$obRegra->setCodigo( $rsCodClassificacao->getCampo( 'cod_classificacao' ));

$rsLista = new RecordSet;

$obRegra->listarClassificacoes( $rsLista );

$obLista = new Lista;

$stLink = isset($stLink) ? $stLink : null;

$obLista->obPaginacao->setFiltro('&stLink='.$stLink.'&stAcao='.$stAcao);

$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Classificação");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 75 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodigo"              , "cod_catalogo"      );
$obLista->ultimaAcao->addCampo("&inCodigoClassificacao" , "cod_classificacao" );
$obLista->ultimaAcao->addCampo("&inNivel"         , "nivel"             );
$obLista->ultimaAcao->addCampo("&stCodigoEstrutural", "cod_estrutural" );

if ($stAcao == 'excluir') {
    $obLista->ultimaAcao->addCampo("&stDescQuestao" ,"[cod_estrutural] - [descricao]");
} else {
    $obLista->ultimaAcao->addCampo("&stDescQuestao" ,"[codigo] - [descricao]"     );
}

if ($stAcao == "excluir") {
   $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink.'&stAcao='.$stAcao );
} elseif ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink.'&stAcao='.$stAcao.'&pg='.$_REQUEST['pg'].'&pos='.$_REQUEST['pos'] );
} else {
   $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->setAjuda("UC-03.03.05");
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

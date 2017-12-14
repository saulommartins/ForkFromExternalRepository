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
/*
    * Lista de Lotes para Alteração
    * Data de Criação   : 05/08/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterLancamentoPartidaDobrada";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$arFiltro = array();
$arFiltro = Sessao::read('filtro');

if (count($arFiltro) > 0) {
    $inCodEntidade = $arFiltro[0]['inCodEntidade'];
    $inCodLote     = $arFiltro[0]['inCodLote'    ];
    $stNomLote     = $arFiltro[0]['stNomLote'    ];
    $stDtLote      = $arFiltro[0]['stDtLote'     ];
} else {
    $inCodEntidade = $arFiltro[0]['inCodEntidade'] = $_REQUEST['inCodEntidade' ];
    $inCodLote     = $arFiltro[0]['inCodLote'    ] = $_REQUEST['inCodLote'     ];
    $stNomLote     = $arFiltro[0]['stNomLote'    ] = $_REQUEST['stNomLote'     ];
    $stDtLote      = $arFiltro[0]['stDtLote'     ] = $_REQUEST['stDtLote'      ];
    Sessao::write('filtro', $arFiltro);
}

switch ($stAcao) {
    case 'alterar':
        $pgProx = $pgForm;
    break;

    DEFAULT:
        $pgProx = $pgForm;
}

include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
$obTContabilidadeLote = new TContabilidadeLote;
if ($inCodEntidade) {
    $stFiltro .= " WHERE lote.cod_entidade   = ".$inCodEntidade."\n";
}
if ($stDtLote) {
    $stFiltro .= " AND lote.dt_lote        = to_date('".$stDtLote."','dd/mm/yyyy') \n";
}
if ($stNomLote) {
    $stFiltro .= " AND lote.nom_lote ilike '%".$stNomLote."%' \n";
}
if ($inCodLote) {
    $stFiltro .= " AND lote.cod_lote       = ".$inCodLote."\n";
}

$stFiltro .= " AND lote.exercicio = '".Sessao::getExercicio()."' \n";
$stFiltro .= " AND lote.tipo = 'M'                               \n";

$stOrder = " ORDER BY cod_lote  \n";

$obTContabilidadeLote->recuperaTodos($rsRecordSet, $stFiltro, $stOrder);

$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}

//LISTA
$obLista = new Lista;
$obLista->setTitulo('Dados da Lista');
$obLista->setMostraPaginacao( false );
$obLista->setRecordSet( $rsRecordSet );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nr. Lote");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome do Lote");
$obLista->ultimoCabecalho->setWidth( 77 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_lote]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_lote]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[dt_lote]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&cod_lote"     , "cod_lote"     );
$obLista->ultimaAcao->addCampo( "&stNomLote"    , "nom_lote"     );
$obLista->ultimaAcao->addCampo( "&dtLote"       , "dt_lote"      );
$obLista->ultimaAcao->addCampo( "&cod_entidade" , "cod_entidade" );
$obLista->ultimaAcao->addCampo( "&exercicio"    , "exercicio"    );
$obLista->ultimaAcao->addCampo( "&tipo"         , "tipo"         );

if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

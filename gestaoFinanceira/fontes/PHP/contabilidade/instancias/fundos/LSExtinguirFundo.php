<?php

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO . "RContabilidadeFundo.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ExtinguirFundo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";

$obRContabilidadeFundo = new RContabilidadeFundo;
$arFiltro = Sessao::read('filtro');

if ( !Sessao::read('paginando')) {
    foreach ($request->getAll() as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }

    Sessao::write('filtro', $arFiltro);
    Sessao::write('pg', $request->get('pg') ? $request->get('pg') : 0);
    Sessao::write('pos', $request->get('pos') ? $request->get('pos') : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $request->get('pg'));
    Sessao::write('pos', $request->get('pos'));
}

$obRContabilidadeFundo->setExercicio( Sessao::getExercicio() );
$obRContabilidadeFundo->setCodFundo( $request->get('inCodFundo') );
$obRContabilidadeFundo->listar( $rsLista, $arFiltro, 'cod_fundo' );

if ($request->get('pg') and  $request->get('pos')) {
    $stLink.= "&pg=".$request->get('pg')."&pos=".$request->get('pos');
}

$rsLista->setPrimeiroElemento();

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Fundo");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade Orçamentária");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Órgão Orçamentário");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Unidade Orçamentária");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CNPJ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_fundo" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "entidade" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "orgao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "unidade" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cnpj" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "Extinguir" );
$obLista->ultimaAcao->addCampo("&value", "cod_fundo");
$obLista->ultimaAcao->setLink( "OCManterFundo?".Sessao::getId() . "&stCtrl=extinguirFundo&exercicio=" . Sessao::getExercicio() );

$obLista->commitAcao();
$obLista->show();

?>

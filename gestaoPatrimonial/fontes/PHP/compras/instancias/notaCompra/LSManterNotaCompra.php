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
    * Página de lista nota de compra
    * Data de Criação   : 08/12/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Thiago La Delfa Cabelleira

    * @ignore

    * Casos de uso: uc-03.04.29

    $Id: LSManterNotaCompra.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");

$stPrograma = "ManterNotaCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_COM_INSTANCIAS."notaCompra/";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case 'alterar':
        $pgProx = $pgForm; break;
    case 'excluir':
        $pgProx = $pgProc; break;
    case 'consultar':
        $pgProx = $pgForm; break;
}

//filtros
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        #sessao->transf4['filtro'][$stCampo] = $stValor;
        $filtro[$stCampo] = $stValor;
    }
    Sessao::write('pg'  , ($_GET['pg'] ? $_GET['pg'] : 0));
    Sessao::write('pos' , ($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando' , true);
} else {
    Sessao::write('pg'  , $_GET['pg']);
    Sessao::write('pos' , $_GET['pos']);
}

    if ( Sessao::read('filtro') ) {

        foreach ( Sessao::read('filtro') as $key => $value ) {
            $_REQUEST[$key] = $value;
        }
    }

    Sessao::write('paginando' , true);

    $stFiltro = "";
    $stLink   = "";

    $stLink .= "&stAcao=".$stAcao;

    $obListaNF = new TComprasOrdemCompraNota;
    $emp = explode('/',$_REQUEST['inCodEmpenho']);
    $emp = $emp[0];

    $obListaNF->setDado('cod_empenho',$emp);
    $obListaNF->setDado('stDataInicial',$_REQUEST['stDataInicial']);
    $obListaNF->setDado('stDataFinal',$_REQUEST['stDataFinal']);
    $obListaNF->setDado('num_nota',$_REQUEST['num_nota']);
    $obListaNF->setDado('cod_ordem',$_REQUEST['inCodOrdemCompra']);
    $obListaNF->setDado('vl_total',$_REQUEST['vl_total']);
    if ($stAcao == 'excluir') {
    $obListaNF->setDado('stAcao','excluir');
    }
    $obListaNF->recuperaNotas($rsRecordSet);

    $obListaNotas = new Lista;
    $obListaNotas->obPaginacao->setFiltro("&stLink=".$stLink );
    $obListaNotas->setRecordSet( $rsRecordSet );

    $obListaNotas->addCabecalho();
    $obListaNotas->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaNotas->ultimoCabecalho->setWidth( 5 );
    $obListaNotas->commitCabecalho();

    $obListaNotas->addCabecalho();
    $obListaNotas->ultimoCabecalho->addConteudo("Nr Nota");
    $obListaNotas->ultimoCabecalho->setWidth( 13 );
    $obListaNotas->commitCabecalho();
    $obListaNotas->addDado();
    $obListaNotas->ultimoDado->setCampo( "num_nota" );
    $obListaNotas->ultimoDado->setAlinhamento( 'CENTRO' );
    $obListaNotas->commitDadoComponente();

    $obListaNotas->addCabecalho();
    $obListaNotas->ultimoCabecalho->addConteudo("Data da Nota");
    $obListaNotas->ultimoCabecalho->setWidth( 13 );
    $obListaNotas->commitCabecalho();
    $obListaNotas->addDado();
    $obListaNotas->ultimoDado->setCampo( "dt_nota" );
    $obListaNotas->ultimoDado->setAlinhamento( 'CENTRO' );
    $obListaNotas->commitDadoComponente();

    $obListaNotas->addCabecalho();
    $obListaNotas->ultimoCabecalho->addConteudo("Valor Total");
    $obListaNotas->ultimoCabecalho->setWidth( 24 );
    $obListaNotas->commitCabecalho();
    $obListaNotas->addDado();
    $obListaNotas->ultimoDado->setCampo( "vl_total" );
    $obListaNotas->ultimoDado->setAlinhamento( 'CENTRO' );
    $obListaNotas->commitDadoComponente();

    $obListaNotas->addCabecalho();
    $obListaNotas->ultimoCabecalho->addConteudo("Nr OC");
    $obListaNotas->ultimoCabecalho->setWidth( 13 );
    $obListaNotas->commitCabecalho();
    $obListaNotas->addDado();
    $obListaNotas->ultimoDado->setCampo( "cod_ordem" );
    $obListaNotas->ultimoDado->setAlinhamento( 'CENTRO' );
    $obListaNotas->commitDadoComponente();

    $obListaNotas->addCabecalho();
    $obListaNotas->ultimoCabecalho->addConteudo("Nr Empenho");
    $obListaNotas->ultimoCabecalho->setWidth( 13 );
    $obListaNotas->commitCabecalho();
    $obListaNotas->addDado();
    $obListaNotas->ultimoDado->setCampo("cod_empenho");
    $obListaNotas->ultimoDado->setAlinhamento( 'CENTRO' );
    $obListaNotas->commitDadoComponente();

    $obListaNotas->addCabecalho();
    if ($stAcao == 'consultar') {
      $obListaNotas->ultimoCabecalho->addConteudo("Consultar");
    }
    if ($stAcao == 'excluir') {
      $obListaNotas->ultimoCabecalho->addConteudo("Excluir");
    }
    $obListaNotas->ultimoCabecalho->setWidth( 13 );
    $obListaNotas->commitCabecalho();

    $obListaNotas->addAcao();
    $obListaNotas->ultimaAcao->setAcao( $stAcao );
    if ($stAcao == 'consultar') {
      $obListaNotas->ultimaAcao->addCampo('&numNota','num_nota');
      $obListaNotas->ultimaAcao->addCampo("&inCodOrdem","cod_ordem");
      $obListaNotas->ultimaAcao->addCampo("&inCodEmpenho","cod_empenho");
      $obListaNotas->ultimaAcao->addCampo("&exercicio","exercicio");
      $obListaNotas->ultimaAcao->addCampo("&codEntidade","cod_entidade");
    }
    if ($stAcao == 'excluir') {
      $obListaNotas->ultimaAcao->addCampo('&exercicio',"exercicio");
      $obListaNotas->ultimaAcao->addCampo("&cod_entidade","cod_entidade");
      $obListaNotas->ultimaAcao->addCampo("&inCodOrdem","cod_ordem");
      $obListaNotas->ultimaAcao->addCampo('&numNota','num_nota');
      $obListaNotas->ultimaAcao->addCampo('&stDescQuestao','num_nota');
    }

    $obListaNotas->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink);
    $obListaNotas->setAjuda("UC-03.04.29");
    $obListaNotas->commitAcao();
    $obListaNotas->Show();

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

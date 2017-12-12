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
    * Data de Criação: 16/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: LSManterMarca.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaMarca.class.php");

$stPrograma = "ManterMarca";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_FRO_INSTANCIAS."configuracao/";

//seta o filtro na sessao e vice-versa
if ( isset($_REQUEST['inCodVeiculo']) ) {
    foreach ($_REQUEST as $stKey=>$stValue) {
        #sessao->transf4['filtro'][$stKey] = $stValue;
        $filtro[$stKey] = $stValue;
    }
} else {
    if ( is_array(Sessao::read('filtro')) ) {
        foreach ( Sessao::read('filtro') as $stKey=>$stValue ) {
            $_REQUEST[$stKey] = $stValue;
        }
    }
}

if ($_REQUEST['pg'] != '') {
    Sessao::write('pg'  , $_GET['pg'] );
    Sessao::write('pos' , $_GET['pos']);
} else {
    $_GET['pg']  = Sessao::read('pg');
    $_GET['pos'] = Sessao::read('pos');
}

Sessao::write('paginando' , true);

//recupera os registro do banco
$obTFrotaMarca = new TFrotaMarca();

//seta os filtros
if ($_REQUEST['stHdnMarca'] != '') {
    $stFiltro = " AND nom_marca ILIKE '".$_REQUEST['stHdnMarca']."' ";
}

//se a acao for excluir restrige os registros
if ($stAcao == 'excluir') {
    $stFiltro .= " AND NOT EXISTS ( SELECT 1
                                      FROM frota.modelo
                                     WHERE modelo.cod_marca = marca.cod_marca
                                  ) ";
}

if ($stFiltro) {
    $stFiltro = ' WHERE '.substr($stFiltro,4);
}

$obTFrotaMarca->recuperaTodos( $rsMarca, $stFiltro, ' ORDER BY nom_marca' );

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda('uc-03.02.03');
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsMarca );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Marca" );
$obLista->ultimoCabecalho->setWidth( 80  );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_marca] - [nom_marca]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodMarca", "cod_marca"  );
$obLista->ultimaAcao->addCampo( "&stNomMarca", "nom_marca"  );
$obLista->ultimaAcao->addCampo( "&stDescQuestao" , "[nom_marca]" );

if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc.'?'.Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

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
    * Página de Listagem de Empenhos com problemas para manutenção de datas
    * Data de Criação   : 07/06/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.16
*/

/*
$Log$
Revision 1.4  2006/07/05 20:48:49  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoManutencaoDatas.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManutencaoDatas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new REmpenhoManutencaoDatas;

if ( Sessao::read('paginando') ) {
    $arFiltro = array();
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('filtro', $arFiltro);
    Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
    $arFiltro = Sessao::read('filtro');
    $_REQUEST['inCodEntidade'       ] = $arFiltro['inCodEntidade'      ];
    $_REQUEST['stExercicioEmpenho'  ] = $arFiltro['stExercicioEmpenho' ];
    $_REQUEST['inCodEmpenho'        ] = $arFiltro['inCodEmpenho'       ];
    $_REQUEST['inCodAutorizacao'    ] = $arFiltro['inCodAutorizacao'   ];
    $_REQUEST['stDtAutorizacao'     ] = $arFiltro['stDtAutorizacao'    ];
    $_REQUEST['stDtEmpenho'         ] = $arFiltro['stDtEmpenho'        ];
    $_GET['stAcao'                  ] = $arFiltro['stAcao'             ];
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgForm; break;
    DEFAULT         : $pgProx = $pgForm;
}
foreach ($_REQUEST['inCodEntidade'] as $value) {
    $stCodEntidade .= $value . " , ";
}
$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);

$obRegra->setExercicioEmpenho ($_REQUEST["stExercicioEmpenho"]);
$obRegra->setCodEntidade($stCodEntidade);
$obRegra->listar( $rsLista );

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Exercício");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Empenho");
$obLista->ultimoCabecalho->setWidth(30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_empenho" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "alterar" );
$obLista->ultimaAcao->addCampo( "&inCodEmpenho"     , "cod_empenho"     );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "&inCodAutorizacao" , "cod_autorizacao" );
$obLista->ultimaAcao->addCampo( "&stDtAutorizacao"  , "dt_autorizacao"  );
$obLista->ultimaAcao->addCampo( "&stDtEmpenho"       , "dt_empenho"      );
$obLista->ultimaAcao->addCampo( "&stExercicioEmpenho" ,"exercicio"      );

$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("65");
$obIFrame->show();
?>

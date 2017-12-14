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
    * Página de Filtro do cadastro de Ata
    * Data de Criação: 14/01/2009

    * @author Analista: Gelson
    * @author Desenvolvedor: Diogo Zarpelon

    * @ignore

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_COMPONENTES."Table/TableTree.class.php";
include_once TLIC."TLicitacaoAta.class.php";

$stPrograma = "ManterAta";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once $pgJs;

$obTLicitacaoAta = new TLicitacaoAta;

$stCaminho = CAM_GP_LIC_INSTANCIAS."processoLicitatorio/";

$stAcao = $request->get('stAcao');

# Paginação e filtros.
$stLink .= "&stAcao=".$stAcao;

if ($_GET["pg"] && $_GET["pos"]) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write('link', $link);
}

if (is_array(Sessao::read('link'))) {
    $_REQUEST = Sessao::read('link');
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link", $link);
}

if ($_REQUEST['stNumAta']) {
    $arAta = explode('/',$_REQUEST['stNumAta']);
    $obTLicitacaoAta->setDado('num_ata'       , $arAta[0]);
    $obTLicitacaoAta->setDado('exercicio_ata' , $arAta[1]);
}

if ($_REQUEST['stNumEdital'] != "") {
    $arEdital = explode('/',$_REQUEST['stNumEdital']);
    if ($arEdital[0] != " ") {
        $obTLicitacaoAta->setDado('num_edital' , $arEdital[0]);
    }
    if ($arEdital[1] != " ") {
        $obTLicitacaoAta->setDado('exercicio'  , $arEdital[1]);
    }
}

$stOrder = " ORDER BY id DESC ";

$rsAta = new RecordSet;
$obTLicitacaoAta->recuperaAta($rsAta, $stFiltro, $stOrder);

$obTable = new Table;
$obTable->setMensagemNenhumRegistro('Nenhum Ata disponível para edição.');
$obTable->setRecordset($rsAta);
$obTable->setSummary('Listagem de Ata');

$obTable->Head->addCabecalho('Ata'    , 30);
$obTable->Head->addCabecalho('Edital' , 30);
$obTable->Head->addCabecalho('Data'   , 40);

$obTable->Body->addCampo('[num_ata]/[exercicio_ata]' , 'C');
$obTable->Body->addCampo('[num_edital]/[exercicio]'  , 'C');
$obTable->Body->addCampo('date'                      , 'C');

$obTable->Body->addAcao('alterar',
                        'listaForm(\'%s\' ,\'%s\', \'%s\' , \'%s\', \'%s\', \'%s\');',
                         array( 'id',
                                'num_ata',
                                'exercicio_ata',
                                'num_edital',
                                'exercicio',
                                $_REQUEST['stAcao']
                              )
                       );

$obTable->montaHTML();
echo $obTable->getHtml();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

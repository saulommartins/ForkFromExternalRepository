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
    * Página que Lista os Processos Fiscais
    * Data de Criacao: 25/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once(CAM_GT_FIS_NEGOCIO."RFISIniciarProcessoFiscal.class.php");
require_once(CAM_GT_FIS_VISAO."VFISIniciarProcessoFiscal.class.php");

//Instanciando a   Classe de Controle e de Visao
$obController = new RFISProcessoFiscal;
$obVisao = new VFISProcessoFiscal($obController);

$obControllerIniciarProcessoFiscal = new RFISIniciarProcessoFiscal;
$obVisaoIniciarProcessoFiscal = new VFISIniciarProcessoFiscal($obControllerIniciarProcessoFiscal);

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
$inTipoFiscalizacao = $_GET['inTipoFiscalizacao'] ?  $_GET['inTipoFiscalizacao'] : $_POST['inTipoFiscalizacao'];

//Define o nome dos arquivos PHP
$stPrograma = "IniciarProcessoFiscal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";
$stCaminho   = CAM_GT_FIS_INSTANCIAS."processoFiscal/";

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar' : $pgProx = $pgForm; break;
    case 'excluir' : $pgProx = $pgProc; break;
    default        : $pgProx = $pgForm; break;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_REQUEST["pg"] and  $_REQUEST["pos"]) {
    $stLink.= "&pg=".$_REQUEST["pg"]."&pos=".$_REQUEST["pos"];
    $link["pg"]  = $_REQUEST["pg"];
    $link["pos"] = $_REQUEST["pos"];
    Sessao::write('link', $link);
}

$rsFiscal = $obVisao->getFiscalLogado();

if ($rsFiscal->getCampo('administrador') == 'f') {
    $_REQUEST['numcgm'] = Sessao::read('numCgm');
}

$link = Sessao::read('link');

$where = $obVisaoIniciarProcessoFiscal->filtrosProcessoFiscal($_REQUEST);

$where.= "GROUP BY pf.cod_processo, tf.cod_tipo, tf.descricao, fc.cod_fiscal, fc.numcgm";

//Switch que monta a pesquisa de acordo com o Tipo de Fiscalização
switch ($inTipoFiscalizacao) {
    case 1:
        $where.=", pfe.inscricao_economica";
        $rsRecordSet = $obVisaoIniciarProcessoFiscal->recuperarListaProcessoFiscalEconomica($where);
        $stTipoInscricao = "Inscrição Econômica";
    break;

    case 2:
        $where.=", pfo.inscricao_municipal";
        $rsRecordSet = $obVisaoIniciarProcessoFiscal->recuperarListaProcessoFiscalObra($where);
        $stTipoInscricao = "Inscrição Municipal";
    break;

    default:
        $condicao = $where.", pfe.inscricao_economica"." # "." where ".$where.",pfo.inscricao_municipal";
        $rsRecordSet = $obVisaoIniciarProcessoFiscal->recuperarListaProcessoFiscaEconomicaObra($condicao);
        $stTipoInscricao = "Inscrição Economica/Municipal";
    break;
}

//echo "<pre>",print_r($rsRecordSet),"</pre>";

//Lista de Processos
$obLista = new Lista;
$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Registros de Processo Fiscal');

$obLista->setRecordSet($rsRecordSet);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Tipo de Fiscalização");
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Processo Fiscal");
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo($stTipoInscricao);
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();
////dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo("[cod_tipo] - [descricao]");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo("cod_processo");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo("inscricao");
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo("&inTipoFiscalizacao", "cod_tipo");
$obLista->ultimaAcao->addCampo("inCodProcesso", "cod_processo");
$obLista->ultimaAcao->addCampo("inInscricao", "inscricao");
$obLista->ultimaAcao->addCampo("inCodFiscal", "cod_fiscal");
$obLista->ultimaAcao->addCampo("numcgm", "numcgm");

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink($stCaminho.$pgProx."?".Sessao::getId().$stLink);
} else {
    $obLista->ultimaAcao->setLink($pgProx."?".Sessao::getId().$stLink);
}
$obLista->commitAcao();
$obLista->show();

//Para corrigir o Cache do Navegador
unset($inTipoFiscalizacao);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
* Arquivo de instância para manutenção de normas
* Data de Criação: 04/09/2005

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 24983 $
$Name$
$Author: domluc $
$Date: 2007-08-21 16:50:15 -0300 (Ter, 21 Ago 2007) $

Casos de uso: uc-01.06.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_PROT_MAPEAMENTO."TPROAssunto.class.php");
$stPrograma = "ManterAssunto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$pgProx = $pgForm;
$stCaminho = CAM_GA_PROT_INSTANCIAS."assunto/";

$stLink   = "";
//GUARDA A POSIÇÃO DA LISTA NA SESSÃO
if ($_GET["pg"] and  $_GET["pos"]) {
    Sessao::write('link_pg',$_GET["pg"]);
    Sessao::write('link_pos',$_GET["pos"]);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(Sessao::read('link')) ) {
    $_REQUEST = Sessao::read('link');
    $_GET["pg"] = Sessao::read('link_pg');
    $_GET["pos"] = Sessao::read('link_pos');
} else {
    $arLink = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
    Sessao::write('link',$arLink);
}

//GERA A LISTA DE ASSUNTOS
$obTPROAssunto = new TPROAssunto;
$stFiltro = ' where cod_classificacao = '.$_REQUEST['inCodigoClassificacao'];
$obTPROAssunto->recuperaTodos($rsAssunto, $stFiltro,'nom_assunto');

//DEFINE A AÇÃO
$stAcao = $_REQUEST['stAcao'] ? $_REQUEST['stAcao'] : 'excluir';

//MONTA O LINK PARA A AÇÃO
$stLink .= '&inCodigoClassificacao='.$_REQUEST['inCodigoClassificacao'];
$stLink .= "&stAcao=".$stAcao;

//GERA A LISTA
$obLista = new Lista;
$obLista->setAjuda('uc-01.06.95');
$obLista->setRecordSet( $rsAssunto );
$obLista->setTitulo('Registros de assunto');

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth('5');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código');
$obLista->ultimoCabecalho->setWidth('10');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição');
$obLista->ultimoCabecalho->setWidth('80');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('');
$obLista->ultimoCabecalho->setWidth('50');
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->ultimoDado->setCampo('cod_assunto');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nom_assunto');
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->setLink($stlink);
$obLista->ultimaAcao->addCampo("&inCodigoAssunto","cod_assunto");
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"nom_assunto");
    $pgProx = $stCaminho.$pgProc;
}
$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();
?>

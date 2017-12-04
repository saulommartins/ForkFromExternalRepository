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
* Lista de condição do assentamento
* Data de Criação: 08/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage

$Revision: 30860 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoVinculado.class.php"      );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCondicaoAssentamento.class.php"       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"               );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"                   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCondicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GRH_PES_INSTANCIAS."assentamento/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

if ($stAcao == "alterar") {
    $pgProx = $pgForm;
} else {
    $pgProx = $pgProc;
}

$obRPessoalVantagem               = new RPessoalVantagem;
$obRPessoalAssentamento1          = new RPessoalAssentamento($obRPessoalVantagem);
$obRPessoalAssentamento2          = new RPessoalAssentamento($obRPessoalVantagem);
$obRPessoalCondicaoAssentamento   = new RPessoalCondicaoAssentamento();
$obRPessoalAssentamentoVinculado  = new RPessoalAssentamentoVinculado( $obRPessoalAssentamento1,$obRPessoalAssentamento2,$obRPessoalCondicaoAssentamento );

$stLink .= '&inCodClassificacao='.$_REQUEST['inCodClassificacaoTxt'];
$stLink .= "&stAcao=".$stAcao;

//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
    $arLink["pg"]  = $_GET["pg"];
    $arLink["pos"] = $_GET["pos"];
}

$rsLista = new RecordSet;
$obRPessoalAssentamentoVinculado->roRPessoalCondicaoAssentamento->addAssentamentoVinculado();
$obRPessoalAssentamentoVinculado->roRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $_REQUEST['inCodClassificacaoTxt'] );
$obRPessoalAssentamentoVinculado->roRPessoalCondicaoAssentamento->listarCondicaoAssentamento( $rsLista );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

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
$obLista->ultimoCabecalho->addConteudo("Sigla");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Assentamento");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_assentamento" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "sigla" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodCondicao","cod_condicao");
$obLista->ultimaAcao->addCampo("&inCodAssentamento","cod_assentamento");
$obLista->ultimaAcao->addCampo("&stTimestampAssentamento","timestamp_assentamento");
$obLista->ultimaAcao->addCampo("&stTimestamp","timestamp");
$obLista->ultimaAcao->addCampo("&stSigla","sigla");
$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();
$obLista->show();

?>

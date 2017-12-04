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
    * Arquivo instância para popup de Fiscal

    * Data de Criação   : 19/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: LSTipoFiscalizacao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.02

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO."TFISTipoFiscalizacao.class.php"                                     );

//Define o nome dos arquivos PHP
$stPrograma = "TipoFiscalizacao";

$pgFilt = "FL".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stJS.= "function insereFiscalizacao(num,prm) {                                                                       \n";
$stJS.= " window.opener.parent.frames['telaPrincipal'].document.frm.inTipoFiscalizacao.value = num;                  \n";
$stJS.= " window.opener.parent.frames['telaPrincipal'].document.getElementById('stTipoFiscalizacao').innerHTML = prm;\n";
$stJS.= " window.opener.parent.frames['telaPrincipal'].document.frm.inTipoFiscalizacao.focus();                      \n";
$stJS.= " window.close();                                                                                            \n";
$stJS.= "}                                                                                                           \n";

$stFiltro = "";
$stLink   = "&campoNum=".$_REQUEST["campoNum"];
$stLink  .= "&boRescindido=".$_REQUEST['boRescindido'];
$stLink  .= "&stTipo=".$_REQUEST["stTipo"];

$stLink .= "&stAcao=".$stAcao;

$stNome = $_REQUEST["campoNom"];

$obTipoFiscalizacao = new TFISTipoFiscalizacao();
$rsTipoFiscalizacao = new RecordSet();

$stFiltro = ' WHERE cod_tipo IN(1,2)';

if ($_REQUEST["campoNom"]) { $stFiltro .= " AND descricao ILIKE UPPER( '%".trim($stNome)."%' ) "; }
$obTipoFiscalizacao->recuperaTipoFiscalizacao( $rsTipoFiscalizacao, $stFiltro );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsTipoFiscalizacao );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_tipo" );
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( $stAcao                            );
$obLista->ultimaAcao->setFuncao( true                               );
$obLista->ultimaAcao->setLink  ( "JavaScript:insereFiscalizacao();" );
$obLista->ultimaAcao->addCampo ( "1","cod_tipo"                     );
$obLista->ultimaAcao->addCampo ( "2","descricao"                    );
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;

$obBtnCancelar = new Button;
$obBtnCancelar->setName               ( 'cancelar'                                         );
$obBtnCancelar->setValue              ( 'Cancelar'                                         );
$obBtnCancelar->obEvento->setOnClick  ( "window.close();"                                  );

$obBtnFiltro = new Button;
$obBtnFiltro->setName                 ( 'filtro'                                           );
$obBtnFiltro->setValue                ( 'Filtro'                                           );
$obBtnFiltro->obEvento->setOnClick    ( "Cancelar('".$pgFilt.$stLink."','telaPrincipal');" );

$obFormulario->defineBarra            ( array( $obBtnCancelar,$obBtnFiltro ) , '', ''      );
$obFormulario->obJavaScript->addFuncao( $stJS                                              );
$obFormulario->show();

?>

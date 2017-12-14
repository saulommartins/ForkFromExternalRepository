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
* Arquivo instância para popup de Estagiário
* Data de Criação: 05/10/2006

* @author Analista: Vandré
* @author Desenvolvedor: Diego Lemos de Souza

$Revision: 32866 $
$Name$
$Author: souzadl $
$Date: 2006-10-06 11:18:17 -0300 (Sex, 06 Out 2006) $

Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCodigoEstagio";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript  = " function insereCodigoEstagio(num,numcgm,nom_cgm) {  \n";
$stFncJavaScript .= " var sNum;                        \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".focus(); \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = numcgm+'-'+nom_cgm; \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obTEstagioEstagiarioEstagio = new TEstagioEstagiarioEstagio();
$stFiltro = "";
$stLink   = "&campoNum=".$request->get("campoNum");
$stLink  .= "&campoNom=".$request->get("campoNom");
$stLink  .= "&nome_cgm=".$request->get("nome_cgm");
if ( !is_null($request->get("nome_cgm")) ) {
    $stFiltro .= " AND nom_cgm like upper('".$request->get("nome_cgm")."%') ";
}

$stLink .= "&stAcao=".$request->get('stAcao');
$rsLista = new RecordSet;
$stOrdem = " nom_cgm";
$obTEstagioEstagiarioEstagio->recuperaCgmDoCodigoEstagio($rsLista,$stFiltro,$stOrdem);
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Estágio");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numero_estagio" );
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereCodigoEstagio();" );
$obLista->ultimaAcao->addCampo("1","numero_estagio");
$obLista->ultimaAcao->addCampo("2","cgm_estagiario");
$obLista->ultimaAcao->addCampo("3","nom_cgm");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;

$obBtnCancelar = new Button;
$obBtnCancelar->setName                 ( 'cancelar'                                        );
$obBtnCancelar->setValue                ( 'Cancelar'                                        );
$obBtnCancelar->obEvento->setOnClick    ( "window.close();"                                 );

$obBtnFiltro = new Button;
$obBtnFiltro->setName                   ( 'filtro'                                          );
$obBtnFiltro->setValue                  ( 'Filtro'                                          );
$obBtnFiltro->obEvento->setOnClick      ( "Cancelar('".$pgFilt.$stLink."','telaPrincipal');"        );

$obFormulario->defineBarra              ( array( $obBtnCancelar,$obBtnFiltro ) , '', ''     );
$obFormulario->obJavaScript->addFuncao  ( $stFncJavaScript                                  );
$obFormulario->show();
?>

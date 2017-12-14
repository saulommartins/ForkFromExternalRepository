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
    * Lista
    * Data de Criação: 10/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-04 13:18:09 -0300 (Seg, 04 Set 2006) $

    * Casos de uso: uc-04.05.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoFGTS";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&nomForm=".$_REQUEST["nomForm"]."&campoNom=".$_REQUEST["campoNom"]."&campoNum=".$_REQUEST["campoNum"]."&stNatureza=".$_REQUEST['stNatureza'];
$pgList = "LS".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php?".Sessao::getId();
$pgProc = "PR".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";

$stFncJavaScript .= " function insereEvento(cod_evento,num,nom,texto) {                      \n";
$stFncJavaScript .= " var sNum;                                                             \n";
$stFncJavaScript .= " var sNom;                                                             \n";
$stFncJavaScript .= " var sTexto;                                                           \n";
$stFncJavaScript .= " sNum = num;                                                           \n";
$stFncJavaScript .= " sNom = nom;                                                           \n";
$stFncJavaScript .= " sTexto = texto;                                                       \n";
$stFncJavaScript .= " d = window.opener.parent.frames['telaPrincipal'].document ;           \n";
$stFncJavaScript .= " d.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom;       \n";
$stFncJavaScript .= " d.".$_REQUEST["nomForm"].".Hdn".$_REQUEST["campoNum"].".value = sNom; \n";
$stFncJavaScript .= " d.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum;    \n";
$stFncJavaScript .= " d.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus();         \n";
$stFncJavaScript .= " window.close();                                                       \n";
$stFncJavaScript .= " }                                                                     \n";

$stCaminho = CAM_GRH_FOL_INSTANCIAS."FGTS/";
$rsLista = new RecordSet;
$obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
$obRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
$obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setNatureza( $_POST['stNatureza'] );
$obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setCodigo( $_POST['inCodEvento'] );
$obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setDescricao( $_POST['stDescricao'] );
$obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setEventoSistema( $_POST['boEventoSistema'] );
$obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEvento( $rsLista );
$rsLista->addFormatacao('valor_quantidade','NUMERIC_BR');
$rsLista->addFormatacao('unidade_quantitativa','NUMERIC_BR');
$obLista = new Lista;
$obLista->setRecordSet          ( $rsLista );
$obLista->setTitulo             ("Eventos Cadastrados");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Evento" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Quantidade" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Texto Complementar" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "codigo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "valor_quantidade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "unidade_quantitativa" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "tipo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "proventos_descontos" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "observacao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "selecionar" );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereEvento();" );
$obLista->ultimaAcao->addCampo( "1"   , "cod_evento" );
$obLista->ultimaAcao->addCampo( "2"   , "codigo" );
$obLista->ultimaAcao->addCampo( "3"   , "descricao");
$obLista->ultimaAcao->addCampo( "4"   , "observacao");
$obLista->ultimaAcao->addCampo( "5"   , "tipo");
$obLista->ultimaAcao->addCampo( "6"   , "fixado");
$obLista->ultimaAcao->addCampo( "7"   , "valor_quantidade");
$obLista->ultimaAcao->addCampo( "8"   , "limite_calculo");
$obLista->ultimaAcao->addCampo( "9"   , "proventos_descontos");
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
$obBtnFiltro->obEvento->setOnClick      ( "Cancelar('".$pgFilt."','telaPrincipal');"        );

$obFormulario->defineBarra              ( array( $obBtnCancelar,$obBtnFiltro ) , '', ''     );
$obFormulario->obJavaScript->addFuncao  ( $stFncJavaScript                                  );
$obFormulario->show();
?>

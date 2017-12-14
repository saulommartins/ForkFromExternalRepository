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
    * Página  para Consulta de Atividades e Elementos da Inscrição Economica
    * Data de Criação: 26/09/2005

    * @author  Marcelo B. Paulino

    * @ignore

    * $Id: FMConsultarAtividades.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.21
*/

/*
$Log$
Revision 1.8  2006/11/20 09:54:18  cercato
bug #7438#

Revision 1.7  2006/09/15 14:32:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"       );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                   );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"             );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarAtividades";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgList = "LSConsultarCadastroEconomico.php";
$pgOcul = "OCConsultarCadastroEconomico.php";
$pgJS   = "JSConsultarCadastroEconomico.js";
include_once( $pgJS   );
include_once( $pgOcul );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// CONSULTA CONFIGURACAO DO MODULO ECONOMICO
$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCEMConfiguracao->getMascaraInscricao();

// RECUPERA OS DADOS DA INSCRICAO ECONOMICA
$obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
$obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inCodInscricao'] );

//processos
$obRCEMInscricaoAtividade->listarProcessosAtividadesCadastroEconomico( $rsProcessos );
$arTmpDados = array();
$inX = 0;

while ( !$rsProcessos->Eof() ) {
    $arTmpDados[ $inX ]["inscricao_economica"] = $rsProcessos->getCampo( "inscricao_economica" );
    $arTmpDados[ $inX ]["ocorrencia_atividade"] = $rsProcessos->getCampo( "ocorrencia_atividade" );
    $arTmpDados[ $inX ]["ano_exercicio"] = $rsProcessos->getCampo( "ano_exercicio" );
    $arTmpDados[ $inX ]["cod_processo"] = $rsProcessos->getCampo( "cod_processo" );
    $arDataHora = explode ( " ", $rsProcessos->getCampo( "timestamp" ) );
    $arData = explode ( "-", $arDataHora[0] );
    $arTmpDados[ $inX ]["stHora"] = $arDataHora[1];
    $arTmpDados[ $inX ]["stData"] = $arData[2]."/".$arData[1]."/".$arData[0];
    $rsProcessos->proximo();
    $inX++;
}

$rsProcessos->preenche( $arTmpDados );
$rsProcessos->setPrimeiroElemento();

//---------

$obRCEMInscricaoAtividade->addAtividade();
$rsAtividades = new RecordSet;
$obRCEMInscricaoAtividade->consultarAtividadesInscricao( $rsAtividades );

// MONTA ARRAY DE ATIVIDADES
$arAtividadesSessao = array();
while ( !$rsAtividades->eof() ) {
    if ( $rsAtividades->getCampo('principal') == 'f' ) {
        $boPrincipal = "Não";
    } else {
        $boPrincipal = "Sim";
    }
    $arAtividadesSessao[] = array(
                                    "inCodigo"    => $rsAtividades->getCampo('cod_atividade'),
                                    "stCodigoEstrutural" => $rsAtividades->getCampo('cod_estrutural'),
                                    "stNome"      => $rsAtividades->getCampo('nom_atividade'),
                                    "boPrincipal" => $boPrincipal
                                    );
    $rsAtividades->proximo();
}

Sessao::write( "atividades", $arAtividadesSessao );

$rsAtividadesLista = new RecordSet;
if ( count($arAtividadesSessao) > 0) {
    $rsAtividadesLista->preenche( $arAtividadesSessao );
}
$stJs = montaListaAtividades( $rsAtividadesLista );

$obRCEMInscricaoAtividade->addAtividade();
$obRCEMInscricaoAtividade->roUltimaAtividade->addAtividadeElemento();
$obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inCodInscricao'] );
$obRCEMInscricaoAtividade->listarElementoAtividadeInscricao( $rsElementos );

// MONTA ARRAY DE ELEMENTOS
$arElementosSessao = array();
while ( !$rsElementos->eof() ) {
    $arElementosSessao[] = array(
                                    "inCodigo" => $rsElementos->getCampo('cod_elemento'),
                                    "stNome"   => $rsElementos->getCampo('nom_elemento')
                                    );
    $rsElementos->proximo();
}

Sessao::write( "elementos", $arElementosSessao );
$rsElementosLista = new RecordSet;
if ( count($arElementosSessao) >0) {
    $rsElementosLista->preenche( $arElementosSessao );
}

$stJs .= montaListaElementos( $rsElementosLista );
$stJs .= montaListaProcessos( $rsProcessos );

SistemaLegado::executaFramePrincipal( $stJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl  );

$obHdnInscricao = new Hidden;
$obHdnInscricao->setName ( 'inCodInscricao' );
$obHdnInscricao->setValue( $_REQUEST['inCodInscricao'] );

$obSpnListaAtividades = new Span;
$obSpnListaAtividades->setId( "lsListaAtividades" );

$obSpnVisualizarAtividade = new Span;
$obSpnVisualizarAtividade->setId( "spnVisualizarAtividade" );

$obSpnListaElementos = new Span;
$obSpnListaElementos->setId( "lsListaElementos" );

$obSpnVisualizarElemento = new Span;
$obSpnVisualizarElemento->setId( "spnVisualizarElemento" );

$obSpnListaProcessos = new Span;
$obSpnListaProcessos->setId( "lsListaProcessos" );

$obSpnVisualizarProcesso = new Span;
$obSpnVisualizarProcesso->setId( "spnVisualizarProcesso" );

$obSpnVisualizarAtividadeProcesso = new Span;
$obSpnVisualizarAtividadeProcesso->setId( "spnVisualizarAtividadeProcesso" );

$obButtonVoltar = new Button;
$obButtonVoltar->setName ( "Voltar" );
$obButtonVoltar->setValue( "Voltar" );
$obButtonVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

// DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm  ( $obForm    );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnInscricao );

$obFormulario->addAba ( "Atividades"              );
$obFormulario->addSpan( $obSpnListaAtividades     );
$obFormulario->addSpan( $obSpnVisualizarAtividade );

$obFormulario->addAba ( "Elementos"               );
$obFormulario->addSpan( $obSpnListaElementos      );
$obFormulario->addSpan( $obSpnVisualizarElemento  );

$obFormulario->addAba ( "Histórico"               );
$obFormulario->addSpan( $obSpnListaProcessos      );
$obFormulario->addSpan( $obSpnVisualizarProcesso  );
$obFormulario->addSpan( $obSpnVisualizarAtividadeProcesso );

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );

$obFormulario->show();

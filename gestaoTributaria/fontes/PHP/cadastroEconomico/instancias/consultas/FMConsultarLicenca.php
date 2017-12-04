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
    * Página  para Consulta de Licença
    * Data de Criação: 27/09/2005

    * @author  Marcelo B. Paulino

    * @ignore

    * $Id: FMConsultarLicenca.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.21
*/

/*
$Log$
Revision 1.8  2007/03/15 14:26:24  cercato
alterando formulario para apresentar a situacao na lista de licencas.

Revision 1.7  2007/03/02 14:44:22  dibueno
Bug #7676#

Revision 1.6  2006/09/15 14:32:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php"             );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeInscricao.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"        );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarLicencas";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgList = "LSConsultarCadastroEconomico.php";
$pgOcul = "OCConsultarCadastroEconomico.php";
$pgJS   = "JSConsultarCadastroEconomico.js";
$pgImprimirLicenca = "../emissao/OCManterEmissao.php";
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
$obRCEMLicenca = new RCEMLicenca;
$obRCEMLicenca->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inCodInscricao'] );
$obRCEMLicenca->listarLicencasConsulta( $rsLicenca );

//MONTA LISTA DE LICENÇAS
$arLicencasSessao = array();
while ( !$rsLicenca->eof() ) {
//    if ( $rsLicenca->getCampo('cod_processo') AND $rsLicenca->getCampo('exercicio_processo') ) {
//        $stProcesso = $rsLicenca->getCampo('cod_processo')."/".$rsLicenca->getCampo('exercicio_processo');
    if ( $rsLicenca->getCampo('processo') ) {
        $stProcesso = $rsLicenca->getCampo('processo');
    } else {
        $stProcesso = "&nbsp;";
    }

    $arLicencasSessao[] = array (
                                    "stSituacao"    => $rsLicenca->getCampo('situacao'),
                                    "inCodigo"      => $rsLicenca->getCampo('cod_licenca'),
                                    "inExercicio"   => $rsLicenca->getCampo('exercicio'),
                                    "stProcesso"    => $stProcesso,
                                    "stEspecie"     => $rsLicenca->getCampo('especie_licenca'),
                                    "inCodDocumento"  => $rsLicenca->getCampo('cod_documento'),
                                    "inCodTipoDocumento"  => $rsLicenca->getCampo('cod_tipo_documento'),
                                    "inInscricaoEconomica"=> $_REQUEST['inCodInscricao'],
                                    "nome_arquivo_template" => $rsLicenca->getCampo('nome_arquivo_template'),
                                    "nome_documento" => $rsLicenca->getCampo('nome_documento')
                                    );
    $rsLicenca->proximo();
}

Sessao::write( "licencas", $arLicencasSessao );

$rsLicencaLista = new RecordSet;
if ( count( $arLicencasSessao ) > 0) {
    $rsLicencaLista->preenche( $arLicencasSessao );
}

$stJs = montaListaLicencas( $rsLicencaLista );

// RECUPERA OS DADOS DAS MODALIDADES RELACIONADAS A INSCRICAO ECONOMICA
$obRCEMModalidadeInscricao = new RCEMModalidadeInscricao;
$obRCEMModalidadeInscricao->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inCodInscricao'] );
$obRCEMModalidadeInscricao->listarModalidadeAtividadeInscricao( $rsModalidades, $boTransacao, false );

$arModalidadesSessao = array();

if ( $rsModalidades->Eof() ) {
    $obRCEMModalidadeInscricao->listarModalidadeAtividadeLancamento( $rsModalidadesAtividade );
    while ( !$rsModalidadesAtividade->eof() ) {
        $arModalidadesSessao[] = array (
            "inCodigo"     => $rsModalidadesAtividade->getCampo('cod_modalidade'),
            "stModalidade" => $rsModalidadesAtividade->getCampo('nom_modalidade')
        );

        $rsModalidadesAtividade->proximo();
    }
} else {
    while ( !$rsModalidades->eof() ) {
        $arModalidadesSessao[] = array (
            "inCodigo"     => $rsModalidades->getCampo('cod_modalidade'),
            "stModalidade" => $rsModalidades->getCampo('nom_modalidade')
        );

        $rsModalidades->proximo();
    }
}

Sessao::write( "modalidades", $arModalidadesSessao );
$rsModalidadesLista = new RecordSet;
if ( count( $arModalidadesSessao)> 0 ) {
    $rsModalidadesLista->preenche( $arModalidadesSessao );
}
$stJs .= montaListaModalidades( $rsModalidadesLista );

SistemaLegado::executaFramePrincipal( $stJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"]  );

$obSpnListaLicencas = new Span;
$obSpnListaLicencas->setId( "lsListaLicencas" );

$obSpnVisualizarLicenca = new Span;
$obSpnVisualizarLicenca->setId( "spnVisualizarLicenca" );

$obSpnListaModalidades = new Span;
$obSpnListaModalidades->setId( "lsListaModalidades" );

$obSpnVisualizarModalidade = new Span;
$obSpnVisualizarModalidade->setId( "spnVisualizarModalidade" );

$obButtonVoltar = new Button;
$obButtonVoltar->setName ( "Voltar" );
$obButtonVoltar->setValue( "Voltar" );
$obButtonVoltar->obEvento->setOnClick( "Cancelar('".$_REQUEST["stLocation"]."');" );

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

// DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm  ( $obForm    );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );

$obFormulario->addAba ( "Licenças"              );
$obFormulario->addSpan( $obSpnListaLicencas     );
$obFormulario->addSpan( $obSpnVisualizarLicenca );

$obFormulario->addAba ( "Modalidades de Lançamento" );
$obFormulario->addSpan( $obSpnListaModalidades      );
$obFormulario->addSpan( $obSpnVisualizarModalidade  );

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );

$obFormulario->show();

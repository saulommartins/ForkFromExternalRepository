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
    * Página  para Consulta de Responsaveis Tecnicos e Contabil da Inscrição Economica
    * Data de Criação: 26/09/2005

    * @author  Marcelo B. Paulino

    * @ignore

    * $Id: FMConsultarResponsaveis.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.21
*/

/*
$Log$
Revision 1.7  2007/03/19 15:53:10  cercato
Bug #8774#

Revision 1.6  2006/09/15 14:32:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarResponsaveis";
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
$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
$obRCEMInscricaoEconomica->setInscricaoEconomica                                     ( $_REQUEST['inCodInscricao']          );
$obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->setNumCGM                       ( $_REQUEST['inCGMRespContabil']       );
$obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->obRProfissao->setCodigoProfissao( $_REQUEST['inProfissaoRespContabil'] );

if ($_REQUEST['inCGMRespContabil']) {
    $obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->listarResponsavelTecnico ( $rsRespContabil );
} else {
    $rsRespContabil = new RecordSet;
}

$obRCEMInscricaoEconomica->listarResponsaveisInscricao                        ( $rsRespTecnico  );

// MONTA ARRAY DE RESPONSAVEIS TECNICOS
$arRespTecnicoSessao = array();
while ( !$rsRespTecnico->eof() ) {
    $stRegistro = $rsRespTecnico->getCampo('nom_conselho')." - ".$rsRespTecnico->getCampo('num_registro')." - ".$rsRespTecnico->getCampo('sigla_uf');
    $arRespTecnicoSessao[] = array(
                                    "inNumCGM"    => $rsRespTecnico->getCampo('numcgm'),
                                    "stNomCGM"    => $rsRespTecnico->getCampo('nom_cgm'),
                                    "stProfissao" => $rsRespTecnico->getCampo('nom_profissao'),
                                    "stRegistro"  => $stRegistro
                                    );
    $rsRespTecnico->proximo();
}

Sessao::write( "respTecnico", $arRespTecnicoSessao );
$rsRespTecnicoLista = new RecordSet;
if ( count($arRespTecnicoSessao)> 0 ) {
    $rsRespTecnicoLista->preenche( $arRespTecnicoSessao );
}
$stJs = montaListaRespTecnico( $rsRespTecnicoLista );
SistemaLegado::executaFramePrincipal( $stJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"] );

$obLblRespContabil = new Label;
$obLblRespContabil->setRotulo ( "Responsável Contábil" );
$obLblRespContabil->setValue  ( $rsRespContabil->getCampo("numcgm")." - ".$rsRespContabil->getCampo("nom_cgm") );

$obLblProfissao = new Label;
$obLblProfissao->setRotulo    ( "Profissão" );
$obLblProfissao->setValue     ( $rsRespContabil->getCampo("nom_profissao") );

$obLblRegistro = new Label;
$obLblRegistro->setRotulo     ( "Registro" );
$obLblRegistro->setValue      ( $rsRespContabil->getCampo("num_registro")." - ".$rsRespContabil->getCampo("sigla_uf") );

$obSpnListaRespTecnico = new Span;
$obSpnListaRespTecnico->setId( "lsListaRespTecnico" );

$obButtonVoltar = new Button;
$obButtonVoltar->setName ( "Voltar" );
$obButtonVoltar->setValue( "Voltar" );
$obButtonVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

// DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm    );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( "Dados de Responsáveis" );

$obFormulario->addComponente( $obLblRespContabil );
$obFormulario->addComponente( $obLblProfissao    );
$obFormulario->addComponente( $obLblRegistro     );

$obFormulario->addSpan( $obSpnListaRespTecnico );

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );

$obFormulario->show();

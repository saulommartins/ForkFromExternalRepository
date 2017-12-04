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
    * Página de Formulario de Definiçao de Responsáveis para uma Inscrição Econômica
    * Data de Criação   : 18/04/2005

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMDefinirResponsaveis.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.14  2007/03/27 19:28:51  rodrigo
Bug #8768#

Revision 1.13  2006/09/15 14:33:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RResponsavelTecnico.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "DefinirResponsaveis";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
Sessao::write( 'responsaveis', array() );
$arResponsaveisSessao = array();
//--------------------------------------------------- LISTA DE ATIVIDADES PARA FILTRAR OS RESPONSAVEIS
//MONTAGEM DA LISTA DE ATIVIDADE JÁ CADASTRADAS
$arAtividadesSessao = Sessao::read( "Atividades" );

$obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
$obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );

$obRCEMInscricaoAtividade->addAtividade();
$rsAtividades = new RecordSet;
$obRCEMInscricaoAtividade->consultarAtividadesInscricao( $rsAtividades );

if ( count ( $rsAtividades ) > 0 ) {
    $cont = 0;
    $arAtividades = '';
    while ( $cont < $rsAtividades->getNumLinhas() ) {
        if ( $cont != 0 )
            $arAtividades .=  ','.$rsAtividades->getCampo('cod_atividade');
        else
            $arAtividades .=  $rsAtividades->getCampo('cod_atividade');

        //$arAtividades .=  $rsAtividades->getCampo('cod_atividade'). ',';
        $rsAtividades->proximo();
        $cont++;
    }
}
//--------------------------------------------------- LISTA DE ATIVIDADES PARA FILTRAR OS RESPONSAVEIS

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( isset($_REQUEST["stCtrl"]) ? $_REQUEST["stCtrl"] : "" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( isset($_REQUEST["stAcao"]) ? $_REQUEST["stAcao"] : "");

$obHdnLinha = new Hidden;
$obHdnLinha->setName( "inLinha" );

$obHdnCodigoResponsavel = new Hidden;
$obHdnCodigoResponsavel->setName  ( "inCodigoResponsavel" );
$obHdnCodigoResponsavel->setValue ( isset($_REQUEST["inCodigoResponsavel"]) ? $_REQUEST["inCodigoResponsavel"] : "");

$obHdnAtividadesInscricao = new Hidden;
$obHdnAtividadesInscricao->setName  ( "arAtividades" );
$obHdnAtividadesInscricao->setValue ( $arAtividades );

$obHdnCodProfissao = new Hidden;
$obHdnCodProfissao->setName ( "inCodProfissao" );
$obHdnCodProfissao->setValue( isset($_REQUEST["inCodProfissao"]) ? $_REQUEST["inCodProfissao"] : "");

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName  ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue ( isset($_REQUEST["inInscricaoEconomica"]) ? $_REQUEST["inInscricaoEconomica"]  : "");

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName ( "inCGM" );
$obHdnNumCGM->setValue( isset($_REQUEST["inCGM"]) ? $_REQUEST["inCGM"] : "");

$obHdnNomCGM = new Hidden;
$obHdnNomCGM->setName ( "stCGM" );
$obHdnNomCGM->setValue( isset($_REQUEST["stCGM"]) ? $_REQUEST["stCGM"] : "");

$obLblCGM = new Label;
$obLblCGM->setName  ( "inNumCGMInscricao" );
$obLblCGM->setValue ( isset($_REQUEST['inCGM']) ? $_REQUEST['inCGM'] : "");
$obLblCGM->setRotulo( "CGM"               );

$obLblNomeCCGM = new Label;
$obLblNomeCCGM->setName ( "stNomeCGM"       );
$obLblNomeCCGM->setValue( isset($_REQUEST['stCGM']) ? $_REQUEST['stCGM'] : "");
$obLblNomeCCGM->setRotulo( "CGM"            );

$obLblInscricao = new Label;
$obLblInscricao->setName ( "inInscricaoEconomica"  );
$obLblInscricao->setValue( isset($_REQUEST['inInscricaoEconomica']) ? $_REQUEST['inInscricaoEconomica'] : "");
$obLblInscricao->setRotulo( "Inscrição Econômica"  );

//----------------------------------------------------------------------------------------------------------------

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo    ( "*CGM"                            );
$obBscCGM->setTitle     ( "Busca Profissional no CGM"       );
$obBscCGM->setId        ( "inNomCGM"                        );
$obBscCGM->obCampoCod->setName  ( "inNumCGM"    );
$obBscCGM->obCampoCod->setValue ( isset($_REQUEST["inNumCGM"]) ? $_REQUEST["inNumCGM"] : "" );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaValor('buscaProfissao');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GT_CEM_POPUPS."responsaveltecnico/FLProcurarResponsavel.php','frm','inNumCGM&stProfissao=stProfissao&inCodProfissao=inCodProfissao&AtividadesInscricao=". $arAtividades ."','inNomCGM','Profissao','".Sessao::getId()."','800','550');" );

$obLblProfissao = new Label;
$obLblProfissao->setName   ( "stProfissao" );
$obLblProfissao->setValue  ( isset($_REQUEST["stProfissao"]) ? $_REQUEST["stProfissao"] : "" );
$obLblProfissao->setId     ( "stProfissao" );
$obLblProfissao->setRotulo ( "Profissão"   );

$obHdnProfissao = new Hidden;
$obHdnProfissao->setName   ( "stProfissao" );
$obHdnProfissao->setValue  ( isset($_REQUEST["stProfissao"]) ? $_REQUEST["stProfissao"] : "" );
$obHdnProfissao->setRotulo ( "stProfissao" );

//$obRResponsavel = new RResponsavelTecnico;
//$obRResponsavel->listarResponsaveis( $rsResponsaveis );

if ($_REQUEST["stAcao"] == "def_resp") {
    $boChecked = true;
} else {
    $boChecked = false;
}
$obCheckSegueElementos = new CheckBox;
$obCheckSegueElementos->setName        ( "boSegueElementos"                    );
$obCheckSegueElementos->setValue       ( "1"                                   );
$obCheckSegueElementos->setLabel       ( "Seguir para Definição de Elementos?" );
$obCheckSegueElementos->setNull        ( true                                  );
$obCheckSegueElementos->setChecked     ( $boChecked                            );

$obBtnIncluirResponsavel = new Button;
$obBtnIncluirResponsavel->setName  ( "btnIncluirResponsavel" );
$obBtnIncluirResponsavel->setValue ( "Incluir"               );
$obBtnIncluirResponsavel->obEvento->setOnClick( "buscaValor('montaResponsavel');" );

$obBtnLimparResponsavel= new Button;
$obBtnLimparResponsavel->setName               ( "btnLimparResponsavel" );
$obBtnLimparResponsavel->setValue              ( "Limpar"               );
$obBtnLimparResponsavel->obEvento->setOnClick( "buscaValor('limparResponsavel');" );

$obSpnListaResponsavel = new Span;
$obSpnListaResponsavel->setId( "lsListaResponsavel" );

$rsResponsaveis = new RecordSet;
$obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
$obRCEMInscricaoEconomica->listarResponsaveisCadastro( $rsResponsaveis );

$inCount = 0;
while ( !$rsResponsaveis->eof() ) {
    $arTmp['inNumCGM']          = $rsResponsaveis->getCampo( "numcgm" );
    $arTmp['inNomCGM']          = $rsResponsaveis->getCampo( "nom_cgm" );
    $arTmp['inRegistro']        = $rsResponsaveis->getCampo( "nom_registro" )." ".$rsResponsaveis->getCampo( "num_registro" )." ".$rsResponsaveis->getCampo( "sigla_uf" );
    $arTmp['stProfissao']       = $rsResponsaveis->getCampo( "nom_profissao" );
    $arTmp['inCodigoProfissao'] = $rsResponsaveis->getCampo( "cod_profissao" );
    $arTmp['sequencia']         = $rsResponsaveis->getCampo( "sequencia"     );
    $arTmp['inLinha']           = $inCount;

    ++$inCount;
    $arResponsaveisSessao[] = $arTmp;
    $rsResponsaveis->proximo();
}

Sessao::write( "responsaveis", $arResponsaveisSessao );
//Sessao::write( "arProfissoes", $arResponsaveisSessao );

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.10");
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnLinha );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnNumCGM );
$obFormulario->addHidden    ( $obHdnNomCGM );
$obFormulario->addHidden    ( $obHdnInscricaoEconomica );
$obFormulario->addHidden    ( $obHdnProfissao );
$obFormulario->addHidden    ( $obHdnCodProfissao );

$obFormulario->addHidden    ( $obHdnAtividadesInscricao );

$obFormulario->addTitulo    ( "Dados da Inscrição Econômica" );
$obFormulario->agrupaComponentes( array( $obLblCGM, $obLblNomeCCGM) );
$obFormulario->addComponente( $obLblInscricao );
$obFormulario->addTitulo    ( "Responsáveis Técnicos");
$obFormulario->addComponente( $obBscCGM );
$obFormulario->addComponente( $obLblProfissao );
$obFormulario->agrupaComponentes( array( $obBtnIncluirResponsavel, $obBtnLimparResponsavel ) );
$obFormulario->addSpan      ( $obSpnListaResponsavel );
$obFormulario->addComponente( $obCheckSegueElementos );
$obFormulario->Ok();
$obFormulario->show();

sistemaLegado::executaFrameOculto("buscaValor('carregaResponsaveis');");

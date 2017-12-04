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
    * Data de Criação: 15/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 27360 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-01-03 15:16:13 -0200 (Qui, 03 Jan 2008) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.1  2007/10/17 13:41:48  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioApolice.class.php" );

$stPrograma = "ManterApolice";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ($stAcao == 'alterar') {
    $obTPatrimonioApolice = new TPatrimonioApolice();
    $obTPatrimonioApolice->setDado('cod_apolice', $_REQUEST['inCodApolice'] );
    $obTPatrimonioApolice->recuperaApolices( $rsApolice );

    $rsApolice->addFormatacao( 'valor_apolice', 'NUMERIC_BR' );
    $rsApolice->addFormatacao( 'valor_franquia', 'NUMERIC_BR' );
} else {
    $rsApolice = new RecordSet();
}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget('oculto');
$obForm->setEncType('multipart/form-data');

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

if ($stAcao == 'alterar') {
    //instancia um hidden para o cod_apolice
    $obHdnCodApolice = new Hidden();
    $obHdnCodApolice->setName( 'inCodApolice');
    $obHdnCodApolice->setValue( $rsApolice->getCampo('cod_apolice') );

    $obHdnNumApolice = new Hidden();
    $obHdnNumApolice->setName( 'hdnNumApolice' );
    $obHdnNumApolice->setValue( $rsApolice->getCampo('num_apolice') );
}

//instancia um componente para o numero da apolice
$obTxtNumApolice = new TextBox();
$obTxtNumApolice->setName( 'stNumApolice' );
$obTxtNumApolice->setRotulo( 'Número' );
$obTxtNumApolice->setTitle( 'Informe o número da apólice.' );
$obTxtNumApolice->setNull( false );
$obTxtNumApolice->setMaxLength( 15 );
$obTxtNumApolice->setSize( 15 );
$obTxtNumApolice->setValue( $rsApolice->getCampo('num_apolice') );

//instancia um busca inner para a seguradora
$obIPopUpCGM = new IPopUpCGMVinculado( $obForm );
$obIPopUpCGM->setRotulo           ( 'Seguradora'     );
$obIPopUpCGM->setTitle            ( 'Informe a seguradora.' );
$obIPopUpCGM->setTabelaVinculo    ( 'sw_cgm_pessoa_juridica' );
$obIPopUpCGM->setCampoVinculo     ( 'numcgm' );
$obIPopUpCGM->setNomeVinculo      ( 'seguradora' );
$obIPopUpCGM->setName             ( 'stNomCGM'       );
$obIPopUpCGM->setId               ( 'stNomCGM'       );
$obIPopUpCGM->obCampoCod->setName ( 'inNumCGM'       );
$obIPopUpCGM->obCampoCod->setId   ( 'inNumCGM'       );
$obIPopUpCGM->setNull             ( false            );
$obIPopUpCGM->setValue            ( $rsApolice->getCampo('nom_seguradora') );
$obIPopUpCGM->obCampoCod->setValue( $rsApolice->getCampo('num_seguradora') );

//instancia um componente data para o início da vigencia
$obDtInicioVigencia = new Data();
$obDtInicioVigencia->setName( 'dtInicioVigencia' );
$obDtInicioVigencia->setRotulo( 'Data de Início da vigência' );
$obDtInicioVigencia->setTitle( 'Informe a data de início da vigência da apólice.' );
$obDtInicioVigencia->setNull( false );
$obDtInicioVigencia->setValue( $rsApolice->getCampo('inicio_vigencia') );

//instancia um componente data para o vencimento
$obDtVencimento = new Data();
$obDtVencimento->setName( 'dtVencimento' );
$obDtVencimento->setRotulo( 'Data de Vencimento' );
$obDtVencimento->setTitle( 'Informe a data de vencimento da apólice.' );
$obDtVencimento->setNull( false );
$obDtVencimento->setValue( $rsApolice->getCampo('dt_vencimento') );

//instancia um componente data para o vencimento
$obDtAssinatura = new Data();
$obDtAssinatura->setName( 'dtAssinatura' );
$obDtAssinatura->setRotulo( 'Data de Assinatura' );
$obDtAssinatura->setTitle( 'Informe a data de assinatura da apólice.' );
$obDtAssinatura->setNull( false );
$obDtAssinatura->setValue( $rsApolice->getCampo('dt_assinatura') );

// Define Objeto Moeda para Valor
$obValorApolice = new Moeda;
$obValorApolice->setName     ( "nuValor" );
$obValorApolice->setId       ( "nuValor" );
$obValorApolice->setValue    ( $rsApolice->getCampo('valor_apolice')  );
$obValorApolice->setRotulo   ( "Valor da apólice"   );
$obValorApolice->setTitle    ( "Informe o Valor da apólice" );
$obValorApolice->setMaxLength( 15 );
$obValorApolice->setSize     ( 22 );
$obValorApolice->setNegativo ( false );

// Define Objeto Moeda para Valor Franquia
$obValorFranquia = new Moeda;
$obValorFranquia->setName     ( "nuValorFranquia" );
$obValorFranquia->setId       ( "nuValorFranquia" );
$obValorFranquia->setValue    ( $rsApolice->getCampo('valor_franquia')  );
$obValorFranquia->setRotulo   ( "Valor da franquia"   );
$obValorFranquia->setTitle    ( "Informe o Valor da franquia" );
$obValorFranquia->setMaxLength( 15 );
$obValorFranquia->setSize     ( 22 );
$obValorFranquia->setNegativo ( false );

// Define Objeto TextBox para Observações
$obTxtObservacoes = new TextArea;
$obTxtObservacoes->setName   ( "stObservacoes" );
$obTxtObservacoes->setId     ( "stObservacoes" );
$obTxtObservacoes->setValue  ( $rsApolice->getCampo('observacoes')  );
$obTxtObservacoes->setRotulo ( "Observações" );
$obTxtObservacoes->setTitle  ( "Informe as observações" );
$obTxtObservacoes->setNull   ( true );
$obTxtObservacoes->setRows   ( 4 );

//instancia um componente para o contato
$obTxtContato = new TextBox();
$obTxtContato->setName( 'stContato' );
$obTxtContato->setRotulo( 'Corretor' );
$obTxtContato->setTitle( 'Informe o corretor da apólice.' );
$obTxtContato->setNull( false );
$obTxtContato->setSize( 40 );
$obTxtContato->setMaxLength( 40 );
$obTxtContato->setValue( $rsApolice->getCampo('contato') );

if ($rsApolice->getCampo('nome_arquivo')) {
    $stCaminhoCompleto = $rsApolice->getCampo('nome_arquivo');
}

$obLnknApolice = new Link;
$obLnknApolice->setRotulo ("Apólice");
$obLnknApolice->setValue  ("Download");
$obLnknApolice->setTarget ("oculto");
$obLnknApolice->setHref   ('DWManterApolice.php?sim=sim&arq='.$stCaminhoCompleto);

$obArquivo = new FileBox;
$obArquivo->setRotulo   ( "Caminho" );
$obArquivo->setName     ( "stArquivo" );
$obArquivo->setId       ( "stArquivo" );
$obArquivo->setSize     ( 40 );
$obArquivo->setNull     ( true );
$obArquivo->setMaxLength( 100 );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-03.01.08');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnCodApolice );
    $obFormulario->addHidden( $obHdnNumApolice );
}

$obFormulario->addTitulo    ( 'Dados da Apolice' );
$obFormulario->addComponente( $obTxtNumApolice );
$obFormulario->addComponente( $obIPopUpCGM );
$obFormulario->addComponente( $obDtInicioVigencia );
$obFormulario->addComponente( $obDtVencimento );
$obFormulario->addComponente( $obDtAssinatura );
$obFormulario->addComponente( $obValorApolice );
$obFormulario->addComponente( $obValorFranquia );
$obFormulario->addComponente( $obTxtObservacoes );
$obFormulario->addComponente( $obTxtContato );
if ($rsApolice->getCampo('nome_arquivo')) {
    $obFormulario->addComponente( $obLnknApolice );
}
$obFormulario->addComponente( $obArquivo );

if ($stAcao == 'incluir') {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

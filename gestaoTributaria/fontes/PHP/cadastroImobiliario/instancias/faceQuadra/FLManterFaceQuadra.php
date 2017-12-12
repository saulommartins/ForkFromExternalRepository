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
    * Página de Formulario de filtro de face de quadra
    * Data de Criação   : 26/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: FLManterFaceQuadra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.14  2006/09/18 10:30:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMFaceQuadra.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php");
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php");
include_once(CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterFaceQuadra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::remove('link');

$obRCIMNivel        = new RCIMNivel;
$obRCIMNivel->recuperaVigenciaAtual( $rsVigenciaAtual );

$obRCIMNivel->setCodigoVigencia( $rsVigenciaAtual->getCampo("cod_vigencia") );
$obRCIMNivel->recuperaUltimoNivel( $rsUltimoNivel );
$inCodigoNivel2 = $rsUltimoNivel->getCampo("cod_nivel")+1;
Sessao::write('inCodigoNivel2', $inCodigoNivel2);

$obRCIMFaceQuadra = new RCIMFaceQuadra;
$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setObrigatorio( false );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obHdnNomeLogradouro = new Hidden;
$obHdnNomeLogradouro->setName( "stNomeLogradouro" );
$obHdnNomeLogradouro->setValue( $_REQUEST["stNomeLogradouro"] );

$obTxtCodigoFace = new TextBox;
$obTxtCodigoFace->setName      ( "inCodigoFace" );
$obTxtCodigoFace->setTitle     ( "Informe o código da face de quadra");
$obTxtCodigoFace->setRotulo    ( "Código da Face de Quadra" );
$obTxtCodigoFace->setMaxLength ( 20 );
$obTxtCodigoFace->setSize      ( 10 );
$obTxtCodigoFace->setInteiro   ( true );

$obBscLogradouro = new BuscaInner;
$obBscLogradouro->setRotulo                       ( "Logradouro"                                       );
$obBscLogradouro->setTitle                        ( "Logradouro onde a face de quadra está localizada" );
$obBscLogradouro->setId                           ( "campoInnerLogr"                                       );
$obBscLogradouro->obCampoCod->setName             ( "inNumLogradouro"                                  );
$obBscLogradouro->obCampoCod->setValue            ( $inNumLogradouro                                   );
$obBscLogradouro->obCampoCod->obEvento->setOnChange ( "BloqueiaFrames(true,false);buscaLogradouro();"  );
$stBusca  = "abrePopUp('../../popups/logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro',";
$stBusca .= "'campoInnerLogr','juridica','".Sessao::getId()."','800','550')";
$obBscLogradouro->setFuncaoBusca                  ( $stBusca                                           );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick    ( "submeteFiltro();" );

$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick( "LimparFL();" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                        );
$obFormulario->setAjuda ( "UC-05.01.07" );
$obFormulario->addHidden            ( $obHdnCtrl                     );
$obFormulario->addHidden            ( $obHdnNomeLogradouro           );
$obFormulario->addTitulo            ( "Dados para Filtro"            );
$obFormulario->addHidden            ( $obHdnAcao                     );
$obMontaLocalizacao->geraFormulario ( $obFormulario                  );
$obFormulario->addComponente        ( $obTxtCodigoFace               );
$obFormulario->addComponente        ( $obBscLogradouro               );
$obFormulario->defineBarra          ( array( $obBtnOK, $obBtnLimpar) );
    $obFormulario->show();

SistemaLegado::executaFrameOculto("f.stChaveLocalizacao.focus();");

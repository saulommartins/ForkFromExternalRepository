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
    * Página de Formulario para Permissoes
    * Data de Criação   : 30/05/2005

    * @author Analista      : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor : Lucas Teixeira Stephanou

    * @ignore

    * $Id: FMManterPermissoes.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.9  2006/09/15 11:10:42  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRPermissao.class.php"    );
include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php"    );
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterGrupo";
$pgFilt = "FLManterPermissoes.php";
$pgList =  "LS".$stPrograma.".php";
$pgForm =  "FM".$stPrograma.".php";
$pgProc =  "PR".$stPrograma.".php";
$pgOcul = "OCManterPermissoes.php";
$pgJs   =  "JSManterPermissoes.js" ;
include_once( $pgJs );

$stAcao = $request->get('stAcao');

// CONSULTAR CGM
$obMontaGrupoCredito = new MontaGrupoCredito;

$obRUsuario= new RUsuario;
$obRUsuario->obRCGM->setNumCGM( $_REQUEST[ "inNumCGM" ] );
$obRUsuario->consultarUsuario( $rsUsuario);
if ( $rsUsuario->getNumLinhas() > 0) {
    $NomeUsuarioCGM = $rsUsuario->getCampo ("nom_cgm")         ;
    $UsuarioCGM     = $rsUsuario->getCampo ("username")        ;
}

// limpar array de grupo
Sessao::write( "grupos", array() );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCGM = new Hidden;
$obHdnCGM->setName( "inNumCGM" );
$obHdnCGM->setValue( $_REQUEST["inNumCGM"] );

$obLblCGM = new Label;
$obLblCGM->setRotulo ( "CGM" );
$obLblCGM->setTitle  ( "Número do CGM." );
$obLblCGM->setName   ( "stNumCGM"    );
$obLblCGM->setId     ( "stNumCGM"    );
$obLblCGM->setValue  ( $_REQUEST["inNumCGM"]     );

$obSpnGrupos  = new Span;
$obSpnGrupos->setId  ( "spnGrupos");

$obLblUsuario = new Label;
$obLblUsuario->setRotulo ( "Usuário" );
$obLblUsuario->setTitle  ( "Usuário do CGM." );
$obLblUsuario->setName   ( "stUsername"    );
$obLblUsuario->setId     ( "stUsername"    );
$obLblUsuario->setValue  ( $UsuarioCGM     );

$obLblNomeUsuario = new Label;
$obLblNomeUsuario->setRotulo ( "Nome" );
$obLblNomeUsuario->setTitle  ( "Nome do usuário do CGM." );
$obLblNomeUsuario->setName   ( "stNomeUsuario"    );
$obLblNomeUsuario->setId     ( "stNomeUsuario"    );
$obLblNomeUsuario->setValue  ( $NomeUsuarioCGM    );

// campos para creditos e acrescimos

$obBscGrupo = new BuscaInner;
$obBscGrupo->setRotulo( "*Grupo de Créditos" );
$obBscGrupo->setTitle( "Busca grupos de créditos." );
$obBscGrupo->setId( "stGrupo" );
$obBscGrupo->obCampoCod->setName("inCodGrupo");
$obBscGrupo->obCampoCod->setValue( $inCodGrupo );
$obBscGrupo->obCampoCod->obEvento->setOnChange("buscaValor('buscaGrupo');");
$obBscGrupo->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupo','stGrupo','todos','".Sessao::getId()."','800','550');" );

$obBtnIncluirGrupo = new Button;
$obBtnIncluirGrupo->setName( "stIncluirGrupo" );
$obBtnIncluirGrupo->setValue( "Incluir" );
$obBtnIncluirGrupo->obEvento->setOnClick( "incluirGrupo();" );

$obBtnLimparGrupo= new Button;
$obBtnLimparGrupo->setName( "stLimparGrupo" );
$obBtnLimparGrupo->setValue( "Limpar" );
$obBtnLimparGrupo->obEvento->setOnClick( "limparGrupo();" );

$obHdnExercicio  = new Hidden;
$obHdnExercicio->setName   ( "inExercicio" );
$obHdnExercicio->setValue  ( $inExercicio  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc     );
$obForm->setTarget( "oculto"    );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                   );
$obFormulario->addHidden            ( $obHdnCtrl                );
$obFormulario->addHidden            ( $obHdnAcao                );
$obFormulario->addHidden            ( $obHdnExercicio           );
$obFormulario->addHidden            ( $obHdnCGM                 );
$obFormulario->addTitulo            ( "Dados para Permissão"    );
$obFormulario->addComponente        ( $obLblCGM                 );
$obFormulario->addComponente        ( $obLblUsuario             );
$obFormulario->addComponente        ( $obLblNomeUsuario         );
$obFormulario->addTitulo            ( "Grupos de Créditos"      );

//$obFormulario->addComponente        ( $obBscGrupo               );
$obMontaGrupoCredito->setRotulo ( "*Grupo de Créditos" );
$obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );

$obFormulario->defineBarra  ( array( $obBtnIncluirGrupo, $obBtnLimparGrupo ),"","" );
$obFormulario->addSpan              ( $obSpnGrupos    );
$obFormulario->Cancelar();
$obFormulario->show();

SistemaLegado::BloqueiaFrames();
$stJs .= "setTimeout(\"buscaValor('montaGrupos')\",4000);";
SistemaLegado::executaFramePrincipal($stJs);
SistemaLegado::LiberaFrames();
?>

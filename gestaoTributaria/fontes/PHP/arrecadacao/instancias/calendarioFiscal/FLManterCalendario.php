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
    * Página de Filtro para Consultar Calendário
    * Data de Criação: 24/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @ignore

    * $Id: FLManterCalendario.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.03
*/

/*
$Log$
Revision 1.9  2006/09/15 11:50:32  fabio
corrigidas tags de caso de uso

Revision 1.8  2006/09/15 11:02:23  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalendarioFiscal.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupoVencimento.class.php"  );
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalendario";
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

Sessao::write( "link", "" );
Sessao::remove( 'grupos' );
$obMontaGrupoCredito = new MontaGrupoCredito;
$obRARRCalendarioFiscal = new RARRCalendarioFiscal;
$obRARRGrupoVencimento  = new RARRGrupoVencimento( $obRARRCalendarioFiscal );

// busca os grupos de crédito para o select
$rsGrupoCredito = new RecordSet;
$obRARRCalendarioFiscal->listarGrupoCredito( $rsGrupoCredito );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"] );

$obTxtCodigoCredito = new TextBox;
$obTxtCodigoCredito->setTitle              ( "Grupo de créditos para o qual o calendário será definido." );
$obTxtCodigoCredito->setName               ( "inCodigoCredito"   );
$obTxtCodigoCredito->setRotulo             ( "Grupo de Créditos" );
$obTxtCodigoCredito->setMaxLength          ( 7                   );
$obTxtCodigoCredito->setSize               ( 7                   );
$obTxtCodigoCredito->setValue              ( $_REQUEST["inCodigoCredito"] );
$obTxtCodigoCredito->setInteiro            ( true                );

$obCmbGrupoCredito = new Select;
$obCmbGrupoCredito->setName               ( "stGrupoCredito"              );
$obCmbGrupoCredito->setRotulo             ( "Grupo de Créditos"           );
$obCmbGrupoCredito->setCampoId            ( "cod_grupo"                   );
$obCmbGrupoCredito->setCampoDesc          ( "[descricao]/[ano_exercicio]" );
$obCmbGrupoCredito->addOption             ( "", "Selecione"               );
$obCmbGrupoCredito->preencheCombo         ( $rsGrupoCredito               );

$obBtnOK = new OK;
$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick( "LimparFL();" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
//$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para Filtro" );
//$obFormulario->addComponenteComposto( $obTxtCodigoCredito, $obCmbGrupoCredito );
$obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );

$obFormulario->defineBarra( array( $obBtnOK, $obBtnLimpar) );
$obFormulario->Show();

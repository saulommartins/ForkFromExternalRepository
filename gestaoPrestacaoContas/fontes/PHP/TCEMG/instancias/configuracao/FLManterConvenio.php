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
	* Página de Filtro de Alteracao/Recisao de Convenio
	* Data de Criação   : 11/03/2014

	* @author Analista: Sergio Luiz dos Santos
	* @author Desenvolvedor: Michel Teixeira
	* @ignore

	$Id: FLManterConvenio.php 59612 2014-09-02 12:00:51Z gelson $

	*Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove( 'link'	);
Sessao::remove( 'stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl'] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.05.04" );
$obFormulario->addTitulo     ( "Dados para Filtro" );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setRotulo        ( "Exercício"				);
$obTxtExercicio->setTitle         ( "Exercício do Convênio"	);
$obTxtExercicio->setName          ( "stExercicio"			);
$obTxtExercicio->setValue         ( Sessao::getExercicio()	);
$obTxtExercicio->setSize          ( 10						);
$obTxtExercicio->setMaxLength     ( 4						);
$obTxtExercicio->setNull          ( false					);
$obTxtExercicio->setInteiro       ( true					);

$obTxtNumConvenio = new TextBox;
$obTxtNumConvenio->setRotulo        ( "Número do Convênio"	);
$obTxtNumConvenio->setTitle         ( "Número do Convênio"	);
$obTxtNumConvenio->setName          ( "inNumConvenio"		);
$obTxtNumConvenio->setValue         ( $inNumConvenio		);
$obTxtNumConvenio->setSize          ( 10					);
$obTxtNumConvenio->setMaxLength     ( 10					);
$obTxtNumConvenio->setNull          ( true					);
$obTxtNumConvenio->setInteiro       ( true					);

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setNull( true  );
$obEntidadeUsuario->obTextBox->setSize( 3 );
$obEntidadeUsuario->obTextBox->setMaxLength( 1 );

$obDtInicial = new Data;
$obDtInicial->setName     ( "dtInicial"                      );
$obDtInicial->setRotulo   ( "Período do Convênio"            );
$obDtInicial->setTitle    ( 'Informe o período do convênio.' );
$obDtInicial->setNull     ( true                             );

$obLabel = new Label;
$obLabel->setValue( " até " );

$obDtFinal = new Data;
$obDtFinal->setName     ( "dtFinal"   );
$obDtFinal->setRotulo   ( "Período"   );
$obDtFinal->setTitle    ( ''          );
$obDtFinal->setNull     ( true        );

$obFormulario->addHidden		( $obHdnAcao									);
$obFormulario->addHidden		( $obHdnCtrl									);
$obFormulario->addComponente	( $obTxtExercicio								);
$obFormulario->addComponente	( $obEntidadeUsuario							);
$obFormulario->addComponente	( $obTxtNumConvenio								);
$obFormulario->agrupaComponentes( array( $obDtInicial,$obLabel, $obDtFinal )	);

$obFormulario->OK();
$obFormulario->show();

$stJs .= 'f.stExercicio.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
?>
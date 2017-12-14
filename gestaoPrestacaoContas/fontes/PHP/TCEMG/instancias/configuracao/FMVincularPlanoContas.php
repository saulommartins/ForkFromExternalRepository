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
/*
    * Formulário de Vinculo do Plano de Contas ao TCE-MG
    * Data de Criação   : 12/07/2016

    * @author: Michel Teixeira

    * @ignore
    * $Id: FMVincularPlanoContas.php 66067 2016-07-14 17:27:32Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php";

$stPrograma = 'VincularPlanoContas';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$inCodUF = 11;
$obHdnCodUF = new Hidden;
$obHdnCodUF->setName ( "inCodUF" );
$obHdnCodUF->setValue( $inCodUF );

$inCodPlano = 1;
$obHdnCodPlano = new Hidden;
$obHdnCodPlano->setName ( "inCodPlano" );
$obHdnCodPlano->setValue( $inCodPlano );

$obTxtExercicio = new TextBox();
$obTxtExercicio->setName      ( 'stExercicio' );
$obTxtExercicio->setId        ( 'stExercicio' );
$obTxtExercicio->setRotulo    ( 'Exercício' );
$obTxtExercicio->setMaxLength ( 4 );
$obTxtExercicio->setSize      ( 5 );
$obTxtExercicio->setNull      ( false );
$obTxtExercicio->setInteiro   ( true );
$obTxtExercicio->setValue     ( Sessao::getExercicio() );
$obTxtExercicio->setLabel     ( true );
$obTxtExercicio->obEvento->setOnChange( "montaParametrosGET('carregaGrupoContas'); BloqueiaFrames(true,false);" );

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setNull ( false );
$obEntidadeUsuario->obTextBox->obEvento->setOnChange("montaParametrosGET('carregaGrupoContas'); BloqueiaFrames(true,false);");
$obEntidadeUsuario->obSelect->obEvento->setOnChange("montaParametrosGET('carregaGrupoContas'); BloqueiaFrames(true,false);");

$obRContabilidadePlanoConta = new RContabilidadePlanoConta;
$obRContabilidadePlanoConta->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoConta->listarGrupos( $rsCodGrupo );

$obISelectGrupos = new Select();
$obISelectGrupos->setName       ( 'inCodGrupo' );
$obISelectGrupos->setId         ( 'inCodGrupo' );
$obISelectGrupos->setRotulo     ( 'Grupo de Contas' );
$obISelectGrupos->setTitle      ( 'Selecione o Grupo de Contas.' );
$obISelectGrupos->addOption     ( '', 'Selecione' );
$obISelectGrupos->setCampoId    ( "[cod_grupo]" );
$obISelectGrupos->setCampoDesc  ( "[cod_grupo] - [nom_conta]" );
$obISelectGrupos->preencheCombo ( $rsCodGrupo );
$obISelectGrupos->setNull       ( false );
$obISelectGrupos->obEvento->setOnChange( "montaParametrosGET('carregaGrupoContas'); BloqueiaFrames(true,false);" );

$obSpnLista = new Span();
$obSpnLista->setId('spnLista');
$obSpnLista->setValue("");

$obOk = new Ok(true);

$obLimpar = new Limpar;

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCodUF);
$obFormulario->addHidden($obHdnCodPlano);
$obFormulario->addComponente($obTxtExercicio);
$obFormulario->addComponente($obEntidadeUsuario);
$obFormulario->addComponente($obISelectGrupos);
$obFormulario->addSpan($obSpnLista);
$obFormulario->defineBarra( array( $obOk, $obLimpar ) );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

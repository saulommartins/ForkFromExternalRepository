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
    * Data de Criação: 24/09/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    * @package URBEM
    * @subpackage

    $Id: $

    * Casos de uso: uc-03.01.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBemResponsavel.class.php' );

$stPrograma = "ModificarResponsavel";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc );
$obForm->setTarget ("oculto");

//cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao );

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia o componente IPopUpCGMVinculado para o responsavel anterior
$obIPopUpCGMVinculadoResponsavelAnterior = new IPopUpCGMVinculado( $obForm );
$obIPopUpCGMVinculadoResponsavelAnterior->setTabelaVinculo    ( 'patrimonio.bem_responsavel'        );
$obIPopUpCGMVinculadoResponsavelAnterior->setCampoVinculo     ( 'numcgm'                            );
$obIPopUpCGMVinculadoResponsavelAnterior->setNomeVinculo      ( 'ResponsavelAnterior'               );
$obIPopUpCGMVinculadoResponsavelAnterior->setRotulo           ( 'Responsável Anterior'              );
$obIPopUpCGMVinculadoResponsavelAnterior->setTitle            ( 'Selecione o Responsável Anterior.' );
$obIPopUpCGMVinculadoResponsavelAnterior->setName             ( 'stNomResponsavelAnterior'          );
$obIPopUpCGMVinculadoResponsavelAnterior->setId               ( 'stNomResponsavelAnterior'          );
$obIPopUpCGMVinculadoResponsavelAnterior->obCampoCod->setName ( 'inNumResponsavelAnterior'          );
$obIPopUpCGMVinculadoResponsavelAnterior->obCampoCod->setId   ( 'inNumResponsavelAnterior'          );
$obIPopUpCGMVinculadoResponsavelAnterior->setNull             ( false                               );
$obIPopUpCGMVinculadoResponsavelAnterior->obCampoCod->obEvento->setOnFocus  ( "montaParametrosGET( 'verificaResponsavelBem'); montaParametrosGET( 'verificaResponsavelDif');" );
$obIPopUpCGMVinculadoResponsavelAnterior->obCampoCod->obEvento->setOnChange ( "montaParametrosGET( 'verificaResponsavelBem'); montaParametrosGET( 'verificaResponsavelDif');" );

//instancia o componente IPopUpCGMVinculado para o novo responsavel
$obIPopUpCGMVinculadoResponsavelNovo = new IPopUpCGMVinculado( $obForm );
$obIPopUpCGMVinculadoResponsavelNovo->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica'          );
$obIPopUpCGMVinculadoResponsavelNovo->setCampoVinculo     ( 'numcgm'                        );
$obIPopUpCGMVinculadoResponsavelNovo->setNomeVinculo      ( 'Responsável Novo'               );
$obIPopUpCGMVinculadoResponsavelNovo->setRotulo           ( 'Novo Responsável'              );
$obIPopUpCGMVinculadoResponsavelNovo->setTitle            ( 'Selecione o Novo Responsável.' );
$obIPopUpCGMVinculadoResponsavelNovo->setName             ( 'stNomResponsavelNovo'          );
$obIPopUpCGMVinculadoResponsavelNovo->setId               ( 'stNomResponsavelNovo'          );
$obIPopUpCGMVinculadoResponsavelNovo->obCampoCod->setName ( 'inNumResponsavelNovo'          );
$obIPopUpCGMVinculadoResponsavelNovo->obCampoCod->setId   ( 'inNumResponsavelNovo'          );
$obIPopUpCGMVinculadoResponsavelNovo->setNull             ( false                           );
$obIPopUpCGMVinculadoResponsavelNovo->obCampoCod->obEvento->setOnFocus  ( "montaParametrosGET( 'verificaResponsavelDif');" );
$obIPopUpCGMVinculadoResponsavelNovo->obCampoCod->obEvento->setOnChange ( "montaParametrosGET( 'verificaResponsavelDif');" );

//instancia o componente data para o responsavel
$obDtInicioResponsavel = new Data();
$obDtInicioResponsavel->setRotulo( 'Data de Início' );
$obDtInicioResponsavel->setTitle( 'Informe a Data de Início do Novo Responsável.' );
$obDtInicioResponsavel->setName( 'dtInicioResponsavel' );
$obDtInicioResponsavel->setId  ( 'dtInicioResponsavel' );
$obDtInicioResponsavel->setNull( false );

$obCheckBoxEmitirTermo = new CheckBox;
$obCheckBoxEmitirTermo->setName  ('boEmitirTermo');
$obCheckBoxEmitirTermo->setId    ('boEmitirTermo');
$obCheckBoxEmitirTermo->setRotulo('Emitir Termo de Responsabilidade');
$obCheckBoxEmitirTermo->setTitle ('Emitir Termo de Responsabilidade');
$obCheckBoxEmitirTermo->setChecked('true');
$obCheckBoxEmitirTermo->setValue ('true');

$obChkValor = new Checkbox;
$obChkValor->setName    ( 'demo_valor'   );
$obChkValor->setId      ( 'demo_valor'   );
$obChkValor->setRotulo  ( 'Demonstrar Valor'       );
$obChkValor->setChecked ( true          );
$obChkValor->setValue   (1);

$obSpnDtInicio = new Span;
$obSpnDtInicio->setId ( "spnDtInicio" );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.01.24');
$obFormulario->addForm      ( $obForm    );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo	( 'Modificar Responsável pelo Bem'         );
$obFormulario->addComponente( $obIPopUpCGMVinculadoResponsavelAnterior );
$obFormulario->addSpan      ( $obSpnDtInicio                           );
$obFormulario->addComponente( $obIPopUpCGMVinculadoResponsavelNovo     );
$obFormulario->addComponente( $obDtInicioResponsavel                   );
$obFormulario->addComponente( $obCheckBoxEmitirTermo                   );
$obFormulario->addComponente( $obChkValor                              );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

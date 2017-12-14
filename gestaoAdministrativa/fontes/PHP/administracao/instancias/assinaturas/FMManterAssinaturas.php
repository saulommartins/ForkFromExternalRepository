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
    * Página de Formulario de Manter Configuração e-Sfinge
    * Data de Criação: 27/04/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-01.01.08
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php"                      );

include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinatura.class.php"  );
include_once( CAM_GA_ADM_NEGOCIO."RModulo.class.php"  );

$obRModulo = new RModulo;
$obRModulo->listar( $rsModulosDisponives );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAssinaturas";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include($pgJs);

$jsOnload   = "executaFuncaoAjax( 'configuracoesIniciais' );";

Sessao::remove('assinaturas');
Sessao::write('assinaturas',array());

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnId= new Hidden;
$obHdnId->setId ("inId");
$obHdnId->setName("inId");

$obExercicio = new Label;
$obExercicio->setRotulo("Exercício");
$obExercicio->setName  ("stExercicio");
$obExercicio->setValue(Sessao::getExercicio());

$obISelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidadeUsuario->setObrigatorio(false);
$obISelectEntidadeUsuario->setObrigatorioBarra(true);

$obIPopUpCGM = new IPopUpCGM($obForm);
$obIPopUpCGM->setTipo ( "fisica" );
$obIPopUpCGM->setRotulo ( "CGM do Assinante" );
$obIPopUpCGM->setTitle ( "Selecione o CGM do Assinante.");
$obIPopUpCGM->setObrigatorio(false);
$obIPopUpCGM->setObrigatorioBarra(true);

$obTxtCargo = new TextBox();
$obTxtCargo->setRotulo    ("Cargo");
$obTxtCargo->setName      ("stCargo");
$obTxtCargo->setId        ("stCargo");
$obTxtCargo->setTitle     ("Informe a descrição do cargo do Assinante.");
$obTxtCargo->setSize      (80);
$obTxtCargo->setMaxLength (80);
$obTxtCargo->setObrigatorioBarra(true);

$obTxtIncricaoCRC = new TextBox();
$obTxtIncricaoCRC->setRotulo    ("Inscricao CRC");
$obTxtIncricaoCRC->setName      ("stCRC");
$obTxtIncricaoCRC->setTitle     ("Informe o CRC do Assinante(Para Contador).");
$obTxtIncricaoCRC->setSize      (10);
$obTxtIncricaoCRC->setMaxLength (10);

$obCmbModulos = new SelectMultiplo;
$obCmbModulos->setName ('inCodModulos');
$obCmbModulos->setRotulo ( "Módulos" );
$obCmbModulos->setObrigatorioBarra(true);
$obCmbModulos->setTitle( "Selecione os módulos onde esta assinatura será utilizada." );

$rsModulos = new RecordSet;
// lista de atributos disponiveis
$obCmbModulos->SetNomeLista1('inCodModulosDisponiveis');
$obCmbModulos->setCampoId1('cod_modulo');
$obCmbModulos->setCampoDesc1('nom_modulo');
$obCmbModulos->SetRecord1( $rsModulosDisponives );

// lista de atributos selecionados
$obCmbModulos->SetNomeLista2('inCodModulosSelecionados');
$obCmbModulos->setCampoId2('cod_modulo');
$obCmbModulos->setCampoDesc2('nom_modulo');
$obCmbModulos->SetRecord2( $rsModulos );

$obSpnListaAssinaturas = new Span;
$obSpnListaAssinaturas->setId    ( "spnListaAssinaturas"   );
$obSpnListaAssinaturas->setValue ( ""                      );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( 'Dados para Configuração de Assinaturas' );
$obFormulario->addHidden( $obHdnId );
$obFormulario->addComponente( $obExercicio );
$obFormulario->addComponente( $obISelectEntidadeUsuario );
$obFormulario->addComponente( $obIPopUpCGM );
$obFormulario->addComponente( $obTxtCargo );
$obFormulario->addComponente( $obTxtIncricaoCRC );
$obFormulario->addComponente( $obCmbModulos );
$obFormulario->IncluirAlterar( 'Assinaturas', array( $obISelectEntidadeUsuario, $obIPopUpCGM, $obTxtCargo, $obTxtIncricaoCRC, $obCmbModulos ) );
$obFormulario->addSpan( $obSpnListaAssinaturas );

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "Limpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "Reset" );
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('configuracoesIniciais')" );

$obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimpar ),"","" );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

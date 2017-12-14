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
    * Página de Filtro - Exportação Arquivos Para o e-Sfinge
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-01-18 14:07:50 -0200 (Qui, 18 Jan 2007) $

    * Casos de uso: uc-02.08.16
*/

/*
    $Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Bimestre.class.php';

include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ParametrosEsfinge";
$pgJs       = "JS".$stPrograma.".js";
$pgProc     = "PR".$stPrograma.".php";

include_once( $pgJs );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( $pgProc );
$obForm->setTarget      ( "telaPrincipal" ); //oculto - telaPrincipal

$obExercicio = new Exercicio;

$obBimestre = new Bimestre;

$obSelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();

$obRdoGeraTodosSim = new Radio;
$obRdoGeraTodosSim->setName  ( "rdoGeraTodos" );
$obRdoGeraTodosSim->setId    ( "rdoGeraTodosSim" );
$obRdoGeraTodosSim->setRotulo( "Gera Todos os Arquivos" );
$obRdoGeraTodosSim->setLabel ( "Sim" );
$obRdoGeraTodosSim->setValue ( "sim" );
$obRdoGeraTodosSim->setChecked( true );
$obRdoGeraTodosSim->obEvento->setOnClick( "mudaStatusCheckBox(true);" );

$obRdoGeraTodosNao = new Radio;
$obRdoGeraTodosNao->setName  ( "rdoGeraTodos" );
$obRdoGeraTodosNao->setId    ( "rdoGeraTodosNao" );
$obRdoGeraTodosNao->setRotulo( "Gera Todos os Arquivos" );
$obRdoGeraTodosNao->setLabel ( "Não" );
$obRdoGeraTodosNao->setValue ( "nao" );
$obRdoGeraTodosNao->setChecked( false );
$obRdoGeraTodosNao->obEvento->setOnClick( "mudaStatusCheckBox(false);" );

$obChkPPA = new CheckBox;
$obChkPPA->setName  ( "chkPPA" );
$obChkPPA->setId    ( "chkPPA" );
$obChkPPA->setRotulo( "Grupos de Arquivos" );
$obChkPPA->setLabel ( "PPA" );
$obChkPPA->setChecked ( true );
$obChkPPA->setDisabled( true );

$obChkLDO = new CheckBox;
$obChkLDO->setName  ( "chkLDO" );
$obChkLDO->setId    ( "chkLDO" );
$obChkLDO->setRotulo( "Grupos de Arquivos" );
$obChkLDO->setLabel ( "LDO" );
$obChkLDO->setChecked ( true );
$obChkLDO->setDisabled( true );

$obChkLOA = new CheckBox;
$obChkLOA->setName  ( "chkLOA" );
$obChkLOA->setId    ( "chkLOA" );
$obChkLOA->setRotulo( "Grupos de Arquivos" );
$obChkLOA->setLabel ( "LOA" );
$obChkLOA->setChecked ( true );
$obChkLOA->setDisabled( true );

$obChkExecOrcamentaria = new CheckBox;
$obChkExecOrcamentaria->setName  ( "chkExecOrcamentaria" );
$obChkExecOrcamentaria->setId    ( "chkExecOrcamentaria" );
$obChkExecOrcamentaria->setRotulo( "Grupos de Arquivos" );
$obChkExecOrcamentaria->setLabel ( "Execução Orçamentária" );
$obChkExecOrcamentaria->setChecked ( true );
$obChkExecOrcamentaria->setDisabled( true );

$obChkRegContabeis = new CheckBox;
$obChkRegContabeis->setName  ( "chkRegContabeis" );
$obChkRegContabeis->setId    ( "chkRegContabeis" );
$obChkRegContabeis->setRotulo( "Grupos de Arquivos" );
$obChkRegContabeis->setLabel ( "Registros Contábeis" );
$obChkRegContabeis->setChecked ( true );
$obChkRegContabeis->setDisabled( true );

$obChkGestaoFiscal = new CheckBox;
$obChkGestaoFiscal->setName  ( "chkGestaoFiscal" );
$obChkGestaoFiscal->setId    ( "chkGestaoFiscal" );
$obChkGestaoFiscal->setRotulo( "Grupos de Arquivos" );
$obChkGestaoFiscal->setLabel ( "Gestão Fiscal" );
$obChkGestaoFiscal->setChecked ( true );
$obChkGestaoFiscal->setDisabled( true );

$obChkLicitacao = new CheckBox;
$obChkLicitacao->setName  ( "chkLicitacao" );
$obChkLicitacao->setId    ( "chkLicitacao" );
$obChkLicitacao->setRotulo( "Grupos de Arquivos" );
$obChkLicitacao->setLabel ( "Licitações" );
$obChkLicitacao->setChecked ( true );
$obChkLicitacao->setDisabled( true );

$obChkContratos = new CheckBox;
$obChkContratos->setName  ( "chkContratos" );
$obChkContratos->setId    ( "chkContratos" );
$obChkContratos->setRotulo( "Grupos de Arquivos" );
$obChkContratos->setLabel ( "Contratos" );
$obChkContratos->setChecked ( true );
$obChkContratos->setDisabled( true );

$obChkConvenios = new CheckBox;
$obChkConvenios->setName  ( "chkConvenios" );
$obChkConvenios->setId    ( "chkConvenios" );
$obChkConvenios->setRotulo( "Grupos de Arquivos" );
$obChkConvenios->setLabel ( "Convênios" );
$obChkConvenios->setChecked ( true );
$obChkConvenios->setDisabled( true );

$obChkConcursos = new CheckBox;
$obChkConcursos->setName  ( "chkConcursos" );
$obChkConcursos->setId    ( "chkConcursos" );
$obChkConcursos->setRotulo( "Grupos de Arquivos" );
$obChkConcursos->setLabel ( "Concursos" );
$obChkConcursos->setChecked ( true );
$obChkConcursos->setDisabled( true );

$obChkPlanoCargos = new CheckBox;
$obChkPlanoCargos->setName  ( "chkPlanoCargos" );
$obChkPlanoCargos->setId    ( "chkPlanoCargos" );
$obChkPlanoCargos->setRotulo( "Grupos de Arquivos" );
$obChkPlanoCargos->setLabel ( "Plano de Cargos" );
$obChkPlanoCargos->setChecked ( true );
$obChkPlanoCargos->setDisabled( true );

$obChkPessoal = new CheckBox;
$obChkPessoal->setName  ( "chkPessoal" );
$obChkPessoal->setId    ( "chkPessoal" );
$obChkPessoal->setRotulo( "Grupos de Arquivos" );
$obChkPessoal->setLabel ( "Atos Relativos à Pessoal" );
$obChkPessoal->setChecked ( true );
$obChkPessoal->setDisabled( true );

$obChkConstDirEmpresa = new CheckBox;
$obChkConstDirEmpresa->setName  ( "chkConstDirEmpresa" );
$obChkConstDirEmpresa->setId    ( "chkConstDirEmpresa" );
$obChkConstDirEmpresa->setRotulo( "Grupos de Arquivos" );
$obChkConstDirEmpresa->setLabel ( "Constituição de Diretoria de Empresa" );
$obChkConstDirEmpresa->setChecked ( true );
$obChkConstDirEmpresa->setDisabled( true );

$obChkGenericos = new CheckBox;
$obChkGenericos->setName  ( "chkGenericos" );
$obChkGenericos->setId    ( "chkGenericos" );
$obChkGenericos->setRotulo( "Grupos de Arquivos" );
$obChkGenericos->setLabel ( "Genéricos" );
$obChkGenericos->setChecked ( true );
$obChkGenericos->setDisabled( true );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo    ( "Parametros da Exportação" );
$obFormulario->addComponente( $obExercicio );
$obFormulario->addComponente( $obSelectMultiploEntidadeUsuario );
$obFormulario->addComponente( $obBimestre );

$obFormulario->addTitulo    ( "Arquivos" );
$obFormulario->agrupaComponentes( array( $obRdoGeraTodosSim, $obRdoGeraTodosNao ) );
$obFormulario->addComponente( $obChkPPA );
$obFormulario->addComponente( $obChkLDO );
$obFormulario->addComponente( $obChkLOA );
$obFormulario->addComponente( $obChkExecOrcamentaria );
$obFormulario->addComponente( $obChkRegContabeis );
$obFormulario->addComponente( $obChkGestaoFiscal );
$obFormulario->addComponente( $obChkLicitacao );
$obFormulario->addComponente( $obChkContratos );
$obFormulario->addComponente( $obChkConvenios );
$obFormulario->addComponente( $obChkConcursos );
$obFormulario->addComponente( $obChkPlanoCargos );
$obFormulario->addComponente( $obChkPessoal );
$obFormulario->addComponente( $obChkConstDirEmpresa );
$obFormulario->addComponente( $obChkGenericos );

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "Limpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "Reset" );
$obBtnLimpar->obEvento->setOnClick( "mudaStatusCheckBox(true);" );

$obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimpar ) );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

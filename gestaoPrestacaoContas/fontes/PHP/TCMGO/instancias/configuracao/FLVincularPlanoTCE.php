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
    * Página de Filtro - Vinculação do Plano de Contas ao TCE
    * Data de Criação   : 21/03/2011

    * @author: Eduardo Paculski Schitz

    * @ignore
    * $Id: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoConta.class.php';

// Usado caso retorne algum erro ao clicar o OK, para poder setar novamente os valores na tela
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setNull ( false );

$obMes = new Mes;
$obMes->setNull(false);

$obRContabilidadePlanoConta = new RContabilidadePlanoConta;
$obRContabilidadePlanoConta->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoConta->listarGrupos( $rsCodGrupo );

//Define o objeto COMBO para Grupo
$obCmbGrupo = new Select;
$obCmbGrupo->setName      ( "stGrupo" );
$obCmbGrupo->setRotulo    ( "Grupo" );

// Caso o usuário tenha permissão para mais de uma entidade, exibe o selecionar.
// Se tiver apenas uma, evita o addOption forçando a primeira e única opção ser selecionada.
if ($rsCodGrupo->getNumLinhas()>1) {
    $obCmbGrupo->addOption    ( "", "Selecione" );
}

$obCmbGrupo->setCampoId   ( "[cod_grupo]" );
$obCmbGrupo->setCampoDesc ( "[cod_grupo] - [nom_conta]" );
$obCmbGrupo->preencheCombo( $rsCodGrupo );
$obCmbGrupo->setNull      ( false );
$obCmbGrupo->setTitle     ( 'Selecione um Grupo' );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction('FMVincularPlanoTCE.php');
$obForm->setTarget('telaPrincipal');

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
$obFormulario->addTitulo    ('Dados para filtro das contas');
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addComponente($obEntidadeUsuario);
$obFormulario->addComponente($obMes);
$obFormulario->addComponente($obCmbGrupo);

$obFormulario->OK  ();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

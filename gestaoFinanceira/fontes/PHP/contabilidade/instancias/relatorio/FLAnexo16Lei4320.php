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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );

$pgGera = "OCGeraRelatorioAnexo16Lei4320.php";

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

$rsRecordset = new RecordSet;

//Monta FORMULARIO
$obForm = new Form;
$obForm->setAction ( $pgGera         );
$obForm->setTarget ( "telaPrincipal" );


$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_CONT_INSTANCIAS."relatorio/OCAnexo16Lei4320.php" );

//Montra Select Multiplo Entidades
include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php");
$obEntidadeUsuario = new ISelectMultiploEntidadeUsuario( $obForm );


//Define Objeto Text para o Exercicio
$obTxtExercicio = new TextBox;
$obTxtExercicio->setName      ('stExercicio');
$obTxtExercicio->setValue     (Sessao::getExercicio());
$obTxtExercicio->setRotulo    ('Exercício');
$obTxtExercicio->setTitle     ('Informe o Exercício para o Extrato de Conta');
$obTxtExercicio->setNull      (false);
$obTxtExercicio->setMaxLength (4);
$obTxtExercicio->setSize      (5);
$obTxtExercicio->setLabel     (true);

//Monta Incluir Assinaturas
include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obEntidadeUsuario );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obEntidadeUsuario );
$obFormulario->addComponente( $obTxtExercicio );

$obMontaAssinaturas->geraFormulario ( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

?>

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
    * Página de Filtro de desdobramento de receita
    * Data de Criação   : 09/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FLManterDesdobramentoReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesdobramentoReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "desdobrar";
}

//ARRAY DE RECEITAS SECUNDARIAS
Sessao::write('arReceitasSecundarias', array());

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgForm );
//$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

// Define Objeto BuscaInner para Receita
$obBscReceita = new BuscaInner;
$obBscReceita->setRotulo ( "Receita" );
$obBscReceita->setTitle ( "" );
$obBscReceita->setNulL ( false );
$obBscReceita->setId ( "stNomReceita" );
$obBscReceita->setValue ( $stNomReceita );
$obBscReceita->setTitle ( "Informe uma receita orçamentária" );
$obBscReceita->obCampoCod->setName ( "inCodReceita" );
$obBscReceita->obCampoCod->setSize ( 10 );
$obBscReceita->obCampoCod->setMaxLength( 5 );
$obBscReceita->obCampoCod->setValue ( $inCodReceita );
$obBscReceita->obCampoCod->setAlign ("left");
$obBscReceita->obCampoCod->obEvento->setOnChange("buscaDado('buscaReceita');");
$obBscReceita->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."receita/LSReceita.php','frm','inCodReceita','stNomReceita','','".Sessao::getId()."','800','550');");

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda             ('UC-02.02.01');
$obFormulario->addTitulo( "Dados para Desdobramento de Receita" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addComponente( $obBscReceita );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

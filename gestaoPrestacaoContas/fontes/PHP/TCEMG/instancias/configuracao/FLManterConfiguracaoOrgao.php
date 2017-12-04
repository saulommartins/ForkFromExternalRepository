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

    * Página de Filtro de Configuracao Contas Bancarias TCEMG
    * Data de Criação   : 14/02/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Arthur Cruz
    
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoOrgao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao   = $request->get('stAcao');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgForm );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ( "inCodEntidade"                 );
$obHdnEntidade->setValue( $request->get('inCodEntidade')  );

$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "modulo"                 );
$obHdnModulo->setValue( $request->get('modulo')  );

$obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obITextBoxSelectEntidadeUsuario->setNull ( false );
if ($request->get('inCodEntidade') != "") {
    $obITextBoxSelectEntidadeUsuario->setCodEntidade($request->get('inCodEntidade'));
}

//Define o objeto TextBox para o Reduzido
$obTxtReduzido = new TextBox;
$obTxtReduzido->setName     ( "inCodPlano" );
$obTxtReduzido->setValue    ( $inCodPlano );
$obTxtReduzido->setRotulo   ( "Reduzido" );
$obTxtReduzido->setInteiro  ( true );
$obTxtReduzido->setSize     ( 20 );
$obTxtReduzido->setMaxLength( 20 );
$obTxtReduzido->setNull     ( true );
$obTxtReduzido->setTitle    ( 'Informe um código reduzido' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo     ( "Configuração de orgão"  );
$obFormulario->addHidden     ( $obHdnAcao     );
$obFormulario->addHidden     ( $obHdnModulo   );
$obFormulario->addComponente ( $obITextBoxSelectEntidadeUsuario );
$obFormulario->OK();
$obFormulario->show();


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

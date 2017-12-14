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
/*******

    * Página de Filtro do Movimentação SEFIP
    * Data de Criação   : 06/02/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena
    * Caso de uso: uc-04.04.40

*********/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCategoria.class.php" );
//include_once ( CAM_GRH_PES_NEGOCIO."RPessoalMovimentacaoSEFIP.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterMovimentacaoSEFIP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

// campos do formulário

// Campo código
$obTxtCodigo = new TextBox;
$obTxtCodigo->setRotulo            ( "Código SEFIP");
$obTxtCodigo->setTitle             ( "Informe o código de movimentação da SEFIP");
$obTxtCodigo->setName              ( "stCodigoSEFIP");
$obTxtCodigo->setId                ( "stCodigoSEFIP");
$obTxtCodigo->setValue             ( $stCodigoSEFIP);
$obTxtCodigo->setSize              ( 3);
$obTxtCodigo->setMaxLength         ( 3);
$obTxtCodigo->setNull              ( false);
$obTxtCodigo->setEspacosExtras     ( false);
$obTxtCodigo->setCaracteresAceitos ( '[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ/-]');
//Fim Código

//descriçao
$obTxtDescricaoSEFIP = new TextBox;
$obTxtDescricaoSEFIP->setRotulo   ( "Descrição" );
$obTxtDescricaoSEFIP->setTitle    ( "Informe a descrição da movimentação da SEFIP");
$obTxtDescricaoSEFIP->setName     ( "stDescricao");
$obTxtDescricaoSEFIP->setValue    ( $stDescricao);
$obTxtDescricaoSEFIP->setSize     ( 50);
$obTxtDescricaoSEFIP->setNull     ( false);
$obTxtDescricaoSEFIP->setMaxLength( 80);
//Fim descrição SEFIP

//Tipo de Movimentação
//Opção Afastamento
$obRdoAfastamento = new Radio;
$obRdoAfastamento->setname               ("stMovimentacao");
$obRdoAfastamento->setTitle              ("Indique se a movimentação SEFIP é de afastamento ou Retorno");
$obRdoAfastamento->setRotulo             ("*Movimentação SEFIP");
$obRdoAfastamento->setLabel              ("Afastamento");
$obRdoAfastamento->setValue              ("A");
$obRdoAfastamento->setChecked            ($stMovimentacao=="A" or !$stMovimentacao);
//$obRdoAfastamento->obEvento->setOnChange ("buscaValor('montaForm');");

//opção Retorno
$obRdoRetorno = new Radio;
$obRdoRetorno->setname("stMovimentacao");
$obRdoRetorno->setTitle("Indique se a movimentação SEFIP é de afastamento ou Retorno");
$obRdoRetorno->setRotulo("*Movimentação SEFIP");
$obRdoRetorno->setLabel("Retorno");
$obRdoRetorno->setValue("R");
$obRdoRetorno->setChecked($stMovimentacao=="R");
//$obRdoRetorno->obEvento->setOnChange ("buscaValor('montaForm');");
//Fim Movimentação

//fim campos

// Definição do formulário

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ($pgList );
$obForm->setTarget ('oculto');

//Definição do Formulário

$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );

$obFormulario->addComponente ( $obTxtCodigo         );
$obFormulario->addComponente ( $obTxtDescricaoSEFIP );
$obFormulario->AgrupaComponentes( array($obRdoAfastamento,$obRdoRetorno) );

$obFormulario->Ok ();

?>

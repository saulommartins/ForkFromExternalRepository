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
  * Página de
  * Data de criação : 13/03/2006

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 13075 $
    $Name$
    $Author: fernando $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    Caso de uso: uc-03.02.17
**/

/*
$Log$
Revision 1.12  2006/07/21 11:34:01  fernando
Inclusão do  Ajuda.

Revision 1.11  2006/07/06 13:57:43  diego
Retirada tag de log com erro.

Revision 1.10  2006/07/06 12:11:17  diego

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include_once("../../../../../../gestaoAdministrativa/fontes/javaScript/genericas.js");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ControleIndividual";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

//define os componentes do formulário

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GP_FRO_INSTANCIAS."relatorio/OCControleIndividual.php" );

$obHdnCtrl   = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue( " " );

// Define Objeto TextBox para o código do veículo
$obTxtCodVeiculo = new TextBox;
$obTxtCodVeiculo->setRotulo    ( "Código do Veículo"           );
$obTxtCodVeiculo->setTitle     ( "Informe o código do veículo." );
$obTxtCodVeiculo->setName      ( "inCodVeiculo"                );
$obTxtCodVeiculo->setValue     ( $stCodVeiculo                 );
$obTxtCodVeiculo->setNull      ( false                         );
$obTxtCodVeiculo->setMaxLength ( 8                             );
$obTxtCodVeiculo->setSize      ( 8                             );
$obTxtCodVeiculo->setInteiro   ( true                          );
$obTxtCodVeiculo->obEvento->setOnChange ("buscaValor('CodVeiculo');");

// Define Objeto TextBox para o Prefixo
$obTxtPrefixo = new TextBox;
$obTxtPrefixo->setRotulo    ( "Prefixo"                      );
$obTxtPrefixo->setTitle     ( "Informe o prefixo do veículo." );
$obTxtPrefixo->setName      ( "stPrefixo"                    );
$obTxtPrefixo->setValue     ( $stPrefixo                     );
$obTxtPrefixo->setMaxLength ( 15                             );
$obTxtPrefixo->setSize      ( 15                             );
$obTxtPrefixo->obEvento->setOnChange ("buscaValor('Prefixo');");

// Define Objeto TextBox para a placa do Veículo
$obTxtPlaca = new TextBox;
$obTxtPlaca->setRotulo    ( "Placa do Veículo"             );
$obTxtPlaca->setTitle     ( "Informe a placa do veículo."   );
$obTxtPlaca->setName      ( "stPlaca"                      );
$obTxtPlaca->setValue     ( $stPlaca                       );
$obTxtPlaca->setMaxLength ( 8                              );
$obTxtPlaca->setSize      ( 8                              );
$obTxtPlaca->obEvento->setOnKeyUp ("mascaraPlacaVeiculo(this);");
$obTxtPlaca->obEvento->setOnBlur ("verificaPlacaVeiculo(this);");
$obTxtPlaca->obEvento->setOnChange("buscaValor('Placa');");

// Define Objeto TextBox para a Marca do veículo
$obTxtMarca = new TextBox;
$obTxtMarca->setRotulo    ( "Marca"            );
$obTxtMarca->setTitle     ( "Informe a marca do veículo."   );
$obTxtMarca->setName      ( "stMarca"          );
$obTxtMarca->setValue     ( $stMarca           );
$obTxtMarca->setReadOnly  ( true               );
$obTxtMarca->setSize      ( 30                 );
$obTxtMarca->setMaxLength ( 30                 );

// Define Objeto TextBox para o Modelo do veículo
$obTxtModelo = new TextBox;
$obTxtModelo->setRotulo    ( "Modelo"           );
$obTxtModelo->setTitle     ( "Informe o modelo do veículo."   );
$obTxtModelo->setName      ( "stModelo"         );
$obTxtModelo->setValue     ( $stModelo          );
$obTxtModelo->setReadOnly  ( true               );
$obTxtModelo->setSize      ( 30                 );
$obTxtModelo->setMaxLength ( 30                 );

// Define Objeto TextBox para o tipo do veículo
$obTxtTipoVeiculo = new TextBox;
$obTxtTipoVeiculo->setRotulo    ( "Tipo de Veículo"  );
$obTxtTipoVeiculo->setTitle     ( "Informe o tipo do veículo."   );
$obTxtTipoVeiculo->setName      ( "stTipoVeiculo"    );
$obTxtTipoVeiculo->setValue     ( $stTipoVeiculo     );
$obTxtTipoVeiculo->setReadOnly  ( true               );
$obTxtTipoVeiculo->setSize      ( 30                 );
$obTxtTipoVeiculo->setMaxLength (30                  );

//Define Objeto Mes para o mês de periodicidade
$obMes = new Mes;
$obMes->setName   ("stMes"   );
$obMes->setNull   (false     );
$obMes->setRotulo ("Mês de Referência");
$obMes->setTitle  ("Selecione o mês de referência.");

//define o formulário

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm             );
$obFormulario->setAjuda         ("UC-03.02.17");
$obFormulario->addHidden        ( $obHdnCaminho       );
$obFormulario->addHidden        ( $obHdnCtrl          );
$obFormulario->addTitulo        ( "Dados de Filtro"   );
$obFormulario->addComponente    ( $obTxtCodVeiculo    );
$obFormulario->addComponente    ( $obTxtPrefixo       );
$obFormulario->addComponente    ( $obTxtPlaca         );
$obFormulario->addComponente    ( $obTxtMarca         );
$obFormulario->addComponente    ( $obTxtModelo        );
$obFormulario->addComponente    ( $obTxtTipoVeiculo   );
$obFormulario->addComponente    ( $obMes              );

$obFormulario->OK();
$obFormulario->show();

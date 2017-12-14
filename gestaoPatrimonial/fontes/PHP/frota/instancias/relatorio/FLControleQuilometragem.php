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
  * Data de criação : 15/03/2006

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 13075 $
    $Name$
    $Author: fernando $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    Caso de uso: uc-03.02.18
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
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GP_FRO_NEGOCIO."RFrotaMarca.class.php"                                            );
include_once (CAM_GP_FRO_NEGOCIO."RFrotaTipoVeiculo.class.php"                                      );
include_once (CAM_GP_FRO_NEGOCIO."RFrotaItem.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "ControleQuilometragem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

$marcaFrota = new RFrotaMarca;
$marcaFrota->listar($marca);
$tipoVeiculoFrota = new RFrotaTipoVeiculo;
$tipoVeiculoFrota->listar($tipoVeiculo);
$tipoCombustivelFrota = new RFrotaItem;
$tipoCombustivelFrota->listarCombustivel($tipoCombustivel);

//define os componentes do formulário

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GP_FRO_INSTANCIAS."relatorio/OCControleQuilometragem.php" );

$obHdnCtrl   = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue( " " );

// Define Objeto TextBox para o código do veículo
$obTxtCodVeiculo = new TextBox;
$obTxtCodVeiculo->setRotulo    ( "Código do Veículo"           );
$obTxtCodVeiculo->setTitle     ( "Informe o código do veículo." );
$obTxtCodVeiculo->setName      ( "inCodVeiculo"                );
$obTxtCodVeiculo->setValue     ( $stCodVeiculo                 );
$obTxtCodVeiculo->setMaxLength ( 8                             );
$obTxtCodVeiculo->setSize      ( 8                             );
$obTxtCodVeiculo->setInteiro   ( true                          );
//$obTxtCodVeiculo->obEvento->setOnChange ("buscaValor('MontaDados');");

// Define Objeto TextBox para o Prefixo
$obTxtPrefixo = new TextBox;
$obTxtPrefixo->setRotulo    ( "Prefixo"                      );
$obTxtPrefixo->setTitle     ( "Informe o prefixo do veículo." );
$obTxtPrefixo->setName      ( "stPrefixo"                    );
$obTxtPrefixo->setValue     ( $stPrefixo                     );
$obTxtPrefixo->setMaxLength ( 8                              );
$obTxtPrefixo->setSize      ( 8                              );
//$obTxtPrefixo->obEvento->setOnChange ("buscaValor('MontaDados');");

// Define Objeto TextBox para a placa do Veículo
$obTxtPlaca = new TextBox;
$obTxtPlaca->setRotulo    ( "Placa do Veículo"             );
$obTxtPlaca->setTitle     ( "Informe a placa do veículo."   );
$obTxtPlaca->setName      ( "stPlaca"               );
$obTxtPlaca->setValue     ( $stPlaca                );
$obTxtPlaca->setMaxLength ( 8                              );
$obTxtPlaca->setSize      ( 8                              );
$obTxtPlaca->obEvento->setOnKeyUp ("mascaraPlacaVeiculo(this);");
$obTxtPlaca->obEvento->setOnBlur ("verificaPlacaVeiculo(this);");
//$obTxtPlaca->obEvento->setOnChange("buscaValor('MontaDados');");

//Define o objeto SelectMultiplo para armazenar a Marca
$obCmbMarca = new Select();
$obCmbMarca->setName     ('inCodMarca');
$obCmbMarca->setRotulo   ( "Marca" );
$obCmbMarca->setStyle    ( "width: 270px"              );
$obCmbMarca->setTitle    ( "Selecione a marca do veículo." );
$obCmbMarca->addOption   ( "", "Selecione"                    );
$obCmbMarca->setCampoId  ("cod_marca");
$obCmbMarca->setCampoDesc("nom_marca");
$obCmbMarca->preencheCombo($marca);
$obCmbMarca->obEvento->setOnChange  ( "buscaValor('MontaModelo');" );

//Define o objeto SelectMultiplo para armazenar o Modelo
$obCmbModelo = new Select();
$obCmbModelo->setName   ('inCodModelo');
$obCmbModelo->setValue  ($inCodModelo);
$obCmbModelo->setStyle( "width: 270px"              );
$obCmbModelo->setRotulo ( "Modelo" );
$obCmbModelo->setTitle  ( "Selecione o modelo do veículo." );
$obCmbModelo->addOption ("", "Selecione");

//Define o objeto SelectMultiplo para armazenar o Tipo de veículo
$obCmbTipoVeiculo = new Select();
$obCmbTipoVeiculo->setName   ('inCodTipoVeiculo');
$obCmbTipoVeiculo->setValue  ($inCodTipoVeiculo );
$obCmbTipoVeiculo->setStyle( "width: 270px"     );
$obCmbTipoVeiculo->setRotulo ( "Tipo de Veículo"  );
$obCmbTipoVeiculo->addOption ("", "Selecione");
$obCmbTipoVeiculo->setTitle  ( "Selecione o tipo de veículo." );
$obCmbTipoVeiculo->setCampoId  ("cod_tipo");
$obCmbTipoVeiculo->setCampoDesc("nom_tipo");
$obCmbTipoVeiculo->preencheCombo($tipoVeiculo);

//Define o objeto SelectMultiplo para armazenar o Tipo de combustível
$obCmbTipoCombustivel = new Select();
$obCmbTipoCombustivel->setName   ('inCodTipoCombustivel');
$obCmbTipoCombustivel->setValue  ($inCodTipoCombustivel );
$obCmbTipoCombustivel->setStyle( "width: 270px"         );
$obCmbTipoCombustivel->setRotulo ( "Tipo de Combustível"             );
$obCmbTipoCombustivel->setTitle  ( "Selecione o tipo de combustível do veículo." );
$obCmbTipoCombustivel->addOption ("", "Selecione");
$obCmbTipoCombustivel->setCampoId  ("cod_item");
$obCmbTipoCombustivel->setCampoDesc("combustivel");
$obCmbTipoCombustivel->preencheCombo($tipoCombustivel);

//Define o objeto SelectMultiplo para armazenar a ordenação
$obCmbOrdenacao = new Select();
$obCmbOrdenacao->setName   ('inCodOrdenacao');
$obCmbOrdenacao->setValue  ($inCodOrdenacao );
$obCmbOrdenacao->setStyle  ( "width: 270px" );
$obCmbOrdenacao->setRotulo ( "Ordenação"    );
$obCmbOrdenacao->setTitle  ( "Selecione a Ordenação." );
$obCmbOrdenacao->setNull   ( false          );
$obCmbOrdenacao->addOption ("1", "Placa");
$obCmbOrdenacao->addOption ("2", "Marca");

//Define Objeto Mes para o mês de periodicidade
$obMes = new Mes;
$obMes->setName   ("stMes"   );
$obMes->setNull   (false     );
$obMes->setRotulo ("Mês de Referência");
$obMes->setTitle  ("Selecione o mês de referência.");

//Radios de origem do veículo
$obRdbOrigemTodos = new Radio;
$obRdbOrigemTodos->setRotulo ( "Origem do Veículo" );
$obRdbOrigemTodos->setName   ( "inCodOrigemVeiculo" );
$obRdbOrigemTodos->setChecked( true );
$obRdbOrigemTodos->setValue  ( "1" );
$obRdbOrigemTodos->setLabel  ( "Todos" );
$obRdbOrigemTodos->setNull   ( false      );
$obRdbOrigemTodos->setTitle  ( "Selecione a origem." );

$obRdbOrigemSim = new Radio;
$obRdbOrigemSim->setName   ( "inCodOrigemVeiculo" );
$obRdbOrigemSim->setValue  ( "2" );
$obRdbOrigemSim->setLabel  ( "Veículo Própio" );

$obRdbOrigemNao = new Radio;
$obRdbOrigemNao->setName   ( "inCodOrigemVeiculo" );
$obRdbOrigemNao->setValue  ( "3" );
$obRdbOrigemNao->setLabel  ( "Veículo de Terceiros" );

//Radios de veículos baixados
$obRdbTodos = new Radio;
$obRdbTodos->setRotulo ( "Veículos Baixados" );
$obRdbTodos->setName   ( "inCodVeiculoBaixado" );
$obRdbTodos->setValue  ( "1" );
$obRdbTodos->setTitle  ( "Selecione se o veículo der baixa." );
$obRdbTodos->setLabel  ( "Todos" );
$obRdbTodos->setNull   ( false      );

$obRdbSim = new Radio;
$obRdbSim->setName   ( "inCodVeiculoBaixado" );
$obRdbSim->setValue  ( "2" );
$obRdbSim->setLabel  ( "Sim" );

$obRdbNao = new Radio;
$obRdbNao->setName   ( "inCodVeiculoBaixado" );
$obRdbNao->setValue  ( "3" );
$obRdbNao->setLabel  ( "Não" );
$obRdbNao->setChecked (true);
//define o formulário

$obBscCGMResponsavel = new IPopUpCGM($obForm);
$obBscCGMResponsavel->setId                    ('stNomeCGMResponsavel');
$obBscCGMResponsavel->setRotulo                ( 'Responsável'       );
$obBscCGMResponsavel->setTipo                  ('fisica'           );
$obBscCGMResponsavel->setTitle                ( 'Informe o CGM relacionado ao responsável pelo almoxarifado');
$obBscCGMResponsavel->setValue                 ( $stNomeCGMResponsavel);
$obBscCGMResponsavel->obCampoCod->setName      ( 'inCGMResponsavel' );
$obBscCGMResponsavel->obCampoCod->setSize      (8);
$obBscCGMResponsavel->obCampoCod->setValue     ( $inCGMResponsavel   );
$obBscCGMResponsavel->setNull                  ( true                );

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm              );
$obFormulario->setAjuda         ("UC-03.02.18");
$obFormulario->addHidden        ( $obHdnCaminho        );
$obFormulario->addHidden        ( $obHdnCtrl           );
$obFormulario->addTitulo        ( "Dados de Filtro"    );
$obFormulario->addComponente    ( $obTxtCodVeiculo     );
$obFormulario->addComponente    ( $obCmbMarca          );
$obFormulario->addComponente    ( $obCmbModelo         );
$obFormulario->addComponente    ( $obCmbTipoVeiculo    );
$obFormulario->addComponente    ( $obCmbTipoCombustivel);
$obFormulario->addComponente    ( $obTxtPrefixo        );
$obFormulario->addComponente    ( $obTxtPlaca          );
$obFormulario->addComponente    ( $obBscCGMResponsavel );
$obFormulario->addComponente    ( $obCmbOrdenacao      );
$obFormulario->agrupaComponentes( array($obRdbOrigemTodos,$obRdbOrigemSim,$obRdbOrigemNao));
$obFormulario->agrupaComponentes( array($obRdbTodos,$obRdbSim,$obRdbNao));
$obFormulario->addComponente    ( $obMes               );
$obFormulario->OK();
$obFormulario->show();

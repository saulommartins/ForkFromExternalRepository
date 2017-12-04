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
    * Página de filtro do CID
    * Data de Criação: 11/05/2006

    * @author Analista      : Diego Barbosa Victoria
    * @author Desenvolvedor : Rodrigo

    * @ignore

    * Casos de uso: uc-03.03.08

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php"                                         );
include_once(CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php"                                          );
include_once(CAM_GP_ALM_COMPONENTES."IPopUpCentroCusto.class.php"                                   );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php"                               );

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotaTransferencia";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

//Define a função do arquivo, ex: excluir ou alterar
$stAcao = $request->get('stAcao');

Sessao::write('link', '');

$rsAlmoxarifados = new RecordSet;
$obRAlmoxarifado = new RAlmoxarifadoAlmoxarifado;
$obRAlmoxarifado->listar($rsAlmoxarifados);

//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgList);

$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue(""      );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setRotulo   ("Exercício"          );
$obTxtExercicio->setTitle    ("Informe o exercício");
$obTxtExercicio->setName     ("stExercicio"        );
$obTxtExercicio->setId       ("stExercicio"        );
$obTxtExercicio->setValue    (Sessao::getExercicio());
$obTxtExercicio->setSize     (4                    );
$obTxtExercicio->setMaxLength(4                    );
$obTxtExercicio->setInteiro  (true                 );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS de Origem
$obCmbAlmoxarifadosOrigem = new SelectMultiplo();
$obCmbAlmoxarifadosOrigem->setName  ('inCodAlmoxarifadoOrigem'            );
$obCmbAlmoxarifadosOrigem->setRotulo("Almoxarifado de Origem"             );
$obCmbAlmoxarifadosOrigem->setTitle ("Selecione o Almoxarifado de Origem.");

// lista de atributos disponiveis
$obCmbAlmoxarifadosOrigem->SetNomeLista1('inCodAlmoxarifadoOrigemDisponivel');
$obCmbAlmoxarifadosOrigem->setCampoId1  ('codigo'                           );
$obCmbAlmoxarifadosOrigem->setCampoDesc1('[codigo]-[nom_a]'                 );
$obCmbAlmoxarifadosOrigem->SetRecord1   ($rsAlmoxarifados                   );
$rsRecordset = new RecordSet;

// lista de atributos selecionados
$obCmbAlmoxarifadosOrigem->SetNomeLista2('inCodAlmoxarifadoOrigem');
$obCmbAlmoxarifadosOrigem->setCampoId2  ('codigo'                            );
$obCmbAlmoxarifadosOrigem->setCampoDesc2('[codigo]-[nom_a]'                  );
$obCmbAlmoxarifadosOrigem->SetRecord2   ($rsRecordset                        );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS de Destino
$obCmbAlmoxarifadosDestino = new SelectMultiplo();
$obCmbAlmoxarifadosDestino->setName  ('inCodAlmoxarifadoDestino'            );
$obCmbAlmoxarifadosDestino->setRotulo("Almoxarifado de Destino"             );
$obCmbAlmoxarifadosDestino->setTitle ("Selecione o Almoxarifado de Destino.");

// lista de atributos disponiveis
$obCmbAlmoxarifadosDestino->SetNomeLista1 ('inCodAlmoxarifadoDestinoDisponivel');
$obCmbAlmoxarifadosDestino->setCampoId1   ('codigo'                             );
$obCmbAlmoxarifadosDestino->setCampoDesc1 ('[codigo]-[nom_a]'                   );
$obCmbAlmoxarifadosDestino->SetRecord1    ($rsAlmoxarifados                     );
$rsRecordset = new RecordSet;

// lista de atributos selecionados
$obCmbAlmoxarifadosDestino->SetNomeLista2 ('inCodAlmoxarifadoDestino');
$obCmbAlmoxarifadosDestino->setCampoId2   ('codigo'                             );
$obCmbAlmoxarifadosDestino->setCampoDesc2 ('[codigo]-[nom_a]'                   );
$obCmbAlmoxarifadosDestino->SetRecord2    ($rsRecordset                         );

$obTxtCodTransferencia = new TextBox;
$obTxtCodTransferencia->setRotulo   ("Código da Transferência"        );
$obTxtCodTransferencia->setTitle    ("Informe o código da nota de Tranferência.");
$obTxtCodTransferencia->setName     ("inCodTransferencia"                );
$obTxtCodTransferencia->setId       ("inCodTransferencia"                );
$obTxtCodTransferencia->setValue    ($inCodTransferencia                 );
$obTxtCodTransferencia->setSize     (5                                );
$obTxtCodTransferencia->setMaxLength(10                               );
$obTxtCodTransferencia->setInteiro  (true                             );

$obTxtObservacao = new TextBox;
$obTxtObservacao->setRotulo   ("Observação"          );
$obTxtObservacao->setTitle    ("Informe a observação");
$obTxtObservacao->setName     ("stObservacao"        );
$obTxtObservacao->setId       ("stObservacao"        );
$obTxtObservacao->setValue    ($stObservacao         );
$obTxtObservacao->setSize     (50                    );
$obTxtObservacao->setMaxLength(160                   );

$obCmbTipoBusca = new TipoBusca($obTxtObservacao);

$obBscItem = new IPopUpItem($obForm);
$obBscItem->setServico( false );
$obBscItem->setComSaldo( true );
$obBscItem->setNull(true);

$obBscMarca = new IPopUpMarca($obForm);
$obBscMarca->setTitle("Selecione a marca do item.");
$obBscMarca->setNull(true);

$obBscCentroCusto = new IPopUpCentroCusto($obForm);
$obBscCentroCusto->setNull(true);

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
//$obFormulario->setAjuda             ("UC-03.03.08");
$obFormulario->addHidden    ($obHdnCtrl                );
$obFormulario->addHidden    ($obHdnAcao                );
$obFormulario->addTitulo    ("Dados para filtro"       );
$obFormulario->addComponente($obTxtExercicio           );
$obFormulario->addComponente($obCmbAlmoxarifadosOrigem );
$obFormulario->addComponente($obCmbAlmoxarifadosDestino);
$obFormulario->addComponente($obTxtCodTransferencia    );
$obFormulario->addComponente($obCmbTipoBusca           );
$obFormulario->addComponente($obBscItem                );
$obFormulario->addComponente($obBscMarca               );
$obFormulario->addComponente($obBscCentroCusto         );
$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

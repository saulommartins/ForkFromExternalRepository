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
    * Página de Filtro do Relatório Emitir Cadastro do Servidor
    * Data de Criação : 24/06/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30547 $
    $Name$
    $Autor: $
    $Date: 2008-03-12 08:29:01 -0300 (Qua, 12 Mar 2008) $

    * Casos de uso: uc-04.04.50
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma = "EmitirFichaCadastro";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc );
$obForm->setTarget                              ( "oculto" );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setGrupoAtributoServidor();
$obIFiltroComponentes->setDisabledQuebra();
$obIFiltroComponentes->setTodos();

//Check para Listar demitidos
$obChkListarDemitidos = new CheckBox();
$obChkListarDemitidos->setRotulo("Listar Demitidos: ");
$obChkListarDemitidos->setName("boListarDemitidos");
$obChkListarDemitidos->setValue(true);
$obChkListarDemitidos->setTitle("Selecione a opção para Listar os servidores demitidos.");
$obChkListarDemitidos->setLabel("Listar Demitidos: ");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                   );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );

$obFormulario->addTitulo                        ( "Parâmetros para a Consulta"              );
$obFormulario->addHidden                        ( $obHdnAcao                                );
$obFormulario->addHidden                        ( $obHdnCtrl                                );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->addComponente                    ( $obChkListarDemitidos );
$obFormulario->Ok(true);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

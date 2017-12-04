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
    * Data de Criação: 15/04/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Liisane Morais

    $Id: FMManterEscola.php 59612 2014-09-02 12:00:51Z gelson $

 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterEscola";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//cria um novo formulario
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("telaPrincipal");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//BUSCA O INTERESSADO(POPUP CGM)
$obBuscaCGM = new IPopUpCGM( $obForm );
$obBuscaCGM->setRotulo               ( 'CGM Escola'  );
$obBuscaCGM->obCampoCod->setName     ( 'numCgm'      );
$obBuscaCGM->setNull                 ( false         );
$obBuscaCGM->setObrigatorio          ( true          );
$obBuscaCGM->obCampoCod->obEvento->setOnChange   ( "montaParametrosGET( 'carregaAtivo', 'numCgm' );" );

$obRdAtivoSim = new Radio();
$obRdAtivoSim->setName('boAtivo');
$obRdAtivoSim->setValue( 't' );
$obRdAtivoSim->setId( 'boAtivoSim' );
$obRdAtivoSim->setRotulo( 'Ativo' );
$obRdAtivoSim->setTitle( 'Informe se a escola é ativo.' );
$obRdAtivoSim->setLabel( 'Sim' );
$obRdAtivoSim->setChecked( true );
$obRdAtivoSim->setObrigatorio( true );
$obRdAtivoSim->setNull( true );

$obRdAtivoNao = new Radio();
$obRdAtivoNao->setName('boAtivo');
$obRdAtivoNao->setId( 'boAtivoNao' );
$obRdAtivoNao->setValue( 'f' );
$obRdAtivoNao->setRotulo( 'Exige Prefixo' );
$obRdAtivoNao->setTitle( 'Informe se o posto é ativo.' );
$obRdAtivoNao->setLabel( 'Não' );
$obRdAtivoNao->setObrigatorio( true );
$obRdAtivoNao->setNull( true );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addTitulo    ( 'Dados da Escola'             );
$obFormulario->addForm      ( $obForm                       );
$obFormulario->addHidden    ( $obHdnAcao                    );
$obFormulario->addHidden    ( $obHdnCtrl                    );
$obFormulario->addComponente( $obBuscaCGM                   );
$obFormulario->agrupaComponentes( array( $obRdAtivoSim, $obRdAtivoNao) );

$obFormulario->OK(true);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

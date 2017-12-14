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
    * Página de Formulário Almoxarifado
    * Data de Criação   : 22/11/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    $Id: FLManterCentroCusto.php 64005 2015-11-17 16:49:06Z michel $

    * @ignore

    * Casos de uso: uc-03.03.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO.'RAlmoxarifadoCentroDeCustos.class.php';

$stPrograma = "ManterCentroCusto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRegra = new RAlmoxarifadoCentroDeCustos();
$obRegra->roUltimaEntidade->setExercicio ( Sessao::getExercicio() );
$obRegra->roUltimaEntidade->listar( $rsEntidade );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'alterar');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $request->get('nomForm') );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get('campoNum') );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get('campoNom') );

$obHdnUsuario = new Hidden;
$obHdnUsuario->setName( "usuario" );
$obHdnUsuario->setValue( $request->get('usuario', FALSE) );

$obForm = new Form;
$obForm->setAction( $pgList );
// Define SELECT multiplo para codigo da entidade
$obCmbEntidade = new SelectMultiplo();
$obCmbEntidade->setName       ( 'inCodEntidade'          );
$obCmbEntidade->setRotulo     ( "Entidades"              );
$obCmbEntidade->setTitle      ( "Selecione as entidades.");
// lista de atributos disponiveis
$obCmbEntidade->SetNomeLista1 ('inCodEntidadeDisponivel' );
$obCmbEntidade->setCampoId1   ( 'cod_entidade'           );
$obCmbEntidade->setCampoDesc1 ( 'nom_cgm'                );
$obCmbEntidade->SetRecord1    ( $rsEntidade              );
// lista de atributos selecionados
$obCmbEntidade->SetNomeLista2 ( 'inCodEntidade'          );
$obCmbEntidade->setCampoId2   ( 'cod_entidade'           );
$obCmbEntidade->setCampoDesc2 ( 'nom_cgm'                );
$obCmbEntidade->SetRecord2    ( new Recordset()          );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo      ( "Descrição"  );
$obTxtDescricao->setName        ( "stDescricao" );
$obTxtDescricao->setSize        ( 50 );
$obTxtDescricao->setMaxLength   ( 160 );
$obTxtDescricao->setTitle       ( "Informe a descrição do nível" );
$obTxtDescricao->setValue       ( isset($stDescricao) ? $stDescricao : null );

$obCmpTipoBusca = new TipoBusca( $obTxtDescricao );

$obFormulario = new Formulario;
$obFormulario->addTitulo( "Dados Para o Filtro" );
$obFormulario->addForm  ( $obForm               );
$obFormulario->addHidden( $obHdnAcao            );
$obFormulario->addHidden( $obHdnUsuario         );
$obFormulario->addHidden( $obHdnForm            );
$obFormulario->addHidden( $obHdnCampoNum        );
$obFormulario->addHidden( $obHdnCampoNom        );
$obFormulario->addComponente( $obCmbEntidade    );
$obFormulario->addComponente( $obCmpTipoBusca   );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

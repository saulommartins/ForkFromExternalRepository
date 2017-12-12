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
* Arquivo de instância para manutenção de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28380 $
$Name$
$Author: rodrigosoares $
$Date: 2008-03-05 14:52:21 -0300 (Qua, 05 Mar 2008) $
$Id: FLConsultarNorma.php 60663 2014-11-06 18:22:19Z evandro $

Casos de uso: uc-01.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");

$stPrograma = "ConsultarNorma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::remove('link');
$rsTipoNorma = $rsAtributos = new RecordSet;
$obRegra = new RNorma;

$obRegra->obRTipoNorma->obRCadastroDinamico->verificaModulo();
$obRegra->obRTipoNorma->listar( $rsTipoNorma );

$stAcao = $request->get('stAcao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo        ( "Tipo" );
$obTxtTipoNorma->setName          ( "inCodTipoNorma" );
$obTxtTipoNorma->setValue         ( $inCodTipoNorma );
$obTxtTipoNorma->setSize          ( 5 );
$obTxtTipoNorma->setMaxLength     ( 5 );
$obTxtTipoNorma->setInteiro       ( true  );

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo        ( "Tipo" );
$obCmbTipoNorma->setName          ( "stNomTipoNorma" );
$obCmbTipoNorma->setStyle         ( "width: 200px");
$obCmbTipoNorma->setCampoID       ( "cod_tipo_norma" );
$obCmbTipoNorma->setCampoDesc     ( "nom_tipo_norma" );
$obCmbTipoNorma->addOption        ( "", "Selecione" );
$obCmbTipoNorma->setValue         ( $inCodTipoNorma );
$obCmbTipoNorma->preencheCombo    ( $rsTipoNorma );

$obTxtNorma = new TextBox;
$obTxtNorma->setRotulo        ( "Número da Norma" );
$obTxtNorma->setTitle         ( "Informe o número da norma" );
$obTxtNorma->setName          ( "inNumNorma" );
$obTxtNorma->setValue         ( $inNumNorma  );
$obTxtNorma->setSize          ( 6 );
$obTxtNorma->setMaxLength     ( 6 );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setRotulo            ( "Exercício" );
$obTxtExercicio->setTitle             ( "Informe o exercício da norma" );
$obTxtExercicio->setName              ( "stExercicio" );
$obTxtExercicio->setValue             ( $stExercicio  );
$obTxtExercicio->setSize              ( 6 );
$obTxtExercicio->setMaxLength         ( 4 );
$obTxtExercicio->setInteiro           ( true  );

$obTxtNome = new TextBox;
$obTxtNome->setRotulo        ( "Nome" );
$obTxtNome->setName          ( "stNomeNorma" );
$obTxtNome->setValue         ( $stNomeNorma  );
$obTxtNome->setSize          ( 80 );
$obTxtNome->setMaxLength     ( 80 );
$obTxtNome->setTitle         ( "Informe o nome da norma" );

$obTipoBusca = new Select();
$obTipoBusca->setName   ( "stTipoBusca"      );
$obTipoBusca->setStyle  ( "width: 80px"      );
$obTipoBusca->addOption ( "inicio", "Início" );
$obTipoBusca->addOption ( "final" , "Final"  );
$obTipoBusca->addOption ( "contem", "Contém" );
$obTipoBusca->addOption ( "exata" , "Exata"  );

$obTxtDescricao = new TextArea;
$obTxtDescricao->setRotulo        ( "Descrição" );
$obTxtDescricao->setName          ( "stDescricao" );
$obTxtDescricao->setValue         ( $stDescricao  );
$obTxtDescricao->setTitle         ( "Informe a descrição da norma" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgList );
$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->setAjuda             ( "UC-01.04.02" );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para filtro" );
$obFormulario->addComponenteComposto( $obTxtTipoNorma , $obCmbTipoNorma );
$obFormulario->addComponente        ( $obTxtNorma         );
$obFormulario->addComponente        ( $obTxtExercicio     );
$obFormulario->agrupaComponentes    ( array($obTxtNome, $obTipoBusca)   );
$obFormulario->addComponente        ( $obTxtDescricao     );

$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

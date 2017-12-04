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

$Revision: 27842 $
$Name$
$Author: domluc $
$Date: 2008-01-31 10:15:44 -0200 (Qui, 31 Jan 2008) $

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
$pgOcul = "OCManterNorma.php"."?".Sessao::getId()."&stCtrl=Anexos&cod_norma=".$_REQUEST['inCodNorma']."&anexo=";
$pgJs   = "JS".$stPrograma.".js";

$rsNorma = $rsTipoNorma = $rsAtributos = new RecordSet;
$obRegra = new RNorma;

$obRegra->obRTipoNorma->obRCadastroDinamico->obRModulo->setCodModulo(15);
$obRegra->obRTipoNorma->obRCadastroDinamico->verificaModulo();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRegra->setCodNorma( $_REQUEST['inCodNorma'] );
$obRegra->consultar( $rsNorma );
$stNomeNorma     = $obRegra->getNomeNorma();
$inNumNorma      = $obRegra->getNumNorma();
$stExercicio     = $obRegra->getExercicio();
$stNomeTipoNorma = $obRegra->obRTipoNorma->getNomeTipoNorma();
$inCodTipoNorma  = $obRegra->obRTipoNorma->getCodTipoNorma();

$stDescricao     				  = $obRegra->getDescricaoNorma();
$stDataPublicacao				  = $obRegra->getDataPublicacao();
$stDataAssinatura				  = $obRegra->getDataAssinatura();
$stDataTermino   				  = $obRegra->getDataTermino();
$stUrl          				  = $obRegra->getUrl();
#$stNomeArquivo = sessao->transf3['stNomeArquivo'] = $obRegra->getUrl();
$stNomeArquivo = $obRegra->getUrl();

$stDirAnexos = realpath(CAM_NORMAS.'anexos/')."/";

$stCaminhoCompleto = $stDirAnexos . $stNomeArquivo;

$obRegra->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
$obRegra->obRTipoNorma->obRCadastroDinamico->setChavePersistenteValores( array("cod_tipo_norma"=>$inCodTipoNorma, "cod_norma"=>$obRegra->getCodNorma()) );
$obRegra->obRTipoNorma->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setName ("Atributo_");
$obMontaAtributos->setLabel( true );
$obMontaAtributos->setRecordSet( $rsAtributos );
$obMontaAtributos->recuperaValores();

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obLblTipoNorma = new Label;
$obLblTipoNorma->setRotulo        ( "Tipo" );
$obLblTipoNorma->setName          ( "stNomeTipoNorma" );
$obLblTipoNorma->setValue         ( $stNomeTipoNorma );

$obHdnCodNorma = new Hidden;
$obHdnCodNorma->setName ( "inNumNorma" );
$obHdnCodNorma->setValue( $inNumNorma  );

$obHdnCodTipoNorma = new Hidden;
$obHdnCodTipoNorma->setName ( "inCodTipoNorma" );
$obHdnCodTipoNorma->setValue( $inCodTipoNorma  );

$obLblNome = new Label;
$obLblNome->setRotulo        ( "Nome" );
$obLblNome->setName          ( "stNomeNorma" );
$obLblNome->setValue         ( $stNomeNorma );

$obLblCodNorma = new Label;
$obLblCodNorma->setRotulo        ( "Número da norma" );
$obLblCodNorma->setName          ( "inNumNorma" );
$obLblCodNorma->setValue         ( $inNumNorma  );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo        ( "Exercício" );
$obLblExercicio->setName          ( "stExercicio" );
$obLblExercicio->setValue         ( $stExercicio  );

$obLblDescricao = new Label;
$obLblDescricao->setRotulo        ( "Descrição" );
$obLblDescricao->setName          ( "stDescricao" );
$obLblDescricao->setValue         ( $stDescricao );

$obLblData = new Label;
$obLblData->setRotulo        ( "Data de Publicação" );
$obLblData->setName          ( "stDataPublicacao" );
$obLblData->setValue         ( $stDataPublicacao );

$obLblDataAssinatura = new Label;
$obLblDataAssinatura->setRotulo        ( "Data de Assinatura" );
$obLblDataAssinatura->setName          ( "stDataAssinatura" );
$obLblDataAssinatura->setValue         ( $stDataAssinatura  );

$obLblDataTermino = new Label;
$obLblDataTermino->setRotulo        ( "Data de Término" );
$obLblDataTermino->setName          ( "stDataTermino" );
$obLblDataTermino->setValue         ( $stDataTermino  );

$obLocalizacao = new Link;
$obLocalizacao->setRotulo("Arquivo");
$obLocalizacao->setHref( 'download.php?arquivo='.$stCaminhoCompleto);
$obLocalizacao->setValue ($stUrl);
$obLocalizacao->setTarget("oculto");

$obBtnVoltar = new Button;
$obBtnVoltar->setName( "btnVoltar" );
$obBtnVoltar->setValue( "Voltar" );
$obBtnVoltar->obEvento->setOnClick ( "voltar ();" );

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

$obFormulario->addTitulo            ( "Dados da Norma" );
$obFormulario->addHidden            ( $obHdnCodNorma );
$obFormulario->addHidden            ( $obHdnCodTipoNorma );
$obFormulario->addComponente        ( $obLblTipoNorma );
$obFormulario->addComponente        ( $obLblCodNorma );
$obFormulario->addComponente        ( $obLblExercicio );

$obFormulario->addComponente        ( $obLblNome );
$obFormulario->addComponente        ( $obLblDescricao );
$obFormulario->addComponente        ( $obLblData );
$obFormulario->addComponente        ( $obLblDataAssinatura );
$obFormulario->addComponente        ( $obLblDataTermino );

//if(!($stUrl == "http://"))
$obFormulario->addComponente        ( $obLocalizacao );

$obMontaAtributos->geraFormulario   ( $obFormulario );

//$obFormulario->voltar               ();
$obFormulario->defineBarra          ( array( $obBtnVoltar ) ,'','');
$obFormulario->show                 ();

include_once($pgJs);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

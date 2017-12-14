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
* Popup para inclusão de arquivos anexos aos processo
* Data de Criação: 17/10/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 17330 $
$Name$
$Author: cassiano $
$Date: 2006-10-31 12:59:22 -0300 (Ter, 31 Out 2006) $

Casos de uso: uc-01.06.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_PROT_MAPEAMENTO."TPRODocumento.class.php";
include_once CAM_GA_PROT_MAPEAMENTO."TPROCopiaDigital.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "DocumentoProcesso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once($pgJs);

$inCodProcesso = (!empty($_REQUEST['inCodProcesso'])) ? $_REQUEST['inCodProcesso'] : Sessao::read('codigo_processo');
$stAnoProcesso = (!empty($_REQUEST['stAnoProcesso'])) ? $_REQUEST['stAnoProcesso'] : Sessao::getExercicio();
$inCodDocumento = $_GET['codDoc'];

$obTPRODocumento = new TPRODocumento();
$obTPRODocumento->setDado('cod_documento',$inCodDocumento);
$obTPRODocumento->recuperaPorChave($rsDocumento);

$obTPROCopiaDigital = new TPROCopiaDigital();
$obTPROCopiaDigital->setDado('cod_documento' , $inCodDocumento);
$obTPROCopiaDigital->setDado('cod_processo'  , $inCodProcesso);
$obTPROCopiaDigital->setDado('exercicio'     , $stAnoProcesso);
$obTPROCopiaDigital->setCampoCod('');
$obTPROCopiaDigital->recuperaPorChave($rsDocumentos);

$obHdnDocumento = new Hidden();
$obHdnDocumento->setName('inCodDocumento');
$obHdnDocumento->setValue($inCodDocumento);

$obHdnCodProcesso = new Hidden();
$obHdnCodProcesso->setName('inCodProcesso');
$obHdnCodProcesso->setValue($inCodProcesso);

$obHdnAnoProcesso = new Hidden();
$obHdnAnoProcesso->setName('stAnoProcesso');
$obHdnAnoProcesso->setValue($stAnoProcesso);

$obSpnListaAnexos = new Span;
$obSpnListaAnexos->setId ( "spnListaAnexos" );

$obRdImagemSim = new Radio();
$obRdImagemSim->setChecked( true );
$obRdImagemSim->setName('boImagem');
$obRdImagemSim->setLabel('Sim');
$obRdImagemSim->setRotulo('Imagem');
$obRdImagemSim->setValue( 't' );

$obRdImagemNao = new Radio();
$obRdImagemNao->setChecked( false );
$obRdImagemNao->setName('boImagem');
$obRdImagemNao->setLabel('Não');
$obRdImagemNao->setRotulo('Imagem');
$obRdImagemNao->setValue( 'f' );

$obFleArquivo = new FileBox();
$obFleArquivo->setName('stArquivo');
$obFleArquivo->setRotulo('*Arquivo');
$obFleArquivo->setSize( 20 );

$obBtnOk = new Ok();

$obBtnFechar = new Button();
$obBtnFechar->setValue('Fechar');
$obBtnFechar->obEvento->setOnclick('window.close();');

$obForm = new Form();
$obForm->setEncType('multipart/form-data');
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnDocumento);
$obFormulario->addHidden($obHdnCodProcesso);
$obFormulario->addHidden($obHdnAnoProcesso);
$obFormulario->addTitulo($rsDocumento->getCampo('nom_documento'));
$obFormulario->agrupaComponentes(array($obRdImagemSim, $obRdImagemNao));
$obFormulario->addComponente($obFleArquivo);
$obFormulario->defineBarra(array($obBtnOk,$obBtnFechar));
$obFormulario->addSpan($obSpnListaAnexos);
$obFormulario->show();

$obIFrameOculto = new IFrame();
$obIFrameOculto->setName('oculto');
$obIFrameOculto->setHeight ('0%');
$obIFrameOculto->setWidth  ('100%');
$obIFrameOculto->setFrameBorder(0);
$obIFrameOculto->show();

$obIFrame = new IFrame();
$obIFrame->setName('telaMensagem');
$obIFrame->setSrc(CAM_FW_INSTANCIAS.'index/mensagem.php?'.Sessao::getId());
$obIFrame->setHeight('20%');
$obIFrame->setWidth('100%');
$obIFrame->setFrameBorder(0);
$obIFrame->show();

$stJs = "<script>montaParametrosGET('montaListaAnexos');</script>";

echo ($stJs);
?>

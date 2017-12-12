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
 * Cadastro de fotos para imóveis já cadastrados no sistema
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Cassiano de Vasconcellos Ferreira <cassiano.ferreira@cnm.org.br>
 * @author     Desenvolvedor Cassiano de Vasconcellos Ferreira <cassiano.ferreira@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelFoto.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterImovelFoto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LSBuscaLote.php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}
Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setId( "stAcao" );
$obHdnAcao->setValue( "incluir" );

$obHdnIncricao = new Hidden;
$obHdnIncricao->setName( "inInscricaoMunicipal" );
$obHdnIncricao->setValue( $_REQUEST['inInscricaoMunicipal'] );

$obHdnLote = new Hidden;
$obHdnLote->setName( "stValorLote" );
$obHdnLote->setValue( $_REQUEST['stValorLote'] );

$obHdnCodFoto = new Hidden;
$obHdnCodFoto->setName( "inCodFoto" );
$obHdnCodFoto->setId( "inCodFoto" );

$obHdnFileSize = new Hidden;
$obHdnFileSize->setName('MAX_FILE_SIZE');
$obHdnFileSize->setValue(2048000);

$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setRotulo    ( "Número da Inscrição" );
$obLblNumeroInscricao->setTitle     ( "Número da inscrição imobiliária" );
$obLblNumeroInscricao->setValue     ( $_REQUEST['inInscricaoMunicipal'] );

$obLblLote = new Label;
$obLblLote->setRotulo    ( "Número do Lote" );
$obLblLote->setTitle     ( "Número do lote do imóvel" );
$obLblLote->setValue     ( $_REQUEST['stValorLote'] );

$obTxtNumeroFotos = new TextBox;
$obTxtNumeroFotos->setName      ("inNumeroFoto");
$obTxtNumeroFotos->setId        ("inNumeroFoto");
$obTxtNumeroFotos->setMaxLength (2);
$obTxtNumeroFotos->setSize      (4);
$obTxtNumeroFotos->setRotulo    ("Quantidade de fotos");
$obTxtNumeroFotos->setTitle     ("Informe o número de fotos para o imóvel");
$obTxtNumeroFotos->setInteiro   (true);
$obTxtNumeroFotos->obEvento->setOnChange("JavaScript:montaSpanFileBox();");

$obBtnOK = new OK;
$obBtnOK->setName("stBtnIncluirFoto");
$obBtnOK->setValue("Incluir");

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "limparFiltro()" );

$obImageBox= new ImageBox();
$obImageBox->setRotulo("Fotos do imóvel");

$obTCIMImovelFoto = new TCIMImovelFoto();
$obTCIMImovelFoto->recuperaFotosPorInscricao($_REQUEST['inInscricaoMunicipal'],$rsFotos);

while (!$rsFotos->eof() ) {
    $obImg = new Img();
    $obImg->setId('idImagem_'.$rsFotos->getCampo('cod_foto'));

    $stURL = $pgOcul."?".Sessao::read('sessao_id').'&stCtrl=carregaImagem';
    $stURL.= '&inCodFoto='.$rsFotos->getCampo('cod_foto').'&inInscricaoMunicipal='.$_REQUEST['inInscricaoMunicipal'];
    $obImg->setCaminho($stURL);
    $obImageBox->addImagem($rsFotos->getCampo('descricao')? $rsFotos->getCampo('descricao'):'Foto '.$rsFotos->getCampo('cod_foto'),$obImg,'Excluir','excluirImagem('.$rsFotos->getCampo('cod_foto').')');
    $rsFotos->proximo();
}

Sessao::write('obImageBox',$obImageBox);

$obSpan=new Span;
$obSpan->setId("spnFileBox");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
$obForm->setEncType("multipart/form-data");

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm               );
$obFormulario->addHidden            ( $obHdnCtrl            );
$obFormulario->addHidden            ( $obHdnAcao            );
$obFormulario->addHidden            ( $obHdnIncricao        );
$obFormulario->addHidden            ( $obHdnLote            );
$obFormulario->addHidden            ( $obHdnCodFoto         );
$obFormulario->addHidden            ( $obHdnFileSize        );
$obFormulario->addTitulo            ( "Fotos para o Imóvel" );
$obFormulario->addComponente($obLblNumeroInscricao);
$obFormulario->addComponente($obLblLote);
$obFormulario->addComponente($obTxtNumeroFotos);
$obFormulario->addSpan($obSpan);
$obFormulario->defineBarraAba   ( array($obBtnOK ),"","" );
$obFormulario->addComponente($obImageBox);
$obFormulario->show();
?>

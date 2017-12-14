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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 11/03/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: FMManterConfiguracaoDMR.php 59612 2014-09-02 12:00:51Z gelson $
  * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
  * $Author: gelson $
  * $Rev: 59612 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TGO_MAPEAMENTO."TTCMGOConfigurarArquivoDMR.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDMR";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
$obForm->setName('frm');
//Define o objeto da ação stAcao
$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "stModulo" );
$obHdnModulo->setValue( $stModulo );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "" );


$rsDecretos = new RecordSet();
$obTTCMGOConfigurarArquivoDMR = new TTCMGOConfigurarArquivoDMR();
$obTTCMGOConfigurarArquivoDMR->setDado('exercicio', Sessao::getExercicio());
$obTTCMGOConfigurarArquivoDMR->recuperaDecretos($rsDecretos);

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Decretos Municipal');
$obLista->setRecordSet($rsDecretos);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Decreto', 20);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[num_norma_exercicio] - [nom_norma]');
$obLista->commitDado();

$arTipoDecreto[0]["tipo_decreto"] = "1";
$arTipoDecreto[0]["cod_tipo_decreto"] = "1";
$arTipoDecreto[0]["nom_tipo_decreto"] = "Registro de preço";
$arTipoDecreto[1]["tipo_decreto"] = "2";
$arTipoDecreto[1]["cod_tipo_decreto"] = "2";
$arTipoDecreto[1]["nom_tipo_decreto"] = "Pregão";
$rsTipoDecreto = new Recordset();
$rsTipoDecreto->preenche($arTipoDecreto);

$obCmbTipoDecreto = new Select();
$obCmbTipoDecreto->setName( 'inTipoDecreto_[cod_norma]' );
$obCmbTipoDecreto->setId( 'inTipoDecreto_[cod_norma]' );
$obCmbTipoDecreto->setValue( 'tipo_decreto' );
$obCmbTipoDecreto->addOption( '', 'Selecione' );
$obCmbTipoDecreto->setCampoId( '[cod_tipo_decreto]' );
$obCmbTipoDecreto->setCampoDesc( 'nom_tipo_decreto' );
$obCmbTipoDecreto->setStyle('width:250px;');
$obCmbTipoDecreto->preencheCombo( $rsTipoDecreto );

$obLista->addCabecalho('Tipo de Decreto', 5);
$obLista->addDadoComponente( $obCmbTipoDecreto , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "tipo_decreto" );
$obLista->commitDadoComponente();


// Define a Lista de Decretos
$obSpnDecretos = new Span();
$obSpnDecretos->setId('spnDecretos');
$obLista->montaHTML();
$obSpnDecretos->setValue($obLista->getHTML());


//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Parâmetros por Orgão" );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnModulo );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addSpan       ( $obSpnDecretos );

$obFormulario->OK();
$obFormulario->show();


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
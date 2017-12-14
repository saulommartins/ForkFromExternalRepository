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

    * Pacote de configuração do TCEPE
    * Data de Criação   : 26/09/2014

    * @author Analista: Silvia Martins
    * @author Desenvolvedor: Lisiane Morais
    * 
    * $id: $
    
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php"                                );
include_once(CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEConfiguracaoOrdenador.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoOrdenador";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once $pgJS;
include_once ($pgOcul);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if (empty($stAcao)) {
    $stAcao = "manter";
}
$arEntidade = explode('-',$_REQUEST['inCodEntidade']);
$stUnidadeOrcamentaria = $request->get('stUnidadeOrcamentaria');

$arUnidadeOrcamentaria = explode('.',$stUnidadeOrcamentaria);

$obTTCEPEConfiguracaoOrdenador = new TTCEPEConfiguracaoOrdenador();
$obTTCEPEConfiguracaoOrdenador->setDado('cod_entidade',$arEntidade[0]);
$obTTCEPEConfiguracaoOrdenador->setDado('exercicio',Sessao::getExercicio());
$obTTCEPEConfiguracaoOrdenador->setDado("num_orgao",$arUnidadeOrcamentaria[0]);
$obTTCEPEConfiguracaoOrdenador->setDado("num_unidade",$arUnidadeOrcamentaria[1]);
$obTTCEPEConfiguracaoOrdenador->recuperaPorChave($rsRecordOrdenador, $boTransacao);

$num_orgao =  SistemaLegado::pegaDado('num_orgao','orcamento.orgao','WHERE num_orgao ='.$arUnidadeOrcamentaria[0]);
$nom_orgao =  SistemaLegado::pegaDado('nom_orgao','orcamento.orgao','WHERE num_orgao ='.$arUnidadeOrcamentaria[0]);

$num_unidade =  SistemaLegado::pegaDado('num_unidade','orcamento.unidade','WHERE num_orgao ='.$arUnidadeOrcamentaria[0].' AND num_unidade ='.$arUnidadeOrcamentaria[1]);
$nom_unidade =  SistemaLegado::pegaDado('nom_unidade','orcamento.unidade','WHERE num_orgao ='.$arUnidadeOrcamentaria[0].' AND num_unidade ='.$arUnidadeOrcamentaria[1]);

$obHdnCodOrgao = new Hidden;
$obHdnCodOrgao->setName ("inMontaCodOrgaoM");
$obHdnCodOrgao->setValue($num_orgao);

$obHdnCodUnidade = new Hidden;
$obHdnCodUnidade->setName ("inMontaCodUnidadeM");
$obHdnCodUnidade->setValue($num_unidade);

$arOrdenadores = array();
$arOrdenadores = $rsRecordOrdenador->getElementos();

$inCount = 0;

foreach ( $arOrdenadores as $ordenador ) {
    $ordenador["nom_cgm"] = SistemaLegado::pegaDado('nom_cgm','sw_cgm','WHERE numcgm ='.$ordenador['cgm_ordenador']);
    $ordenador["inId"] = $inCount;
    $arrOrdenadores[$inCount] = $ordenador;
    $inCount++;
}

Sessao::write("arOrdenadores", $arrOrdenadores);

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');
    
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//Define o objeto da ação stAcao
$obHdnStAcao = new Hidden;
$obHdnStAcao->setName ( "stHdnAcao" );
$obHdnStAcao->setId   ( "stHdnAcao" );
$obHdnStAcao->setValue( $stAcao );

//Define o objeto da ação stAcao
$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "stModulo" );
$obHdnModulo->setValue( $stModulo );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "" );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ( "hdnCodEntidade" );
$obHdnEntidade->setValue( $arEntidade[0] );

$obLblEntidade = new Label;
$obLblEntidade->setName   ( "stEntidade"               );
$obLblEntidade->setValue  ( $_REQUEST['inCodEntidade'] );
$obLblEntidade->setRotulo ( 'Entidade'                 );

$obHdnInId = new Hidden;
$obHdnInId->setName("hdnInId");
$obHdnInId->setId  ("hdnInId");

$obLblOrgao = new Label;
$obLblOrgao->setName   ( "stCodOrgao"               );
$obLblOrgao->setValue  ( $num_orgao.' - '.$nom_orgao );
$obLblOrgao->setRotulo ( 'Órgão'                 );

$obLblUnidade = new Label;
$obLblUnidade->setName   ( "stCodUnidade"               );
$obLblUnidade->setValue  ( $num_unidade.' - '.$nom_unidade );
$obLblUnidade->setRotulo ( 'Unidade'                 );

$obCgmOrdenador =  new IPopUpCGMVinculado($obForm);
$obCgmOrdenador->setTabelaVinculo 	 ( "sw_cgm_pessoa_fisica"           );
$obCgmOrdenador->setCampoVinculo 	 ( "numcgm" 			    );
$obCgmOrdenador->setNomeVinculo          ( "Ordenador" 			    );
$obCgmOrdenador->setRotulo	         ( "Ordenador"			    );
$obCgmOrdenador->setTitle	         ( "Selecione o CGM do Ordenador"   );
$obCgmOrdenador->setNull 	         ( true 		            );
$obCgmOrdenador->setName   	         ( "stNomCgmOrdenador"		    );
$obCgmOrdenador->setId     	         ( "stNomCgmOrdenador"		    );
$obCgmOrdenador->obCampoCod->setName     ( "inCgmOrdenador" 		    );
$obCgmOrdenador->obCampoCod->setId       ( "inCgmOrdenador" 		    );
$obCgmOrdenador->obCampoCod->setNull     ( true               		    );
$obCgmOrdenador->setTipo                 ( 'fisica'                         );

// Campo de Data Inicial
$obDataInicial = new Data();
$obDataInicial->setId	                ('dtDataInicio');
$obDataInicial->setName	                ('dtDataInicio');
$obDataInicial->setRotulo               ('Início da Vigência');
$obDataInicial->setNullBarra            (false         );

// Campo de Data Final
$obDataFinal = new Data();
$obDataFinal->setId	                ('dtDataFim');
$obDataFinal->setName	                ('dtDataFim');
$obDataFinal->setRotulo	                ('Fim da Vigência');
$obDataFinal->setNullBarra              (false);

$obBtnIncluirOrdenador = new Button;
$obBtnIncluirOrdenador->setName             ( "btIncluirOrdenador"                                         );
$obBtnIncluirOrdenador->setId               ( "btIncluirOrdenador"                                         );
$obBtnIncluirOrdenador->setValue            ( "Incluir"                                                    );
$obBtnIncluirOrdenador->obEvento->setOnClick( "buscaValor('incluirOrdenador');"                            );
$obBtnIncluirOrdenador->setTitle            ( "Clique para incluir um ordenador na lista de Ordenadores"   );

$obSpnCGMsOrdenador = new Span();
$obSpnCGMsOrdenador->setId("spnCGMsOrdenadores");

// Definição do Formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnStAcao);
$obFormulario->addHidden($obHdnModulo);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnEntidade);
$obFormulario->addHidden($obHdnInId);
$obFormulario->addHidden($obHdnCodOrgao);
$obFormulario->addHidden($obHdnCodUnidade);
$obFormulario->addTitulo('Unidade Orçamentária');
$obFormulario->addComponente($obLblEntidade);
$obFormulario->addComponente($obLblOrgao);
$obFormulario->addComponente($obLblUnidade);
$obFormulario->addTitulo('Configuração de Ordenador');
$obFormulario->addComponente($obCgmOrdenador);
$obFormulario->addComponente($obDataInicial);
$obFormulario->addComponente($obDataFinal);
$obFormulario->addComponente($obBtnIncluirOrdenador);
$obFormulario->addSpan($obSpnCGMsOrdenador);

$obFormulario->Cancelar($pgFilt);
$obFormulario->show();

processarForm(true,"Form",$stAcao);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
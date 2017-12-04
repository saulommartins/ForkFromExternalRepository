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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 04/05/2016

    * @author Analista: Ane Caroline
    * @author Desenvolvedor: Lisiane da Rosa Morais
    *
    * $Id: FMManterConfiguracaoRgfRreo.php 65345 2016-05-13 18:07:34Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GPC_TCEAL_MAPEAMENTO."TTCEALPublicacaoRGF.class.php";
include_once CAM_GPC_TCEAL_MAPEAMENTO."TTCEALPublicacaoRREO.class.php";

$stPrograma = 'ManterConfiguracaoRgfRreo';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');
$jsOnLoad = "";

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "" );

//Define o objeto de controle do id na listagem do veiculo de publicação
$obHdnCodVeiculo= new Hidden;
$obHdnCodVeiculo->setName  ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setId    ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setValue ( ""              );

$arCodEntidade = explode('_', $request->get('inCodEntidade'));

$request->set('inCodEntidade', $arCodEntidade[0]);
$request->set('stNomEntidade', $arCodEntidade[1]);

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "cod_entidade" );
$obHdnCodEntidade->setId    ( "cod_entidade" );
$obHdnCodEntidade->setValue ( $request->get('inCodEntidade') );

//recupera os veiculos de publicacao de RGF, coloca na sessao e manda para o oculto
$obTTCEALPublicacaoRGF = new TTCEALPublicacaoRGF();
$obTTCEALPublicacaoRGF->setDado('exercicio', Sessao::getExercicio());
$obTTCEALPublicacaoRGF->setDado('cod_entidade',$request->get('inCodEntidade'));
$obTTCEALPublicacaoRGF->recuperaVeiculosPublicacao( $rsVeiculosPublicacaoRGF,'','',$boTransacao ); 
$inCount = 0;
$arValoresRGF = array();

while ( !$rsVeiculosPublicacaoRGF->eof() ) {
    $arValoresRGF[$inCount]['id'            ]   = $inCount + 1;
    $arValoresRGF[$inCount]['inVeiculo'     ]   = $rsVeiculosPublicacaoRGF->getCampo( 'num_veiculo' );
    $arValoresRGF[$inCount]['stVeiculo'     ]   = $rsVeiculosPublicacaoRGF->getCampo( 'nom_veiculo' );
    $arValoresRGF[$inCount]['dtDataPublicacao'] = $rsVeiculosPublicacaoRGF->getCampo( 'dt_publicacao' );
    $arValoresRGF[$inCount]['inNumPublicacao' ] = $rsVeiculosPublicacaoRGF->getCampo( 'num_publicacao' );
    $arValoresRGF[$inCount]['stObservacao'    ] = $rsVeiculosPublicacaoRGF->getCampo( 'observacao' );
    $inCount++;
    $rsVeiculosPublicacaoRGF->proximo();
}
Sessao::write('arValoresRGF', $arValoresRGF);

$obLblEntidade = new Label ();
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setId('stEntidade');
$obLblEntidade->setName('stEntidade');
$obLblEntidade->setValue($request->get('stNomEntidade'));

//Painel veiculos de publicidade RGF
$obVeiculoPublicidadeRGF = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicidadeRGF->setTabelaVinculo       ( 'licitacao.veiculos_publicidade'    );
$obVeiculoPublicidadeRGF->setCampoVinculo        ( 'numcgm'                            );
$obVeiculoPublicidadeRGF->setNomeVinculo         ( 'Veículo de Publicação RGF'         );
$obVeiculoPublicidadeRGF->setRotulo              ( '**Veículo de Publicação RGF'       );
$obVeiculoPublicidadeRGF->setTitle               ( 'Informe o Veículo de Publicidade.' );
$obVeiculoPublicidadeRGF->setName                ( 'stNomCgmVeiculoPublicadadeRGF'     );
$obVeiculoPublicidadeRGF->setId                  ( 'stNomCgmVeiculoPublicadadeRGF'     );
$obVeiculoPublicidadeRGF->obCampoCod->setName    ( 'inVeiculoRGF'                      );
$obVeiculoPublicidadeRGF->obCampoCod->setId      ( 'inVeiculoRGF'                      );
$obVeiculoPublicidadeRGF->setNull                ( true );
$obVeiculoPublicidadeRGF->obCampoCod->setNull    ( true );

$obDataPublicacaoRGF = new Data();
$obDataPublicacaoRGF->setId     ( "dtDataPublicacaoRGF"           );
$obDataPublicacaoRGF->setName   ( "dtDataPublicacaoRGF"           );
$obDataPublicacaoRGF->setValue  ( date('d/m/Y')                   );
$obDataPublicacaoRGF->setRotulo ( "Data de Publicação"            );
$obDataPublicacaoRGF->setObrigatorioBarra( true                   );
$obDataPublicacaoRGF->setTitle  ( "Informe a data de publicação." );

$obNumeroPublicacaoRGF = new Inteiro();
$obNumeroPublicacaoRGF->setId     ( "inNumPublicacaoRGF"              );
$obNumeroPublicacaoRGF->setName   ( "inNumPublicacaoRGF"              );
$obNumeroPublicacaoRGF->setValue  ( ""                                );
$obNumeroPublicacaoRGF->setRotulo ( "Número Publicação"               );
$obNumeroPublicacaoRGF->setObrigatorioBarra( false                    );
$obNumeroPublicacaoRGF->setTitle  ( "Informe o Número da Publicação." );

//Campo Observação da Publicação
$obTxtObservacaoRGF = new TextArea;
$obTxtObservacaoRGF->setId     ( "stObservacaoRGF"                            );
$obTxtObservacaoRGF->setName   ( "stObservacaoRGF"                            );
$obTxtObservacaoRGF->setValue  ( ""                                           );
$obTxtObservacaoRGF->setRotulo ( "Observação"                                 );
$obTxtObservacaoRGF->setTitle  ( "Informe uma breve observação da publicação.");
$obTxtObservacaoRGF->setObrigatorioBarra( false                               );
$obTxtObservacaoRGF->setRows   ( 2                                            );
$obTxtObservacaoRGF->setCols   ( 100                                          );
$obTxtObservacaoRGF->setMaxCaracteres( 80                                     );

//Define Objeto Button para Incluir Veiculo da Publicação
$obBtnIncluirVeiculoRGF = new Button;
$obBtnIncluirVeiculoRGF->setValue             ( "Incluir" );
$obBtnIncluirVeiculoRGF->setId                ( "incluiVeiculoRGF" );
$obBtnIncluirVeiculoRGF->obEvento->setOnClick ( "montaParametrosGET('incluirListaVeiculosRGF', 'id, inVeiculoRGF, stVeiculo, dtDataPublicacaoRGF, inNumPublicacaoRGF, stNomCgmVeiculoPublicadadeRGF, stObservacaoRGF');" );

//Define Objeto Button para Limpar Veiculo da Publicação
$obBtnLimparVeiculoRGF = new Button;
$obBtnLimparVeiculoRGF->setValue             ( "Limpar" );
$obBtnLimparVeiculoRGF->obEvento->setOnClick ( "montaParametrosGET('limparVeiculoRGF', 'id, inVeiculoRGF, stVeiculo, dtDataPublicacaoRGF, inNumPublicacaoRGF, stNomCgmVeiculoPublicadadeRGF, stObservacaoRGF');" );

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaVeiculoRGF = new Span;
$obSpnListaVeiculoRGF->setID("spnListaVeiculosRGF");

//FIM Painel veiculos de publicidade RGF *******************************

//Painel veiculos de publicidade RREO
$obVeiculoPublicidadeRREO = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicidadeRREO->setTabelaVinculo       ( 'licitacao.veiculos_publicidade'    );
$obVeiculoPublicidadeRREO->setCampoVinculo        ( 'numcgm'                            );
$obVeiculoPublicidadeRREO->setNomeVinculo         ( 'Veículo de Publicação RREO'        );
$obVeiculoPublicidadeRREO->setRotulo              ( '**Veículo de Publicação RREO'      );
$obVeiculoPublicidadeRREO->setTitle               ( 'Informe o Veículo de Publicidade.' );
$obVeiculoPublicidadeRREO->setName                ( 'stNomCgmVeiculoPublicadadeRREO'    );
$obVeiculoPublicidadeRREO->setId                  ( 'stNomCgmVeiculoPublicadadeRREO'    );
$obVeiculoPublicidadeRREO->obCampoCod->setName    ( 'inVeiculoRREO'                     );
$obVeiculoPublicidadeRREO->obCampoCod->setId      ( 'inVeiculoRREO'                     );
$obVeiculoPublicidadeRREO->setNull                ( true );
$obVeiculoPublicidadeRREO->obCampoCod->setNull    ( true );

$obDataPublicacaoRREO = new Data();
$obDataPublicacaoRREO->setId     ( "dtDataPublicacaoRREO"          );
$obDataPublicacaoRREO->setName   ( "dtDataPublicacaoRREO"          );
$obDataPublicacaoRREO->setValue  ( date('d/m/Y')                   );
$obDataPublicacaoRREO->setRotulo ( "Data de Publicação"            );
$obDataPublicacaoRREO->setObrigatorioBarra( true                   );
$obDataPublicacaoRREO->setTitle  ( "Informe a data de publicação." );

$obNumeroPublicacaoRREO = new Inteiro();
$obNumeroPublicacaoRREO->setId     ( "inNumPublicacaoRREO"             );
$obNumeroPublicacaoRREO->setName   ( "inNumPublicacaoRREO"             );
$obNumeroPublicacaoRREO->setValue  ( ""                                );
$obNumeroPublicacaoRREO->setRotulo ( "Número Publicação"               );
$obNumeroPublicacaoRREO->setObrigatorioBarra( false                    );
$obNumeroPublicacaoRREO->setTitle  ( "Informe o Número da Publicação." );

//Campo Observação da Publicação
$obTxtObservacaoRREO = new TextArea;
$obTxtObservacaoRREO->setId     ( "stObservacaoRREO"                           );
$obTxtObservacaoRREO->setName   ( "stObservacaoRREO"                           );
$obTxtObservacaoRREO->setValue  ( ""                                           );
$obTxtObservacaoRREO->setRotulo ( "Observação"                                 );
$obTxtObservacaoRREO->setTitle  ( "Informe uma breve observação da publicação.");
$obTxtObservacaoRREO->setObrigatorioBarra( false                               );
$obTxtObservacaoRREO->setRows   ( 2                                            );
$obTxtObservacaoRREO->setCols   ( 100                                          );
$obTxtObservacaoRREO->setMaxCaracteres( 80                                     );

//Define Objeto Button para Incluir Veiculo da Publicação
$obBtnIncluirVeiculoRREO = new Button;
$obBtnIncluirVeiculoRREO->setValue             ( "Incluir" );  
$obBtnIncluirVeiculoRREO->setId                ( "incluiVeiculoRREO" );
$obBtnIncluirVeiculoRREO->obEvento->setOnClick ( "montaParametrosGET('incluirListaVeiculosRREO', 'id, inVeiculoRREO, stVeiculoRREO, dtDataPublicacaoRREO, inNumPublicacaoRREO, stNomCgmVeiculoPublicadadeRREO, stObservacaoRREO');" );

//Define Objeto Button para Limpar Veiculo da Publicação
$obBtnLimparVeiculoRREO = new Button;
$obBtnLimparVeiculoRREO->setValue             ( "Limpar" );
$obBtnLimparVeiculoRREO->obEvento->setOnClick ( "montaParametrosGET('limparVeiculoRREO', 'id, inVeiculoRREO, stVeiculoRREO, dtDataPublicacaoRREO, inNumPublicacaoRREO, stNomCgmVeiculoPublicadadeRREO, stObservacaoRREO');" );

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaVeiculoRREO = new Span;
$obSpnListaVeiculoRREO->setID("spnListaVeiculosRREO");

//recupera os veiculos de publicacao de RGF, coloca na sessao e manda para o oculto
$obTTCEALPublicacaoRREO = new TTCEALPublicacaoRREO();
$obTTCEALPublicacaoRREO->setDado('exercicio', Sessao::getExercicio());
$obTTCEALPublicacaoRREO->setDado('cod_entidade',$request->get('inCodEntidade'));
$obTTCEALPublicacaoRREO->recuperaVeiculosPublicacao( $rsVeiculosPublicacaoRREO,'','',$boTransacao );

$inCount = 0;
$arValoresRREO = array();

while ( !$rsVeiculosPublicacaoRREO->eof() ) {
    $arValoresRREO[$inCount]['id'            ]   = $inCount + 1;
    $arValoresRREO[$inCount]['inVeiculo'     ]   = $rsVeiculosPublicacaoRREO->getCampo( 'num_veiculo' );
    $arValoresRREO[$inCount]['stVeiculo'     ]   = $rsVeiculosPublicacaoRREO->getCampo( 'nom_veiculo' );
    $arValoresRREO[$inCount]['dtDataPublicacao'] = $rsVeiculosPublicacaoRREO->getCampo( 'dt_publicacao' );
    $arValoresRREO[$inCount]['inNumPublicacao' ] = $rsVeiculosPublicacaoRREO->getCampo( 'num_publicacao' );
    $arValoresRREO[$inCount]['stObservacao'    ] = $rsVeiculosPublicacaoRREO->getCampo( 'observacao' );
    $inCount++;
    $rsVeiculosPublicacaoRREO->proximo();
}
Sessao::write('arValoresRREO', $arValoresRREO);

$jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaVeiculosRGF'); \n";
$jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaVeiculosRREO'); \n";

$obFormulario = new Formulario();
$obFormulario->addForm        ( $obForm );
$obFormulario->addHidden      ( $obHdnCtrl );
$obFormulario->addComponente  ( $obLblEntidade );
$obFormulario->addHidden      ( $obHdnCodVeiculo );
$obFormulario->addHidden      ( $obHdnCodEntidade );

$obFormulario->addTitulo      ( 'Veículo de Publicação RGF' );
$obFormulario->addComponente  ( $obVeiculoPublicidadeRGF );
$obFormulario->addComponente  ( $obDataPublicacaoRGF );
$obFormulario->addComponente  ( $obNumeroPublicacaoRGF );
$obFormulario->addComponente  ( $obTxtObservacaoRGF );
$obFormulario->defineBarra    ( array( $obBtnIncluirVeiculoRGF, $obBtnLimparVeiculoRGF ) );
$obFormulario->addSpan        ( $obSpnListaVeiculoRGF );

$obFormulario->addTitulo      ( 'Veículo de Publicação RREO' );
$obFormulario->addComponente  ( $obVeiculoPublicidadeRREO );
$obFormulario->addComponente  ( $obDataPublicacaoRREO );
$obFormulario->addComponente  ( $obNumeroPublicacaoRREO );
$obFormulario->addComponente  ( $obTxtObservacaoRREO );
$obFormulario->defineBarra    ( array( $obBtnIncluirVeiculoRREO, $obBtnLimparVeiculoRREO ) );
$obFormulario->addSpan        ( $obSpnListaVeiculoRREO );

$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

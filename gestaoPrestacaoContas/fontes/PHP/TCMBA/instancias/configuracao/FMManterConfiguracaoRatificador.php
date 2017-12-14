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
* Formulário de Configuração de Ratificador TCM-BA
* Data de Criação: 11/08/2015

* @author Analista: Ane Caroline Fiegenbaum Pereira
* @author Desenvolvedor: Jean Silva 

$Id: FMManterConfiguracaoRatificador.php 63383 2015-08-24 12:34:24Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAConfiguracaoRatificador.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoResponsavel.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoRatificador";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once $pgJS;
include_once $pgOcul;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'manter');

$arEntidade = explode('-',$_REQUEST['inCodEntidade']);
$stUnidadeOrcamentaria = $request->get('stUnidadeOrcamentaria');

$arUnidadeOrcamentaria = explode('.',$stUnidadeOrcamentaria);

$obTTCMBAConfiguracaoRatificador = new TTCMBAConfiguracaoRatificador();
$obTTCMBAConfiguracaoRatificador->setDado('cod_entidade', $arEntidade[0]            );
$obTTCMBAConfiguracaoRatificador->setDado('exercicio'   , Sessao::getExercicio()    );
$obTTCMBAConfiguracaoRatificador->setDado('num_orgao'   , $arUnidadeOrcamentaria[0] );
$obTTCMBAConfiguracaoRatificador->setDado('num_unidade' , $arUnidadeOrcamentaria[1] );
$obTTCMBAConfiguracaoRatificador->recuperaPorChave($rsRecordRatificador, $boTransacao);
 
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

$arRatificadores = array();
$arRatificadores = $rsRecordRatificador->getElementos();

$inCount = 0;

foreach ( $arRatificadores as $ratificador ) {
    $ratificador["nom_cgm"] = SistemaLegado::pegaDado('nom_cgm','sw_cgm','WHERE numcgm ='.$ratificador['cgm_ratificador']);
    $ratificador["tipo_responsavel_desc"] = SistemaLegado::pegaDado('descricao','tcmba.tipo_responsavel','WHERE cod_tipo_responsavel ='.$ratificador['cod_tipo_responsavel']);
    $ratificador["inId"] = $inCount;
    $arrRatificadores[$inCount] = $ratificador;
    $inCount++;
}

Sessao::write("arRatificadores", $arrRatificadores);

$obForm = new Form;
$obForm->setAction( $pgProc     );
$obForm->setTarget( 'oculto'    );
    
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"  );
$obHdnAcao->setValue( $stAcao   );

//Define o objeto da ação stAcao
$obHdnStAcao = new Hidden;
$obHdnStAcao->setName ( "stHdnAcao" );
$obHdnStAcao->setId   ( "stHdnAcao" );
$obHdnStAcao->setValue( $stAcao );

//Define o objeto da ação stAcao
$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "stModulo"  );
$obHdnModulo->setValue( $stModulo   );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "" );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ( "hdnCodEntidade"  );
$obHdnEntidade->setValue( $arEntidade[0]    );

$obLblEntidade = new Label;
$obLblEntidade->setName   ( "stEntidade"                    );
$obLblEntidade->setValue  ( $request->get('inCodEntidade')  );
$obLblEntidade->setRotulo ( 'Entidade'                      );

$obHdnInId = new Hidden;
$obHdnInId->setName( "hdnInId" );
$obHdnInId->setId  ( "hdnInId" );

$obLblOrgao = new Label;
$obLblOrgao->setName   ( "stCodOrgao"                   );
$obLblOrgao->setValue  ( $num_orgao.' - '.$nom_orgao    );
$obLblOrgao->setRotulo ( 'Órgão'                        );

$obLblUnidade = new Label;
$obLblUnidade->setName   ( "stCodUnidade"                   );
$obLblUnidade->setValue  ( $num_unidade.' - '.$nom_unidade  );
$obLblUnidade->setRotulo ( 'Unidade'                        );

$obCgmRatificador =  new IPopUpCGMVinculado($obForm);
$obCgmRatificador->setTabelaVinculo     ( "sw_cgm_pessoa_fisica"            );
$obCgmRatificador->setCampoVinculo      ( "numcgm"                          );
$obCgmRatificador->setNomeVinculo       ( "Ratificador"                     );
$obCgmRatificador->setRotulo            ( "*Ratificador"                    );
$obCgmRatificador->setTitle             ( "Selecione o CGM do Ratificador"  );
$obCgmRatificador->setNull              ( true                              );
$obCgmRatificador->setName              ( "stNomCgmRatificador"             );
$obCgmRatificador->setId                ( "stNomCgmRatificador"             );
$obCgmRatificador->obCampoCod->setName  ( "inCgmRatificador"                );
$obCgmRatificador->obCampoCod->setId    ( "inCgmRatificador"                );
$obCgmRatificador->obCampoCod->setNull  ( true                              );
$obCgmRatificador->setTipo              ( 'fisica'                          );

// Campo de Data Inicial
$obDataInicial = new Data();
$obDataInicial->setId           ( 'dtDataInicio'        );
$obDataInicial->setName         ( 'dtDataInicio'        );
$obDataInicial->setRotulo       ( '*Início da Vigência' );
$obDataInicial->setNullBarra    ( false                 );

// Campo de Data Final
$obDataFinal = new Data();
$obDataFinal->setId         ( 'dtDataFim'           );
$obDataFinal->setName       ( 'dtDataFim'           );
$obDataFinal->setRotulo     ( '*Fim da Vigência'    );
$obDataFinal->setNullBarra  ( false                 );

$obBtnIncluirRatificador = new Button;
$obBtnIncluirRatificador->setName               ( "btIncluirRatificador"                                            );
$obBtnIncluirRatificador->setId                 ( "btIncluirRatificador"                                            );
$obBtnIncluirRatificador->setValue              ( "Incluir"                                                         );
$obBtnIncluirRatificador->obEvento->setOnClick  ( "buscaValor('incluirRatificador');"                               );
$obBtnIncluirRatificador->setTitle              ( "Clique para incluir um ratificador na lista de Ratificadores"    );

$obTTCMBATipoResponsavel = new TTCMBATipoResponsavel();
$obTTCMBATipoResponsavel->recuperaTodos($rsTipoResponsavel);

if ($rsTipoResponsavel->getNumLinhas() < 1) {
    $obErro = new Erro();
    $obErro->setDescricao("Não existe dados para o tipo de responsável!");
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

/* Combo para Selecionar os Tipos de responsavel Ratificador */
$obCmbTipoResponsavel = new Select;
$obCmbTipoResponsavel->setName      ( 'tipo_responsavel'                    );
$obCmbTipoResponsavel->setId        ( 'tipo_responsavel'                    );
$obCmbTipoResponsavel->setRotulo    ( '*Tipo de Responsável'                );
$obCmbTipoResponsavel->setStyle     ( "width: 400px"                        );
$obCmbTipoResponsavel->setNull      ( true                                  );
$obCmbTipoResponsavel->setCampoId   ( '[cod_tipo_responsavel]-[descricao]'  );
$obCmbTipoResponsavel->setCampoDesc ( '[descricao]'                         );
$obCmbTipoResponsavel->addOption    ( "", "Selecione"                       );
$obCmbTipoResponsavel->setValue     ( "[cod_tipo_responsavel]"              );
$obCmbTipoResponsavel->preencheCombo( $rsTipoResponsavel                    );

$obSpnCGMsRatificador = new Span();
$obSpnCGMsRatificador->setId("spnCGMsRatificadores");

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
$obFormulario->addTitulo('Configuração de Ratificador');
$obFormulario->addComponente($obCgmRatificador);
$obFormulario->addComponente($obDataInicial);
$obFormulario->addComponente($obDataFinal);
$obFormulario->addComponente($obCmbTipoResponsavel);
$obFormulario->addComponente($obBtnIncluirRatificador);
$obFormulario->addSpan($obSpnCGMsRatificador);

$obFormulario->Cancelar($pgFilt);
$obFormulario->show();

processarForm(true,"Form",$stAcao);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
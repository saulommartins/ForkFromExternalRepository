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
 * Página de Filtro para Consulta de Imóveis
 * Data de Criação   : 09/06/2005

 * @author Analista: Fabio Bertoldi
 * @author Desenvolvedor: Marcelo Boezzio Paulino

 * @ignore

 * $Id: FLConsultaImovel.php 63503 2015-09-03 18:25:17Z jean $

 * Casos de uso: uc-05.01.18
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";
include_once CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgOculCons = "";

include_once $pgJS;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "consultar";
}

$arTransf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

Sessao::write('paginando', false);
Sessao::write('sessao_transf4', $arTransf4);
Sessao::remove('link');
Sessao::remove('stLink');

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();
$stMascaraLote      = $obRCIMConfiguracao->getMascaraLote();

//****************************************/
//atributos dinamicos
$obRRegra = new RCadastroDinamico;
$arAtributosSelecionados = array(); //lista com dados selecionados

//lote urbano
$obRRegra->setCodCadastro('2');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoLoteUrbano );
$arDadosSelecionados = $obRCIMConfiguracao->arAtbLoteUrbano; //configuracao com dados selecionados
$arDados = $rsAtributoLoteUrbano->getElementos(); //todos elementos
for ( $inX=0; $inX<count( $arDados ); $inX++ ) {
    for ( $inY=0; $inY<count( $arDadosSelecionados ); $inY++ ) {
        if ($arDados[$inX]["cod_atributo"] == $arDadosSelecionados[$inY]) {
            $arAtributosSelecionados[] = $arDados[$inX];
            break;
        }
    }
}

//lote rural
$obRRegra->setCodCadastro('3');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoLoteRural );
$arDadosSelecionados = $obRCIMConfiguracao->arAtbLoteRural; //configuracao com dados selecionados
$arDados = $rsAtributoLoteRural->getElementos(); //todos elementos
for ( $inX=0; $inX<count( $arDados ); $inX++ ) {
    for ( $inY=0; $inY<count( $arDadosSelecionados ); $inY++ ) {
        if ($arDados[$inX]["cod_atributo"] == $arDadosSelecionados[$inY]) {
            $arAtributosSelecionados[] = $arDados[$inX];
            break;
        }
    }
}

//imovel
$obRRegra->setCodCadastro('4');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoImovel );
$arDadosSelecionados = $obRCIMConfiguracao->arAtbImovel; //configuracao com dados selecionados
$arDados = $rsAtributoImovel->getElementos(); //todos elementos
for ( $inX=0; $inX<count( $arDados ); $inX++ ) {
    for ( $inY=0; $inY<count( $arDadosSelecionados ); $inY++ ) {
        if ($arDados[$inX]["cod_atributo"] == $arDadosSelecionados[$inY]) {
            $arAtributosSelecionados[] = $arDados[$inX];
            break;
        }
    }
}

//Edificacao
$obRRegra->setCodCadastro('5');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoEdificacao );
$arDadosSelecionados = $obRCIMConfiguracao->arAtbEdificacao; //configuracao com dados selecionados
$arDados = $rsAtributoEdificacao->getElementos(); //todos elementos
for ( $inX=0; $inX<count( $arDados ); $inX++ ) {
    $boEncontrou = false;
    for ( $inY=0; $inY<count( $arDadosSelecionados ); $inY++ ) {
        if ($arDados[$inX]["cod_atributo"] == $arDadosSelecionados[$inY]) {
            $arAtributosSelecionados[] = $arDados[$inX];
            $boEncontrou = true;
            break;
        }
    }
}

for ( $inX=0; $inX<count( $arAtributosSelecionados ); $inX++ ) {
    $arAtributosSelecionados[$inX]["nao_nulo"] = true;
}

$rsAtributos = new RecordSet;
$rsAtributos->preenche( $arAtributosSelecionados );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//fim atributos dinamicos

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $request->get('stCtrl') );

$obHdnCodigoUF = new Hidden;
$obHdnCodigoUF->setName ( "inCodigoUF" );

$obHdnCodigoMunicipio = new Hidden;
$obHdnCodigoMunicipio->setName( "inCodigoMunicipio" );

$obHdnNomeLogradouro = new Hidden;
$obHdnNomeLogradouro->setName( "stNomeLogradouro" );

$obHdnstCampo = new Hidden;
$obHdnstCampo->setName( 'stCampo');
$obHdnstCampo->setValue($request->get('stCampo'));

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName('campoNom');
$obHdnCampoNom->setValue($request->get('campoNom'));

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( 'campoNum' );
$obHdnCampoNum->setValue($request->get('campoNum'));

$obBscInscricaoMunicipal = new BuscaInner;
$obBscInscricaoMunicipal->setNull                  ( true                         );
$obBscInscricaoMunicipal->setRotulo                ( "Inscrição Imobiliária"      );
$obBscInscricaoMunicipal->obCampoCod->setName      ( "inInscricaoImobiliaria"     );
$obBscInscricaoMunicipal->obCampoCod->setSize      ( strlen($stMascaraInscricao)+1);
$obBscInscricaoMunicipal->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)  );
$obBscInscricaoMunicipal->obCampoCod->setInteiro   ( false                        );
$obBscInscricaoMunicipal->setFuncaoBusca( "abrePopUp( '".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php', 'frm', 'inInscricaoImobiliaria', 'stCampo', 'todos', '".Sessao::getId()."', '800', '550' );" );

$obBscLote = new BuscaInner;
$obBscLote->setRotulo               ( "Lote"     );
$obBscLote->setNull                 ( true       );
$obBscLote->setId                   ( "innerLote" );
$obBscLote->setName                 ( "innerLote" );
$obBscLote->obCampoCod->setName     ( "inCodLote" );
$obBscLote->obCampoCod->setId       ( "inCodLote" );
$obBscLote->obCampoCod->setValue    ( $request->get('inNumLoteamento') );
$obBscLote->obCampoCod->setMaxLength( strlen( $stMascaraLote )   );
$obBscLote->obCampoCod->setSize     ( strlen( $stMascaraLote )+1 );
$obBscLote->obCampoCod->setInteiro  ( false );
$obBscLote->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);" );
$obBscLote->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."lote/FLBuscaLote.php','frm','inCodLote','innerLote','juridica','".Sessao::getId()."','800','550')" );

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );
$obMontaLocalizacao->setPopUp              ( true );
$obMontaLocalizacao->setObrigatorio        ( false );

$obBscCondominio = new BuscaInner;
$obBscCondominio->setRotulo           ( "Condomínio"         );
$obBscCondominio->setNull             ( true                 );
$obBscCondominio->setId               ( "innerCondominio"    );
$obBscCondominio->obCampoCod->setName ( "inCodCondominio"    );
$obBscCondominio->obCampoCod->setValue( $request->get('inCodigoCondominio')  );
$obBscCondominio->obCampoCod->obEvento->setOnChange( "buscaValor('buscaCondominio');" );
$obBscCondominio->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."condominio/FLProcurarCondominio.php','frm','inCodCondominio' ,'innerCondominio','','".Sessao::getId()."','800','550')" );

$obBscLogradouro = new BuscaInner;
$obBscLogradouro->setRotulo          ( "Logradouro"      );
$obBscLogradouro->setNull            ( true              );
$obBscLogradouro->setId              ( "campoInnerLogr" );
$obBscLogradouro->obCampoCod->setName( "inNumLogradouro" );
$obBscLogradouro->obCampoCod->obEvento->setOnChange( "buscaValor('buscaLogradouro');" );
$obBscLogradouro->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro','campoInnerLogr','' ,'".Sessao::getId()."','800','550')" );

$obTxtNumero = new TextBox;
$obTxtNumero->setNull      ( true                   );
$obTxtNumero->setName      ( "inNumero"             );
$obTxtNumero->setRotulo    ( "Número / Complemento" );
$obTxtNumero->setInteiro   ( true                   );
$obTxtNumero->setsize      ( 10                     );
$obTxtNumero->setMaxLength ( 10                     );

$obLblEndereco = new Label;
$obLblEndereco->setValue( " - " );

$obTxtComplemento = new TextBox;
$obTxtComplemento->setNull     ( true                   );
$obTxtComplemento->setName     ( "stComplemento"        );
$obTxtComplemento->setRotulo   ( "Número / Complemento" );
$obTxtComplemento->setSize     ( 80                     );
$obTxtComplemento->setMaxLength( 160                    );

$obBscBairro = new BuscaInner;
$obBscBairro->setRotulo          ( "Bairro"      );
$obBscBairro->setId              ( "innerBairro" );
$obBscBairro->obCampoCod->setName( "inCodBairro" );
$obBscBairro->setNull            ( true          );
$obBscBairro->obCampoCod->obEvento->setOnChange( "buscaValor('buscaBairro');" );
$obBscBairro->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."bairroSistema/FLProcurarBairro.php','frm','inCodBairro','innerBairro','','".Sessao::getId()."','800','550')" );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo           ( "Proprietário" );
$obBscCGM->setId               ( "innerCGM"     );
$obBscCGM->obCampoCod->setName ("inNumCGM"      );
$obBscCGM->obCampoCod->setValue( $request->get('inNumCGM') );
$obBscCGM->setNull             ( true           );
$obBscCGM->obCampoCod->obEvento->setOnChange( "buscaValor('buscaCGM');" );
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','innerCGM','todos','".Sessao::getId()."','800','550');" );

$obBscCreci = new BuscaInner;
$obBscCreci->setRotulo                ( "CRECI"       );
$obBscCreci->setNull                  ( true          );
$obBscCreci->setId                    ( "innerCreci"  );
$obBscCreci->obCampoCod->setName      ( "stCreci"     );
$obBscCreci->obCampoCod->setInteiro   ( false         );
$obBscCreci->obCampoCod->setSize      ( 10            );
$obBscCreci->obCampoCod->setMaxLength ( 10            );
$obBscCreci->obCampoCod->obEvento->setOnChange( "buscaValor('buscaCreci');" );
$obBscCreci->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."corretagem/FLProcurarCorretagem.php','frm','stCreci','innerCreci','todos','".Sessao::getId()."','800','550')" );

// combo de ordenação
$obCmbOrder = new Select;
$obCmbOrder->setName      ( "stOrder"                 );
$obCmbOrder->setRotulo    ( "Ordenação"               );
$obCmbOrder->setTitle     ( "Escolha a ordenação da consulta" );
$obCmbOrder->addOption    ( "inscricao_municipal"  , "Inscrição Imobiliária" );
$obCmbOrder->addOption    ( "localizacao", "Localização" );
$obCmbOrder->addOption    ( "endereco"  , "Endereço"     );
$obCmbOrder->setCampoDesc ( "stOrder"        );
$obCmbOrder->setNull      ( false            );
$obCmbOrder->setStyle     ( "width: 200px"   );

$obBtnOk     = new Ok;
$obBtnLimpar = new Button;
$obBtnLimpar->setName             ( "btnLimparFiltro" );
$obBtnLimpar->setValue            ( "Limpar"          );
$obBtnLimpar->obEvento->setOnClick( "limparFiltro();" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.18" );
$obFormulario->addHidden( $obHdnAcao            );
$obFormulario->addHidden( $obHdnCtrl            );
$obFormulario->addHidden( $obHdnCodigoUF        );
$obFormulario->addHidden( $obHdnCodigoMunicipio );
$obFormulario->addHidden( $obHdnNomeLogradouro  );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnstCampo );

$obFormulario->addTitulo( "Dados para filtro" );

$obFormulario->addComponente( $obBscInscricaoMunicipal );
$obFormulario->addComponente( $obBscLote               );
$obMontaLocalizacao->geraFormulario( $obFormulario     );
$obFormulario->addComponente( $obBscLogradouro         );
$obFormulario->agrupaComponentes( array( $obTxtNumero, $obLblEndereco ,$obTxtComplemento ) );
$obFormulario->addComponente( $obBscCondominio         );
$obFormulario->addComponente( $obBscBairro             );
$obFormulario->addComponente( $obBscCGM                );
$obFormulario->addComponente( $obBscCreci              );
$obFormulario->addComponente( $obCmbOrder              );
$obMontaAtributos->geraFormulario  ( $obFormulario );

$obFormulario->ok();
$obFormulario->show();

$jsOnload = " jq('#innerLote').hide(); \n";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

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
//ini_set("display_errors",1);
//error_reporting(E_ALL ^ E_NOTICE);
/**
    * Página de Formulário para informar os veiculos de publicacao que serao publicados no edital
    * Data de Criação   : 06/10/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Fábio Moreira da Silva

    * @ignore

    * $Id: FMManterPublicacaoEdital.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.05.17
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include de componente
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

include_once( CAM_GP_LIC_COMPONENTES."IPopUpNumeroEdital.class.php" );
//Definições padrões do framework sobre nomes de arquivo
$stPrograma = "ManterPublicacaoEdital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stOnload = "montaParametrosGET('exibeEdital')";

$stAcao = $request->get('stAcao');

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

Sessao::remove('trans6');

/*
 * Definição dos componentes(objetos) que irão ser adicionados no formulário
 */
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

Sessao::write('componente', $obForm);

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

// inicio da alteração devido a inclusão do filtro para acessar esta tela
$arEdital = explode('/',$_REQUEST['inNumEdital']);
$arEdital[1] = ($arEdital[1] == '') ? Sessao::getExercicio() : $arEdital[1];

include_once(TLIC."TLicitacaoEdital.class.php");
include_once(CAM_GP_COM_COMPONENTES."ILabelEditObjeto.class.php");

//O usuario digitou algo, agora busca no BD
$obj = new TLicitacaoEdital;
$obj->setDado("num_edital",$arEdital[0]);
$obj->setDado('exercicio',$arEdital[1]);
$obj->recuperaLicitacao($rs);

$obHdnEdital = new Hidden();
$obHdnEdital->setName( 'numEdital' );
$obHdnEdital->setValue( $arEdital[0] );

$labelNumEdital = new Label();
$labelNumEdital->setId( 'inNumEdital' );
$labelNumEdital->setValue( $arEdital[0].'/'.$arEdital[1] );
$labelNumEdital->setRotulo( 'Número do Edital' );

$labelNumLicitacao = new Label();
$labelNumLicitacao->setId( 'numLicitacao' );
$labelNumLicitacao->setValue($rs->getCampo('cod_licitacao').'/'.$rs->getCampo('exercicio'));
$labelNumLicitacao->setRotulo( 'Número da Licitação' );

$labelModalidade = new Label();
$labelModalidade->setId( 'numModalidade' );
$labelModalidade->setValue($rs->getCampo('cod_modalidade').' - '.$rs->getCampo('modalidade_descricao'));
$labelModalidade->setRotulo( 'Modalidade' );

$labelEntidade = new Label();
$labelEntidade->setId('numEntidade');
$labelEntidade->setValue($rs->getCampo('cod_entidade').' - '.$rs->getCampo('nom_cgm'));
$labelEntidade->setRotulo( 'Entidade' );

//Objeto do processo licitatorio
$labelObjetoProcLic = new ILabelEditObjeto();
$labelObjetoProcLic->setCodObjeto($rs->getCampo('cod_objeto'));
$labelObjetoProcLic->setName( 'objeto' );
$labelObjetoProcLic->setRotulo( 'Objeto' );

//label para a data de aprovação do jurídico;
$labelDataAprovacao = new Label();
$labelDataAprovacao->setRotulo( 'Data de Aprovação do Jurídico' );
$labelDataAprovacao->setValue( $rs->getCampo('dt_aprovacao_juridico') );

//Hidden para armazenar o exercicio do edital
$obExercicio = new Hidden;
$obExercicio->setName( "exercicioEdital" );
$obExercicio->setValue( $rs->getCampo('exercicio') );

//Hidden para armazenar a data de aprovação do edital
$obHdnDataEdital = new Hidden();
$obHdnDataEdital->setName( 'hdnDataEdital' );
$obHdnDataEdital->setValue( $rs->getCampo('dt_aprovacao_juridico') );

$obHdnId = new Hidden();
$obHdnId->setId( 'hdnId' );
$obHdnId->setName( 'hdnId' );
$obHdnId->setValue( '' );

/* Objetos de 'Veiculo de publicacao' */
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

Sessao::getExercicio();
$obVeiculoPublicacao = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicacao->setTabelaVinculo ( 'licitacao.veiculos_publicidade' );
$obVeiculoPublicacao->setCampoVinculo ( 'numcgm' );
$obVeiculoPublicacao->setNomeVinculo ( 'Veículo de Publicação' );
$obVeiculoPublicacao->setRotulo ( 'Veículo de Publicação' );
$obVeiculoPublicacao->setTitle ( 'Informe o CGM do veículo de publicação.' );
$obVeiculoPublicacao->setName ( 'nomeVeiculoPublicacao');
$obVeiculoPublicacao->setId ( 'nomeVeiculoPublicacao');
$obVeiculoPublicacao->obCampoCod->setName ( 'veiculoPublicacao' );
$obVeiculoPublicacao->obCampoCod->setId ( 'veiculoPublicacao' );
$obVeiculoPublicacao->obCampoCod->setNull ( true );
$obVeiculoPublicacao->setNull ( true );
$obVeiculoPublicacao->setObrigatorioBarra(true);

//data da publicação
$obDataPublicacao = new Data;
$obDataPublicacao->setName  ( "dataPublicacao" );
$obDataPublicacao->setRotulo( "Data da Publicação" );
$obDataPublicacao->setTitle ( "Informe a data de publicação do edital." );
$obDataPublicacao->setId("dataPublicacao");
$obDataPublicacao->setNull ( true );
$obDataPublicacao->setObrigatorioBarra(true);
//observacao
$obObservacao = new TextBox;
$obObservacao->setName  ( "observacao" );
$obObservacao->setRotulo( "Observação" );
$obObservacao->setTitle ( "Informe uma breve observação da publicação." );
$obObservacao->setId("observacao");
$obObservacao->setSize(80);
$obObservacao->setMaxLength(80);
$obObservacao->setNull ( true);

$obNumPublicacao = new TextBox;
$obNumPublicacao->setName  ( "inNumPublicacao" );
$obNumPublicacao->setRotulo( "Número da Publicação" );
$obNumPublicacao->setTitle ( "Informe o Número da Publicação." );
$obNumPublicacao->setId("inNumPublicacao");
$obNumPublicacao->setSize(9);
$obNumPublicacao->setMaxLength(9);
$obNumPublicacao->setNull ( true);
$obNumPublicacao->setInteiro ( true);

$obBtnIncluir = new Button;
$obBtnIncluir->setName ( "btIncluirVeiculoPublicacao" );
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->setTipo ( "button" );
$obBtnIncluir->setStyle( "width: 60px" );
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET( 'incluirVeiculoPublicacao', 'hdnDataEdital, veiculoPublicacao, nomeVeiculoPublicacao, dataPublicacao, observacao, inNumPublicacao' );" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName ( "btLimparVeiculoPublicacao" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo ( "button" );
$obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limparListaVeiculoPublicacao');" );

//  INICIALIZA A LISTA A PARTIR DO CONTEUDO DO BD
include_once(TLIC."TLicitacaoPublicacaoEdital.class.php");
$objMapeamento = new TLicitacaoPublicacaoEdital;
$objMapeamento->setDado("num_edital",$arEdital[0]);
$objMapeamento->setDado('exercicio',Sessao::getExercicio());
$objMapeamento->recuperaVeiculosPublicacao($rsVeiculos);
$i=0;

$arVeiculos = array();
while (!$rsVeiculos->eof()) {
    $arVeiculos[$i]['veiculoPublicacao'] = $rsVeiculos->getCampo('veiculopublicacao');
    $arVeiculos[$i]['nomeVeiculoPublicacao'] = $rsVeiculos->getCampo('veiculopublicacao')." - ".$rsVeiculos->getCampo('nomeveiculopublicacao');
    $arVeiculos[$i]['dataPublicacao'] = $rsVeiculos->getCampo('datapublicacao');
    $arVeiculos[$i]['observacao'] = $rsVeiculos->getCampo('observacao');
    $arVeiculos[$i]['inNumPublicacao'] = $rsVeiculos->getCampo('num_publicacao');
    $arVeiculos[$i]['id'] = $i;
    $rsVeiculos->proximo();
    $i++;
}

Sessao::write('arVeiculos', $arVeiculos);
$jsOnLoad = "montaParametrosGET('montaListaVeiculos')";

// Define objeto span para lista de veiculos de publicacao utilizados
$obSpnVeiculoPublicacao = new Span();
$obSpnVeiculoPublicacao->setId("spnVeiculoPublicacao");
// Define objeto span para lista de veiculos de publicacao utilizados
$obSpnListaVeiculos = new Span();
$obSpnListaVeiculos->setId("spnListaVeiculosPublicacao");

$obHdnNomeVeiculo = new Hidden();
$obHdnNomeVeiculo->setId('hdnNomeVeiculo');
$obHdnNomeVeiculo->setName('hdnNomeVeiculo');
$obHdnNomeVeiculo->setValue('');

/*
 * Define o formulário
 */
$obFormulario = new Formulario;
//carrega a lista por padrao
$obFormulario->addForm          ( $obForm                        );
$obFormulario->setAjuda         ("UC-03.05.17"                   );
$obFormulario->addHidden        ( $obHdnCtrl                     );
$obFormulario->addHidden        ( $obHdnAcao                     );
$obFormulario->addHidden        ( $obHdnNomeVeiculo              );

$obFormulario->addTitulo        ( "Dados da Publicação do Edital");

// INCLUIDO DEVIDO A INCLUSÃO DO FILTRO ANTECEDENDO ESTA TELA
$obFormulario->addHidden($obHdnDataEdital);
$obFormulario->addHidden($obHdnId);
$obFormulario->addHidden($obHdnEdital);
$obFormulario->addComponente( $labelNumEdital );
$obFormulario->addComponente($labelEntidade);
$obFormulario->addComponente($labelNumLicitacao);
$obFormulario->addComponente($labelModalidade);
$obFormulario->addComponente($labelObjetoProcLic);
$obFormulario->addComponente($labelDataAprovacao);
$obFormulario->addHidden($obExercicio);

$obFormulario->addTitulo        ( "Veículo de Publicação"        );
$obFormulario->addComponente    ( $obVeiculoPublicacao           );
$obFormulario->addComponente    ( $obDataPublicacao              );
$obFormulario->addComponente    ( $obObservacao                  );  
$obFormulario->addComponente    ( $obNumPublicacao               );  
$obFormulario->defineBarra( array ( $obBtnIncluir , $obBtnLimpar ),"","" );
$obFormulario->addTitulo        ( "Veículos de Publicação Utilizados"  );

$obFormulario->addSpan          ( $obSpnListaVeiculos 			 );
$obFormulario->Cancelar( "LSManterEdital.php?".Sessao::getId()."&stAcao=".$stAcao.$stFiltro );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

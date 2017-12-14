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
    * Página de Formulário para informar os veiculos de publicacao que serao publicados na compra direta
    * Data de Criação   : 03/08/2015

    * @author Analista: Gelson Goncalves
    * @author Desenvolvedor: Lisiane Morais

    * @ignore
    * Casos de uso : uc-03.05.17
     $Id: FMManterPublicacaoCompraDireta.php 63412 2015-08-25 20:06:50Z lisiane $
     $Rev: 63412 $
     $Author: lisiane $
     $Date: 2015-08-25 17:06:50 -0300 (Tue, 25 Aug 2015) $
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include de componente
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once(  TCOM."TComprasPublicacaoCompraDireta.class.php" );

//Definições padrões do framework sobre nomes de arquivo
$stPrograma = "ManterPublicacaoCompraDireta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

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

$obHdnCompraDireta = new Hidden;
$obHdnCompraDireta->setName( "inCodCompraDireta" );
$obHdnCompraDireta->setValue( $request->get('inCodCompraDireta') );

$obHdnModalidade = new Hidden;
$obHdnModalidade->setName( "inCodModalidade" );
$obHdnModalidade->setValue( $request->get('inCodModalidade') );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName( "inCodEntidade" );
$obHdnEntidade->setValue( $request->get('inCodEntidade') );

$labelNumCompraDireta = new Label();
$labelNumCompraDireta->setId( 'inCodCompraDireta' );
$labelNumCompraDireta->setValue($request->get('inCodCompraDireta'));
$labelNumCompraDireta->setRotulo( 'Número da Compra Direta' );

$labelModalidade = new Label();
$labelModalidade->setId( 'numModalidade' );
$labelModalidade->setValue($request->get('inCodModalidade').' - '.$request->get('stModalidadeDescricao'));
$labelModalidade->setRotulo( 'Modalidade' );

$labelEntidade = new Label();
$labelEntidade->setId('numEntidade');
$labelEntidade->setValue($request->get('inCodEntidade').' - '.$request->get('stNomeEntidade'));
$labelEntidade->setRotulo( 'Entidade' );

//Hidden para armazenar o exercicio do edital
$obExercicio = new Hidden;
$obExercicio->setName( "entidade_exercicio" );
$obExercicio->setValue( $request->get('entidade_exercicio') );

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
$obDataPublicacao->setTitle ( "Informe a data de publicação da Compra Direta." );
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
$objMapeamento = new TComprasPublicacaoCompraDireta;
$objMapeamento->setDado('cod_compra_direta', $request->get('inCodCompraDireta'));
$objMapeamento->setDado('exercicio_entidade',Sessao::getExercicio());
$objMapeamento->recuperaVeiculosPublicacao($rsVeiculos,'','',$boTransacao);
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
$obFormulario->addHidden        ( $obHdnCompraDireta             );
$obFormulario->addHidden        ( $obHdnModalidade               );
$obFormulario->addHidden        ( $obHdnEntidade                 );

$obFormulario->addTitulo        ( "Dados da Publicação do Edital");
// INCLUIDO DEVIDO A INCLUSÃO DO FILTRO ANTECEDENDO ESTA TELA
$obFormulario->addHidden($obHdnId);
$obFormulario->addComponente($labelEntidade);
$obFormulario->addComponente($labelNumCompraDireta);
$obFormulario->addComponente($labelModalidade);
$obFormulario->addHidden($obExercicio);

$obFormulario->addTitulo        ( "Veículo de Publicação"        );
$obFormulario->addComponente    ( $obVeiculoPublicacao           );
$obFormulario->addComponente    ( $obDataPublicacao              );
$obFormulario->addComponente    ( $obObservacao                  );  
$obFormulario->addComponente    ( $obNumPublicacao               );  
$obFormulario->defineBarra( array ( $obBtnIncluir , $obBtnLimpar ),"","" );
$obFormulario->addTitulo        ( "Veículos de Publicação Utilizados"  );

$obFormulario->addSpan          ( $obSpnListaVeiculos 			 );
$obFormulario->Cancelar( "LSManterCompraDireta.php?".Sessao::getId()."&stAcao=".$stAcao.$stFiltro );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

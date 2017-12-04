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
    * Página Oculta de Processar Implantacao
    * Data de Criação   : 08/06/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * Casos de uso: uc-03.03.17

    $Id: OCMovimentacaoDiversa.php 65646 2016-06-07 14:09:45Z evandro $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoCatalogoItemBarras.class.php" );
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoItem.class.php");
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoClassificacao.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBem.class.php' );
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php");

$stCtrl = $_REQUEST['stCtrl'];

function listaItens($arRecordSet)
{
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecordSet );
    $rsRecordSet->setPrimeiroElemento();

    $rsRecordSet->addFormatacao('stNomMarca', 'HTML');
    $rsRecordSet->addFormatacao('stNomUnidade', 'HTML');
    $rsRecordSet->addFormatacao('stNomCentroCusto', 'HTML');
    $rsRecordSet->addFormatacao('stNomItem', 'HTML');

    if ( $rsRecordSet->getNumLinhas() != 0 ) {

        $obLista = new Lista;
        $obLista->setRecordSet( $rsRecordSet );

        $obLista->setMostraPaginacao( false );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Item" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Unidade de Medida" );
        $obLista->ultimoCabecalho->setWidth( 6 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Marca" );
        $obLista->ultimoCabecalho->setWidth( 7 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Centro de Custo" );
        $obLista->ultimoCabecalho->setWidth( 12 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Quantidade" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor total de Mercado" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[inCodItem]-[stNomItem]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "stNomUnidade" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[inCodMarca]-[stNomMarca]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[inCodCentroCusto]-[stNomCentroCusto]" );
        $obLista->commitDado();

        $obQuantidade = new Quantidade();
        $obQuantidade->setValue( "quantidade" );
        $obQuantidade->setNull ( false );
        $obQuantidade->setId   ( "quantidade" );
        $obQuantidade->setReadOnly( "[boPerecivel]" );
        $obQuantidade->setSize ( 10 );

        $obLista->addDadoComponente( $obQuantidade );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDadoComponente();

        $obValorTotal = new ValorTotal();
        $obValorTotal->setValue( "vtotal" );
        $obValorTotal->setNull ( false );
        $obValorTotal->setId   ( "inVlTotal");
        $obValorTotal->setSize ( 10 );

        $obLista->addDadoComponente( $obValorTotal);
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDadoComponente();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "javascript:alteraItem('alteraItens');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->addCampo("2","inCodItem");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "javascript:excluiItem('excluiItens');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();

        $html = $obLista->getHTML();

        $html = str_replace("\n","",$html);
        $html = str_replace("  ","",$html);
        $html = str_replace("'","\\'",$html);

        $stJs = "d.getElementById('spnItens').innerHTML = '".$html."';";

        for ($i=0;$i<$rsRecordSet->getNumLinhas();$i++) {
            if($rsRecordSet->getCampo('boPerecivel'))
                $stJs .= "d.getElementById('nuQuantidade_".($i+1)."').readonly = true;";
            $rsRecordSet->proximo();
        }
    }

    return $stJs;
}

function montaListaLotes($arRecordSet)
{
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecordSet );
    $rsRecordSet->addFormatacao('stNumLote', 'HTML');
    $rsRecordSet->addFormatacao('stDataFabricacao', 'HTML');
    $rsRecordSet->addFormatacao('stDataValidade', 'HTML');
    $rsRecordSet->addFormatacao('nmQuantidadeLote', 'HTML');
    if ( $rsRecordSet->getNumLinhas() != 0 ) {
        $obFormulario = new Formulario;

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );

        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Lote" );
        $obLista->ultimoCabecalho->setWidth( 7 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Data de Fabricação" );
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Data de Validade" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Quantidade" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "stNumLote" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "stDataFabricacao" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "stDataValidade" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "nmQuantidadeLote" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "javascript:excluiLote();" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();

        $html = $obLista->getHTML();

        $html = str_replace("\n","",$html);
        $html = str_replace("  ","",$html);
        $html = str_replace("'","\\'",$html);

        $stJs = "d.getElementById('spnListaLotes').innerHTML = '".$html."';";

        return $stJs;
    }
}

function montaCampoAlmoxarifado()
{
    include_once(CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifadoAlmoxarife.class.php");
    include_once(CAM_GP_ALM_COMPONENTES."ILabelAlmoxarifado.class.php");

    $obFormulario = new Formulario;

    $obAlmoxarifado = new ISelectAlmoxarifadoAlmoxarife($obForm);
    $obAlmoxarifado->setNull(false);
    $obAlmoxarifado->setTitle("Selecione o almoxarifado.");
    $obLblAlmoxarifado = new ILabelAlmoxarifado($obForm);

    $obHdnAlmoxarifado = new Hidden;
    $obHdnAlmoxarifado->setName ( 'inCodAlmoxarifado' );
    $obHdnAlmoxarifado->setValue( $_REQUEST['inCodAlmoxarifado'] );

    $obLblAlmoxarifado->setCodAlmoxarifado( $_REQUEST['inCodAlmoxarifado'] );

    if (!$_REQUEST['inCodAlmoxarifado']) {
        $obFormulario->addComponente( $obAlmoxarifado );
    } else {
        $obFormulario->addHidden    ( $obHdnAlmoxarifado );
        $obFormulario->addComponente( $obLblAlmoxarifado );
    }

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = " d.getElementById('spnAlmoxarifado').innerHTML = '". $stHtml. "';\n";
    $stJs .= " d.getElementById('stExercicio').innerHTML = '". Sessao::getExercicio(). "';\n";

    return $stJs;
}

function limparItens()
{
    $stJs .= " limpaFormulariomontaListaItens();                      \n ";

    return $stJs;
}

function montaFormLotes()
{
    $obFormulario = new Formulario;

    $obTxtNumeroLote= new TextBox();
    $obTxtNumeroLote->setName("stNumLote");
    $obTxtNumeroLote->setId("stNumLote");
    $obTxtNumeroLote->setObrigatorioBarra(true);
    $obTxtNumeroLote->setRotulo("Número do Lote");
    $obTxtNumeroLote->setTitle("Informe o número do lote.");

    $obTxtDataFabricacao = new Data();
    $obTxtDataFabricacao->setName("stDataFabricacao");
    $obTxtDataFabricacao->setId("stDataFabricacao");
    $obTxtDataFabricacao->setObrigatorioBarra(true);
    $obTxtDataFabricacao->setRotulo("Data de Fabricação");
    $obTxtDataFabricacao->setTitle("Informe a data de fabricação.");

    $obTxtDataValidade = new Data();
    $obTxtDataValidade->setName("stDataValidade");
    $obTxtDataValidade->setId("stDataValidade");
    $obTxtDataValidade->setObrigatorioBarra(true);
    $obTxtDataValidade->setRotulo("Data de Validade");
    $obTxtDataValidade->setTitle("Informe a data de validade.");

    $obTxtQuantidadeLote = new Quantidade;
    $obTxtQuantidadeLote->setRotulo ( "Quantidade" );
    $obTxtQuantidadeLote->setName("nmQuantidadeLote");
    $obTxtQuantidadeLote->setId("nmQuantidadeLote");
    $obTxtQuantidadeLote->setObrigatorioBarra(true);
    $obTxtQuantidadeLote->setTitle("Informe a quantidade do lote.");

    $obSpnListaLotes = new Span;
    $obSpnListaLotes->setId("spnListaLotes");

    $obFormulario->addTitulo    ( "Perecível"     );
    $obFormulario->addComponente( $obTxtNumeroLote );
    $obFormulario->addComponente( $obTxtDataFabricacao );
    $obFormulario->addComponente( $obTxtDataValidade );
    $obFormulario->addComponente( $obTxtQuantidadeLote );
    $obFormulario->Incluir        ('Lotes', array( $obTxtNumeroLote, $obTxtDataFabricacao, $obTxtDataValidade, $obTxtQuantidadeLote) );
    $obFormulario->addSpan( $obSpnListaLotes );

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = " d.getElementById('spnFormLotes').innerHTML = '".$stHtml."';\n";
    $stJs.= " d.getElementById('nuQuantidade').disabled = true ;\n";
    $stJs.= " d.getElementById('nuQuantidade').value = '0,0000';\n";
    $stJs.= $obFormulario->getInnerJavascriptBarra();

    return $stJs;
}

function montaFormItemPatrimonial($arItemSelecionado = array())
{
    $pgOcul = "OCMovimentacaoDiversa.php";

    include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioSituacaoBem.class.php");

    $obFormulario = new Formulario;

    //instancio o componente TextBoxSelect para a situacao do bem
    $obITextBoxSelectSituacao = new TextBoxSelect();
    $obITextBoxSelectSituacao->setRotulo( 'Situação' );
    $obITextBoxSelectSituacao->setTitle( 'Informe a situação do bem.' );
    $obITextBoxSelectSituacao->setName( 'inCodTxtSituacao' );
    $obITextBoxSelectSituacao->setNull( false );

    $obITextBoxSelectSituacao->obTextBox->setName                ( "inCodTxtSituacao"     );
    $obITextBoxSelectSituacao->obTextBox->setId                  ( "inCodTxtSituacao"     );
    $obITextBoxSelectSituacao->obTextBox->setSize                ( 6                      );
    $obITextBoxSelectSituacao->obTextBox->setMaxLength           ( 3                      );
    $obITextBoxSelectSituacao->obTextBox->setInteiro             ( true                   );
    $obITextBoxSelectSituacao->obTextBox->setValue 				 ( $arItemSelecionado['inCodSituacao'] );

    $obITextBoxSelectSituacao->obSelect->setName                ( "inCodSituacao"                 );
    $obITextBoxSelectSituacao->obSelect->setId                  ( "inCodSituacao"                 );
    $obITextBoxSelectSituacao->obSelect->setStyle               ( "width: 200px"                  );
    $obITextBoxSelectSituacao->obSelect->setCampoID             ( "cod_situacao"                  );
    $obITextBoxSelectSituacao->obSelect->setCampoDesc           ( "nom_situacao"                  );
    $obITextBoxSelectSituacao->obSelect->addOption              ( "", "Selecione"                 );

    //recupero todos os registros da table patrimonio.situacao_bem e preencho o componenete ITextBoxSelect
    $obTPatrimonioSituacaoBem = new TPatrimonioSituacaoBem();
    $obTPatrimonioSituacaoBem->recuperaTodos( $rsSituacaoBem );

    $obITextBoxSelectSituacao->obSelect->preencheCombo( $rsSituacaoBem );
    $obITextBoxSelectSituacao->obSelect->setValue( $arItemSelecionado['inCodSituacao'] );

    $obRdPlacaIdentificacaoSim = new Radio();
    $obRdPlacaIdentificacaoSim->setRotulo( 'Placa de Identificação' );
    $obRdPlacaIdentificacaoSim->setTitle( 'Informe se o item possui placa de identificação.' );
    $obRdPlacaIdentificacaoSim->setName( 'stPlacaIdentificacao' );
    $obRdPlacaIdentificacaoSim->setValue( 'sim' );
    $obRdPlacaIdentificacaoSim->setLabel( 'Sim' );
    $obRdPlacaIdentificacaoSim->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacao', 'stPlacaIdentificacao' );" );

    $obRdPlacaIdentificacaoNao = new Radio();
    $obRdPlacaIdentificacaoNao->setRotulo( 'Placa de Identificação' );
    $obRdPlacaIdentificacaoNao->setTitle( 'Informe se o item possui placa de identificação' );
    $obRdPlacaIdentificacaoNao->setName( 'stPlacaIdentificacao' );
    $obRdPlacaIdentificacaoNao->setValue( 'nao' );
    $obRdPlacaIdentificacaoNao->setLabel( 'Não' );
    $obRdPlacaIdentificacaoNao->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacao', 'stPlacaIdentificacao' );" );

    if ( ($arItemSelecionado['stPlacaIdentificacao'] == 'sim') || ($arItemSelecionado['stPlacaIdentificacao'] == '' )) {
        $obRdPlacaIdentificacaoSim->setChecked( true );
        $montaPlaca = true;
    } else {
        $obRdPlacaIdentificacaoNao->setChecked( true );
        $montaPlaca = false;
    }

    $obFormulario->addTitulo    ( "Detalhes Bem Patrimonial"     );
    $obFormulario->addComponente( $obITextBoxSelectSituacao );
    $obFormulario->agrupaComponentes( array( $obRdPlacaIdentificacaoSim, $obRdPlacaIdentificacaoNao ) );

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = " d.getElementById('spnFormPatrimonio').innerHTML = '".$stHtml."';\n";
    $stJs.= $obFormulario->getInnerJavascriptBarra();

    //monta o text da placa de identificação por padrão
    $stJs.= montaPlacaIdentificacao($arItemSelecionado['stNumeroPlaca'], $montaPlaca);

    return $stJs;
}

function montaPlacaIdentificacao($numPlaca = "", $boMontaPlaca = false)
{
    $arItens = Sessao::read('itens');

    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
    $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
    $obTAdministracaoConfiguracao->pegaConfiguracao( $boPlacaAlfa, 'placa_alfanumerica' );

    if ($boMontaPlaca == true) {
        $obTxtNumeroPlaca = new TextBox();
        $obTxtNumeroPlaca->setRotulo( 'Número da Placa' );
        $obTxtNumeroPlaca->setTitle( 'Informe o número da placa do bem.' );
        $obTxtNumeroPlaca->setName( 'stNumeroPlaca' );
        $obTxtNumeroPlaca->setId( 'stNumeroPlaca' );

        if ($boPlacaAlfa == 'false') {
            $obTxtNumeroPlaca->setInteiro (true);
        } else {
            $obTxtNumeroPlaca->setCaracteresAceitos( "[a-zA-Z0-9\-]" );
        }

        $obTxtNumeroPlaca->setNull( false );

        $obTPatrimonioBem = new TPatrimonioBem();

        $vlTotalItens = 0;

        # Adiciona o total de quantidades na lista para sugerir a numeração da placa.
        if (is_array($arItens)) {
            foreach ($arItens as $key => $value) {
                $vlTotalItens += $value['quantidade'];
            }
        }

        if ($boPlacaAlfa == 'true') {
            $obTPatrimonioBem->recuperaMaxNumPlacaAlfanumerico($rsNumPlaca);
            $maxNumeroPlaca = $rsNumPlaca->getCampo('num_placa');
        } else {
            $obTPatrimonioBem->recuperaMaxNumPlacaNumerico($rsNumPlaca);

            if ( $rsNumPlaca->getNumLinhas() <=0 ) {
                $inMaiorNumeroPlaca = 0;
            } else {
                $inMaiorNumeroPlaca = $rsNumPlaca->getCampo('num_placa');
            }

            $maxNumeroPlaca = $inMaiorNumeroPlaca;
        }

        # Incrementa a sugestão do num_placa considerando as quantidades selecionadas no itens anteriores.
        for ($i = 0; $i <= $vlTotalItens; $i++)
            $maxNumeroPlaca++;

        $obTxtNumeroPlaca->setValue( $maxNumeroPlaca );
        $obTxtNumeroPlaca->obEvento->setOnChange( "montaParametrosGET( 'verificaIntervalo' );" );

        $obFormulario = new Formulario();
        $obFormulario->addComponente( $obTxtNumeroPlaca );
        $obFormulario->montaInnerHTML();

        $stJs.= "$('spnNumeroPlaca').innerHTML = '".$obFormulario->getHTML()."';";
    } else {
        $stJs.= "$('spnNumeroPlaca').innerHTML = '';";
    }

    return $stJs;
}

function montaAtributos($acao = '')
{

    $arItem = Sessao::read('itens');

    if ($_REQUEST['inCodItem']) {
        Sessao::remove('atributos_'.$_REQUEST['inCodItem']);

        include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php");
        include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php");

        $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
        $obTAlmoxarifadoCatalagoItem->setDado("cod_item", $_REQUEST['inCodItem']);
        $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem);

        $obRAlmoxarifadoCatalogoClassificacao = new RAlmoxarifadoCatalogoClassificacao;
        $obRAlmoxarifadoCatalogoItem = new RAlmoxarifadoCatalogoItem;

        $obTAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem();
        $obTAtributoCatalogoItem->recuperaAtributoDinamicoItem( $rsAtributos, ' AND cod_item = '.$_REQUEST['inCodItem'] );

        if ( $rsAtributos->getNumLinhas() > 0 ) {
            $obHdnTipoAtributos = new Hidden;
            $obHdnTipoAtributos->setName     ( "hdnTipoAtributos" );

            $obFormulario = new Formulario;
            $obFormulario->addHidden      ( $obHdnTipoAtributos    );

            $obMontaAtributos = new MontaAtributos;
            $obMontaAtributos->setLabel($_REQUEST['boTemMovimentacao']);
            $obMontaAtributos->setTitulo     ( "Atributos"  );
            $obMontaAtributos->setName       ( "Atributos_" );
            $obMontaAtributos->setRecordSet  ( $rsAtributos );
            $obMontaAtributos->recuperaValores();
            $obMontaAtributos->geraFormulario ( $obFormulario );
            $obFormulario->montaInnerHTML();

            $rsAtributos->setPrimeiroElemento();
            $inCount = 0;
            while ( !$rsAtributos->eof() ) {
                $arr[$inCount]["cod_atributo"] = $rsAtributos->getCampo('cod_atributo');
                $arr[$inCount]["cod_cadastro"] = $rsAtributos->getCampo('cod_cadastro');
                $arr[$inCount]["cod_modulo"]   = $rsAtributos->getCampo('cod_modulo');
                $arr[$inCount]["nom_atributo"] = $rsAtributos->getCampo('nom_atributo');
                $arr[$inCount]["nulo"]         = $rsAtributos->getCampo('nao_nulo');

                $rsAtributos->proximo();
                $inCount++;

                Sessao::write('atributos_'.$_REQUEST['inCodItem'], $arr);
            }

            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);

            $objetoHtml = str_replace("\\\\","\\",$obFormulario->getHTML());
            $stJs.= "$('spnAtributos').innerHTML = '".$objetoHtml."'; ";
            $stJs .= "f.hdnTipoAtributos.value = '".$stEval."';\n";
        } else {
            $stJs.= "$('spnAtributos').innerHTML = '';";
            $stJs.= "$('spnDadosItem').innerHTML = '';";
        }

        if ( $rsCatalogoItem->getNumLinhas() == 1 ) {
            if ( ($rsCatalogoItem->getCampo('cod_tipo') == 0) OR ($rsCatalogoItem->getCampo('cod_unidade') == 0)  ) {
                include_once(CAM_GA_ADM_COMPONENTES . "ISelectUnidadeMedida.class.php");
                include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoItem.class.php");

                $inCodUnidade  = ($acao == 'alterar') ? $arItem[$_REQUEST['inId']-1]['inCodUnidade']  : $rsCatalogoItem->getCampo('cod_unidade');
                $inCodGrandeza = ($acao == 'alterar') ? $arItem[$_REQUEST['inId']-1]['inCodGrandeza'] : $rsCatalogoItem->getCampo('cod_grandeza');
                $inTipo = $arItem[$_REQUEST['inId']-1]['inCodTipo']?$arItem[$_REQUEST['inId']-1]['inCodTipo']:$rsCatalogoItem->getCampo('cod_tipo');

                $obISelectUnidadeMedida = new ISelectUnidadeMedida;
                $obISelectUnidadeMedida->setName                ( "inCodUnidade"                         );
                $obISelectUnidadeMedida->setValue               ( $inCodUnidade.'-'.$inCodGrandeza       );
                $obISelectUnidadeMedida->setStyle               ( "width: 200px"                         );
                $obISelectUnidadeMedida->setObrigatorioBarra    ( true                                   );

                $obRAlmoxarifadoCatalogoItem = new RAlmoxarifadoCatalogoItem;
                $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->listar( $rsTipo ) ;

                $arRdTipo = array();
                $i = 1;

                while (!$rsTipo->eof()) {
                    if ($rsTipo->getCampo('cod_tipo') != 0) {
                        $obRdTipo = new Radio;
                        $obRdTipo->setRotulo                      ( "Tipo"                                      );
                        $obRdTipo->setTitle                       ( "Selecione o tipo de item desejado."        );
                        $obRdTipo->setName                        ( "inCodTipo"                                 );
                        $obRdTipo->setLabel                       ( $rsTipo->getCampo('descricao')              );
                        $obRdTipo->setValue                       ( $rsTipo->getCampo('cod_tipo')               );
                        if ($inTipo == $i) {
                            $obRdTipo->setChecked                 ( true                                        );
                        } else {
                            $obRdTipo->setChecked                 ( false                                       );
                        }
                        $obRdTipo->obEvento->setOnClick( "montaParametrosGET('montaFormLoteNaoInformado','inCodTipo,inCodItem');" );
                        $obRdTipo->setObrigatorioBarra            ( true                                        );
                        $arRdTipo[] = $obRdTipo;
                        $i++;
                    }
                    $rsTipo->proximo();
                }

                $obFormulario = new Formulario;
                $obFormulario->addComponente($obISelectUnidadeMedida);
                $obFormulario->agrupaComponentes ($arRdTipo );
                $obFormulario->montaInnerHTML();

                $stJs.= "$('spnDadosItem').innerHTML = '".$obFormulario->getHTML()."'; ";
            } else {
                $stJs.= "$('spnDadosItem').innerHTML = '';";
                include_once(CAM_GA_ADM_MAPEAMENTO."TUnidadeMedida.class.php");

                $obTUnidadeMedida = new TUnidadeMedida();
                $obTUnidadeMedida->setDado('cod_unidade',$rsCatalogoItem->getCampo('cod_unidade'));
                $obTUnidadeMedida->setDado('cod_grandeza',$rsCatalogoItem->getCampo('cod_grandeza'));
                $obTUnidadeMedida->recuperaPorChave( $rsUnidadeMedida );

                $obLblUnidadeMedida = new Label;
                $obLblUnidadeMedida->setRotulo('Unidade de Medida' );
                $obLblUnidadeMedida->setId    ('stUnidadeMedida'   );
                $obLblUnidadeMedida->setValue ( $rsUnidadeMedida->getCampo('nom_unidade') );

                $obHdnUnidadeMedida = new Hidden;
                $obHdnUnidadeMedida->setName('inCodUnidade');
                $obHdnUnidadeMedida->setValue( $rsUnidadeMedida->getCampo('cod_unidade').'-'.$rsUnidadeMedida->getCampo('cod_grandeza') );

                $obHdnInCodTipo = new Hidden;
                $obHdnInCodTipo->setName('inCodTipo');
                $obHdnInCodTipo->setValue( $rsCatalogoItem->getCampo('cod_tipo') );

                $obFormulario = new Formulario;
                $obFormulario->addComponente( $obLblUnidadeMedida );
                $obFormulario->addHidden( $obHdnUnidadeMedida );
                $obFormulario->addHidden( $obHdnInCodTipo );
                $obFormulario->montaInnerHTML();
                $stJs.= "$('spnDadosItem').innerHTML = '".$obFormulario->getHTML()."';";
            }
        }
    } else {
        $stJs.= "$('spnAtributos').innerHTML = '';";
        $stJs.= "$('spnDadosItem').innerHTML = '';";
        $stJs.= "$('spnFormLotes').innerHTML = '';";
    }

    return $stJs;
}

function setValoresAtributos($arItensSelecionados)
{
    $arLotes = Sessao::read('lotes');

    $stJs = "";
    if (count($arLotes)>0) {
        $stJs.= montaFormLotes();
        $stJs .= montaListaLotes( $arLotes );
    }

    $stJs .= " d.getElementById('inCodItem').value ='".$arItensSelecionados['inCodItem']."';";
    $stJs .= " d.getElementById('stNomItem').innerHTML ='".addslashes($arItensSelecionados['stNomItem'])."';";
    $stJs .= " d.getElementById('inCodItem').disabled  = true;";
    $stJs .= " d.getElementById('stNomItem').disabled  = true;";
    $stJs .= " d.getElementById('imgBuscar').style.display   = 'none';";

    $stJs .= " d.getElementById('inCodMarca').value ='".$arItensSelecionados['inCodMarca']."';";
    $stJs .= " d.getElementById('stNomMarca').innerHTML ='".$arItensSelecionados['stNomMarca']."';";

    $stJs .= " d.getElementById('inCodigoBarras').value ='".$arItensSelecionados['codigoBarras']."';";

    $stJs .= " d.getElementById('inCodCentroCusto').value ='".$arItensSelecionados['inCodCentroCusto']."';";
    $stJs .= " d.getElementById('stNomCentroCusto').innerHTML ='".$arItensSelecionados['stNomCentroCusto']."';";

    $stJs .= " d.getElementById('nuQuantidade').value ='".$arItensSelecionados['quantidade']."';";

    $stJs .= " d.getElementById('nuVlTotal').value ='".$arItensSelecionados['vtotal']."';";

    $stJs .= " d.getElementById('inId').value ='".$_REQUEST['inId']."';";

    $arAtributosItem = $arItensSelecionados['atributos'];

    $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico;

    foreach ($arAtributosItem as $chave => $dadosAtributos) {

        $stFiltroAtributos = ' AND cod_atributo='.$dadosAtributos['cod_atributo'];
        $obTAdministracaoAtributoDinamico->recuperaRelacionamento($rsAtributos,$stFiltroAtributos);

        $tipo = $rsAtributos->getCampo('cod_tipo');
        if ($tipo == 4) {

            $arValoresAtributo = explode(',',$dadosAtributos['valor']);
            foreach ($arValoresAtributo as $chave => $valoresAtributo) {

                $stJs.= "var i = 0 ;";
                $stJs.= "var text = '' ;";
                $stJs.= "var valor = '' ;";
                $stJs.= "var valorSelecionado = '".str_replace(' ','',trim($valoresAtributo))."';";
                $stJs.= "var idDisponiveis = 'Atributos_".$dadosAtributos['cod_atributo']."_".$dadosAtributos['cod_cadastro']."_Disponiveis';";
                $stJs.= "var idSelecionados = 'Atributos_".$dadosAtributos['cod_atributo']."_".$dadosAtributos['cod_cadastro']."_Selecionados';";

                $stJs.= "var objSelectDisponiveis = d.getElementById(idDisponiveis);        ";
                $stJs.= "var objSelectSelecionados = d.getElementById(idSelecionados);      ";

                $stJs.= "for (i = 0; i<objSelectDisponiveis.length; i++) {                  ";
                $stJs.= "    if( objSelectDisponiveis.options[i].value == valorSelecionado) ";
                $stJs.= "       {                                                           ";
                $stJs.= "           var text = objSelectDisponiveis.options[i].text;        ";
                $stJs.= "           var valor = objSelectDisponiveis.options[i].value;      ";
                $stJs.= "           objSelectDisponiveis.options[i] = null;                 ";
                $stJs.= "       }                                                           ";
                $stJs.= "}                                                                  ";

                $stJs.= "if (text != '' && valor !='') {                                     ";
                $stJs.= "   var arTemp = new Option(text,valor);                            ";
                $stJs.= "   destino = objSelectSelecionados.length;                         ";
                $stJs.= "   objSelectSelecionados.options[destino] = arTemp;                ";
                $stJs.= "}                                                                  ";
            }
        } else {
            $stJs.= "var campoForm = 'Atributos_".$dadosAtributos['cod_atributo']."_".$dadosAtributos['cod_cadastro']."';";
            $stJs.= "f[campoForm].value = '".$dadosAtributos['valor']."';";
        }
    }
    $stJs.= "d.getElementById('botaoIncluir').value = 'Alterar';";
    $stJs.= "d.getElementById('botaoIncluir').setAttribute('onclick', \"montaParametrosGET('alteraItemSelecionado')\");";

    return $stJs;
}

function alteraItemSelecionado()
{
    $boIncluir = true;
    $arItens = Sessao::read('itens');

    $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
    $obTAlmoxarifadoCatalagoItem->setDado("cod_item", $_REQUEST['inCodItem']);
    $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem);

    $arAtributosItem = Sessao::read('atributos_'.$_REQUEST['inCodItem']);

    if ($_REQUEST['inCodUnidade'] == "") {
        $stMensagem = "Selecione a <b><i> Unidade de Medida  </i></b>  do item.";
        $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";

        return $stJs;
    }

    if ($_REQUEST['inCodTipo'] == "") {
        $stMensagem = "Selecione o <b><i> Tipo  </i></b>  do item.";
        $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";

        return $stJs;
    }

    if ( ($_REQUEST['nuQuantidade'] == "0,0000") || ($_REQUEST['nuQuantidade'] == "") ) {
        $stMensagem = "A <b><i>Quantidade</i></b> deve ser <i><b>maior</b></i> que zero.";
        $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";

        return $stJs;
    }

    if (empty($_REQUEST['nuVlTotal']) || $_REQUEST['nuVlTotal'] == "0,0000") {
        $stMensagem = "O <b><i>Valor Total de Mercado</i></b> deve ser <i><b>maior</b></i> que zero.";
        $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";

        return $stJs;
    }

    if ($rsCatalogoItem->getCampo('cod_tipo') == 4) {

        if ($_REQUEST['inCodTxtSituacao'] == '') {
            $stMensagem = "O campo <b><i>Situação</i></b> não pode ser nulo.";
            $boIncluir = false;
        }

        if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
            if ($_REQUEST['stNumeroPlaca'] == '') {
                $stMensagem = "O campo <b><i>Numero da placa</i></b> não pode ser nulo.";
                $boIncluir = false;
            }
        }

    }

    $erro = "";
    $erro = validaAtributosNulos();

    if ($erro != "") {
        $stJs = "alertaAviso('".$erro."','form','erro','".Sessao::getId()."');\n";

        return $stJs;
    }

    $boIncluir = verificaRegistroAtributosIguais($arItens);

    if ($boIncluir == false) {
        $stMensagem = "Já existe um item com o mesmos atributos na lista!";
        $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";

        return $stJs;
    }

    if ($boIncluir == true) {

        $arrayLotes = Sessao::read("lotes");

        $inId = $_REQUEST['inId']-1;

        Sessao::remove('itens');

        $arAtributos = array();
        $arAtributos = montaArrayValoresAtributos();

        $arUnidade = explode('-',$_REQUEST['inCodUnidade']);

        include_once(CAM_GA_ADM_MAPEAMENTO."TUnidadeMedida.class.php");
        $obTUnidadeMedida = new TUnidadeMedida();

        $arUnidade = explode('-',$_REQUEST['inCodUnidade']);
        $obTUnidadeMedida->setDado('cod_unidade',$arUnidade[0]);
        $obTUnidadeMedida->setDado('cod_grandeza',$arUnidade[1]);

        $obTUnidadeMedida->recuperaPorChave( $rsUnidadeMedida );

        foreach ($arItens as $key =>$dados) {
            if ($key == $inId) {
                $arItens[$inId]['inId'            ] = $dados['inId'];
                $arItens[$inId]['stNomUnidade'    ] = $rsUnidadeMedida->getCampo('nom_unidade');
                $arItens[$inId]['inCodMarca'      ] = $_REQUEST['inCodMarca'      ];
                $arItens[$inId]['stNomMarca'      ] = $_REQUEST['stNomMarca'      ];
                $arItens[$inId]['inCodCentroCusto'] = $_REQUEST['inCodCentroCusto'];
                $arItens[$inId]['stNomCentroCusto'] = $_REQUEST['stNomCentroCusto'];
                $arItens[$inId]['quantidade'      ] = $_REQUEST['nuQuantidade'    ];
                $arItens[$inId]['vtotal'          ] = $_REQUEST['nuVlTotal'       ];
                $arItens[$inId]['lotes'           ] = $arrayLotes;
                $arItens[$inId]['atributos'	      ] = $arAtributos;
                $arItens[$inId]['codigoBarras'    ] = $_REQUEST['inCodigoBarras'];
                $arItens[$inId]['inCodTipo'       ] = $_REQUEST['inCodTipo'];
                $arItens[$inId]['inCodUnidade'    ] = $arUnidade[0];
                $arItens[$inId]['inCodGrandeza'   ] = $arUnidade[1];
                $arItens[$inId]['inCodSituacao'    ] = $_REQUEST['inCodTxtSituacao'];
                $arItens[$inId]['stPlacaIdentificacao'    ] = $_REQUEST['stPlacaIdentificacao'];
                $arItens[$inId]['stNumeroPlaca'    ] = $_REQUEST['stNumeroPlaca'];
            }
        }

        Sessao::write('itens',$arItens);

        $stJs .= limparItens();
        $stJs .= "$('spnAtributos').innerHTML = '';";
        $stJs .= "f.nuQuantidade.disabled = false;\n";
        $stJs .= "$('spnDadosItem').innerHTML = '';";
        $stJs .= "$('inCodItem').value = '';";
        $stJs .= "$('stNomItem').innerHTML = '&nbsp;';";
        $stJs .= "$('inCodMarca').value='';";
        $stJs .= "$('stNomMarca').innerHTML = '&nbsp;';";
        $stJs .= "$('inCodCentroCusto').value='';";
        $stJs .= "$('stNomCentroCusto').innerHTML='&nbsp;';";
        $stJs .= "$('nuQuantidade').value='0,0000';";
        $stJs .= "$('nuVlTotal').value='0,00';";
        $stJs .= "$('spnFormPatrimonio').innerHTML = '';";
        $stJs .= "$('spnNumeroPlaca').innerHTML = '';";
        $stJs .= "$('spnFormLotes').innerHTML = '';";
        $stJs .= "f.inCodigoBarras.value = '';";

        $stJs .= " d.getElementById('inCodItem').disabled  = false;";
        $stJs .= " d.getElementById('stNomItem').disabled  = false;";
        $stJs .= " d.getElementById('imgBuscar').style.display   = 'inline';";

        $stJs .= "d.getElementById('botaoIncluir').value = 'Incluir';";
        $stJs .= "d.getElementById('botaoIncluir').setAttribute('onclick', \"montaParametrosGET('incluirmontaListaItens');\");";
        $stJs .= listaItens( $arItens );
    } else {
        $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function montaArrayValoresAtributos()
{
    $inCount = 0;
    $arr = array();

    $arrayDadosAtributos = Sessao::read('atributos_'.$_REQUEST['inCodItem']);

    if ( !empty($arrayDadosAtributos) ) {
        foreach ($arrayDadosAtributos as $arAtributos) {
            if (is_array($_REQUEST['Atributos_'.$arAtributos['cod_atributo'].'_'.$arAtributos['cod_cadastro'].'_Selecionados']) == true) {
                $arr[$inCount]["valor"] = implode(', ',$_REQUEST['Atributos_'.$arAtributos['cod_atributo'].'_'.$arAtributos['cod_cadastro'].'_Selecionados']);
            } else {
                $arr[$inCount]["valor"]  = $_REQUEST['Atributos_'.$arAtributos['cod_atributo'].'_'.$arAtributos['cod_cadastro']];
            }

            $arr[$inCount]["cod_cadastro"] = $arAtributos['cod_cadastro'];
            $arr[$inCount]["cod_modulo"] = $arAtributos['cod_modulo'];
            $arr[$inCount]["cod_atributo"] = $arAtributos['cod_atributo'];
            $arr[$inCount]["nom_atributo"] = $arAtributos['nom_atributo'];
            $arr[$inCount]["nao_nulo"] = $arAtributos['nulo'];

            $inCount++;
        }
    }

    return $arr;
}

function validaAtributosNulos()
{
    $erro = "";
    $arrayDadosAtributos = Sessao::read('atributos_'.$_REQUEST['inCodItem']);

    if ( !empty($arrayDadosAtributos) ) {
        foreach ($arrayDadosAtributos as $arAtributos) {
            if (is_array($_REQUEST['Atributos_'.$arAtributos['cod_atributo'].'_'.$arAtributos['cod_cadastro'].'_Selecionados']) == true) {
                if (($arAtributos['nulo'] == 'f') && count($_REQUEST['Atributos_'.$arAtributos['cod_atributo'].'_'.$arAtributos['cod_cadastro'].'_Selecionados']) == 0) {
                    $erro = 'O Atributo <b><i>'.$arAtributos['nom_atributo'].'</i></b> não pode ser nulo!';

                    return $erro;
                }
            } else {
                if ( ($arAtributos['nulo'] == 'f') && trim($_REQUEST['Atributos_'.$arAtributos['cod_atributo'].'_'.$arAtributos['cod_cadastro']]) == ''  ) {
                    $erro = 'O Atributo <b><i>'.$arAtributos['nom_atributo'].'</i></b> não pode ser nulo!';

                    return $erro;
                }
            }
        }
    }

    return $erro;
}

function verificaRegistroAtributosIguais($arrayItens)
{
    $boIncluir = true;
    $atributosIguais = 0;
    $numeroAtributos = 0;

    if ( count( $arrayItens ) > 0 ) {

        $stChave = $_REQUEST['inCodItem'].'-'.$_REQUEST['inCodMarca'];

        foreach ($arrayItens as $key => $array) {
            $stChaveItem = $array['inCodItem'].'-'.$array['inCodMarca'];

            if ($stChave == $stChaveItem) {

                if ($array['inId'] != $_REQUEST['inId']) {

                    $numeroAtributos = count($arrayItens[$key]['atributos']);

                    if ($numeroAtributos > 0) {
                        foreach ($arrayItens[$key]['atributos'] as $chave => $dados) {

                            if (is_array($_REQUEST['Atributos_'.$dados['cod_atributo'].'_'.$dados['cod_cadastro'].'_Selecionados']) == true) {
                                $valor = implode(', ',$_REQUEST['Atributos_'.$dados['cod_atributo'].'_'.$dados['cod_cadastro'].'_Selecionados']);
                            } else {
                                $valor = $_REQUEST['Atributos_'.$dados["cod_atributo"].'_'.$dados["cod_cadastro"]];
                            }

                            if ($valor == $dados['valor']) {
                                $atributosIguais++;
                            }
                            unset($valor);
                        }
                        if ($atributosIguais == $numeroAtributos) {
                            return false;
                        }
                        unset($atributosIguais);
                    }
                }
            }
        }
    }

    return $boIncluir;
}

switch ($stCtrl) {

    case 'incluirmontaListaItens':

        $boIncluir = true;
        $stMensagem = "";

        $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
        $obTAlmoxarifadoCatalagoItem->setDado("cod_item", $_REQUEST['inCodItem']);
        $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem);

        $arrayLotes = Sessao::read("lotes");
        $arrayItens = Sessao::read("itens");

        if (!$_REQUEST['inCodItem']) {
            $stMensagem = "<b><i>Item</i></b> não pode ser nulo.";
            $boIncluir = false;
        } elseif (!$_REQUEST['inCodMarca']) {
            $stMensagem = "<b><i>Marca</i></b> não pode ser nula.";
            $boIncluir = false;
        } elseif (!$_REQUEST['inCodCentroCusto']) {
            $stMensagem = "<b><i>Centro de Custo</i></b> não pode ser nulo.";
            $boIncluir = false;
        } elseif (!$_REQUEST['inCodAlmoxarifado']) {
            $stMensagem = "<b><i>Almoxarifado</i></b> não pode ser nulo.";
            $boIncluir = false;
        } elseif ( ($_REQUEST['nuQuantidade'] == "0,0000") || ($_REQUEST['nuQuantidade'] == "") ) {
            $stMensagem = "A <b><i>Quantidade</i></b> deve ser <i><b>maior</b></i> que zero.";
            $boIncluir = false;
        } elseif (empty($_REQUEST['nuVlTotal']) || $_REQUEST['nuVlTotal'] == "0,0000") {
            $stMensagem = "O <b><i>Valor Total de Mercado</i></b> deve ser <i><b>maior</b></i> que zero.";
            $boIncluir = false;
        }

        if (($rsCatalogoItem->getCampo('cod_unidade') == 0) || ($rsCatalogoItem->getCampo('cod_tipo') == 0)) {
            if ($_REQUEST['inCodUnidade'] == '') {
                $stMensagem = "Selecione a <b><i> Unidade de Medida  </i></b>  do item.";
                $boIncluir = false;
            } elseif ($_REQUEST['inCodTipo'] == '') {
                $stMensagem = "Selecione o <b><i> Tipo  </i></b>  do item.";
                $boIncluir = false;
            } elseif ( ($rsCatalogoItem->getCampo('cod_tipo') == 2) && (empty($arrayLotes)) ) {
                $stMensagem = "É necessária ao menos um lote para um item perecível.";
                $boIncluir = false;
            }
        }

        if ($rsCatalogoItem->getCampo('ativo') == "f") {

            $stJs .= "$('inCodItem').value = '';";
            $stJs .= "$('stNomItem').innerHTML = '&nbsp;';";
            $stJs .= "f.inCodItem.focus();\n";

            $stMensagem = "O item (".$rsCatalogoItem->getCampo('cod_item').' - '.$rsCatalogoItem->getCampo('descricao_resumida').") está inativo.";
            $boIncluir = false;
        }

        if ($rsCatalogoItem->getCampo('cod_tipo') == 4) {

            if ($_REQUEST['inCodTxtSituacao'] == '') {
                $stMensagem = "O campo <b><i>Situação</i></b> não pode ser nulo.";
                $boIncluir = false;
            }

            if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
                if ($_REQUEST['stNumeroPlaca'] == '') {
                    $stMensagem = "O campo <b><i>Numero da placa</i></b> não pode ser nulo.";
                    $boIncluir = false;
                }
            }

        }

        $erro = "";
        $erro = validaAtributosNulos();

        if ($erro != "") {
            $stMensagem = $erro;
            $boIncluir = false;
        }

        // Terminaram as verificações normais, agora vem a de atributos
        if ($boIncluir == true) {
            $arrayDadosAtributos = Sessao::read('atributos_'.$_REQUEST['inCodItem']);

            if ( !empty($arrayDadosAtributos) ) {

                $boIncluir = verificaRegistroAtributosIguais($arrayItens);
                if ($boIncluir == false) {
                    $stMensagem = "Já existe um item com os mesmos atributos na lista!";
                }

            } else {

                if ( count( $arrayItens ) > 0 ) {
                    $stChave = $_REQUEST['inCodItem'].'-'.$_REQUEST['inCodMarca'].'-'.$_REQUEST['inCodCentroCusto'];
                    foreach ($arrayItens as $key => $array) {
                        $stChaveItem = $array['inCodItem'].'-'.$array['inCodMarca'].'-'.$array['inCodCentroCusto'];
                        if ($stChave == $stChaveItem) {
                            $boIncluir = false;
                            $stMensagem = "Este registro já existe na lista.";
                            break;
                        }
                    }
                }
            }

            $arr = array();
            $arr = montaArrayValoresAtributos();

            if ($boIncluir) {

                include_once(CAM_GA_ADM_MAPEAMENTO."TUnidadeMedida.class.php");
                $obTUnidadeMedida = new TUnidadeMedida();
                $arUnidade = explode('-',$_REQUEST['inCodUnidade']);
                $obTUnidadeMedida->setDado('cod_unidade',$arUnidade[0]);
                $obTUnidadeMedida->setDado('cod_grandeza',$arUnidade[1]);
                $obTUnidadeMedida->recuperaPorChave( $rsUnidadeMedida );

                $arItens = Sessao::read('itens');

                $inId = count($arItens)+1;
                $inIdLine = count($arItens);

                $arItens[$inIdLine]['inId'            ] = $inId;
                $arItens[$inIdLine]['inCodItem'       ] = $_REQUEST['inCodItem'       ];
                $arItens[$inIdLine]['stNomItem'       ] = $rsCatalogoItem->getCampo('descricao');
                $arItens[$inIdLine]['stNomUnidade'    ] = $rsUnidadeMedida->getCampo('nom_unidade');
                $arItens[$inIdLine]['inCodUnidade'    ] = $arUnidade[0];
                $arItens[$inIdLine]['inCodGrandeza'   ] = $arUnidade[1];
                $arItens[$inIdLine]['inCodTipo'	      ] = $_REQUEST['inCodTipo'	   ];
                $arItens[$inIdLine]['inCodMarca'      ] = $_REQUEST['inCodMarca'      ];
                $arItens[$inIdLine]['stNomMarca'      ] = $_REQUEST['stNomMarca'      ];
                $arItens[$inIdLine]['inCodCentroCusto'] = $_REQUEST['inCodCentroCusto'];
                $arItens[$inIdLine]['stNomCentroCusto'] = $_REQUEST['stNomCentroCusto'];
                $arItens[$inIdLine]['quantidade'      ] = $_REQUEST['nuQuantidade'    ];
                $arItens[$inIdLine]['vtotal'          ] = $_REQUEST['nuVlTotal'       ];
                $arItens[$inIdLine]['boPerecivel'     ] = !empty($arrayLotes);
                $arItens[$inIdLine]['lotes'           ] = $arrayLotes;
                $arItens[$inIdLine]['atributos'	      ] = $arr;
                $arItens[$inIdLine]['codigoBarras'    ] = $_REQUEST['inCodigoBarras'];
                $arItens[$inIdLine]['inCodSituacao'    ] = $_REQUEST['inCodTxtSituacao'];
                $arItens[$inIdLine]['stPlacaIdentificacao'    ] = $_REQUEST['stPlacaIdentificacao'];
                $arItens[$inIdLine]['stNumeroPlaca'    ] = $_REQUEST['stNumeroPlaca'];

                $_REQUEST['nuVlTotal'] = $_REQUEST['nuVlTotal'] != '' ? $_REQUEST['nuVlTotal'] : '0,00';

                Sessao::write("itens",$arItens);

                $stJs .= montaCampoAlmoxarifado();
                $stJs .= listaItens( $arItens );
                $stJs .= limparItens();
                $stJs .= "$('spnAtributos').innerHTML = '';";
                $stJs .= "f.nuQuantidade.disabled = false;\n";
                $stJs .= "$('spnDadosItem').innerHTML = '';";
                $stJs .= "$('inCodItem').value = '';";
                $stJs .= "$('stNomItem').innerHTML = '&nbsp;';";
                $stJs .= "$('inCodMarca').value='';";
                $stJs .= "$('stNomMarca').innerHTML = '&nbsp;';";
                $stJs .= "$('inCodCentroCusto').value='';";
                $stJs .= "$('stNomCentroCusto').innerHTML='&nbsp;';";
                $stJs .= "$('nuQuantidade').value='0,0000';";
                $stJs .= "$('nuVlTotal').value='0,00';";
                $stJs .= "$('spnFormLotes').innerHTML = '';";
                $stJs .= "$('spnFormPatrimonio').innerHTML = '';";
                $stJs .= "$('spnNumeroPlaca').innerHTML = '';";
                $stJs .= "f.inCodigoBarras.value = '';";
            } else {
                // mudado para funcionar com Ajax
                $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
            }
        } else {
            // mudado para funcionar com Ajax
            $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
        }
    break;

    case 'alteraItemSelecionado':

            $stJs .= alteraItemSelecionado();
    break;

    case 'montaPlacaIdentificacao':

        if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
            $montaPlaca = true;
        } else {
            $montaPlaca = false;
        }

        $stJs = montaPlacaIdentificacao('',$montaPlaca);
    break;

    case "limparFormulario":
            Sessao::remove("itens");
    break;

    case "alteraItens":

        $arItens = Sessao::read('itens');
        $arItensSelecionado = $arItens[$_REQUEST['inId']-1];

        $stJs .= montaAtributos('alterar');
        $stJs .= setValoresAtributos($arItensSelecionado);

        $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
        $obTAlmoxarifadoCatalagoItem->setDado("cod_item", $_REQUEST['inCodItem']);
        $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem);

        //verifica se o item é do tipo Patrimonio
        if ($rsCatalogoItem->getCampo("cod_tipo") == 4) {
            $stJs.= montaFormItemPatrimonial($arItensSelecionado);
        }
    break;

    case 'verificaIntervalo' :

        if ($_REQUEST['stNumeroPlaca'] != '' AND $_REQUEST['nuQuantidade'] != '') {
            $arNumPlaca = array();
            $numeroPlaca = $_REQUEST['stNumeroPlaca'];
            // monta um array com os números das placas possíveis de acordo com a
            // quantidade informada
            for ($i=0; $i < $_REQUEST['nuQuantidade']; $i++) $arNumPlaca[] = "'".($numeroPlaca++)."'";

            if ($_REQUEST['nuQuantidade'] != '' && $_REQUEST['nuQuantidade'] > 0) {
                $stFiltro = " WHERE num_placa IN (".implode("," ,$arNumPlaca).")";
                $obTPatrimonioBem = new TPatrimonioBem();
                $obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro );

                if ( $rsBem->getNumLinhas() >= 0 ) {

                    $inQuantidade = str_replace('.','', $_REQUEST['nuQuantidade']);
                    $inQuantidade = str_replace(',','.', $inQuantidade  );

                    $inQuantidade = (int) $inQuantidade;

                    $intervalo = ($inQuantidade) + $_REQUEST['stNumeroPlaca'];

                    $stJs.= "alertaAviso('Já existem bens com placas no intervalo selecionado (".$_REQUEST['stNumeroPlaca']." - ".$intervalo.")!','form','erro','".Sessao::getId()."');";
                }
            }
        }
    break;

    case "incluirLotes":
            $boIncluir     = true;
            $totalQtdeItem = 0;
            $arrayLotes    = Sessao::read("lotes");

            if ($_REQUEST['nmQuantidadeLote'] <= "0,0000") {
                $stMensagem = "A Quantidade do Lote deve ser maior que zero.";
                $boIncluir = false;
            } elseif ( count( $arrayLotes ) > 0 ) {

                $stChave = trim($_REQUEST['stNumLote']);

                foreach ($arrayLotes as $key => $array) {

                    $stChaveItem = $array['stNumLote'];

                    if ($stChave == $stChaveItem) {
                        $boIncluir = false;
                        $stMensagem = "Este registro já existe na lista.";
                        $break;
                    }
                }
            }

            if ($boIncluir) {
                $inId = count( $arrayLotes )+1;
                $inPosId = count( $arrayLotes );

                // Mantém o indice correto do array.
                $arrayLotes[$inPosId]['inId'            ] = $inId;
                $arrayLotes[$inPosId]['stNumLote'       ] = trim($_REQUEST['stNumLote'  ]);
                $arrayLotes[$inPosId]['stDataValidade'  ] = $_REQUEST['stDataValidade'  ];
                $arrayLotes[$inPosId]['stDataFabricacao'] = $_REQUEST['stDataFabricacao'];
                $arrayLotes[$inPosId]['nmQuantidadeLote'] = $_REQUEST['nmQuantidadeLote'];

                //Verifica se a soma das quantidades dos lotes não ultrapassa a quantidade do item
                $qtdeItem = str_replace(',', '.', str_replace('.', '', $_REQUEST['nuQuantidade']));
                for ($i=0; $i<count($arrayLotes); $i++) {
                    $qtdeLote = str_replace(',', '.', str_replace('.', '', $arrayLotes[$i]['nmQuantidadeLote']));
                    $totalQtdeItem = $totalQtdeItem + $qtdeLote;
                }

                if ($totalQtdeItem > $qtdeItem) {
                    $stJs = "alertaAviso('A soma das quantidades adicionadas nos lotes ultrapassa a quantidade informada no item. Por favor, ajuste uma das duas quantidades.','form','erro','".Sessao::getId()."');\n";
                } else {
                    Sessao::write("lotes",$arrayLotes);
                    $stJs .= montalistaLotes( $arrayLotes );
                    $stJs.= "limpaFormularioLotes();\n";
                }

            } else {
                $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
            }
    break;

    case "montaFormLotes":
            Sessao::write('lotes', array());

            if ($_REQUEST['inCodItem']) {
                include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php");
                $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
                $obTAlmoxarifadoCatalagoItem->setDado("cod_item", $_REQUEST['inCodItem']);
                $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem);
                //verifica c o tipo do item é perecivel
                if ($rsCatalogoItem->getCampo("cod_tipo") == 2) {
                    $stJs .= montaFormLotes();
                } else {
                    $stJs .= "d.getElementById('spnFormLotes').innerHTML = '';\n";
                    $stJs .= "f.nuQuantidade.disabled = false;\n";
                }
            } else {
                $stJs .= "d.getElementById('spnFormLotes').innerHTML = '';\n";
                $stJs .= "f.nuQuantidade.disabled = false;\n";
            }
    break;

    case "montaFormBemPatrimonio":

            if ($_REQUEST['inCodItem']) {
                $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
                $obTAlmoxarifadoCatalagoItem->setDado("cod_item", $_REQUEST['inCodItem']);
                $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem);

                //verifica se o item é do tipo Patrimonio
                if ($rsCatalogoItem->getCampo("cod_tipo") == 4) {
                    $stJs.= montaFormItemPatrimonial($arItensSelecionado);
                } else {
                    $stJs .= "d.getElementById('spnFormPatrimonio').innerHTML = '';\n";
                    $stJs .= "d.getElementById('spnNumeroPlaca').innerHTML = '';\n";
                    $stJs .= "f.nuQuantidade.disabled = false;\n";
                }
            } else {
                $stJs .= "d.getElementById('spnFormPatrimonio').innerHTML = '';\n";
                $stJs .= "d.getElementById('spnNumeroPlaca').innerHTML = '';\n";
                $stJs .= "f.nuQuantidade.disabled = false;\n";
            }
    break;

    case "limparSessao":
            Sessao::remove("itens");
    break;

    case "montaFormLoteNaoInformado":
            Sessao::write('lotes', array());
            if ($_REQUEST['inCodTipo'] == 2) {
                $stJs .= montaFormLotes();
            } else {
                $stJs .= "d.getElementById('spnFormLotes').innerHTML = '';\n";
                $stJs .= "f.nuQuantidade.disabled = false;\n";
            }
    break;

    case 'montaAtributos' :
        $stJs = montaAtributos();
    break;

    case 'excluiItens':
        $arVariaveis = $arTMP = array();
        $id = $_REQUEST['inId'];
        $inCount = 0;

        $arrayItens = Sessao::read("itens");

        foreach ($arrayItens as $campo => $valor) {

            if ($arrayItens[$campo]['inId'] != $id) {

                $arItens['inId'            ] =  ++$inCount;
                $arItens['inCodItem'       ] =  $arrayItens[$campo]['inCodItem'];
                $arItens['stNomItem'       ] =  $arrayItens[$campo]['stNomItem'];
                $arItens['stNomUnidade'    ] =  $arrayItens[$campo]['stNomUnidade'];
                $arItens['inCodMarca'      ] =  $arrayItens[$campo]['inCodMarca'];
                $arItens['stNomMarca'      ] =  $arrayItens[$campo]['stNomMarca'];
                $arItens['inCodCentroCusto'] =  $arrayItens[$campo]['inCodCentroCusto'];
                $arItens['stNomCentroCusto'] =  $arrayItens[$campo]['stNomCentroCusto'];
                $arItens['quantidade'      ] =  $arrayItens[$campo]['quantidade'];
                $arItens['vtotal'          ] =  $arrayItens[$campo]['vtotal'];

                if (substr($campo,0,8)=="Atributo") {
                    $arItensAtributos = Sessao::read('itens');
                    $arItens[$campo] = $arItensAtributos['.$campo.']['.$campo'];
                }

                $arTMP[] = $arItens;
            }

        }

        Sessao::write('itens',$arTMP);
        $stJs .= listaItens( $arTMP );
    break;

    case 'montaCampoAlmoxarifado':
        $stJs .= montaCampoAlmoxarifado();
    break;

    case 'excluiLote':

        $arVariaveis = $arTMP = array();
        $id = $_REQUEST['inIdLote'];
        $inCount = 0;
        $totalQtdeItem = 0;
        $arrayLotes = Sessao::read("lotes");

        foreach ($arrayLotes as $campo => $valor) {

            if ($arrayLotes[$campo]['inId'] != $id) {
                $arLotes['inId'             ] =  ++$inCount;
                $arLotes['stNumLote'        ] = $arrayLotes[$campo]['stNumLote'];
                $arLotes['stDataFabricacao' ] = $arrayLotes[$campo]['stDataFabricacao'];
                $arLotes['stDataValidade'   ] = $arrayLotes[$campo]['stDataValidade'];
                $arLotes['nmQuantidadeLote' ] = $arrayLotes[$campo]['nmQuantidadeLote'];

                $arTMP[] = $arLotes;
            } else {
                $nmQuantidadeLote = $arrayLotes[$campo]['nmQuantidadeLote'];
            }
        }

        Sessao::write("lotes",$arTMP);

        $stJs.= montaListaLotes( $arTMP );
        //$stJs.= "f.nuQuantidade.value = parseToMoeda($totalQtdeItem, 4);";
    break;

    case 'buscaCodigoBarras':
        $stJs = '';
        $stCodigoBarras = '';
        if ( ( $_REQUEST['inCodMarca'] ) and ( $_REQUEST['inCodItem'] ) ) {
            $obTAlmoxarifadoCatalogoItemBarras = new TAlmoxarifadoCatalogoItemBarras;
            $obTAlmoxarifadoCatalogoItemBarras->setDado( 'cod_item'  , $_REQUEST['inCodItem']  );
            $obTAlmoxarifadoCatalogoItemBarras->setDado( 'cod_marca' , $_REQUEST['inCodMarca'] );
            $obTAlmoxarifadoCatalogoItemBarras->consultar();
            $stCodigoBarras = $obTAlmoxarifadoCatalogoItemBarras->getDado( 'codigo_barras' );
        }
        $stJs = "f.inCodigoBarras.value = '$stCodigoBarras';";

    break;
}

echo $stJs;

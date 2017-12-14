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
    * Página Oculta - Exportação Arquivos GF

    * Data de Criação   : 24/05/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php";
include_once CAM_GPC_TCMPA_MAPEAMENTO."TTPATipoCargo.class.php";
include_once CAM_GPC_TCMPA_MAPEAMENTO."TTPALotacao.class.php";
include_once CAM_GPC_TCMPA_MAPEAMENTO."TTPASituacaoFuncional.class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCargoSituacaoFuncional";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$pgForm     = "FM".$stPrograma.".php";

/**
 * Controle do programa
 */
switch ($_REQUEST['stCtrl']) {

    case 'carregaComboSubdivisao':
        carregaComboSubdivisao();
    break;

    case 'carregaFormularioCargos':
        carregaFormularioCargos();
    break;

    case 'carregaListaLotacao':
        carregaDadosListaLotacao();
    break;

    case 'excluirItemLista':
        excluirItemLista();
    break;

    case 'carregaFormularioAlteracao':
        carregaFormularioCargos('alterar');
    break;

    case 'alterarListaItens':
        $stJs = alterarListaItens();
    break;

    case 'novoElemento':
        $_REQUEST['inCodTipoCargo'] = "";
        $_REQUEST['inCodSituacao'] = "";
        carregaFormularioCargos('novo');
    break;

    case 'incluirLista':
        $stJs = incluiItensLista();
    break;

}

/**
 *carregaDadosListaLotacao
 *
 *Monta e carrega a lista de lotações
 *@return void
 */
function carregaDadosListaLotacao()
{

    $obTTTPALotacao = new TTPALotacao();
    $stFiltroListaCargos = "\n WHERE lotacao.cod_regime=".$_REQUEST['inCodRegime'];
    $stFiltroListaCargos.= "\n AND lotacao.cod_sub_divisao=".$_REQUEST['inCodSubDivisao'];
    $stFiltroListaCargos.= "\n  GROUP BY lotacao.cod_tipo
                                       , lotacao.cod_regime
                                       , lotacao.cod_situacao
                                       , lotacao.cod_sub_divisao
                                       , situacao_funcional.descricao
                                       , tipo_cargo.descricao
                                       , regime.descricao
                                       , sub_divisao.descricao";
    $obTTTPALotacao->recuperaListagemLotacao($rsListaCargos, $stFiltroListaCargos);

    $arLotacao = $rsListaCargos->arElementos;

    foreach ($arLotacao as $key =>$value) {
        $stFiltroListaCargos = "\n WHERE lotacao.cod_regime=".$value['incodregime'];
        $stFiltroListaCargos.= "\n AND lotacao.cod_sub_divisao=".$value['incodsubdivisao'];
        $stFiltroListaCargos.= "\n AND lotacao.cod_tipo=".$value['incodtipocargo'];
        $stFiltroListaCargos.= "\n AND lotacao.cod_situacao=".$value['incodsituacao'];
        $obTTTPALotacao->recuperaCargosLotacao($rsListaCargosLotacao, $stFiltroListaCargos);

        foreach ($rsListaCargosLotacao->arElementos as $chave =>$codCargo) {
            $arLotacao[$key]['cargos'][$chave] = $codCargo['cod_cargo'];
        }
        $arLotacao[$key]['id'] = $key;
    }
    Sessao::write('arLotacao', $rsListaCargos->arElementos);
    montaListaCargo($arLotacao);
}

/**
 *carregaComboSubdivisao
 *
 *Monta e carrega a combo de sub-divisão
 */
function carregaComboSubdivisao()
{
    if ($_REQUEST['inCodRegime'] != "") {

        $obTTPATipoCargo = new TTPATipoCargo();
        $obTTPATipoCargo->setDado( 'cod_regime', $_REQUEST['inCodRegime'] );
        $obTTPATipoCargo->recuperaListagemSubDivisao( $rsPessoalSubDivisao );

        $obCmbPessoalSubDivisao = new Select;
        $obCmbPessoalSubDivisao->setRotulo                        ( "Subdivisão");
        $obCmbPessoalSubDivisao->setTitle                         ( "Selecione a Subdivisão");
        $obCmbPessoalSubDivisao->setName                          ( "inCodSubDivisao");
        $obCmbPessoalSubDivisao->setId                            ( "idCodSubDivisao");
        $obCmbPessoalSubDivisao->setValue                         ( $_REQUEST['inCodSubDivisao']);
        $obCmbPessoalSubDivisao->setStyle                         ( "width: 400px");
        $obCmbPessoalSubDivisao->addOption                        ( "", "Selecione");
        $obCmbPessoalSubDivisao->setNull(false);
        $obCmbPessoalSubDivisao->setCampoId("cod_sub_divisao");
        $obCmbPessoalSubDivisao->obEvento->setOnChange('montaParametrosGET( \'carregaFormularioCargos\',\'inCodRegime,inCodSubDivisao\');');
        $obCmbPessoalSubDivisao->setCampoDesc("descricao");
        $obCmbPessoalSubDivisao->preencheCombo($rsPessoalSubDivisao);

        $obForm = new Form;
        $obForm->setAction ( $pgProc  );
        $obForm->setTarget ( "oculto" );

        $obFormulario = new Formulario();
        $obFormulario->addForm( $obForm );
        $obFormulario->addComponente( $obCmbPessoalSubDivisao );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        echo "d.getElementById('spnSubDivisao').innerHTML = '".$stHtml."';";
    } else {
        echo "d.getElementById('spnSubDivisao').innerHTML = '';";
    }
    echo "d.getElementById('spnFormularioCargo').innerHTML = '';";
}

/**
 *carregaFormularioCargos
 *
 *Monta o formulario de seleção de informações sobre cargos
 */
function carregaFormularioCargos($acao = "")
{
    $arLotacao = Sessao::read('arLotacao');

    $obTPessoalCargo = new TPessoalCargo();
    $obTTipoCargo = new TTPATipoCargo();
    $obTSituacaoFuncional = new TTPASituacaoFuncional();

    $rsCargosDisponiveis = new RecordSet;
    $rsCargosSelecionados = new RecordSet;

    // Define Objeto Button para Incluir cargo
    $obBtnIncluirCargo = new Button;
    $obBtnIncluirCargo->setValue( "Incluir" );
    $obBtnIncluirCargo->setId("IncluirItem");
    $obBtnIncluirCargo->obEvento->setOnClick( "marcaArraySelecionados();montaParametrosGET('incluirLista');resetElementos();");

    // Define Objeto Button para Incluir cargo
    $obBtnAlterarCargo = new Button;
    $obBtnAlterarCargo->setValue( "Alterar" );
    $obBtnAlterarCargo->setId("alterarItem");
    $obBtnAlterarCargo->obEvento->setOnClick( "marcaArraySelecionados();alterarListaIntens('".$_REQUEST['id']."');montaParametrosGET('novoElemento');");

    // Define Objeto Button para Incluir novo cargo
    $obBtnNovoCargo = new Button;
    $obBtnNovoCargo->setValue( "Novo" );
    $obBtnNovoCargo->setId("novoItem");
    $obBtnNovoCargo->obEvento->setOnClick( "montaParametrosGET('novoElemento');");

    // Define Objeto Button para Limpar cargo
    $obBtnLimparCargo = new Button;
    $obBtnLimparCargo->setValue( "Limpar" );
    $obBtnLimparCargo->obEvento->setOnClick("LimparCargos();");

    if ($_REQUEST['inCodSubDivisao'] != "") {
        if ($acao == 'alterar') {
            $codcargos = implode(',',$arLotacao['lotacao'][$_REQUEST['id']]['cargos']);

            $_REQUEST['inCodTipoCargo'] = $arLotacao['lotacao'][$_REQUEST['id']]['incodtipocargo'];
            $_REQUEST['inCodSituacao'] = $arLotacao['lotacao'][$_REQUEST['id']]['incodsituacao'];

            $stFiltroCargo = "where cod_sub_divisao=".$_REQUEST['inCodSubDivisao'];

            $stFiltroCargo.= "\n and cod_cargo not in (".$codcargos.")";
            $obTPessoalCargo->recuperaCargosPorSubDivisao($rsCargosDisponiveis,$stFiltroCargo);

            $stFiltroCargoDisponiveis.= "\n where PC.cod_cargo in (".$codcargos.")";
            $obTPessoalCargo->recuperaRelacionamento($rsCargosSelecionados,$stFiltroCargoDisponiveis);

            $arBotoes[0] = $obBtnAlterarCargo;
            $arBotoes[1] = $obBtnNovoCargo;

        } else {
            $stFiltroCargo = "where cod_sub_divisao=".$_REQUEST['inCodSubDivisao'];

            $obTPessoalCargo->recuperaCargosPorSubDivisao($rsCargosDisponiveis,$stFiltroCargo);

            $arBotoes[0] = $obBtnIncluirCargo;
            $arBotoes[1] = $obBtnLimparCargo;
        }

        // Monta a Combo de tipos de Cargo
        $obTTipoCargo->recuperaTodos( $rsTipoCargo );

        $obCmbPessoalCargo = new Select;
        $obCmbPessoalCargo->setRotulo                        ( "Tipo Cargo");
        $obCmbPessoalCargo->setTitle                         ( "Selecione o Tipo de Cargo");
        $obCmbPessoalCargo->setName                          ( "inCodTipoCargo");
        $obCmbPessoalCargo->setId                            ( "idCodTipoCargo");
        $obCmbPessoalCargo->setValue                         ( $_REQUEST['inCodTipoCargo']);
        $obCmbPessoalCargo->setStyle                         ( "width: 400px");
        $obCmbPessoalCargo->addOption                        ( "", "Selecione");
        $obCmbPessoalCargo->setNull(false);
        $obCmbPessoalCargo->setCampoId("cod_tipo");
        $obCmbPessoalCargo->setCampoDesc("descricao");
        $obCmbPessoalCargo->preencheCombo($rsTipoCargo);

        // Monta a Combo de tipos de Situações Funcionais
        $obTSituacaoFuncional->recuperaListagemSituacaoFuncional( $rsSituacaoFuncional );

        $obCmbPessoalSituacaoFuncional = new Select;
        $obCmbPessoalSituacaoFuncional->setRotulo                        ( "Situação Funcional");
        $obCmbPessoalSituacaoFuncional->setTitle                         ( "Selecione a Situação Funcional");
        $obCmbPessoalSituacaoFuncional->setName                          ( "inCodSituacao");
        $obCmbPessoalSituacaoFuncional->setId                            ( "inCodSituacao");
        $obCmbPessoalSituacaoFuncional->setValue                         ( $_REQUEST['inCodSituacao']);
        $obCmbPessoalSituacaoFuncional->setStyle                         ( "width: 400px");
        $obCmbPessoalSituacaoFuncional->addOption                        ( "", "Selecione");
        $obCmbPessoalSituacaoFuncional->setNull(false);
        $obCmbPessoalSituacaoFuncional->setCampoId("cod_situacao");
        $obCmbPessoalSituacaoFuncional->setCampoDesc("descricao");
        $obCmbPessoalSituacaoFuncional->preencheCombo($rsSituacaoFuncional);

        //Monta Select MUltiplo dos Cargos
        $obCbmCargo = new SelectMultiplo();
        $obCbmCargo->setName  ( 'arCargosSelecionados' );
        $obCbmCargo->setRotulo( "Cargos" );
        $obCbmCargo->setNull  ( false );
        $obCbmCargo->setTitle ( 'Cargos Disponiveis' );

        // lista de Cargos disponiveis
        $obCbmCargo->SetNomeLista1( 'arCargosDisponiveis' );
        $obCbmCargo->setCampoId1  ( 'cod_cargo' );
        $obCbmCargo->setCampoDesc1( 'descricao' );
        $obCbmCargo->obSelect1->setStyle( 'width: 250px' );
        $obCbmCargo->SetRecord1   ( $rsCargosDisponiveis );

        // lista de Cargos selecionados
        $obCbmCargo->SetNomeLista2( 'arCargosSelecionados' );
        $obCbmCargo->setCampoId2  ( 'cod_cargo' );
        $obCbmCargo->setCampoDesc2( 'descricao' );
        $obCbmCargo->obSelect2->setStyle( 'width: 250px' );
        $obCbmCargo->SetRecord2   ( $rsCargosSelecionados );

        $obSpanListaCargos = new Span();
        $obSpanListaCargos->setID( 'spnListaCargo' );

        $obForm = new Form;
        $obForm->setAction ( $pgProc  );
        $obForm->setTarget ( "oculto" );

        $obFormulario = new Formulario();
        $obFormulario->addTitulo("Informações do Cargo");
        $obFormulario->addForm( $obForm );
        $obFormulario->addComponente( $obCmbPessoalCargo );
        $obFormulario->addComponente( $obCmbPessoalSituacaoFuncional );
        $obFormulario->addComponente( $obCbmCargo );
        $obFormulario->defineBarra($arBotoes);
        $obFormulario->addSpan($obSpanListaCargos);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        echo "d.getElementById('spnFormularioCargo').innerHTML = '".$stHtml."';";

        Sessao::write('arLotacao', $arLotacao);
        if ( ($acao == 'alterar') || ($acao == 'novo') ) {
            montaListaCargo($arLotacao['lotacao']);
        } else {
            echo "montaParametrosGET('carregaListaLotacao','inCodSubDivisao,inCodRegime');";
        }
    } else {
        echo "d.getElementById('spnFormularioCargo').innerHTML = '';";
    }
}

/**
 *montaListaCargo
 *
 *Monta a lista de lotações na tela após ser feita a inclusão no array global
 *pela função incluiItensLista
 *
 *@param array $listaLotacao listagem de lotações com os cargos e outras informações
 */
function montaListaCargo($listaLotacao)
{
    $stPrograma = "ManterCargoSituacaoFuncional";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsListaCargo = new RecordSet;
    $rsListaCargo->preenche($listaLotacao);

    $rsListaCargo->setPrimeiroElemento();

    $obLista = new Lista;
    $obLista->setTitulo ( "Listagem de Lotações");
    $obLista->setRecordSet ($rsListaCargo );

    $obLista->setMostraPaginacao( false );
    $obLista->addCabecalho();

    $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Regime/SubDivisão" );
    $obLista->ultimoCabecalho->setWidth( 27 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo Cargo" );
    $obLista->ultimoCabecalho->setWidth( 45 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Situação Funcional" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[descricaoregime] / [descricaosubdivisao]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricaotipocargo" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricaosituacao" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:excluirItemLista();" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:alterar();" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->ultimaAcao->addCampo("2","incodsubdivisao");
    $obLista->commitAcao();

    $rsListaCargo->setPrimeiroElemento();

    $obLista->montaHTML();

    $stHTML = $obLista->getHTML();

    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    echo "document.getElementById('spnListaCargo').innerHTML = '".$stHTML."';";
}

/**
 *excluirItemLista
 *
 *Metodo que exclui um dos itens da lista de lotação
 *e chama a função que monta a lista novamente
 *
 *@return void
 */
function excluirItemLista()
{
    $arrayListaItens = array();

    $arLotacao = Sessao::read('arLotacao');

    unset($arLotacao[$_REQUEST['id']]);
    $arrayListaItens = $arLotacao;

    $indice = 0;

    $arLotacao = array();

    foreach ($arrayListaItens as $key => $value) {
        $arLotacao['lotacao'][$indice] = $value;
        $arLotacao['lotacao'][$indice]['id'] = $key;
        $indice++;
    }
    Sessao::write('arLotacao', $arLotacao);
    montaListaCargo($arLotacao);
}

/**
 *alterarListaItens
 *
 *Metodo que altera um dos itens da lista de lotação
 *e chama a função que monta a lista novamente
 *
 *@return void
 */
function alterarListaItens()
{
    $erro = "";
    $erro = verificaErros('alteracao');
    if ($erro =="") {
        $arLotacao = Sessao::read('arLotacao');
        unset($arLotacao['lotacao'][$_REQUEST['id']]);

        $arLotacao[$_REQUEST['id']]['incodtipocargo'] = $_REQUEST['inCodTipoCargo'];
        $arLotacao[$_REQUEST['id']]['descricaotipocargo'] = SistemaLegado::pegaDado("descricao","tcmpa.tipo_cargo","where cod_tipo=".$_REQUEST['inCodTipoCargo']);
        $arLotacao[$_REQUEST['id']]['incodsituacao'] = $_REQUEST['inCodSituacao'];
        $arLotacao[$_REQUEST['id']]['descricaosituacao'] = SistemaLegado::pegaDado("descricao","tcmpa.situacao_funcional","where cod_situacao=".$_REQUEST['inCodSituacao']);

        $arLotacao[$_REQUEST['id']]['incodsubdivisao'] = $_REQUEST['inCodSubDivisao'];
        $arLotacao[$_REQUEST['id']]['descricaosubdivisao'] = SistemaLegado::pegaDado("descricao","pessoal".Sessao::getEntidade().".sub_divisao","where cod_sub_divisao=".$_REQUEST['inCodSubDivisao']);

        $arLotacao[$_REQUEST['id']]['incodregime'] = $_REQUEST['inCodRegime'];
        $arLotacao[$_REQUEST['id']]['descricaoregime'] = SistemaLegado::pegaDado("descricao","pessoal".Sessao::getEntidade().".regime","where cod_regime=".$_REQUEST['inCodRegime']);

        $arLotacao[$_REQUEST['id']]['cargos'] = $_REQUEST['cargos'];
        $arLotacao[$_REQUEST['id']]['id'] = $_REQUEST['id'];

        Sessao::write('arLotacao', $arLotacao);
        montaListaCargo($arLotacao);
    }

    return $erro;
}

/**
 *incluiItensLista
 *
 *Metodo que adiciona informações ao array de lotações
 *e chama a função que monta a lista
 *
 *@return void
 */
function incluiItensLista()
{
    $erro = "";
    $erro = verificaErros();

    if ($erro =="") {
        $arLotacao = Sessao::read('arLotacao');

        $arrayLotacao['incodtipocargo'] = $_REQUEST['inCodTipoCargo'];
        $arrayLotacao['descricaotipocargo'] = SistemaLegado::pegaDado("descricao","tcmpa.tipo_cargo","where cod_tipo=".$_REQUEST['inCodTipoCargo']);
        $arrayLotacao['incodsituacao'] = $_REQUEST['inCodSituacao'];
        $arrayLotacao['descricaosituacao'] = SistemaLegado::pegaDado("descricao","tcmpa.situacao_funcional","where cod_situacao=".$_REQUEST['inCodSituacao']);

        $arrayLotacao['incodsubdivisao'] = $_REQUEST['inCodSubDivisao'];
        $arrayLotacao['descricaosubdivisao'] = SistemaLegado::pegaDado("descricao","pessoal".Sessao::getEntidade().".sub_divisao","where cod_sub_divisao=".$_REQUEST['inCodSubDivisao']);

        $arrayLotacao['incodregime'] = $_REQUEST['inCodRegime'];
        $arrayLotacao['descricaoregime'] = SistemaLegado::pegaDado("descricao","pessoal".Sessao::getEntidade().".regime","where cod_regime=".$_REQUEST['inCodRegime']);

        $arrayLotacao['cargos'] = $_REQUEST['arCargosSelecionados'];

        $arrayLotacao['id'] = count($arLotacao);

        array_push($arLotacao,$arrayLotacao);

        unset($arrayLotacao);

        Sessao::write('arLotacao', $arLotacao);
        montaListaCargo($arLotacao);
    }

    return $erro;
}

/**
 *verificaErros
 *
 *Verifica erros no array de informações selecionadas pelo usuário
 *@return string $erro descrição do erro do usuário
 */
function verificaErros($acao = "")
{
    $erro = "";
    $arLotacao = Sessao::read('arLotacao');

    if ($acao !='alteracao') {
        if (count($_REQUEST['arCargosSelecionados']) == 0) {
            $erro = "alertaAviso('Deve ser selecionado ao menos um cargo!','form','erro','".Sessao::getId()."');\n";
        }
    } else {
        if (count($_REQUEST['cargos']) == 0) {
            $erro = "alertaAviso('Deve ser selecionado ao menos um cargo!','form','erro','".Sessao::getId()."');\n";
        }
    }

    if ($_REQUEST['inCodSituacao'] =="") {
        $erro ="alertaAviso('Deve ser selecionado a Situacao Funcional!','form','erro','".Sessao::getId()."');\n";
    }

    if ($_REQUEST['inCodTipoCargo'] =="") {
        $erro ="alertaAviso('Deve ser selecionado o tipo de cargo!','form','erro','".Sessao::getId()."');\n";
    }

    if ($acao !='alteracao') {
        foreach ($arLotacao as $key => $valores) {
            if ( ($valores['incodtipocargo'] == $_REQUEST['inCodTipoCargo']) &&  ($valores['incodsituacao'] == $_REQUEST['inCodSituacao']) ) {
                $erro = "alertaAviso('Registro já incluso na lista de lotações!','form','erro','".Sessao::getId()."');\n";
            }
        }
    } else {
        foreach ($arLotacao as $key => $valores) {
            if ($key != $_REQUEST['id']) {
                if ( ($valores['incodtipocargo'] == $_REQUEST['inCodTipoCargo']) &&  ($valores['incodsituacao'] == $_REQUEST['inCodSituacao']) ) {
                    $erro = "alertaAviso('Registro já incluso na lista de lotações!','form','erro','".Sessao::getId()."');\n";
                }
            }
        }
    }

    return $erro;
}

echo $stJs;

?>

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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCManterVeiculo.php 65346 2016-05-13 19:22:15Z lisiane $

    * Casos de uso: uc-03.02.06
*/

setlocale(LC_ALL,'pt_BR');

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/empenho/classes/negocio/REmpenhoEmpenho.class.php';
include_once ( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBem.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculoDocumento.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoVeiculo.class.php' );
include_once ( CAM_GP_PAT_COMPONENTES.'IPopUpBem.class.php' );
include_once ( CAM_GA_ADM_COMPONENTES.'IMontaLocalizacao.class.php' );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaInfracao.class.php' );
include_once( CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php' );
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";
include_once(CAM_GA_PROT_COMPONENTES.'IPopUpProcesso.class.php');
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculoLocacao.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculoCessao.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVeiculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaListaDocumentos($arDocumentos)
{
    global $pgOcul;

    if ( !is_array($arDocumentos) ) {
        $arDocumentos = array();
    }

    $rsDocumentos = new RecordSet();
    $rsDocumentos->preenche( $arDocumentos );

    $obTable = new Table();
    $obTable->setRecordset( $rsDocumentos );
    $obTable->setSummary( 'Lista de Documentos do Veículo' );

    $obTable->Head->addCabecalho( 'Documento', 60 );
    $obTable->Head->addCabecalho( 'Vencimento', 10 );
    $obTable->Head->addCabecalho( 'Situação', 10 );

    $obTable->Body->addCampo( 'nom_documento', 'E' );
    $obTable->Body->addCampo( '[desc_mes]/[ano_documento]', 'C' );
    $obTable->Body->addCampo( 'desc_situacao', 'C' );

    $obTable->Body->addAcao( 'alterar', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."veiculo/".$pgOcul."?".Sessao::getId()."&stDocumento=%s&id=%s', 'montaAlteracaoDocumento' );", array( 'cod_documento','id' ) );
    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."veiculo/".$pgOcul."?".Sessao::getId()."&stDocumento=%s&id=%s', 'excluirDocumento' );", array( 'cod_documento','id' ) );

    $obTable->montaHTML( true );

    return "$('spnDocumentos').innerHTML = '".$obTable->getHtml()."';";

}

function comparador($a, $b){
    if ($a->dtIniLocacao == $b->dtIniLocacao){
        return 0;
    }
    return ($a->dtIniLocacao < $b->obDtIniLocacao) ? -1 : 1;
}


function montaListaLocacoes($arLocacoes)
{
    global $pgOcul;

    if ( !is_array($arLocacoes) ) {
        $arLocacoes = array();  
    }

    foreach ($arLocacoes as $index => $val) {
        $arAux = array_reverse(explode("/", $val['dtIniLocacao']));
        $date = date('d/m/Y', strtotime(($arAux[0]."-".$arAux[1]."-".$arAux[2])));
        $arLocacoes[$index]['dtIniLocacao'] = $date;
    }
    
    usort($arLocacoes,"comparador");

    $rsLocacoes = new RecordSet();
    $rsLocacoes->preenche( $arLocacoes );

    $obTable = new Table();
    $obTable->setRecordset( $rsLocacoes );
    $obTable->setSummary( 'Lista de Locações do Veículo' );

    $obTable->Head->addCabecalho( 'Processo', 10 );
    $obTable->Head->addCabecalho( 'Locatário', 30 );
    $obTable->Head->addCabecalho( 'Empenho', 10 );
    $obTable->Head->addCabecalho( 'Data do Contrato', 10 );
    $obTable->Head->addCabecalho( 'Início', 10 );
    $obTable->Head->addCabecalho( 'Término', 10 );
    $obTable->Head->addCabecalho( 'Valor da Locação', 10 );

    $obTable->Body->addCampo( 'stProcessoLocacao', 'C' );
    $obTable->Body->addCampo( '[inCodLocatario] - [stNomLocatario]', 'C' );
    $obTable->Body->addCampo( '[inNumEmpenhoLocacao]/[stExercicioLocacao]', 'C' );
    $obTable->Body->addCampo( 'dtContrato', 'C' );
    $obTable->Body->addCampo( 'dtIniLocacao', 'C' );
    $obTable->Body->addCampo( 'dtFimLocacao', 'C' );
    $obTable->Body->addCampo( 'inValorLocacao', 'C' );

    $obTable->Body->addAcao( 'alterar', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."veiculo/".$pgOcul."?".Sessao::getId()."&id=%s', 'montaAlteracaoLocacoes' );", array( 'id'));
    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."veiculo/".$pgOcul."?".Sessao::getId()."&id=%s', 'excluirLocacoes' );", array( 'id',));

    $obTable->montaHTML( true );

    return "$('spnLocacaoDados').innerHTML = '".$obTable->getHtml()."';";

}

function montaListaCessoes($arCessoes)
{
    global $pgOcul;

    if ( !is_array($arCessoes) ) {
        $arCessoes = array();  
    }

    foreach ($arCessoes as $index => $val) {
        $arAux = array_reverse(explode("/", $val['dt_inicio']));
        $date = date('d/m/Y', strtotime(($arAux[0]."-".$arAux[1]."-".$arAux[2])));
        $arCessoes[$index]['dt_inicio'] = $date;
    }
    
    usort($arCessoes,"comparador");

    $rsCessoes = new RecordSet();
    $rsCessoes->preenche( $arCessoes );

    $obTable = new Table();
    $obTable->setRecordset( $rsCessoes );
    $obTable->setSummary( 'Lista de Cessões do Veículo' );

    $obTable->Head->addCabecalho( 'Processo', 10 );
    $obTable->Head->addCabecalho( 'CGM Cedente', 30 );
    $obTable->Head->addCabecalho( 'Início', 10 );
    $obTable->Head->addCabecalho( 'Término', 10 );

    $obTable->Body->addCampo( '[cod_processo]/[exercicio]', 'C' );
    $obTable->Body->addCampo( '[cgm_cedente] - [nom_cedente]', 'C' );
    $obTable->Body->addCampo( 'dt_inicio', 'C' );
    $obTable->Body->addCampo( 'dt_termino', 'C' );

    $obTable->Body->addAcao( 'alterar', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."veiculo/".$pgOcul."?".Sessao::getId()."&id=%s', 'montaAlterarCessao' );", array( 'id'));
    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."veiculo/".$pgOcul."?".Sessao::getId()."&id=%s', 'excluirCessao' );", array( 'id',));

    $obTable->montaHTML( true );

    return "$('spnListaCessao').innerHTML = '".$obTable->getHtml()."';";

}

function montaEmpenho()
{
    //instancia um formulario
    $obFormulario = new Formulario();

    //instancia um componente exercicio
    $stExercicio = new Exercicio;
    $stExercicio->setId('stExercicioEmpenho');
    $stExercicio->setName('stExercicioEmpenho');
    $stExercicio->setNull( true );
    $stExercicio->setObrigatorioBarra( true );

    // Define Objeto TextBox para Codigo da Entidade
    $obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
    $obEntidadeUsuario->obTextBox->setId('inCodEntidadeOculto');
    $obEntidadeUsuario->obTextBox->setName('inCodEntidadeOculto');
    $obEntidadeUsuario->setObrigatorioBarra( true );
    
    $obTxtEmpenho = new Inteiro();
    $obTxtEmpenho->setRotulo ( "*Empenho Original" );
    $obTxtEmpenho->setTitle  ( "Informe o número do empenho original");
    $obTxtEmpenho->setName   ( "inCodigoEmpenho" );
    $obTxtEmpenho->setId     ( "inCodigoEmpenho" );
    $obTxtEmpenho->setNull   ( true );
    $obTxtEmpenho->setObrigatorioBarra( true );

    $obFormulario->addComponente( $stExercicio );
    $obFormulario->addComponente( $obEntidadeUsuario );
    $obFormulario->addComponente( $obTxtEmpenho );

    $obFormulario->montaInnerHTML();
    
    return $obFormulario->getHTML();

}

switch ($stCtrl) {
    case 'preencheDetalheBem' :
        if ($_REQUEST['inCodBem'] != '') {
            //recupera a localizacao do bem
            $obTPatrimonioBem = new TPatrimonioBem();
            $obTPatrimonioBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
            $obTPatrimonioBem->recuperaRelacionamentoAnalitico( $rsBem );
            
            //label para o exercicio
            $obLblExercicio = new Label();
            $obLblExercicio->setRotulo( 'Exercício do Empenho' );
            $obLblExercicio->setValue( $rsBem->getCampo('exercicio') );

            //label para a entidade
            $obLblEntidade = new Label();
            $obLblEntidade->setRotulo( 'Entidade' );
            $obLblEntidade->setValue( $rsBem->getCampo('cod_entidade').' - '.$rsBem->getCampo('nom_entidade') );
            
            //label para a orgao
            $obLbl_Orgao = new Label();
            $obLbl_Orgao->setRotulo( 'Órgão' );
            $obLbl_Orgao->setValue( $rsBem->getCampo('orgao_num_orgao').' - '.$rsBem->getCampo('orgao_nom_orgao') );
            
            //label para a unidade
            $obLbl_Unidade = new Label();
            $obLbl_Unidade->setRotulo( 'Unidade' );
            $obLbl_Unidade->setValue( $rsBem->getCampo('unidade_num_unidade').' - '.$rsBem->getCampo('unidade_nom_unidade') );

            //label para  o empenho
            $obLblEmpenho = new Label();
            $obLblEmpenho->setRotulo( 'Número do Empenho' );
            $obLblEmpenho->setValue( $rsBem->getCampo('cod_empenho') );

            //label para  o nota fiscal
            $obLblNotaFiscal = new Label();
            $obLblNotaFiscal->setRotulo( 'Nota Fiscal' );
            $obLblNotaFiscal->setValue( $rsBem->getCampo('nota_fiscal') );

//----- Início do bloco que busca órgãos através da classificação
            if ( $rsBem->getNumLinhas() == 1 ) {
                $obTPatrimonioBem = new TPatrimonioBem();
                $obTPatrimonioBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                $obTPatrimonioBem->recuperaOrgaoBem($rsPatrimonioBem);
                $inCodOrgao = $rsPatrimonioBem->getCampo('cod_orgao');
                
                $obTPatrimonioBem = new TPatrimonioBem();
                $obTPatrimonioBem->setDado( 'cod_orgao', $inCodOrgao );
                $obTPatrimonioBem->recuperaOrganogramaBem($rsPatrimonioBem);
                $inCodOrganograma = $rsPatrimonioBem->getCampo('cod_organograma');
                
                $obTPatrimonioBem = new TPatrimonioBem();
                $obTPatrimonioBem->setDado( 'cod_orgao', $inCodOrgao );
                $obTPatrimonioBem->recuperaCountOrgaoNivelBem($rsPatrimonioBem);
                $inCount = $rsPatrimonioBem->getCampo('count');
                
                $obTPatrimonioBem = new TPatrimonioBem();
                $obTPatrimonioBem->setDado( 'cod_orgao', $inCodOrgao );
                $obTPatrimonioBem->setDado( 'cod_organograma', $inCodOrganograma );
                $obTPatrimonioBem->recuperaVwOrgaoNivelBem($rsPatrimonioBem, ' WHERE cod_orgao = '.$inCodOrgao.' AND cod_organograma = '.$inCodOrganograma);
                $classificacao = explode('.', $rsPatrimonioBem->getCampo('orgao'));
                
                $orgao = '';
                for($j=0; $j<count($classificacao); $j++) {
                    $orgao.= $classificacao[$j].'.';
                    $arOrgao[] = substr($orgao, 0, (strlen($orgao)-1));
                }

                if ($orgao != '') {
                    $orgao = substr($orgao, 0, (strlen($orgao)-1));
                }
                
                for($i=0; $i<count($classificacao); $i++) {
                    $obTPatrimonioBem = new TPatrimonioBem();
                    $obTPatrimonioBem->recuperaVwOrgaoNivelBem($rsPatrimonioBem, " WHERE orgao_reduzido = '".$arOrgao[$i]."'");
                    $inCodOrgao = $rsPatrimonioBem->getCampo('cod_orgao');
                    
                    if ($inCodOrgao != '') {
                        $obTPatrimonioBem = new TPatrimonioBem();
                        $obTPatrimonioBem->setDado( 'cod_orgao', $inCodOrgao );
                        $obTPatrimonioBem->recuperaOrgaoDescricaoBem($rsPatrimonioBem);
                        
                        $arOrgaoDescricao[] = $rsPatrimonioBem->getCampo('descricao');
                    }
                }
//----- Término do bloco que busca órgãos através da classificação
                
                //pega a mascara para o localizacao
                $arMascara = explode('.',sistemaLegado::pegaConfiguracao( 'mascara_local',2 ));
                $arMascara[4] = explode('/',$arMascara[4]);

                //label para a localizacao
                $obLblLocalizacao = new Label();
                $obLblLocalizacao->setRotulo( 'Localização Atual' );
                $obLblLocalizacao->setValue( $orgao );

                //label para o orgao
                $obLblOrgao = new Label();
                $obLblOrgao->setRotulo( 'Órgão' );
                $obLblOrgao->setValue( $arOrgaoDescricao[0] );

                //label para a unidade
                $obLblUnidade = new Label();
                $obLblUnidade->setRotulo( 'Unidade' );
                $obLblUnidade->setValue( $arOrgaoDescricao[1] );

                //label para o departamento
                $obLblDepartamento = new Label();
                $obLblDepartamento->setRotulo( 'Departamento' );
                $obLblDepartamento->setValue( $arOrgaoDescricao[2] );

                //label para a setor
                $obLblSetor = new Label();
                $obLblSetor->setRotulo( 'Setor' );
                $obLblSetor->setValue( $rsBem->getCampo('nom_setor') );

                //label para a unidade
                $obLblLocal = new Label();
                $obLblLocal->setRotulo( 'Local' );
                $obLblLocal->setValue( $rsBem->getCampo('nom_local') );

                //cria um form para os labels da localizacao
                $obFormulario = new Formulario();
                $obFormulario->addComponente( $obLblExercicio );
                $obFormulario->addComponente( $obLblEntidade );
                
                $obFormulario->addComponente( $obLbl_Orgao );
                $obFormulario->addComponente( $obLbl_Unidade );
                
                $obFormulario->addComponente( $obLblEmpenho );
                $obFormulario->addComponente( $obLblNotaFiscal );

                $obFormulario->addComponente( $obLblLocalizacao );
                $obFormulario->addComponente( $obLblOrgao );
                $obFormulario->addComponente( $obLblUnidade );
                $obFormulario->addComponente( $obLblDepartamento );
                $obFormulario->addComponente( $obLblSetor );
                $obFormulario->addComponente( $obLblLocal );

                $obFormulario->montaInnerHTML();

                $stJs = "$('spnDados').innerHTML = '".$obFormulario->getHTML()."';";

                $obForm = new Form();

                //cria um formulario para os dados do responsavel pelo bem
                $obFormulario = new Formulario();
                $obFormulario->addTitulo( 'Dados do Responsável' );

                if ( $rsBem->getCampo('num_responsavel') != '' ) {
                    //cria um hidden para o cod_responsavel
                    $obHdnCodResponsavel = new Hidden();
                    $obHdnCodResponsavel->setName('inCodResponsavel');
                    $obHdnCodResponsavel->setValue( $rsBem->getCampo('num_responsavel') );

                    //cria um label para o responsavel
                    $obLblResponsavel = new Label();
                    $obLblResponsavel->setRotulo( 'Responsável');
                    $obLblResponsavel->setValue( $rsBem->getCampo('num_responsavel').' - '.$rsBem->getCampo('nom_responsavel') );

                    //cria um label para a data de inicio do responsavel
                    $obDtInicio = new Data();
                    $obDtInicio->setRotulo( 'Data de Início' );
                    //$obDtInicio->setTitle( 'Informe a data de início do responsável.' );
                    $obDtInicio->setValue( $rsBem->getCampo('dt_inicio') );
                    $obDtInicio->setName( 'dtInicio' );

                    $obFormulario->addHidden( $obHdnCodResponsavel );
                    $obFormulario->addComponente( $obLblResponsavel );
                    $obFormulario->addComponente( $obDtInicio );
                } else {

                    //instancia o componente IPopUpCGMVinculado para o responsavel
                    $obIPopUpResponsavel = new IPopUpCGMVinculado( $obForm );
                    $obIPopUpResponsavel->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica' );
                    $obIPopUpResponsavel->setCampoVinculo     ( 'numcgm'               );
                    $obIPopUpResponsavel->setNomeVinculo      ( 'Responsável'          );
                    $obIPopUpResponsavel->setRotulo           ( 'Responsável'          );
                    $obIPopUpResponsavel->setTitle            ( 'Informe o reponsável pelo veículo.' );
                    $obIPopUpResponsavel->setName             ( 'stNomResponsavel'       );
                    $obIPopUpResponsavel->setId               ( 'stNomResponsavel'       );
                    //$obIPopUpResponsavel->setValue            ( $rsVeiculo->getCampo('nom_responsavel') );
                    $obIPopUpResponsavel->obCampoCod->setName ( 'inCodResponsavel'       );
                    $obIPopUpResponsavel->obCampoCod->setId   ( 'inCodResponsavel'       );
                    //$obIPopUpResponsavel->obCampoCod->setValue( $rsVeiculo->getCampo('cod_responsavel') );
                    $obIPopUpResponsavel->setNull             ( false                    );

                    //instancia um componente data para o inicio do responsavel
                    $obDtInicio = new Data();
                    $obDtInicio->setRotulo('Data de Início');
                    $obDtInicio->setTitle('Informe a data de início do responsável.');
                    $obDtInicio->setName('dtInicio');
                    $obDtInicio->setId('dtInicio');
                    $obDtInicio->setNull(false);

                    $obFormulario->addComponente( $obIPopUpResponsavel );
                    $obFormulario->addComponente( $obDtInicio );

                }

                $obFormulario->montaInnerHTML();

                $stJs .= "$('spnResponsavel').innerHTML = '".$obFormulario->getHTML()."';";
            } else {
                $stJs .= "$('spnDados').innerHTML = '';";
            }
        } else {
            $stJs .= "$('spnDados').innerHTML = '';";
        }
        break;

    case 'montaEmpenho' :

        if ($_REQUEST['stSituacao'] == 'pago') {
            $stJs .= "$('spnEmpenho').innerHTML = '".montaEmpenho()."';";
        } else {
            $stJs .= "$('spnEmpenho').innerHTML = '';";
        }
        break;

    case 'montaOrigem' :
        if ($_REQUEST['stOrigem'] != '') {
            //cria um form
            $obForm = new Form();
            $obForm->setAction ($pgProc);
            $obForm->setTarget ("oculto");

            $obFormulario = new Formulario();

            //se for proprio, monta o formulario com ipoup
            if ($_REQUEST['stOrigem'] == 'proprio') {
                //seta um componente ipopup
                $obIPopUpBem = new IPopUpBem( $obForm );
                $obIPopUpBem->setRotulo     ( 'Código do Bem' );
                $obIPopUpBem->setValue      ( $_REQUEST['stNomPropriedade'] );
                $obIPopUpBem->obCampoCod->setValue( $_REQUEST['inCodPropriedade'] );
                $obIPopUpBem->obCampoCod->obEvento->setOnBlur( "ajaxJavaScript('".$pgOcul.'?'.Sessao::getId()."&inCodBem='+this.value,'preencheDetalheBem');" );

                //span para os dados do bem
                $obSpnDados = new Span();
                $obSpnDados->setId( 'spnDados' );

                //instancia um formulario
                $obFormulario->addComponente( $obIPopUpBem );
                $obFormulario->addSpan      ( $obSpnDados );
            } elseif ($_REQUEST['stOrigem'] == 'terceiro') {

                $obSlEntidade = new Select();
                $obSlEntidade->setRotulo( 'Entidade' );
                $obSlEntidade->setTitle( 'Selecione a entidade do bem.' );
                $obSlEntidade->setName( 'inCodEntidade' );
                $obSlEntidade->setId( 'inCodEntidade' );
                $obSlEntidade->setValue($_REQUEST['inCodEntidade'] );
                $obSlEntidade->addOption( '','Selecione' );
                $obSlEntidade->setNull( true );

                $obExercicio = new Exercicio();
                $obExercicio->setRotulo( 'Exercício' );
                $obExercicio->setNull( true);
                $obExercicio->setName( stExercicioEntidade);
                $obExercicio->setId( 'stExercicioEntidade' );
                $obExercicio->setValue( $_REQUEST['stExercicioEntidade'] );
                $obExercicio->obEvento->setOnChange( "montaParametrosGET( 'preencheComboEntidade', 'stExercicioEntidade,hdnInCodEntidade'); " );
                
                $obTxtOrgao = new TextBox;
                $obTxtOrgao->setRotulo   ("Órgão");
                $obTxtOrgao->setTitle    ("Informe o órgão para filtro");
                $obTxtOrgao->setName     ("inCodOrgaoTxt");
                $obTxtOrgao->setId       ("inCodOrgaoTxt");
                $obTxtOrgao->setValue    ($_REQUEST['inCodOrgao']);
                $obTxtOrgao->setSize     (6);
                $obTxtOrgao->setMaxLength(3);
                $obTxtOrgao->setInteiro  (true);
                if ($_REQUEST['inCodOrgao'] != '' && $_REQUEST['inCodUnidade'] != '') {
                    $obTxtOrgao->obEvento->setOnSubmit("buscaValor('MontaUnidade','inCodUnidade');");
                }else {
                    $obTxtOrgao->obEvento->setOnChange("montaParametrosGET('MontaUnidade');" );
                }
                
                $obTxtUnidade = new TextBox;
                $obTxtUnidade->setRotulo   ("Unidade");
                $obTxtUnidade->setTitle    ("Informe a unidade para filtro");
                $obTxtUnidade->setName     ("inCodUnidadeTxt");
                $obTxtUnidade->setId       ("inCodUnidadeTxt");
                $obTxtUnidade->setValue    ($_REQUEST['inCodUnidade']);
                $obTxtUnidade->setSize     (6);
                $obTxtUnidade->setMaxLength(3);
                $obTxtUnidade->setInteiro  (true);
                  

                include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPAnuLiqEstLiq.class.php";
                $obREmpenhoRPAnuLiqEstLiq = new REmpenhoRelatorioRPAnuLiqEstLiq;
                $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio() );
                $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar($rsCombo);
             
                $obCmbOrgao = new Select;
                $obCmbOrgao->setRotulo            ("Órgão");
                $obCmbOrgao->setName              ("inCodOrgao");
                $obCmbOrgao->setId                ("inCodOrgao");
                $obCmbOrgao->setValue             ($_REQUEST['inCodOrgao']);
                $obCmbOrgao->setStyle             ("width: 400px");
                $obCmbOrgao->setCampoID           ("num_orgao");
                $obCmbOrgao->setCampoDesc         ("nom_orgao");
                $obCmbOrgao->addOption            ('', 'Selecione');
                $obCmbOrgao->preencheCombo        ($rsCombo);
                $obCmbOrgao->obEvento->setOnChange("montaParametrosGET('MontaUnidade');" );

                $obCmbUnidade= new Select;
                $obCmbUnidade->setRotulo   ("Unidade");
                $obCmbUnidade->setName     ("inCodUnidade");
                $obCmbUnidade->setId       ("inCodUnidade");
                $obCmbUnidade->setValue    ($_REQUEST['inCodUnidade']);
                $obCmbUnidade->setStyle    ("width: 400px");
                $obCmbUnidade->setCampoID  ("cod_unidade");
                $obCmbUnidade->setCampoDesc("descricao");                
                $obCmbUnidade->addOption   ('', 'Selecione');
                
                //instancia o componente IPopUpCGM para o proprietario
                $obIPopUpProprietario = new IPopUpCGM      ( $obForm );
                $obIPopUpProprietario->setRotulo           ( 'Proprietário'          );
                $obIPopUpProprietario->setTitle            ( 'Informe o proprietário do veículo.' );
                $obIPopUpProprietario->setName             ( 'stNomProprietario'      );
                $obIPopUpProprietario->setId               ( 'stNomProprietario'      );
                $obIPopUpProprietario->obCampoCod->setName ( 'inCodProprietario'      );
                $obIPopUpProprietario->obCampoCod->setId   ( 'inCodProprietario'      );
                $obIPopUpProprietario->obCampoCod->setValue( $_REQUEST['inCodPropriedade'] );
                $obIPopUpProprietario->setValue( $_REQUEST['stNomPropriedade'] );
                $obIPopUpProprietario->setNull             ( false                    );

                $arOrganogramaLocal = explode('.',$_REQUEST['stLocalizacao']);

                include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
                include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";

                $obIMontaOrganograma = new IMontaOrganograma(false);
                $obIMontaOrganograma->setCodOrgao($arOrganogramaLocal[0]);
                $obIMontaOrganograma->setCadastroOrganograma(true);
                $obIMontaOrganograma->setNivelObrigatorio(1);

                $obIMontaOrganograma->setHiddenEvalName('hdnOrigem');

                $obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
                $obIMontaOrganogramaLocal->setValue($arOrganogramaLocal[1]);
                $obIMontaOrganogramaLocal->setNull(false);
                $obFormulario->addComponente( $obExercicio );
                $obFormulario->addComponente( $obSlEntidade );
                $obFormulario->addComponenteComposto($obTxtOrgao, $obCmbOrgao);
                $obFormulario->addComponenteComposto($obTxtUnidade, $obCmbUnidade);
                $obFormulario->addComponente( $obIPopUpProprietario );
                $obIMontaOrganograma->geraFormulario($obFormulario);
                $obIMontaOrganogramaLocal->geraFormulario($obFormulario);

                if ($obIMontaOrganograma->getScript()) {
                    $stEval = $stEval.$obIMontaOrganograma->getScript();
                }

                //cria um form
                $obFormLocacao = new Form();
                $obFormLocacao->setAction ($pgProc);
                $obFormLocacao->setTarget ("oculto");

                $obFormularioLocacao = new Formulario();

                // LOCAÇÃO -----------------------------------------

                //id da locacao
                $obHdnIdLocacao = new Hidden;
                $obHdnIdLocacao->setId('hdnIdLocacao');
                $obHdnIdLocacao->setName('hdnIdLocacao');

                //processo
                $obPopUpProcesso = new IPopUpProcesso($obFormLocacao);
                $obPopUpProcesso->setRotulo("Processo");
                $obPopUpProcesso->obCampoCod->setId('stProcessoLocacao');
                $obPopUpProcesso->obCampoCod->setName('stProcessoLocacao');
                $obPopUpProcesso->setValidar(true);
                $obPopUpProcesso->setObrigatorioBarra( true );
                $obPopUpProcesso->setNull(true);

                //data do contrato
                $obDtContrato = new Data();
                $obDtContrato->setRotulo( 'Data do Contrato' );
                $obDtContrato->setTitle( 'Informe a data do contrato de locação.' );
                $obDtContrato->setName( 'dtContrato' );
                $obDtContrato->setId( 'dtContrato' );
                $obDtContrato->setObrigatorioBarra( true );
                $obDtContrato->setNull( true );

                //data inicial
                $obDtIniLocacao = new Data();
                $obDtIniLocacao->setRotulo( 'Data de Início' );
                $obDtIniLocacao->setTitle( 'Informe a data de início da locação.' );
                $obDtIniLocacao->setName( 'dtIniLocacao' );
                $obDtIniLocacao->setId( 'dtIniLocacao' );
                $obDtIniLocacao->setObrigatorioBarra( true );
                $obDtIniLocacao->setNull( true );

                //data final
                $obDtFimLocacao = new Data();
                $obDtFimLocacao->setRotulo( 'Data de Término' );
                $obDtFimLocacao->setTitle( 'Informe a data de término da locação.' );
                $obDtFimLocacao->setName( 'dtFimLocacao' );
                $obDtFimLocacao->setId( 'dtFimLocacao' );
                $obDtFimLocacao->setObrigatorioBarra( true );
                $obDtFimLocacao->setNull( true );

                //exercicio da locacao
                $obExercicioLocacao = new Exercicio();
                $obExercicioLocacao->setRotulo( 'Exercício' );
                $obExercicioLocacao->setObrigatorioBarra( true );
                $obExercicioLocacao->setNull( true );
                $obExercicioLocacao->setId( 'stExercicioLocacao' );
                $obExercicioLocacao->setName( 'stExercicioLocacao' );

                // entidade
                $obISelectEntidadeLocacao = new ITextBoxSelectEntidadeGeral();
                $obISelectEntidadeLocacao->obTextBox->setName ('inCodEntidadeLocacao');
                $obISelectEntidadeLocacao->obTextBox->setId ('inCodEntidadeLocacao');
                $obISelectEntidadeLocacao->obSelect->setName ('stNomEntidadeLocacao');
                $obISelectEntidadeLocacao->obSelect->setId ('stNomEntidadeLocacao');
                $obISelectEntidadeLocacao->setObrigatorioBarra( true );
                $obISelectEntidadeLocacao->setNull  ( true );

                //instancia o componente para o empenho
                $obNumEmpenhoLocacao = new Inteiro();
                $obNumEmpenhoLocacao->setRotulo( 'Empenho' );
                $obNumEmpenhoLocacao->setTitle ( 'Informe o número do empenho da locação.' );
                $obNumEmpenhoLocacao->setName  ( 'inNumEmpenhoLocacao' );
                $obNumEmpenhoLocacao->setId    ( 'inNumEmpenhoLocacao' );
                $obNumEmpenhoLocacao->setMaxLength (8);
                $obNumEmpenhoLocacao->setObrigatorioBarra( true );
                $obNumEmpenhoLocacao->setNull  ( true );

                //Valor da depreciação inicial.
                $obInValorLocacao = new Moeda();
                $obInValorLocacao->setRotulo('Valor da Locação');
                $obInValorLocacao->setTitle ('Informe o valor da locação.');
                $obInValorLocacao->setName  ('inValorLocacao');
                $obInValorLocacao->setId  ('inValorLocacao');
                $obInValorLocacao->setObrigatorioBarra( true );
                $obInValorLocacao->setNull  ( true );

                //instancia o componente IPopUpCGMVinculado para o responsavel
                $obIPopUpLocatario = new IPopUpCGMVinculado( $obFormLocacao );
                $obIPopUpLocatario->setTabelaVinculo    ( 'sw_cgm_pessoa_juridica' );
                $obIPopUpLocatario->setCampoVinculo     ( 'numcgm' );
                $obIPopUpLocatario->setNomeVinculo      ( 'locatário' );
                $obIPopUpLocatario->setRotulo           ( 'Locatário' );
                $obIPopUpLocatario->setTitle            ( 'Informe o locatário do veículo.' );
                $obIPopUpLocatario->setName             ( 'stNomLocatario' );
                $obIPopUpLocatario->setId               ( 'stNomLocatario' );
                $obIPopUpLocatario->obCampoCod->setName ( 'inCodLocatario' );
                $obIPopUpLocatario->obCampoCod->setId   ( 'inCodLocatario' );
                $obIPopUpLocatario->setObrigatorioBarra ( true );
                $obIPopUpLocatario->setNull             ( true );

                //define objeto buttion para incluir dados da locação
                $obBtnIncluirLocacao = new Button;
                $obBtnIncluirLocacao->setValue             ( "Incluir" );
                $obBtnIncluirLocacao->setId                ( "incluiDadosLocacao" );
                $obBtnIncluirLocacao->obEvento->setOnClick ( "montaParametrosGET('incluirDadosLocacao',
                                                                                 'stProcessoLocacao,
                                                                                 dtContrato,
                                                                                 dtIniLocacao,
                                                                                 dtFimLocacao,
                                                                                 stExercicioLocacao,
                                                                                 inCodEntidadeLocacao,
                                                                                 inNumEmpenho,
                                                                                 inValorLocacao,
                                                                                 inCodLocatario,
                                                                                 inNumEmpenhoLocacao,
                                                                                 stNomLocatario'
                                                                                );"
                                                            );
                
                //Define Objeto Button para Limpar dados da locação
                $obBtnLimparLocacao = new Button;
                $obBtnLimparLocacao->setValue             ( "Limpar" );
                $obBtnLimparLocacao->obEvento->setOnClick ( "montaParametrosGET('limparDadosLocacao');" );
                
                //cria um span para os dados da locação
                $obSpnLocacaoDados = new Span();
                $obSpnLocacaoDados->setId( 'spnLocacaoDados' );

                $obFormularioLocacao->addTitulo ( 'Locação' );
                $obFormularioLocacao->addHidden($obHdnIdLocacao);
                $obFormularioLocacao->addComponente($obPopUpProcesso);
                $obFormularioLocacao->addComponente($obIPopUpLocatario);
                $obFormularioLocacao->addComponente($obDtContrato);
                $obFormularioLocacao->addComponente($obDtIniLocacao);
                $obFormularioLocacao->addComponente($obDtFimLocacao);
                $obFormularioLocacao->addComponente($obExercicioLocacao);
                $obFormularioLocacao->addComponente($obISelectEntidadeLocacao);
                $obFormularioLocacao->addComponente($obNumEmpenhoLocacao);
                $obFormularioLocacao->addComponente($obInValorLocacao);

                $obFormularioLocacao->defineBarra ( array( $obBtnIncluirLocacao, $obBtnLimparLocacao ) );
                $obFormularioLocacao->addSpan ( $obSpnLocacaoDados );

                $obFormularioLocacao->montaInnerHTML();
                
                
                $rsVeiculoLocacoes = new Recordset;
                if ( $_REQUEST['inCodVeiculo'] ) {
                    $stFiltroLocacao = " WHERE cod_veiculo = ".$_REQUEST['inCodVeiculo']." AND exercicio = '".Sessao::getExercicio()."'";
                
                    $obTFrotaVeiculoLocacao = new TFrotaVeiculoLocacao;
                    $obTFrotaVeiculoLocacao->recuperaTodos($rsVeiculoLocacoes, $stFiltroLocacao);
                }
                

                
                $stJs .= "$('spnLocacao').innerHTML = '".$obFormularioLocacao->getHTML()."'; ";
                if ($rsVeiculoLocacoes->getNumLinhas() > 0) {
                    $arVeiculoLocacoes = array();
                    foreach ($rsVeiculoLocacoes->getElementos() as $i => $valor) {
                        $arVeiculoLocacoes[$i]['id']                   = $i+1;
                        $arVeiculoLocacoes[$i]['id_locacao']           = $valor['id'];
                        $arVeiculoLocacoes[$i]['stProcessoLocacao']    = str_pad($valor['cod_processo'],5,'0',STR_PAD_LEFT)."/".$valor['ano_exercicio'];
                        $arVeiculoLocacoes[$i]['stExercicioLocacao']   = $valor['exercicio'];
                        $arVeiculoLocacoes[$i]['dtIniLocacao']         = $valor['dt_inicio'];
                        $arVeiculoLocacoes[$i]['dtFimLocacao']         = $valor['dt_termino'];
                        $arVeiculoLocacoes[$i]['inCodEntidadeLocacao'] = $valor['cod_entidade'];
                        $arVeiculoLocacoes[$i]['dtContrato']           = $valor['dt_contrato'];
                        $arVeiculoLocacoes[$i]['inCodLocatario']       = $valor['cgm_locatario'];
                        $arVeiculoLocacoes[$i]['inNumEmpenhoLocacao']  = $valor['cod_empenho'];
                        $arVeiculoLocacoes[$i]['inValorLocacao']       = number_format($valor['vl_locacao'], 2, ",", ".");
                        $arVeiculoLocacoes[$i]['stNomLocatario']       = SistemaLegado::pegaDado('nom_cgm','sw_cgm',' WHERE numcgm = '.$valor['cgm_locatario'].'');
                    }
                    Sessao::write('arLocacoes',$arVeiculoLocacoes);
                    $stJs .= montaListaLocacoes($arVeiculoLocacoes);
                }
            }

            $obFormulario->montaInnerHTML();

            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);
            
            $stJs .= "$('spnOrigem').innerHTML = '".$obFormulario->getHTML()."'; ";
            $stJs .= "montaParametrosGET( 'preencheComboEntidade');";
            $stJs .= "jq('#inCodEntidade').val(".$_REQUEST['inCodEntidade'].");"; 
            $stJs .= "montaParametrosGET( 'MontaUnidade');";
            $stJs .= "$('hdnOrigem').value = '".$stEval."'; ";

            if ($_REQUEST['stOrigem'] == 'proprio') {
                $stJs .= "$('spnLocacao').innerHTML = '';";

                Sessao::remove('arLocacoes');
            }
            
        } else {
            $stJs .= "$('spnOrigem').innerHTML = '';";
            $stJs .= "$('hdnOrigem').value = ''; ";
        }
    break;

   case "MontaUnidade":
        $stJs .= "if(f.inCodUnidade){ limpaSelect(f.inCodUnidade,0); } \n";
        $stJs .= "jq('#inCodUnidadeTxt').value = ''; \n";
        $stJs .= "jq('#inCodUnidade').append( new Option('Selecione','', 'selected')) ;\n";

        if ($_REQUEST["inCodOrgao"]) {
            include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPAnuLiqEstLiq.class.php";
            $obREmpenhoRPAnuLiqEstLiq = new REmpenhoRelatorioRPAnuLiqEstLiq;
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inCodOrgao"]);
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setExercicio(Sessao::getExercicio() );
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("num_unidade");
                $stDesc = $rsCombo->getCampo("nom_unidade");
                $stJs .= "jQuery('#inCodUnidade').append( new Option('".$rsCombo->getCampo("nom_unidade")."','".$rsCombo->getCampo("num_unidade")."' )); \n";
                $rsCombo->proximo();
            }
        } 
        if($_REQUEST["hdnInCodUnidade"] !=""){
            $stJs.= "jQuery('#inCodUnidade').val(".$_REQUEST["hdnInCodUnidade"]."); ";
        }
        
    break;
    case 'preencheComboEntidade' :

        $stJs.= "if(d.getElementById('inCodEntidade')) { limpaSelect($('inCodEntidade'),1); } "; 

        if ($_REQUEST['stExercicioEntidade'] ){
            //cria o filtro
            $stFiltro = " AND E.exercicio = '".$_REQUEST['stExercicioEntidade']."' ";
            //recupera todos as entidades para o exercicio
            
            $obTOrcamentoEntidade = new TOrcamentoEntidade();
            $obTOrcamentoEntidade->recuperaRelacionamento( $rsEntidades, $stFiltro, ' ORDER BY cod_entidade ' );
            if ( $rsEntidades->getNumLinhas() > 0 ) {
                $inCount = 1;
                while ( !$rsEntidades->eof() ) {
                    if(( $_REQUEST['inCodEntidade'] == $rsEntidades->getCampo('cod_entidade') )){
                        $stSelected = 'true';
                    }else{
                        $stSelected = 'false';
                    }
                    $stJs .= "jq('#".inCodEntidade."').addOption('".$rsEntidades->getCampo('cod_entidade')."','".$rsEntidades->getCampo('cod_entidade')." - ".$rsEntidades->getCampo('nom_cgm')."',".$stSelected.");";
                    $rsEntidades->proximo();
                    $inCount++;
                }
                if($_REQUEST["hdnInCodEntidade"]&&$_REQUEST["hdnInCodEntidade"]!='')
                    $stJs.= "$('inCodEntidade').value = ".$_REQUEST["hdnInCodEntidade"]."; ";
            } else {
                $stJs.= "$('stExercicioEntidade').value = ''; ";
                $stJs.= "alertaAviso('Exercício sem entidades cadastradas.','form','erro','".Sessao::getId()."');";
            }
        }
    break;
    case 'buscaEmpenho':
        if ($_REQUEST["inCodigoEmpenho"] != '' && $_REQUEST["inCodEntidade"] != '' && $_REQUEST['stExercicioEmpenho'] != '') {

            $obREmpenhoEmpenho = new REmpenhoEmpenho;

            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade ( $_REQUEST["inCodEntidade"]  );
            $obREmpenhoEmpenho->setExercicio  ( $_REQUEST['stExercicioEmpenho'] );
            $obREmpenhoEmpenho->setCodEmpenhoInicial ( $_REQUEST["inCodigoEmpenho"] );
            $obREmpenhoEmpenho->setCodEmpenhoFinal ( $_REQUEST["inCodigoEmpenho"] );
            $obREmpenhoEmpenho->setSituacao ( 5 );

            $obREmpenhoEmpenho->listar($rsLista);

            if ($rsLista->getNumLinhas() > 0 ) {
                $obREmpenhoEmpenho->setCodEmpenho ( $_REQUEST["inCodigoEmpenho"] );
                $obREmpenhoEmpenho->consultar($boTransacao);
                $stNomFornecedor = ( $rsLista->getCampo( 'nom_fornecedor' ) ) ? str_replace( "'","\'",$rsLista->getCampo( "nom_fornecedor" )):'&nbsp;';
                $stJs .= "d.getElementById('stNomFornecedor').innerHTML='".$stNomFornecedor."';";
            } else {
                $stJs .= "$('inCodigoEmpenho').value='';";
                $stJs .= "$('stNomFornecedor').innerHTML = '&nbsp;';";
                $stJs .= "alertaAviso('Empenho informado está anulado ou não existe.','frm','erro','".Sessao::getId()."'); \n";
            }
        } else {
                $stJs .= "$('inCodigoEmpenho').value='';";
                $stJs .= "$('stNomFornecedor').innerHTML = '&nbsp;';";
        }
        break;
    case 'incluirDadosLocacao' :
        $stJs = isset($stJs) ? $stJs : null;

        if ($_REQUEST['stProcessoLocacao'] == '') {
                $stMensagem = 'Selecione um processo.';
        } 

        elseif ($_REQUEST['inNumEmpenhoLocacao'] == '') {
            $stMensagem = 'Informe o empenho da locação.';
        }

        elseif ($_REQUEST['stExercicioLocacao'] == '') {
            $stMensagem = 'Selecione o exercício da locação.';
        }

        elseif ( $_REQUEST['stExercicioLocacao'] != '' && $_REQUEST['stExercicioLocacao'] > Sessao::getExercicio() ) {
            $stMensagem = 'O exerício da locação deve ser menor ou igual ao ano atual.';
        }

        elseif ( $_REQUEST['stExercicioLocacao'] != '' && (substr($_REQUEST['dtContrato'],-4) > $_REQUEST['stExercicioLocacao'])) {
            $stMensagem = 'O exercicio deve ser igual ou maior que o ano de contrato.';
        }

        elseif ($_REQUEST['dtIniLocacao'] == '') {
            $stMensagem = 'Informe a data de início da locação.';
        } 

        elseif ($_REQUEST['dtIniLocacao'] != '' && SistemaLegado::comparaDatas('01/01/'.Sessao::getExercicio(), $_REQUEST['dtIniLocacao'],false) == 1) {
            $stMensagem = 'A data de início da locação não pode ser menor que o ano atual.';
        } 

        elseif ($_REQUEST['dtFimLocacao'] == '') {
            $stMensagem = 'Informe a data de término da locação.';
        } 

        elseif ($_REQUEST['dtFimLocacao'] != '' && SistemaLegado::comparaDatas($_REQUEST['dtIniLocacao'], $_REQUEST['dtFimLocacao'],true) == 1) {
            $stMensagem = 'A data de término da locação não pode ser menor ou igual que a data de início da locação.';
        }

        elseif ($_REQUEST['inCodEntidadeLocacao'] == '') {
            $stMensagem = 'Selecione a entidade para a locação.';
        }

        elseif ($_REQUEST['dtContrato'] == '') {
            $stMensagem = 'Informe a data do contrato de locação.';
        }

        elseif ($_REQUEST['dtContrato'] != '' && SistemaLegado::comparaDatas($_REQUEST['dtContrato'],$_REQUEST['dtIniLocacao'],false) == 1) {
            $stMensagem = 'A data do contrato deve ser igual ou inferior que a data de início.';
        }

        elseif ($_REQUEST['inCodLocatario'] == '') {
            $stMensagem = 'Selecione o locatário.';
        }

        elseif ($_REQUEST['inValorLocacao'] == '') {
            $stMensagem = 'Informe o valor da locação.';
        }

        if ( count( Sessao::read('arLocacoes') ) > 0 ) {
           foreach ( Sessao::read('arLocacoes') AS $arTemp ) {
                if ( ($arTemp['stProcessoLocacao'] == $_REQUEST['stProcessoLocacao'])
                     && ($arTemp['stExercicioLocacao'] == $_REQUEST['stExercicioLocacao'])
                     && ($arTemp['dtIniLocacao']."&&".$arTemp['dtFimLocacao'] == $_REQUEST['dtIniLocacao']."&&".$_REQUEST['dtFimLocacao'])
                     && ($arTemp['inCodEntidadeLocacao'] == $_REQUEST['inCodEntidadeLocacao'])
                     && ($arTemp['dtContrato'] == $_REQUEST['dtContrato'])
                     && ($arTemp['inCodLocatario'] == $_REQUEST['inCodLocatario'])
                     && ($arTemp['inValorLocacao'] == $_REQUEST['inValorLocacao'])
                     && ($arTemp['inNumEmpenhoLocacao'] == $_REQUEST['inNumEmpenhoLocacao'])
                   ) {
                    $stMensagem = 'Esta locação já está na lista.';
                    break;
                }

                if (SistemaLegado::comparaDatas($arTemp['dtIniLocacao'],$_REQUEST['dtIniLocacao'],true) == 1) {
                    $stMensagem = "A data de início deve ser posterior à ".$arTemp['dtIniLocacao']."";
                    break;
                } elseif (SistemaLegado::comparaDatas($arTemp['dtFimLocacao'],$_REQUEST['dtFimLocacao'],true) == 1) {
                    $stMensagem = "A data de término deve ser posterior à ".$arTemp['dtFimLocacao']."";
                    break;
                }
            }
        }

        if (!$stMensagem) {
            $arLocacoes = Sessao::read('arLocacoes');
            $inCount = count($arLocacoes);
            $arLocacoes[$inCount]['id']                   = $inCount + 1;
            $arLocacoes[$inCount]['id_locacao']           = $arLocacoes[$inCount]['id_locacao'];
            $arLocacoes[$inCount]['stProcessoLocacao']    = $_REQUEST['stProcessoLocacao'];
            $arLocacoes[$inCount]['stExercicioLocacao']   = $_REQUEST['stExercicioLocacao'];
            $arLocacoes[$inCount]['dtIniLocacao']         = $_REQUEST['dtIniLocacao'];
            $arLocacoes[$inCount]['dtFimLocacao']         = $_REQUEST['dtFimLocacao'];
            $arLocacoes[$inCount]['inCodEntidadeLocacao'] = $_REQUEST['inCodEntidadeLocacao'];
            $arLocacoes[$inCount]['dtContrato']           = $_REQUEST['dtContrato'];
            $arLocacoes[$inCount]['inCodLocatario']       = $_REQUEST['inCodLocatario'];
            $arLocacoes[$inCount]['inNumEmpenhoLocacao']  = $_REQUEST['inNumEmpenhoLocacao'];
            $arLocacoes[$inCount]['inValorLocacao']       = $_REQUEST['inValorLocacao'];
            $arLocacoes[$inCount]['stNomLocatario']       = $_REQUEST['stNomLocatario'];
            
            $stJs .= montaListaLocacoes( $arLocacoes );
            $stJs .= "jq('#stProcessoLocacao').val('');";
            $stJs .= "jq('#stExercicioLocacao').val('".Sessao::getExercicio()."');";
            $stJs .= "jq('#dtIniLocacao').val('');";
            $stJs .= "jq('#dtFimLocacao').val('');";
            $stJs .= "jq('#dtContrato').val('');";
            $stJs .= "jq('#inCodLocatario').val('');";
            $stJs .= "jq('#stNomLocatario').html('&nbsp;');";
            $stJs .= "jq('#inValorLocacao').val('');";
            $stJs .= "jq('#inCodEntidadeLocacao').val('');";
            $stJs .= "jq('#stNomEntidadeLocacao').val('');";
            $stJs .= "jq('#inNumEmpenhoLocacao').val('');";

            Sessao::write('arLocacoes' , $arLocacoes);
        } else {
            $stJs .= "alertaAviso('".$stMensagem."','frm','erro','".Sessao::getId()."'); \n";
        }
        break;

    case 'incluirDocumento' :
        $stJs = isset($stJs) ? $stJs : null;
        if ($_REQUEST['stDocumento'] == '') {
                $stMensagem = 'Selecione um documento.';
            } elseif ($_REQUEST['stExercicio'] == '') {
                $stMensagem = 'Selecione o ano de vencimento.';
            } elseif ( $_REQUEST['stExercicio'] != '' AND $_REQUEST['stExercicio'] > Sessao::getExercicio() ) {
                $stMensagem = 'O ano do vencimento deve ser menor ou igual ao ano atual.';
            } elseif ($_REQUEST['inMes'] == '') {
                $stMensagem = 'Selecione o mês de vencimento.';
            } elseif ($_REQUEST['stSituacao'] == '') {
                $stMensagem = 'Selecione a situação do documento.';
            } elseif ($_REQUEST['stSituacao'] == 'pago') {
                if ($_REQUEST['stExercicioEmpenho'] == '') {
                    $stMensagem = 'Informe o exercícío do empenho.';
                } elseif ($_REQUEST['inCodEntidadeOculto'] == '') {
                    $stMensagem = 'Informe a entidade do empenho.';
                }
                if ($_REQUEST['inCodigoEmpenho'] == '') {
                    $stMensagem = 'Informe o código do empenho.';
                }
            }
        if ( count( Sessao::read('arDocumentos') ) > 0 ) {
           foreach ( Sessao::read('arDocumentos') AS $arTemp ) {
                if ( ($arTemp['cod_documento'] == $_REQUEST['stDocumento']) AND ($arTemp['ano_documento'] == $_REQUEST['stExercicio']) ) {
                    $stMensagem = 'Este documento já está na lista.';
                    break;
                }
            }
        }
        if (!$stMensagem) {

            $arDocumentos = Sessao::read('arDocumentos');
            $inCount = count($arDocumentos);
            $arDocumentos[$inCount]['id']            = $inCount + 1;
            $arDocumentos[$inCount]['cod_documento'] = $_REQUEST['stDocumento'];
            $arDocumentos[$inCount]['nom_documento'] = sistemaLegado::pegaDado( 'nom_documento', 'frota.documento', ' WHERE cod_documento = '.$_REQUEST['stDocumento'].' ' );
            $arDocumentos[$inCount]['mes']           = $_REQUEST['inMes'];
            $arDocumentos[$inCount]['ano_documento'] = $_REQUEST['stExercicio'];
            $arDocumentos[$inCount]['desc_mes']      = strftime('%b',mktime(0,0,0,$_REQUEST['inMes'],1,$_REQUEST['stExercicio']));
            $arDocumentos[$inCount]['situacao']      = ($_REQUEST['stSituacao'] == 'pago') ? true : false;
            $arDocumentos[$inCount]['desc_situacao'] = ($_REQUEST['stSituacao'] == 'pago') ? 'Pago' : 'Não Pago';
            if ($_REQUEST['stSituacao'] == 'pago') {
                $arDocumentos[$inCount]['exercicio_empenho']    = $_REQUEST['stExercicioEmpenho'];
                $arDocumentos[$inCount]['cod_entidade'] = $_REQUEST['inCodEntidadeOculto'];
                $arDocumentos[$inCount]['cod_empenho']  = $_REQUEST['inCodigoEmpenho'];
            }
            $stJs .= montaListaDocumentos( $arDocumentos );
            $stJs .= "$('stDocumento').selectedIndex = 0;";
            $stJs .= "$('stExercicio').value = '".Sessao::getExercicio()."';";
            $stJs .= "$('inMes').selectedIndex = 0;";
            $stJs .= "$('stSituacao1').checked = false;";
            $stJs .= "$('stSituacao2').checked = false;";
            $stJs .= "$('spnEmpenho').innerHTML = ''; ";

            Sessao::write('arDocumentos' , $arDocumentos);
        } else {
            $stJs .= "alertaAviso('".$stMensagem."','frm','erro','".Sessao::getId()."'); \n";
        }
    break;

    case 'incluirDadosCessao' :
        $stJs = isset($stJs) ? $stJs : null;

        if ($_REQUEST['stProcessoCessao'] == '') {
            $stMensagem = 'Informe o processo!';
        } elseif ($_REQUEST['inCodCedente'] == '') {
            $stMensagem = 'Informe o CGM cedente da cessão!';
        } elseif ($_REQUEST['dtInicioCessao'] == '') {
            $stMensagem = 'Informe a data de início da cessão!';
        } elseif ($_REQUEST['dtTerminoCessao'] == '') {
            $stMensagem = 'Informe a data de término da cessão!';
        } elseif ( SistemaLegado::comparaDatas($_REQUEST['dtInicioCessao'],$_REQUEST['dtTerminoCessao'],true) == true ) {
            $stMensagem = 'O início da cessão não pode ser maior ou igual ao término da cessão!';
        }

        if ( count( Sessao::read('arListaCessao') ) > 0 ) {
           foreach ( Sessao::read('arListaCessao') AS $arTemp ) {
                if ( (str_pad($arTemp['cod_processo'],5,'0',STR_PAD_LEFT)."/".$arTemp['exercicio'] == $_REQUEST['stProcessoCessao']) &&
                     ($arTemp['cgm_cedente'] == $_REQUEST['inCodCedente'])                                                           &&
                     ($arTemp['nom_cedente'] == $_REQUEST['stNomCedente'])                                                           &&
                     ($arTemp['dt_inicio']   == $_REQUEST['dtInicioCessao'])                                                         &&
                     ($arTemp['dt_termino']  == $_REQUEST['dtTerminoCessao']) 
                    ) {
                    $stMensagem = 'Esta cessão já está na lista.';
                    break;
                }

                if ( $arTemp['dt_inicio'] == $_REQUEST['dtInicioCessao'] ) {
                    $stMensagem = "O início da cessão não pode ser igual a ".$arTemp['dt_inicio']."";
                    break;
                } elseif ( $arTemp['dt_termino'] == $_REQUEST['dtTerminoCessao'] ) {
                    $stMensagem = "O término da cessão não pode ser igual a ".$arTemp['dt_termino']."";
                    break;
                } elseif ( (SistemaLegado::comparaDatas($_REQUEST['dtInicioCessao'],$arTemp['dt_inicio'],false) == true) &&
                           (SistemaLegado::comparaDatas($_REQUEST['dtInicioCessao'],$arTemp['dt_termino'],false) == false)
                         ) {
                    $stMensagem = "O início da cessão não pode estar entre ".$arTemp['dt_inicio']." e ".$arTemp['dt_termino']."";
                    break;
                } else if ((SistemaLegado::comparaDatas($_REQUEST['dtInicioCessao'],$arTemp['dt_inicio'],true) == false) &&
                           (SistemaLegado::comparaDatas($_REQUEST['dtTerminoCessao'],$_REQUEST['dtInicioCessao'],true) == true)
                          ) {
                    $stMensagem = "O término da cessão não pode ser igual ou maior que ".$arTemp['dt_inicio']."";
                    break;
                }
            }
        }

        if (!$stMensagem) {
            $arListaCessao = Sessao::read('arListaCessao');
            $inCount = count($arListaCessao);

            $arListaCessao[$inCount]['id']            = $inCount + 1;
            $arListaCessao[$inCount]['id_cessao']     = $arListaCessao[$inCount]['id_cessao'];
            $arListaCessao[$inCount]['cod_processo']  = substr($_REQUEST['stProcessoCessao'], 0, 5);
            $arListaCessao[$inCount]['exercicio']     = substr($_REQUEST['stProcessoCessao'], 6, 4);
            $arListaCessao[$inCount]['cgm_cedente']   = $_REQUEST['inCodCedente'];
            $arListaCessao[$inCount]['nom_cedente']   = $_REQUEST['stNomCedente'];
            $arListaCessao[$inCount]['dt_inicio']     = $_REQUEST['dtInicioCessao'];
            $arListaCessao[$inCount]['dt_termino']    = $_REQUEST['dtTerminoCessao'];

            $stJs .= montaListaCessoes( $arListaCessao );
            $stJs .= "jq('#hdnIdCessao').val('');";
            $stJs .= "jq('#stProcessoCessao').val('');";
            $stJs .= "jq('#inCodCedente').val('');";
            $stJs .= "jq('#stNomCedente').html('&nbsp;');";
            $stJs .= "jq('#dtInicioCessao').val('');";
            $stJs .= "jq('#dtTerminoCessao').val('');";

            Sessao::write('arListaCessao' , $arListaCessao);
        } else {
            $stJs .= "alertaAviso('".$stMensagem."','frm','erro','".Sessao::getId()."'); \n";
        }
    break;

    case 'montaAlteracaoDocumento' :

        $arDocumentos = Sessao::read('arDocumentos');
        $inCount = $_REQUEST['id'];
        $inCount = $inCount - 1;

        $stJs .= "$('hdnId').value = '".$_REQUEST['id']."';";
        $stJs .= "$('stDocumento').selectedIndex = '".$arDocumentos[$inCount]['cod_documento']."';";
        $stJs .= "$('stExercicio').value = '".$arDocumentos[$inCount]['ano_documento']."';";
        $stJs .= "$('inMes').selectedIndex = '".$arDocumentos[$inCount]['mes']."';";
        
        if ($arDocumentos[$inCount]['situacao']) {
            $stJs .= "$('stSituacao1').checked = true;";
            $stJs .= "$('spnEmpenho').innerHTML = '".montaEmpenho()."';";
            $stJs .= "$('stExercicioEmpenho').value = '".$arDocumentos[$inCount]['exercicio_empenho']."';";
            $stJs .= "$('inCodEntidadeOculto').value = '".$arDocumentos[$inCount]['cod_entidade']."';";
            $stJs .= "$('stNomEntidade').selectedIndex = '".$arDocumentos[$inCount]['cod_entidade']."';";
            $stJs .= "$('inCodigoEmpenho').value = '".$arDocumentos[$inCount]['cod_empenho']."';";
        } else {
            $stJs .= "$('stSituacao2').checked = true;";
        }
        $stJs .= "$('incluiDocumento').value = 'Alterar';";
        $stJs .= "$('incluiDocumento').setAttribute( 'onclick','montaParametrosGET(\'alterarDocumento\',\'stDocumento,stExercicio,inMes,stSituacao,stExercicioEmpenho,inCodEntidadeOculto,inCodigoEmpenho,stNomFornecedor,hdnId\');');";
        break;

    case 'montaAlteracaoLocacoes' :
        $arLocacoes = Sessao::read('arLocacoes');
        $inCount = $_REQUEST['id'];
        $inCount = $inCount - 1;

        $stJs .= "jq('#hdnIdLocacao').val ('".$_REQUEST['id']."');";
        $stJs .= "jq('#stProcessoLocacao').val ('".$arLocacoes[$inCount]['stProcessoLocacao']."');";
        $stJs .= "jq('#stExercicioLocacao').val ('".$arLocacoes[$inCount]['stExercicioLocacao']."');";
        $stJs .= "jq('#dtIniLocacao').val ('".$arLocacoes[$inCount]['dtIniLocacao']."');";
        $stJs .= "jq('#dtFimLocacao').val ('".$arLocacoes[$inCount]['dtFimLocacao']."');";
        $stJs .= "jq('#dtContrato').val ('".$arLocacoes[$inCount]['dtContrato']."');";
        $stJs .= "jq('#inCodEntidadeLocacao').val ('".$arLocacoes[$inCount]['inCodEntidadeLocacao']."');";
        $stJs .= "jq('#inCodLocatario').val ('".$arLocacoes[$inCount]['inCodLocatario']."');";
        $stJs .= "jq('#stNomLocatario').html ('".$arLocacoes[$inCount]['stNomLocatario']."');";
        $stJs .= "jq('#inNumEmpenhoLocacao').val ('".$arLocacoes[$inCount]['inNumEmpenhoLocacao']."');";
        $stJs .= "jq('#inValorLocacao').val ('".$arLocacoes[$inCount]['inValorLocacao']."');";
        $stJs .= "jq('#stNomEntidadeLocacao').val ('".$arLocacoes[$inCount]['inCodEntidadeLocacao']."');";

        $stJs .= "$('incluiDadosLocacao').value = 'Alterar';";
        $stJs .= "$('incluiDadosLocacao').setAttribute( 'onclick','montaParametrosGET(\'alterarLocacao\',\'hdnIdLocacao,stProcessoLocacao,stExercicioLocacao,dtIniLocacao,dtFimLocacao,dtContrato,inCodEntidadeLocacao,inCodLocatario,stNomLocatario,inNumEmpenhoLocacao,inValorLocacao\');');";
        $stJs .= "jq('#incluiDadosLocacao').focus();";
        break;

    case 'montaAlterarCessao' :
        $arCessoes = Sessao::read('arListaCessao');
        $inCount = $_REQUEST['id'];
        $inCount = $inCount - 1;

        $stJs .= "jq('#hdnIdCessao').val ('".$_REQUEST['id']."');";
        $stJs .= "jq('#stProcessoCessao').val ('".$arCessoes[$inCount]['cod_processo']."/".$arCessoes[$inCount]['exercicio']."');";
        $stJs .= "jq('#inCodCedente').val (".$arCessoes[$inCount]['cgm_cedente'].");";
        $stJs .= "jq('#stNomCedente').html ('".$arCessoes[$inCount]['nom_cedente']."');";
        $stJs .= "jq('#dtInicioCessao').val ('".$arCessoes[$inCount]['dt_inicio']."');";
        $stJs .= "jq('#dtTerminoCessao').val ('".$arCessoes[$inCount]['dt_termino']."');";

        $stJs .= "$('incluiDadosCessao').value = 'Alterar';";
        $stJs .= "$('incluiDadosCessao').setAttribute( 'onclick','montaParametrosGET(\'alterarCessao\',\'hdnIdCessao,stProcessoCessao,inCodCedente,stNomCedente,dtInicioCessao,dtTerminoCessao\');');";
        $stJs .= "jq('#incluiDadosCessao').focus();";
    break;

    case 'alterarDocumento' :
        if ($_REQUEST['stDocumento'] == '') {
            $stMensagem = 'Selecione um documento.';
        } elseif ($_REQUEST['stExercicio'] == '') {
            $stMensagem = 'Selecione o ano de vencimento.';
        } elseif ($_REQUEST['inMes'] == '') {
            $stMensagem = 'Selecione o mes de vencimento.';
        } elseif ($_REQUEST['stSituacao'] == '') {
            $stMensagem = 'Selecione a situação do documento.';
        } elseif ($_REQUEST['stSituacao'] == 'pago') {
            if ($_REQUEST['stExercicio'] == '') {
                $stMensagem = 'Informe o exercícío do empenho.';
            } elseif ($_REQUEST['inCodEntidadeOculto'] == '') {
                $stMensagem = 'Informe a entidade do empenho.';
            }
            if ($_REQUEST['inCodigoEmpenho'] == '') {
                $stMensagem = 'Informe o código do empenho.';
            }
        }
        
        if ( count( Sessao::read('arDocumentos') ) > 0 ) {
            foreach ( Sessao::read('arDocumentos') AS $arTemp ) {
                if ($arTemp['cod_documento'] == $_REQUEST['stDocumento'] AND $arTemp['ano_documento'] == $_REQUEST['stExercicio'] AND $arTemp['id'] != $_REQUEST['hdnId']) {
                    $stMensagem = 'Este documento já está na lista.';
                    break;
                }
            }
        }
        if (!$stMensagem) {

            $arDocumentos = Sessao::read('arDocumentos');

            $inCount = $_REQUEST['hdnId'];
            $inCount = $inCount - 1;
            $arDocumentos[$inCount]['id']            = $_REQUEST['hdnId'];
            $arDocumentos[$inCount]['cod_documento'] = $_REQUEST['stDocumento'];
            $arDocumentos[$inCount]['nom_documento'] = sistemaLegado::pegaDado( 'nom_documento', 'frota.documento', ' WHERE cod_documento = '.$_REQUEST['stDocumento'].' ' );
            $arDocumentos[$inCount]['ano_documento'] = $_REQUEST['stExercicio'];
            $arDocumentos[$inCount]['mes']           = $_REQUEST['inMes'];
            $arDocumentos[$inCount]['desc_mes']      = strftime('%b',mktime(0,0,0,$_REQUEST['inMes'],1,$_REQUEST['stExercicio']));
            $arDocumentos[$inCount]['situacao']      = ($_REQUEST['stSituacao'] == 'pago') ? true : false;
            $arDocumentos[$inCount]['desc_situacao'] = ($_REQUEST['stSituacao'] == 'pago') ? 'Pago' : 'Não Pago';
            
            if ($_REQUEST['stSituacao'] == 'pago') {
                $arDocumentos[$inCount]['exercicio_empenho'] = $_REQUEST['stExercicioEmpenho'];
                $arDocumentos[$inCount]['cod_entidade'] = $_REQUEST['inCodEntidadeOculto'];
                $arDocumentos[$inCount]['cod_empenho']  = $_REQUEST['inCodigoEmpenho'];
                //$arDocumentos[$inCount]['nom_empenho']  = $rsLista->getCampo('nom_fornecedor');
            }else{
                $arDocumentos[$inCount]['cod_empenho']  = null;
            }
            $stJs .= montaListaDocumentos( $arDocumentos );
            $stJs .= "$('hdnId').value = '';";
            $stJs .= "$('stDocumento').selectedIndex = 0;";
            $stJs .= "$('stExercicio').value = '".Sessao::getExercicio()."';";
            $stJs .= "$('inMes').selectedIndex = 0;";
            $stJs .= "$('stSituacao1').checked = false;";
            $stJs .= "$('stSituacao2').checked = false;";
            $stJs .= "$('incluiDocumento').value = 'Incluir';";
            $stJs .= "$('incluiDocumento').setAttribute( 'onclick','montaParametrosGET(\'incluirDocumento\',\'stDocumento,stProcessoLocacao,stExercicio,inMes,stSituacao,stExercicioEmpenho,inCodEntidadeOculto,inCodigoEmpenho\');');";
            //$stJs .= "$('incluiDocumento').setAttribute( 'onclick','montaParametrosGET(\'incluirDocumento\',\'stDocumento,stExercicio,inMes,stSituacao,stExercicioEmpenho,inCodEntidade,inCodigoEmpenho,stNomFornecedor\');');";
            $stJs .= "$('spnEmpenho').innerHTML = ''; ";

            Sessao::write('arDocumentos' , $arDocumentos);

            //se estivesse excluido, remove das excluidas
            if ( count( Sessao::read('arDocumentosExcluidos') ) > 0 ) {
                foreach ( Sessao::read('arDocumentosExcluidos') AS $arTemp ) {
                    if ($arTemp['cod_documento'] != $_REQUEST['stDocumento'] OR $arTemp['ano_exercicio'] != $_REQUEST['stExercicio']) {
                        $arAux[] = $arTemp;
                    }
                }
            }
            Sessao::write('arDocumentosExcluidos' , $arAux);
        } else {
            $stJs .= "alertaAviso('".$stMensagem."','frm','erro','".Sessao::getId()."'); \n";
        }

        break;

    case 'alterarLocacao' :
       if ($_REQUEST['stProcessoLocacao'] == '') {
            $stMensagem = 'Selecione um processo.';
        } 

        elseif ($_REQUEST['inNumEmpenhoLocacao'] == '') {
            $stMensagem = 'Informe o empenho da locação.';
        }
    
        elseif ($_REQUEST['stExercicioLocacao'] == '') {
            $stMensagem = 'Selecione o exercício da locação.';
        }

        elseif ( $_REQUEST['stExercicioLocacao'] != '' && $_REQUEST['stExercicioLocacao'] > Sessao::getExercicio() ) {
            $stMensagem = 'O exerício da locação deve ser menor ou igual ao ano atual.';
        }

        elseif ( $_REQUEST['stExercicioLocacao'] != '' && (substr($_REQUEST['dtContrato'],-4) > $_REQUEST['stExercicioLocacao'])) {
            $stMensagem = 'O exercicio deve ser igual ou maior que o ano de contrato.';
        }
    
        elseif ($_REQUEST['dtIniLocacao'] == '') {
            $stMensagem = 'Informe a data de início da locação.';
        } 
    
        elseif ($_REQUEST['dtIniLocacao'] != '' && SistemaLegado::comparadatas('01/01/'.Sessao::getExercicio(), $_REQUEST['dtIniLocacao'],false) == 1) {
            $stMensagem = 'A data de início da locação não pode ser menor que o ano atual.';
        } 

        elseif ($_REQUEST['dtFimLocacao'] == '') {
            $stMensagem = 'Informe a data de término da locação.';
        } 

        elseif ($_REQUEST['dtFimLocacao'] != '' && SistemaLegado::comparadatas($_REQUEST['dtIniLocacao'], $_REQUEST['dtFimLocacao'],true) == 1) {
            $stMensagem = 'A data de término da locação não pode ser menor ou igual que a data de início da locação.';
        }

        elseif ($_REQUEST['inCodEntidadeLocacao'] == '') {
            $stMensagem = 'Selecione a entidade para a locação.';
        }

        elseif ($_REQUEST['dtContrato'] == '') {
            $stMensagem = 'Informe a data do contrato de locação.';
        }

        elseif ($_REQUEST['dtContrato'] != '' && SistemaLegado::comparadatas($_REQUEST['dtContrato'],$_REQUEST['dtIniLocacao'],false) == 1) {
            $stMensagem = 'A data do contrato deve ser igual ou inferior que a data de início.';
        }

        elseif ($_REQUEST['inCodLocatario'] == '') {
            $stMensagem = 'Selecione o locatário.';
        }

        elseif ($_REQUEST['inValorLocacao'] == '') {
            $stMensagem = 'Informe o valor da locação.';
        }

        if (!$stMensagem) {
            $arLocacoes = Sessao::read('arLocacoes');

            if ($_REQUEST['stNomLocatario'] == '') {
                $nomLocatario = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm', ' WHERE numcgm = '.$_REQUEST['inCodLocatario']);
            } else {
                $nomLocatario = $_REQUEST['stNomLocatario'];
            }

            $inCount = $_REQUEST['hdnIdLocacao'];
            $inCount = $inCount - 1;
            $arLocacoes[$inCount]['id']                   = $_REQUEST['hdnIdLocacao'];
            $arLocacoes[$inCount]['id_locacao']           = $arLocacoes[$inCount]['id_locacao'];
            $arLocacoes[$inCount]['stProcessoLocacao']    = $_REQUEST['stProcessoLocacao'];
            $arLocacoes[$inCount]['stExercicioLocacao']   = $_REQUEST['stExercicioLocacao'];
            $arLocacoes[$inCount]['dtIniLocacao']         = $_REQUEST['dtIniLocacao'];
            $arLocacoes[$inCount]['dtFimLocacao']         = $_REQUEST['dtFimLocacao'];
            $arLocacoes[$inCount]['inCodEntidadeLocacao'] = $_REQUEST['inCodEntidadeLocacao'];
            $arLocacoes[$inCount]['dtContrato']           = $_REQUEST['dtContrato'];
            $arLocacoes[$inCount]['inCodLocatario']       = $_REQUEST['inCodLocatario'];
            $arLocacoes[$inCount]['inNumEmpenhoLocacao']  = $_REQUEST['inNumEmpenhoLocacao'];
            $arLocacoes[$inCount]['inValorLocacao']       = $_REQUEST['inValorLocacao'];
            $arLocacoes[$inCount]['stNomLocatario']       = $nomLocatario;

            $stJs .= montaListaLocacoes( $arLocacoes );
            $stJs .= "jq('#hdnId').val ('');";
            $stJs .= "jq('#stProcessoLocacao').val('');";
            $stJs .= "jq('#stExercicioLocacao').val('".Sessao::getExercicio()."');";
            $stJs .= "jq('#dtIniLocacao').val('');";
            $stJs .= "jq('#dtFimLocacao').val('');";
            $stJs .= "jq('#dtContrato').val('');";
            $stJs .= "jq('#inCodLocatario').val('');";
            $stJs .= "jq('#stNomLocatario').html('&nbsp;');";
            $stJs .= "jq('#inValorLocacao').val('');";
            $stJs .= "jq('#inCodEntidadeLocacao').val('');";
            $stJs .= "jq('#stNomEntidadeLocacao').val('');";
            $stJs .= "jq('#inNumEmpenhoLocacao').val('');";

            $stJs .= "$('incluiDadosLocacao').value = 'Incluir';";
            $stJs .= "$('incluiDadosLocacao').setAttribute( 'onclick','montaParametrosGET(\'incluirDadosLocacao\',\'st\,stExercicioLocacao,stProcessoLocacao,dtIniLocacao,dtFimLocacao,dtContrato,inCodLocatario,stNomLocatario,inValorLocacao,inCodEntidadeLocacao,stNomEntidadeLocacao,inNumEmpenhoLocacao\');');";
            Sessao::write('arLocacoes' , $arLocacoes);

            //se estivesse excluido, remove das excluidas
            if ( count( Sessao::read('arLocacoesExcluidas') ) > 0 ) {
                foreach ( Sessao::read('arLocacoesExcluidas') AS $arTemp ) {
                    if (($arTemp['stProcessoLocacao']    != $_REQUEST['stProcessoLocacao']   ) &&
                        ($arTemp['stExercicioLocacao']   != $_REQUEST['stExercicioLocacao']  ) &&
                        ($arTemp['dtIniLocacao']         != $_REQUEST['dtIniLocacao']        ) &&
                        ($arTemp['dtFimLocacao']         != $_REQUEST['dtFimLocacao']        ) &&
                        ($arTemp['inCodEntidadeLocacao'] != $_REQUEST['inCodEntidadeLocacao']) &&
                        ($arTemp['dtContrato']           != $_REQUEST['dtContrato']          ) &&
                        ($arTemp['inCodLocatario']       != $_REQUEST['inCodLocatario']      ) &&
                        ($arTemp['inNumEmpenhoLocacao']  != $_REQUEST['inNumEmpenhoLocacao'] ) &&
                        ($arTemp['inValorLocacao']       != $_REQUEST['inValorLocacao']      ) &&
                        ($arTemp['stNomLocatario']       != $_REQUEST['stNomLocatario']      )
                       ) {
                        $arAux[] = $arTemp;
                    }
                }
            }

            Sessao::write('arLocacoesExcluidas' , $arAux);
        } else {
            $stJs .= "alertaAviso('".$stMensagem."','frm','erro','".Sessao::getId()."'); \n";
        }

    break;

    case 'alterarCessao' :
        if ($_REQUEST['stProcessoCessao'] == '') {
            $stMensagem = 'Informe o processo!';
        } elseif ($_REQUEST['inCodCedente'] == '') {
            $stMensagem = 'Informe o CGM cedente da cessão!';
        } elseif ($_REQUEST['dtInicioCessao'] == '') {
            $stMensagem = 'Informe a data de início da cessão!';
        } elseif ($_REQUEST['dtTerminoCessao'] == '') {
            $stMensagem = 'Informe a data de término da cessão!';
        } elseif ( SistemaLegado::comparaDatas($_REQUEST['dtInicioCessao'],$_REQUEST['dtTerminoCessao'],true) ) {
            $stMensagem = 'A data de início não pode ser maior ou igual à data de término da cessão!';
        }

        if (!$stMensagem) {
            $arCessoes = Sessao::read('arListaCessao');

            $inCount = $_REQUEST['hdnIdCessao'];
            $inCount = $inCount - 1;
            $arCessoes[$inCount]['id']              = $_REQUEST['hdnIdCessao'];
            $arCessoes[$inCount]['id_cessao']       = $arCessoes[$inCount]['id_cessao'];
            $arCessoes[$inCount]['cod_processo']    = substr($_REQUEST['stProcessoCessao'], 0, 5);
            $arCessoes[$inCount]['exercicio']       = substr($_REQUEST['stProcessoCessao'], 6, 4);
            $arCessoes[$inCount]['cgm_cedente']     = $_REQUEST['inCodCedente'];
            $arCessoes[$inCount]['nom_cedente']     = $_REQUEST['stNomCedente'];
            $arCessoes[$inCount]['dt_inicio']       = $_REQUEST['dtInicioCessao'];
            $arCessoes[$inCount]['dt_termino']      = $_REQUEST['dtTerminoCessao'];

            $stJs .= montaListaCessoes( $arCessoes );
            $stJs .= "jq('#hdnIdCessao').val ('');";
            $stJs .= "jq('#stProcessoCessao').val('');";
            $stJs .= "jq('#dtInicioCessao').val('');";
            $stJs .= "jq('#dtTerminoCessao').val('');";
            $stJs .= "jq('#inCodCedente').val('');";
            $stJs .= "jq('#stNomCedente').html('&nbsp;');";

            $stJs .= "$('incluiDadosCessao').value = 'Incluir';";
            $stJs .= "$('incluiDadosCessao').setAttribute( 'onclick','montaParametrosGET(\'incluirDadosCessao\',\'st\,stProcessoCessao,dtInicioCessao,dtTerminoCessao,inCodCedente,stNomCedente\');');";

            Sessao::write('arListaCessao' , $arCessoes);

            //se estivesse excluido, remove das excluidas
            if ( count( Sessao::read('arCessoesExcluidas') ) > 0 ) {
                foreach ( Sessao::read('arCessoesExcluidas') AS $arTemp ) {
                    if (($arTemp['cod_processo']    != substr($_REQUEST['stProcessoCessao'], 0, 5)) &&
                        ($arTemp['exercicio']       != substr($_REQUEST['stProcessoCessao'], 6, 4)) &&
                        ($arTemp['dt_inicio']       != $_REQUEST['dtInicioCessao']                ) &&
                        ($arTemp['dt_termino']      != $_REQUEST['dtTerminoCessao']               ) &&
                        ($arTemp['cgm_cedente']     != $_REQUEST['inCodCedente']                  ) &&
                        ($arTemp['nom_cedente']     != $_REQUEST['stNomCedente']                  )
                       ) {
                        $arAux[] = $arTemp;
                    }
                }
            }

            Sessao::write('arCessoesExcluidas' , $arAux);
        } else {
            $stJs .= "alertaAviso('".$stMensagem."','frm','erro','".Sessao::getId()."'); \n";
        }

    break;

    case 'excluirDocumento' :
        $arAux = array();
        $arDocumentosExcluidos = Sessao::read('arDocumentosExcluidos');
        foreach ( Sessao::read('arDocumentos') AS $arTemp ) {
            if ($arTemp['id'] !=  $_REQUEST['id']) {
                $arAux[] = $arTemp;
            } else {
                $inCount = count($arDocumentosExcluidos);
                $arDocumentosExcluidos[$inCount]['cod_documento'] = $arTemp['cod_documento'];
                $arDocumentosExcluidos[$inCount]['ano_documento'] = $arTemp['ano_documento'];
            }
        }
        Sessao::write('arDocumentosExcluidos' , $arDocumentosExcluidos);
        Sessao::write('arDocumentos' , $arAux);
        $stJs .= montaListaDocumentos( Sessao::read('arDocumentos') );
        break;

    case 'excluirLocacoes' :
        $arAux = array();
        $arLocacoesExcluidas = Sessao::read('arLocacoesExcluidas');

        foreach ( Sessao::read('arLocacoes') AS $arTemp ) {
            if ($arTemp['id'] !=  $_REQUEST['id']) {
                $arAux[] = $arTemp;
            } else {
                $inCount = count($arLocacoesExcluidas);
                $arLocacoesExcluidas[$inCount]['id_locacao']           = $arTemp['id_locacao'];
                $arLocacoesExcluidas[$inCount]['stProcessoLocacao']    = $arTemp['stProcessoLocacao'];
                $arLocacoesExcluidas[$inCount]['stExercicioLocacao']   = $arTemp['stExercicioLocacao'];
                $arLocacoesExcluidas[$inCount]['dtIniLocacao']         = $arTemp['dtIniLocacao'];
                $arLocacoesExcluidas[$inCount]['dtFimLocacao']         = $arTemp['dtFimLocacao'];
                $arLocacoesExcluidas[$inCount]['inCodEntidadeLocacao'] = $arTemp['inCodEntidadeLocacao'];
                $arLocacoesExcluidas[$inCount]['dtContrato']           = $arTemp['dtContrato'];
                $arLocacoesExcluidas[$inCount]['inCodLocatario']       = $arTemp['inCodLocatario'];
                $arLocacoesExcluidas[$inCount]['inNumEmpenhoLocacao']  = $arTemp['inNumEmpenhoLocacao'];
                $arLocacoesExcluidas[$inCount]['inValorLocacao']       = $arTemp['inValorLocacao'];
                $arLocacoesExcluidas[$inCount]['stNomLocatario']       = $arTemp['stNomLocatario'];
            }
        }
        Sessao::write('arLocacoesExcluidas' , $arLocacoesExcluidas);
        Sessao::write('arLocacoes' , $arAux);
        $stJs .= montaListaLocacoes( Sessao::read('arLocacoes') );
        $stJs .= "jq('#inCodResponsavel').focus();";
    break;

    case 'excluirCessao' :
        $arAux = array();
        $arCessoesExcluidas = Sessao::read('arCessoesExcluidas');

        foreach ( Sessao::read('arListaCessao') AS $arTemp ) {
            if ($arTemp['id'] !=  $_REQUEST['id']) {
                $arAux[] = $arTemp;
            } else {
                $inCount = count($arCessoesExcluidas);
                $arCessoesExcluidas[$inCount]['id_cessao']      = $arTemp['id_cessao'];
                $arCessoesExcluidas[$inCount]['cod_processo']   = $arTemp['cod_processo'];
                $arCessoesExcluidas[$inCount]['exercicio']      = $arTemp['exercicio'];
                $arCessoesExcluidas[$inCount]['cgm_cedente']    = $arTemp['cgm_cedente'];
                $arCessoesExcluidas[$inCount]['nom_cedente']    = $arTemp['nom_cedente'];
                $arCessoesExcluidas[$inCount]['dt_inicio']      = $arTemp['dt_inicio'];
                $arCessoesExcluidas[$inCount]['dt_termino']     = $arTemp['dt_termino'];
            }
        }
        Sessao::write('arCessoesExcluidas' , $arCessoesExcluidas);
        Sessao::write('arListaCessao' , $arAux);
        $stJs .= montaListaCessoes( Sessao::read('arListaCessao') );
        $stJs .= "jq('#stDocumento').focus();";
    break;

    case 'limparDocumentos' :

        $stJs .= "$('hdnId').value = '';";
        $stJs .= "$('stDocumento').selectedIndex = 0;";
        $stJs .= "$('stExercicio').value = '".Sessao::getExercicio()."';";
        $stJs .= "$('inMes').selectedIndex = 0;";
        $stJs .= "$('stSituacao1').checked = false;";
        $stJs .= "$('stSituacao2').checked = false;";
        $stJs .= "$('incluiDocumento').value = 'Incluir';";
        $stJs .= "$('incluiDocumento').setAttribute( 'onclick','montaParametrosGET(\'incluirDocumento\',\'stDocumento,stExercicio,inMes,stSituacao,stExercicioEmpenho,inCodEntidadeOculto,inCodigoEmpenho\');');";
        $stJs .= "$('spnEmpenho').innerHTML = ''; ";

        break;

    case 'limparDadosLocacao' :

        $stJs .= "jq('#hdnId').val('');";
        $stJs .= "jq('#stProcessoLocacao').val ('');";
        $stJs .= "jq('#stExercicioLocacao').val ('".Sessao::getExercicio()."');";
        $stJs .= "jq('#dtIniLocacao').val ('');";
        $stJs .= "jq('#dtFimLocacao').val ('');";
        $stJs .= "jq('#dtContrato').val ('');";
        $stJs .= "jq('#inCodLocatario').val ('');";
        $stJs .= "jq('#stNomLocatario').html ('');";
        $stJs .= "jq('#inNumEmpenhoLocacao').val ('');";
        $stJs .= "jq('#inValorLocacao').val ('');";
        $stJs .= "jq('#inCodEntidadeLocacao').val ('');";
        $stJs .= "jq('#stNomEntidadeLocacao').html ('');";

        $stJs .= "$('incluiDadosLocacao').value = 'Incluir';";
        $stJs .= "$('incluiDadosLocacao').setAttribute( 'onclick','montaParametrosGET(\'incluirDadosLocacao\',\'hdnId,stProcessoLocacao,stExercicioLocacao,dtIniLocacao,dtFimLocacao,dtContrato,inCodEntidadeLocacao,inCodLocatario,stNomLocatario,inNumEmpenhoLocacao,inValorLocacao\');');";

    break;

    case 'limparDadosCessao' :

        $stJs .= "jq('#hdnIdCessao').val('');";
        $stJs .= "jq('#stProcessoCessao').val ('');";
        $stJs .= "jq('#dtInicioCessao').val ('');";
        $stJs .= "jq('#dtTerminoCessao').val ('');";
        $stJs .= "jq('#inCodCedente').val ('');";
        $stJs .= "jq('#stNomCedente').html ('');";

        $stJs .= "$('incluiDadosCessao').value = 'Incluir';";
        $stJs .= "$('incluiDadosCessao').setAttribute( 'onclick','montaParametrosGET(\'incluirDadosCessao\',\'hdnIdCessao,stProcessoCessao,inCodCedente,stNomCedente,dtInicioCessao,dtTerminoCessao\');');";

    break;

    case 'montaAlterar' :
        //seleciona a origem do bem
        if ($_REQUEST['stOrigem'] == 't') {
            $stJs .= "$('stOrigemBemProprio').checked = true;";
        } elseif ($_REQUEST['stOrigem'] == 'f') {
            $stJs .= "$('stOrigemBemTerceiros').checked = true;";
        }

        //recupera as cessões do veículo
        $obTFrotaVeiculoCessao = new TFrotaVeiculoCessao();
        $obTFrotaVeiculoCessao->recuperaTodos($rsVeiculoCessao, " WHERE cod_veiculo = ".$_REQUEST['inCodVeiculo']);

        $inCount = 1;
        foreach ($rsVeiculoCessao->getElementos() as $indice => $campo) {
            $arCessao[$indice]['id'] = $inCount;
            $arCessao[$indice]['id_cessao'] = $campo['id'];
            $arCessao[$indice]['cod_veiculo'] = $campo['cod_veiculo'];
            $arCessao[$indice]['cod_processo'] = str_pad($campo['cod_processo'],5,'0',STR_PAD_LEFT);
            $arCessao[$indice]['exercicio'] = $campo['exercicio'];
            $arCessao[$indice]['cgm_cedente'] = $campo['cgm_cedente'];
            $arCessao[$indice]['nom_cedente'] = SistemaLegado::pegaDado('nom_cgm','sw_cgm',' WHERE numcgm = '.$campo['cgm_cedente']);
            $arCessao[$indice]['dt_inicio'] = $campo['dt_inicio'];
            $arCessao[$indice]['dt_termino'] = $campo['dt_termino'];
            $inCount++;
        }

        Sessao::write('arListaCessao', $arCessao);

        $stJs .= montaListaCessoes( $arCessao );

        //recupera os documentos do banco
        $obTFrotaVeiculoDocumento = new TFrotaVeiculoDocumento();
        $obTFrotaVeiculoDocumento->setDado('cod_veiculo',$_REQUEST['inCodVeiculo'] );
        $obTFrotaVeiculoDocumento->recuperaDocumentos( $rsDocumentos );

        //monta a lista

        $arDocumentos = Sessao::read('arDocumentos');
        while ( !$rsDocumentos->eof() ) {
            $inCount = count($arDocumentos);
            $arDocumentos[$inCount]['id']            = $inCount + 1;
            $arDocumentos[$inCount]['cod_documento'] = $rsDocumentos->getCampo('cod_documento');
            $arDocumentos[$inCount]['nom_documento'] = $rsDocumentos->getCampo('nom_documento');
            $arDocumentos[$inCount]['mes']           = $rsDocumentos->getCampo('mes');
            $arDocumentos[$inCount]['ano_documento'] = $rsDocumentos->getCampo('exercicio');
            $arDocumentos[$inCount]['desc_mes']      = strftime('%b',mktime(0,0,0,$rsDocumentos->getCampo('mes'),1,$rsDocumentos->getCampo('exercicio')));
            $arDocumentos[$inCount]['situacao']      = ($rsDocumentos->getCampo('situacao') == 'pago') ? true : false;
            $arDocumentos[$inCount]['desc_situacao'] = ($rsDocumentos->getCampo('situacao') == 'pago') ? 'Pago' : 'Não Pago';
            if ( $rsDocumentos->getCampo('situacao') == 'pago' ) {
                $arDocumentos[$inCount]['exercicio_empenho']    = $rsDocumentos->getCampo('exercicio_empenho');
                $arDocumentos[$inCount]['cod_entidade'] = $rsDocumentos->getCampo('cod_entidade');
                $arDocumentos[$inCount]['cod_empenho']  = $rsDocumentos->getCampo('cod_empenho');
                $arDocumentos[$inCount]['nom_empenho']  = $rsDocumentos->getCampo('nom_empenho');
            }
            $rsDocumentos->proximo();
        }
        Sessao::write('arDocumentos' , $arDocumentos);
        $stJs .= montaListaDocumentos( $arDocumentos );
        break;

    case 'montaResponsavel' :

        if ($_REQUEST['stOrigem'] != 'proprio') {
            $obForm = new Form();

            //instancia o componente IPopUpCGMVinculado para o responsavel
            $obIPopUpResponsavel = new IPopUpCGMVinculado( $obForm );
            $obIPopUpResponsavel->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica' );
            $obIPopUpResponsavel->setCampoVinculo     ( 'numcgm'               );
            $obIPopUpResponsavel->setNomeVinculo      ( 'Responsável'          );
            $obIPopUpResponsavel->setRotulo           ( 'Responsável'          );
            $obIPopUpResponsavel->setTitle            ( 'Informe o reponsável pelo veículo.' );
            $obIPopUpResponsavel->setName             ( 'stNomResponsavel'       );
            $obIPopUpResponsavel->setId               ( 'stNomResponsavel'       );
            $obIPopUpResponsavel->setValue            ( $_REQUEST['stNomResponsavel'] );
            $obIPopUpResponsavel->obCampoCod->setName ( 'inCodResponsavel'       );
            $obIPopUpResponsavel->obCampoCod->setId   ( 'inCodResponsavel'       );
            $obIPopUpResponsavel->obCampoCod->setValue( $_REQUEST['inCodResponsavel'] );
            $obIPopUpResponsavel->setNull             ( false                    );

            //instancia um componente data para o inicio do responsavel
            $obDtInicio = new Data();
            $obDtInicio->setRotulo('Data de Início');
            $obDtInicio->setTitle('Informe a data de início do responsável.');
            $obDtInicio->setName('dtInicio');
            $obDtInicio->setId('dtInicio');
            $obDtInicio->setNull(false);
            $obDtInicio->setValue( $_REQUEST['dtInicio'] );

            $obFormulario = new Formulario();
            $obFormulario->addTitulo( 'Dados do Responsável' );
            $obFormulario->addComponente( $obIPopUpResponsavel );
            $obFormulario->addComponente( $obDtInicio );
            $obFormulario->montaInnerHTML();

            $stJs = "$('spnResponsavel').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $stJs = "$('spnResponsavel').innerHTML = '';";
        }

        break;

    case 'montaPrefixoPlaca' :
        if ($_REQUEST['slTipoVeiculo'] != '') {

            //recupera as informacoes do banco
            $obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();
            $obTFrotaTipoVeiculo->setDado( 'cod_tipo', $_REQUEST['slTipoVeiculo'] );
            $obTFrotaTipoVeiculo->recuperaPorChave( $rsTipoVeiculo );

            //instancia um textbox para o numero da placa
            $obTxtPlaca = new TextBox();
            $obTxtPlaca->setRotulo( 'Placa do Veículo' );
            $obTxtPlaca->setTitle ( 'Informe a placa do veículo.' );
            $obTxtPlaca->setName  ( 'stNumPlaca' );
            if ( $rsTipoVeiculo->getCampo( 'placa' ) == 't' ) {
                $obTxtPlaca->setNull  ( false );
            }
            $obTxtPlaca->obEvento->setOnKeyUp( "mascaraPlacaVeiculo(this);" );
            $obTxtPlaca->obEvento->setOnBlur( "mascaraPlacaVeiculo(this);" );
            $obTxtPlaca->setMaxLength  ( 8 );
            $obTxtPlaca->setValue( $_REQUEST['stNumPlaca'] );

            //instancia textbox para o prefixo
            $obTxtPrefixo = new TextBox();
            $obTxtPrefixo->setRotulo( 'Prefixo' );
            $obTxtPrefixo->setTitle ( 'Informe prefixo do veículo.' );
            $obTxtPrefixo->setName  ( 'stPrefixo' );
            if ( $rsTipoVeiculo->getCampo( 'prefixo' ) == 't' ) {
                $obTxtPrefixo->setNull  ( false );
            }
            $obTxtPrefixo->setSize  ( 15 );
            $obTxtPrefixo->setMaxLength( 15 );
            $obTxtPrefixo->setValue( $_REQUEST['stPrefixo'] );

            //cria um formulario
            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTxtPlaca );
            $obFormulario->addComponente( $obTxtPrefixo );
            $obFormulario->montaInnerHTML();

            $stJs .= "$('spnPrefixoPlaca').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $stJs .= "$('spnPrefixoPlaca').innerHTML = '';";
        }

    break;

    case 'carregarListaInfracao':
        //apresenta lista de infrações do motorista
        $obTFrotaInfracao = new TFrotaInfracao();
        $stFiltro = " WHERE veiculo.cod_veiculo=".$_REQUEST['inCodVeiculo'];
        $obTFrotaInfracao->recuperaInfracao( $rsFrotaInfracao, $stFiltro, ' ORDER BY data_infracao DESC ');

        $obTable = new Table();
        $obTable->setRecordset( $rsFrotaInfracao );
        $obTable->setSummary( 'Lista de Infrações do Motorista' );

        $obTable->Head->addCabecalho( 'Motorista'    , 11 );
        $obTable->Head->addCabecalho( 'Auto Infração', 6  );
        $obTable->Head->addCabecalho( 'Data'         , 6  );
        $obTable->Head->addCabecalho( 'Motivo'       , 17 );
        $obTable->Head->addCabecalho( 'Gravidade'    , 5  );
        $obTable->Head->addCabecalho( 'Pontos'       , 4  );

        $obTable->Body->addCampo( 'nom_cgm'      , 'C' );
        $obTable->Body->addCampo( 'auto_infracao', 'C' );
        $obTable->Body->addCampo( 'data_infracao', 'C' );
        $obTable->Body->addCampo( 'motivo'       , 'C' );
        $obTable->Body->addCampo( 'gravidade'    , 'C' );
        $obTable->Body->addCampo( 'pontos'       , 'C' );

        $obTable->montaHTML( true );

        $obLabel = new Label();
        $obLabel->setName('total_infracao');
        $obLabel->setRotulo('Total de infrações');
        if( $rsFrotaInfracao->getNumLinhas() > 0 ) {
            $obLabel->setValue($rsFrotaInfracao->getNumLinhas());
        } else {
            $obLabel->setValue( 0 );
        }

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obLabel );
        $obFormulario->montaInnerHTML();

        $stJs.= "$('spnInfracao').innerHTML='".$obTable->getHtml().$obFormulario->getHTML()."';";
    break;
}

echo $stJs;

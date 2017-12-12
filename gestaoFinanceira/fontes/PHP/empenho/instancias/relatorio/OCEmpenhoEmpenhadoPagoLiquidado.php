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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 18/02/2005

    * @author Lucas Leusin Oaigen

    * @ignore

    $Id: OCEmpenhoEmpenhadoPagoLiquidado.php 64470 2016-03-01 13:12:50Z jean $

    * Casos de uso : uc-02.03.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"            );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                     );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"       );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioEmpenhadoPagoLiquidado.class.php"  );
include_once( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                         );

$obROrcamentoDespesa        = new ROrcamentoDespesa;
$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;

$obREntidade                = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );
$obRRelatorio                       = new RRelatorio;
$obREmpenhoEmpenhadoPagoLiquidado   = new REmpenhoRelatorioEmpenhadoPagoLiquidado;
$obROrcamentoUnidadeOrcamentaria    = new ROrcamentoUnidadeOrcamentaria;
$obROrcamentoRecurso                = new ROrcamentoRecurso;
$obROrcamentoClassificacaoDespesa   = new ROrcamentoClassificacaoDespesa;

$arFiltro = Sessao::read('filtroRelatorio');
$stEntidade = isset($stEntidade) ? $stEntidade : null;
$inCount = 0;
//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidade .= $valor.",";
        $inCount++;
    }
    $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
} else {
    $stEntidade .= $arFiltro['stTodasEntidades'];
}

if ( $rsTotalEntidades->getNumLinhas() == $inCount ) {
   $arFiltro['relatorio'] = "Consolidado";
} else {
   $arFiltro['relatorio'] = "";
}

switch ( $request->get('stCtrl') ) {
    case "MontaUnidade":
        if ($_REQUEST["inCodOrgao"]) {
            $stCombo  = "inCodUnidade";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
            $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";

            $obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST['inCodOrgao']);
            $obROrcamentoUnidadeOrcamentaria->listar($rsCombo);
            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("num_unidade");
                $stDesc = $rsCombo->getCampo("nom_unidade");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsCombo->proximo();
            }
        }

        $stJs .= $js;
        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "mascaraClassificacao":
        //monta mascara da RUBRICA DE DESPESA
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodDespesa'] );
        $js .= "f.inCodDespesa.value = '".$arMascClassificacao[1]."'; \n";

        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
        if ($stDescricao != "") {
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodDespesa.value = "";';
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'buscaFornecedor':
        if ($_POST["inCodFornecedor"] != "") {
            $obRCGM = new RCGM;
            $obRCGM->setNumCGM( $_POST["inCodFornecedor"] );
            $obRCGM->listar( $rsCGM );
            $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomFornecedor) {
                $js .= 'f.inCodFornecedor.value = "";';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodFornecedor"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
            }
        } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;
    case 'mostraSpanContaBanco':
        if ($_POST["inSituacaoTxt"] == "2") {
            include_once( CAM_GF_CONT_COMPONENTES."IPopUpContaBancoEntidades.class.php");

            $obIPopUpContaBancoEntidades = new IPopUpContaBancoEntidades(Sessao::read('obCmbEntidades'));

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obIPopUpContaBancoEntidades );

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );
        } else {
            $stHTML = "";
        }
        SistemaLegado::executaFrameOculto("d.getElementById('spnContaBanco').innerHTML = '".$stHTML."';");
    break;

    case 'buscaDotacao':
        include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioSituacaoEmpenho.class.php";

        $obRegra = new REmpenhoRelatorioSituacaoEmpenho;
        $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST["inCodDotacao"] );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( implode(',',$_REQUEST['inCodEntidade']) );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );

        $stNomDespesa = $rsDespesa->getCampo( "descricao" );
        if (!$stNomDespesa) {
            $js .= 'f.inCodDotacao.value = "";';
            $js .= 'f.inCodDotacao.focus();';
            $js .= 'd.getElementById("stNomDotacao").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodDotacao"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stNomDespesa = $rsDespesa->getCampo( "descricao" );
            $js .= 'd.getElementById("stNomDotacao").innerHTML = "'.$stNomDespesa.'";';
        }

        SistemaLegado::executaFrameOculto($js);
     break;

    case 'buscaContrapartida':
        if ($_REQUEST['inCodFornecedor'] && ( $_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3)) {
            include_once TEMP.'TEmpenhoResponsavelAdiantamento.class.php';
            $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
            $obTEmpenhoResponsavelAdiantamento->setDado("exercicio", Sessao::getExercicio());
            $obTEmpenhoResponsavelAdiantamento->setDado("numcgm"   , $_REQUEST['inCodFornecedor']);
            $obTEmpenhoResponsavelAdiantamento->recuperaContrapartidaLancamento($rsContrapartida);

            if ($rsContrapartida->getNumLinhas() > 0) {
                $obCmbContrapartida = new Select;
                $obCmbContrapartida->setRotulo    ('Contrapartida'                      );
                $obCmbContrapartida->setTitle     ('Informe a contrapartida.'           );
                $obCmbContrapartida->setName      ('inCodContrapartida'                 );
                $obCmbContrapartida->setId        ('inCodContrapartida'                 );
                $obCmbContrapartida->setValue     ($inCodContrapartida                  );
                $obCmbContrapartida->setStyle     ('width: 600'                         );
                $obCmbContrapartida->addOption    ('', 'Selecione'                      );
                $obCmbContrapartida->setCampoId   ('conta_contrapartida'                );
                $obCmbContrapartida->setCampoDesc ("[conta_contrapartida] - [nom_conta]");
                $obCmbContrapartida->preencheCombo($rsContrapartida                     );

                $obFormulario = new Formulario;
                $obFormulario->addComponente( $obCmbContrapartida );
                $obFormulario->montaInnerHTML();
                $stHtml = $obFormulario->getHTML();
                $js .= " d.getElementById('spnContrapartida').innerHTML = '".$stHtml."'; ";

            } else {
               $js .= "  f.inCodCategoria.options.selectedIndex = 0;
                         d.getElementById('spnContrapartida').innerHTML = '';
                         alertaAviso('@O responsável por adiantamento informado não está cadastrado ou está inativo.','form','erro','".Sessao::getId()."');";
            }
        } else {
            $js = " d.getElementById('spnContrapartida').innerHTML = ''; ";
        }

        SistemaLegado::executaFrameOculto($js);
    break;

    default:
        $stFiltro = "";

        if ($arFiltro['inCodCategoria'] != "") {
            $stFiltro .= ' e.cod_categoria = '.$arFiltro['inCodCategoria'].' AND ';
        }

        if ($arFiltro['inCodTipo'] != "") {
            $stFiltro .= ' pe.cod_tipo = '.$arFiltro['inCodTipo'].' AND ';
        }

        if ($arFiltro['inSituacao'] != "2") {

            if( $arFiltro['inCodFornecedor'] )
                $stFiltro .= ' pe.cgm_beneficiario = '.$arFiltro['inCodFornecedor'].' AND ';

            if ($stFiltro) {
                $stFiltro = substr( $stFiltro,0,strlen($stFiltro)-4 );
                $obREmpenhoEmpenhadoPagoLiquidado->setFiltro                 ( $stFiltro );
            }

            if (Sessao::getExercicio() > '2015'){
                $obREmpenhoEmpenhadoPagoLiquidado->setCentroCusto ($arFiltro['inCentroCusto']);
            }

            $obREmpenhoEmpenhadoPagoLiquidado->setCodEntidade            ( $stEntidade );
            $obREmpenhoEmpenhadoPagoLiquidado->setExercicio              ( Sessao::getExercicio() );
            $obREmpenhoEmpenhadoPagoLiquidado->setCodDotacao             ( $arFiltro['inCodDotacao'] );
            $obREmpenhoEmpenhadoPagoLiquidado->setDataInicial            ( $arFiltro['stDataInicial'] );
            $obREmpenhoEmpenhadoPagoLiquidado->setDataFinal              ( $arFiltro['stDataFinal'] );
            $obREmpenhoEmpenhadoPagoLiquidado->setDemonstracaoDescricaoEmpenho ($arFiltro['stDemonstracaoDescricaoEmpenho']);
            $obREmpenhoEmpenhadoPagoLiquidado->setDemonstracaoDescricaoRecurso($arFiltro['stDemonstracaoRecursoEmpenho']);
            $obREmpenhoEmpenhadoPagoLiquidado->setDemonstracaoDescricaoElementoDespesa($arFiltro['stDemonstracaoElementoDespesa']);
            $obREmpenhoEmpenhadoPagoLiquidado->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $arFiltro['inCodOrgao'] );
            $obREmpenhoEmpenhadoPagoLiquidado->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $arFiltro['inCodUnidade'] );
            $obREmpenhoEmpenhadoPagoLiquidado->obROrcamentoFuncao->setCodigoFuncao      ($arFiltro['inCodFuncao']);
            $obREmpenhoEmpenhadoPagoLiquidado->obROrcamentoSubfuncao->setCodigoSubfuncao($arFiltro['inCodSubFuncao']);
            $obREmpenhoEmpenhadoPagoLiquidado->obROrcamentoPrograma->setCodPrograma     ($arFiltro['inCodPrograma']);
            $obREmpenhoEmpenhadoPagoLiquidado->obROrcamentoProjetoAtividade->setNumeroProjeto( $arFiltro['inCodPao'] );
            $obREmpenhoEmpenhadoPagoLiquidado->obROrcamentoClassificacaoDespesa->setCodEstrutural( $arFiltro['inCodDespesa'] );
            $obREmpenhoEmpenhadoPagoLiquidado->obREmpenhoHistorico->setCodHistorico ( $arFiltro['inCodHistorico'] );
            $obREmpenhoEmpenhadoPagoLiquidado->obROrcamentoRecurso->setCodRecurso( $arFiltro['inCodRecurso'] );
            if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
                $obREmpenhoEmpenhadoPagoLiquidado->obROrcamentoRecurso->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );
            $obREmpenhoEmpenhadoPagoLiquidado->obROrcamentoRecurso->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );
            $obREmpenhoEmpenhadoPagoLiquidado->setSituacao               ( $arFiltro['inSituacao'] );
            $obREmpenhoEmpenhadoPagoLiquidado->obRContabilidadePlanoContaAnalitica->setCodPlano($arFiltro['inCodContaBanco']);
            $obREmpenhoEmpenhadoPagoLiquidado->setOrdenacao              ( $arFiltro['stOrdenacao'] );

            // Se for empenhado
            if ($arFiltro['inSituacao'] == 1) {
                $obREmpenhoEmpenhadoPagoLiquidado->setTipoRelatorio( 'empenhado' );
            }

            if ($arFiltro['inSituacao'] == 3) {
                $obREmpenhoEmpenhadoPagoLiquidado->setTipoRelatorio( 'liquidado' );
            }

            $obREmpenhoEmpenhadoPagoLiquidado->geraRecordSet( $rsEmpenhoEmpenhadoPagoLiquidado );

            Sessao::write('filtroRelatorio', $arFiltro);
            Sessao::write('rsRecordSet', $rsEmpenhoEmpenhadoPagoLiquidado);

            $obRRelatorio->executaFrameOculto( "OCGeraRelatorioEmpenhoEmpenhadoPagoLiquidado.php" );

        } else {

            include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioEmpenhadoPagoEstornado.class.php"  );
            $obREmpenhoEmpenhadoPagoEstornado = new REmpenhoRelatorioEmpenhadoPagoEstornado;

            if (isset($arFiltro['inCodFornecedor']) && strlen($arFiltro['inCodFornecedor']) > 0) {
                $stFiltro .= ' pe.cgm_beneficiario = '.$arFiltro['inCodFornecedor'].' AND ';
            }

            if ($stFiltro) {
                $stFiltro = substr( $stFiltro,0,strlen($stFiltro)-4 );
                $obREmpenhoEmpenhadoPagoEstornado->setFiltro                 ( $stFiltro );
            }

            if (Sessao::getExercicio() > '2015'){
                $obREmpenhoEmpenhadoPagoEstornado->setCentroCusto ($arFiltro['inCentroCusto']);
            }

            $obREmpenhoEmpenhadoPagoEstornado->setCodEntidade            ( $stEntidade );
            $obREmpenhoEmpenhadoPagoEstornado->setExercicio              ( Sessao::getExercicio() );
            $obREmpenhoEmpenhadoPagoEstornado->setCodDotacao             ( $arFiltro['inCodDotacao'] );
            $obREmpenhoEmpenhadoPagoEstornado->setDataInicial            ( $arFiltro['stDataInicial'] );
            $obREmpenhoEmpenhadoPagoEstornado->setDataFinal              ( $arFiltro['stDataFinal'] );
            $obREmpenhoEmpenhadoPagoEstornado->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $arFiltro['inCodOrgao'] );
            $obREmpenhoEmpenhadoPagoEstornado->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $arFiltro['inCodUnidade'] );
            $obREmpenhoEmpenhadoPagoEstornado->obROrcamentoFuncao->setCodigoFuncao      ($arFiltro['inCodFuncao']);
            $obREmpenhoEmpenhadoPagoEstornado->obROrcamentoSubfuncao->setCodigoSubfuncao($arFiltro['inCodSubFuncao']);
            $obREmpenhoEmpenhadoPagoEstornado->obROrcamentoPrograma->setCodPrograma     ($arFiltro['inCodPrograma']);
            $obREmpenhoEmpenhadoPagoEstornado->obROrcamentoProjetoAtividade->setNumeroProjeto( $arFiltro['inCodPao'] );
            $obREmpenhoEmpenhadoPagoEstornado->obROrcamentoClassificacaoDespesa->setCodEstrutural( $arFiltro['inCodDespesa'] );
            $obREmpenhoEmpenhadoPagoEstornado->obREmpenhoHistorico->setCodHistorico ( $arFiltro['inCodHistorico'] );
            $obREmpenhoEmpenhadoPagoEstornado->obROrcamentoRecurso->setCodRecurso( $arFiltro['inCodRecurso'] );
            if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
                $obREmpenhoEmpenhadoPagoEstornado->obROrcamentoRecurso->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );
            $obREmpenhoEmpenhadoPagoEstornado->obROrcamentoRecurso->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );
            $obREmpenhoEmpenhadoPagoEstornado->setSituacao               ( $arFiltro['inSituacao'] );
            $obREmpenhoEmpenhadoPagoEstornado->obRContabilidadePlanoContaAnalitica->setCodPlano($arFiltro['inCodContaBanco']);
            $obREmpenhoEmpenhadoPagoEstornado->setOrdenacao              ( $arFiltro['stOrdenacao'] );
            $obREmpenhoEmpenhadoPagoEstornado->setDemonstracaoDescricaoEmpenho ($arFiltro['stDemonstracaoDescricaoEmpenho']);
            $obREmpenhoEmpenhadoPagoEstornado->setDemonstracaoDescricaoRecurso($arFiltro['stDemonstracaoRecursoEmpenho']);
            $obREmpenhoEmpenhadoPagoEstornado->setDemonstracaoDescricaoElementoDespesa($arFiltro['stDemonstracaoElementoDespesa']);

            if ($arFiltro['inSituacao'] == 2) {
                $obREmpenhoEmpenhadoPagoEstornado->setTipoRelatorio( 'pagos' );
            }

            $obREmpenhoEmpenhadoPagoEstornado->geraRecordSet( $rsEmpenhoEmpenhadoPagoEstornado );
            
            Sessao::write('filtroRelatorio', $arFiltro);
            Sessao::write('rsRecordSet', $rsEmpenhoEmpenhadoPagoEstornado);

            $obRRelatorio->executaFrameOculto( "OCGeraRelatorioEmpenhoEmpenhadoPagoEstornado.php" );
        }
    break;
}
?>

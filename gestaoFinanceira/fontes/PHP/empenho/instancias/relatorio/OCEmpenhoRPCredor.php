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
    * Data de Criação   : 23/02/2005

    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Id: OCEmpenhoRPCredor.php 64417 2016-02-18 18:03:51Z michel $

    * Casos de uso : uc-02.03.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPCredor.class.php";

$obRegra = new REmpenhoRelatorioRPCredor;

$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );

$arFiltro = Sessao::read('filtroRelatorio');
$arFiltroNom = Sessao::read('filtroNomRelatorio');
//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    $inCount = 0;
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
switch ($request->get('stCtrl')) {

    case "MontaOrgao":
        if ($request->get('inExercicio')) {
            if ($request->get('inExercicio') > '2004') {
                $obTxtOrgao = new TextBox;
                $obTxtOrgao->setRotulo              ( "Órgão"                      );
                $obTxtOrgao->setTitle               ( "Informe o órgão para filtro");
                $obTxtOrgao->setName                ( "inCodOrgaoTxt"              );
                $obTxtOrgao->setValue               ( ""                           );
                $obTxtOrgao->setSize                ( 6                            );
                $obTxtOrgao->setMaxLength           ( 3                            );
                $obTxtOrgao->setInteiro             ( true                         );
                $obTxtOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');");

                $obCmbOrgao = new Select;
                $obCmbOrgao->setRotulo              ( "Órgão"                       );
                $obCmbOrgao->setName                ( "inCodOrgao"                  );
                $obCmbOrgao->setValue               ( ""                            );
                $obCmbOrgao->setStyle               ( "width: 200px"                );
                $obCmbOrgao->setCampoID             ( "num_orgao"                   );
                $obCmbOrgao->setCampoDesc           ( "nom_orgao"                   );
                $obCmbOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');" );

                $obTxtUnidade = new TextBox;
                $obTxtUnidade->setRotulo              ( "Unidade"                       );
                $obTxtUnidade->setTitle               ( "Informe a unidade para filtro" );
                $obTxtUnidade->setName                ( "inCodUnidadeTxt"               );
                $obTxtUnidade->setValue               ( ""                              );
                $obTxtUnidade->setSize                ( 6                               );
                $obTxtUnidade->setMaxLength           ( 3                               );
                $obTxtUnidade->setInteiro             ( true                            );

                $obCmbUnidade= new Select;
                $obCmbUnidade->setRotulo              ( "Unidade"                       );
                $obCmbUnidade->setName                ( "inCodUnidade"                  );
                $obCmbUnidade->setValue               ( ""                              );
                $obCmbUnidade->setStyle               ( "width: 200px"                  );
                $obCmbUnidade->setCampoID             ( "num_unidade"                   );
                $obCmbUnidade->setCampoDesc           ( "descricao"                     );

                $obFormulario = new Formulario;
                $obFormulario->addComponenteComposto ( $obTxtOrgao,$obCmbOrgao     );
                $obFormulario->addComponenteComposto ( $obTxtUnidade,$obCmbUnidade );

                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                $stJs = "d.getElementById('spnOrgaoUnidade').innerHTML = '".$stHTML."';";

                $js .= "f.inCodOrgao.options[0] = new Option('Selecione','','selected'); \n";
                $js .= "f.inCodOrgaoTxt.value = ''; \n";
                $js .= "f.inCodUnidade.options[0] = new Option('Selecione','','selected'); \n";
                $js .= "f.inCodUnidadeTxt.value = ''; \n";
                $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio($request->get('inExercicio'));
                $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );
                while ( !$rsOrgao->eof() ) {
                    $arFiltroNomFiltro['orgao'][$rsOrgao->getCampo( 'num_orgao' )] = $rsOrgao->getCampo( 'nom_orgao' );
                    $rsOrgao->proximo();
                }
                $rsOrgao->setPrimeiroElemento();

                $inContador = 1;
                while ( !$rsOrgao->eof() ) {
                    $inCodOrgao = $rsOrgao->getCampo( "num_orgao" );
                    $stOrgao    = $rsOrgao->getCampo( "nom_orgao" );
                    $js .= "f.inCodOrgao.options[$inContador] = new Option('".$stOrgao."','".$inCodOrgao."'); \n";
                    $inContador++;
                    $rsOrgao->proximo();
                }
            } else {
                $obTxtOrgao = new TextBox;
                $obTxtOrgao->setRotulo              ( "Órgão"                      );
                $obTxtOrgao->setTitle               ( "Informe o órgão para filtro");
                $obTxtOrgao->setName                ( "inCodOrgao"                 );
                $obTxtOrgao->setValue               ( ""                           );
                $obTxtOrgao->setSize                ( 6                            );
                $obTxtOrgao->setMaxLength           ( 3                            );
                $obTxtOrgao->setInteiro             ( true                         );

                $obTxtUnidade = new TextBox;
                $obTxtUnidade->setRotulo              ( "Unidade"                       );
                $obTxtUnidade->setTitle               ( "Informe a unidade para filtro" );
                $obTxtUnidade->setName                ( "inCodUnidade"                  );
                $obTxtUnidade->setValue               ( ""                              );
                $obTxtUnidade->setSize                ( 6                               );
                $obTxtUnidade->setMaxLength           ( 3                               );
                $obTxtUnidade->setInteiro             ( true                            );

                $obFormulario = new Formulario;
                $obFormulario->addComponente ( $obTxtOrgao   );
                $obFormulario->addComponente ( $obTxtUnidade );

                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                $stJs = "d.getElementById('spnOrgaoUnidade').innerHTML = '".$stHTML."';";
            }
        } else {
            $stJs = "d.getElementById('spnOrgaoUnidade').innerHTML = '';";
        }

    $stJs .= $js;
    SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "MontaUnidade":
     $arFiltroNom = array();
     $js  = "limpaSelect(f.inCodUnidade,0) \n";
     $js .= "f.inCodUnidadeTxt.value = ''; \n";
     $js .= "f.inCodUnidade.options[0] = new Option('Selecione','','selected'); \n";
     if ($request->get('inCodOrgao')) {
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setExercicio( $request->get('inExercicio') );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $request->get('inCodOrgao') );

        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->listar( $rsUnidade, "","", $boTransacao );
        while ( !$rsUnidade->eof() ) {
            $arFiltroNom['unidade'][$rsUnidade->getCampo( 'num_unidade' )] = $rsUnidade->getCampo( 'nom_unidade' );
            $rsUnidade->proximo();
        }
        $rsUnidade->setPrimeiroElemento();

        $inContador = 1;
        while ( !$rsUnidade->eof() ) {
            $inCodUnidade  = $rsUnidade->getCampo( "num_unidade" );
            $stUnidade     = $rsUnidade->getCampo( "nom_unidade" );
            $js .= "f.inCodUnidade.options[$inContador] = new Option('".$stUnidade."','".$inCodUnidade."'); \n";
            $inContador++;
            $rsUnidade->proximo();
        }
        Sessao::write('filtroNomRelatorio', $arFiltroNom);
    }
    $stJs .= $js;
    SistemaLegado::executaFrameOculto( $stJs );
    break;

   case "mascaraClassificacao":
        //monta mascara da RUBRICA DE DESPESA
        $arMascClassificacao = Mascara::validaMascaraDinamica( $request->get('stMascClassificacao') , $request->get('stElementoDespesa') );
        $js .= "f.stElementoDespesa.value = '".$arMascClassificacao[1]."'; \n";

        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $request->get('stMascClassificacao') );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
        if ($stDescricao != "") {
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.stElementoDespesa.value = "";';
            $js .= 'f.stElementoDespesa.focus();';
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'buscaFornecedor':
        if ($request->get('inCGM', '')!= "") {
            $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( $request->get('inCGM') );
            $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->listar( $rsCGM );
            $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomFornecedor) {
                $js .= 'f.inCGM.value = "";';
                $js .= 'f.inCGM.focus();';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$request->get('inCGM').")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
            }
        } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;

    default:
        //RELATÓRIO ANTERIOR ATÉ 2015
        if(Sessao::getExercicio() < 2016){
            $arFiltro = Sessao::read('filtroRelatorio');
            $arFiltro['inOrdenacao'] = $arFiltro['inOrdenacao'] ? $arFiltro['inOrdenacao'] : 1;
            $stFiltro = "";
            $obRegra->setFiltro                 ( $stFiltro );
            $obRegra->setCodEntidade            ( $stEntidade );
            if ($arFiltro['inExercicio'] == '') {
                $obRegra->setExercicio              ( Sessao::getExercicio());
            } else {
                $obRegra->setExercicio              ( $arFiltro['inExercicio'] );
            }
            $obRegra->setDataInicial            ( "01/01/".$arFiltro['inExercicio'] );
            $obRegra->setDataFinal              ( $arFiltro['stDataSituacao'] );
            $obRegra->obROrcamentoOrgao->setNumeroOrgao( $arFiltro['inCodOrgao'] );
            $obRegra->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $arFiltro['inCodUnidade'] );
            $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodEstrutural( $arFiltro['stElementoDespesa'] );
            $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso( $arFiltro['inCodRecurso'] );
            if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
            $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );

            $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );

            $obRegra->setFuncao                ( $arFiltro['stCodFuncao'] );
            $obRegra->setSubFuncao             ( $arFiltro['stCodSubFuncao'] );
            $obRegra->setOrdem                 ( $arFiltro['inOrdenacao'] );
            $obRegra->setModulo                ( $arFiltro['inCodModulo'] );

            $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( $arFiltro['inCGM'] );
            $obRegra->obREmpenhoEmpenho->setCodEmpenhoInicial  ( $arFiltro['inCodEmpenhoInicial']  );
            $obRegra->obREmpenhoEmpenho->setCodEmpenhoFinal    ( $arFiltro['inCodEmpenhoFinal']    );

            switch ($arFiltro['inOrdenacao']) {
                case "1":
                    $arFiltro['stOrdenacao'] = "Empenho";
                break;
    
                case "2":
                    $arFiltro['stOrdenacao'] = "Vencimento";
                break;
    
                case "3":
                    $arFiltro['stOrdenacao'] = "Recurso";
                break;
                case "4":
                    $arFiltro['stOrdenacao'] = "Credor";
                break;

            }

            $obRegra->geraRecordSet( $rsEmpenhoRPCredor , $arFiltro['inOrdenacao'] );
            Sessao::write('filtroRelatorio', $arFiltro);
            Sessao::write('rsRecordSet', $rsEmpenhoRPCredor);
            $obRegra->obRRelatorio->executaFrameOculto( "OCGeraRelatorioEmpenhoRPCredor.php" );
        }else{
            //NOVO RELATÓRIO APÓS 2015
            include_once '../../../../../../config.php';
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
            include_once CLA_MPDF;
            include_once CAM_GF_EMP_MAPEAMENTO."FEmpenhoRelatorioSituacaoRP.class.php";
            include_once CAM_FW_PDF."RRelatorio.class.php";

            $arFiltro = Sessao::read('filtroRelatorio');
            $request = new Request($arFiltro);

            $stDataFinal        = $request->get('stDataSituacao');
            $inCodEntidades     = $request->get('inCodEntidade');
            $stExercicioEmpenho = $request->get('inExercicio');
            $inCGM              = $request->get('inCGM');

            $stFiltro = " WHERE 1 = 1 \n";

            $arDataFinal = explode('/', $stDataFinal);
            $stDataInicial  = '01/01/'.$arDataFinal[2];

            $stExercicio = Sessao::getExercicio();

            $inCodEntidades = implode(',', $inCodEntidades);

            $obFEmpenhoRelatorioSituacaoRP = new FEmpenhoRelatorioSituacaoRP();
            $obFEmpenhoRelatorioSituacaoRP->setDado( 'exercicio'         , $stExercicio);
            $obFEmpenhoRelatorioSituacaoRP->setDado( 'cod_entidade'      , $inCodEntidades);
            $obFEmpenhoRelatorioSituacaoRP->setDado( 'dt_inicial'        , $stDataInicial);
            $obFEmpenhoRelatorioSituacaoRP->setDado( 'dt_final'          , $stDataFinal);
            $obFEmpenhoRelatorioSituacaoRP->setDado( 'exercicio_empenho' , $stExercicioEmpenho);
            $obFEmpenhoRelatorioSituacaoRP->setDado( 'cgm_credor'        , $inCGM);

            //Array Filtro
            $inCount = 0;
            $arFiltro = array();
            $arFiltro[$inCount]['titulo'] = 'Exercício';
            $arFiltro[$inCount]['valor']  = $stExercicio;
            $inCount++;

            $arFiltro[$inCount]['titulo'] = 'Entidades';
            $arFiltro[$inCount]['valor']  = $inCodEntidades;

            $inCount++;

            $arFiltro[$inCount]['titulo'] = 'Data Inicial';
            $arFiltro[$inCount]['valor']  = $stDataInicial;

            $inCount++;

            $arFiltro[$inCount]['titulo'] = 'Data Final';
            $arFiltro[$inCount]['valor']  = $stDataFinal;

            if($stExercicioEmpenho != ''){
                $inCount++;
                $arFiltro[$inCount]['titulo'] = 'Exercício Empenho';
                $arFiltro[$inCount]['valor']  = $stExercicioEmpenho;
            }

            if($request->get('inCodEmpenhoInicial', '') != '' || $request->get('inCodEmpenhoFinal', '') != ''){
                $inCount++;
                $arFiltro[$inCount]['titulo'] = 'Número do Empenho';
                if($request->get('inCodEmpenhoInicial', '') != '' && $request->get('inCodEmpenhoFinal', '') != ''){
                    $stNroEmpenho = $request->get('inCodEmpenhoInicial').' até '.$request->get('inCodEmpenhoFinal');
                    $stFiltro .= " AND empenho.cod_empenho BETWEEN ".$request->get('inCodEmpenhoInicial')." AND ".$request->get('inCodEmpenhoFinal')."   \n";
                }else if($request->get('inCodEmpenhoInicial', '')){
                    $stNroEmpenho = $request->get('inCodEmpenhoInicial').' até '.$request->get('inCodEmpenhoInicial');
                    $stFiltro .= " AND empenho.cod_empenho BETWEEN ".$request->get('inCodEmpenhoInicial')." AND ".$request->get('inCodEmpenhoInicial')." \n";
                }else{
                    $stNroEmpenho = $request->get('inCodEmpenhoFinal').' até '.$request->get('inCodEmpenhoFinal');
                    $stFiltro .= " AND empenho.cod_empenho BETWEEN ".$request->get('inCodEmpenhoFinal')." AND ".$request->get('inCodEmpenhoFinal')."     \n";
                }

                $arFiltro[$inCount]['valor']  = $stNroEmpenho;
            }

            $assinaturas = Sessao::read('assinaturas');
            if ( count($assinaturas['selecionadas']) > 0 ) {
                include_once CAM_FW_PDF."RAssinaturas.class.php";
                $obRAssinaturas = new RAssinaturas;
                $obRAssinaturas->setArAssinaturas( $assinaturas['selecionadas'] );
                $rsAssinaturas = $obRAssinaturas->getArAssinaturas();

                foreach ($rsAssinaturas as $key => $assinatura) {
                    $arAssinaturas[] = $rsAssinaturas[$key]->getElementos();
                }
            }

            if($request->get('stElementoDespesa', '') != ''){
                $inCount++;
                $arFiltro[$inCount]['titulo'] = 'Elemento de Despesa';
                $arFiltro[$inCount]['valor']  = $request->get('stElementoDespesa');
                $stFiltro .= " AND despesa.cod_estrutural LIKE '".str_replace(".", "", $request->get('stElementoDespesa'))."%' \n";
            }

            if($request->get('inCodRecurso', '') != ''){
                $inCount++;
                $arFiltro[$inCount]['titulo'] = 'Recurso';
                $arFiltro[$inCount]['valor']  = $request->get('inCodRecurso').' - '.$request->get('stDescricaoRecurso');
                $stFiltro .= " AND despesa.cod_recurso = ".$request->get('inCodRecurso')."                                     \n";
            }

            if($request->get('inCGM', '') != ''){
                $inCount++;
                $arFiltro[$inCount]['titulo'] = 'Credor';
                $arFiltro[$inCount]['valor']  = $request->get('inCGM').' - '.$request->get('stNomFornecedor');
            }

            if($request->get('stCodFuncao', '') != ''){
                $inCount++;
                $arFiltro[$inCount]['titulo'] = 'Função';
                $arFiltro[$inCount]['valor']  = $request->get('stCodFuncao');
                $stFiltro .= " AND despesa.cod_funcao = ".$request->get('stCodFuncao')."                                       \n";
            }

            if($request->get('stCodSubFuncao', '') != ''){
                $inCount++;
                $arFiltro[$inCount]['titulo'] = 'Sub-função';
                $arFiltro[$inCount]['valor']  = $request->get('stCodSubFuncao');
                $stFiltro .= " AND despesa.cod_subfuncao = ".$request->get('stCodSubFuncao')."                                 \n";
            }

            //Gerando os records sets
            $rsSituacaoRP = new RecordSet();
            $stOrder = "ORDER BY rp.credor
                               , rp.cod_entidade
                               , rp.exercicio
                               , rp.cod_empenho
                       ";
            $obFEmpenhoRelatorioSituacaoRP->recuperaTodos($rsSituacaoRP, $stFiltro, $stOrder);

            $arSituacaoRP = $rsSituacaoRP->getElementos();

            //SOMAR TODOS OS ARRAYS
            $arRestosCredor   = array();
            $arTotalCredor    = array();
            $arTotalExercicio = array();
            $arTotal          = array();
            foreach($arSituacaoRP as $restos) {
                $stCredor = $restos['cgm_credor'].' - '.$restos['credor'];
                $arRestosCredor[$stCredor]['cpf_cnpj'] = $restos['cpf_cnpj'];
                $arRestosCredor[$stCredor]['restos'][] = $restos;

                $arTotalCredor[$restos['cgm_credor']]['empenhado']             += $restos['empenhado'];
                $arTotalCredor[$restos['cgm_credor']]['aliquidar']             += $restos['aliquidar'];
                $arTotalCredor[$restos['cgm_credor']]['liquidadoapagar']       += $restos['liquidadoapagar'];
                $arTotalCredor[$restos['cgm_credor']]['anulado']               += $restos['anulado'];
                $arTotalCredor[$restos['cgm_credor']]['liquidado']             += $restos['liquidado'];
                $arTotalCredor[$restos['cgm_credor']]['pagamento']             += $restos['pagamento'];
                $arTotalCredor[$restos['cgm_credor']]['empenhado_saldo']       += $restos['empenhado_saldo'];
                $arTotalCredor[$restos['cgm_credor']]['aliquidar_saldo']       += $restos['aliquidar_saldo'];
                $arTotalCredor[$restos['cgm_credor']]['liquidadoapagar_saldo'] += $restos['liquidadoapagar_saldo'];

                $arTotalExercicio[$restos['exercicio']]['empenhado']             += $restos['empenhado'];
                $arTotalExercicio[$restos['exercicio']]['aliquidar']             += $restos['aliquidar'];
                $arTotalExercicio[$restos['exercicio']]['liquidadoapagar']       += $restos['liquidadoapagar'];
                $arTotalExercicio[$restos['exercicio']]['anulado']               += $restos['anulado'];
                $arTotalExercicio[$restos['exercicio']]['liquidado']             += $restos['liquidado'];
                $arTotalExercicio[$restos['exercicio']]['pagamento']             += $restos['pagamento'];
                $arTotalExercicio[$restos['exercicio']]['empenhado_saldo']       += $restos['empenhado_saldo'];
                $arTotalExercicio[$restos['exercicio']]['aliquidar_saldo']       += $restos['aliquidar_saldo'];
                $arTotalExercicio[$restos['exercicio']]['liquidadoapagar_saldo'] += $restos['liquidadoapagar_saldo'];

                $arTotal[0]['empenhado']             += $restos['empenhado'];
                $arTotal[0]['aliquidar']             += $restos['aliquidar'];
                $arTotal[0]['liquidadoapagar']       += $restos['liquidadoapagar'];
                $arTotal[0]['anulado']               += $restos['anulado'];
                $arTotal[0]['liquidado']             += $restos['liquidado'];
                $arTotal[0]['pagamento']             += $restos['pagamento'];
                $arTotal[0]['empenhado_saldo']       += $restos['empenhado_saldo'];
                $arTotal[0]['aliquidar_saldo']       += $restos['aliquidar_saldo'];
                $arTotal[0]['liquidadoapagar_saldo'] += $restos['liquidadoapagar_saldo'];
            }

            $arDados['exercicio']               = $stExercicio;
            $arDados['stDataInicial']           = $stDataInicial;
            $arDados['stDataFinal']             = $stDataFinal;
            $arDados['inCodEntidade']           = $inCodEntidades;

            $arDados['restos_pagar']            = $arRestosCredor;
            $arDados['total_credor']            = $arTotalCredor;
            ksort($arTotalExercicio);
            $arDados['total_exercicio']         = $arTotalExercicio;
            $arDados['total']                   = $arTotal;
            $arDados['arAssinaturas']           = $arAssinaturas;
            $arDados['filtro']                  = $arFiltro;

            Sessao::write('arDados', $arDados);

            $obRRelatorio = new RRelatorio;
            $obRRelatorio->executaFrameOculto( "OCGeraRelatorioEmpenhoRPCredor.php" );
        }

    break;
}

?>

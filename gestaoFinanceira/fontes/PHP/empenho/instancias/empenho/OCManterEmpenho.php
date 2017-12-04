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
    * Paginae Oculta de Empenho
    * Data de Criação   : 17/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: OCManterEmpenho.php 66418 2016-08-25 21:02:27Z michel $

    * Casos de uso: uc-02.03.03
                    uc-02.03.04
                    uc-02.01.08
*/

header ("Content-Type: text/html; charset=utf-8");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoAutorizacaoEmpenho.class.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoEmpenhoAutorizacao.class.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoEmpenho.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoPreEmpenho.class.php';
include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoParticipanteDocumentos.class.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";

//Define o nome dos arquivos PHP
$stPrograma = 'ManterEmpenho';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgPror = 'PO'.$stPrograma.'.php';

$stCtrl = $request->get('stCtrl');

$obREmpenhoAutorizacaoEmpenho = new REmpenhoPreEmpenho;
$obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());

$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());

function montaLista($arRecordSet, $boExecuta = true)
{
    $codItem = false;
    for($i=0;$i<count($arRecordSet);$i++){
            if(isset($arRecordSet[$i]['cod_item'])&&$arRecordSet[$i]['cod_item']!=''){
                $codItem = true;
            }
            break;
    }

    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );
    $rsLista->addFormatacao('vl_total'   , 'NUMERIC_BR');

    $obTable = new TableTree();    
    $obTable->setArquivo( 'OCManterEmpenho.php' );
    $obTable->setParametros( array('cod_item','num_item','cod_marca','nome_marca') );
    $obTable->setComplementoParametros( 'stCtrl=detalharItem' );
    $obTable->setRecordset( $rsLista );
    $obTable->setSummary('Registros');
        
    $obTable->Head->addCabecalho('Descricao'       , 55);
    $obTable->Head->addCabecalho('Valor Unitário'  , 15);
    $obTable->Head->addCabecalho('Quantidade'      , 10);
    $obTable->Head->addCabecalho('Valor Total'     , 15);

    $stTitle = "";
    if ($codItem){
        $obTable->Body->addCampo( '[cod_item] - [nom_item]', "E", $stTitle );        
    }else{
        $obTable->Body->addCampo( 'nom_item' , "E", $stTitle );
    }

    $obTable->Body->addCampo( 'vl_unitario' , "D", $stTitle );
    $obTable->Body->addCampo( 'quantidade'  , "D", $stTitle );
    $obTable->Body->addCampo( 'vl_total'    , "D", $stTitle );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $nuVlTotal = 0;
    foreach ($arRecordSet as $value) {
        $vl_total = str_replace('.','',$value['vl_total']);
        $vl_total = str_replace(',','.',$vl_total);
        $nuVlTotal += $value['vl_total'];
    }
    $nuVlTotal = number_format($nuVlTotal,2,',','.');

    if ($boExecuta) {
        echo "d.getElementById('spnLista').innerHTML = '".$stHTML."';\n
              d.getElementById('nuValorTotal').innerHTML='".$nuVlTotal."';\n
              f.nuVlReserva.value='".$nuVlTotal."';";
    } else {
        return $stHTML;
    }
}

function montaCombo(Request $request)
{
    global $obREmpenhoAutorizacaoEmpenho;
    if ($request->get('inCodDespesa', '') != "") {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($request->get('inCodDespesa'));
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa($rsConta);
        $stCodClassificacao = $rsConta->getCampo('cod_estrutural');
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($stCodClassificacao);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa('');
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa($rsClassificacao);

        if ($rsClassificacao->getNumLinhas() > -1) {
            $inContador = 1;
            $js .= "limpaSelect(f.stCodClassificacao,0); \n";
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
            while (!$rsClassificacao->eof()) {
                $stMascaraReduzida = $rsClassificacao->getCampo("mascara_reduzida");
                if ($stMascaraReduzidaOld) {
                    if ($stMascaraReduzidaOld != substr($stMascaraReduzida,0,strlen($stMascaraReduzidaOld))) {
                        $selected = "";
                        if ($stCodEstruturalOld == $request->get("stCodEstrutural")) {
                            $selected = "selected";
                        }
                        $stOption = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";
                        $js .= "f.stCodClassificacao.options[".$inContador."] = new Option( ".$stOption." ); \n";
                        $inContador++;
                    }
                }
                $inCodContaOld        = $rsClassificacao->getCampo("cod_conta");
                $stCodEstruturalOld   = $rsClassificacao->getCampo("cod_estrutural");
                $stDescricaoOld       = $rsClassificacao->getCampo("descricao");
                $stMascaraReduzidaOld = $stMascaraReduzida;
                $stMascaraReduzida    = "";
                $rsClassificacao->proximo();
            }
            if ($stMascaraReduzidaOld) {
                if ($stCodEstruturalOld == $request->get('stCodEstrutural')) {
                    $selected = "selected";
                }
                $stOption = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";
                $js .= "f.stCodClassificacao.options[".$inContador."] = new Option( ".$stOption." ); \n";
            }
        } else {
            $js .= "limpaSelect(f.stCodClassificacao,0); \n";
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
        }
    } else {
        $js .= "limpaSelect(f.stCodClassificacao,0); \n";
        $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
    }

    return $js;
}

function montaLabel($flSaldoDotacao)
{
    if ($flSaldoDotacao == null) {
        $flSaldoDotacao = '&nbsp;';
    } else {
        $flSaldoDotacao = number_format($flSaldoDotacao,2,',','.');
    }
    $js1.= "d.getElementById('nuSaldoAnterior').innerHTML = '".$flSaldoDotacao."';";

    return $js1;
}

function montaListaDiverso(Request $request, $arRecordSet, $boExecuta = true)
{
    $codUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio());
    $rsLista = new RecordSet;
    for($i=0;$i<count($arRecordSet);$i++){
        if(isset($arRecordSet[$i]['cod_marca'])&&$arRecordSet[$i]['cod_marca']!='')
            $arRecordSet[$i]['nom_item'] .= " ( Marca: ".$arRecordSet[$i]['cod_marca']." - ".$arRecordSet[$i]['nome_marca']." )";
    }

    $rsLista->preenche( $arRecordSet );
    $rsLista->addFormatacao('vl_total', 'NUMERIC_BR');
    if (!$rsLista->eof()) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao(false);
        $obLista->setRecordSet($rsLista);
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(3);
        $obLista->commitCabecalho();
        if ($request->get('stTipoItem')=='Catalogo') {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('Código ');
            $obLista->ultimoCabecalho->setWidth(4);
            $obLista->commitCabecalho(); 
        }
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Descrição ');
        $obLista->ultimoCabecalho->setWidth(50);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Valor Unitário ');
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Quantidade ');
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Valor Total');
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();

        if ($request->get('stAcao') != 'anular') {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('&nbsp;');
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
        }

        if ($request->get('stTipoItem')=='Catalogo') {
            $obLista->addDado();
            $obLista->ultimoDado->setCampo('cod_item');
            $obLista->ultimoDado->setAlinhamento('ESQUERDA');
            $obLista->commitDado();
        }

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('nom_item');
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('vl_unitario');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('quantidade');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('vl_total');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        if ($request->get('stAcao') != 'anular') {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao('ALTERAR');
            $obLista->ultimaAcao->setFuncaoAjax(true);
            $obLista->ultimaAcao->setLink("JavaScript:alterarEmpenho('alterarItemPreEmpenhoDiverso');");
            $obLista->ultimaAcao->addCampo('1', 'num_item');
            if ($request->get('stTipoItem')=='Catalogo') {
                $obLista->ultimaAcao->addCampo('2', 'cod_item');
            }
            $obLista->commitAcao();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao('EXCLUIR');
            $obLista->ultimaAcao->setFuncao(true);
            $obLista->ultimaAcao->setLink("JavaScript:excluirItem('excluirItemPreEmpenhoDiverso');");
            $obLista->ultimaAcao->addCampo('1', 'num_item');
            $obLista->commitAcao();
        }
        $obLista->montaHTML();

        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\r\n" ,"" ,$stHTML );
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        foreach ($arRecordSet as $value) {
            $vl_total = str_replace('.','',$value['vl_total']);
            $vl_total = str_replace(',','.',$vl_total);
            $nuVlTotal += $value['vl_total'];
        }
        $nuVlTotal = number_format($nuVlTotal,2,',','.');

        $stLista    = "d.getElementById('spnLista').innerHTML = '".$stHTML."'; ";
        $stLista   .= "f.Ok.disabled = false; ";
        if ($request->get('stTipoItem')=='Catalogo') {
            $stLista .= "d.getElementById('inCodItem').value = ''; ";
            $stLista .= "d.getElementById('stNomItemCatalogo').innerHTML = '&nbsp;'; ";
            $stLista .= "d.getElementById('stUnidadeMedida').innerHTML = '&nbsp;'; ";
        }else{
            $stLista .= "d.getElementById('stNomItem').innerHTML = '&nbsp;'; ";
        }
        $stVlTotal  = "d.getElementById('nuValorTotal').innerHTML='".$nuVlTotal."'; ";
        $stVlTotal .= "d.getElementById('hdnVlReserva').value= '".$nuVlTotal."'; ";
    } else {
        $stLista    = "d.getElementById('spnLista').innerHTML = ''; ";
        $stLista   .= "f.Ok.disabled = false; ";
        $stVlTotal  = "d.getElementById('nuValorTotal').innerHTML='&nbsp;'; ";
        $stVlTotal .= "d.getElementById('hdnVlReserva').value= ''; ";
        Sessao::remove('arItens');
    }

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stLista.$stVlTotal);
    } else {
        return $stLista.$stVlTotal;
    }
}

function montaComboDiverso(Request $request)
{
    global $obREmpenhoAutorizacaoEmpenho;
    if ($request->get('inCodDespesa', '') != "") {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($request->get('inCodDespesa'));
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa($rsConta);
        $stCodClassificacao = $rsConta->getCampo('cod_estrutural');
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($stCodClassificacao);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa('');
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa($rsClassificacao);
        $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento($stFormaExecucao);

        if ($rsClassificacao->getNumLinhas() > -1) {
            $inContador = 1;
            $js .= "limpaSelect(f.stCodClassificacao,0); \n";
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
            while (!$rsClassificacao->eof()) {
                $stMascaraReduzida = $rsClassificacao->getCampo("mascara_reduzida");
                if ($stMascaraReduzidaOld) {

                    if ($stMascaraReduzidaOld != substr($stMascaraReduzida,0,strlen($stMascaraReduzidaOld))) {
                        $selected = "";
                        if ($stCodEstruturalOld == $request->get("stCodEstrutural")) {
                            $selected = "selected";
                        }

                        $arOptions[]['reduzido']                  = $stMascaraReduzidaOld;
                        $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";

                        $inContador++;
                    }
                }
                $inCodContaOld        = $rsClassificacao->getCampo("cod_conta");
                $stCodEstruturalOld   = $rsClassificacao->getCampo("cod_estrutural");
                $stDescricaoOld       = $rsClassificacao->getCampo("descricao");
                $stMascaraReduzidaOld = $stMascaraReduzida;
                $stMascaraReduzida    = "";
                $rsClassificacao->proximo();
            }
            if ($stMascaraReduzidaOld) {
                if ($stCodEstruturalOld == $request->get('stCodEstrutural')) {
                    $selected = "selected";
                }
                $arOptions[]['reduzido'] = $stMascaraReduzidaOld;
                $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";

            }

            // Remove Contas Sintéticas
            if (is_array($arOptions)) {
                $count = 0;
                for ($x=0; $x<count($arOptions); $x++) {
                    for ($y=0; $y<count($arOptions) ; $y++) {
                        $estruturalX = str_replace('.', '', $arOptions[$x]['reduzido']);
                        $estruturalY = str_replace('.', '', $arOptions[$y]['reduzido']);

                        if ((strpos($estruturalY,$estruturalX)!==false) && ($estruturalX !== $estruturalY)) {
                            $count++;
                        }
                    }
                    if ($count>=1) {
                        unset($arOptions[$x]);
                    }
                    $count = 0;
                }
                if ($stFormaExecucao) {
                    $inContador = 1;
                } else {
                    $inContador = 0;
                }

                asort($arOptions);
                foreach ($arOptions as $option) {
                    $js .= "f.stCodClassificacao.options[".$inContador++."] = new Option(". $option['option'] ."); \n";
                }
            }

        } else {
            $js .= "limpaSelect(f.stCodClassificacao,0); \n";
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
        }
    } else {
        $js .= "limpaSelect(f.stCodClassificacao,0); \n";
        $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
    }

    return $js;
}

function montaLabelDiverso($flSaldoDotacao)
{
    $flSaldoDotacao = number_format($flSaldoDotacao ,2,',','.');

    $obHdnSaldo = new Hidden;
    $obHdnSaldo->setName ("flVlSaldo");
    $obHdnSaldo->setValue($flSaldoDotacao);

    $obLblSaldo = new Label;
    $obLblSaldo->setRotulo("Saldo da Dotação");
    $obLblSaldo->setValue ($flSaldoDotacao);

    $obFormulario = new Formulario;
    $obFormulario->addHidden($obHdnSaldo);
    $obFormulario->addComponente($obLblSaldo);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $js1 = "d.getElementById('spnSaldoDotacao').innerHTML = '".$stHtml."';";

    return $js1;
}

function validaDataFornecedor($inCodFornecedor)
{
    $rsLicitacaoDocumentos = new RecordSet;
    $obTLicitacaoParticipanteDocumentos = new TLicitacaoParticipanteDocumentos;
    $stSql = " AND cgm.numcgm = ".$inCodFornecedor." \n";
    $obTLicitacaoParticipanteDocumentos->recuperaDocumentoParticipante($rsRecordSet, $stSql);

    while (!$rsRecordSet->eof()) {
        $comparaData = SistemaLegado::comparaDatas($rsRecordSet->getCampo("dt_validade"),date('d/m/Y'));
        if (!$comparaData) {
            echo "jq('#boMsgValidadeFornecedor').val('true');";
        }
        $rsRecordSet->proximo();
    }
}

function validaContrato($inCodEntidade=null, $inCodFornecedor=null, $inNumContrato=null, $stExercicioContrato=null){
    $stHTML      = '';
    $stObjeto    = '&nbsp;';
    $codContrato = '';
    $dtContrato  = '';
    $stJs        = '';

    if( $inNumContrato ){
        if( $stExercicioContrato ){
            if( $stExercicioContrato <= Sessao::getExercicio() ){
                if( $inCodEntidade && $inCodFornecedor ){
                    require_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoContrato.class.php';
                    require_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoContratoAditivos.class.php';

                    $obTLicitacaoContrato = new TLicitacaoContrato;
                    $obTLicitacaoContratoAditivos = new TLicitacaoContratoAditivos;

                    $rsContrato = new RecordSet;
                    $stFiltro  = " AND contrato.num_contrato    = ".$inNumContrato;
                    $stFiltro .= " AND contrato.exercicio       = '".$stExercicioContrato."'";
                    $stFiltro .= " AND contrato.cgm_contratado  = ".$inCodFornecedor;
                    $stFiltro .= " AND contrato.cod_entidade    = ".$inCodEntidade;
                    $obTLicitacaoContrato->recuperaContrato($rsContratoObjeto, $stFiltro);

                    $obTLicitacaoContrato->recuperaDadosContrato($rsContrato, $stFiltro);

                    if($rsContrato->getNumLinhas()<1){
                        $stObjeto = '&nbsp;';
                        $stJs .= "alertaAviso('@Código do Contrato(".$inNumContrato.") não encontrado.', 'form','erro','".Sessao::getId()."'); \n";
                    }else{
                        $stObjeto = $rsContratoObjeto->getCampo('descricao');
                        $stObjeto = str_replace("\n", ' ', $stObjeto);
                        $stObjeto = str_replace("\r", ' ', $stObjeto);
                        $stObjeto = str_replace('  ', ' ', $stObjeto);

                        $obLblData = new Label;
                        $obLblData->setRotulo('Data do Contrato');
                        $obLblData->setValue($rsContrato->getCampo('dt_assinatura'));
    
                        $dtContrato = $rsContrato->getCampo('dt_assinatura');

                        $obLblValor = new Label;
                        $obLblValor->setRotulo('Valor');
                        $obLblValor->setValue(number_format($rsContrato->getCampo('valor_contratado'),2,',','.'));

                        $stFiltro  = " WHERE num_contrato      = ".$inNumContrato;
                        $stFiltro .= " AND exercicio_contrato  = '".$stExercicioContrato."'";
                        $stFiltro .= " AND cod_entidade        = ".$inCodEntidade;
                        $stOrdem = " ORDER BY exercicio, num_aditivo";
                        $obTLicitacaoContratoAditivos->recuperaTodos($rsAditivo, $stFiltro, $stOrdem);

                        $obCmbContratoAditivo = new Select;
                        $obCmbContratoAditivo->setRotulo ('Aditivo');
                        $obCmbContratoAditivo->setName('inNumAditivo');
                        $obCmbContratoAditivo->setId('inNumAditivo');
                        $obCmbContratoAditivo->setCampoId('[num_aditivo]/[exercicio]');
                        $obCmbContratoAditivo->setCampoDesc('[num_aditivo]/[exercicio]');
                        $obCmbContratoAditivo->addOption('', 'Selecione');
                        $obCmbContratoAditivo->preencheCombo($rsAditivo);
                        $obCmbContratoAditivo->setNull(true);
                        $obCmbContratoAditivo->obEvento->setOnChange("montaParametrosGET('buscaAditivo', 'inCodContrato,inCodEntidade,inCodFornecedor,stExercicioContrato,inNumAditivo');");

                        $obSpanAditivo = new Span;
                        $obSpanAditivo->setId('spnAditivo');

                        $obForm = new Form;
                        $obForm->setName("frm2");

                        $obFormulario = new Formulario;
                        $obFormulario->addForm  ($obForm);
                        $obFormulario->addComponente($obLblData);
                        $obFormulario->addComponente($obLblValor);
                        $obFormulario->addComponente($obCmbContratoAditivo);
                        $obFormulario->addSpan      ($obSpanAditivo);
                        $obFormulario->montaInnerHTML();

                        if($rsContrato->getNumLinhas()==1){
                            $stHTML = $obFormulario->getHTML();
                            $stHTML = str_replace( "\n"     , ""    , $stHTML );
                            $stHTML = str_replace( chr(13)  , "<br>", $stHTML );
                            $stHTML = str_replace( "  "     , ""    , $stHTML );
                            $stHTML = str_replace( "'"      , "\\'" , $stHTML );
                            $stHTML = str_replace( "\\\'"   , "\\'" , $stHTML );
                        }
                        $codContrato=$inNumContrato;
                    }
                }else{
                    if(!$inCodEntidade)
                        $stJs .= "alertaAviso('@Selecione a Entidade do Empenho.', 'form','erro','".Sessao::getId()."');    \n";
                    else
                        $stJs .= "alertaAviso('@Selecione o Fornecedor do Empenho.', 'form','erro','".Sessao::getId()."');  \n";
                }
            }else{
                $stJs .= "alertaAviso('@O Exercício do Contrato não pode ser superior ao Exercício do Empenho.', 'form','erro','".Sessao::getId()."');  \n";
            }
        }else{
            $stJs .= "alertaAviso('@Informe o Exercício do Contrato.', 'form','erro','".Sessao::getId()."');  \n";
        }
    }

    $stJs .= "if(d.getElementById('inCodContrato')){                               \n";
    $stJs .= "  d.getElementById('inCodContrato').value = '".$codContrato."';      \n";
    $stJs .= "  d.getElementById('txtContrato').innerHTML = '".$stObjeto."';       \n";
    $stJs .= "  d.getElementById('dtContrato').value = '".$dtContrato."';          \n";
    $stJs .= "  d.getElementById('spnInfoAdicional').innerHTML = '".$stHTML."';    \n";
    $stJs .= "}                                                                    \n";

    return $stJs;
}

function LiberaDataEmpenho($boLibera = 'true'){
    $js  = "setLabel('stDtEmpenho', ".$boLibera."); ";
    $js .= "jQuery('#stDtEmpenho_label').html(jQuery('#stDtEmpenho').val());";

    return $js;
}

$inCodEntidade = $request->get('inCodEntidade');

switch ($stCtrl) {
    case 'montaListaItemPreEmpenho':
        montaLista(Sessao::read('arItens'),true);
    break;
     case 'verificaFornecedor':
        if ($request->get('inCodFornecedor', '') != "") {
            validaDataFornecedor($request->get('inCodFornecedor'));
            if ($request->get('inCodFornecedor') && $request->get('inCodContrapartida') && ( $request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3)) {
                $boPendente = false;
                include_once TEMP."TEmpenhoResponsavelAdiantamento.class.php";
                $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
                $obTEmpenhoResponsavelAdiantamento->setDado('exercicio',Sessao::getExercicio());
                $obTEmpenhoResponsavelAdiantamento->setDado('numcgm',$request->get('inCodFornecedor'));
                $obTEmpenhoResponsavelAdiantamento->setDado('conta_contrapartida',$request->get('inCodContrapartida'));
                $obTEmpenhoResponsavelAdiantamento->consultaEmpenhosFornecedor($rsVerificaEmpenho);

                if ($rsVerificaEmpenho->getNumLinhas() > 0) {
                    while (!$rsVerificaEmpenho->eof()) {
                        if (SistemaLegado::comparaDatas($request->get('stDtEmpenho'),$rsVerificaEmpenho->getCampo('dt_prazo_prestacao'))) {
                               $boPendente = true;
                        }
                        $rsVerificaEmpenho->Proximo();
                    }
                    if ($boPendente) {
                        echo " alertaAviso('@O responsável por adiantamento informado possui prestação de contas pendentes.','form','erro','".Sessao::getId()."'); ";
                    } else {
                        echo " alertaAviso('','','','".Sessao::getId()."'); ";
                    }
                }
            }
        }

        if($request->get('stDtEmpenho')){
            $js  = "if(d.getElementById('inCodContrato')){                        \n";
            $js .= "    d.getElementById('inCodContrato').value='';               \n";
            $js .= "    d.getElementById('txtContrato').innerHTML = '&nbsp;';     \n";
            $js .= "    d.getElementById('dtContrato').value = '';                \n";
            $js .= "    d.getElementById('spnInfoAdicional').innerHTML = '';      \n";
            $js .= "}                                                             \n";

            echo $js;
        }

    break;

    case 'buscaContrapartida':
        if ($request->get('inCodFornecedor') && ( $request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3)) {
            include_once TEMP.'TEmpenhoResponsavelAdiantamento.class.php';
            $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
            $obTEmpenhoResponsavelAdiantamento->setDado("exercicio", Sessao::getExercicio());
            $obTEmpenhoResponsavelAdiantamento->setDado("numcgm"   , $request->get('inCodFornecedor'));
            $obTEmpenhoResponsavelAdiantamento->recuperaContrapartidaLancamento($rsContrapartida);

            if ($rsContrapartida->getNumLinhas() > 0) {
                $obCmbContrapartida = new Select;
                $obCmbContrapartida->setRotulo    ('Contrapartida'                      );
                $obCmbContrapartida->setTitle     ('Informe a contrapartida.'           );
                $obCmbContrapartida->setName      ('inCodContrapartida'                 );
                $obCmbContrapartida->setId        ('inCodContrapartida'                 );
                $obCmbContrapartida->setNull      (false                                );
                $obCmbContrapartida->setValue     ($inCodContrapartida                  );
                $obCmbContrapartida->setStyle     ('width: 600'                         );
                $obCmbContrapartida->addOption    ('', 'Selecione'                      );
                $obCmbContrapartida->setCampoId   ('conta_contrapartida'                );
                $obCmbContrapartida->setCampoDesc ("[conta_contrapartida] - [nom_conta]");
                $obCmbContrapartida->preencheCombo($rsContrapartida                     );
                $obCmbContrapartida->obEvento->setOnChange("montaParametrosGET('verificaFornecedor','inCodFornecedor,inCodContrapartida,inCodCategoria');");

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

        echo $js;
    break;

    case 'buscaDespesa':
        if ($request->get("inCodDespesa", "") != "" and $inCodEntidade != "") {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($request->get("inCodDespesa"));
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($request->get("inCodEntidade"));
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->consultarDotacao($rsDespesa);
            $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnterior($nuSaldoDotacao);
            $stNomDespesa = $rsDespesa->getCampo('descricao');

            if (!$stNomDespesa) {
                $js .= 'f.inCodDespesa.value = "";';
                $js .= 'window.parent.frames["telaPrincipal"].document.frm.inCodDespesa.focus();';
                $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$request->get("inCodDespesa").")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomDespesa").innerHTML = "'.$stNomDespesa.'";';
                $js .= 'd.getElementById("inCodOrgao").innerHTML   = "'.$rsDespesa->getCampo("num_orgao")  .' - '.trim($rsDespesa->getCampo("nom_orgao")  ).'";';
                $js .= 'd.getElementById("inCodUnidade").innerHTML = "'.$rsDespesa->getCampo("num_unidade").' - '.trim($rsDespesa->getCampo("nom_unidade")).'";';
            }
        } else {
            $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
        }
        $js .= montaLabel($nuSaldoDotacao);
        $js .= montaCombo($request);
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'verificaDataEmpenho':
        if ($request->get("stDtEmpenho", "") != "" and $inCodEntidade != "") {
            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
            $obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoEmpenho->listarMaiorData($rsMaiorData);

            $stMaiorData = $rsMaiorData->getCampo('dataempenho');

            $stDataAtual = date("d") . "/" . date("m") . "/" . date("Y");
            if (SistemaLegado::comparaDatas($rsMaiorData->getCampo( "dataempenho" ),$request->get("stDtEmpenho"))) {
                $js .= "f.stDtEmpenho.value='".$rsMaiorData->getCampo( "dataempenho" )."';";
                $js .= 'window.parent.frames["telaPrincipal"].document.frm.stDtEmpenho.focus();';
                $js .= "alertaAviso('@Data de Empenho deve ser maior ou igual a ".$rsMaiorData->getCampo('dataempenho')." !','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'verificaDataEmpenhoAutorizacao':
        if ($request->get("stDtEmpenho", "") != "" and $inCodEntidade != "") {
            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
            $obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoEmpenho->listarMaiorData($rsMaiorData ,'',$boTransacao, $request->get('stDtAutorizacao'));

            $stMaiorData = $rsMaiorData->getCampo('dataempenho');

            $stDataAtual = date("d") . "/" . date("m") . "/" . date("Y");
            if (SistemaLegado::comparaDatas($rsMaiorData->getCampo( "dataempenho" ),$request->get("stDtEmpenho"))) {
                $js .= "f.stDtEmpenho.value='" . $rsMaiorData->getCampo( "dataempenho" ) . "';";
                $js .= 'window.parent.frames["telaPrincipal"].document.frm.stDtEmpenho.focus();';
                $js .= "alertaAviso('@Data de Empenho deve ser maior ou igual a ".$rsMaiorData->getCampo('dataempenho')." !','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'buscaDtEmpenho':;
        include_once CAM_GF_EMP_NEGOCIO.'REmpenhoConfiguracao.class.php';

        $obErro = new Erro;
        $obREmpenhoConfiguracao = new REmpenhoConfiguracao();
        $obREmpenhoConfiguracao->consultar();

        $obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());

        $js  = "jq('#stDtEmpenho').val('');";
        $js .= LiberaDataEmpenho();
        $js .= "LiberaFrames(true,false);\n";

        if ($obREmpenhoConfiguracao->getNumeracao() == 'P') {
            if ($request->get('inCodEntidade', '') != "") {
                if ($inCodEntidade) {
                    $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
                    $obTAdministracaoConfiguracaoEntidade->setDado("exercicio"    , Sessao::getExercicio());
                    $obTAdministracaoConfiguracaoEntidade->setDado("cod_modulo"   , 10);
                    $obTAdministracaoConfiguracaoEntidade->setDado("cod_entidade" , $inCodEntidade);
                    $obTAdministracaoConfiguracaoEntidade->setDado("parametro"    , "data_fixa_empenho");
                    $obTAdministracaoConfiguracaoEntidade->recuperaPorChave($rsConfiguracao);
                    $stDtFixaEmpenho = trim($rsConfiguracao->getCampo('valor'));

                    $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
                    $obErro = $obREmpenhoEmpenho->recuperaUltimoEmpenho($rsUltimoEmpenho);
                    $dtUltimaDataEmpenho = "01/01/".Sessao::getExercicio();
                    if (!$obErro->ocorreu() && $rsUltimoEmpenho->getNumLinhas() >= 1) {
                        if ($rsUltimoEmpenho->getCampo("dt_empenho")!="") {
                            $dtUltimaDataEmpenho = SistemaLegado::dataToBr($rsUltimoEmpenho->getCampo("dt_empenho"));
                        }
                    }
                    $js .= "f.dtUltimaDataEmpenho.value = '".$dtUltimaDataEmpenho."';";

                    if(!empty($stDtFixaEmpenho)){
                        $js .= "jq('#stDtEmpenho').val('".$stDtFixaEmpenho."');";
                        $js .= LiberaDataEmpenho('false');
                    }
                    else if (!$obErro->ocorreu()) {
                        $obErro = $obREmpenhoEmpenho->listarMaiorData($rsMaiorData);
                        if (!$obErro->ocorreu()) {
                            $stDtEmpenho = $rsMaiorData->getCampo( "dataempenho" );
                            if ($stDtEmpenho) {
                                $js .= "f.stDtEmpenho.value='".$stDtEmpenho."';\n";
                                $js .= "f.inCodDespesa.focus();\n";
                            } else {
                                $js .= "f.stDtEmpenho.value='01/01/".Sessao::getExercicio()."';\n";
                            }
                        }
                    }
                } else {
                    $js .= "f.stDtEmpenho.value='".date("d/m/Y")."';\n";
                }
            }
        } else {
            $obErro = $obREmpenhoEmpenho->recuperaUltimoEmpenho($rsUltimoEmpenho);
            $dtUltimaDataEmpenho = "01/01/".Sessao::getExercicio();
            if (!$obErro->ocorreu() && $rsUltimoEmpenho->getNumLinhas() >= 1) {
                if ($rsUltimoEmpenho->getCampo("dt_empenho")!="") {
                    $dtUltimaDataEmpenho = SistemaLegado::dataToBr($rsUltimoEmpenho->getCampo("dt_empenho"));
                }
            }
            $js .= "f.dtUltimaDataEmpenho.value='".$dtUltimaDataEmpenho."';";
            if (!$obErro->ocorreu) {
                $obErro = $obREmpenhoEmpenho->listarMaiorData($rsMaiorData);
                if (!$obErro->ocorreu()) {
                    $stDtEmpenho = $rsMaiorData->getCampo('dataempenho');
                    if ($stDtEmpenho) {
                        $js .= "f.stDtEmpenho.value='".$stDtEmpenho."';\n";
                        $js .= "f.inCodDespesa.focus();\n";
                    } else {
                         $js .= "f.stDtEmpenho.value='01/01/".Sessao::getExercicio()."';\n";
                    }
                }
            }
        }

        $js .= validaContrato($request->get('inCodEntidade'), $request->get('inCodFornecedor'), $request->get('inCodContrato'), $request->get('stExercicioContrato'));

        echo $js;
    break;

    case "buscaOrgaoUnidadeDiverso":
        if ($request->get('inCodOrgao', '') != "") {
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $request->get('inCodOrgao') );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarUnidadeDespesaEntidadeUsuario( $rsUnidade, "ou.num_orgao, ou.num_unidade");
            if ($rsUnidade->getNumLinhas() > -1) {
                $inContador = 1;
                $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
                $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');\n";
                while (!$rsUnidade->eof()) {
                    $inCodUnidade = $rsUnidade->getCampo("num_unidade");
                    $stNomUnidade = $rsUnidade->getCampo("num_unidade")." - ".$rsUnidade->getCampo("nom_unidade");
                    $selected     = '';

                    $js .= "f.inCodUnidadeOrcamento.options[".$inContador."] = new Option('".$stNomUnidade."','".$inCodUnidade."','".$selected."'); \n";

                    $inContador++;
                    $rsUnidade->proximo();
                }
            } else {
                $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
                $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');\n";
            }
        } else {
            $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
            $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');\n";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'buscaDespesaDiverso':
        if ($request->get("inCodDespesa", "") != "" and $inCodEntidade != "") {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get("inCodDespesa") );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $request->get("inCodEntidade") );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );

            $stNomDespesa = $rsDespesa->getCampo('descricao');

            if (!$stNomDespesa) {
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesa($rsDespesa2);
                $stNomDespesa2 = $rsDespesa2->getCampo('descricao');

                if (!$stNomDespesa2) {
                    $js .= "f.inCodDespesa.value='';";
                    $js .= "f.inCodDespesa.focus();";
                    $js .= "d.getElementById('stNomDespesa').innerHTML='';";
                    $js .= "alertaAviso('@Valor inválido. (".$request->get('inCodDespesa').")','form','erro','".Sessao::getId()."');";
                } else {
                    $js .= "f.inCodDespesa.value='';";
                    $js .= "f.inCodDespesa.focus();";
                    $js .= "d.getElementById('stNomDespesa').innerHTML='&nbsp;';";
                    $js .= "alertaAviso('@Você não possui permissão para esta dotação. (".$request->get('inCodDespesa').")', 'form', 'erro', '".Sessao::getId()."');";
                }
                $js .= "d.getElementById('stOrgaoOrcamento').innerHTML='';";
                $js .= "f.hdnOrgaoOrcamento.value='';";
                $js .= "d.getElementById('stUnidadeOrcamento').innerHTML='';";
                $js .= "f.hdnUnidadeOrcamento.value='';";
            } else {
                $stNomDespesa = $rsDespesa->getCampo( "descricao" );
                $js .= "d.getElementById('stNomDespesa').innerHTML='".$stNomDespesa."';";
                $js .= montaComboDiverso($request);
            }
        } else
            $js .= "d.getElementById('stNomDespesa').innerHTML='&nbsp;';";

        if ($request->get('inCodDespesa') != '' and $stNomDespesa) {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($request->get('inCodDespesa'));
            $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->setdataEmpenho($request->get('stDtEmpenho'));
            $obREmpenhoAutorizacaoEmpenho->setCodEntidade($request->get('inCodEntidade'));
            $obREmpenhoAutorizacaoEmpenho->setTipoEmissao('R');
            $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnteriorDataEmpenho($nuSaldoDotacao);

            $js .= montaLabelDiverso($nuSaldoDotacao);

            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($request->get('inCodDespesa'));
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($request->get('inCodEntidade'));
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario($rsDespesa);

            $inNumOrgao   = $rsDespesa->getCampo('num_orgao');
            $inNumUnidade = $rsDespesa->getCampo('num_unidade');

            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($inNumOrgao);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade($inNumUnidade);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario($rsOrgao);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarUnidadeDespesaEntidadeUsuario($rsUnidade);

            $inCodOrgao   = $rsOrgao->getCampo('num_orgao');
            $stNomOrgao   = $rsOrgao->getCampo('nom_orgao');
            $inCodUnidade = $rsUnidade->getCampo('num_unidade');
            $stNomUnidade = $rsUnidade->getCampo('nom_unidade');

            $js .= "d.getElementById('stOrgaoOrcamento').innerHTML='".$inCodOrgao. " - ".trim($stNomOrgao)."';";
            $js .= "f.hdnOrgaoOrcamento.value='".$inCodOrgao."';";
            $js .= "d.getElementById('stUnidadeOrcamento').innerHTML='".$inCodUnidade." - ".trim($stNomUnidade)."';";
            $js .= "f.hdnUnidadeOrcamento.value='".$inCodUnidade."';";
        } else {
            $js .= "d.getElementById('spnSaldoDotacao').innerHTML='';";
        }
        $js .= montaComboDiverso($request);
        $js .= "LiberaFrames(true,false);";

        SistemaLegado::executaFrameOculto($js);
    break;

    case 'buscaClassificacaoDiverso':
        if ($request->get("stCodClassificacao", "") != "") {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $request->get("stCodClassificacao") );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa( $rsClassificacao );
            $stNomClassificacao = $rsClassificacao->getCampo( "descricao" );
            if (!$stNomClassificacao) {
                $js .= "f.stCodClassificacao.value='';";
                $js .= "f.stCodClassificacao.focus();";
                $js .= "d.getElementById('stNomClassificacao').innerHTML='&nbsp;'";
                $js .= "alertaAviso('@Valor inválido. (".$request->get("stCodClassificacao").")', 'form', 'erro', '" . Sessao::getId() . "');";
            } else {
                $js .= "d.getElementById('stNomClassificacao').innerHTML='".$stNomClassificacao."';";
            }
        } else {
            $js .= "d.getElementById('stNomClassificacao').innerHTML='&nbsp;';";
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'buscaFornecedorDiverso':
        if ($request->get("inCodFornecedor", "") != "") {
            $obREmpenhoAutorizacaoEmpenho->obRCGM->setNumCGM($request->get("inCodFornecedor"));
            $obREmpenhoAutorizacaoEmpenho->obRCGM->listar($rsCGM);
            $stNomFornecedor = trim($rsCGM->getCampo('nom_cgm'));
            if (!$stNomFornecedor) {
                $js .= 'f.inCodFornecedor.value = "";';
                $js .= 'f.inCodFornecedor.focus();';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$request->get("inCodFornecedor").")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
            }
        } else {
            $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
        }
        echo $js;
    break;

    case 'incluiItemPreEmpenhoDiverso':
        $inCount   = sizeof(Sessao::read('arItens'));
        $nuVlTotal = str_replace('.','',$request->get('nuVlTotal'));
        $nuVlTotal = str_replace(',','.',$nuVlTotal);
        if($request->get('stTipoItem')=='Catalogo'){
            list($inCodUnidade, $inCodGrandeza) = explode("-",$request->get('inCodUnidadeMedida'));
            $stNomUnidade = $request->get('stNomUnidade');
        }else{
            list($inCodUnidade, $inCodGrandeza, $stNomUnidade) = explode("-",$request->get('inCodUnidade'));
        }        
        $arItens = Sessao::read('arItens');        
        $arItens[$inCount]['num_item']     = $inCount+1;
        if ($request->get('stTipoItem')=='Catalogo') {
            foreach ($arItens as $key => $valor) {
                if ($valor['cod_item'] == $request->get('inCodItem')) {
                    $erro=true;
                }
            }
            $arItens[$inCount]['cod_item'] = $request->get('inCodItem');
            $arItens[$inCount]['nom_item'] = $request->get('stNomItemCatalogo');
        }else{
            $arItens[$inCount]['nom_item'] = $request->get('stNomItem');
        }
        $arItens[$inCount]['complemento']  = $request->get('stComplemento');
        $arItens[$inCount]['quantidade']   = $request->get('nuQuantidade');
        $arItens[$inCount]['vl_unitario']  = $request->get('nuVlUnitario');
        $arItens[$inCount]['cod_unidade']  = $inCodUnidade;
        $arItens[$inCount]['cod_grandeza'] = $inCodGrandeza;
        $arItens[$inCount]['nom_unidade']  = $stNomUnidade;
        $arItens[$inCount]['cod_marca']    = $request->get('inMarca');
        $arItens[$inCount]['nome_marca']   = $request->get('stNomeMarca');
        $arItens[$inCount]['vl_total']     = $nuVlTotal;

        if($erro){
            $js = "alertaAviso('Item(".$request->get('inCodItem').") Já Incluso na Lista.','frm','erro','".Sessao::getId()."'); \n";
            SistemaLegado::executaFrameOculto($js);
        }else{
            Sessao::write('arItens', $arItens);
            $stHTML = montaListaDiverso( $request, Sessao::read('arItens') );
        }
    break;

    case 'excluirItemPreEmpenhoDiverso':
        $arTEMP = array();
        $inCount = 0;
        $arItens = array();
        $arItens = Sessao::read('arItens');

        for($i=0;$i<count($arItens);$i++){
            if($arItens[$i]['num_item']!=$request->get('inNumItem')){
                $arTEMP[$inCount]['num_item']     = $inCount+1;

                if($request->get('stTipoItem')=='Catalogo'){
                    $arTEMP[$inCount]['cod_item'] = $arItens[$i]['cod_item'];
                }
                $arTEMP[$inCount]['nom_item']     = $arItens[$i]['nom_item'];
                $arTEMP[$inCount]['complemento']  = $arItens[$i]['complemento'];
                $arTEMP[$inCount]['quantidade']   = $arItens[$i]['quantidade'];
                $arTEMP[$inCount]['cod_unidade']  = $arItens[$i]['cod_unidade'];
                $arTEMP[$inCount]['nom_unidade']  = $arItens[$i]['nom_unidade'];
                $arTEMP[$inCount]['cod_grandeza'] = $arItens[$i]['cod_grandeza'];
                $arTEMP[$inCount]['cod_marca']    = $arItens[$i]['cod_marca'];
                $arTEMP[$inCount]['nome_marca']   = $arItens[$i]['nome_marca'];
                $arTEMP[$inCount]['vl_total']     = $arItens[$i]['vl_total'];
                $arTEMP[$inCount]['vl_unitario']  = $arItens[$i]['vl_unitario'];
                $inCount++;
            }
        }
        Sessao::write('arItens', $arTEMP);
        montaListaDiverso($request, Sessao::read('arItens'));
        if(count($arTEMP)==0){
            $js .= "d.getElementById('stTipoItemRadio1').disabled = false;";
            $js .= "d.getElementById('stTipoItemRadio2').disabled = false;";
            SistemaLegado::executaFrameOculto($js);
        }
    break;

    case 'montaListaItemPreEmpenhoDiverso':
        $js  = montaListaDiverso($request, Sessao::read('arItens'), false);
        $js .= montaCombo($request);
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'alterarDiverso':
        $js  = montaLista(Sessao::read('arItens'), false);
        $js .= montaCombo($request);
    break;

    case "alterarItemPreEmpenhoDiverso":
        $arItens = array();
        $arItens = Sessao::read('arItens');

        foreach ($arItens as $valor) {
            if ($valor['num_item'] == $request->get('num_item')) {
                $stJs .= "f.hdnNumItem.value='".$request->get('num_item')."';";
                if ($request->get('cod_item')) {
                    $stJs .= "f.inCodItem.value= '".$valor['cod_item']."';";
                    $stJs .= "f.HdninCodItem.value= '".$valor['cod_item']."';";
                    $stJs .= "f.stNomItemCatalogo.value ='".$valor["nom_item"]."';";
                    $stJs .= "d.getElementById('stNomItemCatalogo').innerHTML ='".$valor["nom_item"]."';";
                    $stJs .= "f.inCodUnidadeMedida.value= '".$valor["cod_unidade"]."-". $valor["cod_grandeza"]."';";
                    $stJs .= "f.stNomUnidade.value= '".$valor["nom_unidade"]."';";
                }else{
                    $stJs .= "f.stNomItem.value='".$valor["nom_item"]."';";
                }
                $stJs .= "f.stComplemento.value='".htmlentities($valor["complemento"], ENT_QUOTES)."';";
                $stJs .= "f.nuQuantidade.value='".$valor["quantidade"]."';";
                $stJs .= "f.nuVlUnitario.value='".$valor["vl_unitario"]."';";
                $stJs .= "f.nuVlTotal.value='".number_format($valor["vl_total"],2,',','.')."';";
                $stJs .= "f.btnIncluir.value='Alterar';";
                $stJs .= "f.btnIncluir.setAttribute('onclick','return alterarItem()');";
                $stJs .= "f.stNomItem.value = f.stNomItem.value.unescapeHTML();";
                $stJs .= "f.stComplemento.value = f.stComplemento.value.unescapeHTML();\n";
                $stJs .= "jq('#inMarca').val('".$valor["cod_marca"]."');\n";
                $stJs .= "jq('#stNomeMarca').html('".$valor["nome_marca"]."');\n";
                $stJs .= "jq('input[name=stNomeMarca]').val('".$valor["nome_marca"]."');\n";

                $value = $valor["cod_unidade"]."-". $valor["cod_grandeza"]."-". $valor["nom_unidade"];
                $stJs .= "f.inCodUnidade.value='".$value."';";
                $stJs .= 'window.parent.frames["telaPrincipal"].document.frm.inCodItem.focus();';
            }
        }
        echo $stJs;
    break;

    case "alteradoItemPreEmpenhoDiverso":
        $arItens = array();
        $arItens = Sessao::read('arItens');

        foreach ($arItens as $key => $valor) {
            if ($valor['num_item'] == $request->get('hdnNumItem')) {
                for($i=0;$i<count($arItens);$i++){
                    if($request->get('stTipoItem')=='Catalogo'&&($arItens[$i]['cod_item'] == $request->get('inCodItem'))&&($arItens[$i]['num_item'] != $request->get('hdnNumItem'))){
                        $erro=true;
                    }
                }

                    if(!$erro){
                        if($request->get('stTipoItem')=='Catalogo'){
                            list($inCodUnidade, $inCodGrandeza) = explode("-",$request->get('inCodUnidadeMedida'));
                            $stNomUnidade = $request->get('stNomUnidade');
                            $arItens[$key]['cod_item']    = $request->get('inCodItem');
                            $arItens[$key]['nom_item']    = stripslashes($request->get('stNomItemCatalogo'));
                        }else{
                            list($inCodUnidade, $inCodGrandeza, $stNomUnidade) = explode("-",$request->get('inCodUnidade'));
                            $arItens[$key]['nom_item'   ] = stripslashes($request->get("stNomItem"));
                        }

                        $arItens[$key]['complemento'] = stripslashes($request->get("stComplemento"));
                        $arItens[$key]['quantidade' ] = $request->get("nuQuantidade");
                        $arItens[$key]['cod_unidade'] = $inCodUnidade;
                        $arItens[$key]['vl_unitario'] = $request->get("nuVlUnitario");
                        $arItens[$key]['nom_unidade'] = $stNomUnidade;
                        $arItens[$key]['cod_grandeza'] = $inCodGrandeza;
                        $arItens[$key]['cod_marca']    = $request->get('inMarca');
                        $arItens[$key]['nome_marca']   = $request->get('stNomeMarca');

                        $nuVlTotal = str_replace('.','',$request->get("nuVlTotal"));
                        $nuVlTotal = str_replace(',','.',$nuVlTotal);

                        $arItens[$key]['vl_total'] = $nuVlTotal;
                        break;
                    }
            }else{
                if($request->get('stTipoItem')=='Catalogo'&&($valor['cod_item'] == $request->get('inCodItem'))){
                    $erro=true;
                }
            }
        }

        if($erro){
            $js = "alertaAviso('Item(".$request->get('inCodItem').") Já Incluso na Lista.','frm','erro','".Sessao::getId()."'); \n";
            SistemaLegado::executaFrameOculto($js);
        }else{
            Sessao::write('arItens', $arItens);
            $stJs.= "f.btnIncluir.setAttribute('onclick','return incluirItem()');";

            echo montaListaDiverso($request, Sessao::read('arItens'));
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;

    case 'buscaEmpenho':
        if ($request->get("inCodigoEmpenho") && $request->get("inCodEntidade")) {
            Sessao::remove('arItens');

            $obREmpenhoEmpenho = new REmpenhoEmpenho;

            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($request->get("inCodEntidade"));
            $obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoEmpenho->setCodEmpenhoInicial($request->get("inCodigoEmpenho"));
            $obREmpenhoEmpenho->setCodEmpenhoFinal($request->get("inCodigoEmpenho"));
            $obREmpenhoEmpenho->setSituacao(5);

            $obREmpenhoEmpenho->listar($rsLista);

            if ($rsLista->getNumLinhas() > 0) {
                $obREmpenhoEmpenho->setCodEmpenho($request->get("inCodigoEmpenho"));
                $obREmpenhoEmpenho->consultar();
                $stNomFornecedor = ($rsLista->getCampo('nom_fornecedor')) ? str_replace( "'","\'",$rsLista->getCampo("nom_fornecedor")):'&nbsp;';
                $js .= "d.getElementById('stNomFornecedor').innerHTML='".$stNomFornecedor."';";

                $stNomCategoria = $obREmpenhoEmpenho->getNomCategoria();
                $inCodDespesa   = $obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
                $stNomDespesa   = $obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getDescricao();
                $inCodHistorico = $obREmpenhoEmpenho->obREmpenhoHistorico->getCodHistorico();
                $stNomHistorico = str_replace ( '\\','',$obREmpenhoEmpenho->obREmpenhoHistorico->getNomHistorico());
                $stCodClassificacao = $obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
                $stNomClassificacao = $obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getDescricao();

                $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;
                $obTEmpenhoPreEmpenho->setDado("exercicio", Sessao::getExercicio());
                $obTEmpenhoPreEmpenho->setDado("cod_despesa", $inCodDespesa);
                $obErro = $obTEmpenhoPreEmpenho->recuperaSaldoAnterior($rsRecordSet, $stOrder, $boTransacao);
                if (!$obErro->ocorreu()) {
                    $nuValorSaldoAnterior = $rsRecordSet->getCampo('saldo_anterior');
                }

                $inNumUnidade = $obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
                $stNomUnidade = $obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
                $inNumOrgao   = $obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
                $stNomOrgao   = $obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
                $inCodFornecedor = $obREmpenhoEmpenho->obRCGM->getNumCGM();
                $stNomFornecedor = $obREmpenhoEmpenho->obRCGM->getnomCGM();

                $stNomCategoria = $obREmpenhoEmpenho->getNomCategoria();
                $inCodCategoria = $obREmpenhoEmpenho->getcodCategoria();
                $stDescricao = $obREmpenhoEmpenho->getDescricao();
                $stDtVencimento = $obREmpenhoEmpenho->getDtVencimento();
                $stDtEmpenho = $obREmpenhoEmpenho->getDtEmpenho();
                $inCodContrapartida = $obREmpenhoEmpenho->getCodContrapartida();
                $stNomContrapartida = $obREmpenhoEmpenho->getNomContrapartida();
                $inCodTipo = $obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();
                $stNomTipo = $obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();

                $flVlSaldo = number_format($nuValorSaldoAnterior,2,',','.');

                $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario( $rsOrgao );
                $hdnOrgaoOrcamento   = $rsOrgao->getCampo('num_orgao');
                $hdnUnidadeOrcamento = $obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade();

                if ($inCodDespesa != $inCodDespesaAnterior) {
                    $inCodDespesaAnterior =  $obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
                }

                $arChaveAtributo =  array( "cod_pre_empenho" => $obREmpenhoEmpenho->getCodPreEmpenho(),
                                           "exercicio"       => Sessao::getExercicio());
                $obREmpenhoEmpenho->obRCadastroDinamico->setChavePersistenteValores($arChaveAtributo);
                $obREmpenhoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsAtributos);

                $arAtributosModificados = array();
                $arAtributosOriginais = $rsAtributos->arElementos;

                for ($w=0; $w < count($arAtributosOriginais); $w++) {
                    if ($arAtributosOriginais[$w]['cod_atributo'] == 101) { // Atributo : Modalidade
                        $obLblModalidade = new Label;
                        $obLblModalidade->setRotulo($arAtributosOriginais[$w]['nom_atributo']);
                        $arAux = explode("[][][]", $arAtributosOriginais[$w]['valor_padrao_desc']);
                        $obLblModalidade->setValue ($arAtributosOriginais[$w]['valor'] . ' - ' .  $arAux[($arAtributosOriginais[$w]['valor'] - 1)]);

                        $obHdnModalidade = new Hidden;
                        $obHdnModalidade->setName ("Atributo_" . $arAtributosOriginais[$w]['cod_atributo'] . "_" . $arAtributosOriginais[$w]['cod_cadastro']);
                        $obHdnModalidade->setValue($arAtributosOriginais[$w]['valor']);
                    } elseif ($arAtributosOriginais[$w]['cod_atributo'] == 103) { // Atributo : TipoCredor
                        $obLblTipoCredor = new Label;
                        $obLblTipoCredor->setRotulo($arAtributosOriginais[$w]['nom_atributo'] );
                        $arAux = explode("[][][]", $arAtributosOriginais[$w]['valor_padrao_desc']);
                        $obLblTipoCredor->setValue ($arAtributosOriginais[$w]['valor'] . ' - ' .  $arAux[($arAtributosOriginais[$w]['valor'] - 1)]);

                        $obHdnTipoCredor = new Hidden;
                        $obHdnTipoCredor->setName ("Atributo_" . $arAtributosOriginais[$w]['cod_atributo'] . "_" . $arAtributosOriginais[$w]['cod_cadastro']);
                        $obHdnTipoCredor->setValue($arAtributosOriginais[$w]['valor']);
                    } else {
                        if ($arAtributosOriginais[$w]['cod_atributo'] == 100) {	// Atributo : Complementar
                            $arAtributosOriginais[$w]['valor'] = 1;
                        }
                        $arAtributosModificados[] = $arAtributosOriginais[$w];
                    }
                }

                $rsAtributos = new RecordSet();
                $rsAtributos->preenche($arAtributosModificados);

                $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
                $obTAdministracaoConfiguracaoEntidade->setDado("exercicio"    , Sessao::getExercicio());
                $obTAdministracaoConfiguracaoEntidade->setDado("cod_modulo"   , 10);
                $obTAdministracaoConfiguracaoEntidade->setDado("cod_entidade" , $request->get("inCodEntidade"));
                $obTAdministracaoConfiguracaoEntidade->setDado("parametro"    , "data_fixa_empenho");
                $obTAdministracaoConfiguracaoEntidade->recuperaPorChave($rsConfiguracao);
                $stDtFixaEmpenho = trim($rsConfiguracao->getCampo('valor'));

                $obHdnBoComplementar = new Hidden;
                $obHdnBoComplementar->setName ('obHdnBoComplementar');
                $obHdnBoComplementar->setValue(1);

                $obHdnCodigoCategoria = new Hidden;
                $obHdnCodigoCategoria->setName ('inCodCategoria');
                $obHdnCodigoCategoria->setValue($inCodCategoria);

                $obHdnContrapartida = new Hidden;
                $obHdnContrapartida->setName ('inCodContrapartida');
                $obHdnContrapartida->setValue($inCodContrapartida);

                $obHdnCodDespesa = new Hidden;
                $obHdnCodDespesa->setName ('inCodDespesa');
                $obHdnCodDespesa->setValue($inCodDespesa);

                $obHdnCodClassificacao = new Hidden;
                $obHdnCodClassificacao->setName ('stCodClassificacao');
                $obHdnCodClassificacao->setValue($stCodClassificacao);

                $obHdnCodFornecedor = new Hidden;
                $obHdnCodFornecedor->setName ('inCodFornecedor');
                $obHdnCodFornecedor->setValue($inCodFornecedor);

                $obHdnCodHistorico = new Hidden;
                $obHdnCodHistorico->setName ('inCodHistorico');
                $obHdnCodHistorico->setValue($inCodHistorico);

                $obHdnCodTipo = new Hidden;
                $obHdnCodTipo->setName ('inCodTipo');
                $obHdnCodTipo->setValue($inCodTipo);

                $obHdnNomTipo = new Hidden;
                $obHdnNomTipo->setName ('stNomTipo');
                $obHdnNomTipo->setValue($stNomTipo);

                $obHdnOrgaoOrcamento = new Hidden;
                $obHdnOrgaoOrcamento->setName ('HdnOrgaoOrcamento');
                $obHdnOrgaoOrcamento->setValue($hdnOrgaoOrcamento);

                $obHdnUnidadeOrcamento = new Hidden;
                $obHdnUnidadeOrcamento->setName ('HdnUnidadeOrcamento');
                $obHdnUnidadeOrcamento->setValue($hdnUnidadeOrcamento);

                $obHdnVlSaldoConta = new Hidden;
                $obHdnVlSaldoConta->setName ('flVlSaldo');
                $obHdnVlSaldoConta->setValue($flVlSaldo);

                $obHdnCodContrapartida = new Hidden;
                $obHdnCodContrapartida->setName ('inCodContrapartida');
                $obHdnCodContrapartida->setValue($inCodContrapartida);

                $obHdnNomDespesa = new Hidden;
                $obHdnNomDespesa->setName ('stNomDespesa');
                $obHdnNomDespesa->setValue($stNomDespesa);

                $obHdnCodDespesaAnterior = new Hidden;
                $obHdnCodDespesaAnterior->setName ('inCodDespesaAnterior');
                $obHdnCodDespesaAnterior->setValue($inCodDespesaAnterior);

                $obLblDotacao = new Label;
                $obLblDotacao->setRotulo('Dotação Orçamentária');
                $obLblDotacao->setId    ('stNomDespesa');
                $obLblDotacao->setValue ($inCodDespesa." - ".$stNomDespesa);

                $obLblDesdobramento = new Label;
                $obLblDesdobramento->setRotulo('Desdobramento');
                $obLblDesdobramento->setId    ('stNomClassificacao');
                $obLblDesdobramento->setValue ($stCodClassificacao.' - '.$stNomClassificacao);

                $obLblSaldoDotacao = new Label;
                $obLblSaldoDotacao->setRotulo('Saldo Dotação');
                $obLblSaldoDotacao->setId    ('flSaldoDotacao');
                $obLblSaldoDotacao->setValue (number_format($nuValorSaldoAnterior,2,',','.'));

                $obLblOrgaoOrcamento = new Label;
                $obLblOrgaoOrcamento->setRotulo('Órgão Orçamentário');
                $obLblOrgaoOrcamento->setId    ('stOrgaoOrcamento');
                $obLblOrgaoOrcamento->setValue ($inNumOrgao." - ".$stNomOrgao);

                $obLblUnidadeOrcamento = new Label;
                $obLblUnidadeOrcamento->setRotulo('Unidade Orçamentária');
                $obLblUnidadeOrcamento->setId    ('stUnidadeOrcamento');
                $obLblUnidadeOrcamento->setValue ($inNumUnidade." - ".$stNomUnidade);

                $obLblFornecedor = new Label;
                $obLblFornecedor->setRotulo('Credor');
                $obLblFornecedor->setValue ($inCodFornecedor.' - '.$stNomFornecedor);

                $obLblCategoria = new Label;
                $obLblCategoria->setRotulo('Categoria do Empenho');
                $obLblCategoria->setId    ('stNomCategoria');
                $obLblCategoria->setValue ($stNomCategoria);

                if ($inCodCategoria == 2 || $inCodCategoria == 3) {
                    $obLblContrapartida = new Label;
                    $obLblContrapartida->setRotulo('Contrapartida');
                    $obLblContrapartida->setValue ($inCodContrapartida.' - '.$stNomContrapartida);
                }

                // Define Objeto TextArea para Descricao
                $obTxtDescricao = new TextArea;
                $obTxtDescricao->setName         ('stDescricao');
                $obTxtDescricao->setId           ('stDescricao');
                $obTxtDescricao->setValue        ($stDescricao);
                $obTxtDescricao->setRotulo       ('Descrição do Empenho');
                $obTxtDescricao->setTitle        ('Informe a descrição do empenho.');
                $obTxtDescricao->setNull         (true);
                $obTxtDescricao->setRows         (6);
                $obTxtDescricao->setCols         (100);
                $obTxtDescricao->setMaxCaracteres(640);

                // Define objeto Data para validade final
                $obDtEmpenho = new Data;
                $obDtEmpenho->setName              ('stDtEmpenho');
                $obDtEmpenho->setId                ('stDtEmpenho');
                $obDtEmpenho->setRotulo            ('Data de Empenho');
                $obDtEmpenho->setTitle             ('Informe a data do empenho.');
                $obDtEmpenho->setNull              (false);
                $obDtEmpenho->obEvento->setOnBlur  ("validaDataEmpenho(); buscaDado('montaLabelSaldoAnterior');");
                $obDtEmpenho->obEvento->setOnChange("montaParametrosGET('verificaFornecedor');");
                $obDtEmpenho->setLabel             ( TRUE );
                if( $stDtFixaEmpenho != ''){
                    $obDtEmpenho->setValue ($stDtFixaEmpenho);
                }else
                    $obDtEmpenho->setValue ($stDtEmpenho);

                // Define objeto Data para validade final
                $obDtValidadeFinal = new Data;
                $obDtValidadeFinal->setName              ('stDtVencimento');
                $obDtValidadeFinal->setValue             ($stDtVencimento);
                $obDtValidadeFinal->setRotulo            ('Data de Vencimento');
                $obDtValidadeFinal->setTitle             ('');
                $obDtValidadeFinal->setNull              (false);
                $obDtValidadeFinal->obEvento->setOnChange('validaVencimento();');

                $obLblHistorico = new Label;
                $obLblHistorico->setRotulo('Histórico');
                $obLblHistorico->setId    ('stNomHistorico');
                $obLblHistorico->setValue ($inCodHistorico.' - '.$stNomHistorico);

                // Atributos Dinamicos
                $obMontaAtributos = new MontaAtributos;
                $obMontaAtributos->setTitulo   ('Atributos');
                $obMontaAtributos->setName     ('Atributo_');
                $obMontaAtributos->setRecordSet($rsAtributos);

                $obFormulario = new Formulario;
                $obFormulario->addHidden($obHdnBoComplementar);
                $obFormulario->addHidden($obHdnContrapartida);
                $obFormulario->addHidden($obHdnCodigoCategoria);
                $obFormulario->addHidden($obHdnCodDespesa);
                $obFormulario->addHidden($obHdnNomDespesa);
                $obFormulario->addHidden($obHdnCodDespesaAnterior);
                $obFormulario->addHidden($obHdnCodClassificacao);
                $obFormulario->addHidden($obHdnCodFornecedor);
                $obFormulario->addHidden($obHdnCodHistorico);
                $obFormulario->addHidden($obHdnCodTipo);
                $obFormulario->addHidden($obHdnNomTipo);
                $obFormulario->addHidden($obHdnOrgaoOrcamento);
                $obFormulario->addHidden($obHdnUnidadeOrcamento);
                $obFormulario->addHidden($obHdnVlSaldoConta);
                $obFormulario->addHidden($obHdnCodContrapartida);
                if (isset($obHdnTipoCredor)) {
                    $obFormulario->addHidden($obHdnTipoCredor);
                }
                if (isset($obHdnModalidade)) {
                    $obFormulario->addHidden($obHdnModalidade);
                }
                $obFormulario->addComponente($obLblDotacao);
                $obFormulario->addComponente($obLblDesdobramento);
                $obFormulario->addComponente($obLblSaldoDotacao);
                $obFormulario->addComponente($obLblOrgaoOrcamento);
                $obFormulario->addComponente($obLblUnidadeOrcamento);
                $obFormulario->addComponente($obLblFornecedor);
                $obFormulario->addComponente($obLblCategoria);
                if ($inCodCategoria == 2 || $inCodCategoria == 3) {
                    $obFormulario->addComponente($obLblContrapartida);
                }
                $obFormulario->addComponente($obTxtDescricao );
                $obFormulario->addComponente($obDtEmpenho );
                $obFormulario->addComponente($obDtValidadeFinal );
                $obFormulario->addComponente($obLblHistorico );

                $obMontaAtributos->geraFormulario($obFormulario);
                validaDataFornecedor($inCodFornecedor);

                if (isset($obLblTipoCredor)) {
                    $obFormulario->addComponente($obLblTipoCredor);
                }
                if (isset($obLblModalidade)) {
                    $obFormulario->addComponente($obLblModalidade);
                }
                $obFormulario->montaInnerHTML();
                $stHtml = $obFormulario->getHTML();

                $js .= "jQuery('#spnEmpenho').html('".$stHtml."');                           \n";
                $js .= "montaParametrosGET('buscaDtEmpenho');                                \n";
                $js .= "jQuery('#spnLista').html('');                                        \n";
            } else {
                $js .= "jQuery('#inCodigoEmpenho').val('');                                  \n";
                $js .= "jQuery('#stNomFornecedor').html('&nbsp;');                           \n";
                $js .= "jQuery('#spnEmpenho').html('');                                      \n";
                $js .= "jQuery('#inCodContrato').val('');                                    \n";
                $js .= "jQuery('#txtContrato').html('&nbsp;');                               \n";
                $js .= "jQuery('#dtContrato').val('');                                       \n";
                $js .= "jQuery('#spnInfoAdicional').html('');                                \n";
                $js .= "alertaAviso('Empenho informado está anulado ou não existe.','frm','erro','".Sessao::getId()."'); \n";
            }
        } else {
            Sessao::remove('arItens');
            $js .= "jQuery('#inCodigoEmpenho').val('');                                  \n";
            $js .= "jQuery('#stNomFornecedor').html('&nbsp;');                           \n";
            $js .= "jQuery('#spnEmpenho').html('');                                      \n";
            $js .= "jQuery('#spnLista').html('');                                        \n";
            $js .= "jQuery('#inCodContrato').val('');                                    \n";
            $js .= "jQuery('#txtContrato').html('&nbsp;');                               \n";
            $js .= "jQuery('#dtContrato').val('');                                       \n";
            $js .= "jQuery('#spnInfoAdicional').html('');                                \n";
            $js .= "alertaAviso('É necessário informar uma entidade.','frm','erro','".Sessao::getId()."'); \n";
        }

        echo $js;
    break;

    case 'buscaFundamentacaoLegal':
        include_once CAM_GPC_TGO_MAPEAMENTO.'TTCMGOFundamentacaoLegal.php';

        /* Monta combo com fundamentações legais */
        $obFundamentacaoLegal = new TTCMGOFundamentacaoLegal();
        $obFundamentacaoLegal->recuperaTodos($rsFundamentacaoLegal);

        $obCmbFundamentacaoLegal = new Select;
        $obCmbFundamentacaoLegal->setRotulo('Fundamentação legal');
        $obCmbFundamentacaoLegal->setTitle('Fundamentação legal conforme art.24 e 25 da Lei 8.666 / 93');
        $obCmbFundamentacaoLegal->setName('inFundamentacaoLegal');
        $obCmbFundamentacaoLegal->setId('inFundamentacaoLegal');
        $obCmbFundamentacaoLegal->setStyle('width: 520');
        $obCmbFundamentacaoLegal->setCampoId   ('cod_fundamentacao');
        $obCmbFundamentacaoLegal->setCampoDesc ('descricao');
        $obCmbFundamentacaoLegal->addOption('', 'Selecione');
        $obCmbFundamentacaoLegal->preencheCombo($rsFundamentacaoLegal);
        $obCmbFundamentacaoLegal->setNull(false);

        // Define Objeto TextArea para Justificativa
        $obTxtJustificativa = new TextArea;
        $obTxtJustificativa->setName  ('stJustificativa');
        $obTxtJustificativa->setId    ('stJustificativa');
        $obTxtJustificativa->setValue ($stComplemento);
        $obTxtJustificativa->setRotulo('Justificativa');
        $obTxtJustificativa->setTitle ('Justificativa para contratação mediante dispensa ou inexigibilidade.');
        $obTxtJustificativa->setNull  (false);
        $obTxtJustificativa->setRows  (3);
        $obTxtJustificativa->setCols  (250);
        $obTxtJustificativa->setMaxCaracteres (250);

        // Define Objeto TextArea para Complemento
        $obTxtRazao = new TextArea;
        $obTxtRazao->setName  ('stRazao');
        $obTxtRazao->setId    ('stRazao');
        $obTxtRazao->setValue ($stComplemento);
        $obTxtRazao->setRotulo('Razão da escolha');
        $obTxtRazao->setTitle ('Razão da escolha do fornecedor ou executante quando contratação mediante dispensa ou inexigibilidade.');
        $obTxtRazao->setNull  (false);
        $obTxtRazao->setRows  (3);
        $obTxtRazao->setCols  (245);
        $obTxtRazao->setMaxCaracteres (245);

        $obFormulario = new Formulario;
        $obFormulario->addComponente($obCmbFundamentacaoLegal);
        $obFormulario->addComponente($obTxtJustificativa);
        $obFormulario->addComponente($obTxtRazao);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnFundamentacaoLegal').innerHTML = '".$stHtml."';</script>";

        echo $js;
    break;

    case "unidadeItem":
        $js = "";
        $stJs = "";
        if( $request->get("codItem") ){
            $stFiltro=" WHERE cod_item=".$request->get("codItem");

            include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php";
            $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
            $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $request->get("codItem"));
            $obTAlmoxarifadoCatalogoItem->recuperaTodos($rsItem, $stFiltro);

            if($rsItem->inNumLinhas==1){
                $value = $rsItem->getCampo('cod_unidade')."-".$rsItem->getCampo('cod_grandeza');

                include_once CAM_GA_ADM_MAPEAMENTO."TUnidadeMedida.class.php";
                $obTUnidadeMedida = new TUnidadeMedida;

                $stFiltro=" WHERE cod_unidade=".$rsItem->getCampo('cod_unidade')." AND cod_grandeza=".$rsItem->getCampo('cod_grandeza');
                $obTUnidadeMedida->recuperaTodos($rsUnidade, $stFiltro);
                if($rsUnidade->inNumLinhas==1){
                    $value=$value."-".$rsUnidade->getCampo('nom_unidade');

                    $js .= "for (var i = 0; i < f.inCodUnidade.options.length; i++)
                            {
                                if (f.inCodUnidade.options[i].value == '".$value."')
                                {
                                    f.inCodUnidade.options[i].selected = 'true';
                                    break;
                                }
                            }\n";
                }
            }
        }

        SistemaLegado::executaFrameOculto($js);
        echo $stJs;
    break;

    case 'montaLabelSaldoAnterior':
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($request->get('inCodDespesa'));
        $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->setdataEmpenho($request->get('stDtEmpenho'));
        $obREmpenhoAutorizacaoEmpenho->setCodEntidade($request->get('inCodEntidade'));
        $obREmpenhoAutorizacaoEmpenho->setTipoEmissao('R');
        $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnteriorDataEmpenho($nuSaldoDotacao);

        if ($nuSaldoDotacao == "0.00") {
            $nuSaldoDotacao = '&nbsp;';
        } else {
            $nuSaldoDotacao = number_format($nuSaldoDotacao ,2 ,',' ,'.');
        }

        $stLabel = $request->get('hdnNomeAcao') == "stEmitirEmpenhoAutorizacao" ? "nuSaldoAnterior" : "flSaldoDotacao";

        $js.= "d.getElementById('".$stLabel."').innerHTML = '".$nuSaldoDotacao."';";

        SistemaLegado::executaFrameOculto($js);
    break;

    case "limparOrdem":
        Sessao::remove('arItens');
    break;

    case "validaContrato":
        echo (validaContrato($request->get('inCodEntidade'), $request->get('inCodFornecedor'), $request->get('inCodContrato'), $request->get('stExercicioContrato')));
    break;

    case "buscaAditivo":
        require_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoContratoAditivos.class.php';
        $obTLicitacaoContratoAditivos = new TLicitacaoContratoAditivos;

        list($inNumAditivo, $stExercicioAditivo) = explode('/', $request->get('inNumAditivo'));
        $inCodContrato = $request->get('inCodContrato');
        $stExercicioContrato = $request->get('stExercicioContrato');
        $inCodEntidade = $request->get('inCodEntidade');

        $stHTML = "";
        if(!empty($inCodContrato) && !empty($stExercicioContrato) && !empty($inCodEntidade) && !empty($inNumAditivo) && !empty($stExercicioAditivo)){
            $stFiltro  = " WHERE num_contrato      = ".$inCodContrato;
            $stFiltro .= " AND exercicio_contrato  = '".$stExercicioContrato."'";
            $stFiltro .= " AND cod_entidade        = ".$inCodEntidade;
            $stFiltro .= " AND num_aditivo         = ".$inNumAditivo;
            $stFiltro .= " AND exercicio           = '".$stExercicioAditivo."'";

            $stOrdem = " ORDER BY exercicio, num_aditivo";
            $obTLicitacaoContratoAditivos->recuperaTodos($rsAditivo, $stFiltro, $stOrdem);

            if($rsAditivo->getNumLinhas()==1){
                $obLblData = new Label;
                $obLblData->setRotulo('Data do Aditivo');
                $obLblData->setValue($rsAditivo->getCampo('dt_assinatura'));

                $obLblValor = new Label;
                $obLblValor->setRotulo('Valor do Aditivo');
                $obLblValor->setValue(number_format($rsAditivo->getCampo('valor_contratado'),2,',','.'));

                $obForm = new Form;
                $obForm->setName("frm2");

                $obFormulario = new Formulario;
                $obFormulario->addForm  ($obForm);
                $obFormulario->addComponente($obLblData);
                $obFormulario->addComponente($obLblValor);
                $obFormulario->montaInnerHTML();

                $stHTML = $obFormulario->getHTML();
                $stHTML = str_replace( "\n"     , ""    , $stHTML );
                $stHTML = str_replace( chr(13)  , "<br>", $stHTML );
                $stHTML = str_replace( "  "     , ""    , $stHTML );
                $stHTML = str_replace( "'"      , "\\'" , $stHTML );
                $stHTML = str_replace( "\\\'"   , "\\'" , $stHTML );
            }
        }

        $stJs  = "if(d.getElementById('spnAditivo')){                            \n";
        $stJs .= "  d.getElementById('spnAditivo').innerHTML = '".$stHTML."';    \n";
        $stJs .= "}                                                              \n";

        echo $stJs;
    break;

    case "montaBuscaContrato":
        $arFiltroBuscaContrato = array('inCodEntidade'=>$request->get('inCodEntidade'), 'inCodFornecedor'=>$request->get('inCodFornecedor'));
        Sessao::write('arFiltroBuscaContrato', $arFiltroBuscaContrato);

        $js .= validaContrato($request->get('inCodEntidade'), $request->get('inCodFornecedor'), $request->get('inCodContrato'), $request->get('stExercicioContrato'));

        echo ($js);
    break;

    case 'detalharItem':
        include_once CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php";

        $obHiddenNumItem = new Hidden();
        $obHiddenNumItem->setName('hdnNumItem_'.$request->get('num_item'));
        $obHiddenNumItem->setValue($request->get('num_item'));

        $obHiddenLinha = new Hidden();
        $obHiddenLinha->setName('hdnLinha_'.$request->get('num_item'));
        $obHiddenLinha->setValue($request->get('linha_table_tree'));

        $obMarca = new IPopUpMarca(new Form);
        $obMarca->setNull               ( true );
        $obMarca->setRotulo             ( 'Marca' );
        $obMarca->setId                 ( 'stNomMarca_'.$request->get('num_item') );
        $obMarca->setId                 ( 'stNomMarca_'.$request->get('num_item') );
        $obMarca->obCampoCod->setName   ( 'inMarca_'.$request->get('num_item') );
        $obMarca->obCampoCod->setId     ( 'inMarca_'.$request->get('num_item') );
        $obMarca->obCampoCod->setValue  ( $request->get('cod_marca') );
        $obMarca->setValue              ( $request->get('nome_marca') );

        $obBtnIncluir = new Button;
        $obBtnIncluir->setName      ( "btnIncluir_".$request->get('num_item')   );
        $obBtnIncluir->setValue     ( "Incluir"                                 );
        $obBtnIncluir->setTipo      ( "button"                                  );
        $obBtnIncluir->setDisabled  ( false                                     );
        $stMontaParametrosGET = "montaParametrosGET('incluirMarca',' hdnNumItem_".$request->get('num_item').",inMarca_".$request->get('num_item').",stNomMarca_".$request->get('num_item').",hdnLinha_".$request->get('num_item')." ');";
        $obBtnIncluir->obEvento->setOnClick ( $stMontaParametrosGET );

        $obFormulario = new Formulario();
        $obFormulario->addHidden( $obHiddenNumItem );
        $obFormulario->addHidden( $obHiddenLinha );
        $obFormulario->addComponente( $obMarca );
        $obFormulario->addComponente( $obBtnIncluir );
        $obFormulario->show();

    break;

    case 'incluirMarca':
        $arItens = Sessao::read('arItens');
        $arRequest = $request->getAll();
        if (!empty($arItens)) {
            foreach ($arItens as $chave => $valor) {
                $inNumItemRequest = $request->get('hdnNumItem_'.$valor['num_item']);
                if ( $valor['num_item'] == $inNumItemRequest ) {
                    $arItens[$chave]['cod_marca']   = $request->get('inMarca_'.$inNumItemRequest);
                    $arItens[$chave]['nome_marca']  = $request->get('stNomMarca_'.$inNumItemRequest);
                    $stMensagem = "Marca do Item (".$valor['num_item']." - ".$valor['nom_item'].") foi alterada!";
                    $stLinha = $request->get('hdnLinha_'.$inNumItemRequest);
                }
            }
        }

        if (!empty($stMensagem)) {
            Sessao::write('arItens', $arItens);
            montaLista($arItens, true);
            $js = " alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."', '../'); ";
            echo $js;
        }
    break;

    case 'LiberaDataEmpenho':
        $js = LiberaDataEmpenho();
        echo $js;
    break;

}

?>
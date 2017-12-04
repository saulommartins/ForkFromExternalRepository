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
    * Página de Oculto do Relatório Razão da Despesa
    * Data de Criação   : 07/03/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @ignore

    * $Id: OCRazaoDespesa.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-02.01.32
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioRazaoDespesa.class.php");
//include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

function carregaUnidade($inNumOrgao)
{
    $obROrcamentoRelatorioRazaoDespesa = new ROrcamentoRelatorioRazaoDespesa;
    $stJs .= "limpaSelect(f.inNumUnidade,1); \n";
    $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($inNumOrgao);
    $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->consultar( $rsUnidades, $stFiltro,"", $boTransacao );
    $inCount = 0;
    while ( !$rsUnidades->eof() ) {
        $inCount++;
        $stJs .= "f.inNumUnidade.options[$inCount] = new Option('".$rsUnidades->getCampo('nom_unidade')."','".$rsUnidades->getCampo('num_unidade')."',''); \n";
        $rsUnidades->proximo();
    }

    return $stJs;
}

function limpaCombos($all = false)
{
    $stJs .= "f.inNumOrgaoTxt.value = ''; \n";
    $stJs .= "f.inNumOrgao.value = ''; \n";
    $stJs .= "f.inNumUnidadeTxt.value = ''; \n";
    $stJs .= "limpaSelect(f.inNumUnidade,1); \n";
    if ($all) {
        $stJs .= "limpaSelect(f.inCodDesdobramento,1); \n";
    }
    $stJs .= "f.inCodRecurso.value = ''; \n";
    $stJs .= "f.stDescricaoRecurso.value = ''; \n";

    return $stJs;
}

function geraMaior($inCodEstrutural)
{
    $arEstrutural = explode('.',$inCodEstrutural);
    $inCount = count($arEstrutural)-1;
    while ( (integer) $arEstrutural[$inCount] == 0 ) {
        $arEstrutural[$inCount] = str_replace(0,9,$arEstrutural[$inCount]);
        $inCount--;
    }
    $inCount=0;

    return implode('.',$arEstrutural);
}

function buscaDesdobramento($dotacao, $entidade)
{
    $obROrcamentoRelatorioRazaoDespesa = new ROrcamentoRelatorioRazaoDespesa;

    $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $dotacao );
    $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $entidade );
    $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obROrcamentoDespesa->consultarDotacao( $rsDesdobramento );

    $stJs .= "f.inCodReduzido.value = ".$rsDesdobramento->getCampo('cod_despesa').";\n";
    if ( $rsDesdobramento->getNumLinhas() > 0 ) {
        $inCount=0;
        $stJs .= "f.inCodEstrutural.value = '".$rsDesdobramento->getCampo('cod_estrutural')."';\n";
        while ( !$rsDesdobramento->eof() ) {
            $inCount++;
            $stJs .= "f.inCodDesdobramento.options[$inCount] = new Option('".$rsDesdobramento->getCampo('cod_estrutural')."-".$rsDesdobramento->getCampo('descricao')."','".$rsDesdobramento->getCampo('cod_estrutural')."','');\n";
            $rsDesdobramento->proximo();
        }
        $rsDesdobramento->setPrimeiroElemento();
    }
    $stJs .= "f.inNumOrgaoTxt.value = ".$rsDesdobramento->getCampo('num_orgao').";\n";
    $stJs .= "f.inNumOrgao.value = ".$rsDesdobramento->getCampo('num_orgao').";\n";
    $stJs .= carregaUnidade($rsDesdobramento->getCampo('num_orgao'));
    $stJs .= "f.inNumUnidade.value = ".$rsDesdobramento->getCampo('num_unidade').";\n";
    $stJs .= "f.inNumUnidadeTxt.value = ".$rsDesdobramento->getCampo('num_unidade').";\n";
    $stJs .= "f.inCodRecurso.value = ".$rsDesdobramento->getCampo('cod_recurso').";\n";
    $stJs .= "d.getElementById('stDescricaoRecurso').innerHTML = '".$rsDesdobramento->getCampo('nom_recurso')."';\n";

    return $stJs;
}

function montaComboDiverso()
{
    $obROrcamentoRelatorioRazaoDespesa = new ROrcamentoRelatorioRazaoDespesa;
    if ($_REQUEST['inCodDotacao'] != "") {
        $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_POST['inCodDotacao'] );
        $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa( $rsConta );
        $stCodDesdobramento = $rsConta->getCampo( "cod_estrutural" );
        $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $stCodDesdobramento );
        $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( "" );
        $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa( $rsClassificacao );
        if ( $rsClassificacao->getNumLinhas() > -1 ) {
            $inContador = 1;
            $js .= "limpaSelect(f.inCodDesdobramento,0); \n";
            $js .= "f.inCodDesdobramento.options[0] = new Option('Selecione','', 'selected');\n";
            while ( !$rsClassificacao->eof() ) {
                $stMascaraReduzida = $rsClassificacao->getCampo("mascara_reduzida");
                if ($stMascaraReduzidaOld) {
                    if ( $stMascaraReduzidaOld != substr($stMascaraReduzida,0,strlen($stMascaraReduzidaOld)) ) {
                        $selected = "";
                      if ($stCodEstruturalOld == $_POST["stCodEstrutural"]) {
                          $selected = "selected";
                      }
                        $stOption = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";
                        $js .= "f.inCodDesdobramento.options[$inContador] = new Option( $stOption ); \n";
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
                if ($stCodEstruturalOld == $_POST['stCodEstrutural']) {
                    $selected = "selected";
                }
                $stOption = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";
                $js .= "f.inCodDesdobramento.options[$inContador] = new Option( $stOption ); \n";
            }
        } else {
            $js .= "limpaSelect(f.inCodDesdobramento,0); \n";
            $js .= "f.stinCodDesdobramento.options[0] = new Option('Selecione','', 'selected');\n";
        }
    } else {
        $js .= "limpaSelect(f.inCodDesdobramento,0); \n";
        $js .= "f.inCodDesdobramento.options[0] = new Option('Selecione','', 'selected');\n";
    }

    return $js;
}

$obROrcamentoRelatorioRazaoDespesa = new ROrcamentoRelatorioRazaoDespesa;

switch ($_REQUEST['stCtrl']) {
    case 'carregaUnidade':
            $stJs .= carregaUnidade($_REQUEST['inNumOrgao']);
            $stJs .= "f.inNumUnidadeTxt.value = '';\n";
            $stJs .= "d.getElementById('stNomDotacao').innerHTML = '&nbsp;';\n";
            $stJs .= "f.inCodDotacao.value = '';\n";
            $stJs .= "limpaSelect('inCodDesdobramento',1);\n";
        break;
    case 'buscaDotacao':
        if (($_REQUEST["inCodDotacao"] != "") && ($_REQUEST['inCodEntidade'] != "")) {
            $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST["inCodDotacao"] );
            $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
            $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
            $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
            $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );

            if ( $rsDespesa->getNumLinhas() > 0 ) {
                $stNomDespesa = $rsDespesa->getCampo( "descricao" );
                $stJs = 'd.getElementById("stNomDotacao").innerHTML = "'.$stNomDespesa.'";';
                $stJs .= buscaDesdobramento($_REQUEST['inCodDotacao'],$_REQUEST['inCodEntidade']);
                $stJs .= montaComboDiverso();
            } else {
                $stJs .= 'f.inCodDotacao.value = "";';
                $stJs .= 'f.inCodDotacao.focus();';
                $stJs .= 'd.getElementById("stNomDotacao").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCodDotacao"].")','form','erro','".Sessao::getId()."');";
                $stJs .= limpaCombos(true);
            }
        } else {
            $stJs  = 'd.getElementById("stNomDotacao").innerHTML = "&nbsp;";';
            $stJs .= limpaCombos(true);
        }
        break;
    case 'selecionaDesdobramento':
        if ( !empty($_REQUEST['inCodDotacao'])  &&  !empty($_REQUEST['inCodEntidade']) ) {
            $stJs .= buscaDesdobramento($_REQUEST['inCodDotacao'],$_REQUEST['inCodEntidade']);
        } else {
            $stJs .= limpaCombos(true);
        }
        break;
    case 'carregaOrgao':
        if ( !empty($_REQUEST['inCodDotacao']) && !empty($_REQUEST['inCodEntidade'])  && !empty($_REQUEST['inCodDesdobramento']) ) {
            $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST["inCodDotacao"] );
            $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
            $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
            $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $_REQUEST['inCodDesdobramento'] );
            $obROrcamentoRelatorioRazaoDespesa->obREmpenhoEmpenho->obROrcamentoDespesa->consultarDotacao( $rsDesdobramento );
            $stJs .= "f.inNumOrgaoTxt.value = ".$rsDesdobramento->getCampo('num_orgao').";\n";
            $stJs .= "f.inNumOrgao.value = ".$rsDesdobramento->getCampo('num_orgao').";\n";
            $stJs .= carregaUnidade($rsDesdobramento->getCampo('num_orgao'));
            $stJs .= "f.inNumUnidade.value = ".$rsDesdobramento->getCampo('num_unidade').";\n";
            $stJs .= "f.inNumUnidadeTxt.value = ".$rsDesdobramento->getCampo('num_unidade').";\n";
            $stJs .= "f.inCodRecurso.value = ".$rsDesdobramento->getCampo('cod_recurso').";\n";
            $stJs .= "f.inCodRecursoTxt.value = ".$rsDesdobramento->getCampo('cod_recurso').";\n";
        } else {
            $stJs .= limpaCombos();
        }
        break;
    case 'limpaCombos':
            $stJs .= limpaCombos();
            $stJs .= "f.inCodDotacao.value = ''; \n";
            $stJs .= "d.getElementById('stNomDotacao').innerHTML = '&nbsp;' \n;";
        break;
    default:

        $arFiltro = Sessao::read('filtroRelatorio');

        $obROrcamentoRelatorioRazaoDespesa->setExercicio  ( Sessao::getExercicio() );
        $obROrcamentoRelatorioRazaoDespesa->setCodEntidade( $arFiltro['inCodEntidade'] );
        $obROrcamentoRelatorioRazaoDespesa->setDataInicial( $arFiltro['stDataInicial'] );
        $obROrcamentoRelatorioRazaoDespesa->setDataFinal  ( $arFiltro['stDataFinal'] );
        $obROrcamentoRelatorioRazaoDespesa->setCodDotacao ( $arFiltro['inCodDotacao'] );
        if ($arFiltro['inCodDesdobramento']) {
            $obROrcamentoRelatorioRazaoDespesa->setCodDesdobramento      ( $arFiltro['inCodDesdobramento'] );
            $obROrcamentoRelatorioRazaoDespesa->setCodDesdobramentoFinal ( $arFiltro['inCodDesdobramento'] );
        } elseif ($arFiltro['inCodEstrutural']) {
            $obROrcamentoRelatorioRazaoDespesa->setCodDesdobramento      ( $arFiltro['inCodEstrutural'] );
            $obROrcamentoRelatorioRazaoDespesa->setCodDesdobramentoFinal ( geraMaior($arFiltro['inCodEstrutural']) );
        }
        $obROrcamentoRelatorioRazaoDespesa->setCodOrgao   ( $arFiltro['inNumOrgao'] );
        $obROrcamentoRelatorioRazaoDespesa->setCodUnidade ( $arFiltro['inNumUnidade'] );
        $obROrcamentoRelatorioRazaoDespesa->setCodRecurso ( $arFiltro['inCodRecurso'] );
        if( $arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
            $obROrcamentoRelatorioRazaoDespesa->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );

        $obROrcamentoRelatorioRazaoDespesa->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );
        $obROrcamentoRelatorioRazaoDespesa->setEmpenhoAnulacao ( $arFiltro['boEmpenhoAnulacao'] );
        $obROrcamentoRelatorioRazaoDespesa->setLiquidacaoAnulacao ( $arFiltro['boLiquidacaoAnulacao'] );
        $obROrcamentoRelatorioRazaoDespesa->setPagamentoEstorno ( $arFiltro['boPagamentoEstorno'] );
        $obROrcamentoRelatorioRazaoDespesa->setSuplementacaoReducao ( $arFiltro['boSuplementacaoReducao'] );
        $obROrcamentoRelatorioRazaoDespesa->setCodReduzido( $arFiltro['inCodReduzido'] );

        $obROrcamentoRelatorioRazaoDespesa->geraRecordSet( $arRecordSet, $arRecordSet1 );

        $inCount=1;
        if ( ( ( count($arRecordSet1) > 0 ) && ( !empty($arFiltro['inCodDesdobramento']) ) ) && ( !empty($arFiltro['inCodDotacao']) ) ) {
            foreach ($arRecordSet as $rsRecordSet) {
                $arTemp[$inCount] = $rsRecordSet->getElementos();
                $inCount++;
            }

            foreach ($arTemp as $Array) {
//                $sumDotacaoInicial += number_format(str_replace(',','.',str_replace('.','', $Array[0]['coluna4'])),2,'.','');
                $sumCreditoPer     += number_format(str_replace(',','.',str_replace('.','', $Array[1]['coluna4'])),2,'.','');
                $sumReducaoPer     += number_format(str_replace(',','.',str_replace('.','', $Array[2]['coluna4'])),2,'.','');
                $sumEmpenhadoPer   += number_format(str_replace(',','.',str_replace('.','', $Array[3]['coluna4'])),2,'.','');
                $sumLiquidadePer   += number_format(str_replace(',','.',str_replace('.','', $Array[4]['coluna4'])),2,'.','');
                $sumAnuladoPer     += number_format(str_replace(',','.',str_replace('.','', $Array[5]['coluna4'])),2,'.','');
                $sumPagoPer        += number_format(str_replace(',','.',str_replace('.','', $Array[6]['coluna4'])),2,'.','');
                $sumEmpenhadoAno   += number_format(str_replace(',','.',str_replace('.','', $Array[7]['coluna4'])),2,'.','');
                $sumLiquidadeAno   += number_format(str_replace(',','.',str_replace('.','', $Array[8]['coluna4'])),2,'.','');
                $sumAnuladoAno     += number_format(str_replace(',','.',str_replace('.','', $Array[9]['coluna4'])),2,'.','');
                $sumPagoAno        += number_format(str_replace(',','.',str_replace('.','', $Array[10]['coluna4'])),2,'.','');
                $sumSaldoPagar     += number_format(str_replace(',','.',str_replace('.','', $Array[11]['coluna4'])),2,'.','');
            }
/*           echo "Dotacao".$sumDotacaoInicial.
                 "Credito".$sumCreditoPer.
                 "Reducao".$sumReducaoPer.
                 "Empenho".$sumEmpenhadoPer.
                 "Liquidacao".$sumLiquidadePer.
                 "Anulado".$sumAnuladoPer.
                 "Pago".$sumPagoPer.
                 "Empenho".$sumEmpenhadoAno.
                 "Liquidado".$sumLiquidadeAno.
                 "Anulado".$sumAnuladoAno.
                 "Pago".$sumPagoAno.
                 "Saldo".$sumSaldoPagar."<br>";
*/

            if (is_object($arRecordSet[0])) {
                $arTemp2 = $arRecordSet[0]->getElementos();
//                $arTemp2[1]['coluna4'] = $sumDotacaoInicial;
                $arTemp2[2]['coluna4']  = number_format($sumReducaoPer  ,2,',','.');
                $arTemp2[3]['coluna4']  = number_format($sumEmpenhadoPer,2,',','.');
                $arTemp2[4]['coluna4']  = number_format($sumLiquidadePer,2,',','.');
                $arTemp2[5]['coluna4']  = number_format($sumAnuladoPer  ,2,',','.');
                $arTemp2[6]['coluna4']  = number_format($sumPagoPer     ,2,',','.');
                $arTemp2[7]['coluna4']  = number_format($sumEmpenhadoAno,2,',','.');
                $arTemp2[8]['coluna4']  = number_format($sumLiquidadeAno,2,',','.');
                $arTemp2[9]['coluna4']  = number_format($sumAnuladoAno  ,2,',','.');
                $arTemp2[10]['coluna4'] = number_format($sumPagoAno     ,2,',','.');
                $arTemp2[11]['coluna4'] = number_format($sumSaldoPagar  ,2,',','.');
            }
            $rsTemp = new RecordSet;
            $rsTemp->preenche( $arTemp2 );

            array_shift($arRecordSet);
            array_unshift($arRecordSet,$rsTemp);
        }

        Sessao::write('arRecordSet',$arRecordSet);
        Sessao::write('arRecordSet1',$arRecordSet1);
        //sessao->transf[0]     = $arRecordSet;
        //sessao->transf[1]     = $arRecordSet1;

        $obROrcamentoRelatorioRazaoDespesa->obRRelatorio->executaFrameOculto("OCGeraRelatorioRazaoDespesa.php");
        break;
}

if ( !empty($stJs) ) {
    $stJs .= "LiberaFrames(true,false);";
    SistemaLegado::executaFrameOculto( $stJs );
}
?>

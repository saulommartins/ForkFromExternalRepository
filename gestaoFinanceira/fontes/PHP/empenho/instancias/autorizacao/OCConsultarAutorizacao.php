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
 * Página de Formulário Oculto de Consultar Autorização
 * Data de Criação: 05/05/2005
 *
 * @category   Urbem
 * @package    Framework
 * @author Analista: Diego Victoria
 * @author Desenvolvedor: Lucas Leusin Oaigen
 * $Id: OCConsultarAutorizacao.php 65373 2016-05-17 12:31:43Z michel $
 * Casos de uso: uc-02.03.02
                    uc-02.01.08
 */

/* includes de sistema */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/* includes de regra de negocio */
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
/* includes de regra de classes */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

$obRegra = new REmpenhoAutorizacaoEmpenho;
$obRegra->setExercicio(Sessao::getExercicio());

function montaLista($arRecordSet , $boExecuta = true)
{
        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "vl_total", "NUMERIC_BR" );

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição ");
        $obLista->ultimoCabecalho->setWidth( 50 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor Unitário ");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Quantidade ");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor Total");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_item" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_unitario" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "quantidade" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_total" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        foreach ($arRecordSet as $value) {
            $vl_total = str_replace('.','',$value['vl_total']);
            $vl_total = str_replace(',','.',$vl_total);
            $nuVlTotal += $value['vl_total'];
        }
        $nuVlTotal = number_format($nuVlTotal,2,',','.');

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '".$stHTML."';");
        } else {
            return $stHTML;
        }
}

function montaLista2($arRecordSet , $boExecuta = true)
{
    $arRecordSetAux = $arRecordSet;

    for($i=0;$i<count($arRecordSet);$i++){
        if(isset($arRecordSet[$i]['cod_item'])&&$arRecordSet[$i]['cod_item']!='')
            $arRecordSet[$i]['nom_item'] = $arRecordSet[$i]['cod_item'].' - '.$arRecordSet[$i]['nom_item'];
        if(isset($arRecordSet[$i]['cod_marca'])&&$arRecordSet[$i]['cod_marca']!='')
            $arRecordSet[$i]['nom_item'] .= " ( Marca: ".$arRecordSet[$i]['cod_marca']." - ".$arRecordSet[$i]['nome_marca']." )";
    }

    foreach ($arRecordSetAux as $inChave => $arValor) {
        if (trim($arValor['complemento']) == "") {
            $arRecordSet[$inChave]['possui_complemento'] = 'f';
        } else {
            $arRecordSet[$inChave]['possui_complemento'] = 't';
        }
        $arRecordSet[$inChave]['inChave'] = $inChave;
    }
    unset($arRecordSetAux);

    $rsLista = new RecordSet;
    $rsLista->preenche($arRecordSet);
    $rsLista->addFormatacao("vl_total", "NUMERIC_BR");

    $table = new TableTree;
    $table->setRecordset($rsLista);
    $table->setArquivo(CAM_GF_EMP_INSTANCIAS.'autorizacao/OCConsultarAutorizacao.php');
    $table->setParametros(array('inChave'));
    $table->setComplementoParametros("stCtrl=detalharAutorizacao");

    // Defina o título da tabela
    $table->setSummary('Registros');

    $table->addCondicionalTree('possui_complemento', 't');

    // lista zebrada
    //$table->setConditional(true, "#efefef");

    $table->Head->addCabecalho('Descrição'      , 50);
    $table->Head->addCabecalho('Valor Unitário' , 15);
    $table->Head->addCabecalho('Quantidade'     , 10);
    $table->Head->addCabecalho('Valor Total'    , 15);

    $table->Body->addCampo('nom_item'   , 'E');
    $table->Body->addCampo('vl_unitario', 'D');
    $table->Body->addCampo('quantidade' , 'D');
    $table->Body->addCampo('vl_total'   , 'D');
    $table->Foot->addSoma ('vl_total'   , 'D');

    $table->montaHTML(true);
    $stHTML = $table->getHtml();

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto("jq_('#spnLista').html('".$stHTML."');");
    } else {
        return $stHTML;
    }
}

switch ($stCtrl) {
    case 'montaListaItemPreEmpenho':
        montaLista2(Sessao::read('arItens'));
    break;
    case 'buscaFornecedorDiverso':
        if ($request->get("inCodFornecedor", "") != "") {
            $obRegra->obRCGM->setNumCGM( $request->get("inCodFornecedor") );
            $obRegra->obRCGM->listar( $rsCGM );
            $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomFornecedor) {
                $js .= 'f.inCodFornecedor.value = "";';
                $js .= 'f.inCodFornecedor.focus();';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$request->get("inCodFornecedor").")','form','erro','".Sessao::getId()."');";
            } else
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
        } else
            $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';

        SistemaLegado::executaFrameOculto($js);
    break;
    case "MontaUnidade":
        if ($request->get("inNumOrgao")) {
            $stCombo  = "inNumUnidade";
            $stComboTxt  = "inNumUnidadeTxt";
            $stJs .= "limpaSelect(f.".$stCombo.",0); \n";
            $stJs .= "f.".$stComboTxt.".value=''; \n";
            $stJs .= "f.".$stCombo.".options[0] = new Option('Selecione','', 'selected');\n";

            $obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($request->get("inNumOrgao"));
            $obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->consultar( $rsCombo, $stFiltro,"", $boTransacao );

            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("num_unidade");
                $stDesc = $rsCombo->getCampo("nom_unidade");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.".$stCombo.".options[".$inCount."] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsCombo->proximo();
            }
        }
        $stJs .= $js;

        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "mascaraClassificacao":
        //monta mascara da RUBRICA DE DESPESA
        $arMascClassificacao = Mascara::validaMascaraDinamica( $request->get('stMascClassificacao') , $request->get('inCodDespesa') );
        $js .= "f.inCodDespesa.value = '".$arMascClassificacao[1]."'; \n";

        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obRegra->obROrcamentoClassificacaoDespesa->setMascara          ( $request->get('stMascClassificacao') );
        $obRegra->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1] );
        $obRegra->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
        if ($stDescricao != "") {
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodDespesa.value = "";';
            $js .= 'f.inCodDespesa.focus();';
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
            $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;
    case 'buscaDotacao':
        $obRegra->obROrcamentoDespesa->setCodDespesa( $request->get("inCodDotacao") );
        $obRegra->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obRegra->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );

        $stNomDespesa = $rsDespesa->getCampo( "descricao" );
        if (!$stNomDespesa) {
            $js .= 'f.inCodDotacao.value = "";';
            $js .= 'f.inCodDotacao.focus();';
            $js .= 'd.getElementById("stNomDotacao").innerHTML = "&nbsp;";';
            $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$request->get("inCodDotacao").")','form','erro','".Sessao::getId()."');";
        } else {
            $stNomDespesa = $rsDespesa->getCampo( "descricao" );
            $js .= 'd.getElementById("stNomDotacao").innerHTML = "'.$stNomDespesa.'";';
        }

        SistemaLegado::executaFrameOculto($js);
    break;
    case 'detalharAutorizacao':
        $arItens = Sessao::read('arItens');
        echo $arItens[$request->get('inChave')]['complemento'];
    break;
}

?>

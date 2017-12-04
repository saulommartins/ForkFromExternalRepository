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
* Página de Processamento Oculto Beneficio Faixa Desconto
* Data de Criação   : 07/07/2005

* @author Analista: Vandré Ramos
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 30922 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_BEN_NEGOCIO . "RBeneficioFaixaDesconto.class.php"    );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"                                             );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

//Adicionado $roAuxiliar como parâmetro vazio, apenas para exitar erro de referência na classe
$obRegra  = new RBeneficioFaixaDesconto( $roAuxilar);
$rsFaixas = new RecordSet;
function listarFaixas($arRecordSet, $boExecuta=true)
{
    global $obRegra;

    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Faixas Cadastradas" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Inicial" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Final" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Percentual de Desconto" );
        $obLista->ultimoCabecalho->setWidth( 80 );
        $obLista->commitCabecalho();
    if ($_REQUEST['stAcao'] != 'consultar') {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
    }

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flSalarioInicial" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flSalarioFinal" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flPercentualDesc" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
    if ($_REQUEST['stAcao'] != 'consultar') {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluiFaixa');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
    }

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnFaixas').innerHTML = '".$stHtml."';";
    $stJs .= "f.flSalarioInicial.value = '';";
    $stJs .= "f.flSalarioFinal.value = '';";
    $stJs .= "f.flPercentualDesc.value = '';";

    if ($boExecuta==true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

$arSessaoFaixas = (is_array(Sessao::read('Faixas')))?Sessao::read('Faixas'):array();

function montaNorma($stSelecionado = "")
{
    $stCombo  = "inCodNorma";
    $stFiltro = "inCodTipoNorma";
    $stJs .= "limpaSelect(f.$stCombo,0); \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
    //$stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";

    if ($_REQUEST[ 'inCodTipoNorma' ] != "") {
        $inCodTipoNorma = $_REQUEST[ 'inCodTipoNorma' ];

        $stFiltro = " WHERE cod_tipo_norma =".$inCodTipoNorma;
        $obTNorma = new TNorma();
        $obTNorma->recuperaNormas( $rsCombo, $stFiltro );

        $inCount = 0;
        while (!$rsCombo->eof()) {
            $inCount++;
            $inId               = str_replace(' ','',$rsCombo->getCampo("cod_norma"));
            $stDesc             = $rsCombo->getCampo("nom_norma");
            if( $stSelecionado == $inId )
                $stSelected = 'selected';
            else
                $stSelected = '';
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsCombo->proximo();
        }
    }

    sistemaLegado::executaFrameOculto($stJs);
}

// Acoes por pagina
switch ($stCtrl) {

//monta HTML com os ATRIBUTOS relativos a Faixa de Desconto
    case "MontaFaixa":
        $stMensagem = false;
        $arElementos = array ();
        $rsRecordSet = new Recordset;
        $rsRecordSet->preenche( $arSessaoFaixas );
        $rsRecordSet->setUltimoElemento();
        $inUltimoId = $rsRecordSet->getCampo("inId");
        if (!$inUltimoId) {
            $inProxId = 1;
        } else {
            $inProxId = $inUltimoId + 1;
        }
        $ultimoValorIncluido = $rsRecordSet->getCampo("flSalarioFinal");
        $ultimoValorIncluido = str_replace('.', '', $ultimoValorIncluido);
        $ultimoValorIncluido = str_replace(',', '', $ultimoValorIncluido);

        $ValorIncluido   = str_replace('.','',$_POST['flSalarioInicial']);
        $ValorIncluido   = str_replace(',','.',$ValorIncluido);
        $arValorIncluido = explode('.',$ValorIncluido);
                if ($arValorIncluido[1] == NULL) {
                   $ValorIncluido = $ValorIncluido.',00';
                }
        $ValorIncluido = number_format($ValorIncluido,2,',','.');
        $ValorIncluido = str_replace('.', '', $ValorIncluido);
        $ValorIncluido = str_replace(',', '', $ValorIncluido);

        $ultimoPercentualIncluido = $rsRecordSet->getCampo("flPercentualDesc");
        $ultimoPercentualIncluido = str_replace('.', '', $ultimoPercentualIncluido);
        $ultimoPercentualIncluido = str_replace(',', '', $ultimoPercentualIncluido);

        $PercentualIncluido = $_POST['flPercentualDesc'];
        $PercentualIncluido = str_replace('.', '', $PercentualIncluido);
        $PercentualIncluido = str_replace(',', '', $PercentualIncluido);

        if ( ($ultimoPercentualIncluido >= $PercentualIncluido) && (count ($arSessaoFaixas) > 0 )) {
            sistemaLegado::exibeAviso("O valor referente ao percentual informado deve ser maior que o da última faixa cadastrada."," "," ");
        } elseif ( ($ultimoValorIncluido >= $ValorIncluido) && (count ($arSessaoFaixas) > 0 )) {
             sistemaLegado::exibeAviso("O valor informado para o salário inicial deve ser maior que o da última faixa cadastrada do salário final." ,"","");
        } else {

            $arElementos['inId'] = $inProxId;
            $flSalarioInicial = str_replace('.','',$_POST['flSalarioInicial']);
            $flSalarioInicial = str_replace(',','.',$flSalarioInicial);
            $arSalarioInicial = explode('.',$flSalarioInicial);
            if ($arSalarioInicial[1] == NULL) {
               $flSalarioInicial = $flSalarioInicial.',00';
            }

            $flSalarioFinal = str_replace('.','',$_POST['flSalarioFinal']);
            $flSalarioFinal = str_replace(',','.',$flSalarioFinal);
            $arSalarioFinal = explode('.',$flSalarioFinal);
            if ($arSalarioFinal[1] == NULL) {
               $flSalarioFinal = $flSalarioFinal.',00';
            }
            $arElementos['flSalarioInicial'] = number_format($flSalarioInicial,2,',','.');
            $arElementos['flSalarioFinal']   = number_format($flSalarioFinal,2,',','.');
            $arElementos['flPercentualDesc'] = $_POST['flPercentualDesc'];

            $arSessaoFaixas[]                = $arElementos;
            Sessao::write('Faixas', $arSessaoFaixas);

            listarFaixas( $arSessaoFaixas );
        }

    break;

    case "excluiFaixa":
        $id = $_GET['inId'];
        $stMensagem = false;
        if ($_REQUEST['stAcao']=='alterar') {
            reset($arSessaoFaixas);
            while ( list( $arId ) = each( $arSessaoFaixas ) ) {
                if ($arSessaoFaixas["inId"] == $id) {
                    echo "->".$_POST['inCodPrevidencia']."<br>";
                    echo "->".$arSessaoFaixas["inId"]."<br>";
                    $obRegra->setCodPrevidencia     ( $_POST['inCodPrevidencia'] );
                    $obRegra->obRFaixa->setCodFaixa ( $arSessaoFaixas["inCodFaixa"] );
                }
            }
        }

        if ($stMensagem==false) {
            reset($arSessaoFaixas);
            while ( list( $arId ) = each( $arSessaoFaixas ) ) {
                if ($arSessaoFaixas[$arId]["inId"] != $id) {
                    $arElementos['inId']               = $arSessaoFaixas[$arId]["inId"];
                    $arElementos['flSalarioInicial']   = $arSessaoFaixas[$arId]["flSalarioInicial"];
                    $arElementos['flSalarioFinal']     = $arSessaoFaixas[$arId]["flSalarioFinal"];
                    $arElementos['flPercentualDesc']   = $arSessaoFaixas[$arId]["flPercentualDesc"];
                    $arTMP[] = $arElementos;
                }
            }
            Sessao::write('Faixas', $arTMP);
            listarFaixas( $arTMP );
        } else {
            $stJs = "sistemaLegado::alertaAviso('@ ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
    break;
    case 'preencheInner':
        if ( count( $arSessaoFaixas ) ) {
            $stJs = listarFaixas( $arSessaoFaixas, false );
        }
    break;

    case 'montaNorma':
        montaNorma();
    break;
}
if($stJs)
    sistemaLegado::executaFrameOculto($stJs);

?>

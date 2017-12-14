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
/*
 * Oculto do Formulário de Consulta do Item
 * Data de Criação   : 02/06/2009

 * @author Analista      Gelson Gonçalves
 * @author Desenvolvedor Alexandre Melo

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function montaDadosClassificacao($inCodCatalogo, $stCodEstrutural)
{
    include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogo.class.php";
    $obTAlmoxarifadoCatalogo = new TAlmoxarifadoCatalogo;
    $obTAlmoxarifadoCatalogo->setDado('cod_catalogo', $inCodCatalogo);
    $obTAlmoxarifadoCatalogo->recuperaPorChave($rsRecordSet);

    $stCatalogo = $rsRecordSet->getCampo('cod_catalogo').' - '.$rsRecordSet->getCampo('descricao');

    $obLblCatalogo = new Label;
    $obLblCatalogo->setRotulo ( "Catálogo"    );
    $obLblCatalogo->setName   ( "stDescricao" );
    $obLblCatalogo->setId     ( "stDescricao" );
    $obLblCatalogo->setValue  ( $stCatalogo   );

    $obLblClassificacao = new Label;
    $obLblClassificacao->setRotulo ( "Classificação"   );
    $obLblClassificacao->setName   ( "stClassificacao" );
    $obLblClassificacao->setId     ( "stClassificacao" );
    $obLblClassificacao->setValue  ( $stCodEstrutural  );

    $obFormulario = new Formulario();
    $obFormulario->addTitulo( "Dados da Classificação");
    $obFormulario->addComponente($obLblCatalogo);
    $obFormulario->addComponente($obLblClassificacao);

    include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoClassificacao.class.php";
    $obTAlmoxarifadoCatalogoClassificacao = new TAlmoxarifadoCatalogoClassificacao;
    $obTAlmoxarifadoCatalogoClassificacao->setDado('cod_catalogo'   , $inCodCatalogo  );
    $obTAlmoxarifadoCatalogoClassificacao->setDado('cod_estrutural' , $stCodEstrutural);
    $obTAlmoxarifadoCatalogoClassificacao->recuperaClassificacao($rsRecordSet);

    $inCount = 0;

    if ($rsRecordSet->getNumLinhas() > 0) {
        while (!$rsRecordSet->eof()) {
            $$inCount = new Label;
            $$inCount->setRotulo ( $rsRecordSet->getCampo('descricao_nivel') );
            $$inCount->setName   ( $inCount                                  );
            $$inCount->setId     ( $inCount                                  );
            $$inCount->setValue  ( $rsRecordSet->getCampo('descricao')       );

            $obFormulario->addComponente($$inCount);

            $inCount++;
            $rsRecordSet->proximo();
        }
    }
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "jq('#spnDadosCLassificacao').html('".$stHtml."');";

    return $stJs;
}

function montaAtributos($rsRecordSet)
{
    $inCount = 0;
    $obFormulario = new Formulario();
    $obFormulario->addTitulo( "Atributos");

    while (!$rsRecordSet->eof()) {
        ${"atrib_".$inCount} = new Label;
        ${"atrib_".$inCount}->setRotulo ( $rsRecordSet->getCampo('nom_atributo') );
        ${"atrib_".$inCount}->setName   ( $inCount                               );
        ${"atrib_".$inCount}->setId     ( $inCount                               );
        ${"atrib_".$inCount}->setValue  ( $rsRecordSet->getCampo('valor')        );

        $obFormulario->addComponente(${"atrib_".$inCount});

        $inCount++;
        $rsRecordSet->proximo();
    }

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "jq('#spnAtributos').html('".$stHtml."');";

    return $stJs;
}

function carregaDados($inCodigo)
{
    include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php";
    $obRAlmoxarifadoCatalogoItem = new RAlmoxarifadoCatalogoItem;
    $obRAlmoxarifadoCatalogoItem->setCodigo($inCodigo);
    $obRAlmoxarifadoCatalogoItem->consultar();

    include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php";
    $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
    $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $inCodigo);
    $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , Sessao::getExercicio());
    $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsItemUltimaCompra);

    if($rsItemUltimaCompra->getNumLinhas() > 0)
        $nuVlUltCompra = number_format(str_replace('.',',', $rsItemUltimaCompra->getCampo('vl_unitario_ultima_compra')),2,',','.');

    $stDescricao         = $obRAlmoxarifadoCatalogoItem->getDescricao();
    $stDescResumida      = $obRAlmoxarifadoCatalogoItem->getDescricaoResumida();
    $boStatus            = $obRAlmoxarifadoCatalogoItem->getAtivo();
    $inCodClassificacao  = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->getCodigo();
    $inCodCatalogo       = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getCodigo();
    $stEstrutural        = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->getEstrutural();
    $inCodTipo           = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->getCodigo();
    $stDescTipo          = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->getDescricao();
    $inCodUnidade        = $obRAlmoxarifadoCatalogoItem->obRUnidadeMedida->getCodUnidade();
    $stDescUnidade       = $obRAlmoxarifadoCatalogoItem->obRUnidadeMedida->getNome();
    $inCodGrandeza       = $obRAlmoxarifadoCatalogoItem->obRUnidadeMedida->obRGrandeza->getCodGrandeza();
    $nuEstoqueMaximo     = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoControleEstoque->getEstoqueMaximo();
    $nuEstoqueMinimo     = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoControleEstoque->getEstoqueMinimo();
    $nuPontoPedido       = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoControleEstoque->getPontoDePedido();

    $nuEstoqueMaximo     = number_format($nuEstoqueMaximo, 2, ',', '.');
    $nuEstoqueMinimo     = number_format($nuEstoqueMinimo, 2, ',', '.');
    $nuPontoPedido       = number_format($nuPontoPedido  , 2, ',', '.');

    if($boStatus == true)
        $stStatus = "Ativo";
    else
        $stStatus = "Inativo";

    $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->obRCadastroDinamico->setChavePersistenteValores(  array("cod_catalogo"=>$inCodCatalogo, "cod_classificacao"=>$inCodClassificacao, "cod_item" => $inCodigo ));
    $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsRecordSet);

    if($rsRecordSet->getNumLinhas() >0)
        $stJs .= montaAtributos($rsRecordSet);

    $stJs .= montaDadosClassificacao($inCodCatalogo, $stEstrutural);
    $stJs .= "jq('#stCatalogo').html('".$inCodCatalogo." -');";
    $stJs .= "jq('#inCodigo').html('".$stEstrutural."');";
    $stJs .= "jq('#stTipo').html('".$stDescTipo."');";
    $stJs .= "jq('#stDescResumida').html('".$stDescResumida."');";
    $stJs .= "jq('#nuVlUltCompra').html('".$nuVlUltCompra."');";
    $stJs .= "jq('#stStatus').html('".$stStatus."');";
    $stJs .= "jq('#stUnidadeMedida').html('".$stDescUnidade."');";
    $stJs .= "jq('#nuEstoqueMinimo').html('".$nuEstoqueMinimo."');";
    $stJs .= "jq('#nuPontoDePedido').html('".$nuPontoPedido."');";
    $stJs .= "jq('#nuEstoqueMaximo').html('".$nuEstoqueMaximo."');";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "carregaDados":
        $stJs = carregaDados($_GET['inCodigo']);
        break;
}

echo $stJs;

?>

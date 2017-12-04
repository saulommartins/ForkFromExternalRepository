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
    * Data de Criação   : 28/06/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.09
*/

/*
$Log$
Revision 1.5  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo1.class.php");

$obRegra        = new ROrcamentoRelatorioAnexo1;

$obRegra->obROrcamentoDespesa->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRegra->obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRegra->obROrcamentoDespesa->obROrcamentoEntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );

//seta elementos do filtro para ENTIDADE
$arFiltro = Sessao::read('filtroRelatorio');
$arNomFiltro = Sessao::read('filtroNomRelatorio');
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

$arFiltro['inCodEntidade'] = $stEntidade;
Sessao::write('filtroRelatorio',$arFiltro);

switch ($_REQUEST['stCtrl']) {
    case "MontaUnidade":
       if ($_REQUEST["inNumOrgao"]) {
            $stCombo  = "inNumUnidade";
            $stComboTxt  = "inNumUnidadeTxt";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stComboTxt.value=''; \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

            $obRegra->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inNumOrgao"]);
            $obRegra->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );
            while ( !$rsCombo->eof() ) {
                $arNomFiltro['unidade'][$rsCombo->getCampo( 'num_unidade' )] = $rsCombo->getCampo( 'nom_unidade' );
                $rsCombo->proximo();
            }
            $rsCombo->setPrimeiroElemento();

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
    SistemaLegado::executaFrameOculto( $stJs );
    break;

    default:
        $obRegra->setFiltro                             ( $stFiltro                         );
        $obRegra->setExercicio                          ( Sessao::getExercicio()                );
        $obRegra->setCodEntidade                        ( $stEntidade                       );
        $obRegra->setCodDemDespesa                      ( $arFiltro['inCodDemDespesa']);
        $obRegra->obROrcamentoDespesa->obTPeriodo->setDataInicial( $arFiltro['stDataInicial']  );
        $obRegra->obROrcamentoDespesa->obTPeriodo->setDataFinal  ( $arFiltro['stDataFinal']    );
        $obRegra->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arFiltro['inNumOrgao']);
        $obRegra->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($arFiltro['inNumUnidade']);

        if($arFiltro['inCodDemValores']==1)
            $obRegra->geraRecordSet( $arRecordSets );
        else
            $obRegra->geraRecordSetBalanco( $arRecordSets );

        Sessao::write('arRecordSet',$arRecordSets);
        //sessao->transf5 = $arRecordSets;
        $obRegra->obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo1.php" );
    break;
}
?>

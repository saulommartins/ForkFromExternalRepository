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
    * Página Oculta de Licitações para Autorização de Empenho Parcial
    * Data de Criação   : 25/09/2015

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: OCManterAutorizacaoParcial.php 65449 2016-05-23 18:17:48Z jean $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoHomologacao.class.php';
include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoLicitacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoFornecedorItem.class.php';
include_once CAM_GA_NORMAS_CLASSES.'componentes/IPopUpNorma.class.php';
include_once CAM_GA_CGM_NEGOCIO.'RCGM.class.php';
include_once CAM_GP_COM_COMPONENTES.'IMontaDotacaoDesdobramento.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoDespesa.class.php';
include_once CAM_GA_NORMAS_MAPEAMENTO.'TNorma.class.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoPreEmpenho.class.php";

$stPrograma = "ManterAutorizacaoParcial";
$pgFilt	= "FL".$stPrograma.".php";
$pgList	= "LS".$stPrograma.".php";
$pgForm	= "FM".$stPrograma.".php";
$pgProc	= "PR".$stPrograma.".php";
$pgOcul	= "OC".$stPrograma.".php";
$pgJs	= "JS".$stPrograma.".js";

function alterarItemDotacao(Request $request){
    $stHtml       = "";
    $arItens      = Sessao::read('arItens');
    $arLicitacao  = Sessao::read('arLicitacao');
    $inCodItem    = $request->get('codItem');
    $inCodCotacao = $request->get('codCotacao');
    $inCodDespesa = $request->get('codDespesa');
    
    $boMontaDespesa = false;
    
    if(!empty($inCodItem) && !empty($inCodCotacao)){
        foreach( $arItens as $chaveItem => $arItem) {
            if ( $arItem["cod_item"] == $inCodItem && $arItem["cod_cotacao"] == $inCodCotacao ) {
                foreach( $arLicitacao as $arLicitacaoTemp) {
                    $stFiltro  = " AND licitacao.cod_licitacao  = ".$arLicitacaoTemp['inCodLicitacao'];
                    $stFiltro .= " AND licitacao.cod_modalidade = ".$arLicitacaoTemp['inCodModalidade'];
                    $stFiltro .= " AND licitacao.cod_entidade   = ".$arLicitacaoTemp['inCodEntidade'];
                    $stFiltro .= " AND mapa_item.cod_item       = ".$arItem["cod_item"];
                    if (!empty($inCodDespesa))
                        $stFiltro .= " AND mapa_item_dotacao.cod_despesa = ".$inCodDespesa;

                    $obTLicitacao = new TLicitacaoLicitacao();

                    if (!empty($inCodDespesa))
                        $obTLicitacao->setDado('inCodDespesa', $inCodDespesa);
                        
                    $obTLicitacao->recuperaItensDetalhesAutorizacaoEmpenhoParcialLicitacao( $rsDetalheItens , $stFiltro);

                    $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem ();
                    $stFiltro = " WHERE mapa_cotacao.exercicio_mapa = '".$arItem["exercicio"]."'
                                    AND mapa_cotacao.cod_mapa       = ".$arItem["cod_mapa"]."
                                    AND cotacao_item.cod_item       = ".$arItem["cod_item"]." \n";

                    if (!empty($inCodDespesa))
                        $stFiltro .= " AND mapa_item_dotacao.cod_despesa = ".$inCodDespesa;

                    $stOrder = " ORDER BY cotacao_item.cod_item, sw_cgm.nom_cgm";
                    $obTComprasCotacaoFornecedorItem->recuperaItensCotacaoJulgadosAutorizacaoParcial ( $rsCotacaoItens, $stFiltro );

                    $arItens[$chaveItem]['arCotacaoItem'] = $rsCotacaoItens->getElementos();
                    Sessao::write('arItens', $arItens);

                    $obROrcamentoDespesa = new ROrcamentoDespesa;
                    $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $rsDetalheItens->getCampo('desdobramento') );
                    $obROrcamentoDespesa->listarContaDespesa($rsDotacao);
                }

                if (!empty($inCodDespesa))
                    $qtdItem            = str_replace(",",".",str_replace(".","",$arItem['arItemDespesa'][$inCodDespesa]['nuQtdeItem']));
                else
                    $qtdItem            = str_replace(",",".",str_replace(".","",$arItem['nuQtdeItem']));

                $inCgmFornecedor    = $arItem['inCgmFornecedor'];
                $stCodClassificacao = $arItem['stCodClassificacao'];

                foreach ($rsCotacaoItens->getElementos() as $key => $cotacaoItem) {
                    if($cotacaoItem['cgm_fornecedor'] == $inCgmFornecedor){
                        $nuVlUnitario = $cotacaoItem['vl_cotacao'] / $cotacaoItem['quantidade'];
                        break;
                    }
                }

                $obForm = new Form;
                $obForm->setAction( $pgProc );
                $obForm->setTarget( "oculto" );

                $obIntCodItem = new Inteiro();
                $obIntCodItem->setRotulo( 'Código'              );
                $obIntCodItem->setName  ( 'inCodItem'           );
                $obIntCodItem->setId    ( 'inCodItem'           );
                $obIntCodItem->setValue ( $arItem['cod_item']   );
                $obIntCodItem->setLabel ( true                  );

                $obLblNomItem = new Label();
                $obLblNomItem->setRotulo( 'Descrição'                   );
                $obLblNomItem->setId    ( 'stNomItem'                   );
                $obLblNomItem->setName  ( 'stNomItem'                   );
                $obLblNomItem->setValue ( $arItem['descricao_completa'] );

                $obLblQtdTotalItem = new Label();
                $obLblQtdTotalItem->setRotulo( 'Quantidade Total' );
                $obLblQtdTotalItem->setValue ( number_format($rsDetalheItens->getCampo('quantidade'), 4, ",", ".") );

                $obLblQtdAutorizadaItem = new Label();
                $obLblQtdAutorizadaItem->setRotulo( 'Quantidade Total para Dotação' );
                $obLblQtdAutorizadaItem->setValue ( number_format($rsDetalheItens->getCampo('quantidade_total_autorizada_dotacao'), 4, ",", ".") );

                $obLblSaldoAutorizarItem = new Label();
                $obLblSaldoAutorizarItem->setRotulo( 'Saldo a Autorizar' );
                $obLblSaldoAutorizarItem->setValue ( number_format($rsDetalheItens->getCampo('quantidade_restante_dotacao'), 4, ",", ".") );

                $obIntQtdeItem = new Quantidade();
                $obIntQtdeItem->setRotulo   ( 'Quantidade para Esta Autorização'    );
                $obIntQtdeItem->setId       ( 'nuQtdeItem'                          );
                $obIntQtdeItem->setName     ( 'nuQtdeItem'                          );
                $obIntQtdeItem->setValue    ( number_format($qtdItem, 4, ",", ".")  );
                $obIntQtdeItem->setMaxLength( 16                                    );
                $obIntQtdeItem->setSize     ( 23                                    );
                if (!empty($inCodDespesa))
                    $obIntQtdeItem->obEvento->setOnChange( "montaParametrosGET('verificaQuantidadeItem', 'inCodItem,inCodCotacao,nuQtdeItem,hdnNuQtdeItem,hdnNuQtdeAutorizada,inCgmFornecedor,inCodDespesa');" );

                $obVlrPrecoUnitario = new Moeda();
                $obVlrPrecoUnitario->setRotulo  ( 'Valor Unitário'                          );
                $obVlrPrecoUnitario->setId      ( 'nuVlUnitario'                            );
                $obVlrPrecoUnitario->setName    ( 'nuVlUnitario'                            );
                $obVlrPrecoUnitario->setValue   ( number_format($nuVlUnitario, 2, ",", ".") );
                $obVlrPrecoUnitario->setDecimais( 4                                         );
                $obVlrPrecoUnitario->setNull    ( true                                      );
                $obVlrPrecoUnitario->setSize    ( 23                                        );
                $obVlrPrecoUnitario->setLabel   ( true                                      );

                $obLblVlrTotalItem = new Label();
                $obLblVlrTotalItem->setRotulo   ( 'Valor Total' );
                $obLblVlrTotalItem->setId       ( 'nuVlTotal'   );
                $obLblVlrTotalItem->setName     ( 'nuVlTotal'   );
                $obLblVlrTotalItem->setValue    ( number_format($qtdItem * $nuVlUnitario, 2, ",", ".") );

                $obISelectFornecedor = new Select();
                $obISelectFornecedor->setName       ( 'inCgmFornecedor'         );
                $obISelectFornecedor->setId         ( 'inCgmFornecedor'         );
                $obISelectFornecedor->setRotulo     ( '**Fornecedor'            );
                $obISelectFornecedor->setTitle      ( 'Selecione o Fornecedor.' );
                $obISelectFornecedor->setCampoID    ( 'cgm_fornecedor'          );
                $obISelectFornecedor->setValue      ( $inCgmFornecedor          );
                $obISelectFornecedor->setCampoDesc  ( 'fornecedor'              );
                $obISelectFornecedor->addOption     ( '', 'Selecione'           );
                $obISelectFornecedor->setNull       ( true                      );
                $obISelectFornecedor->preencheCombo ( $rsCotacaoItens           );
                $obISelectFornecedor->obEvento->setOnChange( "montaParametrosGET('montaFornecedor', 'inCgmFornecedor,hdnInCGMFornecedor,inCodCotacao,inCodItem,nuQtdeItem');" );

                $obSpnAlteraFornecedorItem = new Span;
                $obSpnAlteraFornecedorItem->setId ( 'spnAlteraFornecedorItem' );

                $obLblCentroCusto = new Label();
                $obLblCentroCusto->setRotulo( 'Centro de Custo'         );
                $obLblCentroCusto->setId    ( 'stNomCentroCusto_label'  );
                $obLblCentroCusto->setValue ( $rsDetalheItens->getCampo('cod_centro').' - '.$rsDetalheItens->getCampo('nom_centro') );

                $obHdnIdCentroCusto = new Hidden;
                $obHdnIdCentroCusto->setName    ( 'inCodCentroCusto'                        );
                $obHdnIdCentroCusto->setId      ( 'inCodCentroCusto'                        );
                $obHdnIdCentroCusto->setValue   ( $rsDetalheItens->getCampo('cod_centro')   );

                $obHdnNomCentroCusto = new Hidden;
                $obHdnNomCentroCusto->setName   ( 'stNomCentroCusto'                        );
                $obHdnNomCentroCusto->setId     ( 'stNomCentroCusto'                        );
                $obHdnNomCentroCusto->setValue  ( $rsDetalheItens->getCampo('nom_centro')   );
                
                $obHdnCodDespesa = new Hidden;
                $obHdnCodDespesa->setName   ( 'inCodDespesa' );
                $obHdnCodDespesa->setId     ( 'inCodDespesa' );
                $obHdnCodDespesa->setValue  ( $request->get('codDespesa') );

                $boMontaDesdobramento = true;

                if($rsDetalheItens->getCampo('cod_despesa_atual')!='' && $rsDetalheItens->getCampo('desdobramento')!='' && $rsDetalheItens->getCampo('saldo_despesa')!=''){
                    $obInCodDespesa = new Hidden;
                    $obInCodDespesa->setName    ( 'inCodDespesa' );
                    $obInCodDespesa->setId      ( 'inCodDespesa' );
                    $obInCodDespesa->setValue   ( $rsDetalheItens->getCampo('cod_despesa_atual') );

                    $obStCodClassificacao = new Hidden;
                    $obStCodClassificacao->setName    ( 'stCodClassificacao' );
                    $obStCodClassificacao->setId      ( 'stCodClassificacao' );
                    $obStCodClassificacao->setValue   ( $rsDetalheItens->getCampo('cod_desdobramento') );

                    $obLblDotacao = new Label();
                    $obLblDotacao->setRotulo( 'Dotação Orçamentária'    );
                    $obLblDotacao->setId    ( 'stDotacao'               );
                    $obLblDotacao->setName  ( 'stDotacao'               );
                    $obLblDotacao->setValue ( $rsDetalheItens->getCampo('cod_despesa_atual').' - '.$rsDetalheItens->getCampo('nom_despesa_atual')       );

                    $obLblDesdobramento = new Label();
                    $obLblDesdobramento->setRotulo  ( 'Desdobramento'   );
                    $obLblDesdobramento->setId      ( 'stDesdobramento' );
                    $obLblDesdobramento->setName    ( 'stDesdobramento' );
                    $obLblDesdobramento->setValue   ( $rsDetalheItens->getCampo('desdobramento').' - '.$rsDetalheItens->getCampo('nom_desdobramento')   );

                    $obLblSaldoDotacao = new Label();
                    $obLblSaldoDotacao->setRotulo   ( 'Saldo da Dotação');
                    $obLblSaldoDotacao->setId       ( 'vlSaldoDotacao'  );
                    $obLblSaldoDotacao->setName     ( 'vlSaldoDotacao'  );
                    $obLblSaldoDotacao->setValue    ( number_format($rsDetalheItens->getCampo('saldo_despesa_atual'), 2, ",", ".")                      );

                    $boMontaDesdobramento = false;
                }else{
                    if($rsDetalheItens->getCampo('cod_despesa_empenho') != '' && $rsDetalheItens->getCampo('cod_conta_empenho') != ''){
                        $inCodDespesa = (empty($inCodDespesa)) ? $rsDetalheItens->getCampo('cod_despesa_empenho') : $inCodDespesa;
                        $stCodClassificacao = (empty($stCodClassificacao)) ? $rsDetalheItens->getCampo('cod_conta_empenho') : $stCodClassificacao;
                    }

                    $obMontaDotacao = new IMontaDotacaoDesdobramento();
                    $obMontaDotacao->obBscDespesa->setRotulo    ( "**Dotação Orçamentária"  );
                    $obMontaDotacao->obBscDespesa->obCampoCod->setValue ( $inCodDespesa     );
                    $obMontaDotacao->obCmbClassificacao->setNull( true                      );
                    $obMontaDotacao->setMostraSintetico         ( false                     );
                    $obMontaDotacao->obCmbClassificacao->setRotulo( "**Desdobramento"       );

                    $obInCodClassificacao = new Hidden;
                    $obInCodClassificacao->setName    ( 'codClassificacao' );
                    $obInCodClassificacao->setId      ( 'codClassificacao' );
                    $obInCodClassificacao->setValue   ( $stCodClassificacao );

                    $boMontaDespesa = true;
                }

                $obHdnIdCodCotacao = new Hidden;
                $obHdnIdCodCotacao->setName ( 'inCodCotacao' );
                $obHdnIdCodCotacao->setId   ( 'inCodCotacao' );
                $obHdnIdCodCotacao->setValue( $arItem['cod_cotacao'] );

                $obHdnNuQtdeItem = new Hidden;
                $obHdnNuQtdeItem->setName   ( 'hdnNuQtdeItem' );
                $obHdnNuQtdeItem->setId     ( 'hdnNuQtdeItem' );
                $obHdnNuQtdeItem->setValue  ( $rsDetalheItens->getCampo('quantidade_restante_dotacao') );
                
                $obHdnNuQtdAutorizada = new Hidden;
                $obHdnNuQtdAutorizada->setName   ( 'hdnNuQtdeAutorizada' );
                $obHdnNuQtdAutorizada->setId     ( 'hdnNuQtdeAutorizada' );
                $obHdnNuQtdAutorizada->setValue  ( $rsDetalheItens->getCampo('quantidade_total_autorizada_dotacao') );               
                
                $obHdnCGMCentroCusto = new Hidden;
                $obHdnCGMCentroCusto->setName   ( 'hdnInCodCentroCusto' );
                $obHdnCGMCentroCusto->setId     ( 'hdnInCodCentroCusto' );
                $obHdnCGMCentroCusto->setValue  ( $rsDetalheItens->getCampo('cod_centro') );

                $obHdnCGMFornecedor = new Hidden;
                $obHdnCGMFornecedor->setName    ( 'hdnInCGMFornecedor' );
                $obHdnCGMFornecedor->setId      ( 'hdnInCGMFornecedor' );
                $obHdnCGMFornecedor->setValue   ( $rsDetalheItens->getCampo('cgm_fornecedor') );

                $obHdnExercicioMapa = new Hidden;
                $obHdnExercicioMapa->setName    ( 'hdnStExercicioMapa' );
                $obHdnExercicioMapa->setId      ( 'hdnStExercicioMapa' );
                $obHdnExercicioMapa->setValue   ( $arItem["exercicio"] );

                $obHdnCodMapa = new Hidden;
                $obHdnCodMapa->setName  ( 'hdnInCodMapa' );
                $obHdnCodMapa->setId    ( 'hdnInCodMapa' );
                $obHdnCodMapa->setValue ( $arItem["cod_mapa"] );

                $obBtnAlterarItem = new Button;
                $obBtnAlterarItem->setName  ("btnAlterarItem");
                $obBtnAlterarItem->setId    ("btnAlterarItem");
                $obBtnAlterarItem->setValue ("Alterar Item");
                $obBtnAlterarItem->setTipo  ("button");
                $obBtnAlterarItem->obEvento->setOnClick("montaParametrosGET('alterarListaItem');");
                if($rsDetalheItens->getCampo('quantidade_restante_dotacao') <= 0 ){
                    $obBtnAlterarItem->setDisabled ( true );
                }

                $obFormularioItem = new Formulario();
                $obFormularioItem->setId('sw_table_parcial');
                $obFormularioItem->addTitulo ('Alterar Item');
                $obFormularioItem->addHidden( $obHdnIdCodCotacao        );
                $obFormularioItem->addHidden( $obHdnNuQtdeItem          );
                $obFormularioItem->addHidden( $obHdnCGMFornecedor       );
                $obFormularioItem->addHidden( $obHdnCGMCentroCusto      );
                $obFormularioItem->addHidden( $obHdnExercicioMapa       );
                $obFormularioItem->addHidden( $obHdnCodMapa             );
                $obFormularioItem->addComponente( $obIntCodItem         );
                $obFormularioItem->addComponente( $obLblNomItem         );

                if($boMontaDesdobramento){
                    $obFormularioItem->addHidden( $obInCodClassificacao );
                    $obMontaDotacao->geraFormulario ( $obFormularioItem );
                }else{
                    $obFormularioItem->addHidden( $obInCodDespesa         );
                    $obFormularioItem->addHidden( $obStCodClassificacao   );
                    $obFormularioItem->addComponente( $obLblDotacao       );
                    $obFormularioItem->addComponente( $obLblDesdobramento );
                    $obFormularioItem->addComponente( $obLblSaldoDotacao  );
                }

                $obFormularioItem->addComponente( $obLblQtdTotalItem         );
                $obFormularioItem->addComponente( $obLblQtdAutorizadaItem    );
                $obFormularioItem->addComponente( $obLblSaldoAutorizarItem   );
                $obFormularioItem->addComponente( $obIntQtdeItem             );
                $obFormularioItem->addComponente( $obVlrPrecoUnitario        );
                $obFormularioItem->addComponente( $obLblVlrTotalItem         );
                $obFormularioItem->addComponente( $obISelectFornecedor       );
                $obFormularioItem->addSpan      ( $obSpnAlteraFornecedorItem );
                $obFormularioItem->addComponente( $obLblCentroCusto          );
                $obFormularioItem->addHidden    ( $obHdnIdCentroCusto        );
                $obFormularioItem->addHidden    ( $obHdnNomCentroCusto       );
                $obFormularioItem->addHidden    ( $obHdnNuQtdAutorizada      );
                
                $obFormularioItem->addComponente( $obBtnAlterarItem );

                $obFormularioItem->montaInnerHTML();
                $stHTMLAlteraItem = $obFormularioItem->getHTML();
                $stHTMLAlteraItem = str_replace("\'","\\'",$stHTMLAlteraItem);
                $stHTMLAlteraItem = str_replace("\n","",$stHTMLAlteraItem);
                $stHTMLAlteraItem = str_replace("  ","",$stHTMLAlteraItem);
                $stHTMLAlteraItem = str_replace('"','\\"',$stHTMLAlteraItem);

                $arMontaFornecedor[0]['inCgmFornecedor']    = $inCgmFornecedor;
                $arMontaFornecedor[0]['hdnInCGMFornecedor'] = $rsDetalheItens->getCampo('cgm_fornecedor');
                $arMontaFornecedor[0]['inCodCotacao']       = $arItem['cod_cotacao'];
                $arMontaFornecedor[0]['inCodItem']          = $arItem['cod_item'];
                $arMontaFornecedor[0]['nuQtdeItem']         = number_format($qtdItem, 4, ",", ".");

                Sessao::write('arMontaFornecedor', $arMontaFornecedor);

                $arMontaDespesa[0]['inCodItem']          = $inCodItem;
                $arMontaDespesa[0]['inCodCotacao']       = $inCodCotacao;
                $arMontaDespesa[0]['boMontaDespesa']     = $boMontaDespesa; 
                $arMontaDespesa[0]['inCodDespesa']       = $inCodDespesa;
                $arMontaDespesa[0]['stCodClassificacao'] = $stCodClassificacao;
                $arMontaDespesa[0]['inCodCentro']        = $rsDetalheItens->getCampo('cod_centro');

                Sessao::write('arMontaDespesa', $arMontaDespesa);

                break;
            }
        }
    }
    
    $stJs = "jQuery('#spnDetalheDotacao').html('".$stHTMLAlteraItem."');  \n";
    return $stJs;
}

function montaSpanItens($rsRecordSet)
{
    $stJs = "";

    $rsRecordSet->addFormatacao ( 'vl_cotacao', 'NUMERIC_BR' );
    $rsRecordSet->addFormatacao ( 'quantidade', 'NUMERIC_BR' );

    $obLista = new Lista;
    $obLista->setTitulo( "Itens" );
    $obLista->setMostraPaginacao( false );

    if ( $rsRecordSet->getNumLinhas() >= 5 ) {
        $obLista->setMostraScroll( 150 );
    }

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Seq" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Item" );
    $obLista->ultimoCabecalho->setWidth( 27 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Qtde." );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor" );
    $obLista->ultimoCabecalho->setWidth( 13 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Fornecedor" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 1 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_item] - [item]" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->ultimoDado->setAlinhamento( "DIREITA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vl_cotacao" );
    $obLista->ultimoDado->setAlinhamento( "DIREITA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "fornecedor" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace( "\n", "", $stHtml);
    $stHtml = str_replace( "  ", "", $stHtml);
    $stHtml = str_replace( "'" , "\\'", $stHtml);

    $stJs = "jQuery('#spnItens').html('".$stHtml."');  \n";

    return $stJs;
}

function montaListaItensDetalhe($inCodItem, $inCodCotacao)
{
    $arItensDetalhes = Sessao::read('arItensDetalhes');
    $arItensDetalhes = (is_array($arItensDetalhes)) ? $arItensDetalhes : array();

    $arItens = Sessao::read('arItens');
    $arItens = (is_array($arItens)) ? $arItens : array();

    foreach ($arItensDetalhes as $chaveItemDetalhe => $valorItemDetalhe) {
        foreach( $arItens as $chaveItem => $arItem) {
            if ( $arItem["cod_item"] == $valorItemDetalhe['cod_item'] && $arItem["cod_cotacao"] == $valorItemDetalhe['cod_cotacao'] ) {

                if(empty($valorItemDetalhe['desdobramento']) && !empty($arItem['stCodClassificacao'])){
                    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaDespesa.class.php";
                    $obTOrcamentoContaDespesa = new TOrcamentoContaDespesa();
                    $obTOrcamentoContaDespesa->setDado("cod_conta", $arItem['stCodClassificacao']);
                    $obTOrcamentoContaDespesa->setDado("exercicio", Sessao::getExercicio());
                    $obTOrcamentoContaDespesa->recuperaPorChave( $rsOrcamentoContaDespesa );

                    if ( $rsOrcamentoContaDespesa->getNumLinhas() == 1 ){
                        $arItensDetalhes[$chaveItemDetalhe]['desdobramento'] = $rsOrcamentoContaDespesa->getCampo('cod_estrutural');
                    }
                }

                $vlUnitarioItem = $arItensDetalhes[$chaveItemDetalhe]['vl_unitario'];

                $arCotacaoItem = (is_array($arItem['arCotacaoItem'])) ? $arItem['arCotacaoItem'] : array();

                foreach ($arCotacaoItem as $chaveCotacao => $cotacaoItem) {
                    if($cotacaoItem['cgm_fornecedor'] == $arItem['inCgmFornecedor']){
                        $vlUnitarioItem = $cotacaoItem['vl_cotacao'] / $cotacaoItem['quantidade'];
                        break;
                    }
                }

                $inCodDespesa = $valorItemDetalhe['cod_despesa'];
                $nuQtdeItem = isset($arItem['arItemDespesa'][$inCodDespesa]['nuQtdeItem']) ? $arItem['arItemDespesa'][$inCodDespesa]['nuQtdeItem'] : $arItem['nuQtdeItem'];

                $arItensDetalhes[$chaveItemDetalhe]['vl_unitario']      = $vlUnitarioItem;
                $arItensDetalhes[$chaveItemDetalhe]['quantidade_saldo'] = str_replace(",",".",str_replace(".","",$nuQtdeItem));

                if($arItensDetalhes[$chaveItemDetalhe]['quantidade_saldo'] == 0){
                    $arItensDetalhes[$chaveItemDetalhe]['vl_cotacao_saldo'] = '0,00';
                } else {
                    $arItensDetalhes[$chaveItemDetalhe]['vl_cotacao_saldo'] = $vlUnitarioItem * $arItensDetalhes[$chaveItemDetalhe]['quantidade_saldo'];
                }

                $arItensDetalhes[$chaveItemDetalhe]['cgm_fornecedor']   = $arItem['inCgmFornecedor'];
                $arItensDetalhes[$chaveItemDetalhe]['fornecedor']       = sistemalegado::pegaDado("nom_cgm", "sw_cgm", "WHERE numcgm = '".$arItem['inCgmFornecedor']."' ");
                break;
            }
        }
    }

    $rsRegistros = new RecordSet();
    $rsRegistros->preenche($arItensDetalhes);

    $rsRegistros->addFormatacao ( 'vl_unitario'         , 'NUMERIC_BR'  );
    $rsRegistros->addFormatacao ( 'quantidade_saldo'    , 'NUMERIC_BR_4');
    $rsRegistros->addFormatacao ( 'vl_cotacao_saldo'    , 'NUMERIC_BR'  );

    $obRdSelecione = new Radio;
    $obRdSelecione->setName               ( "rd_dotacao" );
    $obRdSelecione->setId                 ( "" );
    $obRdSelecione->obEvento->setOnChange ( "selecionaDotacao(this)" );
    $obRdSelecione->setValue              ( "[cod_item],[cod_cotacao],[cod_despesa]" );

    $table = new Table();
    $table->setRecordset( $rsRegistros );
    $table->setSummary('Dotação');

    $table->Head->addCabecalho( 'Fornecedor'          , 30 );
    $table->Head->addCabecalho( 'Solicitação'         , 6  );
    $table->Head->addCabecalho( 'Lote'                , 5  );
    $table->Head->addCabecalho( 'Centro de Custo'     , 6  );
    $table->Head->addCabecalho( 'Reduzido Dotação'    , 7  );
    $table->Head->addCabecalho( 'Dotação Orçamentária', 12 );
    $table->Head->addCabecalho( 'Valor Unitário'      , 7  );
    $table->Head->addCabecalho( 'Quantidade'          , 9  );
    $table->Head->addCabecalho( 'Valor Total'         , 9  );
    $table->Head->addCabecalho( 'Selecione'           , 5  );

    $table->Body->addCampo( '[cgm_fornecedor] - [fornecedor]', 'E');
    $table->Body->addCampo( 'cod_solicitacao'                , 'C');
    $table->Body->addCampo( 'lote'                           , 'C');
    $table->Body->addCampo( 'cod_centro'                     , 'C');
    $table->Body->addCampo( 'cod_despesa'                    , 'C');
    $table->Body->addCampo( 'desdobramento'                  , 'C');
    $table->Body->addCampo( 'vl_unitario'                    , 'D');
    $table->Body->addCampo( 'quantidade_saldo'               , 'D');
    $table->Body->addCampo( 'vl_cotacao_saldo'               , 'D');
    $table->Body->addComponente( $obRdSelecione );

    $table->montaHTML();
    $stHTMLDetalhe = $table->getHtml();
    $stHTMLDetalhe = str_replace("\n","",$stHTMLDetalhe);
    $stHTMLDetalhe = str_replace("  ","",$stHTMLDetalhe);
    $stHTMLDetalhe = str_replace('"','\\"',$stHTMLDetalhe);
    $stHTMLDetalhe = str_replace("'","\\'",$stHTMLDetalhe);

    $obSpnDetalheDotacao = new Span;
    $obSpnDetalheDotacao->setId       ( 'spnDetalheDotacao' );

    $obFormulario = new Formulario();
    $obFormulario->addSpan   ( $obSpnDetalheDotacao );

    $obFormulario->montaInnerHTML();
    $stHTMLAlteraItem = $obFormulario->getHTML();

    $stHTMLAlteraItem = str_replace("\'","\\'",$stHTMLAlteraItem);
    $stHTMLAlteraItem = str_replace("\n","",$stHTMLAlteraItem);
    $stHTMLAlteraItem = str_replace("  ","",$stHTMLAlteraItem);
    $stHTMLAlteraItem = str_replace('"','\\"',$stHTMLAlteraItem);

    $stHTML  = $stHTMLDetalhe;
    $stHTML .= $stHTMLAlteraItem;

    return $stHTML;
}

function montaListaItens()
{
    $arItens = Sessao::read('arItens');
    $arItens = (is_array($arItens)) ? $arItens : array();

    $arLicitacao = Sessao::read('arLicitacao');
    $arLicitacao = (is_array($arLicitacao)) ? $arLicitacao : array();

    $table = new TableTree();
    $table->setId ( 'sw_tabletree_parcial' );
    $table->setName ( 'sw_tabletree_parcial' );

    $idTableTree = $table->getId();

    $inCount = 1;

    $js = " var onClickRow; \n";
    $stJsTableTree = "BloqueiaFrames(true,false); ";

    foreach ($arItens as $chaveItem => $valorItem) {
        $idLinhaTableTree = $idTableTree."_row_".$inCount;

        $stParamAdicionais  = "&inCodLicitacao=".$arLicitacao[0]['inCodLicitacao'];
        $stParamAdicionais .= "&cod_item=".$valorItem['cod_item'];
        $stParamAdicionais .= "&stExercicio=".$arLicitacao[0]['stExercicio'];
        $stParamAdicionais .= "&inCodEntidade=".$arLicitacao[0]['inCodEntidade'];
        $stParamAdicionais .= "&inCodModalidade=".$arLicitacao[0]['inCodModalidade'];
        $stParamAdicionais .= "&inCodMapa=".$arLicitacao[0]['inCodMapa'];
        $stParamAdicionais .= "&linha_table_tree=".$idLinhaTableTree;

        $stJsTableTreeItem  = $stJsTableTree;
        $stJsTableTreeItem .= "buscaValor('listarDetalheItem','".$stParamAdicionais."')";

        $js .= "jQuery('#".$idLinhaTableTree."_mais').attr('onclick', \"".$stJsTableTreeItem."\");   \n";

        $arItens[$chaveItem]['id'] = $inCount++;

        $nuQtdeItem = 0;
        $vlTotalItem = 0;

        if( is_array($valorItem['arItemDespesa']) && count($valorItem['arItemDespesa']) > 0 ){
            foreach( $valorItem['arItemDespesa'] as $inDespesa => $arItemDespesa) {
                $nuQtdeItem += str_replace(",",".",str_replace(".","",$arItemDespesa['nuQtdeItem']));
                $vlTotalItem += $arItemDespesa['vl_cotacao_saldo'];
            }
        } else {
            $nuQtdeItem = str_replace(",",".",str_replace(".","",$valorItem['nuQtdeItem']));
        }

        $arItens[$chaveItem]['nuQtdeItem'] = $nuQtdeItem;
        $arItens[$chaveItem]['vl_cotacao_saldo'] = $vlTotalItem;
    }

    $rsItens = new RecordSet();
    $rsItens->preenche($arItens);
    $rsItens->addFormatacao ( 'nuQtdeItem'          , 'NUMERIC_BR_4' );
    $rsItens->addFormatacao ( 'quantidade_saldo'    , 'NUMERIC_BR_4' );
    $rsItens->addFormatacao ( 'quantidade'          , 'NUMERIC_BR_4' );
    $rsItens->addFormatacao ( 'vl_cotacao_saldo'    , 'NUMERIC_BR'   );

    $table->setRecordset( $rsItens );
    $table->setSummary('Itens');

    $table->setArquivo( CAM_GP_LIC_INSTANCIAS.'autorizacao/OCManterAutorizacaoParcial.php' );
    // parametros do recordSet
    $table->setParametros( array( "cod_item") );
    // 	parametros adicionais
    $stParamAdicionais  = "stCtrl=listarDetalheItem&inCodLicitacao=".$arLicitacao[0]['inCodLicitacao'];
    $stParamAdicionais .= "&stExercicio=".$arLicitacao[0]['stExercicio'];
    $stParamAdicionais .= "&inCodEntidade=".$arLicitacao[0]['inCodEntidade'];
    $stParamAdicionais .= "&inCodModalidade=".$arLicitacao[0]['inCodModalidade'];
    $stParamAdicionais .= "&inCodMapa=".$arLicitacao[0]['inCodMapa'];

    $table->setComplementoParametros( $stParamAdicionais );

    $table->Head->addCabecalho( 'Item'                              , 50 );
    $table->Head->addCabecalho( 'Quantidade Total'                  , 10 );
    $table->Head->addCabecalho( 'Quantidade Autorizada'             , 10 );
    $table->Head->addCabecalho( 'Quantidade para Esta Autorização'  , 10 );
    $table->Head->addCabecalho( 'Valor Total'                       , 15 );

    $table->Body->addCampo( '[cod_item] - [descricao_completa]<br>[complemento]', 'E' );
    $table->Body->addCampo( 'quantidade'                                        , 'C' );
    $table->Body->addCampo( 'quantidade_saldo'                                  , 'C' );
    $table->Body->addCampo( 'nuQtdeItem'                                        , 'C' );
    $table->Body->addCampo( 'vl_cotacao_saldo'                                  , 'D' );
    $table->Foot->addSoma ( 'vl_cotacao_saldo'                                  , 'D' );
    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "jQuery('#spnItens').html('".$stHTML."');   \n";
    $stJs.= "jQuery('#Ok').attr('disabled', false);     \n";
    $stJs.=$js;

    return $stJs;
}

function montaVlUnitario($inCgmFornecedor = null, $nuQtdeItem = 0, $inCodItem, $inCodCotacao){

    if($inCgmFornecedor){
        $arItens = Sessao::read('arItens');
        $arItens = (is_array($arItens)) ? $arItens : array();

        foreach( $arItens as $chaveItem => $arItem) {
            if ( $arItem["cod_item"] == $inCodItem && $arItem["cod_cotacao"] == $inCodCotacao ) {
                $arCotacaoItem = (is_array($arItem['arCotacaoItem'])) ? $arItem['arCotacaoItem'] : array();
                foreach ($arCotacaoItem as $key => $cotacaoItem) {
                    if($cotacaoItem['cgm_fornecedor'] == $inCgmFornecedor){
                        $nuVlUnitario = $cotacaoItem['vl_cotacao'] / $cotacaoItem['quantidade'];
                        $stJs  = "jQuery('#nuVlUnitario').val('".number_format($nuVlUnitario, 2, ",", ".")."');         \n";
                        $stJs .= "jQuery('#nuVlUnitario_label').html('".number_format($nuVlUnitario, 2, ",", ".")."');  \n";

                        $qtdItem = str_replace(",",".",str_replace(".","",$nuQtdeItem));
                        $stJs .= "jQuery('#nuVlTotal').val('".number_format($qtdItem * $nuVlUnitario, 2, ",", ".")."'); \n";
                        $stJs .= "jQuery('#nuVlTotal').html('".number_format($qtdItem * $nuVlUnitario, 2, ",", ".")."');\n";

                        break;
                    }
                }
            }
        }
    }
    return $stJs;
}

function montaFornecedor(Request $request){
    $arItens = Sessao::read('arItens');
    $arItens   = (is_array($arItens)) ? $arItens : array();

    $inCgmFornecedor    = $request->get('inCgmFornecedor');
    $hdnInCGMFornecedor = $request->get('hdnInCGMFornecedor');
    $stHtml = '';

    if($inCgmFornecedor != $hdnInCGMFornecedor && $inCgmFornecedor!=''){
        $inNumCGMResponsavel = '';
        $inNomCGMResponsavel = '&nbsp;';
        $inCodNorma          = '';
        $stNorma             = '&nbsp;';
        $stJustificativa     = '';

        foreach( $arItens as $chaveItem => $arItem) {
            if ( $arItem["cod_item"] == $request->get('inCodItem') && $arItem["cod_cotacao"] == $request->get('inCodCotacao') ) {
                $inNumCGMResponsavel = $arItem['inNumCGMResponsavel'];
                $inNomCGMResponsavel = ($arItem['inNomCGMResponsavel']!='') ? $arItem['inNomCGMResponsavel'] : $inNomCGMResponsavel;
                $inCodNorma          = $arItem['inCodNorma'];
                $stNorma             = ($arItem['stNorma']!='') ? $arItem['stNorma'] : $stNorma;
                $stJustificativa     = $arItem['stJustificativa'];
                break;
            }
        }

        $obBscCGMResponsavel = new BuscaInner;
        $obBscCGMResponsavel->setRotulo ( "**CGM Responsável"                   );
        $obBscCGMResponsavel->setTitle  ( "Informe o código do CGM responsável" );
        $obBscCGMResponsavel->setNull   ( true                                  );
        $obBscCGMResponsavel->setId     ( "inNomCGMResponsavel"                 );
        $obBscCGMResponsavel->setValue  ( $inNomCGMResponsavel                  );
        $obBscCGMResponsavel->obCampoCod->setName   ( "inNumCGMResponsavel"     );
        $obBscCGMResponsavel->obCampoCod->setId     ( "inNumCGMResponsavel"     );
        $obBscCGMResponsavel->obCampoCod->setValue  ( $inNumCGMResponsavel      );
        $obBscCGMResponsavel->obCampoCod->obEvento->setOnChange( "montaParametrosGET('buscaResponsavel', 'inNumCGMResponsavel');" );
        $obBscCGMResponsavel->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMResponsavel','inNomCGMResponsavel','fisica','".Sessao::getId()."&stCtrl=buscaCGMResponsavel','800','550');" );

        $obIPopUpNorma = new IPopUpNorma();
        $obIPopUpNorma->obInnerNorma->setRotulo ( "**Fundamentação Legal"   );
        $obIPopUpNorma->obInnerNorma->setNull   ( true                      );
        $obIPopUpNorma->obInnerNorma->obCampoCod->setValue( $inCodNorma     );
        $obIPopUpNorma->obInnerNorma->setValue  ( $stNorma                  );
        $obIPopUpNorma->obInnerNorma->setTitle  ( "Fundamentação legal que regulamenta a aplicação da penalidade." );

        $obTxtJustificativa = new TextArea;
        $obTxtJustificativa->setId      ( 'stJustificativa'             );
        $obTxtJustificativa->setName    ( 'stJustificativa'             );
        $obTxtJustificativa->setRotulo  ( '**Justificativa'             );
        $obTxtJustificativa->setTitle   ( 'Informe a justificativa.'    );
        $obTxtJustificativa->setNull    ( true                          );
        $obTxtJustificativa->setValue   ( $stJustificativa              );

        $obFormularioFornecedor = new Formulario();
        $obFormularioFornecedor->addComponente ( $obBscCGMResponsavel );
        $obIPopUpNorma->geraFormulario         ( $obFormularioFornecedor );
        $obFormularioFornecedor->addComponente ( $obTxtJustificativa );

        $obFormularioFornecedor->montaInnerHTML();
        $stHtml = $obFormularioFornecedor->getHTML();
    }

    $stJs  = "jQuery('#spnAlteraFornecedorItem').html('".$stHtml."'); \n";
    $stJs .= montaVlUnitario($inCgmFornecedor, $request->get('nuQtdeItem'), $request->get('inCodItem'), $request->get('inCodCotacao') );

    return $stJs;
}

function montaDespesa(Request $request){
    $stJs = "";

    if($request->get('inCodDespesa')){
        $obREmpenhoAutorizacaoEmpenho = new REmpenhoPreEmpenho;
        $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodCentroCusto( $request->get('inCodCentro') );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );

        if ( $rsDespesa->getNumLinhas() > -1 ) {
            $stJs .= "jQuery('#inCodDespesaAnterior').val('".$request->get('inCodDespesa')."');          \n";
            $stJs .= "jQuery('#stNomDespesa').html(\"".$rsDespesa->getCampo('descricao')."\");           \n";

            $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho();
            $obTEmpenhoPreEmpenho->setDado( 'exercicio'  , Sessao::getExercicio()        );
            $obTEmpenhoPreEmpenho->setDado( 'cod_despesa', $request->get('inCodDespesa') );
            $obTEmpenhoPreEmpenho->recuperaSaldoDotacaoCompra( $rsSaldoAnterior );

            $nuSaldoDotacao = $rsSaldoAnterior->getCampo('saldo_anterior');
            $stJs .= "jQuery('#nuSaldoDotacao').html(\"".number_format($nuSaldoDotacao,2,',','.')."\");  \n";
            $stJs .= "jQuery('#nuHdnSaldoDotacao').val(\"".number_format($nuSaldoDotacao,2,'.','')."\"); \n";

            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa( $rsConta );

            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $rsConta->getCampo( 'cod_estrutural' ) );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( "" );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa( $rsClassificacao );

            if ( $rsClassificacao->getNumLinhas() > -1 ) {
                $stJs .= "jQuery('#stCodClassificacao').empty().append(new Option('Selecione','') );     \n";

                while ( !$rsClassificacao->eof() ) {
                    $selected = "";
                    $stMascaraReduzida = $rsClassificacao->getCampo("mascara_reduzida");
                    if ($stMascaraReduzidaOld) {
                        if ( $stMascaraReduzidaOld != substr($stMascaraReduzida,0,strlen($stMascaraReduzidaOld)) ) {
                            if ( $inCodContaOld == $request->get('stCodClassificacao') )
                                $selected = "selected";

                            $arOptions[]['reduzido']                  = $stMascaraReduzidaOld;
                            $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$inCodContaOld."','".$selected."','".$selected."'";
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
                    if ($inCodContaOld == $request->get('stCodClassificacao'))
                        $selected = "selected";
                    else
                        $selected = "";

                    $arOptions[]['reduzido'] = $stMascaraReduzidaOld;
                    $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$inCodContaOld."','".$selected."','".$selected."'";
                }

                // Remove Contas Sintéticas
                if (is_array($arOptions)) {
                    $count = 0;
                    for ( $x=0 ; $x<count($arOptions) ; $x++ ) {
                        for ( $y=0 ; $y<count($arOptions) ; $y++ ) {
                            $estruturalX = str_replace( '.', '', $arOptions[$x]['reduzido'] );
                            $estruturalY = str_replace( '.', '', $arOptions[$y]['reduzido'] );

                            if ((strpos($estruturalY,$estruturalX)!==false) && ($estruturalX !== $estruturalY) )
                                $count++;
                        }
                        if ($count>=1)
                            unset($arOptions[$x]);

                        $count = 0;
                    }

                    asort( $arOptions );

                    foreach ($arOptions as $option) {
                        $stJs .= "jQuery('#stCodClassificacao').append(new Option(".$option['option'].") );                      \n";
                    }
                }
            } else {
                $stJs .= "jQuery('#stCodClassificacao').empty().append(new Option('Selecione','') );                             \n";
            }
        } else {
            $stJs .= "jQuery('#stNomDespesa').html('&nbsp;');                                                                    \n";
            $stJs .= "jQuery('#inCodDespesa').val('');                                                                           \n";
            $stJs .= "jQuery('#nuSaldoDotacao').html('&nbsp;');                                                                  \n";
            $stJs .= "jQuery('#stCodClassificacao').empty().append(new Option('Selecione','') );                                 \n";
            $stJs .= "alertaAviso('@Dotação inválida. (".$request->get('inCodDespesa').")','form','erro','".Sessao::getId()."'); \n";
        }
    }

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case 'buscaInfoLicitacao':
        $obTLicitacaoHomolgacao = new TLicitacaoHomologacao;

        $stFiltro = "where homologacao.homologado                                           \n".
                    " and homologacao.cod_cotacao       = ".$request->get('inCodCotacao')." \n".
                    " and homologacao.exercicio_cotacao = '".Sessao::getExercicio()."'      \n";
        $obTLicitacaoHomolgacao->recuperaItensAutorizacaoParcial ( $rsItens, $stFiltro );

        if ( $rsItens->getNumLinhas() > 0 ) {
            list($inCodMapa, $stExercicioMapa) = explode('/',$request->get('inCodMapa'));

            // busca itens do mapa, agrupados
            $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem ();

            $stFiltro = " WHERE mapa_cotacao.exercicio_mapa = '".$stExercicioMapa."'
                            AND mapa_cotacao.cod_mapa       = ".$inCodMapa."
                            AND julgamento_item.ordem       = 1 \n";
            $obTComprasCotacaoFornecedorItem->recuperaItensCotacaoJulgadosAutorizacaoParcial ( $rsMapaItens, $stFiltro );

            // somar total do mapa
            $nuTotal = 0.00;

            while ( !$rsMapaItens->eof() ) {
                $nuTotal += $rsMapaItens->getCampo('vl_cotacao_saldo');
                $rsMapaItens->proximo();
            }
            $nuTotal = number_format($nuTotal,2,',','.');

            $arItens = array();
            $inCount = 0;

            foreach($rsMapaItens->getElementos() as $key => $itemMapa){
                $arItens[$inCount] = $itemMapa;

                $stFiltro  = " and licitacao.cod_licitacao  = ".$request->get('inCodLicitacao');
                $stFiltro .= " and licitacao.cod_modalidade = ".$request->get('inCodModalidade');
                $stFiltro .= " and licitacao.cod_entidade   = ".$request->get('inCodEntidade');
                $stFiltro .= " and mapa_item.cod_item       = ".$itemMapa['cod_item'];

                $obTLicitacao = new TLicitacaoLicitacao();
                $obTLicitacao->recuperaItensDetalhesAutorizacaoEmpenhoParcialLicitacao( $rsDetalheItens , $stFiltro);

                $arItensDespesa = array();
                while ( !$rsDetalheItens->eof() ) {
                    $inCodDespesa = $rsDetalheItens->getCampo('cod_despesa_atual');

                    if(!empty($inCodDespesa)){
                        $arItensDespesa[$inCodDespesa]['nuQtdeItem']            = 0;
                        $arItensDespesa[$inCodDespesa]['vl_cotacao_saldo']      = '0,00';
                        $arItensDespesa[$inCodDespesa]['inCgmFornecedor']       = $itemMapa['cgm_fornecedor'];
                        $arItensDespesa[$inCodDespesa]['inCodCentroCusto']      = $rsDetalheItens->getCampo('cod_centro');
                        $arItensDespesa[$inCodDespesa]['stNomCentroCusto']      = $rsDetalheItens->getCampo('nom_centro');
                        $arItensDespesa[$inCodDespesa]['inCodDespesa']          = $inCodDespesa;
                        $arItensDespesa[$inCodDespesa]['stCodClassificacao']    = $rsDetalheItens->getCampo('cod_desdobramento');
                        $arItensDespesa[$inCodDespesa]['inNumCGMResponsavel']   = '';
                        $arItensDespesa[$inCodDespesa]['inNomCGMResponsavel']   = '';
                        $arItensDespesa[$inCodDespesa]['inCodNorma']            = '';
                        $arItensDespesa[$inCodDespesa]['stNorma']               = '';
                        $arItensDespesa[$inCodDespesa]['stJustificativa']       = '';
                        $arItensDespesa[$inCodDespesa]['boDespesaMapaItem']     = '';
                    }

                    $arItens[$inCount]['indice']                = $inCount;
                    $arItens[$inCount]['nuQtdeItem']            = 0;
                    $arItens[$inCount]['vl_cotacao_saldo']      = '0,00';
                    $arItens[$inCount]['inCgmFornecedor']       = $itemMapa['cgm_fornecedor'];
                    $arItens[$inCount]['inCodCentroCusto']      = $rsDetalheItens->getCampo('cod_centro');
                    $arItens[$inCount]['stNomCentroCusto']      = $rsDetalheItens->getCampo('nom_centro');
                    $arItens[$inCount]['inCodDespesa']          = $rsDetalheItens->getCampo('cod_despesa_atual');
                    $arItens[$inCount]['stCodClassificacao']    = $rsDetalheItens->getCampo('cod_desdobramento');
                    $arItens[$inCount]['inNumCGMResponsavel']   = '';
                    $arItens[$inCount]['inNomCGMResponsavel']   = '';
                    $arItens[$inCount]['inCodNorma']            = '';
                    $arItens[$inCount]['stNorma']               = '';
                    $arItens[$inCount]['stJustificativa']       = '';
                    $arItens[$inCount]['boDespesaMapaItem']     = '';
                    $arItens[$inCount]['arCotacaoItem']         = array();
                    $arItens[$inCount]['arItemDespesa']         = $arItensDespesa;

                    $rsDetalheItens->proximo();
                }

                $inCount++;
            }

            $arLicitacao[0]['inCodLicitacao']   = $request->get('inCodLicitacao');
            $arLicitacao[0]['stExercicio']      = Sessao::getExercicio();
            $arLicitacao[0]['inCodEntidade']    = $request->get('inCodEntidade');
            $arLicitacao[0]['inCodModalidade']  = $request->get('inCodModalidade');
            $arLicitacao[0]['inCodMapa']        = $inCodMapa;

            Sessao::write('arItens', $arItens);
            Sessao::write('arLicitacao', $arLicitacao);

            $stJs  = montaListaItens();

            $stJs .= "jQuery('#spnLabels').html('".$stHTML."');     \n";
            $stJs .= "jQuery('#stTotalMapa').html('".$nuTotal."');  \n";
            $stJs .= "LiberaFrames(true,true);                      \n";
        }
    break;

    case 'listarDetalheItem':
        Sessao::write('arMontaFornecedor', array());
        Sessao::write('arMontaDespesa'   , array());

        $idLinhaTableTree = $request->get("linha_table_tree");
        $arLinhaTableTree = explode('_', $idLinhaTableTree);

        $stFiltro  = " and licitacao.cod_licitacao  = ".$request->get("inCodLicitacao");
        $stFiltro .= " and licitacao.cod_modalidade = ".$request->get("inCodModalidade");
        $stFiltro .= " and licitacao.cod_entidade   = ".$request->get("inCodEntidade");
        $stFiltro .= " and mapa_item.cod_item       = ".$request->get("cod_item");

        $obTLicitacao = new TLicitacaoLicitacao();
        $obTLicitacao->recuperaItensDetalhesAutorizacaoEmpenhoParcialLicitacao( $rsDetalheItens , $stFiltro);

        Sessao::write('arItensDetalhes', $rsDetalheItens->getElementos());

        $stHtmlDetalhe = montaListaItensDetalhe($request->get("cod_item"), $rsDetalheItens->getCampo('cod_cotacao'));

        $arMontaFornecedor = Sessao::read('arMontaFornecedor');
        $request->set('inCgmFornecedor'     , $arMontaFornecedor[0]['inCgmFornecedor']      );
        $request->set('hdnInCGMFornecedor'  , $arMontaFornecedor[0]['hdnInCGMFornecedor']   );
        $request->set('inCodCotacao'        , $arMontaFornecedor[0]['inCodCotacao']         );
        $request->set('inCodItem'           , $arMontaFornecedor[0]['inCodItem']            );
        $request->set('nuQtdeItem'          , $arMontaFornecedor[0]['nuQtdeItem']           );

        $arMontaDespesa = Sessao::read('arMontaDespesa');
        $request->set('boMontaDespesa'      , $arMontaDespesa[0]['boMontaDespesa']          );
        $request->set('inCodDespesa'        , $arMontaDespesa[0]['inCodDespesa']            );
        $request->set('stCodClassificacao'  , $arMontaDespesa[0]['stCodClassificacao']      );
        $request->set('inCodCentro'         , $arMontaDespesa[0]['inCodCentro']             );

        $stJs .= "<script>";
        $stJs .= "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                \n";
        $stJs .= "jQuery('#".$idLinhaTableTree."_sub_cell_2').html(\"".$stHtmlDetalhe."\"); \n";
        $stJs .= "jQuery('#".$idLinhaTableTree."_sub').show();                              \n";
        $stJs .= "jQuery('#".$idLinhaTableTree."_mais').hide();                             \n";
        $stJs .= "jQuery('#".$idLinhaTableTree."_menos').show();                            \n";
        $stJs .= montaFornecedor($request);
        if($request->get('boMontaDespesa'))
            $stJs .= montaDespesa($request);
        $stJs .= "</script>";

        $arItens = Sessao::read('arItens');
        $arItens = (is_array($arItens)) ? $arItens : array();
        $inCount = 1;

        $js .= "var onClickRow;                                                                         \n";
        foreach ($arItens as $chaveItem => $valorItem) {
            $stLinhaTemp  = $arLinhaTableTree[0];
            $stLinhaTemp .= '_'.$arLinhaTableTree[1];
            $stLinhaTemp .= '_'.$arLinhaTableTree[2];
            $stLinhaTemp .= '_'.$arLinhaTableTree[3];
            $stLinhaTemp .= '_'.$inCount;

            $stJsTableTree = "jQuery('#sw_table_parcial').empty(); jQuery('#".$idLinhaTableTree."_sub_cell_2').empty(); TableTreeLineControl( '".$idLinhaTableTree."', 'none', '', 'none');";

            if($stLinhaTemp!=$idLinhaTableTree){
                $stLinhaTemp .= '_mais';

                $js .= "var inCountAr2  = 0;                                                            \n";
                $js .= "var arOnClick   = '';                                                           \n";
                $js .= "arOnClick       = [];                                                           \n";
                $js .= "var arOnClick2  = '';                                                           \n";
                $js .= "arOnClick2      = [];                                                           \n";

                $js .= "onClickRow  = jQuery('#".$stLinhaTemp."').attr('onclick');                      \n";
                $js .= "arOnClick   = onClickRow.split(';');                                            \n";
                $js .= "for ( i = (arOnClick.length - 2); i < arOnClick.length; i++ ) {                 \n";
                $js .= "    arOnClick2[inCountAr2] = arOnClick[i];                                      \n";
                $js .= "    inCountAr2++;                                                               \n";
                $js .= "}                                                                               \n";

                $js .= "onClickRow  = arOnClick2.join(';');                                             \n";
                $js .= "jQuery('#".$stLinhaTemp."').attr('onclick', \"".$stJsTableTree."\"+onClickRow); \n";
            }
            $inCount++;
        }

        $stJsTableTree = "TableTreeLineControl( '".$idLinhaTableTree."', 'none', '', 'none'); jQuery('#Ok').attr('disabled', false); ";
        $js .= "jQuery('#".$idLinhaTableTree."_menos').attr('onclick', \"".$stJsTableTree."\");         \n";
        $js .= "LiberaFrames(true,true);                                                                \n";
        $js .= "jQuery('#Ok').attr('disabled', true);                                                   \n";
    break;

    case 'alterarItem':
        $stJs = alterarItem($request->get("inCodItem"), $request->get("inCodCotacao"));
    break;

    case 'alterarListaItem':
        $obErro = new Erro;
        $arItens = Sessao::read('arItens');
        $arItens = (is_array($arItens)) ? $arItens : array();

        $arItensDetalhes = Sessao::read('arItensDetalhes');

        $nuQtdeItem = str_replace(",",".",str_replace(".","",$request->get('nuQtdeItem')));

        $codClassificacao = $request->get('codClassificacao', 'FALSE');
        if( $codClassificacao != 'FALSE' &&  $nuQtdeItem > 0){
            if( $request->get('inCodDespesa')=='' && !$obErro->ocorreu() )
                $obErro->setDescricao( "Informe o campo Dotação Orçamentária do item ".$request->get('inCodItem')   );
            if( $request->get('stCodClassificacao')=='' && !$obErro->ocorreu() )
                $obErro->setDescricao( "Informe o campo Desdobramento do item ".$request->get('inCodItem')          );
        }

        if( $request->get('inCgmFornecedor')=='' && !$obErro->ocorreu() )
            $obErro->setDescricao( "Informe o campo Fornecedor do item ".$request->get('inCodItem')                 );

        if( $request->get('inCgmFornecedor')!=$request->get('hdnInCGMFornecedor') && !$obErro->ocorreu() ){
            if( $request->get('inNumCGMResponsavel')=='' && !$obErro->ocorreu() )
                $obErro->setDescricao( "Informe o campo CGM Responsável do item ".$request->get('inCodItem')        );

            if( $request->get('inCodNorma')=='' && !$obErro->ocorreu() )
                $obErro->setDescricao( "Informe o campo Fundamentação Legal do item ".$request->get('inCodItem')    );

            if( $request->get('stJustificativa')=='' && !$obErro->ocorreu() )
                $obErro->setDescricao( "Informe o campo Justificativa do item ".$request->get('inCodItem')          );
        }

        if ( $obErro->ocorreu() ) {
            $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."'); \n";
        } else {
            $inCodDespesa = $request->get('inCodDespesa');

            foreach( $arItens as $chaveItem => $arItem) {
                if ( $arItem["cod_item"] == $request->get('inCodItem') && $arItem["cod_cotacao"] == $request->get('inCodCotacao') ) {
                    $stNorma = $request->get('stNorma');
                    if($request->get('inCodNorma')!=''){
                        $obTNorma = new TNorma;
                        $stFiltro = " WHERE cod_norma = ".$request->get('inCodNorma');
                        $obTNorma->recuperaNormasDecreto( $rsNorma, $stFiltro );

                        $stNorma = $rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
                    }

                    if(empty($inCodDespesa)){
                        $arItens[$chaveItem]['nuQtdeItem']           = $request->get('nuQtdeItem');
                        $arItens[$chaveItem]['inCgmFornecedor']      = $request->get('inCgmFornecedor');
                        $arItens[$chaveItem]['inCodCentroCusto']     = $request->get('inCodCentroCusto');
                        $arItens[$chaveItem]['stNomCentroCusto']     = $request->get('stNomCentroCusto');
                        $arItens[$chaveItem]['inCodDespesa']         = $request->get('inCodDespesa');
                        $arItens[$chaveItem]['stCodClassificacao']   = $request->get('stCodClassificacao');
                        $arItens[$chaveItem]['inNumCGMResponsavel']  = $request->get('inNumCGMResponsavel');
                        $arItens[$chaveItem]['inNomCGMResponsavel']  = $request->get('inNomCGMResponsavel');
                        $arItens[$chaveItem]['inCodNorma']           = $request->get('inCodNorma');
                        $arItens[$chaveItem]['stNorma']              = $stNorma;
                        $arItens[$chaveItem]['stJustificativa']      = $request->get('stJustificativa');

                        if( $codClassificacao != 'FALSE' &&  $nuQtdeItem > 0){
                            $arItens[$chaveItem]['boDespesaMapaItem']= 'FALSE';
                        } else {
                            $arItens[$chaveItem]['boDespesaMapaItem']= 'TRUE';
                        }

                        if($nuQtdeItem == 0) {
                            $arItens[$chaveItem]['vl_cotacao_saldo'] = '0,00';
                        } else {
                            $vlUnitarioItem = $arItem['vl_cotacao'] / $nuQtdeItem;
                            $arCotacaoItem = (is_array($arItem['arCotacaoItem'])) ? $arItem['arCotacaoItem'] : array();

                            foreach ($arCotacaoItem as $chaveCotacao => $cotacaoItem) {
                                if($cotacaoItem['cgm_fornecedor'] == $request->get('inCgmFornecedor')){
                                    $vlUnitarioItem = $cotacaoItem['vl_cotacao'] / $cotacaoItem['quantidade'];
                                    break;
                                }
                            }
                            $arItens[$chaveItem]['vl_cotacao_saldo'] = $vlUnitarioItem * $nuQtdeItem;
                        }
                    } else {
                        foreach( $arItem['arItemDespesa'] as $inDespesa => $arItemDespesa) {
                            if($inCodDespesa == $inDespesa){
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['nuQtdeItem']           = $request->get('nuQtdeItem');
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['inCgmFornecedor']      = $request->get('inCgmFornecedor');
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['inCodCentroCusto']     = $request->get('inCodCentroCusto');
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['stNomCentroCusto']     = $request->get('stNomCentroCusto');
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['inCodDespesa']         = $request->get('inCodDespesa');
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['stCodClassificacao']   = $request->get('stCodClassificacao');
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['inNumCGMResponsavel']  = $request->get('inNumCGMResponsavel');
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['inNomCGMResponsavel']  = $request->get('inNomCGMResponsavel');
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['inCodNorma']           = $request->get('inCodNorma');
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['stNorma']              = $stNorma;
                                $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['stJustificativa']      = $request->get('stJustificativa');

                                if( $codClassificacao != 'FALSE' &&  $nuQtdeItem > 0) {
                                    $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['boDespesaMapaItem']= 'FALSE';
                                } else {
                                    $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['boDespesaMapaItem']= 'TRUE';
                                }

                                if($nuQtdeItem == 0) {
                                    $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['vl_cotacao_saldo'] = '0,00';
                                } else {
                                    $vlUnitarioItem = $arItem['vl_cotacao'] / $nuQtdeItem;
                                    $arCotacaoItem = (is_array($arItem['arCotacaoItem'])) ? $arItem['arCotacaoItem'] : array();

                                    foreach ($arCotacaoItem as $chaveCotacao => $cotacaoItem) {
                                        if($cotacaoItem['cgm_fornecedor'] == $request->get('inCgmFornecedor')){
                                            $vlUnitarioItem = $cotacaoItem['vl_cotacao'] / $cotacaoItem['quantidade'];
                                            break;
                                        }
                                    }

                                    $arItens[$chaveItem]['arItemDespesa'][$inDespesa]['vl_cotacao_saldo'] = $vlUnitarioItem * $nuQtdeItem;
                                }
                            }
                        }

                    }
                }

            }

            Sessao::write('arItensDetalhes', $arItensDetalhes);
            Sessao::write('arItens', $arItens);

            $stJs  = montaListaItens();
        }
    break;

    case 'verificaQuantidadeItem':
        
        $nuQtdeItem = str_replace(",",".",str_replace(".","",$request->get('nuQtdeItem')));
        $hdnNuQtdeItem       = $request->get('hdnNuQtdeItem');
        $hdnNuQtdeAutorizada = $request->get('hdnNuQtdeAutorizada');
        
        if(($nuQtdeItem > $hdnNuQtdeItem) || ($nuQtdeItem > $hdnNuQtdeAutorizada)){
            $nuQtdeItem = 0;
            $stJs  = "jQuery('#nuQtdeItem').val(''); \n";
            $stJs .= "jQuery('#nuQtdeItem').focus(); \n";
            $stJs .= "alertaAviso('Item(".$request->get('inCodItem').") - Quantidade Disponível de ".number_format($hdnNuQtdeItem, 4, ",", ".")."','form','erro','".Sessao::getId()."'); \n";
        }

        $stJs .= montaVlUnitario($request->get('inCgmFornecedor'), number_format($nuQtdeItem, 4, ",", "."), $request->get('inCodItem'), $request->get('inCodCotacao') );
    break;

    case 'montaFornecedor':
        $stJs .=  montaFornecedor($request);
    break;

    case 'buscaResponsavel':
    $obRegra = new RCGM;

    if ( $request->get('inNumCGMResponsavel') != "" AND $request->get('inNumCGMResponsavel') != "0" ) {
        $obRegra->setNumCGM ($request->get('inNumCGMResponsavel'));
        $obRegra->listar ($rsCGM);

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= "jQuery('#inNumCGMResponsavel').val('');                                                                           \n";
            $stJs .= "jQuery('#inNumCGMResponsavel').focus();                                                                           \n";
            $stJs .= "jQuery('input[name=inNomCGMResponsavel]').val('');                                                                \n";
            $stJs .= "jQuery('#inNomCGMResponsavel').html('&nbsp;');                                                                    \n";
            $stJs .= "alertaAviso('@Valor inválido. (".$request->get('inNumCGMResponsavel').")','form','erro','".Sessao::getId()."');   \n";
        } else{
            $stJs .= "jQuery('input[name=inNomCGMResponsavel]').val('".$rsCGM->getCampo('nom_cgm')."');                                 \n";
            $stJs .= "jQuery('#inNomCGMResponsavel').html('".$rsCGM->getCampo('nom_cgm')."');                                           \n";
        }
    } else {
        $stJs .= "jQuery('#inNumCGMResponsavel').val('');                                                                               \n";
        $stJs .= "jQuery('#inNumCGMResponsavel').focus();                                                                               \n";
        $stJs .= "jQuery('input[name=inNomCGMResponsavel]').val('');                                                                    \n";
        $stJs .= "jQuery('#inNomCGMResponsavel').html('&nbsp;');                                                                        \n";
        $stJs .= "alertaAviso('@Valor inválido. (".$request->get('inNumCGMResponsavel').")','form','erro','".Sessao::getId()."');       \n";
    }

    break;

    case 'montaVlUnitario':
        $stJs = montaVlUnitario($request->get('inCgmFornecedor'), $request->get('nuQtdeItem'), $request->get('inCodItem'), $request->get('inCodCotacao'));
    break;

    case 'montaListaItens':
        $stJs = montaListaItens();
    break;

    case 'alterarItemDotacao':
        $stJs = alterarItemDotacao($request);
    break;
}

echo $stJs;

if(isset($js))
    sistemalegado::executaFrameOculto($js);

?>
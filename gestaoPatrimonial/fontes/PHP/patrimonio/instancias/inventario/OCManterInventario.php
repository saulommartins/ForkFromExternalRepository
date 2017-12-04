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
 * Página de Oculto de Manter Inventario
 * Data de Criação: 02/10/2007

 * @author Analista:      Gelson Wolowski
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 * @ignore

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php";
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php";

include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioInventario.class.php";
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioInventarioHistoricoBem.class.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
include_once CAM_GP_PAT_COMPONENTES."ISelectSituacaoBem.class.php";

$stCtrl = $_REQUEST['stCtrl'];

function montaTableOrgao($inIdInventario, $stExercicio)
{
    $arInventario = Sessao::read('arInventario');

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche($arInventario);

    $table = new TableTree;
    $table->setRecordset($rsRecordSet);

    $table->setArquivo("OCManterInventario.php");
    $table->setParametros(array("cod_orgao"));
    $table->setComplementoParametros("stCtrl=montaTableLocal&stAcao=".$_REQUEST['stAcao']."&inIdInventario=".$inIdInventario."&stExercicio=".$stExercicio);

    $table->setSummary('Bens do Patrimônio por Órgão');

    $table->Head->addCabecalho('Órgão'         , 80);
    $table->Head->addCabecalho('Total de Bens' , 20);

    $table->Body->addCampo('[cod_estrutural] - [descricao]', 'E');
    $table->Body->addCampo('[total_bem]', 'C');

    $table->montaHTML(true);
    $stHtml = $table->getHtml();

    # Preenche o Span com a tabela de Órgãos.
    $stJs = "jQuery('#spnBemPatrimonio').html('".$stHtml."'); \n";

    return $stJs;

}

function montaTableLocal($inCodOrgao, $inIdInventario, $stExercicio)
{
    $arInventario = Sessao::read('arInventario');

    foreach ($arInventario as $key => $value) {
        if ($value['cod_orgao'] == $inCodOrgao) {
            $arLocal = $arInventario[$key];
            $inArKey = $key;
            break;
        }
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche($arLocal['local']);

    $table = new TableTree;
    $table->setRecordset($rsRecordSet);

    $table->setArquivo("OCManterInventario.php");
    $table->setParametros(array("cod_local"));
    $table->setComplementoParametros("stCtrl=montaFormAlterarBem&stAcao=".$_REQUEST['stAcao']."&inIdArOrgao=".$inArKey."&inIdInventario=".$inIdInventario."&stExercicio=".$stExercicio);

    $table->setSummary('Bens do Patrimônio por Local do Órgão');

    $table->Head->addCabecalho('Local'         , 80);
    $table->Head->addCabecalho('Total de Bens' , 20);

    $table->Body->addCampo('[descricao]', 'E');
    $table->Body->addCampo('[total_bem]', 'C');

    # Função para chamar uma pop-up que exibe os Bens do órgão/local.
    //Verificar se é o primeiro registro no exercicio
    $inIdInventario = SistemaLegado::pegaDado( "id_inventario" ," patrimonio.inventario_historico_bem"," where exercicio = '".Sessao::getExercicio()."' ");    
    if ( empty($inIdInventario) ) 
        $table->Body->addAcao('consultar', 'abreListagemBemInicial(%s, %s, %s)', array($inIdInventario, $inCodOrgao, 'cod_local'), '');
    else
        $table->Body->addAcao('consultar', 'abreListagemBem(%s, %s, %s, %s)', array($inIdInventario, $stExercicio, $inCodOrgao, 'cod_local'), '');

    $table->montaHTML();
    $stHtml = $table->getHtml();

    return $stHtml;
}

# Formulário de alteração de Bem.
function montaFormAlterarBem($arGet, $modo = 'Alterar')
{
    $arInventario = Sessao::read('arInventario');

    $inIdArOrgao = $arGet['inIdArOrgao'];

    $inCodOrgao = $arInventario[$inIdArOrgao]['cod_orgao'];
    $inCodLocal = $arGet['cod_local'];

    $inIdInventario = $arGet['inIdInventario'];
    $stExercicio    = $arGet['stExercicio'];

    foreach ($arInventario[$inIdArOrgao]['local'] as $key => $value) {
        if ($value['cod_local'] == $inCodLocal) {
            $inIdArLocal = $key;
            break;
        }
    }

    $arRecordSet = $arInventario[$inIdArOrgao]['local'][$inIdArLocal]['bem'];

    include_once CAM_GP_PAT_COMPONENTES."IPopUpBem.class.php";

    # Cria objeto de pop-up para buscar itens do Patrimonio.
    $obForm = new Form;
    $obForm->setName("frm2");
    $obForm->setId("frm2");


    $obHdnCodInventario = new Hidden;
    $obHdnCodInventario->setId    ('inHdnCodInventario');
    $obHdnCodInventario->setName  ('inHdnCodInventario');
    $obHdnCodInventario->setValue ($inIdInventario);

    $obHdnExercicio = new Hidden;
    $obHdnExercicio->setId    ('stHdnExercicio');
    $obHdnExercicio->setName  ('stHdnExercicio');
    $obHdnExercicio->setValue ($stExercicio);

    $obHdnChave = new Hidden;
    $obHdnChave->setId    ('stChave_'.$inCodOrgao.'_'.$inCodLocal);
    $obHdnChave->setName  ('stChave_'.$inCodOrgao.'_'.$inCodLocal);

    $obHdnIdArOrgao = new Hidden;
    $obHdnIdArOrgao->setId    ('inIdArOrgao_'.$inCodOrgao.'_'.$inCodLocal);
    $obHdnIdArOrgao->setName  ('inIdArOrgao_'.$inCodOrgao.'_'.$inCodLocal);
    $obHdnIdArOrgao->setValue ($inIdArOrgao);

    $obHdnIdArLocal = new Hidden;
    $obHdnIdArLocal->setId    ('inIdArLocal_'.$inCodOrgao.'_'.$inCodLocal);
    $obHdnIdArLocal->setName  ('inIdArLocal_'.$inCodOrgao.'_'.$inCodLocal);
    $obHdnIdArLocal->setValue ($inIdArLocal);

    $obHdnIdLinhaTableTree = new Hidden;
    $obHdnIdLinhaTableTree->setId    ('linha_table_tree_'.$inCodOrgao.'_'.$inCodLocal);
    $obHdnIdLinhaTableTree->setName  ('linha_table_tree_'.$inCodOrgao.'_'.$inCodLocal);
    $obHdnIdLinhaTableTree->setValue ($arGet['linha_table_tree']);

    # Parâmetros para o Formulário de Alteração.
    $stLink  = "&inCodOrgao=".$inCodOrgao;
    $stLink .= "&inCodLocal=".$inCodLocal;
    $stLink .= "&inIdArOrgao=".$inIdArOrgao;
    $stLink .= "&inIdArLocal=".$inIdArLocal;
    $stLink .= "&inIdInventario=".$inIdInventario;
    $stLink .= "&stExercicio=".$stExercicio;

    $obIPopUpBem = new IPopUpBem($obForm);
    $obIPopUpBem->setId               ( 'stNomBemOrgao_'.$inCodOrgao.'_'.$inCodLocal );
    $obIPopUpBem->setName             ( 'stNomBemOrgao_'.$inCodOrgao.'_'.$inCodLocal );
    $obIPopUpBem->obCampoCod->setId   ( 'inCodBem_'.$inCodOrgao.'_'.$inCodLocal );
    $obIPopUpBem->obCampoCod->setName ( 'inCodBem_'.$inCodOrgao.'_'.$inCodLocal );
    $obIPopUpBem->setRotulo           ( 'Bem' );
    $obIPopUpBem->setTitle            ( 'Informe o Código do Bem.' );
    $obIPopUpBem->setNull             ( true );
    $obIPopUpBem->setObrigatorioBarra ( true );
    $obIPopUpBem->setTipoBusca        ( 'bemNaoBaixado' );
    # Método criado para sobrepor o valor defaulto do componente.
    $obIPopUpBem->setValidacao        ( "ajaxJavaScript('OCManterInventario.php?inCodBem='+this.value+'".$stLink."', 'preencheBemAlteracao'); ");

    # Objeto Órgão
    $obTOrganogramaOrgao = new TOrganogramaOrgao;
    $obTOrganogramaOrgao->setDado('vigencia','now()');
    $stFiltroOrgao = "
        AND  EXISTS
             (
                SELECT  1
                  FROM  organograma.organograma
                 WHERE  organograma.cod_organograma = orgao_nivel.cod_organograma
                   AND  organograma.ativo = true
             ) ";
    $obTOrganogramaOrgao->recuperaOrgaos($rsOrgao, $stFiltroOrgao,' ORDER BY cod_estrutural');

    $obSelectOrgao = new Select;
    $obSelectOrgao->setName       ( 'inCodOrgao_'.$inCodOrgao.'_'.$inCodLocal);
    $obSelectOrgao->setId         ( 'inCodOrgao_'.$inCodOrgao.'_'.$inCodLocal);
    $obSelectOrgao->setRotulo     ( 'Órgão');
    $obSelectOrgao->setStyle      ( 'width: 250px;');
    $obSelectOrgao->setTitle      ( 'Informe o órgão.');
    $obSelectOrgao->setDisabled   ( true );
    $obSelectOrgao->setNull       ( false );
    $obSelectOrgao->setCampoId    ( 'cod_orgao');
    $obSelectOrgao->setCampoDesc  ( '[cod_estrutural] - [descricao]');
    $obSelectOrgao->addOption     ( '', 'Selecione', 'selected');
    $obSelectOrgao->preencheCombo ( $rsOrgao );

    # Objeto Local
    $obTOrganogramaLocal = new TOrganogramaLocal;
    $obTOrganogramaLocal->recuperaTodos($rsLocal, '', ' ORDER BY descricao');

    $obSelectLocal = new Select;
    $obSelectLocal->setName       ( 'inCodLocal_'.$inCodOrgao.'_'.$inCodLocal);
    $obSelectLocal->setId         ( 'inCodLocal_'.$inCodOrgao.'_'.$inCodLocal);
    $obSelectLocal->setRotulo     ( 'Local' );
    $obSelectLocal->setStyle      ( 'width: 250px;' );
    $obSelectLocal->setTitle      ( 'Selecione a descrição do Local.' );
    $obSelectLocal->setDisabled   ( true );
    $obSelectLocal->setNull       ( false );
    $obSelectLocal->setCampoId    ( 'cod_local' );
    $obSelectLocal->setCampoDesc  ( 'descricao' );
    $obSelectLocal->addOption     ( '', 'Selecione', 'selected' );
    $obSelectLocal->preencheCombo ( $rsLocal );

    include_once TPAT."TPatrimonioSituacaoBem.class.php";
    $obTPatrimonioSituacaoBem = new TPatrimonioSituacaoBem;
    $obTPatrimonioSituacaoBem->recuperaTodos($rsSituacaoBem,'',' ORDER BY cod_situacao');

    # Objeto Situação
    $obSelectSituacao = new Select;
    $obSelectSituacao->setName       ( 'inCodSituacao_'.$inCodOrgao.'_'.$inCodLocal);
    $obSelectSituacao->setId         ( 'inCodSituacao_'.$inCodOrgao.'_'.$inCodLocal);
    $obSelectSituacao->setRotulo     ( 'Situação');
    $obSelectSituacao->setTitle      ( 'Informe a situação.');
    $obSelectSituacao->setDisabled   ( true );
    $obSelectSituacao->setNull       ( false);
    $obSelectSituacao->setCampoId    ( 'cod_situacao');
    $obSelectSituacao->setCampoDesc  ( '[cod_situacao] - [nom_situacao]');
    $obSelectSituacao->addOption     ( '', 'Selecione', 'selected');
    $obSelectSituacao->preencheCombo ( $rsSituacaoBem);

    # Objeto Descrição
    $obTxtDescricao = new TextArea;
    $obTxtDescricao->setRotulo    ( 'Observação');
    $obTxtDescricao->setName      ( "stObservacao_".$inCodOrgao.'_'.$inCodLocal );
    $obTxtDescricao->setId        ( "stObservacao_".$inCodOrgao.'_'.$inCodLocal );
    $obTxtDescricao->setValue     ( $stObservacao );
    $obTxtDescricao->setRows      ( 1 );
    $obTxtDescricao->setMaxCaracteres( 100 );
    $obTxtDescricao->setStyle     ( "width: 300px"  );
    $obTxtDescricao->setDisabled  ( true );

    # Parametros necessarios para a funçao de Salvar.
    $stParamsAlterar  = "stChave_".$inCodOrgao."_".$inCodLocal;
    $stParamsAlterar .= ",inCodOrgao_".$inCodOrgao."_".$inCodLocal;
    $stParamsAlterar .= ",inCodLocal_".$inCodOrgao."_".$inCodLocal;
    $stParamsAlterar .= ",inCodSituacao_".$inCodOrgao."_".$inCodLocal;
    $stParamsAlterar .= ",stObservacao_".$inCodOrgao."_".$inCodLocal;
    $stParamsAlterar .= ",inCodBem_".$inCodOrgao."_".$inCodLocal;
    $stParamsAlterar .= ",inIdArOrgao_".$inCodOrgao."_".$inCodLocal;
    $stParamsAlterar .= ",inIdArLocal_".$inCodOrgao."_".$inCodLocal;
    $stParamsAlterar .= ",linha_table_tree_".$inCodOrgao."_".$inCodLocal;
    $stParamsAlterar .= ",inIdInventario";
    $stParamsAlterar .= ",stExercicio";

    # Constroi o botão de Alterar
    $obBtnAlterar = new Button;
    $obBtnAlterar->setId    ( 'obBtnAlterar' );
    $obBtnAlterar->setName  ( 'obBtnAlterar' );
    $obBtnAlterar->setValue ( 'Alterar'      );
    $obBtnAlterar->obEvento->setOnClick("var chave         = jQuery('#stChave_" . $inCodOrgao . "_" . $inCodLocal . "').val();
                                         var cod_orgao     = jQuery('#inCodOrgao_" . $inCodOrgao . "_" . $inCodLocal . " option:selected').val(); 
                                         var cod_local     = jQuery('#inCodLocal_" . $inCodOrgao . "_" . $inCodLocal . " option:selected').val(); 
                                         var situacao      = jQuery('#inCodSituacao_" . $inCodOrgao . "_" . $inCodLocal . " option:selected').val();
                                         var observacao    = jQuery('#stObservacao_" . $inCodOrgao."_" . $inCodLocal . "').val();
                                         var cod_bem       = jQuery('#inCodBem_" . $inCodOrgao . "_" . $inCodLocal . "').val();
                                         var ar_orgao      = jQuery('#inIdArOrgao_" . $inCodOrgao . "_" . $inCodLocal . "').val();
                                         var ar_local      = jQuery('#inIdArLocal_" . $inCodOrgao. "_" . $inCodLocal . "').val();
                                         var tabletree     = jQuery('#linha_table_tree_" . $inCodOrgao . "_" . $inCodLocal . "').val();
                                         var id_inventario = jQuery('#inHdnCodInventario').val();
                                         var exercicio     = jQuery('#stHdnExercicio').val();
                                         executaFuncaoAjax('salvaDadoBem','&stChave='+chave+'&inCodOrgao='+cod_orgao+'&inCodLocal='+cod_local+'&inCodSituacao='+situacao+
                                                                          '&stObservacao='+observacao+'&inCodBem='+cod_bem+'&inIdArOrgao='+ar_orgao+'&inIdArLocal='+ar_local+
                                                                          '&linha_table_tree='+tabletree+'&inIdInventario='+id_inventario+'&stExercicio='+exercicio
                                                          );"
                                );
    
    # Constroi o botão de Cancelar a alteração, retornando ao Grupo.
    $obBtnCancelar = new Button;
    $obBtnCancelar->setId    ( 'obBtnCancelar' );
    $obBtnCancelar->setName  ( 'obBtnCancelar' );
    $obBtnCancelar->setValue ( 'Cancelar'      );
    $obBtnCancelar->obEvento->setOnClick ( "TableTreeLineControl( '".$arGet['linha_table_tree']."' , 'none', '', 'none');");

    $obFormulario = new Formulario;
    $obFormulario->setForm($obForm);
    $obFormulario->addHidden ( $obHdnCodInventario );
    $obFormulario->addHidden ( $obHdnExercicio );
    $obFormulario->addHidden ( $obHdnChave );
    $obFormulario->addHidden ( $obHdnIdArOrgao );
    $obFormulario->addHidden ( $obHdnIdArLocal );
    $obFormulario->addHidden ( $obHdnIdLinhaTableTree );

    $obFormulario->addTitulo     ( 'Alterar Item desse Órgão e Local');
    $obFormulario->addComponente ( $obIPopUpBem      );
    $obFormulario->addComponente ( $obSelectOrgao    );
    $obFormulario->addComponente ( $obSelectLocal    );
    $obFormulario->addComponente ( $obSelectSituacao );
    $obFormulario->addComponente ( $obTxtDescricao   );

    $obFormulario->defineBarra   ( array($obBtnAlterar, $obBtnCancelar), 'left', '' );

    $obFormulario->montaHTML();
    $stHtml = $obFormulario->getHtml();

    Sessao::write('arInventario', $arInventario);

    return $stHtml;
}

function salvaDadoBem($arGet)
{
    foreach ($arGet as $stChave => $stValor) {
        $inResult = strpos($stChave, 'stChave');
        if ($inResult !== false) {
            list($stNomeObjeto, $inKeyOrgao, $inKeyLocal) = explode("_", $stChave);
        }
    }
    
    $inCodOrgao       = $arGet["inCodOrgao"];
    $inCodLocal       = $arGet["inCodLocal"];
    $inCodSituacao    = $arGet["inCodSituacao"];
    $stObservacao     = $arGet["stObservacao"];
    $inIdArOrgao      = $arGet["inIdArOrgao"];
    $inIdArLocal      = $arGet["inIdArLocal"];
    $inCodBem         = $arGet["inCodBem"];
    $arLinhaTableTree = $arGet["linha_table_tree"];
    $inIdInventario   = $arGet['inIdInventario'];
    $stExercicio      = $arGet['stExercicio'];


    $stJs = $stMsgErro = "";

    # Validação dos selects da tela de Alteração do Bem.
    if (!is_numeric($inCodOrgao)) {
        $stMsgErro = "Preencha o campo Órgão";
    } elseif (!is_numeric($inCodLocal)) {
        $stMsgErro = "Preencha o campo Local";
    } elseif (!is_numeric($inCodSituacao)) {
        $stMsgErro = "Preencha o campo Situação";
    }

    if (!empty($stMsgErro)) {
        echo "alertaAviso('@".$stMsgErro.".','form','erro','".Sessao::getId()."');";
    } else {
        # Fecha a TableTree aberta após salvar os dados.
        echo "TableTreeLineControl('".$arLinhaTableTree."' , 'none', '', 'none'); \n";

        # Atualiza a base de dados logo após o alterar na lista, evitando que
        # o usuário por algum motivo perca suas modificações.
        $obTPatrimonioInventarioHistoricoBem = new TPatrimonioInventarioHistoricoBem;

        Sessao::setTrataExcecao(true);

        $obTPatrimonioInventarioHistoricoBem->setDado('exercicio'     , $stExercicio);
        $obTPatrimonioInventarioHistoricoBem->setDado('id_inventario' , $inIdInventario);
        $obTPatrimonioInventarioHistoricoBem->setDado('cod_bem'       , $inCodBem);
        $obTPatrimonioInventarioHistoricoBem->recuperaPorChave($rsInventarioHistoricoBem);

        $stTimestampHistorico = $rsInventarioHistoricoBem->getCampo('timestamp_historico');

        $obTPatrimonioInventarioHistoricoBem->setDado('timestamp_historico' , $stTimestampHistorico);
        $obTPatrimonioInventarioHistoricoBem->setDado('timestamp'           , date('Y-m-d H:i:s.ms'));
        $obTPatrimonioInventarioHistoricoBem->setDado('cod_orgao'           , $inCodOrgao);
        $obTPatrimonioInventarioHistoricoBem->setDado('cod_local'           , $inCodLocal);
        $obTPatrimonioInventarioHistoricoBem->setDado('cod_situacao'        , $inCodSituacao);
        $obTPatrimonioInventarioHistoricoBem->setDado('descricao'           , $stObservacao);
        $obTPatrimonioInventarioHistoricoBem->alteracao();

        Sessao::encerraExcecao();

    }
}

switch ($stCtrl) {

    # Monta TableTree com os Locais do Órgão.
    case 'montaTableLocal':
        $inIdInventario = $_GET['inIdInventario'];
        $stExercicio    = $_GET['stExercicio'];
        $inCodOrgao     = $_GET['cod_orgao'];

        $stJs = montaTableLocal($inCodOrgao, $inIdInventario, $stExercicio);
    break;

    # Monta formulário para alterar o Bem do Patrimônio.
    case "montaFormAlterarBem":
        $stJs = montaFormAlterarBem($_GET);
    break;

    # Salva no Array e na base as alterações.
    case "salvaDadoBem":
        salvaDadoBem($_GET);
    break;

    # Preenche com as informações do Bem no formulário de alterar.
    case "preencheBemAlteracao":

        $stJs           = "";
        $stMsgErro      = "";
        $inCodBem       = $_GET['inCodBem'];
        $inIdArOrgao    = $_GET['inIdArOrgao'];
        $inIdArLocal    = $_GET['inIdArLocal'];
        $inCodOrgao     = $_GET['inCodOrgao'];
        $inCodLocal     = $_GET['inCodLocal'];
        $stObservacao   = $_GET['stObservacao'];
        $boBemBaixado   = $_GET['boBemBaixado'];
        $inIdInventario = $_GET['inIdInventario'];
        $stExercicio    = $_GET['stExercicio'];

        $stJs .= "jQuery('#inCodBem_".$inCodOrgao."_".$inCodLocal."').val('');                                                                   \n";
        $stJs .= "jQuery('#stNomBemOrgao_".$inCodOrgao."_".$inCodLocal."').html('&nbsp;');                                                            \n";
        $stJs .= "jQuery('#inCodOrgao_".$inCodOrgao."_".$inCodLocal."').attr('disabled', 'disabled').val('');    \n";
        $stJs .= "jQuery('#inCodLocal_".$inCodOrgao."_".$inCodLocal."').attr('disabled', 'disabled').val('');    \n";
        $stJs .= "jQuery('#inCodSituacao_".$inCodOrgao."_".$inCodLocal."').attr('disabled', 'disabled').val(''); \n";
        $stJs .= "jQuery('#stObservacao_".$inCodOrgao."_".$inCodLocal."').attr('disabled', 'disabled').val('');  \n";

        if (is_numeric($inCodBem)) {
            # Validação se o Item requisitado pertence ao Órgão e Local.

            if (is_numeric($inCodOrgao) && is_numeric($inCodLocal)) {

                $obTPatrimonioBem = new TPatrimonioBem;
                $obTPatrimonioBem->setDado('cod_bem'   , $inCodBem);
                $obTPatrimonioBem->setDado('cod_orgao' , $inCodOrgao);
                $obTPatrimonioBem->setDado('cod_local' , $inCodLocal);

                $stFiltro = " GROUP BY  historico_bem.timestamp ";

                $stOrdem  = " ORDER BY  historico_bem.timestamp DESC   \n";
                $stOrdem .= "    LIMIT  1                              \n";

                $obTPatrimonioBem->recuperaBemExistente($rsBemExistente, $stFiltro, $stOrdem);

                if ($rsBemExistente->getCampo('total') == 0) {
                    $stMsgErro = "Esse Bem (".$inCodBem.") não pertence a esse Órgão e Local.";
                } else {
                    $stDescBem = SistemaLegado::pegaDado('descricao', 'patrimonio.bem', 'WHERE cod_bem = '.$inCodBem);
                }
            }

            # Validação para verificar se o item existe e não está baixado.
            if (empty($stMsgErro)) {

                $obTPatrimonioInventarioHistoricoBem = new TPatrimonioInventarioHistoricoBem;
                $obTPatrimonioInventarioHistoricoBem->setDado('id_inventario' , $inIdInventario);
                $obTPatrimonioInventarioHistoricoBem->setDado('exercicio'     , $stExercicio);
                $obTPatrimonioInventarioHistoricoBem->setDado('cod_bem'       , $inCodBem);
                $obTPatrimonioInventarioHistoricoBem->recuperaPorChave($rsInventarioHistoricoBem);

                $inCodOrgaoNew    = $rsInventarioHistoricoBem->getCampo('cod_orgao');
                $inCodLocalNew    = $rsInventarioHistoricoBem->getCampo('cod_local');
                $inCodSituacaoNew = $rsInventarioHistoricoBem->getCampo('cod_situacao');
                $stObservacaoNew  = $rsInventarioHistoricoBem->getCampo('descricao');

                # Preenche os campos com os dados e os habilita para alteração.
                $stJs .= "jQuery('#inCodBem_".$inCodOrgao."_".$inCodLocal."').val('".$inCodBem."');                                     \n";
                $stJs .= "jQuery('#stNomBemOrgao_".$inCodOrgao."_".$inCodLocal."').html('".addslashes($stDescBem)."');                       \n";
                $stJs .= "jQuery('#inCodOrgao_".$inCodOrgao."_".$inCodLocal."').removeAttr('disabled').val('".$inCodOrgaoNew."');       \n";
                $stJs .= "jQuery('#inCodLocal_".$inCodOrgao."_".$inCodLocal."').removeAttr('disabled').val('".$inCodLocalNew."');       \n";
                $stJs .= "jQuery('#inCodSituacao_".$inCodOrgao."_".$inCodLocal."').removeAttr('disabled').val('".$inCodSituacaoNew."'); \n";
                $stJs .= "jQuery('#stObservacao_".$inCodOrgao."_".$inCodLocal."').removeAttr('disabled').val('".$stObservacaoNew."');   \n";
            } else {
                $stJs .= "alertaAviso('@".$stMsgErro.".','form','erro','".Sessao::getId()."');";
            }
        }

    break;

    # Método responsável por popular as tabelas de Inventário com o último histórico do Bem.
    case 'recuperaCargaInicial':

        $stExercicio = $_REQUEST['stExercicio'];

        # Inclui um novo Inventário e carrega o último status do Bem do Patrimônio.
        if ($_REQUEST['stAcao'] == 'incluir') {            
            $obTPatrimonioInventario = new TPatrimonioInventario;
            $obTPatrimonioInventario->setDado('exercicio', Sessao::getExercicio() );
            $obTPatrimonioInventario->proximoCod($inIdInventario, $boTransacao);
        
        } else {
            $inIdInventario = $_REQUEST['inIdInventario'];
        }
                
        # Filtro para listar somente órgãos que tenham bens vinculados.
        $obTOrganogramaOrgao = new TOrganogramaOrgao;
        $obTOrganogramaOrgao->setDado('vigencia','now()');

        if ($_REQUEST['stAcao'] == 'alterar') {
            $stFiltroOrgao = "  AND  inventario_historico_bem.id_inventario = ".$inIdInventario."
                                AND  inventario_historico_bem.exercicio     = '".$stExercicio."'
                            ";
        }

        $obTOrganogramaOrgao->recuperaOrgaosInventario($rsOrgao, $stFiltroOrgao,' ORDER BY cod_estrutural');

        while (!$rsOrgao->eof()) {
            $inCountOrgao = $rsOrgao->getCorrente()-1;

            $inCodOrgao = $rsOrgao->getCampo('cod_orgao');

            $arInventario[$inCountOrgao]['cod_orgao']      = $rsOrgao->getCampo('cod_orgao');
            $arInventario[$inCountOrgao]['descricao']      = $rsOrgao->getCampo('descricao');
            $arInventario[$inCountOrgao]['cod_estrutural'] = $rsOrgao->getCampo('cod_estrutural');
            
            # Filtro para listar somente locais que tenham bens vinculados.
            $obTOrganogramaLocal = new TOrganogramaLocal;
            $obTOrganogramaLocal->setDado('id_inventario',$inIdInventario);
            $obTOrganogramaLocal->setDado('acao',$_REQUEST['stAcao']);
            
            if ($_REQUEST['stAcao'] == 'incluir') {
                $stFiltroLocal = "  WHERE historico_bem.cod_orgao = ".$rsOrgao->getCampo('cod_orgao')."
                                    AND bem_baixado.cod_bem IS NULL
                                ";
            }else{
                $stFiltroLocal = "  WHERE inventario_historico_bem.id_inventario = ".$inIdInventario."
                                    AND inventario_historico_bem.exercicio       = '".$stExercicio."'
                                    AND  historico_bem.cod_local 	     	     = local.cod_local
                                    AND historico_bem.cod_orgao                  = ".$rsOrgao->getCampo('cod_orgao')."
                                    AND bem_baixado.cod_bem IS NULL
                            ";
            }
            
            $obTOrganogramaLocal->recuperaTodosTotalizado($rsLocal, $stFiltroLocal,' ORDER BY cod_local');            

            $countTotalBem = 0;

            while (!$rsLocal->eof()) {

                $inCountLocal = $rsLocal->getCorrente()-1;
                $inCodLocal   = $rsLocal->getCampo('cod_local');
                $stDescLocal  = $rsLocal->getCampo('descricao');

                $arInventario[$inCountOrgao]['local'][$inCountLocal]['cod_local'] = $inCodLocal;
                $arInventario[$inCountOrgao]['local'][$inCountLocal]['descricao'] = $stDescLocal;
                $arInventario[$inCountOrgao]['local'][$inCountLocal]['total_bem'] = $rsLocal->getCampo('total');

                $countTotalBem += $rsLocal->getCampo('total');

                $rsLocal->proximo();
            }

            # Guarda o número total de Bens vinculados ao Órgão.
            $arInventario[$inCountOrgao]['total_bem'] = $countTotalBem;

            $rsOrgao->proximo();
        }

        Sessao::write('arInventario', $arInventario);
        
        # Atualiza o Label que exibe o código do Inventário.
        $stJs .= "jQuery('#inIdInventario').val('".$inIdInventario."');  \n";
        $stJs .= "jQuery('#stIdInventario').html('".$inIdInventario."'); \n";
        $stJs .= montaTableOrgao($inIdInventario, $stExercicio);

        $stJs .= "LiberaFrames(true, true);";

    break;

}

if (!empty($stJs)) {
    echo $stJs;
}

?>

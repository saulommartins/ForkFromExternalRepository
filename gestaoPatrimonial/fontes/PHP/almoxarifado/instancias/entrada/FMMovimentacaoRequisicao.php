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
    * Página de Formulario de Inclusao/Alteracao do CID
    * Data de Criação: 04/01/2006

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    $Id: FMMovimentacaoRequisicao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.03.11

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoRequisicao.class.php");
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterial.class.php");
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoRequisicao.class.php");
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoContaDespesaItem.class.php";

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoRequisicao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once($pgJs);

$obRAlmoxarifadoRequisicao = new RAlmoxarifadoRequisicao();
$obRAlmoxarifadoRequisicao->setCodigo($_REQUEST['inCodRequisicao']);
$obRAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->setCodigo($_REQUEST['inCodAlmoxarifado']);
$obRAlmoxarifadoRequisicao->setExercicio($_REQUEST['stExercicio']);
$obRAlmoxarifadoRequisicao->consultar();

$stExercicio       = $obRAlmoxarifadoRequisicao->getExercicio();
$inCodigo          = $obRAlmoxarifadoRequisicao->getCodigo();
$inCodAlmoxarifado = $obRAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->getCodigo();
$stNomAlmoxarifado = $obRAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->obRCGMAlmoxarifado->getNomCGM();
$dtRequisicao      = $obRAlmoxarifadoRequisicao->getDataRequisicao();
$stObservacao      = $obRAlmoxarifadoRequisicao->getObservacao();
$inCgmRequisitante = $obRAlmoxarifadoRequisicao->obRCGMRequisitante->obRCGM->getNumCGM();
$stNomRequisitante = $obRAlmoxarifadoRequisicao->obRCGMRequisitante->obRCGM->getNomCGM();
$inCgmSolicitante  = $obRAlmoxarifadoRequisicao->obRCGMSolicitante->getNumCGM();
$stNomSolicitante  = $obRAlmoxarifadoRequisicao->obRCGMSolicitante->getNomCGM();

Sessao::write('Valores', array());
Sessao::write('Original', array());

Sessao::write("Solicitante",$stNomSolicitante);
Sessao::write("devolucao", $stAcao == 'entrada' );

for ($inPos = 0; $inPos < count($obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem); $inPos++) {
        $inSaldo = $inSaldoRequisitado = $inSaldoAtendido = 0;
        $arElementos = array();

        $boDisabled = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getAtivo();

        if ($boDisabled == "") {
            $boDisabled = true;
        } else {
            $boDisabled = false;
        }

        $arElementos['inCodAlmoxarifado'] = $inCodAlmoxarifado;
        $arElementos['inCodRequisicao']   = $inCodigo;
        $arElementos['stExercicio']       = $stExercicio;
        $arElementos['inId']              = $inPos;
        $arElementos['disabled']          = $boDisabled;
        $arElementos['cod_tipo_item']     = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->obRAlmoxarifadoTipoItem->getCodigo();
        $arElementos['cod_item']          = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo();
        $arElementos['desc_item']         = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getDescricao();
        $arElementos['desc_unidade']      = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->obRUnidadeMedida->getNome();
        $arElementos['inCodCatalogo']     = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getCodigo();
        $arElementos['inCodClassificacao']= $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->obRAlmoxarifadoClassificacao->getCodigo();
        $arElementos['cod_marca']         = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo();
        $arElementos['desc_marca']        = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRMarca->getDescricao();
        $arElementos['cod_centro']        = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo();
        $arElementos['desc_centro']       = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getDescricao();
        $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRFrotaItem->listar($rsRecordSet);
        if ($rsRecordSet->getNumLinhas() > 0) {
            $arElementos['boItemFrota'] = true;
        } else {
            $arElementos['boItemFrota'] = false;
        }

        //valida se os itens estão configurados para lançamento contábil
        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem = new TContabilidadeConfiguracaoLancamentoContaDespesaItem;
        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado( 'cod_item', $arElementos['cod_item'] );
        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado( 'exercicio', Sessao::getExercicio() );
        $obErro = $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->consultarItem();

        $arElementos['cod_conta_despesa'] = $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->getDado( 'cod_conta_despesa' );

        $boUsarMarca = true;
        include_once ( TALM."TAlmoxarifadoCatalogoItemMarca.class.php" );
        $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;
        $stFiltro = " and acim.cod_item = ".$arElementos['cod_item'];
        $stFiltro .= " and spfc.cod_almoxarifado = ".$inCodAlmoxarifado;

        $obTAlmoxarifadoCatalogoItemMarca->recuperaItemMarcaComSaldo( $rsMarcas, $stFiltro );
        $boUsarMarca = count($rsMarcas)-1;

        $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->retornaSaldoEstoque($inSaldo, "", $boUsarMarca);
        $arElementos['saldo_atual'] = number_format($inSaldo, 4, ',', '.');
        $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->retornaSaldoRequisitado($inSaldoRequisitado);
        $arElementos['saldo_req']   = number_format($inSaldoRequisitado, 4, ',', '.');
        $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->retornaSaldoAtendido($inSaldoAtendido);
        $arElementos['saldo_atend'] = number_format($inSaldoAtendido, 4, ',', '.');

        if ($stAcao == 'saida') {
           if(($inSaldoRequisitado - $inSaldoAtendido) <= $inSaldo)
              $inSaldoDisponivel = $inSaldoRequisitado - $inSaldoAtendido;
           else
                $inSaldoDisponivel = $inSaldo;
                $arElementos['quantidade']  = number_format($inSaldoDisponivel, 4, ',', '.');
        } else
           $arElementos['quantidade']  = number_format($inSaldoAtendido, 4, ',', '.');

            $stComplemento = "";

            $stFiltroComplemento = " Where lancamento_requisicao.cod_requisicao = ".$inCodigo." \n";
            $stFiltroComplemento .= " and lancamento_requisicao.exercicio = '".$stExercicio."'  \n";
            $stFiltroComplemento .= " and lancamento_requisicao.cod_marca = ".$arElementos['cod_marca']." \n";
            $stFiltroComplemento .= " and lancamento_requisicao.cod_centro = ".$arElementos['cod_centro']." \n";
            $stFiltroComplemento .= " and lancamento_requisicao.cod_item = ".$arElementos['cod_item']." \n";
            $stFiltroComplemento .= " and lancamento_requisicao.cod_almoxarifado = ".$inCodAlmoxarifado." \n";

            $TAlmoxarifadoLancamentoRequisicao = new TAlmoxarifadoLancamentoRequisicao;
            $TAlmoxarifadoLancamentoRequisicao->recuperaUltimoLancamentoItem($rsCodLancamento,$stFiltroComplemento);
            $inCodUltimoLancamento = $rsCodLancamento->arElementos[0]['lancamento'];

            $TAlmoxarifadoLancamentoRequisicao->recuperaTodos($rsLancamentoRequisicao,$stFiltroComplemento);
            $arElementos['cod_lancamento'] = $rsLancamentoRequisicao->getCampo('cod_lancamento');

            if ($inCodUltimoLancamento != "") {
                $stFiltroComplemento .= " and lancamento_requisicao.cod_lancamento = ".$inCodUltimoLancamento." \n";
                $stFiltroComplemento .= " and lancamento_requisicao.cod_lancamento = lancamento_material.cod_lancamento \n";
                $stFiltroComplemento .= " and lancamento_requisicao.cod_item = lancamento_material.cod_item \n";
                $stFiltroComplemento .= " and lancamento_requisicao.cod_marca = lancamento_material.cod_marca \n";
                $stFiltroComplemento .= " and lancamento_requisicao.cod_almoxarifado = lancamento_material.cod_almoxarifado \n";
                $stFiltroComplemento .= " and lancamento_requisicao.cod_centro = lancamento_material.cod_centro \n";
                $TAlmoxarifadoLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial;
                $TAlmoxarifadoLancamentoMaterial->recuperaComplementoItem($rsComplemento,$stFiltroComplemento);
                $stComplemento = $rsComplemento->arElementos[0]['complemento'];
            }

            $arElementos['complemento'] = $stComplemento;
            $arElementos['perecivel']   = $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->obRAlmoxarifadoTipoItem->getCodigo() == 2;

            if ($arElementos['perecivel']) {

                $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->addPerecivel();
                $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->roUltimoPerecivel->listar($rsPerecivel, " ORDER BY TO_CHAR(dt_validade,'yyyymmdd') ASC");

                $inPosLotes = 0;
                $disp = $inSaldoDisponivel;

                while (!$rsPerecivel->eof()) {

                    $quantidadeLote = 0;
                    $arLotes['inId']          = $inPosLotes++;
                    $arLotes['lote']          = $rsPerecivel->getCampo('lote');
                    $arLotes['dt_validade']   = $rsPerecivel->getCampo('dt_validade');
                    $arLotes['dt_fabricacao'] = $rsPerecivel->getCampo('dt_fabricacao');

                    $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->roUltimoPerecivel->setLote( $rsPerecivel->getCampo('lote') );

                    $obRAlmoxarifadoRequisicao->arRAlmoxarifadoRequisicaoItem[$inPos]->obRAlmoxarifadoEstoqueItem->roUltimoPerecivel->retornaSaldoLote($inSaldoLote);

                    $arLotes['saldo'] = number_format($inSaldoLote, 4, ',', '.');

                    if ($disp > 0) {
                        if ($inSaldoLote >= $disp) {
                             $quantidadeLote = $disp ;
                             $disp = 0;
                        } else {
                             $quantidadeLote = $inSaldoLote;
                             $disp = $disp - $inSaldoLote;
                        }
                    }
                    $arLotes['quantidade'] = number_format($quantidadeLote, 4, ',', '.');
                    $arElementos['ValoresLotes'][] = $arLotes;
                    $rsPerecivel->proximo();
                }
            }

            $arrValores[] = $arElementos;
            $arrOriginal[] = $arElementos;
            Sessao::write("Valores",$arrValores);
            Sessao::write("Original",$arrOriginal);

}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

//Define o objeto de exercicío
$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "stExercicio" );
$obHdnExercicio->setValue( $stExercicio );

//Define o objeto de código de requisição
$obHdnRequisicao = new Hidden;
$obHdnRequisicao->setName ( "inCodRequisicao" );
$obHdnRequisicao->setValue( $inCodigo );

//Define a data da requisição
$obLblDtRequisicao = new Hidden;
$obLblDtRequisicao->setName        ( "hdnDtRequisicao" );
$obLblDtRequisicao->setValue       ( $dtRequisicao     );

//Define o objeto de código de almoxarifado
$obHdnAlmoxarifado = new Hidden;
$obHdnAlmoxarifado->setName ( "inCodAlmoxarifado" );
$obHdnAlmoxarifado->setValue( $inCodAlmoxarifado );

//Define o objeto de código de último lançamento
$obHdnCodUltimoLancamento = new Hidden;
$obHdnCodUltimoLancamento->setName  ( "inCodUltimoLancamento" );
$obHdnCodUltimoLancamento->setValue ( $inCodUltimoLancamento );

$obHdnCGMUsuario = new Hidden;
$obHdnCGMUsuario->setName  ( "stCGMUsuario" );
$obHdnCGMUsuario->setID    ( "stCGMUsuario" );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo        ( "Exercício"                      );
$obLblExercicio->setName          ( "stExercicio"                    );
$obLblExercicio->setId            ( "stExercicio"                    );
$obLblExercicio->setValue         ( $stExercicio );

$obLblCodigo = new Label;
$obLblCodigo->setRotulo        ( "Código"                      );
$obLblCodigo->setName          ( "inCodigo"                    );
$obLblCodigo->setId            ( "inCodigo"                    );
$obLblCodigo->setValue         ( $inCodigo);

$obLblAlmoxarifado = new Label;
$obLblAlmoxarifado->setRotulo        ( "Almoxarifado"                      );
$obLblAlmoxarifado->setName          ( "stAlmoxarifado"                    );
$obLblAlmoxarifado->setId            ( "stAlmoxarifado"                    );
$obLblAlmoxarifado->setValue         ( $inCodAlmoxarifado."-".$stNomAlmoxarifado);

$obLblRequisicao = new Label;
$obLblRequisicao->setRotulo        ( "Data de Requisição"              );
$obLblRequisicao->setName          ( "dtRequisicao"                    );
$obLblRequisicao->setId            ( "dtRequisicao"                    );
$obLblRequisicao->setValue         ( $dtRequisicao                     );

$obLblObservacao = new Label;
$obLblObservacao->setRotulo        ( "Observação"                      );
$obLblObservacao->setName          ( "stObservação"                    );
$obLblObservacao->setId            ( "stObservação"                    );
$obLblObservacao->setValue         ( $stObservacao);

$obLblRequisitante = new Label;
$obLblRequisitante->setRotulo        ( "Requisitante"                      );
$obLblRequisitante->setName          ( "stRequisitante"                    );
$obLblRequisitante->setId            ( "stRequisitante"                    );
$obLblRequisitante->setValue         ( $inCgmRequisitante."-".$stNomRequisitante );

$obLblSolicitante = new Label;
$obLblSolicitante->setRotulo        ( "Solicitante"                      );
$obLblSolicitante->setName          ( "stSolicitante"                    );
$obLblSolicitante->setId            ( "stSolicitante"                    );
$obLblSolicitante->setValue         ( $inCgmSolicitante."-".$stNomSolicitante);

$obSpnDadosItem = new Span();
$obSpnDadosItem->setId("spnDadosItem");

$obSpnListaItens = new Span();
$obSpnListaItens->setId("spnListaItens");

SistemaLegado::executaFramePrincipal("buscaDado('preencheSpanListaItens');");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ("UC-03.03.11");
$obFormulario->addHidden     ( $obHdnCtrl         );
$obFormulario->addHidden     ( $obHdnAcao         );
$obFormulario->addHidden     ( $obHdnExercicio    );
$obFormulario->addHidden     ( $obHdnRequisicao   );
$obFormulario->addHidden     ( $obHdnAlmoxarifado );
$obFormulario->addHidden     ( $obLblDtRequisicao );
$obFormulario->addHidden     ( $obHdnCodUltimoLancamento );
$obFormulario->addHidden     ( $obHdnCGMUsuario   );
$obFormulario->addComponente ( $obLblExercicio    );
$obFormulario->addComponente ( $obLblCodigo       );
$obFormulario->addComponente ( $obLblAlmoxarifado );
$obFormulario->addComponente ( $obLblRequisicao   );
$obFormulario->addComponente ( $obLblObservacao   );
$obFormulario->addComponente ( $obLblRequisitante );
$obFormulario->addComponente ( $obLblSolicitante  );
$obFormulario->addSpan       ( $obSpnDadosItem    );
$obFormulario->addSpan       ( $obSpnListaItens   );

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    if ($stAcao == "saida") {
      $obOk  = new Ok(false);
      $obOk->obEvento->setOnClick("validaUsuarioSecundario('".$obOk->obEvento->getOnClick()."');");

      $obCancelar  = new Cancelar;
      $obCancelar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.'&stFiltroSigla='.$_GET['stFiltroSigla'].'&stFiltroDescricao='.$_GET['stFiltroDescricao']."','telaPrincipal');");

      $obFormulario->defineBarra( array( $obOk, $obCancelar ) );
    } else {
      $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.'&stFiltroSigla='.$_GET['stFiltroSigla'].'&stFiltroDescricao='.$_GET['stFiltroDescricao'] );
    }
}

$obFormulario->show();

// Ao clicar no botão OK, desabilita o botão para evitar duplicidade.
//echo "<script>jQuery(':button').click(function () { jQuery(':button').attr('disabled', 'disabled'); });</script>";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

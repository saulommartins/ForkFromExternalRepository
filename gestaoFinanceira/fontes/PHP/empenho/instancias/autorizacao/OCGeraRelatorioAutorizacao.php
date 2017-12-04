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
 * Página de Relatório
 * Data de Criação   : 17/04/2009

 * @author Analista:      Tonismar Régis Bernardo
 * @author Desenvolvedor: Henrique Boaventura

 $Id: OCGeraRelatorioAutorizacao.php 63841 2015-10-22 19:14:30Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoAutorizacaoEmpenho.class.php';

$preview = new PreviewBirt(2,10,9);
$preview->setVersaoBirt('2.5.0');
$preview->setFormato('pdf');
$arAutorizacao = Sessao::read('arAutorizacao');

# Teste para manter o flarFiltroSessaouxo padrão usado na GF.
if (!is_array($arAutorizacao)) {
    $arDuplicarAutorizacao = Sessao::read('arDuplicarAutorizacao');
    
    if ($arDuplicarAutorizacao) {
        $inCodEntidade           = $arDuplicarAutorizacao['inCodEntidade'];
        $stExercicio             = $arDuplicarAutorizacao['stExercicio'];
        $inCodPreEmpenho         = $arDuplicarAutorizacao['inCodPreEmpenho'];
        $inCodPreEmpenhoAnulada  = $arDuplicarAutorizacao['inCodPreEmpenhoAnulada'];
        $inCodAutorizacao        = $arDuplicarAutorizacao['inCodAutorizacao'];
        $inCodAutorizacaoAnulada = $arDuplicarAutorizacao['inCodAutorizacaoAnulada'];
        $stAcao                  = $arDuplicarAutorizacao['stAcao'];
    } else {
        $inCodEntidade    = $_REQUEST['inCodEntidade'];
        $stExercicio      = $_REQUEST['stExercicio'];
        $inCodPreEmpenho  = $_REQUEST['inCodPreEmpenho'];
        $inCodAutorizacao = $_REQUEST['inCodAutorizacao'];
        $stAcao           = $_REQUEST['stAcao'];
    }
    
    $preview->addParametro('cod_entidade'   , $inCodEntidade);
    $preview->addParametro('exercicio'      , $stExercicio);
    $preview->addParametro('cod_pre_empenho', $inCodPreEmpenho);
    $preview->addParametro('cod_autorizacao', $inCodAutorizacao);

    //recupera a hora e a data da autorizacao/anulacao
    $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho();
    
    if (isset($inCodAutorizacaoAnulada)) {
        $obTEmpenhoAutorizacaoEmpenho->setDado('cod_autorizacao', $inCodAutorizacaoAnulada);
        $preview->addParametro('cod_autorizacao_anulada', $inCodAutorizacaoAnulada);
        $preview->addParametro('cod_pre_empenho_anulada', $inCodPreEmpenhoAnulada);
        Sessao::write('acao', 260);
    } else {
        $obTEmpenhoAutorizacaoEmpenho->setDado('cod_autorizacao', $inCodAutorizacao);
        $preview->addParametro('cod_autorizacao_anulada', $inCodAutorizacao);
        $preview->addParametro('cod_pre_empenho_anulada', $inCodPreEmpenho);
    }
    
    $obTEmpenhoAutorizacaoEmpenho->setDado('cod_entidade',$inCodEntidade);
    $obTEmpenhoAutorizacaoEmpenho->setDado('exercicio'   , $stExercicio);
    
    if (Sessao::read('boLicitacaoCompra') <> 1){
        if ($stAcao == 'autorizacao') {
            $obTEmpenhoAutorizacaoEmpenho->recuperaPorChave($rsAutorizacao);
            $preview->addParametro('dt_cabecalho',$rsAutorizacao->getCampo('dt_autorizacao'));
        } else {
            $obTEmpenhoAutorizacaoEmpenho->recuperaDadosAutorizacaoAnulada($rsAutorizacao);
            $preview->addParametro('dt_cabecalho',implode('/',array_reverse(explode('-',$rsAutorizacao->getCampo('dt_anulacao')))));
        }
        
        $preview->addParametro('hora_cabecalho', substr($rsAutorizacao->getCampo('hora'),0,5));
        $preview->addParametro('tipo_relatorio', $stAcao);
    } else {
        $preview->addParametro('dt_cabecalho'  , $_REQUEST['dt_cabecalho']);
        $preview->addParametro('hora_cabecalho', $_REQUEST['hora_cabecalho']);
        $preview->addParametro('tipo_relatorio', $_REQUEST['tipo_relatorio']);
    }
    
    $preview->preview();

} elseif (count($arAutorizacao) >= 1) {
   
    # Fluxo usado para gerar relatórios em lote (opção de download)

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once CAM_FW_COMPONENTES."Table/Table.class.php";

    foreach ($arAutorizacao as $key => $value) {
        $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho;
        $obTEmpenhoAutorizacaoEmpenho->setDado('cod_autorizacao' , $value['inCodAutorizacao']);
        $obTEmpenhoAutorizacaoEmpenho->setDado('cod_entidade'    , $value['inCodEntidade']   );
        $obTEmpenhoAutorizacaoEmpenho->setDado('exercicio'       , $value['stExercicio']     );
        
        $obTEmpenhoAutorizacaoEmpenho->recuperaPorChave($rsAutorizacao);
        
        while (!$rsAutorizacao->eof()) {
            $arAutorizacao[$key]['stDtCabecalho']   = $rsAutorizacao->getCampo('dt_autorizacao');
            $arAutorizacao[$key]['stHoraCabecalho'] = substr($rsAutorizacao->getCampo('hora'),0,5);
            
            $rsAutorizacao->proximo();
        }
        
        # Entidade
        $inNumCgmEntidade = SistemaLegado::pegaDado("numcgm"  , "orcamento.entidade" , " WHERE cod_entidade = ".$value['inCodEntidade']);
        $stEntidade       = SistemaLegado::pegaDado("nom_cgm" , "sw_cgm"             , " WHERE numcgm = ".$inNumCgmEntidade);
        $arAutorizacao[$key]['stEntidade'] = $stEntidade;
    }

    $rsDadoAutorizacao = new RecordSet;
    $rsDadoAutorizacao->preenche($arAutorizacao);

    $obTable = new Table;
    $obTable->setRecordset($rsDadoAutorizacao);
    $obTable->setSummary('Arquivos para download');

    $obTable->Head->addCabecalho('Autorização' , 25);
    $obTable->Head->addCabecalho('Entidade'    , 75);

    $obTable->Body->addCampo ("Autorização [inCodAutorizacao]/[stExercicio]");
    $obTable->Body->addCampo ("[inCodEntidade] - [stEntidade] ");
    
    # Recria o link para o  <a href> e simula o click.
    $obTable->Body->addAcao ("baixar", "jQuery(this).attr('href', 'OCGeraRelatorioAutorizacao.php?inCodEntidade=%s&stExercicio=%s&inCodPreEmpenho=%s&inCodAutorizacao=%s&dt_cabecalho=%s&hora_cabecalho=%s&tipo_relatorio=autorizacao'); jQuery(this).attr('target', 'oculto');", array('inCodEntidade', 'stExercicio', 'inCodPreEmpenho', 'inCodAutorizacao', 'stDtCabecalho', 'stHoraCabecalho'));

    $obTable->montaHTML();

    # Monta o Formulário com a tabela para download.
    $obFormulario = new Formulario;
    $obFormulario->addForm ( null );

    $obSpan = new Span;
    $obSpan->setId    ('obSpanAutorizacao');
    $obSpan->setValue ($obTable->getHtml());

    $obBtnVoltar = new Button;
    $obBtnVoltar->setName      ( "Voltar" );
    $obBtnVoltar->setValue     ( "Voltar" );
    $obBtnVoltar->setStyle     ( "width: 60px" );
    $obBtnVoltar->setDefinicao ( "Voltar" );

    $arFiltroSessao = Sessao::read('filtro');
    if (is_array($arFiltroSessao)) {
        $stFiltro = http_build_query($arFiltroSessao);
    }

    // Link de origem = Compra Direta
    if (Sessao::read('modulo') == 35 && Sessao::read('acao') == 1730) {
        $stLinkRetorno = CAM_GP_COM_INSTANCIAS."compraDireta/LSManterAutorizacao.php";
    } elseif (Sessao::read('modulo') == 37 && Sessao::read('acao') == 1741) {
        // Link de origem = Licitação -> Emitir Autorização de Empenho
        $stLinkRetorno = CAM_GP_LIC_INSTANCIAS."autorizacao/FLManterAutorizacao.php";
    } elseif (Sessao::read('modulo') == 37 && Sessao::read('acao') == 3086) {
        // Link de origem = Licitação -> Emitir Autorização de Empenho Parcial
        $stLinkRetorno = CAM_GP_LIC_INSTANCIAS."autorizacao/FLManterAutorizacao.php";
    }

    $obBtnVoltar->obEvento->setOnClick("window.location.href='".$stLinkRetorno."?".$stFiltro."'");
    $obFormulario->addSpan ($obSpan);
    $obFormulario->defineBarra(array($obBtnVoltar), 'left', '');
    $obFormulario->show();
    
    Sessao::write('arAutorizacao', true); // Necessário para poder entrar no if e ser considerado TRUE quando carregar novamente a página e gerar o relatório.
    Sessao::write('boLicitacaoCompra', true); // Necessário para poder identificar de onde está buscando as informações para o relatório
}
?>
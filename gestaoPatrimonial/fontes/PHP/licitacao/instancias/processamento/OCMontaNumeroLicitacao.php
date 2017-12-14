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

    * Arquivo de popup de busca de Item do catálogo
    * Data de Criação: 19/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * Casos de uso: uc-03.05.16

    $Id: OCMontaNumeroLicitacao.php 63474 2015-08-31 21:54:15Z carlos.silva $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once TLIC.'TLicitacaoLicitacao.class.php';
include_once CAM_GP_LIC_COMPONENTES.'IMontaNumeroLicitacao.class.php';

$obTLicitacaoLicitacao = new TLicitacaoLicitacao();
$stJs = isset($stJs) ? $stJs : "";

function preencheLicitacao($rsRecordSet, $stComponente)
{
    $stJs = "limpaSelect(f.".$stComponente.",1);\n";
    $obIMontaNumeroLicitacao = Sessao::read('IMontaNumeroLicitacao');
    if ( $rsRecordSet->getNumLinhas() == 1 && $obIMontaNumeroLicitacao->getSelecionaAutomaticamenteLicitacao() ) {
        $selected = 'selected';
        $inNumeroLicitacaoSelecionado = $rsRecordSet->getCampo('cod_licitacao');
        $stJs .= carregaProcesso( $rsRecordSet->getCampo('cod_licitacao') );
    } else {
        $selected = '';
        $inNumeroLicitacaoSelecionado = '';
        $stJs .= "d.getElementById('stProcesso').innerHTML = '&nbsp;';\n";
    }

    while ( !$rsRecordSet->eof() ) {
        $stJs .= "f.".$stComponente."[".$rsRecordSet->getCorrente()."] = new Option('".$rsRecordSet->getCampo('cod_licitacao')."','".$rsRecordSet->getCampo('cod_licitacao')."');\n";
        $rsRecordSet->proximo();
    }

    $stJs .= "f.".$stComponente.".value = '".$inNumeroLicitacaoSelecionado."';\n";

    return $stJs;
}

function carregaProcesso($inCodLicitacao)
{
    global $obTLicitacaoLicitacao;
    global $request;

    if ($_REQUEST['stExercicioLicitacao'] && $_REQUEST['inCodEntidade'] && $_REQUEST['inCodModalidade'] && $inCodLicitacao) {
        $obTLicitacaoLicitacao->setDado( 'exercicio' , $_REQUEST['stExercicioLicitacao'] );
        $obTLicitacaoLicitacao->setDado( 'cod_entidade', $_REQUEST['inCodEntidade'] );
        $obTLicitacaoLicitacao->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
        $obTLicitacaoLicitacao->setDado( 'cod_licitacao', $inCodLicitacao );
        $obTLicitacaoLicitacao->recuperaLicitacao( $rsProcesso );

        $obTLicitacaoLicitacao->recuperaValorLicitacao($rsValorLicitacao);

        Sessao::write('rsProcesso', $rsProcesso);

        if ( $rsProcesso->getNumLinhas() > 0 ) {
            $arProc = explode("/", $rsProcesso->getCampo('processo'));
            $js = "d.getElementById('stProcesso').innerHTML = '".str_pad( $arProc[0], 5, "0", STR_PAD_LEFT ).'/'.$arProc[1]."'; ";
            $js .= "d.getElementById('hdnProcesso').value = '".$rsProcesso->getCampo('processo')."'; ";

            if ($request->get('boSetarValorLicitacao')) {
                $js .= "d.getElementById('valorLicitacao').innerHTML = '".number_format($rsValorLicitacao->getCampo('valor_total'),2,',','.')."'; ";
            }

            $js.= "f.hdnDtLicitacao.value = '".$rsProcesso->getCampo('dt_licitacao')."';\n";
        } else {
            $js = "d.getElementById('stProcesso').innerHTML = '&nbsp;';\n";
            $js .= "d.getElementById('stProcesso').innerHTML = '';\n";
        }
    } else {
        $js = "d.getElementById('stProcesso').innerHTML = '&nbsp;';\n";
        $js.= "d.getElementById('hdnProcesso').innerHTML = '';\n";
        $js.= "f.inCodLicitacao.value = 0;\n";
    }

    return $js;
}

function limpaSelect()
{
    $stJs = "f.stExercicioLicitacao.value = '".Sessao::getExercicio()."';\n";
    $stJs.= "f.inCodEntidade.value = '';\n";
    $stJs.= "f.stNomEntidade.value = '';\n";
    $stJs.= "f.inCodModalidade.value = 0;\n";
    $stJs.= "f.inCodLicitacao.value = '';\n";
    $stJs.= "d.getElementById('stProcesso').innerHTML = '&nbsp;';\n";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {

    case 'carregaModalidade':
        if ( $request->get('stExercicioLicitacao') && $request->get('inCodEntidade') ) {
        } else {
            $stJs.= "f.inCodModalidade.value = '';\n";
            $stJs.= "f.inCodLicitacao.value = '';\n";
            $stJs.= "limpaSelect(f.inCodLicitacao,1);\n";
            $stJs.= "d.getElementById('stProcesso').innerHTML = '&nbsp;';\n";
        }
    break;
    case 'carregaLicitacao':
    case 'carregaLicitacaoContrato':
        if ($request->get('boSetarValorLicitacao'))
            echo "d.getElementById('valorLicitacao').innerHTML = '&nbsp;'; ";

        if ($_REQUEST['stExercicioLicitacao'] && $_REQUEST['inCodEntidade'] && $_REQUEST['inCodModalidade']) {
            $obTLicitacaoLicitacao->setDado( 'exercicio'     , $_REQUEST['stExercicioLicitacao'] );
            $obTLicitacaoLicitacao->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidade'] );
            $obTLicitacaoLicitacao->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
    
            if ( $request->get('inNumEdital'))
                $obTLicitacaoLicitacao->setDado( 'num_edital', $_REQUEST['inNumEdital'] );
            
            $stFiltro = "";
            // verifica licitações que ja tenham editais e estão anulados ou que apenas não tenham editais
            if ($_REQUEST['stFiltraLicitacao'] == true) {
                $stFiltro .= " AND ( EXISTS (
                                           SELECT 1
                                             FROM licitacao.edital, licitacao.edital_anulado
                                                , ( SELECT max(edital.num_edital) as num_edital
                                             FROM licitacao.edital
                                            WHERE edital.cod_licitacao  = ll.cod_licitacao
                                              AND edital.cod_modalidade = ll.cod_modalidade
                                              AND edital.cod_entidade   = ll.cod_entidade
                                              AND edital.exercicio      = ll.exercicio
                                                  ) AS maxEdital
                                            WHERE edital_anulado.num_edital = maxEdital.num_edital
                                           )
                                    OR
                                    NOT EXISTS(
                                              SELECT 1
                                                FROM licitacao.edital
                                               WHERE edital.cod_licitacao  = ll.cod_licitacao
                                                 AND edital.cod_modalidade = ll.cod_modalidade
                                                 AND edital.cod_entidade   = ll.cod_entidade
                                                 AND edital.exercicio      = ll.exercicio
                                              )";
    
                if ($_REQUEST['numLicitacao']) {
                    $stFiltro.= "              AND ll.cod_licitacao <> ".$_REQUEST['numLicitacao']." ";
                }
                $stFiltro.="     )";
            }
            
            if ($_REQUEST['stCtrl'] == 'carregaLicitacaoContrato') {
                $stFiltro.="
                      AND EXISTS (
                           SELECT 1
                                 FROM licitacao.homologacao
                            LEFT JOIN licitacao.homologacao_anulada
                                   ON  homologacao_anulada.num_homologacao = homologacao.num_homologacao
                                  AND  homologacao_anulada.num_adjudicacao = homologacao.num_adjudicacao
                                  AND  homologacao_anulada.cod_entidade = homologacao.cod_entidade
                                  AND  homologacao_anulada.cod_modalidade = homologacao.cod_modalidade
                                  AND  homologacao_anulada.cod_licitacao = homologacao.cod_licitacao
                                  AND  homologacao_anulada.exercicio_licitacao = homologacao.exercicio_licitacao
                                  AND  homologacao_anulada.cod_item = homologacao.cod_item
                                  AND  homologacao_anulada.cod_cotacao = homologacao.cod_cotacao
                                  AND  homologacao_anulada.lote = homologacao.lote
                                  AND  homologacao_anulada.exercicio_cotacao = homologacao.exercicio_cotacao
                                  AND  homologacao_anulada.cgm_fornecedor = homologacao.cgm_fornecedor
                                WHERE  homologacao.cod_entidade = ll.cod_entidade
                                  AND  homologacao.cod_modalidade = ll.cod_modalidade
                                  AND  homologacao.cod_licitacao = ll.cod_licitacao
                                  AND  homologacao.exercicio_licitacao = ll.exercicio
                                  AND  homologacao_anulada.num_homologacao is null
                                )";
            }
    
            if ($request->get('stFiltro') == 'adjudicacao' || $request->get('stFiltro') == 'julgamento') {            
                $stFiltro.= "
                    -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
                    AND CASE WHEN ll.cod_modalidade in (1,2,3,4,5,6,7,10,11) THEN
                                EXISTS (
                                      SELECT 1
                                        FROM licitacao.edital
                                       WHERE edital.cod_licitacao = ll.cod_licitacao
                                         AND edital.cod_modalidade = ll.cod_modalidade
                                         AND edital.cod_entidade = ll.cod_entidade
                                         AND edital.exercicio = ll.exercicio
                                       )
        
                    -- Para as modalidades 8,9 é facultativo possuir um edital
                             WHEN ll.cod_modalidade in (8,9) THEN
                                EXISTS (
                                      SELECT 1
                                        FROM licitacao.edital
                                       WHERE edital.cod_licitacao = ll.cod_licitacao
                                         AND edital.cod_modalidade = ll.cod_modalidade
                                         AND edital.cod_entidade = ll.cod_entidade
                                         AND edital.exercicio = ll.exercicio
                                       )
                         OR NOT EXISTS (
                                      SELECT 1
                                        FROM licitacao.edital
                                       WHERE edital.cod_licitacao = ll.cod_licitacao
                                         AND edital.cod_modalidade = ll.cod_modalidade
                                         AND edital.cod_entidade = ll.cod_entidade
                                         AND edital.exercicio = ll.exercicio
                                       )
                    END
                     
                    AND EXISTS (  SELECT 1
                                    FROM compras.mapa_cotacao
                              INNER JOIN compras.julgamento
                                      ON julgamento.exercicio   = mapa_cotacao.exercicio_cotacao
                                     AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                                   WHERE mapa_cotacao.cod_mapa       = ll.cod_mapa
                                     AND mapa_cotacao.exercicio_mapa = ll.exercicio_mapa
                                     AND NOT EXISTS ( SELECT 1
                                                        FROM compras.cotacao_anulada
                                                       WHERE cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                                                         AND cotacao_anulada.exercicio   = mapa_cotacao.exercicio_cotacao
                                                    )
                                 )";
    
                if ($_REQUEST['stFiltro'] == 'adjudicacao') {
                    $stFiltro.= "
                     AND EXISTS (
                              SELECT 1
                                FROM compras.mapa_cotacao
                          INNER JOIN compras.cotacao
                                  ON cotacao.exercicio      = mapa_cotacao.exercicio_cotacao
                                 AND cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                                 AND cotacao.cod_cotacao    = (SELECT MAX(MC.cod_cotacao)
                                                                 FROM compras.mapa_cotacao AS MC
                                                                WHERE MC.exercicio_mapa = mapa_cotacao.exercicio_mapa
                                                                  AND MC.cod_mapa = mapa_cotacao.cod_mapa)
                          INNER JOIN licitacao.adjudicacao
                                  ON adjudicacao.exercicio_cotacao   = mapa_cotacao.exercicio_cotacao
                                 AND adjudicacao.cod_cotacao         = mapa_cotacao.cod_cotacao
                                 AND adjudicacao.cod_licitacao       = ll.cod_licitacao
                                 AND adjudicacao.cod_modalidade      = ll.cod_modalidade
                                 AND adjudicacao.cod_entidade        = ll.cod_entidade
                                 AND adjudicacao.exercicio_licitacao = ll.exercicio
                           LEFT JOIN licitacao.adjudicacao_anulada
                                  ON adjudicacao.num_adjudicacao    = adjudicacao_anulada.num_adjudicacao
                                 AND adjudicacao.cod_entidade       = adjudicacao_anulada.cod_entidade
                                 AND adjudicacao.cod_modalidade     = adjudicacao_anulada.cod_modalidade
                                 AND adjudicacao.cod_licitacao      = adjudicacao_anulada.cod_licitacao
                                 AND adjudicacao.exercicio_licitacao= adjudicacao_anulada.exercicio_licitacao
                                 AND adjudicacao.cod_item           = adjudicacao_anulada.cod_item
                                 AND adjudicacao.cod_cotacao        = adjudicacao_anulada.cod_cotacao
                                 AND adjudicacao.lote               = adjudicacao_anulada.lote
                                 AND adjudicacao.exercicio_cotacao  = adjudicacao_anulada.exercicio_cotacao
                                 AND adjudicacao.cgm_fornecedor     = adjudicacao_anulada.cgm_fornecedor
                               WHERE adjudicacao_anulada.num_adjudicacao IS NULL
                                 AND adjudicacao.adjudicado
                             ) ";
                }
            }
    
            $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao, $stFiltro );
    
            if ( $rsLicitacao->getNumLinhas() > 0 ) {
                $stJs.= preencheLicitacao( $rsLicitacao, 'inCodLicitacao' );
            } else {
                $stJs.= "f.inCodLicitacao.selectedIndex =  0;\n";
                $stJs.= "limpaSelect(f.inCodLicitacao,1);\n";
                $stJs.= "d.getElementById('stProcesso').innerHTML = '&nbsp;';\n";
            }
            
        } else {
                $stJs = "f.inCodLicitacao.value = '';\n";
                $stJs.= "limpaSelect(f.inCodLicitacao,1);\n";
                $stJs.= "d.getElementById('stProcesso').innerHTML = '&nbsp;';\n";
        }
    break;

    case 'carregaProcesso':
        if ($_REQUEST['boSetarValorLicitacao'])
            echo "d.getElementById('valorLicitacao').innerHTML = '&nbsp;'; ";
    
        $stJs .= carregaProcesso( $_REQUEST['inCodLicitacao'] );
    break;

}
echo $stJs;

?>

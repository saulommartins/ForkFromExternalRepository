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
* Arquivo instância para popup de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Id: OCProcurarCgm.php 65128 2016-04-26 20:07:17Z evandro $
$Revision: 26876 $
$Name$
$Author: hboaventura $
$Date: 2007-11-22 14:32:33 -0200 (Qui, 22 Nov 2007) $

Casos de uso: uc-01.02.92
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"               );
include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"             );
include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php"             );

function buscaPopup()
{
    global $request;
    $stJs = empty($stJs) ? "" : $stJs;
    $stNomCGM = null;
    /* caso seja busca por vinculo */
    if ($_GET[$_GET['stNomCampoCod']]) {
        if ( $request->get('stTabelaVinculo') AND $_GET[$_GET['stNomCampoCod']] != '' ) {
            $obTCGM = new TCGM;
            $stFiltro = " and CGM.numcgm = " . $_GET[$_GET['stNomCampoCod']] ;

            $arCampo = Sessao::read($_GET['stIdCampoDesc']);
            if ($arCampo['FLIPopUpCGMVinculado'] != '') {
                $stFiltro .= $arCampo['FLIPopUpCGMVinculado'];
            }

            $stFiltroVinculado = $arCampo['stFiltroVinculado'];

            if ( $request->get('stTabelaVinculo') == 'patrimonio.bem_responsavel') {
                $stFiltroVinculado .= " AND tabela_vinculo.dt_fim IS NULL ";
            }

            if ($_REQUEST['buscaContrato']) {
                $stFiltro .= retornaFiltroBuscaContratos();
            }

            $obTCGM->recuperaRelacionamentoVinculado( $rsLista, $stFiltro, " ORDER BY CGM.nom_cgm", "" , $_REQUEST['stTabelaVinculo'] , $_REQUEST['stCampoVinculo'],$stFiltroVinculado);

            $stNomCGM = $rsLista->getCampo('nom_cgm');
            if ( !$stNomCGM ){
                if ($request->get('stTipo') == 'usuario') {
                    $stJs .= "alertaAviso('@" . $_REQUEST['stNomeVinculo']  . " Usuário com CGM ". $_GET[ $_GET['stNomCampoCod'] ] ." não foi encontrado no cadastro de Usuários!', 'form','erro','".Sessao::getId()."');";    
                }else{
                    $stJs .= "alertaAviso('@" . $_REQUEST['stNomeVinculo']  . "  ( CGM: ". $_GET[ $_GET['stNomCampoCod'] ] ." ) não encontrado ou inativo!', 'form','erro','".Sessao::getId()."');";
                }
            }
        } else {
            if ($_GET[ $_GET['stNomCampoCod'] ] != "") {
                if ($_REQUEST["stTipoBusca"]=="fisica") {
                    $obRegra = new RCGMPessoaFisica();
                } elseif ($_REQUEST["stTipoBusca"]=="juridica") {
                    $obRegra = new RCGMPessoaJuridica();
                } else {
                    $obRegra = new RCGM();
                }

                $obRegra->setNumCGM( $_GET[$_GET['stNomCampoCod']] );
                $obRegra->consultarCGM($rsCGM);
                $stNomCGM = addslashes($obRegra->getNomCGM());
            }
            if ($stNomCGM == '' and $_REQUEST["stTipoBusca"] and $_GET[ $_GET['stNomCampoCod'] ]!="") {
                $stJs .= "alertaAviso('@Número do CGM (". $_GET[ $_GET['stNomCampoCod'] ] .") não encontrado no cadastro de Pessoa ".$_REQUEST["stTipoBusca"]."', 'form','erro','".Sessao::getId()."');";
            }
        }
    }
    $stNomCGM = stripslashes($stNomCGM);
    $stJs .= "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_REQUEST['stIdCampoDesc']."', 'frm', '".str_replace("'", "\'", $stNomCGM)."');";

    return $stJs;
}

function retornaFiltroBuscaContratos()
{
    if ($_REQUEST['buscaContrato'] == 'compraDireta') {
        $stFiltro .= " AND EXISTS( SELECT 1
                                     FROM licitacao.contrato
                                        , licitacao.contrato_compra_direta
                                    WHERE contrato.cgm_contratado = CGM.numcgm
                                      AND contrato.num_contrato = contrato_compra_direta.num_contrato
                                      AND contrato.cod_entidade = contrato_compra_direta.cod_entidade
                                      AND contrato.exercicio = contrato_compra_direta.exercicio
                                      AND NOT EXISTS ( SELECT 1
                                                         FROM licitacao.contrato_anulado
                                                        WHERE contrato.num_contrato = contrato_anulado.num_contrato
                                                          AND contrato.cod_entidade = contrato_anulado.cod_entidade
                                                          AND contrato.exercicio = contrato_anulado.exercicio
                                                     )
                                      AND NOT EXISTS ( SELECT 1
                                                         FROM licitacao.rescisao_contrato
                                                        WHERE contrato.num_contrato = rescisao_contrato.num_contrato
                                                          AND contrato.cod_entidade = rescisao_contrato.cod_entidade
                                                          AND contrato.exercicio = rescisao_contrato.exercicio
                                                     )
                                )";
    }

    if ($_REQUEST['buscaContrato'] == 'licitacao') {
            $stFiltro .= " AND EXISTS( SELECT 1
                                         FROM licitacao.contrato
                                            , licitacao.contrato_licitacao
                                        WHERE contrato.cgm_contratado = CGM.numcgm
                                          AND contrato.num_contrato = contrato_licitacao.num_contrato
                                          AND contrato.cod_entidade = contrato_licitacao.cod_entidade
                                          AND contrato.exercicio = contrato_licitacao.exercicio
                                          AND NOT EXISTS ( SELECT 1
                                                             FROM licitacao.contrato_anulado
                                                            WHERE contrato.num_contrato = contrato_anulado.num_contrato
                                                              AND contrato.cod_entidade = contrato_anulado.cod_entidade
                                                              AND contrato.exercicio = contrato_anulado.exercicio
                                                         )
                                          AND NOT EXISTS ( SELECT 1
                                                             FROM licitacao.rescisao_contrato
                                                            WHERE contrato.num_contrato = rescisao_contrato.num_contrato
                                                              AND contrato.cod_entidade = rescisao_contrato.cod_entidade
                                                              AND contrato.exercicio = rescisao_contrato.exercicio
                                                         )
                                    )";
    }

    return $stFiltro;
}

switch ($_GET['stCtrl']) {
    case 'buscaPopup':
       $stJs = buscaPopup();
    break;
}

if ($stJs) echo $stJs;

?>

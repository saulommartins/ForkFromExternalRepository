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
    * Arquivo de Processamento do Formulario
    * Data de Criação   : 05/02/2014

    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: PRManterNotasFiscais.php 62431 2015-05-07 20:45:28Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscal.class.php"                    );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscalEmpenhoLiquidacao.class.php"   );

$stPrograma = "ManterNotasFiscais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTTCEMGNotaFiscal = new TTCEMGNotaFiscal;

$arLiquidacoes = Sessao::read('arLiquidacoes');

$count = count($arLiquidacoes['arLiquidacoes']);
for ($i=0;$i<$count; $i++) {
    $arLiquidacoes[$i]['valor_liquidacao']=str_replace('.','',$arLiquidacoes[$i]['valor_liquidacao']);
    $arLiquidacoes[$i]['valor_liquidacao']=$vlLiquidacao=str_replace(',','.',$arLiquidacoes[$i]['valor_liquidacao']);
    $arLiquidacoes[$i]['nuVlAssociado']=str_replace('.','',$arLiquidacoes[$i]['nuVlAssociado']);
    $arLiquidacoes[$i]['nuVlAssociado']=$vlLiquidacao=str_replace(',','.',$arLiquidacoes[$i]['nuVlAssociado']);

}

switch ($_REQUEST['stAcao']) {
    case 'incluir' :

        Sessao::setTrataExcecao ( true );

        $obErro = new Erro;
        
        if (count($arLiquidacoes) > 0 ) {
            $obTTCEMGNotaFiscal->setDado( 'exercicio'                   , $_REQUEST['stExercicio']       		);
            $obTTCEMGNotaFiscal->proximoCod($inCodNota);

            $obTTCEMGNotaFiscal->setDado( 'cod_nota'                    , $inCodNota                            );
            $obTTCEMGNotaFiscal->setDado( 'cod_entidade'                , $_REQUEST['inCodEntidade']            );
            $obTTCEMGNotaFiscal->setDado( 'data_emissao'                , $_REQUEST['dtEmissao']                );
            $obTTCEMGNotaFiscal->setDado( 'cod_tipo'                    , $_REQUEST['inCodTipoNota']            );
            if ($_REQUEST['inNumeroNF'] != '') {
                $obTTCEMGNotaFiscal->setDado('nro_nota'                 , $_REQUEST['inNumeroNF']               );
            }
            if ($_REQUEST['inNumSerie'] != '') {
                $obTTCEMGNotaFiscal->setDado('nro_serie'                , $_REQUEST['inNumSerie']               );
            }
            if ($_REQUEST['stAIFD'] != '') {
                $obTTCEMGNotaFiscal->setDado('aidf'                     , $_REQUEST['stAIFD']                   );
            }
            if ($_REQUEST['inNumInscricaoMunicipal'] != '') {
                $obTTCEMGNotaFiscal->setDado('inscricao_municipal'      , $_REQUEST['inNumInscricaoMunicipal']  );
            }
            if ($_REQUEST['inNumInscricaoEstadual'] != '') {
                $obTTCEMGNotaFiscal->setDado('inscricao_estadual'       , $_REQUEST['inNumInscricaoEstadual']   );
            }
            if ($_REQUEST['inChave']) {
                $obTTCEMGNotaFiscal->setDado ( 'chave_acesso'           , $_REQUEST['inChave']                  );
            }
            if ($_REQUEST['inChaveMunicipal']) {
                $obTTCEMGNotaFiscal->setDado ( 'chave_acesso_municipal' , $_REQUEST['inChaveMunicipal']         );
            }
            
            $nuVlTotalDoctoFiscal = str_replace('.', '' , $_REQUEST['nuTotalNf']);
            $nuVlTotalDoctoFiscal = str_replace(',', '.', $nuVlTotalDoctoFiscal);

            $nuVlDescontoDoctoFiscal = str_replace('.', '' , $_REQUEST['nuVlDesconto']);
            $nuVlDescontoDoctoFiscal = str_replace(',', '.', $nuVlDescontoDoctoFiscal);

            $obTTCEMGNotaFiscal->setDado( 'vl_total'        , (float)$nuVlTotalDoctoFiscal);
            $obTTCEMGNotaFiscal->setDado( 'vl_desconto'     , (float)$nuVlDescontoDoctoFiscal);
            $obTTCEMGNotaFiscal->setDado( 'vl_total_liquido', (float)$nuVlTotalDoctoFiscal - (float)$nuVlDescontoDoctoFiscal );

            $obErro = $obTTCEMGNotaFiscal->inclusao();

            if (!$obErro->ocorreu()) {
                $obTTCEMGNotaFiscalEmpenho = new TTCEMGNotaFiscalEmpenhoLiquidacao;

                $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_nota'           , $inCodNota                      );
                $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio'          , $_REQUEST['stExercicio']        );
                $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_entidade'       , $_REQUEST['inCodEntidade']      );

                $rsRecordSet = new RecordSet;
                $rsRecordSet->preenche($arLiquidacoes);

                while ( !$rsRecordSet->eof() and !$obErro->ocorreu() ) {
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_empenho'          , $rsRecordSet->getCampo('cod_empenho')         );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio_empenho'    , $rsRecordSet->getCampo('exercicio')           );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_nota_liquidacao'  , $rsRecordSet->getCampo('cod_nota_liquidacao') );
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio_liquidacao' , $rsRecordSet->getCampo('exercicio_liquidacao'));
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'vl_associado'         , $rsRecordSet->getCampo('nuVlAssociado'));
                    $obTTCEMGNotaFiscalEmpenho->setDado( 'vl_liquidacao'        , $rsRecordSet->getCampo('valor_liquidacao')    );

                    $obErro = $obTTCEMGNotaFiscalEmpenho->inclusao();
                    $rsRecordSet->proximo();
                }
            }

            if ($obErro->ocorreu()) {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            } else {
                $stLink = "?stAcao=".$_REQUEST['stAcao'].Sessao::read('filtroAux');
                Sessao::remove('arLiquidacoes');
                Sessao::remove('arEmpenhos');
                sistemaLegado::alertaAviso($pgForm.$stLink,"Incluir Notas Fiscais concluído com sucesso!","aviso", Sessao::getId(), "../");
            }
            
        } else {
            sistemaLegado::exibeAviso( 'Informe o(s) empenho(s).',"n_incluir","erro" );
        }

        Sessao::encerraExcecao();

    break;

    case "alterar":

        Sessao::setTrataExcecao ( true );

        $obErro = new Erro;
        
        if (count($arLiquidacoes) > 0 ) {
            $obTTCEMGNotaFiscal->setDado( 'cod_nota'                    , $_REQUEST['inCodNota']                );
            $obTTCEMGNotaFiscal->setDado( 'exercicio'                   , $_REQUEST['stExercicio']              );
            $obTTCEMGNotaFiscal->setDado( 'cod_entidade'                , $_REQUEST['inCodEntidade']            );
            $obTTCEMGNotaFiscal->setDado( 'data_emissao'                , $_REQUEST['dtEmissao']                );
            $obTTCEMGNotaFiscal->setDado( 'cod_tipo'                    , $_REQUEST['inCodTipoNota']            );
            if ($_REQUEST['inNumeroNF'] != '') {
                $obTTCEMGNotaFiscal->setDado('nro_nota'                 , $_REQUEST['inNumeroNF']               );
            }
            if ($_REQUEST['inNumSerie'] != '') {
                $obTTCEMGNotaFiscal->setDado('nro_serie'                , $_REQUEST['inNumSerie']               );
            }
            if ($_REQUEST['stAIFD'] != '') {
                $obTTCEMGNotaFiscal->setDado('aidf'                     , $_REQUEST['stAIFD']                   );
            }
            if ($_REQUEST['inNumInscricaoMunicipal'] != '') {
                $obTTCEMGNotaFiscal->setDado('inscricao_municipal'      , $_REQUEST['inNumInscricaoMunicipal']  );
            }
            if ($_REQUEST['inNumInscricaoEstadual'] != '') {
                $obTTCEMGNotaFiscal->setDado('inscricao_estadual'       , $_REQUEST['inNumInscricaoEstadual']   );
            }
            if ($_REQUEST['inChave']) {
                $obTTCEMGNotaFiscal->setDado ( 'chave_acesso'           , $_REQUEST['inChave']                  );
            }
            if ($_REQUEST['inChaveMunicipal']) {
                $obTTCEMGNotaFiscal->setDado ( 'chave_acesso_municipal' , $_REQUEST['inChaveMunicipal']         );
            }
        
            $nuVlTotalDoctoFiscal = str_replace('.', '' , $_REQUEST['nuTotalNf']);
            $nuVlTotalDoctoFiscal = str_replace(',', '.', $nuVlTotalDoctoFiscal);

            $nuVlDescontoDoctoFiscal = str_replace('.', '' , $_REQUEST['nuVlDesconto']);
            $nuVlDescontoDoctoFiscal = str_replace(',', '.', $nuVlDescontoDoctoFiscal);

            $obTTCEMGNotaFiscal->setDado( 'vl_total'        , $nuVlTotalDoctoFiscal);
            $obTTCEMGNotaFiscal->setDado( 'vl_desconto'     , $nuVlDescontoDoctoFiscal);
            $obTTCEMGNotaFiscal->setDado( 'vl_total_liquido', (float)$nuVlTotalDoctoFiscal - (float)$nuVlDescontoDoctoFiscal );

            $obErro = $obTTCEMGNotaFiscal->alteracao();
            
            if ( !$obErro->ocorreu() ) {
                $obTTCEMGNotaFiscalEmpenho = new TTCEMGNotaFiscalEmpenhoLiquidacao;

                $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_nota'           , $_REQUEST['inCodNota']          );
                $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio'          , $_REQUEST['stExercicio'] 		);
                $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_entidade'       , $_REQUEST['inCodEntidade']      );

                $obErro = $obTTCEMGNotaFiscalEmpenho->exclusao();

                if ( !$obErro->ocorreu() ) {
                    $rsRecordSet = new RecordSet;
                    $rsRecordSet->preenche($arLiquidacoes);

                    while ( !$rsRecordSet->eof() and !$obErro->ocorreu() ) {
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_empenho'          , $rsRecordSet->getCampo('cod_empenho')         );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio_empenho'    , $rsRecordSet->getCampo('exercicio')           );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_nota_liquidacao'  , $rsRecordSet->getCampo('cod_nota_liquidacao') );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio_liquidacao' , $rsRecordSet->getCampo('exercicio_liquidacao'));
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'vl_liquidacao'        , $rsRecordSet->getCampo('valor_liquidacao')    );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'vl_associado'         , $rsRecordSet->getCampo('nuVlAssociado'));

                        $obErro = $obTTCEMGNotaFiscalEmpenho->inclusao();
                        $rsRecordSet->proximo();
                    }
                }
            }

            if ( $obErro->ocorreu() ) {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            } else {
                $stLink = "?stAcao=".$_REQUEST['stAcao'].Sessao::read('filtroAux');
                SistemaLegado::alertaAviso($pgList.$stLink, "Alterar Notas Fiscais concluído com sucesso!","aviso", Sessao::getId(), "../");
                Sessao::remove('arLiquidacoes');
                Sessao::remove('arEmpenhos');
            }
            
        } else {
            sistemaLegado::exibeAviso( 'Informe o(s) empenho(s).',"n_incluir","erro" );
        }

        Sessao::encerraExcecao();

    break;

    case "excluir":

       Sessao::setTrataExcecao ( true );

       $obErro = new Erro;

        $obTTCEMGNotaFiscalEmpenho = new TTCEMGNotaFiscalEmpenhoLiquidacao;
        $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_nota'           , $_REQUEST['inCodNota']      );
        $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio'          , $_REQUEST['stExercicio']    );
        $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_entidade'       , $_REQUEST['cod_entidade']   );
        $obErro = $obTTCEMGNotaFiscalEmpenho->exclusao();

        if ( !$obErro->ocorreu() ) {
            $obTTCEMGNotaFiscal->setDado( 'cod_nota'           , $_REQUEST['inCodNota']     );
            $obTTCEMGNotaFiscal->setDado( 'exercicio'          , $_REQUEST['stExercicio']   );
            $obTTCEMGNotaFiscal->setDado( 'cod_entidade'       , $_REQUEST['cod_entidade']  );
            $obErro = $obTTCEMGNotaFiscal->exclusao();
        }

        if ( $obErro->ocorreu() ) {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        } else {
            $stLink = "?stAcao=".$_REQUEST['stAcao'].Sessao::read('filtroAux');

            SistemaLegado::alertaAviso($pgList.$stLink,"Excluir Notas Fiscais concluído com sucesso!","aviso", Sessao::getId(), "../");
        }

       Sessao::encerraExcecao();

    break;

}

?>

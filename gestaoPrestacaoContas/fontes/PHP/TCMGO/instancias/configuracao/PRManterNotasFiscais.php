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
    * Data de Criação: 23/09/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscal.class.php");

$stPrograma = "ManterNotasFiscais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTTCMGONotaFiscal = new TTCMGONotaFiscal;

switch ($_REQUEST['stAcao']) {
    case 'incluir' :

        Sessao::setTrataExcecao ( true );

        $obErro = new Erro;
        $arEmpenhos = array();
        $arEmpenhos = Sessao::read('arEmpenhos');

        if (count($arEmpenhos) > 0 ) {
            $stFiltro  = " WHERE nro_nota           = ".$_REQUEST['inNumNota'];

                if ($_REQUEST['nuVlTotal'] == $_REQUEST['nuVlNotaFiscal']) {
                    $obTTCMGONotaFiscal->proximoCod($inCodNota);
                    $obTTCMGONotaFiscal->setDado( 'cod_nota'            , $inCodNota                           );
                    $obTTCMGONotaFiscal->setDado( 'nro_serie'           , $_REQUEST['inNumSerie']              );
                    $obTTCMGONotaFiscal->setDado( 'aidf'                , $_REQUEST['stAIFD']                  );
                    $obTTCMGONotaFiscal->setDado( 'vl_nota'             , $_REQUEST['nuVlNotaFiscal']          );
                    $obTTCMGONotaFiscal->setDado( 'inscricao_municipal' , $_REQUEST['inNumInscricaoMunicipal'] );
                    $obTTCMGONotaFiscal->setDado( 'inscricao_estadual'  , $_REQUEST['inNumInscricaoEstadual']  );
                    $obTTCMGONotaFiscal->setDado ( 'cod_tipo'           , $_REQUEST['inCodTipoNota']           );
                    if ($_REQUEST['inChave']) {
                        $obTTCMGONotaFiscal->setDado ( 'chave_acesso'       , $_REQUEST['inChave']             );
                    } else {
                        $obTTCMGONotaFiscal->setDado ( 'chave_acesso'       , ""                               );
                    }
                    if ($_REQUEST['data_emissao'] != '') {
                        $obTTCMGONotaFiscal->setDado('data_emissao', $_REQUEST['data_emissao']);
                    } else {
                        $obTTCMGONotaFiscal->setDado('data_emissao', $_REQUEST['dtEmissao']);
                    }
                    if ($_REQUEST['inNumNota'] != '') {
                        $obTTCMGONotaFiscal->setDado('nro_nota', $_REQUEST['inNumNota']);
                    }
                    $obErro = $obTTCMGONotaFiscal->inclusao();

                    if (!$obErro->ocorreu()) {

                        if (Sessao::getExercicio() > 2010) {
                            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscalEmpenhoLiquidacao.class.php" );
                            $obTTCMGONotaFiscalEmpenho = new TTCMGONotaFiscalEmpenhoLiquidacao;
                        } else {
                            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscalEmpenho.class.php" );
                            $obTTCMGONotaFiscalEmpenho = new TTCMGONotaFiscalEmpenho;
                        }
                        $obTTCMGONotaFiscalEmpenho->setDado( 'cod_nota'           , $inCodNota                     );
                        $obTTCMGONotaFiscalEmpenho->setDado( 'exercicio'          , Sessao::getExercicio()         );
                        $obTTCMGONotaFiscalEmpenho->setDado( 'cod_entidade'       , $_REQUEST['inCodEntidade']     );

                        $rsRecordSet = new RecordSet;
                        $rsRecordSet->preenche(Sessao::read('arEmpenhos'));

                        while ( !$rsRecordSet->eof() and !$obErro->ocorreu() ) {
                            $obTTCMGONotaFiscalEmpenho->setDado( 'cod_empenho'  , $rsRecordSet->getCampo('cod_empenho')   );
                            $obTTCMGONotaFiscalEmpenho->setDado( 'vl_associado' , $rsRecordSet->getCampo('nuVlAssociado') );
                            if (Sessao::getExercicio() > 2010) {
                                $obTTCMGONotaFiscalEmpenho->setDado('cod_nota_liquidacao' , $rsRecordSet->getCampo('cod_nota_liquidacao'));
                                $obTTCMGONotaFiscalEmpenho->setDado('exercicio_liquidacao', $rsRecordSet->getCampo('exercicio_liquidacao'));
                            }
                            $obErro = $obTTCMGONotaFiscalEmpenho->inclusao();
                            $rsRecordSet->proximo();
                        }
                    }

                    if ($obErro->ocorreu()) {
                        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                    } else {
                        $stLink = "?stAcao=".$_REQUEST['stAcao'].Sessao::read('filtroAux');
                        Sessao::remove('arEmpenhos');
                        sistemaLegado::alertaAviso($pgForm.$stLink, $inCodNota .'/'. Sessao::getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
                    }

                } else {
                    sistemaLegado::exibeAviso( 'Valor da nota fiscal deve ser igual ao total vinculado.',"n_incluir","erro" );
                }

        } else {
            sistemaLegado::exibeAviso( 'Informe o(s) empenho(s).',"n_incluir","erro" );
        }

        Sessao::encerraExcecao();

    break;

    case "alterar":

        Sessao::setTrataExcecao ( true );
        $arEmpenhos = Sessao::read('arEmpenhos');

        $obErro = new Erro;

        if (count($arEmpenhos) > 0 ) {

            if ($_REQUEST['nuVlTotal'] == $_REQUEST['nuVlNotaFiscal']) {
                $obTTCMGONotaFiscal->setDado( 'cod_nota'            , $_REQUEST['inCodNota']               );
                $obTTCMGONotaFiscal->setDado( 'nro_serie'           , $_REQUEST['inNumSerie']              );
                $obTTCMGONotaFiscal->setDado( 'aidf'                , $_REQUEST['stAIFD']                  );
                $obTTCMGONotaFiscal->setDado( 'vl_nota'             , $_REQUEST['nuVlNotaFiscal']          );
                $obTTCMGONotaFiscal->setDado( 'inscricao_municipal' , $_REQUEST['inNumInscricaoMunicipal'] );
                $obTTCMGONotaFiscal->setDado( 'inscricao_estadual'  , $_REQUEST['inNumInscricaoEstadual']  );
                $obTTCMGONotaFiscal->setDado( 'cod_tipo'            , $_REQUEST['inCodTipoNota']           );
                if ($_REQUEST['inChave']) {
                        $obTTCMGONotaFiscal->setDado ( 'chave_acesso'       , $_REQUEST['inChave']         );
                    } else {
                        $obTTCMGONotaFiscal->setDado ( 'chave_acesso'       , ""                           );
                    }
                if ($_REQUEST['data_emissao'] != '') {
                    $obTTCMGONotaFiscal->setDado('data_emissao', $_REQUEST['data_emissao']);
                } else {
                    $obTTCMGONotaFiscal->setDado('data_emissao', $_REQUEST['dtEmissao']);
                }
                if ($_REQUEST['inNumNota'] != '') {
                    $obTTCMGONotaFiscal->setDado('nro_nota', $_REQUEST['inNumNota']);
                }
                $obErro = $obTTCMGONotaFiscal->alteracao();

                if ( !$obErro->ocorreu() ) {
                    if (Sessao::getExercicio() > 2010) {
                        include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscalEmpenhoLiquidacao.class.php" );
                        $obTTCMGONotaFiscalEmpenho = new TTCMGONotaFiscalEmpenhoLiquidacao;
                    } else {
                        include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscalEmpenho.class.php" );
                        $obTTCMGONotaFiscalEmpenho = new TTCMGONotaFiscalEmpenho;
                    }
                    $obTTCMGONotaFiscalEmpenho->setDado( 'cod_nota' , $_REQUEST['inCodNota']              );
                    $obErro = $obTTCMGONotaFiscalEmpenho->exclusao();

                    if ( !$obErro->ocorreu() ) {
                        foreach ($arEmpenhos as $registro) {
                            $obTTCMGONotaFiscalEmpenho->setDado( 'cod_nota'     , $_REQUEST['inCodNota']     );
                            $obTTCMGONotaFiscalEmpenho->setDado( 'exercicio'    , $registro['exercicio']     );
                            $obTTCMGONotaFiscalEmpenho->setDado( 'cod_entidade' , $registro['cod_entidade']  );
                            $obTTCMGONotaFiscalEmpenho->setDado( 'cod_empenho'  , $registro['cod_empenho']   );
                            $obTTCMGONotaFiscalEmpenho->setDado( 'vl_associado' , $registro['nuVlAssociado'] );
                            if (Sessao::getExercicio() > 2010) {
                                $obTTCMGONotaFiscalEmpenho->setDado('cod_nota_liquidacao' , $registro['cod_nota_liquidacao']);
                                $obTTCMGONotaFiscalEmpenho->setDado('exercicio_liquidacao', $registro['exercicio_liquidacao']);
                            }

                            $obErro = $obTTCMGONotaFiscalEmpenho->inclusao();
                        }
                    }
                }

                if ( $obErro->ocorreu() ) {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                } else {
                    $stLink = "?stAcao=".$_REQUEST['stAcao'].Sessao::read('filtroAux');
                    SistemaLegado::alertaAviso($pgList.$stLink, $_REQUEST['inCodNota'].'/'. Sessao::getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
                    Sessao::remove('arEmpenhos');
                }

            } else {
                sistemaLegado::exibeAviso( 'Valor da nota fiscal deve ser igual ao total vinculado.',"n_incluir","erro" );
            }

        } else {
            sistemaLegado::exibeAviso( 'Informe o(s) empenho(s).',"n_incluir","erro" );
        }

        Sessao::encerraExcecao();

    break;

    case "excluir":

       Sessao::setTrataExcecao ( true );

       $obErro = new Erro;

       if (Sessao::getExercicio() > 2010) {
           include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscalEmpenhoLiquidacao.class.php" );
           $obTTCMGONotaFiscalEmpenho = new TTCMGONotaFiscalEmpenhoLiquidacao;
           $obTTCMGONotaFiscalEmpenho->setDado('cod_nota' , $_REQUEST['inCodNota']);
           $obErro = $obTTCMGONotaFiscalEmpenho->exclusao();
       }

       include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscalEmpenho.class.php" );
       $obTTCMGONotaFiscalEmpenho = new TTCMGONotaFiscalEmpenho;

       $obTTCMGONotaFiscalEmpenho->setDado('cod_nota' , $_REQUEST['inCodNota']);
       $obErro = $obTTCMGONotaFiscalEmpenho->exclusao();

       if ( !$obErro->ocorreu() ) {
           include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscal.class.php" );
           $obTTCMGONotaFiscal = new TTCMGONotaFiscal;
           $obTTCMGONotaFiscal->setDado('cod_nota' , $_REQUEST['inCodNota'] );
           $obErro = $obTTCMGONotaFiscal->exclusao();
       }

       if ( $obErro->ocorreu() ) {
           SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
       } else {
            $stLink = "?stAcao=".$_REQUEST['stAcao'].Sessao::read('filtroAux');
            SistemaLegado::alertaAviso($pgList.$stLink,"Nota Excluida","aviso", Sessao::getId(), "../");
       }

       Sessao::encerraExcecao();

    break;

}

?>

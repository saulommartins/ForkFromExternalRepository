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
    * Data de Criação   : 01/09/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

switch ($_REQUEST['stAcao']) {
    case 'incluir' :

        Sessao::setTrataExcecao ( true );

        $obErro = new Erro();
        if (($_REQUEST['stTermoAditivo'] == '') and (Sessao::getExercicio() > 2010) and ($_REQUEST['cod_tipo'] == 2 ) ) {
            $obErro->setDescricao('Informe o número do Termo Aditivo');
        }

        if (count(Sessao::read('arEmpenhos')) <= 0) {
            $obErro->setDescricao('Nenhum empenho incluso na lista!');
        }

        if ( !$obErro->ocorreu() ) {

            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContrato.class.php" );
            $obTTCMGOContrato = new TTCMGOContrato;
            $stFiltro  = " WHERE nro_contrato   = ".$_REQUEST['inNumContrato'];
            $stFiltro .= "   AND exercicio      = '".Sessao::getExercicio()."'";
            $obTTCMGOContrato->recuperaTodos($rsRecordSet, $stFiltro);

            if ( $rsRecordSet->getNumLinhas() < 0 ) {

                $obTTCMGOContrato->recuperaProximoContrato($rsRecordSet);
                $inCodContrato = $rsRecordSet->getCampo('cod_contrato');
                if(!$inCodContrato)
                    $inCodContrato = 0;

                $obTTCMGOContrato->setDado('cod_contrato'     , $inCodContrato                           );
                $obTTCMGOContrato->setDado('cod_assunto'      , $_REQUEST['cod_assunto']                 );
                $obTTCMGOContrato->setDado('nro_contrato'     , $_REQUEST['inNumContrato']               );
                $obTTCMGOContrato->setDado('cod_modalidade'   , $_REQUEST['cod_modalidade']              );
                $obTTCMGOContrato->setDado('cod_tipo'         , $_REQUEST['cod_tipo']                    );
                //$obTTCMGOContrato->setDado('numero_termo'     , $_REQUEST['stTermoAditivo']              );
                if ((Sessao::getExercicio() > 2010) and ($_REQUEST['cod_tipo'] == 2 )) {
                    $obTTCMGOContrato->setDado('numero_termo'     , $_REQUEST['stTermoAditivo']          );
                } else {
                    $obTTCMGOContrato->setDado('numero_termo'     , '\'\''                                 );
                }
                $obTTCMGOContrato->setDado('exercicio'        , Sessao::getExercicio()                   );
                $obTTCMGOContrato->setDado('cod_entidade'     , $_REQUEST['cod_entidade']                );
                $obTTCMGOContrato->setDado('vl_contrato'      , $_REQUEST['nuVlContrato']                );
                $obTTCMGOContrato->setDado('objeto_contrato'  , addslashes( $_REQUEST['stObjContrato'])  );
                $obTTCMGOContrato->setDado('data_inicio'      , $_REQUEST['dtInicial']                   );
                $obTTCMGOContrato->setDado('data_final'       , $_REQUEST['dtFinal']                     );
                $obTTCMGOContrato->setDado('data_publicacao'  , $_REQUEST['dtPublicacao']                );
                $obTTCMGOContrato->setDado('nro_processo'     , $_REQUEST['inNumProcesso']               );
                $obTTCMGOContrato->setDado('ano_processo'     , $_REQUEST['stAnoProcesso']               );
                $obTTCMGOContrato->setDado('cod_sub_assunto'  , $_REQUEST['cod_sub_assunto']             );
                $obTTCMGOContrato->SetDado('detalhamentosubassunto', $_REQUEST['stDetSubAssunto']        );
                $obTTCMGOContrato->setDado('dt_firmatura'          , $_REQUEST['dtFirmatura']            );
                $obTTCMGOContrato->setDado('dt_lancamento'         , $_REQUEST['dtLancamento']            );
                $obTTCMGOContrato->setDado('vl_acrescimo'          , $_REQUEST['nuVlAcrescimo']           );
                $obTTCMGOContrato->setDado('vl_decrescimo'         , $_REQUEST['nuVlDecrescimo']          );
                $obTTCMGOContrato->setDado('vl_contratual'         , $_REQUEST['nuVlContratual']          );
                $obTTCMGOContrato->setDado('dt_rescisao'           , $_REQUEST['dtRescisao']              );
                $obTTCMGOContrato->setDado('vl_final_contrato'     , $_REQUEST['nuVlFinalContrato']       );
                $obTTCMGOContrato->setDado('prazo'                 , $_REQUEST['inPrazo']                   );
                $obTTCMGOContrato->inclusao();

                include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoEmpenho.class.php" );
                $obTTCMGOContratoEmpenho = new TTCMGOContratoEmpenho;
                $obTTCMGOContratoEmpenho->setDado( 'cod_contrato' , $inCodContrato                         );
                $obTTCMGOContratoEmpenho->setDado( 'exercicio'    , Sessao::getExercicio()                 );
                $obTTCMGOContratoEmpenho->setDado( 'cod_entidade' , $_REQUEST['cod_entidade']              );

                $rsRecordSet = new RecordSet;
                $rsRecordSet->preenche(Sessao::read('arEmpenhos'));

                while ( !$rsRecordSet->eof() ) {
                    $obTTCMGOContratoEmpenho->setDado( 'exercicio_empenho' , $rsRecordSet->getCampo('exercicio') );
                    $obTTCMGOContratoEmpenho->setDado( 'cod_empenho'       , $rsRecordSet->getCampo('cod_empenho')        );
                    $obTTCMGOContratoEmpenho->inclusao();
                    $rsRecordSet->proximo();
                }

                Sessao::remove('arEmpenhos');
            } else {
                $obErro->setDescricao('Número do contrato já existe para este exercício.');
            }
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inNumContrato'] .'/'. Sessao::getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"n_incluir","erro" );
        }
        Sessao::encerraExcecao();

    break;

    case "alterar":

        Sessao::setTrataExcecao ( true );
        $arEmpenhos = Sessao::read('arEmpenhos');

        $obErro = new Erro();
        if (($_REQUEST['stTermoAditivo'] == '') and (Sessao::getExercicio() > '2010')  and ($_REQUEST['cod_tipo'] == 2 )) {
            $obErro->setDescricao('Informe o número do Termo Aditivo');
        }

        if ( !$obErro->ocorreu() ) {
            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContrato.class.php" );
            $obTTCMGOContrato = new TTCMGOContrato;

            $stFiltro  = " WHERE exercicio     = '".Sessao::getExercicio()."'";
            $stFiltro .= "   AND cod_entidade  =  ".$_REQUEST['cod_entidade'];
            $stFiltro .= "   AND nro_contrato  =  ".$_REQUEST['inNumContrato'];
            $stFiltro .= "   AND cod_contrato <>  ".$_REQUEST['inCodContrato'];

            $obTTCMGOContrato->recuperaTodos($rsRecordSet, $stFiltro);
            if ($rsRecordSet->getNumLinhas() == -1) {
                $obTTCMGOContrato->setDado('cod_contrato'     , $_REQUEST['inCodContrato']              );
                $obTTCMGOContrato->setDado('cod_assunto'      , $_REQUEST['cod_assunto']                );
                $obTTCMGOContrato->setDado('nro_contrato'     , $_REQUEST['inNumContrato']              );
                $obTTCMGOContrato->setDado('cod_modalidade'   , $_REQUEST['cod_modalidade']             );
                $obTTCMGOContrato->setDado('cod_tipo'         , $_REQUEST['cod_tipo']                   );
                if ((Sessao::getExercicio() > 2010) and ($_REQUEST['cod_tipo'] == 2 )) {
                    $obTTCMGOContrato->setDado('numero_termo'     , $_REQUEST['stTermoAditivo']          );
                } else {
                    $obTTCMGOContrato->setDado('numero_termo'     , '\'\''                                 );
                }
                $obTTCMGOContrato->setDado('exercicio'        , $_REQUEST['stExercicioContrato']        );
                $obTTCMGOContrato->setDado('cod_entidade'     , $_REQUEST['cod_entidade']               );
                $obTTCMGOContrato->setDado('vl_contrato'      , $_REQUEST['nuVlContrato']               );
                $obTTCMGOContrato->setDado('objeto_contrato'  , addslashes($_REQUEST['stObjContrato'])  );
                $obTTCMGOContrato->setDado('data_inicio'      , $_REQUEST['dtInicial']                  );
                $obTTCMGOContrato->setDado('data_final'       , $_REQUEST['dtFinal']                    );
                $obTTCMGOContrato->setDado('data_publicacao'  , $_REQUEST['dtPublicacao']               );
                $obTTCMGOContrato->setDado('nro_processo'     , $_REQUEST['inNumProcesso']              );
                $obTTCMGOContrato->setDado('ano_processo'     , $_REQUEST['stAnoProcesso']              );
                $obTTCMGOContrato->setDado('cod_sub_assunto'  , $_REQUEST['subAssunto']                  );
                $obTTCMGOContrato->SetDado('detalhamentosubassunto', $_REQUEST['stDetSubAssunto']        );
                $obTTCMGOContrato->setDado('dt_firmatura'          , $_REQUEST['dtFirmatura']            );
                $obTTCMGOContrato->setDado('dt_lancamento'         , $_REQUEST['dtLancamento']            );
                $obTTCMGOContrato->setDado('vl_acrescimo'          , $_REQUEST['nuVlAcrescimo']           );
                $obTTCMGOContrato->setDado('vl_decrescimo'         , $_REQUEST['nuVlDecrescimo']          );
                $obTTCMGOContrato->setDado('vl_contratual'         , $_REQUEST['nuVlContratual']          );
                $obTTCMGOContrato->setDado('dt_rescisao'           , $_REQUEST['dtRescisao']              );
                $obTTCMGOContrato->setDado('vl_final_contrato'     , $_REQUEST['nuVlFinalContrato']       );
                $obTTCMGOContrato->setDado('prazo'                 , $_REQUEST['inPrazo']                   );
                $obErro = $obTTCMGOContrato->alteracao();
                $obTTCMGOContrato->debug();

                if ( !$obErro->ocorreu() ) {
                    include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoEmpenho.class.php" );
                    $obTTCMGOContratoEmpenho = new TTCMGOContratoEmpenho;
                    $obTTCMGOContratoEmpenho->setDado( 'cod_contrato' , $_REQUEST['inCodContrato']              );
                    $obTTCMGOContratoEmpenho->setDado( 'exercicio'    , $_REQUEST['stExercicioContrato']        );
                    $obTTCMGOContratoEmpenho->setDado( 'cod_entidade' , $_REQUEST['cod_entidade']               );
                    $obErro = $obTTCMGOContratoEmpenho->exclusao();

                    if ( !$obErro->ocorreu() ) {
                        $obTTCMGOContratoEmpenho->setDado( 'cod_contrato'     , $_REQUEST['inCodContrato']             );
                        $obTTCMGOContratoEmpenho->setDado( 'exercicio'        , $_REQUEST['stExercicioContrato']       );
                        $obTTCMGOContratoEmpenho->setDado( 'cod_entidade'     , $_REQUEST['cod_entidade']              );
                        foreach ($arEmpenhos as $registro) {
                            $obTTCMGOContratoEmpenho->setDado( 'exercicio_empenho', $registro['exercicio']                 );
                            $obTTCMGOContratoEmpenho->setDado( 'cod_empenho'      , $registro['cod_empenho']               );
                            $obTTCMGOContratoEmpenho->inclusao();
                        }
                    }
                }
            }

            if ( $obErro->ocorreu() ) {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            } else {
                sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inNumContrato'] .'/'. $_REQUEST['stExercicioContrato'] ,"incluir","aviso", Sessao::getId(), "../");
            }

        } else {
            $obErro = sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"n_incluir","erro" );
        }

        Sessao::remove('arEmpenhos');
        Sessao::encerraExcecao();

    break;

    case "excluir":

       Sessao::setTrataExcecao ( true );

       $obErro = new Erro;

       include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoEmpenho.class.php" );
       $obTTCMGOContratoEmpenho = new TTCMGOContratoEmpenho;
       $obTTCMGOContratoEmpenho->setDado('cod_contrato' , $_REQUEST['inCodContrato']);
       $obTTCMGOContratoEmpenho->setDado('exercicio'    , $_REQUEST['stExercicioContrato']);
       $obTTCMGOContratoEmpenho->setDado('cod_entidade' , $_REQUEST['inCodEntidade']);
       $obErro = $obTTCMGOContratoEmpenho->exclusao();

       if ( !$obErro->ocorreu() ) {
           include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContrato.class.php" );
           $obTTCMGOContrato = new TTCMGOContrato;
           $obTTCMGOContrato->setDado('cod_contrato' , $_REQUEST['inCodContrato']       );
           $obTTCMGOContrato->setDado('exercicio'    , $_REQUEST['stExercicioContrato'] );
           $obTTCMGOContrato->setDado('cod_entidade' , $_REQUEST['inCodEntidade']       );
           $obErro = $obTTCMGOContrato->exclusao();
       }

       if ( $obErro->ocorreu() ) {
           SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
       } else {
           sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']  ,"excluir","aviso", Sessao::getId(), "../");
       }

       Sessao::encerraExcecao();

    break;

}

?>

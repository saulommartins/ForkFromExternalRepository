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
    * Processamento de configuração de Obras e Serviços de Engenharia
    * Data de Criação   : 18/09/2015
    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: PRManterConfiguracaoObrasServicos.php 63809 2015-10-19 16:52:56Z lisiane $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObra.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObraAndamento.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObraFiscal.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObraMedicao.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObraContratos.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoObrasServicos";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgProx    = $pgFilt;

$stAcao = $request->get("stAcao");
$stLink = Sessao::read('stLink');

$obErro = new Erro();
$boFlagTransacao = false;
$obTransacao = new Transacao;
$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
$obTTCMBAObra = new TTCMBAObra();

list($inDiaInicio, $inMesInicio, $inAnoInicio) = explode("/",$request->get('dtInicio'));
list($inDiaPrazo , $inMesPrazo , $inAnoPrazo ) = explode("/",$request->get('dtPrazo')); 
$dtInicio = $inAnoInicio.'-'.$inMesInicio.'-'.$inDiaInicio;
$dtPrazo = $inAnoPrazo.'-'.$inMesPrazo.'-'.$inDiaPrazo;
$inPrazo = SistemaLegado::datediff('d', $dtInicio, $dtPrazo);

if($inPrazo<0){
    $obErro->setDescricao('O Prazo de conclusão da obra não pode ser inferior a Data de Início da obra.');
}

if(!$obErro->ocorreu()){
    switch ($stAcao) {
        case "incluir":
            $pgProx = $pgForm;

            $obTTCMBAObra->setDado("exercicio"              , Sessao::getExercicio());
            $obTTCMBAObra->setDado("cod_entidade"           , $request->get('inCodEntidade')        );
            $obTTCMBAObra->setDado("cod_tipo"               , $request->get('inCodTipoObra')        );

            $obTTCMBAObra->proximoCod( $inCodObra , $boTransacao );
            $obTTCMBAObra->setDado("cod_obra"               , $inCodObra);
            $obTTCMBAObra->setDado("local"                  , $request->get('stLocal')              );
            $obTTCMBAObra->setDado("cep"                    , $request->get('inCEP')                );

            list($inCodUF, $inCodMunicipio, $inCodBairro) = explode("_",$request->get('inCodBairro'));
            $obTTCMBAObra->setDado("cod_uf"                 , $inCodUF                              );
            $obTTCMBAObra->setDado("cod_municipio"          , $inCodMunicipio                       );
            $obTTCMBAObra->setDado("cod_bairro"             , $inCodBairro                          );
            $obTTCMBAObra->setDado("cod_funcao"             , $request->get('inCodTipoFuncaoObra')  );
            $obTTCMBAObra->setDado("nro_obra"               , $request->get('stNroObra')            );
            $obTTCMBAObra->setDado("descricao"              , $request->get('stDescricao')          );
            $obTTCMBAObra->setDado("vl_obra"                , $request->get('nuVlObra')             );
            $obTTCMBAObra->setDado("data_cadastro"          , $request->get('dtCadastro')           );
            $obTTCMBAObra->setDado("data_inicio"            , $request->get('dtInicio')             );
            $obTTCMBAObra->setDado("data_aceite"            , $request->get('dtAceite')             );
            $obTTCMBAObra->setDado("prazo"                  , $inPrazo                              );
            $obTTCMBAObra->setDado("data_recebimento"       , $request->get('dtRecebimento')        );

            $stExercicioLicitacao   = '';
            $inCodModalidade        = '';
            $inCodLicitacao         = '';
            if($request->get('stExercicioLicitacao')!=''&&$request->get('inCodModalidade')!=''&&$request->get('inCodLicitacao')!=''){
                $stExercicioLicitacao   = $request->get('stExercicioLicitacao');
                $inCodModalidade        = $request->get('inCodModalidade');
                $inCodLicitacao         = $request->get('inCodLicitacao');
            }
            $obTTCMBAObra->setDado("exercicio_licitacao"    , $stExercicioLicitacao                 );
            $obTTCMBAObra->setDado("cod_modalidade"         , $inCodModalidade                      );
            $obTTCMBAObra->setDado("cod_licitacao"          , $inCodLicitacao                       );

            $obErro = $obTTCMBAObra->inclusao($boTransacao);

            if(!$obErro->ocorreu()){
                $arAndamento = Sessao::read('arAndamento');
                $arAndamento = (is_array($arAndamento)) ? $arAndamento : array();
                if(count($arAndamento)>0 && !$obErro->ocorreu()){
                    foreach( $arAndamento as $key => $value) {
                        $obTTCMBAObraAndamento = new TTCMBAObraAndamento();
                        $obTTCMBAObraAndamento->setDado("cod_situacao"  , $value['inSituacaoObra']      );
                        $obTTCMBAObraAndamento->setDado("data_situacao" , $value['dtSituacao']          );
                        $obTTCMBAObraAndamento->setDado("justificativa" , $value['stJustificativa']     );

                        $obTTCMBAObraAndamento->setDado("cod_obra"      , $inCodObra                    );
                        $obTTCMBAObraAndamento->setDado("exercicio"     , Sessao::getExercicio()        );
                        $obTTCMBAObraAndamento->setDado("cod_entidade"  , $request->get('inCodEntidade'));
                        $obTTCMBAObraAndamento->setDado("cod_tipo"      , $request->get('inCodTipoObra'));

                        $obErro = $obTTCMBAObraAndamento->inclusao($boTransacao);

                        if($obErro->ocorreu())
                            break;
                    }
                }

                $arFiscal = Sessao::read('arFiscal');
                $arFiscal = (is_array($arFiscal)) ? $arFiscal : array();
                if(count($arFiscal)>0 && !$obErro->ocorreu()){
                    foreach( $arFiscal as $key => $value) {
                        $obTTCMBAObraFiscal = new TTCMBAObraFiscal();
                        $obTTCMBAObraFiscal->setDado("numcgm"                   , $value['inNumResponsavelFiscal']  );
                        $obTTCMBAObraFiscal->setDado("matricula"                , $value['stMatricula']             );
                        $obTTCMBAObraFiscal->setDado("registro_profissional"    , $value['stRegistro']              );
                        $obTTCMBAObraFiscal->setDado("data_inicio"              , $value['dtInicioFiscal']          );
                        $obTTCMBAObraFiscal->setDado("data_final"               , $value['dtFinalFiscal']           );

                        $obTTCMBAObraFiscal->setDado("cod_obra"                 , $inCodObra                        );
                        $obTTCMBAObraFiscal->setDado("exercicio"                , Sessao::getExercicio()            );
                        $obTTCMBAObraFiscal->setDado("cod_entidade"             , $request->get('inCodEntidade')    );
                        $obTTCMBAObraFiscal->setDado("cod_tipo"                 , $request->get('inCodTipoObra')    );

                        $obErro = $obTTCMBAObraFiscal->inclusao($boTransacao);

                        if($obErro->ocorreu())
                            break;
                    }
                }

                $arMedicao = Sessao::read('arMedicao');
                $arMedicao = (is_array($arMedicao)) ? $arMedicao : array();
                if(count($arMedicao)>0 && !$obErro->ocorreu()){
                    foreach( $arMedicao as $key => $value) {
                        $obTTCMBAObraMedicao = new TTCMBAObraMedicao();
                        $obTTCMBAObraMedicao->setDado("cod_medicao"         , $value['inNroMedicao']            );
                        $obTTCMBAObraMedicao->setDado("cod_medida"          , $value['inCodMedidaObra']         );
                        $obTTCMBAObraMedicao->setDado("data_inicio"         , $value['dtInicioMedicao']         );
                        $obTTCMBAObraMedicao->setDado("data_final"          , $value['dtFinalMedicao']          );
                        $obTTCMBAObraMedicao->setDado("data_medicao"        , $value['dtMedicao']               );
                        $obTTCMBAObraMedicao->setDado("vl_medicao"          , $value['nuVlMedicao']             );
                        $obTTCMBAObraMedicao->setDado("nro_nota_fiscal"     , $value['stNFMedicao']             );
                        $obTTCMBAObraMedicao->setDado("data_nota_fiscal"    , $value['dtNFMedicao']             );
                        $obTTCMBAObraMedicao->setDado("numcgm"              , $value['inNumAtestadorMedicao']   );

                        $obTTCMBAObraMedicao->setDado("cod_obra"            , $inCodObra                        );
                        $obTTCMBAObraMedicao->setDado("exercicio"           , Sessao::getExercicio()            );
                        $obTTCMBAObraMedicao->setDado("cod_entidade"        , $request->get('inCodEntidade')    );
                        $obTTCMBAObraMedicao->setDado("cod_tipo"            , $request->get('inCodTipoObra')    );

                        $obErro = $obTTCMBAObraMedicao->inclusao($boTransacao);

                        if($obErro->ocorreu())
                            break;
                    }
                }

                $arContrato = Sessao::read('arContrato');
                $arContrato = (is_array($arContrato)) ? $arContrato : array();
                if(count($arContrato)>0 && !$obErro->ocorreu()){
                    foreach( $arContrato as $key => $value) {
                        $obTTCMBAObraContratos = new TTCMBAObraContratos();
                        $obTTCMBAObraContratos->setDado("cod_contratacao"   , $value['inCodTipoContratacao']);
                        $obTTCMBAObraContratos->setDado("nro_instrumento"   , $value['stNroInstrumento']    );
                        $obTTCMBAObraContratos->setDado("nro_contrato"      , $value['stNroContrato']       );
                        $obTTCMBAObraContratos->setDado("nro_convenio"      , $value['stNroConvenio']       );
                        $obTTCMBAObraContratos->setDado("nro_parceria"      , $value['stNroTermo']          );
                        $obTTCMBAObraContratos->setDado("numcgm"            , $value['inNumContratado']     );
                        $obTTCMBAObraContratos->setDado("funcao_cgm"        , $value['stFuncaoContratada']  );
                        $obTTCMBAObraContratos->setDado("data_inicio"       , $value['dtInicioContrato']    );
                        $obTTCMBAObraContratos->setDado("data_final"        , $value['dtFinalContrato']     );
                        $obTTCMBAObraContratos->setDado("lotacao"           , $value['stLotacao']           );

                        $obTTCMBAObraContratos->setDado("cod_obra"          , $inCodObra                    );
                        $obTTCMBAObraContratos->setDado("exercicio"         , Sessao::getExercicio()        );
                        $obTTCMBAObraContratos->setDado("cod_entidade"      , $request->get('inCodEntidade'));
                        $obTTCMBAObraContratos->setDado("cod_tipo"          , $request->get('inCodTipoObra'));

                        $obErro = $obTTCMBAObraContratos->inclusao($boTransacao);

                        if($obErro->ocorreu())
                            break;
                    }
                }
            }
        break;
    
        case "alterar":
            $pgProx = $pgList;

            $obTTCMBAObra->setDado("exercicio"              , $request->get('stExercicio')          );
            $obTTCMBAObra->setDado("cod_entidade"           , $request->get('inCodEntidade')        );
            $obTTCMBAObra->setDado("cod_tipo"               , $request->get('inCodTipoObra')        );
            $obTTCMBAObra->setDado("cod_obra"               , $request->get('inCodObra')            );
            $obTTCMBAObra->setDado("local"                  , $request->get('stLocal')              );
            $obTTCMBAObra->setDado("cep"                    , $request->get('inCEP')                );

            list($inCodUF, $inCodMunicipio, $inCodBairro) = explode("_",$request->get('inCodBairro'));
            $obTTCMBAObra->setDado("cod_uf"                 , $inCodUF                              );
            $obTTCMBAObra->setDado("cod_municipio"          , $inCodMunicipio                       );
            $obTTCMBAObra->setDado("cod_bairro"             , $inCodBairro                          );
            $obTTCMBAObra->setDado("cod_funcao"             , $request->get('inCodTipoFuncaoObra')  );
            $obTTCMBAObra->setDado("nro_obra"               , $request->get('stNroObra')            );
            $obTTCMBAObra->setDado("descricao"              , $request->get('stDescricao')          );
            $obTTCMBAObra->setDado("vl_obra"                , $request->get('nuVlObra')             );
            $obTTCMBAObra->setDado("data_cadastro"          , $request->get('dtCadastro')           );
            $obTTCMBAObra->setDado("data_inicio"            , $request->get('dtInicio')             );
            $obTTCMBAObra->setDado("data_aceite"            , $request->get('dtAceite')             );
            $obTTCMBAObra->setDado("prazo"                  , $inPrazo                              );
            $obTTCMBAObra->setDado("data_recebimento"       , $request->get('dtRecebimento')        );

            $stExercicioLicitacao   = '';
            $inCodModalidade        = '';
            $inCodLicitacao         = '';
            if($request->get('stExercicioLicitacao')!=''&&$request->get('inCodModalidade')!=''&&$request->get('inCodLicitacao')!=''){
                $stExercicioLicitacao   = $request->get('stExercicioLicitacao');
                $inCodModalidade        = $request->get('inCodModalidade');
                $inCodLicitacao         = $request->get('inCodLicitacao');
            }
            $obTTCMBAObra->setDado("exercicio_licitacao"    , $stExercicioLicitacao                 );
            $obTTCMBAObra->setDado("cod_modalidade"         , $inCodModalidade                      );
            $obTTCMBAObra->setDado("cod_licitacao"          , $inCodLicitacao                       );

            $obErro = $obTTCMBAObra->alteracao($boTransacao);

            if(!$obErro->ocorreu()){
                if(!$obErro->ocorreu()){
                    $obTTCMBAObraAndamento = new TTCMBAObraAndamento();
                    $obTTCMBAObraAndamento->setDado("cod_obra"      , $request->get('inCodObra')    );
                    $obTTCMBAObraAndamento->setDado("exercicio"     , $request->get('stExercicio')  );
                    $obTTCMBAObraAndamento->setDado("cod_entidade"  , $request->get('inCodEntidade'));
                    $obTTCMBAObraAndamento->setDado("cod_tipo"      , $request->get('inCodTipoObra'));
    
                    $obErro = $obTTCMBAObraAndamento->exclusao($boTransacao);
                }

                $arAndamento = Sessao::read('arAndamento');
                $arAndamento = (is_array($arAndamento)) ? $arAndamento : array();
                if(count($arAndamento)>0 && !$obErro->ocorreu()){
                    foreach( $arAndamento as $key => $value) {
                        $obTTCMBAObraAndamento = new TTCMBAObraAndamento();
                        $obTTCMBAObraAndamento->setDado("cod_situacao"  , $value['inSituacaoObra']      );
                        $obTTCMBAObraAndamento->setDado("data_situacao" , $value['dtSituacao']          );
                        $obTTCMBAObraAndamento->setDado("justificativa" , $value['stJustificativa']     );

                        $obTTCMBAObraAndamento->setDado("cod_obra"      , $request->get('inCodObra')    );
                        $obTTCMBAObraAndamento->setDado("exercicio"     , $request->get('stExercicio')  );
                        $obTTCMBAObraAndamento->setDado("cod_entidade"  , $request->get('inCodEntidade'));
                        $obTTCMBAObraAndamento->setDado("cod_tipo"      , $request->get('inCodTipoObra'));

                        $obErro = $obTTCMBAObraAndamento->inclusao($boTransacao);

                        if($obErro->ocorreu())
                            break;
                    }
                }

                if(!$obErro->ocorreu()){
                    $obTTCMBAObraFiscal = new TTCMBAObraFiscal();
                    $obTTCMBAObraFiscal->setDado("cod_obra"                 , $request->get('inCodObra')        );
                    $obTTCMBAObraFiscal->setDado("exercicio"                , $request->get('stExercicio')      );
                    $obTTCMBAObraFiscal->setDado("cod_entidade"             , $request->get('inCodEntidade')    );
                    $obTTCMBAObraFiscal->setDado("cod_tipo"                 , $request->get('inCodTipoObra')    );

                    $obErro = $obTTCMBAObraFiscal->exclusao($boTransacao);
                }

                $arFiscal = Sessao::read('arFiscal');
                $arFiscal = (is_array($arFiscal)) ? $arFiscal : array();
                if(count($arFiscal)>0 && !$obErro->ocorreu()){
                    foreach( $arFiscal as $key => $value) {
                        $obTTCMBAObraFiscal = new TTCMBAObraFiscal();
                        $obTTCMBAObraFiscal->setDado("numcgm"                   , $value['inNumResponsavelFiscal']  );
                        $obTTCMBAObraFiscal->setDado("matricula"                , $value['stMatricula']             );
                        $obTTCMBAObraFiscal->setDado("registro_profissional"    , $value['stRegistro']              );
                        $obTTCMBAObraFiscal->setDado("data_inicio"              , $value['dtInicioFiscal']          );
                        $obTTCMBAObraFiscal->setDado("data_final"               , $value['dtFinalFiscal']           );

                        $obTTCMBAObraFiscal->setDado("cod_obra"                 , $request->get('inCodObra')        );
                        $obTTCMBAObraFiscal->setDado("exercicio"                , $request->get('stExercicio')      );
                        $obTTCMBAObraFiscal->setDado("cod_entidade"             , $request->get('inCodEntidade')    );
                        $obTTCMBAObraFiscal->setDado("cod_tipo"                 , $request->get('inCodTipoObra')    );

                        $obErro = $obTTCMBAObraFiscal->inclusao($boTransacao);

                        if($obErro->ocorreu())
                            break;
                    }
                }

                if(!$obErro->ocorreu()){
                    $obTTCMBAObraMedicao = new TTCMBAObraMedicao();
                    $obTTCMBAObraMedicao->setDado("cod_obra"            , $request->get('inCodObra')        );
                    $obTTCMBAObraMedicao->setDado("exercicio"           , $request->get('stExercicio')      );
                    $obTTCMBAObraMedicao->setDado("cod_entidade"        , $request->get('inCodEntidade')    );
                    $obTTCMBAObraMedicao->setDado("cod_tipo"            , $request->get('inCodTipoObra')    );

                    $obErro = $obTTCMBAObraMedicao->exclusao($boTransacao);
                }

                $arMedicao = Sessao::read('arMedicao');
                $arMedicao = (is_array($arMedicao)) ? $arMedicao : array();
                if(count($arMedicao)>0 && !$obErro->ocorreu()){
                    foreach( $arMedicao as $key => $value) {
                        $obTTCMBAObraMedicao = new TTCMBAObraMedicao();
                        $obTTCMBAObraMedicao->setDado("cod_medicao"         , $value['inNroMedicao']            );
                        $obTTCMBAObraMedicao->setDado("cod_medida"          , $value['inCodMedidaObra']         );
                        $obTTCMBAObraMedicao->setDado("data_inicio"         , $value['dtInicioMedicao']         );
                        $obTTCMBAObraMedicao->setDado("data_final"          , $value['dtFinalMedicao']          );
                        $obTTCMBAObraMedicao->setDado("data_medicao"        , $value['dtMedicao']               );
                        $obTTCMBAObraMedicao->setDado("vl_medicao"          , $value['nuVlMedicao']             );
                        $obTTCMBAObraMedicao->setDado("nro_nota_fiscal"     , $value['stNFMedicao']             );
                        $obTTCMBAObraMedicao->setDado("data_nota_fiscal"    , $value['dtNFMedicao']             );
                        $obTTCMBAObraMedicao->setDado("numcgm"              , $value['inNumAtestadorMedicao']   );

                        $obTTCMBAObraMedicao->setDado("cod_obra"            , $request->get('inCodObra')        );
                        $obTTCMBAObraMedicao->setDado("exercicio"           , $request->get('stExercicio')      );
                        $obTTCMBAObraMedicao->setDado("cod_entidade"        , $request->get('inCodEntidade')    );
                        $obTTCMBAObraMedicao->setDado("cod_tipo"            , $request->get('inCodTipoObra')    );

                        $obErro = $obTTCMBAObraMedicao->inclusao($boTransacao);

                        if($obErro->ocorreu())
                            break;
                    }
                }

                if(!$obErro->ocorreu()){
                    $obTTCMBAObraContratos = new TTCMBAObraContratos();
                    $obTTCMBAObraContratos->setDado("cod_obra"          , $request->get('inCodObra')    );
                    $obTTCMBAObraContratos->setDado("exercicio"         , $request->get('stExercicio')  );
                    $obTTCMBAObraContratos->setDado("cod_entidade"      , $request->get('inCodEntidade'));
                    $obTTCMBAObraContratos->setDado("cod_tipo"          , $request->get('inCodTipoObra'));

                    $obErro = $obTTCMBAObraContratos->exclusao($boTransacao);
                }

                $arContrato = Sessao::read('arContrato');
                $arContrato = (is_array($arContrato)) ? $arContrato : array();
                if(count($arContrato)>0 && !$obErro->ocorreu()){
                    foreach( $arContrato as $key => $value) {
                        $obTTCMBAObraContratos = new TTCMBAObraContratos();
                        $obTTCMBAObraContratos->setDado("cod_contratacao"   , $value['inCodTipoContratacao']);
                        $obTTCMBAObraContratos->setDado("nro_instrumento"   , $value['stNroInstrumento']    );
                        $obTTCMBAObraContratos->setDado("nro_contrato"      , $value['stNroContrato']       );
                        $obTTCMBAObraContratos->setDado("nro_convenio"      , $value['stNroConvenio']       );
                        $obTTCMBAObraContratos->setDado("nro_parceria"      , $value['stNroTermo']          );
                        $obTTCMBAObraContratos->setDado("numcgm"            , $value['inNumContratado']     );
                        $obTTCMBAObraContratos->setDado("funcao_cgm"        , $value['stFuncaoContratada']  );
                        $obTTCMBAObraContratos->setDado("data_inicio"       , $value['dtInicioContrato']    );
                        $obTTCMBAObraContratos->setDado("data_final"        , $value['dtFinalContrato']     );
                        $obTTCMBAObraContratos->setDado("lotacao"           , $value['stLotacao']           );

                        $obTTCMBAObraContratos->setDado("cod_obra"          , $request->get('inCodObra')    );
                        $obTTCMBAObraContratos->setDado("exercicio"         , $request->get('stExercicio')  );
                        $obTTCMBAObraContratos->setDado("cod_entidade"      , $request->get('inCodEntidade'));
                        $obTTCMBAObraContratos->setDado("cod_tipo"          , $request->get('inCodTipoObra'));

                        $obErro = $obTTCMBAObraContratos->inclusao($boTransacao);

                        if($obErro->ocorreu())
                            break;
                    }
                }
            }
        break;
    
        case "excluir":
            $pgProx = $pgList;

            if(!$obErro->ocorreu()){
                $obTTCMBAObraAndamento = new TTCMBAObraAndamento();
                $obTTCMBAObraAndamento->setDado("cod_obra"      , $request->get('inCodObra')    );
                $obTTCMBAObraAndamento->setDado("exercicio"     , $request->get('stExercicio')  );
                $obTTCMBAObraAndamento->setDado("cod_entidade"  , $request->get('inCodEntidade'));
                $obTTCMBAObraAndamento->setDado("cod_tipo"      , $request->get('inCodTipo')    );
                $obErro = $obTTCMBAObraAndamento->exclusao($boTransacao);
            }

            if(!$obErro->ocorreu()){
                $obTTCMBAObraFiscal = new TTCMBAObraFiscal();
                $obTTCMBAObraFiscal->setDado("cod_obra"     , $request->get('inCodObra')    );
                $obTTCMBAObraFiscal->setDado("exercicio"    , $request->get('stExercicio')  );
                $obTTCMBAObraFiscal->setDado("cod_entidade" , $request->get('inCodEntidade'));
                $obTTCMBAObraFiscal->setDado("cod_tipo"     , $request->get('inCodTipo')    );
                $obErro = $obTTCMBAObraFiscal->exclusao($boTransacao);
            }

            if(!$obErro->ocorreu()){
                $obTTCMBAObraMedicao = new TTCMBAObraMedicao();
                $obTTCMBAObraMedicao->setDado("cod_obra"    , $request->get('inCodObra')    );
                $obTTCMBAObraMedicao->setDado("exercicio"   , $request->get('stExercicio')  );
                $obTTCMBAObraMedicao->setDado("cod_entidade", $request->get('inCodEntidade'));
                $obTTCMBAObraMedicao->setDado("cod_tipo"    , $request->get('inCodTipo')    );
                $obErro = $obTTCMBAObraMedicao->exclusao($boTransacao);
            }

            if(!$obErro->ocorreu()){
                $obTTCMBAObraContratos = new TTCMBAObraContratos();
                $obTTCMBAObraContratos->setDado("cod_obra"      , $request->get('inCodObra')    );
                $obTTCMBAObraContratos->setDado("exercicio"     , $request->get('stExercicio')  );
                $obTTCMBAObraContratos->setDado("cod_entidade"  , $request->get('inCodEntidade'));
                $obTTCMBAObraContratos->setDado("cod_tipo"      , $request->get('inCodTipo')    );
                $obErro = $obTTCMBAObraContratos->exclusao($boTransacao);
            }

            if(!$obErro->ocorreu()){
                $obTTCMBAObra->setDado("exercicio"      , $request->get('stExercicio')  );
                $obTTCMBAObra->setDado("cod_entidade"   , $request->get('inCodEntidade'));
                $obTTCMBAObra->setDado("cod_tipo"       , $request->get('inCodTipo')    );
                $obTTCMBAObra->setDado("cod_obra"       , $request->get('inCodObra')    );
                $obErro = $obTTCMBAObra->exclusao($boTransacao);
            }
        break;
    }
}

if(!$obErro->ocorreu()){
    $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCMBAObra);
    SistemaLegado::alertaAviso($pgProx."?".Sessao::getId().$stLink."&stAcao=".$stAcao,$request->get('stNroObra').'/'.Sessao::getExercicio(),$stAcao,"aviso", Sessao::getId(), "../");
}else{
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
}

SistemaLegado::LiberaFrames(true,false);

?>

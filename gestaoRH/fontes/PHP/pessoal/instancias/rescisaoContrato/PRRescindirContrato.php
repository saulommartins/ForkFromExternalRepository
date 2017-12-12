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
* Página de Processamento Pessoal - Rescindir Contrato
* Data de Criação   : 20/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Id: PRRescindirContrato.php 65923 2016-06-30 13:18:20Z michel $

* Casos de uso: uc-04.04.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RPessoalRescisaoContrato.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalPensao.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorContratoServidor.class.php";
include_once CAM_GA_ADM_NEGOCIO."RUsuario.class.php";

$stAcao = $request->get('stAcao');

$stPrograma = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obErro = new Erro;
$obRPessoalRescisaoContrato  = new RPessoalRescisaoContrato;

$obTransacao = new Transacao();
$boFlagTransacao = false;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

switch ($stAcao) {
    case "incluir":
        if (!$obErro->ocorreu()) {
            $obRPessoalRescisaoContrato->obRPessoalCausaRescisao->addPessoalCasoCausa();
            $obRPessoalRescisaoContrato->obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->setCodCasoCausa( $request->get('inCasoCausa') );
            $obRPessoalRescisaoContrato->setDtRescisao( $request->get('dtRescisao') );
            $obRPessoalRescisaoContrato->setNroCertidaoObito( $request->get('stNroCertidaoObito') );
            $obRPessoalRescisaoContrato->setDescCausaMortis( $request->get('stDescCausaMortis') );
            $obRPessoalRescisaoContrato->setAvisoPrevio( $request->get('stAvisoPrevio') );
            $obRPessoalRescisaoContrato->setDataAvisoPrevio( $request->get('dtAviso') );
            $obRPessoalRescisaoContrato->setIncorporarFolhaSalario( ($request->get('boFolhaSalario') == 1) ? true : false );
            $obRPessoalRescisaoContrato->setIncorporarFolhaDecimo( ($request->get('boFolhaDecimo') == 1) ? true : false );
            $inCodNorma = $request->get('inCodNorma');
            $obRPessoalRescisaoContrato->setRNorma( $inCodNorma );
            $obRPessoalRescisaoContrato->setExercicio(Sessao::getExercicio());

            // Verifica se veio da ação de alteracão de pensionista com opção de rescisão de contrato
            if (sessao::read('incluirRescisaoContratoPensionista') != null) {
                $obRPessoalRescisaoContrato->obRPessoalContrato->setCodContrato( $request->get('inCodContrato') );
                $obErro = $obRPessoalRescisaoContrato->incluirRescisaoContratoPensionista($boTransacao);
                $pgFilt = "../pensionista/FLManterPensionista.php?".Sessao::getId();                
            } else {
                $obRPessoalRescisaoContrato->obRPessoalContratoServidor->setCodContrato( $request->get('inCodContrato') );
                $obErro = $obRPessoalRescisaoContrato->incluirRescisaoContrato($boTransacao);
            }

            // Desativar Usuário do Servidor
            if (!$obErro->ocorreu() && $request->get('stDesativarUsuario')=='sim') {
                $obRUsuario = new RUsuario;
                $obRUsuario->obRCGM->setNumCGM( $request->get('inNumCGM') );
                $obErro = $obRUsuario->consultar($rsLista, $boTransacao);

                if (!$obErro->ocorreu() && !$rsLista->eof()) {
                    $obRUsuario->setStatus( 'I' );
                    $obErro = $obRUsuario->alterarUsuario($boTransacao);
                }
            }

            if (!$obErro->ocorreu()) {
                $obTPessoalServidorContratoServidor = new TPessoalServidorContratoServidor();
                $obTPessoalServidorContratoServidor->setDado('cod_contrato',$request->get('inCodContrato'));
                $obErro = $obTPessoalServidorContratoServidor->listar($rsLista,$boTransacao);    
                if (!$obErro->ocorreu()) {
                    $obTPessoalPensao = new TPessoalPensao();
                    $stFiltro = " WHERE cod_servidor = ".$rsLista->getCampo('cod_servidor')." ";
                    $obErro = $obTPessoalPensao->recuperaRelacionamento($rsPensao,$stFiltro,'',$boTransacao);                    
                    //Realiza a alteracao da data limite se já houver pensao
                    if (!$obErro->ocorreu()) {
                        if ( $rsPensao->getNumLinhas() > 0 ){
                            $obTPessoalPensao->setDado('cod_pensao'    , $rsPensao->getCampo('cod_pensao') );
                            $obTPessoalPensao->setDado('cod_dependente', $rsPensao->getCampo('cod_dependente') );
                            $obTPessoalPensao->setDado('cod_servidor'  , $rsPensao->getCampo('cod_servidor') );
                            $obTPessoalPensao->setDado('tipo_pensao'   , $rsPensao->getCampo('tipo_pensao') );
                            $obTPessoalPensao->setDado('dt_inclusao'   , $rsPensao->getCampo('dt_inclusao') );
                            $obTPessoalPensao->setDado('percentual'    , $rsPensao->getCampo('percentual') );
                            $obTPessoalPensao->setDado('observacao'    , $rsPensao->getCampo('observacao') );
                            //Atualizando a data_limite de acordo com a data da rescisao
                            $obTPessoalPensao->setDado('dt_limite'     , $request->get('dtRescisao') );
                        
                            $obErro = $obTPessoalPensao->alteracao($boTransacao);
                        }
                    }
                }
            }

            if (sessao::read('incluirRescisaoContratoPensionista') != null) {
                $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRPessoalRescisaoContrato->obTPessoalContratoPensionistaCasoCausa );
            }else{
                $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRPessoalRescisaoContrato->obTPessoalContratoServidorCasoCausa );
            }

            if (!$obErro->ocorreu()) {
                if ($request->get('boGeraTermoRecisao') == 'true' && !$obErro->ocorreu()) {
                    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                    $obTPessoalContrato = new TPessoalContrato;
                    $stFiltro = " AND contrato.cod_contrato = ".$request->get('inCodContrato');
                    $obErro = $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro,'',$boTransacao);
                    $arContratos = array();
                    $arTmp = array(
                        'inContrato' => $rsCGM->getCampo("registro"),
                        'cod_contrato' => $rsCGM->getCampo("cod_contrato"),
                        'numcgm' => $rsCGM->getCampo("numcgm"),
                        'nom_cgm' => $rsCGM->getCampo("nom_cgm")
                    );
                    $arContratos[] = $arTmp;
    
                    //Necessário calcular recisão para gerar Termo de recisão
                    $obRFolhaPagamentoCalcularFolhas = new RFolhaPagamentoCalcularFolhas();
                    $obRFolhaPagamentoCalcularFolhas->setTipoFiltro('cgm_contrato');
                    $obRFolhaPagamentoCalcularFolhas->setCodigos($arContratos);
                    $obRFolhaPagamentoCalcularFolhas->setCalcularRescisao();
                    $obRFolhaPagamentoCalcularFolhas->calcularFolha(true);
                }
            }

            if ( !$obErro->ocorreu() ) {
                if ($request->get('boGeraTermoRecisao') == 'true') {
                    //busca competência atual
                    $obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
                    $obRFolhaPagamentoFolhaSituacao->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao,$boTransacao);

                    $arData = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
                    $inMes     = (int) ($arData[1]);
                    $stAno     = $arData[2];

                    $stLink  = "?stCaminho=".CAM_GRH_FOL_INSTANCIAS."relatorio/PREmitirTermoRescisao.php";
                    $stLink .= "&stTipoFiltro=contrato_rescisao&stOrdenacao=alfabetica";
                    $stLink .= "&inCodMes=".$inMes;
                    $stLink .= "&inAno=".$stAno;

                    Sessao::write('arContratos', $arContratos);
                }

                SistemaLegado::LiberaFrames(true, false);
                SistemaLegado::alertaAviso($pgFilt,"Matrícula: ".$_POST['inRegistro'],"incluir","aviso", Sessao::getId(), "../");

                if ($request->get('boGeraTermoRecisao') == 'true') {
                    SistemaLegado::mudaFrameOculto(CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stLink);
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }        
    break;

    case "excluir":
        if (!$obErro->ocorreu()) {
            $obTPessoalServidorContratoServidor = new TPessoalServidorContratoServidor();
            $obTPessoalServidorContratoServidor->setDado('cod_contrato',$request->get('inCodContrato'));
            $obErro = $obTPessoalServidorContratoServidor->listar($rsLista,$boTransacao);    
            if (!$obErro->ocorreu()) {
                $obTPessoalPensao = new TPessoalPensao();
                $stFiltro = " WHERE cod_servidor = ".$rsLista->getCampo('cod_servidor')." ";
                $obErro = $obTPessoalPensao->recuperaRelacionamento($rsPensao,$stFiltro,'',$boTransacao);                                
                if (!$obErro->ocorreu()) {
                    if ( $rsPensao->getNumLinhas() > 0 ){
                        $obTPessoalPensao->setDado('cod_pensao'    , $rsPensao->getCampo('cod_pensao') );
                        $obTPessoalPensao->setDado('cod_dependente', $rsPensao->getCampo('cod_dependente') );
                        $obTPessoalPensao->setDado('cod_servidor'  , $rsPensao->getCampo('cod_servidor') );
                        $obTPessoalPensao->setDado('tipo_pensao'   , $rsPensao->getCampo('tipo_pensao') );
                        $obTPessoalPensao->setDado('dt_inclusao'   , $rsPensao->getCampo('dt_inclusao') );
                        $obTPessoalPensao->setDado('percentual'    , $rsPensao->getCampo('percentual') );
                        $obTPessoalPensao->setDado('observacao'    , $rsPensao->getCampo('observacao') );
                        $obTPessoalPensao->setDado('timestamp'    , $rsPensao->getCampo('timestamp') );
                        //Atualizando a data_limite de acordo com a data da rescisao
                        $obTPessoalPensao->setDado('dt_limite'     , '' );
                        $obErro = $obTPessoalPensao->alteracao($boTransacao);
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            $obRPessoalRescisaoContrato->obRPessoalContratoServidor->setCodContrato( $request->get('inCodContrato') );
            $obErro = $obRPessoalRescisaoContrato->excluirRescisaoContrato($boTransacao);
        }

        // Reativar Usuário do Servidor
        if (!$obErro->ocorreu()) {
            $arAtivarUsuario = Sessao::read('arAtivarUsuario');
            $arAtivarUsuario = (is_array($arAtivarUsuario)) ? $arAtivarUsuario : array();

            $boAtivarUsuario = $arAtivarUsuario[$request->get('inRegistro').'.'.$request->get('inNumCGM')];

            if($boAtivarUsuario == 'true'){
                $obRUsuario = new RUsuario;
                $obRUsuario->obRCGM->setNumCGM( $request->get('inNumCGM') );
                $obErro = $obRUsuario->consultar($rsLista, $boTransacao);

                if (!$obErro->ocorreu() && !$rsLista->eof()) {
                    $obRUsuario->setStatus( 'A' );
                    $obErro = $obRUsuario->alterarUsuario($boTransacao);
                }
            }

            Sessao::write('arAtivarUsuario', array());
        }

        if ( !$obErro->ocorreu() ){
            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRPessoalRescisaoContrato->obTPessoalContratoServidorCasoCausa );
            SistemaLegado::alertaAviso($pgFilt,"Matrícula: ".$request->get('inRegistro'),"excluir","aviso", Sessao::getId(), "../");
        }else{
            SistemaLegado::alertaAviso($pgFilt,urlencode($request->get('inRegistro')),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;
}

?>

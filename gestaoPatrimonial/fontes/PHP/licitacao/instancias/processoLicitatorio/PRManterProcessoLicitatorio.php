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
    * Página de Processamento de cadastro de Fornecedores
    * Data de Criação   : 10/10/06

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso: uc-03.05.15

    $Id: PRManterProcessoLicitatorio.php 63445 2015-08-28 13:44:54Z michel $

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC.'TLicitacaoLicitacao.class.php';
include_once TCOM.'TComprasFornecedorAtividade.class.php';
include_once TCOM.'TComprasSolicitacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'../../../licitacao/classes/mapeamento/TLicitacaoComissao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapa.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItem.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItemAnulacao.class.php';
include_once TCOM.'TComprasMapaModalidade.class.php';
include_once TCOM.'TComprasMapaSolicitacao.class.php';
include_once TCOM.'TComprasMapaItemReserva.class.php';
include_once CAM_GA_PROT_MAPEAMENTO.'TProcesso.class.php';
include_once TLIC.'TLicitacaoComissaoLicitacao.class.php';
include_once TLIC.'TLicitacaoMembroAdicional.class.php';
include_once TLIC.'TLicitacaoLicitacaoDocumentos.class.php';
include_once TLIC.'TLicitacaoComissaoLicitacaoMembros.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaSolicitacaoAnulacao.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoAutorizacaoEmpenho.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoAutorizacaoAnulada.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPreEmpenhoJulgamento.class.php';
include_once TLIC.'TLicitacaoLicitacaoAnulada.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoAnulada.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasJulgamento.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasJulgamentoItem.class.php';
include_once TLIC.'TLicitacaoParticipanteDocumentos.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasCompraDireta.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcessoLicitatorio";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

if (!empty($_REQUEST['inCodComissao'])) {
    $stMensagem = validaVigenciaComissao($_REQUEST['inCodComissao']);
}

Sessao::setTrataExcecao( true );
$obTLicitacaoLicitacao = new TLicitacaoLicitacao();
Sessao::getTransacao()->setMapeamento( $obTLicitacaoLicitacao );

$stAcao = $request->get('stAcao');

$boReservaRigida = SistemaLegado::pegaConfiguracao('reserva_rigida', '35', Sessao::getExercicio());
$boReservaRigida = ($boReservaRigida == 'true') ? true : false;

$boReservaAutorizacao = SistemaLegado::pegaConfiguracao('reserva_autorizacao', '35', Sessao::getExercicio());
$boReservaAutorizacao = ($boReservaAutorizacao == 'true') ? true : false;

switch ($stAcao) {
    case "incluir":
        Sessao::write('acao', 1567);

        if ($stMensagem == "") {

            $arMapa = explode('/',$_REQUEST['stMapaCompras'] );

            $stMensagem = verificaMapaAnulado($arMapa[0], $arMapa[1], 'incluir');

            if (empty($stMensagem)) {
                $stMensagem = verificaUtilizacaoMapa($arMapa[0], $arMapa[1], 'incluir');
            }

            //Só entra no if se município pertencer ao estado de MG ou GO
            $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf');
            if ( ($request->get('inCodTipoObjeto') == 2 && $request->get('inCodRegime') == '') && ($inCodUF == 11 || $inCodUF == 9) ) {
                $stMensagem = "O Regime de execução de Obras é obrigatório para o Tipo de Objeto selecionado.";
            }
            
            if ( empty($stMensagem) && $_REQUEST['inMontaCodUnidadeM'] == '') {
                $stMensagem = "O subcampo unidade da Unidade Executora é obrigatório.";   
            }

            if (empty($stMensagem)) {
                $arProcesso = explode('/',$_REQUEST['stChaveProcesso'] );

                $obTPROProcesso = new TProcesso();
                $obTPROProcesso->setDado('cod_processo', (int) $arProcesso[0]);
                $obTPROProcesso->setDado('ano_exercicio',$arProcesso[1]);
                $obTPROProcesso->recuperaPorChave ($rsProcesso);

                $obTComprasMapaSolicitacao = new TComprasMapaSolicitacao();
                $obTComprasMapaItemReserva = new TComprasMapaItemReserva();
                $obTComprasSolicitacao = new TComprasSolicitacao();

                $boReservaSaldo = true;

                $obTComprasMapaSolicitacao->setDado('cod_mapa'  , $arMapa[0]);
                $obTComprasMapaSolicitacao->setDado('exercicio' , $arMapa[1]);
                $obTComprasMapaSolicitacao->recuperaPorChave( $rsSolicitacaoMapa );

                $soma = 0;

                while (!$rsSolicitacaoMapa->eof()) {
                    $obTComprasSolicitacao->setDado('cod_solicitacao'   , $rsSolicitacaoMapa->getCampo('cod_solicitacao'));
                    $obTComprasSolicitacao->setDado('cod_entidade'      , $rsSolicitacaoMapa->getCampo('cod_entidade'));
                    $obTComprasSolicitacao->setDado('exercicio'         , $rsSolicitacaoMapa->getCampo('exercicio'));
                    $obTComprasSolicitacao->recuperaValoresTotaisSolicitacao($rsValoresSolicitacao);

                    $soma = $soma + $rsValoresSolicitacao->getCampo("total");

                    $rsSolicitacaoMapa->proximo();
                }

                $rsSolicitacaoMapa->setPrimeiroElemento();
                while (!$rsSolicitacaoMapa->eof()) {
                    $obTComprasMapaItemReserva->setDado('cod_solicitacao'   , $rsSolicitacaoMapa->getCampo('cod_solicitacao'));
                    $obTComprasMapaItemReserva->setDado('cod_entidade'      , $rsSolicitacaoMapa->getCampo('cod_entidade'));
                    $obTComprasMapaItemReserva->setDado('exercicio'         , $rsSolicitacaoMapa->getCampo('exercicio'));
                    $obTComprasMapaItemReserva->setDado('cod_mapa'          , $arMapa[0]);
                    $obTComprasMapaItemReserva->recuperaMapaItemReserva( $rsMapaItemReserva );
                    if ( $rsMapaItemReserva->getNumLinhas() > 0 ) {
                        if($boReservaRigida){
                            $boReservaSaldo = false;
                            break;
                        }
                    }
                    $rsSolicitacaoMapa->proximo();
                }

                if ( isset($_REQUEST['inCodLicitacaoImplantacao']) ) {
                    if ($_REQUEST['inCodLicitacaoImplantacao'] == '' || $_REQUEST['inCodLicitacaoImplantacao'] == '0') {
                        $stMensagem = "Código da Licitação inválido.";
                    } else {
                        $obTLicitacaoLicitacao->setDado('cod_licitacao' , $_REQUEST['inCodLicitacaoImplantacao'] );
                        $obTLicitacaoLicitacao->setDado('cod_modalidade', $_REQUEST['inCodModalidade']);
                        $obTLicitacaoLicitacao->setDado('cod_entidade'  , $_REQUEST['inCodEntidade'] );
                        $obTLicitacaoLicitacao->setDado('exercicio'     , Sessao::getExercicio() );
                        $obTLicitacaoLicitacao->recuperaPorChave( $rsLicitacao );

                        if ( $rsLicitacao->getNumLinhas() > 0 ) {
                            $stMensagem = "Este código de licitação já existe";
                        }
                    }
                }

                if ( $rsProcesso->getNumLinhas() <= 0 ) {
                    $stMensagem = "Código de Processo Administrativo inválido.";
                }
                if (!$boReservaSaldo) {
                    $stMensagem = "Todos itens do mapa (".$_REQUEST['stMapaCompras'].") devem estar com reserva de saldo. Efetue a manutenção das dotações no mapa.";
                }
                if (count(Sessao::read('arDocumentos')) <= 0) {
                    $stMensagem = "É necessário selecionar ao menos um documento.";
                }

                if ($stMensagem == '') {
                    $dtLicitacao = $_REQUEST['stDtLicitacao'];

                    $arStUnidadeOrcamentaria = explode('.',$request->get('stUnidadeOrcamentaria'));

                    $obTLicitacaoLicitacao->setDado('cod_modalidade'        , $_REQUEST['inCodModalidade']      );
                    $obTLicitacaoLicitacao->setDado('cod_entidade'          , $_REQUEST['inCodEntidade']        );
                    $obTLicitacaoLicitacao->setDado('exercicio'             , Sessao::getExercicio()            );
                    $obTLicitacaoLicitacao->setDado('cod_tipo_objeto'       , $_REQUEST['inCodTipoObjeto']      );
                    $obTLicitacaoLicitacao->setDado('cod_objeto'            , $_REQUEST['stObjeto']             );
                    $obTLicitacaoLicitacao->setDado('cod_criterio'          , $_REQUEST['inCodCriterio']        );
                    $obTLicitacaoLicitacao->setDado('cod_tipo_licitacao'    , $_REQUEST['inCodTipoCotacao']     );
                    
                    if (($_REQUEST['inCodModalidade'] == 8 ||
                         $_REQUEST['inCodModalidade'] == 9 ||
                         $_REQUEST['inCodModalidade'] == 10
                        ) && $_REQUEST['boRegistroModalidade'] != '' ) {                
                         $obTLicitacaoLicitacao->setDado('tipo_chamada_publica', isset($_REQUEST['boRegistroModalidade']) ? $_REQUEST['boRegistroModalidade'] : 0 );
                    } else {
                         $obTLicitacaoLicitacao->setDado('tipo_chamada_publica', 0);
                    }
                    
                    if (($_REQUEST['inCodModalidade'] == 3 ||
                         $_REQUEST['inCodModalidade'] == 6 ||
                         $_REQUEST['inCodModalidade'] == 7
                       ) && $_REQUEST['boHdnTipoRegistroPrecos'] == 1) {
                        $obTLicitacaoLicitacao->setDado('registro_precos'  , 't' );
                    } else {
                        $obTLicitacaoLicitacao->setDado('registro_precos'  , 'f' );
                    }
                    
                    $obTLicitacaoLicitacao->setDado('cod_mapa'              , $arMapa[0]                        );
                    $obTLicitacaoLicitacao->setDado('exercicio_mapa'        , "".$arMapa[1].""                  );
                    $obTLicitacaoLicitacao->setDado('cod_processo'          , $arProcesso[0]                    );
                    $obTLicitacaoLicitacao->setDado('exercicio_processo'    , "".$arProcesso[1].""              );
                    $obTLicitacaoLicitacao->setDado('vl_cotado'             , $_REQUEST['stValorReferencial']   );
                    $obTLicitacaoLicitacao->setDado('timestamp'             , substr($dtLicitacao, 6, 4).'-'.substr($dtLicitacao, 3, 2).'-'.substr($dtLicitacao,0,2).date(' H:i:s.ms'));
                    $obTLicitacaoLicitacao->setDado('num_orgao'             , $arStUnidadeOrcamentaria[0]       );
                    $obTLicitacaoLicitacao->setDado('num_unidade'           , $arStUnidadeOrcamentaria[1]       );
                    $obTLicitacaoLicitacao->setDado('cod_regime'            , $_REQUEST['inCodRegime'] != '' ? $_REQUEST['inCodRegime'] : 'null');
                    $obTLicitacaoLicitacao->inclusao();
                    
                    $obTMapaModalidade = new TComprasMapaModalidade();
                    $obTMapaModalidade->setDado('cod_mapa'      ,$arMapa[0]);
                    $obTMapaModalidade->setDado('exercicio'     ,$arMapa[1]);
                    $obTMapaModalidade->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
                    $obTMapaModalidade->recuperaTodos( $rsMapaModalidade );
                    if ( $rsMapaModalidade->getNumLinhas() > 0 ) {
                        $obTMapaModalidade->alteracao();
                    } else {
                        $obTMapaModalidade->inclusao();
                    }

                    $obTLicitacaoComissaoLicitacao = new TLicitacaoComissaoLicitacao();
                    $obTLicitacaoComissaoLicitacao->setDado('exercicio'     , Sessao::getExercicio()                              );
                    $obTLicitacaoComissaoLicitacao->setDado('cod_entidade'  , $_REQUEST['inCodEntidade']                      );
                    $obTLicitacaoComissaoLicitacao->setDado('cod_licitacao' , $obTLicitacaoLicitacao->getDado('cod_licitacao'));
                    $obTLicitacaoComissaoLicitacao->setDado('cod_modalidade', $_REQUEST['inCodModalidade']                    );
                    $obTLicitacaoComissaoLicitacao->setDado('cod_comissao'  , $_REQUEST['inCodComissao'  ]                    );
                    $obTLicitacaoComissaoLicitacao->inclusao();

                    if ( $request->get('inCodComissaoApoio') ) {
                        $obTLicitacaoComissaoLicitacao->setDado('cod_comissao',$_REQUEST['inCodComissaoApoio']);
                        $obTLicitacaoComissaoLicitacao->inclusao();
                    }

                    if (Sessao::read('arMembro')) {
                        $obTLicitacaoMembroAdicional = new TLicitacaoMembroAdicional();                        
                        foreach (Sessao::read('arMembro') as $value) {
                            if (trim($value['adicional']) == 'Sim') {
                                $obTLicitacaoMembroAdicional->setDado('numcgm'          , $value['num_cgm'] );
                                $obTLicitacaoMembroAdicional->setDado('cod_licitacao'   , $obTLicitacaoLicitacao->getDado('cod_licitacao') );
                                $obTLicitacaoMembroAdicional->setDado('exercicio'       , Sessao::getExercicio() );
                                $obTLicitacaoMembroAdicional->setDado('cod_modalidade'  , $_REQUEST['inCodModalidade'] );
                                $obTLicitacaoMembroAdicional->setDado('cod_entidade'    , $_REQUEST['inCodEntidade'] );
                                $obTLicitacaoMembroAdicional->setDado('cargo'           , $value['cargo_membro'] );
                                $obTLicitacaoMembroAdicional->setDado('natureza_cargo'  , $value['cod_natureza_cargo'] );
                                $obTLicitacaoMembroAdicional->inclusao();
                            }
                        }
                    }

                    if (Sessao::read('arDocumentos')) {
                        $obTLicitacaoLicitacaoDocumentos = new TLicitacaoLicitacaoDocumentos();
                        foreach (Sessao::read('arDocumentos') as $value) {
                            $obTLicitacaoLicitacaoDocumentos->setDado('cod_documento'   , $value['cod_documento']);
                            $obTLicitacaoLicitacaoDocumentos->setDado('cod_licitacao'   , $obTLicitacaoLicitacao->getDado('cod_licitacao'));
                            $obTLicitacaoLicitacaoDocumentos->setDado('cod_modalidade'  , $_REQUEST['inCodModalidade']);
                            $obTLicitacaoLicitacaoDocumentos->setDado('cod_entidade'    , $_REQUEST['inCodEntidade']);
                            $obTLicitacaoLicitacaoDocumentos->setDado('exercicio'       , Sessao::getExercicio());
                            $obTLicitacaoLicitacaoDocumentos->inclusao();
                        }
                    }

                    if (is_array(Sessao::read('arMembros')) && count(Sessao::read('arMembros')) > 0) {
                        $obTLicitacaoComissaoLicitacaoMembros = new TLicitacaoComissaoLicitacaoMembros;

                        $obTLicitacaoComissaoLicitacaoMembros->setDado('exercicio'      , Sessao::getExercicio());
                        $obTLicitacaoComissaoLicitacaoMembros->setDado('cod_entidade'   , $_REQUEST['inCodEntidade']);
                        $obTLicitacaoComissaoLicitacaoMembros->setDado('cod_modalidade' , $_REQUEST['inCodModalidade']);
                        $obTLicitacaoComissaoLicitacaoMembros->setDado('cod_licitacao'  , $obTLicitacaoLicitacao->getDado('cod_licitacao'));

                        foreach (Sessao::read('arMembros') as $value) {
                            $obTLicitacaoComissaoLicitacaoMembros->setDado('cod_comissao'   , $value['cod_comissao']);
                            $obTLicitacaoComissaoLicitacaoMembros->setDado('numcgm'         , $value['numcgm']);
                            $obTLicitacaoComissaoLicitacaoMembros->setDado('cod_norma'      , $value['cod_norma']);
                            $obTLicitacaoComissaoLicitacaoMembros->inclusao();
                        }
                    }

                    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao."","Processo Licitatório: ".$obTLicitacaoLicitacao->getDado('cod_licitacao'),"incluir","aviso", Sessao::getId(), "../");

                    if ( ($_REQUEST['inCodTipoObjeto'] == 1) || ($_REQUEST['inCodTipoObjeto'] == 2) ) {
                        $mensagemAlertaLimite = verficaCompraLimiteEspecieModalidade($_REQUEST['inCodModalidade'],$soma, $_REQUEST['inCodTipoObjeto'],$obTLicitacaoLicitacao->getDado('cod_licitacao'));

                        if ($mensagemAlertaLimite != "") {
                            SistemaLegado::exibeAviso($mensagemAlertaLimite,"aviso","aviso");
                        }
                    }
                }
            }
        }
        if ($stMensagem) {
            SistemaLegado::exibeAviso( $stMensagem, "n_incluir", "erro" );
        }
    break;

    case "alterar":
        Sessao::write('acao', 1568);
        if ($stMensagem == "") {
            if($_REQUEST['hdnMapaCompra']){
                $arMapa = explode('/',$_REQUEST['hdnMapaCompra'] );
            }else{
                $arMapa = explode('/',$_REQUEST['stMapaCompras'] );
            }

            $entidade = explode("-",$_REQUEST['hdnEntidade']);
            $_REQUEST['inCodEntidade'] = trim($entidade[0]);
            $modalidade = explode('-',$_REQUEST['hdnModalidade']);
            $_REQUEST['inCodModalidade'] = $modalidade[0];

            // Atualiza data da licitação caso a mesma seja válida...
            $dtLicitacao     = $_REQUEST['stDtLicitacao'];
            $inCodLicitacao  = $_REQUEST['hdnCodLicitacao'];
            $inCodModalidade = $_REQUEST['inCodModalidade'];
            $inCodEntidade   = $_REQUEST['inCodEntidade'];
            $stExercicio     = $_REQUEST['hdnExercicioLicitacao'];

            $stMensagem = verificaLicitacaoAnulada($inCodLicitacao, $inCodModalidade, $inCodEntidade, $stExercicio, 'alterar');

            // Checa se data alterada eh valida
            $stMensagem = checkIsValidDataLicitacao($dtLicitacao);

            if (empty($stMensagem)) {
                $stMensagem = verificaMapaAnulado($arMapa[0] , $arMapa[1], 'alterar');
            }

            if (empty($stMensagem)) {
                $stMensagem = verificaUtilizacaoMapa($arMapa[0], $arMapa[1], 'alterar');
            }

            if ( empty($stMensagem) && $_REQUEST['inMontaCodUnidadeM'] == '') {
                $stMensagem = "O subcampo unidade da Unidade Executora é obrigatório.";      
            }

            //Só entra no if se município pertencer ao estado de MG
            $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf');
            if ($request->get('inCodTipoObjeto') == 2 && $request->get('inCodRegime') == '' && $inCodUF == 11) {
                $stMensagem = "O Regime de execução de Obras é obrigatório para o Tipo de Objeto selecionado.";
            }

            // No processo de edicao, nada do mapa eh alterado, logo nao precisamos revalidar a informacao
//            if (empty($stMensagem) && $boReservaRigida) {
//                $obTComprasMapaSolicitacao = new TComprasMapaSolicitacao();
//                $obTComprasMapaItemReserva = new TComprasMapaItemReserva();
//
//                $obTComprasMapaSolicitacao->setDado('cod_mapa'  , $arMapa[0]);
//                $obTComprasMapaSolicitacao->setDado('exercicio' , $arMapa[1]);
//                $obTComprasMapaSolicitacao->recuperaPorChave( $rsSolicitacaoMapa );
//
//                while (!$rsSolicitacaoMapa->eof()) {
//                    $obTComprasMapaItemReserva->setDado('cod_solicitacao'   , $rsSolicitacaoMapa->getCampo('cod_solicitacao'));
//                    $obTComprasMapaItemReserva->setDado('cod_entidade'      , $rsSolicitacaoMapa->getCampo('cod_entidade'));
//                    $obTComprasMapaItemReserva->setDado('exercicio'         , $rsSolicitacaoMapa->getCampo('exercicio'));
//                    $obTComprasMapaItemReserva->setDado('cod_mapa'          , $arMapa[0]);
//                    $obTComprasMapaItemReserva->recuperaMapaItemReserva( $rsMapaItemReserva );
//                    if ( $rsMapaItemReserva->getNumLinhas() > 0 ) {
//                        $stMensagem = "Todos itens do mapa (".$arMapa[0].'/'.$arMapa[1].") devem estar com reserva de saldo. Efetue a manutenção das dotações no mapa.";
//                        break;
//                    }
//                    $rsSolicitacaoMapa->proximo();
//                }
//            }

            if (empty($stMensagem)) {
                $obTLicitacaoComissaoLicitacao        = new TLicitacaoComissaoLicitacao;
                $obTLicitacaoComissaoLicitacaoMembros = new TLicitacaoComissaoLicitacaoMembros;
                $obTLicitacaoMembroAdicional          = new TLicitacaoMembroAdicional;

                $arDocumentos = Sessao::read('arDocumentos');

                # Membros da Comissão Permanente.
                $arMembros = Sessao::read('arMembros');

                # Membros Adicionais.
                $arMembrosAdicionais = Sessao::read('arMembro');

                # Exclusão de todos os membros da comissão da licitação para posteriormente serem incluidos corretamente.
                $obTLicitacaoComissaoLicitacaoMembros->setDado('exercicio'      , $_REQUEST['hdnExercicioLicitacao'] );
                $obTLicitacaoComissaoLicitacaoMembros->setDado('cod_entidade'   , $_REQUEST['inCodEntidade']         );
                $obTLicitacaoComissaoLicitacaoMembros->setDado('cod_modalidade' , $_REQUEST['inCodModalidade']       );
                $obTLicitacaoComissaoLicitacaoMembros->setDado('cod_licitacao'  , $_REQUEST['hdnCodLicitacao']       );
                $obTLicitacaoComissaoLicitacaoMembros->exclusao();

                # Exclusão de todos os códigos de comissões da licitação para posteriormente serem incluidos corretamente.
                $obTLicitacaoComissaoLicitacao->setDado('exercicio'      , $_REQUEST['hdnExercicioLicitacao'] );
                $obTLicitacaoComissaoLicitacao->setDado('cod_entidade'   , $_REQUEST['inCodEntidade'  ]       );
                $obTLicitacaoComissaoLicitacao->setDado('cod_licitacao'  , $_REQUEST['hdnCodLicitacao']       );
                $obTLicitacaoComissaoLicitacao->setDado('cod_modalidade' , $_REQUEST['inCodModalidade']       );
                $obTLicitacaoComissaoLicitacao->exclusao();

                # Inclusão da Comissão Permanente.
                if ($_REQUEST['inCodComissao']) {
                    $obTLicitacaoComissaoLicitacao->setDado('cod_comissao', $_REQUEST['inCodComissao']);
                    $obTLicitacaoComissaoLicitacao->inclusao();
                }

                # Inclusão da Comissão de Apoio (se existir).
                if ($_REQUEST['inCodComissaoApoio']) {
                    $obTLicitacaoComissaoLicitacao->setDado('cod_comissao', $_REQUEST['inCodComissaoApoio']);
                    $obTLicitacaoComissaoLicitacao->inclusao();
                }

                # Inclusão de todos os membros da comissão da licitação atualizados.
                foreach ($arMembros as $key => $value) {
                    $obTLicitacaoComissaoLicitacaoMembros->setDado('numcgm'       , $value['numcgm']);
                    $obTLicitacaoComissaoLicitacaoMembros->setDado('cod_norma'    , $value['cod_norma']);
                    $obTLicitacaoComissaoLicitacaoMembros->setDado('cod_comissao' , $value['cod_comissao']);
                    $obTLicitacaoComissaoLicitacaoMembros->inclusao();
                }

                # Controle de Membros Adicionais                
                if (is_array($arMembrosAdicionais) && count($arMembrosAdicionais) > 0) {

                    # Exclusão de todos os Membros Adicionais para posteriormente serem inseridos corretamente.
                    $obTLicitacaoMembroAdicional->setDado('exercicio'      , $_REQUEST['hdnExercicioLicitacao'] );
                    $obTLicitacaoMembroAdicional->setDado('cod_modalidade' , $_REQUEST['inCodModalidade']       );
                    $obTLicitacaoMembroAdicional->setDado('cod_entidade'   , $_REQUEST['inCodEntidade']         );
                    $obTLicitacaoMembroAdicional->setDado('cod_licitacao'  , $_REQUEST['hdnCodLicitacao']       );
                    $obTLicitacaoMembroAdicional->exclusao();

                    foreach ($arMembrosAdicionais as $key => $value) {
                        # Inclusão de todos os Membros Adicionais atualizados.
                        $obTLicitacaoMembroAdicional->setDado('numcgm'          , $value['num_cgm'] );
                        $obTLicitacaoMembroAdicional->setDado('cargo'           , $value['cargo_membro'] );
                        $obTLicitacaoMembroAdicional->setDado('natureza_cargo'  , $value['cod_natureza_cargo'] );                         
                        $obTLicitacaoMembroAdicional->inclusao();
                    }
                }

                $arItensChave = array();

                $obTLicitacaoLicitacaoDocumentos = new TLicitacaoLicitacaoDocumentos();
                $obTLicitacaoLicitacaoDocumentos->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
                $obTLicitacaoLicitacaoDocumentos->setDado('exercicio',Sessao::getExercicio());
                $obTLicitacaoLicitacaoDocumentos->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
                $obTLicitacaoLicitacaoDocumentos->setDado('cod_licitacao',$_REQUEST['hdnCodLicitacao']);
                $obTLicitacaoLicitacaoDocumentos->recuperaDocumentosLicitacao($rsDocumentos);

                $countDoc = 0;
                while (!$rsDocumentos->eof()) {
                    $stKeyDb = trim($rsDocumentos->getCampo('cod_entidade'))."-".
                               trim($rsDocumentos->getCampo('cod_modalidade'))."-".
                               trim($rsDocumentos->getCampo('cod_licitacao'))."-".
                               trim($rsDocumentos->getCampo('cod_documento'))."-".
                               trim($rsDocumentos->getCampo('exercicio'));
                    $arItensChave[$stKeyDb] = true;
                    $rsDocumentos->proximo();
                    $countDoc++;
                }

                $arDocumentosExcluidos = Sessao::read("arDocumentosExcluidos");

                $obTLicitacaoParticipanteDocumentos = new TLicitacaoParticipanteDocumentos();

                $inQuantidadeExcluidos = Sessao::read("inQuantidadeDocumentosExcluidos");

                if (((($countDoc - $inQuantidadeExcluidos) > 0)) && !($countDoc == 0 && $inQuantidadeExcluidos == 0)) {

                    if (($countDoc - $inQuantidadeExcluidos) != 0) {
                        foreach ($arDocumentosExcluidos as $key => $value) {
                            $obTLicitacaoParticipanteDocumentos->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
                            $obTLicitacaoParticipanteDocumentos->setDado('exercicio'      ,Sessao::getExercicio());
                            $obTLicitacaoParticipanteDocumentos->setDado('cod_entidade' ,$_REQUEST['inCodEntidade']);
                            $obTLicitacaoParticipanteDocumentos->setDado('cod_licitacao', $_REQUEST['hdnCodLicitacao']);
                            $obTLicitacaoParticipanteDocumentos->setDado('num_documento', $value['cod_documento']);

                            $obTLicitacaoLicitacaoDocumentos->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
                            $obTLicitacaoLicitacaoDocumentos->setDado('exercicio'      ,Sessao::getExercicio());
                            $obTLicitacaoLicitacaoDocumentos->setDado('cod_entidade' ,$_REQUEST['inCodEntidade']);
                            $obTLicitacaoLicitacaoDocumentos->setDado('cod_licitacao', $_REQUEST['hdnCodLicitacao']);
                            $obTLicitacaoLicitacaoDocumentos->setDado('cod_documento', $value['cod_documento']);

                            $obTLicitacaoParticipanteDocumentos->exclusao();
                            $obTLicitacaoLicitacaoDocumentos->exclusao();
                        }
                    } else {
                        SistemaLegado::exibeAviso( "Ao menos um Documento deverá ser utilizado.", "n_incluir", "erro" );

                        break;
                    }
                }

                if ( count( $arDocumentos > 0 ) ) {

                    foreach ($arDocumentos as $key => $value) {
                        $stKeyNew = trim($_REQUEST['inCodEntidade'])."-".trim($_REQUEST['inCodModalidade'])."-".trim($_REQUEST['hdnCodLicitacao'])."-".trim($value['cod_documento'])."-".trim(Sessao::getExercicio());
                        $obTLicitacaoLicitacaoDocumentos->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
                        $obTLicitacaoLicitacaoDocumentos->setDado('exercicio'      ,Sessao::getExercicio());
                        $obTLicitacaoLicitacaoDocumentos->setDado('cod_entidade' ,$_REQUEST['inCodEntidade']);
                        $obTLicitacaoLicitacaoDocumentos->setDado('cod_licitacao', $_REQUEST['hdnCodLicitacao']);
                        $obTLicitacaoLicitacaoDocumentos->setDado('cod_documento', $value['cod_documento']);

                        if ( !isset( $arItensChave[$stKeyNew] ) ) {
                            $obTLicitacaoLicitacaoDocumentos->inclusao();
                        } else {
                           unset( $arItensChave[$stKeyNew] );
                        }
                    }
                }

                $dtLicitacao = $_REQUEST['stDtLicitacao'];

                $arStUnidadeOrcamentaria = explode('.',$request->get('stUnidadeOrcamentaria'));

                $arProcesso = explode('/',$_REQUEST['stChaveProcesso'] );
                $inCodObjeto = (isset($_REQUEST['stObjeto'])) ? $_REQUEST['stObjeto'] : $_REQUEST['hdnObjeto'];
                
                $obTLicitacaoLicitacao->setDado('cod_licitacao',$_REQUEST['hdnCodLicitacao']);
                $obTLicitacaoLicitacao->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
                $obTLicitacaoLicitacao->setDado('cod_entidade',$_REQUEST['inCodEntidade'] );
                $obTLicitacaoLicitacao->setDado('exercicio',Sessao::getExercicio() );
                $obTLicitacaoLicitacao->setDado('cod_tipo_objeto',$_REQUEST['inCodTipoObjeto'] );
                $obTLicitacaoLicitacao->setDado('cod_objeto',$inCodObjeto );
                $obTLicitacaoLicitacao->setDado('cod_criterio',$_REQUEST['inCodCriterio'] );
                $obTLicitacaoLicitacao->setDado('cod_tipo_licitacao',$_REQUEST['inCodTipoCotacao'] );
                $obTLicitacaoLicitacao->setDado('cod_mapa',$arMapa[0]);
                $obTLicitacaoLicitacao->setDado('exercicio_mapa',"".$arMapa[1]."");
                $obTLicitacaoLicitacao->setDado('cod_processo',$arProcesso[0]);
                $obTLicitacaoLicitacao->setDado('exercicio_processo',"".$arProcesso[1]."");
                $obTLicitacaoLicitacao->setDado('vl_cotado',$_REQUEST['stValorReferencial']  );
                $obTLicitacaoLicitacao->setDado('timestamp', substr($dtLicitacao, 6, 4).'-'.substr($dtLicitacao, 3, 2).'-'.substr($dtLicitacao,0,2).date(' H:i:s.ms'));
                $obTLicitacaoLicitacao->setDado('num_orgao'  , $arStUnidadeOrcamentaria[0]);
                $obTLicitacaoLicitacao->setDado('num_unidade', $arStUnidadeOrcamentaria[1]);
                $obTLicitacaoLicitacao->setDado('cod_regime',$_REQUEST['inCodRegime'] != '' ? $_REQUEST['inCodRegime'] : 'null');

                if (($_REQUEST['inCodModalidade'] == 8 ||
                     $_REQUEST['inCodModalidade'] == 9 ||
                     $_REQUEST['inCodModalidade'] == 10
                    ) && $_REQUEST['boRegistroModalidade'] != '' ) {                
                     $obTLicitacaoLicitacao->setDado('tipo_chamada_publica', isset($_REQUEST['boRegistroModalidade']) ? $_REQUEST['boRegistroModalidade'] : 0 );
                } else {
                     $obTLicitacaoLicitacao->setDado('tipo_chamada_publica', 0);
                }
                 
                if (($_REQUEST['inCodModalidade'] == 3 ||
                     $_REQUEST['inCodModalidade'] == 6 ||
                     $_REQUEST['inCodModalidade'] == 7
                    ) && $_REQUEST['boHdnTipoRegistroPrecos'] == 1) {
                     $obTLicitacaoLicitacao->setDado('registro_precos', true);
                } else {
                     $obTLicitacaoLicitacao->setDado('registro_precos', false);
                }
                
                $obTLicitacaoLicitacao->alteracao();
 
                $obTMapaModalidade = new TComprasMapaModalidade();

                $obTMapaModalidade->setDado('cod_mapa'  ,$arMapa[0]);
                $obTMapaModalidade->setDado('exercicio' ,$arMapa[1]);
                $obTMapaModalidade->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
                $obTMapaModalidade->recuperaTodos( $rsMapaModalidade );

                if ( $rsMapaModalidade->getNumLinhas() > 0 ) {
                     $obTMapaModalidade->alteracao();
                } else {
                     $obTMapaModalidade->inclusao();
                }
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Processo Licitatório - ".$_REQUEST['hdnCodLicitacao']."","alterar","alterar_n", Sessao::getId(), "../");
                Sessao::remove("inQuantidadeDocumentosExcluidos");
            }
        }
        if ($stMensagem) {
            SistemaLegado::exibeAviso( $stMensagem, "n_incluir", "erro" );
            Sessao::remove("inQuantidadeDocumentosExcluidos");
        }
    break;

    case "anular":
        list($inCodMapa, $stExercicioMapa) = explode("/", $_REQUEST['hdnMapaCompra']);

        if (!empty($inCodMapa) && !empty($stExercicioMapa)) {

            $obTComprasMapa = new TComprasMapa;
            $obTComprasMapa->setDado('cod_mapa',$inCodMapa);
            $obTComprasMapa->setDado('exercicio_mapa',$stExercicioMapa);
            $obTComprasMapa->recuperaMapaCotacaoValida( $rsCotacao );

            list ($inCodEntidade, $stNomEntidade) = explode(' - ',$_REQUEST['hdnEntidade']);

            if ( $rsCotacao->getNumLinhas() > 0 ) {
                $obTComprasJulgamento = new TComprasJulgamento;
                $obTComprasJulgamento->setDado ( 'cod_cotacao' , $rsCotacao->getCampo('cod_cotacao')      );
                $obTComprasJulgamento->setDado ( 'exercicio'   , $rsCotacao->getCampo('exercicio_cotacao'));
                $obTComprasJulgamento->recuperaPorChave( $rsJulgamento );

                $stFiltro = " WHERE cod_cotacao = "      .$rsCotacao->getCampo('cod_cotacao'      )." ";
                $stFiltro.= " AND exercicio_julgamento ='".$rsCotacao->getCampo('exercicio_cotacao')."' ";

                $obTEmpenhoItemPreEmpenhoJulgamento = new TEmpenhoItemPreEmpenhoJulgamento();
                $obTEmpenhoItemPreEmpenhoJulgamento->recuperaTodos( $rsItem, $stFiltro);

                if (($rsJulgamento->getNumLinhas() > 0) &&
                    ($rsItem->getNumLinhas() < 1)){

                    $obTComprasJulgamentoItem = new TComprasJulgamentoItem;
                    $obTComprasJulgamentoItem->setComplementoChave('exercicio,cod_cotacao');
                    $obTComprasJulgamentoItem->setDado ( 'cod_cotacao' , $rsJulgamento->getCampo('cod_cotacao'));
                    $obTComprasJulgamentoItem->setDado ( 'exercicio'   , $rsJulgamento->getCampo('exercicio')  );
                    $obTComprasJulgamentoItem->exclusao();

                    $obTComprasJulgamento->exclusao();

                    $stMotivo = "Anulação da Licitação ".$_REQUEST['hdnCodLicitacao'].", do Mapa de Compras ".$_REQUEST['hdnMapaCompra'];

                    $obTComprasCotacaoAnulada = new TComprasCotacaoAnulada;
                    $obTComprasCotacaoAnulada->setDado('exercicio',   $rsCotacao->getCampo('exercicio_cotacao'));
                    $obTComprasCotacaoAnulada->setDado('cod_cotacao', $rsCotacao->getCampo('cod_cotacao')      );
                    $obTComprasCotacaoAnulada->setDado("motivo", $stMotivo);
                    $obTComprasCotacaoAnulada->inclusao();
                }

                if ($rsItem->getNumLinhas() > 0) {
                    $arItensAnulados = array();
                    foreach ($rsItem->arElementos as $item) {
                            $arItensAnulados[$item['cod_pre_empenho']] = $item;
                    }

                    $arAutorizacaoAnuladaPreEmpenho = array();
                    $contaAutorizacaoAnuladaPreEmpenho = array();

                    foreach ($arItensAnulados as $cod_pre_empenho => $item) {

                        // verifica se a autorizacao, do empenho do item,  foi anulda
                        $stFiltro = " WHERE ae.cod_pre_empenho = ". $cod_pre_empenho         ." \n";
                        $stFiltro.= "   AND ae.exercicio       = '". $item['exercicio']      ."' \n";
                        $stFiltro.= "   AND ae.cod_entidade    = ". $inCodEntidade           ." \n";

                        $obTEmpenhoAutorizacaoAnulada = new TEmpenhoAutorizacaoAnulada();
                        $obTEmpenhoAutorizacaoAnulada->recuperaAutorizacaoAnuladaEmpenho( $rsAutorizacaoAnulada, $stFiltro );

                        $arAutorizacaoAnuladaPreEmpenho[$cod_pre_empenho] = $rsAutorizacaoAnulada;
                        if (count($arAutorizacaoAnuladaPreEmpenho[$cod_pre_empenho]->arElementos) > 0) {
                            $contaAutorizacaoAnuladaPreEmpenho[$cod_pre_empenho] = $cod_pre_empenho;
                        }
                    }

                    if (count($contaAutorizacaoAnuladaPreEmpenho) < count($arItensAnulados)) {
                        foreach ($arItensAnulados as $codPreEmpenho => $valor) {
                            if (!in_array($codPreEmpenho,array_keys($contaAutorizacaoAnuladaPreEmpenho))) {
                                $pre_empenhos .= "$codPreEmpenho,";
                            }
                        }

                        $pre_empenhos = substr($pre_empenhos, 0, -1) ;

                        $stFiltro   = " WHERE cod_pre_empenho in ($pre_empenhos) ";
                        $obTAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho();
                        $obTAutorizacaoEmpenho->recuperaTodos($rsTAutorizacaoEmpenho, $stFiltro);

                        $cod_autorizacao = "";
                        $inContaAutorizacoes = 0;

                        foreach ($rsTAutorizacaoEmpenho->arElementos as $arAutorizacao) {
                            $cod_autorizacao .= $arAutorizacao['cod_autorizacao']."/";
                            $cod_autorizacao .= $arAutorizacao['exercicio'].",";
                            $inContaAutorizacoes++;
                         }

                         $cod_autorizacao = substr($cod_autorizacao, 0, -1) ;
                         $arEntidade = explode(' - ',$_REQUEST['hdnEntidade']);

                         if ($inContaAutorizacoes > 1) {
                            $stMensagem = "As Autorizações (".$cod_autorizacao.") ";
                            $stMensagem.= ", da entidade ".$arEntidade[1].", devem ser anuladas ";
                            $stMensagem.= "antes de anular este Processo Licitatório.";
                         } else {
                            $stMensagem = "A Autorização (".$cod_autorizacao.") ";
                            $stMensagem.= ", da entidade ".$arEntidade[1].", deve ser anulada ";
                            $stMensagem.= "antes de anular este Processo Licitatório.";

                         }

                         SistemaLegado::exibeAviso( $stMensagem, "aviso", "aviso" );
                         $boAnularAutorizacao = true;
                    } else {
                        $stMotivo = "Anulação da Licitação ".$_REQUEST['hdnCodLicitacao'].", do Mapa de Compras ".$_REQUEST['hdnMapaCompra'];

                        // inclui os itens em compras.mapa_item_anulacao        
                        Sessao::write('stAcaoTela','anularProcessoLicitatório');
                        $obTComprasMapaItem = new TComprasMapaItem();

                        $stFiltro  = "  AND mapa_item.cod_mapa  = ". $rsCotacao->getCampo('cod_mapa' );
                        $stFiltro .= "  AND mapa_item.exercicio = '". $rsCotacao->getCampo('exercicio_mapa')."'";

                        $obTComprasMapaItem->recuperaItensMapa($rsComprasMapaItem, $stFiltro);

                        $obTComprasMapaItemAnulacao = new TComprasMapaItemAnulacao;

                        $obTComprasMapaSolicitacao = new TComprasMapaSolicitacao;

                        $arCodSolicitacao = array();
                        while (!$rsComprasMapaItem->eof()) {
                            $arCodSolicitacao[$rsComprasMapaItem->getCampo('cod_solicitacao')] = $rsComprasMapaItem->getCampo('cod_solicitacao');
                            $rsComprasMapaItem->proximo();
                        }

                        if (count($arCodSolicitacao) > 1) {
                            $stFiltro = " WHERE cod_solicitacao IN (".implode(',',$arCodSolicitacao).")";
                        } else {
                            $stFiltro = " WHERE cod_solicitacao = ".$rsComprasMapaItem->arElementos[0]['cod_solicitacao'];
                        }

                        $obTComprasMapaSolicitacao->recuperaTodos($rsComprasMapaSolicitacao, $stFiltro);

                        $obTComprasMapaSolicitacaoAnulacao = new TComprasMapaSolicitacaoAnulacao;

                        foreach ($arCodSolicitacao as $codSolicitacao => $valor) {
                            $obTComprasMapaSolicitacaoAnulacao->setDado('exercicio',            $rsComprasMapaSolicitacao->getCampo('exercicio'));
                            $obTComprasMapaSolicitacaoAnulacao->setDado('cod_mapa',             $rsComprasMapaSolicitacao->getCampo('cod_mapa'));
                            $obTComprasMapaSolicitacaoAnulacao->setDado('exercicio_solicitacao',$rsComprasMapaSolicitacao->getCampo('exercicio_solicitacao'));
                            $obTComprasMapaSolicitacaoAnulacao->setDado('cod_entidade',         $rsComprasMapaSolicitacao->getCampo('cod_entidade'));
                            $obTComprasMapaSolicitacaoAnulacao->setDado('cod_solicitacao',      $codSolicitacao);
                            $obTComprasMapaSolicitacaoAnulacao->setDado('timestamp',            $rsComprasMapaSolicitacao->getCampo('timestamp'));
                            $obTComprasMapaSolicitacaoAnulacao->setDado('motivo',               $_REQUEST['stJustificativa']);
                            $obTComprasMapaSolicitacaoAnulacao->inclusao();
                        }

                        while (!$rsComprasMapaItem->eof() ) {
                            $obTComprasMapaItemAnulacao->setDado ( 'exercicio'    ,         $rsComprasMapaItem->getCampo('exercicio'));
                            $obTComprasMapaItemAnulacao->setDado ( 'cod_mapa'     ,         $rsCotacao->getCampo('cod_mapa' ));
                            $obTComprasMapaItemAnulacao->setDado ( 'exercicio_solicitacao', $rsComprasMapaItem->getCampo('exercicio_solicitacao'));
                            $obTComprasMapaItemAnulacao->setDado ( 'cod_entidade'  ,        $rsComprasMapaItem->getCampo('cod_entidade'));
                            $obTComprasMapaItemAnulacao->setDado ( 'cod_solicitacao',       $rsComprasMapaItem->getCampo('cod_solicitacao'));
                            $obTComprasMapaItemAnulacao->setDado ( 'cod_centro'    ,        $rsComprasMapaItem->getCampo('cod_centro'));
                            $obTComprasMapaItemAnulacao->setDado ( 'cod_item'      ,        $rsComprasMapaItem->getCampo('cod_item'));
                            $obTComprasMapaItemAnulacao->setDado ( 'lote'          ,        $rsComprasMapaItem->getCampo('lote'));
                            $obTComprasMapaItemAnulacao->setDado ( 'quantidade'    ,        $rsComprasMapaItem->getCampo('quantidade_mapa'));
                            $obTComprasMapaItemAnulacao->setDado ( 'vl_total'      ,        $rsComprasMapaItem->getCampo('valor_total_mapa'));
                            $obTComprasMapaItemAnulacao->setDado ( 'timestamp'     ,        $rsComprasMapaSolicitacao->getCampo('timestamp'));
                            $obTComprasMapaItemAnulacao->inclusao();
                            $rsComprasMapaItem->proximo();
                        }

                           $obTComprasCotacaoAnulada = new TComprasCotacaoAnulada;
                        $obTComprasCotacaoAnulada->setDado('exercicio', $rsCotacao->getCampo('exercicio_cotacao'));
                        $obTComprasCotacaoAnulada->setDado('cod_cotacao',$rsCotacao->getCampo('cod_cotacao'));
                        $obTComprasCotacaoAnulada->setDado("motivo", $stMotivo);
                        $obTComprasCotacaoAnulada->inclusao();
                    }
                }
            }

              if (!$boAnularAutorizacao) {
                $obTLicitacaoLicitacaoAnula = new TLicitacaoLicitacaoAnulada();
                
                if($_REQUEST['boMotivoAnulacao'] == "boDeserta"){
                    $boDeserta = true;
                    $boFracassada = false;
                }else if($_REQUEST['boMotivoAnulacao'] == "boFracassada"){
                    $boDeserta = false;
                    $boFracassada = true;
                }else{
                    $boDeserta = false;
                    $boFracassada = false;
                }
                                
                $codModalidade = explode(' ',$_REQUEST['hdnModalidade']);
                $codEntidade = explode(' ',$_REQUEST['hdnEntidade']);
                $obTLicitacaoLicitacaoAnula->setDado('cod_licitacao',$_REQUEST['hdnCodLicitacao']);
                $obTLicitacaoLicitacaoAnula->setDado('cod_modalidade',$codModalidade[0]);
                $obTLicitacaoLicitacaoAnula->setDado('exercicio',Sessao::getExercicio());
                $obTLicitacaoLicitacaoAnula->setDado('justificativa',$_REQUEST['stJustificativa']);
                $obTLicitacaoLicitacaoAnula->setDado('deserta', $boDeserta);
                $obTLicitacaoLicitacaoAnula->setDado('fracassada',$boFracassada);
                
                $obTLicitacaoLicitacaoAnula->setDado('cod_entidade',$codEntidade[0]);
                $obTLicitacaoLicitacaoAnula->inclusao();
            }
        }

        if (!$boAnularAutorizacao) {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."","Processo Licitatório - ".$_REQUEST['hdnCodLicitacao']."/".Sessao::getExercicio()." anulado com sucesso!","aviso","aviso", Sessao::getId(), "../");
        }

    break;

}

function verificaLicitacaoAnulada($inCodLicitacao, $inCodModalidade, $inCodEntidade, $stExercicio, $stAcao)
{
    if ($stAcao == "alterar") {
        $obTLicitacaoLicitacao = new TLicitacaoLicitacao;
        $stFiltro  = " WHERE                                                                                 \n";
        $stFiltro .= "       EXISTS( SELECT 1                                                                \n";
        $stFiltro .= "                  FROM licitacao.licitacao_anulada                                     \n";
        $stFiltro .= "                 WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao      \n";
        $stFiltro .= "                   AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade     \n";
        $stFiltro .= "                   AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade       \n";
        $stFiltro .= "                   AND licitacao_anulada.exercicio      = licitacao.exercicio    )     \n";
        $stFiltro .= "   AND licitacao.cod_licitacao  = ".$inCodLicitacao."                                  \n";
        $stFiltro .= "   AND licitacao.cod_modalidade = ".$inCodModalidade."                                 \n";
        $stFiltro .= "   AND licitacao.cod_entidade   = ".$inCodEntidade."                                   \n";
        $stFiltro .= "   AND licitacao.exercicio      = '".$stExercicio."'                                   \n";
        $obTLicitacaoLicitacao->recuperaTodos($rsRecordSet, $stFiltro);

        if ($rsRecordSet->getNumLinhas() > 0) {
            $stLicitacao = $rsRecordSet->getCampo('cod_licitacao')."/".$rsRecordSet->getCampo('exercicio');
            $stErro = "A licitação (".$stLicitacao.") já está anulada.";
        }
    }

    return $stErro;
}

function verificaMapaAnulado($inCodMapa, $stExercicioMapa, $stAcao)
{
    $stMensagem = '';
    if ($stAcao == 'alterar' || $stAcao == 'incluir') {
        $obTComprasMapa = new TComprasMapa;
        $obTComprasMapa->setDado('exercicio', $stExercicioMapa);
        $obTComprasMapa->setDado('cod_mapa' , $inCodMapa);
        $obTComprasMapa->verificaMapaAnulacoes($rsRecordSet);
        if ($rsRecordSet->getNumLinhas() > 0) {
            $stMensagem = "Mapa de Compras (".$inCodMapa."/".$stExercicioMapa.") já está anulado.";
        }
    }

    return $stMensagem;
}

function verificaUtilizacaoMapa($inCodMapa, $stExercicioMapa, $stAcao)
{
    $stMensagem = '';
    if ($stAcao == 'alterar' || $stAcao == 'incluir') {
        $obTLicitacaoLicitacao = new TLicitacaoLicitacao;
        $stFiltro  = " WHERE cod_mapa       =  ".$inCodMapa."                                                   \n";
        $stFiltro .= "   AND exercicio_mapa = '".$stExercicioMapa."'                                            \n";
        $stFiltro .= "   AND NOT EXISTS ( SELECT 1                                                              \n";
        $stFiltro .= "                      FROM licitacao.licitacao_anulada                                    \n";
        $stFiltro .= "                     WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao     \n";
        $stFiltro .= "                       AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade    \n";
        $stFiltro .= "                       AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade      \n";
        $stFiltro .= "                       AND licitacao_anulada.exercicio      = licitacao.exercicio      )  \n";
        if ($stAcao != 'incluir') {
            $stFiltro .= "  AND licitacao.exercicio      <> '".$_REQUEST['hdnExercicioLicitacao']."'                \n";
            $stFiltro .= "  AND licitacao.cod_entidade   <> ".$_REQUEST['inCodEntidade']."                          \n";
            $stFiltro .= "  AND licitacao.cod_modalidade <> ".$_REQUEST['inCodModalidade']."                        \n";
            $stFiltro .= "  AND licitacao.cod_licitacao  <> ".$_REQUEST['hdnCodLicitacao']."                        \n";
        }
        $obTLicitacaoLicitacao->recuperaTodos($rsRecordSet, $stFiltro);

        if ($rsRecordSet->getNumLinhas() > 0) {
            $inCodMapa = $rsRecordSet->getCampo('cod_mapa');
            $stExercicioMapa = $rsRecordSet->getCampo('exercicio_mapa');
            $inCodLicitacao = $rsRecordSet->getCampo('cod_licitacao');
            $stExercicioLicitacao = $rsRecordSet->getCampo('exercicio');
            $stMensagem  = "Mapa de Compras (".$inCodMapa."/".$stExercicioMapa.") já está sendo utilizado ";
            $stMensagem .= "pela licitação (".$inCodLicitacao."/".$stExercicioLicitacao.").";
        }
        if ($stMensagem == '') {
            $obTComprasCompraDireta = new TComprasCompraDireta;
            $obTComprasCompraDireta->setDado('cod_mapa'       , $inCodMapa);
            $obTComprasCompraDireta->setDado('exercicio_mapa' , $stExercicioMapa);
            $obTComprasCompraDireta->recuperaCompraDiretaPorMapa($rsRecordSet);
            if ($rsRecordSet->getNumLinhas() > 0) {
                $stMensagem = "Mapa de compras (".$inCodMapa."/".$stExercicioMapa.") já utilizado pela compra direta.";
            }
        }
    }

    return $stMensagem;
}

function verficaCompraLimiteEspecieModalidade($codModalidade,$soma, $especie, $licitacao)
{
    $mensagem = "";

    switch ($codModalidade) {
        //convite
        case 1:
            if ($especie == 1) {
                if ( ($soma >= 8000) && ($soma <= 80000) ) {
                    $mensagem = "";
                } else {
                    if ($soma > 80000) {
                        $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade! (Processo Licitatório: ".$licitacao." )";
                    }
                }
            } elseif ($especie == 2) {
                if ( ($soma >= 15000) && ($soma <= 150000) ) {
                    $mensagem = "";
                } else {
                    if ($soma > 150000) {
                        $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade! (Processo Licitatório: ".$licitacao." )";
                    }
                }
            }
        break;

        //tomada de preços
        case 2:
            if ($especie == 1) {
                if ( ($soma >= 80000) && ($soma <= 650000) ) {
                    $mensagem = "";
                } else {
                    if ($soma > 650000) {
                        $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade! (Processo Licitatório: ".$licitacao." )";
                    }
                }
            } elseif ($especie == 2) {
                if ( ($soma >= 150000) && ($soma <= 1500000) ) {
                   $mensagem = "";
                } else {
                    if ($soma > 1500000) {
                        $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade! (Processo Licitatório: ".$licitacao." )";
                    }
                }
            }
        break;

        //concorrência
        case 3:
            // Não há limites maximos, apenas minimos ( não necessário informar alertas ao usuário )!
            $mensagem = "";
        break;

        //dispença de licitação
        case 8:
            if ($especie == 1) {
                if ($soma < 8000) {
                    $mensagem = "";
                } else {
                    $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade! (Processo Licitatório: ".$licitacao." )";
                }
            } elseif ($especie == 2) {
                if ($soma < 15000) {
                    $mensagem = "";
                } else {
                    $mensagem = "A compra efetuada ultrapassa o limite máximo para essa Espécie e Modalidade! (Processo Licitatório: ".$licitacao." )";
                }
            }
        break;
    }

    return $mensagem;
}

Sessao::encerraExcecao();

function validaVigenciaComissao($inCodVigencia)
{
    $mensagemErroVigenciaValida = "";
    $obLicitacaoComissao   = new TLicitacaoComissao();
    $rsComissaoVigencia    = new Recordset;

    $stFiltro = " WHERE comissao.cod_tipo_comissao <> 4 \n";
    $stFiltro.= "   AND comissao.cod_comissao =".$inCodVigencia." \n";

    $obLicitacaoComissao->recuperaRelacionamento( $rsComissaoVigencia,$stFiltro,' ORDER BY comissao.cod_comissao');

    $arDataPublicacao = explode('/',$rsComissaoVigencia->getCampo('dt_publicacao'));
    $stDataPublicacao = $arDataPublicacao[2].$arDataPublicacao[1].$arDataPublicacao[0];

    $arDataHoje = date('Ymd');

    $arDataTermino = explode('/',$rsComissaoVigencia->getCampo('dt_termino'));
    $stDataTermino = $arDataTermino[2].$arDataTermino[1].$arDataTermino[0];

    if ( ($stDataPublicacao <= $arDataHoje) && ($stDataTermino >= $arDataHoje ) && ($rsComissaoVigencia->getCampo('dt_termino') != "") ) {
            return;
    } else {
        if (($rsComissaoVigencia->getCampo('dt_termino') == "")) {
            $inNumNorma  = $rsComissaoVigencia->getCampo('num_norma'     );
            $stExercicio = $rsComissaoVigencia->getCampo('exercicio'     );
            $stTipoNorma = $rsComissaoVigencia->getCampo('nom_tipo_norma');

            $mensagemErroVigenciaValida =  "Efetue a alteração da norma ";
            $mensagemErroVigenciaValida .= "(Código:".$rsComissaoVigencia->getCampo('cod_norma');
            $mensagemErroVigenciaValida .= " - ".$stTipoNorma." ".$inNumNorma."/".$stExercicio;
            $mensagemErroVigenciaValida .= "), para informar a data de término!";
        } else {
            $mensagemErroVigenciaValida = "A comissão selecionada está com a vigência expirada!";
        }
    }

    return $mensagemErroVigenciaValida;
}

    function checkIsValidDataLicitacao($dtLicitacao)
    {
        $error = false;
        if (!empty($dtLicitacao)) {
            list($dia, $mes, $ano) = explode("/", $dtLicitacao);
            if (!checkdate($mes, $dia, $ano)) {
                $error = true;
            }
        } else {
            $error = true;
        }

        return $error ? "Data da licitação inválida!" : "";
    }
?>

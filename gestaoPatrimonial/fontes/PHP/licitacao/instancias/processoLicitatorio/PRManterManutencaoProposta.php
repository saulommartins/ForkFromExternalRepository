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
    * Página de Processamento do Objeto
    * Data de Criação   : 22/11/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @ignore

    * Casos de uso: uc-03.05.25

    $Id: PRManterManutencaoProposta.php 63865 2015-10-27 13:55:57Z franver $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TCOM."TComprasCotacao.class.php";
include_once TCOM."TComprasMapa.class.php";
include_once TCOM."TComprasMapaCotacao.class.php";
include_once TCOM."TComprasCotacaoItem.class.php";
include_once TCOM."TComprasCotacaoFornecedorItem.class.php";
include_once TALM."TAlmoxarifadoCatalogoItemMarca.class.php";
include_once TLIC."TLicitacaoCotacaoLicitacao.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoMarca.class.php";

//Define o nome dos arquivos PHP - Este tela é usada na compra direta e na licitação
$stPrograma = "ManterManutencaoProposta";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";

$stAcao = $request->get('stAcao');

if ($stAcao == 'reemitir' || $stAcao == 'reemitirCompra') {
    foreach ($_REQUEST as $campo =>$valor) {
    $link.="&".$campo."=".$valor;
    }

    $inCodMapa = $_REQUEST['inCodMapa'];
    $stExercicioMapa = $_REQUEST['stExercicioMapa'];
    SistemaLegado::alertaAviso($pgGera."?inCodCotacao=".$inCodCotacao.$link,"Manutenção de Proposta do Mapa ".$inCodMapa."/".$stExercicioMapa." gravada com sucesso!".$stAlerta.$stMensagemErro,"aviso","aviso", Sessao::getId(), "../");
} else {
    function salvarProposta()
    {
        global $request;

        //Define o nome dos arquivos PHP - Este tela é usada na compra direta e na licitação
        $stPrograma = "ManterManutencaoProposta";
        $pgFilt = "FL".$stPrograma.".php";
        $pgForm = "FM".$stPrograma.".php";
        $pgProc = "PR".$stPrograma.".php";
        $pgOcul = "OC".$stPrograma.".php";
        $pgGera = "OCGera".$stPrograma.".php";

        $stAcao = $request->get('stAcao');

        list( $diaM , $mesM , $anoM ) = explode( '/' , $_REQUEST['stDataManutencao'] );
        $dataM = $anoM."-".$mesM."-".$diaM." ".date("H:i:s");

        if ($_REQUEST['stAcao'] != 'dispensaLicitacao' and $_REQUEST['stAcao'] != 'manter') {
            if (!Sessao::read('manutencaoPropostas')) {
                if ($_REQUEST['boIncluiMarca']!='false') {
                    $_REQUEST = Sessao::read('REQUEST');
                    $_REQUEST['boIncluiMarca'] = 'true';
                    Sessao::remove('REQUEST');
                }
            }
        }

        if ($stAcao == 'dispensaLicitacao') {
            $pgList = CAM_GP_COM_INSTANCIAS."compraDireta/LSManterProposta.php";
        } else {
            $pgList = "LS".$stPrograma.".php";
        }

        //inicia a transacao
        Sessao::setTrataExcecao( true );

        $arManterPropostas = Sessao::read('arManterPropostas');
        $rsParticipantes = $arManterPropostas['rsParticipantes'];

        $rsParticipantes->setPrimeiroElemento();

        if (!$_REQUEST['dtValidade']) {
            $inCount = 0;
            $arFornecedores = array();
            while ( !$rsParticipantes->eof() ) {
                $inCodCgmFornecedor = $rsParticipantes->getCampo('cgm_fornecedor');

                include_once CAM_GP_COM_MAPEAMENTO.'TComprasFornecedor.class.php';
                $obTComprasFornecedor = new TComprasFornecedor();
                $obTComprasFornecedor->setDado("cgm_fornecedor", $inCodCgmFornecedor);
                $obTComprasFornecedor->recuperaListaFornecedor( $rsFornecedor );

                if ($rsFornecedor->getCampo('status') == 'Inativo') {
                    $arFornecedores[$inCount]['cgm_fornecedor'] = $inCodCgmFornecedor;
                    $arFornecedores[$inCount]['nom_cgm'] = $rsFornecedor->getCampo('nom_cgm');
                    $inCount++;
                }

                $rsParticipantes->proximo();
            }

            if (count($arFornecedores) > 0) {
                if (count($arFornecedores) == 1) {
                    $stMensagemErro = 'O Participante ('.$arFornecedores[0]['cgm_fornecedor'].' - '.$arFornecedores[0]['nom_cgm'].') está inativo! Efetue a Manutenção de Participantes para retirar este Participante.';
                } elseif (count($arFornecedores) > 1) {
                    foreach ($arFornecedores as $arFornecedoresAux) {
                        $stCodNomFornecedores .= $arFornecedoresAux['cgm_fornecedor'].' - '.$arFornecedoresAux['nom_cgm'].', ';
                    }
                    $stCodNomFornecedores = substr($stCodNomFornecedores, 0, strlen($stCodNomFornecedores)-2);
                    $stMensagemErro = 'Os Participantes ('.$stCodNomFornecedores.') estão inativos! Efetue a Manutenção de Participantes para retirar estes Participantes.';
                }
            }
        }

        $arCadastrarMarcas = array();
        $rsParticipantes->setPrimeiroElemento();

        while ( !$rsParticipantes->eof() ) {
            $rsItensParticipante = $rsParticipantes->getCampo('rsItens');
            $rsItensParticipante->setPrimeiroElemento();

            //verifica se possui marcas que não estão cadastradas no sistema (importação)
            while ( !$rsItensParticipante->eof() ) {
                if ( ($rsItensParticipante->getCampo('desc_marca') != "") && ( $rsItensParticipante->getCampo('cod_marca') == "") ) {
                    $marcaJaExiste = SistemaLegado::pegaDado("descricao","almoxarifado.marca"," where descricao LIKE '".$rsItensParticipante->getCampo('desc_marca')."'");
                    $jaEstaNoArray = false;

                    foreach ($arCadastrarMarcas as $chave =>$descricaoMarcas) {
                        if ($descricaoMarcas['marca'] == $rsItensParticipante->getCampo('desc_marca')) {
                            $jaEstaNoArray = true;
                        }
                    }

                    if (($marcaJaExiste == "") && ($jaEstaNoArray==false)) {
                        $arCadastrarMarcas[]['marca'] = $rsItensParticipante->getCampo('desc_marca');
                    }
                }
                $rsItensParticipante->proximo();
            }

            $rsParticipantes->proximo();
        }

        //verifica se ao final do processamento pode encerrar o programa ou não
        $desabilitaPropostaAutomatica = true;
        //se existirem marcas não cadastradas abre uma popup pra confirmar o cadastro
        $boParaMarcasACadastrar = false;

        if ( count( $arCadastrarMarcas ) > 0 ) {
            if ( ($_REQUEST['boIncluiMarca'] == 'false') || (!$_REQUEST['boIncluiMarca']) ) {
                $arManterPropostas['CadastrarMarcas'] = $arCadastrarMarcas;
                Sessao::write('arManterPropostas',$arManterPropostas);
                echo '<script>window.open ("./LSPopUpManterManutencaoPropostaMarcas.php?'.Sessao::getId().'", "popUpMarcas","menubar=0,resizable=0,width=450,height=350");</script>';

                if (Sessao::read('manutencaoPropostas')) {
                    $desabilitaPropostaAutomatica = false;
                } else {
                    Sessao::write('REQUEST',$_REQUEST);
                }
                $boParaMarcasACadastrar = true;
            }
        }

        if ($boParaMarcasACadastrar == false) {
            echo "<script>window.parent.frames['telaPrincipal'].document.getElementById('boIncluiMarca').value = false;</script>";

            //Se existirem marcas a serem cadastradas cadastra aki e seta o codigo da marca na sessão (importar)
            $rsParticipantes->setPrimeiroElemento();
            while ( !$rsParticipantes->eof() ) {
                $rsItensParticipante = $rsParticipantes->getCampo('rsItens');
                $rsItensParticipante->setPrimeiroElemento();

                while ( !$rsItensParticipante->eof() ) {
                    if ( ($rsItensParticipante->getCampo('desc_marca') != "") && ( $rsItensParticipante->getCampo('cod_marca') == "") ) {
                        $marcaJaExiste = SistemaLegado::pegaDado("descricao","almoxarifado.marca"," where descricao LIKE '".$rsItensParticipante->getCampo('desc_marca')."'");
                        if ($marcaJaExiste == "") {
                            $obTAlmoxarifadoMarca = new TAlmoxarifadoMarca;
                            $obTAlmoxarifadoMarca->proximoCod( $inCodigo , $boTransacao );
                            $obTAlmoxarifadoMarca->setDado("cod_marca"      , $inCodigo );
                            $obTAlmoxarifadoMarca->setDado("descricao"      , stripslashes(stripslashes( $rsItensParticipante->getCampo('desc_marca') ) ) );
                            $obTAlmoxarifadoMarca->inclusao();

                            unset( $obTAlmoxarifadoMarca );

                            $rsItensParticipante->setCampo('cod_marca', $inCodigo);
                        } else {
                            $inCodMarca = SistemaLegado::pegaDado('cod_marca',"almoxarifado.marca"," where descricao LIKE '".$rsItensParticipante->getCampo('desc_marca')."'");
                            $rsItensParticipante->setCampo('cod_marca', $inCodMarca);
                        }
                    }
                    $rsItensParticipante->proximo();
                }

                $rsParticipantes->setCampo( 'rsItens', $rsItensParticipante );
                unset( $rsItensParticipante );
                $rsParticipantes->proximo();
            }

            $arManterPropostas['rsParticipantes'] = $rsParticipantes;
            unset( $rsParticipantes );

            $obTComprasCotacao = new TComprasCotacao;
            $obTComprasMapa = new TComprasMapa;

            if (!$_REQUEST[ 'stMapaCompras' ]) {
                if (Sessao::read('manutencaoPropostas')) {
                    Sessao::encerraExcecao();
                    SistemaLegado::alertaAviso($pgForm."?stAcao=$stAcao&".Sessao::getId(),"Informe o Mapa de Compras e preencha as propostas! ","aviso","aviso", Sessao::getId(), "../");
                    exit;
                }
            }

            list( $inCodMapa , $stExercicioMapa ) = explode ( '/' , $_REQUEST[ 'stMapaCompras' ] );

            // validar mapa da sessao e recebido do post
            if ($arManterPropostas["cod_mapa"] != $inCodMapa || $arManterPropostas["exercicio"] != $stExercicioMapa) {
                Sessao::encerraExcecao();
                SistemaLegado::alertaAviso($pgList."?stAcao=$stAcao&".Sessao::getId(),"Dados inconsistentes para continuar processo!","aviso","aviso", Sessao::getId(), "../");
                exit;
            }

            $obTComprasMapaCotacao = new TComprasMapaCotacao;
            $stFiltroMapaCotacao = " WHERE mapa_cotacao.cod_mapa = ".$inCodMapa."
                                       AND mapa_cotacao.exercicio_mapa = ".$stExercicioMapa."::VARCHAR
                                       AND NOT EXISTS
                                       (
                                        SELECT  1
                                          FROM  compras.cotacao_anulada
                                         WHERE  mapa_cotacao.cod_cotacao       = cotacao_anulada.cod_cotacao
                                           AND  mapa_cotacao.exercicio_cotacao = cotacao_anulada.exercicio
                                       )";

            $obTComprasMapaCotacao->recuperaTodos($rsMapaCotacao,$stFiltroMapaCotacao);

            if (Sessao::getExcecao()->getDescricao() == "Nenhum registro encontrado!") {
                Sessao::getExcecao()->setDescricao("");
            }

            $codCotacaoSessao = Sessao::read("codCotacao");

            if (( $rsMapaCotacao->getCampo( 'cod_cotacao' ) )  and ( $_REQUEST['nuCodCotacao'] == 0 ) and (!$codCotacaoSessao) ) {
                //// se entrar neste if significa que o usuário estava incluindo uma manutenção de proposta pro mapa mas outro usuário concluiu uma inclusão entes
                Sessao::encerraExcecao();
                SistemaLegado::alertaAviso($pgList."?stAcao=$stAcao&".Sessao::getId()," Dados inconsistentes para continuar processo, foram realizadas alterações por outro usuário!","aviso","aviso", Sessao::getId(), "../");
                exit;
            }

            if (!$arManterPropostas["cod_cotacao"]) {
                $arManterPropostas["cod_cotacao"] = $codCotacaoSessao;
                $arManterPropostas["exercicio_cotacao"] = Sessao::getExercicio();
            }

            // se nao existir cotacao para o mapa, inclui!
            if (!$arManterPropostas["cod_cotacao"]) {
                $exercicio = Sessao::getExercicio();

                $obTComprasCotacao->setDado('exercicio', $exercicio);
                $obTComprasCotacao->setDado('timestamp', $dataM);

                $obTComprasCotacao->inclusao();

                $inCodCotacao = $obTComprasCotacao->getDado('cod_cotacao');
                $stExercicioCotacao = Sessao::getExercicio();

                $obTComprasMapaCotacao->setDado( 'cod_cotacao'      , $inCodCotacao );
                $obTComprasMapaCotacao->setDado( 'exercicio_cotacao', $stExercicioCotacao );
                $obTComprasMapaCotacao->setDado( 'cod_mapa'         , $inCodMapa );
                $obTComprasMapaCotacao->setDado( 'exercicio_mapa'	, $stExercicioMapa );
                $obTComprasMapaCotacao->inclusao();

                // inserir itens
                $rsItens = $arManterPropostas["rsItens"];
                $obTComprasCotacaoItem = new TComprasCotacaoItem;
                $rsItens->setPrimeiroElemento();
                while (  !$rsItens->eof() ) {
                    $obTComprasCotacaoItem->setDado( 'exercicio' , $stExercicioCotacao);
                    $obTComprasCotacaoItem->setDado( 'cod_cotacao' , $inCodCotacao );
                    $obTComprasCotacaoItem->setDado( 'lote' , $rsItens->getCampo( 'lote' ) );
                    $obTComprasCotacaoItem->setDado( 'cod_item' , $rsItens->getCampo( 'cod_item' ) );
                    $obTComprasCotacaoItem->setDado( 'quantidade' , $rsItens->getCampo( 'quantidade' ));
                    $obTComprasCotacaoItem->inclusao();
                    $rsItens->proximo();
                }
            } else {
                //se existir , deletar cotacoes de itens existentes
                $inCodCotacao = $arManterPropostas["cod_cotacao"];
                $stExercicioCotacao = $arManterPropostas["exercicio_cotacao"];

                if ($stAcao != "dispensaLicitacao") {
                    $obTLicitacaoCotacaoLicitacao = new TLicitacaoCotacaoLicitacao();
                    $obTLicitacaoCotacaoLicitacao->setDado('cod_cotacao',$inCodCotacao);
                    $obTLicitacaoCotacaoLicitacao->setDado('exercicio_cotacao',$stExercicioCotacao);
                    $obTLicitacaoCotacaoLicitacao->exclusao();
                }

                if ($_REQUEST['stDataManutencao'] != '') {
                    $obTComprasCotacao = new TComprasCotacao();
                    $obTComprasCotacao->setDado('exercicio', Sessao::getExercicio());
                    $obTComprasCotacao->setDado('cod_cotacao', $inCodCotacao);
                    $obTComprasCotacao->recuperaTodos($rsMapaCotacao);

                    $obTComprasCotacao->setDado('timestamp', $dataM);
                    $obTComprasCotacao->alteracao();
                }

                $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;

                $stAux = $obTComprasCotacaoFornecedorItem->getComplementoChave();
                $obTComprasCotacaoFornecedorItem->setComplementoChave('cod_cotacao,exercicio');

                $obTComprasCotacaoFornecedorItem->setDado('exercicio',$stExercicioCotacao );
                $obTComprasCotacaoFornecedorItem->setDado('cod_cotacao',$inCodCotacao );
                $obTComprasCotacaoFornecedorItem->exclusao();

                $obTComprasCotacaoFornecedorItem->setComplementoChave($stAux);
            }

            // inserir contações por participantes
            $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;

            // licitacao_cotacao
            $obLicitacaoCotacaoLicitacao = new TLicitacaoCotacaoLicitacao;

            $rsParticipantes = $arManterPropostas["rsParticipantes"];
            $rsParticipantes->setPrimeiroElemento();
            $inTotalParticipantes = $rsParticipantes->getNumLinhas();
            $inTotalParticipantesCadastrados = 0;

            $rsParticipantes->setPrimeiroElemento();
            while (  !$rsParticipantes->eof() ) {
                $rsItens = $rsParticipantes->getCampo('rsItens');
                $rsItens->setPrimeiroElemento();
                $boCadastrado = false;

                while (  !$rsItens->eof() ) {
                    if ( (int) $rsItens->getCampo('cod_marca') >= 0 && is_numeric($rsItens->getCampo('cod_marca')) && $rsItens->getCampo('valor_unitario') != '0,00' && $rsItens->getCampo('valor_unitario') !='' ) {
                        if (!$boCadastrado) {
                            $inTotalParticipantesCadastrados++;
                            $boCadastrado = true;
                        }
                        //VALIDANDO ITEM MARCA
                        $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;
                        $stFiltro = " AND acim.cod_marca = ".$rsItens->getCampo('cod_marca')." AND acim.cod_item = ".$rsItens->getCampo('cod_item');
                        $obTAlmoxarifadoCatalogoItemMarca->recuperaItemMarca($rsItemMarca, $stFiltro);

                        if ($rsItemMarca->getNumLinhas() < 1) {
                            $obTAlmoxarifadoCatalogoItemMarca->setDado('cod_item',$rsItens->getCampo('cod_item'));
                            $obTAlmoxarifadoCatalogoItemMarca->setDado('cod_marca',$rsItens->getCampo('cod_marca'));
                            $obTAlmoxarifadoCatalogoItemMarca->inclusao();
                        }

                        // tratar data
                        list( $dia , $mes , $ano ) = explode( '/' , $rsItens->getCampo('data_validade') );

                        // tratar valor cotacao
                        $nuValor = str_replace('.' , '' , $rsItens->getCampo('valor_total'));
                        $nuValor = str_replace(',' , '.' , $nuValor );

                        $obTComprasCotacaoFornecedorItem->setDado('exercicio',$stExercicioCotacao );
                        $obTComprasCotacaoFornecedorItem->setDado('cod_cotacao',$inCodCotacao );
                        $obTComprasCotacaoFornecedorItem->setDado('cod_item',$rsItens->getCampo('cod_item') );
                        $obTComprasCotacaoFornecedorItem->setDado('lote',$rsItens->getCampo('lote') );
                        $obTComprasCotacaoFornecedorItem->setDado('cgm_fornecedor',$rsParticipantes->getCampo('cgm_fornecedor') );
                        $obTComprasCotacaoFornecedorItem->setDado('cod_marca',$rsItens->getCampo('cod_marca') );
                        $obTComprasCotacaoFornecedorItem->setDado('dt_validade', '\''. $ano . '-' . $mes . '-' . $dia . '\'');
                        $obTComprasCotacaoFornecedorItem->setDado('vl_cotacao',$nuValor );
                        $obTComprasCotacaoFornecedorItem->setDado('timestamp', $dataM);
                        $obTComprasCotacaoFornecedorItem->inclusao();

                        // se tiver licitacao , ligar cotacao com licitacao
                        if ($stAcao != "dispensaLicitacao") {
                            if ($arManterPropostas['licitacao']['cod_licitacao']) {
                                $obLicitacaoCotacaoLicitacao->setDado ( 'cod_licitacao' , $arManterPropostas['licitacao']['cod_licitacao'] );
                                $obLicitacaoCotacaoLicitacao->setDado ( 'cod_modalidade', $arManterPropostas['licitacao']['cod_modalidade'] );
                                $obLicitacaoCotacaoLicitacao->setDado ( 'cod_entidade' , $arManterPropostas['licitacao']['cod_entidade'] );
                                $obLicitacaoCotacaoLicitacao->setDado ( 'exercicio_licitacao' , $arManterPropostas['licitacao']['exercicio'] );
                                $obLicitacaoCotacaoLicitacao->setDado ( 'lote' , $rsItens->getCampo('lote') );
                                $obLicitacaoCotacaoLicitacao->setDado ( 'cod_cotacao' , $inCodCotacao );
                                $obLicitacaoCotacaoLicitacao->setDado ( 'cod_item' , $rsItens->getCampo('cod_item') );
                                $obLicitacaoCotacaoLicitacao->setDado ( 'exercicio_cotacao' , $stExercicioCotacao );
                                $obLicitacaoCotacaoLicitacao->setDado ( 'cgm_fornecedor' , $rsParticipantes->getCampo('cgm_fornecedor') );
                                $obLicitacaoCotacaoLicitacao->inclusao();
                            }
                        }
                    }

                    $rsItens->proximo();
                }

                $rsParticipantes->proximo();
            }

            Sessao::write('arManterPropostas', $arManterPropostas);

            if ($inTotalParticipantes != $inTotalParticipantesCadastrados) {
                $stAlerta = " (Existem participantes sem proposta lançada!) ";
            }
        
            if (!Sessao::read('manutencaoPropostas')) {
                $link = "";
                $arLink = Sessao::read('link');

                if (is_array($arLink)) {
                    foreach ($arLink as $campo =>$valor) {
                        $link.="&".$campo."=".$valor;
                    }
                }

                if ($_REQUEST['boImprimirComparativo'] == "sim") {
                    foreach ($_REQUEST as $campo =>$valor) {
                        $link.="&".$campo."=".$valor;
                    }

                    SistemaLegado::alertaAviso($pgGera."?inCodCotacao=".$inCodCotacao.$link,"Manutenção de Proposta do Mapa ".$inCodMapa."/".$stExercicioMapa." gravada com sucesso!".$stAlerta.$stMensagemErro,"aviso","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::alertaAviso($pgList."?stAcao=$stAcao&".Sessao::getId().$link,"Manutenção de Proposta do Mapa ".$inCodMapa."/".$stExercicioMapa." gravada com sucesso!".$stAlerta.$stMensagemErro,"aviso","aviso", Sessao::getId(), "../");
                }
            } else {
                Sessao::write("codCotacao", $inCodCotacao);
            }
        }
        //encerra a transacao
        Sessao::encerraExcecao();

        return $desabilitaPropostaAutomatica;
    }

    $boDesabilitarPropostaAutomatica = salvarProposta();

    if ($boDesabilitarPropostaAutomatica == true) {
        Sessao::remove('manutencaoPropostas');
    }
}

?>

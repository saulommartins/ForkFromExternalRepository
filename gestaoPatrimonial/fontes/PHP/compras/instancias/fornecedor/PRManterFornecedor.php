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
    * Data de Criação   : 19/09/06

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso: uc-03.04.03

    $Id: PRManterFornecedor.php 63901 2015-11-04 14:04:10Z jean $
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoPatrimonial/fontes/PHP/compras/classes/mapeamento/TComprasCotacaoFornecedorItem.class.php';
include_once(CAM_GP_COM_MAPEAMENTO.'TComprasTipoSocio.class.php');
include_once(CAM_GP_COM_MAPEAMENTO.'TComprasFornecedorSocio.class.php');
include_once(TCOM."TComprasFornecedor.class.php");
include_once(TCOM."TComprasFornecedorAtividade.class.php");
include_once(TCOM."TComprasFornecedorClassificacao.class.php");
include_once(TCOM."TComprasFornecedorConta.class.php");
include_once(TALM."TAlmoxarifadoCatalogoClassificacao.class.php");
include_once(TMON."TMONAgencia.class.php");
include_once(TMON."TMONBanco.class.php");
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php" );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPreEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterFornecedor";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );
$obTFornecedor = new TComprasFornecedor();
$obTFornecedorAtividade = new TComprasFornecedorAtividade();
$obTFornecedorClassificacao = new TComprasFornecedorClassificacao();
$obTFornecedorConta = new TComprasFornecedorConta();
$obTCatalogoClassificacao = new TAlmoxarifadoCatalogoClassificacao();
$obTMONAgencia = new TMONAgencia();
$obTMONBanco = new TMONBanco();
$obErro = new Erro;

Sessao::getTransacao()->setMapeamento( $obTFornecedor );

$stAcao = $request->get('stAcao');
//recupera dados do catálogo de classificação
$obTCatalogoClassificacao->recuperaTodos($rsCatalogoClassificacao);
while (!$rsCatalogoClassificacao->eof()) {
    $arCatalogoClassificacao[$rsCatalogoClassificacao->getCampo('cod_catalogo')][$rsCatalogoClassificacao->getCampo('cod_estrutural')] = $rsCatalogoClassificacao->getCampo('cod_classificacao');
    $rsCatalogoClassificacao->proximo();
}

//recupera dados da agencia
$obTMONAgencia->recuperaTodos($rsContaBancoAgencia);
while (!$rsContaBancoAgencia->eof()) {
    $arContaBancoAgencia[$rsContaBancoAgencia->getCampo('num_agencia')] = $rsContaBancoAgencia->getCampo('cod_agencia');
    $rsContaBancoAgencia->proximo();
}
//recupera dados do banco
$obTMONBanco->recuperaTodos($rsContaBanco);
while (!$rsContaBanco->eof()) {
    $arContaBanco[$rsContaBanco->getCampo('num_banco')] = $rsContaBanco->getCampo('cod_banco');
    $rsContaBanco->proximo();
}

switch ($_REQUEST['stAcao']) {
    case "incluir":

        $stFiltro = " and cgm_fornecedor=".$_REQUEST['inCGM'];
        $obTFornecedor->recuperaRelacionamento($rsFornecedor, $stFiltro);

        $arContaBancariaSessao = Sessao::read('arContaBancaria');
        $arAtividades  = Sessao::read('arAtividades');
        $arFornecedor  = Sessao::read('arFornecedor');
        $arSocioSessao = Sessao::read('arSocio');

        if ( $rsFornecedor->getNumLinhas()  > 0 ) {
            sistemaLegado::exibeAviso( 'Fornecedor já cadastrado no sistema.'    ,"n_incluir","erro");
        } else {

            $flMoeda = str_replace('.','',$_REQUEST['moeda']);
            $flMoeda = str_replace(',','.',$flMoeda);

            $obTFornecedor->setDado("vl_minimo_nf", $flMoeda );
            $obTFornecedor->setDado("ativo","true");
            $obTFornecedor->setDado("cgm_fornecedor",$_REQUEST['inCGM']);
            $obTFornecedor->setDado("tipo",$_REQUEST['stTipoFornecedor']);

            $obTFornecedor->inclusao();

            if ($_REQUEST['inNumeroPisPasep']) {
                $obTCGM = new TCGMPessoaFisica();
                $obTCGM->setDado('numcgm', $_REQUEST['inCGM'] );
                $obTCGM->consultar();
                $obTCGM->setDado('servidor_pis_pasep', $_REQUEST['inNumeroPisPasep'] );
                $obTCGM->alteracao();
            }

            if (count($arAtividades) >0) {
                foreach ($arAtividades as $key => $value) {
                    $obTFornecedorAtividade->setDado("cgm_fornecedor",$_REQUEST['inCGM']);
                    $obTFornecedorAtividade->setDado("cod_atividade",$value['cod_atividade']);
                    $obTFornecedorAtividade->inclusao();
                }
            }
            if (count($arFornecedor) >0) {
                foreach ($arFornecedor as $key => $value) {
                    $obTFornecedorClassificacao->setDado("cgm_fornecedor",$_REQUEST['inCGM']);
                    $obTFornecedorClassificacao->setDado("cod_catalogo",$value['cod_catalogo']);
                    $obTFornecedorClassificacao->setDado("cod_classificacao",$value['cod_classificacao']);
                    $obTFornecedorClassificacao->inclusao();
                }
            }

            if ( count($arContaBancariaSessao) >0 ) {
                foreach ($arContaBancariaSessao as $key => $value) {
                    $obTFornecedorConta->setDado("cgm_fornecedor",$_REQUEST['inCGM']);
                    $obTFornecedorConta->setDado("cod_banco"     ,$value['cod_banco']);
                    $obTFornecedorConta->setDado("cod_agencia"   ,$value['cod_agencia']);
                    $obTFornecedorConta->setDado("num_conta"     ,$value['conta']);
                    $obTFornecedorConta->setDado("padrao"        ,$value['padrao'] == 1 ? "'t'" : "'f'");

                    $obTFornecedorConta->inclusao();
                }
            }
            
            if ( count($arSocioSessao) > 0 ) {
                foreach ($arSocioSessao as $key => $value) {
                    $obTComprasFornecedorSocio = new TComprasFornecedorSocio;
                
                    $obTComprasFornecedorSocio->proximoCod($inCod);
                
                    $obTComprasFornecedorSocio->setDado("id"            , $inCod);
                    $obTComprasFornecedorSocio->setDado("cod_tipo"      , $value['cod_tipo']);
                    $obTComprasFornecedorSocio->setDado("cgm_fornecedor", $_REQUEST['inCGM']);
                    $obTComprasFornecedorSocio->setDado("cgm_socio"     , $value['numcgm']);
                    $obTComprasFornecedorSocio->setDado("ativo"         , $value['ativo'] ? true:false);

                    $obTComprasFornecedorSocio->inclusao();
                }
            }
            
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Fornecedor - ".$_REQUEST['inCGM']."","incluir","incluir_n", Sessao::getId(), "../");
        }
    break;

    case 'excluir':
        $pgProx = $pgList."?".Sessao::getId().'&stAcao='.$stAcao;
        include_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoParticipante.class.php' ) ;
        $obTLicitacaoParticipante = new TLicitacaoParticipante;

        include_once(TCOM."TComprasFornecedorInativacao.class.php");
        $obTFornecedorInativacao = new TComprasFornecedorInativacao();

        $stFiltro = ' where cgm_fornecedor = ' . $_REQUEST['inCGM'] ;
        $obTLicitacaoParticipante->recuperaTodos( $rsParticipante, $stFiltro );

        $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem();
        $obTComprasCotacaoFornecedorItem->recuperaTodos($rsComprasCotacaoFornecedorItem, $stFiltro );

        $stFiltro = " WHERE cgm_beneficiario = ". $_REQUEST['inCGM'];

        $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho();
        $obTEmpenhoPreEmpenho->recuperaTodos($rsEmpenhoPreEmpenho, $stFiltro);

        $stErro = '';

        if (($rsParticipante->getNumLinhas() > 0                ) ||
            ($rsComprasCotacaoFornecedorItem->getNumLinhas() > 0) ||
            ($rsEmpenhoPreEmpenho->getNumLinhas() > 0)){
            $stErro = "Fornecedor ". $_REQUEST["inCGM"]. " está sendo utilizado pelo sistema.";
        }

        if (!$stErro) {

            $obTFornecedorAtividade->setDado("cgm_fornecedor",$_REQUEST['inCGM']);
            $obTFornecedorAtividade->exclusao();

            $obTFornecedorClassificacao->setDado("cgm_fornecedor",$_REQUEST['inCGM']);
            $obTFornecedorClassificacao->exclusao();

            $obTFornecedorConta->setDado("cgm_fornecedor",$_REQUEST['inCGM']);
            $obTFornecedorConta->recuperaListaFornecedorConta( $rsFornecedorConta );
            while ( !$rsFornecedorConta->eof() ) {
                $obTFornecedorConta->setDado("num_conta",$rsFornecedorConta->getCampo('num_conta'));
                $obTFornecedorConta->exclusao();
                $rsFornecedorConta->proximo();
            }

            $obTFornecedorInativacao->setDado("cgm_fornecedor",$_REQUEST['inCGM']);
            $obTFornecedorInativacao->exclusao(Sessao::getTransacao());

            $obTFornecedor->setDado("cgm_fornecedor",$_REQUEST['inCGM']);
            $obErro = $obTFornecedor->exclusao( Sessao::getTransacao() );

            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Fornecedor: ".$_REQUEST["inCGM"].' - '.$_REQUEST["stNomCGM"],"excluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","O Fornecedor ".$_REQUEST["inCGM"].' - '.$_REQUEST["stNomCGM"]." já está sendo usada pelo sistema","n_excluir","erro", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir", $stErro  ,"n_excluir","erro", Sessao::getId(), "../");
        }
        break;

    case 'ativar/desativar':
        include_once(TCOM."TComprasFornecedorInativacao.class.php");
        $obTFornecedorInativacao = new TComprasFornecedorInativacao();
        if ($_REQUEST['stStatus'] == "Inativo") {
            $obTFornecedorInativacao->recuperaUltimosFornecedores($rsLista);
            while (!$rsLista->eof()) {
                if ($rsLista->getCampo('cgm_fornecedor') == $_REQUEST['inCGM']) {
                    $obTFornecedorInativacao->setDado('cgm_fornecedor',$rsLista->getCampo('cgm_fornecedor'));
                    $obTFornecedorInativacao->setDado('timestamp_inicio',$rsLista->getCampo('timestamp_inicio'));
                    $obTFornecedorInativacao->setDado('timestamp_fim',date('Y-m-d H:i:s.us'));
                    $obTFornecedorInativacao->setDado('motivo',$rsLista->getCampo('motivo'));
                    $obTFornecedorInativacao->alteracao();

                    $obTFornecedor->setDado('cgm_fornecedor',$rsLista->getCampo('cgm_fornecedor'));
                    $obTFornecedor->recuperaPorChave( $rsFornecedor );
                    $obTFornecedor->setDado('vl_minimo_nf',$rsFornecedor->getCampo('vl_minimo_nf'));
                    $obTFornecedor->setDado('ativo','true');
                    $obTFornecedor->setDado('tipo',$rsFornecedor->getCampo('tipo'));
                    $obTFornecedor->alteracao();
                }
                $rsLista->proximo();
            }

            $stMsg = "ativado";
        } else {
            $obTFornecedorInativacao = new TComprasFornecedorInativacao();
            $obTFornecedorInativacao->setDado('cgm_fornecedor',$_REQUEST['inCGM']);
            $obTFornecedorInativacao->setDado('timestamp_inicio',date('Y-m-d H:i:s.us'));
            $obTFornecedorInativacao->setDado('motivo',$_REQUEST['stMotivo']);
            $obTFornecedorInativacao->inclusao();

            $obTFornecedor->setDado('cgm_fornecedor',$_REQUEST['inCGM']);
            $obTFornecedor->recuperaPorChave( $rsFornecedor );
            $obTFornecedor->setDado('vl_minimo_nf',$rsFornecedor->getCampo('vl_minimo_nf'));
            $obTFornecedor->setDado('ativo','false');
            $obTFornecedor->setDado('tipo',$rsFornecedor->getCampo('tipo'));
            $obTFornecedor->alteracao();

            $stMsg = "inativado";
        }
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Fornecedor - ".$_REQUEST['inCGM']." ".$stMsg." com sucesso!","aviso","aviso", Sessao::getId(), "../");
    break;

    case 'alterar':
        $obTFornecedor->setDado("cgm_fornecedor",$_REQUEST['inCGM']);
        $obTFornecedor->recuperaPorChave ( $rsFornecedor );
        $flMoeda = str_replace('.','',$_REQUEST['moeda']);
        $flMoeda = str_replace(',','.',$flMoeda);
        $obTFornecedor->setDado("vl_minimo_nf", $flMoeda );
        $obTFornecedor->setDado('ativo','true');
        $obTFornecedor->setDado("tipo",$_REQUEST['stTipoFornecedor']);

        $obTFornecedor->alteracao();

        if ($_REQUEST['inNumeroPisPasep']) {
            $obTCGM = new TCGMPessoaFisica();
            $obTCGM->setDado('numcgm', $_REQUEST['inCGM'] );
            $obTCGM->consultar();
            $obTCGM->setDado('servidor_pis_pasep', $_REQUEST['inNumeroPisPasep'] );
            $obTCGM->alteracao();
        }

        //alteracao do fornecedor na tabela compras.fornecedor_atividade
        $obTFornecedorAtividades = new TComprasFornecedorAtividade();
        $obTFornecedorAtividades->setDado('cgm_fornecedor',$_REQUEST['inCGM']);
        $obTFornecedorAtividades->recuperaAtividadeFornecedor($rsFornecedorAtividades);
        while (!$rsFornecedorAtividades->eof()) {
            $stKeyDb = $rsFornecedorAtividades->getCampo('cgm_fornecedor')."-".
            $rsFornecedorAtividades->getCampo('cod_atividade');
            $arItensChave[$stKeyDb] = true;
            $rsFornecedorAtividades->proximo();
        }

        $arAtividades = Sessao::read('arAtividades');
        if ( count($arAtividades) > 0 ) {
            foreach ($arAtividades as $key => $value) {
                $stKeyNew = $value['cgm_fornecedor'].'-'.$value['cod_atividade'];
                $obTFornecedorAtividades->setDado('cgm_fornecedor',$value['cgm_fornecedor']);
                $obTFornecedorAtividades->setDado('cod_atividade',$value['cod_atividade']);
                if ( !isset( $arItensChave[$stKeyNew] ) ) {
                    $obTFornecedorAtividades->inclusao();
                } else {
                    $obTFornecedorAtividades->alteracao();
                    unset( $arItensChave[$stKeyNew] );
                }
            }
            if ($arItensChave) {
                foreach ($arItensChave as $stChave => $valor) {
                    $arChave = explode('-',$stChave);
                    $obTFornecedorAtividades->setDado('cgm_fornecedor' , $arChave[0] );
                    $obTFornecedorAtividades->setDado('cod_atividade', $arChave[1] );
                    $obTFornecedorAtividades->exclusao();
                }
            }
        }
        //alteracao da tabela de compras.fornecedor_classificacao
        $obTFornecedorClassificacao = new TComprasFornecedorClassificacao();
        $obTFornecedorClassificacao->setDado('cgm_fornecedor',$_REQUEST['inCGM']);
        $obTFornecedorClassificacao->recuperaListaFornecedorClassificacao($rsFornecedorClassificacao);

        while (!$rsFornecedorClassificacao->eof()) {
            $stKeyDb = $rsFornecedorClassificacao->getCampo('cgm_fornecedor')."-".
                       $rsFornecedorClassificacao->getCampo('cod_catalogo')."-".
                       $rsFornecedorClassificacao->getCampo('cod_classificacao');
            $arItensChave[$stKeyDb] = true;
            $rsFornecedorClassificacao->proximo();
        }

        $arFornecedor = Sessao::read('arFornecedor');

        if ( count( $arFornecedor ) > 0  ) {
            foreach ($arFornecedor as $key => $value) {
                $stKeyNew = $value['cgm_fornecedor'].'-'.$value['cod_catalogo']."-".$value['cod_classificacao'];
                $obTFornecedorClassificacao->setDado('cgm_fornecedor'    ,$value['cgm_fornecedor']);
                $obTFornecedorClassificacao->setDado('cod_catalogo'      ,$value['cod_catalogo'  ]);
                $obTFornecedorClassificacao->setDado('cod_classificacao' ,$value['cod_classificacao' ]);
                if ( !isset( $arItensChave[$stKeyNew] ) ) {
                    $obTFornecedorClassificacao->inclusao();
                } else {
                    $obTFornecedorClassificacao->alteracao();
                    unset( $arItensChave[$stKeyNew] );
                }
            }
            if ($arItensChave) {
                foreach ($arItensChave as $stChave => $valor) {
                    $arChave = explode('-',$stChave);
                    $obTFornecedorClassificacao->setDado('cgm_fornecedor'   , $arChave[0] );
                    $obTFornecedorClassificacao->setDado('cod_catalogo'     , $arChave[1] );
                    $obTFornecedorClassificacao->setDado('cod_classificacao', $arChave[2] );
                    $obTFornecedorClassificacao->exclusao();
                }
            }
        }
        //alteracao do fornecedor na tabela compras.fornecedor_conta
        $obTFornecedorConta = new TComprasFornecedorConta();
        $obTFornecedorConta->setDado('cgm_fornecedor',$_REQUEST['inCGM']);
        $obTFornecedorConta->recuperaListaFornecedorConta($rsContaBancaria);
        $obTFornecedorConta->setComplementoChave('');
        $obTFornecedorConta->setCampoCod('cgm_fornecedor');
        $obTFornecedorConta->exclusao();

        $arContaBancaria = Sessao::read('arContaBancaria');

        if ( count( $arContaBancaria ) > 0 ) {
            foreach ($arContaBancaria as $key => $value) {

                $obTFornecedorConta->setDado('cgm_fornecedor'    ,$_REQUEST['inCGM']);
                $obTFornecedorConta->setDado('cod_banco'      ,$value['cod_banco'  ]);
                $obTFornecedorConta->setDado('cod_agencia' ,$value['cod_agencia' ]);
                $obTFornecedorConta->setDado('num_conta', $value['conta']);
                $obTFornecedorConta->setDado('num_banco', $value['num_banco']);
                $obTFornecedorConta->setDado("padrao"   ,$value['padrao'] == 1 ? "'t'" : "'f'");
                $obTFornecedorConta->inclusao();
            }
        }
        

        $obTComprasFornecedorSocio = new TComprasFornecedorSocio;
        $obTComprasFornecedorSocio->setCampoCod('cgm_fornecedor');
        $obTComprasFornecedorSocio->setDado("cgm_fornecedor", $_REQUEST['inCGM']);
        $obTComprasFornecedorSocio->exclusao(Sessao::getTransacao());
        
        $arSocioSessao = Sessao::read('arSocio');
        
        if ( count($arSocioSessao) > 0 ) {
            foreach ($arSocioSessao as $key => $value) {
                $obTComprasFornecedorSocio = new TComprasFornecedorSocio;
                
                $obTComprasFornecedorSocio->proximoCod($inCod);
                
                $obTComprasFornecedorSocio->setDado("id"            , $inCod);
                $obTComprasFornecedorSocio->setDado("cod_tipo"      , $value['cod_tipo']);
                $obTComprasFornecedorSocio->setDado("cgm_fornecedor", $_REQUEST['inCGM']);
                $obTComprasFornecedorSocio->setDado("cgm_socio"     , $value['numcgm']);
                $obTComprasFornecedorSocio->setDado("ativo"         , $value['ativo'] ? true:false);

                $obTComprasFornecedorSocio->inclusao(Sessao::getTransacao());
            }
        }

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Fornecedor - ".$_REQUEST['inCGM']."","alterar","alterar_n", Sessao::getId(), "../");
        break;

}

Sessao::encerraExcecao();
?>
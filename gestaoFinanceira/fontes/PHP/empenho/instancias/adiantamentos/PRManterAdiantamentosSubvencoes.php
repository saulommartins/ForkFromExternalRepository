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
    * Página Oculto de Lancamento Partida Dobrada
    * Data de Criação   : 25/10/2006

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Rodrigo

    * @ignore

    * Casos de uso: uc-02.03.31
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include mapeamentos
include_once( TEMP."TEmpenhoPrestacaoContas.class.php"                                               );
include_once( TEMP."TEmpenhoItemPrestacaoContas.class.php"                                           );
include_once( TEMP."TEmpenhoItemPrestacaoContasAnulado.class.php"                                    );
include_once( TEMP."TEmpenhoResponsavelAdiantamento.class.php"                                       );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePrestacaoContas.class.php"                       );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php"                       );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoEmpenho.class.php"                     );

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];

$stPrograma = "ManterAdiantamentosSubvencoes";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$rsRecordSetItemPrestacao             = new RecordSet();
$rsRecordSetPrestacao                 = new RecordSet();
$obTEmpenhoPrestacaoContas            = new TEmpenhoPrestacaoContas;
$obTEmpenhoItemPrestacaoContas        = new TEmpenhoItemPrestacaoContas;
$obTEmpenhoItemPrestacaoContasAnulado = new TEmpenhoItemPrestacaoContasAnulado;
$boErro = false;

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTEmpenhoItemPrestacaoContas );

switch ($_REQUEST['stAcao']) {

    case 'incluir':

        if ($_REQUEST['boDevolucao'] == 'Nao') {
            // Verifica se a algum item na prestacao de contas
            $arValores = Sessao::read('arValores');
            $arValoresmd5 = Sessao::read('arValoresmd5');
            if (count($arValores) == 0) {
                SistemaLegado::exibeAviso('Deve existir ao menos uma nota fiscal na lista', "n_incluir", "erro" );
                $boErro = true;
            }

    //        // Verifica se os dados foram não foram alterados na base
    //        if (!$boErro) {
    //
    //            $obTEmpenhoItemPrestacaoContas = new TEmpenhoItemPrestacaoContas;
    //            $obTEmpenhoItemPrestacaoContas->setDado('exercicio'   ,Sessao::getExercicio());
    //            $obTEmpenhoItemPrestacaoContas->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
    //            $obTEmpenhoItemPrestacaoContas->setDado('cod_empenho' ,$_REQUEST['inCodEmpenho']);
    //            $obTEmpenhoItemPrestacaoContas->recuperaListagemPrestacao( $rsRecordSetItemPrestacao);
    //
    //            if (!($rsRecordSetItemPrestacao->EOF())) {
    //                $Cont = 0;
    //                while (!($rsRecordSetItemPrestacao->EOF())) {
    //
    //                    $chave = $rsRecordSetItemPrestacao->getCampo("num_item")
    //                            .$rsRecordSetItemPrestacao->getCampo("cod_documento")
    //                            .$rsRecordSetItemPrestacao->getCampo("num_documento")
    //                            .$rsRecordSetItemPrestacao->getCampo("data_item")
    //                            .$rsRecordSetItemPrestacao->getCampo("valor_item")
    //                            .$rsRecordSetItemPrestacao->getCampo("justificativa")
    //                            .$rsRecordSetItemPrestacao->getCampo("credor");
    //
    //                    if ($arValoresmd5[$Cont]['md5'] != md5($chave) ) {
    //                         SistemaLegado::exibeAviso('Esta prestação de contas foi modificada por outro usuário.', "n_incluir", "erro" );
    //                         $boErro = true;
    //                         break;
    //                    }
    //
    //                    $rsRecordSetItemPrestacao->proximo();
    //                    $Cont++;
    //                }
    //            }
    //        }

            if (!$boErro) {

                // Busca o valor anterior prestado contas
                $obTEmpenhoItemPrestacaoContas->setDado('exercicio'   ,$_REQUEST['exercicio']);
                $obTEmpenhoItemPrestacaoContas->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
                $obTEmpenhoItemPrestacaoContas->setDado('cod_empenho' ,$_REQUEST['inCodEmpenho']);
                $obTEmpenhoItemPrestacaoContas->recuperaValorPrestado( $rsValorPrestado );

                $nuTotalAnteriorPrestarContas = $rsValorPrestado->getCampo('vl_prestado');

                // Soma o valor a prestar contas
                foreach ($arValores as $arItem) {
                    $valorItem = str_replace(',','.',str_replace('.','',$arItem['nuValor']));
                    $nuTotalPrestarContas = bcadd( $nuTotalPrestarContas, $valorItem, 2 );
                }

                // Busca as contas lancamento e contrapartida
                $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
                $obTEmpenhoResponsavelAdiantamento->setDado('exercicio'           ,$_REQUEST['exercicio']);
                $obTEmpenhoResponsavelAdiantamento->setDado('numcgm'              ,$_REQUEST['inCodCredor']);
                $obTEmpenhoResponsavelAdiantamento->setDado('conta_contrapartida' ,$_REQUEST['inCodContrapartida']);
                $obTEmpenhoResponsavelAdiantamento->recuperaPorChave($rsContas);

                $stContaDebito   = $rsContas->getCampo('conta_contrapartida');
                $stContaCredito  = $rsContas->getCampo('conta_lancamento');

                // INCLUIR/ALTERAR empenho.prestacao_contas
                $obTEmpenhoPrestacaoContas->setDado( "exercicio"    ,$_REQUEST['exercicio'           ] );
                $obTEmpenhoPrestacaoContas->setDado( "cod_entidade" ,$_REQUEST['inCodEntidade'       ] );
                $obTEmpenhoPrestacaoContas->setDado( "cod_empenho"  ,$_REQUEST['inCodEmpenho'        ] );
                $obTEmpenhoPrestacaoContas->setDado( "data"         ,$_REQUEST['stDtPrestacaoContas' ] );
                $obTEmpenhoPrestacaoContas->recuperaPorChave( $rsPrestacaoContas, $stFiltro );

                if ($rsPrestacaoContas->eof()) {
                    $obTEmpenhoPrestacaoContas->inclusao();
                } else {
                    $obTEmpenhoPrestacaoContas->alteracao();
                }

                // Exclui itens da lista em empenho.item_prestacao_contas
                $inCount = Sessao::read('inCountValores');
                for ($Pos = 1; $Pos <= $inCount; $Pos++) {
                    $obTEmpenhoItemPrestacaoContas->setDado("cod_empenho" , $_REQUEST['inCodEmpenho'] );
                    $obTEmpenhoItemPrestacaoContas->setDado("cod_entidade", $_REQUEST['inCodEntidade']);
                    $obTEmpenhoItemPrestacaoContas->setDado("exercicio"   , $_REQUEST['exercicio']    );
                    $obTEmpenhoItemPrestacaoContas->setDado("num_item"    , $Pos );
                    $obTEmpenhoItemPrestacaoContas->exclusao();
                }

                // INCLUIR empenho.itens_prestacao_contas
                for ($Pos = 0; $Pos < count($arValores); $Pos++) {

                    $obTEmpenhoItemPrestacaoContas->setDado("exercicio"          ,Sessao::getExercicio()                                       );
                    $obTEmpenhoItemPrestacaoContas->setDado("cod_entidade"       ,$arValores[$Pos]['inCodEntidade']     );
                    $obTEmpenhoItemPrestacaoContas->setDado("cod_empenho"        ,$arValores[$Pos]['inCodEmpenho']      );
                    $obTEmpenhoItemPrestacaoContas->setDado("exercicio_conta"    ,$_REQUEST['exercicio']                                   );
                    $obTEmpenhoItemPrestacaoContas->setDado("conta_contrapartida",$_REQUEST['inCodContrapartida']                          );
                    $obTEmpenhoItemPrestacaoContas->setDado("num_item"           ,$arValores[$Pos]['numItem']           );
                    $obTEmpenhoItemPrestacaoContas->setDado("cod_documento"      ,$arValores[$Pos]['inCodTipoDocumento']);
                    $obTEmpenhoItemPrestacaoContas->setDado("data_item"          ,$arValores[$Pos]['stDataDocumento']   );
                    $obTEmpenhoItemPrestacaoContas->setDado("valor_item"         ,$arValores[$Pos]['nuValor']           );
                    $obTEmpenhoItemPrestacaoContas->setDado("justificativa"      ,stripslashes(substr($arValores[$Pos]['stJustificativa'],0,80) ));
                    if ($arValores[$Pos]['inNroDocumento'] != '') {
                        $obTEmpenhoItemPrestacaoContas->setDado("num_documento" ,$arValores[$Pos]['inNroDocumento']);
                    } else {
                        $obTEmpenhoItemPrestacaoContas->setDado("num_documento" , 'null');
                    }
                    $obTEmpenhoItemPrestacaoContas->setDado("credor"             ,$arValores[$Pos]['inCodFornecedor']   );
                    $obTEmpenhoItemPrestacaoContas->inclusao();
                }
            }
        } else {
            if ($_REQUEST['stDtPrestacaoContas'] == '') {
                SistemaLegado::exibeAviso('Deve ser informada a data da prestação', "n_incluir", "erro" );
                $boErro = true;
            }

            // Busca o valor anterior prestado contas
            $obTEmpenhoItemPrestacaoContas->setDado('exercicio'   ,$_REQUEST['exercicio']);
            $obTEmpenhoItemPrestacaoContas->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
            $obTEmpenhoItemPrestacaoContas->setDado('cod_empenho' ,$_REQUEST['inCodEmpenho']);
            $obTEmpenhoItemPrestacaoContas->recuperaValorPrestado( $rsValorPrestado );

            $nuTotalAnteriorPrestarContas = $rsValorPrestado->getCampo('vl_prestado');

            $nuTotalPrestarContas = $_REQUEST['inVlPago'];

            $inCount = Sessao::read('inCountValores');
            if ($inCount > 0) {
                for ($Pos = 1; $Pos <= $inCount; $Pos++) {
                    $obTEmpenhoItemPrestacaoContas->setDado("cod_empenho" , $_REQUEST['inCodEmpenho'] );
                    $obTEmpenhoItemPrestacaoContas->setDado("cod_entidade", $_REQUEST['inCodEntidade']);
                    $obTEmpenhoItemPrestacaoContas->setDado("exercicio"   , $_REQUEST['exercicio']    );
                    $obTEmpenhoItemPrestacaoContas->setDado("num_item"    , $Pos );
                    $obTEmpenhoItemPrestacaoContas->exclusao();
                }
            }

            // Busca as contas lancamento e contrapartida
            $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
            $obTEmpenhoResponsavelAdiantamento->setDado('exercicio'           ,$_REQUEST['exercicio']);
            $obTEmpenhoResponsavelAdiantamento->setDado('numcgm'              ,$_REQUEST['inCodCredor']);
            $obTEmpenhoResponsavelAdiantamento->setDado('conta_contrapartida' ,$_REQUEST['inCodContrapartida']);
            $obTEmpenhoResponsavelAdiantamento->recuperaPorChave($rsContas);

            $stContaDebito   = $rsContas->getCampo('conta_contrapartida');
            $stContaCredito  = $rsContas->getCampo('conta_lancamento');

            if (!$boErro) {
                // INCLUIR/ALTERAR empenho.prestacao_contas
                $obTEmpenhoPrestacaoContas->setDado("exercicio"   , $_REQUEST['exercicio'          ]);
                $obTEmpenhoPrestacaoContas->setDado("cod_entidade", $_REQUEST['inCodEntidade'      ]);
                $obTEmpenhoPrestacaoContas->setDado("cod_empenho" , $_REQUEST['inCodEmpenho'       ]);
                $obTEmpenhoPrestacaoContas->setDado("data"        , $_REQUEST['stDtPrestacaoContas']);
                $obTEmpenhoPrestacaoContas->recuperaPorChave($rsPrestacaoContas, $stFiltro);

                if ($rsPrestacaoContas->eof()) {
                    $obTEmpenhoPrestacaoContas->inclusao();
                } else {
                    $obTEmpenhoPrestacaoContas->alteracao();
                }
            }
        }

        if (!$boErro) {

            // LANCAMENTOS CONTABEIS

            // Faz um novo lancamento com estorno do lancamento anterior
            if ( ( $nuTotalAnteriorPrestarContas > 0 ) && ($nuTotalAnteriorPrestarContas != $nuTotalPrestarContas ) ) {

                // Insere o Lote de estorno
                $obTContabilidadePrestacaoContas = new TContabilidadePrestacaoContas;
                $obTContabilidadePrestacaoContas->setDado( "tipo"          , 'P'                                 );
                $obTContabilidadePrestacaoContas->setDado( "nom_lote"      , "Estorno Prestação de Contas Empenho n° ".$_REQUEST['inCodEmpenho']."/".$_REQUEST['exercicio'] );
                $obTContabilidadePrestacaoContas->setDado( "dt_lote"       , $_REQUEST['stDtPrestacaoContas' ]   );
                $obTContabilidadePrestacaoContas->setDado( "exercicio"     , $_REQUEST['exercicio'           ]   );
                $obTContabilidadePrestacaoContas->setDado( "cod_entidade"  , $_REQUEST['inCodEntidade'       ]   );
                $obTContabilidadePrestacaoContas->insereLote( $inCodLote                                         );

                // Faz o lancamento invertido referente ao estorno
                $obTContabilidadeValorLancamento   = new TContabilidadeValorLancamento;
                $obTContabilidadeValorLancamento->setDado( "cod_lote"      , $inCodLote                                           );
                $obTContabilidadeValorLancamento->setDado( "tipo"          , 'P'                                                  );
                $obTContabilidadeValorLancamento->setDado( "exercicio"     , $_REQUEST['exercicio']                               );
                $obTContabilidadeValorLancamento->setDado( "cod_entidade"  , $_REQUEST['inCodEntidade']                           );
                $obTContabilidadeValorLancamento->setDado( "cod_plano_deb" , $stContaCredito                                      );
                $obTContabilidadeValorLancamento->setDado( "cod_plano_cred", $stContaDebito                                       );
                $obTContabilidadeValorLancamento->setDado( "cod_historico" , 981                                                  );
                $obTContabilidadeValorLancamento->setDado( "complemento"   , $_REQUEST['inCodEmpenho']."/".$_REQUEST['exercicio'] );
                $obTContabilidadeValorLancamento->setDado( "vl_lancamento" , $nuTotalAnteriorPrestarContas );
                $obTContabilidadeValorLancamento->inclusaoPorPl( $rsRecordSet );

                // Insere em contabilidade.lancamento_empenho
                $obTContabilidadeLancamentoEmpenho =  new TContabilidadeLancamentoEmpenho;
                $obTContabilidadeLancamentoEmpenho->setDado( "cod_lote"     , $inCodLote                 );
                $obTContabilidadeLancamentoEmpenho->setDado( "tipo"         , "P"                        );
                $obTContabilidadeLancamentoEmpenho->setDado( "sequencia"    , 1                          );
                $obTContabilidadeLancamentoEmpenho->setDado( "exercicio"    , $_REQUEST['exercicio']     );
                $obTContabilidadeLancamentoEmpenho->setDado( "cod_entidade" , $_REQUEST['inCodEntidade'] );
                $obTContabilidadeLancamentoEmpenho->setDado( "estorno"      , true                       );
                $obTContabilidadeLancamentoEmpenho->inclusao();

                //Faz o lancamento com o novo valor prestado

                // Insere o Lote
                $obTContabilidadePrestacaoContas = new TContabilidadePrestacaoContas;
                $obTContabilidadePrestacaoContas->setDado( "tipo"          , 'P'                                 );
                $obTContabilidadePrestacaoContas->setDado( "nom_lote"      , "Prestação de Contas Empenho n° ".$_REQUEST['inCodEmpenho']."/".$_REQUEST['exercicio'] );
                $obTContabilidadePrestacaoContas->setDado( "dt_lote"       , $_REQUEST['stDtPrestacaoContas' ]   );
                $obTContabilidadePrestacaoContas->setDado( "exercicio"     , $_REQUEST['exercicio'           ]   );
                $obTContabilidadePrestacaoContas->setDado( "cod_entidade"  , $_REQUEST['inCodEntidade'       ]   );
                $obTContabilidadePrestacaoContas->insereLote( $inCodLote );

                // Insere Lancamentos
                $obTContabilidadeValorLancamento   = new TContabilidadeValorLancamento;
                $obTContabilidadeValorLancamento->setDado( "cod_lote"      , $inCodLote                                           );
                $obTContabilidadeValorLancamento->setDado( "tipo"          , 'P'                                                  );
                $obTContabilidadeValorLancamento->setDado( "exercicio"     , $_REQUEST['exercicio']                               );
                $obTContabilidadeValorLancamento->setDado( "cod_entidade"  , $_REQUEST['inCodEntidade']                           );
                $obTContabilidadeValorLancamento->setDado( "cod_plano_deb" , $stContaDebito                                       );
                $obTContabilidadeValorLancamento->setDado( "cod_plano_cred", $stContaCredito                                      );
                $obTContabilidadeValorLancamento->setDado( "cod_historico" , 980                                                  );
                $obTContabilidadeValorLancamento->setDado( "complemento"   , $_REQUEST['inCodEmpenho']."/".$_REQUEST['exercicio'] );
                $obTContabilidadeValorLancamento->setDado( "vl_lancamento" , $nuTotalPrestarContas );
                $obTContabilidadeValorLancamento->inclusaoPorPl( $rsRecordSet );

                // Insere em contabilidade.lancamento_empenho
                $obTContabilidadeLancamentoEmpenho =  new TContabilidadeLancamentoEmpenho;
                $obTContabilidadeLancamentoEmpenho->setDado( "cod_lote"     , $inCodLote                          );
                $obTContabilidadeLancamentoEmpenho->setDado( "tipo"         , "P"                                 );
                $obTContabilidadeLancamentoEmpenho->setDado( "sequencia"    , 1                                   );
                $obTContabilidadeLancamentoEmpenho->setDado( "exercicio"    , $_REQUEST['exercicio']              );
                $obTContabilidadeLancamentoEmpenho->setDado( "cod_entidade" , $_REQUEST['inCodEntidade']          );
                $obTContabilidadeLancamentoEmpenho->setDado( "estorno"      , false ) ;
                $obTContabilidadeLancamentoEmpenho->inclusao();

            // Faz lancamentos sem lancamento anteriores
            } elseif ($nuTotalAnteriorPrestarContas != $nuTotalPrestarContas) {

                // Insere o Lote
                $obTContabilidadePrestacaoContas = new TContabilidadePrestacaoContas;
                $obTContabilidadePrestacaoContas->setDado( "tipo"          , 'P'                                 );
                $obTContabilidadePrestacaoContas->setDado( "nom_lote"      , "Prestação de Contas Empenho n° ".$_REQUEST['inCodEmpenho']."/".$_REQUEST['exercicio'] );
                $obTContabilidadePrestacaoContas->setDado( "dt_lote"       , $_REQUEST['stDtPrestacaoContas' ]   );
                $obTContabilidadePrestacaoContas->setDado( "exercicio"     , $_REQUEST['exercicio'           ]   );
                $obTContabilidadePrestacaoContas->setDado( "cod_entidade"  , $_REQUEST['inCodEntidade'       ]   );
                $obTContabilidadePrestacaoContas->insereLote( $inCodLote );

                // Insere Lancamentos
                $obTContabilidadeValorLancamento   = new TContabilidadeValorLancamento;
                $obTContabilidadeValorLancamento->setDado( "cod_lote"      , $inCodLote                                           );
                $obTContabilidadeValorLancamento->setDado( "tipo"          , 'P'                                                  );
                $obTContabilidadeValorLancamento->setDado( "exercicio"     , $_REQUEST['exercicio']                               );
                $obTContabilidadeValorLancamento->setDado( "cod_entidade"  , $_REQUEST['inCodEntidade']                           );
                $obTContabilidadeValorLancamento->setDado( "cod_plano_deb" , $stContaDebito                                       );
                $obTContabilidadeValorLancamento->setDado( "cod_plano_cred", $stContaCredito                                      );
                $obTContabilidadeValorLancamento->setDado( "cod_historico" , 980                                                  );
                $obTContabilidadeValorLancamento->setDado( "complemento"   , $_REQUEST['inCodEmpenho']."/".$_REQUEST['exercicio'] );
                $obTContabilidadeValorLancamento->setDado( "vl_lancamento" , $nuTotalPrestarContas );
                $obTContabilidadeValorLancamento->inclusaoPorPl( $rsRecordSet );

                // Insere em contabilidade.lancamento_empenho
                $obTContabilidadeLancamentoEmpenho =  new TContabilidadeLancamentoEmpenho;
                $obTContabilidadeLancamentoEmpenho->setDado( "cod_lote"     , $inCodLote                          );
                $obTContabilidadeLancamentoEmpenho->setDado( "tipo"         , "P"                                 );
                $obTContabilidadeLancamentoEmpenho->setDado( "sequencia"    , 1                                   );
                $obTContabilidadeLancamentoEmpenho->setDado( "exercicio"    , $_REQUEST['exercicio']              );
                $obTContabilidadeLancamentoEmpenho->setDado( "cod_entidade" , $_REQUEST['inCodEntidade']          );
                $obTContabilidadeLancamentoEmpenho->setDado( "estorno"      , false ) ;
                $obTContabilidadeLancamentoEmpenho->inclusao();

            }

            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=incluir","Empenho ".$_REQUEST['inCodEmpenho']."/".$_REQUEST['exercicio'],"alterar","aviso",Sessao::getId(),"");

        }

        if (!$boErro) {
            $stCaminho = CAM_GF_EMP_INSTANCIAS."adiantamentos/OCGeraNotaPrestacaoContas.php";
            $stCaminho .= '?'.Sessao::getId();
            $stCaminho .= http_build_query($_REQUEST);
            SistemaLegado::executaFrameOculto( "window.location.href = '".$stCaminho."';" );
        }

        Sessao::encerraExcecao();
}

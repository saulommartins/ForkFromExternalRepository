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
    * @author Desenvolvedor : Luciano Hoffmann

    * @ignore

    * Casos de uso: uc-02.03.31
*/

/*
$Log$
Revision 1.1  2007/08/10 14:28:46  luciano
movido de lugar

*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include mapeamentos
include_once( TEMP."TEmpenhoItemPrestacaoContas.class.php"                                           );
include_once( TEMP."TEmpenhoItemPrestacaoContasAnulado.class.php"                                    );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePrestacaoContas.class.php"   );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php"   );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoEmpenho.class.php" );
include_once ( TEMP."TEmpenhoResponsavelAdiantamento.class.php");

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];

$stPrograma = "ManterAdiantamentosSubvencoes";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );
$obTEmpenhoItemPrestacaoContasAnulado = new TEmpenhoItemPrestacaoContasAnulado;
Sessao::getTransacao()->setMapeamento( $obTEmpenhoItemPrestacaoContasAnulado );

$boErro = false;

switch ($_REQUEST['stAcao']) {

    case 'anular':
        $arValoresAnular = Sessao::read('arValoresAnular');
        if (count($arValoresAnular) == 0) {
            SistemaLegado::exibeAviso('Deve existir ao menos um item a anular.', "n_incluir", "erro" );
            $boErro = true;
        }

        if (!$boErro) {

            // Busca o valor total prestado contas
            $obTEmpenhoItemPrestacaoContas = new TEmpenhoItemPrestacaoContas;
            $obTEmpenhoItemPrestacaoContas->setDado('exercicio'   ,$_REQUEST['exercicio']);
            $obTEmpenhoItemPrestacaoContas->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
            $obTEmpenhoItemPrestacaoContas->setDado('cod_empenho' ,$_REQUEST['inCodEmpenho']);
            $obTEmpenhoItemPrestacaoContas->recuperaValorPrestado( $rsValorPrestado );

            $nuTotalPrestado = $rsValorPrestado->getCampo('vl_prestado');

            // Busca as contas lancamento e contrapartida
            $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
            $obTEmpenhoResponsavelAdiantamento->setDado('exercicio'           ,$_REQUEST['exercicio']);
            $obTEmpenhoResponsavelAdiantamento->setDado('numcgm'              ,$_REQUEST['inCodCredor']);
            $obTEmpenhoResponsavelAdiantamento->setDado('conta_contrapartida' ,$_REQUEST['inCodContrapartida']);
            $obTEmpenhoResponsavelAdiantamento->recuperaPorChave($rsContas);

            $stContaDebito   = $rsContas->getCampo('conta_contrapartida');
            $stContaCredito  = $rsContas->getCampo('conta_lancamento');

            // Insere itens ANULADOS em empenho.item_prestacao_contas_anulado
            foreach ($arValoresAnular as $arItem) {

                $obTEmpenhoItemPrestacaoContasAnulado->setDado( "cod_empenho" , $_REQUEST['inCodEmpenho'] );
                $obTEmpenhoItemPrestacaoContasAnulado->setDado( "cod_entidade", $_REQUEST['inCodEntidade']);
                $obTEmpenhoItemPrestacaoContasAnulado->setDado( "exercicio"   , $_REQUEST['exercicio']    );
                $obTEmpenhoItemPrestacaoContasAnulado->setDado( "num_item"    , $arItem['numItem']        );
                $obTEmpenhoItemPrestacaoContasAnulado->inclusao();

            }

            // Busca o novo valor total prestado contas após as anulacoes
            $obTEmpenhoItemPrestacaoContas = new TEmpenhoItemPrestacaoContas;
            $obTEmpenhoItemPrestacaoContas->setDado('exercicio'   ,$_REQUEST['exercicio']);
            $obTEmpenhoItemPrestacaoContas->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
            $obTEmpenhoItemPrestacaoContas->setDado('cod_empenho' ,$_REQUEST['inCodEmpenho']);
            $obTEmpenhoItemPrestacaoContas->recuperaValorPrestado( $rsValorPrestado );

            $nuValorPrestar = $rsValorPrestado->getCampo('vl_prestado');

            // LANCAMENTOS CONTABEIS

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
            $obTContabilidadeValorLancamento->setDado( "vl_lancamento" , $nuTotalPrestado                                     );
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
            if ($nuValorPrestar > 0) {
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
                $obTContabilidadeValorLancamento->setDado( "vl_lancamento" , $nuValorPrestar );
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

            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=anular","Empenho ".$_REQUEST['inCodEmpenho']."/".$_REQUEST['exercicio'],"alterar","aviso",Sessao::getId(),"");

        }
}

Sessao::encerraExcecao();

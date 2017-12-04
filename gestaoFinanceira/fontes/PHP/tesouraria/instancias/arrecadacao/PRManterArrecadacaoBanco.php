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
    * Página de Processamento para Arrecadacao do módulo Tesouraria
    * Data de Criação   : 05/03/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Lucas Stephanou

    * @ignore

    * $Id: PRManterArrecadacaoBanco.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.33
*/
// caso tenha muitos lotes pendentes a rotina pode levar bastante tempo
set_time_limit(0);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoBanco";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAutenticacao = "../autenticacao/FMManterAutenticacao.php";

if (!$_REQUEST['inCodBoletim']) {
    SistemaLegado::exibeAviso(urlencode("<i><b>Boletim</b></i> deve ser selecionado!"),"n_alterar","erro");
} else {

    // buscar lotes a serem arrecadados
    $arLotes = array();
    foreach ($_REQUEST as $key => $value) {
        if ( preg_match("/^boArrecadar/", $key) ) {
                $arTmp = explode("_", $key);
                $arLotes[] = array( "cod_lote" => $arTmp[1] , "exercicio" => $arTmp[2] );
        }
    } // $arLotes contem lotes a serem arrecadados

    if ( !count($arLotes) ) {
        $stMensagem = ( $stAcao == 'incluir' ) ? 'Arrecadar' :	'Estornar' ;
        SistemaLegado::exibeAviso(urlencode(" &nbsp;Nenhum Lote para ".$stMensagem."! &nbsp;"),"n_alterar","erro");
        exit;
    }

    list($inCodBoletimAberto,$stDtBoletimAberto) = explode ( ':' , $_REQUEST['inCodBoletim']);
    list($stDia, $stMes, $stAno) = explode( '/', $stDtBoletimAberto );

    //valida a utilização da rotina de encerramento do mês contábil
    $boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
    $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
    $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
    $obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
    $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

    if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $stMes) {
        SistemaLegado::exibeAviso(urlencode("Mês do Boletim encerrado!"),"n_incluir","erro");
        exit;
    }

    switch ($stAcao) {
    case 'incluir':
        // carregar arquivos de classes necessarios
        require_once CAM_GF_TES_MAPEAMENTO."TTesourariaAutenticacao.class.php";
        require_once CAM_GF_TES_MAPEAMENTO."TTesourariaArrecadacao.class.php";
        require_once CAM_GF_TES_MAPEAMENTO."TTesourariaArrecadacaoReceita.class.php";
        require_once CAM_GF_TES_MAPEAMENTO."TTesourariaTransferencia.class.php";
        require_once CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLoteArrecadacao.class.php";
        require_once CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLoteTransferencia.class.php";
        require_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php";
        require_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php";

        //inicia a transacao
        Sessao::setTrataExcecao(true);

        $obTBoletimLoteArrecadacao = new TTesourariaBoletimLoteArrecadacao();
        $obTBoletimLoteTransferencia = new TTesourariaBoletimLoteTransferencia();

        Sessao::getTransacao()->setMapeamento($obTBoletimLote);

        $obTAutenticacao = new TTesourariaAutenticacao();
        $obTArrecadacao = new TTesourariaArrecadacao();
        $obTArrecadacaoReceita = new TTesourariaArrecadacaoReceita();
        $obTTransferencia = new TTesourariaTransferencia();
        $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;

        $inCodEntidade = $_REQUEST['inCodEntidade'];

        list($stDia, $stMes, $stAno) = explode('/', $stDtBoletimAberto);
        $stTimestampArrecadacao = $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms');

        foreach ($arLotes as $Lote) {

            $arParametros = array(  "cod_lote"    => $Lote['cod_lote']
                                ,   "exercicio"	  => $Lote['exercicio']
                                ,   "cod_entidade"=> $inCodEntidade
                                ,   "cod_boletim" => $inCodBoletimAberto );

            $obTBoletimLoteArrecadacao->recuperaFuncaoListaPagamentosLote($rsPagamentos,$arParametros);

            while ( !$rsPagamentos->eof() ) {
                if ($rsPagamentos->getCampo('soma') > 0) {
                    switch ( $rsPagamentos->getCampo("tipo")) {
                    case 'extra':
                        $obTAutenticacao->proximoCod( $inCodAutenticacao );
                        $obTAutenticacao->setDado("cod_autenticacao" , $inCodAutenticacao );
                        $obTAutenticacao->setDado("dt_autenticacao" , $stDtBoletimAberto );
                        $obTAutenticacao->setDado("tipo" , "T");
                        $obTAutenticacao->inclusao();

                        // inserir valores na contabilidade
                        $obTContabilidadeValorLancamento->setDado( "cod_lote"      , ''                   );
                        $obTContabilidadeValorLancamento->setDado( "tipo"          , 'T'                                     );
                        $obTContabilidadeValorLancamento->setDado( "exercicio"     , $rsPagamentos->getCampo('exercicio'));
                        $obTContabilidadeValorLancamento->setDado( "cod_entidade"  , $inCodEntidade                          );
                        $obTContabilidadeValorLancamento->setDado( "cod_plano_deb" , $rsPagamentos->getCampo('cod_plano') );
                        $obTContabilidadeValorLancamento->setDado( "cod_plano_cred", $rsPagamentos->getCampo('codigo') ); // cod_plano do banco
                        $obTContabilidadeValorLancamento->setDado( "cod_historico" , 991                                     );
                        $obTContabilidadeValorLancamento->setDado( "nom_lote" 	 , "Transferência - CD:".$rsPagamentos->getCampo('codigo')." | CC:".$rsPagamentos->getCampo('cod_plano') );
                        $obTContabilidadeValorLancamento->setDado( "complemento"   , "" );
                        $obTContabilidadeValorLancamento->setDado( "vl_lancamento" , $rsPagamentos->getCampo('soma') );
                        $obTContabilidadeValorLancamento->inclusaoPorPl( $rsRecordSet );

                        // buscar dados do lote da contabilidade
                        $obTContabilidadeLote = new TContabilidadeLote();
                        $obTContabilidadeLote->setDado("cod_entidade",$inCodEntidade);
                        $obTContabilidadeLote->setDado("exercicio",$rsPagamentos->getCampo("exercicio"));
                        $obTContabilidadeLote->setDado("tipo", "T");
                        $obTContabilidadeLote->recuperaUltimoLotePorEntidade($rsLoteInserido);

                        $inCodLote = $rsLoteInserido->getCampo("cod_lote");

                        $obTTransferencia->setDado("cod_lote", $inCodLote);
                        $obTTransferencia->setDado("exercicio",$rsPagamentos->getCampo('exercicio'));
                        $obTTransferencia->setDado("cod_entidade",$inCodEntidade);
                        $obTTransferencia->setDado("tipo",'T');
                        $obTTransferencia->setDado("cod_autenticacao",$inCodAutenticacao);
                        $obTTransferencia->setDado("dt_autenticacao",$stDtBoletimAberto);
                        $obTTransferencia->setDado("cod_boletim",$inCodBoletimAberto);
                        $obTTransferencia->setDado("cod_historico",991);
                        $obTTransferencia->setDado("cod_terminal",$_REQUEST['inCodTerminal']);
                        $obTTransferencia->setDado("timestamp_terminal",$_REQUEST['stTimestampTerminal']);
                        $obTTransferencia->setDado("timestamp_usuario",$_REQUEST['stTimestampUsuario']);
                        $obTTransferencia->setDado("cgm_usuario", Sessao::read('numCgm') );
                        $obTTransferencia->setDado("timestamp_transferencia", $stTimestampArrecadacao );
                        $obTTransferencia->setDado("observacao","Arrecadação via Banco");
                        $obTTransferencia->setDado("cod_plano_credito",$rsPagamentos->getCampo('codigo'));
                        $obTTransferencia->setDado("cod_plano_debito",$rsPagamentos->getCampo('cod_plano'));
                        $obTTransferencia->setDado("valor",$rsPagamentos->getCampo( 'soma' ));
                        $obTTransferencia->setDado("cod_tipo",2);
                        $obTTransferencia->inclusao();

                        $obTBoletimLoteTransferencia->setDado( 'tipo', 'T' );
                        $obTBoletimLoteTransferencia->setDado( 'exercicio', $rsPagamentos->getCampo('exercicio') );
                        $obTBoletimLoteTransferencia->setDado( 'cod_entidade', $inCodEntidade );
                        $obTBoletimLoteTransferencia->setDado( 'cod_lote', $inCodLote );
                        $obTBoletimLoteTransferencia->setDado( 'cod_lote_arrecadacao', $rsPagamentos->getCampo('cod_lote') );
                        $obTBoletimLoteTransferencia->inclusao();

                        break;
                    case 'orc':
                        $obTAutenticacao->proximoCod( $inCodAutenticacao );
                        $obTAutenticacao->setDado("cod_autenticacao" , $inCodAutenticacao );
                        $obTAutenticacao->setDado("dt_autenticacao" , $stDtBoletimAberto );
                        $obTAutenticacao->setDado("tipo" , "A");
                        $obTAutenticacao->inclusao();

                        $obTArrecadacao->proximoCod( $inCodArrecadacao );
                        $obTArrecadacao->setDado( "cod_arrecadacao" , $inCodArrecadacao );
                        $obTArrecadacao->setDado( "exercicio" , $Lote['exercicio'] );
                        $obTArrecadacao->setDado( "cod_autenticacao" , $inCodAutenticacao );
                        $obTArrecadacao->setDado( "cod_boletim" , $inCodBoletimAberto );
                        $obTArrecadacao->setDado( "dt_autenticacao" , $stDtBoletimAberto );
                        $obTArrecadacao->setDado( "cod_terminal" , $_REQUEST['inCodTerminal'] );
                        $obTArrecadacao->setDado( "timestamp_arrecadacao" , $stTimestampArrecadacao );
                        $obTArrecadacao->setDado( "timestamp_terminal" , $_REQUEST['stTimestampTerminal'] );
                        $obTArrecadacao->setDado( "cgm_usuario" , Sessao::read('numCgm') );
                        $obTArrecadacao->setDado( "timestamp_usuario" , $_REQUEST['stTimestampUsuario'] );
                        $obTArrecadacao->setDado( "cod_plano" , $rsPagamentos->getCampo('cod_plano') );
                        $obTArrecadacao->setDado( "cod_entidade" , $inCodEntidade );
                        $obTArrecadacao->setDado( "observacao" , "Arrecadação via Banco do Lote " . $Lote['cod_lote'] . "/" . $Lote['exercicio'] );
                        $obTArrecadacao->inclusao();

                        $obTArrecadacaoReceita->setDado( "cod_arrecadacao" , $inCodArrecadacao );
                        $obTArrecadacaoReceita->setDado( "cod_receita" , $rsPagamentos->getCampo('codigo') );
                        $obTArrecadacaoReceita->setDado( "timestamp_arrecadacao" , $obTArrecadacao->getDado("timestamp_arrecadacao") );
                        $obTArrecadacaoReceita->setDado( "exercicio" , $rsPagamentos->getCampo('exercicio') );
                        $obTArrecadacaoReceita->setDado( "vl_arrecadacao" , $rsPagamentos->getCampo( 'soma' ) );
                        $obTArrecadacaoReceita->inclusao();

                        $obTBoletimLoteArrecadacao->setDado( 'exercicio', $rsPagamentos->getCampo('exercicio') );
                        $obTBoletimLoteArrecadacao->setDado( 'cod_entidade', $inCodEntidade );
                        $obTBoletimLoteArrecadacao->setDado( 'cod_boletim', $inCodBoletimAberto );
                        $obTBoletimLoteArrecadacao->setDado( 'cod_lote', $rsPagamentos->getCampo('cod_lote') );
                        $obTBoletimLoteArrecadacao->setDado( 'timestamp_arrecadacao', $stTimestampArrecadacao );
                        $obTBoletimLoteArrecadacao->setDado( 'cod_arrecadacao', $inCodArrecadacao );
                        $obTBoletimLoteArrecadacao->inclusao();

                        break;
                    }
                }
                /*$obTBoletimLote->setDado( "cod_arrecadacao" , $inCodArrecadacao );
                $obTBoletimLote->setDado( "cod_entidade" , $inCodEntidade );
                $obTBoletimLote->setDado( "cod_boletim" , $inCodBoletimAberto );
                $obTBoletimLote->setDado( "exercicio" , $Lote['exercicio'] );
                $obTBoletimLote->setDado( "timestamp_arrecadacao" , $obTArrecadacao->getDado("timestamp_arrecadacao") );
                $obTBoletimLote->setDado( "cod_lote" , $Lote['cod_lote'] );
                $obTBoletimLote->inclusao();*/

                $rsPagamentos->proximo();
            }
        }
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),'Boletim '.$stDtBoletimAberto,"incluir","aviso", Sessao::getId(), "../");
        break;
    case 'estornar' :

        //incluir os mapeamentos
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaAutenticacao.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaBoletimLoteArrecadacao.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaBoletimLoteArrecadacaoEstornado.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaBoletimLoteTransferencia.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaBoletimLoteTransferenciaEstornada.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaArrecadacao.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaTransferencia.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaTransferenciaEstornada.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaArrecadacaoEstornada.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaTransferenciaEstornada.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaArrecadacaoReceita.class.php");
        require_once( CAM_GF_TES_MAPEAMENTO . "TTesourariaArrecadacaoEstornadaReceita.class.php");
        require_once( CAM_GF_CONT_MAPEAMENTO. "TContabilidadeValorLancamento.class.php" );
        require_once( CAM_GF_CONT_MAPEAMENTO. "TContabilidadeLote.class.php" );

        //instanciar os mapeametos
        $obTBoletimLoteArrecadacao = new TTesourariaBoletimLoteArrecadacao();
        $obTBoletimLoteArrecadacaoEstornado = new TTesourariaBoletimLoteArrecadacaoEstornado();
        $obTBoletimLoteTransferencia = new TTesourariaBoletimLoteTransferencia();
        $obTBoletimLoteTransferenciaEstornada = new TTesourariaBoletimLoteTransferenciaEstornada();
        $obTTesourariaArrecadacao = new TTesourariaArrecadacao();
        $obTTesourariaArrecadacaoEstornada = new TTesourariaArrecadacaoEstornada();
        $obTTesourariaArrecadacaoReceita = new TTesourariaArrecadacaoReceita();
        $obTTesourariaArrecadacaoEstornadaReceita = new TTesourariaArrecadacaoEstornadaReceita();
        $obTTesourariaTransferencia = new TTesourariaTransferencia();
        $obTTesourariaTransferenciaEstornada = new TTesourariaTransferenciaEstornada();
        $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
        $obTAutenticacao = new TTesourariaAutenticacao();

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTBoletimLoteArrecadacao );

        //gera um timestamp, suloção temporária até ser revisado o caso de uso da arrecadação orçamententária
        //e extra orçamentária e o estorno das mesmas
        list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletimAberto );
        $stTimestampArrecadacao = $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms');

        foreach ($arLotes as $arLote) {

            //recuperando os lotes a serem estornados
            $stFiltro = "
             where boletim_lote_arrecadacao.exercicio = '".$arLote['exercicio']."'
               and boletim_lote_arrecadacao.cod_entidade = ".$_REQUEST['inCodEntidade']."
               and boletim_lote_arrecadacao.cod_boletim = ".$inCodBoletimAberto."
               and boletim_lote_arrecadacao.cod_lote = ".$arLote['cod_lote']."
               and not exists ( select 1
                                  from tesouraria.boletim_lote_arrecadacao_estornado
                                 where boletim_lote_arrecadacao_estornado.exercicio = boletim_lote_arrecadacao.exercicio
                                   and boletim_lote_arrecadacao_estornado.cod_entidade = boletim_lote_arrecadacao.cod_entidade
                                   and boletim_lote_arrecadacao_estornado.cod_boletim = boletim_lote_arrecadacao.cod_boletim
                                   and boletim_lote_arrecadacao_estornado.cod_lote = boletim_lote_arrecadacao.cod_lote
                                   and boletim_lote_arrecadacao_estornado.timestamp_arrecadacao = boletim_lote_arrecadacao.timestamp_arrecadacao
                                   and boletim_lote_arrecadacao_estornado.cod_arrecadacao = boletim_lote_arrecadacao.cod_arrecadacao
                              )
            ";
            $obTBoletimLoteArrecadacao->recuperaTodos( $rsBoletimLoteArrecadacao, $stFiltro );
            //$obTBoletimLoteArrecadacao->debug();

            while ( !$rsBoletimLoteArrecadacao->eof() ) {

                $obTAutenticacao->proximoCod( $inCodAutenticacao );
                $obTAutenticacao->setDado("cod_autenticacao" , $inCodAutenticacao );
                $obTAutenticacao->setDado("dt_autenticacao" , $stDtBoletimAberto );
                $obTAutenticacao->setDado("tipo" , "EA");
                $obTAutenticacao->inclusao();

                //incluindo os estornos na tabela tesouraria.boletim_lote_estornado
                $obTBoletimLoteArrecadacaoEstornado->setDado( 'exercicio'		, $rsBoletimLoteArrecadacao->getCampo('exercicio')		);
                $obTBoletimLoteArrecadacaoEstornado->setDado( 'cod_entidade'	, $rsBoletimLoteArrecadacao->getCampo('cod_entidade') 	);
                $obTBoletimLoteArrecadacaoEstornado->setDado( 'cod_boletim'	, $rsBoletimLoteArrecadacao->getCampo('cod_boletim') 	);
                $obTBoletimLoteArrecadacaoEstornado->setDado( 'cod_lote'		, $rsBoletimLoteArrecadacao->getCampo('cod_lote')		);
                $obTBoletimLoteArrecadacaoEstornado->setDado( 'timestamp_arrecadacao', $rsBoletimLoteArrecadacao->getCampo('timestamp_arrecadacao') );
                $obTBoletimLoteArrecadacaoEstornado->setDado( 'cod_arrecadacao', $rsBoletimLoteArrecadacao->getCampo('cod_arrecadacao') );
                $obTBoletimLoteArrecadacaoEstornado->setDado( 'timestamp_anulacao', $stTimestampArrecadacao );
                $obTBoletimLoteArrecadacaoEstornado->inclusao();

                //recuperando arrecadações
                $obTTesourariaArrecadacao->setDado( 'exercicio'				, $rsBoletimLoteArrecadacao->getCampo('exercicio') );
                $obTTesourariaArrecadacao->setDado( 'timestamp_arrecadacao'	, $rsBoletimLoteArrecadacao->getCampo('timestamp_arrecadacao') );
                $obTTesourariaArrecadacao->setDado( 'cod_arrecadacao'		, $rsBoletimLoteArrecadacao->getCampo('cod_arrecadacao') );
                $obTTesourariaArrecadacao->recuperaPorChave( $rsArrecadacao );

                //incluindo os estornos na tabela tesouraria.arrecadacao_estornada
                $obTTesourariaArrecadacaoEstornada->setDado( 'exercicio'			, $rsArrecadacao->getCampo('exercicio') );
                $obTTesourariaArrecadacaoEstornada->setDado( 'cod_arrecadacao'		, $rsArrecadacao->getCampo('cod_arrecadacao') );
                $obTTesourariaArrecadacaoEstornada->setDado( 'timestamp_arrecadacao', $rsArrecadacao->getCampo('timestamp_arrecadacao') );
                $obTTesourariaArrecadacaoEstornada->setDado( 'cod_autenticacao'		, $inCodAutenticacao );
                $obTTesourariaArrecadacaoEstornada->setDado( 'dt_autenticacao'		, $stDtBoletimAberto );
                $obTTesourariaArrecadacaoEstornada->setDado( 'cod_terminal'			, $rsArrecadacao->getCampo('cod_terminal') );
                $obTTesourariaArrecadacaoEstornada->setDado( 'timestamp_terminal'	, $rsArrecadacao->getCampo('timestamp_terminal') );
                $obTTesourariaArrecadacaoEstornada->setDado( 'cgm_usuario'			, $rsArrecadacao->getCampo('cgm_usuario') );
                $obTTesourariaArrecadacaoEstornada->setDado( 'timestamp_usuario'	, $rsArrecadacao->getCampo('timestamp_usuario') );
                $obTTesourariaArrecadacaoEstornada->setDado( 'observacao'			, 'Estorno de Arrecadação via Banco do Lote '.$rsBoletimLoteArrecadacao->getCampo('cod_lote').'/'.$rsArrecadacao->getCampo('exercicio') );
                $obTTesourariaArrecadacaoEstornada->setDado( 'cod_entidade'			, $rsArrecadacao->getCampo('cod_entidade') );
                $obTTesourariaArrecadacaoEstornada->setDado( 'cod_boletim'			, $rsArrecadacao->getCampo('cod_boletim') );
                $obTTesourariaArrecadacaoEstornada->setDado( 'timestamp_estornada'	, $stTimestampArrecadacao );
                $obTTesourariaArrecadacaoEstornada->inclusao();

                //recuperando as receitas
                $obTTesourariaArrecadacaoReceita->setDado( 'exercicio' 				, $rsArrecadacao->getCampo('exercicio') );
                $obTTesourariaArrecadacaoReceita->setDado( 'cod_arrecadacao'		, $rsArrecadacao->getCampo('cod_arrecadacao') );
                $obTTesourariaArrecadacaoReceita->setDado( 'timestamp_arrecadacao'	, $rsArrecadacao->getCampo('timestamp_arrecadacao') );
                $obTTesourariaArrecadacaoReceita->recuperaPorChave( $rsReceita );
                //$obTTesourariaArrecadacaoReceita->debug();

                $obTTesourariaArrecadacaoEstornadaReceita->setDado( 'timestamp_estornada'	, $stTimestampArrecadacao 					 	);
                $obTTesourariaArrecadacaoEstornadaReceita->setDado( 'timestamp_arrecadacao'	, $rsReceita->getCampo('timestamp_arrecadacao')	);
                $obTTesourariaArrecadacaoEstornadaReceita->setDado( 'exercicio'				, $rsReceita->getCampo('exercicio') 			);
                $obTTesourariaArrecadacaoEstornadaReceita->setDado( 'cod_arrecadacao'		, $rsReceita->getCampo('cod_arrecadacao') 		);
                $obTTesourariaArrecadacaoEstornadaReceita->setDado( 'cod_receita'			, $rsReceita->getCampo('cod_receita') 			);
                $obTTesourariaArrecadacaoEstornadaReceita->setDado( 'vl_estornado'			, $rsReceita->getCampo('vl_arrecadacao')		);
                $obTTesourariaArrecadacaoEstornadaReceita->inclusao();

                $rsBoletimLoteArrecadacao->proximo();
            }

            $obTBoletimLoteTransferencia->setDado( 'exercicio'		, $arLote['exercicio'] 		 	);
            $obTBoletimLoteTransferencia->setDado( 'cod_entidade'	, $_REQUEST['inCodEntidade'] 	);
            $obTBoletimLoteTransferencia->setDado( 'tipo' , 'T' );
            $obTBoletimLoteTransferencia->setDado( 'cod_lote_arrecadacao'		, $arLote['cod_lote']			);

            $stFiltro = "
             where boletim_lote_transferencia.exercicio = '".$arLote['exercicio']."'
               and boletim_lote_transferencia.cod_entidade = ".$_REQUEST['inCodEntidade']."
               and boletim_lote_transferencia.tipo = 'T'
               and boletim_lote_transferencia.cod_lote_arrecadacao = ".$arLote['cod_lote']."
               and not exists ( select 1
                                  from tesouraria.boletim_lote_transferencia_estornada
                                 where boletim_lote_transferencia_estornada.exercicio = boletim_lote_transferencia.exercicio
                                   and boletim_lote_transferencia_estornada.cod_entidade = boletim_lote_transferencia.cod_entidade
                                   and boletim_lote_transferencia_estornada.cod_lote = boletim_lote_transferencia.cod_lote
                                   and boletim_lote_transferencia_estornada.cod_lote_arrecadacao = boletim_lote_transferencia.cod_lote_arrecadacao
                                   and boletim_lote_transferencia_estornada.tipo = boletim_lote_transferencia.tipo
                              )
            ";

            $obTBoletimLoteTransferencia->recuperaTodos( $rsBoletimLoteTransferencia, $stFiltro );
            //$obTBoletimLoteTransferencia->debug();

            while ( !$rsBoletimLoteTransferencia->eof() ) {
                $obTAutenticacao->proximoCod( $inCodAutenticacao );
                $obTAutenticacao->setDado("cod_autenticacao" , $inCodAutenticacao );
                $obTAutenticacao->setDado("dt_autenticacao" , $stDtBoletimAberto );
                $obTAutenticacao->setDado("tipo" , "E");
                $obTAutenticacao->inclusao();

                $obTBoletimLoteTransferenciaEstornada->setDado( 'cod_lote' , $rsBoletimLoteTransferencia->getCampo('cod_lote') );
                $obTBoletimLoteTransferenciaEstornada->setDado( 'cod_lote_arrecadacao', $rsBoletimLoteTransferencia->getCampo('cod_lote_arrecadacao') );
                $obTBoletimLoteTransferenciaEstornada->setDado( 'cod_entidade', $rsBoletimLoteTransferencia->getCampo('cod_entidade') );
                $obTBoletimLoteTransferenciaEstornada->setDado( 'exercicio', $rsBoletimLoteTransferencia->getCampo('exercicio') );
                $obTBoletimLoteTransferenciaEstornada->setDado( 'tipo', $rsBoletimLoteTransferencia->getCampo('tipo') );
                $obTBoletimLoteTransferenciaEstornada->inclusao();

                $obTTesourariaTransferencia->setDado( 'exercicio'	, $arLote['exercicio'] );
                $obTTesourariaTransferencia->setDado( 'cod_lote'	, $rsBoletimLoteTransferencia->getCampo('cod_lote') );
                $obTTesourariaTransferencia->setDado( 'tipo'		, 'T' );
                $obTTesourariaTransferencia->setDado( 'cod_entidade', $_REQUEST['inCodEntidade']  	);

                $obTTesourariaTransferencia->recuperaPorChave( $rsTransferencia );

                $obTContabilidadeValorLancamento->setDado( "cod_lote"      , ''  );
                $obTContabilidadeValorLancamento->setDado( "tipo"          , 'T' );
                $obTContabilidadeValorLancamento->setDado( "exercicio"     , $rsTransferencia->getCampo('exercicio'));
                $obTContabilidadeValorLancamento->setDado( "cod_entidade"  , $inCodEntidade );
                $obTContabilidadeValorLancamento->setDado( "cod_plano_deb" , $rsTransferencia->getCampo('cod_plano_credito') );
                $obTContabilidadeValorLancamento->setDado( "cod_plano_cred", $rsTransferencia->getCampo('cod_plano_debito') ); // cod_plano do banco
                $obTContabilidadeValorLancamento->setDado( "cod_historico" , 992                                     );
                $obTContabilidadeValorLancamento->setDado( "nom_lote" 	   , "Transferência - CD:".$rsTransferencia->getCampo('cod_plano_credito')." | CC:".$rsTransferencia->getCampo('cod_plano_debito') );
                $obTContabilidadeValorLancamento->setDado( "complemento"   , "" );
                $obTContabilidadeValorLancamento->setDado( "vl_lancamento" , $rsTransferencia->getCampo('valor') );
                $obTContabilidadeValorLancamento->inclusaoPorPl( $rsRecordSet );

                $obTContabilidadeLote = new TContabilidadeLote();
                $obTContabilidadeLote->setDado("cod_entidade",$inCodEntidade);
                $obTContabilidadeLote->setDado("exercicio",$rsTransferencia->getCampo("exercicio"));
                $obTContabilidadeLote->setDado("tipo", "T");
                $obTContabilidadeLote->recuperaUltimoLotePorEntidade($rsLoteInserido);

                $inCodLote = $rsLoteInserido->getCampo("cod_lote");

                $obTTesourariaTransferenciaEstornada->setDado( 'cod_lote_estorno'   , $inCodLote );
                $obTTesourariaTransferenciaEstornada->setDado( 'cod_lote'			, $rsTransferencia->getCampo('cod_lote') );
                $obTTesourariaTransferenciaEstornada->setDado( 'exercicio'			, $rsTransferencia->getCampo('exercicio') );
                $obTTesourariaTransferenciaEstornada->setDado( 'cod_entidade'		, $rsTransferencia->getCampo('cod_entidade') );
                $obTTesourariaTransferenciaEstornada->setDado( 'tipo'				, $rsTransferencia->getCampo('tipo') );
                $obTTesourariaTransferenciaEstornada->setDado( 'cod_autenticacao'	, $inCodArrecadacao  );
                $obTTesourariaTransferenciaEstornada->setDado( 'dt_autenticacao'	, $stDtBoletimAberto );
                $obTTesourariaTransferenciaEstornada->setDado( 'cod_boletim'		, $rsTransferencia->getCampo('cod_boletim') );
                $obTTesourariaTransferenciaEstornada->setDado( 'cod_historico'		, $rsTransferencia->getCampo('cod_historico') );
                $obTTesourariaTransferenciaEstornada->setDado( 'cod_terminal'		, $rsTransferencia->getCampo('cod_terminal') );
                $obTTesourariaTransferenciaEstornada->setDado( 'timestamp_terminal' , $rsTransferencia->getCampo('timestamp_terminal') );
                $obTTesourariaTransferenciaEstornada->setDado( 'cgm_usuario'		, $rsTransferencia->getCampo('cgm_usuario') );
                $obTTesourariaTransferenciaEstornada->setDado( 'timestamp_usuario'	, $rsTransferencia->getCampo('timestamp_usuario') );
                $obTTesourariaTransferenciaEstornada->setDado( 'timestamp_transferencia', $rsTransferencia->getCampo('timestamp_transferencia') );
                $obTTesourariaTransferenciaEstornada->setDado( 'observacao'			, $rsTransferencia->getCampo('observacao') );
                $obTTesourariaTransferenciaEstornada->setDado( 'cod_plano_credito'	, $rsTransferencia->getCampo('cod_plano_credito') );
                $obTTesourariaTransferenciaEstornada->setDado( 'cod_plano_debito'	, $rsTransferencia->getCampo('cod_plano_debito') );
                $obTTesourariaTransferenciaEstornada->setDado( 'valor'				, $rsTransferencia->getCampo('valor') );
                $obTTesourariaTransferenciaEstornada->setDado( 'cod_tipo'			, $rsTransferencia->getCampo('cod_tipo') );
                $obTTesourariaTransferenciaEstornada->setDado( 'timestamp_estornada', $stTimestampArrecadacao );
                $obTTesourariaTransferenciaEstornada->inclusao();

                $rsBoletimLoteTransferencia->proximo();
            }

        }
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=estornar",'Boletim ' . $stDtBoletimAberto,"incluir","aviso", Sessao::getId(), "../");
        break;
    }

    Sessao::encerraExcecao();
}
?>

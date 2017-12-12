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
    * Data de Criação   : 18/11/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 28751 $
    $Name$
    $Author: grasiele $
    $Date: 2008-03-26 09:59:59 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.04.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
include_once(CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php" );
include_once(CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php");
include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceitaCredito.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceitaCreditoAcrescimo.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceitaCreditoDesconto.class.php");

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAutenticacao = "../autenticacao/FMManterAutenticacao.php";

$segue = FALSE;

list($inCodBoletimAberto,$stDtBoletimAberto) = explode ( ':' , $_REQUEST['inCodBoletim']);
list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletimAberto );
$stTimestampArrecadacao = $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms');

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $stMes) {
    SistemaLegado::exibeAviso(urlencode("Mês do Boletim encerrado!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit;
}

if (!$_REQUEST['inCodBoletim']) {
    SistemaLegado::exibeAviso(urlencode(" <i><b>Boletim</b></i> deve ser selecionado! "),"n_alterar","erro");
    SistemaLegado::LiberaFrames();
} else {
    if ($_REQUEST['stCodBarraOtico'] == '' && $_REQUEST['stCodBarraManual'] == '' && !$_REQUEST["inCodReceita"]) {
        SistemaLegado::exibeAviso(urlencode("Campo <i><b>Receita</b></i> vazio! "),"n_alterar","erro");
        SistemaLegado::LiberaFrames();
    } else {
        $nuValor = floatval(str_replace(',','.',str_replace('.','', $_REQUEST['nuValor'] ) ) );
        if ((!$nuValor) or ( $nuValor <= 0 )) {
            SistemaLegado::exibeAviso(urlencode("Campo <i><b>Valor</b></i> deve ser maior que zero! "),"n_alterar","erro");
            SistemaLegado::LiberaFrames();
        } else {
            if (($_REQUEST['stCodBarraOtico'] != '' OR $_REQUEST['stCodBarraManual'] != '') AND ($stAno.$stMes.$stDia != date('Ymd'))) {
                $stJs .= '
                    <script type="text/javascript">
                        alert("                         Atenção!\n\nData do boletim deve ser igual a data atual!");
                    </script>
                ';
                echo $stJs;
                SistemaLegado::LiberaFrames();
            } else {
                $segue = TRUE;
            }
        }
    }
}

if ($segue) {
    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->setExercicio  ( Sessao::getExercicio() );
    $obRTesourariaBoletim->setCodBoletim ( $inCodBoletimAberto );
    $obRTesourariaBoletim->setDataBoletim( $stDtBoletimAberto );
    $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCGM(  Sessao::read('numCgm') );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->setTimestampUsuario( $_REQUEST['stTimestampUsuario'] );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal( $_REQUEST['inCodTerminal'] );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal( $_REQUEST['stTimestampTerminal'] );

    $obRTesourariaConfiguracao = new RTesourariaConfiguracao();
    $obRTesourariaConfiguracao->setExercicio( Sessao::getExercicio() );
    $obRTesourariaConfiguracao->consultarTesouraria();

    $stFiltro  = "&stAcao=".$stAcao."&inCodEntidade=".$_REQUEST['inCodEntidade']."&inCodBoletim=".$_REQUEST['inCodBoletim']."&stDtBoletim=".$_REQUEST['stDtBoletim'];
    $stFiltro .= "&inCodPlano=".$_REQUEST['inCodPlano']."&stNomConta=".$_REQUEST['stNomConta'];

    switch ($stAcao) {

        case 'incluir':
        
        $obErro = new Erro();

        if ( $request->get('inCodReceita') != '' && $request->get('inCodBemAlienacao') != '') {
            include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
   
            $obTPatrimonioBem    = new TPatrimonioBem();
            
            
            $obTPatrimonioBem->setDado('exercicio', Sessao::getExercicio());
            $obErro = $obTPatrimonioBem->recuperaContaContabil($rsContaContabil, " WHERE bem.cod_bem IN (".$request->get('inCodBemAlienacao').") \n ");
    
            if ( is_null($rsContaContabil->getCampo('cod_plano')) ) {
                $obErro->setDescricao("Necessário configurar uma Conta Contábil para o Grupo: ".$rsContaContabil->getCampo('cod_natureza')." - ".$rsContaContabil->getCampo('nom_natureza')." ".$rsContaContabil->getCampo('cod_grupo')." - ".$rsContaContabil->getCampo('nom_grupo'));
            } elseif ( is_null($rsContaContabil->getCampo('cod_plano_alienacao_ganho'))) {
                $obErro->setDescricao("Necessário configurar uma Conta de VPA para Alienação para o Grupo: ".$rsContaContabil->getCampo('cod_natureza')." - ".$rsContaContabil->getCampo('nom_natureza')." ".$rsContaContabil->getCampo('cod_grupo')." - ".$rsContaContabil->getCampo('nom_grupo'));
            } else if (is_null($rsContaContabil->getCampo('cod_plano_alienacao_perda'))) {
                $obErro->setDescricao("Necessário configurar uma Conta de VPD para Alienação para o Grupo: ".$rsContaContabil->getCampo('cod_natureza')." - ".$rsContaContabil->getCampo('nom_natureza')." ".$rsContaContabil->getCampo('cod_grupo')." - ".$rsContaContabil->getCampo('nom_grupo'));
            }
        }
        
    if ( !$obErro->ocorreu() ) {
        
        //Faz a arrecadacao via receita
        if ($_REQUEST['inCodReceita'] != '') {
            $obRTesourariaBoletim->addArrecadacao();
            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->setCodReceita          ( $_REQUEST['inCodReceita']         );
            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao( $_REQUEST['stNomReceita']   );
            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceitaDedutora->setCodReceita  ( $_REQUEST['inCodReceitaDeducao']  );
            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceitaDedutora->obROrcamentoClassificacaoReceita->setDescricao( $_REQUEST['stNomReceitaDeducao'] );
            $obRTesourariaBoletim->roUltimaArrecadacao->setTimestampArrecadacao                     ( $stTimestampArrecadacao           );
            $obRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setCodPlano     ( $_REQUEST['inCodPlano']           );
            $obRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setNomConta     ( $_REQUEST['stNomConta']           );
            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setCodigoEntidade     ( $_REQUEST['inCodEntidade']        );
            $obRTesourariaBoletim->roUltimaArrecadacao->setObservacao                               ( $_REQUEST['stObservacoes']        );
            $obRTesourariaBoletim->roUltimaArrecadacao->setVlArrecadacao                            ( $_REQUEST['nuValor']              );
            $obRTesourariaBoletim->roUltimaArrecadacao->setVlDeducao                                ( $_REQUEST['nuValorDeducao']       );
            $stMensagem .= $_REQUEST['inCodReceita'];

            $obErro = $obRTesourariaBoletim->arrecadar( $boTransacao );
        } elseif ($_REQUEST['stCodBarraManual'] != '' OR $_REQUEST['stCodBarraOtico'] != '') {
            if ($_REQUEST['stCodBarraOtico'] != '') {
                $stNumeracao = substr($_REQUEST['stCodBarraOtico'],-17);
            } else {
                $arCodBarra = explode(' ',$_REQUEST['stCodBarraManual']);
                $stNumeracao = substr($arCodBarra[0].$arCodBarra[2].$arCodBarra[4].$arCodBarra[6],-17);
            }

            $stMensagem = $stNumeracao;

            /**
             * Executa as rotinas necessarias para a baixa do carne na GT
             */
            //Pega as informacoes da sessao que foram setadas no OC e da baixa na GF
            $arCarne = Sessao::read('arCarne');

            $obRARRPagamento = new RARRPagamento();

            $nuValorPagamento = str_replace(',','.',str_replace('.','',$_REQUEST['nuValor']));

            $obRARRPagamento->setDataPagamento                  ( $stDtBoletimAberto );
            $obRARRPagamento->setObservacao                     ( $_REQUEST['stObservacoes'] );
            $obRARRPagamento->setValorPagamento                 ( $nuValorPagamento );
            $obRARRPagamento->obRARRCarne->setNumeracao         ( $stNumeracao );
            $obRARRPagamento->obRARRCarne->setExercicio         ( Sessao::getExercicio() );

            $obRARRPagamento->obRARRCarne->obRMONConvenio->setCodigoConvenio( $arCarne[0]['cod_convenio'] );

            $obRARRPagamento->obRARRTipoPagamento->setCodigoTipo( 20 ); //Baixa pela tesouraria, referente a tabela arrecadacao.tipo_pagamento
            $obRARRPagamento->obRARRTipoPagamento->setPagamento ( 't' );

            //Busca o cod_agencia e o cod_banco da conta selecionada
            $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco();
            $obTContabilidadePlanoBanco->setDado('cod_plano',$_REQUEST['inCodPlano']);
            $obTContabilidadePlanoBanco->setDado('exercicio',Sessao::getExercicio());
            $obTContabilidadePlanoBanco->recuperaPorChave($rsPlanoBanco);

            $obRARRPagamento->obRMONAgencia->setCodAgencia      ( $rsPlanoBanco->getCampo( 'cod_agencia') );
            $obRARRPagamento->obRMONBanco->setCodBanco          ( $rsPlanoBanco->getCampo( 'cod_banco'  ) );

            $boFecha = TRUE;
            $obRARRPagamento->setDataLote( $stDia.'-'.$stMes.'-'.$stAno );
            $obRARRPagamento->setExercicio( Sessao::getExercicio() );

            $obErro = $obRARRPagamento->efetuarPagamentoManual('', FALSE, $boFecha, $inTotal);

            /**
             * Calcula o valor total do carne para poder calcular a porcentagem do valor que deve
             * ir em cada parcela
             */
            foreach ($arCarne as $arCredito) {
                $i++;
                $flTotalCarne += $arCredito['valor'];
            }

            /**
             * Calcula a porcentagem que deve ir em cada credito do valor da parcela
             */
            $inCount = 0;
            foreach ($arCarne as $arCredito) {
                $arPorcCredito[$inCount] = ($arCredito['valor'] * 100) / $flTotalCarne;
                $inCount++;
            }

            if ( !$obErro->ocorreu() ) {
                //Instancia a classe TOrcamentoReceitaCredito para recuperar as informacoes da receita para cada credito
                $obTOrcamentoReceitaCredito = new TOrcamentoReceitaCredito();

                $inCount = 0;
                foreach ($arCarne as $arCredito) {

                    /**
                     * Faz a inclusao dos acrescimos/descontos caso eles forem maiores que 0
                     * 1 - Correção
                     * 2 - Juros
                     * 3 - Multa
                     */
                    $obTOrcamentoReceitaCreditoAcrescimo = new TOrcamentoReceitaCreditoAcrescimo();
                    $obTOrcamentoReceitaCreditoAcrescimo->setDado('cod_genero'  ,$arCredito['cod_genero'  ]);
                    $obTOrcamentoReceitaCreditoAcrescimo->setDado('cod_especie' ,$arCredito['cod_especie' ]);
                    $obTOrcamentoReceitaCreditoAcrescimo->setDado('cod_natureza',$arCredito['cod_natureza']);
                    $obTOrcamentoReceitaCreditoAcrescimo->setDado('cod_credito' ,$arCredito['cod_credito' ]);
                    $obTOrcamentoReceitaCreditoAcrescimo->setDado('exercicio'   ,Sessao::getExercicio()   );

                    $arValorAcrescimo = array( 1 => 'valor_credito_correcao',
                                                  2 => 'valor_credito_juros'   ,
                                                  3 => 'valor_credito_multa'
                                           );

                    for ($i = 1; $i <= 3; $i++) {
                        if ($arCredito[$arValorAcrescimo[$i]] > 0) {
                            /**
                             * Recupera a receita vinculada ao credito para cada um dos tipos
                             */
                            $obTOrcamentoReceitaCreditoAcrescimo->setDado('cod_tipo' ,$i);
                            $obTOrcamentoReceitaCreditoAcrescimo->recuperaReceitaCreditoAcrescimo($rsReceitaAcrescimo);

                            $obRTesourariaBoletim->addArrecadacao();
                            $stTimestampArrecadacao = $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.');
                            $obRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setCodPlano     ( $_REQUEST['inCodPlano']           );
                            $obRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setNomConta     ( $_REQUEST['stNomConta']           );
                            $obRTesourariaBoletim->roUltimaArrecadacao->setTimestampArrecadacao                     ( $stTimestampArrecadacao           );
                            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setCodigoEntidade     ( $_REQUEST['inCodEntidade']        );
                            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->setCodReceita          ( $rsReceitaAcrescimo->getCampo('cod_receita') );
                            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao( $rsReceitaAcrescimo->getCampo('descricao') );

                            $stObservacao = ($_REQUEST['stObservacoes'] != '') ? 'Carnê '.$stNumeracao.' - '.$_REQUEST['stObservacoes'] : 'Carnê '.$stNumeracao;
                            $obRTesourariaBoletim->roUltimaArrecadacao->setObservacao                               ( $stObservacao );
                            $flTotalParcelas += $arCredito[$arValorAcrescimo[$i]];
                            $flTotalAcrescimos += $arCredito[$arValorAcrescimo[$i]];
                            $obRTesourariaBoletim->roUltimaArrecadacao->setVlArrecadacao                            ($arCredito[$arValorAcrescimo[$i]] );
                            $obRTesourariaBoletim->roUltimaArrecadacao->obRARRCarne->obRMONConvenio->setCodigoConvenio($arCredito['cod_convenio']);

                            //$obErro = $obRTesourariaBoletim->arrecadar( $boTransacao );
                        }
                    }

                    $obRTesourariaBoletim->addArrecadacao();
                    //$stTimestampArrecadacao = $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.');
                    $obRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setCodPlano     ($_REQUEST['inCodPlano']   );
                    $obRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setNomConta     ($_REQUEST['stNomConta']   );
                    $obRTesourariaBoletim->roUltimaArrecadacao->setTimestampArrecadacao                     ($stTimestampArrecadacao );
                    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setCodigoEntidade     ($_REQUEST['inCodEntidade']);
                    $obRTesourariaBoletim->roUltimaArrecadacao->obRARRCarne->setNumeracao                   ($stNumeracao);
                    $obRTesourariaBoletim->roUltimaArrecadacao->obRARRCarne->obRMONConvenio->setCodigoConvenio ($arCredito['cod_convenio']);

                    //Recupera a receita para o credito principal
                    $obTOrcamentoReceitaCredito->setDado('cod_genero'  ,$arCredito['cod_genero'  ]);
                    $obTOrcamentoReceitaCredito->setDado('cod_especie' ,$arCredito['cod_especie' ]);
                    $obTOrcamentoReceitaCredito->setDado('cod_natureza',$arCredito['cod_natureza']);
                    $obTOrcamentoReceitaCredito->setDado('cod_credito' ,$arCredito['cod_credito' ]);
                    $obTOrcamentoReceitaCredito->setDado('exercicio'   ,Sessao::getExercicio());
                    $obTOrcamentoReceitaCredito->setDado('divida_ativa',(($arCredito['divida_ativa'] == 'f') ? 'false' : 'true'));
                    $obTOrcamentoReceitaCredito->recuperaRelacionamento($rsCredito);

                    /**
                     * Arrecada o valor do credito principal
                     */
                    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->setCodReceita          ( $rsCredito->getCampo('cod_receita') );
                    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao( $rsCredito->getCampo('descricao') );

                    $stObservacao = ($_REQUEST['stObservacoes'] != '') ? 'Carnê '.$stNumeracao.' - '.$_REQUEST['stObservacoes'] : 'Carnê '.$stNumeracao;
                    $obRTesourariaBoletim->roUltimaArrecadacao->setObservacao                               ( $stObservacao );
                    $flValorParcela = round(($arPorcCredito[$inCount] * $arCredito['valor_parcela']) / 100,2);
                    $flTotalParcelas += $flValorParcela;
                    $obRTesourariaBoletim->roUltimaArrecadacao->setVlArrecadacao                            ( $flValorParcela           );

                    /**
                     * Se houverem descontos para o credito deduz o desconto
                     */
                    if ($arCredito['desconto'] > 0) {
                        $obTOrcamentoReceitaCreditoDesconto = new TOrcamentoReceitaCreditoDesconto();
                        $obTOrcamentoReceitaCreditoDesconto->setDado('cod_genero'  ,$arCredito['cod_genero'  ]);
                        $obTOrcamentoReceitaCreditoDesconto->setDado('cod_especie' ,$arCredito['cod_especie' ]);
                        $obTOrcamentoReceitaCreditoDesconto->setDado('cod_natureza',$arCredito['cod_natureza']);
                        $obTOrcamentoReceitaCreditoDesconto->setDado('cod_credito' ,$arCredito['cod_credito' ]);
                        $obTOrcamentoReceitaCreditoDesconto->setDado('cod_receita' ,$rsCredito->getCampo('cod_receita'));
                        $obTOrcamentoReceitaCreditoDesconto->setDado('exercicio'   ,Sessao::getExercicio()   );
                        $obTOrcamentoReceitaCreditoDesconto->setDado('divida_ativa',(($arCredito['divida_ativa'] == 'f') ? 'false' : 'true'));
                        $obTOrcamentoReceitaCreditoDesconto->recuperaReceitaCreditoDesconto($rsReceitaDesconto);

                        $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceitaDedutora->setCodReceita ($rsReceitaDesconto->getCampo('cod_receita_dedutora'));
                        $obRTesourariaBoletim->roUltimaArrecadacao->setVlDeducao                               ($arCredito['desconto']);
                    }

                    $inCount++;
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            if ((($arCredito['valor_parcela'] + $flTotalAcrescimos) - $flTotalParcelas) > 0) {
                $obRTesourariaBoletim->roUltimaArrecadacao->setVlArrecadacao($flValorParcela + (($arCredito['valor_parcela'] + $flTotalAcrescimos) - $flTotalParcelas));
            }
        }

        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST['inCodReceita'] == '') {
                $obErro = $obRTesourariaBoletim->arrecadar( $boTransacao );
            }
        }

        if ($obRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao()) {
            Sessao::write('stDescricao', array('stDescricao'=>$obRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao()));
        }

        if ( $request->get('inCodReceita') != '' && $request->get('inCodBemAlienacao') != '') {
            if ( !$obErro->ocorreu() ) {
                
                include_once CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculoBaixado.class.php";
                include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoBaixaPatrimonioAlienacao.class.php";
                include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php";
                
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao = new TContabilidadeLancamentoBaixaPatrimonioAlienacao();
                
                // Recupera o cod_recurso apartir do cod_receita para vincular as contas de controle (débito e crédito)
                $obTOrcamentoReceita = new TOrcamentoReceita();
                $obTOrcamentoReceita->setDado('exercicio', Sessao::getExercicio());
                $obTOrcamentoReceita->recuperaRelacionamento($rsRecursoReceita, " AND O.cod_receita = ".$request->get('inCodReceita') );

                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stCodBem'               , $request->get('inCodBemAlienacao') );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'inCodPlano'             , $request->get('inCodPlano')        );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'inCodEntidade'          , $request->get('inCodEntidade')     );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stExercicio'            , Sessao::getExercicio()             );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stDataBaixa'            , sistemaLegado::dataToSql($stDtBoletimAberto) );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'nuValorAlienacao'       , str_replace(',','.',str_replace('.','',$request->get('nuValor'))) );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'inCodArrecadacao'       , $obRTesourariaBoletim->roUltimaArrecadacao->getCodArrecadacao()   );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'inCodRecurso'           , $rsRecursoReceita->getCampo('cod_recurso')   );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stExercicioArrecadacao' , Sessao::getExercicio()  );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stTimestampArrecadacao' , $stTimestampArrecadacao );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'inCodHistorico'         , 968     );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stTipo'                 , 'H'     );
                $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'boEstorno'              , 'FALSE' );
                
                $obErro = $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->insereLancamentosBaixaPatrimonioAlienacao($rsLancamentoBaixa, $boTransacao);

                // Verifica se o bem a ser alienados, existe algum que seja veiculo, proprio ou de terceiros, para fazer a sua baixa
                if (!$obErro->ocorreu()){
                    $obTFrotaVeiculoBaixado  = new TFrotaVeiculoBaixado();
                    $obTFrotaVeiculoBaixado->setDado('stCodBem', $request->get('inCodBemAlienacao'));
                    $obTFrotaVeiculoBaixado->recuperaBaixaVeiculoProprio( $rsVeiculoProprio );

                    // Realiza a baixa de somente de veiculos proprios atraves do ultimo timestamp
                    if( $rsVeiculoProprio->getNumLinhas() > 0 ){
                        if ($rsVeiculoProprio->getCampo('veiculo_proprio') == 't') {
                            $obTFrotaVeiculoBaixado->setdado('cod_veiculo', $rsVeiculoProprio->getCampo('cod_veiculo') );
                            $obTFrotaVeiculoBaixado->setdado('dt_baixa'   , date('Y-m-d') );
                            $obTFrotaVeiculoBaixado->setdado('motivo'     , 'Lançamento de baixa do patrimônio por Alienação' );
                            
                            if ( $request->get('inTipoBaixa') == '2') {
                                $obTFrotaVeiculoBaixado->setdado('cod_tipo_baixa', '4' );
                            } elseif ($request->get('inTipoBaixa') == '4') {
                                $obTFrotaVeiculoBaixado->setdado('cod_tipo_baixa', '7' );
                            } else {
                                $obTFrotaVeiculoBaixado->setdado('cod_tipo_baixa', '99' );
                            }
                            $obErro = $obTFrotaVeiculoBaixado->inclusao($boTransacao);
                        }
                    }
                }
            } 
        }
    }

        if ( !$obErro->ocorreu() ) {
            if ( $obRTesourariaConfiguracao->getFormaComprovacao() ) {
                Sessao::write('filtro',$_REQUEST);
                SistemaLegado::alertaAviso($pgAutenticacao."?pg_volta=../arrecadacao/".$pgForm.$stFiltro,$stMensagem,"alterar","aviso", Sessao::getId(), "../");
            } else {
                $stFiltro  = "&stAcao=".$stAcao."&inCodigoEntidade=".$_REQUEST['inCodEntidade']."&inCodBoletim=".$_REQUEST['inCodBoletim']."&stDtBoletim=".$_REQUEST['stDtBoletim'];
                $stFiltro .= "&inCodPlano=".$_REQUEST['inCodPlano']."&stNomConta=".$_REQUEST['stNomConta'];
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId().$stFiltro,$stMensagem,"alterar","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            SistemaLegado::LiberaFrames();
        }

        break;

        case 'alterar':

            $nuValorEstornar = str_replace(".","",$_REQUEST['nuValorEstornar']);
            $nuValorEstornar = str_replace(",",".",$nuValorEstornar);

            $nuValorDeducaoEstornar = str_replace(".","",$_REQUEST['nuValorDeducaoEstornar']);
            $nuValorDeducaoEstornar = str_replace(",",".",$nuValorDeducaoEstornar);

            $obRTesourariaBoletim->addArrecadacao();
            $obRTesourariaBoletim->roUltimaArrecadacao->setCodArrecadacao                           ( $_REQUEST['inCodArrecadacao']       );
            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->setCodReceita          ( $_REQUEST['inCodReceita']           );
            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceitaDedutora->setCodReceita  ( $_REQUEST['inCodReceitaDedutora']   );
            $obRTesourariaBoletim->roUltimaArrecadacao->setTimestampArrecadacao                     ( $_REQUEST['stTimestampArrecadacao'] );
            $obRTesourariaBoletim->roUltimaArrecadacao->setVlEstornado                              ( $nuValorEstornar                    );
            $obRTesourariaBoletim->roUltimaArrecadacao->setVlDeducaoEstornado                       ( $nuValorDeducaoEstornar             );

            $obErro = $obRTesourariaBoletim->roUltimaArrecadacao->estornar();

            if ($obRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao()) {
                Sessao::write('stDescricao', array('stDescricao'=>$obRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao()));
            }

            if ( !$obErro->ocorreu() ) {
                if ( $request->get('inCodBemAlienacao') != '') {
                    include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
                    include_once CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculoBaixado.class.php";
                    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoBaixaPatrimonioAlienacao.class.php";
                    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php";
   
                    $obTContabilidadeLancamentoBaixaPatrimonioAlienacao = new TContabilidadeLancamentoBaixaPatrimonioAlienacao();
                    $obErro = $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->recuperaTodos($rsBemAlienacao, " WHERE cod_arrecadacao = ".$request->get('inCodArrecadacao'));
   
                    if ( !$obErro->ocorreu() ) {
                        // Recupera o cod_recurso apartir do cod_receita para vincular as contas de controle (débito e crédito)
                        $obTOrcamentoReceita = new TOrcamentoReceita();
                        $obTOrcamentoReceita->setDado('exercicio', Sessao::getExercicio());
                        $obTOrcamentoReceita->recuperaRelacionamento($rsRecursoReceita, " AND O.cod_receita = ".$request->get('inCodReceita') );
                       
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stCodBem'               , $request->get('inCodBemAlienacao') );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'inCodPlano'             , $request->get('inCodPlano')        );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'inCodEntidade'          , $request->get('inCodEntidade')     );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stExercicio'            , Sessao::getExercicio()             );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stDataBaixa'            , sistemaLegado::dataToSql( $stDtBoletimAberto ));
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'nuValorAlienacao'       , $nuValorEstornar                   );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'inCodArrecadacao'       , $request->get('inCodArrecadacao')  );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'inCodRecurso'           , $rsRecursoReceita->getCampo('cod_recurso')   );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stExercicioArrecadacao' , $rsBemAlienacao->getCampo('exercicio_arrecadacao') );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stTimestampArrecadacao' , $rsBemAlienacao->getCampo('timestamp_arrecadacao') );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'inCodHistorico'         , 969    );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'stTipo'                 , 'H'    );
                        $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->setDado( 'boEstorno'              , 'TRUE' );
                       
                        $obErro = $obTContabilidadeLancamentoBaixaPatrimonioAlienacao->insereLancamentosBaixaPatrimonioAlienacao($rsLancamentoBaixa, $boTransacao);

                         // verifica se o bem é um veiculo e se sofreu baixa.
                        if ( !$obErro->ocorreu() ){
                            $obTFrotaVeiculoBaixado  = new TFrotaVeiculoBaixado();
                            $obTFrotaVeiculoBaixado->setDado('stCodBem', $request->get('inCodBemAlienacao'));
                            $obErro = $obTFrotaVeiculoBaixado->recuperaUltimaBaixa( $rsUltimaBaixa );
   
                            if ( $rsUltimaBaixa->getNumLinhas() > 0  ) {
                                while(!$rsUltimaBaixa->eof()){
                                    $obTFrotaVeiculoBaixado->setDado('cod_veiculo', $rsUltimaBaixa->getCampo('cod_veiculo') );
                                    $obErro = $obTFrotaVeiculoBaixado->exclusao($boTransacao);
                                    $rsUltimaBaixa->proximo();

                                    if ( $obErro->ocorreu() )
                                        break;
                                }
                            }
                        }
                    }
                }
            }

            if ( !$obErro->ocorreu() ) {
                $stReceitasArrecadadas =  "Arrecadação ".$_REQUEST['inCodArrecadacao'];

                if ( $obRTesourariaConfiguracao->getFormaComprovacao() ) {
                    SistemaLegado::alertaAviso($pgAutenticacao."?&stAcao=".$stAcao."&pg_volta=../arrecadacao/".$pgList,$stReceitasArrecadadas,"alterar","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::alertaAviso($pgList."?&stAcao=".$stAcao,
                                               $stReceitasArrecadadas,
                                               "alterar",
                                               "aviso",
                                               Sessao::getId(), "../");
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
                SistemaLegado::LiberaFrames();
            }

        break;

        case 'devolucao':

        list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletimAberto );
        $stTimestampArrecadacao = $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms');

        $obRTesourariaBoletim->addArrecadacao();
        $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->setCodReceita          ( $_REQUEST['inCodReceita']         );
        $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao( $_REQUEST['stNomReceita']   );
        $obRTesourariaBoletim->roUltimaArrecadacao->setTimestampArrecadacao                     ( $stTimestampArrecadacao           );
        $obRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setCodPlano     ( $_REQUEST['inCodPlano']           );
        $obRTesourariaBoletim->roUltimaArrecadacao->obRContabilidadePlanoBanco->setNomConta     ( $_REQUEST['stNomConta']           );
        $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setCodigoEntidade     ( $_REQUEST['inCodEntidade']        );
        $obRTesourariaBoletim->roUltimaArrecadacao->setObservacao                               ( $_REQUEST['stObservacoes']        );
        $obRTesourariaBoletim->roUltimaArrecadacao->setVlArrecadacao                            ( $_REQUEST['nuValor']              );
        $obRTesourariaBoletim->roUltimaArrecadacao->setDevolucao                                ( true );

        $stReceitasArrecadadas .= $_REQUEST['inCodReceita'];

        $obErro = $obRTesourariaBoletim->arrecadar( $boTransacao );

        if ($obRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao()) {
            Sessao::write('stDescricao', array('stDescricao'=>$obRTesourariaBoletim->roUltimaArrecadacao->obRTesourariaAutenticacao->getDescricao()));
        }
        if ( !$obErro->ocorreu() ) {
            if ( $obRTesourariaConfiguracao->getFormaComprovacao() ) {
                Sessao::write('filtro',$_REQUEST);
                $stFiltros .= "&inCodPlano=".$_REQUEST['inCodPlano']."&stNomConta=".$_REQUEST['stNomConta']."&inCodBoletim=".$_REQUEST['inCodBoletim'];
                SistemaLegado::alertaAviso($pgAutenticacao."?stAcao=".$stAcao."&pg_volta=../arrecadacao/".$pgForm.$stFiltros,$stReceitasArrecadadas,"alterar","aviso", Sessao::getId(), "../");
            } else {
                $stFiltro  = "&stAcao=".$stAcao."&inCodigoEntidade=".$_REQUEST['inCodEntidade']."&inCodBoletim=".$_REQUEST['inCodBoletim']."&stDtBoletim=".$_REQUEST['stDtBoletim'];
                $stFiltro .= "&inCodPlano=".$_REQUEST['inCodPlano']."&stNomConta=".$_REQUEST['stNomConta'];
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId().$stFiltro,$stReceitasArrecadadas,"alterar","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            SistemaLegado::LiberaFrames();
        }

        break;
    }
}

?>
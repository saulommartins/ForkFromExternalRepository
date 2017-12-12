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
  * Página de processamento para calculo
  * Data de criação : 02/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: PRManterCalculo.php 66501 2016-09-05 13:57:47Z evandro $

    Caso de uso: uc-05.03.05
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAuditoria.class.php" );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma    = "ExecutarCalculo";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
$pgFormRelatorioExecucao = "FMRelatorioExecucao.php";
$pgFormRelatorioExecucaoLancamento = "FMRelatorioExecucaoLancamento.php";
$obErro = new Erro;
$obConexao   = new Conexao;

switch ($stAcao) {
    case 'validar':
        for ($inCount=1; $inCount < $request->get('hidden')+1; $inCount++) {
             $boIncluir = $request->get('boIncluir_'.$inCount);
             if ($boIncluir) {
                 $arGrupo      = explode('/', $boIncluir);
                 $stGrupo      = $arGrupo[0];
                 $stExercicio  = $arGrupo[1];
                //rodar PL

                 $stSql = " SELECT fn_desativa_calculo('".$stGrupo."','".$stExercicio."');";
                 $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

                 $stSql = " SELECT arrecadacao.validaCalculosSimulados('".$stGrupo."','".$stExercicio."');";
                 $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
            }
        }

    if (!$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso("LSValidarCalculo.php"."?stAcao=definir","Cálculos validados com sucesso ! ","definir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");
    }
    break;

    case "simular":
    case "incluir":
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        Sessao::remove('calculos');
        /* validar formulario */
        if (!$request->get('inCodGrupo') and !$request->get("inCodCredito")) {
            $obErro->setDescricao('Grupo ou Crédito deve ser setado!');
            break;
        }
        if ($request->get('stTipoCalculo') == 'geral') {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;
            //---------------------------
            $inCodGrupo = $request->get('inCodGrupo');
            list( $inCodGrupo , $inExercicio ) = explode( '/' , $inCodGrupo );
            
            if ( Sessao::read( 'calculados' ) == -1 ) {
                require_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php");

                $obRARRGrupo = new RARRGrupo;

                $obRARRGrupo->setCodGrupo ( $inCodGrupo );
                $obRARRGrupo->setExercicio ( $inExercicio );
                $obRARRGrupo->consultarGrupo();

                Sessao::write( 'calculo_grupo', $obRARRGrupo->getCodModulo() );

                if ( $request->get("boSimular") ) {
                    $stSql = " SELECT arrecadacao.deleteCalculosSimulado('".$inCodGrupo."','".$inExercicio."');";
                } else {
                    $stSql = " SELECT fn_desativa_calculo('".$inCodGrupo."','".$inExercicio."');";
                }

                $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    if ( Sessao::read( 'calculo_grupo' ) == 14 ) {
                        $stSql = "
                            SELECT
                                ece.inscricao_economica AS inscricao
                            FROM
                                economico.cadastro_economico AS ece

                            LEFT JOIN
                                economico.cadastro_economico_empresa_fato
                            ON
                                cadastro_economico_empresa_fato.inscricao_economica = ece.inscricao_economica

                            LEFT JOIN
                                economico.cadastro_economico_empresa_direito
                            ON
                                cadastro_economico_empresa_direito.inscricao_economica = ece.inscricao_economica

                            LEFT JOIN
                                economico.cadastro_economico_autonomo
                            ON
                                cadastro_economico_autonomo.inscricao_economica = ece.inscricao_economica

                            LEFT JOIN (
                                SELECT
                                    BCE.*
                                FROM
                                    economico.baixa_cadastro_economico AS BCE,
                                    (
                                    SELECT
                                        MAX (TIMESTAMP) AS TIMESTAMP,
                                        inscricao_economica
                                    FROM
                                        economico.baixa_cadastro_economico
                                    GROUP BY
                                        inscricao_economica
                                    ) AS BT
                                WHERE
                                    BCE.inscricao_economica = BT.inscricao_economica AND
                                    BCE.timestamp = BT.timestamp
                            ) be
                            ON
                                ece.inscricao_economica = be.inscricao_economica

                            WHERE
                                COALESCE( cadastro_economico_empresa_direito.inscricao_economica, cadastro_economico_empresa_fato.inscricao_economica, cadastro_economico_autonomo.inscricao_economica) IS NOT NULL AND
                                (
                                    (be.dt_inicio IS NULL) OR
                                    (be.dt_inicio IS NOT NULL AND be.dt_termino IS NOT NULL)
                                    AND be.inscricao_economica = ece.inscricao_economica
                                )
                        ";
                    } else {
                        $stSql = "
                            SELECT DISTINCT
                                I.INSCRICAO_MUNICIPAL AS inscricao
                            FROM
                                IMOBILIARIO.IMOVEL AS I
                            LEFT JOIN (
                                SELECT
                                    BAL.*
                                FROM
                                    imobiliario.baixa_imovel AS BAL,
                                    (
                                    SELECT
                                        MAX (TIMESTAMP) AS TIMESTAMP,
                                        inscricao_municipal
                                    FROM
                                        imobiliario.baixa_imovel
                                    GROUP BY
                                        inscricao_municipal
                                    ) AS BT
                                WHERE
                                    BAL.inscricao_municipal = BT.inscricao_municipal AND
                                    BAL.timestamp = BT.timestamp
                            ) bi
                            ON
                                I.inscricao_municipal = bi.inscricao_municipal
                            WHERE
                                (
                                        (bi.dt_inicio IS NULL)
                                    OR
                                        (bi.dt_inicio IS NOT NULL AND bi.dt_termino IS NOT NULL)
                                    AND
                                        bi.inscricao_municipal=I.inscricao_municipal
                                )
                        ";
                    }

                    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $arTMP = $rsRecordSet->getElementos();
                        Sessao::write( 'calculados', 0 );
                        Sessao::write( 'calculados_lista', $arTMP );
                        Sessao::write( 'total_calcular', count($arTMP) );
                    }
                }
            } elseif ( !$obErro->ocorreu() ) {
                $stInscricoes = "";
                $arListaCalc = Sessao::read( 'calculados_lista' );
                for ($inTMP=0; $inTMP<100; $inTMP++) {
                    if ( Sessao::read( 'calculados' ) >= Sessao::read( 'total_calcular' ) ) {
                        break;
                    }

                    $stInscricoes .= $arListaCalc[ Sessao::read( 'calculados' ) ]["inscricao"];
                    if ( ( $inTMP+1 < 100 ) && ( Sessao::read( 'calculados' ) + 1 < Sessao::read( 'total_calcular' ) ))
                        $stInscricoes .= ", ";

                    Sessao::write( 'calculados', Sessao::read( 'calculados' ) + 1 );
                }

                if ( $request->get("boSimular") ) {
                    $boSimular = 'true';
                } else {
                    $boSimular = 'false';
                }

                if ( Sessao::read( 'calculo_grupo' ) == 14 )
                    $stSql = " SELECT fn_calculo_economico_intervalo('".$inCodGrupo."', '".$inExercicio."', '".$stInscricoes."', '".$boSimular."' ) as resultado;";
                else
                    $stSql = " SELECT fn_calculo_imobiliario_intervalo('".$inCodGrupo."', '".$inExercicio."', '".$stInscricoes."', '".$boSimular."' ) as resultado;";
                
                $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao ); //comentado para testar

                if ($boSimular == 'true') {
                    $stSql = "
                        SELECT
                            acgr.cod_calculo,
                            ac.timestamp

                        FROM
                            arrecadacao.calculo_grupo_credito AS acgr

                        JOIN
                            arrecadacao.calculo AS ac
                        ON
                            ac.cod_calculo = acgr.cod_calculo
                            AND ac.ativo = FALSE
                            AND ac.simulado = TRUE

                        LEFT JOIN
                            arrecadacao.lancamento_calculo AS alc
                        ON
                            alc.cod_calculo = acgr.cod_calculo

                        WHERE
                            cod_grupo = ".$inCodGrupo."
                            AND acgr.ano_exercicio = '".$inExercicio."'
                            AND alc.cod_calculo IS NULL
                        ORDER BY
                            acgr.cod_calculo DESC
                    ";
                } else {
                    $stSql = "
                        SELECT
                            acgr.cod_calculo,
                            ac.timestamp

                        FROM
                            arrecadacao.calculo_grupo_credito AS acgr

                        JOIN
                            arrecadacao.calculo AS ac
                        ON
                            ac.cod_calculo = acgr.cod_calculo

                        LEFT JOIN
                            arrecadacao.lancamento_calculo AS alc
                        ON
                            alc.cod_calculo = acgr.cod_calculo

                        WHERE
                            cod_grupo = ".$inCodGrupo."
                            AND acgr.ano_exercicio = '".$inExercicio."'
                            AND alc.cod_calculo IS NULL
                            AND ac.calculado = TRUE
                        ORDER BY
                            acgr.cod_calculo DESC
                    ";
                }

                if ( $rsRecordSet->getCampo( "resultado" ) == "t" ) {
                    unset( $rsRecordSet );
                    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
                    $obTAdministracaoAuditoria = new TAuditoria;

                    $stTimestamp = $rsRecordSet->getCampo("timestamp");
                    $stCalculosInicial = $stCalculosFinal = $rsRecordSet->getCampo("cod_calculo");

                    $obTAdministracaoAuditoria->setDado( "numcgm", Sessao::read( "numCgm" ) );
                    $obTAdministracaoAuditoria->setDado( "cod_acao", Sessao::read( "acao" ) );
                    $obTAdministracaoAuditoria->setDado( "timestamp", $stTimestamp );
                    if ($stCalculosInicial === $stCalculosFinal) {
                        $stCalculos = $stCalculosInicial;
                    } else {
                        $stCalculos = $stCalculosInicial." até ".$stCalculosFinal;
                    }

                    $obTAdministracaoAuditoria->setDado( "objeto", "cod_calculo=".$stCalculos );
                    $obTAdministracaoAuditoria->setDado( "transacao", false );
                    $obErro = $obTAdministracaoAuditoria->inclusao($boTransacao);

                    unset( $rsRecordSet );
                }

            }

            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTAdministracaoAuditoria );
            
            SistemaLegado::LiberaFrames();
            if ( $obErro->ocorreu() ) {
                SistemaLegado::exibeAviso( urlencode($obErro->getDescricao()), "n_erro", "erro", Sessao::getId(), "../" );
            } else {
                $pgListaSituacao = "LSManterCalculoSituacao.php?stAcao=incluir&stTipoCalculo=".$_REQUEST["stTipoCalculo"]."&inCodGrupo=".$_REQUEST["inCodGrupo"]."&boSimular=".$_REQUEST["boSimular"];
                SistemaLegado::alertaAviso( $pgListaSituacao, "Código do Grupo:".$_REQUEST["inCodGrupo"], "incluir", "aviso", Sessao::getId(), "../" );
            }
            exit;
        }

        /* instanciar classe de Calculo*/
        $obRARRLancamento = new RARRLancamento ( new RARRCalculo );
        #$obCalculo = new RARRCalculo;

        /* verificar calculo */
        Sessao::write( 'TipoCalculo', $_REQUEST['stTipoCalculo'] );
        Sessao::write( 'UsaCalendarioFiscal', $_REQUEST['chkUsaCalendario'] );
        switch ($_REQUEST[ 'stTipoCalculo' ]) {  /* 0 individual/parcial | 1 geral | enviar para o calculo*/
            case 'parcial':
                $obRARRLancamento->roRARRCalculo->setTipoCalculo ( 0 ) ;
                // validar
                if ($_REQUEST['inNumInscricaoImobiliariaInicial'] && $_REQUEST['inNumInscricaoImobiliariaFinal']) {

                    $inscricaoMInicial  = $_REQUEST["inNumInscricaoImobiliariaInicial"];
                    $inscricaoMFinal    = $_REQUEST["inNumInscricaoImobiliariaFinal"];

                    $obRARRLancamento->roRARRCalculo->obRCIMImovel->inNumeroInscricaoInicial = $inscricaoMInicial;
                    $obRARRLancamento->roRARRCalculo->obRCIMImovel->inNumeroInscricaoFinal   = $inscricaoMFinal;

                    $obRARRLancamento->obRARRCarne->inInscricaoImobiliariaInicial = $inscricaoMInicial;
                    $obRARRLancamento->obRARRCarne->inInscricaoImobiliariaFinal   = $inscricaoMFinal;

                } elseif ($_REQUEST['inNumInscricaoEconomicaInicial'] && $_REQUEST['inNumInscricaoEconomicaFinal']) {

                    $inscricaoEInicial  = $_REQUEST['inNumInscricaoEconomicaInicial'];
                    $inscricaoEFinal    = $_REQUEST['inNumInscricaoEconomicaFinal'];

                    $obRARRLancamento->roRARRCalculo->obRCEMInscricaoEconomica->setInscricaoEconomicaInicial ( $inscricaoEInicial );
                    $obRARRLancamento->roRARRCalculo->obRCEMInscricaoEconomica->setInscricaoEconomicaFinal   ( $inscricaoEFinal );

                } elseif ($_REQUEST['inCodContribuinteInicial'] && $_REQUEST['inCodContribuinteFinal']) {

                } else {
                    $obErro->setDescricao('Filtro para Calculo Parcial deve ser setado!');
                }
            break;
            case 'individual':
                $arParcelas = Sessao::read( 'parcelas' );
                if ( count( $arParcelas ) <= 0 ) {
                    SistemaLegado::exibeAviso( "Nenhuma parcela foi configurada para execução do cálculo.", "n_erro", "erro", Sessao::getId(), "../" );
                    exit;
                }

                $obRARRLancamento->roRARRCalculo->setTipoCalculo ( 0 );

                // validar
                if ( $request->get('inInscricaoImobiliaria') or $request->get('inNumInscricaoEconomica') or $request->get('inCodContribuinteIndividual') ) {

                    if ( $request->get('inInscricaoImobiliaria') ) {
                        $obRARRLancamento->roRARRCalculo->obRCIMImovel->inNumeroInscricao = $request->get('inInscricaoImobiliaria');
                        $obRARRLancamento->roRARRCalculo->obRCIMImovel->verificaBaixaImovel ( $rsImovelBaixa, $boTransacao );

                        if ( $rsImovelBaixa->getNumLinhas() > 0 ) {
                            $stErro = "Código de inscrição imobiliária inválido. <b>Imóvel Baixado</b>  (". $obRARRLancamento->roRARRCalculo->obRCIMImovel->inNumeroInscricao .")";
                            SistemaLegado::exibeAviso($stErro,"n_erro","erro",Sessao::getId(), "../" );
                            exit;
                        }
                    } elseif ( $request->get('inNumInscricaoEconomica') ) {

                        $obRARRLancamento->roRARRCalculo->obRCEMInscricaoEconomica->setInscricaoEconomica( $request->get('inNumInscricaoEconomica') );
                        $obRARRLancamento->roRARRCalculo->obRCEMInscricaoEconomica->consultarInscricaoEconomicaBaixa( $rsEmpresaBaixa, $boTransacao );

                        if ( $rsEmpresaBaixa->getNumLinhas() > 0 ) {
                            $stErro = "Código de inscrição econômica inválido. <b>Empresa Baixada</b>  [". $obRARRLancamento->roRARRCalculo->obRCEMInscricaoEconomica->getInscricaoEconomica() ."]";
                            SistemaLegado::exibeAviso( $stErro,"n_erro","erro",Sessao::getId(), "../" );
                            exit;
                        }
                    } else {
                        $obRARRLancamento->roRARRCalculo->obRCGM->setNumCGM ( $request->get('inCodContribuinteIndividual') );
                    }
                } else {
                    $obErro->setDescricao('Filtro para Calculo Individual deve ser setado!');
                }
            break;

            case 'geral':
                $obRARRLancamento->roRARRCalculo->setTipoCalculo ( 1 ) ;
            break;
        }
        /* ******* filtros ******** */
        // filtros por contribuinte
        if ( $request->get("inCodContribuinteInicial") && $request->get("inCodContribuinteFinal")) {
            $obRARRLancamento->roRARRCalculo->obRCIMImovel->addProprietario();
            $obRARRLancamento->roRARRCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMInicial = $request->get("inCodContribuinteInicial");
            $obRARRLancamento->roRARRCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMFinal   = $request->get("inCodContribuinteFinal");
        } elseif ( $request->get('inCodContribuinteIndividual') ) {
            $obRARRLancamento->roRARRCalculo->obRCIMImovel->addProprietario();
            $obRARRLancamento->roRARRCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMInicial = $request->get("inCodContribuinteIndividual");
            $obRARRLancamento->roRARRCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMFinal   = $request->get("inCodContribuinteIndividual");
        }
        /* fim - filtros*/

        /* Modulo */
        if ( $request->get( 'inCodModulo' ) ) {
            $obRARRLancamento->roRARRCalculo->obRModulo->setCodModulo( $request->get( "inCodModulo" ) ) ;
        } else {
            // buscar modulo
            if ( $request->get("inCodGrupo") ) {
                require_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php");
                list( $inCodGrupo , $inExercicio ) = explode( '/' , $request->get( 'inCodGrupo' ) );
                $obRARRGrupo = new RARRGrupo;
                $obRARRGrupo->setCodGrupo ( $inCodGrupo );
                $obRARRGrupo->setExercicio ( $inExercicio );
                $obRARRGrupo->consultarGrupo();
                $obRARRLancamento->roRARRCalculo->obRModulo->setCodModulo( $obRARRGrupo->getCodModulo() );
            } else {
                if ( $request->get('inNumInscricaoEconomica') || $request->get("inNumInscricaoEconomicaInicial") ) {
                    $obRARRLancamento->roRARRCalculo->obRModulo->setCodModulo( 14 );
                } else {
                        $obRARRLancamento->roRARRCalculo->obRModulo->setCodModulo(12);
                }
            }
        }

        /* grupo / credito */
        if ( $request->get( 'inCodGrupo' ) ) {
            list( $inCodGrupo , $inExercicio ) = explode( '/' , $request->get( 'inCodGrupo' ) );
            $inCodGrupo = trim ( $inCodGrupo ) * 1;
            $obRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo( $inCodGrupo );
            $obRARRLancamento->roRARRCalculo->obRARRGrupo->setExercicio ( $inExercicio );
            $obRARRLancamento->roRARRCalculo->setExercicio ( $inExercicio );
        } else {
            $inExercicio = $request->get('inExercicioCalculo');
            $obRARRLancamento->roRARRCalculo->setExercicio ( $inExercicio );
            $obRARRLancamento->roRARRCalculo->setChaveCredito ( $request->get( "inCodCredito" ) );
        }

        /* efetuar lançamento  */
        if ( $request->get('efetuar_lancamentos') == 'sim' && $request->get( 'stTipoCalculo' ) == 'individual' ) {
            $obRARRLancamento->roRARRCalculo->boLancamento = 'true';
            $arParcelas = Sessao::read( 'parcelas' );
            $inTotalDados = count( $arParcelas );
            $inTotalParcelas = 0;
            for ($inX=0; $inX < $inTotalDados; $inX++) {
                if ($arParcelas[$inX]["stTipoParcela"] != "Única") {
                    $inTotalParcelas++;
                }
            }

            $obRARRLancamento->roRARRCalculo->setNumParcelas ( $inTotalParcelas );
            $obRARRLancamento->setObservacao                 ( $request->get('stObservacao') );
            $obRARRLancamento->setObservacaoSistema          ( $request->get('stObservacaoInterna') );
            list($inProcesso,$inExercicioPro) = explode ( "/",$request->get('inProcesso') );
            $obRARRLancamento->obRProcesso->setCodigoProcesso ( $inProcesso );
            $obRARRLancamento->obRProcesso->setExercicio      ( $inExercicioPro );

        }

        // executa calculo
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRARRLancamento->roRARRCalculo->calculoTributario ($boTransacao);
        }
        // carne
        if ( $request->get("emissao_carnes") == "local" and !$obErro->ocorreu() ) {
            $obRARRLancamento->obRARRCarne->setExercicio ( $inExercicio );

            // dados para o carne
            $obRARRLancamento->obRARRCarne->setGrupo ( $inCodGrupo );
            $arGruposValidos = explode(',','101,102,121,10120, 10121, 10122, 10123, 10124,10125, 10198, 10199, 10220, 10221, 10222, 10223, 10224, 10225, 10298,10299, 131,13120,13121,13122,13123,13124,13125,13197,131,13198,13199');
            if (in_array( $request->get('inCodGrupo'),$arGruposValidos)) {
                $boDiversas = FALSE;
            }

            $obRARRLancamento->obRARRCarne->inInscricaoImobiliariaInicial = $request->get("inNumInscricaoImobiliariaInicial");
            $obRARRLancamento->obRARRCarne->inInscricaoImobiliariaFinal = $request->get("inNumInscricaoImobiliariaFinal");
            $obRARRLancamento->obRARRCarne->inCodContribuinteInicial = $request->get('inCodContribuinteInicial') ;
            $obRARRLancamento->obRARRCarne->inCodContribuinteFinal = $request->get('inCodContribuinteFinal') ;
            $obRARRLancamento->obRARRCarne->obRARRParcela->roRARRLancamento->inCodLancamento = $obRARRLancamento->roRARRCalculo->obRARRLancamento->inCodLancamento;

            if (!$obRARRLancamento->obRARRCarne->obRARRParcela->roRARRLancamento->inCodLancamento) {
                SistemaLegado::exibeAviso( "Não foi possível efetuar lançamento.","n_erro","erro",$sessao->id, "../");
            }else
                include_once 'PREmitirCarneLancManual.php';
            exit;
        }

        if (!$obErro->ocorreu() && $request->get('efetuar_lancamentos') == 'sim' ) {

            $stPag = $pgFormRelatorioExecucaoLancamento."?stAcao=incluir&stTipoCalculo=".$request->get("stTipoCalculo",'')."&inCodGrupo=".$request->get("inCodGrupo",'')."&inCodCredito=".$request->get("inCodCredito",'');
            if ( $request->get("inCodGrupo") ) {
                SistemaLegado::alertaAviso($stPag,"Codigo do Grupo:".$request->get("inCodGrupo",''),"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($stPag,"Codigo do Crédito:".$request->get("inCodCredito",''),"incluir","aviso", Sessao::getId(), "../");
            }
        } elseif (!$obErro->ocorreu() ) {
            $stPag = $pgFormRelatorioExecucao."?stAcao=incluir&stTipoCalculo=".$request->get("stTipoCalculo",'');
            $stPag .= "&inCodGrupo=".$request->get("inCodGrupo",'');
            $stPag .= "&inInscricaoImobiliariaInicial=".$obRARRLancamento->roRARRCalculo->obRCIMImovel->inNumeroInscricaoInicial;
            $stPag .= "&inInscricaoImobiliariaFinal=".$obRARRLancamento->roRARRCalculo->obRCIMImovel->inNumeroInscricaoFinal;
            $stPag .= "&inCodContribuinteInicial=".$obRARRLancamento->roRARRCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMInicial;
            $stPag .= "&inCodContribuinteFinal=".$obRARRLancamento->roRARRCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMFinal;
            $stPag .= "&inNumInscricaoEconomicaInicial=".$obRARRLancamento->roRARRCalculo->obRCEMInscricaoEconomica->getInscricaoEconomicaInicial();
            $stPag .= "&inNumInscricaoEconomicaFinal=".$obRARRLancamento->roRARRCalculo->obRCEMInscricaoEconomica->getInscricaoEconomicaFinal();

            SistemaLegado::alertaAviso($stPag,"Codigo do Grupo:".$request->get("inCodGrupo",''),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");
        }
        break;

    case "definir":
        $obRegra =  new RARRParametroCalculo;
        /* recupera chave funcao */
        $arCodFuncao        = explode('.',$_REQUEST["inCodFuncao"]);
        /* recupera chave credito */
        $arValores = explode('.',$_REQUEST["inCodCredito"]);// array [0]> cod_credito [1]> cod_especie [2]> cod_genero [3]> cod_natureza

        $obRegra->obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
        $obRegra->obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
        $obRegra->obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
        $obRegra->obRARRGrupo->obRMONCredito->setCodCredito ( $arValores[0] );
        $obRegra->obRARRGrupo->obRMONCredito->setCodEspecie ( $arValores[1] );
        $obRegra->obRARRGrupo->obRMONCredito->setCodGenero  ( $arValores[2] );
        $obRegra->obRARRGrupo->obRMONCredito->setCodNatureza( $arValores[3] );
        $obRegra->setValorCorrespondente( $_REQUEST["stVenal"] );
        $obErro = $obRegra->definirParametro();

    if (!$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso("FMManterParametros.php"."?stAcao=definir","Definir Parâmetros concluido com sucesso! (".$_REQUEST["inCodCredito"].") ","definir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");
    }
    break;
}

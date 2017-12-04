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
/*
* Página de processamento dos aquivos de emprestimos do Banrisul
*
* Data de Criação   : 02/10/2009
*
* @author Analista      Dagine Rodrigues Vieira
* @author Desenvolvedor Cassiano de Vasconcellos Ferreira
*
* @package URBEM
* @subpackage
*
* @ignore
* $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CLA_ARQUIVO_CSV;
include_once CAM_GRH_IMA_MAPEAMENTO.'TIMAConfiguracaoBanrisulEmprestimo.class.php';
include_once CAM_GRH_IMA_MAPEAMENTO.'TIMAConsignacaoEmprestimoBanrisul.class.php';
include_once CAM_GRH_IMA_MAPEAMENTO.'TIMAConsignacaoEmprestimoBanrisulConfiguracao.class.php';
include_once CAM_GRH_IMA_MAPEAMENTO.'TIMAConsignacaoEmprestimoBanrisulErro.class.php';

include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php";
include_once CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "EmprestimoBanrisul";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgForm     = "FMImportar".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$pgFormResumo = "FM".$stPrograma."Resumo.php";

switch ($_REQUEST['stAcao']) {
    case 'validarArquivo':
        /*
         * Recupera a competencia/movimentação vigente no sistema
         */
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
        $arCompetencia = explode('/',substr($rsUltimaMovimentacao->getCampo('dt_final'),3));
        $stCompetencia = $arCompetencia[1].$arCompetencia[0];

        if ( !$rsUltimaMovimentacao->eof() ) {
            $inCodPeriodoMovimentacao = $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao');
            Sessao::write('inCodPeriodoMovimentacao', $inCodPeriodoMovimentacao);
            $pgProx    = $pgForm;
            $stCaminho = $_FILES['stArquivoImportacao']['tmp_name'];
            $arquivoImportacao = new ArquivoCSV( $stCaminho );
            $obErro = $arquivoImportacao->Abrir('r');
            $inContador = 1;
            $boCabecalho = false;
            $boRodape = false;
            $inPosicao = 0;
            $stErro = '';
            $inSomatorioConsignar = 0;
            $arLinha = $arquivoImportacao->LerLinha();
            $arHeader = array();
            $arDetalhe = array();
            $arTrailler = array();

           //VALIDACAO DO ARQUIVO
            while ( !feof( $arquivoImportacao->reArquivo ) ) {
                $stTipo = substr($arLinha[0],0,6);
                if ($inContador == 1) {
                    if ($stTipo=='BCEC00') {
                        $arHeader['inCodConvenio'] = trim(substr($arLinha[0],6,8));
                        $arHeader['stNomeConvenio'] = trim(substr($arLinha[0],14,50));
                        $arHeader['inAnoMesConsignacao'] = trim(substr($arLinha[0],64,6));
                        $arHeader['stFiller'] = trim(substr($arLinha[0],70,200));

                        if ( preg_match("/[^0-9]/",$arHeader['inCodConvenio']) ) {
                            $inPosicao = 6;
                            $stErro = 'Código do Convênio inválido - '.$arHeader['inCodConvenio'];
                            break;
                        } elseif (preg_match("/[^0-9]/",$arHeader['inAnoMesConsignacao'])) {
                            $inPosicao = 64;
                            $stErro = 'Ano e mês da consignação inválido - '.$arHeader['inAnoMesConsignacao'];
                            break;
                        } elseif ($arHeader['inAnoMesConsignacao']!=$stCompetencia) {
                            $inPosicao = 64;
                            $stErro = 'Competência informada ('.$arHeader['inAnoMesConsignacao'].') diferente da competência do sistema ('.$stCompetencia.')';
                            break;
                        } elseif ( substr($arHeader['inAnoMesConsignacao'],4,2) > 12 ) {
                            $inPosicao = 68;
                            $stErro = 'Mês da consignação inválido, maior que 12 - '.substr($arHeader['inAnoMesConsignacao'],4,2);
                            break;
                        }
                        $arHeader['stCompetencia']=$arCompetencia[0].'/'.$arCompetencia[1];
                        $boCabecalho = true;
                    } else {
                        $stErro = 'Sem cabeçalho ou não é um BCEC00 válido!';
                        break;
                    }
                } else {
                    if ($stTipo=='BCEC10') {//DETALHE
                        if (!$boRodape) {
                            $arRegistroDetalhe['inOA']                   = trim(substr($arLinha[0],  6,  6));
                            $arRegistroDetalhe['inMatricula']            = trim(substr($arLinha[0], 12, 15));
                            $arRegistroDetalhe['inCPF']                  = trim(substr($arLinha[0], 27, 11));
                            $arRegistroDetalhe['stNomeFuncionario']      = trim(substr($arLinha[0], 38, 35));
                            $arRegistroDetalhe['inCodigoCanal']          = trim(substr($arLinha[0], 73,  5));
                            $arRegistroDetalhe['stNumeroContrato']       = trim(substr($arLinha[0], 78, 20));
                            $arRegistroDetalhe['stPrestacao']            = trim(substr($arLinha[0], 98,  7));
                            $arRegistroDetalhe['flValorConsignar']       = trim(substr($arLinha[0],105, 15));
                            $arRegistroDetalhe['flValorConsignado']      = trim(substr($arLinha[0],120, 15));
                            $arRegistroDetalhe['stCodigoMotivoRejeicao'] = trim(substr($arLinha[0],135,  2));
                            $arRegistroDetalhe['stFiller']               = trim(substr($arLinha[0],137,200));

                            //verifica se possui um evento
                            //se retornar registro significa que possui evento cadastrado
                            $stFiltro = " AND FPE.codigo = '".$arRegistroDetalhe['inCodigoCanal']."' ";
                            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
                            $obTFolhaPagamentoEvento->recuperaEventos($rsEvento,$stFiltro);

                            //verifica se o empréstimo banrisul está configurado
                            $obTIMAConfiguracaoBanrisulEmprestimo = new TIMAConfiguracaoBanrisulEmprestimo;
                            $obTIMAConfiguracaoBanrisulEmprestimo->recuperaTodos($rsConfiguracao);
                            $inCodEventoConfig = $rsConfiguracao->getCampo('cod_evento');

                            //verifica se possui a matrícula do arquivo no banco
                            $obTPessoalContrato = new TPessoalContrato;
                            $obTPessoalContrato->recuperaPorCPF($rsRecordSet, $arRegistroDetalhe['inCPF']);

                            if ( preg_match("/[^0-9]/",$arRegistroDetalhe['inOA']) ) {
                                $inPosicao = 7;
                                $stErro = 'Código do Órgão/Unidade inválido - '.$arRegistroDetalhe['inOA'];
                                break;
                            }
                            /* elseif (ereg("[^0-9]",$arRegistroDetalhe['inMatricula']) || (int) $arRegistroDetalhe['inMatricula'] == 0 || $rsRecordSet->getNumLinhas() == -1) {//ereg("[^0]",$arRegistroDetalhe['inMatricula']))
                                $inPosicao = 13;
                                $stErro = 'Matrícula do servidor inválida - Servidor '.$arRegistroDetalhe['stNomeFuncionario'].' - Matrícula '.$arRegistroDetalhe['inMatricula'];
                                break;
                            }*/
                            elseif (preg_match("/[^0-9]/",$arRegistroDetalhe['inCPF']) || $rsRecordSet->getNumLinhas() == -1) {
                                $inPosicao = 28;
                                $stErro = 'CPF inválido - '.$arRegistroDetalhe['inCPF'];
                                break;
                            } elseif (preg_match("/[^0-9]/",$arRegistroDetalhe['inCodigoCanal'])){ // || $rsEvento->getNumLinhas() == -1) {
                                $inPosicao = 74;
                                $stErro = 'Código do Canal informado inválido para a configuração de empréstimos. - '.$arRegistroDetalhe['inCodigoCanal'];
                                break;
                            } elseif (preg_match("/([^0-9]{3})\/([^0-9]{3})/",$arRegistroDetalhe['stPrestacao'])) {
                                $inPosicao = 99;
                                $stErro = 'Prestação inválido - NAN '.$arRegistroDetalhe['stPrestacao'];
                                break;

                            }/*elseif ($inCodEventoConfig != (int) $arRegistroDetalhe['inCodigoCanal']) {
                                $inPosicao = 74;
                                $stErro = 'O evento informado não consta em Configuração Empréstimo Banrisul. - Código Evento '.$arRegistroDetalhe['inCodigoCanal'];
                                break;
                            } elseif (ereg("([^0-9]{3})/([^0-9]{3})",$arRegistroDetalhe['flValorConsignar'])) {
                                $inPosicao = 110;
                                $stErro = 'Valor a consignar - NAN '.$arRegistroDetalhe['flValorConsignar'];
                                break;
                            }*/
                            $inSomatorioConsignar += (int) $arRegistroDetalhe['flValorConsignar'];
                            if (preg_match("/([0-9]{3})\/([0-9]{3})/",$arRegistroDetalhe['stPrestacao'])) {
                                $inPosicao = 98;
                                $arPrestacao = explode('/',$arRegistroDetalhe['stPrestacao']);
                                if ( (int) $arPrestacao[0] > (int) $arPrestacao[1] ) {
                                    $stErro = 'Prestação inválido - O número da parcela('.(int) $arPrestacao[0].') não pode ser maior que a quantidade de parcelas('.(int) $arPrestacao[1].')!';
                                    break;
                                }
                            }
                            $arDetalhe[] = $arRegistroDetalhe;
                        } else {
                            $stErro = 'Rodapé duplicado!';
                            break;
                        }
                    } elseif ($stTipo=='BCEC99') {//RODAPE
                        $arTrailler['inQuantidadeRegistros'] = trim(substr($arLinha[0],  6,  15));
                        $arTrailler['inValorTotal'] =          trim(substr($arLinha[0], 21,  15));
                        $arTrailler['stFiller'] =              trim(substr($arLinha[0], 36, 200));
                        if (preg_match("/[^0-9]/",$arTrailler['inQuantidadeRegistros'])) {
                            $inPosicao = 7;
                            $stErro = 'Quantidade de registros inválido - NAN '.$arRegistroDetalhe['flValorConsignar'];
                            break;
                        } elseif ($arTrailler['inQuantidadeRegistros'] != $inContador) {
                            $inPosicao = 7;
                            $stErro = 'Quantidade de registros informado não confere com o número de registros do arquivo!'.$arTrailler['inQuantidadeRegistros'];
                            break;
                        } elseif (preg_match("/[^0-9]/",$arTrailler['inValorTotal'])) {
                            $inPosicao = 22;
                            $stErro = 'Valor total inválido - NAN '.$arRegistroDetalhe['flValorConsignar'];
                            break;
                        } elseif ((int) $arTrailler['inValorTotal']!=(int) $inSomatorioConsignar) {
                            $inPosicao = 22;
                            $stErro = 'Valor total a consignar não confere com o somatório dos registros do arquivo!'.$arTrailler['inValorTotal']."!=".$inSomatorioConsignar;
                            break;
                        }
                        $boRodape = true;
                    } else {
                        $stErro = "Tipo inválido - ".$stTipo;
                        break;
                    }
                }
                $inContador++;
                $arLinha = $arquivoImportacao->LerLinha();
            }//FIM DA VALIDACAO DO ARQUIVO

            if ($stErro != '') {
                $stMensagem = "Erro ao importar arquivo! (Erro (".$stErro.") na linha ".$inContador.")" ;
                SistemaLegado::exibeAviso(urlencode($stMensagem),"unica","erro");
                SistemaLegado::LiberaFrames();
            } else {
                Sessao::write('arHeader'  , $arHeader   );
                Sessao::write('arDetalhe' , $arDetalhe  );
                Sessao::write('arTrailler', $arTrailler );
                $stMensagem='Arquivo lido com sucesso';
                sistemaLegado::alertaAviso($pgProx,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode("Não existe período de movimentação aberto"),"n_incluir","erro");
        }
    break;
    case 'importar':

        $pgProx      = $pgFilt;
        $arHeader    = Sessao::read('arHeader');
        $arDetalhe   = Sessao::read('arDetalhe');
        $arTrailler  = Sessao::read('arTrailler');
        $inCount     = 0;
        $inCountErro = 0;
        $inCodPeriodoMovimentacao = Sessao::read('inCodPeriodoMovimentacao');
        Sessao::setTrataExcecao(true);

        $obTIMAConsignacaoEmprestimoBanrisulErro = new TIMAConsignacaoEmprestimoBanrisulErro;
        $obTIMAConsignacaoEmprestimoBanrisulErro->setDado('cod_periodo_movimentacao', $inCodPeriodoMovimentacao );
        $obTIMAConsignacaoEmprestimoBanrisulErro->exclusaoPorMovimentacao();

        $obTIMAConsignacaoEmprestimoBanrisul = new TIMAConsignacaoEmprestimoBanrisul;
        $obTIMAConsignacaoEmprestimoBanrisul->setDado('cod_periodo_movimentacao', $inCodPeriodoMovimentacao );
        $obTIMAConsignacaoEmprestimoBanrisul->exclusaoPorMovimentacao();

        $obTIMAConsignacaoEmprestimoBanrisulConfiguracao = new TIMAConsignacaoEmprestimoBanrisulConfiguracao;
        $obTIMAConsignacaoEmprestimoBanrisulConfiguracao->setDado('cod_periodo_movimentacao', $inCodPeriodoMovimentacao );
        $obTIMAConsignacaoEmprestimoBanrisulConfiguracao->exclusaoPorMovimentacao();
        $obTIMAConsignacaoEmprestimoBanrisulConfiguracao->setDado('cod_periodo_movimentacao',$inCodPeriodoMovimentacao);
        $obTIMAConsignacaoEmprestimoBanrisulConfiguracao->setDado('cod_convenio'            , $arHeader['inCodConvenio']);
        $obTIMAConsignacaoEmprestimoBanrisulConfiguracao->setDado('nom_convenio'            , $arHeader['stNomeConvenio']);
        $obTIMAConsignacaoEmprestimoBanrisulConfiguracao->setDado('ano_mes'                 , $arHeader['inAnoMesConsignacao']);
        $obTIMAConsignacaoEmprestimoBanrisulConfiguracao->inclusao();

        //VALIDACAO E INCLUSAO DOS REGISTROS NO BANCO DE DADOS
        $obTIMAConfiguracaoBanrisulEmprestimo = new TIMAConfiguracaoBanrisulEmprestimo;
        $obTIMAConfiguracaoBanrisulEmprestimo->recuperaTodos($rsConfiguracao);
        $inCodEventoConfig = $rsConfiguracao->getCampo('cod_evento');

        while (isset($arDetalhe[$inCount])) {
            $obTIMAConsignacaoEmprestimoBanrisul = new TIMAConsignacaoEmprestimoBanrisul;
            $obTIMAConsignacaoEmprestimoBanrisulErro = new TIMAConsignacaoEmprestimoBanrisulErro;
            $arRegDetalhe = $arDetalhe[$inCount];
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('num_linha'               , $inCount);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('cod_periodo_movimentacao',$inCodPeriodoMovimentacao);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('oa'                      , $arRegDetalhe['inOA']);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('matricula'               , $arRegDetalhe['inMatricula']);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('cpf'                     , $arRegDetalhe['inCPF']);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('nom_funcionario'         , $arRegDetalhe['stNomeFuncionario']);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('cod_canal'               , $arRegDetalhe['inCodigoCanal']);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('nro_contrato'            , $arRegDetalhe['stNumeroContrato']);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('prestacao'               , $arRegDetalhe['stPrestacao']);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('val_consignar'           , $arRegDetalhe['flValorConsignar']);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('val_consignado'          , $arRegDetalhe['flValorConsignado']);
            $obTIMAConsignacaoEmprestimoBanrisul->setDado('filler'                  , $arRegDetalhe['stFiller']);
            /*
             * Erro
             */
            $obTIMAConsignacaoEmprestimoBanrisulErro->setDado('num_linha'               , $inCount);
            $obTIMAConsignacaoEmprestimoBanrisulErro->setDado('cod_periodo_movimentacao',$inCodPeriodoMovimentacao);

            //if ($inCodEventoConfig != (int) $arRegDetalhe['inCodigoCanal'] ) {

            if (!(int) $arRegDetalhe['inCodigoCanal'] ) {
                $inPosicao= 78;
                $stErro = "Código do evento informado no arquivo(".(int) $arRegDetalhe['inCodigoCanal'].") difere da configuração (".$inCodEventoConfig.")";
                $obTIMAConsignacaoEmprestimoBanrisulErro->setDado('cod_motivo_rejeicao' , 'H3');
            } else {
                $obTPessoalContrato = new TPessoalContrato;
                $obTPessoalContrato->recuperaPorCPF($rsRecordSet,$arRegDetalhe['inCPF']);
                if ( !$rsRecordSet->eof() ) {
                    $inCodContrato = $rsRecordSet->getCampo('cod_contrato');
                    $obTIMAConsignacaoEmprestimoBanrisul->setDado('cod_contrato',$inCodContrato);
                    $obErro = $obTIMAConsignacaoEmprestimoBanrisul->recuperaSituacaoContrato($rsSituacaoContrato);
                    if ($rsSituacaoContrato->getCampo('situacao') != 'R') {
                        $obTCGMPessoaFisica = new TCGMPessoaFisica;
                        $stFiltro = ' WHERE LPAD(CPF,11,\'0\') = \''.$arRegDetalhe['inCPF'].'\' AND NUMCGM='.$rsRecordSet->getCampo('numcgm');
                        $obTCGMPessoaFisica->recuperaTodos($rsCGM,$stFiltro,'',$boTransacao);
                        if ( !$rsCGM->eof() ) {
                            /*
                             * INICIO INCLUSAO EVENTO
                             */
                            $obTFolhaPagamentoContratoServidorPeriodo = new TFolhaPagamentoContratoServidorPeriodo;
                            $obTFolhaPagamentoRegistroEventoPeriodo   = new TFolhaPagamentoRegistroEventoPeriodo;
                            $obTFolhaPagamentoRegistroEventoPeriodo->obTFolhaPagamentoContratoServidorPeriodo = &$obTFolhaPagamentoContratoServidorPeriodo;
                            $obTFolhaPagamentoRegistroEvento        = new TFolhaPagamentoRegistroEvento;
                            $obTFolhaPagamentoRegistroEvento->obTFolhaPagamentoRegistroEventoPeriodo = &$obTFolhaPagamentoRegistroEventoPeriodo;
                            $obTFolhaPagamentoUltimoRegistroEvento  = new TFolhaPagamentoUltimoRegistroEvento;
                            $obTFolhaPagamentoUltimoRegistroEvento->obTFolhaPagamentoRegistroEvento = &$obTFolhaPagamentoRegistroEvento;
                            $obTFolhaPagamentoRegistroEventoParcela = new TFolhaPagamentoRegistroEventoParcela;
                            $obTFolhaPagamentoRegistroEventoParcela->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
                            $obTFolhaPagamentoLogErroCalculo = new TFolhaPagamentoLogErroCalculo;
                            $obTFolhaPagamentoLogErroCalculo->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
                            $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
                            $obTFolhaPagamentoEventoCalculado->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
                            $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente;
                            $obTFolhaPagamentoEventoCalculadoDependente->obTFolhaPagamentoEventoCalculado = &$obTFolhaPagamentoEventoCalculado;
                            /*
                             * recupera os eventos existentes do contrato
                             */
                            $stFiltro  = ' AND contrato.cod_contrato = '.$inCodContrato;
                            $stFiltro .= ' AND cod_periodo_movimentacao ='.$inCodPeriodoMovimentacao;
                            $obTFolhaPagamentoRegistroEvento->recuperaRegistrosDeEventos($rsRegistroEventros,$stFiltro);

                            $stFiltro  = "   AND cod_contrato = ".$inCodContrato ;
                            $stFiltro .= "   AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                            $obTFolhaPagamentoUltimoRegistroEvento->recuperaRelacionamento($rsRegistroEventoPeriodo,$stFiltro);
                            while (!$rsRegistroEventoPeriodo->eof()) {
                                $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro",$rsRegistroEventoPeriodo->getCampo("cod_registro"));
                                $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_evento",$rsRegistroEventoPeriodo->getCampo("cod_evento"));
                                $obTFolhaPagamentoUltimoRegistroEvento->setDado("desdobramento",$rsRegistroEventoPeriodo->getCampo("desdobramento"));
                                $obTFolhaPagamentoUltimoRegistroEvento->setDado("timestamp",$rsRegistroEventoPeriodo->getCampo("timestamp"));
                                $obTFolhaPagamentoUltimoRegistroEvento->deletarUltimoRegistroEvento($boTransacao);
                                $rsRegistroEventoPeriodo->proximo();
                            }
                            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
                            $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_contrato",$inCodContrato );
                            $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
                            $obTFolhaPagamentoContratoServidorPeriodo->recuperaPorChave($rsContratoServidorPeriodo);
                            if ( $rsContratoServidorPeriodo->getNumLinhas() < 0 ) {
                                $obTFolhaPagamentoContratoServidorPeriodo->inclusao();
                            }
                            //INCLUSÃO DO REGISTRO
                            $nuValor        = (int) substr($arRegDetalhe['flValorConsignar'],0,-2).'.'.(int) substr($arRegDetalhe['flValorConsignar'],-2);
                            $arParcela = explode('/',$arRegDetalhe['stPrestacao']);
                            $nuQuantidade   = (int) $arParcela[0];
                            $inParcela      = (int) $arParcela[1];
                            $obTFolhaPagamentoRegistroEvento->setDado("cod_evento"  ,$inCodEventoConfig);
                            $obTFolhaPagamentoRegistroEvento->setDado("valor"       ,$nuValor);
                            $obTFolhaPagamentoRegistroEvento->setDado("quantidade"  ,$nuQuantidade);
                            $obTFolhaPagamentoRegistroEvento->setDado("proporcional",false);
                            $obTFolhaPagamentoRegistroEventoPeriodo->inclusao();
                            $obTFolhaPagamentoRegistroEvento->inclusao();
                            $obTFolhaPagamentoUltimoRegistroEvento->inclusao();
                            if ($inParcela > 0) {
                                $obTFolhaPagamentoRegistroEventoParcela->setDado("cod_registro",$obTFolhaPagamentoUltimoRegistroEvento->getDado('cod_registro'));
                                $obTFolhaPagamentoRegistroEventoParcela->setDado("timestamp",$obTFolhaPagamentoUltimoRegistroEvento->getDado('timestamp'));
                                $obTFolhaPagamentoRegistroEventoParcela->setDado("cod_evento",$obTFolhaPagamentoUltimoRegistroEvento->getDado('cod_evento'));
                                $obTFolhaPagamentoRegistroEventoParcela->setDado("parcela",$inParcela);
                                $obTFolhaPagamentoRegistroEventoParcela->setDado("mes_carencia",0);
                                $obTFolhaPagamentoRegistroEventoParcela->inclusao();
                            }
                            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
                            /*
                             * Inclui os eventos já cadastrados para o contrato
                             */
                            while (!$rsRegistroEventros->eof()) {
                                if ( $rsRegistroEventros->getCampo('cod_evento') != $inCodEventoConfig) {
                                    $obTFolhaPagamentoRegistroEvento->setDado("cod_evento"  ,$rsRegistroEventros->getCampo('cod_evento'));
                                    $obTFolhaPagamentoRegistroEvento->setDado("valor"       ,$rsRegistroEventros->getCampo('valor'));
                                    $obTFolhaPagamentoRegistroEvento->setDado("quantidade"  ,$rsRegistroEventros->getCampo('quantidade'));
                                    $obTFolhaPagamentoRegistroEvento->setDado("proporcional",$rsRegistroEventros->getCampo('proporcional'));
                                    $obTFolhaPagamentoRegistroEventoPeriodo->inclusao();
                                    $obTFolhaPagamentoRegistroEvento->inclusao();
                                    $obTFolhaPagamentoUltimoRegistroEvento->inclusao();
                                    if ((int) $rsRegistroEventros->getCampo('parcela') > 0 ) {
                                        $obTFolhaPagamentoRegistroEventoParcela->setDado("parcela",(int) $rsRegistroEventros->getCampo('parcela'));
                                        $obTFolhaPagamentoRegistroEventoParcela->inclusao();
                                    }
                                    $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
                                }
                                $rsRegistroEventros->proximo();
                            }
                            //FIM INCLUSAO EVENTO
                        } else {
                            //O CPF INFORMADO PARA O CONTRATO NÃO BATE COM O CPF DO URBEM
                            //ESTE REGISTRO NÃO SERA GRAVADO NO URBEM MAS A IMPORTAÇÃO SEGUIRA
                            //H3
                            $inPosicao = 28;
                            $obTIMAConsignacaoEmprestimoBanrisulErro->setDado('cod_motivo_rejeicao' , 'H3');
                        }
                    } else {
                        //O CONTRATO NÃO ESTÁ ATIVO
                        //BI/H8
                        $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa;
                        $obTPessoalContratoServidorCasoCausa->setDado('cod_contrato', $inCodContrato);
                        $obTPessoalContratoServidorCasoCausa->recuperaCasoCausaContrato($rsCasoCausa);
                        if (!$rsCasoCausa->eof()) {
                            if( $rsCasoCausa->getCampo('num_causa') == 60 ||
                                $rsCasoCausa->getCampo('num_causa') == 62 ||
                                $rsCasoCausa->getCampo('num_causa') == 64 )
                            {
                                $stErro = 'Falecimento do servidor';
                                $obTIMAConsignacaoEmprestimoBanrisulErro->setDado('cod_motivo_rejeicao' , 'BI');
                            } else {
                                $stErro = 'Servidor deliado da entidade';
                                $obTIMAConsignacaoEmprestimoBanrisulErro->setDado('cod_motivo_rejeicao' , 'H8');
                            }
                        }
                    }
                } else {
                    //A MATRICULA/REGISTRO DO CONTRATO NÃO FOI ENCONTRADA NO URBEM
                    //ESTE REGISTRO NÃO SERA GRAVADO NO URBEM MAS A IMPORTAÇÃO SEGUIRA
                    $inPosicao = 13;
                    //erro HM
                    $stErro = "A matricula ".(int) $arRegDetalhe['inMatricula']." do arquivo não está cadastrada no Urbem.";
                    $obTIMAConsignacaoEmprestimoBanrisulErro->setDado('cod_motivo_rejeicao' , 'HM');
                }
            }
            $obTIMAConsignacaoEmprestimoBanrisul->inclusao();
            if ($stErro != '') {
                $inCountErro++;
                $obTIMAConsignacaoEmprestimoBanrisulErro->setDado('descricao_motivo' , $stErro);
                $obTIMAConsignacaoEmprestimoBanrisulErro->inclusao();
                $stErro = '';
            }
            $inCount++;
        }
        Sessao::encerraExcecao();
        $stMensagem = "Importação do arquivo concluída com sucesso!Registros com erro($inCountErro)";
        sistemaLegado::alertaAviso($pgProx,$stMensagem,"importar","aviso", Sessao::getId(), "../");
    break;
    case 'exportar':
        include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEventoCalculado.class.php';
        include_once CAM_GRH_FOL_MAPEAMENTO.'FFolhaPagamentoRecuperarEventosCalculados.class.php';
        include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalAssentamentoGeradoContratoServidor.class.php';
        include_once CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoCalcularFolhas.class.php';

        $pgProx = $pgFormResumo;

        Sessao::setTrataExcecao(true);
        $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor;
        $obFFolhaPagamentoRecuperarEventosCalculados = new FFolhaPagamentoRecuperarEventosCalculados;
        $obTIMAConfiguracaoBanrisulEmprestimo = new TIMAConfiguracaoBanrisulEmprestimo;
        $obTIMAConfiguracaoBanrisulEmprestimo->recuperaTodos($rsConfiguracao);
        $inCodEventoConfig = $rsConfiguracao->getCampo('cod_evento');

        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $_REQUEST['inCodMes'] );
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", $_REQUEST['inAno'] );

        $stUrl = 'inCodMes='.$_REQUEST['inCodMes'].'&inAno='.$_REQUEST['inAno'];
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);
        $inCodPeriodoMovimentacao = $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $stUrl .= '&inCodPeriodoMovimentacao='.$inCodPeriodoMovimentacao;
        $stDataInicialMovimentacao = $rsPeriodoMovimentacao->getCampo("dt_inicial");
        $stDataFinalMovimentacao = $rsPeriodoMovimentacao->getCampo("dt_final");
        $arDataInicialMovimentacao = explode('/',$stDataInicialMovimentacao);
        $arDataFinalMovimentacao = explode('/',$stDataFinalMovimentacao);
        $stDataInicalMovimentacaoFiltro = $arDataInicialMovimentacao[2].'-'.$arDataInicialMovimentacao[1].'-'.$arDataInicialMovimentacao[0];
        $stDataFinalMovimentacaoFiltro  = $arDataFinalMovimentacao[2].'-'.$arDataFinalMovimentacao[1].'-'.$arDataFinalMovimentacao[0];

        $obTIMAConsignacaoEmprestimoBanrisul = new TIMAConsignacaoEmprestimoBanrisul;
        $obTIMAConsignacaoEmprestimoBanrisul->setDado('cod_periodo_movimentacao',$inCodPeriodoMovimentacao);

        $arContratos = Sessao::read("arContratos");

        //Teste dos Filtros
        if ($_REQUEST[stSituacao] == "pensionistas") { //E
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
            $inCodPeriodoMovimentacao = $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao');

            $stFiltro.=" AND recuperarSituacaoDoContrato(consignacao_emprestimo_banrisul.cod_contrato,".$inCodPeriodoMovimentacao.",'') ='E' \n";
        } elseif ($_REQUEST[stSituacao] == "aposentados") { //P
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
            $inCodPeriodoMovimentacao = $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao');

            $stFiltro.=" AND recuperarSituacaoDoContrato(consignacao_emprestimo_banrisul.cod_contrato,".$inCodPeriodoMovimentacao.",'') ='P' \n";
        } elseif ($_REQUEST[stSituacao] == "ativos") { //A
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
            $inCodPeriodoMovimentacao = $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao');

            $stFiltro.=" AND recuperarSituacaoDoContrato(consignacao_emprestimo_banrisul.cod_contrato,".$inCodPeriodoMovimentacao.",'') ='A' \n";
        }

        //Teste quando é filtrado pela matrícula (contratos)
        if (!empty($arContratos)) {
            foreach ($arContratos as $chave => $arValor) {
                $stListaContratos.= $arValor['cod_contrato'].",";
            }
            $stListaContratos = substr($stListaContratos, 0, -1);

            $stFiltro = " AND consignacao_emprestimo_banrisul.cod_contrato in (".$stListaContratos.")";
        }
        ////////////////-----------------------------

        $obTIMAConsignacaoEmprestimoBanrisul->recuperaQuantidadePorMovimentacao($rsCount, $stFiltro);

        $inQuantidade = $rsCount->getCampo('quantidade');

        $stFilto  = " WHERE";
        $stFilto .= " consignacao_emprestimo_banrisul.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;

        $stFiltroSituacao = '';
        switch ($_POST['stSituacao']) {
            case 'ativos':
                    $stFiltroSituacao = 'A';
                break;
            case 'aposentados':
                    $stFiltroSituacao = 'P';
                break;
            case 'pensionistas':
                    $stFiltroSituacao = 'E';
                break;
            case 'todos':
                break;
        }
        $stUrl .= '&stFiltro='.$stFiltroSituacao;
        if ($stFiltroSituacao != '') {
            $stFilto .= " AND recuperarSituacaoDoContrato(consignacao_emprestimo_banrisul.cod_contrato,$inCodPeriodoMovimentacao,'') ='$stFiltroSituacao' \n";
        }

        $arContratos = Sessao::read("arContratos");
        if( $_POST['stSituacao'] == 'ativos'        ||
            $_POST['stSituacao'] == 'aposentados'   ||
            $_POST['stSituacao'] == 'rescindidos'   ||
            $_POST['stSituacao'] == 'pensionistas'  ||
            $_POST['stSituacao'] == 'todos')
        {

            $stValoresFiltro = "";
            switch ($_POST['stTipoFiltro']) {
                case "contrato":
                case "contrato_rescisao":
                case "contrato_aposentado":
                case "contrato_todos":
                case "cgm_contrato":
                case "cgm_contrato_rescisao":
                case "cgm_contrato_aposentado":
                case "cgm_contrato_todos":
                    $arContratos = Sessao::read("arContratos");

                    foreach ($arContratos as $arContrato) {
                        $stValoresFiltro .= $arContrato["cod_contrato"].",";
                    }
                    $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
                    $stFilto .= ' AND consignacao_emprestimo_banrisul.cod_contrato in  ('.$stValoresFiltro.')';
                    break;
                case "lotacao":
                    $stValoresFiltro = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
                    $stFilto .= ' AND contrato_servidor_orgao.cod_orgao in ('.$stValoresFiltro.')';
                    break;
            }
        }

        Sessao::write('stFiltro', $stFilto);
        /*
         * O filtro foi complementado com a condição IS MULL porque nesta consulta os registros que já possuem erro não
         * são necessários.
         * A idéia aqui é justamente verificar se os demais registro tem algum erro.
         */
        //$stFilto .= "   AND cod_motivo_rejeicao IS NULL                                                             \n";
        $obTIMAConsignacaoEmprestimoBanrisul->recuperaRelacionamento($rsEmprestimoBanrisul, $stFilto);

        RFolhaPagamentoCalcularFolhas::percentageBar(0,$nuPorcentagem.'% Matrícula(s) Processada(s) até o momento!');

        $inCount = 0;

        while (!$rsEmprestimoBanrisul->eof()) {
            $obTIMAConsignacaoEmprestimoBanrisulCalculado = new TIMAConsignacaoEmprestimoBanrisul;
            $obTIMAConsignacaoEmprestimoBanrisulCalculadoErro = new TIMAConsignacaoEmprestimoBanrisulErro;
            /*
             * CHAVE DO REGISTRO
             */
            $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('cod_periodo_movimentacao',$inCodPeriodoMovimentacao );
            $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('num_linha', $rsEmprestimoBanrisul->getCampo('num_linha'));
            $obTIMAConsignacaoEmprestimoBanrisulCalculadoErro->setDado('cod_periodo_movimentacao',$inCodPeriodoMovimentacao );
            $obTIMAConsignacaoEmprestimoBanrisulCalculadoErro->setDado('num_linha', $rsEmprestimoBanrisul->getCampo('num_linha'));
            /*
             * UTLIZADO NAS CONSULTAS
             */
            $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('cod_contrato',$rsEmprestimoBanrisul->getCampo('cod_contrato'));
            /*
             * VERIFICA SE O VALOR FOI CALCULADO
             */
            $obFFolhaPagamentoRecuperarEventosCalculados->setDado('cod_contrato',$rsEmprestimoBanrisul->getCampo('cod_contrato') );
            $obFFolhaPagamentoRecuperarEventosCalculados->setDado('cod_periodo_movimentacao',$inCodPeriodoMovimentacao );
            $obFFolhaPagamentoRecuperarEventosCalculados->setDado('cod_configuracao', 1);
            $stFiltroEvento = ' WHERE cod_evento = '.$inCodEventoConfig;
            $obFFolhaPagamentoRecuperarEventosCalculados->recuperarEventosCalculados($rsEventoCalculado,$stFiltroEvento);
            if ( !$rsEventoCalculado->eof()) {
                $inValor = str_replace('.', '', $rsEventoCalculado->getCampo('valor'));
                $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('val_consignado', $inValor);
                $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('origem_pagamento', 'S');
            } else {
                /*
                * CASO NÃO EXISTA UM VALOR CALCULADO PARA A FOLHA DO CONTRATO É FEITA A VERIFICAÇÃO
                * SE EXISTE UM CALCULO NA FOLHA RESCISAO
                */
                $obFFolhaPagamentoRecuperarEventosCalculados->setDado('cod_configuracao', 4);

                $obFFolhaPagamentoRecuperarEventosCalculados->recuperarEventosCalculados($rsEventoCalculadoRescisao,$stFiltroEvento);
                if (!$rsEventoCalculadoRescisao->eof()) {
                    $inValor = str_replace('.', '', $rsEventoCalculadoRescisao->getCampo('valor'));
                    $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('val_consignado', $inValor);
                    $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('origem_pagamento', 'R');
                } else {
                    /*
                    * SE NÃO TIVER VALOR CALCULADO VERIFICAR SE O CONTRATO NÃO TEM ALGUM ASSENTAMENTO
                    * 3 - licença
                    * 5 - doença
                    * 6 - acidente
                    * 7 - maternidade
                    */
                    $stFiltro  = " AND assentamento_assentamento.cod_motivo IN (3,5,6,7) \n";
                    $stFiltro .= " AND (assentamento_gerado.periodo_inicial BETWEEN '$stDataInicalMovimentacaoFiltro' AND '$stDataFinalMovimentacaoFiltro' \n";
                    $stFiltro .= " OR  assentamento_gerado.periodo_final BETWEEN '$stDataInicalMovimentacaoFiltro' AND '$stDataFinalMovimentacaoFiltro') \n";
                    $stFiltro .= " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$rsEmprestimoBanrisul->getCampo('cod_contrato')." \n";
                    $obTPessoalAssentamentoGeradoContratoServidor->recuperaRelacionamento($rsAssentamento,$stFiltro);
                    /*
                    * SE NÃO TIVER ASSENTAMENTO PODE TER OCORRIDO A RESCISAO DO CONTRATO NA MOVIMENTAÇÃO
                    * NESTE CASO DEVE VERIRFICAR SE NÃO EXISTE ALGO NO CALCULO_RECISAO
                    */
                    if ($rsAssentamento->eof()) {
                        //O CONTRATO NÃO ESTÁ ATIVO
                        $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa;
                        $obTPessoalContratoServidorCasoCausa->setDado('cod_contrato', $rsEmprestimoBanrisul->getCampo('cod_contrato'));
                        $obTPessoalContratoServidorCasoCausa->recuperaCasoCausaContrato($rsCasoCausa);
                        if (!$rsCasoCausa->eof()) {
                            if( $rsCasoCausa->getCampo('num_causa') == 60 ||
                            $rsCasoCausa->getCampo('num_causa') == 62 ||
                            $rsCasoCausa->getCampo('num_causa') == 64 )
                            {
                                $obTIMAConsignacaoEmprestimoBanrisulCalculadoErro->setDado('cod_motivo_rejeicao' , 'BI');
                                $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('val_consignado', '');
                                $stErro = 'Falecimento do servidor';
                            } else {
                                $obTIMAConsignacaoEmprestimoBanrisulCalculadoErro->setDado('cod_motivo_rejeicao' , 'H8');
                                $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('val_consignado', '');
                                $stErro = 'Servidor desligado da entidade';
                            }
                        } else {
                            $obTIMAConsignacaoEmprestimoBanrisulCalculadoErro->setDado('cod_motivo_rejeicao' , 'H3');
                            $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('val_consignado', '');
                            $stErro = 'Não descontado outros motivos';
                        }
                    } else {
                        //SERVIDOR AFASTADO POR LICENÇA
                        $obTIMAConsignacaoEmprestimoBanrisulCalculado->setDado('val_consignado', '');
                        $obTIMAConsignacaoEmprestimoBanrisulCalculadoErro->setDado('cod_motivo_rejeicao','H9');
                        $stErro = 'Servidor afastado por licença';
                    }
                }
            }
            if ($stErro=='') {
                $obTIMAConsignacaoEmprestimoBanrisulCalculado->alteracao();
                $obTIMAConsignacaoEmprestimoBanrisulCalculadoErro->exclusao();
            } else {
                if ($rsEmprestimoBanrisul->getCampo('cod_motivo_rejeicao') == '') {
                    $obTIMAConsignacaoEmprestimoBanrisulCalculadoErro->setDado('descricao_motivo',$stErro);
                    $obTIMAConsignacaoEmprestimoBanrisulCalculadoErro->inclusao();
                }
            }
            $stErro='';
            $rsEmprestimoBanrisul->proximo();
            $inCount++;
            $nuPorcentagem = number_format(($inCount *100/$rsEmprestimoBanrisul->getNumLinhas()), 2, ',', ' ');
            RFolhaPagamentoCalcularFolhas::percentageBar($nuPorcentagem,$nuPorcentagem.'% Matrícula(s) Processada(s) até o momento!');
        }
        Sessao::encerraExcecao();

        if ($inQuantidade > 0) {
            $stMensagem = "Matrículas processadas com sucesso!";
        } else {
            $stMensagem = "Não possui registros!";
        }
        $stUrl .= '&inQuantidade='.$inQuantidade;
        sistemaLegado::alertaAviso($pgProx.'?'.$stUrl,$stMensagem,"exportar","aviso", Sessao::getId(), "../");
     break;
}

?>

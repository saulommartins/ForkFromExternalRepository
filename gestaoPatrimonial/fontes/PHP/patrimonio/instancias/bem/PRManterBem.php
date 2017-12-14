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

 * Data de Criação: 12/09/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * $Id: PRManterBem.php 66304 2016-08-05 15:01:36Z michel $

 * Casos de uso: uc-03.01.06
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioApoliceBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioReavaliacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemAtributoEspecie.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemComprado.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemResponsavel.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemMarca.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemPlanoAnalitica.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemPlanoDepreciacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacaoReavaliacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecieAtributo.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioInventarioHistoricoBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioHistoricoBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioManutencao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioManutencaoPaga.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupoPlanoDepreciacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacaoAnulada.class.php";
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALBemCompradoTipoDocumentoFiscal.class.php';
include_once TTGO.'TTGOPatrimonioBemObra.class.php';
include_once(CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemProcesso.class.php");
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReavaliacao.class.php";


$stPrograma = "ManterBem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTPatrimonioBem                      = new TPatrimonioBem();
$obTPatrimonioReavaliacao              = new TPatrimonioReavaliacao();
$obTPatrimonioDepreciacao              = new TPatrimonioDepreciacao();
$obTPatrimonioBemPlanoAnalitica        = new TPatrimonioBemPlanoAnalitica;
$obTPatrimonioBemResponsavel           = new TPatrimonioBemResponsavel();
$obTPatrimonioHistoricoBem             = new TPatrimonioHistoricoBem();
$obTPatrimonioApoliceBem               = new TPatrimonioApoliceBem();
$obTPatrimonioBemAtributoEspecie       = new TPatrimonioBemAtributoEspecie();
$obTPatrimonioBemComprado              = new TPatrimonioBemComprado();
$obTPatrimonioManutencao               = new TPatrimonioManutencao();
$obTPatrimonioManutencaoPaga           = new TPatrimonioManutencaoPaga();
$obTAdministracaoConfiguracao          = new TAdministracaoConfiguracao;
$obTPatrimonioBemMarca                 = new TPatrimonioBemMarca();
$obTPatrimonioInventarioHistoricoBem   = new TPatrimonioInventarioHistoricoBem();
$obTPatrimonioBemPlanoDepreciacao      = new TPatrimonioBemPlanoDepreciacao();
$obTPatrimonioBemProcesso              = new TPatrimonioBemProcesso();

Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTPatrimonioBem );

$obErro = new Erro();

switch ($stAcao) {
    case 'lote' :
    case 'incluir':
        $identificacao = ( $_REQUEST['stPlacaIdentificacao'] == 'sim' ) ? 't' : 'f';
        $inQtdeLote = $_REQUEST['inQtdeLote'];
        $numeroPlaca = $_REQUEST['stNumeroPlaca'];
        $arrayPlacas = array();
        $stMensagem = '';

        if ($identificacao != 'f') {
            for ($i = 0; $i < $inQtdeLote; $i++) {
                //verifica se o numero da placa já existe
                $stFiltro = " WHERE num_placa = '".$numeroPlaca."' ";
                $obErro = $obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro );

                if(!$obErro->ocorreu()){
                    if ( $rsBem->getNumLinhas() > 0 ) {
                        $obErro->setDescricao( 'Já existem bens com placas no intervalo selecionado, escolha um novo intervalo' );
                    } else {
                        $arrayPlacas[] = $numeroPlaca;
                        $numeroPlaca++;
                    }
                }

                if($obErro->ocorreu())
                    break;
            }
        }

        if (!$obErro->ocorreu()) {
            // Verifica a integridade dos valores
            if( $_REQUEST['stNumNotaFiscal'] != '' && empty($_REQUEST['dataNotaFiscal']) ){
                $obErro->setDescricao( 'O campo Data da Nota Fiscal deve ser preenchido' );
            }elseif($_REQUEST['stNumNotaFiscal'] == '' && $_REQUEST['dataNotaFiscal'] != '' ){
                $obErro->setDescricao( 'O campo Data da Nota Fiscal não deve ser preenchido, quando não houver  um Número da Nota Fiscal' );
            }

            if ($_REQUEST['inValorBem'] == '0,00') {
                $obErro->setDescricao( 'Valor do bem inválido' );
            } elseif (!empty($_REQUEST['inValorDepreciacao']) && (str_replace(",",".",str_replace(".", "", $_REQUEST['inValorDepreciacao'])) > str_replace(",",".",str_replace(".", "", $_REQUEST['inValorBem'])))) {
                $obErro->setDescricao( 'O valor da Depreciação Inicial não pode ser maior que o valor do bem.' );
            } elseif ( $_REQUEST['dtDepreciacao'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtDepreciacao'])) ) {
                $obErro->setDescricao( 'A data de depreciação deve ser maior ou igual a data de aquisição' );
            } elseif ( $_REQUEST['dtVencimento'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtVencimento'])) ) {
                $obErro->setDescricao( 'A data de vencimento da garantia deve ser maior ou igual a data de aquisição' );
            } elseif ( $_REQUEST['dtIncorporacao'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtIncorporacao'])) ) {
                $obErro->setDescricao( 'A data de incorporação deve ser maior ou igual a data de aquisição' );
            } elseif ($_REQUEST['stPlacaIdentificacao'] == 'sim' AND $_REQUEST['stNumeroPlaca'] == '') {
                $obErro->setDescricao( 'Número da placa inválido' );
            } elseif ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) < implode('',array_reverse(explode('/',$_REQUEST['dtAquisicao']))) ) {
                $obErro->setDescricao( 'A data de início do responsável deve ser maior ou igual a data de aquisição' );
            } elseif ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) > date('Ymd') ) {
                $obErro->setDescricao( 'A data de início do responsável deve ser menor ou igual a data de hoje' );
            } elseif ($_REQUEST['stApolice'] == 'sim' AND $_REQUEST['inCodSeguradora'] == '') {
                $obErro->setDescricao( 'Selecione uma seguradora' );
            } elseif ($_REQUEST['stApolice'] == 'sim' AND $_REQUEST['inCodApolice'] == '') {
                $obErro->setDescricao( 'Selecione uma apólice' );
            } elseif ($_REQUEST['inQtdeLote'] <= 0) {
                $obErro->setDescricao( 'A Quantidade deve ser maior que zero' );
            } elseif ($_REQUEST['boDepreciacaoAcelerada'] == 'true' && (empty($_REQUEST['flQuotaDepreciacaoAcelerada']) || $_REQUEST['flQuotaDepreciacaoAcelerada'] == '0,00')) {
                $obErro->setDescricao( 'O valor da quota acelerada deve ser informado e maior que zero' );
            }elseif($boDepreciavel === 'true' && !empty($_REQUEST['inCodContaDepreciacao']) && ($_REQUEST['flQuotaDepreciacaoAnual'] == '0,00' || $_REQUEST['flQuotaDepreciacaoAnual'] == '0.00' || $_REQUEST['flQuotaDepreciacaoAnual'] == '')){
                $obErro->setDescricao( 'O valor da quota de depreciação Anual deve ser maior que zero' );
            }elseif(empty($_REQUEST['inCodContaDepreciacao']) && isset($_REQUEST['flQuotaDepreciacaoAnual']) && $_REQUEST['flQuotaDepreciacaoAnual'] != '0,00' && $_REQUEST['flQuotaDepreciacaoAnual'] != ''){
                $obErro->setDescricao( 'O valor da Conta Contábil de Depreciação Acumulada deve ser informado.' );
            }elseif(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02 ){
                if($_REQUEST['stNumNotaFiscal'] != '' and  $_REQUEST['inCodTipoDocFiscal'] == ''){
                    $obErro->setDescricao( 'Informe o Tipo do Documento Fiscal' );    
                }elseif($_REQUEST['stNumNotaFiscal'] == '' && $_REQUEST['inCodTipoDocFiscal'] != '' ){
                    $obErro->setDescricao( 'O campo Tipo do Documento Fiscal não deve ser preenchido, quando não houver  um Número da Nota Fiscal' );
                }
            }else {
                $rsAtributosDinamicos = Sessao::read('rsAtributosDinamicos');
                if (is_array( $rsAtributosDinamicos->arElementos ) ) {
                    $rsAtributosDinamicos->arElementos = array_reverse($rsAtributosDinamicos->arElementos);
                    while ( !$rsAtributosDinamicos->eof() ) {
                        if ( $rsAtributosDinamicos->getCampo('nao_nulo') == 'f' AND $_REQUEST['Atributo_'.$rsAtributosDinamicos->getCampo('cod_atributo').'_'.$rsAtributosDinamicos->getCampo('cod_cadastro')] == '' ) {
                            $obErro->setDescricao( 'Preencha o campo '.$rsAtributosDinamicos->getCampo('nom_atributo') );
                            break;
                        }
                        $rsAtributosDinamicos->proximo();
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            //loop acrescentado para a inclusão em lote
            //se não for em lote, inclui apenas uma vez
            for ($i = 0; $i < $inQtdeLote; $i++) {
                //inclui na table patrimonio.bem
                $obErro = $obTPatrimonioBem->proximoCod( $inCodBem );

                if (!$obErro->ocorreu()) {
                    //coloca no array os códigos do bem para demonstrar na mensagem
                    $arCodBem[] = $inCodBem;
                    $obTPatrimonioBem->setDado( 'cod_bem', $inCodBem );
                    $obTPatrimonioBem->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
                    $obTPatrimonioBem->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
                    $obTPatrimonioBem->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
                    $obTPatrimonioBem->setDado( 'numcgm', $_REQUEST['inCodFornecedor'] );
                    $obTPatrimonioBem->setDado( 'descricao', $_REQUEST['stNomBem'] );
                    $obTPatrimonioBem->setDado( 'detalhamento', $_REQUEST['stDetalhamentoBem'] );
                    $obTPatrimonioBem->setDado( 'dt_aquisicao', $_REQUEST['dtAquisicao'] );
                    $obTPatrimonioBem->setDado( 'vida_util', $_REQUEST['inVidaUtil'] );
                    $obTPatrimonioBem->setDado( 'dt_incorporacao', $_REQUEST['dtIncorporacao'] );
                    $obTPatrimonioBem->setDado( 'dt_depreciacao', $_REQUEST['dtDepreciacao'] );
                    $obTPatrimonioBem->setDado( 'dt_garantia', $_REQUEST['dtVencimento'] );
                    $obTPatrimonioBem->setDado( 'vl_bem', str_replace(',','.',str_replace('.','',$_REQUEST['inValorBem'])) );
                    $obTPatrimonioBem->setDado( 'vl_depreciacao', (float) str_replace(',','.',str_replace('.','',$_REQUEST['inValorDepreciacao'])) );
                    $obTPatrimonioBem->setDado( 'identificacao', $identificacao );
                    if ($identificacao != 'f') {
                        $obTPatrimonioBem->setDado( 'num_placa', $arrayPlacas[$i] );
                    } else {
                        $arEstrutura = $obTPatrimonioBem->getEstrutura();
                        foreach ($arEstrutura as $key => $value) {
                            if ($value->stNomeCampo == 'num_placa') {
                                $keyField = $key;
                                break;
                            }
                        }
                        # Seta provisoriamente o tipo do campo como INTEGER para gravar o valor null ao invés de ''.
                        $arEstrutura[$keyField]->setTipoCampo('integer');
                        $obTPatrimonioBem->setEstrutura($arEstrutura);
                        $obTPatrimonioBem->setDado( 'num_placa', 'null' );
                    }

                    //Configuração de Depreciação
                    $stDepreciacaoAcelerada = $_REQUEST['boDepreciacaoAcelerada'];
                    $boDepreciavel          = $_REQUEST['boDepreciavel'];
                    $inCodBem               = $inCodBem;
                    $inCodPlano             = $_REQUEST['inCodContaAnalitica'];
                    $inCodContaDepreciacao  = $_REQUEST['inCodContaDepreciacao'];
                    $inExercicio            = Sessao::getExercicio();

                    if (!empty($_REQUEST['stChaveProcesso'])) {
                        $arProcesso = array();
                        $arProcesso = explode("/", $_REQUEST['stChaveProcesso']);
                        $inCodProcesso = $arProcesso[0];
                        $stAnoProcesso = $arProcesso[1];    
                    } else {
                        $arProcesso = "";
                    }

                    if ($boDepreciavel === 'true') {
                        $obTPatrimonioBem->setDado( 'depreciavel', true);
                        $obTPatrimonioBem->setDado( 'quota_depreciacao_anual', $_REQUEST['flQuotaDepreciacaoAnual'] );

                        if ($stDepreciacaoAcelerada === "true") {
                            $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'true' );
                            $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', $_REQUEST['flQuotaDepreciacaoAcelerada'] );
                        } else {
                            $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'false' );
                            $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', '0,00' );
                        }
                    } else {
                        $obTPatrimonioBem->setDado( 'depreciavel', 'false');
                        $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'false' );
                        $obTPatrimonioBem->setDado( 'quota_depreciacao_anual', '0,00' );
                        $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', '0,00' );
                    }

                    $obErro = $obTPatrimonioBem->inclusao();

                    if (!$obErro->ocorreu()) {
                        if(!empty($_REQUEST['stChaveProcesso']) && !$obErro->ocorreu()){
                            $obTPatrimonioBemProcesso->setDado('cod_bem', $inCodBem);
                            $obTPatrimonioBemProcesso->setDado('ano_exercicio', $stAnoProcesso);
                            $obTPatrimonioBemProcesso->setDado('cod_processo', $inCodProcesso);
                            $obErro = $obTPatrimonioBemProcesso->inclusao();
                        }

                        if(!empty($inCodContaDepreciacao) && !$obErro->ocorreu()){
                            $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_bem'  , $inCodBem );
                            $obTPatrimonioBemPlanoDepreciacao->setDado( 'exercicio', $inExercicio);
                            $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_plano', $inCodContaDepreciacao);
                            $obErro = $obTPatrimonioBemPlanoDepreciacao->inclusao();
                        }

                        if (!$obErro->ocorreu()) {
                            if (!empty($inCodPlano)) {
                                $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica;
                                $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $inCodBem );
                                $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio', $inExercicio);
                                $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_plano', $inCodPlano);

                                $rsBemPlanoAnalitica = new RecordSet();
                                $obErro = $obTPatrimonioBemPlanoAnalitica->recuperaMaxTimestampBemPlanoAnalitica($rsBemPlanoAnalitica);

                                if (!$obErro->ocorreu()) {
                                    if ($rsBemPlanoAnalitica->getCampo('timestamp') == '') {
                                        $obErro = $obTPatrimonioBemPlanoAnalitica->inclusao();
                                    }
                                }
                            } else {
                                $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica;
                                $obTPatrimonioBemPlanoAnalitica->setDado('cod_plano', $inCodPlano);
                                $obTPatrimonioBemPlanoAnalitica->setDado('cod_bem'  , $inCodBem );
                                $obTPatrimonioBemPlanoAnalitica->setDado('exercicio', $inExercicio);

                                $rsBemPlanoAnalitica = new RecordSet();
                                $obErro = $obTPatrimonioBemPlanoAnalitica->recuperaMaxTimestampBemPlanoAnalitica($rsBemPlanoAnalitica);

                                if (!$obErro->ocorreu()) {
                                    if ($rsBemPlanoAnalitica->getNumLinhas() > 0) {
                                        $obErro = $obTPatrimonioBemPlanoAnalitica->exclusao();
                                    }
                                }
                            }
                        }

                        if (!$obErro->ocorreu()) {
                            //Insere reavaliações
                            $rsReavaliacao = new RecordSet;
                            $rsReavaliacao->preenche(Sessao::read('arReavaliacao'));

                            while (!$rsReavaliacao->eof() ) {
                                $inCodBem = $inCodBem;
                                $inCodReavaliacao = $rsReavaliacao->getCampo('inCodReavaliacao');
                                $dtReavaliacao = $rsReavaliacao->getCampo('dtReavaliacao');
                                $inVidaUtilReavaliacao = $rsReavaliacao->getCampo('inVidaUtilReavaliacao');
                                $flValorBemReavaliacao = $rsReavaliacao->getCampo('flValorBemReavaliacao');
                                $stMotivoReavaliacao = $rsReavaliacao->getCampo('stMotivoReavaliacao');
                                $inserir = $rsReavaliacao->getCampo('inserir');

                                if ($inCodBem != '' && $inCodReavaliacao === 0 && $dtReavaliacao != '' && $inVidaUtilReavaliacao  != '' && $flValorBemReavaliacao  != '' && $stMotivoReavaliacao != '' && $inserir === 'true') {
                                    $obErro = $obTPatrimonioReavaliacao->proximoCod( $inCodReavaliacao );
                                    if (!$obErro->ocorreu()) {
                                        $obTPatrimonioReavaliacao->setDado( 'cod_reavaliacao', $inCodReavaliacao );
                                        $obTPatrimonioReavaliacao->setDado( 'cod_bem', $inCodBem );
                                        $obTPatrimonioReavaliacao->setDado( 'dt_reavaliacao', $dtReavaliacao );
                                        $obTPatrimonioReavaliacao->setDado( 'vida_util', $inVidaUtilReavaliacao );
                                        $obTPatrimonioReavaliacao->setDado( 'vl_reavaliacao', $flValorBemReavaliacao );
                                        $obTPatrimonioReavaliacao->setDado( 'motivo', trim($stMotivoReavaliacao) );
                                        $obErro = $obTPatrimonioReavaliacao->inclusao();
                                    }
                                }
                                $rsReavaliacao->proximo();

                                if ($obErro->ocorreu())
                                    break;
                            }

                            if (!$obErro->ocorreu()) {
                                //inclui na table patrimonio.bem_comprado
                                $arMontaCodOrgaoM = explode("-", $_REQUEST['inMontaCodOrgaoM']);
                                $arMontaCodUnidadeM = explode("-", $_REQUEST['inMontaCodUnidadeM']);
                                $obTPatrimonioBemComprado->setDado( 'cod_bem'         , $inCodBem );
                                $obTPatrimonioBemComprado->setDado( 'exercicio'       , $_REQUEST['stExercicio']     );
                                $obTPatrimonioBemComprado->setDado( 'cod_entidade'    , $_REQUEST['inCodEntidade']   );
                                $obTPatrimonioBemComprado->setDado( 'cod_empenho'     , $_REQUEST['inNumEmpenho']    );
                                $obTPatrimonioBemComprado->setDado( 'nota_fiscal'     , $_REQUEST['stNumNotaFiscal'] );
                                $obTPatrimonioBemComprado->setDado( 'num_orgao'       , $_REQUEST['inCodOrgao']      );
                                $obTPatrimonioBemComprado->setDado( 'num_unidade'     , $_REQUEST['inCodUnidade']    );
                                $obTPatrimonioBemComprado->setDado( 'data_nota_fiscal', $_REQUEST['dataNotaFiscal'] );

                                if ( $_FILES['fileArquivoNF']['name'] != '' ) {
                                    $stDestinoAnexo    = CAM_GP_PAT_ANEXOS;
                                    $stEnderecoArquivo = $_FILES['fileArquivoNF']['tmp_name'];
                                    $stNomeArquivo	   = $_FILES['fileArquivoNF']['name'] ;
                                    if (file_exists($stDestinoAnexo.$stNomeArquivo)) {
                                        $obErro->setDescricao( 'Arquivo já existente, informe um arquivo com outro nome.' );
                                    } else {
                                        if ($_FILES['fileArquivoNF']['size'] < 10485760) {
                                            //Seta campo com nome do arquivo
                                            $obTPatrimonioBemComprado->setDado( 'caminho_nf' , $_FILES['fileArquivoNF']['name']);

                                            $boMoveArquivo = move_uploaded_file( $stEnderecoArquivo, $stDestinoAnexo.$stNomeArquivo );
                                            if (!$boMoveArquivo) {
                                                $obErro->setDescricao( 'Erro ao incluir arquivo.' );
                                            }
                                        } else {
                                            $obErro->setDescricao( 'Arquivo excede tamanho máximo de 10MB.' );
                                        }
                                    }
                                }

                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTPatrimonioBemComprado->inclusao();

                                    if (!$obErro->ocorreu()) {
                                        if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02 AND $_REQUEST['inCodTipoDocFiscal'] ) {
                                            $obTCEALBemCompradoTipoDocumentoFiscal = new TTCEALBemCompradoTipoDocumentoFiscal;
                                            $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_bem'                   , $inCodBem );
                                            $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_tipo_documento_fiscal' , $_REQUEST['inCodTipoDocFiscal'] );
                                            $obErro = $obTCEALBemCompradoTipoDocumentoFiscal->inclusao();
                                        }

                                        if (!$obErro->ocorreu()) {
                                            $arOrgao = explode('/',$_REQUEST['inCodOrgao'] );
                                            //inclui na table patrimonio.historico_bem
                                            $obTPatrimonioHistoricoBem->setDado( 'cod_bem', $inCodBem );
                                            $obTPatrimonioHistoricoBem->setDado( 'cod_situacao', $_REQUEST['inCodSituacao'] );
                                            $obTPatrimonioHistoricoBem->setDado( 'cod_local', $_REQUEST['inCodLocal'] );
                                            $obTPatrimonioHistoricoBem->setDado( 'cod_orgao', $_REQUEST['hdnUltimoOrgaoSelecionado'] );
                                            $obTPatrimonioHistoricoBem->setDado( 'ano_exercicio', Sessao::getExercicio() );
                                            $obTPatrimonioHistoricoBem->setDado( 'descricao', $_REQUEST['stDescricaoSituacao'] );
                                            $obErro = $obTPatrimonioHistoricoBem->inclusao();

                                            if (!$obErro->ocorreu()) {
                                                //incluir na table patrimonio.bem_responsavel
                                                $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $inCodBem );
                                                $obTPatrimonioBemResponsavel->setDado( 'numcgm', $_REQUEST['inNumResponsavel'] );
                                                $obTPatrimonioBemResponsavel->setDado( 'dt_inicio', $_REQUEST['dtInicioResponsavel'] );
                                                $obErro = $obTPatrimonioBemResponsavel->inclusao();
                                            }

                                            if (!$obErro->ocorreu()) {
                                                //incluir na table patrimonio.bem_marca se estiver informada
                                                if ($_REQUEST['inCodMarca'] != '') {
                                                    $obTPatrimonioBemMarca->setDado( 'cod_bem', $inCodBem );
                                                    $obTPatrimonioBemMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
                                                    $obErro = $obTPatrimonioBemMarca->inclusao();
                                                }
                                            }

                                            if (!$obErro->ocorreu()) {
                                                //inclui na table patrimonio.apolice_bem
                                                if ($_REQUEST['stApolice'] == 'sim') {
                                                    $obTPatrimonioApoliceBem->setDado( 'cod_bem', $inCodBem );
                                                    $obTPatrimonioApoliceBem->setDado( 'cod_apolice', $_REQUEST['inCodApolice'] );
                                                    $obErro = $obTPatrimonioApoliceBem->inclusao();
                                                }
                                            }

                                            if (!$obErro->ocorreu()) {
                                                //incluir na table patrimonio.bem_atributo_especie
                                                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_bem', $inCodBem );
                                                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
                                                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
                                                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
                                                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_modulo', 6 );
                                                $obTPatrimonioBemAtributoEspecie->setDado( 'cod_cadastro', 1 );

                                                foreach ($_POST as $stKey => $stValue) {
                                                    if ( strstr( $stKey, 'Atributo_' ) AND $stValue != '' ) {
                                                        $arAtributo = explode( '_', $stKey );
                                                        $obTPatrimonioBemAtributoEspecie->setDado( 'cod_atributo', $arAtributo[1] );
                                                        $obTPatrimonioBemAtributoEspecie->setDado( 'valor', $stValue );
                                                        $obErro = $obTPatrimonioBemAtributoEspecie->inclusao();
                                                    }

                                                    if ($obErro->ocorreu())
                                                        break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if($obErro->ocorreu())
                    break;
            }

            if (!$obErro->ocorreu()) {
                if ($inQtdeLote > 1) {
                    $stMsg = 'Bens: '.$arCodBem[0].' à '.$arCodBem[$i-1];
                } else {
                    $stMsg = 'Bem: '.$inCodBem.' - '.$_REQUEST['stNomBem'];
                }

                $obTPatrimonioGrupo = new TPatrimonioGrupo;
                $obTPatrimonioGrupo->setDado('cod_grupo'    , $_REQUEST['inCodGrupo']);
                $obTPatrimonioGrupo->setDado('cod_natureza' , $_REQUEST['inCodNatureza']);
                $obErro = $obTPatrimonioGrupo->recuperaDadosGrupo($rsGrupo);

                if (!$obErro->ocorreu()) {
                    if ($boDepreciavel == true && ($_REQUEST['flQuotaDepreciacaoAnual'] == "0.00" || empty($_REQUEST['flQuotaDepreciacaoAnual'])) && $rsGrupo->getCampo('depreciacao') == '0.00') {
                        $stMsg .= ". O bem não pode ser depreciado pois não existem quotas definidas.";
                    }

                    if($_REQUEST['inCodObra'] != '') {
                        $inCodObra = explode('|', $_REQUEST['inCodObra']);
                        $obTTGOPatrimonioBemObra = new TTGOPatrimonioBemObra;
                        $obTTGOPatrimonioBemObra->setDado('cod_bem'  , $inCodBem);
                        $obTTGOPatrimonioBemObra->setDado('ano_obra' , $inCodObra[0]);
                        $obTTGOPatrimonioBemObra->setDado('cod_obra' , $inCodObra[1]);
                        $obErro = $obTTGOPatrimonioBemObra->inclusao();
                    }

                    if (!$obErro->ocorreu()) {
                        $stJs .= "jQuery('#stNumeroPlaca', window.parent.frames['telaPrincipal'].document).val('".$numeroPlaca."');";
                        $stJs .= "jQuery('#stSpnListaReavaliacao', window.parent.frames['telaPrincipal'].document).html('');";
                        $stJs .= "window.parent.frames['telaPrincipal'].HabilitaLayer('layer_1');";
                        $stJs .= "d.getElementById('stCodClassificacao').focus();";
                        Sessao::remove('arReavaliacao');
                        SistemaLegado::executaFrameOculto($stJs);
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,$stMsg,"incluir","aviso", Sessao::getId(), "../");
        }

    break;

    case 'alterar' :
        # Exclui as reavaliações.
        $rsReavaliacaoExcluir = new RecordSet;
        $rsReavaliacaoExcluir->preenche(Sessao::read('arReavaliacaoExcluir'));

        while (!$rsReavaliacaoExcluir->eof()) {
            $inCodReavaliacao = $rsReavaliacaoExcluir->getCampo('inCodReavaliacao');
            $inCodBem         = $rsReavaliacaoExcluir->getCampo('inCodBem');

            $obTPatrimonioDepreciacaoReavaliacao = new TPatrimonioDepreciacaoReavaliacao;
            $obErro = $obTPatrimonioDepreciacaoReavaliacao->recuperaReavaliacao($rsDepreciacao, " AND reavaliacao.cod_reavaliacao = ".$inCodReavaliacao);
            
            if (!$obErro->ocorreu())
                $obErro = $obTPatrimonioDepreciacaoReavaliacao->recuperaRelacaoDepreciacao($rsRelacaoDepreciacao, "WHERE depreciacao_reavaliacao.cod_reavaliacao = ".$inCodReavaliacao." AND depreciacao_reavaliacao.cod_bem = ".$inCodBem);

            if (!$obErro->ocorreu()) {
                if ($rsDepreciacao->getNumLinhas() > 0) {
                    $obErro->setDescricao( "Não é possível excluir essa reavaliação, depreciação com data de competência igual ou superior" );
                } else {
                    $obTPatrimonioDepreciacaoReavaliacao = new TPatrimonioDepreciacaoReavaliacao;
                    $obTPatrimonioDepreciacaoReavaliacao->setDado( 'cod_depreciacao', $rsRelacaoDepreciacao->getCampo('cod_depreciacao') );
                    $obTPatrimonioDepreciacaoReavaliacao->setDado( 'cod_reavaliacao', $inCodReavaliacao );
                    $obErro = $obTPatrimonioDepreciacaoReavaliacao->exclusao();

                    if (!$obErro->ocorreu()) {
                        $obTContabilidadeLancamentoReavaliacao = new TContabilidadeLancamentoReavaliacao();
                        $obTContabilidadeLancamentoReavaliacao->setDado('cod_reavaliacao', $inCodReavaliacao );
                        $obTContabilidadeLancamentoReavaliacao->setDado('cod_bem'        , $inCodBem         );
                        $obErro = $obTContabilidadeLancamentoReavaliacao->recuperaPorChave($rsLancamentoReavaliacao);
                        
                        if (!$obErro->ocorreu()) {
                            if($rsLancamentoReavaliacao->getNumLinhas() > 0){
                                $obErro->setDescricao( "Não é possível excluir essa reavaliação, foram realizados lançamentos contábeis desta reavaliação!" );
                            }
                        }
                    }

                    if (!$obErro->ocorreu()) {
                        $obTPatrimonioReavaliacao = new TPatrimonioReavaliacao;
                        $obTPatrimonioReavaliacao->setDado( 'cod_reavaliacao', $inCodReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'cod_bem'        , $inCodBem );
                        $obErro = $obTPatrimonioReavaliacao->exclusao();
                    }
                }
            }

            $rsReavaliacaoExcluir->proximo();

            if ($obErro->ocorreu())
                break;
        }

        if (!$obErro->ocorreu()) {
            # Exclui as depreciações.
            $rsDepreciacaoExcluir = new RecordSet;
            $rsDepreciacaoExcluir->preenche(Sessao::read('arDepreciacaoExcluir'));

            while (!$rsDepreciacaoExcluir->eof()) {
                $inCodBem         = $rsDepreciacaoExcluir->getCampo('inCodBem');
                $inCodDepreciacao = $rsDepreciacaoExcluir->getCampo('inCodDepreciacao');
                $timestamp        = $rsDepreciacaoExcluir->getCampo('timestamp');

                $obTPatrimonioDepreciacaoReavaliacao = new TPatrimonioDepreciacaoReavaliacao;
                $obTPatrimonioDepreciacaoReavaliacao->setDado( 'cod_depreciacao', $inCodDepreciacao );
                $obTPatrimonioDepreciacaoReavaliacao->setDado( 'cod_bem', $inCodBem );
                $obErro = $obTPatrimonioDepreciacaoReavaliacao->exclusao();

                if (!$obErro->ocorreu()) {
                    $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao;
                    $obTPatrimonioDepreciacao->setDado( 'cod_depreciacao', $inCodDepreciacao );
                    $obTPatrimonioDepreciacao->setDado( 'cod_bem', $inCodBem );
                    $obErro = $obTPatrimonioDepreciacao->exclusao();
                }

                $rsDepreciacaoExcluir->proximo();
                
                if ($obErro->ocorreu())
                    break;
            }

            // Insere reavaliações
            $rsReavaliacao = new RecordSet;
            $rsReavaliacao->preenche(Sessao::read('arReavaliacao'));

            if (!$obErro->ocorreu()) {
                while (!$rsReavaliacao->eof() ) {
                    $inCodBem              = $rsReavaliacao->getCampo('inCodBem');
                    $inCodReavaliacao      = $rsReavaliacao->getCampo('inCodReavaliacao');
                    $dtReavaliacao         = $rsReavaliacao->getCampo('dtReavaliacao');
                    $inVidaUtilReavaliacao = $rsReavaliacao->getCampo('inVidaUtilReavaliacao');
                    $flValorBemReavaliacao = $rsReavaliacao->getCampo('flValorBemReavaliacao');
                    $stMotivoReavaliacao   = $rsReavaliacao->getCampo('stMotivoReavaliacao');
                    $inserir               = $rsReavaliacao->getCampo('inserir');

                    if ($inCodBem != '' && $inCodReavaliacao === 0 && $dtReavaliacao != '' && $inVidaUtilReavaliacao  != '' && $flValorBemReavaliacao  != '' && $stMotivoReavaliacao != '' && $inserir === 'true') {
                        $obTPatrimonioReavaliacao->proximoCod( $inCodReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'cod_reavaliacao', $inCodReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'cod_bem', $inCodBem );
                        $obTPatrimonioReavaliacao->setDado( 'dt_reavaliacao', $dtReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'vida_util', $inVidaUtilReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'vl_reavaliacao', $flValorBemReavaliacao );
                        $obTPatrimonioReavaliacao->setDado( 'motivo', trim($stMotivoReavaliacao) );
                        $obErro = $obTPatrimonioReavaliacao->inclusao();
                    }
                    $rsReavaliacao->proximo();

                    if ($obErro->ocorreu())
                        break;
                }
            }

            $boDepreciavel          = $_REQUEST['boDepreciavel'];
            $stDepreciacaoAcelerada = $_REQUEST['boDepreciacaoAcelerada'];
            $inCodBem               = $_REQUEST['inCodBem'];
            $inCodPlano             = $_REQUEST['inCodContaAnalitica'];
            $inCodContaDepreciacao  = $_REQUEST['inCodContaDepreciacao'];
            $inExercicio            = Sessao::getExercicio();

            if (!empty($_REQUEST['stChaveProcesso'])) {
                $arProcesso = array();
                $arProcesso = explode("/", $_REQUEST['stChaveProcesso']);
                $inCodProcesso = $arProcesso[0];
                $stAnoProcesso = $arProcesso[1];    
            } else {
                $arProcesso = "";
            }

            if (!$obErro->ocorreu()) {
                if ($boDepreciavel === 'true') {
                    if (!empty($inCodPlano)) {
                        $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica;
                        $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_plano', $inCodPlano);
                        $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $inCodBem );
                        $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio', $inExercicio);

                        $rsBemPlanoAnalitica = new RecordSet();
                        $obErro = $obTPatrimonioBemPlanoAnalitica->recuperaMaxTimestampBemPlanoAnalitica($rsBemPlanoAnalitica);

                        if (!$obErro->ocorreu()) {
                            if ($rsBemPlanoAnalitica->getCampo('timestamp') == '' || ($rsBemPlanoAnalitica->getCampo('timestamp') < $rsBemPlanoAnalitica->getCampo('ultimo_timestamp'))) {
                                $obErro = $obTPatrimonioBemPlanoAnalitica->inclusao();
                            }
                        }
                    } else {
                        $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica;
                        $obTPatrimonioBemPlanoAnalitica->setDado('cod_plano', $inCodPlano);
                        $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $inCodBem );
                        $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio', $inExercicio);

                        $rsBemPlanoAnalitica = new RecordSet();
                        $obErro = $obTPatrimonioBemPlanoAnalitica->recuperaMaxTimestampBemPlanoAnalitica($rsBemPlanoAnalitica);

                        if (!$obErro->ocorreu()) {
                            if ($rsBemPlanoAnalitica->getNumLinhas() > 0) {
                                $obErro = $obTPatrimonioBemPlanoAnalitica->exclusao();
                            }
                        }
                    }

                    if ($stDepreciacaoAcelerada === "true") {
                        $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'true' );
                        $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', $_REQUEST['flQuotaDepreciacaoAcelerada'] );
                    } else {
                        $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'false' );
                        $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', '0,00' );
                    }
        
                    $obTPatrimonioBem->setDado( 'depreciavel', 'true');
                    $obTPatrimonioBem->setDado( 'quota_depreciacao_anual', $_REQUEST['flQuotaDepreciacaoAnual'] );
                } else {
                    # Terá vinculo com a GF.
                    # $obTPatrimonioBemPlanoAnalitica->recuperaBemPlanoAnalitica($rsBemPlanoAnalitica);
                    # $obTPatrimonioBemPlanoAnalitica->exclusao();

                    $obTPatrimonioBem->setDado( 'depreciavel', 'false');
                    $obTPatrimonioBem->setDado( 'depreciacao_acelerada', 'false' );
                    $obTPatrimonioBem->setDado( 'quota_depreciacao_anual', '0,00' );
                    $obTPatrimonioBem->setDado( 'quota_depreciacao_anual_acelerada', '0,00' );
                }
            }

            if (!$obErro->ocorreu()) {
                //verifica se já existe um responsável pelo bem
                $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                $obErro = $obTPatrimonioBemResponsavel->recuperaUltimoResponsavel( $rsBemResponsavel );

                if (!$obErro->ocorreu()) {
                    //verifica se o numero da placa já existe
                    $stFiltro = " WHERE num_placa = '".$_REQUEST['stNumeroPlaca']."' AND cod_bem  <> ".$_REQUEST['inCodBem'];
                    $obErro = $obTPatrimonioBem->recuperaTodos($rsBem, $stFiltro);
                }

                if (!$obErro->ocorreu()) {
                    // Reupera alguma depreciação na competencia se o bem possuir
                    $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao();
                    $obTPatrimonioDepreciacao->setDado('cod_bem', $inCodBem);
                    $obErro = $obTPatrimonioDepreciacao->recuperaDepreciacao($rsDepreciado, " AND SUBSTR(depreciacao.competencia, 0,5) = '".Sessao::getExercicio()."'");
                }

                if (!$obErro->ocorreu()) {
                    // Recupera o cod_plano se estiver cadastrado no bem
                    $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_bem'   , $inCodBem );
                    $obTPatrimonioBemPlanoDepreciacao->setDado( 'exercicio' , $inExercicio);
                    $obErro = $obTPatrimonioBemPlanoDepreciacao->recuperaBemPlanoDepreciacao( $rsBemPlanoDepreciacao );
                }

                if (!$obErro->ocorreu()) {
                    // Recupera o cod_plano se estiver cadastrado no grupo        
                    $obTPatrimonioGrupo = new TPatrimonioGrupo();
                    $obTPatrimonioGrupo->setDado('cod_bem' , $inCodBem);
                    $obErro = $obTPatrimonioGrupo->recuperaGrupoPlanoDepreciacao( $rsGrupoPlanoDepreciacao );
                }

                if (!$obErro->ocorreu()) {
                    // Verifica se o bem possui depreicação na competencia atual, caso sim, não poderá alterar o valor da conta contabil de depreciação até que anule todas as depreciações
                    if( $rsDepreciado->getNumLinhas() >= 1 ){
                        // Verifica se a conta foi modificada e está cadastrada no bem, pois é a que prevalece sobre o grupo
                        if ( $rsBemPlanoDepreciacao->getNumLinhas() >= 1 && $rsBemPlanoDepreciacao->getCampo("cod_plano") != $inCodContaDepreciacao ) {
                            $obErro->setDescricao( "Já existem depreciações lançadas para este bem. Anule-as para alterar a Conta Contábil de Depreciação." );
                        // se não verifica se sofreu alteração e está diferente da cadastrada no grupo
                        } else if ($rsGrupoPlanoDepreciacao->getNumLinhas() >= 1 && $rsGrupoPlanoDepreciacao->getCampo('cod_plano') != $inCodContaDepreciacao && !empty($inCodContaDepreciacao)) {
                            $obErro->setDescricao( "Já existem depreciações lançadas para este bem. Anule-as para alterar a Conta Contábil de Depreciação." );
                        }
                    } else {
                        //Caso não exista depreciação faz o processo de incluir ou excluir um conta contabil de depreciação para o bem
                        $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_bem'  , $inCodBem );
                        $obTPatrimonioBemPlanoDepreciacao->setDado( 'exercicio', $inExercicio);
                        
                        if (!empty($inCodContaDepreciacao)) {
                            $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_plano', $inCodContaDepreciacao);
                            $obErro = $obTPatrimonioBemPlanoDepreciacao->inclusao();
                        } else {
                            $obErro = $obTPatrimonioBemPlanoDepreciacao->exclusao();
                        }
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                //verifica a integridade dos valores
                if ($_REQUEST['inValorBem'] == '0,00') {
                    $obErro->setDescricao( 'Valor do bem inválido' );
                } elseif (!empty($_REQUEST['inValorDepreciacao']) && (str_replace(",",".",str_replace(".", "", $_REQUEST['inValorDepreciacao'])) > str_replace(",",".",str_replace(".", "", $_REQUEST['inValorBem'])))) {
                    $obErro->setDescricao( 'O valor da Depreciação Inicial não pode ser maior que o valor do bem.' );
                } elseif ( $_REQUEST['dtDepreciacao'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtDepreciacao'])) ) {
                    $obErro->setDescricao( 'A data de depreciação deve ser maior ou igual a data de aquisição' );
                } elseif ( $_REQUEST['dtVencimento'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtVencimento'])) ) {
                    $obErro->setDescricao( 'A data de vencimento da garantia deve ser maior ou igual a data de aquisição' );
                } elseif ( $_REQUEST['dtIncorporacao'] != '' AND array_reverse(explode('/',$_REQUEST['dtAquisicao'])) > array_reverse(explode( '/', $_REQUEST['dtIncorporacao'])) ) {
                    $obErro->setDescricao( 'A data de incorporação deve ser maior ou igual a data de aquisição' );
                } elseif ($_REQUEST['stPlacaIdentificacao'] == 'sim' AND $_REQUEST['stNumeroPlaca'] == '') {
                    $obErro->setDescricao( 'Número da placa inválido' );
                } elseif ( $rsBem->getNumLinhas() > 0 ) {
                    $obErro->setDescricao( 'Este número da placa já existe' );
                } elseif ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) < implode('',array_reverse(explode('/',$_REQUEST['dtAquisicao']))) ) {
                    $obErro->setDescricao( 'A data de início do responsável deve ser maior ou igual a data de aquisição' );
                } elseif ( $rsBemResponsavel->getNumLinhas() > 0 AND ( implode('',array_reverse(explode('/',$rsBemResponsavel->getCampo('dt_inicio')))) >  implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) ) AND ( $_REQUEST['inNumResponsavel'] <> $rsBemResponsavel->getCampo('numcgm') )) {
                    $obErro->setDescricao( 'A data de início do responsável deve ser posterior ou igual a do atual responsável('.implode('/',array_reverse(explode('-',$rsBemResponsavel->getCampo('dt_inicio')))).')' );
                } elseif ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) > date('Ymd') ) {
                    $obErro->setDescricao( 'A data de início do responsável deve ser menor ou igual a data de hoje' );
                } elseif ($_REQUEST['stApolice'] == 'sim' AND $_REQUEST['inCodSeguradora'] == '') {
                    $obErro->setDescricao( 'Selecione uma seguradora' );
                } elseif ($_REQUEST['stApolice'] == 'sim' AND $_REQUEST['inCodApolice'] == '') {
                    $obErro->setDescricao( 'Selecione uma apólice' );
                } elseif (((float) (str_replace(',','.',$_REQUEST['flQuotaDepreciacaoAnual'])) + (float) (str_replace(',','.',$_REQUEST['flQuotaDepreciacaoAcelerada']))) > 100) {
                    $obErro->setDescricao( 'A soma das quotas (anual+acelerada) não pode ultrapassar 100%.' );
                } elseif ($_REQUEST['boDepreciacaoAcelerada'] == 'true' && (empty($_REQUEST['flQuotaDepreciacaoAcelerada']) || $_REQUEST['flQuotaDepreciacaoAcelerada'] == '0,00')) {
                    $obErro->setDescricao( 'O valor da quota acelerada deve ser informado e maior que zero.' );
                }elseif($boDepreciavel === 'true' && !empty($_REQUEST['inCodContaDepreciacao']) && ($_REQUEST['flQuotaDepreciacaoAnual'] == '0,00' || $_REQUEST['flQuotaDepreciacaoAnual'] == '0.00' || $_REQUEST['flQuotaDepreciacaoAnual'] == '')){
                    $obErro->setDescricao( 'O valor da quota de depreciação Anual deve ser maior que zero' );
                }elseif(empty($_REQUEST['inCodContaDepreciacao']) && isset($_REQUEST['flQuotaDepreciacaoAnual']) && $_REQUEST['flQuotaDepreciacaoAnual'] != '0,00'){
                    $obErro->setDescricao( 'O valor da Conta Contábil de Depreciação Acumulada deve ser informado.' );
                }elseif(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02 ){
                    if($_REQUEST['stNumNotaFiscal'] != '' and  $_REQUEST['inCodTipoDocFiscal'] == ''){
                        $obErro->setDescricao( 'Informe o Tipo do Documento Fiscal' );    
                    }elseif($_REQUEST['stNumNotaFiscal'] == '' && $_REQUEST['inCodTipoDocFiscal'] != '' ){
                        $obErro->setDescricao( 'O campo Tipo do Documento Fiscal não deve ser preenchido, quando não houver  um Número da Nota Fiscal' );
                    }
                }
                if( $_REQUEST['stNumNotaFiscal'] != '' && empty($_REQUEST['dataNotaFiscal']) ){
                    $obErro->setDescricao( 'O campo Data da Nota Fiscal deve ser preenchido' );
                }elseif($_REQUEST['stNumNotaFiscal'] == '' && $_REQUEST['dataNotaFiscal'] != '' ){
                    $obErro->setDescricao( 'O campo Data da Nota Fiscal não deve ser preenchido, quando não houver um Número da Nota Fiscal' );
                }
                $rsAtributosDinamicos = Sessao::read('rsAtributosDinamicos');
                if ( is_array( $rsAtributosDinamicos->arElementos ) ) {
                    while ( !$rsAtributosDinamicos->eof() ) {
                        if ( $rsAtributosDinamicos->getCampo('nao_nulo') == 'f' AND $_REQUEST['Atributo_'.$rsAtributosDinamicos->getCampo('cod_atributo').'_'.$rsAtributosDinamicos->getCampo('cod_cadastro')] == '' ) {
                            $obErro->setDescricao( 'Preencha o campo '.$rsAtributosDinamicos->getCampo('nom_atributo') );
                            break;
                        }
                        $rsAtributosDinamicos->proximo();
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                //altera a table patrimonio.bem
                $obTPatrimonioBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                $obTPatrimonioBem->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
                $obTPatrimonioBem->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
                $obTPatrimonioBem->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
                $obTPatrimonioBem->setDado( 'numcgm', $_REQUEST['inCodFornecedor'] );
                $obTPatrimonioBem->setDado( 'descricao', $_REQUEST['stNomBem'] );
                $obTPatrimonioBem->setDado( 'detalhamento', $_REQUEST['stDetalhamentoBem'] );
                $obTPatrimonioBem->setDado( 'dt_aquisicao', $_REQUEST['dtAquisicao'] );
                $obTPatrimonioBem->setDado( 'vida_util', $_REQUEST['inVidaUtil'] );
                $obTPatrimonioBem->setDado( 'dt_incorporacao', $_REQUEST['dtIncorporacao'] );
                $obTPatrimonioBem->setDado( 'dt_depreciacao', $_REQUEST['dtDepreciacao'] );
                $obTPatrimonioBem->setDado( 'dt_garantia', $_REQUEST['dtVencimento'] );
                $obTPatrimonioBem->setDado( 'vl_bem', str_replace(',','.',str_replace('.','',$_REQUEST['inValorBem'])) );

                if ($_REQUEST['inValorDepreciacao'] != '') {
                    $obTPatrimonioBem->setDado( 'vl_depreciacao', str_replace(',','.',str_replace('.','',$_REQUEST['inValorDepreciacao'])) );
                } else {
                    $obTPatrimonioBem->setDado( 'vl_depreciacao', 0.00);
                }

                $obTPatrimonioBem->setDado( 'identificacao', ( $_REQUEST['stPlacaIdentificacao'] == 'sim' ) ? true : false );

                if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
                    $obTPatrimonioBem->setDado( 'num_placa', $_REQUEST['stNumeroPlaca'] );
                } else {
                    $arEstrutura = $obTPatrimonioBem->getEstrutura();
                    foreach ($arEstrutura as $key => $value) {
                        if ($value->stNomeCampo == 'num_placa') {
                            $keyField = $key;
                            break;
                        }
                    }

                    # Seta provisoriamente o tipo do campo como INTEGER para gravar o valor null ao invés de ''.
                    $arEstrutura[$keyField]->setTipoCampo('integer');
                    $obTPatrimonioBem->setEstrutura($arEstrutura);
                    $obTPatrimonioBem->setDado( 'num_placa', 'null' );
                }

                $obErro = $obTPatrimonioBem->alteracao();

                if (!$obErro->ocorreu()) {
                    if (!empty($_REQUEST['stChaveProcesso'])) {
                        $obTPatrimonioBemProcesso->setDado('cod_bem', $inCodBem);
                        $obTPatrimonioBemProcesso->setDado('ano_exercicio', $stAnoProcesso);
                        $obTPatrimonioBemProcesso->setDado('cod_processo', $inCodProcesso);
                        $obErro = $obTPatrimonioBemProcesso->recuperaPorChave($rsProcesso);

                        if (!$obErro->ocorreu()) {
                            if ($rsProcesso->getNumLinhas() > 0) {
                                $obErro = $obTPatrimonioBemProcesso->alteracao();
                            }  else {
                                $obErro = $obTPatrimonioBemProcesso->inclusao();
                            }
                        }
                    } elseif (!empty($_REQUEST['hdnChaveProcesso'])) {
                        $arProcessoAux = explode("/",$_REQUEST['hdnChaveProcesso']);
                        $stAnoProcessoAux = $arProcessoAux[0];
                        $stCodProcessoAux = $arProcessoAux[1];
                        $obTPatrimonioBemProcesso->setDado('cod_bem', $inCodBem);
                        $obTPatrimonioBemProcesso->setDado('ano_exercicio', $stAnoProcessoAux);
                        $obTPatrimonioBemProcesso->setDado('cod_processo', $stCodProcessoAux);
                        $obErro = $obTPatrimonioBemProcesso->recuperaPorChave($rsProcesso);

                        if (!$obErro->ocorreu()) {
                            if ($rsProcesso->getNumLinhas() > 0) {
                                $obErro = $obTPatrimonioBemProcesso->exclusao();
                            }
                        }
                    }
                }

                if (!$obErro->ocorreu()) {
                    $obErro = $obTPatrimonioBemComprado->recuperaTodos($rsBemComprado, ' WHERE cod_bem = '.$_REQUEST['inCodBem'].' ' );

                    if (!$obErro->ocorreu()) {
                        if ( $rsBemComprado->getNumLinhas() > 0) {
                            //altera a table patrimonio.bem_comprado
                            $arMontaCodOrgaoM   = explode("-", $_REQUEST['inMontaCodOrgaoM'  ]);
                            $arMontaCodUnidadeM = explode("-", $_REQUEST['inMontaCodUnidadeM']);

                            $obTPatrimonioBemComprado->setDado( 'cod_bem'          , $_REQUEST['inCodBem'       ]);
                            $obTPatrimonioBemComprado->setDado( 'exercicio'        , $_REQUEST['stExercicio'    ]);
                            $obTPatrimonioBemComprado->setDado( 'cod_entidade'     , $_REQUEST['inCodEntidade'  ]);
                            $obTPatrimonioBemComprado->setDado( 'cod_empenho'      , $_REQUEST['inNumEmpenho'   ]);
                            $obTPatrimonioBemComprado->setDado( 'nota_fiscal'      , $_REQUEST['stNumNotaFiscal']);
                            $obTPatrimonioBemComprado->setDado( 'num_orgao'        , $_REQUEST['inCodOrgao'     ]);
                            $obTPatrimonioBemComprado->setDado( 'num_unidade'      , $_REQUEST['inCodUnidade'   ]);
                            $obTPatrimonioBemComprado->setDado( 'data_nota_fiscal' , $_REQUEST['dataNotaFiscal' ]);

                            if ( $_FILES['fileArquivoNF']['name'] != '' ) {
                                $stDestinoAnexo    = CAM_GP_PAT_ANEXOS;
                                $stEnderecoArquivo = $_FILES['fileArquivoNF']['tmp_name'];
                                $stNomeArquivo	   = $_FILES['fileArquivoNF']['name'] ;
                                if (file_exists($stDestinoAnexo.$stNomeArquivo)) {
                                    $obErro->setDescricao( 'Arquivo já existente, informe um arquivo com outro nome.' );
                                } else {
                                    if ($_FILES['fileArquivoNF']['size'] < 10485760) {
                                        //Seta campo com nome do arquivo
                                        $obTPatrimonioBemComprado->setDado( 'caminho_nf' , $_FILES['fileArquivoNF']['name']);

                                        $boMoveArquivo = move_uploaded_file( $stEnderecoArquivo, $stDestinoAnexo.$stNomeArquivo );
                                        if (!$boMoveArquivo) {
                                            $obErro->setDescricao( 'Erro ao incluir arquivo.' );
                                        }
                                    } else {
                                        $obErro->setDescricao( 'Arquivo excede tamanho máximo de 10MB.' );
                                    }
                                }
                            }

                            if (!$obErro->ocorreu()) {
                                $obErro = $obTPatrimonioBemComprado->alteracao();

                                if (!$obErro->ocorreu()) {
                                    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02  ) {
                                        $obTCEALBemCompradoTipoDocumentoFiscal = new TTCEALBemCompradoTipoDocumentoFiscal;
                                        $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_bem'    , $inCodBem );
                                        $obErro = $obTCEALBemCompradoTipoDocumentoFiscal->recuperaPorChave($rsTipoNotaFiscal);

                                        if (!$obErro->ocorreu()) {
                                            if($_REQUEST['inCodTipoDocFiscal']){
                                                $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_tipo_documento_fiscal' , $_REQUEST['inCodTipoDocFiscal'] );

                                                if( $rsTipoNotaFiscal->getNumLinhas() > 0){
                                                    $obErro = $obTCEALBemCompradoTipoDocumentoFiscal->alteracao();
                                                }else{
                                                    $obErro = $obTCEALBemCompradoTipoDocumentoFiscal->inclusao();
                                                }
                                            }

                                            if (!$obErro->ocorreu()) {
                                                if(empty($_REQUEST['inCodTipoDocFiscal'])){ 
                                                    if( $rsTipoNotaFiscal->getNumLinhas() > 0) {
                                                        $obErro = $obTCEALBemCompradoTipoDocumentoFiscal->exclusao();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            //inclui na table patrimonio.bem_comprado
                            $arMontaCodOrgaoM   = explode("-", $_REQUEST['inMontaCodOrgaoM'  ] );
                            $arMontaCodUnidadeM = explode("-", $_REQUEST['inMontaCodUnidadeM'] );

                            $obTPatrimonioBemComprado->setDado( 'cod_bem'          , $_REQUEST['inCodBem'       ] );
                            $obTPatrimonioBemComprado->setDado( 'exercicio'        , $_REQUEST['stExercicio'    ] );
                            $obTPatrimonioBemComprado->setDado( 'cod_entidade'     , $_REQUEST['inCodEntidade'  ] );
                            $obTPatrimonioBemComprado->setDado( 'cod_empenho'      , $_REQUEST['inNumEmpenho'   ] );
                            $obTPatrimonioBemComprado->setDado( 'nota_fiscal'      , $_REQUEST['stNumNotaFiscal'] );
                            $obTPatrimonioBemComprado->setDado( 'num_orgao'        , $_REQUEST['inCodOrgao'     ] );
                            $obTPatrimonioBemComprado->setDado( 'num_unidade'      , $_REQUEST['inCodUnidade'   ] );
                            $obTPatrimonioBemComprado->setDado( 'data_nota_fiscal' , $_REQUEST['dataNotaFiscal' ] );

                            $obErro = $obTPatrimonioBemComprado->inclusao();

                            if (!$obErro->ocorreu()) {
                                if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02  ) {
                                    $obTCEALBemCompradoTipoDocumentoFiscal = new TTCEALBemCompradoTipoDocumentoFiscal;
                                    $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_bem'    , $inCodBem );
                                    $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_tipo_documento_fiscal' , $_REQUEST['inCodTipoDocFiscal'] );
                                    $obErro = $obTCEALBemCompradoTipoDocumentoFiscal->inclusao();
                                }
                            }
                        }
                    }
                }

                if (!$obErro->ocorreu()) {
                    //inclui na table patrimonio.historico_bem
                    $obTPatrimonioHistoricoBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                    $obTPatrimonioHistoricoBem->setDado( 'cod_situacao', $_REQUEST['inCodSituacao'] );
                    $obTPatrimonioHistoricoBem->setDado( 'cod_local', $_REQUEST['inCodLocal'] );
                    $obTPatrimonioHistoricoBem->setDado( 'cod_orgao', $_REQUEST['hdnUltimoOrgaoSelecionado'] );
                    $obTPatrimonioHistoricoBem->setDado( 'ano_exercicio', Sessao::getExercicio() );
                    $obTPatrimonioHistoricoBem->setDado( 'descricao', $_REQUEST['stDescricaoSituacao'] );
                    $obErro = $obTPatrimonioHistoricoBem->inclusao();

                    if (!$obErro->ocorreu()) {
                        //verifica se existe um responsável cadastrado
                        $obErro = $obTPatrimonioBemResponsavel->recuperaTodos( $rsResponsavel, ' WHERE cod_bem = '.$_REQUEST['inCodBem'].' ' );
                    }

                    if (!$obErro->ocorreu()) {
                        if ( $rsResponsavel->getNumLinhas() > 0) {
                            $obTPatrimonioBemResponsavel->setDado( 'numcgm',$_REQUEST['inNumResponsavel'] );
                            $obTPatrimonioBemResponsavel->setDado( 'dt_inicio',$_REQUEST['dtInicioResponsavel'] );
                            $obErro = $obTPatrimonioBemResponsavel->recuperaUltimoResponsavel( $rsUltimoResponsavel );
                            if (!$obErro->ocorreu()) {
                                if ( $rsUltimoResponsavel->getNumLinhas() <= 0) {
                                    $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                                    $obTPatrimonioBemResponsavel->setDado( 'numcgm', $_REQUEST['inNumResponsavel'] );
                                    $obTPatrimonioBemResponsavel->setDado( 'dt_inicio', $_REQUEST['dtInicioResponsavel'] );
                                    $obTPatrimonioBemResponsavel->setDado( 'dt_fim', '' );
                                    $obErro = $obTPatrimonioBemResponsavel->inclusao();

                                    if (!$obErro->ocorreu()) {
                                        //altera a table patrimonio.bem_responsavel
                                        //coloca a data de fim do último responsável e inclui o novo
                                        $obTPatrimonioBemResponsavel->setDado( 'cod_bem'  , $_REQUEST['inCodBem'] );
                                        $obTPatrimonioBemResponsavel->setDado( 'timestamp', $rsBemResponsavel->getCampo( 'timestamp' ) );
                                        $obTPatrimonioBemResponsavel->setDado( 'dt_inicio', $rsBemResponsavel->getCampo( 'dt_inicio' ) );
                                        $obTPatrimonioBemResponsavel->setDado( 'dt_fim'   , $_REQUEST['dtInicioResponsavel'] );
                                        $obTPatrimonioBemResponsavel->setDado( 'numcgm'   , $rsBemResponsavel->getCampo( 'numcgm' ) );
                                        $obErro = $obTPatrimonioBemResponsavel->alteracao();
                                    }
                                }
                            }
                        } else {
                            $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                            $obTPatrimonioBemResponsavel->setDado( 'numcgm', $_REQUEST['inNumResponsavel'] );
                            $obTPatrimonioBemResponsavel->setDado( 'dt_inicio', $_REQUEST['dtInicioResponsavel'] );
                            $obTPatrimonioBemResponsavel->setDado( 'dt_fim', '' );
                            $obErro = $obTPatrimonioBemResponsavel->inclusao();
                        }
                    }

                    if (!$obErro->ocorreu()) {
                        $obErro = $obTPatrimonioBemMarca->recuperaTodos( $rsMarca, ' WHERE cod_bem = '.$_REQUEST['inCodBem'].' ' );
                    }

                    if (!$obErro->ocorreu()) {
                        if ( $rsMarca->eof()) {
                            if ($_REQUEST['inCodMarca'] != '') {
                                $obTPatrimonioBemMarca->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                                $obTPatrimonioBemMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
                                $obErro = $obTPatrimonioBemMarca->inclusao();
                            }
                        } else {
                            if ($_REQUEST['inCodMarca'] != '') {
                                $obTPatrimonioBemMarca->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                                $obTPatrimonioBemMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
                                $obErro = $obTPatrimonioBemMarca->alteracao();
                            }
                        }
                    }

                    if (!$obErro->ocorreu()) {
                        //inclui na table patrimonio.apolice_bem
                        if ($_REQUEST['stApolice'] == 'sim') {
                            $obTPatrimonioApoliceBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                            $obErro = $obTPatrimonioApoliceBem->recuperaMaxApoliceBem( $rsApoliceBem );

                            if (!$obErro->ocorreu()) {
                                //verifica se a última apolice inserida na base não é a mesma que está sendo alterada
                                if ( $_REQUEST['inCodApolice'] != $rsApoliceBem->getCampo('cod_apolice') ) {
                                    $obTPatrimonioApoliceBem->setDado( 'cod_apolice', $_REQUEST['inCodApolice'] );
                                    $obErro = $obTPatrimonioApoliceBem->inclusao();
                                }
                            }
                        }
                    }

                    if (!$obErro->ocorreu()) {
                        //deleta todos os registros da table patrimonio.bem_atributo_especie
                        $obTPatrimonioBemAtributoEspecie->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                        $obErro = $obTPatrimonioBemAtributoEspecie->exclusao();
                    }

                    if (!$obErro->ocorreu()) {
                        //inclui na table patrimonio.bem_atributo_especie
                        $obTPatrimonioBemAtributoEspecie->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
                        $obTPatrimonioBemAtributoEspecie->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
                        $obTPatrimonioBemAtributoEspecie->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
                        $obTPatrimonioBemAtributoEspecie->setDado( 'cod_modulo', 6 );
                        $obTPatrimonioBemAtributoEspecie->setDado( 'cod_cadastro', 1 );

                        $obTPatrimonioGrupo = new TPatrimonioGrupo;
                        $obTPatrimonioGrupo->setDado('cod_grupo'    , $_REQUEST['inCodGrupo']);
                        $obTPatrimonioGrupo->setDado('cod_natureza' , $_REQUEST['inCodNatureza']);
                        $obErro = $obTPatrimonioGrupo->recuperaDadosGrupo($rsGrupo);

                        if (!$obErro->ocorreu()) {
                            if ($boDepreciavel == true && ($_REQUEST['flQuotaDepreciacaoAnual'] == "0.00" || empty($_REQUEST['flQuotaDepreciacaoAnual'])) && $rsGrupo->getCampo('depreciacao') == '0.00') {
                                $stMsg .= ". O bem não pode ser depreciado pois não existem quotas definidas.";
                            }

                            $obTTGOPatrimonioBemObra = new TTGOPatrimonioBemObra;
                            $obTTGOPatrimonioBemObra->setDado('cod_bem', $_REQUEST['inCodBem']);

                            $obErro = $obTTGOPatrimonioBemObra->recuperaPorChave($rsBemObra);

                            if (!$obErro->ocorreu()) {
                                if( $rsBemObra->getNumLinhas() > 0) {
                                    $obErro = $obTTGOPatrimonioBemObra->exclusao();
                                }
                            }

                            if (!$obErro->ocorreu()) {
                                if($_REQUEST['inCodObra'] != '') {
                                    $inCodObra = explode('|', $_REQUEST['inCodObra']);
                                    $obTTGOPatrimonioBemObra = new TTGOPatrimonioBemObra;
                                    $obTTGOPatrimonioBemObra->setDado('cod_bem'  , $_REQUEST['inCodBem']);
                                    $obTTGOPatrimonioBemObra->setDado('ano_obra' , $inCodObra[0]);
                                    $obTTGOPatrimonioBemObra->setDado('cod_obra' , $inCodObra[1]);
                                    $obErro = $obTTGOPatrimonioBemObra->inclusao();
                                }

                                if (!$obErro->ocorreu()) {
                                    foreach ($_POST as $stKey => $stValue) {
                                        if (strstr( $stKey, 'Atributo_' ) AND $stValue != '' ) {
                                            $arAtributo = explode( '_', $stKey );
                                            $obTPatrimonioBemAtributoEspecie->setDado( 'cod_atributo', $arAtributo[1] );
                                            $obTPatrimonioBemAtributoEspecie->setDado( 'valor', $stValue );
                                            $obErro = $obTPatrimonioBemAtributoEspecie->inclusao();
                                        }

                                        if ($obErro->ocorreu())
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            Sessao::remove('arReavaliacaoExcluir');
            Sessao::remove('stDepreciacaoCompetencia');
            Sessao::remove('arDepreciacaoExcluir');
            Sessao::remove('arReavaliacao');

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Bem: ".$_REQUEST['inCodBem'].' - '.$_REQUEST['stNomBem'].$stMsg,"alterar","aviso", Sessao::getId(), "../");
        }

        break;

    case 'excluir' :
        $inCodBem = $_REQUEST['inCodBem'];

         //exclui da tabela bem_comprado_tipo_documento_fiscal
        if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02  ) {
            $obTCEALBemCompradoTipoDocumentoFiscal = new TTCEALBemCompradoTipoDocumentoFiscal;
            $obTCEALBemCompradoTipoDocumentoFiscal->setDado( 'cod_bem'    , $inCodBem );
            $obErro = $obTCEALBemCompradoTipoDocumentoFiscal->recuperaPorChave($rsTipoNotaFiscal);

            if (!$obErro->ocorreu()) {
                if( $rsTipoNotaFiscal->getNumLinhas() > 0) {
                    $obErro = $obTCEALBemCompradoTipoDocumentoFiscal->exclusao();
                }
            }
        }

        if (!$obErro->ocorreu()) {
            //exclui da tabela bem_comprado
            $obTPatrimonioBemComprado->setDado( 'cod_bem', $inCodBem );
            $obTPatrimonioBemComprado->exclusao();
        }
        
        if (!$obErro->ocorreu()) {
            //exclui da tabela apolice_bem
            $obErro = $obTPatrimonioApoliceBem->recuperaTodos( $rsApoliceBem, ' WHERE cod_bem = '.$inCodBem.' ' );
            if (!$obErro->ocorreu()) {
                while ( !$rsApoliceBem->eof() ) {
                    $obTPatrimonioApoliceBem->setDado( 'cod_bem', $rsApoliceBem->getCampo('cod_bem'));
                    $obTPatrimonioApoliceBem->setDado( 'cod_apolice', $rsApoliceBem->getCampo('cod_apolice'));
                    $obErro = $obTPatrimonioApoliceBem->exclusao();
                    $rsApoliceBem->proximo();

                    if ($obErro->ocorreu())
                        break;
                }
            }
        }

        if (!$obErro->ocorreu()) {
            //exclui da tabela bem_responsavel
            $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $inCodBem );
            $obErro = $obTPatrimonioBemResponsavel->exclusao();
        }

        if (!$obErro->ocorreu()) {
            //exclui da tabela bem_marca
            $obTPatrimonioBemMarca->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
            $obErro = $obTPatrimonioBemMarca->exclusao();
        }

        if (!$obErro->ocorreu()) {
            //exclui da table iventario_historico_bem
            $obTPatrimonioInventarioHistoricoBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
            $obErro = $obTPatrimonioInventarioHistoricoBem->recuperaTodos( $rsInventario, ' WHERE cod_bem = '.$_REQUEST['inCodBem'].' ' );
            if (!$obErro->ocorreu()) {
                while (!$rsInventario->eof()) {
                    $obTPatrimonioInventarioHistoricoBem->setDado('id_inventario', $rsInventario->getCampo('id_inventario'));
                    $obErro = $obTPatrimonioInventarioHistoricoBem->exclusao();
                    $rsInventario->proximo();

                    if ($obErro->ocorreu())
                        break;
                }
            }
        }

        if (!$obErro->ocorreu()) {
            //exclui da table historico_bem
            $obTPatrimonioHistoricoBem->setDado( 'cod_bem', $inCodBem );
            $obErro = $obTPatrimonioHistoricoBem->exclusao();
        }

        if (!$obErro->ocorreu()) {
            //exclui da table bem_atributo_especie
            $obTPatrimonioBemAtributoEspecie->setDado( 'cod_bem', $inCodBem );
            $obErro = $obTPatrimonioBemAtributoEspecie->exclusao();
        }

        if (!$obErro->ocorreu()) {
            //exclui da table manutencao_paga
            $obTPatrimonioManutencaoPaga->setDado( 'cod_bem', $inCodBem );
            $obErro = $obTPatrimonioManutencaoPaga->exclusao();
        }

        if (!$obErro->ocorreu()) {
            //exclui da table manutencao
            $obTPatrimonioManutencao->setDado( 'cod_bem', $inCodBem );
            $obErro = $obTPatrimonioManutencao->exclusao();
        }

        if (!$obErro->ocorreu()) {
            $obTPatrimonioDepreciacaoReavaliacao = new TPatrimonioDepreciacaoReavaliacao;
            $obTPatrimonioDepreciacaoReavaliacao->setDado( 'cod_bem', $inCodBem );
            $obErro = $obTPatrimonioDepreciacaoReavaliacao->exclusao();
        }

        if (!$obErro->ocorreu()) {
            //exclui da tabela reavaliação
            $obTPatrimonioReavaliacao->setDado('cod_bem', $inCodBem);
            $rsReavaliacao = new RecordSet();
            $obErro = $obTPatrimonioReavaliacao->recuperaRelacionamento($rsReavaliacao);

            if (!$obErro->ocorreu()) {
                while (!$rsReavaliacao->eof()) {
                    $obTPatrimonioReavaliacao = new TPatrimonioReavaliacao();
                    $obTPatrimonioReavaliacao->setDado('cod_bem', $inCodBem);
                    $obTPatrimonioReavaliacao->setDado('cod_reavaliacao', $rsReavaliacao->getCampo('cod_reavaliacao'));
                    $obErro = $obTPatrimonioReavaliacao->exclusao();
                    $rsReavaliacao->proximo();

                    if ($obErro->ocorreu())
                        break;
                }
            }
        }

        if (!$obErro->ocorreu()) {
            //exclui da tabela depreciação
            $obTPatrimonioDepreciacao->setDado('cod_bem', $inCodBem);
            $rsDepreciacao = new RecordSet();
            $obErro = $obTPatrimonioDepreciacao->recuperaDepreciacao($rsDepreciacao);

            if (!$obErro->ocorreu()) {
                while (!$rsDepreciacao->eof()) {
                    $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao();
                    $obTPatrimonioDepreciacao->setDado('cod_bem', $inCodBem);
                    $obTPatrimonioDepreciacao->setDado('cod_depreciacao', $rsDepreciacao->getCampo('cod_depreciacao'));
                    $obErro = $obTPatrimonioDepreciacao->exclusao();
                    $rsDepreciacao->proximo();

                    if ($obErro->ocorreu())
                        break;
                }
            }
        }

        if (!$obErro->ocorreu()) {
            //exclui da tabela bem_plano_depreciacao
            $obTPatrimonioBemPlanoDepreciacao->setDado( 'cod_bem', $inCodBem );
            $obErro = $obTPatrimonioBemPlanoDepreciacao->exclusao();
        }

        if (!$obErro->ocorreu()) {
            //exclui da tabela bem_plano_conta_analica
            $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $inCodBem );
            $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio',  Sessao::getExercicio());
            $obErro = $obTPatrimonioBemPlanoAnalitica->recuperaBemPlanoAnalitica($rsBemPlanoAnalitica);

            if (!$obErro->ocorreu()) {
                while (!$rsBemPlanoAnalitica->eof()) {
                    $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica();
                    $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $inCodBem );
                    $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio',  Sessao::getExercicio());
                    $obTPatrimonioBemPlanoAnalitica->setDado( 'timestamp', $rsBemPlanoAnalitica->getCampo('timestamp'));
                    $obErro = $obTPatrimonioBemPlanoAnalitica->exclusao();
                    $rsBemPlanoAnalitica->proximo();

                    if (!$obErro->ocorreu())
                        break;
                }
            }
        }

        if (!$obErro->ocorreu()) {
            $obTTGOPatrimonioBemObra = new TTGOPatrimonioBemObra;
            $obTTGOPatrimonioBemObra->setDado('cod_bem', $_REQUEST['inCodBem']);
            $obErro = $obTTGOPatrimonioBemObra->exclusao();
        }

        if (!$obErro->ocorreu()) {
            $obTPatrimonioBemProcesso->setDado('cod_bem', $inCodBem);
            $obErro = $obTPatrimonioBemProcesso->exclusao();
        }

        if (!$obErro->ocorreu()) {
            $obTPatrimonioBem->setDado( 'cod_bem', $inCodBem );
            $obTPatrimonioBem->recuperaPorChave( $rsBem );
            if (!$obErro->ocorreu()) {
                $obErro = $obTPatrimonioBem->exclusao();
            }
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Bem: ".$inCodBem.' - '.$rsBem->getCampo('descricao'),"excluir","aviso", Sessao::getId(), "../");
        }

    break;
}

Sessao::encerraExcecao();

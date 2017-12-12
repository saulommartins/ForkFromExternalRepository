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
    * Página de Processamento de Anulação de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: PRManterLiquidacao.php 65688 2016-06-09 13:51:55Z jean $

    $Revision: 32142 $
    $Name$
    $Autor:$
    $Date: 2008-01-07 11:40:55 -0200 (Seg, 07 Jan 2008) $

    * Casos de uso: uc-02.03.24,uc-02.03.04, uc-02.03.05
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_EMP_NEGOCIO.'REmpenhoEmpenho.class.php';
include CAM_GF_EMP_NEGOCIO.'REmpenhoNotaLiquidacao.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';

SistemaLegado::BloqueiaFrames(true,true);

//Define o nome dos arquivos PHP
$stPrograma = "ManterLiquidacao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

$pgProxLiquidacao       = explode("?",$pgList);
$pgProxEmpenho          = $request->get('pgProxEmpenho');
$stAcaoEmpenho          = $request->get('stAcaoEmpenho');
$acaoEmpenho            = ($request->get('acaoEmpenho')!='') ? $request->get('acaoEmpenho'): 822;
$moduloEmpenho          = $request->get('moduloEmpenho');
$funcionalidadeEmpenho  = $request->get('funcionalidadeEmpenho');
 
$boTransacao = "";

$arRequest = $request->getAll();

if (strtolower(SistemaLegado::pegaConfiguracao( 'seta_tipo_documento_liq_tceam',30, Sessao::getExercicio(),$boTransacao))=='true') {

    if (empty($arRequest['inCodTipoDocumentoTxt'])) {
        SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }

    if (empty($arRequest['inCodTipoDocumento'])) {
        SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }

    if ($arRequest['inCodTipoDocumento'] == 6) {
        if ($arRequest['inCodTipoReciboTxt']) {
            SistemaLegado::exibeAviso('Campo Tipo Recibo inválido',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if (empty($arRequest['inCodTipoRecibo'])) {
            SistemaLegado::exibeAviso('Campo Tipo Recibo inválido!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
    }
}

//Buscar Codido UF no municipio
$inCodUF = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao);

//se for prefeitura de Tocantins
if ($inCodUF == 27) {
    if (empty($arRequest['inCodTipoDocumentoTxt'])) {
        SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }

    if (empty($arRequest['inCodTipoDocumento'])) {
        SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }

    if (empty($arRequest['inNumeroDocumento'])) {

        switch ($request->get('inCodTipoDocumento')) {
            case 1:
                $tipoDocumento = " Número da Nota Fiscal ";
                break;
            case 2:
                $tipoDocumento = " Número do Recibo ";
                break;
            case 3:
                $tipoDocumento = " Número da Diária ";
                break;
            case 4:
                $tipoDocumento = " Número da Folha de Pagamento ";
                break;
            case 5:
                $tipoDocumento = " Número do Bilhete ";
                break;
            case 6:
                $tipoDocumento = " Número da Nota Fiscal Eletronica ";
                break;
            case 7:
                $tipoDocumento = " Número do Cupom Fiscal ";
                break;
            case 9:
                $tipoDocumento = " Número do Documento ";
                break;
        }

        SistemaLegado::exibeAviso('Campo'.$tipoDocumento.' inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }
}

//se for prefeitura de Alagoas
if ($inCodUF == 02) {
    if (empty($arRequest['inCodTipoDocumentoTxt'])) {
        SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }

    if (empty($arRequest['inCodTipoDocumento'])) {
        SistemaLegado::exibeAviso('Campo Tipo Documento inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }


    if (empty($arRequest['inNumeroDocumento'])) {

        switch ($request->get('inCodTipoDocumento')) {
            case 1:
                $tipoDocumento = " Número da Nota Fiscal ";
                break;
            case 2:
                $tipoDocumento = " Número do Recibo ";
                break;
            case 3:
                $tipoDocumento = " Número da Diária ";
                break;
            case 4:
                $tipoDocumento = " Número da Folha de Pagamento ";
                break;
            case 5:
                $tipoDocumento = " Número do Bilhete ";
                break;
            case 6:
                $tipoDocumento = " Número da Nota Fiscal Eletronica ";
                break;
            case 7:
                $tipoDocumento = " Número do Cupom Fiscal ";
                break;
            case 9:
                $tipoDocumento = " Número do Documento ";
                break;
        }

        SistemaLegado::exibeAviso('Campo'.$tipoDocumento.' inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }

    if (empty($arRequest['dtDocumento'])) {
        SistemaLegado::exibeAviso('Campo Data do Documento inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }

    if ($request->get('inCodTipoDocumento') == 1 OR $request->get('inCodTipoDocumento') == 6) {
        if ($arRequest['stAutorizacao'] == "") {
            SistemaLegado::exibeAviso('Campo Autorização Nota Fiscal inválido',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }

        if (empty($arRequest['stModelo'])) {
            SistemaLegado::exibeAviso('Campo Modelo Nota Fiscal inválido',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if ($request->get('inCodTipoDocumento') == 6 && empty($arRequest['stNumXmlNFe'])) {
           SistemaLegado::exibeAviso('Campo Número da Chave de Acesso inválido!',"n_incluir","erro");
           SistemaLegado::liberaFrames(true,true);
           die;
        }
    }
}

//se for prefeitura de Minas Gerais
if ($inCodUF == 11) {
    if($request->get('stIncluirNF') == "Sim"){
        if (empty($arRequest['inCodTipoNota'])) {
            SistemaLegado::exibeAviso('Campo Tipo de Docto Fiscal inválido!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if (empty($arRequest['dtEmissaoNF'])) {
            SistemaLegado::exibeAviso('Campo Data de Emissão inválido!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if (empty($arRequest['stExercicioNF'])) {
            SistemaLegado::exibeAviso('Campo Exercício inválido!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if(($request->get('inCodTipoNota') == 1 || $request->get('inCodTipoNota') == 4) && empty($arRequest['inChave'])){
            SistemaLegado::exibeAviso('Campo Chave de Acesso inválido!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
        if($request->get('inCodTipoNota') == 2 || $request->get('inCodTipoNota') == 3){
            if ($arRequest['inNumeroNF'] == "") {
                SistemaLegado::exibeAviso('Campo Número do Docto Fiscal inválido!',"n_incluir","erro");
                SistemaLegado::liberaFrames(true,true);
                die;
            }
            if (empty($arRequest['inNumSerie'])) {
                SistemaLegado::exibeAviso('Campo Série do Docto Fiscal inválido!',"n_incluir","erro");
                SistemaLegado::liberaFrames(true,true);
                die;
            }
            if ($request->get('inCodTipoNota') == 2 && empty($arRequest['inChaveMunicipal'])) {
                SistemaLegado::exibeAviso('Campo Chave de Acesso Municipal inválido!',"n_incluir","erro");
                SistemaLegado::liberaFrames(true,true);
                die;
            }
        }elseif (!is_numeric($arRequest['inChave'])) {
            SistemaLegado::exibeAviso('Campo Chave de Acesso Estadual inválido, digite somente números!',"n_incluir","erro");
            SistemaLegado::liberaFrames(true,true);
            die;
        }
    }
}

// se a prefeitura for Pernambuco
if ($inCodUF == 16) {
    if (empty($arRequest['inCodTipoDocumentoTxt'])) {
        SistemaLegado::exibeAviso('Campo Tipo de Documento inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }

    if (empty($arRequest['inCodTipoDocumento'])) {
        SistemaLegado::exibeAviso('Campo Tipo de Documento inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }
    
    if (empty($arRequest['inNumeroDocumento'])) {
        if ($request->get('inCodTipoDocumento') == 1)
            $tipoDocumento = "Número Nota Fiscal ";
        elseif ($request->get('inCodTipoDocumento') == 9)
            $tipoDocumento = " Número do Documento ";

        SistemaLegado::exibeAviso('Campo'.$tipoDocumento.' inválido!',"n_incluir","erro");
        SistemaLegado::liberaFrames(true,true);
        die;
    }
}

include( $pgJS );

$obREmpenhoEmpenho        = new REmpenhoEmpenho;
$obREmpenhoNotaLiquidacao = new REmpenhoNotaLiquidacao( $obREmpenhoEmpenho );

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_Liq" );
$obAtributos->recuperaVetor( $arChave );

$stAcao = $request->get('stAcao');

//Trecho de código do filtro
$stFiltro = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            $stFiltro .= "&".$stCampo."=".urlencode( implode(',', $stValor) );
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

//valida a utilização da rotina de encerramento do mês contábil
$arDtAutorizacao = explode('/', $request->get('stDtLiquidacao'));
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9, '', $boTransacao );
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ', $boTransacao);

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::LiberaFrames(true,true);
    SistemaLegado::exibeAviso(urlencode("Mês da Liquidação encerrado!"),"n_incluir","erro");
    exit;
}

switch ($stAcao) {
    case "liquidar":
        $obErro = new Erro;
        $obTransacao = new Transacao();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ( !$obErro->ocorreu() ) {
            //ATRIBUTOS
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obREmpenhoNotaLiquidacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }

            if ( sistemaLegado::comparaDatas($_REQUEST['stDtLiquidacao'], '31/12/'.Sessao::getExercicio()) ) {
                $obErro->setDescricao("Campo Data de Liquidação deve ser menor que '31/12/".Sessao::getExercicio().".");
            }
    
            if (SistemaLegado::comparaDatas($_REQUEST["stDtLiquidacao"], date('d/m/Y'))) {
                $obErro->setDescricao("Campo Data de Liquidação deve ser menor ou igual a data de hoje.");
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obREmpenhoEmpenho->setExercicio                            ( $request->get("dtExercicioEmpenho")   );
            $obREmpenhoEmpenho->setCodEmpenho                           ( $request->get("inCodEmpenho")         );
            $obREmpenhoEmpenho->setCodPreEmpenho                        ( $request->get("inCodPreEmpenho")      );
            $obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa      ( $request->get('inCodDespesa')         );
            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade ( $request->get("inCodEntidade")        );
            $obErro = $obREmpenhoEmpenho->consultar( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                $obREmpenhoNotaLiquidacao->setExercicio             ( Sessao::getExercicio()                        );
                $obREmpenhoNotaLiquidacao->setExercRP               ( Sessao::getExercicio()                        );
                $obREmpenhoNotaLiquidacao->setDtLiquidacao          ( $request->get("stDtLiquidacao")                  );
                $obREmpenhoNotaLiquidacao->setDtVencimento          ( $request->get("dtValidadeFinal")                  );
                $obREmpenhoNotaLiquidacao->setObservacao            ( substr( $request->get("stObservacao"), 0 , 400 )  );
                $obREmpenhoNotaLiquidacao->setCodContaContabilFinanc( $request->get("inCodContaContabilFinanc")         );

                // Definição das contas credito e debito
                $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaDebito->setExercicio ( Sessao::getExercicio() );
                if($request->get("inCodContaDebito"))
                    $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaDebito->setCodPlano ( $request->get("inCodContaDebito") );

                $obErro = $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaDebito->consultar( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaCredito->setExercicio ( Sessao::getExercicio() );
                    if($request->get("inCodContaCredito"))
                        $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaCredito->setCodPlano ( $request->get("inCodContaCredito") );
                    $obErro = $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaCredito->consultar( $boTransacao );
                }

                if($request->get("inCodHistoricoPatrimon"))
                    $obREmpenhoNotaLiquidacao->setCodHistorico( $request->get("inCodHistoricoPatrimon") );

                if($request->get("stComplemento"))
                    $obREmpenhoNotaLiquidacao->setComplemento( $request->get("stComplemento") );

                $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $request->get('inNumOrgao') );

                foreach ($request->getAll() as $stChave => $stValor) {
                    if ( strstr( $stChave, "nuValor" ) ) {
                        $arCampoItem = explode( "_", $stChave );
                        $arItemPreEmpenho[$arCampoItem[1]] = $stValor;
                    }
                }

                if (isset($arItemPreEmpenho)) {
                    for ( $inContItens = 0; $inContItens <  count( $obREmpenhoEmpenho->arItemPreEmpenho ); $inContItens++ ) {
                        $obREmpenhoEmpenho->arItemPreEmpenho[$inContItens]->setValorALiquidar( $arItemPreEmpenho[$obREmpenhoEmpenho->arItemPreEmpenho[$inContItens]->getNumItem()] );
                    }
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obREmpenhoNotaLiquidacao->incluir( $boTransacao );
        }

        if (!$obErro->ocorreu()) {
            $obAdministracaoConfiguracao = new TAdministracaoConfiguracao;
            $obErro = $obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE configuracao.parametro = 'seta_tipo_documento_liq_tceam' AND exercicio = '".Sessao::getExercicio()."'", "", $boTransacao);
            $boIncluirDocumento = $rsAdministracaoConfiguracao->getCampo('valor');

            //se for prefeitura de Rio Grande do Sul, inclui as informações da nota fiscal
            if ($inCodUF == 23 && !$obErro->ocorreu()) {
                if ($request->get('stIncluirNF') == 'Sim') {
                    include_once CAM_GPC_TCERS_MAPEAMENTO.'TTCERSNotaFiscal.class.php';
                    $obTTCERSNotaFiscal = new TTCERSNotaFiscal;
                    $obTTCERSNotaFiscal->setDado('cod_nota'     , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCERSNotaFiscal->setDado('cod_entidade' , $request->get('inCodEntidade'));
                    $obTTCERSNotaFiscal->setDado('exercicio'    , Sessao::getExercicio());
                    $obTTCERSNotaFiscal->setDado('nro_nota'     , $request->get('inNumeroNF',''));
                    $obTTCERSNotaFiscal->setDado('nro_serie'    , $request->get('inNumSerie',''));
                    $obTTCERSNotaFiscal->setDado('data_emissao' , $request->get('dtEmissaoNF',''));
                    $obErro = $obTTCERSNotaFiscal->inclusao( $boTransacao );
                }
            }

            //se for prefeitura do Rio Grande do Norte, inclui as informações da nota fiscal
            if ( $inCodUF == 20 && !$obErro->ocorreu() ) {
                if ($request->get('stNumeroNF') || $request->get('stSerieNF') || $request->get('stCodValidacaoNF') || $request->get('stCodValidacaoNF') || $request->get('stModeloNF')) {
                    include_once CAM_GPC_TCERN_MAPEAMENTO.'TTCERNNotaFiscal.class.php';
                    $obTTCERNNotaFiscal = new TTCERNNotaFiscal;
                    $obTTCERNNotaFiscal->setDado('cod_nota_liquidacao', $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCERNNotaFiscal->setDado('cod_entidade'       , $_REQUEST['inCodEntidade']);
                    $obTTCERNNotaFiscal->setDado('exercicio'          , Sessao::getExercicio());
                    $obTTCERNNotaFiscal->setDado('nro_nota'           , $request->get('stNumeroNF',''));
                    $obTTCERNNotaFiscal->setDado('nro_serie'          , $request->get('stSerieNF',''));
                    $obTTCERNNotaFiscal->setDado('data_emissao'       , $request->get('stDtEmissaoNF',''));
                    $obTTCERNNotaFiscal->setDado('cod_validacao'      , $request->get('stCodValidacaoNF',''));
                    $obTTCERNNotaFiscal->setDado('modelo'             , $request->get('stModeloNF',''));
                    $obErro = $obTTCERNNotaFiscal->inclusao( $boTransacao );
                }
            }

             //se for prefeitura de Alagoas, inclui as informações de documento
            if ( $inCodUF == 02 && !$obErro->ocorreu() ) { 
                include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALDocumento.class.php';
                $obTTCEALDocumento = new TTCEALDocumento;

                if ( $request->get('inCodTipoDocumento') == 1 OR $request->get('inCodTipoDocumento') == 6) {
                    $obTTCEALDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                    $obTTCEALDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEALDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                    $obTTCEALDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEALDocumento->setDado('nro_documento'  , $request->get('inNumeroDocumento'));
                    $obTTCEALDocumento->setDado('dt_documento'   , $request->get('dtDocumento'));
                    $obTTCEALDocumento->setDado('descricao'      , $request->get('stDescricao'));
                    $obTTCEALDocumento->setDado('autorizacao'    , $request->get('stAutorizacao'));
                    $obTTCEALDocumento->setDado('modelo'         , $request->get('stModelo'));
                    if($request->get('inCodTipoDocumento') == 6)
                        $obTTCEALDocumento->setDado('nro_xml_nfe', $request->get('stNumXmlNFe'));
                    $obErro = $obTTCEALDocumento->inclusao( $boTransacao );
                }

                if ( $request->get('inCodTipoDocumento') == 7 && !$obErro->ocorreu() ) {
                    $obTTCEALDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                    $obTTCEALDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEALDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                    $obTTCEALDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEALDocumento->setDado('nro_documento'  , $request->get('inNumeroDocumento'));
                    $obTTCEALDocumento->setDado('dt_documento'   , $request->get('dtDocumento'));
                    $obTTCEALDocumento->setDado('descricao'      , $request->get('stDescricao'));
                    if($arRequest['stAutorizacao'] != ""){
                        $obTTCEALDocumento->setDado('autorizacao'    , $request->get('stAutorizacao'));
                    }
                    if($arRequest['stModelo'] != ""){
                        $obTTCEALDocumento->setDado('modelo'         , $request->get('stModelo'));
                    }
                    $obErro = $obTTCEALDocumento->inclusao( $boTransacao );
                }

                if ( ($request->get('inCodTipoDocumento') == 2 OR $request->get('inCodTipoDocumento') == 3 OR $request->get('inCodTipoDocumento') == 4 OR $request->get('inCodTipoDocumento') == 5) && !$obErro->ocorreu() ) {
                    $obTTCEALDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                    $obTTCEALDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEALDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                    $obTTCEALDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEALDocumento->setDado('nro_documento'  , $request->get('inNumeroDocumento'));
                    $obTTCEALDocumento->setDado('dt_documento'   , $request->get('dtDocumento'));
                    $obTTCEALDocumento->setDado('descricao'      , $request->get('stDescricao'));
                    $obErro = $obTTCEALDocumento->inclusao( $boTransacao );
                }

                if ( $request->get('inCodTipoDocumento') == 9 && !$obErro->ocorreu() ) {
                    $obTTCEALDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                    $obTTCEALDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEALDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                    $obTTCEALDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEALDocumento->setDado('nro_documento'  , $request->get('inNumeroDocumento'));
                    $obTTCEALDocumento->setDado('dt_documento'   , $request->get('dtDocumento'));
                    $obTTCEALDocumento->setDado('descricao'      , $request->get('stDescricao'));
                    if(!$arRequest['stAutorizacao'] == ""){
                        $obTTCEALDocumento->setDado('autorizacao'    , $request->get('stAutorizacao'));
                    }
                    if(!$arRequest['stModelo'] == ""){
                        $obTTCEALDocumento->setDado('modelo'         , $request->get('stModelo'));
                    }
                    $obErro = $obTTCEALDocumento->inclusao( $boTransacao );
                }
            }

             //se for prefeitura de Tocantins, inclui as informações de documento
            if ( $inCodUF == 27 && !$obErro->ocorreu() ) {
                include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETONotaLiquidacaoDocumento.class.php';
                $obTTCETONotaLiquidacaoDocumento = new TTCETONotaLiquidacaoDocumento;

                if ( ($request->get('inCodTipoDocumento') == 1 OR $request->get('inCodTipoDocumento') == 6) && !$obErro->ocorreu() ) {
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCETONotaLiquidacaoDocumento->setDado('nro_documento'  , $request->get('inNumeroDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('dt_documento'   , $request->get('dtDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('descricao'      , $request->get('stDescricao'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('autorizacao'    , $request->get('stAutorizacao'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('modelo'         , $request->get('stModelo'));
                    if($request->get('inCodTipoDocumento') == 6)
                        $obTTCETONotaLiquidacaoDocumento->setDado('nro_xml_nfe', $request->get('stNumXmlNFe'));
                    $obErro = $obTTCETONotaLiquidacaoDocumento->inclusao( $boTransacao );
                }

                if ( $request->get('inCodTipoDocumento') == 7 && !$obErro->ocorreu() ) {
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCETONotaLiquidacaoDocumento->setDado('nro_documento'  , $request->get('inNumeroDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('dt_documento'   , $request->get('dtDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('descricao'      , $request->get('stDescricao'));
                    if($arRequest['stAutorizacao'] != ""){
                        $obTTCETONotaLiquidacaoDocumento->setDado('autorizacao'    , $request->get('stAutorizacao'));
                    }
                    if($arRequest['stModelo'] != ""){
                        $obTTCETONotaLiquidacaoDocumento->setDado('modelo'         , $request->get('stModelo'));
                    }
                    $obErro = $obTTCETONotaLiquidacaoDocumento->inclusao( $boTransacao );
                }

                if ( ($request->get('inCodTipoDocumento') == 2 OR $request->get('inCodTipoDocumento') == 3 OR $request->get('inCodTipoDocumento') == 4 OR $request->get('inCodTipoDocumento') == 5) && !$obErro->ocorreu() ) {
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCETONotaLiquidacaoDocumento->setDado('nro_documento'  , $request->get('inNumeroDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('dt_documento'   , $request->get('dtDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('descricao'      , $request->get('stDescricao'));
                    $obErro = $obTTCETONotaLiquidacaoDocumento->inclusao( $boTransacao );
                }

                if ( $request->get('inCodTipoDocumento') == 9 && !$obErro->ocorreu() ) {
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCETONotaLiquidacaoDocumento->setDado('nro_documento'  , $request->get('inNumeroDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('dt_documento'   , $request->get('dtDocumento'));
                    $obTTCETONotaLiquidacaoDocumento->setDado('descricao'      , $request->get('stDescricao'));
                     if($arRequest['stAutorizacao'] != ""){
                        $obTTCETONotaLiquidacaoDocumento->setDado('autorizacao'    , $request->get('stAutorizacao'));
                    }
                    if($arRequest['stModelo'] != ""){
                        $obTTCETONotaLiquidacaoDocumento->setDado('modelo'         , $request->get('stModelo'));
                    }
                    $obErro = $obTTCETONotaLiquidacaoDocumento->inclusao( $boTransacao );
                }
            }

            // Se for prefeitura do amazonas, será incluído o tipo de documento
            if ( $boIncluirDocumento == 'true' && !$obErro->ocorreu() ) {
                if ($request->get('inCodTipoDocumento') != "") {
                    include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMDocumento.class.php';
                    $obTTCEAMDocumento = new TTCEAMDocumento;
                    $obErro =  $obTTCEAMDocumento->proximoCod($inCodDocumento, $boTransacao);

                    if (!$obErro->ocorreu()) {
                        $obTTCEAMDocumento->setDado('cod_documento'  , $inCodDocumento);
                        $obTTCEAMDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                        $obTTCEAMDocumento->setDado('exercicio'      , Sessao::getExercicio());
                        $obTTCEAMDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                        $obTTCEAMDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                        $obTTCEAMDocumento->setDado('vl_comprometido', $request->get('nuValorComprometido'));
                        $obTTCEAMDocumento->setDado('vl_total'       , $request->get('nuValorTotal'));
                        $obErro = $obTTCEAMDocumento->inclusao( $boTransacao );
                    }

                    if ( $request->get('inCodTipoDocumento') == 1 && !$obErro->ocorreu() ) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoBilhete.class.php';
                        $obTTCEAMTipoDocumentoBilhete = new TTCEAMTipoDocumentoBilhete;
                        $obErro =  $obTTCEAMTipoDocumentoBilhete->proximoCod($inCodTipoDocumentoBilhete, $boTransacao);
                        
                        if (!$obErro->ocorreu()) {
                            $obTTCEAMTipoDocumentoBilhete->setDado('cod_tipo_documento_bilhete', $inCodTipoDocumentoBilhete);
                            $obTTCEAMTipoDocumentoBilhete->setDado('cod_documento'             , $inCodDocumento);
                            $obTTCEAMTipoDocumentoBilhete->setDado('numero'                    , $request->get('stNumero'));
                            $obTTCEAMTipoDocumentoBilhete->setDado('dt_emissao'                , $request->get('dtEmissao'));
                            $obTTCEAMTipoDocumentoBilhete->setDado('dt_saida'                  , $request->get('dtSaida'));
                            $obTTCEAMTipoDocumentoBilhete->setDado('hora_saida'                , $request->get('hrSaida'));
                            $obTTCEAMTipoDocumentoBilhete->setDado('destino'                   , $request->get('stDestino'));
                            $obTTCEAMTipoDocumentoBilhete->setDado('dt_chegada'                , $request->get('dtChegada'));
                            $obTTCEAMTipoDocumentoBilhete->setDado('hora_chegada'              , $request->get('hrChegada'));
                            $obTTCEAMTipoDocumentoBilhete->setDado('motivo'                    , $request->get('stMotivo'));
                            $obErro = $obTTCEAMTipoDocumentoBilhete->inclusao( $boTransacao );
                        }

                    } elseif ( $request->get('inCodTipoDocumento') == 2 && !$obErro->ocorreu() ) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoDiaria.class.php';
                        $obTTCEAMTipoDocumentoDiaria = new TTCEAMTipoDocumentoDiaria;
                        $obErro =  $obTTCEAMTipoDocumentoDiaria->proximoCod($inCodTipoDocumentoDiaria, $boTransacao);
                        
                        if (!$obErro->ocorreu()) {
                            $obTTCEAMTipoDocumentoDiaria->setDado('cod_tipo_documento_diaria', $inCodTipoDocumentoDiaria);
                            $obTTCEAMTipoDocumentoDiaria->setDado('cod_documento'            , $inCodDocumento);
                            $obTTCEAMTipoDocumentoDiaria->setDado('funcionario'              , $request->get('stFuncionario'));
                            $obTTCEAMTipoDocumentoDiaria->setDado('matricula'                , $request->get('stMatricula'));
                            $obTTCEAMTipoDocumentoDiaria->setDado('dt_saida'                 , $request->get('dtSaida'));
                            $obTTCEAMTipoDocumentoDiaria->setDado('hora_saida'               , $request->get('hrSaida'));
                            $obTTCEAMTipoDocumentoDiaria->setDado('destino'                  , $request->get('stDestino'));
                            $obTTCEAMTipoDocumentoDiaria->setDado('dt_retorno'               , $request->get('dtRetorno'));
                            $obTTCEAMTipoDocumentoDiaria->setDado('hora_retorno'             , $request->get('hrRetorno'));
                            $obTTCEAMTipoDocumentoDiaria->setDado('motivo'                   , $request->get('stMotivo'));
                            $obErro = $obTTCEAMTipoDocumentoDiaria->inclusao( $boTransacao );
                        }

                    } elseif ( $request->get('inCodTipoDocumento') == 3 && !$obErro->ocorreu() ) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoDiverso.class.php';
                        $obTTCEAMTipoDocumentoDiverso = new TTCEAMTipoDocumentoDiverso;
                        $obErro =  $obTTCEAMTipoDocumentoDiverso->proximoCod($inCodTipoDocumentoDiverso, $boTransacao);

                        if (!$obErro->ocorreu()) {
                            $obTTCEAMTipoDocumentoDiverso->setDado('cod_tipo_documento_diverso', $inCodTipoDocumentoDiverso);
                            $obTTCEAMTipoDocumentoDiverso->setDado('cod_documento'             , $inCodDocumento);
                            $obTTCEAMTipoDocumentoDiverso->setDado('numero'                    , $request->get('stNumero'));
                            $obTTCEAMTipoDocumentoDiverso->setDado('data'                      , $request->get('dtDiverso'));
                            $obTTCEAMTipoDocumentoDiverso->setDado('descricao'                 , $request->get('stDescricao'));
                            $obTTCEAMTipoDocumentoDiverso->setDado('nome_documento'            , $request->get('stNomeDocumento'));
                            $obErro = $obTTCEAMTipoDocumentoDiverso->inclusao( $boTransacao );
                        }

                    } elseif ( $request->get('inCodTipoDocumento') == 4 && !$obErro->ocorreu() ) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoFolha.class.php';
                        $obTTCEAMTipoDocumentoFolha = new TTCEAMTipoDocumentoFolha;
                        $obErro =  $obTTCEAMTipoDocumentoFolha->proximoCod($inCodTipoDocumentoFolha, $boTransacao);

                        if (!$obErro->ocorreu()) {
                            $obTTCEAMTipoDocumentoFolha->setDado('cod_tipo_documento_folha', $inCodTipoDocumentoFolha);
                            $obTTCEAMTipoDocumentoFolha->setDado('cod_documento'           , $inCodDocumento);
                            $obTTCEAMTipoDocumentoFolha->setDado('mes'                     , $request->get('inMes'));
                            $obTTCEAMTipoDocumentoFolha->setDado('exercicio'               , $request->get('stExercicio'));
                            $obErro = $obTTCEAMTipoDocumentoFolha->inclusao( $boTransacao );
                        }

                    } elseif ( $request->get('inCodTipoDocumento') == 5 && !$obErro->ocorreu() ) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoNota.class.php';
                        $obTTCEAMTipoDocumentoNota = new TTCEAMTipoDocumentoNota;
                        $obErro =  $obTTCEAMTipoDocumentoNota->proximoCod($inCodTipoDocumentoNota, $boTransacao);
                        
                        if (!$obErro->ocorreu()) {
                            $obTTCEAMTipoDocumentoNota->setDado('cod_tipo_documento_nota', $inCodTipoDocumentoNota);
                            $obTTCEAMTipoDocumentoNota->setDado('cod_documento'          , $inCodDocumento);
                            $obTTCEAMTipoDocumentoNota->setDado('numero_nota_fiscal'     , $request->get('stNumeroNotaFiscal'));
                            $obTTCEAMTipoDocumentoNota->setDado('numero_serie'           , $request->get('stNumeroSerie'));
                            $obTTCEAMTipoDocumentoNota->setDado('numero_subserie'        , $request->get('stNumeroSubserie'));
                            $obTTCEAMTipoDocumentoNota->setDado('data'                   , $request->get('dtNota'));
                            $obErro = $obTTCEAMTipoDocumentoNota->inclusao( $boTransacao );
                        }

                    } elseif ( $request->get('inCodTipoDocumento') == 6 && !$obErro->ocorreu() ) {
                        include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMTipoDocumentoRecibo.class.php';
                        $obTTCEAMTipoDocumentoRecibo = new TTCEAMTipoDocumentoRecibo;
                        $obErro =  $obTTCEAMTipoDocumentoRecibo->proximoCod($inCodTipoDocumentoRecibo, $boTransacao);

                        if (!$obErro->ocorreu()) {
                            $obTTCEAMTipoDocumentoRecibo->setDado('cod_tipo_documento_recibo', $inCodTipoDocumentoRecibo);
                            $obTTCEAMTipoDocumentoRecibo->setDado('cod_documento'            , $inCodDocumento);
                            $obTTCEAMTipoDocumentoRecibo->setDado('cod_tipo_recibo'          , $request->get('inCodTipoRecibo'));
                            $obTTCEAMTipoDocumentoRecibo->setDado('numero'                   , $request->get('stNumero'));
                            $obTTCEAMTipoDocumentoRecibo->setDado('valor'                    , $request->get('nuValor'));
                            $obTTCEAMTipoDocumentoRecibo->setDado('data'                     , $request->get('dtRecibo'));
                            $obErro =  $obTTCEAMTipoDocumentoRecibo->inclusao( $boTransacao );
                        }

                    }
                }
            }

             //se for prefeitura de Pernambuco, inclui as informações de documento
            if ( $inCodUF == 16 && !$obErro->ocorreu() ) {
                include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEDocumento.class.php';
                $obTTCEPEDocumento = new TTCEPEDocumento;

                if ( $request->get('inCodTipoDocumento') == 1 && !$obErro->ocorreu() ) {
                    $obTTCEPEDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                    $obTTCEPEDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEPEDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                    $obTTCEPEDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEPEDocumento->setDado('nro_documento'  , $request->get('inNumeroDocumento'));
                    $obTTCEPEDocumento->setDado('serie'          , $request->get('inSerieDocumento'));
                    $obTTCEPEDocumento->setDado('cod_uf'         , $request->get('stUfDocumento'));
                    $obErro = $obTTCEPEDocumento->inclusao( $boTransacao );
                }

                if ( $request->get('inCodTipoDocumento') == 9 && !$obErro->ocorreu() ) {
                    $obTTCEPEDocumento->setDado('cod_tipo'       , $request->get('inCodTipoDocumento'));
                    $obTTCEPEDocumento->setDado('exercicio'      , Sessao::getExercicio());
                    $obTTCEPEDocumento->setDado('cod_entidade'   , $request->get('inCodEntidade'));
                    $obTTCEPEDocumento->setDado('cod_nota'       , $obREmpenhoNotaLiquidacao->getCodNota());
                    $obTTCEPEDocumento->setDado('nro_documento'  , $request->get('inNumeroDocumento'));
                    $obTTCEPEDocumento->setDado('serie'          , $request->get('inSerieDocumento'));
                    $obTTCEPEDocumento->setDado('cod_uf'         , $request->get('stUfDocumento'));
                    $obErro = $obTTCEPEDocumento->inclusao( $boTransacao );
                }
            }

            //se for prefeitura de Minas Gerais, inclui as informações de documento fiscal
            if ( $inCodUF == 11 && $request->get('stIncluirNF') == "Sim" && !$obErro->ocorreu() ) {
                include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscal.class.php";
                include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscalEmpenhoLiquidacao.class.php";

                $obTTCEMGNotaFiscal = new TTCEMGNotaFiscal;
                $obTTCEMGNotaFiscal->setDado( 'exercicio'                       , $request->get('stExercicioNF')            );
                $obErro = $obTTCEMGNotaFiscal->proximoCod($inCodNota, $boTransacao);

                if (!$obErro->ocorreu()) {
                    $obTTCEMGNotaFiscal->setDado( 'cod_nota'                    , $inCodNota                            );
                    $obTTCEMGNotaFiscal->setDado( 'cod_entidade'                , $request->get('inCodEntidade')            );
                    $obTTCEMGNotaFiscal->setDado( 'data_emissao'                , $request->get('dtEmissaoNF')              );
                    $obTTCEMGNotaFiscal->setDado( 'cod_tipo'                    , $request->get('inCodTipoNota')            );

                    if ($request->get('inNumeroNF') != '') {
                        $obTTCEMGNotaFiscal->setDado('nro_nota'                 , $request->get('inNumeroNF')               );
                    }

                    if ($request->get('inNumSerie') != '') {
                        $obTTCEMGNotaFiscal->setDado('nro_serie'                , $request->get('inNumSerie')               );
                    }

                    if ($request->get('stAIFD') != '') {
                        $obTTCEMGNotaFiscal->setDado('aidf'                     , $request->get('stAIFD')                   );
                    }

                    if ($request->get('inNumInscricaoMunicipal') != '') {
                        $obTTCEMGNotaFiscal->setDado('inscricao_municipal'      , $request->get('inNumInscricaoMunicipal')  );
                    }

                    if ($request->get('inNumInscricaoEstadual') != '') {
                        $obTTCEMGNotaFiscal->setDado('inscricao_estadual'       , $request->get('inNumInscricaoEstadual')   );
                    }

                    if ($request->get('inChave')) {
                        $obTTCEMGNotaFiscal->setDado ( 'chave_acesso'           , $request->get('inChave')                  );
                    }

                    if ($request->get('inChaveMunicipal')) {
                        $obTTCEMGNotaFiscal->setDado ( 'chave_acesso_municipal' , $request->get('inChaveMunicipal')         );
                    }
    
                    $nuVlTotalDoctoFiscal = str_replace('.', '' , $request->get('nuTotalNf'));
                    $nuVlTotalDoctoFiscal = str_replace(',', '.', $nuVlTotalDoctoFiscal);

                    $nuVlDescontoDoctoFiscal = str_replace('.', '' , $request->get('nuVlDesconto'));
                    $nuVlDescontoDoctoFiscal = str_replace(',', '.', $nuVlDescontoDoctoFiscal);

                    $obTTCEMGNotaFiscal->setDado( 'vl_total'        , (float)$nuVlTotalDoctoFiscal);
                    $obTTCEMGNotaFiscal->setDado( 'vl_desconto'     , (float)$nuVlDescontoDoctoFiscal);
                    $obTTCEMGNotaFiscal->setDado( 'vl_total_liquido', (float)$nuVlTotalDoctoFiscal - (float)$nuVlDescontoDoctoFiscal );

                    $obErro = $obTTCEMGNotaFiscal->inclusao( $boTransacao );

                    if (!$obErro->ocorreu()) {
                        $obTTCEMGNotaFiscalEmpenho = new TTCEMGNotaFiscalEmpenhoLiquidacao;

                        $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_nota'             , $inCodNota                              );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio'            , $request->get('stExercicioNF')          );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_entidade'         , $request->get('inCodEntidade')          );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_empenho'          , $request->get('inCodEmpenho')           );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio_empenho'    , $request->get('dtExercicioEmpenho')     );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'cod_nota_liquidacao'  , $obREmpenhoNotaLiquidacao->getCodNota() );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'exercicio_liquidacao' , Sessao::getExercicio()                  );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'vl_associado'         , $nuVlTotalDoctoFiscal                   );
                        $obTTCEMGNotaFiscalEmpenho->setDado( 'vl_liquidacao'        , $nuVlTotalDoctoFiscal                   );

                        $obErro = $obTTCEMGNotaFiscalEmpenho->inclusao($boTransacao);
                    }
                }
            }
        }
        
        //se for prefeitura de Bahia, inclui as informações de documento fiscal
        if ( $inCodUF == 5 && !$obErro->ocorreu() ) {
            include_once(CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTCMBANotaFiscalLiquidacao.class.php");
            $obTTCMBANotaFiscalLiquidacao = new TTCMBANotaFiscalLiquidacao();

            $obTTCMBANotaFiscalLiquidacao->setDado('cod_nota_liquidacao'  , $obREmpenhoNotaLiquidacao->getCodNota() );
            $obTTCMBANotaFiscalLiquidacao->setDado('exercicio_liquidacao' , $obREmpenhoNotaLiquidacao->getExercicio() );
            $obTTCMBANotaFiscalLiquidacao->setDado('cod_entidade'         , $request->get('inCodEntidade') );

            if ( $request->get('stAnoNotaFiscal') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('ano' , $request->get('stAnoNotaFiscal') );                
            }
            if ( $request->get('stNumeroNF') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('nro_nota' , $request->get('stNumeroNF') );                
            }
            if ( $request->get('stSerieNF') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('nro_serie', $request->get('stSerieNF') );                
            }
            if ( $request->get('stSubSerieNF') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('nro_subserie', $request->get('stSubSerieNF') );                
            }
            if ( $request->get('stDtEmissaoNF') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('data_emissao', $request->get('stDtEmissaoNF') );                
            }
            if ( $request->get('nuValorNotaFiscal') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('vl_nota', $request->get('nuValorNotaFiscal') );                
            }
            if ( $request->get('stObjetoNF') ) {
                $obTTCMBANotaFiscalLiquidacao->setDado('descricao', $request->get('stObjetoNF') );                
            }
            if ( $request->get('stUFUnidadeFederacao') ) {
                $inCodUF = SistemaLegado::pegaDado("cod_uf","sw_uf"," WHERE sigla_uf = '".$request->get('stUFUnidadeFederacao')."'", $boTransacao);
                $obTTCMBANotaFiscalLiquidacao->setDado('cod_uf', $inCodUF );                
            }

            $obErro = $obTTCMBANotaFiscalLiquidacao->inclusao($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            /* Salvar assinaturas configuráveis se houverem */
            $arAssinaturas = Sessao::read('assinaturas');
            if ( isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0 ) {
                include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoAssinatura.class.php" );
                $arAssinatura = $arAssinaturas['selecionadas'];
                $obTEmpenhoNotaLiquidacaoAssinatura = new TEmpenhoNotaLiquidacaoAssinatura;
                $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'exercicio', Sessao::getExercicio() );
                $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'cod_entidade', $request->get('inCodEntidade') );
                $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'cod_nota', $obREmpenhoNotaLiquidacao->getCodNota() );

                $boInserir = 'true';
                $inCount = 0;
                $arAssinaInseridos = array();
                $arAssinaturaTemp = array_reverse($arAssinatura);
                foreach ($arAssinaturaTemp as $arAssina) {

                    foreach ($arAssinaInseridos as $inTemp => $inCGMTemp) {
                        if ($arAssina['inCGM'] != $inCGMTemp) {
                            $boInserir = 'true';
                        } else {
                            $boInserir = 'false';
                            break;
                        }
                    }
                    if ($boInserir == 'true') {
                        $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'num_assinatura', 0 );
                        $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'numcgm', $arAssina['inCGM'] );
                        $obTEmpenhoNotaLiquidacaoAssinatura->setDado( 'cargo', $arAssina['stCargo'] );
                        $obErro = $obTEmpenhoNotaLiquidacaoAssinatura->inclusao( $boTransacao );
                        $arAssinaInseridos[$inCount] = $arAssina['inCGM'];

                        if($obErro->ocorreu())
                            break;
                    }
                    $inCount++;
                }
                unset($obTEmpenhoNotaLiquidacaoAssinatura);
            }
        }

        if ( !$obErro->ocorreu() ) {
            if ($request->get('stEmitirEmpenho')) {
                if ($request->get('boEmitirOP') == "S") {
                    $pgProx = CAM_GF_EMP_INSTANCIAS."ordemPagamento/FMManterOrdemPagamento.php";
                    $stFiltroEmissaoOP  = "&acao=816&modulo=10&funcionalidade=203";
                    $stFiltroEmissaoOP .= "&inCodEntidade=".$request->get("inCodEntidade")."&inCodEmpenho=".$request->get("inCodEmpenho");
                    $stFiltroEmissaoOP .= "&dtExercicioEmpenho=".$request->get("dtExercicioEmpenho");
                    $stFiltroEmissaoOP .= "&inCodNota=".$obREmpenhoNotaLiquidacao->getCodNota()."&stExercicioNota=".Sessao::getExercicio();
                    $stFiltroEmissaoOP .= "&stLiq=1";
                    $stFiltroEmissaoOP .= "&dtDataVencimento=".$request->get('dtValidadeFinal');
                    $stFiltroEmissaoOP .= "&stEmitirEmpenho=S";
                    $stFiltroEmissaoOP .= "&pgProxEmpenho=".$pgProxEmpenho."&stAcaoEmpenho=".$stAcaoEmpenho;
                    $stFiltroEmissaoOP .= "&stAcaoLiquidacao=liquidar";
                    $stFiltroEmissaoOP .= "&pgProxLiquidacao=".$pgProxLiquidacao[0];
                    $stFiltroEmissaoOP .= "&acaoEmpenho=".$acaoEmpenho."&moduloEmpenho=".$moduloEmpenho."&funcionalidadeEmpenho=".$funcionalidadeEmpenho;
                    $stFiltroEmissaoOP .= "&acaoLiquidacao=812&moduloLiquidacao=10&funcionalidadeLiquidacao=202";
                    $stFiltroEmissaoOP .= "&pgDespesasFixas=".$request->get('pgDespesasFixas');
                    print '<script type="text/javascript">
                                mudaMenu         ( "Ordem de Pagamento","203" );
                           </script>';

                    SistemaLegado::alertaAviso($pgProx.'?'.Sessao::getId()."&stAcao=incluir".$stFiltroEmissaoOP,"Liquidar Empenho concluído com sucesso!Nota n.(".$obREmpenhoNotaLiquidacao->getCodNota()."/".Sessao::getExercicio().")","", "aviso",Sessao::getId(), "../");
                } else {
                    print '<script type="text/javascript">
                                mudaMenu         ( "Empenho","82" );
                           </script>';
                    $stFiltro  = "&acao=".$acaoEmpenho."&cod_gestao_pass=2&stNomeGestao=Financeira&modulos=Empenho&modulo=".$moduloEmpenho."&funcionalidade=".$funcionalidadeEmpenho."&nivel=1&acaoLiquidar=812";

                    if ($request->get('pgDespesasFixas') != "") {
                        $pgProx = CAM_GF_EMP_INSTANCIAS."empenho/FMManterDespesasMensaisFixas.php";
                    } else {
                        $pgProx = $pgProxEmpenho;
                    }

                    SistemaLegado::alertaAviso($pgProx.'?'."stAcao=".$stAcaoEmpenho.$stFiltro,"Liquidar Empenho concluído com sucesso!Nota n.(".$obREmpenhoNotaLiquidacao->getCodNota()."/".Sessao::getExercicio().")","","aviso",Sessao::getId(), "../");
                }
            } else {
                if ($request->get('boEmitirOP') == "S") {
                    $pgProx = CAM_GF_EMP_INSTANCIAS."ordemPagamento/FMManterOrdemPagamento.php";
                    $stFiltroEmissaoOP  = "&acao=816&modulo=10&funcionalidade=203";
                    $stFiltroEmissaoOP .= "&inCodEntidade=".$request->get("inCodEntidade")."&inCodEmpenho=".$request->get("inCodEmpenho");
                    $stFiltroEmissaoOP .= "&dtExercicioEmpenho=".$request->get("dtExercicioEmpenho");
                    $stFiltroEmissaoOP .= "&stLiq=1";
                    $stFiltroEmissaoOP .= "&dtDataVencimento=".$request->get('dtValidadeFinal');
                    $stFiltroEmissaoOP .= "&inCodNota=".$obREmpenhoNotaLiquidacao->getCodNota()."&stExercicioNota=".Sessao::getExercicio();
                    $stFiltroEmissaoOP .= "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos');
                    $stFiltroEmissaoOP .= "&stAcaoLiquidacao=liquidar";
                    $stFiltroEmissaoOP .= "&acaoLiquidacao=812&moduloLiquidacao=10&funcionalidadeLiquidacao=202";
                    $stFiltroEmissaoOP .= "&pgProxLiquidacao=".$pgProxLiquidacao[0];
                    print '<script type="text/javascript">
                                mudaMenu         ( "Ordem de Pagamento","203" );
                           </script>';

                    SistemaLegado::alertaAviso($pgProx.'?'.Sessao::getId()."&stAcao=incluir".$stFiltroEmissaoOP,"Liquidar Empenho concluído com sucesso!Nota n.(".$obREmpenhoNotaLiquidacao->getCodNota()."/".Sessao::getExercicio().")","","aviso",Sessao::getId(),"../");
                } else {
                    SistemaLegado::alertaAviso($pgList."?"."&stAcao=liquidar".$stFiltro,"Liquidar Empenho concluído com sucesso!Nota n.(".$obREmpenhoNotaLiquidacao->getCodNota()."/".Sessao::getExercicio().")","","aviso",Sessao::getId(), "../");
                }
            }

            Sessao::write('assinaturasPdf', $arAssinaturas);

            Sessao::geraURLRandomica();

            $stCaminho = CAM_GF_EMP_INSTANCIAS."liquidacao/OCRelatorioNotaLiquidacaoEmpenho.php";
            $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodNota=".$obREmpenhoNotaLiquidacao->getCodNota()."&stDtLiquidacao=".$obREmpenhoNotaLiquidacao->getDtLiquidacao()."&acao=812";
            $stCampos .= "&inCodEntidade=" .$request->get('inCodEntidade')."&boImplantado=".$obREmpenhoEmpenho->getImplantado()."&stExercicioNota=".Sessao::getExercicio()."&dtExercicioEmpenho=".$request->get("dtExercicioEmpenho");
            SistemaLegado::executaFrameOculto( "var x = setTimeout('window.open(\'".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."\',\'oculto\');',50);" );
        } else {
            $stErro = $obErro->getDescricao();
            if (strpos(strtolower($stErro),"context") > 0) {
                $stErro = substr($stErro,0,strpos(strtolower($stErro),"context:"));
                $stErro = $stErro." Erro Auditado! ";
            }
            if (strpos(strtolower($stErro),"erro") === 0) {
                $stErro = substr($stErro,6);
            }
            SistemaLegado::exibeAviso(urlencode($stErro),"n_incluir","erro");

            SistemaLegado::liberaFrames(true,true);
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obREmpenhoNotaLiquidacao->getTEmpenhoNotaLiquidacao() );
    break;
}

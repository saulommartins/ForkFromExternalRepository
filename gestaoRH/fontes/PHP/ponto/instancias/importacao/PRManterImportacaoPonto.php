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
    * Formulário
    * Data de Criação: 07/10/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.10.04

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoImportacao.class.php"                             );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoDelimitador.class.php"                            );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoDelimitadorColunas.class.php"                            );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoTamanhoFixo.class.php"                            );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoImportacaoPonto.class.php"                               );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoImportacaoPontoErro.class.php"                           );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoImportacaoPontoHorario.class.php"                        );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php"                            );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                    );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoDadosRelogioPonto.class.php"                             );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoDadosRelogioPontoExtras.class.php"                       );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoRelogioPontoDias.class.php"                              );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoRelogioPontoHorario.class.php"                           );
include_once ( CLA_ARQUIVO_CSV                                                                        );
include_once ( CLA_ARQUIVO                                                                            );

$stPrograma = "ManterImportacaoPonto";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCaminho  = $_FILES["stCaminho"]["tmp_name"];

function getProxCodImportacao()
{
    $obTPontoImportacaoPonto =  new TPontoImportacaoPonto();
    $obTPontoImportacaoPonto->setCampoCod("cod_importacao");
    $obTPontoImportacaoPonto->setComplementoChave("");
    $obTPontoImportacaoPonto->proximoCod($inCodImportacao);

    return $inCodImportacao;
}

function getProxCodImportacaoErro()
{
    $obTPontoImportacaoPontoErro =  new TPontoImportacaoPontoErro();
    $obTPontoImportacaoPontoErro->setCampoCod("cod_importacao_erro");
    $obTPontoImportacaoPontoErro->setComplementoChave("");
    $obTPontoImportacaoPontoErro->proximoCod($inCodImportacaoErro);

    return $inCodImportacaoErro;
}

function importaDemilitador($rsFormatoImportacao)
{
    global $stCaminho;

    $inCodMatrPonto = 1;
    $inCodDia       = 2;
    $inCodMes       = 3;
    $inCodAno       = 4;
    $inCodHora1     = 5;
    $inCodMinuto1   = 6;
    $inCodHora2     = 7;
    $inCodMinuto2   = 8;
    $inCodHora3     = 9;
    $inCodMinuto3   = 10;
    $inCodHora4     = 11;
    $inCodMinuto4   = 12;

    $arServidoresImportados     = array();
    $arServidoresImportadosErro = array();
    $arContratosExcluidos       = array();

    // Busca a referência da posição de cada campo
    $stFiltro  = " WHERE cod_formato = ".$_POST["inCodFormato"];
    $obTPontoDelimitadorColunas = new TPontoDelimitadorColunas();
    $obTPontoDelimitadorColunas->recuperaTodos($rsDelimitadorColunas, $stFiltro);

    // Busca os dados sobre o delimitador do arquivo
    $stFiltro  = " WHERE cod_formato = ".$_POST["inCodFormato"];
    $obTPontoFormatoDelimitador = new TPontoFormatoDelimitador();
    $obTPontoFormatoDelimitador->recuperaTodos($rsFormatoDelimitador, $stFiltro);

    // Monta array com indicando a coluna dos campos no arquivo
    $arFormatoDelimitador = array();
    $inContColunasHora = 1;
    while (!$rsDelimitadorColunas->eof()) {
        $inCodCampo = $rsDelimitadorColunas->getCampo("cod_campo");
        $arFormatoDelimitador[$inCodCampo] = $rsDelimitadorColunas->getCampo("coluna");

        if ($inCodCampo == 7 || $inCodCampo == 9 || $inCodCampo == 11) {
            $inContColunasHora++;
        }

        $rsDelimitadorColunas->proximo();
    }

    // Abre arquivo e quebra por delimitador
    $obArquivoCSV = new ArquivoCSV( $stCaminho );
    $obArquivoCSV->setDelimitadorColuna( $rsFormatoDelimitador->getCampo("formato_delimitador") );
    $obErro = $obArquivoCSV->Abrir('r');

    if ( !$obErro->ocorreu() ) {

        Sessao::setTrataExcecao(true);

        $obTPontoImportacaoPonto        =  new TPontoImportacaoPonto();
        $obTPontoImportacaoPontoErro    =  new TPontoImportacaoPontoErro();
        $obTPontoImportacaoPontoHorario =  new TPontoImportacaoPontoHorario();

        $obTPontoImportacaoPontoHorario->obTPontoImportacaoPonto = &$obTPontoImportacaoPonto;
        $obTPontoImportacaoPontoErro->excluirTodos();

        $inCodImportacao     = getProxCodImportacao();
        $inCodImportacaoErro = getProxCodImportacaoErro();

        Sessao::write("inCodImportacaoErro", $inCodImportacaoErro);

        while ( !feof( $obArquivoCSV->reArquivo ) ) {

            $arLinhas = $obArquivoCSV->LerLinha();

            $inColunaMatriculaCartaoPonto = $arFormatoDelimitador[$inCodMatrPonto];
            $inColunaDia                  = $arFormatoDelimitador[$inCodDia];
            $inColunaMes                  = $arFormatoDelimitador[$inCodMes];
            $inColunaAno                  = $arFormatoDelimitador[$inCodAno];
            $inColunaHora1                = $arFormatoDelimitador[$inCodHora1];
            $inColunaMinuto1              = $arFormatoDelimitador[$inCodMinuto1];
            $inColunaHora2                = $arFormatoDelimitador[$inCodHora2];
            $inColunaMinuto2              = $arFormatoDelimitador[$inCodMinuto2];
            $inColunaHora3                = $arFormatoDelimitador[$inCodHora3];
            $inColunaMinuto3              = $arFormatoDelimitador[$inCodMinuto3];
            $inColunaHora4                = $arFormatoDelimitador[$inCodHora4];
            $inColunaMinuto4              = $arFormatoDelimitador[$inCodMinuto4];

            if ( count($arLinhas)>0 && $arLinhas[0] != "" && is_array($arLinhas) ) {
                // Verifica se a data da linha a ser importada esta dentro do periodo informado
                $stDtPonto     = trim($arLinhas[$inColunaDia-1]."/".$arLinhas[$inColunaMes-1]."/".$arLinhas[$inColunaAno-1]);
                $inDataInicial = formataData($_POST["stDataInicial"]);
                $inDataFinal   = formataData($_POST["stDataFinal"]);
                $inDataPonto   = formataData($stDtPonto);
                $boImportar    = true;

                if (trim($_POST["stDataInicial"])!="" && trim($_POST["stDataFinal"])!="") {
                    if ($inDataPonto <  $inDataInicial || $inDataPonto >  $inDataFinal) {
                        $boImportar = false;
                    }
                }

                // Verifica se é por matricula ou Cartão Ponto e busca o cod_contrato do servidor
                if ( $rsFormatoImportacao->getCampo("referencia_cadastro") == "M" ) {
                    $stFiltro = " WHERE registro = ".(int) $arLinhas[$inColunaMatriculaCartaoPonto-1];
                    $obTPessoalContrato = new TPessoalContrato();
                    $obTPessoalContrato->recuperaTodos($rsContrato, $stFiltro);
                } else {
                    $stFiltro = " WHERE nr_cartao_ponto = '".$arLinhas[$inColunaMatriculaCartaoPonto-1]."'";
                    $obTPessoalContratoServidor = new TPessoalContratoServidor();
                    $obTPessoalContratoServidor->recuperaTodos($rsContrato, $stFiltro);
                }

                // Verifica as matriculas selecionadas para importação parcial
                if ($_POST["boImportacaoParcial"]) {
                    $arContratosParciais = montaContratosParciais();

                    if (!array_key_exists($rsContrato->getCampo("cod_contrato"), $arContratosParciais)) {
                        $boImportar = false;
                    }
                }

                if ($boImportar === true) {

                    if ( $rsContrato->getNumLinhas() == -1 ) {
                        if (trim(implode("", $arLinhas))!="") {
                            $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                            $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                            $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                            $obTPontoImportacaoPontoErro->setDado("linha"               , implode($rsFormatoDelimitador->getCampo("formato_delimitador"), $arLinhas));
                            $obTPontoImportacaoPontoErro->inclusao();

                            $inCodContrato = $rsContrato->getCampo("cod_contrato");
                            $arServidoresImportadosErro[] = 1;
                        }
                    } else {
                        // Verifica se o cantrato já esta gravado para a data
                        $stFiltro  = " WHERE cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                        $stFiltro .= "   AND dt_ponto = to_date('".$stDtPonto."', 'dd/mm/yyyy')";
                        $obTPontoImportacaoPonto->recuperaTodos($rsImportacaoPonto, $stFiltro);

                        $obTPontoImportacaoPonto->setDado("cod_contrato"   , $rsContrato->getCampo("cod_contrato"));
                        if ( $rsImportacaoPonto->getNumLinhas() == -1 ) {
                            $obTPontoImportacaoPonto->setDado("cod_ponto"      , "");
                            $obTPontoImportacaoPonto->setDado("cod_formato"    , $_POST["inCodFormato"]);
                            $obTPontoImportacaoPonto->setDado("dt_ponto"       , $stDtPonto);
                            $obTPontoImportacaoPonto->setDado("cod_importacao" , $inCodImportacao);
                            $obTPontoImportacaoPonto->inclusao();
                        } else {
                            $obTPontoImportacaoPonto->setDado("cod_ponto", $rsImportacaoPonto->getCampo("cod_ponto"));

                            $stContratoExcluido = $rsContrato->getCampo("cod_contrato").$stDtPonto;
                            if ($_POST["boSubstituirDados"] && !in_array($stContratoExcluido,$arContratosExcluidos) ) {
                                $arContratosExcluidos[] = $stContratoExcluido;

                                $stFiltro  = " WHERE cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                                $stFiltro .= "   AND cod_ponto = ".$rsImportacaoPonto->getCampo("cod_ponto");
                                $stFiltro .= "   AND cod_importacao = ".$rsImportacaoPonto->getCampo("cod_importacao");
                                $obTPontoImportacaoPontoHorario->recuperaTodos($rsImportacaoPontoHorario, $stFiltro);

                                while (!$rsImportacaoPontoHorario->eof()) {
                                    $obTPontoImportacaoPontoHorario->setDado("cod_ponto"      , $rsImportacaoPontoHorario->getCampo("cod_ponto"));
                                    $obTPontoImportacaoPontoHorario->setDado("cod_contrato"   , $rsImportacaoPontoHorario->getCampo("cod_contrato"));
                                    $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $rsImportacaoPontoHorario->getCampo("cod_importacao"));
                                    $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , $rsImportacaoPontoHorario->getCampo("cod_hora"));
                                    $obTPontoImportacaoPontoHorario->exclusao();

                                    $rsImportacaoPontoHorario->proximo();
                                }

                                $obTPontoImportacaoPonto->setDado("cod_ponto"      , $rsImportacaoPonto->getCampo("cod_ponto"));
                                $obTPontoImportacaoPonto->setDado("cod_contrato"   , $rsImportacaoPonto->getCampo("cod_contrato"));
                                $obTPontoImportacaoPonto->setDado("cod_importacao" , $rsImportacaoPonto->getCampo("cod_importacao"));
                                $obTPontoImportacaoPonto->exclusao();

                                $obTPontoImportacaoPonto->setDado("cod_ponto"      , "");
                                $obTPontoImportacaoPonto->setDado("cod_formato"    , $_POST["inCodFormato"]);
                                $obTPontoImportacaoPonto->setDado("dt_ponto"       , $stDtPonto);
                                $obTPontoImportacaoPonto->setDado("cod_importacao" , $inCodImportacao);
                                $obTPontoImportacaoPonto->inclusao();

                            } else {
                                $inCodImportacao = $rsImportacaoPonto->getCampo("cod_importacao");
                            }
                        }

                        // Insere os horários de cada servidor
                        $inCodContrato = $rsContrato->getCampo("cod_contrato");
                        switch ($inContColunasHora) {
                            case "1":
                                $stHora1 = trim($arLinhas[$inColunaHora1-1].":".$arLinhas[$inColunaMinuto1-1]);
                                if (validaHora($stHora1)) {
                                    if (existeHorario($inCodContrato, $stDtPonto, $stHora1) === false) {
                                        $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                        $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora1);
                                        $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                        $obTPontoImportacaoPontoHorario->inclusao();
                                        $arServidoresImportados[$inCodContrato] = 1;
                                    }
                                } else {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode($rsFormatoDelimitador->getCampo("formato_delimitador"), $arLinhas));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }
                                break;

                            case "2":
                                $stHora1 = trim($arLinhas[$inColunaHora1-1].":".$arLinhas[$inColunaMinuto1-1]);
                                if (validaHora($stHora1)) {
                                    if (existeHorario($inCodContrato, $stDtPonto, $stHora1) === false) {
                                        $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                        $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora1);
                                        $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                        $obTPontoImportacaoPontoHorario->inclusao();
                                        $arServidoresImportados[$inCodContrato] = 1;
                                    }
                                } else {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode($rsFormatoDelimitador->getCampo("formato_delimitador"), $arLinhas));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }

                                $stHora2 = trim($arLinhas[$inColunaHora2-1].":".$arLinhas[$inColunaMinuto2-1]);
                                if (validaHora($stHora2)) {
                                    if (existeHorario($inCodContrato, $stDtPonto, $stHora2) === false) {
                                        $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                        $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora2);
                                        $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                        $obTPontoImportacaoPontoHorario->inclusao();
                                        $arServidoresImportados[$inCodContrato] = 1;
                                    }
                                } else {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode($rsFormatoDelimitador->getCampo("formato_delimitador"), $arLinhas));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }
                                break;

                            case "4":
                                $stHora1 = trim($arLinhas[$inColunaHora1-1].":".$arLinhas[$inColunaMinuto1-1]);
                                if (validaHora($stHora1)) {
                                    if (existeHorario($inCodContrato, $stDtPonto, $stHora1) === false) {
                                        $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                        $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora1);
                                        $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                        $obTPontoImportacaoPontoHorario->inclusao();
                                        $arServidoresImportados[$inCodContrato] = 1;
                                    }
                                } else {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode($rsFormatoDelimitador->getCampo("formato_delimitador"), $arLinhas));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }

                                $stHora2 = trim($arLinhas[$inColunaHora2-1].":".$arLinhas[$inColunaMinuto2-1]);
                                if (validaHora($stHora2)) {
                                    if (existeHorario($inCodContrato, $stDtPonto, $stHora2) === false) {
                                        $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                        $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora2);
                                        $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                        $obTPontoImportacaoPontoHorario->inclusao();
                                        $arServidoresImportados[$inCodContrato] = 1;
                                    }
                                } else {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode($rsFormatoDelimitador->getCampo("formato_delimitador"), $arLinhas));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }

                                $stHora3 = trim($arLinhas[$inColunaHora3-1].":".$arLinhas[$inColunaMinuto3-1]);
                                if (validaHora($stHora3)) {
                                    if (existeHorario($inCodContrato, $stDtPonto, $stHora3) === false) {
                                        $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                        $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora3);
                                        $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                        $obTPontoImportacaoPontoHorario->inclusao();
                                        $arServidoresImportados[$inCodContrato] = 1;
                                    }
                                } else {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode($rsFormatoDelimitador->getCampo("formato_delimitador"), $arLinhas));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }

                                $stHora4 = trim($arLinhas[$inColunaHora4-1].":".$arLinhas[$inColunaMinuto4-1]);
                                if (validaHora($stHora4)) {
                                    if (existeHorario($inCodContrato, $stDtPonto, $stHora4) === false) {
                                        $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                        $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora4);
                                        $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                        $obTPontoImportacaoPontoHorario->inclusao();
                                        $arServidoresImportados[$inCodContrato] = 1;
                                    }
                                } else {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode($rsFormatoDelimitador->getCampo("formato_delimitador"), $arLinhas));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }
                                break;
                        }
                        if ($_POST["boSubstituirDados"]) {
                            //Excluir todas as manutenções do ponto para o contrato na data informada
                            $obTPontoRelogioPontoDias    = new TPontoRelogioPontoDias();
                            $obTPontoRelogioPontoHorario = new TPontoRelogioPontoHorario();
                            $obTPontoDadosRelogioPonto   = new TPontoDadosRelogioPonto();
                            $obTPontoDadosRelogioPontoExtras = new TPontoDadosRelogioPontoExtras();

                            $stFiltro  = " WHERE relogio_ponto_dias.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                            $stFiltro .= "   AND relogio_ponto_dias.dt_ponto = to_date('".$stDtPonto."', 'dd/mm/yyyy')";
                            $obTPontoRelogioPontoDias->recuperaRelacionamento($rsRelogioPontoDias, $stFiltro);

                            //Excluindo horarios
                            while (!$rsRelogioPontoDias->eof()) {
                                $obTPontoRelogioPontoHorario->setDado("cod_contrato", $rsRelogioPontoDias->getCampo("cod_contrato"));
                                $obTPontoRelogioPontoHorario->setDado("timestamp"   , $rsRelogioPontoDias->getCampo("timestamp"));
                                $obTPontoRelogioPontoHorario->setDado("cod_ponto"   , $rsRelogioPontoDias->getCampo("cod_ponto"));
                                $obTPontoRelogioPontoHorario->setDado("cod_horario" , $rsRelogioPontoDias->getCampo("cod_horario"));
                                $obTPontoRelogioPontoHorario->exclusao();

                                $rsRelogioPontoDias->proximo();
                            }

                            //Excluindo dias
                            $rsRelogioPontoDias->setPrimeiroElemento();
                            while (!$rsRelogioPontoDias->eof()) {
                                $obTPontoRelogioPontoDias->setDado("cod_contrato", $rsRelogioPontoDias->getCampo("cod_contrato"));
                                $obTPontoRelogioPontoDias->setDado("cod_ponto"   , $rsRelogioPontoDias->getCampo("cod_ponto"));
                                $obTPontoRelogioPontoDias->exclusao();

                                $rsRelogioPontoDias->proximo();
                            }

                            // Verifica se existe dados para outros dias
                            $stFiltro  = " WHERE relogio_ponto_dias.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                            $obTPontoRelogioPontoDias->recuperaTodos($rsRelogioPontoDias, $stFiltro);

                            if ($rsRelogioPontoDias->getNumLinhas() == -1) {
                                // Excluindo dados relogio ponto extras
                                $obTPontoDadosRelogioPontoExtras->setComplementoChave("cod_contrato");
                                $obTPontoDadosRelogioPontoExtras->setDado("cod_contrato", $rsContrato->getCampo("cod_contrato"));
                                $obTPontoDadosRelogioPontoExtras->exclusao();

                                // Excluindo dados relogio ponto
                                $obTPontoDadosRelogioPonto->setDado("cod_contrato", $rsContrato->getCampo("cod_contrato"));
                                $obTPontoDadosRelogioPonto->exclusao();
                            }
                        }
                    }
                }
            }
        }
        $obArquivoCSV->Fechar();
        Sessao::write("inTotalServidoresImportados"     , count($arServidoresImportados));
        Sessao::write("inTotalServidoresImportadosErro" , count($arServidoresImportadosErro));
        Sessao::write("inCodImportacao"                 , $inCodImportacao);
        Sessao::encerraExcecao();
    }
}

function importaFixo($rsFormatoImportacao)
{
    global $stCaminho;

    $arServidoresImportados     = array();
    $arServidoresImportadosErro = array();
    $arContratosExcluidos       = array();

    $inCodMatrPonto = 1;
    $inCodDia       = 2;
    $inCodMes       = 3;
    $inCodAno       = 4;
    $inCodHora1     = 5;
    $inCodMinuto1   = 6;
    $inCodHora2     = 7;
    $inCodMinuto2   = 8;
    $inCodHora3     = 9;
    $inCodMinuto3   = 10;
    $inCodHora4     = 11;
    $inCodMinuto4   = 12;

    // Busca os dados sobre a configuração do arquivo
    $stFiltro  = " WHERE cod_formato = ".$_POST["inCodFormato"];
    $obTPontoFormatoTamanhoFixo = new TPontoFormatoTamanhoFixo();
    $obTPontoFormatoTamanhoFixo->recuperaTodos($rsFormatoTamanhoFixo, $stFiltro);

    //Montando array de referencia das posições
    $arPosicaoCampos = array();
    while (!$rsFormatoTamanhoFixo->eof()) {
        $inCodCampo   = $rsFormatoTamanhoFixo->getCampo("cod_campo");
        $inPosInicial = $rsFormatoTamanhoFixo->getCampo("posicao_inicial");
        $inPosFinal   = $rsFormatoTamanhoFixo->getCampo("posicao_final");

        $arPosicaoCampos[$inCodCampo]["posicao_inicial"] = $inPosInicial;
        $arPosicaoCampos[$inCodCampo]["posicao_final"]   = $inPosFinal;
        $arPosicaoCampos[$inCodCampo]["tamanho"]         = ($inPosFinal-$inPosInicial);

        $rsFormatoTamanhoFixo->proximo();
    }

    // Abre arquivo e quebra por delimitador
    $obArquivo = new Arquivo( $stCaminho );
    $obArquivo->Ler();
    $arLinhas = explode("\n", $obArquivo->stConteudo);

    if (count($arLinhas)>0 && is_array($arLinhas)) {
        $inContador = 0;
        // Percorre as linhas do arquivo
        foreach ($arLinhas as $chave => $stLinha) {
            $inContador++;
            $inContadorInterno = 0;
            // Percorre o array com as posições de cada campo
            foreach ($arPosicaoCampos as $inCodCampo => $dadosPosicao) {
                if ($stLinha <> '') {
                    $inContadorInterno++;
                    $valor = substr($stLinha, $dadosPosicao["posicao_inicial"]-1, $dadosPosicao["tamanho"]+1);
                    $arHorariosPonto[$inContador][$inContadorInterno] = $valor;
                }
            }
        }
    }

    if (count($arHorariosPonto)>0) {
        Sessao::setTrataExcecao(true);

        $obTPontoImportacaoPonto        =  new TPontoImportacaoPonto();
        $obTPontoImportacaoPontoErro    =  new TPontoImportacaoPontoErro();
        $obTPontoImportacaoPontoHorario =  new TPontoImportacaoPontoHorario();

        $obTPontoImportacaoPontoHorario->obTPontoImportacaoPonto = &$obTPontoImportacaoPonto;
        $obTPontoImportacaoPontoErro->excluirTodos();

        $inCodImportacao     = getProxCodImportacao();
        $inCodImportacaoErro = getProxCodImportacaoErro();

        Sessao::write("inCodImportacao"    , $inCodImportacao);
        Sessao::write("inCodImportacaoErro", $inCodImportacaoErro);

        foreach ($arHorariosPonto as $chave => $dadosHorario) {
            $inTipoLayout = (count($dadosHorario)-4)/2;

            // Verifica se a data da linha a ser importada esta dentro do periodo informado
            $stDtPonto     = trim($dadosHorario[$inCodDia]."/".$dadosHorario[$inCodMes]."/".$dadosHorario[$inCodAno]);
            $inDataInicial = formataData($_POST["stDataInicial"]);
            $inDataFinal   = formataData($_POST["stDataFinal"]);
            $inDataPonto   = formataData($stDtPonto);
            $boImportar    = true;

            if (trim($_POST["stDataInicial"])!="" && trim($_POST["stDataFinal"])!="") {
                if ($inDataPonto <  $inDataInicial || $inDataPonto >  $inDataFinal) {
                    $boImportar = false;
                }
            }

            // Verifica se é por matricula ou Cartão Ponto e busca o cod_contrato do servidor
            if ($rsFormatoImportacao->getCampo("referencia_cadastro") == "M") {
                $stFiltro = " WHERE registro = ".(int) $dadosHorario[$inCodMatrPonto];
                $obTPessoalContrato = new TPessoalContrato();
                $obTPessoalContrato->recuperaTodos($rsContrato, $stFiltro);
            } else {
                $stFiltro = " WHERE nr_cartao_ponto = '".$dadosHorario[$inCodMatrPonto]."'";
                $obTPessoalContratoServidor = new TPessoalContratoServidor();
                $obTPessoalContratoServidor->recuperaTodos($rsContrato, $stFiltro);
            }

            // Verifica as matriculas selecionadas para importação parcial
            if ($_POST["boImportacaoParcial"]) {
                $arContratosParciais = montaContratosParciais();

                if (!array_key_exists($rsContrato->getCampo("cod_contrato"), $arContratosParciais)) {
                    $boImportar = false;
                }
            }

            if ($boImportar === true) {

                if ($rsContrato->getNumLinhas() == -1) {
                    // Verifica se não é uma linha em branco do arquivo
                    if (trim(implode(" ", $dadosHorario))!="") {
                        $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                        $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                        $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                        $obTPontoImportacaoPontoErro->setDado("linha"               , implode(" ", $dadosHorario));
                        $obTPontoImportacaoPontoErro->inclusao();

                        $inCodContrato = $rsContrato->getCampo("cod_contrato");
                        $arServidoresImportadosErro[] = 1;
                    }
                } else {
                    // Verifica se o cantrato já esta gravado para a data
                    $stFiltro  = " WHERE cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                    $stFiltro .= "   AND dt_ponto = to_date('".$stDtPonto."', 'dd/mm/yyyy')";

                    $obTPontoImportacaoPonto->recuperaTodos($rsImportacaoPonto, $stFiltro);

                    $obTPontoImportacaoPonto->setDado("cod_contrato"   , $rsContrato->getCampo("cod_contrato"));
                    if ($rsImportacaoPonto->getNumLinhas() == -1) {
                        $obTPontoImportacaoPonto->setDado("cod_ponto"      , "");
                        $obTPontoImportacaoPonto->setDado("cod_formato"    , $_POST["inCodFormato"]);
                        $obTPontoImportacaoPonto->setDado("dt_ponto"       , $stDtPonto);
                        $obTPontoImportacaoPonto->setDado("cod_importacao" , $inCodImportacao);
                        $obTPontoImportacaoPonto->inclusao();
                    } else {
                        $obTPontoImportacaoPonto->setDado("cod_ponto", $rsImportacaoPonto->getCampo("cod_ponto"));

                        $stContratoExcluido = $rsContrato->getCampo("cod_contrato").$stDtPonto;
                        if ($_POST["boSubstituirDados"] && !in_array($stContratoExcluido,$arContratosExcluidos) ) {
                            $arContratosExcluidos[] = $stContratoExcluido;

                            $stFiltro  = " WHERE cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                            $stFiltro .= "   AND cod_ponto = ".$rsImportacaoPonto->getCampo("cod_ponto");
                            $stFiltro .= "   AND cod_importacao = ".$rsImportacaoPonto->getCampo("cod_importacao");
                            $obTPontoImportacaoPontoHorario->recuperaTodos($rsImportacaoPontoHorario, $stFiltro);

                            while (!$rsImportacaoPontoHorario->eof()) {
                                $obTPontoImportacaoPontoHorario->setDado("cod_ponto"      , $rsImportacaoPontoHorario->getCampo("cod_ponto"));
                                $obTPontoImportacaoPontoHorario->setDado("cod_contrato"   , $rsImportacaoPontoHorario->getCampo("cod_contrato"));
                                $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $rsImportacaoPontoHorario->getCampo("cod_importacao"));
                                $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , $rsImportacaoPontoHorario->getCampo("cod_hora"));
                                $obTPontoImportacaoPontoHorario->exclusao();

                                $rsImportacaoPontoHorario->proximo();
                            }

                            $obTPontoImportacaoPonto->setDado("cod_ponto"      , $rsImportacaoPonto->getCampo("cod_ponto"));
                            $obTPontoImportacaoPonto->setDado("cod_contrato"   , $rsImportacaoPonto->getCampo("cod_contrato"));
                            $obTPontoImportacaoPonto->setDado("cod_importacao" , $rsImportacaoPonto->getCampo("cod_importacao"));
                            $obTPontoImportacaoPonto->exclusao();

                            $obTPontoImportacaoPonto->setDado("cod_ponto"      , "");
                            $obTPontoImportacaoPonto->setDado("cod_formato"    , $_POST["inCodFormato"]);
                            $obTPontoImportacaoPonto->setDado("dt_ponto"       , $stDtPonto);
                            $obTPontoImportacaoPonto->setDado("cod_importacao" , $inCodImportacao);
                            $obTPontoImportacaoPonto->inclusao();
                        } else {
                            $inCodImportacao = $rsImportacaoPonto->getCampo("cod_importacao");
                        }
                    }

                    // Insere os horários de cada servidor
                    $inCodContrato = $rsContrato->getCampo("cod_contrato");
                    switch ($inTipoLayout) {
                        case "1":
                            $stHora1 = trim($dadosHorario[$inCodHora1].":".$dadosHorario[$inCodMinuto1]);
                            if (validaHora($stHora1)) {
                                if (existeHorario($inCodContrato, $stDtPonto, $stHora1) === false) {
                                    $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                    $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora1);
                                    $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                    $obTPontoImportacaoPontoHorario->inclusao();
                                    $arServidoresImportados[$inCodContrato] = 1;
                                }
                            } else {
                                if (trim(implode(" ", $dadosHorario))!="") {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode(" ", $dadosHorario));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }
                            }
                            break;

                        case "2":
                            $stHora1 = trim($dadosHorario[$inCodHora1].":".$dadosHorario[$inCodMinuto1]);
                            if (validaHora($stHora1)) {
                                if (existeHorario($inCodContrato, $stDtPonto, $stHora1) === false) {
                                    $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                    $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora1);
                                    $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                    $obTPontoImportacaoPontoHorario->inclusao();
                                    $arServidoresImportados[$inCodContrato] = 1;
                                }
                            } else {
                                if (trim(implode(" ", $dadosHorario))!="") {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode(" ", $dadosHorario));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }
                            }

                            $stHora2 = trim($dadosHorario[$inCodHora2].":".$dadosHorario[$inCodMinuto2]);
                            if (validaHora($stHora2)) {
                                if (existeHorario($inCodContrato, $stDtPonto, $stHora2) === false) {
                                    $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                    $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora2);
                                    $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                    $obTPontoImportacaoPontoHorario->inclusao();
                                    $arServidoresImportados[$inCodContrato] = 1;
                                }
                            } else {
                                if (trim(implode(" ", $dadosHorario))!="") {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode(" ", $dadosHorario));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }
                            }
                            break;

                        case "4":
                            $stHora1 = trim($dadosHorario[$inCodHora1].":".$dadosHorario[$inCodMinuto1]);
                            if (validaHora($stHora1)) {
                                if (existeHorario($inCodContrato, $stDtPonto, $stHora1) === false) {
                                    $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                    $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora1);
                                    $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                    $obTPontoImportacaoPontoHorario->inclusao();
                                    $arServidoresImportados[$inCodContrato] = 1;
                                }
                            } else {
                                if (trim(implode(" ", $dadosHorario))!="") {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode(" ", $dadosHorario));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }
                            }

                            $stHora2 = trim($dadosHorario[$inCodHora2].":".$dadosHorario[$inCodMinuto2]);
                            if (validaHora($stHora2)) {
                                if (existeHorario($inCodContrato, $stDtPonto, $stHora2) === false) {
                                    $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                    $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora2);
                                    $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                    $obTPontoImportacaoPontoHorario->inclusao();
                                    $arServidoresImportados[$inCodContrato] = 1;
                                }
                            } else {
                                if (trim(implode(" ", $dadosHorario))!="") {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode(" ", $dadosHorario));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }
                            }

                            $stHora3 = trim($dadosHorario[$inCodHora3].":".$dadosHorario[$inCodMinuto3]);
                            if (validaHora($stHora3)) {
                                if (existeHorario($inCodContrato, $stDtPonto, $stHora3) === false) {
                                    $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                    $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora3);
                                    $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                    $obTPontoImportacaoPontoHorario->inclusao();
                                    $arServidoresImportados[$inCodContrato] = 1;
                                }
                            } else {
                                if (trim(implode(" ", $dadosHorario))!="") {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode(" ", $dadosHorario));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }
                            }

                            $stHora4 = trim($dadosHorario[$inCodHora4].":".$dadosHorario[$inCodMinuto4]);
                            if (validaHora($stHora4)) {
                                if (existeHorario($inCodContrato, $stDtPonto, $stHora4) === false) {
                                    $obTPontoImportacaoPontoHorario->setDado("cod_hora"       , "");
                                    $obTPontoImportacaoPontoHorario->setDado("horario"        , $stHora4);
                                    $obTPontoImportacaoPontoHorario->setDado("cod_importacao" , $inCodImportacao);
                                    $obTPontoImportacaoPontoHorario->inclusao();
                                    $arServidoresImportados[$inCodContrato] = 1;
                                }
                            } else {
                                if (trim(implode(" ", $dadosHorario))!="") {
                                    $obTPontoImportacaoPontoErro->setDado("cod_ponto_erro"      , "");
                                    $obTPontoImportacaoPontoErro->setDado("cod_formato"         , $_POST["inCodFormato"]);
                                    $obTPontoImportacaoPontoErro->setDado("cod_importacao_erro" , $inCodImportacaoErro);
                                    $obTPontoImportacaoPontoErro->setDado("linha"               , implode(" ", $dadosHorario));
                                    $obTPontoImportacaoPontoErro->inclusao();
                                    $arServidoresImportadosErro[] = 1;
                                }
                            }
                            break;
                    }

                    if ($_POST["boSubstituirDados"]) {
                        //Excluir todas as manutenções do ponto para o contrato na data informada
                        $obTPontoRelogioPontoDias    = new TPontoRelogioPontoDias();
                        $obTPontoRelogioPontoHorario = new TPontoRelogioPontoHorario();
                        $obTPontoDadosRelogioPonto   = new TPontoDadosRelogioPonto();
                        $obTPontoDadosRelogioPontoExtras = new TPontoDadosRelogioPontoExtras();

                        $stFiltro  = " WHERE relogio_ponto_dias.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                        $stFiltro .= "   AND relogio_ponto_dias.dt_ponto = to_date('".$stDtPonto."', 'dd/mm/yyyy')";
                        $obTPontoRelogioPontoDias->recuperaRelacionamento($rsRelogioPontoDias, $stFiltro);

                        //Excluindo horarios
                        while (!$rsRelogioPontoDias->eof()) {
                            $obTPontoRelogioPontoHorario->setDado("cod_contrato", $rsRelogioPontoDias->getCampo("cod_contrato"));
                            $obTPontoRelogioPontoHorario->setDado("timestamp"   , $rsRelogioPontoDias->getCampo("timestamp"));
                            $obTPontoRelogioPontoHorario->setDado("cod_ponto"   , $rsRelogioPontoDias->getCampo("cod_ponto"));
                            $obTPontoRelogioPontoHorario->setDado("cod_horario" , $rsRelogioPontoDias->getCampo("cod_horario"));
                            $obTPontoRelogioPontoHorario->exclusao();

                            $rsRelogioPontoDias->proximo();
                        }

                        //Excluindo dias
                        $rsRelogioPontoDias->setPrimeiroElemento();
                        while (!$rsRelogioPontoDias->eof()) {
                            $obTPontoRelogioPontoDias->setDado("cod_contrato", $rsRelogioPontoDias->getCampo("cod_contrato"));
                            $obTPontoRelogioPontoDias->setDado("cod_ponto"   , $rsRelogioPontoDias->getCampo("cod_ponto"));
                            $obTPontoRelogioPontoDias->exclusao();

                            $rsRelogioPontoDias->proximo();
                        }

                        // Verifica se existe dados para outros dias
                        $stFiltro  = " WHERE relogio_ponto_dias.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                        $obTPontoRelogioPontoDias->recuperaTodos($rsRelogioPontoDias, $stFiltro);

                        if ($rsRelogioPontoDias->getNumLinhas() == -1) {
                            // Excluindo dados relogio ponto extras
                            $obTPontoDadosRelogioPontoExtras->setComplementoChave("cod_contrato");
                            $obTPontoDadosRelogioPontoExtras->setDado("cod_contrato", $rsContrato->getCampo("cod_contrato"));
                            $obTPontoDadosRelogioPontoExtras->exclusao();

                            // Excluindo dados relogio ponto
                            $obTPontoDadosRelogioPonto->setDado("cod_contrato", $rsContrato->getCampo("cod_contrato"));
                            $obTPontoDadosRelogioPonto->exclusao();
                        }
                    }
                }
            }
        }
        Sessao::write("inTotalServidoresImportados"     , count($arServidoresImportados));
        Sessao::write("inTotalServidoresImportadosErro" , count($arServidoresImportadosErro));
        Sessao::write("inCodImportacao"                 , $inCodImportacao);
        Sessao::encerraExcecao();
    }
}

function formataData($data)
{
    list($dia, $mes, $ano) = explode("/", $data);
    $retorno = (int) $ano.$mes.$dia;

    return $retorno;
}

function existeHorario($inCodContrato, $stData, $stHora)
{
    $stFiltro  = " WHERE importacao_ponto.cod_contrato = ".$inCodContrato."                                       \n";
    $stFiltro .= "   AND importacao_ponto.dt_ponto = to_date('".$stData."', 'dd/mm/yyyy')                         \n";
    $stFiltro .= "   AND to_char(importacao_ponto_horario.horario, 'HH24:mi') = '".$stHora."'                     \n";

    $obTPontoImportacaoPontoHorario = new TPontoImportacaoPontoHorario();
    $obTPontoImportacaoPontoHorario->recuperaRelacionamento($rsImportacaoPontoHorario, $stFiltro);

    if ($rsImportacaoPontoHorario->getNumLinhas() == -1) {
        return false;
    } else {
        // Verifica caso substituir dados então exclui antes de inserir
        if ($_POST["boSubstituirDados"]) {
            $obTPontoImportacaoPontoHorario->setDado("cod_contrato" , $inCodContrato);
            $obTPontoImportacaoPontoHorario->setDado("cod_ponto"    , $rsImportacaoPontoHorario->getCampo("cod_ponto"));
            $obTPontoImportacaoPontoHorario->setDado("cod_hora"     , $rsImportacaoPontoHorario->getCampo("cod_hora"));
            $obTPontoImportacaoPontoHorario->exclusao();

            return false;
        }

        return true;
    }
}

function validaHora($stHora)
{
    $boRetorno = true;

    if (trim($stHora)=="" || trim($stHora)==":") {
        $boRetorno = false;
    }

    return $boRetorno;
}

function montaContratosParciais()
{
    $arContratos = Sessao::read("arContratos");
    $arTemp = array();

    foreach ($arContratos as $chave => $arContrato) {
        $inCodContrato = $arContrato["cod_contrato"];
        $arTemp[$inCodContrato] = 1;
    }

    return $arTemp;
}

/*
* INICIO DO PROCESSO DE IMPORTAÇÂO DO PONTO
*/

// Verifica se é por delimitador ou campos com tamanho fixo
$stFiltro = " WHERE cod_formato = ".$_POST["inCodFormato"];
$obTPontoFormatoImportacao = new TPontoFormatoImportacao();
$obTPontoFormatoImportacao->recuperaTodos($rsFormatoImportacao, $stFiltro);

if ($rsFormatoImportacao->getCampo("formato_colunas") == "D") {
    importaDemilitador( $rsFormatoImportacao );
} else {
    importaFixo( $rsFormatoImportacao );
}

Sessao::write("stDataInicial", $_POST["stDataInicial"]);
Sessao::write("stDataFinal"  , $_POST["stDataFinal"]);

sistemaLegado::alertaAviso($pgForm, "Importação Finalizada","incluir","aviso", Sessao::getId(), "../");
?>

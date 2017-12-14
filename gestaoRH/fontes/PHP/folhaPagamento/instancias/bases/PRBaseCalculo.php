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
    * Data de Criação: 06/08/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.05.67

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBases.class.php"                                  );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBasesEvento.class.php"                            );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBasesEventoCriado.class.php"                      );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEvento.class.php"                     );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSequenciaCalculoEvento.class.php"                 );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSequenciaCalculo.class.php"                       );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                                 );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php"                                   );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncaoExterna.class.php"                            );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCorpoFuncaoExterna.class.php"                       );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoVariavel.class.php"                                 );
include_once ( CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php"                                             );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"                                    );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php"                                         );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidade.class.php"                                 );

$stPrograma = 'BaseCalculo';
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$boApresetacaoValor   = $request->get("boApresetacaoValor")   == "S" ? true : false;
$boInsercaoAutomatica = $_REQUEST["boInsercaoAutomatica"] == "S" ? true : false;
$boEventoSistema      = $request->get("boEventoSistema")      == "S" ? true : false;
$boTipoBase           = $request->get("boTipoBase");
$stAcao               = $_REQUEST["stAcao"];
$stNomBase            = $_REQUEST["stNomBase"];
$inCodBase            = $_REQUEST["inCodBase"];
$stLink               = "?stAcao=".$stAcao;
$arEventosCalculoBase = carregaEventosCalculoBase();
$inCodModulo          = 27;
$inCodBiblioteca      = buscaCodigoBiblioteca();

function carregaEventosCalculoBase()
{
    $arEventos = array();
    foreach ($_REQUEST as $chave => $valor) {
        $pos = strpos($chave, "arEventosCalculoBase");
        if ($pos === false) {
            //faz nada
        } else {
            $arDados = explode("_", $chave);
            $arEventos[] = $arDados[1];
        }
    }

    return $arEventos;
}

function validaFormulario()
{
    global $stAcao, $stNomBase, $arEventosCalculoBase, $inCodBase;
    $obErro  = new Erro;

    //válida se o número de eventos selecionados para o cálculo da base > 0
    if ( count($arEventosCalculoBase) == 0 && $stAcao != "excluir") {
        $obErro->setDescricao("Selecione pelo menos um evento para o cálculo da base.");

        return $obErro;
    }

    if ( trim($stAcao) == "incluir" ) {
        //verifica se o codigo do evento informado já não existe na base
        if ($_REQUEST["boInsercaoAutomatica"] == "S") {
            $stFiltro = " WHERE codigo = '".$_REQUEST['stCodigo']."'";
            $TFolhaPagamentoEvento = new TFolhaPagamentoEvento();
            $TFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );

            if ($rsEvento->getNumLinhas() > 0) {
                $obErro->setDescricao("Código do evento informado já existe.");

                return $obErro;
            }
        }

        //verifica se o nome da base esta ok, sem espaços
        $arNomeBase = explode(" ", $stNomBase);
        if ( count($arNomeBase)>1 ) {
            $obErro->setDescricao("O nome da base de cálculo (".$stNomBase.") não pode possuir espaços entre os nomes.");

            return $obErro;
        }

        //verifica se existe outra função com  o mesmo nome
        $stCondicao = " WHERE nom_funcao ilike ('".trim($stNomBase)."')";
        $obTAdministracaoFuncao = new TAdministracaoFuncao();
        $obTAdministracaoFuncao->recuperaTodos($rsFuncao, $stCondicao);

        if ( $rsFuncao->getNumLinhas() > 0 ) {
            $stFiltro  = "   AND trim(upper(funcao.nom_funcao)) = trim(upper('".trim($stNomBase)."'))";
            $stFiltro .= "   AND entidade.exercicio = quote_literal(".Sessao::getExercicio().")";

            $obTFolhaPagamentoBases = new TFolhaPagamentoBases();
            $obTFolhaPagamentoBases->recuperaEntidadeFuncao($rsBases, $stFiltro);

            $obErro->setDescricao("Função ".trim($stNomBase)." já existe na entidade ".$rsBases->getCampo('nom_cgm').".");

            return $obErro;
        }
    } elseif ( trim($stAcao) == "excluir" ) {
        $stFiltro = " WHERE cod_base = ".$inCodBase;
        $boTFolhaPagamentoBasesEventoCriado = new TFolhaPagamentoBasesEventoCriado();
        $boTFolhaPagamentoBasesEventoCriado->recuperaTodos( $rsBasesEventoCriado, $stFiltro );

        if ($rsBasesEventoCriado->getNumLinhas() > 0) {
            $stMensagem = "Exclusão não permitida, base possui histórico de dados no sistema.";
            $stFiltro = " WHERE cod_evento = ".$rsBasesEventoCriado->getCampo('cod_evento');

            $arTabelasVerificacao = array("TFolhaPagamentoBeneficioEvento",
                                          "TFolhaPagamentoConfiguracaoEmpenhoEvento",
                                          "TFolhaPagamentoDecimoEvento",
                                          "TFolhaPagamentoFeriasEvento",
                                          "TFolhaPagamentoFgtsEvento",
                                          "TFolhaPagamentoPensaoEvento",
                                          "TFolhaPagamentoPrevidenciaEvento",
                                          "TFolhaPagamentoRegistroEvento",
                                          "TFolhaPagamentoRegistroEventoComplementar",
                                          "TFolhaPagamentoRegistroEventoDecimo",
                                          "TFolhaPagamentoRegistroEventoFerias",
                                          "TFolhaPagamentoRegistroEventoRescisao",
                                          "TFolhaPagamentoSalarioFamiliaEvento",
                                          "TFolhaPagamentoSindicato",
                                          "TFolhaPagamentoTabelaIrrfComprovanteRendimento",
                                          "TFolhaPagamentoTabelaIrrfEvento",
                                          "TIMACagedEvento",
                                          "TIMAConfiguracaoPasep",
                                          "TIMAEventoComposicaoRemuneracao",
                                          "TIMAEventoHorasExtras",
                                          "TPessoalAssentamentoEvento",
                                          "TPessoalAssentamentoEventoProporcional",);

            foreach ($arTabelasVerificacao as $stTabela) {
                if (strpos($stTabela,"TPessoal") === 0) {
                    include_once(CAM_GRH_PES_MAPEAMENTO.$stTabela.".class.php");
                }
                if (strpos($stTabela,"TIMA") === 0) {
                    include_once(CAM_GRH_IMA_MAPEAMENTO.$stTabela.".class.php");
                }
                if (strpos($stTabela,"TFolhaPagamento") === 0) {
                    include_once(CAM_GRH_FOL_MAPEAMENTO.$stTabela.".class.php");
                }

                $obTVerificacaoExclusao = new $stTabela;
                $obErro = $obTVerificacaoExclusao->recuperaTodos($rsVerificacao,$stFiltro,"",$boTransacao);

                if ( !$obErro->ocorreu() ) {
                    if ($rsVerificacao->getNumLinhas() > 0) {
                        $obErro->setDescricao($stMensagem);

                        return $obErro;
                    }
                } else {
                    return $obErro;
                }
            }

            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEventosDescontoExterno.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoIpe.class.php");

            // Validando tabela folhapagamento.configuracao_evento_desconto_externo
            #evento_desconto_irrf
            $stFiltro = " WHERE evento_desconto_irrf = ".$rsBasesEventoCriado->getCampo('cod_evento');
            $obTFolhaPagamentoConfiguracaoEventosDescontoExterno = new TFolhaPagamentoConfiguracaoEventosDescontoExterno();
            $obTFolhaPagamentoConfiguracaoEventosDescontoExterno->recuperaTodos($rsVerificacao,$stFiltro);

            if ($rsVerificacao->getNumLinhas() > 0) {
                $obErro->setDescricao($stMensagem);

                return $obErro;
            }

            #evento_base_irrf
            $stFiltro = " WHERE evento_base_irrf = ".$rsBasesEventoCriado->getCampo('cod_evento');
            $obTFolhaPagamentoConfiguracaoEventosDescontoExterno->recuperaTodos($rsVerificacao,$stFiltro);

            if ($rsVerificacao->getNumLinhas() > 0) {
                $obErro->setDescricao($stMensagem);

                return $obErro;
            }

            #evento_desconto_previdencia
            $stFiltro = " WHERE evento_desconto_previdencia = ".$rsBasesEventoCriado->getCampo('cod_evento');
            $obTFolhaPagamentoConfiguracaoEventosDescontoExterno->recuperaTodos($rsVerificacao,$stFiltro);

            if ($rsVerificacao->getNumLinhas() > 0) {
                $obErro->setDescricao($stMensagem);

                return $obErro;
            }

            #evento_base_previdencia
            $stFiltro = " WHERE evento_base_previdencia = ".$rsBasesEventoCriado->getCampo('cod_evento');
            $obTFolhaPagamentoConfiguracaoEventosDescontoExterno->recuperaTodos($rsVerificacao,$stFiltro);

            if ($rsVerificacao->getNumLinhas() > 0) {
                $obErro->setDescricao($stMensagem);

                return $obErro;
            }

            // Validando tabela folhapagamento.configuracao_ipe
            $obTFolhaPagamentoConfiguracaoIpe = new TFolhaPagamentoConfiguracaoIpe();

            #cod_evento_base
            $stFiltro = " WHERE cod_evento_base = ".$rsBasesEventoCriado->getCampo('cod_evento');
            $obTFolhaPagamentoConfiguracaoIpe->recuperaTodos($rsVerificacao,$stFiltro);

            if ($rsVerificacao->getNumLinhas() > 0) {
                $obErro->setDescricao($stMensagem);

                return $obErro;
            }

            #cod_evento_automatico
            $stFiltro = " WHERE cod_evento_automatico = ".$rsBasesEventoCriado->getCampo('cod_evento');
            $obTFolhaPagamentoConfiguracaoIpe->recuperaTodos($rsVerificacao,$stFiltro);

            if ($rsVerificacao->getNumLinhas() > 0) {
                $obErro->setDescricao($stMensagem);

                return $obErro;
            }
        }
    }

    // Verifica se sequência de evento é posterior as selecionadas
     if ( trim($stAcao) == "incluir" || trim($stAcao) == "alterar" ) {
        if ($_REQUEST["boInsercaoAutomatica"] == "S") {
            $stFiltro = " WHERE cod_sequencia = ".$_REQUEST['inCodSequencia'];
            $obTFolhaPagamentoSequenciaCalculo = new TFolhaPagamentoSequenciaCalculo();
            $obTFolhaPagamentoSequenciaCalculo->recuperaTodos($rsSequenciaCalculo, $stFiltro);

            $obTFolhaPagamentoSequenciaCalculoEvento = new TFolhaPagamentoSequenciaCalculoEvento();

            foreach ($arEventosCalculoBase as $chave => $codEvento) {
                $stFiltro = " AND evento.cod_evento = ".$codEvento;
                $obTFolhaPagamentoSequenciaCalculoEvento->recuperaRelacionamento($rsSequenciaCalculoEvento, $stFiltro);

                if ($rsSequenciaCalculoEvento->getCampo('sequencia') >= $rsSequenciaCalculo->getCampo('sequencia')) {
                    $obErro->setDescricao("A sequência de cálculo da base deve ser posterior a maior sequência dos eventos que a compôem.");

                    return $obErro;
                }
            }
        }
    }

    return $obErro;
}

function montaCorpoPL()
{
    global $stNomBase;
    $arLinhas = array();

    $arLinhas[] = "\n FUNCTION $stNomBase() RETURNS BOOLEAN as '";
    $arLinhas[] = "\n DECLARE";
    $arLinhas[] = "\n     nuValor         NUMERIC:=0;";
    $arLinhas[] = "\n     nuQuantidade    NUMERIC:=0;";
    $arLinhas[] = "\n     boRetorno       BOOLEAN:=true;";
    $arLinhas[] = "\n     listaEventos    VARCHAR:='''';";
    $arLinhas[] = "\n BEGIN";
    $arLinhas[] = "\n    #listaEventos := pegaListaEventosDaBase(''$stNomBase'');";

    if (trim($_REQUEST["boTipoBase"]) == "V") {
        $arLinhas[] = "\n    #nuValor      := pega0MontaBaseValorFolhas(#listaEventos, ''$stNomBase'');";
    } else {
        $arLinhas[] = "\n    #nuQuantidade := pega0MontaBaseQuantidadeFolhas(#listaEventos, ''$stNomBase'');";
    }

    if ($_REQUEST["boApresetacaoValor"] == "S") {
        $arLinhas[] = "\n    #boRetorno    := gravarEvento(#nuValor, #nuQuantidade);";
    }

    $arLinhas[] = "\n    RETURN #boRetorno;";
    $arLinhas[] = "\n END;";
    $arLinhas[] = "\n ' LANGUAGE 'plpgsql';";

    return $arLinhas;
}

function montaVariavel()
{
    $arLinhas = array();

    $arLinhas[0]["DESCRICAO"] = "listaEventos";
    $arLinhas[0]["TIPO"]      = "2";
    $arLinhas[0]["VALOR"]     = "";

    $arLinhas[1]["DESCRICAO"] = "nuValor";
    $arLinhas[1]["TIPO"]      = "4";
    $arLinhas[1]["VALOR"]     = "0";

    $arLinhas[2]["DESCRICAO"] = "nuQuantidade";
    $arLinhas[2]["TIPO"]      = "4";
    $arLinhas[2]["VALOR"]     = "0";

    $arLinhas[3]["DESCRICAO"] = "boRetorno";
    $arLinhas[3]["TIPO"]      = "3";
    $arLinhas[3]["VALOR"]      = "VERDADEIRO";

    return $arLinhas;
}

function buscaCorpoPL()
{
    $arLinhas = montaCorpoPL();
    $stCorpoPL = "";

    foreach ($arLinhas as $chave => $valor) {
        $stCorpoPL .= $valor;
    }
    $stCorpoPL = str_replace("#", "", $stCorpoPL);

    return $stCorpoPL;
}

function buscaCodigoBiblioteca()
{
    global $inCodModulo;

    $stFiltro  = " WHERE cod_modulo = ".$inCodModulo;
    $stFiltro .= "   AND cod_entidade = ".Sessao::getCodEntidade($boTransacao);
    $stFiltro .= "   AND exercicio = '".Sessao::getExercicio()."'";
    $obTEntidade = new TEntidade();
    $obTEntidade->recuperaBibliotecaEntidade($rsEntidade, $stFiltro);

    if ($rsEntidade->getNumLinhas() > 0) {
        return $rsEntidade->getCampo('cod_biblioteca');
    } else {
        return 2;
    }
}

// Inicia execução da PR
$obErro = validaFormulario();

if ( !$obErro->ocorreu() ) {
    switch ($stAcao) {
        case "incluir":
            $obErroInclusao = new erro;
            Sessao::setTrataExcecao(true);
            $obTransacao = new Transacao;
            
            # incluir função
            if ( !$obErroInclusao->ocorreu() ) {
                $obTAdministracaoFuncao = new TAdministracaoFuncao();
                $obTAdministracaoFuncao->setDado( "cod_modulo"      , $inCodModulo );
                $obTAdministracaoFuncao->setDado( "cod_biblioteca"  , $inCodBiblioteca );
                $obTAdministracaoFuncao->setDado( "nom_funcao"      , $stNomBase );
                $obTAdministracaoFuncao->setDado( "cod_tipo_retorno", "3" );
                $obTAdministracaoFuncao->proximoCod($inCodFuncao);
                $obTAdministracaoFuncao->setDado( "cod_funcao"      , $inCodFuncao );
                $obErroInclusao = $obTAdministracaoFuncao->inclusao($obTransacao);
            }

            # incluir função externa (corpo da funçao)
            if ( !$obErroInclusao->ocorreu() ) {
                $obTAdministracaoFuncaoExterna = new TAdministracaoFuncaoExterna();
                $obTAdministracaoFuncaoExterna->setDado( "cod_modulo"     , $inCodModulo );
                $obTAdministracaoFuncaoExterna->setDado( "cod_biblioteca" , $inCodBiblioteca );
                $obTAdministracaoFuncaoExterna->setDado( "cod_funcao"     , $inCodFuncao );
                $obTAdministracaoFuncaoExterna->setDado( "comentario"     , "Criação automatica da PL, referente ao Manter Bases de Cálculo." );
                $obTAdministracaoFuncaoExterna->setDado( "corpo_pl"       , buscaCorpoPL() );
                $obTAdministracaoFuncaoExterna->setDado( "corpo_ln"       , "" );
                $obErroInclusao = $obTAdministracaoFuncaoExterna->inclusao($obTransacao);
            }

            // variavel
            if ( !$obErroInclusao->ocorreu() ) {
                $arVariaveis = montaVariavel();
                $obTAdministracaoVariavel  = new TAdministracaoVariavel();
                foreach ($arVariaveis as $chave => $dadosVariavel) {
                    $obTAdministracaoVariavel->setDado( "cod_modulo"   , $inCodModulo );
                    $obTAdministracaoVariavel->setDado("cod_biblioteca", $inCodBiblioteca );
                    $obTAdministracaoVariavel->setDado("cod_funcao"    , $inCodFuncao );
                    $obTAdministracaoVariavel->proximoCod( $inCodVariavel);
                    $obTAdministracaoVariavel->setDado("cod_variavel"   , $inCodVariavel );
                    $obTAdministracaoVariavel->setDado("nom_variavel"   , $dadosVariavel["DESCRICAO"] );
                    $obTAdministracaoVariavel->setDado("cod_tipo"       , $dadosVariavel["TIPO"] );
                    $obTAdministracaoVariavel->setDado("valor_inicial"  , $dadosVariavel["VALOR"] );
                    $obErroInclusao = $obTAdministracaoVariavel->inclusao($obTransacao);
                }
            }

            # corpo
            if ( !$obErroInclusao->ocorreu() ) {
                $arCorpoPL = montaCorpoPL();
                $obTAdministracaoCorpoFuncaoExterna = new TAdministracaoCorpoFuncaoExterna();
                $inCodLinha = 0;
                foreach ($arCorpoPL as $chave => $stConteudo) {
                    if ($chave > 6 && $chave < 11) {
                        $stConteudo = str_replace(":=", "<-", $stConteudo);
                        $stConteudo = str_replace("RETURN", "RETORNA", $stConteudo);
                        $stConteudo = str_replace("''", "\"", $stConteudo);

                        if ( strpos($stConteudo, "RETORNA") === false) {
                            // faz nada
                        } else {
                            $stConteudo = str_replace(";", "", $stConteudo);
                        }
                        $inCodLinha++;

                        $obTAdministracaoCorpoFuncaoExterna->setDado("cod_modulo"    , $inCodModulo );
                        $obTAdministracaoCorpoFuncaoExterna->setDado("cod_biblioteca", $inCodBiblioteca );
                        $obTAdministracaoCorpoFuncaoExterna->setDado("cod_funcao"    , $inCodFuncao );
                        $obTAdministracaoCorpoFuncaoExterna->proximoCod( $inCodLinha);
                        $obTAdministracaoCorpoFuncaoExterna->setDado("cod_linha"     , $inCodLinha );
                        $obTAdministracaoCorpoFuncaoExterna->setDado("nivel"         , 0    );
                        $obTAdministracaoCorpoFuncaoExterna->setDado("linha"         , trim($stConteudo) );
                        $obErroInclusao = $obTAdministracaoCorpoFuncaoExterna->inclusao($obTransacao);
                    }
                }
            }

            # inclui base
            $obTFolhaPagamentoBases = new TFolhaPagamentoBases();
            $obTFolhaPagamentoBases->setDado( "nom_base"           , $stNomBase  );
            $obTFolhaPagamentoBases->setDado( "tipo_base"          , $boTipoBase );
            $obTFolhaPagamentoBases->setDado( "apresentacao_valor" , $boApresetacaoValor  );
            $obTFolhaPagamentoBases->setDado( "insercao_automatica", $boInsercaoAutomatica);
            $obTFolhaPagamentoBases->setDado( "cod_modulo"         , $inCodModulo );
            $obTFolhaPagamentoBases->setDado( "cod_biblioteca"     , $inCodBiblioteca  );
            $obTFolhaPagamentoBases->setDado( "cod_funcao"         , $inCodFuncao );
            $obErroInclusao = $obTFolhaPagamentoBases->inclusao($obTransacao);

            # cria função PL no banco
            if ( !$obErroInclusao->ocorreu() ) {
                $stDML  = "CREATE OR REPLACE ";
                $stDML .= buscaCorpoPL();
                $stDML  = str_replace("\'","$$",$stDML);
                $obErroInclusao = $obTFolhaPagamentoBases->executaFuncaoPL( $stDML );

                if ( !$obErroInclusao->ocorreu() ) {
                    $stDML  = " ALTER FUNCTION ".trim($stNomBase)."() OWNER TO  urbem";
                    $obErroInclusao = $obTFolhaPagamentoBases->executaFuncaoPL( $stDML );
                }
            }

            # inclui bases_evento
            if ( !$obErroInclusao->ocorreu() ) {
                $obTFolhaPagamentoBasesEvento = new TFolhaPagamentoBasesEvento();
                $obTFolhaPagamentoBasesEvento->obTFolhaPagamentoBases = &$obTFolhaPagamentoBases;

                foreach ($arEventosCalculoBase as $chave => $inCodEvento) {
                    if ( !$obErroInclusao->ocorreu() ) {
                        # inclui bases_evento
                        $obTFolhaPagamentoBasesEvento->setDado("cod_evento", $inCodEvento);
                        $obErroInclusao = $obTFolhaPagamentoBasesEvento->inclusao($obTransacao);
                    }
                }
            }

            if ($_REQUEST["boInsercaoAutomatica"] == "S") {
                if ( !$obErroInclusao->ocorreu() ) {
                    # grava informações do Evento
                    $obRFolhaPagamentoEvento  = new RFolhaPagamentoEvento;
                    $obRFolhaPagamentoEvento->setCodigo                  ( $_REQUEST['stCodigo'] );
                    $obRFolhaPagamentoEvento->setDescricao               ( $_REQUEST['stDescricaoEventoBase'] );
                    $obRFolhaPagamentoEvento->setSigla                   ( "" );
                    $obRFolhaPagamentoEvento->setNatureza                ( "B" );
                    $obRFolhaPagamentoEvento->setTipo                    ( "B" );
                    $obRFolhaPagamentoEvento->setFixado                  ( $_REQUEST["boTipoBase"] );
                    $obRFolhaPagamentoEvento->setLimiteCalculo           ( false );
                    $obRFolhaPagamentoEvento->setEventoAutomaticoSistema ( $boEventoSistema );
                    $obRFolhaPagamentoEvento->setApresentaParcela        ( false );
                    $obRFolhaPagamentoEvento->setValor                   ( "0" );
                    $obRFolhaPagamentoEvento->setUnidadeQuantitativa     ( "0" );
                    $obRFolhaPagamentoEvento->setObservacao              ( "Evento criado automaticamente pela funcionalida Bases de Cálculo." );
                    $obRFolhaPagamentoEvento->obRFolhaPagamentoSequencia->setCodSequencia( $_REQUEST['inCodSequencia'] );

                    # Relacionando o evento as folhas: salário, Férias, Décimo e Rescição
                    $obTFolhaPagamentoConfiguracaoEvento = new TFolhaPagamentoConfiguracaoEvento();
                    $obTFolhaPagamentoConfiguracaoEvento->recuperaTodos($rsConfiguracaoEvento);

                    # busca todos Regime/Subdivisões
                    $obTPessoalSubDivisao = new TPessoalSubDivisao();
                    $obTPessoalSubDivisao->recuperaTodos( $rsRegimeSubDivisao );

                    # Cargos todos cargos vinculados
                    $obTPessoalCargo = new TPessoalCargo();
                    $obTPessoalCargo->recuperaTodos( $rsCargos );

                    $obTPessoalEspecialidade = new TPessoalEspecialidade();

                    while ( !$rsConfiguracaoEvento->eof() ) {
                        $obRFolhaPagamentoEvento->addConfiguracaoEvento();
                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao ( $rsConfiguracaoEvento->getCampo('cod_configuracao') );
                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();

                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFuncao->setCodFuncao                          ( $inCodFuncao );
                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFuncao->obRBiblioteca->setCodigoBiblioteca    ( $inCodBiblioteca );
                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFuncao->obRBiblioteca->roRModulo->setCodModulo( $inCodModulo );
                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setDescricao( $stNomBase );
                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setProporcaoAdiantamento( true );
                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setProporcaoAbono( true );

                        # Subdivisao de cada Caso
                        $arCodSubDivisao = array();
                        while ( !$rsRegimeSubDivisao->eof() ) {
                            $arCodSubDivisao[] = $rsRegimeSubDivisao->getCampo("cod_sub_divisao");
                            $rsRegimeSubDivisao->proximo();
                        }
                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->arCodSubDivisao = $arCodSubDivisao;

                        # Cargo de cada caso
                        $inCodCargoTmp = 0;
                        $arCodCargoCodEspecialidade = array();

                        while ( !$rsCargos->eof() ) {
                            $inCodCargo = $rsCargos->getCampo("cod_cargo");

                            $stCondicao = " WHERE cod_cargo = ".$inCodCargo;
                            $obTPessoalEspecialidade->recuperaTodos($rsEspecialidades, $stCondicao);

                            while ( !$rsEspecialidades->eof() ) {
                                $inCodEspecialidade = $rsCargos->getCampo("cod_especialidade");

                                if ($inCodEspecialidade != "") {
                                    $arCodCargoCodEspecialidade[] = $inCodCargo."-".$inCodEspecialidade;
                                } else {
                                    $arCodCargoCodEspecialidade[] = $inCodCargo."-0";
                                }
                                $rsEspecialidades->proximo();
                            }
                            $rsCargos->proximo();
                        }
                        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->arCodCargoCodEspecialidade = $arCodCargoCodEspecialidade;
                        $rsConfiguracaoEvento->proximo();
                    }
                }

                if ($stAcao == "incluir") {
                    $obErroInclusao = $obRFolhaPagamentoEvento->incluirEvento( $obTransacao );

                    if ( !$obErroInclusao->ocorreu() ) {
                        // inclui bases_evento_criado
                        $obTFolhaPagamentoBasesEventoCriado = new TFolhaPagamentoBasesEventoCriado();
                        $obTFolhaPagamentoBasesEventoCriado->obTFolhaPagamentoBases = &$obTFolhaPagamentoBases;
                        $obTFolhaPagamentoBasesEventoCriado->setDado("cod_evento", $obRFolhaPagamentoEvento->getCodEvento());
                        $obErroInclusao = $obTFolhaPagamentoBasesEventoCriado->inclusao($obTransacao);
                    }
                }
            }

            Sessao::encerraExcecao();
            if ( !$obErroInclusao->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm.$stLink,"Base: ".$stNomBase,"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErroInclusao->getDescricao()),"n_incluir","erro");
            }
            break;

        case "alterar":
            $obErroAlteracao = new erro;
            Sessao::setTrataExcecao(true);

            $stFiltro = " WHERE cod_base = ".$inCodBase;
            $obTFolhaPagamentoBases = new TFolhaPagamentoBases();
            $obTFolhaPagamentoBases->recuperaTodos( $rsBases, $stFiltro );
            $boInsercaoAutomatica = $rsBases->getCampo('insercao_automatica') == 'f' ? 'N' : 'S';

            if ($boInsercaoAutomatica == "S") {
                $stFiltro = " WHERE cod_base = ".$inCodBase;
                $boTFolhaPagamentoBasesEventoCriado = new TFolhaPagamentoBasesEventoCriado();
                $boTFolhaPagamentoBasesEventoCriado->recuperaTodos( $rsBasesEventoCriado, $stFiltro );

                $obTFolhaPagamentoSequenciaCalculoEvento = new TFolhaPagamentoSequenciaCalculoEvento();
                $obTFolhaPagamentoSequenciaCalculoEvento->setDado("cod_evento"   , $rsBasesEventoCriado->getCampo('cod_evento'));
                $obErroAlteracao = $obTFolhaPagamentoSequenciaCalculoEvento->exclusao();

                if ( !$obErroAlteracao->ocorreu() ) {
                    $obTFolhaPagamentoSequenciaCalculoEvento->setDado("cod_evento"   , $rsBasesEventoCriado->getCampo('cod_evento'));
                    $obTFolhaPagamentoSequenciaCalculoEvento->setDado("cod_sequencia", $_REQUEST['inCodSequencia']);
                    $obErroAlteracao = $obTFolhaPagamentoSequenciaCalculoEvento->inclusao();
                }
            }

            if ( !$obErroAlteracao->ocorreu() ) {
                $obTFolhaPagamentoBasesEvento = new TFolhaPagamentoBasesEvento();
                $obTFolhaPagamentoBasesEvento->setDado("cod_base", $inCodBase);

                foreach ($arEventosCalculoBase as $chave => $inCodEvento) {
                    if ( !$obErroAlteracao->ocorreu() ) {
                        // inclui bases_evento
                        $obTFolhaPagamentoBasesEvento->setDado("cod_evento", $inCodEvento);
                        $obErroAlteracao = $obTFolhaPagamentoBasesEvento->inclusao();
                    }
                }
            }

            Sessao::encerraExcecao();
            if ( !$obErroAlteracao->ocorreu() ) {
                SistemaLegado::alertaAviso($pgList.$stLink,"Base: ".$stNomBase,"alterar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErroAlteracao->getDescricao()),"n_alterar","erro");
            }
            break;

        case "excluir":
            $obErroExclusao = new erro;
            $obTransacao = new Transacao;
            $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

            $stFiltro = " WHERE cod_base = ".$inCodBase;
            $obTFolhaPagamentoBases = new TFolhaPagamentoBases();
            $obTFolhaPagamentoBases->recuperaTodos( $rsBases, $stFiltro, '', $boTransacao );

            $inCodFuncao          = $rsBases->getCampo('cod_funcao');
            $boInsercaoAutomatica = $rsBases->getCampo('insercao_automatica') == 'f' ? 'N' : 'S';
            $stNomBase            = $rsBases->getCampo('nom_base');

            $stFiltro = " WHERE cod_base = ".$inCodBase;
            $boTFolhaPagamentoBasesEventoCriado = new TFolhaPagamentoBasesEventoCriado();
            $boTFolhaPagamentoBasesEventoCriado->recuperaTodos( $rsBasesEventoCriado, $stFiltro, '', $boTransacao );

            # Exclui lista de eventos da base
            if ( !$obErroExclusao->ocorreu() ) {
                $obTFolhaPagamentoBasesEvento = new TFolhaPagamentoBasesEvento();
                $obTFolhaPagamentoBasesEvento->obTFolhaPagamentoBases = &$obTFolhaPagamentoBases;
                $obTFolhaPagamentoBasesEvento->setDado("cod_base"  , $inCodBase);
                $obErroExclusao = $obTFolhaPagamentoBasesEvento->exclusao($boTransacao);
            }

            # Dropa função PL no banco
            if ( !$obErroExclusao->ocorreu() ) {
                $stDML  = "DROP FUNCTION ".$rsBases->getCampo('nom_base')."();";
                $obErroExclusao = $obTFolhaPagamentoBases->executaFuncaoPL( $stDML, $boTransacao );
            }

            # exclui bases evento criado
            if ( !$obErroExclusao->ocorreu() ) {
                $obTFolhaPagamentoBasesEventoCriado = new TFolhaPagamentoBasesEventoCriado();
                $obTFolhaPagamentoBasesEventoCriado->setDado("cod_base", $inCodBase);
                $obErroExclusao = $obTFolhaPagamentoBasesEventoCriado->exclusao($boTransacao);
            }

            # Exclui base
            if ( !$obErroExclusao->ocorreu() ) {
               $obTFolhaPagamentoBases->setDado( "cod_base", $inCodBase );
               $obErroExclusao = $obTFolhaPagamentoBases->exclusao($boTransacao);
            }

            # Exclui corpo da função
            if ( !$obErroExclusao->ocorreu() ) {
                $stCondicao  = " WHERE cod_modulo = ".$inCodModulo;
                $stCondicao .= "   AND cod_biblioteca = ".$inCodBiblioteca;
                $stCondicao .= "   AND cod_funcao = ".$inCodFuncao;

                $obTAdministracaoCorpoFuncaoExterna = new TAdministracaoCorpoFuncaoExterna();
                $obTAdministracaoCorpoFuncaoExterna->recuperaTodos($rsCorpoFuncaoExterna, $stCondicao, '', $boTransacao);

                while ( !$rsCorpoFuncaoExterna->eof() ) {
                    if ( !$obErroExclusao->ocorreu() ) {
                        $obTAdministracaoCorpoFuncaoExterna->setDado("cod_modulo"    , $inCodModulo );
                        $obTAdministracaoCorpoFuncaoExterna->setDado("cod_biblioteca", $inCodBiblioteca );
                        $obTAdministracaoCorpoFuncaoExterna->setDado("cod_funcao"    , $inCodFuncao );
                        $obTAdministracaoCorpoFuncaoExterna->setDado("cod_linha"     , $rsCorpoFuncaoExterna->getCampo('cod_linha'));
                        $obErroExclusao = $obTAdministracaoCorpoFuncaoExterna->exclusao($boTransacao);
                    }
                    $rsCorpoFuncaoExterna->proximo();
                }
            }

            # Exclui variavel
            if ( !$obErroExclusao->ocorreu() ) {
                $stCondicao  = " WHERE cod_modulo = ".$inCodModulo;
                $stCondicao .= "   AND cod_biblioteca = ".$inCodBiblioteca;
                $stCondicao .= "   AND cod_funcao = ".$inCodFuncao;

                $obTAdministracaoVariavel = new TAdministracaoVariavel();
                $obTAdministracaoVariavel->recuperaTodos($rsVariavel, $stCondicao, '', $boTransacao);

                while ( !$rsVariavel->eof() ) {
                    if ( !$obErroExclusao->ocorreu() ) {
                        $obTAdministracaoVariavel->setDado( "cod_modulo"   , $inCodModulo );
                        $obTAdministracaoVariavel->setDado("cod_biblioteca", $inCodBiblioteca );
                        $obTAdministracaoVariavel->setDado("cod_funcao"    , $inCodFuncao );
                        $obTAdministracaoVariavel->setDado("cod_variavel"  , $rsVariavel->getCampo('cod_variavel') );
                        $obErroExclusao = $obTAdministracaoVariavel->exclusao($boTransacao);
                    }
                    $rsVariavel->proximo();
                }
            }

            # Excluir função externa (corpo da funçao)
            if ( !$obErroExclusao->ocorreu() ) {
                $obTAdministracaoFuncaoExterna = new TAdministracaoFuncaoExterna();
                $obTAdministracaoFuncaoExterna->setDado( "cod_modulo"     , $inCodModulo );
                $obTAdministracaoFuncaoExterna->setDado( "cod_biblioteca" , $inCodBiblioteca );
                $obTAdministracaoFuncaoExterna->setDado( "cod_funcao"     , $inCodFuncao );
                $obErroExclusao = $obTAdministracaoFuncaoExterna->exclusao($boTransacao);
            }

            # Excluir evento
            if (trim($boInsercaoAutomatica)=="S") {
                $obRFolhaPagamentoEvento  = new RFolhaPagamentoEvento;
                $obRFolhaPagamentoEvento->setCodEvento( $rsBasesEventoCriado->getCampo('cod_evento') );
                $obErroExclusao = $obRFolhaPagamentoEvento->excluirEvento( $boTransacao );
            }

            # Excluir função
            if ( !$obErroExclusao->ocorreu() ) {
                $obTAdministracaoFuncao = new TAdministracaoFuncao();
                $obTAdministracaoFuncao->setDado( "cod_modulo"      , $inCodModulo );
                $obTAdministracaoFuncao->setDado( "cod_biblioteca"  , $inCodBiblioteca );
                $obTAdministracaoFuncao->setDado( "cod_funcao"      , $inCodFuncao );
                $obErroExclusao = $obTAdministracaoFuncao->exclusao($boTransacao);
            }

            if ( !$obErroExclusao->ocorreu() ) {
                $obTransacao->commitAndClose();
                SistemaLegado::alertaAviso($pgList.$stLink,$stNomBase,"excluir","aviso", Sessao::getId(), "../");
            } else {
                $obTransacao->rollbackAndClose();
                SistemaLegado::alertaAviso($pgForm.$stLink,urlencode($obErroExclusao->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
            }
            break;
    }
} else {
    if (trim($stAcao) == "excluir") {
        SistemaLegado::alertaAviso($pgList.$stLink, urlencode($obErro->getDescricao()), "n_$stAcao", "erro", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), "n_$stAcao", "erro");
    }
}

?>

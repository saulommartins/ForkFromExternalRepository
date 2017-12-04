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
    * Página de processamento oculto para o cadastro de Convenio
    * Data de Criação   : 04/11/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: OCManterConvenio.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.05.04
*/

/*
$Log$
Revision 1.14  2007/02/07 15:57:26  cercato
alteracoes para o convenio trabalhar com numero de variacao.

Revision 1.13  2006/09/15 14:57:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"           );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"         );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPermissao.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php"         );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"              );

include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );
$obRMONAgencia = new RMONAgencia;
$obRMONConta = new RMONContaCorrente;

$obRMONAgencia->obRMONBanco->listarBanco($rsBanco);
$obRMONAgencia->listarAgencia($rsAgencia);

//--------------------------------------------------------- FUNCOES
function montaListaContas($rsListaContas, $boRetorna = false)
{
    if ( $rsListaContas->getNumLinhas() > 0 ) {

        $obLista = new Lista;
        $obLista->setRecordSet                 (   $rsListaContas   );
        $obLista->setTitulo                    ( "Lista de Contas Correntes"  );
        $obLista->setMostraPaginacao           ( false                  );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Conta Corrente"       );
        $obLista->ultimoCabecalho->setWidth    ( 80                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Variação"             );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "num_conta_corrente"   );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "num_variacao"         );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirConta();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice","num_conta_corrente" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );

    } else {
        $stHTML = "&nbsp";
    }

    $js .= "d.getElementById('spnListaConta').innerHTML = '".$stHTML."';\n";
    $js .= "f.inNumConta.value ='';\n";
    $js .= "f.inNumConta.focus();\n";

    sistemaLegado::executaFrameOculto($js);

}

// SELECIONA ACAO
switch ($_REQUEST ["stCtrl"]) {
    case 'VerificaTaxa':
        if ($_REQUEST["flTaxaBancaria"] == '0,00') {
            $js .= "f.flTaxaBancaria.value=''; \n";
            sistemaLegado::executaFrameOculto($js);
        }
        break;

    // funcao de busca da conta - verificar se a conta pertence a agencia pré-selecionada
    case "buscaConta":
        $obRMONConta->setNumeroConta  ( $_REQUEST['inNumConta'] );
        $rsLista = new RecordSet;

        if ($_REQUEST['inNumConta']) {
            $obRMONConta->existeContaCorrente( $rsLista );
            if ( $rsLista->getNumLinhas() < 1 ) {
                $stJs .= "f.inNumConta.value ='';\n";
                $stJs .= "f.inNumConta.focus();\n";
                $stJs .= "alertaAviso('@Conta Corrente inexistente (".$_REQUEST["inNumConta"].")','form','erro','".Sessao::getId()."');";
            } else {
                $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inCodBancoTxt"] );
                $obRMONAgencia->setNumAgencia( $_REQUEST['stNumAgencia'] );
                $obRMONAgencia->consultarAgencia( $rsAgencia );
                $CodBancoAtual = $obRMONAgencia->obRMONBanco->getCodBanco();
                $CodAgenciaAtual = $obRMONAgencia->getCodAgencia();
                if ( ($_REQUEST['stNumAgencia'] == '') || ( $_REQUEST['inCodBanco'] == '' ) ) {
                    //campos estao vazios
                    $stJs .= "f.inNumConta.value ='';\n";
                    $stJs .= "f.inCodBanco.focus();\n";
                    $stJs .= "alertaAviso('@Selecione primeiramente o Banco e a Agência', 'form','erro','".Sessao::getId()."');";
                } else {
                    if ( $CodAgenciaAtual != $rsLista->getCampo ('cod_agencia')) {
                        $stJs .= "f.inNumConta.value ='';\n";
                        $stJs .= "f.inNumConta.focus();\n";
                        $stJs .= "alertaAviso('@A conta selecionada não pertence à Agência', 'form','erro','".Sessao::getId()."');";
                } else {
                    if ( $CodBancoAtual != $rsLista->getCampo ('cod_banco')) {
                            $stJs .= "f.inNumConta.value ='';\n";
                            $stJs .= "f.inNumConta.focus();\n";
                            $stJs .= "alertaAviso('@A conta selecionada não pertence à Agência', 'form','erro','".Sessao::getId()."');";
                        }
                    }
                }
            }
        }
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "recuperaContas":
        include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
        include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );
        $obRMONConta = new RMONContaCorrente;
        $obRMONConvenio = new RMONConvenio;

        $rsContas = new RecordSet;

        $arNewConvenio = $_REQUEST['inCodConvenio'];
        $obRMONConvenio->setCodigoConvenio ( $arNewConvenio );

        //fazer funcao de busca de contas
        $obRMONConvenio->listarConvenioContas($rsContas);

        if ( $rsContas->getNumLinhas () > 0 ) {
            $rsContas->ordena ('num_conta_corrente');
            $nregistros = $rsContas->getNumLinhas();
            $cont =0;
            $arContasSessao = Sessao::read( "contas" );
            while ($cont < $nregistros) {
                $stInsere = false;
                if ($arContasSessao) {
                    $inCountSessao = count ( $arContasSessao );
                } else {
                    $inCountSessao = 0;
                    $stInsere = true;
                }

                for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
                    if ( $arContasSessao[$iCount]['cod_conta_corrente'] == $rsContas->getCampo('cod_conta_corrente') ) {
                        $stInsere = false;
                        $iCount = $inCountSessao;
                    } else {
                        $stInsere = true;
                    }
                }

                if ($stInsere) {
                    if ($arContasSessao) {
                        $inLast = count ($arContasSessao);
                    } else {
                        $inLast = 0;
                        $arContasSessao = array();
                    }

                    $arContasSessao[$inLast]['cod_conta_corrente'] = $rsContas->getCampo ('cod_conta_corrente');
                    $arContasSessao[$inLast]['num_conta_corrente'] = $rsContas->getCampo ('num_conta_corrente');
                    $arContasSessao[$inLast]['num_agencia']        = $rsContas->getCampo ('num_agencia');
                    $arContasSessao[$inLast]['num_banco']          = $rsContas->getCampo ('num_banco');
                    $arContasSessao[$inLast]['num_variacao']       = $rsContas->getCampo ('variacao');
                }

                $rsContas->proximo();
                $cont++;
            }
        }

        Sessao::write( "contas", $arContasSessao );
        $rsListaContas = new RecordSet;
        if ( count ( $arContasSessao > 0 ) ) {
            $rsListaContas->preenche ( $arContasSessao );
            $rsListaContas->ordena("num_conta_corrente");
        }

        montaListaContas ( $rsListaContas );
        break;

    case "incluirConta":
        include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
        include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );

        $obRMONConta = new RMONContaCorrente;
        $obRMONConvenio = new RMONConvenio;
        if (!$_REQUEST['stNumAgencia']) {
            $stJs .= "f.inNumConta.value ='';\n";
            $stJs .= "f.inCodBancoTxt.focus();\n";
            $stJs .= "alertaAviso('@Selecione primeiramente o Banco e a Agência','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
        } else {
            $newConvenio = $_REQUEST['inCodConvenio'];
            $newNumeroConta = $_REQUEST['inNumConta'];
            $NumAgencia = $_REQUEST['stNumAgencia'];

            $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inCodBancoTxt"] );
            $obRMONAgencia->setNumAgencia( $NumAgencia );
            $obRMONAgencia->consultarAgencia( $rsAgencia );

            $CodAgencia = $obRMONAgencia->getCodAgencia();

            $obRMONConvenio->setCodigoConvenio  ( $newConvenio );
            $obRMONConvenio->setNumeroConta     ( $newNumeroConta );
            $obRMONConta->setNumeroConta        ( $newNumeroConta );

            $rsLista = new Lista;
            $obRMONConta->existeContaCorrente( $rsLista );

            if ( $rsLista->getNumLinhas() < 1  ) { /* ACRESCIMO INEXISTENTE';*/
                $stJs .= "f.inNumConta.value ='';\n";
                $stJs .= "f.inNumConta.focus();\n";
                $stJs .= "alertaAviso('@Conta Corrente informada não existe. (". $newNumeroConta .")','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
            } else {
                $obRMONConvenio->setCodigoConta ( $rsLista->getCampo ('cod_conta_corrente'));
                if ( $CodAgencia != $rsLista->getCampo ( 'cod_agencia' ) ) {
                    $stJs .= "f.inNumConta.value ='';\n";
                    $stJs .= "f.inNumConta.focus();\n";
                    $stJs .= "alertaAviso('@Conta Corrente não pertence ao banco informado. (". $newNumeroConta .")','form','erro','".Sessao::getId()."');";
                    sistemaLegado::executaFrameOculto( $stJs );
                } else {
                    $arContasSessao = Sessao::read( "contas" );
                    $nregistros = count ( $arContasSessao );
                    if ($nregistros > 0) {
                        if (!$_REQUEST["inNumVariacao"]) {
                            $stJs = "alertaAviso('@Conta Corrente não possuí variação. (".$newNumeroConta.")', 'form', 'erro','".Sessao::getId()."');";
                            sistemaLegado::executaFrameOculto( $stJs );
                            exit;
                        }

                        $cont = 0;
                        while ($cont < $nregistros) {
                            if ( ($arContasSessao[$cont]['num_agencia'] != $_REQUEST['stNumAgencia']) || ($arContasSessao[$cont]['num_banco'] != $_REQUEST["inCodBancoTxt"]) ) {
                                $arContasSessao = array();
                                Sessao::write( "contas", $arContasSessao );
                                $nregistros = 0;
                                break;
                            }else
                            if ($arContasSessao[$cont]['num_variacao'] == $_REQUEST["inNumVariacao"]) {
                                $stJs = "alertaAviso('@Lista de Contas Corrente já possuí a variação. (".$_REQUEST["inNumVariacao"].")', 'form', 'erro','".Sessao::getId()."');";
                                sistemaLegado::executaFrameOculto( $stJs );
                                exit;
                            }else
                            if (!$arContasSessao[$cont]['num_variacao']) {
                                $stJs = "alertaAviso('@Lista de Contas Corrente não possuí variação.', 'form', 'erro','".Sessao::getId()."');";
                                sistemaLegado::executaFrameOculto( $stJs );
                                exit;
                            }else
                            if ( $arContasSessao[$cont]['num_conta_corrente'] == $obRMONConvenio->getNumeroConta () ) {
                                //INCLUIR CONTA CORRENTE -> Conta Corrente jah existente
                                $obErro = new Erro;
                                $obErro->setDescricao("A Conta Corrente ". $newConta." já está na lista de contas vinculadas a este convênio!");
                                $js .= "alertaAviso('@".$obErro->getDescricao()."','form','erro', '".Sessao::getId()."');";
                                $js .= "f.inNumVariacao.value = '';\n";
                                $js .= "f.inCodConta.value = '';\n";
                                $js .= "f.inCodConta.focus();\n";

                                sistemaLegado::executaFrameOculto( $js );
                                exit;
                            }else
                                $cont++;
                        }
                    }

                    $arContasSessao[$nregistros]['num_agencia'] = $_REQUEST['stNumAgencia'];
                    $arContasSessao[$nregistros]['num_banco'] = $_REQUEST["inCodBancoTxt"];
                    $arContasSessao[$nregistros]['cod_conta_corrente'] = $obRMONConvenio->getCodigoConta ();
                    $arContasSessao[$nregistros]['num_conta_corrente'] = $obRMONConvenio->getNumeroConta ();
                    $arContasSessao[$nregistros]['num_variacao'] = $_REQUEST["inNumVariacao"];
                    Sessao::write( "contas", $arContasSessao );
                }//fim se a conta pertence a agencia pre-selecionada
            }//fim se achou registros

            $rsListaContas = new RecordSet;
            $rsListaContas->preenche ( $arContasSessao );
            $rsListaContas->ordena("num_conta_corrente");
            montaListaContas ( $rsListaContas );

            $js .= " d.getElementById('stConta').innerHTML = '&nbsp;';\n";
            $js .= "f.inCodConta.value = '';\n";
            $js .= "f.inNumVariacao.value = '';\n";
            $js .= "f.inCodConta.focus();\n";
            SistemaLegado::executaFrameOculto($js);
            exit(0);
        }//fim se tem agencia escolhida
        break;

    case "excluirConta":
        $arTmpAtividade = array ();
        $arContasSessao = Sessao::read( "contas" );
        $inCountSessao = count ( $arContasSessao );

        $inCountArray = 0;
        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
            if ($arContasSessao[$inCount][ "num_conta_corrente" ] != $_REQUEST[ "inIndice" ]) {
                $arTmpAtividade[$inCountArray]["cod_conta_corrente"]  = $arContasSessao[$inCount][ "cod_conta_corrente"  ];
                $arTmpAtividade[$inCountArray]["num_conta_corrente"]  = $arContasSessao[$inCount][ "num_conta_corrente"  ];
                $arTmpAtividade[$inCountArray]["num_variacao"]        = $arContasSessao[$inCount][ "num_variacao"  ];
                $inCountArray++;
            }
        }

        Sessao::write( "contas", $arTmpAtividade );

        $rsListaAtividades = new RecordSet;
        $rsListaAtividades->preenche ( $arTmpAtividade );
        $rsListaAtividades->ordena("num_conta_corrente");
        montaListaContas( $rsListaAtividades );
        break;

    case "buscaConvenioBanco":
        if ( (!$_REQUEST['inCodBancoTxt']) || (!$_REQUEST['inNumConvenio']) ) {
            $stJs .= "f.inNumConvenio.value ='';\n";
            $stJs .= "f.inNumConvenio.focus();\n";
            $stJs .= "alertaAviso('@"."Selecione primeiramente a Agência Bancária"."','form', 'erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
        } else {
            //deve-se fazer uma busca pelo codigo do convenio antes..
            include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );
            $obRMONConvenio = new RMONConvenio;
            $obRMONConvenio->setNumeroConvenio ( $_REQUEST['inNumConvenio'] );
            $rsConvenio = new Lista;
            $obRMONConvenio->verificaConvenioBanco ( $rsConvenio );
            if ( $rsConvenio->getNumLinhas() > 0 ) {
                //convenio foi buscado com sucesso, faça:
                $obRMONConvenio = new RMONConvenio;

                $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inCodBancoTxt"] );
                $obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );

                $obRMONConvenio->setCodigoBanco    ( $rsBanco->getCampo("cod_banco") );
                $obRMONConvenio->setCodigoConvenio ( $rsConvenio->getCampo('cod_convenio') );

                $rsLista = new Lista;
                $obRMONConvenio->listarConvenioBanco ( $rsLista );

                if ( $rsLista->getNumLinhas() > 0 ) {
                    //JAH TEM UM CONVENIO DESSE CADASTRADO
                    $JaTemComBanco = $rsLista->getCampo ('cod_banco');
                    // $JaTemComAgencia = $rsLista->getCampo ('cod_agencia');
                    if ( $rsBanco->getCampo("cod_banco") == $JaTemComBanco ) {
                        //nao pode o mesmo numero de convenio para o mesmo banco
                        $stJs .= "f.inNumConvenio.value ='';\n";
                        $stJs .= "f.inNumConvenio.focus();\n";
                        $stJs .= "alertaAviso('@"."Convênio já cadastrado para o Banco informado! (". $_REQUEST['inNumConvenio'] .") "."','form', 'erro','".Sessao::getId()."');";
                        sistemaLegado::executaFrameOculto( $stJs );
                    }
                }
            }
        }
        break;

    case "limparContas":
        $stJs .= "f.inNumConta.value ='';\n";
        $stJs .= "f.inNumVariacao.value ='';\n";
        $stJs .= "f.inNumConta.focus();\n";
        sistemaLegado::executaFrameOculto( $stJs );

        $rsListaContas = new RecordSet;
        $rsListaContas->preenche ( Sessao::read( "contas" ) );
        montaListaContas( $rsListaContas );
        break;

    case "limpaTudo":
        Sessao::write( "contas", array() );
        $lista = new RecordSet;
        montaListaContas ( $lista );
        break;
}

SistemaLegado::executaFrameOculto($stJs);
?>

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
    * Página Oculto de responsáveis por adiantamento
    * Data de Criação   : 16/11/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * Casos de uso : uc-02.03.32
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include mapeamentos
include_once( TEMP."TEmpenhoResponsavelAdiantamento.class.php");
include_once( TEMP."TEmpenhoContraPartidaResponsavel.class.php" );

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];

$stPrograma = "ManterResponsaveisAdiantamento";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

function verificaFornecedor($inCGM, $inCodContrapartida)
{
  $boBloqueia = false;
  // Verifica se o responsavel está cadastrado na base
  $obTEmpenhoResponsavel = new TEmpenhoResponsavelAdiantamento();
  $obTEmpenhoResponsavel->setDado( 'exercicio'          , Sessao::getExercicio()  );
  $obTEmpenhoResponsavel->setDado( 'conta_contrapartida', $inCodContrapartida );
  $obTEmpenhoResponsavel->setDado( 'numcgm'             , $inCGM              );
  $obTEmpenhoResponsavel->recuperaPorChave( $rsResponsavel );

  if ($rsResponsavel->getNumLinhas() > 0 ) {

    // Verifica se o responsavel esta sendo utilizado pelo sistema
    $obTEmpenhoResponsavel->verificaExistenciaEmpenho( $rsVerificaEmpenho );
    if ($rsVerificaEmpenho->getNumLinhas() > 0) {
        $boBloqueia = true;
    }

  }

  return $boBloqueia;

}

function verificaContaLancamento($inCGM, $inContaLancamento)
{
    $boBloqueia = false;
    // Verifica se a conta lancamento esta cadastrada para outro responsavel
    $obTEmpenhoResponsavel = new TEmpenhoResponsavelAdiantamento();
    $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND numcgm != ".$inCGM." AND conta_lancamento = ".$inContaLancamento."";
    $obTEmpenhoResponsavel->recuperaTodos( $rsResponsavel, $stFiltro );

    if ($rsResponsavel->getNumLinhas() > 0 ) {
        $boBloqueia = true;
    }

    return $boBloqueia;

}

switch ($_REQUEST['stCtrl']) {

    case 'carregaResponsavelAdiantamento' :
        $arValores = array();
        if ($_REQUEST['inCodContraPartida']) {

            $inCount                            = 0;
            $rsRecordSetResponsavel             = new RecordSet();
            $rsRecordSetAdiantamento            = new RecordSet();
            $obTEmpenhoContraPartidaResponsavel = new TEmpenhoContraPartidaResponsavel();
            $obTEmpenhoResponsavelAdiantamento  = new TEmpenhoResponsavelAdiantamento();

            $stFiltro = "   AND contrapartida_responsavel.conta_contrapartida = ".$_REQUEST['inCodContraPartida']." \n";
            $stFiltro.= "   AND contrapartida_responsavel.exercicio         = '".Sessao::getExercicio()."'              \n";
            $obTEmpenhoContraPartidaResponsavel->recuperaContraPartidaResponsavel($rsRecordSetResponsavel,$stFiltro);

            if ($rsRecordSetResponsavel->getNumLinhas() > 0 ) {
                $stJs = "f.inPrazo.value            = '".$rsRecordSetResponsavel->getCampo('prazo')."';               ";
                $stJs.= "f.inPrazo.disabled         = true;                                                           ";
                $stJs.= "f.inCodContraPartida.value = '".$rsRecordSetResponsavel->getCampo('conta_contrapartida')."'; ";
                $stJs.= "d.getElementById('innerContraPartida').innerHTML = '".$rsRecordSetResponsavel->getCampo('descricao')."';    ";
                $stJs.= "f.inCGM.focus();                                                                             ";

                $stFiltro =" AND responsavel_adiantamento.conta_contrapartida = ".$_REQUEST['inCodContraPartida']."                                \n";
                $stFiltro.=" AND responsavel_adiantamento.exercicio           = '".$rsRecordSetResponsavel->getCampo('exercicio')."' \n";
                $obTEmpenhoResponsavelAdiantamento->recuperaResponsavelAdiantamento($rsRecordSetAdiantamento,$stFiltro);

                if (!($rsRecordSetAdiantamento->EOF())) {
                    while (!$rsRecordSetAdiantamento->EOF()) {
                        ($rsRecordSetAdiantamento->getCampo("ativo")=='t')? $situacao = "A" : $situacao = "I";

                         $arValores[$inCount]['id'                  ]=$inCount + 1;
                         $arValores[$inCount]['inCGM'               ]=$rsRecordSetAdiantamento->getCampo("numcgm");
                         $arValores[$inCount]['stNomCGM'            ]=$rsRecordSetAdiantamento->getCampo("nom_cgm");
                         $arValores[$inCount]['inCodContaLancamento']=$rsRecordSetAdiantamento->getCampo("conta_lancamento");
                         $arValores[$inCount]['inCodContraPartida']=$rsRecordSetAdiantamento->getCampo("conta_contrapartida");
                         $arValores[$inCount]['inPrazo'             ]=$rsRecordSetResponsavel->getCampo('prazo');
                         $arValores[$inCount]['inCodSituacao'       ]=$situacao;

                         $inCount++;
                         $rsRecordSetAdiantamento->proximo();
                    }

                    $stJs.= "f.inCodContraPartida.disabled = true;                                                    ";
                    $stJs.= "d.getElementById('btContraPartida').style.display = 'none';                              ";
               }

            } else {

                $stJs.= "f.inPrazo.disabled = false;";
                $stJs.= "f.inPrazo.value    = '';   ";
                $stJs.= "f.inPrazo.focus();         ";
            }

            Sessao::write('arValores', $arValores);
            echo $stJs.montaListaResponsaveis($arValores);
        }
    break;

    case 'carregaListaResponsaveis' :
        echo montaListaResponsaveis(Sessao::read('arValores'));
    break;

    case 'alterarListaResponsaveis':

        $arValores = Sessao::read('arValores');
        $inCount            = $_REQUEST['id']-1;
        $inCodContrapartida = $arValores[$inCount]['inCodContraPartida'];
        $inCGM              = $arValores[$inCount]['inCGM'];

        $boBloqueia = verificaFornecedor( $inCGM, $inCodContrapartida );

        if ($boBloqueia) {

            $js.="f.inCGM.disabled                                      = true;     ";
            $js.="f.inCodContaLancamento.disabled                       = true;     ";
            $js.="d.getElementById('btCredor').style.display            = 'none';   ";
            $js.="d.getElementById('btContaLancamento').style.display   = 'none';   ";
        } else {
            $js.="f.inCGM.disabled                                      = false;     ";
            $js.="f.inCodContaLancamento.disabled                       = false;     ";
            $js.="d.getElementById('btCredor').style.display            = 'inline';   ";
            $js.="d.getElementById('btContaLancamento').style.display   = 'inline';   ";
        }

        $js.="f.inCGM.value                               = '".$inCGM."';                 ";
        $js.="f.inCodContaLancamento.value                = '".$arValores[$inCount]['inCodContaLancamento']."'; ";
        $js.="f.HdnCodResponsavel.value                   = '".$_REQUEST['id']."';                                           ";
        $js.="f.HdnNomResponsavel.value                   = '".$arValores[$inCount]['stNomCGM']."';             ";

        $arValores[$inCount]['inCodSituacao'] == 'A' ? $js.="f.SituacaoS.checked=true;" : $js.="f.SituacaoN.checked=true;";

        Sessao::write('arValores', $arValores);
        $js.="f.stCtrl.value                              = 'alteradoListaResponsaveis';                                     ";
        $js.="d.getElementById('incluiResponsavel').value = 'Alterar';                                                       ";
        $js.="d.getElementById('stNomCGM').innerHTML      ='".$arValores[$inCount]['stNomCGM']."';              ";
        $js.="ajaxJavaScript('../../../../../../gestaoFinanceira/fontes/PHP/contabilidade/instancias/processamento/OCContaAnalitica.php?".Sessao::getId()."&inCodContaLancamento=".$arValores[$inCount]['inCodContaLancamento']."&stNomCampoCod=inCodContaLancamento&stIdCampoDesc=innerContaLancamento&stUsaEntidade=N','emp_conta_lancamento_adiantamentos')";

        echo $js;

    break;

    case "alteradoListaResponsaveis":

        $boErro = false;

        // Verifica se os dados já não estão salvos na sessão
        $arValores = Sessao::read('arValores');
        foreach ($arValores as $arTEMP) {
            if ( ( $arTEMP["id"] != $_REQUEST["HdnCodResponsavel"] ) && ( ( $arTEMP["inCGM"] == $_REQUEST["inCGM"] ) || ( $arTEMP["inCodContaLancamento"] == $_REQUEST[ "inCodContaLancamento" ]) ) ) {
                $boErro = true;
                $stErro = 'Responsável '.$_REQUEST["inCGM"].' ou Conta Lançamento '.$_REQUEST[ "inCodContaLancamento" ].' já constam na listagem.';
                break;
            }
        }

        if (!$boErro) {
            $boContaLancamentoUsada = verificaContaLancamento($_REQUEST['inCGM'], $_REQUEST[ "inCodContaLancamento" ] );

            if ($boContaLancamentoUsada) {
                $boErro = true;
                $stErro = 'Conta Lançamento '.$_REQUEST[ "inCodContaLancamento" ].' está sendo utilizada por outro responsável.';
            }
        }

        if (!$boErro) {
            $inCount = $_REQUEST['HdnCodResponsavel']-1;
            $arValores[$inCount]['id'                  ] = $inCount + 1;
            $arValores[$inCount]['inCGM'               ] = $_REQUEST[ "inCGM"                ];
            $arValores[$inCount]['stNomCGM'            ] = (!empty($_REQUEST[ "stNomCGM"    ])) ? $_REQUEST[ "stNomCGM"] : $_REQUEST[ "HdnNomResponsavel"];
            $arValores[$inCount]['inCodContaLancamento'] = $_REQUEST[ "inCodContaLancamento" ];
            $arValores[$inCount]['innerContaLancamento'] = $_REQUEST[ "innerContaLancamento" ];
            $arValores[$inCount]['inCodSituacao'       ] = $_REQUEST[ "inCodSituacao"        ];

            $js.="limparResponsaveis();";
            Sessao::write('arValores', $arValores);
            echo $js.montaListaResponsaveis($arValores);
        } else {
            //$js.="f.HdnNomResponsavel.value                             = '';";
            $js.="alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');";
            echo $js;
        }

    break;

    //Exclui itens da listagem de responsáveis por adiantamento utilizados
    case 'excluirListaResponsaveis':

        $boBloqueia        = false;
        $arTEMP            = array();
        $inCount           = 0;

        $arValores = Sessao::read('arValores');
        $inCGM              = $arValores[$_REQUEST['id']-1]['inCGM'];
        $inCodContrapartida = $arValores[$_REQUEST['id']-1]['inCodContraPartida'];

        $boBloqueia = verificaFornecedor( $inCGM, $inCodContrapartida );

        if ($boBloqueia) {
            $stMensagem = "Erro ao Excluir Responsáveis Por Adiantamento! (Responsável ".$inCGM." está sendo utilizado pelo sistema.)";
            echo "alertaAviso('".$stMensagem."','','erro','".Sessao::getId()."');";
        } else {

            foreach ($arValores as $key => $value) {
                if (($key+1) != $_REQUEST['id']) {
                    $arTEMP[$inCount]['id'                  ] = $inCount + 1;
                    $arTEMP[$inCount]['inCGM'               ] = $value[ "inCGM"                ];
                    $arTEMP[$inCount]['stNomCGM'            ] = $value[ "stNomCGM"             ];
                    $arTEMP[$inCount]['inCodContaLancamento'] = $value[ "inCodContaLancamento" ];
                    $arTEMP[$inCount]['innerContaLancamento'] = $value[ "innerContaLancamento" ];
                    $arTEMP[$inCount]['inCodSituacao'       ] = $value[ "inCodSituacao"        ];
                    $arTEMP[$inCount]['inPrazo'             ] = $value[ "inPrazo"              ];
                    $arTEMP[$inCount]['inCodContraPartida'  ] = $value[ "inCodContraPartida"   ];
                    $inCount++;
                }

            }

            $arValores = $arTEMP;

            if ( count($arValores) <= 0 ) {
                $js = "f.inCodContraPartida.disabled = false;";
                $js.= "d.getElementById('btContraPartida').style.display = 'inline';            ";
                $js.= "f.inPrazo.disabled            = false;";
                echo $js;
            }

            Sessao::write('arValores', $arValores);
            echo montaListaResponsaveis($arValores);
        }

    break;

    case 'incluirListaResponsaveis':

        $boErro = false;

        $arValores = Sessao::read('arValores');

        // Verifica se os dados já não estão salvos na sessão
        if (!is_null($arValores)) {
          foreach ($arValores as $arTEMP) {
              if ( ( $arTEMP["id"] != $_REQUEST["HdnCodResponsavel"] ) && ( ( $arTEMP["inCGM"] == $_REQUEST["inCGM"] ) || ( $arTEMP["inCodContaLancamento"] == $_REQUEST[ "inCodContaLancamento" ]) ) ) {
                  $boErro = true;
                  $stErro = 'Responsável '.$_REQUEST["inCGM"].' ou Conta Lançamento '.$_REQUEST[ "inCodContaLancamento" ].' já constam na listagem.';
                  break;
              }
          }
        }
        if (!$boErro) {

            if ($_REQUEST['inCGM'] != null || $_REQUEST[ "inCodContaLancamento" ] != null) {
              $boContaLancamentoUsada = verificaContaLancamento($_REQUEST['inCGM'], $_REQUEST[ "inCodContaLancamento" ] );
            } else {
              break;
            }

            if ($boContaLancamentoUsada) {
              $boErro = true;
              $stErro = 'Conta Lançamento '.$_REQUEST[ "inCodContaLancamento" ].' está sendo utilizada por outro responsável.';
            }
        }

        if (!$boErro) {
            $inCount = sizeof($arValores);
            $arValores[$inCount]['id'                  ] = $inCount + 1;
            $arValores[$inCount]['inCGM'               ] = $_REQUEST[ "inCGM"                ];
            $arValores[$inCount]['stNomCGM'            ] = (!empty($_REQUEST[ "stNomCGM"])) ? $_REQUEST[ "stNomCGM"] : $_REQUEST[ "HdnNomResponsavel"] ;
            $arValores[$inCount]['inCodContaLancamento'] = $_REQUEST[ "inCodContaLancamento" ];
            $arValores[$inCount]['innerContaLancamento'] = $_REQUEST[ "innerContaLancamento" ];
            $arValores[$inCount]['inCodSituacao'       ] = $_REQUEST[ "inCodSituacao"        ];
            $arValores[$inCount]['inPrazo'             ] = $_REQUEST[ "inPrazo"              ];
            $arValores[$inCount]['inCodContraPartida'  ] = $_REQUEST[ "inCodContraPartida"   ];

            if (sizeOf($arValores)>=1) {
                $stJs = "f.inCodContraPartida.disabled = true;";
                $stJs.= "d.getElementById('btContraPartida').style.display = 'none';                              ";
                $stJs.= "f.inPrazo.disabled            = true;";
            }

            $stJs.= "limparResponsaveis();";
            $stJs.= "excluirListaResponsaveis('".$_REQUEST['HdnCodResponsavel']."');";

        } else {
            echo "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');";
        }

        Sessao::write('arValores', $arValores);
        echo $stJs.montaListaResponsaveis( $arValores);

    break;

    case "limparFormulario":
        Sessao::remove('arValores');
        echo montaListaResponsaveis( Sessao::read('arValores'));
    break;
}

function montaListaResponsaveis($arRecordSet , $boExecuta = true)
{
    $stPrograma = "ManterContrato";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsResponsaveis = new RecordSet;
    if ($arRecordSet != '') {
        $rsResponsaveis->preenche( $arRecordSet );
    }

    $obLista = new Lista;

    $obLista->setTitulo('');
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsResponsaveis );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->ultimoCabecalho->setWidth( 50 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conta Contábil");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Situação");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inCGM" );
    $obLista->ultimoDado->setTitle( "CGM" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[stNomCGM]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inCodContaLancamento" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inCodSituacao" );
    $obLista->ultimoDado->setTitle( "Situação." );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:alterarListaResponsaveis();" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:excluirListaResponsaveis();" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        return "d.getElementById('spnListaResponsaveis').innerHTML = '".$stHTML."';";
    } else {
        return $stHTML;
    }

}

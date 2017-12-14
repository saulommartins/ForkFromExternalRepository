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
    * Pagina de formulário para Cadastro de Comissão de licitação
    * Data de Criação   : 28/08/2006

    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: OCManterComissaoLicitacao.php 62654 2015-05-29 12:59:20Z evandro $

    * Casos de uso: uc-03.05.09
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoTipoMembro.class.php'                              );

//// Montagem da listagem de membros
function montaSpanMembros($stAcao = '')
{ 
    $rsListaMembros = new RecordSet;
    if ( count( Sessao::read('arMembros')) > 0  ) {
        $rsListaMembros->preenche( Sessao::read('arMembros'));
    }
    $obLstMembros = new Lista;

    $obLstMembros->setTitulo          ( 'Membros da comissão' );
    $obLstMembros->setMostraPaginacao ( false                 );
    $obLstMembros->setRecordSet       ( $rsListaMembros       );

    // Cabeçalho da lista
    $obLstMembros->addCabecalho();
    $obLstMembros->ultimoCabecalho->addConteudo ( "&nbsp;"     );
    $obLstMembros->ultimoCabecalho->setWidth    ( 3            );
    $obLstMembros->commitCabecalho();

    $obLstMembros->addCabecalho();
    $obLstMembros->ultimoCabecalho->addConteudo ( 'Nome' );
    $obLstMembros->ultimoCabecalho->setWidth    ( 80     );
    $obLstMembros->commitCabecalho();

    $obLstMembros->addCabecalho();
    $obLstMembros->ultimoCabecalho->addConteudo ( 'Tipo' );
    $obLstMembros->ultimoCabecalho->setWidth    ( 10     );
    $obLstMembros->commitCabecalho();

    $obLstMembros->addCabecalho();
    $obLstMembros->ultimoCabecalho->addConteudo ( 'Data Designação' );
    $obLstMembros->ultimoCabecalho->setWidth    ( 10                );
    $obLstMembros->commitCabecalho();

    if ($stAcao != 'consultar') {
        $obLstMembros->addCabecalho();
        $obLstMembros->ultimoCabecalho->addConteudo ( "&nbsp;"     );
        $obLstMembros->ultimoCabecalho->setWidth    ( 6            );
        $obLstMembros->commitCabecalho();

        $obLstMembros->addAcao();
        $obLstMembros->ultimaAcao->setAcao( "ALTERAR" );
        $obLstMembros->ultimaAcao->setFuncaoAjax( true );
        $obLstMembros->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('alterarItem')" );
        $obLstMembros->ultimaAcao->addCampo("1","inId");
        $obLstMembros->commitAcao();

        $obLstMembros->addAcao();
        $obLstMembros->ultimaAcao->setAcao( "EXCLUIR" );
        $obLstMembros->ultimaAcao->setFuncaoAjax( true );
        $obLstMembros->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirItem')"  );
        $obLstMembros->ultimaAcao->addCampo("1","inId");
        $obLstMembros->commitAcao();
    }

    $obLstMembros->addDado();
    $obLstMembros->ultimoDado->setCampo( 'nome' );
    $obLstMembros->commitDado();

    $obLstMembros->addDado();
    $obLstMembros->ultimoDado->setCampo( 'tipo' );
    $obLstMembros->commitDado();

    $obLstMembros->addDado();
    $obLstMembros->ultimoDado->setCampo( 'datadesignacao' );
    $obLstMembros->commitDado();

    $obLstMembros->montaHTML();
    $stHtml = $obLstMembros->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnListaMembros').innerHTML = ' " .$stHtml. "';" ;

    return $stJs;
}

function buscaNorma($inCodNorma)
{
    if ($inCodNorma) {
        include_once CAM_GA_NORMAS_NEGOCIO."RNorma.class.php";
        include_once CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php";

        $obRNorma = new RNorma;
        $obRNorma->setCodNorma        ( $inCodNorma );
        $obRNorma->setExercicio       ( Sessao::getExercicio() );
        $obErro = $obRNorma->consultar( $rsRecordSet );

        if (!$obErro->ocorreu()) {
            if ($obRNorma->getNomeNorma() != NULL) {
                $stNorma  = $obRNorma->obRTipoNorma->getNomeTipoNorma().' '.$obRNorma->getNumNorma();
                $stNorma .= '/'.$obRNorma->getExercicio().' - '.$obRNorma->getNomeNorma();
                $stDataDesignacao = $rsRecordSet->getCampo( 'dt_publicacao' );
                 ///pegando a data final de vigencia
                $stDataTermino = $obRNorma->getDataTermino();

                $js = 'jQuery("#stAtoDesignacao").html("'.$stNorma.'");';
                $js .= "jQuery('#hdStAtoDesignacaoMembro').val('".$stNorma."');";

                if ($_GET['stComData'] != 'N') {
                    $js .= 'jQuery("#stDataDesignacaoComissao").html("'.$stDataDesignacao.'");
                                if (f.hdDataDesignacaoComissao) {                            
                                    f.hdDataDesignacaoComissao.value = "'.$stDataDesignacao . '"; 
                                }';
                }

                $obTNormaDataTermino = new TNormaDataTermino();
                $obTNormaDataTermino->setDado('cod_norma',$inCodNorma);
                $obTNormaDataTermino->recuperaNormaDataTermino($rsDataRecordSet);

                $js .= 'd.getElementById("dtVigencia").innerHTML = "'.$rsDataRecordSet->getCampo('dt_termino'). '";';

            } else {

                $js .= 'd.getElementById("stAtoDesignacao").innerHTML  = "&nbsp;";';
                $js .= 'd.getElementById("inCodNorma").value = "";';
                $js .= 'd.getElementById("dtVigencia").innerHTML = "&nbsp;";';

                if ($_GET['stComData'] != 'N') {
                    $js .= 'd.getElementById("stDataDesignacaoComissao").value = "'.$stDataDesignacao . '";
                            if (f.hdDataDesignacaoComissao) {
                                f.hdDataDesignacaoComissao.value = "'.$stDataDesignacao . '";
                            }                                                                          
                            d.getElementById("dtVigencia").value = "'.$stDataTermino . '";
                        ';
                }

                $js .= "alertaAviso('@Valor inválido. (".$inCodNorma.")','form','erro','".Sessao::getId()."');";
            }
        }
    } else {
        $js .= 'd.getElementById("stAtoDesignacao").innerHTML = "&nbsp;";';
        $js .= 'd.getElementById("inCodNorma").value = "";';
    }

    return $js ;
}

function buscaNormaMembro($inCodNormaMembro = '')
{
        if ($inCodNormaMembro) {
            include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );
            $obRNorma = new RNorma;
            $obRNorma->setCodNorma( $inCodNormaMembro );
            $obRNorma->setExercicio( Sessao::getExercicio() );
            $obErro = $obRNorma->consultar( $rsRecordSet );

            if ( !$obErro->ocorreu() ) {
              if ( $obRNorma->getNomeNorma() != NULL ) {
                $stNorma  = $obRNorma->obRTipoNorma->getNomeTipoNorma().' '.$obRNorma->getNumNorma();
                $stNorma .= '/'.$obRNorma->getExercicio().' - '.$obRNorma->getNomeNorma();

                $stDataDesignacao = $rsRecordSet->getCampo( 'dt_publicacao' );
                ///pegando a data final de vigencia
                $stDataTermino = $obRNorma->getDataTermino()      ;
                $js = 'd.getElementById("stAtoDesignacaoMembro").innerHTML = "'.$stNorma.'";';
                if ($_GET['stComData'] != 'N') {
                 $js .= "d.getElementById('stDataDesignacaoMembro').innerHTML   = '$stDataDesignacao';"   ;
                 $obTNormaDataTermino = new TNormaDataTermino();
                 $obTNormaDataTermino->setDado('cod_norma',$inCodNormaMembro);
                 $obTNormaDataTermino->recuperaNormaDataTermino($rsDataMembroRecordSet);
                 $js.='d.getElementById("dtVigenciaMembro").innerHTML="'.$rsDataMembroRecordSet->getCampo('dt_termino').'";';
                 $js.='f.hdDataDesignacaoMembro.value ="'.$stDataDesignacao.'";';
                 $js.='f.hdDtVigenciaMembro.value ="'.$rsDataMembroRecordSet->getCampo('dt_termino').'";';
                 $js.="jq('#hdAtoDesc').val('".$stNorma."');";
                }
              } else {

                  $js.='d.getElementById("stAtoDesignacaoMembro").innerHTML  = "&nbsp;";';
                  $js.='d.getElementById("inCodNormaMembro").value   = " ";';
                  if ($_GET['stComData'] != 'N') {
                      $js.="d.getElementById('stDataDesignacaoMembro').innerHTML = ' '; \n ";
                      $js.="d.getElementById('dtVigenciaMembro').innerHTML = ' '; \n ";
                      $js.='f.hdDataDesignacaoMembro.value ="";';
                  }
                  $js .= "alertaAviso('@Valor inválido. (".$inCodNormaMembro.")','form','erro','".Sessao::getId()."');";
              }
            }
        } else {
            $js = 'd.getElementById("stAtoDesignacaoMembro").innerHTML = "&nbsp;";';
        }

        return $js ;
}

/*
esta função recebe o codigo de uma comissão e preenche a array sessao->transf['arMembros'] com os dados dos membros dela
*/
function preencheListaMembros($cod_comissao)
{
   include_once ( CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoComissaoMembros.class.php' );

   $obComissaoMembros = new TLicitacaoComissaoMembros;
   $obComissaoMembros->recuperaMembrosPorComissao ($rsMembros, $cod_comissao );

   $inCont = 0;
   $arMembros = array();
   while ( !$rsMembros->eof() ) {

        $arRegistro = array();
        $arRegistro['inId']              = $inCont ;
        $arRegistro['numcgm']            = $rsMembros->getCampo( 'numcgm'          );
        $arRegistro['nome']              = $rsMembros->getCampo( 'nom_cgm'         );
        $arRegistro['inCodNormaMembro']  = $rsMembros->getCampo( 'cod_norma'       );
        $arRegistro['intipo']            = $rsMembros->getCampo( 'cod_tipo_membro' );
        $arRegistro['datadesignacao']    = $rsMembros->getCampo( 'dt_publicacao'   );
        $arRegistro['tipo']              = $rsMembros->getCampo( 'tipo_membro'     );
        $arRegistro['cod_comissao']      = $rsMembros->getCampo( 'cod_comissao'    );
        $arRegistro['stCargoMembro']     = $rsMembros->getCampo( 'cargo'    );
        $arRegistro['inNaturezaCargo']   = $rsMembros->getCampo( 'natureza_cargo'    );


        $arMembros[] = $arRegistro;

        $rsMembros->proximo();
        $inCont++;
   }

   Sessao::write('arMembros', $arMembros);
}

/// inclusão de novo membro na lista
function incluiMembro()
{        
    $boIncluir = true;
    $arMembros = Sessao::read('arMembros');
    $inCountMembros = count($arMembros);
    $stJs = "";

    //$stJs = "alert('".SistemaLegado::comparaDatas(date('d/m/Y'), $_GET['hdDtVigenciaMembro'])."');";
    if ($_GET['hdDtVigenciaMembro'] == '') {
        $stJs .= " alertaAviso('@Efetue a alteração da norma (Código:".$_GET['inCodNormaMembro']." - ".$_GET['hdAtoDesc']."), para informar a data de término!','form','erro','".Sessao::getId()."');";
    } elseif (SistemaLegado::comparaDatas(date('d/m/Y'), $_GET['hdDtVigenciaMembro'])) {
        $stJs .= " alertaAviso('@A norma (Código:".$_GET['inCodNormaMembro']." - ".$_GET['hdAtoDesc'].") expirou, utilize outra norma!','form','erro','".Sessao::getId()."');";
    } else {
        for ($i=0; $i<$inCountMembros;$i++) {
            if ($arMembros[$i]['numcgm'] == $_GET['inCGM']) {
                $boIncluir = false;
                $stJs .= " alertaAviso('@Este membro já está na lista.','form','erro','".Sessao::getId()."');";
            }
            if ( ($arMembros[$i]['intipo'] == 2) AND ($arMembros[$i]['numcgm'] != $_GET['inCGM']) ) {
                $boPresidente = true;
            }
            if ( ($arMembros[$i]['intipo'] == 3) AND ($arMembros[$i]['numcgm'] != $_GET['inCGM']) ) {
                $boPregoeiro = true;
            }
        }
        if ( $boPresidente AND ($_GET['stTipoMembro'] == 2)  ) {
            $boIncluir = false;
            $stJs .= " alertaAviso('@Já existe um presidente para esta comissão.','form','erro','".Sessao::getId()."');";
        }
        if ( $boPregoeiro AND ($_GET['stTipoMembro'] == 3)  ) {
            $boIncluir = false;
            $stJs .= " alertaAviso('@Já existe um pregoeiro para esta comissão.','form','erro','".Sessao::getId()."');";
        }

        if ( ($_REQUEST['stCargoMembro'] == "") || ( $_REQUEST['inNaturezaCargo'] == "") ) {
            $boIncluir = false;
            $stJs .= " alertaAviso('@Deve informar o Cargo do membro e sua natureza.','form','erro','".Sessao::getId()."');";
        }

        if ($boIncluir) {
            $arRegistro = array();
            $arRegistro['inId']             = $inCont = $inCountMembros;
            $arRegistro['numcgm']           = $_GET['inCGM']                 ;
            $arRegistro['nome']             = $_GET['stNomCGM']              ;
            $arRegistro['intipo']           = $_GET['stTipoMembro']          ;
            $arRegistro['inCodNormaMembro'] = $_GET['inCodNormaMembro']      ;
            $arRegistro['dtVigenciaMembro'] = $_GET['hdDtVigenciaMembro']      ;
            $arRegistro['datadesignacao']   = $_GET['hdDataDesignacaoMembro'];
            $arRegistro['stCargoMembro']    = $_GET['stCargoMembro'];
            $arRegistro['inNaturezaCargo']  = $_GET['inNaturezaCargo'];
            $arRegistro['acao']             = 'novo';

            $obTTipoMembro = new TLicitacaoTipoMembro;
            $obTTipoMembro->recuperaTodos( $rsTiposMembro );
            $obTTipoMembro->setDado ( 'cod_tipo_membro', $_GET['stTipoMembro'] );
            $obTTipoMembro->consultar();
            $arRegistro['tipo'] = $obTTipoMembro->getDado( 'descricao' );
            $inPos = $inCountMembros;
            $arMembros[$inPos] = $arRegistro;
            $stJs .= montaSpanMembros();
            $stJs .= "limpaFormularioincluiMembro(); ";
            $stJs .= "f.btIncluirincluiMembro.value = 'Incluir'; ";

            Sessao::write('inPosAlteracao', -1);
            Sessao::write('arMembros', $arMembros);
            $stJs .= montaSpanMembros();
        }
    }

    return $stJs;
}

function alteraMembro()
{
    $boAlterar = true;
    $boPresidente = false;

    $arMembros = Sessao::read('arMembros');
    $inCountMembros = count($arMembros);

    if ($_GET['hdDtVigenciaMembro'] == '') {
        $stJs .= " alertaAviso('@Efetue a alteração da norma (Código:".$_GET['inCodNormaMembro']." - ".$_GET['hdAtoDesc']."), para informar a data de término!','form','erro','".Sessao::getId()."');";
    } elseif (SistemaLegado::comparaDatas(date('d/m/Y'), $_GET['hdDtVigenciaMembro'])) {
        $stJs .= " alertaAviso('@A norma (Código:".$_GET['inCodNormaMembro']." - ".$_GET['hdAtoDesc'].") expirou, utilize outra norma!','form','erro','".Sessao::getId()."');";
    } else {

        for ($i=0; $i<$inCountMembros;$i++) {
            if ( ($arMembros[$i]['numcgm'] == $_GET['inCGM']) AND ($i<>$_GET['hdnId']) ) {
                $boAlterar = false;
                $stJs = " alertaAviso('@Este membro já está na lista.','form','erro','".Sessao::getId()."');";
            }
            if ( ($arMembros[$i]['intipo'] == 2) AND ($arMembros[$i]['numcgm'] != $_GET['inCGM']) ) {
                $boPresidente = true;
            }
            if ( ($arMembros[$i]['intipo'] == 3) AND ($arMembros[$i]['numcgm'] != $_GET['inCGM']) ) {
                $boPregoeiro = true;
            }

        }

        if ( $boPresidente AND ($_GET['stTipoMembro'] == 2)  ) {
            $boAlterar = false;
            $stJs = " alertaAviso('@Já existe um presidente para esta comissão.','form','erro','".Sessao::getId()."');";
        }
        if ( $boPregoeiro AND ($_GET['stTipoMembro'] == 3)  ) {
            $boAlterar = false;
            $stJs = " alertaAviso('@Já existe um pregoeiro para esta comissão.','form','erro','".Sessao::getId()."');";
        }

        if ( ($_REQUEST['stCargoMembro'] == "") || ( $_REQUEST['inNaturezaCargo'] == "") ) {
            $boAlterar = false;
            $stJs .= " alertaAviso('@Deve informar o Cargo do Membro e sua Natureza.','form','erro','".Sessao::getId()."');";
        }

        if ($boAlterar) {

            $arRegistro = array();
            $arRegistro['inId']             = $_REQUEST['hdnId'];
            $arRegistro['numcgm']           = $_GET['inCGM']                 ;
            $arRegistro['nome']             = $_GET['stNomCGM']              ;
            $arRegistro['intipo']           = $_GET['stTipoMembro']          ;
            $arRegistro['inCodNormaMembro'] = $_GET['inCodNormaMembro']      ;
            $arRegistro['dtVigenciaMembro'] = $_GET['hdDtVigenciaMembro']      ;
            $arRegistro['datadesignacao']   = $_GET['hdDataDesignacaoMembro'];
            $arRegistro['stCargoMembro']    = $_GET['stCargoMembro'];
            $arRegistro['inNaturezaCargo']  = $_GET['inNaturezaCargo'];
            $arRegistro['acao']             = 'novo';

            $obTTipoMembro = new TLicitacaoTipoMembro;
            $obTTipoMembro->recuperaTodos( $rsTiposMembro );
            $obTTipoMembro->setDado ( 'cod_tipo_membro', $_GET['stTipoMembro'] );
            $obTTipoMembro->consultar();
           
            $arRegistro['tipo'] = $obTTipoMembro->getDado( 'descricao' );
            $arMembros[$_REQUEST['hdnId']] = $arRegistro;
            Sessao::write('arMembros', $arMembros);

            $stJs = montaSpanMembros();
            $stJs .= "limpaFormularioincluiMembro(); ";
            $stJs .= "f.btIncluirincluiMembro.value = 'Incluir'; ";
            $stJs .= "f.btIncluirincluiMembro.setAttribute('onclick','JavaScript:if ( ValidaincluiMembro() ) { montaParametrosGET( \'incluirincluiMembro\', \'\', true  );  }'); ";

        }
    }
    return $stJs;
}

function alterarMembro()
{
    $arMembros = Sessao::read('arMembros');
    // pegando o registro escolhido
    $inPosAlteracao = 0;
    foreach ($arMembros as $registro) {
        if ($registro['inId'] == $_GET['inId']) {
            $arMembro = $registro;
            Sessao::write('inPosAlteracao', $inPosAlteracao);
        }
        $inPosAlteracao++;
    }

    // preenchendo o "Número do Ato de Designação" do membro
    $stJs .= 'f.inCodNormaMembro.value = "'.$arMembro['inCodNormaMembro'].'";';
    $stJs .= buscaNormaMembro( $arMembro['inCodNormaMembro'] );

    // preenchendo nome do membro
    $stJs  .= 'f.inCGM.value = "'.$arMembro['numcgm'].'";';
    $stJs  .= 'f.hdnId.value = "'.$arMembro['inId'].'";';
    $stJs  .= 'd.getElementById("stNomCGM").innerHTML = "'.$arMembro['nome'].'";';
    $stJs  .= 'f.stNomCGM.value = "'.$arMembro['nome'].'";';
    $stJs  .= 'f.hdnNomCGM.value = "'.$arMembro['nome'].'";';

    // PREENCHE CARGO E NATUREZA DO CARGO
    $stJs  .= 'f.hdnCargo.value = "'.$arMembro['stCargoMembro']. '";';
    $stJs  .= 'f.inNaturezaCargo.value = "'.$arMembro['inNaturezaCargo']. '";';
    $stJs  .= 'f.stCargoMembro.value = "'.$arMembro['stCargoMembro']. '";';
    
    // TIPO DE MEMBRO
    $stJs .= 'f.stTipoMembro.value = ' . $arMembro['intipo'] . ';';

    $stJs .= "f.btIncluirincluiMembro.value = 'Alterar'; ";
    $stJs .= "f.btIncluirincluiMembro.setAttribute('onclick','JavaScript:if ( ValidaincluiMembro() ) { montaParametrosGET( \'alteraMembro\', \'\', true  );  }'); ";
    return $stJs;
}

function excluiMembro()
{
    $arTemp = array();
    $arRegistros = Sessao::read('arMembros');

    Sessao::remove('arMembros');

    $arMembrosExcluidos = array();
    foreach ($arRegistros as $registro) {
        if ($registro['inId'] != $_GET['inId']) {
            $arTemp[] = $registro;
        } else {
            if ($registro['acao'] != 'novo') {
                $arMembrosExcluidos[] = $registro;
            }
        }

    }
    Sessao::write('arMembrosExcluidos', $arMembrosExcluidos);
    Sessao::write('arMembros', $arTemp);

    $stJs = montaSpanMembros();

    return $stJs;
}

function montaSpanTipoMembro($stFinalidade)
{
    $stJs = '';

    $obFormulario = new Formulario;

    switch ($stFinalidade) {
        case '1':
                $stFiltro = ' where cod_tipo_membro in ( 1, 2 ) ';
        break;
        case '2':
                $stFiltro = '';
        break;
        case '3':
                $stFiltro = ' where cod_tipo_membro in ( 1, 3 ) ';
        break;
        case '4':
                $stFiltro = ' where cod_tipo_membro = 1 ';
        break;
    }

    $obTTipoMembro = new TLicitacaoTipoMembro;
    $obTTipoMembro->recuperaTodos( $rsTiposMembro, $stFiltro );

    $obCmbTipoMembro = new Select;
    $obCmbTipoMembro->setRotulo  ( 'Tipo do Membro'                                        );
    $obCmbTipoMembro->setTitle   ( 'Selecione o tipo do membro que está sendo cadastrado.' );
    $obCmbTipoMembro->setName    ( "stTipoMembro"                                          );
    $obCmbTipoMembro->setId      ( "stTipoMembro"                                          );
    $obCmbTipoMembro->setValue   ( $stTipoMembro                                           );
    $obCmbTipoMembro->setStyle   ( "width: 200px"                                          );

    while ( !$rsTiposMembro->eof() ) {
        $obCmbTipoMembro->addOption ( $rsTiposMembro->getCampo( 'cod_tipo_membro' ) , $rsTiposMembro->getCampo ( 'descricao' ) );
        $rsTiposMembro->proximo();
    }
    $obCmbTipoMembro->setObrigatorioBarra( true );

    $obFormulario->addComponente ( $obCmbTipoMembro                );
    $obFormulario->montaInnerHtml();
    $stJs = "d.getElementById('spnTipoMembro').innerHTML = '".$obFormulario->getHTML()."';    \n";

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case 'montaSpanMembros':
        $stJs = montaSpanMembros();
    break;
    case 'incluirincluiMembro':
        $stJs = incluiMembro();
    break;
    case 'buscaNorma':
        $stJs = buscaNorma($_GET['inCodNorma'] );
    break;
    case 'buscaNormaMembro':
        $stJs = buscaNormaMembro($_GET['inCodNormaMembro']);
    break;
    case 'excluirItem':
        $stJs = excluiMembro();
    break;
    case 'alterarItem':
        $stJs = alterarMembro();
    break;
    case 'alteraMembro':
        $stJs = alteraMembro();
    break;
    case 'montaSpanTipoMembro':
        $stJs = montaSpanTipoMembro($_GET['stFinalidade']);
    break;
}

if ($stJs) {
    echo $stJs;
}
?>

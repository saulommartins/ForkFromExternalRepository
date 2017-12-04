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
    * Página de processamento oculto para o Lançamento do Imposto de Transferência
    * Data de Criação   : 04/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCLancarTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO . "RCIMTransferencia.class.php" );
include_once( CAM_GT_CIM_NEGOCIO . "RCIMImovel.class.php"        );
include_once( CAM_GT_CIM_NEGOCIO . "RCIMCorretagem.class.php"    );
include_once( CAM_GA_PROT_NEGOCIO . "RProcesso.class.php"         );
include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php"              );

$obRCIMTransferencia = new RCIMTransferencia;
$obRCIMImovel        = new RCIMImovel (new RCIMLote);
$obRCIMProprietario  = new RCIMProprietario ( $obRCIMImovel );
$obRProcesso         = new RProcesso;
$obRCGM              = new RCGM;
$rsCGM               = new Recordset;

function ListaProprietarios($rsRecordset)
{
    if ( $rsRecordset->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao                   ( false                                       );
        $obLista->setTitulo                            ( "Lista de Proprietários"                    );
        $obLista->setRecordSet                         ( $rsRecordset                                );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ("&nbsp;"                                     );
        $obLista->ultimoCabecalho->setWidth            ( 3                                           );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "CGM"                                       );
        $obLista->ultimoCabecalho->setWidth            ( 10                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Nome"                                      );
        $obLista->ultimoCabecalho->setWidth            ( 64                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Quota Atual(%)"                            );
        $obLista->ultimoCabecalho->setWidth            ( 10                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "cgm"                                       );
        $obLista->commitDado                           (                                             );
        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "nome"                                      );
        $obLista->commitDado                           (                                             );
        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "quota"                                     );
        $obLista->commitDado                           (                                             );

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    } else {
        $stHTML = "&nbsp";
    }

    $stJs = "d.getElementById('spnProprietarios').innerHTML = '".$stHtml."';";

    return $stJs;
}

function listaDocumentos($rsRecordSet)
{
    if ( $rsRecordSet->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao                   ( false                                       );
        $obLista->setTitulo                            ( "Documentos apresentados"                   );
        $obLista->setRecordSet                         ( $rsRecordSet                                );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ("&nbsp;"                                     );
        $obLista->ultimoCabecalho->setWidth            ( 3                                           );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Entregue"                                  );
        $obLista->ultimoCabecalho->setWidth            ( 10                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Documento"                                 );
        $obLista->ultimoCabecalho->setWidth            ( 62                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Obrigatório"                               );
        $obLista->ultimoCabecalho->setWidth            ( 10                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );

        $obChkEntregue = new Checkbox;
        $obChkEntregue->setName                        ( "boEntregue"                                );
        $obChkEntregue->obEvento->setOnChange          ( "buscaValor('atualizaCheckDocumento');"     );

        $obLista->addDadoComponente                    ( $obChkEntregue                              );
        $obLista->ultimoDado->setCampo                 ( "entregue"                                  );
        $obLista->commitDadoComponente                 (                                             );
        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "nome"                                      );
        $obLista->commitDado                           (                                             );
        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "obrigatorio"                               );
        $obLista->commitDado                           (                                             );

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    } else {
        $stHtml = "&nbsp";
    }

    // preenche a lista com innerHTML
    $stJs = "d.getElementById('spnLstDoc').innerHTML = '".$stHtml."';";

    while ( !$rsRecordSet->eof() ) {
        $stJs .= "d.frm.boEntregue_".$rsRecordSet->getCorrente().".checked = ".( $rsRecordSet->getCampo( "entregue" ) == 'f' ? 'false' : 'true' ).";";

        $rsRecordSet->proximo();
    }

    return $stJs;
}

function listaAdquirentes($rsRecordSet)
{
    if ( $rsRecordSet->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao                   ( false                                       );
        $obLista->setTitulo                            ( "Lista de adquirentes"                      );
        $obLista->setRecordSet                         ( $rsRecordSet                                );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ("&nbsp;"                                     );
        $obLista->ultimoCabecalho->setWidth            ( 3                                           );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "CGM"                                       );
        $obLista->ultimoCabecalho->setWidth            ( 10                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Nome"                                      );
        $obLista->ultimoCabecalho->setWidth            ( 50                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Quota Atual (%)"                           );
        $obLista->ultimoCabecalho->setWidth            ( 15                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );

        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Quota Futura (%)"                          );
        $obLista->ultimoCabecalho->setWidth            ( 15                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );

        $obLista->addCabecalho                     (                                             );
        $obLista->ultimoCabecalho->addConteudo     ("&nbsp;"                                     );
        $obLista->ultimoCabecalho->setWidth        ( 3                                           );
        $obLista->commitCabecalho                  (                                             );

        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "codigo"                                    );
        $obLista->commitDado                           (                                             );
        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "nome"                                      );
        $obLista->commitDado                           (                                             );
        /* Quota Atual */

        $obLista->addDado                              (                                              );
        $obLista->ultimoDado->setCampo                 ( "quota_ant"                                  );
        $obLista->commitDado                           (                                              );
        /* Quota Futura */

        $obLista->addDado                              (                                              );
        $obLista->ultimoDado->setCampo                 ( "quota"                                      );
        $obLista->commitDado                           (                                              );

        $obLista->addAcao                              (                                              );
        $obLista->ultimaAcao->setAcao                  ( "EXCLUIR"                                    );
        $obLista->ultimaAcao->setFuncao                ( true                                         );
        $obLista->ultimaAcao->setLink                  ( "javascript:excluiDado('excluiAdquirente');" );
        $obLista->ultimaAcao->addCampo                 ( "1","inId"                                   );
        $obLista->commitAcao                           (                                              );

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    } else {
        $stHtml = "&nbsp";
    }

    // preenche a lista com innerHTML
    $stJs = "d.getElementById('spnAdquirentes').innerHTML = '".$stHtml."';";

    return $stJs;
}

$stJs = "";

switch ($_REQUEST['stCtrl']) {
    case "excluiAdquirente":
        $rsRecordSet = new Recordset;

        $id = $_GET['inId'];

        $arAdquirentes = Sessao::read( 'Adquirentes' );
        reset( $arAdquirentes );
        while ( list( $arId ) = each( $arAdquirentes ) ) {
            if ($arAdquirentes[ $arId ][ 'inId' ] != $id) {
                $arElementos[ 'inId'   ] = $arAdquirentes[ $arId ][ 'inId'   ];
                $arElementos[ 'codigo' ] = $arAdquirentes[ $arId ][ 'codigo' ];
                $arElementos[ 'nome'   ] = $arAdquirentes[ $arId ][ 'nome'   ];
                $arElementos[ 'quota'  ] = $arAdquirentes[ $arId ][ 'quota'  ];
                $arTMP[] = $arElementos;
            }
        }

        Sessao::write( 'Adquirentes', $arTMP );
        if ( count( $arTMP ) - 1 >= 0 ) {
            $rsRecordSet->preenche( $arTMP );
        }

        $stJs = listaAdquirentes( $rsRecordSet );
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "MontaAdquirente":
        $inCountAdq = 0;
        $arListaAdquirentes = array();
        $inTotalQuota = 0;
        $arAdquirentes = Sessao::read( 'Adquirentes' );
        if ( count( $arAdquirentes ) > 0  ) {
            foreach ($arAdquirentes as $inChave => $arAdquirentes) {
                $arListaAdquirentes[$inCountAdq] = $arAdquirentes["codigo"];
                $inTotalQuota += $arAdquirentes["quota"];
                $inCountAdq++;
            }
        }

        $flQuota = str_replace( ".", "", $_POST[ 'nuQuota'  ] );
        $flQuota = str_replace( ",", ".", $flQuota );

        $inTotalQuota += $flQuota;
        $inTotalQuota = floatval($inTotalQuota);
        if ($inTotalQuota > 100) {
            $stJs .= 'f.nuQuota.focus();';
            $stJs .= "alertaAviso('@Quota total supera 100%','form','erro','".Sessao::getId()."');";
        } else {
            if ( !in_array( $_POST['inNumCGM'] , $arListaAdquirentes ) ) {
                $rsRecordSet = new Recordset;
                $arAdquirentes = Sessao::read( 'Adquirentes' );
                if ($arAdquirentes) {
                    $rsRecordSet->preenche ( $arAdquirentes );
                }

                $rsRecordSet->setUltimoElemento         (                                );
                $inUltimoId    = $rsRecordSet->getCampo ( "inId"                         );

                ++$inUltimoId;

                    $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);
                    $obRCIMProprietario->listarProprietariosPorImovel($rsProrprietarios );

                    while (!$rsProrprietarios->eof()) {
                        if ($rsProrprietarios->getCampo("numcgm") == $_POST['inNumCGM']) {
                            $flQuotaAnt    = $rsProrprietarios->getCampo("cota"     );
                        }
                        $rsProrprietarios->proximo();
                    }

                $obRCGM->setNumCGM( $_POST[ 'inNumCGM' ] );
                $obRCGM->consultar( $rsCGM );

                $arElementos[ 'inId'        ] = $inUltimoId;
                $arElementos[ 'codigo'      ] = $_POST[ 'inNumCGM' ];
                $arElementos[ 'nome'        ] = $obRCGM->getNomCGM();
                $arElementos[ 'quota_ant'   ] = $flQuotaAnt;
                $arElementos[ 'quota'       ] = $flQuota;

                $arAdquirentes[] = $arElementos;
                Sessao::write( "Adquirentes", $arAdquirentes );
                $rsRecordSet->preenche( $arAdquirentes );
                $stJs = listaAdquirentes( $rsRecordSet );
                $stJs .= "d.frm.inNumCGM.value                         = '';";
                $stJs .= "d.getElementById('campoInner').innerHTML     = '&nbsp;';";
                $stJs .= "d.frm.nuQuota.value                          = '';";
            } else {
                $stJs = 'f.inNumCGM.value = "";';
                $stJs .= 'f.inNumCGM.focus();';
                $stJs .= "d.getElementById('campoInner').innerHTML     = '&nbsp;';";
                $stJs .= "alertaAviso('@Adquirente já informado.(".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            }
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "MontarListas&Carnes":
        /* Listar Proprietarios */
        if ($_REQUEST["inInscricaoImobiliaria"]) {
            $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);
            /* Recordset com os proprietarios do imovel */

            $obRCIMProprietario->listarProprietariosPorImovel($rsProrprietarios );
            $arProprietarios = array();
            $inCont = 0;

            $arProprietario = Sessao::read( "proprietario" );
            $arProprietario["quota"] = -1;
            while (!$rsProrprietarios->eof()) {
                $inNumCgm   = $rsProrprietarios->getCampo("numcgm"   );
                $flQuota    = $rsProrprietarios->getCampo("cota"     );
                $obRCGM->setNumCGM  ($inNumCgm  );
                $obRCGM->consultar  ( $rsCGM    );
                $arProprietarios[$inCont][ 'inSeq'   ] = $inCont     ;
                $arProprietarios[$inCont][ 'cgm'     ] = $inNumCgm   ;
                $arProprietarios[$inCont][ 'nome'    ] = $obRCGM->getNomCGM();
                $arProprietarios[$inCont][ 'quota'   ] = $flQuota;
                $rsProrprietarios->proximo();

                if ($arProprietario["quota"] < $flQuota) {
                    $arProprietario["quota"] = $flQuota;
                    $arProprietario["cgm"] = $inNumCgm;
                }

                $inCont++;
            }

            Sessao::write ( 'proprietario', $arProprietario );
            $rsProprietarios = new Recordset;

            $rsProprietarios->preenche( $arProprietarios );
            $stJs = ListaProprietarios ( $rsProprietarios );

            if ($_REQUEST['inCodigoTransferencia']) {
                $rsRecordSet = new Recordset;

                $obRCIMTransferencia->setCodigoTransferencia( $_REQUEST['inCodigoTransferencia'] );
                $obRCIMTransferencia->consultarAdquirentes();

                Sessao::write ( 'Adquirentes', $obRCIMTransferencia->getAdquirentes() );
                /* Modificar array para adicionar cota anterior*/
                $arAdquirentes = $obRCIMTransferencia->getAdquirentes();
                $rsProrprietarios->setPrimeiroElemento();
                while ( !$rsProrprietarios->eof() ) {
                    $inCont=0;
                    foreach ($arAdquirentes as $inFor) {
                        if ( $rsProrprietarios->getCampo("numcgm") == $arAdquirentes[$inCont]['codigo'] ) {
                            $arAdquirentes[$inCont]['quota_ant'] = $rsProrprietarios->getCampo("cota");
                        }

                        $inCont++;
                    }

                    $rsProrprietarios->proximo();
                }

                Sessao::write( "Adquirentes", $arAdquirentes );
                if ($arAdquirentes) {
                    $rsRecordSet->preenche    ( $arAdquirentes );
                    $stJs .= listaAdquirentes ( $rsRecordSet );
                }

                /* Lista de Documentos */
                $rsRecordSet = new Recordset;

                $obRCIMTransferencia->setCodigoTransferencia( $_POST['inCodigoTransferencia']);
                $obRCIMTransferencia->setCodigoNatureza( $_POST['inCodigoNatureza'] );
                $obRCIMTransferencia->consultarDocumentos();

                Sessao::write( "Documentos", $obRCIMTransferencia->getDocumentos() );
                if ( $obRCIMTransferencia->getDocumentos() ) {
                    $rsRecordSet->preenche   ( $obRCIMTransferencia->getDocumentos() );
                    $stJs .= listaDocumentos ( $rsRecordSet );
                }
            }

            SistemaLegado::executaFrameOculto($stJs);
        }

       $obRARRCarne = new RARRCarne;
       $obRARRCarne->listarModeloDeCarne( $rsModelos, Sessao::read('acao') );

       $obCmbModelo =  new Select;
       $obCmbModelo->setRotulo        ( "Modelo de Carnê" );
       $obCmbModelo->setName          ( "stArquivo" );
       $obCmbModelo->setStyle         ( "width: 200px");
       $obCmbModelo->setCampoID       ( "[nom_arquivo]§[cod_modelo]" );
       $obCmbModelo->setCampoDesc     ( "nom_modelo" );
       $obCmbModelo->addOption        ( "", "Selecione" );
       $obCmbModelo->setNull          ( false );
       $obCmbModelo->preencheCombo    ( $rsModelos );

       $obFormulario = new Formulario;
       $obFormulario->addComponente( $obCmbModelo );
       $obFormulario->montaInnerHTML();
       $stHTML = $obFormulario->getHTML ();

       $stHTML = str_replace( "\n" ,"" ,$stHTML );
       $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
       $stHTML = str_replace( "  " ,"" ,$stHTML );
       $stHTML = str_replace( "'","\\'",$stHTML );
       $stHTML = str_replace( "\\\\'","\\'",$stHTML );

       if ($_REQUEST['boEmissaoCarne'] == 'sim') {
          SistemaLegado::executaFrameOculto($js."d.getElementById('spnModeloCarne').innerHTML = '".$stHTML."';");
       } else {
          SistemaLegado::executaFrameOculto($js."d.getElementById('spnModeloCarne').innerHTML = '';");
       }
       break;

    case "MontarListas":
        /* Listar Proprietarios */
        if ($_REQUEST["inInscricaoImobiliaria"]) {
            $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);
            /* Recordset com os proprietarios do imovel */

            $obRCIMProprietario->listarProprietariosPorImovel($rsProrprietarios );
            $arProprietarios = array();
            $inCont = 0;

            $arProprietario = Sessao::read( "proprietario" );
            $arProprietario["quota"] = -1;
            while (!$rsProrprietarios->eof()) {
                $inNumCgm   = $rsProrprietarios->getCampo("numcgm"   );
                $flQuota    = $rsProrprietarios->getCampo("cota"     );
                $obRCGM->setNumCGM  ($inNumCgm  );
                $obRCGM->consultar  ( $rsCGM    );
                $arProprietarios[$inCont][ 'inSeq'   ] = $inCont     ;
                $arProprietarios[$inCont][ 'cgm'     ] = $inNumCgm   ;
                $arProprietarios[$inCont][ 'nome'    ] = $obRCGM->getNomCGM();
                $arProprietarios[$inCont][ 'quota'   ] = $flQuota;
                $rsProrprietarios->proximo();

                if ($arProprietario["quota"] < $flQuota) {
                    $arProprietario["quota"] = $flQuota;
                    $arProprietario["cgm"] = $inNumCgm;
                }

                $inCont++;
            }

            $rsProprietarios = new Recordset;

            $rsProprietarios->preenche($arProprietarios);
            $stJs = ListaProprietarios ( $rsProprietarios );

            if ($_REQUEST['inCodigoTransferencia']) {
                $rsRecordSet = new Recordset;

                $obRCIMTransferencia->setCodigoTransferencia( $_REQUEST['inCodigoTransferencia'] );
                $obRCIMTransferencia->consultarAdquirentes();

                Sessao::write( "Adquirentes", $obRCIMTransferencia->getAdquirentes() );
                $arAdquirentes = $obRCIMTransferencia->getAdquirentes();
                /* Modificar array para adicionar cota anterior*/
                $rsProrprietarios->setPrimeiroElemento();
                while ( !$rsProrprietarios->eof() ) {
                    $inCont=0;
                    foreach ($arAdquirentes as $inFor) {
                        if ( $rsProrprietarios->getCampo("numcgm") == $arAdquirentes[$inCont]['codigo'] ) {
                            $arAdquirentes[$inCont]['quota_ant'] = $rsProrprietarios->getCampo("cota"     );
                        }

                        $inCont++;
                    }

                    $rsProrprietarios->proximo();
                }

                Sessao::write ( "Adquirentes", $arAdquirentes );
                if ($arAdquirentes) {
                    $rsRecordSet->preenche    ( $arAdquirentes );
                    $stJs .= listaAdquirentes ( $rsRecordSet );
                }

                /* Lista de Documentos */
                $rsRecordSet = new Recordset;

                $obRCIMTransferencia->setCodigoTransferencia( $_POST['inCodigoTransferencia']);
                $obRCIMTransferencia->setCodigoNatureza( $_POST['inCodigoNatureza'] );
                $obRCIMTransferencia->consultarDocumentos();

                Sessao::write ( "Documentos", $obRCIMTransferencia->getDocumentos() );
                if ( $obRCIMTransferencia->getDocumentos() ) {
                    $rsRecordSet->preenche   ( $obRCIMTransferencia->getDocumentos() );
                    $stJs .= listaDocumentos ( $rsRecordSet );
                }
            }

            SistemaLegado::executaFrameOculto($stJs);
        }
        break;

    case "ListaDocumentos":
        $rsRecordSet = new Recordset;
        $obRCIMTransferencia->setCodigoTransferencia( $_REQUEST['inCodigoTransferencia'] );
        $obRCIMTransferencia->setCodigoNatureza( $_REQUEST['inCodigoNatureza'] );
        $obRCIMTransferencia->consultarDocumentos();

        Sessao::write ( "Documentos", $obRCIMTransferencia->getDocumentos() );
        if ( count( $obRCIMTransferencia->getDocumentos() ) > 0 )
            $rsRecordSet->preenche( $obRCIMTransferencia->getDocumentos() );

        $stJs = listaDocumentos ( $rsRecordSet );
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaCGM":
        if ($_POST[ 'inInscricaoImobiliaria' ]) {
            if ($_POST[ 'inNumCGM' ] && $_POST[ 'inNumCGM' ] != '0') {
                $obRCGM->setNumCGM( $_POST[ 'inNumCGM' ] );
                $obRCGM->consultar( $rsCGM );

                $inNumLinhas = $rsCGM->getNumLinhas();
                if ($inNumLinhas <= 0) {
                    $stJs .= 'f.inNumCGM.value = "";';
                    $stJs .= 'f.inNumCGM.focus();   ';
                    $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                    $stJs .= "alertaAviso('@Número do CGM não encontrado. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
                } else {

                    $boExisteCGM = 'f';
                    $arAdquirentesSessao = Sessao::read( "Adquirentes" );
                    if ( count( $arAdquirentesSessao ) - 1 >= 0 ) {
                        foreach ($arAdquirentesSessao as $inChave => $arAdquirentes) {
                             if ($arAdquirentes["codigo"] == $_POST[ 'inNumCGM' ]) {
                                 $boExisteCGM = 't';
                             }
                        }
                    }
                    if ($boExisteCGM == 'f') {
                        $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);

                        $obRCIMProprietario->listarProprietariosPorImovel($rsProrprietarios );
                        $arProprietarios = array();
                        $inCount = 0;
                        while ( !$rsProrprietarios->eof() ) {
                            if ( $rsProrprietarios->getCampo("promitente") == "f") {
                                $arProprietarios[$inCount] = $rsProrprietarios->getCampo("numcgm");
                            }
                            $inCount++;
                            $rsProrprietarios->proximo();
                        }

                            $stNomCgm = $rsCGM->getCampo("nom_cgm");
                            $stJs .= 'd.getElementById("campoInner").innerHTML = "'.$stNomCgm.'";';

                    } else {
                        $stJs .= 'f.inNumCGM.value = "";';
                        $stJs .= 'f.inNumCGM.focus();';
                        $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                        $stJs .= "alertaAviso('@Adquirente já informado. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
                    }
                }
            } elseif ($_POST[ 'inNumCGM' ] == '0') {
                $stJs .= "alertaAviso('@CGM inválido! (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "alertaAviso('@Inscrição Imobiliária não informada!','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.inNumCGM.value = "";';
            $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
            $stJs .= 'f.inInscricaoImobiliaria.focus();';
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaCreci":
        $obRCIMCorretagem = new RCIMCorretagem;
        if ($_REQUEST["stCreci"]) {
            $obRCIMCorretagem->setRegistroCreci( $_REQUEST["stCreci"]);
            $obRCIMCorretagem->buscaCorretagem ( $rsCorretagem       );
            if ( $rsCorretagem->eof() ) {
                $stJs = 'f.stCreci.value = "";';
                $stJs .= 'f.stCreci.focus();';
                $stJs .= 'd.getElementById("stNomeCreci").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('Registro Creci inválido!','form','erro','".Sessao::getId()."', '../');\n";
            } else {
                $stJs = 'd.getElementById("stNomeCreci").innerHTML = "'.$rsCorretagem->getCampo("nom_cgm").'";';
            }

            SistemaLegado::executaFrameOculto($stJs);
        }
        break;

    case "calculaTotalDeclarado":
        $flTmpTerritorial = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flTerritorialDeclarado'])), 2, '.', '' );
        $flTmpPredial     = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flPredialDeclarado'])), 2, '.', '' );
        $flTmpTotal       = $flTmpTerritorial + $flTmpPredial;

        $stJs = "f.flTotalDeclarado.value = '".number_format($flTmpTotal, 2, ',', '.')."';";
    case "calculaTotalAvaliado":
        $flTmpTerritorial = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flTerritorialAvaliado'])), 2, '.', '' );
        $flTmpPredial     = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flPredialAvaliado'])), 2, '.', '' );
        $flTmpTotal       = $flTmpTerritorial + $flTmpPredial;

        $stJs .= "f.flTotalAvaliado.value = '".number_format($flTmpTotal, 2, ',', '.')."';";

        if (!empty($_REQUEST['flValorFinanciado'])) {
            $flTmpValorFinanciadoTotal = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flValorFinanciado'])), 2, '.', '' );
            $flTmpTotal -= $flTmpValorFinanciadoTotal;
        }

        $stJs .= "f.flTotalValor.value = '".number_format($flTmpTotal, 2, ',', '.')."';";

        $flTmpAliquota     = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flTotalAliquota'])), 2, '.', '' );

        $flTmpTotal *= $flTmpAliquota;
        if ( $flTmpTotal )
            $flTmpTotal /= 100;

        $stJs .= "f.flTotalValorImposto.value = '".number_format($flTmpTotal, 2, ',', '.')."';";

        $flTmpValorTotalImposto = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flFinanciadoImposto'])), 2, '.', '' );

        $flTmpTotal += $flTmpValorTotalImposto;

        $stJs .= "f.flTotalCobranca.value = '".number_format($flTmpTotal, 2, ',', '.')."';";

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "calculaTotalAliquota":
        $flTmpValorVenalTotal = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flTotalValor'])), 2, '.', '' );
        $flTmpAliquota     = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flTotalAliquota'])), 2, '.', '' );
        $flTmpTotal = $flTmpValorVenalTotal * $flTmpAliquota;
        if ( $flTmpTotal )
            $flTmpTotal /= 100;

        $stJs = "f.flTotalValorImposto.value = '".number_format($flTmpTotal, 2, ',', '.')."';";

        $flTmpValorTotalImposto = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flFinanciadoImposto'])), 2, '.', '' );
        $flTmpTotal += $flTmpValorTotalImposto;

        $stJs .= "f.flTotalCobranca.value = '".number_format($flTmpTotal, 2, ',', '.')."';";

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "calculaTotalFinanciado":
        $flTmpValorFinanciadoTotal = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flValorFinanciado'])), 2, '.', '' );
        $flTmpAliquota     = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flFinanciadoAliquota'])), 2, '.', '' );
        $flTmpTotal1 = $flTmpValorFinanciadoTotal * $flTmpAliquota;
        if ( $flTmpTotal1 )
            $flTmpTotal1 /= 100;

        $stJs = "f.flFinanciadoImposto.value = '".number_format($flTmpTotal1, 2, ',', '.')."';";

        $flTmpValorTotal = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flTotalAvaliado'])), 2, '.', '' );
        $flTmpValorTotal -= $flTmpValorFinanciadoTotal;
        $stJs .= "f.flTotalValor.value = '".number_format($flTmpValorTotal, 2, ',', '.')."';";

        $flTmpAliquota     = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flTotalAliquota'])), 2, '.', '' );
        $flTmpTotal = $flTmpValorTotal * $flTmpAliquota;
        if ( $flTmpTotal )
            $flTmpTotal /= 100;

        $stJs .= "f.flTotalValorImposto.value = '".number_format($flTmpTotal, 2, ',', '.')."';";

        $flTmpTotal += $flTmpTotal1;

        $stJs .= "f.flTotalCobranca.value = '".number_format($flTmpTotal, 2, ',', '.')."';";

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaProcesso":
        if ($_POST['inProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();

            if ( $obErro->ocorreu() ) {
                $stJs = 'f.inProcesso.value = "";';
                $stJs .= 'f.inProcesso.focus();';
                $stJs .= "alertaAviso('@Processo não encontrado. (".$_POST["inProcesso"].")','form','erro','".Sessao::getId()."');";

                SistemaLegado::executaFrameOculto($stJs);
            }
        }
        break;

    case "atualizaCheckDocumento":
        $arDocumentos = Sessao::read ( "Documentos" );
        foreach ($arDocumentos as $listaDocumentos) {
            $posicaoSessao = $listaDocumentos["inId"] - 1;
            $posicaoID     = $listaDocumentos["inId"];
            if ($_REQUEST["boEntregue_$posicaoID"]) {
                $arDocumentos[$posicaoSessao]['entregue'] = "t";
            } else {
                $arDocumentos[$posicaoSessao]['entregue'] = "f";
            }
        }

        Sessao::write ( "Documentos", $arDocumentos );
        break;

    case "buscaModeloCarne":
       $obRARRCarne = new RARRCarne;
       $obRARRCarne->listarModeloDeCarne( $rsModelos, Sessao::read('acao') );

       $obCmbModelo =  new Select;
       $obCmbModelo->setRotulo        ( "Modelo de Carnê" );
       $obCmbModelo->setName          ( "stArquivo" );
       $obCmbModelo->setStyle         ( "width: 200px");
       $obCmbModelo->setCampoID       ( "[nom_arquivo]§[cod_modelo]" );
       $obCmbModelo->setCampoDesc     ( "nom_modelo" );
       $obCmbModelo->addOption        ( "", "Selecione" );
       $obCmbModelo->setNull          ( false );
       $obCmbModelo->preencheCombo    ( $rsModelos );

       $obFormulario = new Formulario;
       $obFormulario->addComponente( $obCmbModelo );
       $obFormulario->montaInnerHTML();
       $stHTML = $obFormulario->getHTML ();

       $stHTML = str_replace( "\n" ,"" ,$stHTML );
       $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
       $stHTML = str_replace( "  " ,"" ,$stHTML );
       $stHTML = str_replace( "'","\\'",$stHTML );
       $stHTML = str_replace( "\\\\'","\\'",$stHTML );

       if ($_REQUEST['boEmissaoCarne'] == 'sim') {
          SistemaLegado::executaFrameOculto($js."d.getElementById('spnModeloCarne').innerHTML = '".$stHTML."';");
       } else {
          SistemaLegado::executaFrameOculto($js."d.getElementById('spnModeloCarne').innerHTML = '';");
       }

    break;
}
?>
